<?php

/**
 * Subclass for representing a row from the 'COM_INTERNA' table.
 *
 * 
 *
 * @package lib.model
 */
include_once('lib/PHPOffice/PHPWord/bootstrap.php');

use \PhpOffice\PhpWord\Settings;
use \setasign\Fpdi\Fpdi;
use PhpOffice\PhpWord\TemplateProcessor;

class ComInterna extends BaseComInterna
{
    public function getPathImageDigitByCom(){
        try {
            $digitDocumentFile = "";		
            $dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
            $dir_adj_object  = ParametroPeer::retrieveByPk(15)->getValortexto();
            //$alias_com_object  = ParametroPeer::retrieveByPk(26)->getValortexto();
            $extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
            $periodo_com =  $this->getPeriodoId();
            //******************************************************************************************
            $directorio_raiz = $dirRaiz;
            $entidad_text = trim($this->getRegional()->getEntidad()->getDirectorioName());
            $regional_text = trim($this->getRegional()->getDirectorioName());
            $entidad_text = empty($entidad_text) ? "" : (empty($regional_text) ?  $entidad_text : $entidad_text.'/'.$regional_text);
            $directorio_entidad = empty($entidad_text) ? $directorio_raiz : $directorio_raiz.$entidad_text."/";
            $directorio_com = $dir_adj_object."/".$periodo_com."/";
            $directorio_final = $directorio_entidad.$directorio_com;
            $file_name = $this->getRadicado();
            //******************************************************************************************			
            foreach($extensions as $format){
                //echo $directorio_final.$file_name.'.'.$format;
                if(file_exists($directorio_final.$file_name.'.'.$format)){
                    $digitDocumentFile = $directorio_final.$file_name.'.'.$format;
                    break;
                }
            }
        } catch (Exception $th) {
            $digitDocumentFile = null;
        }
        //**********************************************************************************************
        return $digitDocumentFile;
    }

