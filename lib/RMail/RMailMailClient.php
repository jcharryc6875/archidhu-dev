<?php

/**
 * RMailUploadClient
 *
 * Sube uno o más archivos adjuntos a la API de RMail antes del envío.
 * Retorna los IDs de archivo para incluir en el campo Attachments del correo.
 *
 * Endpoint: POST {base_url}/api/Upload
 */
class RMailUploadClient
{
    private string $baseUrl;
    private RMailAuthClient $auth;

    public function __construct(string $baseUrl, RMailAuthClient $auth)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->auth    = $auth;
    }

    /**
     * Sube un archivo y retorna su ID en RMail.
     *
     * @param string $filePath Ruta absoluta al archivo (UNC o local)
     * @return string ID del archivo subido
     * @throws RMailUploadException
     */
    public function uploadFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new RMailUploadException("Archivo no encontrado: {$filePath}");
        }

        $token = $this->auth->getAccessToken();

        $url   = $this->baseUrl . '/api/Upload';

        // CURLFile es la forma correcta en PHP 7.4 para multipart/form-data
        $cfile = new CURLFile(
            $filePath,
            mime_content_type($filePath) ?: 'application/octet-stream',
            basename($filePath)
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['file' => $cfile],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                // Content-Type lo establece cURL automáticamente para multipart
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError) {
            throw new RMailUploadException("Error cURL al subir archivo: {$curlError}");
        }

        // Reintentar con token fresco si expiró durante la subida
        if ($httpCode === 401) {
            $token = $this->auth->forceRefresh();
            return $this->uploadFile($filePath); // un solo reintento
        }

        if ($httpCode !== 200) {
            throw new RMailUploadException(
                "Error HTTP {$httpCode} al subir archivo. Respuesta: {$response}"
            );
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // RMail puede retornar el ID directamente como string
            $id = trim($response, '"');
            if (empty($id)) {
                throw new RMailUploadException(
                    "Respuesta inesperada al subir archivo: {$response}"
                );
            }
            return $id;
        }

        // Si retorna array con un ID
        if (is_array($data)) {
            return (string)($data[0] ?? $data['id'] ?? '');
        }

        return (string)$data;
    }

    /**
     * Sube múltiples archivos y retorna array de IDs.
     *
     * @param string[] $filePaths
     * @return string[]
     */
    public function uploadFiles(array $filePaths): array
    {
        $ids = [];
        foreach ($filePaths as $path) {
            $ids[] = $this->uploadFile($path);
        }
        return $ids;
    }
}

class RMailUploadException extends RuntimeException {}


// =============================================================================
// RMailMailClient
// Construye y envía el correo registrado (correo certificado) a RMail.
// =============================================================================

/**
 * DTO que representa un correo certificado a enviar por RMail.
 */
class RMailMessage
{
    /** @var string Remitente (debe coincidir con la cuenta RMail autenticada) */
    public string $from = '';

    /** @var string[] Destinatarios */
    public array $to = [];

    /** @var string[] CC (opcional) */
    public array $cc = [];

    /** @var string[] BCC (opcional) */
    public array $bcc = [];

    /** @var string Asunto */
    public string $subject = '';

    /** @var string Cuerpo HTML del mensaje */
    public string $body = '';

    /** @var string[] IDs de archivos subidos previamente con RMailUploadClient */
    public array $attachments = [];

    /**
     * ID de seguimiento personalizado — se mapea al número de radicado del SGDEA.
     * CRÍTICO: permite correlacionar el reporte de entrega con el radicado.
     */
    public string $customerTrackingId = '';

    /**
     * Tipo de correo registrado.
     * 1 = Marcado (Rastrear y Probar) — el que genera el Recibo Registrado/testigo
     * 2 = No marcado
     */
    public int $rpostType = 1;

    /** @var bool Si el correo lleva archivos grandes (>25MB usa LargeMail) */
    public bool $isLargeMail = false;
}

/**
 * RMailMailClient
 *
 * Envía un correo certificado a través de la API de RMail.
 * Retorna el ID de mensaje RMail para usarlo en el polling del estado.
 */
