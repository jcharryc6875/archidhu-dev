<?php

/**
 * Subclass for representing a row from the 'DOC_ESTADO_PRESTAMO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DocEstadoPrestamo extends BaseDocEstadoPrestamo
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
