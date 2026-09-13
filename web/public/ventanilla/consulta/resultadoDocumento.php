<?php
	$fecha_transaccion = date("Y-m-d G:i:s");
	$com_recibidas = array();
	require_once __DIR__ . '/icon-captcha/vendor/autoload.php';
	// si el form ha sido enviado, valida el captcha.
	if (!empty($_POST)) 
	{
		session_start(); 
		// Load the IconCaptcha options.
		$options = require __DIR__ . '/icon-captcha/captcha-config.php';
		// Create an instance of IconCaptcha.
		$captcha = new \IconCaptcha\IconCaptcha($options);
		// Validate the captcha.
		$validation = $captcha->validate($_POST);
		// Confirm the captcha was validated.
		if (!$validation->success()) 
		{
			$result = '<b>Captcha:</b> Validaci&oacute;n fallida con codigo de error:' . $validation->getErrorCode();
			http_response_code(404);
		}
	} 
	else 
	{
		$result = 'El metodo no esta permitido';
		http_response_code(400);
		exit($result);
	}
	//************************************************************************************************
	require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');
	$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
	sfContext::createInstance($configuration);
	//************************************************************************************************
	$path_theme = sfConfig::get('theme_simad');
	$base_path = sfConfig::get('base_simad');
	$urlApi = $base_path . "public/restapi/ventanilla/v1/%s";
	$mainpanel = '<div class="panel panel-success"><div class="panel-heading"><div class="panel-title">Lista de Resultados de la Consulta</div></div><div class="panel-body"><div class="alert alert-default"><strong>%s</strong>, Favor verificar.</div></div></div>';
	//************************************************************************************************

	// AUDITORIA...
    //AuditoriaPeer::addNewAuditoria();
	// AUDITORIA... FIN

    $radicado_entrada = '';
    if (isset($_POST['radicado'])) 
    {
        $radicado_entrada = trim($_POST['radicado']);
    }
	else 
	{
		$error_message = 'EL RADICADO O EL DOCUMENTO NO SON VALIDOS, codigo: RD007';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}

	$httpcoderesponse = 200;

    if(!empty($radicado_entrada))
    { 
        $com_enviada  = ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_entrada);
        if(!empty($com_enviada))
		{
			try
			{
				$list_users = $com_enviada->getBitacoraHistCom();
				$lista_digit_document = $com_enviada->getListDigitDocument();
				///////////////////////////////////////VALIDA ADJUNTO/////////////////////////////***************** */
				//***************************************************************************************
				$nombre_documento_externo = trim($_FILES['adjunto']['tmp_name']);
				$tempath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . md5(time());
				$tempath = simad_util::createPath($tempath);
				$nombre_documento_externo = $tempath . DIRECTORY_SEPARATOR . $_FILES['adjunto']['name'];
				@move_uploaded_file($_FILES['adjunto']['tmp_name'], $nombre_documento_externo);
				$error_message = '';
				$errorDocumento = false;
				if(!is_readable($nombre_documento_externo))
				{
					$errorDocumento = true;
					$error_message = 'SE HA PRODUCIDO UN ERROR CARGANDO SU DOCUMENTO, INTENTELO DE NUEVO, codigo: RD006';
					$result = sprintf($mainpanel, $error_message);
					exit($result);
				}
				//***************************************************************************************
				if($errorDocumento == false)
				{
					$nombre_documento_interno = $com_enviada->getPathImageDigitByCom();
					if(empty($nombre_documento_interno))
					{
						$nombre_documento_interno = $com_enviada->generateFileInDisk();
						if(empty($nombre_documento_interno))
						{
							$errorDocumento = true;
							$error_message = 'SE HA PRODUCIDO UN ERROR COTEJANDO SU DOCUMENTO, COMUNIQUESE CON EL ADMINISTRADOR, codigo: RD005';
							$result = sprintf($mainpanel, $error_message);
							exit($result);
						}
						else if(!is_readable($nombre_documento_interno))
						{
							$errorDocumento = true;
							$error_message = 'SE HA PRODUCIDO UN ERROR COTEJANDO SU DOCUMENTO, COMUNIQUESE CON EL ADMINISTRADOR, codigo: RD004';
							$result = sprintf($mainpanel, $error_message);
							exit($result);
						}
					}
				}
				$verificado_hash = true;
				$com_enviadas = null;
				//***************************************************************************************
				if($errorDocumento == false)
				{
					$hash_externo = hash_file('sha256', $nombre_documento_externo);
					$hash_interno = hash_file('sha256', $nombre_documento_interno);
					if($hash_externo !== $hash_interno) 
					{
						$verificado_hash = false;
						$error_message = 'EL RADICADO O EL DOCUMENTO NO SON VALIDOS, codigo: RD003';
						$result = sprintf($mainpanel, $error_message);
						exit($result);
					}
					else
					{
						$verificado_hash = true;
					}
					// titulos
					$com_enviadas['RADICADO'] = $com_enviada->getRadicado();
					$com_enviadas['USUARIO_ASIGNADO'] = isset($list_users['destinatario_actual']) ? $list_users['destinatario_actual'] : null;
					$com_enviadas['DEPENDENCIA_ASIGNADA'] = isset($list_users['ndependencia_actual']) ? $list_users['ndependencia_actual'] : null;
					//$coenviadasas['DEPENDENCIA_ASIGNADA'] = isset($list_users['dependencia_actual']) ? $list_users['dependencia_actual'] : null;
					$com_enviadas['USUARIO_RADICADOR'] = isset($list_users['radicador']) ? $list_users['radicador'] : null;
					$com_enviadas['DEPENDENCIA_RADICADOR'] = isset($list_users['dependencia_radicador']) ? $list_users['dependencia_radicador'] : null;
					$com_enviadas['FECHA_TRANSACCION'] = $fecha_transaccion;
					$com_enviadas['ISERROR'] = false;
					$com_enviadas['MSG_INFO'] = null;
					//****************************************************************************************************
					$com_enviadas['ADJUNTOS'] = $lista_digit_document; 
					//*************************************************************************************************************
					$wflist_acom = array();
					foreach ($list_users['eventos_list'] as $item) 
					{
						$wf_evento = array();
						// filas de la tabla
						$wf_evento['RADICADO'] = $com_enviada->getRadicado();
						$wf_evento['DEPENDENCIA'] = isset($item['dependencia']) ? $item['dependencia'] : null;
						$wf_evento['FECHA_ACTIVIDAD'] = isset($item['fecha_recibido']) ? $item['fecha_recibido'] : null;
						$wf_evento['ACTIVIDAD_FLUJO'] = isset($item['proceso']) ? $item['proceso'] : null;
						$wf_evento['USUARIO_ACTIVIDAD'] = isset($item['destinatario']) ? $item['destinatario'] : null;
						$wf_evento['OBSERVACIONES'] = isset($item['observaciones']) ? $item['observaciones'] : null;

						$wflist_acom[] = $wf_evento;
					}
					//*************************************************************************************************************
					$com_enviadas['RADICADO_FLUJO'] = $wflist_acom;
				}
			}
			catch(PropelException $pex)
			{
				$httpcoderesponse = 400;
				$error_message = "Se ha producido un error tipo PropelException: " . $pex->getMessage();
			}
			catch(\Exception $ex)
			{
				$httpcoderesponse = 400; 
				$error_message = "Se ha producido un error tipo Exception: " . $ex->getMessage(); 
			}
			catch(\Throwable $th)
			{
				$httpcoderesponse = 400;
				$error_message = "Se ha producido un error tipo Throwable: " . $th->getMessage();
			}	
		} 
		else
		{
			$com_enviadas = null;
			//$httpcoderesponse = 400; 
			$error_message = 'EL RADICADO O EL DOCUMENTO NO SON VALIDOS, codigo: RD002';
			$result = sprintf($mainpanel, $error_message);
			exit($result);
		}
    }
	else 
	{
		$error_message = 'EL RADICADO O EL DOCUMENTO NO SON VALIDOS, codigo: RD001';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}
    $includeDiv = false;
    include('_validarDocumentoView.php');
	http_response_code($httpcoderesponse);
?>
