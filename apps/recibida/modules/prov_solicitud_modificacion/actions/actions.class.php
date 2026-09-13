<?php

/**
 * prov_solicitud_modificacion actions.
 *
 * @package    simad
 * @subpackage prov_solicitud_modificacion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_solicitud_modificacionActions extends sfActions
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
      
  public function executeIndex()
  { 
    
    $currentForm="prov_solicitud_modificacion/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $parametros = "a=1";
	$papelera = false;	  	
	$c = new Criteria();  	
	$c->setDistinct();


	/***************************************************************************************/
	if ($this->getRequestParameter('descripcion')) {
	    $c->add(ProvSolicitudModificacionPeer::DESCRIPCION, '%' . $this->getRequestParameter('descripcion') .'%', Criteria::LIKE);
	    $parametros .= "&descripcion=" . $this->getRequestParameter('descripcion');
	} 
    
    if ($this->getRequestParameter('usuario_id')) {
	    $c->add(ProvSolicitudModificacionPeer::USUARIO_ID, $this->getRequestParameter('usuario_id'));
	    $parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id');
	} 
    
    if ($this->getRequestParameter('prov_estado_sol_mod_id')) {
	    $c->add(ProvSolicitudModificacionPeer::PROV_ESTADO_SOL_MOD_ID,$this->getRequestParameter('prov_estado_sol_mod_id') );
	    $parametros .= "&prov_estado_sol_mod_id=" . $this->getRequestParameter('prov_estado_sol_mod_id');
	}  
    
    if ($this->getRequestParameter('proveedor_nombre')) {
	    $c->addJoin(ProvSolicitudModificacionPeer::PROVEEDOR_ID,ProveedorPeer::PROVEEDOR_ID);
		$c->add(ProveedorPeer::NOMBRE, '%' . $this->getRequestParameter('proveedor_nombre') .'%', Criteria::LIKE);
	    $parametros .= "&proveedor_nombre=" . $this->getRequestParameter('proveedor_nombre');
        
	} 
    
	if ($this->getRequestParameter('ordenar')== "FECHA_CREACION") {		
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
	}elseif ($this->getRequestParameter('ordenar')== "USUARIO_ID") {
		$c->addJoin(ProvSolicitudModificacionPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
		//$c->addSelectColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar')); 
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
    }elseif ($this->getRequestParameter('ordenar')== "NOMBRE") {
    	//$c->addJoin(ProvSolicitudModificacionPeer::PROVEEDOR_ID,ProveedorPeer::PROVEEDOR_ID); 
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::PROVEEDOR_ID);
    }elseif ($this->getRequestParameter('ordenar')== "FECHA_RESPUESTA") {
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
    }
	       
    $pager = new sfPropelPager('ProvSolicitudModificacion', 10);
	$pager->setCriteria($c);    
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	//$pager->setPeerMethod('doSelectByTags');	
	$this->pager = $pager;
	$this->parametros = $parametros;
    
   // $this->prov_solicitud_modificacionList = ProvSolicitudModificacionPeer::doSelect(new Criteria());
  }
  
  public function executeExcel()
  { 
    
    /*
    $currentForm="com_recibida/list";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    */
    $parametros = "a=1";
	$papelera = false;	  	
	$c = new Criteria();  	
	$c->setDistinct();


	/***************************************************************************************/
	if ($this->getRequestParameter('descripcion')) {
	    $c->add(ProvSolicitudModificacionPeer::DESCRIPCION, '%' . $this->getRequestParameter('descripcion') .'%', Criteria::LIKE);
	    $parametros .= "&descripcion=" . $this->getRequestParameter('descripcion');
	} 
    
    if ($this->getRequestParameter('usuario_id')) {
	    $c->add(ProvSolicitudModificacionPeer::USUARIO_ID, $this->getRequestParameter('usuario_id'));
	    $parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id');
	} 
    
    if ($this->getRequestParameter('prov_estado_sol_mod_id')) {
	    $c->add(ProvSolicitudModificacionPeer::PROV_ESTADO_SOL_MOD_ID,$this->getRequestParameter('prov_estado_sol_mod_id') );
	    $parametros .= "&prov_estado_sol_mod_id=" . $this->getRequestParameter('prov_estado_sol_mod_id');
	}  
    
    if ($this->getRequestParameter('proveedor_nombre')) {
	    $c->addJoin(ProvSolicitudModificacionPeer::PROVEEDOR_ID,ProveedorPeer::PROVEEDOR_ID);
		$c->add(ProveedorPeer::NOMBRE, '%' . $this->getRequestParameter('proveedor_nombre') .'%', Criteria::LIKE);
	    $parametros .= "&proveedor_nombre=" . $this->getRequestParameter('proveedor_nombre');
        
	} 
    
	if ($this->getRequestParameter('ordenar')== "FECHA_CREACION") {		
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
	}elseif ($this->getRequestParameter('ordenar')== "USUARIO_ID") {
		$c->addJoin(ProvSolicitudModificacionPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
		//$c->addSelectColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar')); 
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
    }elseif ($this->getRequestParameter('ordenar')== "NOMBRE") {
    	//$c->addJoin(ProvSolicitudModificacionPeer::PROVEEDOR_ID,ProveedorPeer::PROVEEDOR_ID); 
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::PROVEEDOR_ID);
    }elseif ($this->getRequestParameter('ordenar')== "FECHA_RESPUESTA") {
    	$c->addDescendingOrderByColumn(ProvSolicitudModificacionPeer::$this->getRequestParameter('ordenar'));
    }   
    
    //$pager = new sfPropelPager('ProvSolicitudModificacion', 10);
//	$pager->setCriteria($c);    
//	$pager->setPage($this->getRequestParameter('page',1));
//	$pager->init();
	//$pager->setPeerMethod('doSelectByTags');	
	$this->pager = ProvSolicitudModificacionPeer::doSelect($c);
	$this->parametros = $parametros;
    
   // $this->prov_solicitud_modificacionList = ProvSolicitudModificacionPeer::doSelect(new Criteria());
  }
  
  public function executeShow()
  {   	  	  	
  	$currentForm="prov_solicitud_modificacion/show";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegioCerrar($currentForm);
    
  	$this->prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($this->getRequestParameter('prov_solicitud_modificacion_id'));  	
  }

  public function executeCreate()
  { 
    $currentForm="prov_solicitud_modificacion/create";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $this->prov_solicitud_modificacion = new ProvSolicitudModificacion();

    $this->setTemplate('edit');
  }

  public function executeConsultar()
  { 
    $currentForm="prov_solicitud_modificacion/consultar";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $this->prov_solicitud_modificacion = new ProvSolicitudModificacion();

  }  

  public function executeEdit()
  { 
    $currentForm="prov_solicitud_modificacion/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $this->prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($this->getRequestParameter('prov_solicitud_modificacion_id'));
  }

  public function executeUpdate($request)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if (!$this->getRequestParameter('prov_solicitud_modificacion_id'))
    {
      $prov_solicitud_modificacion = new ProvSolicitudModificacion();
      $redirectShow=0;
    }
    else
    {
      $prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($this->getRequestParameter('prov_solicitud_modificacion_id'));
      $redirectShow=1;
      $this->forward404Unless($prov_solicitud_modificacion);
    }
    
    $prov_solicitud_modificacion->setProveedorId($this->getRequestParameter('proveedor_id'));
    $prov_solicitud_modificacion->setFechaCreacion(Date("Y-m-d h:m:s"));
	$prov_solicitud_modificacion->setDescripcion($request->getParameter('descripcion'));    
    $prov_solicitud_modificacion->setUsuarioId($usuariologuiado);
    $prov_solicitud_modificacion->setProvEstadoSolModId(1);
    $prov_solicitud_modificacion->save();
    
    if($redirectShow){
         $this->redirect('prov_solicitud_modificacion/show?prov_solicitud_modificacion_id='.$this->getRequestParameter('prov_solicitud_modificacion_id')); 
    }
    else{
       $this->redirect('prov_solicitud_modificacion/index'); 
    }
    
  }
  
  public function executeRespuesta()
  { 
    $currentForm="prov_solicitud_modificacion/respuesta";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $this->prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($this->getRequestParameter('prov_solicitud_modificacion_id'));
  }
  
  public function executeUpdaterespuesta($request)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($this->getRequestParameter('prov_solicitud_modificacion_id'));
    
    $prov_solicitud_modificacion->setFechaRespuesta(Date("Y-m-d h:m:s"));
    $prov_solicitud_modificacion->setProvEstadoSolModId($this->getRequestParameter('prov_estado_sol_mod_id'));
	$prov_solicitud_modificacion->setRespuestaSolicitud($request->getParameter('respuesta_solicitud'));    
    $prov_solicitud_modificacion->save();
    
         $this->redirect('prov_solicitud_modificacion/show?prov_solicitud_modificacion_id='.$this->getRequestParameter('prov_solicitud_modificacion_id')); 
    
    
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($prov_solicitud_modificacion = ProvSolicitudModificacionPeer::retrieveByPk($request->getParameter('prov_solicitud_modificacion_id')));

    $prov_solicitud_modificacion->delete();

    $this->redirect('prov_solicitud_modificacion/index');
  }
}
