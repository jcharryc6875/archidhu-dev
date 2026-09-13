<?php
/**
 * @javier.charry 
 * @copyright 2021
 */
use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Builder as CommandBuilder;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;
use \setasign\Fpdi\Fpdi;

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
class WsFirmaApiGse
{
	const SERVICE_URL_LOGIN_SING = "https://pre-core.firmaya.co/authentication/api/Login";
    const SERVICE_URL_SING = "https://pre-core.firmaya.co/signature/api/sign/pades";
    const SERVICE_URL_TSA = "https://pre-core.firmaya.co/signature/api/sign/stamp";
	const SERVICE_URL_SIGN_HASH = "https://pre-core.firmaya.co/signature/api/sign/hash";

    const SERVICE_USER_SING = "GCC0153Q";
    const SERVICE_PASSW_SING = "PD62E7QBPA";
    const SERVICE_PSK_SING = '|D@,OiPXP1)u2e4Q;6Z9#H|[S';

    const SERVICE_ENABLE_TSA = true;
    const SERVICE_USER_TSA = "NIT_9004904736";
    const SERVICE_PASSW_TSA = "V9m*dG3b";
    //const SERVICE_PASSW_TSA = "8q5aROBkrwNZtzsfxk6r1xuRPsd1rNNyy8SY9O8E4UTA6cmNNE7T2p68yjbgC0gJfRMohqSI/xK1rQ4bgSsGUKMlqXzxirbSkqpFKXnjh04=";
	
    const MAX_FILE_SIZE_MESSAGE = 4499999;//4999999;
    const SIGNCLI = DIRECTORY_SEPARATOR.'SgdeaCliSign'.DIRECTORY_SEPARATOR.'FirmaHashGse.jar';

    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;
    
    public function WsSimadUariv()
    {
        $this->logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."WsFirmaApiGse";
        $this->nulog = 0;
        $this->nudebug = 0;
    }
    
    public function writetolog($msg, $mimetype = "txt")
    {
        if($this->nulog){
            $f = fopen($this->logfile.'.'.$mimetype,"a");
            if($f){ fprintf($f,"\n%s=>%s\t\r",date("Y-m-d G:i:s"),$msg); }
            fclose($f);
            if($this->nudebug){ echo "<pre>".$msg."</pre>"; }
        }
    }
    
    /**
     * Genera la "clave" cifrada del Login FirmaYa (GSE).
     * Formato: Base64[ nonce(12) + salt(16) + ciphertext + tag(16) ]
     *  - JSON compacto: {"nonce":"<epoch ms>","pass":"<contraseña plana>"}
     *  - PBKDF2-SHA256, 10.000 iteraciones, 32 bytes (PSK como texto UTF-8)
     *  - AES-256-GCM, IV aleatorio 12 bytes, tag 16 bytes
     * Validez: ±10 min contra la hora del servidor (UTC).
     */
    private function generarClaveCifradaGse()
    {
        $payload = json_encode(
            array(
                'nonce' => (string) (int) round(microtime(true) * 1000), // epoch en milisegundos
                'pass'  => WsFirmaApiGse::SERVICE_PASSW_SING,
            ),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $nonce = random_bytes(12);
        $salt  = random_bytes(16);
        $key   = hash_pbkdf2('sha256', WsFirmaApiGse::SERVICE_PSK_SING, $salt, 10000, 32, true);

        $tag = '';
        $ciphertext = openssl_encrypt($payload, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);

        if ($ciphertext === false) {
            throw new Exception('Error cifrando clave GSE: ' . openssl_error_string());
        }

        return base64_encode($nonce . $salt . $ciphertext . $tag);
    }

    /**
    * WsFirmaApi::loginWsApiFirmaGse()
    * funcion para consumir servicio de login de la firma de GSE, envia un objeto json
    * @return
    */
    public function loginWsApiFirmaGse()
    {
        try {
            $curl = curl_init(WsFirmaApiGse::SERVICE_URL_LOGIN_SING);
            curl_setopt($curl, CURLOPT_URL, WsFirmaApiGse::SERVICE_URL_LOGIN_SING);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    
            //********************************************************************************************************************
            $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //********************************************************************************************************************
            $data = array(
                'usuario' => WsFirmaApiGse::SERVICE_USER_SING,
                'clave'   => $this->generarClaveCifradaGse(),
            );
            //********************************************************************************************************************
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            //********************************************************************************************************************
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            //********************************************************************************************************************
            $resp = curl_exec($curl);
            curl_close($curl);
            $response_json = json_decode($resp);
			//var_dump($response_json);exit;
            //********************************************************************************
            if (isset($response_json->httpStatus) && trim($response_json->httpStatus) == 201) {
                return $response_json->token;
            } else {
                // CAMBIO 3: dejar rastro del motivo real del rechazo (RS3, RS?, etc.)
                $codigo = isset($response_json->codigoRespuesta) ? $response_json->codigoRespuesta : 'sin respuesta';
                $detalle = isset($response_json->descripcionRespuesta) ? $response_json->descripcionRespuesta : $resp;
                $this->writetolog("Login GSE fallido [" . $codigo . "]: " . $detalle);
                return null;
            }
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage());
            //********************************************************************************
            $this->writetolog($msgerror);
            return null;
        }
    }

