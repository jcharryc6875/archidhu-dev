<?php

/**
 * entidad actions.
 *
 * @package    simad
 * @subpackage entidad
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class entidadActions extends sfActions
{
    
  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/simad/no_autorizado.html');
  	}
  }
  
  public function verificaPrilegioCerrar($currentForm)
  { 
 	$usuarioLoguiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuarioLoguiado)){
		$this->redirect(sfConfig::get('base_simad').'/simad/no_autorizado.html');
	}	  
  }
  
  public function executeIndex()
  {
    $this->verificaPrilegio("forma/list");    
    $parametros = "a=1";
    $c = new Criteria();
    $c->setDistinct();
    //*********************************************************************************************************/
    if($this->getRequestParameter('descripcion')){
       $c->add(EntidadPeer::DESCRIPCION,'%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
       $parametros .= "&descripcion=" . $this->getRequestParameter('descripcion'); 
    }
    //*********************************************************************************************************/
    if($this->getRequestParameter('codigo')){
       $c->add(EntidadPeer::CODIGO,'%'.$this->getRequestParameter('codigo').'%',Criteria::LIKE);
       $parametros .= "&codigo=" . $this->getRequestParameter('codigo'); 
    }
    //*********************************************************************************************************/
    if($this->getRequestParameter('es_actual')){
       $c->add(EntidadPeer::ES_ACTUAL,$this->getRequestParameter('es_actual'));
       $parametros .= "&es_actual=" . $this->getRequestParameter('es_actual'); 
    }    
    //*********************************************************************************************************/	 
    $c->addAscendingOrderByColumn(EntidadPeer::DESCRIPCION);
    
    $pager = new sfPropelPager('Entidad', 10);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
    $this->parametros = $parametros;
    $this->directorio_header = "/images/encabezado_carta/";
  }

  public function executeCreate()
  {
    $this->verificaPrilegio("forma/list");
    $this->setTemplate('edit');
    $this->entidad = new Entidad();
  }

  public function executeEdit($request)
  {
    $this->verificaPrilegio("forma/list");
    $this->entidad = EntidadPeer::retrieveByPk($request->getParameter('entidad_id'));
  }

  public function executeUpdate($request)
  {
    $this->verificaPrilegio("forma/list");
        
    $this->forward404Unless($request->isMethod('post'));
    
    if(!$request->getParameter('entidad_id'))
    {
        $entidad = new Entidad();
    }else{
        $entidad = EntidadPeer::retrieveByPk($request->getParameter('entidad_id'));
    }
    $entidad->setDescripcion($request->getParameter('descripcion'));
    $entidad->setCodigo($request->getParameter('codigo'));
    $entidad->setDirectorioName($request->getParameter('directorio_name'));
    $entidad->setEsActual($request->getParameter('es_actual'));
    $entidad->setLogoHeader($this->MoveImgHeader($this->getRequestParameter('name_logo'),$corp_logo));
    //**********************************************************************************************
    $corp_logo = 'images'.DIRECTORY_SEPARATOR.'encabezado_carta'.DIRECTORY_SEPARATOR.'logos_carnet';
    //**********************************************************************************************
    $entidad->setLogoCorporativo($this->MoveImgHeader($this->getRequestParameter('corp_logo'),$corp_logo));
    $entidad->save();
    $this->redirect('entidad/saveAndClose');
  }
  
  public function executeFile()
  {
  	$this->actionUpdate = 'entidad/uploads';
  	$this->qvars = $this->getRequestParameter('qvars');
  }
  
  public function executeCorpFile()
  {
  	$this->actionUpdate = 'entidad/uploadsCorp';
  	$this->qvars = $this->getRequestParameter('qvars');
  	$this->setTemplate('file');  	
  }
  
  public function executeSaveAndClose()
  {
  	
  }
  
  public function executeUploads()
  {
    $fileName = "";
    //************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file)
    {
  		$dirRaiz = ParametroPeer::retrieveByPk(17)->getValortexto();
  		$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
  		//********************************************************************************
  		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  		$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
  		$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
  		$path = $dirRaiz.$entidad_text."/".$dirTemp."/";
  		//********************************************************************************
  		$directorio = realPath(simad_util::createPath($path));
  		//********************************************************************************
        $file_vars = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $fileName = md5($fileName.date("Y-m-d G:i:s")).".".$file_vars["extension"];
        @move_uploaded_file($file['tmp_name'], $directorio.'/'.$fileName);
        //********************************************************************************
	  }
    //************************************************************************************
    $this->qvars = $this->getRequestParameter('qvars');	
    $this->fileName = $fileName;
  }
  
  public function executeUploadsCorp()
  {
  	foreach ($this->getRequest()->getFiles() as $file)
    {
  		//********************************************************************************
  		$dirRaiz = ParametroPeer::retrieveByPk(17)->getValortexto();
  		$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
  		//********************************************************************************
  		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  		$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
  		$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
  		$path = $dirRaiz.$entidad_text."/".$dirTemp."/";
  		//********************************************************************************
  		$directorio = realPath(simad_util::createPath($path));
  		//********************************************************************************
  		$file_vars = pathinfo($file['name']);
  		$util_simad = new simad_util();
  		$fileName = $util_simad->clean_name_file($file_vars);
        @move_uploaded_file($file['tmp_name'], $directorio.'/'.$fileName);
  	}
    //************************************************************************************
    $this->qvars = $this->getRequestParameter('qvars');	
    $this->fileName = $fileName;
  	$this->setTemplate('uploads');
  }
  
  private function MoveImgHeader($file_path,$dir_target)
  {
  	$target_name = trim($target_name) ? 'images'.DIRECTORY_SEPARATOR.'encabezado_carta' : trim($dir_target);
    $ruta_archivo = $file_path;
    //MANEJO DE ARCHIVOS UPLAODS
	if(trim($file_path))
    {
	    $archivos_upload = preg_split("/[,]+/", $file_path,-1,PREG_SPLIT_NO_EMPTY);
	    $cantidad_archivos = count($archivos_upload);
	    /********************************************************************************/
        //RUTA Y ESTRUCTURA DE ARCHIVOS
    	$dirRaiz = ParametroPeer::retrieveByPk(17)->getValortexto();
    	$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
    	/********************************************************************************/
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    	$entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        /********************************************************************************/    	    	
    	$directorio_target = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$target_name;
    	$directorio_tmp = $dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTemp.DIRECTORY_SEPARATOR;
    	/********************************************************************************/
    	$directorio_target = realPath(simad_util::createPath($directorio_target));
    	/********************************************************************************/
    	foreach($archivos_upload as $file_upload)
        {
    		if(trim($file_upload) != "")
            {
    			if(copy($directorio_tmp.$file_upload,$directorio_target.DIRECTORY_SEPARATOR.$file_upload))
                {
    				$ruta_archivo = $file_upload;
    				unlink($directorio_tmp.$file_upload);
                }else{
                    $msgerror[] =  "Archivo no encontrado ".$file_upload;
                }
    		}
    	}
    }
    return $ruta_archivo;
	//*******************************************************************************************************************  
  }
  
  public function executeDelete($request)
  {
    $this->verificaPrilegio("forma/list");
    $this->forward404Unless($entidad = EntidadPeer::retrieveByPk($request->getParameter('entidad_id')));

    $entidad->delete();

    $this->redirect('entidad/index');
  }    
}