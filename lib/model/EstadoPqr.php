<?php

/**
 * Subclass for representing a row from the 'estado_pqr' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoPqr extends BaseEstadoPqr
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
