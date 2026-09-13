<?php
require_once('/../OAuth2Providers.php');
require_once('/../EmailCoreProvider .php');

// Proveedor Gmail
class GmailProvider extends EmailCoreProvider {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $scope = 'https://www.googleapis.com/auth/gmail.readonly';
    private $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $tokenUrl = 'https://oauth2.googleapis.com/token';
    
    public function __construct($authType = 'oauth2', $clientId = null, $clientSecret = null, $redirectUri = null) {
        parent::__construct($authType);
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function authenticate($credentials) {
        if ($this->authType === 'oauth2') {
            return $this->authenticateOAuth2($credentials);
        } else {
            return $this->authenticatePassword($credentials);
        }
    }

    private function authenticateOAuth2($credentials) {
        if (isset($credentials['code'])) {
            // Obtener token con código de autorización
            $data = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $credentials['code'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri
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
        } elseif (isset($credentials['refresh_token'])) {
            // Refrescar token
            return $this->refreshAccessToken($credentials['refresh_token']);
        } elseif (isset($credentials['access_token'])) {
            // Usar token existente
            $this->accessToken = $credentials['access_token'];
            return ['access_token' => $this->accessToken];
        }
        
        throw new Exception("Credenciales OAuth2 inválidas");
    }

    private function authenticatePassword($credentials) {
        // Para Gmail con contraseña de aplicación, aún usamos la API pero con autenticación básica
        // Esto simula la autenticación tradicional pero usa OAuth2 internamente
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        
        // Aquí podrías implementar IMAP si prefieres acceso directo
        // Por ahora, retornamos éxito para la demostración
        return ['status' => 'authenticated', 'method' => 'password'];
    }

    public function getAuthUrl() {
        if ($this->authType !== 'oauth2') {
            throw new Exception("URL de autorización solo disponible para OAuth2");
        }
        
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        
        return $this->authUrl . '?' . http_build_query($params);
    }
    
    public function getAccessToken($code) {
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri
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
    
    public function refreshAccessToken($refreshToken) {
        $data = [
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
                $url = "https://gmail.googleapis.com/gmail/v1/users/me/profile";
                $headers = [
                    'Authorization: Bearer ' . $this->accessToken,
                    'Content-Type: application/json'
                ];
                $this->makeHttpRequest($url, $headers);
            } else {
                // Para autenticación por contraseña, probar conexión IMAP
                return $this->testImapConnection();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function testImapConnection() {
        $imap = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', $this->username, $this->password);
        if ($imap) {
            imap_close($imap);
            return true;
        }
        return false;
    }

    public function getEmails($maxResults = 10, $lastUid = null) {
        if ($this->authType === 'oauth2') {
            return $this->getEmailsOAuth2($maxResults);
        } else {
            return $this->getEmailsImap($maxResults);
        }
    }
    
    private function getEmailsOAuth2($maxResults) {
        $url = "https://gmail.googleapis.com/gmail/v1/users/me/messages?maxResults=$maxResults";
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        
        $response = $this->makeHttpRequest($url, $headers);
        
        $emails = [];
        if (isset($response['messages'])) {
            foreach ($response['messages'] as $message) {
                $emailData = $this->getEmailDetails($message['id']);
                $emails[] = $this->parseGmailMessage($emailData);
            }
        }
        
        return $emails;
    }

    private function getEmailsImap($maxResults) {
        $imap = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', $this->username, $this->password);
        
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
                'provider' => 'gmail'
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
    
    private function getEmailDetails($messageId) {
        $url = "https://gmail.googleapis.com/gmail/v1/users/me/messages/$messageId";
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        
        return $this->makeHttpRequest($url, $headers);
    }
    
    private function parseGmailMessage($message) {
        $headers = $message['payload']['headers'];
        $subject = $this->getHeader($headers, 'Subject');
        $from = $this->getHeader($headers, 'From');
        $to = $this->getHeader($headers, 'To');
        $date = $this->getHeader($headers, 'Date');
        
        $body = $this->getMessageBody($message['payload']);
        
        return [
            'message_id' => $message['id'],
            'subject' => $subject,
            'from_email' => $from,
            'to_email' => $to,
            'date_received' => date('Y-m-d H:i:s', strtotime($date)),
            'body' => $body,
            'provider' => 'gmail'
        ];
    }
    
    private function getHeader($headers, $name) {
        foreach ($headers as $header) {
            if ($header['name'] === $name) {
                return $header['value'];
            }
        }
        return '';
    }
    
    private function getMessageBody($payload) {
        $body = '';
        
        if (isset($payload['body']['data'])) {
            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload['body']['data']));
        } elseif (isset($payload['parts'])) {
            foreach ($payload['parts'] as $part) {
                if ($part['mimeType'] === 'text/plain' || $part['mimeType'] === 'text/html') {
                    if (isset($part['body']['data'])) {
                        $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part['body']['data']));
                        break;
                    }
                }
            }
        }
        
        return $body;
    }
}