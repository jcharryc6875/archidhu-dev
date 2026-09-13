<?php

/**
 * prov_documento actions.
 *
 * @package    simad
 * @subpackage prov_documento
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_documentoActions extends sfActions
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
      
  public function executeIndex()
  { 
    $currentForm="prov_documento/index";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegioCerrar($currentForm);
    
    $c = new Criteria();
    $parametros_consulta="";
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    if($prov_periodo_validez_id != ""){
		$c->add(ProvDocumentoPeer::PROV_PERIODO_VALIDEZ_ID, $prov_periodo_validez_id);
		$parametros_consulta .= "&prov_periodo_validez_id=".$prov_periodo_validez_id;		
	}
    $this->parametros_consulta=$parametros_consulta;
    $this->prov_periodo_validez_id=$prov_periodo_validez_id;
    $pager = new sfPropelPager('ProvDocumento', 10);
    $pager->setCriteria($c);        
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
    //$this->prov_documentoList = ProvDocumentoPeer::doSelect(new Criteria());
  }
  
  public function executeExcel()
  {     
  	$this->usuario_name = UsuarioPeer::retrieveByPK($this->getUser()->getAttribute('usuario_id', '', 'subscriber'));    
    
    $c = new Criteria();
    $parametros_consulta="";
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    if($prov_periodo_validez_id != ""){
		$c->add(ProvDocumentoPeer::PROV_PERIODO_VALIDEZ_ID, $prov_periodo_validez_id);
		$parametros_consulta .= "&prov_periodo_validez_id=".$prov_periodo_validez_id;		
	}
    
    $this->periodo_validez = ProvPeriodoValidezPeer::retrieveByPK($prov_periodo_validez_id);
    
    $c->clearSelectColumns();    
    $c->addJoin(ProvEstadoDocPeer::PROV_ESTADO_DOC_ID,ProvDocumentoPeer::PROV_ESTADO_DOC_ID);
    $c->addJoin(ProvListaDocsPeer::PROV_LISTA_DOCS_ID,ProvDocumentoPeer::PROV_LISTA_DOCS_ID);    
        
	$c->addSelectColumn(ProvListaDocsPeer::NOMBRE);//4
	$c->addSelectColumn(ProvEstadoDocPeer::DESCRIPCION);//5
	$c->addSelectColumn(ProvDocumentoPeer::FECHA_CREACION);//6
    $c->addSelectColumn(ProvDocumentoPeer::FECHA_RECIBIDO);//8
	$c->addSelectColumn(ProvDocumentoPeer::OBSERVACIONES);//9
	$c->addSelectColumn(ProvDocumentoPeer::RUTA);//10
    
    $this->resultset = ProvDocumentoPeer::doSelectStmt($c);
            
  }
  
  public function executeShow($request)
  {
    $this->prov_documento = ProvDocumentoPeer::retrieveByPk($request->getParameter('prov_documento_id'));
    $this->forward404Unless($this->prov_documento);
  }

  public function executeCreate()
  { 
    $currentForm="prov_documento/create";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegioCerrar($currentForm);
    
    $this->prov_documento = new ProvDocumento();
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    $this->prov_periodo_validez_id=$prov_periodo_validez_id;
   
    //$this->form = new ProvDocumentoForm();
    //$this->setTemplate('edit');
  }

  public function executeEdit($request)
  { 
    $currentForm="prov_documento/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegioCerrar($currentForm);
    
    $this->prov_documento = ProvDocumentoPeer::retrieveByPk($request->getParameter('prov_documento_id'));
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    $this->prov_periodo_validez_id=$prov_periodo_validez_id;
    
  //  $this->form = new ProvDocumentoForm());
  }

  public function executeUpdate($request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new ProvDocumentoForm(ProvDocumentoPeer::retrieveByPk($request->getParameter('prov_documento_id')));

    $this->form->bind($request->getParameter('prov_documento'));
    if ($this->form->isValid())
    {
      $prov_documento = $this->form->save();

      $this->redirect('prov_documento/edit?prov_documento_id='.$prov_documento->getProvDocumentoId());
    }

    $this->setTemplate('edit');
  }
  
  public function executeUpdatecreate($request)
  {
    $prov_periodo_validez_id=$this->getRequestParameter('prov_periodo_validez_id');
    $prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPK($prov_periodo_validez_id);  
    if (!$this->getRequestParameter('prov_documento_id'))
    {
      $prov_documento = new ProvDocumento();
      $prov_documento->setFechaCreacion(date('Ymd'));
      $old_files = false;
      $txt_oldfiles = "";
    }
    else
    {
      $prov_documento = ProvDocumentoPeer::retrieveByPk($this->getRequestParameter('prov_documento_id'));
      $old_files = true;
      $txt_oldfiles = $prov_documento->getRuta();
      $this->forward404Unless($prov_documento);
    }
    //**************************************************************************************************
    $prov_documento->setProvPeriodoValidezId($prov_periodo_validez_id);
    $prov_documento->setProvEstadoDocId($this->getRequestParameter('prov_estado_doc_id'));
    $prov_documento->setProvListaDocsId($this->getRequestParameter('prov_lista_docs_id'));
    $prov_documento->setObservaciones($this->getRequestParameter('observaciones'));
    $prov_documento->setFechaRecibido($this->getRequestParameter('fecha_recibido'));
    //**************************************************************************************************
    $str_files = trim($this->getRequestParameter('ruta'));
    $prov_documento->setRuta($this->resolveUrlAttachment($str_files,$old_files,$txt_oldfiles));
    //**************************************************************************************************    
    $prov_documento->save();
    //**************************************************************************************************
    if($prov_documento->getPrimaryKey()){
        
        echo $msge="Este mensaje es para informarle que se adjunto el documento ".$prov_documento->getProvListaDocs()." al Proveedor ".$prov_periodo_validez->getProveedor()." para su revision en SIMAD WEB 4.0";        
        $this->enviarAlertas(11,$msge);
    }    
    $this->redirect('prov_documento/index?prov_periodo_validez_id='.$prov_periodo_validez_id);
    //**************************************************************************************************
  }
  
  public function enviarAlertas($item,$msge,$usuario_id=""){
    
      $cuser=new Criteria();
      $cuser->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,$item);//aprobadores de un area especifica
      if($usuario_id != ""){
        $cuser->add(ProvUsuarioAreaPeer::USUARIO_ID,$usuario_id);//enviar alerta a un usuario especifico
      }
      $cuser->add(ProvUsuarioAreaPeer::PROV_ESTADO_APROBADOR_ID,1);//1 es activo, 2 es inactivo
      $usuarios_email=ProvUsuarioAreaPeer::doSelect($cuser);   
      
      foreach($usuarios_email as $usuario_email)
      {
        //*********************************************************************************************************************
        $mail_destino = $usuario_email->getUsuario()->getEmail();
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('Area Proveedores : Aprobaciones');
        $baseMail->SetMsgHTML($msge);
        $baseMail->SetAddAddress($mail_destino, $mail_destino);    
        if($baseMail->InitSend() === true)
        {
           $baseMail->writetolog("Alerta enviada: " . $mail_destino . " Enviado a: " . $mail_destino);
        }else{
           $baseMail->writetolog("Error al enviar alerta: " . $mail_destino . " Cuenta correo: " . $mail_destino);
        }
        //***********************************************************************************************************************        
      }     
  }
  
  public function executeDelete($request)
  {
    $this->forward404Unless($prov_documento = ProvDocumentoPeer::retrieveByPk($request->getParameter('prov_documento_id')));

    $prov_documento->delete();

    $this->redirect('prov_documento/index');
  }
  
  public function executeFile()
  {
    $this->factura = ProvDocumentoPeer::retrieveByPk($this->getRequestParameter('prov_documento_id'));
  }
  
  public function executeUploads()
  {
    //*************************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
	$dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
    $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
    $entidad_text = $usuario->getRegional()->getEntidad()->getDirectorioName();
	//*************************************************************************************
    foreach ($this->getRequest()->getFiles() as $file)
    {
        //*********************************************************************************
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
        $datos_archivo = pathinfo($fileName);
 	    $this->fileNameOriginal  = trim($datos_archivo['filename']);
	}    	         
  }
  
  public function resolveUrlAttachment($files_new,$is_files_old=false,$files_old_text="")
  {
	$url_files = "";
	//*******************************************************************************
	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	$usuario_name    = $this->getUser()->getAttribute('username', '', 'subscriber');
	$dirRaiz         = ParametroPeer::retrieveByPk(9)->getValortexto();	
	$alias_object    = ParametroPeer::retrieveByPk(12)->getValortexto();
	$dirTmp          = ParametroPeer::retrieveByPk(65)->getValortexto();
    $dir_object      = "proveedores/";    
	//********************************************************************************
	$usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
	$entidad_folder  = $usuario->getRegional()->getEntidad()->getDirectorioName();
	$regional_folder = $usuario->getRegional()->getDirectorioName();
	$entidad_text    = $entidad_folder.'/'.$regional_folder;
	$directorio_entidad = $dirRaiz . $entidad_text;
	$directorio_tmp     = $dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp.DIRECTORY_SEPARATOR;
	$directorio_final   = $directorio_entidad.DIRECTORY_SEPARATOR.$dir_object;
	$dirextorio_alias   = $alias_object.$entidad_text.'/'.$dir_object;    
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
