<?php

/**
 * Subclass for representing a row from the 'MARC_INDICADOR_SECUNDARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MarcIndicadorSecundario extends BaseMarcIndicadorSecundario
{
	public function __toString()
    {
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
	
	public function getCodDescripcion(){
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
}
