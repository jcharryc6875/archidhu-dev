<?php

/**
 * Subclass for representing a row from the 'CATEGORIA_ESCALA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CategoriaEscala extends BaseCategoriaEscala
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
