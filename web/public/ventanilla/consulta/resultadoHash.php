<?php
	$fecha_transaccion = date("Y-m-d G:i:s");
	$com_enviadas = array();
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
		$error_message = 'EL RADICADO O EL CSV NO SON VALIDOS, codigo: R005';
		$result = sprintf($mainpanel, $error_message);
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
    $hash_externo = '';
    if (isset($_POST['radicado']) && isset($_POST['strhash'])) 
    {
        $radicado_entrada = trim($_POST['radicado']);
        $hash_externo = trim($_POST['strhash']);
    }
	else 
	{
		$error_message = 'EL RADICADO O EL CSV NO SON VALIDOS, codigo: R004';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}

    if(!empty($radicado_entrada))
    { 
        $com_enviada  = ComEnviadaPeer::getObjectComByRadicadoOrId($radicado_entrada);
        if(!empty($com_enviada))
		{
			try
			{
				// VALIDACION DEL HASH *****************************************************************************
				//****************************************************************************************************
				// invocar app.ini
				$read_sections = array('keyprivate_watermark', 'url_virtual');
				$ini_array = simad_util::readConfigFileApp($read_sections);
				$keyprivate = isset($ini_array['keyprivate_watermark']) ? $ini_array['keyprivate_watermark'] : null;

				$hash_interno = ComEnviadaPeer::getHashComData($com_enviada);
				$encrypted_string = simad_util::encrypt_decrypt('encrypt', $hash_interno, $keyprivate);

				if($encrypted_string !== $hash_externo) 
				{
					$verificado_hash = false;
					//$httpcoderesponse = 'R002'; // HASH NO CORRESPONDE CON EL NUMERO DE RADICADO
					$error_message = 'EL RADICADO O EL CSV NO SON VALIDOS, codigo: R003';
					$result = sprintf($mainpanel, $error_message);
					exit($result);
				}
				else
				{
					$verificado_hash = true;
				}
				//****************************************************************************************************
				$list_users = $com_enviada->getBitacoraHistCom();
				$lista_digit_document = $com_enviada->getListDigitDocument();
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
				//$com_recibidas['DIGITS'] = 1;
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
			catch(PropelException $pex)
			{
				$httpcoderesponse = 400;
				echo "Se ha producido un error tipo PropelException: " . $pex->getMessage();
			}
			catch(\Exception $ex)
			{
				$httpcoderesponse = 400; 
				echo "Se ha producido un error tipo Exception: " . $ex->getMessage(); 
			}
			catch(\Throwable $th)
			{
				$httpcoderesponse = 400;
				echo "Se ha producido un error tipo Throwable: " . $th->getMessage();
			}
		}
		else
		{
			// NUMERO DE RADICADO NO ENCONTRADO
			$com_enviadas = null;
			$error_message = 'EL RADICADO O EL CSV NO SON VALIDOS, codigo: R002';
			$result = sprintf($mainpanel, $error_message);
			exit($result);
		}
    }
	else 
	{
		$error_message = 'EL RADICADO O EL CSV NO SON VALIDOS, codigo: R001';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}
    $includeDiv = false; 
    include('_validarHashView.php');
	http_response_code($httpcoderesponse);
?>
