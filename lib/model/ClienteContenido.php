<?php

/**
 * Subclass for representing a row from the 'CLIENTE_CONTENIDO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ClienteContenido extends BaseClienteContenido
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
