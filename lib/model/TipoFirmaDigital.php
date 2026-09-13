<?php

/**
 * Subclass for representing a row from the 'TIPO_FIRMA_DIGITAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoFirmaDigital extends BaseTipoFirmaDigital
{
    function __toString(){		
		return $this->getDescripcion();
	}
}
