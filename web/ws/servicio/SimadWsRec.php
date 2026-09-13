<?php
  
	require_once('../lib/searchImgByRad.php');
	require_once('../lib/nusoap-1.124/autoload.php');
	//require_once('../lib/nusoap-0.9.5/lib/nusoap.php');
	
	$ns = 'urn:SimadEnterpriseSgdea';  
	$server = new soap_server();
	$server->configureWSDL('SimadEnterpriseSgdea',$ns);
	$server->soap_defencoding = 'UTF-8';
	$server->decode_utf8 = false;
	//$server->encode_utf8 = true;
	$server->wsdl->schemaTargetNamespace = $ns;
	//*************************************************************************************************************************
	$security_incoming = array ( 'SimadUserWs' => array( 'name'=>'SimadUserWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadPassWs' => array( 'name'=>'SimadPassWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadTypeGen' => array( 'name'=>'SimadTypeGen', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSSimadSecurity','complexType','struct','all','',$security_incoming);
	//*************************************************************************************************************************
	$server->wsdl->addSimpleType(
		'tipoIdentificacion',
		'xsd:string',
		'SimpleType',
		'scalar', TipoIdentificacionPeer::getTipoIdentificacionList()
	);
	//*************************************************************************************************************************
	$server->wsdl->addSimpleType(
		'empresaMensajeria',
		'xsd:string',
		'SimpleType',
		'scalar', EmpresaMensajeriaPeer::getCourrierListName()
	);
	//*************************************************************************************************************************
	$comrecibida_incoming = array ( 'REGIONAL_RADICACION' => array( 'name'=>'REGIONAL_RADICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_DOCUMENTO' => array( 'name'=>'TIPO_DOCUMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FORMA_RECEPCION' => array( 'name'=>'FORMA_RECEPCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DEPENDENCIA_DESTINO' => array( 'name'=>'DEPENDENCIA_DESTINO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PRIORIDAD_COM' => array( 'name'=>'PRIORIDAD_COM', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ARCHIVO_DIGIT' => array( 'name'=>'ARCHIVO_DIGIT', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ARCHIVO_NOMBRE' => array( 'name'=>'ARCHIVO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'ASUNTO' => array( 'name'=>'ASUNTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FOLIOS' => array( 'name'=>'FOLIOS', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
		'RADICADO_INTERESADO' => array( 'name'=>'RADICADO_INTERESADO', 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
		'RADICADO_ORIGEN' => array( 'name'=>'RADICADO_ORIGEN', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'ANEXOS' => array( 'name'=>'ANEXOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'OBSERVACIONES' => array( 'name'=>'OBSERVACIONES', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'FECHA_VENCIMIENTO' => array( 'name'=>'FECHA_VENCIMIENTO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_PROCESO' => array( 'name'=>'NUMERO_PROCESO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_FUD' => array( 'name'=>'NUMERO_FUD', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMPRESA_MENSAJERIA' => array( 'name'=>'EMPRESA_MENSAJERIA', 'type'=>'tns:empresaMensajeria', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_GUIA' => array( 'name'=>'NUMERO_GUIA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	);
	$server->wsdl->addComplexType('WSComRecibida','complexType','struct','all','',$comrecibida_incoming);
	//*************************************************************************************************************************
	$comrecibida_response = array ( 'CONSECUTIVO_ID' => array( 'name'=>'CONSECUTIVO_ID' , 'type'=>'xsd:int'),
		'RADICADO' => array( 'name'=>'RADICADO'  , 'type'=>'xsd:string'),
		'FECHA_CREACION' => array( 'name'=>'FECHA_CREACION' , 'type'=>'xsd:string'),
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSGERROR' => array( 'name'=>'MSGERROR' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('ComRecibidaInfoEnt','complexType','struct','all','', $comrecibida_response);
	//*************************************************************************************************************************
	$cominfo_metadata = array (
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSGERROR' => array( 'name'=>'MSGERROR' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('ComInfoMetaData','complexType','struct','all','', $cominfo_metadata);
	//*************************************************************************************************************************
	$response_default = array (
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSGERROR' => array( 'name'=>'MSGERROR' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('MessageReponse','complexType','struct','all','', $response_default);
	//*************************************************************************************************************************
	$radicado_lattacts = array ('NOMBRE_ANEXO' => array( 'name'=>'NOMBRE_ARCHIVO'  , 'type'=>'xsd:string'),
		'URL_ANEXO' => array( 'name'=>'URL_ADJUNTO' , 'type'=>'xsd:string'),
	);
	$server->wsdl->addComplexType('ComRadicadoAttachInfo','complexType','struct','all','', $radicado_lattacts);
	$server->wsdl->addComplexType('ComRadicadoAttachInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ComRadicadoAttachInfo[]')),'tns:ComRadicadoAttachInfo');
	//*************************************************************************************************************************
	$servicio_lattacts = array ('NOMBRE_ARCHIVO' => array( 'name'=>'NOMBRE_ARCHIVO'  , 'type'=>'xsd:string'),
		'DESCRIPCION_ADJUNTO' => array( 'name'=>'DESCRIPCION_ADJUNTO' , 'type'=>'xsd:string'),
		'URL_ADJUNTO' => array( 'name'=>'URL_ADJUNTO' , 'type'=>'xsd:string'),
		'DOWNLOAD_ADJUNTO' => array( 'name'=>'DOWNLOAD_ADJUNTO' , 'type'=>'xsd:string'),
		'FECHA_ADJUNTO' => array( 'name'=>'FECHA_ADJUNTO' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('ServicioAttachInfo','complexType','struct','all','', $servicio_lattacts);
	$server->wsdl->addComplexType('ServicioAttachInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ServicioAttachInfo[]')),'tns:ServicioAttachInfo');
	//*************************************************************************************************************************
	$servicio_com = array ('RADICADO_SERVICIO' => array( 'name'=>'RADICADO_SERVICIO'  , 'type'=>'xsd:string'),
		'FECHA_SERVICIO' => array( 'name'=>'FECHA_SERVICIO' , 'type'=>'xsd:string'),
		'NUMERO_FOLIOS' => array( 'name'=>'NUMERO_FOLIOS' , 'type'=>'xsd:string'),
		'TIPO_SERVICIO' => array( 'name'=>'TIPO_SERVICIO' , 'type'=>'xsd:string'),
		'ESTADO_SERVICIO' => array( 'name'=>'ESTADO_SERVICIO' , 'type'=>'xsd:string'),
		'NUMERO_RESOLUCION' => array( 'name'=>'NUMERO_RESOLUCION' , 'type'=>'xsd:string'),
		'FECHA_RESOLUCION' => array( 'name'=>'FECHA_RESOLUCION' , 'type'=>'xsd:string'),
		'DETALLE_SERVICIO' => array( 'name'=>'DETALLE_SERVICIO' , 'type'=>'xsd:string'),
		'ANEXOS_SERVICIO' => array( 'name'=>'ANEXOS_SERVICIO' , 'type'=>'tns:ServicioAttachInfoOfList', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('ComServicioInfo','complexType','struct','all','', $servicio_com);
	$server->wsdl->addComplexType('ComServicioInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ComServicioInfo[]')),'tns:ComServicioInfo');
	//*************************************************************************************************************************
	$comradicado_response = array ('RADICADO' => array( 'name'=>'RADICADO'  , 'type'=>'xsd:string'),
		'FECHA_RADICACION' => array( 'name'=>'FECHA_RADICACION' , 'type'=>'xsd:string'),
		'NUMERO_FOLIOS' => array( 'name'=>'NUMERO_FOLIOS' , 'type'=>'xsd:string'),
		'DIRECCION_REGISTRADA' => array( 'name'=>'DIRECCION_REGISTRADA' , 'type'=>'xsd:string'),
		'TELEFONO_REGISTRADO' => array( 'name'=>'TELEFONO_REGISTRADO' , 'type'=>'xsd:string'),
		'URL_RADICADO' => array( 'name'=>'URL_RADICADO' , 'type'=>'xsd:string'),
		'URL_DOWNLOAD' => array( 'name'=>'URL_DOWNLOAD' , 'type'=>'xsd:string'),
		'ASUNTO' => array( 'name'=>'ASUNTO' , 'type'=>'xsd:string'),
		'SERVICIOS_RADICADO' => array( 'name'=>'SERVICIOS_RADICADO' , 'type'=>'tns:ComServicioInfoOfList'),
		'ANEXOS_RADICADO' => array( 'name'=>'ANEXOS_RADICADO' , 'type'=>'tns:ComRadicadoAttachInfoOfList'),
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSG_INFO' => array( 'name'=>'MSG_INFO' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('ComRadicadoInfoEnt','complexType','struct','all','', $comradicado_response);
	$server->wsdl->addComplexType('ComRadicadoInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ComRadicadoInfoEnt[]')),'tns:ComRadicadoInfoEnt');
	//*************************************************************************************************************************
	$fljuoradhist_response = array ('RADICADO' => array( 'name'=>'RADICADO'  , 'type'=>'xsd:string'),
		'DEPENDENCIA' => array( 'name'=>'DEPENDENCIA' , 'type'=>'xsd:string'),
		'FECHA_ACTIVIDAD' => array( 'name'=>'FECHA_ACTIVIDAD' , 'type'=>'xsd:string'),
		'ACTIVIDAD_FLUJO' => array( 'name'=>'ACTIVIDAD_FLUJO' , 'type'=>'xsd:string'),
		'USUARIO_ACTIVIDAD' => array( 'name'=>'DEPENDENCIA_RADICADOR' , 'type'=>'xsd:string'),
		'OBSERVACIONES' => array( 'name'=>'OBSERVACIONES' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('FlujoHistRadicadoInfo','complexType','struct','all','', $fljuoradhist_response);
	$server->wsdl->addComplexType('FlujoRadicadoInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:FlujoHistRadicadoInfo[]')),'tns:FlujoHistRadicadoInfo');
	//*************************************************************************************************************************
	$radicadohist_response = array ('RADICADO' => array( 'name'=>'RADICADO'  , 'type'=>'xsd:string'),
		'USUARIO_ASIGNADO' => array( 'name'=>'USUARIO_ASIGNADO' , 'type'=>'xsd:string'),
		'DEPENDENCIA_ASIGNADA' => array( 'name'=>'DEPENDENCIA_ASIGNADA' , 'type'=>'xsd:string'),
		'USUARIO_RADICADOR' => array( 'name'=>'USUARIO_RADICADOR' , 'type'=>'xsd:string'),
		'DEPENDENCIA_RADICADOR' => array( 'name'=>'DEPENDENCIA_RADICADOR' , 'type'=>'xsd:string'),
		'RADICADO_FLUJO' => array( 'name'=>'RADICADO_FLUJO' , 'type'=>'tns:FlujoRadicadoInfoOfList'),
		'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
		'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
		'MSG_INFO' => array( 'name'=>'MSG_INFO' , 'type'=>'xsd:string')
	);
	$server->wsdl->addComplexType('HistRadicadoInfo','complexType','struct','all','', $radicadohist_response);
	//$server->wsdl->addComplexType('HistRadicadoInfoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:HistRadicadoInfo[]')),'tns:HistRadicadoInfo');
	//*************************************************************************************************************************
	$remitente_incoming = array ('CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NOMBRE' => array( 'name'=>'NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'tns:tipoIdentificacion', 'minOccurs' => 1, 'maxOccurs' => 1),
		//'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'FUNCIONARIO' => array( 'name'=>'FUNCIONARIO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CARGO' => array( 'name'=>'CARGO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PREFIJO' => array( 'name'=>'PREFIJO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSRemitenteEnt','complexType','struct','sequence','',$remitente_incoming);
	//*************************************************************************************************************************
	$representante_incoming = array (		
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'tns:tipoIdentificacion', 'minOccurs' => 1, 'maxOccurs' => 1),
		//'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)		
	);
	$server->wsdl->addComplexType('WSRepresentanteInteresadoEnt','complexType','struct','all','',$representante_incoming);
	//*************************************************************************************************************************
	$searchradhist_incoming = array (
		'CONSECUTIVO_ENTRADA' => array( 'name'=>'CONSECUTIVO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_ENTRADA' => array( 'name'=>'RADICADO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CONSECUTIVO_SALIDA' => array( 'name'=>'CONSECUTIVO_SALIDA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_SALIDA' => array( 'name'=>'RADICADO_SALIDA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSComHistRadicado','complexType','struct','all','',$searchradhist_incoming);
	$server->wsdl->addComplexType('WSComSearchInfo','complexType','struct','all','',$searchradhist_incoming);
	//*************************************************************************************************************************
	$search_incoming = array (		
		'RADICADO_ENTRADA' => array( 'name'=>'RADICADO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_SALIDA' => array( 'name'=>'RADICADO_SALIDA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'NUMERO_EXPEDIENTE' => array( 'name'=>'NUMERO_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'RADICADO_ORIGEN' => array( 'name'=>'RADICADO_ORIGEN', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSComBuscarInfo','complexType','struct','all','',$search_incoming);
	//*************************************************************************************************************************
	$interesados_incoming = array (
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		//'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'tns:tipoIdentificacion', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'INTR_INFO_REPRESENTANTE' => array( 'name'=>'INTR_INFO_REPRESENTANTE', 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
		'INFO_REPRESENTANTE' => array( 'name'=>'INTR_INFO_REPRESENTANTE', 'type'=>'tns:WSRepresentanteInteresadoEnt', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSInteresadoEnt','complexType','struct','all','',$interesados_incoming);
	$server->wsdl->addComplexType('InteresadoEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSInteresadoEnt[]')),'tns:WSInteresadoEnt');
	//*************************************************************************************************************************
	$server->register('AddRadicadoEntradaPublic',array('EntSecurity' => 'tns:WSSimadSecurity','EntComRecibida' => 'tns:WSComRecibida','EntInteresado' => 'tns:InteresadoEntOfList','EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ComRecibidaInfoEnt'), $ns, $ns.'#AddRadicadoEntradaPublic', false, false, 'Funcion para radicar una comunicacion externa recibida');
	$server->register('ConsultaRadicadoPublic',array('EntSecurity' => 'tns:WSSimadSecurity','EntComInfo' => 'tns:WSComBuscarInfo'), array('return' => 'tns:ComRadicadoInfoOfList'), $ns, $ns.'#ConsultaRadicadoPublic', false, false, 'Metodo para consultar un radicado del SGDEA');
	$server->register('GetHistoricoRadicado',array('EntSecurity' => 'tns:WSSimadSecurity','EntComHist' => 'tns:WSComHistRadicado'), array('return' => 'tns:HistRadicadoInfo'), $ns, $ns.'#GetHistoricoRadicado', false, false, 'Metodo para consultar el historico del un radicado');
	$server->register('GetMetadataInfoCom',array('EntSecurity' => 'tns:WSSimadSecurity','ComSearchInfo' => 'tns:WSComSearchInfo'), array('return' => 'tns:ComInfoMetaData'), $ns, $ns.'#GetMetadataInfoCom', false, false, 'Metodo para obtener los metadatos de un radicado del SGDEA');
	//*************************************************************************************************************************
	//$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	//$server->service($HTTP_RAW_POST_DATA);
	//*************************************************************************************************************************
	$contens = file_get_contents("php://input");
	@$server->service($contens);
?>