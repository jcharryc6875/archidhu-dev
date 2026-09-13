<?php

/**
 * Subclass for representing a row from the 'TIPO_ESCALA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoEscala extends BaseTipoEscala
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
