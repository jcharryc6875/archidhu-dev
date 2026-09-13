<?php

/**
 * Subclass for representing a row from the 'wf_estado_imagen' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfEstadoImagen extends BaseWfEstadoImagen
{
  
  	public function __toString()
    {
		return $this->getNombre();
	} 
}
