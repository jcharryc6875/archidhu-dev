<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_IMPUESTO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoImpuestoPeer extends BaseTipoImpuestoPeer
{
    public static function getTipoImpuestoOrdenado(){
    	$c = new Criteria();
    	$c->addAscendingOrderByColumn(TipoImpuestoPeer::DESCRIPCION);
    	$rs = TipoImpuestoPeer::doSelect($c);
    	return $rs; 	
	}
}
