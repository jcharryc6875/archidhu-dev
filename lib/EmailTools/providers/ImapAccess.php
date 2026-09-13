<?php
require_once('lib/EmailTools/OAuth2Providers.php');
require_once('lib/EmailTools/providers/EmailCoreProvider.php');
require_once('lib/EmailTools/autoload.php');

use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Carbon\Carbon;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class ImapAccess extends EmailCoreProvider {
    private $server;
    private $port;
    private $encryption;
    private $protocol;
    private $validate_cert;
    private $folder_read;
    private $imapResource;
    
    public function __construct($server, $port = 993, $encryption = 'ssl', $protocol = 'imap',$validate_cert = false, $folder = 'INBOX') {
        parent::__construct('password');
        $this->server = $server;
        $this->port = $port;
        $this->encryption = $encryption;
        $this->protocol = $protocol;
        $this->validate_cert = $validate_cert;
        $this->folder_read = $folder;
    }

    public function getClient($maxResults = 100) {
        if (!$this->imapResource) {
            throw new Exception("No hay conexión IMAP activa");
        }
        //*************************************************************************************************************
        return $this->imapResource;
    }

    public function authenticate($credentials) {
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        
        $options = [
            'host'          => $this->server,
            'port'          => $this->port,
            'encryption'    => $this->encryption,
            'validate_cert' => $this->validate_cert,
            'username'      => $this->username,
            'password'      => $this->password,
            'protocol'      => $this->protocol,
            'debug'         => true
        ];

        try {
            $cm = new Webklex\PHPIMAP\ClientManager();
            $client = $cm->make($options);

            //Connect to the IMAP Server
            $this->imapResource = $client->connect();
            
            if (!$this->imapResource) {
                throw new Exception("Error de autenticación IMAP: " . imap_last_error());
            }
            
            return ['status' => 'authenticated', 'method' => 'imap'];
        } catch (ConnectionFailedException $th) {
            $this->debugAdditionalInfo($options);
        } catch (\Exception $th) {
            $this->debugAdditionalInfo($options);
        } catch (\Throwable $th) {
            $this->debugAdditionalInfo($options);
        }
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

    public function testConnection() {
        try {
            $options = [
                'host'          => $this->server,
                'port'          => $this->port,
                'encryption'    => $this->encryption,
                'validate_cert' => $this->validate_cert,
                'username'      => $this->username,
                'password'      => $this->password,
                'protocol'      => $this->protocol
            ];
    
            $cm = new Webklex\PHPIMAP\ClientManager();
            $client = $cm->make($options);
    
            //Connect to the IMAP Server
            $imap = $client->connect();

            if ($imap) {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getEmails($per_page = 100, $lastUid = null) 
    {
        if (!$this->imapResource) {
            throw new Exception("No hay conexión IMAP activa");
        }
        //*************************************************************************************************************
        $folder = $this->imapResource->getFolder($this->folder_read);
        //*************************************************************************************************************
        if(!$folder){
            throw new Exception("El folder no existe en el buzon de correo");
        }
        //*************************************************************************************************************
        // Zona horaria Colombia
        $tz      = 'America/Bogota';
        $today   = Carbon::now($tz)->startOfDay();
        $from    = $today->copy()->subMonthsNoOverflow(3); // hace 3 meses, 00:00
        $to = (clone $today)->addDay();
        //*************************************************************************************************************
        $query = $folder->query();
        if ($lastUid > 0) {
            //$query->sinceUID($lastUid + 1);
            $query->whereUid(">$lastUid");
        }
        //*************************************************************************************************************
        //$messages = $folder->query()->all()->paginate($per_page, $page = null, $page_name = 'imap_page');
        $messages = $query->since($from)
            ->before($to)
            ->fetchOrderDesc()
            ->all()
            ->limit($per_page, null)->get();
            //->paginate($per_page, $page, 'imap_page');
        //*************************************************************************************************************
        foreach ($messages as $message) 
        {
            try
            {
                $attributes = $message->getHeader()->getAttributes();
                $flags = $message->getFlags();
                //*****************************************************************************************************
                $subject_text = (string)$attributes["subject"];
                $subject_decode = iconv_mime_decode($subject_text);

                if( $subject_decode === false){
                    $subject_text = (string)$attributes["subject"];
                }else{
                    $subject_text = (string)$subject_decode;
                }
                //*****************************************************************************************************
                $from_email = (string)$attributes["from"];
                $from_decode = iconv_mime_decode($from_email);

                if( $from_decode === false){
                    $from_email = (string)$attributes["from"];
                }else{
                    $from_email = (string)$from_decode;
                }
                //*****************************************************************************************************
                $to_email = (string)$attributes["to"];
                $to_decode = iconv_mime_decode($to_email);

                if($to_decode === false){
                    $to_email = (string)$attributes["to"];
                }else{
                    $to_email = (string)$to_decode;
                }
                //*****************************************************************************************************
                $email = [
                    'message_id' => (string)$attributes["message_id"],
                    'subject' => $subject_text,
                    'from_email' => trim($from_email),
                    'from_address' => (string)$attributes["fromaddress"],
                    'to_email' => trim($to_email),
                    'to_address' => (string)$attributes["toaddress"],
                    'cc_email' => $message->getCc(),
                    'date_received' => $message->getDate(),
                    'seen' => $flags['Seen'] != 'Seen' ? false : true,
                    'flagged' => null,
                    'body' => $message->hasHTMLBody() ? $message->getHTMLBody() : $message->getTextBody(),
                    'content_html' => null,
                    'clean_html' => null,
                    'size' => $message->getSize(),
                    'has_attachments' => false,
                    'provider' => 'imap',
                    'message_uid' => $message->uid
                ];
                //*****************************************************************************************************
                $email['attachments'] = $this->getAttachments($message,$email);                
                $message_eml = $this->saveMessageAsEml($message,$email);
                $email['has_attachments'] = count($email['attachments']) > 0;
                //*****************************************************************************************************
                $emails[] = $email;
            }catch (\Webklex\PHPIMAP\Exceptions\RuntimeException $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
            }catch (PropelException $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
            }catch (\Exception $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
            }catch (\IOException $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
            }catch (\Throwable $e) {
                echo 'Exception : ',  $e->getMessage(), "\n";
            }
        }
        //*************************************************************************************************************
        simad_util::deleteDirAndFiles($this->folder_tmp);
        //*************************************************************************************************************
        return $emails;
    }

    /**
    * Obtener adjuntos del correo
    */
    private function getAttachments($message, &$email) 
    {
        $simad_util = new simad_util();
        //**************************************************************************************
        foreach ($message->getAttachments() as $attachment)
        {
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
                $attah_prefix = str_pad(uniqid(),11,"0",STR_PAD_LEFT);
                //******************************************************************************
                $attach_name = (string)$attributes["name"];
                $attach_name_decode = iconv_mime_decode($attach_name);

                if( $attach_name_decode === false){
                    $attach_name = mb_convert_encoding((string)$attributes["name"],'UTF-8');
                }else{
                    $attach_name = mb_convert_encoding((string)$attach_name_decode,'UTF-8');
                }
                //******************************************************************************
                $file_vars = pathinfo($attach_name);
                $fileName = $simad_util->clean_name_file($file_vars);
                //******************************************************************************
                $attah_prefix = uniqid();
                $fullpath = $this->downloads_dir.DIRECTORY_SEPARATOR;
                simad_util::createPath($this->downloads_dir);
                //******************************************************************************
                $status = $attachment->save($fullpath,$attah_prefix.'_'.$fileName);
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
            }else{
                $temp_bdir = $this->folder_tmp.DIRECTORY_SEPARATOR.md5($message->message_id);
                simad_util::createPath($temp_bdir);
                $attah_prefix = uniqid();
                //******************************************************************************
                $temp_bdir = $temp_bdir.DIRECTORY_SEPARATOR;
                $tmp_nfile = $attah_prefix.'_'.$attachment->getName();
                $status = $attachment->save($temp_bdir,$tmp_nfile);
                //******************************************************************************
                if($status){
                    $image = $temp_bdir.$tmp_nfile;
                    $cid_image = 'cid:'.$attachment->getId();
                    // Read image path, convert to base64 encoding
                    $image_data = base64_encode(file_get_contents($image));
                    // Format the image SRC:  data:{mime};base64,{data};
                    $mime =  mime_content_type($image);
                    //replace in body message
                    $new_message_body = str_replace($cid_image, 'data: ' . $mime . ';base64,' . $image_data, $email['body']);
                    unlink($image);
                    //******************************************************************************
                    if(!empty(trim($new_message_body)))
                        $email['body'] = $new_message_body;
                }
            }
        }
        //******************************************************************************************
        return $list_attachments;
    }

    /**
    * Descargar el corre en formato Eml, para almacenarlo en el servidor
    */
    private function saveMessageAsEml($message, &$email): string
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