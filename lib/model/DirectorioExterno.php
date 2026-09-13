<?php

/**
 * Subclass for representing a row from the 'directorio_externo' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DirectorioExterno extends BaseDirectorioExterno
{
	function __toString(){
		return trim($this->getFuncionario()) ?  trim($this->getNombre())." | ".trim($this->getFuncionario()) : trim($this->getNombre());
	}
	
	function getSeleccione(){
		return "<javascript>"."</javascriptt>".$this->getNombre();
	}

	function getNombreAndNuid(){
		$text_name = htmlentities(trim($this->getNombre()));
		$nuid_text = trim($this->getNit()) ? trim($this->getNit()) : null;
		$nombre_compuesto = !empty($nuid_text) ? ($nuid_text." - ".$text_name) : $text_name;
		return $nombre_compuesto;
	}

	public function envioEmailNotificacion($radicado)
    {
		try {
			if(!empty($this->getEmail()) && !empty($radicado)){
				$cuerpo_email = "<p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:35.4pt;
				background:white;vertical-align:baseline'><span style='font-family:Arial Narrow,sans-serif;
				color:#201F1E;border:none windowtext 1.0pt;mso-border-alt:none windowtext 0cm;
				padding:0cm'>La Unidad Para la Atención y Reparación Integral a las Víctimas le
				informa que la comunicación enviada por usted (por&nbsp; correo electrónico o
				en físico), ha sido recibida con el número de radicado&nbsp;</span><span
				style='font-family:Arial Narrow,sans-serif;color:black;border:none windowtext 1.0pt;
				mso-border-alt:none windowtext 0cm;padding:0cm'><b>".$radicado."</b></span><span
				style='font-family:Arial Narrow,sans-serif;color:#201F1E;border:none windowtext 1.0pt;
				mso-border-alt:none windowtext 0cm;padding:0cm'> y será tramitada dentro de los
				términos establecidos por la Ley.<br>
				<br>
				<b>Por favor no responda este correo.</b><br>
				<br>Para información adicional por favor comuníquese a través de los siguientes canales de atención:<br><br>
				Línea de atención al ciudadano (+57 1) 426 11 11<br>
				Línea gratuita nacional 01 8000 911 119<br>
				Atención al ciudadano:&nbsp;</span><span style='color:black'><a
				href='mailto:servicioalciudadano@unidadvictimas.gov.co'><span style='font-family:
				Arial Narrow,sans-serif;border:none windowtext 1.0pt;mso-border-alt:none windowtext 0cm;
				padding:0cm'>servicioalciudadano@unidadvictimas.gov.co</span></a></span><span
				style='color:#323130'> <o:p></o:p></span></p>
				
				<p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:35.4pt;
				background:white;vertical-align:baseline'><span style='font-family:Arial Narrow,sans-serif;
				color:#201F1E;border:none windowtext 1.0pt;mso-border-alt:none windowtext 0cm;
				padding:0cm'>Para notificaciones judiciales:&nbsp;</span><span
				style='color:black'><ahref='mailto:notificaciones.juridicauariv@unidadvictimas.gov.co'><span
				style='font-family:Arial Narrow,sans-serif;border:none windowtext 1.0pt;
				mso-border-alt:none windowtext 0cm;padding:0cm'>notificaciones.juridicauariv@unidadvictimas.gov.co</span></a></span><span
				style='color:#323130'><o:p></o:p></span></p>
				
				<p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:35.4pt;
				background:white'><span style='font-family:Arial Narrow,sans-serif;
				color:#201F1E;border:none windowtext 1.0pt;mso-border-alt:none windowtext 0cm;
				padding:0cm'>Redes sociales: En twitter @UnidadVictimas, en
				Facebook/UnidadVictimas, YouTube/UnidadVictimas e Instagram @UnidadVictimas<br>
				Sitio Web:&nbsp;</span><span style='color:black'><a
				href='https://nam10.safelinks.protection.outlook.com/?url=http%3A%2F%2Fwww.unidadvictimas.gov.co%2F&amp;data=02%7C01%7Cpaola.correa%40unidadvictimas.gov.co%7C91a9b4afdc424075fb4b08d86bb882ee%7C5964d9f2aeb648d9a53d7ab5cb1d07e8%7C0%7C0%7C637377787493794445&amp;sdata=kK2%2FuanbxfV%2FiaC6TWbLI4k7R9%2BG3pDPx6oWB9NmxqA%3D&amp;reserved=0'
				target='_blank'><span style='font-family:Arial Narrow,sans-serif;border:none windowtext 1.0pt;
				mso-border-alt:none windowtext 0cm;padding:0cm'>www.unidadvictimas.gov.co</span></a></span><span
				style='font-family:Arial Narrow,sans-serif;color:#201F1E;border:none windowtext 1.0pt;
				mso-border-alt:none windowtext 0cm;padding:0cm'>&nbsp;</span><span
				style='color:#323130'><o:p></o:p></span></p>";
				//***********************************************************************************************
				$cuerpo = '
				<html>
				<head>
				<title></title>
				</head>
				<body>
				<div id="cotenedor">
				<br>'.$cuerpo_email.'<br>';
				//***********************************************************************************************
				$cuerpo .= '</div></body></html>';
				$cabeceras = "Content-type: text/html; charset=UTF-8\r\n";
				//***********************************************************************************************
				if(simad_util::validateEmail($this->getEmail())){
					$message = "La cuenta de correo no es valida";
					return array('IsSend' => false, 'message' => $message);
				}
				//***********************************************************************************************			
				$baseMail = new BaseMailSimad();
				$baseMail->SetSubject('Comunicaciones Externas Recibidas # Radicado '.$radicado);
				$baseMail->SetNameFrom("RADICADO SOLICITUD UNIDAD PARA LAS VICTIMAS");
				$baseMail->SetMsgHTML($cuerpo);
				$baseMail->SetEnableService(true);
				$baseMail->SetEnableReplyTo(true);
				$baseMail->SetAddAddress($this->getEmail(), $this->getEmail());
				if($baseMail->InitSend() === true){
					$message = "Alerta enviada: " . $radicado . " Enviado a: " . $this->getEmail();
					$baseMail->writetolog($message);
					return array('IsSend' => true, 'message' => $message);
				}else{
					$message = "Error al enviar alerta: " . $radicado . " Cuenta correo: " . $this->getEmail();
					$baseMail->writetolog($message);
					return array('IsSend' => false, 'message' => $message);
				}
			}else{
				return array('IsSend' => false, 'message' => "Error al enviar notificación del radicado, la cuenta de correo o el radicado no son validos");
			}
		} catch (\Exception $th) {
			return array('IsSend' => false, 'message' => $th->getMessage());
		}
    }
}
