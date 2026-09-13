<?php

/**
 * Subclass for representing a row from the 'proveedor' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Proveedor extends BaseProveedor
{   
	public function __toString(){
		return $this->getNombre()." ".$this->getNit() ;
	}
}
