<?php

/**
 * com_aprobaciones actions.
 *
 * @package    simad
 * @subpackage com_aprobaciones
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class com_aprobacionesActions extends sfActions
{
    
  public function envioEmail($user_email,$encabezado_cuerpo="",$destino,$remitente,$asunto,$fecha,$accion)
  {
    $usuario_email = UsuarioPeer::retrieveByPK($user_email);
    $usuario_ejecuta = UsuarioPeer::retrieveByPK($this->getUser()->getAttribute('usuario_id','', 'subscriber'));
    $nombre_user = $usuario_ejecuta->getNombre()." ".$usuario_ejecuta->getApellido();
    
    if(trim($encabezado_cuerpo) == "")
    {
      $encabezado_cuerpo = "Este es un mensaje para informarle que el usuario $nombre_user ha $accion el borrador de la siguiente Comunicacion Saliente:";
    }      	
    $cuerpo = '
    <html>
    <head>
    <title></title>
    </head>
    <body>
    <div id="cotenedor">
    <br>'.$encabezado_cuerpo.'     
    <br>
    <br>
    <b>Fecha Creacion:</b> '.$fecha.' <br>
    <b>Asunto:</b> '.$asunto.'<br>
    <b>Destinatario:</b> '.$destino.'<br>
    <b>Remitente:</b> '.$remitente.'<br>
    <br>
    </div>
    </body></html>';
    $cabeceras = "Content-type: text/html\r\n";
    //*********************************************************************************************************************
    $baseMail = new BaseMailSimad();
    $baseMail->SetSubject('Comunicaciones Salientes $accion Borrador');
    $baseMail->SetMsgHTML($cuerpo);
    $baseMail->SetAddAddress($usuario_email->getEmail(), $usuario_email->getEmail());    
    if($baseMail->InitSend() === true)
    {
       $baseMail->writetolog("Alerta enviada Radicado: " . $usuario_email->getEmail() . " Enviado a: " . $usuario_email->getEmail());
    }else{
       $baseMail->writetolog("Error al enviar alerta radicado: " . $usuario_email->getEmail() . " Cuenta correo: " . $usuario_email->getEmail());
    }
    //***********************************************************************************************************************
  }
  
  public function executeIndex()
  {
    $this->parametros="?a=1";
    $modulo_id = $this->getRequestParameter('modulo_id');    
    $consecutivo_id = $this->getRequestParameter('consecutivo_id');
    $this->parametros.="&modulo_id=".$modulo_id;
    $this->parametros.="&consecutivo_id=".$consecutivo_id;
    $c = new Criteria();
    $c->add(ComAprobacionPeer::CONSECUTIVO_ID,$consecutivo_id);
    $c->add(ComAprobacionPeer::MODULO_ID,$modulo_id);
    $this->com_aprobacionList = ComAprobacionPeer::doSelect($c);
  }

  public function executeCreate()
  {
    $this->form = new ComAprobacionForm();

    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $this->form = new ComAprobacionForm(ComAprobacionPeer::retrieveByPk($request->getParameter('comaprobacion_id')));
  }

  public function executeUpdate($request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new ComAprobacionForm(ComAprobacionPeer::retrieveByPk($request->getParameter('comaprobacion_id')));

    $this->form->bind($request->getParameter('com_aprobacion'));
    if ($this->form->isValid())
    {
      $com_aprobacion = $this->form->save();

      $this->redirect('com_aprobaciones/edit?comaprobacion_id='.$com_aprobacion->getComaprobacionId());
    }

    $this->setTemplate('edit');
  }

  public function executeComAccion($request)
  {
    $comaprobacion_id = $request->getParameter('comaprobacion_id');
    $com_aprobacion = ComAprobacionPeer::retrieveByPk($comaprobacion_id);
    $com_aprobacion->getEstadocomaprobacion();    
    $estado_id = $request->getParameter('estado_id');
    $modulo_id = $request->getParameter('modulo_id');
    $consecutivo_id = $request->getParameter('consecutivo_id');
    $usuario_id = $request->getParameter('user_id');
    if(trim($com_aprobacion->getObservaciones()))
    {
      $observaciones = $com_aprobacion->getObservaciones() .' | '.date("Y-m-d G:i:s").'=>'.$request->getParameter('observaciones');
    }
    else
    {
        $observaciones = trim($request->getParameter('observaciones'));    
    }
    $com_aprobacion->setEstadocomaprobacionId($estado_id);
    $com_aprobacion->setFechaEjecucion(date("Y-m-d G:i:s"));
    $com_aprobacion->setObservaciones($observaciones ? $observaciones : "");
    $com_aprobacion->save();
    $this->validSendAlert($modulo_id,$consecutivo_id,$usuario_id,$com_aprobacion->getEstadocomaprobacion());
    if($estado_id != 3){
        $this->redirect('com_aprobaciones/index?modulo_id='.$modulo_id.'&consecutivo_id='.$consecutivo_id.'&user_id='.$usuario_id);
    }
  }
    
  public function executeRechazar($request)
  {
    $this->com_aprobacion = ComAprobacionPeer::retrieveByPk($request->getParameter('comaprobacion_id'));
    $this->forward404Unless($this->com_aprobacion);
  }
  
  public function executeDelete($request)
  {
    $this->forward404Unless($com_aprobacion = ComAprobacionPeer::retrieveByPk($request->getParameter('comaprobacion_id')));

    $com_aprobacion->delete();

    $this->redirect('com_aprobaciones/index');
  }
  
  public function validSendAlert($modulo_id,$consecutivo,$usuario,$estado_text)
  {
    if(trim($usuario))
    {            
        switch($modulo_id)
        {
            case 4:
                $com_enviada = ComEnviadaPeer::retrieveByPK($consecutivo);
                $asunto = $com_enviada->getAsunto();
                $destino = $this->getDestinoComunicacion($consecutivo);
                $remite = $this->getFirstUsurioFirmaName($consecutivo);
                $fecha_radicacion = $com_enviada->getFechaCreacion();
                $this->envioEmail($usuario,"",$destino,$remite,$asunto,$fecha_radicacion,$estado_text);
            break;
            
            default:
                return;
            break;    
        }
    }
  }
  
  public function getFirstUsurioFirmaName($com_enviadaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(EnviadaUsuarioPeer::COMENVIADA_ID, $com_enviadaId);
	$c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 2);
    $result = EnviadaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$firmante = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
	  $cont=1;					
	}
	return $firmante;
  	
  }
  
  public function getDestinoComunicacion($comenviadaId)
  { 
  	$c=new Criteria();
	  $c->add(EnviadaDirectorioPeer::COMENVIADA_ID, $comenviadaId);
    $respo = EnviadaDirectorioPeer::doSelect($c);
    $destino_name = "";	         
	  foreach($respo as $res)
    {		
  		if($res->getRoldirenviadaId()==1)
      {  			
  			$destino_name = $res->getDirectorioExterno()->getNombre()." - ".$res->getDirectorioExterno()->getFuncionario();
  		}
    }
  	return $destino_name;  	
  }
  
}
