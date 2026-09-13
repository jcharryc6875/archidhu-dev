<?php

/**
 * Subclass for representing a row from the 'forma_recepcion' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FormaRecepcion extends BaseFormaRecepcion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
