<?php

/**
 * Subclass for representing a row from the 'categoria_pqr' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CategoriaPqr extends BaseCategoriaPqr
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
