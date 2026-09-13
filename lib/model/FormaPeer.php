<?php

/**
 * Subclass for performing query and update operations on the 'forma' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FormaPeer extends BaseFormaPeer
{
	public static function getOrdenForma()
	{
		$c = new Criteria();
        	$c->addJoin(FormaPeer::MODULO_ID,ModuloPeer::MODULO_ID);        
    		$c->addAscendingOrderByColumn(ModuloPeer::DESCRIPCION);
		$rs = FormaPeer::doSelect($c);
		return $rs; 	
	}
}