    /**
    * WsFirmaApiGse::documentWsApiFirmaGse()
    * funcion para consumir servicio firma de GSE, envia un objeto json con el archivo en base64
    * @return true|false
    */
    public function documentWsApiFirmaGse($numDoc=null,$claveDoc=null,$fullpath=null,$token=null,$target=null,$imagefirma=null,$ubicacionFirma = array())
    {
		$this->writetolog("NumDocFirma => ".$numDoc);
		
        try {
            if(is_file($fullpath)){
                $fbase64 = simad_util::getConvertFileToB64($fullpath);
            }else{
                $fbase64 = $fullpath;
            }
            //********************************************************************************************************************
            $size_message = strlen($fbase64);
            if($size_message >= self::MAX_FILE_SIZE_MESSAGE){
				$filename = md5(date("YmdGis").$numDoc).".pdf";
                $fsign_img = md5(date("YmdGis").$numDoc).".png";
                $dir_target = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp";
                $path_firma = $dir_target.DIRECTORY_SEPARATOR.$fsign_img;
                $target_file = $dir_target.DIRECTORY_SEPARATOR.$filename;
                $firma_visible = false;
                //****************************************************************************************************************
                if(!empty($imagefirma)){
                    $firma_visible = true;
                    if(!file_put_contents($path_firma,base64_decode($imagefirma))){ $firma_visible = false; }
                }
                //****************************************************************************************************************
                $fsign_uri = simad_util::getConvertB64ToFile($fbase64,$target_file);
                if(!$fsign_uri){ return array('error' => true, 'file_base64'=>null, 'msg_info'=>'Error de acceso al archivo para firmar'); }
                //****************************************************************************************************************                
                //$stamp_sign = 
                //****************************************************************************************************************
                $tsa_var = array('aplicatsa'=>WsFirmaApiGse::SERVICE_ENABLE_TSA,'tsauser'=>WsFirmaApiGse::SERVICE_USER_TSA,
                                    'tsapass'=>WsFirmaApiGse::SERVICE_PASSW_TSA);
                //****************************************************************************************************************
                return $this->doSignHashClientCli($numDoc,$claveDoc,$target_file,$target,$token,$firma_visible,$path_firma,implode(",",$ubicacionFirma),$tsa_var);
            }
            //********************************************************************************************************************
            $curl = curl_init(WsFirmaApiGse::SERVICE_URL_SING);
            curl_setopt($curl, CURLOPT_URL, WsFirmaApiGse::SERVICE_URL_SING);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //********************************************************************************************************************
            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer ".$token,
                "Content-Type: application/json",
            );
            //********************************************************************************************************************
            $firmaVisible = $imagefirma != null ? true : false;
            $imagefirma = $imagefirma != null ? $imagefirma : '';
            //$ubicacionFirma = count($ubicacionFirma) > 0 ? $ubicacionFirma : array('x' => 460,'y' => 660,'w' => 120,'h' => 40);
			$ubicacionFirma = count($ubicacionFirma) > 0 ? $ubicacionFirma : array('x' => 0,'y' => 680,'w' => 40,'h' => 100);
            //********************************************************************************************************************
            $postSign = array();
            if(array_key_exists('x',$ubicacionFirma)){ $postSign['x'] = $ubicacionFirma['x']; }else{ $postSign['x'] = 0; }
            if(array_key_exists('y',$ubicacionFirma)){ $postSign['y'] = $ubicacionFirma['y']; }else{ $postSign['y'] = 680; }
            if(array_key_exists('w',$ubicacionFirma)){ $postSign['w'] = $ubicacionFirma['w']; }else{ $postSign['w'] = 40; }
            if(array_key_exists('h',$ubicacionFirma)){ $postSign['h'] = $ubicacionFirma['h']; }else{ $postSign['h'] = 100; }            
            //********************************************************************************************************************
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //********************************************************************************************************************
            $data = array('numeroDocumento' => $numDoc, 'clave' => $claveDoc,
            'ubicacion' => 'Bogota', 'razon' => 'Uariv','conLTV' => false, 'firmaVisible' => $firmaVisible,
            'imagenFirma' => array('x' => $postSign['x'],'y' => $postSign['y'],'ancho' => $postSign['w'],'alto' => $postSign['h'],
            'pagina' => '1','imagen' => $imagefirma), 'base64' => $fbase64);
            //********************************************************************************************************************
            if(WsFirmaApiGse::SERVICE_ENABLE_TSA){
                $data['conEstampa'] = true;
                $data['usuarioTSA'] = WsFirmaApiGse::SERVICE_USER_TSA;
                $data['claveTSA'] = WsFirmaApiGse::SERVICE_PASSW_TSA;
            }else{
                $data['conEstampa'] = false;
                $data['usuarioTSA'] = '';
                $data['claveTSA'] = '';
            }
            //********************************************************************************************************************
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            //********************************************************************************************************************
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            //********************************************************************************************************************
            $resp = curl_exec($curl);
            curl_close($curl);
            $response_json = json_decode($resp);
			//********************************************************************************************************************
			//$logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."WsFirmaApiGse.log";
			//simad_util::writetolog($logfile,'$numDoc =>'. $numDoc . ' codigoRespuesta => ' . $response_json->codigoRespuesta . ' httpStatus=> ' . $response_json->httpStatus . ' message => ' . $response_json->mensaje);
            //********************************************************************************************************************
            if(trim($response_json->httpStatus) == 200){
                return array('error' => false, 'file_base64'=>$response_json->documentoFirmado, 'msg_info'=>$response_json->mensaje);
            }else{
				if($this->nulog){ $this->writetolog(var_dump($response_json)); }
                return array('error' => true, 'file_base64'=>null, 'msg_info'=>sprintf('CodError: %s; Message: %s',$response_json->codigoRespuesta,$response_json->mensaje));
            }
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage());
            //********************************************************************************************************************
            $this->writetolog($msgerror);
            return array('error' => true, 'file_base64'=>null, 'msg_info'=>$msgerror);
        }
    }

	/**
    * WsFirmaApiGse::documentStampSign()
    * funcion para consumir servicio firma de GSE, envia un objeto json con el archivo en base64
    * @return true|false
    */
    public function stampDocumentOnly($fullpath = null)
    {
        try {
            if(!empty($fullpath) && WsFirmaApiGse::SERVICE_ENABLE_TSA){
				$token = $this->loginWsApiFirmaGse();
				//************************************************************************************************************************
				if($token != null){
					$curl = curl_init(WsFirmaApiGse::SERVICE_URL_TSA);
					curl_setopt($curl, CURLOPT_URL, WsFirmaApiGse::SERVICE_URL_TSA);
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//********************************************************************************************************************
					$headers = array(
						"Accept: application/json",
						"Authorization: Bearer ".$token,
						"Content-Type: application/json",
					);
					//********************************************************************************************************************
					if(is_file($fullpath)){
						$fbase64 = simad_util::getConvertFileToB64($fullpath);
					}else{
						$fbase64 = $fullpath;
					}                    
					//********************************************************************************************************************
					$data = array('base64' => $fbase64);
					//********************************************************************************************************************
					if(WsFirmaApiGse::SERVICE_ENABLE_TSA){
						$data['usuario'] = WsFirmaApiGse::SERVICE_USER_TSA;
						$data['clave'] = WsFirmaApiGse::SERVICE_PASSW_TSA;
					}else{
						$data['usuario'] = '';
						$data['clave'] = '';
					}
					//********************************************************************************************************************
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					//********************************************************************************************************************
					$resp = curl_exec($curl);
					curl_close($curl);
					$response_json = json_decode($resp);
					//********************************************************************************************************************
					if(trim($response_json->httpStatus) == 200){
						return array('error' => false, 'file_base64'=>$response_json->base64, 'msg_info'=>$response_json->mensaje);
					}else{
						//if($this->nulog){ $this->writetolog(var_dump($response_json)); }
						return array('error' => true, 'file_base64'=>null, 'msg_info'=>sprintf('CodError: %s; Message: %s',$response_json->codigoRespuesta,$response_json->mensaje));
					}
				}else{
					return array('error' => true, 'msg_info' => 'Error al autenticar con el proveedor de firma digital', 'file_base64' => null);
				}
            }else{
                return array('error' => true, 'msg_info' => 'Debe enviar un documento para estampar o el servicios de TSA esta deshabilitado', 'file_base64' => null);
            }
        } catch (Exception $th) {
            $msgerror = "Error Exception:<br />" . nl2br($th->getMessage());
            //****************************************************************************************************************************
            $this->writetolog($msgerror);
            return array('error' => true, 'file_base64'=>null, 'msg_info'=>$msgerror);
        }
    }

    /**
    * WsFirmaApiGse::addSignVisbleAll()
    * funcion crear los archivos de firma visible para todos los usuarios firmantes del documento
    * @param array $usert_list lista de objetos de usuario que firman el documento
    * @param int $startPage pagina desde donde se debe iniciar a crear la imagen de firma visible
    * @return string archivo en base64
    */
    public function addSignVisbleAll($inputFileName, $users_list = array(),$startPage = 2)
    {
        $file_doc = null;
        //**************************************************************************************************
        try {
            $temp_firma = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.md5(date("YmdGis"));
            $images_firmas = array();
            foreach ($users_list as $eufirma) {
                $firma_info = mb_convert_encoding($eufirma->getNombreApellido(), 'ISO-8859-1');
                $temp_firma .= md5(uniqid().date("YmdGis"));
                $images_firmas[] = simad_util::createImgFirmaDigital($firma_info,$temp_firma,true,90,false);
            }
            //**********************************************************************************************
            $tmpfile_doc = $this->addSignInfoToFile($inputFileName,$images_firmas,true,$startPage);
            if(file_exists($tmpfile_doc)){
                $file_doc = simad_util::getConvertFileToB64($tmpfile_doc);
            }
        } catch (Exception $th) {
            //throw $th;
            $file_doc = null;
        }
        //**************************************************************************************************
        return $file_doc;
    }

    /**
    * WsFirmaApiGse::addSignInfoToFile()
    * funcion para estampar la firma visible en todos las paginas
    * @param string $inputFileName url del documento para firmar
    * @param array $filesAdd lista de rutas a los archivos con la firma de cada usuario
    * @param string $returnFullPath indica si retorna la url absoluta o la relativa del documento final
    * @param int $startPage pagina desde donde se debe iniciar a estampar la imagen de firma visible
    * @param bool $deleteFile indica si se eliminan los archivos temporales creados
    * @return string archivo en base64
    */
    public function addSignInfoToFile($inputFileName, $filesAdd = array(), $returnFullPath = false, $startPage = 1, $deleteFile = false)
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
                //**************************************************************************************
                $ufopen = fopen($inputFileName, 'rb');
                $stream = new \setasign\Fpdi\PdfParser\StreamReader($ufopen, false);
                //**************************************************************************************
                // initiate FPDI
                $pdf = new Fpdi();
                // set the source file
                $pagecount = $pdf->setSourceFile($stream);
                $ptln = 5;$btopXY = 10;$himg = 50;$wimg = 15;
                //***************************************************************************************
                if($pagecount < $startPage){ return $inputFileName; }
                //***************************************************************************************
                for($pageNo = 1; $pageNo <= $pagecount; $pageNo++){
                    if($pageNo >= $startPage){
                        $tplIdx = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($tplIdx);
                        $pdf->AddPage();
                        $pdf->useTemplate($tplIdx, null, null, null, null, true);
                        $topXY = $btopXY;
                        foreach ($filesAdd as $item) {
                            $lx = $size['width']-($size['width']-0.5);
                            $pdf->Image($item,$lx,$topXY,$wimg,$himg,'png');
                            $topXY += 55;
                        }
                    }else{
                        $tplIdx = $pdf->importPage($pageNo);
                        $pdf->AddPage();
                        $pdf->useTemplate($tplIdx, null, null, null, null, true);
                    }
                }
                //***************************************************************************************
                $fsticker_stamp = $pathToSave.DIRECTORY_SEPARATOR.$source_filename.'.pdf';
                $pdf->Output('F',$fsticker_stamp);
                $source_filename = $source_filename.'.pdf';
                //***************************************************************************************
                $source_filename = $returnFullPath ? ($pathToSave.DIRECTORY_SEPARATOR.$source_filename) : ($target_dir.DIRECTORY_SEPARATOR.$source_filename);
            } catch(Exception $e) {
                $msgex = $e->getMessage();
                $source_filename = null;
            }            
        }else{
            $source_filename = null;
        }
		//echo $source_filename;exit;
		//si hay un error al crear el pdf se debe controlar para mostrarle al usuario o tambien para retornar al web service
        //***********************************************************************************************
        return $source_filename;
    }

    public function doSignHashClientCli($nuid, $pcert, $forigen, $fdestino, $token = null, $fvisible = false, $imagenfirma = "",
                        $ubicacionfirma = null, $tsa_options = array(), $tipoDoc = 1)
    {
        require_once(sfConfig::get('sf_lib_dir').'/ShellWrapper/autoload.php');
        //*********************************************************************************************
        try
        {
            $response = null;
            $cli = sfConfig::get('sf_lib_dir').self::SIGNCLI;
            $working_path = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp";
			
            $shell = new Exec();
            $command = new CommandBuilder('java');            
            $command->addSubCommand('-jar');
            $command->addSubCommand($cli);

            $command->addArgument('wslogin', self::SERVICE_USER_SING);
            $command->addArgument('wspass', self::SERVICE_PASSW_SING);
            $command->addArgument('tipodoc', $tipoDoc);
            $command->addArgument('nuid', $nuid);
            $command->addArgument('pcert', $pcert);
            $command->addArgument('forigen', $forigen);
            $command->addArgument('fdestino', $fdestino);
            $command->addArgument('token', $token);
			$command->addArgument('workingpath', $working_path);

            if($fvisible){
                if(!empty($imagenfirma)){
                    $command->addArgument('fvisible', "true");
                    $command->addArgument('imagenfirma', $imagenfirma);
                    if(!empty($ubicacionfirma)){ $command->addArgument('ubicacionfirma', $ubicacionfirma); }
                }else{
                    $command->addArgument('fvisible', "false");
                }
            }else{
                $command->addArgument('fvisible', "false");
            }

            if($tsa_options['aplicatsa']){
                if(!empty($tsa_options['tsauser']) && !empty($tsa_options['tsapass'])){
                    $command->addArgument('aplicatsa', "true");
                    $command->addArgument('tsauser', $tsa_options['tsauser']);
                    $command->addArgument('tsapass', $tsa_options['tsapass']);
                }else{
                    $command->addArgument('aplicatsa', "false");
                }
            }else{
                $command->addArgument('aplicatsa', "false");
            }

            $cmd = (string) $command;
			
			if($this->nulog){
				$log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.date("Ymd").'_firmahash.log';
				simad_util::writetolog($log_dir,$cmd);
			}
			
            $shell->run($command);            
            $response = $shell->getOutput();

            if(!empty($response)){
                $response_json = json_decode(utf8_decode($response[0]));
                if(!is_null($response_json)){
                    if(trim($response_json->httpStatus) == 200){
                        $file_base64 = simad_util::getConvertFileToB64($fdestino);
                        return array('error' => false, 'file_base64'=>$file_base64, 'msg_info'=>$response_json->mensaje);
                    }else{
                        if($this->nulog){ $this->writetolog(var_dump($response_json)); }
                        return array('error' => true, 'file_base64'=>null, 'msg_info'=>sprintf('CodError: %s; Message: %s',$response_json->codigoRespuesta,$response_json->mensaje));
                    }
                }else{
                    return array('error' => true, 'file_base64'=>null, 'msg_info'=>'Ocurrio un error al decodificar el mensaje del servicio de firmado digital => '.var_dump($response));
                }
            }else{
                return array('error' => true, 'file_base64'=>null, 'msg_info'=>'Ocurrio un error interno en el servidor, error de integraci�n con el servicio de firma digital');
            }

            return $response;
        }catch (Exception $ex) {            
            return array('error' => true, 'file_base64'=>null, 'msg_info'=>$ex->getMessage());
        }
    }
}

