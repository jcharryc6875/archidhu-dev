<?php

/**
 * Subclass for representing a row from the 'soporte_unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SoporteUnidadDocumental extends BaseSoporteUnidadDocumental
{
	public function __toString(){
		return self::getDESCRIPCION();
	}
}
