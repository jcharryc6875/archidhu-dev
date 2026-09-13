<?php

/**
 * Subclass for performing query and update operations on the 'COM_ENVIADA' table.
 *
 * 
 *
 * @package lib.model
 */ 

use \PhpOffice\PhpWord\Settings;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ComEnviadaPeer extends BaseComEnviadaPeer
{
    
    public static function getHashComData(ComEnviada $object)
	{
        $dstorage = array();
		$object_class = "ComEnviada";
		$field_include = array('Asunto', 'ComEnviadaId', 'PeriodoId', 'Radicado', 'FechaCreacion', 'NumeroRadicacion', 'RegionalId', 'DependenciaId');
		try
        {
			$peer_class = sprintf("%sPeer",$object_class);
            $campos_objeto = $peer_class::getFieldNames();
            foreach($campos_objeto as $field)
            {
				if(in_array($field,$field_include))
                {
					$instanceMethod = 'get'.$field;
					$dstorage[] = $object->$instanceMethod();
				} 
            }            
        }
        catch(PropelException $px)
        { 
            return false;
        }
        catch(Throwable $th)
        { 
            return false;
        }
        catch(Exception $ex)
        { 
            return false;
        } 
        //********************************************************************
        $str_encrypt = implode("",$dstorage);
        $hashLog = hash('sha256', $str_encrypt);
        
        return $hashLog;
    }

    public static function getReporteComEnviada($params)
	{
		try
		{
			$c = new Criteria();

			if(!empty($params['comenv_radicado']))
			{
				$c->add(ComEnviadaPeer::RADICADO, '%'.$params['comenv_radicado'].'%', Criteria::LIKE);
				//$c->add(ComEnviadaPeer::TITULO, $params['nombre_expediente']);
			}
			if(!empty($params['comenv_asunto']))
			{
                $c->add(ComEnviadaPeer::ASUNTO, '%'.$params['comenv_asunto'].'%', Criteria::LIKE);
			}
			if(!empty($params['comenv_periodo']))
			{
				$c->add(ComEnviadaPeer::PERIODO_ID, $params['comenv_periodo']);
			}
			if(!empty($params['comenv_estado']))
			{
                $c->add(ComEnviadaPeer::ESTADOCOMENVIADA_ID, $params['comenv_estado']);
			}
			if(!empty($params['comenv_remitente']))
			{
				$c->addJoin(ComEnviadaPeer::COMENVIADA_ID, EnviadaDirectorioPeer::COMENVIADA_ID);
				$c->addJoin(EnviadaDirectorioPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
				//****************************************************************************
				$c->add(DirectorioExternoPeer::FUNCIONARIO, '%'.$params['comenv_remitente'].'%', Criteria::LIKE);
			}

			if(!empty($params['comenv_fechCreaDesde']) && !empty($params['comenv_fechCreaHasta']))
			{
				$c->add(ComEnviadaPeer::FECHA_CREACION, $params['comenv_fechCreaDesde'], Criteria::GREATER_EQUAL);  
				$c->addAnd(ComEnviadaPeer::FECHA_CREACION, $params['comenv_fechCreaHasta'], Criteria::LESS_EQUAL);
			}
			//$com_enviada = ComEnviadaPeer::doSelect($c); return $com_enviada;
			return $c;
		}
		catch(PropelException $ex)
		{
			return $ex->getMessage();
		}
		catch(\Exception $ex)
		{
			return $ex->getMessage();
		}
        catch(\Throwable $ex)
		{
			return $ex->getMessage();
		}
	}

    public static function updateAsignadoCom($comenviada_id, $rolus_id=1, $isAsignado=1)
    {
        try
        {
            $conexion = Propel::getConnection();
            $query = "UPDATE %s SET  %s = ".$isAsignado." WHERE %s = ".$comenviada_id;
            $query = sprintf($query, EnviadaUsuarioPeer::TABLE_NAME, EnviadaUsuarioPeer::ESTA_ASIGNADA, EnviadaUsuarioPeer::COMENVIADA_ID);
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
        } 
        catch (PropelException $x) 
        {
            $msgex = $x->getMessage();
        } 
        catch (Exception $x) 
        {
            $msgex = $x->getMessage();
        }
    }


    public static function addUserRolByCom($comenviada_id, $usuario_id, $cusuario_id, $estadocomenviada_id=1,$rolus_id=1,$isAsig=1,$tprocomId=0, $fecha_reasigna = null)
	{
        try
        {
            $comAsignar = new EnviadaUsuario();
            $comAsignar->setComenviadaId($comenviada_id);
            $comAsignar->setRoluscomenviadaid($rolus_id); 
            $comAsignar->setEstadocomenviadaId($estadocomenviada_id);
            $comAsignar->setCargousuarioId($cusuario_id);
            $comAsignar->setUsuarioId($usuario_id);
            $comAsignar->setEstaAsignada($isAsig);
            $comAsignar->setFechaAsigna(date("Y-m-d G:i:s"));
            $comAsignar->setTipoprocesocomId($tprocomId);
            $comAsignar->save();
            //**************************************************************************************
            if($comAsignar->getPrimaryKey())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (PropelException $th)
        {
            return false;
        }
        catch (\Exception $th)
        {
            return false;
        }
        catch (\Throwable $th)
        {
            return false;
        }
    }
    
    public static function SetUrlWordFileModel($files_new,$is_files_old=false,$files_old_text="")
    {
        $url_files = "";
        /********************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_name    = sfContext::getInstance()->getUser()->getAttribute('username', '', 'subscriber');
        $dirRaiz         = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_object      = ParametroPeer::retrieveByPk(13)->getValortexto();
        $alias_object    = ParametroPeer::retrieveByPk(30)->getValortexto();
        $dirTmp          = ParametroPeer::retrieveByPk(65)->getValortexto();
        /********************************************************************************/
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder.'/'.$regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object.'/'.($usuario_name) . "/";  	
        $directorio_tmp = ComEnviada::NormalizePath($dirRaiz.$entidad_folder.'/'.$dirTmp.'/');
        $directorio_final = ComEnviada::NormalizePath($directorio_entidad.'/'.$directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text . "/" . $directorio_com;
        /********************************************************************************/
        if($is_files_old)
        {
            $url_files = $files_old_text;
        	$files_adjuntos = preg_split("/[,]+/",$files_old_text, -1, PREG_SPLIT_NO_EMPTY);            
        	$files_text = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j < count($files_text); $j++){
    			$file_name = basename($files_text[$j]);
    			$filenamesource = $directorio_tmp . $file_name;
    			$filenametarget = $directorio_final . $file_name;
    			if(file_exists($filenamesource)){
    				$directorio_final = ComEnviada::createPath($directorio_final);
    				copy($filenamesource, $filenametarget);
    				unlink($filenamesource);
    				$url_files .= $dirextorio_alias . $file_name . ",";
    			}
        	}
        }
        else
        {
        	$files_adjuntos = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
        	for($j=0; $j <= count($files_adjuntos); $j++){
        		if(trim($files_adjuntos[$j])){
        			$file_name = basename($files_adjuntos[$j]);
        			$filenamesource = $directorio_tmp . basename($files_adjuntos[$j]);
        			$filenametarget = $directorio_final . basename($files_adjuntos[$j]);
        			if(file_exists($filenamesource)){
        				$directorio_final = ComEnviada::createPath($directorio_final);
        				copy($filenamesource, $filenametarget);
        				unlink($filenamesource);
        				$url_files .= $dirextorio_alias . $file_name . ",";
        			}
        		}
        	}
        }
        return $url_files;
    }

    public static function readPlantillaWordCom($inputFileName, $deleteFile = false)
    {
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/bootstrap.php';
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/autoload.inc.php';
        $domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/src');      
        Settings::setPdfRenderer( Settings::PDF_RENDERER_DOMPDF, $domPdfPath );
        //*************************************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR;
        $directorio_upload = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        if (trim($inputFileName)){
            try {
                //$phpWord = new \PhpOffice\PhpWord\PhpWord();
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($inputFileName);
                $pathToSave = $directorio_tmp.DIRECTORY_SEPARATOR.md5(date("YmdGis"));
                // Adding an empty Section to the document...
                $section = $phpWord->addSection();
                // Adding Text element to the Section having font styled by default...
                $section->addText('SIN Radicar',array('name' => 'Tahoma', 'size' => 12));
                $templateProcessor->saveAs($pathToSave.'.docx');

                // Saving the document as OOXML file...
                //$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                //$objWriter->save($pathToSave.'.docx');
                
                //Save it
                $temp = \PhpOffice\PhpWord\IOFactory::load('result/Sample_23_TemplateBlock.docx');
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($temp , 'PDF');
                $xmlWriter->saveAs('results/Sample_23_TemplateBlock.pdf', TRUE);
            

                if(file_exists($pathToSave.'.pdf')){
                    return basename($pathToSave.'.pdf');
                }else{
                    return null;
                }

            } catch(Exception $e) {
                echo $e->getMessage();exit;
                return null;
            }
            //*********************************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        }else{
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = "Debe enviar un archivo";
        }
        //*************************************************************************************************************
        return $array_data;
    }

    public static function getListIntersadosByComId($comenviada_id)
    {
    	$c = new Criteria();
        $c->add(EnviadaInteresadosPeer::COMENVIADA_ID,$comenviada_id);
        return EnviadaInteresadosPeer::doSelect($c);
    }

    public static function getComObjectByRadicado($radicado,$periodo_id)
    {
    	$c = new Criteria();
        $c->add(ComEnviadaPeer::RADICADO,$radicado);
        $c->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $com_object = ComEnviadaPeer::doSelectOne($c);
    	//************************************************************************************
        if($com_object != null){
            return $com_object;
        }else{
            return null;
        }
    }

    public static function getDigitFormatFileExist($filepath,$filename)
    {
        $extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
        $digitDocumentFile = "";
        try{
            foreach($extensions as $format){
                $digitDocumentFile = $filepath. DIRECTORY_SEPARATOR .$filename.'.'.$format;
                if(file_exists($digitDocumentFile)){
                    return $digitDocumentFile;
                }
            }
        }catch(PropelException $ex){
            return $digitDocumentFile;
        }catch(\Exception $ex){
            return $digitDocumentFile;
        }catch(\Throwable $ex){
            return $digitDocumentFile;
        }
    }
    
    public static function initFolderDigit($regional_id,$periodo,$useTmp = false)
    {
    	$dirRaiz    = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_alias  = ParametroPeer::retrieveByPk(13)->getValortexto();
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";
    	$extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    	//************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
        //************************************************************************************
        $directorio_digit = simad_util::NormalizePath($dirRaiz.$entidad_text.DIRECTORY_SEPARATOR.$dir_alias.DIRECTORY_SEPARATOR.$periodo);
        $folder_attach = simad_util::createPath($directorio_digit);        
        $basic_path = $entidad_text.DIRECTORY_SEPARATOR.$dir_alias.DIRECTORY_SEPARATOR.$periodo;
        //************************************************************************************        
    	$array_url['basic_path'] = $basic_path;
    	$array_url['full_path'] = $folder_attach;
    	$array_url['temp_path'] = $dirTmp;
        $array_url['alias_web'] = "";
    	//************************************************************************************
    	return $array_url;
    }
    
    public static function initFolderAttach($regional_id,$usuario_id,$useTmp = false)
    {
        $array_url = array();
        //************************************************************************************
    	$dirRaiz    = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_attach = ParametroPeer::retrieveByPk(13)->getValortexto();
        $alias_str  = ParametroPeer::retrieveByPk(30)->getValortexto();
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";
    	//************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        $usuario_radica = UsuarioPeer::retrieveByPK($usuario_id);
        $username = trim($usuario_radica->getUserName());
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
        $directorio_attach = simad_util::NormalizePath($dirRaiz.$entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$username);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.DIRECTORY_SEPARATOR.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp);
        $basic_path = $entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$username;
        //************************************************************************************
        $folder_attach = simad_util::createPath($directorio_attach);
        $folder_tmp = $useTmp ? simad_util::createPath($directorio_tmp) : "";
        $alias_web = str_replace("\\","/",$basic_path);
        //************************************************************************************        
    	$array_url['basic_path'] = $basic_path;
    	$array_url['full_path'] = $folder_attach;
    	$array_url['temp_path'] = $folder_tmp;
        $array_url['alias_web'] = $alias_str.$alias_web;
    	//************************************************************************************
    	return $array_url;
        //return $useTmp ? array('folder_attach'=>$folder_attach,'folder_tmp'=>$folder_tmp) : $folder_attach;
    }

    public static function addComEnviada($params,$isBackObj = true)
    {
        try{
            $estadocomenviada_id = isset($params['estadocomenviada_id']) ? $params['estadocomenviada_id'] : 1;
            //****************************************************************************************************
            $com_enviada = new ComEnviada();
            $com_enviada_anterior = $com_enviada->copy();
            $radicado = 'Sin Radicar';
            $numero_radicacion = 0;
            //****************************************************************************************************
            $com_enviada->setRadicado($radicado);
            $com_enviada->setNumeroRadicacion($numero_radicacion);
            $com_enviada->setRegionalId(isset($params['regional_id']) ? $params['regional_id'] : null);
            $com_enviada->setDependenciaId(isset($params['dependencia_id']) ? $params['dependencia_id'] : null);
            $com_enviada->setCiudadId(isset($params['ciudad_id']) ? $params['ciudad_id'] : null);
            $com_enviada->setEstadocomenviadaId($estadocomenviada_id);
            $com_enviada->setEstadodigitalizacionId(isset($params['estadodigitalizacion_id']) ? $params['estadodigitalizacion_id'] : 1);
            $com_enviada->setPeriodoId(isset($params['periodo_id']) ? $params['periodo_id'] : date("Y"));
            $com_enviada->setAsunto(isset($params['asunto']) ? $params['asunto'] : null);
            $com_enviada->setFechaCreacion(date('Y-m-d G:i:s'));
            $com_enviada->setFolios(isset($params['folios']) ? $params['folios'] : null);
            $com_enviada->setAnexos(isset($params['anexos']) ? $params['anexos'] : null);
            $com_enviada->setFuncionarioDestino(isset($params['funcionario_destino']) ? $params['funcionario_destino'] : null);
            $com_enviada->setCargoDestinatario(isset($params['cargo_destinarario']) ? $params['cargo_destinarario'] : null);
            $com_enviada->setDireccionDestinatario(isset($params['direccion_destinarario']) ? $params['direccion_destinarario'] : null);
            $com_enviada->setMarcaVinculacion(isset($params['marca_vinculacion']) ? $params['marca_vinculacion'] : null);
            $com_enviada->setPrefijo(isset($params['prefijo']) ? $params['prefijo'] : null);
            $com_enviada->setObservacionesEnvio(isset($params['observaciones_envio']) ? $params['observaciones_envio'] : null);
            $com_enviada->setFirmaElectronica(isset($params['firma_electronica']) ? (!empty($params['firma_electronica']) ? 1 : 0) : 0);
            $com_enviada->setUseMembrete(isset($params['use_membrete']) ? $params['use_membrete'] : 1);
            $com_enviada->setEstaentregado(isset($params['esta_entregado']) ? $params['esta_entregado'] : 0);
            $com_enviada->setEsCopia(isset($params['es_copia']) ? $params['es_copia'] : 0);
            $com_enviada->setConsecutivoResp(isset($params['consecutivo_resp']) ? $params['consecutivo_resp'] : null);
            $com_enviada->setTipofirmadigitalId(isset($params['tipofirmadigital_id']) ? $params['tipofirmadigital_id'] : null);
            $com_enviada->setPrioridadcomId(isset($params['prioridadcom_id']) ? $params['prioridadcom_id'] : null);
            $com_enviada->setIsCreateWord(isset($params['iscreate_word']) ? $params['iscreate_word'] : 0);
            $com_enviada->setTipoEnvio(isset($params['tipo_envio']) ? $params['tipo_envio'] : null);
            $com_enviada->setExpedienteId(isset($params['expediente_id']) ? $params['expediente_id'] : null);
            $com_enviada->setMarcoNormativo(isset($params['marco_normativo']) ? $params['marco_normativo'] : null);
            $com_enviada->setNumeroFud(isset($params['numero_fud']) ? $params['numero_fud'] : null);
            $com_enviada->setFechaResolucion(isset($params['fecha_resolucion']) ? $params['fecha_resolucion'] : null);
			$com_enviada->setNumeroResolucion(isset($params['numero_resolucion']) ? $params['numero_resolucion'] : null);
            $com_enviada->setSuborigen(isset($params['suborigen']) ? $params['suborigen'] : null);
            $com_enviada->setNumradsysorigen(isset($params['radicado_sys_origen']) ? $params['radicado_sys_origen'] : null);
            $com_enviada->setTipoMasivo(isset($params['tipo_masivo']) ? (!empty($params['tipo_masivo']) ? 1 : 0) : 0);
            $com_enviada->setTipoDocumentalCod(isset($params['tipo_documental_cod']) ? $params['tipo_documental_cod'] : null);
            $com_enviada->setTipoIntegracion(isset($params['tipo_integracion']) ? trim($params['tipo_integracion']) : null);
            $com_enviada->setUrlFileWord(isset($params['UrlFileWord']) ? trim($params['UrlFileWord']) : null);
            $com_enviada->setIsCreateWord(isset($params['IsCreateWord']) ? trim($params['IsCreateWord']) : null);
            $com_enviada->save();
            //*****************************************************************************************************           
            return $com_enviada;
        }catch(PropelException $ex){
            return null;
        }catch(\Exception $ex){
			return null;
        }catch(\Throwable $ex){
			return null;
        }
    }

    public static function getNumeroRadicacion($regional,$dependencia,$periodo_id = null)
    {
        $periodo_id = $periodo_id == null ? date("Y") : $periodo_id;
		$parametro = ParametroPeer::retrieveByPk(2);
		$formaRad=$parametro->getCodigo();
        //****************************************************************************************************
		$conexion = Propel::getConnection();
		if($formaRad=="REG"){
		    $consulta = "SELECT MAX(%s) AS max FROM %s WHERE %s=".$regional."  AND %s=".$periodo_id." ";
		   	$consulta = sprintf($consulta, ComEnviadaPeer::NUMERO_RADICACION, ComEnviadaPeer::TABLE_NAME,ComEnviadaPeer::REGIONAL_ID,ComEnviadaPeer::PERIODO_ID);
		}elseif($formaRad=="DEP"){
		    $consulta = "SELECT MAX(%s) AS max FROM %s WHERE %s=".$dependencia."  AND %s=".$periodo_id." ";
		   	$consulta = sprintf($consulta, ComEnviadaPeer::NUMERO_RADICACION, ComEnviadaPeer::TABLE_NAME,ComEnviadaPeer::DEPENDENCIA_ID,ComEnviadaPeer::PERIODO_ID);
		}elseif($formaRad=="GEN"){
		    $consulta = "SELECT MAX(%s) AS max FROM %s WHERE  %s=".$periodo_id." ";
	   	    $consulta = sprintf($consulta, ComEnviadaPeer::NUMERO_RADICACION, ComEnviadaPeer::TABLE_NAME,ComEnviadaPeer::PERIODO_ID);
		}elseif($formaRad=="SEQ"){
            $consulta = "SELECT NEXT VALUE FOR [dbo].[GENERATE_CONS_COMENVIADA] AS max;";
		}else{
			$consulta = "SELECT MAX(%s) AS max FROM %s WHERE  %s=".$periodo_id." ";
	   	    $consulta = sprintf($consulta, ComEnviadaPeer::NUMERO_RADICACION, ComEnviadaPeer::TABLE_NAME,ComEnviadaPeer::PERIODO_ID);
		}
        //****************************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->max + 1);
	}

    public static function insertaEnviadaUsuarios($usuarios, $comenviada_id, $rol_id, $cargos, $estadocom = 1, $tprocesocom_id=1, $esAsignada=0){     
    	$isValid = false;
        try{
            if($rol_id == 1){
        		$usuario_id = $usuarios;
                $objEnviadaUsuario = EnviadaUsuarioPeer::getComObjectByUser($comenviada_id,$usuario_id,$cargos,$rol_id); 		
        		$objEnviadaUsuario->setUsuarioId($usuario_id);
        		$objEnviadaUsuario->setComenviadaId($comenviada_id);
        		$objEnviadaUsuario->setEstadocomenviadaId($estadocom);
        		$objEnviadaUsuario->setRolusComenviadaId($rol_id);
        		$objEnviadaUsuario->setCargousuarioId($cargos);
                $objEnviadaUsuario->setTipoprocesocomId(1);
                $objEnviadaUsuario->setEstaAsignada(0);
                $objEnviadaUsuario->setFechaAsigna(($objEnviadaUsuario->getFechaAsigna() ? $objEnviadaUsuario->getFechaAsigna() : date("Y-m-d G:i:s")));
        		$objEnviadaUsuario->save();
        	}else{
        		$arrUsuarios = explode(',',$usuarios);
                $arrCargos = explode(',',$cargos);
                $cont = 0;
        		foreach($arrUsuarios as $usuario_id){
        			if(!empty($usuario_id)){
                        $objEnviadaUsuario = EnviadaUsuarioPeer::getComObjectByUser($comenviada_id,$usuario_id,$arrCargos[$cont],$rol_id);
                        //************************************************************************
                        if($tprocesocom_id == 5){
                            $usuario_firma = UsuarioPeer::retrieveByPK($usuario_id);
                            if($usuario_firma->getFirmaDesatendida()){ 
                                $esAsignada = 0;
                                $objEnviadaUsuario->setFirmaAprueba(1);
                                $objEnviadaUsuario->setFechaAprueba(date("Y-m-d G:i:s"));
                            }else{
                                $esAsignada = 1;
                            }
                        }
                        //************************************************************************
        				$objEnviadaUsuario->setUsuarioId($usuario_id);
        				$objEnviadaUsuario->setComenviadaId($comenviada_id);
        				$objEnviadaUsuario->setRolusComenviadaId($rol_id);
        				$objEnviadaUsuario->setEstadocomenviadaId($estadocom);
                        $objEnviadaUsuario->setCargousuarioId($arrCargos[$cont]);
                        $objEnviadaUsuario->setTipoprocesocomId($tprocesocom_id);
                        $objEnviadaUsuario->setEstaAsignada($esAsignada);
                        $objEnviadaUsuario->setFechaAsigna(($objEnviadaUsuario->getFechaAsigna() ? $objEnviadaUsuario->getFechaAsigna() : date("Y-m-d G:i:s")));
                        //************************************************************************
        				$objEnviadaUsuario->save();
                        $cont++;					
        			}
      		    }
            }
            //************************************************************************************
            $isValid = true;
        }catch (Exception $e){
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            $isValid = false;
        }
        //****************************************************************************************
        return $isValid;
    }

    public static function insertaEnviadaUsuariosEdit($usuarios,$com_enviadaId,$rol,$cargos,$enviadausuario_id=null)
    {
        $idenviadaUsuario = "";
        //***************************************************************************************
        $c = new Criteria();
        if($rol==1){
            $usu=$usuarios;
            $c->add(EnviadaUsuarioPeer::USUARIO_ID,$usu);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol);
            $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$com_enviadaId);
            $objEnviadaUsuario = EnviadaUsuarioPeer::doSelectOne($c);        
            if($objEnviadaUsuario == null){
                $objEnviadaUsuarionew= new EnviadaUsuario;
                $objEnviadaUsuarionew->setUsuarioId($usu);
                $objEnviadaUsuarionew->setComenviadaId($com_enviadaId);
                $objEnviadaUsuarionew->setEstadocomenviadaId(2);
                $objEnviadaUsuarionew->setRolusComenviadaId($rol);
                $objEnviadaUsuarionew->setCargousuarioId($cargos);
                $objEnviadaUsuarionew->save();
                $idenviadaUsuario .= $objEnviadaUsuarionew->getPrimaryKey().",";
            }else{
                $objEnviadaUsuario->setUsuarioId($usu);
                $objEnviadaUsuario->setComenviadaId($com_enviadaId);
                //$objEnviadaUsuario->setEstadocomenviadaId(2);
                $objEnviadaUsuario->setRolusComenviadaId($rol);
                $objEnviadaUsuario->setCargousuarioId($cargos);
                $objEnviadaUsuario->save();
                $idenviadaUsuario .= $objEnviadaUsuario->getPrimaryKey().",";
            }                
        }else{
            $arrUsuarios=explode(',',$usuarios);
            $arrCargos=explode(',',$cargos);        
            $cont = 0;        
            foreach($arrUsuarios as $usu){
                if($usu!=0){
                    $c->add(EnviadaUsuarioPeer::USUARIO_ID,$usu);
                    $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$rol);
                    $c->add(EnviadaUsuarioPeer::COMENVIADA_ID,$com_enviadaId);
                    $objEnviadaUsuario=EnviadaUsuarioPeer::doSelectOne($c);
                    if($objEnviadaUsuario == null){
                        $objEnviadaUsuario= new EnviadaUsuario;
                        $objEnviadaUsuario->setUsuarioId($usu);
                        $objEnviadaUsuario->setComenviadaId($com_enviadaId);
                        $objEnviadaUsuario->setEstadocomenviadaId(2);
                        $objEnviadaUsuario->setRolusComenviadaId($rol);
                        $objEnviadaUsuario->setCargousuarioId($arrCargos[$cont]);
                        $objEnviadaUsuario->save();
                        $idenviadaUsuario .= $objEnviadaUsuario->getPrimaryKey().",";
                    }else{
                        $objEnviadaUsuario->setUsuarioId($usu);
                        $objEnviadaUsuario->setComenviadaId($com_enviadaId);
                        //$objEnviadaUsuario->setEstadocomenviadaId(2);
                        $objEnviadaUsuario->setRolusComenviadaId($rol);
                        $objEnviadaUsuario->setCargousuarioId($arrCargos[$cont]);
                        $objEnviadaUsuario->save();
                        $idenviadaUsuario .= $objEnviadaUsuario->getPrimaryKey().",";
                    }			  	
                    $cont++;					
                }            			
            }
                            
        }
        //***************************************************************************************
        return $idenviadaUsuario;
    }

    public static function borrarEnviadaUsuarios($com_enviadaId,$cambiar_radicador = 0)
    {
        $sql_exp = "";
        if($cambiar_radicador){
            $sql_exp = " AND ".EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID." <> 1 ";
        }
        $conexion = Propel::getConnection();
        $consulta = "DELETE FROM %s WHERE %s =".$com_enviadaId.$sql_exp;
        $sql      = sprintf($consulta,EnviadaUsuarioPeer::TABLE_NAME,EnviadaUsuarioPeer::COMENVIADA_ID);
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();		
    }
    
    public static function borrarEnviadaUsuariosNotIn($comenviada_id,$usuarios_str)
    {
        if(!empty($comenviada_id) && !empty($usuarios_str)){
            $arr_users = is_array($usuarios_str) ? $usuarios_str : preg_split("/[,]+/",$usuarios_str, -1, PREG_SPLIT_NO_EMPTY);
            if(!count($arr_users)){ return false; }
            //*********************************************************************************************
            $conexion = Propel::getConnection();
            $consulta = "DELETE FROM %s WHERE %s =".$comenviada_id." AND %s NOT IN(".implode(",",$arr_users).") AND %s <> 1";
            $sql      = sprintf($consulta,EnviadaUsuarioPeer::TABLE_NAME,EnviadaUsuarioPeer::COMENVIADA_ID,EnviadaUsuarioPeer::USUARIO_ID,EnviadaUsuarioPeer::FIRMA_APRUEBA);
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            return true;
        }else{
            return false;
        }
    }

    public static function borrarEnviadaUsuariosEdit($com_enviadaId,$usuariosArr)
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
        //******************************************************************************************
        $conexion = Propel::getConnection();
        $delete = " DELETE FROM %s WHERE %s =".$com_enviadaId." AND %s NOT IN(".$usuariosIn.")";
        $delete = sprintf($delete,EnviadaUsuarioPeer::TABLE_NAME,EnviadaUsuarioPeer::COMENVIADA_ID,EnviadaUsuarioPeer::ENVIADAUSUARIO_ID);        
        $sentencia = $conexion->prepare($delete);
        $sentencia->execute();
    }

    public static function getObjectComByRadicadoOrId($radicado = null,$pkcom_id = null)
    {
        $object = null;
        //******************************************************************************
        try {
            if(!empty($pkcom_id) && is_numeric($pkcom_id)){
                $object = ComEnviadaPeer::retrieveByPK($pkcom_id);
                if($object != null){ return $object; }
            }
            //**************************************************************************
            if(!empty($radicado)){
                $c = new Criteria();
                $c->add(ComEnviadaPeer::RADICADO,$radicado);
                $object = ComEnviadaPeer::doSelectOne($c);
            }
        } catch (\Throwable $th) {
            return null;
        }
        //******************************************************************************
        return $object;
    }

    public static function getObjectComByRadAndPkId($radicado = null,$pkcom_id = null)
    {
        $object = null;
        //******************************************************************************
        if(!empty($pkcom_id) && is_numeric($pkcom_id)){
            $c = new Criteria();
            $c->add(ComEnviadaPeer::RADICADO,$radicado);
            $c->add(ComEnviadaPeer::COMENVIADA_ID,$pkcom_id);
            $object = ComEnviadaPeer::doSelectOne($c);
            if($object != null){ return $object; }
        }
        //******************************************************************************
        return $object;
    }

    public static function getListComEnviadasByNuid($numero_identificacion = null,$notradicado = null, $radicado_com = null)
    {
        $objects = null;
        //******************************************************************************
        try {
            $c = new Criteria();
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaInteresadosPeer::COMENVIADA_ID);
            $c->addJoin(EnviadaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
            //**************************************************************************
            $c1 = $c->getNewCriterion(ComEnviadaPeer::ESTADOCOMENVIADA_ID, 2);//estado por leer - pero enviado	
            $c2 = $c->getNewCriterion(ComEnviadaPeer::ESTADOCOMENVIADA_ID, 3);//estado por leido - pero enviado
            $c1->addOr($c2);
            $c->add($c1);
            //**************************************************************************
            $c->add(InteresadosPeer::NUMERO_IDENTIFICACION,$numero_identificacion);
            //**************************************************************************
            if(!empty($notradicado)){ $c->add(ComEnviadaPeer::RADICADO,$notradicado,Criteria::NOT_EQUAL); }
            //**************************************************************************
            if(!empty($radicado_com)){ $c->add(ComEnviadaPeer::RADICADO,$radicado_com); }
            //**************************************************************************
            $objects = ComEnviadaPeer::doSelect($c);
        } catch (PropelException $th) {
            $objects = null;
        } catch (\Exception $th) {
            $objects = null;
        } catch (\Throwable $th) {
            $objects = null;
        }
        //******************************************************************************
        return $objects;
    }

	public static function isExistComByNumResolucion($numero_resolucion = null,$list_nuids = array(), $comenviada_id = null, $IsTotal = true)
    {
        $object = true;
		$nulog = false;
        //**********************************************************************************
		try {
			if(!empty($numero_resolucion) && count($list_nuids)){
				$c = new Criteria();
				$c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaInteresadosPeer::COMENVIADA_ID);
				$c->addJoin(EnviadaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
				//**************************************************************************
				$c->add(ComEnviadaPeer::ESTADOCOMENVIADA_ID,array(1,4),Criteria::NOT_IN);
				$c->add(ComEnviadaPeer::NUMERO_RESOLUCION,$numero_resolucion);
				$c->add(InteresadosPeer::NUMERO_IDENTIFICACION,$list_nuids,Criteria::IN);
				if(!empty($comenviada_id)){ $c->add(ComEnviadaPeer::COMENVIADA_ID,$comenviada_id,Criteria::NOT_EQUAL); }
				//**************************************************************************
                if($IsTotal == false){
                    $list_com = array();
                    //**********************************************************************
                    $c->clearSelectColumns();
                    $c->addSelectColumn(ComEnviadaPeer::RADICADO);
                    $stmt = ComEnviadaPeer::doSelectStmt($c);
                    while($object = $stmt->fetch()){
                        $list_com[] = $object[0];
                    }
                    //**********************************************************************                    
                    $results = count($list_com) ? implode(",",$list_com) : 0;
                }else{
                    $results = ComEnviadaPeer::doCount($c);
                }
				//**************************************************************************
				if($nulog){
					/*$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'ComEnviadaResoByInt.log';
					$message_log = sprintf("numero_resolucion: %s, numeros identificacion: %s, response_query: %s",$numero_resolucion,implode(",",$list_nuids),$results);
					simad_util::writetolog($logname,$message_log);*/
				}
				//**************************************************************************
				return $results;
			}
        } catch (PropelException $th) {
            $object = true;
		} catch (Exception $th) {
            $object = true;
        }
        //**********************************************************************************
        return $object;
    }

    public static function readFileComCombined($inputFileName, $readHeaders=true, $deleteFile = false, $rows_read = 1000)
    {
		try {
	        require_once sfConfig::get('sf_lib_dir') . '/Spout/Autoloader/autoload.php';
	        //*************************************************************************************************************
	        $simad_util = new simad_util();
	        $sheetData = array();
	        $msg_error = "";
	        $array_data = array();
	        $sheetData = array();
	        //*************************************************************************************************************
	        if (trim($inputFileName) && file_exists($inputFileName)){
	            try {
	                $reader = ReaderEntityFactory::createXLSXReader();
	                $reader->setShouldPreserveEmptyRows(false);
	                $reader->open($inputFileName);
	            } catch(Exception $e) {
	                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	                return null;
	            }
	            //*********************************************************************************************************
	            foreach ($reader->getSheetIterator() as $sheet) {
	                foreach ($sheet->getRowIterator() as $rowNumber => $rows) {
	                    $cells = $rows->getCells();
	                    $rowNumber -= 1;
	                    if($rowNumber > $rows_read){ break 2; }
	                    // do not empty row
	                    $errors = array_filter($cells);
	                    if (empty($errors)) {
	                        continue;
	                    }
	                    //*************************************************************************************************
	                    $cells_data = array_map(function($cell) {
	                        return $cell->getValue();
	                    }, $rows->getCells());
	                    //*************************************************************************************************
	                    // do stuff with the row
	                    if($readHeaders){
	                        $array_data['headerList'] = array_merge(array("rowNumber"),$cells_data);
	                        $readHeaders = false;
	                    }else{
	                        $sheetData[] = array_merge(array($rowNumber),$cells_data);
	                    }
	                }
	                break;
	            }
	            //*********************************************************************************************************            
	            $sheetDataSerialize = $simad_util->getArrayForSend($sheetData);           
	            $array_data['sheetData'] = $sheetData;
	            $array_data['sheetDataSerialize'] = $sheetDataSerialize;
	            $array_data['cod_msg'] = 0;
	            $array_data['msg_error'] = "";
	            //*********************************************************************************************************
	            if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
	        }else{
	            $msg_error = "Debe seleccionar el archivo que contiene las registros para radicar";
	            $array_data['cod_msg'] = 1;
	            $array_data['msg_error'] = $msg_error;
	        }
	        //*************************************************************************************************************
	        return $array_data;
		} catch (\PDOException $th) {
            return array();
        } catch (\Exception $th) {
			//echo $th->getMessage();exit;
            return array();
		}
    }

    /**
     * objectActions::getListComRadicarByUser()
    * genera una lista de comunicaciones pendientes por radicar de un usuario especifico
    * @param $usuario_id id del usuario que se desea consultar
    * @return mixed array lista con los resultados
    */
    public static function getListComRadicarByUser($usuario_id,$comenviada_id = null)
    {
        try {
            $estadocomenviada_id = 1;
            $roluscomenviada_id = 2;
            //*************************************************************************************************
            $c = new Criteria();
            $c->setDistinct();
            //$c->setLimit(100);
            //*************************************************************************************************
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,$roluscomenviada_id);
            $c->add(EnviadaUsuarioPeer::USUARIO_ID,$usuario_id);
            $c->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,$estadocomenviada_id);
            $c->add(ComEnviadaPeer::ESTADOCOMENVIADA_ID,$estadocomenviada_id);
            $c->add(ComEnviadaPeer::MARCA,$usuario_id);
            $c->add(ComEnviadaPeer::PERIODO_ID,date("Y"));
            $c->add(ComEnviadaPeer::RADICAR_INTERESADO,0);
            //$c->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);
            //$c->addDescendingOrderByColumn(ComEnviadaPeer::FECHA_CREACION);
			//*************************************************************************************************
            if(!empty($comenviada_id)){
                $c->add(ComEnviadaPeer::COMENVIADA_ID,$comenviada_id);
            }
            //*************************************************************************************************
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $c->clearSelectColumns();
            //*************************************************************************************************
            $c->addSelectColumn(ComEnviadaPeer::COMENVIADA_ID);//0
            $c->addSelectColumn(ComEnviadaPeer::RADICADO);//1
            $c->addSelectColumn(ComEnviadaPeer::ASUNTO);//2
            $c->addSelectColumn(ComEnviadaPeer::FECHA_CREACION);//3
            $c->addSelectColumn(ComEnviadaPeer::MARCA);//4
            //*************************************************************************************************        
            $sql_custom = "(SELECT COUNT(".EnviadaUsuarioPeer::ENVIADAUSUARIO_ID;
            $sql_custom .= ") FROM ".EnviadaUsuarioPeer::TABLE_NAME;
            $sql_custom .= " WHERE ".EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID." IN(2,4,5)";
            $sql_custom .= " AND ".EnviadaUsuarioPeer::USUARIO_ID." != ".$usuario_id;
            $sql_custom .= " AND ".EnviadaUsuarioPeer::FIRMA_APRUEBA." = 0";
            $sql_custom .= " AND ".ComEnviadaPeer::COMENVIADA_ID." = ".EnviadaUsuarioPeer::COMENVIADA_ID.")";
            $c->addAsColumn('IS_RADICAR',$sql_custom);
            //*************************************************************************************************
            $list_comenviadas = ComEnviadaPeer::doSelectStmt($c);
            return $list_comenviadas->fetchAll();
        } catch (\PDOException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
		}
    }
	
	/**
     * objectActions::getListComMarcadosByUser()
    * genera una lista de comunicaciones marcadas por el usuario
    * @param $usuario_id id del usuario que se desea consultar
    * @return mixed array lista con los resultados
    */
    public static function getListComMarcadosByUser($usuario_id = -1,$max_rows=10, $estado_firma = array(0,3))
    {
        try {
            $c = new Criteria();
            $c->setDistinct();
            $c->setLimit($max_rows);
            //*************************************************************************************************
            $c->add(ComEnviadaPeer::MARCA,$usuario_id);
			$c->add(ComEnviadaPeer::FIRMADO_DIGITAL,$estado_firma,Criteria::IN);
            //$c->add(ComEnviadaPeer::PERIODO_ID,date("Y"));
			$c->addDescendingOrderByColumn(ComEnviadaPeer::FECHA_CREACION);
            //*************************************************************************************************
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $c->clearSelectColumns();
            //*************************************************************************************************
            $c->addSelectColumn(ComEnviadaPeer::COMENVIADA_ID);//0
            $c->addSelectColumn(ComEnviadaPeer::RADICADO);//1
            $c->addSelectColumn(ComEnviadaPeer::ASUNTO);//2
            $c->addSelectColumn(ComEnviadaPeer::FECHA_CREACION);//3
            $c->addSelectColumn(ComEnviadaPeer::MARCA);//4
			$c->addSelectColumn(ComEnviadaPeer::NUMERO_RADICACION);//5
			$c->addSelectColumn(ComEnviadaPeer::FIRMADO_DIGITAL);//6
            //*************************************************************************************************
            $list_comenviadas = ComEnviadaPeer::doSelectStmt($c);
            return $list_comenviadas->fetchAll();
        } catch (\PDOException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
		}
    }
}
