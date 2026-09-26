<?php
require_once(dirname(__FILE__) . '/../../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);
//*************************************************************************************************************************
function ConsultaPublicacionesWeb($no_radicado)
{
	$error = false;
	//*********************************************************************************************************************
	$b = new Criteria();
	$b->setDistinct();
	$b->addJoin(ComEnviadaPeer::COMENVIADA_ID, ComRecibidaPeer::COMENVIADA_ID, Criteria::LEFT_JOIN);
	$b->addJoin(ComEnviadaPeer::COMENVIADA_ID, EnviadaUsuarioPeer::COMENVIADA_ID, Criteria::INNER_JOIN);
	//$b->add(ComEnviadaPeer::ES_PUBLICAR,1);
	//$b->add(ComEnviadaPeer::FECHA_MAX_PUBLICACION,date("Y-m-d"), Criteria::GREATER_EQUAL);
	$b->add(ComEnviadaPeer::ESTADODIGITALIZACION_ID, 1);
	$b->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 2); //firma
	$ilist  = ComEnviadaPeer::doSelect($b);
	$respuesta_vars = array();
	//*********************************************************************************************************************
	if (count($ilist) > 0) {
		foreach ($ilist as $item) {
			$com_recibida = null;
			if ($item->getConsecutivoResp()) {
				$com_recibida = ComRecibidaPeer::retrieveByPK($item->getConsecutivoResp());
			}
			//$url = "http://192.9.200.173/simad/enviada.php/com_enviada/viewImage?comenviada_id=".$item->getPrimaryKey()."&vtoken=".$item->getHashPublicacion();
			$url = "http://192.9.200.173/simad/enviada.php/com_enviada/viewImage?comenviada_id=" . $item->getPrimaryKey() . "&vtoken=" . md5($item->getPrimaryKey());
			//$url = 'http://192.9.200.173/simad/enviada.php/com_enviada/viewImage?comenviada_id=';
			$feha_publicacion = ""; //$item->getFechaPublicacion("Y-m-d");
			$ciudad = $item->getCiudad();
			$fecha_pqr = ""; //$com_recibida != null ? $com_recibida->getFechaPqr("Y-m-d") : "";
			$nro_pqr = ""; //$com_recibida != null ? $com_recibida->getNroPqr() : "";
			$radicado = $item->getRadicado();
			$feha_max_publicacion = ""; //$item->getFechaMaxPublicacion("Y-m-d");
			$destinatario = ""; //(ComEnviada::getDestinatarioCom($item->getPrimaryKey()));
			//$destinatario = "";
			$respuesta_vars[] = array(
				'url' => $url,
				'radicado' => $radicado,
				'destinatario' => $destinatario,
				'ciudad' => $ciudad,
				'fecha_pqr' => $fecha_pqr,
				'nro_pqr' => $nro_pqr,
				'feha_publicacion' => $feha_publicacion,
				'feha_max_publicacion' => $feha_max_publicacion
			);
			//$respuesta_vars[] = $url.';'.$radicado.';'.$destinatario.';'.$ciudad.';'.$fecha_pqr.';'.$nro_pqr.';'.$feha_publicacion.';'.$feha_max_publicacion;
		}
	} else {
		$error = true;
		$respuesta_vars[] = 'none;none;none;none;none;none;none;none';
		//$respuesta_vars[] = array('url' => 'none', 'feha_publicacion' => 'none', 'ciudad' => 'none', 'fecha_pqr' => 'none','nro_pqr' => 'none', 'radicado' => 'none', 'feha_max_publicacion' => 'none', 'destinatario' => 'none');
	}
	//*********************************************************************************************************************
	//return base64_encode(serialize($respuesta_vars));
	return $respuesta_vars;
}

function ConsultaRadicadoPublic($EntSecurity = array(), $EntComInfo = array())
{
	$infoxml = file_get_contents("php://input");
	//*********************************************************************************************************************
	$respuesta_vars = array();
	$error = false;
	$fecha_transaccion = date("Y-m-d G:i:s");
	//*********************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//*****************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("ConsultaRadicadoPublic", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getPrimaryKey());
		//*****************************************************************************************************************
		$radicado_entrada = isset($EntComInfo['RADICADO_ENTRADA']) ? trim($EntComInfo['RADICADO_ENTRADA']) : null;
		$radicado_salida = isset($EntComInfo['RADICADO_SALIDA']) ? trim($EntComInfo['RADICADO_SALIDA']) : null;
		$numero_identificacion = isset($EntComInfo['NUMERO_IDENTIFICACION']) ? trim($EntComInfo['NUMERO_IDENTIFICACION']) : null;
		$numero_expediente = isset($EntComInfo['NUMERO_EXPEDIENTE']) ? trim($EntComInfo['NUMERO_EXPEDIENTE']) : null;
		$radicado_origen = isset($EntComInfo['RADICADO_ORIGEN']) ? trim($EntComInfo['RADICADO_ORIGEN']) : null;
		//*****************************************************************************************************************
		$anyParamValid = false;
		if (!empty($radicado_entrada)) {
			$anyParamValid = true;
			$info_request[] = "Radicado Entrada: " . $radicado_entrada;
		}
		if (!empty($radicado_salida)) {
			$anyParamValid = true;
			$info_request[] = "Radicado Salida: " . $radicado_salida;
		}
		if (!empty($numero_identificacion)) {
			$anyParamValid = true;
			$info_request[] = "Numero Identificacion: " . $numero_identificacion;
		}
		if (!empty($numero_expediente)) {
			$anyParamValid = true;
			$info_request[] = "Numero Expediente: " . $numero_expediente;
		}
		//*****************************************************************************************************************
		if (!$anyParamValid) {
			return responseErrorAttachment('Es obligatorio al menos un parametro de consulta');
		}
		$direccion_registra = "";
		$telefono_registra = "";
		//*****************************************************************************************************************
		if (!empty($radicado_entrada)) {
			$com_recibida  = ComRecibidaPeer::getObjectComByRadicadoOrId($radicado_entrada);
		}
		//*****************************************************************************************************************
		if (!empty($radicado_salida)) {
			$com_enviada  = ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_salida);
		}
		//*****************************************************************************************************************
		$list_comenviadas  = array();
		$list_comrecibidas  = array();
		if (!empty($numero_identificacion)) {
			$interesado = InteresadosPeer::getInteresadoByNuid($numero_identificacion);
			if ($interesado != null) {
				$list_comenviadas  = ComEnviadaPeer::getListComEnviadasByNuid($numero_identificacion);
				$list_comrecibidas  = ComRecibidaPeer::getListComRecibidasByNuid($numero_identificacion, null, $radicado_entrada, $radicado_salida);
			}
		}
		//*****************************************************************************************************************
		if (!empty($com_enviada)) {
			$info_img = $com_enviada->getListDigitDocument(true);
			$url_download = null;
			$url_inline = null;
			$list_anexos = array();
			$size_fdigit = 0;
			//*********************************************************************************************************
			foreach ($info_img as $attach) {
				if ($attach['TIPO_ATTACHMENT'] == 'DIGIT_COM') {
					$url_download = $attach['URL_DOWNLOAD'];
					$url_inline = $attach['URL'];
					$size_fdigit = $attach['SIZE_FILE'];
				} elseif ($attach['TIPO_ATTACHMENT'] == 'ANEXO') {
					$list_anexos[] = array('NOMBRE_ANEXO' => $attach['NOMBRE_ARCHIVO'], 'URL_ANEXO' => $attach['URL'], 'SIZE_FILE' => $attach['SIZE_FILE']);
				}
			}
			//*********************************************************************************************************
			$comenviada_vars['RADICADO'] = $com_enviada->getRadicado();
			$comenviada_vars['FECHA_RADICACION'] = $com_enviada->getFechaCreacion();
			$comenviada_vars['NUMERO_FOLIOS'] = $com_enviada->getFolios();
			$comenviada_vars['DIRECCION_REGISTRADA'] = $direccion_registra;
			$comenviada_vars['TELEFONO_REGISTRADO'] = $telefono_registra;
			$comenviada_vars['URL_RADICADO'] = trim($url_inline) ?: null;
			$comenviada_vars['URL_DOWNLOAD'] = trim($url_download) ?: null;
			$comenviada_vars['SIZE_FILE'] = $size_fdigit;
			$comenviada_vars['ASUNTO'] = $com_enviada->getAsunto();
			$comenviada_vars['SERVICIOS_RADICADO'] = (ServicioPeer::getListDigitDocument($radicado_salida, $radicado_origen, 4));
			$comenviada_vars['ANEXOS_RADICADO'] = $list_anexos;
			$comenviada_vars['ISERROR'] = false;
			$comenviada_vars['MSG_INFO'] = null;
			$comenviada_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars[] = $comenviada_vars;
		}
		//*************************************************************************************************************
		if (!empty($com_recibida)) {
			$info_img = $com_recibida->getListDigitDocument(true);
			$url_download = null;
			$url_inline = null;
			$list_anexos = array();
			$size_fdigit = 0;
			//*********************************************************************************************************
			foreach ($info_img as $attach) {
				if ($attach['TIPO_ATTACHMENT'] == 'DIGIT_COM') {
					$url_download = $attach['URL_DOWNLOAD'];
					$url_inline = $attach['URL'];
					$size_fdigit = $attach['SIZE_FILE'];
				} elseif ($attach['TIPO_ATTACHMENT'] == 'ANEXO') {
					$list_anexos[] = array('NOMBRE_ANEXO' => $attach['NOMBRE_ARCHIVO'], 'URL_ANEXO' => $attach['URL'], 'SIZE_FILE' => $attach['SIZE_FILE']);
				}
			}
			//*********************************************************************************************************
			$comrecibida_vars['RADICADO'] = $com_recibida->getRadicado();
			$comrecibida_vars['FECHA_RADICACION'] = $com_recibida->getFechaCreacion();
			$comrecibida_vars['NUMERO_FOLIOS'] = $com_recibida->getFolios();
			$comrecibida_vars['DIRECCION_REGISTRADA'] = $direccion_registra;
			$comrecibida_vars['TELEFONO_REGISTRADO'] = $telefono_registra;
			$comrecibida_vars['URL_RADICADO'] = trim($url_inline) ?: null;
			$comrecibida_vars['URL_DOWNLOAD'] = trim($url_download) ?: null;
			$comrecibida_vars['SIZE_FILE'] = $size_fdigit;
			$comrecibida_vars['ASUNTO'] = $com_recibida->getAsunto();
			$comrecibida_vars['SERVICIOS_RADICADO'] = array();
			$comrecibida_vars['ANEXOS_RADICADO'] = $list_anexos;
			$comrecibida_vars['ISERROR'] = false;
			$comrecibida_vars['MSG_INFO'] = null;
			$comrecibida_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars[] = $comrecibida_vars;
		}
		//*****************************************************************************************************************
		if (!empty($list_comenviadas)) {
			foreach ($list_comenviadas as $item) {
				$info_img = $item->getListDigitDocument(true);
				$url_download = null;
				$url_inline = null;
				$list_anexos = array();
				$size_fdigit = 0;
				//*********************************************************************************************************
				foreach ($info_img as $attach) {
					if ($attach['TIPO_ATTACHMENT'] == 'DIGIT_COM') {
						$url_download = $attach['URL_DOWNLOAD'];
						$url_inline = $attach['URL'];
						$size_fdigit = $attach['SIZE_FILE'];
					} elseif ($attach['TIPO_ATTACHMENT'] == 'ANEXO') {
						$list_anexos[] = array('NOMBRE_ANEXO' => $attach['NOMBRE_ARCHIVO'], 'URL_ANEXO' => $attach['URL'], 'SIZE_FILE' => $attach['SIZE_FILE']);
					}
				}
				//*********************************************************************************************************
				$comenviada_vars['RADICADO'] = $item->getRadicado();
				$comenviada_vars['FECHA_RADICACION'] = $item->getFechaCreacion();
				$comenviada_vars['NUMERO_FOLIOS'] = $item->getFolios();
				$comenviada_vars['DIRECCION_REGISTRADA'] = $direccion_registra;
				$comenviada_vars['TELEFONO_REGISTRADO'] = $telefono_registra;
				$comenviada_vars['URL_RADICADO'] = trim($url_inline) ?: null;
				$comenviada_vars['URL_DOWNLOAD'] = trim($url_download) ?: null;
				$comenviada_vars['SIZE_FILE'] = $size_fdigit;
				$comenviada_vars['ASUNTO'] = $item->getAsunto();
				$comenviada_vars['SERVICIOS_RADICADO'] = (ServicioPeer::getListDigitDocument($item->getRadicado(), null, 4));
				$comenviada_vars['ANEXOS_RADICADO'] = $list_anexos;
				$comenviada_vars['ISERROR'] = false;
				$comenviada_vars['MSG_INFO'] = null;
				$comenviada_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
				$respuesta_vars[] = $comenviada_vars;
			}
		}
		//*****************************************************************************************************************
		if (!empty($list_comrecibidas)) {
			foreach ($list_comrecibidas as $item) {
				$info_img = $item->getListDigitDocument(true);
				$url_download = null;
				$url_inline = null;
				$list_anexos = array();
				$size_fdigit = 0;
				//*********************************************************************************************************
				foreach ($info_img as $attach) {
					if ($attach['TIPO_ATTACHMENT'] == 'DIGIT_COM') {
						$url_download = $attach['URL_DOWNLOAD'];
						$url_inline = $attach['URL'];
						$size_fdigit = $attach['SIZE_FILE'];
					} elseif ($attach['TIPO_ATTACHMENT'] == 'ANEXO') {
						$list_anexos[] = array('NOMBRE_ANEXO' => $attach['NOMBRE_ARCHIVO'], 'URL_ANEXO' => $attach['URL'], 'SIZE_FILE' => $attach['SIZE_FILE']);
					}
				}
				//*********************************************************************************************************
				$comrecibida_vars['RADICADO'] = $item->getRadicado();
				$comrecibida_vars['FECHA_RADICACION'] = $item->getFechaCreacion();
				$comrecibida_vars['NUMERO_FOLIOS'] = $item->getFolios();
				$comrecibida_vars['DIRECCION_REGISTRADA'] = $direccion_registra;
				$comrecibida_vars['TELEFONO_REGISTRADO'] = $telefono_registra;
				$comrecibida_vars['URL_RADICADO'] = trim($url_inline) ?: null;
				$comrecibida_vars['URL_DOWNLOAD'] = trim($url_download) ?: null;
				$comrecibida_vars['SIZE_FILE'] = $size_fdigit;
				$comrecibida_vars['ASUNTO'] = $item->getAsunto();
				$comrecibida_vars['SERVICIOS_RADICADO'] = array();
				$comrecibida_vars['ANEXOS_RADICADO'] = $list_anexos;
				$comrecibida_vars['ISERROR'] = false;
				$comrecibida_vars['MSG_INFO'] = null;
				$comrecibida_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
				$respuesta_vars[] = $comrecibida_vars;
			}
		}
		//*****************************************************************************************************************
		if (count($respuesta_vars) == 0) {
			$default_vars['RADICADO'] = null;
			$default_vars['FECHA_RADICACION'] = null;
			$default_vars['NUMERO_FOLIOS'] = null;
			$default_vars['DIRECCION_REGISTRADA'] = null;
			$default_vars['TELEFONO_REGISTRADO'] = null;
			$default_vars['URL_RADICADO'] = null;
			$default_vars['URL_DOWNLOAD'] = null;
			$default_vars['SIZE_FILE'] = null;
			$default_vars['ASUNTO'] = null;
			$default_vars['SERVICIOS_RADICADO'] = array();
			$default_vars['ANEXOS_RADICADO'] = array();
			$default_vars['ISERROR'] = true;
			$default_vars['MSG_INFO'] = "No se encontraron registros asociados, con los parametros enviados";
			$default_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars[] = $default_vars;
		}
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . implode(";", $info_request));
			$wslog->setMensaje("Ejecuta Exitoso Genera Informacion");
			$wslog->save();
		}
	} catch (PropelException $th) {
		$respuesta_vars = array();
		$default_vars['RADICADO'] = null;
		$default_vars['FECHA_RADICACION'] = null;
		$default_vars['NUMERO_FOLIOS'] = null;
		$default_vars['DIRECCION_REGISTRADA'] = null;
		$default_vars['TELEFONO_REGISTRADO'] = null;
		$default_vars['URL_RADICADO'] = null;
		$default_vars['URL_DOWNLOAD'] = null;
		$default_vars['SIZE_FILE'] = null;
		$default_vars['ASUNTO'] = null;
		$default_vars['SERVICIOS_RADICADO'] = array();
		$default_vars['ANEXOS_RADICADO'] = array();
		$default_vars['ISERROR'] = true;
		//$default_vars['MSG_INFO'] = $th->getMessage();
		$default_vars['MSG_INFO'] = "Error de acceso a los datos";
		$default_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
		$respuesta_vars[] = $default_vars;
	} catch (Exception $th) {
		$respuesta_vars = array();
		$default_vars['RADICADO'] = null;
		$default_vars['FECHA_RADICACION'] = null;
		$default_vars['NUMERO_FOLIOS'] = null;
		$default_vars['DIRECCION_REGISTRADA'] = null;
		$default_vars['TELEFONO_REGISTRADO'] = null;
		$default_vars['URL_RADICADO'] = null;
		$default_vars['URL_DOWNLOAD'] = null;
		$default_vars['ASUNTO'] = null;
		$default_vars['ISERROR'] = true;
		$default_vars['MSG_INFO'] = $th->getMessage();
		$default_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
		$respuesta_vars[] = $default_vars;
	}
	//*********************************************************************************************************************
	return $respuesta_vars;
}

function UserAutenticate($EntSecurity = array(), $WSUserSgdea = array())
{
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array('IsError' =>  $usuario_ws['isError'], 'MsgError' => $usuario_ws['message']);
		}
		//*********************************************************************************************************************
		$UserSecurity['SimadUserWs'] = $WSUserSgdea['UserNameSgdea'];
		$UserSecurity['SimadPassWs'] = $WSUserSgdea['PasswordSgdea'];
		$UserSecurity['SimadTypeGen'] = 1;
		//*********************************************************************************************************************
		$usuario_user = UsuarioPeer::autenticateUserWs($UserSecurity);
		if ($usuario_user['isError']) {
			return array('IsError' =>  $usuario_user['isError'], 'MsgError' => $usuario_user['message']);
		}
		//*********************************************************************************************************************
		$cargo_usuario = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_user['object']->getPrimaryKey(), true);
		$list_object['cargousuario_id'] = $cargo_usuario->getPrimaryKey();
		$list_object['usuario_id'] = $cargo_usuario->getUsuarioId();
		$list_object['user_name'] = $usuario_user['object']->getUserName();
		$list_object['usuario_ad'] = $usuario_user['object']->getUsuarioAd();
		$list_object['numero_identificacion'] = $usuario_user['object']->getCedula();
		$list_object['nombre'] = $usuario_user['object']->getNombre();
		$list_object['apellido'] = $usuario_user['object']->getApellido();
		$list_object['dependencia'] = $usuario_user['object']->getDependencia()->getNombre();
		$list_object['dependencia_id'] = $usuario_user['object']->getDependenciaId();
		$list_object['cargo'] = $cargo_usuario->getCargo()->getDescripcion();
		$list_object['regional'] = $usuario_user['object']->getRegional()->getDescripcion();
		$list_object['regional_id'] = $usuario_user['object']->getRegionalId();
		$list_object['ciudad'] = $usuario_user['object']->getRegional()->getCiudad()->getNombre();
		$list_object['ciudad_id'] = $usuario_user['object']->getRegional()->getCiudadId();
		$list_object['IsError'] = false;
		$list_object['MsgError'] = null;
		//*********************************************************************************************************************
		return $list_object;
	} catch (PropelException $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error de acceso a los datos");
	} catch (\Exception $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error interno de la aplicacion");
	} catch (\Throwable $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error interno del servidor");
	}
}

function UserAuthorization($EntSecurity = array(), $WSPrivilegioUser = array())
{
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array('IsError' =>  $usuario_ws['isError'], 'MsgError' => $usuario_ws['message']);
		}
		//*********************************************************************************************************************
		$UserSecurity['SimadUserWs'] = $WSPrivilegioUser['UserNameSgdea'];
		$UserSecurity['SimadPassWs'] = $WSPrivilegioUser['PasswordSgdea'];
		$UserSecurity['SimadTypeGen'] = 1;
		//*********************************************************************************************************************
		$usuario_user = UsuarioPeer::autenticateUserWs($UserSecurity);
		if ($usuario_user['isError']) {
			return array('IsError' =>  $usuario_user['isError'], 'MsgError' => $usuario_user['message']);
		}
		//*********************************************************************************************************************
		$cargo_usuario = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_user['object']->getPrimaryKey(), true);
		$isAuthorized = false;
		//*********************************************************************************************************************
		$c = new Criteria();
		$c->addJoin(RolPrivilegioPeer::FORMA_ID, FormaPeer::FORMA_ID, Criteria::INNER_JOIN);
		$c->addJoin(RolPrivilegioPeer::ROL_ID, RolPorUsuarioPeer::ROL_ID, Criteria::INNER_JOIN);
		$c->add(FormaPeer::NOMBRE, trim($WSPrivilegioUser['PrivilegioName']));
		$c->add(RolPorUsuarioPeer::USUARIO_ID, $cargo_usuario->getUsuarioId());
		$isAuthorized = RolPrivilegioPeer::doCount($c);

		if (!$isAuthorized) {
			$c = new Criteria();
			$c->addJoin(UsuarioPrivilegioPeer::FORMA_ID, FormaPeer::FORMA_ID, Criteria::INNER_JOIN);
			$c->add(FormaPeer::NOMBRE, trim($WSPrivilegioUser['PrivilegioName']));
			$c->add(UsuarioPrivilegioPeer::USUARIO_ID, $cargo_usuario->getUsuarioId());
			$isAuthorized = UsuarioPrivilegioPeer::doCount($c);
		}
		//*********************************************************************************************************************
		$list_object['usuario_id'] = $cargo_usuario->getUsuarioId();
		$list_object['nombre'] = $usuario_user['object']->getNombre();
		$list_object['apellido'] = $usuario_user['object']->getApellido();
		$list_object['privilegio_nombre'] = trim($WSPrivilegioUser['PrivilegioName']);
		$list_object['authorized'] = $isAuthorized ? true : false;
		$list_object['IsError'] = false;
		$list_object['MsgError'] = null;
		//*********************************************************************************************************************
		return $list_object;
	} catch (PropelException $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error de acceso a los datos");
	} catch (\Exception $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error interno de la aplicacion");
	} catch (\Throwable $ex) {
		return array('IsError' =>  true, 'MsgError' => "Error interno del servidor");
	}
}

function GetHistoricoRadicado($EntSecurity = array(), $EntComHist = array())
{
	$respuesta_vars = array();
	$infoxml = file_get_contents("php://input");
	$fecha_transaccion = date("Y-m-d G:i:s");
	$error = false;
	//*********************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//*****************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("GetHistoricoRadicado", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getPrimaryKey());
		//*****************************************************************************************************************
		$radicado_entrada = isset($EntComHist['RADICADO_ENTRADA']) ? trim($EntComHist['RADICADO_ENTRADA']) : null;
		$radicado_salida = isset($EntComHist['RADICADO_SALIDA']) ? trim($EntComHist['RADICADO_SALIDA']) : null;
		//*****************************************************************************************************************
		$anyParamValid = false;
		if (!empty($radicado_entrada)) {
			$anyParamValid = true;
		}
		if (!empty($radicado_salida)) {
			$anyParamValid = true;
		}
		//*****************************************************************************************************************
		if (!$anyParamValid) {
			$respuesta_vars['RADICADO'] = null;
			$respuesta_vars['USUARIO_ASIGNADO'] = null;
			$respuesta_vars['DEPENDENCIA_ASIGNADA'] = null;
			$respuesta_vars['USUARIO_RADICADOR'] = null;
			$respuesta_vars['DEPENDENCIA_RADICADOR'] = null;
			$respuesta_vars['RADICADO_FLUJO'] = array();
			$respuesta_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars['ISERROR'] = true;
			$respuesta_vars['MSG_INFO'] = 'Debe enviar un radicado para consultar';
			//*************************************************************************************************************
			return $respuesta_vars;
		}
		//*****************************************************************************************************************
		if (!empty($radicado_entrada)) {
			$com_recibida  = ComRecibidaPeer::getObjectComByRadicadoOrId($radicado_entrada);
		}
		//*****************************************************************************************************************
		if (!empty($radicado_salida)) {
			$com_enviada  = ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_salida);
		}
		//*****************************************************************************************************************
		if (!empty($com_enviada)) {
			$list_users = $com_enviada->getBitacoraHistCom();
			$respuesta_vars['RADICADO'] = $com_enviada->getRadicado();
			$respuesta_vars['USUARIO_ASIGNADO'] = isset($list_users['destinatario_actual']) ? $list_users['destinatario_actual'] : null;
			$respuesta_vars['DEPENDENCIA_ASIGNADA'] = isset($list_users['dependencia_actual']) ? $list_users['dependencia_actual'] : null;
			$respuesta_vars['USUARIO_RADICADOR'] = isset($list_users['radicador']) ? $list_users['radicador'] : null;
			$respuesta_vars['DEPENDENCIA_RADICADOR'] = isset($list_users['dependencia_radicador']) ? $list_users['dependencia_radicador'] : null;
			$respuesta_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars['ISERROR'] = false;
			$respuesta_vars['MSG_INFO'] = null;
			//*************************************************************************************************************
			$wflist_acom = array();
			foreach ($list_users['eventos_list'] as $item) {
				$wf_evento = array();
				$wf_evento['RADICADO'] = $com_enviada->getRadicado();
				$wf_evento['DEPENDENCIA'] = isset($item['dependencia']) ? $item['dependencia'] : null;
				$wf_evento['FECHA_ACTIVIDAD'] = isset($item['fecha_recibido']) ? $item['fecha_recibido'] : null;
				$wf_evento['ACTIVIDAD_FLUJO'] = isset($item['proceso']) ? $item['proceso'] : null;
				$wf_evento['USUARIO_ACTIVIDAD'] = isset($item['destinatario']) ? $item['destinatario'] : null;
				$wf_evento['OBSERVACIONES'] = isset($item['observaciones']) ? $item['observaciones'] : null;
				$wflist_acom[] = $wf_evento;
			}
			//*************************************************************************************************************
			$respuesta_vars['RADICADO_FLUJO'] = $wflist_acom;
		}
		//*****************************************************************************************************************
		if (!empty($com_recibida)) {
			$list_users = $com_recibida->getBitacoraHistCom();
			$respuesta_vars['RADICADO'] = $com_recibida->getRadicado();
			$respuesta_vars['USUARIO_ASIGNADO'] = isset($list_users['destinatario_actual']) ? $list_users['destinatario_actual'] : null;
			$respuesta_vars['DEPENDENCIA_ASIGNADA'] = isset($list_users['dependencia_actual']) ? $list_users['dependencia_actual'] : null;
			$respuesta_vars['USUARIO_RADICADOR'] = isset($list_users['radicador']) ? $list_users['radicador'] : null;
			$respuesta_vars['DEPENDENCIA_RADICADOR'] = isset($list_users['dependencia_radicador']) ? $list_users['dependencia_radicador'] : null;
			$respuesta_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
			$respuesta_vars['ISERROR'] = false;
			$respuesta_vars['MSG_INFO'] = null;
			//*************************************************************************************************************
			$wflist_acom = array();
			foreach ($list_users['eventos_list'] as $item) {
				$wf_evento = array();
				$wf_evento['RADICADO'] = $com_recibida->getRadicado();
				$wf_evento['DEPENDENCIA'] = isset($item['dependencia']) ? $item['dependencia'] : null;
				$wf_evento['FECHA_ACTIVIDAD'] = isset($item['fecha_recibido']) ? $item['fecha_recibido'] : null;
				$wf_evento['ACTIVIDAD_FLUJO'] = isset($item['proceso']) ? $item['proceso'] : null;
				$wf_evento['USUARIO_ACTIVIDAD'] = isset($item['destinatario']) ? $item['destinatario'] : null;
				$wf_evento['OBSERVACIONES'] = isset($item['observaciones']) ? $item['observaciones'] : null;
				$wflist_acom[] = $wf_evento;
			}
			//*************************************************************************************************************
			$respuesta_vars['RADICADO_FLUJO'] = $wflist_acom;
		}
	} catch (Exception $th) {
		$respuesta_vars = array();
		$default_vars['RADICADO'] = null;
		$default_vars['FECHA_RADICACION'] = null;
		$default_vars['NUMERO_FOLIOS'] = null;
		$default_vars['DIRECCION_REGISTRADA'] = null;
		$default_vars['TELEFONO_REGISTRADO'] = null;
		$default_vars['URL_RADICADO'] = null;
		$default_vars['ASUNTO'] = null;
		$default_vars['ISERROR'] = true;
		$default_vars['MSG_INFO'] = $th->getMessage();
		$default_vars['FECHA_TRANSACCION'] = $fecha_transaccion;
		$respuesta_vars[] = $default_vars;
	}
	//*********************************************************************************************************************
	return $respuesta_vars;
}

