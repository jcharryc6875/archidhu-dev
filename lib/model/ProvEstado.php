<?php

/**
 * Subclass for representing a row from the 'PROV_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstado extends BaseProvEstado
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
