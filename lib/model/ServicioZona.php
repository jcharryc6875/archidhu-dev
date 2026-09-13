<?php

/**
 * Subclass for representing a row from the 'SERVICIO_ZONA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ServicioZona extends BaseServicioZona
{
    function __toString(){
		return $this->getDescripcion();
	}
}
