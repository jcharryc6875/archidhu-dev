<?php

/**
 * Subclass for representing a row from the 'TIPO_CONTRATO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoContrato extends BaseTipoContrato
{
    function __toString(){
		return $this->getDescripcion();
	}
}
