<?php

/**
 * Subclass for representing a row from the 'descarte_final_subserie' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DescarteFinalSubserie extends BaseDescarteFinalSubserie
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
