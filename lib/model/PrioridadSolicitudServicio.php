<?php

/**
 * Subclass for representing a row from the 'prioridad_solicitud_servicio' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PrioridadSolicitudServicio extends BasePrioridadSolicitudServicio
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
