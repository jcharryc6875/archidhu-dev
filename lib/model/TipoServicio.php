<?php

/**
 * Subclass for representing a row from the 'tipo_servicio' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoServicio extends BaseTipoServicio
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
