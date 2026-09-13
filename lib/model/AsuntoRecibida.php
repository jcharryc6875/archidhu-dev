<?php

/**
 * Subclass for representing a row from the 'asunto_recibida' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AsuntoRecibida extends BaseAsuntoRecibida
{
	function __toString(){
		return $this->getDescripcion();
		
	}
}
