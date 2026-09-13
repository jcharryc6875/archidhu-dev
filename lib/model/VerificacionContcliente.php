<?php

/**
 * Subclass for representing a row from the 'VERIFICACION_CONTCLIENTE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class VerificacionContcliente extends BaseVerificacionContcliente
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
