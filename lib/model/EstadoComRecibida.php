<?php

/**
 * Subclass for representing a row from the 'estado_com_recibida' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoComRecibida extends BaseEstadoComRecibida
{
	function __toString(){
		
		return $this->getDescripcion();
	}
}
