<?php

/**
 * Subclass for representing a row from the 'PROV_CONDICION_PAGO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvCondicionPago extends BaseProvCondicionPago
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getDescripcion();
	}
}
