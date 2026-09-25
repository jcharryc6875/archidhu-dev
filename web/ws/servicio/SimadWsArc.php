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
// SearchExp CAMPOS SUB ARRAY - SUB ARRAY - SUB ARRAY - ADJUNTOS BUSQUEDA CONTENIDO UNIDAD DOCUMENTAL
$response_search_sub_documentos_adjuntos = array(
    'GUID' => array('name' => 'GUID', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'NOMBRE_ARCHIVO' => array('name' => 'NOMBRE_ARCHIVO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'TIPO_ATTACHMENT' => array('name' => 'TIPO_ATTACHMENT', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'URL' => array('name' => 'URL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'URL_DOWNLOAD' => array('name' => 'URL_DOWNLOAD', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'SIZE_FILE' => array('name' => 'SIZE_FILE', 'type' => 'xsd:int')
);
$server->wsdl->addComplexType('AdjuntosInfo', 'complexType', 'struct', 'all', '', $response_search_sub_documentos_adjuntos);
$server->wsdl->addComplexType('AdjuntosEntOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:AdjuntosInfo[]')), 'tns:AdjuntosInfo');
//*************************************************************************************************************************
// SearchExp CAMPOS SUB ARRAY - SUB ARRAY BUSQUEDA CONTENIDO UNIDAD DOCUMENTAL
$response_search_sub_documentos = array(
    'CODIGO_TIPODOC' => array('name' => 'CODIGO_TIPODOC', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'DESCRIPCION' => array('name' => 'DESCRIPCION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'FECHA_CREACION' => array('name' => 'FECHA_CREACION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'FECHA_DOCUMENTO' => array('name' => 'FECHA_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'NOMBRE_ARCHIVO' => array('name' => 'NOMBRE_ARCHIVO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 0),
    'ADJUNTOS' => array('name' => 'ADJUNTOS', 'type' => 'tns:AdjuntosEntOfList', 'minOccurs' => 0, 'maxOccurs' => 0),
);
$server->wsdl->addComplexType('DocumentoInfo', 'complexType', 'struct', 'all', '', $response_search_sub_documentos);
$server->wsdl->addComplexType('DocumentosEntOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:DocumentoInfo[]')));
//*************************************************************************************************************************
// SearchExp CAMPOS SUB ARRAY BUSQUEDA CONTENIDO UNIDAD DOCUMENTAL
$response_search_expedientes_list = array(
    'DEPENDENCIA' => array('name' => 'DEPENDENCIA', 'type' => 'xsd:string'),
    'CODIGO_DEPENDENCIA' => array('name' => 'CODIGO_DEPENDENCIA', 'type' => 'xsd:string'),
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string'),
    'FECHA_CREACION' => array('name' => 'FECHA_CREACION', 'type' => 'xsd:string'),
    'FECHA_APERTURA' => array('name' => 'FECHA_APERTURA', 'type' => 'xsd:string'),
    'FECHA_CIERRE' => array('name' => 'FECHA_CIERRE', 'type' => 'xsd:string'),
    'NOMBRE_SERIE' => array('name' => 'NOMBRE_SERIE', 'type' => 'xsd:string'),
    'CODIGO_SERIE' => array('name' => 'CODIGO_SERIE', 'type' => 'xsd:string'),
    'NOMBRE_SUBSERIE' => array('name' => 'NOMBRE_SUBSERIE', 'type' => 'xsd:string'),
    'CODIGO_SUBSERIE' => array('name' => 'CODIGO_SUBSERIE', 'type' => 'xsd:string'),
    'FASE_ARCHIVO' => array('name' => 'FASE_ARCHIVO', 'type' => 'xsd:string'),
    'NOMBRE_EXPEDIENTE' => array('name' => 'NOMBRE_EXPEDIENTE', 'type' => 'xsd:string'),
    'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
    'LISTA_DOCUMENTOS' => array('name' => 'LISTA_DOCUMENTOS', 'type' => 'tns:DocumentosEntOfList', 'minOccurs' => 0, 'maxOccurs' => 0)
);
$server->wsdl->addComplexType('SearchExpedienteInfo', 'complexType', 'struct', 'all', '', $response_search_expedientes_list);
$server->wsdl->addComplexType('ExpedientesEntOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:SearchExpedienteInfo[]')));
//*************************************************************************************************************************
$attach_response_search = array(
    'LISTA_EXPEDIENTES' => array('name' => 'LISTA_EXPEDIENTES', 'type' => 'tns:ExpedientesEntOfList', 'minOccurs' => 0, 'maxOccurs' => 0),
    'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
    'MSG_INFO' => array('name' => 'MSG_INFO', 'type' => 'xsd:string')
);
$server->wsdl->addComplexType('SearchExpedienteInfo', 'complexType', 'struct', 'all', '', $attach_response_search);
//*************************************************************************************************************************
$attach_response = array(
    'CONSECUTIVO_ID' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
    'ID_EXPEDIENTE' => array('name' => 'CONSECUTIVO_ID', 'type' => 'xsd:int'),
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string'),
    'FECHA_TRANSACCION' => array('name' => 'FECHA_TRANSACCION', 'type' => 'xsd:string'),
    'ISERROR' =>  array('name' => 'ISERROR', 'type' => 'xsd:boolean'),
    'MSG_INFO' => array('name' => 'MSG_INFO', 'type' => 'xsd:string')
);
$server->wsdl->addComplexType('ExpedienteInfo', 'complexType', 'struct', 'all', '', $attach_response);
$server->wsdl->addComplexType('ContDocumentoInfo', 'complexType', 'struct', 'all', '', $attach_response);
//*************************************************************************************************************************
// CAMPOS TABLA REPRESENTANTE_LEGAL
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
    'EMAIL' => array('name' => 'EMAIL', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'INTR_INFO_REPRESENTANTE' => array('name' => 'INTR_INFO_REPRESENTANTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'INFO_REPRESENTANTE' => array('name' => 'INFO_REPRESENTANTE', 'type' => 'tns:WSRepresentanteInteresadoEnt', 'minOccurs' => 0, 'maxOccurs' => 0)
);
$server->wsdl->addComplexType('WSInteresadoEnt', 'complexType', 'struct', 'all', '', $interesados_incoming);
$server->wsdl->addComplexType('InteresadoEntOfList', 'complextType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:WSInteresadoEnt[]')), 'tns:WSInteresadoEnt');
//*************************************************************************************************************************
$object_data = array(
    'NOMBRE_EXPEDIENTE' => array('name' => 'NOMBRE_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'FECHA_INICIAL' => array('name' => 'FECHA_INICIAL', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'FASE_ARCHIVO' => array('name' => 'FASE_ARCHIVO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NOMBRE_SEDE' => array('name' => 'NOMBRE_SEDE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'SOPORTE_DOCUMENTO' => array('name' => 'SOPORTE_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'ESTADO_EXPEDIENTE' => array('name' => 'ESTADO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'FRECUENCIA_CONSULTA' => array('name' => 'FRECUENCIA_CONSULTA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'MEDIO_CONSERVACION' => array('name' => 'MEDIO_CONSERVACION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUID_INVENTARIADOR' => array('name' => 'NUID_INVENTARIADOR', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUID_RESPONSABLE' => array('name' => 'NUID_RESPONSABLE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_DEPENDENCIA' => array('name' => 'CODIGO_DEPENDENCIA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NOMBRE_DEPENDENCIA' => array('name' => 'NOMBRE_DEPENDENCIA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    //'CODIGO_SERIE' => array( 'name'=>'CODIGO_SERIE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    //'NOMBRE_SERIE' => array( 'name'=>'NOMBRE_SERIE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_SUBSERIE' => array('name' => 'CODIGO_SUBSERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NOMBRE_SUBSERIE' => array('name' => 'NOMBRE_SUBSERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'NUID_CREADOR' => array('name' => 'NUID_CREADOR', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'FECHA_FINAL' => array('name' => 'FECHA_FINAL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'CONTENIDO' => array('name' => 'CONTENIDO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'NOTAS' => array('name' => 'NOTAS', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'NUMERO_CAJA' => array('name' => 'NUMERO_CAJA', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'NUMERO_IDENTIFICACION' => array('name' => 'NUMERO_IDENTIFICACION', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'VOLUMEN' => array('name' => 'VOLUMEN', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
    'FOLIOS' => array('name' => 'FOLIOS', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
    'UBICACION_EXPEDIENTE' => array('name' => 'UBICACIÓN_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSUnidadDocumental', 'complexType', 'struct', 'all', '', $object_data);
//*************************************************************************************************************************
$object_data_update = array(
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_DEPENDENCIA' => array('name' => 'CODIGO_DEPENDENCIA', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_SERIE' => array('name' => 'CODIGO_SERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_SUBSERIE' => array('name' => 'CODIGO_SUBSERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NOTAS_EDICION' => array('name' => 'NOTAS_EDICION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUEVO_NOMBRE_EXPEDIENTE' => array('name' => 'NUEVO_NOMBRE_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSActualizarExpediente', 'complexType', 'struct', 'all', '', $object_data_update);
//*************************************************************************************************************************
$object_search_udocumental = array(
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ID_EXPEDIENTE' => array('name' => 'ID_EXPEDIENTE', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
    'CODIGO_DEPENDENCIA' => array('name' => 'CODIGO_DEPENDENCIA', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'CODIGO_SUBSERIE' => array('name' => 'CODIGO_SUBSERIE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'CODIGO_SERIE' => array('name' => 'CODIGO_SERIE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'FASE_ARCHIVO' => array('name' => 'FASE_ARCHIVO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'IDENTIFICACION_INTERESADO' => array('name' => 'IDENTIFICACION_INTERESADO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'PNOMBRE_INTERESADO' => array('name' => 'PNOMBRE_INTERESADO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'PAPELLIDO_INTERESADO' => array('name' => 'PAPELLIDO_INTERESADO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1)
);
$server->wsdl->addComplexType('WSSearchUnidadDocumental', 'complexType', 'struct', 'all', '', $object_search_udocumental);
//*************************************************************************************************************************
/*$object_data_udocumental = array 
    (
        'NUMERO_EXPEDIENTE' => array( 'name'=>'NUMERO_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'NOMBRE_EXPEDIENTE' => array( 'name'=>'NOMBRE_EXPEDIENTE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'FASE_ARCHIVO' => array( 'name'=>'FASE_ARCHIVO', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'SOPORTE_DOCUMENTAL' => array( 'name'=>'SOPORTE_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'ESTADO_DOCUMENTAL' => array( 'name'=>'ESTADO_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'FRECUENCIA_DOCUMENTAL' => array( 'name'=>'FRECUENCIA_DOCUMENTAL', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'MEDIO_CONSERVACION' => array( 'name'=>'MEDIO_CONSERVACION', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'FECHA_APERTURA' => array( 'name'=>'FECHA_APERTURA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'FECHA_CIERRE' => array( 'name'=>'FECHA_CIERRE', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'TITULO' => array( 'name'=>'TITULO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'CONTENIDO' => array( 'name'=>'CONTENIDO', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'FOLIOS' => array( 'name'=>'FOLIOS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'NOTAS' => array( 'name'=>'NOTAS', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'VOLUMEN' => array( 'name'=>'VOLUMEN', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'NUMERO_IDENTIFICACION' => array( 'name'=>'NUMERO_IDENTIFICACION', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'NUMERO_CAJA' => array( 'name'=>'NUMERO_CAJA', 'type'=>'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
        'NUID_RESPONSABLE' => array( 'name'=>'NUID_RESPONSABLE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'NOMBRE_SUBSERIE' => array( 'name'=>'NOMBRE_SUBSERIE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
        'CODIGO_SUBSERIE' => array( 'name'=>'CODIGO_SUBSERIE', 'type'=>'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1)
    );
    $server->wsdl->addComplexType('WSUnidadDocumental','complexType','struct','all','',$object_data_udocumental);*/
//*************************************************************************************************************************
$obj_documento = array(
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'TIPODOC_CODIGO' => array('name' => 'TIPODOC_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUIP_CREADOR' => array('name' => 'NUIP_CREADOR', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'DESCRIPCION' => array('name' => 'DESCRIPCION', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'FOLIOS' => array('name' => 'FOLIOS', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
    'FECHA_DOCUMENTO' => array('name' => 'FECHA_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'ORIGEN_DOCUMENTO' => array('name' => 'ORIGEN_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'VERIF_UDOCUMENTAL' => array('name' => 'VERIF_UDOCUMENTAL', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ESTADO_DOCUMENTO' => array('name' => 'ESTADO_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'FIRMA_ESTAMPA' => array('name' => 'FIRMA_ESTAMPA', 'type' => 'xsd:boolean', 'minOccurs' => 0, 'maxOccurs' => 1),
    'SOPORTE_DOCUMENTO' => array('name' => 'SOPORTE_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ARCHIVO_ANEXO' => array('name' => 'ARCHIVO_ANEXO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ARCHIVO_NOMBRE' => array('name' => 'ARCHIVO_NOMBRE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ORDEN_CONTENIDO' => array('name' => 'ORDEN_CONTENIDO', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSContDocumental', 'complexType', 'struct', 'all', '', $obj_documento);
//*************************************************************************************************************************
$obj_documento_com = array(
    'TIPODOC_CODIGO' => array('name' => 'TIPODOC_CODIGO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUIP_CREADOR' => array('name' => 'NUIP_CREADOR', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'RADICADO_COM' => array('name' => 'RADICADO_COM', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'TIPO_COM' => array('name' => 'TIPO_COM', 'type' => 'xsd:int', 'minOccurs' => 1, 'maxOccurs' => 1),
    'ORIGEN_DOCUMENTO' => array('name' => 'ORIGEN_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 1, 'maxOccurs' => 1),
    'NUMERO_EXPEDIENTE' => array('name' => 'NUMERO_EXPEDIENTE', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ID_EXPEDIENTE' => array('name' => 'ID_EXPEDIENTE', 'type' => 'xsd:int', 'minOccurs' => 0, 'maxOccurs' => 1),
    'SOPORTE_DOCUMENTO' => array('name' => 'SOPORTE_DOCUMENTO', 'type' => 'xsd:string', 'minOccurs' => 0, 'maxOccurs' => 1),
    'ADD_ANEXOS_COM' => array('name' => 'CREATE_ANEXOS_COM', 'type' => 'xsd:boolean', 'minOccurs' => 0, 'maxOccurs' => 1),
);
$server->wsdl->addComplexType('WSContDocumentalCom', 'complexType', 'struct', 'all', '', $obj_documento_com);
//*************************************************************************************************************************
$server->register('SearchExpedienteList', array('EntSecurity' => 'tns:WSSimadSecurity', 'TermSearchExpediente' => 'tns:WSSearchUnidadDocumental'), array('return' => 'tns:SearchExpedienteInfo'), $ns, $ns . '#SearchExpedienteList', false, false, 'Metodo para consultar un expediente');
//*************************************************************************************************************************
$server->register('CreateNewExpedienteArch', array('EntSecurity' => 'tns:WSSimadSecurity', 'ArcAddExpediente' => 'tns:WSUnidadDocumental', 'ExpInteresado' => 'tns:InteresadoEntOfList'), array('return' => 'tns:ExpedienteInfo'), $ns, $ns . '#CreateNewExpedienteArch', false, false, 'Metodo para crear un nuevo expediente');
//*************************************************************************************************************************
$server->register('ActualizarExpedienteArch', array('EntSecurity' => 'tns:WSSimadSecurity', 'ArcDataExpediente' => 'tns:WSActualizarExpediente'), array('return' => 'tns:ExpedienteInfo'), $ns, $ns . '#UpdateExpedienteArch', false, false, 'Metodo para actualizar información de un expediente existente');
//*************************************************************************************************************************
$server->register('CreateNewDocExpediente', array('EntSecurity' => 'tns:WSSimadSecurity', 'ArcAddDocumento' => 'tns:WSContDocumental'), array('return' => 'tns:ContDocumentoInfo'), $ns, $ns . '#CreateNewDocExpediente', false, false, 'Metodo para adicionar un nuevo documento en el expediente');
//*************************************************************************************************************************
$server->register('AddRadicadoComExpediente', array('EntSecurity' => 'tns:WSSimadSecurity', 'ArcAddRadicadoCom' => 'tns:WSContDocumentalCom'), array('return' => 'tns:ContDocumentoInfo'), $ns, $ns . '#CreateNewDocExpediente', false, false, 'Metodo para adicionar un nuevo documento en el expediente basado en el radicado de una comunicación(enviada o recibida)');
//*************************************************************************************************************************
$contens = file_get_contents("php://input");
@$server->service($contens);
