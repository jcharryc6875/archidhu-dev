<?php

/**
 * Subclass for representing a row from the 'PROV_TIPO_INDUSTRIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvTipoIndustria extends BaseProvTipoIndustria
{
	public function __toString(){
		return $this->getDescripcion()." - ".$this->getPais();
	}
}
