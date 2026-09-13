<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_CONTRATO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoContratoPeer extends BaseTipoContratoPeer
{
    static public function getTipoContratoOrder()
    {
		$c = new Criteria();
		$c->add(TipoContratoPeer::ES_VISIBLE,1);
		$c->addAscendingOrderByColumn(TipoContratoPeer::DESCRIPCION);
		$rs = TipoContratoPeer::doSelect($c);
		return $rs; 	
	}
}
