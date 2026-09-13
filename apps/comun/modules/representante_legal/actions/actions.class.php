<?php

/**
 * representante_legal actions.
 *
 * @package    simad
 * @subpackage representante_legal
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class representante_legalActions extends sfActions
{
  public function preExecute()
  {    
	  $isAuthenticated = $this->getUser()->isAuthenticated();
	  $base_path = sfConfig::get('base_simad');
	  if(!$isAuthenticated){
      $this->redirect($base_path."/index.php");
    }
  }
  
  public function verificaPrilegioCerrar($currentForm = "")
  {
    if(!$this->tienePrilegio($currentForm)){
		  $this->redirect(sfConfig::get('base_simad').'/no_autorizado_cerrar.html');
    }
  }
  
  public function tienePrilegio($currentForm)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');	
	  return $this->getUser()->checkPerm($currentForm, $usuariologuiado);		 
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('representante_legal', 'list');
  }

  public function executeList()
  {
    $currentForm = "representante_legal/list";
    $this->verificaPrilegioCerrar($currentForm);
    //**********************************************************************************
	  $usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
  	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
  	//**********************************************************************************
	  $this->parametros = '';  	
  	$this->parametros .= "&campoText=" .$this->campoText = $this->getRequestParameter('campoText');
    $this->parametros .= "&campoId=" .$this->campoId = $this->getRequestParameter('campoId');
    $this->parametros .= "&opcion=" .$this->opcion = $this->getRequestParameter('opcion');
  	$c = new Criteria();
  	//**********************************************************************************
    $nombre = trim($this->getRequestParameter('nombre'));
    if ($nombre) {    		        
        $c->add(RepresentanteLegalPeer::NOMBRE,'%'.$nombre.'%',Criteria::LIKE);
        $this->parametros .= "&nombre=" . $nombre;
    }    
    //**********************************************************************************
    $numero_identificacion = trim($this->getRequestParameter('numero_identificacion'));
    if ($numero_identificacion) {    		        
        $c->add(RepresentanteLegalPeer::NUMERO_IDENTIFICACION,'%'.$numero_identificacion.'%',Criteria::LIKE);
        $this->parametros .= "&nit=" . $numero_identificacion;
    }
    //**********************************************************************************
    $representantelegal_id = trim($this->getRequestParameter('representantelegal_id'));
    if ($representantelegal_id) {    		        
        $c->add(RepresentanteLegalPeer::REPRESENTANTELEGAL_ID,$representantelegal_id);
        $this->parametros .= "&representantelegal_id=" . $representantelegal_id;
    }
    //**********************************************************************************
    $interesado_id = trim($this->getRequestParameter('interesado_id'));
    $this->com_interesado = null;
    if ($interesado_id) {    
        $c->addJoin(RepresentanteLegalPeer::REPRESENTANTELEGAL_ID,ReprlegalInteresadoPeer::REPRESENTANTELEGAL_ID);
        $c->add(ReprlegalInteresadoPeer::INTERESADO_ID,$interesado_id);
        $this->parametros .= "&interesado_id=" . $interesado_id;
        $this->interesado_id = $interesado_id;
        $this->com_interesado = InteresadosPeer::retrieveByPK($interesado_id);
    }
    //**********************************************************************************
    $tipoidentificacion_id = trim($this->getRequestParameter('tipoidentificacion_id'));
    if ($tipoidentificacion_id) {    		        
        $c->add(RepresentanteLegalPeer::TIPOIDENTIFICACION_ID,$tipoidentificacion_id);
        $this->parametros .= "&tipoidentificacion_id=" . $tipoidentificacion_id;
    }
    //**********************************************************************************
    $direccion = trim($this->getRequestParameter('direccion'));
    if ($direccion) {    		        
        $c->add(RepresentanteLegalPeer::DIRECCION,'%'.$direccion.'%',Criteria::LIKE);
        $this->parametros .= "&direccion=" . $direccion;
    }
    //**********************************************************************************
    $email = trim($this->getRequestParameter('email'));
    if ($email) {    		        
        $c->add(RepresentanteLegalPeer::EMAIL,'%'.$email.'%',Criteria::LIKE);
        $this->parametros .= "&email=" . $email;
    }
    //**********************************************************************************
    $codigo_postal = trim($this->getRequestParameter('codigo_postal'));
    if ($codigo_postal) {    		        
        $c->add(RepresentanteLegalPeer::CODIGO_POSTAL,'%'.$codigo_postal.'%',Criteria::LIKE);
        $this->parametros .= "&codigo_postal=" . $codigo_postal;
    }
    //**********************************************************************************
    $telefono = trim($this->getRequestParameter('telefono'));
    if ($telefono) {    		        
        $c->add(RepresentanteLegalPeer::TELEFONO,'%'.$telefono.'%',Criteria::LIKE);
        $this->parametros .= "&telefono=" . $telefono;
    }
    //**********************************************************************************
    $celular = trim($this->getRequestParameter('celular'));
    if ($celular) {    		        
        $c->add(RepresentanteLegalPeer::CELULAR,'%'.$celular.'%',Criteria::LIKE);
        $this->parametros .= "&celular=" . $celular;
    }
    //**********************************************************************************
    $fax = trim($this->getRequestParameter('fax'));
    if ($fax) {    		        
        $c->add(RepresentanteLegalPeer::FAX,'%'.$fax.'%',Criteria::LIKE);
        $this->parametros .= "&fax=" . $fax;
    }
    //**********************************************************************************
    $ciudad_id = trim($this->getRequestParameter('ciudad_id'));
    if ($ciudad_id) {    		        
        $c->add(RepresentanteLegalPeer::CIUDAD_ID,$ciudad_id);
        $this->parametros .= "&ciudad_id=" . $ciudad_id;
    }
    //**********************************************************************************
    $c->addDescendingOrderByColumn(RepresentanteLegalPeer::REPRESENTANTELEGAL_ID);
    //**********************************************************************************
    $pager = new sfPropelPager('RepresentanteLegal', 15);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;		    
  }

  public function executeConsulta()
  {
    $currentForm = "representante_legal/consulta";
    $this->verificaPrilegioCerrar($currentForm);
    //***********************************************************************************
    $this->setLayout('layout-modal'); 
  	$this->campoText=$this->getRequestParameter('campoText');
    $this->campoId=$this->getRequestParameter('campoId'); 
  	$this->opcion = $this->getRequestParameter('opcion');
    $this->interesadoId = trim($this->getRequestParameter('interesado_id'));
    $this->representante_legal = new RepresentanteLegal();  	
  }

  public function executeEdit()
  {
    $currentForm  = "COMUN_EDITAR_REPRESENTANTE_LEGAL";
    $this->verificaPrilegioCerrar($currentForm);
    //***********************************************************************************
    $this->campoText = $this->getRequestParameter('campoText');
    $this->campoId = $this->getRequestParameter('campoId');
    $this->opcion = $this->getRequestParameter('opcion');
    $this->interesadoId = trim($this->getRequestParameter('interesado_id'));
    //***********************************************************************************
    $this->representante_legal = RepresentanteLegalPeer::retrieveByPk($this->getRequestParameter('representantelegal_id'));
    //***********************************************************************************
    $this->forward404Unless($this->representante_legal);
  }

  public function executeCreate()
  {
	  $currentFormCrear = "COMUN_CREAR_REPRESENTANTE_LEGAL";
	  $this->verificaPrilegioCerrar($currentFormCrear);
	  //***********************************************************************************
  	$this->campoText = trim($this->getRequestParameter('campoText'));
    $this->campoId = trim($this->getRequestParameter('campoId'));
    $this->opcion = trim($this->getRequestParameter('opcion'));
    $this->interesadoId = trim($this->getRequestParameter('interesado_id'));
	  //***********************************************************************************
    $this->representante_legal = new RepresentanteLegal();
	  //***********************************************************************************
    $this->setTemplate('edit');
  }

  public function executeShow()
  {
    $currentForm = "representante_legal/show";
    $this->verificaPrilegioCerrar($currentForm);
    //***********************************************************************************
  	$this->campoText=$this->getRequestParameter('campoText');
    $this->campoId=$this->getRequestParameter('campoId');
    $this->opcion = $this->getRequestParameter('opcion');
    $this->interesadoId = trim($this->getRequestParameter('interesado_id'));
    $this->representante_legal = RepresentanteLegalPeer::retrieveByPk($this->getRequestParameter('representantelegal_id'));
    $this->forward404Unless($this->representante_legal);
  }

  public function executeUpdate()
  {
	$currentFormCrear  = "COMUN_CREAR_REPRESENTANTE_LEGAL";
	$this->verificaPrilegioCerrar($currentFormCrear);
    //******************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $interesado_id = trim($this->getRequestParameter('interesado_id'));
	//******************************************************************************
    $editando_object = false;
    $redirect_url = "";
    if (!$this->getRequestParameter('representantelegal_id')){
      $representante_legal = new RepresentanteLegal();
      $representante_legal->setFechaCreacion(date("Y-m-d G:i:s"));
      $representante_legal->setUsuarioId($usuariologuiado);
    }else{
      $representante_legal = RepresentanteLegalPeer::retrieveByPk($this->getRequestParameter('representantelegal_id'));
      $representante_legal_hist = $representante_legal->toArray();
      $editando_object = true;
      $this->forward404Unless($representante_legal);
    }    
    //*******************************************************************************
    $representante_legal->setCiudadId($this->getRequestParameter('ciudad_id'));	  
    $representante_legal->setTipoidentificacionId($this->getRequestParameter('tipoidentificacion_id') ? $this->getRequestParameter('tipoidentificacion_id') : null);    
    $representante_legal->setTipogeneroId($this->getRequestParameter('tipogenero_id') ? $this->getRequestParameter('tipogenero_id') : null);
    $representante_legal->setPrimerNombre(trim($this->getRequestParameter('primer_nombre')));
    $representante_legal->setSegundoNombre(trim($this->getRequestParameter('segundo_nombre')));
    $representante_legal->setPrimerApellido(trim($this->getRequestParameter('primer_apellido')));
    $representante_legal->setSegundoApellido(trim($this->getRequestParameter('segundo_apellido')));
    $representante_legal->setNumeroIdentificacion(trim($this->getRequestParameter('numero_identificacion')));
    $representante_legal->setDireccion(trim($this->getRequestParameter('direccion')));
    $representante_legal->setCodigoPostal(trim($this->getRequestParameter('codigo_postal')));
    $representante_legal->setTelefono(trim($this->getRequestParameter('telefono')));
    $representante_legal->setCelular(trim($this->getRequestParameter('celular')));
    $representante_legal->setFax(trim($this->getRequestParameter('fax')));
    $representante_legal->setEmail(trim($this->getRequestParameter('email')));
    $representante_legal->setFechaModificacion(date("Y-m-d G:i:s"));
    //*******************************************************************************
	$representantelegal_id = null;
	if($representante_legal->getPrimaryKey() == 0){
      $representantelegal_id = RepresentanteLegalPeer::validarRegistro($representante_legal->getPrimerNombre(),$representante_legal->getNumeroIdentificacion());
      if($representantelegal_id == null){
        $representante_legal->save();
		$representantelegal_id = $representante_legal->getPrimaryKey();
        $redirect_url = 'interesados/list?interesado_id='.$interesado_id.'&opcion='.trim($this->getRequestParameter('opcion')).'&campoText='.trim($this->getRequestParameter('campoText')).'&campoId='.trim($this->getRequestParameter('campoId'));		
      }else{
        $redirect_url = 'interesados/list?interesado_id='.$interesado_id.'&opcion='.trim($this->getRequestParameter('opcion')).'&campoText='.trim($this->getRequestParameter('campoText')).'&campoId='.trim($this->getRequestParameter('campoId'));
      }
    }else{
	    $representante_legal->save();
		$representantelegal_id = $representante_legal->getPrimaryKey();
		$redirect_url = 'interesados/list?interesado_id='.$interesado_id.'&opcion='.trim($this->getRequestParameter('opcion')).'&campoText='.trim($this->getRequestParameter('campoText')).'&campoId='.trim($this->getRequestParameter('campoId'));
    }
    //*******************************************************************************
	if(!empty($representantelegal_id)){
		$isValidSave = ReprlegalInteresadoPeer::addReprLegalByInteresado($representantelegal_id,$interesado_id);
	}
    //*******************************************************************************
	return $this->redirect($this->getRequest()->getScriptName().'/'.$redirect_url);
  }

  public function executeCheckObject()
  {
    $currentFormCrear  = "COMUN_ASIGNAR_REPRESENTANTE_LEGAL";
    $interesadoPk = trim($this->getRequestParameter('interesado_id'));
    //*********************************************************************************
	  if($this->tienePrilegio($currentFormCrear)){
      $campoText = trim($this->getRequestParameter('campoText'));
      $campoId = trim($this->getRequestParameter('campoId'));
      $opcion = trim($this->getRequestParameter('opcion'));
      //****************************************************************************
      $representantelegal_id = trim($this->getRequestParameter('representantelegal_id'));
      $es_actual = 1;
      //****************************************************************************
      //$this->representante_legal = RepresentanteLegalPeer::retrieveByPk($representantelegal_id);
      //****************************************************************************
      ReprlegalInteresadoPeer::updateReprLegalByInteresado($interesadoPk);
      //****************************************************************************
      ReprlegalInteresadoPeer::updateReprLegalByInteresado($interesadoPk,$representantelegal_id,$es_actual);
    }
    //******************************************************************************
    $redirect_url = 'representante_legal/list?interesado_id='.$interesadoPk.'&opcion='.$opcion.'&campoText='.$campoText.'&campoId='.$campoId;
    return $this->redirect($redirect_url);
  }

  public function executeUncheckObject()
  {
    $currentFormCrear  = "COMUN_DESASOCIAR_REPRESENTANTE_LEGAL";
    $interesadoPk = trim($this->getRequestParameter('interesado_id'));
    //*********************************************************************************
	  if($this->tienePrilegio($currentFormCrear)){
      $campoText = trim($this->getRequestParameter('campoText'));
      $campoId = trim($this->getRequestParameter('campoId'));
      $opcion = trim($this->getRequestParameter('opcion'));
      //*******************************************************************************
      $representantelegal_id = trim($this->getRequestParameter('representantelegal_id'));
      $es_actual = 0;
      //*******************************************************************************
      $this->representante_legal = RepresentanteLegalPeer::retrieveByPk($representantelegal_id);
      //*******************************************************************************
      ReprlegalInteresadoPeer::updateReprLegalByInteresado($interesadoPk,$representantelegal_id,$es_actual);
    }
    //*********************************************************************************
    $redirect_url = 'representante_legal/list?interesado_id='.$interesadoPk.'&opcion='.$opcion.'&campoText='.$campoText.'&campoId='.$campoId;
    return $this->redirect($redirect_url);
  }

  public function executeSelectRepresentanteActive()
  {
    $usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    //**********************************************************************************************
    $currentForm = "representante_legal/list";
    //if($this->tienePrilegio($currentForm)){ return null; }
    //**********************************************************************************************        
    $this->parametros = '';
    $this->parametros .= "&campoText=" .$this->campoText=$this->getRequestParameter('campoText');
    $this->parametros .= "&campoId=" .$this->campoId=$this->getRequestParameter('campoId');
    $this->parametros .= "&opcion=" .$this->opcion = $this->getRequestParameter('opcion');
    $modulo_id = trim($this->getRequestParameter('modulo_id'));    
    //**********************************************************************************************
    $c = new Criteria();
    $c->setDistinct();
    //**********************************************************************************************
    $query = $_GET['q'];
    if (trim($query))
    {
        //$query = str_replace(" ",'%',$query);
        $c1 = $c->getNewCriterion(RepresentanteLegalPeer::NOMBRE,'%'.$query.'%',Criteria::LIKE);
        $c2 = $c->getNewCriterion(RepresentanteLegalPeer::NUMERO_IDENTIFICACION,'%'.$query.'%',Criteria::LIKE);
        $c1->addOr($c2);
        $c->add($c1);
    }
    //**********************************************************************************************
    $c->addAscendingOrderByColumn(RepresentanteLegalPeer::NOMBRE);
    //**********************************************************************************************
    $c->clearSelectColumns();
    $c->addSelectColumn(RepresentanteLegalPeer::REPRESENTANTELEGAL_ID);//0
    $c->addSelectColumn(RepresentanteLegalPeer::NUMERO_IDENTIFICACION);//1
    $c->addSelectColumn(RepresentanteLegalPeer::NOMBRE);//2
    //**********************************************************************************************
    $resultset = RepresentanteLegalPeer::doSelectStmt($c);
    //**********************************************************************************************
    $term_list = array();
    //**********************************************************************************************
	  while($list_data = $resultset->fetch()){
        $text_name = (trim($list_data[2]));
        $nuid_text = trim($list_data[1]) ? trim($list_data[1]) : "";
        $text_name = $nuid_text ? ($nuid_text." - ".$text_name) : $text_name;

        $avatar = sfConfig::get('base_simad').'/images/simad/ico_logo_users.png';
        $term_list[] = array("id" => $list_data[0], "text" => $text_name, "img" => $avatar);
    }
    //**********************************************************************************************
    $data_json = json_encode($term_list);
    //**********************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($data_json);
  }

}
