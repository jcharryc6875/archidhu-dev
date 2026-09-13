<?php
 require_once(dirname(__FILE__).'/../../../../../config/ProjectConfiguration.class.php');
 $configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
 sfContext::createInstance($configuration);
//*************************************************************************************** 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//***************************************************************************************
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
//***************************************************************************************
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = preg_split("/[\\/]+/",$uri, -1, PREG_SPLIT_NO_EMPTY);
if(!in_array('public',$uri)){
    header("HTTP/1.1 404 Not Found");
    exit();
}
//***************************************************************************************
$requestMethod = $_SERVER["REQUEST_METHOD"];
if(!in_array($requestMethod,array("POST"))){
    $response['objectdata'] = null;
    $response['error'] = true;
    $response['message'] = mb_convert_encoding("El metodo no esta habilitado","UTF-8");
    echoResponse(405, $response);
}
//***************************************************************************************
$dataid = array_pop($uri);
//***************************************************************************************
/*$response['objectdata'] = $uploadfile;
$response['error'] = false;
$response['message'] = utf8_encode($_POST["filedata"]);
$bytesStr = pack('C*',$_POST["filedata"]);
file_put_contents('C:/Imagenes/pgd/myfile.pdf', base64_decode($_POST["filedata"]));
echoResponse(200, $response);*/
//***************************************************************************************
//if(in_array("fileuploads",$uri) || $dataid == "fileuploads"){
if($dataid == "fileuploads"){
    if (is_uploaded_file($_FILES['file']['tmp_name'])){
        $info_files = pathinfo($_FILES['file']['name']);
        $upload_dir = simad_util::createPath(sfConfig::get('sf_upload_dir'),0777);
        $uploadfile = $upload_dir .DIRECTORY_SEPARATOR. uniqid(date("YmdGis")) .'.'. $info_files['extension'];        
        //$uploadfile = sprintf("%s%s%s","C:/Imagenes/pgd",DIRECTORY_SEPARATOR, $_FILES['file']['name']);
        $isMoved = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        if($isMoved){
            //chmod($uploadfile, 0755);
            $response['objectdata'] = $uploadfile;
            $response['error'] = false;
            $response['message'] = mb_convert_encoding("la imagen se envió correctamente al servidor",'UTF-8');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("Error al subir la imagen al servidor",'UTF-8');
            echoResponse(400, $response);
        }
    }
}
//***************************************************************************************
if((in_array("imguploads",$uri) || ($dataid == "imguploads"))){
    $vars_request = array();
    initObjectByRequest($vars_request);
    //***********************************************************************************
    if(!trim($dataid)){
        $response['objectdata'] = null;
        $response['error'] = true;
        $response['message'] = mb_convert_encoding("El parametro radicado es obligatorio",'UTF-8');
        echoResponse(400, $response);
    }
    //***********************************************************************************
    if($vars_request['tipocom'] == 'com_enviada'){
        $isValid = initProcessComEnviada($dataid,$vars_request);
    }elseif($vars_request['tipocom'] == 'com_recibida'){
        $isValid = initProcessComRecibida($dataid,$vars_request);
    }elseif($vars_request['tipocom'] == 'com_interna'){
        $isValid = initProcessComInterna($dataid,$vars_request);
    }elseif($vars_request['tipocom'] == 'facturas'){
        $isValid = initProcessFactura($dataid,$vars_request);
    }elseif($vars_request['tipocom'] == 'archivo'){
        $isValid = initProcessArchivo($dataid,$vars_request);
	}elseif($vars_request['tipocom'] == 'vinculada'){
        $isValid = initProcessVinculada($dataid,$vars_request);
    }elseif($vars_request['tipocom'] == 'storage'){
        $isValid = initProcessDefault($vars_request);
    }else{
        $response['objectdata'] = null;
        $response['error'] = true;
        $response['message'] = mb_convert_encoding("El valor del parametro tipocom no es valido",'UTF-8');
        echoResponse(400, $response); 
    }
}

