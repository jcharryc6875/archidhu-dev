<?php
/**
 * @javier.charry@gmail.com
 * @copyright 2024
 */

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
/**
 * WsApiAndes
 *
 * Orquestador principal de la integración SGDEA ↔ RMail.
 * Coordina autenticación, subida de adjuntos, envío y persistencia en SQL Server.
 *
 * Uso desde la acción Symfony:
 *   $service = new WsApiAndes();
 *   $resultado = $service->enviarCorreoCertificado($radicadoId, $destinatario, ...);
 */
class WsApiAndes
{
    const SERVICE_URL = "https://test.correocertificado4-72.com.co/webService.php?WSDL";
    const SERVICE_URI = "https://test.correocertificado4-72.com.co/";
    const SERVICE_USER = "unidaddevictimas@unidadvictimas.gov.co";
    const SERVICE_PASSW = "d7c678ad8d7b8f0c2955004047714e724856aeff";
    const SERVICE_APPUID = 0;
    const CONTEXT_SOAP2 = array('http' => array( 'user_agent' => 'PHPSoapClient'),'ssl' => array('verify_peer' => false,'verify_peer_name' => false, 'allow_self_signed' => true));

    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;

    public function WsApiAndes()
    {
        $this->logfile = sfConfig::get("sf_log_dir")."\WsCertiMailApi.log";
        $this->nulog = 0;
        $this->nudebug = 0;
    }

