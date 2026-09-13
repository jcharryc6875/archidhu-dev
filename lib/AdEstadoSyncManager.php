<?php

/**
 * Consulta periodicamente el estado en el Directorio Activo de todos los usuarios
 * activos/bloqueados con autenticacion LDAP Nativa, y dispara la inactivacion +
 * reasignacion (UsuarioPendientesChecker::inactivarPorDirectorioActivo) para los que
 * el DA reporta deshabilitados o expirados. Es el "job programado" equivalente a
 * EmailSyncManager/SipostSyncJob, invocado desde lib/task/AdEstadoSyncJob.php.
 */
class AdEstadoSyncManager
{
    public function sincronizarEstados()
    {
        $auth = $this->conectarDirectorioActivo();
        if (!$auth) {
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
                if ($estado['activo'] === false && in_array($estado['code'], array('USER_LDAP_ERROR_DISABLED', 'USER_LDAP_ERROR_EXPIRED'))) {
                    UsuarioPendientesChecker::inactivarPorDirectorioActivo($usuario);
                    $inactivados++;
                }
            } catch (Exception $e) {
                $errores++;
                error_log('AdEstadoSyncManager: error verificando usuario '.$usuario->getUsuarioId().': '.$e->getMessage());
            }
        }

        error_log(sprintf('AdEstadoSyncManager: revisados=%d inactivados=%d errores=%d', $revisados, $inactivados, $errores));
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
            error_log('AdEstadoSyncManager: no se pudo conectar al Directorio Activo: '.$e->getMessage());

            return null;
        }
    }
}
