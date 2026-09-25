<?php
require_once('../lib/searchImgByRad.php');
require_once('../lib/nusoap-1.124/autoload.php');

$ns = 'urn:SimadEnterpriseSgdea';
$server = new soap_server();
$server->configureWSDL('SimadEnterpriseSgdea', $ns);
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
//$server->encode_utf8 = true;
$server->wsdl->schemaTargetNamespace = $ns;
//*************************************************************************************************************************
$security_incoming = array(
	'SimadUserWs' => array('name' => 'SimadUserWs', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SimadPassWs' => array('name' => 'SimadPassWs', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SimadTypeGen' => array('name' => 'SimadTypeGen', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSSimadSecurity', 'complexType', 'struct', 'all', '', $security_incoming);
//*************************************************************************************************************************
$interesados_fields = array(
	'interesado_id' => array('name' => 'interesado_id', 'type' => 'xsd:string'),
	'ciudad' => array('name' => 'ciudad', 'type' => 'xsd:string'),
	'numero_identificacion' => array('name' => 'numero_identificacion', 'type' => 'xsd:string'),
	'direccion' => array('name' => 'direccion', 'type' => 'xsd:string'),
	'nombre' => array('name' => 'nombre', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$remitentes_response = array(
	'directorioexterno_id' => array('name' => 'directorioexterno_id', 'type' => 'xsd:string'),
	'ciudad' => array('name' => 'ciudad', 'type' => 'xsd:string'),
	'nuid' => array('name' => 'nuid', 'type' => 'xsd:string'),
	'direccion' => array('name' => 'direccion', 'type' => 'xsd:string'),
	'nombre' => array('name' => 'nombre', 'type' => 'xsd:string'),
	'funcionario' => array('name' => 'funcionario', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$cusuarios_fields = array(
	'cargousuario_id' => array('name' => 'cargousuario_id', 'type' => 'xsd:int'),
	'usuario_id' => array('name' => 'usuario_id', 'type' => 'xsd:int'),
	'nombre' => array('name' => 'nombre', 'type' => 'xsd:string'),
	'apellido' => array('name' => 'apellido', 'type' => 'xsd:string'),
	'dependencia' => array('name' => 'dependencia', 'type' => 'xsd:string'),
	'cargo' => array('name' => 'cargo', 'type' => 'xsd:string'),
	'regional' => array('name' => 'regional', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$attach_response = array(
	'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
	'RADICADO' => array('name' => 'RADICADO', 'type' => 'xsd:string'),
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSG_INFO' => array('name' => 'MSG_INFO', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$comenviada_response = array(
	'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
	'RADICADO' => array('name' => 'RADICADO', 'type' => 'xsd:string'),
	'FECHA_CREACION' => array('name' => 'FECHA_CREACION', 'type' => 'xsd:string'),
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSGERROR' => array('name' => 'MSGERROR', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$actoadministrativo_response = array(
	'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
	'RADICADO' => array('name' => 'RADICADO', 'type' => 'xsd:string'),
	'FECHA_CREACION' => array('name' => 'FECHA_CREACION', 'type' => 'xsd:string'),
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSGERROR' => array('name' => 'MSGERROR', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$viewattach_list = array(
	'GUID' => array('name' => 'GUID', 'type' => 'xsd:string'),
	'NOMBRE_ARCHIVO' => array('name' => 'NOMBRE_ARCHIVO', 'type' => 'xsd:string'),
	'URL' => array('name' => 'URL', 'type' => 'xsd:string'),
	'URL_DOWNLOAD' => array('name' => 'URL_DOWNLOAD', 'type' => 'xsd:string'),
	'TIPO_ATTACHMENT' => array('name' => 'TIPO_ATTACHMENT', 'type' => 'xsd:string'),
	'SIZE_FILE' => array('name' => 'SIZE_FILE', 'type' => 'xsd:int')
);
$server->wsdl->addComplexType('DownloadAttachInfo', 'complexType', 'struct', 'all', '', $viewattach_list);
$server->wsdl->addComplexType('AttachComOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:DownloadAttachInfo[]')), 'tns:DownloadAttachInfo');
//*************************************************************************************************************************
$viewattach_response = array(
	'LIST_DOCUMENTOS' => array('name' => 'LIST_DOCUMENTOS', 'type' => 'tns:AttachComOfList', 'minOccurs' => 1, 'maxOccurs' => 1),
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSG_INFO' => array('name' => 'MSG_INFO', 'type' => 'xsd:string')
);
$server->wsdl->addComplexType('ViewAttachInfoEnt', 'complexType', 'struct', 'all', '', $viewattach_response);
//*************************************************************************************************************************
$response_default = array(
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSGERROR' => array('name' => 'MSGERROR', 'type' => 'xsd:string')
);
//*************************************************************************************************************************
$search_attach = array(
	'RADICADO_COMUNICACION' => array('name' => 'RADICADO_COMUNICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_CONSECUTIVO' => array('name' => 'TIPO_CONSECUTIVO', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSViewAttachDoc', 'complexType', 'struct', 'all', '', $search_attach);
//*************************************************************************************************************************
$nuid_firmas = array(
	'NUID_FIRMA' => array('name' => 'NUID_GESTOR', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'UCARGO_FIRMA' => array('name' => 'UCARGO_FIRMA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSComFirmas', 'complexType', 'struct', 'all', '', $nuid_firmas);
$server->wsdl->addComplexType('FirmasComOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:WSComFirmas[]')), 'tns:WSComFirmas');
//*************************************************************************************************************************
$comenviada_incoming = array(
	'CODIGO_MUNICIPIO' => array('name' => 'CODIGO_MUNICIPIO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'MUNICIPIO' => array('name' => 'MUNICIPIO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CODIGO_DEPENDENCIA' => array('name' => 'CODIGO_DEPENDENCIA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUID_GESTOR' => array('name' => 'NUID_GESTOR', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_ENVIO' => array('name' => 'TIPO_ENVIO', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ASUNTO' => array('name' => 'ASUNTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'RADICADO_ENTRADA' => array('name' => 'RADICADO_ENTRADA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ENTRADA_EXTERNA' => array('name' => 'ENTRADA_EXTERNA', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	//'PUNTO_RADICACION' => array( 'name'=>'PUNTO_RADICACION' , 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'ANEXOS' => array( 'name'=>'ANEXOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	//'FUNCIONARIO_DESTINO' => array( 'name'=>'FUNCIONARIO_DESTINO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	//'CLASIFICACION_DOCUMENTO' => array( 'name'=>'CLASIFICACION_DOCUMENTO'  , 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FIRMA_DIGITAL' => array('name' => 'FIRMA_DIGITAL', 'type' => 'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_DIGIT' => array('name' => 'ARCHIVO_DIGIT', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_NOMBRE' => array('name' => 'ARCHIVO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'FIRMAS' => array('name' => 'FIRMAS', 'type' => 'tns:FirmasComOfList', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'NUID_FIRMA' => array( 'name'=>'NUID_FIRMA', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'NUID_RADICA' => array( 'name'=>'NUID_RADICA', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'OBSERVACIONES_ENVIO' => array('name' => 'OBSERVACIONES_ENVIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FOLIOS' => array('name' => 'FOLIOS', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'TIPO_MASIVO' => array('name' => 'TIPO_MASIVO', 'type' => 'xsd:boolean', 'minOccurs' => 0, 'maxOccurs' => 1),
	'SUBORIGEN' => array('name' => 'SUBORIGEN', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FECHA_RESOLUCION' => array('name' => 'FECHA_RESOLUCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUMERO_RESOLUCION' => array('name' => 'NUMERO_RESOLUCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'APP_ORIGEN' => array('name' => 'APP_ORIGEN', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSComEnviada', 'complexType', 'struct', 'all', '', $comenviada_incoming);
//*************************************************************************************************************************
$actoadministrativo_incoming = array(
	'REGIONAL_RADICACION' => array('name' => 'REGIONAL_RADICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_DOCUMENTO' => array('name' => 'TIPO_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'DEPENDENCIA_DESTINO' => array('name' => 'DEPENDENCIA_DESTINO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'PRIORIDAD_COM' => array('name' => 'PRIORIDAD_COM', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'FIRMA_DIGITAL' => array('name' => 'FIRMA_DIGITAL', 'type' => 'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_DIGIT' => array('name' => 'ARCHIVO_DIGIT', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_NOMBRE' => array('name' => 'ARCHIVO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SUBSERIE_NOMBRE' => array('name' => 'SUBSERIE_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SUBSERIE_CODIGO' => array('name' => 'SUBSERIE_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ASUNTO' => array('name' => 'ASUNTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUID_GESTOR' => array('name' => 'NUID_GESTOR', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'FIRMAS' => array('name' => 'FIRMAS', 'type' => 'tns:FirmasComOfList', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_NOTIFICACION' => array('name' => 'TIPO_NOTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FOLIOS' => array('name' => 'FOLIOS', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'ANEXOS' => array('name' => 'ANEXOS', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'OBSERVACIONES' => array('name' => 'OBSERVACIONES', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'CODIGO_TIPODOC' => array('name' => 'CODIGO_TIPODOC', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUID_DESTINATARIO' => array('name' => 'NUID_DESTINATARIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'SUBORIGEN' => array('name' => 'SUBORIGEN', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'MARCO_NORMATIVO' => array('name' => 'MARCO_NORMATIVO', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSActoAdministrativo', 'complexType', 'struct', 'all', '', $actoadministrativo_incoming);
//*************************************************************************************************************************
$comenviada_oferta = array(
	'SERIE' => array('name' => 'SERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SUBSERIE' => array('name' => 'SUBSERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_DOCUMENTAL' => array('name' => 'TIPO_DOCUMENTAL', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'EXPEDIENTE_ID' => array('name' => 'EXPEDIENTE_ID', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'MARCO_NORMATIVO' => array('name' => 'MARCO_NORMATIVO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUMERO_FUD' => array('name' => 'NUMERO_FUD', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'FECHA_RESOLUCION' => array( 'name'=>'FECHA_RESOLUCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'NUMERO_RESOLUCION' => array( 'name'=>'NUMERO_RESOLUCION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	//'SUBORIGEN' => array( 'name'=>'SUBORIGEN', 'type'=>'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUMRADSYSORIGEN' => array('name' => 'NUMRADSYSORIGEN', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
);
$oferta_incoming = array_merge($comenviada_oferta, $comenviada_incoming);
unset($oferta_incoming['RADICADO_ENTRADA']);
$server->wsdl->addComplexType('WSComEnviadaOferta', 'complexType', 'struct', 'all', '', $oferta_incoming);
//*************************************************************************************************************************
$document_attach = array(
	'ARCHIVO_DATA' => array('name' => 'ARCHIVO_DATA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_NOMBRE' => array('name' => 'ARCHIVO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'RADICADO_COMUNICACION' => array('name' => 'RADICADO_COMUNICACION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'CONSECUTIVO_COMUNICACION' => array('name' => 'CONSUCUTIVO_COMUNICACION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'TIPO_CONSECUTIVO' => array('name' => 'TIPO_CONSECUTIVO', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NOTIFICA_RADICADO' => array('name' => 'NOTIFICA_RADICADO', 'type' => 'xsd:boolean', 'minOccurs' => 1, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSAttachDocument', 'complexType', 'struct', 'all', '', $document_attach);
//*************************************************************************************************************************
$remitente_incoming = array(
	'REMITENTE_ID' => array('name' => 'REMITENTE_ID', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CIUDAD' => array('name' => 'CIUDAD', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CIUDAD_CODIGO' => array('name' => 'CIUDAD_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NOMBRE' => array('name' => 'NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'DIRECCION' => array('name' => 'DIRECCION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'FUNCIONARIO' => array('name' => 'FUNCIONARIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CARGO' => array('name' => 'CARGO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'TELEFONO' => array('name' => 'TELEFONO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'EMAIL' => array('name' => 'EMAIL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'PREFIJO' => array('name' => 'PREFIJO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'NUID' => array('name' => 'NUID', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_IDENTIFICACION' => array('name' => 'TIPO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CODIGO_POSTAL' => array('name' => 'CODIGO_POSTAL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0)
);
$server->wsdl->addComplexType('WSRemitenteEnt', 'complexType', 'struct', 'all', '', $remitente_incoming);
//*************************************************************************************************************************
$representante_incoming = array(
	'REPRESENTANTE_ID' => array('name' => 'REPRESENTANTE_ID', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CIUDAD' => array('name' => 'CIUDAD', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CIUDAD_CODIGO' => array('name' => 'CIUDAD_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'PRIMER_NOMBRE' => array('name' => 'PRIMER_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SEGUNDO_NOMBRE' => array('name' => 'SEGUNDO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'PRIMER_APELLIDO' => array('name' => 'PRIMER_APELLIDO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SEGUNDO_APELLIDO' => array('name' => 'SEGUNDO_APELLIDO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'TIPO_IDENTIFICACION' => array('name' => 'TIPO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUMERO_IDENTIFICACION' => array('name' => 'NUMERO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'DIRECCION' => array('name' => 'DIRECCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CODIGO_POSTAL' => array('name' => 'CODIGO_POSTAL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'TELEFONO' => array('name' => 'TELEFONO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CELULAR' => array('name' => 'CELULAR', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'EMAIL' => array('name' => 'EMAIL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'TIPO_GENERO' => array('name' => 'TIPO_GENERO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSRepresentanteInteresadoEnt', 'complexType', 'struct', 'all', '', $representante_incoming);
//*************************************************************************************************************************
$interesados_incoming = array(
	'INTERESADO_ID' => array('name' => 'INTERESADO_ID', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'PRIMER_NOMBRE' => array('name' => 'PRIMER_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SEGUNDO_NOMBRE' => array('name' => 'SEGUNDO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'PRIMER_APELLIDO' => array('name' => 'PRIMER_APELLIDO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'SEGUNDO_APELLIDO' => array('name' => 'SEGUNDO_APELLIDO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'TIPO_IDENTIFICACION' => array('name' => 'TIPO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CIUDAD' => array('name' => 'CIUDAD', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'CIUDAD_CODIGO' => array('name' => 'CIUDAD_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'NUMERO_IDENTIFICACION' => array('name' => 'NUMERO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_GENERO' => array('name' => 'TIPO_GENERO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'DIRECCION' => array('name' => 'DIRECCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CODIGO_POSTAL' => array('name' => 'CODIGO_POSTAL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'TELEFONO' => array('name' => 'TELEFONO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'CELULAR' => array('name' => 'CELULAR', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'EMAIL' => array('name' => 'EMAIL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
	'INTR_INFO_REPRESENTANTE' => array('name' => 'INTR_INFO_REPRESENTANTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'INFO_REPRESENTANTE' => array('name' => 'INFO_REPRESENTANTE', 'type' => 'tns:WSRepresentanteInteresadoEnt', 'minOccurs' => 0, 'maxOccurs' => 0)
);
$server->wsdl->addComplexType('WSInteresadoEnt', 'complexType', 'struct', 'all', '', $interesados_incoming);
$server->wsdl->addComplexType('InteresadoEntOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:WSInteresadoEnt[]')), 'tns:WSInteresadoEnt');
//*************************************************************************************************************************
$servicio_incoming = array(
	'PUNTO_RADICACION' => array('name' => 'PUNTO_RADICACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'TIPO_SERVICIO' => array('name' => 'TIPO_SERVICIO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'DETALLE' => array('name' => 'DETALLE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_DIGIT' => array('name' => 'ARCHIVO_DIGIT', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'ARCHIVO_NOMBRE' => array('name' => 'ARCHIVO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
	'EMAIL_DESTINO' => array('name' => 'EMAIL_DESTINO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FOLIOS' => array('name' => 'FOLIOS', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUMERO_GUIA' => array('name' => 'NUMERO_GUIA', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FUNCIONARIO_DESTINO' => array('name' => 'FUNCIONARIO_DESTINO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'CARGO_DESTINATARIO' => array('name' => 'CARGO_DESTINATARIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'DIRECCION_DESTINATARIO' => array('name' => 'DIRECCION_DESTINATARIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'RADICADO_COM' => array('name' => 'RADICADO_COM', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'TIPO_COM' => array('name' => 'TIPO_COM', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUMERO_RESOLUCION' => array('name' => 'NUMERO_RESOLUCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'FECHA_RESOLUCION' => array('name' => 'FECHA_RESOLUCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'RADICADO_ORIGEN' => array('name' => 'RADICADO_ORIGEN', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'PRIORIDAD_SERVICIO' => array('name' => 'PRIORIDAD_SERVICIO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'MARCO_NORMATIVO' => array('name' => 'MARCO_NORMATIVO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'DOCUMENTO_ORIGEN' => array('name' => 'DOCUMENTO_ORIGEN', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
	'NUID_RADICA' => array('name' => 'NUID_RADICA', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSServicioCom', 'complexType', 'struct', 'all', '', $servicio_incoming);
//*************************************************************************************************************************
$serviciocom_response = array(
	'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
	'RADICADO' => array('name' => 'RADICADO', 'type' => 'xsd:string'),
	'FECHA_CREACION' => array('name' => 'FECHA_CREACION', 'type' => 'xsd:string'),
	'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
	'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
	'MSG_INFO' => array('name' => 'MSG_INFO', 'type' => 'xsd:string')
);
$server->wsdl->addComplexType('ServicioComInfoEnt', 'complexType', 'struct', 'all', '', $serviciocom_response);
//*************************************************************************************************************************
$server->wsdl->addComplexType('Remitente', 'complexType', 'struct', 'all', '', $remitentes_response);
$server->wsdl->addComplexType('RemitentesOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Remitente[]')), 'tns:Remitente');
//*************************************************************************************************************************
$server->wsdl->addComplexType('MessageReponse', 'complexType', 'struct', 'all', '', $response_default);
$server->wsdl->addComplexType('ActoAdministrativoInfoEnt', 'complexType', 'struct', 'all', '', $actoadministrativo_response);
$server->wsdl->addComplexType('ComEnviadaInfoEnt', 'complexType', 'struct', 'all', '', $comenviada_response);
$server->wsdl->addComplexType('AttachInfoEnt', 'complexType', 'struct', 'all', '', $attach_response);
//*************************************************************************************************************************
$server->register('AddActoAdministrativo', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntActoAdministrativo' => 'tns:WSActoAdministrativo', 'EntInteresado' => 'tns:InteresadoEntOfList'), array('return' => 'tns:ActoAdministrativoInfoEnt'), $ns, $ns . '#AddActoAdministrativo', false, false, 'Metodo para crear un acto administrativo');
$server->register('AddRadicadoSalidaEnt', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntComEnviada' => 'tns:WSComEnviada', 'EntInteresado' => 'tns:InteresadoEntOfList', 'EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ComEnviadaInfoEnt'), $ns, $ns . '#AddRadicadoSalidaEnt', false, false, 'Metodo para radicar una comunicacion externa enviada');
$server->register('AddRadicadoSalidaOferta', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntComEnviada' => 'tns:WSComEnviadaOferta', 'EntInteresado' => 'tns:InteresadoEntOfList', 'EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ComEnviadaInfoEnt'), $ns, $ns . '#AddRadicadoSalidaOferta', false, false, 'Metodo para radicar una comunicacion externa enviada por oferta');
//$server->register('AddAttachRadicadoSalida',array('EntSecurity' => 'tns:WSSimadSecurity','EntAttachComEnviada' => 'tns:WSAttachDocComEnviada'), array('return' => 'tns:AttachInfoEnt'), $ns, $ns.'#AddAttachRadicadoSalida', false, false, 'Funcion para cargar documentos a una comunicacion externa enviada');
$server->register('AddAttachDocumento', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntAttachDocument' => 'tns:WSAttachDocument'), array('return' => 'tns:AttachInfoEnt'), $ns, $ns . '#AddAttachDocumento', false, false, 'Funcion para cargar documentos a una comunicacion, a un expediente o al modulo de servicios');
$server->register('ListAttachDocumento', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntViewAttachDoc' => 'tns:WSViewAttachDoc'), array('return' => 'tns:ViewAttachInfoEnt'), $ns, $ns . '#ListAttachDocumento', false, false, 'Funcion para descargar documentos adjuntos de una comunicacion o un expediente');
$server->register('AddServicioComPublic', array('EntSecurity' => 'tns:WSSimadSecurity', 'EntComServicio' => 'tns:WSServicioCom', 'EntInteresado' => 'tns:InteresadoEntOfList', 'EntRemitente' => 'tns:WSRemitenteEnt'), array('return' => 'tns:ServicioComInfoEnt'), $ns, $ns . '#AddServicioComPublic', false, false, 'Funcion para radicar una solicitud de servicio');
//*************************************************************************************************************************
//$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
//$server->service($HTTP_RAW_POST_DATA);
//*************************************************************************************************************************
$contens = file_get_contents("php://input");
@$server->service($contens);
