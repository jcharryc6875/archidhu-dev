<?php

/**
 * Subclass for representing a row from the 'modulo' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Modulo extends BaseModulo
{
    function __toString(){		
		return $this->getDescripcion();
	}

}
