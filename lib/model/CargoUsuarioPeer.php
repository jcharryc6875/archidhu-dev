<?php

/**
 * Subclass for performing query and update operations on the 'CARGO_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CargoUsuarioPeer extends BaseCargoUsuarioPeer
{	
	public static function getCargoUsuarioByIdUser($usuario_id,$isObject=false){
        $c = new Criteria();
        $c->add(CargoUsuarioPeer::USUARIO_ID,$usuario_id);
        $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1);
        $c->add(CargoUsuarioPeer::ES_ACTUAL,1);
        $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);
        //**************************************************************************
        if($cargo_usuario == null)
            return null;
        else
            return $isObject ? $cargo_usuario : $cargo_usuario->getPrimaryKey();
    }

    public static function getCargoUsuarioByNuidAndCargoUser($nuid, $ctcargo = null, $isObject=false){
        $c = new Criteria();
        $c->addJoin(CargoUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->addJoin(CargoUsuarioPeer::CARGO_ID,CargoPeer::CARGO_ID);
        $c->add(UsuarioPeer::CEDULA,$nuid);
        if(!empty($ctcargo)){ $c->add(CargoPeer::DESCRIPCION,$nuid); }
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
        $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1);
        $c->add(CargoUsuarioPeer::ES_ACTUAL,1);
        $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);
        return $isObject ? $cargo_usuario : $cargo_usuario->getPrimaryKey();
    }
    
    public static function getCargoUsuarioByNuidUser($nuid, $isObject=false){
        $c = new Criteria();
        $c->addJoin(CargoUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->add(UsuarioPeer::CEDULA,$nuid);
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
        $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1);
        $c->add(CargoUsuarioPeer::ES_ACTUAL,1);
        $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);
        return $isObject ? $cargo_usuario : $cargo_usuario->getPrimaryKey();
    }

    public static function getCaUsuariosByNuidsUsers($nuids){
        $c = new Criteria();
        $c->addJoin(CargoUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->add(UsuarioPeer::CEDULA,$nuids,Criteria::IN);
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
        $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1);
        $c->add(CargoUsuarioPeer::ES_ACTUAL,1);
        return CargoUsuarioPeer::doSelect($c);
    }

    public static function getCargoUsuarioByNuidUserEx($nuid, $isObject = false){
        try {
            $c = new Criteria();
            $c->addJoin(CargoUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
            $c->add(UsuarioPeer::CEDULA,$nuid);
            //$c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
            $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1);
            $c->add(CargoUsuarioPeer::ES_ACTUAL,1);
            $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);
            if($cargo_usuario == null){
                return array('error' => 400, 'message' => 'Usuario no existe', 'info' => 'Usuario no encontrado', 'object' => null);
            }elseif(in_array($cargo_usuario->getUsuario()->getEstadousuarioId(),array(1,3))){
                $data = $isObject ? $cargo_usuario : $cargo_usuario->getPrimaryKey();
                return array('error' => 200, 'message' => 'Usuario valido', 'info' => 'Usuario encontrado', 'object' => $data);
            }else{
                return array('error' => 401, 'message' => 'Inactivo', 'info' => $cargo_usuario->getUsuario()->getEstadoUsuario(), 'object' => $cargo_usuario);
            }
        } catch (PropelException $ex) {
            return array('error' => 400, 'message' => 'Error interno', 'info' => $ex->getMessage(), 'object' => null);
        } catch (Exception $ex) {
            return array('error' => 400, 'message' => 'Error interno', 'info' => $ex->getMessage(), 'object' => null);
        }
    }
}
