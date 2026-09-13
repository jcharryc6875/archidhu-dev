<?php

/**
 * Subclass for performing query and update operations on the 'cargo' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CargoPeer extends BaseCargoPeer
{
	public static function getOrdenCargo()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(CargoPeer::DESCRIPCION);		
		$rs = CargoPeer::doSelect($c);
		return $rs; 	
	}
}
