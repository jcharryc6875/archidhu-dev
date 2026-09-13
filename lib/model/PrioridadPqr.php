<?php

/**
 * Subclass for representing a row from the 'prioridad_pqr' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PrioridadPqr extends BasePrioridadPqr
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
