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
}
