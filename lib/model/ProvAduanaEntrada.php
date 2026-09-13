<?php

/**
 * Subclass for representing a row from the 'PROV_ADUANA_ENTRADA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvAduanaEntrada extends BaseProvAduanaEntrada
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
