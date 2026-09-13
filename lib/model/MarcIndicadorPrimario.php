<?php

/**
 * Subclass for representing a row from the 'MARC_INDICADOR_PRIMARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MarcIndicadorPrimario extends BaseMarcIndicadorPrimario
{
	public function __toString()
    {
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
	
	public function getCodDescripcion(){
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
}
