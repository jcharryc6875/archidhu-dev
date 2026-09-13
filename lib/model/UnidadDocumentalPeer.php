<?php

/**
 * Subclass for performing query and update operations on the 'unidad_documental' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UnidadDocumentalPeer extends BaseUnidadDocumentalPeer
{
	
	 static public function getAllUD(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(UnidadDocumentalPeer::CODIGO_BARRAS);
		$rs = UnidadDocumentalPeer::doSelect($c);
		return $rs; 	
	}
	
	public static function addUnidadDocumental($params)
	{
		try
		{   
			$unidad_documental = new UnidadDocumental();
			$unidad_documental->setTitulo($params['titulo']);
			$unidad_documental->setLocalizacionunidaddocumentalId($params['fase_archivo']);
			$unidad_documental->setRegionalId($params['regional_id']);
			$unidad_documental->setSoporteunidaddocumentalId($params['soporte_documental']);
			$unidad_documental->setEstadounidaddocumentalId($params['estado_documental']);
			$unidad_documental->setFrecuenciaconsultaId($params['frecuencia_documental']);
			$unidad_documental->setUnidadconservadoraId($params['medio_conservacion']);
			//$unidad_documental->setTipocierreexpedienteId(1);
			$unidad_documental->setSubserieId($params['subserie_id']);
			//***********************************************************************************************
			$unidad_documental->setContenido($params['contenido']);
			$unidad_documental->setCreadoPorWeb(0);
			$unidad_documental->setFolios($params['folios']);
			$unidad_documental->setNotas($params['notas']);
			$unidad_documental->setVolumen($params['volumen']);
			$unidad_documental->setNumeroCaja($params['numero_caja']);
			$unidad_documental->setNumeroCarpeta($params['numero_carpeta']);
			$unidad_documental->setGeoBodega($params['geo_bodega']);
			$unidad_documental->setGeoCuerpo($params['geo_cuerpo']);
			$unidad_documental->setGeoPiso($params['geo_piso']);
			$unidad_documental->setGeoTorre($params['geo_torre']);
			$unidad_documental->setNumeroIdentificacion($params['numero_identificacion']); 
			$unidad_documental->setEstadotransferencia(0);
			$unidad_documental->setFechaCreacion(date('Y-m-d G:i:s'));
			//***********************************************************************************************
			// capturar tiempos para retencion
			$subserie = SubseriePeer::retrieveByPK($params['subserie_id']);
			$paramentro_tvd = ParametroPeer::retrieveByPK(33);
			$fecha_tvd = $paramentro_tvd->getValortexto();
			//***********************************************************************************************
			$codigo_barras = trim($params['codigo_barras']) ? trim($params['codigo_barras']) : null;
			//***********************************************************************************************
			if(!empty($codigo_barras)){
				$repetido = UnidadDocumentalPeer::getUnidadDocumentalByCodigo($codigo_barras);
				if (!empty($repetido)) 
				{ 
					return array('error' => true, 'message' => 'El numero de expediente ya existe', 'object' => null);
				}
				//*******************************************************************************************
				$unidad_documental->setCodigoBarras($codigo_barras);
			}
			//***********************************************************************************************
			if ($params['fecha_apertura'])
			{
				try
				{
					$date = new DateTime($params['fecha_apertura']);
					$unidad_documental->setFechaApertura($date->format('Y-m-d'));
				}catch (Exception $ex){}
			}else{
				$unidad_documental->setFechaApertura(date('Y-m-d'));
			} 
			//***********************************************************************************************
			if ($params['fecha_cierre'])
			{
				try
				{
					$date = new DateTime($params['fecha_cierre']);
					$unidad_documental->setFechaCierre($date->format('Y-m-d'));
				}catch (Exception $ex){}
			} 
			//***********************************************************************************************
			$tiempo_retencion = 0;
			$campo_vencimiento = date("Y-m-d");
			$localizacion = $params['fase_archivo'];
			if($localizacion == 1)
			{
				$campo_vencimiento = $params['fecha_apertura'];
				$unidad_documental->setUbicacionengestion($params['ubicacion_expediente']);
				if($params['fecha_apertura'] <= $fecha_tvd)
				{
					$tiempo_retencion = $subserie->getAnosValoracionDocumental();
				}
				else
				{
					$tiempo_retencion = $subserie->getAnosEnGestion();
				}
			}
			elseif($localizacion == 2)
			{
				$campo_vencimiento = $params['fecha_cierre'];
				$unidad_documental->setUbicacionencentral($params['ubicacion_expediente']);
				if($params['fecha_cierre'] <= $fecha_tvd)
				{
					$tiempo_retencion = $subserie->getAnosValoracionDocumental();
				}
				else
				{
					$tiempo_retencion = $subserie->getAnosEnCentral();
				}
			}
			elseif($localizacion == 3)
			{
				$campo_vencimiento = $params['fecha_cierre'];
				$unidad_documental->setUbicacionenhistorico($params['ubicacion_expediente']);
				if($params['fecha_cierre'] <= $fecha_tvd)
				{
					$tiempo_retencion = $subserie->getAnosValoracionDocumental();
				}
				else
				{
					$tiempo_retencion = $subserie->getAnosEnHistorico();
				}
			}
			//***********************************************************************************************
			try
			{
				$fecha = new DateTime($campo_vencimiento);
				if(is_numeric($tiempo_retencion))
				{
					$fecha->add(new DateInterval('P'.$tiempo_retencion.'Y'));
				}
				$unidad_documental->setFechavencimiento($fecha->format('Y-m-d'));
			}
			catch (Exception $ex)
			{
				$fecha   = AddYears(date("Y-m-d"),$tiempo_retencion);
				$unidad_documental->setFechavencimiento($fecha);
			}
			//***********************************************************************************************
			$unidad_documental->save();
			//***********************************************************************************************
			if($codigo_barras == null)
			{
				$unidad_documental->setCodigoBarras($unidad_documental->getPrimaryKey());
				$unidad_documental->save();
			}
			//***********************************************************************************************
			$unidaddoc_anterior = new UnidadDocumental();
			AuditLogPeer::guardarAuditoriaLite("UnidadDocumental", $unidaddoc_anterior, $unidad_documental, ModulesEnable::Archivo, $unidad_documental->getCodigoBarras(),$params['id_creador']);
			//***********************************************************************************************
			UnidaddocumentalUsuarioPeer::executeUserUniDocRole(1, $params['id_creador'], $unidad_documental->getPrimaryKey());
			UnidaddocumentalUsuarioPeer::executeUserUniDocRole(2, $params['id_responsable'], $unidad_documental->getPrimaryKey());
			UnidaddocumentalUsuarioPeer::executeUserUniDocRole(3, $params['id_inventariador'], $unidad_documental->getPrimaryKey());
			//***********************************************************************************************
			$comlist_interesados = array();
			foreach ($params['new_interesados'] as $newitem) 
			{
				$newitem['USUARIO_ID'] = $params['id_creador'];
				$interesado_id = InteresadosPeer::addNewInteresado($newitem);
				if($interesado_id != null)
				{ 
					$comlist_interesados[] = $interesado_id;
				}						
			}
			//***********************************************************************************************
			$comlist_interesados = array_merge($comlist_interesados, $params['already_interesados']);
			foreach ($comlist_interesados as $interesado_item) 
			{
				$isAddInteresado = UnidaddocumentalInteresadosPeer::addNewInteresadoByComId($unidad_documental->getPrimaryKey(), $interesado_item);
			}
			//***********************************************************************************************
			return array('error' => false, 'message' => 'Expediente creado exitosamente', 'object' => $unidad_documental);
		}
		catch(PropelException $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
		catch(Exception $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
		catch(\Throwable $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
	}
	
	public static function getReporteUnidadDocumental($params, $apply_search = true)
	{
		try
		{
			$c = new Criteria();
			//*********************************************************************************
			$c->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID,$params['localizacionunidaddoc_id']);
			//*********************************************************************************
			if(!empty($params['nombre_expediente']))
			{
				$c->add(UnidadDocumentalPeer::TITULO, '%'.$params['nombre_expediente'].'%', Criteria::LIKE);
			}

			if(!empty($params['numero_expediente']))
			{
				$c->add(UnidadDocumentalPeer::CODIGO_BARRAS, $params['numero_expediente']);
			}

			if(!empty($params['dependencia']))
			{
				$c->addJoin(UnidadDocumentalPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
				$c->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
				$c->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
				//****************************************************************************
				$c->add(DependenciaPeer::DEPENDENCIA_ID, $params['dependencia']);
			}

			if(!empty($params['serie']))
			{
				$c->addJoin(UnidadDocumentalPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
				$c->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
				//****************************************************************************
				$c->add(SeriePeer::SERIE_ID, $params['serie']);
			}

			if(!empty($params['subserie']))
			{
				$c->add(UnidadDocumentalPeer::SUBSERIE_ID, $params['subserie']);
			}

			if(!empty($params['fechaCreaInicial']) && !empty($params['fechaCreaFinal']))
			{
				$c->add(UnidadDocumentalPeer::FECHA_CREACION, $params['fechaCreaInicial'], Criteria::GREATER_EQUAL);  
				$c->addAnd(UnidadDocumentalPeer::FECHA_CREACION, $params['fechaCreaFinal'], Criteria::LESS_EQUAL);
			}

			if(!empty($params['fechaVenceInicial']) && !empty($params['fechaVenceFinal']))
			{
				$c->add(UnidadDocumentalPeer::FECHAVENCIMIENTO, $params['fechaVenceInicial'], Criteria::GREATER_EQUAL);  
				$c->addAnd(UnidadDocumentalPeer::FECHAVENCIMIENTO, $params['fechaVenceFinal'], Criteria::LESS_EQUAL);
			}

			$total_array = array();
			$total_array['criteria'] = $c;
			$total_array['data_set'] = $apply_search ? UnidadDocumentalPeer::doSelect($c) : null;
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
		catch(\Throwable $ex)
		{
			return $ex->getMessage();
		}
	}

	public static function getUnidadDocumentalBySearch($params_search = null, $max_rows = 50)
	{
		try
		{
			if($params_search === null || !is_array($params_search)){
				return array('error' => true, 'message' => 'Error, los filtros enviados no son validos', 'object' => null);
			}
			//*************************************************************************************************
			if(isset($params_search['unidaddocumental_id']) && !empty($params_search['unidaddocumental_id'])){
				$unidad_documental[] = UnidadDocumentalPeer::retrieveByPK($params_search['unidaddocumental_id']);
				return array('error' => false, 'message' => 'Expedientes encontrados', 'object' => $unidad_documental);
			}
			//*************************************************************************************************
			$c = new Criteria(); 
			$c->setLimit($max_rows);
			//*******************************CAMPOS ADICIONALES OPCIONALES ************************************
			if(!empty($params_search['identificacion_interesado']) || !empty($params_search['pnombre_interesado']) || !empty($params_search['papellido_interesado']))
			{
				$c->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, UnidadDocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID);
				$c->addJoin(UnidadDocumentalInteresadosPeer::INTERESADO_ID, InteresadosPeer::INTERESADO_ID);

				if(!empty($params_search['identificacion_interesado']))
				{
					$c->add(InteresadosPeer::NUMERO_IDENTIFICACION, $params_search['identificacion_interesado'] . '%', Criteria::LIKE);
				}

				if(!empty($params_search['pnombre_interesado']))
				{
					$c->add(InteresadosPeer::PRIMER_NOMBRE, $params_search['pnombre_interesado'] . '%', Criteria::LIKE); 
				}

				if(!empty($params_search['papellido_interesado']))
				{
					$c->add(InteresadosPeer::PRIMER_APELLIDO, $params_search['papellido_interesado'] . '%', Criteria::LIKE);
				}
			}
			//*******************************CAMPOS ADICIONALES OPCIONALES ************************************
			if(!empty($params_search['fase_archivo']))
			{
				$c->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID, $params_search['fase_archivo']);
			}

			if(!empty($params_search['numero_expediente']))
			{
				$c->add(UnidadDocumentalPeer::CODIGO_BARRAS, $params_search['numero_expediente']);
			}

			if(!empty($params_search['subserie_id']))
			{
				$c->add(UnidadDocumentalPeer::SUBSERIE_ID, $params_search['subserie_id']);
			}

			if(!empty($params_search['codigo_dependencia']))
			{
				$c->addJoin(UnidadDocumentalPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
				$c->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
				$c->addAlias('DEPDNCIA', DependenciaPeer::TABLE_NAME);
				$c->addJoin(SeriePeer::DEPENDENCIA_ID, 'DEPDNCIA.DEPENDENCIA_ID');
				$c->add('DEPDNCIA.CODIGO', $params_search['codigo_dependencia']);
			}
			//*************************************************************************************************
			$unidad_documental = UnidadDocumentalPeer::doSelect($c);
			//*************************************************************************************************
			if (empty($unidad_documental)) 
			{ 
				return array('error' => true, 'message' => 'No se encontraron expedientes relacionados con los filtros seleccionados', 'object' => null);
			}
			//*************************************************************************************************
			return array('error' => false, 'message' => 'Expedientes encontrados', 'object' => $unidad_documental);
		}
		catch(PropelException $ex)
		{
			return array('error' => true, 'message' => 'Ocurrio un error al realizar la consulta de la informacion, es posible que los parametros de entrada no sean correctos', 'object' => null);
		}
		catch(\Exception $ex)
		{
			return array('error' => true, 'message' => 'Ocurrio un error interno en la aplicación', 'object' => null);
		}
		catch(\Throwable $ex)
		{
			return array('error' => true, 'message' => 'Ocurrio un error interno en el servidor', 'object' => null);
		}
	}
	
	public static function getUnidadDocumentalBySearchCustom($numero_expediente, $codigo_dependencia = null, $codigo_subserie = null, $int_fase_archivo)
	{
		try
		{
			$c = new Criteria();
			$c->add(UnidadDocumentalPeer::CODIGO_BARRAS, $numero_expediente);
			$c->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID, $int_fase_archivo);
			$unidad_documental = UnidadDocumentalPeer::doSelect($c);

			return array('error' => false, 'message' => 'Expediente encontrado', 'object' => $unidad_documental);
		}
		catch(PropelException $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
		catch(Exception $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
	}

	public static function getUnidadDocByCustomFilter($filters = array())
	{
		try
		{
			$c = new Criteria();
			//**************************************************************************************************
			foreach ($filters as $row_key => $row_value) {
				if(!empty($row_key) && !empty($row_value))
					$c->add(sprintf("%s::%s",UnidadDocumentalPeer::TABLE_NAME,$row_key), $row_value);
			}
			//**************************************************************************************************
			$unidad_documental = UnidadDocumentalPeer::doSelect($c);
			//**************************************************************************************************
			return array('error' => false, 'message' => 'Expediente encontrado', 'object' => $unidad_documental);
		}
		catch(PropelException $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
		catch(Exception $ex)
		{
			return array('error' => true, 'message' => $ex->getMessage(), 'object' => null);
		}
	}

	public static function getUnidadDocByCodigoAndTrd($filters = array())
	{
		try
		{
			$c = new Criteria();
			$c->addJoin(UnidadDocumentalPeer::SUBSERIE_ID,SubseriePeer::SUBSERIE_ID,Criteria::INNER_JOIN);
			$c->addJoin(SubseriePeer::SERIE_ID,SeriePeer::SERIE_ID,Criteria::INNER_JOIN);
			$c->addJoin(SeriePeer::DEPENDENCIA_ID,DependenciaPeer::DEPENDENCIA_ID,Criteria::INNER_JOIN);
			//**************************************************************************************************
			$c->add(UnidadDocumentalPeer::ESTADOUNIDADDOCUMENTAL_ID,array(1,2,5),Criteria::IN);
			$c->add(UnidadDocumentalPeer::ESTA_CERRADO,0);
			//**************************************************************************************************
			if(isset($filters['codigo_barras']) && !empty($filters['codigo_barras']))
				$c->add(UnidadDocumentalPeer::CODIGO_BARRAS, $filters['codigo_barras']);
			else
				return null;
			//**************************************************************************************************
			if(isset($filters['codigo_dependencia']) && !empty($filters['codigo_barras']))
				$c->add(DependenciaPeer::CODIGO, $filters['codigo_dependencia']);
			else
				return null;
			//**************************************************************************************************
			if(isset($filters['codigo_serie']) && !empty($filters['codigo_serie']))
				$c->add(SeriePeer::CODIGO, $filters['codigo_serie']);
			else
				return null;
			//**************************************************************************************************
			if(isset($filters['codigo_subserie']) && !empty($filters['codigo_subserie']))
				$c->add(SubseriePeer::CODIGO, $filters['codigo_subserie']);
			else
				return null;
			//**************************************************************************************************
			$unidad_documental = UnidadDocumentalPeer::doSelect($c);
			//**************************************************************************************************
			return $unidad_documental;
		}
		catch(PropelException $ex)
		{
			return null;
		}
		catch(\Exception $ex)
		{
			return null;
		}
		catch(\Throwable $ex)
		{
			return null;
		}
	}

	public static function getUnidadDocumentalByCodigo($el_codigo_barras)
	{
		$c = new Criteria();
		$c->add(UnidadDocumentalPeer::CODIGO_BARRAS, trim($el_codigo_barras));
		return UnidadDocumentalPeer::doSelect($c);
	}

	public static function getPermisoACarpeta($subserie_id,$unidaddocumental_id)
	{
		$usuario_conectado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//***************************************************************************************
		$usuario = UsuarioPeer::retrieveByPK($usuario_conectado);
		$unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
		//***************************************************************************************
		$perm_is_valid = array();
		//***************************************************************************************
		//PERMISO POR SUBSERIE
		$su = new Criteria();
		$su->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_conectado);
		$su->add(SubseriePorUsuarioPeer::SUBSERIE_ID, $subserie_id);
		$su->add(SubseriePorUsuarioPeer::PRESTAMO,true);
		$perm_is_valid['solo_prestamo'] = SubseriePorUsuarioPeer::doCount($su);
		//***************************************************************************************
		//PERMISO POR SUBSERIE
		$su = new Criteria();
		$su->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_conectado);
		$su->add(SubseriePorUsuarioPeer::SUBSERIE_ID, $subserie_id);
		$su->add(SubseriePorUsuarioPeer::CREACION,true);
		$perm_is_valid['solo_creacion'] = SubseriePorUsuarioPeer::doCount($su);
		//***************************************************************************************
		//PERMISO POR SUBSERIE
		$su = new Criteria();
		$su->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_conectado);
		$su->add(SubseriePorUsuarioPeer::SUBSERIE_ID, $subserie_id);
		$su->add(SubseriePorUsuarioPeer::VISUALIZACION,true);
		$perm_is_valid['solo_visualizacion'] = SubseriePorUsuarioPeer::doCount($su);
		//***************************************************************************************
		//PERMISO POR USUARIO CREADOR DE LA UNIDAD DOCUMENTAL - SOLO ARCHIVO DE GESTION
		if($unidad_documental->getLocalizacionunidaddocumentalId() == 1){
			$uc = new Criteria();
			$uc->add(UnidaddocumentalUsuarioPeer::USUARIO_ID, $usuario_conectado);
			$uc->add(UnidaddocumentalUsuarioPeer::ROLUSUUNIDADDOC_ID, 1);
			$uc->add(UnidaddocumentalUsuarioPeer::UNIDADDOCUMENTAL_ID, $unidaddocumental_id);
			$perm_is_valid['creador_expediente'] = UnidaddocumentalUsuarioPeer::doCount($uc);
		}
		//***************************************************************************************
		//PERMISO POR CARPETA
		$pc = new Criteria();
		$pc->add(PermisoUsuarioUniddocPeer::USUARIO_ID, $usuario_conectado);
		$pc->add(PermisoUsuarioUniddocPeer::UNIDADDOCUMENTAL_ID, $unidaddocumental_id);
		$perm_is_valid['permiso_porexpediente'] = PermisoUsuarioUniddocPeer::doCount($pc);
		//***************************************************************************************
		//PERMISO POR ENTIDAD
		$listallexp = sfContext::getInstance()->getUser()->checkPerm('LISTAR_EXPEDIENTES_TODAS_REGIONALES', $usuario_conectado);
		$perm_is_valid['todo_expediente'] = $listallexp ? 1 : 0;
		//***************************************************************************************
		//PERMISO POR DEPENDENCIA DEL USUARIO CON LA DEL EXPEDIENTE
		if($unidad_documental->getSubserie()->getSerie()->getDependenciaId() == $usuario->getDependenciaId()){
			$perm_is_valid['permiso_pordependencia'] = 1;
		}else{
			$perm_is_valid['permiso_pordependencia'] = 0;
		}
		//***************************************************************************************
		/*
		$ua = new Criteria();
		$ua->addJoin(UnidadDocumentalPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
		$ua->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
		$ua->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
		$ua->add(DependenciaPeer::DEPENDENCIA_ID, $usuario->getDependenciaId());
		$ua->add(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $unidaddocumental_id);
		$perm_is_valid['permiso_pordependencia'] = UnidadDocumentalPeer::doCount($ua);*/
		/***************************************************************************************/
		return $perm_is_valid;
	}
	
	public static function getPermSubseriePorUserCount($usuario_id,$subserie_id)
	{
		$usuario_conectado = UsuarioPeer::retrieveByPK($usuario_id);
		$dependencia_conectado = $usuario_conectado->getDependenciaId();
		/*******************************************************************************/
		$s = new Criteria();
		$s->setDistinct();
		$s->addJoin(SubseriePorUsuarioPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
		$s->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
		$s->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
		$s->add(SubseriePorUsuarioPeer::USUARIO_ID, $usuario_id);
		//*******************************************************************************
		$cton0 = $s->getNewCriterion(SubseriePorUsuarioPeer::SUBSERIE_ID, $subserie_id);
		$cton1 = $s->getNewCriterion(DependenciaPeer::DEPENDENCIA_ID,$dependencia_conectado);
		$cton0->addOr($cton1);
		$s->add($cton0);
		//*******************************************************************************
		$count_subseries = SubseriePorUsuarioPeer::doCount($s);
		//*******************************************************************************
		return  $count_subseries;
	}
	
	public static function getUsuarioIdExpByRol($unidaddocumental_id,$rol_id=2)
	{
		$s = new Criteria();
		$s->add(UnidaddocumentalUsuarioPeer::UNIDADDOCUMENTAL_ID, $unidaddocumental_id);
		$s->add(UnidaddocumentalUsuarioPeer::ROLUSUUNIDADDOC_ID, $rol_id);		
		$expediente_usuario = UnidaddocumentalUsuarioPeer::doSelectOne($s);
		//*******************************************************************************
		if($expediente_usuario){
			return $expediente_usuario->getUsuarioId();
		}else{
			return  null;
		}		
	}
	
	public static function getListIntersadosByComId($unidaddocumental_id, $limit_records = 15)
    {
    	$c = new Criteria();
		$c->setLimit($limit_records);
        $c->add(UnidaddocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);
        return UnidaddocumentalInteresadosPeer::doSelectJoinInteresados($c);
    }

	public static function getPagerListIntersadosByComId($unidaddocumental_id = 0, $numPage = 1, $limit_records = 15)
	{
		$c = new Criteria();
		$c->add(UnidaddocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);
		//***********************************************************************************************
		$pager = new sfPropelPager('UnidaddocumentalInteresados',$limit_records);
		$pager->setCriteria($c);
		$pager->setPage($numPage);		
		$pager->init();
		return $pager;
	}
	
	public static function getAllExpedienteByMarca($marca_exp = -1,$localizaciondoc_id = -1){
        if(empty($marca_exp)) { return false; }
		//*******************************************************************
        $c = new Criteria();
        $c->add(UnidadDocumentalPeer::MARCA,$marca_exp);
		$c->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID,$localizaciondoc_id);
        $results = UnidadDocumentalPeer::doSelect($c);
        //*******************************************************************
		return $results;
    }

	public static function readFileComCombined($inputFileName, $readHeaders=true, $deleteFile = false)
    {
        $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
        $directorio = simad_util::createPath($upload_dir);
		$simad_util = new simad_util();
        //*************************************************************************************************************
        $sheetData = array();
        $cod_msg = 0;
        $msg_error = "";
        $process_end = 0;
		$rows_read = 10000;
        $array_data = array();
        //*************************************************************************************************************
        if (trim($inputFileName)){
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                return null;
            }
            //*********************************************************************************************************
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            //*********************************************************************************************************
            $array_data['headerList'] = $sheet->rangeToArray('A1:' . $highestColumn . '1' ,NULL,TRUE);            
            //*********************************************************************************************************
            //$sheetData = $objPHPExcel->getSheet(0)->toArray(null,true,true,true);
            $sheetData = $sheet->rangeToArray('A2:' . $highestColumn . $highestRow, NULL,false,false);
            $sheetDataSerialize = $simad_util->getArrayForSend($sheetData);           
            $array_data['sheetData'] = ($sheetData);
            $array_data['sheetDataSerialize'] = $sheetDataSerialize;
            $array_data['cod_msg'] = 0;
            $array_data['msg_error'] = "";
            //*********************************************************************************************************
            if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        }else{
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = "Debe seleccionar el archivo que contiene las registros para radicar";
        }
        //*************************************************************************************************************
        return $array_data;
    }

	public static function getAllExpedienteByCodCount($lcodigo_barras = array())
	{
        if(!is_array($lcodigo_barras)) { return false; }
		//*******************************************************************
        $c = new Criteria();
        $c->add(UnidadDocumentalPeer::CODIGO_BARRAS,$lcodigo_barras,Criteria::IN);
        $rcount = UnidadDocumentalPeer::doCount($c);
        //*******************************************************************
		return $rcount == count($lcodigo_barras) ? true : false;
    }

	public static function getExpedienteByCodBarras($codigo_barras){
        if(is_null($codigo_barras)) { return null; }
		//*******************************************************************
        $c = new Criteria();
        $c->add(UnidadDocumentalPeer::CODIGO_BARRAS,$codigo_barras);
        $unidad_documental = UnidadDocumentalPeer::doSelectOne($c);
        //*******************************************************************
		return $unidad_documental;
    }

	/**
	 * Obtiene una unidad_documental por el titulo del registro
	 * @param string $codigo_barras
	 * @param int $localizacionunidaddocumental_id
	 * @return array(UnidadDocumental)|null
	 */
	public static function getExpedienteByTitulo($metadato_search, $localizacionunidaddocumental_id = null)
	{
		try
		{
			if(is_null($metadato_search)) { return null; }
			//***************************************************************************************
			$c = new Criteria();
			$c->add(UnidadDocumentalPeer::TITULO,$metadato_search);
			$c->add(UnidadDocumentalPeer::ESTA_CERRADO,0);
			//***************************************************************************************
			if(!is_null($localizacionunidaddocumental_id)){
				$c->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID, $localizacionunidaddocumental_id);
			}
			//***************************************************************************************
			return UnidadDocumentalPeer::doSelect($c);
		}
		catch(PropelException $ex)
		{
			return null;
		}
		catch(\Exception $ex)
		{
			return null;
		}
		catch(\Throwable $ex)
		{
			return null;
		}
    }
	
	public static function createFileRotuloCaja($handle, $object)
    {
        try 
        {
            //logo de la entidad
            $path_logo = sfConfig::get('base_simad') . "/images/encabezado_carta/";
            $default_logo = "logo_planilla.png";
            //******************************************************************************************************
			$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
			$dataUser  = UsuarioPeer::retrieveByPK($usuariologuiado);
			$path_logo .= trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) ? 'logos_carnet/'.trim($dataUser->getRegional()->getEntidad()->getLogoCorporativo()) : $default_logo;
            //******************************************BUCLE DENTRO DE BUCLE*********************************
            $numero_caja = $object['NUMERO_CAJA'];

			$d = new Criteria();
			$d->addJoin(UnidadDocumentalPeer::SUBSERIE_ID, SubseriePeer::SUBSERIE_ID);
			$d->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
			$d->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
			$d->addJoin(DependenciaPeer::ENTIDAD_ID, EntidadPeer::ENTIDAD_ID);
			$d->clearSelectColumns();
			$d->addSelectColumn(UnidadDocumentalPeer::CODIGO_BARRAS);
			$d->addSelectColumn(UnidadDocumentalPeer::TITULO);
			$d->addSelectColumn(EntidadPeer::LOGO_CORPORATIVO);
			$d->addAsColumn('NOMBRE_SUBSERIE', SubseriePeer::DESCRIPCION);
			$d->addAsColumn('CODIGO_SUBSERIE', SubseriePeer::CODIGO);
			$d->addAsColumn('NOMBRE_SERIE', SeriePeer::DESCRIPCION);
			$d->addAsColumn('CODIGO_SERIE', SeriePeer::CODIGO);
			$d->addAsColumn('NOMBRE_DEPENDENCIA', DependenciaPeer::NOMBRE);
			$d->addAsColumn('CODIGO_DEPENDENCIA', DependenciaPeer::CODIGO);
			$d->add(UnidadDocumentalPeer::NUMERO_CAJA, $numero_caja); 
			//******************************************************************************************************
			$list_objects = UnidadDocumentalPeer::doSelectStmt($d);
			//******************************************************************************************************
			$array_dependencia = array();
			$array_serie = array();
			$array_subserie = array();
			$array_udocumental = array();
			//******************************************************************************************************
			while($expediente = $list_objects->fetch())
			{
				if(!in_array($expediente['CODIGO_DEPENDENCIA'], $array_dependencia))
				{
					$array_dependencia[$expediente['CODIGO_DEPENDENCIA']] = sprintf('%s - %s', $expediente['CODIGO_DEPENDENCIA'], $expediente['NOMBRE_DEPENDENCIA']);
				}

				if(!in_array($expediente['CODIGO_SERIE'], $array_serie))
				{
					$array_serie[$expediente['CODIGO_SERIE']] = sprintf('%s - %s', $expediente['CODIGO_SERIE'], $expediente['NOMBRE_SERIE']);
				}

				if(!in_array($expediente['CODIGO_SUBSERIE'], $array_subserie))
				{
					$array_subserie[$expediente['CODIGO_SUBSERIE']] = sprintf('%s - %s', $expediente['CODIGO_SUBSERIE'], $expediente['NOMBRE_SUBSERIE']);
				}
				
				if(!in_array($expediente['CODIGO_BARRAS'], $array_udocumental))
				{
					$array_udocumental[$expediente['CODIGO_BARRAS']] = sprintf('%s - %s', $expediente['CODIGO_BARRAS'], $expediente['TITULO']);
				}
			}
			//******************************************************************************************************
			$array_dependencia_string = implode(', ', $array_dependencia);
			$array_serie_string = implode(', ', $array_serie);
			$array_subserie_string = implode(', ', $array_subserie);
			$array_udocumental_string = implode(', ', $array_udocumental);
            //******************************************DIRECTORIO DE LA PLANTILLAS*********************************
            $dir_plantilla = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
            //****************************************NOMBRES DE LAS PLANTILLAS*************************************
            $name_plantilla = "rptrotulocaja.txt";
            $file_name = $dir_plantilla.$name_plantilla;    
            $plantilla_contents = file_get_contents($file_name);
            //**************************************PATRONES PARA REMPLAZAR LOS VALORES*****************************
            $patrones = array();
            $patrones[0]  = '#.#$path_logo#.#';
            $patrones[1]  = '#.#$array_dependencia_string#.#';
            $patrones[2]  = '#.#$array_udocumental_string#.#';
            $patrones[3]  = '#.#$array_serie_string#.#';
            $patrones[4]  = '#.#$array_subserie_string#.#';
            $patrones[5]  = '#.#$numero_caja#.#';
            $patrones[6]  = '#.#$qrcodigo#.#';
            $patrones[7]  = '#.#$ext_fechainicio#.#';
            $patrones[8]  = '#.#$ext_fechafin#.#';
            $patrones[9]  = '#.#$ext_anioinicial#.#';
            $patrones[10]  = '#.#$ext_aniofinal#.#';
            $patrones[11]  = '#.#$version_formato#.#';
            $patrones[12]  = '#.#$codigo_formato#.#';
            $patrones[13]  = '#.#$fecha_formato#.#';
            $patrones[14]  = '#.#$array_ofproductora_string#.#';
            //******************************************************************************************************
            $qrfile = simad_util::generateCodeQrInFile($array_udocumental_string,20,QR_ECLEVEL_L,1);
            $codebarfile = simad_util::generateCodeBarInFile($numero_caja, false);
            $urlQrCode = sfConfig::get('localUrl') . "/tmp/" . $qrfile;
            $urlBarCode = sfConfig::get('localUrl') . "/tmp/" . $codebarfile;
            //******************************************************************************************************
            $sustituciones    = array();
            $sustituciones[0] = $path_logo;
            $sustituciones[1] = mb_convert_encoding($array_dependencia_string,'UTF-8');
            $sustituciones[2] = mb_convert_encoding($array_udocumental_string,'UTF-8');
            $sustituciones[3] = mb_convert_encoding($array_serie_string,'UTF-8');
            $sustituciones[4] = mb_convert_encoding($array_subserie_string,'UTF-8');
            $sustituciones[5] = $urlBarCode;
            $sustituciones[6] = $urlQrCode;
            $sustituciones[7] = !empty(trim($object['FECH_INICIO'])) ? date("d/m/Y", strtotime($object['FECH_INICIO'])) : '&nbsp;' ;
            $sustituciones[8] = !empty(trim($object['FECH_FIN'])) ? date("d/m/Y", strtotime($object['FECH_FIN'])) : '&nbsp;';    
            $sustituciones[9] = $object['ANIO_INICIAL'];
            $sustituciones[10] = !empty(trim($object['ANIO_FINAL']))? $object['ANIO_FINAL'] : $object['ANIO_INICIAL'];
            $sustituciones[11] = "03";//Version Formato
            $sustituciones[12] = "710,14,15-2";//Codigo Formato
            $sustituciones[13] = "27/07/2022";//Fecha Formato
            $sustituciones[14] = "N/A";
            //**********************************REMPLAZAR DATOS EN LA PLANTILLA*************************************
            $template_contents = str_replace($patrones,$sustituciones,$plantilla_contents); 	
            //******************************************************************************************************    
            fputs($handle, $template_contents);
        } 
        catch (PropelException $th) 
        {
            //throw $th;
        } 
        catch (\Exception $th) 
        {
            //throw $th;
        }
		catch (\Throwable $th) 
        {
            //throw $th;
        }
    }
	
	public static function getLastFolioByExpDoc($unidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT MAX(%s) AS value_max FROM %s WHERE %s = ".$unidaddocumental_id.";";
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::FOLIO_FINAL, ContenidoUnidadDocumentalPeer::TABLE_NAME,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$folio_final_last = !empty($resultset->value_max) ? $resultset->value_max : 1;
		}catch (PropelException $th){
            $folio_final_last = 1;
		}catch (\Exception $th){
            $folio_final_last = 1;
		}catch (\Throwable $th){
			$folio_final_last = 1;
		}
		//***************************************************************************************************
		return $folio_final_last;
    }

	public static function getSumFolioByExpDoc($unidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT SUM(%s) AS value_max FROM %s WHERE %s = ".$unidaddocumental_id." AND %s IS NOT NULL;";
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::FOLIOS, ContenidoUnidadDocumentalPeer::TABLE_NAME,
				ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, ContenidoUnidadDocumentalPeer::FOLIOS);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$folio_final_last = !empty($resultset->value_max) ? $resultset->value_max : 1;
		}catch (PropelException $th){
            $folio_final_last = 1;
		}catch (\Exception $th){
            $folio_final_last = 1;
		}catch (\Throwable $th){
			$folio_final_last = 1;
		}
		//***************************************************************************************************
		return $folio_final_last;
    }
	
	public static function getSumFileSizeByExpDoc($unidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT SUM(CONVERT(INT,%s)) AS sum_filesize FROM %s WHERE %s = ".$unidaddocumental_id." AND %s IS NOT NULL AND %s <> '';";
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::SIZE_FILE, ContenidoUnidadDocumentalPeer::TABLE_NAME,
				ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, ContenidoUnidadDocumentalPeer::SIZE_FILE, ContenidoUnidadDocumentalPeer::SIZE_FILE);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$sum_filesize = !empty($resultset->sum_filesize) ? $resultset->sum_filesize : 0;
		}catch (PropelException $th){
            $sum_filesize = 0;
		}catch (\Exception $th){
            $sum_filesize = 0;
		}catch (\Throwable $th){
			$sum_filesize = 0;
		}
		//***************************************************************************************************
		return $sum_filesize;
    }
	
	public static function getCountDocsByExpDoc($unidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT COUNT(%s) AS count_docs FROM %s WHERE %s = ".$unidaddocumental_id;
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, ContenidoUnidadDocumentalPeer::TABLE_NAME,
				ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$count_docs = !empty($resultset->count_docs) ? $resultset->count_docs : 0;
		}catch (PropelException $th){
            $count_docs = 0;
		}catch (\Exception $th){
            $count_docs = 0;
		}catch (\Throwable $th){
			$count_docs = 0;
		}
		//***************************************************************************************************
		return $count_docs;
    }

		/**
	 * @param array $expedienteIds
	 * @return array [expediente_id => cantidad_documentos]
	 */
	public static function getCountDocsByExpDocBulk(array $expedienteIds)
	{
		if (empty($expedienteIds)) {
			return array();
		}
	
		$c = new Criteria();
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
		$c->addAsColumn('TOTAL_DOCS', 'COUNT(' . ContenidoUnidadDocumentalPeer::CONTENIDOUNIDADDOCUMENTAL_ID . ')');
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $expedienteIds, Criteria::IN);
		$c->addGroupByColumn(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
	
		$stmt = ContenidoUnidadDocumentalPeer::doSelectStmt($c);
	
		$resultado = array();
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$resultado[$row[0]] = (int) $row[1];
		}
		return $resultado;
	}

	/**
	 * @param array $expedienteIds
	 * @return array [expediente_id => peso_total_bytes]
	 */
	public static function getSumFileSizeByExpDocBulk(array $expedienteIds)
	{
		if (empty($expedienteIds)) {
			return array();
		}
	
		$c = new Criteria();
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
		$c->addAsColumn('TOTAL_PESO', 'SUM(' . ContenidoUnidadDocumentalPeer::SIZE_FILE . ')');
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $expedienteIds, Criteria::IN);
		$c->addGroupByColumn(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
	
		$stmt = ContenidoUnidadDocumentalPeer::doSelectStmt($c);
	
		$resultado = array();
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$resultado[$row[0]] = (int) $row[1];
		}
		return $resultado;
	}
}
