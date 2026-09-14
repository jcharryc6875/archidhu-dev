<?php

/**
 * com_recibida actions.
 *
 * @package    simad
 * @subpackage com_recibida
 * @author     Ing. Javier Fernando Charry
 * @version    SVN: $Id: actions.class.php 3335 2007-01-23 16:19:56Z fabien $
 */
include_once(sfConfig::get('sf_lib_dir'). DIRECTORY_SEPARATOR . "Fechas.class.php");
require_once(sfConfig::get('sf_lib_dir'). DIRECTORY_SEPARATOR . "exec_class.php");
use ZipStream\ZipStream;
use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;
use Laminas\ZendFrameworkBridge\Module;

class com_recibidaActions extends sfActions
{
  public function preExecute()
  {    
    $isAuthenticated = $this->getUser()->isAuthenticated();
    $base_path = sfConfig::get('base_simad');
    if(!$isAuthenticated){
      $this->redirect($base_path."/backend.php/security/login");
    }
  }
  
  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
  		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
  	}	 
  }
  
  public function tienePrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $isValid = true;
  	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
  		$isValid = false;
  	}
    return $isValid;
  }

  public function verificaPrilegioCerrar($currentForm)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }	 	  
  }
  
  public function executeMailAccionLegal()
  {
    /******************************************************************************************************/
  	$fecha = AddDays(date("Y-m-d"),1);  	  	  	
  	/******************************************************************************************************/	
  	$c  = new Criteria();
  	//$c->setDistinct();	
  	$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,1);
  	$c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, date("Y-m-d") , Criteria::GREATER_EQUAL);
  	$c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, $fecha , Criteria::LESS_EQUAL);
  	$c->add(ComRecibidaPeer::COMENVIADA_ID,NULL,Criteria::ISNULL);
  	$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
  	$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,true);
  	$c->addAscendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID);
    $c->clearSelectColumns();
    /****************************************JOINSS*********************************************************/
    $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    $c->addJoin(ComRecibidaPeer::TIPOCOMRECIBIDA_ID,TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);
    $c->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
    $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
    $c->addJoin(ComRecibidaPeer::ASUNTORECIBIDA_ID,AsuntoRecibidaPeer::ASUNTORECIBIDA_ID);
    /***************************************SELECT COLUMNAS*************************************************/
    $c->addSelectColumn(ComRecibidaPeer::RADICADO);//0
    $c->addSelectColumn(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA);//1
    $c->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);//2
    $c->addSelectColumn(DirectorioExternoPeer::NOMBRE);//3
    $c->addSelectColumn(AsuntoRecibidaPeer::DESCRIPCION);//4
    $c->addSelectColumn(ComRecibidaPeer::ASUNTO);//5
    $c->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);//6
    $c->addSelectColumn(ComRecibidaPeer::OBSERVACIONES);//7
    $c->addSelectColumn(UsuarioPeer::EMAIL);//8
  	$resultset = ComrecibidaUsuarioPeer::doSelectStmt($c);    
  	/*******************************************************************************************************/    
  	$cuerpo_emails  = array();
  	$emails_destinos = array();	
  	while($object = $resultset->fetch()){
  	    $email_destino = $object[8];		
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">        
        Este es un mensaje para informarle que tiene una Comunicacion Externa Recibida con Accion Legal que se encuentra proxima a vencer su numero de radicado es: 
        <br/>        
        Numero De Radicado: <strong>'.$object[0].'</strong><br/>
        Fecha Vencimiento Legal: <strong>'.$object[1].'</strong><br/>
        Fecha Radicacion: <strong>'.$object[2].'</strong><br/>    
        Remitente: <strong>'.utf8_encode($object[3]).'</strong><br/>
        Asunto: <strong>'.($object[4]).'</strong><br/>
        Detalle Del Asunto: <strong>'.($object[5]).'</strong><br/>
        Tipo De Comunicaci&oacute;n: <strong>'.($object[6]).'</strong><br/>
        Observaciones: <strong>'.($object[7]).'</strong><br/>
        <br/>
        </div>
        </body></html>';
        $cabeceras = "Content-Type: text/html; charset=UTF-8\r\n";
        //*********************************************************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('CAD : Comunicacion Recibida Vencida');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($email_destino, $email_destino);
        //*********************************************************************************************************************
        if($baseMail->InitSend() === true)
        {
          $baseMail->writetolog("Alerta enviada: " .$object[0] . " Enviado a: " . $email_destino);
        }else{
          $baseMail->writetolog("Error al enviar alerta: " . $object[0] . " Cuenta correo: " . $email_destino);
        }
        //*********************************************************************************************************************
        $cuerpo_emails[]  = $cuerpo;
        $emails_destinos[] = $email_destino;
  	}	
  	$this->datos = $cuerpo_emails;
  	$this->cuenta = $emails_destinos;
    //********************************************************CERRAR NAVEGADOR*************************************************
    $work_dir = sfConfig::get("sf_lib_dir");
    $batch_file = "close_browser.bat";    
    //system("D:\archivo.bat");
    $last_line = exec($work_dir."/".$batch_file);//system($mycmd, $retval);
  }
		
  public function executeDownload()
  {    
    
    $strFileName=$this->getRequestParameter('strFileName');
    $strFileType=$this->getRequestParameter('strFileType');
    $fileContent=$this->getRequestParameter('fileContent');    
    //$strFileType = strrev(substr(strrev($strFileName),0,4));
    //$fileContent = imap_fetchbody($imap,$msgno,$valor+2);
    $this->downloadFile($strFileType,$strFileName,$fileContent);
   
  }
	
  public function executeLoadAreaByTramite()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $dependencia_id = $this->getRequestParameter('dependencia_id') ? trim($this->getRequestParameter('dependencia_id')) : 0;
    //******************************************************************************************
    $response_data = TipoComRecibidaPeer::getTipoComByAreaJson($dependencia_id);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($response_data);
  }

  /**
   * com_recibidaActions::executeLoadListBatchMig()
   * accion para listar el resultado de los documentos radicados
   * @return
   */
  public function executeLoadListBatchMig()
  {
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $comIdLote = trim($this->getRequestParameter('comIdLote')) ?: null;
    //*******************************************************************************************
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $status = 400;
        $message = "Esta funcionalidad no esta permitida";
        //***************************************************************************************
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => $status, 'message' => $message);
        return $this->renderText(json_encode($response_info));
    }
    //*******************************************************************************************
    $this->blotes = ComMigmasivoPeer::getListComByComLote($comIdLote);
    $this->comIdLote = $comIdLote;
  }

  /**
   * com_recibidaActions::executeExportarBatchList()
   * accion para exportar los resultados de la radicacion masiva filtrado por un comIdLote
   * @return
   */
  public function executeExportarBatchList()
  {
    $this->setLayout(false);
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $comIdLote = trim($this->getRequestParameter('idComBatch')) ?: null;
    //*******************************************************************************************
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $status = 400;
        $message = "Esta funcionalidad no esta permitida";
        //***************************************************************************************
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => $status, 'message' => $message);
        return $this->renderText(json_encode($response_info));
    }
    //*******************************************************************************************
    $this->blotes = ComMigmasivoPeer::getListComByComLote($comIdLote);
    $this->comIdLote = $comIdLote;
  }

  /**
  * com_recibidaActions::executeUpdateComBatch()
  * Radicar comunicaciones externas recibidas masivas con archivo plano
  * @return
  */
  public function executeUpdateComBatch()
  {
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
		$usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    //*******************************************************************************************
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $status = 400;
        $message = "Esta funcionalidad no esta permitida";
        //***************************************************************************************
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => $status, 'message' => $message, 'comIdLote' => null);
        return $this->renderText(json_encode($response_info));
    }
    //*******************************************************************************************
    $parameters['fileuploadtmp'] = trim($this->getRequestParameter('fileuploadtmp')) ? trim($this->getRequestParameter('fileuploadtmp')) : null;
    $parameters['filedocsupload'] = trim($this->getRequestParameter('filedocsupload')) ? trim($this->getRequestParameter('filedocsupload')) : null;
    $parameters['dataufile'] = trim($this->getRequestParameter('dataufile')) ? trim($this->getRequestParameter('dataufile')) : null;
    $parameters['comIdLote'] = trim($this->getRequestParameter('comIdLote')) ? trim($this->getRequestParameter('comIdLote')) : null;
    //*******************************************************************************************
    $max_time = ini_get("max_execution_time");
    ini_set('max_execution_time', 900);
    $response_info = ComMigmasivoPeer::addNewComByComLote($parameters,ModulesEnable::ComRecibida);
    ini_set('max_execution_time', $max_time);
    //*******************************************************************************************
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info));
  }

  /**
  * com_recibidaActions::executeFileDocsRadMasiva()
  * Carga archivo de documentos para la radicacion masiva, solo se permiten archivos comprimidos
  * @return
  */
  public function executeFileDocsRadMasiva()
  {
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $status = 400;
        $message = "Esta funcionalidad no esta permitida";
    }
    //*******************************************************************************************
    $dirRaiz    = ParametroPeer::retrieveByPk(27)->getValortexto();
    $dirTmp     = ParametroPeer::retrieveByPk(65)->getValortexto();
    $upload_dir = $dirRaiz.DIRECTORY_SEPARATOR.$dirTmp;
    $directorio = simad_util::createPath($upload_dir);
    //*******************************************************************************************
    $util_simad = new simad_util();
    $file_name = "";
    //*******************************************************************************************
    if ($this->getRequest()->hasFiles() && $this->getRequest()->getFileName('filedocs'))  	  	
    {    	
      $info_file = new SplFileInfo($this->getRequest()->getFileName('filedocs'));
      $tempFile = $_FILES['filedocs']['tmp_name'];
      //***************************************************************************************
      $cleanfilename = $util_simad->clean_name_fileinfo($info_file);//limpiar el nombre del archivo
      $file_name = simad_util::uniquename($upload_dir,sha1($cleanfilename.time()),".zip");//validar uniquename    		
      $inputFileName = $directorio.DIRECTORY_SEPARATOR.$file_name;            
      $this->getRequest()->moveFile('filedocs', $inputFileName);
      //***************************************************************************************
      $mime_trust = array('application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed');
      $file_valid = simad_util::CheckIsValidFormatFile($inputFileName,$mime_trust);
      if(!$file_valid){
        unlink($inputFileName);
        $status = 400;
        $message = "El formato del archivo no esta permitido";
      }else{
        $this->inputFileName = basename($inputFileName);
        $status = 200;
        $message = "El archivo se cargo correctamente";
      }
      //***************************************************************************************
      $file_name = $this->inputFileName;
    }
    else
    {            
      $status = 400;
      $message = "Debe enviar un archivo comprimido con todos los documentos para radicar";
    }
    //*******************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $response_info = array('status' => $status, 'file_name' => $file_name, 'message' => $message);
    return $this->renderText(json_encode($response_info));
  }

  /**
   * com_recibidaActions::executeVerifyComBatchData()
   * accion para verificar la informacion enviada en el excel
   * @return
   */
  public function executeVerifyComBatchData()
  {
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $status = 400;
        $message = "Esta funcionalidad no esta permitida";
        //***************************************************************************************
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => $status, 'message' => $message, 'comIdLote' => null);
        return $this->renderText(json_encode($response_info));
    }
    //*******************************************************************************************
    $sheetData = trim($this->getRequestParameter('sheetData')) ?: 0;
    $simad_util = new simad_util();
    //$list_errors = array();
    //*******************************************************************************************
    $data = $simad_util->getArrayUnSerialize($sheetData);
    $idLote = md5(uniqid().rand(9999,100000).date('YmdGisu'));$list_nrow = array();
    foreach($data as $row)
    {
        $dataRow = array();
        $dataRow['COMLOTE_ID'] = $idLote;
        $dataRow['MODULO_ID'] = ModulesEnable::ComRecibida;
        $dataRow['USUARIO_ID'] = $usuariologuiado;
        $dataRow['PUNTO_RADICACION'] = trim($row[1]) ? trim($row[1]) : null;
        $dataRow['COD_DEPENDENCIA'] = trim($row[2]) ? trim($row[2]) : null;
        $dataRow['NOMBRE_DEPENDENCIA'] = trim($row[3]) ? trim($row[3]) : null;
        $dataRow['ASUNTO_COM'] = trim($row[4]) ? trim($row[4]) : null;
        $dataRow['NUM_FOLIOS'] = trim($row[5]) ? trim($row[5]) : null;
        $dataRow['TIPO_DOCUMENTO'] = trim($row[6]) ? trim($row[6]) : null;
        $dataRow['OBSERVACIONES_COM'] = trim($row[7]) ? trim($row[7]) : null;
        $dataRow['NOMBRE_ARCHIVO'] = trim($row[8]) ? trim($row[8]) : null;
        $dataRow['RADICADO_ORIGEN'] = trim($row[9]) ? trim($row[9]) : null;
        $dataRow['FORMA_RECEPCION'] = trim($row[10]) ? trim($row[10]) : null;
        $dataRow['PRIORIDAD_COM'] = trim($row[11]) ? trim($row[11]) : null;
        $dataRow['NUMERO_FUD'] = trim($row[12]) ? trim($row[12]) : null;
        $dataRow['FECHA_LLEGADA'] = trim($row[13]) ? trim($row[13]) : null;
        $dataRow['PNOMBRE_INTERESADO'] = trim($row[14]) ? trim($row[14]) : null;
        $dataRow['SNOMBRE_INTERESADO'] = trim($row[15]) ? trim($row[15]) : null;
        $dataRow['PAPELLIDO_INTERESADO'] = trim($row[16]) ? trim($row[16]) : null;
        $dataRow['SAPELLIDO_INTERESADO'] = trim($row[17]) ? trim($row[17]) : null;
        $dataRow['TIPODOC_INTERESADO'] = trim($row[18]) ? trim($row[18]) : null;
        $dataRow['NUID_INTERESADO'] = trim($row[19]) ? trim($row[19]) : null;
        $dataRow['CIUDAD_INTERESADO'] = trim($row[20]) ? trim($row[20]) : null;
        $dataRow['EMAIL_INTERESADO'] = trim($row[21]) ? trim($row[21]) : null;
        $dataRow['NIT_DESTINATARIO'] = trim($row[22]) ? trim($row[22]) : null;
        $dataRow['RAZON_SOCIAL'] = trim($row[23]) ? trim($row[23]) : null;
        $dataRow['CIUDAD_REMITENTE'] = trim($row[24]) ? trim($row[24]) : null;
        $dataRow['DIRECCION_REMITENTE'] = trim($row[25]) ? trim($row[25]) : null;
        $dataRow['TIPODOC_REMITENTE'] = trim($row[26]) ? trim($row[26]) : null;
        $dataRow['EMAIL_REMITENTE'] = trim($row[27]) ? trim($row[27]) : null;
        $dataRow['ESTADO_MIGRACION'] = "PENDIENTE VALIDAR";
        $list_nrow[] = ComMigmasivoPeer::addNewRow($dataRow);
    }
    //*******************************************************************************************
    $elist_msg = ComMigmasivoPeer::getIsValidByIdBatch($idLote,ModulesEnable::ComRecibida);
    //*******************************************************************************************
    if(count($elist_msg)){ ComMigmasivoPeer::deleteBatchNotValid($idLote); }
    //*******************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $response_info = array('status' => (count($elist_msg) ? 400 : 200), 'message' => $elist_msg, 'comIdLote' => $idLote);
    return $this->renderText(json_encode($response_info));
  }

  /**
   * com_recibidaActions::executeReadFileExcel()
   * accion para funcionalidad leer datos del archivo de excel
   * @return
   */
  public function executeReadFileRadMasiva()
  {
    $currentForm="COM_RECIBIDA_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $this->cod_msg = 1;
        $this->msg_error = "Esta funcionalidad no esta permitida";
    }
    //*******************************************************************************************
    if ($this->getRequest()->hasFiles() && $this->getRequest()->getFileName('file')){ 
      try {
        $file_vars = pathinfo($this->getRequest()->getFileName('file'));
        //*****************************************************************************************
        $dirRaiz    = ParametroPeer::retrieveByPk(27)->getValortexto();
        $dirTmp     = ParametroPeer::retrieveByPk(65)->getValortexto();
        $directorio_tmp = simad_util::createPath($dirRaiz.DIRECTORY_SEPARATOR.$dirTmp);
        //*****************************************************************************************
        $util_simad = new simad_util();
        $extension_file = $file_vars['extension'];
        //*****************************************************************************************
        $info_file = new SplFileInfo($this->getRequest()->getFileName('file'));
        $cleanfilename = $util_simad->clean_name_fileinfo($info_file);//limpiar el nombre del archivo
        $file_name = simad_util::uniquename($directorio_tmp,sha1($cleanfilename.time()),".".$extension_file);//validar uniquename    		
        $inputFileName = $directorio_tmp.DIRECTORY_SEPARATOR.$file_name;            
        $this->getRequest()->moveFile('file', $inputFileName);
        //*****************************************************************************************
        $listVars = ComRecibidaPeer::readFileComCombined($inputFileName);
      } catch (PropelException $th) {
        $inputFileName = null;
        $listVars = array();
      } catch (\Exception $th) {
        //throw $th;
      } catch (\Throwable $th) {
        //throw $th;
      }
      //*****************************************************************************************
      $this->inputFileName = basename($inputFileName);
      $this->headerList = isset($listVars['headerList']) ? $listVars['headerList'] : null;
      $this->sheetData = isset($listVars['sheetData']) ? $listVars['sheetData'] : null;
      $this->sheetDataSerialize = isset($listVars['sheetDataSerialize']) ? $listVars['sheetDataSerialize'] : null;
      $this->cod_msg = isset($listVars['cod_msg']) ? $listVars['cod_msg'] : null;
      $this->msg_error = isset($listVars['msg_error']) ? $listVars['msg_error'] : null;
    }
    else
    {
        $this->cod_msg = 1;
        $this->msg_error = "Debe seleccionar el archivo que contiene las registros para radicar";
    }
  }

  /**
   * com_recibidaActions::executeRadicarMasivas()
   * Permite iniciar el formulario para radicar comunicaciones externas recibidas masivas
   * desde un archivo de excel     
   * @return
   */
  public function executeRadicarMasivas()
  {
      $currentForm = "COM_RECIBIDA_RADICACION_MASIVA";
      $this->verificaPrilegioCerrar($currentForm);
      $this->process_end = $this->getRequestParameter('process_end') ? $this->getRequestParameter('process_end') : 0;
      $this->sheetData = array();
      $this->cod_msg = $this->getRequestParameter('cod_msg') ? $this->getRequestParameter('cod_msg') : 0;
      $this->msg_error = $this->getRequestParameter('msg_error') ? $this->getRequestParameter('msg_error') : "";
      $this->com_recibda = new ComRecibida();
  }

  public function executeIndex()
  {
    $weblog_id = 15100;
    $ws_log = WebserviceLogPeer::retrieveByPK($weblog_id);
    $xml_fname = $ws_log->getPrimaryKey() . '_wsinfo.xml';
    file_put_contents(sfConfig::get('sf_log_dir') . '/' . $xml_fname, $ws_log->getRequestInfo());
    

    /*$comrecibida_id = 358905;
    $interesado_id = 743439;
    $com_recibida = ComRecibidaPeer::retrieveByPK($comrecibida_id);
    $com_recibida->addExpedienteAutoByReglas();*/
    //*********************************************************************************************************
    /*$com_recibida = ComRecibidaPeer::retrieveByPK($comrecibida_id);
    $interesado_obj = InteresadosPeer::retrieveByPK($interesado_id);
    $response_mail = $interesado_obj->envioEmailNotificacion($com_recibida->getRadicado());*/
    //*********************************************************************************************************
    
    $this->forward('com_recibida','consulta');    
  }
  
  public function executeEnviarComToWsLex()
  {
    $currentForm="COM_RECIBIDA_RESPONDER_EXTERNO";
	if(!$this->tienePrilegio($currentForm)){
      $response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
      $array = json_encode($response_data);
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText($array);
	}
	//************************************************************************************************
    $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
    $com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);
    $response_data = $com_recibida->enviarRespuestaExterna();
    //*******************************************************************************************
    $array = json_encode($response_data);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }
  
  public function executeEnviarComToWsLex3()
  {
    try{
	  $currentForm="COM_RECIBIDA_RESPONDER_EXTERNO";
	  if(!$this->tienePrilegio($currentForm)){
		$response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
		$array = json_encode($response_data);
		$this->getResponse()->setContentType('application/json');      
		return $this->renderText($array);
	  }
	  //************************************************************************************************
      $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
      $status = 400;
      //************************************************************************************************
      if($comrecibida_id){
        $simadSoap = new WsSimadUariv();
        $response_data = $simadSoap->loadWsInfoRadicadoEntrada($comrecibida_id);
      }else{
        return "";
        $response_data = array( 'status' => $status, 'message' => 'Error la informaci&oacute; no es valida');
      }
    }catch(Exception $ex){
      $response_data = array( 'status' => 400, 'message' => $ex->getMessage());
    }
    //**************************************************************************************************
    $array = json_encode($response_data);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }
  
  public function executeEnviarComToWsLex2()
  {
    try{
	  $currentForm="COM_RECIBIDA_RESPONDER_EXTERNO";
	  if(!$this->tienePrilegio($currentForm)){
		$response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
		$array = json_encode($response_data);
		$this->getResponse()->setContentType('application/json');      
		return $this->renderText($array);
	  }
	  //************************************************************************************************
      $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
      $status = 400;
      //************************************************************************************************
      if($comrecibida_id){
        $simadSoap = new WsSimadUariv();
        $response_data = $simadSoap->loadWsInfoRadicadoEntrada($comrecibida_id,false);
      }else{
        //return "";
        $response_data = array( 'status' => $status, 'message' => 'Error la informaci&oacute; no es valida');
      }
    }catch(Exception $ex){
      $response_data = array( 'status' => 400, 'message' => $ex->getMessage());
    }
    //**************************************************************************************************
    $array = json_encode($response_data);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }
  
  public function executeGuia()
    {
    	//$this->getUser()->setCulture('es_ES');
    	$c = new Criteria();
        $this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
        $this->mensajeria = EmpresaMensajeriaPeer::doSelect($c);
        //$this->mensajeria = new EmpresaMensajeria();
        $this->forward404Unless($this->com_recibida);
    }
  
  public function executeExcelWorkflow()
  {   	
	$this->setLayout(false);    
    $this->parametros="a=1";
    $this->com_recibidas = "";    
    if($this->getRequestParameter('porWorkflow')=="1"){
       $c=new Criteria();
   	   $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
       $c->addJoin(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,WfInstanciaPeer::WFINSTANCIA_ID);
       $c->addJoin(WfInstanciaPeer::WFINSTANCIA_ID,ComRecibidaPeer::INSTANCIA);
       $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
       $c->addJoin(WfInstanciaPeer::WFINSTANCIA_ID,WfInstanciaBitacoraPeer::WFINSTANCIA_ID);
       $c->addJoin(WfInstanciaBitacoraPeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);
       $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
       $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuariologuiado);       
       $c->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
       $this->object = WfInstanciaBitacoraPeer::doSelect($c);
               
    }
  }
  
  public function executeReporte()
  {
    $this->usuario = new Usuario();
  }
  
  public function executeUpdateGuia()
  {        
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));   
	  $com_recibida_anterior = clone $com_recibida;
    $com_recibida->setGuia($this->getRequestParameter('numGuia'));
    $com_recibida->setFechaEnvioGuia($this->getRequestParameter('fecha_envio_guia'));
    $com_recibida->setValorGuia($this->getRequestParameter('valor_guia'));
    $com_recibida->setEmpresaMensajeriaId($this->getRequestParameter('empresa_mensajeria_id'));
    $com_recibida->save();
    //*************************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$this->getRequestParameter('comrecibida_id'));
  }

  public function guardarAuditoria($anterior,$nueva)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*************************************************************************************************
	AuditLogPeer::guardarAuditoriaLite("ComRecibida",$anterior,$nueva,ModulesEnable::ComRecibida,$nueva->getRadicado(),$usuariologuiado);
  }
  
  public function executeList()
  {  	  	
  	$currentForm = "com_recibida/list";
    /***************************************************************************************************/
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /***************************************************************************************************/
  	$this->usuariologuiado = $usuariologuiado;
  	$this->verificaPrilegio($currentForm);	
    $this->parametros = "&a=1";
    $this->papelera = false;    
    $c = new Criteria();
    $c = $this->getCriteriaBasic($c);
    $c->setDistinct();
    /**************************************************************************************************/
    $pager = new sfPropelPager('ComRecibida', 10);
    $pager->setCriteria($c);
    $pager->setPage($this->getRequestParameter('page',1));
    $pager->init();
    $this->pager = $pager;
    //*************************************************************************************************/
    $this->mensajeListaVacia = ConsultaPermisoHelper::MSG_SIN_REGISTROS;
    if ($pager->getNbResults() == 0 && trim($this->getRequestParameter('radicado'))) {
      $countSinPermiso = ComRecibidaPeer::doCount((new Criteria())->add(ComRecibidaPeer::RADICADO, '%'.trim($this->getRequestParameter('radicado')).'%', Criteria::LIKE));
      $this->mensajeListaVacia = ConsultaPermisoHelper::mensajeListaVacia($countSinPermiso);
    }
    //*************************************************************************************************/
    $this->directorio_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
    $this->directorio_alias  = ParametroPeer::retrieveByPk(28)->getValortexto();
    $this->format_digit_img = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());	
    $this->directorio_adj  = ParametroPeer::retrieveByPk(14)->getValortexto();
  }
  
  public function marcar_comunicacion($comrecibidaId,$marca){
     $conexion = Propel::getConnection();
	 $set = "UPDATE %s  SET  %s =".$marca." WHERE %s=".$comrecibidaId;			       
     $sql = sprintf($set, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA, ComRecibidaPeer::COMRECIBIDA_ID);			  		  		   
     $sentencia = $conexion->prepare($sql);
     $sentencia->execute();
  }
  
  public function executeMarcados()
  {       	
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$parametros = '';	 
	$c = new Criteria();  	
	$c->setDistinct();
  $periodo_id = date("Y");
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('radicado')) {
	    $c->add(ComRecibidaPeer::RADICADO, '%' . $this->getRequestParameter('radicado') .'%', Criteria::LIKE);
	    $this->parametros .= "&radicado=" . $this->getRequestParameter('radicado');
	}
	/*********************************************************************************************************************************/    
	if ($this->getRequestParameter('entidad_origen')) {	    		
	    $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
	    $c->add(DirectorioExternoPeer::NOMBRE,$this->getRequestParameter('entidad_origen').'%',Criteria::LIKE);
	    $parametros .= "&entidad_origen=" . $this->getRequestParameter('entidad_origen');
	}
	/*********************************************************************************************************************************/
    if ($this->getRequestParameter('funcionario_origen')) {		
		$c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
		$c->add(DirectorioExternoPeer::FUNCIONARIO,'%'.$this->getRequestParameter('funcionario_origen').'%',Criteria::LIKE);
	    $parametros .= "&funcionario_origen=" . $this->getRequestParameter('funcionario_origen');
	}
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('asuntorecibida_id')) {
	    $c->add(ComRecibidaPeer::ASUNTORECIBIDA_ID,$this->getRequestParameter('asuntorecibida_id'));
	    $parametros .= "&asuntorecibida_id=" . $this->getRequestParameter('asuntorecibida_id');
	}
	/*********************************************************************************************************************************/	 
	if ($this->getRequestParameter('usuario_destino')) {		
		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_destino'));		
	    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
	    $parametros .= "&usuario_destino=" . $this->getRequestParameter('usuario_destino');     
	}	
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('usuario_copia')) {		
		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));		
	    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
	    $parametros .= "&usuario_copia=" . $this->getRequestParameter('usuario_copia');     
	}	
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('radicador')) {		
		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));		
	    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
	    $parametros .= "&radicador=" . $this->getRequestParameter('radicador');     
	}
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('fechaInicial')) {
		if ($this->getRequestParameter('fechaFinal')) {
	        $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_EQUAL);
	        $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_EQUAL);
	        $parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
	     }else{
		    $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_EQUAL);
		    $parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
	     }
	 }	
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('fechaCierre')) {
		if ($this->getRequestParameter('fechaCierreFin')) {
	        $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_THAN);
	        $c->addAnd(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierreFin').' 23:59:59',Criteria::LESS_EQUAL);
	        $parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
	    }else{
		    $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_EQUAL);
		    $parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
	    } 
	}
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('regional_id')) {		
	    $c->add(ComRecibidaPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));
	    $parametros .= "&regional_id=" . $this->getRequestParameter('regional_id');     
	}
	/*********************************************************************************************************************************/	
  if ($this->getRequestParameter('dependencia_id')) {    
      $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
      $parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id');     
  }
  /*********************************************************************************************************************************/ 
	if ($this->getRequestParameter('estado_com_recibida_id')) {		
	    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('estado_com_recibida_id'));
	    $parametros .= "&estado_com_recibida_id=" . $this->getRequestParameter('estado_com_recibida_id');     
	}
	/*********************************************************************************************************************************/	
	if ($this->getRequestParameter('dependencia_id')) {		
	    $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
	    $parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id');     
	}
	/*********************************************************************************************************************************/ 		
	if ($this->getRequestParameter('tipo_com_recibida_id')) {		
	    $c->add(ComRecibidaPeer::TIPOCOMRECIBIDA_ID,$this->getRequestParameter('tipo_com_recibida_id'));
	    $parametros .= "&tipo_com_recibida_id=" . $this->getRequestParameter('tipo_com_recibida_id');     
	}
	/*********************************************************************************************************************************/ 
	if ($this->getRequestParameter('radicado_origen')) {
      $c->add(ComRecibidaPeer::RADICADO_ORIGEN, $this->getRequestParameter('radicado_origen'));
      $parametros .= "&radicado_origen=" . $this->getRequestParameter('radicado_origen');
    }
	/*********************************************************************************************************************************/ 
	if ($this->getRequestParameter('periodo_id')) {
      $periodo_id = trim($this->getRequestParameter('periodo_id'));
	    $c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
	    $parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');     
	}
	/*********************************************************************************************************************************/ 
	if ($this->getRequestParameter('fechaVence')) {		
	    $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getRequestParameter('fechaVence'));
	    $this->parametros .= "&fechaVence=" . str_replace("/","-",$this->getRequestParameter('fechaVence'));     
	}    
	/*********************************************************************************************************************************/ 	
	if ($this->getRequestParameter('detalles')) {		
    $c->add(ComRecibidaPeer::OBS_REEN_RESP,$this->getRequestParameter('detalles'),Criteria::LIKE);
    $parametros .= "&detalles=" . $this->getRequestParameter('detalles');
	}
	//*********************************************************************************************************************************/
	if ($this->getRequestParameter('entradas')) {				      
    $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
    $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,14,Criteria::NOT_EQUAL);     
    $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
    $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);	           		  		
    $parametros .= "&entradas=" . $this->getRequestParameter('entradas');
	}
	//*********************************************************************************************************************************/
	if ($this->getRequestParameter('leer')) {
    $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	           
    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('leer'));
    $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);		   
    $parametros .= "&leer=" . $this->getRequestParameter('leer');
	}
	//*********************************************************************************************************************************/
	if ($this->getRequestParameter('vencidas')) {
    $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   			   
    $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d"),Criteria::LESS_THAN);
    $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
    $parametros .= "&vencidas=" . str_replace("/","-",$this->getRequestParameter('vencidas'));
	}	
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('porVencer')) {
		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
	    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
	    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
		$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	
		$c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getFechaVence(),Criteria::LESS_EQUAL);		   
		$c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d"),Criteria::GREATER_EQUAL);	
    $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);    
	    $parametros .= "&porVencer=" . str_replace("/","-",$this->getRequestParameter('porVencer'));     
	}
	/*********************************************************************************************************************************/
	if ($this->getRequestParameter('entregado')) {		   
	    $c->add(ComRecibidaPeer::ESTAENTREGADO,$this->getRequestParameter('entregado'));
	    $this->parametros .= "&entregado=" . $this->getRequestParameter('entregado');
	}
	//**************************************************************************************************/
	if ($this->getRequestParameter('marcada')) {				   
	    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
	    $parametros .= "&marcada=" . $this->getRequestParameter('marcada');
	}
	//**************************************************************************************************/
	if ($this->getRequestParameter('papelera')) {		   
	    $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('papelera'));
	    $c->addOr(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12);
	    $parametros .= "&papelera=" . $this->getRequestParameter('papelera');		   
	}
	//**************************************************************************************************/
	if($this->getRequestParameter('ordenar')){
	   $c->addDescendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID,$this->getRequestParameter('ordenar'));	
	}else{	
	   $c->addDescendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID);
	}  
	//**************************************************************************************************/
	if($this->getUser()->checkPerm('COM_RECIBIDA_LISTAR_TODAS', $usuariologuiado)){			   
	   //$this->user = $this->getComAsignadas(1 , $c);
	//**************************************************************************************************/  
	}else{
	//**************************************************************************************************/
	if(!$this->getRequestParameter('usuario_copia') && !$this->getRequestParameter('usuario_destino')&& !$this->getRequestParameter('radicador') && 	!$this->getRequestParameter('entradas') && !$this->getRequestParameter('leer') && !$this->getRequestParameter('porVencer') && 
	!$this->getRequestParameter('vencidas')){
    	   $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    	   $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
    	   //$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);	
    	   $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    	   $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
    	   $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);	   
	   }else{	   	
		  $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);		  
	   }
	//**************************************************************************************************/		
	}
	//**************************************************************************************************/
	$pager = new sfPropelPager('ComRecibida', 10);
	$pager->setCriteria($c);        
	$pager->setPage($this->getRequestParameter('page'));
	$pager->init();
	$this->pager = $pager;		    
	//**************************************************************************************************/
	$temp = 10;
	$cadSet = '';		
	for($i = 1; $i <= $temp; $i++){
		if($this->getRequestParameter('marca'.$i)? '1':'0'){
			$recibida = ComRecibidaPeer::retrieveByPK($this->getRequestParameter('idRec'.$i));
			$recibida->setMarca($usuariologuiado);
			$recibida->save();				
		}else{
			$com_recibida = ComRecibidaPeer::retrieveByPK($this->getRequestParameter('idRec'.$i));
			$com_recibida->setMarca(0);
			$com_recibida->save();
		}
	}		   		   
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/list?'.$parametros);  		          	   
 }
    
  
  public function executeDesmarcar()
  {
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$conexion = Propel::getConnection();
	$set = "UPDATE %s  SET  %s =0 WHERE %s=".$usuariologuiado;			       
    $sql = sprintf($set, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA, ComRecibidaPeer::MARCA);			  		  		   
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
  } 
  
  public function executeBorrar()
  {		
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber'); 	        				
	$c = new Criteria();
	$c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
	$marca  = ComRecibidaPeer::doSelect($c);
	//Se anular solo las Comunicaciones Que se encuentran Marcadas							
	foreach ($marca as $com_recibida) {
       //if($com_recibida->getEstadocomrecibidaId() != 1){
	  	//$com_recibida = ComRecibidaPeer::retrieveByPK($res->getComrecibidaId());
		$com_recibida->setEstadocomrecibidaId(12);
		$com_recibida->save();
					      
    	$c = new Criteria();
        $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$com_recibida->getPrimaryKey());
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
        $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $resp = ComrecibidaUsuarioPeer::doSelect($c);
    	foreach($resp as $result){
    		if($result->getEstadocomrecibidaId() != 1){
				$result->setEstadocomrecibidaId(12);
				$result->save();
			}//end if
		//}	
		}//end foreach
		/*******************************************************************************************************/  		
	}//end foreach
  }
  
  public function actualizaEstados($com_recibida,$estado,$opcion)
  {  	
	/*******************************************************************************************************/
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$com_recibida);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
    if($opcion)
       $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);//para cambiar estado solo para usuario logueado
    $resp = ComrecibidaUsuarioPeer::doSelect($c);
    foreach($resp as $result){
    	if($estado == 12 && $result->getEstadocomrecibidaId() != 1){
			$result->setEstadocomrecibidaId($estado);
			$result->save();
		}elseif($estado != 12){
			$result->setEstadocomrecibidaId($estado);
			$result->save();
		}
	}//end foreach
	/*******************************************************************************************************/
  }

  public function executeRestaurar()
  {		
		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber'); 	        				
		$c = new Criteria();
		$c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
		$marca  = ComRecibidaPeer::doSelect($c);		
		$conexion = Propel::getConnection();				
		foreach ($marca as $res) 
        {
		  if($res->getEstadocomrecibidaId() != "1")
          {	
			$set = "UPDATE %s  SET  %s =2 WHERE COMRECIBIDA_ID=".$res->getComrecibidaId();
		   	$sql = sprintf($set, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::ESTADOCOMRECIBIDA_ID);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            //*****************************************************************************************		  
    		$c = new Criteria();
            $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$res->getPrimaryKey());
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
            $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);            
            $resp = ComrecibidaUsuarioPeer::doSelect($c);
            //*****************************************************************************************
        	foreach($resp as $result){
        		if($result->getEstadocomrecibidaId() != 1){
    				$result->setEstadocomrecibidaId(2);
    				$result->save();
    			}//end if
    		}//end foreach
		  }		  
		}
  }
  
  public function executeMarcar()
  {
    $marcar = 0;
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $com_recibida = ComRecibidaPeer::retrieveByPK($this->getRequestParameter('comrecibida_id'));
    if($com_recibida->getMarca() == 0 || $com_recibida->getMarca() != $usuariologuiado)
    {
        $marcar = $usuariologuiado;
    }
    $com_recibida->setMarca($marcar);
    $com_recibida->save();        
  }
  
  public function executeMarcarTodos()
  {		
	$currentForm="com_recibida/list";
    /***************************************************************************************************/
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    /***************************************************************************************************/
  	$this->usuariologuiado = $usuariologuiado;
  	$this->verificaPrilegio($currentForm);	
	$this->parametros = "&a=1";
	$this->papelera = false;    
	$c = new Criteria();  	
	//$c->setDistinct();
	$c = $this->getCriteriaBasic($c);
    /**************************************************************************************************/
    $c->clearSelectColumns();
    $c->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);
	//*************************************************************************************************/	
	$resultset =  ComRecibidaPeer::doSelectStmt($c);
	$conexion = Propel::getConnection();
	while($object = $resultset->fetch()){	  	
		$set = "UPDATE %s  SET  %s =".$usuariologuiado." WHERE COMRECIBIDA_ID=".$object[0];	       
	    $query = sprintf($set, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA);
        $stmt = $conexion->prepare($query);
	    $res = $stmt->execute();
	}
  }
   
  public function executeEntregados()
  {   
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');		 	        
    $c = new Criteria();
    $parametros = '';
    $c->setDistinct();
    $periodo_id = date("Y");
    /**********************************************************************************************************/
    if ($this->getRequestParameter('radicado')) {
        $c->add(ComRecibidaPeer::RADICADO, '%' . $this->getRequestParameter('radicado') .'%', Criteria::LIKE);
        $parametros .= "&radicado=" . $this->getRequestParameter('radicado');
    }
    /*********************************************************************************************************/    
    if ($this->getRequestParameter('entidad_origen')) {	    		
        $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
        $c->add(DirectorioExternoPeer::NOMBRE,$this->getRequestParameter('entidad_origen').'%',Criteria::LIKE);
        $parametros .= "&entidad_origen=" . $this->getRequestParameter('entidad_origen');
    }
    /*********************************************************************************************************/
      if ($this->getRequestParameter('funcionario_origen')) {		
      $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
      $c->add(DirectorioExternoPeer::FUNCIONARIO,'%'.$this->getRequestParameter('funcionario_origen').'%',Criteria::LIKE);
        $parametros .= "&funcionario_origen=" . $this->getRequestParameter('funcionario_origen');
    }
    /********************************************************************************************************/
    if ($this->getRequestParameter('asuntorecibida_id')) {
        $c->add(ComRecibidaPeer::ASUNTORECIBIDA_ID,$this->getRequestParameter('asuntorecibida_id'));
        $parametros .= "&asuntorecibida_id=" . $this->getRequestParameter('asuntorecibida_id');
    }
    /********************************************************************************************************/	 
    if ($this->getRequestParameter('usuario_destino')) {		
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_destino'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $parametros .= "&usuario_destino=" . $this->getRequestParameter('usuario_destino');     
    }	
    /*****************************************************************************************************/
    if ($this->getRequestParameter('usuario_copia')) {		
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $parametros .= "&usuario_copia=" . $this->getRequestParameter('usuario_copia');     
    }	
    /***********************************************************************************************************/
    if ($this->getRequestParameter('radicador')) {		
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
        $parametros .= "&radicador=" . $this->getRequestParameter('radicador');     
    }
    /***********************************************************************************************************/
    if ($this->getRequestParameter('fechaInicial')) {
      if ($this->getRequestParameter('fechaFinal')) {
            $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_EQUAL);
            $parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
        }else{
          $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_EQUAL);
          $parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
        }
    }	
    /*********************************************************************************************************/
    if ($this->getRequestParameter('fechaCierre')) {
      if ($this->getRequestParameter('fechaCierreFin')) {
          $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_EQUAL);
          $c->addAnd(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierreFin').' 23:59:59',Criteria::LESS_EQUAL);
          $parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
        }else{
          $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_EQUAL);
          $parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
        } 
    }
    /*******************************************************************************************************/
    if ($this->getRequestParameter('regional_id')) {		
        $c->add(ComRecibidaPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));
        $parametros .= "&regional_id=" . $this->getRequestParameter('regional_id');     
    }
    /*******************************************************************************************************/
    if ($this->getRequestParameter('dependencia_id')) {    
        $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
        $parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id');     
    }
    /*******************************************************************************************************/ 	
    if ($this->getRequestParameter('estado_com_recibida_id')) {		
        $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('estado_com_recibida_id'));
        $parametros .= "&estado_com_recibida_id=" . $this->getRequestParameter('estado_com_recibida_id');     
    }
    /****************************************************************************************************/	
    if ($this->getRequestParameter('dependencia_id')) {		
        $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
        $parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id');     
    }
    /****************************************************************************************************/ 		
    if ($this->getRequestParameter('tipo_com_recibida_id')) {		
        $c->add(ComRecibidaPeer::TIPOCOMRECIBIDA_ID,$this->getRequestParameter('tipo_com_recibida_id'));
        $parametros .= "&tipo_com_recibida_id=" . $this->getRequestParameter('tipo_com_recibida_id');     
    }
    /******************************************************************************************************/ 
    if ($this->getRequestParameter('radicado_origen')) {
      $c->add(ComRecibidaPeer::RADICADO_ORIGEN, $this->getRequestParameter('radicado_origen'));
      $parametros .= "&radicado_origen=" . $this->getRequestParameter('radicado_origen');
    }
    /*******************************************************************************************************/ 
    if ($this->getRequestParameter('periodo_id')) {		
        $c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
        $parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');
        $periodo_id = $this->getRequestParameter('periodo_id');
    }
    /********************************************************************************************************/ 
    if ($this->getRequestParameter('fechaVence')) {		
        $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getRequestParameter('fechaVence'));
        $parametros .= "&fechaVence=" . str_replace("/","-",$this->getRequestParameter('fechaVence'));     
    }    
    /******************************************************************************************************/ 	
    if ($this->getRequestParameter('detalles')) {		
        $c->add(ComRecibidaPeer::OBS_REEN_RESP,$this->getRequestParameter('detalles'),Criteria::LIKE);
        $parametros .= "&detalles=" . $this->getRequestParameter('detalles');     
    }
    /******************************************************************************************************/
    if ($this->getRequestParameter('entradas')) {      
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
      $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,14,Criteria::NOT_EQUAL);     
      $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
      $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
      $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);	           		  		
      $parametros .= "&entradas=" . $this->getRequestParameter('entradas');
    }
    /*******************************************************************************************************/
    if ($this->getRequestParameter('leer')) {
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
      $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	           
      $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('leer'));
      $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);		   
      $parametros .= "&leer=" . $this->getRequestParameter('leer');
    }
    
    /********************************************************************************************************/
    if ($this->getRequestParameter('vencidas')) {
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
      $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   			   
      $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d"),Criteria::LESS_THAN);
      $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
      $parametros .= "&vencidas=" . str_replace("/","-",$this->getRequestParameter('vencidas'));
    }	
    /*******************************************************************************************************/
    if ($this->getRequestParameter('porVencer')) {
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);     
      $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
      $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	
      $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getFechaVence(),Criteria::LESS_EQUAL);		   
      $c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d"),Criteria::GREATER_EQUAL);
      $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);	    
      $parametros .= "&porVencer=" . str_replace("/","-",$this->getRequestParameter('porVencer'));     
    }
    /*******************************************************************************************************/
    if ($this->getRequestParameter('entregado')!="") {		   
        $c->add(ComRecibidaPeer::ESTAENTREGADO,$this->getRequestParameter('entregado'));
        $parametros .= "&entregado=" . $this->getRequestParameter('entregado');
    }
    /******************************************************************************************************/
    if ($this->getRequestParameter('marcada')) {		   
        $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
        $parametros .= "&marcada=" . $this->getRequestParameter('marcada');
    }
    /********************************************************************************************************/
    if ($this->getRequestParameter('papelera')) {		   
        $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('papelera'));
        $c->addOr(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,12);
        $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $parametros .= "&papelera=" . $this->getRequestParameter('papelera');		   
    }
    /*********************************************************************************************************/
    if($this->getRequestParameter('ordenar')){
      $c->addDescendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID,$this->getRequestParameter('ordenar'));	
    }else{	
      $c->addDescendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID);
    }  
    /*********************************************************************************************************/
    if($this->getUser()->checkPerm('COM_RECIBIDA_LISTAR_TODAS', $usuariologuiado)){			   
      //$this->user = $this->getComAsignadas(1 , $c);
    /********************************************************************************************************/  
    }else{	
          if(!$this->getRequestParameter('usuario_copia') && !$this->getRequestParameter('usuario_destino')&& !$this->getRequestParameter('radicador') && 	!$this->getRequestParameter('entradas') && !$this->getRequestParameter('leer') && !$this->getRequestParameter('porVencer') && !$this->getRequestParameter('vencidas')){
              $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
              $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
              //$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);	
              $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
              $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
              $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
          }else{        
              $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);        
          }
    }
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
    $c->add(ComRecibidaPeer::ESTAENTREGADO,0);
    $entregados = ComRecibidaPeer::doSelect($c);
    //**********************************************************************************************
    $conexion = Propel::getConnection();				
    $cadSet = '';
    foreach ($entregados as $res) {
          $set = "UPDATE %s  SET  %s = 1 WHERE COMRECIBIDA_ID=".$res->getComrecibidaId();     
          $set = sprintf($set, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::ESTAENTREGADO);		  		   
          $sentencia = $conexion->prepare($set);
          $sentencia->execute();	      		  
    }	  			  	  
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/list?'.$parametros);
 }
  
  
  public function executePlanilla()
  {
    $this->setLayout(false);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario_genera = UsuarioPeer::retrieveByPK($usuariologuiado);
    $parametros = '';
    /***********************************************************************************************************/
    if ($this->getRequestParameter('periodo_id')) {
      //$c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
      $parametros .= " AND periodo_id=" . $this->getRequestParameter('periodo_id');
    }
    /************************************************************************************************************/
    if ($this->getRequestParameter('marcada')) {
      $parametros .= " AND MARCA=" . $usuariologuiado;
    }
    /***********************************************************************************************************/
    $conexion = Propel::getConnection();
    $propel_config = sfPropelDatabase::getConfiguration();
    $dbadapter = $propel_config['propel']['datasources']['propel']['adapter'];
    //**********************************************************************************************************
    switch ($dbadapter) {
      case 'mysql':
        $concatcolum = "CONCAT(usuario.NOMBRE,' ',usuario.APELLIDO)  AS DESTINATARIO";
        $formatfecha01 = 'DATE_FORMAT(com_recibida.FECHA_CREACION, "%Y-%m-%d %H:%i:%s") AS FECHA_CREACION';
        break;
      case 'mssql':
        $concatcolum = "usuario.NOMBRE+' '+usuario.APELLIDO AS DESTINATARIO";
        $formatfecha01 = 'CONVERT(char(10), com_recibida.FECHA_CREACION,126) AS FECHA_CREACION';
        break;
      case 'sqlsrv':
        $concatcolum = "usuario.NOMBRE+' '+usuario.APELLIDO AS DESTINATARIO";
        $formatfecha01 = 'CONVERT(char(10), com_recibida.FECHA_CREACION,126) AS FECHA_CREACION';
        break;
      case 'oracle':
        $concatcolum = "usuario.NOMBRE||' '||usuario.APELLIDO||'-'||dependencia.CODIGO AS DESTINATARIO";
        $formatfecha01 = "to_date(com_recibida.FECHA_CREACION,'YYYY-MM-DD HH24:MI-SS') AS FECHA_CREACION";
        break;
      case 'pgsql':
        $concatcolum = "CONCAT(usuario.NOMBRE,' ',usuario.APELLIDO)  AS DESTINATARIO";
        $formatfecha01 = "to_char(com_recibida.FECHA_CREACION, 'YYYY-MM-DD HH24:MI-SS') AS FECHA_CREACION";
        break;
    }
    //**********************************************************************************************************
    $cadSet = '';
    $sql = "SELECT dependencia.NOMBRE AS DEPENDENCIA_NOMBRE, $concatcolum,
        RADICADO,directorio_externo.NOMBRE AS REMITENTE,com_recibida.ASUNTO,
        com_recibida.OBSERVACIONES,$formatfecha01,tipo_com_recibida.DESCRIPCION AS TIPO_RECIBIDA
      FROM usuario,comrecibida_usuario, com_recibida, directorio_externo,dependencia,tipo_com_recibida
      WHERE com_recibida.COMRECIBIDA_ID=comrecibida_usuario.COMRECIBIDA_ID
      AND usuario.dependencia_id=dependencia.dependencia_id
        AND com_recibida.TIPOCOMRECIBIDA_ID=tipo_com_recibida.TIPOCOMRECIBIDA_ID
      AND ROLUSUARIORECIBIDAID = 2 AND usuario.USUARIO_ID=comrecibida_usuario.USUARIO_ID
      AND directorio_externo.DIRECTORIOEXTERNO_ID=com_recibida.DIRECTORIOEXTERNO_ID" . $parametros;
    //**********************************************************************************************************
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $resultset = $sentencia->fetchAll(PDO::FETCH_BOTH);
    $this->username = $usuariologuiado;
    /******************************INICIO GENERACION DEL PDF****************************************************/
    $orientacion_pdf = "L";
    $pdf = new MYPDF($orientacion_pdf, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ARCHIDHU UARIV');
    $pdf->SetTitle('Planilla Distribucion Interna');
    $pdf->SetSubject('Comunicaciones Recibidas');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_MONOSPACED, 'B', 10));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', 6));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(5, 51, 20);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setPrintFooter(false);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_FOOTER);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $l = 0;
    $pdf->setLanguageArray($l);
    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('dejavusans', '', 10);
    // add a page
    $pdf->AddPage();
    $html_cuerpo = '<table border="1" width="100%" valign="middle" cellspacing="0"  cellpadding="0" >';
    $html_encabezado = "";
    $count_imp = 0;
    
    foreach ($resultset as $object) {
      //while($object = $sentencia->fetch(PDO::FETCH_BOTH)){
      //FUNCION VALIDAR LARGO DEL ASUNTO DE LA COMUNICACION
      if (strlen($object['ASUNTO']) > 2000) {
        $str_asunto = substr($object['ASUNTO'], 0, 57) . '...';
      } else {
        $str_asunto = $object['ASUNTO'];
      }
      //FUNCION VALIDAR LARGO DEL DSTINATARIO DE LA COMUNICACION
      if (strlen($object['DESTINATARIO']) > 400) {
        $str_destino = substr($object['DESTINATARIO'], 0, 37) . '...';
      } else {
        $str_destino = $object['DESTINATARIO'];
      }
      //FUNCION VALIDAR LARGO DEL REMITENTE DE LA COMUNICACION
      if (strlen($object['REMITENTE']) > 300) {
        $str_remitente = substr($object['REMITENTE'], 0, 26) . '...';
      } else {
        $str_remitente = $object['REMITENTE'];
      }
      //FUNCION VALIDAR LARGO DEL DEPENDENCIA DE LA COMUNICACION
      if (strlen($object['DEPENDENCIA_NOMBRE']) > 50) {
        $str_dependencia = substr($object['DEPENDENCIA_NOMBRE'], 0, 24) . '...';
      } else {
        $str_dependencia = $object['DEPENDENCIA_NOMBRE'];
      }
      //***********************************************************************************************
      $html_cuerpo .= '<tr>';
      $html_cuerpo .= '<td width="105" nowrap="" align="center"><font size="8">' . htmlentities($str_dependencia) . '</font></td>';
      $html_cuerpo .= '<td width="130" nowrap align="center"><font size="7">' . htmlentities($str_destino) . '</font></td>';
      $html_cuerpo .= '<td width="100" nowrap="" align="center"><font size="8">' . $object['RADICADO'] . '</font></td>';
      $html_cuerpo .= '<td width="109" nowrap="" align="center"><font size="8">' . htmlentities($str_remitente) . '</font></td>';
      $html_cuerpo .= '<td width="55"><font size="8">' . htmlentities($object['TRAMITE']) . '</font></td>';
      $html_cuerpo .= '<td width="223"><font size="8">' . htmlentities($str_asunto) . '</font></td>';
      $html_cuerpo .= '<td width="140" nowrap=""><font size="8">' . htmlentities($object['OBSERVACIONES']) . '</font></td>';
      $html_cuerpo .= '<td width="140" nowrap="" align="center"></td>';
      $html_cuerpo .= '</tr>';
      $count_imp++;
    }
    //*****************************************************************************************
    $html_cuerpo .= '</table>';
    $html_encabezado .= $html_cuerpo;
    //echo $html_encabezado.$html_cuerpo;
    $pdf->writeHTML($html_encabezado, true, false, true, false, '');
    $pdf->Output('planilla_distribucion_externas_recibidas.pdf', 'I');
    //ob_end_flush();
    //$this->forward404Unless($this->resultset);
    //*****************************************************************************************
  }
   

  public function executePlanillaVur()
  {
    $this->setLayout(false);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario_genera = UsuarioPeer::retrieveByPK($usuariologuiado); 
    $parametros = '';    
    //************************************************************************************************************ 
    if ($this->getRequestParameter('periodo_id')) {
      //$c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
      $parametros .= " AND periodo_id=" . $this->getRequestParameter('periodo_id');     
    }
    //************************************************************************************************************
    if ($this->getRequestParameter('marcada')) {      
      $parametros .= " AND MARCA=" . $usuariologuiado;
    }    
    //************************************************************************************************************
	$conexion = Propel::getConnection();
    $propel_config = sfPropelDatabase::getConfiguration();
    $dbadapter = $propel_config['propel']['datasources']['propel']['adapter'];
    //************************************************************************************************************
    switch($dbadapter){
        case 'mysql':
            $concatcolum = "CONCAT(usuario.NOMBRE,' ',usuario.APELLIDO)  AS DESTINATARIO";
            $formatfecha01 = 'DATE_FORMAT(com_recibida.FECHA_CREACION, "%Y-%m-%d %H:%i:%s") AS FECHA_RADICADO';
        break;
        case 'mssql':
            $concatcolum = "usuario.NOMBRE+' '+usuario.APELLIDO AS DESTINATARIO";
            $formatfecha01 = 'CONVERT(char(10), com_recibida.FECHA_CREACION,126) AS FECHA_RADICADO';
        break;
        case 'oracle':
            $concatcolum = "usuario.NOMBRE||' '||usuario.APELLIDO||'-'||dependencia.CODIGO AS DESTINATARIO";
            $formatfecha01 = "to_date(com_recibida.FECHA_CREACION,'YYYY-MM-DD HH24:MI-SS') AS FECHA_RADICADO";
        break;
        case 'pgsql':
            $concatcolum = "CONCAT(usuario.NOMBRE,' ',usuario.APELLIDO)  AS DESTINATARIO";
            $formatfecha01 = "to_char(com_recibida.FECHA_CREACION, 'YYYY-MM-DD HH24:MI-SS') AS FECHA_RADICADO";
        break;
    }
    //**********************************************************************************************************
	$cadSet = '';
	$sql = "SELECT dependencia.NOMBRE AS DEPENDENCIA_NOMBRE,$concatcolum,
    com_recibida.RADICADO,com_recibida.ASUNTO,
    tipo_com_recibida.DESCRIPCION AS TRAMITE,$formatfecha01
	FROM usuario,comrecibida_usuario, com_recibida, tipo_com_recibida,dependencia
	WHERE com_recibida.COMRECIBIDA_ID=comrecibida_usuario.COMRECIBIDA_ID
	AND usuario.dependencia_id=dependencia.dependencia_id
	AND ROLUSUARIORECIBIDAID = 2 AND usuario.USUARIO_ID=comrecibida_usuario.USUARIO_ID
	AND tipo_com_recibida.TIPOCOMRECIBIDA_ID=com_recibida.TIPOCOMRECIBIDA_ID".$parametros;
    //**********************************************************************************************************   
    $conexion = Propel::getConnection();
    $cadSet = '';
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $resultset = $sentencia->fetchAll(PDO::FETCH_BOTH);
    $this->username = $usuariologuiado;
    //******************************INICIO GENERACION DEL PDF**************************************************/
    $orientacion_pdf = "L";
    $pdf = new MYPDF($orientacion_pdf, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //set tipo celdas encabezados
    $pdf->setHeaderCells(2);
    $pdf->setNombreInforme("RECEPCION DE DOCUMENTOS");
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ARCHIDHU UARIV');
    $pdf->SetTitle('Planilla Distribucion Interna');
    $pdf->SetSubject('Comunicaciones Recibidas');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');            
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_MONOSPACED, 'B', 10));    
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 6));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //**********************************************************************************************************
    //set margins
    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(3,41, 20);
    $pdf->SetHeaderMargin(3);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setPrintFooter(false);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_FOOTER);
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //set some language-dependent strings
    $l=0;
    $pdf->setLanguageArray($l);     
    //**********************************************************************************************************    
    // set font
    $pdf->SetFont('dejavusans', '', 10);
    // add a page
    $pdf->AddPage();
    $html_cuerpo = '<table border="1" width="100%" cellspacing="0"  cellpadding="0" >';
    $html_encabezado = "";
    //**********************************************************************************************************
    foreach($resultset as $object){
        //FUNCION VALIDAR LARGO DEL ASUNTO DE LA COMUNICACION
        if(strlen($object['ASUNTO']) > 200){
            $str_asunto = substr($object['ASUNTO'],0);  
        }else{
            $str_asunto = $object['ASUNTO'];
        }
        //FUNCION VALIDAR LARGO DEL DSTINATARIO DE LA COMUNICACION
        if(strlen($object['DESTINATARIO']) > 40){
            $str_destino = substr($object['DESTINATARIO'],0);  
        }else{
            $str_destino = $object['DESTINATARIO'];
        }        
        //FUNCION VALIDAR LARGO DEL DEPENDENCIA DE LA COMUNICACION
        if(strlen($object['DEPENDENCIA_NOMBRE']) > 27){
            $str_dependencia = substr($object['DEPENDENCIA_NOMBRE'],0);  
        }else{
            $str_dependencia = $object['DEPENDENCIA_NOMBRE'];
        }
        $html_cuerpo .= '<tr>';
        $html_cuerpo .= '<td width="60" nowrap align="left"><font size="7">'.($str_dependencia).'</font></td>';
        $html_cuerpo .= '<td width="109" nowrap align="center"><font size="7">'.($str_destino).'</font></td>';
        $html_cuerpo .= '<td width="70" nowrap="" align="center"><font size="8">'.($object['RADICADO']).'</font></td>';
        $html_cuerpo .= '<td width="75" nowrap="" align="center"><font size="8">&nbsp;</font></td>';
        $html_cuerpo .= '<td width="298" nowrap="" align="left"><font size="8">'.($str_asunto).'</font></td>';                
        $html_cuerpo .= '<td width="102"><font size="8">'.($object['TRAMITE']).'</font></td>';        
        $html_cuerpo .= '<td nowrap="" align="center"></td>';
        //$html_cuerpo .= '<td width="80" nowrap="" align="left"><font size="8">'.$object['FECHA_RADICADO'].'</font></td>';
        $html_cuerpo .= '</tr>';        
        //$count_imp++;
    }
    //**********************************************************************************************************
    $html_cuerpo .= '</table>';
    $html_encabezado .= $html_cuerpo;
    //echo $html_encabezado.$html_cuerpo;
    $pdf->writeHTML($html_encabezado, true, false, true, false,'');
    //$pdf->writeHTML($html_cuerpo, true, true , true, true,'LTR');
    $pdf->Output('planilla_distribucion_externas_recibidas.pdf','I');
    //$this->forward404Unless($this->resultset);
  }
	  
  public function executeExcelInt()
  {
	set_time_limit(45);
    $this->setLayout(false);
    //******************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');  	
    $this->username = $this->getUser()->getAttribute('username', '', 'subscriber');
    $this->regUser = UsuarioPeer::retrieveByPk($usuariologuiado);
    //******************************************************************************************************
    $parametros = "a=1";
    $papelera = false;	  	
    $c = new Criteria();  	
    $por_rol=0;
    //******************************************************************************************************
    $c = $this->getCriteriaBasic($c);
    //******************************************************************************************************
    $criteria = clone $c;  // Clonamos el objeto para evitar modificar el original  
    $c->clearSelectColumns(); // Eliminamos la columnas de seleccion en caso de que esten definidas
	  $c->setDistinct();
    //******************************************************************************************************
    // Hacemos joins
    $cjoins[] = new Join(ComRecibidaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::EMPRESA_MENSAJERIA_ID, EmpresaMensajeriaPeer::EMPRESA_MENSAJERIA_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::FORMARECEPCION_ID, FormaRecepcionPeer::FORMARECEPCION_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::REGIONAL_ID, RegionalPeer::REGIONAL_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::COMENVIADA_ID, ComEnviadaPeer::COMENVIADA_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaInteresadosPeer::COMRECIBIDA_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComrecibidaInteresadosPeer::INTERESADO_ID, InteresadosPeer::INTERESADO_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(InteresadosPeer::CIUDAD_ID, CiudadPeer::CIUDAD_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(CiudadPeer::DEPARTAMENTO_ID, DepartamentoPeer::DEPARTAMENTO_ID, Criteria::LEFT_JOIN);
    //$cjoins[] = new Join(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,EstadoComRecibidaPeer::ESTADOCOMRECIBIDA_ID,Criteria::LEFT_JOIN);
    //$cjoins[] = new Join(ComrecibidaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID,Criteria::LEFT_JOIN);
    //******************************************************************************************************
    foreach ($cjoins as $join) {
      $isAddJoin = ComRecibidaPeer::validateExistJoin($c->getJoins(), $join);
      if (!$isAddJoin) {
        $c->addJoinObject($join);
      }
    }
    //******************************************************************************************************
    $c->addSelectColumn(DependenciaPeer::NOMBRE);//0
    $c->addSelectColumn(DirectorioExternoPeer::FUNCIONARIO);//1  
    $c->addSelectColumn(ComRecibidaPeer::RADICADO);//2
    $c->addSelectColumn(ComRecibidaPeer::ASUNTO);//3
    $c->addSelectColumn(EmpresaMensajeriaPeer::NOMBRE);//4
    $c->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);//5
    $c->addSelectColumn(DirectorioExternoPeer::NOMBRE);//6
    $c->addSelectColumn(RegionalPeer::DESCRIPCION);//7
    $c->addSelectColumn(DirectorioExternoPeer::NIT);//8
    $c->addSelectColumn(ComRecibidaPeer::FOLIOS);//9
    $c->addSelectColumn(FormaRecepcionPeer::DESCRIPCION);//10
    $c->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);//11
    $c->addSelectColumn(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA);//12
    $c->addSelectColumn(InteresadosPeer::PRIMER_NOMBRE);//13
    $c->addSelectColumn(InteresadosPeer::SEGUNDO_NOMBRE);//14
    $c->addSelectColumn(InteresadosPeer::PRIMER_APELLIDO);//15
    $c->addSelectColumn(InteresadosPeer::SEGUNDO_APELLIDO);//16
    $c->addSelectColumn(InteresadosPeer::NUMERO_IDENTIFICACION);//17
    $c->addSelectColumn(ComRecibidaPeer::NUMERO_FUD);//18
    $c->addSelectColumn(ComRecibidaPeer::FECHA_RECIBIDO);//19
    $c->addSelectColumn(ComRecibidaPeer::GUIA);//20
    $c->addSelectColumn(ComRecibidaPeer::ANEXOS);//21
    $c->addSelectColumn(ComRecibidaPeer::OBSERVACIONES);//22
    $c->addSelectColumn(CiudadPeer::NOMBRE);//23
    $c->addSelectColumn(DepartamentoPeer::NOMBRE);//24
    $c->addSelectColumn(ComRecibidaPeer::RESPTA_INTEGRACION);//25
    $c->addSelectColumn(ComRecibidaPeer::NUMERO_RADICACION);//26
    $c->addSelectColumn(ComEnviadaPeer::RADICADO);//27
	$c->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID); //28
	$c->addSelectColumn(InteresadosPeer::EMAIL); //29
	$c->addSelectColumn(ComEnviadaPeer::FECHA_CREACION); //30
    //******************************************************************************************************
    $sql_custom = "(SELECT TOP 1 CONCAT(URADICA.NOMBRE, ' ', URADICA.APELLIDO)";
    $sql_custom .= " FROM USUARIO URADICA";
    $sql_custom .= " JOIN COMRECIBIDA_USUARIO COMURADICA ON URADICA.USUARIO_ID=COMURADICA.USUARIO_ID";
    $sql_custom .= " WHERE COMURADICA.ROLUSUARIORECIBIDAID = 1 ";
    $sql_custom .= " AND COMURADICA.COMRECIBIDA_ID = ".ComRecibidaPeer::COMRECIBIDA_ID.")";
    $c->addAsColumn("NOMBRE_RADICADOR",$sql_custom);//31
    //******************************************************************************************************
    $sql_custom2 = "(SELECT TOP 1 CONCAT(UDESTINO.NOMBRE, ' ', UDESTINO.APELLIDO)";
    $sql_custom2 .= " FROM USUARIO UDESTINO";
    $sql_custom2 .= " JOIN COMRECIBIDA_USUARIO COMUDESTINO ON UDESTINO.USUARIO_ID=COMUDESTINO.USUARIO_ID";
    $sql_custom2 .= " WHERE COMUDESTINO.ROLUSUARIORECIBIDAID = 2 AND COMUDESTINO.ESTA_ASIGNADA = 1";
    $sql_custom2 .= " AND COMUDESTINO.COMRECIBIDA_ID = ".ComRecibidaPeer::COMRECIBIDA_ID.")";
    $c->addAsColumn("NOMBRE_DESTINATARIO",$sql_custom2);//32
    //******************************************************************************************************
	$sql_custom3 = "(SELECT TOP 1 ESTCOMREC.DESCRIPCION";
    $sql_custom3 .= " FROM ESTADO_COM_RECIBIDA ESTCOMREC";
    $sql_custom3 .= " JOIN COMRECIBIDA_USUARIO CRUEST ON ESTCOMREC.ESTADOCOMRECIBIDA_ID=CRUEST.ESTADOCOMRECIBIDA_ID";
    $sql_custom3 .= " WHERE CRUEST.ROLUSUARIORECIBIDAID = 2 AND CRUEST.ESTA_ASIGNADA = 1";
    $sql_custom3 .= " AND CRUEST.COMRECIBIDA_ID = " . ComRecibidaPeer::COMRECIBIDA_ID . ")";
    $c->addAsColumn("ESTADO_COMREC", $sql_custom3); //33
    //******************************************************************************************************
	  //$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    //if($por_rol == 0 )
    //    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    //else
    //    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,$por_rol);
    //******************************************************************************************************
    $arrOrigen = array();
    $this->resultset = ComRecibidaPeer::doSelectStmt($c);
    $this->arrOrigen = $arrOrigen;
  }
	  
  public function executeExcel()
  {
	set_time_limit(45);
    $this->setLayout(false);
    //******************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');  	
    $this->username = $this->getUser()->getAttribute('username', '', 'subscriber');
    $this->regUser = UsuarioPeer::retrieveByPk($usuariologuiado);
    //******************************************************************************************************
    $parametros = "a=1";
    $papelera = false;	  	
    $c = new Criteria();  	
    $por_rol=0;
    //******************************************************************************************************
    $c = $this->getCriteriaBasic($c);
    //******************************************************************************************************
    $criteria = clone $c;  // Clonamos el objeto para evitar modificar el original  
    $c->clearSelectColumns(); // Eliminamos la columnas de seleccion en caso de que esten definidas
	  $c->setDistinct();
    //******************************************************************************************************
    // Hacemos joins
    $cjoins[] = new Join(ComRecibidaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID, Criteria::LEFT_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::FORMARECEPCION_ID, FormaRecepcionPeer::FORMARECEPCION_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::REGIONAL_ID, RegionalPeer::REGIONAL_ID, Criteria::INNER_JOIN);
    $cjoins[] = new Join(ComRecibidaPeer::COMENVIADA_ID, ComEnviadaPeer::COMENVIADA_ID, Criteria::LEFT_JOIN);
    //$cjoins[] = new Join(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,EstadoComRecibidaPeer::ESTADOCOMRECIBIDA_ID,Criteria::LEFT_JOIN);
    //$cjoins[] = new Join(ComrecibidaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID,Criteria::LEFT_JOIN);
    //******************************************************************************************************
    foreach ($cjoins as $join) {
      $isAddJoin = ComRecibidaPeer::validateExistJoin($c->getJoins(), $join);
      if (!$isAddJoin) {
        $c->addJoinObject($join);
      }
    }    
	//******************************************************************************************************
    $c->addSelectColumn(DependenciaPeer::NOMBRE);//0
    $c->addSelectColumn(DirectorioExternoPeer::FUNCIONARIO);//1  
    $c->addSelectColumn(ComRecibidaPeer::RADICADO);//2
    $c->addSelectColumn(ComRecibidaPeer::ASUNTO);//3
    $c->addSelectColumn(ComRecibidaPeer::GUIA);//4
    $c->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);//5  
    //$c->addSelectColumn(UsuarioPeer::NOMBRE);//-6
    //$c->addSelectColumn(UsuarioPeer::APELLIDO);//-7
    $c->addSelectColumn(DirectorioExternoPeer::NOMBRE);//6
    $c->addSelectColumn(RegionalPeer::DESCRIPCION);//7
    $c->addSelectColumn(DirectorioExternoPeer::NIT);//8
    $c->addSelectColumn(ComRecibidaPeer::FOLIOS);//9
    $c->addSelectColumn(FormaRecepcionPeer::DESCRIPCION);//10
    $c->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);//11
    $c->addSelectColumn(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA);//12
    $c->addSelectColumn(ComRecibidaPeer::RESPTA_INTEGRACION);//13
	$c->addSelectColumn(ComEnviadaPeer::RADICADO);//14
	$c->addSelectColumn(ComEnviadaPeer::FECHA_CREACION); //15
    $c->addSelectColumn(ComRecibidaPeer::FECHA_DE_ANULACION); //16
	$c->addSelectColumn(ComRecibidaPeer::OBS_ANULACION); //17
    //******************************************************************************************************
    $sql_custom = "(SELECT TOP 1 CONCAT(URADICA.NOMBRE, ' ', URADICA.APELLIDO)";
    $sql_custom .= " FROM USUARIO URADICA";
    $sql_custom .= " JOIN COMRECIBIDA_USUARIO COMURADICA ON URADICA.USUARIO_ID=COMURADICA.USUARIO_ID";
    $sql_custom .= " WHERE COMURADICA.ROLUSUARIORECIBIDAID = 1 ";
    $sql_custom .= " AND COMURADICA.COMRECIBIDA_ID = ".ComRecibidaPeer::COMRECIBIDA_ID.")";
    $c->addAsColumn("NOMBRE_RADICADOR",$sql_custom);//18
    //******************************************************************************************************
    $sql_custom2 = "(SELECT TOP 1 CONCAT(UDESTINO.NOMBRE, ' ', UDESTINO.APELLIDO)";
    $sql_custom2 .= " FROM USUARIO UDESTINO";
    $sql_custom2 .= " JOIN COMRECIBIDA_USUARIO COMUDESTINO ON UDESTINO.USUARIO_ID=COMUDESTINO.USUARIO_ID";
    $sql_custom2 .= " WHERE COMUDESTINO.ROLUSUARIORECIBIDAID = 2 AND COMUDESTINO.ESTA_ASIGNADA = 1";
    $sql_custom2 .= " AND COMUDESTINO.COMRECIBIDA_ID = ".ComRecibidaPeer::COMRECIBIDA_ID.")";
    $c->addAsColumn("NOMBRE_DESTINATARIO",$sql_custom2);//19
    //******************************************************************************************************
    $c->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID); //20
    //******************************************************************************************************
    //$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
    //******************************************************************************************************
	//$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    /*if($por_rol == 0 )
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    else
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,$por_rol);*/
    //******************************************************************************************************
    $arrOrigen = array();
    $this->resultset = ComRecibidaPeer::doSelectStmt($c);
    $this->arrOrigen = $arrOrigen;
  }
  
  public function executeAutocompletar()
  {
    $search=$this->getRequestParameter('usuario');
    $c = new Criteria();
    $name = array();
    $c->add(UsuarioPeer::NOMBRE,$search.'%',Criteria::LIKE);
    $userName = UsuarioPeer::doSelect($c);
    echo "<ul>";
    foreach ($userName as $value){
          echo "<li id=".$value->getUsuarioId().">".$name = $value->getNombre().' '.$value->getApellido()."</li>"; 
    } 
    //$this->name = $name;
    $this->name = $userName;    
    echo "</ul>";
  }
  
  public function executeReenviar()
  {
    $currentForm = "COM_RECIBIDA_ASIGNAR_GESTOR";
    $this->verificaPrilegioCerrar($currentForm); 	
    //*******************************************************************************************
    $c = new Criteria();
    $this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $this->listcom_users = $this->com_recibida->getUsuariosListCom();
    $this->forward404Unless($this->com_recibida);
  }
  
  public function executeAsignarArea()
  {
    $currentForm = "COM_RECIBIDA_ASIGNAR_DISTRIBUIDOR";
    $this->verificaPrilegioCerrar($currentForm); 	
    //*******************************************************************************************
    $c = new Criteria();
    $this->com_recibida = ComRecibidaPeer::retrieveByPk(trim($this->getRequestParameter('comrecibida_id')));
    $this->forward404Unless($this->com_recibida);
  }

  public function executeRemitir()
  {
    $currentForm = "RECIBIDA_REMITIR_COMUNICACION";
    $this->verificaPrilegioCerrar($currentForm); 	
    //*******************************************************************************************
    $this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    //*******************************************************************************************
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$this->getRequestParameter('comrecibida_id'));
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    //$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
    $idUser = ComrecibidaUsuarioPeer::doSelect($c);
    foreach ($idUser as $resp){
		  $usuario_id = $resp->getUsuarioId();
		  $this->cargousuarioId = $resp->getCargousuarioId();
    }
    //*******************************************************************************************
    $this->userDestino = UsuarioPeer::retrieveByPK($usuario_id); 
    $this->cargousuarioIdCopias = "";
    $this->pais = PaisPeer::doSelect(new Criteria());
    $this->forward404Unless($this->com_recibida);
  }
  
  public function executeDevolver()
  {
    $currentForm = "COM_RECIBIDA_DEVOLVER_RADICADO";
    $this->verificaPrilegioCerrar($currentForm); 	
    //*******************************************************************************************
    $this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    //*******************************************************************************************
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$this->getRequestParameter('comrecibida_id'));
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
    $idUser = ComrecibidaUsuarioPeer::doSelect($c);
    //*******************************************************************************************
    foreach ($idUser as $resp){
		$usuario_id = $resp->getUsuarioId();
		$this->cargousuarioId = $resp->getCargousuarioId();
    }
    //*******************************************************************************************
	//$tipoprocesocom_id = $this->com_recibida->getTipoprocesocomId() == 3 ? 2 : 6;
	$tipoprocesocom_id = 6;
	if($this->com_recibida->getTipoprocesocomId() == 3){
		$tipoprocesocom_id = 2;
	}elseif($this->com_recibida->getTipoprocesocomId() == 2){
		$tipoprocesocom_id = 6;
	}
	//*******************************************************************************************
    $this->usuario_id = null;
    $this->cargousuario_id = null;
    $this->nombre_usuario = null;
	//*******************************************************************************************
    $usuario_calidad = UsuarioPeer::getUserDestinoComByProceso($tipoprocesocom_id);
    //var_dump($usuario_calidad);exit;
    if(count($usuario_calidad) == 1){
      $this->usuario_id = $usuario_calidad[0]->getUsuarioId();
      $this->cargousuario_id = $usuario_calidad[0]->getCargousuarioId();
      $this->nombre_usuario = $usuario_calidad[0]->getUsuario()->getFullNombre();
    }
    //*******************************************************************************************
    $this->tipoprocesocom_id = $tipoprocesocom_id;
    $this->userDestino = UsuarioPeer::retrieveByPK($usuario_id); 
    $this->cargousuarioIdCopias = "";
    $this->forward404Unless($this->com_recibida);
  }

  public function executeConsulta()
  {
    $currentForm = "com_recibida/consulta";
    $this->verificaPrilegio($currentForm); 		      		    	
    $this->com_recibida = new ComRecibida();
    $this->dirExt = new DirectorioExterno();
    //*******************************************************************************************
    /*$c = new Criteria();
    $c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
    $this->usuarios = UsuarioPeer::doSelect($c);*/
    //*******************************************************************************************
    $this->mostrarUsuario = trim($this->getRequestParameter('mostrarUsuario')) ? 0 : 1;
  }
  
  public function executeAnular()
  { 	
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));  	
    $this->com_recibida = $com_recibida;
    $this->forward404Unless($this->com_recibida); 
  }
  
  public function executeDeleteInterCom()
  {
    $currentForm = "COM_RECIBIDA_ELIMINAR_INTERESADO";
    $status = 400;
    $errorMsg = "Error de servidor";
    if($this->tienePrilegio($currentForm)){
      $com_recibida = ComRecibidaPeer::retrieveByPk(trim($this->getRequestParameter('comrecibida_id')));
      $interesado_com = ComrecibidaInteresadosPeer::retrieveByPk(trim($this->getRequestParameter('interesadocom_id')));
      //*******************************************************************************************
      if($interesado_com != null){
        $interesado_com->delete();
        //*****************************************************************************************
        if($interesado_com->isDeleted()) { 
          $errorMsg = "Registro eliminado satisfactoriamente!";
          $status = 200;
        }else{ $errorMsg = "La solicitud no se puede procesar!"; }
      }else{
        $errorMsg = "Se detecto un error interno!";
      }
    }else{
      $errorMsg = "Esta funcionalidad no esta disponible!";
    }
    //*******************************************************************************************
    $array = json_encode(array( 'status' => $status, 'message' => $errorMsg));
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }
  
  public function executeRespuestaMasiva()
  {
    $currentForm = "CREAR_RESPUESTA_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->verificaPrilegioCerrar($currentForm);
  	$com_recibida = new ComRecibida();
    $this->plantillas_com = PlantillasComPeer::getPlantillasComList(4);
    $this->lista_codigos = PlantillasDetallePeer::getPlantillasComDetalleList();
    //*******************************************************************************************    
    //$this->forward404Unless($com_recibida); 
  }
  
  public function executeAddBatchRespuesta()
  {
    $currentForm = "CREAR_RESPUESTA_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->verificaPrilegioCerrar($currentForm);
    //*******************************************************************************************
    $plantillascom_id = trim($this->getRequestParameter('plantillascom_id'));
    $firmantesId = trim($this->getRequestParameter('firmanteId'));
    $cargosUsuarioIdFirmas = trim($this->getRequestParameter('cargousuarioIdFirma'));
    $vfasignadosId = trim($this->getRequestParameter('vfasignadosId'));
    //*******************************************************************************************
    $arrUser  = preg_split("/[,]+/",trim($firmantesId),-1,PREG_SPLIT_NO_EMPTY);
    $arrCargoUser  = preg_split("/[,]+/",trim($cargosUsuarioIdFirmas),-1,PREG_SPLIT_NO_EMPTY);
    $arrVfResponse  = preg_split("/[,]+/",trim($vfasignadosId),-1,PREG_SPLIT_NO_EMPTY);
    //*******************************************************************************************
    $content_text = PlantillasDetallePeer::getHtmlDataById($arrVfResponse);
    //*******************************************************************************************
    $c = new Criteria();
    $c->setDistinct();
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
    $c->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);
    $list_com  = ComRecibidaPeer::doSelectStmt($c);
    //*******************************************************************************************
	while($object = $list_com->fetch()){
      if(!ComRecibidaPeer::validateRespuestaBatch($object[0])){
        $addmasiva = new ComenviadaMasivas();
        $addmasiva->setComrecibidaId($object[0]);
        $addmasiva->setUsuarioId($usuariologuiado);
        $addmasiva->setPlantillascomId($plantillascom_id);
        $addmasiva->setContenidoText($content_text);
        $addmasiva->setEstadoProceso(1);
        $addmasiva->setUsuariosFirmas($firmantesId);
        $addmasiva->setCargosFirmas($cargosUsuarioIdFirmas);
        $addmasiva->setFechaCreacion(date("Y-m-d G:i:s"));
        $addmasiva->save();
      }
	}
    //*******************************************************************************************
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/listAddBatch?status='.md5('pendiente'));
  }
  
  public function executeListAddBatch()
  {
    $currentForm = "LISTA_RESPUESTAS_MASIVAS";
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->verificaPrilegioCerrar($currentForm);
    //*******************************************************************************************
    $bystatus = $this->getRequestParameter('status');
    $this->parametros = "&status=".$bystatus;
    //*******************************************************************************************
	$c = new Criteria();  	
	$c->setDistinct();
    if($bystatus == md5('pendiente')){
	   //$c->add(ComenviadaMasivasPeer::ESTADO_PROCESO,1);
    }
    $c->addDescendingOrderByColumn(ComenviadaMasivasPeer::FECHA_CREACION);					
    //*******************************************************************************************
    $pager = new sfPropelPager('ComenviadaMasivas', 50);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
    $pager->init();
	$this->pager = $pager;	
  }
  
  public function executeUpdateAnular()
  {    
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));    
    $this->forward404Unless($com_recibida);	  
    $com_recibida_anterior = clone $com_recibida;
    $com_recibida->setFechaDeAnulacion($this->getRequestParameter('fecha_de_anulacion'));
    $com_recibida->setObsAnulacion($this->getRequestParameter('obs_anulacion'));
    $com_recibida->setEstadocomrecibidaId(14);
    $com_recibida->save();
    //**************************************************************************
    $this->actualizaEstados($com_recibida->getPrimaryKey(),14,0);
    //**************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getPrimaryKey());
  }        
    
  public function executeUpdateImpuesto()
  {    
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $fechamaximaResp  = $com_recibida->getFechaMaximaRespuesta();
    $this->forward404Unless($com_recibida);
    //*********************************************************************************************
    $com_recibida_anterior = clone $com_recibida;
    $com_recibida->setTipoimpuestoId($this->getRequestParameter('tipoimpuesto_id'));
	//**************************************************************************
    if($this->getRequestParameter('fecha_maxima_respuesta')){
      $com_recibida->setFechaMaximaRespuesta($this->getRequestParameter('fecha_maxima_respuesta'));
    }
    //*********************************************************************************************
    $com_recibida->setFechaMaximaRespuestaImp($fechamaximaResp);
    if($this->getRequestParameter('fecha_documento')){
      $com_recibida->setFechaDocumento($this->getRequestParameter('fecha_documento'));
    }
    //*********************************************************************************************
    $com_recibida->setCertRevFiscal($this->getRequestParameter('cert_rev_fiscal'));      
    $com_recibida->setOpaConsulta($this->getRequestParameter('opa_consulta'));
    $com_recibida->setObsImpuestos($this->getRequestParameter('obs_impuestos'));
    if($this->getRequestParameter('fecha_recibo_imp')){
      $com_recibida->setFechaReciboImp($this->getRequestParameter('fecha_recibo_imp'));
    }
    //*********************************************************************************************
    $com_recibida->save();
    //*********************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getComrecibidaId());
  }
  
  public function executeRadicado()
  {
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $this->com_recibida = $com_recibida;
  }
  
  public function executeViewerLiteModal()  //este si tiene vtoken
  {
      $this->verificaPrilegioCerrar("com_recibida/show");
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
      //*********************************************************************************************************************
      try
      {
        $comrecibida_id = $this->getRequestParameter('comindex_pk');
        $com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //********************************************************************************
        $vtoken = $this->getRequestParameter('vtoken') ? trim($this->getRequestParameter('vtoken')) : null;
        $current_token = md5(base64_encode($com_recibida->getRadicado()).base64_encode($com_recibida->getFechaCreacion()));
        if($vtoken == $current_token)
        {
          if($com_recibida == null){return;}
          //*****************************************************************************************************************
          $source_file = $com_recibida->getPathImageDigitByCom(); 
          if(file_exists($source_file))
          {
              $fname = sprintf("%s.%s", md5(uniqid().time()), "pdf");
              //**************************************************************************************************************
              $target_base = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
              $folder_tmp = md5($com_recibida->getPrimaryKey().time());
              $target_path = $target_base.DIRECTORY_SEPARATOR.$folder_tmp;
              //**************************************************************************************************************
              simad_util::createPath($target_path);            
              if(@copy($source_file, $target_path.DIRECTORY_SEPARATOR.$fname))
              {
                  $pdf_file = '/tmp/'.$folder_tmp.'/'.$fname;
                  sfContext::getInstance()->getUser()->setAttribute('document_idx', $pdf_file, 'subscriber');
                  return $this->renderPartial('pdfContent',array('pdf_file'=>$pdf_file, 'com_recibida' => $com_recibida));
              }
              else
              {
                $this->redirect(sfConfig::get('base_simad').'/no_file_exists.html');
              }
          }
          else
          {
            $this->redirect(sfConfig::get('base_simad').'/no_file_exists.html');
          }
        }
        else
        {
          $this->redirect(sfConfig::get('base_simad').'/no_autorizado_cerrar.html');
        }
      } 
      catch (PropelException $th) 
      {
        $this->redirect(sfConfig::get('base_simad').'/no_file_exists.html');
      }
      catch (\Exception $th) 
      {
        $this->redirect(sfConfig::get('base_simad').'/no_file_exists.html');
      }
      catch (\Throwable $th) 
      {
        $this->redirect(sfConfig::get('base_simad').'/no_file_exists.html');
      }
  }

  public function executeViewImageDigit()
  {
	  $comrecibida_id = base64_decode($this->getRequestParameter('q_vars'));
    $com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
    //********************************************************************************
    $vtoken = $this->getRequestParameter('vtoken') ? trim($this->getRequestParameter('vtoken')) : null;
    $current_token = md5($com_recibida->getRadicado().$usuariologuiado.$com_recibida->getFechaCreacion());
    if($vtoken == $current_token){
      $response_process = $com_recibida->getUriImageDigitById();
    }else{
      $response_process['message'] = 'No tine acceso a este recurso, actualice la pagina e intente de nuevo o comuniquese con el administrador del sistema';
    }
    //***********************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText(json_encode($response_process));
  }

  public function executeImageThumbData()
  {
    $currentForm = "com_recibida/show";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');    
    if(!$this->tienePrilegio($currentForm)){
      $response_process['htmlthumb'] = '<div class="mail-attachments"><span>No existe vista previa</span></div>';
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText(json_encode($response_process));
    }
    //***********************************************************************************************
    $thumb_default = sfConfig::get('theme_simad').'/assets/images/preview_error.jpg';
    $html_ethubm = '<div class="mail-attachments"><ul style="list-style: none;padding: 0;margin: 0;background-color: #ddd;">';
    $html_ethubm .= '<li style="border: dashed 1.5px green;margin-bottom: 5px;">';
    $html_ethubm .= '<a href="#" class="thumb">';
    $html_ethubm .= '<img style="max-width: 150px;" class="img-rounded img-responsive" src="'.$thumb_default.'" />';
    $html_ethubm .= '</a></li>';
    //***********************************************************************************************
    $output_format = "jpg";
    $qthumb = trim($this->getRequestParameter('qthumb')) ? SED::decryption(trim($this->getRequestParameter('qthumb'))) : null;
    $qsource = trim($this->getRequestParameter('qsource')) ? SED::decryption(trim($this->getRequestParameter('qsource'))) : null;
    if(empty($qthumb)){ 
      $response_process['htmlthumb'] = $html_ethubm;
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText(json_encode($response_process));
    }
    //***********************************************************************************************
    if(!file_exists($qthumb)){ 
      $response_process['htmlthumb'] = $html_ethubm;
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText(json_encode($response_process)); 
    }
    //***********************************************************************************************
    $ifile_thumb = pathinfo($qthumb);
    if($ifile_thumb['extension'] != "pdf"){
      $response_process['htmlthumb'] = $html_ethubm;
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText(json_encode($response_process));
    }
    //***********************************************************************************************
    $pdftoppm_dir = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'poppler-24.07.0'.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'bin';
    $tmp_thumb = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR.$qsource;
    $process_arguments = "-l 10 -scale-to 150 -jpeg";
    $web_thumb = '/tmp/thumb/'.$qsource;
    //***********************************************************************************************
    simad_util::createPath($tmp_thumb);
    //***********************************************************************************************
    $mycmd = $pdftoppm_dir.DIRECTORY_SEPARATOR.'pdftoppm '.$process_arguments.' "'.$qthumb.'" "'.$tmp_thumb.DIRECTORY_SEPARATOR.$qsource.'"';
		shell_exec($mycmd);
    //***********************************************************************************************
    $images = glob($tmp_thumb . "/*.".$output_format);    
    $thumb_html = '<div class="mail-attachments"><ul style="list-style: none;padding: 0;margin: 0;background-color: #ddd;">';
    $index = 0;
    foreach($images as $image)
    {
      if($image !== '.' && $image !== '..')
      {
        $thumb_html .= '<li style="border: dashed 1.5px green;margin-bottom: 5px;">';
        $thumb_html .= '<h3 class="previewcom-thumbnail">Pagina '.++$index.'</h3>';
        $thumb_html .= '<a href="#" class="thumb">';
        $thumb_html .= '<img style="max-width: 150px;" class="img-rounded img-responsive" src="'.$web_thumb.'/'.basename($image).'" alt="'.basename($image).'" />';
        $thumb_html .= '</a></li>';
      }
    }
    $thumb_html .= '</ul></div>';
    //***********************************************************************************************
    $response_process['htmlthumb'] = $thumb_html;
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText(json_encode($response_process));
  }


  public function executeViewResp() 
  {
    $comrecibida_id = $this->getRequestParameter('comrecibida_id');
    $displayRespFirst = (null !== $this->getRequestParameter('displayRespFirst')) ? $this->getRequestParameter('displayRespFirst') : null;
    $cant_comenviadas =  ComRecibidaRespuestaPeer::countComEnviadasByComrecibidaId($comrecibida_id);

    if($cant_comenviadas > 0)
    {
      $this->getUser()->setFlash('displayRespuestas', 1);
    }
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id=' . $comrecibida_id . '&displayRespFirst=' . $displayRespFirst);      
  }

  public function executeShow()
  {   	  	  	
  	$currentForm = "com_recibida/show";
    $comrecibida_id = trim($this->getRequestParameter('comrecibida_id')) ? trim($this->getRequestParameter('comrecibida_id')) : null;
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->verificaPrilegioCerrar($currentForm);
  	$com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);
    $this->forward404Unless($com_recibida);
    if (!$this->usuarioTieneAccesoComRecibida($com_recibida, $usuariologuiado)) {
      $this->getUser()->setFlash('messages_error', ConsultaPermisoHelper::MSG_SIN_PERMISOS);
      return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/list');
    }
    //**************************************************************************************************
    $this->ilist_comenviadas =  ComRecibidaRespuestaPeer::getComEnviadasByComrecibidaId($comrecibida_id);
    $this->displayRespuestas = false;
    if ($this->getUser()->hasFlash('displayRespuestas')) 
    {	
      $this->displayRespuestas = true;
    }
    $this->displayRespFirst = (null !== $this->getRequestParameter('displayRespFirst')) ? $this->getRequestParameter('displayRespFirst') : null;
    //*************************************************************************************************
    $O_usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
    $this->qtoken = md5($com_recibida->getPrimaryKey() . $usuariologuiado . $O_usuario->getSalt());
    //*************************************************************************************************
    $stateview = trim($this->getRequestParameter('viewstate'));
    $backid = trim($this->getRequestParameter('backid'));
    $this->dataShared = ContenidoUnidadDocumentalPeer::getFormatSharedComUrlView($backid,$com_recibida->getPrimaryKey(),$stateview,ModulesEnable::ComRecibida);
    //**********************GESTOR BUSCAR DUPLICADOS****************************************************
    if($this->tienePrilegio("COM_RECIBIDA_RESPONDER") && in_array($com_recibida->getTipoprocesocomId(), array(2, 3)) 
          && !in_array($com_recibida->getEstadocomrecibidaId(), array(14,12)))
    {
        $radicados = $com_recibida->buscarDuplicados();
        if(!empty($radicados) && count($radicados) > 0)
        {
            $this->getUser()->setFlash('warning', 'Al parecer esta misma comunicación ya existe');
            $this->radicados_comrecibida = $radicados['radicados_comrecibida'];
            if(!empty($radicados['radicados_comenviada']))
            {
              $this->radicados_comenviada = $radicados['radicados_comenviada'];
            }
        }
    }
    //**************************************************************************************************
    $fullpath = $com_recibida->getPathImageDigitByCom();
    $existe_file = !empty($fullpath) ? true : false;
    if($existe_file){
      $dirRaiz = !empty($com_recibida->getDirDigit()) ? trim($com_recibida->getDirDigit()) : ParametroPeer::retrieveByPk(27)->getValortexto();
      //**********************************************************************************************
      $com_recibida->setEstadodigitalizacionId(2);
          $com_recibida->setFechaDigit(($com_recibida->getFechaDigit() ? $com_recibida->getFechaDigit() : date("Y-m-d G:i:s")));
          $com_recibida->setIsLocked(0);
          $com_recibida->setDirDigit($dirRaiz);
    }else{
      $com_recibida->setEstadodigitalizacionId(1);
    }
    //**************************************************************************************************
    $com_recibida->save();
    //**************************************************************************************************
  	$tempCopia="";
  	$estado_alt = "";
  	$estado_icono = "";
  	$estado_recibida = "";
    $usuario_asignado = "";
  	//**************************************************************************************************
    $user_id = "";
  	$autorizaciones = $this->getAutorizaciones();
  	for($i=0; $i<count($autorizaciones);$i++){
      $user_id .= $autorizaciones[$i];
    }
  	//**************************************************************************************************
    $user_copia = array();
	  foreach ($com_recibida->getComrecibidaUsuarios() as $result) {
		  if($result->getRolusuariorecibidaid() == 3){
			   $user_copia[] = $result->getUsuario()->getNombreAndDependencia();
		  }
      //************************************************************************************************
      if($result->getUsuarioId() == $usuariologuiado || substr_count($user_id,$result->getUsuarioId())){
		    if($result->getEstadocomrecibidaId() == 1 ){ //&& $estado_digitalizacion==2
          $result->setEstadocomrecibidaId(2);
          $result->setFechaLectura(date('Y-m-d G:i:s'));
  	      $result->save();
        }
        $estado_recibida = $result->getEstadocomrecibidaId();
        $estado_icono = $result->getEstadocomrecibida()->getIcono();
        $estado_alt = $result->getEstadocomrecibida()->getDescripcion();
	    }
      //************************************************************************************************
	    if($result->getRolusuariorecibidaid() == 2){
        $estado_recibida = $result->getEstadocomrecibidaId();
        $estado_icono = $result->getEstadocomrecibida()->getIcono();
        $estado_alt = $result->getEstadocomrecibida()->getDescripcion();
        if($result->getEstaAsignada()){ 
          $usuario_asignado = $result->getUsuario()->getNombreAndDependencia();
          $pkusuario_asignado = $result->getUsuarioId(); 
        }
		  }
      //************************************************************************************************
	    if($result->getRolusuariorecibidaid() == 1){
        $this->user_radicador = $result->getUsuario()->getNombreAndDependencia();
		  }
    }
    //*************************************************************************************************
    $this->impuestos_data = false;
    $this->fileExiste = $existe_file;
    $this->estado_alt = $estado_alt;
    $this->estado_icono = $estado_icono;
  	$this->estado_recibida = $estado_recibida;   
    $this->com_recibida = $com_recibida;
    $this->user = $usuario_asignado;
    $this->pkusuario_asignado = $pkusuario_asignado;
    $this->tempCopia = implode(",",$user_copia);
  }
        
  public function executeShowRadicar()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->msg = $this->getRequestParameter('msg');
    $this->usuario = UsuarioPeer::retrieveByPK($this->getRequestParameter('usuario_id'));  	
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    //*************************************************************************************************/
    $fullpath = $com_recibida->getPathImageDigitByCom();
	$existe_file = !empty($fullpath) ? true : false;
    if($existe_file){
		$dirRaiz = !empty($com_recibida->getDirDigit()) ? trim($com_recibida->getDirDigit()) : ParametroPeer::retrieveByPk(27)->getValortexto();
		//**********************************************************************************************
		$com_recibida->setEstadodigitalizacionId(2);
        $com_recibida->setFechaDigit(($com_recibida->getFechaDigit() ? $com_recibida->getFechaDigit() : date("Y-m-d G:i:s")));
        $com_recibida->setIsLocked(0);
        $com_recibida->setDirDigit($dirRaiz);
	}else{
		$com_recibida->setEstadodigitalizacionId(1);
	}
    //**************************************************************************************************/
    $com_recibida->save();
	  //**************************************************************************************************/
    $recibida_usuarios = $com_recibida->getComrecibidaUsuariosJoinUsuario();
    foreach($recibida_usuarios as $userestadocomrecibida){//recorrer la coleccion de estados por usuario
        if($userestadocomrecibida->getRolusuariorecibidaid() == 1){
          $this->userAsig = $userestadocomrecibida->getUsuario();//usuario destino
        }elseif($userestadocomrecibida->getRolusuariorecibidaid() == 2){
          $this->user =  $userestadocomrecibida->getUsuario();//usuario destino
        }
    }
    //**************************************************************************************************/
    $this->com_recibida = $com_recibida;    
    $this->forward404Unless($this->com_recibida);
  }
  
  public function executeChangeInteresado()
  {
    $currentForm = "COM_RECIBIDA_ELIMINAR_INTERESADO";
    $this->verificaPrilegioCerrar($currentForm);
    //**************************************************************************************************
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $list_inteIds = array();
    $list_names = array();
    foreach (ComRecibidaPeer::getListIntersadosByComId($com_recibida->getPrimaryKey()) as $interesado) {
      $list_inteIds[] = $interesado->getInteresados()->getPrimaryKey();
      $list_names[] = trim($interesado->getInteresados()->getNumeroIdentificacion()) . " - " . trim($interesado->getInteresados()->getNombre());
    }
    //**************************************************************************************************
    $this->intesadosIds = implode(",", $list_inteIds);
    $this->intesadosNames = implode(",", $list_names);
    $this->com_recibida = $com_recibida;
    $this->forward404Unless($this->com_recibida);
  }

  public function executeUpdateComInt()
  {
    $currentForm = "COM_RECIBIDA_ELIMINAR_INTERESADO";
    $this->verificaPrilegioCerrar($currentForm);
    //**************************************************************************************************
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $this->forward404Unless($com_recibida);
	$com_recibida_anterior = clone $com_recibida;
    //*******************************************************************************************
    $observaciones = ($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null;
    if (!empty($observaciones)) {
      $obs = $com_recibida->getObservaciones() ? $com_recibida->getObservaciones() . ' | ' . $observaciones : $observaciones;
      $com_recibida->setObservaciones($obs);
      $com_recibida->save();
    }
    //*******************************************************************************************
    $list_interesado = preg_split("/[,]+/", trim($this->getRequestParameter('idUserInteresados')));
    for ($index = 0; $index < count($list_interesado); $index++) {
      ComrecibidaInteresadosPeer::addEditInteresadoByComId($com_recibida->getPrimaryKey(), $list_interesado[$index]);
    }

    if (count($list_interesado)) {
      $isDeleteNotIn = ComrecibidaInteresadosPeer::deleteByComIdNotExist($com_recibida->getPrimaryKey(), $list_interesado);
    }
    //*******************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior, $com_recibida);
    return $this->redirect($this->getRequest()->getScriptName() . '/com_recibida/show?comrecibida_id=' . $com_recibida->getPrimaryKey());
  }

  public function executeModificar()
  {
    $this->cargousuarioId  = "";
  	$c = new Criteria();  	
  	$com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
  	//*******************************************************************************************
  	$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$this->getRequestParameter('comrecibida_id'));
  	$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);  	
	  $usuario      = ComrecibidaUsuarioPeer::doSelect($c);
    //*******************************************************************************************
    $cominteresados_list = ComRecibidaPeer::getListIntersadosByComId($com_recibida->getPrimaryKey());
    foreach ($cominteresados_list as $row) {
      $interesado_comrecibida[] = array('interesado_id' => $row->getInteresadoId(), 
        'interesado_nombre' => $row->getInteresados()->getNombreCustomEmail());
    }
    //*******************************************************************************************
	  $temp = '';
  	foreach ($usuario as $res) {            
      $temp = $res->getUsuarioId();
      $this->cargousuarioId  =  $res->getCargousuarioId();
    }
    //*******************************************************************************************
    $nomb = UsuarioPeer::retrieveByPk($temp);        
    $this->list_interesados = $interesado_comrecibida;
    $this->com_recibida = $com_recibida;
    $this->nomb		= $nomb;
    $this->forward404Unless($this->com_recibida);
  }

  public function executeCreate()  
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $usuario_logueado = UsuarioPeer::retrieveByPK($usuariologuiado);
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    //*********************************************************************************************
  	$currentForm = "com_recibida/create";
    $this->tipo_com = false;
  	$this->verificaPrilegio($currentForm);
    $this->cargousuarioId = "";
    $this->cargousuarioIdCopias = ""; 	 		   		  	
    $this->com_recibida = new ComRecibida();
	  $this->pais = new Pais();
	  $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
	  $this->wfbitacora_id=$this->getRequestParameter('wfbitacora_id');
    //*********************************************************************************************
    if($this->wfinstancia_id >0){
      $this->com_recibida->setAsunto("Comunicacion WF-0". $this->getRequestParameter('generada_tipo'));	
    }
    //*********************************************************************************************
    $this->msg_error = $this->getRequestParameter('msg');
    //*********************************************************************************************
    $this->regionales = RegionalPeer::getAllRegional();
    //*********************************************************************************************     
 	  $this->es_otra_regional = 0;
    $this->regional_select = 0;
    if($this->getUser()->checkPerm("RADICAR_COM_RECIBIDA_OTRA_REGIONAL", $usuariologuiado)){
      $this->es_otra_regional = 1;		
      $this->regional_select = $usuario_logueado->getRegionalId();
    }
    //*********************************************************************************************
    $listallentidad = $this->getUser()->checkPerm('LISTAR_TODAS_REGIONALES', $usuariologuiado);
    //*********************************************************************************************
    //WORFLOW PARA LAS COMUNICACIONES POR TIPO DE RECIBIDA TABLE WFFLUJO
    $buzon_id = 1;//para las comunicaciones recibidas
    $c = new Criteria();
    $c->addJoin(WfFlujoPeer::TIPOCOMRECIBIDA_ID,TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);
    $c->add(WfFlujoPeer::WF_BUZON_ID,1);
    $c->add(WfFlujoPeer::ESTA_ACTIVO,1);
    //*********************************************************************************************
    if(!$listallentidad){
      //$c->add(TipoComRecibidaPeer::ENTIDAD_ID,$usuario_logueado->getRegional()->getEntidadId());
    }
    //*********************************************************************************************
    $c->addAscendingOrderByColumn(WfFlujoPeer::DESCRIPCION);
    $this->wf_tipocom = WfFlujoPeer::doSelect($c);
    //*********************************************************************************************
    if($this->wf_tipocom == null){
        $this->tipo_com = true;
    }
    //*********************************************************************************************
    //trigger regionales destino
    $app_cfg = simad_util::readConfigFileApp(array('distribuidor_regional_area_pk'));
    $this->valorActivador = $app_cfg['distribuidor_regional_area_pk'];
    //*********************************************************************************************
	  $this->setTemplate('edit');	    
  }

  public function executeEdit()
  {
    $this->cargousuarioId = "";
    $this->cargousuarioIdCopias = "";
  	$c = new Criteria();
    $this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $pais = PaisPeer::doSelect($c);
    $this->pais = $pais;
    $this->forward404Unless($this->com_recibida);
    
    $reg = new Criteria();
    $reg->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);
    $this->regionales = RegionalPeer::doSelect($reg);
    
    
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');    
    $this->es_otra_regional=0;
	if($this->getUser()->checkPerm("RADICAR_COM_RECIBIDA_OTRA_REGIONAL", $usuariologuiado)){
	   $this->es_otra_regional=1;		
	   $this->regional_select=$this->com_recibida->getRegionalId();
	}    
  }   
  
  public function executeHistorial()
  {   
  		$nombAsig = array();
  		$user_Asigno = array();
    	$this->com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));		    			
		$c = new Criteria();
		$c->add(ComRecibidaPeer::CODIGO_REEN_RESP, $this->com_recibida->getCodigoReenResp());				
		$this->parametros .= "&comrecibida_id=" . $this->getRequestParameter('comrecibida_id');		
        
        //$c->addAscendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
		$pager = new sfPropelPager('ComRecibida', 10);
        $pager->setCriteria($c);        
        $pager->setPage($this->getRequestParameter('page', 1));
        $pager->init();
        $this->pager = $pager;
		/********************************Consulta para Usuario Asignado*******************************************/
		foreach($pager->getResults() as $hist_recibida){
			$a = new Criteria();		
			$a->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $hist_recibida->getPrimaryKey());		
			$a->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
			$result = ComrecibidaUsuarioPeer::doSelectOne($a);					            			
	        $user_Asignado[] = $result->getUsuario();
	    }
	    /*****************************Consulta para Usuario Asigno***********************************************/
	    foreach($pager->getResults() as $hist_recibida){
			$a = new Criteria();		
			$a->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $hist_recibida->getPrimaryKey());		
			$a->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 1);
			$result = ComrecibidaUsuarioPeer::doSelectOne($a);					            			
	        $user_Asigno[] = $result->getUsuario();			       			              		         			
	    }
	    $this->user_Asignado = $user_Asignado;
	    $this->user_Asigno = $user_Asigno;
		/*************************************************************************************************/                		  	   
  }
    
  public function executeFile()
  {
  	    
  } 
  
  public function executeMezclarPdf()
  {
  	$dirRaiz = ParametroPeer::retrieveByPk(17);
  	$formato = ParametroPeer::retrieveByPk(31);
  	$work_dir = $dirRaiz->getValortexto();
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $fileOrigen = $work_dir.$com_recibida->getRadicado().$formato->getValortexto();
    
	$pdf = new merge_pdf();
	$pdf->getNumeroPaginas($fileOrigen,$work_dir."temp.txt",$work_dir);
	
	$vlineas = file($work_dir."temp.txt");
    
    /* Podemos mostrar / trabajar con todas las l�neas:*/
    foreach ($vlineas as $sLinea){
      $tempLinea = explode(":",$sLinea);
        if($tempLinea[0] == "NumberOfPages")
           $numero_paginas = $tempLinea[1];
    }     	
	/*****************************************************/
	unlink($work_dir."temp.txt");
	
	$this->numero_paginas = $numero_paginas; 
	$this->com_recibida = $com_recibida; 
	$this->fileOrigen =$fileOrigen;      
  }
  
  public function executeUploadMezclarPdf()
  {  	
  	if($submit_boton = $_REQUEST["boton"] == 'Adjuntar'){//para saber de q boton se envio el submit
  		
	  	$dirRaiz = ParametroPeer::retrieveByPk(17)->getValortexto();//directorio donde se guardan las digitalizaciones					
		  //Definir la posicion
        $position = 1;//posicion inicial para la adicion del nuevo pdf
      
      if($this->getRequestParameter('posicion')){//verrificar si se adicionara al principio  o al final del pdf
        $position = $this->getRequestParameter('posicion');//se asigna la seleccion a la variable
      }elseif($this->getRequestParameter('pagina_posicion')){//para saber si se digito una pagina
        $position = $this->getRequestParameter('pagina_posicion');//se asigna el numero de la pagina ala variable
      }	
        
      if ($this->getRequest()->hasFiles())//se verifica q vengan archivos para subir al servidor  	
      {
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);		
        $directorio = simad_util::createPath($dirRaiz);//se verifica o se crea el directorio donde se encuentran los adjuntos
        @move_uploaded_file($file['tmp_name'], $directorio.DIRECTORY_SEPARATOR.$fileName);//se mueve el archivo al directorio del servidor
      }
      //**********************************************************************************
      $outputfile = $dirRaiz.$this->getRequestParameter('fileOrigen');//Archivo al cual se le va adicionar		 
      $uploadFile = $dirRaiz.$fileName;//archivo q se va adicinara
      $numero_paginas = $this->getRequestParameter('numero_paginas');//se obtiene el numero total de paginas
      //**********************************************************************************
      $merge_pdf = new merge_pdf();//se instancia la clase q realzara la adicion de los pdf
      $file_temp = $merge_pdf->create_command('pdftk',$uploadFile,$outputfile,$position,$numero_paginas);//se hace el llamado a funcion.
      //**********************************************************************************
      $error = '';
      unlink($uploadFile);//se elimina el archivo q se adiciono
      if(file_exists($dirRaiz.'temp'.$file_temp.'.pdf')){
          unlink($outputfile);//se elimina el archivo al cual se le va adicionar
          rename($dirRaiz.'temp'.$file_temp.'.pdf',$outputfile);//se renombra el archivo temporal con el nombre original
      }else{
        unlink($dirRaiz.'temp'.$file_temp.'.pdf');//se elimina el archivo al cual se le va adicionar
        $error = 'Error al tratar De abrir el Archivo';
      }
      //**********************************************************************************
      $this->fileOrigen = $this->getRequestParameter('fileOrigen');//se envia el nombre del archivo original a la vista
      $this->error = $error;				
    }elseif($submit_boton = $_REQUEST["boton"] == 'Eliminar'){//if para saber si viene por el eliminar
      return $this->forward('com_recibida', 'deletePagePdf');//se pasa el control a otra accion del clase
    }//fin el else 
  }//fin de la accion
  
  public function executeDeletePagePdf()
  {
  	$numero_pagina = trim($this->getRequestParameter('pagina_posicion'));	    	
  	$total_paginas = trim($this->getRequestParameter('numero_paginas'));
	  $msg = '';$outputfile = '';
    if($numero_pagina){
  		$dirRaiz = ParametroPeer::retrieveByPk(17);
  		$formato = ParametroPeer::retrieveByPk(31);
  		$work_dir = $dirRaiz->getValortexto();
    	$com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    	$outputfile = $com_recibida->getRadicado().$formato->getValortexto();
	   	$pdf = new merge_pdf();
	   	$file_temp = $pdf->deletePage($work_dir.$outputfile,$numero_pagina,$work_dir,$total_paginas);	   				   
      unlink($work_dir.$outputfile);
      rename($dirRaiz->getValortexto().'temp'.$file_temp.'.pdf',$work_dir.$outputfile);
      $msg = 'Se ha Eliminado Satisfactoriamente la Pagina '.$numero_pagina.' Del Archivo '.$outputfile;		
    }else{
      $msg = 'Debe Digitar Una Pagina Para Eliminar Del Archivo '.$outputfile;
    }
    //*****************************************************************************
	  $this->msg = $msg;		
	  $this->com_recibida = $com_recibida;       
  }
  
  public function handleErrorUploadMezclarPdf()
  {
  	$this->forward('com_recibida', 'mezclarPdf');
  }
  
  public function executePrintBatchFile()
  {
    require_once sfConfig::get('sf_lib_dir') . '/ZipTools/autoload.php';
    //*********************************************************************************************
    $this->verificaPrilegio("COM_RECIBIDA_DIGIT_DESCARGA_MASIVA");
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*********************************************************************************************
    $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
	  $digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
    $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    //*********************************************************************************************
    $c = new Criteria();
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);    
    $list_com = ComRecibidaPeer::doSelect($c);
    //*********************************************************************************************
    $zipFileName = md5(date("YmdGis")).".zip";
    $pathzip = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp";
    //*********************************************************************************************
    if(file_exists($pathzip.DIRECTORY_SEPARATOR.$zipFileName)) {        
      unlink ($pathzip.DIRECTORY_SEPARATOR.$zipFileName);
    }
    //*********************************************************************************************
    $path = "{$pathzip}/{$zipFileName}";
    $stream = fopen($path, 'w');

    $zip = new ZipStream($zipFileName, array(
			ZipStream::OPTION_OUTPUT_STREAM => $stream
		));

    $zip->opt['ContentType'] ='application/octet-stream';        
    //*********************************************************************************************
    try {
      foreach($list_com as $com_object){
        $dir_raiz = !empty($com_object->getDirDigit()) ? empty($com_object->getDirDigit()) : $dir_raiz;
        $storage_com = $com_object->getBasicUrlDigitCom($dir_raiz,$digit_dir);
        //*******************************************************************************************
        foreach ($mimetypes as $format) {
          $filename = sprintf("%s.%s",trim($com_object->getRadicado()),$format);
          $filename_digit = $storage_com['storage_path'].DIRECTORY_SEPARATOR.$filename;
          if(file_exists($filename_digit)){
            //$zip->addFile($filename_digit,$filename);
            $zip->addFileFromPath($filename, $filename_digit);
          }
        }
      }
    } catch (\Exception $ex) {
      $data_array = array('status'=>200,'message' =>$ex->getMessage(), 'url_file' => null);
    }
    //*********************************************************************************************
    $zip->finish();
		fclose($stream);
    //*********************************************************************************************
    if(file_exists($pathzip)){
      $data_array = array('status'=>200,'message' =>'Archivo zip generado', 'url_file' => '/tmp/'.$zipFileName);
    }else{
      $data_array = array('status'=>400,'message' =>'Ocurrio un error al generar el archivo', 'url_file' => null);
    }
    //*********************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $data_json = json_encode($data_array);
    return $this->renderText($data_json);
  }
  
  public function executePrintBatchStream()
  {
    require_once sfConfig::get('sf_lib_dir') . '/ZipTools/autoload.php';
    //*********************************************************************************************
    $this->verificaPrilegio("COM_RECIBIDA_DIGIT_DESCARGA_MASIVA");
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*********************************************************************************************
    $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
	$digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
    $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    //*********************************************************************************************
    $c = new Criteria();
	$c->setLimit(100);
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);    
    $list_com = ComRecibidaPeer::doSelect($c);
    //*********************************************************************************************
    $zipFileName = md5(date("YmdGis")).".zip";
    $pathzip = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp";
    //*********************************************************************************************
    if(file_exists($pathzip.DIRECTORY_SEPARATOR.$zipFileName)) {        
      unlink ($pathzip.DIRECTORY_SEPARATOR.$zipFileName);
    }
    //*********************************************************************************************
    $zip = new ZipStream($zipFileName, array(
			'content_type' => 'application/octet-stream'
		));
    //*********************************************************************************************
    try {
      foreach($list_com as $com_object){
        $dir_raiz = !empty($com_object->getDirDigit()) ? empty($com_object->getDirDigit()) : $dir_raiz;
        $storage_com = $com_object->getBasicUrlDigitCom($dir_raiz,$digit_dir);
        //*****************************************************************************************
        foreach ($mimetypes as $format) {
          $filename = sprintf("%s.%s",trim($com_object->getRadicado()),$format);
          $filename_digit = $storage_com['storage_path'].DIRECTORY_SEPARATOR.$filename;
          if(file_exists($filename_digit)){
            if ($streamRead = fopen($filename_digit, 'r')) {
              $zip->addFileFromStream($filename_digit, $streamRead);
              fclose($streamRead);
            }
          }
        }
      }
    } catch (\Exception $ex) {
      
    }
    //********************************************************************************************
    $zip->finish();
  }
  
  public function executePrintBatch()
  { 
  	$this->verificaPrilegio("COM_RECIBIDA_DIGIT_DESCARGA_MASIVA");
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*********************************************************************************************
    $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
	  $digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
    $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    //*********************************************************************************************
    $c = new Criteria();
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);    
    $list_com = ComRecibidaPeer::doSelect($c);
    //*********************************************************************************************
    $zip = new ZipArchive();
    $zipFileName = md5(date("YmdGis")).".zip";
    $pathzip = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.$zipFileName;
    //*********************************************************************************************
    if(file_exists($pathzip)) {        
      unlink ($pathzip);
    }
    //*********************************************************************************************
    if ($zip->open($pathzip, ZIPARCHIVE::CREATE) != TRUE) {
            die ("Could not open archive");
    }
    //*********************************************************************************************
    try {
      foreach($list_com as $com_object){
        $dir_raiz = !empty($com_object->getDirDigit()) ? empty($com_object->getDirDigit()) : $dir_raiz;
        $storage_com = $com_object->getBasicUrlDigitCom($dir_raiz,$digit_dir);
        //*******************************************************************************************
        foreach ($mimetypes as $format) {
          $filename = sprintf("%s.%s",trim($com_object->getRadicado()),$format);
          $filename_digit = $storage_com['storage_path'].DIRECTORY_SEPARATOR.$filename;
          if(file_exists($filename_digit)){
            $zip->addFile($filename_digit,$filename);
          }
        }
      }
    } catch (\Exception $ex) {
      $data_array = array('status'=>200,'message' =>$ex->getMessage(), 'url_file' => null);
    }
    //*********************************************************************************************
    $zip->close();
    //*********************************************************************************************
    if(file_exists($pathzip)){
      $data_array = array('status'=>200,'message' =>'Archivo zip generado', 'url_file' => '/tmp/'.$zipFileName);
    }else{
      $data_array = array('status'=>400,'message' =>'Ocurrio un error al generar el archivo', 'url_file' => null);
    }
    //*********************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $data_json = json_encode($data_array);
    return $this->renderText($data_json);
  }
  
  public function executeEnvioMasivo()
  {
    $currentForm = "COM_RECIBIDA_DISTRIBUCION_MASIVA";
  	$this->verificaPrilegioCerrar($currentForm);
    $this->tipoprocesos_com = TipoProcesoComPeer::getTipoProcesoComById(3);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //******************************************************************************
    $c = new Criteria();
    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
    $this->count_marcadas = ComRecibidaPeer::doCount($c);
    //******************************************************************************
    $this->esProcesoMasivo = true;
  }

  public function executeDigitalizar()
  {
    $currentForm = "RECIBIDA_ADJUNTAR_DIGITALIZACION";
  	$this->verificaPrilegioCerrar($currentForm);
    $this->esRadicadoMasivo = false;
    //******************************************************************************
	  $this->comrecibida_id = $this->getRequestParameter('comrecibida_id');
  }  

  public function executeDigitalizarMasivo()
  {
    $currentForm = "ADJUNTAR_DIGITALIZACION_MASIVA";
  	$this->verificaPrilegioCerrar($currentForm);
    //******************************************************************************
    $this->esRadicadoMasivo = true;
	  $this->setTemplate('digitalizar');
  }
  
  public function executeFileDigitalizarMasivo()
  {
    $currentForm = "ADJUNTAR_DIGITALIZACION_MASIVA";
  	$this->verificaPrilegioCerrar($currentForm);
    $usuariologuiado = $this->getUser()->getAttribute('username', '', 'subscriber');
    //**********************************************************************************************************
    //CREANDO ESTRUCTURA DE DIRECTORIOS
    $dirRaiz   = ParametroPeer::retrieveByPk(27)->getValortexto();
    $dir_alias  = ParametroPeer::retrieveByPk(14)->getValortexto();
    $extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
	  $fileName = "";$isDuplicate = false;
	  $this->message_stamp = "";
    $msgerror = array();
    //**********************************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file){
      $file_vars = pathinfo($file['name']);
      if(!in_array($file_vars['extension'], $extensions)){
        $msgerror[] = "El archivo no esta permitido";
        continue;
      }
      //********************************************************************************************************
	  $tmpdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
      $tmpfile = $tmpdir.DIRECTORY_SEPARATOR.uniqid().'.'.$file_vars['extension'];
	  //********************************************************************************************************
      $isCopyFile = @move_uploaded_file($file['tmp_name'], $tmpfile);      
      if(!$isCopyFile){
        $msgerror[] = "Ocurrio un error con el archivo no se pudo copiar";
        continue;
      }
      //********************************************************************************************************
      $comrecibida_list = ComRecibidaPeer::getComObjectByMarcados(date('Y'));
      if(count($comrecibida_list) <= 0){
        $this->forward('com_recibida','digitalizarMasivo');
      }
      //********************************************************************************************************
      foreach($comrecibida_list as $com_recibida) {
        $nomFile = trim($com_recibida->getRadicado());
        //******************************************************************************************************
        if(!empty(trim($com_recibida->getDirDigit()))){
          $dirRaiz = trim($com_recibida->getDirDigit());
        }
        //******************************************************************************************************
        $entidad_text = trim($com_recibida->getRegional()->getEntidad()->getDirectorioName());
        $regional_text = trim($com_recibida->getRegional()->getDirectorioName());
        $entidad_text = $entidad_text ? $entidad_text : "";
        $entidad_text = $entidad_text ? ($regional_text ? $entidad_text.DIRECTORY_SEPARATOR.$regional_text : $entidad_text) : "";
        $periodo = $com_recibida->getPeriodoId();
        $directorio_entidad = $dirRaiz . $entidad_text.'/';
        $directorio_entidad .= $dir_alias.'/'.$periodo;
        //******************************************************************************************************
        $directorio_entidad = simad_util::NormalizePath($directorio_entidad);
        $directorio = simad_util::createPath($directorio_entidad);
        //******************************************************************************************************
        $file_name =  $nomFile.'.'.$file_vars['extension']; 
        $fullpath = $directorio.DIRECTORY_SEPARATOR.$file_name;
        //****************************************************************************************************
        if(file_exists($fullpath)){ unlink($fullpath); }
        //****************************************************************************************************
        if(file_exists($tmpfile)){
          $fileName = $file_name;
          $this->message_stamp = 'El documento fue estampado exitosamente';
          //**************************************************************************************************
          if($com_recibida->getFormaRecepcion()->getStampSticker()){
            $file_sticker = $com_recibida->stampStickerToDigit($tmpfile,true);
          }else{
			$file_sticker = $tmpdir.DIRECTORY_SEPARATOR.md5(uniqid().date("Y-m-d G:i:s")).'.'.$file_vars['extension'];
			if(!copy($tmpfile, $file_sticker)){
				$this->message_stamp = '';
				$msgerror[] = "Ocurrio un error con el archivo y no pudo adjuntar a los radicados";
				return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/digitAttach?msgerror='.implode(";",$msgerror).'&message_stamp='.$this->message_stamp);
			}
          }
          //**************************************************************************************************
		  if(file_exists($file_sticker)){
              $response_ws = $com_recibida->stampDocumentProcess($file_sticker);
              if(!$response_ws['error']){
                $unique_file = $tmpdir.DIRECTORY_SEPARATOR.uniqid().'.pdf';
                $isFileCreate = $response_ws['file_base64'] != null ? simad_util::getConvertB64ToFile($response_ws['file_base64'],$unique_file) : rename($file_sticker,$unique_file);
                if($isFileCreate){
                  unlink($file_sticker);
                  rename($unique_file,$fullpath);
                }else{
                  rename($file_sticker,$fullpath);
                }
              }else{
				        rename($file_sticker, $fullpath);
              	$this->message_stamp = 'Ocurrio un error al estampar algunos o todos los documentos, sin embargo los archivos se adjuntaron a los radicados';
                $msgerror[] = $response_ws['msg_info'];
              }
            }
          }
          //************************************************************************************************
          if(file_exists($fullpath)){
            if (in_array($com_recibida->getTipoprocesocomId(),array(1,3)) && ($com_recibida->getIsLocked() != 1)){
              $tipoproceso_destino = $com_recibida->getTipoprocesocomId() == 1 ? 2 : 3;
              //****************************************************************************************
              $com_recibida->setFechaDigit(($com_recibida->getFechaDigit() ? $com_recibida->getFechaDigit() : date("Y-m-d G:i:s")));
              $com_recibida->setIsLocked(0);
              $com_recibida->setTipoprocesocomId($tipoproceso_destino);
              $com_recibida->setEstadocomrecibidaId(1);
              $com_recibida->setDirDigit($dirRaiz);
              $com_recibida->save();
              //****************************************************************************************
              ComRecibidaPeer::updateEstadosComByEstado($com_recibida->getPrimaryKey(), 1, array(2,3));
              ComRecibidaPeer::updateFechaAsignaCom($com_recibida->getPrimaryKey(),array(2));
              //****************************************************************************************
              $usuarios_mail = $com_recibida->getUserIdComRecibidaRol(2);
              //$encabezado_email = "Este es un mensaje para informarle que se ha adjuntado un documento electr&oacute;nico a la siguiente comunicaci&oacute;n: ";
              $encabezado_email = "Este es un mensaje para informarle que se ha asignado la siguiente comunicaci&oacute;n: ";
              ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuarios_mail,$encabezado_email,false,false);
            }
          }else{
            $msgerror[] = "Ocurrio un error al adjuntar el archivo, radicado ".trim($com_recibida->getRadicado());
          }
      }
    }
    //************************************************************************************************************
    //$this->msgerror = $msgerror;
    //$this->setTemplate('fileDigitalizar');    
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/digitAttach?msgerror='.implode(";",$msgerror).'&message_stamp='.$this->message_stamp);
  }

  public function executeDigitAttach()
  {
    $this->fileName = $this->getRequestParameter('fileName') ? $this->getRequestParameter('fileName') : null;
    $this->message_stamp = $this->getRequestParameter('message_stamp') ? $this->getRequestParameter('message_stamp') : null;
    $this->msgerror = $this->getRequestParameter('msgerror') ? $this->getRequestParameter('msgerror') : null;
	$this->setTemplate('fileDigitalizar');
  }
  
  public function executeFileDigitalizar()
  {
    $currentForm = "RECIBIDA_ADJUNTAR_DIGITALIZACION";
    $this->verificaPrilegioCerrar($currentForm);
    //********************************************************************************************************
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $com_recibida_anterior = clone $com_recibida;
    //********************************************************************************************************
    //CREANDO ESTRUCTURA DE DIRECTORIOS
    $dirRaiz   = ParametroPeer::retrieveByPk(27)->getValortexto();
    $dir_alias  = ParametroPeer::retrieveByPk(14)->getValortexto();
    $extensions = explode(";", ParametroPeer::retrieveByPK(31)->getValortexto());
    $this->fileName = "";
    $this->message_stamp = "";
    //********************************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file) {
      $usuariologuiado = $this->getUser()->getAttribute('username', '', 'subscriber');
      $file_vars = pathinfo($file['name']);
      $nomFile   =  ($com_recibida->getRadicado());
      $tmpfdir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
      $tmpfname = $tmpfdir . DIRECTORY_SEPARATOR . md5(date("YmdGis") . uniqid()) . '.' . $file_vars['extension'];
      //******************************************************************************************************
      if(!empty(trim($com_recibida->getDirDigit()))){
        $dirRaiz = trim($com_recibida->getDirDigit());
      }
      //******************************************************************************************************
      $entidad_text = trim($com_recibida->getRegional()->getEntidad()->getDirectorioName());
      $regional_text = trim($com_recibida->getRegional()->getDirectorioName());
      $entidad_text = $entidad_text ? $entidad_text : "";
      $entidad_text = $entidad_text ? ($regional_text ? $entidad_text . DIRECTORY_SEPARATOR . $regional_text : $entidad_text) : "";
      $periodo = $com_recibida->getPeriodoId();
      $directorio_entidad = $dirRaiz . $entidad_text . '/';
      $directorio_entidad .= $dir_alias . '/' . $periodo;
      //******************************************************************************************************
      $directorio_entidad = simad_util::NormalizePath($directorio_entidad);
      $directorio = simad_util::createPath($directorio_entidad);
      //******************************************************************************************************
      if (in_array($file_vars['extension'], $extensions)) {
        $file_name =  $nomFile . '.' . $file_vars['extension'];
        $fullpath = $directorio . DIRECTORY_SEPARATOR . $file_name;
        $tmpupload = $directorio . DIRECTORY_SEPARATOR . $file_name;
        //****************************************************************************************************
        if(file_exists($fullpath)){ unlink($fullpath); }
        //****************************************************************************************************
        @move_uploaded_file($file['tmp_name'], $tmpfname);
        //****************************************************************************************************
        if (file_exists($tmpfname)) 
        {
          $com_recibida->saveNewComrecibidaDuplicado($tmpfname);
          //**************************************************************************************************
          $this->fileName = $file_name;
          $this->message_stamp = 'El documento fue estampado exitosamente';
          //**************************************************************************************************
          if (strtolower($file_vars['extension']) == 'pdf') {
            if ($com_recibida->getFormaRecepcion()->getStampSticker()) {
              $file_sticker = $com_recibida->stampStickerToDigit($tmpfname, true);
            } else {
              $file_sticker = $tmpfname;
            }
            //************************************************************************************************
            if (file_exists($file_sticker)) {
              $response_ws = $com_recibida->stampDocumentProcess($file_sticker);
              if (!$response_ws['error']) {
                $unique_file = $tmpfdir . DIRECTORY_SEPARATOR . uniqid() . '.pdf';
                $isFileCreate = simad_util::getConvertB64ToFile($response_ws['file_base64'], $unique_file);
                if ($isFileCreate) {
                  unlink($file_sticker);
                  if(copy($unique_file,$fullpath)){ unlink($unique_file); }
                } else {
                  unlink($fullpath);
                  copy($file_sticker, $fullpath);
                }
              } else {
                $ms101 = 'Ocurrio un error con el documento, el archivo se adjunto al radicado, pero no se puede incluir la estampa de tiempo';
                $this->msgerror = sprintf("%s, %s", $ms101, $response_ws['msg_info']);
                $this->message_stamp = 'El archivo se adjunto al radicado, pero no se generó la estampa de tiempo';
                if(copy($file_sticker,$fullpath)){ unlink($file_sticker); }
              }
            } else {
              copy($tmpfname, $fullpath);
              $this->msgerror = "Ocurrio un error con el documento, el archivo se adjunto al radicado, pero no se puede incluir la estampa de tiempo";
            }
          } else {
            if(copy($tmpfname,$fullpath)){ unlink($tmpfname); }
          }
          //******************************************************************************************************
          if (file_exists($fullpath)) {
            $com_recibida->setDirDigit($dirRaiz);
            $com_recibida->setNewStateByDigitCom();
            //automatizacion 1 FUNCION UNA SOLA LINEA DE CODIGO, QUE SE LE ENVIEN PARAMETROS
          } elseif (copy($tmpfname, $fullpath)) {
            $com_recibida->setDirDigit($dirRaiz);
            $com_recibida->setNewStateByDigitCom();
            $this->msgerror = "El archivo se adjunto al radicado, pero no se creó la estampa de tiempo";
            //automatizacion 1 FUNCION UNA SOLA LINEA DE CODIGO, QUE SE LE ENVIEN PARAMETROS
          } else {
            $this->msgerror = "Ocurrio un error al adjuntar el archivo, por favor intente de nuevo";
          }
        } else {
          $this->msgerror = "Ocurrio un error subiendo el archivo al servidor, por favor intente de nuevo";
        }
      } else {
        $file_name =  null;
        $this->msgerror = "El archivo no esta permitido, envia un archivo valido";
      }
      //**********************************************************************************************************
      AuditLogPeer::guardarAuditoriaLite('ComRecibida', $com_recibida_anterior, $com_recibida, ModulesEnable::ComRecibida, $com_recibida->getRadicado());
    }
    //************************************************************************************************************
    if (file_exists($tmpfname)) {
      unlink($tmpfname);
    }
    //************************************************************************************************************
    return $this->redirect($this->getRequest()->getScriptName() . '/com_recibida/digitAttach?fileName=' . $this->fileName . '&msgerror=' . $this->msgerror . '&message_stamp=' . $this->message_stamp);
  }
  
  public function executeDzFileUpload()
  {
    $data_array = array();
  	if (!empty($_FILES))
    {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');        
      $dirRaiz = ParametroPeer::retrieveByPk(27)->getValortexto();
      $dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
      //***************************************************************************************
      $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
      $entidad_text = trim($usuario->getRegional()->getEntidad()->getDirectorioName());
      $entidad_text = $entidad_text ? $entidad_text.DIRECTORY_SEPARATOR : "";
      $cons    = $this->getNewConsecutivo();
      //***************************************************************************************
      $directorio_entidad = $dirRaiz.$entidad_text;
      $directorio_tmp = $directorio_entidad.$dirTmp.DIRECTORY_SEPARATOR;    	
      //***************************************************************************************
      $file_vars = pathinfo($_FILES['file']['name']);
      $util_simad = new simad_util();
      $fileName = $util_simad->clean_name_file($file_vars);
      //***************************************************************************************
      $tempFile = $_FILES['file']['tmp_name'];
      $directorio = simad_util::createPath($directorio_tmp);
      $newfile = $cons.'_'.$fileName;
      //***************************************************************************************
      @move_uploaded_file($tempFile,$directorio.DIRECTORY_SEPARATOR.$newfile);
      $data_array['name'] = $newfile;
      //***************************************************************************************
      $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
      if($comrecibida_id){
        $com_recibida = ComRecibidaPeer::retrieveByPK($comrecibida_id);
        $strfiles = $com_recibida->getRutaAdjuntos($newfile,$usuariologuiado);
        //*************************************************************************************
        if(trim($com_recibida->getRuta())){
          $currentfiles = preg_split("/[,]+/",trim($com_recibida->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
          $currentfiles[] = $strfiles;
          $com_recibida->setRuta(implode(",",$currentfiles));
        }else{
          $com_recibida->setRuta($strfiles);
        }
        //*************************************************************************************
        $com_recibida->save();
      }
      //***************************************************************************************
      $this->getResponse()->setContentType('application/json');
      $data_json = json_encode($data_array);
      return $this->renderText($data_json);
    }else{
      $this->getResponse()->setContentType('application/json');
      $data_json = json_encode($data_array);
      return $this->renderText($data_json);
    }        
  }
  
  public function executeUploads()
  {
    //*************************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(27)->getValortexto();
	$dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
	//*************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file)	
    {
        //*********************************************************************************
    	$directorio_entidad = $dirRaiz.$entidad_text.DIRECTORY_SEPARATOR;
    	$directorio_tmp = $directorio_entidad.$dirTmp.DIRECTORY_SEPARATOR;    	
        //*********************************************************************************
		$cons    = $this->getNewConsecutivo();
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $directorio = simad_util::createPath($directorio_tmp);
        //*********************************************************************************
        @move_uploaded_file($file['tmp_name'], $directorio.DIRECTORY_SEPARATOR.$cons.'_'.$fileName);
        //*********************************************************************************
        $this->fileName = $cons.'_'.$fileName;
    }
  }
  
  public function executeUpdateDevolver()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $currentForm = "COM_RECIBIDA_DEVOLVER_RADICADO";
    $this->verificaPrilegioCerrar($currentForm);
    //************************************************************************************************************
    $usuario_asignado = trim($this->getRequestParameter('idUserAsig'));
    $usuario_asignar  = trim($this->getRequestParameter('idUser'));
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
    $estadocomrecibida_id = 10;
    $observaciones_remitir = trim($this->getRequestParameter('observaciones_remitir'));
    //************************************************************************************************************
    $com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);  	
    $com_recibida_anterior = clone $com_recibida;
    //************************************************************************************************************
    $tipoprocesocomId = 6;
    if($com_recibida->getTipoprocesocomId() == 3){
      $tipoprocesocomId = 2;
    }
    //************************************************************************************************************
    if(trim($com_recibida->getObsRemitir())){
      $observaciones_remitir = trim($com_recibida->getObsRemitir()). ' | ' . $observaciones_remitir;
    }
    //************************************************************************************************************
    $com_recibida->setObsRemitir($observaciones_remitir);
    $com_recibida->setTipoprocesocomId($tipoprocesocomId);
    $com_recibida->save();
    //************************************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior, $com_recibida);
    //************************************************************************************************************
    ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(),2,0);
    ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$usuario_asignar,$cargousuarioid,1,2,1,$tipoprocesocomId);
    //**************************************SEND EMAIL **********************************************************
    $encabezado_email = "Este es un mensaje para informarle que se le ha devuelto una comunicación externa recibida y puede consultarla en ARCHIDHU con la siguiente información: ";
    ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuario_asignar,$encabezado_email);
    /******************************************************************************************************/
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$comrecibida_id);
  }

  public function executeUpdateRemitir()
  {
    $currentForm = "RECIBIDA_REMITIR_COMUNICACION";
  	$this->verificaPrilegioCerrar($currentForm);
    //************************************************************************************************************
  	//$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$usuario_asignado = $this->getRequestParameter('idUserAsig');
  	$usuario_asignar  = trim($this->getRequestParameter('idUser'));
  	$cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $comrecibida_id = trim($this->getRequestParameter('comrecibida_id'));
    $estadocomrecibida_id = 10;
    //************************************************************************************************************
  	$com_recibida = ComRecibidaPeer::retrieveByPk($comrecibida_id);  	
  	$com_recibida_copia = $com_recibida->copy();
	//************************************************************************************************************
	$com_recibida_anterior = clone $com_recibida;
	$com_recibida_copia_anterior = clone $com_recibida_copia;
    //************************************************************************************************************
    $com_recibida_copia->setObsRemitir($this->getRequestParameter('observaciones_remitir'));
    $com_recibida_copia->setCodigoReenResp($com_recibida->getCodigoReenResp());
    $com_recibida_copia->setFechaCreacion(date("Y-m-d G:i:s"));
    $com_recibida_copia->save();
    //************************************************************************************************************
  	$c  =  new Criteria();
  	$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$comrecibida_id);
  	//$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
  	//$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
  	//$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuario_asignado);
  	$resp = ComrecibidaUsuarioPeer::doSelect($c);
  	//***********************************************************************************************************
  	foreach ($resp as $cambAsig) {
      $cambAsig->setEstadocomrecibidaId($estadocomrecibida_id);
      $cambAsig->setEstaAsignada(false);
      $cambAsig->save();
    }        
    //***********************************************************************************************************
    $com_recibida->setEstadocomrecibidaId($estadocomrecibida_id);
    $com_recibida->save();
    //***********************************************************************************************************
	$this->guardarAuditoria($com_recibida_anterior,$com_recibida);
	$this->guardarAuditoria($com_recibida_copia_anterior,$com_recibida_copia);
	//***********************************************************************************************************
	  $this->asignarUser($com_recibida_copia->getPrimaryKey(),$usuario_asignar,$cargousuarioid);
	  //**************************************SEND EMAIL **********************************************************
    $encabezado_email = "Este es un mensaje para informarle que se le ha remitido una comunicacion externa recibida y puede consultarla en ARCHIDHU con los siguientes datos: ";
    ComRecibidaPeer::envioEmail($com_recibida_copia->getPrimaryKey(),$usuario_asignar,$encabezado_email);
    /******************************************************************************************************/
  	$this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$comrecibida_id);
  }
  
  public function executeUpdateAsignarArea()
  {
    $currentForm = "COM_RECIBIDA_ASIGNAR_DISTRIBUIDOR";
  	$this->verificaPrilegioCerrar($currentForm);
    //******************************************************************************************************
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $newuser = trim($this->getRequestParameter('idUser'));
	//******************************************************************************************************
    $com_recibida_anterior = clone $com_recibida;
	//******************************************************************************************************
    $CodigoReenResp = $com_recibida->getCodigoReenResp() ? $com_recibida->getCodigoReenResp() : $com_recibida->getPrimaryKey();
    $observaciones = trim($this->getRequestParameter('detalles'));
    $tipoprocesocomId = 2;
  	//******************************************************************************************************
    if(trim($com_recibida->getObsReenResp())){
      $observaciones = trim($com_recibida->getObsReenResp()). ' | ' . $observaciones;
    }
    //******************************************************************************************************
    $com_recibida->setObsReenResp($observaciones);
    $com_recibida->setCodigoReenResp($CodigoReenResp);
    $com_recibida->setTipoprocesocomId($tipoprocesocomId);
	$com_recibida->save();
	//******************************************************************************************************
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $estadocomrecibida_id = 1;//estado por defecto de la comunicacion
    //******************************************************************************************************
    ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(),2,0);
    ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$newuser,$cargousuarioid,$estadocomrecibida_id,2,1,$tipoprocesocomId);
	  /******************************************* SEND EMAIL ***********************************************/
    $encabezado_email = "Este es un mensaje para informarle que se le ha asignado una comunicación externa recibida y la puede consultar en ARCHIDHU con la siguiente información: ";
    ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$newuser,$encabezado_email);
    /******************************************************************************************************/
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getPrimaryKey());
  }

  public function executeUpdateBatchProcess()
  {
    $currentForm = "COM_RECIBIDA_ASIGNAR_GESTOR";
    $this->verificaPrilegioCerrar($currentForm);
    //******************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $newuser = trim($this->getRequestParameter('usuario_asignado'));
    $tipoprocesocom_id = 3;
  	//******************************************************************************************************
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $estadocomrecibida_id = 1;//estado por defecto de la comunicacion
    $observaciones = ($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null;
    //******************************************************************************************************
    $h = new Criteria();    
    //$h->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    //$h->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
    //$h->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    $h->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,2);
    $h->add(ComRecibidaPeer::IS_LOCKED,0);
    //$h->add(ComRecibidaPeer::PERIODO_ID,date("Y"))
    $h->add(ComRecibidaPeer::MARCA,$usuariologuiado);
    $list_distribuir = ComRecibidaPeer::doSelect($h);
    //******************************************************************************************************
    $isGenerateError = false;
    foreach ($list_distribuir as $com_recibida) {
      try {
        if(!empty($observaciones)){
          $obs = $com_recibida->getObsReenResp() ? $com_recibida->getObsReenResp().' | '.$observaciones : $observaciones;
          $com_recibida->setObsReenResp($obs);
        }
        //**************************************************************************************************
        $com_recibida->setTipoprocesocomId($tipoprocesocom_id);
        $com_recibida->save();
        //**************************************************************************************************
        ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(),2,0);
        ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$newuser,$cargousuarioid,$estadocomrecibida_id,2,1,$tipoprocesocom_id);
      } catch (\Throwable $th) {
        //throw $th;
        $isGenerateError = true;
      }
    }
	  /******************************************* SEND EMAIL ***********************************************/
    //$usuarios_mail = $this->getRequestParameter('usuario_asignado');
    //$encabezado_email = "Este es un mensaje para informarle que se le han asignado comunicaciones externas recibidas y las puede consultar con la siguiente informaci&oacte;n: ";
    //ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$newuser,$encabezado_email);
    /******************************************************************************************************/
    //$this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/asignarBatch?isError='.$isGenerateError);
  }

  public function executeAsignarBatch()
  {
    $this->isError = trim($this->getRequestParameter('isError'));
  }

  public function executeUpdateAsignarGestor()
  {
    $currentForm = "COM_RECIBIDA_ASIGNAR_GESTOR";
  	$this->verificaPrilegioCerrar($currentForm);
    //******************************************************************************************************
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $newuser = trim($this->getRequestParameter('idUser'));
    //******************************************************************************************************
    $com_recibida_anterior = clone $com_recibida;
	  //******************************************************************************************************
    $CodigoReenResp = $com_recibida->getCodigoReenResp() ? $com_recibida->getCodigoReenResp() : $com_recibida->getPrimaryKey();
    $tipoprocesocomId = 3;
  	//******************************************************************************************************
    $com_recibida->setObsReenResp($this->getRequestParameter('detalles'));
    $com_recibida->setCodigoReenResp($CodigoReenResp);
    $com_recibida->setTipoprocesocomId($tipoprocesocomId);
    $com_recibida->save();
	  //******************************************************************************************************
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $estadocomrecibida_id = 1;//estado por defecto de la comunicacion
    //******************************************************************************************************
    ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(),2,0);
    ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$newuser,$cargousuarioid,$estadocomrecibida_id,2,1,$tipoprocesocomId);
	  //******************************************************************************************************
    $usuarios_mail = $this->getRequestParameter('idUser');
    $encabezado_email = "Este es un mensaje para informarle que se le ha asignado una comunicación externa recibida y la puede consultar en ARCHIDHU con la siguiente información: ";
    ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$newuser,$encabezado_email);
    //******************************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    //******************************************************************************************************
    //Automatización de expedientes
    $com_recibida->addExpedienteAutoByReglas();
    //******************************************************************************************************
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getPrimaryKey());
  }

  public function executeUpdateReenviar()
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	//******************************************************************************************************
  	$newRadicado  = ParametroPeer::retrieveByPK(16); 	
  	$com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
  	$com_recibida_anterior = clone $com_recibida;
	//******************************************************************************************************
  	$recibida_reenvio = $com_recibida->copy();
	$recibida_reenvio_anterior = clone $recibida_reenvio;
  	//******************************************************************************************************
  	$recibida_reenvio->setEstadocomrecibidaId(1);
  	$recibida_reenvio->setFechaCreacion(date("Y-m-d G:i:s"));
    if($newRadicado->getCodigo()=='SI'){
		  $recibida_reenvio->setRadicado($this->executeRadicar($this->getRequestParameter('paraUser')));
	  }else{
		  $recibida_reenvio->setRadicado($com_recibida->getRadicado());
	  }
    $recibida_reenvio->setObsReenResp($this->getRequestParameter('detalles'));
    $recibida_reenvio->setCodigoReenResp($com_recibida->getCodigoReenResp());
  	//$recibida_reenvio->setFechaRespuestaReen($this->getRequestParameter('fechaMaxima'));
	  $recibida_reenvio->save();
	  //******************************************************************************************************
    $cargousuarioid = $this->getRequestParameter('cargousuarioId');
    $estadocomrecibida_id = 1;//estado por defecto de la comunicacion
    //if para validar si la comunicacion requiere respuesta    
    if($this->getRequestParameter('requiere_respuesta')?'1':'0'){
	   $estadocomrecibida_id = 6;//6 es el estado de com recibida por responder
    }
	//******************************************************************************************************
    $this->asignarUser($recibida_reenvio->getComrecibidaId(),trim($this->getRequestParameter('idUser')),$cargousuarioid,$estadocomrecibida_id);
	  //$com_recibida->setCodigoReenResp($this->getRequestParameter('comrecibida_id'));
	//******************************************************************************************************
	  if($com_recibida->getCodigoReenResp() == ''){
	    $com_recibida->setCodigoReenResp($this->getRequestParameter('comrecibida_id'));	
	  }else{
	    $com_recibida->setCodigoReenResp($com_recibida->getCodigoReenResp());	
	  }
  	$com_recibida->setEstadocomrecibidaId(7);	
    $com_recibida->save();
    //******************************************************************************************************
    $usuarios_autorizados = $this->getAutorizaciones();
    $usuarios_autorizados[] = $usuariologuiado;
    //******************************************************************************************************
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$com_recibida->getPrimaryKey());
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuarios_autorizados,Criteria::IN);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
    //$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);//para cambiar estado solo para usuario logueado
    $resp = ComrecibidaUsuarioPeer::doSelect($c);
    foreach($resp as $result){        
		  $this->updateEstadoCom($result,7);        
	  }//end foreach
	//******************************************* SEND EMAIL ***********************************************
    $usuarios_mail = $this->getRequestParameter('idUser');
    $encabezado_email = "Este es un mensaje para informarle que se le ha Reenviado una comunicacion externa recibida y la puede consultar en ARCHIDHU con los siguientes datos: ";
    ComRecibidaPeer::envioEmail($recibida_reenvio->getComrecibidaId(),$usuarios_mail,$encabezado_email);
    //******************************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
	$this->guardarAuditoria($recibida_reenvio_anterior, $recibida_reenvio);
	//******************************************************************************************************
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getComrecibidaId());
  }
  
  public function updateEstadoCom(ComrecibidaUsuario $comrecibidausuario,$estado_id)
  {
    $comrecibidausuario->setEstadocomrecibidaId($estado_id);
	  $comrecibidausuario->save();
  }
  
  public function executeUpdateAsignar()
  {
    $currentForm = "RECIBIDA_EDITAR_COMUNICACION";
    $this->verificaPrilegioCerrar($currentForm);
    //*************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*************************************************************************************************
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId')) ? trim($this->getRequestParameter('cargousuarioId')) : null;
    $usuarioasignar_id = trim($this->getRequestParameter('idUser')) ? trim($this->getRequestParameter('idUser')) : null;
	//*************************************************************************************************
  	$com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
  	$com_recibida_anterior = clone $com_recibida;
	//*************************************************************************************************
    $tipoprocesocomId = 2;
    $dependencia_id = $com_recibida->getDependenciaId();
	  $directorioexterno_id = $this->getRequestParameter('directorioexterno_id') ? trim($this->getRequestParameter('directorioexterno_id')) : null;
	  $fecha_recibido = !empty($this->getRequestParameter('fecha_recibido')) ? trim($this->getRequestParameter('fecha_recibido')) : null;
    //*************************************************************************************************
    if(!empty($usuarioasignar_id)){
		  $usuario_asignar = UsuarioPeer::retrieveByPk($usuarioasignar_id);
		  $dependencia_id = $usuario_asignar != null ? $usuario_asignar->getDependenciaId() : $dependencia_id;
    }
    //*************************************************************************************************
    $com_recibida->setFechaRecibido($fecha_recibido);   
  	$com_recibida->setRadicadoOrigen(trim($this->getRequestParameter('radicado_origen')));
  	$com_recibida->setTipocomrecibidaId(trim($this->getRequestParameter('tipo_com_recibida_id')));
	  $com_recibida->setFormarecepcionId(trim($this->getRequestParameter('formarecepcion_id')));
    $com_recibida->setDependenciaId($dependencia_id);
  	$com_recibida->setObservaciones(trim($this->getRequestParameter('observaciones')));
  	$com_recibida->setAsunto(trim($this->getRequestParameter('asunto')));
  	$com_recibida->setFolios(trim($this->getRequestParameter('folios')));
  	$com_recibida->setAnexos(trim($this->getRequestParameter('anexos')));
	  $com_recibida->setDirectorioexternoId($directorioexterno_id);
	  $com_recibida->setNumeroFud(trim($this->getRequestParameter('numero_fud')));
	  $com_recibida->setNumeroProceso(trim($this->getRequestParameter('numero_proceso')));
    $com_recibida->setTipoprocesocomId($tipoprocesocomId);
    $com_recibida->save();
    //*************************************************************************************************
    $list_interesado = preg_split("/[,]+/", trim($this->getRequestParameter('idUserInteresados')), -1, PREG_SPLIT_NO_EMPTY);
    //******************************************************************************************************
    $isAddInteresado = false;$listcurrent_intereados = array();
    for ($idx = 0; $idx < count($list_interesado); $idx++) {      
      $isAddInteresado = ComrecibidaInteresadosPeer::addEditInteresadoByComId($com_recibida->getPrimaryKey(), $list_interesado[$idx]);
      $listcurrent_intereados[] = $list_interesado[$idx];
    }
    //******************************************************************************************************
    if (count($listcurrent_intereados)) {//elimina los interesados que ya no hacen parte de la comunicacion
      $isDeleteNotIn = ComrecibidaInteresadosPeer::deleteByComIdNotExist($com_recibida->getPrimaryKey(), $listcurrent_intereados);
    }
    //*************************************************************************************************
	  if(!empty($usuarioasignar_id)){
      ComRecibidaPeer::updateAsignadoCom($com_recibida->getPrimaryKey(),2,0);
      ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$usuarioasignar_id,$cargousuarioid,1,2,1,$tipoprocesocomId);
      //**************************************SEND EMAIL ************************************************
      $encabezado_email = "Este es un mensaje para informarle que se le asignó; una comunicación externa recibida y puede consultarla en ARCHIDHU con la siguiente información: ";
      ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuarioasignar_id,$encabezado_email);
	  }
    //*************************************************************************************************
    $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
    $this->redirect($this->getRequest()->getScriptName().'/com_recibida/show?comrecibida_id='.$com_recibida->getComrecibidaId());
  }
  
  public function executeRegionalesDest()
  {
    $json_data = array('status' => 400, 'message' => 'Error interno del servidor');
    //**************************************************************************************
    try{
      $text_select = '<label for="lbregdestino" class="col-sm-1 control-label">Regional Destino<span class="ctrlreq">(*)</span>:</label>';
      $text_select .= '<div class="col-sm-5">';
      $text_select .= '<select id="regional_destino_id" name="regional_destino_id" class="form-control input-sm required">';
      $text_select .= '<option value="" selected>Seleccione...</option>';
      $lista_regionales = RegionalPeer::getAllRegional(true);
      //************************************************************************************
      if(empty($lista_regionales) || count($lista_regionales) <= 0)
      {
        $json_data = array('status' => 400, 'message' => 'Error, el listado de registros esta vacío, intente de nuevo');
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($json_data));
      }
      //************************************************************************************
      foreach ($lista_regionales as $regional) 
      {
          $text_select .= '<option value="'.$regional->getPrimaryKey().'">'.$regional->getDescripcion().'</option>';
      }
      $text_select .= '</select></div>';
      //************************************************************************************
      $json_data = array('status' => 200, 'message' => 'registros encontrados', 'text_select' => $text_select);
    }catch(PropelException $ex){
      $json_data = array('status' => 400, 'message' => 'Error interno de acceso a los datos');
    }catch(\Exception $ex){
      $json_data = array('status' => 400, 'message' => 'Error interno de la aplicacion');
    }catch(\Throwable $ex){
      $json_data = array('status' => 400, 'message' => 'Error interno del servidor');
    }
    //***************************************************************************************
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($json_data));
  }

  public function executeUpdate()
  {
    $currentForm = "com_recibida/create";
  	$this->verificaPrilegio($currentForm);
    //******************************************************************************************
    if (!$this->getRequestParameter('comrecibida_id')){
      $com_recibida = new ComRecibida();
    }else{
      $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
      $this->forward404Unless($com_recibida);
    } 
    //******************************************************************************************
    $com_recibida_anterior = clone $com_recibida;
    //******************************************************************************************
  	/* begin wf */
  	$this->wfinstancia_id =$this->getRequestParameter('wfinstancia_id');
  	$this->wfbitacora_id =$this->getRequestParameter('wfbitacora_id');
  	if($this->wfinstancia_id>0){ $com_recibida->setInstancia($this->wfinstancia_id); }
  	if($this->wfbitacora_id>0){ $com_recibida->setBitacora($this->wfbitacora_id); }
    /* end   wf */
    //******************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //******************************************************************************************
    $reg = $this->getRegional($usuariologuiado);
    if($this->getRequestParameter('regional_id') != ""){
        $regional_id = $this->getRequestParameter('regional_id');
    }else{
        $regional_id = $reg[0];
    }
    $com_recibida->setRegionalId($regional_id);
    //******************************************************************************************
    $usuario_destino = $this->getRequestParameter('idUser') ? trim($this->getRequestParameter('idUser')) : null;
    $cusuario_destino = $this->getRequestParameter('cargousuarioId') ? preg_split("/[,]+/", trim($this->getRequestParameter('cargousuarioId'))) : null;
    if($usuario_destino != null){
      $dependencia_id = $this->getDependenciaByUser($usuario_destino);
    }else{
      $dependencia_id = trim($this->getRequestParameter('dependencia_id'));
    }
    //*****************************PARA MANEJO DEl TIPO COM RECIBIDA****************************
    //por defecto un tipo de coumunicacion recibida
    $parametro_tipocomrecibida = ParametroPeer::retrieveByPK(63);
    $tipocomrecibida_id = $parametro_tipocomrecibida->getValorNumerico();
    $estadocomrecibida_id = $this->getRequestParameter('estadocomrecibida_id') ? $this->getRequestParameter('estadocomrecibida_id') : 11;
    //******************************************************************************************
    if($this->getRequestParameter('wf_tipo_com_recibida_id')){
        $tipocomrecibida_id = $this->getRequestParameter('wf_tipo_com_recibida_id');
    }elseif($this->getRequestParameter('tipo_com_recibida_id')){
        $tipocomrecibida_id = $this->getRequestParameter('tipo_com_recibida_id');
    }
    //******************************************************************************************
    $list_dirs = preg_split("/[,]+/",trim($this->getRequestParameter('directorioexterno_id')), -1, PREG_SPLIT_NO_EMPTY);
    $directorioexterno_id = null;
    if(count($list_dirs) > 0){ $directorioexterno_id = $list_dirs[0]; }
    //******************************************************************************************
    $com_recibida->setTipocomrecibidaId($tipocomrecibida_id);
    $com_recibida->setPeriodoId(date("Y"));
    $com_recibida->setDependenciaId($dependencia_id);//dependencia destino
    $com_recibida->setEstadodigitalizacionId($this->getRequestParameter('estadodigitalizacion_id') ? $this->getRequestParameter('estadodigitalizacion_id') : 1);
    $com_recibida->setEstadocomrecibidaId($estadocomrecibida_id);
    $com_recibida->setFormarecepcionId($this->getRequestParameter('formarecepcion_id') ? $this->getRequestParameter('formarecepcion_id') : null);   
    $com_recibida->setMediorespuestaId($this->getRequestParameter('mediorespuesta_id') ? $this->getRequestParameter('mediorespuesta_id') : null);
    $com_recibida->setAsuntorecibidaId($this->getRequestParameter('asuntorecibida_id') ? $this->getRequestParameter('asuntorecibida_id') : 1);
    $com_recibida->setTipoexpedienteId($this->getRequestParameter('tipoexpediente_id') ? $this->getRequestParameter('tipoexpediente_id') : null);
    $com_recibida->setTipoprocesocomId($this->getRequestParameter('tipoprocesocom_id') ? $this->getRequestParameter('tipoprocesocom_id') : 1);
    $com_recibida->setCiudadId(RegionalPeer::getCiudadIdByRegional($reg[0]));	
    $com_recibida->setDirectorioexternoId($directorioexterno_id);
    $com_recibida->setPrioridadcomId($this->getRequestParameter('prioridadcom_id') ? $this->getRequestParameter('prioridadcom_id') : null);
    $com_recibida->setRadicadoOrigen(trim($this->getRequestParameter('radicado_origen')));
    $com_recibida->setFechaCreacion(date("Y-m-d G:i:s"));
    $com_recibida->setAsunto(trim($this->getRequestParameter('asunto')));
    $com_recibida->setNumeroFud(trim($this->getRequestParameter('numero_fud')) ? trim($this->getRequestParameter('numero_fud')) : null);
    $com_recibida->setNumeroProceso(trim($this->getRequestParameter('numero_proceso')) ? trim($this->getRequestParameter('numero_proceso')) : null);
    $com_recibida->setEsCopia(trim($this->getRequestParameter('idUserCopia')) ? 1 : 0);
    $com_recibida->setGuia(trim($this->getRequestParameter('guia')));
    $com_recibida->setEmpresaMensajeriaId($this->getRequestParameter('empresamensajeria_id') ? $this->getRequestParameter('empresamensajeria_id') : null);
    $com_recibida->setValorGuia($this->getRequestParameter('valor_guia'));    
    $com_recibida->setDestinoComrecibida(trim($this->getRequestParameter('destino_recibida')));
    $com_recibida->setFolios($this->getRequestParameter('folios'));
    //******************************************************************************************
    $observaciones = trim($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null;
    $regional_destino_id = trim($this->getRequestParameter('regional_destino_id')) ? trim($this->getRequestParameter('regional_destino_id')) : null;
    if(!empty($regional_destino_id))
    {
        $regional_descripcion = RegionalPeer::retrieveByPK($regional_destino_id)->getDescripcion();
        $observaciones = $observaciones . " | Redireccionado para distribuidor: " . $regional_descripcion;
    }
    //******************************************************************************************
    $com_recibida->setObservaciones(strtoupper($observaciones));
    $com_recibida->setAnexos(strtoupper(trim($this->getRequestParameter('anexos'))));
    $com_recibida->setObsReenResp(strtoupper(trim($this->getRequestParameter('detalles'))));    
    $com_recibida->setEstaentregado(0);
    $com_recibida->setObsAnulacion(strtoupper(trim($this->getRequestParameter('obs_anulacion'))));
    $com_recibida->setMarca(0);
    $com_recibida->setIsLocked(1);    
    //******************************************************************************************
    $fecha_recibido = !empty($this->getRequestParameter('fecha_recibido')) ? trim($this->getRequestParameter('fecha_recibido')) : null;
    $fecha_vencimiento = !empty($this->getRequestParameter('fecha_maxima_respuesta')) ? trim($this->getRequestParameter('fecha_maxima_respuesta')) : null;
    $fecha_respuesta = null;
    if(!empty($fecha_vencimiento)){
      $fecha_respuesta = ComRecibidaPeer::getFechaMaxRespByTipoCom($tipocomrecibida_id,$fecha_vencimiento);
    }elseif(!empty($fecha_recibido)){
      $fecha_respuesta = ComRecibidaPeer::getFechaMaxRespByCustomFechaEntrada($tipocomrecibida_id,$fecha_recibido);
    }else{
      $fecha_respuesta = ComRecibidaPeer::getFechaMaxRespByTipoCom($tipocomrecibida_id);
    }
    //******************************************************************************************
    $com_recibida->setFechaMaximaRespuesta($fecha_respuesta);
    $com_recibida->setFechaRecibido($fecha_recibido);    
    //******************************************************************************************
    $com_recibida->save();
    //*******************************************************************************************v
    $tipoproceso_rad = 1;
    $cusuario_radicador = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariologuiado);
    $isAddUserRad = ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(), $usuariologuiado, $cusuario_radicador, 1, 1, 0, $tipoproceso_rad);
    //*******************************************************************************************
    $tipoproceso_dest = 2;
    $addDestinoUser = ComRecibidaPeer::addDestinoCom($com_recibida,$tipoproceso_dest,$usuario_destino,$cusuario_destino, $regional_destino_id);
    //*******************************************************************************************
    if(!$addDestinoUser){
      $this->deleteCascada($com_recibida->getPrimaryKey());
      $this->getRequest()->setError("Error Configuración","Se deben configurar los distribuidores para la dependencia y el tramite seleccionado");
      $this->com_recibida = $com_recibida;
      $this->forward('com_recibida', 'create');
    }    
    //******************************************************************************************
    if(!empty($com_recibida->getTipocomrecibidaId())){
      if($com_recibida->getTipoComRecibida()->getTipodistribucionId() == 2){
        $tipoproceso_dest = 3;
        $addUserGestor = ComRecibidaPeer::addDestinoCom($com_recibida,$tipoproceso_dest,$usuario_destino,$cusuario_destino);
        if($addUserGestor){
          $com_recibida->setTipoprocesocomId($tipoproceso_dest);
          $com_recibida->save();
          //************************************************************************************
          $tipoproceso_origen = 2;
          ComRecibidaPeer::updateAsignadoComAuto($com_recibida->getPrimaryKey(),1,$tipoproceso_origen,2,0);
        }else{
          $this->getUser()->setFlash('messages_error', 'El tramite esta configurado con distribucion automatica, sin embargo no se encontro el usuario gestor para asignar el radicado');
        }
      }
    }
    //******************************************************************************************
    $com_recibida->setCodigoReenResp($com_recibida->getPrimaryKey());
    $entidad_id = $com_recibida->getRegional()->getEntidadId();
    //******************************************************************************************
    if(trim($this->getRequestParameter('radicado'))){
      $com_recibida->setRadicado(trim($this->getRequestParameter('radicado')));	
    }elseif($entidad_id == 2){
      $radicado_compose = $com_recibida->getRadicadoFormatByTipoCom($com_recibida->getRegionalId(),$com_recibida->getDependenciaId(),null,false);
      $com_recibida->setRadicado($radicado_compose);
    }else{
      $radicado_compose = $com_recibida->getRadicadoFormat($com_recibida->getRegionalId(),$com_recibida->getDependenciaId(),null,false);
      $com_recibida->setRadicado($radicado_compose);
      //$com_recibida->save();
    }
    //******************************************************************************************
    if (!empty(trim($this->getRequestParameter('archivo')))) {
      if (!$this->getRequestParameter('comrecibida_id')){
        $ruta = $com_recibida->getRutaAdjuntos(trim($this->getRequestParameter('archivo')),$usuariologuiado,false);
      }else{
        $ruta = $com_recibida->getRutaAdjuntos(trim($this->getRequestParameter('archivo')),$usuariologuiado,true,$com_recibida->getRuta());
      }
      $com_recibida->setRuta($ruta);
    }else{
      $com_recibida->setRuta(null);
    }
    //*******************************************************************************************
    $com_recibida->save();
    //*******************************************************************************************
    if(trim($this->getRequestParameter('idUserCopia'))){
      $user_copia = preg_split("/[,]+/", trim($this->getRequestParameter('idUserCopia')));
      $causer_copia = preg_split("/[,]+/", trim($this->getRequestParameter('cargousuarioIdCopias')));
      for($x=0; $x < count($user_copia); $x++) {
        ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$user_copia[$x],$causer_copia[$x],11,3,0);
      }
    }
    //*******************************************************************************************
    $radicarByInt = trim($this->getRequestParameter(md5('radByIntzx1'))) ? $this->getRequestParameter(md5('radByIntzx1')) : 0;
    $isAddInteresado = false;
    $list_interesado = preg_split("/[,]+/", trim($this->getRequestParameter('idUserInteresados')), -1, PREG_SPLIT_NO_EMPTY);
    $radicado_com = "";$mailErrorMsg = array();
    for($index = 0; $index < count($list_interesado); $index++) {
      $interesado_obj = InteresadosPeer::retrieveByPK($list_interesado[$index]);
      if(!$radicarByInt){
        $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(),$list_interesado[$index]);
        $radicado_com = $com_recibida->getRadicado();
      }elseif($index == 0){
        $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(),$list_interesado[$index]);
        $radicado_com = $com_recibida->getRadicado();
      }else{
        $com_recibida_new = $com_recibida->copy();
        $com_recibida_new->save();
        //***************************************************************************************
        $radicado_compose = $com_recibida_new->getRadicadoFormat($com_recibida_new->getRegionalId(),$com_recibida_new->getDependenciaId(),0,false);
        $com_recibida_new->setRadicado($radicado_compose);
        $com_recibida_new->setCodigoReenResp($com_recibida_new->getPrimaryKey());
        $com_recibida_new->save();
        $radicado_com = $radicado_compose;
        //***************************************************************************************
        foreach ($com_recibida->getComrecibidaUsuarios() as $current_ucom) {
          $newucomrecibida = $current_ucom->copy();
          $newucomrecibida->setComrecibidaId($com_recibida_new->getPrimaryKey());
          $newucomrecibida->setFechaAsigna(date("Y-m-d G:i:s"));
          $newucomrecibida->save();
        }
        //***************************************************************************************
        $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida_new->getPrimaryKey(),$list_interesado[$index]);
      }
      //*****************************************************************************************
      if($interesado_obj != null){ 
        $response_mail = $interesado_obj->envioEmailNotificacion($radicado_com); 
        if($response_mail['IsSend'] === false){
          $mailErrorMsg[] = $response_mail['message'];
        }
      }
      //*****************************************************************************************
      if ($com_recibida->getDirectorioexternoId()) {
        $response_mail = $com_recibida->getDirectorioExterno()->envioEmailNotificacion($radicado_com);
        if ($response_mail['IsSend'] === false) {
          $mailErrorMsg[] = $response_mail['message'];
        }
      }
    }
	//*********************************************************************************************
    $qurl = count($mailErrorMsg) ? '&msgmail='.implode(",",$mailErrorMsg) : '';
    //*******************************************************************************************
  	if($isAddInteresado || $com_recibida->getDirectorioexternoId()){
  	  //if(!$this->getRequestParameter('radicado')){ //ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuario_destino); }
      //*****************************************************************************************
  	  if(trim($this->getRequestParameter('idUserCopia'))){
         //$this->asignarUserCopia($com_recibida->getPrimaryKey(),trim($this->getRequestParameter('idUserCopia')),$this->getRequestParameter('cargousuarioIdCopias'));
         //Envio email a los usuarios copia de la comunicacion
         $alerta_usuario_copias = array();
         $alerta_usuario_copias = preg_split("/[,]+/", trim($this->getRequestParameter('idUserCopia')));
         $encabezado_cuerpo_copia = "Este es un mensaje para informarle que se le ha generado una copia de la siguiente comunicacion entrante:";
         foreach($alerta_usuario_copias as $user_copia_alerta){
           if(trim($user_copia_alerta)) {
            ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$user_copia_alerta,$encabezado_cuerpo_copia);
           }
         }
  	  }
      //*******************************************************************************************
      $this->guardarAuditoria($com_recibida_anterior,$com_recibida);
      //*******************************************************************************************
      //$tipo_com_recibida = TipoComRecibidaPeer::retrieveByPK($com_recibida->getTipocomrecibidaId());
      //$isMasivoResp = ComRecibidaPeer::initUserEncargadoCom($com_recibida,$tipo_com_recibida);
      //*******************************************************************************************
      //if(!$isMasivoResp){
        //$usuarioWF = $usuario_destino;
        //ComRecibidaPeer::initWorkflowCom($com_recibida,$usuarioWF,$usuariologuiado);
      //}      
      //*******************************************************************************************
      $redir = $this->verRedirect($com_recibida->getPrimaryKey(),$usuario_destino);
      //*******************************************************************************************
      if( $redir != null){
          $cargousuario_id = CargoUsuarioPeer::getCargoUsuarioByIdUser($redir);
          $this->asignarUserCopia($com_recibida->getPrimaryKey(),$redir,$cargousuario_id);
          $body = "Este es un mensaje para informarle que se le ha radicado una Comunicaci&oacute;n Externa Recibida - Copia por cuenta redireccionada :";
          ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$redir,$body);
          return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/showRadicar?comrecibida_id='.$com_recibida->getPrimaryKey().'&msg=1&usuario_id='.$redir.$qurl);	
      }else{
          return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/showRadicar?comrecibida_id='.$com_recibida->getPrimaryKey().'&msg=0'.$qurl);	
      }
  	}else{
  	  $this->deleteCascada($com_recibida->getPrimaryKey());	
  	  return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/create?msg=!Error imposible radicar por favor intenta de nuevo');	
  	}
  }


  public function deleteCascada($comrecibida_id)
  {
  	$conexion = Propel::getConnection();
  	$query01 = "DELETE FROM %s WHERE %s = ".$comrecibida_id;
	  $sql01      = sprintf($query01, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    $sentencia = $conexion->prepare($sql01);
    $sentencia->execute();
    //****************************************************************************************************
  	$conexion = Propel::getConnection();
  	$query02 = "DELETE FROM %s WHERE %s = ".$comrecibida_id;
	  $sql02      = sprintf($query02, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::COMRECIBIDA_ID);
    $sentencia = $conexion->prepare($sql02);
    $sentencia->execute();
  }
  
  public function executeDelete()
  {
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));

    $this->forward404Unless($com_recibida);

    $com_recibida->delete();	
	
    return $this->redirect($this->getRequest()->getScriptName().'/com_recibida/list');
  }
  
  public function executeSticker()
  { 
    $com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
    $this->com_recibida = $com_recibida;
    //**********************************************************************************************
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $this->getRequestParameter('comrecibida_id'));
    $resp = $this->objEnviadaUsuario = ComrecibidaUsuarioPeer::doSelectJoinUsuario($c);        
    $firma=0;
    //**********************************************************************************************
    foreach($resp as $res){		
      if($res->getRolusuariorecibidaid() == 1){
        //$this->radicador = $res->getUsuarioId();
        $this->radicador_name = $res->getUsuario()->getIniciales();
      }	elseif($res->getRolusuariorecibidaid()== 2){
        $this->destinatarioId .= $res->getUsuarioId().",";
        $this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
      }elseif($res->getRolusuariorecibidaid()== 3){			
        $this->firma = $firma;
        $this->copiaInternaId .= $res->getUsuarioId().",";
        $this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";       
      }
    }
    //**********************************************************************************************
    $c=new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $this->getRequestParameter('comrecibida_id'));
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 3);
    $this->resultCopias = ComrecibidaUsuarioPeer::doSelect($c);
    //**********************************************************************************************           
    $this->setLayout(false);
  }

  public function executeSobre()
  { 
  	$com_recibida = ComRecibidaPeer::retrieveByPk($this->getRequestParameter('comrecibida_id'));
  	$this->com_recibida = $com_recibida;
    $c=new Criteria();
	$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $this->getRequestParameter('comrecibida_id'));
    $resp = $this->objEnviadaUsuario = ComrecibidaUsuarioPeer::doSelect($c);        
	$firma=0;
	foreach($resp as $res){		
		if($res->getRolusuariorecibidaid() == 1){
			//$firma = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			//$this->creador = $res->getUsuarioId();
			//$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			
		}	elseif($res->getRolusuariorecibidaid()== 2){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
					
		}elseif($res->getRolusuariorecibidaid()== 3){			
			    $this->firma = $firma;
				$this->copiaInternaId .= $res->getUsuarioId().",";
			    $this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";       
		}		
	}
	
	$c=new Criteria();
	$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $this->getRequestParameter('comrecibida_id'));
	$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 3);
    $this->resultCopias = ComrecibidaUsuarioPeer::doSelect($c);
            
	//$this->setLayout(false);
  }

  
  /**
   * Replica, para un registro puntual, la misma jerarquia de permisos que getCriteriaBasic()
   * aplica a la lista (LISTAR_TODAS > TODA_ENTIDAD > TODAS_REGIONALES > dueno/autorizado/copia),
   * para distinguir "no existen registros" de "existe pero sin permiso" en executeShow().
   */
  private function usuarioTieneAccesoComRecibida(ComRecibida $com_recibida, $usuario_id)
  {
    if ($this->getUser()->checkPerm('COM_RECIBIDA_LISTAR_TODAS', $usuario_id)) {
      return true;
    }
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    //*********************************************************************************
    if ($this->getUser()->checkPerm('LISTAR_COM_RECIBIDA_TODA_ENTIDAD', $usuario_id)) {
      $regional = RegionalPeer::retrieveByPk($com_recibida->getRegionalId());

      return $regional && $regional->getEntidadId() == $entidad_conectado;
    }
    if ($this->getUser()->checkPerm('LISTAR_COM_RECIBIDA_TODAS_REGIONALES', $usuario_id)) {
      return $com_recibida->getRegionalId() == $regional_conectado;
    }
    //*********************************************************************************
    $arrIds = $this->getAutorizaciones();
    $arrIds[] = $usuario_id;
    $c = new Criteria();
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $com_recibida->getPrimaryKey());
    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID, $arrIds, Criteria::IN);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, array(1, 2, 3), Criteria::IN);

    return ComrecibidaUsuarioPeer::doCount($c) > 0;
  }

  private function getCriteriaBasic(Criteria $c)
  {
      $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
      $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      /****************************************************************************************/
      $consulta_buzones = false;
      $consulta_usuarios = false;
      $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      /****************************************************************************************/
    	if ($this->getRequestParameter('radicado')) {
    	    $c->add(ComRecibidaPeer::RADICADO, '%' . $this->getRequestParameter('radicado') .'%', Criteria::LIKE);
    	    $this->parametros .= "&radicado=" . $this->getRequestParameter('radicado');
    	}
    	/**************************************************************************************/ 
    	if ($this->getRequestParameter('destino_recibida')) {
    	    $c->add(ComRecibidaPeer::DESTINO_COMRECIBIDA, '%' . $this->getRequestParameter('destino_recibida') .'%', Criteria::LIKE);
    	    $this->parametros .= "&destino_recibida=" . $this->getRequestParameter('destino_recibida');
    	}
    	/**************************************************************************************/ 
    	if ($this->getRequestParameter('asunto')) {
        $asunto_text = trim($this->getRequestParameter('asunto'));
        $asunto_mb = iconv(mb_detect_encoding($asunto_text, mb_detect_order(), true), "UTF-8//IGNORE", $asunto_text);	
        $c->add(ComRecibidaPeer::ASUNTO, '%' . $asunto_mb . '%', Criteria::LIKE);
        $this->parametros .= "&asunto=" . $asunto_text;
    	}
    	/**************************************************************************************/
      if ($this->getRequestParameter('observaciones')) {
        $observaciones_text = trim($this->getRequestParameter('observaciones'));
        $observaciones_mb = iconv(mb_detect_encoding($observaciones_text, mb_detect_order(), true), "UTF-8//IGNORE", $observaciones_text);
        $c->add(ComRecibidaPeer::OBSERVACIONES, '%' . $observaciones_mb . '%', Criteria::LIKE);
        $this->parametros .= "&observaciones=" . $observaciones_text;
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('entidad_origen')) {	    		
        $entidad_origen_text = trim($this->getRequestParameter('entidad_origen'));
        $entidad_origen_mb = iconv(mb_detect_encoding($entidad_origen_text, mb_detect_order(), true), "UTF-8//IGNORE", $entidad_origen_text);
        $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
        $c->add(DirectorioExternoPeer::NOMBRE, '%' . $entidad_origen_mb . '%', Criteria::LIKE);
        $this->parametros .= "&entidad_origen=" . $entidad_origen_text;
    	}
    	/****************************************************************************************/
    	if ($this->getRequestParameter('nit_origen')) {
    		$c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
    		$c->add(DirectorioExternoPeer::NIT,'%'.$this->getRequestParameter('nit_origen').'%',Criteria::LIKE);
    		$this->parametros .= "&nit_origen=" . $this->getRequestParameter('nit_origen');
    	}
    	/****************************************************************************************/
    	if ($this->getRequestParameter('funcionario_origen')) {		
    		$funcionario_origen_text = trim($this->getRequestParameter('funcionario_origen'));
        $funcionario_origen_mb = iconv(mb_detect_encoding($funcionario_origen_text, mb_detect_order(), true), "UTF-8//IGNORE", $funcionario_origen_text);
        $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
        $c->add(DirectorioExternoPeer::FUNCIONARIO, '%' . $funcionario_origen_mb . '%', Criteria::LIKE);
        $this->parametros .= "&funcionario_origen=" . $funcionario_origen_text;
    	}
    	/****************************************************************************************/
    	if ($this->getRequestParameter('asuntorecibida_id')) {
    	    $c->add(ComRecibidaPeer::ASUNTORECIBIDA_ID,$this->getRequestParameter('asuntorecibida_id'));
    	    $this->parametros .= "&asuntorecibida_id=" . $this->getRequestParameter('asuntorecibida_id');
    	}
		/****************************************************************************************/
    	if ($this->getRequestParameter('formarecepcion_id')) {
    	    $c->add(ComRecibidaPeer::FORMARECEPCION_ID,$this->getRequestParameter('formarecepcion_id'));
    	    $this->parametros .= "&formarecepcion_id=" . $this->getRequestParameter('formarecepcion_id');
    	}
    	/***************************************************************************************/	 
    	if ($this->getRequestParameter('estadodigitalizacion_id')) {
    	    $c->add(ComRecibidaPeer::ESTADODIGITALIZACION_ID,$this->getRequestParameter('estadodigitalizacion_id'));
    	    $this->parametros .= "&estadodigitalizacion_id=" . $this->getRequestParameter('estadodigitalizacion_id');
    	}
      /***************************************************************************************/	
      if($this->getRequestParameter('qucom')){
        $view_notify = $this->getUser()->getAttribute('badge_view_notify', '', 'subscriber');
        $qucom = $this->getRequestParameter('qucom');
        if($qucom == md5($usuariologuiado.$view_notify)){
          $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,array(12,13,14),Criteria::NOT_IN);
          $this->parametros .= "&qucom=".$this->getRequestParameter('qucom');
        }
      }
      /**************************************************************************************/
      $numero_proceso = trim($this->getRequestParameter('numero_proceso'));
    	if ($numero_proceso){
        $c->add(ComRecibidaPeer::NUMERO_PROCESO,'%'.$numero_proceso.'%',Criteria::LIKE);
        $this->parametros .= "&numero_proceso=" . $numero_proceso;
      }
      /**************************************************************************************/
	  $radicado_archivado = trim($this->getRequestParameter('radicado_archivado')) ? trim($this->getRequestParameter('radicado_archivado')) : null;
	if (!empty($radicado_archivado)) {
		if($radicado_archivado == 1) {
			$c->add(ComRecibidaPeer::MARCA_VINCULACION,1,Criteria::NOT_EQUAL);
			$this->parametros .= "&radicado_archivado=" . $radicado_archivado;
		}elseif($radicado_archivado == 2){
			$c->add(ComRecibidaPeer::MARCA_VINCULACION,1);
			$this->parametros .= "&radicado_archivado=" . $radicado_archivado;
		}
	}
	/**************************************************************************************/
    $estado_respuesta = trim($this->getRequestParameter('estado_respuesta')) ? trim($this->getRequestParameter('estado_respuesta')) : null;
	if (!empty($estado_respuesta)) {
		if($estado_respuesta == 1) {
			$c->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
			$c->addOr(ComRecibidaPeer::COMENVIADA_ID,0);
			$this->parametros .= "&estado_respuesta=" . $estado_respuesta;
		}elseif($estado_respuesta == 2){
			$c->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNOTNULL);
			$this->parametros .= "&estado_respuesta=" . $estado_respuesta;
		}
	}
    /**************************************************************************************/
      $numero_fud = trim($this->getRequestParameter('numero_fud'));
    	if ($numero_fud){
        $c->add(ComRecibidaPeer::NUMERO_FUD,'%'.$numero_fud.'%',Criteria::LIKE);
        $this->parametros .= "&numero_fud=" . $numero_fud;
      }
      /**************************************************************************************/
      $expaddjoin_interesado = false;
      $pnombre_interesado = trim($this->getRequestParameter('pnombre_interesado'));
      if (!empty($pnombre_interesado)) {
        $expaddjoin_interesado = true;
        $pnombre_interesado_mb = iconv(mb_detect_encoding($pnombre_interesado, mb_detect_order(), true), "UTF-8//IGNORE", $pnombre_interesado);
        $c->add(InteresadosPeer::PRIMER_NOMBRE, '%' . $pnombre_interesado_mb . '%', Criteria::LIKE);
        $this->parametros .= "&pnombre_interesado=" . $pnombre_interesado;
      }
      /**************************************************************************************/
      $snombre_interesado = trim($this->getRequestParameter('snombre_interesado'));
      if (!empty($snombre_interesado)) {
        $expaddjoin_interesado = true;
        $snombre_interesado_mb = iconv(mb_detect_encoding($snombre_interesado, mb_detect_order(), true), "UTF-8//IGNORE", $snombre_interesado);
        $c->add(InteresadosPeer::SEGUNDO_NOMBRE, '%' . $snombre_interesado_mb . '%', Criteria::LIKE);
        $this->parametros .= "&snombre_interesado=" . $snombre_interesado;
      }
      /**************************************************************************************/
      $papellido_interesado = trim($this->getRequestParameter('papellido_interesado'));
      if (!empty($papellido_interesado)) {
        $expaddjoin_interesado = true;
        $papellido_interesado_mb = iconv(mb_detect_encoding($papellido_interesado, mb_detect_order(), true), "UTF-8//IGNORE", $papellido_interesado);
        $c->add(InteresadosPeer::PRIMER_APELLIDO, '%' . $papellido_interesado_mb . '%', Criteria::LIKE);
        $this->parametros .= "&papellido_interesado=" . $papellido_interesado;
      }
      /**************************************************************************************/
      $sapellido_interesado = trim($this->getRequestParameter('sapellido_interesado'));
      if (!empty($sapellido_interesado)) {
        $expaddjoin_interesado = true;
        $sapellido_interesado_mb = iconv(mb_detect_encoding($sapellido_interesado, mb_detect_order(), true), "UTF-8//IGNORE", $sapellido_interesado);
        $c->add(InteresadosPeer::SEGUNDO_APELLIDO, '%' . $sapellido_interesado_mb . '%', Criteria::LIKE);
        $this->parametros .= "&sapellido_interesado=" . $sapellido_interesado;
      }
      /**************************************************************************************/
      $nuid_interesado = trim($this->getRequestParameter('nuid_interesado'));
      if(!empty($nuid_interesado)){
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::NUMERO_IDENTIFICACION,'%'.$nuid_interesado.'%',Criteria::LIKE);  
        $this->parametros .= "&nuid_interesado=".$nuid_interesado;
      }
      /**************************************************************************************/
      if($expaddjoin_interesado){
        $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
        $c->addJoin(ComrecibidaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
      }
      /**************************************************************************************/
      $nombre_representantelegal = trim($this->getRequestParameter('nombre_representantelegal'));
    if ($nombre_representantelegal) {
	  $nombre_representantelegal_mb = iconv(mb_detect_encoding($nombre_representantelegal, mb_detect_order(), true), "UTF-8//IGNORE", $nombre_representantelegal);
      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
      $c->addJoin(ComrecibidaInteresadosPeer::REPRESENTANTELEGAL_ID, RepresentanteLegalPeer::REPRESENTANTELEGAL_ID);
      $c->add(RepresentanteLegalPeer::PRIMER_NOMBRE, '%' . $nombre_representantelegal_mb . '%', Criteria::LIKE);
      $this->parametros .= "&nombre_representantelegal=" . $nombre_representantelegal;
    }
      /**************************************************************************************/
      $nuid_representantelegal = trim($this->getRequestParameter('nuid_representantelegal'));
      if ($nuid_representantelegal) {		
        $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
        $c->addJoin(ComrecibidaInteresadosPeer::REPRESENTANTELEGAL_ID,RepresentanteLegalPeer::REPRESENTANTELEGAL_ID);
        $c->add(RepresentanteLegalPeer::NUMERO_IDENTIFICACION,'%'.$nuid_representantelegal.'%',Criteria::LIKE);
        $this->parametros .= "&nuid_representantelegal=" . $nuid_representantelegal;
      }
    	/***************************************************************************************/
    	if ($this->getRequestParameter('usuario_destino')) {
    		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_destino'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $this->parametros .= "&usuario_destino=" . $this->getRequestParameter('usuario_destino');        
        $consulta_usuarios = true;
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('usuario_copia')) {		
    		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuario_copia'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $this->parametros .= "&usuario_copia=" . $this->getRequestParameter('usuario_copia');        
        $consulta_usuarios = true;
    	}	
    	/**************************************************************************************/
    	if ($this->getRequestParameter('radicador')) {		
    		$c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
    		$c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('radicador'));		
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
        $this->parametros .= "&radicador=" . $this->getRequestParameter('radicador');        
        $consulta_usuarios = true;
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('fechaInicial')) {
    		if ($this->getRequestParameter('fechaFinal')) {
          $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
          $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaFinal').' 23:59:59',Criteria::LESS_THAN);
          $this->parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
          $this->parametros .= "&fechaFinal=" . str_replace("/","-",$this->getRequestParameter('fechaFinal'));
        }else{
          $c->add(ComRecibidaPeer::FECHA_CREACION,$this->getRequestParameter('fechaInicial').' 00:00:00',Criteria::GREATER_THAN);
          $this->parametros .= "&fechaInicial=" . str_replace("/","-",$this->getRequestParameter('fechaInicial'));
        }
      }
    	/**************************************************************************************/
    	if ($this->getRequestParameter('fechaCierre')) {
    		if ($this->getRequestParameter('fechaCierreFin')) {
            $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_THAN);
            $c->addAnd(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierreFin').' 23:59:59',Criteria::LESS_THAN);
            $this->parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
    	    }else{
    		    $c->add(ComRecibidaPeer::FECHA_DE_ANULACION,$this->getRequestParameter('fechaCierre').' 00:00:00',Criteria::GREATER_THAN);
    		    $this->parametros .= "&fechaCierre=" . str_replace("/","-",$this->getRequestParameter('fechaCierre'));
    	    } 
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('regional_id')) {		
    	    $c->add(ComRecibidaPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));
    	    $this->parametros .= "&regional_id=" . $this->getRequestParameter('regional_id');     
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('estado_com_recibida_id')) {		
    	    $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('estado_com_recibida_id'));
    	    $this->parametros .= "&estado_com_recibida_id=" . $this->getRequestParameter('estado_com_recibida_id');     
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('dependencia_id')) {		
    	    $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
    	    $this->parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id');     
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('tipo_com_recibida_id')) {		
    	    $c->add(ComRecibidaPeer::TIPOCOMRECIBIDA_ID,$this->getRequestParameter('tipo_com_recibida_id'));
    	    $this->parametros .= "&tipo_com_recibida_id=" . $this->getRequestParameter('tipo_com_recibida_id');     
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('radicado_origen')) {
	      $c->add(ComRecibidaPeer::RADICADO_ORIGEN, $this->getRequestParameter('radicado_origen'));
	      $this->parametros .= "&radicado_origen=" . $this->getRequestParameter('radicado_origen');
	    }
    	/**************************************************************************************/
    	if ($this->getRequestParameter('periodo_id')) {		
    	    $c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
    	    $this->parametros .= "&periodo_id=" . $this->getRequestParameter('periodo_id');     
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('fechaVence')) {		
    	    $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getRequestParameter('fechaVence'));
    	    $this->parametros .= "&fechaVence=" . str_replace("/","-",$this->getRequestParameter('fechaVence'));     
    	}    
    	/**************************************************************************************/
    	if ($this->getRequestParameter('detalles')) {		
    	    $c->add(ComRecibidaPeer::OBS_REEN_RESP,$this->getRequestParameter('detalles'),Criteria::LIKE);
    	    $this->parametros .= "&detalles=" . $this->getRequestParameter('detalles');     
    	}
      /**************************************************************************************/
      if(!$this->getUser()->checkPerm("LISTAR_COM_RECIBIDA_BLOQUEADAS", $usuariologuiado)){
        $c->add(ComRecibidaPeer::IS_LOCKED,1,Criteria::NOT_EQUAL);
      }
      /**************************************************************************************/
    	if ($this->getRequestParameter('wf_flujo_id')) {
    		$transicion_id = $this->getRequestParameter('wfaccionlegal');
    		$c->addJoin(ComRecibidaPeer::INSTANCIA, WfInstanciaPeer::WFINSTANCIA_ID);
    		$c->add(WfInstanciaPeer::WF_FLUJO_ID,$this->getRequestParameter('wf_flujo_id'));		
    		if($transicion_id){
    			$sql_custom = sprintf("%s IN (",WfInstanciaPeer::WF_FLUJO_ID);
    			$sql_custom .= "SELECT DISTINCT ".WfActividadTransicionPeer::WF_FLUJO_ID;
    			$sql_custom .= " FROM ".WfActividadTransicionPeer::TABLE_NAME;
    			$sql_custom .= " WHERE ".WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID." NOT IN(";
    			$sql_custom .= " SELECT ".WfInstanciaBitacoraPeer::WFACTIVIDADTRANSICION_ID." FROM ".WfInstanciaBitacoraPeer::TABLE_NAME.")";
    			$sql_custom .= " AND ".WfActividadTransicionPeer::ES_DESTINO." = 1";
    			$sql_custom .= " AND ".WfActividadTransicionPeer::WF_TRANSICION_ID." = ".$transicion_id . ")";
    			$sql_aux = $c->getNewCriterion(WfInstanciaPeer::WF_FLUJO_ID, $sql_custom, Criteria::CUSTOM);			
    			$c->add($sql_aux);
    			$this->parametros .= "&wfaccionlegal=" . $transicion_id;
    		}
    		$this->parametros .= "&wf_flujo_id=" . $this->getRequestParameter('wf_flujo_id');
    	}
    	/**************************************************************************************/
    	$estado_wf = $this->getRequestParameter('workflow_estado');
    	if($estado_wf != ""){		
    	   $c->addJoin(ComRecibidaPeer::INSTANCIA, WfInstanciaPeer::WFINSTANCIA_ID);
    	   $c->add(WfInstanciaPeer::ESTA_ABIERTA, $estado_wf);
    	   $this->parametros .= "&workflow_estado=" . $this->getRequestParameter('workflow_estado');
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('entradas')) {
			$c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,14,Criteria::NOT_EQUAL);     
			$c->addOr(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);	    
			$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
			$c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
			$c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);	           		  		
			$this->parametros .= "&entradas=" . $this->getRequestParameter('entradas');
			$consulta_buzones = true;
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('leer')) {
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,1);	    
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	           
        $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        //$c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('leer'));
        $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $c->add(ComRecibidaPeer::IS_LOCKED,0);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $this->parametros .= "&leer=" . $this->getRequestParameter('leer');
        $consulta_buzones = true;
    	}	
    	/**************************************************************************************/
    	if ($this->getRequestParameter('vencidas')) {		     
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   			   
        //$c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("0000-00-00 00:00:00"),Criteria::NOT_EQUAL);
        $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 23:59:59"),Criteria::LESS_THAN);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,5,Criteria::NOT_EQUAL);
        //$c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $c->add(ComRecibidaPeer::IS_LOCKED,0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $this->parametros .= "&vencidas=" . str_replace("/","-",$this->getRequestParameter('vencidas'));
        $consulta_buzones = true;
    	}	
    	/**************************************************************************************/
    	if ($this->getRequestParameter('porVencer')) {		     
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);	   	
        $c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getFechaVence(),Criteria::LESS_THAN);		   
        $c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 23:59-59"),Criteria::GREATER_THAN);
        //$c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $c->add(ComRecibidaPeer::IS_LOCKED,0);
        $c->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $this->parametros .= "&porVencer=" . str_replace("/","-",$this->getRequestParameter('porVencer'));
        $consulta_buzones = true;
    	}
    	/**************************************************************************************/
      if ($this->getRequestParameter('papelera')) {		        		   
			$c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,$this->getRequestParameter('papelera'));
			$c->addor(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12);        
			$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
			$c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
			$c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);        
			$this->parametros .= "&papelera=" . $this->getRequestParameter('papelera');
			$consulta_buzones = true;
			$this->papelera = true;
    	}
    	/**************************************************************************************/   
    	if ($this->getRequestParameter('porWorkflow')){
        $c->addJoin(ComRecibidaPeer::INSTANCIA,WfInstanciaPeer::WFINSTANCIA_ID);
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,14,Criteria::NOT_EQUAL);
        $c->addOr(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
        $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $c->add(WfInstanciaPeer::ESTA_ABIERTA,1);//workflow ejecutandose
        $this->parametros .= "&porWorkflow=" . $this->getRequestParameter('porWorkflow');
        $consulta_buzones = true;
    	}
      /**************************************************************************************/
	    if ($this->getRequestParameter('porEncargado')){
        $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,6);	    
        $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,4);
        $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);		   
        $this->parametros .= "&porEncargado=" . $this->getRequestParameter('porEncargado');
        $consulta_buzones = true;
        $consulta_usuarios = true;
      }
	    /**************************************************************************************/ 
      if (trim($this->getRequestParameter('tipoprocesocom_id'))){
        $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,trim($this->getRequestParameter('tipoprocesocom_id')));
	      $c->add(ComRecibidaPeer::IS_LOCKED,0);
        //$c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
		    $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $this->parametros .= "&tipoprocesocom_id=" . trim($this->getRequestParameter('tipoprocesocom_id'));
        $consulta_buzones = true;
        $consulta_usuarios = true;
	    }
      /**************************************************************************************/ 
      if (trim($this->getRequestParameter('porProcesoCom'))){
        if(trim($this->getRequestParameter('porProcesoCom')) == md5(2)){
          $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,2);
          $c->add(ComRecibidaPeer::IS_LOCKED,0);
          $c->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        }elseif(trim($this->getRequestParameter('porProcesoCom')) == md5(3)){
          $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,3);
          $c->add(ComRecibidaPeer::IS_LOCKED,0);
		      $c->add(ComRecibidaPeer::MARCA_VINCULACION,0);
		      $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,5,Criteria::NOT_EQUAL);
        }elseif(trim($this->getRequestParameter('porProcesoCom')) == md5(4)){
          $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,4);
          $c->add(ComRecibidaPeer::IS_LOCKED,0);
        }elseif(trim($this->getRequestParameter('porProcesoCom')) == md5(5)){
          $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,5);
          $c->add(ComRecibidaPeer::IS_LOCKED,0);
        }elseif(trim($this->getRequestParameter('porProcesoCom')) == md5(6)){
          $c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,6);
          $c->add(ComRecibidaPeer::IS_LOCKED,0);
        }
        $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
		    $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);        
        $this->parametros .= "&porProcesoCom=" . trim($this->getRequestParameter('porProcesoCom'));
        $consulta_buzones = true;
        $consulta_usuarios = true;
	  }
    	/**************************************************************************************/
    	if ($this->getRequestParameter('entregado') != "") {						   
    	    $c->add(ComRecibidaPeer::ESTAENTREGADO,$this->getRequestParameter('entregado'));
    	    $this->parametros .= "&entregado=" . $this->getRequestParameter('entregado');
    	}
    	/**************************************************************************************/
    	if ($this->getRequestParameter('marcada')) {				   
    	    $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
    	    $this->parametros .= "&marcada=" . $this->getRequestParameter('marcada');
    	}
    	/**************************************************************************************/
    	$orden = $this->getRequestParameter('ordenar');
    	switch($orden){
    		case 1:
    		   $c->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
    		break;
    		case 2:
    		   $c->addDescendingOrderByColumn(ComRecibidaPeer::NUMERO_RADICACION);		   
    		break;
    		case 3:
    		   $c->addDescendingOrderByColumn(ComRecibidaPeer::DIRECTORIOEXTERNO_ID);
    		break;
    		case 4:
    		   $c->addDescendingOrderByColumn(ComRecibidaPeer::ASUNTO);
    		default:
    		     $c->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
    		break;
    	}
		//**************************************************************************************
    	$c->addDescendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID);
    	//**************************************************************************************
    	$user_id = '';
    	$this->parametros .= "&ordenar=" . $orden;  
    	//**************************************************************************************
		//validar si tiene permisos para listar todas las comunicaciones externas recibidas    
    	if(!$this->getUser()->checkPerm('COM_RECIBIDA_LISTAR_TODAS', $usuariologuiado)){
    	  if(!$consulta_buzones){//si la consulta viene por los buzones no se ejecuta este bloque de codigo
          $arrIds = $this->getAutorizaciones();          
          if(count($arrIds) > 0){
            $arrIds[] = $usuariologuiado;
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);		  		   
            $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$arrIds,Criteria::IN);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
            //$c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
            //$this->user = $this->getComAsignadas(0,$c);
          }
          //**********************************************************************************
          if($this->getUser()->checkPerm("LISTAR_COM_RECIBIDA_TODA_ENTIDAD", $usuariologuiado)){
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $c->addJoin(ComRecibidaPeer::REGIONAL_ID,RegionalPeer::REGIONAL_ID);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);                
            $c->add(RegionalPeer::ENTIDAD_ID, $entidad_conectado);
          }elseif($this->getUser()->checkPerm("LISTAR_COM_RECIBIDA_TODAS_REGIONALES", $usuariologuiado)){
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            //$c->addJoin(ComRecibidaPeer::REGIONAL_ID,RegionalPeer::REGIONAL_ID);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);                
            $c->add(ComRecibidaPeer::REGIONAL_ID, $regional_conectado);
          }else{ 
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
            $c->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
            $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
          }
          //***********************************************************************************
          $user_id = implode(",",$arrIds);
        }else{//cuando no tiene permiso de listar todas y la consulta es por los buzones
    	      $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID); 
    		    $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);		
        }
    	}else{
        $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);          
        if($consulta_buzones){//si la consulta viene por los buzones no se ejecuta este bloque de codigo             
          $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        }
        //**************************************************************************************
        if(!$consulta_usuarios){
          $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        }
      }
      /**************************************************************************************/
      $this->user_id = $user_id;
      return $c;
    }
    
    public function getRegional($userId){
        $regId = array();
        $user = UsuarioPeer::retrieveByPk($userId);
        $regId[0] = $user->getRegionalId();
        $regId[1] = $user->getDependenciaId();
        return $regId;
    }
    
    public function executeRadicar($user_destino,$regional_id="",$num_radicado=0)
    {
    	$periodoActual = date("Y");
    	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      //*****************************************************************
      if($regional_id != ""){
          $reg = $regional_id;
      }else{
        $reg = $this->getRegionalFirma($usuariologuiado);
      }
      //*****************************************************************
      $max = $num_radicado ? $num_radicado : ComRecibidaPeer::getNumRadicacion($reg);
      $regional_cod = sprintf("%02d-",$reg);
      $num_radicacion = sprintf("%04d",$max);
      $area =  ComRecibidaPeer::getAbrevDependencia($user_destino) . "-";
      $radicado = $area.$num_radicacion."-E-".date("Y");
      return $radicado;
    }
	
	  public function getRegionalFirma($userId)
    {
      // consultar regional firmante inicial.
      $c = new Criteria();
      $firmante = '';
      $c->add(UsuarioPeer::USUARIO_ID, $userId);
      $result = UsuarioPeer::doSelect($c);
      $cont = 0;
      foreach ($result as $res) {
          if ($cont == 0)
              $firmante = $res->getRegionalId();
          $cont = 1;
      }
      return $firmante;
    }

    public function getDependenciaByUser($userId)
    {
      // consultar regional firmante inicial.
      $c = new Criteria();
      $firmante = 0;
      $c->add(UsuarioPeer::USUARIO_ID, $userId);
      $result = UsuarioPeer::doSelect($c);
      $cont = 0;
      foreach ($result as $res) {
          if ($cont == 0)
              $firmante = $res->getDependenciaId();
          $cont = 1;
      }
      return $firmante;
    }
    
	public function verRedirect($idComRec , $idUser)
    {
  		$fecha  = date('Y-m-d');
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');    
      /*******************************************************************************************************/   
      $c = new Criteria();
      $c->addJoin(RedireccionPeer::REDIRECCION_ID,RedireccionUsuarioPeer::REDIRECCION_ID);
      $c->addJoin(RedireccionUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
      $c->add(RedireccionUsuarioPeer::USUARIO_ID,$idUser);
      $c->add(RedireccionUsuarioPeer::ROLUSUREDIRECCION_ID,1);
      $c->add(UsuarioPeer::ESTADOUSUARIO_ID,3);
      $c->add(RedireccionPeer::FECHA_INICIAL,$fecha.' 00:00:00',Criteria::LESS_EQUAL);
      $c->add(RedireccionPeer::FECHA_FINAL,$fecha.' 00:00:00',Criteria::GREATER_EQUAL);
      $c->add(RedireccionPeer::ESTADOREDIRECCION_ID,2);
      $redireccionar = RedireccionUsuarioPeer::doSelectOne($c);
      if($redireccionar != null){
        $a = new Criteria();
        $a->add(RedireccionUsuarioPeer::ROLUSUREDIRECCION_ID,2);
        $a->add(RedireccionUsuarioPeer::REDIRECCION_ID,$redireccionar->getRedireccionId());
        $resp = RedireccionUsuarioPeer::doSelectOne($a);
        return $resp->getUsuarioId();
      }   
      /********************************************************************************************************/
      return null;				
	}
	
	public function asignarUserEdit($idComRec, $idUser,$idCargoUsuario)
	{
		$c = new Criteria();			 
		$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$idComRec);
		$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
		//$c->addDescendingOrderByColumn(ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
		$resp = ComrecibidaUsuarioPeer::doSelectOne($c);
		if($resp != null){
      $resp->setUsuarioId($idUser);
      $resp->setCargousuarioId($idCargoUsuario);
      $resp->save();
    }else{
      $comAsignar = new ComrecibidaUsuario();
      $comAsignar->setRolusuariorecibidaid(2);
      $comAsignar->setUsuarioId($idUser);
      $comAsignar->setComrecibidaId($idComRec);
      $comAsignar->setCargousuarioId($idCargoUsuario);
      $comAsignar->setEstaAsignada(1);
      $comAsignar->setEstadocomrecibidaId(1);
      $comAsignar->save();
    }
	}    
        
  public function asignarUser($idComRec, $idUser,$idCargoUsuario,$estadocomrecibida_id=1,$esta_asigado=0)
	{
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //**************************************************************************************
    $comAsignarRad = new ComrecibidaUsuario();
    $comAsignarRad->setRolusuariorecibidaid(1);
    $comAsignarRad->setUsuarioId($usuariologuiado);
    $comAsignarRad->setEstadocomrecibidaId(1);
    $comAsignarRad->setComrecibidaId($idComRec);
    $comAsignarRad->setCargousuarioId(CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariologuiado));
    $comAsignarRad->setEstaAsignada(0);
    $comAsignarRad->save();
    //**************************************************************************************
    $comAsignar = new ComrecibidaUsuario();
    $comAsignar->setRolusuariorecibidaid(2);
    $comAsignar->setUsuarioId($idUser);
    $comAsignar->setComrecibidaId($idComRec);
    $comAsignar->setCargousuarioId($idCargoUsuario);
    $comAsignar->setEstaAsignada(1);
    $comAsignar->setEstadocomrecibidaId($estadocomrecibida_id);
    $comAsignar->save();
		//**************************************************************************************
		if($comAsignarRad->getPrimaryKey() && $comAsignar->getPrimaryKey()){
			return true;
		}else{
			return false;
		}
  }
	
	public function asignarUserCopia($idComRec, $idUser, $idCargoUsuario)
	{	
	  	$arrCargosUser = array();
	  	if($idCargoUsuario != ""){
		   $arrCargosUser  = preg_split("/[,]+/",trim($idCargoUsuario),-1,PREG_SPLIT_NO_EMPTY);
    	}
        //**************************************************************************************
    	$arrUser  = preg_split("/[,]+/",trim($idUser),-1,PREG_SPLIT_NO_EMPTY);
		$iterador = count($arrUser); 	
		//**************************************************************************************
		for($j =0 ; $j < $iterador; $j++){
            if(trim($arrUser[$j]) != ""){
                $comAsignar = new ComrecibidaUsuario();
                $comAsignar->setRolusuariorecibidaid(3);
                $comAsignar->setUsuarioId($arrUser[$j]);
                $comAsignar->setComrecibidaId($idComRec);
                if(count($arrCargosUser) > 0){
                    $comAsignar->setCargousuarioId($arrCargosUser[$j]);
                }else{
                    $comAsignar->setCargousuarioId(CargoUsuarioPeer::getCargoUsuarioByIdUser($arrUser[$j]));
                }
                $comAsignar->setEstaAsignada(true);
                $comAsignar->setEstadocomrecibidaId(1);
                $comAsignar->save();
            }
		}//for
	}
	
	public function getComAsignadas(sfPropelPager $pager)
	{		
		foreach($pager->getResults() as $resp){			
			 $c = new Criteria();			 
			 $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$resp->getComrecibidaId());
			 $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
			 $c->addDescendingOrderByColumn(ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
			 $res = ComrecibidaUsuarioPeer::doSelectOne($c);
			 $arrCom[] = $res->getUsuario();			 
	    }										
		return $arrCom;	
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
  
  public function getUserForRol($idCom,$rol_id=0)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $arrUser = '';
    $c = new Criteria();    
    $c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID, $idCom);
    $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, $rol_id);
    $userCom =  ComrecibidaUsuarioPeer::doSelectJoinAll($c);
    foreach ($userCom as $res) 
    {            
       $arrUser = $res->getUsuario();                  
    }
    return $arrUser;
  }
	
	public function getNombUser()
	{
		$arrNomb = array();
		$c = new Criteria();
		$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 1);
		$c->addJoin(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,ComRecibidaPeer::COMRECIBIDA_ID);
		$c->addJoin(ComRecibidaPeer::MARCA,1);
		$c->addJoin(ComRecibidaPeer::ESTAENTREGADO,1);
		$userCom =  ComrecibidaUsuarioPeer::doSelect($c);		
		foreach ($userCom as $res) {            
             $arrNomb = $res->getNombre().' '.$res->getApellido();			             
        }
        return $arrNomb;
	}
	
	public function getAutorizaciones()
	{
	/*****************************Verificar las autorizaciones para usuario actual*********************************/
	 $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	 $userIdAut  = array(); 
	 $a = new Criteria();
	 $a->addJoin(AutorizacionComPeer::AUTORIZACIONCOM_ID,AutcomUsuarioPeer::AUTORIZACIONCOM_ID);
	 $a->add(AutorizacionComPeer::FECHA_INICIAL_AUT_COM,date('Y-m-d G:i:s'),Criteria::LESS_EQUAL);
	 $a->add(AutorizacionComPeer::FECHA_FINAL_AUT_COM,date('Y-m-d G:i:s'),Criteria::GREATER_EQUAL);
	 $a->add(AutcomUsuarioPeer::USUARIO_ID,$usuariologuiado);
	 $a->add(AutcomUsuarioPeer::ROLAUTCOMUSUARIO_ID,2);
	 $aut = AutcomUsuarioPeer::doSelect($a);
	 foreach($aut as $temp){
		 $b = new Criteria();
		 $b->add(AutcomUsuarioPeer::AUTORIZACIONCOM_ID,$temp->getAutorizacioncomId());
		 $b->add(AutcomUsuarioPeer::ROLAUTCOMUSUARIO_ID,1);
		 $resp = AutcomUsuarioPeer::doSelect($b);
		 foreach($resp as $result){
	 		 $userIdAut[] = $result->getUsuarioId();
		 }	
	 }
	 return $userIdAut;	
	}
	/**************************************************************************************************************/
	public function remitirUpdate($idCom , $idUser)
	{
		$conexion = Propel::getConnection();
		$c = new Criteria();
		$c->add(ComrecibidaUsuarioPeer::COMRECIBIDA_ID,$idCom);
		$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
		$resp =  ComrecibidaUsuarioPeer::doSelect($c);		
		foreach ($resp as $temp) {                             
      		$set = "UPDATE %s  SET  %s =".$idUser." WHERE COMRECIBIDA_ID=".$resp->getComrecibidaId();     
      		$sql = sprintf($set, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::USUARIO_ID);    
      		$sentencia = $conexion->prepare($sql);
            $sentencia->execute();			             
        }
	}	    
    
	public function getFechaVence()
	{
		$dias = 3;
		$fecha   = AddDays(date("Y-m-d"),$dias);
      	return $fecha;
	}
	
  public function downloadFile($strFileType,$strFileName,$fileContent) 
  {
   	$ContentType = "application/octet-stream";
   
   	if ($strFileType == ".asf") 
   		$ContentType = "video/x-ms-asf";
   	if ($strFileType == ".avi")
   		$ContentType = "video/avi";
   	if ($strFileType == ".doc")
   		$ContentType = "application/msword";
   	if ($strFileType == ".zip")
   		$ContentType = "application/zip";
   	if ($strFileType == ".xls")
   		$ContentType = "application/vnd.ms-excel";
   	if ($strFileType == ".gif")
   		$ContentType = "image/gif";
   	if ($strFileType == ".jpg" || $strFileType == "jpeg")
   		$ContentType = "image/jpeg";
   	if ($strFileType == ".wav")
   		$ContentType = "audio/wav";
   	if ($strFileType == ".mp3")
   		$ContentType = "audio/mpeg3";
   	if ($strFileType == ".mpg" || $strFileType == "mpeg")
   		$ContentType = "video/mpeg";
   	if ($strFileType == ".rtf")
   		$ContentType = "application/rtf";
   	if ($strFileType == ".htm" || $strFileType == "html")
   		$ContentType = "text/html";
   	if ($strFileType == ".xml") 
   		$ContentType = "text/xml";
   	if ($strFileType == ".xsl") 
   		$ContentType = "text/xsl";
   	if ($strFileType == ".css") 
   		$ContentType = "text/css";
   	if ($strFileType == ".php") 
   		$ContentType = "text/php";
   	if ($strFileType == ".asp") 
   		$ContentType = "text/asp";
   	if ($strFileType == ".pdf")
   		$ContentType = "application/pdf";
    
    echo $ContentType;
	header ("Content-Type: $ContentType"); 
	//header ("Content-Disposition: attachment; filename=$strFileName; size=$fileSize;");
	header ("Content-Disposition: attachment; filename=$strFileName;"); 
	
	// Updated oktober 29. 2005
	if (substr($ContentType,0,4) == "text") {
			echo imap_qprint($fileContent);
	} else {
			echo imap_base64($fileContent);
	}
 }	
}

