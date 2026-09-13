<?php

/**
 * Subclass for representing a row from the 'factura_proceso' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaProceso extends BaseFacturaProceso
{
	function __toString(){
		
		return $this->getDescripcion();
	}
}
