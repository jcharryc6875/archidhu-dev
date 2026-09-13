<?php

/**
 * control_contratistas actions.
 *
 * @package    simad
 * @subpackage control_contratistas
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class control_contratistasActions extends sfActions
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
		$this->redirect(sfConfig::get('base_simad').'/simad/no_autorizado_cerrar.html');
	}	 	  
  }
  
  public function executeIndex($request)
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/list";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $this->filtros_consulta = "a=1";
    /**************************************************************************************************************/
    $criteria = new Criteria();
    $criteria = $this->getBasicCriteria($criteria,$request);
    /**************************************************************************************************************/
    $pager = new sfPropelPager('ControlContratistas', 15);
	  $pager->setCriteria($criteria);
	  $pager->setPage($this->getRequestParameter('page',1));
    $pager->init();
	  $this->pager = $pager;	
    /**************************************************************************************************************/
  }
  
  public function executeExcel($request)
  {
    $this->setLayout(false);
    /**************************************************************************************************************/
    $currentForm="control_contratistas/list";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    /**************************************************************************************************************/
    $criteria = new Criteria();
    $criteria = $this->getBasicCriteria($criteria,$request);
    /**************************************************************************************************************/
    $criteria->clearSelectColumns();  	
  	$criteria->addJoin(ControlContratistasPeer::TIPOIDENTIFICACION_ID,TipoIdentificacionPeer::TIPOIDENTIFICACION_ID);
    $criteria->addJoin(ControlContratistasPeer::CIUDAD_ID,CiudadPeer::CIUDAD_ID);
    $criteria->addJoin(ControlContratistasPeer::ESTADOCONTRATISTA_ID,EstadoContratistaPeer::ESTADOCONTRATISTA_ID);
    $criteria->addJoin(ControlContratistasPeer::TIPOCONTRATO_ID,TipoContratoPeer::TIPOCONTRATO_ID);
    $criteria->addJoin(ControlContratistasPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
    $criteria->addJoin(ControlContratistasPeer::CARGO_ID,CargoPeer::CARGO_ID);
    $criteria->addJoin(ControlContratistasPeer::DEPENDENCIA_ID,DependenciaPeer::DEPENDENCIA_ID);
    /**************************************************************************************************************/
  	$criteria->addSelectColumn(ControlContratistasPeer::CONTROLCONTRATISTAS_ID);//0
  	$criteria->addSelectColumn(ControlContratistasPeer::ESTADOCONTRATISTA_ID);//1
  	$criteria->addSelectColumn(ControlContratistasPeer::TIPOIDENTIFICACION_ID);//2
  	$criteria->addSelectColumn(ControlContratistasPeer::USUARIO_ID);//3
  	$criteria->addSelectColumn(ControlContratistasPeer::CIUDAD_ID);//4
  	$criteria->addSelectColumn(ControlContratistasPeer::NUMERO_IDENTIFICACION);//5
  	$criteria->addSelectColumn(ControlContratistasPeer::NOMBRE);//6
  	$criteria->addSelectColumn(ControlContratistasPeer::PRIMER_APELLIDO);//7
  	$criteria->addSelectColumn(ControlContratistasPeer::SEGUNDO_APELLIDO);//8
  	$criteria->addSelectColumn(ControlContratistasPeer::GENERO);//9
  	$criteria->addSelectColumn(ControlContratistasPeer::NACIONALIDAD);//10
  	$criteria->addSelectColumn(ControlContratistasPeer::LIBRETA_MILITAR);//11
  	$criteria->addSelectColumn(ControlContratistasPeer::FECHA_NACIMIENTO);//12
  	$criteria->addSelectColumn(ControlContratistasPeer::DIRECCION_ENVIO);//13
  	$criteria->addSelectColumn(ControlContratistasPeer::EMAIL);//14
  	$criteria->addSelectColumn(ControlContratistasPeer::EDUCACION_BASICA);//15
  	$criteria->addSelectColumn(ControlContratistasPeer::FECHA_GRADO_BASICA);//16
  	$criteria->addSelectColumn(ControlContratistasPeer::EDUCACION_SUPERIOR);//17
  	$criteria->addSelectColumn(ControlContratistasPeer::EXPERIENCIA_LABORAL);//18
  	$criteria->addSelectColumn(ControlContratistasPeer::EPS);//19
  	$criteria->addSelectColumn(ControlContratistasPeer::FONDO_PENSIONES);//20
  	$criteria->addSelectColumn(ControlContratistasPeer::FECHA_CREACION);//21
  	$criteria->addSelectColumn(ControlContratistasPeer::TITULO_UNIVERSITARIO);//22
  	$criteria->addSelectColumn(ControlContratistasPeer::FECHA_GRADO_UNIVERSITARIO);//23
  	$criteria->addSelectColumn(ControlContratistasPeer::NUMERO_TARJETA);//24
  	$criteria->addSelectColumn(ControlContratistasPeer::OTROS_IDIOMAS);//25
    $criteria->addSelectColumn(TipoIdentificacionPeer::DESCRIPCION);//26
    $criteria->addSelectColumn(EstadoContratistaPeer::DESCRIPCION);//27
    $criteria->addSelectColumn(CiudadPeer::NOMBRE);//28
    $criteria->addSelectColumn(UsuarioPeer::NOMBRE);//29
    $criteria->addSelectColumn(UsuarioPeer::APELLIDO);//30
    $criteria->addSelectColumn(TipoControlPeer::DESCRIPCION);//31
    $criteria->addSelectColumn(CargoPeer::DESCRIPCION);//32
    $criteria->addSelectColumn(DependenciaPeer::NOMBRE);//33
    $criteria->addSelectColumn(ControlContratistasPeer::UBICACION_LABORAL);//34
    $criteria->addSelectColumn(ControlContratistasPeer::ESTADO_CIVIL);//35
    $criteria->addSelectColumn(ControlContratistasPeer::NUMERO_HIJOS);//36
    /**************************************************************************************************************/
	$this->select_objects = ControlContratistasPeer::doSelectStmt($criteria);
    /**************************************************************************************************************/
    $this->regUser = UsuarioPeer::retrieveByPK($usuariologuiado);
    /**************************************************************************************************************/
  }
   
  public function executeCreate()
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/create";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $str_eciviles = ParametroPeer::retrieveByPk(70)->getValortexto();
    /**************************************************************************************************************/
    $objestado_civil = array();
    $piezas = preg_split("/[;]+/",$str_eciviles,-1, PREG_SPLIT_NO_EMPTY);
    for($j =0 ; $j < count($piezas); $j++){
       $estados_civiles[$piezas[$j]] = $piezas[$j];
    }
    /**************************************************************************************************************/
    $this->control_contratistas = new ControlContratistas();
    $this->libreta_militar = array("","","");
    $this->direccion_envio = array("","","");
    $this->educacion_basica = array("","","","","");
    $this->educacion_superior = array("","","");
    $this->experiencia_laboral = array("","","","","","","","");
    $this->estados_civiles = $estados_civiles;
    $this->estado_civil = null;
    $this->setTemplate('edit');
  }
    
  public function executeConsultar()
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/consultar";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    /**************************************************************************************************************/
    $this->control_contratistas = new ControlContratistas();
    $this->libreta_militar = array("","","");
    $this->direccion_envio = array("","","");
    $this->educacion_basica = array("","","","","");
    $this->educacion_superior = array("","","");
    $this->experiencia_laboral = array("","","","","","","","");
  }
  
  public function executeEdit($request)
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $str_eciviles = ParametroPeer::retrieveByPk(70)->getValortexto();
    /**************************************************************************************************************/
    $objestado_civil = array();
    $piezas = preg_split("/[;]+/",$str_eciviles,-1, PREG_SPLIT_NO_EMPTY);
    for($j =0 ; $j < count($piezas); $j++){
       $objestado_civil[$piezas[$j]] = $piezas[$j];
    }
    $this->estados_civiles = $objestado_civil;
    /**************************************************************************************************************/
    $this->control_contratistas = ControlContratistasPeer::retrieveByPk($request->getParameter('controlcontratistas_id'));
    $this->libreta_militar = explode("$$",$this->control_contratistas->getLibretaMilitar());
    $this->direccion_envio = explode("$$",$this->control_contratistas->getDireccionEnvio());
    $this->educacion_basica = explode("$$",$this->control_contratistas->getEducacionBasica());
    $this->educacion_superior = explode("$$",$this->control_contratistas->getEducacionSuperior());
    $this->experiencia_laboral = explode("$$",$this->control_contratistas->getExperienciaLaboral());
    $this->estado_civil = $this->control_contratistas->getEstadoCivil();
  }
  
  public function executeShow($request)
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/show";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    /**************************************************************************************************************/
    $this->control_contratistas = ControlContratistasPeer::retrieveByPk($request->getParameter('controlcontratistas_id'));
    $this->libreta_militar = explode("$$",$this->control_contratistas->getLibretaMilitar());
    $this->direccion_envio = explode("$$",$this->control_contratistas->getDireccionEnvio());
    $this->educacion_basica = explode("$$",$this->control_contratistas->getEducacionBasica());
    $this->educacion_superior = explode("$$",$this->control_contratistas->getEducacionSuperior());
    $this->experiencia_laboral = explode("$$",$this->control_contratistas->getExperienciaLaboral());
  }
  
  public function executeUpdate($request)
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    /**************************************************************************************************************/
    $this->forward404Unless($request->isMethod('post'));
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if(!$request->getParameter('controlcontratistas_id'))
    {
        $control_contratistas = new ControlContratistas();
        $control_contratistas->setFechaCreacion(date("Y-m-d G:i:s"));
    }else{
        $control_contratistas = ControlContratistasPeer::retrieveByPk($request->getParameter('controlcontratistas_id'));    
    }
    $control_contratistas->setEstadocontratistaId($request->getParameter('estadocontratista_id'));
    $control_contratistas->setTipocontratoId($request->getParameter('tipocontrato_id'));
    $control_contratistas->setTipoidentificacionId($request->getParameter('tipoidentificacion_id'));
    $control_contratistas->setCargoId($request->getParameter('cargo_id'));
    $control_contratistas->setDependenciaId($request->getParameter('dependencia_id'));
    $control_contratistas->setUsuarioId($usuariologuiado);
    $control_contratistas->setCiudadId($request->getParameter('ciudad_id'));
    $control_contratistas->setNombre(trim($request->getParameter('nombre')));
    $control_contratistas->setPrimerApellido(trim($request->getParameter('primer_apellido')));
    $control_contratistas->setSegundoApellido(trim($request->getParameter('segundo_apellido')));
    $control_contratistas->setNumeroIdentificacion(trim($request->getParameter('numero_identificacion')));
    $control_contratistas->setGenero(trim($request->getParameter('genero')));
    $control_contratistas->setNacionalidad(trim($request->getParameter('nacionalidad')));
    $control_contratistas->setFechaNacimiento($request->getParameter('fecha_nacimiento') ? $request->getParameter('fecha_nacimiento') : null);
    $control_contratistas->setEmail(trim($request->getParameter('email')));
    $control_contratistas->setUbicacionLaboral(trim($request->getParameter('ubicacion_laboral')));
    $control_contratistas->setEstadoCivil(trim($request->getParameter('estado_civil')));
    $control_contratistas->setNumeroHijos(trim($request->getParameter('numero_hijos')));
    //***********************************************************************************************************
    $libreta_militar = array();
    $libreta_militar[] = $request->getParameter('numero_militar') ? trim($request->getParameter('numero_militar')) : "undefined";
    $libreta_militar[] = $request->getParameter('distrito') ? trim($request->getParameter('distrito')) : "undefined";
    $libreta_militar[] = $request->getParameter('clase_libreta');
    $control_contratistas->setLibretaMilitar(implode("$$",$libreta_militar));    
    //***********************************************************************************************************
    $direccion_envio = array();
    $direccion_envio[] = $request->getParameter('direccion_envio') ? trim($request->getParameter('direccion_envio')) : "undefined";
    $direccion_envio[] = $request->getParameter('municipio_envio') ? trim($request->getParameter('municipio_envio')) : "undefined";
    $direccion_envio[] = $request->getParameter('dpto_envio') ? trim($request->getParameter('dpto_envio')) : "undefined";
    $control_contratistas->setDireccionEnvio(implode("$$",$direccion_envio));
    //***********************************************************************************************************
    $educacion_basica = array();
    $educacion_basica[] = $request->getParameter('primaria') ? '1' : '0';
    $educacion_basica[] = $request->getParameter('secundaria') ? '1' : '0';
    $educacion_basica[] = $request->getParameter('media') ? '1' : '0';
    $educacion_basica[] = trim($request->getParameter('titulo_obtenido'));    
    $control_contratistas->setEducacionBasica(implode("$$",$educacion_basica));
    //***********************************************************************************************************
    $educacion_superior = array();
    $educacion_superior[] = $request->getParameter('modalidad') ? trim($request->getParameter('modalidad')) : "undefined";
    $educacion_superior[] = $request->getParameter('semestres_aprobados') ? trim($request->getParameter('semestres_aprobados')) : "0";
    $educacion_superior[] = $request->getParameter('esta_graduado');
    $control_contratistas->setEducacionSuperior(implode("$$",$educacion_superior));
    //***********************************************************************************************************
    $control_contratistas->setFechaGradoBasica($request->getParameter('fecha_grado_basica') ? $request->getParameter('fecha_grado_basica') : null);
    $control_contratistas->setTituloUniversitario(trim($request->getParameter('titulo_universitario')));
    $control_contratistas->setFechaGradoUniversitario($request->getParameter('fecha_grado_universitario') ? $request->getParameter('fecha_grado_universitario') : null);
    $control_contratistas->setNumeroTarjeta(trim($request->getParameter('numero_tarjeta')));
    $control_contratistas->setOtrosIdiomas(trim($request->getParameter('otros_idiomas')));
    $control_contratistas->setEps(trim($request->getParameter('eps')));
    $control_contratistas->setFondoPensiones(trim($request->getParameter('fondo_pensiones')));
    //***********************************************************************************************************
    $experiencia_laboral = array();
    $experiencia_laboral[] = $request->getParameter('spuanos') ? trim($request->getParameter('spuanos')) : "0";
    $experiencia_laboral[] = $request->getParameter('spumeses') ? trim($request->getParameter('spumeses')) : "0";
    $experiencia_laboral[] = $request->getParameter('espranos') ? trim($request->getParameter('espranos')) : "0";
    $experiencia_laboral[] = $request->getParameter('esprmeses') ? trim($request->getParameter('esprmeses')) : "0";
    $experiencia_laboral[] = $request->getParameter('tianos') ? trim($request->getParameter('tianos')) : "0";
    $experiencia_laboral[] = $request->getParameter('timeses') ? trim($request->getParameter('timeses')) : "0";
    $experiencia_laboral[] = $request->getParameter('sumanos') ? trim($request->getParameter('sumanos')) : "0";
    $experiencia_laboral[] = $request->getParameter('summeses') ? trim($request->getParameter('summeses')) : "0";
    $control_contratistas->setExperienciaLaboral(implode("$$",$experiencia_laboral));
    //***********************************************************************************************************
    $control_contratistas->save();
    //***********************************************************************************************************
    $this->redirect('control_contratistas/index');
  }

  public function executeDelete($request)
  {
    /**************************************************************************************************************/
    $currentForm="control_contratistas/delete";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $this->filtros_consulta = "a=1";
    /**************************************************************************************************************/
    $this->forward404Unless($control_contratistas = ControlContratistasPeer::retrieveByPk($request->getParameter('controlcontratistas_id')));
    $control_contratistas->delete();
    $this->redirect('control_contratistas/index');
  }
  
  public function getBasicCriteria(Criteria $c,$request)
  {
    //**************************************************************************************************************************
    $usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $c->setDistinct();
    //**************************************************************************************************************************
    if($request->getParameter('estadocontratista_id')){
		$c->add(ControlContratistasPeer::ESTADOCONTRATISTA_ID, $request->getParameter('estadocontratista_id'));
		$this->filtros_consulta .= "&estadocontratista_id=".$request->getParameter('estadocontratista_id');		
	}
    
    if($request->getParameter('tipocontrato_id')){
		$c->add(ControlContratistasPeer::TIPOCONTRATO_ID, $request->getParameter('tipocontrato_id'));
		$this->filtros_consulta .= "&tipocontrato_id=".$request->getParameter('tipocontrato_id');		
	}
     
    if($request->getParameter('cargo_id')){
		$c->add(ControlContratistasPeer::CARGO_ID, $request->getParameter('cargo_id'));
		$this->filtros_consulta .= "&cargo_id=".$request->getParameter('cargo_id');		
	}
    
    if($request->getParameter('dependencia_id')){
		$c->add(ControlContratistasPeer::DEPENDENCIA_ID, $request->getParameter('dependencia_id'));
		$this->filtros_consulta .= "&dependencia_id=".$request->getParameter('dependencia_id');		
	}
    
    if($request->getParameter('ubicacion_laboral')){
		$c->add(ControlContratistasPeer::UBICACION_LABORAL, '%'.$request->getParameter('ubicacion_laboral').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&ubicacion_laboral=".$request->getParameter('ubicacion_laboral');		
	}
       
    if($request->getParameter('nombre')){
		$c->add(ControlContratistasPeer::NOMBRE, '%'.$request->getParameter('nombre').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&nombre=".$request->getParameter('nombre');		
	}
    
    if($request->getParameter('primer_apellido')){
		$c->add(ControlContratistasPeer::PRIMER_APELLIDO, '%'.$request->getParameter('primer_apellido').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&primer_apellido=".$request->getParameter('primer_apellido');		
	}
    
    if($request->getParameter('segundo_apellido')){
		$c->add(ControlContratistasPeer::SEGUNDO_APELLIDO, '%'.$request->getParameter('segundo_apellido').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&segundo_apellido=".$request->getParameter('segundo_apellido');		
	}
    
    if($request->getParameter('eps')){
		$c->add(ControlContratistasPeer::EPS, '%'.$request->getParameter('eps').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&eps=".$request->getParameter('eps');		
	}
    
    if($request->getParameter('fondo_pensiones')){
		$c->add(ControlContratistasPeer::FONDO_PENSIONES, '%'.$request->getParameter('fondo_pensiones').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&fondo_pensiones=".$request->getParameter('fondo_pensiones');		
	}
    
    if($request->getParameter('tipoidentificacion_id')){
		$c->add(ControlContratistasPeer::TIPOIDENTIFICACION_ID, $request->getParameter('tipoidentificacion_id'));
		$this->filtros_consulta .= "&tipoidentificacion_id=".$request->getParameter('tipoidentificacion_id');		
	}
    
    if($request->getParameter('numero_identificacion')){
		$c->add(ControlContratistasPeer::NUMERO_IDENTIFICACION, '%'.$request->getParameter('numero_identificacion').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&numero_identificacion=".$request->getParameter('numero_identificacion');		
	}
    
    if($request->getParameter('genero')){
		$c->add(ControlContratistasPeer::GENERO, $request->getParameter('genero'));
		$this->filtros_consulta .= "&genero=".$request->getParameter('genero');		
	}
    
    if($request->getParameter('nacionalidad')){
		$c->add(ControlContratistasPeer::NACIONALIDAD, '%'.$request->getParameter('nacionalidad').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&nacionalidad=".$request->getParameter('nacionalidad');		
	}
    
    if($request->getParameter('numero_militar')){
		$c->add(ControlContratistasPeer::NUMERO_TARJETA, '%'.$request->getParameter('numero_militar').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&numero_militar=".$request->getParameter('numero_militar');		
	}
    
    // CONSULTA FECHA CREACION
	$fecha_creacion_inicial = $this->getRequestParameter('fecha_creacion_inicial');
	$fecha_creacion_final = $this->getRequestParameter('fecha_creacion_final');
	if($fecha_creacion_inicial != ""){
		if($fecha_creacion_final == ""){
			$fecha_creacion_final = $fecha_creacion_inicial;
		}
		$c->add(ControlContratistasPeer::FECHA_CREACION, $fecha_creacion_inicial.' 00:00:00', Criteria::GREATER_EQUAL);
		$c->addAnd(ControlContratistasPeer::FECHA_CREACION, $fecha_creacion_final.' 23:59:59', Criteria::LESS_EQUAL);
		$parametros_consulta .= "&fecha_creacion_inicial=".str_replace("/","-",$fecha_creacion_inicial);
		$parametros_consulta .= "&fecha_creacion_final=".str_replace("/","-",$fecha_creacion_final);		
	}
    
    // CONSULTA FECHA NACIMIENTO
	$str_fecha_inicial = $this->getRequestParameter('fecha_nacimiento_inicial');
	$str_fecha_final = $this->getRequestParameter('fecha_nacimiento_final');
	if($str_fecha_inicial != ""){
		if($str_fecha_final == ""){
			$str_fecha_final = $str_fecha_inicial;
		}
		$c->add(ControlContratistasPeer::FECHA_NACIMIENTO, $str_fecha_inicial.' 00:00:00', Criteria::GREATER_EQUAL);
		$c->addAnd(ControlContratistasPeer::FECHA_CREACION, $str_fecha_final.' 23:59:59', Criteria::LESS_EQUAL);
		$parametros_consulta .= "&fecha_nacimiento_inicial=".str_replace("/","-",$str_fecha_inicial);
		$parametros_consulta .= "&fecha_nacimiento_final=".str_replace("/","-",$str_fecha_final);		
	}
    
    // CONSULTA FECHA GRADO BASICA
	$str_fecha_inicial = $this->getRequestParameter('fecha_gradobasica_inicial');
	$str_fecha_final = $this->getRequestParameter('fecha_gradobasica_final');
	if($str_fecha_inicial != ""){
		if($str_fecha_final == ""){
			$str_fecha_final = $str_fecha_inicial;
		}
		$c->add(ControlContratistasPeer::FECHA_GRADO_BASICA, $str_fecha_inicial.' 00:00:00', Criteria::GREATER_EQUAL);
		$c->addAnd(ControlContratistasPeer::FECHA_GRADO_BASICA, $str_fecha_final.' 23:59:59', Criteria::LESS_EQUAL);
		$parametros_consulta .= "&fecha_gradobasica_inicial=".str_replace("/","-",$str_fecha_inicial);
		$parametros_consulta .= "&fecha_gradobasica_final=".str_replace("/","-",$str_fecha_final);		
	}
    
    // CONSULTA FECHA GRADO SUPERIOR
	$str_fecha_inicial = $this->getRequestParameter('fecha_gradosuperior_inicial');
	$str_fecha_final = $this->getRequestParameter('fecha_gradosuperior_final');
	if($str_fecha_inicial != ""){
		if($str_fecha_final == ""){
			$str_fecha_final = $str_fecha_inicial;
		}
		$c->add(ControlContratistasPeer::FECHA_GRADO_BASICA, $str_fecha_inicial.' 00:00:00', Criteria::GREATER_EQUAL);
		$c->addAnd(ControlContratistasPeer::FECHA_GRADO_BASICA, $str_fecha_final.' 23:59:59', Criteria::LESS_EQUAL);
		$parametros_consulta .= "&fecha_gradosuperior_inicial=".str_replace("/","-",$str_fecha_inicial);
		$parametros_consulta .= "&fecha_gradosuperior_final=".str_replace("/","-",$str_fecha_final);		
	}
    
    if($request->getParameter('ciudad_id')){
		$c->add(ControlContratistasPeer::CIUDAD_ID, $request->getParameter('ciudad_id'));
		$this->filtros_consulta .= "&ciudad_id=".$request->getParameter('ciudad_id');		
	}
    
    if($request->getParameter('email')){
		$c->add(ControlContratistasPeer::EMAIL, '%'.$request->getParameter('email').'%' ,Criteria::LIKE);
		$this->filtros_consulta .= "&email=".$request->getParameter('email');		
	}
    
    if($request->getParameter('titulo_obtenido')){
  		$c->add(ControlContratistasPeer::EDUCACION_BASICA, '%'.$request->getParameter('titulo_obtenido').'%' ,Criteria::LIKE);
  		$this->filtros_consulta .= "&titulo_obtenido=".$request->getParameter('titulo_obtenido');		
  	}
    
    if($request->getParameter('titulo_universitario')){
  		$c->add(ControlContratistasPeer::TITULO_UNIVERSITARIO, '%'.$request->getParameter('titulo_universitario').'%' ,Criteria::LIKE);
  		$this->filtros_consulta .= "&titulo_universitario=".$request->getParameter('titulo_universitario');		
  	}
    
    if($request->getParameter('numero_tarjeta')){
  		$c->add(ControlContratistasPeer::NUMERO_TARJETA, '%'.$request->getParameter('numero_tarjeta').'%' ,Criteria::LIKE);
  		$this->filtros_consulta .= "&numero_tarjeta=".$request->getParameter('numero_tarjeta');		
  	}
    
    if($request->getParameter('numero_hijos')){
  		$c->add(ControlContratistasPeer::NUMERO_HIJOS, $request->getParameter('numero_hijos'));
  		$this->filtros_consulta .= "&numero_hijos=".$request->getParameter('numero_hijos');		
  	}
    //**************************************************************************************************************************
    $c->addAscendingOrderByColumn(ControlContratistasPeer::CONTROLCONTRATISTAS_ID);
    //**************************************************************************************************************************
    return $c;
  }
}
