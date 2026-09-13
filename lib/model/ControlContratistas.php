<?php

/**
 * Subclass for representing a row from the 'CONTROL_CONTRATISTAS' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ControlContratistas extends BaseControlContratistas
{
    public function __toString(){
		return $this->getNombre() . " " . $this->getPrimerApellido() ;
	}
}
