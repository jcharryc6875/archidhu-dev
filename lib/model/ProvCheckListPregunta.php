<?php

/**
 * Subclass for representing a row from the 'PROV_CHECK_LIST_PREGUNTA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvCheckListPregunta extends BaseProvCheckListPregunta
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
