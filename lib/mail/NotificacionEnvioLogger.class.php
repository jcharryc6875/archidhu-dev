<?php

/**
 * Registra la huella de cada intento de envio de correo (cuenta, destinatarios, asunto, resultado y
 * error devuelto por el servidor o PhpMailer). Nunca guarda el cuerpo del mensaje y nunca interrumpe
 * el envio: si no puede escribir, lo reporta a error_log y continua.
 */
class NotificacionEnvioLogger
{
    const RESULTADO_OK = 'OK';
    const RESULTADO_ERROR = 'ERROR';

    /**
     * @param int|null $cuentaId cuenta usada, null cuando el llamador configuro su propio SMTP
     * @param array $destinatarios correos destino
     * @param bool $reportable false para pruebas manuales: su falla no entra al consolidado de fallas
     */
    public static function registrar($cuentaId, array $destinatarios, $asunto, $ok, $error = null, $reportable = true)
    {
        try {
            if (!class_exists('NotificacionEnvioLog')) {
                return;
            }
            $log = new NotificacionEnvioLog();
            $log->setNotificacioncuentaId($cuentaId ? (int) $cuentaId : null);
            $log->setFecha(date('Y-m-d H:i:s'));
            $log->setDestinatarios(NotificacionCuentaResolver::truncar(implode(', ', $destinatarios), 500));
            $log->setAsunto(NotificacionCuentaResolver::truncar($asunto, 250));
            $log->setResultado($ok ? self::RESULTADO_OK : self::RESULTADO_ERROR);
            $log->setError($ok ? null : NotificacionCuentaResolver::truncar($error, 1000));
            // Los OK y las pruebas manuales no se reportan en el consolidado de fallas.
            $log->setNotificado(($ok || !$reportable) ? 1 : 0);
            $log->save();
        } catch (Throwable $e) {
            error_log('NotificacionEnvioLogger: no se pudo registrar el envio: ' . $e->getMessage());
        }
    }
}
