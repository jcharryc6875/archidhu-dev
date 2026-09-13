<?php

/**
 * Subclass for representing a row from the 'CLIENTE_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClienteEstado extends BaseClienteEstado
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
