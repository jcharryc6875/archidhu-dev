<?php

/**
 * Subclass for representing a row from the 'CONTENIDO_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Builder as CommandBuilder;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;

class ContenidoUnidadDocumental extends BaseContenidoUnidadDocumental
{
    public function generateValorHuella(){
        $this->setValorHuella($this->getDataValorHuella());
        $this->save();
    }

    public function getDataValorHuella(){
        $dstorage = array();
        $dstorage[] = $this->getPrimaryKey();
        $dstorage[] = $this->getUnidaddocumentalId();
        $dstorage[] = $this->getUnidadDocumental()->getSubserieId();
        $dstorage[] = $this->getTipodocumentalId();
        $dstorage[] = $this->getFolioInicial();
        $dstorage[] = $this->getFolioFinal();
        $dstorage[] = $this->getFormatFile();
        $dstorage[] = $this->getSizeFile();
        $dstorage[] = $this->getOrigendocumentoId();
        $dstorage[] = $this->getFechaCreacion();       
        $dstorage[] = $this->getFolios();
        $dstorage[] = $this->getEstadocontenidounidaddocId();
        $dstorage[] = $this->getFechaDocumento();
        $dstorage[] = $this->getPathAbsolute();
        $dstorage[] = $this->getPathRelative();
        $dstorage[] = $this->getOrdenContenido();
        //********************************************************************
        $str_encrypt = implode("",$dstorage);
        return sha1($str_encrypt);
    }

	public function getUrlTokenTrustImageByObject($xguid) {
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
            $url_query['tipocom'] = 4;
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
	
	public function getDigitDocumentByXguid($search_xguid)
	{
        try{
            $urlfile = preg_split("/[,]+/",trim($this->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
			$base_web = sfConfig::get('base_simad');
			//**************************************************************************************
			$absolute_path = $this->getPathAbsolute();
			$relative_path = $this->getPathRelative();
			$fullpath = iconv('utf-8', 'cp1252',$absolute_path.DIRECTORY_SEPARATOR.$relative_path.DIRECTORY_SEPARATOR.basename($urlfile[0]));
			$file_source = simad_util::NormalizePath($fullpath);
			$extension = pathinfo($file_source, PATHINFO_EXTENSION);
			//**************************************************************************************
			if(file_exists($file_source)){
				$xguid = simad_util::create_guid(basename($urlfile[0]));
				if($search_xguid == $xguid){
					return array('NOMBRE_ARCHIVO' => basename($urlfile[0]), 'URL' => trim($file_source), 'TIPO_ATTACHMENT' => 'ANEXO');
				}else{
					return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
				}
			}else{
				return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
			}
            //*******************************************************************************************
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        }catch(Exception $ex){
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        }
	}
	
    function getUrlTokenViewImageByObject() {
		try{		
			if($this == null){
			   return null;
			}
			//*****************************************************************************************
			$time = time();
			$token_base = hash("sha512",$this->getPrimaryKey().$this->getFechaCreacion().$time);
			$mitad = strlen($token_base ) / 2;
			$parte1 = substr($token_base , 0, $mitad); 
			$parte2 = substr($token_base , $mitad);
			$token = $parte1.urlencode($time).$parte2;
			//*****************************************************************************************            
			return ("contenido_documental/viewImage?key_id=".$this->getPrimaryKey()."&vtoken=".$token);			
		}catch(Exception $ex){
			//return utf8_encode($ex->getMessage());
			return null;
		}
	}

    function getUrlTokenViewImageByObjectAjax() {
		try{		
			if($this == null){
			   return null;
			}
            //*****************************************************************************************
            $keys['baseurl'] = 'contenido_documental/viewImage';
			//*****************************************************************************************
			$time = time();
			$token_base = hash("sha512",$this->getPrimaryKey().$this->getFechaCreacion().$time);
			$mitad = strlen($token_base ) / 2;
			$parte1 = substr($token_base , 0, $mitad); 
			$parte2 = substr($token_base , $mitad);
			$token = $parte1.urlencode($time).$parte2;
			//*****************************************************************************************
            $keys['key_id'] = $this->getPrimaryKey();
            $keys['vtoken'] = $token;
			return $keys;			
		}catch(Exception $ex){
			//return utf8_encode($ex->getMessage());
			return null;
		}
	}

	/**
    * objectActions::addFileProcessOcrAndSign()
    * Valida si el documento adjunto al expediente se debe firmar y estampar
    * @param mixed $nfiles lista de documentos a validar
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */
	public function addFileProcessOcrAndSign($nfiles)
	{
		try{
			if($this->getTipofirmadigitalId() != null){
				foreach ($nfiles as $file) {
					$extension = pathinfo($file,PATHINFO_EXTENSION);
					//*********************************************************************************
					if($extension == "pdf"){
						switch ($this->getTipofirmadigitalId()) {
							case 1://solo firma
								$tsign = SignDigitalOptions::OnlySign;
								break;
							case 2://firma y estampa
								$tsign = SignDigitalOptions::SingAndStamp;
								break;
							case 3://solo estampa
								$tsign = SignDigitalOptions::OnlyStamp;
								break;
							default:
								$tsign = SignDigitalOptions::None;
								break;
						}
						//*****************************************************************************
						return $this->signDocumentExpProcess($file,$tsign,true);
					}
				}
			}else{
				$message_sign = 'El documento no se debe firmar';
				$ocrDoc = true;
				foreach ($nfiles as $file) {
					if(file_exists($file) && is_readable($file)){
						$extension = strtolower(pathinfo($file,PATHINFO_EXTENSION));
						//*****************************************************************************
						if($extension != "pdf"){
							continue;
						}
						//*****************************************************************************
						if($ocrDoc){
							$response_ocr = $this->documentOcrProcess($file);
							$ocr_text = isset($response_ocr['message']) ? ','.trim($response_ocr['message']) : null;
							$message_sign .= $ocr_text;
						}
					}
				}
				//**************************************************************************************
				$message_gral = !empty($message_sign) ? 'El documento fue asociado al expediente,'.$message_sign : 'El documento fue asociado al expediente';
				return array('httpStatus' => 200, 'message' => $message_gral);
			}
		}catch(PropelException $ex){
			return array('httpStatus' => 400, 'message' => $ex->getMessage());
		}catch(\IOException $ex){
			return array('httpStatus' => 400, 'message' => $ex->getMessage());
		}catch(\Exception $ex){
			return array('httpStatus' => 400, 'message' => $ex->getMessage());
		}catch(\Throwable $ex){
			return array('httpStatus' => 400, 'message' => $ex->getMessage());
		}
	}

	/**
    * objectActions::signDocumentExpProcess()
    * Inicia proceso de firmado digital del documento, usando la integracion con alguno de los proveedores de firma digital
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @param bool $stampDoc indica si el documento firmado se debe asignar estampa de tiempo
	* @param bool $ocrDoc indica si se aplica ocr al documento
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
	public function signDocumentExpProcess($fullpath, $tsign = SignDigitalOptions::None, $ocrDoc = false)
    {
		$ocr_text = null;
		$read_sections = array('pdftk_cli_command_path');
		$ini_array = simad_util::readConfigFileApp($read_sections);
		//***********************************************************************************************
		try{
			$reponse_process = array();
			//*******************************************************************************************
            if(file_exists($fullpath) && is_readable($fullpath)){
				$pdftk_cli_path = isset($ini_array['pdftk_cli_command_path']) ? $ini_array['pdftk_cli_command_path'] : 'pdftk';
				//***************************************************************************************
				$pdfTk_util = new PdfTkWrapper($pdftk_cli_path);
				$pdf_digitsigned = $pdfTk_util->isDigitallySigned($fullpath);
				if(!empty($pdf_digitsigned))
				{
					$resp_sgitalsign = json_decode($pdf_digitsigned);
					if($resp_sgitalsign->signed === true){
						$message_sign = !empty($ocr_text) ? 'Este radicado no se puede firmar(no cuenta con un consecutivo de radicación), esta anulado o es un borrador,'.$ocr_text : $reponse_process['message'];
						$reponse_process =  array('httpStatus' => 400, 'message' => $resp_sgitalsign->message);
					}
				}
				//***************************************************************************************
				if($ocrDoc){
					$response_ocr = $this->documentOcrProcess($fullpath);
					$ocr_text = isset($response_ocr['message']) ? trim($response_ocr['message']) : null;
				}
				//***************************************************************************************
				if($tsign == SignDigitalOptions::OnlySign){
					$reponse_process =  $this->singDocumentProcessAndes($fullpath);
					//return $this->singDocumentProcessGse($signAllPages);
				}elseif($tsign == SignDigitalOptions::SingAndStamp){
					$reponse_process =  $this->singDocumentProcessAndes($fullpath,true);
					//return $this->singDocumentProcessGse($signAllPages);
				}elseif($tsign == SignDigitalOptions::OnlyStamp){
					$reponse_process =  $this->stampDocumentProcessAndes($fullpath,true);
					//return $this->singDocumentProcessGse($signAllPages);
				}
				//***************************************************************************************
				$reponse_process['message'] = !empty($ocr_text) ? $reponse_process['message'].','.$ocr_text : $reponse_process['message'];
            }else{
				$message_sign = !empty($ocr_text) ? 'Este radicado no se puede firmar(no cuenta con un consecutivo de radicación), esta anulado o es un borrador,'.$ocr_text : $reponse_process['message'];
                $reponse_process =  array('httpStatus' => 400, 'message' => $message_sign);
            }
		}catch(PropelException $ex) {
			$message_sign = !empty($ocr_text) ? 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage().','.$ocr_text : $ex->getMessage();
            $reponse_process = array('httpStatus' => 400, 'message' => $message_sign);
		}catch(\Exception $ex){
			$message_sign = !empty($ocr_text) ? $ex->getMessage().','.$ocr_text : $ex->getMessage();
			$reponse_process = array('httpStatus' => 400, 'message' => $message_sign);
        }catch (\Throwable $ex) {
			$message_sign = !empty($ocr_text) ? $ex->getMessage().','.$ocr_text : $ex->getMessage();
            $reponse_process = array('httpStatus' => 400, 'message' => $message_sign);
        }
		//*******************************************************************************************
		return $reponse_process;
	}

    /**
    * objectActions::singDocumentProcessAndes()
    * genera la firma digital al documento
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @param bool $stampDoc indica si el documento firmado se debe asignar estampa de tiempo
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
    public function singDocumentProcessAndes($file_attach = null, $stampDoc = false)
    {
        try{
            $firmaApi = new WsFirmaApiAndes();
            //***************************************************************************************************
            $unidad_documental = $this->getUnidadDocumental();
            $ulist_expediente = $unidad_documental->getListUsuariosObject();
            $ulist_firma = $ulist_expediente['usuario_responsable'];
			$filevars = pathinfo($file_attach);
            //***************************************************************************************************
            if($ulist_firma != null){
                if(empty($ulist_firma->getLoginFirma()) && empty($ulist_firma->getPassFirma())){
                    $msg = sprintf("El usuario %s no tiene habilitada la firma digital",$ulist_firma->getNombreAll());
                    return array('httpStatus' => 400, 'message' => $msg);
                }
            }else{
                $msg = sprintf("El usuario %s no tiene habilitada la firma digital",$ulist_firma->getNombreAll());
                return array('httpStatus' => 400, 'message' => $msg);
            }
            //***************************************************************************************************
            if(file_exists($file_attach))
            {
                $filesing = $file_attach;$response_list = array();
				$targetpath = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(time());
				$targetfile = $targetpath.DIRECTORY_SEPARATOR.md5($filevars['basename']).".".$filevars['extension'];

				if(!is_readable($targetpath)){
					simad_util::createPath($targetpath);
				}
                //***********************************************************************************************
                if(WsFirmaApiAndes::SERVICE_ENABLE_WS){
					$Xc = 5;$Yc = 640;$Wc = 40;$Hc = 120;$fy1=100;
                    //*******************************************************************************************
                    $isSingned = true;$ubicacionFirma = array('x' => $Xc,'y' => $Yc,'w' => $Wc,'h' => $Hc);                    
                    $firma_info = $ulist_firma->getNombreApellido();
                    //***************************************************************************************
                    $b64firma = sfConfig::get("sf_lib_dir").DIRECTORY_SEPARATOR.'efirma'.DIRECTORY_SEPARATOR.WsFirmaApiAndes::FIRMA_VISIBLE_IMAGE;
                    //***************************************************************************************
                    $response_sing = $firmaApi->documentWsApiFirmaAndes($ulist_firma->getLoginFirma(),base64_decode($ulist_firma->getPassFirma()),$filesing,$targetfile,$b64firma,$ubicacionFirma);
                    //***************************************************************************************
                    $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                    if(!$response_sing['error']){
                        $Yc = ($Yc-$fy1);
                        //***********************************************************************************
                        $filesing = $response_sing['filesing'];
                        $ubicacionFirma = array('x' => $Xc,'y' => $Yc,'w' => $Wc,'h' => $Hc);
                    }else{
                        return array('httpStatus' => 400, 'message' => $response_sing['mesagge']);
                    }                    
                    //*******************************************************************************************
					if(!is_file($response_sing['filesing'])){
						if($isSingned){
							$target_tmp = $targetpath.DIRECTORY_SEPARATOR.md5(time().$filevars['basename']).".".$filevars['extension'];
							$isSingned = simad_util::getConvertB64ToFile($response_sing['filesing'],$target_tmp);
							if(is_readable($target_tmp)){
								if(!rename($target_tmp,$file_attach)){
									$isSingned = false;
								}
							}else{
								$isSingned = false;
							}
						}
					}elseif(is_readable($response_sing['filesing'])){
						if(!rename($response_sing['filesing'],$file_attach)){
							$isSingned = false;
						}
					}else{
						$isSingned = false;
					}
                    //*******************************************************************************************
                    if($isSingned){
                        $response_process = array('httpStatus' => 200, 'message' => 'Documento firmado existosamente!');
                    }else{
                        $singOnMsg = 'Ocurrio un error con el proveedor de firma digital(error de archivo1), el documento no se firmo!';
                        //***************************************************************************************
                        foreach ($response_list as $item_error) {
                            if($item_error['error']){
                                $singOnMsg .= ", ".$item_error['mesagge'];
                            }
                        }
                        //***************************************************************************************
                        $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                    }
                }else{
                    $response_process = array('httpStatus' => 400, 'message' => 'El servicio de firma digital esta deshabilitado');
                }
            }else{
                $response_process = array('httpStatus' => 400, 'message' => 'El archivo con el indice electronico no existe');
            }
            //***************************************************************************************************
            return $response_process;
		} catch (PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }catch (\Exception $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

	/**
    * objectActions::singDocumentProcessAndes()
    * genera estampa de tiempo en el documento PDF
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @param bool $stampDoc indica si el documento firmado se debe asignar estampa de tiempo
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
    public function stampDocumentProcessAndes($file_attach = null)
    {
        try{
            $firmaApi = new WsFirmaApiAndes();
			$file_info = pathinfo($file_attach);
            //***************************************************************************************************
            if(file_exists($file_attach))
            {
                $response_stamp = $firmaApi->stampDocumentOnly($file_attach);
				//***********************************************************************************************
				if(!$response_stamp['error']){
					$tmpdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(uniqid().time());
					simad_util::createPath($tmpdir);
					$target_fstamp = $tmpdir.DIRECTORY_SEPARATOR.md5(uniqid().time()).".".$file_info['extension'];
					//********************************************************************************************
					simad_util::getConvertB64ToFile($response_stamp['file_base64'],$target_fstamp);
					$isCopy_fstamp = false;
					if(file_exists($target_fstamp) && is_readable($target_fstamp)){
						$filecontent = file_get_contents($target_fstamp);
						$pdf_freadable = false;
						//*******************************************************************************************
						if (preg_match("/^%PDF-1./", $filecontent)) {
							$pdf_freadable = true;
						}
						//*******************************************************************************************
						if($pdf_freadable){
							if(unlink($file_attach))
								$isCopy_fstamp = @rename($target_fstamp,$file_attach); 
						}
					}
					//***********************************************************************************************
					if($isCopy_fstamp)
						$response_stamp = array('httpStatus' => 200, 'message' => 'El archivo se genero con estampa de tiempo exitosamente');
					else
						$response_stamp = array('httpStatus' => 400, 'message' => 'Ocurrio un error interno en el servidor, el documento no se le asigno la estampa de tiempo,'.$response_stamp['msg_info']);
					//***********************************************************************************************
					simad_util::deleteDirAndFiles($tmpdir);
				}else{
					//$response_stamp = array('httpStatus' => 400, 'message' => $response_stamp['msg_info']);
					$response_stamp = array('httpStatus' => 400, 'message' => 'Ocurrio un error con el proveedor de firma digital');
				}
            }else{
                $response_stamp = array('httpStatus' => 400, 'message' => 'El archivo con el indice electronico no existe');
            }
            //***************************************************************************************************
            return $response_stamp;
		} catch (PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
		}catch (\IOException $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }catch (\Exception $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

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
				$read_sections = array('ocr_engine_cli_command','ocr_engine_cli_arguments','verapdf_cli_command_path');
				$ini_array = simad_util::readConfigFileApp($read_sections);
				//***********************************************************************************************
				$verapdf_cli_path = isset($ini_array['verapdf_cli_command_path']) ? $ini_array['verapdf_cli_command_path'] : 'verapdf';
				$veraPdf = new VeraPDFWrapper($verapdf_cli_path);
				$result_verapdf = $veraPdf->validatePDFA($file_path);
				if ($result_verapdf['pdf_type'] != null && $result_verapdf['pdfa_valid'] === true) {
					return array('httpStatus' => 300, 'message' => 'El documento es un PDF/A, no se puede generar el OCR');
				}
				//***********************************************************************************************
				$tmpdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(uniqid().time());
				simad_util::createPath($tmpdir);
				$target_focr = $tmpdir.DIRECTORY_SEPARATOR.md5(uniqid().time()).".".$file_info['extension'];
				//***********************************************************************************************
				$ocr_engine = isset($ini_array['ocr_engine_cli_command']) ? $ini_array['ocr_engine_cli_command'] : null;
				$ocr_arguments = isset($ini_array['ocr_engine_cli_arguments']) ? $ini_array['ocr_engine_cli_arguments'] : null;
				//***********************************************************************************************
				$ocrPdfWrapper = new OcrPDFWrapper();
				$result_ocrpdf = $ocrPdfWrapper->has_selectable_text($file_path);
				if($result_ocrpdf === true && 1 != 1){// se evita la ejecucion de este bloque de codigo
					if(strpos($ocr_arguments,'--skip-text') === false){
						$ocr_arguments = str_replace(array('--force-ocr'),'--skip-text',$ocr_arguments);
						$ocr_arguments = str_replace(array('--redo-ocr'),'',$ocr_arguments);
					}else{
						$ocr_arguments = str_replace(array('--force-ocr','--redo-ocr'),'',$ocr_arguments);
					}
				}
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
		}catch (\InvalidArgumentException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }catch (\IOException $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
		}catch (\Exception $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }
	
	/**
    * objectActions::setSoporteExpedienteByOrigenDoc()
    * Establece el soporte documental del expediente, teniendo en cuenta el origen de cada documento creado en el expediente
    */ 
    public function setSoporteExpedienteByOrigenDoc()
    {
		try{
			$unidad_documental = $this->getUnidadDocumental();
			$origendoc_group = ContenidoUnidadDocumentalPeer::getGroupOrigenDocByExpPk($this->getUnidaddocumentalId());
			//var_dump($origendoc_group);
			//***************************************************************************************************************************
			if(!empty($origendoc_group)){
				$soportedoc_id = $unidad_documental->getSoporteunidaddocumentalId();
				if(count($origendoc_group) === 1){
					if(array_search('Electrónico',$origendoc_group) !== false){
						$soportedoc_id = 1;//ELECTRONICO
					}elseif(array_search('Digitalizado',$origendoc_group) !== false){
						$soportedoc_id = 5;//HIBRIDO
					}elseif(array_search('Fisico',$origendoc_group) !== false){
						$soportedoc_id = 6;//FISICO
					}else{
						$soportedoc_id = 5;//HIBRIDO
					}
				}elseif(count(array_uintersect($origendoc_group,array('Digitalizado','Fisico'), "strcasecmp")) == 2){
					$soportedoc_id = 5;//HIBRIDO
				}elseif(count(array_uintersect($origendoc_group,array('Electrónico','Fisico'), "strcasecmp")) == 2){
					$soportedoc_id = 5;//HIBRIDO
				}elseif(count(array_uintersect($origendoc_group,array('Electrónico','Digitalizado')), "strcasecmp") == 2){
					$soportedoc_id = 5;//HIBRIDO
				}elseif(count(array_uintersect($origendoc_group,array('Digitalizado','Electrónico'), "strcasecmp")) == 2){
					$soportedoc_id = 5;//HIBRIDO
				}
				//***************************************************************************************************************************
				if($soportedoc_id != $unidad_documental->getSoporteunidaddocumentalId()){
					$unidad_documental->setSoporteunidaddocumentalId($soportedoc_id);
					$unidad_documental->save();
				}
			}
		}catch (PropelException $th) {
            return false;
		}catch (\Exception $th) {
            return false;
        }catch (\Throwable $th) {
            return false;
        }
	}

	/**
    * ContenidoUnidadDocumentalPeer::closeExpedienteByTipoDoc()
    * Permite cerrar un expediente validando las reglas configuradas para los expedientes
	* @return bool true|false
    */
	public function closeExpedienteByTipoDoc()
    {
		try {
			$tipodoc_closeexp = $this->getTipoDocumental()->getCierraExpediente();
			$subserie_id = $this->getUnidadDocumental()->getSubserieId();
			$unidaddocumental_id = $this->getUnidaddocumentalId();
			//***********************************************************************************************
			if(empty($tipodoc_closeexp))//no cierra el expediente el TD
			{
				$countFaltan_tdCierraExpediente = TipoDocumentalPeer::getCountTipoDocCloseExpFaltantes($subserie_id, $unidaddocumental_id, $this->getTipodocumentalId());
				//*******************************************************************************************
				if(empty($countFaltan_tdCierraExpediente))
				{
					$tdNew_esObligatorio = TipoDocumentalPeer::getCountTipoDocRequiredFaltantes($subserie_id, $unidaddocumental_id, $this->getTipodocumentalId());
					//***************************************************************************************
					if(empty($tdNew_esObligatorio)){//todos los obligatorios estan creados
						$respuesta_array = $this->getUnidadDocumental()->cerrarUnidadDocumental($this->getFechaDocumento());
						return array('isError' => $respuesta_array['isError'],'mensaje' => $respuesta_array['mensaje']);
					}
				}
			}else{
				$countFaltan_tdObligatorios = TipoDocumentalPeer::getCountTipoDocRequiredFaltantes($subserie_id, $unidaddocumental_id, $this->getTipodocumentalId());
				//*******************************************************************************************
				if(empty($countFaltan_tdObligatorios)){//todos los obligatorios estan creados
					$respuesta_array = $this->getUnidadDocumental()->cerrarUnidadDocumental($this->getFechaDocumento());
					return array('isError' => $respuesta_array['isError'],'mensaje' => $respuesta_array['mensaje']);
				}
			}
			//***********************************************************************************************
			return array('isError' => false,'mensaje' => 'Actividad realizada satisfactoriamente');
		}catch (PropelException $th){
			return array('isError' => true,'mensaje' => 'Error interno en el servidor');
		}catch (\Exception $th){
            return array('isError' => true,'mensaje' => 'Error interno en el servidor');
		}catch (\Throwable $th){
			return array('isError' => true,'mensaje' => 'Error interno en el servidor');
		}
    }
}
