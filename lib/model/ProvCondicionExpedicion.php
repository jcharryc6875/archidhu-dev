<?php

/**
 * Subclass for representing a row from the 'PROV_CONDICION_EXPEDICION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvCondicionExpedicion extends BaseProvCondicionExpedicion
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
