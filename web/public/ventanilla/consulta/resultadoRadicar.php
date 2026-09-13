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
			exit; 
		}
		else //si el captcha SI VALIDA CORRECTAMENTE... 
		{ 
			//var_dump($_POST); exit; 
			include_once('../../restapi/ventanilla/v1/index.php');
		}
	} 
	else 
	{
		$result = 'El metodo no esta permitido';
		http_response_code(400);
		exit($result);
	}
	
?>
