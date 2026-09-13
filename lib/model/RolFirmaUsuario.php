<?php

/**
 * Subclass for representing a row from the 'ROL_FIRMA_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class RolFirmaUsuario extends BaseRolFirmaUsuario
{
    public function __toString(){
		return $this->getDescripcion();
	}
}
