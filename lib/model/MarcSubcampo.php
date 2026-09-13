<?php

/**
 * Subclass for representing a row from the 'MARC_SUBCAMPO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MarcSubcampo extends BaseMarcSubcampo
{
	public function __toString()
    {
		return $this->getDescripcion();
	}
	
	public function getCodDescripcion(){
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
}
