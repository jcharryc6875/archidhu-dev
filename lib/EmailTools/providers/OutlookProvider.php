<?php
require_once('lib/EmailTools/OAuth2Providers.php');
require_once('lib/EmailTools/providers/EmailCoreProvider.php');
require_once('lib/EmailTools/autoload.php');

use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Carbon\Carbon;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use \Webklex\PHPIMAP\Query\WhereQuery;

// Proveedor Outlook
class OutlookProvider extends EmailCoreProvider  {
    private $tenantId;
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    //private $scope = 'https://graph.microsoft.com/Mail.Read';
    //private $scope = 'https://outlook.office.com/IMAP.AccessAsApp';
    private $scope = "https://outlook.office365.com/.default";
    private $authUrl = 'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize';
    private $tokenUrl = "https://login.microsoftonline.com/%s/oauth2/v2.0/token";
    private $grant_type = "client_credentials";//authorization_code
    public $accessToken = null;
    public $refreshToken = null;
    protected $validate_cert = true;
    protected $query_post = array();
    protected $imap_config = array();
    protected $protocol = "imap";
    protected $authentication = "oauth";
    private $imapResource;

    private $server;
    private $port;
    private $encryption;
    private $folder_read;
    
    public function __construct($settings, $authType = 'oauth2', $folder = 'INBOX') {
        parent::__construct($authType);
        //************************************************************************************
        if(!isset($settings['client_id']))
            throw new Exception("Debe suministrar un client_id");
        //************************************************************************************
        if(!isset($settings['tenant_id']))
            throw new Exception("Debe suministrar un tenant_id");
        //************************************************************************************
        if(!isset($settings['client_secret']))
            throw new Exception("Debe suministrar un client_secret");
        //************************************************************************************
        $this->clientId = $settings['client_id'];
        $this->tenantId = $settings['tenant_id'];
        $this->clientSecret = $settings['client_secret'];
        $this->tokenUrl = sprintf($this->tokenUrl,$this->tenantId);
        $this->authUrl = sprintf($this->authUrl,$this->tenantId);
        $this->folder_read = $folder;
        //************************************************************************************
        if(isset($settings['redirect_uri']))
            $this->redirectUri = $settings['redirect_uri'];
        //************************************************************************************
        if($authType =! 'oauth2'){
            if(isset($settings['username']))
                throw new Exception("Debe suministrar el nombre de usuario para acceder a la cuenta de correo");

            if(isset($settings['password']))
                throw new Exception("Debe suministrar las credenciales de acceso a la cuenta");
        }else{
            $this->accessToken = $this->getAccessTokenAzure();
            if($this->accessToken == null)
                throw new Exception("Error al obtener el token de acceso a la cuenta, revise los parametros");
        }
        //************************************************************************************
        $this->query_post = http_build_query([
            'client_id' => $settings['client_id'],
            'grant_type' => $this->grant_type,
            'scope' => $this->scope,
            'client_secret' => $settings['client_secret'],
        ]);
        //************************************************************************************
        $this->imap_config = [
            'host' => $settings['host_name'],
            'port' => $settings['port_listen'],
            'encryption' => $settings['encryption_listen'],
            'validate_cert' => $this->validate_cert,
            'username' => $settings['username'],
            'password' => $this->accessToken,
            'protocol' => $this->protocol,
            'authentication' => $this->authentication,
            'options' => [
                'fetch_order' => 'desc',
                'fetch' => \Webklex\PHPIMAP\IMAP::FT_UID,
                'debug' => true,
            ]
        ];
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

    public function getAccessTokenBasic($code) {
        $data = [
            'tenant_id' => $this->tenantId,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];
        
        $response = $this->makeHttpRequest(
            $this->tokenUrl,
            ['Content-Type: application/x-www-form-urlencoded'],
            http_build_query($data),
            'POST'
        );
        
        $this->accessToken = $response['access_token'];
        $this->refreshToken = $response['refresh_token'] ?? null;
        
        return $response;
    }

    function getAccessTokenAzure() 
    {
        $url  = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";
        $post = http_build_query([
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'https://outlook.office365.com/.default',
            'grant_type'    => 'client_credentials',
        ]);
        //************************************************************************************
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $post,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 20,
        ]);
        //************************************************************************************
        $raw = curl_exec($ch);
        if ($raw === false) {
            throw new \RuntimeException('CURL error: ' . curl_error($ch));
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //************************************************************************************
        $data = json_decode($raw, true);
        if ($code !== 200 || empty($data['access_token'])) {
            throw new \RuntimeException("Token error ($code): " . ($data['error_description'] ?? $raw));
        }
        //************************************************************************************
        // Guardamos exp como unix time (ahora + expires_in)
        $data['exp'] = time() + (int)$data['expires_in'];    
        //************************************************************************************
        if (isset($data['access_token'])) {
            return $data['access_token'];
        } else {
            throw new Exception('Error obteniendo token: ' . $data['error_description']);
        }
    }

