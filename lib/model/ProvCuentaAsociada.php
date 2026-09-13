<?php

/**
 * Subclass for representing a row from the 'PROV_CUENTA_ASOCIADA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvCuentaAsociada extends BaseProvCuentaAsociada
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