class WsFirmaApiAndes
{
	const SERVICE_ENABLE_WS = true;
    const SERVICE_USER_SIGN = 'DiPa';
    const SERVICE_PASSW_SIGN = '7npA2SfVge';
	const SERVICE_TIPO_DOC = 1;
    
    const SERVICE_WS_URL = "https://ra.andesscd.com.co/test/WebService/wsdl.php?WSDL";
    const SERVICE_WS_URI = "https://ra.andesscd.com.co/";

    const SERVICE_ENABLE_TSA = false;
    const SERVICE_USER_TSA = "UNIDADVICTIMASSPN";
    const SERVICE_PASSW_TSA = "xxxx1";
	const SERVICE_URL_TSA = "https://tsa.andesscd.com.co";
	
    const CONTEXT_SOAP2 = array('http' => array( 'user_agent' => 'PHPSoapClient'),'ssl' => array('verify_peer' => false,'verify_peer_name' => false, 'allow_self_signed' => true));
	const SIGNCLI = 'AndesSCDFirmador.jar';
    const MAX_FILE_SIZE_MESSAGE = 4499999;//4499999;
    const FIRMA_VISIBLE_IMAGE = "firmadigitalmarcaagua.png";
	
    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;
	var $java_cli = "";
	var $jar_cli = "";
	var $java = "C:\Java\openjdk-8u382\bin\java.exe";
	//var $java = "C:\Java\jdk-1.8\bin\java.exe";
    
