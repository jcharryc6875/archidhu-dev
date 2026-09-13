<?php

/**
 * Subclass for representing a row from the 'ESTADO_REDIRECCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoRedireccion extends BaseEstadoRedireccion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
