<?php 
use ZipArchive;

class DocxPlaceholderUtil
{
    /**
     * Convierte placeholders {[CLAVE]} a ${CLAVE} dentro de un DOCX,
     * en document.xml, headerN.xml y footerN.xml.
     */
    public static function convertCurlyPlaceholdersToPhpWordTpl(string $docxIn, string $docxOut): void
    {
        if (file_exists($docxOut)) {
            @unlink($docxOut);
        }

        if (!copy($docxIn, $docxOut)) {
            throw new \RuntimeException("No pude copiar DOCX a destino: $docxOut");
        }

        $zip = new ZipArchive();
        if ($zip->open($docxOut) !== true) {
            throw new \RuntimeException("No pude abrir DOCX destino: $docxOut");
        }

        // Partes a procesar
        $parts = ['word/document.xml'];
        for ($i = 1; $i <= 10; $i++) {
            foreach (['header', 'footer'] as $hf) {
                $p = "word/{$hf}{$i}.xml";
                if ($zip->locateName($p) !== false) {
                    $parts[] = $p;
                }
            }
        }

        foreach ($parts as $partPath) {
            $xml = $zip->getFromName($partPath);
            if ($xml === false) {
                continue;
            }

            // 1) Junta "{ [ ... ] }" que quedaron partidos por tags
            $xml = preg_replace('/\{\s*(?:<[^>]+>\s*)*\[/', '{[', $xml);
            $xml = preg_replace('/\]\s*(?:<[^>]+>\s*)*\}/', ']}', $xml);

            // 2) Convierte {[CAMPO]} → ${CAMPO}
            $xml = preg_replace_callback('/\{\[(.*?)\]\}/s', function ($m) {
                // Quita etiquetas internas (w:t, w:r, etc.) y espacios
                $inside = preg_replace('/<[^>]+>/', '', $m[1]);
                $inside = preg_replace('/\s+/', '', $inside);

                // Seguridad básica: solo letras, números, _ y / (la / solo se usa para marcar el
                // cierre de un bloque repetible, ej. {[/BLOQUE_FIRMAS]} -> ${/BLOQUE_FIRMAS})
                $inside = preg_replace('/[^A-Za-z0-9_\/]/', '', $inside);

                if ($inside === '') {
                    return ''; // si quedara vacío por seguridad
                }

                return '${' . $inside . '}';
            }, $xml);

            // Guardar de vuelta
            $zip->deleteName($partPath);
            $zip->addFromString($partPath, $xml);
        }

        $zip->close();
    }

    /**
     * Envuelve, dentro de un DOCX, el rango contiguo de párrafos de word/document.xml que contienen
     * alguna de las etiquetas {[TAG]} indicadas, con marcadores de bloque {[BLOQUE]} / {[/BLOQUE]},
     * para poder duplicar esa sección más adelante con PhpWord\TemplateProcessor::cloneBlock().
     *
     * No requiere que el usuario agregue nada a su plantilla: los marcadores los inserta este método
     * automáticamente, detectando el primer y el último párrafo que contienen alguna de las etiquetas
     * dadas (algunas pueden no estar presentes en una plantilla concreta, eso es tolerado).
     *
     * UARIV-202605 (ampliación): duplicar el bloque de firma(s) cuando hay más de un firmante.
     *
     * @param string $docxIn      Ruta del .docx original (sin modificar)
     * @param string $docxOut     Ruta donde se guarda la copia con los marcadores insertados
     * @param string $blockName   Nombre del bloque (letras/números/_ únicamente)
     * @param array  $anchorTags  Lista de nombres de etiqueta (sin {[ ]}) que delimitan el bloque a envolver
     *
     * @return bool true si se encontró al menos una etiqueta y se insertaron los marcadores; false si
     *              no se encontró ninguna (en ese caso no se modificó nada y el llamador debe usar el
     *              flujo sin duplicar, igual que antes de esta ampliación).
     */
    public static function insertRepeatingBlockMarkers(string $docxIn, string $docxOut, string $blockName, array $anchorTags): bool
    {
        if (file_exists($docxOut)) {
            @unlink($docxOut);
        }

        if (!copy($docxIn, $docxOut)) {
            throw new \RuntimeException("No pude copiar DOCX a destino: $docxOut");
        }

        $zip = new ZipArchive();
        if ($zip->open($docxOut) !== true) {
            throw new \RuntimeException("No pude abrir DOCX destino: $docxOut");
        }

        $partPath = 'word/document.xml';
        $xml = $zip->getFromName($partPath);
        if ($xml === false) {
            $zip->close();
            return false;
        }

        // Junta "{ [ ... ] }" que quedaron partidos por tags de Word, igual que
        // convertCurlyPlaceholdersToPhpWordTpl(), para poder detectar las etiquetas por texto plano.
        $xml = preg_replace('/\{\s*(?:<[^>]+>\s*)*\[/', '{[', $xml);
        $xml = preg_replace('/\]\s*(?:<[^>]+>\s*)*\}/', ']}', $xml);

        if (!preg_match_all('/<w:p\b[^>]*>.*?<\/w:p>/s', $xml, $matches, PREG_OFFSET_CAPTURE)) {
            $zip->close();
            return false;
        }

        $firstOffset = null;
        $lastEnd = null;
        foreach ($matches[0] as $paragraph) {
            $paragraphXml = $paragraph[0];
            $offset = $paragraph[1];
            $contieneEtiqueta = false;
            foreach ($anchorTags as $tag) {
                if (strpos($paragraphXml, '{[' . $tag . ']}') !== false) {
                    $contieneEtiqueta = true;
                    break;
                }
            }
            if ($contieneEtiqueta) {
                if ($firstOffset === null) {
                    $firstOffset = $offset;
                }
                $lastEnd = $offset + strlen($paragraphXml);
            }
        }

        if ($firstOffset === null) {
            $zip->close();
            return false;
        }

        $blockNameSeguro = preg_replace('/[^A-Za-z0-9_]/', '', $blockName);
        $markerStart = '<w:p><w:r><w:t>{[' . $blockNameSeguro . ']}</w:t></w:r></w:p>';
        $markerEnd = '<w:p><w:r><w:t>{[/' . $blockNameSeguro . ']}</w:t></w:r></w:p>';

        $xml = substr($xml, 0, $firstOffset)
            . $markerStart
            . substr($xml, $firstOffset, $lastEnd - $firstOffset)
            . $markerEnd
            . substr($xml, $lastEnd);

        $zip->deleteName($partPath);
        $zip->addFromString($partPath, $xml);
        $zip->close();

        return true;
    }
}
