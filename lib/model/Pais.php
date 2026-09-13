<?php

/**
 * Subclass for representing a row from the 'pais' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Pais extends BasePais
{
	public function __toString()
    {
		return $this->getNombre();
	} 
}
