<?php

/**
 * Subclass for representing a row from the 'WF_BUZON' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfBuzon extends BaseWfBuzon
{
	public function __toString()
	{
		return $this->getDescripcion();
	}
}
