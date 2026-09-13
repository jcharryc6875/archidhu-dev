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
class WsApiSipost
{
    const SERVICE_PREADMISION_URL = "http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc";
    const SERVICE_SHIPPING_URL = "http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc";

    const SERVICE_PREADMISION_USER = "uariv.app";
    const SERVICE_PREADMISION_PASSW = "Key2025**";

    const SERVICE_TYPE_SERVICENAME = "CORREO CERTIFICADO NACIONAL";
    const SERVICE_NUID_CLIENT = "900490473";

    const SERVICE_TRAZABILIDAD_USER = "TrazabilidadCorp.User";
    const SERVICE_TRAZABILIDAD_PASSW = "f9^@ugNKpQea";

    const SERVICE_TRAZABILIDAD_URL = "https://appcer.4-72.com.co/TrazabilidadCorp/api";
    const SERVICE_DELIVERY_URL = "https://appcer.4-72.com.co/DeliveryEvidence/api";

    const SERVICE_DELIVERY_USER = "uariv.pdt";
    const SERVICE_DELIVERY_PASSW = "Key2025**";

    //const CONTEXT_SOAP2 = array('http' => array( 'user_agent' => 'PHPSoapClient'),'ssl' => array('verify_peer' => false,'verify_peer_name' => false, 'allow_self_signed' => true));

    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;

    protected $path_absolute;
    protected $folder_tmp;
    public $fullpath;
    public $enable_service = false;
    public $save_preguia = false;


    public function __construct()
    {
        $this->logfile = sfConfig::get("sf_log_dir")."\WsApiSipost.log";
        $this->nulog = 0;
        $this->nudebug = 0;
        //********************************************************************************
        $dir_raiz = ParametroPeer::retrieveByPK(9)->getValortexto();
        $dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
        $folder_date = md5(date("YmdGis"));
        //****************************************************************************************
        $this->path_absolute = $dir_raiz;
        $this->folder_tmp = $dir_raiz.$dir_tmp.DIRECTORY_SEPARATOR.$folder_date;

        //************************************************************************************
        $certfisicoenable = array('correo_cert_fisico_enable');
        $certfisenab_array = simad_util::readConfigFileApp($certfisicoenable);
        $this->enable_service = isset($certfisenab_array['correo_cert_fisico_enable']) ? (bool)$certfisenab_array['correo_cert_fisico_enable'] : false;
        //****************************************************************************************
        simad_util::createPath($this->folder_tmp);
    }

