<?php
/**
 * @javier.charry 
 * @copyright 2021
 */

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Builder as CommandBuilder;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************

class WsSimadUariv
{
	const MAX_FILE_SIZE_MESSAGE = 18999999;//19MB
	//const SERVICE_URL = "http://serviciospruebas.unidadvictimas.gov.co/SIV.IntegradorGD/FachadaGD.svc?singleWsdl";
    const SERVICE_URL = "http://serviciospruebas.unidadvictimas.gov.co/SIV.IntegradorGD/FachadaGD.svc?wsdl";
    const SERVICE_URI = "http://serviciospruebas.unidadvictimas.gov.co/SIV.IntegradorGD/";

    const SERVICE_USER = "usrGestorUARIV";
    const SERVICE_PASSW = "csgduplaialv16";
    const SERVICE_APPUID = 0;
	
	const CONTEXT_SOAP = array('http' => array('user_agent' => 'PHPSoapClient'));
	const CONTEXT_SOAP2 = array('http' => array( 'user_agent' => 'PHPSoapClient'),'ssl' => array('verify_peer' => false,'verify_peer_name' => false, 'allow_self_signed' => true));
    
    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;
    
    public function WsSimadUariv()
    {
        register_shutdown_function ('shutDown_handler');
        $this->logfile = sfConfig::get("sf_log_dir")."\WsSimadUariv";      
        $this->nulog = 0;
        $this->nudebug = 0;
    }
    
    function shutDown_handler()
	{
		$last_error = error_get_last();
		//verify if shutwown is caused by an error
		if (isset ($last_error['type']) && $last_error['type'] == E_ERROR)
		{
			/*
				my activity for log or messaging
				you can use info: 
				$last_error['type'], $last_error['message'],
				$last_error['file'], $last_error['line']
				see about on the manual PHP at error_get_last()
			*/
		}
	}

    public function writetolog($msg,$mimetype="txt")
    {
        if($this->nulog){
            $f = fopen($this->logfile.'.'.$mimetype,"a");
            if($f){ fprintf($f,"\n%s=>%s\t\r",date("Y-m-d G:i:s"),$msg); }
            fclose($f);
            if($this->nudebug){ echo "<pre>".$msg."</pre>"; }
        }
    }
    
	public function initComEnviadaReplyAsync($estado_proceso = "PENDIENTE")
    {
        // add your code here
        $log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.date("Ymd").'_comenviadareplycli.log';
        //***************************************************************************************************************
        // add your code here
        simad_util::writetolog($log_dir,'Hora Incio '.date("Y-m-d G:i:s"));
        //***************************************************************************************************************
        $max_rows = 800;
        //***************************************************************************************************************
        $c1 = new Criteria();
		$c1->setLimit($max_rows);
		$c1->add(WebserviceReplyPeer::ESTADO_PROCESO,$estado_proceso);		
        $c1->addAscendingOrderByColumn(WebserviceReplyPeer::WEBSERVICEREPLY_ID);
		//***************************************************************************************************************
        $c1->addJoin(WebserviceReplyPeer::PKCONSECUTIVO_ID,ComEnviadaPeer::COMENVIADA_ID);
        //***************************************************************************************************************
		$c1->clearSelectColumns();
        $c1->addSelectColumn(WebserviceReplyPeer::WEBSERVICEREPLY_ID);
		$c1->addSelectColumn(WebserviceReplyPeer::PKCONSECUTIVO_ID);
		$c1->addSelectColumn(WebserviceReplyPeer::NOMBRE_METODO);
        $c1->addSelectColumn(WebserviceReplyPeer::ESTADO_PROCESO);
        $c1->addSelectColumn(ComEnviadaPeer::CONSECUTIVO_RESP);        
		//***************************************************************************************************************
        try{
            $stmt  = WebserviceReplyPeer::doSelectStmt($c1);
            $objects_com = $stmt->fetchAll();
            //***********************************************************************************************************
            simad_util::writetolog($log_dir,'Total registros => '.count($objects_com));
            //***********************************************************************************************************
            foreach ($objects_com as $object_info)
            {
                $pkobject_id = trim($object_info['WEBSERVICEREPLY_ID']);
                try{
                    if(!empty($object_info['CONSECUTIVO_RESP']) || !empty($object_info['PKCONSECUTIVO_ID'])){
                        $comenviada_id = trim($object_info['PKCONSECUTIVO_ID']);
                        $comrecibida_id = trim($object_info['CONSECUTIVO_RESP']);

                        $response = $this->loadWsInfoRadicadoSalida($comenviada_id,$comrecibida_id);
                        
                        if($response['status'] == 200){
                            $str_log = 'Consecutivo Salida '.$comenviada_id.' fue enviado a Fachada/Lex satisfactoriamente';
                            WebserviceReplyPeer::updateWsReply($pkobject_id,"ENVIADO_OK",$response['message']);
                        }else{
                            $str_log = 'Consecutivo Salida '.$comenviada_id.' ocurrio un error de integracion y no se pudo enviar a Fachada/Lex';
                            WebserviceReplyPeer::updateWsReply($pkobject_id,"ERROR_FACHADA",$response['message']);
                        }
                    }else{
                        $message_error = $str_log = "No se encontro el consecutivo de entrada o de salida";
                        WebserviceReplyPeer::updateWsReply($pkobject_id,"ERROR_DATOS",$message_error);
                    }
                    //******************************************************************************************************
                    simad_util::writetolog($log_dir,$str_log);
                } catch (PropelException $ex) {
                    $msgex = $ex->getMessage();
                    simad_util::writetolog($log_dir,sprintf("WebserviceReplyId: %s, Message: %s",$pkobject_id,$msgex));
                    WebserviceReplyPeer::updateWsReply($pkobject_id,"ERROR_PROPEL_EX",$msgex);
                } catch (Exception $ex) {
                    $msgex = $ex->getMessage();
                    simad_util::writetolog($log_dir,sprintf("WebserviceReplyId: %s, Message: %s",$pkobject_id,$msgex));
                    WebserviceReplyPeer::updateWsReply($pkobject_id,"ERROR_EXCEPTION",$msgex);
                }
            }
        } catch (PropelException $ex) {
            $msgex = $ex->getMessage();
            simad_util::writetolog($log_dir,$msgex);
        } catch (Exception $ex) {
            $msgex = $ex->getMessage();
            simad_util::writetolog($log_dir,$msgex);
        }
        //**************************************************************************************************************
        simad_util::writetolog($log_dir,'Hora Fin '.date("Y-m-d G:i:s"));
    }
	
