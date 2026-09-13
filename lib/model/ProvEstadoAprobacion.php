<?php

/**
 * Subclass for representing a row from the 'PROV_ESTADO_APROBACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvEstadoAprobacion extends BaseProvEstadoAprobacion
{	public function __toString(){
		return $this->getDescripcion();
	}
}
