<?php

/**
 * Subclass for representing a row from the 'serie' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Serie extends BaseSerie
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
