<?php

/**
 * Subclass for representing a row from the 'PROV_CORRESPONDIENTE_AUTOFACTURACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvCorrespondienteAutofacturacion extends BaseProvCorrespondienteAutofacturacion
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
