<?php

/**
 * Subclass for representing a row from the 'WF_ACTIVIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfActividad extends BaseWfActividad
{
  public function __toString()
	{
		return $this->getDescripcion()." - ".$this->getWfActividadId();
	}
}
