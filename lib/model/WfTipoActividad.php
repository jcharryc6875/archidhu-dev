<?php

/**
 * Subclass for representing a row from the 'WF_TIPO_ACTIVIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfTipoActividad extends BaseWfTipoActividad
{
	
	public function __toString()
    {
		return $this->getDescripcion();
	} 
}
