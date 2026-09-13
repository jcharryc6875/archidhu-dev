<?php

/**
 * Subclass for representing a row from the 'PROCESOS' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Procesos extends BaseProcesos
{
    function __toString(){		
		return $this->getDescripcion();
	}
}
