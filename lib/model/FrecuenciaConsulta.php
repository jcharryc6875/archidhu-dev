<?php

/**
 * Subclass for representing a row from the 'frecuencia_consulta' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FrecuenciaConsulta extends BaseFrecuenciaConsulta
{
	public function __toString(){
		return self::getDESCRIPCION();
	}
}