function getParamsByHeaderType()
{
    $request_params = array();
    if($_SERVER["CONTENT_TYPE"] == "application/x-www-form-urlencoded"){
        parse_str(file_get_contents("php://input"),$request_params);
    }elseif($_SERVER["CONTENT_TYPE"] == "application/json"){
        $request_params = json_decode(file_get_contents("php://input"),true);
    }else{
        parse_str(file_get_contents("php://input"),$request_params);
    }
    return $request_params;
}

function saveFileByData($filename,$data,$targetpath)
{
    if(empty($data)){
        $response = array();
        $response['error'] = true;
        $response['message'] = mb_convert_encoding('Error con el archivo adjunto, parametro filedata esta vacio','UTF-8');
        echoResponse(400, $response);
    }
    //************************************************************************************
    if(empty($targetpath)){
        $response = array();
        $response['error'] = true;
        $response['message'] = mb_convert_encoding('Error con el archivo adjunto, directorio destino no existe','UTF-8');
        echoResponse(400, $response);
    }
    //************************************************************************************
    try{
        $fullpath = $targetpath.DIRECTORY_SEPARATOR.$filename;
        simad_util::createPath($targetpath);
        //********************************************************************************
        if(file_exists($data)){
            if(copy($data,mb_convert_encoding($fullpath,'UTF-8'))){ unlink($data); }
        }else{
            file_put_contents($fullpath,base64_decode($data));
        }
        //********************************************************************************
        if(file_exists(mb_convert_encoding($fullpath,'UTF-8'))){
            return $fullpath;
        }else{
            $response = array();
            $response['error'] = true;
            $response['message'] = mb_convert_encoding('Error al subir el adjunto saveFileByData '.$data,'UTF-8');
            echoResponse(400, $response);
        }
    }catch(Exception $ex){        
        $response = array();
        $response['error'] = true;
        $response['message'] = mb_convert_encoding('Error relacionado con el archivo adjunto {'.$ex->getMessage().'}','UTF-8');
        echoResponse(400, $response);
    }
}

function initProcessDefault($vars_request)
{
    try{
        $folder_target = "";
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        $rootdir = isset($vars_request['rootdir']) ? trim($vars_request['rootdir']) : "";
        $pathdest = isset($vars_request['pathdest']) ? trim($vars_request['pathdest']) : "";
        //********************************************************************
        if($vars_request['tipofolder'] == 'attach'){
            $dir_tmp  = ParametroPeer::retrieveByPk(65)->getValortexto();
            $dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
            //$folder_target = simad_util::createPath($dirRaiz.$dir_tmp.DIRECTORY_SEPARATOR.$rootdir.DIRECTORY_SEPARATOR.date("Ymd"));
            $folder_target = !empty($pathdest) ? simad_util::createPath($pathdest.DIRECTORY_SEPARATOR.$rootdir) : simad_util::createPath($dirRaiz.$dir_tmp.DIRECTORY_SEPARATOR.$rootdir);
            //**************************************************************************
            $filetmp     = $vars_request['filename'].'.'.$extension_file;
            $filename    = $folder_target.DIRECTORY_SEPARATOR.$filetmp;
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("El valor del parametro tipofolder no es valido",'UTF-8');
            echoResponse(400, $response);
        }
        //******************************************************************************
        if(!empty($filename)){
            simad_util::getConvertB64ToFile($vars_request['filedata'],$filename);            
            //**************************************************************************
            if(file_exists($filename)){
                $response['objectdata'] = ($filename);
                $response['error'] = false;
                $response['message'] = ('El archivo fue almacenado satisfactoriamente');
                echoResponse(200, $response);
            }else{
                $response['objectdata'] = "";
                $response['error'] = true;
                $response['message'] = mb_convert_encoding('Ocurrio un error interno y no se pudo enviar el archivo al servidor','UTF-8');
                echoResponse(200, $response);
            }
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = mb_convert_encoding('Ocurrio un error interno y no se pudo enviar el archivo al servidor','UTF-8');
            echoResponse(200, $response);
        }
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = mb_convert_encoding($ex->getMessage(),'UTF-8');
        echoResponse(500, $response);
    }
}