	public static function getCriteriaBasic($dependencia_id = 0, $isNullOrZero = false, $fecha_inicial = null, $ndias = 1, $max_rows = 800)
    {
		try{
			$fecha_actual = date("Y-m-d");
			//***************************************************************************************************************
			$fecha_actual = new DateTime(); 
			$fecha_actual->modify("-$ndias days");
			$new_fecha = $fecha_actual->format('Y-m-d');
			//***************************************************************************************************************
			if(!empty($fecha_inicial)){
				$dt = new DateTime($fecha_inicial); 
				$fecha_init = $dt->format('Y-m-d');
			}
			//***************************************************************************************************************
			$c1 = new Criteria();
			$c1->setLimit($max_rows);
			//$c1->add(ComRecibidaPeer::MARCA,$marcausuario_id);
			//***************************************************************************************************************
			if($isNullOrZero){
				$cor1 = $c1->getNewCriterion(ComRecibidaPeer::RESPTA_INTEGRACION,null,Criteria::ISNULL);
				$cor2 = $c1->getNewCriterion(ComRecibidaPeer::RESPTA_INTEGRACION, "0");
				$cor1->addOr($cor2);
				$c1->add($cor1);
			}else{
				$c1->add(ComRecibidaPeer::RESPTA_INTEGRACION,null,Criteria::ISNULL);
			}
			//***************************************************************************************************************
			$c1->add(ComRecibidaPeer::DEPENDENCIA_ID,$dependencia_id);
			$c1->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
			//***************************************************************************************************************
			if(empty($fecha_init)){
				$c1->add(ComRecibidaPeer::FECHA_CREACION,$new_fecha.' 00:00:00',Criteria::GREATER_EQUAL);
				$c1->addAnd(ComRecibidaPeer::FECHA_CREACION,$new_fecha.' 23:59:59',Criteria::LESS_EQUAL);
			}else{
				$c1->add(ComRecibidaPeer::FECHA_CREACION,$fecha_init.' 00:00:00',Criteria::GREATER_EQUAL);
				$c1->addAnd(ComRecibidaPeer::FECHA_CREACION,$new_fecha.' 23:59:59',Criteria::LESS_EQUAL);
			}
			//***************************************************************************************************************
			$c1->clearSelectColumns();
			$c1->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);
			$c1->addSelectColumn(ComRecibidaPeer::RADICADO);
			$c1->addSelectColumn(ComRecibidaPeer::RESPTA_INTEGRACION);
			$c1->addSelectColumn(ComRecibidaPeer::MARCA_VINCULACION);
			//***************************************************************************************************************
			$result_stmt  = ComRecibidaPeer::doSelectStmt($c1);
			return $objects_com = $result_stmt->fetchAll();
		} catch (\Throwable $th) {
            return array();
        }
	}
	
	public function initPrcessMasivo($marcausuario_id = 0, $fecha_inicial = null, $isNullOrZero = false)
    {
        // add your code here
        $log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.date("Ymd").'_enviadoslexcli'.(!empty($fecha_inicial) ? '_reply' : '').'.log';
        
        // add your code here
        simad_util::writetolog($log_dir,'Hora Incio '.date("Y-m-d G:i:s"));
        //***************************************************************************************************************
		$unidaddocumental_id = 761178;
        $tipodocumental_id = 21676;
        $origentransferencia_id = 2;
        $usuarioorigen_id = 4249;
        $ucargoorigen_id = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuarioorigen_id);
        $estadocomrecibida_id = 1;
        $tipoprocesocom_id = 3;
		$dependencia_id = 30;
		$fecha_actual = date("Y-m-d");
		//***************************************************************************************************************
		$objects_com = WsSimadUariv::getCriteriaBasic($dependencia_id,$isNullOrZero,$fecha_inicial);
		//***************************************************************************************************************
        simad_util::writetolog($log_dir,'Total registros => '.count($objects_com));
		print('Total registros => '.count($objects_com). PHP_EOL);
        $index = 1;
		$conexion = Propel::getConnection();
		//***************************************************************************************************************
        foreach ($objects_com as $com_recibida){
			$c2 = new Criteria();
			$c2->add(WebserviceLogPeer::TIPO_OPERACION,'%'.$com_recibida['RADICADO'],Criteria::LIKE);
			$c2->add(WebserviceLogPeer::NOMBRE_METODO,'InformacionRadicadoEntrada');
			$c2->add(WebserviceLogPeer::MENSAJE,'Enviado Exitoso%',Criteria::LIKE);
			$c2->addAscendingOrderByColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
			//***********************************************************************************************************
			$c2->setLimit(1);
			$c2->clearSelectColumns();
			$c2->addSelectColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
			$c2->addSelectColumn(WebserviceLogPeer::MENSAJE);
			//***********************************************************************************************************
			$ws_log = WebserviceLogPeer::doSelectStmt($c2);
			$list_objects = $ws_log->fetchAll();
			//***********************************************************************************************************
            $str_log = null;$enviarRespExt = true;
			if(count($list_objects)){
				foreach ($list_objects as $item) {
					$isNotError = strpos($item['MENSAJE'], 'Enviado Exitoso');
					if ($isNotError !== false){
						$codigo_envio = str_replace("Enviado Exitoso => ","",$item['MENSAJE']);
						$str_log = 'Radicado '.trim($com_recibida['RADICADO']).' ya fue enviado ha LEX Codigo => '.$codigo_envio;
						
						if(empty($com_recibida['RESPTA_INTEGRACION'])){
							$query = "UPDATE %s SET %s = 0, %s = '".$codigo_envio."' WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
							$runsql = sprintf($query, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA,ComRecibidaPeer::RESPTA_INTEGRACION,ComRecibidaPeer::COMRECIBIDA_ID);
							$sentencia = $conexion->prepare($runsql);
							$sentencia->execute();
						}
						
						$enviarRespExt = false;
					}else{
						$str_log = 'Radicado '.trim($com_recibida['RADICADO']).' fue enviado a Lex pero ocurrio un error interno en Fachada/Lex';
						$query1 = "UPDATE %s SET %s = '0' WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
						$runsql1 = sprintf($query1, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::RESPTA_INTEGRACION, ComRecibidaPeer::COMRECIBIDA_ID);
						$sentencia = $conexion->prepare($runsql1);
						$sentencia->execute();
						
						$enviarRespExt = false;
					}
				}
            }else{
				$str_log = 'Radicado '.trim($com_recibida['RADICADO']).' pendiente por enviar a LEX';
            }
			//**********************************************************************************************************
			simad_util::writetolog($log_dir,$str_log);
			print($index++.'. '.$str_log . PHP_EOL);
            //continue;
			$ws_log = null;$c2 = null;$str_log = null;$list_objects = null;
            //**********************************************************************************************************
            if($enviarRespExt){
                $marca_vinculacion = (int)$com_recibida['MARCA_VINCULACION'];
                $pkcomid = $com_recibida['COMRECIBIDA_ID'];
                //******************************************************************************************************
                if($marca_vinculacion == 0){
					$response_transfer = TransferenciaPeer::addAutoTransfAndContenido($unidaddocumental_id,$tipodocumental_id,$pkcomid,$origentransferencia_id,$usuarioorigen_id);
					$isTransfer = $response_transfer['isError'];
                }else{
					$isTransfer = true;
                }
                //******************************************************************************************************
				try{
					if(!$isTransfer){
						simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' enviado para transferencia');
						
						foreach(ComRecibidaPeer::getListIntersadosByComId($pkcomid) as $com_interesado){
							UnidaddocumentalInteresadosPeer::addNewInteresadoByComId($unidaddocumental_id,$com_interesado->getInteresadoId());
						}
						
						$response_data = $this->loadWsInfoRadicadoEntrada($pkcomid);
						if($response_data['status'] == 200){
							simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' enviado respuesta externa');
							//**********************************************************************************************
							$query2 = "UPDATE %s SET %s = 0, %s = 3 WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
							$runsql2 = sprintf($query2, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA, ComRecibidaPeer::TIPOPROCESOCOM_ID, ComRecibidaPeer::COMRECIBIDA_ID);
							$sentencia = $conexion->prepare($runsql2);
							$sentencia->execute();
							//**********************************************************************************************
							ComRecibidaPeer::updateAsignadoCom($pkcomid,2,0);
							ComRecibidaPeer::addUserRolByCom($pkcomid,$usuarioorigen_id,$ucargoorigen_id,$estadocomrecibida_id,2,1,$tipoprocesocom_id);
						}else{
							$query5 = "UPDATE %s SET %s = '0' WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
							$runsql5 = sprintf($query5, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::RESPTA_INTEGRACION, ComRecibidaPeer::COMRECIBIDA_ID);
							$sentencia = $conexion->prepare($runsql5);
							$sentencia->execute();
							//**********************************************************************************************
							simad_util::writetolog($log_dir,'La comunicación con radicado '.trim($com_recibida['RADICADO']).' se archivo pero ocurrio un error al enviar para respuesta externa');
						}
						//**************************************************************************************************
						$response_data = null;
					}elseif($marca_vinculacion == 1){
						$response_data = $this->loadWsInfoRadicadoEntrada($pkcomid);
						if($response_data['status'] == 200){
							simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' enviado respuesta externa');
							//**********************************************************************************************
							$query3 = "UPDATE %s SET %s = 0, %s = 3 WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
							$runsql3 = sprintf($query3, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA, ComRecibidaPeer::TIPOPROCESOCOM_ID, ComRecibidaPeer::COMRECIBIDA_ID);
							$sentencia = $conexion->prepare($runsql3);
							$sentencia->execute();
							//**********************************************************************************************
							ComRecibidaPeer::updateAsignadoCom($pkcomid,2,0);
							ComRecibidaPeer::addUserRolByCom($pkcomid,$usuarioorigen_id,$ucargoorigen_id,$estadocomrecibida_id,2,1,$tipoprocesocom_id);
						}else{
							$query4 = "UPDATE %s SET %s = '0' WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
							$runsql4 = sprintf($query4, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::RESPTA_INTEGRACION, ComRecibidaPeer::COMRECIBIDA_ID);
							$sentencia = $conexion->prepare($runsql4);
							$sentencia->execute();
							//**********************************************************************************************
							simad_util::writetolog($log_dir,'La comunicación con radicado '.trim($com_recibida['RADICADO']).' se archivo pero ocurrio un error al enviar para respuesta externa');
						}
						//**************************************************************************************************
						$response_data = null;
					}else{
						simad_util::writetolog($log_dir,'Ocurrio un error al archivar el radicado => '.trim($com_recibida['RADICADO']));
					}
				}catch(Exception $ex){
				  simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' ocurrio un error interno, '.$ex->getMessage());
				}
            }
        }
        //**************************************************************************************************************
        simad_util::writetolog($log_dir,'Hora Fin '.date("Y-m-d G:i:s"));
    }

    /**
    * WsSimadUariv::loadWsInfoRadicadoEntrada()
    * funcion para consumir servicio web fachada y enviar un radicado de entrada
    * @return
    */
    public function loadWsInfoRadicadoEntrada($pkComId,$sendFile = true)
    {
        $com_recibida = ComRecibidaPeer::retrieveByPK($pkComId);
		$radicado_com = $com_recibida != null ? trim($com_recibida->getRadicado()) : "";
        $folder_digit = ComRecibidaPeer::initFolderDigit($com_recibida->getRegionalId(),$com_recibida->getPeriodoId());
        $filepath_digit = ComRecibidaPeer::getDigitFormatFileExist($folder_digit['full_path'],$radicado_com);
        //**********************************************************************
        $com_recibida_old = clone $com_recibida;
        //**********************************************************************
        $infoCredencial = array(
            "ContrasenaWsFachada" => WsSimadUariv::SERVICE_PASSW,
            "IdAplicacion" => WsSimadUariv::SERVICE_APPUID,
            "UsuarioWsFachada" => WsSimadUariv::SERVICE_USER
        );
        //**********************************************************************
        $unidad_documental = TransferenciaPeer::getExpedienteByComRecibidaId($com_recibida->getPrimaryKey());
        //**********************************************************************
        $list_users = $com_recibida->getUsuariosListCom();
        //**********************************************************************
		if($sendFile){
			$infoRadEntrada['ARCHIVO'] = ComRecibidaPeer::getDigitFileB64($filepath_digit);
			//$log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.date("Ymd").'_sizefile.log';
			//$infoRadEntrada['ARCHIVO'] = simad_util::getConvert2FileToB64($filepath_digit);
			//simad_util::writetolog($log_dir,simad_util::getConvert2FileToB64($filepath_digit));
		}else{
			$test_file = sfConfig::get("sf_web_dir").DIRECTORY_SEPARATOR.'test.pdf';
			$infoRadEntrada['ARCHIVO'] = ComRecibidaPeer::getDigitFileB64($test_file);
		}
        //***********************************************************************************
        $options = array(
            "uri"=> WsSimadUariv::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_NONE,
			"timeout" => 300,
			"connection_timeout" => 300,
            "trace" => true,
            "encoding" => "UTF-8",
            /*"encoding"=>"ISO-8859-1",*/
            "exceptions" => true,
			'keep_alive' => false,
			'stream_context' => stream_context_create(WsSimadUariv::CONTEXT_SOAP2),
        );
        //************************************************************************************
        $csizemsg = strlen($infoRadEntrada['ARCHIVO']);
        $client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
        //************************************************************************************
        if($csizemsg >= self::MAX_FILE_SIZE_MESSAGE)
        {
            $client->setComOrExpVars($com_recibida->getPrimaryKey(),$com_recibida->getRadicado(),true);
            $response = $client->doRequestClientCli();

            $message_response = json_decode($response);
            if($message_response != null){
                $mensaje = $message_response->MENSAJE;
                if(trim($message_response->CODIGO) != "200"){
                    WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",null,$mensaje,true,"Consumen Fachada => ".$com_recibida->getRadicado(),"Error Fachada => ".$mensaje,1);
                    $com_recibida->setResptaIntegracion("0");
                    $com_recibida->save();
                    //*************************************************************************
                    //AuditLogPeer::guardarAuditoriaLite(ComRecibidaPeer::getOMClass(),$com_recibida_old,$com_recibida,ModulesEnable::ComRecibida,$com_recibida->getRadicado(),)
                    //*************************************************************************
                    return array('status' => 400,'message' => $mensaje);
                }else{
                    $cod_respuesta = $message_response->RESPONSE;
                    WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",null,$mensaje,true,"Consumen Fachada => ".$com_recibida->getRadicado(),"Enviado Exitoso => ".$cod_respuesta,1);
                    $com_recibida->setResptaIntegracion($cod_respuesta);
                    $com_recibida->save();
                    return array('status' => 200, 'message' => 'Los datos fueron enviados satisfactoriamente, codigo de integracion '.$cod_respuesta);
                }
            }else{
                $message = 'Ocurrio un error al enviar los datos, error de integraci&oacute;n con la herramienta de gesti&oacute;n';
                WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",null,null,true,"Consumen Fachada => ".$com_recibida->getRadicado(),"Enviado Error => ".$message,1);
                $com_recibida->setResptaIntegracion("0");
                $com_recibida->save();
                return array('status' => 400, 'message' => $message);
            }
        }else{
			$infoRadEntrada['ASUNTO'] = (mb_strtoupper($com_recibida->getAsunto()));
			$infoRadEntrada['CLASIFICACION_DOCUMENTO'] = (mb_strtoupper($com_recibida->getTipoComRecibida()->getDescripcion()));
			$infoRadEntrada['CODIGO_GRUPO_TRABAJO'] = ($com_recibida->getDependencia()->getCodigo());
			$infoRadEntrada['DANE_DEPARTAMENTO_RADICADO'] = ($com_recibida->getCiudad()->getDepartamento()->getCodigoDane());
			$infoRadEntrada['DANE_MUNICIPIO_RADICADO'] = ($com_recibida->getCiudad()->getCodigoDane());
			$infoRadEntrada['DEPENDENCIA_DESTINO'] = (mb_strtoupper($com_recibida->getDependencia()->getNombre()));
			//***********************************************************************************
			if(!empty($com_recibida->getFechaMaximaRespuesta())){
				$infoRadEntrada['FECHA_VENCIMIENTO'] = trim($com_recibida->getFechaMaximaRespuesta()) ? ($com_recibida->getFechaMaximaRespuesta('Y-m-d')) : "";
			}
			//***********************************************************************************
			$infoRadEntrada['FUD'] = trim($com_recibida->getNumeroFud()) ? mb_strtoupper($com_recibida->getNumeroFud()) : "";
			$infoRadEntrada['ID_JUZGADO'] = 0;
			$infoRadEntrada['MEDIO_RECEPCION'] = (mb_strtoupper($com_recibida->getFormaRecepcion()->getDescripcion()));
			$infoRadEntrada['MarcoNormativo'] = 0;
			$infoRadEntrada['NOMBRE_ARCHIVO'] = basename($filepath_digit);
			$infoRadEntrada['NOMBRE_EXPEDIENTE'] = (mb_strtoupper($unidad_documental->getTitulo()));
			$infoRadEntrada['NUMERO_DECLARACION'] = trim($com_recibida->getNumeroFud()) ? mb_strtoupper($com_recibida->getNumeroFud()) : "";
			$infoRadEntrada['NUMERO_FOLIOS'] = $com_recibida->getFolios();
			$infoRadEntrada['NUMERO_PROCESO'] = trim($com_recibida->getNumeroProceso()) ? $com_recibida->getNumeroProceso() : "";
			$infoRadEntrada['NUMERO_RADICADO_ENTRADA']= $radicado_com;
			$infoRadEntrada['PRIORIDAD'] = (mb_strtoupper($com_recibida->getPrioridadCom()->getDescripcion()));
			$infoRadEntrada['PUNTO_RADICACION'] = (mb_strtoupper($com_recibida->getRegional()->getDescripcion()));
			$infoRadEntrada['TIPO_DOCUMENTO'] = 1;
			$infoRadEntrada['USUARIO_RADICADOR'] = mb_strtoupper($list_users['radicador']);
			$infoRadEntrada['ID_GESTOR'] = isset($list_users['nuid_usuario_actual']) ? $list_users['nuid_usuario_actual'] : 0;
				//***********************************************************************************
			if(!empty($com_recibida->getFechaRecibido())){
				$infoRadEntrada['FECHA_NOTIFICACION'] = !empty($com_recibida->getFechaRecibido()) ? ($com_recibida->getFechaRecibido('Y-m-d')) : "";
			}
			//***********************************************************************************
			$listIntesados = array();
			$intesados_objects = ComRecibidaPeer::getListIntersadosByComId($com_recibida->getPrimaryKey()) ;
			foreach ($intesados_objects as $interesado) {
				$inumero_identificacion = $interesado->getInteresados()->getNumeroIdentificacion();
				$infoInteresado['CELULAR']= $interesado->getInteresados()->getCelular() ? $interesado->getInteresados()->getCelular() : "";
				$infoInteresado['CODIGO_PAIS']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getPais()->getCodigoNumerico();
				$infoInteresado['PAIS']= (mb_strtoupper($interesado->getInteresados()->getCiudad()->getDepartamento()->getPais()->getNombre()));
				$infoInteresado['CODIGO_DEPARTAMENTO']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getCodigoDane();
				$infoInteresado['CODIGO_MUNICIPIO']= $interesado->getInteresados()->getCiudad()->getCodigoDane();
				$infoInteresado['MUNICIPIO']= (mb_strtoupper($interesado->getInteresados()->getCiudad()->getNombre()));
				$infoInteresado['DATOS_REPRESENTANTE']= RepresentanteLegalPeer::getToArraySoap($interesado->getRepresentantelegalId());
				$infoInteresado['DEPARTAMENTO']= (mb_strtoupper($interesado->getInteresados()->getCiudad()->getDepartamento()->getNombre()));
				$infoInteresado['DIRECCION']= trim($interesado->getInteresados()->getDireccion()) ? (mb_strtoupper($interesado->getInteresados()->getDireccion())) : "";
				$infoInteresado['EMAIL']= trim($interesado->getInteresados()->getEmail()) ? mb_strtoupper($interesado->getInteresados()->getEmail()) : "";
				$infoInteresado['FAX']= $interesado->getInteresados()->getFax() ? $interesado->getInteresados()->getFax() : "";
				$infoInteresado['NUMERO_DOCUMENTO']= !empty($inumero_identificacion) ? $inumero_identificacion : 0;
				$infoInteresado['PRIMER_NOMBRE']= trim($interesado->getInteresados()->getPrimerNombre()) ? (mb_strtoupper($interesado->getInteresados()->getPrimerNombre())) : "";
				$infoInteresado['PRIMER_APELLIDO']= trim($interesado->getInteresados()->getPrimerApellido()) ? (mb_strtoupper($interesado->getInteresados()->getPrimerApellido())) : "";
				$infoInteresado['SEGUNDO_NOMBRE']= trim($interesado->getInteresados()->getSegundoNombre()) ? (mb_strtoupper($interesado->getInteresados()->getSegundoNombre())) : "";
				$infoInteresado['SEGUNDO_APELLIDO']= trim($interesado->getInteresados()->getSegundoApellido()) ? (mb_strtoupper($interesado->getInteresados()->getSegundoApellido())) : "";
				$infoInteresado['TELEFONO']= $interesado->getInteresados()->getTelefono() ? $interesado->getInteresados()->getTelefono() : "";
				$infoInteresado['TIPO_DOCUMENTO']= $interesado->getInteresados()->getTipoIdentificacion()->getCodigo();
				$infoInteresado['GENERO']= $interesado->getInteresados()->getTipogeneroId();
				$listIntesados[] = $infoInteresado;
			}
			//***********************************************************************************
			$infoRemitente = array();
			if($com_recibida->getDirectorioexternoId()){
				$infoRemitente['CODIGO_DEPARTAMENTO'] = $com_recibida->getDirectorioExterno()->getCiudad()->getDepartamento()->getCodigoDane();
				$infoRemitente['CODIGO_MUNICIPIO'] = $com_recibida->getDirectorioExterno()->getCiudad()->getCodigoDane();
				$infoRemitente['CODIGO_PAIS'] = $com_recibida->getDirectorioExterno()->getCiudad()->getDepartamento()->getPais()->getCodigoNumerico();
				$infoRemitente['DEPARTAMENTO'] = (mb_strtoupper($com_recibida->getDirectorioExterno()->getCiudad()->getDepartamento()->getNombre()));
				$infoRemitente['DIRECCION'] = trim($com_recibida->getDirectorioExterno()->getDireccion()) ? (mb_strtoupper($com_recibida->getDirectorioExterno()->getDireccion())) : "";
				$infoRemitente['EMAIL'] = trim($com_recibida->getDirectorioExterno()->getEmail()) ? mb_strtoupper($com_recibida->getDirectorioExterno()->getEmail()) : "";
				$infoRemitente['MUNICIPIO'] = (mb_strtoupper($com_recibida->getDirectorioExterno()->getCiudad()->getNombre()));
				$infoRemitente['NUMERO_DOCUMENTO'] = trim($com_recibida->getDirectorioExterno()->getNit()) ? ($com_recibida->getDirectorioExterno()->getNit()) : "";
				$infoRemitente['PAIS'] = (mb_strtoupper($com_recibida->getDirectorioExterno()->getCiudad()->getDepartamento()->getPais()->getNombre()));
				$infoRemitente['REMITENTE'] = (mb_strtoupper($com_recibida->getDirectorioExterno()->getNombre()));
				$infoRemitente['TELEFONO']= $com_recibida->getDirectorioExterno()->getTelefono() ? $com_recibida->getDirectorioExterno()->getTelefono() : "";
				$infoRemitente['TIPO_DOCUMENTO']= $com_recibida->getDirectorioExterno()->getTipoIdentificacion()->getCodigo();
                $infoRemitente['ID_ENTIDAD']= !empty($com_recibida->getDirectorioExterno()->getIdEntidadRuv()) ? $com_recibida->getDirectorioExterno()->getIdEntidadRuv() : 0;
			}else{
				$infoRemitente['CODIGO_DEPARTAMENTO'] = 0;
				$infoRemitente['CODIGO_MUNICIPIO'] = 0;
				$infoRemitente['CODIGO_PAIS'] = 0;
				$infoRemitente['DEPARTAMENTO'] = 0;
				$infoRemitente['DIRECCION'] = 0;
				$infoRemitente['EMAIL'] = 0;
				$infoRemitente['MUNICIPIO'] = 0;
				$infoRemitente['NUMERO_DOCUMENTO'] = 0;
				$infoRemitente['PAIS'] = 0;
				$infoRemitente['REMITENTE'] = 0;
				$infoRemitente['TELEFONO']= 0;
				$infoRemitente['TIPO_DOCUMENTO']= 0;
                $infoRemitente['ID_ENTIDAD']= 0;
			}
			//***********************************************************************************
			if($com_recibida->getMarcaVinculacion()){
				$infoExpediente = array();
				$infoExpediente['CODIGO_DEPENDENCIA'] = $unidad_documental->getSubserie()->getSerie()->getDependencia()->getCodigo();
				$infoExpediente['CODIGO_SERIE_DOCUMENTAL'] = $unidad_documental->getSubserie()->getSerie()->getCodigo();
				$infoExpediente['CODIGO_SUBSERIE_DOCUMENTAL'] = $unidad_documental->getSubserie()->getCodigo();
				$infoExpediente['ESTADO'] = (mb_strtoupper($unidad_documental->getEstadoUnidadDocumental()->getDescripcion()));
				$infoExpediente['ID_EXPEDIENTE'] = $unidad_documental->getCodigoBarras();
				$infoExpediente['NOMBRE_DEPENDENCIA'] = (mb_strtoupper($unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre()));
				$infoExpediente['NOMBRE_EXPEDIENTE'] = (mb_strtoupper($unidad_documental->getTitulo()));
				$infoExpediente['NOMBRE_SERIE_DOCUMENTAL'] = (mb_strtoupper($unidad_documental->getSubserie()->getSerie()->getDescripcion()));
				$infoExpediente['NOMBRE_SUBSERIE_DOCUMENTAL'] = (mb_strtoupper($unidad_documental->getSubserie()->getDescripcion()));
				$infoExpediente['NUMERO_EXPEDIENTE'] = mb_strtoupper($unidad_documental->getCodigoBarras());
			}else{
				$infoExpediente = array();
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
			//************************************************************************************
			try {
				$infoComplex = array('Credencial' =>$infoCredencial,'infoRadEntrada' => $infoRadEntrada,'infoInteresado' => array('EntInformacionInteresadoRequest' => $listIntesados),'infoRemitente' => $infoRemitente,'datosExpediente' => $infoExpediente);
				$response = $client->InformacionRadicadoEntrada($infoComplex);
				//********************************************************************************
				$a = new stdClass();
				$a = (object)$response->InformacionRadicadoEntradaResult;
				//********************************************************************************
				//if($this->nulog){
                    //$this->writetolog(htmlspecialchars($client->__getLastRequest()),'xml');
				//}
				//********************************************************************************
				if(trim($a->DESC_ERROR)){
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".trim($a->DESC_ERROR),1);
					$com_recibida->setResptaIntegracion("0");
					$com_recibida->save();
					$infoComplex = null;$response = null;
					return array('status' => 400,'message' => trim($a->DESC_ERROR));
				}else{
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".trim($a->RESULT),1);
					$com_recibida->setResptaIntegracion(trim($a->RESULT));
					$com_recibida->save();
					$infoComplex = null;$response = null;
					return array('status'=>200,'message'=>'Los datos fueron enviados satisfactoriamente');
				}
			}catch (SoapFault $e){
                $rawFault = $client->getLastRawFaultResponse();
                if (!empty($rawFault)) {
                    $faultMessage = $client->extractFaultFromSoapResponse($rawFault);
                    if ($faultMessage !== null) {
                        $msgerror =  "SoapFault Error:<br />" . nl2br($faultMessage). '<br />';
                    }
                }else{
                    $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
                }
                //********************************************************************************
				try{
					$com_recibida->setResptaIntegracion("0");
					$com_recibida->save();
					
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($msgerror),true,"Consumen Fachada SoapFault => ".$radicado_com,"SoapFault Error => ".$msgerror,1);
				} catch (Exception $e) {
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",null,htmlspecialchars($msgerror),true,"Consumen Fachada SoapFault => ".$radicado_com,"SoapFault Error => ".$msgerror,1);
				}
								
				return array('status'=>400,'message'=>'Ocurrio un error al enviar los datos, error de integraci&oacute;n con la herramienta de gesti&oacute;n => ' . $msgerror);
			} catch (Exception $e) {
				$msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
				try{
					$com_recibida->setResptaIntegracion("0");
					$com_recibida->save();
				
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada Exception => ".$radicado_com,"Exception Error => ".$msgerror,1);
				} catch (Exception $e) {
					WebserviceLogPeer::addLogWs("InformacionRadicadoEntrada",null,htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada Exception => ".$radicado_com,"Exception Error => ".$msgerror,1);
				}
				
				return array('status'=>400,'message'=>'Ocurrio un error al enviar la datos, error de servidor => ' . $msgerror);
			}
		}
    }

    /**
    * WsSimadUariv::loadWsInfoRadicadoSalida()
    * consume servicio de fachada para notificar la radicacion de un externa enviada por demanda
    * @return array
    */
    public function loadWsInfoRadicadoSalida($comenviada_id,$comrecibida_id,$msgsing="")
    {
    	$com_recibida = ComRecibidaPeer::retrieveByPK($comrecibida_id);
        $com_enviada = ComEnviadaPeer::retrieveByPK($comenviada_id);
        //***********************************************************************************
		$radicado_com = $com_enviada != null ? trim($com_enviada->getRadicado()) : "";
		$radicado_entrada = $com_recibida != null ? trim($com_recibida->getRadicado()) : "";
        $folder_digit = ComEnviadaPeer::initFolderDigit($com_enviada->getRegionalId(),$com_enviada->getPeriodoId());
        $filepath_digit = ComEnviadaPeer::getDigitFormatFileExist($folder_digit['full_path'],$radicado_com);
        //***********************************************************************************
        $infoCredencial = array(
            "ContrasenaWsFachada" => WsSimadUariv::SERVICE_PASSW,
            "IdAplicacion" => WsSimadUariv::SERVICE_APPUID,
            "UsuarioWsFachada" => WsSimadUariv::SERVICE_USER
        );
        //***********************************************************************************
        $infoRadSalida = array();
        $infoRadSalida['IMAGEN_DOC_FIRMADO'] = simad_util::getConvertFileToB64($filepath_digit);
        $infoRadSalida['ID_EXPEDIENTE'] = $com_recibida->getTipocomrecibidaId();
        $infoRadSalida['CODIGO_DEPENDENCIA'] = $com_enviada->getDependencia()->getCodigo();
        $infoRadSalida['NUMERO_EXPEDIENTE'] = $radicado_entrada;
        $infoRadSalida['NUMERO_RADICADO_SALIDA'] = $radicado_com;
        //***********************************************************************************
        if($com_enviada->getTipoIntegracion() == "MASIVOEXCEL"){
            $infoRadSalida['CODIGO_SEGUIM_3'] = 1;
        }
        //***********************************************************************************
        $options = array(
            "uri"=> WsSimadUariv::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_NONE,
            "timeout" => 600,
			"connection_timeout" => 600,
            "trace" => true,
            "encoding" => "UTF-8",
            /*'encoding'=>'ISO-8859-1',*/
            "exceptions" => true,
			'stream_context' => stream_context_create(WsSimadUariv::CONTEXT_SOAP2),
        );
        //************************************************************************************
		try {
            $client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
        } catch (\Throwable $th) {
            //throw $th;
        }
		//************************************************************************************
        try {
            //var_dump($listIntesados);exit;
            $infoComplex = array('Credencial' =>$infoCredencial,'infoRadSalida' => $infoRadSalida);
            $response = $client->InformacionRadicadoSalida($infoComplex);
            //********************************************************************************
            $a = new stdClass();
            $a = (object)$response->InformacionRadicadoSalidaResponse;
            //********************************************************************************
            if($this->nulog){ $this->writetolog(htmlspecialchars($client->__getLastRequest()),'xml'); }
            //********************************************************************************
            if(trim($a->DESC_ERROR)){
                WebserviceLogPeer::addLogWs("InformacionRadicadoSalida",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".trim($a->DESC_ERROR),1);
                return array('status'=>400,'message'=>sprintf("%s %s",$msgsing,trim($a->DESC_ERROR)));
            }else{
                WebserviceLogPeer::addLogWs("InformacionRadicadoSalida",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".trim($a->RESULT),1);
                return array('status'=>200,'message'=>sprintf("%s, %s",$msgsing,'El radicado se envio a la herramienta de gesti&oacute;n'));
            }
		}catch (PropelException $e){
            $msgerror = $e->getMessage();
            WebserviceLogPeer::addLogWs("InformacionRadicadoSalida",null,null,true,"Consumen Fachada PropelException => ".$radicado_com, "Error => ".$msgerror, 1);
            $this->writetolog($msgerror);
            return array('status'=> 400,'message'=>sprintf("%s %s",$msgsing,'Ocurrio un error al enviar los datos, error de de integracion'));
        }catch (SoapFault $e){
            $rawFault = $client->getLastRawFaultResponse();
            if (!empty($rawFault)) {
                $faultMessage = $client->extractFaultFromSoapResponse($rawFault);
                if ($faultMessage !== null) {
                    $msgerror =  "SoapFault Error:<br />" . nl2br($faultMessage). '<br />';
                }
            }else{
                $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            }
            //********************************************************************************
            WebserviceLogPeer::addLogWs("InformacionRadicadoSalida",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($msgerror),true,"Consumen Fachada SoapFault => ".$radicado_com,"Error => ".$msgerror,1);
            //$this->writetolog(htmlspecialchars($client->__getLastRequest()),"xml" );
            return array('status'=>400,'message'=>sprintf("%s %s",$msgsing.' => '.$msgerror,'Ocurrio un error al enviar los datos, error con la integraci&oacute;n de la herramienta de gesti&oacute;n'));
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            WebserviceLogPeer::addLogWs("InformacionRadicadoSalida",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada Exception => ".$radicado_com,"Error => ".$msgerror,1);
            //$this->writetolog(htmlspecialchars($client->__getLastRequest()),"xml" );
            return array('status'=>400,'message'=>sprintf("%s %s",$msgsing,'Ocurrio un error al enviar la datos, error de servidor => ' . $msgerror));            
        }
    }

    /**
    * WsSimadUariv::loadWsCrearExpedienteOferta()
    * consume servicio de fachada para notificar la radicacion de un externa enviada por oferta
    * @return array
    */
    public function loadWsCrearExpedienteOferta($comenviada_id,$msgsing="")
    {
        $com_enviada = ComEnviadaPeer::retrieveByPK($comenviada_id);
		$radicado_com = $com_enviada != null ? trim($com_enviada->getRadicado()) : "";
        //***********************************************************************************
        $folder_digit = ComEnviadaPeer::initFolderDigit($com_enviada->getRegionalId(),$com_enviada->getPeriodoId());
        $filepath_digit = ComEnviadaPeer::getDigitFormatFileExist($folder_digit['full_path'],$radicado_com);
        $extension_digit = pathinfo($filepath_digit,PATHINFO_EXTENSION);
        //***********************************************************************************
        $infoCredencial = array(
            "ContrasenaWsFachada" => WsSimadUariv::SERVICE_PASSW,
            "IdAplicacion" => 25,
            "UsuarioWsFachada" => WsSimadUariv::SERVICE_USER
        );
        //***********************************************************************************
        $nuid_gestor = $com_enviada->getNuidComObjectByRol(5,1);
        $usuariogestor_id = !empty($nuid_gestor) ? trim($nuid_gestor) : 0;
        //***********************************************************************************
        $nuid_firmas = $com_enviada->getNuidComObjectByRol(2);
        $list_firmantes = array();
        foreach ($nuid_firmas as $firmaId) {
            $list_firmantes[] = array('numeroCedula' => $firmaId);
        }
        //crear arreglo de cedulas de firmantes EntFirmanteRequest
        //***********************************************************************************
        $unidad_documental = null;
        if(!empty($com_enviada->getExpedienteId())){
            $unidad_documental = UnidadDocumentalPeer::retrieveByPK(trim($com_enviada->getExpedienteId()));
        }
        //***********************************************************************************
        $infoRadSalida = array();
        $infoRadSalida['TipoExpediente'] = "EG";
        $infoRadSalida['CodigoDependencia'] = $com_enviada->getDependencia()->getCodigo();
        $infoRadSalida['GestorExpediente'] = $usuariogestor_id;
        $infoRadSalida['Serie'] = $unidad_documental->getSubserie()->getSerie()->getCodigo();
        $infoRadSalida['Subserie'] = $unidad_documental->getSubserie()->getCodigo();
        $infoRadSalida['TipoDocumental'] = !empty($com_enviada->getTipoDocumentalCod()) ? trim($com_enviada->getTipoDocumentalCod()) : 0;
        $infoRadSalida['NombreExpediente'] = $unidad_documental->getTitulo();
        $infoRadSalida['DocumentoAsociado'] = simad_util::getConvertFileToB64($filepath_digit);
        $infoRadSalida['NombreDocumento'] = $radicado_com.'.'.$extension_digit;
        $infoRadSalida['MarcoNormativo'] = $com_enviada->getMarcoNormativo();
        $infoRadSalida['NumeroFUD'] = $com_enviada->getNumeroFud();
        $infoRadSalida['FechaResolucion'] = !empty($com_enviada->getFechaResolucion()) ? $com_enviada->getFechaResolucion('d/m/Y') : "";
        $infoRadSalida['SubOrigen'] = !empty($com_enviada->getSuborigen()) ? $com_enviada->getSuborigen() : "";
        $infoRadSalida['NumeroRadicadoSistemaOrigen'] = !empty($com_enviada->getNumradsysorigen()) ? $com_enviada->getNumradsysorigen() : "";
        $infoRadSalida['TipoEnvio'] = !empty($com_enviada->getTipoEnvio()) ? $com_enviada->getTipoEnvio() : "";
        $infoRadSalida['NumRadicado'] = $radicado_com;
        $infoRadSalida['IdExp'] = !empty($com_enviada->getExpedienteId()) ? $com_enviada->getExpedienteId() : "";
        $infoRadSalida['Firmantes'] = array('EntFirmanteRequest' => $list_firmantes);
        //***********************************************************************************
        $infoInteresado = array();
        $intesados_objects = ComEnviadaPeer::getListIntersadosByComId($com_enviada->getPrimaryKey()) ;
        foreach ($intesados_objects as $interesado) {
            //$infoInteresado['Celular']= $interesado->getInteresados()->getCelular() ? $interesado->getInteresados()->getCelular() : "0";
            $infoInteresado['Pais']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getPais()->getCodigoNumerico();
            $infoInteresado['Departamento']= $interesado->getInteresados()->getCiudad()->getDepartamento()->getCodigoDane();
            $infoInteresado['Municipio']= $interesado->getInteresados()->getCiudad()->getCodigoDane();
            //$infoInteresado['DATOS_REPRESENTANTE']= RepresentanteLegalPeer::getToArraySoap($interesado->getRepresentantelegalId());
            $infoInteresado['Direccion']= trim($interesado->getInteresados()->getDireccion()) ? (strtoupper($interesado->getInteresados()->getDireccion())) : "";
            $infoInteresado['Email']= trim($interesado->getInteresados()->getEmail()) ? strtoupper($interesado->getInteresados()->getEmail()) : "";
            //$infoInteresado['Fax']= $interesado->getInteresados()->getFax() ? $interesado->getInteresados()->getFax() : 0;
            $infoInteresado['Cedula']= trim($interesado->getInteresados()->getNumeroIdentificacion()) ? trim($interesado->getInteresados()->getNumeroIdentificacion()) : "";
            $infoInteresado['PrimerNombre']= trim($interesado->getInteresados()->getPrimerNombre()) ? (strtoupper($interesado->getInteresados()->getPrimerNombre())) : "";
            $infoInteresado['PrimerApellido']= trim($interesado->getInteresados()->getPrimerApellido()) ? (strtoupper($interesado->getInteresados()->getPrimerApellido())) : "";
            $infoInteresado['SegundoNombre']= trim($interesado->getInteresados()->getSegundoNombre()) ? (strtoupper($interesado->getInteresados()->getSegundoNombre())) : "";
            $infoInteresado['SegundoApellido']= trim($interesado->getInteresados()->getSegundoApellido()) ? (strtoupper($interesado->getInteresados()->getSegundoApellido())) : "";
            $infoInteresado['Telefono']= $interesado->getInteresados()->getTelefono() ? $interesado->getInteresados()->getTelefono() : 0;
            //$infoInteresado['TipoDocumento']= $interesado->getInteresados()->getTipoIdentificacion()->getCodigo();
            $infoInteresado['Genero']= $interesado->getInteresados()->getTipogeneroId();
            $infoInteresado['TipoPersona']= 1;
            //$listIntesados[] = $infoInteresado;
        }
        //***********************************************************************************
        //$infoRadSalida['InteresadoOferta'] = array('InteresadoOferta' => $listIntesados);
        $infoRadSalida['InteresadoOferta'] = $infoInteresado;
        //***********************************************************************************
        $options = Array(
            "uri"=> WsSimadUariv::SERVICE_URI,
            "style"=> SOAP_DOCUMENT,
            "use"=> SOAP_LITERAL,
            "soap_version"=> SOAP_1_1,
            "cache_wsdl"=> WSDL_CACHE_NONE,
            "timeout" => 300,
			"connection_timeout" => 300,
            "trace" => true,
            "encoding" => "UTF-8",
            /*'encoding'=>'ISO-8859-1',*/
            "exceptions" => true,
			'stream_context' => stream_context_create(WsSimadUariv::CONTEXT_SOAP2),
        );
        //************************************************************************************
        $client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
        //************************************************************************************
        try {
            //var_dump($infoRadSalida);exit;
            $infoComplex = array('credencial' =>$infoCredencial,'request' => $infoRadSalida);
            $response = $client->CrearExpedienteOferta($infoComplex);
            //********************************************************************************
            $a = new stdClass();
            $a = (object)$response->CrearExpedienteOfertaResponse;
            //********************************************************************************
            if($this->nulog){
                $this->writetolog(htmlspecialchars($client->__getLastRequest()),'xml');
            }
            //********************************************************************************
            if(trim($a->DESC_ERROR)){
                WebserviceLogPeer::addLogWs("CrearExpedienteOferta",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".trim($a->DESC_ERROR),1);
                return array('status'=>401,'message'=>sprintf("%s %s",$msgsing,trim($a->DESC_ERROR)));
            }else{
                WebserviceLogPeer::addLogWs("CrearExpedienteOferta",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".trim($a->RESULT),1);
                return array('status'=>200,'message'=>sprintf("%s, %s",$msgsing,'Los datos fueron enviados satisfactoriamente'));
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            WebserviceLogPeer::addLogWs("CrearExpedienteOferta",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada SoapFault => ".$radicado_com,"Error => ".$msgerror,1);
            //$this->writetolog($msgerror);
            //$this->writetolog(htmlspecialchars($client->__getLastRequest()),"xml" );
            //return array('status'=>400,'message'=>sprintf("%s %s",$msgsing,'Ocurrio un error al enviar los datos, error de protocolo'));
            file_put_contents(sfConfig::get("sf_log_dir")."\CrearExpedienteOferta.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES));
            return array('status'=>400,'message'=>sprintf("%s %s",$msgsing,$msgerror));
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            WebserviceLogPeer::addLogWs("CrearExpedienteOferta",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada Exception => ".$radicado_com,"Error => ".$msgerror,1);
			file_put_contents(sfConfig::get("sf_log_dir")."\CrearExpedienteOferta.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES));
            //$this->writetolog($msgerror);
            //$this->writetolog(htmlspecialchars($client->__getLastRequest()),"xml" );
            return array('status'=>400,'message'=>sprintf("%s %s",$msgsing,'Ocurrio un error al enviar la datos, error de servidor => ' . $msgerror));            
        }
    }

    /**
    * WsSimadUariv::loadWsRadActoAdministrativo()
    * consume servicio de fachada para servicio de sgv notificaciones
    * @return
    */
    public function loadWsRadActoAdministrativo($pkconsecutivo_id, $msgsing = null,$modulo_id = ModulesEnable::ComEnviada)
    {
        try
        {
            if($modulo_id == ModulesEnable::ComEnviada){
                return $this->loadWsRadActoAdminByComEnviada($pkconsecutivo_id, $msgsing);
            }elseif($modulo_id == ModulesEnable::ActosAdministrativos){
                return $this->loadWsRadActoAdminByActoAdministrativo($pkconsecutivo_id, $msgsing);
            }else{
                return array('status' => 400,'message' => 'Error de integraci&oacute;n, el registro no se debe enviar por servicios externos '.$msgsing);
            }
        }catch(PropelException $ex){
            return null;
            return array('status' => 400,'message' => 'Error de integraci&oacute;n, NO se envio la informaci&oacute;n al area de notificaciones,  '.$msgsing);
        }catch(\Exception $ex){
            return array('status' => 400,'message' => 'Error de integraci&oacute;n, NO se envio la informaci&oacute;n al area de notificaciones,  '.$msgsing);
        }catch(\Throwable $ex){
            return array('status' => 400,'message' => 'Error de integraci&oacute;n, NO se envio la informaci&oacute;n al area de notificaciones,  '.$msgsing);
        }
    }

    /**
    * WsSimadUariv::loadWsRadActoAdminByComEnviada()
    * consume servicio de fachada para envios de solicitudes a sgv notificaciones con comunicaciones enviadas
    * @return
    */
    private function loadWsRadActoAdminByComEnviada($comenviada_id, $msgsing=null)
    {
		$com_enviada = ComEnviadaPeer::retrieveByPK($comenviada_id);
		$com_recibida = !empty($com_enviada->getConsecutivoResp()) ? ComRecibidaPeer::retrieveByPK($com_enviada->getConsecutivoResp()) : null;
        $unidad_documental = null;
        //***************************************************************************************
		if(!empty($com_enviada->getExpedienteId())){
			$unidad_documental = UnidadDocumentalPeer::retrieveByPK($com_enviada->getExpedienteId());
		}elseif(!empty($com_enviada->getContenidodocId())){
			$contunidad_doc = ContenidoUnidadDocumentalPeer::retrieveByPK($com_enviada->getContenidodocId());			
			if($contunidad_doc != null){ $unidad_documental = UnidadDocumentalPeer::retrieveByPK($contunidad_doc->getUnidaddocumentalId()); }
		}
		//***************************************************************************************
		$radicado_com = $com_enviada != null ? trim($com_enviada->getRadicado()) : "";
		$folder_digit = ComEnviadaPeer::initFolderDigit($com_enviada->getRegionalId(),$com_enviada->getPeriodoId());
		$filepath_digit = ComEnviadaPeer::getDigitFormatFileExist($folder_digit['full_path'],$radicado_com);
        $error_image = null;
        //***************************************************************************************
        try{
            if(empty($filepath_digit)){
                if($com_enviada != null){
                    if(!empty($com_enviada->getUrlFileWord())){
                        $attach_url = $com_enviada->SimadGeneratePdf(trim($com_enviada->getUrlFileWord()),true);
                    }else{
                        $attach_url = $com_enviada->generateFileInDisk();
                    }
                }
                //*******************************************************************************
                if(file_exists($attach_url)){
                    $path_parts = pathinfo($attach_url);
                    $format = $path_parts['extension'];
                    $filename = sprintf("%s.%s",trim($radicado_com),$format);
                    $targetpath = $folder_digit['full_path'] . DIRECTORY_SEPARATOR . $filename;
                    if(!file_exists($targetpath)){
                        if(rename($attach_url,$targetpath)){
                            $filepath_digit = $targetpath;
                        }
                    }else{
                        $filepath_digit = $targetpath;
                    }
                }
            }
        }catch(PropelException $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }catch(\Exception $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }catch(\Exception $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }
		//***************************************************************************************
		try 
		{
			$list_interesados = array();
			foreach(ComEnviadaPeer::getListIntersadosByComId($com_enviada->getPrimaryKey()) as $item_com){
				$list_interesados[] = array('NOMBRE' => (strtoupper($item_com->getInteresados()->getNombreCompuesto())),'NUMERO_IDENTIFICACION' => trim($item_com->getInteresados()->getNumeroIdentificacion()),
											'TIPO_DOCUMENTO' => trim($item_com->getInteresados()->getTipoidentificacionId()));
			}
            //***********************************************************************************
            $suborigenid = !empty($com_enviada->getSuborigen()) ? $com_enviada->getSuborigen() : null;
            if(empty($suborigenid)){
                if(!empty($com_enviada->getPlantillascomId())){
                    $suborigenid = !empty($com_enviada->getPlantillasCom()->getSuborigenid()) ? $com_enviada->getPlantillasCom()->getSuborigenid() : null;
                }else{
                    $suborigenid = PlantillasComPeer::getSuborigenDefault();
                }
            }
			//***********************************************************************************
			$infoRadActoAdm = array();
            $infoRadActoAdm['NombrePersonaANotificar'] = (strtoupper($list_interesados[0]['NOMBRE']));
            $infoRadActoAdm['NumeroDocumentoPersonaANotificar'] = $list_interesados[0]['NUMERO_IDENTIFICACION'];
            $infoRadActoAdm['RadicadoArcadoc'] = $radicado_com;
            $infoRadActoAdm['RadicadoEntradaArcadoc'] = $com_recibida != null ? trim($com_recibida->getRadicado()) : "";
            $infoRadActoAdm['Expediente'] = $unidad_documental != null ? $unidad_documental->getPrimaryKey() : "";
            $infoRadActoAdm['NombreExpediente'] = $unidad_documental != null ? (strtoupper($unidad_documental->getTitulo())) : "";
            $infoRadActoAdm['DocumentoAsociado'] = simad_util::getConvertFileToB64($filepath_digit);
            $infoRadActoAdm['Serie'] = $unidad_documental != null ? trim($unidad_documental->getSubserie()->getSerie()->getCodigo()) : "";
            $infoRadActoAdm['Subserie'] = $unidad_documental != null ? trim($unidad_documental->getSubserie()->getCodigo()) : "";
            $infoRadActoAdm['TipoDocumental'] = trim($com_enviada->getTipoDocumentalCod());
            $infoRadActoAdm['IdTipoDocumentoPersonaANotificar'] = $list_interesados[0]['TIPO_DOCUMENTO'];
            $infoRadActoAdm['ConsecutivoRadicadoSalida'] = $com_enviada != null ? $com_enviada->getPrimaryKey() : 0;
			$infoRadActoAdm['FechaResolucion'] = $com_enviada != null ? $com_enviada->getFechaResolucion() : "";
            $infoRadActoAdm['NumeroResolucion'] = $com_enviada != null ? $com_enviada->getNumeroResolucion() : "";
            $infoRadActoAdm['SubOrigen'] = $suborigenid;
			//***********************************************************************************
			$options = Array(
				"uri"=> WsSimadUariv::SERVICE_URI,
				"style"=> SOAP_DOCUMENT,
				"use"=> SOAP_LITERAL,
				"soap_version"=> SOAP_1_1,
				"cache_wsdl"=> WSDL_CACHE_NONE,
				"timeout" => 300,
				"connection_timeout" => 300,
				"trace" => true,
				"encoding" => "UTF-8",
				/*'encoding'=>'ISO-8859-1',*/
				"exceptions" => true,
				'stream_context' => stream_context_create(WsSimadUariv::CONTEXT_SOAP2),
			);
			//************************************************************************************
			$client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
			//************************************************************************************
            $infoComplex = array('request' => $infoRadActoAdm);
            $response = $client->RadicarActoAdministrativo($infoComplex);
            //************************************************************************************
            $response_acto = (object)$response->RadicarActoAdministrativoResult;
            $estado_response = $response_acto->Estado;
            $message_response = $response_acto->Mensaje;
            $codigo_response = $response_acto->Codigo;
            //************************************************************************************
            if($this->nulog){
				file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES));
            }
            //************************************************************************************
            if(trim($estado_response) != "OK"){
                //$response_operation = sprintf("FirmaDigital => %s; Estado => %s; Codigo => %s; Mensaje => %s",trim($msgsing),trim($estado_response),trim($codigo_response),trim($message_response));
                $msg_error = !empty($message_response) ? ", ".trim($message_response) : "";
                WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".$message_response,1);       
                return array('status' => 400,'message' => 'Error de integraci&oacute;n, NO se envio informaci&oacute;n al area de notificaciones'.$msg_error);
            }else{
                $response_operation = sprintf("FirmaDigital => %s; Codigo => %s; Mensaje => Los datos fueron enviados satisfactoriamente",trim($msgsing),trim($codigo_response));
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".$response_operation,1);
                return array('status' => 200,'message' => 'Se envio la informaci&oacute;n al area de notificaciones');
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
			if(isset($client)){
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),false,"Consumen Fachada => ".$radicado_com,sprintf("Error SoapFault; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
				if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
			}else{
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo","ERROR CLIENTE SOAP",trim($msgerror),false,"Consumen Fachada => ".$radicado_com,sprintf("Error SoapFault; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
			}
			return array('status' => 400,'message' => 'Ocurrio un error al enviar los datos, error de protocolo SOAP');   
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
			if(isset($client)){
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,sprintf("Error Exception; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
				if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
			}else{
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo","ERROR EXCEPTION SERVICIO SGDEA",$msgerror,false,"Consumen Fachada => ".$radicado_com,sprintf("Error Exception; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
			}
            return array('status' => 400,'message' => 'Ocurrio un error al enviar la datos, error de servidor SGDEA');            
        }
    }

    /**
    * WsSimadUariv::loadWsRadActoAdminByActos()
    * consume servicio de fachada para envios de solicitudes a sgv notificaciones desde actos adminstrativos
    * @return
    */
    private function loadWsRadActoAdminByActoAdministrativo($actoadministrativo_id, $msgsing=null)
    {
		$acto_administrativo = ActoAdministrativoPeer::retrieveByPK($actoadministrativo_id);
		$unidad_documental = !empty($acto_administrativo->getExpedienteId()) ? UnidadDocumentalPeer::retrieveByPK($acto_administrativo->getExpedienteId()) : null;
        $radicado_com = $acto_administrativo != null ? trim($acto_administrativo->getRadicadoCompuesto()) : "";
		$radicado_fname = $acto_administrativo != null ? trim($acto_administrativo->getRadicadoCustom()) : "";
		//***************************************************************************************
        $tracker = new SgdeaTrackerMetrics([
            'documento_id' => basename($radicado_com),
            'usuario_id' => $acto_administrativo->getPrimaryKey(),
            'transaccion_id' => uniqid('firma_', true),
            'log_name' => 'fachada_servicios_timers.log'
        ]);
		//***************************************************************************************
		$folder_digit = ActoAdministrativoPeer::initFolderDigit($acto_administrativo->getRegionalId(),$acto_administrativo->getPeriodoId());
		$filepath_digit = ActoAdministrativoPeer::getDigitFormatFileExist($folder_digit['full_path'],$radicado_fname);
        $error_image = null;
        //***************************************************************************************
        try{
            $tracker->mark('inicio');
            //***********************************************************************************
            if(!file_exists($filepath_digit)){
                if($acto_administrativo != null){
                    if(!empty($acto_administrativo->getUrlFileWord())){
                        $attach_url = $acto_administrativo->generatePdfByFile(trim($acto_administrativo->getUrlFileWord()),true);
                    }else{
                        $attach_url = $acto_administrativo->generateFileInDisk();
                    }
                }
                //*******************************************************************************
                if(file_exists($attach_url)){
                    $path_parts = pathinfo($attach_url);
                    $format = $path_parts['extension'];
                    $filename = sprintf("%s.%s",trim($radicado_fname),$format);
                    $targetpath = $folder_digit['full_path'] . DIRECTORY_SEPARATOR . $filename;
                    if(!file_exists($targetpath)){
                        if(rename($attach_url,$targetpath)){
                            $filepath_digit = $targetpath;
                        }
                    }else{
                        $filepath_digit = $targetpath;
                    }
                }
            }
        }catch(PropelException $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }catch(\Exception $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }catch(\Exception $ex){
            $error_image = "Ocurrio un error con la generacion del archivo asociado a la comunicación";
        }
		//***************************************************************************************
		try 
		{
			$list_interesados = array();
			foreach(ActoAdministrativoPeer::getListIntersadosByActoId($acto_administrativo->getPrimaryKey()) as $item_com){
				$list_interesados[] = array('NOMBRE' => (strtoupper($item_com->getInteresados()->getNombreCompuesto())),'NUMERO_IDENTIFICACION' => trim($item_com->getInteresados()->getNumeroIdentificacion()),
											'TIPO_DOCUMENTO' => trim($item_com->getInteresados()->getTipoidentificacionId()));
			}
            //***********************************************************************************
            $suborigenid = !empty(trim($acto_administrativo->getSuborigen())) ? trim($acto_administrativo->getSuborigen()) : null;
            if(empty($suborigenid)){
                if(!empty($acto_administrativo->getPlantillascomId())){
                    $suborigenid = !empty($acto_administrativo->getPlantillasCom()->getSuborigenid()) ? $acto_administrativo->getPlantillasCom()->getSuborigenid() : null;
                }else{
                    //$suborigenid = PlantillasComPeer::getSuborigenDefault();
                    $suborigenid = null;
                }
            }
			//***********************************************************************************
			$infoRadActoAdm = array();
            $infoRadActoAdm['NombrePersonaANotificar'] = (strtoupper($list_interesados[0]['NOMBRE']));
            $infoRadActoAdm['NumeroDocumentoPersonaANotificar'] = $list_interesados[0]['NUMERO_IDENTIFICACION'];
            $infoRadActoAdm['RadicadoArcadoc'] = $radicado_com;
            $infoRadActoAdm['RadicadoEntradaArcadoc'] = "";
            $infoRadActoAdm['Expediente'] = $unidad_documental != null ? $unidad_documental->getPrimaryKey() : "";
            $infoRadActoAdm['NombreExpediente'] = $unidad_documental != null ? (strtoupper($unidad_documental->getTitulo())) : "";
            $infoRadActoAdm['DocumentoAsociado'] = simad_util::getConvertFileToB64($filepath_digit);
            $infoRadActoAdm['Serie'] = $unidad_documental != null ? trim($unidad_documental->getSubserie()->getSerie()->getCodigo()) : "";
            $infoRadActoAdm['Subserie'] = $unidad_documental != null ? trim($unidad_documental->getSubserie()->getCodigo()) : "";
            $infoRadActoAdm['TipoDocumental'] = trim($acto_administrativo->getTipoDocumentalCod());
            $infoRadActoAdm['IdTipoDocumentoPersonaANotificar'] = $list_interesados[0]['TIPO_DOCUMENTO'];
            $infoRadActoAdm['ConsecutivoRadicadoSalida'] = $acto_administrativo != null ? $acto_administrativo->getPrimaryKey() : 0;
			$infoRadActoAdm['FechaResolucion'] = $acto_administrativo != null ? $acto_administrativo->getFechaResolucion() : "";
            $infoRadActoAdm['NumeroResolucion'] = $acto_administrativo != null ? $acto_administrativo->getNumeroResolucion() : "";
            $infoRadActoAdm['SubOrigen'] = $suborigenid;
			//***********************************************************************************
            $tracker->mark('antes_firma');
			//***********************************************************************************
			$options = Array(
				"uri"=> WsSimadUariv::SERVICE_URI,
				"style"=> SOAP_DOCUMENT,
				"use"=> SOAP_LITERAL,
				"soap_version"=> SOAP_1_1,
				"cache_wsdl"=> WSDL_CACHE_NONE,
				"timeout" => 300,
				"connection_timeout" => 300,
				"trace" => true,
				"encoding" => "UTF-8",
				/*'encoding'=>'ISO-8859-1',*/
				"exceptions" => true,
				'stream_context' => stream_context_create(WsSimadUariv::CONTEXT_SOAP2),
			);
			//************************************************************************************
			$client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
			//************************************************************************************
            $infoComplex = array('request' => $infoRadActoAdm);
            $response = $client->RadicarActoAdministrativo($infoComplex);
            //************************************************************************************
            $response_acto = (object)$response->RadicarActoAdministrativoResult;
            $estado_response = $response_acto->Estado;
            $message_response = $response_acto->Mensaje;
            $codigo_response = $response_acto->Codigo;
            //************************************************************************************
            $tracker->mark('despues_firma', [
                'status_code' => trim($estado_response) != "OK" ?? "ERROR",
                'duration_ms' => ($tracker->steps['despues_firma']['elapsed'] - $tracker->steps['antes_firma']['elapsed']) * -1000
            ]);
            //************************************************************************************
            if($this->nulog){
				file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES));
            }
            //************************************************************************************
            if(trim($estado_response) != "OK"){
                //$response_operation = sprintf("FirmaDigital => %s; Estado => %s; Codigo => %s; Mensaje => %s",trim($msgsing),trim($estado_response),trim($codigo_response),trim($message_response));
                $msg_error = !empty($message_response) ? ", ".trim($message_response) : "";
                WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".$message_response,1);
                //********************************************************************************
                $tracker->mark('error');
                $tracker->finish();
                //********************************************************************************
                return array('status' => 400,'message' => 'Error de integraci&oacute;n, NO se envio informaci&oacute;n al area de notificaciones'.$msg_error);
            }else{
                $response_operation = sprintf("FirmaDigital => %s; Codigo => %s; Mensaje => Los datos fueron enviados satisfactoriamente",trim($msgsing),trim($codigo_response));
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".$response_operation,1);
                //********************************************************************************
                $tracker->mark('exito');
                $tracker->finish();
                //********************************************************************************
                return array('status' => 200,'message' => 'Se envio la informaci&oacute;n al area de notificaciones');
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
			if(isset($client)){
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),false,"Consumen Fachada => ".$radicado_com,sprintf("Error SoapFault; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
				if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
			}else{
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo","ERROR CLIENTE SOAP",trim($msgerror),false,"Consumen Fachada => ".$radicado_com,sprintf("Error SoapFault; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
			}
			return array('status' => 400,'message' => 'Ocurrio un error al enviar los datos, error de protocolo SOAP');   
        } catch (\Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
			if(isset($client)){
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,sprintf("Error Exception; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
				if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
			}else{
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo","ERROR EXCEPTION SERVICIO SGDEA",$msgerror,false,"Consumen Fachada => ".$radicado_com,sprintf("Error Exception; FirmaDigital => %s; Error => %s",trim($msgsing),trim($msgerror)),1);
			}
            return array('status' => 400,'message' => 'Ocurrio un error al enviar la datos, error de servidor SGDEA');            
        }
    }

    /**
    * WsSimadUariv::initLinkResponseCom()
    * asocia la comunicacion recibida con la respuesta enviada generada
    * funcion por lotes    
    * @return
    */
    public function initLinkResponseCom($rows = 2000)
	{
        /*SELECT COM_ENVIADA.COMENVIADA_ID, COM_RECIBIDA.COMRECIBIDA_ID,COM_ENVIADA.CONSECUTIVO_RESP, COM_RECIBIDA.COMENVIADA_ID,COM_ENVIADA.CONTENIDODOC_ID
        FROM COM_ENVIADA 
        JOIN COM_RECIBIDA ON COM_ENVIADA.CONSECUTIVO_RESP = COM_RECIBIDA.COMRECIBIDA_ID
        where COM_ENVIADA.CONSECUTIVO_RESP > 0 AND COM_ENVIADA.CONTENIDODOC_ID IS NULL AND COM_ENVIADA.TIPO_INTEGRACION = 'DEMANDA'
        AND COM_RECIBIDA.COMENVIADA_ID IS NULL*/
		//***************************************************************************************
        $c = new Criteria();
		$c->setLimit($rows);
        $c->addJoin(ComEnviadaPeer::CONSECUTIVO_RESP,ComRecibidaPeer::COMRECIBIDA_ID);
        $c->add(ComEnviadaPeer::CONSECUTIVO_RESP,0,Criteria::GREATER_THAN);
        $c->add(ComEnviadaPeer::CONTENIDODOC_ID,null,Criteria::ISNULL);
        $c->add(ComEnviadaPeer::TIPO_INTEGRACION,'DEMANDA');
        $c->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
		$c->add(ComEnviadaPeer::PERIODO_ID,2023);
        //***************************************************************************************
        $c->clearSelectColumns();
        $c->addSelectColumn(ComEnviadaPeer::COMENVIADA_ID);//0
        $c->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);//1
        $c->addSelectColumn(ComEnviadaPeer::CONSECUTIVO_RESP);//2
        $c->addSelectColumn(ComEnviadaPeer::CONTENIDODOC_ID);//3
        $c->addSelectColumn(ComRecibidaPeer::COMENVIADA_ID);//4
        //***************************************************************************************
        $stmt  = ComEnviadaPeer::doSelectStmt($c);
        $objects_com = $stmt->fetchAll();
        print('TOTAL REGISTROS => '.count($objects_com). PHP_EOL);
        //***************************************************************************************
        $estado_respondida = 5;$origentransfer_id = 3;$item = 1;
        foreach ($objects_com as $object_info)
        {
			$com_recibida_resp = null;
			$firmante_id = null;
			$com_enviada = null;
			$contenidodoc_id = null;
            try {
                if(!empty($object_info[2])){
                    $com_recibida_resp = ComRecibidaPeer::retrieveByPk($object_info[2]);
					if($com_recibida_resp->getEstadocomrecibidaId() == 14){ continue; }
                    $com_recibida_resp->setEstadocomrecibidaId($estado_respondida);
                    $com_recibida_resp->setComenviadaId($object_info[0]);
                    $com_recibida_resp->save();
					//***********************************************************************************************************
					print(($item++).'. RADICADO ENTRADA => '.$com_recibida_resp->getRadicado(). PHP_EOL);
                    //***********************************************************************************************************
                    ComRecibidaPeer::updateEstadosComRecibida($com_recibida_resp->getPrimaryKey(),$estado_respondida);
                    //***********************************************************************************************************
                    if(!empty($com_recibida_resp->getContenidodocId())){
                        $com_enviada = ComEnviadaPeer::retrieveByPk($object_info[0]);
                        if($com_enviada != null){
                            $firmante_id = EnviadaUsuarioPeer::getFirstUsurioFirma($object_info[0]);
                            $contenidodoc_id = $com_recibida_resp->getContenidodocId();
                            $com_enviada->addNewTransferenciaAuto($contenidodoc_id,$origentransfer_id,$firmante_id);
                        }
                    }
                }
            } catch (PropelException $px) {
                return false;
            } catch (Throwable $th) {
                return false;
            } catch (Exception $ex) {
                return false;
            }
        }
		//************************************************************************************************************************
		return true;
	}

    /**
    * send_alert::loadWsRadActoAdmByNitCom()
    * consume servicio de fachada para servicio de sgv notificaciones cuando no ingresa por comunicaciones
    * @return
    */
    public function loadWsRadActoAdmByNotCom($infodata,$interesados_coll)
    {
		$radicado_com = isset($infodata['radicado_origen']) ? trim($infodata['radicado_origen']) : $infodata['radicado'];
        $suborigenid = isset($infodata['suborigen']) ? $infodata['suborigen'] : null;
        $radicado_entrada = isset($infodata['radicado_entrada']) ? $infodata['radicado_entrada'] : "";
		//***************************************************************************************
		try 
		{
			$list_interesados = array();
			foreach($interesados_coll as $item_com){
				$list_interesados[] = array('NOMBRE' => (strtoupper($item_com->getInteresados()->getNombreCompuesto())),'NUMERO_IDENTIFICACION' => trim($item_com->getInteresados()->getNumeroIdentificacion()),
											'TIPO_DOCUMENTO' => trim($item_com->getInteresados()->getTipoidentificacionId()));
			}                        
			//***********************************************************************************
			$infoRadActoAdm = array();
            $infoRadActoAdm['NombrePersonaANotificar'] = $list_interesados[0]['NOMBRE'];
            $infoRadActoAdm['NumeroDocumentoPersonaANotificar'] = $list_interesados[0]['NUMERO_IDENTIFICACION'];
            $infoRadActoAdm['RadicadoArcadoc'] = $radicado_com;
            $infoRadActoAdm['RadicadoEntradaArcadoc'] = $radicado_entrada;
            $infoRadActoAdm['Expediente'] = isset($infodata['expediente_id']) ? $infodata['expediente_id'] : "";
            $infoRadActoAdm['NombreExpediente'] = isset($infodata['titulo_exp']) ? $infodata['titulo_exp'] : "";
            $infoRadActoAdm['DocumentoAsociado'] = $infodata['archivo_digit'];
            $infoRadActoAdm['Serie'] = isset($infodata['serie_cod']) ? $infodata['serie_cod'] : "";
            $infoRadActoAdm['Subserie'] = isset($infodata['subserie_cod']) ? $infodata['subserie_cod'] : "";
            $infoRadActoAdm['TipoDocumental'] = isset($infodata['tipo_documental_cod']) ? $infodata['tipo_documental_cod'] : "";
            $infoRadActoAdm['IdTipoDocumentoPersonaANotificar'] = $list_interesados[0]['TIPO_DOCUMENTO'];
            $infoRadActoAdm['ConsecutivoRadicadoSalida'] = isset($infodata['consecutivo_salida']) ? $infodata['consecutivo_salida'] : "";
			$infoRadActoAdm['FechaResolucion'] = isset($infodata['fecha_resolucion']) ? $infodata['fecha_resolucion'] : "";
            $infoRadActoAdm['NumeroResolucion'] = isset($infodata['numero_resolucion']) ? $infodata['numero_resolucion'] : "";
            $infoRadActoAdm['SubOrigen'] = $suborigenid;
			//***********************************************************************************
			$options = Array(
				"uri"=> WsSimadUariv::SERVICE_URI,
				"style"=> SOAP_DOCUMENT,
				"use"=> SOAP_LITERAL,
				"soap_version"=> SOAP_1_1,
				"cache_wsdl"=> WSDL_CACHE_NONE,
				"timeout" => 300,
				"connection_timeout" => 300,
				"trace" => true,
				"encoding" => "UTF-8",
				/*'encoding'=>'ISO-8859-1',*/
				"exceptions" => true,
			);
			//************************************************************************************
			$client = new SoapClientExtended(WsSimadUariv::SERVICE_URL,$options);
			//************************************************************************************
            $infoComplex = array('request' => $infoRadActoAdm);
            $response = $client->RadicarActoAdministrativo($infoComplex);
            //************************************************************************************
            $response_acto = (object)$response->RadicarActoAdministrativoResult;
            $estado_response = $response_acto->Estado;
            $message_response = $response_acto->Mensaje;
            $codigo_response = $response_acto->Codigo;
            //************************************************************************************
            if(trim($estado_response) != "OK"){
                //$response_operation = sprintf("FirmaDigital => %s; Estado => %s; Codigo => %s; Mensaje => %s",trim($msgsing),trim($estado_response),trim($codigo_response),trim($message_response));
                $msg_error = !empty($message_response) ? ", ".trim($message_response) : "";
                WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Error Fachada => ".$message_response,1);       
                return array('status' => 400,'message' => 'Error de integración, NO se envio información al area de notificaciones'.$msg_error);
            }else{
                $response_operation = sprintf("Codigo => %s; Mensaje => Los datos fueron enviados satisfactoriamente",trim($codigo_response));
				WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,"Enviado Exitoso => ".$response_operation,1);
                return array('status' => 200,'message' => 'Se envio la información al area de notificaciones');
            }
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,sprintf("Error SoapFault => %s",trim($msgerror)),1);
			if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
			return array('status' => 400,'message' => 'Ocurrio un error al enviar los datos, error de con la herramienta de gestión, '.$e->faultstring);   
        } catch (Exception $e) {
            $msgerror = "Error Exception: ".$e->getMessage();
            WebserviceLogPeer::addLogWs("RadicarActoAdministrativo",htmlspecialchars($client->__getLastRequest()),htmlspecialchars($client->__getLastResponse()),true,"Consumen Fachada => ".$radicado_com,sprintf("Error Exception => %s",trim($msgerror)),1);
			if($this->nulog){ file_put_contents(sfConfig::get("sf_log_dir")."\RadicarActoAdministrativo.log",htmlspecialchars($client->__getLastRequest(), ENT_QUOTES)); }
            return array('status' => 400,'message' => 'Ocurrio un error al enviar la datos, error de servidor SGDEA. '.$e->getMessage());            
        }
    }
}

class SoapClientExtended extends SoapClient
{
    const MAX_FILE_SIZE_MESSAGE = 189999999;
    const SOAPCLI = DIRECTORY_SEPARATOR.'SgdeaCliSoap'.DIRECTORY_SEPARATOR.'SgdeaCliTools.exe';

    var $pkregid = null;
    var $com_radicado = null;
    var $istest = null;

    private $lastRawFaultResponse = null;

    public function getLastRawFaultResponse(): ?string
    {
        return $this->lastRawFaultResponse;
    }

    /**
     * Set values for integration
     *
     * @param int $compkid  id del registro a enviar por soap
     * @param string $radicado_com radicado de la comunicación o codigo del expediente
     * @param string $istest si se usa servicio de pruebas o produccion true|false por defecto false
     * @return string The XML SOAP response.
     */
    public function setComOrExpVars($pkId, $comrad, $istest = true)
    {
        $this->pkregid = $pkId;
        $this->com_radicado = $comrad;
        $this->istest = $istest;
    }

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
        $this->lastRawFaultResponse = null;
        $curlResult = $this->doRequestCurl($request, $location, $action);

        if ($curlResult['httpCode'] >= 400) {
            // Guardar para el catch externo
            $this->lastRawFaultResponse = $curlResult['body'];
            
            // Lanzar SoapFault para que el catch externo lo maneje
            throw new SoapFault('HTTP', 'HTTP ' . $curlResult['httpCode'] . ' Error');
        }

        $xmlBody = $this->extractXmlFromMultipart($curlResult['body']);

        return !empty($xmlBody) ? $xmlBody : $curlResult['body'];
    }

    /**
     * Realiza la petición SOAP vía cURL capturando SIEMPRE el body de respuesta,
     * incluso ante HTTP 500
     */
    private function doRequestCurl(string $request, string $location, string $action): array
    {
        try {
            $purl    = parse_url($location);
            $headers = [
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Host: " . $purl['host'],
                "Content-length: " . strlen($request),
                "SOAPAction: " . $action,
            ];

            $ch = curl_init($location);
            curl_setopt($ch, CURLOPT_URL,            $location);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT,        300);
            curl_setopt($ch, CURLOPT_POST,           true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $request);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
            // MUY IMPORTANTE: capturar el body aunque sea HTTP 4xx/5xx
            curl_setopt($ch, CURLOPT_FAILONERROR,    false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_errno($ch) ? curl_error($ch) : null;
            curl_close($ch);

            if ($curlError) {
                throw new SoapFault('Client', 'Error: ' . $curlError);
            }

            return [
                'httpCode' => $httpCode,
                'body'     => $response ?: '',
            ];

        } catch (Exception $e) {
            return [
                'httpCode' => 500,
                'body'     => $e->getMessage(),
            ];
        }
    }

    public function doRequestClientCli()
    {
        require_once(sfConfig::get('sf_lib_dir').'/ShellWrapper/autoload.php');
        //*********************************************************************************************
        try
        {
            $response = null;
            $cli = sfConfig::get('sf_lib_dir').self::SOAPCLI;
            
            $shell = new Exec();
            $command = new CommandBuilder($cli);
            $command->addArgument('comrecibida', $this->pkregid);
            $command->addArgument('radicado_com', $this->com_radicado);
            $command->addArgument('istest', ($this->istest ? "true" : "false"));

            $shell->run($command);
            //$ccm = print_r($command);

            $response = $shell->getOutput();
            //echo $outvalue = $shell->getReturnValue();

            if(!empty($response)){
                if(is_array($response)){ $response = $response[0]; }
            }else{
                $response = '<codigo>400</codigo><mensaje>Ocurrio un error interno en el servidor, error de integracion</mensaje>';
            }

            return $response;
        }catch (Exception $e) {
            $msgerror = "Error Exception:<br /><br />Error Details:<br />". $e->getMessage() . "<br />";            
            return $msgerror;
        }
    }

    private function doRequestCurlCustom($request, $location, $action, $version, $one_way = 0)
    {
        try{
                $purl = parse_url($location);
                $headers = array(
                    "Content-Type: text/xml; charset=utf-8",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Host: ".$purl['host'],
                    "Content-length: ".strlen($request),
                    "SOAPAction: ".$action
                );
                
                $curl = WsSimadUariv::SERVICE_URL;  // WSDL web service url for request method/function
				$ch = curl_init($curl);
                curl_setopt($ch, CURLOPT_URL, $curl);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request); // the SOAP request
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($ch);
                
                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                }
                
                curl_close($ch);

                $xml_response = null;

                if(!isset($error_msg)){
                    // Catch XML response
                    preg_match('/<s[\s\S]*nvelope>/', $response, $xml_response);
                $result = $xml_response[0];

                    // Do we have a multipart request?
                    if (preg_match('#^Content-Type:.*multipart\/.*#mi', $result) !== 0) {
                        // Make all line breaks even.
                        $result = str_replace("\r\n", "\n", $result);

                        // Split between headers and content.
                        list(, $content) = preg_split("#\n\n#", $result);
                        // Split again for multipart boundary.
                        list($result, ) = preg_split("#\n--#", $content);
                    }
            }    
        }catch (SoapFault $e){
            $msgerror =  "SoapFault Error:<br />" . nl2br($e->faultcode) . '<br /><br />Error Details:<br />'. nl2br($e->faultstring) . '<br />';
            return $msgerror;
        } catch (Exception $e) {
            $msgerror = "Error Exception:<br />" . nl2br($e->getMessage()) . '<br /><br />Error Details:<br />'. nl2br($e->getMessage()) . '<br />';
            return $msgerror;
        }
    }

    public function extractFaultFromSoapResponse(string $rawResponse): ?string
    {
        try {
            libxml_use_internal_errors(true);

            // Limpiar la respuesta: puede venir como multipart/XOP
            // Extraer solo la parte XML del envelope SOAP
            $xmlContent = $this->extractXmlFromMultipart($rawResponse);

            if (empty($xmlContent)) {
                return null;
            }

            $xml = new \SimpleXMLElement($xmlContent);

            $xml->registerXPathNamespace('s',   'http://schemas.xmlsoap.org/soap/envelope/');
            $xml->registerXPathNamespace('s12', 'http://www.w3.org/2003/05/soap-envelope');

            // SOAP 1.1
            $faultNodes = $xml->xpath('//s:Body/s:Fault/faultstring');

            // SOAP 1.2 fallback
            if (empty($faultNodes)) {
                $faultNodes = $xml->xpath('//s12:Body/s12:Fault/s12:Reason/s12:Text');
            }

            if (!empty($faultNodes)) {
                $faultMsg  = (string) $faultNodes[0];
                $codeNodes = $xml->xpath('//s:Body/s:Fault/faultcode');
                if (!empty($codeNodes)) {
                    return '[' . (string)$codeNodes[0] . '] ' . $faultMsg;
                }
                return $faultMsg;
            }

            return null;

        } catch (\Exception $e) {
            return null;
        } finally {
            libxml_clear_errors();
        }
    }

    /**
     * Extrae el XML SOAP puro de una respuesta que puede ser:
     * - XML puro directamente
     * - Multipart/XOP con boundaries
     */
    private function extractXmlFromMultipart(string $rawResponse): ?string
    {
        $rawResponse = trim($rawResponse);

        // Caso 1: Ya es XML puro, empieza con '<'
        if (strpos($rawResponse, '<') === 0) {
            return $rawResponse;
        }

        // Caso 2: Es multipart — buscar directamente el bloque <s:Envelope...>...</s:Envelope>
        // Esta regex es robusta ante cualquier variante de namespace (s:, soap:, SOAP-ENV:, etc.)
        if (preg_match('/<(?:[a-zA-Z0-9_-]+:)?Envelope[\s\S]*?<\/(?:[a-zA-Z0-9_-]+:)?Envelope>/i', $rawResponse, $matches)) {
            return $matches[0];
        }

        // Caso 3: Intentar extraer entre los boundary markers del multipart
        // Normalizar saltos de línea
        $normalized = str_replace("\r\n", "\n", $rawResponse);

        // Saltar la primera línea de boundary (--uuid:...)
        $parts = preg_split('/^--[^\n]+$/m', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $part = trim($part);
            // Separar headers de la parte multipart del contenido
            if (strpos($part, "\n\n") !== false) {
                list(, $content) = explode("\n\n", $part, 2);
                $content = trim($content);
                if (strpos($content, '<') === 0) {
                    return $content;
                }
            }
        }

        return null;
    }
}
?>