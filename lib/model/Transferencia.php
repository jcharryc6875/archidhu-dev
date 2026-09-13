<?php

/**
 * Subclass for representing a row from the 'TRANSFERENCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Transferencia extends BaseTransferencia
{
    public function enviarAlertas()
    {
        //$unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
        //$transferencia = TransferenciaPeer::retrieveByPK($transferencia_id);
        $codigo_barras = trim($this->getUnidaddocumental()->getCodigoBarras());
        //****************************************************************************
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">
        <br>
        Este es un mensaje para informarle que su solicitud de transferencia esta en estado: ' . mb_strtolower($this->getEstadoTransferencia()) . ' 
        <br>
        <br>
        <b>Fecha Solicitud:</b> ' . $this->getFechaCreacion() . ' <br>
        <b>Numero Solicitud:</b> ' . $this->getTransferenciaId() . '<br>
        <b>Titulo Expediente:</b> ' . $this->getUnidaddocumental()->getTitulo() . '<br>
        <b>Codigo Barras:</b> ' . $codigo_barras . '<br>    
        <b>Observaciones:</b> ' . $this->getObservaciones() . '<br>    
        <br>
        </div>
        </body></html>';
        //****************************************************************************
        $cabeceras = "Content-type: text/html\r\n";
        //****************************************************************************
        $userSolicitaMail = "";
        foreach ($this->getUsuarioTransferencias() as $usuario_email) {
            if ($usuario_email->getRolusuariotransferenciaId() == 1) {
                $userSolicitaMail = trim($usuario_email->getUsuario()->getEmail());
            } /*elseif ($usuario_email->getRolusuariotransferenciaId() == 2) {
                $userAceptaName = $usuario_email->getUsuario();
            }*/
        }
        //****************************************************************************
        if ($userSolicitaMail != "") {        
            try {
                $baseMail = new BaseMailSimad();
                $baseMail->SetSubject(sprintf('CAD : Transferencias Archivo Expediente # %s',$codigo_barras));
                $baseMail->SetMsgHTML($cuerpo);
                $baseMail->SetAddAddress($userSolicitaMail, $userSolicitaMail);
                if ($baseMail->InitSend() === true) {
                    $baseMail->writetolog("Alerta enviada: " . $userSolicitaMail . " Enviado a: " . $userSolicitaMail);
                } else {
                    $baseMail->writetolog("Error al enviar alerta: " . $userSolicitaMail . " Cuenta correo: " . $userSolicitaMail);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    public function envioEmail(Usuario $usuario)
    {
        $codigo_barras = $this->getUnidaddocumental()->getCodigoBarras();
        //*****************************************************************************************
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">
        <br>
        Este es un mensaje para informarle que su solicitud de transferencia fue rechazada: 
        <br>
        <br>
        Fecha Solicitud: ' . $this->getFechaCreacion() . ' <br>
        Numero Solicitud: ' . $this->getTransferenciaId() . '<br>
        Expediente: ' . $this->getUnidaddocumental()->getTitulo() . '<br>
        Codigo Barras: ' . $codigo_barras . '<br>    
        Observaciones Rechazo: ' . $this->getObservaciones() . '<br>    
        <br>
        </div>
        </body></html>';
        $cabeceras = "Content-type: text/html\r\n";
        //*****************************************************************************************
        if($usuario != null){
            try {
                $baseMail = new BaseMailSimad();
                $baseMail->SetSubject(sprintf('SGDEA : Transferencia Archivo Expediente # %s',$codigo_barras));
                $baseMail->SetMsgHTML($cuerpo);
                $baseMail->SetAddAddress($usuario->getEmail(), $usuario->getEmail());
                if ($baseMail->InitSend() === true) {
                    $baseMail->writetolog("Alerta enviada: " . $this->getTransferenciaId() . " Enviado a: " . $usuario->getEmail());
                } else {
                    $baseMail->writetolog("Error al enviar alerta: " . $this->getTransferenciaId() . " Cuenta correo: " . $usuario->getEmail());
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    public function transferirGestion($usuario_solicita = 0, $gestionar_anexos = true)
    {
        try {
            $unidaddocumental_id = $this->getUnidaddocumentalId();
            $total_folios = 1;
            //*************************************************************************************************
            if ($this->getDestinotransferenciaId() == 1) 
            {
                $cotenido_doc_old = new ContenidoUnidadDocumental();
                //*************************************************************************************************
                $contenido_documental = new ContenidoUnidadDocumental();
                $contenido_documental->setUnidaddocumentalId($unidaddocumental_id);
                $contenido_documental->setVerificacioncontunidaddocId(2);
                $contenido_documental->setEstadocontenidounidaddocId(1);
                $contenido_documental->setFechaCreacion(date('Y-m-d G:i:s'));
                $contenido_documental->setCreadoPorWeb(1);
                $contenido_documental->setMarca(0);
                $contenido_documental->setSoporteunidaddocumentalId(6); //SOPORTE DIGITAL
                $contenido_documental->setOrigendocumentoId(1); //ORIGEN ELECTRONICO
                $szfile = 0;
                $exfile = "N/A";
                $modulo_name = null;
                $modulo_id = null;
                //*********************************************************************************************
                $mimetypes = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());	
                //*********************************************************************************************
                $list_docs = null;
                switch ($this->getOrigentransferenciaid())
                {
                    case 1: // INTERNA com interna
                        $url_registro = ParametroPeer::retrieveByPK(19)->getValortexto();
                        $com_interna = $this->getComInterna();
                        $com_interna->setMarcaVinculacion(1);
                        $total_folios = !empty($com_interna->getFolios()) ? trim($com_interna->getFolios()) : 1;
                        //*************************************************************************************
                        $list_docs = trim($com_interna->getRuta());
                        $modulo_name = 'ComInterna';
                        $modulo_id = ModulesEnable::ComInterna; // 2 com_interna
                        $consecutivo_id = $this->getCominternaId();
                        //*************************************************************************************
                        $targetpath = $com_interna->getPathImageDigitByCom();
                        $format = pathinfo($targetpath,PATHINFO_EXTENSION);
                        if(file_exists($targetpath)){
                            $szfile = filesize($targetpath);
                            $exfile = strtoupper($format);
                        }
                        //*************************************************************************************
                        if(!empty($fullpath_com) && file_exists($targetpath)){
                            $file_metadato = new FileMetaInfo();
                            $total_folios = $file_metadato->countPagesWithExifTool($fullpath_com);
                        }
                        //*************************************************************************************
                        $ruta = $url_registro . $this->getCominternaId();
                        $contenido_documental->setDescripcion($com_interna->getRadicado() . " - " . $com_interna->getReferencia());
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setFechaDocumento($com_interna->getFechaCreacion('Y-m-d'));
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                    break;
                    case 2: //ENTRANTE
                        $url_registro = ParametroPeer::retrieveByPK(20)->getValortexto();
                        $com_recibida = $this->getComRecibida();
                        $com_recibida->setMarcaVinculacion(1);
                        $total_folios = !empty($com_recibida->getFolios()) ? trim($com_recibida->getFolios()) : 1;
                        //*************************************************************************************
                        $list_docs = trim($com_recibida->getRuta());
                        $modulo_name = 'ComRecibida';
                        $modulo_id = ModulesEnable::ComRecibida; // 3 com_recibida
                        $consecutivo_id = $this->getComrecibidaId();
                        //*************************************************************************************
                        $targetpath = $com_recibida->getPathImageDigitByCom();
                        $format = pathinfo($targetpath,PATHINFO_EXTENSION);
                        if(file_exists($targetpath)){
                            $szfile = filesize($targetpath);
                            $exfile = strtoupper($format);
                        }
                        //*************************************************************************************
                        if(!empty($fullpath_com) && file_exists($targetpath)){
                            $file_metadato = new FileMetaInfo();
                            $total_folios = $file_metadato->countPagesWithExifTool($fullpath_com);
                        }
                        //*************************************************************************************
                        $ruta = $url_registro . $this->getComrecibidaId();
                        $contenido_documental->setDescripcion(trim($com_recibida->getRadicado()) . " - " . trim($com_recibida->getAsunto()));
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setFechaDocumento($com_recibida->getFechaCreacion('Y-m-d'));
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                    break;
                    case 3: // SALIENTE
                        $url_registro = ParametroPeer::retrieveByPK(21)->getValortexto();
                        $com_enviada = $this->getComEnviada();
                        $com_enviada->setMarcaVinculacion(1);
                        $total_folios = !empty($com_enviada->getFolios()) ? trim($com_enviada->getFolios()) : 1;
                        //*************************************************************************************
                        $list_docs = trim($com_enviada->getRuta());
                        $modulo_name = 'ComEnviada';
                        $modulo_id = ModulesEnable::ComEnviada; // 4 com_enviada
                        $consecutivo_id = $this->getComenviadaId();
                        //*************************************************************************************
                        $targetpath = $com_enviada->getPathImageDigitByCom();
                        $format = pathinfo($targetpath,PATHINFO_EXTENSION);
                        if(file_exists($targetpath)){
                            $szfile = filesize($targetpath);
                            $exfile = strtoupper($format);
                        }
                        //*************************************************************************************
                        if(!empty($fullpath_com) && file_exists($targetpath)){
                            $file_metadato = new FileMetaInfo();
                            $total_folios = $file_metadato->countPagesWithExifTool($fullpath_com);
                        }
                        //*************************************************************************************
                        $ruta = $url_registro . $this->getComenviadaId();
                        $contenido_documental->setTipodocumentalId($this->getTipodocumentalId());
                        $contenido_documental->setDescripcion($com_enviada->getRadicado() . " - " . $com_enviada->getAsunto());
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setFechaDocumento($com_enviada->getFechaCreacion('Y-m-d'));
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                    break;
                    case 7: //DOCUMENTACION TECNICA
                        $url_registro = ParametroPeer::retrieveByPK(44)->getValortexto();
                        $ruta = $url_registro . $this->getDocumentacionId();
                        $documentacion = DocumentacionPeer::retrieveByPK($this->getDocumentacionId());
                        $contenido_documental->setDescripcion($documentacion->getTitulo() . " - " . $documentacion->getCodigoBarras());
                        $contenido_documental->setFechaDocumento($documentacion->getFechaCreacion('Y-m-d'));
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                    break;
                    case 8: //ACTOS ADMINISTRATIVOS
                        $url_registro = ParametroPeer::retrieveByPK(78)->getValortexto();
                        $ruta = $url_registro . $this->getActoadministrativoId();
                        //*************************************************************************************
                        $acto_administrativo = ActoAdministrativoPeer::retrieveByPK($this->getActoadministrativoId());
                        $acto_administrativo->setMarcaVinculacion(1);
                        //*************************************************************************************
						$total_folios = !empty($acto_administrativo->getFolios()) ? trim($acto_administrativo->getFolios()) : 1;
                        $fecha_documento = trim($acto_administrativo->getFechaResolucion()) ? date("Y-m-d",strtotime($acto_administrativo->getFechaResolucion())) : $acto_administrativo->getFechaCreacion('Y-m-d');
						//*************************************************************************************
                        $digit_document = $acto_administrativo->getPathImageDigitByCom();
                        if(!empty($digit_document)){
                            $file_infor = pathinfo($digit_document);
                            $szfile = filesize($digit_document);
                            $exfile = strtoupper($file_infor['extension']);
                        }
                        //*************************************************************************************
                        if(!empty($fullpath_com) && file_exists($digit_document)){
                            $file_metadato = new FileMetaInfo();
                            $total_folios = $file_metadato->countPagesWithExifTool($fullpath_com);
                        }
                        //*************************************************************************************
                        $contenido_documental->setDescripcion($acto_administrativo->getAsunto() . " - " . $acto_administrativo->getRadicadoCompuesto());
                        $contenido_documental->setFechaDocumento($fecha_documento);
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                        //*************************************************************************************
                        $list_docs = trim($acto_administrativo->getRuta());
                        $modulo_name = 'ActoAdministrativo';
                        $modulo_id = ModulesEnable::ActosAdministrativos;
                        $consecutivo_id = $this->getActoadministrativoId();
                    break;
                    case 9: //FACTURAS
                        $url_registro = ParametroPeer::retrieveByPK(71)->getValortexto();
                        $ruta = $url_registro . $this->getFacturaId();
                        $factura = FacturaPeer::retrieveByPK($this->getFacturaId());
                        $contenido_documental->setDescripcion($factura->getAsunto() . " - " . $factura->getRadicado());
                        $contenido_documental->setFechaDocumento($factura->getFechaRadicado('Y-m-d'));
                        $contenido_documental->setFolios($total_folios);
                        $contenido_documental->setVinculoRegistro(1);
                        $contenido_documental->setRuta($ruta);
                        //*************************************************************************************
                        $list_docs = trim($factura->getRuta());
                        $modulo_name = 'Facturas';
                        $modulo_id = ModulesEnable::Facturas;
                        $consecutivo_id = $this->getFacturaId();
                    break;
                }
                //****************************************************************************************
                $total_folios = !empty($total_folios) ? (int)trim($total_folios) : 1;
                $folio_inicial = ContenidoUnidadDocumentalPeer::getMaxFolioFinalByExpDoc($unidaddocumental_id);
                $folio_final = $folio_inicial + ($total_folios - 1);
                //****************************************************************************************
                $contenido_documental->setFolioInicial($folio_inicial);
                $contenido_documental->setFolioFinal($folio_final);
                $contenido_documental->setFuncEncryp("SHA1");
                $contenido_documental->setTipodocumentalId($this->getTipodocumentalId());
                $contenido_documental->setUsuarioId($usuario_solicita);
                $contenido_documental->setSizeFile($szfile);
                $contenido_documental->setFormatFile($exfile);
                $contenido_documental->setModuloId($modulo_id);
                $contenido_documental->setConsecutivoId($consecutivo_id);
                $contenido_documental->save();
                //****************************************************************************************
                $orden_documento = ContenidoUnidadDocumentalPeer::getOrderByDocInExp($unidaddocumental_id);
                $contenido_documental->setOrdenContenido($orden_documento);
                //****************************************************************************************
                $contenido_documental->setValorHuella($contenido_documental->generateValorHuella());
                //****************************************************************************************
                $this->setEstadotransferenciaId(2);      
                $this->setFechaAceptacion(date('Y-m-d G:i:s'));
                $this->save();
                //****************************************************************************************
                if($this->getOrigentransferenciaid() == 1){
                    $com_interna->setMarcaVinculacion(1);
                    $com_interna->setContenidodocId($contenido_documental->getPrimaryKey());
                    $com_interna->save();
                }elseif($this->getOrigentransferenciaid() == 2){
                    $com_recibida->setMarcaVinculacion(1);
                    $com_recibida->setContenidodocId($contenido_documental->getPrimaryKey());
                    $com_recibida->save();
                }elseif($this->getOrigentransferenciaid() == 3){
                    $com_enviada->setMarcaVinculacion(1);
                    $com_enviada->setContenidodocId($contenido_documental->getPrimaryKey());
                    $com_enviada->save();
                }elseif($this->getOrigentransferenciaid() == 8){
                    $acto_administrativo->setMarcaVinculacion(1);
                    $acto_administrativo->setContenidodocId($contenido_documental->getPrimaryKey());
                    $acto_administrativo->save();
                }
                //****************************************************************************************
                AuditLogPeer::guardarAuditoriaLite(
                    ContenidoUnidadDocumentalPeer::getOMClass(),
                    $cotenido_doc_old,
                    $contenido_documental,
                    ModulesEnable::Archivo,
                    $contenido_documental->getUnidadDocumental()->getCodigoBarras(),
                    $contenido_documental->getUsuarioId()
                );
                //****************************************************************************************
                if($gestionar_anexos){
                    $this->gestionarAnexos($list_docs, $unidaddocumental_id, $contenido_documental, $modulo_name, $consecutivo_id, $modulo_id);
                }
                //****************************************************************************************
                try{
                    $folioManager = ExpedienteFolioManager::getInstance();
                    $tieneDesorden = $folioManager->tieneFoliosDesordenados($unidaddocumental_id);
                    if($tieneDesorden){
                        $resultado = ContenidoUnidadDocumentalPeer::reordenarFoliosPorFecha($unidaddocumental_id);
                    }
                } catch (PropelException $ex) {
                } catch (\Exception $ex) {
                } catch (\Throwable $ex) {
                }
                //****************************************************************************************
                $this->enviarAlertas();
                //****************************************************************************************
                return $contenido_documental->getPrimaryKey();
            }else{
                return -1;
            }
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function gestionarAnexos($list_docs, $unidaddocumental_id, $contenido_documental,$modulo_name,$consecutivo_id,$modulo_id)
    {
        try
        {
            if(!empty($list_docs))
            {
                $full_array = simad_paths_app::readConfigFileApp();
                $source_alias = $full_array['source_alias'];
                $source_paths = $full_array['source_paths'];
                //*********************************************************************************************
                $unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
                $subserie_id = $unidad_documental->getSubserieId();
                $tipoDocIdAnexo = TipodocumentalPeer::getTipoDocIdAnexo($subserie_id);
                //*********************************************************************************************
                $array_list_docs = preg_split("/[,]+/",$list_docs,-1, PREG_SPLIT_NO_EMPTY);
                $list_contenidodoc = array();
                //*********************************************************************************************
                foreach ($array_list_docs as $ruta_doc) 
                {
                    if (!empty(trim($ruta_doc)))
                    {
                        $furl = preg_replace('~//+~', '/', $ruta_doc);  
                        $p3url = str_replace($source_alias, '/', $furl);
                        //************************************************************************************
                        $path_absolute = null;
                        $path_relative = null;
                        $fname = null;
                        $fonlyname = null;
                        $fsize = null;
                        $fformat = null;
                        $typefile = null;
                        //************************************************************************************
                        foreach ($source_paths as $fpath)  
                        {
                            if(file_exists($fpath . DIRECTORY_SEPARATOR . $p3url))
                            {
                                $path_absolute = $fpath;
                                $path_relative = pathinfo($p3url, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
                                $fname = basename($p3url);
                                $fonlyname = pathinfo($p3url, PATHINFO_FILENAME);
                                $fsize = filesize($fpath . DIRECTORY_SEPARATOR . $p3url);
                                $fformat = strtoupper(pathinfo($p3url, PATHINFO_EXTENSION));
                                $typefile = pathinfo($p3url, PATHINFO_EXTENSION);
                                break;
                            }
                        }
                        //************************************************************************************
                        if(empty(trim($path_absolute)) || empty(trim($fsize))){
                            continue;
                        }
                        //************************************************************************************
                        // obteniendo el radicado
                        $peername = $modulo_name . 'Peer';
                        $object = $peername::retrieveByPk($consecutivo_id);
                        $radicado = $object->getRadicado();
                        //************************************************************************************
                        $params = [];
                        $params['archivo_nombre'] = $fname;
                        $params['unidad_documental_pk'] = $contenido_documental->getUnidaddocumentalId();                            
                        $params['verificacion_doc'] = $contenido_documental->getVerificacioncontunidaddocId();
                        $params['tipo_doc_id'] = $tipoDocIdAnexo;
                        $params['estado_doc'] = $contenido_documental->getEstadocontenidounidaddocId(); 
                        $params['usuario_id'] = $contenido_documental->getUsuarioId();   
                        $params['descripcion'] = sprintf('%s - %s %s', $radicado, 'Anexo ', $fonlyname);
                        //************************************************************************************
                        $total_folios = 1;
                        //************************************************************************************
                        if ($typefile == 'pdf' || $typefile == 'doc' || $typefile == 'docx') 
                        {
                            $total_folios = 1;
                        } 
                        //************************************************************************************
                        $params['folios'] = $total_folios;
                        $params['fecha_documento'] = $contenido_documental->getFechaDocumento();
                        $params['tipofirmadigital_id'] = $contenido_documental->getTipofirmadigitalId();  // getTipoFirmaDigitalId
                        $params['soporte_documental'] = $contenido_documental->getSoporteunidaddocumentalId(); //6;
                        $params['estado_doc'] =  $contenido_documental->getEstadocontenidounidaddocId();
                        $params['origen_documento'] =  $contenido_documental->getOrigendocumentoId();
                        $params['format_file'] = $fformat;
                        $params['size_file'] = $fsize;
                        $params['path_absolute'] = $path_absolute;
                        $params['path_relative'] = $path_relative;
                        $params['modulo_id'] = $modulo_id;
                        $params['consecutivo_id'] = $consecutivo_id;
                        //************************************************************************************
                        $list_contenidodoc[] = ContenidoUnidadDocumentalPeer::addContenidoUnidadDocumental($params);
                    }
                }
                //********************************************************************************************
                return $list_contenidodoc;
            }else{
                return null;
            }
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
