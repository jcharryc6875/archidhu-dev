<?php

/**
 * Subclass for representing a row from the 'UNIDCONSERVADORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Unidconservadora extends BaseUnidconservadora
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
