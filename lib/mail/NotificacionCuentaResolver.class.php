<?php

/**
 * Entrega las cuentas remitentes activas en el orden en que deben intentarse (principal primero,
 * luego respaldo por prioridad), aplica la cuenta a un PHPMailer y mantiene su estado operativo.
 * La lectura de BD se cachea para no consultar la tabla en cada correo.
 */
class NotificacionCuentaResolver
{
    const TIPO_SMTP = 'SMTP';
    const TIPO_OAUTH2 = 'OAUTH2';

    const ESTADO_OK = 'OK';
    const ESTADO_ERROR = 'ERROR';
    const ESTADO_SIN_PROBAR = 'SIN_PROBAR';

    const LIST_TTL = 90;
    // Tiempo que una cuenta que fallo se intenta al final de la lista.
    const COOLDOWN_TTL = 120;
    // Cada cuanto se refresca FECHA_ULTIMO_ENVIO_OK mientras la cuenta sigue funcionando.
    const OK_REFRESH_SECONDS = 600;
    const SMTP_TIMEOUT = 45;

    private static $cuentas = null;
    private static $appParams = null;

    /**
     * @return array cuentas activas (secretos aun cifrados), vacio si no hay o el modelo no esta generado
     */
    public static function getCuentasActivas()
    {
        if (self::$cuentas !== null) {
            return self::$cuentas;
        }

        $cached = NotificacionMailCache::get('cuentas');
        if (is_array($cached)) {
            self::$cuentas = $cached;

            return self::$cuentas;
        }

        $cuentas = array();
        try {
            if (class_exists('NotificacionCuentaQuery')) {
                $rows = NotificacionCuentaQuery::create()
                    ->filterByActivo(1)
                    ->orderByEsPrincipal(Criteria::DESC)
                    ->orderByPrioridad(Criteria::ASC)
                    ->orderByNotificacioncuentaId(Criteria::ASC)
                    ->find();
                foreach ($rows as $row) {
                    $cuentas[] = self::aArray($row);
                }
            }
        } catch (Throwable $e) {
            error_log('NotificacionCuentaResolver: no se pudo leer NOTIFICACION_CUENTA: ' . $e->getMessage());

            return array();
        }

        NotificacionMailCache::set('cuentas', $cuentas, self::LIST_TTL);
        self::$cuentas = $cuentas;

        return $cuentas;
    }

    /**
     * Las cuentas en enfriamiento (fallaron hace poco) pasan al final, conservando el orden relativo.
     */
    public static function ordenarPorDisponibilidad(array $cuentas)
    {
        $disponibles = array();
        $enfriando = array();
        foreach ($cuentas as $cuenta) {
            if (NotificacionMailCache::get('cd_' . $cuenta['id']) !== null) {
                $enfriando[] = $cuenta;
            } else {
                $disponibles[] = $cuenta;
            }
        }

        return array_merge($disponibles, $enfriando);
    }

    /**
     * Registra el resultado de un envio. A BD solo se escribe cuando el estado cambia (o cada
     * OK_REFRESH_SECONDS para mantener la fecha del ultimo envio exitoso), no en cada correo.
     */
    public static function registrarResultado(array $cuenta, $ok, $error = null)
    {
        $id = (int) $cuenta['id'];
        $now = time();
        $known = NotificacionMailCache::get('st_' . $id);
        $estadoConocido = is_array($known) ? $known['estado'] : $cuenta['estado'];
        $ultimoOk = is_array($known) ? (int) $known['ok_ts'] : 0;

        if ($ok) {
            NotificacionMailCache::remove('cd_' . $id);
            if ($estadoConocido === self::ESTADO_OK && ($now - $ultimoOk) < self::OK_REFRESH_SECONDS) {
                return;
            }
            self::actualizarEstado($id, array(
                'EstadoOperativo' => self::ESTADO_OK,
                'UltimoError' => null,
                'FechaUltimoEnvioOk' => date('Y-m-d H:i:s', $now),
            ));
            NotificacionMailCache::set('st_' . $id, array('estado' => self::ESTADO_OK, 'ok_ts' => $now), 86400);

            return;
        }

        NotificacionMailCache::set('cd_' . $id, 1, self::COOLDOWN_TTL);
        if ($estadoConocido === self::ESTADO_ERROR) {
            return;
        }
        self::actualizarEstado($id, array(
            'EstadoOperativo' => self::ESTADO_ERROR,
            'UltimoError' => self::truncar($error, 500),
        ));
        NotificacionMailCache::set('st_' . $id, array('estado' => self::ESTADO_ERROR, 'ok_ts' => $ultimoOk), 86400);
    }

