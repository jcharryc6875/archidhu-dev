<?php

/**
 * prov_check_list_vigencia actions.
 *
 * @package    simad
 * @subpackage prov_check_list_vigencia
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_check_list_vigenciaActions extends sfActions
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
    $currentForm="prov_check_list_vigencia/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $c=new Criteria();
    $c->add(ProvCheckListVigenciaPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $cantidad=ProvCheckListVigenciaPeer::doCount($c);
    if($cantidad<1){
       $prov_check_list_preguntas = ProvCheckListPreguntaPeer::doSelect(new Criteria());
       foreach($prov_check_list_preguntas as $prov_check_list_pregunta ){
        $prov_check_list_vigencia= new ProvCheckListVigencia();
        $prov_check_list_vigencia->setProvCheckListPreguntaId($prov_check_list_pregunta->getPrimaryKey());
        $prov_check_list_vigencia->setProvPeriodoValidezId($request->getParameter('prov_periodo_validez_id'));
        $prov_check_list_vigencia->save();
       }
    }
    $this->prov_periodo_validez_id=$request->getParameter('prov_periodo_validez_id');   
    $this->prov_check_list_vigenciaList = ProvCheckListVigenciaPeer::doSelect($c);
  }
  
  public function executeHistorico($request)
  { 
    $currentForm="prov_check_list_vigencia/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $c=new Criteria();
    $c->add(ProvCheckListVigenciaHistoricoPeer::PROV_CHECK_LIST_VIGENCIA_ID,$request->getParameter('prov_check_list_vigencia_id'));
    $c->addDescendingOrderByColumn(ProvCheckListVigenciaHistoricoPeer::PROV_CHECK_LIST_VIGENCIA_HISTORICO_ID);
    $this->prov_check_list_vigencia_historicoList = ProvCheckListVigenciaHistoricoPeer::doSelect($c);    
   // $this->prov_periodo_validez_id=$request->getParameter('prov_periodo_validez_id');   
    
  }

  public function executeShow($request)
  {
    $this->prov_check_list_vigencia = ProvCheckListVigenciaPeer::retrieveByPk($request->getParameter('prov_check_list_vigencia_id'));
    $this->forward404Unless($this->prov_check_list_vigencia);
  }
  
  public function executeAlertacheckcompleto($request)
  {
    $periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $periodo_validez->setAvisoCheckList('SI'); ////TODO actualizar estado a SI
    $periodo_validez->save();
    $msge="Este mensaje es para informarle que se termino la tarea de check list del siguiente proveedor en SIMAD WEB 4.0: 
     Proveedor:".$periodo_validez->getProveedor()->getNombre();
     $this->enviarAlertas(10,$msge);
    
    //lo del action list
    $currentForm="prov_check_list_vigencia/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $c=new Criteria();
    $c->add(ProvCheckListVigenciaPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $cantidad=ProvCheckListVigenciaPeer::doCount($c);
    if($cantidad<1){
       $prov_check_list_preguntas = ProvCheckListPreguntaPeer::doSelect(new Criteria());
       foreach($prov_check_list_preguntas as $prov_check_list_pregunta ){
        $prov_check_list_vigencia= new ProvCheckListVigencia();
        $prov_check_list_vigencia->setProvCheckListPreguntaId($prov_check_list_pregunta->getPrimaryKey());
        $prov_check_list_vigencia->setProvPeriodoValidezId($request->getParameter('prov_periodo_validez_id'));
        $prov_check_list_vigencia->save();
       }
    }
    
    $this->prov_periodo_validez_id=$request->getParameter('prov_periodo_validez_id');   
    $this->prov_check_list_vigenciaList = ProvCheckListVigenciaPeer::doSelect($c);
    $this->setTemplate('index'); 
  }
  
  public function enviarAlertas($id_area,$msge,$usuario_id="")
  {
    
      $cuser=new Criteria();
      $cuser->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,$id_area);//aprobadores de un area especifica
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
        $baseMail->SetSubject('CAD : Proveedores');
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

  public function executeCreate()
  { 
    $this->form = new ProvCheckListVigenciaForm();
    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $currentForm="prov_check_list_vigencia/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegioCerrar($currentForm);
    //$this->form = new ProvCheckListVigenciaForm(ProvCheckListVigenciaPeer::retrieveByPk($request->getParameter('prov_check_list_vigencia_id')));
    $this->prov_check_list_vigencia=ProvCheckListVigenciaPeer::retrieveByPk($request->getParameter('prov_check_list_vigencia_id'));
  }

  public function executeUpdate($request)
  {
     $prov_check_list_vigencia=$this->prov_check_list_vigencia=ProvCheckListVigenciaPeer::retrieveByPk($request->getParameter('prov_check_list_vigencia_id'));
     $prov_check_list_vigencia->setDescripcion($request->getParameter('descripcion'));
     $prov_check_list_vigencia->setRespuesta($request->getParameter('respuesta'));
     $prov_check_list_vigencia->setObservaciones($request->getParameter('observaciones')); 
     $prov_check_list_vigencia->save();
     
     //inserta historico antes de guardar cambios
     $historico=new ProvCheckListVigenciaHistorico();
     $historico->setDescripcion($prov_check_list_vigencia->getDescripcion());
     $historico->setRespuesta($prov_check_list_vigencia->getRespuesta());
     $historico->setObservaciones($prov_check_list_vigencia->getObservaciones());
     $historico->setProvCheckListVigenciaId($request->getParameter('prov_check_list_vigencia_id'));
     $historico->setUsuarioId($this->getUser()->getAttribute('usuario_id', '', 'subscriber'));
     $historico->setFechaCreacion(date("Y-m-d H:i:s"));
     $historico->save();
     // fin historico
     
     
     $this->setTemplate('show');
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($prov_check_list_vigencia = ProvCheckListVigenciaPeer::retrieveByPk($request->getParameter('prov_check_list_vigencia_id')));

    $prov_check_list_vigencia->delete();

    $this->redirect('prov_check_list_vigencia/index');
  }
}
