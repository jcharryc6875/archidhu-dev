<?php

/**
 * Subclass for performing query and update operations on the 'USUARIO_ASIGNADO_SOLICITUD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UsuarioAsignadoSolicitudPeer extends BaseUsuarioAsignadoSolicitudPeer
{
    /**
    * servicioActions::updateEstadoAsignado()
    * Dejar el servicio sin usuario asignado
    * @return
    */
	public static function updateEstadoAsignado($asignarservicio_id)
    {
        try {
            if(!empty($asignarservicio_id)){
                $conexion = Propel::getConnection();
                $consulta = "UPDATE %s SET %s = 0 WHERE %s = ".$asignarservicio_id;
                $sql      = sprintf($consulta, UsuarioAsignadoSolicitudPeer::TABLE_NAME,UsuarioAsignadoSolicitudPeer::ESTA_ASIGNADO, UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID);
                $sentencia = $conexion->prepare($sql);
                $sentencia->execute();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        //******************************************************************************************
        $usuario_id = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //******************************************************************************************
        if($usuario_id){//OJO ESTA DESMARCANDO TODOS LOS REGISTROS DEL USUARIO
    		$conexion = Propel::getConnection();
    		$consulta = "UPDATE %s SET %s = NULL WHERE %s = ".$usuario_id;
            $sql      = sprintf($consulta, ServicioPeer::TABLE_NAME,ServicioPeer::MARCA, ServicioPeer::MARCA);
            //$sentencia = $conexion->prepare($sql);
            //$sentencia->execute();
        }
	}
}
