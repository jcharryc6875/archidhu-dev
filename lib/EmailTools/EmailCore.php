<?php
require_once('/providers/Gmail.php');
require_once('/providers/Outlook.php');
require_once('OAuth2Providers.php');

// Clase principal para sincronización
/*class EmailSyncManager {
    private $emailRepository;
    private $providers = [];
    
    public function __construct($database) {
        $this->emailRepository = new EmailRepository();
        $this->emailRepository->createEmailsTable();
    }
    
    public function addProvider($name, OAuth2Provider $provider) {
        $this->providers[$name] = $provider;
    }
    
    public function syncEmails($providerName, $maxResults = 10) {
        if (!isset($this->providers[$providerName])) {
            throw new Exception("Proveedor no encontrado: $providerName");
        }
        
        $provider = $this->providers[$providerName];
        
        try {
            $emails = $provider->getEmails($maxResults);
            $savedCount = 0;
            
            foreach ($emails as $email) {
                if ($this->emailRepository->saveEmail($email)) {
                    $savedCount++;
                }
            }
            
            return [
                'success' => true,
                'message' => "Sincronización completada. $savedCount correos procesados.",
                'total_emails' => count($emails),
                'saved_emails' => $savedCount
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error en la sincronización: ' . $e->getMessage()
            ];
        }
    }
    
    public function getStoredEmails($limit = 50, $offset = 0) {
        return $this->emailRepository->getEmails($limit, $offset);
    }
    
    public function getEmailCount() {
        return $this->emailRepository->getEmailCount();
    }
}*/

// Ejemplo de uso
try {
    // Configuración de la base de datos
    $dbConfig = new DatabaseConfig();
    $database = $dbConfig->getConnection();
    
    // Crear el gestor de sincronización
    $syncManager = new EmailSyncManager($database);
    
    // Configurar Gmail
    $gmailProvider = new GmailProvider(
        'tu_gmail_client_id',
        'tu_gmail_client_secret',
        'http://localhost/callback'
    );
    
    // Configurar Outlook
    $outlookProvider = new OutlookProvider(
        'tu_outlook_client_id',
        'tu_outlook_client_secret',
        'http://localhost/callback'
    );
    
    // Agregar proveedores
    $syncManager->addProvider('gmail', $gmailProvider);
    $syncManager->addProvider('outlook', $outlookProvider);
    
    // Para iniciar la autenticación, redirigir al usuario a:
    // echo $gmailProvider->getAuthUrl();
    // echo $outlookProvider->getAuthUrl();
    
    // Después de recibir el código de autorización:
    // $gmailProvider->getAccessToken($code);
    // $outlookProvider->getAccessToken($code);
    
    // Sincronizar correos
    // $result = $syncManager->syncEmails('gmail', 20);
    // $result = $syncManager->syncEmails('outlook', 20);
    
    // Obtener correos almacenados
    // $emails = $syncManager->getStoredEmails(10, 0);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}