<?php

/**
 * novedades actions.
 *
 * @package    simad
 * @subpackage novedades
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
require_once(sfConfig::get('sf_lib_dir').'/tcpdf/config/lang/eng.php');
require_once(sfConfig::get('sf_lib_dir').'/tcpdf/tcpdf.php');

class novedadesActions extends sfActions
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
    /********************************************************************************************/
    $this->verificaPrilegio("novedades/list");
    $this->parametros = 'a=1';
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /********************************************************************************************/    
    $this->modulo_id = $this->getRequestParameter('modulo_id');
    $this->consecutivo_id = $this->getRequestParameter('consecutivo_id');
    /********************************************************************************************/
    $c = new Criteria();
    /********************************************************************************************/
    //POR DEFECTO SE CONSULTA POR EL CODIGO PRINCIPAL Y EL MODULO
    if(!$this->getUser()->checkPerm('NOVEDADES_LISTAR_TODAS', $usuariologuiado))
    {    
        $c->add(NovedadesPeer::USUARIO_ID,$usuariologuiado);        
    }
    /***********************************FILTROS**************************************************/
    if($this->getRequestParameter('tipocontrol_id'))
    {
       $c->add(NovedadesPeer::TIPOCONTROL_ID,$this->getRequestParameter('tipocontrol_id'));
       $this->parametros .= "&tipocontrol_id=" . $this->getRequestParameter('tipocontrol_id');
    }
    /********************************************************************************************/
    if($this->getRequestParameter('usuario_id'))
    {
       $c->add(NovedadesPeer::USUARIO_ID,$this->getRequestParameter('usuario_id'));
       $this->parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('descripcion'))
    {
       $c->add(NovedadesPeer::DESCRIPCION,'%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
       $this->parametros .= "&descripcion=" . $this->getRequestParameter('descripcion');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('modulo_select'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $c->add(NovedadesPeer::MODULO_ID,$modulo_select);
       //$c = $this->getJoinModulo($c,$modulo_select,$codigo_principal);
       $this->parametros .= "&modulo_select=" . $modulo_select;       
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('modulo_select') && $this->getRequestParameter('codigo_principal'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $codigo_principal = $this->getRequestParameter('codigo_principal');
       $c = $this->getJoinModulo($c,$modulo_select,$codigo_principal);       
       $this->parametros .= "&modulo_select=" . $this->getRequestParameter('modulo_select');
       $this->parametros .= "&codigo_principal=" . $this->getRequestParameter('codigo_principal');
    }
    /*******************************************************************************************/    
    if($this->modulo_id && $this->consecutivo_id)
    {
       $c->add(NovedadesPeer::MODULO_ID,$this->modulo_id);
       $c->add(NovedadesPeer::CODIGO_PRINCIPAL,$this->consecutivo_id);       
    }
    /*******************************************************************************************/    
    if($this->getRequestParameter('numero_factura'))
    {
       $c->add(NovedadesPeer::NUMERO_FACTURA,'%'.$this->getRequestParameter('numero_factura').'%',Criteria::LIKE);
       $this->parametros .= "&numero_factura=" . $this->getRequestParameter('numero_factura');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('guia'))
    {
       $c->add(NovedadesPeer::GUIA,'%'.$this->getRequestParameter('guia').'%',Criteria::LIKE);
       $this->parametros .= "&guia=" . $this->getRequestParameter('guia');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('valor_factura'))
    {
       $c->add(NovedadesPeer::VALOR_FACTURA,'%'.$this->getRequestParameter('valor_factura').'%',Criteria::LIKE);
       $this->parametros .= "&valor_factura=" . $this->getRequestParameter('valor_factura');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('remitente'))
    {
       $c->add(NovedadesPeer::REMITENTE,'%'.$this->getRequestParameter('remitente').'%',Criteria::LIKE);
       $this->parametros .= "&remitente=" . $this->getRequestParameter('remitente');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('destinatario'))
    {
       $c->add(NovedadesPeer::DESTINATARIO,'%'.$this->getRequestParameter('destinatario').'%',Criteria::LIKE);
       $this->parametros .= "&destinatario=" . $this->getRequestParameter('destinatario');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('tipo_documento'))
    {
       $c->add(NovedadesPeer::TIPO_DOCUMENTO,'%'.$this->getRequestParameter('tipo_documento').'%',Criteria::LIKE);
       $this->parametros .= "&tipo_documento=" . $this->getRequestParameter('tipo_documento');
    }
    /*******************************************************************************************/
    if ($this->getRequestParameter('fecha_creacion_inicial')) {
		if ($this->getRequestParameter('fecha_creacion_final')) {
	        $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_final').' 23:59:59',Criteria::LESS_THAN);
	        $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	        $this->parametros .= "&fecha_creacion_final=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_final'));
	     }else{
		    $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
		    $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	     }
	 }    
    /****************************************ORDEN***********************************************/
    if($this->getRequestParameter('orden'))
    {
        $c->addDescendingOrderByColumn(NovedadesPeer::$this->getRequestParameter('orden'));                
        $this->parametros .= "&orden=" . $this->getRequestParameter('orden');
    }
    else{
        $c->addDescendingOrderByColumn(NovedadesPeer::FECHA_CREACION);
    }
    /********************************************************************************************/    
    $pager = new sfPropelPager('Novedades', 10);
    $pager->setCriteria($c);
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
	/***********************************************************************************************/
    $dir_novedades  = ParametroPeer::retrieveByPk(60);			
    $this->alias_ajuntos = '/'.$dir_novedades->getValortexto().'/';    
  }
  
  public function executeReporteGeneral()
  {
    $this->setLayout(false);
    /********************************************************************************************/    
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /********************************************************************************************/    
    $this->modulo_id = $this->getRequestParameter('modulo_id');
    $this->consecutivo_id = $this->getRequestParameter('consecutivo_id');
    /********************************************************************************************/
    $c = new Criteria();
    /********************************************************************************************/
    //POR DEFECTO SE CONSULTA POR EL CODIGO PRINCIPAL Y EL MODULO
    if(!$this->getUser()->checkPerm('NOVEDADES_LISTAR_TODAS', $usuariologuiado))
    {    
        $c->add(NovedadesPeer::USUARIO_ID,$usuariologuiado);        
    }
    /***********************************FILTROS**************************************************/
    if($this->getRequestParameter('tipocontrol_id'))
    {
       $c->add(NovedadesPeer::TIPOCONTROL_ID,$this->getRequestParameter('tipocontrol_id'));
       $this->parametros .= "&tipocontrol_id=" . $this->getRequestParameter('tipocontrol_id');
    }
    /********************************************************************************************/
    if($this->getRequestParameter('usuario_id'))
    {
       $c->add(NovedadesPeer::USUARIO_ID,$this->getRequestParameter('usuario_id'));
       $this->parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('descripcion'))
    {
       $c->add(NovedadesPeer::DESCRIPCION,'%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
       $this->parametros .= "&descripcion=" . $this->getRequestParameter('descripcion');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('modulo_select'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $c->add(NovedadesPeer::MODULO_ID,$modulo_select);       
       $this->parametros .= "&modulo_select=" . $modulo_select;       
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('modulo_select') && $this->getRequestParameter('codigo_principal'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $codigo_principal = $this->getRequestParameter('codigo_principal');
       $c = $this->getJoinModulo($c,$modulo_select,$codigo_principal);       
       $this->parametros .= "&modulo_select=" . $this->getRequestParameter('modulo_select');
       $this->parametros .= "&codigo_principal=" . $this->getRequestParameter('codigo_principal');
    }
    /*******************************************************************************************/    
    if($this->modulo_id && $this->consecutivo_id)
    {
       $c->add(NovedadesPeer::MODULO_ID,$this->modulo_id);
       $c->add(NovedadesPeer::CODIGO_PRINCIPAL,$this->consecutivo_id);       
    }
    /*******************************************************************************************/    
    if($this->getRequestParameter('numero_factura'))
    {
       $c->add(NovedadesPeer::NUMERO_FACTURA,'%'.$this->getRequestParameter('numero_factura').'%',Criteria::LIKE);
       $this->parametros .= "&numero_factura=" . $this->getRequestParameter('numero_factura');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('guia'))
    {
       $c->add(NovedadesPeer::GUIA,'%'.$this->getRequestParameter('guia').'%',Criteria::LIKE);
       $this->parametros .= "&guia=" . $this->getRequestParameter('guia');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('valor_factura'))
    {
       $c->add(NovedadesPeer::VALOR_FACTURA,'%'.$this->getRequestParameter('valor_factura').'%',Criteria::LIKE);
       $this->parametros .= "&valor_factura=" . $this->getRequestParameter('valor_factura');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('remitente'))
    {
       $c->add(NovedadesPeer::REMITENTE,'%'.$this->getRequestParameter('remitente').'%',Criteria::LIKE);
       $this->parametros .= "&remitente=" . $this->getRequestParameter('remitente');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('destinatario'))
    {
       $c->add(NovedadesPeer::DESTINATARIO,'%'.$this->getRequestParameter('destinatario').'%',Criteria::LIKE);
       $this->parametros .= "&destinatario=" . $this->getRequestParameter('destinatario');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('tipo_documento'))
    {
       $c->add(NovedadesPeer::TIPO_DOCUMENTO,'%'.$this->getRequestParameter('tipo_documento').'%',Criteria::LIKE);
       $this->parametros .= "&tipo_documento=" . $this->getRequestParameter('tipo_documento');
    }
    /*******************************************************************************************/
    if ($this->getRequestParameter('fecha_creacion_inicial')) {
		if ($this->getRequestParameter('fecha_creacion_final')) {
	        $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_final').' 23:59:59',Criteria::LESS_THAN);
	        $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	        $this->parametros .= "&fecha_creacion_final=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_final'));
	     }else{
		    $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
		    $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	     }
	 }    
    /****************************************ORDEN***********************************************/
    if($this->getRequestParameter('orden'))
    {
        $c->addDescendingOrderByColumn(NovedadesPeer::$this->getRequestParameter('orden'));                
        $this->parametros .= "&orden=" . $this->getRequestParameter('orden');
    }
    else{
        $c->addDescendingOrderByColumn(NovedadesPeer::FECHA_CREACION);
    }
    /***************************************CLONAR OBJETO CRITERIA********************************/
    $criteria = clone $c;  // Clonamos el objeto criteria para evitar modificar el original
    /****************************************ALIAS***********************************************/
    $criteria->addAlias("NOVEDADES",NovedadesPeer::TABLE_NAME);
    $criteria->addAlias("MODU",ModuloPeer::TABLE_NAME);
    $criteria->addAlias("US",UsuarioPeer::TABLE_NAME);
    $criteria->addAlias("SER",ServicioPeer::TABLE_NAME);
    $criteria->addAlias("CI",ComInternaPeer::TABLE_NAME);
    $criteria->addAlias("CR",ComRecibidaPeer::TABLE_NAME);
    $criteria->addAlias("CE",ComEnviadaPeer::TABLE_NAME);
    $criteria->addAlias("UD",UnidadDocumentalPeer::TABLE_NAME);
    $criteria->addAlias("PQ",PqrPeer::TABLE_NAME);
    $criteria->addAlias("PR",ProcedimientoPeer::TABLE_NAME);
    $criteria->addAlias("CLI",ClientePeer::TABLE_NAME);
    $criteria->addAlias("DOC",DocumentacionPeer::TABLE_NAME);
    $criteria->addAlias("FACT",FacturaPeer::TABLE_NAME);
    $criteria->addAlias("PROV",ProveedorPeer::TABLE_NAME);
    $criteria->addAlias("PRES",PrestamoPeer::TABLE_NAME);
    $criteria->addAlias("TC",TipoControlPeer::TABLE_NAME);
    /*************************************JOINS**************************************************/
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","CI.COMINTERNA_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","CR.COMRECIBIDA_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","CE.COMENVIADA_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","UD.UNIDADDOCUMENTAL_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","PQ.PQR_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","SER.SERVICIO_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","PR.PROCEDIMIENTO_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","CLI.CLIENTE_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","DOC.DOCUMENTACION_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","FACT.FACTURA_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","PROV.PROVEEDOR_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.CODIGO_PRINCIPAL","PRES.PRESTAMO_ID",Criteria::LEFT_JOIN);
    $criteria->addJoin("NOVEDADES.USUARIO_ID","US.USUARIO_ID",Criteria::INNER_JOIN);
    $criteria->addJoin("NOVEDADES.MODULO_ID","MODU.MODULO_ID",Criteria::INNER_JOIN);
    $criteria->addJoin("NOVEDADES.TIPOCONTROL_ID","TC.TIPOCONTROL_ID",Criteria::INNER_JOIN);
    /*************************************SELECT COLUMNS********************************************/
    $criteria->addSelectColumn("NOVEDADES.NOVEDADES_ID");//0
	$criteria->addSelectColumn("US.NOMBRE");//1
    $criteria->addSelectColumn("US.APELLIDO");//2
	$criteria->addSelectColumn("TC.DESCRIPCION");//3
    $criteria->addSelectColumn("MODU.DESCRIPCION");//4
	$criteria->addSelectColumn("NOVEDADES.DESCRIPCION");//5
	$criteria->addSelectColumn("NOVEDADES.CODIGO_PRINCIPAL");//6
	$criteria->addSelectColumn("NOVEDADES.DESCRIPCION");//7
	$criteria->addSelectColumn("NOVEDADES.FECHA_CREACION");//8
	$criteria->addSelectColumn("NOVEDADES.RUTA");//9
	$criteria->addSelectColumn("NOVEDADES.REMITENTE");//10
	$criteria->addSelectColumn("NOVEDADES.DESTINATARIO");//11
	$criteria->addSelectColumn("NOVEDADES.VALOR_FACTURA");//12
	$criteria->addSelectColumn("NOVEDADES.TIPO_DOCUMENTO");//13
	$criteria->addSelectColumn("NOVEDADES.GUIA");//14
	$criteria->addSelectColumn("NOVEDADES.NUMERO_FACTURA");//15
    $criteria->addSelectColumn("CI.RADICADO");//16
    $criteria->addSelectColumn("CR.RADICADO");//17
    $criteria->addSelectColumn("CE.RADICADO");//18
    $criteria->addSelectColumn("UD.CODIGO_BARRAS");//19
    $criteria->addSelectColumn("PQ.PQR_ID");//20
    $criteria->addSelectColumn("SER.RADICADO");//21
    $criteria->addSelectColumn("PR.CODIGO");//22
    $criteria->addSelectColumn("CLI.CODIGO_CLIENTE");//23
    $criteria->addSelectColumn("DOC.CODIGO_BARRAS");//24
    $criteria->addSelectColumn("FACT.RADICADO");//25
    $criteria->addSelectColumn("PROV.NIT");//26
    $criteria->addSelectColumn("PRES.NUMERO_RADICACION");//27
    $criteria->addSelectColumn("NOVEDADES.MODULO_ID");//28
    /***********************************************************************************************/
    $this->resultset = NovedadesPeer::doSelectStmt($criteria);
    $this->usuariologuiado = $this->getUser()->getAttribute('username', '', 'subscriber');
	/***********************************************************************************************/
  }
  
  public function executeReporte()
  {
    /***********************************************************************************************/
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /***********************************************************************************************/
    $c = new Criteria();
    /***********************************************************************************************/
    //POR DEFECTO SE CONSULTA POR EL CODIGO PRINCIPAL Y EL MODULO
    if(!$this->getUser()->checkPerm('NOVEDADES_LISTAR_TODAS', $usuariologuiado))
    {    
        $c->add(NovedadesPeer::USUARIO_ID,$usuariologuiado);        
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('tipocontrol_id'))
    {
       $c->add(NovedadesPeer::TIPOCONTROL_ID,$this->getRequestParameter('tipocontrol_id'));
       $this->parametros .= "&tipocontrol_id=" . $this->getRequestParameter('tipocontrol_id');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('usuario_id'))
    {
       $c->add(NovedadesPeer::USUARIO_ID,$this->getRequestParameter('usuario_id'));
       $this->parametros .= "&usuario_id=" . $this->getRequestParameter('usuario_id');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('descripcion'))
    {
       $c->add(NovedadesPeer::DESCRIPCION,'%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
       $this->parametros .= "&descripcion=" . $this->getRequestParameter('descripcion');
    }
    /*******************************************************************************************/
    if($this->getRequestParameter('modulo_select'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $c->add(NovedadesPeer::MODULO_ID,$modulo_select);
       //$c = $this->getJoinModulo($c,$modulo_select,$codigo_principal);
       $this->parametros .= "&modulo_select=" . $modulo_select;       
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('modulo_select') && $this->getRequestParameter('codigo_principal'))
    {
       $modulo_select = $this->getRequestParameter('modulo_select');
       $codigo_principal = $this->getRequestParameter('codigo_principal');
       $c = $this->getJoinModulo($c,$modulo_select,$codigo_principal);       
       $this->parametros .= "&modulo_select=" . $this->getRequestParameter('modulo_select');
       $this->parametros .= "&codigo_principal=" . $this->getRequestParameter('codigo_principal');
    }
    /***********************************************************************************************/    
    if($this->modulo_id && $this->consecutivo_id)
    {
       $c->add(NovedadesPeer::MODULO_ID,$this->modulo_id);
       $c->add(NovedadesPeer::CODIGO_PRINCIPAL,$this->consecutivo_id);       
    }
    /***********************************************************************************************/ 
    if($this->getRequestParameter('numero_factura'))
    {
       $c->add(NovedadesPeer::NUMERO_FACTURA,'%'.$this->getRequestParameter('numero_factura').'%',Criteria::LIKE);
       $this->parametros .= "&numero_factura=" . $this->getRequestParameter('numero_factura');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('guia'))
    {
       $c->add(NovedadesPeer::GUIA,'%'.$this->getRequestParameter('guia').'%',Criteria::LIKE);
       $this->parametros .= "&guia=" . $this->getRequestParameter('guia');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('valor_factura'))
    {
       $c->add(NovedadesPeer::VALOR_FACTURA,'%'.$this->getRequestParameter('valor_factura').'%',Criteria::LIKE);
       $this->parametros .= "&valor_factura=" . $this->getRequestParameter('valor_factura');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('remitente'))
    {
       $c->add(NovedadesPeer::REMITENTE,'%'.$this->getRequestParameter('remitente').'%',Criteria::LIKE);
       $this->parametros .= "&remitente=" . $this->getRequestParameter('remitente');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('destinatario'))
    {
       $c->add(NovedadesPeer::DESTINATARIO,'%'.$this->getRequestParameter('destinatario').'%',Criteria::LIKE);
       $this->parametros .= "&destinatario=" . $this->getRequestParameter('destinatario');
    }
    /***********************************************************************************************/
    if($this->getRequestParameter('tipo_documento'))
    {
       $c->add(NovedadesPeer::TIPO_DOCUMENTO,'%'.$this->getRequestParameter('tipo_documento').'%',Criteria::LIKE);
       $this->parametros .= "&tipo_documento=" . $this->getRequestParameter('tipo_documento');
    }
    /***********************************************************************************************/
    
    if ($this->getRequestParameter('fecha_creacion_inicial')) {
		if ($this->getRequestParameter('fecha_creacion_final')) {
	        $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_final').' 23:59:59',Criteria::LESS_THAN);
	        $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	        $this->parametros .= "&fecha_creacion_final=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_final'));
	     }else{
		    $c->add(NovedadesPeer::FECHA_CREACION,$this->getRequestParameter('fecha_creacion_inicial').' 00:00:00',Criteria::GREATER_THAN);
		    $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$this->getRequestParameter('fecha_creacion_inicial'));
	     }
	 }
     /***********************************FIN DE LOS FILTROS INICIA EL ORDEN****************************/    
    if($this->getRequestParameter('orden'))
    {
        $c->addDescendingOrderByColumn(NovedadesPeer::$this->getRequestParameter('orden'));                
        $this->parametros .= "&orden=" . $this->getRequestParameter('orden');
    }
    else{
        $c->addDescendingOrderByColumn(NovedadesPeer::FECHA_CREACION);
    }
    /*************************************************************************************************/
    $c->clearSelectColumns();
    //adicionamos las columnas que debe retornar la consulta
    $c->addSelectColumn(NovedadesPeer::NOVEDADES_ID);//1
	$c->addSelectColumn(NovedadesPeer::USUARIO_ID);//2
	$c->addSelectColumn(NovedadesPeer::MODULO_ID);//3
	$c->addSelectColumn(NovedadesPeer::TIPOCONTROL_ID);//4
	$c->addSelectColumn(NovedadesPeer::CODIGO_PRINCIPAL);//5
	$c->addSelectColumn(NovedadesPeer::DESCRIPCION);//6
	$c->addSelectColumn(NovedadesPeer::FECHA_CREACION);//7
	$c->addSelectColumn(NovedadesPeer::RUTA);//8
	$c->addSelectColumn(NovedadesPeer::REMITENTE);//9
	$c->addSelectColumn(NovedadesPeer::DESTINATARIO);//10
	$c->addSelectColumn(NovedadesPeer::VALOR_FACTURA);//11
	$c->addSelectColumn(NovedadesPeer::TIPO_DOCUMENTO);//12
	$c->addSelectColumn(NovedadesPeer::GUIA);//13
	$c->addSelectColumn(NovedadesPeer::NUMERO_FACTURA);//14
    /*************************************************************************************************/
    //$novedadesList = NovedadesPeer::doSelectStmt($c);
    $resultset = NovedadesPeer::doSelectStmt($c);    
	/*************************************************************************************************/
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');					 	        	
	$usuario_data  = UsuarioPeer::retrieveByPK($usuariologuiado);
    $tipo_reporte = $this->getRequestParameter('tipo_reporte');
    /*************************************************************************************************/
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ARCHIDHU PGD SAS');
    $pdf->SetTitle('Novedades Del Servicio');
    $pdf->SetSubject('Novedades');
    $pdf->SetKeywords('TCPDF, PDF, Novedades, test, guide');
    
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    //set margins
    $pdf->SetMargins(5, 15, 5);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            

    //logo de la entidad    
    $pdf->SetHeaderData("logo_empresa_encabezado_cartas.jpg", 80, "", "");
    // set font
    $pdf->SetFont('helvetica', '', 9);

    // add a page
    $pdf->AddPage();
    
    //variables para el html                
    $htmlCuerpo = '<table cellspacing="0"  cellpadding="0" border="1" width="99%">';    
    switch($tipo_reporte)
    {
        case 1:
              $htmlCuerpo .= '<center><h1 align="center">Control De Correspondencia Sin Radicar</h1></center><br />';
              $htmlCuerpo .= '<tr>
              <th align="center">Fecha</th>
              <th align="center">Remitente</th>
              <th align="center">Destinatario</th>
              <th align="center">Tipo Documento</th>        
              <th align="center">Numero Guia</th>                        
              </tr>';
              while($object = $resultset->fetch())
              { 
                $htmlCuerpo .= '<tr><th align="center">'.$object[7].'&nbsp;</th>';
                $htmlCuerpo .= '<td align="center">'.($object[9]).'&nbsp;</td>';
                $htmlCuerpo .= '<td style="font-size: medium;">'.($object[7]).'&nbsp;</td>';
                $htmlCuerpo .= '<td align="center">'.($object[12]).'&nbsp;</td>';
                $htmlCuerpo .= '<td align="center">'.$object[13].'&nbsp;</td>';                        
                $htmlCuerpo .= '</tr>';        
              }
        break;        
        case 2:
              $htmlCuerpo .= '<center><h1 align="center">Control De Facturas Recibidas</h1></center><br/>';
              $htmlCuerpo .= '<tr>
              <th align="center">Item</th>
              <th align="center">Proveedor</th>
              <th align="center">Numero Factura</th>
              <th align="center">Observaciones</th>
              </tr>';
              $item = 1;
              while($object = $resultset->fetch())
              { 
                $htmlCuerpo .= '<tr><th align="center">'.$item++.'</th>';
                $htmlCuerpo .= '<td align="center">'.($object[9]).'&nbsp;</td>';
                $htmlCuerpo .= '<td style="font-size: medium;">'.$object[14].'&nbsp;</td>';
                $htmlCuerpo .= '<td style="font-size: medium;">'.($object[6]).'&nbsp;</td>';
                $htmlCuerpo .= '</tr>';
              }
        break;
        case 3:
              $htmlCuerpo .= '<center><h1 align="center">Control De Entrega Facturas Mayoristas</h1></center><br/>';
              $htmlCuerpo .= '<tr>
              <th align="center">Proveedor</th>
              <th align="center">Numero Factura</th>
              <th align="center">Valor Factura</th>
              <th align="center">Fecha Recibido</th>        
              <th align="center">Entregado Por:</th>
              <th align="center">Recibido Por:</th>                        
              </tr>';
              while($object = $resultset->fetch())
              { 
                $htmlCuerpo .= '<tr><th align="center">'.($object[9]).'</th>';
                $htmlCuerpo .= '<td align="center">'.$object[13].'&nbsp;</td>';
                $htmlCuerpo .= '<td style="font-size: medium;">'.$object[11].'&nbsp;</td>';
                $htmlCuerpo .= '<td style="font-size: medium;">'.$object[7].'&nbsp;</td>';
                $htmlCuerpo .= '<td align="center">&nbsp;</td>';
                $htmlCuerpo .= '<td align="center">&nbsp;</td>';                        
                $htmlCuerpo .= '</tr>';        
              }
        break;
    }
    //***************************************************************************************    
    $htmlCuerpo .= '</table>';
    //exit;
    //***************************************************************************************    
    $pdf->writeHTML($htmlCuerpo, true, false, true, false,'');    
    //***************************************************************************************
    $pdf->Output('novedades_del_servicio.pdf', 'I');
    //$pdf->Output();
    //============================================================+
    // END OF FILE                                                 
    //============================================================+

    //***************************************************************************************
            
  }
  
  public function executeCreate()
  {
    $this->verificaPrilegio("novedades/create");    
    $this->consecutivo_id = $this->getRequestParameter('consecutivo_id');    
    $this->modulo_id = $this->getRequestParameter('modulo_id');    
    $this->novedades = new Novedades();
    
    $c = new Criteria();
    if($this->modulo_id)
    {
       $c->add(TipoControlPeer::MODULO_ID,$this->modulo_id);    
    }    
    $this->tipos_control = TipoControlPeer::doSelect($c);
    
    $this->setTemplate('edit');
  }
  
  public function executeConsulta()
  {
    $this->verificaPrilegio("novedades/consulta");    
    $this->consecutivo_id = $this->getRequestParameter('consecutivo_id');    
    $this->modulo_id = $this->getRequestParameter('modulo_id');    
    $this->novedades = new Novedades();    
  }
  
  public function executeSelectReporte($request)
  {    
    /******************************************************************************************************/    
    $this->parametros = 'a=1';
    //$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /******************************************************************************************************/    
    $this->parametros .= "&modulo_id=" . $request->getParameter('modulo_id');
    $this->parametros .= "&consecutivo_id=" . $request->getParameter('consecutivo_id');    
    /******************************************************************************************************/
    if($request->getParameter('tipocontrol_id'))
    {       
       $this->parametros .= "&tipocontrol_id=" . $request->getParameter('tipocontrol_id');
    }
    /******************************************************************************************************/
    if($request->getParameter('usuario_id'))
    {       
       $this->parametros .= "&usuario_id=" . $request->getParameter('usuario_id');
    }
    /******************************************************************************************************/
    if($request->getParameter('descripcion'))
    {       
       $this->parametros .= "&descripcion=" . $request->getParameter('descripcion');
    }
    /******************************************************************************************************/
    if($request->getParameter('modulo_select'))
    {       
       $this->parametros .= "&modulo_select=" . $request->getParameter('modulo_select');
    }
    /******************************************************************************************************/
    if($request->getParameter('codigo_principal'))
    {       
       //$this->parametros .= "&codigo_principal=" . $this->getRequestParameter('codigo_principal');
    }
    /******************************************************************************************************/    
    if($this->getRequestParameter('numero_factura'))
    {       
       $this->parametros .= "&numero_factura=" . $this->getRequestParameter('numero_factura');
    }
    /******************************************************************************************************/
    if($this->getRequestParameter('guia'))
    {       
       $this->parametros .= "&guia=" . $this->getRequestParameter('guia');
    }
    /******************************************************************************************************/
    if($this->getRequestParameter('valor_factura'))
    {     
       $this->parametros .= "&valor_factura=" . $this->getRequestParameter('valor_factura');
    }
    /******************************************************************************************************/
    if($this->getRequestParameter('remitente'))
    {     
       $this->parametros .= "&remitente=" . $this->getRequestParameter('remitente');
    }
    /******************************************************************************************************/
    if($this->getRequestParameter('destinatario'))
    {     
       $this->parametros .= "&destinatario=" . $this->getRequestParameter('destinatario');
    }
    /******************************************************************************************************/
    if($this->getRequestParameter('tipo_documento'))
    {       
       $this->parametros .= "&tipo_documento=" . $this->getRequestParameter('tipo_documento');
    }
    /******************************************************************************************************/        
    if ($request->getParameter('fecha_creacion_inicial')) {
		if ($request->getParameter('fecha_creacion_final')) {
	        $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$request->getParameter('fecha_creacion_inicial'));
	        $this->parametros .= "&fecha_creacion_final=" . str_replace("/","-",$request->getParameter('fecha_creacion_final'));
	     }else{
		    $this->parametros .= "&fecha_creacion_inicial=" . str_replace("/","-",$request->getParameter('fecha_creacion_inicial'));
	     }
	 }
     /******************************************************************************************************/
     if($this->getRequestParameter('orden'))
     {                        
        $this->parametros .= "&orden=" . $this->getRequestParameter('orden');
     }    
     /******************************************************************************************************/         
  }
  
  public function executeEdit($request)
  {
    $this->verificaPrilegio("novedades/edit");    
    $this->novedades = NovedadesPeer::retrieveByPk($request->getParameter('novedades_id'));    
    $this->consecutivo_id = $this->novedades->getCodigoPrincipal();
    $this->modulo_id = $this->getRequestParameter('modulo_id');
    //*************************************************************************************
    $c = new Criteria();
    if($this->modulo_id)
    {
       $c->add(TipoControlPeer::MODULO_ID,$this->modulo_id);    
    }    
    $this->tipos_control = TipoControlPeer::doSelect($c);
    //*************************************************************************************
  }
  
  public function executeShow($request)
  {
    $this->novedades = NovedadesPeer::retrieveByPk($request->getParameter('novedades_id'));
  }
  
  public function executeUpdate($request)
  {
    if(!$request->getParameter('novedades_id')){
        $novedades = new Novedades();
        $novedades->setFechaCreacion(date("Y-m-d G:i:s"));
        $novedades->setCodigoPrincipal($request->getParameter('consecutivo_id'));
        $old_files = false;
        $txt_oldfiles = "";
    }else{
        $novedades = NovedadesPeer::retrieveByPk($request->getParameter('novedades_id'));
        $old_files = true;
        $txt_oldfiles = $novedades->getRuta();
    }    
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $dir_novedades  = ParametroPeer::retrieveByPk(60);
    //*************************************************************************************
    $novedades->setTipocontrolId($request->getParameter('tipocontrol_id'));
    $novedades->setModuloId($request->getParameter('modulo_id'));
    $novedades->setUsuarioId($usuariologuiado);
    $novedades->setDescripcion($request->getParameter('descripcion'));
    $novedades->setGuia($request->getParameter('guia'));
    $novedades->setRemitente($request->getParameter('remitente'));
    $novedades->setDestinatario($request->getParameter('destinatario'));
    $novedades->setTipoDocumento($request->getParameter('tipo_documento'));
    $novedades->setValorFactura($request->getParameter('valor_factura'));
    $novedades->setNumeroFactura($request->getParameter('numero_factura'));
    //**************************************************************************************************
    $str_files = trim($this->getRequestParameter('archivo'));
    $novedades->setRuta($this->resolveUrlAttachment($str_files,$old_files,$txt_oldfiles));
    //**************************************************************************************************
    $novedades->save();
    //*************************************************************************************
    if($novedades->getPrimaryKey()){
        $this->redirect('novedades/index');
    }else{
        $this->setTemplate('edit');
    }
    //*************************************************************************************
  }
  
  public function executeFile()
  {
  	    $this->setLayout('layout-modal');
  }
  
  
  public function executeEnvioAlerta()
  {
    $modulo_id = $this->getRequestParameter('modulo_id');
    $consecutivo_id = $this->getRequestParameter('consecutivo_id');
    $usuario_id = $this->getUsuarioDestino($consecutivo_id);    
    if($usuario_id)
    {
       $this->envioEmail($consecutivo_id,$usuario_id);
    }    
    $this->redirect('novedades/index?modulo_id=0&consecutivo_id=0');
  }
  
  public function getUsuarioDestino($comunicacion_id)
  {
    /******************************************************************************************************/	
	$c  = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comunicacion_id);
	$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
	$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);	
    $comunicacion = ComrecibidaUsuarioPeer::doSelectOne($c);
    if($comunicacion)
    {
        return $comunicacion->getUsuarioId();
    }
    else
    {
        return 0;
    }
    /******************************************************************************************************/
  }
  
  public function executeUploads()
  {   	
    //*************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
    $dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
    //$dir_novedades  = ParametroPeer::retrieveByPk(60);
    //*************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file)
    {
        //*********************************************************************************
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $directorio_entidad = $dirRaiz.$entidad_text."/";
        $directorio_tmp = $directorio_entidad.$dirTmp."/";    	
        //*********************************************************************************
        $cons    = $this->getNewConsecutivo();
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $directorio = simad_util::createPath($directorio_tmp);
        //*********************************************************************************
        @move_uploaded_file($file['tmp_name'], $directorio.'/'.$cons.'_'.$fileName);
        //*********************************************************************************
        $this->fileName = $cons.'_'.$fileName;                                                              		
    }
    $this->setLayout('layout-modal');     	    
  }
    
  public function getNewConsecutivo()
  {
    $cons = ParametroPeer::retrieveByPk(8);
    $regId = $cons->getValorNumerico();
    if($regId > 0){
    	$cons->setValorNumerico($regId+1);
    	$cons->save();
    }
    return $regId;		
  }
      
  public function executeDelete($request)
  {
    $this->forward404Unless($novedades = NovedadesPeer::retrieveByPk($request->getParameter('novedades_id')));

    $novedades->delete();

    $this->redirect('novedades/index');
  }
  
  public function envioEmail($comrecibida_id,$user)
  {
    $com_recibida_mail = ComRecibidaPeer::retrieveByPK($comrecibida_id);
  	$usuario = UsuarioPeer::retrieveByPK($user);
    $cuerpo = '
    <html>
    <head>
    <title></title>
    </head>
    <body>
    <div id="cotenedor">
    <br>
    Este es un mensaje para informarle que se le ha radicado una Comunicacion Entrante: 
    <br>
    <br>
    Fecha Radicacion: '.$com_recibida_mail->getFechaCreacion().' <br>
    Numero De Radicado: '.$com_recibida_mail->getRadicado().'<br>
    Remitente: '.$com_recibida_mail->getDirectorioExterno().'<br>
    Asunto: '.$com_recibida_mail->getAsuntoRecibida().'<br>
    Detalle Del Asunto: '.$com_recibida_mail->getAsunto().'<br>
    Tipo De Comunicaci�n: '.$com_recibida_mail->getTipoComRecibida().'<br>
    Observaciones: '.$com_recibida_mail->getObservaciones().'<br>
    <br>
    </div>
    </body></html>';
    $cabeceras = "Content-type: text/html\r\n";
    //*********************************************************************************************************************
    $baseMail = new BaseMailSimad();
    $baseMail->SetSubject('CAD : Novedades');
    $baseMail->SetMsgHTML($cuerpo);
    $baseMail->SetAddAddress($usuario->getEmail(), $usuario->getEmail());    
    if($baseMail->InitSend() === true)
    {
       $baseMail->writetolog("Alerta enviada: " . $com_recibida_mail->getRadicado() . " Enviado a: " . $usuario->getEmail());
    }else{
       $baseMail->writetolog("Error al enviar alerta: " . $com_recibida_mail->getRadicado() . " Cuenta correo: " . $usuario->getEmail());
    }
    //***********************************************************************************************************************
  }
  
  public function resolveUrlAttachment($files_new,$is_files_old=false,$files_old_text="")
  {
	$url_files = "";
	//*******************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz       = ParametroPeer::retrieveByPk(9)->getValortexto();	
	$alias_object  = ParametroPeer::retrieveByPk(12)->getValortexto();
	$dirTmp        = ParametroPeer::retrieveByPk(65)->getValortexto();
    $dir_object    = ParametroPeer::retrieveByPk(60)->getValortexto();    
	//********************************************************************************
	$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
	$entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
	$regional_folder = $usuario->getRegional()->getDirectorioName();
	$entidad_text = $entidad_folder.'/'.$regional_folder;
	$directorio_entidad = $dirRaiz . $entidad_text;
	$directorio_tmp = $dirRaiz.$entidad_folder.'/'.$dirTmp.'/';
	$directorio_final = $directorio_entidad.'/'.$dir_object.'/';
	$dirextorio_alias = $alias_object.$entidad_text.'/'.$dir_object.'/';    
	//********************************************************************************    
	if($is_files_old)
	{	   
		$files_adjuntos = preg_split("/[,]+/",$files_old_text, -1, PREG_SPLIT_NO_EMPTY);
		$files_text = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
		for($j=0; $j < count($files_text); $j++){
			$url = $this->strpos_array($files_text[$j], $files_adjuntos);
			if(!is_null($url)){
				$url_files .= $url . ",";
			}else{
				$file_name = basename($files_text[$j]);
				$filenamesource = $directorio_tmp . $file_name;
				$filenametarget = $directorio_final . $file_name;
				if(file_exists($filenamesource)){
					$directorio_final = simad_util::createPath($directorio_final);
					copy($filenamesource, $filenametarget);
					unlink($filenamesource);
					$url_files .= $dirextorio_alias . $file_name . ",";
				}
			}
		}
	}
	else
	{
		$files_adjuntos = preg_split("/[,]+/",$files_new, null, PREG_SPLIT_NO_EMPTY);
		for($j=0; $j <= count($files_adjuntos); $j++){
			if(trim($files_adjuntos[$j])){
				$file_name = basename($files_adjuntos[$j]);
				$filenamesource = $directorio_tmp . basename($files_adjuntos[$j]);
				$filenametarget = $directorio_final . basename($files_adjuntos[$j]);                
				if(file_exists($filenamesource)){
					$directorio_final = simad_util::createPath($directorio_final);
					copy($filenamesource, $filenametarget);
					unlink($filenamesource);
					$url_files .= $dirextorio_alias . $file_name . ",";
				}
			}
		}
	}
	return $url_files;
  }
  
  public function getJoinModulo(Criteria $criteria,$modulo_id,$codigo_principal)
  {    
    $class_peer = "";    
    switch($modulo_id)
       {
        case 1:
            //MODULO SEGURIDAD NO TIENE NOVEDADES
        break;
        case 2:             
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ComInternaPeer::COMINTERNA_ID);
             $criteria->add(ComInternaPeer::RADICADO,$codigo_principal);             
        break;
        case 3:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ComRecibidaPeer::COMRECIBIDA_ID);
             $criteria->add(ComRecibidaPeer::RADICADO,$codigo_principal);                          
        break;
        case 4:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ComEnviadaPeer::COMENVIADA_ID);
             $criteria->add(ComEnviadaPeer::RADICADO,$codigo_principal);                          
        break;
        case 5:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
             $criteria->add(UnidadDocumentalPeer::CODIGO_BARRAS,$codigo_principal);                          
        break;
        case 6:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,PqrPeer::PQR_ID);
             $criteria->add(PqrPeer::PQR_ID,$codigo_principal);             
        break;
        case 7:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ServicioPeer::SERVICIO_ID);
             $criteria->add(ServicioPeer::RADICADO,$codigo_principal);             
        break;
        case 8:
             $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ProcedimientoPeer::PROCEDIMIENTO_ID);
             $criteria->add(ProcedimientoPeer::CODIGO,$codigo_principal);
        break;
        case 9:
             //NO TIENE CODIGO NO RADICADO EL COMUN
        break;
        case 10:
            $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ClientePeer::CLIENTE_ID);
            $criteria->add(ClientePeer::CODIGO_CLIENTE,$codigo_principal);            
        break;
        case 11:
             //panel de contro no tiene codigo
        break;
        case 12:
            $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,DocumentacionPeer::DOCUMENTACION_ID);
            $criteria->add(DocumentacionPeer::CODIGO_BARRAS,$codigo_principal);            
        break;
        case 13:
            $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,FacturaPeer::FACTURA_ID);
            $criteria->add(FacturaPeer::CODIGO_BARRAS,$codigo_principal);            
        break;
        case 14:
            $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,ProveedorPeer::PROVEEDOR_ID);
            $criteria->add(ProveedorPeer::NIT,$codigo_principal);            
        break;
        case 15:
            //NOVEDADES NO SE CONSULTA
        break;
        case 16:
            $criteria->addJoin(NovedadesPeer::CODIGO_PRINCIPAL,PrestamoPeer::PRESTAMO_ID);
            $criteria->add(PrestamoPeer::NUMERO_RADICACION,$codigo_principal);            
        break;
        default:
            $class_peer = "";
        break;    
       }
       
       return $criteria;
  }
}
