<?php

/**
 * Subclass for representing a row from the 'ESPECIALIDAD_PLANO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EspecialidadPlano extends BaseEspecialidadPlano
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
