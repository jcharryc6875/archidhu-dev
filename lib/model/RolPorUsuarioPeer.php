<?php

/**
 * Subclass for performing query and update operations on the 'ROL_POR_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class RolPorUsuarioPeer extends BaseRolPorUsuarioPeer
{
    static public function getCurrentRolUsuario($usuario_id=0){
        $c = new Criteria();
        $c->add(RolPorUsuarioPeer::USUARIO_ID,$usuario_id);
        $rs = RolPorUsuarioPeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) {
			$array[] = $object->getRolId();
		}
		//**********************************************************************************************
        return $array; 	
	}

    public static function addNewRolByUser($rolPkDelete = array(), $usuario_id = 0){
        if(count($rolPkDelete) > 0){
            $rows_affected = RolPorUsuarioPeer::removeRolByUsuario($rolPkDelete,$usuario_id);
            //**********************************************************************************************
            foreach ($rolPkDelete as $rolId) {
                $c = new Criteria();
                $c->add(RolPorUsuarioPeer::ROL_ID,$rolId);
                $c->add(RolPorUsuarioPeer::USUARIO_ID,$usuario_id);
                $countAdded = RolPorUsuarioPeer::doCount($c);
                if($countAdded <= 0){
                    $new_object = new RolPorUsuario();
                    $new_object->setRolId($rolId);
                    $new_object->setUsuarioId($usuario_id);
                    $new_object->save();
                }
            }
        }
	}

    public static function removeRolByUsuario($rolPkDelete = array(), $usuario_id = 0){
        $c = new Criteria();
        $c->add(RolPorUsuarioPeer::ROL_ID,$rolPkDelete,Criteria::NOT_IN);
        $c->add(RolPorUsuarioPeer::USUARIO_ID,$usuario_id);
        return RolPorUsuarioPeer::doDelete($c);
	}

}
