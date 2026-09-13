<?php

/**
 * Subclass for representing a row from the 'PROV_PERIODO_VALIDEZ' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvPeriodoValidez extends BaseProvPeriodoValidez
{
	public function __toString(){
		return $this->getFechaInicial("Y-m-d")." - ".$this->getFechaFinal("Y-m-d");
	}    
}
