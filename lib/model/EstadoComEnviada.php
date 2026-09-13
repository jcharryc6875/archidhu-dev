<?php

/**
 * Subclass for representing a row from the 'estado_com_enviada' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoComEnviada extends BaseEstadoComEnviada
{
	function __toString(){		
		return $this->getDescripcion();
	}
}
