<?php

/**
 * Subclass for representing a row from the 'ENTIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Entidad extends BaseEntidad
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
