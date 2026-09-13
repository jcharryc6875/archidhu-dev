<?php

/**
 * Subclass for representing a row from the 'TIPO_CONTROL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoControl extends BaseTipoControl
{
    function __toString(){
		
		return $this->getDescripcion();
	}
}
