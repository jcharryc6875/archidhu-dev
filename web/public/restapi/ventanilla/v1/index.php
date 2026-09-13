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
    if(!in_array('oficina_virtual',$uri)){
        header("HTTP/1.1 404 Not Found");
        exit();
    }
}
//***************************************************************************************
$requestMethod = $_SERVER["REQUEST_METHOD"];
if(!in_array($requestMethod,array("POST","PUT","GET"))){
    $response['object'] = null;
    $response['error'] = true;
    $response['message'] = "El metodo no esta habilitado";
    echoResponse(405, $response);
}
//***************************************************************************************
$dataid = array_pop($uri);
$dataid = isset($_GET['q1']) ? $_GET['q1'] : "";
//***************************************************************************************
if((in_array("regionales",$uri) || ($dataid == "regionales")) && $requestMethod == "GET"){
    $json = RegionalPeer::getRegionalJson();
    //var_dump($json);echo "hola...";exit;
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("peticionario",$uri) || ($dataid == "peticionario")) && $requestMethod == "GET"){
    $json = TipoPeticionarioPeer::getTipoPeticionarioJson();
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("tpeticion",$uri) || ($dataid == "tpeticion")) && $requestMethod == "GET"){
    $json = TipoComRecibidaPeer::getTipoComRecibidaJson();
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("tidentificacion",$uri) || ($dataid == "tidentificacion")) && $requestMethod == "GET"){
    $json = TipoIdentificacionPeer::getTipoIdentificacionJson();
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("paises",$uri) || ($dataid == "paises")) && $requestMethod == "GET"){
    $json = PaisPeer::getPaisesJson();
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("departamentos",$uri) || ($dataid == "departamentos")) && $requestMethod == "GET"){
    $item = isset($_GET['item']) ? $_GET['item'] : -1;
    $json = DepartamentoPeer::getDepartamentosJson($item);
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("ciudades",$uri) || ($dataid == "ciudades")) && $requestMethod == "GET"){
    $item = isset($_GET['item']) ? $_GET['item'] : -1;
    $json = CiudadPeer::getCiudadesJson($item);
    $response['object'] = $json;
    $response['error'] = false;
    $response['message'] = utf8_encode('Consulta realizada sin errores');
    echoResponse(200, $response);
}

if((in_array("radicar",$uri) || ($dataid == "radicar")) && $requestMethod == "POST"){
    //***************************************************************************************
    //validateToken();
    $vars_request = array();
    initObjectByRequest($vars_request);
    initDefaultFields($vars_request);
    saveFilesTmpFolder($vars_request);
    //***************************************************************************************
    $comlist_interesados = $vars_request['currentlist_interesados'];
    foreach ($vars_request['newaddlist_interesados'] as $newitem) {
        $interesado_id = InteresadosPeer::addNewInteresado($newitem);
        if($interesado_id == null){
            $response['object'] = null;
            $response['error'] = true;
            $response['message'] = "OcurOcurrio un error al crear el interesado en el SGDEA";
            echoResponse(500, $response);
        }else{
            $comlist_interesados[] = $interesado_id;
        }
    }
    //***************************************************************************************
    $com_recibida = ComRecibidaPeer::addComRecibida($vars_request,true);
    //***************************************************************************************
    foreach ($comlist_interesados as $item) {
        ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(),$item);
    }
    //***************************************************************************************
    $data_intern['numero_radicacion'] = $com_recibida->getNumeroRadicacion();
    $data_intern['radicado'] = $com_recibida->getRadicado();
    $data_intern['asunto_com'] = mb_convert_encoding(trim($com_recibida->getAsunto()), 'UTF-8');
    $data_intern['fecha_creacion'] = $com_recibida->getFechaCreacion();            
    $data_intern['fecha_respuesta'] = $com_recibida->getFechaMaximaRespuesta();
    //***************************************************************************************
    if(!count($data_intern)){
        $response['object'] = null;
        $response['error'] = true;
        $response['message'] = "Ocurrio un error al radicar la comunicación";
        echoResponse(500, $response);
    }
    //***************************************************************************************
    $response['object'] = $data_intern;
    $response['error'] = false;
    $response['message'] = mb_convert_encoding('La comunicación se radico con exito', 'UTF-8');
    echoResponse(200, $response);
}

