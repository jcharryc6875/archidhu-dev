<?php

/**
 * Subclass for performing query and update operations on the 'REDIRECCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class RedireccionPeer extends BaseRedireccionPeer
{
    public static function UserIsRedirect($idUser)
    {
        try {
            $fecha  = date('Y-m-d');
            //*******************************************************************************************************
            $c = new Criteria();
            $c->addJoin(RedireccionPeer::REDIRECCION_ID,RedireccionUsuarioPeer::REDIRECCION_ID);
            $c->addJoin(RedireccionUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
            $c->add(RedireccionUsuarioPeer::USUARIO_ID,$idUser);
            $c->add(RedireccionUsuarioPeer::ROLUSUREDIRECCION_ID,1);
            $c->add(UsuarioPeer::ESTADOUSUARIO_ID,3);
            $c->add(RedireccionPeer::FECHA_INICIAL,$fecha.' 00:00:00',Criteria::LESS_EQUAL);
            $c->add(RedireccionPeer::FECHA_FINAL,$fecha.' 00:00:00',Criteria::GREATER_EQUAL);
            $c->add(RedireccionPeer::ESTADOREDIRECCION_ID,2);
            $redireccionar = RedireccionUsuarioPeer::doSelectOne($c);
            
            if($redireccionar != null){
                $a = new Criteria();
                $a->add(RedireccionUsuarioPeer::ROLUSUREDIRECCION_ID,2);
                $a->add(RedireccionUsuarioPeer::REDIRECCION_ID,$redireccionar->getRedireccionId());
                $resp = RedireccionUsuarioPeer::doSelectOne($a);
                return $resp->getUsuarioId();
            }		
            //********************************************************************************************************
            return null;
        } catch (\PropelException $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    					
	}
}
