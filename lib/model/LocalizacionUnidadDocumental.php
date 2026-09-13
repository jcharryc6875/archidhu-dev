<?php

/**
 * Subclass for representing a row from the 'localizacion_unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class LocalizacionUnidadDocumental extends BaseLocalizacionUnidadDocumental
{
	public function __toString(){
		return self::getDESCRIPCION();
	}
}
