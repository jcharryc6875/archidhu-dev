<?php
abstract class EmailCoreProvider {
    protected $accessToken;
    protected $refreshToken;
    protected $username;
    protected $password;
    protected $authType; // 'oauth2' o 'password'
    protected $downloads_dir;
    protected $path_absolute;
    protected $path_relative;
    protected $path_attachments;
    protected $folder_tmp;
    
    public function __construct($authType = 'oauth2') {
        $this->authType = $authType;
        //****************************************************************************************
        $dir_raiz = ParametroPeer::retrieveByPK(9)->getValortexto();
        $dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
        $folder_date = date("Ymd");
        //****************************************************************************************
        $this->path_attachments = "emails_attachment";
        $this->path_absolute = $dir_raiz;
        $this->path_relative = $this->path_attachments.DIRECTORY_SEPARATOR.$folder_date;
        $this->downloads_dir = $this->path_absolute.$this->path_relative;
        $this->folder_tmp = $this->path_absolute.$dir_tmp.DIRECTORY_SEPARATOR.'attachment';
        //****************************************************************************************
        simad_util::createPath($this->downloads_dir);
        simad_util::createPath($this->folder_tmp);
    }
        
    abstract public function authenticate($credentials);
    abstract public function getEmails($maxResults = 10, $lastUid = null);
    abstract public function testConnection();
    
    protected function makeHttpRequest($url, $headers = [], $data = null, $method = 'GET') {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Error cURL: $error");
        }
        
        if ($httpCode >= 400) {
            throw new Exception("Error HTTP: $httpCode - $response");
        }
        
        return json_decode($response, true);
    }

    public function getMaxUid($messages) : int
    {
        $maxUid = 0;
        foreach ($messages as $message) {
            $uid = $message["message_uid"];
            if ($uid > $maxUid) {
                $maxUid = $uid;
            }
        }
        return $maxUid;
    }
}