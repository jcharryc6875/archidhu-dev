<?php

/**
 * Subclass for representing a row from the 'WF_ESTADO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfEstado extends BaseWfEstado
{
		public function __toString()
	{
		return $this->getDescripcion();
	}
}
