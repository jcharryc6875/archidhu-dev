<?php

/**
 * Subclass for representing a row from the 'estado_contrato' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoContrato extends BaseEstadoContrato
{	
   function __toString(){
		
		return $this->getDescripcion();
	}

	
}
