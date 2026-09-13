<?php

/**
 * Subclass for representing a row from the 'TIPO_IDENTIFICACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoIdentificacion extends BaseTipoIdentificacion
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
