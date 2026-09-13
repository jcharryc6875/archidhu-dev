<?php

/**
 * Subclass for performing query and update operations on the 'dependencia' table.
 *
 * 
 *
 * @package lib.model
 */ 

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class DependenciaPeer extends BaseDependenciaPeer
{
	
    /**
     * DependenciaPeer::addNewTrd($params,$isBackObj = true)
     * funcion para crear una nueva TRD
     * @return object or json basic data
     * @params array que contine todos los datos para crear la comunicacion recibida
     * @isBackObj parametro que indica si retorna el objeto o un json
    */
    public static function addNewTrd($params,$isBackObj = false)
    {
        $list_newreg = array();
        //********************************************************************************************************
        try
        {
            $dependencia = DependenciaPeer::getDependenciaByNombreAndCodigo(trim($params['CODIGO_DEPENDENCIA']) ,trim($params['NOMBRE_DEPENDENCIA']));
            //****************************************************************************************************
            if(empty($dependencia)){
                $dependencia = new Dependencia();
                $dependencia_anterior = $dependencia->copy();
                //$fecha_creacion = date("Y-m-d G:i:s");
                //************************************************************************************************
                $entidad = EntidadPeer::getEntidadByNombre($params['ENTIDAD']);
                $dependencia->setEntidadId($entidad->getPrimaryKey());
                //************************************************************************************************
                $oficina_productora = OficinaProductoraPeer::getOfProductoraByNombre($params['OFICINA_PRODUCTORA']);
                $dependencia->setOficinaproductoraId($oficina_productora != null ? $oficina_productora->getPrimaryKey() : 1);
                //************************************************************************************************
                $dependencia->setNombre(isset($params['NOMBRE_DEPENDENCIA']) ? trim($params['NOMBRE_DEPENDENCIA']) : null);
                $dependencia->setCodigo(isset($params['CODIGO_DEPENDENCIA']) ? trim($params['CODIGO_DEPENDENCIA']) : null);
                $dependencia->setTipoTabla(isset($params['TIPO_TABLA']) ? trim($params['TIPO_TABLA']) : "TRD");
                $dependencia->setTiempoPrestamo(isset($params['TIEMPO_PRESTAMO']) ? trim($params['TIEMPO_PRESTAMO']) : "TRD");
                $dependencia->setEsActual(1);
                $dependencia->setVersionInst(isset($params['VERSION_INST']) ? trim($params['VERSION_INST']) : NULL);
                $dependencia->save();
                //************************************************************************************************
                if($dependencia->getPrimaryKey() != null){
                    $list_newreg['dependencias_list'][] = $isBackObj ? $dependencia : $dependencia->getPrimaryKey();
                    AuditLogPeer::guardarAuditoriaLite("Dependencia",$dependencia_anterior,$dependencia,ModulesEnable::Seguridad,$dependencia->getCodigo(),$params['USUARIO_ID']);
                }else{
                    return null; 
                }
            }
            //****************************************************************************************************
            $serie = SeriePeer::getSerieByNombreAndCodigo(trim($params['CODIGO_SERIE']),trim($params['DESCIPCION_SERIE']));
            //****************************************************************************************************
            if(empty($serie) && !empty($dependencia)){
                $serie = new Serie();
                $serie_anterior = $serie->copy();

                $serie->setDependenciaId($dependencia->getPrimaryKey());
                $serie->setDescripcion(isset($params['DESCIPCION_SERIE']) ? trim($params['DESCIPCION_SERIE']) : null);
                $serie->setCodigo(isset($params['CODIGO_SERIE']) ? trim($params['CODIGO_SERIE']) : null);
                $serie->setEsVisible(1);
                $serie->save();
                //************************************************************************************************
                if($serie->getPrimaryKey() != null){
                    $list_newreg['series_list'][] = $isBackObj ? $serie : $serie->getPrimaryKey();
                    AuditLogPeer::guardarAuditoriaLite("Serie",$serie_anterior,$serie,ModulesEnable::Seguridad,$serie->getCodigo(),$params['USUARIO_ID']);
                }
            }
            //****************************************************************************************************
            $subserie = SubseriePeer::getSubserieByNombreAndCodigo(trim($params['CODIGO_SUBSERIE']),trim($params['DESCIPCION_SUBSERIE']));
            //****************************************************************************************************
            if(empty($subserie) && !empty($serie)){
                $subserie = new Subserie();
                $subserie_anterior = $subserie->copy();

                $subserie->setSerieId($serie->getPrimaryKey());
                $subserie->setDescripcion(isset($params['DESCIPCION_SUBSERIE']) ? trim($params['DESCIPCION_SUBSERIE']) : null);
                $subserie->setCodigo(isset($params['CODIGO_SUBSERIE']) ? trim($params['CODIGO_SUBSERIE']) : null);
                $subserie->setAnosEnGestion(isset($params['ANOS_EN_GESTION']) ? trim($params['ANOS_EN_GESTION']) : null);
                $subserie->setAnosEnCentral(isset($params['ANOS_EN_CENTRAL']) ? trim($params['ANOS_EN_CENTRAL']) : null);
                $subserie->setAnosEnHistorico(isset($params['ANOS_EN_HISTORICO']) ? trim($params['ANOS_EN_HISTORICO']) : null);
                $subserie->setAnosValoracionDocumental(isset($params['ANOS_VALORACION_DOCUMENTAL']) ? trim($params['ANOS_VALORACION_DOCUMENTAL']) : null);
                $subserie->setDescartefinalsubserieId(isset($params['DESCARTE_FINAL']) ? trim($params['DESCARTE_FINAL']) : 1);
                //************************************************************************************************
                $tipo_firma_digital = TipoFirmaDigitalPeer::getTipoFirmaDigitalDescripcion($params['TIPO_FIRMA_DIGITAL']);
                $subserie->setTipofirmadigitalId($tipo_firma_digital != null ? $tipo_firma_digital->getPrimaryKey() : null);
                //************************************************************************************************
                $subserie->setProcedimiento(isset($params['PROCEDIMIENTO']) ? trim($params['PROCEDIMIENTO']) : null);
                //************************************************************************************************
                $nivel_confidencialidad = NivelConfidencialidadPeer::getNivelConfidencialidadByNombre($params['NIVEL_CONFIDENCIALIDAD']);
                $subserie->setNivelconfidencialidadId($nivel_confidencialidad != null ? $nivel_confidencialidad->getPrimaryKey() : null);
                //************************************************************************************************
                $subserie->setEsVisible(1);
                $subserie->save();
                //************************************************************************************************
                if($subserie->getPrimaryKey() != null){
                    $list_newreg['subserie_list'][] = $isBackObj ? $subserie : $subserie->getPrimaryKey();
                    AuditLogPeer::guardarAuditoriaLite("Subserie",$subserie_anterior,$subserie,ModulesEnable::Seguridad,$subserie->getCodigo(),$params['USUARIO_ID']);
                }
            }
            //****************************************************************************************************
            $tipo_documental = TipoDocumentalPeer::getTipoDocByNombreAndCodigo(trim($params['CODIGO_TIPODOC']),trim($params['DESCRIPCION_DOC']));
            //****************************************************************************************************
            if(empty($tipo_documental) && !empty($subserie)){
                $tipo_documental = new TipoDocumental();
                $tipo_documental_anterior = $serie->copy();

                $tipo_documental->setSubserieId($subserie->getPrimaryKey());
                $tipo_documental->setDescripcion(isset($params['DESCRIPCION_DOC']) ? trim($params['DESCRIPCION_DOC']) : null);
                $tipo_documental->setCodigo(isset($params['CODIGO_TIPODOC']) ? trim($params['CODIGO_TIPODOC']) : null);
                $tipo_documental->setOrden(isset($params['ORDEN_TIPODOC']) ? trim($params['ORDEN_TIPODOC']) : null);
                //************************************************************************************************
                $tipodoc_cierre = 0;
                if(isset($params['CIERRA_EXPEDIENTE'])){
                    $tipodoc_cierre = trim($params['CIERRA_EXPEDIENTE']) == "SI" ? 1 : 0;
                }
                $tipo_documental->setCierraExpediente($tipodoc_cierre);
                //************************************************************************************************
                if($params['DOCF'] == "SI" && $params['DOCE'] == "SI")
                    $tipo_documental->setSoporteunidaddocumentalId(9);
                else if($params['DOCF'] == "SI")
                    $tipo_documental->setSoporteunidaddocumentalId(8);
                else if($params['DOCE'] == "SI")
                    $tipo_documental->setSoporteunidaddocumentalId(11);
                else
                    $tipo_documental->setSoporteunidaddocumentalId(null);
                //************************************************************************************************
                $tipo_documental->save();
                //************************************************************************************************
                if($serie->getPrimaryKey() != null){
                    $list_newreg['tiposdoc_list'][] = $isBackObj ? $tipo_documental : $tipo_documental->getPrimaryKey();
                    AuditLogPeer::guardarAuditoriaLite("TipoDocumental",$tipo_documental_anterior,$tipo_documental,ModulesEnable::Seguridad,$tipo_documental->getCodigo(),$params['USUARIO_ID']);
                }
            }
            //****************************************************************************************************
            if(count($list_newreg)){
                return array('isError' => false, 'message' => 'Trds importadas exitosamente', 'list_newreg' => $list_newreg);
            }else{
                return array('isError' => true, 'message' => 'Ocurrio un error y ningun registro se importo', 'list_newreg' => arayy());
            }
        }catch(PropelException $ex){
            return array('isError' => true, 'message' => 'Error de acceso a la base de datos','list_newreg' => $list_newreg);
        }catch(\Exception $ex){
            return array('isError' => true, 'message' => 'Error interno de la aplicacion','list_newreg' => $list_newreg);
        }catch(\Throwable $ex){
            return array('isError' => true, 'message' => 'Error interno del servidor','list_newreg' => $list_newreg);
        }
    }

	public static function getDependenciaAll()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $listalldep = sfContext::getInstance()->getUser()->checkPerm('TRD_LISTAR_DEPENDENCIAS_TODAS_ENTIDADES', $usuariologuiado);
        //**********************************************************************************************
        $criteria = new Criteria();
        //**********************************************************************************************
        if(!$listalldep){
  		    $criteria->add(DependenciaPeer::ENTIDAD_ID,$entidad_conectado);
        }      	
        //**********************************************************************************************
        $criteria->add(DependenciaPeer::ES_ACTUAL,1);
        $criteria->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);        
        return DependenciaPeer::doSelect($criteria);
    }
    
    public static function getDependenciaUserLoadList()
    {        
        $criteria = new Criteria();
        //**********************************************************************************************
        $criteria->add(DependenciaPeer::ES_ACTUAL,1);
        $criteria->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);        
        return DependenciaPeer::doSelect($criteria);
    }
	
	public static function getDependenciaByRecepCom()
    {        
        $criteria = new Criteria();
        $criteria->setDistinct();
        //**********************************************************************************************
        $criteria->add(DependenciaPeer::ES_ACTUAL,1);
        $criteria->addJoin(DependenciaPeer::DEPENDENCIA_ID,ReceptorComunicacionesPeer::DEPENDENCIA_ID);
        $criteria->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);        
        return DependenciaPeer::doSelect($criteria);
    }
	
    public static function getDependenciaAllJoin()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //*******************************************************************
        $listalldep = sfContext::getInstance()->getUser()->checkPerm('TRD_LISTAR_DEPENDENCIAS_TODAS_ENTIDADES', $usuariologuiado);
        //*******************************************************************
        $c = new Criteria();
        //*******************************************************************
        if(!$listalldep){
  		    $c->add(DependenciaPeer::ENTIDAD_ID,$entidad_conectado);
        }      	
        //*******************************************************************
        $c->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);        
        return DependenciaPeer::doSelectJoinAll($c);
    }
    
    public static function getDependenciaIdByUser($userdestino_id)
    {
		if(!trim($userdestino_id)) { return ""; }
		//*******************************************************************
		$user_destino = UsuarioPeer::retrieveByPk($userdestino_id);
		return $user_destino->getDependenciaId();
	}
    
    public static function getCodigoDependenciaById($dependencia_id)
    {
		if(!trim($dependencia_id)) { return ""; }
		//*******************************************************************
		$dependencia = DependenciaPeer::retrieveByPk($dependencia_id);
		return trim($dependencia->getCodigo());
	}

    public static function getDependenciaByCodigo($codigo = null)
    {
		if(!trim($codigo)) { return ""; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(DependenciaPeer::CODIGO,$codigo);
        $dependencia = DependenciaPeer::doSelectOne($c);
        //*******************************************************************
		return $dependencia;
	}

    public static function getDependenciaByNombreAndCodigo($codigo = null, $nombre = null)
    {
		if(empty(trim($codigo)) || empty(trim($nombre))) { return null; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(DependenciaPeer::CODIGO,$codigo);
        $c->add(DependenciaPeer::NOMBRE,$nombre);
        $dependencia = DependenciaPeer::doSelectOne($c);
        //*******************************************************************
		return $dependencia;
	}

    public static function getDependenciaPorArchivo($permiso,$form_tag,$param_trd)
    {
        $usuario_conectado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************OBTENER EL PARAMETRO PARA EL MANEJO DE TRD***********************
        $param_factory = ParametroPeer::retrieveByPK(61);
        $param_trd = $param_factory->getValornumerico();
        //***************************************************************************************
        $objusuario = UsuarioPeer::retrieveByPK($usuario_conectado);//usuario logueado
        $entidad_id = $objusuario->getRegional()->getEntidadId();//entidad del usuario
        $dependencia_usuario = $objusuario->getDependenciaId();//dependencia del usuario logueado
        //***************************************************************************************
        $tipo_tabla = "TRD";//tipo de tabla para creacion
        $perm_all_depen =  sfContext::getInstance()->getUser()->checkPerm("ARCHIVO_CREAR_EXPEDIENTE_TVD", $usuario_conectado);
        $perm_alldep =  sfContext::getInstance()->getUser()->checkPerm("LISTAR_EXPEDIENTES_TODAS_ENTIDADES", $usuario_conectado);
        $perm_all_subseries =  sfContext::getInstance()->getUser()->checkPerm("VER_TODAS_LAS_SUBSERIES", $usuario_conectado);
        //***************************************************************************************
        if($param_trd == 2){
            $c = new Criteria();
            $c->setDistinct();
            $c->addJoin(MacroProcesoPeer::MACROPROCESO_ID,ProcesosPeer::MACROPROCESO_ID);
            $c->addJoin(ProcesosPeer::PROCESOS_ID,OficinaProductoraPeer::PROCESOS_ID);
            $c->addJoin(OficinaProductoraPeer::OFICINAPRODUCTORA_ID,DependenciaPeer::OFICINAPRODUCTORA_ID);
            //***********************************************************************************
            if($permiso == 2 && !$perm_all_subseries){	  		  	
                // CONSULTA DE MACRO PROCESOS
                $c->addJoin(DependenciaPeer::DEPENDENCIA_ID, SeriePeer::DEPENDENCIA_ID);                
                $c->addJoin(SeriePeer::SERIE_ID, SubseriePeer::SERIE_ID);
                $c->addJoin(SubseriePeer::SUBSERIE_ID, SubseriePorUsuarioPeer::SUBSERIE_ID);
                $c->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_conectado);
                //*******************************************************************************
                if($form_tag != 1){ $c->add(SubseriePorUsuarioPeer::CREACION, true); }
                //*******************************************************************************
            }
            //***********************************************************************************
            //SI ES PARA CREACION SOLO SE LISTAN LOS MACRO PROCESOS QUE TIENEN DEPENDENCIAS EN TRD
            if($form_tag != 1 && !$perm_all_depen){
                $c->add(DependenciaPeer::TIPO_TABLA,$tipo_tabla);
                $c->add(DependenciaPeer::ES_ACTUAL,1);
            }
            //***********************************************************************************
            //validar permiso para listar macro procesos para todas las entidades
            if(!$perm_alldep){
                $c->add(DependenciaPeer::ENTIDAD_ID, $entidad_id);
            }
            //***********************************************************************************
            $c->addAscendingOrderByColumn(MacroProcesoPeer::DESCRIPCION);
            return MacroProcesoPeer::doSelect($c);
        }elseif($param_trd == 1){
            $c = new Criteria();
            $c->setDistinct();
            //***********************************************************************************
            if(($permiso == 2) && !$perm_all_subseries){
                // PARA CARPTURAR DEPENDENCIAS DE SUBSERIES POR USUARIO EN ARCHIVO DE GESTION
                $dependencias_disponibles = array();    	  	
                $dependencias_disponibles = DependenciaPeer::getDepForSubseriesUsuario($usuario_conectado, $dependencia_usuario);
                //*******************************************************************************
                // ADICIONAMOS LAS DEPENDENCIAS DISPONIBLES PARA EL USUARIO ACTUAL    	  	
                $c->add(DependenciaPeer::DEPENDENCIA_ID, $dependencias_disponibles, Criteria::IN);
                //*******************************************************************************
            }
            //***********************************************************************************
            //SI ES PARA CREACION SOLO SE LISTAN LOS MACRO PROCESOS QUE TIENEN DEPENDENCIAS EN TRD
            if($form_tag != 1 && !$perm_all_depen){
                $c->add(DependenciaPeer::TIPO_TABLA,$tipo_tabla);
                $c->add(DependenciaPeer::ES_ACTUAL,1);
            }
            //***********************************************************************************
            //validar permiso para listar macro procesos para todas las entidades
            if(!$perm_alldep){ $c->add(DependenciaPeer::ENTIDAD_ID, $entidad_id); }
            //***********************************************************************************
            $c->addAscendingOrderByColumn(DependenciaPeer::NOMBRE);
            return DependenciaPeer::doSelectJoinAll($c);
        }
    }

    public static function getDepForSubseriesUsuario($usuario_id,$dependencia_id)
    {
        // PARA CARPTURAR DEPENDENCIAS DE SUBSERIES POR USUARIO EN ARCHIVO DE GESTION
        $s = new Criteria();
        $s->setDistinct();
        $s->addJoin(SubseriePorUsuarioPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
        $s->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
        $s->addJoin(SeriePeer::DEPENDENCIA_ID,DependenciaPeer::DEPENDENCIA_ID);
        $s->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_id);
        /*******************************************************************************/
        $s->clearSelectColumns();
        /*******************************************************************************/
        $s->addSelectColumn(DependenciaPeer::DEPENDENCIA_ID);
        /*******************************************************************************/
        $resultset = SubseriePorUsuarioPeer::doSelectStmt($s);
        $dependencias_disponibles = array();
        while($object = $resultset->fetch())
        {
            if($object[0] != $dependencia_id)
            {
                $dependencias_disponibles[] = $object[0];
            }
        }
        $dependencias_disponibles[] = $dependencia_id;  	
        /*******************************************************************************/
        return  $dependencias_disponibles;
    }

    public static function guardarAuditoria($registro_anterior,$registro_nuevo)
    {
        try{ 
            $campos_objeto = DependenciaPeer::getFieldNames();
            $valor_anterior = array();
            $valor_nuevo = array();

            foreach($campos_objeto as $field){
				$instanceMethod = 'get'.$field;
				$valor_anterior[] = $registro_anterior->$instanceMethod();
				$valor_nuevo[] = $registro_nuevo->$instanceMethod();
            }
            //**************************************************************************************
            sfContext::getInstance()->getUser()->guardarAuditoria(1,$campos_objeto,$valor_anterior,$valor_nuevo,$registro_nuevo->getCodigo());
        }catch (Exception $ex){       
            $msg_error = $ex->getMessage();       
        }
    }

    /**
    * objectActions::readFileComCombined()
    * Obtiene los datos de un archivo de excel
    * @param $inputFileName rutal absoluta al archivo de excel en el servidor
	* @param $readHeaders indica si la primera fila son los encabezados del archivo
	* @param $deleteFile indica si se debe eliminar el archivo temporal
	* @param $rows_read inidca cuantas filas maximo se van a leer del archivo de excel
    * @return mixed array lista con los resultados
    */
	public static function readFileComCombined($inputFileName, $readHeaders=true, $deleteFile = false, $rows_read = 1000)
    {
        require_once sfConfig::get('sf_lib_dir') . '/Spout/Autoloader/autoload.php';
        //*************************************************************************************************************
        $simad_util = new simad_util();
        $sheetData = array();
        $msg_error = "";
        $array_data = array();
        $sheetData = array();
        //*************************************************************************************************************
        if (trim($inputFileName)){
            try {
                $reader = ReaderEntityFactory::createXLSXReader();
                //$reader->setShouldPreserveEmptyRows(true);
                $reader->open($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                return null;
            }
            //*********************************************************************************************************
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowNumber => $rows) {
                    $rowNumber -= 1;
                    if($rowNumber > $rows_read){ break 2; }
                    // do not empty row
                    $errors = array_filter($rows);
                    if (empty($errors)) {
                        continue;
                    }
                    // do stuff with the row
                    if($readHeaders){
                        $array_data['headerList'] = array_merge(array("rowNumber"),$rows);
                        $readHeaders = false;
                    }else{
                        $sheetData[] = array_merge(array($rowNumber),$rows);
                    }
                }
                break;
            }
            //*********************************************************************************************************            
            $sheetDataSerialize = $simad_util->getArrayForSend($sheetData);           
            $array_data['sheetData'] = $sheetData;
            $array_data['sheetDataSerialize'] = $sheetDataSerialize;
            $array_data['cod_msg'] = 0;
            $array_data['msg_error'] = "";
            //*********************************************************************************************************
            if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        }else{
            $msg_error = "Debe seleccionar el archivo que contiene las registros para importar";
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = $msg_error;
        }
        //*************************************************************************************************************
        return $array_data;
    }

    /**
     * ComMigmasivoPeer::getIsValidByIdBatch()
    * funcion para validar los datos de un lote de radicacion masiva
    * @param idLote mixed id del lote de migracion
    * @return mixed resultado proceso array('isError' => true|false, 'message' => 'mensaje de error o exito')
    */
    public static function getIsValidByIdBatch($list_datarows = array())
    {
        try {
            $listall_valid = array();
            $listfields_madatory = array('ENTIDAD','NOMBRE_DEPENDENCIA','CODIGO_DEPENDENCIA','TIPO_TABLA','VERSION_INST','CODIGO_SERIE',
                'DESCIPCION_SERIE','CODIGO_SUBSERIE','DESCIPCION_SUBSERIE','ANOS_EN_GESTION','ANOS_EN_CENTRAL','ANOS_EN_HISTORICO',
                'NIVEL_CONFIDENCIALIDAD','CTOTAL','MTECN','SELECCION','ELIMINAR','CODIGO_TIPODOC','DESCRIPCION_DOC','ORDEN_TIPODOC',
                'CIERRA_EXPEDIENTE');
            //*******************************************************************************************
            $index = 1;
            foreach ($list_datarows as $data_row) {
                foreach ($listfields_madatory as $rmadatory) {
                    if(!simad_util::array_check($data_row,$rmadatory)){
                        $listall_valid[] = sprintf("El campo %s para la fila %s no existe y es obligatorio", $rmadatory, $index);
                    }
                }
                //***************************************************************************************
                $index++;
            }
            //*******************************************************************************************
            if(count($listall_valid)){ return $listall_valid; }
            //*******************************************************************************************
            $index = 1;
            foreach ($list_datarows as $data_row) {
                $dependencia = DependenciaPeer::getDependenciaByNombreAndCodigo($data_row['CODIGO_DEPENDENCIA'],$data_row['NOMBRE_DEPENDENCIA']);
                if($dependencia != null)
                    $listall_valid[] = sprintf("La dependencia %s - %s de la fila %s ya existe en el SGDEA", $data_row['CODIGO_DEPENDENCIA'], $data_row['NOMBRE_DEPENDENCIA'] , $index);
                //***************************************************************************************
                $entidad = EntidadPeer::getEntidadByNombre($data_row['ENTIDAD']);
                if($entidad == null)
                    $listall_valid[] = sprintf("La entidad %s de la fila %s NO existe en el SGDEA", $data_row['ENTIDAD'] , $index);
                //***************************************************************************************
                $oficina_productora = OficinaProductoraPeer::getOfProductoraByNombre($data_row['OFICINA_PRODUCTORA']);
                if($oficina_productora == null)
                    $listall_valid[] = sprintf("La oficina productoria %s de la fila %s NO existe en el SGDEA", $data_row['OFICINA_PRODUCTORA'] , $index);
                //***************************************************************************************
                $tipo_tabla = trim($data_row['TIPO_TABLA']);
                if(!in_array($tipo_tabla,array('TRD','TVD')))
                    $listall_valid[] = sprintf("El tipo de tabla %s de la fila %s no es valido", $tipo_tabla , $index);
                //***************************************************************************************
                $serie = SeriePeer::getSerieByNombreAndCodigo($data_row['CODIGO_SERIE'],$data_row['DESCIPCION_SERIE']);
                if($serie != null)
                    $listall_valid[] = sprintf("La serie %s - %s de la fila %s ya existe en el SGDEA", $data_row['CODIGO_SERIE'], $data_row['DESCIPCION_SERIE'] , $index);
                //***************************************************************************************
                $subserie = SubseriePeer::getSubserieByNombreAndCodigo($data_row['CODIGO_SUBSERIE'],$data_row['DESCIPCION_SUBSERIE']);
                if($subserie != null)
                    $listall_valid[] = sprintf("La subserie %s - %s de la fila %s ya existe en el SGDEA", $data_row['CODIGO_SUBSERIE'], $data_row['DESCIPCION_SUBSERIE'] , $index);
                //***************************************************************************************
                $tipo_documental = TipoDocumentalPeer::getTipoDocByNombreAndCodigo($data_row['CODIGO_TIPODOC'],$data_row['DESCRIPCION_DOC']);
                if($tipo_documental != null)
                    $listall_valid[] = sprintf("El tipo documental %s - %s de la fila %s ya existe en el SGDEA", $data_row['CODIGO_TIPODOC'], $data_row['DESCRIPCION_DOC'] , $index);
                //***************************************************************************************
                $nivel_confidencialidad = NivelConfidencialidadPeer::getNivelConfidencialidadByNombre($data_row['NIVEL_CONFIDENCIALIDAD']);
                if($nivel_confidencialidad != null)
                    $listall_valid[] = sprintf("El nivel de confidencialidad %s de la fila %s no existe en el SGDEA", $data_row['NIVEL_CONFIDENCIALIDAD'] , $index);
            }
            //*******************************************************************************************
            $elist_msg = array();
            foreach ($listall_valid as $ierror) {
                if(is_array($ierror))
                {
                    if($ierror['isError']){
                        $elist_msg[] = $ierror['message'];
                    }
                }            
            }
            //*******************************************************************************************
            return $elist_msg;
        } catch (PropelException $th) {
            //throw $th;
            return array('Error interno del servidor, '.$th->getMessage());
        } catch (\Throwable $th) {
            //throw $th;
            return array('Error interno del servidor, '.$th->getMessage());
        } catch (\Throwable $th) {
            //throw $th;
            return array('Error interno del servidor, '.$th->getMessage());
        }
    }

    /**
    * ComMigmasivoPeer::getRowsFilesToArray()
    * Genera un array con la estructura necesaria de datos para la migracion de las TRDs, con la informacion del excel 
    * @param mixed @list_datarows array de datos leidos desde el archivo de excel
    * @return mixed @ilist lista de registros ajustados a la estructura necesaria para la migracion
    */
    public static function getRowsFilesToArray($list_datarows = array(),$usuario_id)
    {
        try {
            $list_nrow = array();
            foreach($list_datarows as $row)
            {
                $dataRow = array();
                $dataRow['USUARIO_ID'] = $usuario_id;
                $dataRow['ENTIDAD'] = trim($row[1]) ? trim($row[1]) : null;
                $dataRow['OFICINA_PRODUCTORA'] = trim($row[2]) ? trim($row[2]) : null;
                $dataRow['NOMBRE_DEPENDENCIA'] = trim($row[3]) ? trim($row[3]) : null;
                $dataRow['CODIGO_DEPENDENCIA'] = trim($row[4]) ? trim($row[4]) : null;
                $dataRow['TIEMPO_PRESTAMO'] = trim($row[5]) ? trim($row[5]) : 5;
                $dataRow['TIPO_TABLA'] = trim($row[6]) ? trim($row[6]) : null;
                $dataRow['VERSION_INST'] = trim($row[7]) ? trim($row[7]) : null;
                $dataRow['CODIGO_SERIE'] = trim($row[8]) ? trim($row[8]) : null;
                $dataRow['DESCIPCION_SERIE'] = trim($row[9]) ? trim($row[9]) : null;
                $dataRow['CODIGO_SUBSERIE'] = trim($row[10]) ? trim($row[10]) : null;
                $dataRow['DESCIPCION_SUBSERIE'] = trim($row[11]) ? trim($row[11]) : null;
                $dataRow['ANOS_EN_GESTION'] = trim($row[12]) ? trim($row[12]) : 0;
                $dataRow['ANOS_EN_CENTRAL'] = trim($row[13]) ? trim($row[13]) : 0;
                $dataRow['ANOS_EN_HISTORICO'] = trim($row[14]) ? trim($row[14]) : 0;
                $dataRow['ANOS_VALORACION_DOCUMENTAL'] = trim($row[15]) ? trim($row[15]) : 0;
                $dataRow['TIPO_FIRMA_DIGITAL'] = trim($row[16]) ? trim($row[16]) : null;
                $dataRow['NIVEL_CONFIDENCIALIDAD'] = trim($row[17]) ? trim($row[17]) : null;
                $dataRow['CTOTAL'] = trim($row[18]) ? trim($row[18]) : "NO";
                $dataRow['MTECN'] = trim($row[19]) ? trim($row[19]) : "NO";
                $dataRow['SELECCION'] = trim($row[20]) ? trim($row[20]) : "NO";
                $dataRow['ELIMINAR'] = trim($row[21]) ? trim($row[21]) : "NO";
                $dataRow['CODIGO_TIPODOC'] = trim($row[22]) ? trim($row[22]) : null;
                $dataRow['DESCRIPCION_DOC'] = trim($row[23]) ? trim($row[23]) : null;
                $dataRow['ORDEN_TIPODOC'] = trim($row[24]) ? trim($row[24]) : null;
                $dataRow['DOCF'] = trim($row[25]) ? trim($row[25]) : "NO";
                $dataRow['DOCE'] = trim($row[26]) ? trim($row[26]) : "NO";
                $dataRow['DDHH'] = trim($row[27]) ? trim($row[27]) : "NO";
                $dataRow['DIH'] = trim($row[28]) ? trim($row[28]) : "NO";
                $dataRow['CIERRA_EXPEDIENTE'] = trim($row[29]) ? trim($row[29]) : null;
                $dataRow['PROCEDIMIENTO'] = trim($row[30]) ? trim($row[30]) : null;
                //***************************************************************************************
                $list_nrow[] = $dataRow;
            }
            //*******************************************************************************************
            return $list_nrow;
        } catch (\IOException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        } catch (\Throwable $th) {
            return array();
        }
    }
}
