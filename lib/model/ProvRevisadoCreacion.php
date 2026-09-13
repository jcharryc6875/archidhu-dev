<?php

/**
 * Subclass for representing a row from the 'PROV_REVISADO_CREACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvRevisadoCreacion extends BaseProvRevisadoCreacion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
