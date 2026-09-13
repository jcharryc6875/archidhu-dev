<?php

/**
 * Subclass for representing a row from the 'estado_com_interna' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoComInterna extends BaseEstadoComInterna
{
	 function __toString(){
		
		return $this->getDescripcion();
	}
}
