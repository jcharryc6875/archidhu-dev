<?php

/**
 * Subclass for representing a row from the 'forma' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Forma extends BaseForma
{
	public function __toString()
    {
		return  $this->getModulo()->getDescripcion()." - ".$this->getDescripcion();
	} 
	function getNombreModulo(){
		
		return $this->getModulo()->getDescripcion();
	}
}
