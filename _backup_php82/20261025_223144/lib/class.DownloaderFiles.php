<?php
class DescargadorArchivos {
    protected $downloads_dir;
    protected $path_absolute;
    protected $path_relative;
    protected $path_attachments;
    private $extensionesPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
    private $tamanoMaximo = 50 * 1024 * 1024; // 50 MB en bytes
    private $folder_tmp;

    public function __construct() {
        $dir_raiz = ParametroPeer::retrieveByPK(9)->getValortexto();
        $dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
        $folder_date = date("Ymd");
        //****************************************************************************************
        $this->path_attachments = "shared_links_attachment";
        $this->path_absolute = $dir_raiz;
        $this->path_relative = $this->path_attachments.DIRECTORY_SEPARATOR.$folder_date;
        $this->downloads_dir = $this->path_absolute.$this->path_relative;
        $this->folder_tmp = $this->path_absolute.$dir_tmp.DIRECTORY_SEPARATOR.$this->path_attachments;
        //****************************************************************************************
        simad_util::createPath($this->downloads_dir);
        simad_util::createPath($this->folder_tmp);
        //****************************************************************************************
        $ini_array = simad_util::readConfigFileApp();
        $this->extensionesPermitidas = isset($ini_array['mtypes']) ? $this->parsearExtensionesPermitidas($ini_array['mtypes']) : $this->extensionesPermitidas;
        $this->tamanoMaximo = isset($ini_array['max_file_size']) ? ($ini_array['max_file_size'] * 1024 * 1024) : $this->tamanoMaximo;
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

    public function getFullPathDownloads() {
        try {            
            return $this->downloads_dir;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getFileBasicData($shared_link) {
        try {            
            // Codificar URL para manejar espacios antes de usarla
            $url_original = $shared_link;
            $space_sharedlink = $this->codificarUrlConEspacios($url_original);
            $shared_link = $this->normalizarUrl($space_sharedlink);
            //********************************************************************************************************
            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'user_agent' => 'Mozilla/5.0 (compatible; PHP downloader)',
                    'header' => [
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.5',
                        'Accept-Encoding: gzip, deflate',
                        'Connection: keep-alive',
                    ]
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ]
            ]);
            //********************************************************************************************************
            $response = @file_get_contents($shared_link, false, $context);
            //********************************************************************************************************
            if ($response === false) {
                $urlTransformada = $this->transformarUrl($shared_link);
                // Codificar también la URL transformada
                $urlTransformada = $this->codificarUrlConEspacios($urlTransformada);
                $response = @file_get_contents($urlTransformada, false, $context);

                if ($response === false) {
                    return null;
                }
            }
            //********************************************************************************************************
            // Obtener headers de respuesta
            $headers = $this->parseHttpResponseHeaders($http_response_header);
            //********************************************************************************************************
            // Verificar código HTTP
            $httpCode = $headers['response_code'] ?? 0;
            if ($httpCode >= 400) {
                return [
                    'success' => false,
                    'error' => "Error HTTP {$httpCode}. El enlace puede haber expirado o requiere autenticación."
                ];
            }
            //********************************************************************************************************
            // Obtener Content-Type
            $contentType = $headers['content-type'] ?? 'application/octet-stream';
            //********************************************************************************************************
            // Verificar si es HTML (página de error)
            if (strpos($contentType, 'text/html') !== false) {
                // Verificar patrones de error comunes
                if (stripos($response, 'login') !== false || 
                    stripos($response, 'autenticación') !== false ||
                    stripos($response, 'expirado') !== false ||
                    stripos($response, 'expired') !== false) {
                    
                    return [
                        'success' => false,
                        'error' => 'El enlace requiere autenticación o ha expirado.'
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => 'No se pudo descargar el archivo. Respuesta HTML recibida.'
                ];
            }
            //********************************************************************************************************
            $contentLength = strlen($response);
            //********************************************************************************************************
            // Verificar tamaño mínimo
            if ($contentLength < 100) {
                return [
                    'success' => false,
                    'error' => 'El archivo descargado está vacío o es demasiado pequeño.'
                ];
            }
            //********************************************************************************************************
            // Verificar tamaño máximo
            if ($contentLength > $this->tamanoMaximo) {
                return [
                    'success' => false,
                    'error' => 'El archivo excede el tamaño máximo permitido (' . ($this->tamanoMaximo / 1024 / 1024) . 'MB)'
                ];
            }
            //********************************************************************************************************
            // Validar que sea un archivo válido (no HTML de error)
            if (!$this->isValidFileContent($response)) {
                return [
                    'success' => false,
                    'error' => 'El contenido descargado no es un archivo válido.'
                ];
            }
            //********************************************************************************************************
            // Extraer nombre del archivo
            $original_fname = $this->extractFilenameFromHeaders($headers, $shared_link);
            $filename = uniqid()."_".$this->sanitizarNombreArchivo($original_fname);
            //********************************************************************************************************
            $directorioDestino = simad_util::NormalizePath(rtrim($this->downloads_dir, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR;
            //********************************************************************************************************
            // Crear directorio si no existe
            if (!is_dir($directorioDestino)) {
                if (!mkdir($directorioDestino, 0755, true)) {
                    return [
                        'success' => false,
                        'error' => 'No se puede crear el directorio de destino'
                    ];
                }
            }
            //********************************************************************************************************
            if (!is_writable($directorioDestino)) {
                return [
                    'success' => false,
                    'error' => 'No se tienen permisos de escritura en el directorio de destino'
                ];
            }
            //********************************************************************************************************
            // Ruta completa de destino
            $rutaCompleta = $directorioDestino . $filename;
            //******************************************************************************************************** 
            // Prevenir path traversal
            $rutaReal = realpath($directorioDestino);
            $rutaArchivoReal = realpath(dirname($rutaCompleta)) . DIRECTORY_SEPARATOR . basename($rutaCompleta);
            if (strpos($rutaArchivoReal, $rutaReal) !== 0) {
                return [
                    'success' => false,
                    'error' => 'Ruta de archivo no válida'
                ];
            }
            //********************************************************************************************************
            if(@file_put_contents($rutaCompleta,$response) === false){
                return [
                    'success' => false,
                    'error' => 'Error almacenando el archivo en el servidor'
                ];
            }
            //********************************************************************************************************
            return [
                'success' => true,
                'file_path' => $rutaCompleta,
                'file_name' => $filename,
                'file_size' => $contentLength,
                'url_original' => $shared_link,
                'url_transformada' => $shared_link,
                'path_absolute' => $this->path_absolute,
                'path_relative' => $this->path_relative,
                'extension' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
                'mime_type' => $contentType,
                'data' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extrae el nombre del archivo desde los headers
    */
    private function extractFilenameFromHeaders($headers, $url) 
    {
        // Intentar desde Content-Disposition
        if (isset($headers['content-disposition'])) {
            if (preg_match('/filename[^;=\n]*=["\']?([^"\';\n]*)["\']?/i', $headers['content-disposition'], $matches)) {
                return $matches[1];
            }
        }
        
        // Extraer desde URL
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        $filename = basename($path);
        
        if (!empty($filename) && strpos($filename, '.') !== false) {
            return $filename;
        }
        
        // Generar nombre basado en content-type
        $contentType = $headers['content-type'] ?? '';
        $extension = $this->getExtensionFromContentType($contentType);
        
        return 'archivo_' . time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Decodifica automáticamente URLs que vengan codificadas
     */
    private function normalizarUrl($url) 
    {
        $prev = '';
        while ($url !== $prev) {
            $prev = $url;
            $url = urldecode($url);
        }
        //*************************************************************************
        $url = $this->generarUrlDescargaGoogle($url);
        //*************************************************************************
        return $url;
    }

    /**
     * Extrae la URL original desde un enlace "SafeLinks" de Microsoft (Outlook, Teams, etc.)
     *
     * @param string $safelink  URL completa del tipo https://namXX.safelinks.protection.outlook.com/?url=...
     * @return string|null       Devuelve la URL real o null si no se encuentra
     */
    function extractUrlSafelink(string $safelink): ?string
    {
        // Analiza la URL y sus parámetros
        $parts = parse_url($safelink);
        if (!isset($parts['query'])) {
            return null; // No hay parámetros -> no es un SafeLink válido
        }

        parse_str($parts['query'], $params);

        if (!isset($params['url'])) {
            return null; // No contiene parámetro 'url'
        }

        // Decodifica la URL (una o más veces, según sea necesario)
        $decoded = $params['url'];
        $prev = '';
        while ($decoded !== $prev) {
            $prev = $decoded;
            $decoded = urldecode($decoded);
        }

        return $decoded;
    }

    /**
     * Obtiene extensión desde Content-Type
    */
    private function getExtensionFromContentType($contentType) 
    {
        $mimeMap = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'text/plain' => 'txt',
            'video/mp4' => 'mp4',
            'video/avi' => 'avi',
            'audio/mpeg' => 'mp3'
        ];
        
        foreach ($mimeMap as $mime => $ext) {
            if (strpos($contentType, $mime) !== false) {
                return $ext;
            }
        }
        
        return 'bin';
    }

    /**
     * Valida que el contenido sea un archivo válido
    */
    private function isValidFileContent($content) {
        if (strlen($content) < 12) {
            return false;
        }
        
        $magicBytes = substr($content, 0, 12);
        $hex = strtolower(bin2hex($magicBytes));
        
        // Signatures de archivos comunes
        $validSignatures = [
            '255044462d',       // %PDF-
            'ffd8ff',           // JPEG
            '89504e47',         // PNG
            '47494638',         // GIF
            '504b0304',         // ZIP/DOCX/XLSX/PPTX
            'd0cf11e0',         // DOC/XLS/PPT
            '0000001466747970', // MP4
            '52494646',         // AVI/RIFF
            '526172211a07',     // RAR
            '1f8b08',           // GZIP
        ];
        
        foreach ($validSignatures as $signature) {
            if (strpos($hex, $signature) === 0) {
                return true;
            }
        }
        
        // Si no es texto imprimible, probablemente es binario válido
        if (!ctype_print(substr($magicBytes, 0, 4))) {
            return true;
        }
        
        // Verificar que NO sea HTML
        $htmlPatterns = ['<!doctype', '<html', '<head', '<body', '<!DOCTYPE'];
        $start = strtolower(substr($content, 0, 200));
        
        foreach ($htmlPatterns as $pattern) {
            if (strpos($start, $pattern) !== false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Parsea los headers HTTP de la respuesta
    */
    private function parseHttpResponseHeaders($headers) {
        $result = [];
        
        if (!empty($headers)) {
            // Primera línea contiene el código de respuesta
            if (isset($headers[0])) {
                if (preg_match('/HTTP\/[\d.]+\s+(\d+)/', $headers[0], $matches)) {
                    $result['response_code'] = (int)$matches[1];
                }
            }
            
            // Procesar resto de headers
            foreach ($headers as $header) {
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $result[strtolower(trim($key))] = trim($value);
                }
            }
        }
        
        return $result;
    }


    private function parsearExtensionesPermitidas($cadenaExtensiones) {
        $extensiones = [];
        
        // Separar por comas
        $items = explode(',', $cadenaExtensiones);
        
        foreach ($items as $item) {
            $item = trim($item);
            
            // Caso especial: image/* (todos los tipos de imagen)
            if ($item === 'image/*') {
                // Agregar extensiones comunes de imagen
                $extensiones = array_merge($extensiones, [
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'tif', 'svg', 'ico'
                ]);
                continue;
            }
            
            // Caso especial: application/pdf
            if ($item === 'application/pdf') {
                $extensiones[] = 'pdf';
                continue;
            }
            
            // Eliminar punto inicial si existe
            if (strpos($item, '.') === 0) {
                $item = substr($item, 1);
            }
            
            // Convertir a minúsculas y agregar
            if (!empty($item)) {
                $extensiones[] = strtolower($item);
            }
        }
        
        // Eliminar duplicados y devolver array único
        return array_unique($extensiones);
    }
    
    public function codificarUrlConEspacios($url) {
        $parsed = parse_url($url);
        if (isset($parsed['path'])) {
            // Codificar cada segmento de la ruta
            $segmentos = explode('/', $parsed['path']);
            foreach ($segmentos as &$segmento) {
                if (!empty($segmento)) {
                    $segmento = rawurlencode($segmento);
                    $segmento = str_replace('%2F', '/', $segmento); // Mantener barras
                }
            }
            $parsed['path'] = implode('/', $segmentos);
        }
        
        // Reconstruir URL
        return $this->construirUrl($parsed);
    }
    
    public function construirUrl($parsed) {
        $url = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        if (isset($parsed['user'])) {
            $url .= $parsed['user'] . (isset($parsed['pass']) ? ':' . $parsed['pass'] : '') . '@';
        }
        $url .= isset($parsed['host']) ? $parsed['host'] : '';
        $url .= isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $url .= isset($parsed['path']) ? $parsed['path'] : '';
        $url .= isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $url .= isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
        return $url;
    }

    //Extensiones permitidas
    public function getValidFormatFile($fileName) {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($extension, $this->extensionesPermitidas);
    }

    //validar el size file
    public function getValidFileSizeMax($url) {
        // Codificar URL para manejar espacios
        $url = $this->codificarUrlConEspacios($url);

        // Usar cURL para obtener solo headers
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Solo encabezados
        curl_setopt($ch, CURLOPT_HEADER, false); // No incluir encabezados en la respuesta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devolver la respuesta como string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // Ejecutar cURL
        curl_exec($ch); // Ejecutar para obtener la información
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Verificar errores de cURL
        if (!empty($error)) {
            // Si hay error, permitir descarga para validación en tiempo real
            return true;
        }

        // Verificar código HTTP
        if ($httpCode >= 400) {
            return false;
        }
        
        // Si no se puede obtener el tamaño, permitir descarga (validación durante la descarga)
        if ($contentLength === -1 || $contentLength === false) {
            return true;
        }
        
        return $contentLength <= $this->tamanoMaximo;
    }

    // Sanitizar nombre de archivo
    public function sanitizarNombreArchivo($fileName) {
        // Decodificar primero por si acaso ya está codificado
        $fileName = rawurldecode($fileName);
        // Sanitizar caracteres peligrosos
        $fileName = preg_replace('/[^a-zA-Z0-9._áéíóúÁÉÍÓÚñÑüÜ\- ]/', '_', $fileName);
        // Limitar longitud
        if (strlen($fileName) > 255) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $nombreBase = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = substr($nombreBase, 0, 250 - strlen($extension)) . '.' . $extension;
        }
        
        // Evitar nombres peligrosos
        $fileName = str_replace(['..', './', '.\\'], '_', $fileName);
        
        return $fileName;
    }

    // Validar URL de la descarga
    public function getValidUrlDownload($url) {
        // Verificar que sea una URL válida
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Verificar protocolo permitido
        $parsed = parse_url($url);
        if (!in_array(strtolower($parsed['scheme']), ['http', 'https'])) {
            return false;
        }
        
        // Verificar dominios permitidos (opcional)
        $dominiosPermitidos = [
            'drive.google.com',
            'google.com',
            'onedrive.live.com',
            'sharepoint.com',
            'dropbox.com',
            'dropboxusercontent.com',
            '1drv.ms' // Agregar soporte para URLs cortas de OneDrive
        ];
        
        $host = strtolower($parsed['host']);
        $dominioPermitido = false;
        foreach ($dominiosPermitidos as $dominio) {
            if (strpos($host, $dominio) !== false) {
                $dominioPermitido = true;
                break;
            }
        }
        
        return true; // Permitir todas por ahora, pero puedes restringir si lo deseas
    }


    /**
     * Genera una URL de descarga directa a partir de un enlace compartido de Google
     * (Docs, Sheets, Slides o archivo genérico de Drive).
     *
     * Ejemplos soportados:
     *  - https://docs.google.com/document/d/ID/edit?usp=sharing
     *  - https://docs.google.com/spreadsheets/d/ID/edit?usp=sharing
     *  - https://docs.google.com/presentation/d/ID/edit?usp=sharing
     *  - https://drive.google.com/file/d/ID/view?usp=sharing
     *  - https://drive.google.com/open?id=ID
     *
     * Retorna:
     *  - string URL de descarga directa
     *  - false si no reconoce el formato
     *
     * @param string      $url            URL compartida original
     * @param string|null $preferFormat   Formato preferido (opcional: 'pdf', 'docx', 'pptx', 'xlsx', 'csv', etc.)
     * @return string|false
     */
    public function generarUrlDescargaGoogle($url, $preferFormat = null) {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            return false;
        }

        $host = strtolower($parts['host']);
        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? $parts['query'] : '';

        $id   = null;
        $kind = null; // document | spreadsheet | presentation | file

        // -------------------------
        // 1) URLs de docs.google.com
        // -------------------------
        if (strpos($host, 'docs.google.com') !== false) {
            // Google Docs (tipo Word)
            if (preg_match('#^/document/d/([^/]+)#', $path, $m)) {
                $kind = 'document';
                $id   = $m[1];
            }
            // Google Sheets (tipo Excel)
            elseif (preg_match('#^/spreadsheets/d/([^/]+)#', $path, $m)) {
                $kind = 'spreadsheet';
                $id   = $m[1];
            }
            // Google Slides (tipo PowerPoint)
            elseif (preg_match('#^/presentation/d/([^/]+)#', $path, $m)) {
                $kind = 'presentation';
                $id   = $m[1];
            } else {
                // No coincide con ningún patrón conocido
                return false;
            }
        }
        // -------------------------
        // 2) URLs de drive.google.com
        // -------------------------
        elseif (strpos($host, 'drive.google.com') !== false) {
            // /file/d/ID/view
            if (preg_match('#^/file/d/([^/]+)#', $path, $m)) {
                $kind = 'file';
                $id   = $m[1];
            }
            // /open?id=ID
            elseif (strpos($path, '/open') === 0 && $query) {
                parse_str($query, $q);
                if (!empty($q['id'])) {
                    $kind = 'file';
                    $id   = $q['id'];
                }
            }

            if ($id === null) {
                return false;
            }
        } else {
            // No es dominio de Google Docs/Drive soportado
            return false;
        }

        // Validación mínima del ID
        if (!$id || !preg_match('/^[a-zA-Z0-9_\-]+$/', $id)) {
            return false;
        }

        // -------------------------
        // 3) Construir URL de descarga según tipo
        // -------------------------
        switch ($kind) {
            case 'document':
                // Google Docs (tipo Word)
                // Formato por defecto: DOCX (o PDF si prefieres)
                $format = $preferFormat ?: 'docx'; // 'pdf' si quieres forzar PDF
                return "https://docs.google.com/document/d/{$id}/export?format={$format}";

            case 'spreadsheet':
                // Google Sheets (tipo Excel)
                // Formato por defecto: XLSX (puedes usar 'csv' si prefieres)
                $format = $preferFormat ?: 'xlsx'; // 'csv' si quieres hoja específica
                // Nota: para CSV de una hoja específica, deberías añadir &gid=XXX
                return "https://docs.google.com/spreadsheets/d/{$id}/export?format={$format}";

            case 'presentation':
                // Google Slides (tipo PowerPoint)
                // Formato por defecto: PPTX
                $format = $preferFormat ?: 'pptx'; // 'pdf' también es común
                return "https://docs.google.com/presentation/d/{$id}/export?format={$format}";

            case 'file':
                // Archivo genérico de Drive (zip, pdf, jpg, docx subido, etc.)
                // URL estándar de descarga
                // preferFormat no aplica aquí, Drive entrega el binario tal cual
                return "https://drive.google.com/uc?export=download&id={$id}";

            default:
                return false;
        }
    }

    // Protección contra comandos del sistema
    public function escaparParaSistema($rutaArchivo) {
        // Usar escapeshellarg para proteger rutas
        return escapeshellarg($rutaArchivo);
    }

    public function transformarUrl($url) {
        $url = $this->codificarUrlConEspacios(trim($url)); // Codificar al inicio
        // Google Drive
        if (strpos($url, 'drive.google.com/file/d/') !== false) {
            $pattern = '/\/file\/d\/([^\/]+)/';
            if (preg_match($pattern, $url, $matches)) {
                $fileId = $matches[1];
                return "https://drive.google.com/uc?export=download&id=" . $fileId;
            }
        }
        
        // Google Drive (formato corto)
        elseif (strpos($url, 'drive.google.com/open?id=') !== false) {
            $parsed = parse_url($url);
            parse_str($parsed['query'], $params);
            if (isset($params['id'])) {
                return "https://drive.google.com/uc?export=download&id=" . $params['id'];
            }
        }
        
        // OneDrive - Compartir enlace
        elseif (strpos($url, 'onedrive.live.com/') !== false && strpos($url, 'share=') !== false) {
            // Extraer el ID del archivo
            $parsed = parse_url($url);
            parse_str($parsed['query'], $params);
            if (isset($params['cid']) && isset($params['id'])) {
                return "https://onedrive.live.com/download.aspx?cid=" . $params['cid'] . "&resid=" . $params['id'];
            }
        }
        // OneDrive - Vista directa (URL corta)
        elseif (strpos($url, '1drv.ms/') !== false) {
            // Expandir URL corta (esto requiere una solicitud adicional)
            $expandedUrl = $this->expandirUrlCorta($url);
            if ($expandedUrl) {
                // Recursivo para procesar la URL expandida
                // No usar $this->transformarUrl() recursivamente para evitar bucles infinitos potenciales
                // En su lugar, aplicar transformaciones específicas
                return $this->transformarUrlOneDrive($expandedUrl);
            }
        }
        
        // OneDrive - Vista moderna
        elseif (strpos($url, 'my.sharepoint.com/') !== false) {
            // Convertir a URL de descarga directa
            if (strpos($url, '?download=1') === false) {
                // Verificar si ya tiene parámetros
                if (strpos($url, '?') !== false) {
                    return $url . '&download=1';
                } else {
                    return $url . '?download=1';
                }
            }
        }
        
        // Dropbox
        elseif (strpos($url, 'dropbox.com/') !== false) {
            // Convertir URL de vista a descarga directa
            $url = str_replace('www.dropbox.com', 'dl.dropboxusercontent.com', $url);
            $url = preg_replace('/\?dl=0$/', '', $url);
            $url = preg_replace('/\?dl=1$/', '', $url);
            return $url . '?dl=1';
        }
        
        // URLs directas
        return $url;
    }

    /**
     * Transformaciones específicas para URLs de OneDrive expandidas
     */
    private function transformarUrlOneDrive($url) {
        // Aplicar transformaciones específicas de OneDrive si es necesario
        if (strpos($url, 'my.sharepoint.com/') !== false) {
            if (strpos($url, '?download=1') === false) {
                if (strpos($url, '?') !== false) {
                    return $url . '&download=1';
                } else {
                    return $url . '?download=1';
                }
            }
        }
        return $url;
    }


    private function expandirUrlCorta($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // No seguir automáticamente
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL); // Obtener URL de redirección
        $error = curl_error($ch);
        curl_close($ch);
        
        if (!empty($error)) {
            return false;
        }

        if (($httpCode == 301 || $httpCode == 302 || $httpCode == 303) && !empty($redirectUrl)) {
            return $redirectUrl;
        }
        
        // Alternativa: Parsear manualmente la respuesta para encontrar Location header
        if (!empty($response)) {
             $lines = explode("\n", $response);
             foreach ($lines as $line) {
                 if (stripos($line, 'Location:') === 0) {
                     $parts = explode(':', $line, 2);
                     $location = trim($parts[1]);
                     if (!empty($location)) {
                         return $location;
    				 }
                 }
             }
        }

        return false;
    }
    // Aplica el limite de tamaño del archivo en tiempo real
    public function descargarFilesizeLimit($url, $rutaDestino) {
        // Codificar URL para manejar espacios
        $url = $this->codificarUrlConEspacios($url);

        // Usar cURL para descargar
        $ch = curl_init();
        $fp = fopen($rutaDestino, 'wb');
        //********************************************************************************************************
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //********************************************************************************************************
        // Monitorear el tamaño durante la descarga
        $tamanoDescargado = 0;
        $self = $this;
        //********************************************************************************************************
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$tamanoDescargado, $fp, $self) {
            $tamanoDescargado += strlen($data);
            // Verificar límite de tamaño
            if ($tamanoDescargado > $self->tamanoMaximo) {
                return -1; // Detener descarga
            }
            
            return fwrite($fp, $data);
        });
        //********************************************************************************************************
        $resultado = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        fclose($fp);
        //********************************************************************************************************
        // Verificar si se excedió el tamaño
        if ($tamanoDescargado > $self->tamanoMaximo) { // Corregido: usar $self->tamanoMaximo
            if (file_exists($rutaDestino)) {
                unlink($rutaDestino);
            }
            return [
                'success' => false,
                'error' => 'El archivo excede el tamaño máximo permitido de ' . simad_util::formatBytes($this->tamanoMaximo)
            ];
        }
        //********************************************************************************************************
        // Verificar si la descarga fue exitosa
        if (!$resultado || $httpCode >= 400) {
            if (file_exists($rutaDestino)) {
                unlink($rutaDestino);
            }
            return [
                'success' => false,
                'error' => "Error HTTP: $httpCode - $error"
            ];
        }
        //********************************************************************************************************
        return [
            'success' => true,
            'file_size' => $tamanoDescargado,
        ];
    }

    public function descargarArchivo($urlOriginal,$urlcodificada) {
        // Transformar URL si es necesario (usar la original para transformar)
        $urlDescarga = $this->transformarUrl($urlOriginal);
        //********************************************************************************************************
        if (!$this->getValidUrlDownload($urlDescarga)) {
            return [
                'success' => false,
                'error' => 'URL inválida o no permitida'
            ];
        }
        //********************************************************************************************************
        if (!$this->getValidFileSizeMax($urlDescarga)) {
            return [
                'success' => false,
                'error' => 'El archivo excede el tamaño máximo permitido de ' . simad_util::formatBytes($this->tamanoMaximo)
            ];
        }
        //********************************************************************************************************
        // Obtener nombre del archivo
        $fileName = $this->getFileNameByCurl($urlDescarga, $urlOriginal);
        // Limpiar el nombre del archivo
        $fileName = $this->sanitizarNombreArchivo($fileName);
        //********************************************************************************************************
        if (!$this->getValidFormatFile($fileName)) {
            return [
                'success' => false,
                'error' => 'Tipo de archivo no permitido, extensiones permitidas: ' . implode(', ', $this->extensionesPermitidas)
            ];
        }
        //********************************************************************************************************
        $directorioDestino = rtrim($this->downloads_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        //********************************************************************************************************
        // Crear directorio si no existe
        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                return [
                    'success' => false,
                    'error' => 'No se puede crear el directorio de destino'
                ];
            }
        }
        //********************************************************************************************************
        if (!is_writable($directorioDestino)) {
            return [
                'success' => false,
                'error' => 'No se tienen permisos de escritura en el directorio de destino'
            ];
        }
        //********************************************************************************************************
        // Ruta completa de destino
        $rutaCompleta = $directorioDestino . $fileName;
        //******************************************************************************************************** 
        // Prevenir path traversal
        $rutaReal = realpath($directorioDestino);
        $rutaArchivoReal = realpath(dirname($rutaCompleta)) . DIRECTORY_SEPARATOR . basename($rutaCompleta);
        if (strpos($rutaArchivoReal, $rutaReal) !== 0) {
            return [
                'success' => false,
                'error' => 'Ruta de archivo no válida'
            ];
        }
        //********************************************************************************************************
        $resultado = $this->descargarFilesizeLimit($urlDescarga, $rutaCompleta);
        //********************************************************************************************************
        if (!$resultado['success']) {
            return $resultado;
        }
        //********************************************************************************************************
        $fileSize = filesize($rutaCompleta);
        if ($fileSize === 0) {
            unlink($rutaCompleta);
            return [
                'success' => false,
                'error' => 'El archivo descargado está vacío'
            ];
        }
        //********************************************************************************************************
        $mimeType = null;
        // Usar finfo (más preciso)
        if (class_exists('finfo') && file_exists($rutaCompleta)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($rutaCompleta);
        }
        
        // Usar mime_content_type (fallback)
        if (!$mimeType && function_exists('mime_content_type') && file_exists($rutaCompleta)) {
            $mimeType = mime_content_type($rutaCompleta);
        }
        //********************************************************************************************************
        return [
            'success' => true,
            'file_path' => $rutaCompleta,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'url_original' => $urlOriginal,
            'url_transformada' => $urlDescarga,
            'path_absolute' => $this->path_absolute,
            'path_relative' => $this->path_relative,
            'extension' => strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Obtener nombre desde Content-Disposition (método seguro)
     */
    private function getFileNameByCurl($url, $urlOriginal) {
        // Codificar URL para manejar espacios
        $url = $this->codificarUrlConEspacios($url);

        try {
            // Usar cURL para obtener solo headers
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // $headers = curl_getinfo($ch, CURLINFO_HEADER_OUT); // Esta línea no es necesaria
            curl_close($ch);
            
            // Verificar si fue exitoso
            if ($httpCode >= 200 && $httpCode < 300 && !empty($response)) {
                // Buscar Content-Disposition en la respuesta completa
                $lines = explode("\r\n", $response); // Usar \r\n para separar headers HTTP
                foreach ($lines as $line) {
                    if (stripos($line, 'Content-Disposition:') !== false) {
                        $contentDisposition = trim($line);
                    // Extraer filename
                    if (preg_match('/filename[^;=\n]*=((["\']).*?\2|[^;\n]*)/', $contentDisposition, $matches)) {
                        $fileName = trim($matches[1], '"\'');
                            if (!empty($fileName)) {
                                return rawurldecode($fileName); // Decodificar el nombre si está codificado
                            }
                        }
                        // Alternativa: buscar filename* para nombres codificados UTF-8
                        if (preg_match('/filename\*[^;=\n]*=(?:UTF-8\'\')?([^;\n]*)/', $contentDisposition, $matches)) {
                            $fileName = trim($matches[1], '"\'');
                            if (!empty($fileName)) {
                                // Decodificar UTF-8 codificado como 'UTF-8''es'%C3%A1lbum.pdf
                                if (strpos($fileName, "UTF-8''") === 0) {
                                    $fileName = substr($fileName, 7); // Eliminar 'UTF-8''
                                }
                        return rawurldecode($fileName);
                    }
                }
            }
                }
            }
            // Si no se encuentra en Content-Disposition, usar fallback
            return $this->getFileNameByHeaders($url, $urlOriginal);
            
        } catch (\Exception $e) {
            // Si hay error, usar fallback
            return $this->getFileNameByHeaders($url, $urlOriginal);
        }
    }

    private function getFileNameByHeaders($urlDescarga, $urlOriginal) {
        // Codificar URLs para manejar espacios
        $urlDescarga = $this->codificarUrlConEspacios($urlDescarga);
        $urlOriginal = $this->codificarUrlConEspacios($urlOriginal);

        // Intentar obtener nombre del Content-Disposition header usando get_headers
        $headers = @get_headers($urlOriginal, 1); // Usar la URL original para get_headers
        if ($headers !== false && isset($headers['Content-Disposition'])) {
            $disposition = $headers['Content-Disposition'];
            if (preg_match('/filename[^;=\n]*=((["\']).*?\2|[^;\n]*)/', $disposition, $matches)) {
                $fileName = trim($matches[1], '"\'');
                if (!empty($fileName)) {
                    return rawurldecode($fileName); // Decodificar
                }
            }
        }
        // Fallback: obtener de la URL transformada
        $fileName = basename(parse_url($urlDescarga, PHP_URL_PATH));
        if (empty($fileName) || $fileName === 'download.aspx') {
            // Fallback adicional: obtener de la URL original
            $fileName = basename(parse_url($urlOriginal, PHP_URL_PATH));
        }
        
        // Último fallback
        if (empty($fileName)) {
            $fileName = 'FileSharedLinkSync_' . time();
        }

        $headers = null;
        return rawurldecode($fileName); // Decodificar el nombre final
    }
}
?>