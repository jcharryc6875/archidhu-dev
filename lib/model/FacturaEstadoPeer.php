<?php

/**
 * Subclass for performing query and update operations on the 'FACTURA_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaEstadoPeer extends BaseFacturaEstadoPeer
{
	static public function getFacturaEstadoAsc(){
		$c = new Criteria();      
		$c->addAscendingOrderByColumn(FacturaEstadoPeer::DESCRIPCION);
		$rs = FacturaEstadoPeer::doSelect($c);
		return $rs; 	
	}
}
