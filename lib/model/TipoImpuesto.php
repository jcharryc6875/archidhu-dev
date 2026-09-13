<?php

/**
 * Subclass for representing a row from the 'TIPO_IMPUESTO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoImpuesto extends BaseTipoImpuesto
{
    function __toString(){
		return $this->getDescripcion();
		
	}
}
