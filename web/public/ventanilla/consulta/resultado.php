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

		$error_message = 'EL RADICADO O LOS DEMAS DATOS DE ENTRADA NO SON VALIDOS, codigo: R004';
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
    $radicado_entrada = '';
    if (isset($_POST['radicado'])) 
    {
        $radicado_entrada = trim($_POST['radicado']);

		//opcionales
		$nuid = !empty($_POST['nuid']) ? trim($_POST['nuid']) : null;
		$pnombre = !empty($_POST['pnombre']) ? trim($_POST['pnombre']) : null;
		$papellido = !empty($_POST['papellido']) ? trim($_POST['papellido']) : null;
    }
	else 
	{
		//$result = 'El metodo no esta permitido';
		//http_response_code(400);
		//exit($result);

		$error_message = 'EL RADICADO O LOS DEMAS DATOS DE ENTRADA NO SON VALIDOS, codigo: R003';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}

    if(!empty($radicado_entrada))
    { 
        $com_recibida  = ComRecibidaPeer::getComByOpcionales($radicado_entrada, $nuid, $pnombre, $papellido);

        if(!empty($com_recibida)) 
		{
			try
			{
				$list_users = $com_recibida->getBitacoraHistCom();
				$lista_digit_document = $com_recibida->getListDigitDocument();

				// titulos
				$com_recibidas['RADICADO'] = $com_recibida->getRadicado();
				$com_recibidas['USUARIO_ASIGNADO'] = isset($list_users['destinatario_actual']) ? $list_users['destinatario_actual'] : null;
				$com_recibidas['DEPENDENCIA_ASIGNADA'] = isset($list_users['ndependencia_actual']) ? $list_users['ndependencia_actual'] : null;
				//$com_recibidas['DEPENDENCIA_ASIGNADA'] = isset($list_users['dependencia_actual']) ? $list_users['dependencia_actual'] : null;
				$com_recibidas['USUARIO_RADICADOR'] = isset($list_users['radicador']) ? $list_users['radicador'] : null;
				$com_recibidas['DEPENDENCIA_RADICADOR'] = isset($list_users['dependencia_radicador']) ? $list_users['dependencia_radicador'] : null;
				
				$com_recibidas['FECHA_TRANSACCION'] = $fecha_transaccion;
				$com_recibidas['ISERROR'] = false;
				$com_recibidas['MSG_INFO'] = null;
				//*************************************************************************************************************
				$com_recibidas['ADJUNTOS'] = $lista_digit_document;
				//$com_recibidas['DIGITS'] = 1;
				//*************************************************************************************************************
				$wflist_acom = array();
				foreach ($list_users['eventos_list'] as $item) 
				{
					$wf_evento = array();
					// filas de la tabla
					$wf_evento['RADICADO'] = $com_recibida->getRadicado();
					$wf_evento['DEPENDENCIA'] = isset($item['dependencia']) ? $item['dependencia'] : null;
					$wf_evento['FECHA_ACTIVIDAD'] = isset($item['fecha_recibido']) ? $item['fecha_recibido'] : null;
					$wf_evento['ACTIVIDAD_FLUJO'] = isset($item['proceso']) ? $item['proceso'] : null;
					$wf_evento['USUARIO_ACTIVIDAD'] = isset($item['destinatario']) ? $item['destinatario'] : null;
					$wf_evento['OBSERVACIONES'] = isset($item['observaciones']) ? $item['observaciones'] : null;
					$wflist_acom[] = $wf_evento;
				}
				//*************************************************************************************************************
				//*************************************************************************************************************
				//BURBUJAS
				$comrecibida_id = $com_recibida->getPrimaryKey();
        		$array_process_current = ComrecibidaUsuarioPeer::getCurrentListProcess($comrecibida_id);

				//intento leer el Enum y pasarlo a array...
				$array_StatusPublicacion = OficinaVirtualProcess::getAll();				

				//obtener proceso actual ESTA_ASIGNADA = 1
				$current_process_string = '';
				$current_process_id = 0;
				$array_descripciones = [];

				foreach ($array_process_current as $element) 
				{
					$array_descripciones[] = $element['PROCESO_ACTUAL'];

					if (isset($element['ESTA_ASIGNADA']) && $element['ESTA_ASIGNADA'] == '1') 
					{
						//if (isset($element['PROCESO_ACTUAL']) && $current_process_string = '' && $current_process_id = 0) 
						if (isset($element['PROCESO_ACTUAL'])) 
						{
							$current_process_string = $element['PROCESO_ACTUAL']; // Distribuidor
							$current_process_id = $element['TIPOPROCESOCOM_ID']; // 2
							break;
						}
					}
				}
				
				$array_descripciones = array_unique($array_descripciones);
				
				$array_vista_data = [];
				$array_vista_data['current_process_string'] = $current_process_string;
				$array_vista_data['current_process_id'] = $current_process_id;
				$array_vista_data['array_descripciones'] = $array_descripciones;
				$array_vista_data['status_publicacion'] = $array_StatusPublicacion;


				$comenviada_id = $com_recibida->getComEnviadaId();
				if(!empty($comenviada_id))
				{
					$com_enviada = ComEnviadaPeer::retrieveByPk($comenviada_id); 

					$servicio_id = $com_enviada->getServicioId();

					if(!empty($servicio_id))
					{
						$servicio = ServicioPeer::retrieveByPk($servicio_id);
						$servicioestado_id = $servicio->getServicioestadoId();
						$servicioestado = ServicioEstadoPeer::retrieveByPk($servicioestado_id);
						$descripcion = $servicioestado->getDescripcion();

						if($descripcion === 'Ejecutada')
						{
							$array_vista_data['current_process_id'] = OficinaVirtualProcess::Notificacion;
						}
						else if($descripcion === 'Ejecutado Devuelto')
						{
							$array_vista_data['current_process_id'] = OficinaVirtualProcess::Notificacion;
							$array_vista_data['warning'] = 'Advertencia: El proceso aun no termina';
						}
						else
						{
							$array_vista_data['current_process_id'] = OficinaVirtualProcess::Respuesta;
						}
						
					}
					else
					{
						$array_vista_data['current_process_id'] = OficinaVirtualProcess::Respuesta;
					}
				}

				//*************************************************************************************************************
				$com_recibidas['RADICADO_FLUJO'] = $wflist_acom;
				/* segunda parte */
				//$comenviada_id = $com_recibida->getComEnviadaId();
				if(!empty($comenviada_id))
				{
					$com_enviada = ComEnviadaPeer::retrieveByPk($comenviada_id); 
					
					$bitac_com_enviada = $com_enviada->getBitacoraHistCom();
					$anexos_com_enviada = $com_enviada->getListDigitDocument();
				}
				else
				{
					$com_enviada = null; 
					$bitac_com_enviada = null;
					$anexos_com_enviada = null;
				}

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
			$com_recibidas = null;
			$error_message = 'EL RADICADO O LOS DEMAS DATOS DE ENTRADA NO SON VALIDOS, codigo: R002';
			$result = sprintf($mainpanel, $error_message);
			exit($result);
		}
    }
	else 
	{
		$error_message = 'EL RADICADO O LOS DEMAS DATOS DE ENTRADA NO SON VALIDOS, codigo: R001';
		$result = sprintf($mainpanel, $error_message);
		exit($result);
	}
    $includeDiv = false;
    include('_customBodyViews.php');
?>
