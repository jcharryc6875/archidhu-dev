<?php

/**
 * Subclass for representing a row from the 'verificacion_cont_unidad_doc' table.
 *
 * 
 *
 * @package lib.model
 */ 
class VerificacionContUnidadDoc extends BaseVerificacionContUnidadDoc
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
