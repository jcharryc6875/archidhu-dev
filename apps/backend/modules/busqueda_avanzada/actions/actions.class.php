<?php

/**
 * busqueda_avanzada actions.
 *
 * @package    simad
 * @subpackage busqueda_avanzada
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
 require_once(sfConfig::get('sf_lib_dir').'/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;

class busqueda_avanzadaActions extends sfActions
{

  public function preExecute()
  {    
    $isAuthenticated = $this->getUser()->isAuthenticated();
    $base_path = sfConfig::get('base_simad');
    if(!$isAuthenticated){
      $this->redirect($base_path."/backend.php/security/login");
    }
  }
  
  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
  		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
  	}	 
  }
  
  public function tienePrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $isValid = true;
  	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
  		$isValid = false;
  	}
    return $isValid;
  }

  public function verificaPrilegioCerrar($currentForm)
  { 
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
    }	 	  
  }

  /**
   * Executes index action
   *
   */
  public function executeIndex()
  {
    //$this->forward('busqueda_avanzada', 'consulta');
  }

  public function executeReporteDinamico()
  {
	//reseteo variables de sesion.
	$this->getUser()->setAttribute('sess_checkSelected', null, 'subscriber');
	$this->getUser()->setAttribute('es_pit_id', null, 'subscriber');
	$this->getUser()->setAttribute('es_search_after', null, 'subscriber');
	$this->getUser()->setAttribute('sess_modulo', null, 'subscriber');
	$this->getUser()->setAttribute('sess_criteria', null, 'subscriber');
	$this->getUser()->setAttribute('sess_maintablename', null, 'subscriber');
	$this->getUser()->setAttribute('sess_data_set', null, 'subscriber');

	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	//$this->localizacion = $localizacion = $this->getRequestParameter('localizacion');
	$this->localizacion = 1;
	if($this->localizacion == 1)
	{
        $this->permiso = 2;
    }
	elseif($this->localizacion == 2)
	{		
        $this->permiso = 1;
    }
	elseif($this->localizacion == 3)
	{		
        $this->permiso = 1;        
    }

	$param_trd = ParametroPeer::retrieveByPK(61)->getValornumerico();
	$this->form_tag = 1;//si es consulta o creacion

	$this->dependencias = DependenciaPeer::getDependenciaPorArchivo($this->permiso,$this->form_tag,$param_trd);
	$this->com_enviada = new ComEnviada();
	$this->com_recibida = new ComRecibida();
	$this->com_interna = new ComInterna();
  }

  public function executeReportarDinamico()
  {
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	//*****************************************************************************************************************
	$elModulo = !empty($this->getRequestParameter('el_modulo')) ? trim($this->getRequestParameter('el_modulo')) : null;
	$localizacionunidaddoc_id = !empty($this->getRequestParameter('localizacionunidaddoc_id')) ? trim($this->getRequestParameter('localizacionunidaddoc_id')) : null;
	$el_criteria = new Criteria();
	$params = array();
	$params['el_modulo'] = $elModulo;
	//*****************************************************************************************************************
	switch ($elModulo)
	{
		case ModulesEnable::Archivo: //1 unidad documental // reemplazar x ENUM
			if(empty($localizacionunidaddoc_id)){
				$localizacionunidaddoc_id = 0;
			}
			//*********************************************************************************************************
			$params['nombre_expediente'] = !empty($this->getRequestParameter('nombre_expediente')) ? trim($this->getRequestParameter('nombre_expediente')) : null;
			$params['numero_expediente'] = !empty($this->getRequestParameter('numero_expediente')) ? trim($this->getRequestParameter('numero_expediente')) : null;
			$params['dependencia'] =       !empty($this->getRequestParameter('dependencia_id')) ? trim($this->getRequestParameter('dependencia_id')) : null;
			$params['serie'] =             !empty($this->getRequestParameter('serie_id')) ? trim($this->getRequestParameter('serie_id')) : null;
			$params['subserie'] =          !empty($this->getRequestParameter('subserie_id')) ? trim($this->getRequestParameter('subserie_id')) : null;
			$params['fechaCreaInicial'] =  !empty($this->getRequestParameter('fechaCreaInicial')) ? trim($this->getRequestParameter('fechaCreaInicial')) : null;
			$params['fechaCreaFinal'] =    !empty($this->getRequestParameter('fechaCreaFinal')) ? trim($this->getRequestParameter('fechaCreaFinal')) : null;
			$params['fechaVenceInicial'] = !empty($this->getRequestParameter('fechaVenceInicial')) ? trim($this->getRequestParameter('fechaVenceInicial')) : null;
			$params['fechaVenceFinal'] =   !empty($this->getRequestParameter('fechaVenceFinal')) ? trim($this->getRequestParameter('fechaVenceFinal')) : null;
			$params['localizacionunidaddoc_id'] =  $localizacionunidaddoc_id;
			//*********************************************************************************************************
			$table_cabezeras = ['NUMERO EXPEDIENTE', 'NOMBRE EXPEDIENTE', 'LOCALIZACION', 'DEPENDENCIA', 'SUBSERIE'];
			$modulo = UnidadDocumentalPeer::getOMClass();
			//*********************************************************************************************************
			$all_array = UnidadDocumentalPeer::getReporteUnidadDocumental($params,false);
			//*********************************************************************************************************
			$el_criteria = $all_array['criteria'];
			break;
		case ModulesEnable::ComEnviada:  //2 comenviada			
			$params['comenv_radicado'] =       !empty($this->getRequestParameter('comenv_radicado')) ? trim($this->getRequestParameter('comenv_radicado')) : null;
			$params['comenv_asunto'] =         !empty($this->getRequestParameter('comenv_asunto')) ? trim($this->getRequestParameter('comenv_asunto')) : null;
			$params['comenv_periodo'] =        !empty($this->getRequestParameter('comenv_periodo')) ? trim($this->getRequestParameter('comenv_periodo')) : null;
			$params['comenv_estado'] =         !empty($this->getRequestParameter('comenv_estado')) ? trim($this->getRequestParameter('comenv_estado')) : null;
			$params['comenv_remitente'] =      !empty($this->getRequestParameter('comenv_remitente')) ? trim($this->getRequestParameter('comenv_remitente')) : null;
			$params['comenv_fechCreaDesde'] =  !empty($this->getRequestParameter('comenv_fechCreaDesde')) ? trim($this->getRequestParameter('comenv_fechCreaDesde')) : null;
			$params['comenv_fechCreaHasta'] =  !empty($this->getRequestParameter('comenv_fechCreaHasta')) ? trim($this->getRequestParameter('comenv_fechCreaHasta')) : null;
			//*********************************************************************************************************
			$table_cabezeras = ['RADICADO', 'ASUNTO', 'TRAMITE', 'FECHA CREACION'];
			$modulo = ComEnviadaPeer::getOMClass();
			//*********************************************************************************************************
			$el_criteria = ComEnviadaPeer::getReporteComEnviada($params);
			break;
		case ModulesEnable::ComRecibida:  //3 comrecibida
			$params['comrec_radicado'] =       !empty($this->getRequestParameter('comrec_radicado')) ? trim($this->getRequestParameter('comrec_radicado')) : null;
			$params['comrec_asunto'] =         !empty($this->getRequestParameter('comrec_asunto')) ? trim($this->getRequestParameter('comrec_asunto')) : null;
			$params['comrec_periodo'] =        !empty($this->getRequestParameter('comrec_periodo')) ? trim($this->getRequestParameter('comrec_periodo')) : null;
			$params['comrec_estado'] =         !empty($this->getRequestParameter('comrec_estado')) ? trim($this->getRequestParameter('comrec_estado')) : null;
			$params['comrec_remitente'] =      !empty($this->getRequestParameter('comrec_remitente')) ? trim($this->getRequestParameter('comrec_remitente')) : null;
			$params['comrec_fechCreaDesde'] =  !empty($this->getRequestParameter('comrec_fechCreaDesde')) ? trim($this->getRequestParameter('comrec_fechCreaDesde')) : null;
			$params['comrec_fechCreaHasta'] =  !empty($this->getRequestParameter('comrec_fechCreaHasta')) ? trim($this->getRequestParameter('comrec_fechCreaHasta')) : null;
			//*********************************************************************************************************
			$table_cabezeras = ['RADICADO', 'ASUNTO', 'TRAMITE', 'FECHA CREACION'];
			$modulo = ComRecibidaPeer::getOMClass();
			//*********************************************************************************************************
			$all_array = ComRecibidaPeer::getReporteComRecibida($params,false);
			//*********************************************************************************************************
			$el_criteria = $all_array['criteria'];
			break;			
		case ModulesEnable::ComInterna:  //4 cominterna
			$params['comint_radicado'] =       !empty($this->getRequestParameter('comint_radicado')) ? trim($this->getRequestParameter('comint_radicado')) : null;
			$params['comint_asunto'] =         !empty($this->getRequestParameter('comint_asunto')) ? trim($this->getRequestParameter('comint_asunto')) : null;
			$params['comint_periodo'] =        !empty($this->getRequestParameter('comint_periodo')) ? trim($this->getRequestParameter('comint_periodo')) : null;
			$params['comint_estado'] =         !empty($this->getRequestParameter('comint_estado')) ? trim($this->getRequestParameter('comint_estado')) : null;
			$params['comint_remitente'] =      !empty($this->getRequestParameter('comint_remitente')) ? trim($this->getRequestParameter('comint_remitente')) : null;
			$params['comint_fechCreaDesde'] =  !empty($this->getRequestParameter('comint_fechCreaDesde')) ? trim($this->getRequestParameter('comint_fechCreaDesde')) : null;
			$params['comint_fechCreaHasta'] =  !empty($this->getRequestParameter('comint_fechCreaHasta')) ? trim($this->getRequestParameter('comint_fechCreaHasta')) : null;
			//*********************************************************************************************************
			$table_cabezeras = ['RADICADO', 'ASUNTO', 'TRAMITE', 'FECHA CREACION'];
			$modulo = ComInternaPeer::getOMClass();
			//*********************************************************************************************************
			$el_criteria = ComInternaPeer::getReporteComInterna($params);
			break;
		default:
			$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
			break;

	}
	//*****************************************************************************************************************
	unset($_SESSION['rptdinamic_search']);
	//*****************************************************************************************************************
	$_SESSION['rptdinamic_search'] = [
		'sess_modulo' => $elModulo,
		'params_search' => base64_encode(serialize($params)),
		'sess_maintablename' => $modulo,
	];
	//*****************************************************************************************************************
	$this->el_modulo = $modulo;
	$this->list_headers = $table_cabezeras;
	//*****************************************************************************************************************
	$la_pagina = trim($this->getRequestParameter('page')) ? trim($this->getRequestParameter('page')) : 1;
	//*****************************************************************************************************************
	$pager = new sfPropelPager($modulo, 10);
    $pager->setCriteria($el_criteria);
    $pager->setPage((int)$la_pagina);
    $pager->init();
    $this->pager = $pager;
	$this->parametros = '&' . http_build_query($params);
  }

  public function executeExportarExcel()
  {
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	//**********************************************************************************************************
	// RECOGEMOS LA VARIABLE DE SESION
	$clasename_main = null;
	if (isset($_SESSION['rptdinamic_search'])) {
		$rptdinamic_search = $_SESSION['rptdinamic_search'];
		if(isset($rptdinamic_search['sess_maintablename'])){
			$clasename_main = $rptdinamic_search['sess_maintablename'];
		}
	}else{
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}
	//**********************************************************************************************************
	$peername_main = sprintf("%sPeer",$clasename_main);
	//**********************************************************************************************************
	$tableMap = $peername_main::getTableMap();
	$current_table = $tableMap->getClassName();
	//**********************************************************************************************************
	$listCustom = array();
	//$fks_table = $tableMap->getForeignKeys();
	$tableColumns = $tableMap->getColumns();
	foreach($tableColumns as $row)
	{
		if(!empty($row->getRelatedTableName()))
		{
			$relClassName = str_replace(" ","",ucwords(strtolower(str_replace("_"," ",$row->getRelatedTableName()))));
			$relPeerClass = sprintf("%sPeer",$relClassName);
			$relTableMap = $relPeerClass::getTableMap();
			foreach ($relTableMap->getColumns() as $relCol) 
			{
				if(!$relCol->isPrimaryKey() && empty($relCol->getRelatedTableName()))
				{
					$tableNameInit = sprintf("%s.%s", $relTableMap->getPhpName(), $relCol->getPhpName());
					$listCustom[] = $tableNameInit;
				}
			}
		}
		else
		{
			$tableNameInit = sprintf("%s.%s", $current_table, $row->getPhpName());
			$listCustom[] = $tableNameInit;
		}
	}
	//**********************************************************************************************************
	$this->customFields = $listCustom;
  }

  public function executeGenerarTablaExcel()
  {
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	$this->setLayout(false);
	//**********************************************************************************************************
	$response_process = array('status' => 400, 'message' => 'Error interno del servidor');
	$clasename_main = null;
	$el_criteria = null;
	$elModulo = null;
	$params_search = array();
	//**********************************************************************************************************
	try
	{
		$checkSelected = $this->getRequest()->getPostParameters();
		if(empty($checkSelected['duallistbox_demo1']))
		{
			$this->getUser()->setFlash('error', 'Debe seleccionar al menos un campo para exportar.');
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		//*******************************************************************************************************
		// RECOGEMOS LA VARIABLE DE SESION
		if (isset($_SESSION['rptdinamic_search'])) {
			$rptdinamic_search = $_SESSION['rptdinamic_search'];
			if(isset($rptdinamic_search['sess_maintablename'])){
				$clasename_main = $rptdinamic_search['sess_maintablename'];
			}

			if(isset($rptdinamic_search['params_search'])){
				$params_search = unserialize(base64_decode($rptdinamic_search['params_search']));
			}

			if(isset($rptdinamic_search['sess_modulo'])){
				$elModulo = $rptdinamic_search['sess_modulo'];
			}
		}else{
			$response_process['message'] = 'Error relacionado con la sesión del usuario';
			$this->getResponse()->setContentType('application/json');
			return $this->renderText(json_encode($response_process));
		}
		//*******************************************************************************************************
		if (empty($params_search)) 
		{
			$response_process['message'] = 'Error con los filtros de consulta';
			$this->getResponse()->setContentType('application/json');
			return $this->renderText(json_encode($response_process));
        }
		//*******************************************************************************************************
		switch ($elModulo)
		{
			case ModulesEnable::Archivo:
				$data = UnidadDocumentalPeer::getReporteUnidadDocumental($params_search,false);
				$el_criteria = $data['criteria'];
				break;
			case ModulesEnable::ComEnviada:  //2 comenviada			
				$el_criteria = ComEnviadaPeer::getReporteComEnviada($params_search);
				break;
			case ModulesEnable::ComRecibida: //3 comrecibida
				$data = ComRecibidaPeer::getReporteComRecibida($params_search,true);
				$el_criteria = $data['criteria'];
				break;			
			case ModulesEnable::ComInterna:  //4 cominterna
				$el_criteria = ComInternaPeer::getReporteComInterna($params_search);
				break;
			default:
				$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
				break;

		}
		//*******************************************************************************************************
		$peername_main = sprintf("%sPeer",$clasename_main);
		//*******************************************************************************************************
		$dbMap = Propel::getDatabaseMap($el_criteria->getDbName());
		//*******************************************************************************************************
		$tableMapMain = $peername_main::getTableMap();
		$fks_table = $tableMapMain->getForeignKeys();
		//*******************************************************************************************************
		$criteria = clone $el_criteria;
		$criteria->clearSelectColumns();
		$criteria->clearOrderByColumns();
		$criteria->setLimit(null);
		//*******************************************************************************************************
		$last_peer = null;
		$listphpnames = null;
		$listcolnames = null;
		$headers_cols = array();
		//******************************************************************************************************* 
		foreach ($checkSelected['duallistbox_demo1'] as $key => $value) // $checkSelected['searchable'] 
		{
			$partes = explode("_", str_replace('.', '_', $value));
			$className = $partes[1];
			$peer_name = sprintf("%sPeer",$className);
			$tableMap = $peer_name::getTableMap();
			$colsTable = $tableMap->getColumns();

			if($peer_name !== $last_peer || (empty($listphpnames) || empty($listcolnames)))
			{
				$listphpnames = $peer_name::getFieldNames(BasePeer::TYPE_PHPNAME);  
				$listcolnames = $peer_name::getFieldNames(BasePeer::TYPE_COLNAME); 
			}
			//***************************************************************************************************
			$isNotNull = null;
			$relationTable = null;
			$relationColumn = null;
			$peerTableRealtion = null;
			foreach ($fks_table as $fk_row) 
			{
				$relationColumn1 = $fk_row->getRelatedColumnName();
				$isFkRelated = $colsTable[$fk_row->getRelatedColumnName()];
				//***********************************************************************************************
				if($isFkRelated != null){
					if(!empty($isFkRelated->getRelatedColumnName())){
						continue;
					}
				}
				//***********************************************************************************************
				$isExistCol = $colsTable[$fk_row->getRelatedColumnName()];
				if($isExistCol)
				{
					$isNotNull = $fk_row->isNotNull();
					$relationTable = $fk_row->getRelatedTableName();
					$relationColumn = $fk_row->getRelatedColumnName();
					//$peerTableRealtion = sprintf("%sPeer",ucwords($relationTable));
					break;
				}
			}
			//***************************************************************************************************
			for ($i= 0; $i<count($listphpnames); $i++) 
			{
				if($listphpnames[$i] == $partes[2]) //si hay coincidencia, lo guarda en un array de cabeceras excel
				{
					if(!empty($relationTable))
					{
						$colUniqueName = sprintf("%s",strtoupper(str_replace(".","_",$listcolnames[$i])));

						if(!empty($isNotNull))
						{
							if($isNotNull)
								$criteria->addJoin(sprintf("%s.%s",$tableMapMain->getName(),$relationColumn),sprintf("%s.%s",$relationTable,$relationColumn),Criteria::INNER_JOIN);
							else
								$criteria->addJoin(sprintf("%s.%s",$tableMapMain->getName(),$relationColumn),sprintf("%s.%s",$relationTable,$relationColumn),Criteria::LEFT_JOIN);
							//***********************************************************************************
							$criteria->addAsColumn($colUniqueName,$listcolnames[$i]);
						}
					}else{
						$colUniqueName = sprintf("%s",strtoupper(str_replace(".","_",$listcolnames[$i])));
						$criteria->addAsColumn($colUniqueName,$listcolnames[$i]);
					}
					//*******************************************************************************************
					if(!in_array($colUniqueName,$headers_cols)){
						$headers_cols[] =  $colUniqueName;
					}
					break;
				}
			}
			//***************************************************************************************************
			$last_peer = sprintf("%sPeer",$className);
		}
		//*******************************************************************************************************
		$results = $peername_main::doSelectStmt($criteria)->fetchAll(PDO::FETCH_ASSOC);
		//*******************************************************************************************************
		$writer = WriterEntityFactory::createXLSXWriter();
		//*******************************************************************************************************
		$dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
		//*******************************************************************************************************
		$filename = 'reporte_' . uniqid() . '_' . date('YmdGis') . '.xlsx';
		$tempPath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $dir_tmp . DIRECTORY_SEPARATOR . $filename;
		//*******************************************************************************************************
		$writer->openToFile($tempPath);
		//*******************************************************************************************************
		$headerStyle = (new StyleBuilder())
		->setFontBold()
		->setBackgroundColor(Color::LIGHT_BLUE)
		->build();
		//*******************************************************************************************************
		$dataStyle = (new StyleBuilder())
		->setFontSize(10)
		->build();
		//*******************************************************************************************************
		// Encabezados
		$headerRow = WriterEntityFactory::createRowFromArray($headers_cols, $headerStyle);
		$writer->addRow($headerRow);
		//*******************************************************************************************************
		$batchRows = [];
		$count = 0;
		//*******************************************************************************************************
		// Data rows 
		foreach($results as $rowData)
		{
			$batchRows[] = WriterEntityFactory::createRowFromArray($rowData, $dataStyle);
			$count++;
			if ($count % 1000 == 0) 
			{
				$writer->addRows($batchRows);
				unset($batchRows);
				$batchRows = [];
			}
		}
		//*******************************************************************************************************
		if (!empty($batchRows)) 
		{
			$writer->addRows($batchRows);
		}
		//*******************************************************************************************************
		$writer->close();
		//*******************************************************************************************************
        // Verificar que el archivo se creó correctamente
        if (!file_exists($tempPath)) 
		{
			$response_process['message'] = 'Error al generar el archivo Excel';
			$this->getResponse()->setContentType('application/json');
			return $this->renderText(json_encode($response_process));
        }
		//*******************************************************************************************************
		$publicUrl = sfConfig::get('publicUrl');
		if (substr($publicUrl, -1) !== '/') 
		{
    		$publicUrl .= '/';
		}
		//*******************************************************************************************************
		$response_process['message'] = 'El reporte se genero correctamente';
		$response_process['status'] = 200;
		$response_process['url_download'] = $publicUrl . $dir_tmp . "/". $filename;
	}
	catch(PropelException $e)
	{
		$response_process['message'] = 'Ocurrio un error genrando los datos para el excel';
	}
	catch(\Exception $e)
	{
		$response_process['message'] = 'Ocurrio un error interno en la aplicacion';
	}
	catch(\Throwable $e)
	{
		$response_process['message'] = 'Ocurrio un error interno en el servidor';
	}
	//***********************************************************************************************************
	$this->getResponse()->setContentType('application/json');
	return $this->renderText(json_encode($response_process));
  }

  public function executeFormGenerarReporte()
  {	
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	$postParams = $this->getRequest()->getPostParameters();
	$checkSelected = !empty($postParams["duallistbox_demo1"]) ? $postParams["duallistbox_demo1"] : null;

	if($checkSelected == null)
	{
		header('HTTP/1.1 400 Bad Request');
		http_response_code(400);
		exit();
	}
	$this->string_resultado = implode(';', $checkSelected);
	$this->usuario_reporte_id = !empty($this->getRequestParameter('usuario_reporte_id')) ? trim($this->getRequestParameter('usuario_reporte_id')) : null;
  }


  public function executeGuardarReporte()
  {	
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	//*********************************************************************************
	$params = [];
	$params['modulo_id'] = isset($_SESSION['rptdinamic_search']) ? $_SESSION['rptdinamic_search']['sess_modulo'] : $this->getUser()->getAttribute('sess_modulo', '', 'rptdinamic_search');
	$params['campos_export'] = !empty($this->getRequestParameter('campos_export')) ? trim($this->getRequestParameter('campos_export')) : null;
	$params['nombre_reporte'] = !empty($this->getRequestParameter('nombre_reporte')) ? trim($this->getRequestParameter('nombre_reporte')) : null;
	$params['descripcion_reporte'] = !empty($this->getRequestParameter('descripcion_reporte')) ? trim($this->getRequestParameter('descripcion_reporte')) : null;

	$usuario_reporte_id = !empty($this->getRequestParameter('usuario_reporte_id')) ? trim($this->getRequestParameter('usuario_reporte_id')) : null;

	if(empty($params['nombre_reporte']) || empty($params['descripcion_reporte']))
	{
		$data_array['status'] = 403;
		$data_array['mensaje'] = 'Hay campos obligatorios que no fueron diligenciados';
		//*****************************************************************************
		$this->getResponse()->setContentType('application/json');
		$data_json = json_encode($data_array);
		return $this->renderText($data_json);
	}

	$reporte_guardado = UsuarioReportePeer::updateUsuarioReporte($params, $usuario_reporte_id);

	if($reporte_guardado)
	{
		$data_array['status'] = 200;
		$data_array['mensaje'] = 'Reporte guardado exitosamente';
		//*****************************************************************************
		$this->getResponse()->setContentType('application/json');
		$data_json = json_encode($data_array);
		return $this->renderText($data_json);
	}
	else
	{
		$data_array['status'] = 500;
		$data_array['mensaje'] = 'No se pudo guardar, intente mas tarde';
		//*****************************************************************************
		$this->getResponse()->setContentType('application/json');
		$data_json = json_encode($data_array);
		return $this->renderText($data_json);
	}
  }

  public function executeLoadStoredReport()
  {
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	$usuario_reporte_id = $this->getRequestParameter('usuario_reporte_id');
	$usuarioreporte = UsuarioReportePeer::retrieveByPk($usuario_reporte_id);

	if($usuarioreporte != null)
	{
		$data_array['status'] = 200;
		$data_array['campos'] = explode(';', $usuarioreporte->getCamposExport());
		$data_array['reporte_nombre'] = $usuarioreporte->getNombre();
		$data_array['reporte_descripcion'] = $usuarioreporte->getDescripcion();
		$data_array['mensaje'] = 'Reporte encontrado';
		//*****************************************************************************
		$this->getResponse()->setContentType('application/json');
		$data_json = json_encode($data_array);
		return $this->renderText($data_json);
	}
	else
	{
		$data_array['status'] = 500;
		$data_array['mensaje'] = 'No se pudo encontrar el reporte, intente mas tarde';
		//*****************************************************************************
		$this->getResponse()->setContentType('application/json');
		$data_json = json_encode($data_array);
		return $this->renderText($data_json);
	}
  }

  public function executeGenerarSelectReporte()
  {
	$this->verificaPrilegio("BUSQUEDA_AVANZADA_REPORTAR_DINAMICO");
	$this->setLayout(false);
	$response_process = array('status' => 400, 'message' => 'Error interno del servidor');
	//***************************************************************************************************************
	$checkSelected = $this->getRequest()->getPostParameters();
	$this->getUser()->setAttribute('sess_checkSelected', $checkSelected, 'subscriber');
	//***************************************************************************************************************
	try
	{
		$checkSelected = $this->getRequest()->getPostParameters();
		if(empty($checkSelected['duallistbox_demo1']))
		{
			$this->getUser()->setFlash('error', 'Debe seleccionar al menos un campo para exportar.');
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		//***********************************************************************************************************
		// RECOGEMOS LA VARIABLE DE SESION 
		$clasename_main = $this->getUser()->getAttribute('sess_maintablename', '', 'subscriber');
		$peername_main = sprintf("%sPeer",$clasename_main);

		$c = $this->getUser()->getAttribute('sess_criteria', '', 'subscriber');
		//***********************************************************************************************************
		$tableMapMain = $peername_main::getTableMap();
		$fks_table = $tableMapMain->getForeignKeys();
		//***********************************************************************************************************
		$criteria = clone $c;
		$criteria->clearSelectColumns();
		$criteria->clearOrderByColumns();
		$criteria->setLimit(null);
		//***********************************************************************************************************
		$last_peer = null;
		$listphpnames = null;
		$listcolnames = null;
		$headers_cols = array();
		//***********************************************************************************************************
		foreach ($checkSelected['duallistbox_demo1'] as $key => $value)    
		{
			$partes = explode("_", str_replace('.', '_', $value));
			$className = $partes[1];
			$peer_name = sprintf("%sPeer",$className);
			$tableMap = $peer_name::getTableMap();
			$colsTable = $tableMap->getColumns();

			if($peer_name !== $last_peer || (empty($listphpnames) || empty($listcolnames)))
			{
				$listphpnames = $peer_name::getFieldNames(BasePeer::TYPE_PHPNAME);  
				$listcolnames = $peer_name::getFieldNames(BasePeer::TYPE_COLNAME);  
			}
			//*******************************************************************************************************
			$isNotNull = null;
			$relationTable = null;
			$relationColumn = null;
			$peerTableRealtion = null;
			foreach ($fks_table as $fk_row) 
			{
				$relationColumn1 = $fk_row->getRelatedColumnName();
				$isFkRelated = $colsTable[$fk_row->getRelatedColumnName()];
				//**************************************************************************************************
				if($isFkRelated != null){
					if(!empty($isFkRelated->getRelatedColumnName())){
						continue;
					}
				}
				//**************************************************************************************************
				$isExistCol = $colsTable[$fk_row->getRelatedColumnName()];
				if($isExistCol)
				{
					$isNotNull = $fk_row->isNotNull();
					$relationTable = $fk_row->getRelatedTableName();
					$relationColumn = $fk_row->getRelatedColumnName();
					break;
				}
			}
			//*******************************************************************************************************
			for ($i= 0; $i<count($listphpnames); $i++) 
			{
				if($listphpnames[$i] == $partes[2]) //si hay coincidencia, lo guarda en un array de cabezeras excel
				{
					if(!empty($relationTable))
					{
						$colUniqueName = sprintf("%s",strtoupper(str_replace(".","_",$listcolnames[$i])));

						if(!empty($isNotNull))
						{
							if($isNotNull)
								$criteria->addJoin(sprintf("%s.%s",$tableMapMain->getName(),$relationColumn),sprintf("%s.%s",$relationTable,$relationColumn),Criteria::INNER_JOIN);
							else
								$criteria->addJoin(sprintf("%s.%s",$tableMapMain->getName(),$relationColumn),sprintf("%s.%s",$relationTable,$relationColumn),Criteria::LEFT_JOIN);
							//****************************************************************************************
							$criteria->addAsColumn($colUniqueName,$listcolnames[$i]);
						}
					}else{
						$colUniqueName = sprintf("%s",strtoupper(str_replace(".","_",$listcolnames[$i])));
						$criteria->addAsColumn($colUniqueName,$listcolnames[$i]);
					}
					//************************************************************************************************
					if(!in_array($colUniqueName,$headers_cols)){
						$headers_cols[] =  $colUniqueName;
					}
					break;
				}
			}
			//********************************************************************************************************
			$last_peer = sprintf("%sPeer",$className);
		}
		//************************************************************************************************************
		$final_data_list = $peername_main::doSelectStmt($criteria);
		//************************************************************************************************************
		$writer = WriterEntityFactory::createXLSXWriter();
		//************************************************************************************************************
		$dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
		//************************************************************************************************************
		$filename = 'reporte_' . uniqid() . '_' . date('YmdGis') . '.xlsx';
		$tempPath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $dir_tmp . DIRECTORY_SEPARATOR . $filename;
		//************************************************************************************************************
		$writer->openToFile($tempPath);
		//************************************************************************************************************
		$headerStyle = (new StyleBuilder())
		->setFontBold()
		->setBackgroundColor(Color::LIGHT_BLUE)
		->build();
		//************************************************************************************************************
		$dataStyle = (new StyleBuilder())
		->setFontSize(10)
		->build();
		//************************************************************************************************************
		// Encabezados
		$headerRow = WriterEntityFactory::createRowFromArray($headers_cols, $headerStyle);
		$writer->addRow($headerRow);
		//************************************************************************************************************
		$batchRows = [];
		$count = 0;
		//************************************************************************************************************
		// Data rows 
		while($rowData = $final_data_list->fetch(PDO::FETCH_ASSOC))
		{
			$batchRows[] = WriterEntityFactory::createRowFromArray($rowData, $dataStyle);
			$count++;
			if ($count % 1000 == 0) 
			{
				$writer->addRows($batchRows);
				unset($batchRows);
				$batchRows = [];
			}
		}
		//************************************************************************************************************
		if (!empty($batchRows)) 
		{
			$writer->addRows($batchRows);
		}
		//************************************************************************************************************
		$writer->close();
		//************************************************************************************************************
        // Verificar que el archivo se cre� correctamente
        if (!file_exists($tempPath)) 
		{
			$response_process['message'] = 'Error al generar el archivo Excel';
			$this->getResponse()->setContentType('application/json');
			return $this->renderText(json_encode($response_process));
        }
		//************************************************************************************************************
		$publicUrl = sfConfig::get('publicUrl');
		if (substr($publicUrl, -1) !== '/') 
		{
    		$publicUrl .= '/';
		}
		//************************************************************************************************************
		$response_process['message'] = 'El reporte se genero correctamente';
		$response_process['status'] = 200;
		$response_process['url_download'] = $publicUrl . $dir_tmp . "/". $filename;
	}
	catch(PropelException $e)
	{
		$response_process['message'] = 'Ocurrio un error genrando los datos para el excel';
	}
	catch(\Exception $e)
	{
		$response_process['message'] = 'Ocurrio un error interno en la aplicacion';
	}
	catch(\Throwable $e)
	{
		$response_process['message'] = 'Ocurrio un error interno en el servidor';
	}
	//****************************************************************************************************************
	$this->getResponse()->setContentType('application/json');
	return $this->renderText(json_encode($response_process));
  }  
  
  public function executeList()
  {
  	$filtros_consulta = "&a=1";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$datos = '';
	$param_list = array();
	//**************************************************************************************************************
	$param_list['asunto'] = !empty($this->getRequestParameter('asunto')) ? trim($this->getRequestParameter('asunto')) : null;
	$param_list['codigo_barras'] = !empty($this->getRequestParameter('codigo_barras')) ? trim($this->getRequestParameter('codigo_barras')) : null;
	$param_list['entidad_origen'] = !empty($this->getRequestParameter('entidad_origen')) ? trim($this->getRequestParameter('entidad_origen')) : null;
	$param_list['funcionario_origen'] = !empty($this->getRequestParameter('funcionario_origen')) ? trim($this->getRequestParameter('funcionario_origen')) : null;
	$param_list['pnombre_interesado'] = !empty($this->getRequestParameter('pnombre_interesado')) ? trim($this->getRequestParameter('pnombre_interesado')) : null;
	$param_list['papellido_interesado'] = !empty($this->getRequestParameter('papellido_interesado')) ? trim($this->getRequestParameter('papellido_interesado')) : null;
	$param_list['nuid_interesado'] = !empty($this->getRequestParameter('nuid_interesado')) ? trim($this->getRequestParameter('nuid_interesado')) : null;
	//******************************************************************************************************************
	$this->list_data = true;
	$errors = array_filter($param_list);
	if (empty($errors)) {
		$this->list_data = false;
	}
	//******************************************************************************************************************
	$this->mensajeInternas = ConsultaPermisoHelper::MSG_SIN_REGISTROS;
	$this->mensajeEnviadas = ConsultaPermisoHelper::MSG_SIN_REGISTROS;
	$this->mensajeRecibidas = ConsultaPermisoHelper::MSG_SIN_REGISTROS;
	if(count($param_list)){
		//**************************************COMUNICACIONES INTERNAS*************************************************
		if(!empty($param_list['codigo_barras']) || !empty($param_list['asunto'])){
			$i = new Criteria();
			if(!empty($param_list['asunto'])){
				$i->add(ComInternaPeer::REFERENCIA, $param_list['asunto'].'%', Criteria::LIKE);
				$filtros_consulta .= "&asunto=".$param_list['asunto'];
			}

			if(!$this->getUser()->checkPerm('COM_INTERNA_LISTAR_TODAS', $usuariologuiado)){
				$i->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
				$i->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1,Criteria::NOT_EQUAL);
				$i->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);	
			}else{
				$i->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,1);
			}
			
			if(!empty($param_list['codigo_barras'])){
				$i->add(ComInternaPeer::RADICADO, $param_list['codigo_barras'].'%', Criteria::LIKE);
				$filtros_consulta .= "&codigo_barras=".$param_list['codigo_barras'];
			}
			//*********************************************************************************************************
			$i->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
			$i->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,array(1,4),Criteria::NOT_IN);
			$this->enviada =  0;
			//*********************************************************************************************************
			$i->setDistinct();
			$i->clearSelectColumns();
			$i->addSelectColumn(ComInternaPeer::COMINTERNA_ID);
			$i->addSelectColumn(ComInternaPeer::RADICADO);
			$i->addSelectColumn(ComInternaPeer::REFERENCIA);
			$i->addSelectColumn(ComInternaPeer::FECHA_CREACION);
			//*********************************************************************************************************
			$resulset =  ComInternaPeer::doSelectStmt($i);
			$this->list_internas =  $resulset->fetchAll();
			//*********************************************************************************************************
			if(empty($this->list_internas) && !empty($param_list['codigo_barras'])){
				$countSinPermiso = ComInternaPeer::doCount((new Criteria())->add(ComInternaPeer::RADICADO, $param_list['codigo_barras'].'%', Criteria::LIKE));
				$this->mensajeInternas = ConsultaPermisoHelper::mensajeListaVacia($countSinPermiso);
			}
		}else{
			$this->interna =  0;
			$this->list_internas =  null;
		}
		//************************************COMUNICACIONES ENVIADAS**************************************************
		if(!empty($param_list['codigo_barras']) || !empty($param_list['asunto']) || !empty($param_list['entidad_origen']) || !empty($param_list['funcionario_origen'])
		|| !empty($param_list['pnombre_interesado']) || !empty($param_list['papellido_interesado']) || !empty($param_list['nuid_interesado'])){
			$e = new Criteria();

			if(!empty($param_list['asunto'])){	  		 
				$e->add(ComEnviadaPeer::ASUNTO,$param_list['asunto'].'%',Criteria::LIKE);
				$filtros_consulta .= "&asunto=".$param_list['asunto'];
			}
			
			if(!empty($param_list['entidad_origen'])){
				$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaDirectorioPeer::COMENVIADA_ID);
				$e->addJoin(EnviadaDirectorioPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
				$e->add(DirectorioExternoPeer::NOMBRE,$param_list['entidad_origen'].'%',Criteria::LIKE);	 
				$e->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID,1);
				$filtros_consulta .= "&entidad=".$param_list['entidad_origen'];
			}
			
			if(!empty($param_list['funcionario_origen'])){
				$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaDirectorioPeer::COMENVIADA_ID);
				$e->addJoin(EnviadaDirectorioPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
				$e->add(DirectorioExternoPeer::FUNCIONARIO,$param_list['funcionario_origen'].'%',Criteria::LIKE);  
				$filtros_consulta .= "&funcionario=".$param_list['funcionario_origen'];
			}
			
			if(!empty($param_list['codigo_barras'])){
				$e->add(ComEnviadaPeer::RADICADO,$param_list['codigo_barras'].'%',Criteria::LIKE);
				$filtros_consulta .= "&codigo_barras=".$param_list['codigo_barras'];
			}
			
			$addjoin_interesado = true;
			if(!empty($param_list['pnombre_interesado'])){
				if($addjoin_interesado){
					$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaInteresadosPeer::COMENVIADA_ID);
					$e->addJoin(EnviadaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$addjoin_interesado = false;
				}
				//******************************************************************************************************
				//$e->add(InteresadosPeer::PRIMER_NOMBRE,'%'.$param_list['pnombre_interesado'].'%',Criteria::LIKE);  
				$e->add(InteresadosPeer::PRIMER_NOMBRE,$param_list['pnombre_interesado'].'%',Criteria::LIKE);  
				$filtros_consulta .= "&pnombre_interesado=".$param_list['pnombre_interesado'];
			}

			if(!empty($param_list['papellido_interesado'])){
				if($addjoin_interesado){
					$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaInteresadosPeer::COMENVIADA_ID);
					$e->addJoin(EnviadaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$addjoin_interesado = false;
				}
				//******************************************************************************************************
				//$e->add(InteresadosPeer::PRIMER_APELLIDO,'%'.$param_list['papellido_interesado'].'%',Criteria::LIKE);  
				$e->add(InteresadosPeer::PRIMER_APELLIDO,$param_list['papellido_interesado'].'%',Criteria::LIKE);  
				$filtros_consulta .= "&papellido_interesado=".$param_list['papellido_interesado'];
			}

			if(!empty($param_list['nuid_interesado'])){
				if($addjoin_interesado){
					$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaInteresadosPeer::COMENVIADA_ID);
					$e->addJoin(EnviadaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$addjoin_interesado = false;
				}
				//******************************************************************************************************
				//$e->add(InteresadosPeer::NUMERO_IDENTIFICACION,'%'.$param_list['nuid_interesado'].'%',Criteria::LIKE);
				$e->add(InteresadosPeer::NUMERO_IDENTIFICACION,$param_list['nuid_interesado'].'%',Criteria::LIKE);
				$filtros_consulta .= "&nuid_interesado=".$param_list['nuid_interesado'];
			}
			
			if(!$this->getUser()->checkPerm('COM_ENVIADA_LISTAR_TODAS', $usuariologuiado)){
				$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);	
				$e->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);	
				$e->addor(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,3);
				$e->addor(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);
				$e->addor(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,1);
			}else{
				$e->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,1);
			}
			//*********************************************************************************************************
			$e->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
			$e->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(1,4),Criteria::NOT_IN);
			$this->enviada =  0;
			//*********************************************************************************************************
			//$e->setDistinct();
			$e->setLimit($max_rows);
			$e->clearSelectColumns();
			$e->addSelectColumn(ComEnviadaPeer::COMENVIADA_ID);
			$e->addSelectColumn(ComEnviadaPeer::RADICADO);
			$e->addSelectColumn(ComEnviadaPeer::ASUNTO);
			$e->addSelectColumn(ComEnviadaPeer::FECHA_CREACION);
			//*********************************************************************************************************
			$resulset =  ComEnviadaPeer::doSelectStmt($e);
			$this->list_enviadas =  $resulset->fetchAll();
			//*********************************************************************************************************
			if(empty($this->list_enviadas) && !empty($param_list['codigo_barras'])){
				$countSinPermiso = ComEnviadaPeer::doCount((new Criteria())->add(ComEnviadaPeer::RADICADO, $param_list['codigo_barras'].'%', Criteria::LIKE));
				$this->mensajeEnviadas = ConsultaPermisoHelper::mensajeListaVacia($countSinPermiso);
			}
		}else{
			$this->enviada =  0;
			$this->list_enviadas =  null;
		}
		//************************************COMUNICACIONES RECIBIDA***************************************************
		if(!empty($param_list['codigo_barras']) || !empty($param_list['asunto']) || !empty($param_list['entidad_origen']) || !empty($param_list['funcionario_origen'])
		|| !empty($param_list['pnombre_interesado']) || !empty($param_list['papellido_interesado']) || !empty($param_list['nuid_interesado'])){
			$r = new Criteria();

			if(!empty($param_list['asunto'])){
				$r->add(ComRecibidaPeer::ASUNTO,$param_list['asunto'].'%',Criteria::LIKE);   	 
			}

			if(!empty($param_list['entidad_origen'])){	 
				$r->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);	 
				$r->add(DirectorioExternoPeer::NOMBRE,$param_list['entidad_origen'].'%',Criteria::LIKE);
				$filtros_consulta .= "&entidad=".$param_list['entidad_origen'];
			}
			
			if(!empty($param_list['funcionario_origen'])){	 
				$r->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
				$r->add(DirectorioExternoPeer::FUNCIONARIO,$param_list['funcionario_origen'].'%',Criteria::LIKE);
				$filtros_consulta .= "&funcionario=".$param_list['funcionario_origen'];
			}
			
			if(!empty($param_list['codigo_barras'])){
				$r->add(ComRecibidaPeer::RADICADO,$param_list['codigo_barras'].'%',Criteria::LIKE);
				$filtros_consulta .= "&codigo_barras=".$param_list['codigo_barras'];
			}

			$raddjoin_interesado = true;
			if(!empty($param_list['pnombre_interesado'])){
				if($raddjoin_interesado){
					$r->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
					$r->addJoin(ComrecibidaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$raddjoin_interesado = false;
				}
				//******************************************************************************************************
				$r->add(InteresadosPeer::PRIMER_NOMBRE,$param_list['pnombre_interesado'].'%',Criteria::LIKE);  
				$filtros_consulta .= "&pnombre_interesado=".$param_list['pnombre_interesado'];
			}

			if(!empty($param_list['papellido_interesado'])){
				if($raddjoin_interesado){
					$r->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
					$r->addJoin(ComrecibidaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$raddjoin_interesado = false;
				}
				//******************************************************************************************************
				//$r->add(InteresadosPeer::PRIMER_APELLIDO,'%'.$param_list['papellido_interesado'].'%',Criteria::LIKE);  
				$r->add(InteresadosPeer::PRIMER_APELLIDO,$param_list['papellido_interesado'].'%',Criteria::LIKE);  
				$filtros_consulta .= "&papellido_interesado=".$param_list['papellido_interesado'];
			}

			if(!empty($param_list['nuid_interesado'])){
				if($raddjoin_interesado){
					$r->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaInteresadosPeer::COMRECIBIDA_ID);
					$r->addJoin(ComrecibidaInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$raddjoin_interesado = false;
				}
				//******************************************************************************************************
				//$r->add(InteresadosPeer::NUMERO_IDENTIFICACION,'%'.$param_list['nuid_interesado'].'%',Criteria::LIKE);  
				$r->add(InteresadosPeer::NUMERO_IDENTIFICACION,$param_list['nuid_interesado'].'%',Criteria::LIKE);
				$filtros_consulta .= "&nuid_interesado=".$param_list['nuid_interesado'];
			}

			if(!$this->getUser()->checkPerm('COM_RECIBIDA_LISTAR_TODAS', $usuariologuiado)){
				$r->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
				$r->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
				$r->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
				$r->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);	
				$r->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
			}else{
				$r->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,1);
			}
			//*********************************************************************************************************
			$r->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
			$r->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,array(13,14),Criteria::NOT_IN);
			$this->recibida = 0;
			//*********************************************************************************************************
			//$r->setDistinct();
			$r->setLimit($max_rows);
			$r->clearSelectColumns();
			$r->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);
			$r->addSelectColumn(ComRecibidaPeer::RADICADO);
			$r->addSelectColumn(ComRecibidaPeer::ASUNTO);
			$r->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);
			//*********************************************************************************************************
			$resulset =  ComRecibidaPeer::doSelectStmt($r);
			$this->list_recibidas =  $resulset->fetchAll();
			//*********************************************************************************************************
			if(empty($this->list_recibidas) && !empty($param_list['codigo_barras'])){
				$countSinPermiso = ComRecibidaPeer::doCount((new Criteria())->add(ComRecibidaPeer::RADICADO, $param_list['codigo_barras'].'%', Criteria::LIKE));
				$this->mensajeRecibidas = ConsultaPermisoHelper::mensajeListaVacia($countSinPermiso);
			}
		}else{
			$this->recibida =  0;
			$this->list_recibidas =  null;
		}
		//********************************************ARCHIVO************************************************************
		if(!empty($param_list['codigo_barras']) || !empty($param_list['asunto']) || !empty($param_list['entidad_origen']) || !empty($param_list['funcionario_origen'])
		|| !empty($param_list['pnombre_interesado']) || !empty($param_list['papellido_interesado']) || !empty($param_list['nuid_interesado'])){
			
			$cbasic = new Criteria();
			$cbasic->setDistinct();
			$cbasic->setLimit($max_rows);

			$cbasic->setDistinct();
			if(!empty($param_list['asunto'])){
				$cbasic->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,Criteria::LEFT_JOIN);
				$cton0 = $cbasic->getNewCriterion(UnidadDocumentalPeer::TITULO,'%'.$param_list['asunto'].'%',Criteria::LIKE);
    			$cton1 = $cbasic->getNewCriterion(ContenidoUnidadDocumentalPeer::DESCRIPCION,'%'.$param_list['asunto'].'%',Criteria::LIKE);
				$cton0->addOr($cton1);
    			$cbasic->add($cton0);

				$filtros_consulta .= "&asunto=".$param_list['asunto'];		    	
			}

			if(!empty($param_list['codigo_barras'])){
				$cbasic->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,ContenidoUnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,Criteria::LEFT_JOIN);
				$cton3 = $cbasic->getNewCriterion(UnidadDocumentalPeer::CODIGO_BARRAS,'%'.$param_list['codigo_barras'].'%',Criteria::LIKE);
    			$cton4 = $cbasic->getNewCriterion(ContenidoUnidadDocumentalPeer::DESCRIPCION,'%'.$param_list['codigo_barras'].'%',Criteria::LIKE);
				$cton3->addOr($cton4);
    			$cbasic->add($cton3);

				$filtros_consulta .= "&codigo_barras=".$param_list['codigo_barras'];
			}

			$expaddjoin_interesado = true;
			if(!empty($param_list['pnombre_interesado'])){
				if($expaddjoin_interesado){
					$cbasic->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,UnidaddocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID);
					$cbasic->addJoin(UnidaddocumentalInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$expaddjoin_interesado = false;
				}
				//******************************************************************************************************
				//$cbasic->add(InteresadosPeer::PRIMER_NOMBRE,'%'.$param_list['pnombre_interesado'].'%',Criteria::LIKE);
				$cbasic->add(InteresadosPeer::PRIMER_NOMBRE,$param_list['pnombre_interesado'].'%',Criteria::LIKE);
				$filtros_consulta .= "&pnombre_interesado=".$param_list['pnombre_interesado'];
			}

			if(!empty($param_list['papellido_interesado'])){
				if($expaddjoin_interesado){
					$cbasic->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,UnidaddocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID);
					$cbasic->addJoin(UnidaddocumentalInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$expaddjoin_interesado = false;
				}
				//******************************************************************************************************
				//$cbasic->add(InteresadosPeer::PRIMER_APELLIDO,'%'.$param_list['papellido_interesado'].'%',Criteria::LIKE);
				$cbasic->add(InteresadosPeer::PRIMER_APELLIDO,$param_list['papellido_interesado'].'%',Criteria::LIKE);
				$filtros_consulta .= "&papellido_interesado=".$param_list['papellido_interesado'];
			}

			if(!empty($param_list['nuid_interesado'])){
				if($expaddjoin_interesado){
					$cbasic->addJoin(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID,UnidaddocumentalInteresadosPeer::UNIDADDOCUMENTAL_ID);
					$cbasic->addJoin(UnidaddocumentalInteresadosPeer::INTERESADO_ID,InteresadosPeer::INTERESADO_ID);
					$expaddjoin_interesado = false;
				}
				//******************************************************************************************************
				//$cbasic->add(InteresadosPeer::NUMERO_IDENTIFICACION,'%'.$param_list['nuid_interesado'].'%',Criteria::LIKE);  
				$cbasic->add(InteresadosPeer::NUMERO_IDENTIFICACION,$param_list['nuid_interesado'].'%',Criteria::LIKE);
				$filtros_consulta .= "&nuid_interesado=".$param_list['nuid_interesado'];
			}
			//****************************************ARCHIVO GESTION***************************************************
			$ag = clone $cbasic;
			$ag->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID,1);
			$this->gestion = 0;
			//**********************************************************************************************************
			$ag->addJoin(UnidadDocumentalPeer::SUBSERIE_ID,SubseriePeer::SUBSERIE_ID,Criteria::INNER_JOIN);
			$ag->clearSelectColumns();
			$ag->addSelectColumn(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			$ag->addSelectColumn(UnidadDocumentalPeer::CODIGO_BARRAS);
			$ag->addSelectColumn(UnidadDocumentalPeer::TITULO);
			$ag->addSelectColumn(SubseriePeer::DESCRIPCION);
			//**********************************************************************************************************
			$resulset =  UnidadDocumentalPeer::doSelectStmt($ag);
			$this->list_gestion =  $resulset->fetchAll();
			//***********************************ARCHIVO CENTRAL********************************************************
			$ac = clone $cbasic;
			$ac->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID,2);
			$this->central = 0;
			//**********************************************************************************************************
			$ac->addJoin(UnidadDocumentalPeer::SUBSERIE_ID,SubseriePeer::SUBSERIE_ID);
			$ac->clearSelectColumns();
			$ac->addSelectColumn(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			$ac->addSelectColumn(UnidadDocumentalPeer::CODIGO_BARRAS);
			$ac->addSelectColumn(UnidadDocumentalPeer::TITULO);
			$ac->addSelectColumn(SubseriePeer::DESCRIPCION);
			//**********************************************************************************************************
			$resulset2 =  UnidadDocumentalPeer::doSelectStmt($ac);
			$this->list_central =  $resulset2->fetchAll();
			//***************************************ARCHIVO HISTORICO**************************************************
			$ah = clone $cbasic;
			$ah->add(UnidadDocumentalPeer::LOCALIZACIONUNIDADDOCUMENTAL_ID,3);
			$this->historico = 0;
			//**********************************************************************************************************
			$ah->addJoin(UnidadDocumentalPeer::SUBSERIE_ID,SubseriePeer::SUBSERIE_ID);
			$ah->clearSelectColumns();
			$ah->addSelectColumn(UnidadDocumentalPeer::UNIDADDOCUMENTAL_ID);
			$ah->addSelectColumn(UnidadDocumentalPeer::CODIGO_BARRAS);
			$ah->addSelectColumn(UnidadDocumentalPeer::TITULO);
			$ah->addSelectColumn(SubseriePeer::DESCRIPCION);
			//**********************************************************************************************************
			$resulset_ah =  UnidadDocumentalPeer::doSelectStmt($ah);
			$this->list_historico =  $resulset_ah->fetchAll();
		}
		//**************************************************************************************************************
		$this->datos = 1;  	
		$this->asunto = $param_list['asunto'];
		$this->codigo_barras = $param_list['codigo_barras'];
		$this->entidad = $param_list['entidad_origen'];
		$this->funcionario = $param_list['funcionario_origen'];
		$this->pnombre_interesado = $param_list['pnombre_interesado'];
		$this->papellido_interesado = $param_list['papellido_interesado'];
		$this->nuid_interesado = $param_list['nuid_interesado'];
		$this->filtros = "";
		//**************************************************************************************************************
		foreach ($param_list as $key => $value) {
			if(!empty($value)){
				$this->filtros .= sprintf('&%s=%s',$key,$value);
			}
		}
  	}else{
		$this->datos   = $datos;
	}
  }
  
  public function executeConsulta()
  {
  	
  }
}