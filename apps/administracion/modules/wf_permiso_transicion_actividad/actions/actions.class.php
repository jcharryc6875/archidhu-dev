<?php

/**
 * wf_permiso_transicion_actividad actions.
 *
 * @package    symfony
 * @subpackage wf_permiso_transicion_actividad
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class wf_permiso_transicion_actividadActions extends sfActions
{
  
  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
	}
  
  public function executeIndex()
  {
    $this->verificaPrilegio("wf_permiso_transicion_actividad/list");
  	$c=new Criteria();
  	$this->parametros="a=1";
   	$usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
  	
  	if($this->getRequestParameter('wf_actividad_id'))
    {	
	      $c->add(WfPermisoTransicionActividadPeer::WF_ACTIVIDAD_ID,$this->getRequestParameter('wf_actividad_id'));		 
	      $this->parametros.="&wf_actividad_id=".$this->getRequestParameter('wf_actividad_id');			 	
	  }
    
    if($this->getRequestParameter('wf_transicion_id'))
    {	
	      $c->add(WfPermisoTransicionActividadPeer::WF_TRANSICION_ID,$this->getRequestParameter('wf_transicion_id'));		 
	      $this->parametros.="&wf_transicion_id=".$this->getRequestParameter('wf_transicion_id');
        $this->wf_transicion_id=$this->getRequestParameter('wf_transicion_id');			 	
	  }
	
  if($this->getRequestParameter('orden'))
  {
	 	$c->addDescendingOrderByColumn(WfPermisoTransicionActividadPeer::$this->getRequestParameter('orden'));		
		$this->parametros.="&orden=".$this->getRequestParameter('orden');			 	
	 }
	 else
   {
		$c->addAscendingOrderByColumn(WfPermisoTransicionActividadPeer::WF_TRANSICION_ID);
	}	 
	
  $pager=new sfPropelPager('WfPermisoTransicionActividad',15);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	
	$this->pager=$pager;   
	$this->controlPaginacion = 1;	
  }

  public function executeShow($request)
  {
    $this->wf_permiso_transicion_actividad = WfPermisoTransicionActividadPeer::retrieveByPk($request->getParameter('wf_permiso_transicion_actividad_id'));
    $this->forward404Unless($this->wf_permiso_transicion_actividad);
  }

  public function executeCreate()
  {
    $this->verificaPrilegio("wf_permiso_transicion_actividad/create");
    
    $this->wf_permiso_transicion_actividad = new WfPermisoTransicionActividad();
    
    $this->wf_permiso_transicion_actividad->setWfTransicionId($this->getRequestParameter('wf_transicion_id'));
    
    $this->setTemplate('edit');
  }
  
  public function executeConsulta()
  { 
     $this->verificaPrilegio("wf_permiso_transicion_actividad/consulta");    
     $this->wf_permiso_transicion_actividad = new WfPermisoTransicionActividad();
     $this->wf_permiso_transicion_actividad->setWfTransicionId($this->getRequestParameter('wf_transicion_id'));
     $this->wf_transicion_id = $this->getRequestParameter('wf_transicion_id');
  }

  public function executeEdit($request)
  {
    $this->verificaPrilegio("wf_permiso_transicion_actividad/edit");
    $this->wf_permiso_transicion_actividad = WfPermisoTransicionActividadPeer::retrieveByPk($request->getParameter('wf_permiso_transicion_actividad_id'));
    $this->forward404Unless($this->wf_permiso_transicion_actividad);
  }

  public function executeUpdate($request)
  {
    
    if (!$this->getRequestParameter('wf_permiso_transicion_actividad_id'))
    {
      $wf_permiso_transicion_actividad = new WfPermisoTransicionActividad();
    }
    else
    {
      $wf_permiso_transicion_actividad = WfPermisoTransicionActividadPeer::retrieveByPk($this->getRequestParameter('wf_permiso_transicion_actividad_id'));
      
    }

    $wf_permiso_transicion_actividad->setWfActividadId($this->getRequestParameter('wf_actividad_id'));
    $wf_permiso_transicion_actividad->setWfTransicionId($this->getRequestParameter('wf_transicion_id'));
    
    $wf_permiso_transicion_actividad->save();

    return $this->redirect($this->getRequest()->getScriptName().'/wf_permiso_transicion_actividad/show?wf_permiso_transicion_actividad_id='.$wf_permiso_transicion_actividad->getWfPermisoTransicionActividadId());
  }

  public function executeDelete($request)
  {
    
    $this->verificaPrilegio("wf_permiso_transicion_actividad/delete");
    
    $wf_permiso_transicion_actividad = WfPermisoTransicionActividadPeer::retrieveByPk($this->getRequestParameter('wf_permiso_transicion_actividad_id'));

    $this->forward404Unless($wf_permiso_transicion_actividad);

    $wf_permiso_transicion_actividad->delete();

    return $this->redirect($this->getRequest()->getScriptName().'/wf_permiso_transicion_actividad/index');
    
  }
}
