<?php

/**
 * Subclass for representing a row from the 'CL_SOLICITUD_PRESTAMO_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClSolicitudPrestamoEstado extends BaseClSolicitudPrestamoEstado
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
