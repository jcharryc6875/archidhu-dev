<?php

/**
 * Subclass for representing a row from the 'contrato' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Contrato extends BaseContrato
{
	
	 function __toString(){
		
		return $this->getNumeroContrato()." - ".$this->getObjeto();
	}
	
}
