<?php

/**
 * Subclass for representing a row from the 'NIVEL_DESCRIPCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class NivelDescripcion extends BaseNivelDescripcion
{
	function __toString(){		
		return $this->getDescripcion();
	}
}
