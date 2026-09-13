<?php

/**
 * Subclass for representing a row from the 'WS_USUARIOS' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WsUsuarios extends BaseWsUsuarios
{
	public function __toString()
    {
		return  $this->getNombre();
	}
}
