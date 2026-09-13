<?php

/**
 * Subclass for performing query and update operations on the 'ENVIADA_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EnviadaUsuarioPeer extends BaseEnviadaUsuarioPeer
{
    public static function getComObjectByUser($comenviada_id,$usuario_id,$ucargo_id,$rol_id)
    {
    	$c = new Criteria();
        $c->add(EnviadaUsuarioPeer::USUARIO_ID,$usuario_id);
        $c->add(EnviadaUsuarioPeer::CARGOUSUARIO_ID,$ucargo_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_id);
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$comenviada_id);
        $com_object = EnviadaUsuarioPeer::doSelectOne($c);
    	//************************************************************************************
        if($com_object != null){
            return $com_object;
        }else{
            return new EnviadaUsuario();
        }
    }

    public static function getListObjectsUserByCom($comenviada_id)
    {
    	$c = new Criteria();
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$comenviada_id);
        $com_object = EnviadaUsuarioPeer::doSelect($c);
    	//************************************************************************************
        return $com_object;
    }

    public static function getCopiaComEnviada($comenviada_id)
    {
        $c=new Criteria();
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 3);
        $result = EnviadaUsuarioPeer::doSelect($c);    
        $userCopia=array();
        foreach($result as $res){				
            $userCopia[] = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();	  					
        }
        return $userCopia;	
    }

    public static function getFirstUsurioFirma($comenviada_id,$rol_id=2,$IsIdPk = true)
    { 
        // consultar firmante inicial
        $c=  new Criteria();
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, $rol_id);
        $result = EnviadaUsuarioPeer::doSelectJoinUsuario($c);    
        $cont = 0;
        foreach($result as $res){
            if($cont==0) { $firmante = $IsIdPk ? $res->getUsuarioId() : $res->getUsuario()->getFullNombre(); }
            $cont=1;
        }
        return $firmante;        
    }

    public static function getFirstUsurioFirmaObject($comenviada_id,$rol_id=2,$IsObjPk = true)
    { 
        // consultar firmante inicial
        $c =  new Criteria();
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, $rol_id);
        $result = EnviadaUsuarioPeer::doSelectJoinUsuario($c);    
        $cont = 0;
        foreach($result as $res){
            if($cont==0) { $firmante = $IsObjPk ? $res->getUsuario() : $res->getUsuario()->getFullNombre(); }
            $cont=1;
        }
        return $firmante;        
    }

    public static function getAllUserFirmaDigitalObj($comenviada_id,$rol_id=2,$IsIdPk = false)
    { 
        // consultar firmante inicial
        $c=  new Criteria();
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, $rol_id);
        $result = EnviadaUsuarioPeer::doSelectJoinUsuario($c);    
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

    public static function getUserToEfirma($list_user = array())
    {
        foreach($list_user as $object){
            if($object != null){
                if(!empty($object->getLoginFirma()) && !empty($object->getPassFirma())){
                    return true;
                }
            }
        }
        return false;        
    }

    public static function updateEstados($comenviada_id,$newestado_id=2)
    {
        try {
          $conexion = Propel::getConnection();
          $consulta = "UPDATE %s set  %s = ".$newestado_id." WHERE %s = ".$comenviada_id." ";
          $sql      = sprintf($consulta, EnviadaUsuarioPeer::TABLE_NAME, EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
          $sentencia = $conexion->prepare($sql);
          $sentencia->execute();
          return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public static function updateAproFirma($comenviada_id, $roles_ids= array(2), $isAsigned = 1)
    {
        try {
            $conexion = Propel::getConnection();
            $consulta = "UPDATE %s set  %s = 0, %s = '".date("Y-m-d G:i:s")."', %s = 1 WHERE %s = ".$comenviada_id." AND %s = ".$isAsigned." AND %s IN (".implode(",",$roles_ids).");";
            $sql      = sprintf($consulta, EnviadaUsuarioPeer::TABLE_NAME, EnviadaUsuarioPeer::ESTA_ASIGNADA,EnviadaUsuarioPeer::FECHA_APRUEBA,
                            EnviadaUsuarioPeer::FIRMA_APRUEBA,EnviadaUsuarioPeer::COMENVIADA_ID,EnviadaUsuarioPeer::ESTA_ASIGNADA,EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public static function updateAproFirmaAll($comenviada_id, $roles_ids= array(2))
    {
        try {
            $conexion = Propel::getConnection();
            $consulta = "UPDATE %s set  %s = 0, %s = '".date("Y-m-d G:i:s")."', %s = 1 WHERE %s = ".$comenviada_id." AND %s IN (".implode(",",$roles_ids).");";
            $sql      = sprintf($consulta, EnviadaUsuarioPeer::TABLE_NAME, EnviadaUsuarioPeer::ESTA_ASIGNADA,EnviadaUsuarioPeer::FECHA_APRUEBA,
                            EnviadaUsuarioPeer::FIRMA_APRUEBA,EnviadaUsuarioPeer::COMENVIADA_ID,EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public static function getCopiaUserObjectByCom($comenviadaId)
    {
        $userCopia = array();
        try{
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $comenviadaId);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 3);
            $result = EnviadaUsuarioPeer::doSelectJoinUsuario($c);
            //*****************************************************************************
            foreach($result as $object){				
                $userCopia[] = $object->getUsuario();	  					
            }
            //*****************************************************************************
            return $userCopia;
        } catch (\Throwable $th) {
            //throw $th;
            return $userCopia;
        }        	
    }

    public static function getObjUsuarioEnviadaByRol($comenviada_id, $usuario_id = 0, $rol_id = 0)
    {
        $cap = new Criteria();
        $cap->add(EnviadaUsuarioPeer::COMENVIADA_ID,$comenviada_id);
        $cap->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, $rol_id);
        $cap->add(EnviadaUsuarioPeer::USUARIO_ID, $usuario_id);
        $euser_aprob = EnviadaUsuarioPeer::doSelect($cap);
        return $euser_aprob;
    }

    public static function getCurrentUserProcess($comenviada_id)
    {
        $cap = new Criteria();
        $cap->add(EnviadaUsuarioPeer::COMENVIADA_ID,$comenviada_id);
        $cap->add(EnviadaUsuarioPeer::ESTA_ASIGNADA, 1);
        $euser_aprob = EnviadaUsuarioPeer::doSelect($cap);
        return $euser_aprob;
    } 

    public static function getComUserProcess($comenviada_id,$rol_id)
    {
        $cap = new Criteria();
        $cap->add(EnviadaUsuarioPeer::COMENVIADA_ID,$comenviada_id);
        $cap->add(EnviadaUsuarioPeer::ESTA_ASIGNADA, 0);
        $cap->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, $rol_id);
        $euser_aprob = EnviadaUsuarioPeer::doSelect($cap);
        return $euser_aprob;
    }     

    public static function initUserByCom($comenviada_id,$user_params = array(),$usuario_radicador,$estadocomenviada_id = 1, $snext_user = false){
        $ucargo_list = array();$uidcargo_list = array();
        //*********************************************************************************************************
        try {
            $usuarios_firma = preg_split("/[,]+/",$user_params['str_firmausers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_firma = preg_split("/[,]+/",$user_params['str_ucargosfirma'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_copia = preg_split("/[,]+/",$user_params['str_copiausers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_copia = preg_split("/[,]+/",$user_params['str_ucargoscopia'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_revisor = preg_split("/[,]+/",$user_params['str_revisorusers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_revisor = preg_split("/[,]+/",$user_params['str_ucargosrevisor'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_gestor = preg_split("/[,]+/",$user_params['str_gestorusers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_gestor = preg_split("/[,]+/",$user_params['str_ucargosgestor'], -1, PREG_SPLIT_NO_EMPTY);
            $usuarios_aprobador = preg_split("/[,]+/",$user_params['str_aprobadorusers'], -1, PREG_SPLIT_NO_EMPTY);
            $ucargos_aprobador = preg_split("/[,]+/",$user_params['str_ucargosaprobador'], -1, PREG_SPLIT_NO_EMPTY);
            //*****************************************************************************************************
            $cargo_radicador = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_radicador,true);
            //*****************************************************************************************************
            $list_users = array();
            $list_users['usuario_gestor'] = array('lusuarios' => $usuarios_gestor, 'cargousuarioId' => $ucargos_gestor, 'rol_id' => 5, 'tipoprocesocom_id' => 3);
            $list_users['usuarios_creador'] = array('usuario_id' => $cargo_radicador->getUsuarioId(), 'cargousuarioId' => $cargo_radicador->getPrimaryKey(), 'rol_id' => 1, 'tipoprocesocom_id' => 1);
            $list_users['usuarios_firma'] = array('lusuarios' => $usuarios_firma, 'ucargos' =>$ucargos_firma, 'rol_id' => 2, 'tipoprocesocom_id' => 5);
            $list_users['usuarios_copia'] = array('lusuarios' => $usuarios_copia, 'ucargos' =>$ucargos_copia, 'rol_id' => 3);
            $list_users['usuarios_revisor'] = array('lusuarios' => $usuarios_revisor, 'ucargos' =>$ucargos_revisor, 'rol_id' => 4, 'tipoprocesocom_id' => 4);
            //*****************************************************************************************************
            $info_com['pkcom_id'] = $comenviada_id;
            foreach ($list_users as $key => $value) {
                if($key == 'usuarios_creador'){
                    $info_com['usuario_id'] = $value['usuario_id'];
                    $info_com['cusuario_id'] =  $value['cargousuarioId'];
                    $info_com['estadocom_id'] = $estadocomenviada_id;
                    $info_com['rol_id'] = $value['rol_id'];
                    $info_com['tipoprocesocom_id'] = isset($value['tipoprocesocom_id']) ? $value['tipoprocesocom_id'] : null;
                    $ucom_object = EnviadaUsuarioPeer::addOrUpdateUserByCom($info_com);
                    $ucargo_list[] = $ucom_object;
                    $uidcargo_list[] = $ucom_object->getPrimaryKey();
                }else{
                    for ($i=0; $i < count($value['lusuarios']); $i++){ 
                        $info_com['usuario_id'] = $value['lusuarios'][$i];
                        $info_com['cusuario_id'] =  $value['ucargos'][$i];
                        $info_com['estadocom_id'] = $estadocomenviada_id;
                        $info_com['rol_id'] = $value['rol_id'];
                        $info_com['tipoprocesocom_id'] = isset($value['tipoprocesocom_id']) ? $value['tipoprocesocom_id'] : null;
                        $ucom_object = EnviadaUsuarioPeer::addOrUpdateUserByCom($info_com);
                        $ucargo_list[] = is_array($ucom_object) ? array_merge($ucargo_list,$ucom_object) : $ucom_object;
                        if(is_array($ucom_object)){
                            foreach ($ucom_object as $item_com) {
                                $uidcargo_list[] = $item_com->getPrimaryKey();
                            }
                        }else{
                            $uidcargo_list[] = $ucom_object->getPrimaryKey();
                        }
                    }
                }
            }
            //*****************************************************************************************************
            EnviadaUsuarioPeer::deleteUserCargoNotIn($comenviada_id,$uidcargo_list);
            if($snext_user){ EnviadaUsuarioPeer::setNextUserProceso($comenviada_id); }
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

    public static function setNextUserProceso($comobject_id,$snext_user = false){
        try {
            $ucom_current = EnviadaUsuarioPeer::getCurrentUserAsignado($comobject_id);
            //*********************************************************************************************************
            if($ucom_current == null){
                $ucom_next = EnviadaUsuarioPeer::getUserComByRol($comobject_id,1);
            }else{
                //$urol_current = $ucom_current->getRoluscomenviadaId() == 1 ? 5 : $ucom_current->getRoluscomenviadaId();
				$urol_current = $ucom_current->getRoluscomenviadaId();
                $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,$urol_current,array($ucom_current->getPrimaryKey()));
                $cambia_asignado = false;
            }
            //*********************************************************************************************************
            if($ucom_next != null){
                $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                $ucom_next->setEstaAsignada(1);
                $ucom_next->setFirmaAprueba(0);
                $ucom_next->setFechaAprueba(null);
                $cambia_asignado = true;
            }elseif($urol_current == 1){//si actualmente esta proyecta
                $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,5);//gestores insumo
				if($ucom_next == null){ $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,4); }//revisores
				if($ucom_next == null){ $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,2); }//firmas
				if($ucom_next != null){
                    $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                    $ucom_next->setEstaAsignada(1);
                    $ucom_next->setFirmaAprueba(0);
                    $ucom_next->setFechaAprueba(null);
				}
                $cambia_asignado = true;
            }elseif($urol_current == 2){//si actualmente esta en firmas
                $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,1);
				if($ucom_next != null){
                    $ucom_next->setEstaAsignada(1);
                    $cambia_asignado = true;
				}
            }elseif($urol_current == 5){//si actualmente esta en gestion
                $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,4);
                if($ucom_next == null){ $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,2); }
                
				if($ucom_next != null){
                    $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                    $ucom_next->setEstaAsignada(1);
                    $ucom_next->setFirmaAprueba(0);
                    $ucom_next->setFechaAprueba(null);
                    $cambia_asignado = true;
                }
            }elseif($urol_current == 4){//si actualmente esta en revision
                $ucom_next = EnviadaUsuarioPeer::getNextUserComByProceso($comobject_id,2);
				if($ucom_next != null){
                    $ucom_next->setFechaAsigna(date("Y-m-d G:i:s"));
                    $ucom_next->setEstaAsignada(1);
                    $ucom_next->setFirmaAprueba(0);
                    $ucom_next->setFechaAprueba(null);
                    $cambia_asignado = true;
                }
            }
            //*********************************************************************************************************
            if($ucom_next != null){ $ucom_next->save(); }
            //*********************************************************************************************************
            if($cambia_asignado && ($ucom_current != null)){
                $ucom_current->setEstaAsignada(0);
                $ucom_current->setFechaAprueba(date("Y-m-d G:i:s"));
                $ucom_current->setFirmaAprueba(1);
                $ucom_current->save();
            }
            //*********************************************************************************************************
            return $ucom_next;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getCurrentUserAsignado($pkcom_id,$esta_asignada=1){
        try {
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
            $c->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,$esta_asignada);
            return EnviadaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getNextUserComByProceso($pkcom_id,$rol_id=0,$excludePks=array()){
        try {
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_id);
            $c->add(EnviadaUsuarioPeer::FIRMA_APRUEBA,0);
            if(count($excludePks)){ $c->add(EnviadaUsuarioPeer::ENVIADAUSUARIO_ID,$excludePks,Criteria::NOT_IN); }
            return EnviadaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function addOrUpdateUserByCom($info_com = array()){
        try {
            if(!empty($info_com['pkcom_id'])){
                $ucom_enviada = EnviadaUsuarioPeer::getUserAddedInCom($info_com['pkcom_id'],$info_com['usuario_id'],$info_com['rol_id']);
                $esta_asignada = isset($info_com['esta_asignada']) ? $info_com['esta_asignada'] : 0;
                //**************************************************************************************************
                if(is_array($ucom_enviada)){
                    foreach ($ucom_enviada as $ucom) {
                        if($ucom->getEstadocomenviadaId() != $info_com['estadocom_id']){
                            $ucom->setEstadocomenviadaId($info_com['estadocom_id']);
                            $ucom->setFechaAsigna(($esta_asignada ? date("Y-m-d G:i:s") : null));
                            $ucom->setEstaAsignada($esta_asignada);
                            $ucom->save();
                        }
                    }
                }elseif($ucom_enviada == null){
                    $ucom_enviada = EnviadaUsuarioPeer::addUserByCom($info_com);
                }elseif($ucom_enviada->getEstadocomenviadaId() != $info_com['estadocom_id']){
                    $ucom_enviada->setEstadocomenviadaId($info_com['estadocom_id']);
                    $ucom_enviada->setFechaAsigna(($esta_asignada ? date("Y-m-d G:i:s") : null));
                    $ucom_enviada->setEstaAsignada($esta_asignada);
                    $ucom_enviada->save();
                }
                //**************************************************************************************************
                return $ucom_enviada;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function addUserByCom($info_com = array()){
        try {
            if(!empty($info_com['pkcom_id'])){
                $esta_asignada = isset($info_com['esta_asignada']) ? $info_com['esta_asignada'] : 0;
                //**************************************************************************************************
                $ucom_object = new EnviadaUsuario();
                $ucom_object->setEstadocomenviadaId($info_com['estadocom_id']);
                $ucom_object->setUsuarioId($info_com['usuario_id']);
                $ucom_object->setComenviadaId($info_com['pkcom_id']);
                $ucom_object->setRoluscomenviadaId($info_com['rol_id']);
                $ucom_object->setCargousuarioId($info_com['cusuario_id']);
                $ucom_object->setTipoprocesocomId($info_com['tipoprocesocom_id']);
                $ucom_object->setFechaAsigna(($esta_asignada ? date("Y-m-d G:i:s") : null));
                $ucom_object->setEstaAsignada($esta_asignada);
                $ucom_object->save();
                //**************************************************************************************************
                return $ucom_object;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getUserAddedInCom($pkcom_id,$usuario_id = 0,$rol_id = 0){
        try {
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::USUARIO_ID,$usuario_id);
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
            //$c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_id);
            if($rol_id > 0){ $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_id); }
            //return EnviadaUsuarioPeer::doSelectOne($c);
            $list_user = EnviadaUsuarioPeer::doSelect($c);
            if(count($list_user) === 1)
                return $list_user[0];
            elseif(count($list_user) === 0)
                return null;
            else
                return $list_user;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getUserComByRol($pkcom_id,$rol_id = 0){
        try {
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_id);
            return EnviadaUsuarioPeer::doSelectOne($c);
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
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
            $sql = sprintf($consulta,EnviadaUsuarioPeer::TABLE_NAME,EnviadaUsuarioPeer::COMENVIADA_ID,EnviadaUsuarioPeer::ENVIADAUSUARIO_ID,implode(",",$lcargos_current));
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function getListUncheckApro($pkcom_id,$isChecked=0,$rol_ids = array(2,4,5))
    {
        $list_objects = array();
        //*************************************************************************************************
        try {
            $c = new Criteria();
            $c->add(EnviadaUsuarioPeer::FIRMA_APRUEBA,$isChecked);
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_ids,Criteria::IN);
            $results = EnviadaUsuarioPeer::doSelect($c);
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
	
	public static function getDevolucionesData($pkcom_id, $rol_ids = array(), $notInUser = array())
    {
        $list_objects = array();
        //*******************************************************************************
        $c = new Criteria();
        $c->setDistinct();
        $c->addJoin(EnviadaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->addJoin(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,RolusComenviadaPeer::ROLUSCOMENVIADA_ID);
        //*******************************************************************************
        $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$pkcom_id);
        $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol_ids,Criteria::IN);
        $c->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1,Criteria::NOT_EQUAL);
        $c->add(EnviadaUsuarioPeer::FIRMA_APRUEBA,1);
        if(count($notInUser)){ $c->add(EnviadaUsuarioPeer::USUARIO_ID,$notInUser,Criteria::NOT_IN);  }
        //*******************************************************************************
        $c->clearSelectColumns();
        $c->addSelectColumn(UsuarioPeer::USUARIO_ID);
        $c->addAsColumn('ROL_NAME',RolusComenviadaPeer::DESCRIPCION);
        $c->addAsColumn('UNOMBRE',UsuarioPeer::NOMBRE);
        $c->addAsColumn('UAPELLIDO',UsuarioPeer::APELLIDO);
        $c->addAsColumn('UROL_ID',EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID);
        $c->addAsColumn('TPCOM_ID',EnviadaUsuarioPeer::TIPOPROCESOCOM_ID);
        //*******************************************************************************
        $list_userscom = UsuarioPeer::doSelectStmt($c);
        //*******************************************************************************
        while ($object = $list_userscom->fetch()) {
            $nombre_apellido = trim($object['UNOMBRE']).' '.trim($object['UAPELLIDO']);
            $list_objects[] = array('USUARIO_ID'=>$object['USUARIO_ID'],'NOMBRE_USER'=>$nombre_apellido,'UTROL'=>$object['ROL_NAME'],'UTROL_ID'=>$object['UROL_ID'],'TPCOM_ID'=>$object['TPCOM_ID']);
        }
        //*******************************************************************************
        $cuser_creador = EnviadaUsuarioPeer::getUserComByRol($pkcom_id,1);
        if($cuser_creador != null){
            $nombre_apellido = trim($cuser_creador->getUsuario()->getNombreApellido());
            $list_objects[] = array('USUARIO_ID'=>$cuser_creador->getUsuarioId(),'NOMBRE_USER'=>$nombre_apellido,
                'UTROL'=>$cuser_creador->getRolusComenviada()->getDescripcion(),'UTROL_ID'=>$cuser_creador->getRoluscomenviadaId(),'TPCOM_ID'=>1);
        }
        //*******************************************************************************
        return $list_objects;
    }
}
