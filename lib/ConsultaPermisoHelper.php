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
}
