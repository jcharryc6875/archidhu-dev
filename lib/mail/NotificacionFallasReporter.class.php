<?php

/**
 * Consolida las fallas de envio de correo registradas en NOTIFICACION_ENVIO_LOG y las informa por correo
 * a los destinatarios configurados en config/app.ini. Tambien purga la huella antigua segun la retencion.
 * Pensada para ejecutarse dos veces al dia desde la tarea programada notificacion:reporte_fallas.
 */
class NotificacionFallasReporter
{
    const MAX_FILAS = 5000;
    const MAX_GRUPOS = 50;
    const RETENCION_DEFECTO_DIAS = 90;

    /**
     * @return array reporte (enviado|omitido|error), fallas (cantidad informada), purgados y mensaje
     */
    public static function ejecutar()
    {
        $cfg = simad_util::readConfigFileApp(array(
            'notif_mail_fail_report_enable', 'notif_mail_fail_report_to', 'notif_mail_log_retention_days',
        ));
        $cfg = is_array($cfg) ? $cfg : array();
        $resumen = array('reporte' => 'omitido', 'fallas' => 0, 'purgados' => 0, 'mensaje' => 'Reporte de fallas deshabilitado');

        if (!empty($cfg['notif_mail_fail_report_enable'])) {
            $destinos = self::parseDestinos(isset($cfg['notif_mail_fail_report_to']) ? $cfg['notif_mail_fail_report_to'] : '');
            if (count($destinos) == 0) {
                $resumen['mensaje'] = 'No hay correos validos en notif_mail_fail_report_to';
            } else {
                $resumen = array_merge($resumen, self::enviarReporte($destinos));
            }
        }

        $dias = isset($cfg['notif_mail_log_retention_days']) && $cfg['notif_mail_log_retention_days'] !== ''
            ? (int) $cfg['notif_mail_log_retention_days']
            : self::RETENCION_DEFECTO_DIAS;
        $resumen['purgados'] = self::purgar($dias);

        return $resumen;
    }

    public static function parseDestinos($valor)
    {
        $destinos = array();
        foreach (preg_split('/[,;\s]+/', (string) $valor, -1, PREG_SPLIT_NO_EMPTY) as $correo) {
            if (filter_var($correo, FILTER_VALIDATE_EMAIL) && !in_array($correo, $destinos, true)) {
                $destinos[] = $correo;
            }
        }

        return $destinos;
    }

    private static function enviarReporte(array $destinos)
    {
        $filas = NotificacionEnvioLogQuery::create()
            ->filterByResultado(NotificacionEnvioLogger::RESULTADO_ERROR)
            ->filterByNotificado(0)
            ->orderByNotificacionenviologId(Criteria::ASC)
            ->limit(self::MAX_FILAS)
            ->find();
        if (count($filas) == 0) {
            return array('mensaje' => 'No hay fallas pendientes de informar');
        }

        $cuentas = array();
        foreach (NotificacionCuentaPeer::doSelect(new Criteria()) as $cuenta) {
            $cuentas[$cuenta->getPrimaryKey()] = $cuenta;
        }
        $grupos = array();
        $maxId = 0;
        foreach ($filas as $fila) {
            $maxId = max($maxId, (int) $fila->getPrimaryKey());
            $error = NotificacionCuentaResolver::truncar($fila->getError(), 200);
            $clave = (int) $fila->getNotificacioncuentaId() . '|' . $error;
            if (!isset($grupos[$clave])) {
                $grupos[$clave] = array('cuenta_id' => $fila->getNotificacioncuentaId(), 'error' => $error, 'total' => 0,
                    'primera' => $fila->getFecha(), 'ultima' => $fila->getFecha());
            }
            $grupos[$clave]['total']++;
            $grupos[$clave]['ultima'] = $fila->getFecha();
        }
        usort($grupos, function ($a, $b) {
            return $b['total'] - $a['total'];
        });

        $total = count($filas);
        $mail = new BaseMailSimad();
        $mail->SetSubject('SGDEA ArchiDHu: ' . $total . ' falla(s) en el envio de correos');
        $mail->SetMsgHTML(self::armarCuerpo($grupos, $cuentas, $total));
        foreach ($destinos as $destino) {
            $mail->SetAddAddress($destino, $destino);
        }
        $resultado = $mail->InitSend();
        if ($resultado !== true) {
            // Sin marcar: las fallas se informan en la siguiente ejecucion.
            return array('reporte' => 'error', 'mensaje' => 'No se pudo enviar el reporte: ' . $resultado);
        }

        NotificacionEnvioLogQuery::create()
            ->filterByResultado(NotificacionEnvioLogger::RESULTADO_ERROR)
            ->filterByNotificado(0)
            ->filterByNotificacionenviologId(array('max' => $maxId))
            ->update(array('Notificado' => 1));

        return array('reporte' => 'enviado', 'fallas' => $total, 'mensaje' => 'Reporte enviado a ' . implode(', ', $destinos));
    }

    private static function armarCuerpo(array $grupos, array $cuentas, $total)
    {
        $h = function ($texto) {
            return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
        };
        $html = '<p>Se registraron <b>' . (int) $total . '</b> falla(s) en el env&iacute;o de correos desde el &uacute;ltimo reporte.</p>'
            . '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;font-size:12px">'
            . '<tr><th>Cuenta</th><th>Error</th><th>Cantidad</th><th>Primera</th><th>&Uacute;ltima</th></tr>';
        foreach (array_slice($grupos, 0, self::MAX_GRUPOS) as $grupo) {
            $nombre = isset($cuentas[$grupo['cuenta_id']]) ? $cuentas[$grupo['cuenta_id']]->getNombre() : 'SMTP propio del llamador / sin cuenta';
            $html .= '<tr><td>' . $h($nombre) . '</td><td>' . $h($grupo['error']) . '</td><td align="center">' . (int) $grupo['total']
                . '</td><td>' . $h($grupo['primera']) . '</td><td>' . $h($grupo['ultima']) . '</td></tr>';
        }
        $html .= '</table>';
        if (count($grupos) > self::MAX_GRUPOS) {
            $html .= '<p>Se muestran los ' . self::MAX_GRUPOS . ' grupos m&aacute;s frecuentes de ' . count($grupos) . '.</p>';
        }
        $html .= '<p><b>Estado de las cuentas:</b></p><ul>';
        foreach ($cuentas as $cuenta) {
            $html .= '<li>' . $h($cuenta->getNombre()) . ': ' . $h($cuenta->getEstadoOperativo())
                . ($cuenta->getActivo() ? '' : ' (inactiva)') . '</li>';
        }
        $html .= '</ul><p>Consulte el detalle en el m&oacute;dulo de auditor&iacute;a de env&iacute;os de correo.</p>';

        return $html;
    }

    private static function purgar($dias)
    {
        if ($dias <= 0) {
            return 0;
        }

        return (int) NotificacionEnvioLogQuery::create()
            ->filterByFecha(array('max' => date('Y-m-d H:i:s', strtotime('-' . $dias . ' days'))))
            ->delete();
    }
}
