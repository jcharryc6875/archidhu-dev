<?php

/**
 * Subclass for representing a row from the 'PROV_GRUPO_TESORERIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvGrupoTesoreria extends BaseProvGrupoTesoreria
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