class MYPDF extends TCPDF {

  /////////////////////////////////////////////////
  // METHODS, VARIABLES
  /////////////////////////////////////////////////

  protected $nombreinforme = 1;
  protected $headercells = 1;
  private $pdf;  


  public function setHeaderCells($type_header){
      $this->headercells = $type_header; 
  }

  public function setNombreInforme($value){
      $this->nombreinforme = $value; 
  }

	//Page header
  public function Header()
  {
    $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario_genera = UsuarioPeer::retrieveByPK($usuariologuiado);
    //directorio de las imagenes de los encabezados
    //*******************************************************************************************************
    //$img_contenedor = sfConfig::get('base_simad')."/images/encabezado_carta/logos_carnet/";
    $img_contenedor = "images/encabezado_carta/logos_carnet/";
    $img_logo = trim($usuario_genera->getRegional()->getEntidad()->getLogoCorporativo()) ?
    $usuario_genera->getRegional()->getEntidad()->getLogoCorporativo() : "default.jpg";
    //********************************************************************************************************
    $html_encabezado =
      '<table border="1" width="100%" valign="middle" cellspacing="0"  cellpadding="0" height="90">
        <tr>
        <td colspan="2" align="center" height="30" ><div align="center" style="align-items: center;"><img width="80px" height="70px" align="abs" src="' . $img_contenedor . $img_logo . '" /></div></td>
        <td height="10%" width="563" colspan="6" align="center" valign="middle"><b>VENTANILLA &Uacute;NICA DE RADICACI&Oacute;N</b></td>
        <td colspan="3" valign="middle" align="center"><div align="center"><img height="80" width="150" align="abs" src="' . $img_mipg . '" /></div></td>
        </tr>
        <tr>
        <td colspan="9" align="center" rowspan="2"><b>Registro Entrega Correspondencia</b></td>
        <td><b>Versi&oacute;n</b></td>
        <td width="87"><b>02</b></td>
        </tr>
        <tr>
        <td><b>Fecha</b></td>
        <td width="87"><b>&nbsp;</b></td>
        </tr>
        <tr>
        <td align="center" style="background-color: #C0C0C0;"><b>Regional que Envia</b></td>
        <td align="center"><b>' . $usuario_genera->getRegional()->getDescripcion() . '</b></td>
        <td align="center" style="background-color: #C0C0C0;"><b>Fecha y Hora</b></td>
        <td align="center">' . date("d-m-Y G:i:s") . '</td>
        <td colspan="2" align="center"><b>&nbsp;</b></td>
        <td align="center" style="background-color: #C0C0C0;"><b>MENSAJERO</b></td>
        <td width="126"></td>
        </tr>';

	  $html_encabezado .= '
        <tr>
        <th width="105" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Dependencia Destino</b></font></th>
        <th width="130" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Funcionario Destino</b></font></th>
        <th width="100" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Referencia/No Solicitud</b></font></th>
        <th width="109" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Remitente</b></font></th>
		    <th width="55" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Tipo Documento</b></font></th>
        <th width="223" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Descripci&oacute;n</b></font></th>
        <th width="140" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Observaciones</b></font></th>
        <th width="140" align="center" style="background-color: #C0C0C0;"><font size="8"><b>Nombre Recibido</b></font></th>
        </tr></table>';
    //set margin header
    $this->setHeaderMargin(5);
    //set font
    $this->SetFont('dejavusans', '', 10);
    // Title
    $this->writeHTML($html_encabezado, true, false, true, false, '');
  }

  // Page footer
  public function Footer()
  {
    // Position at 15 mm from bottom
    $this->SetY(-15);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
  }
}

?>