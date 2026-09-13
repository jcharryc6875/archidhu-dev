<?php

/**
 * Subclass for performing query and update operations on the 'usuario' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UsuarioPeer extends BaseUsuarioPeer
{
	static public function getAllUser(){
		$c = UsuarioPeer::getCriteriaPerm();
		//**********************************************************************************************
		$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
		$rs = UsuarioPeer::doSelect($c);
		return $rs; 	
	}
    
	static public function addUserProcessCom($usuario_id, $processcom_id){
		$object = new UsuarioProcesocom();
		$object->setUsuarioId($usuario_id);
		$object->setTipoprocesocomId($processcom_id);
		$object->save();
	}

    static public function getAllUserActive(){
		$c = UsuarioPeer::getCriteriaPerm();
		//**********************************************************************************************
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
		$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
		$rs = UsuarioPeer::doSelect($c);
		return $rs; 	
	}
	
    static public function getAllUserActiveFacturas($procesofact_id=0){
		$c = UsuarioPeer::getCriteriaPerm();
		$c->setDistinct($c);
		//**********************************************************************************************
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,1);
        //**********************************************************************************************
        $c->addJoin(UsuarioPeer::USUARIO_ID,FacturaReceptorPeer::USUARIO_ID);
    	$c->addAscendingOrderByColumn(UsuarioPeer::REGIONAL_ID);
    	$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
    	$c->addAscendingOrderByColumn(UsuarioPeer::APELLIDO);
        $c->add(FacturaReceptorPeer::FACTURAPROCESO_ID,$procesofact_id);
        //**********************************************************************************************		
		$rs = UsuarioPeer::doSelect($c);
        return $rs;		 	
	}
    
	static public function getCriteriaPerm(){		
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
		$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
		$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//**********************************************************************************************
		$listalluserentidad = sfContext::getInstance()->getUser()->checkPerm('LISTAR_USUARIOS_TODAS_ENTIDADES', $usuariologuiado);
		$listalluserregional = sfContext::getInstance()->getUser()->checkPerm('LISTAR_USUARIOS_TODAS_REGIONALES', $usuariologuiado);
		//**********************************************************************************************
		$c = new Criteria();		
		//**********************************************************************************************
		if(!$listalluserentidad){
			if(!$listalluserregional){
				$c->add(UsuarioPeer::REGIONAL_ID,$regional_conectado);
			}else{
				$c->addJoin(UsuarioPeer::REGIONAL_ID, RegionalPeer::REGIONAL_ID);
				$c->add(RegionalPeer::ENTIDAD_ID,$entidad_conectado);
			}
		}
		//**********************************************************************************************
		return $c;
	}
	
	static public function getAllUserActiveSelf(){
        $c = UsuarioPeer::getCriteriaPerm();
        //**********************************************************************************************
        $c->add(UsuarioPeer::ESTADOUSUARIO_ID,2,Criteria::NOT_EQUAL);
        $c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
        //**********************************************************************************************
        $rs = UsuarioPeer::doSelect($c);
        return $rs; 	
	}
	
	static public function getUsuarioPocesoComListText($usuario_id = 0){
        $c = new Criteria();
        $c->add(UsuarioProcesocomPeer::USUARIO_ID,$usuario_id);        
        $rs = UsuarioProcesocomPeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) {
			$array[] = $object->getTipoProcesoCom();
		}
		//**********************************************************************************************
        return $array; 	
	}

	static public function getUsuarioByNuid($nuid)
	{
		$c = new Criteria();
		$c->add(UsuarioPeer::CEDULA, $nuid);
		$c->add(UsuarioPeer::ESTADOUSUARIO_ID, array(1, 3), Criteria::IN);
		$rs = UsuarioPeer::doSelectOne($c);
		//**********************************************************************************************
		return $rs;
	}
	
	static public function getJefeDependencia($dependencia_id)
	{
		$c = new Criteria();
		$c->add(UsuarioPeer::DEPENDENCIA_ID, $dependencia_id);
		$c->add(UsuarioPeer::RECEPTOR_DEP, 1);
		$rs = UsuarioPeer::doSelect($c);
		//**********************************************************************************************
		return $rs === null ? null : $rs[0];
	}
	
	static public function getPerfilesByUsuarioId($usuario_id = 0){
        $c = new Criteria();
        $c->add(RolPorUsuarioPeer::USUARIO_ID,$usuario_id);        
        $rs = RolPorUsuarioPeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) {
			$array[] = $object->getRol()->getDescripcion();
		}
		//**********************************************************************************************
        return $array; 	
	}

	static public function getUsuarioPocesoComList($usuario_id=0){
        $c = new Criteria();
        $c->add(UsuarioProcesocomPeer::USUARIO_ID,$usuario_id);        
        $rs = UsuarioProcesocomPeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) {
			$array[] = $object->getTipoprocesocomId();
		}
		//**********************************************************************************************
        return $array; 	
	}

	static public function getAllProcesoComList()
	{
        $c = new Criteria();       
        $rs = TipoProcesoComPeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) 
		{
			$array[] = $object->getTipoprocesocomId();
		}
		//**********************************************************************************************
        return $array; 	
	}

	static public function getUserDestinoPocesoComByDep($dependencia_id=0, $tipoprocesocom_id=2, $isObject = false){
        $c = new Criteria();
		$c->addJoin(UsuarioProcesocomPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID,Criteria::INNER_JOIN);
		$c->add(UsuarioProcesocomPeer::TIPOPROCESOCOM_ID,$tipoprocesocom_id);
        $c->add(UsuarioPeer::DEPENDENCIA_ID,$dependencia_id);
		$c->add(UsuarioPeer::ESTADOUSUARIO_ID,array(1,3),Criteria::IN);
        $rs = UsuarioProcesocomPeer::doSelectOne($c);
		//**********************************************************************************************
		$cargo_usuario = new CargoUsuario();
		if($rs != null) {
			$cargo_usuario = CargoUsuarioPeer::getCargoUsuarioByIdUser($rs->getUsuarioId(),$isObject);
		}else{
			return null;
		}
		//**********************************************************************************************
        return $isObject ? $cargo_usuario : $cargo_usuario->getPrimaryKey(); 	
	}

	public static function getUserDestinoComByProceso($tipoprocesocom_id=2,$estadousuario_id=1){
        $c = new Criteria();
		$c->addJoin(CargoUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID,Criteria::INNER_JOIN);
		$c->addJoin(UsuarioPeer::USUARIO_ID,UsuarioProcesocomPeer::USUARIO_ID,Criteria::INNER_JOIN);
		$c->add(UsuarioProcesocomPeer::TIPOPROCESOCOM_ID,$tipoprocesocom_id);
		$c->add(UsuarioPeer::ESTADOUSUARIO_ID,$estadousuario_id);
        return CargoUsuarioPeer::doSelect($c);
	}
	
	public static function countValidateTipoFirma($str_firmausers, $useFirmaElectronica = 1, $validCurrent = false)
	{
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
		$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
		$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//************************************************************************************
		$usuarios_firma = preg_split("/[,]+/",trim($str_firmausers), -1, PREG_SPLIT_NO_EMPTY);
		if(!count($usuarios_firma)){ return 0; }
		//************************************************************************************
		if(!$validCurrent){
			if (($key = array_search($usuariologuiado, $usuarios_firma)) !== false) {
				unset($usuarios_firma[$key]);
			}
		}
		//************************************************************************************
		if(!count($usuarios_firma)){
			$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
			return $usuario->getUseFirmaElectronica(); 
		}
		//************************************************************************************
		$conexion = Propel::getConnection();
		$consulta = "SELECT COUNT(%s) AS number_reg FROM %s WHERE %s IN (".implode(",",$usuarios_firma).") AND %s = ".$useFirmaElectronica;
		$consulta = sprintf($consulta, UsuarioPeer::USUARIO_ID, UsuarioPeer::TABLE_NAME,UsuarioPeer::USUARIO_ID, UsuarioPeer::USE_FIRMA_ELECTRONICA);
		//echo $consulta;exit;
		//************************************************************************************
		$sentencia = $conexion->prepare($consulta);
		$sentencia->execute();
		$resultset = $sentencia->fetch(PDO::FETCH_NUM);
		return $resultset[0] ? 1 : 0;
	}

	public static function countValidateFirmaDesatenida($str_firmausers, $useFirmaDesatendida = 1, $validCurrent = false)
	{
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
		//************************************************************************************
		$usuarios_firma = preg_split("/[,]+/",trim($str_firmausers), -1, PREG_SPLIT_NO_EMPTY);
		if(!count($usuarios_firma)){ return 0; }
		//************************************************************************************
		if(!$validCurrent){
			if (($key = array_search($usuariologuiado, $usuarios_firma)) !== false) {
				unset($usuarios_firma[$key]);
			}
		}
		//************************************************************************************
		if(!count($usuarios_firma)){
			$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
			return $usuario->getFirmaDesatendida(); 
		}
		//************************************************************************************
		$conexion = Propel::getConnection();
		$consulta = "SELECT COUNT(%s) AS number_reg FROM %s WHERE %s IN (".implode(",",$usuarios_firma).") AND %s = ".$useFirmaDesatendida;
		$consulta = sprintf($consulta, UsuarioPeer::USUARIO_ID, UsuarioPeer::TABLE_NAME,UsuarioPeer::USUARIO_ID, UsuarioPeer::FIRMA_DESATENDIDA);
		//************************************************************************************
		$sentencia = $conexion->prepare($consulta);
		$sentencia->execute();
		$resultset = $sentencia->fetch(PDO::FETCH_NUM);
		return $resultset[0] == count($usuarios_firma) ? 1 : 0;
	}

	public static function updateReceptorDep($usuario_id, $dependencia_id)
	{
		$conexion = Propel::getConnection();
		$query = "UPDATE %s SET %s = 0 WHERE %s <> %s AND %s = %s;";
		$query = sprintf($query, UsuarioPeer::TABLE_NAME, UsuarioPeer::RECEPTOR_DEP, UsuarioPeer::USUARIO_ID, $usuario_id, UsuarioPeer::DEPENDENCIA_ID, $dependencia_id);
		//************************************************************************************
		$sentencia = $conexion->prepare($query);
		$sentencia->execute();		
	}

	public static function autenticateUserWs($EntSecurity)
	{
		$aFuncVars = array(); 
		$aFuncVars['SCRIPT_NAME'] = !empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : "(none)";
		$aFuncVars['PATH_INFO'] = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : "(none)";
		$aFuncVars['REQUEST_METHOD'] = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "(none)";
		$aFuncVars['REQUEST_TIME'] = !empty($_SERVER['REQUEST_TIME']) ? date('Y-m-d G:i:s', $_SERVER['REQUEST_TIME']) : date('Y-m-d G:i:s');
		$aFuncVars['HTTP_USER_AGENT'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "(none)";
		//*************************************************************************************************
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $aFuncVars['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $aFuncVars['HTTP_CLIENT_IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		  $aFuncVars['HTTP_CLIENT_IP'] = $_SERVER['REMOTE_ADDR'];
		}
		//*************************************************************************************************
		try {
			$SimadUserWs = isset($EntSecurity['SimadUserWs']) ? trim($EntSecurity['SimadUserWs']) : null;
			$SimadPassWs = isset($EntSecurity['SimadPassWs']) ? html_entity_decode(trim($EntSecurity['SimadPassWs'])) : null;
			$SimadTypeGen = isset($EntSecurity['SimadTypeGen']) ? trim($EntSecurity['SimadTypeGen']) : null;
			//*********************************************************************************************
			if(empty($SimadUserWs)){ return array('isError'=>true,'message'=>'Error de credenciales', 'object' => null); }
			if(empty($SimadPassWs)){ return array('isError'=>true,'message'=>'Error de credenciales', 'object' => null); }
			if(empty($SimadTypeGen)){ return array('isError'=>true,'message'=>'Error de credenciales', 'object' => null); }
			//*********************************************************************************************
			$aFuncVars['USER_NAME'] = $SimadUserWs;
			//*********************************************************************************************
			$c = new Criteria();
			$c->add(UsuarioPeer::USER_NAME, $SimadUserWs);
			$user = UsuarioPeer::doSelectOne($c);
			//*********************************************************************************************
			if(empty($user)){ 
				$aFuncVars['LOGIN_STATUS'] = "ERROR_USER_NAME";
				AccesoLogPeer::addAuditAccess($aFuncVars);
				return array('isError'=>true,'message'=>'Error de credenciales1', 'object' => null); 
			}
			//*********************************************************************************************
			if($SimadTypeGen == 0){
				$read_sections = array('keyprivate_svc_password');
				$ini_array = simad_util::readConfigFileApp($read_sections);
				$keyprivate_svc = isset($ini_array['keyprivate_svc_password']) ? $ini_array['keyprivate_svc_password'] : null;
				//*****************************************************************************************
				$sha256pass = hash('sha256', $user->getPassword().$keyprivate_svc);
				if ($sha256pass != $SimadPassWs){
					$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
					AccesoLogPeer::addAuditAccess($aFuncVars);
					return array('isError'=>true,'message'=>'Error de credenciales2', 'object' => null); 
				}
			}else{
				$sha1pass = sha1($user->getSalt(). $SimadPassWs);
				if ($sha1pass != $user->getPassword()){
					$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
					AccesoLogPeer::addAuditAccess($aFuncVars);
					return array('isError'=>true,'message'=>'Error de credenciales3', 'object' => null); 
				}
			}
			//*********************************************************************************************
			if ($user->getEstadousuarioId() != 1){
				$aFuncVars['LOGIN_STATUS'] = "USER_LOCKED_DISABLED_DISCONTINUED";
				AccesoLogPeer::addAuditAccess($aFuncVars);
				return array('isError'=>true,'message'=>'Error de credenciales3', 'object' => null);
			}
			//*********************************************************************************************
			//if ($user->getIntentos() <= $num_intentos ){}
			//*********************************************************************************************
			$aFuncVars['LOGIN_STATUS'] = "SUCCESS";
			AccesoLogPeer::addAuditAccess($aFuncVars);
			return array('isError'=>false,'message'=>'Usuario autenticado en el SGDEA', 'object' => $user);
		}catch (\Throwable $th) {
			$aFuncVars['LOGIN_STATUS'] = "SERVER_ERROR_ACTIVITY";
			AccesoLogPeer::addAuditAccess($aFuncVars);
			return array('isError'=>true,'message'=>$th->getMessage(), 'object' => null);
		}
	}

	public static function deleteUserProccessCom($usuario_id=0)
	{
		if($usuario_id){
			$conexion = Propel::getConnection();
			$query = "DELETE %s WHERE %s = %s;";
			$query = sprintf($query, UsuarioProcesocomPeer::TABLE_NAME, UsuarioProcesocomPeer::USUARIO_ID, $usuario_id);
			//************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
		}	
	}

	public static function addHistoricoUser(Usuario $usuario)
	{
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
		//guarda en historico
		$hist = new HistUsuario();
        $hist->fromArray($usuario->toArray());
        $hist->setFechaModificacion(date('Y-m-d G:i:s'));
        $hist->save();
		//**********************************************************************************************
        $rolhist = new UsUsHistorico();
        $rolhist->setUsuarioId($usuariologuiado);
        $rolhist->setRolushistoricoid(1);
        $rolhist->setHistusuarioId($hist->getPrimaryKey());
        $rolhist->save();
		//**********************************************************************************************
        $rolhist = new UsUsHistorico();
        $rolhist->setUsuarioId($usuario->getPrimaryKey());
        $rolhist->setRolushistoricoid(2);
        $rolhist->setHistusuarioId($hist->getPrimaryKey());
        $rolhist->save();
	}

	public static function verificarActiveDirectory($nickname)
	{
		try {
			$config = require_once(sfConfig::get('sf_config_dir'). DIRECTORY_SEPARATOR . 'ldap_settings.php');
			//******************************************************************************************
			$config_controllers = simad_util::readConfigFileApp(['domain_controllers']);
			$domains_controllers = isset($config_controllers['domain_controllers']) ? $config_controllers['domain_controllers'] : null;
			//******************************************************************************************
			if(!empty($domains_controllers)){
				$config['hosts'] = $domains_controllers;
			}
			//******************************************************************************************
			$auth = null;
			try {
				$auth = new AdldapAuth($config);
				if(empty($auth)){
					return 0;
				}
			} catch (\Adldap\AdldapException $th) {
				return 0;
			} catch (\Exception $th) {
				return 0;
			}
			//******************************************************************************************
			$user_ldap = $auth->findUser($nickname);
			//******************************************************************************************
			if($user_ldap != null){
				$isActiveUser = $auth->isUserActive($user_ldap);
				if($isActiveUser['activo'] === true){
					return 1;
				}else{
					return 0;
				}
			}else{
				return [
                    'success' => false,
                    'message' => 'El usuario no existe en el directorio activo',
                    'code' => 'USER_LDAP_ERROR_NOTFOUND'
                ];
			}
		} catch (PropelException $th) {
			return 0;
		} catch (\Exception $th) {
			return 0;
		} catch (\Throwable $th) {
			return 0;
		}
	}
	
	public static function getTotalRegPend($periodo_id = null, $usuarioEscogido, $process_usuario, $concepto = null, $ifCriteria = false) // a UsuarioPeer
    {
		$usuario = UsuarioPeer::retrieveByPk($usuarioEscogido);
        $totalRegPendientes = array();

		$dias = 3;
		$fecha_vence = AddDays(date("Y-m-d"), $dias);	
		 ///////////////////////////////////////////// COMUNICACIONES RECIBIDAS  TABLA: COMRECIBIDA ///////////////////////////////////////////////////////
		//**********************************************Comunicaciones Recibidas Por Leer*******************************************
		$e = new Criteria();
		$e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$e->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,1);
		$e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		$e->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
		$e->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$e->add(ComRecibidaPeer::IS_LOCKED,0);
		$e->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$e->add(ComRecibidaPeer::PERIODO_ID,$periodo_id); 
		
		if(!empty($concepto))
		{
			if($concepto == 'recibidas_leer')
			{
				if($ifCriteria)
				{
					return array('criteria' => $e, 'modelo' => 'ComRecibida');
}
				else
				{
					$totalRegPendientes['recibidas_leer'] = ComRecibidaPeer::doSelect($e);
				}
			}	
		}
		else
		{
			$totalRegPendientes['recibidas_leer'] = ComRecibidaPeer::doCount($e);
		}
		//***********************************************Comunicaciones Recibidas Vencidas******************************************
		$estadocom_notin = array(5,7,11,12,13,14);
		$f = new Criteria();    
		$f->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$f->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$f->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		$f->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
		$f->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
		$f->add(ComRecibidaPeer::IS_LOCKED,0);
		$f->add(ComRecibidaPeer::MARCA_VINCULACION,0);
		$f->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$f->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 23:59:59"),Criteria::LESS_THAN);
		
		if(!empty($concepto))
		{
			if($concepto == 'vencidas')
			{
				if($ifCriteria)
				{
					return array('criteria' => $f, 'modelo' => 'ComRecibida');
				}
				else
				{
					$totalRegPendientes['vencidas'] = ComRecibidaPeer::doSelect($f);
				}
			}	
		}
		else
		{
			$totalRegPendientes['vencidas'] = ComRecibidaPeer::doCount($f);
		}
		//***********************************************Comunicaciones Recibidas Por Vencer****************************************
		$estadocom_notin = array(5,7,11,13,14);
		$g = new Criteria();    
		$g->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$g->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$g->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		$g->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, $fecha_vence, Criteria::LESS_THAN);
		$g->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 00:00:00"),Criteria::GREATER_THAN);
		$g->add(ComRecibidaPeer::IS_LOCKED,0);
		$g->add(ComRecibidaPeer::MARCA_VINCULACION,0);
		$g->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$g->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
		$g->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
		
		if(!empty($concepto))
		{			
			if($concepto == 'por_vencer')
			{
				if($ifCriteria)
				{
					return array('criteria' => $g, 'modelo' => 'ComRecibida');
				}
				else
				{
					$totalRegPendientes['por_vencer'] = ComRecibidaPeer::doSelect($g);
				}
			}
		}
		else
		{
			$totalRegPendientes['por_vencer'] = ComRecibidaPeer::doCount($g);
		}
		//******************************** Comunicaciones Recibidas Copias**********************************************************
		$h1 = new Criteria();    
		$h1->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$h1->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$h1->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1); // agregada Elvis
		$h1->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
		$h1->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
		$h1->add(ComRecibidaPeer::IS_LOCKED,0);

		if(!empty($concepto))
		{			
			if($concepto == 'recibida_copia')
			{
				if($ifCriteria)
				{
					return array('criteria' => $h1, 'modelo' => 'ComRecibida');
				}
				else
				{
					$totalRegPendientes['recibida_copia'] = ComRecibidaPeer::doSelect($h1);
				}
			}
		}
		else
		{
			$totalRegPendientes['recibida_copia'] = ComRecibidaPeer::doCount($h1);
		}
		//******************************** Comunicaciones Recibidas Respuestas******************************************************
		$estadocom_notresp = array(5,7,11,13,14);
		$h2 = new Criteria();    
		$h2->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$h2->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);		
		$h2->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		$h2->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notresp,Criteria::NOT_IN);
		$h2->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notresp,Criteria::NOT_IN);
		
		$h2->add(ComRecibidaPeer::IS_LOCKED,0);
		//$h2->add(ComRecibidaPeer::MARCA_VINCULACION,0);
		$h2->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$h2->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
		//*******************************************************************************
		$h2cton0 = $h2->getNewCriterion(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
		$h2cton1 = $h2->getNewCriterion(ComRecibidaPeer::COMENVIADA_ID,0);  	
		$h2cton0->addOr($h2cton1);
		$h2->add($h2cton0);
		
		if(!empty($concepto))
		{			
			if($concepto == 'comrecibida_responder')
			{
				if($ifCriteria)
				{
					return array('criteria' => $h2, 'modelo' => 'ComRecibida');
				}
				else
				{
					$totalRegPendientes['comrecibida_responder'] = ComRecibidaPeer::doSelect($h2);
				}
			}
		}
		else
		{
			$totalRegPendientes['comrecibida_responder'] = ComRecibidaPeer::doCount($h2);
		}
		//******************************** Comunicaciones Recibidas Distribucion****************************************************
		if(in_array(2,$process_usuario))
		{
			$estadocom_notin = array(5,7,11,13,14);
			$h3 = new Criteria();
			$h3->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
			$h3->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
			$h3->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
			$h3->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,2);
			$h3->add(ComRecibidaPeer::IS_LOCKED,0);
			$h3->add(ComRecibidaPeer::MARCA_VINCULACION,0);
			$h3->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
			$h3->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
			$h3->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
			$h3->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
			//$this->por_distribuir = ComRecibidaPeer::doCount($h3);
			//$totalRegPendientes['por_distribuir'] = ComRecibidaPeer::doCount($h3);


			if(!empty($concepto))
			{
				
				if($concepto == 'por_distribuir')
				{
					

					if($ifCriteria)
					{
						return array('criteria' => $h3, 'modelo' => 'ComRecibida');
					}
					else
					{
						$totalRegPendientes['por_distribuir'] = ComRecibidaPeer::doSelect($h3);
					}
				}
			}
			else
			{
				$totalRegPendientes['por_distribuir'] = ComRecibidaPeer::doCount($h3);
			}

		}
		else
		{ 
			//$this->por_distribuir = null; 
			$totalRegPendientes['por_distribuir'] = null;
		}

		//******************************** Comunicaciones Recibidas Por Gestionar **********************************************************
		if(in_array(3, $process_usuario))
		{
			$estadocom_notin = array(5,7,11,13,14);
			
            $h4 = new Criteria();    
            $h4->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $h4->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuarioEscogido);
            $h4->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $h4->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,3);
            $h4->add(ComRecibidaPeer::IS_LOCKED,0);
            $h4->add(ComRecibidaPeer::MARCA_VINCULACION,0);
            $h4->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
            $h4->add(ComRecibidaPeer::PERIODO_ID, $periodo_id);
            //$h4->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,5,Criteria::NOT_EQUAL);
			$h4->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
			$h4->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$estadocom_notin,Criteria::NOT_IN);
            //$this->por_gestionar = ComRecibidaPeer::doCount($h4);

			if(!empty($concepto))
			{
				if($concepto == 'por_gestionar')
				{
					if($ifCriteria)
					{
						return array('criteria' => $h4, 'modelo' => 'ComRecibida');
					}
					else
					{
						$totalRegPendientes['por_gestionar'] = ComRecibidaPeer::doSelect($h4);
					}
				}
			}
			else
			{
				$totalRegPendientes['por_gestionar'] = ComRecibidaPeer::doCount($h4);
			}			
        }
		else
		{ 
			$totalRegPendientes['por_gestionar'] = null; 
		}
		 ///////////////////////////////////////////// COMUNICACIONES INTERNAS  TABLA: COMINTERNA ///////////////////////////////////////////////////////
        //****************************************Comunicaciones internas Por Leer**************************************************
        $c = new Criteria();	
        //$c->setDistinct();
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,2);//por leer
        //$c->addOr(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,5);//por responder
        $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$c->add(CominternaUsuarioPeer::ESTA_ASIGNADA, 1); // agregada Elvis
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $c->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        //$this->por_leer = ComInternaPeer::doCount($c);
        //$totalRegPendientes['por_leer'] = ComInternaPeer::doCount($c);

		if(!empty($concepto))
		{
			
			if($concepto == 'por_leer')
			{
				
				if($ifCriteria)
				{
					return array('criteria' => $c, 'modelo' => 'ComInterna');
				}
				else
				{
					$totalRegPendientes['por_leer'] = ComInternaPeer::doSelect($c);
				}
			}
		}
		else
		{
			$totalRegPendientes['por_leer'] = ComInternaPeer::doCount($c);
		}
        //****************************************Comunicaciones internas por responder*********************************************
        $r = new Criteria();	
        //$c->setDistinct();
        $r->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $r->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,5); //por responder
		$r->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);  // agregada Elvis
        $r->add(CominternaUsuarioPeer::USUARIO_ID,$usuarioEscogido);    
        $r->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $r->add(ComInternaPeer::PERIODO_ID,$periodo_id);

		if(!empty($concepto))
		{
			if($concepto == 'por_responder')
			{
				if($ifCriteria)
				{
					return array('criteria' => $r, 'modelo' => 'ComInterna');
				}
				else
				{
					$totalRegPendientes['por_responder'] = ComInternaPeer::doSelect($r);
				}
			}
		}
		else
		{
			$totalRegPendientes['por_responder'] = ComInternaPeer::doCount($r);
		}
        //********************************************Comunicaciones Internas Copias************************************************
        $a = new Criteria();
        $a->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $a->add(CominternaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$a->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);  // agregada Elvis
        $a->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $a->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3); // COPIA
		
		if(!empty($concepto))
		{
			if($concepto == 'copia')
			{
				
				if($ifCriteria)
				{
					return array('criteria' => $a, 'modelo' => 'ComInterna');
				}
				else
				{
					$totalRegPendientes['copia'] = ComInternaPeer::doSelect($a);
				}
			}
		}
		else
		{
			$totalRegPendientes['copia'] = ComInternaPeer::doCount($a);
		}
        //****************************************************************************************************************************	
		///////////////////////////////////////////// COMUNICACIONES ENVIADAS  TABLA: COMENVIADA ///////////////////////////////////////////////////////	
		//***********************************************Comunicaciones Enviadas*****************************************************
        $d = new Criteria();    
        $d->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $d->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $d->add(EnviadaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
		$d->add(EnviadaUsuarioPeer::ESTA_ASIGNADA, 1); //agregada Elvis
        $d->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(4,5,1),Criteria::NOT_IN);
        $d->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);

		if(!empty($concepto))
		{
			
			if($concepto == 'enviadas')
			{
				
				if($ifCriteria)
				{
					return array('criteria' => $d, 'modelo' => 'ComEnviada');
				}
				else
				{
					$totalRegPendientes['enviadas'] = ComEnviadaPeer::doSelect($d);
				}
			}
		}
		else
		{
			$totalRegPendientes['enviadas'] = ComEnviadaPeer::doCount($d);
		}
		

		//**********************************Comunicaciones Eviadas Copias Informativas***********************************************
        $b = new Criteria();
        $b->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $b->add(EnviadaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
        $b->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);  // agregada Elvis 
        $b->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,3);
        $b->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(4,1),Criteria::NOT_IN);
        $b->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        //$this->copia_informativa = ComEnviadaPeer::doCount($b);
		//$totalRegPendientes['copia_informativa'] = ComEnviadaPeer::doCount($b);

		if(!empty($concepto))
		{
			
			if($concepto == 'copia_informativa')
			{
				if($ifCriteria)
				{
					return array('criteria' => $b, 'modelo' => 'ComEnviada');
				}
				else
				{
					$totalRegPendientes['copia_informativa'] = ComEnviadaPeer::doSelect($b);
				}
			}
		}
		else
		{
			$totalRegPendientes['copia_informativa'] = ComEnviadaPeer::doCount($b);
		}
		
		//***********************************************Comunicaciones Enviadas gestor salida***************************************
		/**/
        $gs = new Criteria();    
        $gs->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $gs->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $gs->add(ComEnviadaPeer::SERVICIO_ID,null,Criteria::ISNULL);
		$gs->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);  // agregada Elvis
		$gs->add(EnviadaUsuarioPeer::USUARIO_ID,$usuarioEscogido);  // agregada Elvis
        $gs->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,2);
        $gs->addOr(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,3);
        $gs->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2); 

        if(sfContext::getInstance()->getUser()->checkPerm("COM_ENVIADA_LIST_DEPENDENCIA", $usuarioEscogido))
		{
            $gs->add(ComEnviadaPeer::DEPENDENCIA_ID,$usuario->getDependenciaId());
        }else
		{
            $gs->add(EnviadaUsuarioPeer::USUARIO_ID,$usuarioEscogido);
        }
        //$this->enviadas_gsalida = ComEnviadaPeer::doCount($gs);
		//$totalRegPendientes['enviadas_gsalida'] = ComEnviadaPeer::doCount($gs);
		
		if(!empty($concepto))
		{
			
			if($concepto == 'enviadas_gsalida')
			{
				
				if($ifCriteria)
				{
					return array('criteria' => $gs, 'modelo' => 'ComEnviada');
				}
				else
				{
					$totalRegPendientes['enviadas_gsalida'] = ComEnviadaPeer::doSelect($gs);
				}
			}
		}
		else
		{
			$totalRegPendientes['enviadas_gsalida'] = ComEnviadaPeer::doCount($gs);
		}
		///////////////////////////////////////////// PRESTAMO  TABLA: PRESTAMO ///////////////////////////////////////////////////////	
		//*********************************Archivo Prestamos************************************************************************
        $apr = new Criteria();
        $apr->setDistinct();
        $apr->addJoin(PrestamoPeer::PRESTAMO_ID,DetallePrestamoPeer::PRESTAMO_ID);
        $apr->add(PrestamoPeer::ESTADOPRESTAMO_ID,1);
        $apr->add(PrestamoPeer::USUARIO_ID,$usuarioEscogido);
        //$this->prestamos_pendientes = PrestamoPeer::doCount($apr);
		//$totalRegPendientes['prestamos_pendientes'] = PrestamoPeer::doCount($apr);

		if(!empty($concepto))
		{
			
			if($concepto == 'prestamos_pendientes')
			{
				

				if($ifCriteria)
				{
					return array('criteria' => $apr, 'modelo' => 'Prestamo');
				}
				else
				{
					$totalRegPendientes['prestamos_pendientes'] = PrestamoPeer::doSelect($apr);
				}
			}
		}
		else
		{
			$totalRegPendientes['prestamos_pendientes'] = PrestamoPeer::doCount($apr);
		}
		
		return $totalRegPendientes;
    }
}
