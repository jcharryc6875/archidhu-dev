<?php

/**
 * Skeleton subclass for performing query and update operations on the 'ACTOADMIN_ETAPA_ACTO_CONFIG' table.
 *
 * Override, por acto administrativo puntual, de si una etapa permite edición
 * (UARIV-202605, ampliación). Si no hay fila para un acto+etapa, se hereda
 * ACTOADMIN_ETAPA.PERMITE_EDICION (valor global).
 *
 * @package    propel.generator.lib.model
 */
class ActoadminEtapaActoConfigPeer extends BaseActoadminEtapaActoConfigPeer
{
    public static function getOverride($actoadministrativo_id, $actoadminetapa_id)
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminEtapaActoConfigPeer::ACTOADMINISTRATIVO_ID,$actoadministrativo_id);
            $c->add(ActoadminEtapaActoConfigPeer::ACTOADMINETAPA_ID,$actoadminetapa_id);
            return ActoadminEtapaActoConfigPeer::doSelectOne($c);
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        }
    }

    public static function getOverridesByActoId($actoadministrativo_id)
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminEtapaActoConfigPeer::ACTOADMINISTRATIVO_ID,$actoadministrativo_id);
            $lista = array();
            foreach (ActoadminEtapaActoConfigPeer::doSelect($c) as $override) {
                $lista[$override->getActoadminetapaId()] = $override;
            }
            return $lista;
        } catch (PropelException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        }
    }

    /**
     * Crea o actualiza el override de un acto+etapa. $permite_edicion en null borra el override
     * (vuelve a heredar el valor global de la etapa).
     */
    public static function setOverride($actoadministrativo_id, $actoadminetapa_id, $permite_edicion, $usuario_id)
    {
        try {
            $override = ActoadminEtapaActoConfigPeer::getOverride($actoadministrativo_id,$actoadminetapa_id);
            //*********************************************************************************************
            if($permite_edicion === null){
                if($override != null){ $override->delete(); }
                return null;
            }
            //*********************************************************************************************
            if($override == null){
                $override = new ActoadminEtapaActoConfig();
                $override->setActoadministrativoId($actoadministrativo_id);
                $override->setActoadminetapaId($actoadminetapa_id);
            }
            $override->setPermiteEdicion($permite_edicion ? 1 : 0);
            $override->setUsuarioId($usuario_id);
            $override->setFechaModificacion(date('Y-m-d G:i:s'));
            $override->save();
            //*********************************************************************************************
            return $override;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        }
    }
}
