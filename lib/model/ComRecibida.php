<?php

/**
 * Subclass for representing a row from the 'COM_RECIBIDA' table.
 *
 * 
 *
 * @package lib.model
 */ 
		
use \PhpOffice\PhpWord\Settings;
use \setasign\Fpdi\Fpdi;
use \mikehaertl\pdftk\Pdf;

class ComRecibida extends BaseComRecibida
{
	/**
    * objectActions::documentOcrProcess()
    * Aplica el proceso de ocr al documento indexado
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de ocr')
    */ 
    public function documentOcrProcess($file_path = null)
    {
		require_once(sfConfig::get('sf_lib_dir').'/ShellWrapper/autoload.php');
		//*******************************************************************************************************
        try{           
            if(file_exists($file_path))
            {
				$file_info = pathinfo($file_path);
				if(!simad_util::CheckIsValidFormatFile($file_path)){
					$response_process = array('httpStatus' => 400, 'message' => 'El archivo enviado no es un pdf o el pdf no es valido');
				}
				//***********************************************************************************************
				$tmpdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(uniqid().time());
				simad_util::createPath($tmpdir);
				$target_focr = $tmpdir.DIRECTORY_SEPARATOR.md5(uniqid().time()).".".$file_info['extension'];
				//***********************************************************************************************
				$read_sections = array('ocr_engine_cli_command','ocr_engine_cli_arguments');
				$ini_array = simad_util::readConfigFileApp($read_sections);
				$ocr_engine = isset($ini_array['ocr_engine_cli_command']) ? $ini_array['ocr_engine_cli_command'] : null;
				$ocr_arguments = isset($ini_array['ocr_engine_cli_arguments']) ? $ini_array['ocr_engine_cli_arguments'] : null;
				//***********************************************************************************************
				//$mycmd = $ocr_engine.' -l eng+spa --rotate-pages --deskew --output-type pdfa-3 "'.$file_path.'" "'.$target_focr.'" 2>&1';
				$mycmd = $ocr_engine.' '.$ocr_arguments.' "'.$file_path.'" "'.$target_focr.'" 2>&1';
				$last_line = shell_exec($mycmd);
				//***********************************************************************************************
				$isCopy_focr = false;
				if(file_exists($target_focr) && is_readable($target_focr)){
					$filecontent = file_get_contents($target_focr);
					$pdfOcr_readable = false;
					//*******************************************************************************************
					if (preg_match("/^%PDF-1./", $filecontent)) {
						$pdfOcr_readable = true;
					}
					//*******************************************************************************************
					if($pdfOcr_readable){
						if(unlink($file_path))
							$isCopy_focr = @rename($target_focr,$file_path); 
					}
				}
				//***********************************************************************************************
				if($isCopy_focr)
					$response_process = array('httpStatus' => 200, 'message' => 'El proceso de OCR se realizo con exito');
				else
					$response_process = array('httpStatus' => 400, 'message' => 'Ocurrio un error interno en el servidor, no se realizo el OCR del archivo');
				//***********************************************************************************************
				simad_util::deleteDirAndFiles($tmpdir);
            }
			else
			{
				$response_process = array('httpStatus' => 400, 'message' => 'El archivo no existe en la ruta especificada');
            }
            //***************************************************************************************************
            return $response_process;
		} catch (\InvalidArgumentException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }catch (\IOException $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
		}catch (\Exception $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }
	
    public function saveNewComrecibidaDuplicado($tmpfname)
    {
        try
        {
            $ini_array = parse_ini_file(sfConfig::get('sf_config_dir')."/app.ini");
            $campos_metadatos_bd = isset($ini_array['campos_metadatos_bd']) ? (array) $ini_array['campos_metadatos_bd'] : array();
            //**********************************************************************************************************************
            if (empty($campos_metadatos_bd)) {
                return null;
            }
            //**********************************************************************************************************************
            $comrecibida_id = $this->getPrimaryKey();
            if (!is_numeric($comrecibida_id) || $comrecibida_id <= 0) {
                throw new Exception('ID de comunicación inválido');
            }
            $comrecibida_id = (int) $comrecibida_id;
            //**********************************************************************************************************************
            $metadata_sql_info = simad_util::generarSqlCamposMetadatos($campos_metadatos_bd);
            //**********************************************************************************************************************
            $metadata_sql_info['sql'] = sprintf(
                "%s WHERE %s = ?",  // ← Placeholder ?
                $metadata_sql_info['sql'],
                ComRecibidaPeer::COMRECIBIDA_ID
            );
            //**********************************************************************************************************************
            $sqlinfo_sql = $metadata_sql_info['sql'];
            //**********************************************************************************************************************
            // Pasar SQL y parámetros SEPARADOS
            $resultset = ComRecibidaPeer::getHashMetadata($sqlinfo_sql, [$comrecibida_id]);
            //**********************************************************************************************************************
            if ($resultset === false || empty($resultset)) {
                throw new Exception('No se encontraron datos para generar el hash');
            }
            //**********************************************************************************************************************
            $metadata_encrypt = "";
            for ($i = 0; $i < count($resultset); $i++) { 
                $metadata_encrypt .= $resultset[$i];
            }
            //**********************************************************************************************************************
            if (empty($metadata_encrypt)) {
                throw new Exception('No hay metadatos disponibles para encriptar');
            }
            //**********************************************************************************************************************
            // Metadatos encriptados
            $read_sections = array('com_recibida_watermark','keyprivate_watermark');
            $app_cfg = simad_util::readConfigFileApp($read_sections);
            //**********************************************************************************************************************
            $hash_metadata = hash('sha256', $metadata_encrypt);
            $hash_maindocument = simad_util::calcularHashPorChunks($tmpfname);
            //**********************************************************************************************************************
            $params = [];
            $params['comrecibida_id'] = $comrecibida_id;
            $params['hash_metadata'] = $hash_metadata;
            $params['hash_maindocument'] = $hash_maindocument;
            //**********************************************************************************************************************
            $ya_existe = ComrecibidaDuplicadosPeer::existeRegistroActual($comrecibida_id);
            //**********************************************************************************************************************
            if($ya_existe === true)
            {
                ComrecibidaDuplicadosPeer::updateComRecDuplicate($params);
            }
            else
            {
                ComrecibidaDuplicadosPeer::addComRecDuplicate($params);
            }
            //**********************************************************************************************************************
            return array('error' => false, 'message' => 'Proceso completado exitosamente', 'object' => null);
        }
        catch(PropelException $ex)
        {
            error_log('PropelException: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error en el acceso a los datos', 'object' => null);
        }
        catch(\Exception $ex)
        {
            error_log('Exception: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error interno de la aplicación', 'object' => null);
        }
        catch(\Throwable $ex)
        {
            error_log('Throwable: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error interno del servidor', 'object' => null);
        }
    }

    public function buscarDuplicados()
    {
        try
        {
            $duplicados_conresp = ComrecibidaDuplicadosPeer::existeDuplicadosConRespuesta($this->getPrimaryKey());
            //**********************************************************************************************************************
            if(count($duplicados_conresp) > 0)
            {
                if(!empty($duplicados_conresp[0]['RADICADO_COMENVIADA']))
                {
                    $radicados_comrecibida = array_unique(array_map(function($rad_comrec) { return $rad_comrec['RADICADO_COMRECIBIDA']; }, $duplicados_conresp));
                    $radicados_comenviada = array_unique(array_map(function($rad_comenv) { return $rad_comenv['RADICADO_COMENVIADA']; }, $duplicados_conresp));
                }
                else
                {
                    $radicados_comrecibida = [];
                    foreach($duplicados_conresp as $row_dup)
                    {
                        $comrecibida_obj = ComrecibidaPeer::retrieveByPk($row_dup['COMRECIBIDA_ID']);
                        if($comrecibida_obj !== null)
                        {
                            $radicados_comrecibida[] = $comrecibida_obj->getRadicado();
                        }
                    }
                }
                //**********************************************************************************************************************
                $radicados_all = [];
                $radicados_all['radicados_comrecibida'] = $radicados_comrecibida;
                if(!empty($radicados_comenviada))
                {
                    $radicados_all['radicados_comenviada'] = $radicados_comenviada;
                }
                //**********************************************************************************************************************
                return $radicados_all;
            }
            else
            {
                return array();
            }
        }
        catch(PropelException $ex)
        {
            error_log('PropelException: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error en el acceso a los datos', 'object' => null);
        }
        catch(\Exception $ex)
        {
            error_log('Exception: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error interno de la aplicación', 'object' => null);
        }
        catch(\Throwable $ex)
        {
            error_log('Throwable: ' . $ex->getMessage());
            return array('error' => true, 'message' => 'Error interno del servidor', 'object' => null);
        }
    }

    /**
     * ComRecibida::addExpedienteAutoByReglas()
     * realiza la verificacion y automatizacion para la creacion del expediente
     * @return mixed $respuesta_vars lista de datos del flujo
     */
    public function addExpedienteAutoByReglas($usaurio_origen = null)
	{
        $usuariologuiado = empty($usaurio_origen) ? sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber') : $usaurio_origen;
        //******************************************************************************************************
		try
		{
            if(!empty($this->getMarcaVinculacion()) || !empty($this->getContenidodocId())){
                return array('error' => false, 'message' => 'El documento ya esta archivado');
            }
            //**************************************************************************************************
            $ini_array = simad_util::readConfigFileApp();
            $automatice_expediente = isset($ini_array['app_automatice_expediente']) ? $ini_array['app_automatice_expediente'] : false;
            if($automatice_expediente == false){
                return array('error' => false, 'message' => 'La automatización de expedientes esta deshabilitada');
            }
            //**************************************************************************************************
            $list_exprules = ExpedienteReglaEtiquetaPeer::getExisteRuleByTipoCom(ModulesEnable::ComRecibida,$this->getTipocomrecibidaId());
            //**************************************************************************************************
            if(count($list_exprules) <= 0){
                return array('error' => false, 'message' => 'La automatización de expedientes esta deshabilitada');
            }
            //**************************************************************************************************
            $list_unidaddocs = array();
            $list_fileds = array();
            $peer_class = $this->getPeer();
            $current_metadato = array();
            //**************************************************************************************************
            $pk = $peer_class::getPrimaryKeyColumnName();
            $incomingRelations = $peer_class::getIncomingRelations($peer_class::TABLE_NAME);
            $main_table = strtoupper($peer_class::TABLE_NAME);
            $expediente_reglas = null;
            //**************************************************************************************************
            foreach ($list_exprules as $row_unidaddoc) {
                $regla_compuesta = $row_unidaddoc->getExpedienteReglas()->getTextoCompuesto();
                $table_origen = $row_unidaddoc->getExpedienteEtiqueta()->getTablaOrigen();
                $filed_origen = $row_unidaddoc->getExpedienteEtiqueta()->getCampoOrigen();
                $object_class = $row_unidaddoc->getExpedienteEtiqueta()->getObjectClass();
                $object_method = $row_unidaddoc->getExpedienteEtiqueta()->getNombreInterno();
                //**********************************************************************************************
                $expediente_reglas = $row_unidaddoc->getExpedienteReglas();
                //**********************************************************************************************
                $current_metadato['texto_compuesto'] = $regla_compuesta;
                $current_metadato['texto_base'] = $row_unidaddoc->getExpedienteReglas()->getTextoBase();
                $list_fileds['field_select'] = sprintf("%s.%s",$row_unidaddoc->getExpedienteEtiqueta()->getTablaOrigen(),$filed_origen);
                if($main_table != $table_origen){
                    $list_fileds['table_join'] = sprintf("%s.%s",$row_unidaddoc->getExpedienteEtiqueta()->getTablaOrigen(),$filed_origen);
                    if($table_origen == InteresadosPeer::TABLE_NAME){
                        //MEJORAR ESTO PARA HACERLOS DIMINICO EN SU MAYORIA
                        $idata_relations = ComRecibidaPeer::getListIntersadosByComId($this->getPrimaryKey());
                        if(count($idata_relations)){
                            $current_metadato[strtoupper($object_method)] = call_user_func(array($idata_relations[0]->getInteresados(), sprintf("get%s",$object_method)));
                        }
                    }else{
                        $current_metadato[strtoupper($object_method)] = call_user_func(array($this, sprintf("get%s",$object_method)));
                    }
                }else{
                    $current_metadato[strtoupper($object_method)] = call_user_func(array($this, sprintf("get%s",$object_method)));
                }
            }
            //**************************************************************************************************
            $titulo_expediente = $current_metadato['texto_compuesto'];
            foreach ($current_metadato as $key => $value) {
                $titulo_expediente = str_replace($key,$value,$titulo_expediente);
            }
            //**************************************************************************************************
            if(empty($titulo_expediente)){
                return array('error' => false, 'message' => 'El nombre del expediente');
            }
            //**************************************************************************************************
            $iunidad_documental = UnidadDocumentalPeer::getExpedienteByTitulo($titulo_expediente);
            if(count($iunidad_documental) > 0){
                $unidad_documental = $iunidad_documental[0];
            }
            //**************************************************************************************************
            if($expediente_reglas->getTipoexpedientereglasId() == TipoAutomatizacionUnidadDoc::Segerencia){
                return array('error' => false, 'message' => 'La regla de automatización se basa en una segerencia');
            }
            //**************************************************************************************************
            $tipodocumental_id = $expediente_reglas->getTipodocumentalId();
            //**************************************************************************************************
			if($unidad_documental == null)
			{
				$tipo_com_recibida_id = $this->getTipocomrecibidaId();
				$tipo_com_recibida = TipoComRecibidaPeer::retrieveByPK($tipo_com_recibida_id);
				$tipodocumental_id = $expediente_reglas->getTipodocumentalId() ? $expediente_reglas->getTipodocumentalId() : $tipo_com_recibida->getTipodocumentalId();
				$tipo_documental = TipoDocumentalPeer::retrieveByPK($tipodocumental_id);
                //***********************************************************************************************
                if(empty($tipo_documental)){
                    return array('error'=>true,'message'=>'El tipo documental no existe');
                }
                //***********************************************************************************************
				$subserie_id = $tipo_documental->getSubserieId(); 
				$subserie = SubseriePeer::retrieveByPK($subserie_id);
				//***********************************************************************************************
				$params = array();
				$params['titulo'] = $titulo_expediente;
				$params['fase_archivo'] = FasesArchivo::Gestion;
				$params['regional_id'] = $this->getRegionalId();
				$params['soporte_documental'] = 2;
				$params['estado_documental'] = 1;
				$params['frecuencia_documental'] = 2;
				$params['medio_conservacion'] = 1;
				$params['subserie_id'] = $subserie->getPrimaryKey();  
				$params['contenido'] = $this->getObservaciones();
				$params['id_creador'] = $usuariologuiado;
				$params['id_responsable'] = $usuariologuiado;
				$params['id_inventariador'] = $usuariologuiado;
				$params['folios'] = !empty($this->getFolios()) ? $this->getFolios() : 1;
                $params['fecha_apertura'] = $this->getFechaCreacion("Y-m-d");
                $params['volumen'] = 1;
				//***********************************************************************************************
                $comlist_interesados = array();
                $current_interesados = ComRecibidaPeer::getListIntersadosByComId($this->getPrimaryKey());
                foreach ($current_interesados as $row_interesado) {
                    $comlist_interesados[] = $row_interesado->getInteresadoId();
                }
                $params['already_interesados'] = count($comlist_interesados) ? $comlist_interesados : null;
				//***********************************************************************************************
				$response_process = UnidadDocumentalPeer::addUnidadDocumental($params);
                //***********************************************************************************************
                if($response_process['error'] === false){
                    $unidad_documental = $response_process['object'];
                    TransferenciaPeer::addAutoTransfAndContenido($unidad_documental->getPrimaryKey(),$tipodocumental_id,
                        $this->getPrimaryKey(),OrigenTransferenciaCom::ComRecibida,$usuariologuiado);
                    //*******************************************************************************************
                    return array('error' => false,'message' => 'El documento se asocio al expediente', 'object' => $unidad_documental['object']);
                }else{
                    return array('error' => false, 'message' => 'Ocurrio un error al crear el expediente', 'object' => null);
                }
			}else{
                $tipo_documental = TipoDocumentalPeer::retrieveByPK($tipodocumental_id);
                //***********************************************************************************************
                TransferenciaPeer::addAutoTransfAndContenido($unidad_documental->getPrimaryKey(),$tipodocumental_id,
                        $this->getPrimaryKey(),OrigenTransferenciaCom::ComRecibida,$usuariologuiado);
                //***********************************************************************************************
                return array('error' => false,'message' => 'El documento se asocio al expediente', 'object' => $unidad_documental['object']);
            }
		}
		catch(PropelException $ex)
		{
            return array('error'=>false,'message'=>'Error en el acceso a los datos', 'object' => null);
			return $ex->getMessage();
		}
		catch(\Exception $ex)
		{
            return array('error'=>false,'message'=>'Error interno la aplicación', 'object' => null);
		}
        catch(\Throwable $ex)
		{
            return array('error'=>false,'message'=>'Error interno del servidor', 'object' => null);
		}
    }

    public function getPathImageDigitByCom()
    {
        try {
            $digitDocumentFile = "";		
            $dirRaiz = ParametroPeer::retrieveByPk(27)->getValortexto();
            $dir_adj_object  = ParametroPeer::retrieveByPk(14)->getValortexto();
            //$alias_com_object  = ParametroPeer::retrieveByPk(28)->getValortexto();
            $extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
            $periodo_com =  $this->getPeriodoId();
            //******************************************************************************************
            //$directorio_raiz = $dirRaiz;
            $directorio_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : $dirRaiz;
            $entidad_text = trim($this->getRegional()->getEntidad()->getDirectorioName());
            $regional_text = trim($this->getRegional()->getDirectorioName());
            $entidad_text = empty($entidad_text) ? "" : (empty($regional_text) ?  $entidad_text : $entidad_text.'/'.$regional_text);
            $directorio_entidad = empty($entidad_text) ? $directorio_raiz : $directorio_raiz.$entidad_text."/";
            $directorio_com = $dir_adj_object."/".$periodo_com."/";
            $directorio_final = $directorio_entidad.$directorio_com;
            $file_name = $this->getRadicado();
            //******************************************************************************************			
            foreach($extensions as $format){
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

    public function getUriImageDigitById()
    {
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
                $extension = pathinfo($digitDocumentFile, PATHINFO_EXTENSION);
                if (copy($digitDocumentFile, $pathtmp)) {
                    $url_viewer = $base_web.'/tmp/'.$filename_tmp;
                    if(strtolower($extension) == 'pdf'){
                        $url_viewer = $base_web.'/viewerEx.php?fileview='.$filename_tmp;
                        if($this->getEstadocomrecibidaId() == 14){
							$tanulado = "DOCUMENTO ANULADO";
                            $fanulado = $this->getFechaDeAnulacion();
                            $pdfTools = new PdfTools();
                            $fnewTmp = $pdfTools->setPdfWatherMark($pathtmp,$tanulado,$fanulado);
                            //$url_viewer .= '&qvars='.md5($filename_tmp.'anulado');
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
        } catch (Exception $th) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
        }
        //************************************************************************************************
        return $response_process;
    }

    public function getUsuariosListCom()
    {
		$list_users = array();$copias_list = array();
		//************************************************************************************************
		try{			
			//foreach($this->getComrecibidaUsuariosJoinEstadoComRecibida() as $usuario_recibida){
			foreach($this->getComrecibidaUsuarios() as $usuario_recibida){
				if($usuario_recibida->getRolusuariorecibidaid() == 1){
					$list_users['radicador'] = $usuario_recibida->getUsuario()->getNombreAll();
					$list_users['dependencia_radicador'] = $usuario_recibida->getUsuario()->getDependencia()->getNombreCustom();
				}elseif($usuario_recibida->getRolusuariorecibidaid() == 2){
					if($usuario_recibida->getEstaAsignada()){
						$list_users['destinatario_asignado'] = $usuario_recibida->getUsuario()->getNombreAll();
						$list_users['dependencia_asignado'] = $usuario_recibida->getUsuario()->getDependenciaId();
						$list_users['nuid_usuario_actual'] = $usuario_recibida->getUsuario()->getCedula();
					}else{
						$list_users['destinatario'] = $usuario_recibida->getUsuario()->getNombreAll();
						$list_users['dependencia'] = $usuario_recibida->getUsuario()->getDependencia()->getNombreCustom();
					}
				}else{
					$copias_list[] = $usuario_recibida->getUsuario()->getNombreAll();
				}
				//****************************************************************************************
				$list_users['firmas'] = array();
				$list_users['copias'] = implode(",",$copias_list);
			}
		} catch (PropelException $ex) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
			//print($ex->getMessage() . PHP_EOL);
		} catch (Exception $ex) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
			//print($ex->getMessage() . PHP_EOL);
        }
		//************************************************************************************************
		return $list_users;
    }
    
    public function getBitacoraHistCom()
    {
        $list_users = array();$copias_list = array();$evento_list = array();
        foreach($this->getComrecibidaUsuariosJoinEstadoComRecibida() as $usuario_recibida){
            $evento = array();
            $evento['destinatario'] = $usuario_recibida->getUsuario()->getNombreAll();
            $evento['dependencia'] = $usuario_recibida->getUsuario()->getDependencia()->getNombreCustom();
            $evento['fecha_recibido'] = $usuario_recibida->getFechaAsigna();
            $evento['fecha_lectura'] = $usuario_recibida->getFechaLectura();
            $evento['proceso'] = $usuario_recibida->getTipoProcesoCom()->getDescripcion();
			$evento['proceso'] = $usuario_recibida->getTipoprocesocomId() ? $usuario_recibida->getTipoProcesoCom()->getDescripcion() : "";
            $evento_list[] = $evento;
            //**************************************************************************
            if($usuario_recibida->getRolusuariorecibidaid() == 1){
                $list_users['radicador'] = $usuario_recibida->getUsuario()->getNombreAll();
                $list_users['dependencia_radicador'] = $usuario_recibida->getUsuario()->getDependencia()->getNombreCustom();
            }elseif($usuario_recibida->getRolusuariorecibidaid() == 2){
				if($usuario_recibida->getEstaAsignada()){
                    $list_users['destinatario_actual'] = $usuario_recibida->getUsuario()->getNombreAll();
					$list_users['dependencia_actual'] = $usuario_recibida->getUsuario()->getDependenciaId();
					$list_users['nuid_usuario_actual'] = $usuario_recibida->getUsuario()->getCedula();
				}
            }else{
                $copias_list[] = $usuario_recibida->getUsuario()->getNombreAll();
            }
        }
        //******************************************************************************
        $list_users['copias'] = implode(",",$copias_list);
        $list_users['eventos_list'] = $evento_list;
        //******************************************************************************
        return $list_users;
    }

    public function getRadicadoFormat($regional_id,$dependencia_id,$entidad_id=0,$use_regional=true)
	{
		$entidad_object = trim($entidad_id) ? EntidadPeer::retrieveByPk(trim($entidad_id)) : null;
		$entidad = "";
        $data_rad = array();
		//****************************************************************************************
		if($entidad_object != null && !$use_regional){
            $code_init = trim($entidad_object->getCodigo()) ? trim($entidad_object->getCodigo()) : "";
        }elseif(!$use_regional && is_numeric($dependencia_id)){
            //$code_init = ComRecibidaPeer::getAbrevDependencia($usuario_id);
            $code_init = DependenciaPeer::getCodigoDependenciaById($dependencia_id);
		}else{
            $code_init = ComRecibida::getRadCodByRegional($regional_id);
            $code_init = is_numeric($code_init) ? sprintf("%02d",$code_init) : $code_init;
		}
		//****************************************************************************************
        $numero_radicado = ComRecibidaPeer::getNumRadicacion($regional_id);
		$num_consecutivo = sprintf("%07d",$numero_radicado);
        //$data_rad['num_radicado'] = $num_consecutivo;
        $this->setNumeroRadicacion($num_consecutivo);
		//****************************************************************************************
		//$radicado = $entidad."-".$num_consecutivo."-".date("Y")."-R";
        //$radicado = sprintf("%s-%s-%s-2",date("Y"),$code_init,$num_consecutivo);
		$radicado = sprintf("%s-%s-2",date("Y"),$num_consecutivo);
		//****************************************************************************************
		return $radicado;
	}
	
	static private function getRadCodByRegional($regional_id)
	{
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
		$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
		$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//**********************************************************************************************
		$validperm = sfContext::getInstance()->getUser()->checkPerm('RADICAR_COM_RECIBIDA_OTRA_REGIONAL', $usuariologuiado);
		//**********************************************************************************************
		$cod_com = $regional_conectado;
		if($validperm){
            $regional = RegionalPeer::retrieveByPk(trim($regional_id));
			if($regional != null){
				$cod_com = trim($regional->getCodigo()) ? trim($regional->getCodigo()) : $regional->getPrimaryKey();
			}
		}
		//****************************************************************************************
		return $cod_com;
	}
    
    public function enviarRespuestaExterna()
	{
        try{
            $status = 400;
            //************************************************************************************************
            if($this->getPrimaryKey()){
                $simadSoap = new WsSimadUariv();
                $response_data = $simadSoap->loadWsInfoRadicadoEntrada($this->getPrimaryKey());
            }else{
                $response_data = array( 'status' => $status, 'message' => 'Error la informaci&oacute; no es valida');
            }
        }catch(Exception $ex){
            $response_data = array( 'status' => $status, 'message' => $ex->getMessage());
        }
        //****************************************************************************************************
        return $response_data;
    }

    public function getBasicInfoIntesadosCom(&$listIntesados)
	{
        try {
            $intesados_objects = ComRecibidaPeer::getListIntersadosByComId($this->getPrimaryKey()) ;
            foreach ($intesados_objects as $interesado) {
                $inumero_identificacion = $interesado->getInteresados()->getNumeroIdentificacion();
                $infoInteresado['CELULAR']= $interesado->getInteresados()->getCelular() ? $interesado->getInteresados()->getCelular() : "";
                $infoInteresado['PAIS_CODIGO']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getPais()->getCodigoNumerico();
                $infoInteresado['PAIS']= mb_strtoupper($interesado->getInteresados()->getCiudad()->getDepartamento()->getPais()->getNombre());
                $infoInteresado['DEPARTAMENTO_CODIGO']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getCodigoDane();
                $infoInteresado['CIUDAD_CODIGO']= $interesado->getInteresados()->getCiudad()->getCodigoDane();
                $infoInteresado['CIUDAD']= mb_strtoupper($interesado->getInteresados()->getCiudad()->getNombre());
                $infoInteresado['INFO_REPRESENTANTE']= RepresentanteLegalPeer::getToArraySoap($interesado->getRepresentantelegalId());
                $infoInteresado['INTR_INFO_REPRESENTANTE']= false;
                $infoInteresado['DEPARTAMENTO']= mb_strtoupper($interesado->getInteresados()->getCiudad()->getDepartamento()->getNombre());
                $infoInteresado['DIRECCION']= trim($interesado->getInteresados()->getDireccion()) ? (mb_strtoupper($interesado->getInteresados()->getDireccion())) : "";
                $infoInteresado['EMAIL']= trim($interesado->getInteresados()->getEmail()) ? mb_strtoupper($interesado->getInteresados()->getEmail()) : "";
                $infoInteresado['FAX']= $interesado->getInteresados()->getFax() ? $interesado->getInteresados()->getFax() : "";
                $infoInteresado['NUMERO_IDENTIFICACION']= !empty($inumero_identificacion) ? $inumero_identificacion : 0;
                $infoInteresado['PRIMER_NOMBRE']= trim($interesado->getInteresados()->getPrimerNombre()) ? (mb_strtoupper($interesado->getInteresados()->getPrimerNombre())) : "";
                $infoInteresado['PRIMER_APELLIDO']= trim($interesado->getInteresados()->getPrimerApellido()) ? (mb_strtoupper($interesado->getInteresados()->getPrimerApellido())) : "";
                $infoInteresado['SEGUNDO_NOMBRE']= trim($interesado->getInteresados()->getSegundoNombre()) ? (mb_strtoupper($interesado->getInteresados()->getSegundoNombre())) : "";
                $infoInteresado['SEGUNDO_APELLIDO']= trim($interesado->getInteresados()->getSegundoApellido()) ? (mb_strtoupper($interesado->getInteresados()->getSegundoApellido())) : "";
                $infoInteresado['TELEFONO']= $interesado->getInteresados()->getTelefono() ? $interesado->getInteresados()->getTelefono() : "";
                $infoInteresado['TIPO_IDENTIFICACION']= $interesado->getInteresados()->getTipoIdentificacion()->getCodigo();
                $infoInteresado['TIPO_GENERO']= $interesado->getInteresados()->getTipogeneroId();
                $listIntesados[] = $infoInteresado;
            }
        } catch (Exception $th) {
            //throw $th;
        }
    }

    public function getBasicInfoRemitenteCom(&$infoRemitente)
	{
        try {
            if($this->getDirectorioexternoId()){
                $infoRemitente['CODIGO_DEPARTAMENTO'] = $this->getDirectorioExterno()->getCiudad()->getDepartamento()->getCodigoDane();
                $infoRemitente['CODIGO_MUNICIPIO'] = $this->getDirectorioExterno()->getCiudad()->getCodigoDane();
                $infoRemitente['CIUDAD_CODIGO'] = $this->getDirectorioExterno()->getCiudad()->getCodigoDane();
                $infoRemitente['CODIGO_PAIS'] = $this->getDirectorioExterno()->getCiudad()->getDepartamento()->getPais()->getCodigoNumerico();
                $infoRemitente['DEPARTAMENTO'] = (($this->getDirectorioExterno()->getCiudad()->getDepartamento()->getNombre()));
                $infoRemitente['DIRECCION'] = trim($this->getDirectorioExterno()->getDireccion()) ? (($this->getDirectorioExterno()->getDireccion())) : "";
                $infoRemitente['EMAIL'] = trim($this->getDirectorioExterno()->getEmail()) ? ($this->getDirectorioExterno()->getEmail()) : "";
                $infoRemitente['MUNICIPIO'] = (($this->getDirectorioExterno()->getCiudad()->getNombre()));
                $infoRemitente['NUMERO_DOCUMENTO'] = trim($this->getDirectorioExterno()->getNit()) ? ($this->getDirectorioExterno()->getNit()) : "";
                $infoRemitente['PAIS'] = (($this->getDirectorioExterno()->getCiudad()->getDepartamento()->getPais()->getNombre()));
                $infoRemitente['REMITENTE'] = (($this->getDirectorioExterno()->getNombre()));
                $infoRemitente['NOMBRE'] = (($this->getDirectorioExterno()->getNombre()));
                $infoRemitente['TELEFONO']= $this->getDirectorioExterno()->getTelefono() ? $this->getDirectorioExterno()->getTelefono() : "";
                $infoRemitente['TIPO_DOCUMENTO']= $this->getDirectorioExterno()->getTipoIdentificacion()->getCodigo();
            }else{
                $infoRemitente['CODIGO_DEPARTAMENTO'] = 0;
                $infoRemitente['CODIGO_MUNICIPIO'] = 0;
                $infoRemitente['CIUDAD_CODIGO'] = 0;
                $infoRemitente['CODIGO_PAIS'] = 0;
                $infoRemitente['DEPARTAMENTO'] = 0;
                $infoRemitente['DIRECCION'] = 0;
                $infoRemitente['EMAIL'] = 0;
                $infoRemitente['MUNICIPIO'] = 0;
                $infoRemitente['NUMERO_DOCUMENTO'] = 0;
                $infoRemitente['PAIS'] = 0;
                $infoRemitente['REMITENTE'] = 0;
                $infoRemitente['NOMBRE'] = 0;
                $infoRemitente['TELEFONO']= 0;
                $infoRemitente['TIPO_DOCUMENTO']= 0;
            }
        } catch (Exception $th) {
            //throw $th;
        }
    }

    public function getBasicInfoExpedienteCom(&$infoExpediente, $unidad_documental)
	{
        try {
            if(!empty($unidad_documental)){
                $infoExpediente['CODIGO_DEPENDENCIA'] = $unidad_documental->getSubserie()->getSerie()->getDependencia()->getCodigo();
                $infoExpediente['CODIGO_SERIE_DOCUMENTAL'] = $unidad_documental->getSubserie()->getSerie()->getCodigo();
                $infoExpediente['CODIGO_SUBSERIE_DOCUMENTAL'] = $unidad_documental->getSubserie()->getCodigo();
                $infoExpediente['ESTADO'] = (mb_strtoupper($unidad_documental->getEstadoUnidadDocumental()->getDescripcion()));
                //$infoExpediente['ID_EXPEDIENTE'] = $unidad_documental->getCodigoBarras();
				$infoExpediente['ID_EXPEDIENTE'] = $unidad_documental->getPrimaryKey();
                $infoExpediente['NOMBRE_DEPENDENCIA'] = (mb_strtoupper($unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre()));
                $infoExpediente['NOMBRE_EXPEDIENTE'] = (mb_strtoupper($unidad_documental->getTitulo()));
                $infoExpediente['NOMBRE_SERIE_DOCUMENTAL'] = (mb_strtoupper($unidad_documental->getSubserie()->getSerie()->getDescripcion()));
                $infoExpediente['NOMBRE_SUBSERIE_DOCUMENTAL'] = (mb_strtoupper($unidad_documental->getSubserie()->getDescripcion()));
                $infoExpediente['NUMERO_EXPEDIENTE'] = mb_strtoupper($unidad_documental->getCodigoBarras());
            }else{
                $infoExpediente['CODIGO_DEPENDENCIA'] = "";
                $infoExpediente['CODIGO_SERIE_DOCUMENTAL'] = "";
                $infoExpediente['CODIGO_SUBSERIE_DOCUMENTAL'] = "";
                $infoExpediente['ESTADO'] = "";
                $infoExpediente['ID_EXPEDIENTE'] = "";
                $infoExpediente['NOMBRE_DEPENDENCIA'] = "";
                $infoExpediente['NOMBRE_EXPEDIENTE'] = "";
                $infoExpediente['NOMBRE_SERIE_DOCUMENTAL'] = "";
                $infoExpediente['NOMBRE_SUBSERIE_DOCUMENTAL'] = "";
                $infoExpediente['NUMERO_EXPEDIENTE'] = "";
            }
        } catch (Exception $th) {
            //throw $th;
        }
    }

    public function getBasicInfoMetadata()
	{
        try{
            $unidad_documental = null;
            //************************************************************************************************
            if($this->getContenidodocId()){
                $contenido_doc = ContenidoUnidadDocumentalPeer::retrieveByPK($this->getContenidodocId());
                if($contenido_doc != null){
                    $unidad_documental = $contenido_doc->getUnidadDocumental();
                }
            }/*else{
                $unidad_documental = TransferenciaPeer::getExpedienteByComRecibidaId($this->getPrimaryKey());
            }*/
            //************************************************************************************************
            $list_users = $this->getUsuariosListCom();
            //************************************************************************************************
            $listIntesados = array();
            $this->getBasicInfoIntesadosCom($listIntesados);
            //************************************************************************************************
            $infoRemitente = array();
            $this->getBasicInfoRemitenteCom($infoRemitente);
            //************************************************************************************************
            $infoExpediente = array();
            $this->getBasicInfoExpedienteCom($infoExpediente,$unidad_documental);
            //************************************************************************************************
            $digitcom_file = $this->getLocalDigitDocument();
            //************************************************************************************************
            $data_response =  array ( 'COMRECIBIDA_ID' =>$this->getPrimaryKey(),
                'REGIONAL_RADICACION' => mb_strtoupper($this->getRegional()->getDescripcion()),
                'CLASIFICACION_DOCUMENTO' => mb_strtoupper($this->getTipoComRecibida()->getDescripcion()),
                'FORMA_RECEPCION' => mb_strtoupper($this->getFormaRecepcion()->getDescripcion()),
                'DEPENDENCIA_DESTINO' => mb_strtoupper($this->getDependencia()->getNombre()),
                'DEPENDENCIA_CODIGO' => $this->getDependencia()->getCodigo(),
                'PRIORIDAD_COM' => mb_strtoupper($this->getPrioridadCom()->getDescripcion()),
                'ARCHIVO_DIGIT' => !empty($digitcom_file) ? $digitcom_file : "",
                'ARCHIVO_NOMBRE' => !empty($digitcom_file) ? basename($digitcom_file) : "",
                'ASUNTO' => mb_strtoupper($this->getAsunto()),
                'RADICADO' => $this->getRadicado(),
                'FOLIOS' => $this->getFolios(),		
                'RADICADO_ORIGEN' => $this->getRadicadoOrigen(),
                'ANEXOS' => $this->getAnexos(),
                'OBSERVACIONES' => mb_strtoupper($this->getObservaciones()),
                'FECHA_VENCIMIENTO' => $this->getFechaMaximaRespuesta() ? $this->getFechaMaximaRespuesta("Y-m-d") : "",
                'FECHA_LLEGADA' => $this->getFechaRecibido() ? $this->getFechaRecibido("Y-m-d") : "",
                'NUMERO_PROCESO' => $this->getNumeroProceso(),
                'NUMERO_FUD' => $this->getNumeroFud() ? $this->getNumeroFud() : 0,
                'NUMERO_GUIA' => $this->getGuia(),
                'DEPARTAMENTO_CODIGO' => ($this->getCiudad()->getDepartamento()->getCodigoDane()),
                'CIUDAD_CODIGO' => ($this->getCiudad()->getCodigoDane()),
                'MARCONORMATIVO' => "0",
                'ID_JUZGADO' => "0",
                'MARCA_VINCULACION' => ($this->getMarcaVinculacion() ? "true" : "false"),
                'NOMBRE_EXPEDIENTE' => $unidad_documental != null ? (mb_strtoupper($unidad_documental->getTitulo())) : null,
                'NUMERO_DECLARACION' => trim($this->getNumeroFud()) ? mb_strtoupper($this->getNumeroFud()) : "0",
                'ID_GESTOR' => isset($list_users['nuid_usuario_actual']) ? $list_users['nuid_usuario_actual'] : 0,
                'TIPO_DOCUMENTO' => "1",
                'USUARIO_RADICADOR' => mb_strtoupper($list_users['radicador']),
                'INTERESADOS_COM' => $listIntesados,
                'REMITENTE_COM' => $infoRemitente,
                'EXPEDIENTE_COM' => $infoExpediente,
            );
        }catch(Exception $ex){
            $data_response = null;
        }
        //***************************************************************************************************
        return $data_response;
    }

	public function getBasicUrlDigitCom($dir_raiz,$digit_com)
	{
		$array_url = array();
		//*****************************************************************************************
		//$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        //$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
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
	
    public function getLocalDigitDocument()
	{
        $targetpath = null;
        //*****************************************************************************************
        try{
            $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
            //*************************************************************************************
            $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
            //*************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : $dir_raiz;
            //*************************************************************************************
            $storage_com = $this->getBasicUrlDigitCom($dir_raiz,$digit_dir);
            foreach ($mimetypes as $format) {
                $filename = sprintf("%s.%s",trim($this->getRadicado()),$format);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                if(file_exists($targetpath)){
                    return $targetpath;
                    break;
                }
            }
        }catch(Exception $ex){
            $targetpath = null;
        }
        //******************************************************************************************
		return $targetpath;
	}

    public function getListDigitDocument($include_anexos = true)
	{
		$list_attach = array();
		//*****************************************************************************************
        if($include_anexos){
            $files_adjuntos = preg_split("/[,]+/",$this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
            //*****************************CREANDO LISTA DE ARCHIVOS*******************************
            for($j = 0; $j < count($files_adjuntos); $j++){
                if(trim($files_adjuntos[$j])){
                    $filename = basename($files_adjuntos[$j]);
                    $xguid = simad_util::create_guid($filename);
                    $url_secure = $this->getUrlTokenViewImageByObject($xguid);
                    $list_attach[] = array('GUID' => $xguid, 'NOMBRE_ARCHIVO' => $filename, 'TIPO_ATTACHMENT' => 'ANEXO', 'URL' => $url_secure);
                }
            }
        }
		//******************************************************************************************
        $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
        //******************************************************************************************
        $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
        $digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
        //******************************************************************************************
        $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : $dir_raiz;
        //******************************************************************************************
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
            $dir_raiz = ParametroPeer::retrieveByPk(27)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(14)->getValortexto();
            //******************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : $dir_raiz;
            //******************************************************************************************
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

    function getUrlTokenViewImageByObject($xguid)
    {
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
            $url_query['tipocom'] = 2;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;

            //$url_abs = urlencode($base_path."recibida.php/com_recibida/viewImage?key_id=".$this->getPrimaryKey()."&vtoken=".$token."&xguid=".$xguid);
            $url_abs = "http://".$_SERVER["HTTP_HOST"]."/viewImage.php?".http_build_query($url_query);
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
            $url_query['tipocom'] = 2;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;
            $url_query['attachment'] = md5('attachment'.$this->getRadicado());
            $url_abs = "http://".$_SERVER["HTTP_HOST"]."/viewImage.php?".http_build_query($url_query);
			return ($url_abs);			
		}catch(Exception $ex){
			return null;
		}
	}

    public function getRutaAdjuntos($files_new,$usuario_id,$is_files_old = false,$files_old_text = "")
    {
		$url_files = array();
        $util_simad = new simad_util();
        $usuario = UsuarioPeer::retrieveByPK($usuario_id);
		/********************************************************************************/		
		$usuario_name = $usuario->getUserName();
		$dirRaiz      = ParametroPeer::retrieveByPk(27)->getValortexto();
		$dir_object   = ParametroPeer::retrieveByPk(14)->getValortexto();
		$alias_object = ParametroPeer::retrieveByPk(28)->getValortexto();
		$dirTmp       = ParametroPeer::retrieveByPk(65)->getValortexto();
		/********************************************************************************/		
		$entidad_folder = trim($usuario->getRegional()->getEntidad()->getDirectorioName());
		$regional_folder = trim($usuario->getRegional()->getDirectorioName());
		$entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.'/'.$regional_folder : $entidad_folder) : "";
        /********************************************************************************/
		$directorio_entidad = $dirRaiz . $entidad_text;
		$directorio_com = $dir_object.DIRECTORY_SEPARATOR.($usuario_name);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp);
		$directorio_final = simad_util::NormalizePath($directorio_entidad.DIRECTORY_SEPARATOR.$directorio_com);
		$dirextorio_alias = $alias_object . $entidad_text . "/" . $dir_object . "/" . ($usuario_name);
		//********************************************************************************
		if($is_files_old){
			$files_adjuntos = preg_split("/[,]+/",$files_old_text, -1, PREG_SPLIT_NO_EMPTY);
			$files_text = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
			for($j=0; $j < count($files_text); $j++){
                $url = ComRecibida::strpos_array($files_text[$j], $files_adjuntos);
				if(!is_null($url)){
					$url_files[] = $url . ",";
				}else{
					$file_name = basename($files_text[$j]);
					$filenamesource = $directorio_tmp . DIRECTORY_SEPARATOR . $file_name;
					$filenametarget = $directorio_final . DIRECTORY_SEPARATOR . $file_name;
					if(file_exists($filenamesource)){
						$directorio_final = simad_util::createPath($directorio_final);
						copy($filenamesource, $filenametarget);
						unlink($filenamesource);
						$url_files[] = $dirextorio_alias . "/" . $file_name;
					}
				}
			}
		}else{
			$files_adjuntos = preg_split("/[,]+/",$files_new, -1, PREG_SPLIT_NO_EMPTY);
			for($j=0; $j <= count($files_adjuntos); $j++){
				if(trim($files_adjuntos[$j])){
					$file_name = basename($files_adjuntos[$j]);
					$filenamesource = $directorio_tmp . DIRECTORY_SEPARATOR . $file_name;
					$filenametarget = $directorio_final . DIRECTORY_SEPARATOR . $file_name;
					if(file_exists($filenamesource)){
						$directorio_final = simad_util::createPath($directorio_final);
						copy($filenamesource, $filenametarget);
						unlink($filenamesource);
						$url_files[] = $dirextorio_alias . "/" . $file_name;
					}
				}
			}
		}
        //********************************************************************************
		return implode(",",$url_files);
	}
    
    public function moveDigitFile($filedigit,$overwrite = false,$isFullPath = false)
    {
        if(trim($filedigit)){
        	$nomFile   =  ($this->getRadicado());
        	$file_vars = pathinfo($filedigit);
            //$util_simad = new simad_util();
        	//**********************************CREANDO ESTRUCTURA DE DIRECTORIOS*******************************
        	$dirRaiz   = ParametroPeer::retrieveByPk(27)->getValortexto();
            $alias_com  = ParametroPeer::retrieveByPk(14)->getValortexto();
            $dirTmp       = ParametroPeer::retrieveByPk(65)->getValortexto();
        	$extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
        	//**************************************************************************************************
            $entidad_folder = trim($this->getRegional()->getEntidad()->getDirectorioName());
            $regional_folder = trim($this->getRegional()->getDirectorioName());
		    $entidad_text = $entidad_folder ? $entidad_folder : "";
            $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
            $directorio_tmp = simad_util::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp);
			$tmpfdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
            //**************************************************************************************************
            $this->setDirDigit($dirRaiz);
            //**************************************************************************************************
        	$periodo = $this->getPeriodoId();
        	$directorio_text = $dirRaiz.DIRECTORY_SEPARATOR.$entidad_text.DIRECTORY_SEPARATOR.$alias_com.DIRECTORY_SEPARATOR.$periodo;
            $directorio_entidad = simad_util::NormalizePath($directorio_text);
        	$directorio = simad_util::createPath($directorio_entidad);
        	//**************************************************************************************************
            if(in_array($file_vars['extension'], $extensions)){
                $file_name =  $nomFile.'.'.$file_vars['extension'];
                $filenametarget = $directorio.DIRECTORY_SEPARATOR.$file_name;
                $filenamesource = $isFullPath ? $filedigit : $directorio_tmp.DIRECTORY_SEPARATOR.$filedigit;
    			//**********************************************************************************************
				if($overwrite){
    				if(file_exists($filenametarget)){ unlink($filenametarget); }
				}
                //**********************************************************************************************
				if(strtolower($file_vars['extension']) == 'pdf'){
					$file_stamp = $this->stampStickerToDigit($filenamesource,true);
					if(file_exists($file_stamp)){
						$response_ws = $this->stampDocumentProcess($file_stamp);
						if(!$response_ws['error']){
							$unique_file = $tmpfdir.DIRECTORY_SEPARATOR.uniqid().'.pdf';
							$isFileCreate = simad_util::getConvertB64ToFile($response_ws['file_base64'],$unique_file);
							if($isFileCreate){
							  unlink($file_stamp);
							  if(copy($unique_file,$filenametarget)){ unlink($unique_file); }
							}else{
							  copy($file_stamp,$filenametarget);
							}
						}else{
							copy($file_stamp,$filenametarget);
						}
					}else{
                        copy($filedigit,$filenametarget);
                        unlink($filedigit);
                    }
				}else{
                    if(copy($filedigit,$filenametarget)){
                        unlink($filedigit);
                    }
                }
                //**********************************************************************************************
                if(file_exists($filenametarget)){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    
    public static function strpos_array($haystack, $needles) {
        if ( is_array($needles) ) {
        	foreach ($needles as $str) {
        		if ( is_array($str) ) {
                    $pos = ComRecibida::strpos_array($haystack, $str);
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
    
    /**
     * recibidaActions::getUserIdComRecibidaRol()
     * funcion que devuelve el o los ids de los usuarios asignados a la comunicacion 
     * @return mixed or single id
     * @com_recibida parametro que contiene la comunicacion
     * @rol_id parametro que especifica el usuario con el rol a retorna el valor cero retorna todos los roles
    */ 
    public function getUserIdComRecibidaRol($rol_id)
	{
        $index = 0;
        $users_id = array();
        $recibida_usuarios = $this->getComrecibidaUsuarios();
    	foreach ($recibida_usuarios as $recibida)
        {
            if($recibida->getRolusuariorecibidaid() == $rol_id)
            {
               return $recibida->getUsuarioId();
            }elseif($rol_id == 0){
               $users_id[$index] = $recibida->getUsuarioId();
               $index++;
            }
        }
        return $users_id;
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
    
    public function mergePdftk($inputFileName, $fsticker,$pathToSave)
    {
        require_once(sfConfig::get('sf_lib_dir').'/pdftk/autoload.php');
        $pdftk = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pdftk'.DIRECTORY_SEPARATOR.'pdftk.exe';
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        //**********************************************************************************************
        try{
            $pdf = new Pdf([
                'A' => $inputFileName, // A is alias for file1.pdf
                'B' => $fsticker, // B is alias for file2.pdf
            ],['command' => $pdftk,'useExec' => true]);

            $pdf->tempDir = $tmp_dir;
            $pdf->compress(true);
            $tmpFile = (string) $pdf->getTmpFile();

            $result = $pdf->cat(1, null, 'B')
                ->cat(2, 'end', 'A')->saveAs('filled-in-sheet.pdf');
            if ($result === false) {
                $error = $pdf->getError();
                return null;
            }

            if(file_exists($tmpFile)){
                $newtmpfile = $pathToSave.DIRECTORY_SEPARATOR.basename($tmpFile);
                if(copy($tmpFile,$newtmpfile)){
                    unlink($fsticker);
                    return basename($newtmpfile);
                }else{
                    return null;
                }
            }
        } catch(Exception $e) {
            //$error_text = $e->getMessage();
            return null;
        }
    }

    public function extractPagePdftk($inputFileName, $pages = array())
    {
        require_once(sfConfig::get('sf_lib_dir').'/pdftk/autoload.php');
        $pdftk = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pdftk'.DIRECTORY_SEPARATOR.'pdftk.exe';
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        //**********************************************************************************************
        try{
            $pdf = new Pdf($inputFileName,['command' => $pdftk,'useExec' => true]);

            $pdf->tempDir = $tmp_dir;
            $pdf->compress(true);
            $tmpFile = (string) $pdf->getTmpFile();

            $result = $pdf->cat($pages)->saveAs('filled-in-sheet.pdf');
            if ($result === false) {
                $error = $pdf->getError();
                return null;
            }

            if(file_exists($tmpFile)){ return null; }

            return basename($tmpFile);
        } catch(Exception $e) {
            //$error_text = $e->getMessage();
            return null;
        }
    }

    public function stampStickerToDigit($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdf/fpdf.php');
		require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/autoload.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/PDF-Parser-1.5/autoload.php');
        /*require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/Fpdi.php');*/
        //**********************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        $source_filename = pathinfo($inputFileName, PATHINFO_FILENAME);
        $target_dir = md5(date("YmdGis"));
        //**********************************************************************************************
        if (trim($inputFileName)){
            try {
                $pathToSave = simad_util::createPath($directorio_tmp.DIRECTORY_SEPARATOR.$target_dir);
                $codebar = $tmp_dir.DIRECTORY_SEPARATOR.$this->generateCodeBarInFile();
                //**************************************************************************************
                $ufopen = fopen($inputFileName, 'rb');
                $stream = new \setasign\Fpdi\PdfParser\StreamReader($ufopen, false);
                //**************************************************************************************
                // initiate FPDI
                $pdf = new Fpdi();
                // set the source file
                $pagecount = $pdf->setSourceFile($stream);
                //$pagecount = $pdf->setSourceFile($inputFileName);
                // import page 1
                $pageNo = 1;
                $ptln = 5;
				$topXY = 5;
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId);
                // add a page
                $pdf->AddPage();
                // use the imported page and place it at point 10,10 with a width of 100 mm
                $pdf->useTemplate($tplId, null, null, null, null, true);
				$pdf->SetXY($topXY,$topXY);
				$pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(($size['width']- 50), 10, "Fecha:", 0, 0, 'R',true);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(0, 10, $this->getFechaCreacion("d/m/Y H:i:s A"), 0, 0, 'R',true);
                $pdf->Ln($ptln);
                $pdf->Cell( 0, 10, $pdf->Image($codebar,($size['width']-55),(($topXY * 2) + 1.5),0,0,'png'), 0, 0, 'R');
                //***************************************************************************************
                /*for($pageNo = ($pageNo + 1); $pageNo <= $pagecount; $pageNo++){
                    $tplIdx = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplIdx, null, null, null, null, true);
                }*/
                //***************************************************************************************
                $fsticker_stamp = $pathToSave.DIRECTORY_SEPARATOR.$source_filename.'.pdf';
                $pdf->Output('F',$fsticker_stamp);
                //***************************************************************************************
                if(file_exists($fsticker_stamp)){ 
                    if($pagecount > 1){
                        $source_filename = $this->mergePdftk($inputFileName,$fsticker_stamp,$pathToSave);
                    }else{
                        $source_filename = $source_filename.'.pdf';
                    }
                }
                //***************************************************************************************
                $source_filename = $returnFullPath ? ($pathToSave.DIRECTORY_SEPARATOR.$source_filename) : ($target_dir.DIRECTORY_SEPARATOR.$source_filename);
            } catch(Exception $e) {
                $msgex = $e->getMessage();
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
        $usuario = empty($usuario_attach) ? UsuarioPeer::retrieveByPK($this->getUserIdComRecibidaRol($rolu_proyecta)) : $usuario_attach;
        $usuario_name    = $usuario->getUserName();
        //********************************************************************************
        $simad_util   = new simad_util();
        $dirRaiz      = ParametroPeer::retrieveByPk(27)->getValortexto();
        $dir_object   = ParametroPeer::retrieveByPk(14)->getValortexto();
        $alias_object = ParametroPeer::retrieveByPk(28)->getValortexto();
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
	
	/**
     * objectActions::stampDocumentProcessGse()
    * estampa la comunicacion
    * @return mixed array('error' => true|false, 'file_base64' => null|base64 archivo,'msg_info' => 'mesaje del resultado de la operacion de firma')
    */ 
    public function stampDocumentProcessGse($fullpath)
    {
        try{
            $stampWs = new WsFirmaApiGse();
            return $stampWs->stampDocumentOnly($fullpath);
        }catch (Exception $th) {
            return array('error' => true, 'file_base64' => null, 'msg_info' => $th->getMessage());
        }
    }
	
	/**
     * objectActions::stampDocumentProcessAndes()
    * estampa la comunicacion
    * @return mixed array('error' => true|false, 'file_base64' => null|base64 archivo,'msg_info' => 'mesaje del resultado de la operacion de firma')
    */ 
    public function stampDocumentProcessAndes($fullpath)
    {
        try{
            $stampWs = new WsFirmaApiAndes();
            return $stampWs->stampDocumentOnly($fullpath);
        }catch (Exception $th) {
            return array('error' => true, 'file_base64' => null, 'msg_info' => $th->getMessage());
        }
    }
	
    /**
     * objectActions::stampDocumentProcess()
    * estampa la comunicacion
    * @return mixed array('error' => true|false, 'file_base64' => null|base64 archivo,'msg_info' => 'mesaje del resultado de la operacion de firma')
    */ 
    public function stampDocumentProcess($fullpath)
    {
        try{
            //return $this->stampDocumentProcessAndes($fullpath);
			return $this->stampDocumentProcessGse($fullpath);
        }catch (Exception $th) {
            return array('error' => true, 'file_base64' => null, 'msg_info' => $th->getMessage());
        }
    }
	
	/**
     * objectActions::setNewStateByDigitCom()
    * se actualiza el estado de la comunicacion despues de adjuntar la digitalizacion
    * @return void
    */ 
    public function setNewStateByDigitCom($tipoprocesocom_id = 2)
    {
        try{
			if($this->getEstadocomrecibidaId() == 11){
				$this->setFechaDigit(($this->getFechaDigit() ? $this->getFechaDigit() : date("Y-m-d G:i:s")));
				$this->setIsLocked(0);
                //****************************************************************************************
                $tipoprocesocom_id = $this->getTipoprocesocomId() == 3 ? null : $tipoprocesocom_id;
                //****************************************************************************************
				if(!empty($tipoprocesocom_id)){ $this->setTipoprocesocomId($tipoprocesocom_id); }
                //****************************************************************************************
				$this->setEstadocomrecibidaId(1);
				$this->save();
				//****************************************************************************************
				ComRecibidaPeer::updateEstadosComByEstado($this->getPrimaryKey(),1,array(2,3));
				ComRecibidaPeer::updateFechaAsignaCom($this->getPrimaryKey(),array(2));
				//****************************************************************************************
				$usuarios_mail = $this->getUserIdComRecibidaRol(2);
				//$encabezado_email = "Este es un mensaje para informarle que se ha adjuntado un documento electr&oacute;nico a la siguiente comunicaci&oacute;n: ";
				$encabezado_email = "Este es un mensaje para informarle que se ha asignado la siguiente comunicaci&oacute;n: ";
				ComRecibidaPeer::envioEmail($this->getPrimaryKey(),$usuarios_mail,$encabezado_email,false,false);
            }
        }catch (Exception $th) {
			$exmsg = $th->getMessage();
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
     * ComRecibida::getAttachmentCom()
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
			if(!in_array($this->getEstadocomrecibidaId(),array(12,14))){
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
