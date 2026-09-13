<?php

/**
 * Subclass for representing a row from the 'empresa_mensajeria' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EmpresaMensajeria extends BaseEmpresaMensajeria
{
	 function __toString(){
		
		return $this->getNombre();
	}
}
