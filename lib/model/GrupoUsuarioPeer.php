<?php

/**
 * Subclass for performing query and update operations on the 'grupo_usuario' table.
 *
 * 
 *
 * @package lib.model
 */ 
class GrupoUsuarioPeer extends BaseGrupoUsuarioPeer
{
	static public function getGrupoUsuarioOrdenado(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(GrupoUsuarioPeer::DESCRIPCION);
		$rs = GrupoUsuarioPeer::doSelect($c);
		return $rs; 	
	}
}
