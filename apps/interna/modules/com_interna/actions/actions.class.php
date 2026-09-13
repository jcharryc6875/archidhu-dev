<?php
/**
 * com_interna actions.
 *
 * @package    simad
 * @subpackage com_interna
 * @author     Luis Gabriel Quiceno Cardenas
 * @version    SVN: $Id: actions.class.php 3335 2007-01-23 16:19:56Z fabien $
 */
require_once(sfConfig::get('sf_lib_dir')."/exec_class.php");
class com_internaActions extends sfActions
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
    
    public function verificaPrilegioCerrar($currentForm)
    { 
        $usuarioLoguiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if(!$this->getUser()->checkPerm($currentForm, $usuarioLoguiado)){
        	$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
        }
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

  public function executeFile()
  {
    if($this->getRequestParameter('cominterna_id'))
        $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
  }
  
  public function executeFileTemplate()
    {
        unset($_SESSION['ftype_com']);
        unset($_SESSION['btn_text']);
        //*********************************************************************************
        $chekedcom = $this->getRequestParameter('chekedcom') ? (bool)$this->getRequestParameter('chekedcom') : false;
        if(!$chekedcom){
            return sfView::NONE;
        }
        //*********************************************************************************
        $this->isGenWord = false;
        $_SESSION['ftype_com'] = '.pdf,.PDF';
        $_SESSION['btn_text'] = "Cargar Pdf";
    }

    public function executeFileTemplateWord()
    {
        unset($_SESSION['ftype_com']);
        unset($_SESSION['btn_text']);
        //*********************************************************************************
        $chekedcom = $this->getRequestParameter('chekedcom') ? (bool)$this->getRequestParameter('chekedcom') : false;
        if($chekedcom === false){
            return sfView::NONE;
        }
        //*********************************************************************************
        $_SESSION['ftype_com'] = '.docx';
        $_SESSION['btn_text'] = "Cargar Word";
        $this->isGenWord = true;
        //*********************************************************************************
        $this->setTemplate('fileTemplate');
    }

    public function executeLoadTplWord()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $plantillascom_id = $this->getRequestParameter('plantillascom_id') ? $this->getRequestParameter('plantillascom_id') : null;
        $cominterna_id = $this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : null;
        //*********************************************************************************
        if(empty($plantillascom_id)){
            $response_data = array('status' => 400, 'message' => 'Acceso denegado, los parametros no son validos');
            $array = json_encode($response_data);
            $this->getResponse()->setContentType('application/json');
            return $this->renderText($array);
        }
        //*********************************************************************************
        $plantillas_com = PlantillasComPeer::retrieveByPK($plantillascom_id);
        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        //*********************************************************************************
        if(empty($plantillas_com) || empty($com_enviada)){
            $response_data = array('status' => 400, 'message' => 'Ocurrio un error, los parametros enviados no son validos');
            $array = json_encode($response_data);
            $this->getResponse()->setContentType('application/json');
            return $this->renderText($array);
        }
        //*********************************************************************************
        $params['comobject_id'] = $com_interna->getPrimaryKey();
        $params['periodo_id'] = $com_interna->getPeriodoId();
        $params['use_membrete'] = $com_interna->getUseMembrete();
        //*********************************************************************************
        $savePath = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $reponse_gen = $plantillas_com->generateWordByPlantilla($savePath,$params);
        //*********************************************************************************
        if($reponse_gen['isError'] === true){
            $response_data = array('status' => 400, 'message' => $reponse_gen['message']);
        }else{
            $url_download = sfConfig::get('publicUrl').'/'.$reponse_gen['path_plantilla'];
            $response_data = array('status' => 200, 'message' => $reponse_gen['message'], 'url_download' => $url_download);
        }
        //*********************************************************************************
        $array = json_encode($response_data);
        $this->getResponse()->setContentType('application/json');
        return $this->renderText($array);
    }

    public function executeDownloadTplByWord()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $cominterna_id = $this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : null;
        //*********************************************************************************
        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        $format_file = pathinfo($com_interna->getUrlFileWord(),PATHINFO_EXTENSION);
        //*********************************************************************************
        if(file_exists($com_interna->getUrlFileWord()) && $format_file == 'docx'){
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . basename($com_interna->getUrlFileWord()) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($com_interna->getUrlFileWord()));
            
            // Limpiar buffer de salida
            ob_clean();
            flush();
            
            // Leer y enviar el archivo
            readfile($com_interna->getUrlFileWord());
            exit;
        }else{
            $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
            exit;
        }
    }

  public function executeUploadTemplate()
  {
    $cominterna_id = !empty($this->getRequestParameter('cominterna_id')) ? $this->getRequestParameter('cominterna_id') : null;
    //****************************************************************************************
    if(empty($cominterna_id)){
        $this->com_interna = null;
        $this->file_format = '.pdf,.PDF,application/pdf';
        $this->btntext = 'Cargar Archivo';
        $this->isGenWord = false;
    }else{
        $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
        if($this->com_interna->getIsCreateWord() == ResponseDocTypeCom::Word){
            $this->file_format = '.docx';
            $this->btntext = "Cargar Word";
            $this->isGenWord = true;
        }else{
            $this->file_format = '.pdf,.PDF,application/pdf';
            $this->btntext = 'Cargar Archivo';
            $this->isGenWord = false;
        }
    }
  }

  public function executeUploadTemplateEdit()
  {
    $currentForm="COM_INTERNA_REMPLAZAR_FILE_DIGIT";
    $this->verificaPrilegioCerrar($currentForm);

    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $this->setTemplate('uploadTemplate');
  }
	
  public function executeUploadDocTemplate()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');	
    //*********************************************************************************************    
    if (!empty($_FILES)){
        $is_error = false;
        $file_vars = pathinfo($_FILES['file']['name']);
        $file_tmp = $_FILES['file']['tmp_name'];
        //*****************************************************************************************
        if(empty($file_tmp)){
            $this->getRequest()->setError("attachs",'Debe seleccionar un archivo para cargar');
            $is_error = true;
        }
        //*****************************************************************************************
        if($is_error){ $this->forward('com_interna', 'uploadTemplate'); }
        //*****************************************************************************************
        $directorio_tmp = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'tmp';
        //*****************************************************************************************
        $cons = uniqid();
        $util_simad = new simad_util();
        $new_filename = $util_simad->clean_name_file($file_vars);
        $fullpath = $directorio_tmp.DIRECTORY_SEPARATOR.$cons.'_'.$new_filename;
        $replyfile = $cons.'_'.$new_filename;
        //*****************************************************************************************
        @move_uploaded_file($file_tmp, $fullpath);        
        //*****************************************************************************************
        $mime_trust = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/pdf', 'application/x-pdf', 'application/x-bzpdf');
        $file_valid = simad_util::CheckIsValidFormatFile($fullpath,$mime_trust);
        if(!$file_valid){   
            $this->getRequest()->setError("attachs",'El tipo de archivo no es valido');
            $this->forward('com_interna', 'uploadTemplate');
        }
        //*****************************************************************************************
        if(!file_exists($fullpath)){
            $this->getRequest()->setError("attachs",'Ocurrio un error al cargar el archivo');
            $this->forward('com_interna', 'uploadTemplate');
        }
        //*****************************************************************************************
        $cominterna_id = trim($this->getRequestParameter('cominterna_id')) ? trim($this->getRequestParameter('cominterna_id')) : null;
        $this->com_interna = null;
        if(!empty($cominterna_id))
        {
            $currentForm="COM_INTERNA_REMPLAZAR_FILE_DIGIT";
            $this->verificaPrilegioCerrar($currentForm);
            $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
            //*************************************************************************************
            if(empty($com_interna->getUrlFileWord()) && !file_exists($com_interna->getUrlFileWord())){
                $dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto().'uploads');
                $filedir_upload = simad_util::createPath($dir_raiz.DIRECTORY_SEPARATOR.date("Ymd")).DIRECTORY_SEPARATOR.$replyfile;
            }else{
                $filedir_upload = $com_interna->getUrlFileWord();
            }
            //*************************************************************************************
            if(file_exists($fullpath)) 
            {
                if($com_interna->getIsCreateWord() == ResponseDocTypeCom::Word){
                    $com_interna->setIsCreateWord(ResponseDocTypeCom::Word);
                    //*****************************************************************************
                    $docxProps = simad_util::readCustomPropsFromDocx($fullpath);
                    //*****************************************************************************
                    $uuid = $com_interna->getPrimaryKey().'#'.$com_interna->getTipoComInterna()->getPlantillasCom()->getPrimaryKey();
                    $plantillaId = $com_interna->getTipoComInterna()->getPlantillasCom()->getPrimaryKey();
                    $modulo_id = $com_interna->getTipoComInterna()->getPlantillasCom()->getModuloId();
                    $periodoAt = $com_interna->getPeriodoId();
                    $current_hmac = $com_interna->getTipoComInterna()->getPlantillasCom()->makeHmac($uuid, $plantillaId, $periodoAt, $modulo_id);
                    //*****************************************************************************
                    if (!hash_equals($current_hmac, $docxProps['xd_hmac'])) {
                        $this->getRequest()->setError("attachs", 'El documento de word no es el asociado a esta comunicación');
                        $this->forward('com_interna', 'uploadTemplate');
                    }
                }else{
                    $com_interna->setIsCreateWord(ResponseDocTypeCom::Pdf);
                }
                //*********************************************************************************
                if(rename($fullpath,$filedir_upload))
                {
                    $com_interna->setUrlFileWord($filedir_upload);
                    $com_interna->setContenido(null);
                    $com_interna->setUseMembrete(0);
                    //*****************************************************************************
                    if($com_interna->getFirmadoDigital() == 1)
                    {
                        $com_interna->setFirmadoDigital(0);
                    }
                    //*****************************************************************************
                    $com_interna->save();
                    //*****************************************************************************
                    $digitDocFile = $com_interna->getPathImageDigitByCom();
                    //*****************************************************************************
                    if(!empty($digitDocFile))
                    {
                        unlink($digitDocFile);
                    }
                    //*****************************************************************************
                    $this->com_interna = $com_interna;
                }
            }
        }
        //*****************************************************************************************
        $this->fileName = basename($fullpath);
    }
  }

  public function executeFileWord()
  {
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
  }
  
  public function executeLoadPlantilla()
  {
	$tipo_com_interna = TipoComInternaPeer::retrieveByPk($this->getRequestParameter('tipocominterna_id'));
	$contents = "";
	//***************************************************************************************************
	if($tipo_com_interna == null){
		return $this->renderText($contents);
	}
	//***************************************************************************************************
	if($tipo_com_interna->getPlantillascomId()){
		$contents = trim($tipo_com_interna->getPlantillasCom()->getContents());
	}
	return $this->renderText($contents);
  }
  
  /**
     * com_internaActions::executeRadicarMasivas()
     * accion para funcionalidad de actualizar las guias masivamente, obteniendo los datos
     * desde un archivo de excel     
     * @return
     */
    public function executeRadicarMasivas()
    {
        $currentForm="COM_INTERNA_RADICACION_MASIVA";
		$this->verificaPrilegioCerrar($currentForm);
        $this->process_end = $this->getRequestParameter('process_end') ? $this->getRequestParameter('process_end') : 0;
        $this->sheetData = array();
        $this->cod_msg = $this->getRequestParameter('cod_msg') ? $this->getRequestParameter('cod_msg') : 0;
        $this->msg_error = $this->getRequestParameter('msg_error') ? $this->getRequestParameter('msg_error') : "";
        $this->com_interna = new ComInterna();
    }

    public function executeRadicarMasivas2()
    {
        $currentForm="COM_INTERNA_RADICACION_MASIVA";
		$this->verificaPrilegioCerrar($currentForm);
        $this->process_end = $this->getRequestParameter('process_end') ? $this->getRequestParameter('process_end') : 0;
        $this->sheetData = array();
        $this->cod_msg = $this->getRequestParameter('cod_msg') ? $this->getRequestParameter('cod_msg') : 0;
        $this->msg_error = $this->getRequestParameter('msg_error') ? $this->getRequestParameter('msg_error') : "";
        $this->com_interna = new ComInterna();
    }

    public function executeUpdateComBatch()
    {
        $currentForm="COM_INTERNA_RADICACION_MASIVA";
		$this->verificaPrilegioCerrar($currentForm);        
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
        $usuario_autenticado = UsuarioPeer::retrieveByPk($usuariologuiado);
        //************************************************************************************************
        $firmas_str = trim($this->getRequestParameter('firmanteId'));
        $destino_str = trim($this->getRequestParameter('destinatarioId'));
        $file_name = trim($this->getRequestParameter('dataufile'));
        //************************************************************************************************
        $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $inputFileName = $upload_dir.DIRECTORY_SEPARATOR.$file_name;
        $listVars = ComInternaPeer::readFileComCombined($inputFileName);
        $sheetData = $listVars['sheetData'];
        $headerList = $listVars['headerList'][0];
        $iserror = 0;
        $errormsg = null;
        //************************************************************************************************
        if(!count($listVars['sheetData'])){
            return sfView::ERROR;
        }
        //************************************************************************************************
        $list_firmas = explode(",", $firmas_str);
    	$user_firma = UsuarioPeer::retrieveByPk($list_firmas[0]);
    	$dependencia_id = $user_firma->getDependenciaId();
        $depen_codigo = $user_firma->getDependencia()->getCodigo();
        $regional_id = $usuario_autenticado->getRegionalId();
        //************************************************************************************************
        $cur = new Criteria();
        $cur->add(CargoUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $cur->add(CargoUsuarioPeer::ES_PRINCIPAL,true);
        $cargoUsuario = CargoUsuarioPeer::doSelectOne($cur);
        $uscargo_radicador = $cargoUsuario->getCargousuarioId();
        //************************************************************************************************
        $patrones = array();
        for($ihead = 0; $ihead < count($headerList); $ihead++){
            if(trim($headerList[$ihead]))
                $patrones[$ihead]  = '#.#$'.mb_strtolower(trim($headerList[$ihead])).'#.#';
        }
        $patrones[]  = '#.#$dia_fecha_letra#.#';
        $patrones[]  = '#.#$dia_mes_num#.#';
        $patrones[]  = '#.#$mes_fecha_letra#.#';
        $patrones[]  = '#.#$periodo_fecha#.#';
        $patrones[]  = '#.#$asunto_com#.#';
        //************************************************************************************************
        $data_response = array();
        //************************************************************************************************
        for($row = 0; $row <= count($sheetData); ++$row){
            try{
                if(count($sheetData[$row]) == 0){ continue; }
                //****************************************************************************************
                $com_interna = new ComInterna();
                $com_interna->setEstadocominternaId(2);
                $com_interna->setPeriodoId(date("Y"));
                $com_interna->setDependenciaId($dependencia_id);
                $com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id'));
                $com_interna->setRegionalId($regional_id);
                $com_interna->setEstadodigitalizacionId(1);
                $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
                $com_interna->setReferencia((trim($this->getRequestParameter('referencia'))));
                $com_interna->setNumeroRadicacion(0);
                $com_interna->setEsCopia(0);
                $com_interna->setFolios(1);
                $com_interna->setAnexos(null);
                $com_interna->setEstaentregado(0);
                $com_interna->setMarca(0);
                $com_interna->setUseMembrete($this->getRequestParameter('use_membrete') ? $this->getRequestParameter('use_membrete') : 0);
                $com_interna->setFirmaElectronica(UsuarioPeer::countValidateTipoFirma(trim($this->getRequestParameter('firmanteId'))));
                $com_interna->setRequiereRespuesta(null);
                $com_interna->save();
                //****************************************************************************************
                $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());
                $com_interna->setContenido($com_interna->getTipoComInterna()->getPlantillasCom()->getContents());
                $radicado = $com_interna->getRadicadoFormat(null,$regional_id,$depen_codigo);
                $com_interna->setRadicado($radicado);
                //****************************************************************************************
                $com_interna->save();
                //****************************************************************************************
                $cargosDestino = trim($this->getRequestParameter('cargousuarioId'));
                $cargoFirma = trim($this->getRequestParameter('cargousuarioIdFirma'));
                //****************************************************************************************
                $this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),1,$uscargo_radicador,2); 
                $this->insertaCominternaUsuario($firmas_str,$com_interna->getPrimaryKey(),2,$cargoFirma,2);
                $this->insertaCominternaUsuario($destino_str,$com_interna->getPrimaryKey(),4,$cargosDestino,2);
                //****************************************************************************************
                if($this->getRequestParameter('revisorUserId')){
                    $revisorUserId = $this->getRequestParameter('revisorUserId');
                    $cargousuarioIdRevisor = $this->getRequestParameter('cargousuarioIdRevisor');
                    $usurioFirma = ComEnviadaPeer::insertaEnviadaUsuarios($revisorUserId,$com_interna->getPrimaryKey(),4,$cargousuarioIdRevisor,1,4,$esta_asignada);
                    $esta_asignada = 0;
                }
                //****************************************************************************************
                setlocale(LC_TIME, "spanish");
                $contenido_body = $com_interna->getTipoComInterna()->getPlantillasCom()->getContents();
                $sustituciones    = array();
                foreach($sheetData[$row] as $item){
                    if(trim($item))
                        $sustituciones[] = ($item);
                }

                $newDate = date("d-m-Y", strtotime($com_interna->getFechaCreacion()));
                $sustituciones[]  = "";//ucwords(strftime("%A", strtotime($newDate)));
                $sustituciones[]  = date("d", $newDate);
                $sustituciones[]  = ucwords(strftime("%B", strtotime($newDate)));
                $sustituciones[]  = date("Y", mktime($com_interna->getFechaCreacion()));
                $sustituciones[]  = $com_interna->getReferencia();
                //****************************************************************************************                
                for($x=0; $x<=count($patrones);$x++){
                    $contenido_body = str_replace($patrones[$x],$sustituciones[$x],$contenido_body);
                }
                //**************************************************************************************** 
                $com_interna->setContenido($contenido_body);
                $com_interna->save();
                //****************************************************************************************
                $data_response[] = array('radicado'=>$com_interna->getRadicado(),
                                    'asunto'=>$com_interna->getReferencia(),'fecha_creacion'=>$com_interna->getFechaCreacion());
            }catch(Exception $ex){
                $errormsg = $ex->getMessage();
                $iserror = 1;
                break;
            }
        }
        //************************************************************************************************
        $objects = array("error" => $iserror,"errormsg" => $errormsg, 'dataobj' => $data_response);        
        return $this->renderPartial('listMasivos', array('object_response' => $objects));
    }

    /**
     * executeBatchFirma function
     * Permite generar la vista para listar las comunicaciones enviadas en estado borrador del usuario
     * y que se pueden o no radicar dependiendo del flujo
     * @return vista
     */
    public function executeBatchFirma()
    {
        $this->verificaPrilegio("com_interna/list");
        $this->verificaPrilegio("COM_INTERNA_FIRMA_LOTE");
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
        //*********************************************************************************************
        $this->ilist_objects = ComInternaPeer::getListComRadicarByUser($usuariologuiado);
    }

    /**
     * executeUpdateRadicarBatch function
     * Inicia el proceso de radicacion de comunicaciones en estado borrador para el usuario actual
     * se realizan las validaciones correspondientes para constatar que comunicaciones se pueden radicar
     * @return array respuesta del proceso de radicacion en lotes
     */
    public function executeUpdateRadicarBatch()
    {
        $this->verificaPrilegio("com_interna/radicar");
        $this->verificaPrilegio("COM_INTERNA_FIRMA_LOTE");
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
        //*********************************************************************************************
        $ilist_coms = ComInternaPeer::getListComRadicarByUser($usuariologuiado);
        $response_data = array();
        foreach ($ilist_coms as $com_object) {
            if($com_object['IS_RADICAR'] == 0){
                $com_interna = ComInternaPeer::retrieveByPK($com_object['COMINTERNA_ID']);
                $com_data = array('COMINTERNA_ID' => $com_interna->getPrimaryKey(),'RADICADO' => $com_interna->getRadicado(),'FECHA_CREACION' => $com_interna->getFechaCreacion());

                try {
                    $com_interna_anterior = clone $com_interna;
                    $isGenerateRad = $com_interna->radicaComBatch();
                    
                    if($isGenerateRad){
                        $com_data['isError'] = false;
                        $com_data['RADICADO'] = $com_interna->getRadicado();
                        if($com_interna->getFirmadoDigital() == 0){
                            $response_sign = $com_interna->singDocumentProcess();
                        }
                    }else{
                        $com_data['isError'] = true;
                    }
                } catch (PropelException $th) {
                    $com_data['isError'] = true;
                } catch (Exception $th) {
                    $com_data['isError'] = true;
                }

                $response_data[$com_object['COMINTERNA_ID']] = $com_data;
                $this->guardarAuditoria($com_interna_anterior,$com_interna);
            }
        }
        //*********************************************************************************************
        $array = json_encode($response_data);
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($array);
    }

    /**
     * com_internaActions::executeReadFileExcel()
     * accion para funcionalidad leer datos del archivo de excel
     * @return
     */
    public function executeReadFileExcel()
    {
        $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio = simad_util::createPath($upload_dir);
        //*******************************************************************************************
        if ($this->getRequest()->hasFiles() && $this->getRequest()->getFileName('file'))  	  	
        {    	
        	$file_vars = pathinfo($this->getRequest()->getFileName('file'));
            $util_simad = new simad_util();
            $extension_file = $file_vars['extension'];
            $cleanfilename = $util_simad->clean_name_file($file_vars);//limpiar el nombre del archivo
            $file_name = simad_util::uniquename($upload_dir,sha1($cleanfilename.time()),".".$extension_file);//validar uniquename    		
            $inputFileName = $directorio.DIRECTORY_SEPARATOR.$file_name;            
            $this->getRequest()->moveFile('file', $inputFileName);
            //***************************************************************************************
            $listVars = ComInternaPeer::readFileComCombined($inputFileName);            
            //***************************************************************************************
            $this->inputFileName = basename($inputFileName);
            $this->headerList = $listVars['headerList'];
            $this->sheetData = $listVars['sheetData'];
            $this->sheetDataSerialize = $listVars['sheetDataSerialize'];
            $this->cod_msg = $listVars['cod_msg'];
            $this->msg_error = $listVars['msg_error'];
        }
        else
        {            
            $this->cod_msg = 1;
            $this->msg_error = "Debe seleccionar el archivo que contiene las registros para radicar";
            //$this->setTemplate('radicarMasivas');
        }
    }

  public function executeUploadsWord()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
	$dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    //***********************************************************************************************
  	foreach ($this->getRequest()->getFiles() as $file)  	
    {
        //*******************************************************************************************
    	$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
    	$directorio_entidad = $dirRaiz.$entidad_text."/";
    	$directorio_tmp = $directorio_entidad.$dirTmp."/";    	
        //*******************************************************************************************
		$cons    = $this->getNewConsecutivo();
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $directorio = simad_util::createPath($directorio_tmp);
        //*******************************************************************************************
        @move_uploaded_file($file['tmp_name'], $directorio.'/'.$cons.'_'.$fileName);
        //*********************************************************************************
        $this->fileName = $cons.'_'.$fileName;
    }     	         
  }        
  
  public function executeConsulta()
  { 
  	$this->verificaPrilegio("com_interna/consulta");    
	  $this->com_interna = new ComInterna();
    $this->ObjCominternaUsuario=new CominternaUsuario();
    
	$this->mostrarUsuario=0;
    if($this->getRequestParameter('mostrarUsuario')==1)
     $this->mostrarUsuario=1;
     
     $c = new Criteria();
     $c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);          
     $this->usuarios = UsuarioPeer::doSelect($c);
     
     $r = new Criteria();
     $r->addAscendingOrderByColumn(RegionalPeer::REGIONAL_ID);
     $r->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);          
     $this->regionales = RegionalPeer::doSelect($r);
     
     
  }
  
  public function executeDigitalizar()
  {  
  	$this->verificaPrilegioCerrar("com_interna/digitalizar");	
	$this->cominterna_id = $this->getRequestParameter('cominterna_id');	
  }
  
  public function executeFileDigitalizar()
  {
    $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    //******************************************************************************************************
    //CREANDO ESTRUCTURA DE DIRECTORIOS
    $dirRaiz   = ParametroPeer::retrieveByPk(25)->getValortexto();
    $dir_alias  = ParametroPeer::retrieveByPk(15)->getValortexto();
    $extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    //******************************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file) 	
    {
    	$usuariologuiado = $this->getUser()->getAttribute('username', '', 'subscriber');
    	$file_vars = pathinfo($file['name']);
        $nomFile   =  ($com_interna->getRadicado());
        //**************************************************************************************************
    	$entidad_text = $com_interna->getRegional()->getEntidad()->getDirectorioName();
    	$regional_text = $com_interna->getRegional()->getDirectorioName();
    	$entidad_text = $entidad_text.'/'.$regional_text;
    	$periodo = $com_interna->getPeriodoId();
    	$directorio_entidad = $dirRaiz . $entidad_text.'/';
    	$directorio_entidad .= $dir_alias.'/'.$periodo;
        //**************************************************************************************************
        $directorio = simad_util::createPath($directorio_entidad);
        //**************************************************************************************************
        if(in_array($file_vars['extension'], $extensions)){
            $file_name =  $nomFile.'.'.$file_vars['extension'];            
            //**********************************************************************************************
            @move_uploaded_file($file['tmp_name'], $directorio.'/'.$file_name);
            //**********************************************************************************************
            if(file_exists($directorio.'/'.$file_name)){
                $this->fileName = $file_name;
            }else{
                $this->fileName = "Ocurrio un error al adjuntar el archivo,Por favor intente de nuevo";
            }
            //**********************************************************************************************
        }else{
            $file_name =  null;
            $this->fileName = "Ocurrio un error al adjuntar el archivo,Por favor intente de nuevo";
        }
        //**************************************************************************************************
    }
  }
  
  public function executeUploads()
  {
    //*************************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
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
  
  public function executeDzFileUpload()
  {
    $data_array = array();
  	if (!empty($_FILES))
    {
    	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');        
    	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
		$dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
        //***************************************************************************************
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
		$cons    = $this->getNewConsecutivo();
        //***************************************************************************************
    	$directorio_entidad = $dirRaiz.$entidad_text.DIRECTORY_SEPARATOR;
    	$directorio_tmp = $directorio_entidad.$dirTmp.DIRECTORY_SEPARATOR;    	
        //***************************************************************************************
        $file_vars = pathinfo($_FILES['file']['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        //***************************************************************************************
        $tempFile = $_FILES['file']['tmp_name'];
		$directorio = simad_util::createPath($directorio_tmp);
        //***************************************************************************************
        @move_uploaded_file($tempFile,$directorio.DIRECTORY_SEPARATOR.$cons.'_'.$fileName);
        $data_array['name'] = $cons.'_'.$fileName;
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
  
  public function executeIndex()
  {
  	//$encabezado_cuerpo_copia = "Este es un mensaje para informarle que se le ha radicado la siguiente Comunicacion Interna (PRUEBA ALCALDIA CUCUTA 201.245.191.89):";
  	//$this->envioEmail('1','1','1',$encabezado_cuerpo_copia);
  	//$this->envioEmail('259','68',$encabezado_cuerpo_copia);
    return $this->forward('com_interna', 'consulta');
    //$filesource = "C:\projects\simad\web\uploads\prueba.docx";
    //echo ComInternaPeer::readPlantillaWordCom($filesource);exit;
  }
  
  public function executePlanilla()
  {   
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');		 	        
	$this->setLayout(false);
	$parametros = '';
	if ($this->getRequestParameter('periodo_id')) {		
		//$c->add(ComRecibidaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));
		$parametros .= " AND periodo_id=" . $this->getRequestParameter('periodo_id');     
	}
	//************************************************************************************************	
	if ($this->getRequestParameter('marca')) {		   
		//$c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
		$parametros .= " AND MARCA=" . $usuariologuiado;
	}
	//************************************************************************************************	
	$conexion = Propel::getConnection();				
	$cadSet = '';
	$set = "select RADICADO,REFERENCIA AS ASUNTO, USUARIO.NOMBRE AS DESTINATARIO,'________________' AS FIRMA 
	 from usuario,cominterna_usuario, com_interna where com_interna.COMINTERNA_ID=cominterna_usuario.COMINTERNA_ID  AND ROLUSUARIOCOMINTERNA_ID != 1 AND ROLUSUARIOCOMINTERNA_ID != 2
	 AND usuario.USUARIO_ID=cominterna_usuario.USUARIO_ID".$parametros;
	//************************************************************************************************		
	$sentencia = $conexion->prepare($set);
	$sentencia->execute();
	$this->resultset = $sentencia->fetchAll(PDO::FETCH_BOTH);		
	$this->username = $usuariologuiado;		          	   
  }
 
  
  public function executeList()
  { 
  	$this->verificaPrilegio("com_interna/list");
	$c = new Criteria();
	$c->setDistinct();
    //*********************************************************************************************
    $entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    $regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	//*********************************************************************************************
    $c = $this->getCriteriaBasic($c);    
    //*********************************************************************************************
    $pager = new sfPropelPager('ComInterna',10);
    $pager->setCriteria($c);
    $pager->setPage($this->getRequestParameter('page',1));
    $pager->init();
    $this->pager = $pager;
    $this->controlPaginacion = 1;
	//*********************************************************************************************	      
	$this->usuariologuiado = $usuariologuiado;
	$this->anular = "";    
  }    

  public function executeHistorial()
  { 
  	$this->verificaPrilegio("com_interna/list");
    $this->com_interna_origen = $this->getRequestParameter('cominterna_id');
	$c=new Criteria();	
	/**************************************************************************************************/
	$this->directorio_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
    $this->directorio_alias  = ParametroPeer::retrieveByPk(26)->getValortexto();
    $this->format_digit_img = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
	$this->directorio_adj  = ParametroPeer::retrieveByPk(15)->getValortexto();
	/**************************************************************************************************/
	$this->parametros="a=1";
	$usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
	$user_id = '';
	
	if($this->getRequestParameter('porFunciEntrada')=="1"){
		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID, $usuariologuiado  );	
		$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
		$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        if($this->getRequestParameter('estadocominterna_id') == ""){
            $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,4,Criteria::NOT_EQUAL);     
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
        }        
	 	$this->parametros.="&porFunciEntrada=1";
	    /*
		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
		if($this->getRequestParameter('estadocominterna_id') == 6){
			//$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
		  //$c->add(ComInternaPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);     
	      //$c->addAnd(ComInternaPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);
	    }else{
		  $c->add(ComInternaPeer::ESTADOCOMINTERNA_ID,4,Criteria::NOT_EQUAL);     
	      $c->addAnd(ComInternaPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);	
		}
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID, $usuariologuiado  );	
		$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
		$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
		$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
	 	$this->parametros.="&porFunciEntrada=1";	
		*/
		//var_dump($c);			 	
	}
	elseif($this->getRequestParameter('porFunciSalida')=="1"){
		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);	
		$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);	
		$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
        if($this->getRequestParameter('estadocominterna_id') == ""){
            $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,4,Criteria::NOT_EQUAL);     
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
        }		
	 	$this->parametros.="&porFunciSalida=1";
	}else{			
		if(!$this->getUser()->checkPerm('COM_INTERNA_LISTAR_TODAS', $usuariologuiado)){			
			//si tiene autorizaciones hace un in() incluyendo a el mismo sino como esta
			$arrIds = $this->getAutorizaciones();
		  if($arrIds){
		  	$arrIds[] = $usuariologuiado;
				$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
			 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$arrIds,Criteria::IN);//in	
				$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
				$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
				$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
				//$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
			 	
			  
			}else{
				$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
			 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);	
				$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
				$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
				$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
				$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
		 		
			}
			for($i=0; $i<count($arrIds);$i++){
		     $user_id .= $arrIds[$i];
	       }	
		}
	}
	
	
	
	if($this->getRequestParameter('marca')){
		if($this->getRequestParameter('marca')=="sinMarca"){		  	
		  $c->add(ComInternaPeer::MARCA,$usuariologuiado,Criteria::NOT_EQUAL);		
		  $this->parametros.="&marca=".$this->getRequestParameter('marca');	
		}
		else{	
	 	  $c->add(ComInternaPeer::MARCA,$this->getRequestParameter('marca'));		
		  $this->parametros.="&marca=".$this->getRequestParameter('marca');
		  $this->marcarEntrega=1;
		}			 	
	}
	
	
	if($this->getRequestParameter('codigo_reen_resp')){	
	 	$c->add(ComInternaPeer::CODIGO_REEN_RESP,$this->getRequestParameter('codigo_reen_resp'));		
		$this->parametros.="&codigo_reen_resp=".$this->getRequestParameter('codigo_reen_resp');			 	
	}
    
    if($this->getRequestParameter('tipocominterna_id')){	
	 	$c->add(ComInternaPeer::TIPOCOMINTERNA_ID,$this->getRequestParameter('tipocominterna_id'));		
		$this->parametros.="&tipocominterna_id=".$this->getRequestParameter('tipocominterna_id');
	}
    	
	if($this->getRequestParameter('estadodigitalizacion_id')){	
	 	$c->add(ComInternaPeer::ESTADODIGITALIZACION_ID,$this->getRequestParameter('estadodigitalizacion_id'));		
		$this->parametros.="&estadodigitalizacion_id=".$this->getRequestParameter('estadodigitalizacion_id');
	}
	
	if($this->getRequestParameter('radicado')){	
	 	$c->add(ComInternaPeer::RADICADO,'%'.$this->getRequestParameter('radicado').'%',Criteria::LIKE);		
		$this->parametros.="&radicado=".$this->getRequestParameter('radicado');			 	
	}
	
	if($this->getRequestParameter('dependenciaOrigen')){
	 	$c->add(ComInternaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependenciaOrigen'));		
		$this->parametros.="&dependenciaOrigen=".$this->getRequestParameter('dependenciaOrigen');			 	
	}
	
	if($this->getRequestParameter('referencia')){
	 	$c->add(ComInternaPeer::REFERENCIA,'%'.$this->getRequestParameter('referencia').'%',Criteria::LIKE);		
		$this->parametros.="&referencia=".$this->getRequestParameter('referencia');			 	
	}
	if($this->getRequestParameter('contenido')){
	 	$c->add(ComInternaPeer::CONTENIDO,'%'.$this->getRequestParameter('contenido').'%',Criteria::LIKE);		
		$this->parametros.="&contenido=".$this->getRequestParameter('contenido');			 	
	}
	if($this->getRequestParameter('guia')){
	 	$c->add(ComInternaPeer::GUIA,'%'.$this->getRequestParameter('guia').'%',Criteria::LIKE);		
		$this->parametros.="&guia=".$this->getRequestParameter('guia');			 	
	}
	//////////////////FILTROS POR USUARIO CON DIFERENTES ROLES///////////////////////////////////////////
    $perm_list_all = false;

	if($this->getRequestParameter('usuarioProyecto')){
	    if(!$perm_list_all)
        {
            $c->addAlias('CIUP',CominternaUsuarioPeer::TABLE_NAME);
            $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUP.COMINTERNA_ID');
            $c->add('CIUP.USUARIO_ID',$this->getRequestParameter('usuarioProyecto'));            
            $c->add('CIUP.ROLUSUARIOCOMINTERNA_ID',1);
        }
        else
        {    	  		
    	 	    $c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuarioProyecto'));	
    		    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
        }
        $this->parametros.="&usuarioProyecto=".$this->getRequestParameter('usuarioProyecto');
	}
	if($this->getRequestParameter('usuarioFirma')){
	   if(!$perm_list_all)
        {
            $c->addAlias('CIUF',CominternaUsuarioPeer::TABLE_NAME);
            $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUF.COMINTERNA_ID');
            $c->add('CIUF.USUARIO_ID',$this->getRequestParameter('usuarioFirma'));            
            $c->add('CIUF.ROLUSUARIOCOMINTERNA_ID',2);
        }
        else
        {    	  		
    	 	    $c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuarioFirma'));	
	          $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
        }
	 	$this->parametros.="&usuarioFirma=".$this->getRequestParameter('usuarioFirma');			 	
	}
	if($this->getRequestParameter('usuarioCopia')){
	    if(!$perm_list_all)
        {
            $c->addAlias('CIUCOP',CominternaUsuarioPeer::TABLE_NAME);
            $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUCOP.COMINTERNA_ID');
            $c->add('CIUCOP.USUARIO_ID',$this->getRequestParameter('usuarioCopia'));            
            $c->add('CIUCOP.ROLUSUARIOCOMINTERNA_ID',3);
        }
        else
        {    	  		
    	 	    $c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuarioCopia'));	
	          $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        }
	 	$this->parametros.="&usuarioCopia=".$this->getRequestParameter('usuarioCopia');			 	
	}
	if($this->getRequestParameter('usuarioDestino')){
		if(!$perm_list_all)
        {
            $c->addAlias('CIUDES',CominternaUsuarioPeer::TABLE_NAME);
            $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUDES.COMINTERNA_ID');
            $c->add('CIUDES.USUARIO_ID',$this->getRequestParameter('usuarioDestino'));            
            $c->add('CIUDES.ROLUSUARIOCOMINTERNA_ID',4);
        }
        else
        {    	  		
 	 	    $c->add(CominternaUsuarioPeer::USUARIO_ID,$this->getRequestParameter('usuarioDestino'));	
	        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        }
	 	$this->parametros.="&usuarioDestino=".$this->getRequestParameter('usuarioDestino');			 	
	}
    //*************************************************************************************************
    $this->estado_com_interna = $this->getRequestParameter('estadocominterna_id'); 
	if($this->getRequestParameter('estadocominterna_id')){
		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
		$c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$this->getRequestParameter('estadocominterna_id'));
	 	//$c->add(ComInternaPeer::ESTADOCOMINTERNA_ID,$this->getRequestParameter('estadocominterna_id'));		
		$this->parametros.="&estadocominterna_id=".$this->getRequestParameter('estadocominterna_id');			 	
	}	
    if ($this->getRequestParameter('fechaCreaInicial')) {
        $c->add(ComInternaPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaInicial'),Criteria::GREATER_EQUAL);      
        $this->parametros.="&fechaCreaInicial=".$this->getRequestParameter('fechaCreaInicial');
	}
    if ($this->getRequestParameter('fechaCreaFinal')) {        
        $c->addAnd(ComInternaPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaFinal')." 23:59",Criteria::LESS_EQUAL);        
        $this->parametros.="&fechaCreaFinal=".$this->getRequestParameter('fechaCreaFinal');
     }
    if($this->getRequestParameter('estaEntregado')!=""){
	 	$c->add(ComInternaPeer::ESTAENTREGADO,$this->getRequestParameter('estaEntregado'));		
		$this->parametros.="&estaEntregado=".$this->getRequestParameter('estaEntregado');			 	
	} 
	
	if($this->getRequestParameter('periodo_id')){
	 	$c->add(ComInternaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));		
		$this->parametros.="&periodo_id=".$this->getRequestParameter('periodo_id');			 	
	}
	if ($this->getRequestParameter('fechaEnvioInicial')) {
        $c->add(ComInternaPeer::FECHA_ENVIO_GUIA, $this->getRequestParameter('fechaEnvioInicial'),Criteria::GREATER_EQUAL);      
        $this->parametros.="&fechaEnvioInicial=".$this->getRequestParameter('fechaEnvioInicial');
	}
    if ($this->getRequestParameter('fechaEnvioFinal')) {        
        $c->addAnd(ComInternaPeer::FECHA_ENVIO_GUIA, $this->getRequestParameter('fechaEnvioFinal'),Criteria::LESS_EQUAL);        
        $this->parametros.="&fechaEnvioFinal=".$this->getRequestParameter('fechaEnvioFinal');
     }
	
	
	if($this->getRequestParameter('orden')){
	 	$c->addDescendingOrderByColumn(ComInternaPeer::$this->getRequestParameter('orden'));		
		$this->parametros.="&orden=".$this->getRequestParameter('orden');			 	
	 }
	 else{
		$c->addDescendingOrderByColumn(ComInternaPeer::COMINTERNA_ID);
	}
    //*********************************************************************************************    
	$pager=new sfPropelPager('ComInterna',10);
	$pager->setCriteria($c);
	$pager->setPage($this->getRequestParameter('page',1));
	$pager->init();
	$this->pager=$pager;
	$this->controlPaginacion = 1;
}
	
 public function getDestinatario($com_internaId)
  { 
  	// consultar firmante inicial
  	$destinatario="";
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
		    $destinatario = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			//$destinatario = $res->getUsuario();//->getNombre()." ".$res->getUsuario()->getApellido();
	  $cont=1;					
	} 
	return $destinatario;
  	
  }
  
  
  
  public function getDestinatarioId($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$destinatario = $res->getUsuarioId();
	  $cont=1;					
	} 
	return $destinatario;
  	
  }
 
  public function executeMarcar()
  {
  	$marcar = 0;
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$com_interna = ComInternaPeer::retrieveByPK($this->getRequestParameter('cominterna_id'));
  	if($com_interna->getMarca() == 0 || $com_interna->getMarca() != $usuariologuiado)
  	{
  		$marcar = $usuariologuiado;
  	}
  	$com_interna->setMarca($marcar);
  	$com_interna->save();
  }
  
 public function colocarMarca($cominternaId,$marca){	 
	 
  	 $com_interna = ComInternaPeer::retrieveByPk($cominternaId);
  	 $com_interna->setMarca($marca);
  	 $com_interna->save();
  }
 
 public function executeDesmarcarTodos()
 {
	$currentForm="com_interna/list";
	//**************************************************************************************************
	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$conexion = Propel::getConnection();
	//**************************************************************************************************
	$this->usuariologuiado = $usuariologuiado;
	$this->verificaPrilegio($currentForm);	
	$this->parametros = "&a=1";
	$this->papelera = false;    
	$wherec = new Criteria();
	$wherec = $this->getCriteriaBasic($wherec);
	$wherec->setDistinct();
	$wherec->clearSelectColumns();
	//**************************************************************************************************
	try{
		$updc = new Criteria();
		$updc->add(ComInternaPeer::MARCA, 0);

		$affectedRows = BasePeer::doUpdate($wherec, $updc, $conexion);
		//**********************************************************************************************
		$this->message_info = sprintf('Las %s comunicaciones fueron desmarcadas satisfactoriamente',$affectedRows);
		$this->isError = false;
	}catch (PropelException $ex){
		$this->message_info = 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage();
		$this->isError = true;
	}catch (Exception $ex){
		$this->message_info = 'Error interno del servidor, '.$ex->getMessage();
		$this->isError = true;
	}
  }
  
  public function executeMarcarTodos()
  {	
	$currentForm = "com_interna/list";
	//**************************************************************************************************
	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$conexion = Propel::getConnection();
	//**************************************************************************************************
	$this->usuariologuiado = $usuariologuiado;
	$this->verificaPrilegio($currentForm);
	$this->parametros = "&a=1";
	$this->papelera = false;
	$wherec = new Criteria();
	$wherec = $this->getCriteriaBasic($wherec);
	$wherec->setDistinct();
	$wherec->clearSelectColumns();
	//**************************************************************************************************
	try {
		$updc = new Criteria();
		$updc->add(ComInternaPeer::MARCA, $usuariologuiado);

		$affectedRows = BasePeer::doUpdate($wherec, $updc, $conexion);
		//**********************************************************************************************
		$this->message_info = sprintf('Las %s comunicaciones fueron marcadas satisfactoriamente', $affectedRows);
		$this->isError = false;
	} catch (PropelException $ex) {
		$this->message_info = 'Error interno del servidor, Por favor comuniquese con el administrador,' . $ex->getMessage();
		$this->isError = true;
	} catch (Exception $ex) {
		$this->message_info = 'Error interno del servidor, ' . $ex->getMessage();
		$this->isError = true;
	}
	//**************************************************************************************************
	$this->setTemplate('marcarTodos');
  }
  
  public function executeBorrarMarcados(/*$comenviadaId,$marca*/)
  {	
     $com_internas_id = "";
  	 $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
  	 $c = new Criteria();
     $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
  	 $c->add(ComInternaPeer::MARCA,$usuariologuiado);
     $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,4,Criteria::NOT_EQUAL);
  	 $marca_interna = CominternaUsuarioPeer::doSelect($c);
  	 foreach($marca_interna as $com_interna){  	        
			$com_interna->setEstadocominternaId(6);
			$com_interna->save();
            $this->updateAnularCominterna($com_interna->getCominternaId(),6);
	 }
  }
  
  public function updateAnularCominterna($id_interna,$estado_id){          
     $com_interna= ComInternaPeer::retrieveByPK($id_interna);          
     $com_interna->setEstadocominternaId($estado_id);                     
     $com_interna->save();
  }
  
  public function executeRestaurarMarcados($comenviadaId,$marca)
  {
  	 $com_internas_id = "";
  	 $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
  	 $c = new Criteria();
     $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);  	 
     $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,6);
     $c->add(ComInternaPeer::MARCA,$usuariologuiado);
  	 $marca_interna = CominternaUsuarioPeer::doSelect($c);
  	 foreach($marca_interna as $com_interna){  	        
			$com_interna->setEstadocominternaId(3);
			$com_interna->save();
            $this->updateAnularCominterna($com_interna->getCominternaId(),3);
	 }
  }  
  
  public function executeMarcarEntregados($comenviadaId,$marca)
  {
  	 $conexion = Propel::getConnection();
  	 $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
  	 $consulta = "update %s set  %s=1  where %s=".$usuariologuiado." ";
	 $sql      = sprintf($consulta, ComInternaPeer::TABLE_NAME, ComInternaPeer::ESTAENTREGADO,ComInternaPeer::MARCA);
     $sentencia = $conexion->prepare($sql);
     $sentencia->execute();
  }
 
 public function executeExcelWorkflow()
  {   	
	$this->setLayout(false);    
    $this->parametros="a=1";
    $this->com_internas = "";
    if($this->getRequestParameter('porWorkflow')=="1"){
       $c=new Criteria();	
   	   $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
       $c->addJoin(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,WfInstanciaPeer::WFINSTANCIA_ID);
       $c->addJoin(WfInstanciaPeer::WFINSTANCIA_ID,ComInternaPeer::INSTANCIA);					
       $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
       $c->addJoin(WfInstanciaPeer::WFINSTANCIA_ID,WfInstanciaBitacoraPeer::WFINSTANCIA_ID);
       $c->addJoin(WfInstanciaBitacoraPeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);
       $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
       $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuariologuiado);	                              
       $c->addDescendingOrderByColumn(ComInternaPeer::FECHA_CREACION);	
				   
       $arrComInternas=$this->object = WfInstanciaBitacoraPeer::doSelect($c);
    
       $this->destinatario=array(); 
   	   $this->firmas=array();
       $i=0;
       /*foreach ($arrComInternas as $comInterna){
    	  $this->destinatario[$i]=$this->getDestinatario($comInterna->getCominternaId());
    	  $this->firmas[$i]=$this->getFirstUsurioFirmaName($comInterna->getCominternaId());
    	  $i++;
   	   }*/    
    }
  }
   
  public function executeExcel()
  {
	$this->verificaPrilegio("com_interna/excel");
	$this->setLayout(false);
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$this->usuario_genera = UsuarioPeer::retrieveByPK($usuariologuiado);     
	//********************************************************************************************
	$c = new Criteria();
	$c->setDistinct();
	$this->parametros="a=1";
	$c = $this->getCriteriaBasic($c);
	//********************************************************************************************
	$c->addJoin(ComInternaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
	//********************************************************************************************
	$c->clearSelectColumns();
	//********************************************************************************************
	$c->addAsColumn('NOMBRE_DEPENDENCIA', DependenciaPeer::NOMBRE);
	$c->addAsColumn('CODIGO_DEPENDENCIA', DependenciaPeer::CODIGO);
	$c->addSelectColumn(ComInternaPeer::RADICADO);
	$c->addSelectColumn(ComInternaPeer::REFERENCIA);
	$c->addSelectColumn(ComInternaPeer::COMINTERNA_ID);  
	$c->addSelectColumn(ComInternaPeer::FECHA_CREACION);
	$c->addSelectColumn(ComInternaPeer::FECHA_DE_ANULACION);
	$c->addSelectColumn(ComInternaPeer::OBS_ANULACION);
	//********************************************************************************************
	// EL QUE PROYECTA... ROL 1.
	$sql_uproyecta = "(SELECT TOP 1 CONCAT(UPROYECTA.NOMBRE, ' ', UPROYECTA.APELLIDO)";
	$sql_uproyecta .= " FROM USUARIO UPROYECTA";
	$sql_uproyecta .= " JOIN COMINTERNA_USUARIO COMUPROYECTA ON UPROYECTA.USUARIO_ID = COMUPROYECTA.USUARIO_ID";
	$sql_uproyecta .= " WHERE COMUPROYECTA.ROLUSUARIOCOMINTERNA_ID = 1";
	$sql_uproyecta .= " AND COMUPROYECTA.COMINTERNA_ID = " . ComInternaPeer::COMINTERNA_ID . ")";
	$c->addAsColumn('USUARIO_PROYECTA', $sql_uproyecta);
	//********************************************************************************************
	// FUNCIONARIO DESTINO... ROL 4.
	$sql_destinatario = "(SELECT TOP 1 CONCAT(UDEST01.NOMBRE, ' ', UDEST01.APELLIDO)";
	$sql_destinatario .= " FROM USUARIO UDEST01";
	$sql_destinatario .= " JOIN COMINTERNA_USUARIO UDEST02 ON UDEST01.USUARIO_ID = UDEST02.USUARIO_ID";
	$sql_destinatario .= " WHERE UDEST02.ROLUSUARIOCOMINTERNA_ID = 4";
	$sql_destinatario .= " AND UDEST02.COMINTERNA_ID = " . ComInternaPeer::COMINTERNA_ID . ")";
	$c->addAsColumn('DESTINATARIO', $sql_destinatario);
	//********************************************************************************************
	// FUNCIONARIO ORIGEN... ROL 2.
	$sql_ufirma = "(SELECT TOP 1 CONCAT(UORIGEN_01.NOMBRE, ' ', UORIGEN_01.APELLIDO)";
	$sql_ufirma .= " FROM USUARIO UORIGEN_01";
	$sql_ufirma .= " JOIN COMINTERNA_USUARIO UORIGEN_02 ON UORIGEN_01.USUARIO_ID = UORIGEN_02.USUARIO_ID";
	$sql_ufirma .= " WHERE UORIGEN_02.ROLUSUARIOCOMINTERNA_ID = 2";
	$sql_ufirma .= " AND UORIGEN_02.COMINTERNA_ID = " . ComInternaPeer::COMINTERNA_ID . ")";
	$c->addAsColumn('REMITENTE', $sql_ufirma);
	$this->com_internas = ComInternaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function executePrintBatch()
  {
	$this->verificaPrilegio("IMPRESION_COM_MASIVA");
	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	//*********************************************************************************************
	$dir_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
	$digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
	//*********************************************************************************************
	$c = new Criteria();
	$c->setLimit(250);
	$c->add(ComInternaPeer::MARCA, $usuariologuiado);
	$list_com = ComInternaPeer::doSelect($c);
	//*********************************************************************************************
	$zip = new ZipArchive();
	$zipFileName = md5(date("YmdGis")) . ".zip";
	$pathzip = sfConfig::get('sf_web_dir') . "/tmp/" . $zipFileName;

	if (file_exists($pathzip)) {
	  unlink($pathzip);
	}

	if ($zip->open($pathzip, ZIPARCHIVE::CREATE) != TRUE) {
	  die("Could not open archive");
	}
	//*********************************************************************************************
	foreach ($list_com as $com_interna) {

	  if(in_array($com_interna->getEstadocominternaId(), array(1,4))){ continue; }

	  $storage_com = $com_interna->getBasicUrlDigitCom($dir_raiz, $digit_dir);

	  $filename = $com_interna->getRadicado() . ".pdf";
	  $filename_digit = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
	  
	  if (!file_exists($filename_digit)) {
		  //MARGENES DE IMPRESION
		  $margins_list['top'] = 5;
		  $margins_list['left'] = 15;
		  $margins_list['buttom'] = 50;
		  $margins_list['rigth'] = 18;
		  //*************************************************************************************
		  $filename_digit = $com_interna->generateFileInDisk($margins_list);
	  }
	  //*****************************************************************************************
	  $zip->addFile($filename_digit, basename($filename));
	}
	//*********************************************************************************************
	$zip->close();
	//*********************************************************************************************  
	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=$zipFileName");
	header("Pragma: no-cache");
	header("Expires: 0");
	readfile("$pathzip");
	exit;
  }

  public function executeMezclarPdf()
  {
  	$dirRaiz = ParametroPeer::retrieveByPk(17);
  	$formato = ParametroPeer::retrieveByPk(31);
  	$work_dir = $dirRaiz->getValortexto();
    $com_interna= ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $fileOrigen = $work_dir.$com_interna->getRadicado().$formato->getValortexto();
    
	$pdf = new merge_pdf();
	$pdf->getNumeroPaginas($fileOrigen,$work_dir."temp.txt",$work_dir);
	
	$vlineas = file($work_dir."temp.txt");
    
    /* Podemos mostrar / trabajar con todas las lineas:*/
    foreach ($vlineas as $sLinea){
      $tempLinea = explode(":",$sLinea);
        if($tempLinea[0] == "NumberOfPages")
           $numero_paginas = $tempLinea[1];
    }     	
	/*****************************************************/
	unlink($work_dir."temp.txt");
	
	$this->numero_paginas = $numero_paginas; 
	$this->com_interna = $com_interna; 
	$this->fileOrigen =$fileOrigen;      
  }
  
  public function executeUploadMezclarPdf()
  {  	
  	if($submit_boton = $_REQUEST["boton"] == 'Adjuntar'){//para saber de q boton se envio el submit
  		
	  	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();//directorio donde se guardan las digitalizaciones					
		//Definir la posicion
		$position = 1;//posicion inicial para la adicion del nuevo pdf
		
		if($this->getRequestParameter('posicion')){//verrificar si se adicionara al principio  o al final del pdf
			$position = $this->getRequestParameter('posicion');//se asigna la seleccion a la variable
		}elseif($this->getRequestParameter('pagina_posicion')){//para saber si se digito una pagina
			$position = $this->getRequestParameter('pagina_posicion');//se asigna el numero de la pagina ala variable
		}	
			
	  	foreach ($this->getRequest()->getFiles() as $file)//se verifica q vengan archivos para subir al servidor 	
	    {   
	        $file_vars = pathinfo($file['name']);
            $util_simad = new simad_util();
            $fileName = $util_simad->clean_name_file($file_vars);		
	        $directorio = simad_util::createPath($dirRaiz);//se verifica o se crea el directorio donde se encuentran los adjuntos
            @move_uploaded_file($file['tmp_name'], $directorio.DIRECTORY_SEPARATOR.$fileName);//se mueve el archivo al directorio del servidor
		}
        
		$outputfile = $dirRaiz.$this->getRequestParameter('fileOrigen');//Archivo al cual se le va adicionar		 
		$uploadFile = $dirRaiz.$fileName;//archivo q se va adicinara
		$numero_paginas = $this->getRequestParameter('numero_paginas');//se obtiene el numero total de paginas
			
	    $merge_pdf = new merge_pdf();//se instancia la clase q realzara la adicion de los pdf
		$file_temp = $merge_pdf->create_command('pdftk',$uploadFile,$outputfile,$position,$numero_paginas);//se hace el llamado a funcion.
		
		$error = '';
		unlink($uploadFile);//se elimina el archivo q se adiciono
		if(file_exists($dirRaiz.'temp'.$file_temp.'.pdf')){
		   	unlink($outputfile);//se elimina el archivo al cual se le va adicionar
		   	rename($dirRaiz.'temp'.$file_temp.'.pdf',$outputfile);//se renombra el archivo temporal con el nombre original
		}else{
			unlink($dirRaiz.'temp'.$file_temp.'.pdf');//se elimina el archivo al cual se le va adicionar
			$error = 'Error al tratar De abrir el Archivo';
		}
        
		$this->fileOrigen = $this->getRequestParameter('fileOrigen');//se envia el nombre del archivo original a la vista
		$this->error = $error;
	}elseif($submit_boton = $_REQUEST["boton"] == 'Eliminar'){//if para saber si viene por el eliminar
		return $this->forward('com_interna', 'deletePagePdf');//se pasa el control a otra accion del clase
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
    	$com_interna= ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    	$outputfile = $com_interna->getRadicado().$formato->getValortexto();
	   	$pdf = new merge_pdf();
	   	$file_temp = $pdf->deletePage($work_dir.$outputfile,$numero_pagina,$work_dir,$total_paginas);	   				   
		unlink($work_dir.$outputfile);
		rename($dirRaiz->getValortexto().'temp'.$file_temp.'.pdf',$work_dir.$outputfile);
		$msg = 'Se ha Eliminado Satisfactoriamente la Pagina '.$numero_pagina.' Del Archivo '.$outputfile;		
	}else{
		$msg = 'Debe Digitar Una Pagina Para Eliminar Del Archivo '.$outputfile;
	}	   				    	
	
	$this->msg = $msg;		
	$this->com_interna = $com_interna;       
  }
  
  public function handleErrorUploadMezclarPdf()
  {
  	$this->forward('com_interna', 'mezclarPdf');
  }

  public function executeShowPdfByWord()
  {
    $base_path = sfConfig::get('base_simad');
    $com_interna = ComInternaPeer::retrieveByPk(trim($this->getRequestParameter('cominterna_id')));
    //***************************************************************************************************
    if(!empty($com_interna->getUrlFileWord())){
        if(file_exists(trim($com_interna->getUrlFileWord()))){
            $filegenerate = $com_interna->generatePdfByFile(trim($com_interna->getUrlFileWord()),false);
            $extension = pathinfo($filegenerate, PATHINFO_EXTENSION);
            $url_viewer = $base_path.'/tmp/'.$filegenerate;
            if(strtolower($extension) == 'pdf'){ $url_viewer = $base_path.'/viewerEx.php?fileview='.$filegenerate; }
            //*******************************************************************************************
			if(strtolower($extension) == 'pdf'){ 
                $url_viewer = $base_path.'/viewerEx.php?fileview='.$filegenerate; 
                if($com_interna->getEstadocominternaId() == 4){
                    $pathtmp = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR . $filegenerate;
                    $relf_path = pathinfo($filegenerate, PATHINFO_DIRNAME);
                    //***********************************************************************************
                    $tanulado = "DOCUMENTO ANULADO";
                    $fanulado = $com_interna->getFechaDeAnulacion();
                    $pdfTools = new PdfTools();
                    $fnewTmp = $pdfTools->setPdfWatherMark($pathtmp,$tanulado,$fanulado);
                    $url_viewer = $base_path.'/viewerEx.php?fileview='.sprintf("%s/%s",$relf_path,$fnewTmp);
                }
            }
            //*******************************************************************************************
            $this->redirect($url_viewer);
        }
    }
  }

  public function executeShowpdf()
  {
    $this->setLayout(false);
    $com_interna = ComInternaPeer::retrieveByPk(trim($this->getRequestParameter('cominterna_id')));
    $base_path = sfConfig::get('base_simad');
    //*************************************************************************************
    if($com_interna->getTipoComInterna()->getPlantillasComId()){
        $params_margin['top'] = 15;
        $params_margin['left'] = 25;
        $params_margin['buttom'] = 30;
        $params_margin['rigth'] = 25;
        //*********************************************************************************
        //$filegenerate = $com_interna->genPdfByPlantillaCom();
        $filegenerate = $com_interna->generateFileInDisk($params_margin);
    }else{
        $params_margin['top'] = 15;
        $params_margin['left'] = 25;
        $params_margin['buttom'] = 30;
        $params_margin['rigth'] = 25;
        //*********************************************************************************
        //$com_interna->generateDocPdf($com_interna);
        $filegenerate = $com_interna->generateFileInDisk($params_margin);
    }
    //*************************************************************************************
    if(file_exists($filegenerate)){
        $extension = pathinfo($filegenerate, PATHINFO_EXTENSION);
        $url_viewer = $base_path.'/tmp/'.basename($filegenerate);
        //*********************************************************************************
        if(strtolower($extension) == 'pdf'){ 
            $url_viewer = $base_path.'/viewerEx.php?fileview='.basename($filegenerate);
            if($com_interna->getEstadocominternaId() == 4){
                $tanulado = "DOCUMENTO ANULADO";
                $fanulado = $com_interna->getFechaDeAnulacion();
                $pdfTools = new PdfTools();
                $fnewTmp = $pdfTools->setPdfWatherMark($filegenerate,$tanulado,$fanulado);
                $url_viewer = $base_path.'/viewerEx.php?fileview='.$fnewTmp;
            }
        }
        //*********************************************************************************
        $this->redirect($url_viewer);
    }else{
        $this->redirect($base_path.'/no_file_exists.html');
    }
    //*************************************************************************************
    //$this->getResponse()->clearHttpHeaders();
    //$this->redirect($base_path.'/generatepdf.php?cominterna_id='.$com_interna->getCominternaId().$params_margin);
    //*************************************************************************************
    $this->forward404Unless($this->com_interna);
  }  
    
  public function executeShow()
  {
  	$this->verificaPrilegioCerrar("com_interna/show");
  	$this->com_interna = $comInterna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    //**************************************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
	$dir_adj_object  = ParametroPeer::retrieveByPk(15)->getValortexto();
	$alias_com_object  = ParametroPeer::retrieveByPk(26)->getValortexto();
	$extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
	$periodo_com =  $comInterna->getPeriodoId();
	//**************************************************************************************************
    $stateview = trim($this->getRequestParameter('viewstate'));
    $backid = trim($this->getRequestParameter('backid'));
    $this->dataShared = null;
    if(!empty($backid) && !empty($stateview)){
        $this->dataShared = ContenidoUnidadDocumentalPeer::getFormatSharedComUrlView($backid,$comInterna->getPrimaryKey(),$stateview,2);
    }
	//**************************************************************************************************
	$this->directorio_raiz = $dirRaiz;
	$entidad_text = $comInterna->getRegional()->getEntidad()->getDirectorioName();
	$regional_text = $comInterna->getRegional()->getDirectorioName();
	$entidad_text = $entidad_text.'/'.$regional_text;
	$directorio_entidad = $this->directorio_raiz.$entidad_text."/";
	$directorio_com = $dir_adj_object."/".$periodo_com."/";
	$alias_entidad = $alias_com_object.$entidad_text."/";
	$directorio_final = $directorio_entidad.$directorio_com;
	$directorio_alias = $alias_entidad.$directorio_com;
	$file_name = $comInterna->getRadicado();
    $this->ruta_alias_word = $directorio_alias.'/digitword/'.$comInterna->getRadicado();
	//**************************************************************************************************
	$existe_file = false;
    $this->digitDocumentFile = "";    
    foreach($extensions as $format){
        $file_digit = $directorio_final.$file_name.'.'.$format;
    	if(file_exists($file_digit) && ($comInterna->getEstadocominternaId() != 1)){
            $existe_file = true;
            $this->digitDocumentFile = $directorio_alias.$file_name.'.'.$format;
            $comInterna->setEstadodigitalizacionId(2);
            break;
    	}else{
    	   $comInterna->setEstadodigitalizacionId(1);
    	}
    }
	//*************************************************************************************************
    $comInterna->save();
	//*************************************************************************************************
	$user_id = "";
  	$autorizaciones = $this->getAutorizaciones();
  	for($i=0; $i<count($autorizaciones);$i++){
		$user_id .= $autorizaciones[$i];
	}
  	//*************************************************************************************************
  	$list_users = CominternaUsuarioPeer::getListUsersByCom($comInterna->getPrimaryKey());
    $usuario_asignado = null;$firmas_aprobacion = array();
	//*************************************************************************************************
	$usuarios_firman = array();$firmantes_name = array();$this->usuarios_copia = array();
    $usuarios_revisan = array();$revisores_name = array();$firmas_desatendida = true;$usuarios_destino = array();
	foreach($list_users as $interna){
		if($interna->getUsuarioId() == $usuariologuiado || substr_count($user_id,$interna->getUsuarioId()) != 0){
			if($interna->getEstadocominternaId() == 2 ){
				$interna->setEstadocominternaId(3);  
                $interna->save();
                $comInterna->setEstadocominternaId(3);  
                $comInterna->save();
            }
            $this->estado = $interna->getEstadocominterna();
            $this->estadocominterna_id = $interna->getEstadocominternaId();
		}elseif($interna->getRolusuariocominternaId() == 4){
			//en caso q e usuario logueado no sea destinatario ni copia se devuelve el estado del destinatario
			$this->estado = $interna->getEstadocominterna();
            $this->estadocominterna_id = $interna->getEstadocominternaId();
            $usuarios_destino[] = $interna->getUsuario()->getNombreApellido();
        }
        //*********************************************************************************************
        if($interna->getRolusuariocominternaId() == 3){
            $this->usuarios_copia[] = $interna->getUsuario()->getNombreApellido();
		}elseif($interna->getRolusuariocominternaId() == 1){
            $this->creador_id = $interna->getUsuarioId();
            $this->creador_name = $interna->getUsuario()->getNombreApellido();
		}elseif($interna->getRolusuariocominternaId() == 2){//usuarios que firman la comunicacion
			$usuarios_firman[] = $interna->getUsuarioId();
			$firmantes_name[] = $interna->getUsuario()->getNombreApellido();
            if($interna->getCheckAprobacion() == 0){ 
                if(!$interna->getUsuario()->getFirmaDesatendida()){
                    $firmas_aprobacion[] = $interna->getUsuarioId();
                    //*********************************************************************************
                    if(!empty($comInterna->getTipoComInterna()->getPlantillascomId())){
                        if($comInterna->getTipoComInterna()->getPlantillasCom()->getDependenciaId() == $interna->getUsuario()->getDependenciaId()){
                            $firmas_desatendida = $comInterna->getTipoComInterna()->getPlantillasCom()->getFirmaDesatendida() ? true : false;
                        }else{
                            $firmas_desatendida = false;
                        }
                    }else{
                        $firmas_desatendida = false;
                    }
                }
            }
		}elseif($interna->getRolusuariocominternaId() == 5){//usuarios que revisan la comunicacion
			$usuarios_revisan[] = $interna->getUsuarioId();
			$revisores_name[] = $interna->getUsuario()->getNombreApellido();
		}elseif($interna->getRolusuariocominternaId() == 6){//usuarios que radica
			$this->radicador_usuario = $interna->getUsuarioId();
			$this->radicador_name = $interna->getUsuario()->getNombreApellido();
		}
        //*********************************************************************************************
        if($interna->getEstaAsignada()){ $usuario_asignado = $interna->getUsuarioId(); }
	}
    //*************************************************************************************************
    $aprob_urlist = $firmas_desatendida ? CominternaUsuarioPeer::getListUncheckApro($comInterna->getPrimaryKey(),0,array(5)) : 
                        CominternaUsuarioPeer::getListUncheckApro($comInterna->getPrimaryKey());
    //*************************************************************************************************
    if (($clave = array_search($usuariologuiado, $aprob_urlist)) !== false) {
        unset($aprob_urlist[$clave]);
    }    
    //*************************************************************************************************
    $this->firmantes_name = $firmantes_name;
    $this->users_aprueban = CominternaUsuarioPeer::getCountUncheckApro($comInterna->getPrimaryKey());
    $this->usuario_asignado = $usuario_asignado;
    $this->usuarios_firman = $usuarios_firman;
    $this->list_users = $list_users;
    $this->aprobacion_ulist = $aprob_urlist;
    $this->aprobacion_count = count($aprob_urlist);
    $this->destinatarios = implode(",",$usuarios_destino);
	//*************************************************************************************************
    //verificar permiso firma electronica(la misma firma mecanica) para radicar
    $this->permisoRadicarFirmaElectronica = 0;
	if($this->estadocominterna_id == 1){
		$this->permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,implode(",",$usuarios_firman),2);
	}
    //*************************************************************************************************
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeViewImageDigit()
  {
    $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
    //************************************************************************************
    try{
        $cominterna_id = base64_decode($this->getRequestParameter('q_vars'));
        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //********************************************************************************
        $vtoken = $this->getRequestParameter('vtoken') ? trim($this->getRequestParameter('vtoken')) : null;
        $current_token = md5($com_interna->getRadicado().$usuariologuiado.$com_interna->getFechaCreacion());
        if($vtoken == $current_token){
            $response_process = $com_interna->getUriImageDigitById();
        }else{
            $response_process['message'] = 'No tine acceso a este recurso, actualice la pagina e intente de nuevo o comuniquese con el administrador del sistema';
        }
    } catch (\Throwable $th) {
        //throw $th;
        $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
    }
    //***********************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText(json_encode($response_process));
  }

  public function executeSingWsContract()
  {
    try {
        $response_process = array('httpStatus' => 400, 'message' => 'Error interno del servidor');
        //***************************************************************************************************
        $cominterna_id = trim($this->getRequestParameter('cominterna_id'));
        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        $response_process = $com_interna->singDocumentProcessAndes();
    } catch (\Throwable $th) {
        //throw $th;
        $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
    }
    //***************************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText(json_encode($response_process));
  }

  public function executeShowRadicar()
  {
    $this->verificaPrilegioCerrar("com_interna/show");
  	$this->com_interna = $comInterna= ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
    //**************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
	$dir_adj_object  = ParametroPeer::retrieveByPk(15)->getValortexto();
	$alias_com_object  = ParametroPeer::retrieveByPk(26)->getValortexto();
	$extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
	$periodo_com =  $comInterna->getPeriodoId();
    //**************************************************************************************************
    $stateview = trim($this->getRequestParameter('viewstate'));
    $backid = trim($this->getRequestParameter('backid'));
    $this->dataShared = ContenidoUnidadDocumentalPeer::getFormatSharedComUrlView($backid,$comInterna->getPrimaryKey(),$stateview,2);
    //**************************************************************************************************
	$this->directorio_raiz = $dirRaiz;
	$entidad_text = $comInterna->getRegional()->getEntidad()->getDirectorioName();
	$regional_text = $comInterna->getRegional()->getDirectorioName();
	$entidad_text = $entidad_text.'/'.$regional_text;
	$directorio_entidad = $this->directorio_raiz.$entidad_text."/";
	$directorio_com = $dir_adj_object."/".$periodo_com."/";
	$alias_entidad = $alias_com_object.$entidad_text."/";
	$directorio_final = $directorio_entidad.$directorio_com;
	$directorio_alias = $alias_entidad.$directorio_com;
	$file_name = $comInterna->getRadicado();
    $this->ruta_alias_word = $directorio_alias.'/digitword/'.$comInterna->getRadicado();
    $this->msg = trim($this->getRequestParameter('is_redirect')) ? trim($this->getRequestParameter('is_redirect')) : 0;
	//**************************************************************************************************
	$existe_file = false;
    $this->digitDocumentFile = "";
    foreach($extensions as $format){
    	if(file_exists($directorio_final.$file_name.'.'.$format)){
    		$existe_file = true;
            $this->digitDocumentFile = $directorio_alias.$file_name.'.'.$format;
            $comInterna->setEstadodigitalizacionId(2);
            break;
    	}else{
    	   $comInterna->setEstadodigitalizacionId(1);
    	}
    }
	//*************************************************************************************************
    $comInterna->save();
	//*************************************************************************************************
	$user_id = "";
  	$autorizaciones = $this->getAutorizaciones();
  	for($i=0; $i<count($autorizaciones);$i++){
		$user_id .= $autorizaciones[$i];
	}
  	//*************************************************************************************************
      $this->usuarios_copia = array();$usuario_asignado = null;
  	$result = CominternaUsuarioPeer::getListUsersByCom($comInterna->getPrimaryKey());    
	foreach($result as $usuariocom_interna){
		if($usuariocom_interna->getUsuarioId() == $usuariologuiado || substr_count($user_id,$usuariocom_interna->getUsuarioId()) != 0){
			if($usuariocom_interna->getEstadocominternaId() == 2 ){ //&& $comInterna->getEstadodigitalizacionId()==2
				$usuariocom_interna->setEstadocominternaId(3);  
  	  	    	$usuariocom_interna->save();
                //*************************************************************************************
                $comInterna->setEstadocominternaId(3);  
  	  	    	$comInterna->save();
  	  	    	//$estado = $interna->getEstadocominterna();
  	  	    }
  	  	    $this->estado = $usuariocom_interna->getEstadoComInterna();
            $this->estado_id = $usuariocom_interna->getEstadocominternaId();
		}elseif($usuariocom_interna->getRolusuariocominternaId() == 4){
			//en caso q e usuario logueado no sea destinatario ni copia se devuelve el estado del destinatario
			$this->estado = $usuariocom_interna->getEstadoComInterna();
            $this->estado_id = $usuariocom_interna->getEstadocominternaId();
		}
        //*********************************************************************************************
        if($usuariocom_interna->getRolusuariocominternaId() == 3){
            $this->usuarios_copia[] = $usuariocom_interna->getUsuario()->getNombreApellido();
		}elseif($usuariocom_interna->getRolusuariocominternaId() == 1){
            $this->creador_id = $usuariocom_interna->getUsuarioId();
            $this->creador_name = $usuariocom_interna->getUsuario()->getNombreApellido();
		}elseif($usuariocom_interna->getRolusuariocominternaId() == 2){//usuarios que firman la comunicacion
			$usuarios_firman[] = $usuariocom_interna->getUsuarioId();
			$firmantes_name[] = $usuariocom_interna->getUsuario()->getNombreApellido();
		}elseif($usuariocom_interna->getRolusuariocominternaId() == 5){//usuarios que revisan la comunicacion
			$usuarios_revisan[] = $usuariocom_interna->getUsuarioId();
			$revisores_name[] = $usuariocom_interna->getUsuario()->getNombreApellido();
		}elseif($usuariocom_interna->getRolusuariocominternaId() == 6){//usuarios que radica
			$this->radicador_usuario = $usuariocom_interna->getUsuarioId();
			$this->radicador_name = $usuariocom_interna->getUsuario()->getNombreApellido();
		}
        //********************************************************************************************
        if($usuariocom_interna->getEstaAsignada()){ $usuario_asignado = $usuariocom_interna->getUsuarioId(); }
	}
    //************************************************************************************************ 
	$this->destinatarios = array();
	$this->firmas = array(); 
	$this->radicador = array();
    $this->destinatarios[] = $this->getDestinatario($comInterna->getCominternaId());
    $this->firmas[] = $this->getFirstUsurioFirmaName($comInterna->getCominternaId());
    $this->radicador[] = $this->creador_name;
    $this->radicador_id = $this->creador_id;
    //************************************************************************************************
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeCreate()
  {
    $this->SetDataCreationCom();
    $this->setTemplate('edit');
  }
  
  public function executeCreatePlantillaWord()
  {
    $this->SetDataCreationCom();
  }
  
  public function executeCreateRadicarWord()
  {
    $this->SetDataCreationCom();	
  }
  
  public function executeSaveCreateRadicarWord()
  {
    if (!$this->getRequestParameter('cominterna_id'))
    {
      $com_interna = new ComInterna();
      $com_interna->setMarca(0);
    }
    else
    {
      $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
      $this->forward404Unless($com_interna);
    }    
    //**************************************************************************************************************
	$permiso_regional =  $this->getRequestParameter('regional_id');    
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $objUsuarioLoguiado=UsuarioPeer::retrieveByPk($usuariologuiado);
    //**************************************************************************************************************
    if(!$objUsuarioLoguiado){
	    $dependencia_id=1;
        $regional_id=1;	
    }else{
       if($objUsuarioLoguiado->getDependenciaId()){
		    $dependencia_id=$objUsuarioLoguiado->getDependenciaId();
       }else{
	    	$dependiencia_id=1;	
       }
       if( $objUsuarioLoguiado->getRegionalId() ){
            $regional_id=$objUsuarioLoguiado->getRegionalId();
       }else{
	        $regional_id=1;
       }
    }
    //**************************************************************************************************************
    if($permiso_regional ){
       $regional_id = $permiso_regional; 
    }    
    //**************************************************************************************************************
    $com_interna->setEstadocominternaId(1);
    $yearActual = date("Y");	
	$com_interna->setPeriodoId($yearActual);
    //**************************************************************************************************************
	//$com_interna->setIdiomaId($this->getRequestParameter('idioma_id'));
    //$com_interna->setTamFuenteLetra( $this->getRequestParameter('tam_fuente_letra'));
    //**************************************************************************************************************
    $com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id'));    
	$com_interna->setEstadodigitalizacionId(1);
    $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
	$com_interna->setReferencia($this->getRequestParameter('referencia'));
    //**************************************************************************************************************
    $usernameloguiado = $this->getUser()->getAttribute('username', '', 'subscriber');	
    $com_interna->setRuta('');
    $com_interna->setRadicado("Sin Radicar");
    $com_interna->setEsCopia(0);
    $com_interna->setFolios($this->getRequestParameter('folios'));
    $com_interna->setAnexos($this->getRequestParameter('anexos'));
    $com_interna->setRequiereRespuesta($this->getRequestParameter('requiere_respuesta'));
    $com_interna->setEstaentregado(0);
    $com_interna->setDependenciaId($dependencia_id);
    $com_interna->setRegionalId($regional_id);
    //**************************************************************************************************************
    if($this->getRequestParameter('url_file_word')!=""){
        $template_word_upload = $this->getRequestParameter('url_file_word');
        $folder_compose = $yearActual . '/' . 'word_template';
        if(trim($com_interna->getUrlFileWord())){
            $com_interna->setUrlFileWord(ComInterna::moveFilesWordTempalteCreate($template_word_upload,$folder_compose,($com_interna->getUrlFileWord())));
        }else{
            $com_interna->setUrlFileWord(ComInterna::moveFilesWordTempalteCreate($template_word_upload,$folder_compose));
        }
	}else{    	
        $com_interna->setUrlFileWord('');
    }
    $com_interna->save();
    //**************************************************************************************************************
    if (!$this->getRequestParameter('cominterna_id'))
    {
      $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());
    }
    //**************************************************************************************************************
    $c2=new Criteria();
    $c2->add(CargoUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $c2->add(CargoUsuarioPeer::ES_PRINCIPAL,true);
    $cargoUsuario= CargoUsuarioPeer::doSelectOne($c2);
    $cargo_radicador=$cargoUsuario->getCargousuarioId();
    //**************************************************************************************************************
    $cargoDestinatario= $this->getRequestParameter('cargousuarioId');
    $cargoFirma= $this->getRequestParameter('cargousuarioIdFirma');
    $cargoUserCopia = $this->getRequestParameter('cargousuarioIdCopias');
    //**************************************************************************************************************
	$this->borrarCominternaUsuario($com_interna->getPrimaryKey());
	$this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),1,$cargo_radicador);
    //**************************************************************************************************************
    if($this->getRequestParameter('firmanteId'))
       $this->insertaCominternaUsuario($this->getRequestParameter('firmanteId'),$com_interna->getPrimaryKey(),2,$cargoFirma);
     else
       $this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),2,null);
    //**************************************************************************************************************
	$this->insertaCominternaUsuario($this->getRequestParameter('copiaInternaId'),$com_interna->getPrimaryKey(),3,$cargoUserCopia);
	$this->insertaCominternaUsuario($this->getRequestParameter('destinatarioId'),$com_interna->getPrimaryKey(),4,$cargoDestinatario);
    //**************************************************************************************************************       
	$usurioFirma=$this->getFirstUsurioFirmaId($com_interna->getCominternaId());   
    if(!$usurioFirma){
        $usurioFirma=$usuariologuiado;    
	} 
    //************************************************************************************************************** 
    $objUsuarioFirma=UsuarioPeer::retrieveByPk($usurioFirma);	
    $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());     
    $com_interna->setDependenciaId($objUsuarioFirma->getDependenciaId());    
    $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());  
	$com_interna->save();   
    //**************************************************************************************************************
    //para editar
    $this->com_interna=$com_interna;
    $this->creador = "";
    $this->creador_name = "";
    $this->firmanteId = "";
	$this->firmanteName = "";
    $this->copiaInternaId = "";
  	$this->copiaInternaName = "";
  	$this->destinatarioId = "";
  	$this->destinatarioName = "";
  	$this->cargousuarioId = "";
  	$this->cargousuarioIdCopias = "";
  	$this->cargousuarioIdFirma = "";
  	$this->Radicar=" Radicar";
  	$this->editando=1;
    $this->wfinstancia_id = $this->getRequestParameter('wfinstancia_id');
	$this->wfbitacora_id = $this->getRequestParameter('wfbitacora_id');
    $this->permisoFirmaOtroAutorizado = 0;
    $this->regional_seleccionada = 0;
    $this->permisoRadicarOtraRegional = 0;
    $this->es_otra_dependencia = 0;
	//**************************************************************************************************************
	if($this->getRequestParameter('generada_tipo' ) >0){
	    $this->referencia="Comunicacion WF-0". $this->getRequestParameter('generada_tipo');
        $this->contenido =urldecode($this->getRequestParameter('generada_msg'));
	}
	//******************************************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";    
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologueado)){       
       $this->permisoRadicarOtraRegional = 1;
       $c = new Criteria();
       $c->add(ComPermisoRegionalPeer::USUARIO_ID,$usuariologueado);       
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //**************************************************************************************************************/
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";
    $this->permisoRadicarFirmaAut = 0;
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologueado)){       
       $this->permisoRadicarFirmaAut = 1;
    }
    //**************************************************************************************************************
	if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologueado)){
		$this->es_otra_dependencia=1;
    }
    //**************************************************************************************************************
	$respo = CominternaUsuarioPeer::getListUsersByCom($this->com_interna->getCominternaId());
	//**************************************************************************************************************
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
		}elseif($res->getRolusuariocominternaId()==2){
			$this->firmanteId .= $res->getUsuarioId().",";
			$this->firmanteName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioIdFirma .= $res->getCargoUsuarioId().",";
		}elseif($res->getRolusuariocominternaId()== 3){
			$this->copiaInternaId .= $res->getUsuarioId().",";
			$this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioIdCopias .= $res->getCargoUsuarioId().",";
		}
		elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioId .= $res->getCargoUsuarioId().",";
		}			    	    
		
	}
    //**************************************************************************************************************    
    $this->setTemplate('createRadicarWord');     
  }
  
  public function executeSingComCheck()
  {
    try {
        $status = 400;
        $cominterna_id = !empty($this->getRequestParameter('cominterna_id')) ? trim($this->getRequestParameter('cominterna_id')) : null;
        $response_data = array( 'status' => 400, 'message' => 'error interno no se realizo la aprobaci&oacute;n');
        //$usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
        //************************************************************************************************
        if($cominterna_id != null){
            $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
            $usuario_asignado = CominternaUsuarioPeer::setNextUserProceso($com_interna->getPrimaryKey());            
            //********************************************************************************************
            if($usuario_asignado != null){
                $status = 200;
                $nombre_asignado = $usuario_asignado->getUsuario()->getFullNombre();
                $email_destino = !empty($usuario_asignado->getUsuario()->getEmail()) ? trim($usuario_asignado->getUsuario()->getEmail()) : null;
                $com_interna->sendMailAprob($email_destino);
            }
            //********************************************************************************************
            $response_data = array( 'status' => $status, 'message' => 'Se envio la comunicaci&oacute;n al siguiente usuario del proceso ('.$nombre_asignado.')');
        }else{
            $response_data = array( 'status' => $status, 'message' => 'Error la informaci&oacute;n, la comunicaci&oacute;n no es valida');
        }
    } catch (\Exception $ex) {
        $response_data = array( 'status' => 400, 'message' => $ex->getMessage());
    }
    //****************************************************************************************************
    $array = json_encode($response_data);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }

  public function executeSaveCreatePlantillaWord()
  {
    if (!$this->getRequestParameter('cominterna_id'))
    {
      $com_interna = new ComInterna();
      $com_interna->setMarca(0);
    }
    else
    {
      $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
      $this->forward404Unless($com_interna);
    }
    //******************************************************************************************************************    
	$permiso_regional =  $this->getRequestParameter('regional_id');    
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $objUsuarioLoguiado=UsuarioPeer::retrieveByPk($usuariologuiado);
    //******************************************************************************************************************
    if(!$objUsuarioLoguiado){
	    $dependencia_id=1;
        $regional_id=1;	
    }else{
        if($objUsuarioLoguiado->getDependenciaId()){
            $dependencia_id=$objUsuarioLoguiado->getDependenciaId();
        }else{
            $dependiencia_id=1;	
        }
        if( $objUsuarioLoguiado->getRegionalId() ){
            $regional_id=$objUsuarioLoguiado->getRegionalId();
        }else{
            $regional_id=1;
        }
    }   
    //**************************************************************************************************************
    if($permiso_regional ){
       $regional_id = $permiso_regional; 
    }    
    //**************************************************************************************************************
    $com_interna->setEstadocominternaId(1);
    $yearActual = date("Y");	
	$com_interna->setPeriodoId($yearActual);
    //**************************************************************************************************************	
	//$com_interna->setIdiomaId($this->getRequestParameter('idioma_id'));
    //$com_interna->setTamFuenteLetra( $this->getRequestParameter('tam_fuente_letra'));
    //**************************************************************************************************************
    $com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id'));    
	$com_interna->setEstadodigitalizacionId(1);
    $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
	$com_interna->setReferencia($this->getRequestParameter('referencia'));
    $contenido = $this->getRequestParameter('contenido');
    $com_interna->setContenido($contenido);
    $usernameloguiado = $this->getUser()->getAttribute('username', '', 'subscriber');	
    $com_interna->setRuta('');  
    $com_interna->setUrlFileWord("PLANTILLA_WORD");
    $com_interna->setRadicado("Sin Radicar");
    $com_interna->setEsCopia(0);
    $com_interna->setFolios($this->getRequestParameter('folios'));
    $com_interna->setAnexos($this->getRequestParameter('anexos'));
    $com_interna->setRequiereRespuesta($this->getRequestParameter('requiere_respuesta'));
    $com_interna->setEstaentregado(0);
    $com_interna->setDependenciaId($dependencia_id);
    $com_interna->setRegionalId($regional_id);
    $com_interna->save();     
    //******************************************************************************************************************
    if (!$this->getRequestParameter('cominterna_id'))
    {
      $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());
    }
    //******************************************************************************************************************
    $c2=new Criteria();
    $c2->add(CargoUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $c2->add(CargoUsuarioPeer::ES_PRINCIPAL,true);
    $cargoUsuario= CargoUsuarioPeer::doSelectOne($c2);
    $cargo_radicador=$cargoUsuario->getCargousuarioId();
    //******************************************************************************************************************
    $cargoDestinatario= $this->getRequestParameter('cargousuarioId');
    $cargoFirma= $this->getRequestParameter('cargousuarioIdFirma');
    $cargoUserCopia = $this->getRequestParameter('cargousuarioIdCopias');
    //******************************************************************************************************************
	$this->borrarCominternaUsuario($com_interna->getPrimaryKey());
	$this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),1,$cargo_radicador); 
    if($this->getRequestParameter('firmanteId'))
       $this->insertaCominternaUsuario($this->getRequestParameter('firmanteId'),$com_interna->getPrimaryKey(),2,$cargoFirma);
     else
       $this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),2,null);
    //******************************************************************************************************************
	$this->insertaCominternaUsuario($this->getRequestParameter('copiaInternaId'),$com_interna->getPrimaryKey(),3,$cargoUserCopia);
	$this->insertaCominternaUsuario($this->getRequestParameter('destinatarioId'),$com_interna->getPrimaryKey(),4,$cargoDestinatario);
    //******************************************************************************************************************   
    $usurioFirma=$this->getFirstUsurioFirmaId($com_interna->getCominternaId());   
    if(!$usurioFirma){
   	    $usurioFirma=$usuariologuiado;    
    }
    //****************************************************************************************************************** 
    $objUsuarioFirma=UsuarioPeer::retrieveByPk($usurioFirma);	
    $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());     
    $com_interna->setDependenciaId($objUsuarioFirma->getDependenciaId());    
    $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());  
	$com_interna->save();   
    //******************************************************************************************************************
    //para editar
    $this->com_interna=$com_interna;
    $this->creador = "";
    $this->creador_name = "";
    $this->firmanteId = "";
	$this->firmanteName = "";
    $this->copiaInternaId = "";
  	$this->copiaInternaName = "";
  	$this->destinatarioId = "";
  	$this->destinatarioName = "";
  	$this->cargousuarioId = "";
  	$this->cargousuarioIdCopias = "";
  	$this->cargousuarioIdFirma = "";
  	$this->Radicar=" Radicar";
  	$this->editando=1;
    $this->wfinstancia_id = $this->getRequestParameter('wfinstancia_id');
	$this->wfbitacora_id = $this->getRequestParameter('wfbitacora_id');
    $this->permisoFirmaOtroAutorizado = 0;
    $this->regional_seleccionada = 0;
    $this->permisoRadicarOtraRegional = 0;
    $this->es_otra_dependencia = 0;
	//******************************************************************************************************************
	if($this->getRequestParameter('generada_tipo' ) >0){
	    $this->referencia="Comunicacion WF-0". $this->getRequestParameter('generada_tipo');
        $this->contenido =urldecode($this->getRequestParameter('generada_msg'));
	}
	//******************************************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";    
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologueado)){       
       $this->permisoRadicarOtraRegional = 1;
       $c = new Criteria();
       $c->add(ComPermisoRegionalPeer::USUARIO_ID,$usuariologueado);       
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //**************************************************************************************************************/
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";
    $this->permisoRadicarFirmaAut = 0;
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologueado)){       
       $this->permisoRadicarFirmaAut = 1;
    }
    //**************************************************************************************************************
	if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologueado)){
		$this->es_otra_dependencia=1;
    }
    //**************************************************************************************************************
	$respo = CominternaUsuarioPeer::getListUsersByCom($this->com_interna->getCominternaId());
	//**************************************************************************************************************        
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
		}elseif($res->getRolusuariocominternaId()==2){
			$this->firmanteId .= $res->getUsuarioId().",";
			$this->firmanteName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioIdFirma .= $res->getCargoUsuarioId().",";
		}elseif($res->getRolusuariocominternaId()== 3){
			$this->copiaInternaId .= $res->getUsuarioId().",";
			$this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioIdCopias .= $res->getCargoUsuarioId().",";
		}
		elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
			$this->cargousuarioId .= $res->getCargoUsuarioId().",";
		}			    	    
		
	}
    //******************************************************************************************************************        
    $this->setTemplate('createPlantillaWord');     
  }
  
  public function SetDataCreationCom()
  {
    $parametro_firmas = ParametroPeer::retrieveByPk(62); 
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologueado);
  	$this->verificaPrilegio("com_interna/create");
    $this->com_interna = new ComInterna();
    $this->cargousuarioIdFirma = "";
    //****************************************************************************************
    if($parametro_firmas){
      $this->firmanteId = $usuario->getPrimaryKey().',';
  	  $this->firmanteName = $usuario->getNombreAll().',';
  	  $this->cargousuarioIdFirma = $this->getCargoUsuario($usuario->getPrimaryKey()).',';
    }else{
      	$this->firmanteId = "";
	    $this->firmanteName = "";
	    $this->cargousuarioIdFirma = "";
    }
    //****************************************************************************************
    $this->copiaInternaId = "";
    $this->copiaInternaName = "";
    $this->destinatarioId = "";
  	$this->destinatarioName = "";
  	$this->cargousuarioId = "";
  	$this->cargousuarioIdCopias = "";
    $this->cargousuarioIdAprob = "";
    $this->aprobadores = "";
    $this->aprobadores_name = "";
  	//$this->cargousuarioIdFirma = $this->getCargoUsuario($this->firmanteId).',';
    $this->permisoFirmaOtroAutorizado = 0;
    $this->regional_seleccionada = 0;
    $this->permisoRadicarOtraRegional = 0;
    $this->es_otra_dependencia = 0;
    $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
	$this->wfbitacora_id=$this->getRequestParameter('wfbitacora_id');
    $this->revisorUserId = "";
    $this->cargousuarioIdRevisor = "";
    $this->revisorUserNames = "";
    $this->expediente_id = null;
    //**************************************************************************************************************
    $this->wfinstancia_id = $this->getRequestParameter('wfinstancia_id');
    $this->wfbitacora_id = $this->getRequestParameter('wfbitacora_id');
    //**************************************************************************************************************
    /* wf begin */
    if($this->wfinstancia_id >0){
        $this->com_interna->setReferencia("Comunicacion WF-0". $this->getRequestParameter('generada_tipo'));
        $this->com_interna->setContenido( urldecode($this->getRequestParameter('generada_msg'))  );	
    }
    /* wf end   */
    //**************************************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";    
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologueado)){       
       $this->permisoRadicarOtraRegional = 1;
       $c = new Criteria();
       $c->add(ComPermisoRegionalPeer::USUARIO_ID,$usuariologueado);       
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";
    $this->permisoRadicarFirmaAut = 0;
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologueado)){       
       $this->permisoRadicarFirmaAut = 1;
    }
	//**************************************************************************************** 
	$this->permisoRadicarFirmaElectronica = 1;
    //****************************************************************************************
    $this->es_otra_dependencia=0;
	if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologueado)){
		$this->es_otra_dependencia=1;
    }
    //**************************************************************************************** 
    $currentFormConMembrete = "CREAR_INTERNA_CON_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologueado)){
       $this->permisoCrearConMembrete = 1;
       $this->usar_membrete = $usuario->getRegional()->getEntidad()->getUsarMembrete();
    }
    //****************************************************************************************
    $this->Radicar=" ";
    $this->editando=0;	
  }
  
  public function getObjUsuarioInternaByRol($comId=0,$usuario_id=0,$rol_id=0)
  {
    $cap=new Criteria();
    $cap->add(CominternaUsuarioPeer::COMINTERNA_ID,$comId);
    $cap->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, $rol_id);
    $cap->add(CominternaUsuarioPeer::USUARIO_ID, $usuario_id);
    $euser_aprob = CominternaUsuarioPeer::doSelectOne($cap);
    return $euser_aprob;
  }
  
  public function getCargoUsuario($usuario_id)
  {   	
    $c = new Criteria();
    $c->add(CargoUsuarioPeer::USUARIO_ID,$usuario_id);
    $c->add(CargoUsuarioPeer::ES_PRINCIPAL,true);
    $cargo_usuario = CargoUsuarioPeer::doSelectOne($c);    
    return $cargo_usuario->getCargousuarioId();
  }
  
  public function getNumeroRadicacion($regional,$dependencia)
  {
    $periodoActual=date("Y"); 
	$parametro = ParametroPeer::retrieveByPk(4);
	$formaRad=$parametro->getCodigo();
	//************************************************************************************
	$conexion = Propel::getConnection();
	if($formaRad=="REG"){
	    $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$regional."  and %s=".$periodoActual." ";
	   	$consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::REGIONAL_ID,ComInternaPeer::PERIODO_ID);
	}elseif($formaRad=="DEP"){
	    $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$dependencia."  and %s=".$periodoActual." ";
	   	$consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::DEPENDENCIA_ID,ComInternaPeer::PERIODO_ID);
	}elseif($formaRad=="GEN"){
        $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=".$periodoActual." ";
   	    $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::PERIODO_ID);
	}
    //************************************************************************************
    $sentencia = $conexion->prepare($consulta);
    $sentencia->execute();
    $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
    return ($resultset->max + 1);
    //************************************************************************************
  }  
  
  public function executeRadicar()
  {
    $this->verificaPrilegio("com_interna/radicar");
    $this->com_interna = $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    //********************************************************************************************
    $usuarios_com = $com_interna->getUsuariosListComIds();
    $permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,$usuarios_com['firmas'],2);
    if($com_interna->getFirmaDesatendida() == 0){
        if($permisoRadicarFirmaElectronica == 0){
            return $this->redirect($this->getRequest()->getScriptName().'/com_enviada/edit?comenviada_id='.$com_interna->getPrimaryKey());
        }
    }
    //********************************************************************************************
    $ufirmas_list = preg_split("/[,]+/",$usuarios_com['firmas'], -1, PREG_SPLIT_NO_EMPTY);
    $udestinatarios_list = preg_split("/[,]+/",$usuarios_com['destinatario'], -1, PREG_SPLIT_NO_EMPTY);
	//********************************************************************************************
    $com_interna_anterior = clone $com_interna;
	//********************************************************************************************
    $permiso_regional =  $com_interna->getRegionalId();        
    $destinatario_id = count($udestinatarios_list) > 0 ? $udestinatarios_list[0] : 0;
    //********************************************************************************************
    $firmante = $ufirmas_list[0];
    if(!$firmante){ $firmante = $this->getUser()->getAttribute('usuario_id','', 'subscriber'); }
    //********************************************************************************************
    $objUsuarioFirmante = UsuarioPeer::retrieveByPk($firmante);
    $regional = $objUsuarioFirmante->getRegionalId();
    if($permiso_regional != $regional){ $regional = $permiso_regional; }
    //********************************************************************************************
    $usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
    $depen_codigo = "";
    //********************************************************************************************
    if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologuiado)){
        $obj_dependencia = DependenciaPeer::retrieveByPk($com_interna->getDependenciaId());			 
        $depen_codigo = $obj_dependencia->getCodigo();
        $dependencia = $obj_dependencia->getDependenciaId();
    }else{
        $depen_codigo = $objUsuarioFirmante->getDependencia()->getCodigo();
        $dependencia = $objUsuarioFirmante->getDependenciaId();
    }
    //********************************************************************************************
    //$consecutivo_radicado = ComInternaPeer::getConsecutivoRadicacion($regional,$dependencia);
    //if(!$consecutivo_radicado){ $consecutivo_radicado = 0; }
    $com_interna->setDependenciaId($dependencia);
	$com_interna->setPeriodoId(date("Y"));
    if($this->getRequestParameter('reenviar') == 1){ $com_interna->setNumeroRadicacion(0); }
    if($com_interna->getEstadocominternaId() == 1){ $com_interna->setNumeroRadicacion(0); }
    $radicado = $com_interna->getRadicadoFormat(null,$regional,$depen_codigo);
    //********************************************************************************************
    if($this->getRequestParameter('reenviar') != 1 && $com_interna->getEstadocominternaId() == 1){
        $com_interna->setRadicado($radicado);
        $com_interna->setEstadocominternaid(2);
    }
    //********************************************************************************************
    $com_interna->setRegionalId($regional); 
    $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
    $com_interna->save();
    //********************************************************************************************
    if($this->getRequestParameter('reenviar') == 1){
        CominternaUsuarioPeer::updateEstados($com_interna->getPrimaryKey(),$this->getRequestParameter('reenviar'),true,5);
    }elseif($com_interna->getRequiereRespuesta()){
        CominternaUsuarioPeer::updateEstados($com_interna->getPrimaryKey(),$this->getRequestParameter('reenviar'),true,5);
    }else{          
        CominternaUsuarioPeer::updateEstados($com_interna->getPrimaryKey(),$this->getRequestParameter('reenviar'));
    }       
    //********************************************************************************************
    $cuser_current = CominternaUsuarioPeer::getCurrentUserAsignado($com_interna->getPrimaryKey());
    if($cuser_current != null){
        $cuser_current->setEstaAsignada(0);
        $cuser_current->setFechaAprobacion(date("Y-m-d G:i:s"));
        $cuser_current->setCheckAprobacion(1);
        $cuser_current->save();
    }
    //********************************************************************************************
    $cuser_destino = CominternaUsuarioPeer::getUserComByRol($com_interna->getPrimaryKey(),4);
    if($cuser_destino != null){
        $cuser_destino->setEstaAsignada(1);
		$cuser_destino->setCheckAprobacion(0);
		$cuser_destino->setFechaAsigna(date("Y-m-d G:i:s"));
        $cuser_destino->save();
    }
    //********************************************************************************************
    $this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),6,null,2);
    //********************************************************************************************
    $redir = RedireccionPeer::UserIsRedirect($destinatario_id);
    if( $redir != null){//redireccion
        $cargo_redireccionado = CargoUsuarioPeer::getCargoUsuarioByIdUser($redir);
        $this->insertaCominternaUsuario($redir,$com_interna->getPrimaryKey(),3,$cargo_redireccionado);
        $msg = 1;
        $this->redireccionado = UsuarioPeer::retrieveByPk($redir);	
    }else{
        $msg = 0;	
    }
    //********************************************************************************************
    if($com_interna->getConsecutivoResp()){
        $c = new Criteria();
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$com_interna->getConsecutivoResp());
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
        $estado_interna = CominternaUsuarioPeer::doSelect($c);
        foreach($estado_interna as $result){
            $result->setEstadocominternaId(9);
            $result->save();
        }
        //****************************************************************************************
        $interna_origen = ComInternaPeer::retrieveByPK($com_interna->getConsecutivoResp());
        $interna_origen->setEstadocominternaId(9);
        $interna_origen->save();
        //****************************************************************************************
        if(empty($interna_origen->getExpedienteId()) && empty($interna_origen->getTipoDocumentalCod())){
            if(!empty($com_interna->getExpedienteId()) && !empty($com_interna->getTipoDocumentalCod())){
                $origentransfer_id = 1;
                $interna_origen->addNewTransferenciaAndContenido($origentransfer_id,$objUsuarioFirmante->getPrimaryKey());
            }
        }
    }
    //********************************************************************************************
    if(!empty($com_interna->getExpedienteId()) && !empty($com_interna->getTipoDocumentalCod())){
        $origentransfer_id = 1;
        $com_interna->addNewTransferenciaAndContenido($origentransfer_id,$objUsuarioFirmante->getPrimaryKey());
    }
    //********************************************************************************************
    $this->guardarAuditoria($com_interna_anterior,$com_interna);
    //********************************************************************************************
    $buzon = 2;
    ComInternaPeer::initWorkflowCom($com_interna,$destinatario_id,$objUsuarioFirmante->getPrimaryKey(),$buzon);
    //********************************************************************************************
    //Envio email a los usuarios copia de la comunicacion
    $alerta_usuario_copias = array();
    $alerta_usuario_copias = $this->getCopiaIdComInterna($com_interna->getCominternaId());
    $encabezado_cuerpo_copia = "Este es un mensaje para informarle que se le ha generado una copia de la siguiente comunicación interna:";
    foreach($alerta_usuario_copias as $user_copia_alerta){
        $com_interna->envioEmail($user_copia_alerta,$firmante,$encabezado_cuerpo_copia);
    }
    //********************************************************************************************
    if(count($udestinatarios_list) > 1){
        unset($udestinatarios_list[0]);
        $com_interna->addNewBatchComInterna($udestinatarios_list,$objUsuarioFirmante,$regional,$depen_codigo);
    }
    //********************************************************************************************
    if(trim($this->getRequestParameter('vshow')) == md5($com_interna->getPrimaryKey())){
        $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getPrimaryKey());
    }else{
        $this->redirect($this->getRequest()->getScriptName().'/com_interna/showRadicar?cominterna_id='.$com_interna->getPrimaryKey().'&is_redirect='.$msg);
    }
  }
  
  
  public function createInstanciaBitacora($com_interna_id,$usuario_id,$instancia_id,$buzon)
  {
    $wf_instancia = WfInstanciaPeer::retrieveByPk($instancia_id);
    $WfActividadId=$wf_instancia->getWfactividadtransicionId();
    $estadoActividad="1";
    $wf_instancia_bitacora = new WfInstanciaBitacora();
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $wf_instancia_bitacora->setUsuarioId($usuariologuiado);
    //$wf_instancia_bitacora->setComenviadaId();
    $wf_instancia_bitacora->setWfactividadtransicionId($WfActividadId);
    $wf_instancia_bitacora->setCominternaId($com_interna_id);
    $wf_instancia_bitacora->setWfinstanciaId($instancia_id);
    //$wf_instancia_bitacora->setComrecibidaId();
    $wf_instancia_bitacora->setFechaI(date("Y-m-d G:i:s"));
    $wf_instancia_bitacora->setFechaF(date("Y-m-d G:i:s"));
    $wf_instancia_bitacora->setObservaciones("Radicado en ".date("Y-m-d G:i:s"));
    $wf_instancia_bitacora->setError("0");
    $wf_instancia_bitacora->setEsActual(0);
    $wf_instancia_bitacora->setFechaLimite(date("Y-m-d G:i:s"));
    $wf_instancia_bitacora->setBuzon($buzon);
    $wf_instancia_bitacora->setBuzonId($buzon);
    $wf_instancia_bitacora->setEstadoActividad($estadoActividad);
    //$wf_instancia_bitacora->setScript();
    //$wf_instancia_bitacora->setScriptParams();
    $wf_instancia_bitacora->save();

    return $wf_instancia_bitacora->getWfinstanciabitacoraId();
  }

  
  
  public function createWF($com_interna_id,$usuario_id,$tipo_interna)
  {   
    //busca el flujo
    $c = new Criteria();  	
    $c->add(WfFlujoPeer::TIPOCOMINTERNA_ID,$tipo_interna);
    $flujo = WfFlujoPeer::doSelectOne($c);
    //***********************************************************************************************
    if($flujo)
    {
        //busca la primera transicion
        $c = new Criteria();
        $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$flujo->getPrimaryKey());
        $c->add(WfActividadTransicionPeer::ES_DESTINO,1);
        $c->add(WfActividadTransicionPeer::ORDEN,1);
        $wf_actcount = WfActividadTransicionPeer::doCount($c);        
        if($wf_actcount)
        {
            $Wf_Actividad_Transicion = WfActividadTransicionPeer::doSelectOne($c);
            //****************************************************************************************
            $wf_instancia = new WfInstancia();
            $wf_instancia->setUsuarioId($usuario_id);
            //$wf_instancia->setWfActividadId($Wf_Actividad_Transicion->getWfActividadId());
            $wf_instancia->setWfactividadtransicionId($Wf_Actividad_Transicion->getPrimaryKey());
            $wf_instancia->setCominternaId($com_interna_id);
            //$wf_instancia->setComrecibidaId();
            $wf_instancia->setWfFlujoId($flujo->getPrimaryKey());
            $wf_instancia->setEstaAbierta(1);
            $wf_instancia->setFechaI(date("Y-m-d G:i:s"));
            //$wf_instancia->setFechaF(date("Y-m-d  h:m:s"));
            $wf_instancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
            $wf_instancia->setObservaciones("WorkFlow radicado en ".date("Y-m-d G:i:s"));
            $wf_instancia->save();
            //$this->createBitacora();
            return $wf_instancia->getPrimaryKey();
        }else{
            return 0;
        }
    }else{
		return 0;
	}
  }  
  
 public function executeEditSinCR()
  {
  	$this->verificaPrilegio("com_interna/edit");
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $this->creador = "";
	$this->creador_name = "";
	$this->firmanteId = "";
	$this->firmanteName = "";
    $this->copiaInternaId = "";
	$this->copiaInternaName = "";
	$this->destinatarioId = "";
    $this->cargousuarioId = "";
	$this->cargousuarioIdCopias = "";
	$this->cargousuarioIdFirma = "";
	$this->destinatarioName = "";
    $this->radicadorInternaId = "";
	$this->Radicar=" Radicar";
	$this->editando=1;
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologueado);
    //****************************************************************************************
    $this->permisoFirmaOtroAutorizado = 0;
    $this->regional_seleccionada = 0;
    $this->permisoRadicarOtraRegional = 0;
    $this->es_otra_dependencia = 0;
	//****************************************************************************************
	$this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
	$this->wfbitacora_id=$this->getRequestParameter('wfbitacora_id');
    //****************************************************************************************
	if($this->getRequestParameter('generada_tipo' ) >0){
	    $this->referencia="Comunicacion WF-0". $this->getRequestParameter('generada_tipo');
        $this->contenido =urldecode($this->getRequestParameter('generada_msg'));
	}
	//****************************************************************************************
	$respo = CominternaUsuarioPeer::getListUsersByCom(trim($this->getRequestParameter('cominterna_id')));
	//****************************************************************************************  
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){		  
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
            $this->radicadorInternaId = $res->getUsuarioId();
		}elseif($res->getRolusuariocominternaId()==2){
			$this->firmanteId .= $res->getUsuarioId().",";
			$this->firmanteName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
            $this->cargousuarioIdFirma .= $res->getCargoUsuarioId().",";
		}elseif($res->getRolusuariocominternaId()== 3){
			$this->copiaInternaId .= $res->getUsuarioId().",";
			$this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
            $this->cargousuarioIdCopias .= $res->getCargoUsuarioId().",";
		}
		elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
            $this->cargousuarioId .= $res->getCargoUsuarioId().",";
		}
	}
	//****************************************************************************************
	if($this->getRequestParameter('respuesta'))
	   $this->respuesta =  $this->getRequestParameter('respuesta');
	//****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas    
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";    
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologueado)){       
       $this->permisoRadicarOtraRegional = 1;
       $c = new Criteria();
       $c->add(ComPermisoRegionalPeer::USUARIO_ID,explode(",",$this->firmanteId),Criteria::IN);       
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //****************************************************************************************
	if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologueado)){
		$this->es_otra_dependencia=1;
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";    
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologueado)){       
       $this->permisoRadicarFirmaAut = 1;
       $c = new Criteria();
       $c->add(AutFirmaUsuarioPeer::USUARIO_ID,explode(",",$this->firmanteId),Criteria::IN);       
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //****************************************************************************************
    $currentFormConMembrete = "CREAR_INTERNA_CON_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologueado)){
       $this->permisoCrearConMembrete = 1;
       $this->usar_membrete = $this->com_interna->getUseMembrete();
    }else{
       $this->usar_membrete = $usuario->getRegional()->getEntidad()->getUsarMembrete(); 
    }
    //****************************************************************************************
    $this->regional_seleccionada = $this->com_interna->getRegionalId();
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeEdit()
  { 
    $this->verificaPrilegio("com_interna/edit");
    $cominterna_id = $this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : 0;
    $this->com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
    $this->creador = "";
    $this->creador_name = "";
    $this->firmanteId = "";
	$this->firmanteName = "";
    $this->copiaInternaId = "";
  	$this->copiaInternaName = "";
  	$this->destinatarioId = "";
  	$this->destinatarioName = "";
  	$this->cargousuarioId = "";
  	$this->cargousuarioIdCopias = "";
  	$this->cargousuarioIdFirma = "";
    $this->aprobadores = "";
    $this->aprobadores_name = "";
    $this->cargousuarioIdAprob = "";
    $this->revisorUserId = "";
    $this->cargousuarioIdRevisor = "";
    $this->revisorUserNames = "";
  	$this->Radicar=" Radicar";
  	$this->editando=1;	
	$this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
	$this->wfbitacora_id=$this->getRequestParameter('wfbitacora_id');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $usuarios_firman = array();
    $this->expediente_id = null;
    $this->nombre_expediente = null;
    //*******************************************************************************************************
	if($this->getRequestParameter('generada_tipo' ) >0){
	    $this->referencia="Comunicacion WF-0". $this->getRequestParameter('generada_tipo');
        $this->contenido =urldecode($this->getRequestParameter('generada_msg'));
	}
	//*******************************************************************************************************
    $firmas_aprobacion = array();$firmas_desatendida = true;
    //*******************************************************************************************************
	$respo = CominternaUsuarioPeer::getListUsersByCom($cominterna_id);
	//*******************************************************************************************************   
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getFullNombre();
		}elseif($res->getRolusuariocominternaId() == 2){
			$this->firmanteId .= $res->getUsuarioId().",";
			$this->firmanteName .= $res->getUsuario()->getFullNombre().",";
			$this->cargousuarioIdFirma .= $res->getCargoUsuarioId().",";
            $usuarios_firman[] = $res->getUsuarioId();
            if($res->getCheckAprobacion() == 0){
                if(!$res->getUsuario()->getFirmaDesatendida()){
                    $firmas_aprobacion[] = $res->getUsuarioId();
                    //*********************************************************************
                    if(!empty($this->com_interna->getTipoComInterna()->getPlantillascomId())){
                        if($this->com_interna->getTipoComInterna()->getPlantillasCom()->getDependenciaId() == $res->getUsuario()->getDependenciaId()){
                            $firmas_desatendida = $this->com_interna->getTipoComInterna()->getPlantillasCom()->getFirmaDesatendida() ? true : false;
                        }else{
                            $firmas_desatendida = false;
                        }
                    }else{
                        $firmas_desatendida = false;
                    }
                }
            }
		}elseif($res->getRolusuariocominternaId() == 3){
			$this->copiaInternaId .= $res->getUsuarioId().",";
			$this->copiaInternaName .= $res->getUsuario()->getFullNombre().",";
			$this->cargousuarioIdCopias .= $res->getCargoUsuarioId().",";
		}elseif($res->getRolusuariocominternaId() == 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getFullNombre().",";
			$this->cargousuarioId .= $res->getCargoUsuarioId().",";
		}elseif($res->getRolusuariocominternaId() == 5){
            $this->revisorUserId .= $res->getUsuarioId().",";
            $this->revisorUserNames .= $res->getUsuario()->getFullNombre().",";
            $this->cargousuarioIdRevisor .= $res->getCargousuarioId().",";
            if($res->getCheckAprobacion() == 0){  $firmas_aprobacion[] = $res->getUsuarioId(); }
		}        
	}
    //****************************************************************************************
    $aprob_urlist = $firmas_desatendida ? CominternaUsuarioPeer::getListUncheckApro($this->com_interna->getPrimaryKey(),0,array(5)) : 
                        CominternaUsuarioPeer::getListUncheckApro($this->com_interna->getPrimaryKey());
    //****************************************************************************************
    if (($clave = array_search($usuariologuiado, $aprob_urlist)) !== false) {
        unset($aprob_urlist[$clave]);
    }
    //****************************************************************************************
    $this->user_asignado = CominternaUsuarioPeer::getIsUserAsignado($cominterna_id,$usuariologuiado);
    $this->aprobacion_count = CominternaUsuarioPeer::getCountUncheckApro($cominterna_id);
    $this->usuarios_firman = $usuarios_firman;
    $this->users_aprueban = $firmas_aprobacion;
    $this->firmas_desatendida = $firmas_desatendida;
    $this->aprobacion_ulist = $aprob_urlist;
    //****************************************************************************************
    if(!empty($this->com_interna->getExpedienteId())){
        $unidad_documental = UnidadDocumentalPeer::retrieveByPK($this->com_interna->getExpedienteId());
        $this->expediente_id = $unidad_documental->getPrimaryKey();
        $this->nombre_expediente = $unidad_documental->getCodigoTitulo();
        $this->list_tdocs = TipoDocumentalPeer::getTipoDocListBySubserie($unidad_documental->getSubserieId());
        $this->tipodocumental_id = $this->com_interna->getTipoDocumentalCod();
    }
	//****************************************************************************************
	//$this->respuesta = $this->com_interna->getConsecutivoResp();
	//****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";
    $this->permisoRadicarOtraRegional = 0;
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologuiado)){       
        $this->permisoRadicarOtraRegional = 1;
        $c = new Criteria();
        $c->add(ComPermisoRegionalPeer::USUARIO_ID,$usuarios_firman,Criteria::IN);
        $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";
    $this->permisoRadicarFirmaAut = 0;
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologuiado)){       
        $this->permisoRadicarFirmaAut = 1;
        $c = new Criteria();
        $c->add(ComPermisoRegionalPeer::USUARIO_ID,$usuarios_firman,Criteria::IN);
        $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c);
    }
    //****************************************************************************************
    //verificar permiso firma electronica(la misma firma mecanica) para radicar
    $this->permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,$this->firmanteId,2);    
    //****************************************************************************************
    $this->es_otra_dependencia = 0;				
    if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologuiado)){
        $this->es_otra_dependencia=1;
    }
    //****************************************************************************************
    $currentFormConMembrete = "CREAR_INTERNA_CON_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologuiado)){
       $this->permisoCrearConMembrete = 1;
       $this->usar_membrete = $this->com_interna->getUseMembrete();
    }else{
       $this->usar_membrete = $usuario->getRegional()->getEntidad()->getUsarMembrete(); 
    }
    //****************************************************************************************
    //$this->regional_seleccionada = $this->com_interna->getPrimaryKey() ? $this->com_interna->getRegionalId() : 0;
    $this->regional_seleccionada = 0;
    $this->forward404Unless($this->com_interna);
  }
  
  public function executeResponder()
  { 
    $parametro_firmas = ParametroPeer::retrieveByPk(62)->getValorNumerico(); 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    //****************************************************************************************
  	$this->verificaPrilegio("com_interna/responder");
    $this->com_interna = new ComInterna();
    $this->com_internaOrigen = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));    
    //****************************************************************************************
    $this->creador = "";
	$this->creador_name = "";
	$this->firmanteId = "";
	$this->firmanteName = "";
    $this->copiaInternaId = "";
	$this->copiaInternaName = "";
	$this->destinatarioId = "";
	$this->destinatarioName = "";
	$this->cargousuarioId = "";
	$this->cargousuarioIdCopias = "";
	$this->cargousuarioIdFirma = "";
    $this->permisoFirmaOtroAutorizado = "";
	$this->Radicar=" Radicar ";
	$this->editando=1;
    $this->expediente_id = null;
    $this->nombre_expediente = null;
	//****************************************************************************************
    if($parametro_firmas){
      $this->firmanteId = $usuario->getPrimaryKey();
  	  $this->firmanteName = $usuario->getNombreAll().',';
  	  $this->cargousuarioIdFirma = $this->getCargoUsuario($usuario->getPrimaryKey());
    }else{
      	$this->firmanteId = "";
	    $this->firmanteName = "";
	    $this->cargousuarioIdFirma = "";
    }
	//****************************************************************************************
    if(!empty($this->com_internaOrigen->getExpedienteId())){
        $unidad_documental = UnidadDocumentalPeer::retrieveByPK($this->com_internaOrigen->getExpedienteId());
        $this->expediente_id = $unidad_documental->getPrimaryKey();
        $this->nombre_expediente = $unidad_documental->getCodigoTitulo();
        $this->list_tdocs = TipoDocumentalPeer::getTipoDocListBySubserie($unidad_documental->getSubserieId());
        $this->tipodocumental_id = $this->com_internaOrigen->getTipoDocumentalCod();
    }
	//****************************************************************************************
    $acceso_com = CominternaUsuarioPeer::getUserAccessComProcess($this->com_internaOrigen->getPrimaryKey(),$usuariologuiado,4);
    $responder_todas = $this->getUser()->checkPerm("COM_INTERNA_RESPONDER_TODAS", $usuariologuiado);
    if($acceso_com == false && $responder_todas == false){
        $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //****************************************************************************************
	$firmante_original = false;
	$respo = CominternaUsuarioPeer::getListUsersByCom($this->com_internaOrigen->getPrimaryKey());
    //****************************************************************************************
	foreach($respo as $res){
		if($res->getRolusuariocominternaId() == 1 ){
			$this->creador = $usuario->getPrimaryKey();
			$this->creador_name = $usuario->getNombreAll();
			if($res->getUsuarioId() == $usuariologuiado){
				$firmante_original = true;
			}
		}elseif($res->getRolusuariocominternaId() == 2){
			$this->destinatarioId = $res->getUsuarioId();
			$this->destinatarioName = $res->getUsuario()->getNombreAll();
			//*********************************************************************************
            $cargousuarioFirma = CargoUsuarioPeer::getCargoUsuarioByIdUser($res->getUsuarioId(),true);
            $this->cargousuarioId = $cargousuarioFirma->getCargoUsuarioId();            
			//*********************************************************************************
		}elseif($res->getRolusuariocominternaId() == 3){
		    $copiainternacom_id = 0;
			$addcopia = false;
			if($res->getUsuarioId() != $usuariologuiado){
				$copiainternacom_id = $res->getUsuarioId();
				$this->copiaInternaId .= $res->getUsuarioId().",";
				$this->copiaInternaName .= $res->getUsuario()->getNombreAll().",";
				//*****************************************************************************
                $cargousuariocopias = CargoUsuarioPeer::getCargoUsuarioByIdUser($res->getUsuarioId(),true);
                $this->cargousuarioIdCopias .= $cargousuariocopias->getCargoUsuarioId().',';
			}else{
				$addcopia = true;
			}
		}elseif($res->getRolusuariocominternaId() == 4){
		    $firmantecom_id = 0;
            $addcopia = false;
			if($firmante_original){
			    $firmantecom_id = $res->getUsuarioId();
				$this->firmanteId = $res->getUsuarioId().",";
				$this->firmanteName = $res->getUsuario()->getNombreAll().",";
				if($addcopia){
					$this->copiaInternaId = $usuario->getPrimaryKey().",";
					$this->copiaInternaName = $usuario->getNombreAll().",";
				}
			}else{
			    $firmantecom_id = $usuario->getPrimaryKey();
				$this->firmanteId = $usuario->getPrimaryKey().",";
				$this->firmanteName = $usuario->getNombreAll().",";				
				//******************************************************************************
				if($res->getUsuarioId() != $usuariologuiado){
					$this->copiaInternaId .= $res->getUsuarioId().",";
					$this->copiaInternaName .= $res->getUsuario()->getNombreAll().",";
					$this->cargousuarioIdCopias .= $res->getCargoUsuarioId().',';
				}
			}
			//**********************************************************************************
            $cargousuariodestino = CargoUsuarioPeer::getCargoUsuarioByIdUser($firmantecom_id,true);
            $this->cargousuarioIdFirma = $cargousuariodestino->getCargoUsuarioId().',';
		}
	}
    //******************************************************************************************
	$this->es_otra_dependencia = 0;	
	if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologuiado)){
		$this->es_otra_dependencia=1;
	}
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearFirmaAut = "CREAR_INTERNA_SOLO_USUARIO_AUTORIZADO";
    $this->permisoRadicarFirmaAut = 0;
    if($this->getUser()->checkPerm($currentFormCrearFirmaAut, $usuariologuiado)){       
       $this->permisoRadicarFirmaAut = 1;
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";
    $this->permisoRadicarOtraRegional = 0;    
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologuiado)){       
       $this->permisoRadicarOtraRegional = 1;
       $c = new Criteria();
       $c->add(ComPermisoRegionalPeer::USUARIO_ID,explode(",",$this->firmanteId),Criteria::IN);
       $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c); 
    }            
    //****************************************************************************************
    $currentFormConMembrete = "CREAR_INTERNA_CON_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologuiado)){
       $this->permisoCrearConMembrete = 1;
       $this->usar_membrete = $this->com_internaOrigen->getUseMembrete();
    }else{
       $this->usar_membrete = $usuario->getRegional()->getEntidad()->getUsarMembrete(); 
    }
    //****************************************************************************************
    $this->regional_seleccionada = $this->com_internaOrigen->getRegionalId();
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeRejectedLinksOpt()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $cominterna_id = $this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : -1;
    $this->com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
    $this->devoluciones_list = CominternaUsuarioPeer::getDevolucionesData($cominterna_id,array(2,5),array($usuariologuiado));
  }

  public function executeReasignarAct()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $currentForm = "COM_INTERNA_DEVOLUCIONES_FLUJO";
    $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
    //**********************************************************************************************
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
        $array = json_encode($response_data);
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($array);
    }
    //**********************************************************************************************
    $cominterna_id = $this->getRequestParameter('cominterna_id') ? $this->getRequestParameter('cominterna_id') : 0;
    $usuariodestino_id = $this->getRequestParameter('usuariodestino_id') ? $this->getRequestParameter('usuariodestino_id') : 0;
    $rolusuariocom_id = $this->getRequestParameter('rolusuariocom_id') ? $this->getRequestParameter('rolusuariocom_id') : 0;
    $cusuario_id = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariodestino_id);
    //**********************************************************************************************
    if(!empty($cominterna_id) && !empty($usuariodestino_id) && !empty($rolusuariocom_id)){
        $info_com = array();
        $info_com['pkcom_id'] = $cominterna_id;
        $info_com['usuario_id'] = $usuariodestino_id;
        $info_com['cusuario_id'] =  $cusuario_id;
        $info_com['estadocom_id'] = 1;
        $info_com['rol_id'] = $rolusuariocom_id;
        $info_com['esta_asignada'] = 1;
        //*******************************************************************************************
        $ucom_current = CominternaUsuarioPeer::getCurrentUserAsignado($cominterna_id);
        //*******************************************************************************************
        if($rolusuariocom_id == 1){
            $ucom_interna = CominternaUsuarioPeer::getUserAddedInCom($info_com['pkcom_id'],$info_com['usuario_id'],$info_com['rol_id']);
            if($ucom_interna != null){
                $ucom_interna->setCheckAprobacion(0);
                $ucom_interna->setFechaAprobacion(null);
                $ucom_interna->setEstaAsignada(1);
                $ucom_interna->setFechaAsigna(date("Y-m-d G:i:s"));
                $ucom_interna->save();
            }else{
                $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
                $array = json_encode($response_data);
                $this->getResponse()->setContentType('application/json');      
                return $this->renderText($array);
            }
        }else{
            $ucom_interna = CominternaUsuarioPeer::addUserByCom($info_com);
        }
        //*******************************************************************************************
        if($ucom_current != null){            
            $ucom_current->setEstaAsignada(0);
            $ucom_current->save();
        }
        //*******************************************************************************************
        $response_data = array( 'status' => 200, 'message' => 'Se asignó la comunicación al usuario satisfactoriamente');
    }else{
        $response_data = array( 'status' => 400, 'message' => 'La información enviada no es valida o esta incompleta, no se realizó la devolución');
    }
    //***********************************************************************************************
    $array = json_encode($response_data);
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText($array);
  }

  public function executeReenviar()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio("com_interna/reenviar");
    //****************************************************************************************
    $this->com_interna = new ComInterna();
    $this->com_internaOrigen = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $loguiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $user = UsuarioPeer::retrieveByPK($loguiado);
    $this->cargousuarioIdFirma = $this->getCargoUsuario($loguiado).',';
    $this->creador = "";
	$this->creador_name = $user->getFullNombre();
	$this->firmanteId = $user->getUsuarioid().',';
	$this->firmanteName = $user->getFullNombre().',';
    $this->copiaInternaId = "";
	$this->copiaInternaName = "";
	$this->destinatarioId = "";
	$this->destinatarioName = "";
	$this->cargousuarioId = "";
	$this->cargousuarioIdCopias = "";	
	$this->Radicar=" Radicar ";
	$this->editando=1;
	$this->user = $user;
    //****************************************************************************************
    $acceso_com = CominternaUsuarioPeer::getUserAccessComProcess($this->com_internaOrigen->getPrimaryKey(),$usuariologuiado,4);
    $responder_todas = $this->getUser()->checkPerm("COM_INTERNA_RESPONDER_TODAS", $usuariologuiado);
    if($acceso_com == false && $responder_todas == false){
        $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
	//****************************************************************************************
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeDuplicar()
  { 
  	$usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio("com_interna/duplicar");     
    //****************************************************************************************
    $usuario = UsuarioPeer::retrieveByPK($usuariologueado);
    //****************************************************************************************
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    //****************************************************************************************
    $this->creador = $usuario->getPrimaryKey();
	$this->creador_name = $usuario->getNombreAll();
    $this->copiaInternaId = "";
	$this->copiaInternaName = "";
	$this->destinatarioId = "";
	$this->destinatarioName = "";
	$this->cargousuarioId = "";
	$this->cargousuarioIdCopias = "";
    $this->permisoFirmaOtroAutorizado = "";
	$this->Radicar = " Radicar";
    $this->cargousuarioIdAprob = "";
    $this->aprobadores = "";
    $this->aprobadores_name = ""; 
	$this->editando = 1;
    $this->wfinstancia_id = "";
    $this->wfbitacora_id = "";
    $this->expediente_id = null;
    $this->nombre_expediente = null;
	//****************************************************************************************
	$firma_todos = $this->getUser()->checkPerm("CREAR_INTERNA_OTRO_USUARIO_AUTORIZADO",$usuariologueado);
	//****************************************************************************************
	$respo = CominternaUsuarioPeer::getListUsersByCom(trim($this->getRequestParameter('cominterna_id')));
    //****************************************************************************************
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombreAll();
		}elseif($res->getRolusuariocominternaId()==2){
			$this->firmanteId .= "";
			$this->firmanteName .= "";
			$this->cargousuarioIdFirma .= "";
		}elseif($res->getRolusuariocominternaId()== 3){
			$this->copiaInternaId .= $res->getUsuarioId().",";
			$this->copiaInternaName .= $res->getUsuario()->getNombreAll().",";
			$this->cargousuarioIdCopias .= $res->getCargoUsuarioId().',';
		}elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombreAll().",";
			$this->cargousuarioId .= $res->getCargoUsuarioId().',';
		}elseif($res->getRolusuariocominternaId()== 5){
            $this->cargousuarioIdAprob .= $res->getCargousuarioId().",";
		}
	}
    //******************************************************************************************************
    if(!empty($this->com_interna->getExpedienteId())){
        $unidad_documental = UnidadDocumentalPeer::retrieveByPK($this->com_interna->getExpedienteId());
        $this->expediente_id = $unidad_documental->getPrimaryKey();
        $this->nombre_expediente = $unidad_documental->getCodigoTitulo();
        $this->list_tdocs = TipoDocumentalPeer::getTipoDocListBySubserie($unidad_documental->getSubserieId());
        $this->tipodocumental_id = $this->com_interna->getTipoDocumentalCod();
    }
	/*********************************************APROBACIONES**********************************************/
    $matriz_data = $this->getAprobadores($this->getRequestParameter('cominterna_id'));
    $this->is_usuario_aprobador = false;    
    if(count($matriz_data) > 0){
        $this->aprobadores = implode(",",$matriz_data[0]) . ',';
        $this->aprobadores_name = implode(",",$matriz_data[1]) . ',';    
    
        if(in_array($usuariologueado,$matriz_data[0])){
            $this->is_usuario_aprobador = true;
        }
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas
    $currentFormCrearOtraRegional = "RADICAR_COM_INTERNA_OTRA_REGIONAL";
    $usuariologueado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->permisoRadicarOtraRegional = 0;
    if($this->getUser()->checkPerm($currentFormCrearOtraRegional, $usuariologueado)){       
        $this->permisoRadicarOtraRegional = 1;
        $c = new Criteria();
        $c->add(ComPermisoRegionalPeer::USUARIO_ID,explode(",",$this->firmanteId),Criteria::IN);
        $this->lista_regionales = ComPermisoRegionalPeer::doSelect($c);
    }
    //****************************************************************************************
    //verificar permiso para radicar de otras regionales y enviar las autorizadas    
    $this->permisoRadicarFirmaAut = $firma_todos;
	//**************************************************************************************** 
	$this->permisoRadicarFirmaElectronica = 1;
    //****************************************************************************************
    $this->es_otra_dependencia = 0;				
    if($this->getUser()->checkPerm("RADICAR_COM_INTERNA_OTRA_DEPENDENCIA", $usuariologueado)){
        $this->es_otra_dependencia=1;
    }
    //****************************************************************************************
    $currentFormConMembrete = "CREAR_INTERNA_CON_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologueado)){
        $this->permisoCrearConMembrete = 1;
        $this->usar_membrete = $this->com_interna->getUseMembrete();
    }else{
        $this->usar_membrete = $usuario->getRegional()->getEntidad()->getUsarMembrete(); 
    }
	//****************************************************************************************
    $this->regional_seleccionada = $this->com_interna->getRegionalId();
	$this->forward404Unless($this->com_interna);
  }
  
  public function executeShowSticker()
  { 
  	$this->verificaPrilegioCerrar("com_interna/showSticker");
  	$this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
	//*****************************************************************************************
    $respo = CominternaUsuarioPeer::getListUsersByCom(trim($this->getRequestParameter('cominterna_id')));
	//*****************************************************************************************     
	$primeraFirma=0;
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
		}elseif($res->getRolusuariocominternaId()==2){
			if($primeraFirma==0){
			    $this->firmanteId .= $res->getUsuarioId();
			   $this->firmanteName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			   $primeraFirma=1;
			   $this->cargousuarioIdFirma = $res->getCargoUsuarioId();
			}
		}elseif($res->getRolusuariocominternaId()== 3){						
				$this->copiaInternaId .= $res->getUsuarioId().",";
			    $this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";
				$this->cargousuarioIdCopias = $res->getCargoUsuarioId();       
		}
		elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
			$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			$this->cargousuarioId = $res->getCargoUsuarioId();
		}
	}
	//*****************************************************************************************	
	$c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $this->getRequestParameter('cominterna_id'));
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 3);
    $this->resultCopias = CominternaUsuarioPeer::doSelect($c);
	//*****************************************************************************************
	$this->setLayout(false);
  }
  
  
   public function executeSobre()
  { 
  	$this->verificaPrilegioCerrar("com_interna/showSticker");
  	$this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $respo = CominternaUsuarioPeer::getListUsersByCom(trim($this->getRequestParameter('cominterna_id')));
	//*****************************************************************************************
	$primeraFirma=0;
	foreach($respo as $res){		
		if($res->getRolusuariocominternaId()==1){
			$this->creador = $res->getUsuarioId();
			$this->creador_name = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
		}elseif($res->getRolusuariocominternaId()==2){
			if($primeraFirma==0){
			    $this->firmanteId .= $res->getUsuarioId();
			   $this->firmanteName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
			   $primeraFirma=1;
			}
		}elseif($res->getRolusuariocominternaId()== 3){
			
			
				$this->copiaInternaId .= $res->getUsuarioId().",";
			    $this->copiaInternaName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido().",";       
		}
		elseif($res->getRolusuariocominternaId()== 4){
			$this->destinatarioId .= $res->getUsuarioId().",";
		 	$this->destinatarioName .= $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
		    $this->destinatarioDir .= $res->getUsuario()->getRegional()->getDireccion();
			$this->destinatarioCiudad .= $res->getUsuario()->getRegional()->getCiudad()->getNombre();
		}
	}
	//**********************************************************************************************
	$c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $this->getRequestParameter('cominterna_id'));
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 3);
    $this->resultCopias = CominternaUsuarioPeer::doSelect($c);
    //**********************************************************************************************        
	$this->setLayout(false);
  }
  
  
  public function executeUpdateGuia()
  {  
      $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));    
      $this->forward404Unless($com_interna);
	  $com_interna_anterior = clone $com_interna;
	  $com_interna->setGuia($this->getRequestParameter('guia'));
      $com_interna->setValorGuia($this->getRequestParameter('valor_guia'));
      $com_interna->setEmpresaMensajeriaId($this->getRequestParameter('empresa_mensajeria_id'));
      $com_interna->setFechaEnvioGuia($this->getRequestParameter('fecha_envio_guia'));	  
      $com_interna->save();
      //*********************************************************************************************
      $this->guardarAuditoria($com_interna_anterior,$com_interna);
	  return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getPrimaryKey());
  }
  
  public function guardarAuditoria($anterior,$nueva)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    AuditLogPeer::guardarAuditoriaLite("ComInterna",$anterior,$nueva,ModulesEnable::ComInterna,$nueva->getRadicado(),$usuariologuiado);
  }
  
  public function executeGuia() 
  { 
	$this->verificaPrilegio("com_interna/guia");
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));   
	$this->forward404Unless($this->com_interna);    
  }
  
  public function executePublicar()
  { 
    $this->verificaPrilegio("COM_INTERNA_PUBLICAR");
	$cominterna_id = $this->getRequestParameter('cominterna_id');
	$this->com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
	$estadocominterna_id = $this->com_interna->getEstadocominternaId();
	//********************************************************************************************
	if(in_array($estadocominterna_id,array(1, 4, 6)))
    {
		return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$this->com_interna->getCominternaId());
	}
	//********************************************************************************************
	$this->forward404Unless($this->com_interna);    
  }

  
  public function executeUpdateCartelera()
  {
    $this->verificaPrilegio("COM_INTERNA_PUBLICAR");
    //*************************************************************************************
    $cominterna_id = trim($this->getRequestParameter('cominterna_id')) ? trim($this->getRequestParameter('cominterna_id')) : null;
    $obs_publicacion = trim($this->getRequestParameter('obs_publicacion')) ? trim($this->getRequestParameter('obs_publicacion')) : null;
    $cominterna = ComInternaPeer::retrieveByPk($cominterna_id);
    //*************************************************************************************
    try
    {
        if(in_array($cominterna->getEstadocominternaId(), array(1, 4, 6)))
        {
            $data_array['status'] = 400;
            $data_array['mensaje'] = 'Publicacion no permitida';
            //*****************************************************************************
            $this->getResponse()->setContentType('application/json');
            $data_json = json_encode($data_array);
            return $this->renderText($data_json);
        }

        if($obs_publicacion == null)
        {
            $data_array['status'] = 403;
            $data_array['mensaje'] = 'Debe llenar el campo de observaciones';
            //*****************************************************************************
            $this->getResponse()->setContentType('application/json');
            $data_json = json_encode($data_array);
            return $this->renderText($data_json);
        }

        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        $consecutivo_id = $com_interna->getPrimaryKey();
        $modulo_id = ModulesEnable::ComInterna; //2
        $alreadyPublished = CarteleraInformativaPeer::getDocumentIsPublished($consecutivo_id, $modulo_id);

        if($alreadyPublished)
        {
            $data_array['status'] = 402;
            $data_array['mensaje'] = 'Error: Esta comunicacion ya se encuentra publicada,';
            //*****************************************************************************
            $this->getResponse()->setContentType('application/json');
            $data_json = json_encode($data_array);
            return $this->renderText($data_json);
        }
        else
        {
            $params = [];
            $params['id_creador'] = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
            $params['consecutivo_id'] = $consecutivo_id;
            $params['modulo_id'] = $modulo_id;
            $params['dependencia_id'] = $com_interna->getDependenciaId();
            $params['periodo_id'] = $com_interna->getPeriodoId();
            $params['estadodigitalizacion_id'] = $com_interna->getEstadodigitalizacionId();
            $params['estadopublicacion_id'] = StatusPublicacion::Publicado;
            $params['dependencia_id'] = $com_interna->getDependenciaId();
            $params['asunto'] = $com_interna->getReferencia();
            $params['radicado'] = $com_interna->getRadicado();
            $params['fecha_documento'] = $com_interna->getFechaCreacion();
            $params['tipo_documento'] = $com_interna->getTipoComInterna()->getDescripcion();
            $params['observaciones'] = $obs_publicacion;

            $creado = CarteleraInformativaPeer::addRegistroCartelera($params);
            if($creado)
            {
                $data_array['status'] = 200;
                $data_array['mensaje'] = 'Comunicación publicada correctamente';
                $data_array['url_back'] = $this->getRequest()->getScriptName() . '/com_interna/show?cominterna_id=' . $com_interna->getPrimaryKey();
                //*****************************************************************************
                $this->getUser()->setFlash('message_success', 'Comunicación publicada correctamente');
                $this->getResponse()->setContentType('application/json');
                $data_json = json_encode($data_array);
                return $this->renderText($data_json);
            }
            else
            {
                $data_array['status'] = 401;
                $data_array['mensaje'] = 'Error: Esta comunicacion no se ha podido publicar';
                //*****************************************************************************
                $this->getResponse()->setContentType('application/json');
                $data_json = json_encode($data_array);
                return $this->renderText($data_json);
            }
        }
    }
    catch (PropelException $th) 
    {
        $data_array['status'] = 400;
        $data_array['mensaje'] = $th->getMessage();
    }
    catch (\Exception $th) 
    {
        $data_array['status'] = 400;
        $data_array['mensaje'] = $th->getMessage();    
    }
    catch (\Throwable $th) 
    {
        $data_array['status'] = 400;
        $data_array['mensaje'] = $th->getMessage();
    }
    //****************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $data_json = json_encode($data_array);
    return $this->renderText($data_json);
  }

  public function executeAnular()
  { 
  	$this->verificaPrilegio("com_interna/anular");
	$cominterna_id = $this->getRequestParameter('cominterna_id');
	$this->com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
	$estadocominterna_id = $this->com_interna->getEstadocominternaId();
	//********************************************************************************************
	if(in_array($estadocominterna_id,array(4,7,8,9))){
		$this->getUser()->setFlash('error', "No se puede anular este radicado, por favor comuniquese con el administrador del sistema");
		return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$this->com_interna->getCominternaId());
	}
	//********************************************************************************************
	$this->forward404Unless($this->com_interna);    
  }
  
  public function executeAddNota()
  { 
  	$this->verificaPrilegio("COM_INTERNA_ADICIONAR_NOTACOM");	
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));   
	$this->forward404Unless($this->com_interna);    
  }

  public function executeUpdateAnular()
  {    
	  $this->verificaPrilegio("com_interna/anular");
	  $cominterna_id = $this->getRequestParameter('cominterna_id');
      $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
	  $estadocominterna_id = $com_interna->getEstadocominternaId();
	  //********************************************************************************************
	  if(in_array($estadocominterna_id,array(4,7,8,9))){
		$this->getUser()->setFlash('error', "No se puede anular este radicado, por favor comuniquese con el administrador del sistema");
		return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getCominternaId());
	  }
	  //********************************************************************************************
	  
      $this->forward404Unless($com_interna);
      $com_interna->setFechaDeAnulacion($this->getRequestParameter('fecha_de_anulacion'));
      $com_interna->setObsAnulacion($this->getRequestParameter('obs_anulacion'));
      $com_interna->setEstadocominternaId(4);
      $com_interna->save();
      //********************************************************************************************
      $usuarios_interna = CominternaUsuarioPeer::getListUsersByCom(trim($this->getRequestParameter('cominterna_id')));
  	  foreach($usuarios_interna as $com_internas){		
		 $com_internas->setEstadocominternaId(4);
		 $com_internas->save();
	  }
	  //********************************************************************************************
	  return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getCominternaId());   
  }
  
  public function executeUpdateAddNota()
  {
    $cominterna_id = $this->getRequestParameter('cominterna_id') ? trim($this->getRequestParameter('cominterna_id')) : null;
    $note_com = $this->getRequestParameter('nota_com') ? trim($this->getRequestParameter('nota_com')) : null;
    $note_split = !empty($note_com) ? substr($note_com,0,200) : "";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    //*******************************************************************************************************
    if(!empty($cominterna_id) && !empty($note_split)){
        $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
        $newNote = CominternaDocnotaPeer::addNewNote($com_interna->getPrimaryKey(),$note_split,$usuariologuiado);
        //***************************************************************************************************
        if($newNote != null){
            $this->docnota_com = $newNote;
            $this->com_interna = $com_interna;
            $this->setTemplate('newNota');
        }else{
            return sfView::ERROR;
        }
    }else{
        return sfView::NONE;
    }
  }

  public function executeResolverNota()
  {    
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $com_docnota = CominternaDocnotaPeer::retrieveByPk($this->getRequestParameter('cominternadocnota_id'));
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    //***********************************************************************************************************
    if($com_docnota != null){
        $com_docnota->setFechaActualiza(date('Y-m-d G:i:s'));
        $com_docnota->setEstadoNota(2);
        $com_docnota->setNota(sprintf("%s - Resuleve => %s",$com_docnota->getNota(),$usuario->getFullNombre()));
        $com_docnota->save();
        //*******************************************************************************************************
        $response_info = array('status' => 200, 'message' => $com_docnota->getNota());
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }else{
        $response_info = array('status' => 400, 'message' => 'Error al actualizar la información');
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }
  }

  public function executeUpdateDestinos()
  {  
  	 /*****************************************************************************************************************************************/
  	 $loguiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');  	 	 	 	   	 	  	 
  	 $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('interna_id'));
	 $codigo_reen_resp = $com_interna->getCodigoReenResp() ? $com_interna->getCodigoReenResp() : $com_interna->getPrimaryKey();
     $com_interna->setCodigoReenResp($codigo_reen_resp);
     $com_interna->save();	
	 $copia_interna = $com_interna->copy();//se realiza una copia del objero com_interna original	      	 	 	 		 
	 /**************************************Consulta Para los roles de los usurios en Interna**************************************************/
	 $user_interna = CominternaUsuarioPeer::getListUsersByCom($com_interna->getCominternaId());
	 /*****************************************************************************************************************************************/
     //arreglo de los usuario que se le van a enviar copias
	 $cadDestinos = preg_split("/[,]+/",$this->getRequestParameter('copiasUsuario_id'), -1, PREG_SPLIT_NO_EMPTY);
     //arreglo de los cargos de los usuario que se le van a enviar copias
     $cargosCopias = preg_split("/[,]+/",$this->getRequestParameter('cargousuarioIdCopias'), -1, PREG_SPLIT_NO_EMPTY);
     //exit
    //se obtiene el usuario que firma la comunicacion *****************************************
    $firmante = $this->getFirstUsurioFirmaId($this->getRequestParameter('interna_id'));//se obtiene el id del usuario que firma la interna	
   	if(!$firmante){//if para controlar que alguien halla firmado la interna
       $firmante=$this->getUser()->getAttribute('usuario_id','', 'subscriber');//si no hay firma el usaurio logueado sera el que firme	
   	}   		     
 	$objUsuarioFirmante = UsuarioPeer::retrieveByPk($firmante);//consulta para traer los datos del firmante		
 	$regional = $objUsuarioFirmante->getRegionalId();//se obtiene la regional del firmante.
 	$dependencia = $objUsuarioFirmante->getDependenciaId();//se obtiene la dependencia del firmante
    $depen_codigo = $objUsuarioFirmante->getDependencia()->getCodigo();
    $copia_interna->setCodigoReenResp($codigo_reen_resp);
    /********************************************************************************************/                        
  	 $iterador = (count($cadDestinos));//se elimina el ultimo campo del array pues viene solo con una coma            	   	 	 
	 for($i = 0; $i < $iterador; $i++){//for para recorrer arreglo de destinatarios 
       if($cadDestinos[$i] != ""){
            $copia_interna->setFechaCreacion(date("Y-m-d G:i:s"));
            $copia_interna->setNumeroRadicacion(0);
            $copia_interna->setRadicado(null);
            $copia_interna->save();//se guarda copia de la comunicacion en la base de datos.
            $radicado = $copia_interna->getRadicadoFormat(null,$regional,$depen_codigo);
            $copia_interna->setRadicado($radicado);
            $copia_interna->save();
            ///////////////////////////////////////////inserta los USUARIOS////////////////////////////////////////////
            //insertar roles de radicador y firma
            foreach($user_interna as $tmpUser){//foreach para recorrer los roles de los usuarios en la comunicacion que se va a copiar
                if($tmpUser->getRolusuariocominternaId() != 4){//Insertar radicador y firma tal cual como esta la original                     	
                    $rolCopia = new CominternaUsuario();//se crea nuevo objero de usuario com_interna
                    $rolCopia->setCominternaId($copia_interna->getCominternaId());//se asigna consecutivo de la com_interna
                    $rolCopia->setRolusuariocominternaId($tmpUser->getRolusuariocominternaId());//se deja el rol de la com_interna original
                    if($tmpUser->getCargousuarioId() == ""){//validar que tenga cargo usuario
                        $rolCopia->setCargousuarioId($this->getCargoUsuario($tmpUser->getUsuarioId()));
                    }else{
                        $rolCopia->setCargousuarioId($tmpUser->getCargousuarioId());
                    }
                    $rolCopia->setEstadocominternaId(2);//se deja el estado como no leido
                    $rolCopia->setUsuarioId($tmpUser->getUsuarioId());//se deja el usuario de la com_interna original
                    $rolCopia->save();//se guardan el usuario de la com_interna
        		}elseif($tmpUser->getRolusuariocominternaId() == 4){//si el rol es destinatario
                    $rolDest = new CominternaUsuario();//se crea nuevo objero de usuario com_interna
                    $rolDest->setCominternaId($copia_interna->getCominternaId());//se asigna consecutivo de la com_interna
                    $rolDest->setRolusuariocominternaId(4);//se asigna rol como destinatario
                    $rolDest->setEstadocominternaId(2);//se deja el estado como no leido
                    if($cargosCopias[$i] == ""){//validar que tenga cargo usuario por defecto
                        $rolDest->setCargousuarioId($this->getCargoUsuario($cadDestinos[$i]));
                    }else{
                        $rolDest->setCargousuarioId($cargosCopias[$i]);
                    }
                    $rolDest->setUsuarioId($cadDestinos[$i]);//se asigan id de usuario seleccionados
                    $rolDest->save();//se guarda el objeto cominterna_usuario en la base de datos
        		}//fin frl if
            }//fin del foreach de usuarios
            //**********************************************************************************************************
            if(!empty($copia_interna->getExpedienteId()) && !empty($copia_interna->getTipoDocumentalCod())){
                $origentransfer_id = 1;
                $copia_interna->addNewTransferenciaAndContenido($origentransfer_id,$objUsuarioFirmante->getPrimaryKey());
                //FALTA CODIGO PARA CREAR LA TRANSFERENCIA CUANDO SE MARCA RADICADO POR INTERESADO
            }
        	//**********************************************************************************************************
        	$copia_interna = $copia_interna->copy();//se realiza una nueva copia de la com_interna
         }//fin if de control validando que las cadenas de usaurio no esten vacias			
  	 }//fin del for para insertar las comunicaciones en lotes
  	 //*****************************************************************************************************************
  	 //redirecciona al listar las comunicaciones internas por funcion de salida
  	 return $this->redirect($this->getRequest()->getScriptName().'/com_interna/list?porFunciSalida=1');
  }	
  
  public function executeUpdate()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $estadoborrador_id = 1;
    //*************************************************************************************************
    if (!$this->getRequestParameter('cominterna_id') || $this->getRequestParameter('duplicar') == 1)
    {
        $com_interna = new ComInterna();
        $com_interna->setMarca(0);
        $com_interna->setNumeroRadicacion(0);
        $com_interna->setRadicado(null);
        $com_interna->setReferencia(null);
    }
    else
    {
        $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
        //*********************************************************************************************
        if($com_interna->getEstadocominternaId() != $estadoborrador_id){
            if(trim($this->getRequestParameter('vshow')) == md5($com_interna->getPrimaryKey())){
                $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getPrimaryKey());
            }else{
                $this->redirect($this->getRequest()->getScriptName().'/com_interna/showRadicar?cominterna_id='.$com_interna->getPrimaryKey());
            }
        }
        //*********************************************************************************************
        $com_edit_all = $this->getUser()->checkPerm("COM_INTERNA_EDIT_TODAS", $usuariologuiado);
        $ucom_asignada = CominternaUsuarioPeer::getIsUserAsignado($com_interna->getPrimaryKey(),$usuariologuiado);
        //$ucom_creador = CominternaUsuarioPeer::getUserAccessComProcess($com_interna->getPrimaryKey(),$usuariologuiado,1);
        //*********************************************************************************************
        if(!$ucom_asignada && !$com_edit_all){
            $url_redidrect = $this->getRequest()->getScriptName().'/com_interna/edit?cominterna_id='.$com_interna->getPrimaryKey();
            $url_redidrect .= $this->getRequestParameter('respuesta') ?  '&respuesta='.trim($this->getRequestParameter('respuesta')) : "";
            return $this->redirect($url_redidrect);
        }
        //*********************************************************************************************
        $this->forward404Unless($com_interna);
    }
    //*************************************************************************************************
    $yearActual = date("Y");
    $com_interna_anterior = clone $com_interna;
    //*************************************************************************************************
    $objUsuarioLoguiado = UsuarioPeer::retrieveByPk($usuariologuiado);
    $permiso_regional =  $this->getRequestParameter('regional_id');
    $membrete_default = $objUsuarioLoguiado->getRegional()->getEntidad()->getUsarMembrete() ? 1 : 0;
    //*************************************************************************************************
	$firmas_str = trim($this->getRequestParameter('firmanteId'));
	//*************************************************************************************************
    if(!$objUsuarioLoguiado){
        $dependencia_id = 1;
		$regional_id = 1;
    }elseif(trim($this->getRequestParameter('dependencia_id'))){
        $dependencia_id = trim($this->getRequestParameter('dependencia_id'));
        if($objUsuarioLoguiado->getRegionalId()){
            $regional_id = $objUsuarioLoguiado->getRegionalId();
        }else{
            $regional_id = 1;
        }
    }elseif(trim($this->getRequestParameter('firmanteId'))){
        $list_firmas = explode(",", $firmas_str);
        $user_firma = UsuarioPeer::retrieveByPk($list_firmas[0]);
        $dependencia_id = $user_firma->getDependenciaId();
        //*********************************************************************************************
        if($objUsuarioLoguiado->getRegionalId() ){
            $regional_id = $objUsuarioLoguiado->getRegionalId();
        }else{
            $regional_id = 1;
        }
	}else{
        if($objUsuarioLoguiado->getDependenciaId()){
            $dependencia_id = $objUsuarioLoguiado->getDependenciaId();
        }else{
            $dependiencia_id = 1;	
        }
	    //*********************************************************************************************
        if($objUsuarioLoguiado->getRegionalId() ){
			$regional_id = $objUsuarioLoguiado->getRegionalId();
		}else{
			$regional_id = 1;
		}
	}
    //*************************************************************************************************
    //si tiene el permiso de radicar de otra regional modifica el valor de la variable $regional
    //entonces la comunicacion quedara con el consecutivo de la regional seleccionada
    if($permiso_regional ){ $regional_id = $permiso_regional; }
    //*************************************************************************************************
    if($this->getUser()->checkPerm("CREAR_INTERNA_CON_MEMBRETE", $usuariologuiado)){
        $com_interna->setUseMembrete($this->getRequestParameter('use_membrete') ? $this->getRequestParameter('use_membrete') : 0);
    }else{
        $com_interna->setUseMembrete($membrete_default);
    }
    //*************************************************************************************************
    $estadocominterna_id = 1;
    $com_interna->setEstadocominternaId($estadoborrador_id);    	
	$com_interna->setPeriodoId($yearActual);
    $com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id'));    
	$com_interna->setEstadodigitalizacionId($estadocominterna_id);
    $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
	$com_interna->setReferencia($this->getRequestParameter('referencia'));        
    $contenido = $this->getRequestParameter('contenido');
    $com_interna->setContenido($this->cleanStringTable($contenido));            
    $com_interna->setRadicado("Sin Radicar");
    $com_interna->setEsCopia(0);
    $com_interna->setFolios(trim($this->getRequestParameter('folios')) ? trim($this->getRequestParameter('folios')) : 1);
    $com_interna->setAnexos(trim($this->getRequestParameter('anexos')) != "" ? trim($this->getRequestParameter('anexos')) : null);
    $com_interna->setRequiereRespuesta($this->getRequestParameter('requiere_respuesta'));
    $com_interna->setEstaentregado(0);    
    $com_interna->setDependenciaId($dependencia_id);
    $com_interna->setRegionalId($regional_id);
    $com_interna->setExpedienteId($this->getRequestParameter('unidaddocumental_id') ? $this->getRequestParameter('unidaddocumental_id') : null);
    $com_interna->setTipoDocumentalCod($this->getRequestParameter('tipodocumental_id') ? $this->getRequestParameter('tipodocumental_id') : null);
    $com_interna->setPrioridadcomId($this->getRequestParameter('prioridadcom_id') ?: null);
    $com_interna->save();
    //*****************************************************************************************************
    $list_users = array();
    $list_users['str_firmausers'] = trim($this->getRequestParameter('firmanteId')) ? trim($this->getRequestParameter('firmanteId')) : $usuariologuiado;
    $list_users['str_ucargosfirma'] = trim($this->getRequestParameter('cargousuarioIdFirma'));
    $list_users['str_copiausers'] = trim($this->getRequestParameter('copiaInternaId'));
    $list_users['str_ucargoscopia'] = trim($this->getRequestParameter('cargousuarioIdCopias'));
    $list_users['str_revisorusers'] = trim($this->getRequestParameter('revisorUserId'));
    $list_users['str_ucargosrevisor'] = trim($this->getRequestParameter('cargousuarioIdRevisor'));
    $list_users['str_destinorusers'] = trim($this->getRequestParameter('destinatarioId'));
    $list_users['str_ucargosdestino'] = trim($this->getRequestParameter('cargousuarioId'));
    //*****************************************************************************************************
    $lusuarios_destino = preg_split("/[,]+/",$list_users['str_destinorusers'], -1, PREG_SPLIT_NO_EMPTY);
    //*****************************************************************************************************
    $files_uploads = trim($this->getRequestParameter('ruta'));
    if($files_uploads){
        if (!$this->getRequestParameter('cominterna_id')){
            $ruta = ComInterna::getRutaAdjuntos($files_uploads, false);
        }else{
            $ruta = ComInterna::getRutaAdjuntos($files_uploads, true, $com_interna->getRuta());
        }
        $com_interna->setRuta($ruta);
    }else{
        $com_interna->setRuta(null);
    }
    //*****************************************************************************************************
    $replyfile = trim($this->getRequestParameter('replyfile'));
    if(!empty($replyfile) && count($lusuarios_destino) <= 1){
        $filedir_tmp = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$replyfile;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto().'uploads');
		$filedir_upload = simad_util::createPath($dir_raiz.DIRECTORY_SEPARATOR.date("Ymd")).DIRECTORY_SEPARATOR.$replyfile;
        //*************************************************************************************************
        if(file_exists($filedir_tmp)){
            $fileinfo = new SplFileInfo($filedir_tmp);
            //*********************************************************************************************
            if($fileinfo->getExtension() == "pdf"){
                if(rename($filedir_tmp,$filedir_upload)){
                    $com_interna->setUrlFileWord($filedir_upload);
                    $com_interna->setIsCreateWord(1);
                    $com_interna->setContenido(null);
                    $com_interna->setUseMembrete(0);
                }
            }
        }
    }else if($this->getRequestParameter(md5('radComByWord')) && !empty($com_interna->getTipoComInterna()->getPlantillascomId())){
        $dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto() . 'uploads');
        $filedir_upload = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd"));
        //*****************************************************************************************************
        $params['use_membrete'] = $com_interna->getUseMembrete();
        $params['membrete_com'] = $com_interna->getRegional()->getImageMembrete();
        $params['comobject_id'] = $com_interna->getPrimaryKey();
        $params['periodo_id'] = $com_interna->getPeriodoId();
        $response_tpl = $com_interna->getTipoComInterna()->getPlantillasCom()->generateWordByPlantilla($filedir_upload,$params);
        //*****************************************************************************************************
        if($response_tpl['isError'] == false){
            $com_interna->setUrlFileWord($response_tpl['path_plantilla']);
            $com_interna->setIsCreateWord(2);
            $com_interna->setContenido(null);
            $com_interna->setUseMembrete(0);
        }
    }else{
        $com_interna->setUrlFileWord(null);
        $com_interna->setIsCreateWord(null);
    }
    //*****************************************************************************************************
    //if (!$this->getRequestParameter('cominterna_id')){ $com_interna->setCodigoReenResp($com_interna->getPrimaryKey()); }
    //*****************************************************************************************************
	$com_interna->setFirmaElectronica(UsuarioPeer::countValidateTipoFirma($list_users['str_firmausers']));
    //*****************************************************************************************************
    $snext_user = $this->getRequestParameter('save') ? false : true;
    $usuario_creador = CominternaUsuarioPeer::getUserComByRol($com_interna->getPrimaryKey(),1);
    $usuariocreador_id = $usuario_creador != null ? $usuario_creador->getUsuarioId() : $usuariologuiado;
    CominternaUsuarioPeer::initUserByCom($com_interna->getPrimaryKey(),$list_users,$usuariocreador_id,$estadocominterna_id,$snext_user);
    $ucom_current = CominternaUsuarioPeer::getCurrentUserAsignado($com_interna->getPrimaryKey());
    //*****************************************************************************************************
    if($ucom_current == null){
        $usuario_creador = CominternaUsuarioPeer::setNextUserProceso($com_interna->getPrimaryKey());
    }
    //*****************************************************************************************************
    $usurioFirma = $this->getFirstUsurioFirmaId($com_interna->getPrimaryKey());   
    if(!$usurioFirma){ $usurioFirma = $usuariologuiado; }
	//*****************************************************************************************************
	$objUsuarioFirma = null;
    if(empty($permiso_regional)){
        $objUsuarioFirma = UsuarioPeer::retrieveByPk($usurioFirma);
        $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());
    }
    //*****************************************************************************************************
    //dependencia        
    if(empty($this->getRequestParameter('dependencia_id'))){
        $objUsuarioFirma = $objUsuarioFirma == null ? UsuarioPeer::retrieveByPk($usurioFirma) : $objUsuarioFirma;
        $com_interna->setDependenciaId($objUsuarioFirma->getDependenciaId());
    }
    //*****************************************************************************************************
    //firma desatendida
    if($this->getUser()->checkPerm("COM_INTERNA_CREAR_FIRMA_DESATENDIDA", $usuariologuiado)){
        $setisautofirma = 0;
        if(UsuarioPeer::countValidateFirmaDesatenida( $list_users['str_firmausers'])){
            $setisautofirma = $this->getRequestParameter(md5('singIsDesatendida')) ? 1 : 0;
        }
        $com_interna->setFirmaDesatendida($setisautofirma);
    }else{
        $com_interna->setFirmaDesatendida(0);
    }
    //*****************************************************************************************************
    $codigo_reen_resp = $com_interna->getCodigoReenResp() ? $com_interna->getCodigoReenResp() : $com_interna->getPrimaryKey();
    $com_interna->setCodigoReenResp($codigo_reen_resp);
    $com_interna->save();
	//*****************************************************************************************************
    $this->guardarAuditoria($com_interna_anterior,$com_interna);
    //*****************************************************************************************************
    $url_redidrect = $this->getRequest()->getScriptName().'/com_interna/edit?cominterna_id='.$com_interna->getPrimaryKey();
    $url_redidrect .= $this->getRequestParameter('respuesta') ?  '&respuesta='.trim($this->getRequestParameter('respuesta')) : "";
    //*****************************************************************************************************
    $qoperation = !empty($this->getRequestParameter('save_and_send')) ? trim($this->getRequestParameter('save_and_send')) : null;
    if($snext_user){
        if(md5('save_and_send'.$usuariologuiado) == $qoperation){
            $this->getResponse()->setContentType('application/json');
            $response_info = array('status' => 200, 'message' => 'La comunicación se envio satisfactoriamente');
            return $this->renderText(json_encode($response_info));            
        }
    }
    return $this->redirect($url_redidrect);
  }
   
  public function executeUpdateSinCR()
  {
    $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $regional_radicacion =  $this->getRequestParameter('regional_id');    
    //*********************************************************************************************
    $com_interna_anterior = clone $com_interna;
    //*********************************************************************************************
    $usuariologuiado=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $objUsuarioLoguiado=UsuarioPeer::retrieveByPk($usuariologuiado);    
    $yearActual=date("Y");
    $com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id') ? $this->getRequestParameter('tipocominterna_id') : null);    
   	$com_interna->setFechaCreacion($this->getRequestParameter('fecha_creacion'));
	$com_interna->setReferencia($this->getRequestParameter('referencia'));    
    $usurioFirma=$this->getFirstUsurioFirmaId($com_interna->getCominternaId());   
    if(!$usurioFirma){
     	$usurioFirma=$usuariologuiado;    
    }  
	//*********************************************************************************************
    //obtiene el usuario que firma la comunicacion para traer la regional
    //luego si el usuario no selecciona ninguna regional se le asigna la del usuario que firma
	$objUsuarioFirma = UsuarioPeer::retrieveByPk($usurioFirma);    
    if($regional_radicacion){       
       $com_interna->setRegionalId($regional_radicacion); 
    }else{       
       $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());
    }
    $contenido = $this->getRequestParameter('contenido');
    $com_interna->setContenido($this->cleanStringTable($contenido));
    //*********************************************************************************************
    $usernameloguiado = $this->getUser()->getAttribute('username', '', 'subscriber');
    $com_interna->setFolios($this->getRequestParameter('folios'));
    $com_interna->setAnexos(trim($this->getRequestParameter('anexos')) != "" ? trim($this->getRequestParameter('anexos')) : null);    
    $com_interna->save();
    //*****************************************************************************************************
    if(trim($this->getRequestParameter('ruta'))){
    	if (!$this->getRequestParameter('cominterna_id')){
    		$ruta = ComInterna::getRutaAdjuntos($this->getRequestParameter('ruta'), false);
    	}else{
    		$ruta = ComInterna::getRutaAdjuntos($this->getRequestParameter('ruta'), true,$com_interna->getRuta());
    	}
    	$com_interna->setRuta($ruta);
    }else{
    	$com_interna->setRuta("");
    }
    //*****************************************************************************************************
    $cargo_radicador = $this->getCargoUsuario($usuariologuiado);    
    $usuariosIn = "";
    $cargoDestinatario= $this->getRequestParameter('cargousuarioId');
    $cargoFirma= $this->getRequestParameter('cargousuarioIdFirma');
    $cargoUserCopia = $this->getRequestParameter('cargousuarioIdCopias');
    $userRadicador= $this->getRequestParameter('radicadorInternaId');
    //*********************************************************************************************
    $currentFormRadicador = "COM_INTERNA_NO_CAMBIAR_RADICADOR";
    if(!$this->getUser()->checkPerm($currentFormRadicador, $usuariologuiado)){
		$usuariosIn .= $this->insertaCominternaUsuarioEdit($usuariologuiado,$com_interna->getPrimaryKey(),1,$cargo_radicador);
	}else{
	   if($userRadicador != ""){
           $cargoradicadorActual=$this->getCargoUsuario($userRadicador);            
           $usuariosIn .= $this->insertaCominternaUsuarioEdit($userRadicador,$com_interna->getPrimaryKey(),1,$cargoradicadorActual);
       }else{
           $usuariosIn .= $this->insertaCominternaUsuarioEdit($usuariologuiado,$com_interna->getPrimaryKey(),1,$cargo_radicador);
       }       
	}
    //*********************************************************************************************
	//$this->borrarCominternaUsuario($com_interna->getPrimaryKey());	 
    if($this->getRequestParameter('firmanteId') != ""){
       $usuariosIn .= $this->insertaCominternaUsuarioEdit($this->getRequestParameter('firmanteId'),$com_interna->getPrimaryKey(),2,$cargoFirma);       
     }else{
       $usuariosIn .= $this->insertaCominternaUsuarioEdit($usuariologuiado,$com_interna->getPrimaryKey(),2,$cargo_radicador);       
    }
    //*********************************************************************************************
    if($this->getRequestParameter('copiaInternaId') != ""){
    	$usuariosIn .= $this->insertaCominternaUsuarioEdit($this->getRequestParameter('copiaInternaId'),$com_interna->getPrimaryKey(),3,$cargoUserCopia);        
    }
    //*********************************************************************************************
    if($this->getRequestParameter('destinatarioId') != ""){
	   $usuariosIn .= $this->insertaCominternaUsuarioEdit($this->getRequestParameter('destinatarioId'),$com_interna->getPrimaryKey(),4,$cargoDestinatario);       
    } 
    //*********************************************************************************************
    //barrar los que nos estan             
    $this->borrarInternaUsuariosEdit($com_interna->getPrimaryKey(),$usuariosIn);
       
	 $usurioFirma=$this->getFirstUsurioFirmaId($com_interna->getCominternaId());   
     if(!$usurioFirma){
     	$usurioFirma=$usuariologuiado;    
	 } 
		
	$com_interna->save();	
	$this->guardarAuditoria($com_interna_anterior,$com_interna);
    return $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getPrimaryKey());	
    
  }
  
  
  public function executeUpdateResponder()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $regional_radicacion =  $this->getRequestParameter('regional_id');
    $com_interna = new ComInterna();
    $com_interna_origen = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $com_interna_anterior = clone $com_interna;
    //****************************************************************************************
    $acceso_com = CominternaUsuarioPeer::getUserAccessComProcess($com_interna_origen->getPrimaryKey(),$usuariologuiado,4);
    $responder_todas = $this->getUser()->checkPerm("COM_INTERNA_RESPONDER_TODAS", $usuariologuiado);
    if($acceso_com == false && $responder_todas == false){
        $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //****************************************************************************************
    $this->forward404Unless($com_interna);	
    $objUsuarioLoguiado = UsuarioPeer::retrieveByPk($usuariologuiado);
    $permiso_regional =  $this->getRequestParameter('regional_id');
    $membrete_default = $objUsuarioLoguiado->getRegional()->getEntidad()->getUsarMembrete() ? 1 : 0;
    //*************************************************************************************************
	$firmas_str = trim($this->getRequestParameter('firmanteId'));
    //*************************************************************************************************
    if(!$objUsuarioLoguiado){
        $dependencia_id = 1;
		$regional_id = 1;
    }elseif(trim($this->getRequestParameter('dependencia_id'))){
        $dependencia_id = trim($this->getRequestParameter('dependencia_id'));
        if($objUsuarioLoguiado->getRegionalId()){
            $regional_id = $objUsuarioLoguiado->getRegionalId();
        }else{
            $regional_id = 1;
        }
    }elseif(trim($this->getRequestParameter('firmanteId'))){
        $list_firmas = explode(",", $firmas_str);
        $user_firma = UsuarioPeer::retrieveByPk($list_firmas[0]);
        $dependencia_id = $user_firma->getDependenciaId();
        //*********************************************************************************************
        if($objUsuarioLoguiado->getRegionalId() ){
            $regional_id = $objUsuarioLoguiado->getRegionalId();
        }else{
            $regional_id = 1;
        }
	}else{
        if($objUsuarioLoguiado->getDependenciaId()){
            $dependencia_id = $objUsuarioLoguiado->getDependenciaId();
        }else{
            $dependiencia_id = 1;	
        }
	    //*********************************************************************************************
        if($objUsuarioLoguiado->getRegionalId() ){
			$regional_id = $objUsuarioLoguiado->getRegionalId();
		}else{
			$regional_id = 1;
		}
	}
    //*************************************************************************************************
    //si tiene el permiso de radicar de otra regional modifica el valor de la variable $regional
    //entonces la comunicacion quedara con el consecutivo de la regional seleccionada
    if($permiso_regional ){ $regional_id = $permiso_regional; }
	$codigo_reen_resp = $com_interna_origen->getCodigoReenResp() ? $com_interna_origen->getCodigoReenResp() : 
						(trim($this->getRequestParameter('codigo_reen_resp')) ? trim($this->getRequestParameter('codigo_reen_resp')) : $com_interna_origen->getPrimaryKey());
    //*************************************************************************************************
    $estadocominterna_id = 1;
    $com_interna->setEstadocominternaId($estadocominterna_id);
    $yearActual = date("Y");
	$com_interna->setPeriodoId($yearActual);
	$com_interna->setTipocominternaId($this->getRequestParameter('tipocominterna_id'));
	$com_interna->setEstadodigitalizacionId(1);
    $com_interna->setFechaCreacion(date("Y-m-d G:i:s"));
	$com_interna->setReferencia($this->getRequestParameter('referencia'));
    $contenido = trim($this->getRequestParameter('contenido'));
    $com_interna->setContenido($this->cleanStringTable($contenido));            	    
    $com_interna->setRadicado("Sin Radicar");
    $com_interna->setEsCopia(0);
    $com_interna->setFolios($this->getRequestParameter('folios'));
    $com_interna->setAnexos(trim($this->getRequestParameter('anexos')) != "" ? trim($this->getRequestParameter('anexos')) : null);
    $com_interna->setRequiereRespuesta($this->getRequestParameter('requiere_respuesta'));
    $com_interna->setEstaentregado(0);    
    $com_interna->setDependenciaId($dependencia_id);        
    $com_interna->setRegionalId($regional_id);
    $com_interna->setCodigoReenResp($codigo_reen_resp);
    $com_interna->setExpedienteId($this->getRequestParameter('unidaddocumental_id') ? $this->getRequestParameter('unidaddocumental_id') : null);
    $com_interna->setTipoDocumentalCod($this->getRequestParameter('tipodocumental_id') ? $this->getRequestParameter('tipodocumental_id') : null);
	$com_interna->setConsecutivoResp($com_interna_origen->getPrimaryKey());
    //*****************************************************************************************************
    if($this->getUser()->checkPerm("CREAR_INTERNA_CON_MEMBRETE", $usuariologuiado)){
        $com_interna->setUseMembrete($this->getRequestParameter('use_membrete') ? $this->getRequestParameter('use_membrete') : 0);
    }else{
        $com_interna->setUseMembrete($membrete_default);
    }
    //*****************************************************************************************************
    $com_interna->save();
    //*****************************************************************************************************
    if(trim($this->getRequestParameter('ruta'))){
        if (!$this->getRequestParameter('cominterna_id')){
            $ruta = ComInterna::getRutaAdjuntos($this->getRequestParameter('ruta'), false);
        }else{
            $ruta = ComInterna::getRutaAdjuntos($this->getRequestParameter('ruta'), true,$com_interna->getRuta());
        }
        $com_interna->setRuta($ruta);
    }else{
        $com_interna->setRuta("");
    }
    //*****************************************************************************************************
    $replyfile = trim($this->getRequestParameter('replyfile'));
    if(!empty($replyfile)){
        $filedir_tmp = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$replyfile;
        //$filedir_upload = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$replyfile;
		$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(53)->getValortexto().'uploads');
		$filedir_upload = simad_util::createPath($dir_raiz.DIRECTORY_SEPARATOR.date("Ymd")).DIRECTORY_SEPARATOR.$replyfile;
        if(file_exists($filedir_tmp)){
            if(rename($filedir_tmp,$filedir_upload)){
                $com_interna->setUrlFileWord($filedir_upload);
                $com_interna->setIsCreateWord(1);
                $com_interna->setContenido(null);
                $com_interna->setUseMembrete(0);
            }
        }
    }else{
        $com_interna->setUrlFileWord(null);
        $com_interna->setIsCreateWord(null);
    }
	//*****************************************************************************************************
    $list_users = array();
    $list_users['str_firmausers'] = trim($this->getRequestParameter('firmanteId')) ? trim($this->getRequestParameter('firmanteId')) : $usuariologuiado;
    $list_users['str_ucargosfirma'] = trim($this->getRequestParameter('cargousuarioIdFirma'));
    $list_users['str_copiausers'] = trim($this->getRequestParameter('copiaInternaId'));
    $list_users['str_ucargoscopia'] = trim($this->getRequestParameter('cargousuarioIdCopias'));
    $list_users['str_revisorusers'] = trim($this->getRequestParameter('revisorUserId'));
    $list_users['str_ucargosrevisor'] = trim($this->getRequestParameter('cargousuarioIdRevisor'));
    $list_users['str_destinorusers'] = trim($this->getRequestParameter('destinatarioId'));
    $list_users['str_ucargosdestino'] = trim($this->getRequestParameter('cargousuarioId'));
    //*****************************************************************************************************	
	$com_interna->setFirmaElectronica(UsuarioPeer::countValidateTipoFirma($list_users['str_firmausers']));
    //*****************************************************************************************************    
    $snext_user = $this->getRequestParameter('save') ? false : true;
	$usuario_creador = CominternaUsuarioPeer::getUserComByRol($com_interna->getPrimaryKey(),1);
    $usuariocreador_id = $usuario_creador != null ? $usuario_creador->getUsuarioId() : $usuariologuiado;
    CominternaUsuarioPeer::initUserByCom($com_interna->getPrimaryKey(),$list_users,$usuariocreador_id,$estadocominterna_id,$snext_user);
    $ucom_current = CominternaUsuarioPeer::getCurrentUserAsignado($com_interna->getPrimaryKey());
    //*****************************************************************************************************
    if($ucom_current == null){
        $usuario_creador = CominternaUsuarioPeer::setNextUserProceso($com_interna->getPrimaryKey());
    }
    //*****************************************************************************************************
    $usurioFirma = $this->getFirstUsurioFirmaId($com_interna->getPrimaryKey());   
    if(!$usurioFirma){ $usurioFirma = $usuariologuiado; }
    //*****************************************************************************************************
	$objUsuarioFirma = null;
    if(empty($permiso_regional)){
        $objUsuarioFirma = UsuarioPeer::retrieveByPk($usurioFirma);
        $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());
    }
    //*****************************************************************************************************
    //dependencia        
    if(empty($this->getRequestParameter('dependencia_id'))){
        $objUsuarioFirma = $objUsuarioFirma == null ? UsuarioPeer::retrieveByPk($usurioFirma) : $objUsuarioFirma;
        $com_interna->setDependenciaId($objUsuarioFirma->getDependenciaId());
    }
    //*****************************************************************************************************
    //firma desatendida
    if($this->getUser()->checkPerm("COM_INTERNA_CREAR_FIRMA_DESATENDIDA", $usuariologuiado)){
        $setisautofirma = 0;
        if(UsuarioPeer::countValidateFirmaDesatenida( $list_users['str_firmausers'])){
            $setisautofirma = $this->getRequestParameter(md5('singIsDesatendida')) ? 1 : 0;
        }
        $com_interna->setFirmaDesatendida($setisautofirma);
    }else{
        $com_interna->setFirmaDesatendida(0);
    }
    //*****************************************************************************************************
	$com_interna->save();
    //*****************************************************************************************************
    $com_interna_origen->setCodigoReenResp($codigo_reen_resp);
    $com_interna_origen->setEstadocominternaId(9);
    $com_interna_origen->save();
    //*****************************************************************************************************
    $this->guardarAuditoria($com_interna_anterior,$com_interna);
    //*****************************************************************************************************    
    return $this->redirect($this->getRequest()->getScriptName().'/com_interna/edit?respuesta='.$com_interna_origen->getPrimaryKey().'&cominterna_id='.$com_interna->getCominternaId());
  }
  
  public function executeUpdateReenviar()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));    
    //****************************************************************************************
    $acceso_com = CominternaUsuarioPeer::getUserAccessComProcess($com_interna->getPrimaryKey(),$usuariologuiado,4);
    $responder_todas = $this->getUser()->checkPerm("COM_INTERNA_RESPONDER_TODAS", $usuariologuiado);
    if($acceso_com == false && $responder_todas == false){
        $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //****************************************************************************************************    
    $codigoreenresp = trim($com_interna->getCodigoReenResp()) ? $com_interna->getCodigoReenResp() : $com_interna->getPrimaryKey();
    $oldfiles = trim($com_interna->getRuta()) ? trim($com_interna->getRuta()) : "";
    //****************************************************************************************************
    $copia_interna = $com_interna->copy();
    $copia_interna->setReferencia($this->getRequestParameter('referencia'));
    $copia_interna->setObsReenResp($this->getRequestParameter('obs_reen_resp'));
    $copia_interna->setCodigoReenResp($codigoreenresp);
    $copia_interna->setEstadocominternaId(5);
    //*****************************************************************************************************
    if($this->getRequestParameter('fecha_maxima_respuesta')){
        $copia_interna->setFechaMaximaRespuesta($this->getRequestParameter('fecha_maxima_respuesta'));
    }
    //*****************************************************************************************************
    if(trim($this->getRequestParameter('ruta'))){
    	$ruta = ComInterna::getRutaAdjuntos($this->getRequestParameter('ruta'), false);        
    	$copia_interna->setRuta($ruta.$oldfiles);
    }else{
    	$copia_interna->setRuta($oldfiles);
    }
    //*****************************************************************************************************
    $copia_interna->save();
    //*****************************************************************************************************
  	$estadocominterna_id = 1;//estado por defecto de la comunicacion
    //if para validar si la comunicacion requiere respuesta    
    if($this->getRequestParameter('requiere_respuesta')?'1':'0'){
	   $estadocominterna_id = 5;//5 es el estado de com interna por responder
    }
    //*****************************************************************************************************
    //obtiene o establece los cargos de los usuarios/////////////////////      	
    $cargo_radicador = $this->getCargoUsuario($usuariologuiado);    
    $cargoDestinatario = $this->getRequestParameter('cargousuarioId');    
    $cargoFirma = $this->getRequestParameter('cargousuarioIdFirma');    
    $cargoUserCopia = $this->getRequestParameter('cargousuarioIdCopias');
    //****************************************************************************************************
	$this->borrarCominternaUsuario($copia_interna->getPrimaryKey());
    //****************************************************************************************************
	$this->insertaCominternaUsuario($usuariologuiado,$copia_interna->getPrimaryKey(),1,$cargo_radicador);
    //****************************************************************************************************
    if($this->getRequestParameter('firmanteId'))
       $this->insertaCominternaUsuario($this->getRequestParameter('firmanteId'),$copia_interna->getPrimaryKey(),2,$cargoFirma);
     else
       $this->insertaCominternaUsuario($usuariologuiado,$copia_interna->getPrimaryKey(),2,null);
    //****************************************************************************************************
	$this->insertaCominternaUsuario($this->getRequestParameter('copiaInternaId'),$copia_interna->getPrimaryKey(),3,$cargoUserCopia);
	$this->insertaCominternaUsuario($this->getRequestParameter('destinatarioId'),$copia_interna->getPrimaryKey(),4,$cargoDestinatario,$estadocominterna_id);
    //****************************************************************************************************
	$usuarios_autorizados = $this->getAutorizaciones();
    $usuarios_autorizados[] = $usuariologuiado;
    //****************************************************************************************************        
	$c = new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID,$com_interna->getPrimaryKey());
	$c->add(CominternaUsuarioPeer::USUARIO_ID,$usuarios_autorizados,Criteria::IN);	
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);	
	$estado_interna = CominternaUsuarioPeer::doSelect($c);
	foreach($estado_interna as $result){
		$this->updateEstadoCom($result,7);        
	}
	//****************************************************************************************************
    if($com_interna->getCodigoReenResp() == ""){
  	   $com_interna->setCodigoReenResp($codigoreenresp);
    }
    $com_interna->setEstadocominternaId(7);
  	$com_interna->save();
    //****************************************************************************************************
	$this->redirect($this->getRequest()->getScriptName().'/com_interna/radicar?reenviar=1&cominterna_id='.$copia_interna->getPrimaryKey());
  }
  
  public function executeUpdateReasignar()
  {
    $currentForm = "COM_INTERNA_REASIGNAR";
  	$this->verificaPrilegioCerrar($currentForm);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //******************************************************************************************************
    $cominterna_id = $this->getRequestParameter('cominterna_id');  	
    $com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
    $newuser = $this->getRequestParameter('idUser') ? trim($this->getRequestParameter('idUser')) : 0;
    $com_interna_anterior = clone $com_interna;
	//******************************************************************************************************
    $codigo_reen_resp = $com_interna->getCodigoReenResp() ? $com_interna->getCodigoReenResp() : $com_interna->getPrimaryKey();
    $observaciones = trim($this->getRequestParameter('observaciones'));
    $listucom_current = CominternaUsuarioPeer::getListUserComByRol($com_interna->getPrimaryKey(),4,1);
  	//******************************************************************************************************
    if(trim($com_interna->getObsReenResp())){
      $observaciones = trim($com_interna->getObsReenResp()). ' | ' . $observaciones;
    }
    //******************************************************************************************************
    $com_interna->setObsReenResp($observaciones);
    $com_interna->setCodigoReenResp($codigo_reen_resp);
    //******************************************************************************************************
    $cargousuarioid = trim($this->getRequestParameter('cargousuarioId'));
    $estadocominterna_id = in_array($com_interna->getEstadocominternaId(),array(5)) ? 5 : 2;//estado por defecto de la comunicacion
    $rolusariocom_id = 4;
    //******************************************************************************************************
    $info_com['usuario_id'] = $newuser;
    $info_com['cusuario_id'] =  $cargousuarioid;
    $info_com['estadocom_id'] = $estadocominterna_id;
    $info_com['rol_id'] = $rolusariocom_id;
    $info_com['esta_asignada'] = 1;
    $info_com['pkcom_id'] = $com_interna->getPrimaryKey();
    $new_ucom = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
    //******************************************************************************************************
    if(count($listucom_current) > 0 && $new_ucom != null){
        foreach ($listucom_current as $ucom_current) {
            $fecha_aprobacion = ($ucom_current->getFechaAprobacion() ? $ucom_current->getFechaAprobacion() : date("Y-m-d G:i:s"));
            $ucom_current->setEstaAsignada(0);
            $ucom_current->setFechaAprobacion($fecha_aprobacion);
            $ucom_current->setCheckAprobacion(1);
			$ucom_current->setEstadocominternaId(7);
            $ucom_current->save();
        }
    }
    //******************************************* SEND EMAIL ***********************************************
    $com_interna->save();
    $encabezado_email = "Este es un mensaje para informarle que se le ha asignado una comunicaci&oacute;n interna y la puede consultar con la siguiente informaci&oacte;n: ";
    $com_interna->envioEmail($newuser,$usuariologuiado,$encabezado_email);
    //******************************************************************************************************
    $this->guardarAuditoria($com_interna_anterior,$com_interna);
    $this->redirect($this->getRequest()->getScriptName().'/com_interna/show?cominterna_id='.$com_interna->getPrimaryKey());
  }

  public function executeReasignar()
  {
    $currentForm = "COM_INTERNA_REASIGNAR";
    $this->verificaPrilegioCerrar($currentForm); 	
    //*******************************************************************************************
    $cominterna_id = $this->getRequestParameter('cominterna_id');
    $this->com_interna = ComInternaPeer::retrieveByPk($cominterna_id);
    $usuario_destino = null;
    //*******************************************************************************************
    $c = new Criteria();
    $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$cominterna_id);
    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
    //$c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
    $cudestino = CominternaUsuarioPeer::doSelect($c);
    foreach ($cudestino as $cusuario_com){
        $usuario_destino = $cusuario_com->getUsuario();
        $this->cargousuarioId = $cusuario_com->getCargousuarioId();
    }
    //*******************************************************************************************
    $this->usuario_destino = $usuario_destino;
    $this->forward404Unless($this->com_interna);
  }

  public function executeInternaWord()
  {
    $this->archivo = $this->getRequestParameter('archivo');
    $this->Remitente = $this->getRequestParameter('Remitente');
    $this->Destinatario = $this->getRequestParameter('Destinatario');
    $this->Asunto = $this->getRequestParameter('Asunto');
    $this->Folios = $this->getRequestParameter('Folios');
    $this->Fecha = $this->getRequestParameter('Fecha');
    $this->Anexos = $this->getRequestParameter('Anexos');
    $this->Radicado = $this->getRequestParameter('Radicado');
    $this->Anexos = $this->getRequestParameter('Anexos');
  }
  
  public function updateEstadoCom(CominternaUsuario $cominternausuario,$estado_id)
  {
    $cominternausuario->setEstadocominternaId($estado_id);
	$cominternausuario->save();
  }
  
  public function getAprobadores($cominterna_id = 0)
  {
    /*********************************************APROBACIONES**********************************************/
    $modulo_id = 2;$matriz_data = array();$col=0;
    $ap = new Criteria();
    $ap->add(ComAprobacionPeer::CONSECUTIVO_ID,$cominterna_id);
    $ap->add(ComAprobacionPeer::MODULO_ID,$modulo_id);
    $reg_aprobadores = ComAprobacionPeer::doSelect($ap);
    foreach($reg_aprobadores as $data)
    {
        $fila=0;
        $matriz_data[$fila][$col] = $data->getUsuarioId();
        $matriz_data[++$fila][$col] = $data->getUsuario()->getNombre().' '.$data->getUsuario()->getApellido();        
        $col++;
    }
    return $matriz_data;
    /*******************************************************************************************************/
  }
  
  public function validarEnvioEmailAprob($cominterna_id = 0,$aprobadores_id = 0,$modulo_id = 2)
  {
    $aprobadores_arr = preg_split("/[,]+/", $aprobadores_id, -1, PREG_SPLIT_NO_EMPTY);
  	$c = new Criteria();
    $c->add(ComAprobacionPeer::CONSECUTIVO_ID,$cominterna_id);
    $c->add(ComAprobacionPeer::MODULO_ID,$modulo_id);
    $c->add(ComAprobacionPeer::USUARIO_ID,$aprobadores_arr,Criteria::IN);
    $c->add(ComAprobacionPeer::ESTADOCOMAPROBACION_ID,1);
    $c->clearSelectColumns();
    $c->addSelectColumn(ComAprobacionPeer::USUARIO_ID);    
    $resultset = ComAprobacionPeer::doSelectStmt($c);
    while($object = $resultset->fetch())
    {
        //$this->sendMailAprob($cominterna_id,$object[0]);
    }
  }
  
  public function executeDelete()
  { 
  	$this->verificaPrilegio("com_interna/delete");
    $com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('cominterna_id'));
    $this->forward404Unless($com_interna);
    $com_interna->delete();
    return $this->redirect($this->getRequest()->getScriptName().'/com_interna/list');
  }
  
  public function borrarCominternaUsuario($com_internaId)
  {
	$conexion = Propel::getConnection();
	$consulta = " delete FROM %s where %s =".$com_internaId;
	$sql      = sprintf($consulta,CominternaUsuarioPeer::TABLE_NAME,CominternaUsuarioPeer::COMINTERNA_ID);
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
  }
  
  public function borrarInternaUsuariosEdit($com_internaId,$usuariosArr )
  {     
    $arrUsuarios=explode(',',$usuariosArr);
    $cont = count($arrUsuarios);
    $usuariosIn = "";
    for($i=0;  $i < ($cont-1); $i++){
        if($i == 0){
           $usuariosIn .= $arrUsuarios[$i];  
        }else{                
           $usuariosIn .= ",".$arrUsuarios[$i]; 
        }              
    }
    //*****************************************************************************************
	$conexion = Propel::getConnection();
	$delete = " DELETE FROM %s WHERE %s =".$com_internaId." AND %s NOT IN(".$usuariosIn.")";
	$delete = sprintf($delete,CominternaUsuarioPeer::TABLE_NAME,CominternaUsuarioPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);        
 	$sentencia = $conexion->prepare($delete);
    $sentencia->execute();		
  }
  
  public function insertaUsuarioReenvio($usuario,$com_internaId,$rol,$cargos)
  {
	$objCominternaUsuario=new CominternaUsuario();
	$objCominternaUsuario->setUsuarioId($usuario);
	$objCominternaUsuario->setCominternaId($com_internaId);
	$objCominternaUsuario->setCargousuarioId($cargos);
	$objCominternaUsuario->setEstadocominternaId(5);
	$objCominternaUsuario->setRolusuariocominternaId($rol);
	$objCominternaUsuario->save();
  }
  
  public function insertaCominternaUsuario($usuarios,$com_internaId,$rol,$cargos,$estadocom_id=1)
  {
    $objCominternaUsuario = null; 
  	if($rol==1){
  		$usu=$usuarios;		
  		$objCominternaUsuario=new CominternaUsuario();
		$objCominternaUsuario->setUsuarioId($usu);
		$objCominternaUsuario->setCominternaId($com_internaId);
		$objCominternaUsuario->setCargousuarioId($cargos);
		$objCominternaUsuario->setEstadocominternaId(1);
		$objCominternaUsuario->setRolusuariocominternaId($rol);
		$objCominternaUsuario->setWfEjecutado(0);
		$objCominternaUsuario->save();
	}else{
		$arrUsuarios=explode(',',$usuarios);
		$arrCargos=explode(',',$cargos);
		$i=0;
		foreach($arrUsuarios as $users){
			if($users!=0){
				$objCominternaUsuario=new CominternaUsuario();
				$objCominternaUsuario->setUsuarioId($users);
				$objCominternaUsuario->setCominternaId($com_internaId);
				$objCominternaUsuario->setRolusuariocominternaId($rol);
				$objCominternaUsuario->setEstadocominternaId($estadocom_id);
				$objCominternaUsuario->setWfEjecutado(0);
				//$cargo=$arrCargos[$i];
				$cargo=$arrCargos[$i];
				if(trim($cargo) == ""){					
				    $cargo=$this->getCargoUsuario($users);
    			}				
				$objCominternaUsuario->setCargousuarioId($cargo);
				$objCominternaUsuario->save();
				$i++;					
			}					
		}     			
	}
  	return $objCominternaUsuario;
  }  
  
  public function insertaCominternaUsuarioEdit($usuarios,$com_internaId,$rol,$cargos)
  {
    $idInternaUsuario = "";
    $c = new Criteria();
  	if($rol==1){
  		$usu=$usuarios;
        $c->add(CominternaUsuarioPeer::USUARIO_ID,$usu);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol);
        $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$com_internaId);
		$objInternaUsuario=CominternaUsuarioPeer::doSelectOne($c);
        if($objInternaUsuario == null){
    		$objCominternaUsuario=new CominternaUsuario();
    		$objCominternaUsuario->setUsuarioId($usu);
    		$objCominternaUsuario->setCominternaId($com_internaId);
            if($cargos == ""){
                $objCominternaUsuario->setCargousuarioId($this->getCargoUsuario($usu));
            }else{
    		    $objCominternaUsuario->setCargousuarioId($cargos);
            }    		
    		$objCominternaUsuario->setEstadocominternaId(2);
    		$objCominternaUsuario->setRolusuariocominternaId($rol);
    		$objCominternaUsuario->save();
            $idInternaUsuario .= $objCominternaUsuario->getPrimaryKey().",";
        }else{
            //var_dump($objEnviadaUsuario);
    		$objInternaUsuario->setUsuarioId($usu);
    		$objInternaUsuario->setCominternaId($com_internaId);    		
    		$objInternaUsuario->setRolusuariocominternaId($rol);
            if($cargos == ""){
                $objInternaUsuario->setCargousuarioId($this->getCargoUsuario($usu));
            }else{
    		    $objInternaUsuario->setCargousuarioId($cargos);
            }    		
    		$objInternaUsuario->save();
            $idInternaUsuario .= $objInternaUsuario->getPrimaryKey().",";
        }
	}else{
		$arrUsuarios=explode(',',$usuarios);
		$arrCargos=explode(',',$cargos);
		$i=0;
		foreach($arrUsuarios as $usu){
			if($usu!=0){
                $c->add(CominternaUsuarioPeer::USUARIO_ID,$usu);
                $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rol);
                $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$com_internaId);
                $objInternaUsuario=CominternaUsuarioPeer::doSelectOne($c);
                if($objInternaUsuario == null){
                    $objCominternaUsuario=new CominternaUsuario();
            		$objCominternaUsuario->setUsuarioId($usu);
            		$objCominternaUsuario->setCominternaId($com_internaId);
                    if($cargos == ""){
                        $objCominternaUsuario->setCargousuarioId($this->getCargoUsuario($usu));
                    }else{
            		    $objCominternaUsuario->setCargousuarioId($cargos);
                    }    
            		$objCominternaUsuario->setEstadocominternaId(2);
            		$objCominternaUsuario->setRolusuariocominternaId($rol);
            		$objCominternaUsuario->save();
                    $idInternaUsuario .= $objCominternaUsuario->getPrimaryKey().",";
                }else{
                    $objInternaUsuario->setUsuarioId($usu);
            		$objInternaUsuario->setCominternaId($com_internaId);    		
            		$objInternaUsuario->setRolusuariocominternaId($rol);
                    if($cargos == ""){
                        $objInternaUsuario->setCargousuarioId($this->getCargoUsuario($usu));
                    }else{
            		    $objInternaUsuario->setCargousuarioId($cargos);
                    }            		
            		$objInternaUsuario->save();
                    $idInternaUsuario .= $objInternaUsuario->getPrimaryKey().",";
                }
                $i++;			  						
			}					
		}     			
	 }
     //********************************************************************************************
     return  $idInternaUsuario; 	
  }
  
  public function getFirstUsurioFirmaName($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$firmante = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
	  $cont=1;					
	}
	return $firmante;
  	
  }
  
  public function AprobadoresAdd($aprobadores_id,$cominterna_id,$modulo_id)
  {
    if(!empty($aprobadores_id) && !empty($modulo_id))
    {
        $aprobadores_arr = preg_split("/[,]+/", $aprobadores_id, -1, PREG_SPLIT_NO_EMPTY);
        $usuarios_actuales = $this->AprobadoresExists($cominterna_id,$modulo_id,$aprobadores_id);
        $this->AprobadoresDelete($cominterna_id,$modulo_id,$aprobadores_arr);        
        foreach($aprobadores_arr as $aprobador)
        {
            if(!in_array($aprobador,$usuarios_actuales,true))
            {
                $obj_insert = new ComAprobacion();
                $obj_insert->setEstadocomaprobacionId(1);
                $obj_insert->setUsuarioId($aprobador);
                $obj_insert->setModuloId($modulo_id);
                $obj_insert->setConsecutivoId($cominterna_id);
                $obj_insert->setFechaCreacion(date("Y-m-d G:i:s"));
                $obj_insert->save();   
            }            
        }
    }
  }    
  
  public function AprobadoresDelete($cominterna_id,$modulo_id,$usuarios_id)
  {
    if($usuarios_id)
    {
    	$conexion = Propel::getConnection();
    	$consulta = " DELETE FROM %s WHERE %s =".$cominterna_id." AND %s =".$modulo_id." AND USUARIO_ID NOT IN(".implode(",",$usuarios_id).");";    
    	$sql      = sprintf($consulta,ComAprobacionPeer::TABLE_NAME,ComAprobacionPeer::CONSECUTIVO_ID,ComAprobacionPeer::MODULO_ID);
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();
    }
  }
  
  public function AprobadoresExists($cominterna_id = 0,$modulo_id = 2,$usuarios_id)
  { 
    $arr_reg = array();
	  $c = new Criteria();
    $c->add(ComAprobacionPeer::CONSECUTIVO_ID,$cominterna_id);
    $c->add(ComAprobacionPeer::MODULO_ID,$modulo_id);
    //$c->add(ComAprobacionPeer::USUARIO_ID,$usuarios_id,Criteria::IN);
    $c->clearSelectColumns();
    $c->addSelectColumn(ComAprobacionPeer::USUARIO_ID);    
    $resultset = ComAprobacionPeer::doSelectStmt($c);
    while($object = $resultset->fetch())
    {
        $arr_reg[] = $object[0];
    }
    return $arr_reg;
  }
  
  public function getRadicadorName($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 1);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$radicador = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
	  $cont=1;					
	}
	return $radicador;  	
  }
  
  public function getRadicadorUserId($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 1);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
    $radicador_id = 0;
	foreach($result as $res){
		if($cont==0)		
			$radicador_id = $res->getUsuarioId();
	  $cont=1;					
	}
	return $radicador_id;  	
  } 
  
  public function getCopiaIdComInterna($com_internaId)
  {
  	$c=new Criteria();
	  $c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	  $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 3);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $userCopia=array();
  	foreach($result as $res)
    {				
  		$userCopia[] = $res->getUsuarioId();	  					
  	}
  	return $userCopia;	
  }
  
  public function getFirstUsurioFirmaId($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	  $c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$firmante = $res->getUsuarioId();
	    $cont=1;					
	}
	return $firmante;  	
  }
  
  public function getPrimerDestinatario($com_internaId)
  { 
  	// consultar firmante inicial
  	$intreturn=0;
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$intreturn = $res->getUsuarioId();
	    $cont=1;					
	}
	return $intreturn;  	
  }    
  
  public function cleanStringTable($content){
  	$clean_string = str_replace('nowrap="nowrap"', ' ', $content);
  	return $clean_string;
  }
  
  private function getCriteriaBasic(Criteria $c)
  {
    //*******************************************************************************************
    //$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
    //$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*******************************************************************************************
    $this->directorio_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
    $this->directorio_alias  = ParametroPeer::retrieveByPk(26)->getValortexto();
    $this->format_digit_img = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
	$this->directorio_adj  = ParametroPeer::retrieveByPk(15)->getValortexto();
	//*******************************************************************************************
    $this->marcarEntrega = "";   	  
	$this->parametros="&a=1";
	$this->user_id = '';
    $this->isBorradorCom = false;
    //*******************************************************************************************
    $perm_list_all = $this->getUser()->checkPerm('COM_INTERNA_LISTAR_TODAS', $usuariologuiado);
	$perm_list_anuladas = $this->getUser()->checkPerm('COM_INTERNA_LISTAR_ANULADAS', $usuariologuiado);
	$estado_anulado = trim($this->getRequestParameter('estadocominterna_id'));
	$periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
	//*******************************************************************************************
	if(trim($this->getRequestParameter('porFunciEntrada')) == "1"){
		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
        $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuariologuiado);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
        //****************************************************************************************
        if($estado_anulado == 4 && $perm_list_anuladas){
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$estado_anulado,Criteria::EQUAL);
        }elseif(!$perm_list_anuladas){
            $estado_anulado = 4;
            $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$estado_anulado,Criteria::NOT_EQUAL);
        }
        //****************************************************************************************
        $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);        
		$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
		$c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        $c->add(ComInternaPeer::PERIODO_ID,$periodo_id);
	 	$this->parametros.="&porFunciEntrada=1";
	}elseif($this->getRequestParameter('porFunciSalida') == "1"){
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
	 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $this->isBorradorCom = false;
        //****************************************************************************************
        if($this->getRequestParameter('estadocominterna_id') != 1){
           $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
        }elseif($this->getRequestParameter('estadocominterna_id') == 1){
            $this->isBorradorCom = true;
        }
        //****************************************************************************************
        $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,4,Criteria::NOT_EQUAL);        
        $c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,6,Criteria::NOT_EQUAL);	
		$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);	
		$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
        $c->add(ComInternaPeer::PERIODO_ID,$periodo_id);
	 	$this->parametros.="&porFunciSalida=1";
	}elseif($this->getRequestParameter('porWorkflow')=="1"){            
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $c->addJoin(ComInternaPeer::INSTANCIA,WfInstanciaPeer::WFINSTANCIA_ID);
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        $c->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
        $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuariologuiado);
        $c->add(WfInstanciaPeer::ESTA_ABIERTA,1);//workflow ejecutandose
        $c->add(CominternaUsuarioPeer::WF_EJECUTADO,0);//pendiente por ejecutar
        $c->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $this->parametros .= "&porWorkflow=" . $this->getRequestParameter('porWorkflow');
        $consulta_buzones = true;
    }elseif($this->getRequestParameter('porProcesoCom')){
        $value_process = $this->getRequestParameter('porProcesoCom');
        //*****************************************************************************************
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $c->add(ComInternaPeer::PERIODO_ID,date("Y"));
        $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1);
        $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
        //*****************************************************************************************
        $rolus_id = 0;$tipoproceso_id = 0;
        if($value_process == md5(4)){//revision
            $rolus_id = 5;$tipoproceso_id = 4;
        }elseif($value_process == md5(2)){//firma
            $rolus_id = 2;$tipoproceso_id = 2;
        }
        //*****************************************************************************************
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,$rolus_id);
        $c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
        $this->parametros.="&porProcesoCom=".md5($tipoproceso_id);
    }else{
		if(!$perm_list_all){
			//si tiene autorizaciones hace un in() incluyendo a el mismo sino como esta
			$arrIds = $this->getAutorizaciones();
		  if($arrIds){
		  	    $arrIds[] = $usuariologuiado;
				$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
			 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$arrIds,Criteria::IN);//in	
				$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);	
				$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
				$c->addor(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
			}else{
				$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);	
			 	$c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
			}
			$this->user_id = implode(",",$arrIds);	
		}else{
		  $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID); 
		}
        //********************************************************************************
        if(!$perm_list_anuladas){
        	$estado_anulado = 4;
        	$c->addAnd(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$estado_anulado,Criteria::NOT_EQUAL);
        }
	}
    //************************************************************************************
	if($this->getRequestParameter('marca')){
		if($this->getRequestParameter('marca')=="sinMarca"){		  	
		  $c->add(ComInternaPeer::MARCA,$usuariologuiado,Criteria::NOT_EQUAL);		
		  $this->parametros.="&marca=".$this->getRequestParameter('marca');	
		}
		else{	
	 	  $c->add(ComInternaPeer::MARCA,$usuariologuiado);
		  $this->parametros.="&marca=".$this->getRequestParameter('marca');
		  $this->marcarEntrega=1;
		}			 	
	}
	//************************************************************************************	
	if($this->getRequestParameter('codigo_reen_resp')){	
	 	$c->add(ComInternaPeer::CODIGO_REEN_RESP,$this->getRequestParameter('codigo_reen_resp'));		
		$this->parametros.="&codigo_reen_resp=".$this->getRequestParameter('codigo_reen_resp');			 	
	}
    //************************************************************************************
    if($this->getRequestParameter('obs_reen_resp')){	
	 	$c->add(ComInternaPeer::OBS_REEN_RESP,'%'.$this->getRequestParameter('obs_reen_resp').'%',Criteria::LIKE);		
		$this->parametros.="&obs_reen_resp=".$this->getRequestParameter('obs_reen_resp');			 	
	}    
	//************************************************************************************
	if($this->getRequestParameter('tipocominterna_id')){	
	 	$c->add(ComInternaPeer::TIPOCOMINTERNA_ID,$this->getRequestParameter('tipocominterna_id'));		
		$this->parametros.="&tipocominterna_id=".$this->getRequestParameter('tipocominterna_id');
	}
	//************************************************************************************
    if($this->getRequestParameter('estadodigitalizacion_id')){	
	 	$c->add(ComInternaPeer::ESTADODIGITALIZACION_ID,$this->getRequestParameter('estadodigitalizacion_id'));		
		$this->parametros.="&estadodigitalizacion_id=".$this->getRequestParameter('estadodigitalizacion_id');
	}
    //************************************************************************************
    if($this->getRequestParameter('prioridadcom_id')){	
        $c->add(ComInternaPeer::PRIORIDADCOM_ID,$this->getRequestParameter('prioridadcom_id'));		
       $this->parametros.="&prioridadcom_id=".$this->getRequestParameter('prioridadcom_id');
    }
    //************************************************************************************
	if($this->getRequestParameter('radicado')){	
	 	$c->add(ComInternaPeer::RADICADO,'%'.$this->getRequestParameter('radicado').'%',Criteria::LIKE);
		$this->parametros.="&radicado=".$this->getRequestParameter('radicado');			 	
	}
	//************************************************************************************
	if($this->getRequestParameter('dependenciaOrigen')){
	 	$c->add(ComInternaPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependenciaOrigen'));		
		$this->parametros.="&dependenciaOrigen=".$this->getRequestParameter('dependenciaOrigen');			 	
	}
	//************************************************************************************
	if($this->getRequestParameter('referencia')){
		$referencia = iconv('ISO8859-1', 'UTF-8', $this->getRequestParameter('referencia'));
		$referencia = trim($this->getRequestParameter('referencia'));
	 	$c->add(ComInternaPeer::REFERENCIA,'%'.$referencia.'%',Criteria::LIKE);
		$this->parametros.="&referencia=".($referencia);
	}
    //************************************************************************************
	if($this->getRequestParameter('contenido')){
	 	$c->add(ComInternaPeer::CONTENIDO,'%'.$this->getRequestParameter('contenido').'%',Criteria::LIKE);		
		$this->parametros.="&contenido=".$this->getRequestParameter('contenido');			 	
	}
    //************************************************************************************
	if($this->getRequestParameter('guia')){
	 	$c->add(ComInternaPeer::GUIA,'%'.$this->getRequestParameter('guia').'%',Criteria::LIKE);		
		$this->parametros.="&guia=".$this->getRequestParameter('guia');			 	
	}
	//////////////////FILTROS POR USUARIO CON DIFERENTES ROLES///////////////////////////////////////////
	if($this->getRequestParameter('usuarioProyecto')){
        $c->addAlias('CIUP',CominternaUsuarioPeer::TABLE_NAME);
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUP.COMINTERNA_ID');
        $c->add('CIUP.USUARIO_ID',$this->getRequestParameter('usuarioProyecto'));            
        $c->add('CIUP.ROLUSUARIOCOMINTERNA_ID',1);
        $this->parametros.="&usuarioProyecto=".$this->getRequestParameter('usuarioProyecto');
	}
	if($this->getRequestParameter('usuarioFirma')){
        $c->addAlias('CIUF',CominternaUsuarioPeer::TABLE_NAME);
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUF.COMINTERNA_ID');
        $c->add('CIUF.USUARIO_ID',$this->getRequestParameter('usuarioFirma'));            
        $c->add('CIUF.ROLUSUARIOCOMINTERNA_ID',2);
 	    $this->parametros.="&usuarioFirma=".$this->getRequestParameter('usuarioFirma');			 	
	}
	if($this->getRequestParameter('usuarioCopia')){
        $c->addAlias('CIUCOP',CominternaUsuarioPeer::TABLE_NAME);
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUCOP.COMINTERNA_ID');
        $c->add('CIUCOP.USUARIO_ID',$this->getRequestParameter('usuarioCopia'));            
        $c->add('CIUCOP.ROLUSUARIOCOMINTERNA_ID',3);
	 	$this->parametros.="&usuarioCopia=".$this->getRequestParameter('usuarioCopia');			 	
	}	

	if($this->getRequestParameter('usuarioDestino')){
        $c->addAlias('CIUDES',CominternaUsuarioPeer::TABLE_NAME);
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,'CIUDES.COMINTERNA_ID');
        $c->add('CIUDES.USUARIO_ID',$this->getRequestParameter('usuarioDestino'));            
        $c->add('CIUDES.ROLUSUARIOCOMINTERNA_ID',4);
	 	$this->parametros.="&usuarioDestino=".$this->getRequestParameter('usuarioDestino');			 	
	}
    //*************************************************************************************************
    if($this->getRequestParameter('estadocominterna_id')){
	   if($this->getRequestParameter('porFunciSalida')=="1"){	      
	      $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$this->getRequestParameter('estadocominterna_id'));           		
       }elseif($this->getRequestParameter('estadocominterna_id') == 4){
       		$c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
       	    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
       	    $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);
       	    $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$this->getRequestParameter('estadocominterna_id'));
       }else{
       	  $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
       	  $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,$this->getRequestParameter('estadocominterna_id'));
       }
	   $this->parametros.="&estadocominterna_id=".$this->getRequestParameter('estadocominterna_id');			 	
	}
    //*************************************************************************************************
    if ($this->getRequestParameter('fechaCreaInicial')) {
        $c->add(ComInternaPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaInicial'),Criteria::GREATER_EQUAL);      
        $this->parametros.="&fechaCreaInicial=".str_replace("/","-",$this->getRequestParameter('fechaCreaInicial'));
	}
    //*************************************************************************************************
    if ($this->getRequestParameter('fechaCreaFinal')) {        
        $c->addAnd(ComInternaPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaFinal')." 23:59",Criteria::LESS_EQUAL);        
        $this->parametros.="&fechaCreaFinal=".str_replace("/","-",$this->getRequestParameter('fechaCreaFinal'));
    }
    //*************************************************************************************************
    if($this->getRequestParameter('estaEntregado')!=""){
	 	$c->add(ComInternaPeer::ESTAENTREGADO,$this->getRequestParameter('estaEntregado'));		
		$this->parametros.="&estaEntregado=".$this->getRequestParameter('estaEntregado');			 	
	} 
	//*************************************************************************************************
    if($this->getRequestParameter('regional_id')!=""){
	 	$c->add(ComInternaPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));		
		$this->parametros.="&regional_id=".$this->getRequestParameter('regional_id');			 	
	}
    //*************************************************************************************************
	if($this->getRequestParameter('periodo_id')){
	 	$c->add(ComInternaPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));		
		$this->parametros.="&periodo_id=".$this->getRequestParameter('periodo_id');			 	
	}
    //*************************************************************************************************
	if ($this->getRequestParameter('fechaEnvioInicial')) {
        $c->add(ComInternaPeer::FECHA_ENVIO_GUIA, $this->getRequestParameter('fechaEnvioInicial'),Criteria::GREATER_EQUAL);      
        $this->parametros.="&fechaEnvioInicial=".str_replace("/","-",$this->getRequestParameter('fechaEnvioInicial'));
	}
    //*************************************************************************************************
    if ($this->getRequestParameter('fechaEnvioFinal')) {        
        $c->addAnd(ComInternaPeer::FECHA_ENVIO_GUIA, $this->getRequestParameter('fechaEnvioFinal'),Criteria::LESS_EQUAL);        
        $this->parametros.="&fechaEnvioFinal=".str_replace("/","-",$this->getRequestParameter('fechaEnvioFinal'));
    }	    
	//*************************************************************************************************
	$orden = trim($this->getRequestParameter('orden'));
	if($orden){
	    if($orden == "FECHA_CREACION"){ $c->addDescendingOrderByColumn(ComInternaPeer::FECHA_CREACION); }
		if($orden == "RADICADO"){ $c->addDescendingOrderByColumn(ComInternaPeer::RADICADO); }
		if($orden == "REFERENCIA"){ $c->addDescendingOrderByColumn(ComInternaPeer::REFERENCIA); }
		if($orden == "NUMERO_RADICACION"){ $c->addDescendingOrderByColumn(ComInternaPeer::NUMERO_RADICACION); }
		$this->parametros.="&orden=".$this->getRequestParameter('orden');			 	
    }else{
		$c->addDescendingOrderByColumn(ComInternaPeer::FECHA_CREACION);
	}
	//************************************************************************************************
	$c->addDescendingOrderByColumn(ComInternaPeer::COMINTERNA_ID);
	//************************************************************************************************
    return $c;
  }
}