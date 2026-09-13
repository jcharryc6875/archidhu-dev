<?php

/**
 * Subclass for representing a row from the 'estado_prestamo' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoPrestamo extends BaseEstadoPrestamo
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
