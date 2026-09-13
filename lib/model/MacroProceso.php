<?php

/**
 * Subclass for representing a row from the 'MACRO_PROCESO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MacroProceso extends BaseMacroProceso
{
    function __toString(){		
		return $this->getDescripcion();
	}
}