function validateToken() {
    //echo $_POST['uidtoken'] == $_SESSION['uidtoken'] ? "SI " : "NO ";
    if ((isset($_SESSION['uidtoken']) && $_POST['uidtoken']) == trim($_SESSION['uidtoken'])){
        echo "ERROR";
        $response['object'] = null;
        $response['error'] = true;
        $response['message'] = "El metodo no esta habilitado";
        echoResponse(405, $response);
    }
    echo isset($_SESSION['uidtoken']). " VALID";
    EXIT;
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

function initDefaultFields(&$fiels_request)
{
    //$dependencia_codigo = "162";
    //$dependenica_default = DependenciaPeer::getDependenciaByCodigo($dependencia_codigo);
    //$dependencia_id = $dependenica_default->getPrimaryKey();
    $tipocomrecibida_id = isset($fiels_request['tipocomrecibida_id']) ? $fiels_request['tipocomrecibida_id'] : null;
    $modulo_id = 3;
    //**********************************************************************************************************
    $cusuario_destino = ReceptorComunicacionesPeer::getUserDestinoAutomatic($tipocomrecibida_id,null,$modulo_id);    
    //**********************************************************************************************************
    $fiels_request['estadodigitalizacion_id'] = 1;
    $fiels_request['estadocomrecibida_id'] = 1;
    $fiels_request['formarecepcion_id'] = FormaRecepcionPeer::getFormaRecepcionDefault();
    $fiels_request['ciudad_id'] = RegionalPeer::getCiudadIdByRegional($fiels_request['regional_id']);    
    $fiels_request['asuntorecibida_id'] = AsuntoRecibidaPeer::getAsuntoRecibidaDefault();    
    $fiels_request['usuario_destino'] = $cusuario_destino->getUsuarioId();
    $fiels_request['cargo_udestino'] = $cusuario_destino->getPrimaryKey();
    $fiels_request['usuario_radicador'] = 1;//validar controlar por tipo com recibida, crear tabla asignar usuario por tipo o usar tabla receptor
    $fiels_request['dependencia_id'] = DependenciaPeer::getDependenciaIdByUser($fiels_request['usuario_destino']);
    $fiels_request['directorioexterno_id'] = DirectorioExternoPeer::addNewDirectorioExterno($fiels_request,true);
    //**********************************************************************************************************
    $comlist_interesados = array();$laddnew_interesados = array();
    $ciudad = CiudadPeer::retrieveByPK($fiels_request['nciudad_id']);
    //**********************************************************************************************************
    $info_list['PRIMER_NOMBRE'] = trim($fiels_request['primer_nombre']);
    $info_list['SEGUNDO_NOMBRE'] = trim($fiels_request['segundo_nombre']);
    $info_list['PRIMER_APELLIDO'] = trim($fiels_request['primer_apellido']);
    $info_list['SEGUNDO_APELLIDO'] = trim($fiels_request['segundo_apellido']);
    $info_list['TIPO_IDENTIFICACION'] = $fiels_request['tipoidentificacion_id'];
    $info_list['TIPO_GENERO'] = null;
    $info_list['CIUDAD_ID'] = $fiels_request['nciudad_id'];
    $info_list['CIUDAD_CODIGO'] = $ciudad->getCodigoDane();
    $info_list['NUMERO_IDENTIFICACION'] = trim($fiels_request['numero_identificacion']);
    $info_list['DIRECCION'] = trim($fiels_request['direccion']);
    $info_list['TELEFONO'] = trim($fiels_request['telefono']);
    $info_list['CELULAR'] = trim($fiels_request['celular']);
    $info_list['EMAIL'] = trim($fiels_request['email']);
    $info_list['USUARIO_ID'] = $cusuario_destino->getUsuarioId();
    //**********************************************************************************************************
    $ciudad = CiudadPeer::retrieveByPK($fiels_request['nciudad_id']);
    //**********************************************************************************************************
    if(simad_util::array_check($info_list,'PRIMER_NOMBRE') && simad_util::array_check($info_list,'PRIMER_APELLIDO') && simad_util::array_check($info_list,'NUMERO_IDENTIFICACION')){
        if(!trim($info_list['NUMERO_IDENTIFICACION'])){
            $response['object'] = null;
            $response['error'] = true;
            $response['message'] = "El numero de identificación del interesado no es valido";
            echoResponse(400, $response);
        }
        //*******************************************************************************************************
        $interesado_id = InteresadosPeer::existsIntByNameAndNuid($info_list['PRIMER_NOMBRE'],$info_list['PRIMER_APELLIDO'],$info_list['NUMERO_IDENTIFICACION']);
        if($interesado_id != null){
            $comlist_interesados[] = $interesado_id;
        }elseif(simad_util::array_check($info_list,'NUMERO_IDENTIFICACION')){
            $tipouid_id = TipoIdentificacionPeer::getTipoIdentificacionPkById(trim($info_list['TIPO_IDENTIFICACION']));
            if($tipouid_id == null){
                $response['object'] = null;
                $response['error'] = true;
                $response['message'] = "El tipo de identificación del interesado no es un valor valido";
                echoResponse(400, $response);
            }
            //***************************************************************************************************
            $fiels_request['TIPO_IDENTIFICACION'] = $tipouid_id;
            $isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
            if($isValidIntInfo['IsValid'] == true){
                $laddnew_interesados[] = $info_list;
            }else{
                $response['object'] = null;
                $response['error'] = true;
                $response['message'] = $isValidIntInfo['MsgError'];
                echoResponse(400, $response);
                exit;
            }
        }else{
            $fiels_request['TIPO_IDENTIFICACION'] = 5;
            $isValidIntInfo = InteresadosPeer::validateInfoNewInteresado($info_list);
            if($isValidIntInfo['IsValid'] == true){
                $laddnew_interesados[] = $info_list;
            }else{
                $response['object'] = null;
                $response['error'] = true;
                $response['message'] = $isValidIntInfo['MsgError'];
                exit;
            }
        }
    }else{
        $response['object'] = null;
        $response['error'] = true;
        $response['message'] = "El nombre o numero de identificación del interesado no es valido";
        echoResponse(400, $response);
        exit;
    }
    //**********************************************************************************************************
    $fiels_request['currentlist_interesados'] = $comlist_interesados;
    $fiels_request['newaddlist_interesados'] = $laddnew_interesados;
}

function initObjectByRequest(&$fiels_request)
{
    if(!count($_POST)){
        $response['object'] = null;
        $response['error'] = true;
        $response['message'] = "Ningun parametro recibido";
        echoResponse(400, $response);
    }
    //**********************************************************************************
    if($_POST['nregional_id']){
       $fiels_request['regional_id'] =  $_POST['nregional_id'];
    }
    if($_POST['ntipopeticionario_id']){
       $fiels_request['tipopeticionario_id'] = $_POST['ntipopeticionario_id'];
    }
    if($_POST['ntipopeticion_id']){
       $fiels_request['tipocomrecibida_id'] = $_POST['ntipopeticion_id'];
    }
    if($_POST['ntipoidentificacion_id']){
       $fiels_request['tipoidentificacion_id'] = $_POST['ntipoidentificacion_id'];
    }
    if($_POST['num_identificacion']){
       $fiels_request['numero_identificacion'] = trim($_POST['num_identificacion']);
    }
    if($_POST['primer_nombre']){
       $fiels_request['primer_nombre'] = trim($_POST['primer_nombre']);
    }
    if($_POST['segundo_nombre']){
       $fiels_request['segundo_nombre'] = trim($_POST['segundo_nombre']);
    }
    if($_POST['primer_apellido']){
       $fiels_request['primer_apellido'] = trim($_POST['primer_apellido']);
    }
    if($_POST['segundo_apellido']){
       $fiels_request['segundo_apellido'] = trim($_POST['segundo_apellido']);
    }
    if($_POST['npais_id']){
       $fiels_request['pais_id'] = $_POST['npais_id'];
    }
    if($_POST['ndepartamento_id']){
       $fiels_request['departamento_id'] = $_POST['ndepartamento_id'];
    }
    if($_POST['nciudad_id']){
       $fiels_request['nciudad_id'] = trim($_POST['nciudad_id']);
    }
    if($_POST['email']){
       $fiels_request['email'] = trim($_POST['email']);
    }
    if($_POST['ndireccion']){
       $fiels_request['direccion'] = trim($_POST['ndireccion']);
    }
    if($_POST['ntelefono']){
       $fiels_request['telefono'] = trim($_POST['ntelefono']);
    }
    if($_POST['nasunto']){
       $fiels_request['asunto'] = trim($_POST['nasunto']);
    }
    if($_POST['nobservaciones']){
       $fiels_request['observaciones'] = trim($_POST['nobservaciones']);
    }
    if($_POST['nobservaciones']){
       $fiels_request['observaciones'] = trim($_POST['nobservaciones']);
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

/*function createComRecibidaRequest1()
{
    echo var_dump($_POST);EXIT;
    //if($_FILES)
    $rawData  = file_get_contents('php://input');
    var_dump($rawData);
    echo $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));exit;
    $input = getParamsByHeaderType();
    var_dump($input);exit;
    $this->personGateway->insert($input);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = null;
    return $response;
}*/

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