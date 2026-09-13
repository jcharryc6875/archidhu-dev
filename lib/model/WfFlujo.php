<?php

/**
 * Subclass for representing a row from the 'WF_FLUJO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfFlujo extends BaseWfFlujo
{
	public function __toString()
	{
		return $this->getDescripcion();
	}
}
