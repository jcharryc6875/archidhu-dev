<?php

/**
 * Subclass for performing query and update operations on the 'contrato' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ContratoPeer extends BaseContratoPeer
{
	
	public function __toString(){
	return $this->getNumeroContrato()." ".$this->getObjeto() ;
	}
	
}
