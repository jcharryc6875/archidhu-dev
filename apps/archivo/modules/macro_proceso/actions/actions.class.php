<?php

/**
 * macro_proceso actions.
 *
 * @package    simad
 * @subpackage macro_proceso
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class macro_procesoActions extends sfActions
{
  
  public function executeIndex()
  {
    //$this->macro_procesoList = MacroProcesoPeer::doSelect(new Criteria());
    return $this->forward('macro_proceso', 'list');
  }
  
  
  public function executeList()
  {
  	$parametros_consulta = "";
  	    
    $c = new Criteria();
    ///////////////////////////// INICIO FILTROS ///////////////////////////////////////		
	if($this->getRequestParameter('descripcion') != ""){		        
        $c->add(MacroProcesoPeer::DESCRIPCION, '%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
		$parametros_consulta .= "&descripcion=".$this->getRequestParameter('descripcion');
	}
	
	if($this->getRequestParameter('codigo')){
		$c->add(MacroProcesoPeer::CODIGO, '%'.$this->getRequestParameter('codigo').'%',Criteria::LIKE);
		$parametros_consulta .= "&codigo=".$this->getRequestParameter('codigo');
	}		
	/////////////////////////////FIN DE FILTROS ////////////////////////////////////////
    ///////////////////////////// INICIO ORDEN ///////////////////////////////////////
    $c->addAscendingOrderByColumn(MacroProcesoPeer::DESCRIPCION);
    ///////////////////////////// FIN ORDEN ///////////////////////////////////////	
	$pager=new sfPropelPager('MacroProceso',12);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->parametros = $parametros_consulta;
  }
  
  public function executeCreate()
  {
    $this->macro_proceso = new MacroProceso();

    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $this->macro_proceso = MacroProcesoPeer::retrieveByPk($request->getParameter('macroproceso_id'));
  }
  
  public function executeConsultar()
  {
  	$this->macro_proceso = new MacroProceso();
  }
  
  public function executeUpdate($request)
  {
    if (!$request->getParameter('macroproceso_id'))
    {
      $macro_proceso = new MacroProceso();
    }
    else
    {
      $macro_proceso = MacroProcesoPeer::retrieveByPk($request->getParameter('macroproceso_id'));
      $this->forward404Unless($macro_proceso);
    }    
    $macro_proceso->setMacroprocesoId($request->getParameter('macroproceso_id') ? $request->getParameter('macroproceso_id') : null);
    $macro_proceso->setCodigo($request->getParameter('codigo'));
    $macro_proceso->setDescripcion($request->getParameter('descripcion'));
    $macro_proceso->save();

    return $this->redirect($this->getRequest()->getScriptName().'/macro_proceso/list');
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($macro_proceso = MacroProcesoPeer::retrieveByPk($request->getParameter('macroproceso_id')));

    $macro_proceso->delete();

    $this->redirect('macro_proceso/index');
  }
}
