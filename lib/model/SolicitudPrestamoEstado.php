<?php

/**
 * Subclass for representing a row from the 'solicitud_prestamo_estado' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SolicitudPrestamoEstado extends BaseSolicitudPrestamoEstado
{
	function __toString(){		
		return $this->getDescripcion();
	}
}
