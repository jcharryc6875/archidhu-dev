<?php

/**
 * acto_administrativo actions.
 *
 * @package    simad
 * @subpackage acto_administrativo
 * @author     javier.charry@gmail.com
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class acto_administrativoActions extends sfActions
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
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
  }

  public function tienePrivilegio($currentForm)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    return $this->getUser()->checkPerm($currentForm, $usuariologuiado);
  }

  public function executePublicar()
  { 
    $this->verificaPrilegio("ACTO_ADMINISTRATIVO_PUBLICAR");
    $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id');
    $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    //********************************************************************************************
    if(!in_array($this->acto_administrativo->getEstadoactoadministrativoId(),array(6, 7, 9)))
    {
      return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/show?actoadministrativo_id='.$this->acto_administrativo->getCominternaId());
    }
    //********************************************************************************************
    $this->forward404Unless($this->acto_administrativo);    
  }

  public function executeUpdateCartelera()
  {
    $this->verificaPrilegio("ACTO_ADMINISTRATIVO_PUBLICAR");
    //*************************************************************************************
    $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : null;
    $obs_publicacion = trim($this->getRequestParameter('obs_publicacion')) ? trim($this->getRequestParameter('obs_publicacion')) : null;
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    //*************************************************************************************
    try
    {
      if(!in_array($acto_administrativo->getEstadoactoadministrativoId(), array(6, 7, 9)))
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

      $consecutivo_id = $acto_administrativo->getPrimaryKey();
      $modulo_id = ModulesEnable::ActosAdministrativos;
      $alreadyPublished = CarteleraInformativaPeer::getDocumentIsPublished($consecutivo_id, $modulo_id);

      if($alreadyPublished)
      {
        $data_array['status'] = 402;
        $data_array['mensaje'] = 'Error: Este acto administrativo ya se encuentra publicado';
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
        $params['periodo_id'] = $acto_administrativo->getPeriodoId();
        $params['estadodigitalizacion_id'] = $acto_administrativo->getEstadodigitalizacionId();
        $params['estadopublicacion_id'] = StatusPublicacion::Publicado;
        $params['dependencia_id'] = $acto_administrativo->getDependenciaId();
        $params['asunto'] = $acto_administrativo->getAsunto();
        $params['radicado'] = $acto_administrativo->getRadicadoCompuesto();
        $params['fecha_documento'] = $acto_administrativo->getFechaCreacion();
        $params['tipo_documento'] =  $acto_administrativo->getPlantillascomId() ? mb_strtoupper($acto_administrativo->getPlantillasCom()->getDescripcion()) : "ACTO ADMINISTRATIVO";
        $params['observaciones'] = $obs_publicacion;

        $creado = CarteleraInformativaPeer::addRegistroCartelera($params);

        if($creado)
        {
            $data_array['status'] = 200;
            $data_array['mensaje'] = 'Acto administrativo publicado correctamente';
            $data_array['url_back'] = $this->getRequest()->getScriptName() . '/acto_administrativo/show?actoadministrativo_id=' . $acto_administrativo->getPrimaryKey();
            //*****************************************************************************
            $this->getUser()->setFlash('message_success', 'Acto administrativo publicado correctamente');
            $this->getResponse()->setContentType('application/json');
            $data_json = json_encode($data_array);
            return $this->renderText($data_json);
        }
        else
        {
            $data_array['status'] = 401;
            $data_array['mensaje'] = 'Error: Este acto administrativo no se ha podido publicar';
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

  public function executeMarcarEntregados()
  {
    $currentForm = "acto_administrativo/list";
    //**************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $conexion = Propel::getConnection();
    //**************************************************************************************************
    $this->usuariologuiado = $usuariologuiado;
    $this->verificaPrilegio($currentForm);	
    $this->parametros = "&a=1";
    $this->papelera = false;
    //**************************************************************************************************
    $wherec = new Criteria();
    $wherec = $this->getCriteriaBasic($wherec);
    $wherec->setDistinct();
    $wherec->clearSelectColumns();
    //**************************************************************************************************
    try{
      $updc = new Criteria();
      $updc->add(ActoAdministrativoPeer::ESTAENTREGADO, 1);
      $affectedRows = BasePeer::doUpdate($wherec, $updc, $conexion);
      //************************************************************************************************
      $this->message_info = sprintf('Los %s registros fueron marcadas satisfactoriamente',$affectedRows);
      $this->isError = false;
    }catch (PropelException $ex){
      $this->message_info = 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage();
      $this->isError = true;
    }catch (Exception $ex){
      $this->message_info = 'Error interno del servidor, '.$ex->getMessage();
      $this->isError = true;
    }
    //**************************************************************************************************
    $this->setTemplate('marcarTodos');
  }

  public function executeMarcarTodos()
  {
    $currentForm = "acto_administrativo/list";
    //**************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $conexion = Propel::getConnection();
    //**************************************************************************************************
    $this->usuariologuiado = $usuariologuiado;
    $this->verificaPrilegio($currentForm);	
    $this->parametros = "&a=1";
    $this->papelera = false;
    //**************************************************************************************************
    $wherec = new Criteria();
    $wherec = $this->getCriteriaBasic($wherec);
    $wherec->setDistinct();
    $wherec->clearSelectColumns();
      //**************************************************************************************************
    try{
      $updc = new Criteria();
      $updc->add(ActoAdministrativoPeer::MARCA, $usuariologuiado);
      $affectedRows = BasePeer::doUpdate($wherec, $updc, $conexion);
      //**********************************************************************************************
      $this->message_info = sprintf('Los %s registros fueron marcadas satisfactoriamente',$affectedRows);
      $this->isError = false;
    }catch (PropelException $ex){
      $this->message_info = 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage();
      $this->isError = true;
    }catch (Exception $ex){
      $this->message_info = 'Error interno del servidor, '.$ex->getMessage();
      $this->isError = true;
    }
  }

  public function executeDesmarcarTodos()
	{
		$currentForm="acto_administrativo/list";
		//**************************************************************************************************
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
			$updc->add(ActoAdministrativoPeer::MARCA, 0);
			$affectedRows = BasePeer::doUpdate($wherec, $updc, $conexion);
			//**********************************************************************************************
			$this->message_info = 'Los registros fueron desmarcados satisfactoriamente';
			$this->isError = false;
		}catch (PropelException $ex){
			$this->message_info = 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage();
			$this->isError = true;
		}catch (Exception $ex){
			$this->message_info = 'Error interno del servidor, '.$ex->getMessage();
			$this->isError = true;
		}
	}

  public function executeMarcar()
  {
    $marcar = 0;
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*******************************************************************************************
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPK(trim($this->getRequestParameter('actoadministrativo_id')));
    //*******************************************************************************************
    if(empty($acto_administrativo->getMarca()) || $acto_administrativo->getMarca() != $usuariologuiado)
    {
      $marcar = $usuariologuiado;
    }
    //*******************************************************************************************
    $acto_administrativo->setMarca($marcar);
    $acto_administrativo->save();
    //*******************************************************************************************
    return sfView::NONE;
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex()
  {
    /*$dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto() . 'tmp2');
    $filedir_upload = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd"));

    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk(337);
    $params['use_membrete'] = $acto_administrativo->getUseMembrete();
    $params['membrete_com'] = $acto_administrativo->getRegional()->getImageMembrete();
    $params['comobject_id'] = $acto_administrativo->getPrimaryKey();
    $params['periodo_id'] = $acto_administrativo->getPeriodoId();
    $response_tpl = $acto_administrativo->getPlantillasCom()->generateWordByPlantilla($filedir_upload,$params);*/

    /*$acto_administrativo = ActoAdministrativoPeer::retrieveByPk(255);
    $simadSoap = new WsSimadUariv();
    $response_acto = $simadSoap->loadWsRadActoAdministrativo($acto_administrativo->getPrimaryKey(),"Acto Administrativo",ModulesEnable::ActosAdministrativos);*/

    /*$weblog_id = 13108;
    $ws_log = WebserviceLogPeer::retrieveByPK($weblog_id);
    $xml_fname = $ws_log->getPrimaryKey() . '_wsinfo.xml';
    file_put_contents(sfConfig::get('sf_log_dir') . '/' . $xml_fname, $ws_log->getRequestInfo());*/

    $this->forward('acto_administrativo', 'consulta');
  }

  /**
  * Executes consulta action
  *
  */
  public function executeConsulta()
  {
    $this->verificaPrilegio("acto_administrativo/consulta");
    $this->acto_administrativo = new ActoAdministrativo();
    //**************************************************************************************************
    $this->regionales = RegionalPeer::getAllRegional(); 
  }

  public function executeSignWsContract()
  {
    try {
      $response_process = array('httpStatus' => 400, 'message' => 'Error interno del servidor');
      //***************************************************************************************************
      $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id'));
      $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
      $response_process = $acto_administrativo->signDocumentProcess(true);
      //***************************************************************************************************
      if($response_process['httpStatus'] == 200){
        $resp_serv = $acto_administrativo->initServicioProcess();
        if($resp_serv['isError'] == true){
          $response_process['message'] = sprintf("%s => %s",$response_process['message'],$resp_serv['message']);
        }
      }
    } catch (PropelException $th) {
      $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
    } catch (\Exception $th) {
      $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
    } catch (\Throwable $th) {
      $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
    }
    //*******************************************************************************************************
    $this->getResponse()->setContentType('application/json');      
    return $this->renderText(json_encode($response_process));
  }

  public function executeShow()
  {
    $this->verificaPrilegioCerrar("acto_administrativo/show");
    $acto_administrativo = $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($this->getRequestParameter('actoadministrativo_id'));
    $this->forward404Unless($acto_administrativo);
    //**************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if (!$this->usuarioTieneAccesoActoAdministrativo($acto_administrativo, $usuariologuiado)) {
      $this->getUser()->setFlash('messages_error', ConsultaPermisoHelper::MSG_SIN_PERMISOS);
      return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/list');
    }
    //**************************************************************************************************
    $stateview = trim($this->getRequestParameter('viewstate'));
    $backid = trim($this->getRequestParameter('backid'));
    $this->dataShared = ContenidoUnidadDocumentalPeer::getFormatSharedComUrlView($backid,$acto_administrativo->getPrimaryKey(),$stateview,17);    
    //**************************************************************************************************
    $this->digitDocumentFile = $acto_administrativo->getPathImageDigitByCom();
    if(file_exists($this->digitDocumentFile)){
      $acto_administrativo->setEstadodigitalizacionId(2);
    }else{
        $acto_administrativo->setEstadodigitalizacionId(1);
    }
    //**************************************************************************************************
    $acto_administrativo->save();
    //**************************************************************************************************
    $user_firman = array();$this->estado_acto_id = 0;$usuario_asigando = array();$user_copias = array();$firmas_desatendida = true;
    $firmas_aprobacion = array();$object_asignado = null;$aprob_urlist = array();
    $destinatariosIds = array();$cargousuarioIdDestinos = array();$udestinarios_names = array();
    $list_iusers = $acto_administrativo->getActoadministrativoUsuarios();
    foreach($list_iusers as $iuobject){
        if($iuobject->getUsuarioId() == $usuariologuiado){
            if(in_array($iuobject->getEstadoactoadministrativoId(),array(6))){
                $iuobject->setEstadoactoadministrativoId(9);
                $iuobject->setFechaLectura(date("Y-m-d G:i:s"));
                $iuobject->save();
                //**************************************************************************************
                $acto_administrativo->setEstadoactoadministrativoId(9);
                $acto_administrativo->save();
            }
            $estado = $iuobject->getEstadoActoAdministrativo()->getDescripcion();
            $this->estado_acto_id = $iuobject->getEstadoactoadministrativoId();
        }elseif($iuobject->getRolusuarioactoadministvoId() == 2){
            //en caso q e usuario logueado no sea copia se devuelve el estado del que firma
            $estado = $iuobject->getEstadoActoAdministrativo()->getDescripcion();
            $this->estado_acto_id = $iuobject->getEstadoactoadministrativoId();        
        }
        //******************************************************************************************************
        if($iuobject->getRolusuarioactoadministvoId() == 2){
          $user_firman[] = $iuobject->getUsuarioId();
          if($iuobject->getEstaAprobado() == 0){ 
            if(!$iuobject->getUsuario()->getFirmaDesatendida()){
              $firmas_aprobacion[] = $iuobject->getUsuarioId();
              //************************************************************************************************
              if(!empty($acto_administrativo->getPlantillascomId())){
                if($acto_administrativo->getPlantillasCom()->getDependenciaId() == $iuobject->getUsuario()->getDependenciaId()){
                  $firmas_desatendida = $acto_administrativo->getPlantillasCom()->getFirmaDesatendida() ? true : false;
                }else{
                  $firmas_desatendida = false;
                }
              }else{
                $firmas_desatendida = false;
              }
            }
          }
        }
        //******************************************************************************************************
        if($iuobject->getRolusuarioactoadministvoId() == 3){        
            $list_revisores[] = $iuobject->getUsuarioId();
        }
        //******************************************************************************************************
        if($iuobject->getRolusuarioactoadministvoId() == 1){
          $this->radicador = $iuobject->getUsuario();
          $this->radicador_id = $iuobject->getUsuarioId();
        }
        //******************************************************************************************************
        if($iuobject->getRolusuarioactoadministvoId() == 6){
          $destinatariosIds[] = $iuobject->getUsuarioId();
          $cargousuarioIdDestinos[] = $iuobject->getCargousuarioId();
          $udestinarios_names[] = $iuobject->getUsuario();
        }
        //******************************************************************************************************
        if($iuobject->getEstaAsignada() == 1){ 
            $usuario_asigando[] = $iuobject->getUsuarioId();
            $object_asignado = $iuobject;
        }
    }
    //**********************************************************************************************************
    $aprob_urlist = $firmas_desatendida ? ActoadministrativoUsuarioPeer::getListUncheckApro($acto_administrativo->getPrimaryKey(),0,array(3,4)) : 
            ActoadministrativoUsuarioPeer::getListUncheckApro($acto_administrativo->getPrimaryKey());
    //**********************************************************************************************************
    if (($clave = array_search($usuariologuiado, $aprob_urlist)) !== false) {
        unset($aprob_urlist[$clave]);
    }
    //**********************************************************************************************************
    $this->estado = $estado;
    $this->usersCopia = $user_copias;
    $this->usuarios_firman = $user_firman;
    $this->users_aprueban = $firmas_aprobacion;
    $this->users_asignado = $usuario_asigando;
    $this->firmas_desatendida = $firmas_desatendida;
    $this->tipoprocesocom_id = $object_asignado != null ? $object_asignado->getTipoprocesocomId() : 0;
    $this->aprobacion_count = count($aprob_urlist);
    $this->destinatariosIds = $destinatariosIds;
    $this->cargousuarioIdDestinos = $cargousuarioIdDestinos;
    $this->udestinarios_names = $udestinarios_names;
    //**********************************************************************************************************
    //verificar permiso firma electronica(la misma firma mecanica) para radicar
    $this->permisoRadicarFirmaElectronica = 0;
    if($this->estado_acto_id == 1){
      $this->permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,implode(",",$user_firman),4);
    }
    //**********************************************************************************************************
    $this->forward404Unless($this->acto_administrativo);
  }

  public function executeSingComCheck()
  {
      try {
          $status = 400;
          $actoadministrativo_id = !empty($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : null;
          $response_data = array( 'status' => 400, 'message' => 'error interno no se realizo la aprobación');
          $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
          $rol_firma = 2;
          //************************************************************************************************
          if($actoadministrativo_id != null){
              $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
              //********************************************************************************************
              $ucom_current = ActoAdministrativoPeer::getCurrentUserAsignado($acto_administrativo->getPrimaryKey());
              //********************************************************************************************
              if($ucom_current == null){
                  $array = json_encode($response_data);
                  $this->getResponse()->setContentType('application/json');      
                  return $this->renderText($array);
              }
              //********************************************************************************************
              if($ucom_current->getUsuarioId() != $usuariologuiado){
                  $array = json_encode($response_data);
                  $this->getResponse()->setContentType('application/json');      
                  return $this->renderText($array);
              }
              //********************************************************************************************
              $ucom_next = ActoAdministrativoPeer::setNextUserProceso($acto_administrativo->getPrimaryKey());
              $usuario_asignado = $ucom_next != null ? $ucom_next->getUsuario() : null;
              //********************************************************************************************
              if($usuario_asignado != null){
                  $nombre_asignado = $usuario_asignado->getFullNombre();
                  //$email_destino = !empty($usuario_asignado->getEmail()) ? trim($usuario_asignado->getEmail()) : null;
                  $acto_administrativo->sendMailAprob($usuario_asignado);
              }
              //********************************************************************************************
              $response_data = array( 'status' => 200, 'message' => 'Se envio el registro al siguiente usuario del proceso ('.$nombre_asignado.')');
          }else{
              $response_data = array( 'status' => $status, 'message' => 'Error la información, el registro no es valida');
          }
      } catch (\Exception $ex) {
          $response_data = array( 'status' => 400, 'message' => $ex->getMessage());
      }
      //****************************************************************************************************
      $array = json_encode($response_data);
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText($array);
  }

  public function executeShowPdfByWord()
  {
    $base_path = sfConfig::get('base_simad');
    $this->verificaPrilegio("acto_administrativo/previsualizar");
    $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : 0;
    //***************************************************************************************************
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    //***************************************************************************************************
    if(!empty($acto_administrativo->getUrlFileWord())){
      if(file_exists(trim($acto_administrativo->getUrlFileWord()))){
        $filegenerate = $acto_administrativo->generatePdfByFile(trim($acto_administrativo->getUrlFileWord()),false);
        $extension = pathinfo($filegenerate, PATHINFO_EXTENSION);
        $url_viewer = $base_path.'/tmp/'.$filegenerate;
        //***********************************************************************************************
        if(strtolower($extension) == 'pdf'){ 
          $url_viewer = $base_path.'/viewerEx.php?fileview='.$filegenerate; 
          if($acto_administrativo->getEstadoactoadministrativoId() == 5){
              $pathtmp = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR . $filegenerate;
              $relf_path = pathinfo($filegenerate, PATHINFO_DIRNAME);
              //*****************************************************************************************
              $tanulado = "DOCUMENTO ANULADO";
              $fanulado = $acto_administrativo->getFechaAnulacion();
              $pdfTools = new PdfTools();
              $fnewTmp = $pdfTools->setPdfWatherMark($pathtmp,$tanulado,$fanulado);
              $url_viewer = $base_path.'/viewerEx.php?fileview='.sprintf("%s/%s",$relf_path,$fnewTmp);
          }
        }
        //***********************************************************************************************
        $this->redirect($url_viewer);
      }
    }
  }

  /**
  * Executes showpdf action
  *
  * @param sfRequest $request A request object
  */
  public function executeShowpdf()
  {
    $this->verificaPrilegio("acto_administrativo/previsualizar");
    $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : 0;
    $base_path = sfConfig::get('base_simad');
    //*************************************************************************************
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    //*************************************************************************************
    if($acto_administrativo->getIsCreateWord() && trim($acto_administrativo->getUrlFileWord())){
      return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/showPdfByWord?actoadministrativo_id='.$acto_administrativo->getPrimaryKey());
    }
    //*************************************************************************************
    if($acto_administrativo->getPlantillascomId()){            
        //MARGENES DE IMPRESION
        $params_margin['top'] = 30;
        $params_margin['left'] = 20;
        $params_margin['buttom'] = 25;
        $params_margin['rigth'] = 18;
        //*********************************************************************************
        $filegenerate = $acto_administrativo->generateFileInDisk($params_margin);
    }else{
      $this->redirect($base_path.'/no_file_exists.html');
    }    
    //*************************************************************************************
    if(file_exists($filegenerate)){
      $extension = pathinfo($filegenerate, PATHINFO_EXTENSION);
      $url_viewer = $base_path.'/tmp/'.basename($filegenerate);
      if(strtolower($extension) == 'pdf'){
          $url_viewer = $base_path.'/viewerEx.php?fileview='.basename($filegenerate);
        //*********************************************************************************
        if(strtolower($extension) == 'pdf'){
          $url_viewer = $base_path.'/viewerEx.php?fileview='.basename($filegenerate);
                if($acto_administrativo->getEstadoactoadministrativoId() == 4){
                    $tanulado = "DOCUMENTO ANULADO";
                    $fanulado = $acto_administrativo->getFechaAnulacion();
                    $pdfTools = new PdfTools();
                    $fnewTmp = $pdfTools->setPdfWatherMark($filegenerate,$tanulado,$fanulado);
                    $url_viewer = $base_path.'/viewerEx.php?fileview='.$fnewTmp;
                }
            }
      }
      //***********************************************************************************
      $this->redirect($url_viewer);
    }else{
      $this->redirect($base_path.'/no_file_exists.html');
    }
    //*************************************************************************************
    $this->forward404Unless($this->acto_administrativo);
  }

  public function executeRejectedObs()
  {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : -1;
      $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
      $this->devoluciones_list = ActoadministrativoUsuarioPeer::getDevolucionesData($actoadministrativo_id,array(2,3,4),array($usuariologuiado));
      $this->actoadmin_devolucion = new ActoadminDevolucion();
  }

  public function executeRejectedLinksOpt()
  {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : -1;
      $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
      $this->devoluciones_list = ActoadministrativoUsuarioPeer::getDevolucionesData($actoadministrativo_id,array(2,3,4),array($usuariologuiado));
  }

  public function executeReasignarAct()
  {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $currentForm = "ACTO_ADMINISTRATIVO_DEVOLUCIONES_FLUJO";
      $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
      //**********************************************************************************************
      if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
          $response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
          $array = json_encode($response_data);
          $this->getResponse()->setContentType('application/json');      
          return $this->renderText($array);
      }
      //**********************************************************************************************
      $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : 0;
      $usuariodestino_id = $this->getRequestParameter('usuariodestino_id') ? $this->getRequestParameter('usuariodestino_id') : 0;
      $rolusuarioactoadministvo_id = $this->getRequestParameter('rolusuarioactoadministvo_id') ? $this->getRequestParameter('rolusuarioactoadministvo_id') : 0;
      $tipoprocesocom_id = $this->getRequestParameter('tipoprocesocom_id') ? $this->getRequestParameter('tipoprocesocom_id') : 1;
      $cusuario_id = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariodestino_id);
      //**********************************************************************************************
      if(!empty($actoadministrativo_id) && !empty($usuariodestino_id) && !empty($rolusuarioactoadministvo_id)){
          $info_com = array();
          $info_com['pkcom_id'] = $actoadministrativo_id;
          $info_com['usuario_id'] = $usuariodestino_id;
          $info_com['cusuario_id'] =  $cusuario_id;
          $info_com['estadocom_id'] = 1;
          $info_com['rol_id'] = $rolusuarioactoadministvo_id;
          $info_com['esta_asignada'] = 1;
          //*******************************************************************************************
          $uacto_current = ActoAdministrativoPeer::getCurrentUserAsignado($actoadministrativo_id);
          $nuser_asignado = "";
          //*******************************************************************************************
          if($rolusuarioactoadministvo_id == 1){
              $uacto_administrativo = ActoAdministrativoPeer::getUserAddedInCom($info_com['pkcom_id'],$info_com['usuario_id'],$info_com['rol_id']);
              if($uacto_administrativo != null){
                  $uacto_administrativo->setEstaAprobado(0);
                  $uacto_administrativo->setFechaAprobacion(null);
                  $uacto_administrativo->setEstaAsignada(1);
                  $uacto_administrativo->setFechaAsigna(date("Y-m-d G:i:s"));
                  $uacto_administrativo->save();
                  //************************************************************************************
                  $nuser_asignado = $uacto_administrativo->getUsuario()->getNombreAll();
              }else{
                  $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
                  $array = json_encode($response_data);
                  $this->getResponse()->setContentType('application/json');      
                  return $this->renderText($array);
              }
          }else{
              $info_com['tipoprocesocom_id'] = $tipoprocesocom_id;
              $uacto_administrativo = ActoAdministrativoPeer::addUserByActo($info_com);
              $nuser_asignado = $uacto_administrativo!= null ? $uacto_administrativo->getUsuario()->getNombreAll() : "";
          }
          //*********************************************************************************************
          if($uacto_current != null){            
              $uacto_current->setEstaAsignada(0);
              $uacto_current->save();
          }
          //*********************************************************************************************
          $response_data = array( 'status' => 200, 'message' => 'Se asignó el acto administrativo al usuario '.$nuser_asignado.'satisfactoriamente');
      }else{
          $response_data = array( 'status' => 400, 'message' => 'La información enviada no es valida o esta incompleta, no se realizó la devolución');
      }
      //*************************************************************************************************
      $array = json_encode($response_data);
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText($array);
  }

  public function executeReasignarActLite()
  {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $currentForm = "ACTO_ADMINISTRATIVO_DEVOLUCIONES_FLUJO";
      $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
      $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : 0;
      $rejectedinfo_id = trim($this->getRequestParameter('rejectedinfo_id')) ? trim($this->getRequestParameter('rejectedinfo_id')) : 0;
      $obs_devolucion = trim($this->getRequestParameter('obsdevolucion')) ? trim($this->getRequestParameter('obsdevolucion')) : null;
      //**********************************************************************************************
      if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
          $response_data = array( 'status' => 400, 'message' => 'Acceso denegado, no tienes permiso para realizar esta actividad');
          $array = json_encode($response_data);
          $this->getResponse()->setContentType('application/json');      
          return $this->renderText($array);
      }
      //********************************************************************************************** 
      if(empty(trim($actoadministrativo_id)) || empty(trim($rejectedinfo_id)) || empty(trim($obs_devolucion))){
          $response_data = array( 'status' => 400, 'message' => 'Error, los parametros enviados no son correctos');
          $array = json_encode($response_data);
          $this->getResponse()->setContentType('application/json');      
          return $this->renderText($array);
      }
      //********************************************************************************************** 
      $post_json = json_decode(SED::decryption($rejectedinfo_id));
      //**********************************************************************************************      
      $usuariodestino_id = $post_json->usuariodestino_id ? $post_json->usuariodestino_id : 0;
      $rolusuarioactoadministvo_id = $post_json->rolusuarioactoadm_id ? $post_json->rolusuarioactoadm_id : 0;
      $tipoprocesocom_id = $post_json->tipoprocesocom_id ? $post_json->tipoprocesocom_id : 1;
      $cusuario_id = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariodestino_id);
      //**********************************************************************************************
      if(!empty($actoadministrativo_id) && !empty($usuariodestino_id) && !empty($rolusuarioactoadministvo_id)){
          $info_com = array();
          $info_com['pkcom_id'] = $actoadministrativo_id;
          $info_com['usuario_id'] = $usuariodestino_id;
          $info_com['cusuario_id'] =  $cusuario_id;
          $info_com['estadocom_id'] = 1;
          $info_com['rol_id'] = $rolusuarioactoadministvo_id;
          $info_com['esta_asignada'] = 1;
          //*******************************************************************************************
          $uacto_current = ActoAdministrativoPeer::getCurrentUserAsignado($actoadministrativo_id);
          $nuser_asignado = "";
          $uacto_administrativo = null;
          //*******************************************************************************************
          if($rolusuarioactoadministvo_id == 1){
              $uacto_administrativo = ActoAdministrativoPeer::getUserAddedInCom($info_com['pkcom_id'],$info_com['usuario_id'],$info_com['rol_id']);
              if($uacto_administrativo != null){
                  $uacto_administrativo->setEstaAprobado(0);
                  $uacto_administrativo->setFechaAprobacion(null);
                  $uacto_administrativo->setEstaAsignada(1);
                  $uacto_administrativo->setFechaAsigna(date("Y-m-d G:i:s"));
                  $uacto_administrativo->save();
                  //************************************************************************************
                  $nuser_asignado = $uacto_administrativo->getUsuario()->getNombreAll();
              }else{
                  $response_data = array( 'status' => 400, 'message' => 'Error interno, no se realizó la devolución');
                  $array = json_encode($response_data);
                  $this->getResponse()->setContentType('application/json');      
                  return $this->renderText($array);
              }
          }else{
              $info_com['tipoprocesocom_id'] = $tipoprocesocom_id;
              $uacto_administrativo = ActoAdministrativoPeer::addUserByActo($info_com);
              $nuser_asignado = $uacto_administrativo!= null ? $uacto_administrativo->getUsuario()->getNombreAll() : "";
          }
          //*********************************************************************************************
          if($uacto_current != null){
              if($uacto_current->getPrimaryKey() != $uacto_administrativo->getPrimaryKey()){
                $uacto_current->setEstaAsignada(0);
                $uacto_current->setEstaAprobado(0);
                $uacto_current->setFechaAprobacion(null);
                $uacto_current->setFechaAsigna(null);
                $uacto_current->save();
              }
          }
          //*********************************************************************************************
          ActoadminDevolucionPeer::addDevolucionByPkActoAdmon($actoadministrativo_id,$usuariologuiado,$obs_devolucion);
          //*********************************************************************************************
          $response_data = array( 'status' => 200, 'message' => 'Se asignó el acto administrativo al usuario '.$nuser_asignado.' satisfactoriamente');
      }else{
          $response_data = array( 'status' => 400, 'message' => 'La información enviada no es valida o esta incompleta, no se realizó la devolución');
      }
      //*************************************************************************************************
      $array = json_encode($response_data);
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText($array);
  }

  /**
  * Executes create action
  *
  */
  public function executeCreate()
  {
    $this->verificaPrilegio("acto_administrativo/create");
    //***************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $this->usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $parametro_firmas = ParametroPeer::retrieveByPk(62); 
    //***************************************************************************************************
    if($parametro_firmas){
      $this->firmanteId = $this->usuario->getPrimaryKey().',';
      $this->firmanteName = $this->usuario->getNombreAll().',';
      $this->cargousuarioIdFirma = CargoUsuarioPeer::getCargoUsuarioByIdUser($this->usuario->getPrimaryKey()).',';
    }else{
        $this->firmanteId = "";
      $this->firmanteName = "";
      $this->cargousuarioIdFirma = "";
    }
    //***************************************************************************************************
    $this->setTemplate('edit');
    $this->acto_administrativo = new ActoAdministrativo();
    $this->expediente_id = null;
    $this->permisoFirmaOtroAutorizado = 0;
    //***************************************************************************************************
    $this->intesadosIds = array();
    $this->intesadosNames = array();
    //***************************************************************************************************
    $this->usuarios_firman = array();
    $this->ufirman_names = array();
    $this->cargousuarioIdFirma = array();
    //***************************************************************************************************
    $this->gestorUserId = array();
    $this->gestor_name = array();
    $this->cargousuarioIdGestor = array();
    //***************************************************************************************************
    $this->revisorUserId = array();
    $this->revisor_name = array();
    $this->cargousuarioIdRevisor = array();
    //***************************************************************************************************
    $this->copiaInternaId = array();
    $this->copiaInternaName = array();
    $this->cargousuarioIdCopias = array();
    //***************************************************************************************************
    $currentFormCreadoPor = "CREAR_ACTO_ADMINISTRATIVO_OTRO_USUARIO_AUTORIZADO";
    if($this->getUser()->checkPerm($currentFormCreadoPor, $usuariologuiado)){       
      $this->permisoFirmaOtroAutorizado = 1;
    }
    //***************************************************************************************************
    $currentFormConMembrete = "CREAR_ACTO_ADMINISTRATIVO_MEMBRETE";
    $this->usar_membrete = false;
    $this->permisoCrearConMembrete = 0;
    if($this->getUser()->checkPerm($currentFormConMembrete, $usuariologuiado)){
       $this->permisoCrearConMembrete = 1;
       $this->usar_membrete = $this->usuario->getRegional()->getEntidad()->getUsarMembrete();
    }
    //***************************************************************************************************
    $this->es_otra_dependencia = 0;
    if($this->getUser()->checkPerm("RADICAR_ACTO_ADMINISTRATIVO_OTRA_DEPENDENCIA", $usuariologuiado)){
      $this->es_otra_dependencia=1;
    }
  }

  /**
	 * acto_administrativoActions::executeDownloadBatchDocs()
	 * permite inicar el procedimiento para descargar los archivos de documentos del expediente seleccionado por lotes 
	 * @return
	*/
	public function executeDownloadBatchDocs()
	{
		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
		//********************************************************************************************************
		$currentForm = "ACTO_ADMINISTRATIVO_DESCARGAR_DOCUMENTOS";
		if(!$this->tienePrivilegio($currentForm)){
			$this->redirect(sfConfig::get('base_simad').'/no_autorizado_cerrar.html');
		}		
		//********************************************************************************************************
		$this->list_registros = ActoAdministrativoPeer::getAllActosAdministrativosByMarca($usuariologuiado);
		//********************************************************************************************************
		$this->setTemplate('downloadBatchDocs');
	}

  /**
	 * acto_administrativoActions::executeAsyncDownloadDocs()
	 * crea el archivo comprimido de todos los documentos electronicos que componen un expedientes 
	 * @return
	*/
	public function executeAsyncDownloadDocs()
	{
		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
		//********************************************************************************************************
    $currentForm = "ACTO_ADMINISTRATIVO_DESCARGAR_DOCUMENTOS";
		if(!$this->tienePrivilegio($currentForm)){
      $response_info['httpStatus'] = 400;
      $response_info['message'] = "Ocurrio un error, no puedes decargar estos documentos";
      $response_info['url_descarga'] = null;
      //******************************************************************************************************
      $this->getResponse()->setContentType('application/json');
      return $this->renderText(json_encode($response_info)); 
		}
		//********************************************************************************************************
		$list_documentos = ActoAdministrativoPeer::getAllActosAdministrativosByMarca($usuariologuiado);
    $dir_raiz = ParametroPeer::retrieveByPk(75)->getValortexto();
    $digit_dir  = ParametroPeer::retrieveByPk(76)->getValortexto();
		//********************************************************************************************************
		$response_info['httpStatus'] = 200;
		$response_info['message'] = "Archivo generado..";
		$base_path = sfConfig::get('base_simad');
		$response_info['url_descarga'] = $base_path;
		//********************************************************************************************************
		$zip = new ZipArchive();
		$zipFileName = md5(ModulesEnable::ActosAdministrativos.time()).".zip";
		$pathzip = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.$zipFileName;
		if(file_exists($pathzip)) { unlink ($pathzip); }
		if ($zip->open($pathzip, ZIPARCHIVE::CREATE) != TRUE) { die ("Could not open archive"); }
		//********************************************************************************************************
    $error_list = null;
		foreach($list_documentos as $documento)
		{
			if(in_array($documento->getEstadoactoadministrativoId(), array(1,2,3,4))){
        continue; 
      }
			//********************************************************************************************************
      $storage_com = $documento->getBasicUrlDigitCom($dir_raiz, $digit_dir);
      $filename = trim($documento->getRadicadoCompuesto()) . ".pdf";
      $filename_digit = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
      $prefix_suberie = trim($documento->getSubserie()->getCodigo());
      //********************************************************************************************************
      $nro_expediente = trim($documento->getRadicadoCompuesto());
			$folder_bzip = sprintf("%s/%s",$prefix_suberie,$nro_expediente);
      //********************************************************************************************************
			if (!file_exists($filename_digit)) {
          if(!empty($documento->getUrlFileWord()) && file_exists($documento->getUrlFileWord())){
              $filename_digit = $documento->generatePdfByFile($documento->getUrlFileWord(),true);
          }else{
              $margins = array('top' => 30,'left' => 20,'buttom' => 25,'rigth' => 18);
              $filename_digit = $documento->generateFileInDisk($margins);
          }
      }
      //********************************************************************************************************
      if(!empty($filename_digit)){ 
        $zip->addFile($filename_digit,$folder_bzip.'/'.mb_convert_encoding($filename,'UTF-8')); 
      }
		}
		//**********************************************************************************************************
		$zip->close();
    //**********************************************************************************************************
		if(file_exists($pathzip)){ 
      $response_info['url_descarga'] = "/tmp/".$zipFileName; 
		}else{
      $response_info['httpStatus'] = 400;
      $response_info['message'] = "Ocurrio un error generando el archivo..";
      $response_info['url_descarga'] = null;
		}
		//********************************************************************************************************
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info)); 
	}

  /**
  * acto_administrativoActions::executeRadicarMasivas()
  * 
  */
  public function executeRadicarMasivas()
  {
    $currentForm="ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
    $this->verificaPrilegio($currentForm);
    $this->process_end = $this->getRequestParameter('process_end') ? $this->getRequestParameter('process_end') : 0;
    $this->sheetData = array();
    $this->cod_msg = $this->getRequestParameter('cod_msg') ? $this->getRequestParameter('cod_msg') : 0;
    $this->msg_error = $this->getRequestParameter('msg_error') ? $this->getRequestParameter('msg_error') : "";
    $this->acto_administrativo = new ActoAdministrativo();
  }

  /**
 * acto_administrativoActionss::executeReadFileExcel()
  * accion para funcionalidad leer datos del archivo de excel
  */
  public function executeReadFileRadMasiva()
  {
    $currentForm="ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
        $this->cod_msg = 1;
        $this->msg_error = "Esta funcionalidad no esta permitida";
    }
    //*******************************************************************************************
    $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
    $directorio = simad_util::createPath($upload_dir);
    //*******************************************************************************************
    if ($this->getRequest()->hasFiles() && $this->getRequest()->getFileName('file'))
    {
      $file_vars = pathinfo($this->getRequest()->getFileName('file'));
      $util_simad = new simad_util();
      $extension_file = $file_vars['extension'];
      //***************************************************************************************
      $info_file = new SplFileInfo($this->getRequest()->getFileName('file'));
      $cleanfilename = $util_simad->clean_name_fileinfo($info_file);//limpiar el nombre del archivo
      $file_name = simad_util::uniquename($upload_dir,sha1($cleanfilename.time()),".".$extension_file);//validar uniquename    		
      $inputFileName = $directorio.DIRECTORY_SEPARATOR.$file_name;            
      $this->getRequest()->moveFile('file', $inputFileName);
      //***************************************************************************************
      $listVars = ComEnviadaPeer::readFileComCombined($inputFileName);            
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
      $this->msg_error = "Debe seleccionar el archivo que contiene los registros para radicar";
    }
  }

  /**
 * com_enviadaActions::executeVerifyComBatchData()
  * accion para verificar la informacion enviada en el excel
  * @return
  */
  public function executeVerifyComBatchData()
  {
    $currentForm="ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
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
    $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
    $fileuploadtmp = trim($this->getRequestParameter('fileuploadtmp')) ?: 0;
    $sheetData = trim($this->getRequestParameter('sheetData')) ?: 0;
    $simad_util = new simad_util();
    $list_errors = array();
    //*******************************************************************************************
    $data = $simad_util->getArrayUnSerialize($sheetData);
    $idLote = md5(uniqid().rand(9999,100000).date('YmdGisu'));$list_nrow = array();
    foreach($data as $row)
    {
        $dataRow = array();
        $dataRow['COMLOTE_ID'] = $idLote;
        $dataRow['MODULO_ID'] = ModulesEnable::ActosAdministrativos;
        $dataRow['USUARIO_ID'] = $usuariologuiado;
        $dataRow['PUNTO_RADICACION'] = trim($row[1]) ? trim($row[1]) : null;
        $dataRow['COD_DEPENDENCIA'] = trim($row[2]) ? trim($row[2]) : null;
        $dataRow['NOMBRE_DEPENDENCIA'] = trim($row[3]) ? trim($row[3]) : null;
        $dataRow['ASUNTO_COM'] = trim($row[4]) ? trim($row[4]) : null;
        $dataRow['PRIORIDAD_COM'] = trim($row[5]) ? trim($row[5]) : null;
        $dataRow['NUM_FOLIOS'] = trim($row[6]) ? trim($row[6]) : null;
        $dataRow['TIPO_DOCUMENTO'] = trim($row[7]) ? trim($row[7]) : null;
        $dataRow['OBSERVACIONES_COM'] = trim($row[8]) ? trim($row[8]) : null;
        $dataRow['NOMBRE_ARCHIVO'] = trim($row[9]) ? trim($row[9]) : null;
        //$dataRow['FIRMA_DIGITAL'] = $row[10] == "SI" ? 1 : 0;
        $dataRow['TIPO_NOTIFICACION'] = trim($row[10]) ? trim($row[10]) : null;
        $dataRow['SUBSERIE_CODIGO'] = trim($row[11]) ? trim($row[11]) : null;
        $dataRow['NUMERO_EXPEDIENTE'] = trim($row[12]) ? trim($row[12]) : null;
        $dataRow['FECHA_RESOLUCION'] = trim($row[13]) ? trim($row[13]) : null;
        $dataRow['ID_SUBORIGEN'] = trim($row[14]) ? trim($row[14]) : null;
        $dataRow['MARCO_NORMATIVO'] = trim($row[15]) ? trim($row[15]) : null;

        //$dataRow['NUMERO_RESOLUCION'] = trim($row[15]) ? trim($row[15]) : null;
        $dataRow['COD_TIPO_DOC'] = trim($row[16]) ? trim($row[16]) : null;
        $dataRow['PNOMBRE_INTERESADO'] = trim($row[17]) ? trim($row[17]) : null;
        $dataRow['SNOMBRE_INTERESADO'] = trim($row[18]) ? trim($row[18]) : null;
        $dataRow['PAPELLIDO_INTERESADO'] = trim($row[19]) ? trim($row[19]) : null;
        $dataRow['SAPELLIDO_INTERESADO'] = trim($row[20]) ? trim($row[20]) : null;
        $dataRow['TIPODOC_INTERESADO'] = trim($row[21]) ? trim($row[21]) : null;
        $dataRow['NUID_INTERESADO'] = trim($row[22]) ? trim($row[22]) : null;
        $dataRow['CIUDAD_INTERESADO'] = trim($row[23]) ? trim($row[23]) : null;
        $dataRow['EMAIL_INTERESADO'] = trim($row[24]) ? trim($row[24]) : null;
        $dataRow['NUIDS_FIRMAS'] = trim($row[25]) ? trim($row[25]) : null;
        $dataRow['NUID_DESTINARIO'] = trim($row[26]) ? trim($row[26]) : null;        
        $dataRow['ESTADO_MIGRACION'] = "PENDIENTE VALIDAR";
        $list_nrow[] = ComMigmasivoPeer::addNewRow($dataRow);
    }
    //*******************************************************************************************
    $elist_msg = ComMigmasivoPeer::getIsValidByIdBatch($idLote,ModulesEnable::ActosAdministrativos);
    //*******************************************************************************************
    $this->getResponse()->setContentType('application/json');
    $response_info = array('status' => (count($elist_msg) ? 400 : 200), 'message' => $elist_msg, 'comIdLote' => $idLote);
    return $this->renderText(json_encode($response_info));
  }

  /**
  * acto_administrativoActions::executeUpdateComBatch()
  * Radicar actos administrativos masivas con archivo plano
  * @return
  */
  public function executeUpdateComBatch()
  {
    $currentForm="ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
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
    $parameters['firma_digital'] = trim($this->getRequestParameter('firma_digital')) ? trim($this->getRequestParameter('firma_digital')) : null;
    //*******************************************************************************************
    $max_time = ini_get("max_execution_time");
    ini_set('max_execution_time', 1800);
    $response_info = ComMigmasivoPeer::addNewComByComLote($parameters,ModulesEnable::ActosAdministrativos);
    ini_set('max_execution_time', $max_time);
    //*******************************************************************************************
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info));
  }

  /**
   * acto_administrativoActions::executeExportarBatchList()
   * accion para exportar los resultados de la radicacion masiva filtrado por un comIdLote
   * @return
   */
  public function executeExportarBatchList()
  {
    $this->setLayout(false);
    $currentForm = "ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
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
  * acto_administrativoActions::executeLoadListBatchMig()
  * accion para listar el resultado de los documentos radicados
  * @return
  */
  public function executeLoadListBatchMig()
  {
    $currentForm="ACTO_ADMINISTRATIVO_RADICACION_MASIVA";
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
  * Executes FileTemplate action
  *
  */
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
    $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : null;
    //*********************************************************************************
    if(empty($plantillascom_id)){
        $response_data = array('status' => 400, 'message' => 'Acceso denegado, los parametros no son validos');
        $array = json_encode($response_data);
        $this->getResponse()->setContentType('application/json');
        return $this->renderText($array);
    }
    //*********************************************************************************
    $plantillas_com = PlantillasComPeer::retrieveByPK($plantillascom_id);
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    //*********************************************************************************
    if(empty($plantillas_com) || empty($acto_administrativo)){
        $response_data = array('status' => 400, 'message' => 'Ocurrio un error, los parametros enviados no son validos');
        $array = json_encode($response_data);
        $this->getResponse()->setContentType('application/json');
        return $this->renderText($array);
    }
    //*********************************************************************************
    $params['comobject_id'] = $acto_administrativo->getPrimaryKey();
    $params['periodo_id'] = $acto_administrativo->getPeriodoId();
    $params['use_membrete'] = $acto_administrativo->getUseMembrete();
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
    $actoadministrativo_id = $this->getRequestParameter('actoadministrativo_id') ? $this->getRequestParameter('actoadministrativo_id') : null;
    //*********************************************************************************
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
    $format_file = pathinfo($acto_administrativo->getUrlFileWord(),PATHINFO_EXTENSION);
    //*********************************************************************************
    if(file_exists($acto_administrativo->getUrlFileWord()) && $format_file == 'docx'){
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . basename($acto_administrativo->getUrlFileWord()) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($acto_administrativo->getUrlFileWord()));
        
        // Limpiar buffer de salida
        ob_clean();
        flush();
        
        // Leer y enviar el archivo
        readfile($acto_administrativo->getUrlFileWord());
        exit;
    }else{
        $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
        exit;
    }
  }

  /**
  * Executes UploadTemplate action
  *
  */
  public function executeUploadTemplate()
  {
    $actoadministrativo_id = !empty($this->getRequestParameter('actoadministrativo_id')) ? $this->getRequestParameter('actoadministrativo_id') : null;
    //****************************************************************************************
    if(empty($actoadministrativo_id)){
        $this->acto_administrativo = null;
        $this->file_format = '.pdf,.PDF,application/pdf';
        $this->btntext = 'Cargar Archivo';
        $this->isGenWord = false;
    }else{
        $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($this->getRequestParameter('actoadministrativo_id'));
        if($this->acto_administrativo->getIsCreateWord() == ResponseDocTypeCom::Word){
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

  /**
  * Executes UploadTemplateEdit action
  *
  */
  public function executeUploadTemplateEdit()
  {
    $currentForm="ACTO_ADMINISTRATIVO_REMPLAZAR_FILE_DIGIT";
    $this->verificaPrilegioCerrar($currentForm);
    //*********************************************************************************************   
    $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPk($this->getRequestParameter('actoadministrativo_id'));
    //*********************************************************************************************   
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
        if($is_error){ $this->forward('acto_administrativo', 'uploadTemplate'); }
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
            $this->forward('acto_administrativo', 'uploadTemplate');
        }
        //*****************************************************************************************
        if(!file_exists($fullpath)){
            $this->getRequest()->setError("attachs",'Ocurrio un error al cargar el archivo');
            $this->forward('acto_administrativo', 'uploadTemplate');
        }
        //*****************************************************************************************
        $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : null;
        $this->acto_administrativo = null;
        if(!empty($actoadministrativo_id))
        {
          $currentForm="ACTO_ADMINISTRATIVO_REMPLAZAR_FILE_DIGIT";
          $this->verificaPrilegioCerrar($currentForm);
          $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
          //************************************************************************************
          if(empty($acto_administrativo->getUrlFileWord()) && !file_exists($acto_administrativo->getUrlFileWord())){
              $dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(75)->getValortexto().'uploads');
              $filedir_upload = simad_util::createPath($dir_raiz.DIRECTORY_SEPARATOR.date("Ymd")).DIRECTORY_SEPARATOR.$replyfile;
          }else{
              $filedir_upload = $acto_administrativo->getUrlFileWord();
          }
          //*************************************************************************************
          if(file_exists($fullpath)) 
          {
            if($acto_administrativo->getIsCreateWord() == ResponseDocTypeCom::Word){
              $acto_administrativo->setIsCreateWord(ResponseDocTypeCom::Word);
              //*****************************************************************************
              $docxProps = simad_util::readCustomPropsFromDocx($fullpath);
              //*****************************************************************************
              $uuid = $acto_administrativo->getPrimaryKey().'#'.$acto_administrativo->getPlantillasCom()->getPrimaryKey();
              $plantillaId = $acto_administrativo->getPlantillascomId();
              $modulo_id = $acto_administrativo->getPlantillasCom()->getModuloId();
              $periodoAt = $acto_administrativo->getPeriodoId();
              $current_hmac = $acto_administrativo->getPlantillasCom()->makeHmac($uuid, $plantillaId, $periodoAt, $modulo_id);
              //*****************************************************************************
              if (!hash_equals($current_hmac, $docxProps['xd_hmac'])) {
                  $this->getRequest()->setError("attachs", 'El documento de word no es el asociado a este acto administrativo');
                  $this->forward('acto_administrativo', 'uploadTemplate');
              }
            }else{
                $acto_administrativo->setIsCreateWord(ResponseDocTypeCom::Pdf);
            }
            //*********************************************************************************
            if(rename($fullpath,$filedir_upload))
            {
              $acto_administrativo->setUrlFileWord($filedir_upload);
              $acto_administrativo->setContenido(null);
              $acto_administrativo->setUseMembrete(0);
              //******************************************************************************
              if($acto_administrativo->getFirmadoDigital() == 1){
                  $acto_administrativo->setFirmadoDigital(0);
              }
              //******************************************************************************
              $acto_administrativo->save();
              //******************************************************************************
              $digitDocFile = $acto_administrativo->getPathImageDigitByCom();
              //******************************************************************************
              if(!empty($digitDocFile)){
                  unlink($digitDocFile);
              }
              $this->acto_administrativo = $acto_administrativo;
            }
          }
        }
        //*****************************************************************************************
        $this->fileName = basename($fullpath);
    }
  }

  /**
  * Executes FileWord action
  *
  */
  public function executeFileWord()
  {
    $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : -1;
    $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPK($actoadministrativo_id);
  }

  /**
  * Executes edit action
  *
  */
  public function executeEdit()
  {
    $this->verificaPrilegio("acto_administrativo/edit");
    //***************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : -1;
    //***************************************************************************************************
    $acto_administrativo = ActoAdministrativoPeer::retrieveByPK($actoadministrativo_id);
    $this->expediente_id = $acto_administrativo->getExpedienteId();
    $this->permisoFirmaOtroAutorizado = 0;
    $ulist_actoadmin = $acto_administrativo->getUsuariosListCom();
    //***************************************************************************************************
    $this->intesadosIds = array();
    $this->intesadosNames = array();
    //***************************************************************************************************
    $this->usuarios_firman = $ulist_actoadmin['firmas_pkusers'];
    $this->ufirman_names = $ulist_actoadmin['firmas_names'];
    $this->cargousuarioIdFirma = $ulist_actoadmin['firmas_ucargo'];
    //***************************************************************************************************
    $this->gestorUserId = $ulist_actoadmin['gestores_pkusers'];
    $this->gestor_name = $ulist_actoadmin['gestores_names'];
    $this->cargousuarioIdGestor = $ulist_actoadmin['gestores_ucargo'];
    //***************************************************************************************************
    $this->revisorUserId = $ulist_actoadmin['revisores_pkusers'];
    $this->revisor_name = $ulist_actoadmin['revisores_names'];
    $this->cargousuarioIdRevisor = $ulist_actoadmin['revisores_ucargo'];
    //***************************************************************************************************
    $this->destinatariosIds = $ulist_actoadmin['destino_pkusers'];
    $this->udestinarios_names = $ulist_actoadmin['destino_names'];
    $this->cargousuarioIdDestinos = $ulist_actoadmin['destino_ucargo'];
    //***************************************************************************************************
    $this->copiaInternaId = $ulist_actoadmin['ucopias_pkusers'];
    $this->copiaInternaName = $ulist_actoadmin['ucopias_names'];
    $this->cargousuarioIdCopias = $ulist_actoadmin['ucopias_ucargo'];
    //***************************************************************************************************
    $list_inteIds = array();$listint_names = array();
    foreach (ActoAdministrativoPeer::getListIntersadosByActoId($acto_administrativo->getPrimaryKey()) as $interesado) {
        $list_inteIds[] = $interesado->getInteresados()->getPrimaryKey();
        $listint_names[] = trim($interesado->getInteresados()->getNumeroIdentificacion())." - ".trim($interesado->getInteresados()->getNombre());
    }
    //***************************************************************************************************
    $currentFormCreadoPor = "CREAR_ACTO_ADMINISTRATIVO_OTRO_USUARIO_AUTORIZADO";
    if($this->getUser()->checkPerm($currentFormCreadoPor, $usuariologuiado)){       
      $this->permisoFirmaOtroAutorizado = 1;
    }
    //***************************************************************************************************
    $this->es_otra_dependencia = 0;
    if($this->getUser()->checkPerm("RADICAR_ACTO_ADMINISTRATIVO_OTRA_DEPENDENCIA", $usuariologuiado)){
      $this->es_otra_dependencia=1;
    }
    //***************************************************************************************************
    //$this->permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,implode(",",$ulist_actoadmin['firmas_pkusers']),17);
    $this->permisoRadicarFirmaElectronica = 0;
    //***************************************************************************************************
    $object_asignado = $ulist_actoadmin['object_asignado'];    
    //***************************************************************************************************
    $this->expediente_id = null;
    $this->nombre_expediente = null;
    $this->list_tdocs = array();
    $this->tipodocumental_id = null;    
    if(!empty($acto_administrativo->getExpedienteId())){
      $unidad_documental = UnidadDocumentalPeer::retrieveByPK($acto_administrativo->getExpedienteId());
      $this->expediente_id = $unidad_documental != null ? $unidad_documental->getPrimaryKey() : null;
      $this->nombre_expediente = $unidad_documental != null ? $unidad_documental->getCodigoTitulo(): null;
      $this->list_tdocs = $unidad_documental != null ? TipoDocumentalPeer::getTipoDocListBySubserie($unidad_documental->getSubserieId()) : null;
      $this->tipodocumental_id = $acto_administrativo->getTipoDocumentalCod();
    }
    //***************************************************************************************************
    $aprob_urlist = ActoAdministrativoPeer::getListUncheckApro($acto_administrativo->getPrimaryKey());
    if(count($aprob_urlist) <= 1 && in_array($object_asignado->getUsuarioId(),$aprob_urlist)){
      $this->permisoRadicarFirmaElectronica = 1;
    }
    //***************************************************************************************************
    $this->aprobacion_ulist = $aprob_urlist;
    $this->intesadosIds = $list_inteIds;
    $this->intesadosNames = $listint_names;
    $this->usuario = $usuario;
    $this->acto_administrativo = $acto_administrativo;
    $this->users_asignado = $ulist_actoadmin['usuarios_asigandos'];
    $this->tipoprocesocom_id = $object_asignado != null ? $object_asignado->getTipoprocesocomId() : 0;
    //***************************************************************************************************
    $this->ldocument_version = DocsControlCambioPeer::getAllVersionDocs($acto_administrativo->getPrimaryKey(),ModulesEnable::ActosAdministrativos);
    //***************************************************************************************************
    $this->forward404Unless($this->acto_administrativo);
  }

  /**
  * Executes edit action
  *
  */
  public function executeCompareVersion()
  {
    $this->verificaPrilegio("acto_administrativo/edit");
    //***************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $strencryp = trim($this->getRequestParameter('docscontrolcambio_id')) ? trim($this->getRequestParameter('docscontrolcambio_id')) : null;
    //***************************************************************************************************
    if(empty($strencryp)){
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //***************************************************************************************************
    $docscontrolcambio_id = SED::decryption($strencryp);
    $last_controldoc = DocsControlCambioPeer::retrieveByPK($docscontrolcambio_id);
    //***************************************************************************************************
    if(empty($last_controldoc)){
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //***************************************************************************************************
    $this->acto_administrativo = ActoAdministrativoPeer::retrieveByPK($last_controldoc->getConsecutivoId());
    //$current_controldoc = DocsControlCambioPeer::getCurrentVersionDoc($acto_administrativo->getPrimaryKey(),$last_controldoc->getModuloId());
    //***************************************************************************************************
    $this->diff_data = $last_controldoc->compareVersions('Inline');
    //***************************************************************************************************
    $this->forward404Unless($this->acto_administrativo);
  }

  /**
  * Executes edit action
  *
  */
  public function executeTransferVersion()
  {
    $this->verificaPrilegio("acto_administrativo/edit");
    //***************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    $strencryp = trim($this->getRequestParameter('docscontrolcambio_id')) ? trim($this->getRequestParameter('docscontrolcambio_id')) : null;
    //***************************************************************************************************
    if(empty($strencryp)){
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //***************************************************************************************************
    $docscontrolcambio_id = SED::decryption($strencryp);
    $select_controldoc = DocsControlCambioPeer::retrieveByPK($docscontrolcambio_id);
    //***************************************************************************************************
    if(empty($select_controldoc)){
      $response_info['status'] = 400;
      $response_info['message'] = "Ocurrio un error, los parametros enviados no son validos";
      $this->getResponse()->setContentType('application/json');
      return $this->renderText(json_encode($response_info));
    }
    //***************************************************************************************************
    $response_info['status'] = 200;
    $response_info['message'] = "Versión revertida satisfactoriamente, recurde que debe guardar para confirmar los cambios, esta acción genera una versión";
    $response_info['htmlData'] = $select_controldoc->getContenidoText();
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info));
  }

  public function executeRadicar()
  {
      $this->verificaPrilegio("ACTO_ADMINISTRATIVO_RADICAR");
      $this->acto_administrativo = $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($this->getRequestParameter('actoadministrativo_id'));
      $actoadmin_anterior = $acto_administrativo->copy();
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
      //***************************************************************************************************************
      $usuarios_com = $acto_administrativo->getUsuariosListComIds();
      $permisoRadicarFirmaElectronica = AutorizacionFirmaPeer::validateFirmaElectronica($usuariologuiado,$usuarios_com['firmas'],4);
      if($permisoRadicarFirmaElectronica == 0){
          return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/edit?actoadministrativo_id='.$acto_administrativo->getPrimaryKey());
      }      
      //***************************************************************************************************************
      $firmante_id = $acto_administrativo->getFirstUsurioFirma($acto_administrativo->getPrimaryKey());
      if(!$firmante_id){ $firmante_id = $this->getUser()->getAttribute('usuario_id','', 'subscriber'); }
      //***************************************************************************************************************
      $regional_id = $acto_administrativo->getRegionalId();
      //$regional_object = RegionalPeer::retrieveByPK($regional_id);
      //***************************************************************************************************************
      $numero_resolucion = $acto_administrativo->getRadicadoFormat();
      //***************************************************************************************************************
      if($acto_administrativo->getEstadoactoadministrativoId() == 1){
        $acto_administrativo->setPeriodoId(date("Y"));
        $acto_administrativo->setNumeroResolucion($numero_resolucion);
      }else{
        return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/show?actoadministrativo_id='.$acto_administrativo->getPrimaryKey());
      }
      //***************************************************************************************************************
      $estadoactoadmin_id = 6;
      $acto_administrativo->setEstadoactoadministrativoId($estadoactoadmin_id);
      $acto_administrativo->setFechaCreacion(date("Y-m-d G:i:s"));
      $acto_administrativo->setFechaAprobacion(date("Y-m-d G:i:s"));
      $acto_administrativo->setFechaResolucion(date("Y-m-d"));
      $acto_administrativo->setRegionalId($regional_id);
      $acto_administrativo->save();
      //***************************************************************************************************************
      ActoAdministrativoPeer::updateEstadosObj($acto_administrativo->getPrimaryKey());
      ActoAdministrativoPeer::updateAproFirmaObjAll($acto_administrativo->getPrimaryKey());
      ActoAdministrativoPeer::updateAproObjAllProcess($acto_administrativo->getPrimaryKey(),$usuariologuiado);
      ActoAdministrativoPeer::AsignarUserDestinoByPk($acto_administrativo->getPrimaryKey());
      //***************************************************************************************************************
      $info_uradica['pkcom_id'] = $acto_administrativo->getPrimaryKey();
      $info_uradica['esta_asignada'] = 0;
      $info_uradica['estadocom_id'] = 6;
      $info_uradica['usuario_id'] = $usuariologuiado;
      $info_uradica['rol_id'] = 5;
      $info_uradica['cusuario_id'] = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariologuiado);
      $info_uradica['tipoprocesocom_id'] = 1;
      $info_uradica['fecha_aprobacion'] = date("Y-m-d G:i:s");
      $info_uradica['fecha_lectura'] = date("Y-m-d G:i:s");
      $info_uradica['esta_aprobado'] = 1;
      ActoAdministrativoPeer::addUserByActo($info_uradica);
      //***************************************************************************************************************
      if(!empty($acto_administrativo->getExpedienteId()) && !empty($acto_administrativo->getTipoDocumentalCod())){
          $origentransfer_id = 8;
          $acto_administrativo->addNewTransferenciaAndContenido($origentransfer_id,$firmante_id);
      }
      //***************************************************************************************************************
      AuditLogPeer::guardarAuditoriaLite("ActoAdministrativo",$actoadmin_anterior,$acto_administrativo,17,$acto_administrativo->getNumeroResolucion(),$usuariologuiado);
      //***************************************************************************************************************
      //$acto_administrativo->initUserNotifications($list_com);
      //***************************************************************************************************************
      return $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/show?actoadministrativo_id='.$acto_administrativo->getPrimaryKey());
  }

  /**
  * Executes viewImageDigit action
  *
  */
  public function executeViewImageDigit()
  {
      $actoadministrativo_id = base64_decode($this->getRequestParameter('q_vars'));
      $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
      //********************************************************************************
      $vtoken = $this->getRequestParameter('vtoken') ? trim($this->getRequestParameter('vtoken')) : null;
      $current_token = md5($acto_administrativo->getNumeroResolucion().$usuariologuiado.$acto_administrativo->getFechaCreacion());
      if($vtoken == $current_token){
          $response_process = $acto_administrativo->getUriImageDigitById();
      }else{
          $response_process['message'] = 'No tine acceso a este recurso, actualice la pagina e intente de nuevo o comuniquese con el administrador del sistema';
      }
      //***********************************************************************************************
      $this->getResponse()->setContentType('application/json');      
      return $this->renderText(json_encode($response_process));
  }

  public function executeExcel()
  {
    $this->verificaPrilegio("acto_administrativo/excel");
    $this->setLayout(false);
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->usuario_genera = UsuarioPeer::retrieveByPK($usuariologuiado);
    //*************************************************************************************************
    $c = new Criteria();
    $c->setDistinct();
    $this->parametros="a=1";
    $c = $this->getCriteriaBasic($c);
    //*************************************************************************************************
    $c->addJoin(ActoAdministrativoPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
    $c->addJoin(ActoAdministrativoPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
    $c->clearSelectColumns();
    //*************************************************************************************************
    $c->addAsColumn('NOMBRE_DEPENDENCIA', DependenciaPeer::NOMBRE);
    $c->addAsColumn('CODIGO_DEPENDENCIA', DependenciaPeer::CODIGO);
    $c->addSelectColumn(ActoAdministrativoPeer::NUMERO_RESOLUCION);
    $c->addSelectColumn(ActoAdministrativoPeer::ASUNTO);
    $c->addSelectColumn(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID);  
    $c->addSelectColumn(ActoAdministrativoPeer::FECHA_CREACION);
    $c->addAsColumn('CODIGO_SUBSERIE', SubseriePeer::CODIGO);
    $c->addAsColumn('NOMBRE_SUBSERIE', SubseriePeer::DESCRIPCION);
    $c->addSelectColumn(ActoAdministrativoPeer::FECHA_RESOLUCION);
    //********************************************************************************************
    $sql_uproyecta = "(SELECT TOP 1 CONCAT(UPROYECTA.NOMBRE, ' ', UPROYECTA.APELLIDO)";
    $sql_uproyecta .= " FROM USUARIO UPROYECTA";
    $sql_uproyecta .= " JOIN ACTOADMINISTRATIVO_USUARIO COMUPROYECTA ON UPROYECTA.USUARIO_ID = COMUPROYECTA.USUARIO_ID";
    $sql_uproyecta .= " WHERE COMUPROYECTA.ROLUSUARIOACTOADMINISTVO_ID = 1";
    $sql_uproyecta .= " AND COMUPROYECTA.ACTOADMINISTRATIVO_ID = " . ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID . ")";
    $c->addAsColumn('USUARIO_PROYECTA', $sql_uproyecta);
    //********************************************************************************************
    $sql_destinatario = "(SELECT TOP 1 CONCAT(UDEST01.NOMBRE, ' ', UDEST01.APELLIDO)";
    $sql_destinatario .= " FROM USUARIO UDEST01";
    $sql_destinatario .= " JOIN ACTOADMINISTRATIVO_USUARIO UDEST02 ON UDEST01.USUARIO_ID = UDEST02.USUARIO_ID";
    $sql_destinatario .= " WHERE UDEST02.ROLUSUARIOACTOADMINISTVO_ID = 6";
    $sql_destinatario .= " AND UDEST02.ACTOADMINISTRATIVO_ID = " . ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID . ")";
    $c->addAsColumn('DESTINATARIO', $sql_destinatario);
    //********************************************************************************************
    $sql_ufirma = "(SELECT TOP 1 CONCAT(UORIGEN_01.NOMBRE, ' ', UORIGEN_01.APELLIDO)";
    $sql_ufirma .= " FROM USUARIO UORIGEN_01";
    $sql_ufirma .= " JOIN ACTOADMINISTRATIVO_USUARIO UORIGEN_02 ON UORIGEN_01.USUARIO_ID = UORIGEN_02.USUARIO_ID";
    $sql_ufirma .= " WHERE UORIGEN_02.ROLUSUARIOACTOADMINISTVO_ID = 2";
    $sql_ufirma .= " AND UORIGEN_02.ACTOADMINISTRATIVO_ID = " . ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID . ")";
    $c->addAsColumn('REMITENTE', $sql_ufirma);
    //*************************************************************************************************
    $this->list_registros = ActoAdministrativoPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
  * Executes loadPlantilla action
  *
  */
  public function executeLoadPlantilla()
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
    //***************************************************************************************************
    $plantillascom_id = trim($this->getRequestParameter('plantillascom_id')) ? trim($this->getRequestParameter('plantillascom_id')) : null;
    $plantillas_com = PlantillasComPeer::retrieveByPk($plantillascom_id);
    $contents = "";
    //***************************************************************************************************
    if($plantillas_com == null){
      return $this->renderText($contents);
    }
    //***************************************************************************************************
    if($plantillas_com->getPrimaryKey()){
      $contents = trim($plantillas_com->getContents());
    }
    //***************************************************************************************************
    return $this->renderText($contents);
  }

  /**
  * Executes update action
  *
  * @param sfRequest $request A request object
  */
  public function executeList()
  { 
    $this->verificaPrilegio("acto_administrativo/list");
    //*************************************************************************************************
    $this->directorio_raiz = ParametroPeer::retrieveByPk(73)->getValortexto();
    $this->directorio_alias  = ParametroPeer::retrieveByPk(75)->getValortexto();
    $this->format_digit_img = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
    $this->directorio_adj  = ParametroPeer::retrieveByPk(74)->getValortexto();
    //*************************************************************************************************
    $this->parametros = "&a=1";
    $this->papelera = false;
    $this->isBorradorCom = false;
    $this->marcarEntrega = 0;
    //*************************************************************************************************
    $c = new Criteria();
    $c->setDistinct();
    $c = $this->getCriteriaBasic($c);
    //*************************************************************************************************
    $pager = new sfPropelPager('ActoAdministrativo',10);
    $pager->setCriteria($c);
    $pager->setPage($this->getRequestParameter('page',1));
    $pager->init();
    //***********************************************************************************************
    $this->pager = $pager;
    $this->controlPaginacion = 1;
    $this->anular="Anular";
    //***********************************************************************************************
    $this->mensajeListaVacia = ConsultaPermisoHelper::MSG_SIN_REGISTROS;
    if ($pager->getNbResults() == 0 && trim($this->getRequestParameter('numero_resolucion'))) {
      $countSinPermiso = ActoAdministrativoPeer::doCount((new Criteria())->add(ActoAdministrativoPeer::NUMERO_RESOLUCION, '%'.trim($this->getRequestParameter('numero_resolucion')).'%', Criteria::LIKE));
      $this->mensajeListaVacia = ConsultaPermisoHelper::mensajeListaVacia($countSinPermiso);
    }
  }

  /**
  * Executes update action
  *
  */
  public function executeUpdate()
  {
    try{    
      if(!$this->tienePrivilegio("acto_administrativo/update")){
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => 405, 'message' => 'Ocurrio un error, no es posible procesar la solicitud, comuniquese con el administrador de la aplicación');
        return $this->renderText(json_encode($response_info));
      }
      //***************************************************************************************************
      $actoadministrativo_id = trim($this->getRequestParameter('actoadministrativo_id')) ? trim($this->getRequestParameter('actoadministrativo_id')) : 0;
      $prioridadcom_id = trim($this->getRequestParameter('prioridadcom_id')) ? trim($this->getRequestParameter('prioridadcom_id')) : 1;
      $plantillascom_id = trim($this->getRequestParameter('plantillascom_id')) ? trim($this->getRequestParameter('plantillascom_id')) : null;
      //*********************************************************************************************************
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id','', 'subscriber');
      $perm_cambiar_radicador = 0;
      $estadoactoadm_id = 1;//estado 1 es borrador
      $isNewActoAdm = false;
      $periodo_id = date("Y");
      //*********************************************************************************************************
      if (empty($actoadministrativo_id))
      {
          $acto_administrativo = new ActoAdministrativo();
          $acto_administrativo->setNumeroResolucion(null);
          $acto_administrativo->setAsunto(null);
          $isNewActoAdm = true;
      }else{
          $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
          //*****************************************************************************************************
          if($acto_administrativo->getEstadoactoadministrativoId() != $estadoactoadm_id){
            $this->redirect($this->getRequest()->getScriptName().'/acto_administrativo/show?actoadministrativo_id='.$acto_administrativo->getPrimaryKey());
          }
          //*****************************************************************************************************
          $com_edit_all = $this->getUser()->checkPerm("ACTO_ADMINISTRATIVO_EDIT_TODAS", $usuariologuiado);
          //*****************************************************************************************************
          $ucom_asignada = ActoAdministrativoPeer::getIsUserAsignado($acto_administrativo->getPrimaryKey(),$usuariologuiado);
          //*****************************************************************************************************
          if(!$ucom_asignada && !$com_edit_all){
            $this->getResponse()->setContentType('application/json');
            $response_info = array('status' => 405, 'message' => 'Ocurrio un error, no puedes realizar cambios en este acto administrativo');
            return $this->renderText(json_encode($response_info));
          }
          //*****************************************************************************************************
          $this->forward404Unless($acto_administrativo);
      }
      //*********************************************************************************************************
      $unidaddocumental_id = trim($this->getRequestParameter('unidaddocumental_id')) ? trim($this->getRequestParameter('unidaddocumental_id')) : null;
      if(empty($unidaddocumental_id)){
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => 405, 'message' => 'Ocurrio un error, debe seleccionar un expediente para archivar el acto administrativo');
        return $this->renderText(json_encode($response_info));
      }
      //*********************************************************************************************************
      $object_anterior = $acto_administrativo->copy();
      //*********************************************************************************************************
      $unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
      $subserie_id = $unidad_documental->getSubserieId();
      $dependencia_id = $unidad_documental->getSubserie()->getSerie()->getDependenciaId();
      //*********************************************************************************************************
      $current_user = UsuarioPeer::retrieveByPk($usuariologuiado);
      $membrete_default = $current_user->getRegional()->getEntidad()->getUsarMembrete() ? 1 : 0;
      $tipoprocesocom_id = 1;
      //*********************************************************************************************************
      $ciudad_id = trim($this->getRequestParameter('ciudad_id')) ? trim($this->getRequestParameter('ciudad_id')) : 0;
      if(empty($ciudad_id)){
        $ciudad_id = $current_user->getRegional()->getCiudadId();
      }
      //*********************************************************************************************************
      $regional_id = trim($this->getRequestParameter('regional_id')) ? trim($this->getRequestParameter('regional_id')) : 0;
      if(empty($regional_id)){
        $regional_id = $current_user->getRegionalId();
      }
      //********************************************************************************************************* 
      $regional_selected = RegionalPeer::retrieveByPk($regional_id);
      
      //*************************************************************************************************
      if($this->getUser()->checkPerm("ACTO_ADMINISTRATIVO_CON_MEMBRETE", $usuariologuiado)){
          $membrete_default = $this->getRequestParameter('use_membrete') ? $this->getRequestParameter('use_membrete') : 0;
      }else{
          $membrete_default = $regional_selected->getEntidad()->getUsarMembrete() ? 1 : 0;
      }
      //*********************************************************************************************************
      $list_interesado = preg_split("/[,]+/", trim($this->getRequestParameter('idUserInteresados')), -1, PREG_SPLIT_NO_EMPTY);
      $list_destinatario = preg_split("/[,]+/", trim($this->getRequestParameter('destinatariosIds')), -1, PREG_SPLIT_NO_EMPTY);
      //*********************************************************************************************************
      $list_users = array();
      $list_users['str_firmausers'] = trim($this->getRequestParameter('firmanteId')) ? trim($this->getRequestParameter('firmanteId')) : $usuariologuiado;
      $list_users['str_ucargosfirma'] = trim($this->getRequestParameter('cargousuarioIdFirma'));
      $list_users['str_revisorusers'] = trim($this->getRequestParameter('revisorUserId'));
      $list_users['str_ucargosrevisor'] = trim($this->getRequestParameter('cargousuarioIdRevisor'));
      $list_users['str_gestorusers'] = trim($this->getRequestParameter('gestorUserId'));
      $list_users['str_ucargosgestor'] = trim($this->getRequestParameter('cargousuarioIdGestor'));
      $list_users['str_destinousers'] = trim($this->getRequestParameter('destinatariosIds'));
      $list_users['str_ucargosdestino'] = trim($this->getRequestParameter('cargousuarioIdDestinos'));
      $list_users['str_copiasusers'] = trim($this->getRequestParameter('copiaInternaId'));
      $list_users['str_ucargoscopias'] = trim($this->getRequestParameter('cargousuarioIdCopias'));
      //*********************************************************************************************************
      if(count($list_interesado) > 0 && count($list_destinatario) > 0){
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => 405, 'message' => 'No se permite enviar interesados y destinatarios, debe seleccionar una sola opcion');
        return $this->renderText(json_encode($response_info));
      }
      //*********************************************************************************************************
      $acto_administrativo->setDependenciaId($dependencia_id);
      $acto_administrativo->setRegionalId($regional_id);
      $acto_administrativo->setPlantillascomId($plantillascom_id);
      $acto_administrativo->setPrioridadcomId($prioridadcom_id);
      $acto_administrativo->setTipoprocesocomId($tipoprocesocom_id);
      $acto_administrativo->setEstadoactoadministrativoId($estadoactoadm_id);
      $acto_administrativo->setPeriodoId($periodo_id);
      $acto_administrativo->setEstadodigitalizacionId(1);
      $acto_administrativo->setAsunto(trim($this->getRequestParameter('asunto')));
      $acto_administrativo->setNumeroResolucion(null);
      $acto_administrativo->setFechaResolucion(null);
      $acto_administrativo->setFechaCreacion(date("Y-m-d G:i:s"));
      //*********************************************************************************************************
      $current_content = trim($this->getRequestParameter('contenido'));
      $acto_administrativo->setContenido($current_content);
      //*********************************************************************************************************
      $acto_administrativo->setFolios(trim($this->getRequestParameter('folios')) ? trim($this->getRequestParameter('folios')) : 1);
      $acto_administrativo->setFechaAnulacion(null);
      $acto_administrativo->setObsAnulacion(null);
      $acto_administrativo->setFechaAprobacion(null);
      $acto_administrativo->setObservaciones(trim($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null);
      $acto_administrativo->setUseMembrete($membrete_default);
      //*********************************************************************************************************
      //firma mecanica
      $acto_administrativo->setFirmaElectronica(UsuarioPeer::countValidateTipoFirma($list_users['str_firmausers']));
      //*********************************************************************************************************
      $acto_administrativo->setFirmadoDigital(0);
      $acto_administrativo->setExpedienteId($this->getRequestParameter('unidaddocumental_id') ? $this->getRequestParameter('unidaddocumental_id') : null);
      $acto_administrativo->setTipoDocumentalCod($this->getRequestParameter('tipodocumental_id') ? $this->getRequestParameter('tipodocumental_id') : null);
      $acto_administrativo->setSubserieId($subserie_id);
      $acto_administrativo->setContenidodocId(null);
      $acto_administrativo->setMarcaVinculacion(0);
      $acto_administrativo->setTipoIntegracion(null);
      $acto_administrativo->setConsIntegracion(null);
      $acto_administrativo->setAnexos(trim($this->getRequestParameter('anexos')) ? trim($this->getRequestParameter('anexos')) : null);
      //*********************************************************************************************************
      $ruta_anexos = trim($this->getRequestParameter('ruta')) ? trim($this->getRequestParameter('ruta')) : null;
      if(!empty($ruta_anexos)){
        if (!$isNewActoAdm && $acto_administrativo->isNew()){
            $ruta = $acto_administrativo->getRutaAdjuntos($ruta_anexos, false);
        }else{
            $ruta = $acto_administrativo->getRutaAdjuntos($ruta_anexos, true, $acto_administrativo->getRuta());
        }
        $acto_administrativo->setRuta($ruta);
      }else{
        $acto_administrativo->setRuta("");
      }
      //*********************************************************************************************************
      $acto_administrativo->save();
      //*********************************************************************************************************
      $is_create_doc = false;
      $replyfile = trim($this->getRequestParameter('replyfile'));
      if(!empty($replyfile))
      {
        $filedir_tmp = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$replyfile;
        $dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto().'uploads');
        $filedir_upload = simad_util::createPath($dir_raiz.DIRECTORY_SEPARATOR.date("Ymd")).DIRECTORY_SEPARATOR.$replyfile;
        //*******************************************************************************************************
        if(file_exists($filedir_tmp)){
          $fileinfo = new SplFileInfo($filedir_tmp);
          //*****************************************************************************************************
          if($fileinfo->getExtension() == "pdf"){
            if(rename($filedir_tmp,$filedir_upload)){
              $acto_administrativo->setUrlFileWord($filedir_upload);
              $acto_administrativo->setIsCreateWord(1);
              $acto_administrativo->setContenido(null);
              $acto_administrativo->setUseMembrete(0);
              $is_create_doc = true;
            }
          }
        }
      }else if($this->getRequestParameter(md5('radComByWord')) && !empty($acto_administrativo->getPlantillascomId())){
        $dir_raiz = simad_util::NormalizePath(ParametroPeer::retrieveByPk(25)->getValortexto() . 'uploads');
        $filedir_upload = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . date("Ymd"));
        //*****************************************************************************************************
        $params['use_membrete'] = $acto_administrativo->getUseMembrete();
        $params['membrete_com'] = $acto_administrativo->getRegional()->getImageMembrete();
        $params['comobject_id'] = $acto_administrativo->getPrimaryKey();
        $params['periodo_id'] = $acto_administrativo->getPeriodoId();
        $response_tpl = $acto_administrativo->getPlantillasCom()->generateWordByPlantilla($filedir_upload,$params);
        //*****************************************************************************************************
        if($response_tpl['isError'] == false){
          $acto_administrativo->setUrlFileWord($response_tpl['path_plantilla']);
          $acto_administrativo->setIsCreateWord(2);
          $acto_administrativo->setContenido(null);
          $acto_administrativo->setUseMembrete($params['use_membrete']);
        }
      }else{
        $acto_administrativo->setUrlFileWord(null);
        $acto_administrativo->setIsCreateWord(0);
      }
      //*********************************************************************************************************
      $acto_administrativo->save();
      //*********************************************************************************************************
      if($is_create_doc === false){
        DocsControlCambioPeer::addVersionDoc($acto_administrativo->getPrimaryKey(),$current_content,ModulesEnable::ActosAdministrativos,$usuariologuiado);
      }
      //*********************************************************************************************************
      AuditLogPeer::guardarAuditoriaLite(ActoAdministrativoPeer::OM_CLASS,$object_anterior,$acto_administrativo,
        ModulesEnable::ActosAdministrativos,$acto_administrativo->getPrimaryKey(),$usuariologuiado);
      //*********************************************************************************************************
      $snext_user = $this->getRequestParameter('optadd') ? false : true;
      $usuario_creador = ActoAdministrativoPeer::getUserActoByRol($acto_administrativo->getPrimaryKey(),1);
      $usuariocreador_id = $usuario_creador != null ? $usuario_creador->getUsuarioId() : $usuariologuiado;
      ActoAdministrativoPeer::initUserByCom($acto_administrativo->getPrimaryKey(),$list_users,$usuariocreador_id,$estadoactoadm_id,$snext_user);
      //*********************************************************************************************************
      if($snext_user && $isNewActoAdm){
        $ucom_current = ActoAdministrativoPeer::setNextUserProceso($acto_administrativo->getPrimaryKey());
      }else{
        $ucom_current = ActoAdministrativoPeer::getCurrentUserAsignado($acto_administrativo->getPrimaryKey());
      }
      //*********************************************************************************************************
      if($ucom_current == null){
          $usuario_creador = ActoAdministrativoPeer::setNextUserProceso($acto_administrativo->getPrimaryKey());
      }
      //*********************************************************************************************************
      if(count($list_interesado)){
        for($index=0; $index < count($list_interesado); $index++) {
            ActoadministraInteresadoPeer::addNewInteresadoByComId($acto_administrativo->getPrimaryKey(),$list_interesado[$index]);
        }
        //*******************************************************************************************************
        if(count($list_interesado)){ 
          ActoadministraInteresadoPeer::deleteByComIdNotExist($acto_administrativo->getPrimaryKey(),$list_interesado); 
        }
      }else{
        ActoadministraInteresadoPeer::deleteByComIdNotExist($acto_administrativo->getPrimaryKey(),$list_interesado);
      }
      //*********************************************************************************************************
        if(!empty($acto_administrativo->getPlantillascomId()) && $acto_administrativo->getPlantillasCom()->getFirmaDesatendida()){
            foreach ($acto_administrativo->getActoadministrativoUsuarios() as $uobject) {
                if($uobject->getRolusuarioactoadministvoId() == 2 && $uobject->getUsuario()->getDependenciaId() == $acto_administrativo->getPlantillasCom()->getDependenciaId()){
                    $acto_administrativo->setFirmaDesatendida(1);
                }else if($uobject->getRolusuarioactoadministvoId() == 2 && $uobject->getUsuario()->getFirmaDesatendida()){
                    $acto_administrativo->setFirmaDesatendida(1);
                }else{
                    $acto_administrativo->setFirmaDesatendida(0);
                }
            }
        }
      //*********************************************************************************************************
      $url_redirect = '';
      $url_redirect = $this->getRequest()->getScriptName().'/acto_administrativo/edit?actoadministrativo_id='.$acto_administrativo->getPrimaryKey();
      $response_info = array('status' => 200, 'message' => 'La información del registro se guardo exitosamente','url_redirect' => $url_redirect);
    } catch (PropelException $th) {
      $messagex = 'Ocurrio un error interno en el servidor, los datos no son correctos, intente de nuevo si el error persiste contacte al administrador';
      $response_info = array('status' => 400, 'message' => $messagex);
    } catch (Exception $th) {
      $messagex = 'Ocurrio un error interno en el servidor,'.$th->getMessage();
      $response_info = array('status' => 400, 'message' => $messagex);
    }
    //***********************************************************************************************************
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info));
  }

  /**
  * Executes dzFileUpload action
  *
  * @param sfRequest $request A request object
  */
  public function executeDzFileUpload()
  {
    $data_array = array();
  	if (!empty($_FILES))
    {
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');        
      $dirRaiz = ParametroPeer::retrieveByPk(73)->getValortexto();
      $dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
      //***************************************************************************************
      $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
      $entidad_text = trim($usuario->getRegional()->getEntidad()->getDirectorioName());
      $cons    = ParametroPeer::getNewConsecutivo();
      //***************************************************************************************
      $entidad_text = empty($entidad_text) ? "" : $entidad_text.DIRECTORY_SEPARATOR;
    	$directorio_entidad = $dirRaiz.$entidad_text;
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

  /**
  * Executes getCriteriaBasic action
  *
  * @param sfRequest $request A request object
  */
  /**
   * Replica, para un registro puntual, la misma jerarquia de permisos que getCriteriaBasic()
   * aplica a la lista por defecto (ACTOS_ADMINISTRATIVOS_LISTAR_TODAS > dueno/asignado), para
   * distinguir "no existen registros" de "existe pero sin permiso" en executeShow().
   */
  private function usuarioTieneAccesoActoAdministrativo(ActoAdministrativo $acto_administrativo, $usuario_id)
  {
    if ($this->getUser()->checkPerm('ACTOS_ADMINISTRATIVOS_LISTAR_TODAS', $usuario_id)) {
      return true;
    }
    $c = new Criteria();
    $c->add(ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID, $acto_administrativo->getPrimaryKey());
    $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID, $usuario_id);

    return ActoadministrativoUsuarioPeer::doCount($c) > 0;
  }

  private function getCriteriaBasic(Criteria $c)
  {
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    //*************************************************************************************************
    $estado_anulado = trim($this->getRequestParameter('estadoactoadministrativo_id')) ? (int)trim($this->getRequestParameter('estadoactoadministrativo_id')) : null;
    $periodo_id = trim($this->getRequestParameter('periodo_id')) ? (int)trim($this->getRequestParameter('periodo_id')) : date("Y");
    //*************************************************************************************************
    $this->parametros="&a=1";
    $this->isBorradorCom = false;
    $stgestion_actoadm = array(1,2,3,4);
    //*************************************************************************************************
    $perm_list_all = $this->getUser()->checkPerm('ACTOS_ADMINISTRATIVOS_LISTAR_TODAS', $usuariologuiado);
    $perm_list_anuladas = $this->getUser()->checkPerm('ACTOS_ADMINISTRATIVOS_LISTAR_ANULADAS', $usuariologuiado);
    //*************************************************************************************************
    if(trim($this->getRequestParameter('porFunciEntrada')) == "1"){
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
      $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID, $usuariologuiado);
      $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$stgestion_actoadm,Criteria::NOT_IN);
      //***********************************************************************************************
      if($estado_anulado == 5 && $perm_list_anuladas){
        $c->addAnd(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$estado_anulado,Criteria::EQUAL);
      }elseif(!$perm_list_anuladas){
        $estado_anulado = 4;
        $c->addAnd(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$estado_anulado,Criteria::NOT_EQUAL);
      }
      //***********************************************************************************************
      $c->addAnd(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,8,Criteria::NOT_EQUAL);        
      $c->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,6);
      $c->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
      $this->parametros.="&porFunciEntrada=1";
    }elseif($this->getRequestParameter('porFunciSalida') == "1"){
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
      $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $this->isBorradorCom = false;
      //***********************************************************************************************
      if(!in_array(trim($this->getRequestParameter('estadoactoadministrativo_id')),$stgestion_actoadm)){
        $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$stgestion_actoadm,Criteria::NOT_IN);
      }elseif(in_array(trim($this->getRequestParameter('estadoactoadministrativo_id')),$stgestion_actoadm)){
        $this->isBorradorCom = true;
        $c->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA, 1);
      }
      //***********************************************************************************************
      $c->addAnd(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(5,8),Criteria::NOT_IN);
      $c->addAnd(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,array(1,2,3,4),Criteria::IN);
      //$c->addAnd(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);
      $c->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
      $this->parametros.="&porFunciSalida=1";
    } elseif ($this->getRequestParameter('porGestSaldida') == md5($usuariologuiado . 'porGestSaldida')) {
      $c->setDistinct();
      //***********************************************************************************************
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID,Criteria::INNER_JOIN);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministraInteresadoPeer::ACTOADMINISTRATIVO_ID,Criteria::INNER_JOIN);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoServicioPeer::ACTOADMINISTRATIVO_ID,Criteria::LEFT_JOIN);
      //***********************************************************************************************
      $c->add(ActoAdministrativoPeer::PERIODO_ID, $periodo_id);
      $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(6,7,8,9),Criteria::IN);
      $c->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);//firmante
      $c->add(ActoadministrativoServicioPeer::ACTOADMINISTRATIVO_ID,null,Criteria::ISNULL);
      //***********************************************************************************************
      if($this->getUser()->checkPerm("ACTO_ADMINISTRATIVO_LIST_DEPENDENCIA", $usuariologuiado)){
          $c->add(ActoAdministrativoPeer::DEPENDENCIA_ID,$usuario->getDependenciaId());
      }else{
          $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
      }
      //***********************************************************************************************
      $this->parametros .= "&porGestSaldida=" . md5($usuariologuiado . 'porGestSaldida');
    } elseif ($this->getRequestParameter('porFunciCopia') == "1") {
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
      $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID, $usuariologuiado);
      $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(6,7,8,9),Criteria::IN);
      $c->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID, 7);//copias
      $c->add(ActoAdministrativoPeer::PERIODO_ID, $periodo_id);
      $this->parametros .= "&porFunciCopia=1";
    }elseif($this->getRequestParameter('porProcesoCom')){
      $value_process = trim($this->getRequestParameter('porProcesoCom')) ? trim($this->getRequestParameter('porProcesoCom')) : null;
      //***********************************************************************************************
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
      $c->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
      $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
      $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$stgestion_actoadm,Criteria::IN);
      $c->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
      //***********************************************************************************************
      if($value_process == md5(4)){//revision
        $rolus_id = 3;$tipoproceso_id = 4;
      }elseif($value_process == md5(3)){//gestionar
        $rolus_id = 4;$tipoproceso_id = 3;
      }elseif($value_process == md5(2)){//firma
        $rolus_id = 2;$tipoproceso_id = 2;
      }else{//no definido
        $rolus_id = -1;$tipoproceso_id = -1;
      }
      //***********************************************************************************************
      $c->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,$rolus_id);
      $c->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
      //***********************************************************************************************
      $this->parametros.="&porProcesoCom=".md5($tipoproceso_id);
    }else{
      if(!$perm_list_all){
        $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $c->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
      }else{
        $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
      }
      //***********************************************************************************************
      if(!$perm_list_anuladas){
        $estado_anulado = 4;
        $c->addAnd(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$estado_anulado,Criteria::NOT_EQUAL);
      }
    }
    //*************************************************************************************************
    if(trim($this->getRequestParameter('estadoactoadministrativo_id'))){
      if(trim($this->getRequestParameter('porFunciSalida')) == "1"){
        if(in_array(trim($this->getRequestParameter('estadoactoadministrativo_id')),$stgestion_actoadm)){
          $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,$stgestion_actoadm,Criteria::IN);
        }else{
          $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,trim($this->getRequestParameter('estadoactoadministrativo_id')));
        }
      }elseif(trim($this->getRequestParameter('estadoactoadministrativo_id')) == 5){
        $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $c->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,array(1,2),Criteria::IN);
        $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,trim($this->getRequestParameter('estadoactoadministrativo_id')));
      }else{
        $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $c->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,trim($this->getRequestParameter('estadoactoadministrativo_id')));
      }
      $this->parametros.="&estadoactoadministrativo_id=".trim($this->getRequestParameter('estadoactoadministrativo_id'));			 	
    }
    //************************************************************************************************
    if($this->getRequestParameter('marca')){
      if($this->getRequestParameter('marca')=="sinMarca"){		  	
        $c->add(ActoAdministrativoPeer::MARCA,$usuariologuiado,Criteria::NOT_EQUAL);		
        $this->parametros.="&marca=".$this->getRequestParameter('marca');	
      }else{	
        $c->add(ActoAdministrativoPeer::MARCA,$usuariologuiado);		
        $this->parametros.="&marca=".$this->getRequestParameter('marca');
        $this->marcarEntrega = 1;
      }			 	
    }
    //*************************************************************************************************
    if(trim($this->getRequestParameter('estaEntregado')) != ""){
      $c->add(ActoAdministrativoPeer::ESTAENTREGADO,1);		
      $this->parametros.="&estaEntregado=".trim($this->getRequestParameter('estaEntregado'));
      $this->marcarEntrega = 1;
    }
    //*************************************************************************************************
    if(trim($this->getRequestParameter('consecutivo_documento'))){
      $c->addJoin(ActoAdministrativoPeer::DEPENDENCIA_ID,DependenciaPeer::DEPENDENCIA_ID,Criteria::INNER_JOIN);

      $sqlCondition = sprintf("CONCAT(" . ActoAdministrativoPeer::PERIODO_ID . ", '-', " . DependenciaPeer::CODIGO . ", '-', " . ActoAdministrativoPeer::NUMERO_RESOLUCION . 
      ") LIKE '%s'", '%'.trim($this->getRequestParameter('consecutivo_documento')).'%');
      $c->add(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID, $sqlCondition, Criteria::CUSTOM);
      $this->parametros.="&consecutivo_documento=".trim($this->getRequestParameter('consecutivo_documento'));			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('plantillascom_id')){	
      $c->add(ActoAdministrativoPeer::PLANTILLASCOM_ID, $this->getRequestParameter('plantillascom_id'));
      $this->parametros.="&plantillascom_id=".$this->getRequestParameter('plantillascom_id');			 	
    }
    //*************************************************************************************************
    if(trim($this->getRequestParameter('estadodigitalizacion_id'))){
      $c->add(ActoAdministrativoPeer::ESTADODIGITALIZACION_ID,trim($this->getRequestParameter('estadodigitalizacion_id')));		
      $this->parametros.="&estadodigitalizacion_id=".trim($this->getRequestParameter('estadodigitalizacion_id'));
    }
    //*************************************************************************************************
    if($this->getRequestParameter('prioridadcom_id')){	
      $c->add(ActoAdministrativoPeer::PRIORIDADCOM_ID,$this->getRequestParameter('prioridadcom_id'));		
      $this->parametros.="&prioridadcom_id=".$this->getRequestParameter('prioridadcom_id');
    }
    //*************************************************************************************************
    if($this->getRequestParameter('dependencia_id')){
      $c->add(ActoAdministrativoPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));		
      $this->parametros.="&dependencia_id=".$this->getRequestParameter('dependencia_id');			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('asunto')){
      $c->add(ActoAdministrativoPeer::ASUNTO,'%'.$this->getRequestParameter('asunto').'%',Criteria::LIKE);		
      $this->parametros.="&asunto=".$this->getRequestParameter('asunto');			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('numero_resolucion')){
      $c->add(ActoAdministrativoPeer::NUMERO_RESOLUCION,'%'.$this->getRequestParameter('contenido').'%',Criteria::LIKE);		
      $this->parametros.="&numero_resolucion=".$this->getRequestParameter('numero_resolucion');			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('contenido')){
      $c->add(ActoAdministrativoPeer::CONTENIDO,'%'.$this->getRequestParameter('contenido').'%',Criteria::LIKE);		
      $this->parametros.="&contenido=".$this->getRequestParameter('contenido');			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('periodo_id')){
      $c->add(ActoAdministrativoPeer::PERIODO_ID,$this->getRequestParameter('periodo_id'));		
      $this->parametros.="&periodo_id=".$this->getRequestParameter('periodo_id');			 	
    }
    //*************************************************************************************************
    if($this->getRequestParameter('regional_id')!=""){
      $c->add(ActoAdministrativoPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));		
      $this->parametros.="&regional_id=".$this->getRequestParameter('regional_id');			 	
    }
    //*************************************************************************************************
    $expaddjoin_interesado = false;
    $pnombre_interesado = trim($this->getRequestParameter('pnombre_interesado'));
    if (!empty($pnombre_interesado)) {
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::PRIMER_NOMBRE, '%' . $pnombre_interesado . '%', Criteria::LIKE);
        $this->parametros .= "&pnombre_interesado=" . $pnombre_interesado;
    }
    //*************************************************************************************************
    $snombre_interesado = trim($this->getRequestParameter('snombre_interesado'));
    if (!empty($snombre_interesado)) {
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::SEGUNDO_NOMBRE, '%' . $snombre_interesado . '%', Criteria::LIKE);
        $this->parametros .= "&snombre_interesado=" . $snombre_interesado;
    }
    //*************************************************************************************************
    $papellido_interesado = trim($this->getRequestParameter('papellido_interesado'));
    if (!empty($papellido_interesado)) {
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::PRIMER_APELLIDO, '%' . $papellido_interesado . '%', Criteria::LIKE);
        $this->parametros .= "&papellido_interesado=" . $papellido_interesado;
    }
    //*************************************************************************************************
    $sapellido_interesado = trim($this->getRequestParameter('sapellido_interesado'));
    if (!empty($sapellido_interesado)) {
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::SEGUNDO_APELLIDO, '%' . $sapellido_interesado . '%', Criteria::LIKE);
        $this->parametros .= "&sapellido_interesado=" . $sapellido_interesado;
    }
    //*************************************************************************************************
    $nuid_interesado = trim($this->getRequestParameter('nuid_interesado'));
    if (!empty($nuid_interesado)) {
        $expaddjoin_interesado = true;
        $c->add(InteresadosPeer::NUMERO_IDENTIFICACION, '%' . $nuid_interesado . '%', Criteria::LIKE);
        $this->parametros .= "&nuid_interesado=" . $nuid_interesado;
    }
    //*************************************************************************************************
    if ($expaddjoin_interesado) {
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID, ActoadministraInteresadoPeer::ACTOADMINISTRATIVO_ID);
      $c->addJoin(ActoadministraInteresadoPeer::INTERESADO_ID, InteresadosPeer::INTERESADO_ID);
    }
    //*************************************************************************************************
    if($this->getRequestParameter('fechaCreaInicial')) {
      if ($this->getRequestParameter('fechaCreaFinal')) {
        $c->add(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaInicial') . ' 00:00:00', Criteria::GREATER_THAN);
        $c->addAnd(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaFinal') . ' 23:59:59', Criteria::LESS_THAN);
        $this->parametros .= "&fechaCreaInicial=" . str_replace("/", "-", $this->getRequestParameter('fechaCreaInicial'));
        $this->parametros .= "&fechaCreaFinal=" . str_replace("/", "-", $this->getRequestParameter('fechaCreaFinal'));
      } else {
        $c->add(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaCreaInicial') . ' 00:00:00', Criteria::GREATER_THAN);
        $this->parametros .= "&fechaCreaInicial=" . str_replace("/", "-", $this->getRequestParameter('fechaCreaInicial'));
      }
    }
    //*************************************************************************************************
    if($this->getRequestParameter('fechaResolInicial')) {
      if ($this->getRequestParameter('fechaResolFinal')) {
        $c->add(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaResolInicial') . ' 00:00:00', Criteria::GREATER_THAN);
        $c->addAnd(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaResolFinal') . ' 23:59:59', Criteria::LESS_THAN);
        $this->parametros .= "&fechaResolInicial=" . str_replace("/", "-", $this->getRequestParameter('fechaResolInicial'));
        $this->parametros .= "&fechaResolFinal=" . str_replace("/", "-", $this->getRequestParameter('fechaResolFinal'));
      } else {
        $c->add(ActoAdministrativoPeer::FECHA_CREACION, $this->getRequestParameter('fechaResolInicial') . ' 00:00:00', Criteria::GREATER_THAN);
        $this->parametros .= "&fechaResolInicial=" . str_replace("/", "-", $this->getRequestParameter('fechaResolInicial'));
      }
    }
    //*************************************************************************************************
    //////////////////FILTROS POR USUARIO CON DIFERENTES ROLES///////////////////////////////////////////
    if(trim($this->getRequestParameter('usuarioProyecto'))){
      $c->addAlias('CIUP',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUP.ACTOADMINISTRATIVO_ID');
      $c->add('CIUP.USUARIO_ID',$this->getRequestParameter('usuarioProyecto'));            
      $c->add('CIUP.ROLUSUARIOACTOADMINISTVO_ID',1);//rol proyector
      $this->parametros.="&usuarioProyecto=".$this->getRequestParameter('usuarioProyecto');
    }
    if(trim($this->getRequestParameter('usuarioFirma'))){
      $c->addAlias('CIUF',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUF.ACTOADMINISTRATIVO_ID');
      $c->add('CIUF.USUARIO_ID',$this->getRequestParameter('usuarioFirma'));            
      $c->add('CIUF.ROLUSUARIOACTOADMINISTVO_ID',2);//rol firmas
      $this->parametros.="&usuarioFirma=".$this->getRequestParameter('usuarioFirma');			 	
    }
    if(trim($this->getRequestParameter('usuarioGestiona'))){
      $c->addAlias('CIUCOP',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUCOP.ACTOADMINISTRATIVO_ID');
      $c->add('CIUCOP.USUARIO_ID',$this->getRequestParameter('usuarioGestiona'));            
      $c->add('CIUCOP.ROLUSUARIOACTOADMINISTVO_ID',4);//rol gestor
      $this->parametros.="&usuarioGestiona=".$this->getRequestParameter('usuarioGestiona');			 	
    }
    if(trim($this->getRequestParameter('usuarioRevisa'))){
      $c->addAlias('CIUDES',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUDES.ACTOADMINISTRATIVO_ID');
      $c->add('CIUDES.USUARIO_ID',$this->getRequestParameter('usuarioRevisa'));            
      $c->add('CIUDES.ROLUSUARIOACTOADMINISTVO_ID',3);//rol revisor
      $this->parametros.="&usuarioRevisa=".$this->getRequestParameter('usuarioRevisa');			 	
    }	
    if(trim($this->getRequestParameter('usuarioDestino'))){
      $c->addAlias('CIUDES',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUDES.ACTOADMINISTRATIVO_ID');
      $c->add('CIUDES.USUARIO_ID',$this->getRequestParameter('usuarioDestino'));            
      $c->add('CIUDES.ROLUSUARIOACTOADMINISTVO_ID',6);//rol revisor
      $this->parametros.="&usuarioDestino=".$this->getRequestParameter('usuarioDestino');			 	
    }
    if(trim($this->getRequestParameter('usuarioCopia'))){
      $c->addAlias('CIUCPO',ActoadministrativoUsuarioPeer::TABLE_NAME);
      $c->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,'CIUCPO.ACTOADMINISTRATIVO_ID');
      $c->add('CIUCPO.USUARIO_ID',$this->getRequestParameter('usuarioCopia'));            
      $c->add('CIUCPO.ROLUSUARIOACTOADMINISTVO_ID',7);//rol copias
      $this->parametros.="&usuarioCopia=".$this->getRequestParameter('usuarioCopia');			 	
    }
    //*************************************************************************************************
    $orden = trim($this->getRequestParameter('orden'));
    if($orden){
      if($orden == "FECHA_CREACION"){ $c->addDescendingOrderByColumn(ActoAdministrativoPeer::FECHA_CREACION); }
      if($orden == "RADICADO"){ $c->addDescendingOrderByColumn(ActoAdministrativoPeer::NUMERO_RESOLUCION); }
      if($orden == "ASUNTO"){ $c->addDescendingOrderByColumn(ActoAdministrativoPeer::ASUNTO); }
      if($orden == "DEPEPENDENCIA"){ $c->addDescendingOrderByColumn(ActoAdministrativoPeer::DEPENDENCIA_ID); }
      $this->parametros.="&orden=".$this->getRequestParameter('orden');			 	
    }else{
      $c->addDescendingOrderByColumn(ActoAdministrativoPeer::FECHA_CREACION);
    }
    //*************************************************************************************************
    return $c;
  }
}
