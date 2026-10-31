<?php

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/OAuthTokenProvider.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Prueba del servicio de una cuenta de notificacion: verifica token y autenticacion SMTP sin enviar,
 * o envia un correo de prueba. Devuelve un diagnostico paso a paso que nunca incluye credenciales ni tokens.
 */
class NotificacionMailTester
{
    const MAX_TRAZAS = 40;

    /**
     * @return array ok, error, pasos (cada uno: paso, ok, detalle) y trazas (respuestas del servidor)
     */
    public static function probarConexion(array $cuenta)
    {
        $trazas = array();
        $pasos = array();
        $mail = self::crearMailer($trazas);
        $provider = NotificacionCuentaResolver::configurarMailer($mail, $cuenta);

        if ($provider !== null) {
            try {
                $provider->getAccessToken(true);
                $pasos[] = self::paso('Token OAuth2 (Microsoft Entra ID)', true, 'Token obtenido correctamente');
            } catch (Throwable $e) {
                $pasos[] = self::paso('Token OAuth2 (Microsoft Entra ID)', false, $e->getMessage());

                return self::resultado(false, $e->getMessage(), $pasos, $trazas);
            }
        }

        try {
            $ok = $mail->smtpConnect();
        } catch (Throwable $e) {
            $ok = false;
        }
        $error = $ok ? null : self::describirError($mail, $trazas);
        $pasos[] = self::paso(
            'Conexion SMTP y autenticacion',
            $ok,
            $ok ? 'Conectado a ' . $cuenta['host'] . ':' . $cuenta['puerto'] . ' y autenticado como ' . $mail->Username : $error
        );
        $mail->smtpClose();

        return self::resultado($ok, $error, $pasos, $trazas);
    }

    /**
     * Envia un correo real de prueba al destino indicado y deja la huella en NOTIFICACION_ENVIO_LOG.
     */
    public static function enviarPrueba(array $cuenta, $destino)
    {
        $trazas = array();
        $pasos = array();
        $mail = self::crearMailer($trazas);
        $asunto = 'Prueba de correo SGDEA ArchiDHu - ' . $cuenta['nombre'];

        try {
            $provider = NotificacionCuentaResolver::configurarMailer($mail, $cuenta);
            if ($provider !== null) {
                // PHPMailer descarta el error del token; se obtiene antes para reportar el motivo real.
                $provider->getAccessToken(true);
                $pasos[] = self::paso('Token OAuth2 (Microsoft Entra ID)', true, 'Token obtenido correctamente');
            }
            $from = $cuenta['remitente'];
            $mail->setFrom($from, trim((string) $cuenta['nombre_remitente']) !== '' ? $cuenta['nombre_remitente'] : $from);
            $mail->addAddress($destino);
            $mail->Subject = $asunto;
            $mail->msgHTML(
                '<p>Este es un correo de prueba generado desde el m&oacute;dulo de cuentas de notificaci&oacute;n de ArchiDHu.</p>'
                . '<p><b>Cuenta:</b> ' . htmlspecialchars($cuenta['nombre']) . '<br>'
                . '<b>Tipo:</b> ' . htmlspecialchars($cuenta['tipo']) . '<br>'
                . '<b>Fecha:</b> ' . date('Y-m-d H:i:s') . '</p>'
            );
            $ok = $mail->send();
            $error = $ok ? null : self::describirError($mail, $trazas);
        } catch (Throwable $e) {
            $ok = false;
            $error = $e->getMessage();
        }

        $pasos[] = self::paso('Envio del correo de prueba', $ok, $ok ? 'Correo aceptado por el servidor para ' . $destino : $error);
        NotificacionEnvioLogger::registrar($cuenta['id'], array($destino), $asunto, $ok, $error, false);

        return self::resultado($ok, $error, $pasos, $trazas);
    }

    /**
     * Solo se retienen respuestas del servidor y errores: los comandos del cliente llevan credenciales.
     */
    private static function crearMailer(array &$trazas)
    {
        $mail = new PHPMailer(false);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function ($str, $level) use (&$trazas) {
            $line = trim((string) $str);
            $esRespuesta = strncmp($line, 'SERVER -> CLIENT:', 17) === 0;
            if (($esRespuesta || stripos($line, 'SMTP ERROR') === 0 || strncmp($line, 'Connection:', 11) === 0)
                && count($trazas) < NotificacionMailTester::MAX_TRAZAS) {
                $trazas[] = NotificacionCuentaResolver::truncar($line, 300);
            }
        };

        return $mail;
    }

    private static function describirError($mail, array $trazas)
    {
        $error = trim(str_replace('https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting', '', $mail->ErrorInfo));
        $detalle = array();
        foreach ($trazas as $traza) {
            if (stripos($traza, 'SMTP ERROR') === 0 || preg_match('/^SERVER -> CLIENT:\s*[45]\d\d/', $traza)) {
                $detalle[] = trim(preg_replace('/^SERVER -> CLIENT:/', '', $traza));
            }
        }
        if (count($detalle) > 0) {
            $extra = implode(' | ', array_slice($detalle, -2));
            $error = $error === '' ? $extra : $error . ' [' . $extra . ']';
        }

        return $error;
    }

    private static function paso($nombre, $ok, $detalle)
    {
        return array('paso' => $nombre, 'ok' => (bool) $ok, 'detalle' => (string) $detalle);
    }

    private static function resultado($ok, $error, array $pasos, array $trazas)
    {
        return array('ok' => (bool) $ok, 'error' => $error, 'pasos' => $pasos, 'trazas' => $trazas);
    }
}
