<?php

/**
 * Subclass for representing a row from the 'PROV_TIPO_RETENCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvTipoRetencion extends BaseProvTipoRetencion
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
