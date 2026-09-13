<?php

/**
 * Subclass for representing a row from the 'WF_INSTANCIA_BITACORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfInstanciaBitacora extends BaseWfInstanciaBitacora
{
	static public function envioEmail($comunicacion_id,$user,$buzon_id){
        if(trim($comunicacion_id) != ""){
            $radicado ="";
            $fecha_radicacion = "";
            $asunto = "";
            $subject = "";
            $usuario = UsuarioPeer::retrieveByPK($user);
            switch($buzon_id){
                case 1:
                        $com_recibida_mail = ComRecibidaPeer::retrieveByPK($comunicacion_id);
                        $radicado = $com_recibida_mail->getRadicado();
                        $fecha_radicacion = $com_recibida_mail->getFechaCreacion();
                        $asunto = $com_recibida_mail->getAsunto();
                        $subject = ".::SIMAD.:: Workflow Comunicaciones Recibidas";
                break;
                case 2:
                        $com_interna_mail = ComInternaPeer::retrieveByPK($comunicacion_id);
                        $radicado = $com_interna_mail->getRadicado();
                        $fecha_radicacion = $com_interna_mail->getFechaCreacion();
                        $asunto = $com_interna_mail->getReferencia();
                        $subject = ".::SIMAD.:: Workflow Comunicaciones Internas";
                break;
            }
            $cuerpo = '
            <html>
            <head>
            <title></title>
            </head>
            <body>
            <div id="cotenedor">
            <br>
                Este es un mensaje para informarle que se le ha asignado una actividad en el workflow de la siguiente comunicacion: 
            <br>
            <br>
            Radicado: '.$radicado.'<br>
            Fecha radicacion: '.$fecha_radicacion.' <br>
            Asunto: '.$asunto.'<br>
            Fecha asignacion tramite: '.date("Y-m-d G:i:s").'<br>
            <br>
            </div>
            </body></html>';
            $cabeceras = "Content-type: text/html\r\n";
            //*********************************************************************************************************************
            $baseMail = new BaseMailSimad();
            $baseMail->SetSubject($subject);
            $baseMail->SetMsgHTML($cuerpo);
            $baseMail->SetAddAddress($usuario->getEmail(), $usuario->getEmail());    
            if($baseMail->InitSend() === true)
            {
               $baseMail->writetolog("Alerta enviada Radicado: " . $usuario->getEmail() . " Enviado a: " . $usuario->getEmail());
            }else{
               $baseMail->writetolog("Error al enviar alerta radicado: " . $usuario->getEmail() . " Cuenta correo: " . $usuario->getEmail());
            }
            //***********************************************************************************************************************
        }
    }
    
    public function getLinkImgAdjuntosCom(){
		$links = "";
		$path_theme = sfConfig::get('theme_simad');
		$base_path = sfConfig::get('base_simad');
		//************************************************************************
		if($this->getCominternaId()){
			$arr = explode(",",$this->getCominterna()->getRuta());
			foreach ($arr as $result):
				if(trim(basename($result))){
					$links = '
					<a class="tooltip-primary" data-toggle="tooltip" data-original-title="'.basename($result).'" href="'.($result).'" target="_blank">
						<img src="'.$base_path.'/images/simad/ico-adj-file.png" width="25" height="25" align="middle" />
					</a>
					';
				}
			endforeach;
		}elseif($this->getComrecibidaId()){			
			$arr = explode(",",$this->getComrecibida()->getRuta());
			foreach ($arr as $result):
				if(trim(basename($result))){
					$links = '
					<a class="tooltip-primary" data-toggle="tooltip" data-original-title="'.basename($result).'" href="'.($result).'" target="_blank">
	                   <img src="'.$base_path.'/images/simad/ico-adj-file.png" width="25" height="25" align="middle" />                                    
					</a> 
					';                           
				} 
            endforeach; 			                            
		}		
		return $links;
	}
}