    /**
    * WsApiAndes::addCertiMailEnviada()
    * funcion para consumir servicio web de andes para enviar un correo certificado
    * @return array()
    */
    public function addCertiMailEnviada($params = array())
    {
        //***********************************************************************************
        $options = array(
            "uri"=> WsApiAndes::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_BOTH,
            "timeout" => 300,
            "connection_timeout" => 300,
            "trace" => true,
            "encoding" => "UTF-8",
            /*"encoding"=>"ISO-8859-1",*/
            "exceptions" => true,
            'keep_alive' => false,
            'stream_context' => stream_context_create(WsApiAndes::CONTEXT_SOAP2),
        );
        //************************************************************************************
        $client = new SoapClientExtendedMail(WsApiAndes::SERVICE_URL,$options);
        //$functions = $client->__getFunctions();
        $client->__setSoapHeaders($this->soapClientWSSecurityHeader(WsApiAndes::SERVICE_USER,WsApiAndes::SERVICE_PASSW));
        //************************************************************************************
        $tpl_email = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tplBodyCertimail.txt';
        if(!file_exists($tpl_email)){
            return array('status' => 400,'message'=>'Error leyendo la plantilla del correo de certificación', 'idMensaje' => null);
        }
        $bodyEmail = file_get_contents($tpl_email);
        //************************************************************************************
        $patrones = array();
        $patrones[0] = '{$#ENTIDAD_NOMBRER$#}';
        $patrones[1] = '{$#ENTIDAD_FUNCIONARIO$#}';

        $sustituciones = array();
        $sustituciones[0] = $params['ENTIDAD_DESTINO'];
        $sustituciones[1] = $params['FUNCIONARIO_DESTINO'];
        //************************************************************************************
        $econtents = str_replace($patrones, $sustituciones, $bodyEmail);
        $activeAlertas = false;
        //************************************************************************************
        try {
            $infoComplex = array('idUsuario' => WsApiAndes::SERVICE_USER,
                'Asunto' => $params['ASUNTO_COM'],'Texto' => $econtents,
                'NombreDestinatario' => sprintf("%s %s",$params['ENTIDAD_DESTINO'],$params['FUNCIONARIO_DESTINO']),
                'CorreoDestinatario' => $params['EMAIL_DESTINO'],
                'Adjunto' => $params['ARCHIVO_B64'],'NombreArchivo' => $params['FILENAME'],'Alertas' => $activeAlertas,
            );
            //********************************************************************************
            $response = $client->RegistrarMensaje($infoComplex);
            //********************************************************************************
            $aresp = new stdClass();
            $aresp = (object)$response;
            //********************************************************************************
            $msgpost = strpos($aresp->hash, "Observacion");
            $response_idmessage = explode("=", substr($aresp->hash,0,($msgpost-1)));
            $response_obsvacion = explode("=", substr($aresp->hash,$msgpost,strlen($aresp->hash)));
            //********************************************************************************
            if($response_idmessage[0] == 'idMensaje' && $response_idmessage[1] > 0){
                return array('status' => 200,'message' => $aresp->hash, 'idMensaje' => $response_idmessage[1]);
            }elseif($response_obsvacion[0] == 'Observacion'){
                return array('status' => 400,'message' => $response_obsvacion[1], 'idMensaje' => null);
            }else{
                return array('status' => 400,'message' => $aresp->hash, 'idMensaje' => null);
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            return array('status' => 400,'message'=>'SOAP Error, Ocurrio un error al enviar los datos, error de integración con la herramienta externa', 'idMensaje' => null);
        }catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('status' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental', 'idMensaje' => null);
        }
    }

    /**
    * WsApiAndes::searchRegCertiMail()
    * Permite consultar una lista de registros pendientes por descargar las pruebas de entrega
    * @return array()
    */
    public function searchRegCertiMail()
    {
        try {
            // invocar app.ini
            $read_sections = array('archivar_acta_com_enviada');
            $ini_array = simad_util::readConfigFileApp($read_sections);
            $paramArchivarDoc = isset($ini_array['archivar_acta_com_enviada']) ? $ini_array['archivar_acta_com_enviada'] : 'servicio';
            //******************************************************************************************
            $ilsit_objects = ServicioCertimailPeer::getListObjectsByProcess(ServicioAppExterna::CertiMail);
            //******************************************************************************************
            foreach ($ilsit_objects as $object_info)            
            {
                $servicio_crtmail = ServicioCertimailPeer::retrieveByPK($object_info['SERVICIOCERTIMAIL_ID']);
                $response = $this->getActaEntregaCertiMail($object_info['SERVICIO_ID'],$object_info['CERTIMAILMSG_ID'],$paramArchivarDoc);
                //**************************************************************************************
                try {
                    if($response['status'] == 200){
                        $servicio_crtmail->setEstaAbierta(0);
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->setEstadoEjecucion("DescargaCertificadoExitoso");
                        $servicio_crtmail->save();
                    }else{
                        $servicio_crtmail->setEstaAbierta(1);
                        $servicio_crtmail->setEstadoEjecucion("ErrorDescargaCertificado");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                    }
                } catch (PropelException $th) {
                    //throw $th;
                } catch (\Exception $th) {
                    //throw $th;
                } catch (\Throwable $th) {
                    //throw $th;
                }
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
    * WsApiAndes::addCertiMailEnviada()
    * funcion para consumir servicio web de andes para enviar un correo certificado
    * @return array()
    */
    public function getActaEntregaCertiMail($servicio_id, $idMensageCrtMail, $paramArchivarDoc = "servicio")
    {
        $options = array(
            "uri"=> WsApiAndes::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_BOTH,
            "timeout" => 300,
            "connection_timeout" => 300,
            "trace" => true,
            "encoding" => "UTF-8",
            /*"encoding"=>"ISO-8859-1",*/
            "exceptions" => true,
            'keep_alive' => false,
            'stream_context' => stream_context_create(WsApiAndes::CONTEXT_SOAP2),
        );
        //****************************************************************************************
        $client = new SoapClientExtendedMail(WsApiAndes::SERVICE_URL,$options);
        $client->__setSoapHeaders($this->soapClientWSSecurityHeader(WsApiAndes::SERVICE_USER,WsApiAndes::SERVICE_PASSW));
        //****************************************************************************************
        try {
            $infoComplex = array('idUsuario' => WsApiAndes::SERVICE_USER,
                'idMensaje' => $idMensageCrtMail,'generarPDF' => true
            );
            //************************************************************************************
            $response = $client->ObtenerToken($infoComplex);
            //************************************************************************************
            $aresp = new stdClass();
            $aresp = (object)$response;
            //************************************************************************************
            $msgpost = strpos($aresp->hash, "Observacion");
            $msgPdfToken = strpos($aresp->hash, "Token");
            //************************************************************************************
            $response_idmessage = explode("=", substr($aresp->hash,0,($msgpost)));
            $response_obsvacion = explode("=", substr($aresp->hash,$msgpost,($msgPdfToken-($msgpost+1))));
            $fbase64 = substr($aresp->hash,($msgPdfToken+6),strlen($aresp->hash));            
            //************************************************************************************
            if($response_idmessage[0] == 'idMensaje' && in_array(trim($response_idmessage[1]),array(2,3,4,7,8))){
                $servicio = ServicioPeer::retrieveByPK($servicio_id);
                $servicioestado_id = $servicio->getServicioestadoId();
                //********************************************************************************
                if(!empty($fbase64)){
                    $tmp_path = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(time().$servicio->getPrimaryKey());
                    $filename = sprintf("%s_%s_%s_ActaEnvio_y_EntregaCorreoElectronico.pdf",uniqid(),$servicio->getPrimaryKey(),$idMensageCrtMail);
                    $file_target = $tmp_path.DIRECTORY_SEPARATOR.$filename;
                    simad_util::createPath($tmp_path);
                    $isCreateFile = simad_util::getConvertB64ToFile($fbase64,$file_target);
                    //****************************************************************************
                    if($isCreateFile && file_exists($file_target)){
                        $isNotifiedTask = $paramArchivarDoc == "servicio" ? $servicio->attachNewDocumetBitacora($file_target) : true;
                        //************************************************************************
                        if($isNotifiedTask){
                            $desc_documento = "Acta de Envío y Entrega de Correo Electrónico";
                            //********************************************************************
                            if($paramArchivarDoc == "expediente"){
                                $contenido_doc = $servicio->archivarActaNotificacion($file_target,$desc_documento);
                                if($contenido_doc != null){
                                    $desc_documento = sprintf("%s / Documento indexado en el expediente ExpedienteId %s",$desc_documento, $contenido_doc->getUnidaddocumentalId());
                                }else{
                                    $desc_documento = sprintf("%s / Error al indexar el documento en el expediente",$desc_documento);
                                }
                                //*****************************************************************
                                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                            }else if($paramArchivarDoc == "radicado"){
                                $desc_documento = sprintf("%s / Indexado en el expediente",$desc_documento);
                                $radicado_com = $servicio->addActaNotificacionToCom($file_target);
                                if(!empty($radicado_com)){
                                    $desc_documento = sprintf("%s / Documento indexado en el radicado %s",$desc_documento, $radicado_com);
                                }else{
                                    $desc_documento = sprintf("%s / Error al indexar el documento en el radicado",$desc_documento);
                                }
                                //*****************************************************************
                                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                            }
                            //********************************************************************
                            unlink($file_target);
                            //********************************************************************
                            return array('status' => 200,'message' => 'El archivo fue almacenado correctamente en el Sgdea', 'idMensaje' => $idMensageCrtMail);
                        }else{
                            return array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $idMensageCrtMail);
                        }
                    }else{
                        return array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $idMensageCrtMail);
                    }
                }else{
                    return array('status' => 400,'message' => 'El archivo no existe', 'idMensaje' => $idMensageCrtMail);
                }
            }elseif($response_obsvacion[0] == 'Observacion'){
                return array('status' => 400,'message' => $response_obsvacion[1], 'idMensaje' => $idMensageCrtMail);
            }else{
                return array('status' => 400,'message' => $aresp->hash, 'idMensaje' => $idMensageCrtMail);
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            return array('status' => 400,'message'=>'SOAP Error, Ocurrio un error al enviar los datos, error de integración con la herramienta externa', 'idMensaje' => $idMensageCrtMail);
        }catch (\Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('status' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental', 'idMensaje' => $idMensageCrtMail);
        }catch (\Throwable $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('status' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental', 'idMensaje' => $idMensageCrtMail);
        }
    }

    /**
    * WsApiAndes::searchRegCorreoCertFisico()
    * Permite consultar una lista de registros pendientes por descargar las pruebas de entrega
    * del servicio de correo nacional certificado con sipost
    * @return array()
    */
    public function searchRegCorreoCertFisico()
    {
        try {
            // invocar app.ini
            $read_sections = array('archivar_acta_com_enviada');
            $ini_array = simad_util::readConfigFileApp($read_sections);
            $paramArchivarDoc = isset($ini_array['archivar_acta_com_enviada']) ? $ini_array['archivar_acta_com_enviada'] : 'servicio';
            //******************************************************************************************
            $ilsit_objects = ServicioCertimailPeer::getListObjectsByProcess(ServicioAppExterna::CorreoFisicoNacional);
            //******************************************************************************************
            foreach ($ilsit_objects as $object_info)            
            {
                $servicio_crtmail = ServicioCertimailPeer::retrieveByPK($object_info['SERVICIOCERTIMAIL_ID']);
                $response = $this->getActaEntregaCertiFisicoNal($object_info['SERVICIO_ID'],$object_info['CERTIMAILMSG_ID'],$paramArchivarDoc);
                //**************************************************************************************
                try {
                    if($response['status'] == 200){
                        $servicio_crtmail->setEstaAbierta(0);
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->setEstadoEjecucion("DescargaCertificadoExitoso");
                        $servicio_crtmail->save();
                    }else{
                        $servicio_crtmail->setEstaAbierta(1);
                        $servicio_crtmail->setEstadoEjecucion("ErrorDescargaCertificado");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                    }
                } catch (PropelException $th) {
                    //throw $th;
                } catch (\Exception $th) {
                    //throw $th;
                } catch (\Throwable $th) {
                    //throw $th;
                }
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
    * WsApiAndes::getActaEntregaCertiFisicoNal()
    * funcion para consumir servicio web para descargar actas de entrega del correo fisico nacional
    * @return array()
    */
    public function getActaEntregaCertiFisicoNal($servicio_id, $idMensageCrtMail, $paramArchivarDoc = "servicio")
    {
        $options = array(
            "uri"=> WsApiAndes::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_BOTH,
            "timeout" => 300,
            "connection_timeout" => 300,
            "trace" => true,
            "encoding" => "UTF-8",
            /*"encoding"=>"ISO-8859-1",*/
            "exceptions" => true,
            'keep_alive' => false,
            'stream_context' => stream_context_create(WsApiAndes::CONTEXT_SOAP2),
        );
        //****************************************************************************************
        $client = new SoapClientExtendedMail(WsApiAndes::SERVICE_URL,$options);
        $client->__setSoapHeaders($this->soapClientWSSecurityHeader(WsApiAndes::SERVICE_USER,WsApiAndes::SERVICE_PASSW));
        //****************************************************************************************
        try {
            $infoComplex = array('idUsuario' => WsApiAndes::SERVICE_USER,
                'idMensaje' => $idMensageCrtMail,'generarPDF' => true
            );
            //************************************************************************************
            $response = $client->ObtenerToken($infoComplex);
            //************************************************************************************
            $aresp = new stdClass();
            $aresp = (object)$response;
            //************************************************************************************
            $msgpost = strpos($aresp->hash, "Observacion");
            $msgPdfToken = strpos($aresp->hash, "Token");
            //************************************************************************************
            $response_idmessage = explode("=", substr($aresp->hash,0,($msgpost)));
            $response_obsvacion = explode("=", substr($aresp->hash,$msgpost,($msgPdfToken-($msgpost+1))));
            $fbase64 = substr($aresp->hash,($msgPdfToken+6),strlen($aresp->hash));            
            //************************************************************************************
            if($response_idmessage[0] == 'idMensaje' && in_array(trim($response_idmessage[1]),array(2,3,4,7,8))){
                $servicio = ServicioPeer::retrieveByPK($servicio_id);
                $servicioestado_id = $servicio->getServicioestadoId();
                //********************************************************************************
                if(!empty($fbase64)){
                    $tmp_path = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(time().$servicio->getPrimaryKey());
                    $filename = sprintf("%s_%s_%s_ActaEnvio_y_EntregaCertificadoFisico.pdf",uniqid(),$servicio->getPrimaryKey(),$idMensageCrtMail);
                    $file_target = $tmp_path.DIRECTORY_SEPARATOR.$filename;
                    simad_util::createPath($tmp_path);
                    $isCreateFile = simad_util::getConvertB64ToFile($fbase64,$file_target);
                    //****************************************************************************
                    if($isCreateFile && file_exists($file_target)){
                        $isNotifiedTask = $paramArchivarDoc == "servicio" ? $servicio->attachNewDocumetBitacora($file_target) : true;
                        //************************************************************************
                        if($isNotifiedTask){
                            $desc_documento = "Prueba de entrega de correo fisico";
                            //********************************************************************
                            if($paramArchivarDoc == "expediente"){
                                $contenido_doc = $servicio->archivarActaNotificacion($file_target,$desc_documento);
                                if($contenido_doc != null){
                                    $desc_documento = sprintf("%s / Documento indexado en el expediente ExpedienteId %s",$desc_documento, $contenido_doc->getUnidaddocumentalId());
                                }else{
                                    $desc_documento = sprintf("%s / Error al indexar el documento en el expediente",$desc_documento);
                                }
                                //*****************************************************************
                                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                            }else if($paramArchivarDoc == "radicado"){
                                $desc_documento = sprintf("%s / Indexado en el expediente",$desc_documento);
                                $radicado_com = $servicio->addActaNotificacionToCom($file_target);
                                if(!empty($radicado_com)){
                                    $desc_documento = sprintf("%s / Documento indexado en el radicado %s",$desc_documento, $radicado_com);
                                }else{
                                    $desc_documento = sprintf("%s / Error al indexar el documento en el radicado",$desc_documento);
                                }
                                //*****************************************************************
                                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                            }
                            //********************************************************************
                            unlink($file_target);
                            //********************************************************************
                            return array('status' => 200,'message' => 'El archivo fue almacenado correctamente en el Sgdea', 'idMensaje' => $idMensageCrtMail);
                        }else{
                            return array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $idMensageCrtMail);
                        }
                    }else{
                        return array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $idMensageCrtMail);
                    }
                }else{
                    return array('status' => 400,'message' => 'El archivo no existe', 'idMensaje' => $idMensageCrtMail);
                }
            }elseif($response_obsvacion[0] == 'Observacion'){
                return array('status' => 400,'message' => $response_obsvacion[1], 'idMensaje' => $idMensageCrtMail);
            }else{
                return array('status' => 400,'message' => $aresp->hash, 'idMensaje' => $idMensageCrtMail);
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            return array('status' => 400,'message'=>'SOAP Error, Ocurrio un error al enviar los datos, error de integración con la herramienta externa', 'idMensaje' => $idMensageCrtMail);
        }catch (\Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('status' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental', 'idMensaje' => $idMensageCrtMail);
        }catch (\Throwable $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('status' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental', 'idMensaje' => $idMensageCrtMail);
        }
    }

    /**
    * This function implements a WS-Security digest authentification for PHP.
    *
    * @access private
    * @param string $user
    * @param string $password
    * @return SoapHeader
    */
    public function soapClientWSSecurityHeader($user, $password)
    {
        // Creating date using yyyy-mm-ddThh:mm:ssZ format
        $tm_created = gmdate('Y-m-d\TH:i:s\Z');
        $tm_expires = gmdate('Y-m-d\TH:i:s\Z', gmdate('U') + 180); //only necessary if using the timestamp element

        // Generating and encoding a random number
        $simple_nonce = mt_rand();
        $encoded_nonce = base64_encode($simple_nonce);
        $pw_encrypt = $password;

        // Compiling WSS string
        $passdigest = base64_encode(sha1($simple_nonce . $tm_created .$pw_encrypt , true));

        // Initializing namespaces
        $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
        $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
        $encoding_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

        // Creating WSS identification header using SimpleXML
        $root = new SimpleXMLElement('<root/>');

        $security = $root->addChild('wsse:Security', null, $ns_wsse);

        //the timestamp element is not required by all servers
        $timestamp = $security->addChild('wsu:Timestamp', null, $ns_wsu);
        $timestamp->addAttribute('wsu:Id', 'Timestamp-28');
        $timestamp->addChild('wsu:Created', $tm_created, $ns_wsu);
        $timestamp->addChild('wsu:Expires', $tm_expires, $ns_wsu);

        $usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
        $usernameToken->addChild('wsse:Username', $user, $ns_wsse);
        $usernameToken->addChild('wsse:Password',$passdigest, $ns_wsse)->addAttribute('Type', $password_type);
        $usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse)->addAttribute('EncodingType', $encoding_type);
        $usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);

        // Recovering XML value from that object
        $root->registerXPathNamespace('wsse', $ns_wsse);
        $full = $root->xpath('/root/wsse:Security');
        $auth = $full[0]->asXML();

        return new SoapHeader($ns_wsse, 'Security', new SoapVar($auth, XSD_ANYXML), true);
    }
}

/**
 * WsApiRMail
 *
 * Orquestador principal de la integración SGDEA ↔ RMail.
 * Coordina autenticación, subida de adjuntos, envío y persistencia en SQL Server.
 *
 * Uso desde la acción Symfony:
 *   $service = new WsApiRMail($config);
 *   $resultado = $service->enviarCorreoCertificado($radicadoId, $destinatario, ...);
 */
class WsApiRMail
{
    private RMailAuthClient   $authClient;
    private RMailUploadClient $uploadClient;
    private RMailMailClient   $mailClient;
    private RMailReportClient $reportClient;
    private array             $config;

    public function __construct()
    {
        $config = $this->buildConfig();

        if (!isset($config['rmail_enable_service']) && empty($config['rmail_enable_service'])) {
            throw new CorreoCertificadoException('El servicio de RMail no está habilitado.');
        }

        if ($config['rmail_base_url'] == '') {
            throw new CorreoCertificadoException('La URL base de RMail no está configurada.');
        }

        if ($config['rmail_username'] == '') {
            throw new CorreoCertificadoException('El usuario de RMail no está configurado.');
        }

        if ($config['rmail_password'] == '') {
            throw new CorreoCertificadoException('La contraseña de RMail no está configurada.');
        }

        if ($config['rmail_client_id'] == '') {
            //throw new CorreoCertificadoException('El ID del cliente de RMail no está configurado.');
        }

        if ($config['rmail_app_id'] == '') {
            //throw new CorreoCertificadoException('El ID de la aplicación de RMail no está configurado.');
        }

        $this->config = $config;

        $this->authClient = new RMailAuthClient(
            $config['rmail_base_url'],
            $config['rmail_username'],
            $config['rmail_password'],
            $config['rmail_client_id'],
            300
        );

        $this->uploadClient = new RMailUploadClient(
            $config['rmail_base_url'],
            $this->authClient
        );

        $this->mailClient = new RMailMailClient(
            $config['rmail_base_url'],
            $this->authClient,
            $config['rmail_app_id'] ?? ''
        );

        $this->reportClient = new RMailReportClient(
            $config['rmail_base_url'],
            $this->authClient
        );
    }

    private function buildConfig(): array
    {
        $ini = simad_util::readConfigFileApp([
            'rmail_enable_service',
            'rmail_base_url',
            'rmail_app_id',
            'rmail_username',
            'rmail_password',
            'rmail_client_id',
            'rmail_receipts_path',
            'rmail_from',
            'rmail_test_env',
        ]);

        return [
            'rmail_base_url'        => $ini['rmail_base_url']      ?? '',
            'rmail_username'        => $ini['rmail_username']       ?? '',
            'rmail_password'        => $ini['rmail_password']       ?? '',
            'rmail_client_id'       => $ini['rmail_client_id']      ?? '0',
            'rmail_app_id'          => $ini['rmail_app_id']         ?? '0',
            'rmail_from'            => $ini['rmail_from']           ?? $ini['rmail_username'] ?? '',
            'rmail_receipts_path'   => $ini['rmail_receipts_path']  ?? '',
            'rmail_test_env'        => (bool)($ini['rmail_test_env'] ?? true),
            'rmail_enable_service'  => (bool)($ini['rmail_enable_service'] ?? false),
        ];
    }

    /**
     * Envía un correo certificado y registra el resultado en la BD.
     *
     * @param int      $radicadoId    ID del radicado en COM_RECIBIDA
     * @param string   $destinatario  Correo del destinatario
     * @param string   $asunto        Asunto del correo
     * @param string   $cuerpoHtml    Cuerpo HTML del mensaje
     * @param string[] $adjuntos      Rutas absolutas de archivos adjuntos (opcional)
     * @return RMailEnvioResultado
     * @throws CorreoCertificadoException
     */
    public function enviarCorreoCertificado(
        int    $radicadoId,
        string $destinatario,
        string $asunto,
        string $cuerpoHtml,
        array  $adjuntos = []
    ): RMailEnvioResultado {

        // El CustomerTrackingId es el número de radicado — clave para el polling
        $customerTrackingId = (string)$radicadoId;

        try {
            // 1a. Subir adjuntos si los hay
            $attachmentIds = [];
            if (!empty($adjuntos)) {
                $attachmentIds = $this->uploadClient->uploadFiles($adjuntos);
            }

            // 1b. Construir el mensaje
            $message                     = new RMailMessage();
            $message->from               = $this->config['rmail_from'];
            $message->to                 = [$destinatario];
            $message->subject            = ($this->config['rmail_test_env'] == true ? '[Pruebas 472] ' : '') . $asunto;
            $message->body               = $cuerpoHtml;
            $message->attachments        = $attachmentIds;
            $message->customerTrackingId = $customerTrackingId;
            $message->rpostType          = 1; // Rastrear y Probar → genera Recibo Registrado

            // 1c. Enviar
            $sendResult = $this->mailClient->send($message);

            return new RMailEnvioResultado(
                true,
                200,
                $radicadoId,
                $sendResult->messageId,
                $sendResult->trackingId,
                $customerTrackingId,
                'Correo certificado enviado correctamente.'
            );
        } catch (RMailAuthException $e) {
            throw new CorreoCertificadoException(
                "Error de autenticación con RMail: " . $e->getMessage(),
                400,
                $e
            );
        } catch (RMailUploadException $e) {
            throw new CorreoCertificadoException(
                "Error al subir adjuntos a RMail: " . $e->getMessage(),
                400,
                $e
            );
        } catch (RMailMailException $e) {
            throw new CorreoCertificadoException(
                "Error al enviar correo certificado: " . $e->getMessage(),
                400,
                $e
            );
        }
    }

    /**
     * WsApiRMail::searchRegCertiMail()
     * Permite consultar una lista de registros pendientes por descargar las pruebas de entrega
     * @return bool
     */
    public function searchRegCertiMail()
    {
        try {
            if (!isset($this->config['rmail_enable_service']) || empty($this->config['rmail_enable_service'])) {
                return false;
            }
            //*****************************************************************************************
            $read_sections = array('archivar_acta_com_enviada');
            $ini_array = simad_util::readConfigFileApp($read_sections);
            $paramArchivarDoc = isset($ini_array['archivar_acta_com_enviada']) ? $ini_array['archivar_acta_com_enviada'] : 'servicio';
            //******************************************************************************************
            $ilsit_objects = ServicioCertimailPeer::getListObjectsByProcess(ServicioAppExterna::CertiMail);
            //******************************************************************************************
            foreach ($ilsit_objects as $object_info) {
                $servicio_crtmail = ServicioCertimailPeer::retrieveByPK($object_info['SERVICIOCERTIMAIL_ID']);
                $response = $this->getActaEntregaCertiMail($object_info['SERVICIO_ID'], $object_info['CERTIMAILMSG_ID'], $paramArchivarDoc);
                //**************************************************************************************
                try {
                    if ($response['status'] == 200) {
                        $servicio_crtmail->setEstaAbierta(0);
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->setEstadoEjecucion("DescargaCertificadoExitoso");
                        $servicio_crtmail->save();
                    } else {
                        $servicio_crtmail->setEstaAbierta(1);
                        $servicio_crtmail->setEstadoEjecucion("ErrorDescargaCertificado");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                    }
                } catch (PropelException $th) {
                    //throw $th;
                } catch (\Exception $th) {
                    //throw $th;
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            //******************************************************************************************
            return true;
        } catch (PropelException $th) {
            return false;
        } catch (\Exception $th) {
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function getActaEntregaCertiMail(int $radicadoId, string $trackingId, string $paramArchivarDoc = "servicio"): array
    {
        $destPath   = $this->buildReceiptPath($radicadoId, $trackingId);

        try {
            // Consultar estado de entrega en RMail
            $descargado = $this->reportClient->downloadReceipt(
                $trackingId,
                $destPath
            );
        } catch (RMailReportException $e) {
            return array('status' => 400, 'message' => 'Error al descargar el acta de entrega o el acta de entrega no se encuentra disponible para descargar');
        }

        if ($descargado) {
            // Asegurar extensión .zip en la ruta guardada
            if (strtolower(pathinfo($destPath, PATHINFO_EXTENSION)) !== 'zip') {
                $destPath = preg_replace('/\.[^.]+$/', '', $destPath) . '.zip';
            }

            return $this->actualizarTestigoDescargado($radicadoId, $trackingId, $destPath, $paramArchivarDoc);
        } else {
            return array('status' => 400, 'message' => 'Error al descargar el acta de entrega o el acta de entrega no se encuentra disponible para descargar');
        }
    }

    private function actualizarTestigoDescargado(int $radicadoId, string $trackingId, string $rutaActaEntrega, string $paramArchivarDoc): array
    {
        try {
            if (file_exists($rutaActaEntrega)) {
                $servicio = ServicioPeer::retrieveByPK($radicadoId);
                if (!$servicio) {
                    return [
                        'status'    => 400,
                        'message'   => 'No se encontró el servicio asociado al radicado',
                        'idMensaje' => $trackingId
                    ];
                }
                //********************************************************************************************
                $servicioestado_id = $servicio->getServicioestadoId();
                $isNotifiedTask = $paramArchivarDoc == "servicio" ? $servicio->attachNewDocumetBitacora($rutaActaEntrega) : true;
                //********************************************************************************************
                if ($isNotifiedTask) {
                    $desc_documento = "Acta de Envío y Entrega de Correo Electrónico";
                    //****************************************************************************************
                    if ($paramArchivarDoc == "expediente") {
                        $contenido_doc = $servicio->archivarActaNotificacion($rutaActaEntrega, $desc_documento);
                        if ($contenido_doc != null) {
                            $desc_documento = sprintf("%s / Documento indexado en el expediente ExpedienteId %s", $desc_documento, $contenido_doc->getUnidaddocumentalId());
                        } else {
                            $desc_documento = sprintf("%s / Error al indexar el documento en el expediente", $desc_documento);
                        }
                        //*************************************************************************************
                        ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(), $servicioestado_id, $servicio->getUsuarioId(), $servicio->getUsuarioId(), $desc_documento, date("Y-m-d G:i:s"));
                    } else if ($paramArchivarDoc == "radicado") {
                        $desc_documento = sprintf("%s / Indexado en el expediente", $desc_documento);
                        $radicado_com = $servicio->addActaNotificacionToCom($rutaActaEntrega);
                        if (!empty($radicado_com)) {
                            $desc_documento = sprintf("%s / Documento indexado en el radicado %s", $desc_documento, $radicado_com);
                        } else {
                            $desc_documento = sprintf("%s / Error al indexar el documento en el radicado", $desc_documento);
                        }
                        //*************************************************************************************
                        ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(), $servicioestado_id, $servicio->getUsuarioId(), $servicio->getUsuarioId(), $desc_documento, date("Y-m-d G:i:s"));
                    }
                    //*****************************************************************************************
                    unlink($rutaActaEntrega);
                    //*****************************************************************************************
                    return array('status' => 200, 'message' => 'Archivo creado correctamente en el repositorio del Sgdea', 'idMensaje' => $trackingId);
                } else {
                    return array('status' => 400, 'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $trackingId);
                }
            } else {
                return array('status' => 400, 'message' => 'Archivo no encontrado en el repositorio del Sgdea', 'idMensaje' => $trackingId);
            }
        } catch (\Exception $e) {
            return array('status' => 400, 'message' => 'Error creando el archivo en el repositorio del Sgdea', 'idMensaje' => $trackingId);
        }
    }

    private function buildReceiptPath(int $radicadoId, string $trackingId): string
    {
        $baseDir = rtrim(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . md5(time() . $radicadoId), DIRECTORY_SEPARATOR);
        //$baseDir =  rtrim($this->config['rmail_receipts_path'], DIRECTORY_SEPARATOR);
        $fecha   = date('Y' . DIRECTORY_SEPARATOR . 'm');
        $archivo = $radicadoId . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $trackingId) . '_ActaEntrega_Certimail.zip';
        return $baseDir . DIRECTORY_SEPARATOR . $fecha . DIRECTORY_SEPARATOR . $archivo;
    }
}

class SoapClientExtendedMail extends SoapClient
{
    /**
     * Sends SOAP request using a predefined XML
     *
     * Overwrites the default method SoapClient::__doRequest() to make it work
     * with multipart responses.
     *
     * @param string $request      The XML content to send
     * @param string $location The URL to request.
     * @param string $action   The SOAP action. [optional] default=''
     * @param int    $version  The SOAP version. [optional] default=1
     * @param int    $one_way  [optional] ( If one_way is set to 1, this method
     *                         returns nothing. Use this where a response is
     *                         not expected. )
     *
     * @return string The XML SOAP response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        try{
            $current_size_message = strlen($request);
            $result = null;
            file_put_contents("C:/temp/wsdl/soapmsf.txt",$request);
            //if($current_size_message <= self::MAX_FILE_SIZE_MESSAGE){
			$result = parent::__doRequest($request, $location, $action, $version, $one_way);
			$headers = $this->__getLastResponseHeaders();

			// Do we have a multipart request?
			if (preg_match('#^Content-Type:.*multipart\/.*#mi', $headers) !== 0) {
				// Make all line breaks even.
				$result = str_replace("\r\n", "\n", $result);

				// Split between headers and content.
				list(, $content) = preg_split("#\n\n#", $result);
				// Split again for multipart boundary.
				list($result, ) = preg_split("#\n--#", $content);
			}

            //}else{
                //$this->doRequestCurlCustom($request, $location, $action, $version, $one_way = 0);
                //$this->doRequestClientCli($request, $location, $action, $version, $one_way = 0);
            //}
            //**********************************************************************************
            return $result;
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            //echo "<br />";
            //echo "REQUEST SoapFault:\n" . htmlspecialchars($client->__getLastRequest()) . "\n";
            //echo "<br />";
            //echo "RESPONSE SoapFault:\n" . htmlspecialchars($client->__getLastResponse()) . "\n";
            return array('status'=>400,'message'=>'Ocurrio un error al enviar la informaci&oacute;n, error de protocolo => ' . $msgerror);
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            //var_dump($client->__getLastResponseHeaders());
            //var_dump(htmlspecialchars($client->__getLastResponse()));
            return array('status'=>400,'message'=>'Ocurrio un error al enviar la informaci&oacute;n, error de servidor => ' . $msgerror);
        }
    }
}