function getPuntoRadicacion($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//*********************************************************************************************************************
	$usuario_creador = $usuario_ws['object'];
	$list_regional = array();
	foreach (RegionalPeer::getAllRegional() as $object) {
		$list_regional[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => (strtoupper($object->getDescripcion())));
	}
	//*********************************************************************************************************************
	return $list_regional;
}

function getTipoComRecibida($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//*********************************************************************************************************************
	$list_object = array();
	foreach (TipoComRecibidaPeer::getTipoComRecibidaAll() as $object) {
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => (strtoupper($object->getDescripcionCompuesta())));
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getFormaRecepcionList()
{
	$list_object = array();
	foreach (FormaRecepcionPeer::getFormaRecepcionOrderAll() as $object) {
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => (strtoupper($object->getDescripcion())));
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getPrioridadComList()
{
	$list_object = array();
	foreach (PrioridadComPeer::getPrioridadComOrderAll() as $object) {
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => (strtoupper($object->getDescripcion())));
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getEmpresaMensajeriaList()
{
	$list_object = array();
	foreach (EmpresaMensajeriaPeer::getEmpresaMensajeriaOrderAll() as $object) {
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => (strtoupper($object->getNombre())));
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getDependenciasList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//*********************************************************************************************************************
	$list_object = array();
	foreach (DependenciaPeer::getDependenciaUserLoadList() as $object) {
		$nombre_dep = sprintf("%s - %s", trim($object->getCodigo()), (mb_strtoupper($object->getNombre())));
		if (!array_key_exists($object->getPrimaryKey(), $list_object))
			$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'codigoField' => trim($object->getCodigo()), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getFaseArchivoList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return array(responseErrorDataCatalogoList($usuario_ws['message']));
	}

	$localizaciones = LocalizacionUnidadDocumentalPeer::getLacalizacionAll();

	//var_dump($localizaciones); exit; 
	//*********************************************************************************************************************
	$list_object = array();
	foreach ($localizaciones as $object) {
		//$nombre_dep = sprintf("%s - %s",trim($object->getPrimaryKey()),(strtoupper($object->getDescripcion())));
		$nombre_dep = mb_strtoupper($object->getDescripcion());
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getSoporteDocumentalList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return array(responseErrorDataCatalogoList($usuario_ws['message']));
	}
	//*********************************************************************************************************************
	$list_object = array();
	foreach (SoporteUnidadDocumentalPeer::getSoporteAll() as $object) {
		//$nombre_dep = sprintf("%s - %s",trim($object->getPrimaryKey()),(strtoupper($object->getDescripcion())));
		$nombre_dep = mb_strtoupper($object->getDescripcion());
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getEstadoDocumentalList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return array(responseErrorDataCatalogoList($usuario_ws['message']));
	}
	//*********************************************************************************************************************
	$list_object = array();
	foreach (EstadoUnidadDocumentalPeer::getEstadoAll() as $object) {
		//$nombre_dep = sprintf("%s - %s",trim($object->getPrimaryKey()),(strtoupper($object->getDescripcion())));
		$nombre_dep = mb_strtoupper($object->getDescripcion());
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getFrecuenciaConsultaList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return array(responseErrorDataCatalogoList($usuario_ws['message']));
	}
	//*********************************************************************************************************************
	$list_object = array();
	foreach (FrecuenciaConsultaPeer::getFrecuenciaAll() as $object) {
		//$nombre_dep = sprintf("%s - %s",trim($object->getPrimaryKey()),(strtoupper($object->getDescripcion())));
		$nombre_dep = mb_strtoupper($object->getDescripcion());
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getMedioConservacionList($EntSecurity = array())
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return array(responseErrorDataCatalogoList($usuario_ws['message']));
	}

	$unidades_conservadoras = UnidadConservadoraPeer::getAllConservadoras();
	//var_dump($unidades_conservadoras); exit; 
	//*********************************************************************************************************************
	$list_object = array();
	foreach ($unidades_conservadoras as $object) {
		//$nombre_dep = sprintf("%s - %s",trim($object->getPrimaryKey()),(strtoupper($object->getDescripcion())));
		$nombre_dep = mb_strtoupper($object->getDescripcion());
		$list_object[] = array('idFielKey' => $object->getPrimaryKey(), 'nombreField' => $nombre_dep);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function UpdateProcessInfoEntrada($EntSecurity = array(), $consecutivocom_id)
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//*********************************************************************************************************************
	try {
		$com_recibida = ComRecibidaPeer::retrieveByPK($consecutivocom_id);
		if ($com_recibida->getTipoprocesocomId() == 1) {
			$com_recibida->setFechaDigit(($com_recibida->getFechaDigit() ? $com_recibida->getFechaDigit() : date("Y-m-d G:i:s")));
			$com_recibida->setTipoprocesocomId(2);
			$com_recibida->setIsLocked(0);
			$com_recibida->save();

			$response_default = array(
				'fecha_transaccion' => date("Y-m-d G:i:s"),
				'IsError' => false,
				'MsgError' => 'Actividad realizada con exito'
			);
		} else {
			$response_default = array(
				'fecha_transaccion' => date("Y-m-d G:i:s"),
				'IsError' => true,
				'MsgError' => 'Parametro no encontrado'
			);
		}
	} catch (Exception $ex) {
		$response_default = array(
			'fecha_transaccion' => date("Y-m-d G:i:s"),
			'IsError' => true,
			'MsgError' => $ex->getMessage()
		);
	}
	//*********************************************************************************************************************
	return $response_default;
}

function getUsuarioSgdeaById($EntSecurity = array(), $consecutivopk_id = null)
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//**********************************************************************************************
	$object_reg = array();
	//**********************************************************************************************
	$cargo_usuario = CargoUsuarioPeer::getCargoUsuarioByIdUser($consecutivopk_id, true);
	//**********************************************************************************************
	if ($cargo_usuario == null) {
		$list_object['cargousuario_id'] = 0;
		$list_object['usuario_id'] = 0;
		$list_object['user_name'] = null;
		$list_object['usuario_ad'] = null;
		$list_object['numero_identificacion'] = null;
		$list_object['nombre'] = null;
		$list_object['apellido'] = null;
		$list_object['dependencia'] = null;
		$list_object['dependencia_id'] = 0;
		$list_object['cargo'] = null;
		$list_object['regional'] = null;
		$list_object['regional_id'] = 0;
		$list_object['ciudad'] = null;
		$list_object['ciudad_id'] = 0;
		$list_object['IsError'] = true;
		$list_object['MsgError'] = "Error consultando la informacion del usuario";
		return $list_object;
	}
	//**********************************************************************************************
	$object_reg['cargousuario_id'] = $cargo_usuario->getPrimaryKey();
	$object_reg['usuario_id'] = $cargo_usuario->getUsuarioId();
	$object_reg['user_name'] = $cargo_usuario->getUsuario()->getUserName();
	$object_reg['usuario_ad'] = $cargo_usuario->getUsuario()->getUsuarioAd();
	$object_reg['numero_identificacion'] = $cargo_usuario->getUsuario()->getCedula();
	$object_reg['nombre'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getUsuario()->getNombre()), 'UTF-8');
	$object_reg['apellido'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getUsuario()->getApellido()), 'UTF-8');
	$object_reg['dependencia'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getUsuario()->getDependencia()->getNombre()), 'UTF-8');
	$object_reg['dependencia_id'] = $cargo_usuario->getUsuario()->getDependenciaId();
	$object_reg['cargo'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getCargo()->getDescripcion()), 'UTF-8');
	$object_reg['regional'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getUsuario()->getRegional()->getDescripcion()), 'UTF-8');
	$object_reg['regional_id'] = $cargo_usuario->getUsuario()->getRegionalId();
	$object_reg['ciudad'] = mb_convert_encoding(mb_strtoupper($cargo_usuario->getUsuario()->getRegional()->getCiudad()->getNombre()), 'UTF-8');
	$object_reg['ciudad_id'] = $cargo_usuario->getUsuario()->getRegional()->getCiudadId();
	$object_reg['IsError'] = false;
	$object_reg['MsgError'] = null;
	//**********************************************************************************************
	return $object_reg;
}

function getUsuariosList($EntSecurity = array(), $nombre = null, $apellido = null, $numero_identificacion = 0, $email = null, $dependencia_id = 0, $limit_select = 25)
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//**********************************************************************************************
	$list_object = array();
	$isFilter = false;
	//**********************************************************************************************
	$c = new Criteria();
	$c->setDistinct();
	$c->setLimit($limit_select);
	//**********************************************************************************************
	$c->addJoin(CargoUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID);
	$c->addJoin(CargoUsuarioPeer::CARGO_ID, CargoPeer::CARGO_ID);
	$c->addJoin(UsuarioPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
	$c->addJoin(UsuarioPeer::REGIONAL_ID, RegionalPeer::REGIONAL_ID);
	$c->addJoin(RegionalPeer::CIUDAD_ID, CiudadPeer::CIUDAD_ID);
	//**********************************************************************************************
	if (trim($dependencia_id)) {
		$c->add(UsuarioPeer::DEPENDENCIA_ID, $dependencia_id);
		$isFilter = true;
	}
	//**********************************************************************************************
	if (isset($nombre) && trim($nombre)) {
		$c->add(UsuarioPeer::NOMBRE, '%' . trim($nombre) . '%', Criteria::LIKE);
		$isFilter = true;
	}
	//**********************************************************************************************
	if (isset($apellido) && trim($apellido)) {
		$c->add(UsuarioPeer::APELLIDO, '%' . trim($apellido) . '%', Criteria::LIKE);
		$isFilter = true;
	}
	//**********************************************************************************************
	if (isset($numero_identificacion) && trim($numero_identificacion)) {
		$c->add(UsuarioPeer::CEDULA, trim($numero_identificacion));
		$isFilter = true;
	}
	//**********************************************************************************************
	if (isset($email) && trim($email)) {
		//$c->add(UsuarioPeer::EMAIL,trim($email));
		$isFilter = true;
	}
	//**********************************************************************************************
	if (!$isFilter) {
		$list_object['cargousuario_id'] = 0;
		$list_object['usuario_id'] = 0;
		$list_object['user_name'] = null;
		$list_object['usuario_ad'] = null;
		$list_object['numero_identificacion'] = null;
		$list_object['nombre'] =  null;
		$list_object['apellido'] = null;
		$list_object['dependencia'] = null;
		$list_object['dependencia_id'] = 0;
		$list_object['cargo'] = null;
		$list_object['regional'] = null;
		$list_object['regional_id'] = 0;
		$list_object['ciudad'] = null;
		$list_object['ciudad_id'] = null;
		$list_object['IsError'] = true;
		$list_object['MsgError'] = "Debe enviar al menos un parametro de consulta";
		return array($list_object);
	}
	//**********************************************************************************************
	$c->add(CargoUsuarioPeer::ES_ACTUAL, 1);
	$c->add(UsuarioPeer::ESTADOUSUARIO_ID, 2, Criteria::NOT_EQUAL);
	$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
	//**********************************************************************************************
	$c->clearSelectColumns();
	$c->addSelectColumn(CargoUsuarioPeer::USUARIO_ID); //0
	$c->addSelectColumn(CargoUsuarioPeer::CARGOUSUARIO_ID); //1
	$c->addSelectColumn(UsuarioPeer::NOMBRE); //2
	$c->addSelectColumn(UsuarioPeer::APELLIDO); //3
	$c->addSelectColumn(CargoPeer::DESCRIPCION); //4
	$c->addSelectColumn(RegionalPeer::DESCRIPCION); //5
	$c->addSelectColumn(DependenciaPeer::CODIGO); //6
	$c->addSelectColumn(DependenciaPeer::NOMBRE); //7
	$c->addSelectColumn(UsuarioPeer::EMAIL); //8
	$c->addSelectColumn(UsuarioPeer::USER_NAME); //9
	$c->addSelectColumn(UsuarioPeer::USUARIO_AD); //10
	$c->addSelectColumn(UsuarioPeer::CEDULA); //11
	$c->addSelectColumn(DependenciaPeer::DEPENDENCIA_ID); //12
	$c->addSelectColumn(RegionalPeer::REGIONAL_ID); //13
	$c->addSelectColumn(CiudadPeer::NOMBRE); //14
	$c->addSelectColumn(CiudadPeer::CIUDAD_ID); //15
	//**********************************************************************************************
	$resultset = CargoUsuarioPeer::doSelectStmt($c);
	//**********************************************************************************************
	$list_users = array();
	while ($object = $resultset->fetch()) {
		$list_object['cargousuario_id'] = $object[0];
		$list_object['usuario_id'] = $object[1];
		$list_object['user_name'] = $object[9];
		$list_object['usuario_ad'] = $object[10];
		$list_object['numero_identificacion'] = $object[11];
		$list_object['nombre'] =  mb_convert_encoding(mb_strtoupper($object[2]), 'UTF-8');
		$list_object['apellido'] = mb_convert_encoding(mb_strtoupper($object[3]), 'UTF-8');
		$list_object['dependencia'] = mb_convert_encoding(mb_strtoupper($object[7]), 'UTF-8');
		$list_object['dependencia_id'] = $object[12];
		$list_object['cargo'] = mb_convert_encoding(mb_strtoupper($object[4]), 'UTF-8');
		$list_object['regional'] = mb_convert_encoding(mb_strtoupper($object[5]), 'UTF-8');
		$list_object['regional_id'] = $object[13];
		$list_object['ciudad'] = mb_convert_encoding(mb_strtoupper($object[14]), 'UTF-8');
		$list_object['ciudad_id'] = $object[15];
		$list_object['IsError'] = false;
		$list_object['MsgError'] = null;

		$list_users[] = $list_object;
	}
	//**********************************************************************************************
	if (count($list_users) <= 0) {
		$list_object['cargousuario_id'] = 0;
		$list_object['usuario_id'] = 0;
		$list_object['user_name'] = null;
		$list_object['usuario_ad'] = null;
		$list_object['numero_identificacion'] = null;
		$list_object['nombre'] =  null;
		$list_object['apellido'] = null;
		$list_object['dependencia'] = null;
		$list_object['dependencia_id'] = 0;
		$list_object['cargo'] = null;
		$list_object['regional'] = null;
		$list_object['regional_id'] = 0;
		$list_object['ciudad'] = null;
		$list_object['ciudad_id'] = null;
		$list_object['IsError'] = false;
		$list_object['MsgError'] = "Ningun registro encontrado";
		return array($list_object);
	}
	//**********************************************************************************************
	return $list_users;
}

function getAreaDestinoList($EntSecurity = array(), $tipoproceso = 0)
{
	$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
	if ($usuario_ws['isError']) {
		return responseErrorData($usuario_ws['message']);
	}
	//**********************************************************************************************
	$list_object = array();
	//**********************************************************************************************
	$c = new Criteria();
	$c->setDistinct();
	//**********************************************************************************************
	$c->addJoin(CargoUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID);
	$c->addJoin(UsuarioPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
	$c->addJoin(CargoUsuarioPeer::CARGO_ID, CargoPeer::CARGO_ID);
	$c->addJoin(UsuarioPeer::USUARIO_ID, UsuarioProcesocomPeer::USUARIO_ID);
	//**********************************************************************************************
	$c->add(UsuarioProcesocomPeer::TIPOPROCESOCOM_ID, $tipoproceso);
	$c->add(CargoUsuarioPeer::ES_ACTUAL, 1);
	//**********************************************************************************************
	$c->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);
	//**********************************************************************************************
	$c->clearSelectColumns();
	$c->addSelectColumn(CargoUsuarioPeer::USUARIO_ID); //0
	$c->addSelectColumn(DependenciaPeer::CODIGO); //1
	$c->addSelectColumn(DependenciaPeer::NOMBRE); //2
	$c->addSelectColumn(CargoUsuarioPeer::CARGOUSUARIO_ID); //3
	//**********************************************************************************************
	try {
		$resultset = CargoUsuarioPeer::doSelectStmt($c);
		//**********************************************************************************************
		while ($object = $resultset->fetch()) {
			$list_object[] = array('idFielKey' => $object[0], 'idFielKeyCargo' => $object[3], 'codigoField' => trim($object[1]), 'nombreField' => sprintf("%s - %s", trim($object[1]), (strtoupper($object[2]))));
		}
	} catch (PropelException $ex) {
		return $ex->getMessage();
	} catch (Exception $ex) {
		return $ex->getMessage();
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getListInteresados($EntSecurity = array(), $nombre = "", $apellido = "", $numero_identificacion = "", $limit_select = 25)
{
	try {
		$infoxml = file_get_contents("php://input");
		//*********************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//*********************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		WebserviceLogPeer::addLogWs("getListInteresados", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//*********************************************************************************************************************
		$c = new Criteria();
		$c->setLimit($limit_select);
		//*********************************************************************************************************************
		if (isset($nombre) && !empty($nombre)) {
			$c->add(InteresadosPeer::PRIMER_NOMBRE, '%' . trim($nombre) . '%', Criteria::LIKE);
		}
		//*********************************************************************************************************************
		if (isset($apellido) && !empty($apellido)) {
			$c->add(InteresadosPeer::PRIMER_APELLIDO, '%' . trim($apellido) . '%', Criteria::LIKE);
		}
		//*********************************************************************************************************************
		if (isset($numero_identificacion) && !empty($numero_identificacion)) {
			$c->add(InteresadosPeer::NUMERO_IDENTIFICACION, '%' . trim($numero_identificacion) . '%', Criteria::LIKE);
		}
		//*********************************************************************************************************************
		$c->addAscendingOrderByColumn(InteresadosPeer::PRIMER_NOMBRE);
		$c->addAscendingOrderByColumn(InteresadosPeer::PRIMER_APELLIDO);
		//*********************************************************************************************************************
		$c->clearSelectColumns();
		$c->addJoin(InteresadosPeer::CIUDAD_ID, CiudadPeer::CIUDAD_ID);
		//*********************************************************************************************************************
		$c->addSelectColumn(InteresadosPeer::INTERESADO_ID); //0
		$c->addSelectColumn(InteresadosPeer::NUMERO_IDENTIFICACION); //1
		$c->addSelectColumn(InteresadosPeer::PRIMER_NOMBRE); //2
		$c->addSelectColumn(InteresadosPeer::SEGUNDO_NOMBRE); //3
		$c->addSelectColumn(InteresadosPeer::PRIMER_APELLIDO); //4
		$c->addSelectColumn(InteresadosPeer::SEGUNDO_APELLIDO); //5
		$c->addSelectColumn(InteresadosPeer::DIRECCION); //6
		$c->addSelectColumn(CiudadPeer::NOMBRE); //7
		$c->addSelectColumn(InteresadosPeer::EMAIL); //8
		$c->addSelectColumn(CiudadPeer::CODIGO_DANE); //9
		//*********************************************************************************************************************
		$resultset = InteresadosPeer::doSelectStmt($c);
		//*********************************************************************************************************************
		$list_object = array();
		while ($list_data = $resultset->fetch()) {
			$row['INTERESADO_ID'] = $list_data[0];
			$row['CIUDAD'] = (strtoupper($list_data[7]));
			$row['CIUDAD_CODIGO'] = trim($list_data[9]);
			$row['NUMERO_IDENTIFICACION'] = $list_data[1];
			$row['DIRECCION'] = (strtoupper($list_data[6]));
			$row['EMAIL'] = trim($list_data[8]);
			//*****************************************************************************************************************
			$row['PRIMER_NOMBRE'] = strtoupper($list_data[2]);
			$row['SEGUNDO_NOMBRE'] = trim($list_data[3]) ?  strtoupper($list_data[3]) : "";
			$row['PRIMER_APELLIDO'] = strtoupper($list_data[4]);
			$row['SEGUNDO_APELLIDO'] = trim($list_data[5]) ?  strtoupper($list_data[5]) : "";
			$list_object[] = $row;
		}
		//*********************************************************************************************************************
		return $list_object;
	} catch (PropelException $ex) {
		return $ex->getMessage();
	} catch (SoapFault $ex) {
		print $ex->faultstring;
	} catch (Exception $ex) {
		print $ex->getMessage();
	}
}

function getRemitentesList($EntSecurity = array(), $nombre = null, $funcionario = null, $nuid = null, $email = null, $limit_select = 25)
{
	$list_object = array();
	$isFilter = false;
	//*************************************************************************************************************************
	try {
		$infoxml = file_get_contents("php://input");
		//*********************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//*********************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		WebserviceLogPeer::addLogWs("getRemitentesList", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//*********************************************************************************************************************
		$c = new Criteria();
		$c->setDistinct();
		$c->setLimit($limit_select);
		//*********************************************************************************************************************
		if (isset($nombre) && !empty($nombre)) {
			$c->add(DirectorioExternoPeer::NOMBRE, '%' . trim($nombre) . '%', Criteria::LIKE);
			$isFilter = true;
		}
		//*********************************************************************************************************************
		if (isset($funcionario) && !empty($funcionario)) {
			$c->add(DirectorioExternoPeer::FUNCIONARIO, '%' . trim($funcionario) . '%', Criteria::LIKE);
			$isFilter = true;
		}
		//*********************************************************************************************************************
		if (isset($nuid) && !empty($nuid)) {
			$c->add(DirectorioExternoPeer::NIT, '%' . trim($nuid) . '%', Criteria::LIKE);
			$isFilter = true;
		}
		//*********************************************************************************************************************
		if (isset($email) && !empty($email)) {
			//$c->add(DirectorioExternoPeer::EMAIL,trim($email).'%',Criteria::LIKE);
			$isFilter = true;
		}
		//*********************************************************************************************************************
		if ($isFilter == false) {
			$row['directorioexterno_id'] = 0;
			$row['ciudad'] = null;
			$row['ciudad_codigo'] = null;
			$row['nuid'] = null;
			$row['direccion'] = null;
			$row['nombre'] = null;
			$row['funcionario'] = null;
			$row['email'] = null;
			$row['IsError'] = true;
			$row['MsgError'] = "Debe enviar al menos un filtro de consulta";

			return array($row);
		}
		//*********************************************************************************************************************
		$c->addAscendingOrderByColumn(DirectorioExternoPeer::NOMBRE);
		//*********************************************************************************************************************
		$c->clearSelectColumns();
		$c->addJoin(DirectorioExternoPeer::CIUDAD_ID, CiudadPeer::CIUDAD_ID);
		//*********************************************************************************************************************
		$c->addSelectColumn(DirectorioExternoPeer::DIRECTORIOEXTERNO_ID); //0
		$c->addSelectColumn(DirectorioExternoPeer::NOMBRE); //1
		$c->addSelectColumn(DirectorioExternoPeer::FUNCIONARIO); //2
		$c->addSelectColumn(DirectorioExternoPeer::NIT); //3
		$c->addSelectColumn(DirectorioExternoPeer::DIRECCION); //4		
		$c->addSelectColumn(CiudadPeer::NOMBRE); //5
		$c->addSelectColumn(DirectorioExternoPeer::EMAIL); //6
		$c->addSelectColumn(CiudadPeer::CODIGO_DANE); //7
		//*********************************************************************************************************************
		$resultset = DirectorioExternoPeer::doSelectStmt($c);
		//*********************************************************************************************************************
		$list_object = array();
		while ($list_data = $resultset->fetch()) {
			$row['directorioexterno_id'] = $list_data[0];
			$row['ciudad'] = mb_convert_encoding(mb_strtoupper($list_data[5]), 'UTF-8');
			$row['ciudad_codigo'] = mb_convert_encoding(mb_strtoupper($list_data[7]), 'UTF-8');
			$row['nuid'] = $list_data[3];
			$row['direccion'] = mb_convert_encoding(mb_strtoupper($list_data[4]), 'UTF-8');
			$row['nombre'] = mb_convert_encoding(mb_strtoupper($list_data[1]), 'UTF-8');
			$row['funcionario'] = mb_convert_encoding(mb_strtoupper($list_data[2]), 'UTF-8');
			$row['email'] = mb_strtoupper($list_data[6]);
			$row['IsError'] = false;
			$row['MsgError'] = null;
			$list_object[] = $row;
		}
		//*********************************************************************************************************************
		if (count($list_object) <= 0) {
			$row['directorioexterno_id'] = 0;
			$row['ciudad'] = null;
			$row['ciudad_codigo'] = null;
			$row['nuid'] = null;
			$row['direccion'] = null;
			$row['nombre'] = null;
			$row['funcionario'] = null;
			$row['email'] = null;
			$row['IsError'] = true;
			$row['MsgError'] = "Ningun registro encontrado";

			return array($row);
		}
	} catch (PropelException $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'ciudad_codigo' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Error de acceso a los datos'
		);
	} catch (SoapFault $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'ciudad_codigo' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Error SoapFault ' . $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'ciudad_codigo' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Ocurrio un error de aplicacion'
		);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function getRemitenteById($EntSecurity = array(), $consecutivopk_id = null)
{
	$list_object = array();
	//*************************************************************************************************************************
	try {
		$infoxml = file_get_contents("php://input");
		//*********************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//*********************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		WebserviceLogPeer::addLogWs("getRemitenteById", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//*********************************************************************************************************************
		$directorio_exteno = DirectorioExternoPeer::retrieveByPK($consecutivopk_id);
		//*********************************************************************************************************************
		$list_object['directorioexterno_id'] = $directorio_exteno->getPrimaryKey();
		$list_object['ciudad'] = mb_convert_encoding($directorio_exteno->getCiudad()->getNombre(), 'UTF-8');
		$list_object['ciudad_codigo'] = mb_convert_encoding($directorio_exteno->getCiudad()->getCodigoDane(), 'UTF-8');
		$list_object['nuid'] = $directorio_exteno->getNit();
		$list_object['direccion'] = mb_convert_encoding(mb_strtoupper($directorio_exteno->getDireccion()), 'UTF-8');
		$list_object['nombre'] = mb_convert_encoding(mb_strtoupper($directorio_exteno->getNombre()), 'UTF-8');
		$list_object['funcionario'] = mb_convert_encoding(mb_strtoupper($directorio_exteno->getFuncionario()), 'UTF-8');
		$list_object['email'] = mb_strtoupper($directorio_exteno->getEmail());
		$list_object['IsError'] = false;
		$list_object['MsgError'] = null;
	} catch (PropelException $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'ciudad_codigo' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Error de acceso a los datos'
		);
	} catch (SoapFault $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'ciudad_codigo' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Error SoapFault ' . $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$list_object[] = array(
			'directorioexterno_id' => 0,
			'ciudad' => null,
			'nuid' => null,
			'direccion' => null,
			'nombre' => null,
			'funcionario' => null,
			'email' => null,
			'IsError' => true,
			'MsgError' => 'Ocurrio un error de aplicacion'
		);
	}
	//*********************************************************************************************************************
	return $list_object;
}