    public function getTokenAccessOauth()
    {
        $query_post = http_build_query([
            'client_id' => $this->clientId,
            'scope' => 'https://outlook.office.com/.default',
            'client_secret' => $this->clientSecret,
            'grant_type' => $this->grant_type
        ]);
        //************************************************************************************
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch,CURLOPT_URL, sprintf($this->tokenUrl,$this->tenantId));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$query_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_result = curl_exec($ch);
            if (empty($curl_result)) {
                throw new Exception('Missing results.');
            }
            $curl_result = json_decode($curl_result);
            if (empty($curl_result)) {
                throw new Exception('Error decoding json result.');
            }
            if (!isset($curl_result->access_token)) {
                throw new Exception('Missing access token from result.');
            }
            
            return $curl_result->access_token;
        }catch(\Exception $ex){
            return null;
        } finally {
            curl_close($ch);
        }
    }

    function getAccessToken() {
        $data = [
            'client_id' => $this->clientId,
            'scope' => 'https://outlook.office.com/.default',
            'client_secret' => $this->clientSecret,
            'grant_type' => $this->grant_type
        ];
        //************************************************************************************
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        //************************************************************************************
        $response = curl_exec($ch);
        curl_close($ch);
        //************************************************************************************
        $tokenData = json_decode($response, true);
        //************************************************************************************
        if (isset($tokenData['access_token'])) {
            return $tokenData['access_token'];
        } else {
            throw new Exception('Error obteniendo token: ' . $response);
        }
    }

    public function authenticate($credentials) {
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        //************************************************************************************
        if ($this->authType === 'oauth2') {
            return $this->authenticateOAuth2();
        } else {
            return $this->authenticatePassword();
        }
    }

    private function authenticateOAuth2() {
        try {
            $cm = new Webklex\PHPIMAP\ClientManager();
            $client = $cm->make($this->imap_config);
            //$client->setDebug(true);

            //Connect to the IMAP Server
            $this->imapResource = $client->connect();
            
            if (!$this->imapResource) {
                throw new Exception("Error de autenticación IMAP: " . imap_last_error());
            }
            
            return ['status' => 'authenticated', 'method' => 'imap'];
        } catch (ConnectionFailedException $th) {
            $this->debugAdditionalInfo($this->imap_config);
        } catch (\Exception $th) {
            $this->debugAdditionalInfo($this->imap_config);
        } catch (\Throwable $th) {
            $this->debugAdditionalInfo($this->imap_config);
        }
    }
    
    private function authenticatePassword() {
        return ['status' => 'authenticated', 'method' => 'password'];
    }

    public function getAuthUrl() {
        if ($this->authType !== 'oauth2') {
            throw new Exception("URL de autorización solo disponible para OAuth2");
        }
        
        $params = [
            'tenant_id' => $this->tenantId,
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'response_mode' => 'query'
        ];
        
        return $this->authUrl . '?' . http_build_query($params);
    }
    
    public function refreshAccessToken($refreshToken) {
        $data = [
            'tenant_id' => $this->tenantId,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        $response = $this->makeHttpRequest(
            $this->tokenUrl,
            ['Content-Type: application/x-www-form-urlencoded'],
            http_build_query($data),
            'POST'
        );
        
        $this->accessToken = $response['access_token'];
        return $response;
    }
    
    public function testConnection() {
        try {
            if ($this->authType === 'oauth2') {
                $url = "https://graph.microsoft.com/v1.0/me";
                $headers = [
                    'Authorization: Bearer ' . $this->accessToken,
                    'Content-Type: application/json'
                ];
                $this->makeHttpRequest($url, $headers);
            } else {
                return $this->testImapConnection();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function testImapConnection() {
        $imap = imap_open('{outlook.office365.com:993/imap/ssl}INBOX', $this->username, $this->password);
        if ($imap) {
            imap_close($imap);
            return true;
        }
        return false;
    }

    public function getEmailsOld($maxResults = 10, $lastUid = null) {
        if ($this->authType === 'oauth2') {
            return $this->getEmailsOAuth2($maxResults);
        } else {
            return $this->getEmailsImap($maxResults);
        }
    }
    
    public function getEmails($per_page = 100, $lastUid = null) {
        if (!$this->imapResource) {
            throw new Exception("No hay conexión IMAP activa");
        }
        //**************************************************************************************************
        $folder = $this->imapResource->getFolder($this->folder_read);

        // Obtener correos solo del día de hoy
        $today = date('d-M-Y');
        
        // CRÍTICO: Usar FLAGS en lugar de cargar todo el mensaje
        $query = $folder->query()->since($today)->setFetchFlags(false)->setFetchBody(false);
        
        // Obtener solo los UIDs primero (muy liviano)
        $messages = $query->get();
        
        echo "Total de mensajes hoy: " . $messages->count() . "\n";

        // Procesar en lotes de 10 mensajes
        $batchSize = 10;
        $processed = 0;
        $emails = array();
        //**************************************************************************************************
        foreach ($messages->chunk($batchSize) as $batch) {
            foreach ($batch as $message) {
                try {
                    $attributes = $message->getHeader()->getAttributes();
                    $asunto_original = (string)$attributes["subject"];
                    $flags = $message->getFlags();
                    //**************************************************************************************
                    // Extraer solo lo necesario
                    $uid = $message->getUid();
                    $message_id = (string)$attributes["message_id"];
                    //**************************************************************************************
                    if(EmailTrackingPeer::isMessageProcessed($message_id,$this->username,$uid)){
                        continue;
                    }
                    //**************************************************************************************
                    $subject_decode = $this->decodificarAsunto((string)$attributes["subject"]);
                    $from_decode = $this->decodificarAsunto((string)$attributes["from"]);
                    $to_decode = $this->decodificarAsunto((string)$attributes["to"]);
                    //**************************************************************************************
                    $attributeDate = $message->getDate();
                    $carbonDate = $attributeDate->toDate();
                    $dateColombia = $carbonDate->setTimezone('America/Bogota'); 
                    //**************************************************************************************
                    $body_data = $this->obtenerCuerpoMensaje($message);
                    //**************************************************************************************
                    $email = [
                        'message_id' => (string)$attributes["message_id"],
                        'subject' => $subject_decode,
                        'from_email' => trim($from_decode),
                        'from_address' => (string)$attributes["fromaddress"],
                        'to_email' => trim($to_decode),
                        'to_address' => (string)$attributes["toaddress"],
                        'cc_email' => $message->getCc(),
                        'date_received' => $dateColombia->format('Y-m-d H:i:s'),
                        'seen' => $flags['Seen'] != 'Seen' ? false : true,
                        'flagged' => null,
                        'body' => trim($body_data),
                        'content_html' => null,
                        'clean_html' => $this->extraerTextoPlano($message),
                        'size' => $message->getSize(),
                        'has_attachments' => false,
                        'provider' => 'imap',
                        'message_uid' => $uid
                    ];
                    //**************************************************************************************
                    echo "************************************************************************************\n";
                    echo "Asunto Original: {$asunto_original}\n";
                    echo "Procesando UID: {$uid} Fecha Colombia: {$dateColombia->format('Y-m-d H:i:s')} - Asunto: {$subject_decode}\n";
                    echo "************************************************************************************";                    
                    // Marcar como leído si es necesario
                    // $message->setFlag(['Seen']);
                    //**************************************************************************************
                    $email['attachments'] = $this->getAttachmentsMessage($message,$email);                
                    $this->saveMessageAsEml($message,$email);
                    $email['has_attachments'] = count($email['attachments']) > 0;
                    //**************************************************************************************
                    $emails[] = $email;
                    $processed++;
                    //**************************************************************************************
                    // Liberar memoria del mensaje
                    unset($body_data, $message);
                }catch (\Webklex\PHPIMAP\Exceptions\RuntimeException $e) {
                    $message_error =  'Exception error procesando mensaje : '.  $e->getMessage();
                    error_log($message_error);
                }catch (PropelException $e) {
                    $message_error =  'Exception error procesando mensaje : '.  $e->getMessage();
                    error_log($message_error);
                }catch (\Exception $e) {
                    $message_error =  'Exception error procesando mensaje : '.  $e->getMessage();
                    error_log($message_error);
                }catch (\IOException $e) {
                    $message_error =  'Exception error procesando mensaje : '.  $e->getMessage();
                    error_log($message_error);
                }catch (\Throwable $e) {
                    $message_error =  'Exception error procesando mensaje : '.  $e->getMessage();
                    error_log($message_error);
                }
            }
            //**************************************************************************************************
            // Liberar memoria del batch
            unset($batch);
            //**************************************************************************************************
            // Forzar recolección de basura cada lote
            gc_collect_cycles();
            //**************************************************************************************************
            echo "Procesados: {$processed} / " . $messages->count() . " - Memoria: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB\n";
        }
        //*************************************************************************************************************
        simad_util::deleteDirAndFiles($this->folder_tmp);
        //*************************************************************************************************************
        return $emails;
    }

    public function obtenerCuerpoMensaje($message)
    {
        $body = '';    
        try {
            // Forzar recarga del mensaje completo con body
            $message->parseBody();
            
            // Método 1: Intentar HTML primero
            try {
                if ($message->hasHTMLBody()) {
                    $bodyParts = $message->getHTMLBody();
                    
                    // Si es un array, concatenar
                    if (is_array($bodyParts)) {
                        $body = implode("\n", $bodyParts);
                    } else {
                        $body = $bodyParts;
                    }
                }
            } catch (\Exception $e) {
                // Continuar al siguiente método
            }
            
            // Método 2: Si no hay HTML, intentar texto plano
            if (empty($body)) {
                try {
                    if ($message->hasTextBody()) {
                        $bodyParts = $message->getTextBody();
                        
                        if (is_array($bodyParts)) {
                            $body = implode("\n", $bodyParts);
                        } else {
                            $body = $bodyParts;
                        }
                    }
                } catch (\Exception $e) {
                    // Continuar
                }
            }
            
            // Método 3: Obtener bodies directamente
            if (empty($body)) {
                $bodies = $message->getBodies();
                
                if (!empty($bodies)) {
                    // Buscar HTML primero
                    foreach ($bodies as $bodyPart) {
                        if ($bodyPart->content_type && 
                            stripos($bodyPart->content_type, 'text/html') !== false) {
                            $body = $bodyPart->content;
                            break;
                        }
                    }
                    
                    // Si no hay HTML, buscar texto
                    if (empty($body)) {
                        foreach ($bodies as $bodyPart) {
                            if ($bodyPart->content_type && 
                                stripos($bodyPart->content_type, 'text/plain') !== false) {
                                $body = $bodyPart->content;
                                break;
                            }
                        }
                    }
                    
                    // Si aún nada, tomar el primer body
                    if (empty($body) && isset($bodies[0])) {
                        $body = $bodies[0]->content ?? '';
                    }
                }
            }
            
            // Método 4: Fallback usando estructura
            if (empty($body)) {
                $structure = $message->getStructure();
                if ($structure && isset($structure->parts)) {
                    foreach ($structure->parts as $part) {
                        if (isset($part->type) && $part->type == 'text') {
                            $body = $message->getBodyPart($part->part_number ?? 1);
                            if (!empty($body)) break;
                        }
                    }
                }
            }
            
            // Limpiar
            $body = trim($body);
            
        } catch (\Exception $e) {
            echo "Error obteniendo body: " . $e->getMessage() . "\n";
        }
        
        return $body ?: '(Sin contenido)';
    }

    /**
     * Extrae solo texto plano del mensaje (sin HTML)
     * Útil para búsquedas o análisis de texto
     */
    private function extraerTextoPlano($message): string
    {
        $texto = '';
        
        try {
            $message->parseBody();
            
            // Primero intentar texto plano directo
            if ($message->hasTextBody()) {
                $textBody = $message->getTextBody();
                $texto = is_array($textBody) ? implode("\n", $textBody) : $textBody;
            }
            
            // Si no hay texto plano, extraer de HTML
            if (empty($texto) && $message->hasHTMLBody()) {
                $htmlBody = $message->getHTMLBody();
                $html = is_array($htmlBody) ? implode("\n", $htmlBody) : $htmlBody;
                
                // Convertir HTML a texto plano
                $texto = $this->htmlATextoPlano($html);
            }
            
            // Fallback
            if (empty($texto)) {
                $bodies = $message->getBodies();
                
                if (!empty($bodies) && is_array($bodies)) {
                    foreach ($bodies as $bodyPart) {
                        if (isset($bodyPart->content) && !empty($bodyPart->content)) {
                            if (isset($bodyPart->content_type) && 
                                stripos($bodyPart->content_type, 'text/html') !== false) {
                                $texto = $this->htmlATextoPlano($bodyPart->content);
                            } else {
                                $texto = $bodyPart->content;
                            }
                            break;
                        }
                    }
                }
            }
            
            // Limpiar
            $texto = trim($texto);
            
            if (empty($texto)) {
                $texto = '(Sin contenido)';
            }
            
        } catch (\Exception $e) {
            echo "Error extrayendo texto: " . $e->getMessage() . "\n";
            $texto = '(Error al extraer)';
        }
        
        return $texto;
    }

    /**
     * Convierte HTML a texto plano limpio
     */
    private function htmlATextoPlano(string $html): string
    {
        if (empty($html)) {
            return '';
        }
        
        // Remover scripts y styles
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        
        // Convertir <br> y <p> a saltos de línea
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n\n", $html);
        $html = preg_replace('/<\/div>/i', "\n", $html);
        
        // Remover todas las etiquetas HTML
        $texto = strip_tags($html);
        
        // Decodificar entidades HTML
        $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Limpiar espacios múltiples
        $texto = preg_replace('/[ \t]+/', ' ', $texto);
        
        // Limpiar saltos de línea múltiples (máximo 2)
        $texto = preg_replace('/\n{3,}/', "\n\n", $texto);
        
        return trim($texto);
    }

    private function decodificarAsunto($subject) 
    {
        if (empty($subject)) {
            return '(Sin asunto)';
        }
        
        // Paso 1: Decodificar MIME si existe
        if (strpos($subject, '=?') !== false) {
            // Usar mb_decode_mimeheader que preserva mejor los acentos
            $decoded = @mb_decode_mimeheader($subject);
            
            if (empty($decoded)) {
                // Fallback: iconv sin //TRANSLIT para no perder acentos
                $decoded = @iconv_mime_decode($subject, 0, 'UTF-8');
            }
            
            if (empty($decoded)) {
                $decoded = $subject;
            }
        } else {
            $decoded = $subject;
        }
        
        // Paso 2: Reemplazar guiones bajos por espacios
        $decoded = str_replace('_', ' ', $decoded);
        
        // Paso 3: Verificar y corregir encoding UTF-8
        if (!mb_check_encoding($decoded, 'UTF-8')) {
            // Detectar el encoding real
            $currentEncoding = mb_detect_encoding(
                $decoded, 
                ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ISO-8859-15'], 
                true
            );
            
            if ($currentEncoding) {
                $decoded = mb_convert_encoding($decoded, 'UTF-8', $currentEncoding);
            }
        }
        
        // Paso 4: Limpiar espacios múltiples (sin tocar caracteres especiales)
        $decoded = preg_replace('/\s+/', ' ', trim($decoded));
        
        return $decoded ?: '(Sin asunto)';
    }

    private function getEmailsOAuth2($maxResults) {
        $url = "https://graph.microsoft.com/v1.0/me/messages?\$top=$maxResults";
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        
        $response = $this->makeHttpRequest($url, $headers);
        
        $emails = [];
        if (isset($response['value'])) {
            foreach ($response['value'] as $message) {
                $emails[] = $this->parseOutlookMessage($message);
            }
        }
        
        return $emails;
    }
    
    private function getEmailsImap($maxResults) {
        $imap = imap_open('{outlook.office365.com:993/imap/ssl}INBOX', $this->username, $this->password);
        
        if (!$imap) {
            throw new Exception("Error conectando a IMAP: " . imap_last_error());
        }
        
        $emails = [];
        $totalEmails = imap_num_msg($imap);
        $startMsg = max(1, $totalEmails - $maxResults + 1);
        
        for ($msgNum = $totalEmails; $msgNum >= $startMsg && count($emails) < $maxResults; $msgNum--) {
            $header = imap_headerinfo($imap, $msgNum);
            $body = imap_body($imap, $msgNum);
            
            $emails[] = [
                'message_id' => $header->message_id,
                'subject' => isset($header->subject) ? $this->decodeHeader($header->subject) : '',
                'from_email' => isset($header->from[0]) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : '',
                'to_email' => isset($header->to[0]) ? $header->to[0]->mailbox . '@' . $header->to[0]->host : '',
                'date_received' => date('Y-m-d H:i:s', $header->udate),
                'body' => $body,
                'provider' => 'outlook'
            ];
        }
        
        imap_close($imap);
        return $emails;
    }
    
    private function decodeHeader($header) {
        $decoded = imap_mime_header_decode($header);
        $result = '';
        foreach ($decoded as $element) {
            $result .= $element->text;
        }
        return $result;
    }
    
    private function parseOutlookMessage($message) {
        return [
            'message_id' => $message['id'],
            'subject' => $message['subject'] ?? '',
            'from_email' => $message['from']['emailAddress']['address'] ?? '',
            'to_email' => isset($message['toRecipients'][0]) ? $message['toRecipients'][0]['emailAddress']['address'] : '',
            'date_received' => date('Y-m-d H:i:s', strtotime($message['receivedDateTime'])),
            'body' => $message['body']['content'] ?? '',
            'provider' => 'outlook'
        ];
    }

    function debugAdditionalInfo($account) 
    {
        echo "\n=== INFORMACIÓN ADICIONAL ===\n";
        
        // Verificar conectividad de red
        echo "Verificando conectividad a {$account['host']}:{$account['port']}...\n";
        $connection = @fsockopen($account['host'], $account['port'], $errno, $errstr, 10);
        if ($connection) {
            echo "✓ Puerto accesible\n";
            fclose($connection);
        } else {
            echo "❌ Puerto NO accesible - Error: {$errstr} ({$errno})\n";
        }
        
        // Verificar DNS
        echo "Resolviendo DNS para {$account['host']}...\n";
        $ip = gethostbyname($account['host']);
        if ($ip !== $account['host']) {
            echo "DNS resuelto: {$ip}\n";
        } else {
            echo "Error de resolución DNS\n";
        }
        
        // Verificar certificados SSL (si aplica)
        if (in_array($account['encryption'], ['ssl', 'tls'])) {
            echo "Verificando certificado SSL...\n";
            $context = stream_context_create(["ssl" => ["capture_peer_cert" => true]]);
            $client = stream_socket_client("ssl://{$account['host']}:{$account['port']}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            if ($client) {
                $params = stream_context_get_params($client);
                if (isset($params["options"]["ssl"]["peer_certificate"])) {
                    echo "Certificado SSL válido\n";
                    $cert = openssl_x509_parse($params["options"]["ssl"]["peer_certificate"]);
                    echo "  Emisor: " . ($cert['issuer']['CN'] ?? 'Desconocido') . "\n";
                    echo "  Válido hasta: " . date('Y-m-d', $cert['validTo_time_t']) . "\n";
                } else {
                    echo "? Certificado SSL presente pero no verificado\n";
                }
                fclose($client);
            } else {
                echo "Certificado SSL no verificado: {$errstr}\n";
            }
        }
    }

    /**
    * Obtener adjuntos del correo
    */
    private function getAttachmentsMessage($message, &$email) 
    {
        $simad_util = new simad_util();
        $list_attachments = [];
        $process_attachs = array();  
        //*********************************************************************************************
        try {
            $attachments = $message->getAttachments();
            // Si no hay attachments, retornar vacío
            if (empty($attachments) || $attachments->count() == 0) {
                return $list_attachments;
            }
            //*****************************************************************************************
            foreach ($attachments as $attachment)
            {
                try {
                    $attributes = $attachment->getAttributes();
                    $attach_disposition = !empty(trim($attributes['disposition'])) ? $attributes['disposition']->toArray() : null;
                    $type_disposition = "attachment";
                    //**********************************************************************************
                    if(is_array($attach_disposition)){
                        $type_disposition = !empty($attach_disposition[0]) ? $attach_disposition[0] : $type_disposition;
                    }else{
                        $type_disposition = $attributes['disposition'];
                    }
                    //**********************************************************************************
                    if($type_disposition != "inline" && !empty($type_disposition))
                    {
                        $attach_name = (string)$attributes["name"];
                        $attach_name_decode = iconv_mime_decode($attach_name);
                        //******************************************************************************
                        if( $attach_name_decode === false){
                            $attach_name = mb_convert_encoding((string)$attributes["name"],'UTF-8');
                        }else{
                            $attach_name = mb_convert_encoding((string)$attach_name_decode,'UTF-8');
                        }
                        //******************************************************************************
                        $file_vars = pathinfo($attach_name);
                        $fileName = $simad_util->clean_name_file($file_vars);
                        //******************************************************************************
                        if(in_array($fileName,$process_attachs)){
                            continue;
                        }
                        //******************************************************************************
                        $attah_prefix = uniqid();
                        $fullpath = $this->downloads_dir.DIRECTORY_SEPARATOR;
                        simad_util::createPath($this->downloads_dir);
                        //******************************************************************************
                        $status = $attachment->save($fullpath, $attah_prefix.'_'.$fileName);
                        //******************************************************************************
                        $list_attachments[] = 
                        [
                            'filename_attach' =>  $attach_name,
                            'filename' => $attah_prefix.'_'.$fileName,
                            'size' => $attachment->getAttributes()["size"],
                            'mime_type' => $attachment->getMimeType(),
                            'part_number' => $attachment->getAttributes()["id"],
                            'extension' => $file_vars['extension'],
                            'type' => $attachment->getAttributes()["type"],
                            'fulltpath' => $this->downloads_dir,
                            'isSave' => $status,
                        ];
                        //******************************************************************************
                        $process_attachs[] = $fileName;
                        //******************************************************************************
                        unset($attachment, $attributes, $attach_disposition);
                    }else{
                        // Solo se continua el proceso si hay body HTML
                        if (empty($email['body'])) {
                            unset($attachment, $attributes, $attach_disposition);
                            continue;
                        }
                        //******************************************************************************
                        $temp_bdir = $this->folder_tmp.DIRECTORY_SEPARATOR.md5($message->message_id);
                        simad_util::createPath($temp_bdir);
                        $attah_prefix = uniqid();
                        //******************************************************************************
                        $cid = $attachment->getId();
                        $cid = trim($cid, '<>');
                        // Buscar diferentes formatos de CID en el HTML
                        $cid_patterns = [
                            'cid:' . $cid,
                            'cid:<' . $cid . '>',
                            'cid:&lt;' . $cid . '&gt;',
                        ];

                        // Verificar si algún patrón existe en el body
                        $cid_exists = false;
                        foreach ($cid_patterns as $pattern) {
                            if (stripos($email['body'], $pattern) !== false) {
                                $cid_exists = true;
                                break;
                            }
                        }
                        
                        if (!$cid_exists) {
                            unset($attachment);
                            continue;
                        }
                        //******************************************************************************
                        $temp_bdir = $temp_bdir.DIRECTORY_SEPARATOR;
                        simad_util::createPath($temp_bdir);
                        $tmp_nfile = $attah_prefix.'_'.$attachment->getName();
                        //******************************************************************************
                        $status = $attachment->save($temp_bdir,$tmp_nfile);
                        //******************************************************************************
                        if($status){
                            $image = $temp_bdir.$tmp_nfile;
                            //**************************************************************************
                            // Verificar que el archivo exista en el repositorio temporal
                            if (!file_exists($image)) {
                                error_log("Advertencia: Imagen inline no guardada correctamente, MessageId: {$email['message_id']}, AsuntoEmail: {$email['subject']},");
                                unset($attachment, $attributes, $attach_disposition);
                                continue;
                            }
                            //**************************************************************************
                            $cid_image = 'cid:'.$attachment->getId();
                            $file_size = filesize($image);
                            //**************************************************************************
                            // Si la imagen es muy grande (>2MB), no embedirla
                            if ($file_size > 2097152) {
                                error_log("Imagen inline muy grande MessageId: {$email['message_id']}, AsuntoEmail: {$email['subject']}, omitiendo: {$tmp_nfile} ({$file_size} bytes)");
                                @unlink($image);
                                unset($attachment, $attributes, $attach_disposition);
                                continue;
                            }
                            //**************************************************************************
                            //if (strpos($email['body'], $cid_image) !== false) {
                            $image_data = base64_encode(file_get_contents($image));
                            $mime = mime_content_type($image);
                            //**************************************************************************
                            // Reemplazar en el body
                            $data_str = 'data:' . $mime . ';base64,' . $image_data;
                            //$new_message_body = str_replace($cid_image, 'data:' . $mime . ';base64,' . $image_data, $email['body']);
                            $new_body = $email['body'];
                            //**************************************************************************
                            foreach ($cid_patterns as $pattern) {
                                $new_body = str_ireplace($pattern, $data_str, $new_body);
                            }
                            //**************************************************************************
                            // Verificar si se hizo algún reemplazo
                            if ($new_body !== $email['body']) {
                                $email['body'] = $new_body;
                            }
                            //**************************************************************************
                            // CRÍTICO: Liberar memoria de la imagen base64
                            unset($image_data, $new_body);
                            //}
                            //**************************************************************************
                            if (file_exists($image)) { @unlink($image); }
                        }
                        //******************************************************************************
                        unset($attachment, $attributes);
                    }
                    //**********************************************************************************
                    // Forzar limpieza de memoria cada 5 attachments
                    static $attachment_count = 0;
                    $attachment_count++;
                    if ($attachment_count % 5 == 0) {
                        gc_collect_cycles();
                    }
                } catch (\Exception $e) {
                    echo "Error procesando attachment: " . $e->getMessage() . "\n";
                    unset($attachment);
                    continue;
                }
            }
            //*******************************************************************************************
            unset($attachments,$process_attachs);
            gc_collect_cycles();
        } catch (\Exception $e) {
            echo "Error en getAttachments: " . $e->getMessage() . "\n";
        }
        //***********************************************************************************************
        return $list_attachments;
    }

    /**
    * Descargar el corre en formato Eml, para almacenarlo en el servidor
    */
    private function saveMessageAsEml($message, &$email): bool
    {
        try {
            if (!is_dir($this->downloads_dir)) {
                mkdir($this->downloads_dir, 0775, true);
            }
            //********************************************************************************************
            $uid = $message->getUid();
            //********************************************************************************************
            $attributeDate = $message->getDate();
            $carbonDate = $attributeDate->toDate();
            $dateColombia = $carbonDate->setTimezone('America/Bogota'); 
            $date = $dateColombia->format('Ymd_His');
            //********************************************************************************************
            $subjectRaw = $message->getSubject();
            if (empty($subjectRaw)) {
                $subjectRaw = 'sin_asunto';
            }
            //********************************************************************************************
            $subject = $this->sanitizarNombreArchivo($subjectRaw, [
                'max_length' => 70,
                'agregar_timestamp' => false
            ]);
            //********************************************************************************************
            // Nombre de archivo
            $filename = sprintf('%s_%s_%s.eml', $uid, $date, $subject);
            $filepath = $this->downloads_dir . DIRECTORY_SEPARATOR . $filename;
            //********************************************************************************************
            // Verificar longitud de ruta (límite Windows: 260)
            if (strlen($filepath) > 240) {
                $subject = mb_substr($subject, 0, 30);
                $filename = sprintf('%s_%s_%s.eml', $uid, $date, $subject);
                $filepath = $this->downloads_dir . DIRECTORY_SEPARATOR . $filename;
            }
            //********************************************************************************************
            // Construir EML
            $rawHeader = $message->getHeader()->raw;
            $rawBody = $message->getRawBody();
            $eml = rtrim($rawHeader, "\r\n") . "\r\n\r\n" . $rawBody;
            //********************************************************************************************
            // Guardar
            $bytes = file_put_contents($filepath, $eml);
            //********************************************************************************************
            if ($bytes === false || $bytes === 0) {
                return false;
            }
            //********************************************************************************************
            // Agregar a attachments
            $email['attachments'][] = [
                'filename_attach' => $filename,
                'filename' => $filename,
                'size' => $bytes,
                'mime_type' => 'message/rfc822',
                'extension' => 'eml',
                'type' => 'eml',
                'fulltpath' => $this->downloads_dir,
                'isSave' => true,
            ];
            //********************************************************************************************
            // Liberar memoria
            unset($eml, $rawHeader, $rawBody);
            //********************************************************************************************  
            return true;
        } catch (\Exception $e) {
            echo "Error guardando EML: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function sanitizarNombreArchivo($nombre, $opciones = [])
    {
        $defaults = [
            'max_length' => 80,
            'agregar_timestamp' => false,
        ];
        
        $opciones = array_merge($defaults, $opciones);
        
        // Decodificar MIME
        $decoded = @mb_decode_mimeheader($nombre);
        $nombre = $decoded ?: $nombre;
        
        // Reemplazar _ por espacios primero
        $nombre = str_replace('_', ' ', $nombre);
        
        // Transliterar acentos manualmente (más confiable que iconv)
        $transliteraciones = [
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
            'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
            'ñ'=>'n', 'Ñ'=>'N',
            'ü'=>'u', 'Ü'=>'U',
            'ç'=>'c', 'Ç'=>'C',
            'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
            'â'=>'a', 'ê'=>'e', 'î'=>'i', 'ô'=>'o', 'û'=>'u',
            'ã'=>'a', 'õ'=>'o',
        ];
        
        $nombre = strtr($nombre, $transliteraciones);
        
        // Eliminar caracteres prohibidos en Windows
        $nombre = preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $nombre);
        
        // Eliminar cualquier caracter que no sea letra, número, espacio, guion o punto
        $nombre = preg_replace('/[^a-zA-Z0-9\s\-\.]/', '', $nombre);
        
        // Convertir espacios en guiones bajos (mejor para nombres de archivo)
        $nombre = preg_replace('/\s+/', '_', $nombre);
        
        // Limpiar underscores múltiples
        $nombre = preg_replace('/_+/', '_', $nombre);
        
        // Trim
        $nombre = trim($nombre, '_-.');
        
        // Default si está vacío
        if (empty($nombre)) {
            $nombre = 'email';
        }
        
        // Truncar
        if (strlen($nombre) > $opciones['max_length']) {
            $nombre = substr($nombre, 0, $opciones['max_length']);
            $nombre = rtrim($nombre, '_-.');
        }
        
        if ($opciones['agregar_timestamp']) {
            $nombre .= '_' . date('Ymd_His');
        }
        
        return $nombre;
    }

    /**
    * Descargar el corre en formato Eml, para almacenarlo en el servidor
    */
    private function saveMessageAsEmlOld($message, &$email): string
    {
        if (!is_dir($this->downloads_dir)) { mkdir($this->downloads_dir, 0775, true); }
        $fullpath = $this->downloads_dir.DIRECTORY_SEPARATOR;
        $currente_attachs = $email['attachments'];
        //**************************************************************************************
        // 1) Cabeceras crudas + 2) separador en blanco + 3) cuerpo crudo
        $rawHeader = $message->getHeader()->raw;   // string de cabeceras sin parsear
        $rawBody   = $message->getRawBody();       // cuerpo MIME completo
        //**************************************************************************************
        // Asegura separación header/body con CRLF
        $eml = rtrim($rawHeader, "\r\n") . "\r\n\r\n" . $rawBody;
        //**************************************************************************************
        // Nombre de archivo seguro (UID-fecha-asunto.eml)
        $uid     = $message->getUid();
        $date    = date('Ymd_His',strtotime($message->getDate()));
        $subject = preg_replace('/[^\w\-.]+/u', '_', (string)$message->getSubject());
        $fname   = sprintf('%s%s_%s_%s.eml', $fullpath, $uid, $date, $subject);
        $fname   = substr($fname, 0, 240); // evita rutas demasiado largas
        //**************************************************************************************
        $bytes_written = file_put_contents($fname, $eml);
        //**************************************************************************************
        if ($bytes_written !== false) {
            $mail_raw = 
            [
                'filename_attach' =>  basename($fname),
                'filename' => basename($fname),
                'size' => $bytes_written,
                'mime_type' => 'message/rfc822',
                'part_number' => null,
                'extension' => 'eml',
                'type' => 'eml',
                'fulltpath' => $this->downloads_dir,
                'isSave' => true,
            ];
            //**********************************************************************************
            $currente_attachs[] = $mail_raw;
            $email['attachments'] = $currente_attachs;
            return true;
        }else{
            return false;
        }
    }
}