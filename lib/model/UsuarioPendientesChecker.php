<?php

/**
 * Valida si un usuario tiene actividades pendientes (Comunicaciones Externas Recibidas,
 * Comunicaciones Internas, Comunicaciones Externas Enviadas y Prestamos), en cualquier
 * vigencia, para bloquear su deshabilitacion/suspension; y gestiona la inactivacion +
 * reasignacion (o la notificacion de novedad) cuando el usuario es inactivado en el
 * Directorio Activo (detectado en el login o por el job programado de sincronizacion).
 *
 * Los "Copias" nunca cuentan como pendientes.
 */
class UsuarioPendientesChecker
{
    const ESTADO_USUARIO_ACTIVO = 1;
    const ESTADO_USUARIO_BLOQUEADO = 2;
    const ESTADO_USUARIO_DESHABILITADO = 4;
    const ESTADO_USUARIO_SUSPENDIDO = 5;

    const MENSAJE_BLOQUEO = "No es posible deshabilitar o suspender el usuario, debido a que presenta actividades pendientes en el sistema. Verifique las actividades Pendientes.";

    // Usuario de sistema "INTEGRACION ARCHIDHU LEX - GRUPO DE SERVICIO AL CIUDADANO",
    // registrado como actor de las reasignaciones/inactivaciones automaticas por DA.
    const USUARIO_SISTEMA_INTEGRACION_ID = 4249;

    const OBSERVACION_REASIGNACION_AUTOMATICA = 'Reasignacion automatica generada por inactivacion de usuario desde Directorio Activo.';

    // TIPO_PROCESO_COM: 3 = Gestor (aplica a ComRecibida y ComEnviada, que tienen esta columna)
    const TIPOPROCESOCOM_GESTOR = 3;

    // ComRecibida: ROL_USUARIO_RECIBIDA 2 = Destinatario; ESTADO_COM_RECIBIDA 1 = Enviada
    const RECIBIDA_ROL_DESTINATARIO = 2;
    const RECIBIDA_ESTADO_ENVIADA = 1;

    // ComInterna no tiene TIPOPROCESOCOM_ID ni "Gestor"; se reasigna con el rol Revision
    // (ROLUSUARIO_COMINTERNA 5) y se deja en el estado que ya usa la reasignacion manual (5 = Por Responder)
    const INTERNA_ROL_REVISION = 5;
    const INTERNA_ESTADO_POR_RESPONDER = 5;

    // ComEnviada: ROLUS_COMENVIADA 5 = Gestiona; no existe estado "Enviada" en su catalogo,
    // se deja en el mismo estado por defecto que ya usa la reasignacion manual (2 = Por Leer)
    const ENVIADA_ROL_GESTIONA = 5;
    const ENVIADA_ESTADO_POR_LEER = 2;

    private static $etiquetasBandeja = array(
        'com_recibida' => 'Comunicaciones Externas Recibidas',
        'com_interna'  => 'Comunicaciones Internas',
        'com_enviada'  => 'Comunicaciones Externas Enviadas',
        'prestamo'     => 'Prestamos',
    );

    public static function getMensajeBloqueo()
    {
        return self::MENSAJE_BLOQUEO;
    }

