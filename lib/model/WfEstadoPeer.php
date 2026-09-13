<?php

/**
 * Subclass for performing query and update operations on the 'wf_estado' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfEstadoPeer extends BaseWfEstadoPeer
{
	static public function getOrdenEstado(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(WfEstadoPeer::DESCRIPCION);
		$rs = WfEstadoPeer::doSelect($c);
		return $rs; 	
	}
}