function AddRadicadoEntrada($EntSecurity = array(), $EntComRecibida = array(), $EntInteresadoOfList = array(), $EntRemitente = array())
{
	//file_put_contents("c:/temp/outputfile.txt", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	$fecha_transaccion = date("Y-m-d G:i:s");
	//*******************************************************************************************************************
	try {
		//$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'request_timed.log';
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddRadicadoEntrada", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//***************************************************************************************************************
		if (count($EntInteresadoOfList) <= 0) {
			return responseErrorData('Debe enviar por lo menos un interesado o la información de los interesados no es valida');
		}
		//***************************************************************************************************************
		$empresa_mensajeria = -1;
		if (isset($EntComRecibida['EMPRESA_MENSAJERIA'])) {
			$courrier_pk = trim($EntComRecibida['EMPRESA_MENSAJERIA']) ? trim($EntComRecibida['EMPRESA_MENSAJERIA']) : 0;
			$empresa_mensajeria = !is_null($courrier_pk) ? EmpresaMensajeriaPeer::retrieveByPK($courrier_pk) : -1;
		}
		//***************************************************************************************************************
		$tipocom_pk = isset($EntComRecibida['CLASIFICACION_DOCUMENTO']) ? trim($EntComRecibida['CLASIFICACION_DOCUMENTO']) : 0;
		$regional_origen = isset($EntComRecibida['REGIONAL_ID']) ? RegionalPeer::retrieveByPK(trim($EntComRecibida['REGIONAL_ID'])) : null;
		$asunto = isset($EntComRecibida['ASUNTO']) ? trim($EntComRecibida['ASUNTO']) : null;
		$folios = isset($EntComRecibida['FOLIOS']) ? trim($EntComRecibida['FOLIOS']) : null;
		//***************************************************************************************************************
		$cuser_destino = CargoUsuarioPeer::retrieveByPK($EntComRecibida['UCARGO_DESTINO']);
		$cuser_origen = CargoUsuarioPeer::retrieveByPK($EntComRecibida['UCARGO_ORIGEN']);
		$tipoprocesocom_id = 2; //distribuidor
		$dependencia_destino = $cuser_destino->getUsuario()->getDependenciaId();
		//***************************************************************************************************************
		if ($regional_origen == null) {
			return responseErrorData('El punto de radicación es un campo obligatorio y no se encontro en el SGDEA');
		}
		if (empty($tipocom_pk)) {
			return responseErrorData('El tipo de documento o trámite es un campo obligatorio o no se encontro en el SGDEA');
		}
		if ($dependencia_destino == null) {
			return responseErrorData('La dependencia destino es un campo obligatorio o no se encontro en el SGDEA');
		}
		if ($asunto == null) {
			return responseErrorData('El campo asunto es obligatorio');
		}
		if ($folios == null) {
			return responseErrorData('El campo folios es obligatorio');
		}
		if ($empresa_mensajeria == 0) {
			return responseErrorData('La empresa de mensajeria no esta creada en el SGDEA');
		}
		//***************************************************************************************************************
		$params = array();
		$params['regional_id'] = $regional_origen->getPrimaryKey();
		$params['tipocomrecibida_id'] = $tipocom_pk;
		$params['dependencia_id'] = $dependencia_destino;
		$params['formarecepcion_id'] = $EntComRecibida['FORMA_RECEPCION'];
		$params['prioridadcom_id'] = $EntComRecibida['PRIORIDAD_COM'];
		$params['ciudad_id'] = $regional_origen->getCiudadId();
		$params['directorioexterno_id'] = isset($EntRemitente['REMITENTE_ID']) ? trim($EntRemitente['REMITENTE_ID']) : null;
		$params['radicado_origen'] = isset($EntComRecibida['RADICADO_ORIGEN']) ? trim($EntComRecibida['RADICADO_ORIGEN']) : null;
		$params['asunto'] = $asunto;
		$params['folios'] = $folios;
		$params['observaciones'] = isset($EntComRecibida['OBSERVACIONES']) ? trim($EntComRecibida['OBSERVACIONES']) : null;
		$params['anexos'] = isset($EntComRecibida['ANEXOS']) ? trim($EntComRecibida['ANEXOS']) : null;
		$params['numero_fud'] = isset($EntComRecibida['NUMERO_FUD']) ? trim($EntComRecibida['NUMERO_FUD']) : null;
		$params['empresamensajeria_id'] = $empresa_mensajeria > 0 ? $empresa_mensajeria->getPrimaryKey() : null;
		$params['numero_guia'] = isset($EntComRecibida['NUMERO_GUIA']) ? trim($EntComRecibida['NUMERO_GUIA']) : null;
		$params['numero_proceso'] = isset($EntComRecibida['NUMERO_PROCESO']) ? trim($EntComRecibida['NUMERO_PROCESO']) : null;
		$params['fecha_vencimiento'] = isset($EntComRecibida['FECHA_VENCIMIENTO']) ? trim($EntComRecibida['FECHA_VENCIMIENTO']) : null;
		$params['fecha_recibido'] = isset($EntComRecibida['FECHA_RECIBIDO']) ? trim($EntComRecibida['FECHA_RECIBIDO']) : null;
		$params['entidad_id'] = $regional_origen->getEntidadId();
		$params['tipoprocesocom_id'] = $tipoprocesocom_id;
		$params['is_locked'] = 0;
		$params['file'] = null;
		$params['filefullpath'] = true;
		$params['digitOverWrite'] = true;
		//***************************************************************************************************************
		$params['usuario_radicador'] = $cuser_origen->getUsuarioId();
		$params['cusuario_radicador'] = $cuser_origen->getPrimaryKey();
		$params['usuario_destino'] = $cuser_destino->getUsuarioId();
		$params['cusuario_destino'] = $cuser_destino->getPrimaryKey();
		//***************************************************************************************************************
		$com_recibida = ComRecibidaPeer::addComRecibida($params);
		//***************************************************************************************************************
		if (empty($com_recibida)) {
			return responseErrorData('No se puedo radicar, ocurrio un error interno en el servidor.');
		}
		//***************************************************************************************************************
		foreach ($EntInteresadoOfList as $info_list) {
			ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(), $info_list['INTERESADO_ID']);
		}
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $com_recibida->getPrimaryKey(),
			'RADICADO' => $com_recibida->getRadicado(),
			'FECHA_CREACION' => $com_recibida->getFechaCreacion(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' => false,
			'MSGERROR' => ''
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' => true,
			'MSGERROR' => 'Ocurrio un error interno en el servidor al generar el numero de radicacion, intente de nuevo o comuniquese con el administrador del sistema'
		);
	} catch (Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' => true,
			'MSGERROR' => 'Ocurrio un error interno en el servidor, intente de nuevo o comuniquese con el administrador del sistema'
		);
	}
	//*********************************************************************************************************************
	return $response_data;
}

function AddRadicadoEntradaPublic($EntSecurity = array(), $EntComRecibida = array(), $ListInteresadoEnt = array(), $EntRemitente = array())
{
	//file_put_contents("c:/temp/outputfile.txt", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	//*******************************************************************************************************************
	try {
		$logname = sfConfig::get("sf_log_dir") . DIRECTORY_SEPARATOR . 'wsentrada_public.log';
		//$filedir_target = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(29)->getValortexto() . 'uploads');
		$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddRadicadoEntradaPublic", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getPrimaryKey());
		//***************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		foreach ($ListInteresadoEnt as $info_list) {
			if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
				if (!trim($info_list['NUMERO_IDENTIFICACION'])) {
					return responseErrorData('El numero de identificación del interesado no es valido');
				}
				//*******************************************************************************************************
				$interesado_id = InteresadosPeer::existsIntByNameAndNuid(trim($info_list['PRIMER_NOMBRE']), trim($info_list['PRIMER_APELLIDO']), trim($info_list['NUMERO_IDENTIFICACION']));
				if ($interesado_id != null) {
					$comlist_interesados[] = $interesado_id;
				} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
					//$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
					$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkById(trim($info_list['TIPO_IDENTIFICACION']));
					if ($tipouid_id == null) {
						return responseErrorData('El tipo de identificación del interesado no es un valor valido');
					}
					//***************************************************************************************************
					$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				} else {
					$info_list['TIPO_IDENTIFICACION'] = 5;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				}
			} else {
				return responseErrorData('El nombre o numero de identificación del interesado no es valido');
				exit;
			}
		}
		//***************************************************************************************************************
		if (count($comlist_interesados) <= 0 && count($laddnew_interesados) <= 0) {
			return responseErrorData('Debe enviar por lo menos un interesado o la información de los interesados no es valida');
		}
		//***************************************************************************************************************
		$regional_text = isset($EntComRecibida['REGIONAL_RADICACION']) ? utf8_encode(trim($EntComRecibida['REGIONAL_RADICACION'])) : null;
		$regional_origen = trim($regional_text) ? RegionalPeer::getRegionalByName($regional_text) : null;
		//***************************************************************************************************************
		$tipocom_text = isset($EntComRecibida['TIPO_DOCUMENTO']) ? (trim($EntComRecibida['TIPO_DOCUMENTO'])) : null;
		$tipo_com_recibida = TipoComRecibidaPeer::getTipoComRecibidaByText($tipocom_text);
		//***************************************************************************************************************
		$dependencia_codigo = isset($EntComRecibida['DEPENDENCIA_DESTINO']) ? trim($EntComRecibida['DEPENDENCIA_DESTINO']) : null;
		$dependencia_destino = trim($dependencia_codigo) ? DependenciaPeer::getDependenciaByCodigo($dependencia_codigo) : null;
		//***************************************************************************************************************
		$empresa_mensajeria = -1;
		if (isset($EntComRecibida['EMPRESA_MENSAJERIA'])) {
			$courrier_text = trim($EntComRecibida['EMPRESA_MENSAJERIA']) ? utf8_encode(trim($EntComRecibida['EMPRESA_MENSAJERIA'])) : null;
			$empresa_mensajeria = !is_null($courrier_text) ? EmpresaMensajeriaPeer::getEmpresaMensajeriaByName($courrier_text) : -1;
		}
		//***************************************************************************************************************
		$asunto = isset($EntComRecibida['ASUNTO']) ? utf8_encode(trim($EntComRecibida['ASUNTO'])) : null;
		$folios = isset($EntComRecibida['FOLIOS']) ? trim($EntComRecibida['FOLIOS']) : null;
		$adju_b64 = isset($EntComRecibida['ARCHIVO_DIGIT']) ? trim($EntComRecibida['ARCHIVO_DIGIT']) : null;
		$filename_source = isset($EntComRecibida['ARCHIVO_NOMBRE']) ? utf8_encode(trim($EntComRecibida['ARCHIVO_NOMBRE'])) : null;
		//***************************************************************************************************************
		$prioridad_com = isset($EntComRecibida['PRIORIDAD_COM']) ? (trim($EntComRecibida['PRIORIDAD_COM'])) : 1;
		$strforma_recepcion = isset($EntComRecibida['FORMA_RECEPCION']) ? utf8_encode(trim($EntComRecibida['FORMA_RECEPCION'])) : null;
		$forma_recepcion = $strforma_recepcion != null ? FormaRecepcionPeer::getFormaRecepcionByText($strforma_recepcion, true) : null;
		//***************************************************************************************************************
		if ($forma_recepcion == null) {
			return responseErrorData('La forma de recepción(' . $strforma_recepcion . ') es un campo obligatorio o no se encontro en el SGDEA');
		}
		if ($regional_origen == null) {
			return responseErrorData('El punto de radicación es un campo obligatorio y no se encontro en el SGDEA');
		}
		if ($tipo_com_recibida == null) {
			return responseErrorData('El tipo de documento o trámite es un campo obligatorio o no se encontro en el SGDEA');
		}
		if ($dependencia_destino == null) {
			return responseErrorData('La dependencia destino es un campo obligatorio o no se encontro en el SGDEA');
		}
		if ($asunto == null) {
			return responseErrorData('El campo asunto es obligatorio');
		}
		if ($folios == null) {
			return responseErrorData('El campo folios es obligatorio');
		}
		if ($filename_source == null) {
			return responseErrorData('El nombre del archivo es un campo obligatorio');
		}
		if ($adju_b64 == null) {
			return responseErrorData('Debe enviar el documento digital del radicado de entrada');
		}
		if ($empresa_mensajeria == 0) {
			return responseErrorData('La empresa de mensajeria no esta creada en el SGDEA');
		}
		if (!is_numeric($prioridad_com)) {
			return responseErrorData('La prioridad enviada no es valida, debe ser un numero');
		}
		//if(!is_int($prioridad_com)){ return responseErrorData('La prioridad enviada no es valida, debe un valor entero'.$prioridad_com); }
		//***************************************************************************************************************
		$mimetypes = explode(";", ParametroPeer::retrieveByPK(31)->getValortexto());
		$fullpath = $filedir_target . md5(rand() . date("YmdGis")) . "_" . $filename_source;
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorData('Error interno del servidor al crear el archivo');
		}
		//***************************************************************************************************************
		$mime_trust = array('application/pdf', 'application/x-pdf', 'application/x-bzpdf', 'application/x-gzpdf', 'application/zip', 'application/vnd.ms-outlook');
		$file_valid = simad_util::CheckIsValidFormatFile($fullpath, $mime_trust);
		if (!$file_valid) {
			return responseErrorData('El formato del archivo no esta permitido');
		}
		//***************************************************************************************************************			
		$info_file = new SplFileInfo($fullpath);
		$extension  = strtolower($info_file->getExtension());
		if (!in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorData('El formato ' . $extension . ' del archivo no es valido');
		}
		//***************************************************************************************************************
		$tipoprocesocom_id = 2; //distribuidor
		if (!$tipo_com_recibida->getTipodistribucionId()) {
			$cusuario_destino = UsuarioPeer::getUserDestinoPocesoComByDep($dependencia_destino->getDependenciaId(), $tipoprocesocom_id, true);
		} else {
			$cusuario_destino = ComRecibidaPeer::getConfigReceptorComByDep($dependencia_destino->getDependenciaId(), $tipo_com_recibida->getPrimaryKey(), $tipoprocesocom_id);
			if (is_array($cusuario_destino)) {
				$cusuario_destino = $cusuario_destino[0];
			}
			if ($cusuario_destino == null) {
				$cusuario_destino = UsuarioPeer::getUserDestinoPocesoComByDep($dependencia_destino->getDependenciaId(), $tipoprocesocom_id, true);
			}
		}
		//***************************************************************************************************************
		if ($cusuario_destino == null) {
			return responseErrorData('El dependencia no tiene parametrizado el usuario distribuidor');
		}
		//***************************************************************************************************************
		$cusuario_origen = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_creador->getPrimaryKey(), true);
		$EntRemitente['USUARIO_RADICADOR'] = $cusuario_origen->getUsuarioId();
		//***************************************************************************************************************			
		$params = array();
		$params['regional_id'] = $regional_origen->getPrimaryKey();
		$params['tipocomrecibida_id'] = $tipo_com_recibida->getPrimaryKey();
		$params['dependencia_id'] = $dependencia_destino->getPrimaryKey();
		$params['formarecepcion_id'] = $forma_recepcion->getPrimaryKey();
		$params['prioridadcom_id'] = $prioridad_com;
		$params['ciudad_id'] = $regional_origen->getCiudadId();
		$params['directorioexterno_id'] = DirectorioExternoPeer::addNewDirectorioExterno($EntRemitente);
		$params['radicado_origen'] = isset($EntComRecibida['RADICADO_ORIGEN']) ? trim($EntComRecibida['RADICADO_ORIGEN']) : null;
		$params['fecha_vencimiento'] = isset($EntComRecibida['FECHA_VENCIMIENTO']) ? trim($EntComRecibida['FECHA_VENCIMIENTO']) : null;
		$params['fecha_recibido'] = isset($EntComRecibida['FECHA_RECIBIDO']) ? trim($EntComRecibida['FECHA_RECIBIDO']) : null;
		$params['asunto'] = $asunto;
		$params['folios'] = $folios;
		$params['observaciones'] = isset($EntComRecibida['OBSERVACIONES']) ? utf8_encode(trim($EntComRecibida['OBSERVACIONES'])) : null;
		$params['anexos'] = isset($EntComRecibida['ANEXOS']) ? utf8_encode(trim($EntComRecibida['ANEXOS'])) : null;
		$params['numero_fud'] = isset($EntComRecibida['NUMERO_FUD']) ? trim($EntComRecibida['NUMERO_FUD']) : null;
		$params['empresamensajeria_id'] = $empresa_mensajeria > 0 ? $empresa_mensajeria->getPrimaryKey() : null;
		$params['numero_guia'] = isset($EntComRecibida['NUMERO_GUIA']) ? trim($EntComRecibida['NUMERO_GUIA']) : null;
		$params['numero_proceso'] = isset($EntComRecibida['NUMERO_PROCESO']) ? trim($EntComRecibida['NUMERO_PROCESO']) : null;
		$params['entidad_id'] = $regional_origen->getEntidadId();
		$params['tipoprocesocom_id'] = $tipoprocesocom_id;
		$params['is_locked'] = 0;
		$params['file'] = $fullpath;
		$params['filefullpath'] = true;
		$params['digitOverWrite'] = true;
		//***************************************************************************************************************
		$params['usuario_radicador'] = $cusuario_origen->getUsuarioId();
		$params['cusuario_radicador'] = $cusuario_origen->getPrimaryKey();
		$params['usuario_destino'] = $cusuario_destino->getUsuarioId();
		$params['cusuario_destino'] = $cusuario_destino->getPrimaryKey();
		//***************************************************************************************************************
		foreach ($laddnew_interesados as $newitem) {
			$newitem['USUARIO_ID'] = $cusuario_origen->getUsuarioId();
			$interesado_id = InteresadosPeer::addNewInteresado($newitem);
			if ($interesado_id == null) {
				return responseErrorData('Ocurrio un error al crear el interesado en el SGDEA');
			} else {
				$comlist_interesados[] = $interesado_id;
			}
		}
		//***************************************************************************************************************
		$com_recibida = ComRecibidaPeer::addComRecibida($params);
		//***************************************************************************************************************
		if (empty($com_recibida)) {
			return responseErrorData('No se puedo radicar, ocurrio un error interno en el servidor.');
		}
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $com_recibida->getRadicado());
			$wslog->setMensaje("Ejecuta Exitoso Genera Radicado");
			$wslog->save();
		}
		//***************************************************************************************************************
		//ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),); OJO ENVIAR EMAIL
		//***************************************************************************************************************
		foreach ($comlist_interesados as $item) {
			ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(), $item);
		}
		//***************************************************************************************************************
		$com_recibida->addExpedienteAutoByReglas($cusuario_origen->getUsuarioId());
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $com_recibida->getPrimaryKey(),
			'RADICADO' => $com_recibida->getRadicado(),
			'FECHA_RADICACION' => $com_recibida->getFechaCreacion(),
			'FECHA_VENCIMIENTO' => $com_recibida->getFechaMaximaRespuesta(),
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' => false,
			'MSGERROR' => ''
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => 0,
			'RADICADO' => 0,
			'FECHA_RADICACION' => null,
			'FECHA_VENCIMIENTO' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' => true,
			'MSGERROR' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => 0,
			'RADICADO' => 0,
			'FECHA_RADICACION' => null,
			'FECHA_VENCIMIENTO' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' => true,
			'MSGERROR' => $ex->getMessage()
		);
	} catch (\Throwable $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => 0,
			'RADICADO' => 0,
			'FECHA_RADICACION' => null,
			'FECHA_VENCIMIENTO' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' => true,
			'MSGERROR' => $ex->getMessage()
		);
	}
	//*******************************************************************************************************************
	return $response_data;
}

function responseErrorDataExp($msg, $IsError = true)
{
	$response = array(
		'CONSECUTIVO_ID' => null,
		'NUMERO_EXPEDIENTE' => null,
		'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
		'ISERROR' =>  $IsError,
		'MSG_INFO' => $msg
	);

	return $response;
}

function responseErrorData($msg, $IsError = true)
{
	$response = array(
		'CONSECUTIVO_ID' => null,
		'RADICADO' => null,
		'FECHA_CREACION' => null,
		'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
		'ISERROR' =>  $IsError,
		'MSGERROR' => $msg
	);
	return $response;
}

function responseErrorDataCatalogoList($msg, $IsError = true)
{
	$response = array(
		'idFielKey' => null,
		'nombreField' => null,
		'Fecha_Transaccion' => date("Y-m-d G:i:s"),
		'IsError' =>  $IsError,
		'MsgError' => $msg
	);
	return $response;
}

function responseErrorAttachment($msg, $IsError = true)
{
	return array(
		'CONSECUTIVO_ID' => null,
		'RADICADO' => null,
		'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
		'ISERROR' =>  $IsError,
		'MSG_INFO' => $msg
	);
}

