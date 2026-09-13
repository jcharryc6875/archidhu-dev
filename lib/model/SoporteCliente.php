<?php

/**
 * Subclass for representing a row from the 'SOPORTE_CLIENTE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SoporteCliente extends BaseSoporteCliente
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
