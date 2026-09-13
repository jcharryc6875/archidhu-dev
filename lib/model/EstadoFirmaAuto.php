<?php

/**
 * Subclass for representing a row from the 'ESTADO_FIRMA_AUTO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoFirmaAuto extends BaseEstadoFirmaAuto
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