    public function getUriImageDigitById(){
        $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
        $base_web = sfConfig::get('base_simad');
        //**********************************************************************************************
        try {
            $file_name = $this->getRadicado();
            $digitDocumentFile = $this->getPathImageDigitByCom();
            $existe_file = empty($digitDocumentFile) ? false : true;
            //******************************************************************************************
            if($existe_file){
                $extension = pathinfo($digitDocumentFile, PATHINFO_EXTENSION);
                $filename_tmp = md5($file_name.time()).'.'.$extension;
                $pathtmp = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR . $filename_tmp;
                if (copy($digitDocumentFile, $pathtmp)) {
                    $url_viewer = $base_web.'/tmp/'.$filename_tmp;
                    if(strtolower($extension) == 'pdf'){
                        $url_viewer = $base_web.'/viewerEx.php?fileview='.$filename_tmp;
                        if($this->getEstadocominternaId() == 4){
                            $tanulado = "DOCUMENTO ANULADO";
                            $fanulado = $this->getFechaDeAnulacion();
                            $pdfTools = new PdfTools();
                            $fnewTmp = $pdfTools->setPdfWatherMark($pathtmp,$tanulado,$fanulado);
                            $url_viewer = $base_web.'/viewerEx.php?fileview='.$fnewTmp;
                        }
                        //*******************************************************************************
                        $response_process = array('status' => 200, 'message' => 'Archivo generado y enviado para visualización', 'url_file' => $url_viewer);
                    }else{
                        $response_process = array('status' => 200, 'message' => 'Archivo generado y enviado para visualización', 'url_file' => $url_viewer);
                    }
                }else{
                    $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
                }
            }else{
                $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
            }
        } catch (\Throwable $th) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
        }
        //************************************************************************************************
        return $response_process;
    }

    public function getUsuariosListCom()
    {
        $list_users = array();$copias_list = array();$firmas_list = array();
        foreach($this->getCominternaUsuarios() as $usuario_com){
            if($usuario_com->getRolusuariocominternaId() == 1){
                $list_users['radicador'] = $usuario_com->getUsuario()->getNombreAll();
            }elseif($usuario_com->getRolusuariocominternaId() == 2){
                $firmas_list[] = $usuario_com->getUsuario()->getNombreAll();
            }elseif($usuario_com->getRolusuariocominternaId() == 3){
                $copias_list[] = $usuario_com->getUsuario()->getNombreAll();
            }elseif($usuario_com->getRolusuariocominternaId() == 4){
                $list_users['destinatario'] = $usuario_com->getUsuario()->getNombreAll();               
            }
            //**************************************************************************
            $list_users['firmas'] = implode(",",$firmas_list);
            $list_users['copias'] = implode(",",$copias_list);
        }
        //******************************************************************************
        return $list_users;
    }
    
    /**
    * objectActions::getUsuariosListComObjs()
    * funcion que devuelve la informacion basica de los usuarios asignados a la comunicacion
    * @return mixed datos de los usuarios    
    */
    public function getUsuariosListComObjs()
    {
        $list_users = array();
        $copias_list = array();
        $firmas_list = array();
        $revisa_list = array();
        //******************************************************************************
        try {
            foreach ($this->getCominternaUsuarios() as $usuario_com) 
            {
                if ($usuario_com->getRolusuariocominternaId() == 1)//proyecta
                {
                    $list_users['nombre_creador'] = $usuario_com->getUsuario()->getNombreAll();
                    $list_users['area_ucreador'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
					//******************************************************************
                    if ($usuario_com->getUsuario()->getUseFirmaElectronica()) {
                        $list_users['ucreador_fmecanica'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    }
                } 
                elseif ($usuario_com->getRolusuariocominternaId() == 2)//firmas
                {
                    $usobject = array();
                    $usobject['ufirma_mecanica'] = "";
                    //******************************************************************
                    if ($usuario_com->getUsuario()->getUseFirmaElectronica() && $usuario_com->getCheckAprobacion()) 
                    {
                        $usobject['ufirma_mecanica'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    }
                    //******************************************************************
					$usobject['checkaprobado_ufirma'] = $usuario_com->getCheckAprobacion();
                    $usobject['nombre_ufirma'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_ufirma'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_ufirma'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_ufirma'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $firmas_list[] = $usobject;
                } 
                elseif ($usuario_com->getRolusuariocominternaId() == 3) //copias
                {
                    $usobject = array();
                    $usobject['nombre_copia'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_ucopia'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_ucopia'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_ucopia'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $copias_list[] = $usobject;
                }
                elseif ($usuario_com->getRolusuariocominternaId() == 4) //destinatario
                {
                    $list_users['nombre_destino'] = $usuario_com->getUsuario()->getNombreAll();
                    $list_users['cargo_udestino'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $list_users['area_udestino'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $list_users['regional_udestino'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $list_users['prefijo_udestino'] = $usuario_com->getUsuario()->getPrefijo();
                    $list_users['email_udestino'] = $usuario_com->getUsuario()->getEmail();
                    $list_users['nuid_udestino'] = $usuario_com->getUsuario()->getCedula();
                }
                elseif ($usuario_com->getRolusuariocominternaId() == 5) //revisores
                {
                    $usobject = array();
                    $usobject['nombre_urevisor'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_urevisor'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_urevisor'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_urevisor'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
					$usobject['checkaprobado_urevisor'] = $usuario_com->getCheckAprobacion();
					//******************************************************************
                    if ($usuario_com->getUsuario()->getUseFirmaElectronica() && $usuario_com->getCheckAprobacion()) {
                        $usobject['urevisor_fmecanica'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    }
					//******************************************************************
                    $revisa_list[] = $usobject;
                } 
                elseif ($usuario_com->getRolusuariocominternaId() == 6) //radicador
                {
                    $list_users['nombre_radicador'] = $usuario_com->getUsuario()->getNombreAll();
                    $list_users['area_uradicador'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
					//******************************************************************
                    if ($usuario_com->getUsuario()->getUseFirmaElectronica()) {
                        $list_users['fmecanica_uradicador'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    }
					//******************************************************************
                    $list_users['cargo_uradicador'] = $usuario_com->getCargousuarioId() ?  : "";
                    $list_users['area_uradicador'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $list_users['regional_uradicador'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                }
            }
        } catch (PropelException $th) {
            return array('error'=>$th->getMessage());
        } catch (\Exception $th) {
            return array('error'=>$th->getMessage());
		} catch (\Throwable $th) {
            return array('error'=>$th->getMessage());
        }
        //******************************************************************************
        $list_users['firmas'] = $firmas_list;
        $list_users['copias'] = $copias_list;
        $list_users['revisores'] = $revisa_list;
        //******************************************************************************
        return $list_users;
    }

    /**
    * objectActions::getUserIdComObjectByRol()
    * funcion que devuelve el o los ids de los usuarios asignados a la comunicacion 
    * @return mixed or single id
    * @rol_id parametro que especifica el usuario con el rol a retornar, el valor cero retorna todos los roles
    */
    public function getUserIdComObjectByRol($rol_id = 0)
    {
        $index = 0;
        $users_id = array();
        $object_usuarios = $this->getCominternaUsuarios();
        foreach ($object_usuarios as $object){
            if($object->getRolusuariocominternaId() == $rol_id){
                return $object->getUsuarioId();
            }elseif($rol_id == 0){
                $users_id[$index] = $object->getUsuarioId();
                $index++;
            }
        }
        return $users_id;
    }

	public function getUsuariosListComIds($isArray = false)
    {
        $list_users = array();$copias_list = array();$firmas_list = array();$destinatarios_list = array();
        foreach($this->getCominternaUsuarios() as $usuario_com){
            if($usuario_com->getRolusuariocominternaId() == 1){
                $list_users['radicador'] = $usuario_com->getUsuarioId();
            }elseif($usuario_com->getRolusuariocominternaId() == 2){
                $firmas_list[] = $usuario_com->getUsuarioId();
            }elseif($usuario_com->getRolusuariocominternaId() == 3){
                $copias_list[] = $usuario_com->getUsuarioId();
            }elseif($usuario_com->getRolusuariocominternaId() == 4){
                $destinatarios_list[] = $usuario_com->getUsuarioId();
            }
            //**************************************************************************
            $list_users['firmas'] = $isArray ? $firmas_list : implode(",",$firmas_list);
            $list_users['copias'] = $isArray ? $copias_list : implode(",",$copias_list);
            $list_users['destinatario'] = $isArray ? $destinatarios_list : implode(",",$destinatarios_list);
        }
        //******************************************************************************
        return $list_users;
    }
	
	public function getBasicUrlDigitCom($dir_raiz,$digit_com)
	{
		$array_url = array();
		//*****************************************************************************************
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//*****************************CREANDO ESTRUCTURA DE DIRECTORIOS***************************
        $folder_entidad = trim($this->getRegional()->getEntidad()->getDirectorioName());
        $folder_regional = trim($this->getRegional()->getDirectorioName());
        //*****************************************************************************************
        $periodo = $this->getPeriodoId();
		$entidad_text = $folder_entidad ? $folder_entidad : "";
		$entidad_text = $entidad_text ? ($folder_regional ? $entidad_text.DIRECTORY_SEPARATOR.$folder_regional : $entidad_text) : $folder_regional;
        $basic_path = $dir_raiz . $entidad_text .DIRECTORY_SEPARATOR. $digit_com .DIRECTORY_SEPARATOR. $periodo;
		//*****************************************************************************************
        $basic_path = preg_replace("#/+#",DIRECTORY_SEPARATOR,$basic_path);
		$array_url['storage_path'] = simad_util::createPath($basic_path);
		//*****************************************************************************************
		return $array_url;
	}
	
    public function getListDigitDocument()
	{
		$list_attach = array();
		//*****************************************************************************************
		$files_adjuntos = preg_split("/[,]+/",$this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
		//*****************************CREANDO LISTA DE ARCHIVOS***********************************
        for($j = 0; $j < count($files_adjuntos); $j++){
            if(trim($files_adjuntos[$j])){
                $filename = basename($files_adjuntos[$j]);
                $xguid = simad_util::create_guid($filename);
                $url_secure = $this->getUrlTokenViewImageByObject($xguid);
                $list_attach[] = array('GUID' => $xguid, 'NOMBRE_ARCHIVO' => $filename, 'TIPO_ATTACHMENT' => 'ANEXO', 'URL' => $url_secure);
            }
        }
		//******************************************************************************************
        $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
        //******************************************************************************************
        $dir_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
        $digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
        $storage_com = $this->getBasicUrlDigitCom($dir_raiz,$digit_dir);
        foreach ($mimetypes as $format) {
          $filename = sprintf("%s.%s",trim($this->getRadicado()),$format);
          $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
          if(file_exists($targetpath)){
            $xguid = simad_util::create_guid($filename);
            $url_secure = $this->getUrlTokenViewImageByObject($xguid);
			$url_download = $this->getUrlTokenDownloadImageByObject($xguid);
            $list_attach[] = array('GUID' => $xguid, 'NOMBRE_ARCHIVO' => $filename, 'TIPO_ATTACHMENT' => 'DIGIT_COM', 'URL' => $url_secure, 'URL_DOWNLOAD' => $url_download);
            break;
          }
        }
        //*******************************************************************************************
		return $list_attach;
	}

    public function getDigitDocumentByXguid($search_xguid)
	{
        try{
            $files_adjuntos = preg_split("/[,]+/",$this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
            //*****************************CREANDO LISTA DE ARCHIVOS***********************************
            for($j = 0; $j < count($files_adjuntos); $j++){
                if(trim($files_adjuntos[$j])){
                    $filename = basename(trim($files_adjuntos[$j]));
                    $xguid = simad_util::create_guid($filename);
                    if($search_xguid == $xguid){
                        return array('NOMBRE_ARCHIVO' => $filename, 'URL' => trim($files_adjuntos[$j]), 'TIPO_ATTACHMENT' => 'ANEXO');
                    }
                }
            }
            //******************************************************************************************
            $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
            //******************************************************************************************
            $dir_raiz = ParametroPeer::retrieveByPk(25)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
            $storage_com = $this->getBasicUrlDigitCom($dir_raiz,$digit_dir);
            foreach ($mimetypes as $format) {
                $filename = sprintf("%s.%s",trim($this->getRadicado()),$format);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                if(file_exists($targetpath)){
                    $xguid = simad_util::create_guid($filename);
                    if($search_xguid == $xguid){
                        return array('NOMBRE_ARCHIVO' => $filename, 'URL' => trim($targetpath), 'TIPO_ATTACHMENT' => 'DIGIT_COM');
                    }
                }
            }
            //*******************************************************************************************
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        }catch(Exception $ex){
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        }
	}

    public function getRadicadoFormat($entidad_id, $regional_id, $depen_codigo = null)
	{
		$entidad_object = trim($entidad_id) ? EntidadPeer::retrieveByPk(trim($entidad_id)) : null;
		//****************************************************************************************
		if($entidad_object != null){
			$entidad_code = trim($entidad_object->getCodigo()) ? trim($entidad_object->getCodigo())."-" : "";
		}
		//****************************************************************************************
        $numero_radicado = ComInternaPeer::getConsecutivoRadicacion($regional_id,$this->getDependenciaId(),$this->getPeriodoId());
		$num_consecutivo = sprintf("%07d",$numero_radicado);
        $this->setNumeroRadicacion($numero_radicado);        
		//****************************************************************************************
		//$radicado = $regional_cod."-".$depen_codigo."-".$num_consecutivo."-".date("Y")."-I";
		//$radicado = $regional_cod."-".$depen_codigo."-".$num_consecutivo."-"."I-".date("Y");
        //$radicado = sprintf("%s-%s-%s-1",date("Y"),$depen_codigo,$num_consecutivo);
        //$radicado = sprintf("%s-%s-%s-I-%s",$regional_cod,$depen_codigo,$num_radicado,date("Y"));
        $radicado = sprintf("%s-%s-3",date("Y"),$num_consecutivo);
		//****************************************************************************************
		return $radicado;
	}

    public function getUrlTokenViewImageByObject($xguid) {
		try{		
			if($this == null){ return null;}
			//*****************************************************************************************
			$time = time();
			$token_base = hash("sha512",$this->getPrimaryKey().$this->getFechaCreacion().$time);
			$mitad = strlen($token_base ) / 2;
			$parte1 = substr($token_base , 0, $mitad); 
			$parte2 = substr($token_base , $mitad);
			$token = $parte1.urlencode($time).$parte2;
			//*****************************************************************************************
            $base_path = sfConfig::get('base_simad');
            $url_query = array();
            $url_query['tipocom'] = 3;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;
            //$url_abs = urlencode($base_path."recibida.php/com_recibida/viewImage?key_id=".$this->getPrimaryKey()."&vtoken=".$token."&xguid=".$xguid);
            $url_abs = "http://".$_SERVER["HTTP_HOST"]."viewImage.php?".http_build_query($url_query);
			return ($url_abs);			
		}catch(Exception $ex){
			return null;
		}
	}    

	function getUrlTokenDownloadImageByObject($xguid) {
		try{		
			if($this == null){ return null;}
			//*****************************************************************************************
			$time = time();
			$token_base = hash("sha512",$this->getPrimaryKey().$this->getFechaCreacion().$time);
			$mitad = strlen($token_base ) / 2;
			$parte1 = substr($token_base , 0, $mitad); 
			$parte2 = substr($token_base , $mitad);
			$token = $parte1.urlencode($time).$parte2;
			//*****************************************************************************************
            $base_path = sfConfig::get('base_simad');
            $url_query = array();
            $url_query['tipocom'] = 3;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;
            $url_query['attachment'] = md5('attachment'.$this->getRadicado());
            //$url_abs = urlencode($base_path."recibida.php/com_recibida/viewImage?key_id=".$this->getPrimaryKey()."&vtoken=".$token."&xguid=".$xguid);
            //$url_abs = $base_path."viewImage.php?".http_build_query($url_query);
            $url_abs = "http://".$_SERVER["HTTP_HOST"]."/viewImage.php?".http_build_query($url_query);
			return ($url_abs);			
		}catch(Exception $ex){
			return null;
		}
	}

    public static function getRutaAdjuntos($files_new,$is_files_old=false,$files_old_text="")
    {
        $url_files = "";
        $util_simad = new simad_util();
        /********************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_name    = sfContext::getInstance()->getUser()->getAttribute('username', '', 'subscriber');
        $dirRaiz         = ParametroPeer::retrieveByPk(25)->getValortexto();
        $dir_object      = ParametroPeer::retrieveByPk(15)->getValortexto();
        $alias_object    = ParametroPeer::retrieveByPk(26)->getValortexto();
        $dirTmp          = ParametroPeer::retrieveByPk(65)->getValortexto();
        /********************************************************************************/
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
		$entidad_folder = trim($usuario->getRegional()->getEntidad()->getDirectorioName()) ? trim($usuario->getRegional()->getEntidad()->getDirectorioName()) : "";
		$regional_folder = trim($usuario->getRegional()->getDirectorioName()) ? trim($usuario->getRegional()->getDirectorioName()) : ""; 
		$entidad_text = trim($entidad_folder) ? (trim($regional_folder) ? "" : $regional_folder) : (trim($regional_folder) ? $entidad_folder : $entidad_folder.'/'.$regional_folder);
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object.'/'.($usuario_name);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp);
        $directorio_final = simad_util::NormalizePath($directorio_entidad.DIRECTORY_SEPARATOR.$directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text.'/'.$directorio_com;            
        /********************************************************************************/
        if(trim($is_files_old))
        {
        	$files_adjuntos = preg_split("/[,]+/",$files_old_text, -1, PREG_SPLIT_NO_EMPTY);
        	$files_text = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j < count($files_text); $j++){
        		$url = ComInterna::strpos_array($files_text[$j], $files_adjuntos);
        		if(!is_null($url)){
        			$url_files .= $url . ",";
        		}else{
        			$file_name = basename($files_text[$j]);
        			$filenamesource = $directorio_tmp . DIRECTORY_SEPARATOR . $file_name;        			
        			if(file_exists($filenamesource)){
        				$directorio_target = simad_util::createPath($directorio_final);
						$filenametarget = $directorio_target . DIRECTORY_SEPARATOR . $file_name;
        				copy($filenamesource, $filenametarget);
						if(file_exists($filenametarget)) { unlink($filenamesource); }
        				$url_files .= $dirextorio_alias . "/" . $file_name . ",";
        			}
        		}
        	}
        }
        else
        {
        	$files_adjuntos = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j <= count($files_adjuntos); $j++){
        		if(trim($files_adjuntos[$j])){
                    $file_name = basename($files_adjuntos[$j]);
        			$filenamesource = $directorio_tmp . DIRECTORY_SEPARATOR . $file_name;
        			if(file_exists($filenamesource)){
        				$directorio_target = simad_util::createPath($directorio_final);
						$filenametarget = $directorio_target . DIRECTORY_SEPARATOR . $file_name;
        				copy($filenamesource, $filenametarget);
						if(file_exists($filenametarget)) { unlink($filenamesource); }
                        $url_files .= $dirextorio_alias . "/" . $file_name . ",";
        			}
        		}
        	}
        }
        return $url_files;
    }
    
    public static function moveFilesWordTempalteCreate($files_new, $folder_compose = "", $files_old_text = "")
    {
        $url_files = "";
        /********************************************************************************/        
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_name = sfContext::getInstance()->getUser()->getAttribute('username', '', 'subscriber');
        $dirRaiz = ParametroPeer::retrieveByPk(25)->getValortexto();
        $dir_object  = ParametroPeer::retrieveByPk(15)->getValortexto();
        $alias_object  = "";//ParametroPeer::retrieveByPk(26)->getValortexto();
        $dirTmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
        /********************************************************************************/
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder.'/'.$regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object.'/'.$folder_compose . "/";
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp.DIRECTORY_SEPARATOR);
        $directorio_final = simad_util::NormalizePath($directorio_entidad.'/'.$directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text.'/'.$directorio_com;    
        /********************************************************************************/
        if(trim($files_old_text))
        {
        	$files_adjuntos = preg_split("/[,]+/",$files_old_text, -1, PREG_SPLIT_NO_EMPTY);
        	$files_text = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j < count($files_text); $j++){
        		$url = ComInterna::strpos_array($files_text[$j], $files_adjuntos);
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
        	$files_adjuntos = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j <= count($files_adjuntos); $j++){
        		if(trim($files_adjuntos[$j])){
        			$file_name = basename($files_adjuntos[$j]);
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
        return $url_files;
    }
    
    public static function strpos_array($haystack, $needles) {
        if ( is_array($needles) ) {
        	foreach ($needles as $str) {
        		if ( is_array($str) ) {
        			$pos = ComInterna::strpos_array($haystack, $str);
        		} else {
        			$pos = strpos($haystack, basename($str));
        		}
        		if ($pos !== FALSE) {
        			return $str;
        		}
        	}
        } else {
        	return strpos($haystack, basename($needles));
        }
    }
    
    public static function createPath($cadena)
	{
   	    return simad_util::createPath($cadena);
   	}//function createPath

    public function readTemplateData()
    {
        $path_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates";
        $name_plantilla = $this->getTipoComInterna()->getPlantillasCom()->getNombre();
        $fullpath_plantilla = $path_plantilla.DIRECTORY_SEPARATOR.$name_plantilla;
        //**********************************************************************************
        $contenido_plantilla = $this->getTipoComInterna()->getPlantillasCom()->getContents();
    }

    /**
     * objectActions::addNewAttachDocument()
    * funcion para adjuntar un nuevo archivo al registro de la comunicacion
    * @return mixed resultado proceso array('isError' => true|false, list_files => array('filename' => $file, 'info' => xxx, 'isError' => true|false))
    * @files_new mixed lista con ruta de los arhivos adjuntos
    * @userid_attach int usuario id que adjunta el documento
    */
    public function addNewAttachDocument($files_new = array(),$userid_attach = null)
    {
        $current_ruta = !empty($this->getRuta()) ? trim($this->getRuta()) : "";
        $current_files = preg_split("/[,]+/",$current_ruta, -1, PREG_SPLIT_NO_EMPTY);
        //********************************************************************************
        $rolu_proyecta = 1;
        $usuario_attach = !empty($userid_attach) ? UsuarioPeer::retrieveByPK($userid_attach) : null;
        $usuario = empty($usuario_attach) ? UsuarioPeer::retrieveByPK($this->getUserIdComObjectByRol($rolu_proyecta)) : $usuario_attach;
        $usuario_name    = $usuario->getUserName();
        //********************************************************************************
        $simad_util   = new simad_util();
        $dirRaiz      = ParametroPeer::retrieveByPk(25)->getValortexto();
        $dir_object   = ParametroPeer::retrieveByPk(15)->getValortexto();
        $alias_object = ParametroPeer::retrieveByPk(26)->getValortexto();
        //$dirTmp       = ParametroPeer::retrieveByPk(65)->getValortexto();
        //********************************************************************************
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder.'/'.$regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object.'/'.($usuario_name);  	
        //$directorio_tmp = ComEnviada::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp.DIRECTORY_SEPARATOR);
        $directorio_final = ComEnviada::NormalizePath($directorio_entidad.'/'.$directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text."/".$directorio_com;
        //********************************************************************************
        $isError = false;
        $list_state = array();
        //********************************************************************************
        if(count($files_new)){
            foreach($files_new as $nfile){
                try{            
                    if(!empty($nfile)){
                        $info_file = new SplFileInfo($nfile);
                        $filename_new = uniqid().'_'.$simad_util->clean_name_fileinfo($info_file);
                        //*********************************************************************
                        $path_source = $info_file->getRealPath();
                        if(file_exists($path_source)){
                            $path_target = simad_util::createPath($directorio_final). DIRECTORY_SEPARATOR. $filename_new;
                            if(copy($path_source, $path_target)){
                                $current_files[] = $dirextorio_alias . "/" . $filename_new;
                                $list_state[] = array('filename' => $nfile, 'info' => 'El archivo se adjunto correctamente', 'isError' => false);
                                unlink($path_source);
                            }else{
                                $isError = true;
                                $list_state[] = array('filename' => $nfile, 'info' => 'No fue posible copiar el archivo en la carpeta final');
                            }
                        }else{
                            $isError = true;
                            $list_state[] = array('filename' => $nfile, 'info' => 'el archivo no se encontro en el servidor', 'isError' => true);
                        }
                    }else{
                        $isError = true;
                        $list_state[] = array('filename' => $nfile ? $nfile : 'null', 'info' => 'nombre del archivo no es valido o es nulo', 'isError' => true);
                    }
                }catch(Exception $ex){
                    $isError = true;
                    $list_state[] = array('filename' => $nfile, 'info' => $ex->getMessage(), 'isError' => true);
                }
            }
        }else{
            $isError = true;
        }
        //********************************************************************************
        $this->setRuta(implode(",",$current_files));
        $this->save();
        //********************************************************************************
        return array('isError' => $isError, 'list_state' => $list_state);
    }

    public function genPdfByPlantillaComOld()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_solicita  = UsuarioPeer::retrieveByPK($usuariologuiado);
        //************************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR**********************
        $base_path = sfConfig::get('base_simad');
        $path_tmp = sfConfig::get('sf_web_dir')."/com_html/com_interna/";//directorio temporal para guardar los archivos a convertir a pdf
        if (!is_dir($path_tmp)) {//verificar si el directorio existe de lo contrario se crea
        try {            
                @mkdir(($path_tmp),0766,true);//crea el directorio destiono
        } catch (Exception $e) {
                echo 'Excepción: ',  $e->getMessage(), "\n";
        }
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey().".php";//nombre del archivo temporal que contiene los datos a convertir
        $path_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates";
        $name_plantilla = $this->getTipoComInterna()->getPlantillasCom()->getNombre();
        $fullpath = $path_plantilla.DIRECTORY_SEPARATOR.$name_plantilla;
        //**************************************************************************************************
        $path_logo = sfConfig::get('base_simad') . "/images/encabezado_carta/logos_carnet";
        $logo_empresa = $this->getRegional()->getEntidad()->getLogoCorporativo();
        //**************************************************************************************************
        if(file_exists($path_tmp.$nomb_file_html)){//verificar si ya esta generado el archivo a convertir
            unlink($path_tmp.$nomb_file_html);//se elimina para actualizar el contenido del documento
        }
        $pt = fopen($path_tmp.$nomb_file_html, 'w');//se crea el archivo a convertir
        //**************************************************************************************************
        //firmantes
        $cd = new Criteria();
        $cd->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cd->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
        $cominterna_usuario_firma = CominternaUsuarioPeer::doSelect($cd);
        //**************************************************************************************************
        $plantilla_contents = file_get_contents($fullpath);      
        //**************************************PATRONES PARA REMPLAZAR LOS VALORES*************************
        $patrones = array();
        $patrones[0]  = '#.#$fecha_carta#.#';        
        $patrones[1]  = '#.#$tipo_comunicacion#.#';
        $patrones[2]  = '#.#$contenido_carta#.#';
        $patrones[3]  = '#.#$firmas_comunicacion#.#';
        $patrones[4]  = '#.#$logo_empresa#.#';
        $patrones[5]  = '#.#$asunto_com#.#';
        //**************************************************************************************************
        $ciudad_carta = $this->getRegional()->getCiudad()->getNombre();
        //$fecha_carta = date('d \of F \of Y',strtotime($com_interna->getFechaCreacion()));
        setlocale(LC_TIME, "spanish");
        //$fecha_carta = strftime("%A, %d de %B de %Y",strtotime($com_interna->getFechaCreacion()));
        $fecha_carta = strftime("%d de %B de %Y",strtotime($this->getFechaCreacion()));
        //**************************************************************************************************
        $sustituciones    = array();
        $sustituciones[0] = sprintf("%s, %s",$ciudad_carta,$fecha_carta);
        $sustituciones[1] = $this->getTipoComInterna()->getDescripcion();
        $sustituciones[2] = $this->getContenido();
        //**************************************************************************************************
        $firma_nombres = array();$firma_cargos = array();$firma_area = array();$firma_mecanica = array();
        foreach ($cominterna_usuario_firma as $cominterna_usuario_firmaOne){
            if(trim($this->getFirmaElectronica())){
                if(trim($cominterna_usuario_firmaOne->getUsuario()->getFirmaElectronica())){
                    $firma_mecanica[] = '<img src="'.trim($cominterna_usuario_firmaOne->getUsuario()->getFirmaElectronica()).'" style="min-height: 80px;max-height: 100px;min-width: 100px;max-width: 200px;">';
                }else{
                    $firma_mecanica[] = '&nbsp;';
                }
            }else{
                $firma_mecanica[] = '&nbsp;';
            }
            //**********************************************************************************************
            $firma_nombres[] =  $cominterna_usuario_firmaOne->getUsuario()->getNombreApellido();
            $firma_cargos[] = ($cominterna_usuario_firmaOne->getCargoUsuario()->getCargo()->getDescripcion());
            $firma_area[] = $cominterna_usuario_firmaOne->getUsuario()->getDependencia()->getNombre();
        }
        //**************************************************************************************************
        $dataFirmas['firma_nombres'] = $firma_nombres;
        $dataFirmas['firma_cargos'] = $firma_cargos;
        $dataFirmas['firma_areas'] = $firma_area;
        $dataFirmas['firma_mecanica'] = $firma_mecanica;
        //**************************************************************************************************    
        $firmas = '<table border="0" width="100%" align="left">';
        $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
        $contFirmas = 0;$contCopias = 0;$contCargos = 0;
        //**************************************************************************************************
        foreach($dataFirmas['firma_nombres'] as $row_firma){
            if($contFirmas > 0){
                if($contFirmas%2 == 0){
                    //print_r($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas,false);
                    $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
                    $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
                    $firmas .= $this->getDataEmptyRowFirmas(3);
                }
            }
            //**********************************************************************************************
            if($dataFirmas['firma_mecanica'][$contFirmas] != null){
                $mecanica_firmas[] = $dataFirmas['firma_mecanica'][$contFirmas];
            }
            //**********************************************************************************************
            $usuario_firmas[] = '<td><b>'.$row_firma.'</b></td>';
            $cargos_firmas[] = $dataFirmas['firma_cargos'][$contFirmas];
            $areas_firmas[] = $dataFirmas['firma_areas'][$contFirmas];		
            $contFirmas++;
        }
        //**************************************************************************************************
        if(count($cargos_firmas)){	    
            $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas,false);
            $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
            $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
        }
        //**************************************************************************************************
        $firmas .= '</table>';
        $sustituciones[3] = $firmas;
        //**************************************************************************************************
        $sustituciones[4] = sprintf("%s/%s",$path_logo,$logo_empresa);
        $sustituciones[5] = $this->getReferencia();
        //***************************************** REMPLAZAR DATOS ****************************************
        $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents);
        //********************************************GUARDAR LA PLANTILLA TEMPORAL*************************
        // Guarda el RTF generado
        simad_util::createPath($path_tmp);    
        file_put_contents($path_tmp.DIRECTORY_SEPARATOR.$nomb_file_html,$template_contents);
        //**************************************************************************************************
        //MARGENES DE IMPRESION
        $params_margin  = "&top=25";
        $params_margin .= "&left=25";
        $params_margin .= "&buttom=20";
        $params_margin .= "&rigth=25";
        
    }

    /**
     * objectActions::genPdfByPlantillaCom()
     * realiza la combinacion de correspondencia segun los campos configurados en la plantilla    
     * @return void
     */
    public function genPdfByPlantillaCom()
    {
        require_once(sfConfig::get('sf_lib_dir')."/BarcodeGenerator/generate_barcode.php");
        //***************************************************************************************************
        //$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
    	//$usuario_solicita  = UsuarioPeer::retrieveByPK($usuariologuiado);
        //************************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR**********************
        $base_path = sfConfig::get('base_simad');
        $path_tmp = sfConfig::get('sf_web_dir')."/com_html/com_interna/";//directorio temporal para guardar los archivos a convertir a pdf
        if (!is_dir($path_tmp)) {//verificar si el directorio existe de lo contrario se crea
            try {            
                    @mkdir(($path_tmp),0766,true);//crea el directorio destiono
            } catch (Exception $e) {
                    echo 'Excepción: ',  $e->getMessage(), "\n";
            }
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey().".php";//nombre del archivo temporal que contiene los datos a convertir        
    	//**************************************************************************************************
        if(file_exists($path_tmp.$nomb_file_html)){//verificar si ya esta generado el archivo a convertir
            unlink($path_tmp.$nomb_file_html);//se elimina para actualizar el contenido del documento
        }
        //**************************************************************************************************
        $template_contents = $this->setMergeFiledsCom();
        //********************************************GUARDAR LA PLANTILLA TEMPORAL*************************
        // Guarda el RTF generado
        simad_util::createPath($path_tmp);
        file_put_contents($path_tmp . DIRECTORY_SEPARATOR . $nomb_file_html, $template_contents);
        //**************************************************************************************************
        return $nomb_file_html;
    }

    /**
     * objectActions::setMergeFiledsCom()
     * realiza la combinacion de correspondencia segun los campos configurados en la plantilla    
     * @return void
     */
    public function setMergeFiledsCom()
    {
        try {
            //$fields_cods = PlantillasComPeer::getFieldsCodByTemplate();
            $userscom_data = $this->getUsuariosListComObjs();
            $contenido_merge = $this->getContenido();
            $base_path = sfConfig::get('base_simad');
            //*******************************************************************************************************
            $lstfirmas = $userscom_data['firmas'];
            $unfirma = "";
            $ufcargo = "";
            $ufarea = "";
            $ufmecanica = "";
            $ufregional = "";
            foreach ($lstfirmas as $key => $value) {
                $unfirma = $value['nombre_ufirma'];
                $ufcargo = $value['cargo_ufirma'];
                $ufarea = $value['area_ufirma'];
                $ufregional = $value['regional_ufirma'];
				if($this->getEstadocominternaId() != 1)
					$ufmecanica = '<img src="' . trim($value['ufirma_mecanica']) . '" min-height="80px" max-height="150px" width="250px">';
			}
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[FIRMAS_NOMBRE]}", $unfirma, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_CARGO]}", $ufcargo, $contenido_merge);
			$contenido_merge = str_replace("{[FIRMAS_CARGOS]}", $ufcargo, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_DEPENDENCIA]}", $ufarea, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMA_MECANICA]}", $ufmecanica, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_REGIONAL]}", $ufregional, $contenido_merge);
            //*******************************************************************************************************
			if(isset($userscom_data['nombre_destino']))
			{	
				$contenido_merge = str_replace("{[DESTINO_PREFIJO]}", trim($userscom_data['prefijo_udestino']), $contenido_merge);
				//***************************************************************************************************
				if(strpos($contenido_merge,"{[DESTINO_FUNCIONARIO]}") !== false)
					$contenido_merge = str_replace("{[DESTINO_FUNCIONARIO]}", trim($userscom_data['nombre_destino']), $contenido_merge);
				else
					$contenido_merge = str_replace("{[DESTINO_NOMBRE]}", trim($userscom_data['nombre_destino']), $contenido_merge);
				//***************************************************************************************************
				$contenido_merge = str_replace("{[DESTINO_EMAIL]}", trim($userscom_data['email_udestino']), $contenido_merge);
				$contenido_merge = str_replace("{[DESTINO_IDENTIFICACION]}", trim($userscom_data['nuid_udestino']), $contenido_merge);
				$contenido_merge = str_replace("{[DESTINO_CARGO]}", trim($userscom_data['cargo_udestino']), $contenido_merge);
			}
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[RADICADOR_NOMBRE]}", $userscom_data['nombre_radicador'], $contenido_merge);
            $contenido_merge = str_replace("{[RADICADOR_DEPENDENCIA]}", $userscom_data['area_uradicador'], $contenido_merge);
			if(strpos($contenido_merge,"{[RADICADOR_FMECANICA]}") !== false){
				$uradica_fmecanica = "";
				if(isset($userscom_data['fmecanica_uradicador'])){
					$uradica_fmecanica = '<img src="' . trim($userscom_data['fmecanica_uradicador']) . '" min-height="40px" max-height="60px" width="60px">';
				}
				//***************************************************************************************************
				$contenido_merge = str_replace("{[RADICADOR_FMECANICA]}", $uradica_fmecanica, $contenido_merge);
			}
			//*******************************************************************************************************
            $contenido_merge = str_replace("{[UPROYECTA_NOMBRE]}", $userscom_data['nombre_creador'], $contenido_merge);
            $contenido_merge = str_replace("{[UPROYECTA_AREA]}", $userscom_data['area_ucreador'], $contenido_merge);
			if(strpos($contenido_merge,"{[UPROYECTA_FMECANICA]}") !== false){
				$uproyecta_fmecanica = "";
				if(isset($userscom_data['ucreador_fmecanica'])){
					$uproyecta_fmecanica = '<img src="' . trim($userscom_data['ucreador_fmecanica']) . '" min-height="40px" max-height="60px" width="60px">';
				}
				//***************************************************************************************************
				$contenido_merge = str_replace("{[UPROYECTA_FMECANICA]}", $uproyecta_fmecanica, $contenido_merge);
			}
            //*******************************************************************************************************
            $lstcopias = $userscom_data['copias'];
            $uncopia = "";
            $uccargo = "";
            $ucarea = "";
            foreach ($lstcopias as $key => $uccopia) {
                $uncopia = $uccopia['nombre_ucopia'];
                $uccargo = $uccopia['cargo_ucopia'];
                $ucarea = $uccopia['area_ucopia'];
				
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[COPIAS_NOMBRE]}", $uncopia, $contenido_merge);
            $contenido_merge = str_replace("{[COPIAS_CARGO]}", $uccargo, $contenido_merge);
            $contenido_merge = str_replace("{[COPIAS_DEPENDENCIA]}", $ucarea, $contenido_merge);
            //***********************************INICIA SECCION DE REVISORES*****************************************
            $lstrevisores = $userscom_data['revisores'];
			$unrevisa = ""; $urcargo = ""; $urarea = ""; $urevisa_fmecanica = "";
            //*******************************************************************************************************
            $revisor_fields_merge = array();
            if(strpos($contenido_merge,"{[REVISOR_NOMBRE]}") !== false)
                $revisor_fields_merge[] = "{[REVISOR_NOMBRE]}";
            if(strpos($contenido_merge,"{[REVISOR_CARGO]}") !== false)
                $revisor_fields_merge[] = "{[REVISOR_CARGO]}";
            if(strpos($contenido_merge,"{[REVISOR_DEPENDENCIA]}") !== false)
                $revisor_fields_merge[] = "{[REVISOR_DEPENDENCIA]}";
            if(strpos($contenido_merge,"{[REVISOR_FMECANICA]}") !== false)
                $revisor_fields_merge[] = "{[REVISOR_FMECANICA]}";
            //*******************************************************************************************************
            if(count($lstrevisores) > 1)
            {
                $arrayHtml = array();
                foreach ($lstrevisores as $value) 
                {
                    $data_list_com = array();
                    if(in_array("{[REVISOR_NOMBRE]}",$revisor_fields_merge))
                        $data_list_com['nombre_urevisor'] = $value['nombre_urevisor'];
                    if(in_array("{[REVISOR_CARGO]}",$revisor_fields_merge))
                        $data_list_com['cargo_urevisor'] = $value['cargo_urevisor'];
                    if(in_array("{[REVISOR_DEPENDENCIA]}",$revisor_fields_merge))
                        $data_list_com['area_urevisor'] = $value['area_urevisor'];
                    if(in_array("{[REVISOR_FMECANICA]}",$revisor_fields_merge))
                        $data_list_com['urevisor_fmecanica'] = $value['urevisor_fmecanica'];
                    //***********************************************************************************************
                    $arrayHtml[] = $data_list_com;
                }
                //***************************************************************************************************
                $tablaFinal = simad_util::generateTablaHtml($arrayHtml);
                //***************************************************************************************************
                $contenido_merge = str_replace("{[REVISOR_NOMBRE]}", $tablaFinal, $contenido_merge);
            }
            else
            {
                foreach ($lstrevisores as $key => $value) 
                {
                    $unrevisa = $value['nombre_urevisor'];
                    $urcargo = $value['cargo_urevisor'];
                    $urarea = $value['area_urevisor'];
                    $urregional = $value['regional_urevisor'];
                    //***********************************************************************************************
                    $urevisa_fmecanica = '';
                    if(!empty(trim($value['urevisor_fmecanica'])))
                        $urevisa_fmecanica = '<img src="' . trim($value['urevisor_fmecanica']) . '" style="vertical-align:top;display:inline-block;height:20px;width:40px;">';
                }
                //***************************************************************************************************
                $contenido_merge = str_replace("{[REVISOR_NOMBRE]}", $unrevisa, $contenido_merge);
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[REVISOR_CARGO]}", $urcargo, $contenido_merge);
            $contenido_merge = str_replace("{[REVISOR_DEPENDENCIA]}", $urarea, $contenido_merge);
			if(strpos($contenido_merge,"{[REVISOR_FMECANICA]}") !== false){
				$contenido_merge = str_replace("{[REVISOR_FMECANICA]}", $urevisa_fmecanica, $contenido_merge);
			}
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[ANEXOS_COM]}", trim($this->getAnexos()), $contenido_merge);
            $contenido_merge = str_replace("{[FECHA_COM]}", trim($this->getFechaCreacion()), $contenido_merge);
            $contenido_merge = str_replace("{[ASUNTO_COM]}", trim($this->getReferencia()), $contenido_merge);
            $contenido_merge = str_replace("{[RADICADO_COM]}", trim($this->getRadicado()), $contenido_merge);
            $contenido_merge = str_replace("{[CODEBAR_COM]}", '<img src="'.$base_path.'/tmp/'.$this->generateCodeBarInFile(true).'"/>', $contenido_merge);
            //********************************definir font para el documento*****************************************
            $struct_init = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title>
            <style type="text/css">body {font-family:Work Sans,sans-serif;font-size: 12pt;}</style></head><body>';
            //*******************************************************************************************************
            $htmlfooter = '<pd4ml:page.footer>    
            <div width="100%" style="text-align:right;font-size: 6pt;">Pagina $[page] de $[total]</div>
            </pd4ml:page.footer>';
            //*******************************************************************************************************
            $struct_end = '</body></html>';
            //*******************************************************************************************************
            $struct_main = $struct_init.$htmlfooter.$contenido_merge.$struct_end;
            //*******************************************************************************************************
            return $struct_main;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function generateDocPdf()
    {
        //************************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR*********************
        $base_path = sfConfig::get('base_simad');
        $dir_tmp=sfConfig::get('sf_web_dir')."/com_html/com_interna/";//directorio temporal para guardar los archivos a convertir a pdf
        if (!is_dir($dir_tmp)) {//verificar si el directorio existe de lo contrario se crea
        try {            
                @mkdir(($dir_tmp),0766,true);//crea el directorio destiono
        } catch (Exception $e) {
                echo 'Excepción: ',  $e->getMessage(), "\n";
        }
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey().".php";//nombre del archivo temporal que contiene los datos a convertir
        //**************************************************************************************************
        if(file_exists($dir_tmp.$nomb_file_html)){//verificar si ya esta generado el archivo a convertir
            unlink($dir_tmp.$nomb_file_html);//se elimina para actualizar el contenido del documento
        }
        $pt = fopen($dir_tmp.$nomb_file_html, 'w');//se crea el archivo a convertir
        //**************************************************************************************************
        //destinatario
        $cD = new Criteria();
        $cD->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cD->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 4);
        $cominterna_usuario_destino = CominternaUsuarioPeer::doSelectOne($cD);
        //**************************************************************************************************
        //copias
        $cc = new Criteria();
        $cc->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cc->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 3);
        $cominterna_usuario_copia = CominternaUsuarioPeer::doSelect($cc);
        //**************************************************************************************************
        //firmantes
        $cf=new Criteria();
        $cf->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cf->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
		$cf->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
        $cominterna_usuario_firma = CominternaUsuarioPeer::doSelect($cf);
        //**************************************************************************************************
        //creador proyecto
        $cp=new Criteria();
        $cp->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cp->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 1);
        $cominterna_usuario_proyecto = CominternaUsuarioPeer::doSelectOne($cp);
        //**************************************************************************************************
        //revisores
        $cr=new Criteria();
        $cr->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getCominternaId());
        $cr->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 5);
        $cr->add(CominternaUsuarioPeer::CHECK_APROBACION, 1);
        $cominterna_usuario_revision = CominternaUsuarioPeer::doSelect($cr);
        //**************************************************************************************************
        //aprobadores estado item aprobado
        $cap=new Criteria();
        $cap->add(ComAprobacionPeer::CONSECUTIVO_ID,$this->getPrimaryKey());
        $cap->add(ComAprobacionPeer::MODULO_ID, 2);
        $cap->add(ComAprobacionPeer::ESTADOCOMAPROBACION_ID, 2);//estado id aprobado
        $usuarios_aprobadores = ComAprobacionPeer::doSelect($cap);
        //**************************************************************************************************
        $is_borrador = false;
        $dir_regional = $this->getRegional()->getDireccion();    
        if(1 == $this->getEstadocominternaId()){
            $correspondenciaEnBorrador="BORRADOR DE ";
            $is_borrador = true;
        }else{
            $correspondenciaEnBorrador = " &nbsp;";
        }
        //**************************************************************************************************
        $ciudadOrigen = $this->getRegional()->getCiudad()->getNombre();
        $FechaMensaje = $this->getFechaCreacion();
        if(empty($FechaMensaje)){ $FechaMensaje = date("Y-m-d h:m:s"); }
        //**************************************************************************************************
        if (($timestamp = strtotime($FechaMensaje)) == -1) {
            echo "La cadena ($FechaMensaje) no es v&aacute;lida.";
        }else{
            $fecha_carta=date("d",$timestamp);
            $fecha_carta.=" de ";
            $mes=date( "n",$timestamp );
            switch($mes){
                case 1: {$fecha_carta.= "Enero"; break;}
                case 2: {$fecha_carta.= "Febrero"; break;}
                case 3: {$fecha_carta.= "Marzo"; break;}
                case 4: {$fecha_carta.= "Abril"; break;}
                case 5: {$fecha_carta.= "Mayo"; break;}
                case 6: {$fecha_carta.= "Junio"; break;}
                case 7: {$fecha_carta.= "Julio"; break;}
                case 8: {$fecha_carta.= "Agosto"; break;}
                case 9: {$fecha_carta.= "Septiembre"; break;}
                case 10: {$fecha_carta.= "Octubre"; break;}
                case 11: {$fecha_carta.= "Noviembre"; break;}
                case 12: {$fecha_carta.= "Diciembre"; break;}
            }
            $fecha_carta.= " de ";
            $fecha_carta.= date( "Y ",$timestamp );
        }
        //**************************************************************************************************
        $destinatario = $cominterna_usuario_destino->getUsuario()->getFullNombre();	
        if($cominterna_usuario_destino->getUsuario()->getPrefijo() != ""){
            $tratamiento = $cominterna_usuario_destino->getUsuario()->getPrefijo();
        }else{
            $tratamiento="Se&ntilde;or(a):";
        }
        //**************************************************************************************************
        //$tratamiento=utf8_encode($cominterna_usuario_destino->getUsuario()->getPrefijo());
        $cargoDestinatario = $cominterna_usuario_destino->getCargoUsuario()->getCargo()->getDescripcion();
        $dependenciaDestinatario = $cominterna_usuario_destino->getUsuario()->getDependencia()->getNombre();
        $regionalDestinatario = $cominterna_usuario_destino->getUsuario()->getRegional()->getDescripcion();	
        $asunto = trim($this->getReferencia());
        $mensaje = trim($this->getContenido());
        //**************************************************************************************************
        $firma_nombres = array();$firma_cargos = array();$firma_area = array();$firma_mecanica = array();
        foreach ($cominterna_usuario_firma as $cominterna_usuario_firmaOne){
            if(trim($this->getFirmaElectronica()) && !$is_borrador){
                if(trim($cominterna_usuario_firmaOne->getUsuario()->getFirmaElectronica())){
                    //$firma_mecanica[] = '<img style="margin-top: -60px;position: absolute;" src="'.trim($cominterna_usuario_firmaOne->getUsuario()->getFirmaElectronica()).'" min-height="80px" max-height="100px" min-width="100px" max-width="120px">';
					$firma_mecanica[] = '<img src="'.trim($cominterna_usuario_firmaOne->getUsuario()->getFirmaElectronica()).'" style="min-height: 80px;max-height: 100px;min-width: 100px;max-width: 200px;">';
                }else{
                    $firma_mecanica[] = '&nbsp;';
                }
            }else{
                $firma_mecanica[] = '&nbsp;';
            }
            //**********************************************************************************************
            $firma_nombres[] =  $cominterna_usuario_firmaOne->getUsuario()->getFullNombre();
            $firma_cargos[] = ($cominterna_usuario_firmaOne->getCargoUsuario()->getCargo()->getDescripcion());
            $dependencia_id = $cominterna_usuario_firmaOne->getCargoUsuario()->getDependenciaId();

            if(!empty($dependencia_id)){
                $area_encargo = DependenciaPeer::retrieveByPK($dependencia_id);
                $firma_area[] = $area_encargo != null ? $area_encargo->getNombre() : $cominterna_usuario_firmaOne->getUsuario()->getDependencia()->getNombre();
            }else{
                $firma_area[] = $cominterna_usuario_firmaOne->getUsuario()->getDependencia()->getNombre();
            }
        }
        //**************************************************************************************************
        $dataFirmas['firma_nombres'] = $firma_nombres;
        $dataFirmas['firma_cargos'] = $firma_cargos;
        $dataFirmas['firma_areas'] = $firma_area;
        $dataFirmas['firma_mecanica'] = $firma_mecanica;
        //**************************************************************************************************    
        $firmas = '<br /><table border="0" cellspacing="1" cellpadding="1" width="99%" align="left">';
        $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
        $contFirmas = 0;$contCopias = 0;$contCargos = 0;
        //$firmas .= '<tr>';
        //**************************************************************************************************
        foreach($dataFirmas['firma_nombres'] as $row_firma){
            if($contFirmas > 0){
                if($contFirmas%2 == 0){
                    //print_r($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas,false);
                    $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
                    $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
                    $firmas .= $this->getDataEmptyRowFirmas(3);
                }
            }
            //**********************************************************************************************
            if($dataFirmas['firma_mecanica'][$contFirmas] != null){
                $mecanica_firmas[] = $dataFirmas['firma_mecanica'][$contFirmas];
            }
            //**********************************************************************************************
            $usuario_firmas[] = '<td><b>'.$row_firma.'</b></td>';
            $cargos_firmas[] = $dataFirmas['firma_cargos'][$contFirmas];
            $areas_firmas[] = $dataFirmas['firma_areas'][$contFirmas];		
            $contFirmas++;
        }
        //**************************************************************************************************
        if(count($cargos_firmas)){	    
            $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas,false);
            $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
            $usuario_firmas = array();$cargos_firmas = array();$areas_firmas = array();$mecanica_firmas = array();
        }
        //**************************************************************************************************
        $firmas .= '</table><br/>';
        //*********************************************************************************************
        $iterator= 0;
        $is_copias_html = false;
        $copias_html = '<br/><br/><table width="100%" border="0" cellspacing="0" cellpadding="0">';
        foreach ($cominterna_usuario_copia as $cominterna_usuario_copiaOne){
            $usuario_name = $cominterna_usuario_copiaOne->getUsuario()->getFullNombre();
            $cargo_usuario = trim($cominterna_usuario_copiaOne->getCargoUsuario()->getCargo());
            $prefijo_usuario = trim($cominterna_usuario_copiaOne->getUsuario()->getPrefijo());
            $struct_copia_interna = ($prefijo_usuario).' - '.($usuario_name);
            //*****************************************************************************************
            if(($cargo_usuario)){
                $struct_copia_interna .= ' - '.$cargo_usuario;
            }
            //*****************************************************************************************
            if($iterator == 0){
                $copias_html .= '<tr><td style="width:100px; text-align:left; font-size: 8pt;"><b>Copia Interna:</b></td>';
                $copias_html .= '<td style="text-align:left; font-size: 8pt;">'.($struct_copia_interna).'</td></tr>';
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($usuario_name).'</td></tr>';    		
            }else{
                $copias_html .= '<tr><td style="width:55px; text-align:left; font-size: 8pt;">&nbsp;</td>';
                $copias_html .= '<td style="text-align:left; font-size: 8pt;">'.($struct_copia_interna).'</td></tr>';
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($usuario_name).'</td></tr>';
            }
            //*****************************************************************************************
            if(trim($cargo_usuario)){
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($cargo_usuario).'</td></tr>';
            }
            //*****************************************************************************************
            $is_copias_html = true;
            $iterator++;
        }
        $copias_html .= '</table>';
        //*********************************************************************************************
        $proyecto_text = $cominterna_usuario_proyecto->getUsuario()->getFullNombre();
        $proyecto = '<div width="100%" style="margin-top:20px;text-align:left; font-size: 8pt;"><b>Elaborado por:</b> '.($proyecto_text)."</div>";
        $anexos = "";
        $anexosHtml = "";
        $ObsReeRespHtml = "";
        if($this->getObsReenResp() != ""){
            //$ObsReeRespHtml .= " <h6>Observaciones: ".($com_interna->getObsReenResp())." </h6>";
        }
        //**********************************************************************************************
        $list_revisores = array();
        foreach ($cominterna_usuario_revision as $usuario_revisor){
            if(!in_array($usuario_revisor->getUsuarioId(),$list_revisores)){
                $revisor_text = $usuario_revisor->getUsuario()->getFullNombre();
                $revisor_area = $usuario_revisor->getUsuario()->getDependencia()->getNombre();
				$dependencia_id = $usuario_revisor->getCargoUsuario()->getDependenciaId();
				
				if(!empty($dependencia_id)){ $revisor_area = $usuario_revisor->getCargoUsuario()->getDependencia()->getNombre(); }
                $proyecto .= '<div width="100%" style="margin-top:5px;text-align:left; font-size: 8pt;"><b>Revisado por:</b> '.(sprintf('%s - %s',$revisor_text,$revisor_area))."</div>";
                $list_revisores[] = $usuario_revisor->getUsuarioId();
            }
        }
        //**********************************************************************************************
        if(trim($this->getAnexos()) == ""){
            if( basename($this->getRuta()) != ""){
                $fileanexos = explode(",",$this->getRuta());       
                for($n=0; $n<count($fileanexos); $n++){
                    if(trim(basename($fileanexos[$n])) != ""){
                        $anexos.= basename($fileanexos[$n]).',';
                    }
                }                
            }
        }else{
            $anexos = $this->getAnexos();  
        }
        //**********************************************************************************************
        $anexosHtml = '';
		if(trim($anexos) != "" && trim($anexos) != ","){
			$anexosHtml .= '<div style="margin-top: 10px; display: table; width: 99%;"><table border="0" width="100%"><tr><td style="width: 50px; font-family: Arial; font-size: 8pt;text-align:left; vertical-align:middle; white-space:normal;"><b>Anexos:</b>&nbsp;</td>';
			$anexosHtml .= '<td style="font-family: Arial; font-size: 9pt;"><p style="word-wrap: break-word;">'.($anexos).'</p></td>';
			$anexosHtml .= '</tr></table></div>';
		}
        //******************************definir font para el documento**********************************
        $struct_init = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title>
        <style type="text/css">body {font-family:Work Sans;font-size: 11pt;}</style></head><body>';
        fputs($pt, ($struct_init));
        //******************************definir font para el documento**********************************
        //se crea el html para el pie de pagina de la carta y el encoding
        $htmlfooter = '<pd4ml:page.footer>
        <table border="0" width="100%" style="pd4ml-display: table;">        
        <tr><td width="100%" align="right"><font size="2">$[page]/$[total]</font></td></tr>
        </table></pd4ml:page.footer>';
        fputs($pt, ($htmlfooter));            
        //*********************************************************************************************
        //se crea el html para el encabezado de la carta
        $htmlencabezado = '<pd4ml:page.header>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 7pt;"><b>F-OAP-018-CAR</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;"><img src="'.$base_path.'/tmp/'.$this->generateCodeBarInFile(true).'"/></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;"><b>Al contestar por favor cite estos datos:</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;">Radicado No.: <b>'.$this->getRadicado().'</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;">Fecha: '.$this->getFechaCreacion("d/m/Y H:i:s A").'</div>';
        //$htmlencabezado .= '<div width="480px" style="text-align:right">'.date( "h:m",$timestamp_carta ).'</div>';
        $htmlencabezado .= '</pd4ml:page.header>';
        fputs($pt, ($htmlencabezado));
        //$htmlencabezado = '<pd4ml:page.header><div width="100%" style="text-align:left">'.$this->getRadicado().'</div><br/><br/></pd4ml:page.header>';
        //fputs($pt, ($htmlencabezado));
        //*********************************************************************************************
        $html_metas = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title></title></head><style>
        body { font-family: Work Sans; font-size: 15pt; color: black; }
        </style><body>';
        //escribimos en el archivo etiquetas html
        fputs($pt, $html_metas);
        //*********************************************************************************************
        $htmlcontent = '<br/><div style="text-align:center"><h4>'.$correspondenciaEnBorrador.$this->getTipoComInterna().'</h4></div>
        <div style="text-align:left">'.($ciudadOrigen).', '.$fecha_carta.'</div><br/><br/><br/>';
        $htmlcontent .= '<div style="text-align:left">'.($tratamiento).'</div>';
        $htmlcontent .= '<div style="text-align:left"><b>'.($destinatario).'</b></div>
        <div style="text-align:left">'.($cargoDestinatario).'</div>
        <div style="text-align:left">'.($dependenciaDestinatario).'</div>
        <div style="text-align:left">'.($regionalDestinatario).'</div>
        <br/><br/><div style="text-align:left"><table><tr><td valign="top"><b>Asunto:</b></td><td width="100%">
        <p style="text-align: justify">'.$asunto.'</p></td></tr></table></div><br/>';
        //*********************************************************************************************
        fputs($pt, ($htmlcontent));
        //*********************************************************************************************
        fputs($pt, ($mensaje));
        //*********************************************************************************************
        $firmasHtml = ($firmas).'';    
        //*********************************************************************************************
        if(trim($copias_html) != "" && $is_copias_html){
            $firmasHtml .= $copias_html.'';
        }
        //*********************************************************************************************
        if($ObsReeRespHtml != ""){
        $firmasHtml .= ($ObsReeRespHtml).'';
        }
        //*********************************************************************************************
        if($anexosHtml != ""){
        $firmasHtml .= ($anexosHtml).'';
        }
        //*********************************************************************************************
        fputs($pt, ($firmasHtml));
        //*********************************************************************************************
        if(trim($proyecto) != ""){
            fputs($pt, ($proyecto));
        }
        //*********************************************************************************************
        $struct_end = '</body></html>';
        fputs($pt, ($struct_end));
        //*********************************************************************************************
        //Close and output PDF document
        fclose($pt);
    }

    public function getDataEmptyRowFirmas($emptyRows = 2)
    {
        $fila_html = '';
        for($i = 0; $i < $emptyRows; $i++){
            $fila_html .= '<tr><td>&nbsp;</td></tr>';
        }
        return $fila_html;
    }
    
    public function getDataAreasOrCargosFirmas($data_str, $add_td = true)
    {
        if(!count($data_str)){ return ''; }
        $fila_html = '';
        foreach($data_str as $row_str){
            if($add_td){
                $fila_html .= '<td style="width: 50%;">'.$row_str.'</td>';
            }else{
                $fila_html .= $row_str;
            }
        }
        return '<tr>'.$fila_html.'</tr>';
    }

    public function generateCodeBarInFile($clearlabels = false, $scale = 1, $height = 25, $fsize = 8, $dpi = 72)
    {
        try {
            include_once(sfConfig::get('sf_lib_dir').'/BarcodeGenerator/generate_barcode.php');
            //******************************************************************************
            $text_radicado = $this->getRadicado();
            if(empty($text_radicado) || is_null($text_radicado)){
                $text_radicado = "Sin Radicar";
            }
            //******************************************************************************
            $sticker_dir = sfConfig::get('sf_web_dir');
            $font_dir = sfConfig::get('sf_lib_dir') . "/BarcodeGenerator/class/font/ariblk.ttf";
            $filename = md5($text_radicado) . '.png';
            $filepath = $sticker_dir . "/tmp/" . $filename;
            GenerateCode($filepath,$text_radicado,$font_dir,$scale,$height,$fsize,$dpi,$clearlabels);
            //******************************************************************************
            return file_exists($filepath) ?  $filename : null;
        } catch(Exception $e) {
            //$error_text = $e->getMessage();
            return null;
        }
    }

    public function generateFileInDisk($margins_list = null){
        $base_path = sfConfig::get('base_simad');
        //*********************************************************************************************
        if($this->getTipoComInterna()->getPlantillasComId()){
            $this->genPdfByPlantillaCom();
            //$this->generateDocPdf();
        }else{
            $this->generateDocPdf();
        }
        //*********************************************************************************************
        $filedata = $this->getPrimaryKey().'.php';
        $url = "http://".$_SERVER["HTTP_HOST"];
        $url .= '/com_html/com_interna/'.$filedata;
        //*********************************************************************************************
        //MARGENES DE IMPRESION
        if(empty($margins_list)){
            $margins_list['top'] = 15;
            $margins_list['left'] = 25;
            $margins_list['buttom'] = 30;
            $margins_list['rigth'] = 25;
        }
        //*********************************************************************************************
        $filename = md5(date("YmdGis").$this->getPrimaryKey()).".pdf"; 
        $dir_jar = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'pd4ml.jar';
        $dir_font = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR;
        $fullpath_source = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.$filename;        
        //*********************************************************************************************
        $java = "java";
        $format_page = "A4";
        $size_point_page = "840";
        $font_use = "-ttf ".$dir_font;
        $adjustwidth = "-adjustwidth";
        //$margins = '-insets 10,23,8,17,mm';//superior=3,izquierda=3,inferior=2,derecha=2
        $margins = '-insets '.$margins_list['top'].','.$margins_list['left'].','.$margins_list['buttom'].','.$margins_list['rigth'].',mm';
        $outfile = "-out $fullpath_source";
        $orientation = "";
        //*********************************************************************************************
        if($this->getUseMembrete()){
            $image_membrete = $this->getRegional()->getImageMembrete();
			if(!empty($this->getTipoComInterna()->getPlantillascomId())){
				if($this->getTipoComInterna()->getPlantillasCom()->getUseMembrete())
					$image_membrete = !empty($this->getTipoComInterna()->getPlantillasCom()->getImageMembrete()) ? trim($this->getTipoComInterna()->getPlantillasCom()->getImageMembrete()) : "";
                else
                    $image_membrete = "";
            }
            //*****************************************************************************************
            $filepath = "/images%sencabezado_carta%s".$image_membrete;
            $web_path = sprintf($filepath,"/","/");            
            $fullpath = sprintf($filepath,DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR);
            $realpath = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$fullpath;
            if(file_exists($realpath)){
                $watermark = "-bgimage " . $base_path . $web_path;
            }
        }
        //*********************************************************************************************
        if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
            // server platform: Windows
            $dir_jar = preg_replace('/\//', "\\", $dir_jar);
            $cmdline = "$java -Xmx512m -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
        } else {
            $font_use = "";
            $cmdline = "$java -XX:MaxHeapSize=8m -XX:CompressedClassSpaceSize=64m -XX:+UseSerialGC -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
			//$cmdline = "$java -Xms1024m -Xmx4096m -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use";	    
        }        
        //*********************************************************************************************
        //echo $cmdline;exit;
        shell_exec($cmdline);
        //*********************************************************************************************
        if(file_exists($fullpath_source)){
            return $fullpath_source;
        }else{
            return null;
        }
    }

    public function readPdfAndGenPdf($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdf/fpdf.php');
		require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/autoload.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/PDF-Parser-1.5/autoload.php');
        /*require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/Fpdi.php');*/
        //**********************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //$source_filename = pathinfo($inputFileName, PATHINFO_FILENAME);
		$source_filename = md5(date("YmdGis").pathinfo($inputFileName, PATHINFO_FILENAME));
        $target_dir = md5(date("YmdGis"));
        //**********************************************************************************************
        if (trim($inputFileName)){
            try {
                $pathToSave = simad_util::createPath($directorio_tmp.DIRECTORY_SEPARATOR.$target_dir);
                $codebar = $tmp_dir.DIRECTORY_SEPARATOR.$this->generateCodeBarInFile(true);
                //**************************************************************************************
                // initiate FPDI
                $pdf = new Fpdi();
                // set the source file
                $pagecount = $pdf->setSourceFile($inputFileName);
                // import page 1
                $pageNo = 1;
                $ptln = 5;
                $tplId = $pdf->importPage($pageNo);
                //$size = $pdf->getTemplateSize($tplId);
                // add a page
                $pdf->AddPage();
                // use the imported page and place it at point 10,10 with a width of 100 mm
                $pdf->useTemplate($tplId, null, null, null, null, true);
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(0, 10, "F-OAP-018-CAR", 0, 0, 'R');
                $pdf->Ln($ptln);
                //$pdf->SetFont('Arial','B',18);
                //$pdf->Image($codebar, 10,30,0,0,'png');
                $pdf->Cell( 0, 10, $pdf->Image($codebar,157.5,16.5,0,0,'png'), 0, 0, 'R');
                //$pdf->Cell(0, 10, $this->getRadicado(), 0, 0, 'R');
                $pdf->Ln($ptln);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(0, 10, "Al contestar por favor cite estos datos:", 0, 0, 'R');
                $pdf->Ln($ptln-1);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(164, 10, "Radicado No.:", 0, 0, 'R');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(0, 10, $this->getRadicado(), 0, 0, 'R');
                $pdf->Ln($ptln-1);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(154, 10, "Fecha:", 0, 0, 'R');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(0, 10, $this->getFechaCreacion("d/m/Y H:i:s A"), 0, 0, 'R');
                //***************************************************************************************
                for($pageNo = ($pageNo + 1); $pageNo <= $pagecount; $pageNo++){
                    $tplIdx = $pdf->importPage($pageNo);
                    //$size_unit = $pdf->getTemplateSize($tplIdx);
                    $pdf->AddPage();
                    //$pdf->useTemplate($tplIdx);
                    $pdf->useTemplate($tplIdx, null, null, null, null, true);
                }
                //***************************************************************************************
                $pdf->Output('F',$pathToSave.DIRECTORY_SEPARATOR.$source_filename.'.pdf');
                $source_filename = $returnFullPath ? ($pathToSave.DIRECTORY_SEPARATOR.$source_filename).'.pdf' : ($target_dir.'/'.$source_filename.'.pdf');
            } catch(Exception $e) {
                echo $e->getMessage();
                $source_filename = null;
            }
            //*******************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        }else{
            $source_filename = null;
        }
		//echo $source_filename;exit;
		//si hay un error al crear el pdf se debe controlar para mostrarle al usuario o tambien para retornar al web service
        //***********************************************************************************************
        return $source_filename;
    }

    public function readPlantillaWordAndGenCom($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        $path_info = pathinfo($inputFileName);
        //*************************************************************************************************************
        $outFileName = md5(date("YmdGis")) . '.pdf';
        $pathToSave = $directorio_tmp . DIRECTORY_SEPARATOR . $outFileName;
        $temp_name = dirname($pathToSave).DIRECTORY_SEPARATOR.$path_info['filename'] . '.pdf';
        $replacement_images = array();
        //*************************************************************************************************************
        if (trim($inputFileName) && file_exists($inputFileName)) {
            try {
                if(file_exists($temp_name)){
                    unlink($temp_name);
                }
                //*****************************************************************************************************
                $docxSalida = $tmp_dir . DIRECTORY_SEPARATOR . basename($inputFileName);
                if(file_exists($docxSalida)){
                    @unlink($docxSalida);
                }
                //*****************************************************************************************************
                $userscom_data = $this->getUsuariosListComObjs();
                //*****************************************************************************************************
                $lstfirmas = $userscom_data['firmas'];
                $unfirma = "";
                $ufcargo = "";
                $ufarea = "";
                $ufmecanica = "";
                $ufregional = "";
                foreach ($lstfirmas as $value) {
                    $unfirma = $value['nombre_ufirma'];
                    $ufcargo = $value['cargo_ufirma'];
                    $ufarea = $value['area_ufirma'];
                    $ufregional = $value['regional_ufirma'];
                    if($this->getEstadocominternaId() != 1)
                        $urlfmecanica = $value['ufirma_mecanica'];
                }
                //*****************************************************************************************************
                $tmp_codebar = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
                $codebar_radicado = $tmp_codebar.simad_util::generateCodeBarInFile(trim($this->getRadicado()));
                $replacement_images['CODEBAR_COM'] = ['path' => $codebar_radicado, 'wcm' => 150, 'hcm' => 40];
                //*****************************************************************************************************
                $datos = [
                    'FECHA_COM' => $this->getFechaCreacion("Y-m-d"),
                    'ASUNTO_COM' => trim($this->getReferencia()),
                    'RADICADO_COM' => trim($this->getRadicado()),
                    'ANEXOS_COM' => trim($this->getAnexos()),
                    'FIRMAS_NOMBRE' => $unfirma,
                    'FIRMAS_CARGOS' => $ufcargo,
                    'FIRMAS_CARGO' => $ufcargo,
                    'FIRMAS_DEPENDENCIA' => $ufarea,
                    'FIRMAS_REGIONAL' => $ufregional,
                    'VIGENCIA_COM' => $this->getFechaCreacion("Y"),
                    'AREA_CODIGO_COM' => $this->getDependencia()->getCodigo(),
                    'RADICADOR_NOMBRE' => $userscom_data['nombre_radicador'],
                    'RADICADOR_DEPENDENCIA' => $userscom_data['area_uradicador'],
                    'UPROYECTA_NOMBRE' => $userscom_data['nombre_creador'],
                    'UPROYECTA_AREA' => $userscom_data['area_ucreador'],
                ];
                //*****************************************************************************************************
                if(isset($urlfmecanica) && !empty($urlfmecanica)){
                    $replacement_images['FIRMA_MECANICA'] = ['path' => $urlfmecanica, 'wcm' => 120, 'hcm' => 80];
                }else{
                    $datos['FIRMA_MECANICA'] = "";
                }
                //*****************************************************************************************************
                if($this->getEstadocominternaId() != 1){
                    if(isset($userscom_data['fmecanica_uradicador']) && !empty($userscom_data['fmecanica_uradicador']))
                        $replacement_images['RADICADOR_FMECANICA'] = ['path' => $userscom_data['fmecanica_uradicador'], 'wcm' => 50, 'hcm' => 30];
                    else
                        $datos['RADICADOR_FMECANICA'] = "";
                    //*****************************************************************************************************
                    if(isset($userscom_data['ucreador_fmecanica']) && !empty($userscom_data['ucreador_fmecanica']))
                        $replacement_images['UPROYECTA_FMECANICA'] = ['path' => $userscom_data['ucreador_fmecanica'], 'wcm' => 50, 'hcm' => 30];
                    else
                        $datos['UPROYECTA_FMECANICA'] = "";
                }else{
                    $datos['RADICADOR_FMECANICA'] = "";
                    $datos['UPROYECTA_FMECANICA'] = "";
                }
                //*****************************************************************************************************
                if(isset($userscom_data['nombre_destino']))
                {
                    $datos['DESTINO_PREFIJO'] = trim($userscom_data['prefijo_udestino']);
                    $datos['DESTINO_NOMBRE'] = trim($userscom_data['nombre_destino']);
                    $datos['DESTINO_FUNCIONARIO'] = trim($userscom_data['nombre_destino']);
                    $datos['DESTINO_EMAIL'] = trim($userscom_data['email_udestino']);
                    $datos['DESTINO_IDENTIFICACION'] = trim($userscom_data['nuid_udestino']);
                    $datos['DESTINO_CARGO'] = trim($userscom_data['cargo_udestino']);
                }
                //*****************************************************************************************************
                $lstcopias = $userscom_data['copias'];
                $uncopia = "";
                $uccargo = "";
                $ucarea = "";
                foreach ($lstcopias as $key => $uccopia) {
                    $uncopia = $uccopia['nombre_ucopia'];
                    $uccargo = $uccopia['cargo_ucopia'];
                    $ucarea = $uccopia['area_ucopia'];
                    
                }
                //*****************************************************************************************************
                $datos['COPIAS_NOMBRE'] = $uncopia;
                $datos['COPIAS_CARGO'] = $uccargo;
                $datos['COPIAS_DEPENDENCIA'] = $ucarea;
                //***********************************INICIA SECCION DE REVISORES*****************************************
                $lstrevisores = $userscom_data['revisores'];$urevisor_urlfmecanica = "";
                $unrevisa = ""; $urcargo = ""; $urarea = "";
                //*******************************************************************************************************
                if(count($lstrevisores) > 1)
                {
                    $arrayHtml = array();
                    foreach ($lstrevisores as $value) {
                        $data_list_com = array();
                        $data_list_com['nombre_revisor'] = $value['nombre_urevisor'];
                        $data_list_com['cargo_urevisor'] = $value['cargo_urevisor'];
                        $data_list_com['area_urevisor'] = $value['area_urevisor'];
                        $data_list_com['urevisor_fmecanica'] = $value['urevisor_fmecanica'];
                        //***********************************************************************************************
                        $arrayHtml[] = $data_list_com;
                    }
                    //***************************************************************************************************
                    $tablaFinal = simad_util::generateTablaHtml($arrayHtml);
                    //***************************************************************************************************
                    $datos['REVISOR_NOMBRE'] = $tablaFinal;
                }
                else
                {
                    foreach ($lstrevisores as $value) 
                    {
                        $unrevisa = $value['nombre_urevisor'];
                        $urcargo = $value['cargo_urevisor'];
                        $urarea = $value['area_urevisor'];
                        //***********************************************************************************************
                        $urevisor_urlfmecanica = "";
                        if(!empty(trim($value['urevisor_fmecanica']))){
                            $urevisor_urlfmecanica = trim($value['urevisor_fmecanica']);
                        }
                    }
                    //***************************************************************************************************
                    $datos['REVISOR_NOMBRE'] = $unrevisa;
                }
                //*******************************************************************************************************
                $datos['REVISOR_CARGO'] = $urcargo;
                $datos['REVISOR_AREA'] = $urarea;
                
                if(!empty($urevisor_urlfmecanica))
                    $replacement_images['REVISOR_FMECANICA'] = ['path' => $urevisor_urlfmecanica, 'wcm' => 50, 'hcm' => 30];
                else
                    $datos['REVISOR_FMECANICA'] = "";
                //*******************************************************************************************************
                $docxTpl = $tmp_dir . DIRECTORY_SEPARATOR . 'tpl_'.basename($inputFileName);
                DocxPlaceholderUtil::convertCurlyPlaceholdersToPhpWordTpl($inputFileName, $docxTpl);
                //*******************************************************************************************************
                $tpl = new TemplateProcessor($docxTpl);
                //*******************************************************************************************************
                foreach ($datos as $key => $value) {
                    $tpl->setValue($key,  $value);
                }
                //*******************************************************************************************************
                $pathDefault = simad_paths_app::readConfigFileApp();
                $imgreal_replacement = array();
                foreach ($replacement_images as $key => $row) {
                    $pathreal = null;
                    if(!is_file($row['path']) && !empty($row['path'])){
                        $pathreal = simad_paths_app::resolveRelativePath($row['path'],$pathDefault);
                    }else if(file_exists($row['path']) && !empty($row['path'])){
                        $pathreal = $row['path'];
                    }
                    
                    $imgreal_replacement[$key] = ['path' => $pathreal, 'wcm' => $row['wcm'], 'hcm' => $row['hcm']];
                }
                //*******************************************************************************************************
                foreach ($imgreal_replacement as $img_key => $img_value) {
                    if(file_exists($img_value['path'])){
                        $tpl->setImageValue($img_key, [
                            'path'   => $img_value['path'],
                            'width'  => $img_value['wcm'],
                            'height' => $img_value['hcm'],
                            'ratio'  => true,
                        ]);
                    }
                }
                //*******************************************************************************************************
                $docxFinal = $tmp_dir . DIRECTORY_SEPARATOR . 'convert_'.basename($inputFileName);
                if(file_exists($docxFinal)){
                    @unlink($docxFinal);
                }

                $tpl->saveAs($docxFinal);
                //*******************************************************************************************************
                if(file_exists($docxFinal)){
                    unlink($docxSalida);
                    rename($docxFinal,$docxSalida);
                }
                //*******************************************************************************************************
                $soffice_cli = simad_util::libreOfficeCliPath();
                //*******************************************************************************************************
                $command = sprintf(
                    '"'.$soffice_cli.'" --headless --convert-to pdf --outdir %s %s',
                    escapeshellarg(dirname($pathToSave)),
                    escapeshellarg($docxSalida)
                );
                //*******************************************************************************************************
                exec($command, $output, $returnVar);
                //*******************************************************************************************************
                if ($returnVar !== 0) {
                    return null;
                }
                //*******************************************************************************************************
                if(file_exists($temp_name)){
                    @rename($temp_name,$pathToSave);
                }
                //*******************************************************************************************************
                return $returnFullPath ? $pathToSave : $outFileName;
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
        } else {
            return null;
        }
        //***************************************************************************************************************
        return $outFileName;
    }

    public function readPlantillaWordAndGenPdf($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/bootstrap.php';
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/autoload.inc.php';
        $domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/src');      
        Settings::setPdfRenderer( Settings::PDF_RENDERER_DOMPDF, $domPdfPath );
		//$domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/tcpdf');
		//Settings::setPdfRenderer( Settings::PDF_RENDERER_TCPDF, $domPdfPath );
        //*************************************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR;
        $directorio_upload = simad_util::createPath($upload_dir);
		$outFileName = md5(date("YmdGis"));
        //*************************************************************************************************************
         if (trim($inputFileName)){
            try {
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($inputFileName);
                $pathToSave = $directorio_tmp.DIRECTORY_SEPARATOR.$outFileName;
                $templateProcessor->saveAs($pathToSave.'.docx');
				
				$phpWord = \PhpOffice\PhpWord\IOFactory::load($pathToSave.'.docx');
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');
                $xmlWriter->save($pathToSave.'.pdf', TRUE);
                //*****************************************************************************************************
				// Saving the document as HTML file...
				//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
				//$objWriter->save($pathToSave.'.html');
                //*****************************************************************************************************
                return $returnFullPath ? $pathToSave.'.pdf' : $outFileName.'.pdf';
            } catch(Exception $e) {
                echo $e->getMessage();
                return null;
            }
            //*********************************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        }else{
            return null;
        }
        //*************************************************************************************************************
        return $outFileName;
    }

    public function generatePdfByFile($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
		$typefile = strtolower(pathinfo($inputFileName, PATHINFO_EXTENSION));
        if($typefile == 'pdf'){
            return $this->readPdfAndGenPdf($inputFileName,$returnFullPath,$deleteFile);
        }elseif($typefile == 'doc' || $typefile == 'docx'){
            $key_config = simad_util::readConfigFileApp(array('strategy_gen_word_to_pdf'));
            $word_to_pdf = isset($key_config['strategy_gen_word_to_pdf']) ? trim($key_config['strategy_gen_word_to_pdf']) : "PhpWord";
            //***************************************************************************
            if($word_to_pdf == "PhpWord")
                return $this->readPlantillaWordAndGenPdf($inputFileName, $returnFullPath, $deleteFile);
            else
                return $this->readPlantillaWordAndGenCom($inputFileName, $returnFullPath, $deleteFile);
        }else{
            return null;
        }
    }
		
    /**
    * objectActions::singDocumentProcessAndes()
    * firma el documento digitalmente
	* @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
    public function singDocumentProcessAndes($signAllPages = false)
    {
        try{
            $firmaApi = new WsFirmaApiAndes();
            $simadSoap = new WsSimadUariv();
            //***************************************************************************************************
            $dir_raiz   = ParametroPeer::retrieveByPk(25)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
            $filename   = sprintf("%s.%s",trim($this->getRadicado()),'pdf');
            $temp_firma = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.md5(date("YmdGis"));
            //***************************************************************************************************
            if(!empty($this->getUrlFileWord())){
                $file_attach = $this->generatePdfByFile($this->getUrlFileWord(),true);
            }else{
                $params_margin['top'] = 15;
                $params_margin['left'] = 25;
                $params_margin['buttom'] = 20;
                $params_margin['rigth'] = 25;
                //***********************************************************************************************
                $file_attach = $this->generateFileInDisk($params_margin);
            }
            //***************************************************************************************************
            if($file_attach != null){
                $storage_com = $this->getBasicUrlDigitCom($dir_raiz,$digit_dir);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                //***********************************************************************************************
                $filesing = $file_attach;$response_list = array();
                //***********************************************************************************************
                if(WsFirmaApiAndes::SERVICE_ENABLE_WS){
                    $ulist_firma = CominternaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());//FALTA VALIDAR CUANDO NO HAY USUARIOS, RESPONDE CON FIRMADO PERO EL ARCHIVO DAÑADO
                    if(count($ulist_firma) <= 0){
						$this->setFirmadoDigital(4);//EL DOCUMENTO NO SE FIRMA DIGITAL
                        $this->save();
						return $response_process = array('httpStatus' => 400, 'message' => 'Ninguno de los usuarios que firman la comunicaci&oacute;n tienen habilitada la firma digital');
					}
					//*******************************************************************************************
                    $Xc = 5;$Yc = 640;$Wc = 40;$Hc = 120;
					//*******************************************************************************************
                    $isSingned = true; $ubicacionFirma = array('x' => $Xc,'y' => $Yc,'w' => $Wc,'h' => $Hc);
                    foreach ($ulist_firma as $eufirma) {
                        $firma_info = $eufirma->getNombreApellido();
                        //***************************************************************************************
						$b64firma = simad_util::createImgFirmaDigital($firma_info,$temp_firma,true,90,false);
                        //$b64firma = sfConfig::get("sf_lib_dir").DIRECTORY_SEPARATOR.'efirma'.DIRECTORY_SEPARATOR.WsFirmaApiAndes::FIRMA_VISIBLE_IMAGE;
                        //***************************************************************************************
                        $response_sing = $firmaApi->documentWsApiFirmaAndes($eufirma->getLoginFirma(),base64_decode($eufirma->getPassFirma()),$filesing,$targetpath,$b64firma,$ubicacionFirma);
                        //***************************************************************************************
                        $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                        if(!$response_sing['error']){
                            $Yc = ($Yc-$Hc);
                            $filesing = $response_sing['filesing'];							
                            $ubicacionFirma = array('x' => $Xc,'y' => $Yc,'w' => $Wc,'h' => $Hc);
                        }else{
                            $isSingned = false;
                            break;
                        }
                    }
                    //*******************************************************************************************
					if(!is_file($response_sing['filesing'])){
						if($isSingned){ $isSingned = simad_util::getConvertB64ToFile($response_sing['filesing'],$targetpath); }
					}
                    //*******************************************************************************************
                    if($isSingned){
                        $singOnMsg = 'Documento firmado existosamente!';
                        $response_process = array('httpStatus' => 200, 'message' => $singOnMsg);
                        $this->setFirmadoDigital(1);//FIRMA EXITOSA
                        $this->save();
                    }else{
                        $this->setFirmadoDigital(3);//ERROR SERVIDOR PROVEEDOR FIRMA
                        $this->save();
                        $singOnMsg = 'Ocurrio un error con el proveedor de firma digital(isSingned), el documento no se firmo';
                        //***************************************************************************************
                        foreach ($response_list as $item_error) {
                            if($item_error['error']){
                                $singOnMsg .= ", ".$item_error['mesagge'];
                            }
                        }
                        //***************************************************************************************
                        $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                        //***************************************************************************************
                        if(file_exists($file_attach)){ unlink($file_attach); }
                        //***************************************************************************************
                        return $response_process;
                    }
                }else{
                    $singOnMsg = 'El servicio de firma digital esta deshabilitado';
                    $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                }
            }else{
                $response_process = array('httpStatus' => 400, 'message' => 'Error al generar el archivo pdf');
            }
            //***************************************************************************************************
            if(file_exists($file_attach)){ unlink($file_attach); }
            return $response_process;
        }catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }
	
    /**
     * objectActions::singDocumentProcessGse()
    * firma la comunicacion digitalmente
	* @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
    public function singDocumentProcessGse($signAllPages = false)
    {
        try{
            $firmaApi = new WsFirmaApiGse();
            $simadSoap = new WsSimadUariv();
            //***************************************************************************************************
            $dir_raiz   = ParametroPeer::retrieveByPk(25)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(15)->getValortexto();
            $filename   = sprintf("%s.%s",trim($this->getRadicado()),'pdf');
            $temp_firma = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.md5(date("YmdGis"));
            //***************************************************************************************************
            if(!empty($this->getUrlFileWord())){
                $file_attach = $this->generatePdfByFile($this->getUrlFileWord(),true);
            }else{
                $params_margin['top'] = 15;
                $params_margin['left'] = 25;
                $params_margin['buttom'] = 20;
                $params_margin['rigth'] = 25;
                //***********************************************************************************************
                $file_attach = $this->generateFileInDisk($params_margin);
            }
            //***************************************************************************************************
            if($file_attach != null){
                $storage_com = $this->getBasicUrlDigitCom($dir_raiz,$digit_dir);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                //***********************************************************************************************
                $token = $firmaApi->loginWsApiFirmaGse();
                $filesing_base64 = null;$response_list = array();
                //***********************************************************************************************
                if($token != null){
                    $ulist_firma = CominternaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());//FALTA VALIDAR CUANDO NO HAY USUARIOS, RESPONDE CON FIRMADO PERO EL ARCHIVO DAÑADO
                    if(count($ulist_firma) <= 0){
						$this->setFirmadoDigital(4);//EL DOCUMENTO NO SE FIRMA DIGITAL
                        $this->save();
						return $response_process = array('httpStatus' => 400, 'message' => 'Ninguno de los usuarios que firman la comunicaci&oacute;n tienen habilitada la firma digital');
					}
					//*******************************************************************************************
                    $filesing_base64 = simad_util::getConvertFileToB64($file_attach);
                    $isSingned = true; $ubicacionFirma = array();$Yc = 680;$Hc = 100;
                    foreach ($ulist_firma as $eufirma) {
                        $firma_info = $eufirma->getNombreApellido();
                        $b64firma = simad_util::createImgFirmaDigital($firma_info,$temp_firma);
                        $response_sing = $firmaApi->documentWsApiFirmaGse($eufirma->getLoginFirma(),base64_decode($eufirma->getPassFirma()),$filesing_base64,$token,$targetpath,$b64firma,$ubicacionFirma);
                        //***************************************************************************************
                        $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                        if(!$response_sing['error']){
                            $Yc = ($Yc-$Hc);
                            $filesing_base64 = $response_sing['file_base64'];
                            $ubicacionFirma = array('x' => 0,'y' => $Yc,'w' => 40,'h' => $Hc);
                        }else{
                            $isSingned = false;
                            break;
                        }
                    }
                    //*******************************************************************************************
                    if($isSingned){ $isSingned = simad_util::getConvertB64ToFile($response_sing['file_base64'],$targetpath); }
                    //*******************************************************************************************
                    if($isSingned){
                        $singOnMsg = 'Documento firmado existosamente!';
                        $response_process = array('httpStatus' => 200, 'message' => $singOnMsg);
                        $this->setFirmadoDigital(1);//FIRMA EXITOSA
                        $this->save();
                    }else{
                        $this->setFirmadoDigital(3);//ERROR SERVIDOR PROVEEDOR FIRMA
                        $this->save();
                        $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo';
                        //***************************************************************************************
                        foreach ($response_list as $item_error) {
                            if($item_error['error']){
                                $singOnMsg .= ", ".$item_error['mesagge'];
                            }
                        }
						//***************************************************************************************
						//$logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."WsFirmaApiGse.log";
						//simad_util::writetolog($logfile,'radicado => '. $this->getRadicado() . ' message => ' . $singOnMsg);
                        //***************************************************************************************
                        $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                        //***************************************************************************************
                        if(file_exists($file_attach)){ unlink($file_attach); }
                        //***************************************************************************************
                        return $response_process;
                    }
                }else{
                    //rename($file_attach, $targetpath);
                    $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo digitalmente';
                    $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                }
            }else{
                $response_process = array('httpStatus' => 400, 'message' => 'Error al generar el archivo pdf');
            }
            //***************************************************************************************************
            if(file_exists($file_attach)){ unlink($file_attach); }
            return $response_process;
        }catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }
	
	/**
    * objectActions::singDocumentProcess()
    * Inicia proceso de firmado digital del documento, usando la integracion con alguno de los proveedores de firma digital
    * @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
	public function singDocumentProcess($signAllPages = false)
    {
		try{
			return $this->singDocumentProcessAndes($signAllPages);
			//return $this->singDocumentProcessGse($signAllPages);
		} catch (\PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
	}
	
    /**
    * objectActions::radicaComBatch()
    * funcion que permite radicar una comunicacion externa enviada que se encuentra en estado borrador
    * @return bool retorna true|false
    */
    public function radicaComBatch()
    {
        try {
            $usuarios_com = $this->getUsuariosListComIds();
            $ufirmas_list = preg_split("/[,]+/",$usuarios_com['firmas'], -1, PREG_SPLIT_NO_EMPTY);
            $udestinatarios_list = preg_split("/[,]+/",$usuarios_com['destinatario'], -1, PREG_SPLIT_NO_EMPTY);
            $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
            //*************************************************************************************************
            $destinatario_id = count($udestinatarios_list) > 0 ? $udestinatarios_list[0] : 0;
            $firmante_id = $ufirmas_list[0];
            if(!$firmante_id){ $firmante_id = $this->getUser()->getAttribute('usuario_id','', 'subscriber'); }
            //$objUsuarioFirmante =  UsuarioPeer::retrieveByPk($firmante_id);
            //*************************************************************************************************
            $depen_codigo =  $this->getDependencia()->getCodigo();
            $dependencia = $this->getDependenciaId();
            $regional = $this->getRegionalId();
            //$entidad_id = $this->getRegional()->getEntidadId();
            //*************************************************************************************************
            if($this->getEstadocominternaId() == 1){
                $radicado = $this->getRadicadoFormat(null,$regional,$depen_codigo);
                $this->setRadicado($radicado);
            }else{
                return false;
            }
            //*************************************************************************************************
            $estadocominterna_id = 2;
            $this->setEstadocominternaId($estadocominterna_id);
            $this->setFechaCreacion(date("Y-m-d G:i:s"));
            $this->setRegionalId($regional);
            $this->setDependenciaId($dependencia);
            $this->save();
            //*************************************************************************************************
            if($this->getRequiereRespuesta()){
                CominternaUsuarioPeer::updateEstados($this->getPrimaryKey(),"",true,5);
            }else{
                CominternaUsuarioPeer::updateEstados($this->getPrimaryKey(),"");
            }
            //*************************************************************************************************
            $info_com['esta_asignada'] = 0;
            $info_com['usuario_id'] = $usuariologuiado;
            $info_com['pkcom_id'] = $this->getPrimaryKey();
            $info_com['cusuario_id'] = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuariologuiado);
            $info_com['estadocom_id'] = $estadocominterna_id;
            $info_com['rol_id'] = 6;
            CominternaUsuarioPeer::addUserByCom($info_com);
            //*************************************************************************************************
            if($this->getConsecutivoResp()){
                $c = new Criteria();
                $c->add(CominternaUsuarioPeer::COMINTERNA_ID,$this->getConsecutivoResp());
                $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
                $estado_interna = CominternaUsuarioPeer::doSelect($c);
                foreach($estado_interna as $result){
                    $result->setEstadocominternaId(9);
                    $result->save();
                }
                //*********************************************************************************************
                $interna_origen = ComInternaPeer::retrieveByPK($this->getConsecutivoResp());
                if($interna_origen != null){
                    $interna_origen->setEstadocominternaId(9);
                    $interna_origen->save();
                }
            }
            //*************************************************************************************************
            $cuser_current = CominternaUsuarioPeer::getCurrentUserAsignado($this->getPrimaryKey());
            if($cuser_current != null){
                if($cuser_current->getRolusuariocominternaId() != 2){
                    $cuser_current->setEstaAsignada(0);
                    $cuser_current->setFechaAprobacion(date("Y-m-d G:i:s"));
                    $cuser_current->setCheckAprobacion(1);
                    $cuser_current->save();
                }   
            }
            //*************************************************************************************************
            $cuser_proyecta = CominternaUsuarioPeer::getUserComByRol($this->getPrimaryKey(),1);
            if($cuser_proyecta != null){
                $cuser_proyecta->setEstaAsignada(1);
                $cuser_proyecta->save();
            }
            //*************************************************************************************************
            CominternaUsuarioPeer::updateCheckAllFirmaUser($this->getPrimaryKey());
            //*************************************************************************************************
            if(!empty($this->getExpedienteId()) && !empty($this->getTipoDocumentalCod())){
                $origentransfer_id = 1;
                $this->addNewTransferenciaAndContenido($origentransfer_id,$firmante_id);
            }
            //*************************************************************************************************
            $buzon = 2;
            ComInternaPeer::initWorkflowCom($this,$destinatario_id,$firmante_id,$buzon);
            //*************************************************************************************************
            if(count($udestinatarios_list) > 1){
                unset($udestinatarios_list[0]);
                $objUsuarioFirmante =  UsuarioPeer::retrieveByPk($firmante_id);
                $this->addNewBatchComInterna($udestinatarios_list,$objUsuarioFirmante,$regional,$depen_codigo);
            }
            //*************************************************************************************************
            return true;
        } catch (PropelException $ex) {
            return false;
        } catch (Throwable $ex) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function sendMailAprob($user_email,$encabezado_cuerpo="")
    {
        if(trim($encabezado_cuerpo) == ""){
        $encabezado_cuerpo = "Este es un mensaje para informarle que le asignó una comunicación interna y esta pendiente para su aprobación <br>";
        $encabezado_cuerpo .= "puede consultar la comunicación en su buzon de borradores con la siguiente información:";
        }
        //*********************************************************************************************************************
        $cuerpo = '
        <html>
            <head>
                <title></title>
            </head>
            <body>
                <div id="cotenedor">
                    <br>'.$encabezado_cuerpo.'     
                    <br>
                    <br>
                    <b>Radicado:</b> '.$this->getRadicado().' <br>
                    <b>Asunto:</b> '.($this->getReferencia()).'<br>
                    <b>Fecha Creacion:</b> '.$this->getFechaCreacion().' <br>
                    <br>
                </div>
            </body>
        </html>';
        $cabeceras = "Content-type: text/html\r\n";
        //*********************************************************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('CAD: Aprobación Comunicación Interna');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($user_email, $user_email);    
        if($baseMail->InitSend() === true)
        {
            $baseMail->writetolog("Alerta enviada: " . $this->getRadicado() . " Enviado a: " . $user_email);
        }else{
            $baseMail->writetolog("Error al enviar alerta: " . $this->getRadicado() . " Cuenta correo: " . $user_email);
        }
        //***********************************************************************************************************************
    }

    public function envioEmail($user_destino,$user_firma,$encabezado_cuerpo=""){
        $http = 'http';	
        $server = $http . "://".$_SERVER["SERVER_NAME"];
        if(trim($encabezado_cuerpo) == ""){
            $encabezado_cuerpo = "Este es un mensaje para informarle que se le ha radicado una Comunicaci&oacute;n Interna:";
        }    
        //*********************************************************************************************************
        $usuario_destino = UsuarioPeer::retrieveByPK($user_destino);
        $usuario_firma = UsuarioPeer::retrieveByPK($user_firma);
        //*********************************************************************************************************
        $nota_alpie = '<p class="MsoNormal" style="text-autospace:none">
        <b><i><span style="font-size:9.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;color:#FF0000">&nbsp;
        *Debe estar autenticado para poder visualizar la comunicaci&oacute;n!<u></u><u></u></span></i></b></p>';
        //*********************************************************************************************************
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">
        <br>'.$encabezado_cuerpo.'
        <br>
        <br>
        <b>Fecha Radicaci&oacute;n:</b> '.$this->getFechaCreacion().' <br>
        <b>Numero De Radicado:</b> '.$this->getRadicado().'<br>
        <b>Remitente:</b> '.utf8_encode($usuario_firma->getFullNombre()).'<br>    
        <b>Asunto:</b> '.utf8_encode($this->getReferencia()).'<br>
        <b>'.utf8_encode('Tipo De Comunicación:').'</b> '.utf8_encode($this->getTipoComInterna()).'<br>';
        //*********************************************************************************************************
        //$linkshared = '<a target="_blank" href="'.$server.'/interna.php/com_interna/show?cominterna_id='.$this->getPrimaryKey().'"/>Para Visualizar la comunicacion haga clic aqui!(*)</a>'.$nota_alpie;
        //*********************************************************************************************************
        $cuerpo .= '<br></div></body></html>';    
        $cabeceras = "Content-type: text/html; charset=UTF-8\r\n";
        //*********************************************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('CAD : Comunicaciones Internas');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($usuario_destino->getEmail(), $usuario_destino->getEmail());    
        if($baseMail->InitSend() === true){
            $baseMail->writetolog("Alerta enviada: " . $this->getRadicado() . " Enviado a: " . $usuario_destino->getEmail());
        }else{
            $baseMail->writetolog("Error al enviar alerta: " . $this->getRadicado() . " Cuenta correo: " . $usuario_destino->getEmail());
        }
    }

    /**
     * objectActions::addNewTransferenciaAndContenido()
    * funcion para crear una transferencia automatica, crea el contenido documental
    * @return mixed resultado proceso array('isError' => true|false, 'message' => message)
    */
    public function addNewTransferenciaAndContenido($origentransfer_id,$usuariosolicita_id)
    {
        try {
            $tipodocumental_id = $this->getTipoDocumentalCod();
            $unidaddocumental_id = $this->getExpedienteId();
            $unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
            //***************************************************************************************************
            $localizacionexp_id = $unidad_documental->getLocalizacionunidaddocumentalId();
            $fecha_acepta = null;
            $observaciones = "Transferencia automatica desde comunicaciones";
            //***************************************************************************************************
            if($localizacionexp_id == 1){
                $destinotransferencia_id = 1;
                $fecha_acepta = date('Y-m-d G:i:s');
                $estadotrans_id = 2;
            }elseif($localizacionexp_id == 2){
                $destinotransferencia_id = 2;
                $estadotrans_id = 1;
            }else{
                $destinotransferencia_id = 3;
                $estadotrans_id = 1;
            }
            //***************************************************************************************************
            if($unidad_documental != null){
                $data = array();
                $data['cominterna_id'] = $this->getPrimaryKey();
                $data['estadotrans_id'] = $estadotrans_id;
                $data['unidaddocumental_id'] = $unidad_documental->getPrimaryKey();
                $data['origentransferencia_id'] = $origentransfer_id;
                $data['tipodocumental_id'] = $tipodocumental_id;
                $data['destinotransferencia_id'] = $destinotransferencia_id;
                $data['fecha_acepta'] = $fecha_acepta;
                $data['observaciones'] = $observaciones;
                //***********************************************************************************************
                $transferencia = TransferenciaPeer::createDefaultTransfer($data);
                //***********************************************************************************************
                if($transferencia != null){
                    $transferencia_user = TransferenciaPeer::createUserTransferencia($transferencia,$usuariosolicita_id);
                    if($transferencia_user == null){
                        $transferencia->delete();
                        return null;
                    }
                    //*******************************************************************************************
                    $transferencia->transferirGestion($usuariosolicita_id);
                    return array('isError' => false, 'message' => 'Comunicación archivda correctamente');
                }else{
                    return array('isError' => true, 'message' => 'Ocurrio un error y no se pudo transferir la comunicación');
                }
            }else{
                return array('isError' => true, 'message' => 'El expediente no es valido, se puede transferir la comunicación');
            }
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::addNewBatchComInterna()
    * funcion para crear una comunicacion interna masiva basada en un objeto de una interna ya radicada
    * @param $unewuserdestino lista de usuarios destinatarios de las nuevas comunicaciones
    * @return mixed resultado proceso array('isError' => true|false, 'message' => message)
    */
    public function addNewBatchComInterna($udestinatarios_list = array(),$ucom_firmante, $regional,$depen_codigo)
    {
        $listcom_efirma = array();
        try {
            $listcom_efirma[] = $this;
            foreach ($udestinatarios_list as $udestino) {
                $ncom_interna = new ComInterna();
                $ncom_interna = $this->copy();
                $radicado = $ncom_interna->getRadicadoFormat(null,$regional,$depen_codigo);
                $ncom_interna->setRadicado($radicado);
                $ncom_interna->setFechaCreacion(date("Y-m-d G:i:s"));
                $ncom_interna->save();
                //************************************************************************************
                $nusuarios_com = CominternaUsuarioPeer::getListUsersByCom($this->getPrimaryKey());
                foreach ($nusuarios_com as $urow) {
                    if($urow->getRolusuariocominternaId() != 4){
                        $info_com['pkcom_id'] = $ncom_interna->getPrimaryKey();
                        $info_com['usuario_id'] = $urow->getUsuarioId();
                        $info_com['cusuario_id'] =  $urow->getCargousuarioId();
                        $info_com['estadocom_id'] = $urow->getEstadocominternaId();
                        $info_com['rol_id'] = $urow->getRolusuariocominternaId();
                        $uncom_interna = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
                    }elseif($urow->getRolusuariocominternaId() == 4 && $udestino == $urow->getUsuarioId()){
                        $info_com['pkcom_id'] = $ncom_interna->getPrimaryKey();
                        $info_com['usuario_id'] = $udestino;
                        $info_com['cusuario_id'] =  $urow->getCargousuarioId();
                        $info_com['estadocom_id'] = $urow->getEstadocominternaId();
                        $info_com['rol_id'] = $urow->getRolusuariocominternaId();
                        $uncom_interna = CominternaUsuarioPeer::addOrUpdateUserByCom($info_com);
                        //****************************************************************************
                        $urow->delete();
                    }
                }
                //************************************************************************************
                $listcom_efirma[] = $ncom_interna;
                //************************************************************************************
                if(!empty($ncom_interna->getExpedienteId()) && !empty($ncom_interna->getTipoDocumentalCod())){
                    $origentransfer_id = 1;
                    $ncom_interna->addNewTransferenciaAndContenido($origentransfer_id,$ucom_firmante->getPrimaryKey());
                }
                //************************************************************************************
                ComInternaPeer::initWorkflowCom($ncom_interna,$udestino,$ucom_firmante->getPrimaryKey(),2);
                //************************************************************************************
               
            }
            //****************************************************************************************
            ComInternaPeer::singBatchComInternas($listcom_efirma);
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }
	
	/**
    * objectActions::getComIsArchivedExpediente()
    * valida si la comunicacion esta archivada
    * @return void
    */ 
    public function getComIsArchivedExpediente()
    {
        try
        {
            if(!empty($this->getMarcaVinculacion()))
            {
                $expediente_info = array();
                //********************************************************************************************
                $contenidodoc_id = $this->getContenidodocId();
                $contenido_unidad_documental = ContenidoUnidadDocumentalPeer::retrieveByPk($contenidodoc_id);
                //********************************************************************************************
                if($contenido_unidad_documental == null){
                    //no se encontro el contenido documental
                    return array('existe_expediente' => false,'titulo_expediente' => null);
                }
                //********************************************************************************************
                $unidaddocumental_id = $contenido_unidad_documental->getUnidaddocumentalId();
                $unidad_documental = $contenido_unidad_documental->getUnidadDocumental();
                if($unidad_documental == null){
                    //no se encontro el tipo documental
                    return array('existe_expediente' => false,'titulo_expediente' => null);
                }
                //********************************************************************************************
                $subserie = $unidad_documental->getSubserie();
                if($subserie == null){
                    //no se encontro la subserie
                    return array('existe_expediente' => false,'titulo_expediente' => null);
                }
                //********************************************************************************************
                $expediente_info['existe_expediente'] = true;
                $expediente_info['titulo_expediente'] = $unidad_documental->getCodigoTitulo();
                $expediente_info['subserie_expediente'] = $subserie->getDescripcion();
                $expediente_info['fase_archivo'] = $unidad_documental->getLocalizacionunidaddocumentalId();
                $expediente_info['unidaddocumental_id'] = $unidaddocumental_id;
                //********************************************************************************************                
                return $expediente_info;
            }
        }catch (PropelException $th) {
            return array('existe_expediente' => false,'titulo_expediente' => null);
        }catch (\Exception $th) {
            return array('existe_expediente' => false,'titulo_expediente' => null);
        }catch (\Throwable $th) {
            return array('existe_expediente' => false,'titulo_expediente' => null);
        }
    }

    /**
    * objectActions::getAttachmentCom()
    * Obtiene los anexos del registro
    * @return mixed array(ruta_archivos)
    */
    public function getAttachmentCom()
	{
		try {
			$attach_url = array();
            $pathtmp = simad_util::createPath(sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR);
            $rootUrl = sfConfig::get('localUrl');
            //****************************************************************************************
			if(!in_array($this->getEstadocominternaId(),array(1,4))){
				$files_adjuntos = preg_split("/[,]+/",$this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
				//************************************************************************************
				for($j = 0; $j < count($files_adjuntos); $j++){
                    if(trim($files_adjuntos[$j])){
                        try {
                            $filename = basename($files_adjuntos[$j]);
                            echo $fileurl = $rootUrl.$files_adjuntos[$j];

                            if(@file_exists($pathtmp.$filename)){
                                @unlink($pathtmp.$filename);
							}

                            $ch = curl_init($fileurl);
                            $fp = fopen($pathtmp.$filename, 'wb');
                            curl_setopt($ch, CURLOPT_FILE, $fp);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                            curl_exec($ch);

                            $tamanoDescargado = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

                            curl_close($ch);
                            fclose($fp);

                            // Verificar integridad del archivo
                            if (!simad_util::verificarIntegridadArchivo($pathtmp.$filename, $tamanoDescargado)) {
                                @unlink($pathtmp.$filename);
                                continue;
                            }

                            if(file_exists($pathtmp.$filename)){
                                $attach_url[] = $pathtmp.$filename;
                            }
                        } catch (\Exception $th) {
                            //throw $th;
                        }
                    }
                }
			}
        } catch (PropelException $th) {
			$attach_url = null;
		} catch (\Exception $th) {
			$attach_url = null;
		}
		//*********************************************************************************************
		return $attach_url;
	}
}