function initProcessComEnviada($com_radicado,$vars_request)
{
    try{
        $folder_target = "";
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $com_enviada = ComEnviadaPeer::getComObjectByRadicado($com_radicado,$vars_request['periodo']);
        //********************************************************************
        if($com_enviada == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("La comunicación no fue encontrada",'UTF-8');
            echoResponse(400, $response);
        }
        //******************************************************************************
        if($vars_request['tipofolder'] == 'digit'){
            $folder_target = ComEnviadaPeer::initFolderDigit($com_enviada->getRegionalId(),$com_enviada->getPeriodoId());
            //**************************************************************************
            $extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
            //**************************************************************************
            if(!in_array($extension_file,$extensions)){
                $response['objectdata'] = null;
                $response['error'] = true;
                $response['message'] = utf8_encode("El tipo de archivo no esta permitido");
                echoResponse(400, $response);
            }
            //**************************************************************************            
            $filename = $com_enviada->getRadicado().'.'.$extension_file;
        }elseif($vars_request['tipofolder'] == 'attach'){
            $usuario_radica = $com_enviada->getUserIdComObjectByRol(1);
            $folder_target  = ComEnviadaPeer::initFolderAttach($com_enviada->getRegionalId(),$usuario_radica,true);
            //**************************************************************************
            $filetmp     = $vars_request['filename'].'.'.$extension_file;
            $file_vars   = pathinfo($folder_target['full_path'].DIRECTORY_SEPARATOR.$filetmp);
            $util_simad  = new simad_util();
            $filename    = uniqid().$util_simad->clean_name_file($file_vars);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El valor del parametro tipofolder no es valido");
            echoResponse(400, $response);
        }
        //******************************************************************************
        if(!count($folder_target)){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("Ocurrio un error interno y no se pudo realizar la acción");
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$vars_request['filedata'],$folder_target['full_path']);
        //******************************************************************************
        if(trim($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = $folder_target['alias_web'].'/'.$filename;
                $strspe = simad_util::endsWith(trim($com_enviada->getRuta()),",") ? "" : ",";
                $ruta_str = trim($com_enviada->getRuta()) ? trim($com_enviada->getRuta()).$strspe.$alias_file : $alias_file;
                $com_enviada->setRuta($ruta_str);
                $com_enviada->save();
            }
            //**************************************************************************
            $response['objectdata'] = basename($filename);
            $response['error'] = false;
            $response['message'] = utf8_encode('El archivo fue indexado satisfactoriamente');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = utf8_encode('Ocurrio un error interno y no se pudo enviar el archivo al servidor');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = utf8_encode($ex->getMessage());
        echoResponse(500, $response);
    }
}

function initProcessComInterna($com_radicado,$vars_request)
{
    try{
        $folder_target = "";
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $com_interna = ComInternaPeer::getComObjectByRadicado($com_radicado,$vars_request['periodo']);
        //********************************************************************    
        if($com_interna == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("La comunicación no fue encontrada");
            echoResponse(400, $response);
        }
        //********************************************************************
        if($vars_request['tipofolder'] == 'digit'){
            $folder_target = ComInternaPeer::initFolderDigit($com_interna->getRegionalId(),$com_interna->getPeriodoId());
            //**************************************************************************
            $extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
            //**************************************************************************
            if(!in_array($extension_file,$extensions)){
                $response['objectdata'] = null;
                $response['error'] = true;
                $response['message'] = utf8_encode("El tipo de archivo no esta permitido");
                echoResponse(400, $response);
            }
            //**************************************************************************
            $filename = $com_interna->getRadicado().'.'.$extension_file;
        }elseif($vars_request['tipofolder'] == 'attach'){
            $comuser_list = $com_interna->getUsuariosListComIds(true);
            $usuario_radica = $comuser_list['radicador'];
            $folder_target = ComInternaPeer::initFolderAttach($com_interna->getRegionalId(),$usuario_radica,true);
            //**************************************************************************
            $filetmp     = $vars_request['filename'].'.'.$extension_file;
            $file_vars   = pathinfo($folder_target['full_path'].DIRECTORY_SEPARATOR.$filetmp);
            $util_simad  = new simad_util();
            $filename    = uniqid().$util_simad->clean_name_file($file_vars);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El valor del parametro tipofolder no es valido");
            echoResponse(400, $response);
        }
        //******************************************************************************
        if(!count($folder_target)){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("Ocurrio un error interno y no se pudo realizar la acción");
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$vars_request['filedata'],$folder_target['full_path']);
        //******************************************************************************
        if(trim($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = $folder_target['alias_web'].'/'.$filename;
                $strspe = simad_util::endsWith(trim($com_interna->getRuta()),",") ? "" : ",";
                $ruta_str = trim($com_interna->getRuta()) ? trim($com_interna->getRuta()).$strspe.$alias_file : $alias_file;
                $com_interna->setRuta($ruta_str);
                $com_interna->save();
            }
            //**************************************************************************
            $response['objectdata'] = basename($filename);
            $response['error'] = false;
            $response['message'] = utf8_encode('El archivo fue indexado satisfactoriamente');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = utf8_encode('Ocurrio un error interno y no se pudo enviar el archivo al servidor');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = mb_convert_encoding($ex->getMessage(),'UTF-8');
        echoResponse(500, $response);
    }
}

function initProcessComRecibida($com_radicado,$vars_request)
{
    try{
        $folder_target = array();
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $com_recibida = ComRecibidaPeer::getComObjectByRadicado($com_radicado,$vars_request['periodo']);
        //********************************************************************
        if($com_recibida == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("La comunicación no fue encontrada",'UTF-8');
            echoResponse(400, $response);
        }
        //********************************************************************    
        if($vars_request['tipofolder'] == 'digit'){
            $folder_target = ComRecibidaPeer::initFolderDigit($com_recibida->getRegionalId(),$com_recibida->getPeriodoId());
            //**************************************************************************
            $extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
            //**************************************************************************
            if(!in_array($extension_file,$extensions)){
                $response['objectdata'] = null;
                $response['error'] = true;
                $response['message'] = mb_convert_encoding("El tipo de archivo no esta permitido",'UTF-8');
                echoResponse(400, $response);
            }
            //**************************************************************************
            $filename = $com_recibida->getRadicado().'.'.$extension_file;
            $file_attach = $vars_request['filedata'];
            //**************************************************************************
            if(strtoupper($extension_file) == 'PDF'){
                $tmpfdir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp'.DIRECTORY_SEPARATOR. md5(time() . 'rademail' . uniqid());
                simad_util::createPath($tmpfdir);
                $tmpfname = $tmpfdir . DIRECTORY_SEPARATOR . md5(date("YmdGis") . uniqid()) . '.' . $extension_file;
                //**********************************************************************
                simad_util::getConvertB64ToFile($vars_request['filedata'],$tmpfname);
                //**********************************************************************
                if ($com_recibida->getFormaRecepcion()->getStampSticker()) {
                    $file_attach = $com_recibida->stampStickerToDigit($tmpfname, true);
                    if (file_exists($file_attach)) {
                        $response_ws = $com_recibida->stampDocumentProcess($file_attach);
                        if (!$response_ws['error']) {
                            $unique_file = $tmpfdir . DIRECTORY_SEPARATOR . md5(time() . uniqid()) . '.pdf';
                            $isFileCreate = simad_util::getConvertB64ToFile($response_ws['file_base64'], $unique_file);

                            if ($isFileCreate) {
                                $file_attach = $unique_file;
                            }else{
                                $file_attach = $tmpfname;
                            }
                        }
                    }else{
                        $file_attach = $tmpfname;
                    }
                }else if(file_exists($tmpfname)){
                    $response_ws = $com_recibida->stampDocumentProcess($file_attach);
                    if (!$response_ws['error']) {
                        $unique_file = $tmpfdir . DIRECTORY_SEPARATOR . md5(time() . uniqid()) . '.pdf';
                        $isFileCreate = simad_util::getConvertB64ToFile($response_ws['file_base64'], $unique_file);
                    }

                    if ($isFileCreate) {
                        $file_attach = $unique_file;
                    }else{
                        $file_attach = $tmpfname;
                    }
                }else{
                    $file_attach = $vars_request['filedata'];
                }
            }
        }elseif($vars_request['tipofolder'] == 'attach'){
            $usuario_radica = $com_recibida->getUserIdComRecibidaRol(1);
            $usuario_radica = $usuario_radica == null ? $com_recibida->getUserIdComRecibidaRol(2) : $usuario_radica;
            $folder_target = ComRecibidaPeer::initFolderAttach($com_recibida->getRegionalId(),$usuario_radica,true);
            //**************************************************************************
            $filetmp     = $vars_request['filename'].'.'.$extension_file;
            $file_vars   = pathinfo($folder_target['full_path'].DIRECTORY_SEPARATOR.$filetmp);
            $util_simad  = new simad_util();
            $filename    = uniqid().'_'.$util_simad->clean_name_file($file_vars);
            $file_attach = $vars_request['filedata'];
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("El valor del parametro tipofolder no es valido",'UTF-8');
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$file_attach,$folder_target['full_path']);
        //******************************************************************************
        if(file_exists($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = $folder_target['alias_web'].'/'.$filename;
                $strspe = simad_util::endsWith(trim($com_recibida->getRuta()),",") ? "" : ",";
                $ruta_str = trim($com_recibida->getRuta()) ? trim($com_recibida->getRuta()).$strspe.$alias_file : $alias_file;
                $com_recibida->setRuta($ruta_str);
                $com_recibida->save();
            }
            //**************************************************************************
            if($vars_request['tipofolder'] == 'digit'){
                $com_recibida->setFechaDigit(date("Y-m-d G:i:s"));
                $com_recibida->setTipoprocesocomId(2);
                $com_recibida->setIsLocked(0);
                $com_recibida->setEstadodigitalizacionId(2);
                $com_recibida->save();
                //**********************************************************************
                ComRecibidaPeer::updateEstadosComByEstado($com_recibida->getPrimaryKey(),1,array(2,3));
                ComRecibidaPeer::updateFechaAsignaCom($com_recibida->getPrimaryKey(),array(2));
            }
            //**************************************************************************
            $response['objectdata'] = basename($filename);
            $response['error'] = false;
            $response['message'] = mb_convert_encoding('El archivo indexado satisfactoriamente','UTF-8');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = mb_convert_encoding('Ocurrio un error interno y no se pudo enviar el archivo al servidor','UTF-8');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = mb_convert_encoding($ex->getMessage(),'UTF-8');
        echoResponse(500, $response);
    }
}

function initProcessFactura($com_radicado,$vars_request)
{
    try{
        $folder_target = "";
        $util_simad  = new simad_util();
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $factura = FacturaPeer::getComObjectByRadicado($com_radicado,$vars_request['periodo']);
        //********************************************************************
        if($factura == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("La factura no fue encontrada",'UTF-8');
            echoResponse(400, $response);
        }
        //********************************************************************
        if($vars_request['tipofolder'] == 'attach'){
            $folder_target = "";
            $usuario_radica = FacturaPeer::getUserIdByFirtsRadicador($factura->getPrimaryKey());
            $folder_list = FacturaPeer::getBasicUrlAttachFacturas($usuario_radica,$factura->getPeriodoId(),true);
            if(count($folder_list)) { $folder_target =  $folder_list['full_path']; }
            //**************************************************************************
            $filetmp     = $vars_request['filename'].'.'.$extension_file;
            $file_vars   = pathinfo($folder_target.DIRECTORY_SEPARATOR.$filetmp);        
            $filename    = uniqid().$util_simad->clean_name_file($file_vars);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = mb_convert_encoding("El valor del parametro tipofolder no es valido",'UTF-8');
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$vars_request['filedata'],$folder_target);
        //******************************************************************************
        if(trim($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = $folder_list['alias_web'].'/'.$filename;
                $strspe = simad_util::endsWith(trim($factura->getRuta()),",") ? "" : ","; 
                $ruta_str = trim($factura->getRuta()) ? trim($factura->getRuta()).$strspe.$alias_file : $alias_file;
                $factura->setRuta($ruta_str);
                $factura->save();
            }
            //**************************************************************************
            $response['objectdata'] = ($filename);
            $response['error'] = false;
            $response['message'] = utf8_encode('El archivo fue indexado satisfactoriamente');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = utf8_encode('Ocurrio un error interno y no se pudo enviar el archivo al servidor');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = utf8_encode($ex->getMessage());
        echoResponse(500, $response);
    }
}

function initProcessArchivo($contenidoexp_id,$vars_request)
{
    try{
        $folder_target = "";
        $util_simad  = new simad_util();
        $filename = "";
        $folder_list = array();
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $contenido_exp = ContenidoUnidadDocumentalPeer::retrieveByPK($contenidoexp_id);
        //********************************************************************
        if($contenido_exp == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El expediente no fue encontrado");
            echoResponse(400, $response);
        }
        //********************************************************************
        if($vars_request['tipofolder'] == 'attach'){
            $folder_target = "";
            $unidad_documental = UnidadDocumentalPeer::retrieveByPK($contenido_exp->getUnidaddocumentalId());    
            $folder_list = $unidad_documental->getBaseUrlAttachment(null,true);
            if(count($folder_list)) { $folder_target =  $folder_list['full_path']; }
            //**************************************************************************
            $filetmp   = $vars_request['filename'].'.'.$extension_file;
            $file_vars = pathinfo($folder_target.DIRECTORY_SEPARATOR.$filetmp);        
            $filename  = uniqid().$util_simad->clean_name_file($file_vars);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El valor del parametro tipofolder no es valido");
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$vars_request['filedata'],$folder_target);
        //******************************************************************************
        if(trim($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = utf8_decode($folder_list['alias_web']).'/'.$filename;
                $strspe = simad_util::endsWith(trim($contenido_exp->getRuta()),",") ? "" : ","; 
                $ruta_str = trim($contenido_exp->getRuta()) ? trim($contenido_exp->getRuta()).$strspe.$alias_file : $alias_file;
                $contenido_exp->setRuta($ruta_str);
                $contenido_exp->save();
            }
            //**************************************************************************
            $response['objectdata'] = ($filename);
            $response['error'] = false;
            $response['message'] = utf8_encode('El archivo fue indexado satisfactoriamente');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = utf8_encode('Ocurrio un error interno y no se pudo enviar el archivo al servidor');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = utf8_encode($ex->getMessage());
        echoResponse(500, $response);
    }    
}

function initProcessVinculada($vinculada_id,$vars_request)
{
    try{
        $folder_target = "";
        $util_simad  = new simad_util();
        $filename = "";
        $folder_list = array();
        $extension_file = trim(strtolower($vars_request['tipoimg']));
        //********************************************************************
        $vinculada = VinculadaPeer::retrieveByPK($vinculada_id);
        //********************************************************************
        if($vinculada == null){
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El registro de vinculación no fue encontrado");
            echoResponse(400, $response);
        }
        //********************************************************************
        if($vars_request['tipofolder'] == 'attach'){
            $folder_target = "";
            $folder_list = $vinculada->getBaseUrlAttachment(null,true);
            if(count($folder_list)) { $folder_target =  $folder_list['full_path']; }
            //**************************************************************************
            $filetmp   = $vars_request['filename'].'.'.$extension_file;
            $file_vars = pathinfo($folder_target.DIRECTORY_SEPARATOR.$filetmp);        
            $filename  = uniqid().$util_simad->clean_name_file($file_vars);
        }else{
            $response['objectdata'] = null;
            $response['error'] = true;
            $response['message'] = utf8_encode("El valor del parametro tipofolder no es valido");
            echoResponse(400, $response);
        }
        //******************************************************************************
        $isValidFile = saveFileByData($filename,$vars_request['filedata'],$folder_target);
        //******************************************************************************
        if(trim($isValidFile)){
            if($vars_request['tipofolder'] == 'attach'){
                $alias_file = utf8_decode($folder_list['alias_web']).'/'.$filename;
                $strspe = simad_util::endsWith(trim($vinculada->getRuta()),",") ? "" : ","; 
                $ruta_str = trim($vinculada->getRuta()) ? trim($vinculada->getRuta()).$strspe.$alias_file : $alias_file;
                $vinculada->setRuta($ruta_str);
                $vinculada->save();
            }
            //**************************************************************************
            $response['objectdata'] = ($filename);
            $response['error'] = false;
            $response['message'] = utf8_encode('El archivo fue indexado satisfactoriamente');
            echoResponse(200, $response);
        }else{
            $response['objectdata'] = "";
            $response['error'] = true;
            $response['message'] = utf8_encode('Ocurrio un error interno y no se pudo enviar el archivo al servidor');
            echoResponse(200, $response);
        }
        //********************************************************************
        return $isValidFile;
    }catch(Exception $ex){
        $response['objectdata'] = "";
        $response['error'] = true;
        $response['message'] = utf8_encode($ex->getMessage());
        echoResponse(500, $response);
    }
}

function initObjectByRequest(&$fiels_request)
{
    if(!count($_POST)){
        $response['objectdata'] = null;
        $response['error'] = true;
        $response['message'] = utf8_encode("Ningun parametro recibidoxx");
        echoResponse(400, $response);
    }    
    //********************************************************************
    $error = false;
    $msgerror = array();
    //********************************************************************
    if($_POST['tipocom']){
       $fiels_request['tipocom'] =  trim($_POST['tipocom']);
    }else{
        $msgerror[] = utf8_encode("El parametro tipocom es obligatorio");
        $error = true;
    }
    //********************************************************************
    if($_POST['tipofolder']){
       $fiels_request['tipofolder'] = trim($_POST['tipofolder']);
    }else{
        $msgerror[] = utf8_encode("El parametro tipofolder es obligatorio");
        $error = true;
    }
    //********************************************************************
    if($_POST['filename']){
       $fiels_request['filename'] = trim($_POST['filename']);
    }else{
        $msgerror[] = utf8_encode("El parametro filename es obligatorio");
        $error = true;
    }
    //********************************************************************
    if($_POST['tipoimg']){
       $fiels_request['tipoimg'] = trim($_POST['tipoimg']);
    }else{
        $msgerror[] = utf8_encode("El parametro tipoimg es obligatorio");
        $error = true;
    }
    //********************************************************************
    if($_POST['filedata']){
       $fiels_request['filedata'] = ($_POST['filedata']);
    }else{
        $msgerror[] = utf8_encode("El parametro filedata es obligatorio");
        $error = true;
    }
    //********************************************************************
    if($_POST['periodo']){
       $fiels_request['periodo'] = trim($_POST['periodo']);
    }else{
        $msgerror[] = utf8_encode("El parametro periodo es obligatorio");
        $error = true;
    }    
    //********************************************************************
    if($_POST['rootdir']){
        $fiels_request['rootdir'] = trim($_POST['rootdir']);
    }
    //********************************************************************
    if($_POST['pathdest']){
        $fiels_request['pathdest'] = trim($_POST['pathdest']);
    } 
    //********************************************************************
    if($error){
        $response['objectdata'] = null;
        $response['error'] = true;
        $response['message'] = implode(" , ",($msgerror));
        echoResponse(400, $response);
    }
}

function saveFilesTmpFolder(&$fiels_request)
{
    if(!count($_FILES)){ return; }
    //***************************************************************************************
    $dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
    $dirTemp = ParametroPeer::retrieveByPk(65)->getValortexto();
    $regional_id = $fiels_request['regional_id'];
    $regional = RegionalPeer::retrieveByPK($regional_id);
    //***************************************************************************************
    foreach($_FILES as $clave => $file){
      	$entidad_text = trim($regional->getEntidad()->getDirectorioName());
		$entidad_text = $entidad_text ? $entidad_text.DIRECTORY_SEPARATOR : "";
      	$path = $dirRaiz.$entidad_text.$dirTemp.DIRECTORY_SEPARATOR;        
        $directorio = realpath(simad_util::createPath($path));      	        
        //***********************************************************************************
        $file_vars  = pathinfo($file['name']);
        $util_simad = new simad_util();
        $fileName   = $util_simad->clean_name_file($file_vars);
        $tempFile   = $file['tmp_name'];
        $cons       = uniqid();
        //***********************************************************************************
        move_uploaded_file($tempFile,$directorio.DIRECTORY_SEPARATOR.$cons.'_'.$fileName);
        $fiels_request[$clave] = $cons.'_'.$fileName;
    }
}

function getHttpResponseCodeText($code = NULL) {
    if ($code !== NULL) {
        switch ($code) {
            case 100: $text = 'Continue';
                break;
            case 101: $text = 'Switching Protocols';
                break;
            case 200: $text = 'OK';
                break;
            case 201: $text = 'Created';
                break;
            case 202: $text = 'Accepted';
                break;
            case 203: $text = 'Non-Authoritative Information';
                break;
            case 204: $text = 'No Content';
                break;
            case 205: $text = 'Reset Content';
                break;
            case 206: $text = 'Partial Content';
                break;
            case 300: $text = 'Multiple Choices';
                break;
            case 301: $text = 'Moved Permanently';
                break;
            case 302: $text = 'Moved Temporarily';
                break;
            case 303: $text = 'See Other';
                break;
            case 304: $text = 'Not Modified';
                break;
            case 305: $text = 'Use Proxy';
                break;
            case 400: $text = 'Bad Request';
                break;
            case 401: $text = 'Unauthorized';
                break;
            case 402: $text = 'Payment Required';
                break;
            case 403: $text = 'Forbidden';
                break;
            case 404: $text = 'Not Found';
                break;
            case 405: $text = 'Method Not Allowed';
                break;
            case 406: $text = 'Not Acceptable';
                break;
            case 407: $text = 'Proxy Authentication Required';
                break;
            case 408: $text = 'Request Time-out';
                break;
            case 409: $text = 'Conflict';
                break;
            case 410: $text = 'Gone';
                break;
            case 411: $text = 'Length Required';
                break;
            case 412: $text = 'Precondition Failed';
                break;
            case 413: $text = 'Request Entity Too Large';
                break;
            case 414: $text = 'Request-URI Too Large';
                break;
            case 415: $text = 'Unsupported Media Type';
                break;
            case 500: $text = 'Internal Server Error';
                break;
            case 501: $text = 'Not Implemented';
                break;
            case 502: $text = 'Bad Gateway';
                break;
            case 503: $text = 'Service Unavailable';
                break;
            case 504: $text = 'Gateway Time-out';
                break;
            case 505: $text = 'HTTP Version not supported';
                break;
            default:
                exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        $code = $protocol . ' ' . $code . ' ' . $text;        
    } else {
        $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
    }
    return $code;
}

function echoResponse($status_code, $response) {
    header('Content-Type: application/json; charset=UTF-8');
    header(getHttpResponseCodeText($status_code));
    echo json_encode($response);
    exit;
}

?>