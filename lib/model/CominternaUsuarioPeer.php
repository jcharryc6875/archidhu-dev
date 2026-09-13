<?php

/**
 * Subclass for performing query and update operations on the 'COMINTERNA_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CominternaUsuarioPeer extends BaseCominternaUsuarioPeer
{
    public static function getUserAccessComProcess($pkcom_id,$usuario_id,$rol_id=null)
    {
        $c = new Criteria();
        $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuario_id);
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
        if(!empty($rol_id)){ $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id); }
        return CominternaUsuarioPeer::doCount($c);
    }

    public static function getDevolucionesData($pkcom_id, $rol_ids = array(), $notInUser = array())
    {
        $list_objects = array();
        //*******************************************************************************
        $c = new Criteria();
        $c->setDistinct();
        $c->addJoin(CominternaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->addJoin(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,RolusuarioCominternaPeer::ROLUSUARIOCOMINTERNA_ID);
        //*******************************************************************************
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_ids,Criteria::IN);
        $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1,Criteria::NOT_EQUAL);
        $c->add(CominternaUsuarioPeer::CHECK_APROBACION,1);
        if(count($notInUser)){ $c->add(CominternaUsuarioPeer::USUARIO_ID,$notInUser,Criteria::NOT_IN);  }
        //*******************************************************************************
        $c->clearSelectColumns();
        $c->addSelectColumn(UsuarioPeer::USUARIO_ID);
        $c->addAsColumn('ROL_NAME',RolusuarioCominternaPeer::DESCRIPCION);
        $c->addAsColumn('UNOMBRE',UsuarioPeer::NOMBRE);
        $c->addAsColumn('UAPELLIDO',UsuarioPeer::APELLIDO);
        $c->addAsColumn('UROL_ID',CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID);
        //*******************************************************************************
        $list_userscom = UsuarioPeer::doSelectStmt($c);
        //*******************************************************************************
        while ($object = $list_userscom->fetch()) {
            $nombre_apellido = trim($object['UNOMBRE']).' '.trim($object['UAPELLIDO']);
            $list_objects[] = array('USUARIO_ID'=>$object['USUARIO_ID'],'NOMBRE_USER'=>$nombre_apellido,'UTROL'=>$object['ROL_NAME'],'UTROL_ID'=>$object['UROL_ID']);
        
        }
        //*******************************************************************************
        $cuser_creador = CominternaUsuarioPeer::getUserComByRol($pkcom_id,1);
        if($cuser_creador != null){
            $nombre_apellido = trim($cuser_creador->getUsuario()->getNombreApellido());
            $list_objects[] = array('USUARIO_ID'=>$cuser_creador->getUsuarioId(),'NOMBRE_USER'=>$nombre_apellido,
                'UTROL'=>$cuser_creador->getRolusuarioCominterna()->getDescripcion(),'UTROL_ID'=>$cuser_creador->getRolusuariocominternaId());
        }
        //*******************************************************************************
        return $list_objects;
    }

    public static function getListUsersByCom($pkcom_id)
    {
        $c = new Criteria();
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
        $c->addAscendingOrderByColumn(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID);
        return CominternaUsuarioPeer::doSelectJoinAllExceptComInterna($c);
    }

    public static function getIsUserAsignado($pkcom_id,$usuario_id=0,$esta_asignada=1,$isObject=false){
        try {
            $c = new Criteria();
            if(!empty($usuario_id)){ $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuario_id); }
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,$esta_asignada);
            return $isObject ? CominternaUsuarioPeer::doSelectOne($c) : CominternaUsuarioPeer::doCount($c);
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public static function getAllUserFirmaDigitalObj($cominterna_id,$rol_id=2,$IsIdPk = false)
    {
        $c=  new Criteria();
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID, $cominterna_id);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, $rol_id);
        $result = CominternaUsuarioPeer::doSelectJoinUsuario($c);    
        $list_objects = array();
        foreach($result as $object){
            if($object != null){
                if(!empty($object->getUsuario()->getLoginFirma()) && !empty($object->getUsuario()->getPassFirma())){
                    $list_objects[] = $object->getUsuario();
                }
            }
        }
        return $list_objects;        
    }

    public static function getCurrentUserAsignado($pkcom_id,$esta_asignada=1){
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,$esta_asignada);
            return CominternaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getNextUserComByProceso($pkcom_id,$rol_id=0,$excludePks=array()){
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id);
            $c->add(CominternaUsuarioPeer::CHECK_APROBACION,0);
            if(count($excludePks)){ $c->add(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID,$excludePks,Criteria::NOT_IN); }
			$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
            return CominternaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getCountUncheckApro($pkcom_id,$isChecked=0,$rol_ids = array(2,5)){
        $list_objects = array();
        //*************************************************************************************************
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::CHECK_APROBACION,$isChecked);
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_ids,Criteria::IN);
            $results = CominternaUsuarioPeer::doSelect($c);
            //*********************************************************************************************
            foreach ($results as $object) {
                $list_objects[] = $object->getUsuarioId();
            }
            //*********************************************************************************************
            return $list_objects;
        } catch (\Throwable $th) {
            //throw $th;
            return $list_objects;
        }
    }

    public static function getUserAddedInCom($pkcom_id,$usuario_id = 0,$rol_id = 0){
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuario_id);
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id);
            //if($rol_id != 1){ $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id); }
            return CominternaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getUserComByRol($pkcom_id,$rol_id = 0){
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id);
            return CominternaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public static function getListUserComByRol($pkcom_id,$rol_id = null,$esta_asignada = null){
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            if($rol_id != null){ $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id); }
            if($esta_asignada != null){ $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,$esta_asignada); }
            return CominternaUsuarioPeer::doSelect($c);
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public static function initUserByCom($cominterna_id,$user_params = array(),$usuario_radicador,$estadocominterna_id = 1, $snext_user = false){
        $ucargo_list = array();$uidcargo_list = array();
        //*********************************************************************************************************
        try {
            $usuarios_firma = preg_split("/[,]+/",$user_params['str_firmausers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_firma = preg_split("/[,]+/",$user_params['str_ucargosfirma'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_copia = preg_split("/[,]+/",$user_params['str_copiausers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_copia = preg_split("/[,]+/",$user_params['str_ucargoscopia'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_revisor = preg_split("/[,]+/",$user_params['str_revisorusers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_revisor = preg_split("/[,]+/",$user_params['str_ucargosrevisor'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_destino = preg_split("/[,]+/",$user_params['str_destinorusers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_destino = preg_split("/[,]+/",$user_params['str_ucargosdestino'], -1, PREG_SPLIT_NO_EMPTY);
            //*****************************************************************************************************
            $cargo_radicador = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_radicador,true);
            //*****************************************************************************************************
            $list_users = array();
            if(count($usuarios_destino) == 1){
                $list_users['usuario_destino'] = array('usuario_id' => $usuarios_destino[0], 'cargousuarioId' => $ucargos_destino[0], 'rol_id' => 4);
            }else{
                $list_users['usuarios_destinatarios'] = array('lusuarios' => $usuarios_destino, 'ucargos' => $ucargos_destino, 'rol_id' => 4);
            }
            //*****************************************************************************************************
            $list_users['usuarios_creador'] = array('usuario_id' => $cargo_radicador->getUsuarioId(), 'cargousuarioId' => $cargo_radicador->getPrimaryKey(), 'rol_id' => 1);
            $list_users['usuarios_firma'] = array('lusuarios' => $usuarios_firma, 'ucargos' =>$ucargos_firma, 'rol_id' => 2);
            $list_users['usuarios_copia'] = array('lusuarios' => $usuarios_copia, 'ucargos' =>$ucargos_copia, 'rol_id' => 3);
            $list_users['usuarios_revisor'] = array('lusuarios' => $usuarios_revisor, 'ucargos' =>$ucargos_revisor, 'rol_id' => 5);
            //*****************************************************************************************************
            $info_com['pkcom_id'] = $cominterna_id;
            foreach ($list_users as $key => $value) {
                if($key == 'usuario_destino'){
                    $info_com['usuario_id'] = $value['usuario_id'];
                    $info_com['cusuario_id'] =  $value['cargousuarioId'];
                    $info_com['estadocom_id'] = $estadocominterna_id;
                    $info_com['rol_id'] = $value['rol_id'];
                    $ucom_interna = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
                    $ucargo_list[] = $ucom_interna;
                    $uidcargo_list[] = $ucom_interna->getPrimaryKey();
                }elseif($key == 'usuarios_creador'){
                    $info_com['usuario_id'] = $value['usuario_id'];
                    $info_com['cusuario_id'] =  $value['cargousuarioId'];
                    $info_com['estadocom_id'] = $estadocominterna_id;
                    $info_com['rol_id'] = $value['rol_id'];
                    $ucom_interna = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
                    $ucargo_list[] = $ucom_interna;
                    $uidcargo_list[] = $ucom_interna->getPrimaryKey();
                }else{
                    for ($i=0; $i < count($value['lusuarios']); $i++){ 
                        $info_com['usuario_id'] = $value['lusuarios'][$i];
                        $info_com['cusuario_id'] =  $value['ucargos'][$i];
                        $info_com['estadocom_id'] = $estadocominterna_id;
                        $info_com['rol_id'] = $value['rol_id'];
                        $ucom_interna = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
                        $ucargo_list[] = $ucom_interna;
                        $uidcargo_list[] = $ucom_interna->getPrimaryKey();
                    }
                }
            }
            //*****************************************************************************************************
            CominternaUsuarioPeer::deleteUserCargoNotIn($cominterna_id,$uidcargo_list);
            if($snext_user){ CominternaUsuarioPeer::setNextUserProceso($cominterna_id); }
            //*****************************************************************************************************
            return $ucargo_list;
        } catch (\Throwable $th) {
            //throw $th;
            foreach ($ucargo_list as $ucargo_com) {
                $ucargo_com->delete();
            }
            //*****************************************************************************************************
            return null;
        }
    }

    public static function setNextUserProceso($cominterna_id,$snext_user = false){
        try {
            $ucom_current = CominternaUsuarioPeer::getCurrentUserAsignado($cominterna_id);
            //*********************************************************************************************************
            if($ucom_current == null){
                $ucom_next = CominternaUsuarioPeer::getUserComByRol($cominterna_id,1);
            }else{
                $urol_current = $ucom_current->getRolusuariocominternaId();
                $ucom_next = CominternaUsuarioPeer::getNextUserComByProceso($cominterna_id,$urol_current,array($ucom_current->getPrimaryKey()));
                $cambia_asignado = false;
            }
            //*********************************************************************************************************
            if($ucom_next != null){
                $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                $ucom_next->setEstaAsignada(1);
                $ucom_next->setCheckAprobacion(0);
                $ucom_next->setFechaAprobacion(null);
                $cambia_asignado = true;
            }elseif($urol_current == 1){//si actualmente esta proyecta
                $ucom_next = CominternaUsuarioPeer::getNextUserComByProceso($cominterna_id,5);
				if($ucom_next == null){ $ucom_next = CominternaUsuarioPeer::getNextUserComByProceso($cominterna_id,2); }
				if($ucom_next != null){
                $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                $ucom_next->setEstaAsignada(1);
                $ucom_next->setCheckAprobacion(0);
                $ucom_next->setFechaAprobacion(null);
				}
                $cambia_asignado = true;
            }elseif($urol_current == 2){//si actualmente esta en firmas
                $ucom_next = CominternaUsuarioPeer::getNextUserComByProceso($cominterna_id,1);
				if($ucom_next != null){
                $ucom_next->setEstaAsignada(1);
                $cambia_asignado = true;
				}
            }elseif($urol_current == 5){//si actualmente esta en revision
                $ucom_next = CominternaUsuarioPeer::getNextUserComByProceso($cominterna_id,2);
				if($ucom_next != null){
                $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                $ucom_next->setEstaAsignada(1);
                $ucom_next->setCheckAprobacion(0);
                $ucom_next->setFechaAprobacion(null);
                $cambia_asignado = true;
            }
            }
            //*********************************************************************************************************
            if($ucom_next != null){ $ucom_next->save(); }
            //*********************************************************************************************************
            if($cambia_asignado && ($ucom_current != null)){
                $ucom_current->setEstaAsignada(0);
                $ucom_current->setFechaAprobacion(date("Y-m-d G:i:s"));
                $ucom_current->setCheckAprobacion(1);
                $ucom_current->save();
            }
            //*********************************************************************************************************
            return $ucom_next;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getListUncheckApro($pkcom_id,$isChecked=0,$rol_ids = array(2,5))
    {
        $list_objects = array();
        //*************************************************************************************************
        try {
            $c = new Criteria();
            $c->add(CominternaUsuarioPeer::CHECK_APROBACION,$isChecked);
            $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$pkcom_id);
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_ids,Criteria::IN);
            $results = CominternaUsuarioPeer::doSelect($c);
            //*********************************************************************************************
            foreach ($results as $object) {
                $list_objects[] = $object->getUsuarioId();
            }
            //*********************************************************************************************
            return $list_objects;
        } catch (\Throwable $th) {
            //throw $th;
            return $list_objects;
        }
    }

    public static function addUserByCom($info_com = array()){
        try {
            if(!empty($info_com['pkcom_id'])){
                $esta_asignada = isset($info_com['esta_asignada']) ? $info_com['esta_asignada'] : 0;
                //**************************************************************************************************
                $ucom_interna = new CominternaUsuario();
                $ucom_interna->setUsuarioId($info_com['usuario_id']);
                $ucom_interna->setCominternaId($info_com['pkcom_id']);
                $ucom_interna->setCargousuarioId($info_com['cusuario_id']);
                $ucom_interna->setEstadocominternaId($info_com['estadocom_id']);
                $ucom_interna->setRolusuariocominternaId($info_com['rol_id']);
                $ucom_interna->setWfEjecutado(0);
                $ucom_interna->setFechaAsigna(($esta_asignada ? date("Y-m-d G:i:s") : null));
                $ucom_interna->setEstaAsignada($esta_asignada);
                $ucom_interna->save();
                //**************************************************************************************************
                return $ucom_interna;
            }
        } catch (\PropelException $th) {
            //throw $th;
            return null;        
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function addOrUpdateUserByCom($info_com = array()){
        try {
            
            if(!empty($info_com['pkcom_id'])){
                $ucom_interna = CominternaUsuarioPeer::getUserAddedInCom($info_com['pkcom_id'],$info_com['usuario_id'],$info_com['rol_id']);
                $esta_asignada = isset($info_com['esta_asignada']) ? $info_com['esta_asignada'] : 0;
                if($ucom_interna == null){
                    $ucom_interna = CominternaUsuarioPeer::addUserByCom($info_com);
                }elseif($ucom_interna->getEstadocominternaId() != $info_com['estadocom_id']){
                    $ucom_interna->setEstadocominternaId($info_com['estadocom_id']);
                    $ucom_interna->setFechaAsigna(($esta_asignada ? date("Y-m-d G:i:s") : null));
                    $ucom_interna->setEstaAsignada($esta_asignada);
                    $ucom_interna->save();
                }
                //**************************************************************************************************
                return $ucom_interna;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function deleteUserCargoNotIn($pkcom_id = 0,$lcargos_current = array())
    {
        try{
            if(empty($pkcom_id) || !count($lcargos_current) || !is_array($lcargos_current)){
                return null;
            }
            //*******************************************************************************************************
            $conexion = Propel::getConnection();
            $consulta = "DELETE FROM %s WHERE %s = ".$pkcom_id." AND %s NOT IN(%s)";
            $sql = sprintf($consulta,CominternaUsuarioPeer::TABLE_NAME,CominternaUsuarioPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNAUSUARIO_ID,implode(",",$lcargos_current));
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function updateEstados($pkcom_id,$renviado="",$requiere_respuesta=false,$estadocom=2)
    {
        $conexion = Propel::getConnection();
        $consulta = "UPDATE %s SET %s = 2 WHERE %s = ".$pkcom_id." AND %s = 1";
        $sql      = sprintf($consulta, CominternaUsuarioPeer::TABLE_NAME, CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID,CominternaUsuarioPeer::ESTADOCOMINTERNA_ID);
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();
        //************************************************************************************
        if($renviado != "" || $requiere_respuesta){
            $consulta = "UPDATE %s SET %s = ".$estadocom." WHERE %s = ".$pkcom_id." and %s = 4";
            $sql      = sprintf($consulta, CominternaUsuarioPeer::TABLE_NAME, CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID,CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
        }
    }

    public static function updateCheckAllFirmaUser($cominterna_id, $roles_ids= array(2))
    {
        try {
            $conexion = Propel::getConnection();
            $consulta = "UPDATE %s SET  %s = 0, %s = '".date("Y-m-d G:i:s")."', %s = 1 WHERE %s = ".$cominterna_id." AND %s IN (".implode(",",$roles_ids).") AND %s != 1;";
            $sql      = sprintf($consulta, CominternaUsuarioPeer::TABLE_NAME, CominternaUsuarioPeer::ESTA_ASIGNADA,CominternaUsuarioPeer::FECHA_APROBACION,CominternaUsuarioPeer::CHECK_APROBACION,
                            CominternaUsuarioPeer::COMINTERNA_ID,CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,CominternaUsuarioPeer::CHECK_APROBACION);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}