    public function WsFirmaApiAndes()
    {
        $this->logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."WsFirmaApiAndes.log";
        $this->nulog = 1;
        $this->nudebug = 0;
		$this->jar_cli = sfConfig::get("sf_lib_dir").DIRECTORY_SEPARATOR.'efirma'.DIRECTORY_SEPARATOR.self::SIGNCLI;
    }   
	
	/**
    * WsFirmaApiAndes::documentWsApiFirmaAndes()
    * funcion para consumir servicio firma de ANDES SCD, usa componente en linea de comandos AndesFirmador.jar
    * @return true|false
    */
    public function documentWsApiFirmaAndes($numDoc=null,$claveDoc=null,$filesource=null,$fileout=null,$imagefirma=null,$ubicacionFirma = array())
    {
        $tracker = new SgdeaTrackerMetrics([
            'documento_id' => basename($filesource),
            'usuario_id' => $numDoc,
            'transaccion_id' => uniqid('firma_', true),
        ]);
        //********************************************************************************************************************
        if(is_file($filesource)){
            $fbase64 = filesize($filesource);
        }else{
            $fbase64 = $filesource;
        }
        //********************************************************************************************************************
        $tracker->mark('inicio');
        $size_message = strlen($fbase64);
        if($size_message >= self::MAX_FILE_SIZE_MESSAGE){
            $tracker->mark('antes_firma');
            $resp_firma = $this->docFirmarByCompApiAndes($numDoc,$claveDoc,$filesource,$fileout,$imagefirma,$ubicacionFirma);
            //****************************************************************************************************************
            $tracker->mark('despues_firma', [
                'status_code' => $resp_firma ?? null,
                'duration_ms' => ($tracker->steps['despues_firma']['elapsed'] - $tracker->steps['antes_firma']['elapsed']) * 1000,
            ]);
        }else{
            $tracker->mark('antes_firma');
            $firma_visible = !empty($imagefirma) ? true : false;
            //$firma_visible = true;
            //return $this->docFirmarByCompApiAndes($numDoc,$claveDoc,$filesource,$fileout,$imagefirma,$ubicacionFirma);
            $resp_firma = $this->docFirmaWsSoapApiAndes($numDoc,$claveDoc,$filesource,$fileout,$firma_visible,$imagefirma,$ubicacionFirma);
            //*****************************************************************************************************************
            $tracker->mark('despues_firma', [
                'status_code' => $resp_firma ?? null,
                'duration_ms' => ($tracker->steps['despues_firma']['elapsed'] - $tracker->steps['antes_firma']['elapsed']) * 1000,
            ]);
        }
        //*********************************************************************************************************************
        $resp_firma ? $tracker->mark('exito') : $tracker->mark('error');
        $tracker->finish();
        //*********************************************************************************************************************
        return $resp_firma;
    }
	
