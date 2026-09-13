<?php

/**
 * Subclass for performing query and update operations on the 'usuario_privilegio' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UsuarioPrivilegioPeer extends BaseUsuarioPrivilegioPeer
{	
	public static function getAllUser()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
		$rs = UsuarioPeer::doSelect($c);
		return $rs; 	
	}
}
