<?php
// Clase base para autenticación OAuth2
abstract class OAuth2Provider {
    protected $clientId;
    protected $tenantId;
    protected $clientSecret;
    protected $redirectUri;
    protected $accessToken;
    protected $refreshToken;
    
    public function __construct($tenantId, $clientId, $clientSecret, $redirectUri) {
        $this->$tenantId = $$tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }
    
    abstract public function getAuthUrl();
    abstract public function getAccessToken($code);
    abstract public function refreshAccessToken($refreshToken);
    abstract public function getEmails($maxResults = 10);
    
    protected function makeHttpRequest($url, $headers = [], $data = null, $method = 'GET') {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Error HTTP: $httpCode - $response");
        }
        
        return json_decode($response, true);
    }
}