    public static function tienePendientes($usuario_id)
    {
        foreach (self::bucketDefinitions($usuario_id) as $bandeja) {
            foreach ($bandeja['criterios'] as $criteria) {
                if (call_user_func(array($bandeja['peer'], 'doCount'), $criteria) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getPendientesPorBandeja($usuario_id)
    {
        $resultado = array();
        foreach (self::bucketDefinitions($usuario_id) as $bandeja_key => $bandeja) {
            $registros = array();
            foreach ($bandeja['criterios'] as $criteria) {
                $registros = array_merge($registros, call_user_func(array($bandeja['peer'], 'doSelect'), $criteria));
            }
            if (count($registros)) {
                $resultado[$bandeja_key] = $registros;
            }
        }

        return $resultado;
    }

    /**
     * Punto unico de entrada cuando se confirma que un usuario fue inactivado en el
     * Directorio Activo, sin importar quien lo detecto: el login (security/actions.class.php)
     * o el job programado de sincronizacion con AD. Deshabilita al usuario en Archidhu,
     * audita el cambio y reasigna (o alerta) sus actividades pendientes.
     */
    public static function inactivarPorDirectorioActivo(Usuario $user)
    {
        if (in_array($user->getEstadousuarioId(), array(self::ESTADO_USUARIO_DESHABILITADO, self::ESTADO_USUARIO_SUSPENDIDO))) {
            return; // ya estaba inactivado, no reprocesar
        }

        $usuario_anterior = clone $user;
        $user->setEstadousuarioId(self::ESTADO_USUARIO_DESHABILITADO);
        $user->save();
        //*********************************************************************************
        UsuarioPeer::addHistoricoUser($user);
        AuditLogPeer::guardarAuditoriaLite(
            UsuarioPeer::OM_CLASS,
            $usuario_anterior,
            $user,
            ModulesEnable::Seguridad,
            $user->getCedula(),
            self::USUARIO_SISTEMA_INTEGRACION_ID
        );
        //*********************************************************************************
        self::reasignarOAlertar($user);
    }

    /**
     * Reasigna las actividades pendientes del usuario al jefe de dependencia
     * (RECEPTOR_DEP=1, Activo o Bloqueado) dejando bitacora (Rol Gestor/Revision/Gestiona,
     * Estado, Observacion fija, actor = usuario de integracion) en cada comunicacion, o
     * envia el correo de novedad si no hay un jefe valido.
     */
    public static function reasignarOAlertar(Usuario $user)
    {
        $pendientes = self::getPendientesPorBandeja($user->getUsuarioId());
        if (empty($pendientes)) {
            return;
        }

        $jefe = UsuarioPeer::getJefeDependencia($user->getDependenciaId());
        $jefeValido = $jefe
            && $jefe->getUsuarioId() != $user->getUsuarioId()
            && in_array($jefe->getEstadousuarioId(), array(self::ESTADO_USUARIO_ACTIVO, self::ESTADO_USUARIO_BLOQUEADO));

        if ($jefeValido) {
            $resumen = self::reasignarRegistros($pendientes, $jefe, $user);
            self::enviarCorreoResumenJefe($jefe, $user, $resumen);
        } else {
            self::enviarCorreoNovedadSinJefe($user);
        }
    }

    private static function reasignarRegistros($pendientesPorBandeja, Usuario $jefe, Usuario $usuarioDeshabilitado)
    {
        $cargousuarioid = self::getCargoUsuarioPrincipalId($jefe->getUsuarioId());

        $resumen = array();
        foreach ($pendientesPorBandeja as $bandeja_key => $registros) {
            $resumen[$bandeja_key] = array('cantidad' => count($registros), 'radicados' => array());
            foreach ($registros as $registro) {
                switch ($bandeja_key) {
                    case 'com_recibida':
                        $codigo = self::reasignarComRecibida($registro, $jefe, $cargousuarioid);
                        break;
                    case 'com_interna':
                        $codigo = self::reasignarComInterna($registro, $jefe, $cargousuarioid);
                        break;
                    case 'com_enviada':
                        $codigo = self::reasignarComEnviada($registro, $jefe, $cargousuarioid);
                        break;
                    case 'prestamo':
                        $codigo = self::reasignarPrestamo($registro, $jefe);
                        break;
                    default:
                        $codigo = null;
                }
                if ($codigo) {
                    $resumen[$bandeja_key]['radicados'][] = $codigo;
                }
            }
        }

        return $resumen;
    }

    private static function getCargoUsuarioPrincipalId($usuario_id)
    {
        $c = new Criteria();
        $c->add(CargoUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(CargoUsuarioPeer::ES_PRINCIPAL, true);
        $cargoUsuario = CargoUsuarioPeer::doSelectOne($c);

        return $cargoUsuario ? $cargoUsuario->getPrimaryKey() : null;
    }

    /**
     * Reasigna una comunicacion recibida al jefe dejando bitacora: nueva fila
     * COMRECIBIDA_USUARIO (Rol Destinatario, Tipo Proceso Gestor, Estado Enviada),
     * observacion fija en la comunicacion y auditoria con actor = usuario de integracion.
     */
    private static function reasignarComRecibida(ComrecibidaUsuario $registro, Usuario $jefe, $cargousuarioid)
    {
        $com_recibida = ComRecibidaPeer::retrieveByPk($registro->getComrecibidaId());
        if (!$com_recibida) {
            return null;
        }
        $com_recibida_anterior = $com_recibida->copy();
        //*********************************************************************************
        $codigoReenResp = $com_recibida->getCodigoReenResp() ? $com_recibida->getCodigoReenResp() : $com_recibida->getPrimaryKey();
        $observaciones = trim($com_recibida->getObsReenResp())
            ? trim($com_recibida->getObsReenResp()).' | '.self::OBSERVACION_REASIGNACION_AUTOMATICA
            : self::OBSERVACION_REASIGNACION_AUTOMATICA;
        $com_recibida->setObsReenResp($observaciones);
        $com_recibida->setCodigoReenResp($codigoReenResp);
        $com_recibida->save();
        //*********************************************************************************
        ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(), self::RECIBIDA_ROL_DESTINATARIO, 0);
        ComRecibidaPeer::addUserRolByCom(
            $com_recibida->getPrimaryKey(),
            $jefe->getUsuarioId(),
            $cargousuarioid,
            self::RECIBIDA_ESTADO_ENVIADA,
            self::RECIBIDA_ROL_DESTINATARIO,
            1,
            self::TIPOPROCESOCOM_GESTOR
        );
        //*********************************************************************************
        AuditLogPeer::guardarAuditoriaLite(
            'ComRecibida',
            $com_recibida_anterior,
            $com_recibida,
            ModulesEnable::ComRecibida,
            $com_recibida->getRadicado(),
            self::USUARIO_SISTEMA_INTEGRACION_ID
        );

        return $com_recibida->getRadicado();
    }

    /**
     * Reasigna una comunicacion interna al jefe dejando bitacora: nueva fila
     * COMINTERNA_USUARIO (Rol Revision, Estado Por Responder), observacion fija en la
     * comunicacion y auditoria con actor = usuario de integracion.
     */
    private static function reasignarComInterna(CominternaUsuario $registro, Usuario $jefe, $cargousuarioid)
    {
        $com_interna = ComInternaPeer::retrieveByPk($registro->getCominternaId());
        if (!$com_interna) {
            return null;
        }
        $com_interna_anterior = $com_interna->copy();
        //*********************************************************************************
        $codigoReenResp = $com_interna->getCodigoReenResp() ? $com_interna->getCodigoReenResp() : $com_interna->getPrimaryKey();
        $observaciones = trim($com_interna->getObsReenResp())
            ? trim($com_interna->getObsReenResp()).' | '.self::OBSERVACION_REASIGNACION_AUTOMATICA
            : self::OBSERVACION_REASIGNACION_AUTOMATICA;
        $com_interna->setObsReenResp($observaciones);
        $com_interna->setCodigoReenResp($codigoReenResp);
        $com_interna->save();
        //*********************************************************************************
        ComInternaPeer::updateAsignadoCom($com_interna->getPrimaryKey(), self::INTERNA_ROL_REVISION, 0);
        ComInternaPeer::addUserRolByCom(
            $com_interna->getPrimaryKey(),
            $jefe->getUsuarioId(),
            $cargousuarioid,
            self::INTERNA_ESTADO_POR_RESPONDER,
            self::INTERNA_ROL_REVISION,
            1
        );
        //*********************************************************************************
        AuditLogPeer::guardarAuditoriaLite(
            'ComInterna',
            $com_interna_anterior,
            $com_interna,
            ModulesEnable::ComInterna,
            $com_interna->getRadicado(),
            self::USUARIO_SISTEMA_INTEGRACION_ID
        );

        return $com_interna->getRadicado();
    }

    /**
     * Reasigna una comunicacion enviada al jefe dejando bitacora: nueva fila
     * ENVIADA_USUARIO (Rol Gestiona, Tipo Proceso Gestor, Estado Por Leer), observacion
     * fija en la comunicacion y auditoria con actor = usuario de integracion.
     */
    private static function reasignarComEnviada(EnviadaUsuario $registro, Usuario $jefe, $cargousuarioid)
    {
        $com_enviada = ComEnviadaPeer::retrieveByPk($registro->getComenviadaId());
        if (!$com_enviada) {
            return null;
        }
        $com_enviada_anterior = $com_enviada->copy();
        //*********************************************************************************
        $codigoReenResp = $com_enviada->getCodigoReenResp() ? $com_enviada->getCodigoReenResp() : $com_enviada->getPrimaryKey();
        $observaciones = trim($com_enviada->getObsReenResp())
            ? trim($com_enviada->getObsReenResp()).' | '.self::OBSERVACION_REASIGNACION_AUTOMATICA
            : self::OBSERVACION_REASIGNACION_AUTOMATICA;
        $com_enviada->setObsReenResp($observaciones);
        $com_enviada->setCodigoReenResp($codigoReenResp);
        $com_enviada->setTipoprocesocomId(self::TIPOPROCESOCOM_GESTOR);
        $com_enviada->save();
        //*********************************************************************************
        ComEnviadaPeer::updateAsignadoCom($com_enviada->getPrimaryKey(), self::ENVIADA_ROL_GESTIONA, 0);
        ComEnviadaPeer::addUserRolByCom(
            $com_enviada->getPrimaryKey(),
            $jefe->getUsuarioId(),
            $cargousuarioid,
            self::ENVIADA_ESTADO_POR_LEER,
            self::ENVIADA_ROL_GESTIONA,
            1,
            self::TIPOPROCESOCOM_GESTOR
        );
        //*********************************************************************************
        AuditLogPeer::guardarAuditoriaLite(
            'ComEnviada',
            $com_enviada_anterior,
            $com_enviada,
            ModulesEnable::ComEnviada,
            $com_enviada->getRadicado(),
            self::USUARIO_SISTEMA_INTEGRACION_ID
        );

        return $com_enviada->getRadicado();
    }

    /**
     * Prestamo no tiene tabla de roles/bitacora (es una sola tabla): se mantiene el
     * traspaso simple del propietario, con auditoria generica.
     */
    private static function reasignarPrestamo(Prestamo $registro, Usuario $jefe)
    {
        $anterior = clone $registro;
        $registro->setUsuarioId($jefe->getUsuarioId());
        $registro->save();
        //*********************************************************************************
        AuditLogPeer::guardarAuditoriaLite(
            'Prestamo',
            $anterior,
            $registro,
            ModulesEnable::Archivo,
            $registro->getConsecutivoRegional() ? $registro->getConsecutivoRegional() : $registro->getPrimaryKey(),
            self::USUARIO_SISTEMA_INTEGRACION_ID
        );

        return $registro->getConsecutivoRegional() ? $registro->getConsecutivoRegional() : $registro->getNumeroRadicacion();
    }

    private static function enviarCorreoResumenJefe(Usuario $jefe, Usuario $usuarioDeshabilitado, $resumen)
    {
        if (!trim($jefe->getEmail())) {
            return;
        }

        $filas = '';
        foreach ($resumen as $bandeja_key => $detalle) {
            $radicados = !empty($detalle['radicados']) ? implode(', ', $detalle['radicados']) : '-';
            $filas .= sprintf(
                '<tr><td style="padding:6px 10px;border:1px solid #ddd;">%s</td><td style="padding:6px 10px;border:1px solid #ddd;text-align:center;">%d</td><td style="padding:6px 10px;border:1px solid #ddd;">%s</td></tr>',
                htmlspecialchars(isset(self::$etiquetasBandeja[$bandeja_key]) ? self::$etiquetasBandeja[$bandeja_key] : $bandeja_key),
                $detalle['cantidad'],
                htmlspecialchars($radicados)
            );
        }
        //*********************************************************************************
        $dependencia = $usuarioDeshabilitado->getDependencia() ? $usuarioDeshabilitado->getDependencia()->getNombre() : 'N/D';
        $cuerpo = '
        <html><body style="font-family:Calibri,Arial,sans-serif;color:#222222;">
        '.self::getLogoCorporativoHtml($usuarioDeshabilitado).'
        <h3 style="color:#1b3d6d;margin-bottom:4px;">Reasignacion de actividades pendientes</h3>
        <p>Estimado(a) '.htmlspecialchars(trim($jefe->getNombre().' '.$jefe->getApellido())).',</p>
        <p>El usuario <b>'.htmlspecialchars(trim($usuarioDeshabilitado->getNombre().' '.$usuarioDeshabilitado->getApellido())).' ('.htmlspecialchars($usuarioDeshabilitado->getUserName()).')</b>
        de la dependencia <b>'.htmlspecialchars($dependencia).'</b> fue deshabilitado en el sistema el <b>'.date('Y-m-d H:i:s').'</b>,
        al detectarse inactivo en el Directorio Activo.</p>
        <p>Como jefe de esta dependencia, las siguientes actividades pendientes fueron reasignadas automaticamente a su usuario
        y a partir de ahora estan bajo su responsabilidad:</p>
        <table style="border-collapse:collapse;width:100%;font-size:13px;">
            <tr style="background:#1b3d6d;color:#ffffff;">
                <th style="padding:6px 10px;border:1px solid #ddd;text-align:left;">Bandeja</th>
                <th style="padding:6px 10px;border:1px solid #ddd;">Cantidad</th>
                <th style="padding:6px 10px;border:1px solid #ddd;text-align:left;">Radicados</th>
            </tr>
            '.$filas.'
        </table>
        <p style="margin-top:16px;">Por favor verifique sus bandejas de trabajo en SGDEA ArchiDhu para dar continuidad a estas actividades.</p>
        </body></html>';
        //*********************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('SGDEA ArchiDhu: Reasignacion de actividades pendientes - usuario deshabilitado');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($jefe->getEmail(), $jefe->getEmail());
        if ($baseMail->InitSend() === true) {
            $baseMail->writetolog('Reasignacion de pendientes notificada al jefe: '.$jefe->getUsuarioId().' por deshabilitacion de: '.$usuarioDeshabilitado->getUsuarioId());
        } else {
            $baseMail->writetolog('Error notificando reasignacion de pendientes al jefe: '.$jefe->getUsuarioId());
        }
    }

    private static function enviarCorreoNovedadSinJefe(Usuario $user)
    {
        $config = simad_util::readConfigFileApp(array('email_novedad_jefe'));
        $destinatarios = array();
        if (!empty($config['email_novedad_jefe'])) {
            $destinatarios = preg_split("/[,]+/", $config['email_novedad_jefe'], -1, PREG_SPLIT_NO_EMPTY);
        }
        if (empty($destinatarios)) {
            return;
        }
        //*********************************************************************************
        $dependencia = $user->getDependencia() ? $user->getDependencia()->getNombre() : 'N/D';
        $cuerpo = '
        <html><body style="font-family:Calibri,Arial,sans-serif;color:#222222;">
        <h3 style="color:#a83232;margin-bottom:4px;">Novedad: usuario deshabilitado sin jefe de dependencia configurado</h3>
        <p>El usuario <b>'.htmlspecialchars(trim($user->getNombre().' '.$user->getApellido())).' ('.htmlspecialchars($user->getUserName()).')</b>
        de la dependencia <b>'.htmlspecialchars($dependencia).'</b> fue deshabilitado automaticamente el <b>'.date('Y-m-d H:i:s').'</b>,
        al detectarse inactivo en el Directorio Activo.</p>
        <p>Este usuario presenta actividades pendientes en el sistema, pero no fue posible reasignarlas automaticamente porque
        no hay un jefe de dependencia (Activo o Bloqueado) configurado para <b>'.htmlspecialchars($dependencia).'</b>.</p>
        <p>Las actividades pendientes quedan sin reasignar y deben gestionarse manualmente.</p>
        </body></html>';
        //*********************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('SGDEA ArchiDhu: Novedad - usuario deshabilitado sin jefe configurado');
        $baseMail->SetMsgHTML($cuerpo);
        foreach ($destinatarios as $correo) {
            $correo = trim($correo);
            if ($correo) {
                $baseMail->SetAddAddress($correo, $correo);
            }
        }
        if ($baseMail->InitSend() === true) {
            $baseMail->writetolog('Novedad sin jefe enviada por deshabilitacion de: '.$user->getUsuarioId());
        } else {
            $baseMail->writetolog('Error enviando novedad sin jefe por deshabilitacion de: '.$user->getUsuarioId());
        }
    }

    private static function getLogoCorporativoHtml(Usuario $user)
    {
        try {
            $entidad = $user->getRegional() ? $user->getRegional()->getEntidad() : null;
            $logo = $entidad ? trim($entidad->getLogoCorporativo()) : '';
            $base_url = rtrim(sfConfig::get('publicUrl'), '/');
            $src = $logo
                ? $base_url.'/images/encabezado_carta/logos_carnet/'.$logo
                : $base_url.'/images/lincenciadoa.jpg';

            return '<div style="text-align:center;margin-bottom:16px;"><img src="'.$src.'" alt="Logo" style="max-height:80px;"></div>';
        } catch (Exception $ex) {
            return '';
        }
    }

    private static function bucketDefinitions($usuario_id)
    {
        return array(
            'com_recibida' => array(
                'peer' => 'ComrecibidaUsuarioPeer',
                'criterios' => array(
                    self::criteriaRecibidaPorLeer($usuario_id),
                    self::criteriaRecibidaVencidas($usuario_id),
                    self::criteriaRecibidaPorVencer($usuario_id),
                    self::criteriaRecibidaPorResponder($usuario_id),
                    self::criteriaRecibidaPorDistribuir($usuario_id),
                    self::criteriaRecibidaPorGestionar($usuario_id),
                ),
            ),
            'com_interna' => array(
                'peer' => 'CominternaUsuarioPeer',
                'criterios' => array(
                    self::criteriaInternaPorLeer($usuario_id),
                    self::criteriaInternaPorResponder($usuario_id),
                ),
            ),
            'com_enviada' => array(
                'peer' => 'EnviadaUsuarioPeer',
                'criterios' => array(
                    self::criteriaEnviadaPorGestionar($usuario_id),
                ),
            ),
            'prestamo' => array(
                'peer' => 'PrestamoPeer',
                'criterios' => array(
                    self::criteriaPrestamoPendiente($usuario_id),
                ),
            ),
        );
    }

    // ---- Comunicaciones Externas Recibidas -------------------------------------------------

    private static function criteriaRecibidaPorLeer($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 1);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);

        return $c;
    }

    private static function criteriaRecibidaVencidas($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 12, Criteria::NOT_EQUAL);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 5, Criteria::NOT_EQUAL);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION, 0);
        $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, date('Y-m-d 23:59:59'), Criteria::LESS_THAN);

        return $c;
    }

    private static function criteriaRecibidaPorVencer($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION, 0);
        $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, date('Y-m-d', strtotime('+3 day')), Criteria::LESS_THAN);
        $c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, date('Y-m-d 00:00:00'), Criteria::GREATER_THAN);

        return $c;
    }

    private static function criteriaRecibidaPorResponder($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 4);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 6);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION, 0);

        return $c;
    }

    private static function criteriaRecibidaPorDistribuir($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID, 2);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION, 0);

        return $c;
    }

    private static function criteriaRecibidaPorGestionar($usuario_id)
    {
        $c = new Criteria();
        $c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 5, Criteria::NOT_EQUAL);
        $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID, 3);
        $c->add(ComRecibidaPeer::IS_LOCKED, 0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION, 0);

        return $c;
    }

    // ---- Comunicaciones Internas -------------------------------------------------------------

    private static function criteriaInternaPorLeer($usuario_id)
    {
        $c = new Criteria();
        $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID, 2);

        return $c;
    }

    private static function criteriaInternaPorResponder($usuario_id)
    {
        $c = new Criteria();
        $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID, 5);

        return $c;
    }

    // ---- Comunicaciones Externas Enviadas ----------------------------------------------------

    private static function criteriaEnviadaPorGestionar($usuario_id)
    {
        $c = new Criteria();
        $c->add(EnviadaUsuarioPeer::USUARIO_ID, $usuario_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 5);
        $c->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID, 1);
        $c->add(EnviadaUsuarioPeer::TIPOPROCESOCOM_ID, 3);
        $c->add(EnviadaUsuarioPeer::ESTA_ASIGNADA, 1);

        return $c;
    }

    // ---- Prestamos ----------------------------------------------------------------------------

    private static function criteriaPrestamoPendiente($usuario_id)
    {
        $c = new Criteria();
        $c->setDistinct();
        $c->addJoin(PrestamoPeer::PRESTAMO_ID, DetallePrestamoPeer::PRESTAMO_ID);
        $c->add(PrestamoPeer::USUARIO_ID, $usuario_id);
        $c->add(PrestamoPeer::ESTADOPRESTAMO_ID, 1);

        return $c;
    }
}
