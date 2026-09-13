<?php

/**
 * Subclass for representing a row from the 'WF_TIPO_DATO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfTipoDato extends BaseWfTipoDato
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
