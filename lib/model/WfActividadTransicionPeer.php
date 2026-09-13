<?php

/**
 * Subclass for performing query and update operations on the 'WF_ACTIVIDAD_TRANSICION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfActividadTransicionPeer extends BaseWfActividadTransicionPeer
{
	static public function getActByTransicionData($wfat_id,$wflujo_id,$orden,$es_destino=null)
	{
		$c = new Criteria();		
		$c->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$wfat_id);
        $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wflujo_id);
        $c->add(WfActividadTransicionPeer::ORDEN,$orden);		
        if($es_destino == 1){
            $c->add(WfActividadTransicionPeer::ES_DESTINO,1);
            $rs = WfActividadTransicionPeer::doSelectOne($c);
        }elseif($es_destino==0){
            $c->add(WfActividadTransicionPeer::ES_DESTINO,0);
            $rs = WfActividadTransicionPeer::doSelectOne($c);            
        }else{
            $rs = WfActividadTransicionPeer::doSelect($c);
        }
		return $rs;
	}
}