class RMailMailClient
{
    private string $baseUrl;
    private RMailAuthClient $auth;

    /** Identificador de aplicación asignado por RPost */
    private string $appId;

    public function __construct(string $baseUrl, RMailAuthClient $auth, string $appId = '')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->auth    = $auth;
        $this->appId   = $appId;
    }

    /**
     * Envía el correo certificado y retorna el resultado.
     *
     * @return RMailSendResult
     * @throws RMailMailException
     */
    public function send(RMailMessage $message): RMailSendResult
    {
        $this->validateMessage($message);

        $token   = $this->auth->getAccessToken();
        $url     = $this->baseUrl . '/api/v1/Mail';
        $payload = $this->buildPayload($message);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError) {
            throw new RMailMailException("Error cURL al enviar correo: {$curlError}");
        }

        if ($httpCode === 401) {
            $this->auth->forceRefresh();
            return $this->send($message); // un solo reintento
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RMailMailException(
                "Error HTTP {$httpCode} al enviar correo certificado. Respuesta: {$response}"
            );
        }

        $data = json_decode($response, true);

        $messageId     = $data['Message'][0]['MessageId']     ?? $data['MessageId'] ?? '';
        $messageText   = $data['Message'][0]['Message']       ?? '';
        $trackingId    = $data["ResultContent"]["TrackingId"] ?? '';

        return new RMailSendResult(
            true,
            $httpCode,
            $response,
            $messageId,
            $messageText,
            $trackingId,
            $message->customerTrackingId,
            new DateTime()
        );
    }

    // -------------------------------------------------------------------------
    // Construcción del payload JSON
    // -------------------------------------------------------------------------

    private function buildPayload(RMailMessage $message): array
    {
        $options = [
            'X-RPost-Type' => (string)$message->rpostType,
        ];

        if (!empty($this->appId)) {
            $options['X-RPost-App'] = $this->appId;
        }

        if (!empty($message->customerTrackingId)) {
            $options['X-Rpost-CustomerTrackingId'] = $message->customerTrackingId;
        }

        $payload = [
            'From'        => $message->from,
            'To'          => implode(',', $message->to),
            'Subject'     => $message->subject,
            'Body'        => $message->body,
            'Options'     => $options,
            'IsLargeMail' => $message->isLargeMail,
        ];

        if (!empty($message->cc)) {
            $payload['Cc'] = implode(',', $message->cc);
        }

        if (!empty($message->bcc)) {
            $payload['Bcc'] = implode(',', $message->bcc);
        }

        if (!empty($message->attachments)) {
            $payload['Attachments'] = $message->attachments;
        }

        return $payload;
    }

    private function validateMessage(RMailMessage $message): void
    {
        if (empty($message->from)) {
            throw new RMailMailException('El campo From es obligatorio.');
        }
        if (empty($message->to)) {
            throw new RMailMailException('Debe especificar al menos un destinatario (To).');
        }
        if (empty($message->subject)) {
            throw new RMailMailException('El asunto del correo es obligatorio.');
        }
        if (empty($message->customerTrackingId)) {
            throw new RMailMailException(
                'CustomerTrackingId es obligatorio para correlacionar con el radicado del SGDEA.'
            );
        }
    }
}

// =============================================================================
// DTO de resultado del envío
// =============================================================================

class RMailSendResult
{
    public bool     $success;
    public int      $httpCode;
    public string   $rawResponse;
    public string   $messageId;
    public string   $message;
    public string   $trackingId;
    public string   $customerTrackingId;
    public DateTime $sentAt;

    public function __construct(
        bool     $success,
        int      $httpCode,
        string   $rawResponse,
        string   $messageId,
        string   $message,
        string   $trackingId,
        string   $customerTrackingId,
        DateTime $sentAt
    ) {
        $this->success            = $success;
        $this->httpCode           = $httpCode;
        $this->rawResponse        = $rawResponse;
        $this->messageId          = $messageId;
        $this->message            = $message;
        $this->trackingId         = $trackingId;
        $this->customerTrackingId = $customerTrackingId;
        $this->sentAt             = $sentAt;
    }
}

class RMailMailException extends RuntimeException {}
