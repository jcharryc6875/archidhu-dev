<?php

/**
 * Subclass for representing a row from the 'tipo_com_interna' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoComInterna extends BaseTipoComInterna
{
	function __toString(){		
		return $this->getDescripcion();
	}
}
