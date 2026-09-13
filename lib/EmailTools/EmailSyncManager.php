<?php
class EmailSyncManager 
{
    private $maxWorkers = 5;
    private $batchSize = 100;
    private $parallelProcessor;
    private $messageProcessor;
    
    public function __construct() 
    {
        //$this->parallelProcessor = new ParallelProcessor();
        //$this->messageProcessor = new MessageProcessor();
    }

    public function syncAllAccounts() 
    {
        $ilist_accounts = $this->getActiveAccounts();
        
        foreach ($ilist_accounts as $account) {
            $lastUid = $this->getLastProcessedUid($account->getAccountLogin());
            EmailPeer::getDataEmailAcountWebklex($account->getPrimaryKey(),$lastUid);
        }
    }
    
    private function getActiveAccounts() 
    {
        try {
            return EmailAccountPeer::getAllEmailAccountSync();
        } catch (Exception $e) {
            
        }
    }

    public function getLastProcessedUid($accountEmail) 
    {
        return EmailAccountSyncPeer::getLastProcessedUid($accountEmail);
    }
    
    public function isMessageProcessed($messageId, $accountEmail) 
    {
        return EmailTrackingPeer::isMessageProcessed($messageId, $accountEmail);
    }    
    
    private function processMessagesBatch($messages, $account) 
    {
        $messageItems = [];
        
        foreach ($messages as $message) {
            $messageId = $message->getMessageId() ?: 'no-message-id-' . $message->getUid();
            $uid = $message->getUid();
            
            // Verificar si ya fue procesado
            if (!$this->isMessageProcessed($messageId, $account['email'])) {
                $messageItems[] = [
                    'message' => $message,
                    'message_id' => $messageId,
                    'uid' => $uid
                ];
            }
        }
        
        Logger::info("Procesando " . count($messageItems) . " mensajes nuevos");
        
        // Procesar en lotes paralelos
        if (!empty($messageItems)) {
            $this->parallelProcessor->processInParallel($messageItems, $account);
        }
    }

    public function markMessageAsProcessed($messageId, $accountEmail, $uid) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_tracking (message_id, account_email, uid, processed_at, status) 
            VALUES (?, ?, ?, NOW(), 'downloaded') 
            ON DUPLICATE KEY UPDATE status = 'downloaded'
        ");
        return $stmt->execute([$messageId, $accountEmail, $uid]);
    }

    public function updateLastUid($accountEmail, $uid) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_accounts_sync (account_email, last_uid, last_sync) 
            VALUES (?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE 
            last_uid = VALUES(last_uid), 
            last_sync = VALUES(last_sync)
        ");
        $stmt->execute([$accountEmail, $uid]);
    }
}

class ParallelProcessor 
{
    private $maxConcurrency = 5;
    private $messageProcessor;
    
    public function __construct() 
    {
        $this->messageProcessor = new MessageProcessor();
    }
    
    public function processInParallel($messages, $account) 
    {
        if (function_exists('pcntl_fork')) {
            $this->processWithForking($messages, $account);
        } else {
            $this->processSequential($messages, $account);
        }
    }
    
    private function processWithForking($messages, $account) 
    {
        $chunks = array_chunk($messages, ceil(count($messages) / $this->maxConcurrency));
        
        $processes = [];
        foreach ($chunks as $chunk) {
            $pid = pcntl_fork();
            
            if ($pid == -1) {
                // Error al crear proceso, procesar secuencialmente
                Logger::warning("No se pudo crear proceso hijo, procesando secuencialmente");
                $this->processChunk($chunk, $account);
                continue;
            } elseif ($pid == 0) {
                // Proceso hijo
                try {
                    $this->processChunk($chunk, $account);
                } catch (Exception $e) {
                    Logger::error("Error en proceso hijo: " . $e->getMessage());
                }
                exit(0);
            } else {
                // Proceso padre
                $processes[] = $pid;
            }
        }
        
        // Esperar a que terminen todos los procesos hijos
        foreach ($processes as $pid) {
            pcntl_waitpid($pid, $status);
        }
    }
    
    private function processSequential($messages, $account) 
    {
        Logger::info("Procesando secuencialmente " . count($messages) . " mensajes");
        $this->processChunk($messages, $account);
    }
    
    public function processChunk($chunk, $account) 
    {
        $syncManager = new EmailSyncManager(); // Para acceso a métodos de tracking
        
        foreach ($chunk as $item) {
            try {
                $this->messageProcessor->processSingleMessage($item['message'], $account);
                
                // Registrar mensaje como procesado
                $syncManager->markMessageAsProcessed(
                    $item['message_id'], 
                    $account['email'], 
                    $item['uid']
                );
                
                Logger::info("Mensaje procesado: {$item['message_id']} (UID: {$item['uid']})");
                
            } catch (Exception $e) {
                Logger::error("Error procesando mensaje {$item['message_id']}: " . $e->getMessage());
                // Continuar con el siguiente mensaje
            }
        }
    }
}

class MessageProcessor 
{
    private $db;
    private $attachmentPath;
    
    public function __construct() 
    {
        $this->attachmentPath = __DIR__ . '/../attachments/';
        
        // Crear directorio de adjuntos si no existe
        if (!is_dir($this->attachmentPath)) {
            mkdir($this->attachmentPath, 0755, true);
        }
    }
    
