<?php

/**
 * Subclass for representing a row from the 'ESTADO_CONTRATISTA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoContratista extends BaseEstadoContratista
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
