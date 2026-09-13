<?php

/**
 * Subclass for representing a row from the 'grupo_usuario' table.
 *
 * 
 *
 * @package lib.model
 */ 
class GrupoUsuario extends BaseGrupoUsuario
{
	public function __toString(){
		return $this->getDescripcion();
	}
}
