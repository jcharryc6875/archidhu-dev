<?php

/**
 * Subclass for representing a row from the 'estado_usuario' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoUsuario extends BaseEstadoUsuario
{
	public function __toString()
    {
		return $this->getDescripcion();
	} 
}
