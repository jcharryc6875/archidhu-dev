<?php

/**
 * oficina_productora actions.
 *
 * @package    simad
 * @subpackage oficina_productora
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class oficina_productoraActions extends sfActions
{
  public function executeIndex()
  {
    //$this->oficina_productoraList = OficinaProductoraPeer::doSelect(new Criteria());
    return $this->forward('oficina_productora', 'list');
  }
  
  public function executeList()
  {
  	$parametros_consulta = "";
  	$procesos_id = $this->getRequestParameter('procesos_id');
    $procesos = ProcesosPeer::retrieveByPK($procesos_id);
    $macroproceso_id = $procesos->getMacroprocesoId();
    $c = new Criteria();
    ///////////////////////////// INICIO FILTROS ///////////////////////////////////////
    if($procesos_id){
		$c->add(OficinaProductoraPeer::PROCESOS_ID, $procesos_id);
		$parametros_consulta .= "&procesos_id=".$procesos_id;
	}
    		
	if($this->getRequestParameter('descripcion') != ""){		        
        $c->add(OficinaProductoraPeer::DESCRIPCION, '%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
		$parametros_consulta .= "&descripcion=".$this->getRequestParameter('descripcion');
	}
	
	if($this->getRequestParameter('codigo')){
		$c->add(OficinaProductoraPeer::CODIGO, '%'.$this->getRequestParameter('codigo').'%',Criteria::LIKE);
		$parametros_consulta .= "&codigo=".$this->getRequestParameter('codigo');
	}		
	/////////////////////////////FIN DE FILTROS ////////////////////////////////////////
    ///////////////////////////// INICIO ORDEN ///////////////////////////////////////    
    $c->addAscendingOrderByColumn(OficinaProductoraPeer::DESCRIPCION);
    ///////////////////////////// FIN ORDEN ///////////////////////////////////////	
	$pager=new sfPropelPager('OficinaProductora',12);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->parametros = $parametros_consulta;
    $this->procesos_id = $procesos_id;
    $this->macroproceso_id = $macroproceso_id;
  }
  
  public function executeCreate()
  {
    $this->procesos_id = $this->getRequestParameter('procesos_id');
    
    $this->oficina_productora = new OficinaProductora();

    $this->setTemplate('edit');
  }

  public function executeConsultar($request)
  {
    $this->procesos_id = $request->getParameter('procesos_id');
    
    $this->oficina_productora = new OficinaProductora();
    
  }
  
  public function executeEdit($request)
  {
    $this->procesos_id = $request->getParameter('procesos_id');
    
    $this->oficina_productora = OficinaProductoraPeer::retrieveByPk($request->getParameter('oficinaproductora_id'));
  }

  public function executeUpdate($request)
  {
    if (!$request->getParameter('oficinaproductora_id'))
    {
      $oficina_productora = new OficinaProductora();
    }
    else
    {
      $oficina_productora = OficinaProductoraPeer::retrieveByPk($request->getParameter('oficinaproductora_id'));
      $this->forward404Unless($oficina_productora);
    }    
    $oficina_productora->setProcesosId($request->getParameter('procesos_id') ? $request->getParameter('procesos_id') : null);
    $oficina_productora->setCodigo($request->getParameter('codigo'));
    $oficina_productora->setDescripcion($request->getParameter('descripcion'));
    $oficina_productora->save();

    return $this->redirect($this->getRequest()->getScriptName().'/oficina_productora/list?procesos_id='.$oficina_productora->getProcesosId());
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($oficina_productora = OficinaProductoraPeer::retrieveByPk($request->getParameter('oficinaproductora_id')));

    $oficina_productora->delete();

    return $this->redirect($this->getRequest()->getScriptName().'/oficina_productora/list?procesos_id='.$request->getParameter('procesos_id'));
    
  }
}
