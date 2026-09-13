<?php

/**
 * regional actions.
 *
 * @package    simad
 * @subpackage regional
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class regionalActions extends sfActions
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
 	$usuarioLoguiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuarioLoguiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}	  
  }
  
  public function executeIndex()
  {
    //**********************************************************************************************
  	$usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$regional_conectado = $this->getUser()->getAttribute('regional_id', '', 'subscriber');
  	$entidad_conectado = $this->getUser()->getAttribute('entidad_id', '', 'subscriber');
  	//**********************************************************************************************
    $this->verificaPrilegio("regional/list");    
    $parametros = "a=1";
    $c = new Criteria();
    $c->setDistinct();
    //*********************************************************************************************************/
    if($this->getRequestParameter('descripcion')){
       $c->add(RegionalPeer::DESCRIPCION,'%'.$this->getRequestParameter('descripcion').'%',Criteria::LIKE);
       $parametros .= "&descripcion=" . $this->getRequestParameter('descripcion'); 
    }
    //*********************************************************************************************************/	 
    $c->addAscendingOrderByColumn(RegionalPeer::ENTIDAD_ID);
    $c->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);
    //*********************************************************************************************************/
    $listall = $this->getUser()->checkPerm('LISTAR_TODAS_REGIONALES', $usuario_conectado);
    //*********************************************************************************************************/
    if(!$listall){
    	$c->add(RegionalPeer::ENTIDAD_ID,$entidad_conectado);
    }
    //*********************************************************************************************************/
    $pager = new sfPropelPager('Regional', 5);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
    $this->parametros = $parametros;
    $this->directorio_header = "/images/encabezado_carta/";
    $this->filedir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."encabezado_carta".DIRECTORY_SEPARATOR;
  }

  public function executeCreate()
  {
    $this->verificaPrilegio("regional/create");
    $this->regional = new Regional();
    $this->setTemplate('edit');
    $this->directorio_header = "/images/encabezado_carta/";
    $this->filedir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."encabezado_carta".DIRECTORY_SEPARATOR;
  }

  public function executeEdit($request)
  {
    $this->verificaPrilegio("regional/edit");
    $this->regional = RegionalPeer::retrieveByPk($request->getParameter('regional_id'));
    $this->directorio_header = "/images/encabezado_carta/";
    $this->filedir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."encabezado_carta".DIRECTORY_SEPARATOR;
  }

  public function executeUpdate($request)
  {
    $this->verificaPrilegio("regional/edit");
    $this->forward404Unless($request->isMethod('post'));
    //**********************************************************************************************
    $dir_membrete = 'images'.DIRECTORY_SEPARATOR.'encabezado_carta'.DIRECTORY_SEPARATOR;
    //**********************************************************************************************
    if(!$request->getParameter('regional_id')){
        $regional = new Regional();
    }else{
        $regional = RegionalPeer::retrieveByPk($request->getParameter('regional_id'));
        $old_membrete = $regional->getImageMembrete();
    }
    $regional->setCiudadId($request->getParameter('ciudad_id'));
    $regional->setEntidadId($request->getParameter('entidad_id'));
    $regional->setDirectorioName($request->getParameter('directorio_name'));
    $regional->setDescripcion($request->getParameter('descripcion'));
    $regional->setDireccion($request->getParameter('direccion'));
    $regional->setEsVisible($request->getParameter('es_visible'));
    $regional->setImageMembrete($this->MoveImgHeader($this->getRequestParameter('image_membrete'),$dir_membrete));
    $regional->save();
    //**********************************************************************************************
    $this->redirect('regional/edit?regional_id='.$regional->getPrimaryKey());
  }
  
  public function executeFile()
  {
  	$this->actionUpdate = 'regional/uploads';
  	$this->qvars = $this->getRequestParameter('qvars');
  }
  
  public function executeDelete($request)
  {
    $this->verificaPrilegio("regional/delete");
    $this->forward404Unless($regional = RegionalPeer::retrieveByPk($request->getParameter('regional_id')));

    $regional->delete();

    $this->redirect('regional/index');
  }
  
  public function executeSaveAndClose()
  {
  	
  }
  
  public function executeUploads()
  {
  	if ($this->getRequest()->hasFiles()){
  		/********************************************************************************/
  		$dirRaiz = ParametroPeer::retrieveByPk(17);
  		$dirTemp = ParametroPeer::retrieveByPk(65);
  		/********************************************************************************/
  		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  		$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
  		$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
  		$path = $dirRaiz->getValortexto().$entidad_text.DIRECTORY_SEPARATOR.$dirTemp->getValortexto().DIRECTORY_SEPARATOR;
  		/********************************************************************************/
  		$directorio = realPath(simad_util::createPath($path));
  		/********************************************************************************/
        $file_vars = pathinfo($this->getRequest()->getFileName('file'));
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
        $fileName = md5($fileName.date("Y-m-d G:i:s")).".".$file_vars["extension"];
        $this->getRequest()->moveFile('file', $directorio.'/'.$fileName);
        $this->fileName = $fileName;
        $this->qvars = $this->getRequestParameter('qvars');
    }elseif($files_uploads){
      $this->fileName = "";
      $this->qvars = $this->getRequestParameter('qvars');
    }	
  }
  
  private function MoveImgHeader($file_path,$dir_target=null)
  {
    $target_name = null;
  	$target_name = trim($target_name) ? 'images'.DIRECTORY_SEPARATOR.'encabezado_carta' : trim($dir_target);
    $ruta_archivo = trim($file_path) ? trim($file_path) : "";
    //MANEJO DE ARCHIVOS UPLAODS
	if(trim($file_path))
    {
	    $archivos_upload = preg_split("/[,]+/", $file_path,-1,PREG_SPLIT_NO_EMPTY);
	    //********************************************************************************
        //RUTA Y ESTRUCTURA DE ARCHIVOS
    	$dirRaiz = ParametroPeer::retrieveByPk(17)->getValortexto();
    	$dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
    	//********************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    	$entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        //********************************************************************************    	    	
    	$directorio_target = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$target_name;
    	$directorio_tmp = $dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTemp.DIRECTORY_SEPARATOR;
    	//********************************************************************************
    	$directorio_target = realPath(simad_util::createPath($directorio_target));
    	//********************************************************************************
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
	//*************************************************************************************  
  }
  
  public function executeVerificar()
  {
  	$regional = RegionalPeer::retrieveByPk($this->getRequestParameter('regional_id'));
    if(trim($regional->getEsVisible()) == 1){
        $regional->setEsVisible(0);
    }else{
        $regional->setEsVisible(1);
    }
    $regional->save();
    $this->regional = $regional;
  	$this->forward404Unless($regional);
  }
}