<?php

/**
 * Skeleton subclass for performing query and update operations on the 'ACTOADMIN_ETAPA' table.
 *
 * Etapas configurables del flujo de aprobación de Actos Administrativos (UARIV-202605).
 *
 * @package    propel.generator.lib.model
 */
class ActoadminEtapaPeer extends BaseActoadminEtapaPeer
{
    /**
     * Etapas activas ordenadas por ORDEN ascendente: es la cadena del flujo configurado.
     */
    public static function getEtapasActivasOrdenadas()
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminEtapaPeer::ESTA_ACTIVO, 1);
            $c->addAscendingOrderByColumn(ActoadminEtapaPeer::ORDEN);
            return ActoadminEtapaPeer::doSelect($c);
        } catch (PropelException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        }
    }

    /**
     * Primera etapa activa configurada para un tipo de participante (rol), la de menor ORDEN.
     */
    public static function getEtapaByRol($rol_id)
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminEtapaPeer::ROLUSUARIOACTOADMINISTVO_ID, $rol_id);
            $c->add(ActoadminEtapaPeer::ESTA_ACTIVO, 1);
            $c->addAscendingOrderByColumn(ActoadminEtapaPeer::ORDEN);
            return ActoadminEtapaPeer::doSelectOne($c);
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        }
    }

    /**
     * Rol (tipo de participante) de la etapa activa inmediatamente posterior a la del rol dado,
     * siguiendo el orden configurado. Retorna null si el rol dado corresponde a la última etapa
     * del flujo (o si no está configurado ninguna etapa).
     */
    public static function getSiguienteRolEtapa($rol_actual_id)
    {
        $etapas = ActoadminEtapaPeer::getEtapasActivasOrdenadas();
        $encontrada = false;
        foreach ($etapas as $etapa) {
            if ($encontrada) {
                return $etapa->getRolusuarioactoadministvoId();
            }
            if ($etapa->getRolusuarioactoadministvoId() == $rol_actual_id) {
                $encontrada = true;
            }
        }
        return null;
    }

    /**
     * Rol de la primera etapa activa configurada (inicio del flujo), o null si no hay etapas.
     */
    public static function getPrimeraEtapaRol()
    {
        $etapas = ActoadminEtapaPeer::getEtapasActivasOrdenadas();
        return count($etapas) ? $etapas[0]->getRolusuarioactoadministvoId() : null;
    }

    /**
     * Rol de la última etapa activa configurada (fin del flujo), o null si no hay etapas.
     */
    public static function getUltimaEtapaRol()
    {
        $etapas = ActoadminEtapaPeer::getEtapasActivasOrdenadas();
        return count($etapas) ? $etapas[count($etapas) - 1]->getRolusuarioactoadministvoId() : null;
    }
}
