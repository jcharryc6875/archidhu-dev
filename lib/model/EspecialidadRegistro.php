<?php

/**
 * Subclass for representing a row from the 'ESPECIALIDAD_REGISTRO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EspecialidadRegistro extends BaseEspecialidadRegistro
{
	public function __toString()
    {
		return $this->getDescripcion();
	}
}