function AddRadicadoSalidaEnt($EntSecurity = array(), $EntComEnviada = array(), $EntInteresadoOfList = array(), $EntRemitente = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//*********************************************************************************************************************
	//file_put_contents("c:/temp/outputfile.txt", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	file_put_contents(sfConfig::get("sf_log_dir") . DIRECTORY_SEPARATOR . date("Ymd") . "_AddRadicadoSalidaOferta.log", $infoxml);
	$util_simad = new simad_util();
	//*******************************************************************************************************************
	try {
		//$filedir_target = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(29)->getValortexto() . 'uploads');
		$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
		//$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'request_timed.log';
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddRadicadoSalidaEnt", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//***************************************************************************************************************
		$params = array();
		$periodo_id = isset($EntComEnviada['PERIODO_ID']) ? trim($EntComEnviada['PERIODO_ID']) : date("Y");
		$adju_b64 = isset($EntComEnviada['ARCHIVO_DIGIT']) ? trim($EntComEnviada['ARCHIVO_DIGIT']) : null;
		$filename_source = isset($EntComEnviada['ARCHIVO_NOMBRE']) ? trim($EntComEnviada['ARCHIVO_NOMBRE']) : null;
		$tramite_origen = isset($EntComEnviada['CLASIFICACION_DOCUMENTO']) ? trim($EntComEnviada['CLASIFICACION_DOCUMENTO']) : null;
		$uiduser_origen = isset($EntComEnviada['NUID_RADICA']) ? trim($EntComEnviada['NUID_RADICA']) : trim($EntComEnviada['NUID_GESTOR']);
		$tipo_envio = isset($EntComEnviada['TIPO_ENVIO']) ? trim($EntComEnviada['TIPO_ENVIO']) : null;
		//***************************************************************************************************************
		$object_list = $EntComEnviada['FIRMAS'];
		$cuser_firma = array();
		foreach ($object_list as $ufirma_list) {
			if (is_array($ufirma_list)) {
				if (is_array($ufirma_list['NUID_FIRMA'])) {
					foreach ($ufirma_list['NUID_FIRMA'] as $xufirma) {
						$var_ccfirma = !empty($xufirma) ? trim($xufirma) : -1;
						if (empty($var_ccfirma)) {
							continue;
						}
						$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser($var_ccfirma, true);
						//***************************************************************************************************
						if ($usuario_firma == null) {
							return responseErrorData('El usuario con cedula ' . $var_ccfirma . ', que firma el oficio no se encontro en el SGDEA');
						} else {
							$cuser_firma[] = $usuario_firma;
							$nuid_firma = $var_ccfirma;
						}
					}
				} else {
					$var_ccfirma = !empty($ufirma_list['NUID_FIRMA']) ? trim($ufirma_list['NUID_FIRMA']) : -1;
					if (empty($var_ccfirma)) {
						continue;
					}
					$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser($var_ccfirma, true);
					//***************************************************************************************************
					if ($usuario_firma == null) {
						return responseErrorData('El usuario con cedula ' . $var_ccfirma . ', que firma el oficio no se encontro en el SGDEA');
					} else {
						$cuser_firma[] = $usuario_firma;
						$nuid_firma = $var_ccfirma;
					}
				}
			} elseif (trim($ufirma_list)) {
				$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser(trim($ufirma_list), true);
				$nuid_firma = trim($ufirma_list);
			} else {
				$usuario_firma = null;
				$nuid_firma = "(unidefined)";
			}
			//***********************************************************************************************************
			if ($usuario_firma == null) {
				return responseErrorData('El usuario con cedula ' . $nuid_firma . ', que firma el oficio no se encontro en el SGDEA');
			} else {
				$cuser_firma[] = $usuario_firma;
			}
		}
		$cuser_firma = array_unique($cuser_firma);
		//***************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		$interesados_nuids = array();
		foreach ($EntInteresadoOfList as $info_list) {
			if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
				if (!trim($info_list['NUMERO_IDENTIFICACION'])) {
					return responseErrorData('El numero de identificación del interesado no es valido');
					exit;
				}
				//********************************************************************************************************
				$interesados_nuids[] = trim($info_list['NUMERO_IDENTIFICACION']);
				//********************************************************************************************************
				$interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'], $info_list['PRIMER_APELLIDO'], $info_list['NUMERO_IDENTIFICACION']);
				if ($interesado_id != null) {
					$comlist_interesados[] = $interesado_id;
				} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
					$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
					if ($tipouid_id == null) {
						return responseErrorData('El tipo de identificación del interesado no es un valor valido');
					}
					//****************************************************************************************************
					$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				} else {
					$info_list['TIPO_IDENTIFICACION'] = 5;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
					//return responseErrorData('El tipo de identificación del interesado es obligatorio');
					//exit;
				}
			} else {
				return responseErrorData('El nombre o numero de identificación del interesado no es valido');
				exit;
			}
		}
		//***************************************************************************************************************
		if (count($comlist_interesados) <= 0 && count($laddnew_interesados) <= 0) {
			return responseErrorData('Debe enviar como minimo un interesado');
		}
		//***************************************************************************************************************
		$cuser_gestor = CargoUsuarioPeer::getCargoUsuarioByNuidUser(trim($EntComEnviada['NUID_GESTOR']), true);
		//***************************************************************************************************************			
		$cuser_origen = $usuario_creador != null ? $usuario_creador : CargoUsuarioPeer::getCargoUsuarioByNuidUser($uiduser_origen, true);
		$regional_origen = isset($EntComEnviada['PUNTO_RADICACION']) ? RegionalPeer::getRegionalByName(trim($EntComEnviada['PUNTO_RADICACION'])) : null;
		//***************************************************************************************************************
		if (empty($cuser_gestor) || is_null($cuser_gestor)) {
			$cuser_gestor = $cuser_firma[0];
		}
		//***************************************************************************************************************
		$ciudad = isset($EntComEnviada['CODIGO_MUNICIPIO']) ? CiudadPeer::getCiudadByNombAndCod(null, trim($EntComEnviada['CODIGO_MUNICIPIO'])) : null;
		$asunto = isset($EntComEnviada['ASUNTO']) ? trim($EntComEnviada['ASUNTO']) : null;
		$estado_enviada = 1; //borrador estado inicial
		$radicado_entrada = isset($EntComEnviada['RADICADO_ENTRADA']) ? trim($EntComEnviada['RADICADO_ENTRADA']) : null;
		$entrada_externa = isset($EntComEnviada['ENTRADA_EXTERNA']) ? trim($EntComEnviada['ENTRADA_EXTERNA']) : null;
		$observaciones = isset($EntComEnviada['OBSERVACIONES_ENVIO']) ? (trim($EntComEnviada['OBSERVACIONES_ENVIO'])) : null;
		//***************************************************************************************************************
		if (count($cuser_firma) <= 0) {
			return responseErrorData('El usuario que firma el oficio es obligatorio o no se encontro en el SGDEA');
		}
		//***************************************************************************************************************
		$dependencia_codigo = isset($EntComEnviada['CODIGO_DEPENDENCIA']) ? trim($EntComEnviada['CODIGO_DEPENDENCIA']) : null;
		$dependencia_destinopk = $cuser_firma[0]->getUsuario()->getDependenciaId();
		if (!empty($dependencia_codigo)) {
			$dependencia_com = !empty(trim($dependencia_codigo)) ? DependenciaPeer::getDependenciaByCodigo($dependencia_codigo) : null;
			if ($dependencia_com != null) {
				$dependencia_destinopk = $dependencia_com->getPrimaryKey();
			}
		}
		//***************************************************************************************************************
		if (empty($cuser_gestor) || is_null($cuser_gestor)) {
			$msg_text = 'El usuario gestor del oficio esta inactivo o no se encuentra registrado en el SGDEA';
			$user_info = CargoUsuarioPeer::getCargoUsuarioByNuidUserEx(trim($EntComEnviada['NUID_GESTOR']), true);
			if ($user_info['error'] == 200) {
				$msg_text = 'Ocurrio un error con el usuario gestor del oficio en el SGDEA';
			} elseif ($user_info['error'] == 400) {
				$msg_text = sprintf('Ocurrio un error interno en el SGDEA(%s => %s)', $user_info['message'], $user_info['info']);
			} elseif ($user_info['error'] == 401) {
				$cusuario_gestor = $user_info['object'];
				$msg_text = sprintf('El usuario gestor(%s) del oficio esta (%s) en el SGDEA', $cusuario_gestor->getUsuario()->getFullNombre(), $user_info['info']);
			}
			//***********************************************************************************************************
			return responseErrorData($msg_text);
		}
		//***************************************************************************************************************
		if (empty($radicado_entrada) && empty($entrada_externa)) {
			return responseErrorData('El radicado de entrada es obligatorio');
		}
		//if($regional_origen == null){ responseErrorData('El punto de radicacion es obligatorio o no se encontro en el SGDEA'); }
		if ($ciudad == null) {
			return responseErrorData('El municipio es obligatorio o no se encontro en el SGDEA');
		}
		if ($adju_b64 == null) {
			return responseErrorData('Debe enviar el documento digital del oficio');
		}
		if ($asunto == null) {
			return responseErrorData('El asunto es obligatorio');
		}
		if ($tipo_envio == null) {
			return responseErrorData('El tipo de envio es un campo obligatorio');
		}
		if ($filename_source == null) {
			return responseErrorData('El nombre del archivo es un campo obligatorio');
		}
		//***************************************************************************************************************
		$IsEntradaExterna = false;
		if (!empty($radicado_entrada)) {
			$com_recibida = ComRecibidaPeer::getComObjectByRadicado($radicado_entrada);
			if ($com_recibida == null) {
				return responseErrorData('El radicado de entrada no existe en el SGDEA');
			}
		} elseif (!empty($entrada_externa)) {
			$IsEntradaExterna = true;
			$observaciones = $observaciones ? sprintf("%s, Radicado Origen => %s", $observaciones, $entrada_externa) :  sprintf("Radicado Origen => %s", $entrada_externa);
		} else {
			return responseErrorData('El radicado de entrada no existe en el SGDEA');
		}
		//***************************************************************************************************************
		$file_vars = pathinfo($filedir_target . $filename_source);
		$filename_source = $util_simad->clean_name_file($file_vars);
		$fullpath = $filedir_target . uniqid() . "_" . $filename_source;
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorData('Error interno del servidor al crear el archivo');
		}
		//***************************************************************************************************************
		$mime_trust = array('application/pdf', 'application/x-pdf', 'application/x-bzpdf', 'application/x-gzpdf');
		$file_valid = simad_util::CheckIsValidFormatFile($fullpath, $mime_trust);
		if (!$file_valid) {
			return responseErrorData('El formato del archivo no esta permitido');
		}
		//***************************************************************************************************************
		$info_file = new SplFileInfo($fullpath);
		//$mimetypes = array("pdf","doc", "docx");
		$mimetypes = array("pdf");
		$extension  = strtolower($info_file->getExtension());
		if (!in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorData('El formato ' . $extension . ' del archivo no es valido');
		}
		//***************************************************************************************************************
		if ($regional_origen == null) {
			$regional_origen = RegionalPeer::retrieveByPk($cuser_firma[0]->getUsuario()->getRegionalId());
		}
		//***************************************************************************************************************
		$addRemitente = false;
		$directorio_exteno = null;
		if (count($EntRemitente)) {
			$nuid_remitente = isset($EntComEnviada['NUID']) ? trim($EntComEnviada['NUID']) : null;
			if ($nuid_remitente != null) {
				$directorio_exteno = DirectorioExternoPeer::existsDirectorioExterno($EntRemitente);
			} else {
				$addRemitente = false;
			}
		}
		//***************************************************************************************************************
		$directorioexteno_id = !empty($directorio_exteno) ? $directorio_exteno->getPrimaryKey() : null;
		$folios = isset($EntComEnviada['FOLIOS']) ? trim($EntComEnviada['FOLIOS']) : 1;
		//***************************************************************************************************************
		$params['regional_id'] = $regional_origen->getPrimaryKey();
		$params['dependencia_id'] = $dependencia_destinopk;
		$params['estadocomenviada_id'] = $estado_enviada;
		$params['ciudad_id'] = $cuser_firma[0]->getUsuario()->getRegional()->getCiudadId();
		$params['periodo_id'] = date("Y");
		$params['asunto'] = isset($EntComEnviada['ASUNTO']) ? (trim($EntComEnviada['ASUNTO'])) : null;
		$params['folios'] = is_int($folios) ? $folios : 1;
		$params['observaciones_envio'] = $observaciones;
		$params['consecutivo_resp'] = $com_recibida != null ? $com_recibida->getPrimaryKey() : null;
		$params['firma_electronica'] = isset($EntComEnviada['FIRMA_DIGITAL']) ? trim($EntComEnviada['FIRMA_DIGITAL']) : 0;
		$params['archivo_digit'] = $adju_b64;
		$params['use_membrete'] = isset($EntComEnviada['USE_MEMBRETE']) ? trim($EntComEnviada['USE_MEMBRETE']) : null;
		$params['tipofirmadigital_id'] = isset($EntComEnviada['TIPO_FIRMA']) ? trim($EntComEnviada['TIPO_FIRMA']) : null;
		$params['tipo_envio'] = $tipo_envio;
		$params['tipo_masivo'] = isset($EntComEnviada['TIPO_MASIVO']) ? trim($EntComEnviada['TIPO_MASIVO']) : null;
		$params['tipo_integracion'] = "DEMANDA";
		$params['suborigen'] = isset($EntComEnviada['SUBORIGEN']) ? trim($EntComEnviada['SUBORIGEN']) : null;
		$params['UrlFileWord'] = $fullpath;
		$params['IsCreateWord'] = true;
		$params['numero_resolucion'] = isset($EntComEnviada['NUMERO_RESOLUCION']) ? trim($EntComEnviada['NUMERO_RESOLUCION']) : null;
		//***************************************************************************************************************
		if (isset($EntComEnviada['FECHA_RESOLUCION'])) {
			try {
				$date = new DateTime($EntComEnviada['FECHA_RESOLUCION']);
				$params['fecha_resolucion'] = $date->format('Y-m-d');
			} catch (Exception $ex) {
				$params['fecha_resolucion'] = null;
				return responseErrorData('El formato de la fecha resolucion no es valido ' . $ex->getMessage());
			}
		}
		//***************************************************************************************************************
		foreach ($laddnew_interesados as $newitem) {
			$newitem['USUARIO_ID'] = $cuser_origen->getUsuarioId();
			$interesado_id = InteresadosPeer::addNewInteresado($newitem);
			if ($interesado_id == null) {
				return responseErrorData('Ocurrio un error interno creando un interesado en el SGDEA');
			} else {
				$comlist_interesados[] = $interesado_id;
			}
		}
		//***************************************************************************************************************
		if (!empty($params['numero_resolucion'])) {
			$existsComByResol = ComEnviadaPeer::isExistComByNumResolucion($params['numero_resolucion'], $interesados_nuids, null, false);
			if ($existsComByResol || !empty($existsComByResol)) {
				return responseErrorData('No fue posible radicar la comunicación, existen radicados asociados al numero de resolución y el interesado en el SGDEA, numeros de radicado (' . $existsComByResol . ')');
			}
		}
		//***************************************************************************************************************
		$com_enviada_anterior = new ComEnviada();
		$com_enviada = ComEnviadaPeer::addComEnviada($params);
		//***************************************************************************************************************
		if ($com_enviada == null) {
			return responseErrorData('Error interno del servidor SGDEA');
		}
		//***************************************************************************************************************
		$comlist_interesados = array_unique($comlist_interesados);
		foreach ($comlist_interesados as $interesado_item) {
			EnviadaInteresadosPeer::addNewInteresadoByComId($com_enviada->getPrimaryKey(), $interesado_item);
		}
		//***************************************************************************************************************
		if (!$IsEntradaExterna && $com_recibida != null) {
			foreach ($com_recibida->getComrecibidaInteresadoss() as $interesado_origen) {
				$isAdded = EnviadaInteresadosPeer::addNewInteresadoByComId($com_enviada->getPrimaryKey(), $interesado_origen->getInteresadoId());
				if ($isAdded) {
					$comlist_interesados[] = $interesado_origen->getInteresadoId();
				}
			}
		}
		//***************************************************************************************************************
		ComEnviadaPeer::insertaEnviadaUsuarios($cuser_origen->getUsuarioId(), $com_enviada->getPrimaryKey(), 1, $cuser_origen->getPrimaryKey(), $estado_enviada);
		ComEnviadaPeer::insertaEnviadaUsuarios($cuser_gestor->getUsuarioId(), $com_enviada->getPrimaryKey(), 5, $cuser_gestor->getPrimaryKey(), $estado_enviada, 3, 0);
		//***************************************************************************************************************
		EnviadaUsuarioPeer::updateAproFirmaAll($com_enviada->getPrimaryKey(), array(1, 5));
		//***************************************************************************************************************
		$estaAsignada = 0;
		$otherAsigned = false;
		$genRadAutomatico = true;
		foreach ($cuser_firma as $cuser) {
			if (!$cuser->getUsuario()->getFirmaDesatendida()) {
				$genRadAutomatico = false;
				$otherAsigned = true;
				$estaAsignada = $estaAsignada == 0 ? 1 : 0;
			}
			//***********************************************************************************************************
			ComEnviadaPeer::insertaEnviadaUsuarios($cuser->getUsuarioId(), $com_enviada->getPrimaryKey(), 2, $cuser->getPrimaryKey(), $estado_enviada, 5, $estaAsignada);
		}
		//***************************************************************************************************************
		if ($otherAsigned == false) {
			EnviadaUsuarioPeer::updateAproFirma($com_enviada->getPrimaryKey(), array(1));
		}
		//***************************************************************************************************************
		$msg_info = array();
		if ($com_enviada->getTipoMasivo() == 1 && $genRadAutomatico == true) {
			$estadocomenviada_id = 2;
			$radicado = $com_enviada->getRadicadoFormat(null, $com_enviada->getRegionalId());
			$com_enviada->setRadicado($radicado);
			$com_enviada->setEstadocomenviadaId($estadocomenviada_id);
			$com_enviada->save();
			//***********************************************************************************************************
			if ($wslog != null) {
				$wslog->setTipoOperacion("Consumen servicio web SGDEA => " . $radicado);
				$wslog->setMensaje("Ejecuta exitoso genera radicado");
				$wslog->save();
			}
			//***********************************************************************************************************
			EnviadaUsuarioPeer::updateEstados($com_enviada->getPrimaryKey(), $estadocomenviada_id);
			EnviadaUsuarioPeer::updateAproFirmaAll($com_enviada->getPrimaryKey(), array(2, 4, 5));
			//***********************************************************************************************************
			if (($com_enviada->getEstadocomenviadaId() != 1) && ($com_enviada->getFirmadoDigital() == 0)) {
				$response_firma = $com_enviada->singDocumentProcess();
				$msg_info[] = isset($response_firma['message']) ? trim($response_firma['message']) : "Por favor verique si el documento fue firmado correctamente";
			}
			//***********************************************************************************************************
			if ($com_recibida != null) {
				$com_enviada->setResponseComOrigen($cuser_firma[0]->getUsuarioId());
			}
		} elseif ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $com_enviada->getPrimaryKey());
			$wslog->setMensaje("Ejecuta Exitoso Genera Consecutivo, el radicado debe generarse manual");
			$wslog->save();
			//***********************************************************************************************************
			$msg_info[] = "No se genero radicado automatico, alguno de los usuarios firmantes no tienen la firma desatendida habilitada, se debe radicar de forma manual ingresando al SGDEA";
		}
		//***************************************************************************************************************
		if ($com_enviada->getTipoEnvio() == 4 && ($com_enviada->getEstadocomenviadaId() != 1)) {
			$tiposervicio_id = TipoServicioPeer::getTipoServicioByTipoEnvioIntegra($com_enviada->getTipoEnvio());
			$response_servicio = $com_enviada->addServicioByCom($cuser_gestor->getUsuarioId(), $tiposervicio_id, $comlist_interesados, $directorioexteno_id);
			//***********************************************************************************************************
			if (!$response_servicio['isError']) {
				$servicio = $response_servicio['object'];
				$com_enviada->setServicioId($servicio->getPrimaryKey());
				$com_enviada->setEstadocomenviadaId(6);
				$com_enviada->save();
				//*******************************************************************************************************
				EnviadaUsuarioPeer::updateEstados($com_enviada->getPrimaryKey());
				//*******************************************************************************************************
				if (count($msg_info)) {
					ServicioPeer::insertBitacoraServicio(
						$servicio->getPrimaryKey(),
						$servicio->getServicioestadoId(),
						$servicio->getUsuarioId(),
						$servicio->getUsuarioId(),
						$msg_info[0]
					);
				}
				//*******************************************************************************************************
				$msg_info[] = sprintf('Solicitud de servicio creada con radicado %s', $servicio->getRadicado());
			} else {
				$msg_info[] = isset($response_servicio['message']) ? trim($response_servicio['message']) : "Error al crear la solicitud de servicio";
			}
		}
		//****************************************************************************************************
		AuditLogPeer::guardarAuditoriaLite("ComEnviada", $com_enviada_anterior, $com_enviada, ModulesEnable::ComEnviada, $com_enviada->getRadicado(), $cuser_origen->getPrimaryKey());
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $com_enviada->getPrimaryKey(),
			'RADICADO' => $com_enviada->getRadicado(),
			'FECHA_CREACION' => $com_enviada->getFechaCreacion(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSGERROR' => implode(", ", $msg_info)
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => $ex->getMessage()
		);
	} catch (Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => $ex->getMessage()
		);
	}
	return $response_data;
}

function AddRadicadoSalidaOferta($EntSecurity = array(), $EntComEnviada = array(), $EntInteresadoOfList = array(), $EntRemitente = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//*******************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	$util_simad = new simad_util();
	//*******************************************************************************************************************
	try {
		//$filedir_target = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(29)->getValortexto() . 'uploads');
		$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddRadicadoSalidaOferta", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//***************************************************************************************************************
		$params = array();
		$periodo_id = isset($EntComEnviada['PERIODO_ID']) ? trim($EntComEnviada['PERIODO_ID']) : date("Y");
		$adju_b64 = isset($EntComEnviada['ARCHIVO_DIGIT']) ? trim($EntComEnviada['ARCHIVO_DIGIT']) : null;
		$filename_source = isset($EntComEnviada['ARCHIVO_NOMBRE']) ? (trim($EntComEnviada['ARCHIVO_NOMBRE'])) : null;
		$tramite_origen = isset($EntComEnviada['CLASIFICACION_DOCUMENTO']) ? trim($EntComEnviada['CLASIFICACION_DOCUMENTO']) : null;
		$uiduser_origen = isset($EntComEnviada['NUID_RADICA']) ? trim($EntComEnviada['NUID_RADICA']) : trim($EntComEnviada['NUID_GESTOR']);
		$tipo_envio = isset($EntComEnviada['TIPO_ENVIO']) ? trim($EntComEnviada['TIPO_ENVIO']) : null;
		$app_origen = isset($EntComEnviada['APP_ORIGEN']) ? trim($EntComEnviada['APP_ORIGEN']) : "";
		//***************************************************************************************************************
		$object_list = $EntComEnviada['FIRMAS'];
		$cuser_firma = array();
		foreach ($object_list as $ufirma_list) {
			if (isset($ufirma_list['NUID_FIRMA'])) {
				$var_ccfirma = !empty($ufirma_list['NUID_FIRMA']) ? trim($ufirma_list['NUID_FIRMA']) : -1;
				$var_ucargofirma = !empty($ufirma_list['UCARGO_FIRMA']) ? trim($ufirma_list['UCARGO_FIRMA']) : -1;
				//***************************************************************************************************
				if (empty($var_ucargofirma)) {
					$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser($var_ccfirma, true);
				} else {
					$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidAndCargoUser($var_ccfirma, $var_ucargofirma, true);
				}
				//***************************************************************************************************
				if ($usuario_firma == null) {
					return responseErrorData('El usuario con cedula ' . $var_ccfirma . ', que firma el oficio no se encontro en el SGDEA');
				} else {
					$cuser_firma[] = $usuario_firma;
					$nuid_firma = $var_ccfirma;
				}
			} elseif (is_array($ufirma_list)) {
				foreach ($ufirma_list as $xufirma) {
					$var_ccfirma = !empty($xufirma['NUID_FIRMA']) ? trim($xufirma['NUID_FIRMA']) : -1;
					$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser($var_ccfirma, true);
					//***************************************************************************************************
					if ($usuario_firma == null) {
						return responseErrorData('El usuario con cedula ' . $var_ccfirma . ', que firma el oficio no se encontro en el SGDEA');
					} else {
						$cuser_firma[] = $usuario_firma;
						$nuid_firma = $var_ccfirma;
					}
				}
			} elseif (trim($ufirma_list)) {
				//return responseErrorData('Un Firmante '.$ufirma_list);
				$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser(trim($ufirma_list), true);
				$nuid_firma = trim($ufirma_list);
			} else {
				$usuario_firma = null;
				$nuid_firma = "(unidefined)";
			}
			//***********************************************************************************************************
			if ($usuario_firma == null) {
				return responseErrorData('El usuario con cedula ' . $nuid_firma . ', que firma el oficio no se encontro en el SGDEA');
			} else {
				$cuser_firma[] = $usuario_firma;
			}
		}
		$cuser_firma = array_unique($cuser_firma);
		//***************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		$interesados_nuids = array();
		foreach ($EntInteresadoOfList as $info_list) {
			if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
				if (!trim($info_list['NUMERO_IDENTIFICACION'])) {
					return responseErrorData('El numero de identificación del interesado no es valido');
					exit;
				}
				//********************************************************************************************************
				$interesados_nuids[] = trim($info_list['NUMERO_IDENTIFICACION']);
				//********************************************************************************************************
				$interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'], $info_list['PRIMER_APELLIDO'], $info_list['NUMERO_IDENTIFICACION']);
				if ($interesado_id != null) {
					$comlist_interesados[] = $interesado_id;
				} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
					$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
					if ($tipouid_id == null) {
						return responseErrorData('El tipo de identificación del interesado no es un valor valido');
					}
					//****************************************************************************************************
					$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
					$optional_fileds = array('TIPO_GENERO' => true, 'EMAIL' => true);
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list, $optional_fileds);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				} else {
					$info_list['TIPO_IDENTIFICACION'] = 5;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				}
			} else {
				return responseErrorData('El nombre o numero de identificación del interesado no es valido');
				exit;
			}
		}
		//***************************************************************************************************************
		if (count($comlist_interesados) <= 0 && count($laddnew_interesados) <= 0) {
			return responseErrorData('Debe enviar como minimo un interesado');
		}
		//***************************************************************************************************************
		$cuser_origen = $usuario_creador != null ? $usuario_creador : CargoUsuarioPeer::getCargoUsuarioByNuidUser($uiduser_origen, true);
		$cuser_gestor = CargoUsuarioPeer::getCargoUsuarioByNuidUser(trim($EntComEnviada['NUID_GESTOR']), true);
		$regional_origen = isset($EntComEnviada['PUNTO_RADICACION']) ? RegionalPeer::getRegionalByName(trim($EntComEnviada['PUNTO_RADICACION'])) : null;
		//***************************************************************************************************************
		if (empty($cuser_gestor) || is_null($cuser_gestor)) {
			$cuser_gestor = $cuser_firma[0];
		}
		//***************************************************************************************************************
		$ciudad = isset($EntComEnviada['CODIGO_MUNICIPIO']) ? CiudadPeer::getCiudadByNombAndCod(null, trim($EntComEnviada['CODIGO_MUNICIPIO'])) : null;
		$asunto = isset($EntComEnviada['ASUNTO']) ? (trim($EntComEnviada['ASUNTO'])) : null;
		$estado_enviada = 1; //borrador estado inicial
		//$com_recibida = ComRecibidaPeer::getComObjectByRadicado(trim($EntComEnviada['RADICADO_ENTRADA']),$periodo_id);
		//***************************************************************************************************************
		if (count($cuser_firma) <= 0) {
			return responseErrorData('El usuario que firma el oficio es obligatorio o no se encontro en el SGDEA');
		}
		//if($cuser_origen == null){ responseErrorData('El usuario que radica el oficio es obligatorio o no se encontro en el SGDEA'); }
		//***************************************************************************************************************
		$dependencia_codigo = isset($EntComEnviada['CODIGO_DEPENDENCIA']) ? trim($EntComEnviada['CODIGO_DEPENDENCIA']) : null;
		$dependencia_destinopk = $cuser_firma[0]->getUsuario()->getDependenciaId();
		if (!empty($dependencia_codigo)) {
			$dependencia_com = !empty(trim($dependencia_codigo)) ? DependenciaPeer::getDependenciaByCodigo($dependencia_codigo) : null;
			if ($dependencia_com != null) {
				$dependencia_destinopk = $dependencia_com->getPrimaryKey();
			}
		}
		//***************************************************************************************************************
		if (empty($cuser_gestor) || is_null($cuser_gestor)) {
			$msg_text = 'El usuario gestor del oficio esta inactivo o no se encuentra registrado en el SGDEA';
			$user_info = CargoUsuarioPeer::getCargoUsuarioByNuidUserEx(trim($EntComEnviada['NUID_GESTOR']), true);
			if ($user_info['error'] == 200) {
				$msg_text = 'Ocurrio un error con el usuario gestor del oficio en el SGDEA';
			} elseif ($user_info['error'] == 400) {
				$msg_text = sprintf('Ocurrio un error interno en el SGDEA(%s => %s)', $user_info['message'], $user_info['info']);
			} elseif ($user_info['error'] == 401) {
				$cusuario_gestor = $user_info['object'];
				$msg_text = sprintf('El usuario gestor(%s) del oficio esta (%s) en el SGDEA', $cusuario_gestor->getUsuario()->getFullNombre(), $user_info['info']);
			}
			//***********************************************************************************************************
			return responseErrorData($msg_text);
		}
		//***************************************************************************************************************
		//if($regional_origen == null){ responseErrorData('El punto de radicacion es obligatorio o no se encontro en el SGDEA'); }
		if ($ciudad == null) {
			return responseErrorData('El municipio es obligatorio o no se encontro en el SGDEA');
		}
		if ($adju_b64 == null) {
			return responseErrorData('Debe enviar el documento digital del oficio');
		}
		//if(!count($EntInteresadoOfList)){ return responseErrorData('Debe enviar minimo un interesado'); }
		//if(!($EntInteresadoOfList)){ return responseErrorData('Los datos del interesado no son corresctos'); }
		if ($asunto == null) {
			return responseErrorData('El asunto es obligatorio');
		}
		if ($tipo_envio == null) {
			return responseErrorData('El tipo de envio es un campo obligatorio');
		}
		if ($filename_source == null) {
			return responseErrorData('El nombre del archivo es un campo obligatorio');
		}
		//***************************************************************************************************************
		$file_vars = pathinfo($filedir_target . $filename_source);
		$filename_source = $util_simad->clean_name_file($file_vars);
		$fullpath = $filedir_target . uniqid() . "_" . utf8_encode($filename_source);
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorData('Error interno del servidor al crear el archivo');
		}
		//***************************************************************************************************************
		$mime_trust = array('application/pdf', 'application/x-pdf', 'application/x-bzpdf', 'application/x-gzpdf');
		$file_valid = simad_util::CheckIsValidFormatFile($fullpath, $mime_trust);
		if (!$file_valid) {
			return responseErrorData('El formato del archivo no esta permitido');
		}
		//***************************************************************************************************************
		$info_file = new SplFileInfo($fullpath);
		$mimetypes = array("pdf");
		$extension  = strtolower($info_file->getExtension());
		if (!in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorData('El formato ' . $extension . ' del archivo no es valido');
		}
		//***************************************************************************************************************
		if ($regional_origen == null) {
			$regional_origen = RegionalPeer::retrieveByPk($cuser_firma[0]->getUsuario()->getRegionalId());
		}
		//***************************************************************************************************************
		$addRemitente = false;
		$directorio_exteno = null;
		if (count($EntRemitente)) {
			$nuid_remitente = isset($EntComEnviada['NUID']) ? trim($EntComEnviada['NUID']) : null;
			if ($nuid_remitente != null) {
				$directorio_exteno = DirectorioExternoPeer::existsDirectorioExterno($EntRemitente);
			} else {
				$addRemitente = false;
			}
		}
		//***************************************************************************************************************
		$directorioexteno_id = !empty($directorio_exteno) ? $directorio_exteno->getPrimaryKey() : null;
		//***************************************************************************************************************
		$params['regional_id'] = $regional_origen->getPrimaryKey();
		$params['dependencia_id'] = $dependencia_destinopk;
		$params['estadocomenviada_id'] = $estado_enviada;
		$params['ciudad_id'] = $cuser_firma[0]->getUsuario()->getRegional()->getCiudadId();
		$params['periodo_id'] = $periodo_id;
		$params['asunto'] = $asunto;
		$params['folios'] = isset($EntComEnviada['FOLIOS']) ? trim($EntComEnviada['FOLIOS']) : 1;
		$params['observaciones_envio'] = isset($EntComEnviada['OBSERVACIONES_ENVIO']) ? utf8_encode(trim($EntComEnviada['OBSERVACIONES_ENVIO'])) : null;
		$params['consecutivo_resp'] = null;
		$params['firma_electronica'] = isset($EntComEnviada['FIRMA_DIGITAL']) ? trim($EntComEnviada['FIRMA_DIGITAL']) : 0;
		$params['archivo_digit'] = $adju_b64;
		$params['use_membrete'] = isset($EntComEnviada['USE_MEMBRETE']) ? trim($EntComEnviada['USE_MEMBRETE']) : null;
		$params['tipofirmadigital_id'] = isset($EntComEnviada['TIPO_FIRMA']) ? trim($EntComEnviada['TIPO_FIRMA']) : null;
		$params['tipo_envio'] = $tipo_envio;
		$params['tipo_masivo'] = isset($EntComEnviada['TIPO_MASIVO']) ? trim($EntComEnviada['TIPO_MASIVO']) : 1;
		$params['expediente_id'] = isset($EntComEnviada['EXPEDIENTE_ID']) ? trim($EntComEnviada['EXPEDIENTE_ID']) : null;
		$params['marco_normativo'] = isset($EntComEnviada['MARCO_NORMATIVO']) ? trim($EntComEnviada['MARCO_NORMATIVO']) : null;
		$params['numero_fud'] = isset($EntComEnviada['NUMERO_FUD']) ? trim($EntComEnviada['NUMERO_FUD']) : null;
		//$params['tipo_integracion'] = trim($app_origen) ? "OFERTA - ".$app_origen : "OFERTA";
		$params['tipo_integracion'] = "OFERTA";
		//***************************************************************************************************************
		if (isset($EntComEnviada['FECHA_RESOLUCION'])) {
			try {
				$date = new DateTime($EntComEnviada['FECHA_RESOLUCION']);
				$params['fecha_resolucion'] = $date->format('Y-m-d');
			} catch (Exception $ex) {
				$params['fecha_resolucion'] = null;
				return responseErrorData('El formato de la fecha resolucion no es valido ' . $ex->getMessage());
			}
		}
		//***************************************************************************************************************
		$params['numero_resolucion'] = isset($EntComEnviada['NUMERO_RESOLUCION']) ? trim($EntComEnviada['NUMERO_RESOLUCION']) : null;
		$params['suborigen'] = isset($EntComEnviada['SUBORIGEN']) ? trim($EntComEnviada['SUBORIGEN']) : null;
		$params['radicado_sys_origen'] = isset($EntComEnviada['NUMRADSYSORIGEN']) ? trim($EntComEnviada['NUMRADSYSORIGEN']) : null;
		$params['tipo_documental_cod'] = isset($EntComEnviada['TIPO_DOCUMENTAL']) ? trim($EntComEnviada['TIPO_DOCUMENTAL']) : null;
		$params['UrlFileWord'] = $fullpath;
		$params['IsCreateWord'] = true;
		//***************************************************************************************************************
		foreach ($laddnew_interesados as $newitem) {
			$newitem['USUARIO_ID'] = $cuser_origen->getUsuarioId();
			$interesado_id = InteresadosPeer::addNewInteresado($newitem);
			if ($interesado_id == null) {
				return responseErrorData('Ocurrio un error al crear el interesado en el SGDEA');
			} else {
				$comlist_interesados[] = $interesado_id;
			}
		}
		//***************************************************************************************************************
		if (!empty($params['numero_resolucion'])) {
			$existsComByResol = ComEnviadaPeer::isExistComByNumResolucion($params['numero_resolucion'], $interesados_nuids, null, false);
			if ($existsComByResol || !empty($existsComByResol)) {
				return responseErrorData('No fue posible radicar la comunicación, existen radicados asociados al numero de resolución y el interesado en el SGDEA, numeros de radicado (' . $existsComByResol . ')');
			}
		}
		//***************************************************************************************************************
		$com_enviada_anterior = new ComEnviada();
		$com_enviada = ComEnviadaPeer::addComEnviada($params);
		//***************************************************************************************************************
		if ($com_enviada == null) {
			return responseErrorData('Error interno del servidor del SGDEA');
		}
		//***************************************************************************************************************
		foreach ($comlist_interesados as $interesado_item) {
			EnviadaInteresadosPeer::addNewInteresadoByComId($com_enviada->getPrimaryKey(), $interesado_item);
		}
		//***************************************************************************************************************
		ComEnviadaPeer::insertaEnviadaUsuarios($cuser_origen->getUsuarioId(), $com_enviada->getPrimaryKey(), 1, $cuser_origen->getPrimaryKey(), $estado_enviada);
		ComEnviadaPeer::insertaEnviadaUsuarios($cuser_gestor->getUsuarioId(), $com_enviada->getPrimaryKey(), 5, $cuser_gestor->getPrimaryKey(), $estado_enviada, 3, 0);
		//***************************************************************************************************************
		$estaAsignada = 0;
		$genRadAutomatico = true;
		foreach ($cuser_firma as $cuser) {
			if (!$cuser->getUsuario()->getFirmaDesatendida()) {
				$genRadAutomatico = false;
				$estaAsignada = $estaAsignada == 0 ? 1 : 0;
			}
			//***********************************************************************************************************
			ComEnviadaPeer::insertaEnviadaUsuarios($cuser->getUsuarioId(), $com_enviada->getPrimaryKey(), 2, $cuser->getPrimaryKey(), $estado_enviada, 5, $estaAsignada);
		}
		//***************************************************************************************************************
		$msg_info = array();
		if ($com_enviada->getTipoMasivo() && $genRadAutomatico == true) {
			$estadocomenviada_id = 2;
			$radicado = $com_enviada->getRadicadoFormat(null, $com_enviada->getRegionalId());
			$com_enviada->setRadicado($radicado);
			$com_enviada->setEstadocomenviadaId($estadocomenviada_id);
			$com_enviada->save();
			//***************************************************************************************************************
			if ($wslog != null) {
				$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $radicado);
				$wslog->setMensaje("Ejecuta Exitoso Genera Radicado");
				$wslog->save();
			}
			//***********************************************************************************************************
			EnviadaUsuarioPeer::updateEstados($com_enviada->getPrimaryKey(), $estadocomenviada_id);
			EnviadaUsuarioPeer::updateAproFirmaAll($com_enviada->getPrimaryKey(), array(2, 4, 5));
			//***********************************************************************************************************
			if (($com_enviada->getEstadocomenviadaId() != 1) && ($com_enviada->getFirmadoDigital() == 0)) {
				$response_firma = $com_enviada->singDocumentProcess();
				$msg_info[] = isset($response_firma['message']) ? trim($response_firma['message']) : "Por favor verique la firma digital de documento";
			}
		} elseif ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $com_enviada->getPrimaryKey());
			$wslog->setMensaje("Ejecuta Exitoso Genera Consecutivo");
			$wslog->save();
		}
		//***************************************************************************************************************
		if ($com_enviada->getTipoEnvio() == 4 && ($com_enviada->getEstadocomenviadaId() != 1)) {
			$tiposervicio_id = TipoServicioPeer::getTipoServicioByTipoEnvioIntegra($com_enviada->getTipoEnvio());
			$response_servicio = $com_enviada->addServicioByCom($cuser_gestor->getUsuarioId(), $tiposervicio_id, $comlist_interesados, $directorioexteno_id);
			//***********************************************************************************************************
			if (!$response_servicio['isError']) {
				$servicio = $response_servicio['object'];
				$com_enviada->setServicioId($servicio->getPrimaryKey());
				$com_enviada->setEstadocomenviadaId(6);
				$com_enviada->save();
				//*******************************************************************************************************
				EnviadaUsuarioPeer::updateEstados($com_enviada->getPrimaryKey());
				//*******************************************************************************************************
				if (count($msg_info)) {
					ServicioPeer::insertBitacoraServicio(
						$servicio->getPrimaryKey(),
						$servicio->getServicioestadoId(),
						$servicio->getUsuarioId(),
						$servicio->getUsuarioId(),
						$msg_info[0]
					);
				}
				//*******************************************************************************************************
				$msg_info[] = sprintf('Solicitud de servicio creada con radicado %s', $servicio->getRadicado());
			} else {
				$msg_info[] = isset($response_servicio['message']) ? trim($response_servicio['message']) : "Error al crear la solicitud de servicio";
			}
		}
		//****************************************************************************************************
		AuditLogPeer::guardarAuditoriaLite("ComEnviada", $com_enviada_anterior, $com_enviada, ModulesEnable::ComEnviada, $com_enviada->getRadicado(), $cuser_origen->getPrimaryKey());
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $com_enviada->getPrimaryKey(),
			'RADICADO' => $com_enviada->getRadicado(),
			'FECHA_CREACION' => $com_enviada->getFechaCreacion(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSGERROR' => implode(", ", $msg_info)
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => 'SoapFault => ' . $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => 'SoapFault => ' . $ex->getMessage()
		);
	} catch (Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => 'Exception => ' . $ex->getMessage()
		);
	}
	return $response_data;
}

