<?php

/**
 * Subclass for representing a row from the 'PROV_ESTADO_SOL_MOD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstadoSolMod extends BaseProvEstadoSolMod
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
