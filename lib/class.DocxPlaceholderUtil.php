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

                // Seguridad básica: solo letras, números y _
                $inside = preg_replace('/[^A-Za-z0-9_]/', '', $inside);

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
}