    /**
    * WsFirmaApiAndes::docFirmarByCompApiAndes()
    * funcion para consumir servicio firma de ANDES SCD, usa componente en linea de comandos AndesFirmador.jar
    * @return true|false
    */
    public function docFirmarByCompApiAndes($numDoc = null, $claveDoc = null,$filesource = null,$fileout = null,$imagefirma = null,$ubicacionFirma = array())
    {
        if($this->nulog){ simad_util::writetolog($this->logfile,"CompApiAndes => NumDocFirma => ".$numDoc); }
		//************************************************************************************************************************
		$isTest = false;
		$test_svc = $isTest ? " --test true" : "";
		$firma_visible = false;
		//************************************************************************************************************************
        try {
			$filename = md5(date("YmdGis").$numDoc).".pdf";
			$fsign_img = md5(date("YmdGis").$numDoc).".png";
			$dir_target = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp";
			$path_firma = $dir_target.DIRECTORY_SEPARATOR.$fsign_img;
			$target_file = $dir_target.DIRECTORY_SEPARATOR.$filename;
			//********************************************************************************************************************
			$loginwsdl = self::SERVICE_USER_SIGN;
			$passwordwsdl = self::SERVICE_PASSW_SIGN;
			$tipodoc = self::SERVICE_TIPO_DOC;
			$applytsa = self::SERVICE_ENABLE_TSA;
			//********************************************************************************************************************
			if(file_exists($imagefirma)){
				$firma_visible = true;
			}
			//********************************************************************************************************************
			if(!file_exists($filesource)){ return array('error' => true, 'filesing'=>null, 'msg_info'=>'Error de acceso al archivo para firmar'); }
			//********************************************************************************************************************
			$command = "$this->java -Xms1024m -Xmx2048m -jar $this->jar_cli --metodofirma ws --formatofirma pdf --login $loginwsdl --password $passwordwsdl --tipodocumento $tipodoc --documento $numDoc --pinfirma $claveDoc --entrada $filesource --formatoentrada archivo --salida $fileout --formatosalida archivo";
			//********************************************************************************************************************
			if($applytsa){
				$tsausuario = self::SERVICE_USER_TSA;
				$tsapass = self::SERVICE_PASSW_TSA;
				$tsaurl = self::SERVICE_URL_TSA;
				$command .= " --aplicatsa true --tsausuario $tsausuario --tsapass $tsapass --tsaurl $tsaurl";
			}
			//********************************************************************************************************************
			$command .= "$test_svc";
			//********************************************************************************************************************
			if($firma_visible){
				//--ubicacion 400,700,170,60
				//$pagina = "--pagina 5000";
				$command .= $firma_visible ? " --visible true --imagenFirma $imagefirma --pagina 5000 --tamanofuentefirma 5 --ubicacion ".implode(",", $ubicacionFirma) : "";
			}else{
				$command .= " --visible false";
			}
			//********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$command); }
			//********************************************************************************************************************
			$response_firma = shell_exec($command);
			
			//$logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."firmashell.log";
			//simad_util::writetolog($logfile,$response_firma);
			
			/*preg_match('/ {" (.*?)"}/is', $response_firma, $coincidencias);
			
			$logfile = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR."FileWordPdfMessage.log";
			simad_util::writetolog($logfile,implode(";",$coincidencias));*/
						
			$isError = false;$file_sign = null;
			if(!empty($response_firma)){
				$firma_data = json_decode($response_firma);
				if($firma_data->estado == 0){
					$message = $firma_data->mensaje;
					$file_sign = file_exists($fileout) ? $fileout : null;
				}else{
					$isError = true;
					$message = sprintf("CodigoResp => %s; MessageResp => %s",$firma_data->estado,$firma_data->mensaje);
				}
			}else{
				$isError = true;
				$message = "Error proveedor de firma digital";
			}
			//********************************************************************************************************************
			return array('error' => $isError, 'filesing'=>$file_sign, 'msg_info'=>$message);
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage());
            //********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$msgerror); }
            return array('error' => true, 'filesing'=>null, 'msg_info'=>$msgerror);
        }
    }

    /**
    * WsFirmaApiAndes::validateFirmaDocSoapApiAndes()
    * funcion para consumir servicio firma de ANDES SCD, permite validar la firma de un documento
    * @param string $filesource ruta al archivo en repositorio del SGDEA
    * @param string $typyFile formato del archivo a validar docx|pdf|p7
    * @return mixed array('error' => true|false,'estado' => 0|N,'mensaje'=>'mensaje')
    * Si el proceso es exitoso se retorna 0, de lo contrario se retorna un código para identificar el tipo de error que se genera
    */
    public function validateFirmaDocSoapApiAndes($filesource=null, $typyFile="pdf")
    {
        $options = array(
            "uri"=> WsFirmaApiAndes::SERVICE_WS_URI,
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
            'stream_context' => stream_context_create(WsFirmaApiAndes::CONTEXT_SOAP2),
        );
        //************************************************************************************
		if($this->nulog){ simad_util::writetolog($this->logfile,"SoapApiAndes => VerificarFirma => formato del archivo ".$typyFile); }
		//************************************************************************************************************************
        $client = new SoapClient(WsFirmaApiAndes::SERVICE_WS_URL,$options);
        //$functions = $client->__getFunctions();
        $client->__setSoapHeaders($this->soapClientWSSecurityHeader(WsFirmaApiAndes::SERVICE_USER_SIGN,WsFirmaApiAndes::SERVICE_PASSW_SIGN));
        //************************************************************************************************************************
        try {
            if(!file_exists($filesource)){
                return array('error' => true, 'estado' => '-1', 'msg_info'=>'El archivo para verificar la firma no existe o es ilegible');
            }
            //********************************************************************************************************************
            $archivo_b64 = simad_util::getConvertFileToB64($filesource);
            $format_file = pathinfo($filesource,PATHINFO_EXTENSION);
            //********************************************************************************************************************
            $params_api = array('archivo' => $archivo_b64,'tipoArchivo' => $format_file);
            //********************************************************************************************************************
            $response = $client->VerificarFirma($params_api);
            //********************************************************************************************************************
            $resp_vfirma = new stdClass();
            $resp_vfirma = (object)$response;
            //********************************************************************************************************************
            if($resp_vfirma->estado == 0){
                /*if(file_exists($fileout)){ unlink($fileout); }
                if(simad_util::getConvertB64ToFile($resp_firma->mensaje,$fileout)){                    
                    return array('error' => false, 'filesing'=>$fileout, 'msg_info'=>'El archivo fue firmado exitosamente');                  
                }else{
                    return array('error' => true, 'filesing'=>null, 'msg_info'=>'Ocurrio un error interno en el SGDEA, intente de nuevo o comuniquese con el administrador');
                }*/
            }else{
                return array('error' => true, 'filesing'=>null, 'msg_info'=>$resp_vfirma->mensaje);
            }            
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring);
			//********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$msgerror); }
            return array('error'=>true,'filesing'=>null, 'msg_info'=>'Ocurrio un error al enviar la informaci&oacute;n, error de protocolo soap => ' . $msgerror);
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage());
            //********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$msgerror); }
            return array('error' => true, 'filesing'=>null, 'msg_info'=>$msgerror);
        }
    }
    
    /**
    * WsFirmaApiAndes::docFirmaWsSoapApiAndes()
    * funcion para consumir servicio firma de ANDES SCD, usa el servicio web publicado por andes
    * @return true|false
    */
    public function docFirmaWsSoapApiAndes($numDoc = null, $claveDoc = null,$filesource = null,$fileout = null,$firma_visible = false,$imagefirma = null,$ubicacionFirma = array())
    {
        $options = array(
            "uri"=> WsFirmaApiAndes::SERVICE_WS_URI,
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
            'stream_context' => stream_context_create(WsFirmaApiAndes::CONTEXT_SOAP2),
        );
        //************************************************************************************
		if($this->nulog){ simad_util::writetolog($this->logfile,"SoapApiAndes => NumDocFirma => ".$numDoc); }
		//************************************************************************************************************************
        $client = new SoapClient(WsFirmaApiAndes::SERVICE_WS_URL,$options);
        //$functions = $client->__getFunctions();
        $client->__setSoapHeaders($this->soapClientWSSecurityHeader(WsFirmaApiAndes::SERVICE_USER_SIGN,WsFirmaApiAndes::SERVICE_PASSW_SIGN));
        //************************************************************************************************************************
        try {
            if(!file_exists($filesource)){
                return array('error' => true, 'filesing'=>null, 'msg_info'=>'El archivo para firmar no existe o esta dañado');
            }
            //********************************************************************************************************************
            $archivo_b64 = simad_util::getConvertFileToB64($filesource);
            //********************************************************************************************************************
            $params_api = array('tipoDoc' =>WsFirmaApiAndes::SERVICE_TIPO_DOC, 'documento' => $numDoc,'pin' => $claveDoc,'archivo' => $archivo_b64);
            //********************************************************************************************************************
            $params_api['firmaVisible'] = 'false';
            if($firma_visible){
                //--ubicacion 400,700,170,60
                $ubicacionFirma = count($ubicacionFirma) ? $ubicacionFirma : array(5,640,40,120);
                $params_api['firmaVisible'] = 'true';
                $params_api['ubicacionImagen'] = implode(",", $ubicacionFirma);
                //$params_api['tamanoFuenteFirma'] = 5;
                //$params_api['imagen'] = simad_util::getConvertFileToB64($imagefirma);
                //$params_api['pagina'] = '1';
            }
            //********************************************************************************************************************
            $params_api['firmarEstampa'] = 'true';
            if(self::SERVICE_ENABLE_TSA){
                $params_api['firmarEstampa'] = 'true';
                $params_api['loginTSA'] = self::SERVICE_USER_TSA;
                $params_api['passswordTSA'] = self::SERVICE_PASSW_TSA;
            }else{
                $params_api['firmarEstampa'] = 'false';
            }
            //********************************************************************************************************************
            $response = $client->Firmar($params_api);
            //********************************************************************************************************************
            $resp_firma = new stdClass();
            $resp_firma = (object)$response;
            //********************************************************************************************************************
            if($resp_firma->estado == 0){
                if(file_exists($fileout)){ unlink($fileout); }
                if(simad_util::getConvertB64ToFile($resp_firma->mensaje,$fileout)){                    
                    return array('error' => false, 'filesing'=>$fileout, 'msg_info'=>'El archivo fue firmado exitosamente');                  
                }else{
                    return array('error' => true, 'filesing'=>null, 'msg_info'=>'Ocurrio un error interno en el SGDEA, intente de nuevo o comuniquese con el administrador');
                }
            }else{
                return array('error' => true, 'filesing'=>null, 'msg_info'=>$resp_firma->mensaje);
            }            
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />  NumDocFirma => '.$numDoc;
			//********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$msgerror); }
            return array('error'=>true,'filesing'=>null, 'msg_info'=>'Ocurrio un error al enviar la informaci&oacute;n, error de protocolo soap => ' . $msgerror);
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . "  NumDocFirma => " . $numDoc;
            //********************************************************************************************************************
			if($this->nulog){ simad_util::writetolog($this->logfile,$msgerror); }
            return array('error' => true, 'filesing'=>null, 'msg_info'=>$msgerror);
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

        // Compiling WSS string
        $passdigest = base64_encode(sha1($simple_nonce . $tm_created . $password, true));

        // Initializing namespaces
        $ns_wsse = 'https://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $ns_wsu = 'https://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
        $password_type = 'https://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
        $encoding_type = 'https://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

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
        $usernameToken->addChild('wsse:Password', $passdigest, $ns_wsse)->addAttribute('Type', $password_type);
        $usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse)->addAttribute('EncodingType', $encoding_type);
        $usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);

        // Recovering XML value from that object
        $root->registerXPathNamespace('wsse', $ns_wsse);
        $full = $root->xpath('/root/wsse:Security');
        $auth = $full[0]->asXML();

        return new SoapHeader($ns_wsse, 'Security', new SoapVar($auth, XSD_ANYXML), true);
    }

	/**
    * WsFirmaApiAndes::stampDocumentOnly()
    * Adiciona la estampa de tiempo en un documento
    * @return mixed array('error' => true|false, 'file_base64'=>archivo en base64 o null, 'msg_info'=>mensaje del proceso realizado o mensaje de error)
    */
    public function stampDocumentOnly($fullpath = null)
    {
        try {
            if(!empty($fullpath)){
				if(WsFirmaApiAndes::SERVICE_ENABLE_TSA){
					if(!file_exists($fullpath)){
						return array('error' => true, 'file_base64'=>null, 'msg_info'=>'El archivo para estampar no existe');
					}                    
					//********************************************************************************************************************
					$applytsa = WsFirmaApiAndes::SERVICE_ENABLE_TSA;
                    $isError = "";
					//********************************************************************************************************************
					$file_vars = pathinfo($fullpath);
					$tmpfdir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
					$fileout = $tmpfdir.DIRECTORY_SEPARATOR.md5(date("YmdGis").uniqid()).'.'.$file_vars['extension'];
					//********************************************************************************************************************
					//-Xms3g -Xmx3g
					$command = "$this->java -Xms512m -Xmx512m -jar $this->jar_cli --metodofirma estampa --formatofirma pdf --entrada $fullpath --salida $fileout --formatoentrada archivo --formatosalida archivo";
					$command .= " --visible false";
					//********************************************************************************************************************
					if($applytsa){
						$tsausuario = WsFirmaApiAndes::SERVICE_USER_TSA;
						$tsapass = WsFirmaApiAndes::SERVICE_PASSW_TSA;
						$tsaurl = WsFirmaApiAndes::SERVICE_URL_TSA;
						$command .= " --aplicatsa true --tsausuario $tsausuario --tsapass $tsapass --tsaurl $tsaurl";
					}
					//********************************************************************************************************************
					if($this->nulog){ simad_util::writetolog($this->logfile,$command); }
					//********************************************************************************************************************
					$response_tsa = shell_exec($command);
					$isError = false;$file_sign = null;
					if(!empty($response_tsa)){
						$firma_data = json_decode($response_tsa);
						if($firma_data->estado == 0){
							$message = $firma_data->mensaje;
							$file_sign = file_exists($fileout) ? simad_util::getConvertFileToB64($fileout) : null;
						}else{
							$isError = true;
							$message = sprintf("CodigoResp => %s; MessageResp => %s",$firma_data->estado,$firma_data->mensaje);
						}
					}else{
						$isError = true;
						$message = "Error proveedor de firma digital";
					}
					//********************************************************************************************************************
					return array('error' => $isError, 'file_base64'=>$file_sign, 'msg_info'=>$message);
				}else{
					return array('error' => true, 'msg_info' => 'El servicio de TSA esta deshabilitado', 'file_base64' => null);
				}
            }else{
                return array('error' => true, 'msg_info' => 'Debe enviar un documento para estampar o el servicios de TSA esta deshabilitado', 'file_base64' => null);
            }
        } catch (Exception $th) {
            $msgerror = "Error Exception:<br />" . nl2br($th->getMessage());
            //****************************************************************************************************************************
            return array('error' => true, 'file_base64'=>null, 'msg_info'=>$msgerror);
        }
    }
}
?>