    /**
     * Registra el resultado de una prueba manual del servicio desde el modulo administrativo.
     * A diferencia de registrarResultado(), siempre escribe en BD.
     */
    public static function registrarPrueba($id, $ok, $error = null)
    {
        $id = (int) $id;
        self::actualizarEstado($id, array(
            'EstadoOperativo' => $ok ? self::ESTADO_OK : self::ESTADO_ERROR,
            'UltimoError' => $ok ? null : self::truncar($error, 500),
            'FechaUltimaPrueba' => date('Y-m-d H:i:s'),
        ));
        if ($ok) {
            NotificacionMailCache::remove('cd_' . $id);
        }
        NotificacionMailCache::set('st_' . $id, array('estado' => $ok ? self::ESTADO_OK : self::ESTADO_ERROR, 'ok_ts' => $ok ? time() : 0), 86400);
    }

    /**
     * Debe llamarse al guardar o eliminar cuentas desde el modulo administrativo.
     */
    public static function invalidarCache()
    {
        self::$cuentas = null;
        NotificacionMailCache::remove('cuentas');
    }

    /**
     * Aplica la cuenta (host, credenciales o token OAuth2, remitente de transporte) al PHPMailer.
     * El remitente visible (From) lo decide quien envia.
     *
     * @return OAuthTokenProviderSimad|null el proveedor cuando la cuenta es OAuth2, para que quien envia
     *         pueda obtener el token antes y reportar el motivo exacto (PHPMailer oculta el error del token)
     */
    public static function configurarMailer($mail, array $cuenta)
    {
        $usuario = trim((string) $cuenta['usuario']) !== '' ? trim($cuenta['usuario']) : $cuenta['remitente'];
        $secure = trim((string) $cuenta['secure']);

        $mail->Host = $cuenta['host'];
        $mail->Port = (int) $cuenta['puerto'];
        $mail->Username = $usuario;
        $mail->Timeout = self::SMTP_TIMEOUT;

        if ($cuenta['tipo'] === self::TIPO_OAUTH2) {
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = $secure !== '' ? $secure : 'tls';
            $mail->AuthType = 'XOAUTH2';
            $mail->Password = '';
            $provider = new OAuthTokenProviderSimad(
                $usuario,
                $cuenta['tenant'],
                $cuenta['client'],
                self::descifrar($cuenta['secret']),
                $cuenta['scope'],
                self::getAppParam('notif_mail_ca_bundle'),
                self::getAppParam('notif_mail_token_endpoint')
            );
            $mail->setOAuth($provider);

            return $provider;
        }

        $mail->SMTPAuth = !empty($cuenta['auth']);
        $mail->SMTPSecure = $secure;
        $mail->AuthType = '';
        $mail->Password = self::descifrar($cuenta['pass']);

        return null;
    }

    public static function aArray($row)
    {
        return array(
            'id' => $row->getNotificacioncuentaId(),
            'nombre' => $row->getNombre(),
            'tipo' => $row->getTipo(),
            'remitente' => $row->getEmailRemitente(),
            'nombre_remitente' => $row->getNombreRemitente(),
            'host' => $row->getHostSmtp(),
            'puerto' => $row->getPuerto(),
            'secure' => $row->getSecureSmtp(),
            'auth' => $row->getAuthSmtp(),
            'usuario' => $row->getUsuarioSmtp(),
            'pass' => $row->getPassSmtp(),
            'tenant' => $row->getTenantId(),
            'client' => $row->getClientId(),
            'secret' => $row->getClientSecret(),
            'scope' => $row->getScope(),
            'estado' => $row->getEstadoOperativo(),
        );
    }

    public static function descifrar($valor)
    {
        if ($valor === null || $valor === '') {
            return '';
        }
        $plano = SED::decryption($valor);

        return $plano === false ? '' : $plano;
    }

    public static function truncar($texto, $max)
    {
        $texto = (string) $texto;

        return function_exists('mb_substr') ? mb_substr($texto, 0, $max, 'UTF-8') : substr($texto, 0, $max);
    }

    private static function actualizarEstado($id, array $valores)
    {
        try {
            NotificacionCuentaQuery::create()->filterByPrimaryKey($id)->update($valores);
        } catch (Throwable $e) {
            error_log('NotificacionCuentaResolver: no se pudo actualizar el estado de la cuenta ' . $id . ': ' . $e->getMessage());
        }
    }

    /**
     * Parametros opcionales de config/app.ini (se lee el archivo una sola vez por proceso).
     */
    private static function getAppParam($name)
    {
        if (self::$appParams === null) {
            $cfg = simad_util::readConfigFileApp(array('notif_mail_ca_bundle', 'notif_mail_token_endpoint'));
            self::$appParams = is_array($cfg) ? $cfg : array();
        }

        return !empty(self::$appParams[$name]) ? trim(self::$appParams[$name]) : null;
    }
}
