<?php

/**
 * Subclass for representing a row from the 'AREAS_DESCRIPCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AreasDescripcion extends BaseAreasDescripcion
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
