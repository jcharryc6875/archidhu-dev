<?php

/**
 * Subclass for performing query and update operations on the 'PROV_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstadoPeer extends BaseProvEstadoPeer
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
