<?php
include_once(sfConfig::get('sf_lib_dir')."/Fechas.class.php");
/**
 * security actions.
 *
 * @package    simad
 * @subpackage security
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class securityActions extends sfActions
{
	public static $aFuncVars = array(); 

	public function preExecute()
	{
		$server_vars = array();
		$server_vars['SCRIPT_NAME'] = !empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : "(none)";
		$server_vars['PATH_INFO'] = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : "(none)";
		$server_vars['REQUEST_METHOD'] = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "(none)";
		$server_vars['REQUEST_TIME'] = !empty($_SERVER['REQUEST_TIME']) ? date('Y-m-d G:i:s', $_SERVER['REQUEST_TIME']) : date('Y-m-d G:i:s');
		$server_vars['HTTP_USER_AGENT'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "(none)";

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $server_vars['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $server_vars['HTTP_CLIENT_IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		  $server_vars['HTTP_CLIENT_IP'] = $_SERVER['REMOTE_ADDR'];
		}
		securityActions::$aFuncVars = array_merge(securityActions::$aFuncVars, $server_vars);
		/*$output = implode(', ', array_map(
		  function ($v, $k) { return sprintf("%s => '%s'", $k, $v); },
		  $server_vars,
		  array_keys($server_vars)
		));*/

		//$logfilename = sfConfig::get("sf_log_dir"). DIRECTORY_SEPARATOR ."access_app.log";
		//simad_util::writetolog($logfilename,$output);
	}
	
	public function handleErrorRecuperar()
	{
	  return sfView::SUCCESS;
	}

	public function executeRecuperar()
	{
	  if ($this->getRequest()->getMethod() != sfRequest::POST)
	  {
		 // display the form
		 $this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
		 //return sfView::SUCCESS;
	  }else{
		$nickname = $this->getRequestParameter('username');
		$email    = $this->getRequestParameter('email');
		
		$c = new Criteria();
		$c->add(UsuarioPeer::USER_NAME, $nickname);
		$user = UsuarioPeer::doSelectOne($c);
			
		$generated_pass=substr(md5(rand(100000,999999).$nickname . $email ),0,8);
		$str_message='Buenos dias, atendiendo su requerimiento, el nuevo password para el usuario  '. $nickname .' es ' . $generated_pass  ;
		
		if($user && $user->getEmail() == $email ){
			//enviar la clave por correo...
			$this->pear_mail($email,"Recuperacion clave SIMAD", $str_message);
			
			$this->setSha1Password($user->getUsuarioId(),$generated_pass);
			
			//$user->setPassword($generated_pass);
			//$user->save();
			$this->redirect('security/ok');    
		}else{
			return sfView::ERROR;
		}
	  }
	}
	
	public function executeOk()
	{
	  return sfView::SUCCESS;
	}

	public function handleErrorPasswd()
	{
	  return sfView::SUCCESS;
	}

	public function executePasswd()
	{
	  if ($this->getRequest()->getMethod() != sfRequest::POST)
	  {
		// display the form
		 $this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
		 return sfView::SUCCESS;
	  }	else{
		 //handle form submission...
		 //update password.
		$nickname = $this->getRequestParameter('username');
		$c = new Criteria();
		$c->add(UsuarioPeer::USER_NAME, $nickname);
		$user = UsuarioPeer::doSelectOne($c);
		if($user){
			$newpassword=$this->getRequestParameter('newpassword1');
			$this->setSha1Password($user->getUsuarioId(), $newpassword);
		}else{
			echo "Usuario NO encontrado. Error Actualizando.";
		}
		//return $this->redirect($this->getRequestParameter('referer','@homepage'));
		  $this->redirect('security/ok');	
	  }
	}

	public function executeLogin()
	{
		$this->setLayout(false);
		$this->postbackurl = $this->getRequestParameter('postbackurl');
		if ($this->getRequest()->getMethod() != sfRequest::POST){
			// display the form
			$this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
			$this->msgError = $this->getRequestParameter('msgError');
			//*******************************************************************************************
			$this->getUser()->setAuthenticated(false);        
			$this->getUser()->setAttribute('usuario_id', '', 'subscriber');
			$this->getUser()->setAttribute('username', '', 'subscriber');
			$this->getUser()->setAttribute('entidad_id', '', 'subscriber');
			$this->getUser()->setAttribute('regional_id', '', 'subscriber');
			$this->getUser()->setAttribute('nombre_apellido', '', 'subscriber');
			$this->getUser()->setAttribute('ruta_foto', '', 'subscriber');
			//*******************************************************************************************
			$this->getUser()->setAttribute('com_interna_advance_create','',  'subscriber');
			$this->getUser()->setAttribute('com_enviada_advance_create','',  'subscriber');
			$this->getUser()->setAttribute('badge_init','',  'subscriber');
			$this->getUser()->setAttribute('view_storage_information','',  'subscriber');
			$this->getUser()->setAttribute('badge_view_notify','',  'subscriber');
			//*******************************************************************************************
			$this->getUser()->setAttribute('firma_mecanica','',  'subscriber');
			$this->getUser()->setAttribute('firma_digital','',  'subscriber');
			$this->getUser()->setAttribute('firma_desatendida','',  'subscriber');
			$this->getUser()->setAttribute('config_advanced_firma', '', 'subscriber');
			$this->getUser()->setAttribute('image_licencia', '', 'subscriber');
			$this->getUser()->setAttribute('submit_time', '', 'subscriber');
			//*******************************************************************************************
			$this->getUser()->setAttribute('login_time','',  'subscriber');
			$this->getUser()->setAttribute('session_record_id', '', 'subscriber');
			$this->getUser()->setAttribute('session_start_time', '', 'subscriber');
			$this->getUser()->setAttribute('session_last_activity', '', 'subscriber');
		}else{
			$nickname = trim($this->getRequestParameter('username'));
			$post_pass = trim($this->getRequestParameter('password'));
			securityActions::$aFuncVars['USER_NAME'] = $nickname;
			//********************************************************************************************
			// handle the form submission
			$c = new Criteria();
			$c->add(UsuarioPeer::USER_NAME, $nickname);
			$user = UsuarioPeer::doSelectOne($c);
			//********************************************************************************************
			if(empty($user) || empty($user->getPrimaryKey())){
				$resp = array();
				$resp['login_status'] = "invalid";
				//$resp['error_mensaje'] = "Acceso denegado. El usuario no esta registrado";
				$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
				//*****************************************************************************
				securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_NOT_EXISTS";
				AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);              
				return $this->renderText(json_encode($resp));
			}
			//********************************************************************************************
			// Verificacion con active directory por tabla parametros
			//$activeDirectory = ParametroPeer::retrieveByPK(47);
			//********************************************************************************************
			if($user->getTipoautenticacionId() === UserAuthType::LdapNativo){
				$response_ldap = $user->ldapNativoAuth($post_pass);
				//****************************************************************************************
				if ($response_ldap['success'] === false) {
					$resp['login_status'] = "invalid";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales7, ".$response_ldap['message'];
					//************************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = $response_ldap['code'];
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
					//************************************************************************************
					if(in_array($response_ldap['code'], array("USER_LDAP_ERROR_DISABLED","USER_LDAP_ERROR_EXPIRED"))){
						$user->setEstadousuarioId(4);
						$user->save();
						//********************************************************************************
						$resp['error_mensaje'] = "Acceso denegado, error de credenciales8";
					}
					//************************************************************************************
					return $this->renderText(json_encode($resp));
				}else{
					$this->getUser()->setAuthenticated(true);
					$this->getUser()->addCredential('subscriber');
					$this->getUser()->setAttribute('usuario_id', trim($user->getUsuarioId()), 'subscriber');
					$this->getUser()->setAttribute('username',   trim($user->getUserName()),  'subscriber');
					$this->getUser()->setAttribute('entidad_id',trim($user->getRegional()->getEntidadId()),  'subscriber');
					$this->getUser()->setAttribute('regional_id',trim($user->getRegionalId()),  'subscriber');
					$this->getUser()->setAttribute('nombre_apellido',trim($user->getNombre().' '.$user->getApellido()),  'subscriber');
					$this->getUser()->setAttribute('ruta_foto',trim($user->getRutaFoto()),  'subscriber');
					$this->getUser()->setAttribute('login_time', date("Y-m-d G:i:s"),  'subscriber');
					//************************************************************************************
					$com_interna_advance_create = $this->getUser()->checkPerm("COM_INTERNA_ADVANCE_CREATE", $user->getUsuarioId());
					$com_enviada_advance_create = $this->getUser()->checkPerm("COM_ENVIADA_ADVANCE_CREATE", $user->getUsuarioId());
					$com_recibida_view_notify = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA", $user->getUsuarioId());
					$com_recibida_view_notify_all = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA_ALL", $user->getUsuarioId());
					$config_advanced_firma = $this->getUser()->checkPerm("CONFIGURACION_ADVANCED_FIRMA", $user->getUsuarioId());
					$view_storage_information = $this->getUser()->checkPerm("estadistica/storageInfo", $user->getUsuarioId());
					//************************************************************************************
					sfConfig::set('com_interna_advance_create', $com_interna_advance_create);
					sfConfig::set('com_enviada_advance_create', $com_enviada_advance_create);
					$this->getUser()->setAttribute('badge_view_notify',($com_recibida_view_notify_all ? 1 : ($com_recibida_view_notify ? 2 : "")),  'subscriber');
					$this->getUser()->setAttribute('view_storage_information',($view_storage_information ? 1 : 0),  'subscriber');
					//************************************************************************************
					$this->getUser()->setAttribute('firma_mecanica',$user->getUseFirmaElectronica(), 'subscriber');
					$this->getUser()->setAttribute('firma_digital',$user->getFirmaDigital(),  'subscriber');
					$this->getUser()->setAttribute('firma_desatendida',$user->getFirmaDesatendida(), 'subscriber');
					$this->getUser()->setAttribute('config_advanced_firma', $config_advanced_firma, 'subscriber');
					//************************************************************************************
					$time_espera = ParametroPeer::retrieveByPK(79)->getValorNumerico();
					$this->getUser()->setAttribute('submit_time', (!empty($time_espera) ? $time_espera : 30), 'subscriber');
					//************************************************************************************
					$image_entidad = trim($user->getRegional()->getEntidad()->getLogoCorporativo());
					$base_image = "images";
					$licencia_image = $image_entidad ? $base_image."/encabezado_carta/logos_carnet/".$image_entidad : $base_image."/lincenciadoa.jpg";
					$this->getUser()->setAttribute('image_licencia', $licencia_image, 'subscriber');
					$url_redirect = '/backend.php/resumen';
					//************************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LDAP_SUCCESS";
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
					//************************************************************************************
					SessionManager::startUniqueSession($user->getUsuarioId());
					//************************************************************************************
					$resp = array();
					$resp['login_status'] = "success";
					$resp['redirect_url'] = $url_redirect;
					//************************************************************************************
					UsuarioNotificacionPeer::notifyUserTask(array($user->getUsuarioId()),ModuleEnableNotify::Seguridad,null,RolComTypeNotify::TrustInicioSesionday);
					//************************************************************************************
					return $this->renderText(json_encode($resp));
				}
			}else{
				// user exists?
				if ($user){
					$sha1pass = sha1($user->getSalt(). $this->getRequestParameter('password'));
					$num_intentos = ParametroPeer::retrieveByPK(72)->getValorNumerico();
					//password is OK?
					if ($sha1pass  == $user->getPassword()  && $user->getPassword()!="" && in_array($user->getEstadousuarioId(),array(1,3)) && $user->getIntentos() <= $num_intentos ){
						$this->getUser()->setAuthenticated(true);
						$this->getUser()->addCredential('subscriber');
						$this->getUser()->setAttribute('usuario_id', $user->getUsuarioId(), 'subscriber');
						$this->getUser()->setAttribute('username',   $user->getUserName(),  'subscriber');
						$this->getUser()->setAttribute('entidad_id',$user->getRegional()->getEntidadId(),  'subscriber');
						$this->getUser()->setAttribute('regional_id',$user->getRegionalId(),  'subscriber');
						$this->getUser()->setAttribute('nombre_apellido',$user->getNombre().' '.$user->getApellido(),  'subscriber');
						$this->getUser()->setAttribute('ruta_foto',$user->getRutaFoto(),  'subscriber');
						$this->getUser()->setAttribute('login_time', date("Y-m-d G:i:s"),  'subscriber');
						//*******************************************************************************************************
						$time_espera = ParametroPeer::retrieveByPK(79)->getValorNumerico();
						$this->getUser()->setAttribute('submit_time', (!empty($time_espera) ? $time_espera : 30), 'subscriber');
						//*******************************************************************************************************
						$com_interna_advance_create = $this->getUser()->checkPerm("COM_INTERNA_ADVANCE_CREATE", $user->getUsuarioId());
						$com_enviada_advance_create = $this->getUser()->checkPerm("COM_ENVIADA_ADVANCE_CREATE", $user->getUsuarioId());
						$com_recibida_view_notify = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA", $user->getUsuarioId());
						$com_recibida_view_notify_all = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA_ALL", $user->getUsuarioId());
						$config_advanced_firma = $this->getUser()->checkPerm("CONFIGURACION_ADVANCED_FIRMA", $user->getUsuarioId());
						$view_storage_information = $this->getUser()->checkPerm("estadistica/storageInfo", $user->getUsuarioId());
						//*******************************************************************************************************
						$this->getUser()->setAttribute('com_interna_advance_create',$com_interna_advance_create,  'subscriber');
						$this->getUser()->setAttribute('com_enviada_advance_create',$com_enviada_advance_create,  'subscriber');
						$this->getUser()->setAttribute('badge_view_notify',($com_recibida_view_notify_all ? 1 : ($com_recibida_view_notify ? 2 : "")),  'subscriber');
						$this->getUser()->setAttribute('view_storage_information',($view_storage_information ? 1 : 0),  'subscriber');
						//*******************************************************************************************************
						$this->getUser()->setAttribute('firma_mecanica',$user->getUseFirmaElectronica(), 'subscriber');
						$this->getUser()->setAttribute('firma_digital',$user->getFirmaDigital(),  'subscriber');
						$this->getUser()->setAttribute('firma_desatendida',$user->getFirmaDesatendida(), 'subscriber');
						$this->getUser()->setAttribute('config_advanced_firma', $config_advanced_firma, 'subscriber');
						//*******************************************************************************************************
						$image_entidad = trim($user->getRegional()->getEntidad()->getLogoCorporativo());
						$base_image = "images";
						$licencia_image = $image_entidad ? $base_image."/encabezado_carta/logos_carnet/".$image_entidad : $base_image."/lincenciadoa.jpg";
						$this->getUser()->setAttribute('image_licencia', $licencia_image, 'subscriber');
						//*******************************************************************************************************
						$dias = ParametroPeer::retrieveByPK(1)->getValorNumerico();
						$fecha = explode(" ", $user->getFechaActualizacion());
						$fecha = AddDays($fecha[0],$dias);
						$resp = array();
						//*******************************************************************************************************	
						if($fecha > date("Y-m-d")){
							$user->setIntentos(0);
							$user->save();
							//***************************************************************************************************
							$url_redirect = '/backend.php/resumen';
							//***************************************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "SUCCESS";
							//***************************************************************************************************
							SessionManager::startUniqueSession($user->getUsuarioId());
							//***************************************************************************************************
							$resp['login_status'] = "success";
							$resp['redirect_url'] = $url_redirect;
						}else{
							$resp['login_status'] = "success";
							$resp['redirect_url'] = '/actualizaPassword.php';
							//***************************************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "SUCCESS_UPDATE_CREDENTIALS";
						}
						//*******************************************************************************************************
						UsuarioNotificacionPeer::notifyUserTask(array($user->getUsuarioId()),ModuleEnableNotify::Seguridad,null,RolComTypeNotify::TrustInicioSesionday);
						AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
						//*******************************************************************************************************
						return $this->renderText(json_encode($resp));
					}else{
						if($user->getEstadousuarioId() != 2 ){
							if($user->getIntentos() > $num_intentos){
								$resp = array();
								$resp['login_status'] = "invalid";
								//$resp['error_mensaje'] = "Acceso denegado. Usuario bloqueado por numero de intentos.";
								$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
								//**************************************************************************
								securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LOCKED";
							}else{
								$user->setIntentos($user->getIntentos()+1);
								$user->save();
								$resp = array();
								$resp['login_status'] = "invalid";
								//$resp['error_mensaje'] = "Acceso denegado. Password incorrecto.";
								$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
								//***************************************************************************
								securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
							}
						}elseif($user->getEstadousuarioId() == 4){
							$resp['login_status'] = "invalid";               
							//$resp['error_mensaje'] = "Acceso denegado. Usuario deshabilitado";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
							//******************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISABLED";
						}elseif($user->getEstadousuarioId() == 5){
							$resp['login_status'] = "invalid";               
							//$resp['error_mensaje'] = "Acceso denegado. Usuario suspendido";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
							//******************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISCONTINUED";
						}else{
							$resp = array();
							$resp['login_status'] = "invalid";
							//$resp['error_mensaje'] = "Acceso denegado. Usuario Inactivo";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
						}
						//*****************************************************************************
						AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
						return $this->renderText(json_encode($resp));	
					}
				}else{
					$resp = array();
					$resp['login_status'] = "invalid";
					//$resp['error_mensaje'] = "Acceso denegado. El usuario no esta registrado";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//*****************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);              
					return $this->renderText(json_encode($resp));
				}
			}
		}
	}
	
	public function executeLoginGov()
	{
		$this->setLayout(false);
		$this->postbackurl = $this->getRequestParameter('postbackurl');
		if ($this->getRequest()->getMethod() != sfRequest::POST){
			// display the form
			$this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
			$this->msgError = $this->getRequestParameter('msgError');
			//***************************************************************************************
			$this->getUser()->setAuthenticated(false);        
			$this->getUser()->setAttribute('usuario_id', '', 'subscriber');
			$this->getUser()->setAttribute('username', '', 'subscriber');
			$this->getUser()->setAttribute('entidad_id', '', 'subscriber');
			$this->getUser()->setAttribute('regional_id', '', 'subscriber');
			$this->getUser()->setAttribute('nombre_apellido', '', 'subscriber');
			$this->getUser()->setAttribute('ruta_foto', '', 'subscriber');
			//***************************************************************************************
			$this->getUser()->setAttribute('com_interna_advance_create','',  'subscriber');
			$this->getUser()->setAttribute('com_enviada_advance_create','',  'subscriber');
			$this->getUser()->setAttribute('badge_init','',  'subscriber');
			//***************************************************************************************
			$this->getUser()->setAttribute('firma_mecanica','',  'subscriber');
			$this->getUser()->setAttribute('firma_digital','',  'subscriber');
			$this->getUser()->setAttribute('firma_desatendida','',  'subscriber');
			$this->getUser()->setAttribute('config_advanced_firma','', 'subscriber');
		}else{
			$nickname = trim($this->getRequestParameter('username'));
			securityActions::$aFuncVars['USER_NAME'] = $nickname;
			//***************************************************************************************
			// handle the form submission
			$c = new Criteria();
			$c->add(UsuarioPeer::USER_NAME, $nickname);
			$user = UsuarioPeer::doSelectOne($c);
			//***************************************************************************************
			if(empty($user) || empty($user->getPrimaryKey())){
				$resp = array();
				$resp['login_status'] = "invalid";
				//$resp['error_mensaje'] = "Acceso denegado. El usuario no esta registrado";
				$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
				//***********************************************************************************
				securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_NOT_EXISTS";
				AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);              
				return $this->renderText(json_encode($resp));
			}
			//****************************************************************************************
			// Verificacion con active directory por tabla parametros
			//$activeDirectory = ParametroPeer::retrieveByPK(47);
			//****************************************************************************************
			if($user->getTipoautenticacionId() === UserAuthType::LdapNativo){
				require_once(sfConfig::get('sf_lib_dir')."\adLDAP\src\adLDAP.php");
				//************************************************************************************
				$adldap = new adLDAP();
				$adldap->setUseSSL(true);
				// 2) Recuperar userAccountControl del usuario (verifica existencia y estado)
				$userInfo = $adldap->user()->info($nickname, ['useraccountcontrol']);
				if ($userInfo === false) {
					$resp['login_status'] = "invalid";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales7";
					//********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LDAP_ERROR_NOTFOUND";
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
					//********************************************************************************
					return $this->renderText(json_encode($resp));
				}
				//************************************************************************************
				// Extraemos el valor decimal de userAccountControl
				$uac = (int) $userInfo[0]['useraccountcontrol'][0];
				//************************************************************************************
				// Bit 0x2 = ACCOUNTDISABLE → si está a 1, la cuenta está deshabilitada
				if ($uac & 0x2) {
					$user->setEstadousuarioId(4);
					$user->save();
					//*********************************************************************************
					$resp['login_status'] = "invalid";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales8";
					//*********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LDAP_ERROR_DISABLED";
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
					//*********************************************************************************
					return $this->renderText(json_encode($resp));
				}
				//*************************************************************************************
				$result = $adldap->authenticate($nickname, $post_pass);
				//*************************************************************************************
				if ($result){
					// Consultar el Usuario con Active Directory
					$c = new Criteria();
					$c->add(UsuarioPeer::USUARIO_AD, $nickname);
					$user = UsuarioPeer::doSelectOne($c);
					//*********************************************************************************
					$this->getUser()->setAuthenticated(true);
					$this->getUser()->addCredential('subscriber');
					$this->getUser()->setAttribute('usuario_id', trim($user->getUsuarioId()), 'subscriber');
					$this->getUser()->setAttribute('username',   trim($user->getUserName()),  'subscriber');
					$this->getUser()->setAttribute('entidad_id',trim($user->getRegional()->getEntidadId()),  'subscriber');
					$this->getUser()->setAttribute('regional_id',trim($user->getRegionalId()),  'subscriber');
					$this->getUser()->setAttribute('nombre_apellido',trim($user->getNombre().' '.$user->getApellido()),  'subscriber');
					$this->getUser()->setAttribute('ruta_foto',trim($user->getRutaFoto()),  'subscriber');
					//**********************************************************************************
					$com_interna_advance_create = $this->getUser()->checkPerm("COM_INTERNA_ADVANCE_CREATE", $user->getUsuarioId());
					$com_enviada_advance_create = $this->getUser()->checkPerm("COM_ENVIADA_ADVANCE_CREATE", $user->getUsuarioId());
					$com_recibida_view_notify = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA", $user->getUsuarioId());
					$com_recibida_view_notify_all = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA_ALL", $user->getUsuarioId());
					$config_advanced_firma = $this->getUser()->checkPerm("CONFIGURACION_ADVANCED_FIRMA", $user->getUsuarioId());
					//**********************************************************************************
					sfConfig::set('com_interna_advance_create', $com_interna_advance_create);
					sfConfig::set('com_enviada_advance_create', $com_enviada_advance_create);
					$this->getUser()->setAttribute('badge_view_notify',($com_recibida_view_notify_all ? 1 : ($com_recibida_view_notify ? 2 : "")),  'subscriber');
					//**********************************************************************************
					$this->getUser()->setAttribute('firma_mecanica',$user->getUseFirmaElectronica(), 'subscriber');
					$this->getUser()->setAttribute('firma_digital',$user->getFirmaDigital(),  'subscriber');
					$this->getUser()->setAttribute('firma_desatendida',$user->getFirmaDesatendida(), 'subscriber');
					$this->getUser()->setAttribute('config_advanced_firma', $config_advanced_firma, 'subscriber');
					//**********************************************************************************
					$resp = array();
					//**********************************************************************************
					if ( $user->getEstadousuarioId() == 2){
					$resp['login_status'] = "invalid";               
					//$resp['error_mensaje'] = "Acceso denegado. Usuario bloqueado";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LOCKED";
					}elseif($user->getEstadousuarioId() == 4){
					$resp['login_status'] = "invalid";               
					//$resp['error_mensaje'] = "Acceso denegado. Usuario deshabilitado";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISABLED";
					}elseif($user->getEstadousuarioId() == 5){
					$resp['login_status'] = "invalid";               
					//$resp['error_mensaje'] = "Acceso denegado. Usuario suspendido";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISCONTINUED";
				}else{
					$url_redirect = '/backend.php/resumen';
					//********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "SUCCESS";
					//********************************************************************************
					$resp = array();
					$resp['login_status'] = "success";
					$resp['redirect_url'] = $url_redirect;                
				}
				//**********************************************************************************
				AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
				//**********************************************************************************
				return $this->renderText(json_encode($resp));
				}else{
					$resp = array();
					$resp['login_status'] = "invalid";
					//$resp['error_mensaje'] = "Acceso denegado. Usuario no existe";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//**********************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "INVALID_CREDENTIALS";
					//**********************************************************************************
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
					return $this->renderText(json_encode($resp));	
				}
      		}else{
				// handle the form submission
				$c = new Criteria();
				$c->add(UsuarioPeer::USER_NAME, $nickname);
				$user = UsuarioPeer::doSelectOne($c);
				// user exists?
				if ($user){
					$sha1pass = sha1($user->getSalt(). $this->getRequestParameter('password'));
					$num_intentos = ParametroPeer::retrieveByPK(72)->getValorNumerico();
					//password is OK?
					if ($sha1pass  == $user->getPassword()  && $user->getPassword()!="" && $user->getEstadousuarioId() != 2 && $user->getIntentos() <= $num_intentos ){
						$this->getUser()->setAuthenticated(true);
						$this->getUser()->addCredential('subscriber');
						$this->getUser()->setAttribute('usuario_id', $user->getUsuarioId(), 'subscriber');
						$this->getUser()->setAttribute('username',   $user->getUserName(),  'subscriber');
						$this->getUser()->setAttribute('entidad_id',$user->getRegional()->getEntidadId(),  'subscriber');
						$this->getUser()->setAttribute('regional_id',$user->getRegionalId(),  'subscriber');
						$this->getUser()->setAttribute('nombre_apellido',$user->getNombre().' '.$user->getApellido(),  'subscriber');
						$this->getUser()->setAttribute('ruta_foto',$user->getRutaFoto(),  'subscriber');
						//*******************************************************************************************************
						$com_interna_advance_create = $this->getUser()->checkPerm("COM_INTERNA_ADVANCE_CREATE", $user->getUsuarioId());
						$com_enviada_advance_create = $this->getUser()->checkPerm("COM_ENVIADA_ADVANCE_CREATE", $user->getUsuarioId());
						$com_recibida_view_notify = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA", $user->getUsuarioId());
						$com_recibida_view_notify_all = $this->getUser()->checkPerm("RECIBIDA_ADJUNTAR_DIGIT_NOTIFICA_ALL", $user->getUsuarioId());
						$config_advanced_firma = $this->getUser()->checkPerm("CONFIGURACION_ADVANCED_FIRMA", $user->getUsuarioId());
						//*******************************************************************************************************
						$this->getUser()->setAttribute('com_interna_advance_create',$com_interna_advance_create,  'subscriber');
						$this->getUser()->setAttribute('com_enviada_advance_create',$com_enviada_advance_create,  'subscriber');
						$this->getUser()->setAttribute('badge_view_notify',($com_recibida_view_notify_all ? 1 : ($com_recibida_view_notify ? 2 : "")),  'subscriber');
						//*******************************************************************************************************
						$this->getUser()->setAttribute('firma_mecanica',$user->getUseFirmaElectronica(), 'subscriber');
						$this->getUser()->setAttribute('firma_digital',$user->getFirmaDigital(),  'subscriber');
						$this->getUser()->setAttribute('firma_desatendida',$user->getFirmaDesatendida(), 'subscriber');
						$this->getUser()->setAttribute('config_advanced_firma', $config_advanced_firma, 'subscriber');
						//*******************************************************************************************************
						$dias = ParametroPeer::retrieveByPK(1)->getValorNumerico();
						$fecha = explode(" ", $user->getFechaActualizacion());
						$fecha = AddDays($fecha[0],$dias);
						$resp = array();
						//*******************************************************************************************************	
						if($fecha > date("Y-m-d")){
							$user->setIntentos(0);
							$user->save();
							//***************************************************************************************************
							$url_redirect = '/backend.php/resumen';//$url_redirect = '/inicio.php';
							//***************************************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "SUCCESS";
							//***************************************************************************************************
							$resp['login_status'] = "success";
							$resp['redirect_url'] = $url_redirect;
						}else{
							$resp['login_status'] = "success";
							$resp['redirect_url'] = '/actualizaPassword.php';
							//***************************************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "SUCCESS_UPDATE_CREDENTIALS";
						}
						//****************************************************************************************************
						AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
						return $this->renderText(json_encode($resp));
					}else{
						if($user->getEstadousuarioId() != 2 ){
							if($user->getIntentos() > $num_intentos){
								$resp = array();
								$resp['login_status'] = "invalid";
								//$resp['error_mensaje'] = "Acceso denegado. Usuario bloqueado por numero de intentos.";
								$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
								//**************************************************************************
								securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LOCKED";
							}else{
								$user->setIntentos($user->getIntentos()+1);
								$user->save();
								$resp = array();
								$resp['login_status'] = "invalid";
								//$resp['error_mensaje'] = "Acceso denegado. Password incorrecto.";
								$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
								//***************************************************************************
								securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
							}
						}elseif($user->getEstadousuarioId() == 4){
							$resp['login_status'] = "invalid";               
							//$resp['error_mensaje'] = "Acceso denegado. Usuario deshabilitado";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
							//******************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISABLED";
						}elseif($user->getEstadousuarioId() == 5){
							$resp['login_status'] = "invalid";               
							//$resp['error_mensaje'] = "Acceso denegado. Usuario suspendido";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
							//******************************************************************************
							securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_DISCONTINUED";
						}else{
							$resp = array();
							$resp['login_status'] = "invalid";
							//$resp['error_mensaje'] = "Acceso denegado. Usuario Inactivo";
							$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
						}
						//*****************************************************************************
						AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
						return $this->renderText(json_encode($resp));	
					}
				}else{
					$resp = array();
					$resp['login_status'] = "invalid";
					//$resp['error_mensaje'] = "Acceso denegado. El usuario no esta registrado";
					$resp['error_mensaje'] = "Acceso denegado, error de credenciales";
					//*****************************************************************************
					securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_ERROR_CREDENTIALS";
					AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);              
					return $this->renderText(json_encode($resp));
				}
			}
		}
	}

	public function executeLogout()
	{
		$nickname = $this->getUser()->getAttribute('username', '', 'subscriber');
		securityActions::$aFuncVars['USER_NAME'] = $nickname;
		securityActions::$aFuncVars['LOGIN_STATUS'] = "USER_LOGOUT";
		//*****************************************************************************
		SessionManager::terminateCurrentSession();
		//*****************************************************************************
		$this->getUser()->setAuthenticated(false);
		$this->getUser()->clearCredentials();
		$this->getUser()->getAttributeHolder()->removeNamespace('subscriber');
		//*****************************************************************************
		AccesoLogPeer::addAuditAccess( securityActions::$aFuncVars);
		//*****************************************************************************
		$this->redirect('security/login');
	}

	public function handleErrorLogin()
	{
		return sfView::SUCCESS;
	}

	public function setSha1Password($usuario_id, $password)
	{   
		$user = UsuarioPeer::retrieveByPk($usuario_id);
		if($user){
			$salt= md5(rand(100000,999999).$user->getEmail().$user->getNombre());   	
			$user->setSalt($salt);
			$newpass=sha1($salt.$password);
			$user->setPassword($newpass);
			$user->save();
			return $newpass;
		}
		return "";
	}

	public function getSha1Password($usuario_id){
	   $user = UsuarioPeer::retrieveByPk($usuario_id);
	   if($user){
		   return $user->getPassword();
		}else{
			return "";
		}   	
	}
}
