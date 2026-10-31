<?php

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/OAuthTokenProvider.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\OAuthTokenProvider;

/**
 * Proveedor de token XOAUTH2 para PHPMailer con Microsoft Entra ID (flujo client credentials).
 * El token se cachea hasta poco antes de expirar para no pedir uno por cada correo.
 */
class OAuthTokenProviderSimad implements OAuthTokenProvider
{
    const TOKEN_ENDPOINT = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token';
    const DEFAULT_SCOPE = 'https://outlook.office365.com/.default';
    // Margen antes de la expiracion real para renovar el token.
    const EXPIRY_MARGIN = 300;

    private $userName;
    private $tenantId;
    private $clientId;
    private $clientSecret;
    private $scope;
    private $caBundle;
    private $endpoint;

    /**
     * @param string|null $endpoint URL del token con %s para el tenant; null usa login.microsoftonline.com
     *        (permite nubes soberanas como login.microsoftonline.us)
     */
    public function __construct($userName, $tenantId, $clientId, $clientSecret, $scope = null, $caBundle = null, $endpoint = null)
    {
        $this->endpoint = !empty($endpoint) ? $endpoint : self::TOKEN_ENDPOINT;
        $this->userName = (string) $userName;
        $this->tenantId = trim((string) $tenantId);
        $this->clientId = trim((string) $clientId);
        $this->clientSecret = (string) $clientSecret;
        $this->scope = trim((string) $scope) !== '' ? trim((string) $scope) : self::DEFAULT_SCOPE;
        $this->caBundle = $caBundle;
    }

    /**
     * Cadena XOAUTH2 en base64 que PHPMailer envia en el comando AUTH.
     */
    public function getOauth64()
    {
        $token = $this->getAccessToken();

        return base64_encode('user=' . $this->userName . "\001auth=Bearer " . $token . "\001\001");
    }

    /**
     * @param bool $forceRefresh ignora el token cacheado (usado por la prueba del servicio)
     * @throws MailException si Entra ID rechaza la solicitud o no hay conexion
     */
    public function getAccessToken($forceRefresh = false)
    {
        $cacheKey = 'token_' . md5($this->tenantId . '|' . $this->clientId . '|' . $this->scope . '|' . $this->clientSecret);
        if (!$forceRefresh) {
            $cached = NotificacionMailCache::get($cacheKey);
            if (is_array($cached) && !empty($cached['t'])) {
                $token = SED::decryption($cached['t']);
                if (!empty($token)) {
                    return $token;
                }
            }
        }

        $data = $this->requestToken();
        $ttl = max(60, (int) $data['expires_in'] - self::EXPIRY_MARGIN);
        NotificacionMailCache::set($cacheKey, array('t' => SED::encryption($data['access_token'])), $ttl);

        return $data['access_token'];
    }

    /**
     * @return array access_token y expires_in de la respuesta de Entra ID
     */
    private function requestToken()
    {
        if ($this->tenantId === '' || $this->clientId === '' || $this->clientSecret === '') {
            throw new MailException('Cuenta OAuth2 incompleta: se requiere Tenant ID, Client ID y Client Secret');
        }
        if (!function_exists('curl_init')) {
            throw new MailException('La extension cURL de PHP no esta habilitada');
        }

        $ch = curl_init(sprintf($this->endpoint, rawurlencode($this->tenantId)));
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
                'grant_type' => 'client_credentials',
            )),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => array('Accept: application/json'),
        );
        if (!empty($this->caBundle)) {
            $options[CURLOPT_CAINFO] = $this->caBundle;
        }
        curl_setopt_array($ch, $options);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            throw new MailException('No se pudo contactar a Microsoft Entra ID (cURL ' . $errno . '): ' . $curlError);
        }

        $json = json_decode($body, true);
        if ($httpCode !== 200 || !is_array($json) || empty($json['access_token'])) {
            $detail = is_array($json) && !empty($json['error_description'])
                ? $json['error_description']
                : (is_array($json) && !empty($json['error']) ? $json['error'] : 'HTTP ' . $httpCode);
            // error_description trae trace id y timestamp en lineas siguientes: solo la primera es util.
            $detail = trim(preg_split('/\r\n|\r|\n/', (string) $detail)[0]);
            throw new MailException('Entra ID rechazo la autenticacion: ' . $detail);
        }
        if (empty($json['expires_in'])) {
            $json['expires_in'] = 3600;
        }

        return $json;
    }
}
