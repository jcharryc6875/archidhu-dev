<?php

/**
 * Subclass for representing a row from the 'factura_estado_pago' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaEstadoPago extends BaseFacturaEstadoPago
{
		
   function __toString(){
		
		return $this->getDescripcion();
	}

}
