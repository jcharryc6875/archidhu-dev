<?php

/**
 * Subclass for representing a row from the 'PROV_AREA_APROBADORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvAreaAprobadora extends BaseProvAreaAprobadora
{
	public function __toString(){
		return $this->getNombre();
	}
}
