<?php

/**
 * Subclass for performing query and update operations on the 'COM_INTERNA' table.
 *
 * 
 *
 * @package lib.model
 */

use \PhpOffice\PhpWord\Settings;

class ComInternaPeer extends BaseComInternaPeer
{

    public static function getReporteComInterna($params)
    {
        try {
            $c = new Criteria();

            if (!empty($params['comint_radicado'])) {
                $c->add(ComInternaPeer::RADICADO, $params['comint_radicado']);
            }

            if (!empty($params['comint_asunto'])) {
                $c->add(ComInternaPeer::REFERENCIA, '%' . $params['comint_asunto'] . '%', Criteria::LIKE);
            }

            if (!empty($params['comint_periodo'])) {
                $c->add(ComInternaPeer::PERIODO_ID, $params['comint_periodo']);
            }

            if (!empty($params['comint_estado'])) {
                $c->add(ComInternaPeer::ESTADOCOMINTERNA_ID, $params['comint_estado']);
            }

            if (!empty($params['comint_remitente'])) {
                //$c->add(ComInternaPeer::??, $params['comint_remitente']);  // DE DONDE EL REMITENTE??
            }

            if (!empty($params['comint_fechCreaDesde']) && !empty($params['comint_fechCreaHasta'])) {
                $c->add(ComInternaPeer::FECHA_CREACION, $params['comint_fechCreaDesde'], Criteria::GREATER_EQUAL);
                $c->addAnd(ComInternaPeer::FECHA_CREACION, $params['comint_fechCreaHasta'], Criteria::LESS_EQUAL);
            }

            return $c;
        } catch (PropelException $ex) {
            return $ex->getMessage();
        } catch (\Exception $ex) {
            return $ex->getMessage();
        } catch (\Throwable $ex) {
            return $ex->getMessage();
        }
    }

