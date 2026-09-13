<?php

/**
 * Subclass for representing a row from the 'estado_digitalizacion' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoDigitalizacion extends BaseEstadoDigitalizacion
{
	
	 function __toString(){
		return $this->getDescripcion();
		
	}
}
