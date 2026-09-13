<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_FIRMA_DIGITAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoFirmaDigitalPeer extends BaseTipoFirmaDigitalPeer
{
	public static function getAllFirmas()
	{
		$c = new Criteria();	
		return TipoFirmaDigitalPeer::doSelect($c); 	
	}



    public static function getTipoFirmaDigitalDescripcion($la_descripcion)
	{
		if(empty(trim($la_descripcion))){ return null; }
		//***********************************************************************************
		$c = new Criteria();
		$c->add(TipoFirmaDigitalPeer::DESCRIPCION, $la_descripcion);
		return TipoFirmaDigitalPeer::doSelectOne($c);
	}
}
