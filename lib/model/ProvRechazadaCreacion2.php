<?php

/**
 * Subclass for representing a row from the 'PROV_RECHAZADA_CREACION2' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvRechazadaCreacion2 extends BaseProvRechazadaCreacion2
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
