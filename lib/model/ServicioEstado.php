<?php

/**
 * Subclass for representing a row from the 'servicio_estado' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ServicioEstado extends BaseServicioEstado
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
