<?php

/**
 * Subclass for representing a row from the 'PROV_TIPO_CONTRIBUYENTE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvTipoContribuyente extends BaseProvTipoContribuyente
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