    public static function updateAsignadoCom($cominterna_id, $rolus_id = 1, $isAsignado = 1)
    {
        try {
            $conexion = Propel::getConnection();
            $query = "UPDATE %s SET  %s = " . $isAsignado . " WHERE %s = " . $cominterna_id;
            $query = sprintf($query, CominternaUsuarioPeer::TABLE_NAME, CominternaUsuarioPeer::ESTA_ASIGNADA, CominternaUsuarioPeer::COMINTERNA_ID);
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
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

    public static function addUserRolByCom($cominterna_id, $usuario_id, $cusuario_id, $estadocominterna_id = 1, $rolus_id = 1, $isAsig = 1, $tprocomId = 0)
    {
        try {
            $comAsignar = new CominternaUsuario();
            $comAsignar->setCominternaId($cominterna_id);
            $comAsignar->setRolusuariocominternaid($rolus_id);
            $comAsignar->setEstadocominternaId($estadocominterna_id);
            $comAsignar->setCargousuarioId($cusuario_id);
            $comAsignar->setUsuarioId($usuario_id);
            $comAsignar->setEstaAsignada($isAsig);
            $comAsignar->setFechaAsigna(date("Y-m-d G:i:s"));
            $comAsignar->setWfEjecutado(0);
            $comAsignar->save();
            //**************************************************************************************
            if ($comAsignar->getPrimaryKey()) {
                return true;
            } else {
                return false;
            }
        } catch (PropelException $th) {
            return false;
        } catch (\Exception $th) {
            return false;
        } catch (\Throwable $x) {
            return false;
        }
    }

    public static function getConsecutivoRadicacion($regional_id, $dependencia_id)
    {
        $periodoActual = date("Y");
        $formaRad = ParametroPeer::retrieveByPk(4)->getCodigo();
        //************************************************************************************
        $conexion = Propel::getConnection();
        if ($formaRad == "REG") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where %s=" . $regional_id . "  and %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME, ComInternaPeer::REGIONAL_ID, ComInternaPeer::PERIODO_ID);
        } elseif ($formaRad == "DEP") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where %s=" . $dependencia_id . "  and %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME, ComInternaPeer::DEPENDENCIA_ID, ComInternaPeer::PERIODO_ID);
        } elseif ($formaRad == "GEN") {
            $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME, ComInternaPeer::PERIODO_ID);
        } elseif ($formaRad == "SEQ") {
            $consulta = "SELECT NEXT VALUE FOR [dbo].[GENERATE_CONS_COMINTERNA] AS max;";
        } else {
            $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=" . $periodoActual . " ";
            $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME, ComInternaPeer::PERIODO_ID);
        }
        //************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_BOTH);
        return ($resultset[0] + 1);
    }

    public static function getComObjectByRadicado($radicado, $periodo_id)
    {
        $c = new Criteria();
        $c->add(ComInternaPeer::RADICADO, $radicado);
        $c->add(ComInternaPeer::PERIODO_ID, $periodo_id);
        $com_object = ComInternaPeer::doSelectOne($c);
        //************************************************************************************
        if ($com_object != null) {
            return $com_object;
        } else {
            return null;
        }
    }

    public static function getObjectComByRadicadoOrId($radicado = null, $pkcom_id = null)
    {
        $object = null;
        //******************************************************************************
        if (!empty($pkcom_id) && is_numeric($pkcom_id)) {
            $object = ComInternaPeer::retrieveByPK($pkcom_id);
            if ($object != null) {
                return $object;
            }
        }
        //******************************************************************************
        if (!empty($radicado)) {
            $c = new Criteria();
            $c->add(ComInternaPeer::RADICADO, $radicado);
            $object = ComInternaPeer::doSelectOne($c);
        }
        //******************************************************************************
        return $object;
    }

    public static function initFolderDigit($regional_id, $periodo, $useTmp = false)
    {
        $dirRaiz    = ParametroPeer::retrieveByPk(25)->getValortexto();
        $dir_alias  = ParametroPeer::retrieveByPk(15)->getValortexto();
        $extensions = explode(";", ParametroPeer::retrieveByPK(31)->getValortexto());
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";
        //************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
        $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder . DIRECTORY_SEPARATOR . $regional_folder : $entidad_folder) : "";
        //************************************************************************************
        $directorio_digit = simad_util::NormalizePath($dirRaiz . DIRECTORY_SEPARATOR . $entidad_text . DIRECTORY_SEPARATOR . $dir_alias . DIRECTORY_SEPARATOR . $periodo);
        $folder_attach = simad_util::createPath($directorio_digit);
        $basic_path = $entidad_text . DIRECTORY_SEPARATOR . $dir_alias . DIRECTORY_SEPARATOR . $periodo;
        //************************************************************************************
        $array_url['basic_path'] = $basic_path;
        $array_url['full_path'] = $folder_attach;
        $array_url['temp_path'] = $dirTmp;
        $array_url['alias_web'] = "";
        //************************************************************************************
        return $array_url;
    }

    public static function initFolderAttach($regional_id, $usuario_id, $useTmp = false)
    {
        $dirRaiz    = ParametroPeer::retrieveByPk(25)->getValortexto();
        $dir_attach = ParametroPeer::retrieveByPk(15)->getValortexto();
        $alias_str  = ParametroPeer::retrieveByPk(30)->getValortexto();
        $dirTmp     = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";
        //************************************************************************************
        $regional = RegionalPeer::retrieveByPK($regional_id);
        $usuario_radica = UsuarioPeer::retrieveByPK($usuario_id);
        $username = ($usuario_radica->getUserName());
        //************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
        $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder . DIRECTORY_SEPARATOR . $regional_folder : $entidad_folder) : "";
        $directorio_attach = simad_util::NormalizePath($dirRaiz . $entidad_text . DIRECTORY_SEPARATOR . $dir_attach . DIRECTORY_SEPARATOR . $username);
        $directorio_tmp = simad_util::NormalizePath($dirRaiz . DIRECTORY_SEPARATOR . $entidad_folder . DIRECTORY_SEPARATOR . $dirTmp);
        $basic_path = $entidad_text . DIRECTORY_SEPARATOR . $dir_attach . DIRECTORY_SEPARATOR . $username;
        //************************************************************************************
        $folder_attach = simad_util::createPath($directorio_attach);
        $folder_tmp = $useTmp ? simad_util::createPath($directorio_tmp) : "";
        $alias_web = str_replace("\\", "/", $basic_path);
        //************************************************************************************
        $array_url['basic_path'] = $basic_path;
        $array_url['full_path'] = $folder_attach;
        $array_url['temp_path'] = $folder_tmp;
        $array_url['alias_web'] = $alias_str . $alias_web;
        //************************************************************************************
        return $array_url;
        //return $useTmp ? array('folder_attach'=>$folder_attach,'folder_tmp'=>$folder_tmp) : $folder_attach;
    }

    public static function readFileComCombined($inputFileName, $readHeaders = true, $deleteFile = false)
    {
        $upload_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        $sheetData = array();
        $cod_msg = 0;
        $msg_error = "";
        $process_end = 0;
        $rows_read = 10000;
        $array_data = array();
        //*************************************************************************************************************
        if (trim($inputFileName)) {
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                return null;
            }
            //*********************************************************************************************************
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            //*********************************************************************************************************
            $array_data['headerList'] = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE);
            //*********************************************************************************************************
            //$sheetData = $objPHPExcel->getSheet(0)->toArray(null,true,true,true);
            $sheetData = $sheet->rangeToArray('A2:' . $highestColumn . $highestRow, NULL, true, true);
            $sheetDataSerialize = simad_util::getArrayForSend($sheetData);
            $array_data['sheetData'] = $sheetData;
            $array_data['sheetDataSerialize'] = $sheetDataSerialize;
            $array_data['cod_msg'] = 0;
            $array_data['msg_error'] = "";
            //*********************************************************************************************************
            if (file_exists($inputFileName) && $deleteFile) {
                unlink($inputFileName);
            }
        } else {
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = "Debe seleccionar el archivo que contiene las registros para radicar";
        }
        //*************************************************************************************************************
        return $array_data;
    }

    public static function readPlantillaWordCom($inputFileName, $deleteFile = false)
    {
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/bootstrap.php';
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/autoload.inc.php';
        $domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/src');
        Settings::setPdfRenderer(Settings::PDF_RENDERER_DOMPDF, $domPdfPath);
        //*************************************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR;
        $directorio_upload = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        if (trim($inputFileName)) {
            try {
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($directorio_upload . DIRECTORY_SEPARATOR . '5c2e3c2bf3bca5630a13ec9ab7e67b0a.docx');
                echo $pathToSave = $directorio_tmp . DIRECTORY_SEPARATOR . md5(date("YmdGis"));
                $templateProcessor->saveAs($pathToSave . '.docx');
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
                return null;
            }
            //*********************************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        } else {
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = "Debe enviar un archivo";
        }
        //*************************************************************************************************************
        return $array_data;
    }

    public static function readPlantillaWordCom2($inputFileName, $deleteFile = false)
    {
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/bootstrap.php';
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/autoload.inc.php';
        $domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/src');
        Settings::setPdfRenderer(Settings::PDF_RENDERER_DOMPDF, $domPdfPath);
        //*************************************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //*************************************************************************************************************
        $upload_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR;
        $directorio_upload = simad_util::createPath($upload_dir);
        //*************************************************************************************************************
        if (trim($inputFileName)) {
            try {
                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                echo $pathToSave = $directorio_tmp . DIRECTORY_SEPARATOR . md5(date("YmdGis"));
                // Adding an empty Section to the document...
                $section = $phpWord->addSection();
                // Adding Text element to the Section having font styled by default...
                $section->addText(
                    '"Javier Fernando Charrry Learn from yesterday, live for today, hope for tomorrow. '
                        . 'The important thing is not to stop questioning." '
                        . '(Albert Einstein)'
                );
                // Adding Text element with font customized inline...
                $section->addText(
                    '"Great achievement is usually born of great sacrifice, '
                        . 'and is never the result of selfishness." '
                        . '(Napoleon Hill)',
                    array('name' => 'Tahoma', 'size' => 12)
                );
                // Saving the document as OOXML file...
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($pathToSave . '.docx');

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($pathToSave . '.docx');

                //Save it
                $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
                $phpWord2 = $objReader->load($directorio_upload . DIRECTORY_SEPARATOR . '5c2e3c2bf3bca5630a13ec9ab7e67b0a.docx');

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord2, 'Word2007');
                try {
                    $objWriter->save($directorio_upload . DIRECTORY_SEPARATOR . 'my.docx');
                } catch (Exception $ex2) {
                    echo $ex2->getMessage();
                    exit;
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
                return null;
            }
            //*********************************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        } else {
            $array_data['cod_msg'] = 1;
            $array_data['msg_error'] = "Debe enviar un archivo";
        }
        //*************************************************************************************************************
        return $array_data;
    }

    public static function initWorkflowCom(ComInterna $com_interna, $usuario_destino, $usuario_origen, $buzon = 2)
    {
        try {
            $wfinstancia_id = ComInternaPeer::createWF($com_interna->getPrimaryKey(), $usuario_destino, $com_interna->getTipocominternaId());
            if ($wfinstancia_id) {
                $wfinstanciabitacora_id = ComInternaPeer::createInstanciaBitacora($com_interna->getPrimaryKey(), $usuario_origen, $wfinstancia_id, $buzon);
                $myinwf = new wf_Interface();
                $com_interna->setInstancia($wfinstancia_id);
                $com_interna->setBitacora($wfinstanciabitacora_id);
                $com_interna->save();
                //**********************************************************************************
                $myinwf->envioEmail($com_interna->getPrimaryKey(), $usuario_destino, $buzon);
                //**********************************************************************************
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function createWF($cominterna_id, $usuario_destino, $tipo_com)
    {
        $c = new Criteria();
        $c->add(WfFlujoPeer::TIPOCOMINTERNA_ID, $tipo_com);
        $flujo = WfFlujoPeer::doSelectOne($c);
        //*************************************************************************
        if ($flujo) {
            //busca la primera transicion
            $c = new Criteria();
            $c->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo->getPrimaryKey());
            $c->add(WfActividadTransicionPeer::ES_DESTINO, 1);
            $c->add(WfActividadTransicionPeer::ORDEN, 1);
            $wf_actividad_transicion = WfActividadTransicionPeer::doSelectOne($c);
            if (!$wf_actividad_transicion) {
                return 0;
            }
            //*********************************************************************
            $wf_instancia = new WfInstancia();
            $wf_instancia->setUsuarioId($usuario_destino);
            $wf_instancia->setWfactividadtransicionId($wf_actividad_transicion->getPrimaryKey());
            $wf_instancia->setCominternaId($cominterna_id);
            $wf_instancia->setWfFlujoId($flujo->getPrimaryKey());
            $wf_instancia->setEstaAbierta(1);
            $wf_instancia->setFechaI(date("Y-m-d G:i:s"));
            $wf_instancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
            $wf_instancia->setObservaciones("WorkFlow radicado en " . date("Y-m-d G:i:s"));
            $wf_instancia->save();
            //*********************************************************************
            return $wf_instancia->getPrimaryKey();
        } else {
            return 0;
        }
    }

    public static function createInstanciaBitacora($cominterna_id, $usuario_origen, $wfinstancia_id, $buzon)
    {
        $wf_instancia = WfInstanciaPeer::retrieveByPk($wfinstancia_id);
        $wfactividad_id = $wf_instancia->getWfactividadtransicionId();
        $estadoActividad = "1";
        //****************************************************************************
        $wf_instancia_bitacora = new WfInstanciaBitacora();
        $wf_instancia_bitacora->setUsuarioId($usuario_origen);
        $wf_instancia_bitacora->setWfactividadtransicionId($wfactividad_id);
        $wf_instancia_bitacora->setWfinstanciaId($wfinstancia_id);
        $wf_instancia_bitacora->setCominternaId($cominterna_id);
        $wf_instancia_bitacora->setFechaI(date("Y-m-d G:i:s"));
        $wf_instancia_bitacora->setFechaF(date("Y-m-d G:i:s"));
        $wf_instancia_bitacora->setObservaciones("Radicado en " . date("Y-m-d G:i:s"));
        $wf_instancia_bitacora->setError("0");
        $wf_instancia_bitacora->setEsActual(0);
        $wf_instancia_bitacora->setFechaLimite(date("Y-m-d G:i:s"));
        $wf_instancia_bitacora->setBuzon($buzon);
        $wf_instancia_bitacora->setBuzonId($buzon);
        $wf_instancia_bitacora->setEstadoActividad($estadoActividad);
        $wf_instancia_bitacora->save();
        //****************************************************************************
        return $wf_instancia_bitacora->getPrimaryKey();
    }

    /**
     * objectActions::singBatchComInternas()
     * funcion para enviar a firmar una lista de comunicaciones internas
     * @param $ucominterna_list lista de objetos ComInterna de las comunicaciones para firmar
     * @return mixed resultado proceso array('isError' => true|false, 'message' => message)
     */
    public static function singBatchComInternas($ucominterna_list = array())
    {
        foreach ($ucominterna_list as $com_object) {
            try {
                $response_process = array('httpStatus' => 400, 'message' => 'Error interno del servidor');
                //********************************************************************************
                $response_process = $com_object->singDocumentProcess();
            } catch (PropelException $th) {
                $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
            } catch (Exception $th) {
                $response_process = array('httpStatus' => 400, 'message' => $th->getMessage());
            }
        }

        return $response_process;
    }

    /**
     * objectActions::getListComRadicarByUser()
     * genera una lista de comunicaciones pendientes por radicar de un usuario especifico
     * @param $usuario_id id del usuario que se desea consultar
     * @return mixed array lista con los resultados
     */
    public static function getListComRadicarByUser($usuario_id)
    {
        try {
            $estadocominterna_id = 1;
            $roluscominterna_id = 2;
            //*************************************************************************************************
            $c = new Criteria();
            $c->setDistinct();
            //$c->setLimit(100);
            //*************************************************************************************************
            $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, $roluscominterna_id);
            $c->add(CominternaUsuarioPeer::USUARIO_ID, $usuario_id);
            $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID, $estadocominterna_id);
            $c->add(ComInternaPeer::ESTADOCOMINTERNA_ID, $estadocominterna_id);
            $c->add(ComInternaPeer::MARCA, $usuario_id);
            $c->add(ComInternaPeer::PERIODO_ID, date("Y"));
            //$c->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
            $c->addDescendingOrderByColumn(ComInternaPeer::FECHA_CREACION);
            //*************************************************************************************************
            $c->addJoin(ComInternaPeer::COMINTERNA_ID, CominternaUsuarioPeer::COMINTERNA_ID);
            $c->clearSelectColumns();
            //*************************************************************************************************
            $c->addSelectColumn(ComInternaPeer::COMINTERNA_ID); //0
            $c->addSelectColumn(ComInternaPeer::RADICADO); //1
            $c->addSelectColumn(ComInternaPeer::REFERENCIA); //2
            $c->addSelectColumn(ComInternaPeer::FECHA_CREACION); //3
            $c->addSelectColumn(ComInternaPeer::MARCA); //4
            //*************************************************************************************************        
            $sql_custom = "(SELECT COUNT(" . CominternaUsuarioPeer::COMINTERNAUSUARIO_ID;
            $sql_custom .= ") FROM " . CominternaUsuarioPeer::TABLE_NAME;
            $sql_custom .= " WHERE " . CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID . " IN(5)";
            $sql_custom .= " AND " . CominternaUsuarioPeer::USUARIO_ID . " != " . $usuario_id;
            $sql_custom .= " AND " . CominternaUsuarioPeer::CHECK_APROBACION . " = 0";
            $sql_custom .= " AND " . ComInternaPeer::COMINTERNA_ID . " = " . CominternaUsuarioPeer::COMINTERNA_ID . ")";
            $c->addAsColumn('IS_RADICAR', $sql_custom);
            //*************************************************************************************************
            $list_cominternas = ComInternaPeer::doSelectStmt($c);
            return $list_cominternas->fetchAll();
        } catch (\PDOException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        }
    }
}
