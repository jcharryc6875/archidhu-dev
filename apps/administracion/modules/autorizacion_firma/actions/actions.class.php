<?php

/**
 * autorizacion_firma actions.
 *
 * @package    simad
 * @subpackage autorizacion_firma
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class autorizacion_firmaActions extends sfActions
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
 	$usuarioLoguiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuarioLoguiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}
  }
 
  public function executeIndex()
  {
    //$this->autorizacion_firmaList = AutorizacionFirmaPeer::doSelect(new Criteria());
    return $this->forward('autorizacion_firma', 'list');
  }
  
  public function executeList()
  { 
  	$this->verificaPrilegio("autorizacion_firma/list");
    $c = new Criteria();
    $c->setDistinct();
    $this->parametros = '';
	$usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
	
    //////////////////FILTROS POR USUARIO CON DIFERENTES ROLES///////////////////////////////////////////
	if($this->getRequestParameter('usuario_autoriza_id')){
        $c->add(AutorizacionFirmaPeer::USUARIO_ID,$this->getRequestParameter('usuario_autoriza_id'));
        $this->parametros.="&usuario_autoriza_id=".$this->getRequestParameter('usuario_autoriza_id');
	}
    
    if($this->getRequestParameter('usuario_autorizado_id')){
        $c->addAlias('AFUR2',AutFirmaUsuarioPeer::TABLE_NAME);
        $c->addJoin(AutorizacionFirmaPeer::AUTORIZACIONFIRMA_ID,'AFUR2.AUTORIZACIONFIRMA_ID');
        $c->add('AFUR2.USUARIO_ID',$this->getRequestParameter('usuario_autorizado_id'));        
        $this->parametros.="&usuario_autorizado_id=".$this->getRequestParameter('usuario_autorizado_id');
	}    	
    /*******************************************************************************************************/
    if($this->getRequestParameter('estadofirmaauto_id')){
	  $c->add(AutorizacionFirmaPeer::ESTADOFIRMAAUTO_ID,$this->getRequestParameter('estadofirmaauto_id'));
	  $this->parametros.="&estadofirmaauto_id=".$this->getRequestParameter('estadofirmaauto_id');
	}
	/*************************************************************************************************************/
	 $c->addDescendingOrderByColumn(AutorizacionFirmaPeer::AUTORIZACIONFIRMA_ID);
	/**************************************************************************************************************/
	$pager=new sfPropelPager('AutorizacionFirma',10);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();	
	$this->pager=$pager;
  }
   
  public function executeCreate()
  {
    $this->verificaPrilegio("autorizacion_firma/create");
    $this->autorizacion_firma = new AutorizacionFirma();
	$this->usuario_autoriza  =  new Usuario();
    $this->usuario_autorizado  =  new Usuario();
    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $this->verificaPrilegio("autorizacion_firma/edit");
    $this->autorizacion_firma = AutorizacionFirmaPeer::retrieveByPk($this->getRequestParameter('autorizacionfirma_id'));
		
	$b = new Criteria();
	$b->add(AutFirmaUsuarioPeer::AUTORIZACIONFIRMA_ID,$this->getRequestParameter('autorizacionfirma_id'));
	$resp =  AutFirmaUsuarioPeer::doSelectOne($b);
	$this->userAutorizado = $resp->getUsuarioId();
    $this->usuario_autorizado = $resp;
	
	//$c = new Criteria();
    //$this->usuarios  =  UsuarioPeer::doSelect($c);
    $this->forward404Unless($this->autorizacion_firma);
    
  }

  public function executeUpdate($request)
  {    
    $is_edit = false;
    if (!$this->getRequestParameter('autorizacionfirma_id'))
    {
      $this->verificaPrilegio("autorizacion_firma/create");
      $autorizacion_firma = new AutorizacionFirma();
    }
    else
    {
      $this->verificaPrilegio("autorizacion_firma/edit");
      $autorizacion_firma = AutorizacionFirmaPeer::retrieveByPk($this->getRequestParameter('autorizacionfirma_id'));
      $is_edit = true;
      $this->forward404Unless($autorizacion_firma);
    }
    //**************************************************************************************************
    $autorizacion_firma->setModuloId($this->getRequestParameter('modulo_id')); 
    $autorizacion_firma->setEstadofirmaautoId($this->getRequestParameter('estadofirmaauto_id')); 
    $autorizacion_firma->setUsuarioId($this->getRequestParameter('usuario_autoriza_id'));
	$autorizacion_firma->setFirmaElectronica($this->getRequestParameter('firma_electronica') ? $this->getRequestParameter('firma_electronica') : 0);
	$autorizacion_firma->setFirmaDigital($this->getRequestParameter('firma_digital') ? $this->getRequestParameter('firma_digital') : 0);
	$autorizacion_firma->setFirmaFisica(($this->getRequestParameter('firma_electronica') || $this->getRequestParameter('firma_digital')) ? 0 : 1);
    $autorizacion_firma->save();   
    $autorizado_id  = $this->getRequestParameter('usuario_autorizado_id');
    //**************************************************************************************************
    if($autorizacion_firma->getPrimaryKey())
    {
       $this->delete_usuario($autorizacion_firma->getPrimaryKey());
       $this->insert_usuario($autorizacion_firma->getPrimaryKey(),$autorizado_id);
       
       if($is_edit)
       {
            return $this->redirect($this->getRequest()->getScriptName().'/autorizacion_firma/edit?autorizacionfirma_id='.$autorizacion_firma->getAutorizacionfirmaId().'&cod_msg=3');
       }
       else
       {
            return $this->redirect($this->getRequest()->getScriptName().'/autorizacion_firma/edit?autorizacionfirma_id='.$autorizacion_firma->getAutorizacionfirmaId().'&cod_msg=1');
       }
    }
    else
    {
        return $this->redirect($this->getRequest()->getScriptName().'/autorizacion_firma/create?cod_msg=1');
    }    
    $this->setTemplate('edit');
  }
  
  public function executeConsulta()
  {
    $this->verificaPrilegio("autorizacion_firma/consultar");
  	$this->usuarios  =  new Usuario();
  	$this->autorizacion_firma = new AutorizacionFirma();
  }
  
  public function insert_usuario($autorizacionfirma_id,$autorizado_id,$rol_id=2)
  {
     /*******************************************************************************************************/
     $object_reg = new AutFirmaUsuario();
     $object_reg->setAutorizacionfirmaId($autorizacionfirma_id);
     $object_reg->setRolfirmausuarioId($rol_id);
     $object_reg->setUsuarioId($autorizado_id);
     $object_reg->save();
     /*******************************************************************************************************/
  }
  
  public function delete_usuario($autorizacionfirma_id)
  {
    /*******************************************************************************************************/
	$conexion = Propel::getConnection();
  	$set = "DELETE FROM %s WHERE AUTORIZACIONFIRMA_ID=".$autorizacionfirma_id;
	$sql = sprintf($set, AutFirmaUsuarioPeer::TABLE_NAME);
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
	/*******************************************************************************************************/
  }
  
  public function executeDelete($request)
  {
    $this->verificaPrilegio("autorizacion_firma/delete");
    $this->forward404Unless($autorizacion_firma = AutorizacionFirmaPeer::retrieveByPk($request->getParameter('autorizacionfirma_id')));
    $this->delete_usuario($autorizacion_firma->getPrimaryKey());
    $autorizacion_firma->delete();
    $this->redirect('autorizacion_firma/index');
  }
}