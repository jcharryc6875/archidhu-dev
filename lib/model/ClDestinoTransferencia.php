<?php

/**
 * Subclass for representing a row from the 'CL_DESTINO_TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClDestinoTransferencia extends BaseClDestinoTransferencia
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