    public function processSingleMessage($message, $account) 
    {
        try {
            // Extraer información del correo
            $emailData = [
                'subject' => $message->getSubject() ?: '',
                'from' => $this->formatAddresses($message->getFrom()),
                'to' => $this->formatAddresses($message->getTo()),
                'cc' => $this->formatAddresses($message->getCc()),
                'bcc' => $this->formatAddresses($message->getBcc()),
                'date' => $message->getDate() ? $message->getDate()->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                'body' => $this->extractBody($message),
                'attachments' => []
            ];
            
            // Procesar adjuntos
            $attachments = $message->getAttachments();
            foreach ($attachments as $attachment) {
                try {
                    $attachmentData = $this->processAttachment($attachment);
                    $emailData['attachments'][] = $attachmentData;
                } catch (Exception $e) {
                    Logger::error("Error procesando adjunto: " . $e->getMessage());
                }
            }
            
            // Guardar en base de datos
            $this->saveEmailData($emailData, $account, $message);
            
        } catch (Exception $e) {
            Logger::error("Error procesando mensaje: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function formatAddresses($addresses) 
    {
        if (!$addresses) return '';
        
        $formatted = [];
        foreach ($addresses as $address) {
            $formatted[] = $address->mail . ($address->personal ? " ({$address->personal})" : '');
        }
        return implode(', ', $formatted);
    }
    
    private function extractBody($message) 
    {
        try {
            // Intentar obtener cuerpo HTML primero
            $htmlBody = $message->getHTMLBody();
            if ($htmlBody) {
                return $htmlBody;
            }
            
            // Si no hay HTML, obtener cuerpo de texto plano
            $textBody = $message->getTextBody();
            if ($textBody) {
                return $textBody;
            }
            
            return '';
        } catch (Exception $e) {
            Logger::warning("Error extrayendo cuerpo del mensaje: " . $e->getMessage());
            return '';
        }
    }
    
    private function processAttachment($attachment) 
    {
        try {
            // Generar nombre único para evitar conflictos
            $filename = uniqid() . '_' . $attachment->getName();
            $filepath = $this->attachmentPath . $filename;
            
            // Guardar archivo
            $attachment->saveAs($filepath);
            
            return [
                'original_name' => $attachment->getName() ?: 'attachment',
                'saved_name' => $filename,
                'path' => $filepath,
                'size' => $attachment->getSize() ?: filesize($filepath),
                'mime_type' => $attachment->getMimeType() ?: 'application/octet-stream'
            ];
        } catch (Exception $e) {
            Logger::error("Error guardando adjunto: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function saveEmailData($emailData, $account, $message) 
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO emails (
                    account_email, subject, sender, recipients, cc, bcc, 
                    sent_date, body, message_id, uid, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $account['email'],
                $emailData['subject'],
                $emailData['from'],
                $emailData['to'],
                $emailData['cc'],
                $emailData['bcc'],
                $emailData['date'],
                $emailData['body'],
                $message->getMessageId() ?: 'no-message-id-' . $message->getUid(),
                $message->getUid()
            ]);
            
            $emailId = $this->db->lastInsertId();
            
            // Guardar adjuntos
            foreach ($emailData['attachments'] as $attachment) {
                $this->saveAttachment($emailId, $attachment);
            }
            
        } catch (Exception $e) {
            Logger::error("Error guardando datos de email: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function saveAttachment($emailId, $attachment) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_attachments (
                email_id, original_name, saved_name, file_path, 
                file_size, mime_type, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $emailId,
            $attachment['original_name'],
            $attachment['saved_name'],
            $attachment['path'],
            $attachment['size'],
            $attachment['mime_type']
        ]);
    }
}

class ProcessedMessagesCache 
{
    private static $cache = [];
    private static $maxCacheSize = 10000;
    
    public static function isProcessed($messageId, $accountEmail) 
    {
        $key = $accountEmail . ':' . $messageId;
        
        // Verificar en cache primero
        if (isset(self::$cache[$key])) {
            return true;
        }
        
        // Verificar en base de datos
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT 1 FROM email_tracking WHERE message_id = ? AND account_email = ? LIMIT 1");
        $stmt->execute([$messageId, $accountEmail]);
        
        $result = $stmt->fetch();
        if ($result) {
            self::$cache[$key] = true;
            self::maintainCacheSize();
            return true;
        }
        
        return false;
    }
    
    private static function maintainCacheSize() 
    {
        if (count(self::$cache) > self::$maxCacheSize) {
            // Eliminar elementos antiguos
            self::$cache = array_slice(self::$cache, -5000, null, true);
        }
    }
}

class EmailSyncMonitor 
{
    public static function logSyncStatus($account, $processed, $errors, $duration) 
    {
        $logData = [
            'account' => $account,
            'processed_emails' => $processed,
            'errors' => $errors,
            'duration_seconds' => $duration,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents(
            '/var/log/email_sync_status.log', 
            json_encode($logData) . "\n", 
            FILE_APPEND
        );
    }
    
    public static function checkSystemHealth() 
    {
        // Verificar espacio en disco
        $freeSpace = disk_free_space('/');
        if ($freeSpace < 1073741824) { // Menos de 1GB
            throw new Exception("Espacio en disco insuficiente");
        }
        
        // Verificar memoria
        if (memory_get_usage() > 536870912) { // Más de 512MB
            throw new Exception("Uso de memoria excesivo");
        }
    }
}