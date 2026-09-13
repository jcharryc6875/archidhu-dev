<?php

/**
 * Subclass for representing a row from the 'CL_ORIGEN_TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClOrigenTransferencia extends BaseClOrigenTransferencia
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
