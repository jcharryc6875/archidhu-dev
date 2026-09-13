<?php

/**
 * Subclass for performing query and update operations on the 'wf_transicion' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfTransicionPeer extends BaseWfTransicionPeer
{
	static public function getOrdenTransiciones(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(WfTransicionPeer::DESCRIPCION);
		$rs = WfTransicionPeer::doSelect($c);
		return $rs; 	
	}
}
