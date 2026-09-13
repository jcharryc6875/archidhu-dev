<?php

/**
 * Subclass for representing a row from the 'ciudad' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Ciudad extends BaseCiudad
{
    function __toString(){
		return $this->getNombre()." ".$this->getCodigoDane();		
	}
}
