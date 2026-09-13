<?php

/**
 * Subclass for performing query and update operations on the 'SERVICIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ServicioPeer extends BaseServicioPeer
{
	public static function insertBitacoraServicio($servicio_id, $estadoservicio_id, $usuario_asignado, $usuario_envia, $observaciones, $fecha_ejecucion=null)
    {
        try {
            $bitacora_servicio = new ServicioBitacora();
            $bitacora_servicio->setServicioId($servicio_id);
            $bitacora_servicio->setServicioestadoId($estadoservicio_id);
            $bitacora_servicio->setFechaAsignacion(date("Y-m-d G:i:s"));        
            $bitacora_servicio->setFechaEjecucion($fecha_ejecucion);
            $bitacora_servicio->setObservaciones($observaciones);
            $bitacora_servicio->setUsuarioasignadoId($usuario_asignado);
            $bitacora_servicio->setUsuarioenviaId($usuario_envia);
            $bitacora_servicio->setFechaCreacion(date("Y-m-d G:i:s"));
            return $bitacora_servicio->save();
        } catch (PropelException $th) {
            //throw $th;
            return null;
        } catch (\Exception $th) {
            //throw $th;
            return null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function saveAnexosExpedientes($servicio_id, $usuariologuiado, $descripcion, $tiposervicio_id)
    {
        $full_array = simad_paths_app::readConfigFileApp();
        $unidad_documental = null;
        $contenido_doc = null;
        $tipodocumental_id = null;
        //*********************************************************************************************
        $servicio = ServicioPeer::retrieveByPk($servicio_id);
        //*********************************************************************************************
        try
        {
            if(!empty($servicio->getConsecutivocomId()) && $servicio->getModuloId() == ModulesEnable::ComEnviada)
            {
                $com_enviada = ComEnviadaPeer::retrieveByPK($servicio->getConsecutivocomId());
                if($com_enviada != null){
                    if(!empty($com_enviada->getExpedienteId())){
                        $unidad_documental = UnidadDocumentalPeer::retrieveByPK($com_enviada->getExpedienteId());
                        $contenido_doc = ContenidoUnidadDocumentalPeer::retrieveByPK($com_enviada->getContenidodocId());
                        if(!empty($com_enviada->getTipoDocumentalCod())){
                            $tipodocumental_id = $com_enviada->getTipoDocumentalCod();
                        }elseif($contenido_doc != null){
                            $tipodocumental_id = $contenido_doc->getTipodocumentalId();
                        }
                    }
                }
            }
            //*****************************************************************************************
            $list_expedientetiposerv = UnidaddocumentalTiposervicioPeer::getExpedientesByTipoServicio($tiposervicio_id);
            //*****************************************************************************************
            if($unidad_documental != null && !empty($tipodocumental_id))
            {
                if(empty($unidad_documental->getEstaCerrado()))
                {
                    self::saveAnexoExpedienteByOne($servicio_id, $unidad_documental->getPrimaryKey(), $tipodocumental_id, $usuariologuiado, $descripcion, $full_array);
                    $observacion_str = "El documento se asoció al expediente " . $unidad_documental->getCodigoTitulo();
                    ServicioPeer::insertBitacoraServicio($servicio_id, $servicio->getServicioestadoId(), $usuariologuiado, $usuariologuiado, $observacion_str);
                }
                else
                {
                    $observacion_str = "No se anexo el documento al expediente " . $unidad_documental->getCodigoTitulo() ." porque este se encuentra cerrado.";
                    ServicioPeer::insertBitacoraServicio($servicio_id, $servicio->getServicioestadoId(), $usuariologuiado, $usuariologuiado, $observacion_str);
                }
            }
            //*****************************************************************************************
            foreach($list_expedientetiposerv as $object)
            {
                if(!empty($object->getUnidaddocumentalId())){

                    if(empty($object->getUnidadDocumental()->getEstaCerrado()))
                    {
                        self::saveAnexoExpedienteByOne($servicio_id, $object->getUnidaddocumentalId(), $object->getTipodocumentalId(), $usuariologuiado, $descripcion,$full_array);

                        $observacion_str = "El documento se asoció al expediente " . $object->getUnidadDocumental()->getCodigoTitulo();
                        ServicioPeer::insertBitacoraServicio($servicio_id, $servicio->getServicioestadoId(), $usuariologuiado, $usuariologuiado, $observacion_str);
                    }
                    else
                    {
                        $observacion_str = "No se anexo el documento al expediente " . $object->getUnidadDocumental()->getCodigoTitulo() ." porque este se encuentra cerrado.";
                        ServicioPeer::insertBitacoraServicio($servicio_id, $servicio->getServicioestadoId(), $usuariologuiado, $usuariologuiado, $observacion_str);
                    }
                }
            }			
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function saveAnexoExpedienteByOne($servicio_id, $expediente_id, $tipodoc_id, $usuariologuiado, $descripcion, $full_array)
    {
        try
        {
			$servicio_anexo = ServicioAnexosPeer::getServicioAnexoByServicioId($servicio_id);
            //**********************************************************************************************
			$ruta_doc = $servicio_anexo->getRuta();
            //**********************************************************************************************
			// traemos los arrays de app.ini
			$source_alias = $full_array['source_alias'];
            //**********************************************************************************************
			if (trim($ruta_doc) != "") 
			{
                $p3url = str_replace($source_alias, '/', preg_replace('~//+~', '/', $ruta_doc)); 
                $ifile_data = simad_util::getBasicRouteAttachMod($full_array,$p3url);
                //******************************************************************************************
                $params = [];
                $params['unidad_documental_pk'] = $expediente_id;
                $params['verificacion_doc'] = 2;
                $params['tipo_doc_id'] = $tipodoc_id;
                $params['estado_doc'] = 1; 
                $params['usuario_id'] = $usuariologuiado;
                $params['descripcion'] = $descripcion;
                $params['archivo_nombre'] = $ifile_data['fname'];
                $params['folios'] = $servicio_anexo->getFolios();
                $params['fecha_documento'] = $servicio_anexo->getFechaCreacion();
                $params['soporte_documental'] = 6;
				$params['origen_documento'] = 1;
				$params['modulo_id'] = ModulesEnable::Servicios;
				$params['consecutivo_id'] = $servicio_id;
                $params['format_file'] = $ifile_data['fformat'];
				$params['size_file'] = $ifile_data['fsize'];
				$params['path_absolute'] = $ifile_data['path_absolute'];
				$params['path_relative'] = $ifile_data['path_relative'];
                $params['es_copia'] = 1;
                //******************************************************************************************
				$contenido_doc = ContenidoUnidadDocumentalPeer::addContenidoUnidadDocumental($params);

                if(!empty($contenido_doc) && $contenido_doc instanceof ContenidoUnidadDocumental)
                {
                    $contenido_doc->closeExpedienteByTipoDoc();
                }
			}
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

	public static function insertAnexoServicios($servicio_id, $file_str, $descripcion, $folios, $usuario_id, $es_actual = 1)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_id = ($usuario_id ? $usuario_id : $usuariologuiado);
        //**********************************************************************************************
		$servicio_anexos = new ServicioAnexos();
        $servicio_anexos->setServicioId($servicio_id);
        $servicio_anexos->setUsuarioId($usuario_id);
        $servicio_anexos->setDescripcion($descripcion);        
        $servicio_anexos->setRuta($file_str);
		$servicio_anexos->setFolios($folios);
		$servicio_anexos->setFechaCreacion(date("Y-m-d G:i:s"));
		$servicio_anexos->setEsActual($es_actual);        
        return $servicio_anexos->save();        
    }
	
    public static function getBitacoraServicios($servicio_id){
        $c = new Criteria();
        $c->add(ServicioBitacoraPeer::SERVICIO_ID,$servicio_id);
        $c->addAscendingOrderByColumn(ServicioBitacoraPeer::FECHA_CREACION);
        return ServicioBitacoraPeer::doSelect($c);        
    }
	
    public static function getListIntersadosByComId($servicio_id){
    	$c = new Criteria();
        $c->add(ServicioInteresadosPeer::SERVICIO_ID,$servicio_id);
        return ServicioInteresadosPeer::doSelect($c);
    }

	public static function getAnexoServicios($servicio_id){
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        //$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
		$viewAll = sfContext::getInstance()->getUser()->checkPerm('SERVICIOS_VER_TODAS_IMAGENES', $usuariologuiado);
		//**********************************************************************************************
		$c = new Criteria();
        $c->add(ServicioAnexosPeer::SERVICIO_ID,$servicio_id);
		if(!$viewAll){ $c->add(ServicioAnexosPeer::ES_ACTUAL,1); }
        $c->addAscendingOrderByColumn(ServicioAnexosPeer::FECHA_CREACION);
        return ServicioAnexosPeer::doSelect($c);        
    }

    public static function getCurrentUserAsignado($servicio_id)
	{
		//$c = new Criteria();
		//$c->add(AsignarServicioPeer::SERVICIO_ID,trim($servicio_id));                
		//$resp = AsignarServicioPeer::doSelectOne($c);
		//*****************************************************************************************
		//$asignarservicioId = $resp != null ? $resp->getAsignarservicioId() : 0;
		//*****************************************************************************************
		$a =  new Criteria();
        $a->addJoin(UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID,AsignarServicioPeer::ASIGNARSERVICIO_ID,Criteria::INNER_JOIN);
		//$a->add(UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID,$asignarservicioId);
		$a->add(UsuarioAsignadoSolicitudPeer::ROLUSUARIOASIGNACIONSERVICIO_ID,2);
		$a->add(UsuarioAsignadoSolicitudPeer::ESTA_ASIGNADO,1);
        $a->add(AsignarServicioPeer::SERVICIO_ID,trim($servicio_id));    
		return UsuarioAsignadoSolicitudPeer::doSelectOne($a);
	}

    /**
     * servicioActions::getNumRadicacion()
     * metodo para obtener el maximo numero de radicacion por regional
     * @param mixed $regional
     * @return
     */       
    public static function getNumRadicacion($regional)
    {
        $periodoActual = date("Y");
        $parametro = ParametroPeer::retrieveByPk(7);
        $formaRad = $parametro->getCodigo();
        $conexion = Propel::getConnection();
        //************************************************************************************
        if ($formaRad == "REG") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where %s=" . $regional . "  and %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ServicioPeer::NUMERO_RADICACION, ServicioPeer::TABLE_NAME, ServicioPeer::REGIONAL_ID, ServicioPeer::PERIODO_ID);
        } elseif ($formaRad == "GEN") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ServicioPeer::NUMERO_RADICACION, ServicioPeer::TABLE_NAME, ServicioPeer::PERIODO_ID);
        }
        //************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->max + 1);
    }

    /**
     * servicioActions::executeRadicar()
     *Funcion que retorna el radicado con el formato definido
     * @param mixed $regional
     * @return
     */  
    public static function executeRadicar($regional, $numero_radicado)
    {
        return sprintf("%02d-%05d", $regional,$numero_radicado);
    }

    /**
     * servicioActions::asignarServicio()
     * Crear registro para asignar el servicio a un usuario
     * @param mixed $regional_id
     * @param mixed $servicioId
     * @return
     */  
    public static function asignarServicio($regional_id, $servicio_id, $addNew = false)
    {
        $asignar = new AsignarServicio();
        $asignar->setServicioId($servicio_id);
        $asignar->setObservaciones('Solicitud de servicio radicada');
        $asignar->setFechaAsignacion(date("Y-m-d G:i:s"));
        $asignar->save();
        //********************************************************************
        $is_insert_user = ServicioPeer::asignarUserServicio($asignar->getAsignarservicioId(), $regional_id, $servicio_id, $addNew);
        if (!$is_insert_user){
        	ServicioPeer::deleteData(AsignarServicioPeer::TABLE_NAME,AsignarServicioPeer::ASIGNARSERVICIO_ID,$asignar->getAsignarservicioId());
        	return $is_insert_user;
        }else{
        	return $is_insert_user;
        }
    }

    /**
    * servicioActions::deleteData()
    * elimnar los registros de una tabla
    * @param mixed $table_name
    * @param mixed $name_column
    * @param mixed $servicio_id
    * @return
    */
    public static function deleteData($table_name = null, $name_column = null ,$servicio_id = null)
    {
        try {
            if(empty($table_name) || empty($name_column) || empty($servicio_id)){ return 0; }
            //**********************************************************************************************
            $conexion = Propel::getConnection();
            $sql = "DELETE " . $table_name . " WHERE " . $name_column . " = " . $servicio_id;
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    /**
    * servicioActions::deleteData()
    * elimna todo el regisitro de servicio en cascada
    * @param mixed $servicio_id
    * @return
    */
    public static function deleteCascada($servicio_id = null)
    {
        try {
            if(empty($servicio_id)){ return 0; }
            //**********************************************************************************************
            $conexion = Propel::getConnection();
            //**********************************************************************************************
            $sql = "DELETE " . UsuarioAsignadoSolicitudPeer::TABLE_NAME . " WHERE " . UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID . " IN(";
            $sql .= "SELECT " . AsignarServicioPeer::ASIGNARSERVICIO_ID . " FROM ".AsignarServicioPeer::TABLE_NAME." WHERE " . AsignarServicioPeer::SERVICIO_ID ." = ".$servicio_id;            
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            //**********************************************************************************************
            $sql = "DELETE " . ServicioBitacoraPeer::TABLE_NAME . " WHERE " . ServicioBitacoraPeer::SERVICIO_ID ." = ".$servicio_id;            
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            //**********************************************************************************************
            $sql = "DELETE " . AsignarServicioPeer::TABLE_NAME . " WHERE " . AsignarServicioPeer::SERVICIO_ID ." = ".$servicio_id;            
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
            //**********************************************************************************************
            $sql = "DELETE " . ServicioPeer::TABLE_NAME . " WHERE " . ServicioPeer::SERVICIO_ID ." = ".$servicio_id;            
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    /**
     * servicioActions::asignarUserServicio()
     * asigna el servicio a los usuarios receptores
     * @param mixed $id_asignar
     * @param mixed $regional_id
     * @return
     */  
    public static function asignarUserServicio($id_asignar, $regional_id,$servicio_id, $addNew = false)
    {
        $isValid = true;
        $servicio = ServicioPeer::retrieveByPk($servicio_id);
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_servicio = empty($usuariologuiado) ? $servicio->getUsuarioId() : $usuariologuiado;
		//**************************************************************************************************
        try {
            //codigo para usuario que asigno el servicio
            $userasignado2  = new UsuarioAsignadoSolicitud();		
            $userasignado2->setRolusuarioasignacionservicioId(1);
            $userasignado2->setAsignarservicioId($id_asignar);
            $userasignado2->setEstaAsignado(false);
            $userasignado2->setUsuarioId($servicio->getUsuarioId());
            $userasignado2->save();
            //**********************************************************************************************
            if($servicio->getTipoServicio()->getInitIntegracion() || !in_array($servicio->getTipoServicio()->getTipoEnvio(),array(3))){
                $ureceptor_id = $usuario_servicio;
            }else{
                $dependencia_id = $servicio->getUsuario()->getDependenciaId();
                //$proceso_id = ServicioPeer::getReceptorProcesoId($servicio->getTiposervicioId(),$regional_id);
                $ureceptor_id = ServicioPeer::getReceptorId($regional_id,$dependencia_id,$servicio->getTiposervicioId());
            }
            //**********************************************************************************************
            $servicioestado_id = $servicio->getServicioestadoId();
            if(!empty($ureceptor_id)){
                //codigo para usuario al que se le asigna el servicio
                $userasignado   = new UsuarioAsignadoSolicitud();                
                $userasignado->setRolusuarioasignacionservicioId(2);
                $userasignado->setAsignarservicioId($id_asignar);
                $userasignado->setUsuarioId($ureceptor_id);
                $userasignado->setEstaAsignado(true);
                $userasignado->save();
            }
            //**********************************************************************************************
            if($servicio->getTipoServicio()->getInitIntegracion()){ $servicioestado_id = 8; }//SGV
            else if(in_array($servicio->getTipoServicio()->getTipoEnvio(),array(2,5))){ $servicioestado_id = 10; }//email
            else if(!empty($ureceptor_id)){ $servicioestado_id = 2; }//receptor asignado
			else{  $servicioestado_id = 4; }//pendiente no tiene receptor
            //**********************************************************************************************
            $notas_email = null;
            if($addNew){
                $estadoinicial_bitacora = 4;//pendiente
                $obs_bitacora = "Servicio radicado";
                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$estadoinicial_bitacora,$ureceptor_id,$servicio->getUsuarioId(),$obs_bitacora);
            }elseif(in_array($servicio->getServicioestadoId(),array(1,2,4,10))){
                $usuario_envia = $servicio->getUsuarioId();
                $obs_bitacora = "Servicio radicado";
                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicio->getServicioestadoId(),$ureceptor_id,$usuario_envia,$obs_bitacora);
            }
            //**********************************************************************************************
            if($servicio->getTipoServicio()->getInitIntegracion()){
                $usuario_envia = $servicio->getUsuarioId();
                $obs_bitacora = "Servicio radicado enviado aplicacion externa (".$servicio->getServicioEstado().")";
                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicio->getServicioestadoId(),$ureceptor_id,$usuario_envia,$obs_bitacora);
            }else if($servicio->getTipoServicio()->getTipoEnvio()){
                $usuario_envia = $servicio->getUsuarioId();
                $obs_bitacora = "Servicio radicado enviado aplicacion externa (".$servicio->getServicioEstado().")";
                ServicioPeer::insertBitacoraServicio($servicio->getPrimaryKey(),$servicio->getServicioestadoId(),$ureceptor_id,$usuario_envia,$obs_bitacora);
            }
            //**********************************************************************************************
            if($servicio->getServicioestadoId() != $servicioestado_id){
                $servicio->setServicioestadoId($servicioestado_id);
                $servicio->save();
            }
            //**********************************************************************************************
            if(!empty($ureceptor_id)){
                if(in_array($servicio->getServicioestadoId(), array(1, 2))){
                    $encabezado_cuerpo = 'Este es un mensaje para informarle se le asign&oacute; la siguiente solicitud de servicio:';
                }else{
                    $encabezado_cuerpo = 'Este es un mensaje para informarle que se registro una nueva actividad en la siguiente solicitud de servicio:';
                }
                //******************************************************************************************
                $servicio->envioEmail($ureceptor_id,$notas_email,$encabezado_cuerpo);
            }
            //**********************************************************************************************
            return $isValid;
        } catch (PropelException $th) {
            return false;
        } catch (\Exception $th) {
            return false;
        } catch (\Throwable $th) {
            return false;
        }				
    }

    /**
   * servicioActions::getIdReasignar()
   *
   * @param mixed $llaveId
   * @return
   */
	public static function getIdReasignar($llaveId){				
		$c = new Criteria();
		$c->add(UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID,$llaveId);
		$c->add(UsuarioAsignadoSolicitudPeer::ROLUSUARIOASIGNACIONSERVICIO_ID,2);
		$res = UsuarioAsignadoSolicitudPeer::doSelect($c);
		$tempAs = array();
		$cont = 0;
		foreach ($res as $val) {
    		if ($cont == 0){                    
        	   $tempAs[0] = $val->getUsuarioId();
        	   $tempAs[1] = $val->getUsuarioasignadosolicitudId();
               $cont = 1;
            }            		
		}
		return $tempAs;	
	}

    /**
     * objectActions::createServicioByCom()
    * crea una nueva solicitud de servicio con la informacion de la comunicacion 
    * @return mixed array('isError' => true|false, 'message' => '', 'object' => objeto con los datos del servicio)
    * @param mixed $metadatos array con los datos del servicio
    * @param mixed llave primaria de la comunicacion o el registro de origen
    * @param mixed llave primaria de modulo al que pertenece el registro de origen
    */
    public static function createServicioByCom($metadatos, $pkcomid, $modulo_id)
	{
        $servicio = new Servicio();
        //**********************************************************************************
        try {
            $modulo_id = !empty($modulo_id) ? trim($modulo_id) : 7;
            $consecutivocompk = !empty($pkcomid) ? trim($pkcomid) : null;
            $estadoservicio_id = isset($metadatos['estadoservicio_id']) ? $metadatos['estadoservicio_id'] : 1;
            $list_interesado = isset($metadatos['coll_interesados']) ? $metadatos['coll_interesados'] : array();
            //******************************************************************************
            $tiposervicio_id = isset($metadatos['tiposervicio_id']) ? $metadatos['tiposervicio_id'] : 0;
            $tipo_servicio = TipoServicioPeer::retrieveByPK($tiposervicio_id);
			$usuario_servicio = UsuarioPeer::retrieveByPK($metadatos['usuario_id']);
            //******************************************************************************
            if(isset($metadatos['dependencia_id']) && !empty($metadatos['dependencia_id'])){
                $dependencia_id = $metadatos['dependencia_id'];
            }else{
			    $dependencia_id = $usuario_servicio != null ? $usuario_servicio->getDependenciaId() : 0;
            }
            //******************************************************************************
            if($tipo_servicio == null){
                return array('isError' => true, 'message' => 'El tipo de servicio no es valido', 'object' => null);
            }
            //******************************************************************************
            if($usuario_servicio == null){
                return array('isError' => true, 'message' => 'El usuario radicador del servicio no fue encontrado en el SGDEA', 'object' => null);
            }
            //******************************************************************************
            $receptor_id = ServicioPeer::validarReceptor($metadatos['regional_id'],$dependencia_id,$tiposervicio_id);
            if($tipo_servicio->getInitIntegracion()){ 
                $estadoservicio_id = 8;
                $receptor_id = $usuario_servicio->getPrimaryKey();
			}elseif($tipo_servicio->getTipoEnvio() == 2 && empty(trim($receptor_id))){
				$receptor_id = $usuario_servicio->getPrimaryKey();
            }
            //******************************************************************************
            if(empty($receptor_id)){
                return array('isError' => true, 'message' => 'La dependencia con el tipo de servicio, no tiene un receptor configurado para la solicitud de servicio, en el SGDEA', 'object' => null);
            }
            //******************************************************************************
            $servicio->setUsuarioId($metadatos['usuario_id']);
            $servicio->setEmpresaMensajeriaId(isset($metadatos['empresa_mensajeria_id']) ? trim($metadatos['empresa_mensajeria_id']) : null);
            $servicio->setPeriodoId(date("Y"));
            $servicio->setPrioridadsolicitudservicioId($metadatos['prioridadsolicitudservicio_id']);
            $servicio->setDirectorioexternoId(isset($metadatos['directorioexterno_id']) ? trim($metadatos['directorioexterno_id']) : null);
            $servicio->setTiposervicioId($metadatos['tiposervicio_id']);
            $servicio->setRegionalId($metadatos['regional_id']);
            $servicio->setServicioestadoId($estadoservicio_id);
            $servicio->setServiciotipodevolucionId(isset($metadatos['serviciotipodevolucion_id']) ? trim($metadatos['serviciotipodevolucion_id']) : null);
            $servicio->setModuloId($modulo_id);
            $servicio->setMarconormativoId(isset($metadatos['marconormativo_id']) ? $metadatos['marconormativo_id'] : null);
            $servicio->setProcesodocorigenId(isset($metadatos['procesodocorigen_id']) ? $metadatos['procesodocorigen_id'] : null);
            //******************************************************************************
            $servicio->setDetalle(isset($metadatos['detalle']) ? trim($metadatos['detalle']) : null);
            $servicio->setFechaCreacion(date("Y-m-d G:i:s"));
            $servicio->setEmailDestino(isset($metadatos['email_destino']) ? trim($metadatos['email_destino']) : null);
            $servicio->setFolios(isset($metadatos['folios']) ? trim($metadatos['folios']) : 0);
            $servicio->setGuia(isset($metadatos['guia']) ? trim($metadatos['guia']) : null);
            $servicio->setFechaEnvioGuia(isset($metadatos['fecha_guia']) ? trim($metadatos['fecha_guia']) : null);
            $servicio->setValorGuia(isset($metadatos['valor_guia']) ? trim($metadatos['valor_guia']) : null);
            $servicio->setFuncionarioDestino(isset($metadatos['funcionario_destino']) ? trim($metadatos['funcionario_destino']) : null);
            $servicio->setCargoDestinatario(isset($metadatos['cargo_destinatario']) ? trim($metadatos['cargo_destinatario']) : null);
            $servicio->setDireccionDestinatario(isset($metadatos['direccion_destinatario']) ? trim($metadatos['direccion_destinatario']) : null);
            $servicio->setPrefijo(isset($metadatos['prefijo']) ? trim($metadatos['prefijo']) : null);
            $servicio->setConsecutivocomId($consecutivocompk);
            $servicio->setFechaResolucion(isset($metadatos['fecha_resolucion']) ? $metadatos['fecha_resolucion'] : null);
            $servicio->setNumeroResolucion(isset($metadatos['numero_resolucion']) ? $metadatos['numero_resolucion'] : null);
            $servicio->setNumerosCaja(isset($metadatos['numeros_caja']) ? $metadatos['numeros_caja'] : null);
            $servicio->setNumerosCarpeta(isset($metadatos['numeros_carpeta']) ? $metadatos['numeros_carpeta'] : null);
            //$servicio->setRadicadoOrigen(isset($metadatos['radicado_origen']) ? $metadatos['radicado_origen'] : null);
            //******************************************************************************
            $numero_radicado = ServicioPeer::getNumRadicacion($metadatos['regional_id']);
            $radicado = ServicioPeer::executeRadicar($metadatos['regional_id'],$numero_radicado);
            $servicio->setRadicado($radicado);
            $servicio->setNumeroRadicacion($numero_radicado);
            $servicio->save();
            //******************************************************************************
            $is_insert_valid = ServicioPeer::asignarServicio($servicio->getRegionalId(), $servicio->getServicioId(),true);
            //******************************************************************************
            if (empty($is_insert_valid)){
                ServicioPeer::deleteData(ServicioPeer::TABLE_NAME,ServicioPeer::SERVICIO_ID,$servicio->getPrimaryKey());
                return array('isError' => true, 'message' => 'Error interno del servidor SGDEA', 'object' => null);
            }else{
                for($index = 0; $index < count($list_interesado); $index++) {
                    ServicioInteresadosPeer::addNewInteresadoByComId($servicio->getPrimaryKey(),$list_interesado[$index]);
                }
                //***************************************************************************
                //$servicio_anterior = new Servicio();
                //$servicio_anterior = $servicio->copy();
                //$this->guardarAuditoria($servicio_anterior,$servicio);
                //***************************************************************************
                return array('isError' => false, 'message' => 'Servicio creado', 'object' => $servicio);
            }
        } catch (PropelException $th) {
            if($servicio->getPrimaryKey() != null){
                ServicioPeer::deleteData(ServicioPeer::TABLE_NAME,ServicioPeer::SERVICIO_ID,$servicio->getPrimaryKey());
            }
            return array('isError' => true, 'message' => 'Error interno del servidor SGDEA, '.$th->getMessage(), 'object' => null);
        } catch (\Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        }
    }

    /**
     * servicioActions::getReceptorProcesoId()
     * funcion para obtener proceso_id por tipo de servicio
     * @param mixed $tiposervicio_id
     * @param mixed $regional_id
     * @return
     */  
    public static function getReceptorProcesoId($tiposervicio_id,$regional_id)
    {
        $proceso_id = 8;
		//*********************************************************************
        switch($tiposervicio_id)
        {
            case 1:
                    $proceso_id = 8;
            break;
            case 2:
                    $proceso_id = 8;
            break;
            case 3:
                    $proceso_id = 8;
            break;
            case 4:
                    $proceso_id = 8;
            break;
            case 5:
                    $proceso_id = 8;
            break;
            case 6:
                    $proceso_id = 8;
            break;
            case 7:
                    $proceso_id = 8;
            break;
            default:
                    $proceso_id = 8;
            break;
        }
        //******************************************************************
        $c = new Criteria();
        $c->add(FacturaReceptorPeer::REGIONAL_ID, $regional_id);
        $c->addAnd(FacturaReceptorPeer::FACTURAPROCESO_ID,$proceso_id);
        //$resp = FacturaReceptorPeer::doSelectOne($c);
		//******************************************************************        
        return $proceso_id;        
    }
	
    /**
     * servicioActions::validarReceptor()
     *
     * @param mixed $reg
     * @param mixed $servicioId
     * @return
     */  
    public static function validarReceptor($regional_id,$dependencia_id,$tiposervicio_id)
    {
        /*$c = new Criteria();
        $c->add(FacturaReceptorPeer::REGIONAL_ID, $regional_id);
        $c->addAnd(FacturaReceptorPeer::FACTURAPROCESO_ID,$proceso_id);
        $receptor = FacturaReceptorPeer::doSelectOne($c);*/
        $receptor_id = ServicioPeer::getReceptorId($regional_id,$dependencia_id,$tiposervicio_id);
        //********************************************************************************
        if(is_null($receptor_id)){
            return false;
        }else{
            return $receptor_id;
        }
    }

    /**
     * servicioActions::getReceptorId()
     * funcion para obtener el ID del usuario receptor de la Solicitud del servicio
     * @param mixed $regional_id
     * @return
     */  
    /*public static function getReceptorId($regional_id,$proceso_id)
    {
        $userId = '';
        //$receptor = new Receptor();
        $c = new Criteria();
        $c->add(FacturaReceptorPeer::REGIONAL_ID, $regional_id);
        $c->addAnd(FacturaReceptorPeer::FACTURAPROCESO_ID,$proceso_id);
        $resp = FacturaReceptorPeer::doSelectOne($c);                
        return $resp->getUsuarioId();        
        //return $userId;
    }*/

    /**
     * servicioActions::getReceptorId()
     * funcion para obtener el ID del usuario receptor de la Solicitud del servicio
     * @param mixed $regional_id
     * @return
     */  
    public static function getReceptorId($regional_id,$dependencia_id,$tiposervicio_id)
    {
        try {
            $c = new Criteria();
            $c->add(ServicioReceptorPeer::TIPOSERVICIO_ID, $tiposervicio_id);
            $c->add(ServicioReceptorPeer::DEPENDENCIA_ID, $dependencia_id);
            $resp = ServicioReceptorPeer::doSelectOne($c);
            //********************************************************************************
            return $resp != null ? $resp->getUsuarioId() : null;
		} catch (PropelException $th) {
			return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
    * servicioActions::getAsignado()
    *
    * @param mixed $idServicio
    * @return
    */
	public static function getAsignadoId($idServicio){
		$a = new Criteria();
		$a->add(AsignarServicioPeer::SERVICIO_ID,$idServicio);		
		$res = AsignarServicioPeer::doSelect($a);
		$cont = 0;
		foreach ($res as $val) {
            if ($cont == 0){ $tempAs = $val->getAsignarservicioId(); }
            $cont = 1;
		}
		//************************************************************************************
		$conexion = Propel::getConnection();
		$consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$tempAs;
        $sql      = sprintf($consulta, UsuarioAsignadoSolicitudPeer::USUARIOASIGNADOSOLICITUD_ID,UsuarioAsignadoSolicitudPeer::TABLE_NAME, UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID);		        
        //************************************************************************************
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        $max = ($resultset->max);        				
		//************************************************************************************	
		$c=new Criteria();
		$c->add(UsuarioAsignadoSolicitudPeer::USUARIOASIGNADOSOLICITUD_ID,$max);		
	    $resp =  UsuarioAsignadoSolicitudPeer::doSelect($c);        
		foreach($resp as $res){		
		  $temp = $res->getUsuarioId();		  			
	    }
	    //************************************************************************************	    
	    return $temp;
	}

    public static function getListServicosByComId($consecutivo_id = 0, $modulo_id = 0)
    {
    	$c = new Criteria();
        $c->add(ServicioPeer::CONSECUTIVOCOM_ID,$consecutivo_id);
        $c->add(ServicioPeer::MODULO_ID,$modulo_id);
        return ServicioPeer::doSelect($c);
    }

    public static function getListServicosByRadCom($com_radicado = 0, $radicado_origen = 0, $modulo_id = 0)
    {
        try {
            $c = new Criteria();
            //********************************************************************************
            switch ($modulo_id) {
                case 2://interna
                    $c->addJoin(ServicioPeer::CONSECUTIVOCOM_ID,ComInternaPeer::COMINTERNA_ID);
                    $c->add(ComInternaPeer::RADICADO,$com_radicado);
                    break;
                case 3://recibida
                    $c->addJoin(ServicioPeer::CONSECUTIVOCOM_ID,ComRecibidaPeer::COMRECIBIDA_ID);
                    $c->add(ComRecibidaPeer::RADICADO,$com_radicado);
                    break;
                case 4://enviada
                    $c->addJoin(ServicioPeer::CONSECUTIVOCOM_ID,ComEnviadaPeer::COMENVIADA_ID);
                    $c->add(ComEnviadaPeer::RADICADO,$com_radicado);
                    break;
                default:
                    if(!empty($radicado_origen)){ 
                        $c->add(ServicioPeer::RADICADO_ORIGEN,$radicado_origen); 
                    }else{
                        $c->add(ServicioPeer::CONSECUTIVOCOM_ID,0);
                    }
                    break;
            }
            //*************************************************************************************
            return ServicioPeer::doSelect($c);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getListDigitDocument($com_radicado = 0, $radicado_origen = 0, $modulo_id = 0)
	{
        $servicioscom_list = array();
        //*********************************************************************************************
        try{
            if(!empty($com_radicado) || !empty($radicado_origen)){
                //$files_adjuntos = preg_split("/[,]+/",$this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
                $servicios_list = ServicioPeer::getListServicosByRadCom($com_radicado,$radicado_origen,$modulo_id);
                //*****************************CREANDO LISTA DE ARCHIVOS*******************************
                foreach ($servicios_list as $servicio) {
                    $list_attach = array();
                    foreach ($servicio->getServicioAnexoss() as $serv_attachs){
                        if($serv_attachs->getEsActual()){
                            $files_adjuntos = preg_split("/[,]+/",$serv_attachs->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
                            if(trim($files_adjuntos[0])){
                                $filename = basename($files_adjuntos[0]);
                                $xguid = simad_util::create_guid($filename);
                                $url_secure = $serv_attachs->getUrlTokenViewImageByObject($xguid);
                                $url_download = $serv_attachs->getUrlTokenDownloadImageByObject($xguid);
                                $list_attach[] = array('NOMBRE_ARCHIVO' => $filename, 'DESCRIPCION_ADJUNTO' => $serv_attachs->getDescripcion(), 
                                                    'URL_ADJUNTO' => $url_secure,'DOWNLOAD_ADJUNTO' => $url_download, 'FECHA_ADJUNTO' => $serv_attachs->getFechaCreacion());
                            }
                        }
                    }
                    //*********************************************************************************
                    $servicio_info['RADICADO_SERVICIO'] = $servicio->getRadicado();
                    $servicio_info['FECHA_SERVICIO'] = $servicio->getFechaCreacion();
                    $servicio_info['NUMERO_FOLIOS'] = $servicio->getFolios();
                    $servicio_info['TIPO_SERVICIO'] = $servicio->getTipoServicio()->getDescripcion();
                    $servicio_info['ESTADO_SERVICIO'] = $servicio->getServicioEstado()->getDescripcion();
                    $servicio_info['NUMERO_RESOLUCION'] = $servicio->getNumeroResolucion();
                    $servicio_info['FECHA_RESOLUCION'] = $servicio->getFechaResolucion();
                    $servicio_info['DETALLE_SERVICIO'] = $servicio->getDetalle();
                    $servicio_info['ANEXOS_SERVICIO'] = $list_attach;
                    $servicioscom_list[] = $servicio_info;
                }
            }
        } catch (\Throwable $th) {
            $servicioscom_list = array();
        }
        //*********************************************************************************************
		return $servicioscom_list;
	}
}
