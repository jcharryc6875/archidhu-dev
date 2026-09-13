<?php

/**
 * prov_aprobacion actions.
 *
 * @package    simad
 * @subpackage prov_aprobacion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_aprobacionActions extends sfActions
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
    $parametros_consulta="";
    $currentForm="prov_aprobacion/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $userAprobadores = array();
    $cont = 0;
    $c=new Criteria();
    $c->add(ProvAprobacionPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $parametros_consulta .= "&prov_periodo_validez_id=".$request->getParameter('prov_periodo_validez_id'); 
    $prov_aprobacionList = ProvAprobacionPeer::doSelect($c);
    
    foreach ($prov_aprobacionList as $prov_aprobacion){
    	$usuario = UsuarioPeer::retrieveByPK($prov_aprobacion->getUsuarioId());
		$userAprobadores[$cont] = $usuario->getNombre()." ".$usuario->getApellido();
		$cont++;
	}
	$this->prov_aprobacionList = $prov_aprobacionList;
	$this->usuariosAprobadores = $userAprobadores;
    $this->prov_periodo_validez_id=$request->getParameter('prov_periodo_validez_id');
     $this->parametros_consulta=$parametros_consulta;
  }
  
  public function executeExcel($request)
  {
    
    $c = new Criteria();
    $parametros_consulta="";
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    $this->periodo_validez = ProvPeriodoValidezPeer::retrieveByPK($prov_periodo_validez_id);
    $this->usuario_name = UsuarioPeer::retrieveByPK($this->getUser()->getAttribute('usuario_id', '', 'subscriber'));
    
    if($prov_periodo_validez_id != ""){
		$c->add(ProvAprobacionPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
		$parametros_consulta .= "&prov_periodo_validez_id=".$prov_periodo_validez_id;		
	}        
    
    $c->clearSelectColumns();
    
    $c->addJoin(ProvAprobacionPeer::PROV_ESTADO_APROBACION_ID,ProvEstadoAprobacionPeer::PROV_ESTADO_APROBACION_ID);
    $c->addJoin(ProvAprobacionPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        
	$c->addSelectColumn(UsuarioPeer::NOMBRE);//0
	$c->addSelectColumn(UsuarioPeer::APELLIDO);//1
	$c->addSelectColumn(ProvEstadoAprobacionPeer::DESCRIPCION);//2
    $c->addSelectColumn(ProvAprobacionPeer::FECHA_CREACION);//3
	$c->addSelectColumn(ProvAprobacionPeer::OBSERVACIONES);//4

    $this->resultset = ProvDocumentoPeer::doSelectStmt($c);
    
    $this->parametros_consulta=$parametros_consulta;
    
  }
  
  public function executeShow($request)
  {
    $this->prov_aprobacion = ProvAprobacionPeer::retrieveByPk($request->getParameter('prov_aprobacion_id'));
    $this->forward404Unless($this->prov_aprobacion);
  }

  public function executeCreate()
  {
    $this->form = new ProvAprobacionForm();

    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $this->form = new ProvAprobacionForm(ProvAprobacionPeer::retrieveByPk($request->getParameter('prov_aprobacion_id')));
  }

  public function executeUpdate($request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new ProvAprobacionForm(ProvAprobacionPeer::retrieveByPk($request->getParameter('prov_aprobacion_id')));

    $this->form->bind($request->getParameter('prov_aprobacion'));
    if ($this->form->isValid())
    {
      $prov_aprobacion = $this->form->save();

      $this->redirect('prov_aprobacion/edit?prov_aprobacion_id='.$prov_aprobacion->getProvAprobacionId());
    }

    $this->setTemplate('edit');
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($prov_aprobacion = ProvAprobacionPeer::retrieveByPk($request->getParameter('prov_aprobacion_id')));

    $prov_aprobacion->delete();

    $this->redirect('prov_aprobacion/index');
  }
}
