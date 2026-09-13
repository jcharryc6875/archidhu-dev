<?php

/**
 * Subclass for representing a row from the 'tipo_procedimiento' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoProcedimiento extends BaseTipoProcedimiento
{
	function __toString(){		
		return $this->getDescripcion();
	}
}
