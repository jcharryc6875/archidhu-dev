<?php

/**
 * Subclass for performing query and update operations on the 'CONTENIDO_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ContenidoUnidadDocumentalPeer extends BaseContenidoUnidadDocumentalPeer
{
	public static function addContenidoUnidadDocumental($params, $isBackObj = true)
	{
		try
		{
			$contenido_unidad_documental = new ContenidoUnidadDocumental();
			$cotenido_doc_old = clone $contenido_unidad_documental;
			//****************************************************************************************************
			$unidad_documental = isset($params['unidad_documental_pk']) ? UnidadDocumentalPeer::retrieveByPk($params['unidad_documental_pk']) : null;
			if($unidad_documental == null)
			{
				return null;
			}
			//****************************************************************************************************
			$unidad_documental_old = clone $unidad_documental;
			//****************************************************************************************************
			$contenido_unidad_documental->setUnidaddocumentalId($params['unidad_documental_pk']);
			$contenido_unidad_documental->setVerificacioncontunidaddocId($params['verificacion_doc']);
			$contenido_unidad_documental->setTipoDocumentalId($params['tipo_doc_id']);
			$contenido_unidad_documental->setEstadocontenidounidaddocId($params['estado_doc']);
			$contenido_unidad_documental->setUsuarioId($params['usuario_id']);
			$contenido_unidad_documental->setTipoFirmaDigitalId($params['tipofirmadigital_id']);
			$contenido_unidad_documental->setSoporteunidaddocumentalId($params['soporte_documental']);
			$contenido_unidad_documental->setOrigendocumentoId($params['origen_documento']);
			$contenido_unidad_documental->setModuloId($params['modulo_id']);
			$contenido_unidad_documental->setConsecutivoId($params['consecutivo_id']);
			$contenido_unidad_documental->setDescripcion($params['descripcion']);
			$contenido_unidad_documental->setRuta($params['archivo_nombre']);
			$contenido_unidad_documental->setFechaCreacion(date('Y-m-d G:i:s'));
			$contenido_unidad_documental->setFolios($params['folios']);
			$contenido_unidad_documental->setCreadoPorWeb(0);
			//****************************************************************************************************
			if(trim($params['fecha_documento']))
			{
				$fecha_documento = trim($params['fecha_documento']);
				$contenido_unidad_documental->setFechaDocumento($fecha_documento);
			}
			//****************************************************************************************************
			$folio_inicial = ContenidoUnidadDocumentalPeer::getMaxFolioFinalByExpDoc($params['unidad_documental_pk']);
			$folio_final = $folio_inicial + ($params['folios'] - 1);
			//****************************************************************************************************
			$contenido_unidad_documental->setFolioInicial($folio_inicial);
			$contenido_unidad_documental->setFolioFinal($folio_final);
			$contenido_unidad_documental->setFuncEncryp("SHA1");
			$contenido_unidad_documental->setFormatFile($params['format_file']); 
			$contenido_unidad_documental->setSizeFile($params['size_file']); 
			$contenido_unidad_documental->setPathAbsolute($params['path_absolute']); 
			$contenido_unidad_documental->setPathRelative($params['path_relative']);
			$contenido_unidad_documental->setParentdocId($params['parentdoc_id'] ? $params['parentdoc_id'] : null); 
			$contenido_unidad_documental->setEsCopia($params['es_copia'] ? $params['es_copia'] : 0);
			$contenido_unidad_documental->setMarca(0); 
			$contenido_unidad_documental->setVinculoRegistro(0);
			//****************************************************************************************************
			$orden_documento = ContenidoUnidadDocumentalPeer::getOrderByDocInExp($params['unidad_documental_pk']);
			$contenido_unidad_documental->setOrdenContenido($orden_documento);
			//****************************************************************************************************
			$contenido_unidad_documental->save();
			$contenido_unidad_documental->generateValorHuella();
			//****************************************************************************************************
			AuditLogPeer::guardarAuditoriaLite(
				ContenidoUnidadDocumentalPeer::getOMClass(),
				$cotenido_doc_old,
				$contenido_unidad_documental,
				ModulesEnable::Archivo,
				$unidad_documental->getCodigoBarras(),
				$contenido_unidad_documental->getUsuarioId()
			);
			//****************************************************************************************************
			if(trim($params['fecha_documento']))
			{
				if($unidad_documental->getFechaCierre() == null)
				{
					$new_vencimiento = $unidad_documental->getFechaVencimientoCustom($contenido_unidad_documental->getFechaDocumento());
					$unidad_documental->setFechavencimiento($new_vencimiento);
					$unidad_documental->save();
					//********************************************************************************************
					AuditLogPeer::guardarAuditoriaLite(
						ContenidoUnidadDocumentalPeer::getOMClass(),
						$unidad_documental_old,
						$unidad_documental,
						ModulesEnable::Archivo,
						$unidad_documental->getCodigoBarras(),
						$contenido_unidad_documental->getUsuarioId()
					);
				}
			}
			//****************************************************************************************************
			if($isBackObj)
				return $contenido_unidad_documental;
			else
				return $contenido_unidad_documental->getPrimaryKey();
		}
		catch(PropelException $ex)
		{
            return null;
        }
		catch(\Exception $ex)
		{
            return null;
        }
	}

	public static function getContUnidadDocByUDocId($unidad_documental_id)
	{
		$c = new Criteria();
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $unidad_documental_id);
		$c->addDescendingOrderByColumn(ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO);
		return ContenidoUnidadDocumentalPeer::doSelect($c);
	}
	
	public static function getContUnidadDocByUDocIdFileTree($unidad_documental_id)
	{
		$c = new Criteria();
		$c->addJoin(ContenidoUnidadDocumentalPeer::CONTENIDODOCFILETREE_ID, ContenidodocFiletreePeer::CONTENIDODOCFILETREE_ID, Criteria::LEFT_JOIN);
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $unidad_documental_id); 
		$list_objects = ContenidoUnidadDocumentalPeer::doSelect($c);
		return $list_objects;
		
	}

	public static function getLastContUnidadDocByUDocId($unidad_documental_id)
	{
		$c = new Criteria();
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID, $unidad_documental_id);
		$c->addDescendingOrderByColumn(ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO);
		return ContenidoUnidadDocumentalPeer::doSelectOne($c);
	}
	
	static public function getCountPrestamo($unidad_documental)
	{
		$pru = new Criteria();
		$pru->addJoin(DetallePrestamoPeer::SOLICITUDPRESTAMO_ID,SolicitudPrestamoPeer::SOLICITUDPRESTAMO_ID);	
		$pru->add(DetallePrestamoPeer::ESTADOPRESTAMO_ID,1);	
		$pru->add(SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID,NULL);		
		$pru->add(SolicitudPrestamoPeer::UNIDADDOCUMENTAL_ID,$unidad_documental);
		$rs = DetallePrestamoPeer::doCount($pru);
		return $rs; 	
	}
	
	static public function getCountSolicitudes($unidad_documental,$usuariologuiado)
	{
		//************Verificar solicitudes del usuario Para Toda La Unidad Documental******************
		$tu = new Criteria();
		$tu->add(SolicitudPrestamoPeer::UNIDADDOCUMENTAL_ID,$unidad_documental);
		$tu->add(SolicitudPrestamoPeer::SOLICITUDPRESTAMOESTADO_ID,1);
		$tu->add(SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID,NULL);
		$tu->add(SolicitudPrestamoPeer::USUARIO_ID,$usuariologuiado);
		$rs  = SolicitudPrestamoPeer::doCount($tu);
		//**********************************************************************************************
		return $rs;
	}
	
	static public function getCountSolicitudesPorContenido($contenido_id,$usuariologuiado)
	{
		//*******************************Verificar solicitudes del usuario Por Contenido****************
		$s = new Criteria();
		$s->add(SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID,$contenido_id);
		$s->add(SolicitudPrestamoPeer::SOLICITUDPRESTAMOESTADO_ID,1);
		$s->add(SolicitudPrestamoPeer::USUARIO_ID,$usuariologuiado);
		$rs = SolicitudPrestamoPeer::doCount($s);
		//**********************************************************************************************
		return $rs;
	}
	
	static public function getDetallePrestamoPorContenido($contenido_id)
	{
		//*******************************Verificar prestamo Por Contenido*******************************
		$dp = new Criteria();
		$dp->addJoin(DetallePrestamoPeer::SOLICITUDPRESTAMO_ID,SolicitudPrestamoPeer::SOLICITUDPRESTAMO_ID);
		$dp->add(DetallePrestamoPeer::ESTADOPRESTAMO_ID,1);	
		$dp->add(SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID,$contenido_id);		
		$rs = DetallePrestamoPeer::doSelectOne($dp);
		//***********************************************************************************************
		return $rs;
	}
	
	static public function getListDocumentsByComId($unidaddocumental_id = 0)
	{
		$c = new Criteria();
		$c->setLimit(50);
		$c->addJoin(ContenidoUnidadDocumentalPeer::TIPODOCUMENTAL_ID,TipoDocumentalPeer::TIPODOCUMENTAL_ID);
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);	
		$c->addAscendingOrderByColumn(ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO);	
		$rs = ContenidoUnidadDocumentalPeer::doSelect($c);
		//***********************************************************************************************
		return $rs;
	}
	
	public static function getBulkListDocumentsByComId($unidaddocumental_id = 0)
	{
		$c = new Criteria();
		$c->setLimit(50);
		$c->addJoin(ContenidoUnidadDocumentalPeer::TIPODOCUMENTAL_ID,TipoDocumentalPeer::TIPODOCUMENTAL_ID);
		$c->addJoin(ContenidoUnidadDocumentalPeer::ORIGENDOCUMENTO_ID,OrigenDocumentoPeer::ORIGENDOCUMENTO_ID);
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);	
		$c->addAscendingOrderByColumn(ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO);
		//***********************************************************************************************
		$c->clearSelectColumns();
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::CONTENIDOUNIDADDOCUMENTAL_ID);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
		$c->addAsColumn('TIPODOC_CODIGO',TipoDocumentalPeer::CODIGO);
		$c->addAsColumn('CONTDOC_DESCP',ContenidoUnidadDocumentalPeer::DESCRIPCION);
		$c->addAsColumn('TIPODOC_DESCP',TipoDocumentalPeer::DESCRIPCION);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FECHA_CREACION);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::VALOR_HUELLA);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FUNC_ENCRYP);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FOLIO_INICIAL);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FOLIO_FINAL);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::FORMAT_FILE);
		$c->addSelectColumn(ContenidoUnidadDocumentalPeer::SIZE_FILE);
		$c->addAsColumn('ORIGENDOC_DESCP',OrigenDocumentoPeer::DESCRIPCION);
		//***********************************************************************************************
		return ContenidoUnidadDocumentalPeer::doSelectStmt($c);
	}
	
	public static function getPagerListDocumentsByComId($unidaddocumental_id = 0, $numPage = 1, $limit_records = 15)
	{
		$c = new Criteria();
		$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);	
		$c->add(ContenidoUnidadDocumentalPeer::ESTADOCONTENIDOUNIDADDOC_ID,3,Criteria::NOT_EQUAL);
		$c->addAscendingOrderByColumn(ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO);
		//***********************************************************************************************
		$pager = new sfPropelPager('ContenidoUnidadDocumental',$limit_records);
		$pager->setCriteria($c);
		$pager->setPage($numPage);
		$pager->setPeerMethod("doSelectJoinAllExceptContenidodocFiletree");
		$pager->init();
		return $pager;
	}
	
	public static function getFormatSharedComUrlView($contenido_id,$cosecutivo_id,$statehash,$modulo_id)
	{
		$data_view = array();
		//***********************************************************************************************
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //***********************************************************************************************
		if(empty($contenido_id) || empty($statehash) || empty($modulo_id) || empty($cosecutivo_id)){
            $data_view['IsViewValid'] = false;
            $data_view['backurl'] = "";
			return $data_view;
		}
		//***********************************************************************************************
		$viewhash = md5($cosecutivo_id.$usuariologuiado);
		$viewSharedLink = $statehash == $viewhash ? true : false;
		$data_view['IsViewValid'] = $viewSharedLink;
		//***********************************************************************************************
		$contenido_unidad_documental = ContenidoUnidadDocumentalPeer::retrieveByPK($contenido_id);
		$modulo_archivo = $contenido_unidad_documental->getUnidadDocumental()->getLocalizacionUnidadDocumental();
		//***********************************************************************************************
		$archivourl = "/archivo.php/contenido_documental/list?unidaddocumental_id=%s&modulo=%s";
		$archivourl = sprintf($archivourl,$contenido_unidad_documental->getUnidaddocumentalId(),$modulo_archivo);
		$data_view['backurl'] = $viewSharedLink ? $archivourl : "";
		//***********************************************************************************************
		return $data_view;
		/*if($modulo_id == 2){//ComInterna
			
		}elseif($modulo_id == 2){//ComInterna
		}elseif($modulo_id == 3){//ComRecibida
		}elseif($modulo_id == 4){//ComEnviada		
		}elseif($modulo_id == 12){//Documentacion Tecnica
		}elseif($modulo_id == 13){//Facturas
		}else{
			return null;
		}*/
	}

	public static function getMaxFolioFinalByExpDoc($unidaddocumental_id = 0, $estadocontdoc_id = 1)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT MAX(%s) AS value_max FROM %s WHERE %s = ".$unidaddocumental_id." AND %s = ".$estadocontdoc_id;
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::FOLIO_FINAL, ContenidoUnidadDocumentalPeer::TABLE_NAME,
					ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,ContenidoUnidadDocumentalPeer::ESTADOCONTENIDOUNIDADDOC_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$folio_final_last = !empty($resultset->value_max) ?  ($resultset->value_max + 1) : 1;
		} catch (PropelException $th) {
			$folio_final_last = 1;
		} catch (\Exception $th) {
			$folio_final_last = 1;
		} catch (\Throwable $th) {
			$folio_final_last = 1;
		}
		return $folio_final_last;
    }

	public static function getOrderByDocInExp($unidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT MAX(%s) AS value_max FROM %s WHERE %s = ".$unidaddocumental_id.";";
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::ORDEN_CONTENIDO, ContenidoUnidadDocumentalPeer::TABLE_NAME,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$value_max = !empty($resultset->value_max) ?  ($resultset->value_max + 1) : 1;
		} catch (\Throwable $th) {
			$value_max = 1;
		}
		return $value_max;
    }
	
	public static function addNewTotalFolios($unidaddocumental_id = 0)
    {
		$unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
		$num_folios = $unidad_documental->getFolios();
		$estadocontdoc_id = 1;
		//***********************************************************************************************************
		try {
			$conexion = Propel::getConnection();
			$consulta = "SELECT SUM(%s) AS total FROM %s where %s=".$unidaddocumental_id." AND %s=".$estadocontdoc_id;
			$sql = sprintf($consulta, ContenidoUnidadDocumentalPeer::FOLIOS, ContenidoUnidadDocumentalPeer::TABLE_NAME,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,
			ContenidoUnidadDocumentalPeer::ESTADOCONTENIDOUNIDADDOC_ID);
			//*******************************************************************************************************
			$sentencia = $conexion->prepare($sql);
			$sentencia->execute();
			$resultset = $sentencia->fetch(PDO::FETCH_OBJ);
			$num_folios = $resultset->total;    
			//***************************actualiza los folios de la unidad documental********************************
			$unidad_documental->setFolios($num_folios);
			$unidad_documental->save();
			//*******************************************************************************************************	
			return $num_folios;
		} catch (PropelException $th) {
            return $num_folios;
        } catch (Throwable $th) {
            return $num_folios;
        } catch (Exception $th) {
            return $num_folios;
        }
    }

	public static function deleteRegCascada($contenidounidaddocumental_id = 0)
    {
		try {
			$conexion = Propel::getConnection();
			$query = "DELETE FROM %s WHERE %s IN(SELECT %s FROM %s WHERE %s = ".$contenidounidaddocumental_id.");";
			$query = sprintf($query, DetallePrestamoPeer::TABLE_NAME, DetallePrestamoPeer::SOLICITUDPRESTAMO_ID,SolicitudPrestamoPeer::SOLICITUDPRESTAMO_ID,
						SolicitudPrestamoPeer::TABLE_NAME,SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			//***********************************************************************************************
			$query01 = "DELETE FROM %s WHERE %s = ".$contenidounidaddocumental_id.";";
			$query01 = sprintf($query01, SolicitudPrestamoPeer::TABLE_NAME, SolicitudPrestamoPeer::CONTENIDOUNIDADDOCUMENTAL_ID);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query01);
			$sentencia->execute();
		} catch (\Throwable $th) {
			$msgex = $th->getMessage();
		}		
    }

	public static function getListDocsDownload($unidaddocumental_id = 0)
    {
		$list_docs = array();
		//***************************************************************************************************
		try {
			$c = new Criteria();
			$c->addJoin(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			$c->addJoin(ContenidoUnidadDocumentalPeer::TIPODOCUMENTAL_ID,TipoDocumentalPeer::TIPODOCUMENTAL_ID);
			$c->add(ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,$unidaddocumental_id);
			$c->clearSelectColumns();
			//***********************************************************************************************
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::CONTENIDOUNIDADDOCUMENTAL_ID);//0
			$c->addSelectColumn(UnidadDocumentalPeer::CODIGO_BARRAS);//1
			$c->addSelectColumn(UnidadDocumentalPeer::TITULO);//2
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::RUTA);//3
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::PATH_ABSOLUTE);//4
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::PATH_RELATIVE);//5
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::DESCRIPCION);//6
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::VINCULO_REGISTRO);//7
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::PATH_ABSOLUTE);//8
			$c->addSelectColumn(ContenidoUnidadDocumentalPeer::PATH_RELATIVE);//9
			$c->addAsColumn("TIPODOC_NOMBRE",TipoDocumentalPeer::DESCRIPCION);//10
			//***********************************************************************************************
			$list_docs = ContenidoUnidadDocumentalPeer::doSelectStmt($c);
		}catch (PropelException $th){
			$list_docs = array();
		} catch (\Exception $th) {
			$msgex = $th->getMessage();
			$list_docs = array();
		} catch (\Throwable $th) {
			$msgex = $th->getMessage();
			$list_docs = array();
		}
		//***************************************************************************************************
		return $list_docs;
    }

	/**
    * ContenidoUnidadDocumentalPeer::getGroupOrigenDocByExpPk()
	* @param int $unidaddocumental_id id de la unidad documental
    * Obtiene un array de todos los origenes de los documentos indexados en el expediente
	* @return mixed array('descripcion del origen del documento')
    */
	public static function getGroupOrigenDocByExpPk($unidaddocumental_id = 0)
    {
		try {
			$origen_docs = array();
			//***********************************************************************************************
			$conexion = Propel::getConnection();
			$query = "SELECT %s AS origen_docs FROM %s JOIN %s ON %s = %s WHERE %s = ".$unidaddocumental_id." GROUP BY %s";
			$query = sprintf($query, OrigenDocumentoPeer::DESCRIPCION, ContenidoUnidadDocumentalPeer::TABLE_NAME,OrigenDocumentoPeer::TABLE_NAME,
				ContenidoUnidadDocumentalPeer::ORIGENDOCUMENTO_ID,OrigenDocumentoPeer::ORIGENDOCUMENTO_ID,
				ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,OrigenDocumentoPeer::DESCRIPCION);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$list_docs = $sentencia->fetchAll(PDO::FETCH_OBJ);
			//***********************************************************************************************
			foreach ($list_docs as $row) {
				$origen_docs[] = $row->origen_docs;
			}
		}catch (PropelException $th){
            $origen_docs = array();
		}catch (\Exception $th){
            $origen_docs = array();
		}catch (\Throwable $th){
			$origen_docs = array();
		}
		//***************************************************************************************************
		return $origen_docs;
    }

	/**
    * ContenidoUnidadDocumentalPeer::getLastFechaDocByExpPk()
	* @param int $unidaddocumental_id id de la unidad documental
    * Obtiene un array de todos los origenes de los documentos indexados en el expediente
	* @return mixed array('descripcion del origen del documento')
    */
	public static function getLastFechaDocByExpPk($unidaddocumental_id = 0)
    {
		$fecha_doc = "";
		//***************************************************************************************************
		try {
			$conexion = Propel::getConnection();
			$query = "SELECT MAX(%s) AS fecha_last_doc FROM %s WHERE %s = ".$unidaddocumental_id." AND %s IS NOT NULL AND %s <> ''";
			$query = sprintf($query, ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO, ContenidoUnidadDocumentalPeer::TABLE_NAME,
				ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO, ContenidoUnidadDocumentalPeer::FECHA_DOCUMENTO);
			//***********************************************************************************************
			$sentencia = $conexion->prepare($query);
			$sentencia->execute();
			$list_docs = $sentencia->fetchAll(PDO::FETCH_OBJ);
			//***********************************************************************************************
			foreach ($list_docs as $row) {
				$fecha_doc = $row->fecha_last_doc;
			}
		}catch (PropelException $th){
            $fecha_doc = "";
		}catch (\Exception $th){
            $fecha_doc = "";
		}catch (\Throwable $th){
			$fecha_doc = "";
		}
		//***************************************************************************************************
		return $fecha_doc;
    }

	/**
     * Reordena los folios de una unidad documental específica
     * 
     * @param int $unidadDocumentalId
     * @return array Resultado de la operación
     * @throws Exception
     */
    public static function reordenarFoliosPorFecha($unidadDocumentalId)
    {
        // Validar parámetro
        if (!is_numeric($unidadDocumentalId) || $unidadDocumentalId <= 0) {
            throw new InvalidArgumentException('ID de unidad documental inválido');
        }

        // Obtener conexión de Propel
        $con = Propel::getConnection(ContenidoUnidadDocumentalPeer::DATABASE_NAME);
        
        try {
            // Preparar llamada al procedimiento almacenado
            $sql = "{CALL sp_ReordenarFoliosPorFecha(:unidad_id)}";
            $stmt = $con->prepare($sql);
            
            // Vincular parámetro
            $stmt->bindParam(':unidad_id', $unidadDocumentalId, PDO::PARAM_INT);
            
            // Ejecutar
            $stmt->execute();
            
            // Obtener resultados
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $resultado,
                'message' => 'Folios reordenados exitosamente'
            ];
            
        } catch (PDOException $e) {
            throw new Exception('Error al reordenar folios: ' . $e->getMessage());
        }
    }

	 /**
     * Obtiene documentos desordenados de una unidad
     * 
     * @param int $unidadDocumentalId
     * @param PropelPDO $con
     * @return array
     */
    public static function getDocumentosDesordenados($unidadDocumentalId, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ContenidoUnidadDocumentalPeer::DATABASE_NAME);
        }
        
        $sql = "
            SELECT 
                CONTENIDOUNIDADDOCUMENTAL_ID,
                FECHA_DOCUMENTO,
                FOLIO_INICIAL,
                FOLIO_FINAL,
                CASE 
                    WHEN FOLIO_INICIAL <= LAG(FOLIO_FINAL) OVER (
                        ORDER BY FECHA_DOCUMENTO, CONTENIDOUNIDADDOCUMENTAL_ID
                    ) THEN 'DESORDENADO' 
                    ELSE 'OK' 
                END AS ESTADO_ORDEN
            FROM CONTENIDO_UNIDAD_DOCUMENTAL
            WHERE UNIDADDOCUMENTAL_ID = :unidad_id
            ORDER BY FECHA_DOCUMENTO, CONTENIDOUNIDADDOCUMENTAL_ID
        ";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':unidad_id', $unidadDocumentalId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

	/**
     * Obtiene el último folio de una unidad documental
     * 
     * @param int $unidadDocumentalId
     * @return int
     */
    public static function getUltimoFolio($unidadDocumentalId)
    {
        $c = new Criteria();
        $c->add(self::UNIDADDOCUMENTAL_ID, $unidadDocumentalId);
        $c->addDescendingOrderByColumn(self::FOLIO_FINAL);
        $c->setLimit(1);
        
        $ultimoDocumento = self::doSelectOne($c);
        
        return $ultimoDocumento ? $ultimoDocumento->getFolioFinal() : 0;
    }
}
