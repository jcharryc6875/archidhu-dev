<?php

/**
 * Subclass for representing a row from the 'unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UnidadDocumental extends BaseUnidadDocumental
{
	public function __toString(){
		return $this->getCodigoBarras()." - ".$this->getTitulo();
	}
	
    public function getCodigoTitulo(){
		return $this->getCodigoBarras()." - ".$this->getTitulo();
	}
	
	public function getUbicacionTopografica()
    {
        return sprintf('BODEGA_%s/CUERPO_%s/TORRE_%s/PISO_%s', $this->getGeoBodega(), $this->getGeoCuerpo(), $this->getGeoTorre(), $this->getGeoPiso());
	}
	
    public function cerrarUnidadDocumental($fecha_cierre = null, $obs_cierre = null, $acta_cierre = null)
	{
        $usuario_conectado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//***************************************************************************************************
        $unidaddoc_anterior = clone $this;
        $message = "";
        //***************************************************************************************************
        try 
        {
            if($this->getEstaCerrado())
            {
                return array('isError'=>true,'codeStatus' => 403,'mensaje' => 'El expediente ya esta cerrado');
            }
            //***********************************************************************************************
            $observaciones_cierre = trim($obs_cierre) ? trim($obs_cierre) : "Se cierra el expediente automatico por tipo documental";
            $fecha_cierre = trim($fecha_cierre) ? trim($fecha_cierre) : date("Y-m-d");
            $acta_cierre = trim($acta_cierre) ? trim($acta_cierre) : null;
            $newestadounidaddoc_id = 4;
            //***********************************************************************************************
            $this->setEstaCerrado(1);
            $this->setObsCierre($observaciones_cierre);
            $this->setFechaCierre($fecha_cierre);
            $this->setActaCierre($acta_cierre);
            //***********************************************************************************************
            $new_vencimiento = $this->getFechaVencimientoCustom($fecha_cierre);
            $this->setFechavencimiento($new_vencimiento);
            $this->setEstadounidaddocumentalId($newestadounidaddoc_id);
            //***********************************************************************************************
            $source_temp = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
            $genFileInDisk = true;
            //***********************************************************************************************
            $filename = $this->generateIndiceElectronico($genFileInDisk);
            $target_path = $source_temp.DIRECTORY_SEPARATOR.$filename;
            //***********************************************************************************************
            if(file_exists($target_path))
            {
                $response = $this->singExpDocumentProcess($target_path,true);
                //*******************************************************************************************
                $this->save();
                //*******************************************************************************************
                AuditLogPeer::guardarAuditoriaLite("UnidadDocumental",$unidaddoc_anterior,$this,ModulesEnable::Archivo,$this->getCodigoBarras());
                //*******************************************************************************************
                if($response['httpStatus'] == 200)
                {
                    return array('isError' => false, 'codeStatus' => $response['httpStatus'],'mensaje' => 'El expediente se cerro satisfactoriamente, por favor valide el indice electronico');
                }
                else
                {
                    $str_error = 'Ocurrio un error firmando el indice electronico, el expediente se cerro sin embargo el indice electronico no se firmo';
                    $message .= empty(trim($response['message'])) ? $str_error : $str_error.",".trim($response['message']);
                    return array('isError' => true, 'codeStatus' => $response['httpStatus'], 'mensaje' => $message);
                }
            }
            else
            {
                return array('isError' => true, 'codeStatus' => 400, 'mensaje' => 'Ocurrio un error al generar el archivo del indice electronico, no puede cerrar el expediente');
            }           
        } catch (PropelException $th) {
            return array('isError' => true,'codeStatus' => 500,'mensaje' => 'Ocurrio un error accediendo a los datos');
        } catch (\Exception $th) {
            return array('isError' => true,'codeStatus' => 500,'mensaje' => 'Ocurrio un error en la aplicacion');
        } catch (\Throwable $th) {
            return array('isError' => true,'codeStatus' => 500,'mensaje' => 'Ocurrio un error en el servidor');
        }
	}

    public function getUrlIndiceElectronico()
    {
        try{
            $source_temp = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
            $genFileInDisk = true;
            //********************************************************************************
            $filename = $this->generateIndiceElectronico($genFileInDisk);
            $target_path = $source_temp.DIRECTORY_SEPARATOR.$filename;
            //********************************************************************************
            return $target_path;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getFechaVencimientoCustom($fecha_final = null){
        $tiempo_retencion = 0;
        $campo_vencimiento = $fecha_final != null ? $fecha_final : date("Y-m-d");
        if($this->getLocalizacionunidaddocumentalId() == 1){
            $tiempo_retencion = $this->getSubserie()->getAnosEnGestion();
        }elseif($this->getLocalizacionunidaddocumentalId() == 2){
            $tiempo_retencion = $this->getSubserie()->getAnosEnCentral();
        }elseif($this->getLocalizacionunidaddocumentalId() == 3){
            $tiempo_retencion = $this->getSubserie()->getAnosEnHistorico();
        }
        ///////////// FECHA DE VENCIMIENTO CON TABLAS DE RETENCION DOCUMENTAL
        //*****************************************************************************************
        try{
            $fecha = new DateTime($campo_vencimiento);
            if(is_numeric($tiempo_retencion)){
                $fecha->add(new DateInterval('P'.$tiempo_retencion.'Y'));
            }
            $fecha_vencimiento = $fecha->format('Y-m-d');
        }catch (Exception $ex){
            $fecha_vencimiento = AddYears(date("Y-m-d"),$tiempo_retencion);
        }
        //*****************************************************************************************
        $fvence_current = new DateTime($this->getFechavencimiento());
        $fvence_new = new DateTime($fecha_vencimiento);
        if($fvence_current > $fvence_new){
            $fecha_vencimiento = $this->getFechavencimiento();
        }
        //*****************************************************************************************
        return $fecha_vencimiento;
	}

    public function getFechaVencimientoNewFase($fecha_final = null)
    {
        $tiempo_retencion = 0;
        $campo_vencimiento = $fecha_final != null ? $fecha_final : date("Y-m-d");
        if($this->getLocalizacionunidaddocumentalId() == 1)
        {
            $tiempo_retencion = $this->getSubserie()->getAnosEnGestion();
        }
        elseif($this->getLocalizacionunidaddocumentalId() == 2)
        {
            $tiempo_retencion = $this->getSubserie()->getAnosEnCentral();
        }
        elseif($this->getLocalizacionunidaddocumentalId() == 3)
        {
            $tiempo_retencion = $this->getSubserie()->getAnosEnHistorico();
        }
        ///////////// FECHA DE VENCIMIENTO CON TABLAS DE RETENCION DOCUMENTAL
        //*****************************************************************************************
        try
        {
            $fecha = new DateTime($campo_vencimiento);
            if(is_numeric($tiempo_retencion))
            {
                $fecha->add(new DateInterval('P'.$tiempo_retencion.'Y'));
            }
            $fecha_vencimiento = $fecha->format('Y-m-d');
        }
        catch (Exception $ex)
        {
            $fecha_vencimiento = AddYears(date("Y-m-d"),$tiempo_retencion);
        }
        //*****************************************************************************************
        return $fecha_vencimiento;
	}

	public function getBaseUrlAttachment($usuario_id = null, $isTemp = false, $isCreate = true){
        $array_url = array();
        //*****************************************************************************************
    	$dirRaiz  = ParametroPeer::retrieveByPk(17)->getValortexto();
    	$alias_str = ParametroPeer::retrieveByPk(32)->getValortexto();
    	$dirTemp  = ParametroPeer::retrieveByPk(65)->getValortexto();
        //*****************************************************************************************
        if($usuario_id){
            $usuario = UsuarioPeer::retrieveByPK($usuario_id);
            $regional = $usuario->getRegional();
        }elseif($this->getRegionalId()){
            $regional = $this->getRegionalId() ? $this->getRegional() : "";
        }else{
            $usuario_resp = UnidadDocumentalPeer::getUsuarioIdExpByRol($this->getPrimaryKey(),2);
            $usuario = UsuarioPeer::retrieveByPK($usuario_resp);
            $regional = $usuario->getRegional();
        }        
        //*****************************************************************************************
        //$subserie = SubseriePeer::retrieveByPk($this->getSubserieId());
        $codigo_barras = trim($this->getCodigoBarras());
    	$subserie_text = simad_util::clean_str(trim($this->getSubserie()->getDescripcion()));
    	$serie_text = simad_util::clean_str(trim($this->getSubserie()->getSerie()->getDescripcion()));
    	$dependencia_text = simad_util::clean_str(trim($this->getSubserie()->getSerie()->getDependencia()->getNombre()));
        //*****************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
        //*****************************************************************************************
        $ruta_trd = $dependencia_text.DIRECTORY_SEPARATOR.$serie_text.DIRECTORY_SEPARATOR.$subserie_text.DIRECTORY_SEPARATOR.$codigo_barras;
        $directorio_attach = simad_util::NormalizePath($dirRaiz.DIRECTORY_SEPARATOR.$entidad_text.DIRECTORY_SEPARATOR.$ruta_trd);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.DIRECTORY_SEPARATOR.$entidad_folder.DIRECTORY_SEPARATOR.$dirTemp);
        $basic_path = $entidad_text.DIRECTORY_SEPARATOR.$ruta_trd;
        $alias_web = str_replace("\\","/",$basic_path);
        //*****************************************************************************************
        if($isCreate){ $directorio_attach = simad_util::createPath($directorio_attach); }
        //*****************************************************************************************
        $array_url['absolute_path'] = str_replace("\\",DIRECTORY_SEPARATOR,$dirRaiz);;
    	$array_url['basic_path'] = $basic_path;
    	$array_url['full_path'] = $directorio_attach;
    	$array_url['temp_path'] = $directorio_tmp;
        $array_url['alias_web'] = ($alias_str.$alias_web);
    	//*****************************************************************************************
    	return $array_url;
	}

    /**
     * UnidadDocumental::getListUsuariosObject
     * Genera una lista de los usuarios asociados al expediente
     * @return mixed retorna un array con la lista de usuarios
     * @throws PropelException
     */
    public function getListUsuariosObject()
    {
        $ilist_users = array();
        try {
            foreach ($this->getUnidaddocumentalUsuarios() as $uitem) {
                if($uitem->getRolusuunidaddocId() == 1){//creador
                    $ilist_users['usuario_creador'] = $uitem->getUsuario();
                }else if($uitem->getRolusuunidaddocId() == 2){//responsable
                    $ilist_users['usuario_responsable'] = $uitem->getUsuario();
                }else if($uitem->getRolusuunidaddocId() == 3){//inventariador
                    $ilist_users['usuario_inventariador'] = $uitem->getUsuario();
                }
            }
        } catch (PropelException $th) {
            //throw $th;
            return $ilist_users;
        } catch (\Exception $th) {
            //throw $th;
            return $ilist_users;
        } catch (\Throwable $th) {
            //throw $th;
            return $ilist_users;
        }
        //*****************************************************************************************
        return $ilist_users;
    }

    /**
     * UnidadDocumental::generateIndiceElectronico
     * Genera el indice electronico del expediente segun el formato seleccionado
     * @param bool $genFileInDisk indica si el archivo se genera en el disco o se genera al vuelo
     * @param string $format indica el formato del archiv por defecto se genera en pdf
     * @return string retorna la ruta donde se crea el archivo del indice electronico
     * @throws PropelException
     */
    public function generateIndiceElectronico($genFileInDisk = true,$format = "pdf")
    {
        try {
            $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $dataUser  = UsuarioPeer::retrieveByPK($usuariologuiado);
            //***************************************************************************************
            // CONTENIDO UNIDAD DOCUMENTAL
            $f = new Criteria();
            $f->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $this->getPrimaryKey());
            $f->addAscendingOrderByColumn(ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO);
            $contenido_documentales = ContenidoUnidadDocumentalPeer::doSelectJoinAll($f);
            //***************************************************************************************
            // CONSULTA RESPONSABLE DEL DOCUMENTO
            $c = new Criteria();
            $c->add(UnidaddocumentalUsuarioPeer::ROLUSUUNIDADDOC_ID, 2);
            $c->add(UnidaddocumentalUsuarioPeer::UNIDADDOCUMENTAL_ID, $this->getPrimaryKey());
            $responsable = UnidaddocumentalUsuarioPeer::doSelectOne($c);
            //************************MANEJO PARA CREACION DIRECTORIO Y ARCHIVO A CONVERTIR***********
            //directorio temporal para guardar los archivos a convertir a pdf
            $dir_tmp = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."com_html".DIRECTORY_SEPARATOR."archivo".DIRECTORY_SEPARATOR;
            if (!is_dir($dir_tmp)) {//verificar si el directorio existe de lo contrario se crea
                simad_util::createPath($dir_tmp,0766);//crea el directorio destiono
            }
            //nombre del archivo temporal que contiene los datos a convertir
            $aleatorio = rand(1, 10000000);  
            $hash_is = md5($this->getPrimaryKey().date('YmdGis').$aleatorio);
            $nomb_file_html = simad_util::uniquename($dir_tmp,$hash_is);
            //******************************************************************************************************
            if(file_exists($dir_tmp.$nomb_file_html)){//verificar si ya esta generado el archivo a convertir
                unlink($dir_tmp.$nomb_file_html);//se elimina para actualizar el contenido del documento
            }
            $pt = fopen($dir_tmp.$nomb_file_html, 'w');//se crea el archivo a convertir
            //******************************************************************************************************
            $path_logo = sfConfig::get('base_simad') . "/images/encabezado_carta/";
            $default_logo = "logo_planilla.png";
            //******************************************************************************************************
            if($this->getRegionalId() != null){
                $path_logo .= trim($this->getRegional()->getEntidad()->getLogoCorporativo()) ? 
                                    'logos_carnet/'.trim($this->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }else{
                $path_logo .= trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) ? 
                                    'logos_carnet/'.trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }
            //******************************************DIRECTORIO DE LA PLANTILLAS*********************************
            $dir_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
            //****************************************NOMBRES DE LAS PLANTILLAS*************************************
            $name_plantilla = "rptindiceelectronico.txt";
            $file_name = $dir_plantilla.$name_plantilla;    
            $plantilla_contents = file_get_contents($file_name);
            //**************************************PATRONES PARA REMPLAZAR LOS VALORES*****************************
            $patrones = array();
            $patrones[0]  = '#.#$path_logo#.#';
            $patrones[1]  = '#.#$codigo_barras#.#';
            $patrones[2]  = '#.#$tipo_archivo#.#';
            $patrones[3]  = '#.#$titulo_expediente#.#';
            $patrones[4]  = '#.#$fecha_inicial#.#';
            $patrones[5]  = '#.#$fecha_final#.#';
            $patrones[6]  = '#.#$responsable_expediente#.#';
            $patrones[7]  = '#.#$folios_expediente#.#';
            $patrones[8]  = '#.#$dependencia_expediente#.#';
            $patrones[9]  = '#.#$serie_expediente#.#';
            $patrones[10]  = '#.#$subserie_expediente#.#';
            $patrones[11]  = '#.#$fecha_reporte#.#';
            $patrones[12]  = '#.#$detalles_expediente#.#';
            //******************************************************************************************************
            $sustituciones    = array();
            $sustituciones[0] = $path_logo;
            $sustituciones[1] = trim($this->getCodigoBarras());
            $sustituciones[2] = trim($this->getLocalizacionUnidadDocumental()->getDescripcion());
            $sustituciones[3] = htmlentities(trim($this->getTitulo()));
            $sustituciones[4] = trim($this->getFechaApertura());
            $sustituciones[5] = trim($this->getFechaCierre());
            $sustituciones[6] = htmlentities(trim($responsable->getUsuario()->getNombreAll()));
            $sustituciones[7] = trim($this->getFolios());
            $sustituciones[8] = mb_convert_encoding(trim($this->getSubserie()->getSerie()->getDependencia()),'UTF-8');
            $sustituciones[9] = mb_convert_encoding(trim($this->getSubserie()->getSerie()->getDescripcion()),'UTF-8');
            $sustituciones[10] = mb_convert_encoding(trim($this->getSubserie()->getDescripcion()),'UTF-8');
            $sustituciones[11] = date("Y-m-d G:i:s");
            //******************************************************************************************************
            $version = trim($this->getSubserie()->getSerie()->getDependencia()->getVersionInst());
            $html_contenido = '';
            //******************************************************************************************************
            foreach($contenido_documentales as $item){
                $html_contenido .= '<tr>';
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:60pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.sprintf("%s-%s-%s",trim($item->getTipoDocumental()->getCodigo()),$item->getPrimaryKey(),$version).'</span>
                                        </span>
                                    </td>';
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:120pt">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.htmlentities(trim($item->getDescripcion())).'</span>
                                        </span>
                                    </td>';
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:96pt">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.htmlentities(trim($item->getTipoDocumental())).'</span>
                                        </span>
                                    </td>';

                $html_contenido .= '<td style="border-color:black;white-space:normal; width:50pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getFechaDocumento("Y-m-d")).'</span>
                                        </span>
                                    </td>';
                                    
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:50pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getFechaCreacion("Y-m-d")).'</span>
                                        </span>
                                    </td>';
                
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:60pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getValorHuella()).'</span>
                                        </span>
                                    </td>';
                
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getFuncEncryp()).'</span>
                                        </span>
                                    </td>';
                                    
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getFolioInicial()).'</span>
                                        </span>
                                    </td>';

                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getFolioFinal()).'</span>
                                        </span>
                                    </td>';

                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getOrdenContenido()).'</span>
                                        </span>
                                    </td>';
                
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.strtoupper(trim($item->getFormatFile())).'</span>
                                        </span>
                                    </td>';
                
                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.trim($item->getSizeFile()).'</span>
                                        </span>
                                    </td>';

                $html_contenido .= '<td style="border-color:black;white-space:normal; width:33pt;text-align: center">
                                        <span style="font-size:5.0pt"><span style="color:black"></span>
                                            <span style="color:black">'.htmlentities(trim($item->getOrigenDocumento())).'</span>
                                        </span>
                                    </td>';

                $html_contenido .= '</tr>';
                        
            }
            //******************************************************************************************************
            $sustituciones[12] = $html_contenido;
            //**********************************REMPLAZAR DATOS EN LA PLANTILLA*************************************
            $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents); 	
            //******************************************************************************************************    
            fputs($pt, $template_contents);
            //Cierra el archivo y envia para generar el pdf    
            fclose($pt);
            //******************************************************************************************************
            if($genFileInDisk == true && $format == "pdf")
            {
                $dir_jar = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'pd4ml.jar';
                $filename = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5($nomb_file_html).".".$format;
                $dir_font = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'static';
                $url_site = sfConfig::get('localUrl');
                //**************************************************************************************************
                $java = simad_util::getJavaJdkRoot();
                $format_page = "A4";
                $size_point_page = "840";
                $font_use = "-ttf ".$dir_font;
                $outfile = "-out ".$filename;                
                $margins = "-insets 5,5,5,5,pt";
                $orientation = '-orientation LANDSCAPE';
                $watermark = "";
                $adjustwidth = "";
                //**************************************************************************************************
                $name_file =  "com_html/archivo/".$nomb_file_html;
                $url = sprintf("%s/%s",$url_site,$name_file);
                //**************************************************************************************************
                if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
                    // server platform: Windows
                    $dir_jar = preg_replace('/\//', "\\", $dir_jar);
					$url = "file:".$dir_tmp.$nomb_file_html;
                    $cmdline = "$java -Xmx512m -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
                } else {    		
                    $cmdline = "$java -Xmx512m -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
                }
                //**************************************************************************************************
                //echo $cmdline;exit;
                passthru( $cmdline );
                //**************************************************************************************************
                if(file_exists($filename))
                {
                    return basename($filename);
                }else{
                    return null;
                }
            }else{
                return $nomb_file_html;
            }
        } catch (PropelException $th) {
            //throw $th;
            return null;
        } catch (\Exception $th) {
            //throw $th;
            return null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    /**
    * objectActions::singExpDocumentProcess()
    * Inicia proceso de firmado digital del documento, usando la integracion con alguno de los proveedores de firma digital
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @param bool $stampDoc indica si el documento firmado se debe asignar estampa de tiempo
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
	public function singExpDocumentProcess($fullpath, $stampDoc = false)
    {
		try{
            if(SIGN_DIGITAL_ENABLE == false){
                $storage_expediente = $this->getBaseUrlAttachment();
                //***************************************************************************************************
                $filename   = sprintf("Indice_Electronico_Expediente_%s.%s",trim($this->getCodigoBarras()),'pdf');
                $targetpath = $storage_expediente['full_path'] . DIRECTORY_SEPARATOR . $filename;
                //***************************************************************************************************
                if(!rename($fullpath,$targetpath)){
                    return array('httpStatus' => 400, 'message' => 'Error renombrando el archivo del indice electrónico, no se almaceno correctamente');
                }
                //***************************************************************************************************
                $this->setEidxAbspath($storage_expediente['absolute_path']);
                $this->setEidxRelpath($storage_expediente['basic_path']. DIRECTORY_SEPARATOR . $filename);
                //***************************************************************************************************
                return array('httpStatus' => 200, 'message' => 'El indice electrónico se genero correctamente y fue almacenado en el carpeta del expediente');
            }else if(file_exists($fullpath) && is_readable($fullpath)){
                $response_sing = $this->singDocumentProcessAndes($fullpath,$stampDoc);
                //***************************************************************************************************
                if($response_sing['httpStatus'] !== 200){
                    $storage_expediente = $this->getBaseUrlAttachment();
                    //***********************************************************************************************
                    $filename   = sprintf("Indice_Electronico_Expediente_%s.%s",trim($this->getCodigoBarras()),'pdf');
                    $targetpath = $storage_expediente['full_path'] . DIRECTORY_SEPARATOR . $filename;
                    //***********************************************************************************************
                    if(!rename($fullpath,$targetpath)){
                        return array('httpStatus' => 400, 'message' => 'Error renombrando el archivo del indice electrónico, no se almaceno correctamente');
                    }else{
                        $msg_idx = $response_sing['message'] . ", El indice se almaceno en el expediente sin la firma digital";
                        $response_sing['message'] = $msg_idx;
                    }
                    //***********************************************************************************************
                    $this->setEidxAbspath($storage_expediente['absolute_path']);
                    $this->setEidxRelpath($storage_expediente['basic_path']. DIRECTORY_SEPARATOR . $filename);
                }
                //***************************************************************************************************
                return $response_sing;
                //return $this->singDocumentProcessGse($signAllPages);
            }else{
                return array('httpStatus' => 400, 'message' => 'Este radicado no se puede firmar el archivo no existe o esta se puede leer');
            }
        } catch (PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error de acceso a los datos, Por favor comuniquese con el administrador,'.$ex->getMessage());
		} catch (\Exception $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno de la aplicacion, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,'.$ex->getMessage());
        }
	}

    /**
    * objectActions::singDocumentProcessAndes()
    * genera la firma digital al documento
    * @param string $fullpath ruta absoluta del archivo que se debe firmar
    * @param bool $stampDoc indica si el documento firmado se debe asignar estampa de tiempo
    * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
    */ 
    public function singDocumentProcessAndes($fullpath = null, $stampDoc = false)
    {
        try{
            $firmaApi = new WsFirmaApiAndes();
            //***************************************************************************************************
            $storage_expediente = $this->getBaseUrlAttachment();
            //***************************************************************************************************
            //$dir_raiz   = $storage_expediente['temp_path'];
            $filename   = sprintf("Indice_Electronico_Expediente_%s.%s",trim($this->getCodigoBarras()),'pdf');
            //$tempdir_firma = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.md5(date("YmdGis"));
            $ulist_expediente = $this->getListUsuariosObject();
            $ulist_firma = $ulist_expediente['usuario_responsable'];
            //$isFirmaDigital = count($ulist_firma) ? true : false;
            $file_attach = trim($fullpath) ? $fullpath : $this->getUrlIndiceElectronico();
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
                $targetpath = $storage_expediente['full_path'] . DIRECTORY_SEPARATOR . $filename;
                //***********************************************************************************************
                $filesing = $file_attach;$response_list = array();
                //***********************************************************************************************
                if(WsFirmaApiAndes::SERVICE_ENABLE_WS){
					$Xc = 5;$Yc = 640;$Wc = 40;$Hc = 120;$fy1=100;
                    //*******************************************************************************************
                    $isSingned = true;$ubicacionFirma = array('x' => $Xc,'y' => $Yc,'w' => $Wc,'h' => $Hc);                    
                    $firma_info = $ulist_firma->getNombreApellido();
                    //***************************************************************************************
                    $b64firma = sfConfig::get("sf_lib_dir").DIRECTORY_SEPARATOR.'efirma'.DIRECTORY_SEPARATOR.WsFirmaApiAndes::FIRMA_VISIBLE_IMAGE;
                    //***************************************************************************************
                    $response_sing = $firmaApi->documentWsApiFirmaAndes($ulist_firma->getLoginFirma(),base64_decode($ulist_firma->getPassFirma()),$filesing,$targetpath,$b64firma,$ubicacionFirma);
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
						if($isSingned){ $isSingned = simad_util::getConvertB64ToFile($response_sing['filesing'],$targetpath); }
					}
                    //*******************************************************************************************
                    if($isSingned){
                        $response_process = array('httpStatus' => 200, 'message' => 'Documento firmado existosamente!');
                        $this->setEidxAbspath($storage_expediente['absolute_path']);
                        $this->setEidxRelpath($storage_expediente['basic_path']. DIRECTORY_SEPARATOR . $filename);
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
	
	public function generateIndiceXml()
    {
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $xml_file_name = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . $this->getCodigoBarras() .'.xml';
        //*********************************************************************************************
        $root = $dom->createElement('TipoDocumentoFoliado');
        $attr_root = new DOMAttr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNode($attr_root);
        $version_trd = trim($this->getSubserie()->getSerie()->getDependencia()->getVersionInst());
        $list_documentos = ContenidoUnidadDocumentalPeer::getListDocumentsByComId($this->getPrimaryKey());
        //*********************************************************************************************
        $zip = new ZipArchive();
        $zipFileName = md5(date("YmdGis")).".zip";
        $pathzip = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.$zipFileName;
        //*********************************************************************************************
        if(file_exists($pathzip)) {        
            unlink ($pathzip);
        }
        //*********************************************************************************************
        if ($zip->open($pathzip, ZIPARCHIVE::CREATE) != TRUE) {
                die ("Could not open archive");
        }
        //*********************************************************************************************
        foreach ($list_documentos as $documento) 
        {
            $doc_root = $dom->createElement('DocumentoIndizado');
            
            $child_node = $dom->createElement('ID', sprintf("%s-%s-%s",trim($documento->getTipoDocumental()->getCodigo()),$documento->getPrimaryKey(),$version_trd)); 
            $doc_root->appendChild($child_node);
            
            $child_node = $dom->createElement('Nombre_Documento', $documento->getDescripcion()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Tipologia_Documental', $documento->getTipoDocumental()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Fecha_Creacion_Documento', $documento->getFechaDocumento()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Fecha_Incorporacion_Expediente', $documento->getFechaCreacion()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Valor_Huella', $documento->getValorHuella()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Funcion_Resumen', $documento->getFuncEncryp()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Orden_Documento_Expediente', $documento->getOrdenContenido()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Pagina_Inicio', $documento->getFolioInicial()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Pagina_Fin', $documento->getFolioFinal()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Formato', $documento->getFormatFile()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Tamano', $documento->getSizeFile()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Origen_Documento', $documento->getOrigenDocumento()); 
            $doc_root->appendChild($child_node);

            $child_node = $dom->createElement('Observaciones', ""); 
            $doc_root->appendChild($child_node);
			
			$child_node = $dom->createElement('Nombre_Archivo', trim($documento->getRuta())); 
            $doc_root->appendChild($child_node);
			
            $root->appendChild($doc_root);
            //*****************************************************************************************
            if(!empty($documento->getRuta())){
                $filename = sprintf("%s/%s/%s",$documento->getPathAbsolute(),$documento->getPathRelative(),$documento->getRuta());
                if(file_exists($filename)){
                    $zip->addFile($filename,basename($filename));
                }
            }
        }

        /*$movie_node = $dom->createElement('movie');
        
        $attr_movie_id = new DOMAttr('movie_id', '5467');        
        $movie_node->setAttributeNode($attr_movie_id);*/
        //*********************************************************************************************
        $dom->appendChild($root);
        $dom->save($xml_file_name);        
        //*********************************************************************************************
        $zip->addFile($xml_file_name,basename($xml_file_name));
        $zip->close();
        //*********************************************************************************************
        return $pathzip;
    }

    public function createFileRotuloCarpeta($handle)
    {
        try {
            //logo de la entidad
            $path_logo = sfConfig::get('base_simad') . "/images/encabezado_carta/";
            $default_logo = "logo_planilla.png";
            //******************************************************************************************************
            if(!empty($this->getRegionalId())){
                $path_logo .= trim($this->getRegional()->getEntidad()->getLogoCorporativo()) ? 'logos_carnet/'.trim($this->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }else{
                $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
                $dataUser  = UsuarioPeer::retrieveByPK($usuariologuiado);
                $path_logo .= trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) ? 'logos_carnet/'.trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }
            //******************************************DIRECTORIO DE LA PLANTILLAS*********************************
            $dir_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
            //****************************************NOMBRES DE LAS PLANTILLAS*************************************
            $name_plantilla = "rptrotulocarpeta.txt";
            $file_name = $dir_plantilla.$name_plantilla;    
            $plantilla_contents = file_get_contents($file_name);
            //**************************************PATRONES PARA REMPLAZAR LOS VALORES*****************************
            $patrones = array();
            $patrones[0]  = '#.#$path_logo#.#';
            $patrones[1]  = '#.#$dependencia_nombre#.#';
            $patrones[2]  = '#.#$dependencia_codigo#.#';
            $patrones[3]  = '#.#$ofproductora_nombre#.#';
            $patrones[4]  = '#.#$ofproductora_codigo#.#';
            $patrones[5]  = '#.#$serie_nombre#.#';
            $patrones[6]  = '#.#$serie_codigo#.#';
            $patrones[7]  = '#.#$subserie_nombre#.#';
            $patrones[8]  = '#.#$subserie_codigo#.#';
            $patrones[9]  = '#.#$exp_marcolegal#.#';
            $patrones[10]  = '#.#$exp_titulo#.#';
            $patrones[11]  = '#.#$exp_codigobarras#.#';
            $patrones[12]  = '#.#$exp_fechainicial#.#';
            $patrones[13]  = '#.#$exp_fechafinal#.#';
            $patrones[14]  = '#.#$numvolumen#.#';
            $patrones[15]  = '#.#$total_folios#.#';
            $patrones[16]  = '#.#$exp_ubicacion#.#';	
            $patrones[17]  = '#.#$qrcodigo#.#';
            $patrones[18]  = '#.#$version_formato#.#';
            $patrones[19]  = '#.#$codigo_formato#.#';
            $patrones[20]  = '#.#$fecha_formato#.#';

            $patrones[21]  = '#.#$numbodega#.#';
            $patrones[22]  = '#.#$ncuerpo#.#';
            $patrones[23]  = '#.#$numtorre#.#';
            $patrones[24]  = '#.#$numpiso#.#';
            $patrones[25]  = '#.#$numnivel#.#';
            $patrones[26]  = '#.#$numpasillo#.#';
            $patrones[27]  = '#.#$position_cj#.#';
            $patrones[28]  = '#.#$codigo_barras#.#';
            $patrones[29]  = '#.#$numero_caja#.#';
	        $patrones[30]  = '#.#$numero_carpeta#.#';
            //******************************************************************************************************
            $exp_ubicacion = "";
            if($this->getLocalizacionunidaddocumentalId() == 1)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionengestion(),'UTF-8');
            else if($this->getLocalizacionunidaddocumentalId() == 2)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionencentral(),'UTF-8');
            else if($this->getLocalizacionunidaddocumentalId() == 3)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionenhistorico(),'UTF-8');
            else
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionengestion(),'UTF-8');
            //******************************************************************************************************
            if(empty($exp_ubicacion)){
                $exp_ubicacion = sprintf("%s / %s",trim($this->getNumeroCaja()),trim($this->getNumeroCarpeta()));
            }
            //******************************************************************************************************
            $qrfile = simad_util::generateCodeQrInFile($this->getCodigoTitulo(),20,QR_ECLEVEL_L,1);
            $codebarfile = simad_util::generateCodeBarInFile($exp_ubicacion,false);
            $urlQrCode = sfConfig::get('localUrl') . "/tmp/" . $qrfile;
            $urlBarCode = sfConfig::get('localUrl') . "/tmp/" . $codebarfile;
            //******************************************************************************************************
            $sustituciones    = array();
            $sustituciones[0] = $path_logo;
            $sustituciones[1] = mb_convert_encoding($this->getSubserie()->getSerie()->getDependencia()->getNombre(),'UTF-8');
            $sustituciones[2] = mb_convert_encoding($this->getSubserie()->getSerie()->getDependencia()->getCodigo(),'UTF-8');
            $sustituciones[3] = mb_convert_encoding($this->getSubserie()->getSerie()->getDependencia()->getOficinaProductora()->getDescripcion(),'UTF-8');
            $sustituciones[4] = $this->getSubserie()->getSerie()->getDependencia()->getOficinaProductora()->getCodigo();
            $sustituciones[5] = mb_convert_encoding($this->getSubserie()->getSerie()->getDescripcion(),'UTF-8');
            $sustituciones[6] = $this->getSubserie()->getSerie()->getCodigo();
            $sustituciones[7] = mb_convert_encoding($this->getSubserie()->getDescripcion(),'UTF-8');
            $sustituciones[8] = $this->getSubserie()->getCodigo();
            $sustituciones[9] = "N/A";//Marco Normativo
            $sustituciones[10] = mb_convert_encoding($this->getTitulo(),'UTF-8');
            $sustituciones[11] = trim($this->getCodigoBarras());
            $sustituciones[12] = $this->getFechaApertura('Y-m-d');
            $sustituciones[13] = $this->getFechaCierre('Y-m-d');
            $sustituciones[14] = $this->getVolumen();
            $sustituciones[15] = $this->getFolios();
            $sustituciones[16] = $urlBarCode;
            $sustituciones[17] = $urlQrCode;
            $sustituciones[18] = "3";//Version Formato
            $sustituciones[19] = "710,14,15-5";//Codigo Formato
            $sustituciones[20] = "27/07/2022";//Fecha Formato
            $sustituciones[21] = trim($this->getGeoBodega());
            $sustituciones[22] = trim($this->getGeoCuerpo());
            $sustituciones[23] = trim($this->getGeoTorre());
            $sustituciones[24] = trim($this->getGeoPiso());
            $sustituciones[25] = "";
            $sustituciones[26] = "";
            $sustituciones[27] = "";
            $sustituciones[28] = trim($this->getCodigoBarras());
            $sustituciones[29] = trim($this->getNumeroCaja());
            $sustituciones[30] = trim($this->getNumeroCarpeta());
            //**********************************REMPLAZAR DATOS EN LA PLANTILLA*************************************
            $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents); 	
            //******************************************************************************************************    
            fputs($handle, $template_contents);
        } catch (PropelException $th) {
            //throw $th;
        } catch (Exception $th) {
            //throw $th;
        }
    }

    public function createFileHojaControl($handle)
    {
        try {
            //logo de la entidad
            $path_logo = sfConfig::get('base_simad') . "/images/encabezado_carta/";
            $default_logo = "logo_planilla.png";
            //******************************************************************************************************
            if(!empty($this->getRegionalId())){
                $path_logo .= trim($this->getRegional()->getEntidad()->getLogoCorporativo()) ? 'logos_carnet/'.trim($this->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }else{
                $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
                $dataUser  = UsuarioPeer::retrieveByPK($usuariologuiado);
                $path_logo .= trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) ? 'logos_carnet/'.trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            }
            //******************************************************************************************************
            // CONTENIDO UNIDAD DOCUMENTAL
            $f = new Criteria();
            $f->setLimit(250);
            $f->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $this->getPrimaryKey());
            $contenidos_documentales = ContenidoUnidadDocumentalPeer::doSelect($f);
            //******************************************DIRECTORIO DE LA PLANTILLAS*********************************
            $dir_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
            //****************************************NOMBRES DE LAS PLANTILLAS*************************************
            $name_plantilla = "rpthojacontrol.txt";
            $file_name = $dir_plantilla.$name_plantilla;    
            $plantilla_contents = file_get_contents($file_name);
            //**************************************PATRONES PARA REMPLAZAR LOS VALORES*****************************
            $patrones = array();
            $patrones[0]  = '#.#$path_logo#.#';
            $patrones[1]  = '#.#$dependencia_nombre#.#';
            $patrones[2]  = '#.#$oficina_productora#.#';
            $patrones[3]  = '#.#$macro_proceso#.#';
            $patrones[4]  = '#.#$fecha_elaboracion#.#';
            $patrones[5]  = '#.#$version_formato#.#';
            $patrones[6]  = '#.#$codigo_formato#.#';
            $patrones[7]  = '#.#$fecha_formato#.#';
            $patrones[8]  = '#.#$codigo_barras#.#';
            $patrones[9]  = '#.#$ubicacion_expediente#.#';
            $patrones[10]  = '#.#$titulo_expediente#.#';
            $patrones[11]  = '#.#body_content#.#';
            //******************************************************************************************************
            $exp_ubicacion = "";
            if($this->getLocalizacionunidaddocumentalId() == 1)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionengestion(),'UTF-8');
            else if($this->getLocalizacionunidaddocumentalId() == 2)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionencentral(),'UTF-8');
            else if($this->getLocalizacionunidaddocumentalId() == 3)
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionenhistorico(),'UTF-8');
            else
                $exp_ubicacion = mb_convert_encoding($this->getUbicacionengestion(),'UTF-8');
            //******************************************************************************************************
            $sustituciones    = array();
            $sustituciones[0] = $path_logo;
            $sustituciones[1] = mb_convert_encoding($this->getSubserie()->getSerie()->getDependencia()->getNombre(),'UTF-8');
            $sustituciones[2] = mb_convert_encoding($this->getSubserie()->getSerie()->getDependencia()->getOficinaProductora(),'UTF-8');
            $sustituciones[3] = "";
            $sustituciones[4] = date("Y-m-d");
            $sustituciones[5] = "05";
            $sustituciones[6] = "710,14,15-40";
            $sustituciones[7] = "27/07/2022";
            $sustituciones[8] = mb_convert_encoding(trim($this->getCodigoBarras()),'UTF-8');
            $sustituciones[9] = mb_convert_encoding(trim($exp_ubicacion),'UTF-8');
            $sustituciones[10] = mb_convert_encoding(trim($this->getTitulo()),'UTF-8');
            //******************************************************************************************************
            $html_contenido = "";$rowIdx = 1;
            foreach($contenidos_documentales as $documentos)
            {
                $html_contenido .= '
                <tr class="row'.$rowIdx.'">
                    <td class="column0 style16 null">'.$rowIdx.'</td>
                    <td class="column1 style73 null style73" colspan="2">'.$documentos->getFolios().'</td>
                    <td class="column4 style29 null style29" colspan="24">'.mb_convert_encoding($documentos->getTipoDocumental(),'UTF-8').'</td>
                    <td class="column13 style30 null style32" colspan="2">'.$documentos->getFolioInicial().'</td>
                    <td class="column15 style30 null style32" colspan="2">'.$documentos->getFolioFinal().'</td>
                    <td class="column17 style74 null style74" colspan="4">'.$documentos->getFechaDocumento('d/m/Y').'</td>
                    <td class="column21 style30 null style32" colspan="15">'.mb_convert_encoding($documentos->getUsuario()->getFullNombre(),'UTF-8').'</td>
                    <td class="column27 style33 null style35" colspan="4">'.$documentos->getFechaCreacion('d/m/Y').'</td>
                    <td class="column33 style36 null style38" colspan="7">'.mb_convert_encoding($documentos->getDescripcion(),'UTF-8').'</td>
                    <td class="column38 style103 null"></td>
                </tr>';
                $rowIdx++;
            }
            //******************************************************************************************************
            $sustituciones[11] = $html_contenido;
            //**********************************REMPLAZAR DATOS EN LA PLANTILLA*************************************
            $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents); 	
            //******************************************************************************************************    
            fputs($handle, $template_contents);
        } catch (PropelException $th) {
            //throw $th;
        } catch (\Exception $th) {
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
