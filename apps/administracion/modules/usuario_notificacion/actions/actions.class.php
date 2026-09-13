<?php

/**
 * usuario_notificacion actions.
 *
 * @package    simad
 * @subpackage usuario_notificacion
 * @author     Javier Fernando Charry
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class usuario_notificacionActions extends sfActions
{
  /**
  * Executes verificaPrilegio action
  */
  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	  if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		  $this->redirect(sfConfig::get('base_simad').'no_autorizado_cerrar.html');
  	}
  }
  
  /**
  * Executes verificaPrilegioCerrar action
  */
  public function verificaPrilegioCerrar($currentForm)
  { 
    $usuarioLoguiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuarioLoguiado)){
      $this->redirect(sfConfig::get('base_simad').'no_autorizado.html');
    }	  
  }

  /**
  * Executes preExecute action
  */
  public function preExecute()
  {    
    $isAuthenticated = $this->getUser()->isAuthenticated();
    $base_path = sfConfig::get('base_simad');
    if(!$isAuthenticated){
      $this->redirect($base_path."backend.php/security/login");
    }
  }

  /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  /**
  * Executes create action
  * Despliega formulario que permite crear un nuevo registro
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $usuario_id = $request->getParameter('usuario_id');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

    //$this->verificaPrilegioCerrar("usuario_notificacion/create");
    $this->setTemplate('edit');
    $this->usuario_notificacion = new UsuarioNotificacion();
    $this->usuario_notificacion->setUsuarioId($usuario_id);
  }

  /**
  * Executes edit action
  * Despliega formulario de edicion del registro seleccionado
  * @param sfRequest $request A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
    //$this->verificaPrilegioCerrar("usuario_notificacion/edit");
    $this->usuario_notificacion = UsuarioNotificacionPeer::retrieveByPk($request->getParameter('usuarionotificacion_id'));
  }

  /**
  * Executes update action
  * Despliega formulario de edicion del registro seleccionado
  * @param sfRequest $request A request object
  */
  public function executeUpdate(sfWebRequest $request)
  {
    //$this->verificaPrilegio("usuario_notificacion/edit");        
    $this->forward404Unless($request->isMethod('post'));
    
    if(!$request->getParameter('usuarionotificacion_id'))
    {
        $usuario_notificacion = new UsuarioNotificacion();
        $fecha_creacion = date("Y-m-d G:i:s");
    }else{
        $usuario_notificacion = UsuarioNotificacionPeer::retrieveByPk($request->getParameter('usuarionotificacion_id'));
        $fecha_creacion = $usuario_notificacion->getFechaCreacion();
    }
    //**********************************************************************************************
    $usuario_notificacion->setUsuarioId($request->getParameter('usuario_id'));
    $usuario_notificacion->setTiponotificacionId($request->getParameter('tiponotificacion_id'));
    $usuario_notificacion->setFechaCreacion($fecha_creacion);
    $usuario_notificacion->setFechaModificacion(date("Y-m-d G:i:s"));
    $usuario_notificacion->setEmailAsunto(trim($request->getParameter('email_asunto')));
    $usuario_notificacion->setEmailMensajeBody(trim($request->getParameter('email_mensaje_body')));
    $usuario_notificacion->save();
    //**********************************************************************************************
    $this->redirect('usuario_notificacion/saveAndClose');
  }

  public function executeSaveAndClose()
  {
    
  }
}
