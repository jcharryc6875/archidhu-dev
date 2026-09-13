<?php

/**
 * Subclass for representing a row from the 'factura_estado' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaEstado extends BaseFacturaEstado
{
	public function __toString(){
		return $this->getDescripcion() ;
	}

}
