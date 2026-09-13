<?php
require_once('lib/EmailTools/OAuth2Providers.php');
require_once('lib/EmailTools/providers/EmailCoreProvider.php');

class IMAPProvider extends EmailCoreProvider {
    private $server;
    private $port;
    private $encryption;
    private $imapResource;
    
    public function __construct($server, $port = 993, $encryption = 'ssl') {
        parent::__construct('password');
        $this->server = $server;
        $this->port = $port;
        $this->encryption = $encryption;
    }
    
    public function authenticate($credentials) {
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        
        $connectionString = "{{$this->server}:{$this->port}/imap/{$this->encryption}}INBOX";
        $this->imapResource = imap_open($connectionString, $this->username, $this->password);
        
        if (!$this->imapResource) {
            throw new Exception("Error de autenticación IMAP: " . imap_last_error());
        }
        
        return ['status' => 'authenticated', 'method' => 'imap'];
    }
    
    public function testConnection() {
        try {
            $connectionString = "{{$this->server}:{$this->port}/imap/{$this->encryption}}INBOX";
            $imap = imap_open($connectionString, $this->username, $this->password);
            if ($imap) {
                imap_close($imap);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getPathAbsolute() {
        try {            
            return $this->path_absolute;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPathRelative() {
        try {            
            return $this->path_relative;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getEmails($maxResults = 100, $lastUid = null) {
        if (!$this->imapResource) {
            throw new Exception("No hay conexión IMAP activa");
        }
        
        $emails = [];
        $totalEmails = imap_num_msg($this->imapResource);
        $startMsg = max(1, $totalEmails - $maxResults + 1);
        
        for ($msgNum = $totalEmails; $msgNum >= $startMsg && count($emails) < $maxResults; $msgNum--) {
            $header = imap_headerinfo($this->imapResource, $msgNum);
            $body = imap_body($this->imapResource, $msgNum);
            $structure = imap_fetchstructure($this->imapResource, $msgNum);
            
            $email = [
                'message_id' => $header->message_id,
                'subject' => isset($header->subject) ? $this->decodeHeader($header->subject) : '',
                'from_email' => isset($header->from[0]) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : '',
                'to_email' => isset($header->to[0]) ? $header->to[0]->mailbox . '@' . $header->to[0]->host : '',
                'cc_email' => null,
                'date_received' => date('Y-m-d H:i:s', $header->udate),
                'seen' => $header->Unseen == 'U' ? false : true,
                'flagged' => $header->Flagged == 'F' ? true : false,
                'body' => $body,
                'content_html' => $this->extractHtmlContent($msgNum, $structure),
                'clean_html' => $this->getCleanHtml($msgNum, $structure),
                'size' => $header->Size,
                'attachments' => $this->getAttachments($msgNum, $structure),
                'has_attachments' => false,
                'msgNum' => $msgNum,
                'provider' => 'imap'
            ];

            $email['has_attachments'] = count($email['attachments']) > 0;

            $emails[] = $email;
        }

        return $emails;
    }
    
    // Función para extraer HTML del email
    private function extractHtmlContent($msgNum, $structure, $partNumber = '') {
        if (isset($structure->parts)) {
            // Email multipart
            foreach ($structure->parts as $index => $part) {
                $currentPartNumber = $partNumber ? $partNumber . '.' . ($index + 1) : ($index + 1);
                
                if ($part->subtype == 'HTML') {
                    $body = imap_fetchbody($this->imapResource, $msgNum, $currentPartNumber);
                    return $this->decodeBody($body, $part->encoding);
                }
                
                // Buscar recursivamente
                $result = $this->extractHtmlContent($msgNum, $part, $currentPartNumber);
                if ($result) {
                    return $result;
                }
            }
        } else {
            // Email simple
            if ($structure->subtype == 'HTML') {
                $body = imap_fetchbody($this->imapResource, $msgNum, $partNumber ?: '1');
                return $this->decodeBody($body, $structure->encoding);
            }
        }

        return null;
    }

    // Función para obtener HTML completamente limpio
    private function getCleanHtml($msgNum, $structure) {
        // Primero intentar obtener HTML
        $htmlContent = $this->extractHtmlContent($msgNum, $structure);
        
        if ($htmlContent) {
            $cleaned = $this->cleanHtmlContent($htmlContent);
            // Si la limpieza eliminó todo el contenido, devolver el HTML original
            if (empty(trim($cleaned)) || strlen(trim($cleaned)) < 50) {
                return $this->basicCleanHtml($htmlContent);
            }
            return $cleaned;
        }

        // Si no hay HTML, obtener texto plano y convertir
        $textContent = $this->extractTextContent($msgNum, $structure);
        if ($textContent) {
            return $this->textToHtml($this->cleanTextContent($textContent));
        }

        // Como última opción, procesar el body completo
        $bodyContent = imap_body($this->imapResource, $msgNum);
        if ($bodyContent) {
            return $this->textToHtml($this->cleanTextContent($bodyContent));
        }

        return null;
    }

    // Función para extraer texto plano
    private function extractTextContent($msgNum, $structure, $partNumber = '') {
        if (isset($structure->parts)) {
            foreach ($structure->parts as $index => $part) {
                $currentPartNumber = $partNumber ? $partNumber . '.' . ($index + 1) : ($index + 1);
                
                // Buscar texto plano
                if ($part->subtype == 'PLAIN') {
                    $body = imap_fetchbody($this->imapResource, $msgNum, $currentPartNumber);
                    return $this->decodeBody($body, $part->encoding);
                }
                
                $result = $this->extractTextContent($msgNum, $part, $currentPartNumber);
                if ($result) {
                    return $result;
                }
            }
        } else {
            if ($structure->subtype == 'PLAIN') {
                $body = imap_fetchbody($this->imapResource, $msgNum, $partNumber ?: '1');
                return $this->decodeBody($body, $structure->encoding);
            }
        }

        // Si no encontramos texto plano específico, intentar obtener el body completo
        if (empty($partNumber)) {
            $body = imap_body($this->imapResource, $msgNum);
            if ($body) {
                return $body;
            }
        }

        return null;
    }

    // Nueva función para limpiar contenido de texto plano
    private function cleanTextContent($textContent) {
        if (empty($textContent)) {
            return '';
        }
        
        $lines = explode("\n", $textContent);
        $cleanLines = [];
        $skipHeaders = true;
        $foundContent = false;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Detectar headers MIME
            if ($skipHeaders && $this->isMimeHeader($trimmedLine)) {
                continue;
            }
            
            // Si encontramos una línea vacía, podría ser el fin de headers
            if ($skipHeaders && empty($trimmedLine)) {
                $skipHeaders = false;
                continue;
            }
            
            // Si no parece header, incluir la línea
            if (!$this->isMimeHeader($trimmedLine)) {
                $skipHeaders = false;
                $foundContent = true;
                $cleanLines[] = $line;
            }
        }
        
        // Si no encontramos contenido, devolver todo menos las primeras líneas obvias de headers
        if (!$foundContent) {
            $cleanLines = [];
            $headerCount = 0;
            
            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                
                // Saltar las primeras líneas que parecen headers
                if ($headerCount < 10 && $this->isMimeHeader($trimmedLine)) {
                    $headerCount++;
                    continue;
                }
                
                $cleanLines[] = $line;
            }
        }
        
        return trim(implode("\n", $cleanLines));
    }

    // Función para detectar headers MIME
    private function isMimeHeader($line) {
        $mimePatterns = [
            '/^This is a multipart message/',
            '/^------.*NextPart/',
            '/^Content-Type:/',
            '/^Content-Transfer-Encoding:/',
            '/^charset=/',
            '/^boundary=/',
            '/^------.*--$/',
            '/^Message-ID:/',
            '/^Date:/',
            '/^From:/',
            '/^To:/',
            '/^Subject:/',
            '/^MIME-Version:/',
            '/^Return-Path:/',
            '/^Received:/',
            '/^X-.*:/',
        ];
        
        foreach ($mimePatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
        return false;
    }

    // Decodificar el contenido según la codificación
    private function decodeBody($body, $encoding) {
        switch ($encoding) {
            case 0: // 7bit
            case 1: // 8bit
                return $body;
            case 2: // binary
                return $body;
            case 3: // base64
                return base64_decode($body);
            case 4: // quoted-printable
                return quoted_printable_decode($body);
            default:
                return $body;
        }
    }

    /**
    * Convertir texto plano a HTML
    */
    private function textToHtml($text) {
        if (empty(trim($text))) {
            return '<html><body><p>Contenido vacío</p></body></html>';
        }
        
        $html = '<html><head><meta charset="UTF-8"><title>Email Content</title></head><body>';
        $html .= '<div style="font-family: Consolas, Monaco, monospace; font-size: 12px; line-height: 1.4; white-space: pre-wrap; background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd;">';
        
        // Preservar espacios y saltos de línea, pero escapar HTML
        $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // Convertir URLs a enlaces
        $escapedText = preg_replace(
            '/(https?:\/\/[^\s]+)/', 
            '<a href="$1" target="_blank" style="color: #0066cc;">$1</a>', 
            $escapedText
        );
        
        // Convertir emails a enlaces
        $escapedText = preg_replace(
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', 
            '<a href="mailto:$1" style="color: #0066cc;">$1</a>', 
            $escapedText
        );
        
        $html .= $escapedText;
        $html .= '</div></body></html>';
        
        return $html;
    }

    // Limpiar HTML de headers y metadatos
    private function cleanHtmlContent($html) {
        // Guardar el HTML original por si algo sale mal
        $originalHtml = $html;
        
        // Eliminar headers de email comunes al inicio
        $html = preg_replace('/^[A-Za-z-]+:\s.*[\r\n]+/m', '', $html);
        
        // Eliminar líneas que parecen headers específicos de email
        $html = preg_replace('/^Content-Type:.*[\r\n]+/im', '', $html);
        $html = preg_replace('/^Content-Transfer-Encoding:.*[\r\n]+/im', '', $html);
        $html = preg_replace('/^charset=.*[\r\n]+/im', '', $html);
        $html = preg_replace('/^boundary=.*[\r\n]+/im', '', $html);
        $html = preg_replace('/^--.*--.*[\r\n]+/m', '', $html);
        
        // Buscar patrones de HTML válido
        $patterns = [
            '/<html[^>]*>.*<\/html>/is',
            '/<body[^>]*>.*<\/body>/is',
            '/<div[^>]*>.*<\/div>/is',
            '/<p[^>]*>.*<\/p>/is',
            '/<table[^>]*>.*<\/table>/is'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $extractedHtml = $matches[0];
                // Verificar que no esté vacío después de limpiar
                $testContent = strip_tags($extractedHtml);
                if (strlen(trim($testContent)) > 10) {
                    return $extractedHtml;
                }
            }
        }
        
        // Si no encontramos patrones válidos, intentar limpieza línea por línea
        $lines = explode("\n", $html);
        $cleanLines = [];
        $htmlStarted = false;
        $headerEnded = false;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Marcar que terminaron los headers si encontramos HTML
            if (stripos($trimmedLine, '<html') !== false || 
                stripos($trimmedLine, '<!doctype') !== false ||
                stripos($trimmedLine, '<body') !== false ||
                stripos($trimmedLine, '<div') !== false ||
                stripos($trimmedLine, '<p') !== false) {
                $htmlStarted = true;
                $headerEnded = true;
            }
            
            // Si encontramos una línea vacía después de headers, marcar fin de headers
            if (!$headerEnded && empty($trimmedLine)) {
                $headerEnded = true;
                continue;
            }
            
            // Incluir línea si:
            // - Ya empezó el HTML
            // - Los headers terminaron y no parece header
            // - Contiene tags HTML
            if ($htmlStarted || 
                ($headerEnded && !$this->isEmailHeader($trimmedLine)) ||
                (stripos($trimmedLine, '<') !== false && stripos($trimmedLine, '>') !== false)) {
                $cleanLines[] = $line;
                if (!$htmlStarted) $htmlStarted = true;
            }
        }
        
        $result = implode("\n", $cleanLines);
        
        // Si el resultado está muy vacío, usar limpieza básica
        if (strlen(trim(strip_tags($result))) < 20) {
            return $this->basicCleanHtml($originalHtml);
        }
        
        return trim($result);
    }

    // Función auxiliar para detectar headers de email
    private function isEmailHeader($line) {
        $headerPatterns = [
            '/^[A-Za-z-]+:\s/',
            '/^Content-/',
            '/^Message-/',
            '/^Date:/',
            '/^From:/',
            '/^To:/',
            '/^Subject:/',
            '/^MIME-/',
            '/^boundary=/',
            '/^charset=/',
            '/^--.*--$/'
        ];
        
        foreach ($headerPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
        return false;
    }

    // Limpieza básica más conservadora
    private function basicCleanHtml($html) {
        // Solo eliminar headers obvios al principio
        $lines = explode("\n", $html);
        $cleanLines = [];
        $skipHeaders = true;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Si encontramos HTML, dejar de saltar headers
            if (stripos($trimmedLine, '<') !== false || !$skipHeaders) {
                $skipHeaders = false;
                $cleanLines[] = $line;
            }
            // Si la línea está vacía, podría ser el fin de headers
            elseif (empty($trimmedLine)) {
                $skipHeaders = false;
            }
            // Si no parece header, incluir
            elseif (!$this->isEmailHeader($trimmedLine)) {
                $skipHeaders = false;
                $cleanLines[] = $line;
            }
        }
        
        $result = implode("\n", $cleanLines);
        
        // Si aún no tenemos tags HTML, envolver el contenido
        if (stripos($result, '<html') === false && stripos($result, '<body') === false) {
            $textContent = strip_tags($result);
            if (strlen(trim($textContent)) > 0) {
                return $this->textToHtml($textContent);
            }
        }
        
        return trim($result);
    }

    /**
     * Descargar adjunto
     */
    public function downloadAttachment($emailNumber, $partNumber, $encoding, $filename = null) 
    {
        try {
            // Usar UID para mayor confiabilidad
            $data = imap_fetchbody($this->imapResource, $emailNumber, $partNumber, FT_UID);
            //****************************************************************************************
            // Si no obtuvimos datos con UID, intentar sin UID
            if (empty($data)) {
                $data = imap_fetchbody($this->imapResource, $emailNumber, $partNumber);
            }
            //****************************************************************************************
            // Verificar si obtuvimos datos
            if (empty($data)) {
                error_log("No se pudieron obtener datos del adjunto. Email: $emailNumber, Parte: $partNumber");
                return false;
            }
            //****************************************************************************************
            // Decodificar según el encoding
            $decodedData = $this->decodeBody($data, $encoding);
            //****************************************************************************************
            // Verificar que la decodificación fue exitosa
            if (empty($decodedData) && !empty($data)) {
                // Si la decodificación falló, usar datos originales
                $decodedData = $data;
                error_log("Warning: Decodificación falló, usando datos originales");
            }
            //****************************************************************************************
            $email_path = $this->downloads_dir.DIRECTORY_SEPARATOR.md5($emailNumber);
            //****************************************************************************************
            if ($filename) {
                $downloadPath = $email_path . DIRECTORY_SEPARATOR . $filename;
                if (!file_exists($email_path)) {
                    mkdir($email_path, 0777, true);
                }
                //************************************************************************************
                file_put_contents($downloadPath, $data);
                return $downloadPath;
            }
            //****************************************************************************************
            return $data;
        } catch (PropelException $th) {
            //throw $th;
            return null;
        } catch (\Exception $th) {
            //throw $th;
            return null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
        
    }

    private function decodeHeader($header) {
        $decoded = imap_mime_header_decode($header);
        $result = '';
        foreach ($decoded as $element) {
            $result .= $element->text;
        }
        return $result;
    }
    
    public function __destruct() {
        if ($this->imapResource) {
            imap_close($this->imapResource);
        }
    }

    /**
     * Sistema de notificaciones para nuevos correos
     */
    public function checkNewEmails($lastCheckTime = null) {
        if (!$lastCheckTime) {
            $lastCheckTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
        }
        
        $newEmails = $this->getEmailsWithFilters([
            'date_from' => $lastCheckTime,
            'unread' => true
        ]);
        
        if (!empty($newEmails)) {
            $this->sendNotifications($newEmails);
        }
        
        return $newEmails;
    }

    /**
     * Obtener adjuntos del correo
    */
    public function getAttachments($emailNumber, $structure = null) {
        if (!$structure) {
            $structure = imap_fetchstructure($this->imapResource, $emailNumber);
        }
        
        $attachments = [];
        
        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $part = $structure->parts[$i];
                
                // Verificar si es un adjunto
                if ($this->isAttachment($part)) {
                    $attachment = $this->extractAttachment($emailNumber, $i + 1, $part);
                    if ($attachment) {
                        $attachments[] = $attachment;
                    }
                }
                
                // Verificar subpartes (adjuntos anidados)
                if (isset($part->parts) && count($part->parts)) {
                    for ($j = 0; $j < count($part->parts); $j++) {
                        $subpart = $part->parts[$j];
                        if ($this->isAttachment($subpart)) {
                            $attachment = $this->extractAttachment($emailNumber, ($i + 1) . '.' . ($j + 1), $subpart);
                            if ($attachment) {
                                $attachments[] = $attachment;
                            }
                        }
                    }
                }
            }
        }
        
        return $attachments;
    }

    /**
     * Método completamente nuevo para obtener adjuntos
     */
    public function getAttachmentsNew($emailNumber) {
        $structure = imap_fetchstructure($this->imapResource, $emailNumber);
        $attachments = [];
        
        if (isset($structure->parts)) {
            $this->extractAttachmentsFromParts($emailNumber, $structure->parts, '', $attachments);
        } else {
            // Correo simple sin partes múltiples
            if ($this->hasAttachmentProperties($structure)) {
                $attachment = $this->buildAttachmentInfo($emailNumber, '1', $structure);
                if ($attachment) {
                    $attachments[] = $attachment;
                }
            }
        }
        
        return $attachments;
    }

    /**
     * Extraer adjuntos recursivamente de las partes
     */
    private function extractAttachmentsFromParts($emailNumber, $parts, $prefix, &$attachments) {
        foreach ($parts as $index => $part) {
            $partNumber = $prefix . ($index + 1);
            
            // Si esta parte tiene subpartes, procesarlas recursivamente
            if (isset($part->parts)) {
                $this->extractAttachmentsFromParts($emailNumber, $part->parts, $partNumber . '.', $attachments);
            }
            
            // Verificar si esta parte es un adjunto
            if ($this->hasAttachmentProperties($part)) {
                $attachment = $this->buildAttachmentInfo($emailNumber, $partNumber, $part);
                if ($attachment) {
                    $attachments[] = $attachment;
                }
            }
        }
    }

    /**
     * Verificar si una parte tiene propiedades de adjunto
     */
    private function hasAttachmentProperties($part) {
        // Verificar disposition
        if (isset($part->disposition)) {
            $disposition = strtolower($part->disposition);
            if ($disposition === 'attachment' || $disposition === 'inline') {
                return true;
            }
        }
        
        // Verificar parámetros de disposition
        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) === 'filename') {
                    return true;
                }
            }
        }
        
