<?php

/**
 * Subclass for representing a row from the 'FREC_CONSULTA_CLIENTE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FrecConsultaCliente extends BaseFrecConsultaCliente
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
