<?php

/**
 * Subclass for representing a row from the 'ESTADO_COM_APROBACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoComAprobacion extends BaseEstadoComAprobacion
{
    function __toString(){
		return $this->getDescripcion();
	}
}
