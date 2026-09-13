<?php

/**
 * Subclass for performing query and update operations on the 'OFICINA_PRODUCTORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class OficinaProductoraPeer extends BaseOficinaProductoraPeer
{
    public static function getOfProductoraByNombre($nombre = null)
    {
		if(empty(trim($nombre))) { return ""; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(OficinaProductoraPeer::DESCRIPCION,$nombre);
        $object = OficinaProductoraPeer::doSelectOne($c);
        //*******************************************************************
		return $object;
	}
}
