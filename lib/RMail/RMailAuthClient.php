<?php

/**
 * RMailAuthClient
 *
 * Gestiona la autenticación con la API de RMail mediante token Bearer.
 * Almacena el token en caché (archivo temporal) para evitar reautenticación
 * en cada petición. Renueva automáticamente cuando está próximo a expirar.
 *
 * Stack: PHP 7.4 / Symfony 1.4 / Windows IIS
 */
class RMailAuthClient
{
    /** @var string URL base de la API (sin slash final) */
    private string $baseUrl;

    /** @var string Correo del usuario RMail */
    private string $username;

    /** @var string Contraseña del usuario RMail */
    private string $password;

    /** @var string Client ID único asignado a esta integración por RPost (opcional) */
    private string $clientId;

    /**
     * Margen de seguridad en segundos antes del vencimiento real del token
     * para forzar renovación anticipada. Valor recomendado: 300 (5 minutos).
     */
    private int $expiryMargin;

    /** @var string Ruta del archivo de caché del token */
    private string $cacheFile;

    /**
     * @param string $baseUrl      Ej: https://app.rmail.com/rmail
     * @param string $username     Correo de la cuenta RMail
     * @param string $password     Contraseña de la cuenta RMail
     * @param string $clientId     Client_Id asignado por RPost a esta integración (vacío si no aplica)
     * @param int    $expiryMargin Segundos de margen antes de renovar (default 300)
     * @param string $cacheFile    Ruta del archivo de caché (default sys_get_temp_dir)
     */
    public function __construct(
        string $baseUrl,
        string $username,
        string $password,
        string $clientId     = '',
        int    $expiryMargin = 300,
        string $cacheFile    = ''
    ) {
        $this->baseUrl      = rtrim($baseUrl, '/');
        $this->username     = $username;
        $this->password     = $password;
        $this->clientId     = $clientId;
        $this->expiryMargin = $expiryMargin;
        $this->cacheFile    = $cacheFile ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rmail_token.json';
    }

    // -------------------------------------------------------------------------
    // API pública
    // -------------------------------------------------------------------------

    /**
     * Retorna un token de acceso válido.
     * Usa el token en caché si aún es válido; solicita uno nuevo en caso contrario.
     *
     * @return string access_token listo para usar en cabeceras Authorization
     * @throws RMailAuthException Si la autenticación falla
     */
    public function getAccessToken(): string
    {
        $cached = $this->loadFromCache();

        if ($cached !== null && $this->isTokenValid($cached)) {
            return $cached['access_token'];
        }

        return $this->requestNewToken();
    }

    /**
     * Fuerza la obtención de un token nuevo, descartando el caché.
     * Útil cuando una llamada a la API retorna HTTP 401.
     *
     * @return string access_token nuevo
     * @throws RMailAuthException
     */
    public function forceRefresh(): string
    {
        $this->clearCache();
        return $this->requestNewToken();
    }

    // -------------------------------------------------------------------------
    // Lógica interna
    // -------------------------------------------------------------------------

    /**
     * Solicita un token nuevo al endpoint /token de RMail.
     *
     * Endpoint : POST {base_url}/token
     * Content-Type: application/x-www-form-urlencoded
     * Body      : grant_type=password&username=...&password=...&Client_Id=...
     */
    private function requestNewToken(): string
    {
        $url    = $this->baseUrl . '/token';
        $params = [
            'grant_type' => 'password',
            'username'   => $this->username,
            'password'   => $this->password,
        ];
        if (!empty($this->clientId)) {
            $params['Client_Id'] = $this->clientId;
        }
        $body = http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            // En producción con certificado válido, dejar SSL habilitado
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError) {
            throw new RMailAuthException(
                "Error cURL al obtener token RMail: {$curlError}"
            );
        }

        if ($httpCode !== 200) {
            throw new RMailAuthException(
                "Error HTTP {$httpCode} al obtener token RMail. Respuesta: {$response}"
            );
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['access_token'])) {
            throw new RMailAuthException(
                "Respuesta inesperada del endpoint /token: {$response}"
            );
        }

        // Calcular timestamp de expiración absoluta
        // expires_in viene en segundos desde el momento de emisión
        $data['expires_at'] = time() + (int)($data['expires_in'] ?? 3600);

        $this->saveToCache($data);

        return $data['access_token'];
    }

    /**
     * Verifica si el token en caché sigue siendo válido considerando el margen.
     */
    private function isTokenValid(array $tokenData): bool
    {
        if (empty($tokenData['expires_at'])) {
            return false;
        }
        return (time() + $this->expiryMargin) < $tokenData['expires_at'];
    }

    // -------------------------------------------------------------------------
    // Caché en archivo (compatible con Windows IIS / PHP 7.4 sin APCu)
    // -------------------------------------------------------------------------

    private function loadFromCache(): ?array
    {
        if (!file_exists($this->cacheFile)) {
            return null;
        }
        $raw = @file_get_contents($this->cacheFile);
        if ($raw === false) {
            return null;
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    private function saveToCache(array $data): void
    {
        file_put_contents(
            $this->cacheFile,
            json_encode($data),
            LOCK_EX
        );
    }

    private function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            @unlink($this->cacheFile);
        }
    }
}

// =============================================================================
// Excepción específica del cliente de autenticación
// =============================================================================

class RMailAuthException extends RuntimeException {}
