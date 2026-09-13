<?php

/**
 * Subclass for representing a row from the 'FORMATO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Formato extends BaseFormato
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
