<?php

/**
 * Subclass for representing a row from the 'ESTADO_TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoTransferencia extends BaseEstadoTransferencia
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
