<?php

/**
 * prov_estado_flujo_periodo actions.
 *
 * @package    simad
 * @subpackage prov_estado_flujo_periodo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_estado_flujo_periodoActions extends sfActions
{

  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}	 
  }
  
  public function verificaPrilegioCerrar($currentForm)
  { 
 	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}	 	  
  } 
      
  public function executeIndex($request)
  { 
    $currentForm="prov_estado_flujo_periodo/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $c=new Criteria();
    $c->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $this->prov_aprobacionList = ProvEstadoFlujoPeriodoPeer::doSelect($c);    
    $this->prov_estado_flujo_periodoList = ProvEstadoFlujoPeriodoPeer::doSelect($c);
    $this->prov_periodo_validez_id=$request->getParameter('prov_periodo_validez_id');
  }

  public function executeShow($request)
  {
    $this->prov_estado_flujo_periodo = ProvEstadoFlujoPeriodoPeer::retrieveByPk($request->getParameter('prov_estado_flujo_periodo_id'));
    $this->forward404Unless($this->prov_estado_flujo_periodo);
  }

  public function executeCreate()
  {
    $this->form = new ProvEstadoFlujoPeriodoForm();

    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  { 
    $currentForm="prov_estado_flujo_periodo/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $this->prov_estado_flujo_periodo = ProvEstadoFlujoPeriodoPeer::retrieveByPk($request->getParameter('prov_estado_flujo_periodo_id'));
  }

  public function executeUpdate($request)
  {
    $prov_estado_flujo_periodo = ProvEstadoFlujoPeriodoPeer::retrieveByPk($request->getParameter('prov_estado_flujo_periodo_id'));
    $prov_estado_flujo_periodo->setProvEstadoAprobacionId($request->getParameter('prov_estado_aprobacion'));
    $prov_estado_flujo_periodo->setDescripcion($request->getParameter('descripcion'));
    $prov_estado_flujo_periodo->save();
    if($request->getParameter('prov_estado_aprobacion') ==2){
        $msge="Este mensaje es para informarle que se le reasingno la aprobacion de un proveedor para su revision en SIMAD WEB 4.0: 
         Proveedor:".$prov_estado_flujo_periodo->getProvPeriodoValidez()->getProveedor();
         
         $this->enviarAlertas($prov_estado_flujo_periodo->getProvItemFlujoId(),$msge);
     }
     
    $this->redirect('prov_estado_flujo_periodo/index?prov_periodo_validez_id='.$prov_estado_flujo_periodo->getProvPeriodoValidezId());
  }
 
   public function enviarAlertas($item,$msge,$usuario_id=""){
    
      $cuser=new Criteria();
      $cuser->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,$item);//aprobadores de un area especifica
      if($usuario_id != ""){
        $cuser->add(ProvUsuarioAreaPeer::USUARIO_ID,$usuario_id);//enviar alerta a un usuario especifico
      }
      $cuser->add(ProvUsuarioAreaPeer::PROV_ESTADO_APROBADOR_ID,1);//1 es activo, 2 es inactivo
      $usuarios_email=ProvUsuarioAreaPeer::doSelect($cuser);   
      
      foreach($usuarios_email as $usuario_email)
      {
        //*********************************************************************************************************************
        $mail_destino = $usuario_email->getUsuario()->getEmail();
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('Area Proveedores : Aprobaciones');
        $baseMail->SetMsgHTML($msge);
        $baseMail->SetAddAddress($mail_destino, $mail_destino);    
        if($baseMail->InitSend() === true)
        {
           $baseMail->writetolog("Alerta enviada: " . $mail_destino . " Enviado a: " . $mail_destino);
        }else{
           $baseMail->writetolog("Error al enviar alerta: " . $mail_destino . " Cuenta correo: " . $mail_destino);
        }
        //***********************************************************************************************************************
      }     
  }
  
  public function executeDelete($request)
  {
    $this->forward404Unless($prov_estado_flujo_periodo = ProvEstadoFlujoPeriodoPeer::retrieveByPk($request->getParameter('prov_estado_flujo_periodo_id')));

    $prov_estado_flujo_periodo->delete();

    $this->redirect('prov_estado_flujo_periodo/index');
  }
}
