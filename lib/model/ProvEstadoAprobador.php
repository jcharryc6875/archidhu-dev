<?php

/**
 * Subclass for representing a row from the 'PROV_ESTADO_APROBADOR' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstadoAprobador extends BaseProvEstadoAprobador
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
