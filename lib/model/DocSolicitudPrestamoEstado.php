<?php

/**
 * Subclass for representing a row from the 'DOC_SOLICITUD_PRESTAMO_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DocSolicitudPrestamoEstado extends BaseDocSolicitudPrestamoEstado
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
