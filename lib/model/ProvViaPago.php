<?php

/**
 * Subclass for representing a row from the 'PROV_VIA_PAGO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvViaPago extends BaseProvViaPago
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
