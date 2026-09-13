<?php
  
	require_once('../lib/searchImgByRad.php');
	require_once('../lib/nusoap-1.124/autoload.php');
	//require_once('../lib/nusoap-0.9.5/lib/nusoap.php');
	
	$ns = 'urn:SimadEnterpriseSgdea';  
	$server = new soap_server();
	$server->configureWSDL('SimadEnterpriseSgdea',$ns);
	$server->soap_defencoding = 'UTF-8';
	$server->decode_utf8 = false;
	$server->encode_utf8 = true;
	$server->wsdl->schemaTargetNamespace = $ns;
	//*************************************************************************************************************************
	$security_incoming = array ( 'SimadUserWs' => array( 'name'=>'SimadUserWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadPassWs' => array( 'name'=>'SimadPassWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadTypeGen' => array( 'name'=>'SimadTypeGen', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSSimadSecurity','complexType','struct','all','',$security_incoming);
	//*************************************************************************************************************************
	$remitente_response = array ('CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CODIGO_DEPARTAMENTO' => array( 'name'=>'CODIGO_DEPARTAMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPARTAMENTO' => array( 'name'=>'DEPARTAMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CODIGO_PAIS' => array( 'name'=>'CODIGO_PAIS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PAIS' => array( 'name'=>'PAIS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE' => array( 'name'=>'NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FUNCIONARIO' => array( 'name'=>'FUNCIONARIO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CARGO' => array( 'name'=>'CARGO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PREFIJO' => array( 'name'=>'PREFIJO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSRemitenteEnt','complexType','struct','sequence','',$remitente_response);
	//*************************************************************************************************************************
	$representante_response = array (		
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPARTAMENTO' => array( 'name'=>'DEPARTAMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPARTAMENTO_CODIGO' => array( 'name'=>'DEPARTAMENTO_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PAIS' => array( 'name'=>'PAIS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PAIS_CODIGO' => array( 'name'=>'PAIS_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)		
	);
	$server->wsdl->addComplexType('WSRepresentanteInteresadoEnt','complexType','struct','all','',$representante_response);
	//*************************************************************************************************************************
	$expediente_response = array ('CODIGO_DEPENDENCIA' => array( 'name'=>'CODIGO_DEPENDENCIA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CODIGO_SERIE_DOCUMENTAL' => array( 'name'=>'CODIGO_SERIE_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_SUBSERIE_DOCUMENTAL' => array( 'name'=>'CODIGO_SUBSERIE_DOCUMENTAL' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ESTADO' => array( 'name'=>'ESTADO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'ID_EXPEDIENTE' => array( 'name'=>'ID_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE_DEPENDENCIA' => array( 'name'=>'NOMBRE_DEPENDENCIA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE_EXPEDIENTE' => array( 'name'=>'NOMBRE_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE_SERIE_DOCUMENTAL' => array( 'name'=>'NOMBRE_SERIE_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE_SUBSERIE_DOCUMENTAL' => array( 'name'=>'NOMBRE_SUBSERIE_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_EXPEDIENTE' => array( 'name'=>'NUMERO_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1)		
	);
	$server->wsdl->addComplexType('WSExpedienteCom','complexType','struct','all','',$expediente_response);
	//*************************************************************************************************************************
	$interesados_response = array (
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPARTAMENTO' => array( 'name'=>'DEPARTAMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPARTAMENTO_CODIGO' => array( 'name'=>'DEPARTAMENTO_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PAIS' => array( 'name'=>'PAIS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PAIS_CODIGO' => array( 'name'=>'PAIS_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'FAX' => array( 'name'=>'FAX', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'INTR_INFO_REPRESENTANTE' => array( 'name'=>'INTR_INFO_REPRESENTANTE', 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
		'INFO_REPRESENTANTE' => array( 'name'=>'INFO_REPRESENTANTE', 'type'=>'tns:WSRepresentanteInteresadoEnt', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSInteresadoEnt','complexType','struct','all','',$interesados_response);
	$server->wsdl->addComplexType('InteresadoEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSInteresadoEnt[]')),'tns:WSInteresadoEnt');
	//*************************************************************************************************************************
	$comrecibida_response = array ('COMRECIBIDA_ID' => array( 'name'=>'COMRECIBIDA_ID', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
		'REGIONAL_RADICACION' => array( 'name'=>'REGIONAL_RADICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_DOCUMENTO' => array( 'name'=>'TIPO_DOCUMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CLASIFICACION_DOCUMENTO' => array( 'name'=>'CLASIFICACION_DOCUMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FORMA_RECEPCION' => array( 'name'=>'FORMA_RECEPCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPENDENCIA_DESTINO' => array( 'name'=>'DEPENDENCIA_DESTINO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPENDENCIA_CODIGO' => array( 'name'=>'DEPENDENCIA_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PRIORIDAD_COM' => array( 'name'=>'PRIORIDAD_COM', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ARCHIVO_DIGIT' => array( 'name'=>'ARCHIVO_DIGIT', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ARCHIVO_NOMBRE' => array( 'name'=>'ARCHIVO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ASUNTO' => array( 'name'=>'ASUNTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FOLIOS' => array( 'name'=>'FOLIOS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'RADICADO_ORIGEN' => array( 'name'=>'RADICADO_ORIGEN', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO' => array( 'name'=>'RADICADO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'ANEXOS' => array( 'name'=>'ANEXOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'OBSERVACIONES' => array( 'name'=>'OBSERVACIONES', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'FECHA_VENCIMIENTO' => array( 'name'=>'FECHA_VENCIMIENTO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'FECHA_LLEGADA' => array( 'name'=>'FECHA_LLEGADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_PROCESO' => array( 'name'=>'NUMERO_PROCESO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_FUD' => array( 'name'=>'NUMERO_FUD', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_GUIA' => array( 'name'=>'NUMERO_GUIA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'DEPARTAMENTO_CODIGO' => array( 'name'=>'DEPARTAMENTO_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'MARCONORMATIVO' => array( 'name'=>'MARCONORMATIVO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE_EXPEDIENTE' => array( 'name'=>'NOMBRE_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_DECLARACION' => array( 'name'=>'NUMERO_DECLARACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ID_JUZGADO' => array( 'name'=>'ID_JUZGADO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'MARCA_VINCULACION' =>  array( 'name'=>'MARCA_VINCULACION' , 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ID_GESTOR' => array( 'name'=>'ID_GESTOR', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'USUARIO_RADICADOR' => array( 'name'=>'USUARIO_RADICADOR', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'INTERESADOS_COM' => array( 'name'=>'INTERESADOS_COM' , 'type'=>'tns:InteresadoEntOfList'),
		'REMITENTE_COM' => array( 'name'=>'REMITENTE_COM' , 'type'=>'tns:WSRemitenteEnt'),
		'EXPEDIENTE_COM' => array( 'name'=>'REMITENTE_COM' , 'type'=>'tns:WSExpedienteCom'),
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSG_INFO' => array( 'name'=>'MSG_INFO' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('ComInfoMetaData','complexType','struct','all','',$comrecibida_response);
	//*************************************************************************************************************************
	$info_incoming = array (		
		'CONSECUTIVO_ENTRADA' => array( 'name'=>'CONSECUTIVO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CONSECUTIVO_SALIDA' => array( 'name'=>'CONSECUTIVO_SALIDA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_ENTRADA' => array( 'name'=>'RADICADO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_SALIDA' => array( 'name'=>'RADICADO_SALIDA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSComSearchInfo','complexType','struct','all','',$info_incoming);
	//*************************************************************************************************************************
	$server->register('GetMetadataInfoCom',array('EntSecurity' => 'tns:WSSimadSecurity','ComSearchInfo' => 'tns:WSComSearchInfo'), array('return' => 'tns:ComInfoMetaData'), $ns, $ns.'#GetMetadataInfoCom', false, false, 'Metodo para obtener los metadatos de un radicado del SGDEA');
	//*************************************************************************************************************************
	//$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	//$server->service($HTTP_RAW_POST_DATA);
	//*************************************************************************************************************************
	$contens = file_get_contents("php://input");
	@$server->service($contens);
?>