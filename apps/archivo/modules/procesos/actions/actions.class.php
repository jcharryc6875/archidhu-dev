<?php

/**
 * procesos actions.
 *
 * @package    simad
 * @subpackage procesos
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class procesosActions extends sfActions
{
  public function executeIndex()
  {
    return $this->forward('procesos', 'list');
  }
  
  public function executeList()
  {
  	$parametros_consulta = "";
  	    
    $c = new Criteria();        
    ///////////////////////////// INICIO FILTROS ///////////////////////////////////////		
	if($this->getRequestParameter('macroproceso_id') != ""){		        
        $c->add(ProcesosPeer::MACROPROCESO_ID, $this->getRequestParameter('macroproceso_id'));
		$parametros_consulta .= "&macroproceso_id=".$this->getRequestParameter('macroproceso_id');
	}
    
    if($this->getRequestParameter('descripcion') != ""){		        
        $c->add(ProcesosPeer::DESCRIPCION, '%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
		$parametros_consulta .= "&descripcion=".$this->getRequestParameter('descripcion');
	}
	
	if($this->getRequestParameter('codigo')){
		$c->add(ProcesosPeer::CODIGO, '%'.$this->getRequestParameter('codigo').'%',Criteria::LIKE);
		$parametros_consulta .= "&codigo=".$this->getRequestParameter('codigo');
	}		
	/////////////////////////////FIN DE FILTROS ////////////////////////////////////////
    ///////////////////////////// INICIO ORDEN ///////////////////////////////////////
    $c->addAscendingOrderByColumn(ProcesosPeer::MACROPROCESO_ID);
    $c->addAscendingOrderByColumn(ProcesosPeer::DESCRIPCION);
    ///////////////////////////// FIN ORDEN ///////////////////////////////////////	
	$pager=new sfPropelPager('Procesos',12);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->parametros = $parametros_consulta;
    $this->macroproceso_id = $this->getRequestParameter('macroproceso_id');
  }
  
  public function executeConsultar()
  {
    $this->macroproceso_id = $this->getRequestParameter('macroproceso_id');   
    
    $this->procesos = new Procesos();
    
  }
  
  public function executeCreate()
  {
    $this->macroproceso_id = $this->getRequestParameter('macroproceso_id');   
    
    $this->procesos = new Procesos();

    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {    
    $this->macroproceso_id = $request->getParameter('macroproceso_id');
    $this->procesos = ProcesosPeer::retrieveByPk($request->getParameter('procesos_id'));
  }

  public function executeUpdate($request)
  {
    if (!$request->getParameter('procesos_id'))
    {
      $procesos = new Procesos();
    }
    else
    {
      $procesos = ProcesosPeer::retrieveByPk($request->getParameter('procesos_id'));
      $this->forward404Unless($procesos);
    }    
    $procesos->setMacroprocesoId($request->getParameter('macroproceso_id') ? $request->getParameter('macroproceso_id') : null);
    $procesos->setCodigo($request->getParameter('codigo'));
    $procesos->setDescripcion($request->getParameter('descripcion'));
    $procesos->save();

    return $this->redirect($this->getRequest()->getScriptName().'/procesos/list?macroproceso_id='.$procesos->getMacroprocesoId());
  }

  public function executeDelete($request)
  {    
    
    $this->forward404Unless($procesos = ProcesosPeer::retrieveByPk($request->getParameter('procesos_id')));

    $procesos->delete();

    $this->redirect('procesos/list?macroproceso_id='.$request->getParameter('macroproceso_id'));
  }
}
