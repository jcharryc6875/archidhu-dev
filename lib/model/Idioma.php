<?php

/**
 * Subclass for representing a row from the 'IDIOMA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Idioma extends BaseIdioma
{
	function __toString(){
		
		return $this->getDescripcion();
	}
}
