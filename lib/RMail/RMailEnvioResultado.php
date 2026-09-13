<?php
// =============================================================================
// DTO resultado del envío
// =============================================================================

class RMailEnvioResultado
{
    public bool   $exito;
    public int    $status;
    public int    $radicadoId;
    public string $messageId;
    public string $trackingId;
    public string $customerTrackingId;
    public string $mensaje;

    public function __construct(
        bool   $exito,
        int    $status,
        int    $radicadoId,
        string $messageId,
        string $trackingId,
        string $customerTrackingId,
        string $mensaje
    ) {
        $this->exito              = $exito;
        $this->status             = $status;
        $this->radicadoId         = $radicadoId;
        $this->messageId          = $messageId;
        $this->trackingId         = $trackingId;
        $this->customerTrackingId = $customerTrackingId;
        $this->mensaje            = $mensaje;
    }
}

class CorreoCertificadoException extends RuntimeException {}
