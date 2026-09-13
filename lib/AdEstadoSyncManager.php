<?php

/**
 * Consulta periodicamente el estado en el Directorio Activo de todos los usuarios
 * activos/bloqueados con autenticacion LDAP Nativa, y dispara la inactivacion +
 * reasignacion (UsuarioPendientesChecker::inactivarPorDirectorioActivo) para los que
 * el DA reporta deshabilitados o expirados. Es el "job programado" equivalente a
 * EmailSyncManager/SipostSyncJob, invocado desde lib/task/AdEstadoSyncJob.php.
 *
 * Cada ejecucion escribe su propio archivo de log en
 * <sf_log_dir>/ad_sync/<AAAA-MM-DD>/ad_sync_<AAAA-MM-DD_His>.log
 */
class AdEstadoSyncManager
{
    private $logFile;

    public function sincronizarEstados()
    {
        $this->logFile = $this->crearArchivoLog();
        $this->log('Inicio de sincronizacion de estados AD');

        $auth = $this->conectarDirectorioActivo();
        if (!$auth) {
            $this->log('No se pudo conectar al Directorio Activo, se cancela la ejecucion');

            return;
        }

        $c = new Criteria();
        $c->add(UsuarioPeer::TIPOAUTENTICACION_ID, UserAuthType::LdapNativo);
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID, array(
            UsuarioPendientesChecker::ESTADO_USUARIO_ACTIVO,
            UsuarioPendientesChecker::ESTADO_USUARIO_BLOQUEADO,
        ), Criteria::IN);
        $usuarios = UsuarioPeer::doSelect($c);

        $revisados = 0;
        $inactivados = 0;
        $errores = 0;

        foreach ($usuarios as $usuario) {
            if (!trim($usuario->getUsuarioAd())) {
                continue;
            }
            $revisados++;
            try {
                $user_ldap = $auth->findUser($usuario->getUsuarioAd());
                $estado = $auth->isUserActive($user_ldap);
                $this->log(sprintf(
                    'Usuario %d (%s / AD:%s) -> activo=%s codigo=%s razon=%s',
                    $usuario->getUsuarioId(),
                    $usuario->getUserName(),
                    $usuario->getUsuarioAd(),
                    $estado['activo'] ? 'true' : 'false',
                    $estado['code'],
                    $estado['razon']
                ));
                if ($estado['activo'] === false && in_array($estado['code'], array('USER_LDAP_ERROR_DISABLED', 'USER_LDAP_ERROR_EXPIRED'))) {
                    $this->log(sprintf(
                        'Usuario %d (%s / AD:%s) inactivo en AD (%s) -> se inactiva y reasigna en Archidhu',
                        $usuario->getUsuarioId(),
                        $usuario->getUserName(),
                        $usuario->getUsuarioAd(),
                        $estado['code']
                    ));
                    UsuarioPendientesChecker::inactivarPorDirectorioActivo($usuario);
                    $inactivados++;
                }
            } catch (Exception $e) {
                $errores++;
                $this->log(sprintf('Error verificando usuario %d (%s): %s', $usuario->getUsuarioId(), $usuario->getUserName(), $e->getMessage()));
            }
        }

        $this->log(sprintf('Fin de sincronizacion: revisados=%d inactivados=%d errores=%d', $revisados, $inactivados, $errores));
    }

    private function conectarDirectorioActivo()
    {
        try {
            $config = require sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'ldap_settings.php';
            //*****************************************************************************
            $config_controllers = simad_util::readConfigFileApp(array('domain_controllers'));
            $domains_controllers = isset($config_controllers['domain_controllers']) ? $config_controllers['domain_controllers'] : null;
            if (!empty($domains_controllers)) {
                $config['hosts'] = $domains_controllers;
            }
            //*****************************************************************************
            return new AdldapAuth($config);
        } catch (Exception $e) {
            $this->log('No se pudo conectar al Directorio Activo: '.$e->getMessage());

            return null;
        }
    }

    private function crearArchivoLog()
    {
        $carpetaDia = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.'ad_sync'.DIRECTORY_SEPARATOR.date('Y-m-d');
        if (!is_dir($carpetaDia)) {
            @mkdir($carpetaDia, 0755, true);
        }

        return $carpetaDia.DIRECTORY_SEPARATOR.'ad_sync_'.date('Y-m-d_His').'.log';
    }

    private function log($mensaje)
    {
        if ($this->logFile) {
            simad_util::writetolog($this->logFile, $mensaje);
        } else {
            error_log('AdEstadoSyncManager: '.$mensaje);
        }
    }
}
