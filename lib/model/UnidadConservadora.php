<?php

/**
 * Subclass for representing a row from the 'unidad_conservadora' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UnidadConservadora extends BaseUnidadConservadora
{
	public function __toString(){
		return self::getDESCRIPCION();
	}
}
