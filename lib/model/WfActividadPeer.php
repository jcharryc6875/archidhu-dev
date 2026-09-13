<?php

/**
 * Subclass for performing query and update operations on the 'WF_ACTIVIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfActividadPeer extends BaseWfActividadPeer
{
    public static function getAllWfActividades(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(WfActividadPeer::DESCRIPCION);
		$rs = WfActividadPeer::doSelect($c);
		return $rs; 	
	}
}
