<?php

/**
 * Subclass for representing a row from the 'PROV_GRUPO_ESQUEMA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvGrupoEsquema extends BaseProvGrupoEsquema
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
