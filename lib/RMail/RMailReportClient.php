<?php

/**
 * RMailReportClient
 *
 * Consulta el estado de entrega de correos certificados enviados por RMail
 * y descarga el Recibo Registrado (testigo/acta de entrega) en PDF.
 *
 * Proceso desatendido:
 * 1. Consulta UsageReport filtrando por CustomerTrackingId (= radicado SGDEA)
 * 2. Evalúa el estado de entrega
 * 3. Cuando está confirmado, descarga el PDF del recibo
 * 4. Vincula el archivo al radicado en SQL Server
 *
 * Este cliente está diseñado para ejecutarse desde una tarea programada
 * (Windows Task Scheduler → script PHP CLI).
 */
class RMailReportClient
{
    private string $baseUrl;
    private RMailAuthClient $auth;

    // Estados de entrega conocidos en RMail
    const STATUS_DELIVERED     = 'delivered';
    const STATUS_OPENED        = 'opened';
    const STATUS_PENDING       = 'pending';
    const STATUS_FAILED        = 'failed';
    const STATUS_BOUNCED       = 'bounced';

    public function __construct(string $baseUrl, RMailAuthClient $auth)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->auth    = $auth;
    }

    /**
     * Descarga el testigo de entrega del correo certificado.
     * RMail retorna el testigo como archivo ZIP.
     *
     * Endpoint: GET {base_url}/api/v1/Receipt/{customerTrackingId}
     *
     * @param  string $customerTrackingId Número de radicado del SGDEA
     * @param  string $destPath           Ruta destino donde guardar el ZIP
     * @return bool   true = descarga exitosa, false = aún no disponible (reintentar)
     * @throws RMailReportException
     */
    public function downloadReceipt(string $customerTrackingId, string $destPath): bool
    {
        $token = $this->auth->getAccessToken();
        $url   = $this->baseUrl . '/api/v1/Receipt/' . urlencode($customerTrackingId);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPGET        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $content   = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $mimeType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($content === false || $curlError) {
            throw new RMailReportException(
                "Error cURL descargando testigo (TrackingId: {$customerTrackingId}): {$curlError}"
            );
        }

        if ($httpCode === 401) {
            $this->auth->forceRefresh();
            return $this->downloadReceipt($customerTrackingId, $destPath);
        }

        // 404 = testigo aún no generado por RMail, el desatendido reintentará
        if ($httpCode === 404) {
            return false;
        }

        if ($httpCode !== 200) {
            throw new RMailReportException(
                "Error HTTP {$httpCode} descargando testigo (TrackingId: {$customerTrackingId})"
            );
        }

        // Verificar magic bytes ZIP: los primeros 2 bytes deben ser "PK"
        if (substr($content, 0, 2) !== 'PK') {
            error_log(sprintf(
                '[RMail] Respuesta no es ZIP para TrackingId %s. MimeType: %s. Primeros bytes: %s',
                $customerTrackingId,
                $mimeType,
                bin2hex(substr($content, 0, 8))
            ));
            return false;
        }

        // Asegurar extensión .zip en la ruta destino
        $destPath = $this->ensureZipExtension($destPath);

        // Crear directorio destino si no existe (importante en rutas UNC/IIS)
        $dir = dirname($destPath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RMailReportException(
                "No se pudo crear el directorio para el testigo: {$dir}"
            );
        }

        $written = file_put_contents($destPath, $content, LOCK_EX);
        if ($written === false) {
            throw new RMailReportException(
                "No se pudo escribir el testigo en: {$destPath}"
            );
        }

        return true;
    }

    /**
     * Asegura que la ruta destino tenga extensión .zip
     */
    private function ensureZipExtension(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            $path = preg_replace('/\.[^.]+$/', '', $path) . '.zip';
        }
        return $path;
    }

    /**
     * Verifica si el estado de entrega indica que el correo fue entregado.
     */
    public function isDelivered(RMailDeliveryStatus $status): bool
    {
        $delivered = [self::STATUS_DELIVERED, self::STATUS_OPENED];
        return in_array(strtolower($status->deliveryStatus), $delivered, true);
    }
}

// =============================================================================
// DTO de estado de entrega
// =============================================================================

class RMailDeliveryStatus
{
    public string  $messageId       = '';
    public string  $customerTrackingId = '';
    public string  $deliveryStatus  = '';
    public string  $recipient       = '';
    public string  $subject         = '';
    public ?string $deliveredAt     = null;
    public ?string $openedAt        = null;
    public array   $rawData         = [];

    public static function fromArray(array $data): self
    {
        $obj = new self();
        $obj->rawData             = $data;
        $obj->messageId           = (string)($data['MessageId']           ?? $data['messageId']           ?? '');
        $obj->customerTrackingId  = (string)($data['CustomerTrackingId']  ?? $data['customerTrackingId']  ?? '');
        $obj->deliveryStatus      = (string)($data['DeliveryStatus']      ?? $data['deliveryStatus']      ?? '');
        $obj->recipient           = (string)($data['Recipient']           ?? $data['recipient']           ?? '');
        $obj->subject             = (string)($data['Subject']             ?? $data['subject']             ?? '');
        $obj->deliveredAt         = $data['DeliveredAt']  ?? $data['deliveredAt']  ?? null;
        $obj->openedAt            = $data['OpenedAt']     ?? $data['openedAt']     ?? null;
        return $obj;
    }
}

class RMailReportException extends RuntimeException {}
