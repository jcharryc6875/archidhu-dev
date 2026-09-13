<?php

/**
 * Subclass for representing a row from the 'SERVICIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Servicio extends BaseServicio
{
	public function getBasicUrlAttach()
	{
		$array_url = array();
		//*****************************************************************************************
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		$periodo = date("Y");
		//*****************************CREANDO ESTRUCTURA DE DIRECTORIOS***************************
		$entidad = EntidadPeer::retrieveByPk($this->getRegional()->getEntidadId());
		$regional = RegionalPeer::retrieveByPk($this->getRegionalId());
		$dirRaiz = ParametroPeer::retrieveByPk(73)->getValortexto();
		$dir_tmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
		$alias_web = ParametroPeer::retrieveByPk(74)->getValortexto();
		//*****************************************************************************************
		$entidad_folder = trim($entidad->getDirectorioName()) ? trim($entidad->getDirectorioName()) : "";
		$regional_folder = trim($regional->getDirectorioName()) ? trim($regional->getDirectorioName()) : "";
		$entidad_text = empty($entidad_folder) ? (empty($regional_folder) ? "" : $regional_folder) : (empty($regional_folder) ? $entidad_folder : $entidad_folder. DIRECTORY_SEPARATOR .$regional_folder);
		//*****************************************************************************************
		$basic_path = $entidad_text .DIRECTORY_SEPARATOR . 'servicios' . DIRECTORY_SEPARATOR . $periodo;    	
		$directorio_entidad = $dirRaiz . $basic_path;
		$directorio_temp = $dirRaiz.$entidad_text. DIRECTORY_SEPARATOR . $dir_tmp;
		//*****************************************************************************************
		$array_url['basic_path'] = $basic_path;
		$array_url['full_path'] = $directorio_entidad;
		$array_url['temp_path'] = $directorio_temp;
		$array_url['alias_web'] = $alias_web;
		//*****************************************************************************************
		return $array_url;
	}

	public function envioEmail($useremail_id, $notas, $encabezado_cuerpo = "")
	{
		try {
			$usuario = UsuarioPeer::retrieveByPK($useremail_id);
			$radicado = $this->getRadicado();
			//***********************************************************************************************
			if(!trim($encabezado_cuerpo)){
				$encabezado_cuerpo = 'Este es un mensaje para informarle que registro una actividad en la siguiente solicitud solicitud de servicio:';
			}
			//***********************************************************************************************
			$cuerpo = '<html>
				<head>
					<title></title>
				</head>
				<body>
				<br>'.$encabezado_cuerpo.'     
				<br>
				<br>        
				<b>Numero Radicado:</b> '.$radicado.'<br>
				<b>Tipo Solicitud:</b> '.utf8_encode($this->getTipoServicio()).'<br>
				<b>Fecha Radicaci&oacute;n:</b> '.$this->getFechaCreacion().'<br>
				<b>Detalle Servicio:</b> '.utf8_encode($this->getDetalle()).'<br>';
			//***********************************************************************************************
			if(trim($notas)){
				$cuerpo .=  '<b>Observaciones:</b> '.utf8_encode($notas).'<br>';
			}
			//***********************************************************************************************
			$baseMail = new BaseMailSimad();
			$baseMail->SetSubject('CAD : Solicitud Servicio # '.$this->getRadicado());
			$baseMail->SetMsgHTML($cuerpo);
			$baseMail->SetAddAddress($usuario->getEmail(), $usuario->getEmail());
			//***********************************************************************************************
			$sendEmail = $baseMail->InitSend();
			if($sendEmail === true){
				$baseMail->writetolog("Alerta enviada: " . $radicado . " Enviado a: " . $usuario->getEmail());
			}else{
				$baseMail->writetolog("Error al enviar alerta: " . $radicado . " Cuenta correo: " . $usuario->getEmail());
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}
	
	/**
     * getListAttachmentCom()
     * Crea una lista de archivos adjuntos a una comunicacion, que esta asociada a un servicio
     * @return mixed array('size'=> filesize en bytes,'fullpath' => ruta temporal al archivo)
     */
	public function getListAttachmentCom()
	{
		try {
			$attach_url = array();
            $pathtmp = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR .md5(time().$this->getPrimaryKey()). DIRECTORY_SEPARATOR;
			simad_util::createPath($pathtmp);
            $rootUrl = sfConfig::get('publicUrl');
			//****************************************************************************************
			switch($this->getModuloId()){
				case 2: //interna
					$com_interna = ComInternaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					if(!in_array($com_interna->getEstadocominternaId(),array(1,4,6)) || ($com_interna->getNumeroRadicacion() <= 0)
						|| (empty($com_interna->getRadicado()) || ($com_interna->getRadicado() == "Sin Radicar"))){
						$files_adjuntos = preg_split("/[,]+/",trim($com_interna->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
					}
					break;
				case 3: //recibida
					$com_recibida = ComRecibidaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					$files_adjuntos = preg_split("/[,]+/",trim($com_recibida->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
					break;
				case 4: //enviada
					$com_enviada = ComEnviadaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					if(!in_array($com_enviada->getEstadocomenviadaId(),array(1,4,5)) || ($com_enviada->getNumeroRadicacion() <= 0)
						|| (empty($com_enviada->getRadicado()) || ($com_enviada->getRadicado() == "Sin Radicar"))){
						$files_adjuntos = preg_split("/[,]+/",trim($com_enviada->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
					}
					break;
				case 17: //acto_administrativo
					$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					if(!in_array($acto_administrativo->getEstadoactoadministrativoId(),array(1,2,3,4,5,8)) || (!empty($acto_administrativo->getNumeroResolucion())
						&& !empty($acto_administrativo->getFechaResolucion()))){
						$files_adjuntos = preg_split("/[,]+/",trim($acto_administrativo->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
					}
					break;
				default:
					$files_adjuntos = array();
			}
			//****************************************************************************************
			for($j = 0; $j < count($files_adjuntos); $j++){
				if(trim($files_adjuntos[$j])){
					try {
						$filename = basename($files_adjuntos[$j]);
						$fileurl = $rootUrl.$files_adjuntos[$j];
						//****************************************************************************
						if(file_exists($pathtmp.$filename)){
							unlink($pathtmp.$filename);
						}
						//****************************************************************************
						file_put_contents($pathtmp.$filename, file_get_contents($fileurl));
						//****************************************************************************
						if(file_exists($pathtmp.$filename)){
							$attach_url[] = array('size' => filesize($pathtmp.$filename), 'fullpath' => $pathtmp.$filename);
						}
					} catch (\IOException $th) {
						//throw $th;
					} catch (\Exception $th) {
						//throw $th;
					}
				}
			}
        } catch (PropelException $th) {
			$attach_url = array();
		} catch (\Exception $th) {
			$attach_url = array();
		} catch (\Throwable $th) {
			$attach_url = array();
		}
		//*********************************************************************************************
		return $attach_url;
	}

	public function getAttachComDocDigit()
	{
		try {
			$attach_url = null;
			if(!empty($this->getConsecutivocomId()) && !empty($this->getModuloId())){
				$mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
				switch($this->getModuloId()){
					case 2: //interna
						$com_interna = ComInternaPeer::retrieveByPK($this->getConsecutivocomId());
						$com_radicado = trim($com_interna->getRadicado());
						//************************************************************************************
						$dir_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
						$digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
						$storage_com = $com_interna->getBasicUrlDigitCom($dir_raiz,$digit_dir);
						break;
					case 3: //recibida
						$com_recibida = ComRecibidaPeer::retrieveByPK($this->getConsecutivocomId());
						$com_radicado = trim($com_recibida->getRadicado());
						//************************************************************************************
						$dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
						$digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
						$storage_com = $com_recibida->getBasicUrlDigitCom($dir_raiz,$digit_dir);
						break;
					case 4: //enviada
						$com_enviada = ComEnviadaPeer::retrieveByPK($this->getConsecutivocomId());
						$com_radicado = trim($com_enviada->getRadicado());
						//************************************************************************************
						$dir_raiz = ParametroPeer::retrieveByPk(29)->getValortexto();
						$digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
						$storage_com = $com_enviada->getBasicUrlDigitCom($dir_raiz,$digit_dir);
						break;
					case 17: //actos administrativos
						$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getConsecutivocomId());
						$com_radicado = trim($acto_administrativo->getRadicadoCustom());
						//************************************************************************************
						$dir_raiz = ParametroPeer::retrieveByPk(75)->getValortexto();
        				$digit_dir  = ParametroPeer::retrieveByPk(76)->getValortexto();
						$storage_com = $acto_administrativo->getBasicUrlDigitCom($dir_raiz,$digit_dir);
						break;
				}
				//********************************************************************************************
				foreach ($mimetypes as $format) {
					$filename = sprintf("%s.%s",$com_radicado,$format);
					$targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
					if(file_exists($targetpath)){
						$attach_url = $targetpath;
						break;
					}
				}
				//********************************************************************************************
				if(empty($attach_url)){
					switch($this->getModuloId()){
						case 2: //interna
							if($com_interna != null){
								if(!empty($com_interna->getUrlFileWord())){
									$attach_url = $com_interna->generatePdfByFile(trim($com_interna->getUrlFileWord()),true);
								}else{
									$attach_url = $com_interna->generateFileInDisk();
								}
							}
						break;
						case 4: //enviada
							if($com_enviada != null){
								if(!empty($com_enviada->getUrlFileWord())){
									$attach_url = $com_enviada->SimadGeneratePdf(trim($com_enviada->getUrlFileWord()),true);
								}else{
									$attach_url = $com_enviada->generateFileInDisk();
								}
							}
						break;
						case 17: //acto_administrativo
							if($acto_administrativo != null){
								$ulist_firma = ActoadministrativoUsuarioPeer::getAllUserFirmaDigitalObj($acto_administrativo->getPrimaryKey());
								$isFirmaDigital = count($ulist_firma) ? true : false;
								//*****************************************************************************
								$margins = array('top' => 30,'left' => 20,'buttom' => 25,'rigth' => 18);
								$file_attach = $acto_administrativo->generateFileInDisk($margins,$isFirmaDigital);
							}
						break;
					}
					//*****************************************************************************************
					if(file_exists($attach_url)){
						$path_parts = pathinfo($attach_url);
						$format = $path_parts['extension'];
						$filename = sprintf("%s.%s",trim($com_radicado),$format);
						$targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
						if(!file_exists($targetpath)){
							if(rename($attach_url,$targetpath)){
								$attach_url = $targetpath;
							}
						}else{
							$attach_url = $targetpath;
						}
					}
				}
			}
		} catch (Exception $th) {
			$attach_url = null;
		}
		//***************************************************************************************************
		return $attach_url;
	}
	
	public function envioEmailNotificacion($dependencia_id = 0)
	{
		try {
			$mail_notificacion = NotificacionEmailPeer::getInfoEmailNotificacion($dependencia_id);
			if(empty($mail_notificacion)){
				$estadoservicio_current = 3;
				$this->setServicioestadoId($estadoservicio_current);
				$this->setObsDevolucion('No esta parametrizado el email para la dependencia del servicio');
				return array('error' => true, 'message' => 'No esta parametrizado el email para la dependencia del servicio');
			}
			//***********************************************************************************************
			$baseMail = new BaseMailSimad();
			$baseMail->SetHost($mail_notificacion->getHostSmtp());
			$baseMail->SetEmailUser($mail_notificacion->getEmailLogin());
			$baseMail->SetEmailPass(SED::decryption($mail_notificacion->getEmailPass()));
			$baseMail->SetPuerto($mail_notificacion->getPuerto());
			$baseMail->SetSMTPAuth(($mail_notificacion->getAuthSmtp() ? true : false));
			$baseMail->SetFrom($mail_notificacion->getEmailLogin());
			$baseMail->SetSMTPSecure($mail_notificacion->getSecureSmtp());
			$baseMail->SetEnableService(true);
			$baseMail->setVerifySsl(true);
			$baseMail->SetLogDir(sfConfig::get("sf_log_dir"));
			//***********************************************************************************************
			$asunto_email = trim($this->getDetalle());
			$body_email = $mail_notificacion->getEmailBody();
			//***********************************************************************************************
			$baseMail->SetSubject($asunto_email);
			$baseMail->SetMsgHTML($body_email);
			$baseMail->SetFromText("Notificaciones Electronicas");
			//***********************************************************************************************
			$address_list = trim($this->getEmailDestino()) ? preg_split("/[;]+/",trim($this->getEmailDestino()), -1, PREG_SPLIT_NO_EMPTY) : array();
			//***********************************************************************************************
			$interesados_list = ServicioPeer::getListIntersadosByComId($this->getPrimaryKey());
			foreach ($interesados_list as $row) {
				$interesado_com = $row->getInteresados();
				$email_interesado = !empty(trim($interesado_com->getEmail())) ? trim($interesado_com->getEmail()) : null;
				if(!empty($email_interesado) && !in_array($email_interesado,$address_list)){
					$address_list[] = $email_interesado;
				}
			}
			//***********************************************************************************************
			if($this->getDirectorioexternoId() != null){
				$directorio_externo = DirectorioExternoPeer::retrieveByPK($this->getDirectorioexternoId());
				$email_directorio = !empty(trim($directorio_externo->getEmail())) ? trim($directorio_externo->getEmail()) : null;
				if(!empty($email_directorio) && !in_array($email_directorio,$address_list)){
					$address_list[] = $email_directorio;
				}
			}
			//***********************************************************************************************
			$address_copy = explode(";",trim($mail_notificacion->getCertimail()));
			$baseMail->SetAddCopyEmail($address_copy);
			//***********************************************************************************************
			$digit_com = $this->getAttachComDocDigit();
			$size_attach = 0;
			if(!empty($digit_com)){
				$size_attach += filesize($digit_com);
				$baseMail->SetAddAttach($digit_com); 
			}
			//***********************************************************************************************
			$list_attach = $this->getAttachmentCom($size_attach);			
			if($size_attach <= 26214400){
				foreach ($list_attach as $newattach) {
					$baseMail->SetAddAttach($newattach);
				}
			}else{
				$estadoservicio_current = 9;
				$msg_error = "Los anexos del email superan el maximo permitido por el proveedor de correos size actual {$size_attach} en bytes";
				$this->setServicioestadoId($estadoservicio_current);
				$this->setObsDevolucion($msg_error);
				return array('error' => true, 'message' => $msg_error);
			}
			//***********************************************************************************************
			$message_list = array();
			$isAnyError = false;
			foreach ($address_list as $email_destino) {
				try {
					if(!empty($email_destino)){
						$baseMail->SetAddAddress($email_destino, $email_destino); 
					}else{
						continue;
					}
					//***************************************************************************************
					$fecha_envio = date("Y-m-d G:i:s");
					$isSendMail = $baseMail->InitSend();
					if($isSendMail === true){
						$bitacora_text = "Correo no certificado enviado exitosamente al email de destino => {$email_destino}";
						$baseMail->writetolog("Servicios::envioEmailNotificacion Alerta enviada: " . $this->getRadicado() . " Enviado a: " . $email_destino);
						$message_list[] = array('error' => false, 'message' => 'Alerta enviada');
					}else{
						$bitacora_text = "Error enviando correo no certificado email destino => {$email_destino} Error => {$isSendMail}";
						$baseMail->writetolog($bitacora_text . $this->getRadicado() . " Cuenta correo: " . $email_destino. ", Error => ". $isSendMail);
						$message_list[] =  array('error' => true, 'message' => trim($bitacora_text));
						$isAnyError = true;
						//***********************************************************************************
						if(!empty($this->getObsDevolucion())){
							$this->setObsDevolucion(trim($this->getObsDevolucion()).'|'.trim($bitacora_text));
						}else{
							$this->setObsDevolucion(trim($bitacora_text));
						}
					}
					//***************************************************************************************
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
					//***************************************************************************************
					$baseMail->mail_object->clearAddresses();
				} catch (PropelException $th) {
					$bitacora_text = "Error enviando correo no certificado email destino => {$email_destino}, message {$th->getMessage()}";
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
				} catch (\Exception $th) {
					$bitacora_text = "Error enviando correo no certificado email destino => {$email_destino}, message {$th->getMessage()}";
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
				} catch (\Throwable $th) {
					$bitacora_text = "Error enviando correo no certificado email destino => {$email_destino}, message {$th->getMessage()}";
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
				}
				//********************************************************************************************
				$baseMail->mail_object->clearAddresses();
			}
			//************************************************************************************************
			$estadoservicio_current = $isAnyError ? 9 : 3;
			$this->setServicioestadoId($estadoservicio_current);
			//************************************************************************************************
			if(!empty($this->getObsDevolucion())){
				$this->setObsDevolucion(trim($this->getObsDevolucion()).'|'.trim($bitacora_text));
			}else{
				$this->setObsDevolucion(trim($bitacora_text));
			}
			//************************************************************************************************
			return array('error' => $isAnyError, 'message' => implode("|",$message_list));
		} catch (PropelException $th) {
			$baseMail->writetolog("Error al enviar el correo electronico => ".$th->getMessage());
			return array('error' => true, 'message' => "Error al enviar el correo electronico => {$th->getMessage()}");
		} catch (\Exception $th) {
			$baseMail->writetolog("Error al enviar el correo electronico => ".$th->getMessage());
			return array('error' => true, 'message' => "Error al enviar el correo electronico => {$th->getMessage()}");
		} catch (\Throwable $th) {
			$baseMail->writetolog("Error al enviar el correo electronico => ".$th->getMessage());
			return array('error' => true, 'message' => "Error al enviar el correo electronico => {$th->getMessage()}");
		}
	}

	/**
    * servicioActions::getAttachmentCom()
    * funcion que obtiene un array de anexos de las comunicaciones, validando el tipo de comunicacion
    * @return
    */
	public function getAttachmentCom(&$size_attach)
	{
		try {
			$list_attach = array();
			switch($this->getModuloId()){
				case ModulesEnable::ComInterna: //interna
					$com_interna = ComInternaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					$response = $com_interna->getAttachmentCom();
					break;
				case ModulesEnable::ComRecibida: //recibida
					$com_recibida = ComRecibidaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					$response = $com_recibida->getAttachmentCom();
					break;
				case ModulesEnable::ComEnviada: //enviada
					$com_enviada = ComEnviadaPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					$response = $com_enviada->getAttachmentCom();
					if(!empty($response)){
						foreach ($response as $ifile) {
							if(!empty($ifile)){
								$size_attach += filesize($ifile);
								$list_attach[] = $ifile;
							}
						}
					}
					break;
				case ModulesEnable::ActosAdministrativos: //actos administrativos
					$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getConsecutivocomId());
					//************************************************************************************
					$response = $acto_administrativo->getAttachmentCom();
					if(!empty($response)){
						foreach ($response as $ifile) {
							if(!empty($ifile)){
								$size_attach += filesize($ifile);
								$list_attach[] = $ifile;
							}
						}
					}
					break;
			}
		} catch (PropelException $th) {
			return array();
		} catch (\Exception $th) {
			return array();
		} catch (\Throwable $th) {
			return array();
		}
	}
	
	/**
	 * servicioActions::sendCertiMailApi()
	 * funcion que realiza integracion con proveedor de correo certificado
	 * @return
	 */
	public function sendCertiMailApi()
	{
		$provider = simad_util::readConfigFileApp(['provider_certi_email_enable']);
		$svc_certimail = isset($provider['provider_certi_email_enable']) ? $provider['provider_certi_email_enable'] : null;
		//***************************************************************************************************
		if (empty($svc_certimail)) {
			return array('status' => 400, 'message' => 'El proveedor de certimail no esta configurado', 'email_notified' => null);
		}
		//***************************************************************************************************
		switch ($svc_certimail) {
			case CertiEmailProviders::RMail:
				return $this->sendCertiMailApiRMail();
			case CertiEmailProviders::Andes:
				return $this->sendCertiMailApiAndes();
			default:
				return array('status' => 400, 'message' => 'El proveedor de certimail no esta configurado', 'email_notified' => null);
		}
	}
	
	/**
	 * servicioActions::sendCertiMailApiRMail()
	 * funcion que realiza integracion con proveedor de correo certificado RMail
	 * @return void
	 */
	public function sendCertiMailApiRMail()
	{
		$ws_api = new WsApiRMail();
		$max_size = 20000000;
		$error_maxsize = "";
		//***************************************************************************************************
		try {
			$digit_com = $this->getAttachComDocDigit();
			if (empty($digit_com) || !file_exists($digit_com)) {
				return array('status' => 400, 'message' => 'No se pudo obtener el documento principal de la comunicación', 'email_notified' => null);
			}
			//***********************************************************************************************
			$attach_list = array();
			$fsize_all = filesize($digit_com);
			//***********************************************************************************************
			$files_attach = $this->getListAttachmentCom();
			if (count($files_attach) > 0) {
				foreach ($files_attach as $fattach) {
					$fsize_all += $fattach['size'];
					$attach_list[] = $fattach['fullpath'];
				}
				//*******************************************************************************************
				if ($fsize_all >= $max_size) {
					$attach_list = array($digit_com);
					$error_maxsize = "Error, Los archivos adjuntos sobrepasan el tamaño maximo permitido por el proveedor de correo certificado, solo se enviara la comunicacion sin anexos";
				} else {
					$attach_list[] = $digit_com;
				}
			} else {
				$attach_list[] = $digit_com;
			}
			//***********************************************************************************************
			$params_service = array();
			$params_service['subject']     = mb_convert_encoding($this->getDetalle(), 'UTF-8', 'auto');
			$params_service['attachments'] = $attach_list;
			$params_service['IsLargeMail'] = $fsize_all >= $max_size;
			//***********************************************************************************************
			$certimail_destino = array();
			$email_destino = trim($this->getEmailDestino()) ? preg_split("/[;]+/", trim($this->getEmailDestino()), -1, PREG_SPLIT_NO_EMPTY) : array();
			$email_all = !empty($email_destino) ? $email_destino : array();
			//***********************************************************************************************
			$interesados_list = ServicioPeer::getListIntersadosByComId($this->getPrimaryKey());
			if (count($interesados_list) > 0) {
				foreach ($interesados_list as $row) {
					$interesado_com   = $row->getInteresados();
					$email_interesado = trim($interesado_com->getEmail());
					if (!in_array($email_interesado, $email_all) && filter_var($email_interesado, FILTER_VALIDATE_EMAIL)) {
						$certimail_destino[] = array(
							'ENTIDAD_DESTINO'     => mb_convert_encoding($interesado_com->getNombreCompuesto(), 'UTF-8', 'auto'),
							'FUNCIONARIO_DESTINO' => '',
							'EMAIL_DESTINO'       => $email_interesado,
						);
						$email_all[] = $email_interesado;
					}
				}
			}
			//***********************************************************************************************
			if ($this->getDirectorioexternoId() != null) {
				$directorio_externo = DirectorioExternoPeer::retrieveByPK($this->getDirectorioexternoId());
				$email_externo = trim($directorio_externo->getEmail());
				if (!in_array($email_externo, $email_all) && filter_var($email_externo, FILTER_VALIDATE_EMAIL)) {
					$certimail_destino[] = array(
						'ENTIDAD_DESTINO'     => mb_convert_encoding($directorio_externo->getNombre(), 'UTF-8', 'auto'),
						'FUNCIONARIO_DESTINO' => mb_convert_encoding($directorio_externo->getFuncionario(), 'UTF-8', 'auto'),
						'EMAIL_DESTINO'       => $email_externo,
					);
					$email_all[] = $email_externo;
				}
			}
			//***********************************************************************************************
			foreach ($email_destino as $row) {
				$entidad_destino = trim($this->getFuncionarioDestino()) ? mb_convert_encoding($this->getFuncionarioDestino(), 'UTF-8', 'auto') : $row;
				if (!in_array($row, $email_all) && filter_var($row, FILTER_VALIDATE_EMAIL)) {
					$certimail_destino[] = array('ENTIDAD_DESTINO' => $entidad_destino, 'FUNCIONARIO_DESTINO' => null, 'EMAIL_DESTINO' => $row);
					$email_all[] = $row;
				}
			}
			//***********************************************************************************************
			if (empty($email_all)) {
				return array('status' => 400, 'message' => 'No se encontraron destinatarios válidos para el envío del correo certificado', 'email_notified' => null);
			}
			//***********************************************************************************************
			$tpl_email = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'tplBodyCertimail.txt';
			if (!file_exists($tpl_email)) {
				return array('status' => 400, 'message' => 'Error leyendo la plantilla del correo de certificación');
			}
			//***********************************************************************************************
			$bodyEmail     = file_get_contents($tpl_email);
			$patrones      = array('{$#ENTIDAD_NOMBRER$#}', '{$#ENTIDAD_FUNCIONARIO$#}');
			$sustituciones = array(
				count($certimail_destino) === 1 ? $certimail_destino[0]['ENTIDAD_DESTINO']     : '',
				count($certimail_destino) === 1 ? $certimail_destino[0]['FUNCIONARIO_DESTINO'] : '',
			);
			$params_service['body'] = str_replace($patrones, $sustituciones, $bodyEmail);
			//***********************************************************************************************
			$emails        = implode(',', $email_all);
			$response_data = $ws_api->enviarCorreoCertificado(
				$this->getRadicado(),
				$emails,
				$params_service['subject'],
				$params_service['body'],
				$params_service['attachments']
			);
			$fecha_envio = date("Y-m-d G:i:s");
			//*******************************************************************************************
			if ($response_data->exito) {
				$textresp_send = empty($error_maxsize) ? $response_data->mensaje : $response_data->mensaje . " " . $error_maxsize;
				//***************************************************************************************
				$bitacora_text = sprintf("Correo certificado enviado => %s MessageId: %s => Email Notificado => %s", $textresp_send,$response_data->trackingId, $emails);
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(), $this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
				//***************************************************************************************
				$this->setServicioestadoId(10);
				$this->save();
				//***************************************************************************************
				ServicioCertimailPeer::addServicioCertMail($this->getPrimaryKey(), $response_data->trackingId, 'EnviadoEnteCertificador', ServicioAppExterna::CertiMail);
				return array('status' => 200, 'message' => $textresp_send, 'email_notified' => $emails);
			} else {
				$textresp_send = empty($error_maxsize) ? $response_data->mensaje : $response_data->mensaje . " " . $error_maxsize;
				//***************************************************************************************
				$bitacora_text = sprintf("Error enviando correo certificado => %s => Email Notificado => %s", $textresp_send, $emails);
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(), $this->getServicioestadoId(), $this->getUsuarioId(), $this->getUsuarioId(), $bitacora_text, $fecha_envio);
				//***************************************************************************************
				$obsDevolucion = trim($this->getObsDevolucion()) ? trim($this->getObsDevolucion()) . '|' . $textresp_send : $textresp_send;
				$this->setServicioestadoId(9);
				$this->setObsDevolucion($obsDevolucion);
				$this->save();
				return array('status' => 400, 'message' => $textresp_send, 'email_notified' => $emails);
			}
		} catch (PropelException $th) {
			return array('status' => 400, 'message' => 'PropelException error, ' . $th->getMessage());
		} catch (\Exception $th) {
			return array('status' => 400, 'message' => 'Exception error, ' . $th->getMessage());
		} catch (\Throwable $th) {
			return array('status' => 400, 'message' => 'Throwable error, ' . $th->getMessage());
		}
	}

	/**
    * servicioActions::sendCertiMailApiAndes()
    * funcion que realiza integracion con porveedore de correo certificado
    * @return
    */
	public function sendCertiMailApiAndes()
	{
		set_time_limit(180);
		//***************************************************************************************************
		$response_data = null;
		$ws_api = new WsApiAndes();
		$max_size = 20000000;
		$error_maxsize = "";
		//***************************************************************************************************
		try {
			$digit_com = $this->getAttachComDocDigit();
			$params_service = array();
			$certimail_destino = array();
			$email_all = array();
			//***********************************************************************************************
			$files_attach = $this->getListAttachmentCom();
			if(count($files_attach)){
				$finfo = new SplFileInfo($digit_com);
				$filename = sprintf("%s.%s",$finfo->getBasename('.'.$finfo->getExtension()),'zip');
				$pathzip_tmp = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR .md5(time().$this->getPrimaryKey()). DIRECTORY_SEPARATOR;
				//*******************************************************************************************
				$attach_list = array();
				$fsize_all = $finfo->getSize();
				foreach ($files_attach as $fattach) {
					$fsize_all += $fattach['filesize'];
					$attach_list[] = $fattach['fullpath'];
				}
				//*******************************************************************************************
				if($fsize_all >= $max_size)
				{
					$attach_list = array();
					$attach_list[] = $digit_com;
					$error_maxsize = "Error, Los archivos adjuntos sobrepasan el tamaño maximo permitido por el proveedor de correo certificado, solo se enviara la comunicacion sin anexos";
				}else{
					$attach_list[] = $digit_com;
				}
				//*******************************************************************************************
				$pathzip = simad_util::createZipFileByFiles($attach_list,$pathzip_tmp);
				$file_base64 = file_exists($pathzip) ? simad_util::getConvertFileToB64($pathzip) : null;
			}else {
				$file_base64 = simad_util::getConvertFileToB64($digit_com);
				$filename = basename($digit_com);
			}
			//***********************************************************************************************
			if(empty($file_base64)){
				return array( 'status' => 400, 'message' => 'IOException Error, no se pudo crear el archivo adjunto para la notificación');
			}
			//***********************************************************************************************
			$params_service['ASUNTO_COM'] = mb_convert_encoding($this->getDetalle(),'UTF-8');
			$params_service['ARCHIVO_B64'] = $file_base64;
			$params_service['FILENAME'] = $filename;
			$entidad_destino = "";
			$funcionario_destino = "";
			$email_destino = trim($this->getEmailDestino()) ? preg_split("/[;]+/",trim($this->getEmailDestino()), -1, PREG_SPLIT_NO_EMPTY) : null;
			//***********************************************************************************************
			if(count($email_destino) <= 0){
				$email_all = !empty($email_destino) ? array_merge($email_all,$email_destino) : $email_all;
				$interesados_list = ServicioPeer::getListIntersadosByComId($this->getPrimaryKey());
				//*******************************************************************************************
				if(count($interesados_list) >= 0){
					foreach ($interesados_list as $row) {
						$interesado_com = $row->getInteresados();
						$entidad_destino = mb_convert_encoding($interesado_com->getNombreCompuesto(),'UTF-8');
						$email_interesado = trim($interesado_com->getEmail());
						if(!in_array($email_interesado,$email_all)){
							$certimail_destino[] = array('ENTIDAD_DESTINO'=>$entidad_destino,'FUNCIONARIO_DESTINO'=>$funcionario_destino,'EMAIL_DESTINO'=>$email_interesado);
							$email_all[] = $email_destino;
						}
					}
				}
				//*******************************************************************************************
				if($this->getDirectorioexternoId() != null){
					$directorio_externo = DirectorioExternoPeer::retrieveByPK($this->getDirectorioexternoId());
					$entidad_destino = mb_convert_encoding($directorio_externo->getNombre(),'UTF-8');
					$funcionario_destino = mb_convert_encoding($directorio_externo->getFuncionario(),'UTF-8');
					$email_destino = trim($directorio_externo->getEmail());
					//***************************************************************************************
					if(!in_array($email_destino,$email_all)){
						$certimail_destino[] = array('ENTIDAD_DESTINO'=>$entidad_destino,'FUNCIONARIO_DESTINO'=>$funcionario_destino,'EMAIL_DESTINO'=>$email_destino);
						$email_all[] = $email_destino;
					}
				}
			}else{
				foreach ($email_destino as $row) {
					$entidad_destino = trim($this->getFuncionarioDestino()) ? mb_convert_encoding($this->getFuncionarioDestino(),'UTF-8') : $row;
					if(!in_array($row,$email_all)){
						$certimail_destino[] = array('ENTIDAD_DESTINO'=>$entidad_destino,'FUNCIONARIO_DESTINO'=>null,'EMAIL_DESTINO'=>$row);
						$email_all[] = $email_destino;
					}
				}
			}
			//***********************************************************************************************
			$enotify_send = array();
			//***********************************************************************************************
			foreach ($certimail_destino as $item_parts) 
			{
				$umail_notify = array_merge($params_service,$item_parts);
				$response_data = $ws_api->addCertiMailEnviada($umail_notify);
				$fecha_envio = date("Y-m-d G:i:s");
				//*******************************************************************************************
				if($response_data['status'] == 200){
					$textresp_send = empty($error_maxsize) ? $response_data['message'] : $response_data['message']." ".$error_maxsize;
					//***************************************************************************************
					$bitacora_text = sprintf("Correo certificado enviado => %s => Email Notificado => %s",$textresp_send,$umail_notify['EMAIL_DESTINO']);
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$bitacora_text,$fecha_envio);
					$servicioestado_id = 10;//enviado por correo certificado
					//***************************************************************************************
					$this->setServicioestadoId($servicioestado_id);
					$this->save();
					//***************************************************************************************
					ServicioCertimailPeer::addServicioCertMail($this->getPrimaryKey(),$response_data['idMensaje'],'EnviadoEnteCertificador',ServicioAppExterna::CertiMail);
					//***************************************************************************************
					$enotify_send[] = array('status' => 200, 'message' => $textresp_send, 'email_notified' =>  $umail_notify['EMAIL_DESTINO']);
				}else{
					$textresp_send = empty($error_maxsize) ? $response_data['message'] : $response_data['message']." ".$error_maxsize;
					//***************************************************************************************
					$bitacora_text = sprintf("Error enviando correo certificado => %s => Email Notificado => %s",$textresp_send,$umail_notify['EMAIL_DESTINO']);
					ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$bitacora_text,$fecha_envio);
					$servicioestado_id = 9;//servicio ejecutado devuelto
					//***************************************************************************************
					$obsDevolucion = trim($this->getObsDevolucion()) ? trim($this->getObsDevolucion()) .'|'.$textresp_send : $textresp_send;
					//***************************************************************************************
					$this->setServicioestadoId($servicioestado_id);
					$this->setObsDevolucion($obsDevolucion);
					$this->save();
					//***************************************************************************************
					$enotify_send[] = array('status' => 400, 'message' => $textresp_send, 'email_notified' =>  $umail_notify['EMAIL_DESTINO']);
				}
			}
			//***********************************************************************************************
			return $enotify_send;
		} catch (PropelException $th) {
			return array( 'status' => 400, 'message' => 'PropelException('.$th->getMessage().') error, ocurrio un error interno en el servidor');
		} catch (\Exception $th) {			
			return array( 'status' => 400, 'message' => 'Exception error, ocurrio un error interno en el servidor');	
		} catch (\Throwable $th) {
			return array( 'status' => 400, 'message' => 'Throwable error, ocurrio un error interno en el servidor');	
		}
	}

	/**
    * servicioActions::attachNewDocumetBitacora()
    * funcion que realiza integracion con porveedor de correo certificado
    * @return
    */
	public function attachNewDocumetBitacora($nfile = null, $desc_documento = null, $servicioestado_id = 3)
	{
		try {
			$path_data = $this->getBasicUrlAttach();
			//******************************************************************************************
			$info_file = new SplFileInfo($nfile);
			$directorio = simad_util::createPath($path_data['full_path']);
			$fileweb = $path_data['alias_web'].preg_replace('/\\\\+/', '/', $path_data['basic_path']).'/'.$info_file->getFilename();
			$path_target = $directorio.DIRECTORY_SEPARATOR.$info_file->getFilename();
			//******************************************************************************************
			$isError = false;
			if(rename($nfile, $path_target)){
				$list_state[] = array('filename' => $path_target, 'info' => 'El archivo se adjunto correctamente', 'isError' => $isError);
				//**************************************************************************************
				$result = true;
				$desc_documento = !empty($desc_documento) ? $desc_documento : "Acta de Envío y Entrega de Correo Electrónico";
				$folios_doc = 2;
				if(!empty($path_target) && file_exists($path_target))
					ServicioPeer::insertAnexoServicios($this->getPrimaryKey(),$fileweb,$desc_documento,$folios_doc,$this->getUsuarioId());
				//**************************************************************************************
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$servicioestado_id,$this->getUsuarioId(),$this->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
				//**************************************************************************************
				$this->setServicioestadoId($servicioestado_id);
				$this->save();
			}else{
				$isError = true;
				$list_state[] = array('filename' => $path_target, 'info' => 'No fue posible copiar el archivo', 'isError' => $isError);
				$result = false;
			}
			//******************************************************************************************
			return $result;
		} catch (PropelException $th) {
			return false;
		} catch (\Exception $th) {			
			return false;
		} catch (\Throwable $th) {
			return false;
		}
	}

	/**
    * servicioActions::archivarActaNotificacion()
    * funcion que realiza integracion con el porveedor de correo certificado
    * @return object ContenidoUnidadDocumental creado
    */
	public function archivarActaNotificacion($nfile,$descp_doc)
	{
		try {
			if(!file_exists($nfile)){ return null; }
			//*******************************************************************************************
			if(!empty($this->getConsecutivocomId()) && !empty($this->getModuloId()))
			{
				switch($this->getModuloId()){
					case 2: //interna
						$com_interna = ComInternaPeer::retrieveByPK($this->getConsecutivocomId());
						$contenidodoc_id = 	$com_interna->getContenidodocId();					
						break;
					case 3: //recibida
						$com_recibida = ComRecibidaPeer::retrieveByPK($this->getConsecutivocomId());
						$contenidodoc_id = 	$com_recibida->getContenidodocId();
						break;
					case 4: //enviada
						$com_enviada = ComEnviadaPeer::retrieveByPK($this->getConsecutivocomId());
						$contenidodoc_id = 	$com_enviada->getContenidodocId();
						break;
					case 17: //acto_administrativo
						$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getConsecutivocomId());
						$contenidodoc_id = 	$acto_administrativo->getContenidodocId();
						break;
					default:
						return null;
				}
				//*******************************************************************************************
				$info_file = new SplFileInfo($nfile);
				//*******************************************************************************************
				if(!empty($contenidodoc_id)){
					$contenido_documental = ContenidoUnidadDocumentalPeer::retrieveByPK($contenidodoc_id);
					if($contenido_documental != null){
						$storage_trd = $contenido_documental->getUnidadDocumental()->getBaseUrlAttachment();
						//***********************************************************************************
						$full_path = $storage_trd['full_path'].DIRECTORY_SEPARATOR.$info_file->getFilename();
						if(!copy($nfile,$full_path)){
							return null;
						}
						//***********************************************************************************
						$params_doc['unidad_documental_pk'] = $contenido_documental->getUnidaddocumentalId();
						$params_doc['verificacion_doc'] = 1;//verificado
						$params_doc['tipo_doc_id'] = $contenido_documental->getTipodocumentalId();
						$params_doc['estado_doc'] = 1;//revisado
						$params_doc['usuario_id'] = $this->getUsuarioId();
						$params_doc['tipofirmadigital_id'] = null;
						$params_doc['soporte_documental'] = 6;
						$params_doc['origen_documento'] = 1;
						$params_doc['descripcion'] = $descp_doc;
						$params_doc['archivo_nombre'] = basename($nfile);
						$params_doc['folios'] = 1;
						$params_doc['fecha_documento'] = date('Y-m-d');
						$params_doc['format_file'] = $info_file->getExtension();
						$params_doc['size_file'] = $info_file->getSize();
						$params_doc['path_absolute'] = $storage_trd['absolute_path'];
						$params_doc['path_relative'] = $storage_trd['basic_path'];
						//***********************************************************************************
						$contdoc_new = ContenidoUnidadDocumentalPeer::addContenidoUnidadDocumental($params_doc);
						if($contdoc_new == null){ 
							return null;
						}else{ 
							return $contdoc_new; 
						}
					}else{
						return null;
					}
				}else{
					return null;
				}
			}
		} catch (PropelException $th) {
			return null;
		} catch (\Exception $th) {			
			return null;
		} catch (\Throwable $th) {
			return null;
		}
	}

	/**
    * servicioActions::addActaNotificacionToCom()
    * funcion que realiza integracion con porveedores de correo certificado
    * @return
    */
	public function addActaNotificacionToCom($nfile)
	{
		try {
			if(!file_exists($nfile)){ return null; }
			$info_file = new SplFileInfo($nfile);
			//*******************************************************************************************
			if(!empty($this->getConsecutivocomId()) && !empty($this->getModuloId()))
			{
				switch($this->getModuloId()){
					case 2: //interna
						$com_interna = ComInternaPeer::retrieveByPK($this->getConsecutivocomId());
						$radicado_com = trim($com_interna->getRadicado());
						$response_attach = $com_interna->addNewAttachDocument(array($nfile),$this->getUsuarioId());
						return $response_attach['isError'] == false ? $radicado_com : null;
						break;
					case 3: //recibida
						$com_recibida = ComRecibidaPeer::retrieveByPK($this->getConsecutivocomId());
						$radicado_com = trim($com_recibida->getRadicado());
						$response_attach = $com_recibida->addNewAttachDocument(array($nfile),$this->getUsuarioId());
						return $response_attach['isError'] == false ? $radicado_com : null;
						break;
					case 4: //enviada
						$com_enviada = ComEnviadaPeer::retrieveByPK($this->getConsecutivocomId());
						$radicado_com = trim($com_enviada->getRadicado());
						$response_attach = $com_enviada->addNewAttachDocument(array($nfile),$this->getUsuarioId());
						return $response_attach['isError'] == false ? $radicado_com : null;
						break;
					case 17: //acto_administrativo
						$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getConsecutivocomId());
						$radicado_com = $acto_administrativo->getRadicadoCompuesto();
						//$area_codigo = trim($acto_administrativo->getDependencia()->getCodigo());
						//$area_codigo.'_'.trim($acto_administrativo->getNumeroResolucion()).'_'.$acto_administrativo->getFechaCreacion("Y");
						$response_attach = $acto_administrativo->addNewAttachDocument(array($nfile),$this->getUsuarioId());
						return $response_attach['isError'] == false ? $radicado_com : null;
						break;
					default:
						return null;
				}
			}else{
				return null;
			}
		} catch (PropelException $th) {
			return null;
		} catch (\Exception $th) {			
			return null;
		} catch (\Throwable $th) {
			return null;
		}
	}

	/**
    * servicioActions::apiSipostSvcActaEntrega()
    * funcion que realiza integracion con de correo certificado fisico
    * @return
    */
	public function apiSipostSvcActaEntrega()
	{
		try {
			$enable_service = true;//implementar la lectura de un parametro para habilitar y deshabilitar el servicio
			//************************************************************************************************
			if($enable_service == false){
				return array('statusHttp' => 400, 'message' => 'El servicio de correo certificado fisico esta deshabilitado');
			}
			//************************************************************************************************
			$api_sipost = new WsApiSipost($enable_service);
			$trazabilidad_guias = $api_sipost->getTrazabilidadCorreoFisico([
				'username' => WsApiSipost::SERVICE_TRAZABILIDAD_USER,
				'password' => WsApiSipost::SERVICE_TRAZABILIDAD_PASSW,
				'endpoint_login' => 'Login',
				'endpoint_trazabilidad' => 'TraceabilityCorp/Summarized',
			],$this->getPrimaryKey(),$this->getGuia());
			//************************************************************************************************
			if($trazabilidad_guias == null){
				foreach ($trazabilidad_guias as $row) {
					//guardar el archivo
					//$this->attachNewDocumetBitacora($api_sipost->fullpath, $desc_doc, $estadoservicio_current);
				}
				return array('statusHttp' => 400, 'message' => 'Error al obtener el token de autorizacion','file' => null);
			}else{
				return array('statusHttp' => 400, 'message' => 'Error al obtener datos de la guia desde el proveedor del servicio','file' => null);
			}
		} catch (PropelException $th) {
			return array('statusHttp' => 400, 'message' => 'Error obteniendo los datos del servicio');
		} catch (\Exception $th) {
			return array('statusHttp' => 400, 'message' => 'Error interno de la aplicación');
		} catch (\Throwable $th) {
			return array('statusHttp' => 400, 'message' => 'Error interno del servidor');
		}
	}

	/**
    * servicioActions::apiSipostSvc()
    * funcion que realiza integracion con de correo certificado fisico
    * @return
    */
	public function apiSipostSvc()
	{
		$ilist_response = array();
		//***************************************************************************************************
		try {
			foreach ($this->getServicioInteresadoss() as $srvinteresado) {
				$list_destinatarios[] = $srvinteresado->getInteresados();
			}
			//************************************************************************************************
			if(!empty($this->getDirectorioexternoId())){
				$list_destinatarios[] = DirectorioExternoPeer::retrieveByPK($this->getDirectorioexternoId());
			}
			//************************************************************************************************
			foreach ($list_destinatarios as $object) 
			{
				if($object == null){ continue; }
				//********************************************************************************************
				$response_svc = $this->addSendSipostSvc($object);
				//********************************************************************************************
				if($response_svc['statusHttp'] === 200)
				{
					$this->save();
					//****************************************************************************************
					ServicioCertimailPeer::addServicioCertMail($this->getPrimaryKey(), $response_svc['guia_barcode'],
						'EnviadoEnteCertificador', ServicioAppExterna::CorreoFisicoNacional);
				}
				//********************************************************************************************
				$ilist_response[] = $response_svc;
			}
		} catch (PropelException $th) {
			return array('statusHttp' => 400, 'message' => 'Error obteniendo los datos del servicio');
		} catch (\Exception $th) {
			return array('statusHttp' => 400, 'message' => 'Error interno de la aplicación');
		} catch (\Throwable $th) {
			return array('statusHttp' => 400, 'message' => 'Error interno del servidor');
		}
		//****************************************************************************************************
		return $ilist_response;
	}

	/**
    * servicioActions::addSendSipostSvc()
    * funcion que realiza consume servicio de correo fisico certificado
	* @param mixed objeto del interesado o del destinatario
    * @return
    */
	private function addSendSipostSvc($object)
	{
		$response = array('statusHttp' => 200, 'message' => 'Guía generada exitosamente','file' => null);
		$post_api = null;
		//****************************************************************************************************
		try {
			$api_sipost = new WsApiSipost();
			$padmision = $api_sipost->getPreadminisionInfo([
				'username' => WsApiSipost::SERVICE_PREADMISION_USER,
				'password' => WsApiSipost::SERVICE_PREADMISION_PASSW,
				'endpoint_method' => 'GetHeadquarter/0',
				'service_name' => WsApiSipost::SERVICE_TYPE_SERVICENAME,
			]);
			//************************************************************************************************
			if($padmision == null || !isset($padmision->intCodeContract)){
				return array('statusHttp' => 400, 'message' => 'Error al obtener datos de la preadminision','file' => null);
			}
			//************************************************************************************************
			//CREACION DEL ARRAY GRANDE
			$post_api['intAditionalOS'] = 0;
			$post_api['intCodeContract'] = $padmision->intCodeContract;//12709
			$post_api['intCodeHeadquarter'] = $padmision->intCodeHeadquarter;//73331;
			$post_api['intCodeService'] = $padmision->intCodeService;//29
			$post_api['intGuidesNumber'] = 1;
			$post_api['intTypePay'] = $padmision->intTypePay;//3
			$post_api['intTypeRequest'] = 2;
			//************************************************************************************************
			//todos elementos del subarray placeReceiverBe  TABLA directorio externo o interesado
			$placeReceiverBe['intAditional'] = 0;
			$placeReceiverBe['intCodeCity'] = ""; 
			$placeReceiverBe['intCodeHeadquarter'] = 0;
			$placeReceiverBe['intCodeOperationalCenter'] = 0;
			$placeReceiverBe['intTypePlace'] = 2;
			$placeReceiverBe['strAddress'] = "";
			$placeReceiverBe['strAditional'] = "";
			$placeReceiverBe['strEmail'] = ""; 
			$placeReceiverBe['strLocker'] = "";
			$placeReceiverBe['strNameCountry'] = "CO";
			$placeReceiverBe['strPhone'] = "";
			//************************************************************************************************
			$lstShippingTraceBe['boolMasterGuide'] = false;
			$lstShippingTraceBe['placeReceiverBe'] = $placeReceiverBe;
			//************************************************************************************************
			$lstShippingTraceBe['boolLading'] = false;			
			//************************************************************************************************
			// TABLA INTERESADOS O DIRECTORIO EXTERNO.
			$customerReceiverBe['intAditional']    = 0;
			$customerReceiverBe['intCodeCity']     = "";
			$customerReceiverBe['intTypeActor']    = 3;
			$customerReceiverBe['intTypeDocument'] = 1;
			$customerReceiverBe['strAddress']      = "";
			$customerReceiverBe['strAditional']    = "";
			$customerReceiverBe['strCountry']      = "CO";
			$customerReceiverBe['strDocument']     = "";  
			$customerReceiverBe['strEmail']        = "";  
			$customerReceiverBe['strLastNames']    = "";
			$customerReceiverBe['strNames']        = ""; 
			$customerReceiverBe['strPhone']        = "";
			//************************************************************************************************
			// DATOS DE LA UNIDAD DE VICTIMAS ALGUNOS SALEN DE LA REGIONAL DEL USUARIO INTERESADO.
			$customerSenderBe['intAditional']     = 0;
			$customerSenderBe['intCodeCity']      = str_pad($this->getRegional()->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
			$customerSenderBe['intTypeActor']     = 2;
			$customerSenderBe['intTypeDocument']  = 1;
			$customerSenderBe['strAddress']       = trim($this->getRegional()->getDireccion());
			$customerSenderBe['strAditional']     = "";
			$customerSenderBe['strCountry']       = "CO";
			$customerSenderBe['strDocument']      = "900490473";
			$customerSenderBe['strEmail']         = "servicioalciudadano@unidadvictimas.gov.co";
			$customerSenderBe['strLastNames']     = "UNIDAD PARA LA ATENCION  Y REPARACION INTEGRAL A LAS VICTIMAS";
			$customerSenderBe['strNames']         = "UARIV";
			$customerSenderBe['strPhone']         = "6014261111";
			//************************************************************************************************
			$lstShippingTraceBe['customerSenderBe'] = $customerSenderBe;
			//************************************************************************************************
			$lstShippingTraceBe['decCollectValue'] = 0;
			$lstShippingTraceBe['decLading'] = 0;
			$lstShippingTraceBe['intAditionalShipping'] = 0;
			$lstShippingTraceBe['intAditionalShipping1'] = 0;
			$lstShippingTraceBe['intAditionalShipping2'] = 0;
			$lstShippingTraceBe['intDeclaredValue'] = empty(trim($this->getValorGuia())) ? 200 : trim($this->getValorGuia());
			$lstShippingTraceBe['intHeight'] = 10; 
			$lstShippingTraceBe['intLength'] = 10;
			$lstShippingTraceBe['intWidth'] = 10; 
			$lstShippingTraceBe['intWeight'] = 200; 
			//************************************************************************************************
			$placeSenderBe['intAditional']             = 0;
			$placeSenderBe['intCodeCity']              = str_pad($this->getRegional()->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
			$placeSenderBe['intCodeHeadquarter']       = 0;
			$placeSenderBe['intCodeOperationalCenter'] = 0;
			$placeSenderBe['intTypePlace']             = 2;
			$placeSenderBe['strAddress']               = trim($this->getRegional()->getDireccion());
			$placeSenderBe['strAditional']             = "";
			$placeSenderBe['strEmail']                 = "unidaddevictimas@unidadvictimas.gov.co"; 
			$placeSenderBe['strLocker']                = "";
			$placeSenderBe['strNameCountry']           = "CO";
			$placeSenderBe['strPhone']                 = "6014261111";
			//************************************************************************************************
			$lstShippingTraceBe['placeSenderBe'] = $placeSenderBe;
			//************************************************************************************************
			$lstShippingTraceBe['strAditionalShipping'] = "";
			$lstShippingTraceBe['strIdentification'] = "";
			$lstShippingTraceBe['strObservation'] =  substr(trim($this->getDetalle()),0,250);
			$lstShippingTraceBe['strReference'] = substr(trim($this->getDetalle()),0,50);
			//************************************************************************************************
			$key_destino = 0;
			//************************************************************************************************
			if($object instanceof Interesados)
			{
				$interesado = $object;
				$key_destino = $interesado->getPrimaryKey();
				//********************************************************************************************
				$placeReceiverBe['intCodeCity'] = str_pad($interesado->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
				$placeReceiverBe['strAddress'] = $interesado->getDireccion();
				$placeReceiverBe['strEmail'] = $interesado->getEmail();
				$placeReceiverBe['strPhone'] = !empty(trim($interesado->getTelefono())) ? trim($interesado->getTelefono()) : trim($interesado->getCelular());
				//********************************************************************************************
				$customerReceiverBe['intCodeCity'] = str_pad($interesado->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
				$customerReceiverBe['strAddress'] = $interesado->getDireccion();
				$customerReceiverBe['strDocument'] = $interesado->getNumeroIdentificacion();
				$customerReceiverBe['strEmail'] = $interesado->getEmail();
				$customerReceiverBe['strLastNames'] = $interesado->getNombreCompuesto();
				$customerReceiverBe['strNames'] = $interesado->getNombreCompuesto();
				$customerReceiverBe['strPhone'] = !empty(trim($interesado->getTelefono())) ? trim($interesado->getTelefono()) : trim($interesado->getCelular());
				//********************************************************************************************
				if(empty(trim($interesado->getTelefono())) && empty(trim($interesado->getCelular()))){
					$imessage = sprintf('El interesado %s no tiene registrado ningun telefono de contacto, este valor es obligatorio',$interesado->getNombreCompuesto());
					return array('statusHttp' => 400, 'message' => $imessage);
				}
			}
			elseif($object instanceof DirectorioExterno) //empresas
			{
				$directorioexterno = $object;
				$key_destino = $directorioexterno->getPrimaryKey();
				//********************************************************************************************
				$placeReceiverBe['intCodeCity'] = str_pad($directorioexterno->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
				$placeReceiverBe['strAddress'] = $directorioexterno->getDireccion();
				$placeReceiverBe['strEmail'] = $directorioexterno->getEmail();
				$placeReceiverBe['strPhone'] = $directorioexterno->getTelefono();
				//********************************************************************************************
				$customerReceiverBe['intCodeCity'] = str_pad($directorioexterno->getCiudad()->getCodigoDane(),8,"0",STR_PAD_RIGHT);
				$customerReceiverBe['strAddress'] = $directorioexterno->getDireccion();
				$customerReceiverBe['strDocument'] = $directorioexterno->getNit();
				$customerReceiverBe['strEmail'] = $directorioexterno->getEmail();
				$customerReceiverBe['strLastNames'] = trim($directorioexterno->getNombre());
				$customerReceiverBe['strNames'] = trim($directorioexterno->getNombre());
				$customerReceiverBe['strPhone'] = $directorioexterno->getTelefono();
				//********************************************************************************************
				if(empty(trim($directorioexterno->getTelefono())))
				{
					$imessage = sprintf('El destinatario %s no tiene registrado ningun telefono de contacto, este valor es obligatorio',$directorioexterno->getNombreAndNuid());
					return array('statusHttp' => 400, 'message' => $imessage);
				}
			}
			//************************************************************************************************
			$lstShippingTraceBe['customerReceiverBe'] = $customerReceiverBe;
			$lstShippingTraceBe['placeReceiverBe'] = $placeReceiverBe;
			//************************************************************************************************
			$post_api['lstShippingTraceBe'] = [$lstShippingTraceBe];
			$post_api['boolMasterGuide'] = false;
			$post_api['strAditionalOS'] = "";
			//************************************************************************************************
			$json_data = json_encode($post_api);
			//************************************************************************************************
			$post_shipping = $api_sipost->createPostShipping([
				'username' => WsApiSipost::SERVICE_PREADMISION_USER,
				'password' => WsApiSipost::SERVICE_PREADMISION_PASSW,
				'endpoint_method' => 'PostShipping',
				'filename_shipping' => sprintf('PostShipping_KeySvc[%s]_KeyDest[%s].pdf',$this->getPrimaryKey(),$key_destino),
			],$json_data);
			//************************************************************************************************
			$post_shipping = !empty(get_object_vars($post_shipping)) ? $post_shipping : new stdClass();
			//************************************************************************************************
			if(!isset($post_shipping->intCodeError)){
				$error_msg = 'Error al consumir el servicio PostShipping, The server encountered an error processing the request,Please see the service help page for constructing valid requests to the service';
				$error_msg = sprintf("%s, Destinatario: %s",$error_msg,$customerReceiverBe['strDocument']." ".$customerReceiverBe['strNames']);
				$response = array('statusHttp' => 400, 'message' => $error_msg);
				//********************************************************************************************
				$estadoservicio_current = 9;
				$this->setServicioestadoId($estadoservicio_current);
				if(!empty($this->getObsDevolucion())){
					$this->setObsDevolucion(sprintf("%s|%s",trim($this->getObsDevolucion()),$error_msg));
				}else{
					$this->setObsDevolucion($error_msg);
				}
				//********************************************************************************************
				$this->setServicioestadoId($estadoservicio_current);
				//********************************************************************************************
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(),$this->getUsuarioId(),
					$this->getUsuarioId(),$error_msg);
			}elseif($post_shipping->status == 405){
				$response = array('statusHttp' => $post_shipping->status, 'message' =>$post_shipping->message);
				$estadoservicio_current = 9;
				//********************************************************************************************
				if(!empty($this->getObsDevolucion())){
					$this->setObsDevolucion(sprintf("%s|%s",trim($this->getObsDevolucion()),trim($post_shipping->message)));
				}else{
					$this->setObsDevolucion(trim($post_shipping->message));
				}
				//********************************************************************************************
				$this->setServicioestadoId($estadoservicio_current);
			}elseif(isset($post_shipping->intCodeError) && $post_shipping->intCodeError == 0){
				$destinatario_text = $customerReceiverBe['strDocument']." ".$customerReceiverBe['strNames'];
				//********************************************************************************************
				$estadoservicio_current = 2;
				$desc_doc = sprintf("Guía generada exitosamente con código de envio => #%s, Destinatario: %s",$post_shipping->strBarcode,$destinatario_text);
				//********************************************************************************************
				$response = array('statusHttp' => 200, 'message' => $desc_doc,'guia_barcode' => $post_shipping->strBarcode, 'file' => $api_sipost->fullpath);
				//********************************************************************************************
				//$this->attachNewDocumetBitacora($api_sipost->fullpath, $desc_doc, $estadoservicio_current);
				//********************************************************************************************
				$this->setEmpresaMensajeriaId(CourrierEnable::ServiciosPostales);
				$this->setServicioestadoId($estadoservicio_current);
				//********************************************************************************************
				if(empty(trim($this->getGuia()))){
					$this->setFechaEnvioGuia(date("Y-m-d G:i:s",strtotime($post_shipping->dtDatePreAdmission)));
					$this->setGuia($post_shipping->strBarcode);
					$this->setValorGuia($post_shipping->decTotalRate);
				}else{
					$this->setGuia(sprintf("%s;%s",trim($this->getGuia()),$post_shipping->strBarcode));
					//****************************************************************************************
					$current_value = $this->getValorGuia() + $post_shipping->decTotalRate;
					$this->setValorGuia($current_value);
					//****************************************************************************************
					if(empty($this->getFechaEnvioGuia())){
						$this->setFechaEnvioGuia(date("Y-m-d G:i:s",strtotime($post_shipping->dtDatePreAdmission)));
					}
				}
				//********************************************************************************************
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$estadoservicio_current,$this->getUsuarioId(),
					$this->getUsuarioId(),$desc_doc);
			}else{
				$destinatario_text = $customerReceiverBe['strDocument']." ".$customerReceiverBe['strNames'];
				$error_msg = sprintf("Error al generar la guía del servicio, CodeError: %s Error: %s, Destinatario %s",$post_shipping->intCodeError,$post_shipping->strError,$destinatario_text);
				$response = array('statusHttp' => 400, 'message' =>$error_msg);
				//********************************************************************************************
				//********************************************************************************************
				if(!empty($this->getObsDevolucion())){
					$this->setObsDevolucion(sprintf("%s|%s",trim($this->getObsDevolucion()),trim($error_msg)));
				}else{
					$this->setObsDevolucion(trim($error_msg));
				}
				//********************************************************************************************
				$estadoservicio_current = 9;
				$this->setServicioestadoId($estadoservicio_current);
				//********************************************************************************************
				ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(),$this->getUsuarioId(),
					$this->getUsuarioId(),$error_msg);
			}
			//************************************************************************************************
			return $response;
		} catch (PropelException $th) {
			$estadoservicio_current = 9;
			$this->setServicioestadoId($estadoservicio_current);
			return array('statusHttp' => 400, 'message' => 'Error obteniendo los datos del servicio');
		} catch (\Exception $th) {
			$estadoservicio_current = 9;
			$this->setServicioestadoId($estadoservicio_current);
			return array('statusHttp' => 400, 'message' => 'Error interno de la aplicación');
		} catch (\Throwable $th) {
			$estadoservicio_current = 9;
			$this->setServicioestadoId($estadoservicio_current);
			return array('statusHttp' => 400, 'message' => 'Error interno del servidor');
		}
	}

	/**
    * servicioActions::initIntegraciones()
    * funcion que realiza el proceso de integraciones del modulo de servicios
    * @return mixed array con los mensajes de respuestas
    */
	public function initIntegraciones($params = array())
	{
		set_time_limit(300);
		//************************************************************************************************************
		$message = array();
		$isError = false;
		$codeResp = 0;
		$coll_interesados = isset($params['coll_interesados']) ? $params['coll_interesados'] : array();
		$dependencia_id = isset($params['dependencia_origen']) ? $params['dependencia_origen'] : (isset($params['dependencia_id']) ? $params['dependencia_id'] : null);
		$fecha_envio = date("Y-m-d G:i:s");
		//************************************************************************************************************
		try {
			//integracion con comunicaciones enviadas o actos administrativos
			if($this->getTipoServicio()->getInitIntegracion() && ($this->getModuloId() == ModulesEnable::ComEnviada || $this->getModuloId() == ModulesEnable::ActosAdministrativos)){
				if(!empty($this->getConsecutivocomId())){
					try {
						$simadSoap = new WsSimadUariv();
						if($this->getModuloId() == ModulesEnable::ComEnviada){
							$response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getConsecutivocomId(),"Externa Enviada");
						}elseif(ModulesEnable::ActosAdministrativos){
							$response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getConsecutivocomId(),"Acto Administrativo",ModulesEnable::ActosAdministrativos);
						}
						//********************************************************************************************
						$message_acto = !empty($response_acto['message']) ? trim($response_acto['message']) : null;
						$message_status = !empty($response_acto['status']) ? trim($response_acto['status']) : 400;
						$msgintegracion = !empty($message_status) ? "&msgintegra=".$message_status : "";
						//********************************************************************************************
						if($message_status == 400){
							$servicioestado_id = 9;//servicio ejecutado devuelto
							$bitacora_text = !empty($message_acto) ? "Error Herramienta Externa, la notificacion NO fue enviada => ". $message_acto : "Error Herramienta Externa, la notificacion NO fue enviada";
							$message[] = $bitacora_text;
							//****************************************************************************************
							ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$servicioestado_id, $this->getUsuarioId(),$this->getUsuarioId(),$bitacora_text,$fecha_envio);
							//****************************************************************************************
							$obsDevolucion = trim($this->getObsDevolucion()) ? trim($this->getObsDevolucion()) .'|'.$bitacora_text : $bitacora_text;
							//****************************************************************************************
							$this->setServicioestadoId($servicioestado_id);
							$this->setObsDevolucion($obsDevolucion);
							$this->save();
							//****************************************************************************************
							$codeResp = $message_status;
							$isError = true;
						}else{
							$bitacora_text = !empty($message_acto) ? $message_acto : "Se realizo la integración con la herramienta externa";
							$message[] = $bitacora_text;
							//****************************************************************************************
							ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$bitacora_text,$fecha_envio);
						}
					} catch (PropelException $th) {
						$message[] = $th->getMessage();
						ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$msgintegracion,$fecha_envio);
					} catch (\Exception $th) {
						$message[] = $th->getMessage();
						ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$msgintegracion,$fecha_envio);
					} catch (\Throwable $th) {
						$message[] = $th->getMessage();
						ServicioPeer::insertBitacoraServicio($this->getPrimaryKey(),$this->getServicioestadoId(), $this->getUsuarioId(),$this->getUsuarioId(),$msgintegracion,$fecha_envio);
					}
				}
			}
			//********************************************************************************************************
			$msgnotify = "";
			if($this->getTipoServicio()->getTipoEnvio() == 2 && (!empty($this->getEmailDestino()) || count($coll_interesados) > 0)){
				$response_crtemail = $this->sendCertiMailApi();
				//****************************************************************************************************
				foreach ($response_crtemail as $response_item) {
					if(is_array($response_item)){
						if($response_item['status'] == 200){
							$codeResp = 200;
							$isError = false;
							$message[] = sprintf("<ul><li><strong>%s - %s</strong></li></ul>",'Correo Certificado Enviado => '.$response_item['message'],$response_item['email_notified']);
						}else{
							$codeResp = 400;
							$isError = true;	
							$message[] = sprintf("<ul><li><strong>%s - %s</strong></li></ul>",'Error Enviado Correo Certificado => '.$response_item['message'],$response_item['email_notified']);
						}
					}else{
						if($response_crtemail['status'] == 200){
							$codeResp = 200;
							$isError = false;
							$message[] = sprintf("<ul><li><strong>%s - %s</strong></li></ul>",'Correo Certificado Enviado => '.$response_crtemail['message'],$response_crtemail['email_notified']);
						}else{
							$codeResp = 400;
							$isError = true;	
							$message[] = sprintf("<ul><li><strong>%s - %s</strong></li></ul>",'Error Enviado Correo Certificado => '.$response_crtemail['message'],$response_crtemail['email_notified']);
						}
						
						break;
					}
				}
				//****************************************************************************************************
				$sendEmailNotify = $isError ? false : true;
				$msgnotify = ($sendEmailNotify != true) ? "&msgnotify=false" : "&msgnotify=true";
			}
			//********************************************************************************************************
			if($this->getTipoServicio()->getTipoEnvio() == 3 && (count($coll_interesados) > 0 || !empty($this->getDirectorioexternoId()))){
				$response_crtnnal = $this->apiSipostSvc(); 
				//****************************************************************************************************
				foreach ($response_crtnnal as $response_item) 
				{
					$tservicio_desc = $this->getTipoServicio()->getDescripcion();
					if($response_item['statusHttp'] == 200){
						$isError = false;
						$codeResp = 200;
						$message[] = sprintf("<ul><li><strong>%s</strong></li></ul>",$tservicio_desc.' => '.$response_item['message']);
					}else{
						$isError = true;
						$codeResp = 400;
						$message[] = sprintf("<ul><li><strong>%s</strong></li></ul>",'Error '.$tservicio_desc.' => '.$response_item['message']);
					}
				}
			}
			//********************************************************************************************************
			if($this->getTipoServicio()->getTipoEnvio() == 5 && (count($coll_interesados) > 0 || !empty($this->getDirectorioexternoId()))){
				if($dependencia_id){
					$sendEmailNotify = $this->envioEmailNotificacion($dependencia_id);
					$isError = $sendEmailNotify['error'];

					if($sendEmailNotify['error'] != true){
						$codeResp = 200;
						$message[] = "<ul><li><strong>{$sendEmailNotify['message']}</strong></li></ul>";
					}else{
						$codeResp = 400;
						$message[] =  "<ul><li><strong>Error => {$sendEmailNotify['message']}</strong></li></ul>";
					}
				}
			}
			//********************************************************************************************************
			$this->save();
			//********************************************************************************************************
			return array('isError' => $isError, 'code' => $codeResp, 'message' => implode(",",$message));
		} catch (PropelException $th) {
			return array('isError' => true, 'code' => 400, 'message' => $th->getMessage());
		} catch (\Exception $th) {
			return array('isError' => true, 'code' => 400, 'message' => $th->getMessage());
		} catch (\Throwable $th) {
			return array('isError' => true, 'code' => 400, 'message' => $th->getMessage());
		}
	}
}