function AddActoAdministrativo($EntSecurity = array(), $EntActoAdministrativo = array(), $EntInteresado = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//*******************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	$util_simad = new simad_util();
	//*******************************************************************************************************************
	try {
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(29)->getValortexto() . 'uploads');
		$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorData($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddActoAdministrativo", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//***************************************************************************************************************
		$params = array();
		//***************************************************************************************************************
		$object_list = $EntActoAdministrativo['FIRMAS'];
		$cuser_firma = array();
		foreach ($object_list as $ufirma_list) {
			if (is_array($ufirma_list)) {
				foreach ($ufirma_list as $xufirma) {
					$var_ccfirma = !empty($xufirma) ? trim($xufirma) : -1;
					$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser($var_ccfirma, true);
					//***************************************************************************************************
					if ($usuario_firma == null) {
						return responseErrorData('El usuario con cedula ' . $var_ccfirma . ', que firma el oficio no se encontro en el SGDEA');
					} else {
						$cuser_firma[] = $usuario_firma;
						$nuid_firma = $var_ccfirma;
					}
				}
			} elseif (trim($ufirma_list)) {
				$usuario_firma = CargoUsuarioPeer::getCargoUsuarioByNuidUser(trim($ufirma_list), true);
				$nuid_firma = trim($ufirma_list);
			} else {
				$usuario_firma = null;
				$nuid_firma = "(unidefined)";
			}
			//***********************************************************************************************************
			if ($usuario_firma == null) {
				return responseErrorData('El usuario con cedula ' . $nuid_firma . ', que firma el oficio no se encontro en el SGDEA');
			} else {
				$cuser_firma[] = $usuario_firma;
			}
		}
		$cuser_firma = array_unique($cuser_firma);
		//***************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		$interesados_nuids = array();

		foreach ($EntInteresado as $info_list) {
			if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
				if (!trim($info_list['NUMERO_IDENTIFICACION'])) {
					return responseErrorData('El numero de identificación del interesado no es valido');
					exit;
				}
				//********************************************************************************************************
				$interesados_nuids[] = trim($info_list['NUMERO_IDENTIFICACION']);
				//********************************************************************************************************
				$interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'], $info_list['PRIMER_APELLIDO'], $info_list['NUMERO_IDENTIFICACION']);
				if ($interesado_id != null) {
					$comlist_interesados[] = $interesado_id;
				} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
					$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
					if ($tipouid_id == null) {
						return responseErrorData('El tipo de identificación del interesado no es un valor valido');
					}
					//****************************************************************************************************
					$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				} else {
					$info_list['TIPO_IDENTIFICACION'] = 5;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorData($isValidIntInfo['MsgError']);
						exit;
					}
				}
			} else {
				return responseErrorData('El nombre o numero de identificación del interesado no es valido');
				exit;
			}
		}
		//***************************************************************************************************************
		$nuid_gestor = isset($EntActoAdministrativo['NUID_GESTOR']) ? trim($EntActoAdministrativo['NUID_GESTOR']) : 1;
		$cuser_gestor = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuid_gestor, true);
		if (empty($cuser_gestor) || is_null($cuser_gestor)) {
			$cuser_gestor = $cuser_firma[0];
		}
		//***************************************************************************************************************
		$estadoactoadministrativo = 1;
		$regional_descripcion = isset($EntActoAdministrativo['REGIONAL_RADICACION']) ? trim($EntActoAdministrativo['REGIONAL_RADICACION']) : 0;
		$plantillascom_descripcion = isset($EntActoAdministrativo['TIPO_DOCUMENTO']) ? trim($EntActoAdministrativo['TIPO_DOCUMENTO']) : 0;
		$dependencia_text = isset($EntActoAdministrativo['DEPENDENCIA_DESTINO']) ? trim($EntActoAdministrativo['DEPENDENCIA_DESTINO']) : 0;
		$prioridadcom_text = isset($EntActoAdministrativo['PRIORIDAD_COM']) ? trim($EntActoAdministrativo['PRIORIDAD_COM']) : 0; //validar q exista
		$firma_digital = isset($EntActoAdministrativo['FIRMA_DIGITAL']) ? trim($EntActoAdministrativo['FIRMA_DIGITAL']) : null;
		$adju_b64 = isset($EntActoAdministrativo['ARCHIVO_DIGIT']) ? trim($EntActoAdministrativo['ARCHIVO_DIGIT']) : null;
		$filename_source = isset($EntActoAdministrativo['ARCHIVO_NOMBRE']) ? trim($EntActoAdministrativo['ARCHIVO_NOMBRE']) : null;
		$nombre_subserie = isset($EntActoAdministrativo['SUBSERIE_NOMBRE']) ? trim($EntActoAdministrativo['SUBSERIE_NOMBRE']) : null;
		$codigo_subserie = isset($EntActoAdministrativo['SUBSERIE_CODIGO']) ? trim($EntActoAdministrativo['SUBSERIE_CODIGO']) : 0;
		$asunto = isset($EntActoAdministrativo['ASUNTO']) ? trim($EntActoAdministrativo['ASUNTO']) : null;
		$folios = isset($EntActoAdministrativo['FOLIOS']) ? trim($EntActoAdministrativo['FOLIOS']) : 1;
		$anexos = isset($EntActoAdministrativo['ANEXOS']) ? htmlspecialchars(trim($EntActoAdministrativo['ANEXOS'])) : null;
		$observaciones = isset($EntActoAdministrativo['OBSERVACIONES']) ? htmlspecialchars(trim($EntActoAdministrativo['OBSERVACIONES'])) : null;
		$unidaddocumental_id = isset($EntActoAdministrativo['NUMERO_EXPEDIENTE']) ? trim($EntActoAdministrativo['NUMERO_EXPEDIENTE']) : null;
		$tipodoc_codigo = isset($EntActoAdministrativo['CODIGO_TIPODOC']) ? trim($EntActoAdministrativo['CODIGO_TIPODOC']) : null;
		$nuid_destinatario = isset($EntActoAdministrativo['NUID_DESTINATARIO']) ? trim($EntActoAdministrativo['NUID_DESTINATARIO']) : null;
		$suborigen = isset($EntActoAdministrativo['SUBORIGEN']) ? trim($EntActoAdministrativo['SUBORIGEN']) : null;
		$marco_normativo = isset($EntActoAdministrativo['MARCO_NORMATIVO']) ? trim($EntActoAdministrativo['MARCO_NORMATIVO']) : null;
		$tipo_notificacion = isset($EntActoAdministrativo['TIPO_NOTIFICACION']) ? trim($EntActoAdministrativo['TIPO_NOTIFICACION']) : null;
		//***************************************************************************************************************
		$cargo_usuario = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_creador->getPrimaryKey(), true);
		//***************************************************************************************************************
		$cargo_usuario_destinatario = null;
		if ($nuid_destinatario != null || $nuid_destinatario != "") {
			$cargo_usuario_destinatario = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuid_destinatario, true);
			$ucargodestino_id = $cargo_usuario_destinatario->getPrimaryKey();
			$usuario_destinatario_id = $cargo_usuario_destinatario->getUsuarioId();
		} else {
			$usuario_destinatario_id = null;
		}

		//***************************************************************************************************************
		$tipo_envio = 1;
		//***************************************************************************************************************
		if (empty($dependencia_text)) {
			return responseErrorData('La dependencia es obligatoria');
		}
		if (empty($prioridadcom_text)) {
			return responseErrorData('La prioridad es obligatoria');
		}
		if (empty($adju_b64)) {
			return responseErrorData('Debe enviar el documento digital del oficio');
		}
		if (empty($filename_source)) {
			return responseErrorData('El nombre del archivo es un campo obligatorio');
		}
		if (empty($nombre_subserie)) {
			return responseErrorData('El nombre subserie es obligatorio');
		}
		if (empty($codigo_subserie)) {
			return responseErrorData('El codigo subserie es obligatorio');
		}
		if (empty($asunto)) {
			return responseErrorData('El asunto es obligatorio');
		}
		if (empty($tipo_envio)) {
			return responseErrorData('El tipo de envio es un campo obligatorio');
		}
		//*************************************VALIDACIONES ESPECIALES **************************************************
		$tipo_servicio = $tipo_notificacion ? TipoServicioPeer::getTipoServicioObjByName($tipo_notificacion) : null;
		$tiposervicio_id = null;
		$tipo_envio = null;
		if ($tipo_servicio != null) {
			$tipo_servicio = ($tipo_servicio instanceof TipoServicio) ? $tipo_servicio : TipoServicioPeer::retrieveByPK($tipo_servicio);
			$tiposervicio_id = $tipo_servicio->getPrimaryKey();
			$lcomtipo_servicio[$tiposervicio_id] = $tipo_notificacion;
			$tipo_envio = $tipo_servicio->getTipoEnvio();
		}
		//***************************************************************************************************************
		//VALIDAR DEPENDENCIA
		$dependencia = DependenciaPeer::getDependenciaByCodigo($dependencia_text);
		if ($dependencia == null || $dependencia == array()) {
			return responseErrorData('La dependencia no se encuentra en el sistema');
		}
		//***************************************************************************************************************
		//VALIDAR PRIORIDAD COM
		$prioridadcom = PrioridadComPeer::getPrioridadcomById($prioridadcom_text);
		if ($prioridadcom == null || $prioridadcom == array()) {
			return responseErrorData('La prioridad no se encuentra en el sistema');
		}
		//***************************************************************************************************************
		//VALIDAR REGIONAL_RADICACION
		$regional = RegionalPeer::getRegionalByName($regional_descripcion);
		if ($regional == null || $regional == array()) {
			return responseErrorData('La regional no se encuentra en el sistema');
		}
		//***************************************************************************************************************
		$plantillacomModulo = PlantillasComPeer::getTplByDescripcionModulo($plantillascom_descripcion, ModulesEnable::ActosAdministrativos);
		if ($plantillacomModulo == null) {
			return responseErrorData('El tipo de plantilla no existe o no esta asociada al modulo de actos administrativos');
		}
		$plantillacom_id = $plantillacomModulo->getPrimaryKey();
		//***************************************************************************************************************
		if ($usuario_destinatario_id == null && (!count($comlist_interesados) && !count($laddnew_interesados))) {
			return responseErrorData('Se necesita al menos un destinatario o un interesado');
		}
		//***************************************************************************************************************
		if ($usuario_destinatario_id !== null && (count($comlist_interesados) || count($laddnew_interesados))) {
			return responseErrorData('No puede existir interesado y destinatario al mismo tiempo');
		}
		//***************************************************************************************************************
		// validar que el destinatario exista, si viene desde el usuario
		if ($nuid_destinatario != null || $nuid_destinatario != "") {
			$usuario_exist = UsuarioPeer::getUsuarioByNuid($nuid_destinatario);
			if ($usuario_exist === false) {
				return responseErrorData('El destinatario no se encuentra en el sistema');
			}
		}
		//***************************************************************************************************************
		$subserie = SubseriePeer::getSubserieByNombreAndCodigo($codigo_subserie, $nombre_subserie);
		if ($subserie == null || $subserie == array()) {
			return responseErrorData('La subserie no se encuentra en el sistema');
		}
		//***************************************************************************************************************
		$tipodocumental_id = null;
		if (trim($tipodoc_codigo)) {
			$tipo_documental = TipoDocumentalPeer::getTipoDocByCodigo(trim($tipodoc_codigo));
			$tipodocumental_id = $tipo_documental != null ? $tipo_documental->getPrimaryKey() : null;
			//***********************************************************************************************************
			if ($tipo_documental->getSubserieId() != $subserie->getPrimaryKey()) {
				return responseErrorData('El tipo de documento no corresponde con la subserie del acto administrativo');
			}
		}
		//***************************************************************************************************************
		if ($unidaddocumental_id != null) {
			$unidad_documental = UnidadDocumentalPeer::getExpedienteByCodBarras($unidaddocumental_id);
			if ($unidad_documental == null) {
				return responseErrorData('El expediente no se encuentra en el sistema');
			}

			if ($unidad_documental->getSubserieId() != $subserie->getPrimaryKey()) {
				return responseErrorData('La subserie no corresponde con el expediente del acto administrativo');
			}
			//***********************************************************************************************************
			if ($dependencia->getPrimaryKey() != $unidad_documental->getSubserie()->getSerie()->getDependenciaId()) {
				return responseErrorData('La dependencia del acto administrativo, no corresponde con la serie y subserie del expediente');
			}
		}
		//*************************************VALIDACION DEL ARCHIVO****************************************************
		$file_vars = pathinfo($filedir_target . $filename_source);
		$filename_source = $util_simad->clean_name_file($file_vars);
		$fullpath = $filedir_target . uniqid() . "_" . $filename_source;
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorData('Error interno del servidor al crear el archivo');
		}
		//***************************************************************************************************************
		//$mime_trust = array('application/pdf', 'application/x-pdf', 'application/x-bzpdf', 'application/x-gzpdf');
		//$file_valid = simad_util::CheckIsValidFormatFile($fullpath, $mime_trust);
		$file_valid = simad_util::validateBase64FilePdfOrWord($fullpath, $adju_b64);
		if ($file_valid['ok'] === false) {
			return responseErrorData("El formato del archivo no esta permitido, message: {$file_valid['error']}");
		}
		//***************************************************************************************************************
		$info_file = new SplFileInfo($fullpath);
		$mimetypes = array("pdf", "doc", "docx");
		$extension  = strtolower($info_file->getExtension());
		if (!in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorData('El formato ' . $extension . ' del archivo no es valido');
		}
		//***************************************************************************************************************
		if ($regional == null) {
			$regional = RegionalPeer::retrieveByPk($cuser_firma[0]->getUsuario()->getRegionalId());
		}
		//***************************************************************************************************************
		$params['regional_id'] = $regional->getPrimaryKey();
		$params['dependencia_id'] = $dependencia->getPrimaryKey();
		$params['estadoactoadministrativo_id'] = $estadoactoadministrativo;
		$params['estadodigitalizacion_id'] = 2;
		$params['periodo_id'] = date("Y");
		$params['asunto'] =  $asunto;
		$params['folios'] = $folios;
		$params['anexos'] = $anexos;
		$params['observaciones'] =  $observaciones;
		$params['firma_electronica'] = 0;
		$params['firmado_digital'] = $firma_digital;
		$params['archivo_digit'] = $adju_b64;
		$params['use_membrete'] = 0;
		$params['tipo_envio'] = $tipo_envio;
		//$params['tipo_masivo'] = 1;
		$params['expediente_id'] = $unidaddocumental_id;
		$params['plantillacom_id'] = $plantillacom_id;
		$params['subserie_id'] = $subserie->getPrimaryKey();
		$params['prioridadcom_id'] = $prioridadcom->getPrimaryKey();
		$params['fecha_aprobacion'] = date("Y-m-d G:i:s");
		$params['usuariodestino_id'] = $usuario_destinatario_id;
		$params['ucargodestino_id'] = $ucargodestino_id;
		$params['tipodocumental_id'] = $tipodocumental_id;
		$params['tipoprocesocom_id'] = 1;
		$params['marco_normativo'] = $marco_normativo;
		$params['id_suborigen'] = $suborigen;
		$params['esta_entregado'] = 0;
		$params['UrlFileWord'] = $fullpath;
		$params['IsCreateWord'] = true;
		$params['firma_desatendida'] = 1;
		//$params['contenidodocumental_id'] = $contenidodoc_id; PENDIENTE - TRANSFERENCIA
		$params['tipo_integracion'] = "APP_EXTERNA";
		$params['tipo_documental_cod'] = $tipodocumental_id;
		$params['tiposervicio_id'] = $tiposervicio_id;
		//***************************************************************************************************************
		$params['nuid_gestor'] = $nuid_gestor;
		//***************************************************************************************************************
		$fecha_resolucion = date("Y-m-d G:i:s");
		if (trim($fecha_resolucion)) {
			try {
				$date = new DateTime(trim($fecha_resolucion));
				$params['fecha_resolucion'] = $date->format('Y-m-d');
			} catch (Exception $ex) {
				$params['fecha_resolucion'] = date("Y-m-d");
			}
		}
		//***************************************************************************************************************
		$acto_administrativo = ActoAdministrativoPeer::addActoAdministrativo($params);
		//***************************************************************************************************************
		if ($acto_administrativo == null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA AddActoAdministrativo");
			$wslog->setMensaje("Error al generar el acto administrativo");
			$wslog->save();
			//***********************************************************************************************************
			return responseErrorData('Error al generar el acto administrativo');
		}
		//***************************************************************************************************************
		foreach ($laddnew_interesados as $newitem) {
			$newitem['USUARIO_ID'] = $usuario_creador->getUsuarioId();
			$interesado_id = InteresadosPeer::addNewInteresado($newitem);
			if ($interesado_id == null) {
				ActoAdministrativoPeer::deleteCascada($acto_administrativo->getPrimaryKey());
				return responseErrorData('Ocurrio un error interno creando un interesado en el SGDEA');
			} else {
				$comlist_interesados[] = $interesado_id;
			}
		}
		//***************************************************************************************************************
		$coll_intersadosPk = array();
		$email_interesado = array();
		foreach ($comlist_interesados as $interesado_id) {
			$interesado = InteresadosPeer::retrieveByPk($interesado_id);
			$isAddInteresadoCom = ActoadministraInteresadoPeer::addNewInteresadoByComId($acto_administrativo->getPrimaryKey(), $interesado->getPrimaryKey());
			if ($isAddInteresadoCom) {
				$coll_intersadosPk[] = $interesado->getPrimaryKey();
				if (!empty(trim($interesado->getEmail()))) {
					if (!in_array(trim($interesado->getEmail()), $email_interesado))
						$email_interesado[] = trim($interesado->getEmail());
				}
			}
		}
		//***************************************************************************************************************
		//proyecta el usuario autenticado
		$info_upryecta['pkcom_id'] = $acto_administrativo->getPrimaryKey();
		$info_upryecta['esta_asignada'] = 0;
		$info_upryecta['estadocom_id'] = $estadoactoadministrativo;
		$info_upryecta['usuario_id'] = $usuario_creador->getUsuarioId();
		$info_upryecta['rol_id'] = 1;
		$info_upryecta['cusuario_id'] = $cargo_usuario->getPrimaryKey();
		$info_upryecta['tipoprocesocom_id'] = null;
		$info_upryecta['fecha_aprobacion'] = date("Y-m-d G:i:s");
		$info_upryecta['fecha_lectura'] = date("Y-m-d G:i:s");
		$info_upryecta['esta_aprobado'] = 1;
		ActoAdministrativoPeer::addUserByActo($info_upryecta);
		//***************************************************************************************************************
		//radica
		$info_uradica['pkcom_id'] = $acto_administrativo->getPrimaryKey();
		$info_uradica['esta_asignada'] = 0;
		$info_uradica['estadocom_id'] = $estadoactoadministrativo;
		$info_uradica['usuario_id'] = $usuario_creador->getUsuarioId();
		$info_uradica['rol_id'] = 5;
		$info_uradica['cusuario_id'] = 	$cargo_usuario->getPrimaryKey();
		$info_uradica['tipoprocesocom_id'] = 1;
		$info_uradica['fecha_aprobacion'] = date("Y-m-d G:i:s");
		$info_uradica['fecha_lectura'] = date("Y-m-d G:i:s");
		$info_uradica['esta_aprobado'] = 1;
		ActoAdministrativoPeer::addUserByActo($info_uradica);
		//***************************************************************************************************************
		//gestor
		if ($cuser_gestor != null) {
			$info_upryecta['pkcom_id'] = $acto_administrativo->getPrimaryKey();
			$info_upryecta['esta_asignada'] = 0;
			$info_upryecta['estadocom_id'] = $estadoactoadministrativo;
			$info_upryecta['usuario_id'] = $cuser_gestor->getUsuarioId();
			$info_upryecta['rol_id'] = 4;
			$info_upryecta['cusuario_id'] = $cuser_gestor->getPrimaryKey();
			$info_upryecta['tipoprocesocom_id'] = 3;
			$info_upryecta['fecha_aprobacion'] = date("Y-m-d G:i:s");
			$info_upryecta['fecha_lectura'] = null;
			$info_upryecta['esta_aprobado'] = 1;
			ActoAdministrativoPeer::addUserByActo($info_upryecta);
		}
		//***************************************************************************************************************
		$radicar_automativo = true;
		$list_addfirmas = array();
		foreach ($cuser_firma as $cuser) {
			//firmantes
			$info_ufirma = null;
			//***********************************************************************************************************
			if (in_array($cuser->getPrimaryKey(), $list_addfirmas)) {
				continue;
			}
			//***********************************************************************************************************
			if (empty($cuser->getUsuario()->getFirmaDesatendida())) {
				$radicar_automativo = false;
			}
			//***********************************************************************************************************
			$info_ufirma['pkcom_id'] = $acto_administrativo->getPrimaryKey();
			$info_ufirma['esta_asignada'] = 0;
			$info_ufirma['estadocom_id'] = $estadoactoadministrativo;
			$info_ufirma['usuario_id'] = $cuser->getUsuarioId();
			$info_ufirma['rol_id'] = 2;
			$info_ufirma['cusuario_id'] = $cuser->getPrimaryKey();
			$info_ufirma['tipoprocesocom_id'] = 5;
			//***********************************************************************************************************
			if (empty($cuser->getUsuario()->getFirmaDesatendida())) {
				$info_ufirma['fecha_aprobacion'] = null;
				$info_ufirma['fecha_lectura'] = null;
				$info_ufirma['esta_aprobado'] = 0;
			} else {
				$info_ufirma['fecha_aprobacion'] = date("Y-m-d G:i:s");
				$info_ufirma['fecha_lectura'] = date("Y-m-d G:i:s");
				$info_ufirma['esta_aprobado'] = 1;
			}
			//***********************************************************************************************************
			ActoAdministrativoPeer::addUserByActo($info_ufirma);
			$list_addfirmas[] = $cuser->getPrimaryKey();
		}
		//***************************************************************************************************************
		if (!empty(trim($nuid_destinatario)) && count($comlist_interesados) <= 0) {
			//destinatario
			$info_udestino['pkcom_id'] = $acto_administrativo->getPrimaryKey();
			$info_udestino['esta_asignada'] = 1;
			$info_udestino['estadocom_id'] = 6;
			$info_udestino['usuario_id'] = $usuario_destinatario_id;
			$info_udestino['rol_id'] = 6;
			$info_udestino['cusuario_id'] = $ucargodestino_id;
			$info_udestino['tipoprocesocom_id'] = null;
			$info_udestino['fecha_aprobacion'] = null;
			$info_udestino['fecha_lectura'] = null;
			$info_udestino['esta_aprobado'] = null;
			ActoAdministrativoPeer::addUserByActo($info_udestino);
			//***********************************************************************************************************
			if ($cargo_usuario_destinatario != null && !empty(trim($cargo_usuario_destinatario->getUsuario()->getEmail()))) {
				$email_interesado[] = trim($cargo_usuario_destinatario->getUsuario()->getEmail());
			}
		}
		//***************************************************************************************************************
		if ($radicar_automativo) {
			$estado_documento = 6;
			$acto_administrativo->getRadicadoFormat();
			$acto_administrativo->setEstadoactoadministrativoId($estado_documento);
			$acto_administrativo->save();
		}
		//***************************************************************************************************************
		if ($acto_administrativo->getEstadoactoadministrativoId() !== 6 && $radicar_automativo) {
			//1. elimina usuarios del acto administrativo
			//2. elimina interesados del acto administrativo
			//3. elimina servicios del acto administrativo(falta revisar, hay tablas relacionas al servicio)
			//4. elimina el acto administrativo
			ActoAdministrativoPeer::deleteCascada($acto_administrativo->getPrimaryKey());
			return responseErrorData('Error al generar el acto administrativo');
		}
		//***************************************************************************************************************
		AuditLogPeer::guardarAuditoriaLite(
			ActoAdministrativoPeer::getOMClass(),
			new ActoAdministrativo(),
			$acto_administrativo,
			ModulesEnable::ActosAdministrativos,
			$acto_administrativo->getRadicadoCompuesto(),
			$usuario_creador->getUsuarioId()
		);
		//***************************************************************************************************************
		ActoAdministrativoPeer::updateEstadosObj($acto_administrativo->getPrimaryKey(), $estado_documento);
		ActoAdministrativoPeer::updateAproObjAllProcess($acto_administrativo->getPrimaryKey(), array(2, 3, 4));
		//***************************************************************************************************************
		if (!empty($unidaddocumental_id) && !empty($tipodocumental_id) && $radicar_automativo) {
			$origentrans_id = 8;
			$lexptransfer[] = TransferenciaPeer::addAutoTransfAndContenido($unidaddocumental_id, $tipodocumental_id, $acto_administrativo->getPrimaryKey(), $origentrans_id, $usuario_creador->getPrimaryKey());  // antes... $usuario_origen->getPrimaryKey()
			//***********************************************************************************************************
			if (in_array($acto_administrativo->getEstadoactoadministrativoId(), array(6, 7, 9)) && ($acto_administrativo->getFirmadoDigital() == 0)) {
				$response_firma = $acto_administrativo->signDocumentProcess();
				$msg_firma[] = isset($response_firma['message']) ? trim($response_firma['message']) : "Por favor verifique que el documento fue firmado correctamente";
			}
		}
		//***************************************************************************************************************
		//SOLUCITUD SERVICIO
		$msgintegracion = "";
		if ($tipo_servicio != null && $cargo_usuario_destinatario == null && count($coll_intersadosPk) > 0) {
			$response_servicio = $acto_administrativo->addServicioByCom($usuario_creador->getPrimaryKey(), $tiposervicio_id, $coll_intersadosPk);
			$servicio = $response_servicio['isError'] == false ? $response_servicio['object'] : null;
			//***********************************************************************************************************
			if ($servicio != null) {
				$response_svc = $servicio->initIntegraciones();
				if ($response_svc['isError'] == true) {
					$msgintegracion = sprintf('Error integrando servicio. %s', $response_svc['message']);
				}
			} else {
				$msgintegracion = sprintf('Error radicando servicio. %s', $response_servicio['message']);
			}
		} else if ($cargo_usuario_destinatario != null) {
			$response_email = $acto_administrativo->sendMailAprob($cargo_usuario_destinatario->getUsuario());
			if ($response_email === true) {
				$msgintegracion = sprintf('Notificacion enviada al destinatario. %s', $cargo_usuario_destinatario->getUsuario()->getEmail());
			} else if ($response_email === false) {
				$msgintegracion = sprintf('Error enviando notificacion al destinatario. %s', $cargo_usuario_destinatario->getUsuario()->getEmail());
			} else {
				$msgintegracion = sprintf('Error enviando notificacion al destinatario. %s', $response_email);
			}
		}
		//***************************************************************************************************************
		$msg_info[] = sprintf('Acto administrativo creado con radicado %s', $acto_administrativo->getRadicadoCompuesto());
		if (!empty(trim($msgintegracion))) {
			$msg_info[] = $msgintegracion;
		}
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $acto_administrativo->getPrimaryKey(),
			'RADICADO' => $acto_administrativo->getRadicadoCompuesto(),
			'FECHA_CREACION' => $acto_administrativo->getFechaCreacion(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSGERROR' => implode(", ", $msg_info)
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => $ex->getMessage()
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSGERROR' => $ex->getMessage()
		);
	}

	//return array('ComRecibidaInfoEnt' =>$response_data);
	return $response_data;
}

