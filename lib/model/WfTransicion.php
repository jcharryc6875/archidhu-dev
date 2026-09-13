<?php

/**
 * Subclass for representing a row from the 'WF_TRANSICION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfTransicion extends BaseWfTransicion
{
	public function __toString()
	{
		return $this->getDescripcion()." - ".$this->getWfTransicionId();
	}
}
