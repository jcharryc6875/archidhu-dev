<?php

/**
 * Subclass for representing a row from the 'periodo' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Periodo extends BasePeriodo
{
 function __toString(){
		
		return $this->getDescripcion();
	}
}
