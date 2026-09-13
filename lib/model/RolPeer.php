<?php

/**
 * Subclass for performing query and update operations on the 'rol' table.
 *
 * 
 *
 * @package lib.model
 */ 
class RolPeer extends BaseRolPeer
{
	public static function getOrdenarRol()
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(RolPeer::DESCRIPCION);        
        return RolPeer::doSelect($criteria);
    }
    
    static public function getAllUser()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
		$rs = UsuarioPeer::doSelect($c);
		return $rs; 	
	}
    
}
