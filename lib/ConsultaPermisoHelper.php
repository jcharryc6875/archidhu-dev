<?php

/**
 * Fuente unica de los mensajes y la logica para distinguir, en una consulta puntual
 * (radicado/consecutivo/expediente/guia), entre "no existen registros" y "el registro
 * existe pero el usuario no tiene permiso para verlo".
 */
class ConsultaPermisoHelper
{
    const MSG_SIN_REGISTROS = 'No existen registros, intente con diferentes filtros de consulta.';
    const MSG_SIN_PERMISOS = 'El registro existe pero no cuenta con permisos para su visualización dentro del sistema.';

    /**
     * @param int $countSinPermiso resultado de un doCount() con el mismo filtro puntual
     *                             (ej. radicado) pero SIN las condiciones de permiso/alcance
     */
    public static function mensajeListaVacia($countSinPermiso)
    {
        return $countSinPermiso > 0 ? self::MSG_SIN_PERMISOS : self::MSG_SIN_REGISTROS;
    }

    public static function esMensajeSinPermiso($mensaje)
    {
        return $mensaje === self::MSG_SIN_PERMISOS;
    }

    /**
     * Bloque HTML resaltado (icono + texto en negrita) para el mensaje de restriccion,
     * reutilizable en cualquier template. Los templates que ya tenian un alert-default
     * para "sin registros" deben usar este bloque solo cuando el mensaje es MSG_SIN_PERMISOS.
     */
    public static function htmlAlertaSinPermiso($mensaje)
    {
        return '<div class="alert alert-warning" style="border-left:4px solid #f0ad4e;">'
            .'<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> '
            .'<strong>Acceso restringido:</strong> '.htmlspecialchars($mensaje)
            .'</div>';
    }

    /**
     * Cuenta, SIN ninguna condicion de permiso/alcance, si existe algun interesado que
     * coincida con los filtros de busqueda por interesado (pnombre_interesado,
     * snombre_interesado, papellido_interesado, sapellido_interesado, nuid_interesado -
     * mismos nombres de parametro en Recibida/Enviada/Archivo/Actos Administrativos/Servicios)
     * vinculado a algun registro a traves de la tabla intermedia indicada.
     *
     * @param string $joinPeerClass       Peer de la tabla intermedia (ej. 'ComrecibidaInteresadosPeer')
     * @param string $interesadoIdColumn  Columna FQ de esa tabla hacia INTERESADO_ID (ej. ComrecibidaInteresadosPeer::INTERESADO_ID)
     */
    public static function countInteresadoSinPermiso($joinPeerClass, $interesadoIdColumn)
    {
        $request = sfContext::getInstance()->getRequest();
        $pnombre = trim($request->getParameter('pnombre_interesado'));
        $snombre = trim($request->getParameter('snombre_interesado'));
        $papellido = trim($request->getParameter('papellido_interesado'));
        $sapellido = trim($request->getParameter('sapellido_interesado'));
        $nuid = trim($request->getParameter('nuid_interesado'));

        if (!$pnombre && !$snombre && !$papellido && !$sapellido && !$nuid) {
            return 0;
        }

        $c = new Criteria();
        $c->addJoin($interesadoIdColumn, InteresadosPeer::INTERESADO_ID);
        if ($pnombre) {
            $c->add(InteresadosPeer::PRIMER_NOMBRE, '%'.$pnombre.'%', Criteria::LIKE);
        }
        if ($snombre) {
            $c->add(InteresadosPeer::SEGUNDO_NOMBRE, '%'.$snombre.'%', Criteria::LIKE);
        }
        if ($papellido) {
            $c->add(InteresadosPeer::PRIMER_APELLIDO, '%'.$papellido.'%', Criteria::LIKE);
        }
        if ($sapellido) {
            $c->add(InteresadosPeer::SEGUNDO_APELLIDO, '%'.$sapellido.'%', Criteria::LIKE);
        }
        if ($nuid) {
            $c->add(InteresadosPeer::NUMERO_IDENTIFICACION, '%'.$nuid.'%', Criteria::LIKE);
        }

        return call_user_func(array($joinPeerClass, 'doCount'), $c);
    }
}
