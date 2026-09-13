<?php

/**
 * Subclass for performing query and update operations on the 'COM_RECIBIDA' table.
 *
 * 
 *
 * @package lib.model
 */ 

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ComRecibidaPeer extends BaseComRecibidaPeer
{
    /**
     * ComRecibidaPeer::getPrimaryKeyColumnName()
     * funcion que obtiene el nombre de la llave primaria de la tabla
     * @return string nombre de la columna que es la primary key
    */
    public static function getPrimaryKeyColumnName()
    {
        $tableMap = ComRecibidaPeer::getTableMap();
        $pkColumns = $tableMap->getPrimaryKeys();
        foreach ($pkColumns as $column) {
            return $column->getName();
        }
    }
    
    /**
     * ComRecibidaPeer::getIncomingRelations($tableName)
     * funcion que devuelve las relaciones de entrada con la tabla actual
     * @param string $tableName nombre de la tabla que se evaluara
     * @return mexed array con los datos basicos de las relaciones de la tabla
    */
    public static function getIncomingRelations($tableName)
    {
        $tables = array(ComrecibidaUsuarioPeer::getOMClass(), ComrecibidaInteresadosPeer::getOMClass()); 
        $incomingRelations = array();
        
        foreach ($tables as $otherTable) {
            $peerClass = $otherTable . 'Peer';
            if (class_exists($peerClass)) {
                $tableMap = call_user_func(array($peerClass, 'getTableMap'));
                $foreignKeys = $tableMap->getForeignKeys();
                foreach ($foreignKeys as $fk) {
                    $current_ntable = $fk->getRelatedTableName();
                    if ($fk->getRelatedTableName() == $tableName) {
                        $incomingRelations[] = array(
                            'from_table' => $otherTable,
                            'from_column' => $fk->getRelatedTableName(),
                            'to_column' => $fk->getRelatedColumn()
                        );
                    }
                }
            }
        }
        
        return $incomingRelations;
    }

    /**
     * ComRecibidaPeer::addComRecibida($params,$isBackObj = true)
     * funcion que devuelve el o los ids de los usuarios asignados a la comunicacion 
     * @return object or json basic data
     * @params array que contine todos los datos para crear la comunicacion recibida
     * @isBackObj parametro que indica si retorna el objeto o un json
    */
    public static function addComRecibida($params,$isBackObj = true)
    {
        $com_recibida = new ComRecibida();
        //********************************************************************************************************
        try
        {
			$simad_util = new simad_util();
            //****************************************************************************************************
            $com_recibida = new ComRecibida();
            $com_recibida_anterior = clone $com_recibida;
			$fecha_creacion = date("Y-m-d G:i:s");
            //****************************************************************************************************
            $com_recibida->setRegionalId(isset($params['regional_id']) ? $params['regional_id'] : null);
            $com_recibida->setTipocomrecibidaId(isset($params['tipocomrecibida_id']) ? $params['tipocomrecibida_id'] : null);
            $com_recibida->setDependenciaId(isset($params['dependencia_id']) ? $params['dependencia_id'] : null);
            $com_recibida->setEstadodigitalizacionId(isset($params['estadodigitalizacion_id']) ? $params['estadodigitalizacion_id'] : 1);
            $com_recibida->setEstadocomrecibidaId(isset($params['estadocomrecibida_id']) ? $params['estadocomrecibida_id'] : 1);
            $com_recibida->setFormarecepcionId(isset($params['formarecepcion_id']) ? $params['formarecepcion_id'] : null);
            $com_recibida->setCiudadId(isset($params['ciudad_id']) ? $params['ciudad_id'] : null);	
            $com_recibida->setDirectorioexternoId(isset($params['directorioexterno_id']) ? $params['directorioexterno_id'] : null);
            $com_recibida->setRadicadoOrigen(isset($params['radicado_origen']) ? $params['radicado_origen'] : null);
            $com_recibida->setFechaCreacion($fecha_creacion);
            $com_recibida->setPeriodoId(date("Y"));
            $com_recibida->setAsuntorecibidaId(isset($params['asuntorecibida_id']) ? $params['asuntorecibida_id'] : 1);
            $com_recibida->setAsunto(isset($params['asunto']) ? mb_convert_encoding(trim($params['asunto']),'UTF-8') : null);
            $com_recibida->setMediorespuestaId(isset($params['mediorespuesta_id']) ? trim($params['mediorespuesta_id']) : null);
            $com_recibida->setTipoexpedienteId(isset($params['tipoexpediente_id']) ? trim($params['tipoexpediente_id']) : null);
            $com_recibida->setTipoprocesocomId(isset($params['tipoprocesocom_id']) ? trim($params['tipoprocesocom_id']) : 1);
            $com_recibida->setPrioridadcomId(isset($params['prioridadcom_id']) ? trim($params['prioridadcom_id']) : 1);
            $com_recibida->setNumeroFud(isset($params['numero_fud']) ? trim($params['numero_fud']) : null);
            $com_recibida->setNumeroProceso(isset($params['numero_proceso']) ? trim($params['numero_proceso']) : null);
            $com_recibida->setEmpresaMensajeriaId(isset($params['empresamensajeria_id']) ? trim($params['empresamensajeria_id']) : null);
            $com_recibida->setGuia(isset($params['numero_guia']) ? trim($params['numero_guia']) : null);
            $com_recibida->setFechaRecibido(isset($params['fecha_recibido']) ? trim($params['fecha_recibido']) : $fecha_creacion);
            //****************************************************************************************************
            $com_recibida->setEsCopia(isset($params['es_copia']) ? trim($params['es_copia']) : 0);
            $com_recibida->setFolios(isset($params['folios']) ? trim($params['folios']) : 0);
            $com_recibida->setObservaciones(isset($params['observaciones']) ? mb_convert_encoding(trim($params['observaciones']),'UTF-8') : null);
            $com_recibida->setAnexos(isset($params['anexos']) ? mb_convert_encoding(trim($params['anexos']),'UTF-8') : null);
            $com_recibida->setEstaentregado(0);
            $com_recibida->setMarca(0);
            $com_recibida->setIsLocked(isset($params['is_locked']) ? trim($params['is_locked']) : 1);
            //****************************************************************************************************
            if(isset($params['fecha_vencimiento'])){
                if(trim($params['fecha_vencimiento'])){
                    $com_recibida->setFechaMaximaRespuesta(trim($params['fecha_vencimiento']));
                }else{
                    $com_recibida->setFechaMaximaRespuesta(ComRecibidaPeer::getFechaMaxRespByTipoCom(trim($params['tipocomrecibida_id'])));
                }
            }else{
                $com_recibida->setFechaMaximaRespuesta(ComRecibidaPeer::getFechaMaxRespByTipoCom(trim($params['tipocomrecibida_id'])));
            }
            //****************************************************************************************************
            $com_recibida->save();
            //****************************************************************************************************
            $attachs = isset($params['attachments']) ? $com_recibida->getRutaAdjuntos($params['attachments'],$params['usuario_radicador']) : null;
            $com_recibida->setRuta($attachs);
            //****************************************************************************************************
            $isFileDigit = isset($params['file']) ? simad_util::NormalizePath($params['file']) : null;
            $isFullPath = isset($params['filefullpath']) ? $params['filefullpath'] : false;
            $digitOverWrite = isset($params['digitOverWrite']) ? $params['digitOverWrite'] : true;
            //****************************************************************************************************
            $usuarios_data = array();
            $usuarios_data['usuario_origen'] = $params['usuario_radicador'];
            $usuarios_data['cargo_uorigen'] = isset($params['cusuario_radicador']) ? $params['cusuario_radicador'] : CargoUsuarioPeer::getCargoUsuarioByIdUser($params['usuario_radicador']);
			$usuarios_data['usuario_destino'] = $params['usuario_destino'];
            $usuarios_data['cargo_udestino'] = isset($params['cusuario_destino']) ? $params['cusuario_destino'] : CargoUsuarioPeer::getCargoUsuarioByIdUser($params['usuario_destino']);
            //****************************************************************************************************
            ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$usuarios_data['usuario_origen'],$usuarios_data['cargo_uorigen'],1,1,0,1);
            //****************************************************************************************************
            if(!empty($usuarios_data['usuario_destino'])){
                $usuarios_data['usuario_destino'] = $params['usuario_destino'];
                $usuarios_data['cargo_udestino'] = isset($params['cusuario_destino']) ? $params['cusuario_destino'] : CargoUsuarioPeer::getCargoUsuarioByIdUser($params['usuario_destino']);
                ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$usuarios_data['usuario_destino'],$usuarios_data['cargo_udestino'],1,2,1,2);
            }else{
                $tipoproceso_dest = 2;
                $addDestinoUser = ComRecibidaPeer::addDestinoCom($com_recibida,$tipoproceso_dest,null,null,$usuarios_data['regional_destino_id']);
                if(!$addDestinoUser){
                    ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey());
                    return null;
                }
            }
            //****************************************************************************************************
            $radicado_compose = $com_recibida->getRadicadoFormat($params['regional_id'],$params['dependencia_id'],$params['entidad_id'],false);
            $com_recibida->setRadicado($radicado_compose);
            $com_recibida->setCodigoReenResp($com_recibida->getPrimaryKey());
            $com_recibida->save();
            //****************************************************************************************************
            $isCopyDigit = $com_recibida->moveDigitFile($isFileDigit,$digitOverWrite,$isFullPath);
            if($isCopyDigit){
                $com_recibida->setFechaDigit(date("Y-m-d G:i:s"));
                $com_recibida->setTipoprocesocomId(2);
                $com_recibida->setIsLocked(0);
                $com_recibida->setEstadodigitalizacionId(2);
                $com_recibida->setEstadocomrecibidaId(1);
                $com_recibida->save();
				//****************************************************************************************
				ComRecibidaPeer::updateEstadosComByEstado($com_recibida->getPrimaryKey(),1,array(2,3));
				ComRecibidaPeer::updateFechaAsignaCom($com_recibida->getPrimaryKey(),array(2));
            }else{
                $com_recibida->setFechaDigit(null);
                $com_recibida->setIsLocked(1);
                $com_recibida->setEstadodigitalizacionId(1);
                $com_recibida->setEstadocomrecibidaId(11);
                $com_recibida->save();
                //****************************************************************************************
				ComRecibidaPeer::updateEstadosComByEstado($com_recibida->getPrimaryKey(),11,array(2,3));
            }
            //****************************************************************************************************
            if($com_recibida->getEstadodigitalizacionId() == 2){
                $usuario_destino = !empty($params['usuario_destino']) ? $params['usuario_destino'] : $com_recibida->getUserIdComRecibidaRol(2);
                ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuario_destino);
            }
            //****************************************************************************************************
			AuditLogPeer::guardarAuditoriaLite("ComRecibida",$com_recibida_anterior,$com_recibida,ModulesEnable::ComRecibida,$com_recibida->getRadicado(),$params['usuario_radicador']);
            //****************************************************************************************************
            $isWfInit = ComRecibidaPeer::initWorkflowCom($com_recibida,$params['usuario_destino'],$params['usuario_radicador']);
            //****************************************************************************************************
            if(!$isWfInit && $com_recibida->getEstadodigitalizacionId() == 1 && $com_recibida->getIsLocked() == 0){
                ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$params['usuario_destino']);
            }
            //****************************************************************************************************
            if($isBackObj){
                return $com_recibida;
            }else{
                $data_intern = array();
                $data_intern['numero_radicacion'] = $com_recibida->getNumeroRadicacion();
                $data_intern['radicado'] = $com_recibida->getRadicado();
                $data_intern['asunto_com'] = mb_convert_encoding(trim($com_recibida->getAsunto()), 'UTF-8');
                $data_intern['fecha_creacion'] = $com_recibida->getFechaCreacion();            
                $data_intern['fecha_respuesta'] = $com_recibida->getFechaMaximaRespuesta();
                return ($data_intern);
            }
        }catch(PropelException $ex){
            if($com_recibida != null && $com_recibida->getPrimaryKey())
                ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey());
            return null;
        }catch(\Exception $ex){
            if($com_recibida != null && $com_recibida->getPrimaryKey())
                ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey());
            return null;
        }catch(\Throwable $ex){
            if($com_recibida != null && $com_recibida->getPrimaryKey())
                ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey());
            return null;
        }
    }
    
    public static function getHashMetadata($sqlinfo_sql, array $params = [])
    {
        try
        {
            // Validación: Solo permitir consultas SELECT
            if (!preg_match('/^\s*SELECT\s+/i', trim($sqlinfo_sql))) {
                throw new Exception('Solo se permiten consultas SELECT');
            }
            
            $conexion = Propel::getConnection();
            $sentencia = $conexion->prepare($sqlinfo_sql);
            
            // Vincular parámetros si existen
            if (!empty($params)) {
                $posicion = 1; // PDO usa posiciones desde 1, no desde 0
                foreach ($params as $valor) {
                    // Determinar el tipo de dato para PDO
                    if (is_int($valor)) {
                        $tipo = PDO::PARAM_INT;
                    } elseif (is_bool($valor)) {
                        $tipo = PDO::PARAM_BOOL;
                    } elseif (is_null($valor)) {
                        $tipo = PDO::PARAM_NULL;
                    } else {
                        $tipo = PDO::PARAM_STR;
                    }
                    
                    $sentencia->bindValue($posicion, $valor, $tipo);
                    $posicion++;
                }
            }
            
            $sentencia->execute();
            $resultset = $sentencia->fetch(PDO::FETCH_NUM);
            
            return $resultset;

        } catch(PropelException $ex) {
            error_log('PropelException en getHashMetadata: ' . $ex->getMessage());
            return false;
        } catch(\Exception $ex) {
            error_log('Exception en getHashMetadata: ' . $ex->getMessage());
            return false;
        } catch(\Throwable $ex) {
            error_log('Throwable en getHashMetadata: ' . $ex->getMessage());
            return false;
        }
    }

    public static function getReporteComRecibida($params, $apply_search = true)
	{
		try
		{
			$c = new Criteria();

			if(!empty($params['comrec_radicado']))
			{
				$c->add(ComRecibidaPeer::RADICADO, $params['comrec_radicado']);
				//$c->add(ComRecibidaPeer::TITULO, $params['nombre_expediente']);
			}
			if(!empty($params['comrec_asunto']))
			{
                $c->add(ComRecibidaPeer::ASUNTO, '%'.$params['comrec_asunto'].'%', Criteria::LIKE);
			}
			if(!empty($params['comrec_periodo']))
			{
				$c->add(ComRecibidaPeer::PERIODO_ID, $params['comrec_periodo']);
			}
			if(!empty($params['comrec_estado']))
			{
                $c->add(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID, $params['comrec_estado']);
			}
			if(!empty($params['comrec_remitente']))
			{
                $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
				$c->add(DirectorioExternoPeer::FUNCIONARIO, '%'.$params['comrec_remitente'].'%', Criteria::LIKE);
			}
			if(!empty($params['comrec_fechCreaDesde']) && !empty($params['comrec_fechCreaHasta']))
			{
				$c->add(ComRecibidaPeer::FECHA_CREACION, $params['comrec_fechCreaDesde'], Criteria::GREATER_EQUAL);  
				$c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['comrec_fechCreaHasta'], Criteria::LESS_EQUAL);
			}

            $total_array = array();
			$total_array['criteria'] = $c;
			$total_array['data_set'] = $apply_search ? ComRecibidaPeer::doSelect($c) : null;

			return $total_array;
		}
		catch(PropelException $ex)
		{
			return $ex->getMessage();
		}
		catch(\Exception $ex)
		{
			return $ex->getMessage();
		}
	}

    public static function getListIntersadosByComId($comrecibida_id)
    {
		try{
			$c = new Criteria();
			$c->add(ComrecibidaInteresadosPeer::COMRECIBIDA_ID,$comrecibida_id);
			return ComrecibidaInteresadosPeer::doSelect($c);
		} catch (PropelException $ex) {
            return null;
		} catch (\Exception $ex) {
            return null;
        } catch (\Throwable $ex) {
            return null;
        }
    }

    public static function getComObjectByRadicado($radicado,$periodo_id = null)
    {
		if(!trim($radicado) || is_null($radicado)){ return null; }
		//************************************************************************************
    	$c = new Criteria();
        $c->add(ComRecibidaPeer::RADICADO,$radicado);
        if($periodo_id != null){ $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id); }
        $com_object = ComRecibidaPeer::doSelectOne($c);
    	//************************************************************************************
        if($com_object != null){
            return $com_object;
        }else{
            return null;
        }
    }     

    public static function getComObjectByMarcados($periodo_id = null)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //************************************************************************************
    	$c = new Criteria();
        $c->add(ComRecibidaPeer::MARCA,$usuariologuiado);
        //$c->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,1);
        $c->add(ComRecibidaPeer::ESTADODIGITALIZACION_ID,1);
        if($periodo_id != null){ $c->add(ComRecibidaPeer::PERIODO_ID,$periodo_id); }
        $com_objects = ComRecibidaPeer::doSelect($c);
    	//************************************************************************************
        if($com_objects != null){
            return $com_objects;
        }else{
            return null;
        }
    }

    public static function initFolderDigit($regional_id,$periodo,$useTmp = false)
    {
    	$dirRaiz    = ParametroPeer::retrieveByPk(27)->getValortexto();
        $dir_attach  = ParametroPeer::retrieveByPk(14)->getValortexto();
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";        
    	//$extensions = explode(";",ParametroPeer::retrieveByPK(31)->getValortexto());
    	//************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
        //************************************************************************************
        $directorio_digit = simad_util::NormalizePath($dirRaiz.DIRECTORY_SEPARATOR.$entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$periodo);
        $folder_attach = simad_util::createPath($directorio_digit);
        $basic_path = $entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$periodo;
        //************************************************************************************
        $array_url['basic_path'] = $basic_path;
    	$array_url['full_path'] = $folder_attach;
    	$array_url['temp_path'] = $dirTmp;
        $array_url['alias_web'] = "";
    	//************************************************************************************
    	return $array_url;
    }
    
    public static function getDigitFileB64($filepath)
    {
        return simad_util::getConvertFileToB64($filepath);
    }

    public static function getDigitFormatFileExist($filepath,$filename)
    {
        $extensions = explode(";",ParametroPeer::retrieveByPk(31)->getValortexto());
        $digitDocumentFile = "";
        try{
            foreach($extensions as $format){
                $digitDocumentFile = $filepath. DIRECTORY_SEPARATOR .$filename.'.'.$format;
                if(file_exists($digitDocumentFile)){
                    return $digitDocumentFile;
                }
            }
        }catch(Exception $ex){
            return $digitDocumentFile;
        }
    }

    public static function initFolderAttach($regional_id,$usuario_id,$useTmp = false)
    {
    	$dirRaiz    = ParametroPeer::retrieveByPk(27)->getValortexto();
        $dir_attach  = ParametroPeer::retrieveByPk(14)->getValortexto();
        $alias_str  = ParametroPeer::retrieveByPk(28)->getValortexto();
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";    	
    	//************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        $usuario_radica = UsuarioPeer::retrieveByPK($usuario_id);
        $username = ($usuario_radica->getUserName());
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";
        $directorio_attach = simad_util::NormalizePath($dirRaiz.$entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$username);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz.DIRECTORY_SEPARATOR.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp);
        $basic_path = $entidad_text.DIRECTORY_SEPARATOR.$dir_attach.DIRECTORY_SEPARATOR.$username;
        //************************************************************************************
        $folder_attach = simad_util::createPath($directorio_attach);
        $folder_tmp = $useTmp ? simad_util::createPath($directorio_tmp) : "";
        $alias_web = str_replace("\\","/",$basic_path);
        //************************************************************************************
        $array_url['basic_path'] = $basic_path;
    	$array_url['full_path'] = $folder_attach;
    	$array_url['temp_path'] = $folder_tmp;
        $array_url['alias_web'] = $alias_str.$alias_web;
        //************************************************************************************
        return $array_url;
        //return $useTmp ? array('folder_attach'=>$folder_attach,'folder_tmp'=>$folder_tmp) : $folder_attach;
    }
    
    public static function getNumRadicacion($regional_id)
    {
        $periodoActual = date("Y");
        $formaRad = ParametroPeer::retrieveByPk(3)->getCodigo();
        $conexion = Propel::getConnection();
        //************************************************************************************
        if ($formaRad == "REG") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where %s=" . $regional_id . "  and %s=" .
            $periodoActual . " ";
            $consulta = sprintf($consulta, ComRecibidaPeer::NUMERO_RADICACION, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::REGIONAL_ID, ComRecibidaPeer::PERIODO_ID);
        }elseif ($formaRad == "GEN") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComRecibidaPeer::NUMERO_RADICACION, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::PERIODO_ID);
        }elseif($formaRad=="SEQ"){
            $consulta = "SELECT NEXT VALUE FOR [dbo].[GENERATE_CONS_COMRECIBIDA] AS max;";
		}else{
			$consulta = "SELECT MAX(%s) AS max FROM %s where  %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComRecibidaPeer::NUMERO_RADICACION, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::PERIODO_ID);
		}
    	//************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->max + 1);
    }
    
	public static function getComRecibidaByRadicadoOrAsunto($radicado, $asunto = null)
    {
        $object = null;
        //******************************************************************************
        if(!empty($radicado))
        {
            $c = new Criteria();
            $c->add(ComRecibidaPeer::RADICADO, '%' . $radicado  . '%',Criteria::LIKE);

            if(!empty($asunto))
            {
                $c->add(ComRecibidaPeer::ASUNTO, '%' . $asunto . '%', Criteria::LIKE);
            }
            $object = ComRecibidaPeer::doSelect($c);
        }
        //******************************************************************************
        return $object;
    }
	
    public static function getObjectComByRadicadoOrId($radicado = null,$pkcom_id = null)
    {
        $object = null;
        //******************************************************************************
        if(!empty($pkcom_id) && is_numeric($pkcom_id)){
            $object = ComRecibidaPeer::retrieveByPK($pkcom_id);
            if($object != null){ return $object; }
        }
        //******************************************************************************
        if(!empty($radicado)){
            $c = new Criteria();
            $c->add(ComRecibidaPeer::RADICADO,$radicado);
            $object = ComRecibidaPeer::doSelectOne($c);
        }
        //******************************************************************************
        return $object;
    }

    public static function getComByOpcionales($radicado, $nuid = null, $pnombre = null, $papellido = null)
    {
        $object = null;
        $c = new Criteria();
        //************************************************************************************************
        if(!empty($radicado))
        {
            $c->add(ComRecibidaPeer::RADICADO, $radicado);
        }
        //*******************************CAMPOS ADICIONALES OPCIONALES ***********************************
        if(!empty($nuid) || !empty($pnombre) || !empty($papellido))
        {
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComRecibidaInteresadosPeer::COMRECIBIDA_ID);
            $c->addJoin(ComRecibidaInteresadosPeer::INTERESADO_ID, InteresadosPeer::INTERESADO_ID);

            if(!empty($nuid))
            {
                $c->add(InteresadosPeer::NUMERO_IDENTIFICACION, $nuid . '%', Criteria::LIKE);
            }

            if(!empty($pnombre))
            {
                $c->add(InteresadosPeer::PRIMER_NOMBRE, $pnombre . '%', Criteria::LIKE); 
            }

            if(!empty($papellido))
            {
                $c->add(InteresadosPeer::PRIMER_APELLIDO, $papellido . '%', Criteria::LIKE);
            }
        }
        //*******************************CAMPOS ADICIONALES OPCIONALES *************************************
        $object = ComRecibidaPeer::doSelectOne($c);
        return $object;
    }
    
    public static function getObjectComByRadAndPkId($radicado = null,$pkcom_id = null)
    {
        $object = null;
        //******************************************************************************
        if(!empty($pkcom_id) && is_numeric($pkcom_id)){
            $c = new Criteria();
            $c->add(ComRecibidaPeer::RADICADO,$radicado);
            $c->add(ComRecibidaPeer::COMRECIBIDA_ID,$pkcom_id);
            $object = ComRecibidaPeer::doSelectOne($c);
            if($object != null){ return $object; }
        }
        //******************************************************************************
        return $object;
    }

    public static function validateRespuestaBatch($comrecibida_id)
    {
        $conexion = Propel::getConnection();
        //************************************************************************************
        $consulta = "SELECT count(1) AS total FROM %s WHERE  %s = %s;";
        $consulta = sprintf($consulta, ComenviadaMasivasPeer::TABLE_NAME, ComenviadaMasivasPeer::COMRECIBIDA_ID,$comrecibida_id);
    	//************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->total);
    }
    
    public static function getDigitTaskNotImage($optionlist = 1, $status = 1)
    {
        $conexion = Propel::getConnection();
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $isAuthenticated = sfContext::getInstance()->getUser()->isAuthenticated();
		$nulog = false;
        //************************************************************************************
        if(!$isAuthenticated){
            return "(error)";
        }
        //************************************************************************************
		$statuscom = array(12,13,14);
        if($optionlist == 2){
            $consulta = sprintf("SELECT COUNT(DISTINCT %s) AS total FROM %s WITH (NOLOCK)", ComRecibidaPeer::COMRECIBIDA_ID, ComRecibidaPeer::TABLE_NAME);
            $consulta .= sprintf(" JOIN %s ON %s = %s", ComrecibidaUsuarioPeer::TABLE_NAME, ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $consulta .= sprintf(" WHERE %s = %s ",ComRecibidaPeer::ESTADODIGITALIZACION_ID, $status);
            $consulta .= sprintf(" AND %s = 1 AND %s = %s ",ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
			$consulta .= sprintf(" AND %s NOT IN(%s)",ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,implode(",",$statuscom));
        }else{
			$consulta = sprintf("SELECT COUNT(DISTINCT %s) AS total FROM %s WITH (NOLOCK)", ComRecibidaPeer::COMRECIBIDA_ID,ComRecibidaPeer::TABLE_NAME);
			$consulta .= sprintf(" JOIN %s ON %s = %s", ComrecibidaUsuarioPeer::TABLE_NAME, ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
			$consulta .= " WHERE %s = ".$status;
			$consulta .= sprintf(" AND %s NOT IN(%s)",ComRecibidaPeer::ESTADOCOMRECIBIDA_ID,implode(",",$statuscom));
            $consulta = sprintf($consulta, ComRecibidaPeer::ESTADODIGITALIZACION_ID);
        }
        //************************************************************************************
        if($nulog){ simad_util::writetolog(sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR."notify.log",$consulta); }
    	//************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->total);
    }

    public static function updateEstadosComRecibida($comrecibida_id,$estado_final=1)
    {
        try{
            $conexion = Propel::getConnection();
            $query = "UPDATE %s SET  %s = ".$estado_final." WHERE %s = ".$comrecibida_id;
            $query = sprintf($query, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
        } catch (Exception $x) {
            $msgex = $x->getMessage();
        }
    }

    public static function updateEstadosComByEstado($comrecibida_id,$newestado,$urolComIn = array())
    {
        if(count($urolComIn) && is_numeric($comrecibida_id) && is_numeric($newestado)){
            try{
                $conexion = Propel::getConnection();
                $query = "UPDATE %s SET %s = ".$newestado." WHERE %s IN (".implode(",",$urolComIn).") AND %s = ".$comrecibida_id;
                $query = sprintf($query, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
                $sentencia = $conexion->prepare($query);
                $sentencia->execute();
            } catch (\Exception $x) {
                $msgex = $x->getMessage();
            }
        }
    }

    public static function updateFechaAsignaCom($comrecibida_id,$urolComIn = array(), $isAsignado = 1)
    {
        if(count($urolComIn) && is_numeric($comrecibida_id)){
            try{
                $conexion = Propel::getConnection();
                $query = "UPDATE %s SET %s = '".date("Y-m-d G:i:s")."' WHERE %s IN (".implode(",",$urolComIn).") AND %s = ".$comrecibida_id." AND %s = ".$isAsignado;
                $query = sprintf($query, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::FECHA_ASIGNA,ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::ESTA_ASIGNADA);
                $sentencia = $conexion->prepare($query);
                $sentencia->execute();
            } catch (PropelException $x) {
				$msgex = $x->getMessage();
			} catch (\Exception $x) {
				$msgex = $x->getMessage();
			}
        }
    }

    public static function updateAsignadoCom($comrecibida_id,$rolus_id=1,$isAsignado=1)
    {
        try{
            $conexion = Propel::getConnection();
            $query = "UPDATE %s SET  %s = ".$isAsignado." WHERE %s = ".$comrecibida_id;
            $query = sprintf($query, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::ESTA_ASIGNADA,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
        } catch (PropelException $x) {
            $msgex = $x->getMessage();
        } catch (\Exception $x) {
            $msgex = $x->getMessage();
        }
    }
    
    public static function updateAsignadoComAuto($comrecibida_id, $estadodestinocom_id, $tiporpoceso_com, $rolus_id, $isAsignado = 0)
    {
        try{
            if(empty($comrecibida_id) && empty($rolus_id) && empty($tiporpoceso_com) && empty($estadodestinocom_id)){
                return false;
            }
            //*********************************************************************************************************
            $conexion = Propel::getConnection();
            $query = "UPDATE %s SET %s = ".$isAsignado.", %s = ".$estadodestinocom_id." WHERE %s = ".$comrecibida_id." AND %s = ".$rolus_id." AND %s = ".$tiporpoceso_com;
            $query = sprintf($query, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::ESTA_ASIGNADA,ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID,
                                ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,ComrecibidaUsuarioPeer::TIPOPROCESOCOM_ID);
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
            //*********************************************************************************************************
            return true;
        } catch (PropelException $x) {
            $msgex = $x->getMessage();
            return false;
        } catch (\Exception $x) {
            $msgex = $x->getMessage();
            return false;
        } catch (\Throwable $x) {
            $msgex = $x->getMessage();
            return false;
        }
    }

    public static function getAbrevDependencia($userdestino_id)
    {
		if(!trim($userdestino_id)) { return ""; }
		//*******************************************************************
		$user_destino = UsuarioPeer::retrieveByPk($userdestino_id);
		$idDep = $user_destino->getDependenciaId();
		$codDep = DependenciaPeer::retrieveByPk($idDep);
		return $codDep->getCodigo();        
	}
    
    public static function getFechaMaxRespByTipoCom($tipocomrecibida_id, $fecha_vencimiento = null)
    {
		if(empty($fecha_vencimiento)){
		  $dfecha = TipoComRecibidaPeer::getVencimientoByTipoCom($tipocomrecibida_id);
        }else{
          try{
            $dt = new DateTime($fecha_vencimiento);
            $dfecha = $dt->format("Y-m-d");
          } catch (Exception $x) {
            $dfecha = date("Y-m-d");
          }
        }
        return $dfecha;
	}
    
    public static function getFechaMaxRespByCustomFechaEntrada($tipocomrecibida_id, $fecha_inicial = null)
    {
        return TipoComRecibidaPeer::getVencimientoByTipoCom($tipocomrecibida_id,$fecha_inicial);
	}

    public static function addUserByCom($comrecibida_id,$usuarios_data,$estadocomrecibida_id=1,$tipoprocesocom_id=1)
	{
		try{
			$comAsignarRad = new ComrecibidaUsuario();
			$comAsignarRad->setRolusuariorecibidaid(1);
			$comAsignarRad->setEstadocomrecibidaId($estadocomrecibida_id);
			$comAsignarRad->setEstaAsignada(0);
			$comAsignarRad->setUsuarioId($usuarios_data['usuario_origen']);
			$comAsignarRad->setComrecibidaId($comrecibida_id);
			$comAsignarRad->setCargousuarioId($usuarios_data['cargo_uorigen']);
			$comAsignarRad->setTipoprocesocomId(1);
			$comAsignarRad->save();
			//**************************************************************************************
			$comAsignar = new ComrecibidaUsuario();
			$comAsignar->setRolusuariorecibidaid(2);
			$comAsignar->setEstadocomrecibidaId($estadocomrecibida_id);
			$comAsignar->setEstaAsignada(1);
			$comAsignar->setUsuarioId($usuarios_data['usuario_destino']);
			$comAsignar->setComrecibidaId($comrecibida_id);
			$comAsignar->setCargousuarioId($usuarios_data['cargo_udestino']);
			$comAsignarRad->setTipoprocesocomId($tipoprocesocom_id);
			$comAsignar->save();
			//**************************************************************************************
			if($comAsignarRad->getPrimaryKey() && $comAsignar->getPrimaryKey()){
				return true;
			}else{
				return false;
			}
		}catch (PropelException $th){
            return false;
        }catch (Exception $th){
            return false;
        }
    }
    
    public static function addUserRolByCom($comrecibida_id,$usuario_id,$cusuario_id,$estadocomrecibida_id=1,$rolus_id=1,$isAsig=1,$tprocomId=0)
	{
        try{
            $comAsignar = new ComrecibidaUsuario();
            $comAsignar->setRolusuariorecibidaid($rolus_id);
            $comAsignar->setEstadocomrecibidaId($estadocomrecibida_id);
            $comAsignar->setEstaAsignada($isAsig);
            $comAsignar->setUsuarioId($usuario_id);
            $comAsignar->setComrecibidaId($comrecibida_id);
            $comAsignar->setCargousuarioId($cusuario_id);
            $comAsignar->setTipoprocesocomId($tprocomId);
            $comAsignar->setFechaAsigna(date("Y-m-d G:i:s"));
            $comAsignar->save();
            //**************************************************************************************
            if($comAsignar->getPrimaryKey()){
                return true;
            }else{
                return false;
            }
        }catch (PropelException $th){
            return false;
        }catch (Exception $th){
            return false;
        }
    }

    public static function addNextUserProcessBatch($comrecibida_id,$usuario_id,$cusuario_id,$estadocomrecibida_id=1,$rolus_id=1,$isAsig=1,$tprocomId=0)
	{
        try{
            $comAsignar = new ComrecibidaUsuario();
            $comAsignar->setRolusuariorecibidaid($rolus_id);
            $comAsignar->setEstadocomrecibidaId($estadocomrecibida_id);
            $comAsignar->setEstaAsignada($isAsig);
            $comAsignar->setUsuarioId($usuario_id);
            $comAsignar->setComrecibidaId($comrecibida_id);
            $comAsignar->setCargousuarioId($cusuario_id);
            $comAsignar->setTipoprocesocomId($tprocomId);
            $comAsignar->setFechaAsigna(date("Y-m-d G:i:s"));
            $comAsignar->save();
            //**************************************************************************************
            if($comAsignar->getPrimaryKey()){
                return true;
            }else{
                return false;
            }
        }catch (PropelException $th){
            return false;
        }catch (Exception $th){
            return false;
        }
    }

    public static function addDestinoCom(ComRecibida $com_recibida, $tipoproceso_dest = 0, $usuario_destino, $cusuario_destino, $regional_destino_id = null)
    {
        $isAddUserDest = false;
        //**************************************************************************************************
        try {
            if(!empty($usuario_destino)){
                $user_destino = preg_split("/[,]+/", $usuario_destino);
                $causer_destino = preg_split("/[,]+/", $cusuario_destino);
                //*******************************************************************************************
                for($index=0; $index < count($user_destino); $index++) {
                    $isAddUserDest = ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$user_destino[$index],$causer_destino[$index],11,2,1,$tipoproceso_dest);
                }
            }else{
                $tipo_com_recibida = TipoComRecibidaPeer::retrieveByPK($com_recibida->getTipocomrecibidaId());
                $cusers_dest_list = ComRecibidaPeer::getConfigUserDestProcessCom($com_recibida,$tipo_com_recibida,$tipoproceso_dest, $regional_destino_id);
                if(!empty($cusers_dest_list)){
                    foreach ($cusers_dest_list as $cuser_dest) {
                        $isAddUserDest = ComRecibidaPeer::addUserRolByCom($com_recibida->getPrimaryKey(),$cuser_dest->getUsuarioId(),$cuser_dest->getPrimaryKey(),11,2,1,$tipoproceso_dest);
                        if($isAddUserDest == false){
                            return false;
                        }
                    }
                }else{
                    $isAddUserDest = false;
                }
            }
		} catch (PropelException $th) {
            $isAddUserDest = false;
        } catch (Exception $th) {
            $isAddUserDest = false;
        }
        //*******************************************************************************************
        return $isAddUserDest;
    }

	public static function getConfigUserDestProcessCom(ComRecibida $com_recibida, TipoComRecibida $tipo_com, $tipoprocesocom_id = 0, $regional_destino_id)
    {
        $cargo_usuario = null;
        //******************************************************************************************
        try {
            $usuarios_mail = array();
            //**************************************************************************************
            if($tipo_com->getTipodistribucionId()){
                $cargo_usuario = ComRecibidaPeer::getConfigReceptorComByDep($com_recibida->getDependenciaId(),$tipo_com->getPrimaryKey(),$tipoprocesocom_id, $regional_destino_id);
            }
            //**************************************************************************************
            /*if($isValid){
                $encabezado_email = "Este es un mensaje para informarle que se le asign&oacute; la siguiente comunicaci&oacute;n para que gestione la respuesta: ";
                ComRecibidaPeer::envioEmail($com_recibida->getPrimaryKey(),$usuarios_mail,$encabezado_email);
            }*/
        } catch (PropelException $th) {
            $cargo_usuario = null;
        } catch (Exception $th) {
            $cargo_usuario = null;
        }
        //******************************************************************************************       
        return $cargo_usuario;
    }
	
	public static function deleteCascada($comrecibida_id)
	{
		try{
            if(empty($comrecibida_id) || !is_numeric($comrecibida_id)){ return false; }
            $conexion = Propel::getConnection();
            //****************************************************************************************************
			$query01 = "DELETE FROM %s WHERE %s = ".$comrecibida_id;
			$sql01      = sprintf($query01, ComrecibidaUsuarioPeer::TABLE_NAME, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
			$sentencia = $conexion->prepare($sql01);
			$sentencia->execute();
            //****************************************************************************************************
			$query03 = "DELETE FROM %s WHERE %s = ".$comrecibida_id;
			$sql03      = sprintf($query03, ComrecibidaInteresadosPeer::TABLE_NAME, ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
			$sentencia = $conexion->prepare($sql03);
			$sentencia->execute();
			//****************************************************************************************************
			$query02 = "DELETE FROM %s WHERE %s = ".$comrecibida_id;
			$sql02      = sprintf($query02, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::COMRECIBIDA_ID);
			$sentencia = $conexion->prepare($sql02);
			$sentencia->execute();
		} catch (PropelException $th) {
            return false;
        } catch (\Exception $th) {
            return false;
        }
	}
  
    public static function getConfigReceptorComByDep($dependencia_id, $tipocomrecibida_id, $tipoprocesocom_id = 0, $regional_destino_id = null)
    {
        $cargo_usuario = null;
		$nulog = false;
		$modulo_id = 3;
		$mquery = "";
		$esta_asignada = 1;
        //******************************************************************************************
        try {
            $query_count = "(SELECT COUNT(*) FROM ".ComrecibidaUsuarioPeer::TABLE_NAME." WITH (NOLOCK) ";
            $query_count .= " INNER JOIN ".ComRecibidaPeer::TABLE_NAME." ON ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID." = ".ComRecibidaPeer::COMRECIBIDA_ID." WHERE ".ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID." = 2";
            //$query_count .= " AND ".ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID." IN (1,2,6) AND ".ReceptorComunicacionesPeer::USUARIO_ID." = ".ComrecibidaUsuarioPeer::USUARIO_ID;
			$query_count .= " AND ".ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID." = 1 AND ".ReceptorComunicacionesPeer::USUARIO_ID." = ".ComrecibidaUsuarioPeer::USUARIO_ID;
			$query_count .= " AND ".ComrecibidaUsuarioPeer::ESTA_ASIGNADA." = ".$esta_asignada;
			$query_count .= " AND ".ComRecibidaPeer::TIPOPROCESOCOM_ID." = 2";
			$query_count .= " AND ".ComRecibidaPeer::TIPOCOMRECIBIDA_ID." = ".$tipocomrecibida_id;
            $query_count .= " AND ".ComRecibidaPeer::PERIODO_ID." = ".date("Y")." AND ".ComRecibidaPeer::TIPOCOMRECIBIDA_ID." = ".ReceptorComunicacionesPeer::TIPOCOMRECIBIDA_ID.") AS TOTAL";
            //**********************************************************************************
			//$mquery = "SELECT DISTINCT TOP 1 ".ReceptorComunicacionesPeer::USUARIO_ID.",".$query_count." FROM ".ReceptorComunicacionesPeer::TABLE_NAME." WITH (NOLOCK) ";
			$mquery = "SELECT TOP 1 ".ReceptorComunicacionesPeer::USUARIO_ID.",".$query_count." FROM ".ReceptorComunicacionesPeer::TABLE_NAME." WITH (NOLOCK) ";
            //**********************************************************************************
            //regional - destino
            if(!empty($regional_destino_id) && is_numeric($regional_destino_id) && $regional_destino_id > 0 && null !== $regional_destino_id){
                $mquery .= " INNER JOIN ".UsuarioPeer::TABLE_NAME." ON ".ReceptorComunicacionesPeer::USUARIO_ID." = ".UsuarioPeer::USUARIO_ID;
            }
            //**********************************************************************************
            $mquery .= " INNER JOIN ".UsuarioProcesocomPeer::TABLE_NAME." ON ".ReceptorComunicacionesPeer::USUARIO_ID." = ".UsuarioProcesocomPeer::USUARIO_ID;
            $mquery .= " WHERE ".ReceptorComunicacionesPeer::TIPOCOMRECIBIDA_ID." = ".$tipocomrecibida_id;
            $mquery .= " AND ".ReceptorComunicacionesPeer::DEPENDENCIA_ID." = ".$dependencia_id;
			$mquery .= " AND ".ReceptorComunicacionesPeer::MODULO_ID." = ".$modulo_id;
            //**********************************************************************************
            //regional - destino
            if(!empty($regional_destino_id) && is_numeric($regional_destino_id) && $regional_destino_id > 0 && null !== $regional_destino_id){
                $mquery .= " AND ".UsuarioPeer::REGIONAL_ID." = ".$regional_destino_id;
            }
            //**********************************************************************************
            $mquery .= " AND ".UsuarioProcesocomPeer::TIPOPROCESOCOM_ID." = ".$tipoprocesocom_id." ORDER BY TOTAL;";
            //**********************************************************************************
			if($nulog == true){
				$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'receptorComByDep.log';
				simad_util::writetolog($logname,$mquery);
			}
			//**********************************************************************************
            $conexion = Propel::getConnection();
            $sentencia = $conexion->prepare($mquery);
            $sentencia->execute();
            $resultset = $sentencia->fetchAll(PDO::FETCH_BOTH);
            //**********************************************************************************
            $ilcurrent_user = array();
            //**********************************************************************************
            foreach($resultset as $object){
                $usuario_id = $object['USUARIO_ID'];
                if(!in_array($usuario_id,$ilcurrent_user)){
                    $cargo_usuario[] = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_id,true);
                    $ilcurrent_user[] = $usuario_id;
                    //$usuarios_mail = $usuario_id;
                }
            }
		} catch (PropelException $th) {
            $cargo_usuario = null;
			if($nulog == true){
				$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'receptorComByDep.log';
				simad_util::writetolog($logname,"Error Propel Comrecibida => ".$th->getMessage(). " query => ".$mquery);
			}
        } catch (\Exception $th) {
            $cargo_usuario = null;
			if($nulog == true){
				$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'receptorComByDep.log';
				simad_util::writetolog($logname,"Error General Comrecibida => ".$th->getMessage(). " query => ".$mquery);
			}
        }
        //******************************************************************************************       
        return $cargo_usuario;
    }

    public static function initWorkflowCom(ComRecibida $com_recibida,$usuario_destino,$usuario_origen,$buzon=1)
    {
        try{
            $wfinstancia_id = ComRecibidaPeer::createWF($com_recibida->getPrimaryKey(),$usuario_destino,$com_recibida->getTipocomrecibidaId());
            if($wfinstancia_id){
                $wfinstanciabitacora_id = ComRecibidaPeer::createInstanciaBitacora($com_recibida->getPrimaryKey(),$usuario_origen,$wfinstancia_id,$buzon);
                $myinwf = new wf_Interface(); 
                $com_recibida->setInstancia($wfinstancia_id);
                $com_recibida->setBitacora($wfinstanciabitacora_id);
                $com_recibida->save();
                //**********************************************************************************
                $myinwf->envioEmail($com_recibida->getPrimaryKey(),$usuario_destino,$buzon);
                //**********************************************************************************
                return true;
            }else{
                return false;
            }
        } catch (PropelException $th) {
            return false;
        } catch (\Exception $th) {
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
    
    public static function createWF($comrecibida_id,$usuario_destino,$tipo_com)
    {
        try{
            $c = new Criteria();  	
            $c->add(WfFlujoPeer::TIPOCOMRECIBIDA_ID,$tipo_com);
            $flujo = WfFlujoPeer::doSelectOne($c);
            //*************************************************************************
            if($flujo){
                //busca la primera transicion
                $c = new Criteria();
                $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$flujo->getPrimaryKey());
                $c->add(WfActividadTransicionPeer::ES_DESTINO,1);
                $c->add(WfActividadTransicionPeer::ORDEN,1);	
                $wf_actividad_transicion = WfActividadTransicionPeer::doSelectOne( $c );
                if(!$wf_actividad_transicion){ return 0; }
                //*********************************************************************
                $wf_instancia = new WfInstancia();
                $wf_instancia->setUsuarioId($usuario_destino);
                $wf_instancia->setWfactividadtransicionId($wf_actividad_transicion->getPrimaryKey());
                $wf_instancia->setComrecibidaId($comrecibida_id);
                $wf_instancia->setWfFlujoId($flujo->getPrimaryKey());
                $wf_instancia->setEstaAbierta(1);
                $wf_instancia->setFechaI(date("Y-m-d G:i:s"));
                $wf_instancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
                $wf_instancia->setObservaciones("WorkFlow radicado en ".date("Y-m-d G:i:s"));
                $wf_instancia->save();
                //*********************************************************************
                return $wf_instancia->getPrimaryKey();
            }else{
                return 0;
            }
        } catch (PropelException $th) {
            return 0;
        } catch (\Exception $th) {
            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }
    
    public static function createInstanciaBitacora($comrecibida_id,$usuario_origen,$wfinstancia_id,$buzon)
    {
        try{
            $wf_instancia = WfInstanciaPeer::retrieveByPk($wfinstancia_id);
            $wfactividad_id = $wf_instancia->getWfactividadtransicionId();
            $estadoActividad = "1";
            //****************************************************************************
            $wf_instancia_bitacora = new WfInstanciaBitacora();        
            $wf_instancia_bitacora->setUsuarioId($usuario_origen);
            $wf_instancia_bitacora->setWfactividadtransicionId($wfactividad_id);
            $wf_instancia_bitacora->setWfinstanciaId($wfinstancia_id);
            $wf_instancia_bitacora->setComrecibidaId($comrecibida_id);
            $wf_instancia_bitacora->setFechaI(date("Y-m-d G:i:s"));
            $wf_instancia_bitacora->setFechaF(date("Y-m-d G:i:s"));
            $wf_instancia_bitacora->setObservaciones("Radicado en ".date("Y-m-d G:i:s"));
            $wf_instancia_bitacora->setError("0");
            $wf_instancia_bitacora->setEsActual(0);
            $wf_instancia_bitacora->setFechaLimite(date("Y-m-d G:i:s"));
            $wf_instancia_bitacora->setBuzon($buzon);
            $wf_instancia_bitacora->setBuzonId($buzon);
            $wf_instancia_bitacora->setEstadoActividad($estadoActividad);
            $wf_instancia_bitacora->save();
            //****************************************************************************
            return $wf_instancia_bitacora->getPrimaryKey();
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
    
    public static function getListComRecibidasByNuid($numero_identificacion = null,$notradicado = null, $radicado_com = null)
    {
        $objects = null;
        //******************************************************************************
        try {
            $c = new Criteria();
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
            $c->addJoin(ComrecibidaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
            //**************************************************************************
            $c1 = $c->getNewCriterion(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID, 14, Criteria::NOT_EQUAL);//estado por leer - pero enviado	
            $c2 = $c->getNewCriterion(ComRecibidaPeer::ESTADOCOMRECIBIDA_ID, 13, Criteria::NOT_EQUAL);//estado por leido - pero enviado
            $c1->addOr($c2);
            $c->add($c1);
            //**************************************************************************
            $c->add(InteresadosPeer::NUMERO_IDENTIFICACION,$numero_identificacion);
            //**************************************************************************
            if(!empty($notradicado)){ $c->add(ComRecibidaPeer::RADICADO,$notradicado, Criteria::NOT_EQUAL); }
            //**************************************************************************
            if(!empty($radicado_com)){ $c->add(ComRecibidaPeer::RADICADO,$radicado_com); }
            //**************************************************************************
            $objects = ComRecibidaPeer::doSelect($c);            
        } catch (PropelException $th) {
            $objects = null;
        } catch (\Exception $th) {
            $objects = null;
        } catch (\Throwable $th) {
            $objects = null;
        }
        //******************************************************************************
        return $objects;
    }

    public static function envioEmail($comrecibida_id, $user, $encabezado_cuerpo="", $is_footer=false, $is_note=false)
    {         
        if(trim($encabezado_cuerpo) == ""){
          $encabezado_cuerpo = "Este es un mensaje para informarle que se le ha radicado una Comunicaci&oacute;n Externa Recibida:";
        }
        //***********************************************************************************************
        $com_recibida_mail = ComRecibidaPeer::retrieveByPK($comrecibida_id);
        $usuario = UsuarioPeer::retrieveByPK($user);
        if($usuario == null){
            return false;
        }
        //***********************************************************************************************
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">
        <br>'.$encabezado_cuerpo.'     
        <br>
        <br>
        <b>Fecha Radicaci&oacute;n:</b> '.$com_recibida_mail->getFechaCreacion().' <br>
        <b>Numero Radicado:</b> '.$com_recibida_mail->getRadicado().'<br>
        <b>Remitente:</b> '.($com_recibida_mail->getDirectorioExterno()).'<br>
        <b>Asunto:</b> '.mb_convert_encoding($com_recibida_mail->getAsuntoRecibida(),'UTF-8').'<br>
        <b>Detalle Asunto:</b> '.mb_convert_encoding($com_recibida_mail->getAsunto(),'UTF-8').'<br>
        <b>Tipo Comunicaci&oacute;n:</b> '.mb_convert_encoding($com_recibida_mail->getTipoComRecibida(),'UTF-8').'<br>
        <b>Fecha Vencimiento:</b> '.$com_recibida_mail->getFechaMaximaRespuesta().' <br>
        <b>Observaciones:</b> '.mb_convert_encoding($com_recibida_mail->getObservaciones(),'UTF-8').'<br>';
        //***********************************************************************************************
        if($is_footer){
           $nota_alpie = '<p class="MsoNormal" style="text-autospace:none">
           <b><i><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;color:#FF0000">&nbsp;
           *Debe estar autenticado en SIMAD ENTERPRISE para poder visualizar la comunicaci&oacute;n!<u></u><u></u></span></i></b></p>';
           $link_com = '<a target="_blank" href="'.ComRecibidaPeer::getProtocolBase().'/recibida.php/com_recibida/show?comrecibida_id='.$com_recibida_mail->getPrimaryKey().'"/>Visualizar la comunicacion!(*)</a>';
           $link_com .= $nota_alpie;
           $cuerpo .= $link_com;
        }
        //***********************************************************************************************
        if($is_note){
           $nota = '<p class="MsoNormal" style="text-autospace:none">
           <b><i><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;color:#FF0000">&nbsp;
           Por favor ingresa a SIMAD ENTERPRISE para consultar esta comunicaci&oacute;n!<u></u><u></u></span></i></b></p>';
           $cuerpo .= $nota;
        }
        //***********************************************************************************************
        $cuerpo .= '<br></div></body></html>';
        $cabeceras = "Content-type: text/html; charset=UTF-8\r\n";
        //***********************************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('SGDEA .::. Comunicaciones Externas Recibidas');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetEnableService($usuario->getActivarAlertas());
        $baseMail->SetAddAddress($usuario->getEmail(), $usuario->getEmail());    
        if($baseMail->InitSend() === true){
           $baseMail->writetolog("Alerta enviada: " . $com_recibida_mail->getRadicado() . " Enviado a: " . $usuario->getEmail());
        }else{
           $baseMail->writetolog("Error al enviar alerta: " . $com_recibida_mail->getRadicado() . " Cuenta correo: " . $usuario->getEmail());
        }
    }
    
    public static function getProtocolBase()
    {
        $http = 'http';
        if(!empty($_SERVER["HTTPS"])){
        	$http .= "s";
        }
        //***********************************************************************************************
        if ($_SERVER["SERVER_PORT"]){
          $server = $http . "://".$_SERVER["HTTP_HOST"] .':'. $_SERVER["SERVER_PORT"];
        }else{
          $server = $http . "://".$_SERVER["HTTP_HOST"];
        }
        //***********************************************************************************************
        return $server;
    }
	
	public static function validateExistJoin($joins = array(), Join $addJoin)
    {
        $isExists = false;
        $typeJoinOrg = $addJoin->getJoinType();
        //***********************************************************************************************
        foreach ($joins as $key => $value)
        {
            if($value instanceof Join) {
                $join = new Join();
                $join = $value;
                
                $cjoint1 = $addJoin->setJoinType(Criteria::LEFT_JOIN);
                $cjoint2 = $addJoin->setJoinType(Criteria::RIGHT_JOIN);
                $cjoint3 = $addJoin->setJoinType(Criteria::INNER_JOIN);

                $isExists = $join->equals($addJoin) || $join->equals($cjoint1) || $join->equals($cjoint2) || $join->equals($cjoint3);
                if($isExists){
                    $addJoin = $addJoin->setJoinType($typeJoinOrg);
                    return $isExists; 
                }
            } else {
                return false;
            }      
        }
        //***********************************************************************************************
        $addJoin = $addJoin->setJoinType($typeJoinOrg);
        return $isExists;
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
            $msg_error = "Debe seleccionar el archivo que contiene las registros para radicar";
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = $msg_error;
        }
        //*************************************************************************************************************
        return $array_data;
    }
}