function AddServicioComPublic($EntSecurity = array(), $EntServicioCom = array(), $EntInteresadoOfList = array(), $EntRemitente = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$servicio_id = 0;
	$simad_util = new simad_util();
	//*******************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//*******************************************************************************************************************
	try {
		$filedir_target = sfConfig::get("sf_web_dir") . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
		//$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'request_timed.log';
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorAttachment($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddServicioComPublic", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getUsuarioId());
		//***************************************************************************************************************
		$params = array();
		$periodo_id = isset($EntServicioCom['PERIODO_ID']) ? trim($EntServicioCom['PERIODO_ID']) : date("Y");
		$adju_b64 = isset($EntServicioCom['ARCHIVO_DIGIT']) ? trim($EntServicioCom['ARCHIVO_DIGIT']) : null;
		$filename_source = isset($EntServicioCom['ARCHIVO_NOMBRE']) ? (trim($EntServicioCom['ARCHIVO_NOMBRE'])) : null;
		$tiposervicio_text = isset($EntServicioCom['TIPO_SERVICIO']) ? trim($EntServicioCom['TIPO_SERVICIO']) : null;
		$uiduser_origen = isset($EntServicioCom['NUID_RADICA']) ? trim($EntServicioCom['NUID_RADICA']) : $usuario_creador->getCedula();
		$detalle = isset($EntServicioCom['DETALLE']) ? trim($EntServicioCom['DETALLE']) : null;
		$email_destino = isset($EntServicioCom['EMAIL_DESTINO']) ? trim($EntServicioCom['EMAIL_DESTINO']) : null;
		$radicado_com = isset($EntServicioCom['RADICADO_COM']) ? trim($EntServicioCom['RADICADO_COM']) : null;
		$radicado_origen = isset($EntServicioCom['RADICADO_ORIGEN']) ? trim($EntServicioCom['RADICADO_ORIGEN']) : null;
		$tipo_com = isset($EntServicioCom['TIPO_COM']) ? trim($EntServicioCom['TIPO_COM']) : null;
		$prioridadservicio_text = isset($EntServicioCom['PRIORIDAD_SERVICIO']) ? trim($EntServicioCom['PRIORIDAD_SERVICIO']) : null;
		$marconormativo_text = isset($EntServicioCom['MARCO_NORMATIVO']) ? trim($EntServicioCom['MARCO_NORMATIVO']) : null;
		$documentoorigen_text = isset($EntServicioCom['DOCUMENTO_ORIGEN']) ? trim($EntServicioCom['DOCUMENTO_ORIGEN']) : null;
		//***************************************************************************************************************
		$tiposervicio_id = TipoServicioPeer::getTipoServicioByName($tiposervicio_text);
		$prioridadservicio_id = PrioridadSolicitudServicioPeer::getPrioridadServicioByName($prioridadservicio_text);
		$marco_normativo = MarcoNormativoPeer::getMarcoNormativoByName($marconormativo_text);
		$marconormativo_id = $marco_normativo != null ? $marco_normativo->getPrimaryKey() : null;
		$procesodoc_origen = ProcesodocOrigenPeer::getProcesoDocOrigenByName($documentoorigen_text);
		$procesodocorigen_id = $procesodoc_origen != null ? $procesodoc_origen->getPrimaryKey() : null;
		$suborigen = $procesodoc_origen != null ?  $procesodoc_origen->getSuborigen() : 0;
		//***************************************************************************************************************
		if (!empty($uiduser_origen)) {
			$cuser_origen = CargoUsuarioPeer::getCargoUsuarioByNuidUser($uiduser_origen, true);
		} else {
			$cuser_origen = CargoUsuarioPeer::getCargoUsuarioByNuidUser($usuario_creador->getCedula(), true);
		}
		//***************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		foreach ($EntInteresadoOfList as $info_list) {
			if (is_array($info_list)) {
				if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
					if (!trim($info_list['NUMERO_IDENTIFICACION'])) {
						return responseErrorAttachment('El numero de identificación del interesado no es valido');
						exit;
					}
					//********************************************************************************************************
					$interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'], $info_list['PRIMER_APELLIDO'], $info_list['NUMERO_IDENTIFICACION']);
					if ($interesado_id != null) {
						$comlist_interesados[] = $interesado_id;
					} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
						$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
						if ($tipouid_id == null) {
							return responseErrorAttachment('El tipo de identificación del interesado no es un valor valido');
						}
						//********************************************************************************************************
						$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
						$optional_fileds = array('TIPO_GENERO' => true, 'EMAIL' => true);
						$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list, $optional_fileds);
						if ($isValidIntInfo['IsValid'] == true) {
							$laddnew_interesados[] = $info_list;
						} else {
							return responseErrorAttachment($isValidIntInfo['MsgError']);
						}
					} else {
						$info_list['TIPO_IDENTIFICACION'] = 5;
						$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
						if ($isValidIntInfo['IsValid'] == true) {
							$laddnew_interesados[] = $info_list;
						} else {
							return responseErrorAttachment($isValidIntInfo['MsgError']);
						}
					}
				} else {
					return responseErrorAttachment('El nombre o numero de identificación del interesado no es valido');
				}
			}
		}
		//***************************************************************************************************************
		if (count($comlist_interesados) <= 0 && count($laddnew_interesados) <= 0) {
			return responseErrorAttachment('Debe enviar como minimo un interesado');
		}
		//***************************************************************************************************************
		//$cuser_origen = $usuario_creador != null ? $usuario_creador : CargoUsuarioPeer::getCargoUsuarioByNuidUser($uiduser_origen,true);
		$regional_origen = isset($EntServicioCom['PUNTO_RADICACION']) ? RegionalPeer::getRegionalByName(trim($EntServicioCom['PUNTO_RADICACION'])) : null;
		//***************************************************************************************************************
		if ($detalle == null) {
			return responseErrorAttachment('Debe enviar el detalle del servicio');
		}
		if ($adju_b64 == null) {
			return responseErrorAttachment('Debe enviar el documento digital del servicio');
		}
		if ($tiposervicio_id == null) {
			return responseErrorAttachment('El tipo de servicio no se encontro en el SGDEA');
		}
		if ($prioridadservicio_id == null) {
			return responseErrorAttachment('La prioridad del servicio no se encontro en el SGDEA');
		}
		if ($filename_source == null) {
			return responseErrorAttachment('El nombre del archivo es un campo obligatorio');
		}
		//***************************************************************************************************************
		$file_vars = pathinfo($filedir_target . $filename_source);
		$filename_source = $simad_util->clean_name_file($file_vars);
		//***************************************************************************************************************
		$fullpath = $filedir_target . $filename_source;
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorAttachment('Error interno del servidor al crear el archivo');
		}
		//***************************************************************************************************************
		$mime_trust = array('application/pdf', 'application/x-pdf', 'application/x-bzpdf', 'application/x-gzpdf');
		$file_valid = simad_util::CheckIsValidFormatFile($fullpath, $mime_trust);
		if (!$file_valid) {
			return responseErrorAttachment('El formato del archivo no esta permitido');
		}
		//***************************************************************************************************************
		$info_file = new SplFileInfo($fullpath);
		//$mimetypes = array("pdf","doc", "docx");
		$mimetypes = array("pdf");
		$extension  = strtolower($info_file->getExtension());
		if (!in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorAttachment('El formato ' . $extension . ' del archivo no es valido');
		}
		//***************************************************************************************************************
		if ($regional_origen == null) {
			$regional_origen = RegionalPeer::retrieveByPk($usuario_creador->getRegionalId());
		}
		//***************************************************************************************************************
		$addRemitente = false;
		$directorio_externo = null;
		if (count($EntRemitente)) {
			$nuid_remitente = isset($EntComEnviada['NUID']) ? trim($EntComEnviada['NUID']) : null;
			if ($nuid_remitente != null) {
				$directorio_externo = DirectorioExternoPeer::existsDirectorioExterno($EntRemitente);
			} else {
				$addRemitente = false;
			}
		}
		//***************************************************************************************************************
		$directorioexterno_id = !empty($directorio_externo) ? $directorio_externo->getPrimaryKey() : null;
		//***************************************************************************************************************
		$params['regional_id'] = $regional_origen->getPrimaryKey();
		$params['usuario_id'] = $cuser_origen->getUsuarioId();
		$params['periodo_id'] = $periodo_id;
		$params['detalle'] = $detalle;
		$params['folios'] = isset($EntServicioCom['FOLIOS']) ? trim($EntServicioCom['FOLIOS']) : 0;
		$params['archivo_digit'] = $adju_b64;
		$params['tiposervicio_id'] = $tiposervicio_id;
		$params['marconormativo_id'] = $marconormativo_id;
		$params['procesodocorigen_id'] = $procesodocorigen_id;
		$params['prioridadsolicitudservicio_id'] = $prioridadservicio_id != null ? $prioridadservicio_id : 1;
		$params['numero_resolucion'] = isset($EntServicioCom['NUMERO_RESOLUCION']) ? trim($EntServicioCom['NUMERO_RESOLUCION']) : null;
		$params['directorioexterno_id'] = $directorioexterno_id;
		$params['email_destino'] = $email_destino;
		$params['radicado_origen'] = $radicado_origen;
		$params['coll_interesados'] = null;
		$params['suborigen'] = $suborigen;
		$params['full_path'] = $fullpath;
		//***************************************************************************************************************
		$pkconsecutivo_id = null;
		if ($tipo_com == 1) {
			$com_enviada = ComEnviadaPeer::getComObjectByRadicado($radicado_com, $periodo_id);
			$pkconsecutivo_id = $com_enviada != null ? $com_enviada->getPrimaryKey() : null;
		} elseif ($tipo_com == 2) {
			$com_recibida = ComRecibidaPeer::getComObjectByRadicado($radicado_com, $periodo_id);
			$pkconsecutivo_id = $com_recibida != null ? $com_recibida->getPrimaryKey() : null;
		} elseif ($tipo_com == 3) {
			$com_interna = ComInternaPeer::getComObjectByRadicado($radicado_com, $periodo_id);
			$pkconsecutivo_id = $com_interna != null ? $com_interna->getPrimaryKey() : null;
		}
		//***************************************************************************************************************
		$params['pkconsecutivo_id'] = $pkconsecutivo_id;
		//***************************************************************************************************************
		if (isset($EntServicioCom['FECHA_RESOLUCION'])) {
			$fecha_resolucion = trim($EntServicioCom['FECHA_RESOLUCION']);
			try {
				if (strpos($fecha_resolucion, "/") !== false) {
					$date = str_replace('/', '-', $fecha_resolucion);
					$fecha_resolucion = date('Y-m-d', strtotime($date));
				}

				$date = new DateTime($fecha_resolucion);
				$params['fecha_resolucion'] = $date->format('Y-m-d');
			} catch (Exception $ex) {
				$params['fecha_resolucion'] = null;
				return responseErrorAttachment('El formato de la fecha resolución no es valido ' . $ex->getMessage());
			}
		}
		//***************************************************************************************************************
		$response = ServicioPeer::createServicioByCom($params, $pkconsecutivo_id, null);
		//***************************************************************************************************************
		if ($response['isError'] == true) {
			return responseErrorAttachment(!empty($response['message']) ? $response['message'] : 'Error interno del servidor del SGDEA');
		}
		//***************************************************************************************************************
		//$servicio_com = new Servicio();
		$servicio_com = (object)$response['object'];
		$servicio_id = $servicio_com->getPrimaryKey();
		//***************************************************************************************************************
		foreach ($laddnew_interesados as $newitem) {
			$newitem['USUARIO_ID'] = $servicio_com->getUsuarioId();
			$interesado_id = InteresadosPeer::addNewInteresado($newitem);
			if ($interesado_id == null) {
				$servicio_com->delete();
				return responseErrorAttachment('Ocurrio un error al crear el interesado en el SGDEA');
			} else {
				$comlist_interesados[] = $interesado_id;
			}
		}
		//***************************************************************************************************************
		foreach ($comlist_interesados as $interesado_item) {
			ServicioInteresadosPeer::addNewInteresadoByComId($servicio_com->getPrimaryKey(), $interesado_item);
		}
		//***************************************************************************************************************
		$response_anexo = $servicio_com->addNewAnexo($fullpath);
		if ($response_anexo == null) {
			ServicioPeer::deleteCascada($servicio_com->getPrimaryKey());
			return responseErrorAttachment('Ocurrio un error al adjuntar el archivo, el servicio no se radico');
		}
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $servicio_com->getRadicado());
			$wslog->setMensaje("Ejecuta Exitoso Genera Radicado" . (empty($msgintegracion) ? "" : ", " . $msgintegracion));
			$wslog->save();
		}
		//***************************************************************************************************************
		$msgintegracion = "";
		if ($servicio_com->getTipoServicio()->getInitIntegracion()) {
			try {
				$coll_interesados = ServicioPeer::getListIntersadosByComId($servicio_com->getPrimaryKey());
				//*******************************************************************************************************
				$simadSoap = new WsSimadUariv();
				$response_acto = $simadSoap->loadWsRadActoAdmByNotCom($params, $coll_interesados);
				$msgintegracion = $response_acto['message'];
			} catch (\Throwable $th) {
				$msgintegracion = $th->getMessage();
			}
		}
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $servicio_com->getRadicado());
			$wslog->setMensaje("Ejecuta Exitoso Genera Radicado" . (empty($msgintegracion) ? "" : ", " . $msgintegracion));
			$wslog->save();
		}
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $servicio_com->getPrimaryKey(),
			'RADICADO' => $servicio_com->getRadicado(),
			'FECHA_CREACION' => $servicio_com->getFechaCreacion(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSG_INFO' => "Servicio radicado exitosamente" . (empty($msgintegracion) ? "" : ", " . $msgintegracion)
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => 'El servicio no se radico, SoapFault => ' . $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		ServicioPeer::deleteCascada($servicio_id);
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => 'El servicio no se radico, SoapFault => ' . $ex->getMessage()
		);
	} catch (Exception $ex) {
		ServicioPeer::deleteCascada($servicio_id);
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => 'El servicio no se radico, Exception => ' . $ex->getMessage()
		);
	}
	//*******************************************************************************************************************
	return $response_data;
}

