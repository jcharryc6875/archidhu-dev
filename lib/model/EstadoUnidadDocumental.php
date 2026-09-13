<?php

/**
 * Subclass for representing a row from the 'estado_unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoUnidadDocumental extends BaseEstadoUnidadDocumental
{
	public function __toString(){
		return self::getDESCRIPCION();
	}
}