        // Verificar parámetros normales
        if (isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) === 'name') {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Construir información del adjunto
     */
    private function buildAttachmentInfo($emailNumber, $partNumber, $part) {
        $filename = 'archivo_sin_nombre';
        $size = isset($part->bytes) ? $part->bytes : 0;
        $encoding = isset($part->encoding) ? $part->encoding : 0;
        
        // Obtener nombre del archivo desde parámetros de disposition
        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) === 'filename') {
                    $filename = $this->decodeText($param->value);
                    break;
                }
            }
        }
        
        // Si no se encontró en dparameters, buscar en parameters
        if ($filename === 'archivo_sin_nombre' && isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) === 'name') {
                    $filename = $this->decodeText($param->value);
                    break;
                }
            }
        }
        
        return [
            'filename' => $filename,
            'size' => $size,
            'encoding' => $encoding,
            'part_number' => $partNumber,
            'email_number' => $emailNumber,
            'mime_type' => $this->getMimeType($part->type ?? 0, $part->subtype ?? 'unknown'),
            'type' => $part->type ?? 0,
            'subtype' => $part->subtype ?? 'unknown'
        ];
    }

    /**
     * Verificar si una parte es un adjunto
     */
    private function isAttachment($part) {
        // Verificar disposition
        if (isset($part->disposition)) {
            return strtolower($part->disposition) == 'attachment' || 
                   strtolower($part->disposition) == 'inline';
        }
        
        // Verificar parámetros de disposition
        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) == 'filename') {
                    return true;
                }
            }
        }
        
        // Verificar parámetros regulares
        if (isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) == 'name') {
                    return true;
                }
            }
        }
        
        // Verificar por tipo MIME específico
        if (isset($part->type) && isset($part->subtype)) {
            $mimeType = strtolower($this->getMimeType($part->type, $part->subtype));
            
            // Tipos MIME que generalmente son adjuntos
            $attachmentTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats',
                'application/zip',
                'application/x-zip-compressed',
                'image/jpeg',
                'image/png',
                'image/gif'
            ];
            
            foreach ($attachmentTypes as $type) {
                if (strpos($mimeType, $type) === 0) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Verificar si los parámetros contienen un nombre de archivo
     */
    private function hasFilename($parameters) {
        foreach ($parameters as $param) {
            if (strtolower($param->attribute) == 'name') {
                return true;
            }
        }
        return false;
    }

    /**
     * Extraer información del adjunto
    */
    private function extractAttachment($emailNumber, $partNumber, $part) {
        $filename = 'sin_nombre';
        $size = $part->bytes ?? 0;
        //************************************************************************************
        // filtrar inline images:
        if (!empty($part->disposition) && strtoupper($part->disposition) === 'INLINE') {
            return;
        }
        if (!empty($part->id)) {
            // Content-ID presente → contenido embebido
            return;
        }
         //************************************************************************************
        // Obtener nombre del archivo
        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) == 'filename') {
                    $filename = $this->decodeText($param->value);
                    break;
                }
            }
        }
        
        if ($filename == 'sin_nombre' && isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) == 'name') {
                    $filename = $this->decodeText($param->value);
                    break;
                }
            }
        }
        
        $mimeType = $this->getMimeType($part->type, $part->subtype);
        
        return [
            'filename' => $filename,
            'size' => $size,
            'mime_type' => $mimeType,
            'part_number' => $partNumber,
            'email_number' => $emailNumber,
            'encoding' => $part->encoding
        ];
    }

    /**
     * Decodificar texto
    */
    private function decodeText($text) {
        $decoded = imap_mime_header_decode($text);
        $result = '';
        foreach ($decoded as $part) {
            $result .= $part->text;
        }
        return $result;
    }

    /**
     * Obtener tipo MIME
     */
    private function getMimeType($type, $subtype) {
        $types = [
            0 => 'text',
            1 => 'multipart',
            2 => 'message',
            3 => 'application',
            4 => 'audio',
            5 => 'image',
            6 => 'video',
            7 => 'other'
        ];
        
        return ($types[$type] ?? 'other') . '/' . strtolower($subtype);
    }

    /**
     * Función de testing para un correo específico
     */
    public function testAttachmentDownload($emailNumber) {
        echo "=== TESTING ADJUNTOS PARA EMAIL $emailNumber ===\n";
        
        // Obtener estructura completa
        $structure = imap_fetchstructure($this->imapResource, $emailNumber);
        echo "Estructura del correo:\n";
        $this->printStructure($structure, 0);
        
        // Obtener adjuntos con el método nuevo
        $attachments = $this->getAttachmentsNew($emailNumber);
        echo "\nAdjuntos encontrados: " . count($attachments) . "\n";
        
        foreach ($attachments as $index => $attachment) {
            echo "\n--- ADJUNTO " . ($index + 1) . " ---\n";
            echo "Nombre: " . $attachment['filename'] . "\n";
            echo "Tamaño: " . $attachment['size'] . " bytes\n";
            echo "Parte: " . $attachment['part_number'] . "\n";
            echo "Encoding: " . $attachment['encoding'] . "\n";
            echo "MIME: " . $attachment['mime_type'] . "\n";
            
            // Intentar descarga
            $result = $this->downloadAttachmentRobust(
                $attachment['email_number'],
                $attachment['part_number'],
                $attachment['encoding'],
                $attachment['filename']
            );
            
            if ($result) {
                echo "✓ Descarga exitosa: $result\n";
            } else {
                echo "✗ Falló la descarga\n";
            }
            
            echo "------------------------\n";
        }
    }

    /**
     * Imprimir estructura del correo para debugging
     */
    private function printStructure($structure, $level = 0) {
        $indent = str_repeat('  ', $level);
        
        echo $indent . "Tipo: " . ($structure->type ?? 'N/A') . "\n";
        echo $indent . "Subtipo: " . ($structure->subtype ?? 'N/A') . "\n";
        echo $indent . "Encoding: " . ($structure->encoding ?? 'N/A') . "\n";
        echo $indent . "Bytes: " . ($structure->bytes ?? 'N/A') . "\n";
        
        if (isset($structure->disposition)) {
            echo $indent . "Disposition: " . $structure->disposition . "\n";
        }
        
        if (isset($structure->parameters)) {
            echo $indent . "Parámetros:\n";
            foreach ($structure->parameters as $param) {
                echo $indent . "  " . $param->attribute . " = " . $param->value . "\n";
            }
        }
        
        if (isset($structure->dparameters)) {
            echo $indent . "D-Parámetros:\n";
            foreach ($structure->dparameters as $param) {
                echo $indent . "  " . $param->attribute . " = " . $param->value . "\n";
            }
        }
        
        if (isset($structure->parts)) {
            echo $indent . "Subpartes: " . count($structure->parts) . "\n";
            foreach ($structure->parts as $index => $part) {
                echo $indent . "Parte " . ($index + 1) . ":\n";
                $this->printStructure($part, $level + 1);
            }
        }
        
        echo $indent . "---\n";
    }

    /**
     * gardar un adjunto
     */
    public function saveAttachments($uid) 
    {
        $imap = $this->imapResource;
        //***************************************************************************************
        $structure = imap_fetchstructure($imap, $uid, FT_UID);
        if (!isset($structure->parts)) {
            return;
        }
        //***************************************************************************************
        $email_path = $this->downloads_dir.DIRECTORY_SEPARATOR.md5($uid);
        //***************************************************************************************
        foreach ($structure->parts as $partNum => $part) {
            // Verifica si es attachment
            $isAttachment = false;
            $filename     = '';
    
            if (!empty($part->dparameters)) {
                foreach ($part->dparameters as $obj) {
                    if (strtolower($obj->attribute) == 'filename') {
                        $isAttachment = true;
                        $filename     = $obj->value;
                    }
                }
            }
            if (!empty($part->parameters)) {
                foreach ($part->parameters as $obj) {
                    if (strtolower($obj->attribute) == 'name') {
                        $isAttachment = true;
                        $filename     = $obj->value;
                    }
                }
            }
    
            if ($isAttachment && $filename) {
                // Extrae el cuerpo de la parte
                $body = imap_fetchbody($imap, $uid, $partNum + 1, FT_UID);
                // Decodifica según el encoding
                switch ($part->encoding) {
                    case 3: // BASE64
                        $data = base64_decode($body);
                        break;
                    case 4: // QUOTED-PRINTABLE
                        $data = quoted_printable_decode($body);
                        break;
                    default:
                        $data = $body;
                }
                // Guarda el archivo
                file_put_contents($email_path . DIRECTORY_SEPARATOR . $filename, $data);
                echo "Guardado: $filename\n";
            }
        }
    }

    /**
     * Descarga únicamente las partes con disposition=ATTACHMENT.
     *
     * @param int      $uid      UID del mensaje.
     */
    public function saveOnlyAttachments($uid, $partNumber) 
    {
        //$data = imap_fetchbody($this->imapResource, $emailNumber, $partNumber, FT_UID);
        $email_path = $this->downloads_dir.DIRECTORY_SEPARATOR.md5($uid);
        //****************************************************************************************
        // 1) Traemos la estructura completa sólo UNA vez
        $structure = imap_fetchstructure($this->imapResource, $uid, FT_UID);
        if (empty($structure->parts)) {
            return;
        }
        // 2) Llamamos al helper recursivo, pasando el array de partes raíz
        return $this->_recursiveSave($uid, $structure->parts[$partNumber], $email_path, '');
    }

    /**
     * Guarda en disco exclusivamente las partes con disposition=ATTACHMENT.
     * @param resource $imap     Recurso IMAP (de imap_open).
     * @param int      $uid UID del mensaje.
     * @param array    $parts   Array de objetos stdClass de partes MIME
     * @param string   $downloads_dir directorio de destino de los adjuntos
     * @param string   $prefix   (Interno) prefijo de la parte para recursión.
     */
    public function _recursiveSave($uid, array $parts, $downloads_dir, $prefix = '') 
    {
        foreach ($parts as $index => $part) {
            // Número de parte: "1", "2.1", "2.2.1", etc.
            $partNum = $prefix === '' ? ($index + 1) : $prefix . '.' . ($index + 1);
            //************************************************************************************
            // filtrar inline images:
            if (!empty($part->disposition) && strtoupper($part->disposition) === 'INLINE') {
                continue;
            }
            if (!empty($part->id)) {
                // Content-ID presente → contenido embebido
                continue;
            }
            //************************************************************************************
            // Nombre de fichero (filename o name)
            $filename = '';
            if (!empty($part->dparameters)) {
                foreach ($part->dparameters as $obj) {
                    if (strcasecmp($obj->attribute, 'filename') === 0) {
                        $filename = $obj->value;
                        break;
                    }
                }
            }
            //*******************************************************************************
            if (!$filename && !empty($part->parameters)) {
                foreach ($part->parameters as $obj) {
                    if (strcasecmp($obj->attribute, 'name') === 0) {
                        $filename = $obj->value;
                        break;
                    }
                }
            }
            //*******************************************************************************
            if ($filename) {
                // Traemos el contenido crudo
                $raw = imap_fetchbody($this->imapResource, $uid, $partNum, FT_UID);
                // Decodificamos según encoding
                switch ($part->encoding) {
                    case ENCBASE64:
                        $data = base64_decode($raw);
                        break;
                    case ENCQUOTEDPRINTABLE: 
                        $data = quoted_printable_decode($raw);
                        break;
                    default:
                        $data = $raw;
                        break;
                }
                //**************************************************************************
                $downloadPath = $downloads_dir . DIRECTORY_SEPARATOR . $filename;                    
                if (!file_exists($downloads_dir)) {
                    mkdir($downloads_dir, 0777, true);
                }
                //**************************************************************************
                if (file_exists($downloadPath)) {
                    unlink($downloadPath);
                }
                //**************************************************************************
                // Guardamos
                file_put_contents($downloadPath, $data);
                return $downloadPath;
            }
            //**********************************************************************************
            // Si es multipart, descendemos
            if (!empty($part->parts)) {
                $this->_recursiveSave($uid, $part->parts, $downloads_dir, $partNum);
            }
        }
    }
}