    /**
    * WsApiSipost::getTokenTrazabilidad()
    * Solicita un token de acceso al metodo de trazabilidad
    * @return string $token
    */
    private function getTokenTrazabilidad(array $options)
    {
        if($this->enable_service == false){
            return array('status' => 400,'message' => 'El servicio de correo fisico certificado esta deshabilitado');
        }
        //***************************************************************************************
        $parametros = ['Usuario' => trim($options['username']),'Password' => trim($options['password'])];
        //****************************************************************************************
        $headers = [
            'Content-Type: application/json'
        ];
        //****************************************************************************************
        $api = new RestClient([
            'base_url' => self::SERVICE_TRAZABILIDAD_URL,
            'headers' => $headers,
            'curl_options' => [
                CURLOPT_VERBOSE => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        //****************************************************************************************
        $result = $api->post($options['endpoint_login'],$parametros);
        //****************************************************************************************
        if($result->info->http_code == 200)
            $response_json = $result->decode_response();
        else
            return null;
        //****************************************************************************************
        $tokenData = null;
        foreach ($response_json as $key => $value) {
            if($key == "Token" && !empty(trim($value))){
                $tokenData = trim($value);
                break;
            }
        }
        //****************************************************************************************
        return $tokenData;
    }

    /**
    * WsApiSipost::getDeliveryEvidence()
    * Valida y descarga la guia de entrega
    * @return string base64 del archivo
    */
    private function getDeliveryEvidence(array $options)
    {
        if($this->enable_service == false){
            return array('status' => 400,'message' => 'El servicio de correo fisico certificado esta deshabilitado');
        }
        //***************************************************************************************
        $username = trim($options['username']);
        $password = trim($options['password']);
        $credentials = base64_encode("$username:$password");
        //****************************************************************************************
        $parametros = array('IdNumber' => self::SERVICE_NUID_CLIENT,'Barcode' => trim($options['numero_guia']),
            'responseFormat' => 1);
        //****************************************************************************************
        $headers = [
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type: application/json'
        ];
        //****************************************************************************************
        $api = new RestClient([
            'base_url' => self::SERVICE_DELIVERY_URL,
            'headers' => $headers,
            'curl_options' => [
                CURLOPT_VERBOSE => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        //****************************************************************************************
        $api->register_decoder('pdf', function($data) {
            return $data;
        });
        //****************************************************************************************
        $result = $api->post($options['endpoint_delivery'],($parametros));
        //****************************************************************************************
        if($result->info->http_code == 200){
            $response_json = $result->decode_response();
        }else{
            return null;
        }
        //***************************************************************************************
        if($response_json != null){
            return base64_encode($response_json);
        }
        //***************************************************************************************
        return null;
    }

    /**
    * WsApiSipost::getPreadminisionInfo()
    * Obtiene los datos del contrato
    * @return void
    */
    public function getPreadminisionInfo(array $options)
    {
        $username = trim($options['username']);
        $password = trim($options['password']);
        $credentials = base64_encode("$username:$password");
        //***************************************************************************************
        $headers = [
            'Authorization' => 'Basic ' . $credentials,
        ];
        //***************************************************************************************
        $api = new RestClient([
            'base_url' => self::SERVICE_PREADMISION_URL,
            'headers' => $headers,
            'curl_options' => [
                CURLOPT_VERBOSE => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        //***************************************************************************************
        $result = $api->get($options['endpoint_method']);
        //***************************************************************************************
        if($result->info->http_code == 200)
            $response_json = $result->decode_response();
        else
            return null;
        //***************************************************************************************
        foreach ($response_json as $row) {
            if($row->strNameService == $options['service_name']){
                return $row;
            }
        }
    }

    /**
    * WsApiSipost::createPostShipping()
    * Genera una nueva guia de mensajeria de correo fisico
    * @return stdClass
    */
    public function createPostShipping(array $options, $json_data, $saveShipping = false)
    {
        try 
        {
            if($this->enable_service == false){
                return (object)array('status' => 405,'message' => 'El servicio de correo certificado nacional fisico esta deshabilitado');
            }
            //***************************************************************************************
            $username = trim($options['username']);
            $password = trim($options['password']);
            $credentials = base64_encode("$username:$password");
            //***************************************************************************************
            $headers = [
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json'
            ];
            //***************************************************************************************
            $api = new RestClient([
                'base_url' => self::SERVICE_PREADMISION_URL,
                'headers' => $headers,
                'curl_options' => [
                    CURLOPT_VERBOSE => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ]
            ]);
            //***************************************************************************************
            $result = $api->post($options['endpoint_method'],$json_data);
            if($result->info->http_code == 200){
                $response_json = $result->decode_response();
            }elseif($result->info->http_code == 202){
                $this->fullpath = null;
                return (object)array('status' => 400,'message' => 'Ocurrio un error consumiendo el servicio de integracion del proveedor');
            }
            //***************************************************************************************
            $response = new stdClass();
            foreach ($response_json as $row) {
                if(!empty($row)){
                    if($saveShipping){
                        $this->savePdfFromApi($row,$options['filename_shipping']);
                    }
                    //*******************************************************************************
                    $response = (object)$row;
                    break;
                }
            }
            //***************************************************************************************
            return $response;
        } catch (PropelException $th) {
            return (object)(array('status' => 400,'message' => 'Ocurrio con la base de datos => '.$th->getMessage()));
        } catch (\Exception $th) {
            return (object)(array('status' => 400,'message' => 'Ocurrio interno de la aplicacion => '.$th->getMessage()));
        } catch (\Throwable $th) {
            return (object)(array('status' => 400,'message' => 'Ocurrio interno del servidor => '.$th->getMessage()));
        }
    }

    /**
    * WsApiSipost::searchRegCertiCorreoFisico()
    * Permite consultar una lista de registros pendientes por descargar las pruebas de entrega
    * @return array('status','message')
    */
    public function searchRegCertiCorreoFisico()
    {
        $reponse_general = array();
        //**********************************************************************************************
        try 
        {
            if($this->enable_service == false){
                return (object)array('status' => 405,'message' => 'El servicio de correo certificado nacional fisico esta deshabilitado');
            }
            //******************************************************************************************
            // invocar app.ini
            $read_sections = array('archivar_acta_com_enviada');
            $ini_array = simad_util::readConfigFileApp($read_sections);
            $paramArchivarDoc = isset($ini_array['archivar_acta_com_enviada']) ? trim($ini_array['archivar_acta_com_enviada']) : 'servicio';
            //******************************************************************************************
            $ilsit_objects = ServicioCertimailPeer::getListObjectsByProcess(ServicioAppExterna::CorreoFisicoNacional);
            //******************************************************************************************
            foreach ($ilsit_objects as $object_info)            
            {
                $servicio_crtmail = ServicioCertimailPeer::retrieveByPK($object_info['SERVICIOCERTIMAIL_ID']);
                //**************************************************************************************
                $servicio = ServicioPeer::retrieveByPK($object_info['SERVICIO_ID']);
                $servicioestado_id = $servicio->getServicioestadoId();
                //**************************************************************************************
                $response = $this->getTrazabilidadCorreoFisico([
                    'username' => WsApiSipost::SERVICE_TRAZABILIDAD_USER,
                    'password' => WsApiSipost::SERVICE_TRAZABILIDAD_PASSW,
                    'endpoint_login' => 'Login',
                    'endpoint_trazabilidad' => 'TraceabilityCorp/Summarized',
                ],$servicio_crtmail->getServicioId(),$servicio_crtmail->getCertimailmsgId());
                //**************************************************************************************
                $response_process = array('status' => 400,'message' => 'Ocurrio un error en el aplicativo Sgdea');
                //**************************************************************************************
                try 
                {
                    if($response['statusCode'] == 200)
                    {
                        $tmp_path = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(time().$servicio->getPrimaryKey());
                        $filename = sprintf("%s_%s_%s_Guia_ActaEntregaFisica.pdf",uniqid(),$servicio_crtmail->getServicioId(),$servicio_crtmail->getCertimailmsgId());
                        $file_target = $tmp_path.DIRECTORY_SEPARATOR.$filename;
                        $isCreateFile = false;
                        //******************************************************************************
                        if(!empty($response['file_base64'])){
                            simad_util::createPath($tmp_path);
                            $isCreateFile = simad_util::getConvertB64ToFile($response['file_base64'],$file_target);
                        }
                        //******************************************************************************
                        if($isCreateFile && file_exists($file_target)){
                            $isNotifiedTask = $paramArchivarDoc == "servicio" ? $servicio->attachNewDocumetBitacora($file_target) : true;
                            //**************************************************************************
                            if($isNotifiedTask){
                                $desc_documento = "Guía de Envío y Entrega de Correo Certificado Nacional";
                                //**********************************************************************
                                if($paramArchivarDoc == "expediente"){
                                    $contenido_doc = $servicio->archivarActaNotificacion($file_target,$desc_documento);
                                    if($contenido_doc != null){
                                        $desc_documento = sprintf("%s / Documento indexado en el expediente ExpedienteId %s",$desc_documento, $contenido_doc->getUnidaddocumentalId());
                                    }else{
                                        $desc_documento = sprintf("%s / Error al indexar el documento en el expediente",$desc_documento);
                                    }
                                    //******************************************************************
                                    ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                                }else if($paramArchivarDoc == "radicado"){
                                    $desc_documento = sprintf("%s / Indexado en la comunicación",$desc_documento);
                                    //******************************************************************
                                    $current_file = file_get_contents($file_target);
                                    //******************************************************************
                                    $radicado_com = $servicio->addActaNotificacionToCom($file_target);
                                    //******************************************************************
                                    if(!empty($radicado_com) || $radicado_com == true){
                                        //**************************************************************
                                        if(!file_exists($file_target)){ file_put_contents($file_target,$current_file); }
                                        //**************************************************************
                                        $contenido_doc = $servicio->archivarActaNotificacion($file_target,$desc_documento);
                                        //**************************************************************
                                        if($contenido_doc != null){
                                            $contenido_doc->setModuloId($servicio->getModuloId());
                                            $contenido_doc->setConsecutivoId($servicio->getConsecutivocomId());
                                            $contenido_doc->setParentdocId($contenido_doc->getPrimaryKey());
                                            $contenido_doc->save();
                                            //**********************************************************
                                            $desc_documento = sprintf("%s / Documento indexado en el radicado %s y en el expediente con Id_Expediente %s",$desc_documento, $radicado_com,$contenido_doc->getUnidaddocumentalId());
                                        }else{
                                            $desc_documento = sprintf("%s / Documento indexado en el radicado %s",$desc_documento, $radicado_com);
                                        }
                                    }else{
                                        $desc_documento = sprintf("%s / Error al indexar el documento en el radicado",$desc_documento);
                                    }
                                    //******************************************************************
                                    ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$desc_documento,date("Y-m-d G:i:s"));
                                }
                                //**********************************************************************
                                if(file_exists($file_target)){ unlink($file_target); }
                                //**********************************************************************
                                $response_process = array('status' => 200,'message' => 'El archivo fue almacenado correctamente en el Sgdea');
                            }else{
                                $response_process = array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea');
                            }
                        }else{
                            $response_process = array('status' => 400,'message' => 'Error creando el archivo en el repositorio del Sgdea');
                        }
                        //******************************************************************************
                        if($response_process['status'] == 200){
                            $servicio_crtmail->setEstaAbierta(0);
                            $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                            $servicio_crtmail->setEstadoEjecucion("DescargaCertificadoExitoso");
                            $servicio_crtmail->save();
                            //**************************************************************************
                            $servicio->setServicioestadoId(SrvMensajeriaStatus::Ejecutada);
                            $servicio->save();
                        }else{
                            $servicio_crtmail->setEstaAbierta(1);
                            $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                            $servicio_crtmail->setEstadoEjecucion("ErrorProveedorIntegracion");
                            $servicio_crtmail->save();
                        }
                        //******************************************************************************
                        $reponse_general[] = $response_process;
                    }elseif($response['statusCode'] == 205){
                        $servicio_crtmail->setEstaAbierta(0);
                        $servicio_crtmail->setEstadoEjecucion("GuiaTramiteEntregaFallida");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                        //******************************************************************************
                        ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicioestado_id,$servicio->getUsuarioId(),$servicio->getUsuarioId(),$response['message'],date("Y-m-d G:i:s"));
                        //******************************************************************************
                        $servicio->setServicioestadoId(SrvMensajeriaStatus::EjecutadoDevuelto);
                        $obs_serv = empty(trim($servicio->getObsDevolucion())) ? $response['message'] : trim($servicio->getObsDevolucion())."|".$response['message'];
                        $servicio->setObsDevolucion($obs_serv);
                        //******************************************************************************
                        $servicio->save();
                    }elseif($response['statusCode'] == 300){
                        $servicio_crtmail->setEstaAbierta(1);
                        $servicio_crtmail->setEstadoEjecucion("GuiaTramiteSinEventos");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                    }else{
                        $servicio_crtmail->setEstaAbierta(1);
                        $servicio_crtmail->setEstadoEjecucion("ErrorDescargaCertificado");
                        $servicio_crtmail->setFechaModificacion(date("Y-m-d G:i:s"));
                        $servicio_crtmail->save();
                    }
                } catch (PropelException $th) {
                    $response_process = array('status' => 400,'message' => 'Ocurrio con la base de datos => '.$th->getMessage());
                } catch (\Exception $th) {
                    $response_process = array('status' => 400,'message' => 'Ocurrio interno de la aplicacion => '.$th->getMessage());
                } catch (\Throwable $th) {
                    $response_process = array('status' => 400,'message' => 'Ocurrio interno del servidor => '.$th->getMessage());
                }
            }
            //******************************************************************************************
            return $reponse_general;
        } catch (PropelException $th) {
            $reponse_general = array('status' => 400,'message' => 'Ocurrio con la base de datos => '.$th->getMessage());
        } catch (\Exception $th) {
            $reponse_general = array('status' => 400,'message' => 'Ocurrio interno de la aplicacion => '.$th->getMessage());
        } catch (\Throwable $th) {
            $reponse_general = array('status' => 400,'message' => 'Ocurrio interno del servidor => '.$th->getMessage());
        }
        //**********************************************************************************************
        return $reponse_general;
    }

    /**
    * WsApiSipost::getTrazabilidadCorreoFisico()
    * funcion para consumir servicio web de andes para enviar un correo certificado fisico
    * @return array()
    */
    public function getTrazabilidadCorreoFisico(array $options, $servicio_id, $idsMensage, $paramArchivarDoc = "servicio")
    {
        if($this->enable_service == false){
            return (object)array('status' => 405,'message' => 'El servicio de correo certificado nacional fisico esta deshabilitado');
        }
        //****************************************************************************************
        $token_trazabilidad = $this->getTokenTrazabilidad($options);
        if(empty($token_trazabilidad)){
            return array('error' => true, 'message' => 'error obteniendo el token para consultar la trazabilidad');
        }
        //****************************************************************************************
        $headers = [
            'Authorization' => 'Bearer ' . $token_trazabilidad,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        //****************************************************************************************
        if(is_array($idsMensage)){
            $list_guias = $idsMensage;
        }else{
            $list_guias = [
                $idsMensage
            ];
        }
        //****************************************************************************************
        $api = new RestClient([
            'base_url' => self::SERVICE_TRAZABILIDAD_URL,
            'headers' => $headers,
            'curl_options' => [
                CURLOPT_VERBOSE => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);
        //****************************************************************************************
        try {
            $result = $api->post($options['endpoint_trazabilidad'],json_encode($list_guias));
            if($result->info->http_code == 200){
                $response_json = $result->decode_response();
            }else{
                return array('error' => true, 'statusCode' => 400, 'message' => 'error consumiendo el servicio de trazbilidad');
            }
            //************************************************************************************
            $b64_guias = null;
            foreach($response_json as $row_class){
                if($row_class != null && !empty(trim($row_class->ShipmentCode))){
                    $trazabilidad_event = $row_class->Eventos;
                    //****************************************************************************
                    if(empty($trazabilidad_event)){
                        return  array('error' => false, 'statusCode' => 300, 'file_base64' => null, 'message' => $row_class->ResponseMessage);
                    }
                    //****************************************************************************
                    foreach ($trazabilidad_event as $row_event) {
                        if($row_event->TipoEvento == "EN DESTINO" && in_array($row_event->IdObjeto,$list_guias)){
                            if($row_event->Objeto == "GUIA"){
                                $current_b64 = $this->getDeliveryEvidence([
                                    'username' => WsApiSipost::SERVICE_DELIVERY_USER,
                                    'password' => WsApiSipost::SERVICE_DELIVERY_PASSW,
                                    'numero_guia' => trim($row_class->ShipmentCode),
                                    'endpoint_delivery' => 'DeliveryEvidence/DownloadFileDeliveryTest',
                                ]);
                                //****************************************************************
                                if($current_b64){
                                    $b64_guias['file_base64'] = $current_b64;
                                    $b64_guias['statusCode'] = 200;
                                    $b64_guias['numero_guia'] = trim($row_class->ShipmentCode);
                                    $b64_guias['message'] = trim($row_event->Descripcion);
                                }
                            }else{
                                $b64_guias['statusCode'] = 500;
                                $b64_guias['numero_guia'] = trim($row_class->ShipmentCode);
                                $b64_guias['message'] = "Guia no digitalizada(".trim($row_event->Descripcion).")";
                            }
                        }elseif ($row_event->TipoEvento == "EN DESTINO" && isset($row_event->liquidacion)) {
                            $b64_guias['statusCode'] = 205;
                            $b64_guias['numero_guia'] = trim($row_class->ShipmentCode);
                            $b64_guias['message'] = sprintf("Error Entrega, %s, %s",trim($row_event->Descripcion),trim($row_event->liquidacion));
                        }elseif ($row_event->TipoEvento == "EN ORIGEN" && $row_event->Descripcion == "RECHAZADO") {
                            $b64_guias['statusCode'] = 205;
                            $b64_guias['numero_guia'] = trim($row_class->ShipmentCode);
                            $b64_guias['message'] = sprintf("Error Entrega, %s, %s",trim($row_event->Descripcion),trim($row_event->liquidacion));
                        }
                    }
                }
            }
            //************************************************************************************
            return $b64_guias;
        }catch (\Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('error' => true,'statusCode' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental');
        }catch (\Throwable $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return array('error' => true,'statusCode' => 400,'message'=>'Ocurrio un error al enviar la datos, error de servidor del aplicativo de gestión documental');
        }
    }

    /**
    * WsApiSipost::savePdfFromApi()
    * funcion para guardar el pdf de la api rest de sipost
    * @return array()
    */
    private function savePdfFromApi($apiResponse, $filename) 
    {
        try {
            if (!isset($apiResponse->byteGuidePDF)) {
                return false;
            }
            //************************************************************************************
            $pdfContent = '';
            foreach ($apiResponse->byteGuidePDF as $byte) {
                $pdfContent .= chr($byte);
            }
            //************************************************************************************
            $this->fullpath = null;
            if(file_put_contents($this->folder_tmp.DIRECTORY_SEPARATOR.$filename, $pdfContent)){
                $this->fullpath = $this->folder_tmp.DIRECTORY_SEPARATOR.$filename;
                return true;
            }else{
                return true;
            }
        } catch (\Exception $th) {
            return true;
        } catch (\Throwable $th) {
            return true;
        }
    }
}