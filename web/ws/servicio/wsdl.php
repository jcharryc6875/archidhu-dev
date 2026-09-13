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
	//*********************************************************************************************
	$security_incoming = array ( 'SimadUserWs' => array( 'name'=>'SimadUserWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadPassWs' => array( 'name'=>'SimadPassWs', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SimadTypeGen' => array( 'name'=>'SimadTypeGen', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSSimadSecurity','complexType','struct','all','',$security_incoming);
	//*********************************************************************************************
	$user_autenticate = array ( 'UserNameSgdea' => array( 'name'=>'UserNameSgdea', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PasswordSgdea' => array( 'name'=>'PasswordSgdea', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSUserSgdea','complexType','struct','all','',$user_autenticate);
	//*********************************************************************************************
	$user_privilegios = array ( 'PrivilegioName' => array( 'name'=>'PrivilegioName', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1) );
	$server->wsdl->addComplexType('WSPrivilegioUser','complexType','struct','all','',array_merge($user_autenticate,$user_privilegios));
	//*********************************************************************************************
	$server->wsdl->addComplexType(
			'ComenviadaInfo',
			'complextType',
			'struct',
			'all',
			'',
			array ( 'url' => array( 'name'=>'url'  , 'type'=>'xsd:string'),
					'feha_publicacion' => array( 'name'=>'feha_publicacion'  , 'type'=>'xsd:string'),
					'ciudad' => array( 'name'=>'ciudad', 'type'=>'xsd:string'),
					'fecha_pqr' => array( 'name'=>'fecha_pqr', 'type'=>'xsd:string'),
					'nro_pqr' => array( 'name'=>'nro_pqr','type'=>'xsd:string'),
					'radicado' => array( 'name'=>'radicado','type'=>'xsd:string'),
					'feha_max_publicacion' => array( 'name'=>'feha_max_publicacion','type'=>'xsd:string'),
					'destinatario' => array( 'name'=>'destinatario','type'=>'xsd:string')
			)
	);
	//*********************************************************************************************	
	$server->wsdl->addComplexType(
		'ComenviadaListInfo',
		'complextType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array( 
			array(
				'ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ComenviadaInfo[]'
			)
		),
		'tns:ComenviadaInfo'
	);	
	//*********************************************************************************************
	$interesados_fields = array ( 'interesado_id' => array( 'name'=>'interesado_id' , 'type'=>'xsd:string'),
			'ciudad' => array( 'name'=>'ciudad'  , 'type'=>'xsd:string'),
			'numero_identificacion' => array( 'name'=>'numero_identificacion' , 'type'=>'xsd:string'),
			'direccion' => array( 'name'=>'direccion' , 'type'=>'xsd:string'),
			'nombre' => array( 'name'=>'nombre', 'type'=>'xsd:string')			
	);
	//*********************************************************************************************
	$remitentes_response = array ( 'directorioexterno_id' => array( 'name'=>'directorioexterno_id' , 'type'=>'xsd:int'),
			'ciudad' => array( 'name'=>'ciudad'  , 'type'=>'xsd:string'),
			'ciudad_codigo' => array( 'name'=>'ciudad_codigo'  , 'type'=>'xsd:string'),
			'nuid' => array( 'name'=>'nuid' , 'type'=>'xsd:string'),
			'direccion' => array( 'name'=>'direccion' , 'type'=>'xsd:string'),
			'nombre' => array( 'name'=>'nombre', 'type'=>'xsd:string'),
			'email' => array( 'name'=>'email', 'type'=>'xsd:string'),
			'funcionario' => array( 'name'=>'funcionario', 'type'=>'xsd:string'),
			'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
			'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$cusuarios_fields = array ( 'cargousuario_id' => array( 'name'=>'cargousuario_id' , 'type'=>'xsd:int'),
			'usuario_id' => array( 'name'=>'usuario_id' , 'type'=>'xsd:int'),
			'user_name' => array( 'name'=>'user_name' , 'type'=>'xsd:string'),
			'usuario_ad' => array( 'name'=>'usuario_ad' , 'type'=>'xsd:string'),
			'numero_identificacion' => array( 'name'=>'numero_identificacion'  , 'type'=>'xsd:string'),
			'nombre' => array( 'name'=>'nombre'  , 'type'=>'xsd:string'),
			'apellido' => array( 'name'=>'apellido' , 'type'=>'xsd:string'),
			'dependencia' => array( 'name'=>'dependencia' , 'type'=>'xsd:string'),
			'dependencia_id' => array( 'name'=>'dependencia_id', 'type'=>'xsd:int'),
			'cargo' => array( 'name'=>'cargo', 'type'=>'xsd:string'),
			'regional' => array( 'name'=>'regional', 'type'=>'xsd:string'),
			'regional_id' => array( 'name'=>'regional_id', 'type'=>'xsd:int'),
			'ciudad' => array( 'name'=>'ciudad', 'type'=>'xsd:string'),
			'ciudad_id' => array( 'name'=>'regional_id', 'type'=>'xsd:int'),
			'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
			'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$uprivlegios_fields = array ( 'usuario_id' => array( 'name'=>'usuario_id' , 'type'=>'xsd:int'),
			'nombre' => array( 'name'=>'nombre'  , 'type'=>'xsd:string'),
			'apellido' => array( 'name'=>'apellido' , 'type'=>'xsd:string'),
			'privilegio_nombre' => array( 'name'=>'privilegio_nombre', 'type'=>'xsd:string'),
			'authorized' => array( 'name'=>'privilegio_nombre', 'type'=>'xsd:boolean'),
			'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
			'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$comrecibida_response = array ( 'CONSECUTIVO_ID' => array( 'name'=>'CONSECUTIVO_ID' , 'type'=>'xsd:string'),
			'RADICADO' => array( 'name'=>'RADICADO' , 'type'=>'xsd:string'),
			'FECHA_CREACION' => array( 'name'=>'FECHA_CREACION' , 'type'=>'xsd:string'),
			'FECHA_TRANSACCION' => array( 'name'=>'FECHA_TRANSACCION' , 'type'=>'xsd:string'),
			'ISERROR' =>  array( 'name'=>'ISERROR' , 'type'=>'xsd:boolean'),
			'MSGERROR' => array( 'name'=>'MSGERROR' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$comenviada_response = array ( 'consecutivo_id' => array( 'name'=>'consecutivo_id' , 'type'=>'xsd:int'),
			'radicado' => array( 'name'=>'radicado'  , 'type'=>'xsd:string'),
			'fecha_radicacion' => array( 'name'=>'fecha_radicacion' , 'type'=>'xsd:string'),
			'fecha_transaccion' => array( 'name'=>'fecha_transaccion' , 'type'=>'xsd:string'),
			'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
			'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$response_default = array ('fecha_transaccion' => array( 'name'=>'fecha_transaccion' , 'type'=>'xsd:string'),
			'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
			'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
	);
	//*********************************************************************************************
	$server->wsdl->addSimpleType(
		'tipoIdentificacion',
		'xsd:string',
		'SimpleType',
		'scalar', TipoIdentificacionPeer::getTipoIdentificacionList()
	);
	//*********************************************************************************************
	$server->wsdl->addSimpleType(
		'empresaMensajeria',
		'xsd:string',
		'SimpleType',
		'scalar', EmpresaMensajeriaPeer::getCourrierListName()
	);
	//*********************************************************************************************
	$comrecibida_incoming = array ( 'REGIONAL_ID' => array( 'name'=>'REGIONAL_ID', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'CLASIFICACION_DOCUMENTO' => array( 'name'=>'CLASIFICACION_DOCUMENTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'FORMA_RECEPCION' => array( 'name'=>'FORMA_RECEPCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'REMITENTE_ID' => array( 'name'=>'REMITENTE_ID' , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'RADICADO_ORIGEN' => array( 'name'=>'RADICADO_ORIGEN', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'ASUNTO' => array( 'name'=>'ASUNTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'FOLIOS' => array( 'name'=>'FOLIOS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'ANEXOS' => array( 'name'=>'ANEXOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'OBSERVACIONES' => array( 'name'=>'OBSERVACIONES', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),			
			'PRIORIDAD_COM' => array( 'name'=>'PRIORIDAD_COM', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'RADICADO_INTERESADO' => array( 'name'=>'RADICADO_INTERESADO', 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
			'ARCHIVO_DIGIT' => array( 'name'=>'ARCHIVO_DIGIT', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'ARCHIVO_NOMBRE' => array( 'name'=>'ARCHIVO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'UCARGO_DESTINO' => array( 'name'=>'UCARGO_DESTINO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'UCARGO_ORIGEN' => array( 'name'=>'UCARGO_ORIGEN', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'FECHA_VENCIMIENTO' => array( 'name'=>'FECHA_VENCIMIENTO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'NUMERO_PROCESO' => array( 'name'=>'NUMERO_PROCESO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'NUMERO_FUD' => array( 'name'=>'NUMERO_FUD', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'EMPRESA_MENSAJERIA' => array( 'name'=>'EMPRESA_MENSAJERIA', 'type'=>'tns:empresaMensajeria', 'minOccurs' => 0, 'maxOccurs' => 1),
			'NUMERO_GUIA' => array( 'name'=>'NUMERO_GUIA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSComRecibida','complexType','struct','all','',$comrecibida_incoming);
	//*********************************************************************************************
	$comenviada_incoming = array ( 'CODIGO_MUNICIPIO' => array( 'name'=>'CODIGO_MUNICIPIO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'MUNICIPIO' => array( 'name'=>'MUNICIPIO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'CODIGO_DEPENDENCIA' => array( 'name'=>'CODIGO_DEPENDENCIA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'PUNTO_RADICACION' => array( 'name'=>'PUNTO_RADICACION' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'ASUNTO' => array( 'name'=>'ASUNTO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'FOLIOS' => array( 'name'=>'FOLIOS', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			//'ANEXOS' => array( 'name'=>'ANEXOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			//'FUNCIONARIO_DESTINO' => array( 'name'=>'FUNCIONARIO_DESTINO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			//'CARGO_DESTINATARIO' => array( 'name'=>'CARGO_DESTINATARIO'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'OBSERVACIONES' => array( 'name'=>'OBSERVACIONES', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'RADICADO_ENTRADA' => array( 'name'=>'RADICADO_ENTRADA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
			'FIRMA_DIGITAL' => array( 'name'=>'FIRMA_DIGITAL', 'type'=>'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),			
			'ARCHIVO_DIGIT' => array( 'name'=>'ARCHIVO_DIGIT', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			//'ARCHIVO_NOMBRE' => array( 'name'=>'ARCHIVO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'NUID_FIRMA' => array( 'name'=>'NUID_FIRMA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'NUID_RADICA' => array( 'name'=>'NUID_RADICA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'NUID_GESTOR' => array( 'name'=>'NUID_GESTOR', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'TIPO_PROCESO' => array( 'name'=>'TIPO_PROCESO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSComEnviada','complexType','struct','all','',$comenviada_incoming);
	//*********************************************************************************************
	$remitente_incoming = array ( 'REMITENTE_ID' => array( 'name'=>'REMITENTE_ID', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'NOMBRE' => array( 'name'=>'NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'FUNCIONARIO' => array( 'name'=>'FUNCIONARIO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'CARGO' => array( 'name'=>'CARGO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'PREFIJO' => array( 'name'=>'PREFIJO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
			'NUID' => array( 'name'=>'NUID', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
			'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0)
	);
	$server->wsdl->addComplexType('WSRemitenteEnt','complexType','struct','all','',$remitente_incoming);
	//*********************************************************************************************
	$representante_incoming = array ( 'REPRESENTANTE_ID' => array( 'name'=>'REPRESENTANTE_ID', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
	);
	$server->wsdl->addComplexType('WSRepresentanteInteresadoEnt','complexType','struct','all','',$representante_incoming);
	//*********************************************************************************************
	$interesados_incoming = array ( 'INTERESADO_ID' => array( 'name'=>'INTERESADO_ID', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'PRIMER_NOMBRE' => array( 'name'=>'PRIMER_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_NOMBRE' => array( 'name'=>'SEGUNDO_NOMBRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'PRIMER_APELLIDO' => array( 'name'=>'PRIMER_APELLIDO' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'SEGUNDO_APELLIDO' => array( 'name'=>'SEGUNDO_APELLIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
		'TIPO_IDENTIFICACION' => array( 'name'=>'TIPO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'TIPO_GENERO' => array( 'name'=>'TIPO_GENERO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD' => array( 'name'=>'CIUDAD', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'CIUDAD_CODIGO' => array( 'name'=>'CIUDAD_CODIGO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'DIRECCION' => array( 'name'=>'DIRECCION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'CODIGO_POSTAL' => array( 'name'=>'CODIGO_POSTAL'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'TELEFONO' => array( 'name'=>'TELEFONO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'CELULAR' => array( 'name'=>'CELULAR', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'EMAIL' => array( 'name'=>'EMAIL', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
		'INTR_INFO_REPRESENTANTE' => array( 'name'=>'INTR_INFO_REPRESENTANTE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
		'INFO_REPRESENTANTE' => array( 'name'=>'INTR_INFO_REPRESENTANTE', 'type'=>'tns:WSRepresentanteInteresadoEnt', 'minOccurs' => 0, 'maxOccurs' => 0)
	);
	//*********************************************************************************************
	//$server->wsdl->addComplexType('WSResponseObjInfo','complexType','struct','all','',array());
	//*********************************************************************************************
	$catalogolist_info = array 
    ( 
        'idFielKey' => array( 'name'=>'idFielKey' , 'type'=>'xsd:int'),
        'nombreField' => array( 'name'=>'nombreField' , 'type'=>'xsd:string'),
		'Fecha_Transaccion' => array( 'name'=>'Fecha_Transaccion' , 'type'=>'xsd:string'),
		'IsError' =>  array( 'name'=>'IsError' , 'type'=>'xsd:boolean'),
		'MsgError' => array( 'name'=>'MsgError' , 'type'=>'xsd:string')
    );
  	//****COMPLEX TYPE FASE ARCHIVO (LOCALIZACION) *********/
	$server->wsdl->addComplexType('WSFaseArchivoEnt','complexType','struct','all','', $catalogolist_info);
	$server->wsdl->addComplexType('FaseArchivoEntOfList','complextType','array','','SOAP-ENC:Array', array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSFaseArchivoEnt[]')),'tns:WSFaseArchivoEnt');
	//*********************************************************************************************
	//****COMPLEX TYPE SOPORTE DOCUMENTAL *********/
	$server->wsdl->addComplexType('WSSoporteDocumentalEnt','complexType','struct','all','', $catalogolist_info);
	$server->wsdl->addComplexType('SoporteDocumentalEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSSoporteDocumentalEnt[]')),'tns:WSSoporteDocumentalEnt');
	//*********************************************************************************************
	//****COMPLEX TYPE ESTADO DOCUMENTAL *********/
	$server->wsdl->addComplexType('WSEstadoDocumentalEnt','complexType','struct','all','', $catalogolist_info);
	$server->wsdl->addComplexType('EstadoDocumentalEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSEstadoDocumentalEnt[]')),'tns:WSEstadoDocumentalEnt');
	//*********************************************************************************************
	//****COMPLEX TYPE FRECUENCIA DOCUMENTAL *********/
	$server->wsdl->addComplexType('WSFrecuenciaConsultaEnt','complexType','struct','all','', $catalogolist_info);
	$server->wsdl->addComplexType('FrecuenciaConsultaEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSFrecuenciaConsultaEnt[]')),'tns:WSFrecuenciaConsultaEnt');
	//*********************************************************************************************
	//****COMPLEX TYPE MEDIO DE CONSERVACION *********/
	$server->wsdl->addComplexType('WSMedioConservacionEnt','complexType','struct','all','', $catalogolist_info);
	$server->wsdl->addComplexType('MedioConservacionEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSMedioConservacionEnt[]')),'tns:WSMedioConservacionEnt');
	//*********************************************************************************************
	$server->wsdl->addComplexType('WSInteresadoEnt','complexType','struct','all','',$interesados_incoming);
	$server->wsdl->addComplexType('InteresadoEntOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSInteresadoEnt[]')),'tns:WSInteresadoEnt');
	$server->wsdl->addComplexType('ListArray','complextType','array','','SOAP-ENC:Array',array(),array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:WSInteresadoEnt[]')),'xsd:string');
	//*********************************************************************************************
	$server->wsdl->addComplexType('Regional','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('RegionalOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Regional[]')),'tns:Regional');
	//*********************************************************************************************	
	$server->wsdl->addComplexType('TipoComRecibida','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('TipoComRecibidaOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:TipoComRecibida[]')),'tns:TipoComRecibida');
	//*********************************************************************************************
	$server->wsdl->addComplexType('FormaRecepcion','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('FormaRecepcionOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:FormaRecepcion[]')),'tns:FormaRecepcion');
	//*********************************************************************************************	
	$server->wsdl->addComplexType('PrioridadCom','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('PrioridadComOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:PrioridadCom[]')),'tns:PrioridadCom');
	//*********************************************************************************************
	$server->wsdl->addComplexType('EmpresaMensajeria','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('EmpresaMensajeriaOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:EmpresaMensajeria[]')),'tns:EmpresaMensajeria');
	//*********************************************************************************************
	$server->wsdl->addComplexType('DependenciaDestino','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'),'idFielKeyCargo' => array('name' => 'idFielKeyCargo', 'type' => 'xsd:int'),'codigoField' => array('name' => 'codigoField', 'type' => 'xsd:string'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('DependenciaDestinoOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:DependenciaDestino[]')),'tns:DependenciaDestino');
	//*********************************************************************************************
	$server->wsdl->addComplexType('Dependencia','complexType','struct','all','', array('idFielKey' => array('name' => 'idFielKey', 'type' => 'xsd:int'), 'codigoField' => array('name' => 'codigoField', 'type' => 'xsd:string'),'nombreField' => array('name' => 'nombreField', 'type' => 'xsd:string')));
	$server->wsdl->addComplexType('DependenciaOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Dependencia[]')),'tns:Dependencia');
	//*********************************************************************************************
	$server->wsdl->addComplexType('Interesado','complexType','struct','all','', $interesados_fields);
	$server->wsdl->addComplexType('InteresadosOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Interesado[]')),'tns:Interesado');
	//*********************************************************************************************
	$server->wsdl->addComplexType('Remitente','complexType','struct','all','', $remitentes_response);
	$server->wsdl->addComplexType('RemitentesOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Remitente[]')),'tns:Remitente');
	//*********************************************************************************************
	$server->wsdl->addComplexType('CargoUsuario','complexType','struct','all','', $cusuarios_fields);
	$server->wsdl->addComplexType('CargoUsuarioOfList','complextType','array','','SOAP-ENC:Array',array(),array( array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:CargoUsuario[]')),'tns:CargoUsuario');
	//*********************************************************************************************
	$server->wsdl->addComplexType('ComRecibidaInfoEnt','complexType','struct','all','', $comrecibida_response);
	$server->wsdl->addComplexType('MessageReponse','complexType','struct','all','', $response_default);
	$server->wsdl->addComplexType('ComEnviadaInfoEnt','complexType','struct','all','', $comenviada_response);
	//*********************************************************************************************
	$server->wsdl->addComplexType('UsuarioAuthInfo','complexType','struct','all','', $uprivlegios_fields);
	//*********************************************************************************************
	//****GET FASE ARCHIVO (LOCALIZACION) *********/
	$server->register('getFaseArchivoList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:FaseArchivoEntOfList'), $ns, $ns.'#getFaseArchivoList', false, false, 'Funcion para obtener lista de las localizaciones'); 
	//****GET SOPORTE DOCUMENTAL *********/
	$server->register('getSoporteDocumentalList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:SoporteDocumentalEntOfList'), $ns, $ns.'#getSoporteDocumentalList', false, false, 'Funcion para obtener lista de soportes documentales');
	//****GET ESTADO DOCUMENTAL *********/
	$server->register('getEstadoDocumentalList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:EstadoDocumentalEntOfList'), $ns, $ns.'#getEstadoDocumentalList', false, false, 'Funcion para obtener lista de los estados documentales');
	//****GET FRECUENCIA DOCUMENTAL *********/
	$server->register('getFrecuenciaConsultaList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:FrecuenciaConsultaEntOfList'), $ns, $ns.'#getFrecuenciaConsultaList', false, false, 'Funcion para obtener lista de las frecuencias documentales');
	//****GET MEDIO DE CONSERVACION *********/
	$server->register('getMedioConservacionList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:MedioConservacionEntOfList'), $ns, $ns.'#getMedioConservacionList', false, false, 'Funcion para obtener lista de las localizaciones');
	//*********************************************************************************************
	$server->register('ConsultaPublicacionesWeb',array('EntSecurity' => 'tns:WSSimadSecurity', 'no_radicado' => 'xsd:string'), array('return' => 'tns:ComenviadaInfo'), $ns, $ns.'#ConsultaPublicacionesWeb', false, false, 'Funcion para consultar radicados de salida');
	$server->register('getPuntoRadicacion',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:RegionalOfList'), $ns, $ns.'#getPuntoRadicacion', false, false, 'Funcion para obtener lista de puntos de radicacion');
	$server->register('getTipoComRecibida',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:TipoComRecibidaOfList'), $ns, $ns.'#getTipoComRecibida', false, false, 'Funcion para obtener lista de los tipos de tramites');
	$server->register('getFormaRecepcionList',array(), array('return' => 'tns:FormaRecepcionOfList'), $ns, $ns.'#getFormaRecepcionList', false, false, 'Funcion para obtener lista de los puntos de radicacion');
	$server->register('getPrioridadComList',array(), array('return' => 'tns:PrioridadComOfList'), $ns, $ns.'#getPrioridadComList', false, false, 'Funcion para obtener lista de las prioridades');
	$server->register('getEmpresaMensajeriaList',array(), array('return' => 'tns:EmpresaMensajeriaOfList'), $ns, $ns.'#getEmpresaMensajeriaList', false, false, 'Funcion para obtener lista de las empresas de mensajeria');
	$server->register('getAreaDestinoList',array('EntSecurity' => 'tns:WSSimadSecurity', 'tipoproceso_id' => 'xsd:int'), array('return' => 'tns:DependenciaDestinoOfList'), $ns, $ns.'#getAreaDestinoList', false, false, 'Funcion para obtener lista de las areas de destino');
	$server->register('getListInteresados',array('EntSecurity' => 'tns:WSSimadSecurity', 'nombre' => 'xsd:string','apellido' => 'xsd:string', 'numero_identificacion' => 'xsd:string', 'limit_select' => 'xsd:int'), array('return' => 'tns:InteresadoEntOfList'), $ns, $ns.'#getListInteresados', false, false, 'Funcion para obtener lista de los interesados');
	$server->register('getRemitentesList',array('EntSecurity' => 'tns:WSSimadSecurity', 'nombre' => 'xsd:string','funcionario' => 'xsd:string', 'nuid' => 'xsd:string', 'email' => 'xsd:string', 'limit_select' => 'xsd:int'), array('return' => 'tns:RemitentesOfList'), $ns, $ns.'#getRemitentesList', false, false, 'Funcion para obtener lista de los remitentes');
	$server->register('getRemitenteById',array('EntSecurity' => 'tns:WSSimadSecurity', 'consecutivopk_id' => 'xsd:int'), array('return' => 'tns:Remitente'), $ns, $ns.'#getRemitenteById', false, false, 'Funcion para obtener un remitente por consecutivo interno');
	$server->register('getDependenciasList',array('EntSecurity' => 'tns:WSSimadSecurity'), array('return' => 'tns:DependenciaOfList'), $ns, $ns.'#getDependenciasList', false, false, 'Funcion para obtener lista de las dependencias');
	$server->register('getUsuariosList',array('EntSecurity' => 'tns:WSSimadSecurity', 'nombre' => 'xsd:string','apellido' => 'xsd:string', 'numero_identificacion' => 'xsd:string','email' => 'xsd:string','dependencia_id' => 'xsd:int', 'limit_select' => 'xsd:int'), array('return' => 'tns:CargoUsuarioOfList'), $ns, $ns.'#getUsuariosList', false, false, 'Funcion para obtener lista de usuarios del sgdea');
	$server->register('getUsuarioSgdeaById',array('EntSecurity' => 'tns:WSSimadSecurity', 'consecutivopk_id' => 'xsd:int'), array('return' => 'tns:CargoUsuario'), $ns, $ns.'#getUsuarioSgdeaById', false, false, 'Funcion para obtener un usuario del SGDEA por consecutivo interno');
	$server->register('AddRadicadoEntrada',array('EntSecurity' => 'tns:WSSimadSecurity', 'EntComRecibida' => 'tns:WSComRecibida','EntInteresado' => 'tns:InteresadoEntOfList','EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ComRecibidaInfoEnt'), $ns, $ns.'#AddRadicadoEntrada', false, false, 'Funcion para radicar una comunicacion externa recibida');
	$server->register('UpdateProcessInfoEntrada',array('EntSecurity' => 'tns:WSSimadSecurity', 'consecutivo_id' => 'xsd:int'), array('MessageReponseBasic' => 'tns:MessageReponse'), $ns, $ns.'#UpdateProcessInfoEntrada', false, false, 'Funcion para actualizar proceso correspondencia recibida');
	$server->register('UserAutenticate',array('EntSecurity' => 'tns:WSSimadSecurity', 'UserCredential' => 'tns:WSUserSgdea'), array('return' => 'tns:CargoUsuario'), $ns, $ns.'#UserAutenticate', false, false, 'Funcion para validar permisos de acceso a las funcionalidades');
	$server->register('UserAuthorization',array('EntSecurity' => 'tns:WSSimadSecurity', 'UserAuthInfo' => 'tns:WSPrivilegioUser'), array('return' => 'tns:UsuarioAuthInfo'), $ns, $ns.'#Userauthorization', false, false, 'Funcion para autenticar un usuario');
	//*********************************************************************************************
	//$server->register('AddRadicadoSalidaEnt',array('EntComEnviada' => 'tns:WSComEnviada','EntInteresado' => 'tns:InteresadoEntOfList','EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ComEnviadaInfoEnt'), $ns, $ns.'#AddRadicadoSalidaEnt', false, false, 'Funcion para radicar una comunicacion externa enviada');
	//*************************************************************************************************************************
	$contens = file_get_contents("php://input");
	@$server->service($contens);
?>