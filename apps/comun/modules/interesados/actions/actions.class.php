<?php

/**
 * interesados actions.
 *
 * @package    simad
 * @subpackage interesados
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class interesadosActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */

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
  
  public function guardarAuditoria($anterior,$nueva)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*************************************************************************************************
    AuditLogPeer::guardarAuditoriaLite("Interesados",$anterior,$nueva,ModulesEnable::Interesados,$nueva->getNumeroIdentificacion(),$usuariologuiado);
  }

  public function executeIndex()
  {
    return $this->forward('interesados', 'list');
  }

  public function executeConsulta()
  {
    $currentForm = "interesados/consulta";
    $this->verificaPrilegioCerrar($currentForm);
    //**************************************************************************************************************
    $this->setLayout('layout-modal'); 
  	$this->campoText=$this->getRequestParameter('campoText');
    $this->campoId=$this->getRequestParameter('campoId'); 
  	$this->opcion = $this->getRequestParameter('opcion');
    $this->interesado = new Interesados();  	
  }	

  public function executeEdit()
  {
	  $currentForm  = "COMUN_EDITAR_INTERESADO";
	  $this->verificaPrilegioCerrar($currentForm);
	  //***********************************************************************************
  	$this->campoText = $this->getRequestParameter('campoText');
    $this->campoId = $this->getRequestParameter('campoId');
    $this->opcion = $this->getRequestParameter('opcion');
    //***********************************************************************************
    $this->interesado = InteresadosPeer::retrieveByPk($this->getRequestParameter('interesado_id'));
    //***********************************************************************************
    $this->forward404Unless($this->interesado);
  }

  public function executeHistoricoInteresado()
  {
    $currentForm  = "COMUN_EDITAR_INTERESADO";
	  $this->verificaPrilegioCerrar($currentForm);
	  //***********************************************************************************
    $interesado_id = trim($this->getRequestParameter('interesado_id'));
    $this->interesado = InteresadosPeer::retrieveByPK($interesado_id);
	  //***********************************************************************************
    $this->interesados = InteresadosPeer::getHistoricoInteresados($this->interesado->getParentinteresadoId());
  }

  public function executeCreate()
  {
	  $currentFormCrear = "COMUN_CREAR_INTERESADO";
	  $this->verificaPrilegioCerrar($currentFormCrear);
	  //***********************************************************************************
  	$this->campoText = $this->getRequestParameter('campoText');
    $this->campoId = $this->getRequestParameter('campoId');
    $this->opcion = $this->getRequestParameter('opcion');
	  //***********************************************************************************
    $this->interesado = new Interesados();
	  //***********************************************************************************
    $this->setTemplate('edit');
  }

  public function executeShow()
  {
    $currentForm = "interesados/show";
    $this->verificaPrilegioCerrar($currentForm);
    //**************************************************************************************************************
  	$this->campoText=$this->getRequestParameter('campoText');
    $this->campoId=$this->getRequestParameter('campoId');
    $this->opcion = $this->getRequestParameter('opcion');    
    $this->interesado = InteresadosPeer::retrieveByPk($this->getRequestParameter('interesado_id'));
    $this->forward404Unless($this->interesado);
  }

  public function executeList()
  {
    $currentForm = "interesados/list";
    $this->verificaPrilegioCerrar($currentForm);
    //**************************************************************************************************************
	$usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
  	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
  	//**************************************************************************************************************
	$this->parametros = '';  	
  	$this->parametros .= "&campoText=" .trim($this->campoText = $this->getRequestParameter('campoText'));
    $this->parametros .= "&campoId=" .trim($this->campoId = $this->getRequestParameter('campoId'));
    $this->parametros .= "&opcion=" .trim($this->opcion = $this->getRequestParameter('opcion'));
    $this->parametros .= "&interesado_id=" .trim($this->getRequestParameter('interesado_id'));
  	$c = new Criteria();
  	//**************************************************************************************************************
    $c->add(InteresadosPeer::ES_ACTIVO, 1);
    $c->add(InteresadosPeer::VERSION_LAST, 1);
    //**************************************************************************************************************
    $pnombre = trim($this->getRequestParameter('primer_nombre'));
    if ($pnombre) {    		        
        $c->add(InteresadosPeer::PRIMER_NOMBRE,'%'.$pnombre.'%',Criteria::LIKE);
        $this->parametros .= "&primer_nombre=" . $pnombre;
    }
    //**************************************************************************************************************
    $snombre = trim($this->getRequestParameter('segundo_nombre'));
    if ($snombre) {    		        
        $c->add(InteresadosPeer::SEGUNDO_NOMBRE,'%'.$snombre.'%',Criteria::LIKE);
        $this->parametros .= "&segundo_nombre=" . $snombre;
    }
    //**************************************************************************************************************
    $papellido = trim($this->getRequestParameter('primer_apellido'));
    if ($papellido) {    		        
        $c->add(InteresadosPeer::PRIMER_APELLIDO,'%'.$papellido.'%',Criteria::LIKE);
        $this->parametros .= "&primer_apellido=" . $papellido;
    }
    //**************************************************************************************************************
    $sapellido = trim($this->getRequestParameter('segundo_apellido'));
    if ($sapellido) {    		        
        $c->add(InteresadosPeer::SEGUNDO_APELLIDO,'%'.$sapellido.'%',Criteria::LIKE);
        $this->parametros .= "&segundo_apellido=" . $sapellido;
    }
    //**************************************************************************************************************
    $numero_identificacion = trim($this->getRequestParameter('numero_identificacion'));
    if ($numero_identificacion) {    		        
        $c->add(InteresadosPeer::NUMERO_IDENTIFICACION,'%'.$numero_identificacion.'%',Criteria::LIKE);
        $this->parametros .= "&numero_identificacion=" . $numero_identificacion;
    }
    //**************************************************************************************************************
    $this->interesado_id = trim($this->getRequestParameter('interesado_id'));
    if (is_numeric($this->interesado_id)) {    		        
        $c->add(InteresadosPeer::INTERESADO_ID,$this->interesado_id);
    }
    //**************************************************************************************************************
    $tipoidentificacion_id = trim($this->getRequestParameter('tipoidentificacion_id'));
    if ($tipoidentificacion_id) {    		        
        $c->add(InteresadosPeer::TIPOIDENTIFICACION_ID,$tipoidentificacion_id);
        $this->parametros .= "&tipoidentificacion_id=" . $tipoidentificacion_id;
    }
    //**************************************************************************************************************
    $direccion = trim($this->getRequestParameter('direccion'));
    if ($direccion) {    		        
        $c->add(InteresadosPeer::DIRECCION,'%'.$direccion.'%',Criteria::LIKE);
        $this->parametros .= "&direccion=" . $direccion;
    }
    //**************************************************************************************************************
    $numero_fud = trim($this->getRequestParameter('numero_fud'));
    if ($numero_fud) {    		        
        $c->add(InteresadosPeer::NUMERO_FUD,'%'.$numero_fud.'%',Criteria::LIKE);
        $this->parametros .= "&numero_fud=" . $numero_fud;
    }
    //**************************************************************************************************************
    $email = trim($this->getRequestParameter('email'));
    if ($email) {    		        
        $c->add(InteresadosPeer::EMAIL,'%'.$email.'%',Criteria::LIKE);
        $this->parametros .= "&email=" . $email;
    }
    //**************************************************************************************************************
    $codigo_postal = trim($this->getRequestParameter('codigo_postal'));
    if ($codigo_postal) {    		        
        $c->add(InteresadosPeer::CODIGO_POSTAL,'%'.$codigo_postal.'%',Criteria::LIKE);
        $this->parametros .= "&codigo_postal=" . $codigo_postal;
    }
    //**************************************************************************************************************
    $telefono = trim($this->getRequestParameter('telefono'));
    if ($telefono) {    		        
        $c->add(InteresadosPeer::TELEFONO,'%'.$telefono.'%',Criteria::LIKE);
        $this->parametros .= "&telefono=" . $telefono;
    }
    //**************************************************************************************************************
    $celular = trim($this->getRequestParameter('celular'));
    if ($celular) {    		        
        $c->add(InteresadosPeer::CELULAR,'%'.$celular.'%',Criteria::LIKE);
        $this->parametros .= "&celular=" . $celular;
    }
    //**************************************************************************************************************
    $fax = trim($this->getRequestParameter('fax'));
    if ($fax) {    		        
        $c->add(InteresadosPeer::FAX,'%'.$fax.'%',Criteria::LIKE);
        $this->parametros .= "&fax=" . $fax;
    }
    //**************************************************************************************************************
    $ciudad_id = trim($this->getRequestParameter('ciudad_id'));
    if ($ciudad_id) {    		        
        $c->add(InteresadosPeer::CIUDAD_ID,$ciudad_id);
        $this->parametros .= "&ciudad_id=" . $ciudad_id;
    }
    //**************************************************************************************************************
    $tipoidentificacion_id = trim($this->getRequestParameter('tipoidentificacion_id'));
    if ($tipoidentificacion_id) {    		        
        $c->add(InteresadosPeer::TIPOIDENTIFICACION_ID,$tipoidentificacion_id);
        $this->parametros .= "&tipoidentificacion_id=" . $tipoidentificacion_id;
    }
    //**************************************************************************************************************
    $tipogenero_id = trim($this->getRequestParameter('tipogenero_id'));
    if ($tipogenero_id) {    		        
        $c->add(InteresadosPeer::TIPOGENERO_ID,$tipogenero_id);
        $this->parametros .= "&tipogenero_id=" . $tipogenero_id;
    }
    //**************************************************************************************************************
    $c->addAscendingOrderByColumn(InteresadosPeer::INTERESADO_ID);
    //**************************************************************************************************************
    $pager = new sfPropelPager('Interesados', 15);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;		    
  }

  public function executeSelectInteresadosActive()
  {
	  $this->setLayout(false);
    $usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    //**********************************************************************************************
    $currentForm = "interesados/list";
    if(!$this->tienePrilegio($currentForm)){
      $data_json = json_encode(array("id" => null, "text" => null, "img" => null));
      //********************************************************************************************
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText($data_json);
    }
    //**********************************************************************************************
    $c = new Criteria();
    $c->setDistinct();
    $c->setLimit(15);
    //**********************************************************************************************
    $query = $_GET['q'];
    if (trim($query)){
      //$query = str_replace(" ",'%',$query);
      $c1 = $c->getNewCriterion(InteresadosPeer::PRIMER_NOMBRE,$query.'%',Criteria::LIKE);
      $c2 = $c->getNewCriterion(InteresadosPeer::PRIMER_APELLIDO,$query.'%',Criteria::LIKE);
      $c3 = $c->getNewCriterion(InteresadosPeer::NUMERO_IDENTIFICACION, $query.'%',Criteria::LIKE);
      $c1->addOr($c2);
      $c1->addOr($c3);
      $c->add($c1);
    }
    //**********************************************************************************************
    $c->add(InteresadosPeer::ES_ACTIVO, 1);
    $c->add(InteresadosPeer::VERSION_LAST, 1);
    //**********************************************************************************************
    $c->addAscendingOrderByColumn(InteresadosPeer::PRIMER_NOMBRE);
    //**********************************************************************************************
    $c->clearSelectColumns();
    $c->addSelectColumn(InteresadosPeer::INTERESADO_ID);//0
    $c->addSelectColumn(InteresadosPeer::NUMERO_IDENTIFICACION);//1
    $c->addSelectColumn(InteresadosPeer::PRIMER_NOMBRE);//2
	  $c->addSelectColumn(InteresadosPeer::SEGUNDO_NOMBRE);//3
	  $c->addSelectColumn(InteresadosPeer::PRIMER_APELLIDO);//4
	  $c->addSelectColumn(InteresadosPeer::SEGUNDO_APELLIDO);//5
	  $c->addSelectColumn(InteresadosPeer::EMAIL);//6
    $c->addSelectColumn(InteresadosPeer::DIRECCION);//7
    //**********************************************************************************************
    $resultset = InteresadosPeer::doSelectStmt($c);
    //**********************************************************************************************
    $term_list = array();
    //**********************************************************************************************
	  while($list_data = $resultset->fetch()){
      $text_name = (trim($list_data[2]));
      $nuid_text = trim($list_data[1]) ? trim($list_data[1]) : "";
      $text_name = $nuid_text ? ($nuid_text." - ".$text_name) : $text_name;
      //******************************************************************************************
      if(trim($list_data[3])){
        $text_name .= " ".(trim($list_data[3]));
      }
      if(trim($list_data[4])){
        $text_name .= " ".(trim($list_data[4]));
      }
      if(trim($list_data[5])){
        $text_name .= " ".(trim($list_data[5]));
      }
	    if(trim($list_data[6])){
        $text_name .= " ".trim($list_data[6]);
      }
      if(trim($list_data[7])){
        $text_name .= " ".trim($list_data[7]);
      }
      //******************************************************************************************
      $avatar = sfConfig::get('base_simad').'/images/simad/ico_logo_users.png';
      $term_list[] = array("id" => $list_data[0], "text" => ($text_name), "img" => $avatar);
    }
    //**********************************************************************************************
    $data_json = json_encode($term_list);
    //**********************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($data_json);
  }

  public function executeUpdate()
  {
    $currentFormCrear  = "COMUN_CREAR_INTERESADO";
    $this->verificaPrilegioCerrar($currentFormCrear);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	  //**********************************************************************************************
    $editando_directorio = false;
    $redirect_url = "";
    if (!$this->getRequestParameter('interesado_id'))
    {
      $interesado = new Interesados();
      $interesado_anterior = clone $interesado;
      $interesado->setFechaCreacion(date("Y-m-d G:i:s"));
      $interesado->setUsuarioId($usuariologuiado);
    }
    else
    {
      $interesado = InteresadosPeer::retrieveByPk($this->getRequestParameter('interesado_id'));
      $interesado_anterior = clone $interesado;
      $this->forward404Unless($interesado);
    }
    //**********************************************************************************************
    $params = [];
    $tipo_genero = !empty($this->getRequestParameter('tipogenero_id')) ? $this->getRequestParameter('tipogenero_id') : 5;
    //*******************************************************************************
    $pnombre = !empty($this->getRequestParameter('primer_nombre')) ? mb_strtoupper(trim($this->getRequestParameter('primer_nombre'))) : "";
    $papellido = !empty($this->getRequestParameter('primer_apellido')) ? mb_strtoupper(trim($this->getRequestParameter('primer_apellido'))) : "";
    $nuid = !empty($this->getRequestParameter('numero_identificacion')) ? trim($this->getRequestParameter('numero_identificacion')) : "";
    //**********************************************************************************************
    $params['usuario_id'] = $usuariologuiado;
    $params['ciudad_id'] = $this->getRequestParameter('ciudad_id') ? $this->getRequestParameter('ciudad_id') : null;
    $params['tipoidentificacion_id'] = $this->getRequestParameter('tipoidentificacion_id') ? $this->getRequestParameter('tipoidentificacion_id') : null;
    $params['tipogenero_id'] =  $tipo_genero;
	  //**********************************************************************************************
    $params['primer_nombre'] = $pnombre;
    $params['segundo_nombre'] = !empty($this->getRequestParameter('segundo_nombre')) ? mb_strtoupper(trim($this->getRequestParameter('segundo_nombre'))) : "";
    $params['primer_apellido'] = $papellido;
    $params['segundo_apellido'] = !empty($this->getRequestParameter('segundo_apellido')) ? mb_strtoupper(trim($this->getRequestParameter('segundo_apellido'))) : "";
	  //**********************************************************************************************
    $params['numero_identificacion'] = $nuid;
    $params['direccion'] = !empty($this->getRequestParameter('direccion')) ? mb_strtoupper(trim($this->getRequestParameter('direccion'))) : "";
    $params['codigo_postal'] = !empty($this->getRequestParameter('codigo_postal')) ? trim($this->getRequestParameter('codigo_postal')) : "";
    $params['telefono'] = !empty($this->getRequestParameter('telefono')) ? trim($this->getRequestParameter('telefono')) : null;
	  //**********************************************************************************************
    $params['celular'] = !empty($this->getRequestParameter('celular')) ? trim($this->getRequestParameter('celular')) : null;
    $params['fax'] = !empty($this->getRequestParameter('fax')) ? trim($this->getRequestParameter('fax')) : null;
    $params['email'] = !empty($this->getRequestParameter('email')) ? trim($this->getRequestParameter('email')) : null;
    $params['fecha_creacion'] = date("Y-m-d G:i:s");
    $params['numero_fud'] = !empty($this->getRequestParameter('numero_fud')) ? trim($this->getRequestParameter('numero_fud')) : null;
    $params['fecha_modificacion'] = date("Y-m-d G:i:s");
    $params['prefijo'] = trim($this->getRequestParameter('prefijo')) ? ucfirst(trim($this->getRequestParameter('prefijo'))) : null; 
	  //**********************************************************************************************
    $params['version_current'] = empty($interesado->getVersionCurrent()) ? 1 : ($interesado->getVersionCurrent() + 1);
    $params['fecha_version'] = date("Y-m-d");
	  //********************************************************************************************** 
    $params['version_last'] = empty($interesado->getVersionLast()) ? 1 : ($interesado->getVersionLast());
    $params['parentinteresado_id'] = empty($interesado->getParentinteresadoId()) ? $interesado->getPrimaryKey() : $interesado->getParentinteresadoId();
    //**********************************************************************************************
    if(empty($interesado->getPrimaryKey()))
    {
      $interesado_id = InteresadosPeer::validarRegistro($pnombre, $papellido, $nuid);
      //********************************************************************************************
      if(empty($interesado_id))
      {
        $smart_interesado = InteresadosPeer::smartUpdateInteresado($params, $interesado);
        $redirect_url = 'interesados/show?interesado_id='.$smart_interesado->getPrimaryKey().'&opcion='.$this->getRequestParameter('opcion').'&campoText='.$this->getRequestParameter('campoText').'&campoId='.$this->getRequestParameter('campoId');
      }
      else
      {
        $interesado_current = InteresadosPeer::retrieveByPk($interesado_id);
        $interesado_last = clone $interesado_current;
        //******************************************************************************************
        if(empty($interesado_current->getVersionLast()) || empty($interesado_current->getEsActivo())){
          $interesado_current->setVersionLast(1);
          $interesado_current->setEsActivo(1);
          //****************************************************************************************
          if(empty($interesado_current->getFechaVersion())){ $interesado_current->setFechaVersion(date("Y-m-d")); }
          if(empty($interesado_current->getVersionCurrent())){ $interesado_current->setVersionCurrent(1); }
          if(empty($interesado_current->getParentinteresadoId())){ $interesado_current->setParentinteresadoId($interesado_current->getPrimaryKey()); }
          //****************************************************************************************
          $interesado_current->save();
        }
        //******************************************************************************************
        AuditLogPeer::guardarAuditoriaLite(InteresadosPeer::OM_CLASS, $interesado_last, $interesado_current, 
            ModulesEnable::Interesados, $interesado_current->getNumeroIdentificacion(),$usuariologuiado);
        //******************************************************************************************
        $this->getUser()->setFlash('messages_info', 'El interesado ya existe, NO se actualizo la información del interesado');
        $redirect_url = 'interesados/list?interesado_id=' . $interesado_id . '&opcion=' . $this->getRequestParameter('opcion') . '&campoText=' . $this->getRequestParameter('campoText') . '&campoId=' . $this->getRequestParameter('campoId');
      }
    }
    else
    {
      $this->getUser()->setFlash('messages_info', 'Datos actualizados aplicarán solo para nuevos interesados');
      $smart_interesado = InteresadosPeer::smartUpdateInteresado($params, $interesado);
      $redirect_url = 'interesados/show?interesado_id='.$smart_interesado->getPrimaryKey().'&opcion='.$this->getRequestParameter('opcion').'&campoText='.$this->getRequestParameter('campoText').'&campoId='.$this->getRequestParameter('campoId');
    }
    //**********************************************************************************************
    return $this->redirect($this->getRequest()->getScriptName() . '/' . $redirect_url);
  }
}
