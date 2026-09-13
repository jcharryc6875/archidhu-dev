<?php

/**
 * plantillas_com actions.
 *
 * @package    simad
 * @subpackage entidad
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class plantillas_comActions extends sfActions
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
  
  public function executeLoadServicioFields()
  {
    $status = 400;
    $plantillacom_id = $this->getRequestParameter('plantillacom_id') ? $this->getRequestParameter('plantillacom_id') : null;
    $genera_servicio = $this->getRequestParameter('genera_servicio') ? $this->getRequestParameter('genera_servicio') : false;
    //***********************************************************************************************
    try {
      if($genera_servicio !== "true"){
        $response_info['status'] = 300;
        $response_info['fields_data'] = "";
        $response_info['message'] = "Campos dinamicos generados exitosamente";
        //*******************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
      }
      //*********************************************************************************************
      $response_info['message'] = "Campos dinamicos generados exitosamente";
      $response_info['fields_data'] = PlantillasComPeer::getHtmlFieldsByServicio();
      $status = !empty($response_info['fields_data']) ? 200 : 400;
    } catch (PropelException $th) {
      $status = 400;
      $response_info['message'] = "Error de aceso a los datos, ".$th->getMessage();
    } catch (\Exception $th) {
      $status = 400;
      $response_info['message'] = "Error interno de la aplicacion, ".$th->getMessage();
    } catch (\Throwable $th) {
      $status = 400;
      $response_info['message'] = "Error interno del servidor, ".$th->getMessage();
    }
    //***********************************************************************************************
    $response_info['status'] = $status;
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response_info));
  }

  public function executeList()
  {
    $this->forward('plantillas_com','index');
  }

  public function executeIndex()
  {
    //$this->verificaPrilegio("plantillas_com/list");    
    $parametros = "a=1";
    $c = new Criteria();
    $c->setDistinct();
    //*********************************************************************************************************
    if(trim($this->getRequestParameter('descripcion'))){
       $c->add(PlantillasComPeer::DESCRIPCION,'%'.trim($this->getRequestParameter('descripcion')).'%',Criteria::LIKE);
       $parametros .= "&descripcion=" . trim($this->getRequestParameter('descripcion')); 
    }
    //*********************************************************************************************************
    if(trim($this->getRequestParameter('nombre'))){
      $c->add(PlantillasComPeer::DESCRIPCION,'%'.trim($this->getRequestParameter('nombre')).'%',Criteria::LIKE);
      $parametros .= "&nombre=" . trim($this->getRequestParameter('nombre')); 
    }
    //*********************************************************************************************************
    if(trim($this->getRequestParameter('codigo'))){
       $c->add(PlantillasComPeer::CODIGO,'%'.trim($this->getRequestParameter('codigo')).'%',Criteria::LIKE);
       $parametros .= "&codigo=" . trim($this->getRequestParameter('codigo')); 
    }
    //*********************************************************************************************************
    if($this->getRequestParameter('modulo_id')){
      $c->add(PlantillasComPeer::MODULO_ID,$this->getRequestParameter('modulo_id'));
      $parametros .= "&modulo_id=" . $this->getRequestParameter('modulo_id'); 
    }
    //*********************************************************************************************************
    if($this->getRequestParameter('dependencia_id')){
      $c->add(PlantillasComPeer::DEPENDENCIA_ID,$this->getRequestParameter('dependencia_id'));
      $parametros .= "&dependencia_id=" . $this->getRequestParameter('dependencia_id'); 
    }
    //*********************************************************************************************************
    if($this->getRequestParameter('regional_id')){
      $c->add(PlantillasComPeer::REGIONAL_ID,$this->getRequestParameter('regional_id'));
      $parametros .= "&regional_id=" . $this->getRequestParameter('regional_id'); 
    }
    //*********************************************************************************************************
    if($this->getRequestParameter('es_actual')){
      $c->add(PlantillasComPeer::ES_ACTUAL,$this->getRequestParameter('es_actual'));
      $parametros .= "&es_actual=" . $this->getRequestParameter('es_actual'); 
    }    
    //*********************************************************************************************************
    $c->addAscendingOrderByColumn(PlantillasComPeer::MODULO_ID);
    $c->addAscendingOrderByColumn(PlantillasComPeer::DESCRIPCION);
    //*********************************************************************************************************
    $this->pager = PlantillasComPeer::doSelect($c);
    $this->parametros = $parametros;
  }

  public function executeCurrentList()
  {
    //$this->verificaPrilegio("plantillas_com/user_enable");
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    //*********************************************************************************************************
    $isFirmante = UsuarioProcesocomPeer::hasProcesoComByUser($usuario->getPrimaryKey(),ComTipoProceso::Firmante);
    if(!$isFirmante){
      $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }
    //*********************************************************************************************************
    $this->plantillas_com = PlantillasComPeer::getPlantillasComByDependencia($usuario->getDependenciaId());
  }

  public function executeEnableComDoc($request)
  {
    //$this->verificaPrilegio("plantillas_com/user_enable");
    $this->forward404Unless($request->isMethod('post'));
    $status = 400;
    //*********************************************************************************************************
    $objectcom_pk = $request->getParameter('objectcom_pk') ? SED::decryption($request->getParameter('objectcom_pk')) : null;
    $isEnable = $request->getParameter('isEnable') ? $request->getParameter('isEnable') : null;
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //*********************************************************************************************************
    try {
      $plantillas_com = PlantillasComPeer::retrieveByPk($objectcom_pk);
      $plantillas_com_old = clone $plantillas_com;
      //*******************************************************************************************************
      $plantillas_com->setFirmaDesatendida($isEnable == true ? 1 : 0);
      $plantillas_com->save();
      $status = 200;
      //*******************************************************************************************************
      $codigo_main = empty(trim($plantillas_com->getCodigo())) ? trim($plantillas_com->getCodigo()) : $plantillas_com->getPrimaryKey();
      AuditLogPeer::guardarAuditoriaLite(PlantillasComPeer::getOMClass(),$plantillas_com_old,$plantillas_com,ModulesEnable::Seguridad,$codigo_main,$usuariologuiado);
    } catch (PropelException $th) {
      $status = 400;
    } catch (\Exception $th) {
      $status = 400;
    } catch (\Throwable $th) {
      $status = 400;
    }
    //*********************************************************************************************************
    http_response_code($status);
    exit;
  }

  public function executeCreate()
  {
    //$this->verificaPrilegio("plantillas_com/create");
    $this->setTemplate('edit');
    $this->plantillas_com = new PlantillasCom();
    $this->directorio_header = "/images/encabezado_carta/";
    $this->filedir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."encabezado_carta".DIRECTORY_SEPARATOR;
  }

  public function executeEdit($request)
  {
    //$this->verificaPrilegio("plantillas_com/edit");
    $this->plantillas_com = PlantillasComPeer::retrieveByPk($request->getParameter('plantillascom_id'));
    $this->directorio_header = "/images/encabezado_carta/";
    $this->filedir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."encabezado_carta".DIRECTORY_SEPARATOR;
    $this->plantillacom_servicio = PlantillascomServicioPeer::getPlantillaComByTipoServicio($request->getParameter('plantillascom_id'));
    if($this->plantillacom_servicio == null){
      $this->plantillas_com->setGeneraServicio(0);
  	}
  }

  public function executeUpdate($request)
  {
    //$this->verificaPrilegio("plantillas_com/edit");
    $this->forward404Unless($request->isMethod('post'));

    if(!$request->getParameter('plantillascom_id'))
    {
      $plantillas_com = new PlantillasCom();
      $object_old = null;
    }else{
      $plantillas_com = PlantillasComPeer::retrieveByPk($request->getParameter('plantillascom_id'));
      $object_old = clone $plantillas_com;
    }
    //**********************************************************************************************************
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    //**********************************************************************************************************
    $plantillas_com->setDescripcion(trim($request->getParameter('descripcion')));
    $plantillas_com->setNombre(trim($request->getParameter('nombre')));
    $plantillas_com->setCodigo(trim($request->getParameter('codigo')));
    $plantillas_com->setModuloId($request->getParameter('modulo_id') ? $request->getParameter('modulo_id') : null);
    $plantillas_com->setDependenciaId($request->getParameter('dependencia_id') ? $request->getParameter('dependencia_id') : null);
    $plantillas_com->setRegionalId($request->getParameter('regional_id') ? $request->getParameter('regional_id') : null);
    $plantillas_com->setEsActual($request->getParameter('es_actual'));
    $plantillas_com->setContents($request->getParameter('contents'));
    $plantillas_com->setUseMembrete(trim($request->getParameter('use_membrete')) ? 1 : 0);
    $plantillas_com->setUseHeaders(trim($request->getParameter('use_headers')) ? 1 : 0);
    $plantillas_com->setGeneraServicio(trim($request->getParameter('genera_servicio')) ? 1 : 0);
    //**********************************************************************************************
    $dir_membretes = 'images'.DIRECTORY_SEPARATOR.'encabezado_carta';
    $image_membrete = trim($this->getRequestParameter('image_membrete')) ? trim($this->getRequestParameter('image_membrete')) : null;
    //**********************************************************************************************
    if(!empty($image_membrete)){
      $plantillas_com->setImageMembrete($this->MoveImgHeader($image_membrete,$dir_membretes));
    }else{
      $plantillas_com->setImageMembrete($image_membrete);
    }
    //**********************************************************************************************************
    $plantillas_com->save();
    //**********************************************************************************************************
    if($plantillas_com->getGeneraServicio()){
      $tiposervicio_id = $request->getParameter('tiposervicio_id') ? $request->getParameter('tiposervicio_id') : null;
      $prioridadservicio_id = $request->getParameter('prioridadsolicitudservicio_id') ? $request->getParameter('prioridadsolicitudservicio_id') : null;
      $resp_conf = PlantillascomServicioPeer::addNewObject($plantillas_com->getPrimaryKey(),$tiposervicio_id,$prioridadservicio_id);
      if($resp_conf['error'] == true){
        $plantillas_com->setGeneraServicio(0);
        $plantillas_com->save();
        //******************************************************************************************************
        $this->getUser()->setFlash('notice', 'Los parametros de configuracion del servicio no se pudieron establecer, contactese con el administrador');
      }else{
        $this->getUser()->setFlash('success', 'Todos los cambios fueron almacenados satisfactoriamente');
      }
    }
    //**********************************************************************************************************
    $codigo_main = empty(trim($plantillas_com->getCodigo())) ? trim($plantillas_com->getCodigo()) : $plantillas_com->getPrimaryKey();
    AuditLogPeer::guardarAuditoriaLite("PlantillasCom",$object_old,$plantillas_com,ModulesEnable::Seguridad,$codigo_main,$usuariologuiado);
    //**********************************************************************************************************
    $this->redirect('plantillas_com/edit?plantillascom_id='.$plantillas_com->getPrimaryKey());
  }
  
  public function executeFile()
  {
  	$this->actionUpdate = 'plantillas_com/uploads';
    $this->qvars = $this->getRequestParameter('qvars');
  }
  
  public function executeSaveAndClose()
  {
  	
  }

  public function executeUploads()
  {
    $fileName = "";
    //**********************************************************************************
    foreach ($this->getRequest()->getFiles() as $file)
    {
  		$dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
  		$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
  		//********************************************************************************
  		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  		$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
      //********************************************************************************
  		$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
  		$path = $dirRaiz.$entidad_text.DIRECTORY_SEPARATOR.$dirTemp.DIRECTORY_SEPARATOR;
  		//********************************************************************************
  		$directorio = realPath(simad_util::createPath($path));
  		//********************************************************************************
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $fileName = md5($fileName.date("Y-m-d G:i:s")).".".$file_vars["extension"];
        @move_uploaded_file($file['tmp_name'], $directorio.'/'.$fileName);
	  }
    //**********************************************************************************
    $this->qvars = trim($this->getRequestParameter('qvars'));	
    $this->fileName = $fileName;
  }

  private function MoveImgHeader($file_path,$target_name)
  {
  	$target_name = trim($target_name) ? trim($target_name) : 'images'.DIRECTORY_SEPARATOR.'encabezado_carta';
    $fullpath_membrete = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$target_name;
    //*************************************************************************************
    if(file_exists($fullpath_membrete.DIRECTORY_SEPARATOR.$file_path)){
      return $file_path;
    }
    //*************************************************************************************
    //MANEJO DE ARCHIVOS UPLAODS
	  if(trim($file_path))
    {
	    $archivos_upload = preg_split("/[,]+/", $file_path,-1,PREG_SPLIT_NO_EMPTY);
	    //***********************************************************************************
      //RUTA Y ESTRUCTURA DE ARCHIVOS
    	$dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
    	$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
    	//***********************************************************************************
      $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
      $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    	$entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
      //*********************************************************************************** 	    	
    	$directorio_target = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$target_name;
    	$directorio_tmp = $dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTemp.DIRECTORY_SEPARATOR;
    	//***********************************************************************************
    	$directorio_target = realPath(simad_util::createPath($directorio_target));
    	//***********************************************************************************
    	foreach($archivos_upload as $file_upload)
      {
    		if(trim($file_upload) != "")
        {
    			if(@rename($directorio_tmp.$file_upload,$directorio_target.DIRECTORY_SEPARATOR.$file_upload))
          {
    				$file_path = $file_upload;
    				if(file_exists($directorio_tmp.$file_upload)){ unlink($directorio_tmp.$file_upload); }
          }else{
            $msgerror[] =  "Archivo no encontrado ".$file_upload;
            $file_path = null;
          }
    		}
    	}
    }
    return $file_path;
  }
}