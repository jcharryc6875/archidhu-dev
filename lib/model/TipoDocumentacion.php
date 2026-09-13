<?php

/**
 * Subclass for representing a row from the 'TIPO_DOCUMENTACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoDocumentacion extends BaseTipoDocumentacion
{
	function __toString(){
		
		return $this->getDescripcion();
	}
}
