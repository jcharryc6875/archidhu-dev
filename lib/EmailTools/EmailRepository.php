<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', true);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************

class EmailRepository {
    private $db;
    
    public function __construct() {
    }
    
    public function createEmailsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS emails (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id VARCHAR(255) UNIQUE NOT NULL,
            subject TEXT,
            from_email VARCHAR(255),
            to_email VARCHAR(255),
            date_received DATETIME,
            body LONGTEXT,
            provider VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->db->exec($sql);
    }
    
    public function saveEmail($emailData) {
        $sql = "INSERT INTO emails (message_id, subject, from_email, to_email, date_received, body, provider) 
                VALUES (:message_id, :subject, :from_email, :to_email, :date_received, :body, :provider)
                ON DUPLICATE KEY UPDATE
                subject = VALUES(subject),
                from_email = VALUES(from_email),
                to_email = VALUES(to_email),
                date_received = VALUES(date_received),
                body = VALUES(body),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($emailData);
    }
    
    public function getEmails($limit = 50, $offset = 0) {
        $sql = "SELECT * FROM emails ORDER BY date_received DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEmailCount() {
        $sql = "SELECT COUNT(*) FROM emails";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}