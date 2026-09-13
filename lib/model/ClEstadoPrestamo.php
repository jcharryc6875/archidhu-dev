<?php

/**
 * Subclass for representing a row from the 'CL_ESTADO_PRESTAMO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClEstadoPrestamo extends BaseClEstadoPrestamo
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
