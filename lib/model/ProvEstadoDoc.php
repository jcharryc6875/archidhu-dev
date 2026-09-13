<?php

/**
 * Subclass for representing a row from the 'PROV_ESTADO_DOC' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstadoDoc extends BaseProvEstadoDoc
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
