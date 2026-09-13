<?php

/**
 * Subclass for representing a row from the 'CL_ESTADO_TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClEstadoTransferencia extends BaseClEstadoTransferencia
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
