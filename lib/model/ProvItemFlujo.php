<?php

/**
 * Subclass for representing a row from the 'PROV_ITEM_FLUJO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvItemFlujo extends BaseProvItemFlujo
{
    public function __toString()
    {
		return $this->getDescripcion();
	} 
}
