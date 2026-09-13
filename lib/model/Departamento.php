<?php

/**
 * Subclass for representing a row from the 'departamento' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Departamento extends BaseDepartamento
{
	public function __toString()
    {
		return $this->getNombre();
	} 
}
