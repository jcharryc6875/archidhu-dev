<?php

/**
 * wf_imagen_instancia actions.
 *
 * @package    symfony
 * @subpackage wf_imagen_instancia
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class wf_imagen_instanciaActions extends sfActions
{
  public function executeIndex()
  {
    $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
    //$this->wf_imagen_instanciaList = WfImagenInstanciaPeer::doSelect(new Criteria());
    $parametros_consulta="wfinstancia_id=".$this->getRequestParameter('wfinstancia_id');
    $c=new Criteria();
    $c->add(WfImagenInstanciaPeer::WFINSTANCIA_ID, $this->wfinstancia_id);
    $pager=new sfPropelPager('WfImagenInstancia',10);
  	$pager->setCriteria($c);
  	$pager->setPage($this->getRequestParameter('page',1));
  	$pager->init();
  	$this->filtros_consulta = $parametros_consulta;
  	$this->pager=$pager;
  	  
  }

  public function executeShow($request)
  {
    $this->wf_imagen_instancia = WfImagenInstanciaPeer::retrieveByPk($request->getParameter('wf_imagen_instancia_id'));
    $this->forward404Unless($this->wf_imagen_instancia);
  }

  public function executeCreate()
  {
    $this->wf_imagen_instancia = new WfImagenInstancia();
    $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
    $this->setTemplate('edit');
  }

  public function executeEdit($request)
  {
    $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
    $this->wf_imagen_instancia=WfImagenInstanciaPeer::retrieveByPk($request->getParameter('wf_imagen_instancia_id'));
  }

  public function executeUpdate($request)
  {
    $this->wfinstancia_id=$this->getRequestParameter('wfinstancia_id');
    
    if (!$this->getRequestParameter('wf_imagen_instancia_id'))
    {
      $wf_imagen_instancia = new WfImagenInstancia();
    }
    else
    {
      $wf_imagen_instancia = WfImagenInstanciaPeer::retrieveByPk($this->getRequestParameter('wf_imagen_instancia_id'));
      //$this->validaEditFile($wf_imagen_instancia->getRuta());
      $this->forward404Unless($wf_imagen_instancia);
    }
    $msgerror = array();
	$usuario_conectado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $usernameloguiado = $this->getUser()->getAttribute('username', '', 'subscriber');
    $wf_imagen_instancia->setWfEstadoImagenId($this->getRequestParameter('wf_estado_imagen'));//
    $wf_imagen_instancia->setDescripcion($this->getRequestParameter('descripcion'));
    $wf_imagen_instancia->setFolios($this->getRequestParameter('folios'));
    $wf_imagen_instancia->setFechaDocumento($this->getRequestParameter('fecha_documento'));
    $wf_imagen_instancia->setWfinstanciaId($this->getRequestParameter('wfinstancia_id'));
    $wf_imagen_instancia->setFechaCreacion(date("Y-m-d G:i:s"));
    //******************************************************************************************
    //MANEJO DE ARCHIVOS UPLAODS
    $ruta_uploads = trim($this->getRequestParameter('ruta'));
    $ruta_archivo = WfImagenInstancia::resolveWfUrlUpload($ruta_uploads);
    $wf_imagen_instancia->setRuta($ruta_archivo);    
    //******************************************************************************************
    $wf_imagen_instancia->save();
    //******************************************************************************************
    return $this->redirect($this->getRequest()->getScriptName().'/wf_imagen_instancia/index?wf_imagen_instancia_id='.$wf_imagen_instancia->getPrimaryKey().'&wfinstancia_id='.$this->getRequestParameter('wfinstancia_id'));
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($wf_imagen_instancia = WfImagenInstanciaPeer::retrieveByPk($request->getParameter('wf_imagen_instancia_id')));

    $wf_imagen_instancia->delete();

    $this->redirect('wf_imagen_instancia/index');
  }
  
  public function executeFile()
  {
    $this->com_interna = ComInternaPeer::retrieveByPk($this->getRequestParameter('comenviada_id'));
  }
  
  public function executeUploads()
  { 
    
  	if ($this->getRequest()->hasFiles())  	
    {
      
    	$usuariologuiado = $this->getUser()->getAttribute('username', '', 'subscriber');
    	$dirRaiz = ParametroPeer::retrieveByPk(9);
		  $dirInterna  = ParametroPeer::retrieveByPk(15);
		  $cons    = $this->getNewConsecutivo();
      //$fileName = (str_replace(" ","-",$file_vars["filename"]));
        
        
        $file_vars = pathinfo($this->getRequest()->getFileName('file'));
        $util_simad = new simad_util();
        $fileName = $util_simad->clean_name_file($file_vars);
		    
        $directorio = simad_util::createPath($dirRaiz->getValortexto().'/'.$dirInterna->getValortexto().'/'.($usuariologuiado));                
        
        
        $this->getRequest()->moveFile('file', $directorio.'/'.$cons.'_'.$fileName);
        
        $this->fileName = $cons.'_'.($fileName);
        
        
    } 	         
  }	
  
  public function executeDzFileUpload()
  {
  	$data_array = array();
  	if (!empty($_FILES))
  	{
  		//***************************************************************************************
  		$dirRaiz = ParametroPeer::retrieveByPk(17);
  		$dirTemp = ParametroPeer::retrieveByPk(65);
  		//***************************************************************************************
  		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  		$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
  		$entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
  		//$regional_text = $usuario->getRegional()->getDirectorioName();
  		//$entidad_text = $entidad_text.'/'.$regional_text;
  		$path = $dirRaiz->getValortexto().$entidad_text."/".$dirTemp->getValortexto()."/";
  		$directorio = realpath(simad_util::createPath($path));
  		//***************************************************************************************
  		$file_vars = pathinfo($_FILES['file']['name']);
  		$util_simad = new simad_util();
  		$fileName = $util_simad->clean_name_file($file_vars);
  		$tempFile = $_FILES['file']['tmp_name'];
  		$cons    = $this->getNewConsecutivo();
  		//***************************************************************************************
  		move_uploaded_file($tempFile,$directorio.DIRECTORY_SEPARATOR.$cons.'_'.$fileName);
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
  	
	function StrEndsWith($haystack, $needle = ",")
	{
		if (strlen($haystack) == 0) {
			return true;
		}
		return (substr($haystack, strlen($haystack) - 1) === $needle);
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
}
