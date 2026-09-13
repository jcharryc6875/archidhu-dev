<?php

/**
 * wf_instancia_bitacora actions.
 *
 * @package    simad
 * @subpackage wf_instancia_bitacora
 * @author     Javier Fernando Charry - Alfonso Ayala
 * @version    SVN: $Id: actions.class.php 3335 2007-01-23 16:19:56Z fabien $
 */
class wf_instancia_bitacoraActions extends sfActions
{
  public function preExecute()
  {    
 	$isAuthenticated = $this->getUser()->isAuthenticated();
 	$base_path = sfConfig::get('base_simad');
	if(!$isAuthenticated)
	{
		$this->redirect($base_path."/backend.php/security/login");
 	}
  }
  
  public function executeIndex()
  {
    return $this->forward('wf_instancia_bitacora', 'list');
  }
  
  public function executeListCom()
  {
  	$c=new Criteria();    
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $actividad_transicion = $this->getRequestParameter('wfactividadtransicion_id');
  	$parametros = '?a=1';
  	$parametros.="&wfactividadtransicion_id=".$actividad_transicion;  	
    $this->actividad_transicion = $actividad_transicion;
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($actividad_transicion);
  	
	if($wf_actividad_transicion->getRequiereComenviada() != null){//consulta cuando se requiere una enviada
    //echo 'enviada';
    $rol = 2;//rol usuario que firma comunicacion enviada
    $rol_directorio = 2;//rol usuario destino de la comunicacion enviada
	/**********************************************************************************************************/
	if($this->getRequestParameter('radicado')){	
	 	$c->add(ComEnviadaPeer::RADICADO,'%'.$this->getRequestParameter('radicado').'%',Criteria::LIKE);		
		$parametros.="&radicado=".$this->getRequestParameter('radicado');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('asunto')){
	 	$c->add(ComEnviadaPeer::ASUNTO,'%'.$this->getRequestParameter('asunto').'%',Criteria::LIKE);		
		$parametros.="&asunto=".$this->getRequestParameter('asunto');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('usuario_copia')){	  				
	 	$c->add(EnviadaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));
        $rol = 3;//rol usuario copia de la comunicacion enviada
	 	$parametros.="&usuarioCopia=".$this->getRequestParameter('usuario_copia');
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('radicador')){
	 	$c->add(EnviadaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));
        $rol = 1;//rol usuario radicador de la comunicacion enviada
	 	$parametros.="&usuarioProyecto=".$this->getRequestParameter('radicador');
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('fechaInicial')) {
		if ($this->getRequestParameter('fechaFinal')) {
	        $c->add(ComEnviadaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(ComEnviadaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_THAN);
	        $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }else{
		    $c->add(ComEnviadaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
		    $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }
	 }	
	/************************************************************************************************************/	
	if ($this->getRequestParameter('periodo_id')) {		
	    $c->add(ComEnviadaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
	    $parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');     
	}
	/*************************************************************************************************************/
	if ($this->getRequestParameter('marcada')) {				   
	    $c->add(ComEnviadaPeer::MARCA,$usuariologuiado);
	    $parametros .= "&marcada=" . $this->getRequestParameter('marcada');
	}
    /**********************************************************************************************************/
    //ROL DEL USUARIO DE LA COMUNICACION
    $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
    //$c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaDirectorioPeer::COMENVIADA_ID);
    $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol);
    //$c->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID,$rol_directorio);
    /**********************************************************************************************************/
    //SIEMPRE SE DEBEN ORDENAR LOS RESULTADOS PARA QUE SE PUEDA PAGINAR
    $c->addDescendingOrderByColumn(ComEnviadaPeer::FECHA_CREACION);
    /**********************************************************************************************************/
	$pager=new sfPropelPager('ComEnviada',10);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;   
    $this->parametros = $parametros;
    $this->modulo = 4;
    $i = 0;
  	$destinatario=array();
	$remitente=array();
	/*foreach ($pager->getResults() as $comEnviada){
	   $destinatario[$i]=$this->getDestinatario($comEnviada->getComenviadaId());
	   $remitente[$i]=$this->getFirstUsuarioFirmaName($comEnviada->getComenviadaId());
	   $i++;
	}*/
	$this->destinatario = $destinatario;
	$this->remitente = $remitente;
	  	
	}elseif($wf_actividad_transicion->getRequiereCominterna() != null){//consulta cuando se requiere una Interna
    //echo "interna";
    $rol = 2;//rol usuario que firma comunicacion interna  
	/**********************************************************************************************************/
	if($this->getRequestParameter('radicado')){	
	 	$c->add(ComInternaPeer::RADICADO,'%'.$this->getRequestParameter('radicado').'%',Criteria::LIKE);		
		$parametros.="&radicado=".$this->getRequestParameter('radicado');			 	
	}	
	/**********************************************************************************************************/
	if($this->getRequestParameter('asunto')){
	 	$c->add(ComInternaPeer::REFERENCIA,'%'.$this->getRequestParameter('asunto').'%',Criteria::LIKE);		
		$parametros.="&asunto=".$this->getRequestParameter('asunto');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('radicador')){
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));
        $rol = 1;//rol usuario que radica la comunicacion interna	
		//$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);	
	 	$parametros.="&radicador=".$this->getRequestParameter('radicador');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('usuario_copia')){			
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));
        $rol = 3;//rol usuario con copia de la comunicacion interna	
		//$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);	
	 	$parametros.="&usuario_copia=".$this->getRequestParameter('usuario_copia');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('usuario_destino')){
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_destino'));
        $rol = 3;//rol usuario destinatario de la comunicacion interna	
		//$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
	 	$parametros.="&usuario_destino=".$this->getRequestParameter('usuario_destino');			 	
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('fechaInicial')) {
		if ($this->getRequestParameter('fechaFinal')) {
	        $c->add(ComInternaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(ComInternaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_THAN);
	        $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }else{
		    $c->add(ComInternaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
		    $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }
	 }	
	/**************************************************************************************************************/	
    if ($this->getRequestParameter('periodo_id')) {		
	    $c->add(ComInternaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
	    $parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');     
	}
	/*************************************************************************************************************/
	if ($this->getRequestParameter('marcada')) {				   
	    $c->add(ComInternaPeer::MARCA,$usuariologuiado);
	    $parametros .= "&marcada=" . $this->getRequestParameter('marcada');
	}
	/**********************************************************************************************************/
    //ROL DEL USUARIO DE LA COMUNICACION
    $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol);
    /**********************************************************************************************************/
    //SIEMPRE SE DEBEN ORDENAR LOS RESULTADOS PARA QUE SE PUEDA PAGINAR
    $c->addDescendingOrderByColumn(ComInternaPeer::FECHA_CREACION);
    /**********************************************************************************************************/        
	$pager=new sfPropelPager('ComInterna',10);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->modulo = 2;    
    $this->parametros = $parametros;
  	//$this->user = $this->getComAsignadas($pager);	
	}elseif($wf_actividad_transicion->getRequiereComrecibida() != null){//consulta cuando se requiere una recibida
    //echo "recibida";
    $rol = 2;//rol usuario que firma comunicacion interna
	/**********************************************************************************************************/
	if ($this->getRequestParameter('radicado')) {
	    $c->add(ComRecibidaPeer::RADICADO, '%' . $this->getRequestParameter('radicado') .'%', Criteria::LIKE);
	    $parametros .= "&radicado=" . $this->getRequestParameter('radicado');
	}
	/**********************************************************************************************************/
	if ($this->getRequestParameter('radicado')) {
	    $c->add(ComRecibidaPeer::RADICADO, '%' . $this->getRequestParameter('radicado') .'%', Criteria::LIKE);
	    $parametros .= "&radicado=" . $this->getRequestParameter('radicado');
	}
	/**********************************************************************************************************/
	if ($this->getRequestParameter('asunto')){
		$c->addJoin(ComRecibidaPeer::ASUNTORECIBIDA_ID,AsuntoRecibidaPeer::ASUNTORECIBIDA_ID);		
	    $c->add(AsuntoRecibidaPeer::DESCRIPCION,'%'.$this->getRequestParameter('asunto').'%',Criteria::LIKE);
	    $parametros .= "&asuntorecibida_id=" . $this->getRequestParameter('asuntorecibida_id');
	}
	/**********************************************************************************************************/
	if ($this->getRequestParameter('usuario_destino')) {
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_destino'));
        $rol = 2;//rol usuario destinatario de la comunicacion recibida
	    $parametros .= "&usuario_destino=" . $this->getRequestParameter('usuario_destino');     
	}	
	/**********************************************************************************************************/
	if ($this->getRequestParameter('usuario_copia')) {
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));
        $rol = 3;//rol usuario con copia de la comunicacion recibida
	    $parametros .= "&usuario_copia=" . $this->getRequestParameter('usuario_copia');     
	}	
	/**********************************************************************************************************/
	if ($this->getRequestParameter('radicador')) {
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));
        $rol = 1;//rol usuario con copia de la comunicacion recibida
	    $parametros .= "&radicador=" . $this->getRequestParameter('radicador');     
	}
	/**********************************************************************************************************/
	if($this->getRequestParameter('fechaInicial')) {
		if ($this->getRequestParameter('fechaFinal')) {
	        $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_THAN);
	        $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }else{
		    $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
		    $parametros .= "&fechaInicial=" . $this->getRequestParameter('fechaInicial');
	     }
	 }	
	/**************************************************************************************************************/
	if ($this->getRequestParameter('periodo_id')) {		
	    $c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
	    $parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');     
	}
	/*************************************************************************************************************/
	if ($this->getRequestParameter('marcada')) {				   
	    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
	    $parametros .= "&marcada=" . $this->getRequestParameter('marcada');
	}	
	/************************************************************************************************************/
    /**********************************************************************************************************/
    //ROL DEL USUARIO DE LA COMUNICACION
    $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,$rol);
    /**********************************************************************************************************/
    //SIEMPRE SE DEBEN ORDENAR LOS RESULTADOS PARA QUE SE PUEDA PAGINAR
    $c->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
    /**********************************************************************************************************/    
	$pager=new sfPropelPager('ComRecibida',10);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;   
    $this->parametros = $parametros;
    $this->modulo = 3;
    //$this->destinatario=$this->getComAsignadas($pager);
    $this->remitente= array();
    /*foreach($pager->getResults() as $recibida){
	   $this->remitente[] = $recibida->getDirectorioexterno().' - '.$recibida->getDirectorioexterno();
	}*/
	  	 
	/************************************************************************************************************/	
	}
    //$parametros.="&wfactividadtransicion_id=".$this->getRequestParameter('wfactividadtransicion_id');			    			
    //$this->wf_instancia_bitacoras = WfInstanciaBitacoraPeer::doSelect(new Criteria());
  }
  
  public function verificaPrilegio($currentForm)
  {	
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
  		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
  	}	 
  }
  
  public function executeList()
  {
  	$c=new Criteria();	 
	$c->setDistinct();
	$this->instancia = $this->getRequestParameter('instancia_id');  
	$this->parametros="a=1";
	$usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
	//********************************************************************************************************
	if($this->getRequestParameter('instancia_id')){
		$c->add(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,$this->getRequestParameter('instancia_id'));
	 	$this->parametros.="&instancia_id=".$this->getRequestParameter('instancia_id');
	}
	//*****************************************************************************************************
	//toka todos porque hay un error en la paginacion porq aparece el ejecutar aun terminado
	$pager=new sfPropelPager('WfInstanciaBitacora',50);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->usuariologuiado = $usuariologuiado;
  	//*****************************************************************************************************
    $this->wf_instancia_bitacoras = WfInstanciaBitacoraPeer::doSelect(new Criteria());
    $this->wf_instancia = WfInstanciaPeer::retrieveByPk($this->getRequestParameter('instancia_id'));
    $this->wf_imagen_instancias = $this->wf_instancia->getWfImagenInstancias();
    //*****************************************************************************************************
    if($this->wf_instancia->getComrecibidaId()){
	   $this->directorio_raiz   = ParametroPeer::retrieveByPk(27)->getValortexto();
	   $this->directorio_adj    = ParametroPeer::retrieveByPk(14)->getValortexto();
	   $this->directorio_alias  = ParametroPeer::retrieveByPk(28)->getValortexto();
    }elseif($this->wf_instancia->getCominternaId()){
       $this->directorio_raiz   = ParametroPeer::retrieveByPk(25)->getValortexto();
	   $this->directorio_adj    = ParametroPeer::retrieveByPk(15)->getValortexto();
	   $this->directorio_alias  = ParametroPeer::retrieveByPk(26)->getValortexto();
    }elseif($this->wf_instancia->getComenviadaId()){
       $this->directorio_raiz   = ParametroPeer::retrieveByPk(29)->getValortexto();
	   $this->directorio_adj    = ParametroPeer::retrieveByPk(13)->getValortexto();
	   $this->directorio_alias  = ParametroPeer::retrieveByPk(30)->getValortexto();
    }
	$this->format_digit_img  = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());    
    //*****************************************************************************************************
    $v = new Criteria();
    $v->addJoin(WfBitacoraVariablePeer::WFINSTANCIABITACORA_ID,WfInstanciaBitacoraPeer::WFINSTANCIABITACORA_ID);    
    $v->add(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,$this->getRequestParameter('instancia_id'));
    $this->wf_variables = WfBitacoraVariablePeer::doSelect($v);
    //*****************************************************************************************************
    }

  public function executeShow()
  {
    $this->wf_instancia_bitacora = WfInstanciaBitacoraPeer::retrieveByPk($this->getRequestParameter('wfinstanciabitacora_id'));
    $this->forward404Unless($this->wf_instancia_bitacora);
  }
  
  public function executeEdit()
  {
  	$this->instancia = trim($this->getRequestParameter('instancia_id'));
	$wf_instancia = WfInstanciaPeer::retrieveByPk($this->instancia);
	$myIfz = new wf_Interface();
	//***************************************************************************************************/
  	$jumpwf = trim($this->getRequestParameter('jumpwf'));
  	$actividad_transicion = trim($this->getRequestParameter('wfactividadtransicion_id'));
  	$wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($actividad_transicion);
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    //******************************Consulta para la Siguiente Actividad*********************************/
    $c = new Criteria();
	//***************************************************************************************************/
    if(!$jumpwf){
     $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
     $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());  
    }
	//***************************************************************************************************/
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);    
	$c->add(WfActividadTransicionPeer::ORDEN,0,Criteria::NOT_EQUAL);
	$c->addAscendingOrderByColumn(WfActividadTransicionPeer::ORDEN);
    $actividades_origen = WfActividadTransicionPeer::doSelect($c);    	  	
  	//***************************************************************************************************/
	$i = 0; 
	$this->actDestinos = array();
	$this->actDestinosId = array();	
    foreach ($actividades_origen as $actividade_origen){
	   $wf_actividad_transicion_destino = WfActividadTransicionPeer::retrieveByPk($actividade_origen->getPrimaryKey() + 1);
	   $wfact_destino = $wf_actividad_transicion_destino->getWfActividad()->getDescripcion();
	   //************************************************************************************************
	   if($jumpwf){
			$wf_transicion = $wf_actividad_transicion_destino->getWfTransicion()->getDescripcion();
			$wf_orden = $wf_actividad_transicion_destino->getOrden();
			$wfact_origen = $actividade_origen->getWfActividad()->getDescripcion();
			$this->actDestinos[$i] = $wfact_origen . " => ". $wfact_destino;
	   }else{
			$this->actDestinos[$i] = $wfact_destino;
	   }
	   //************************************************************************************************
  	   $this->actDestinosId[$i]= $actividade_origen->getPrimaryKey();
       $i++; 
	}
  	/*****************************************************************************************************/
  	$this->wf_actividad_transicion = $wf_actividad_transicion;
    $this->wf_instancia_bitacora = new WfInstanciaBitacora();
	$this->jumpwf = $jumpwf;
	$this->title_action = "Reasignar Actividad";
  }
	
  public function executeCreate()
  {
  	$this->instancia = trim($this->getRequestParameter('instancia_id'));
	$wf_instancia = WfInstanciaPeer::retrieveByPk($this->instancia);
	//***************************************************************************************************/
  	$jumpwf = trim($this->getRequestParameter('jumpwf'));
  	$actividad_transicion = trim($this->getRequestParameter('wfactividadtransicion_id'));
  	$wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($actividad_transicion);
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    //******************************Consulta para la Siguiente Actividad*********************************/
    $c = new Criteria();
	//***************************************************************************************************/
    if(!$jumpwf){
		$c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
		$c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());  
    }
	//***************************************************************************************************/
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);    
	$c->addAnd(WfActividadTransicionPeer::ORDEN,0,Criteria::NOT_EQUAL);
	$c->addAscendingOrderByColumn(WfActividadTransicionPeer::ORDEN);
    $actividades_origen = WfActividadTransicionPeer::doSelect($c);    	  	
  	//***************************************************************************************************/
	$i = 0; 
	$this->actDestinos = array();
	$this->actDestinosId = array();	
    foreach ($actividades_origen as $actividade_origen){
  	   $wf_actividad_transicion_destino = WfActividadTransicionPeer::retrieveByPk($actividade_origen->getPrimaryKey() + 1);
	   //************************************************************************************************
	   if($jumpwf){
			$wf_transicion = $wf_actividad_transicion_destino->getWfTransicion()->getDescripcion();
			$wf_orden = $wf_actividad_transicion_destino->getOrden();
			$this->actDestinos[$i]= $wf_transicion . " / ". $wf_actividad_transicion_destino->getWfActividad()->getDescripcion() ." - ". $wf_orden;
	   }else{
			$this->actDestinos[$i]= $wf_actividad_transicion_destino->getWfActividad()->getDescripcion();
	   }
	   //************************************************************************************************
  	   $this->actDestinosId[$i]= $wf_actividad_transicion_destino->getPrimaryKey();
       $i++; 
	}
  	/*****************************************************************************************************/
  	$this->wf_actividad_transicion = $wf_actividad_transicion;
    $this->wf_instancia_bitacora = new WfInstanciaBitacora();
	//$this->jumpwf = false;
    $this->jumpwf = $jumpwf;
	$this->title_action = "";
    $this->setTemplate('edit');
  }
  
  public function executeUsuariosEdit()
  {
    $actividad_transicion = WfActividadTransicionPeer::retrieveByPk($this->getRequestParameter('id_seleccion'));
  	/**********************************************************************************************************/
  	$a = new Criteria();
    $a->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$actividad_transicion->getWfTransicionId());
    $a->add(WfActividadTransicionPeer::WF_FLUJO_ID,$actividad_transicion->getWfFlujoId());
    $a->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $transicion = WfActividadTransicionPeer::doSelectOne($a);
  	/******************************Consulta para usuarios asignados a la actividad******************************/
    $myIfz = new wf_Interface();
  	//$this->usuarios_transicion = $myIfz->getObjCurrentActividadUsuarios($transicion->getWfactividadtransicionId());
	$this->usuarios_transicion = $myIfz->getObjCurrentActividadUsuarios($this->getRequestParameter('id_seleccion'));
  	/**********************************Consulta Para las Variables**********************************************/
  	$v = new Criteria();
  	$v->add(WfVariablePeer::WFACTIVIDADTRANSICION_ID,$transicion->getWfactividadtransicionId());
  	$v->addDescendingOrderByColumn(WfVariablePeer::ORDEN);
  	$this->variables =  WfVariablePeer::doSelect($v);    
    /**********************************Tipo Comunicacion que se requiere****************************************/
    $modulo = "";
    if ($actividad_transicion->getRequiereComenviada()){
        $this->modulo = ModuloPeer::retrieveByPK(4); 
    }elseif($actividad_transicion->getRequiereCominterna()){
        $this->modulo = ModuloPeer::retrieveByPK(2);
    }elseif($actividad_transicion->getRequiereComrecibida()){
        $this->modulo = ModuloPeer::retrieveByPK(3);
    }
    /***********************************************************************************************************/
  	$this->setLayout(false);
  	$this->actividad_transicion = $actividad_transicion;
	$this->setTemplate('usuarios');
  }
  
  public function executeUsuarios()
  {
    $actividad_transicion = WfActividadTransicionPeer::retrieveByPk($this->getRequestParameter('id_seleccion'));
    $isEditWf = trim($this->getRequestParameter('editwf'));
  	//**********************************************************************************************************
  	$a = new Criteria();
    $a->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$actividad_transicion->getWfTransicionId());
    $a->add(WfActividadTransicionPeer::WF_FLUJO_ID,$actividad_transicion->getWfFlujoId());
    $a->add(WfActividadTransicionPeer::ORDEN,$actividad_transicion->getOrden());
    $a->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $transicion = WfActividadTransicionPeer::doSelectOne($a);
  	/******************************Consulta para usuarios asignados a la actividad******************************/
    $myIfz = new wf_Interface();
    if($isEditWf){
        $this->usuarios_transicion = $myIfz->getObjCurrentActividadUsuarios($transicion->getPrimaryKey());
    }else{
        $this->usuarios_transicion = $myIfz->getObjNextActividadUsuarios($this->getRequestParameter('id_seleccion'));
    }
  	//**********************************Consulta Para las Variables*********************************************
	$this->variables = null;
	if($transicion != null){
		$v = new Criteria();
		$v->add(WfVariablePeer::WFACTIVIDADTRANSICION_ID,$transicion->getWfactividadtransicionId());
		$v->addDescendingOrderByColumn(WfVariablePeer::ORDEN);
		$this->variables =  WfVariablePeer::doSelect($v);    
	}
    //**********************************Tipo Comunicacion que se requiere***************************************
    $modulo = "";
    if ($actividad_transicion->getRequiereComenviada()){
        $this->modulo = ModuloPeer::retrieveByPK(4); 
    }elseif($actividad_transicion->getRequiereCominterna()){
        $this->modulo = ModuloPeer::retrieveByPK(2);
    }elseif($actividad_transicion->getRequiereComrecibida()){
        $this->modulo = ModuloPeer::retrieveByPK(3);
    }
    //**********************************************************************************************************
  	$this->setLayout(false);
  	$this->actividad_transicion = $actividad_transicion;
  }
  
  public function executeConsultaCom()
  {
    $c = new Criteria();
	$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
	$this->usuarios = UsuarioPeer::doSelect($c);
      	
  	$this->periodo  = new Periodo();
  }
  	
  public function executeRemitir()
  {
  	/******************************Consulta para usuarios asignados a la actividad******************************/
    $transicion = $this->getRequestParameter('wfactividadtransicion_id');
	$c = new Criteria();	
  	$c->add(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,$transicion);  	
  	$this->usuarios_transicion = WfActividadtransicionUsuarioPeer::doSelect($c);
  	/***********************************************************************************************************/
  	
  	$this->wf_instancia = WfInstanciaPeer::retrieveByPk($this->getRequestParameter('instancia_id'));	     
    $this->forward404Unless($this->wf_instancia);
  }
  
  public function executeUpdateUsuario()
  {  	  	
    $wf_instancia = WfInstanciaPeer::retrieveByPk($this->getRequestParameter('wfinstancia_id'));
    
    if($this->getRequestParameter('usuario_id')){
		$wf_instancia->setUsuarioId($this->getRequestParameter('usuario_id'));
	}
	$wf_instancia->save();
	return $this->redirect($this->getRequest()->getScriptName().'/wf_instancia_bitacora/list?instancia_id='.$wf_instancia->getWfinstanciaId());
  }
  
  public function executeUpdate()
  {
    if (!$this->getRequestParameter('wfinstanciabitacora_id'))
    {
      $wf_instancia_bitacora = new WfInstanciaBitacora();
    }else{
      $wf_instancia_bitacora = WfInstanciaBitacoraPeer::retrieveByPk($this->getRequestParameter('wfinstanciabitacora_id'));
      $this->forward404Unless($wf_instancia_bitacora);
    }
    // $wf_instancia_bitacora->setWfinstanciabitacoraId($this->getRequestParameter('wfinstanciabitacora_id'));
    $wf_instancia_bitacora->setUsuarioId($this->getRequestParameter('usuario_id') ? $this->getRequestParameter('usuario_id') : null);
    $wf_instancia_bitacora->setComenviadaId($this->getRequestParameter('comenviada_id') ? $this->getRequestParameter('comenviada_id') : null);
    $wf_instancia_bitacora->setCominternaId($this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : null);
    $wf_instancia_bitacora->setWfinstanciaId($this->getRequestParameter('wfinstancia_id') ? $this->getRequestParameter('wfinstancia_id') : null);
    $wf_instancia_bitacora->setWfactividadtransicionId($this->getRequestParameter('wfactividadtransicion_id') ? $this->getRequestParameter('wfactividadtransicion_id') : null);
    $wf_instancia_bitacora->setComrecibidaId($this->getRequestParameter('comrecibida_id') ? $this->getRequestParameter('comrecibida_id') : null);
    if ($this->getRequestParameter('fecha_i')){
      list($d, $m, $y) = sfI18N::getDateForCulture($this->getRequestParameter('fecha_i'), $this->getUser()->getCulture());
      $wf_instancia_bitacora->setFechaI("$y-$m-$d");
    }
    if ($this->getRequestParameter('fecha_f')){
      list($d, $m, $y) = sfI18N::getDateForCulture($this->getRequestParameter('fecha_f'), $this->getUser()->getCulture());
      $wf_instancia_bitacora->setFechaF("$y-$m-$d");
    }
    $wf_instancia_bitacora->setObservaciones($this->getRequestParameter('observaciones'));
    $wf_instancia_bitacora->setError($this->getRequestParameter('error'));
    $wf_instancia_bitacora->setEsActual($this->getRequestParameter('es_actual', 0));
    if ($this->getRequestParameter('fecha_limite'))
    {
      list($d, $m, $y) = sfI18N::getDateForCulture($this->getRequestParameter('fecha_limite'), $this->getUser()->getCulture());
      $wf_instancia_bitacora->setFechaLimite("$y-$m-$d");
    }
    $wf_instancia_bitacora->setBuzon($this->getRequestParameter('buzon'));
    $wf_instancia_bitacora->setBuzonId($this->getRequestParameter('buzon_id'));
    $wf_instancia_bitacora->setEstadoActividad($this->getRequestParameter('estado_actividad'));
    $wf_instancia_bitacora->setScript($this->getRequestParameter('script'));
    $wf_instancia_bitacora->setScriptParams($this->getRequestParameter('script_params'));
    $wf_instancia_bitacora->setValorVariables($this->getRequestParameter('valor_variables'));
    $wf_instancia_bitacora->setWfNombreVariables($this->getRequestParameter('wf_nombre_variables'));
    $wf_instancia_bitacora->save();
	//****************************************************************************
    return $this->redirect($this->getRequest()->getScriptName().'/wf_instancia_bitacora/show?wfinstanciabitacora_id='.$wf_instancia_bitacora->getWfinstanciabitacoraId());
  }
  
  public function executeUpdateActividad()
  {
  	$buzon = '';
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$jumpwf = trim($this->getRequestParameter('jumpwf')) ? trim($this->getRequestParameter('jumpwf')) : false;
    $wf_interface = new wf_Interface();
  	//**************************************************************************************
	$wfactividad_select = $this->getRequestParameter('actividades');
	$wfactividad_main = $this->getRequestParameter('actividades');
	$wfinstancia_id = $this->getRequestParameter('instancia_id');
    $observaciones = $this->getRequestParameter('observaciones');
	//**************************************************************************************
	if(trim($jumpwf)){
	   $wfactividad_select = $wf_interface->getObjLastIdByActividadTransicionDestino($wfactividad_select);
       $observaciones .= $observaciones . " => Reasignación del flujo por usuario";
	}
	//**************************************************************************************
	if(!$wfactividad_select){
		$wfatid_current = $this->getRequestParameter('wfactividadtransicion_id');
		$wfacttran_current = WfActividadTransicionPeer::retrieveByPk($wfatid_current);		
		return $this->redirect($this->getRequest()->getScriptName().'/wf_instancia_bitacora/edit?jumpwf='.$jumpwf.'&wfactividadtransicion_id='.$wfatid_current.'&instancia_id='.$wfinstancia_id);
	}
	//**************************************************************************************
  	$wf_instancia_bitacora = new WfInstanciaBitacora();    
  	$actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wfactividad_select);//es la escogida de destino
  	//**************************************************************************************
  	$wf_instancia = WfInstanciaPeer::retrieveByPk($wfinstancia_id);
    //**************************************************************************************
    $usuario_destinos = array();
    if(trim($this->getRequestParameter('usuario_id'))){
        $usuario_destinos[] = trim($this->getRequestParameter('usuario_id'));
    }else{
        $usuario_all = preg_split("/[,]+/",$this->getRequestParameter('allusuarios_id'), null, PREG_SPLIT_NO_EMPTY);
        $usuario_destinos = array_unique($usuario_all);
    }
    //**************************************************************************************
    if(trim($this->getRequestParameter('usuario_id'))){
        $wf_instancia->setUsuarioId(trim($this->getRequestParameter('usuario_id')));
    }else{
        $wf_instancia->setUsuarioId($usuariologuiado);
    }
    //**************************************************************************************
  	$wf_instancia->setFechaF(date('Y-m-d G:i:s'));
  	$wf_instancia->setFechaUltimaActividad(date('Y-m-d G:i:s'));
  	//**************************************************************************************
  	$c = new Criteria();
  	$c->add(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,$wf_instancia->getPrimaryKey());
  	$c->add(WfInstanciaBitacoraPeer::WFACTIVIDADTRANSICION_ID,$this->getRequestParameter('wfactividadtransicion_id'));	   
  	$wf_bitacora_anterior = WfInstanciaBitacoraPeer::doSelectOne($c);
  	//**************************************************************************************
  	//$wf_instancia_bitacora->setWfinstanciabitacoraId($wfactividad_select);
	//**************************************************************************************
    $wf_instancia_bitacora->setUsuarioId($usuariologuiado);
    if($actividad_transicion->getRequiereComenviada()){
	   $wf_instancia_bitacora->setComenviadaId($this->getRequestParameter('idCom'));
	   $buzon = 3;	
	}elseif($actividad_transicion->getRequiereCominterna()){
       $wf_instancia_bitacora->setCominternaId($this->getRequestParameter('idCom'));
       $buzon = 2;
    }elseif($actividad_transicion->getRequiereComrecibida()){
	   $wf_instancia_bitacora->setComrecibidaId($this->getRequestParameter('idCom'));
	   $buzon = 1;			
	}
    //**************************************************************************************
    try{
        $dt = new DateTime(date('Y-m-d G:i:s'));
        $fecha = $dt->format("Y-m-d G:i:s");
    } catch (Exception $x) {
        $fecha = date("Y-m-d G:i:s");
    }
	//**************************************************************************************
    $tiempo_limite = trim($actividad_transicion->getTiempoLimite()) ? trim($actividad_transicion->getTiempoLimite()) : 0;
    $fecha_actualizada = $this->sumarHoras($fecha,0,0,0,$tiempo_limite,0,0);
    //**************************************************************************************
    if($actividad_transicion->getWftipoactividadId() == 3){
	   $wf_instancia->setEstaAbierta(false);
       //$wf_instancia->setFechaVencimiento(null);
	}else{
	   $wf_instancia->setEstaAbierta(true);
	   //$wf_instancia->setFechaVencimiento($fecha_actualizada);
	}
    $wf_instancia->save();
	//*************************************************************************************
    $wf_instancia_bitacora->setWfinstanciaId($wfinstancia_id);
    $wf_instancia_bitacora->setWfactividadtransicionId($wfactividad_select);    
    $wf_instancia_bitacora->setFechaI($wf_bitacora_anterior->getFechaF());        
    $wf_instancia_bitacora->setFechaF(date('Y-m-d G:i:s'));    
    $wf_instancia_bitacora->setObservaciones($observaciones);
    $wf_instancia_bitacora->setError($this->getRequestParameter('error', 0));
    $wf_instancia_bitacora->setEsActual($this->getRequestParameter('es_actual', 0));	
    //*************************************************************************************
    $wf_instancia_bitacora->setFechaLimite($fecha_actualizada);    
    $wf_instancia_bitacora->setBuzon($buzon);
    $wf_instancia_bitacora->setBuzonId($buzon);
    $wf_instancia_bitacora->setEstadoActividad('');
    $wf_instancia_bitacora->setScript(trim($this->getRequestParameter('script')) ? trim($this->getRequestParameter('script')) : null);
    $wf_instancia_bitacora->setScriptParams(trim($this->getRequestParameter('script_params')) ? trim($this->getRequestParameter('script_params')) : null);
    $wf_instancia_bitacora->setValorVariables(trim($valor_variable) ? trim($valor_variable) : null);
    $wf_instancia_bitacora->setWfNombreVariables(trim($name_variable) ? trim($name_variable) : null);
    $wf_instancia_bitacora->save();    
    //****************Consulta para la actividad transicion de ORIGEN***********************
    $wflujo_id = $actividad_transicion->getWfFlujoId();
    $wftransicion_id = $actividad_transicion->getWfTransicionId();
    $wforden = $actividad_transicion->getOrden();
    //$transicion_origen = WfActividadTransicionPeer::getActByTransicionData($wftransicion_id,$wflujo_id,$wforden,0);
    $transicion_origen = WfActividadTransicionPeer::retrieveByPk($wfactividad_select -1);
    //**************************************************************************************
    $user_active_mail = WfActividadtransicionUsuarioPeer::getUsuarioActiveMail($wfactividad_main);
    //**************************************************************************************
	if($actividad_transicion->getWfFlujo()->getWfBuzonId() == 2){
	    $this->UpdateWfStateByRolComInterna($wf_instancia->getCominternaId(),0,0,1);
	}elseif($actividad_transicion->getWfFlujo()->getWfBuzonId() == 1){
		$this->UpdateWfEjecutadoByRolComRecibida($wf_instancia->getComrecibidaId(),0,0,1);
	}
	//****************************************************************************************
  	foreach($usuario_destinos as $usuario_wf){
  	  if(trim($usuario_wf)){
        if($actividad_transicion->getWfFlujo()->getWfBuzonId() == 2){
            $this->insertUsuarioInterna($wf_instancia->getCominternaId(),$usuario_wf,3,2);
            if(in_array($usuario_wf,$user_active_mail)){
                $wf_interface->envioEmail($wf_instancia->getCominternaId(),$usuario_wf,2);
            }
        }elseif($actividad_transicion->getWfFlujo()->getWfBuzonId() == 1){
  	       $this->insertUsuarioRecibida($wf_instancia->getComrecibidaId(),$usuario_wf,3,1);
		   //*********************************************************************************
           if(in_array($usuario_destino,$user_active_mail)){
                $wf_interface->envioEmail($wf_instancia->getComrecibidaId(),$usuario_destino,1);
           }
        }
      }
  	}
	//****************************************************************************************
	//$this->UpdateAllWfEjecutadoByRolComRecibida($wf_instancia->getComrecibidaId(),$usuario_destinos,0);    
  	//****************************************************************************************
    //MANEJO DE ARCHIVOS UPLAODS    
    $ruta_uploads = trim($this->getRequestParameter('ruta'));
    if($ruta_uploads){
        $fecha_documento = trim($this->getRequestParameter('fecha_documento'));
        $descripcion = trim($this->getRequestParameter('descripcion_img'));        
        $str_files = preg_split("/[,]+/", trim(WfImagenInstancia::resolveWfUrlUpload($ruta_uploads)),-1,PREG_SPLIT_NO_EMPTY);        
        foreach($str_files as $file){
            //$descripcion = $actividad_transicion->getWfTransicion()->getDescripcion(). ' / ' . basename($file);
            $wf_imagen_instancia = new WfImagenInstancia();
            $wf_imagen_instancia->setWfEstadoImagenId(2);//
            $wf_imagen_instancia->setDescripcion($descripcion);
            $wf_imagen_instancia->setRuta($file);
            $wf_imagen_instancia->setFolios(0);
            $wf_imagen_instancia->setFechaDocumento($fecha_documento);
            $wf_imagen_instancia->setWfinstanciaId($wf_instancia->getPrimaryKey());
            $wf_imagen_instancia->setFechaCreacion(date("Y-m-d G:i:s"));
            $wf_imagen_instancia->save();
        }
    }
    //*****************************Consulta Variables*******************************
    if($transicion_origen){
      	$v = new Criteria();
      	$v->add(WfVariablePeer::WFACTIVIDADTRANSICION_ID,$transicion_origen->getWfactividadtransicionId());
      	$v->addDescendingOrderByColumn(WfVariablePeer::ORDEN);
      	$variables =  WfVariablePeer::doSelect($v); 
        //***********************************************************************************
      	$i = 1;
      	$name_variable = '';
      	$valor_variable = '';
        foreach($variables as $variable){
            $nombre_variable = $variable->getWfvariableId().'_'.mb_strtolower(str_replace(" ","_",$variable->getNombre()));
            $campo_variable = $this->getRequestParameter($nombre_variable);
            if(trim($campo_variable) != ""){
                $wfbv = new WfBitacoraVariable();
                $wfbv->setWfinstanciabitacoraId($wf_instancia_bitacora->getPrimaryKey());
                $wfbv->setWfvariableId($variable->getPrimaryKey());
                $wfbv->setValorVariable($campo_variable);
                $wfbv->save();
                $name_variable  .= $variable->getNombre()."|";
                $valor_variable .= $campo_variable."|";
            }    	 		 
    	}
    }
    //****************************************************************************************
  	return $this->redirect($this->getRequest()->getScriptName().'/wf_instancia_bitacora/list?instancia_id='.$wf_instancia_bitacora->getWfinstanciaId());
  }
  
  public function executeDelete()
  {
    $wf_instancia_bitacora = WfInstanciaBitacoraPeer::retrieveByPk($this->getRequestParameter('wfinstanciabitacora_id'));
    $this->forward404Unless($wf_instancia_bitacora);
    //$wf_instancia_bitacora->delete();
    return $this->redirect($this->getRequest()->getScriptName().'/wf_instancia_bitacora/list');
  }
  
  public function getComAsignadas(sfPropelPager $pager)
  {				
	foreach($pager->getResults() as $resp){			
		 $c = new Criteria();			 
		 $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$resp->getComrecibidaId());
		 $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		 $c->addDescendingOrderByColumn(ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		 $res = ComrecibidaUsuarioPeer::doSelectOne($c);
		 $arrCom[] = $res->getUsuario()->getNombre().' '.$res->getUsuario()->getApellido();			 
	}										
	return $arrCom;	
  }        
  

  public function sumarHoras($date, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0){
    try{            
        $fecha = new DateTime($date);
        $fecha->modify("$hh hour");
        $date_result = $fecha->format('Y-m-d G:i:s');
    } catch (Exception $x) {
        $date_r = getdate(strtotime($date));
        $date_result = date("Y-m-d G:i:s", mktime(($date_r["hours"]+$hh),($date_r["minutes"]+$mn),($date_r["seconds"]+$ss),($date_r["mon"]+$mm),($date_r["mday"]+$dd),($date_r["year"]+$yy)));
    }               
    return $date_result;
  }
  
  public function getActDestinatario($act_transicion,$flujo , $nuevo_orden){ 
  	// consulta ACT_DESTINO select WF_ACTIVIDAD_ID FROM WF_ACTIVIDAD_TRANSICION
    //where WF_TRANSICION_ID=3 AND ES_DESTINO=1 AND WF_FLUJO_ID=18  	
    $destinatario="";
    $c = new Criteria();
	$c->add(WfActividadTransicionPeer::WF_TRANSICION_ID, $act_transicion);
	$c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
	$c->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo);
	$c->add(WfActividadTransicionPeer::ES_DESTINO, 1);
    $result = WfActividadTransicionPeer::doSelect($c);
    //************************************************************************
    $cont=0;
	foreach($result as $res){
		if($cont==0){ $actDestino = $res->getWfActividad()->getDescripcion(); }
	  $cont=1;					
	}
    //************************************************************************
	return $actDestino;
  }
  
   public function getActDestinatarioId($act_transicion,$flujo, $nuevo_orden)
  { 
  	// consulta ACT_DESTINO select WF_ACTIVIDAD_ID FROM WF_ACTIVIDAD_TRANSICION
    //where WF_TRANSICION_ID=3 AND ES_DESTINO=1 AND WF_FLUJO_ID=18
  	
	$destinatario = "";
    $c = new Criteria();
	$c->add(WfActividadTransicionPeer::WF_TRANSICION_ID, $act_transicion);
	$c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
	$c->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo);
	$c->add(WfActividadTransicionPeer::ES_DESTINO, 1);	
    $result = WfActividadTransicionPeer::doSelect($c);
    //************************************************************************    
    $cont = 0;
	foreach($result as $res){
	   if($cont==0){ $actDestino = $res->getPrimaryKey(); }
	   $cont=1;					
	}
    //************************************************************************ 
	return $actDestino;
  }
  
  public function insertUsuarioRecibida($comunicacion_id, $usuario_id, $rol_id, $estado_id)
  {
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comunicacion_id);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuario_id);
    $countRecibida = ComrecibidaUsuarioPeer::doCount($c);    
    if(!$countRecibida){
    	//*********************************************************************************************
        $comAsignar = new ComrecibidaUsuario();
    	$comAsignar->setRolusuariorecibidaid($rol_id);
    	$comAsignar->setUsuarioId($usuario_id);
    	$comAsignar->setComrecibidaId($comunicacion_id);
    	$comAsignar->setEstaAsignada(true);
        $comAsignar->setWfEjecutado(0);
    	$comAsignar->setEstadocomrecibidaId($estado_id);
        $comAsignar->setCargousuarioId($this->getCargoUsuario($usuario_id));
    	$comAsignar->save();
    	//*********************************************************************************************
    }else{
        $this->UpdateWfEjecutadoByRolComRecibida($comunicacion_id,$usuario_id,0,0,true);
    }
  }
  
  public function insertUsuarioInterna($comunicacion_id, $usuario_id,$rol_id,$estado_id)
  {
    $c = new Criteria();
    $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$comunicacion_id);
    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
    $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuario_id);
    $countInterna = CominternaUsuarioPeer::doCount($c);
    //**************************************************************************************
    if(!$countInterna){
        $objCominternaUsuario = new CominternaUsuario();
    	$objCominternaUsuario->setUsuarioId($usuario_id);
    	$objCominternaUsuario->setCominternaId($comunicacion_id);
    	$objCominternaUsuario->setRolusuariocominternaId($rol_id);
    	$objCominternaUsuario->setEstadocominternaId($estado_id);
    	$objCominternaUsuario->setCargousuarioId($this->getCargoUsuario($usuario_id));
        $objCominternaUsuario->setWfEjecutado(0);
    	$objCominternaUsuario->save();
    }else{
        $this->UpdateWfStateByRolComInterna($comunicacion_id,$usuario_id,0,0,true);
    }		
  }
  
  public function getUserDestinoComRecibida($comrecibida_id, $rol_id=2){
	$users_destino = array();
    //*********************************************************************************************
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comrecibida_id);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,$rol_id);
    $com_users = ComrecibidaUsuarioPeer::doSelect($c);
    //*********************************************************************************************
	foreach($com_users as $comunicacion){
		$users_destino[] = $comunicacion->getUsuarioId();
	}
	return $users_destino;
  }
  
  public function UpdateWfEjecutadoByRolComRecibida($comrecibida_id,$usuario_id,$rol_id,$wf_ejecutado=null,$wf_comstaterunfilter=false){
	$conexion = Propel::getConnection();
    //*********************************************************************************************
	$c = new Criteria();
	$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comrecibida_id);
    //*********************************************************************************************
    if($usuario_id > 0){
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuario_id);
    }
    //*********************************************************************************************
    if($rol_id > 0){
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,$rol_id);
    }
    //*********************************************************************************************
	if($wf_comstaterunfilter){
		$c->add(ComrecibidaUsuarioPeer::WF_EJECUTADO,1);
	}else{
        $c->add(ComrecibidaUsuarioPeer::WF_EJECUTADO,0);
	}
	$resp =  ComrecibidaUsuarioPeer::doSelect($c);
    //*********************************************************************************************
    $pks = array();
	foreach ($resp as $row){
        if($row->getRolusuariorecibidaid() != 1){
  		    $pks[] = $row->getPrimaryKey();
        }
    }
    //*********************************************************************************************
    if(count($pks)){
        $sql = "UPDATE %s  SET  %s = ".($wf_ejecutado)." WHERE %s IN (".implode(',',$pks).");";     
    	$sql = sprintf($sql, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::WF_EJECUTADO, ComrecibidaUsuarioPeer::COMRECIBIDAUSUARIO_ID);    
    	$sentencia = $conexion->prepare($sql);
        $sentencia->execute();
    }    
  }
  
  public function UpdateWfStateByRolComInterna($cominterna_id,$usuario_id,$rol_id,$wf_ejecutado=null,$wf_comstaterunfilter=false){
	$conexion = Propel::getConnection();
    //****************************************************************************
	$c = new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID,$cominterna_id);
    //****************************************************************************
    if(trim($usuario_id)){ $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuario_id); }
    //****************************************************************************
    if(trim($rol_id)){ $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol_id); }
    //****************************************************************************
	if($wf_comstaterunfilter){
		$c->add(CominternaUsuarioPeer::WF_EJECUTADO,1);
        $c->addOr(CominternaUsuarioPeer::WF_EJECUTADO,null,Criteria::ISNULL);
	}else{
        $c->add(CominternaUsuarioPeer::WF_EJECUTADO,0);
	}
    //****************************************************************************
	$resp =  CominternaUsuarioPeer::doSelect($c);
    //****************************************************************************
    $pks = array();
	foreach ($resp as $row){
	    if($row->getRolusuariocominternaId() != 1){
  		    $pks[] = $row->getPrimaryKey();
        }
    }
    //*********************************************************************************************
    if(count($pks)){
        $sql = "UPDATE %s  SET  %s = ".($wf_ejecutado)." WHERE %s IN (".implode(',',$pks).");";     
    	$sql = sprintf($sql, CominternaUsuarioPeer::TABLE_NAME, CominternaUsuarioPeer::WF_EJECUTADO, CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);    
    	$sentencia = $conexion->prepare($sql);
        $sentencia->execute();
    }
  }
  
  public function UpdateAllWfEjecutadoByRolComRecibida($comrecibida_id,$usuarios_array,$wf_ejecutado,$rol_id = array(2,3)){
    $conexion = Propel::getConnection();		
    //*********************************************************************************************
    if(trim($comrecibida_id) && $wf_ejecutado >= 0 && count($usuarios_array)){
        $sql = "UPDATE %s SET %s =".($wf_ejecutado)." WHERE %s=".$comrecibida_id." AND %s IN (".implode(",",$rol_id).") AND %s IN(".implode(",",$usuarios_array).")";     
        $sql = sprintf($sql, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::WF_EJECUTADO, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, ComrecibidaUsuarioPeer::USUARIO_ID);            
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();
    }
  }    
  
  public function getCargoUsuario($usuario_id)
  { 
    $c = new Criteria();
    $c->add(CargoUsuarioPeer::USUARIO_ID,$usuario_id);
    $c->add(CargoUsuarioPeer::ES_PRINCIPAL,true);
    $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);    
    return $cargo_usuario->getCargousuarioId();
  }
  
  public function executeExportBitacora()
  {
    $this->verificaPrilegio("REPORTE_ACTIVIDADES_WORKFLOW");
    //***************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');    
    $base_path = sfConfig::get('base_simad');
    $wf_instancia = WfInstanciaPeer::retrieveByPk($this->getRequestParameter('wfinstancia_id'));
    $nomb_file_html = $this->GenFormatoBitacotaFileData($wf_instancia);
    //***************************************************************************************
    $this->redirect($base_path.'/generatepdf.php?exportworkflow='.$nomb_file_html);
  }
  
  /**
     * GenFormatoBitacotaFileData actions.
     *
     * @package    Simad
     * @subpackage Workflow
     * @author     Javier Fernando Charry
     * @function   Genera archivo de datos para el formato de impresion de la bitacora
     * @param      Object $wf_instancia objeto registro del flujo de facturas
     */
    public function GenFormatoBitacotaFileData(WfInstancia $wf_instancia)
    {
        $logo_entidad = "";$nombre_entidad = "";$remitente = "";$destinatario = "";
        $radicado = "";$fecha_radicado = "";$asunto_com = "";$tipo_com = "";
        $regional_com = "";$dependencia_com = "";
        //*****************************SE OBTIENE LA COMUNICACION **********************************
        if($wf_instancia->getCominternaId()){
            $com_interna = ComInternaPeer::retrieveByPK($wf_instancia->getCominternaId());
            $usuarios_com = $com_interna->getUsuariosListCom();
            //**************************************************************************************
            $logo_entidad = trim($com_interna->getRegional()->getEntidad()->getLogoHeader());
            $nombre_entidad = ($com_interna->getRegional()->getEntidad()->getDescripcion());
            $remitente = ($usuarios_com['firmas']);
            $destinatario = ($usuarios_com['destinatario']);
            $radicado = $com_interna->getRadicado();
            $asunto_com = ($com_interna->getReferencia());
            $tipo_com = ($com_interna->getTipoComInterna()->getDescripcion());
            $fecha_radicado = $com_interna->getFechaCreacion();
            $regional_com = ($com_interna->getRegional());
            $dependencia_com = ($com_interna->getDependencia());
        }elseif($wf_instancia->getComrecibidaId()){
            $com_recibida = ComRecibidaPeer::retrieveByPK($wf_instancia->getComrecibidaId());
            $usuarios_com = $com_recibida->getUsuariosListCom();
            //**************************************************************************************
            $logo_entidad = trim($com_recibida->getRegional()->getEntidad()->getLogoHeader());
            $nombre_entidad = ($com_recibida->getRegional()->getEntidad()->getDescripcion());
            $remitente = ($com_recibida->getDirectorioExterno());            
            $destinatario = ($usuarios_com['destinatario']);
            $radicado = $com_recibida->getRadicado();
            $asunto_com = ($com_recibida->getAsunto());
            $tipo_com = ($com_recibida->getTipoComRecibida()->getDescripcion());
            $fecha_radicado = $com_interna->getFechaCreacion();
            $regional_com = ($com_recibida->getRegional());
            $dependencia_com = ($com_interna->getDependencia());
        }else{
            return null;
        }
        //var_dump($usuarios_com);exit;
        //******************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_solicita  = UsuarioPeer::retrieveByPK($usuariologuiado);
        //******************************************DIRECTORIO DE LA PLANTILLAS*********************
        $dir_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
        $tmp_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."com_html".DIRECTORY_SEPARATOR."workflow".DIRECTORY_SEPARATOR;
        //******************************************************************************************
        $contenedor_logos = sfConfig::get('base_simad').'/images/encabezado_carta/logos_carnet/';
        $logo_header = $logo_entidad ? $contenedor_logos.$logo_entidad : $contenedor_logos."maloka.jpg";
        //****************************************NOMBRES DE LAS PLANTILLAS*************************
        $name_plantilla = "tplwfbitacora.txt";
        $file_name = $dir_plantilla.$name_plantilla;
        $aleatorio = rand(1, 10000000);  
        $hash_is = md5($wf_instancia->getPrimaryKey().$aleatorio);
        $name_plantilla_tmp = $hash_is.".php";
        $plantilla_contents = file_get_contents($file_name);      
        //**************************************PATRONES PARA REMPLAZAR LOS VALORES*****************
        $patrones = array();
        $patrones[0]  = '#.#$logo_entidad#.#';
        $patrones[1]  = '#.#$regional_reporte#.#';
        $patrones[2]  = '#.#$fecha_reporte#.#';
        $patrones[3]  = '#.#$funcionario_reporte#.#';
        $patrones[4]  = '#.#$remitente_name#.#';
        $patrones[5]  = '#.#$destinatario_name#.#';
        $patrones[6]  = '#.#$radicado_com#.#';
        $patrones[7]  = '#.#$asunto_com#.#';
        $patrones[8]  = '#.#tipo_com#.#';
        $patrones[9]  = '#.#$fecha_radicado#.#';
        $patrones[10] = '#.#$regional_com#.#';
        $patrones[11] = '#.#$dependencia_com#.#';
        $patrones[12] = '#.#estado_flujo#.#';
        $patrones[13] = '#.#$detalles_bitacora#.#';
        //******************************************************************************************
        $sustituciones    = array();
        $sustituciones[0] = $nombre_entidad;
        $sustituciones[1] = ($usuario_solicita->getRegional()->getDescripcion());
        $sustituciones[2] = date("Y-m-d G:i:s");
        $sustituciones[3] = ($usuario_solicita->getNombreAll());
        $sustituciones[4] = $remitente;
        $sustituciones[5] = $destinatario;
        $sustituciones[6] = $radicado;
        $sustituciones[7] = $asunto_com;
        $sustituciones[8] = $tipo_com;
        $sustituciones[9] = $fecha_radicado;
        $sustituciones[10] = $regional_com;
        $sustituciones[11] = $dependencia_com;
        $sustituciones[12] = $wf_instancia->getEstaAbierta() ? "Tram&iacute;te Abierto" : "Cerrado";
        //******************************************************************************************
        $html_contenido = "";
        foreach($wf_instancia->getWfInstanciaBitacorasJoinWfActividadTransicion() as $object){
            $html_contenido .= '<tr>
            <td>'.($object->getWfActividadTransicion()->getWfTransicion()->getDescripcion()).'</td>
            <td>'.($object->getWfActividadTransicion()->getWfActividad()->getDescripcion()).'</td>
            <td>'.($object->getUsuario()->getNombreAll()).'</td>
            <td>'.date("Y-m:d G:i:s",strtotime($object->getFechaI())).'</td>
            <td>'.date("Y-m:d G:i:s",strtotime($object->getFechaF())).'</td>
            <td>'.($object->getObservaciones()).'</td>
            <td>'.($object->getEstadoActividad() ? "Ejecutada" : "Pendiente").'</td>';
        }        
        $sustituciones[13] = $html_contenido;        
        //**********************************REMPLAZAR DATOS EN LA FACTURA***************************
        $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents);
        //********************************************GUARDAR LA PLANTILLA TEMPORAL*****************
        // Guarda el RTF generado
        $wf_instancia->createPath($tmp_plantilla);    
        file_put_contents($tmp_plantilla.$name_plantilla_tmp,$template_contents);
        //******************************************************************************************
        return $name_plantilla_tmp;
    }
}