<?php

/**
 * Subclass for representing a row from the 'CL_ESTADO_CONTENIDO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClEstadoContenido extends BaseClEstadoContenido
{
	function __toString(){
		return $this->getDescripcion();		
	}
}
