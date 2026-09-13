<?php

/**
 * Subclass for representing a row from the 'ESPECIALIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Especialidad extends BaseEspecialidad
{
	public function __toString()
    {
		return $this->getDescripcion();
	}
}
