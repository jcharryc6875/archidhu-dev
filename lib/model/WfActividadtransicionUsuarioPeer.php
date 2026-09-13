<?php

/**
 * Subclass for performing query and update operations on the 'WF_ACTIVIDADTRANSICION_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfActividadtransicionUsuarioPeer extends BaseWfActividadtransicionUsuarioPeer
{
	public static function getUsuarioActiveMail($wfactividadtransicion_id)
    {
        $array_users = array();
        //**********************************************************************************************
        $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wfactividadtransicion_id);    
        $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );    
        //******************************Consulta para la Siguiente Actividad****************************
        $c = new Criteria();
        $c->setDistinct();
        $c->addJoin(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID,Criteria::INNER_JOIN);
        $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
        $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
        $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
        $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
        $list_data = WfActividadtransicionUsuarioPeer::doSelect($c);    
        //**********************************************************************************************
        foreach($list_data as $wfatu_item)
        {
            if($wfatu_item->getEnviarAlerta())
            {
                $array_users[] = $wfatu_item->getUsuarioId();
            }
        }
        //**********************************************************************************************
        return $array_users;
    }
}
