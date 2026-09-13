<?php

/**
 * Subclass for representing a row from the 'PROV_RECHAZADA_CREACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvRechazadaCreacion extends BaseProvRechazadaCreacion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
