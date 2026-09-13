<?php

/**
 * Subclass for representing a row from the 'MARC' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Marc extends BaseMarc
{
	public function __toString()
    {
		return  $this->getCodigo().'-'.$this->getDescripcion();
	}
}
