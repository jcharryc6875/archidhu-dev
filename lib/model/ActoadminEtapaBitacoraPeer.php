<?php

/**
 * Skeleton subclass for performing query and update operations on the 'ACTOADMIN_ETAPA_BITACORA' table.
 *
 * Histórico de transiciones del flujo de aprobación de Actos Administrativos (UARIV-202605).
 *
 * @package    propel.generator.lib.model
 */
class ActoadminEtapaBitacoraPeer extends BaseActoadminEtapaBitacoraPeer
{
    const ACCION_CREACION = 'CREACION';
    const ACCION_APROBACION = 'APROBACION';
    const ACCION_RECHAZO = 'RECHAZO';
    const ACCION_DEVOLUCION = 'DEVOLUCION';
    const ACCION_SOLICITUD_FIRMA = 'SOLICITUD_FIRMA';
    const ACCION_FIRMA = 'FIRMA';
    const ACCION_RADICACION = 'RADICACION';
    const ACCION_FINALIZACION = 'FINALIZACION';
    const ACCION_PURGA_VERSIONES = 'PURGA_VERSIONES';

    public static function addBitacora($actoadministrativo_id, $actoadminetapa_id, $usuario_id, $rol_id, $estado_id, $accion, $observacion = null)
    {
        try {
            if (empty($actoadministrativo_id) || empty($actoadminetapa_id) || empty($usuario_id) || empty($accion)) {
                return null;
            }
            //**************************************************************************************************
            $bitacora = new ActoadminEtapaBitacora();
            $bitacora->setActoadministrativoId($actoadministrativo_id);
            $bitacora->setActoadminetapaId($actoadminetapa_id);
            $bitacora->setUsuarioId($usuario_id);
            $bitacora->setRolusuarioactoadministvoId($rol_id);
            $bitacora->setEstadoactoadministrativoId($estado_id);
            $bitacora->setAccion($accion);
            $bitacora->setObservacion($observacion ? trim($observacion) : null);
            $bitacora->setFechaAccion(date('Y-m-d G:i:s'));
            $bitacora->save();
            //**************************************************************************************************
            return $bitacora;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getListByActoId($actoadministrativo_id)
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminEtapaBitacoraPeer::ACTOADMINISTRATIVO_ID, $actoadministrativo_id);
            $c->addDescendingOrderByColumn(ActoadminEtapaBitacoraPeer::FECHA_ACCION);
            return ActoadminEtapaBitacoraPeer::doSelect($c);
        } catch (PropelException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        }
    }
}
