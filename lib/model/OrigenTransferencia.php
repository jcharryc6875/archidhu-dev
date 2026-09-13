<?php

/**
 * Subclass for representing a row from the 'ORIGEN_TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class OrigenTransferencia extends BaseOrigenTransferencia
{
   public function __toString()
    {
		return $this->getDescripcion();
	} 
}
