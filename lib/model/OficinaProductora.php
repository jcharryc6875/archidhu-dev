<?php

/**
 * Subclass for representing a row from the 'OFICINA_PRODUCTORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class OficinaProductora extends BaseOficinaProductora
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