function AddAttachDocumento($EntSecurity = array(), $EntAttachDocument = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$info_response = "";
	$isErrorWs = false;
	//*******************************************************************************************************************
	//file_put_contents(sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."AddRadicadoSalidaOferta.log", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	$simad_util = new simad_util();
	//*******************************************************************************************************************
	try {
		//$filedir_target = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(29)->getValortexto() . 'uploads');
		$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorAttachment($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("AddAttachDocumento", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getPrimaryKey());
		//***************************************************************************************************************
		$params = array();
		$adju_b64 = isset($EntAttachDocument['ARCHIVO_DATA']) ? trim($EntAttachDocument['ARCHIVO_DATA']) : null;
		$filename = isset($EntAttachDocument['ARCHIVO_NOMBRE']) ? utf8_encode(trim($EntAttachDocument['ARCHIVO_NOMBRE'])) : null;
		$radicado_com = isset($EntAttachDocument['RADICADO_COMUNICACION']) ? trim($EntAttachDocument['RADICADO_COMUNICACION']) : null;
		$consecutivo_com = isset($EntAttachDocument['CONSECUTIVO_COMUNICACION']) ? trim($EntAttachDocument['CONSECUTIVO_COMUNICACION']) : null;
		$tipo_consecutivo = isset($EntAttachDocument['TIPO_CONSECUTIVO']) ? trim($EntAttachDocument['TIPO_CONSECUTIVO']) : null;
		$notifica_radicado = isset($EntAttachDocument['NOTIFICA_RADICADO']) ? trim($EntAttachDocument['NOTIFICA_RADICADO']) : false;
		//***************************************************************************************************************
		$file_parts = pathinfo($filename);
		if (empty($file_parts['extension'])) {
			return responseErrorAttachment('El nombre del archivo no es valido');
		}
		//***************************************************************************************************************
		if (empty($radicado_com) && empty($consecutivo_com)) {
			return responseErrorAttachment('El numero de radicado o el consucutivo de la comunicación es obligatorio');
		}
		if (empty($filename)) {
			return responseErrorAttachment('El nombre del archivo es un campo obligatorio');
		}
		if (empty($adju_b64)) {
			return responseErrorAttachment('El documento adjunto no fue enviado');
		}
		if (empty($tipo_consecutivo)) {
			return responseErrorAttachment('El tipo de consecutivo es obligatorio');
		}
		//***************************************************************************************************************
		$file_vars = pathinfo($filedir_target . $filename);
		$filename_source = $simad_util->clean_name_file($file_vars);
		$fullpath = $filedir_target . uniqid() . "_" . $filename_source;
		$isCreate = simad_util::getConvertB64ToFile($adju_b64, $fullpath);
		//***************************************************************************************************************
		if (!$isCreate) {
			return responseErrorAttachment('Error interno del servidor o el archivo no es valido, no se realizó la acción solicitada');
		}
		//***************************************************************************************************************
		$info_file = new SplFileInfo($fullpath);
		//$mimetypes = array("pdf","doc", "docx");
		$mimetypes = array("exe", "sys", "ini", "com", "dll", "jar", "bat", "cmd", "vbs", "tmp", "php", "py", "inf", "lnk", "csf", "msi", "msp", "gadget", "ps1", "ps1xml", "ps2", "ps2xml", "psc1", "psc2");
		$extension  = strtolower($info_file->getExtension());
		if (in_array($extension, $mimetypes)) {
			unlink($fullpath);
			return responseErrorAttachment('El tipo de archivo ' . $extension . ' no esta permitido');
		}
		//***************************************************************************************************************
		$params['archivo_digit'] = $adju_b64;
		//$params['filename'] = $filename;
		$params['filename'] = $filename_source;
		$params['fullpath'] = $info_file->getRealPath();
		$params['radicado_com'] = $radicado_com;
		$params['consecutivo_com'] = $consecutivo_com;
		$list_files = array($params['fullpath']);
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $radicado_com);
			$wslog->setMensaje("Ejecuta Exitoso Adjunta Documentos");
			$wslog->save();
		}
		//***************************************************************************************************************
		$isErrorCom = false;
		$consecutivo_id = 0;
		$radicado = "";
		switch ($tipo_consecutivo) {
			case 1: //enviadas
				$com_enviada =  ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_com, $consecutivo_com);
				if ($com_enviada == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				if ($notifica_radicado && ($com_enviada->getServicioId())) {
					foreach ($list_files as $ifile) {
						$response_attach = $com_enviada->attachDocumentToServiceCom($ifile, $usuario_creador->getPrimaryKey());
					}
				} elseif (($com_enviada->getTipoEnvio() == 4) && ($com_enviada->getServicioId())) {
					foreach ($list_files as $ifile) {
						$response_attach = $com_enviada->attachDocumentToServiceCom($ifile, $usuario_creador->getPrimaryKey());
					}
				} else {
					$response_attach = $com_enviada->addNewAttachDocument($list_files);
				}
				//*******************************************************************************************************
				$radicado = $com_enviada->getRadicado();
				$consecutivo_id = $com_enviada->getPrimaryKey();
				break;
			case 2: //recibidas
				$com_recibida =  ComRecibidaPeer::getObjectComByRadicadoOrId($radicado_com, $consecutivo_com);
				if ($com_recibida == null) {
					$isErrorCom = true;
					break;
				}
				$response_attach = $com_recibida->addNewAttachDocument($list_files);
				$radicado = $com_recibida->getRadicado();
				$consecutivo_id = $com_recibida->getPrimaryKey();
				break;
			case 3: //interna
				$com_interna =  ComInternaPeer::getObjectComByRadicadoOrId($radicado_com, $consecutivo_com);
				if ($com_interna == null) {
					$isErrorCom = true;
					break;
				}
				$response_attach = $com_interna->addNewAttachDocument($list_files);
				$radicado = $com_interna->getRadicado();
				$consecutivo_id = $com_interna->getPrimaryKey();
				break;
			case 17: //acto administrativo
				$acto_administrativo =  ActoAdministrativoPeer::getObjectComByRadicadoOrId($radicado_com, $consecutivo_com);
				if ($acto_administrativo == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				/*
					if ($notifica_radicado && ($acto_administrativo->getServicioId())) {
						foreach ($list_files as $ifile) {
							$response_attach = $acto_administrativo->attachDocumentToServiceCom($ifile, $usuario_creador->getPrimaryKey());
						}
					} elseif (($acto_administrativo>getTipoEnvio() == 4) && ($acto_administrativo->getServicioId())) {
						foreach ($list_files as $ifile) {
							$response_attach = $acto_administrativo->attachDocumentToServiceCom($ifile, $usuario_creador->getPrimaryKey());
						}
					} else {
						$response_attach = $acto_administrativo->addNewAttachDocument($list_files);
					}
					*/
				//*******************************************************************************************************
				$response_attach = $acto_administrativo->addNewAttachDocument($list_files);
				$radicado = $acto_administrativo->getRadicadoCompuesto();
				$consecutivo_id = $acto_administrativo->getPrimaryKey();
				break;
			default:
				$isErrorCom = true;
				break;
		}
		//***************************************************************************************************************
		if ($isErrorCom) {
			return responseErrorAttachment('El radicado no se encontro en el sgdea(radicado => ' . $radicado_com . '; consecutivo_com => ' . $consecutivo_com . '), verifique los datos');
		}
		//***************************************************************************************************************
		if ($response_attach['isError'] == true) {
			$isErrorWs = true;
			foreach ($response_attach['list_state'] as $breponse) {
				if ($breponse['filename'] == $filename) {
					$info_response = $breponse['info'];
					break;
				}
			}
		} else {
			$info_response = 'El archivo se adjunto correctamente a la comunicación con radicado ' . $radicado_com;
		}
		//***************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $consecutivo_id,
			'RADICADO' => $radicado,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  $isErrorWs,
			'MSG_INFO' => $info_response
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (Exception $ex) {
		//$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'AddAttachRadicadoSalida.log';
		//simad_util::writetolog($logname,$ex->getMessage());
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'RADICADO' => null,
			'FECHA_CREACION' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	return $response_data;
}

function ListAttachDocumento($EntSecurity = array(), $EntViewAttachDoc = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$info_response = "";
	$isErrorWs = false;
	//*******************************************************************************************************************
	//file_put_contents(sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."AddRadicadoSalidaOferta.log", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	//*******************************************************************************************************************
	try {
		$target_tmp = sfConfig::get("sf_web_dir") . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
		//***************************************************************************************************************
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return responseErrorAttachment($usuario_ws['message']);
		}
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("ListAttachDocumento", $infoxml, null, 1, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuario_creador->getPrimaryKey());
		//***************************************************************************************************************
		$list_attach =  array();
		$radicado_com = isset($EntViewAttachDoc['RADICADO_COMUNICACION']) ? trim($EntViewAttachDoc['RADICADO_COMUNICACION']) : null;
		$tipo_consecutivo = isset($EntViewAttachDoc['TIPO_CONSECUTIVO']) ? trim($EntViewAttachDoc['TIPO_CONSECUTIVO']) : null;
		//***************************************************************************************************************
		if (empty($radicado_com)) {
			return responseErrorAttachment('El numero de radicado de la comunicación es obligatorio');
		}
		if (empty($tipo_consecutivo)) {
			return responseErrorAttachment('El tipo de consecutivo es obligatorio');
		}
		//***************************************************************************************************************
		$isErrorCom = false;
		$consecutivo_id = 0;
		$radicado = "";
		switch ($tipo_consecutivo) {
			case 1: //enviadas
				$com_enviada =  ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_com);
				if ($com_enviada == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				$list_attach = $com_enviada->getListDigitDocument();
				break;
			case 2: //recibidas
				$com_recibida =  ComRecibidaPeer::getObjectComByRadicadoOrId($radicado_com);
				if ($com_recibida == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				$list_attach = $com_recibida->getListDigitDocument();
				break;
			case 3: //interna
				$com_interna =  ComInternaPeer::getObjectComByRadicadoOrId($radicado_com);
				if ($com_interna == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				$list_attach = $com_interna->getListDigitDocument();
				break;
			case 17: //acto administrativo
				$acto_administrativo = ActoAdministrativoPeer::getObjectComByRadicadoOrId($radicado_com);
				if ($acto_administrativo == null) {
					$isErrorCom = true;
					break;
				}
				//*******************************************************************************************************
				$list_attach = $acto_administrativo->getListDigitDocument();
				break;
			default:
				$isErrorCom = true;
				break;
		}
		//***************************************************************************************************************
		if ($isErrorCom) {
			return responseErrorAttachment('No se encontro en el SGDEA el radicado ' . $radicado_com . ', verifique los datos');
		}
		//***************************************************************************************************************
		if ($wslog != null) {
			$wslog->setTipoOperacion("Consumen Servicio web SGDEA => " . $radicado_com);
			$wslog->setMensaje("Ejecuta Exitoso Genera Listado Adjuntos");
			$wslog->save();
		}
		//***************************************************************************************************************
		if (count($list_attach)) {
			$response_data = array(
				'LIST_DOCUMENTOS' => $list_attach,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => 'Lista de documentos adjuntos al radicado'
			);
		} else {
			$response_data = array(
				'LIST_DOCUMENTOS' => null,
				'URL_ACCESO' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => 'El radicado no contiene documentos adjuntos'
			);
		}
		//***************************************************************************************************************
	} catch (PropelException $ex) {
		$response_data = array(
			'LIST_DOCUMENTOS' => null,
			'URL_ACCESO' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'LIST_DOCUMENTOS' => null,
			'URL_ACCESO' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (Exception $ex) {
		$response_data = array(
			'LIST_DOCUMENTOS' => null,
			'URL_ACCESO' => null,
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	return $response_data;
}

function GetMetadataInfoCom($EntSecurity = array(), $ComSearchInfo = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//*******************************************************************************************************************
	//file_put_contents(sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."AddRadicadoSalidaOferta.log", file_get_contents("php://input"));
	$infoxml = file_get_contents("php://input");
	//*******************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array('FECHA_TRANSACCION' => $fecha_transaccion, 'ISERROR' =>  true, 'MSG_INFO' => $usuario_ws['message']);
		}
		//***************************************************************************************************************
		$com_infodata =  array();
		$cons_entrada = isset($ComSearchInfo['CONSECUTIVO_ENTRADA']) ? trim($ComSearchInfo['CONSECUTIVO_ENTRADA']) : null;
		$cons_salida = isset($ComSearchInfo['CONSECUTIVO_SALIDA']) ? trim($ComSearchInfo['CONSECUTIVO_SALIDA']) : null;
		$radicado_entrada = isset($ComSearchInfo['RADICADO_ENTRADA']) ? trim($ComSearchInfo['RADICADO_ENTRADA']) : null;
		$radicado_salida = isset($ComSearchInfo['RADICADO_SALIDA']) ? trim($ComSearchInfo['RADICADO_SALIDA']) : null;
		$radicado_com = $radicado_entrada != null ? $radicado_entrada : $radicado_salida;
		//***************************************************************************************************************
		$usuario_creador = $usuario_ws['object'];
		$wslog = WebserviceLogPeer::addLogWs("GetMetadataInfoCom", $infoxml, null, 1, "Consumen Servicio web SGDEA => " . ($radicado_com), "Ejecuta Exitoso => " . ($radicado_com), $usuario_creador->getPrimaryKey());
		//***************************************************************************************************************
		if (empty($cons_entrada) && empty($cons_salida)) {
			return responseErrorAttachment('Debe enviar por lo menos un consecutivo para consultar');
		}
		//***************************************************************************************************************
		$isErrorCom = false;
		$radicado_com = "";
		if (!empty($cons_entrada)) {
			$com_recibida =  ComRecibidaPeer::getObjectComByRadAndPkId($radicado_entrada, $cons_entrada);
			if ($com_recibida == null) {
				$isErrorCom = true;
			} else {
				$com_infodata = $com_recibida->getBasicInfoMetadata();
			}
		} else {
			$com_enviada =  ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_salida, $cons_salida);
			if ($com_enviada == null) {
				$isErrorCom = true;
				return;
			} else {
				$com_infodata = $com_enviada->getBasicInfoMetadata();
			}
		}
		//***************************************************************************************************************
		if ($isErrorCom) {
			return responseErrorAttachment('No se encontro en el SGDEA el radicado ' . $radicado_com . ', verifique los datos');
		}
		//***************************************************************************************************************
		if (count($com_infodata)) {
			$response_data = array_merge($com_infodata, array(
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => 'Informacion del radicado'
			));
		} else {
			$response_data = array(
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => 'El radicado no contiene documentos adjuntos'
			);
		}
		//***************************************************************************************************************

	} catch (SoapFault $ex) {
		$response_data = array(
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (Exception $ex) {
		$response_data = array(
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	return $response_data;
}

function CreateNewDocExpediente($EntSecurity = array(), $ArcAddDocumento = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$simad_util = new simad_util();
	//***********************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//***********************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array(
				'CONSECUTIVO_ID' => null,
				'NUMERO_EXPEDIENTE' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => $usuario_ws['message']
			);
		}
		//*******************************************************************************************************************
		$numero_expediente = isset($ArcAddDocumento['NUMERO_EXPEDIENTE']) ? trim($ArcAddDocumento['NUMERO_EXPEDIENTE']) : null;
		$verif_udocumental = isset($ArcAddDocumento['VERIF_UDOCUMENTAL']) ? trim($ArcAddDocumento['VERIF_UDOCUMENTAL']) : null;
		$tipodoc_codigo = isset($ArcAddDocumento['TIPODOC_CODIGO']) ? trim($ArcAddDocumento['TIPODOC_CODIGO']) : null;
		$estado_documento = isset($ArcAddDocumento['ESTADO_DOCUMENTO']) ? trim($ArcAddDocumento['ESTADO_DOCUMENTO']) : null;
		$nuip_creador = isset($ArcAddDocumento['NUIP_CREADOR']) ? trim($ArcAddDocumento['NUIP_CREADOR']) : null;
		$firma_estampa = isset($ArcAddDocumento['FIRMA_ESTAMPA']) ? trim($ArcAddDocumento['FIRMA_ESTAMPA']) : null;
		$soporte_documento = isset($ArcAddDocumento['SOPORTE_DOCUMENTO']) ? trim($ArcAddDocumento['SOPORTE_DOCUMENTO']) : null;
		$origen_documento = isset($ArcAddDocumento['ORIGEN_DOCUMENTO']) ? trim($ArcAddDocumento['ORIGEN_DOCUMENTO']) : null;
		$descripcion = isset($ArcAddDocumento['DESCRIPCION']) ? trim($ArcAddDocumento['DESCRIPCION']) : null;
		$archivo_anexo = isset($ArcAddDocumento['ARCHIVO_ANEXO']) ? trim($ArcAddDocumento['ARCHIVO_ANEXO']) : null;
		$archivo_nombre = isset($ArcAddDocumento['ARCHIVO_NOMBRE']) ? trim($ArcAddDocumento['ARCHIVO_NOMBRE']) : null;
		$folios = isset($ArcAddDocumento['FOLIOS']) ? trim($ArcAddDocumento['FOLIOS']) : null;
		$fecha_documento = isset($ArcAddDocumento['FECHA_DOCUMENTO']) ? trim($ArcAddDocumento['FECHA_DOCUMENTO']) : null;
		$orden_contenido = isset($ArcAddDocumento['ORDEN_CONTENIDO']) ? trim($ArcAddDocumento['ORDEN_CONTENIDO']) : null;
		//*******************************************************************************************************************
		$wslog = WebserviceLogPeer::addLogWs("CreateNewDocExpediente", $infoxml, null, 1, "Consumen Servicio web SGDEA => " . ($numero_expediente), "Ejecuta Exitoso => " . ($numero_expediente), $usuario_ws['object']->getPrimaryKey());
		//*******************************************************************************************************************
		if (empty($numero_expediente)) {
			return responseErrorDataExp('El campo numero expediente es obligatorio');
		}
		if (empty($descripcion)) {
			return responseErrorDataExp('El campo descripcion es obligatorio');
		}
		if (empty($folios)) {
			return responseErrorDataExp('El campo folios es obligatorio');
		}
		if (empty($fecha_documento)) {
			return responseErrorDataExp('El campo fecha documento es obligatorio');
		}
		if (empty($origen_documento)) {
			return responseErrorDataExp('El campo origen documento es obligatorio');
		}
		if (empty($tipodoc_codigo)) {
			return responseErrorDataExp('El campo codigo tipo documental es obligatorio');
		}
		if (empty($nuip_creador)) {
			return responseErrorDataExp('El campo NUIP_CREADOR es obligatorio');
		}
		/*if(empty($orden_contenido))
			{
				return responseErrorDataExp('El campo Orden Contenido es obligatorio');
			}*/
		//*******************************************************************************************************************
		$ilunidad_documental = UnidadDocumentalPeer::getUnidadDocumentalByCodigo($numero_expediente); //codigo de barras
		if (count($ilunidad_documental) > 1) {
			return responseErrorDataExp('Existe mas de un registro con el mismo numero de expediente');
		} else if (!array_key_exists(0, $ilunidad_documental)) {
			return responseErrorDataExp('El numero de expediente no es valido, no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$unidad_documental = $ilunidad_documental[0];
		$unidad_documental_pk = $unidad_documental->getPrimaryKey();
		$subSerieId = $unidad_documental->getSubserieId();
		//*******************************************************************************************************************
		if (!in_array($unidad_documental->getEstadounidaddocumentalId(), array(1, 5))) {
			return responseErrorDataExp('El expediente esta cerrado o inactivo y no se pueden asociar nuevos documentos');
		}
		//*******************************************************************************************************************
		$is_verif_udocumental = VerificacionContUnidadDocPeer::getVerificacionContUnidadDocumentalDescripcion($verif_udocumental);
		if (!empty($is_verif_udocumental)) {
			$verificacion_doc = $is_verif_udocumental->getPrimaryKey();
		} else {
			$verificacion_doc = 1;
		}
		//*******************************************************************************************************************
		$obj_tipodoc_codigo = TipoDocumentalPeer::getTipoDocumentalCodigo($tipodoc_codigo, $subSerieId);
		if (empty($obj_tipodoc_codigo)) {
			return responseErrorDataExp('El codigo del tipo documental no es valido o no existe en el SGDEA');
		}
		$tipo_doc_id = $obj_tipodoc_codigo->getPrimaryKey();
		//*******************************************************************************************************************
		$is_estado_documento = EstadoContenidoUnidadDocumentalPeer::getEstadoContenidoUnidadDocumentalDescripcion($estado_documento);
		if (!empty($is_estado_documento)) {
			$estado_doc = $is_estado_documento->getPrimaryKey();
		} else {
			$estado_doc = 1;
		}
		//*******************************************************************************************************************
		$cuser_creador = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuip_creador, true);
		if (empty($cuser_creador)) {
			return responseErrorDataExp('El numero de identificacion del usuario creador(' . trim($nuip_creador) . '), no es valido o no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$usuario_id = $cuser_creador->getUsuarioId();
		//*******************************************************************************************************************
		$is_firma_estampa = TipoFirmaDigitalPeer::getTipoFirmaDigitalDescripcion($firma_estampa);
		$tipofirmadigital_id = null;
		if (!empty($is_firma_estampa)) {
			$tipofirmadigital_id = $is_firma_estampa->getPrimaryKey();
		}
		//*******************************************************************************************************************
		$is_soporte_documento = SoporteUnidadDocumentalPeer::getSoporteUnidadDocDesp($soporte_documento);
		$soporte_documental = 1;
		if (!empty($is_soporte_documento)) {
			$soporte_documental = $is_soporte_documento->getPrimaryKey();
		}
		//*******************************************************************************************************************
		$is_origen_documento = OrigenDocumentoPeer::getOrigenDocumentoDescripcion($origen_documento);
		$origen_documental = null;
		if (!empty($is_origen_documento)) {
			$origen_documental = $is_origen_documento->getPrimaryKey();
		} else {
			return responseErrorDataExp('El campo origen documento no es valido o no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$params = array();
		$params['tipodoc_codigo'] = $tipodoc_codigo;
		$params['estado_documento'] = $estado_documento;
		$params['firma_estampa'] = !empty($firma_estampa) ? $firma_estampa : null;
		$params['soporte_documento'] = $soporte_documento;
		$params['origen_documento'] = $origen_documental;
		$params['descripcion'] = $descripcion;
		$params['archivo_anexo'] = $archivo_anexo;
		$params['folios'] = $folios;
		$params['fecha_documento'] = $fecha_documento;
		$params['orden_contenido'] = $orden_contenido;
		$params['verificacion_doc'] = $verificacion_doc;
		$params['estado_doc'] = $estado_doc;
		$params['tipofirmadigital_id'] = $tipofirmadigital_id;
		$params['soporte_documental'] = $soporte_documental;
		$params['tipo_doc_id'] = $tipo_doc_id;
		$params['unidad_documental_pk'] = $unidad_documental_pk;
		$params['usuario_id'] = $usuario_id;
		//*******************************************************************************************************************
		//MANEJO DE ARCHIVOS UPLOADS
		$path_absolute = null;
		$path_relative = null;
		$szfile = null;
		$filename_source = null;
		$exfile = null;
		//*******************************************************************************************************************
		if (!empty($archivo_anexo) || !empty($archivo_nombre)) {
			$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(17)->getValortexto() . 'uploads');
			$filedir_target = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd")) . DIRECTORY_SEPARATOR;
			//***************************************************************************************************************
			$extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
			if (empty($extension)) {
				return responseErrorDataExp('Error, el nombre del archivo no es valido');
			}
			//***************************************************************************************************************
			$file_vars = pathinfo($filedir_target . $archivo_nombre);
			$filename_source = uniqid() . "_" . $simad_util->clean_name_file($file_vars);
			$fullpath = $filedir_target . $filename_source;
			$isCreate = simad_util::getConvertB64ToFile($archivo_anexo, $fullpath);
			//***************************************************************************************************************
			if (!$isCreate) {
				return responseErrorDataExp('Error interno del servidor o el archivo no es valido, no se realizó la acción solicitada');
			}
			//***************************************************************************************************************
			// valida que le archivo sea de la extension permitida solamente
			$info_file = new SplFileInfo($fullpath);
			$mimetypes = array("exe", "sys", "ini", "com", "dll", "jar", "bat", "cmd", "vbs", "tmp", "php", "py", "inf", "lnk", "csf", "msi", "msp", "gadget", "ps1", "ps1xml", "ps2", "ps2xml", "psc1", "psc2");
			$extension  = strtolower($info_file->getExtension());
			if (in_array($extension, $mimetypes)) {
				unlink($fullpath);
				return responseErrorDataExp('El tipo de archivo ' . $extension . ' no esta permitido');
			}
			//***************************************************************************************************************
			$folder_list = $unidad_documental->getBaseUrlAttachment($cuser_creador->getUsuarioId(), true);
			$path_absolute = $folder_list['absolute_path'];
			$path_relative = $folder_list['basic_path'];
			//***************************************************************************************************************
			$directorio_entidad = $folder_list['full_path']; // TARGET - FULL PATH
			$file_target = ($directorio_entidad) . DIRECTORY_SEPARATOR . $filename_source;
			//***************************************************************************************************************
			$szfile = 0;
			$exfile = null;
			if (@copy($fullpath, $file_target)) {
				$szfile = filesize($file_target);
				$exfile = pathinfo($fullpath, PATHINFO_EXTENSION);
				unlink($fullpath);
			} else {
				return responseErrorDataExp('Error interno del servidor, relacionado con el archivo adjunto');
			}
		}
		//*******************************************************************************************************************
		$params['path_absolute'] = $path_absolute;
		$params['path_relative'] = $path_relative;
		$params['size_file'] = $szfile;
		$params['format_file'] = !empty(trim($exfile)) ? strtoupper($exfile) : null;
		$params['archivo_nombre'] = $filename_source;
		//*******************************************************************************************************************
		$contenido_unidad_documental_added = ContenidoUnidadDocumentalPeer::addContenidoUnidadDocumental($params);
		//*******************************************************************************************************************
		if (empty($contenido_unidad_documental_added)) {
			return responseErrorDataExp('No se puedo asociar el documento en el expediente, ocurrio un error interno en el servidor.');
		}
		//*******************************************************************************************************************
		try {
			$folioManager = ExpedienteFolioManager::getInstance();
			$tieneDesorden = $folioManager->tieneFoliosDesordenados($unidad_documental->getPrimaryKey());
			if ($tieneDesorden) {
				$resultado = ContenidoUnidadDocumentalPeer::reordenarFoliosPorFecha($unidad_documental->getPrimaryKey());
			}
		} catch (PropelException $ex) {
		} catch (\Exception $ex) {
		} catch (\Throwable $ex) {
		}
		//*******************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $contenido_unidad_documental_added->getPrimaryKey(),
			'NUMERO_EXPEDIENTE' => $unidad_documental->getCodigoBarras(),
			'ID_EXPEDIENTE' => $unidad_documental->getPrimaryKey(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSG_INFO' => 'Documento asociado exitosamente al expediente'
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Throwable $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	//***********************************************************************************************************************
	return $response_data;
}

function AddRadicadoComExpediente($EntSecurity = array(), $ArcAddDocumento = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$params = array();
	//***********************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//***********************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array(
				'CONSECUTIVO_ID' => null,
				'NUMERO_EXPEDIENTE' => null,
				'ID_EXPEDIENTE' => null,
				'RADICADO_COM' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => $usuario_ws['message']
			);
		}
		//*******************************************************************************************************************
		$numero_expediente = isset($ArcAddDocumento['NUMERO_EXPEDIENTE']) ? trim($ArcAddDocumento['NUMERO_EXPEDIENTE']) : null;
		$unidaddocumental_id = isset($ArcAddDocumento['ID_EXPEDIENTE']) ? trim($ArcAddDocumento['ID_EXPEDIENTE']) : null;
		$tipodoc_codigo = isset($ArcAddDocumento['TIPODOC_CODIGO']) ? trim($ArcAddDocumento['TIPODOC_CODIGO']) : null;
		$radicado_com = isset($ArcAddDocumento['RADICADO_COM']) ? trim($ArcAddDocumento['RADICADO_COM']) : null;
		$nuip_creador = isset($ArcAddDocumento['NUIP_CREADOR']) ? trim($ArcAddDocumento['NUIP_CREADOR']) : null;
		$add_com_attachs = isset($ArcAddDocumento['ADD_ANEXOS_COM']) ? (bool)trim($ArcAddDocumento['ADD_ANEXOS_COM']) : false;
		$soporte_documento = isset($ArcAddDocumento['SOPORTE_DOCUMENTO']) ? trim($ArcAddDocumento['SOPORTE_DOCUMENTO']) : null;
		$origen_documento = isset($ArcAddDocumento['ORIGEN_DOCUMENTO']) ? trim($ArcAddDocumento['ORIGEN_DOCUMENTO']) : null;
		$tipo_com = isset($ArcAddDocumento['TIPO_COM']) ? trim($ArcAddDocumento['TIPO_COM']) : null;
		//*******************************************************************************************************************
		$wslog = WebserviceLogPeer::addLogWs("AddRadicadoComExpediente", $infoxml, null, 1, "Consumen Servicio web SGDEA => " . ($numero_expediente), "Ejecuta Exitoso => " . ($numero_expediente), $usuario_ws['object']->getPrimaryKey());
		//*******************************************************************************************************************
		if (empty($unidaddocumental_id) && empty($numero_expediente)) {
			return responseErrorDataExp('Se debe enviar el NUMERO_EXPEDIENTE o ID_EXPEDIENTE, uno de los dos campos son obligatorios');
		}
		if (empty($tipodoc_codigo)) {
			return responseErrorDataExp('El campo TIPODOC_CODIGO es obligatorio');
		}
		if (empty($radicado_com)) {
			return responseErrorDataExp('El campo RADICADO_COM es obligatorio');
		}
		if (empty($tipo_com)) {
			return responseErrorDataExp('El campo TIPO_COM es obligatorio');
		}
		if (empty($origen_documento)) {
			return responseErrorDataExp('El campo ORIGEN_DOCUMENTO es obligatorio');
		}
		if (empty($tipodoc_codigo)) {
			return responseErrorDataExp('El campo codigo tipo documental es obligatorio');
		}
		if (empty($nuip_creador)) {
			return responseErrorDataExp('El campo NUIP_CREADOR es obligatorio');
		}
		//*******************************************************************************************************************
		if (!empty($unidaddocumental_id)) {
			$ilunidad_documental[] = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id); //por PK
		} elseif (!empty($numero_expediente)) {
			$ilunidad_documental = UnidadDocumentalPeer::getUnidadDocumentalByCodigo($numero_expediente); //codigo de barras
		} else {
			return responseErrorDataExp('Ocurrio un error obteniendo la información del expediente en el SGDEA');
		}
		//*******************************************************************************************************************
		if (count($ilunidad_documental) > 1) {
			return responseErrorDataExp('Existe mas de un registro con el mismo numero de expediente, se debe corregir este error antes de continuar');
		} else if (!array_key_exists(0, $ilunidad_documental)) {
			return responseErrorDataExp('El numero de expediente no es valido, no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$unidad_documental = $ilunidad_documental[0];
		$subserie_id = $unidad_documental->getSubserieId();
		//*******************************************************************************************************************
		if (!in_array($unidad_documental->getEstadounidaddocumentalId(), array(1, 5))) {
			return responseErrorDataExp('El expediente esta cerrado o inactivo y no se pueden asociar nuevos documentos');
		}
		//*******************************************************************************************************************
		$object_com = null;
		$origentransfer_id = null;
		$contenidodoc_id = null;
		$fullpath_com = null;
		$total_pages = 1;
		if ($tipo_com == 1) {
			$object_com = ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_com);
			$origentransfer_id = OrigenTransferenciaCom::ComEnviada;
			$contenidodoc_id = $object_com->getContenidodocId() ?: null;
			$params['comenviada_id'] = $object_com->getPrimaryKey();
			$fullpath_com = $object_com->getPathImageDigitByCom();
			$total_pages = $object_com->getFolios();
		} else if ($tipo_com == 2) {
			$object_com = ComRecibidaPeer::getObjectComByRadicadoOrId($radicado_com);
			$origentransfer_id = OrigenTransferenciaCom::ComRecibida;
			$contenidodoc_id = $object_com->getContenidodocId() ?: null;
			$params['comrecibida_id'] = $object_com->getPrimaryKey();
			$fullpath_com = $object_com->getPathImageDigitByCom();
			$total_pages = $object_com->getFolios();
		} else {
			return responseErrorDataExp('El tipo de comunicacion enviado no existo o no esta implementado el servicio, no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		if (!empty($contenidodoc_id)) {
			$contenido_documental = ContenidoUnidadDocumentalPeer::retrieveByPK($contenidodoc_id);
			$current_expediente = $contenido_documental->getUnidadDocumental()->getCodigoTitulo();
			return responseErrorDataExp('El radicado ' . $radicado_com . ' ya esta asociado a un expediente en el SGDEA, expediente ' . $current_expediente);
		}
		//*******************************************************************************************************************
		if (!empty($fullpath_com) && file_exists($fullpath_com)) {
			$file_metadato = new FileMetaInfo();
			$total_pages = $file_metadato->countPagesWithExifTool($fullpath_com);
		}
		//*******************************************************************************************************************
		$verificacion_doc = 2;
		$is_verif_udocumental = VerificacionContUnidadDocPeer::retrieveByPK($verificacion_doc);
		if (!empty($is_verif_udocumental)) {
			$verificacion_doc = $is_verif_udocumental->getPrimaryKey();
		} else {
			return responseErrorDataExp('Ocurrio un error el estado de verficacion del documento no existes en el SGDEA');
		}
		//*******************************************************************************************************************
		$obj_tipodoc_codigo = TipoDocumentalPeer::getTipoDocumentalCodigo($tipodoc_codigo, $subserie_id);
		if (empty($obj_tipodoc_codigo)) {
			return responseErrorDataExp('El codigo del tipo documental(' . $tipodoc_codigo . ') no es valido o no existe en el SGDEA');
		}
		$tipodocumental_id = $obj_tipodoc_codigo->getPrimaryKey();
		//*******************************************************************************************************************
		$estado_doc = 1;
		$is_estado_documento = EstadoContenidoUnidadDocumentalPeer::retrieveByPK($estado_doc);
		if (!empty($is_estado_documento)) {
			$estado_doc = $is_estado_documento->getPrimaryKey();
		} else {
			return responseErrorDataExp('Ocurrio un error el estado del documento no existes en el SGDEA');
		}
		//*******************************************************************************************************************
		$cuser_creador = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuip_creador, true);
		if (empty($cuser_creador)) {
			return responseErrorDataExp('El numero de identificacion del usuario que sera asociado al documento como creador con identificacion(' . trim($nuip_creador) . '), no es valido, esta inactivo o no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$usuario_id = $cuser_creador->getUsuarioId();
		//*******************************************************************************************************************
		$is_soporte_documento = SoporteUnidadDocumentalPeer::getSoporteUnidadDocDesp($soporte_documento);
		$soporte_documento = 1;
		if (!empty($is_soporte_documento)) {
			$soporte_documento = $is_soporte_documento->getPrimaryKey();
		}
		//*******************************************************************************************************************
		$is_origen_documento = OrigenDocumentoPeer::getOrigenDocumentoDescripcion($origen_documento);
		$origen_documento = null;
		if (!empty($is_origen_documento)) {
			$origen_documento = $is_origen_documento->getPrimaryKey();
		} else {
			return responseErrorDataExp('El campo origen documento no es valido o no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$localizacionexp_id = $unidad_documental->getLocalizacionunidaddocumentalId();
		if ($localizacionexp_id == 1) {
			$destinotransferencia_id = 1;
			$fecha_acepta = date('Y-m-d G:i:s');
			$estadotrans_id = 2;
		} elseif ($localizacionexp_id == 2) {
			$destinotransferencia_id = 2;
			$estadotrans_id = 1;
			$fecha_acepta = null;
		} elseif ($localizacionexp_id == 3) {
			$destinotransferencia_id = 3;
			$estadotrans_id = 1;
			$fecha_acepta = null;
		} else {
			return responseErrorDataExp('El destino de la transferencia no existe en el SGDEA');
		}
		//*******************************************************************************************************************
		$params['unidaddocumental_id'] = $unidaddocumental_id;
		$params['estadotrans_id'] = $estadotrans_id;
		$params['unidaddocumental_id'] = $unidad_documental->getPrimaryKey();
		$params['origentransferencia_id'] = $origentransfer_id;
		$params['tipodocumental_id'] = $tipodocumental_id;
		$params['destinotransferencia_id'] = $destinotransferencia_id;
		$params['fecha_acepta'] = $fecha_acepta;
		$params['observaciones'] = "Transferencia automatica por integraciones con aplicaciones externas";
		//*******************************************************************************************************************
		$transferencia = TransferenciaPeer::createDefaultTransfer($params);
		$contenidounidaddocumental_id = null;
		$reponse_transfer = array('isError' => false, 'message' => 'Ocurrio un error interno el el servidor del SGDEA');
		//*******************************************************************************************************************
		if ($transferencia != null) {
			$transferencia_user = TransferenciaPeer::createUserTransferencia($transferencia, $usuario_id);
			if ($transferencia_user == null) {
				$transferencia->delete();
				return null;
			}
			//***************************************************************************************************************
			$contenidounidaddocumental_id = $transferencia->transferirGestion($usuario_id, $add_com_attachs);
			//***************************************************************************************************************
			if (empty($contenidounidaddocumental_id)) {
				return responseErrorDataExp('Ocurrio un error y no se pudo realizar la transferencia de la comunicación en el SGDEA');
			}
			//***************************************************************************************************************
			$reponse_transfer = array('isError' => false, 'message' => 'Comunicación asociada exitosamente al expediente ' . $unidad_documental->getCodigoTitulo() . ' en el SGDEA');
		} else {
			return responseErrorDataExp('Ocurrio un error y no se pudo crear la solicitud de transferencia de la comunicación en el SGDEA');
		}
		//*******************************************************************************************************************
		$contenido_documental_exp = ContenidoUnidadDocumentalPeer::retrieveByPK($contenidounidaddocumental_id);
		//*******************************************************************************************************************
		if (empty($contenido_documental_exp)) {
			return responseErrorDataExp('No se crear el contenido documental, ocurrio un error interno en el servidor del SGDEA');
		}
		//*******************************************************************************************************************
		$contenido_documental_exp->setValorHuella($contenido_documental_exp->getDataValorHuella());
		$contenido_documental_exp->setEstadocontenidounidaddocId($estado_doc);
		$contenido_documental_exp->setOrigendocumentoId($origen_documento);
		$contenido_documental_exp->setSoporteunidaddocumentalId($soporte_documento);
		$contenido_documental_exp->setVerificacioncontunidaddocId($verificacion_doc);
		$contenido_documental_exp->setFolios($total_pages);
		$contenido_documental_exp->save();
		//*******************************************************************************************************************
		try {
			$folioManager = ExpedienteFolioManager::getInstance();
			$tieneDesorden = $folioManager->tieneFoliosDesordenados($unidad_documental->getPrimaryKey());
			if ($tieneDesorden) {
				$resultado = ContenidoUnidadDocumentalPeer::reordenarFoliosPorFecha($unidad_documental->getPrimaryKey());
			}
		} catch (PropelException $ex) {
		} catch (\Exception $ex) {
		} catch (\Throwable $ex) {
		}
		//*******************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $contenido_documental_exp->getPrimaryKey(),
			'NUMERO_EXPEDIENTE' => $unidad_documental->getCodigoBarras(),
			'ID_EXPEDIENTE' => $unidad_documental->getPrimaryKey(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'RADICADO_COM' => $radicado_com,
			'ISERROR' =>  $reponse_transfer['isError'],
			'MSG_INFO' => $reponse_transfer['message']
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'RADICADO_COM' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'RADICADO_COM' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'RADICADO_COM' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Throwable $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'ID_EXPEDIENTE' => null,
			'RADICADO_COM' => null,
			'FECHA_TRANSACCION' => date("Y-m-d G:i:s"),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	//***********************************************************************************************************************
	return $response_data;
}

function ActualizarExpedienteArch($EntSecurity = array(), $ArcAddExpediente = array())
{
	$response_data = array();
	$filter_list = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//******************************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//******************************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			$usuario_default = 1;
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Credenciales", $usuario_default);
			return array(
				'CONSECUTIVO_ID' => null,
				'NUMERO_EXPEDIENTE' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => $usuario_ws['message']
			);
		}
		//**************************************************************************************************************************
		$numero_expediente = isset($ArcAddExpediente['NUMERO_EXPEDIENTE']) ? trim($ArcAddExpediente['NUMERO_EXPEDIENTE']) : null;
		if (empty($numero_expediente)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo NUMERO_EXPEDIENTE, es un parametro obligatorio');
		}
		$filter_list['codigo_barras'] = $numero_expediente;
		//**************************************************************************************************************************
		$notas_edicion = isset($ArcAddExpediente['NOTAS_EDICION']) ? trim($ArcAddExpediente['NOTAS_EDICION']) : null;
		if (empty($notas_edicion)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo NOTAS_EDICION, es un parametro obligatorio');
		}
		//**************************************************************************************************************************
		$nombre_expediente = isset($ArcAddExpediente['NUEVO_NOMBRE_EXPEDIENTE']) ? trim($ArcAddExpediente['NUEVO_NOMBRE_EXPEDIENTE']) : null;
		if (empty($nombre_expediente)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo NUEVO_NOMBRE_EXPEDIENTE, es un parametro obligatorio');
		}
		//**************************************************************************************************************************
		$codigo_dependencia = isset($ArcAddExpediente['CODIGO_DEPENDENCIA']) ? trim($ArcAddExpediente['CODIGO_DEPENDENCIA']) : null;
		if (empty($codigo_dependencia)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo CODIGO_DEPENDENCIA, es un parametro obligatorio');
		}
		$filter_list['codigo_dependencia'] = $codigo_dependencia;
		//**************************************************************************************************************************
		$codigo_serie = isset($ArcAddExpediente['CODIGO_SERIE']) ? trim($ArcAddExpediente['CODIGO_SERIE']) : null;
		if (empty($codigo_serie)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo CODIGO_SERIE, es un parametro obligatorio');
		}
		$filter_list['codigo_serie'] = $codigo_serie;
		//**************************************************************************************************************************
		$codigo_subserie = isset($ArcAddExpediente['CODIGO_SUBSERIE']) ? trim($ArcAddExpediente['CODIGO_SUBSERIE']) : null;
		if (empty($codigo_subserie)) {
			$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA", "Error Parametros", $usuario_ws['object']->getPrimaryKey());
			return responseErrorDataExp('Debe enviar un valor valido para el campo CODIGO_SUBSERIE, es un parametro obligatorio');
		}
		$filter_list['codigo_subserie'] = $codigo_subserie;
		//**************************************************************************************************************************
		$wslog = WebserviceLogPeer::addLogWs("ActualizarExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA => " . $numero_expediente, "Ejecuta Exitoso", $usuario_ws['object']->getPrimaryKey());
		//**************************************************************************************************************************
		$unidad_documental_list = UnidadDocumentalPeer::getUnidadDocByCodigoAndTrd($filter_list);
		//**************************************************************************************************************************
		if (count($unidad_documental_list) > 1) {
			return responseErrorDataExp('Se encontraron varios expedientes asociados a los filtros enviados en la consulta, solo se permite actualizar un expediente');
		} else {
			$unidad_documental = $unidad_documental_list[0];
		}
		//**************************************************************************************************************************
		if (!($unidad_documental instanceof UnidadDocumental) or empty($unidad_documental)) {
			return responseErrorDataExp('El expedinte solicitado no fue encontrado');
		}
		//**************************************************************************************************************************
		$unidad_documental_last = clone $unidad_documental;
		$notas_expediente = !empty($unidad_documental->getNotas()) ? trim($unidad_documental->getNotas()) . "|" . $notas_edicion : $notas_edicion;
		//**************************************************************************************************************************
		$unidad_documental->setTitulo($nombre_expediente);
		$unidad_documental->setNotas($notas_expediente);
		$unidad_documental->save();
		//**************************************************************************************************************************
		AuditLogPeer::guardarAuditoriaLite(
			"UnidadDocumental",
			$unidad_documental_last,
			$unidad_documental,
			ModulesEnable::Archivo,
			trim($unidad_documental->getCodigoBarras()),
			$usuario_ws['object']->getPrimaryKey()
		);
		//**************************************************************************************************************************
		return $response_data = array(
			'CONSECUTIVO_ID' => $unidad_documental->getPrimaryKey(),
			'NUMERO_EXPEDIENTE' => $unidad_documental->getCodigoBarras(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  false,
			'MSG_INFO' => 'Expediente actualizado exitosamente'
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	//*****************************************************************************************************************************
	return $response_data;
}

function CreateNewExpedienteArch($EntSecurity = array(), $ArcAddExpediente = array(), $ExpInteresado = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	$simad_util = new simad_util();
	//******************************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//******************************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array(
				'CONSECUTIVO_ID' => null,
				'NUMERO_EXPEDIENTE' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => $usuario_ws['message']
			);
		}
		//**************************************************************************************************************************
		$wslog = WebserviceLogPeer::addLogWs("CreateNewExpedienteArch", $infoxml, null, 1, "Consumen Servicio Web SGDEA ", "Ejecuta Exitoso", $usuario_ws['object']->getPrimaryKey());
		//**************************************************************************************************************************
		$usuariologuiado = $usuario_ws['object']->getPrimaryKey();
		$id_creador = $usuariologuiado;
		//**************************************************************************************************************************
		$nuid_responsable = isset($ArcAddExpediente['NUID_RESPONSABLE']) ? trim($ArcAddExpediente['NUID_RESPONSABLE']) : null;
		$cuser_responsable = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuid_responsable, true);
		if ($cuser_responsable == null) {
			return responseErrorDataExp('Debe enviar una numero de identificacion valido para el campo NUID_RESPONSABLE');
		}
		$id_responsable = $cuser_responsable->getUsuarioId();
		$usuario_responsable = UsuarioPeer::retrieveByPK($id_responsable);
		//**************************************************************************************************************************
		$nuid_inventariador = isset($ArcAddExpediente['NUID_INVENTARIADOR']) ? trim($ArcAddExpediente['NUID_INVENTARIADOR']) : null;
		$cuser_inventariador = CargoUsuarioPeer::getCargoUsuarioByNuidUser($nuid_inventariador, true);
		if ($cuser_inventariador == null) {
			return responseErrorDataExp('Debe enviar una numero de identificacion valido para el campo NUID_INVENTARIADOR');
		}
		$id_inventariador = $cuser_inventariador->getUsuarioId();
		//**************************************************************************************************************************
		$volumen = isset($ArcAddExpediente['VOLUMEN']) ? trim($ArcAddExpediente['VOLUMEN']) : 1;
		//**************************************************************************************************************************
		$numero_expediente = isset($ArcAddExpediente['NUMERO_EXPEDIENTE']) ? trim($ArcAddExpediente['NUMERO_EXPEDIENTE']) : null;
		$str_fase_archivo = isset($ArcAddExpediente['FASE_ARCHIVO']) ? trim($ArcAddExpediente['FASE_ARCHIVO']) : null;
		$obj_fase_archivo = LocalizacionUnidadDocumentalPeer::getLocalizacionByDesc($str_fase_archivo);
		if ($obj_fase_archivo == null) {
			return responseErrorDataExp('Debe enviar un valor valido para la fase de archivo (GESTION,CENTRAL o HISTORICO)');
		}
		$fase_archivo = $obj_fase_archivo->getLocalizacionunidaddocumentalId();
		//**************************************************************************************************************************
		$str_regional = isset($ArcAddExpediente['NOMBRE_SEDE']) ? trim($ArcAddExpediente['NOMBRE_SEDE']) : null;
		$obj_regional = RegionalPeer::getRegionalByName($str_regional);
		if ($obj_regional == null) {
			$obj_regional = $usuario_responsable->getRegional();
		}
		//**************************************************************************************************************************
		$str_soporte_documental = isset($ArcAddExpediente['SOPORTE_DOCUMENTO']) ? trim($ArcAddExpediente['SOPORTE_DOCUMENTO']) : null;
		$obj_soporte_documental = SoporteUnidadDocumentalPeer::getSoporteUnidadDocDesp($str_soporte_documental);
		if ($obj_soporte_documental == null) {
			return responseErrorDataExp('Debe enviar un valor valido para campo SOPORTE_DOCUMENTO');
		}
		$soporte_documental = $obj_soporte_documental->getSoporteunidaddocumentalId();
		//**************************************************************************************************************************
		$str_estado_documental = isset($ArcAddExpediente['ESTADO_EXPEDIENTE']) ? trim($ArcAddExpediente['ESTADO_EXPEDIENTE']) : null;
		$obj_estado_documental = EstadoUnidadDocumentalPeer::getEstadoByDesc($str_estado_documental);
		if ($obj_estado_documental == null) {
			return responseErrorDataExp('Debe enviar un valor valido para campo ESTADO_EXPEDIENTE');
		}
		$estado_documental = $obj_estado_documental->getEstadounidaddocumentalId();
		//**************************************************************************************************************************
		$frecuencia_documental = isset($ArcAddExpediente['FRECUENCIA_CONSULTA']) ? trim($ArcAddExpediente['FRECUENCIA_CONSULTA']) : 1;
		if (isset($ArcAddExpediente['FRECUENCIA_CONSULTA'])) {
			$str_frecuencia_documental = trim($ArcAddExpediente['FRECUENCIA_CONSULTA']);
			$obj_frecuencia_documental = FrecuenciaConsultaPeer::getFrecuenciaByDesc($str_frecuencia_documental);
			if ($obj_frecuencia_documental == null) {
				return responseErrorDataExp('Debe enviar un valor valido para campo FRECUENCIA_CONSULTA');
			}
			$frecuencia_documental = $obj_frecuencia_documental->getFrecuenciaconsultaId();
		} else {
			$frecuencia_documental = 1;
		}
		//**************************************************************************************************************************
		if (isset($ArcAddExpediente['MEDIO_CONSERVACION'])) {
			$str_medio_conservacion = trim($ArcAddExpediente['MEDIO_CONSERVACION']);
			$obj_medio_conservacion = UnidadConservadoraPeer::getUnidadByDesc($str_medio_conservacion);
			if ($obj_medio_conservacion == null) {
				return responseErrorDataExp('Debe enviar un valor valido para campo MEDIO_CONSERVACION');
			}
			$medio_conservacion = $obj_medio_conservacion->getUnidadconservadoraId();
		} else {
			$medio_conservacion = 1;
		}
		//**************************************************************************************************************************
		$codigo_dependencia = isset($ArcAddExpediente['CODIGO_DEPENDENCIA']) ? trim($ArcAddExpediente['CODIGO_DEPENDENCIA']) : null;
		$nombre_dependencia = isset($ArcAddExpediente['NOMBRE_DEPENDENCIA']) ? trim($ArcAddExpediente['NOMBRE_DEPENDENCIA']) : null;
		if (empty($codigo_dependencia)) {
			return responseErrorDataExp('El campo CODIGO_DEPENDENCIA es obligatorio');
		}
		//**************************************************************************************************************************
		if (!empty($codigo_dependencia) && !empty($nombre_dependencia))
			$dependecia_expediente = DependenciaPeer::getDependenciaByNombreAndCodigo($codigo_dependencia, $nombre_dependencia);
		elseif (!empty($codigo_dependencia))
			$dependecia_expediente = DependenciaPeer::getDependenciaByCodigo($codigo_dependencia);

		if (empty($dependecia_expediente)) {
			return responseErrorDataExp('La dependencia con codigo ' . $codigo_dependencia . ' no existe en el SGDEA');
		}
		//**************************************************************************************************************************
		$codigo_subserie = isset($ArcAddExpediente['CODIGO_SUBSERIE']) ? trim($ArcAddExpediente['CODIGO_SUBSERIE']) : null;
		//**************************************************************************************************************************
		if (empty($codigo_subserie)) {
			return responseErrorDataExp('El campo CODIGO_SUBSERIE es obligatorio');
		}
		//**************************************************************************************************************************
		$subserie_obj = SubseriePeer::getSubserieByCodigo($codigo_subserie);
		if ($subserie_obj == null) {
			return responseErrorDataExp('El codigo de SUBSERIE enviado no existe en el SGDEA');
		}

		$subserie_id = $subserie_obj->getSubserieId();
		$regional_id = $obj_regional->getRegionalId();
		//**************************************************************************************************************************
		$titulo = isset($ArcAddExpediente['NOMBRE_EXPEDIENTE']) ? trim($ArcAddExpediente['NOMBRE_EXPEDIENTE']) : null;
		if (empty($titulo)) {
			return responseErrorDataExp('El campo NOMBRE_EXPEDIENTE es obligatorio');
		}
		//**************************************************************************************************************************
		$contenido = isset($ArcAddExpediente['CONTENIDO']) ? trim($ArcAddExpediente['CONTENIDO']) : null;
		$folios = isset($ArcAddExpediente['FOLIOS']) ? trim($ArcAddExpediente['FOLIOS']) : 1;
		$notas = isset($ArcAddExpediente['NOTAS']) ? trim($ArcAddExpediente['NOTAS']) : null;
		$volumen = isset($ArcAddExpediente['VOLUMEN']) ? trim($ArcAddExpediente['VOLUMEN']) : 1;
		$numero_caja = isset($ArcAddExpediente['NUMERO_CAJA']) ? trim($ArcAddExpediente['NUMERO_CAJA']) : null;
		$numero_identificacion = isset($ArcAddExpediente['NUMERO_IDENTIFICACION']) ? trim($ArcAddExpediente['NUMERO_IDENTIFICACION']) : null;
		$fecha_apertura = isset($ArcAddExpediente['FECHA_APERTURA']) ? trim($ArcAddExpediente['FECHA_APERTURA']) : date("Y-m-d");
		$fecha_cierre = isset($ArcAddExpediente['FECHA_CIERRE']) ? trim($ArcAddExpediente['FECHA_CIERRE']) : null;
		$ubicacion_expediente = isset($ArcAddExpediente['UBICACION_EXPEDIENTE']) ? trim($ArcAddExpediente['UBICACION_EXPEDIENTE']) : null;
		//**************************************************************************************************************************
		//validaciones FKs y campos obligatorios
		if (empty($fase_archivo)) {
			return responseErrorDataExp('El campo Fase Archivo es obligatorio');
		}
		//**************************************************************************************************************************
		if (empty($soporte_documental)) {
			return responseErrorDataExp('El campo Soporte Documental es obligatorio');
		}
		//**************************************************************************************************************************
		if (empty($estado_documental)) {
			return responseErrorDataExp('El campo Estado Documental es obligatorio');
		}
		//**************************************************************************************************************************
		if (empty($frecuencia_documental)) {
			return responseErrorDataExp('El campo Frecuencia Documental es obligatorio');
		}
		//**************************************************************************************************************************
		if (empty($medio_conservacion)) {
			return responseErrorDataExp('El campo Medio Conservacion es obligatorio');
		}
		//**************************************************************************************************************************
		if (empty($subserie_id)) {
			return responseErrorDataExp('El campo Subserie Id es obligatorio');
		}
		//**************************************************************************************************************************
		if ($soporte_documental == null) {
			//SOPORTE_UNIDAD_DOCUMENTAL.SOPORTEUNIDADDOCUMENTAL_ID = 1  (DESCRIPCION = PAPEL)
			$soporte_documental = 1;
		}
		if ($estado_documental == null) {
			//ESTADO_UNIDAD_DOCUMENTAL.ESTADOUNIDADDOCUMENTAL_ID = 1  (DESCRIPCION = ACTIVO)
			$estado_documental = 1;
		}
		//**************************************************************************************************************************
		$comlist_interesados = array();
		$laddnew_interesados = array();
		$interesados_nuids = array();
		$isMandatory_interesado = false;

		foreach ($ExpInteresado as $info_list) {
			if (simad_util::array_check($info_list, 'PRIMER_NOMBRE') && simad_util::array_check($info_list, 'PRIMER_APELLIDO') && simad_util::array_check($info_list, 'NUMERO_IDENTIFICACION')) {
				if (!trim($info_list['NUMERO_IDENTIFICACION'])) //si no existe el nro de identificacion, da error
				{
					return responseErrorDataExp('El numero de identificación del interesado no es valido');
					exit;
				}
				//******************************************************************************************************************
				$interesados_nuids[] = trim($info_list['NUMERO_IDENTIFICACION']);
				//******************************************************************************************************************
				$interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'], $info_list['PRIMER_APELLIDO'], $info_list['NUMERO_IDENTIFICACION']);
				if ($interesado_id != null) {
					$comlist_interesados[] = $interesado_id;
				} elseif (simad_util::array_check($info_list, 'TIPO_IDENTIFICACION')) {
					$tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla(trim($info_list['TIPO_IDENTIFICACION']));
					if ($tipouid_id == null) {
						return responseErrorDataExp('El tipo de identificación del interesado no es un valor valido');
					}
					//**************************************************************************************************************
					$info_list['TIPO_IDENTIFICACION'] = $tipouid_id;
					$optional_fileds = array('TIPO_GENERO' => true, 'EMAIL' => true);
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list, $optional_fileds);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorDataExp($isValidIntInfo['MsgError']);
						exit;
					}
				} else {
					$info_list['TIPO_IDENTIFICACION'] = 5;
					$isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
					if ($isValidIntInfo['IsValid'] == true) {
						$laddnew_interesados[] = $info_list;
					} else {
						return responseErrorDataExp($isValidIntInfo['MsgError']);
						exit;
					}
				}
			} else {
				return responseErrorDataExp('El nombre o numero de identificación del interesado no es valido');
				exit;
			}
		}
		//************************************************************************************************************************
		if (count($comlist_interesados) <= 0 && count($laddnew_interesados) <= 0 && $isMandatory_interesado == true) {
			return responseErrorDataExp('Debe enviar como minimo un interesado');
		}
		//************************************************************************************************************************
		$params = array();
		$params['codigo_barras'] = !empty(trim($numero_expediente)) ? trim($numero_expediente) : null;
		$params['id_responsable'] = $id_responsable;
		$params['id_creador'] = $id_creador;
		$params['id_inventariador'] = $id_inventariador;
		$params['usuario_responsable'] = $usuario_responsable;
		$params['volumen'] = trim($volumen) ? trim($volumen) : 1;
		//FK...
		$params['fase_archivo'] = $fase_archivo;
		$params['sede_regional'] = $obj_regional;
		$params['soporte_documental'] = $soporte_documental;
		$params['estado_documental'] = $estado_documental;
		$params['frecuencia_documental'] = $frecuencia_documental;
		$params['medio_conservacion'] = $medio_conservacion;
		$params['subserie_id'] = $subserie_id;
		$params['regional_id'] = $regional_id;
		$params['titulo'] = $titulo;
		$params['contenido'] = $contenido;
		$params['folios'] = $folios;
		$params['notas'] = $notas;
		$params['volumen'] = $volumen;
		$params['numero_caja'] = $numero_caja;
		$params['fecha_apertura'] = $fecha_apertura;
		$params['fecha_cierre'] = $fecha_cierre;
		$params['numero_identificacion'] = $numero_identificacion;
		$params['ubicacion_expediente'] = !empty(trim($ubicacion_expediente)) ? trim($ubicacion_expediente) : "DIGITAL";
		$params['numero_caja'] = $numero_caja;
		$params['already_interesados'] = $comlist_interesados;
		$params['new_interesados'] = $laddnew_interesados;
		//************************************************************************************************************************
		$response = UnidadDocumentalPeer::addUnidadDocumental($params);
		//************************************************************************************************************************
		if ($response['error']) {
			return responseErrorDataExp($response['message']);
		}
		//************************************************************************************************************************
		$unidad_documental = $response['object'];
		//************************************************************************************************************************
		$response_data = array(
			'CONSECUTIVO_ID' => $unidad_documental->getPrimaryKey(),
			'NUMERO_EXPEDIENTE' => $unidad_documental->getCodigoBarras(),
			'FECHA_TRANSACCION' => $fecha_transaccion,
			'ISERROR' =>  False,
			'MSG_INFO' => 'Expediente creado exitosamente'
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'CONSECUTIVO_ID' => null,
			'NUMERO_EXPEDIENTE' => null,
			'FECHA_TRANSACCION' => null,
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	//****************************************************************************************************************************
	return $response_data;
}

function SearchExpedienteList($EntSecurity = array(), $TermSearchExpediente = array())
{
	$response_data = array();
	$fecha_transaccion = date('Y-m-d G:i:s');
	//*****************************************************************************************************************************
	$infoxml = file_get_contents("php://input");
	//*****************************************************************************************************************************
	try {
		$usuario_ws = UsuarioPeer::autenticateUserWs($EntSecurity);
		if ($usuario_ws['isError']) {
			return array(
				'CONSECUTIVO_ID' => null,
				'ID_EXPEDIENTE' => null,
				'NUMERO_EXPEDIENTE' => null,
				'FECHA_TRANSACCION' => $fecha_transaccion,
				'ISERROR' =>  false,
				'MSG_INFO' => $usuario_ws['message']
			);
		}
		//************************************************************************************************************************
		$usuariologuiado = $usuario_ws['object']->getUsuarioId();
		//************************************************************************************************************************
		$wslog = WebserviceLogPeer::addLogWs("SearchExpedienteList", $infoxml, null, true, "Consumen Servicio web SGDEA", "Ejecuta Exitoso", $usuariologuiado);
		//************************************************************************************************************************
		$numero_expediente = isset($TermSearchExpediente['NUMERO_EXPEDIENTE']) ? trim($TermSearchExpediente['NUMERO_EXPEDIENTE']) : null;
		$codigo_dependencia = isset($TermSearchExpediente['CODIGO_DEPENDENCIA']) ? trim($TermSearchExpediente['CODIGO_DEPENDENCIA']) : null;
		$codigo_subserie = isset($TermSearchExpediente['CODIGO_SUBSERIE']) ? trim($TermSearchExpediente['CODIGO_SUBSERIE']) : null;
		$codigo_serie = isset($TermSearchExpediente['CODIGO_SERIE']) ? trim($TermSearchExpediente['CODIGO_SERIE']) : null;
		$str_fase_archivo = isset($TermSearchExpediente['FASE_ARCHIVO']) ? trim($TermSearchExpediente['FASE_ARCHIVO']) : null;
		$unidaddocumental_id = isset($TermSearchExpediente['ID_EXPEDIENTE']) ? trim($TermSearchExpediente['ID_EXPEDIENTE']) : null;
		$max_rows = isset($TermSearchExpediente['MAX_ROWS']) ? trim($TermSearchExpediente['MAX_ROWS']) : 100;
		//************************************************************************************************************************
		//campos opcionales
		$identificacion_interesado = isset($TermSearchExpediente['IDENTIFICACION_INTERESADO']) ? trim($TermSearchExpediente['IDENTIFICACION_INTERESADO']) : null;
		$pnombre_interesado = isset($TermSearchExpediente['PNOMBRE_INTERESADO']) ? trim($TermSearchExpediente['PNOMBRE_INTERESADO']) : null;
		$papellido_interesado = isset($TermSearchExpediente['PAPELLIDO_INTERESADO']) ? trim($TermSearchExpediente['PAPELLIDO_INTERESADO']) : null;
		//************************************************************************************************************************
		//validaciones FKs y campos obligatorios
		if (empty($numero_expediente) && empty($str_fase_archivo)) {
			//return responseErrorDataExp('El campo n&uacute;mero expediente es obligatorio');
		}
		//************************************************************************************************************************
		if (empty($codigo_dependencia) && empty($codigo_subserie) && empty($codigo_serie) && empty($unidaddocumental_id)) {
			if (empty($numero_expediente))
				return responseErrorDataExp('Es requerido uno de los dos campos: código dependencia, código serie , código subserie o el numero de expediente');
		}
		//************************************************************************************************************************
		if (empty($str_fase_archivo) && $unidaddocumental_id) {
			return responseErrorDataExp('El campo Fase Archivo es obligatorio');
		} else {
			$obj_fase_archivo = !empty($str_fase_archivo) ? LocalizacionUnidadDocumentalPeer::getLocalizacionByDesc($str_fase_archivo) : null;
			if ($obj_fase_archivo == null) {
				return responseErrorDataExp("La fase de archivo no se encuentra en el SGDEA");
			}
		}
		$int_fase_archivo = $obj_fase_archivo != null ? $obj_fase_archivo->getLocalizacionunidaddocumentalId() : null;
		//************************************************************************************************************************
		$subserie = null;
		if (!empty($codigo_subserie)) {
			$subserie = SubseriePeer::getSubserieByCodigo($codigo_subserie);
			if (empty($subserie) || $subserie == null) {
				return responseErrorDataExp("La subserie con codigo " . $codigo_subserie . " no existe en el SGDEA");
			}
		}
		//************************************************************************************************************************
		$params_search = [];
		$params_search['numero_expediente'] = $numero_expediente;
		$params_search['codigo_dependencia'] = $codigo_dependencia;
		$params_search['subserie_id'] = $subserie;
		$params_search['fase_archivo'] = $int_fase_archivo;
		$params_search['identificacion_interesado'] = $identificacion_interesado;
		$params_search['pnombre_interesado'] = $pnombre_interesado;
		$params_search['papellido_interesado'] = $papellido_interesado;
		$params_search['unidaddocumental_id'] = $unidaddocumental_id;
		//************************************************************************************************************************
		$array_unidad_documental = UnidadDocumentalPeer::getUnidadDocumentalBySearch($params_search, $max_rows);
		//************************************************************************************************************************
		if ($array_unidad_documental['error']) {
			return responseErrorDataExp($array_unidad_documental['message']);
		}
		$pre_unidad_documental = $array_unidad_documental['object'];
		//*********************INICIO FOR EACH MASIVA ****************************************************************************
		$list_expedientes = array();
		foreach ($pre_unidad_documental as $exp_row) {
			$row_list_expedientes['DEPENDENCIA'] = $exp_row->getSubserie()->getSerie()->getDependencia()->getNombreCustomFull();
			$row_list_expedientes['CODIGO_DEPENDENCIA'] = $exp_row->getSubserie()->getSerie()->getDependencia()->getCodigo();
			$row_list_expedientes['NUMERO_EXPEDIENTE'] = $exp_row->getCodigoBarras();
			$row_list_expedientes['FECHA_CREACION'] = $exp_row->getFechaCreacion();
			$row_list_expedientes['FECHA_APERTURA'] = $exp_row->getFechaApertura();
			$row_list_expedientes['FECHA_CIERRE'] = $exp_row->getFechaCierre();
			$row_list_expedientes['NOMBRE_SERIE'] = $exp_row->getSubserie()->getSerie()->getDescripcion();
			$row_list_expedientes['CODIGO_SERIE'] = $exp_row->getSubserie()->getSerie()->getCodigo();
			$row_list_expedientes['NOMBRE_SUBSERIE'] = $exp_row->getSubserie()->getDescripcion();
			$row_list_expedientes['CODIGO_SUBSERIE'] = $exp_row->getSubserie()->getCodigo();
			$row_list_expedientes['FASE_ARCHIVO'] = $exp_row->getLocalizacionUnidadDocumental()->getDescripcion();
			$row_list_expedientes['NOMBRE_EXPEDIENTE'] = $exp_row->getTitulo();
			$row_list_expedientes['CONSECUTIVO_ID'] = $exp_row->getPrimaryKey();
			//********************************************************************************************************************
			$unidad_documental_id = $exp_row->getUnidaddocumentalId();
			$contenido_unidad_documental = ContenidoUnidadDocumentalPeer::getContUnidadDocByUDocId($unidad_documental_id);
			//********************************************************************************************************************
			$list_documentos = array();
			foreach ($contenido_unidad_documental as $row) {
				$xguid = simad_util::create_guid(basename($row->getRuta()));
				$row_list_documentos['DESCRIPCION'] = $row->getDescripcion();
				$row_list_documentos['FECHA_CREACION'] = $row->getFechaCreacion();
				$row_list_documentos['FECHA_DOCUMENTO'] = $row->getFechaDocumento();
				$row_list_documentos['CODIGO_TIPODOC'] = $row->getTipoDocumental()->getCodigo();
				//****************************************************************************************************************
				if ($row->getVinculoRegistro()) {
					$url_modulo = preg_split("/[\/]+/", trim($row->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
					//************************************************************************************************************
					$tipo_consecutivo = 0;
					$list_attach = array();
					$object_pk = -1;
					//************************************************************************************************************
					if ($url_modulo[0] == 'recibida.php') {
						$tipo_consecutivo = 2;
						$object_pk = end($url_modulo);
					} else if ($url_modulo[0] == 'interna.php') {
						$tipo_consecutivo = 3;
						$object_pk = end($url_modulo);
					} else if ($url_modulo[0] == 'enviada.php') {
						$tipo_consecutivo = 1;
						$object_pk = end($url_modulo);
					} else {
						$tipo_consecutivo = 0;
					}
					//************************************************************************************************************
					switch ($tipo_consecutivo) {
						case 1: //enviadas
							$com_enviada =  ComEnviadaPeer::retrieveByPK($object_pk);
							if ($com_enviada == null) {
								break;
							}
							//***************************************************************************************
							$list_attach = $com_enviada->getListDigitDocument();
							break;
						case 2: //recibidas
							$com_recibida =  ComRecibidaPeer::retrieveByPK($object_pk);
							if ($com_recibida == null) {
								break;
							}
							//***************************************************************************************
							$list_attach = $com_recibida->getListDigitDocument();
							break;
						case 3: //interna
							$com_interna =  ComInternaPeer::retrieveByPK($object_pk);
							if ($com_interna == null) {
								break;
							}
							//***************************************************************************************
							$list_attach = $com_interna->getListDigitDocument();
							break;
						default:
							$list_attach = array();
							break;
					}
					//***********************************************************************************************
					$array_comdocs = array();
					$array_normal = array();
					foreach ($list_attach as $adjunto) {
						$array_normal['GUID'] = $adjunto['GUID'];
						$array_normal['NOMBRE_ARCHIVO'] = $adjunto['NOMBRE_ARCHIVO'];
						$array_normal['TIPO_ATTACHMENT'] = $adjunto['TIPO_ATTACHMENT'];
						$array_normal['URL'] = $adjunto['URL'];
						$array_normal['URL_DOWNLOAD'] = $adjunto['URL_DOWNLOAD'];
						$array_normal['SIZE_FILE'] = $adjunto['SIZE_FILE'];
						$array_comdocs[] = $array_normal;
					}
					$row_list_documentos['ADJUNTOS'] = array($array_comdocs);
				} else {
					$file_response = $row->getDigitDocumentByXguid($xguid);
					//***********************************************************************************************
					$array_normal['GUID'] = $xguid;
					$array_normal['NOMBRE_ARCHIVO'] = basename($row->getRuta());
					$array_normal['TIPO_ATTACHMENT'] = 'ANEXO';
					$array_normal['URL'] = $row->getUrlTokenTrustImageByObject($xguid);
					$array_normal['URL_DOWNLOAD'] = $row->getUrlTokenTrustImageByObject($xguid);
					$array_normal['SIZE_FILE'] = $file_response['SIZE_FILE'];

					$row_list_documentos['ADJUNTOS'] = array($array_normal);
				}
				$list_documentos[] = $row_list_documentos;
			}
			//********************************************************************************************************************
			$row_list_expedientes['LISTA_DOCUMENTOS'] = $list_documentos;
			$list_expedientes[] = $row_list_expedientes;
		}
		//************************************************************************************************************************
		$response_data = array(
			'LISTA_EXPEDIENTES' => array($list_expedientes),
			'ISERROR' =>  false,
			'MSG_INFO' => 'Expedientes encontrados exitosamente, se listan los documentos de los primeros ' . $max_rows . ' expedientes que coinciden con los filtros'
		);
	} catch (SoapFault $ex) {
		$response_data = array(
			'LISTA_EXPEDIENTES' => array(),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (PropelException $ex) {
		$response_data = array(
			'LISTA_EXPEDIENTES' => array(),
			'ISERROR' =>  true,
			'MSG_INFO' => "Se presento un error al realizar la consulta"
		);
	} catch (\Exception $ex) {
		$response_data = array(
			'LISTA_EXPEDIENTES' => array(),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	} catch (\Throwable $ex) {
		$response_data = array(
			'LISTA_EXPEDIENTES' => array(),
			'ISERROR' =>  true,
			'MSG_INFO' => $ex->getMessage()
		);
	}
	//*****************************************************************************************************************************
	return $response_data;
}
