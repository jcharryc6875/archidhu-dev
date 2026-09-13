<?php

/**
 * Subclass for representing a row from the 'PROV_APROBACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvAprobacion extends BaseProvAprobacion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
