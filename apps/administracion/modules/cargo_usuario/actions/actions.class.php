<?php

/**
 * cargo_usuario actions.
 *
 * @package    simad
 * @subpackage cargo_usuario
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class cargo_usuarioActions extends sfActions
{
  public function executeIndex()
  {
    $parametros = "a=1";
    $c = new Criteria();
    $c->setDistinct();
    $c->add(CargoUsuarioPeer::ES_PRINCIPAL,1,Criteria::NOT_EQUAL);
    
    if($this->getRequestParameter('usuario_id')){
       $c->add(CargoUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_id'));
       $parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id'); 
    }
    
    if($this->getRequestParameter('cargo_id')){
       $c->add(CargoUsuarioPeer::CARGO_ID,$this->getRequestParameter('cargo_id'));
       $parametros .= "&cargo_id=" . $this->getRequestParameter('cargo_id'); 
    }
    
    if($this->getRequestParameter('cargo_id')){
       $c->add(CargoUsuarioPeer::CARGO_ID,$this->getRequestParameter('cargo_id'));
       $parametros .= "&cargo_id=" . $this->getRequestParameter('cargo_id'); 
    }
    
    /*********************************************************************************************************/
	if ($this->getRequestParameter('fecha_inicio')) {		
        $c->add(CargoUsuarioPeer::FECHA_INICIO,$this->getRequestParameter('fecha_inicio').' 00:00:00',Criteria::GREATER_THAN);	    
	    $parametros .= "&fecha_inicio=" . $this->getRequestParameter('fecha_inicio');	    
	}
	/*********************************************************************************************************/
	if ($this->getRequestParameter('fecha_fin')) {		
        $c->add(CargoUsuarioPeer::FECHA_FIN,$this->getRequestParameter('fecha_fin').' 00:00:00',Criteria::LESS_THAN);	    
	    $parametros .= "&fecha_fin=" . $this->getRequestParameter('fecha_fin');	    
	}
	/*********************************************************************************************************/
    $c->addDescendingOrderByColumn(CargoUsuarioPeer::FECHA_CREACION);
    $pager = new sfPropelPager('CargoUsuario', 15);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
    $this->parametros = $parametros;
    //$this->cargo_usuarioList = CargoUsuarioPeer::doSelect(new Criteria());
  }

  public function executeCreate()
  {
    $this->form = new CargoUsuario();

    $this->setTemplate('edit');
  }


  public function executeConsultar()
  {
    $this->form = new CargoUsuario();
    
  }
  
  public function executeEdit($request)
  {
    $this->form = CargoUsuarioPeer::retrieveByPk($request->getParameter('cargousuario_id'));
  }

  public function executeUpdate($request)
  {    
    //$this->forward404Unless($request->isMethod('post'));    
    if (!$request->getParameter('cargo_usuario_id'))
    {
      $cargo_usuario = new CargoUsuario();
    }
    else
    {
      $cargo_usuario = CargoUsuarioPeer::retrieveByPk($request->getParameter('cargo_usuario_id'));        
      $this->forward404Unless($cargo_usuario);
    }    
    $cargo_usuario->setUsuarioId($request->getParameter('usuario_id'));
    $cargo_usuario->setCargoId($request->getParameter('cargo_id'));
    $cargo_usuario->setDependenciaId($request->getParameter('dependencia_id') ? $request->getParameter('dependencia_id') : null);
    $cargo_usuario->setFechaInicio($request->getParameter('fecha_inicio')); 
    $cargo_usuario->setFechaFin($request->getParameter('fecha_fin'));
    $cargo_usuario->setEsPrincipal(0);
    if(!$cargo_usuario->getPrimaryKey()){
        $cargo_usuario->setFechaCreacion(date("Y-m-d G:i:s"));
    }
    if($request->getParameter('es_actual')?'1':'0'){
       $cargo_usuario->setEsActual(1); 
    }else{
       $cargo_usuario->setEsActual(0); 
    }    
    $cargo_usuario->save();

    $this->redirect('cargo_usuario/index');    
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($cargo_usuario = CargoUsuarioPeer::retrieveByPk($request->getParameter('cargo_usuario_id')));
    $cargo_usuario->delete();
    $this->redirect('cargo_usuario/index');
  }
}
