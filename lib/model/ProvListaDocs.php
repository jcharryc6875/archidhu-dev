<?php

/**
 * Subclass for representing a row from the 'PROV_LISTA_DOCS' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvListaDocs extends BaseProvListaDocs
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
