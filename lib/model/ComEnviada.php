<?php

/**
 * Subclass for representing a row from the 'COM_ENVIADA' table.
 *
 * 
 *
 * @package lib.model
 */

include_once('lib/PHPOffice/PHPWord/bootstrap.php');

use \PhpOffice\PhpWord\Settings;
use \setasign\Fpdi\Fpdi;
use \setasign\Fpdi\FpdfWatermark;
use PhpOffice\PhpWord\TemplateProcessor;

class ComEnviada extends BaseComEnviada
{
    public function getPrefijoHtml()
    {
        return ($this->getPrefijo());
    }

    public function getDireccionHtml()
    {
        return ($this->getDireccionDestinatario());
    }

    public function getCargoHtml()
    {
        return ($this->getCargoDestinatario());
    }

    public function getFuncionarioHtml()
    {
        return ($this->getFuncionarioDestino());
    }

    public function getAsuntoHtml()
    {
        return ($this->getAsunto());
    }

    public function getPathImageDigitByCom()
    {
        try {
            $digitDocumentFile = "";
            $dirRaiz = ParametroPeer::retrieveByPk(29)->getValortexto();
            $dir_adj_object  = ParametroPeer::retrieveByPk(13)->getValortexto();
            //$alias_com_object  = ParametroPeer::retrieveByPk(30)->getValortexto();
            $extensions = explode(";", ParametroPeer::retrieveByPk(31)->getValortexto());
            $periodo_com =  $this->getPeriodoId();
            //******************************************************************************************
            $directorio_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : $dirRaiz;
            $entidad_text = trim($this->getRegional()->getEntidad()->getDirectorioName());
            $regional_text = trim($this->getRegional()->getDirectorioName());
            $entidad_text = empty($entidad_text) ? "" : (empty($regional_text) ?  $entidad_text : $entidad_text . '/' . $regional_text);
            $directorio_entidad = empty($entidad_text) ? $directorio_raiz : $directorio_raiz . $entidad_text . "/";
            $directorio_com = $dir_adj_object . "/" . $periodo_com . "/";
            $directorio_final = $directorio_entidad . $directorio_com;
            $file_name = $this->getRadicado();
            //******************************************************************************************			
            foreach ($extensions as $format) {
                if (file_exists($directorio_final . $file_name . '.' . $format)) {
                    $digitDocumentFile = $directorio_final . $file_name . '.' . $format;
                    break;
                }
            }
        } catch (Exception $th) {
            $digitDocumentFile = null;
        }
        //**********************************************************************************************
        return $digitDocumentFile;
    }

    public function getUriImageDigitById()
    {
        $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
        $base_web = sfConfig::get('base_simad');
        //**********************************************************************************************
        try {
            $file_name = $this->getRadicado();
            $digitDocumentFile = $this->getPathImageDigitByCom();
            $existe_file = empty($digitDocumentFile) ? false : true;
            //******************************************************************************************
            if ($existe_file) {
                $extension = pathinfo($digitDocumentFile, PATHINFO_EXTENSION);
                $filename_tmp = md5($file_name . time()) . '.' . $extension;
                $pathtmp = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $filename_tmp;
                if (copy($digitDocumentFile, $pathtmp)) {
                    $url_viewer = $base_web . '/tmp/' . $filename_tmp;
                    if (strtolower($extension) == 'pdf') {
                        $url_viewer = $base_web . '/viewerEx.php?fileview=' . $filename_tmp;
                        if ($this->getEstadocomenviadaId() == 4) {
                            $tanulado = "DOCUMENTO ANULADO";
                            $fanulado = $this->getFechaDeAnulacion();
                            $pdfTools = new PdfTools();
                            $fnewTmp = $pdfTools->setPdfWatherMark($pathtmp, $tanulado, $fanulado);
                            //$url_viewer .= '&qvars='.md5($filename_tmp.'anulado');
                            $url_viewer = $base_web . '/viewerEx.php?fileview=' . $fnewTmp;
                        }
                        //*******************************************************************************
                        $response_process = array('status' => 200, 'message' => 'Archivo generado y enviado para visualización', 'url_file' => $url_viewer);
                    } else {
                        $response_process = array('status' => 200, 'message' => 'Archivo generado y enviado para visualización', 'url_file' => $url_viewer);
                    }
                } else {
                    $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
                }
            } else {
                $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
            }
        } catch (PropelException $th) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
        } catch (Exception $th) {
            //$response_process['message'] = 'Ocurrio un error interno en el servidor';
        }
        //************************************************************************************************
        return $response_process;
    }

    public function getUsuariosListCom()
    {
        $list_users = array();
        $copias_list = array();
        $firmas_list = array();
        $revisa_list = array();
        $gestiona_list = array();
        foreach ($this->getEnviadaUsuarios() as $usuario_com) {
            if ($usuario_com->getRoluscomenviadaId() == 1) {
                $list_users['radicador'] = $usuario_com->getUsuario()->getNombreAll();
                $list_users['dependencia_radicador'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
            } elseif ($usuario_com->getRoluscomenviadaId() == 2) {
                $firmas_list[] = $usuario_com->getUsuario()->getNombreAll();
            } elseif ($usuario_com->getRoluscomenviadaId() == 3) {
                $copias_list[] = $usuario_com->getUsuario()->getNombreAll();
            } elseif ($usuario_com->getRoluscomenviadaId() == 4) {
                $revisa_list[] = $usuario_com->getUsuario()->getNombreAll();
            } elseif ($usuario_com->getRoluscomenviadaId() == 5) {
                $gestiona_list[] = $usuario_com->getUsuario()->getNombreAll();
            }
            //**************************************************************************
            $list_users['firmas'] = implode(",", $firmas_list);
            $list_users['copias'] = implode(",", $copias_list);
            $list_users['revisores'] = implode(",", $copias_list);
            $list_users['gestores'] = implode(",", $copias_list);
        }
        //******************************************************************************
        return $list_users;
    }

    public function getUsuariosListComObjs()
    {
        $list_users = array();
        $copias_list = array();
        $firmas_list = array();
        $revisa_list = array();
        $gestiona_list = array();
        //******************************************************************************
        try {
            foreach ($this->getEnviadaUsuarios() as $usuario_com) {
                if ($usuario_com->getRoluscomenviadaId() == 1) //radicador
                {
                    $list_users['nombre_radicador'] = $usuario_com->getUsuario()->getNombreAll();
                    $list_users['area_uradicador'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
                    $list_users['cargo_uradicador'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $list_users['fmecanica_uradicador'] = $usuario_com->getUsuario()->getFirmaElectronica();
                } elseif ($usuario_com->getRoluscomenviadaId() == 2) //firmas
                {
                    $usobject = array();
                    $usobject['ufirma_mecanica'] = "";
                    //******************************************************************
                    if ($usuario_com->getUsuario()->getUseFirmaElectronica()) {
                        $usobject['ufirma_mecanica'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    }
                    //******************************************************************
                    $usobject['nombre_ufirma'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_ufirma'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_ufirma'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_ufirma'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $firmas_list[] = $usobject;
                } elseif ($usuario_com->getRoluscomenviadaId() == 3) //copias
                {
                    $usobject = array();
                    $usobject['nombre_copia'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_ucopia'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_ucopia'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_ucopia'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $copias_list[] = $usobject;
                } elseif ($usuario_com->getRoluscomenviadaId() == 4) //revisores
                {
                    $usobject = array();
                    $usobject['nombre_revisor'] = $usuario_com->getUsuario()->getNombreAll();
                    $usobject['cargo_urevisor'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    $usobject['area_urevisor'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_urevisor'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $usobject['fmecanica_urevisor'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    $revisa_list[] = $usobject;
                } elseif ($usuario_com->getRoluscomenviadaId() == 5) //gestores
                {
                    $usobject = array();
                    $usobject['nombre_gestor'] = $usuario_com->getUsuario()->getNombreAll();

                    if ($usuario_com->getCargousuarioId()) {
                        $usobject['cargo_ugestor'] = $usuario_com->getCargoUsuario()->getCargo()->getDescripcion();
                    } else {
                        $cusuario_obj = CargoUsuarioPeer::getCargoUsuarioByIdUser($usuario_com->getUsuarioId(), true);
                        $usobject['cargo_ugestor'] = !empty($cusuario_obj) ? $cusuario_obj->getCargo()->getDescripcion() : "";
                    }

                    $usobject['cargo_ugestor'] = $usuario_com->getCargousuarioId() ?: "";
                    $usobject['area_ugestor'] = $usuario_com->getUsuario()->getDependencia()->getNombre();
                    $usobject['regional_ugestor'] = $usuario_com->getUsuario()->getRegional()->getDescripcion();
                    $usobject['fmecanica_ugestor'] = $usuario_com->getUsuario()->getFirmaElectronica();
                    $gestiona_list[] = $usobject;
                }
            }
        } catch (PropelException $th) {
            //throw $th;
        } catch (Exception $th) {
            //throw $th;
        }
        //******************************************************************************
        $list_users['firmas'] = $firmas_list;
        $list_users['copias'] = $copias_list;
        $list_users['revisores'] = $revisa_list;
        $list_users['gestores'] = $gestiona_list;
        //******************************************************************************
        return $list_users;
    }

    public function getBitacoraHistCom()
    {
        $list_users = array();
        $copias_list = array();
        $evento_list = array();
        foreach ($this->getEnviadaUsuarios() as $usuario_com) {
            $evento = array();
            $evento['destinatario'] = $usuario_com->getUsuario()->getNombreAll();
            $evento['dependencia'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
            $evento['fecha_recibido'] = $usuario_com->getFechaAsigna();
            $evento['fecha_lectura'] = $usuario_com->getFechaAcceso();
            $evento['proceso'] = $usuario_com->getTipoprocesocomId() ? $usuario_com->getTipoProcesoCom()->getDescripcion() : "";
            $evento_list[] = $evento;
            //**************************************************************************
            if ($usuario_com->getRoluscomenviadaId() == 1) {
                $list_users['radicador'] = $usuario_com->getUsuario()->getNombreAll();
                $list_users['dependencia_radicador'] = $usuario_com->getUsuario()->getDependencia()->getNombreCustom();
            } elseif ($usuario_com->getRoluscomenviadaId() == 2) {
                if ($usuario_com->getEstaAsignada()) {
                    $list_users['destinatario_actual'] = $usuario_com->getUsuario()->getNombreAll();
                    $list_users['dependencia_actual'] = $usuario_com->getUsuario()->getDependenciaId();
                    $list_users['nuid_usuario_actual'] = $usuario_com->getUsuario()->getCedula();
                }
            } else {
                $copias_list[] = $usuario_com->getUsuario()->getNombreAll();
            }
        }
        //******************************************************************************
        $list_users['copias'] = implode(",", $copias_list);
        $list_users['eventos_list'] = $evento_list;
        //******************************************************************************
        return $list_users;
    }

    public function getUsuariosListComIds($isArray = false)
    {
        $list_users = array();
        $copias_list = array();
        $firmas_list = array();
        foreach ($this->getEnviadaUsuarios() as $usuario_com) {
            if ($usuario_com->getRoluscomenviadaId() == 1) {
                $list_users['radicador'] = $usuario_com->getUsuarioId();
            } elseif ($usuario_com->getRoluscomenviadaId() == 2) {
                $firmas_list[] = $usuario_com->getUsuarioId();
            } elseif ($usuario_com->getRoluscomenviadaId() == 3) {
                $copias_list[] = $usuario_com->getUsuarioId();
            }
            //**************************************************************************
            $list_users['firmas'] = $isArray ? $firmas_list : implode(",", $firmas_list);
            $list_users['copias'] = $isArray ? $copias_list : implode(",", $copias_list);
        }
        //******************************************************************************
        return $list_users;
    }

    public function getRadicadoFormat($entidad_id, $regional_id, $depen_codigo = null)
    {
        $entidad_object = trim($entidad_id) ? EntidadPeer::retrieveByPk(trim($entidad_id)) : null;
        //****************************************************************************************
        if ($entidad_object != null) {
            $entidad_code = trim($entidad_object->getCodigo()) ? trim($entidad_object->getCodigo()) . "-" : "";
        }
        //****************************************************************************************
        $numero_radicado = ComEnviadaPeer::getNumeroRadicacion($regional_id, $this->getDependenciaId(), $this->getPeriodoId());
        $num_consecutivo = sprintf("%07d", $numero_radicado);
        $this->setNumeroRadicacion($numero_radicado);
        //****************************************************************************************
        //$radicado = $regional_cod."-".$depen_codigo."-".$num_consecutivo."-".date("Y")."-I";
        //$radicado = $regional_cod."-".$depen_codigo."-".$num_consecutivo."-"."I-".date("Y");
        //$radicado = sprintf("%s-%s-%s-1",date("Y"),$depen_codigo,$num_consecutivo);
        $radicado = sprintf("%s-%s-1", date("Y"), $num_consecutivo);
        //****************************************************************************************
        return $radicado;
    }

    public function getBasicUrlDigitCom($dir_raiz, $digit_com)
    {
        $array_url = array();
        //*****************************************************************************************
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //*****************************CREANDO ESTRUCTURA DE DIRECTORIOS***************************
        $folder_entidad = trim($this->getRegional()->getEntidad()->getDirectorioName());
        $folder_regional = trim($this->getRegional()->getDirectorioName());
        //*****************************************************************************************
        $periodo = $this->getPeriodoId();
        $entidad_text = $folder_entidad ? $folder_entidad : "";
        $entidad_text = $entidad_text ? ($folder_regional ? $entidad_text . DIRECTORY_SEPARATOR . $folder_regional : $entidad_text) : $folder_regional;
        $basic_path = $dir_raiz . $entidad_text . DIRECTORY_SEPARATOR . $digit_com . DIRECTORY_SEPARATOR . $periodo;
        //*****************************************************************************************
        $basic_path = preg_replace("#/+#", DIRECTORY_SEPARATOR, $basic_path);
        $array_url['storage_path'] = simad_util::createPath($basic_path);
        //*****************************************************************************************
        return $array_url;
    }

    public function getListDigitDocument($include_anexos = true)
    {
        $list_attach = array();
        //*****************************************************************************************
        if ($include_anexos) {
            $files_adjuntos = preg_split("/[,]+/", $this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
            //*****************************CREANDO LISTA DE ARCHIVOS*******************************
            for ($j = 0; $j < count($files_adjuntos); $j++) {
                $size_file = 0;
                if (trim($files_adjuntos[$j])) {
                    $filename = basename($files_adjuntos[$j]);
                    $xguid = simad_util::create_guid($filename);
                    //*****************************************************************************
                    $fpath_resolve = simad_paths_app::resolveRelativePath($files_adjuntos[$j]);
                    if (file_exists($fpath_resolve)) {
                        $size_file = filesize($fpath_resolve);
                    }
                    //*****************************************************************************
                    $url_secure = $this->getUrlTokenViewImageByObject($xguid);
                    $list_attach[] = array(
                        'GUID' => $xguid,
                        'NOMBRE_ARCHIVO' => $filename,
                        'TIPO_ATTACHMENT' => 'ANEXO',
                        'URL' => $url_secure,
                        'SIZE_FILE' => $size_file
                    );
                }
            }
        }
        //******************************************************************************************
        $mimetypes = explode(";", ParametroPeer::retrieveByPK(31)->getValortexto());
        //******************************************************************************************
        $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : ParametroPeer::retrieveByPk(29)->getValortexto();
        $digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
        $storage_com = $this->getBasicUrlDigitCom($dir_raiz, $digit_dir);
        $filedigit = false;
        foreach ($mimetypes as $format) {
            $filename = sprintf("%s.%s", trim($this->getRadicado()), $format);
            $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($targetpath)) {
                $size_file = filesize($targetpath);
                $xguid = simad_util::create_guid($filename);
                $url_secure = $this->getUrlTokenViewImageByObject($xguid);
                $url_download = $this->getUrlTokenDownloadImageByObject($xguid);
                $list_attach[] = array(
                    'GUID' => $xguid,
                    'NOMBRE_ARCHIVO' => $filename,
                    'TIPO_ATTACHMENT' => 'DIGIT_COM',
                    'URL' => $url_secure,
                    'URL_DOWNLOAD' => $url_download,
                    'SIZE_FILE' => $size_file,
                );
                $filedigit = true;
                break;
            }
        }
        //*******************************************************************************************
        if (!$filedigit) {
            $xguid = simad_util::create_guid($this->getRadicado());
            $format = "pdf";
            $filename = sprintf("%s.%s", trim($this->getRadicado()), $format);
            $url_secure = $this->getUrlTokenViewImageByObject($xguid, true);
            $url_download = $this->getUrlTokenDownloadImageByObject($xguid, true);
            $list_attach[] = array('GUID' => $xguid, 'NOMBRE_ARCHIVO' => $filename, 'TIPO_ATTACHMENT' => 'DIGIT_COM', 'URL' => $url_secure, 'URL_DOWNLOAD' => $url_download);
        }
        //*******************************************************************************************
        return $list_attach;
    }

    public function getDigitDocumentByXguid($search_xguid)
    {
        try {
            $files_adjuntos = preg_split("/[,]+/", $this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
            //*****************************CREANDO LISTA DE ARCHIVOS***********************************
            for ($j = 0; $j < count($files_adjuntos); $j++) {
                if (trim($files_adjuntos[$j])) {
                    $filename = basename(trim($files_adjuntos[$j]));
                    $xguid = simad_util::create_guid($filename);
                    if ($search_xguid == $xguid) {
                        return array('NOMBRE_ARCHIVO' => $filename, 'URL' => trim($files_adjuntos[$j]), 'TIPO_ATTACHMENT' => 'ANEXO');
                    }
                }
            }
            //******************************************************************************************
            $mimetypes = explode(";", ParametroPeer::retrieveByPK(31)->getValortexto());
            //******************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : ParametroPeer::retrieveByPk(29)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
            $storage_com = $this->getBasicUrlDigitCom($dir_raiz, $digit_dir);
            foreach ($mimetypes as $format) {
                $filename = sprintf("%s.%s", trim($this->getRadicado()), $format);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($targetpath)) {
                    $xguid = simad_util::create_guid($filename);
                    if ($search_xguid == $xguid) {
                        return array('NOMBRE_ARCHIVO' => $filename, 'URL' => trim($targetpath), 'TIPO_ATTACHMENT' => 'DIGIT_COM');
                    }
                }
            }
            //*******************************************************************************************
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        } catch (Exception $ex) {
            return array('NOMBRE_ARCHIVO' => null, 'URL' => null);
        }
    }

    function getUrlTokenViewImageByObject($xguid, $digitNew = false)
    {
        try {
            if ($this == null) {
                return null;
            }
            //*****************************************************************************************
            $time = time();
            $token_base = hash("sha512", $this->getPrimaryKey() . $this->getFechaCreacion() . $time);
            $mitad = strlen($token_base) / 2;
            $parte1 = substr($token_base, 0, $mitad);
            $parte2 = substr($token_base, $mitad);
            $token = $parte1 . urlencode($time) . $parte2;
            //*****************************************************************************************
            $base_path = sfConfig::get('base_simad');
            $url_query = array();
            $url_query['tipocom'] = 1;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;
            if ($digitNew) {
                $url_query['nvar'] = md5("newgenpdf");
            }
            //*****************************************************************************************
            //$url_abs = urlencode($base_path."recibida.php/com_recibida/viewImage?key_id=".$this->getPrimaryKey()."&vtoken=".$token."&xguid=".$xguid);
            //$url_abs = $base_path."viewImage.php?".http_build_query($url_query);
            $url_abs = "http://" . $_SERVER["HTTP_HOST"] . "/viewImage.php?" . http_build_query($url_query);
            return ($url_abs);
        } catch (Exception $ex) {
            return null;
        }
    }

    function getUrlTokenDownloadImageByObject($xguid, $digitNew = false)
    {
        try {
            if ($this == null) {
                return null;
            }
            //*****************************************************************************************
            $time = time();
            $token_base = hash("sha512", $this->getPrimaryKey() . $this->getFechaCreacion() . $time);
            $mitad = strlen($token_base) / 2;
            $parte1 = substr($token_base, 0, $mitad);
            $parte2 = substr($token_base, $mitad);
            $token = $parte1 . urlencode($time) . $parte2;
            //*****************************************************************************************
            $base_path = sfConfig::get('base_simad');
            $url_query = array();
            $url_query['tipocom'] = 1;
            $url_query['key_id'] = $this->getPrimaryKey();
            $url_query['vtoken'] = $token;
            $url_query['xguid'] = $xguid;
            $url_query['attachment'] = md5('attachment' . $this->getRadicado());
            if ($digitNew) {
                $url_query['nvar'] = md5("newgenpdf");
            }
            $url_abs = "http://" . $_SERVER["HTTP_HOST"] . "/viewImage.php?" . http_build_query($url_query);
            return ($url_abs);
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getRutaAdjuntos($files_new, $is_files_old = false, $files_old_text = "")
    {
        $url_files = "";
        /********************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_name    = sfContext::getInstance()->getUser()->getAttribute('username', '', 'subscriber');
        $dirRaiz         = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_object      = ParametroPeer::retrieveByPk(13)->getValortexto();
        $alias_object    = ParametroPeer::retrieveByPk(30)->getValortexto();
        $dirTmp          = ParametroPeer::retrieveByPk(65)->getValortexto();
        /********************************************************************************/
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder . '/' . $regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object . '/' . ($usuario_name) . "/";
        $directorio_tmp = ComEnviada::NormalizePath($dirRaiz . $entidad_folder . DIRECTORY_SEPARATOR . $dirTmp . DIRECTORY_SEPARATOR);
        $directorio_final = ComEnviada::NormalizePath($directorio_entidad . '/' . $directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text . "/" . $directorio_com;
        /********************************************************************************/
        if ($is_files_old) {
            $files_adjuntos = preg_split("/[,]+/", $files_old_text, -1, PREG_SPLIT_NO_EMPTY);
            $files_text = preg_split("/[,]+/", $files_new, -1, PREG_SPLIT_NO_EMPTY);
            for ($j = 0; $j < count($files_text); $j++) {
                $url = ComEnviada::strpos_array($files_text[$j], $files_adjuntos);
                if (!is_null($url)) {
                    $url_files .= $url . ",";
                } else {
                    $file_name = basename($files_text[$j]);
                    $filenamesource = $directorio_tmp . $file_name;
                    $filenametarget = $directorio_final . $file_name;
                    if (file_exists($filenamesource)) {
                        $directorio_final = ComEnviada::createPath($directorio_final);
                        copy($filenamesource, $filenametarget);
                        unlink($filenamesource);
                        $url_files .= $dirextorio_alias . $file_name . ",";
                    }
                }
            }
        } else {
            $files_adjuntos = preg_split("/[,]+/", $files_new, -1, PREG_SPLIT_NO_EMPTY);
            for ($j = 0; $j <= count($files_adjuntos); $j++) {
                if (trim($files_adjuntos[$j])) {
                    $file_name = basename($files_adjuntos[$j]);
                    $filenamesource = $directorio_tmp . basename($files_adjuntos[$j]);
                    $filenametarget = $directorio_final . basename($files_adjuntos[$j]);
                    if (file_exists($filenamesource)) {
                        $directorio_final = ComEnviada::createPath($directorio_final);
                        copy($filenamesource, $filenametarget);
                        unlink($filenamesource);
                        $url_files .= $dirextorio_alias . $file_name . ",";
                    }
                }
            }
        }
        return $url_files;
    }

    /**
     * objectActions::radicaComBatch()
     * funcion que permite radicar una comunicacion externa enviada que se encuentra en estado borrador
     * @return bool retorna true|false
     */
    public function radicaComBatch()
    {
        try {
            $firmante_id = EnviadaUsuarioPeer::getFirstUsurioFirma($this->getPrimaryKey());
            if (!$firmante_id) {
                $firmante_id = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
            }
            //$objUsuarioFirmante =  UsuarioPeer::retrieveByPk($firmante_id);
            //*************************************************************************************************
            $depen_codigo =  $this->getDependencia()->getCodigo();
            $dependencia = $this->getDependenciaId();
            $regional = $this->getRegionalId();
            $entidad_id = $this->getRegional()->getEntidadId();
            //*************************************************************************************************
            $radicado = $this->getRadicadoFormat($entidad_id, $regional, $depen_codigo);
            if ($this->getEstadocomenviadaId() == 1) {
                $this->setRadicado($radicado);
            } else {
                return false;
            }
            //*************************************************************************************************
            $estadocomenviada_id = 2;
            $this->setEstadocomenviadaid($estadocomenviada_id);
            $this->setFechaCreacion(date("Y-m-d G:i:s"));
            $this->setRegionalId($regional);
            $this->setDependenciaId($dependencia);
            $this->save();
            //*************************************************************************************************
            EnviadaUsuarioPeer::updateEstados($this->getPrimaryKey());
            EnviadaUsuarioPeer::updateAproFirmaAll($this->getPrimaryKey());
            //*************************************************************************************************
            if (!empty($this->getExpedienteId()) && !empty($this->getTipoDocumentalCod())) {
                $origentransfer_id = 3;
                $this->addNewTransferenciaAndContenido($origentransfer_id, $firmante_id);
                //FALTA CODIGO PARA CREAR LA TRANSFERENCIA CUANDO SE MARCA RADICADO POR INTERESADO
            }
            //*************************************************************************************************
            $list_com = array();
            $list_comdirdestino = EnviadaDirectorioPeer::getListEnviadaDirByRol($this->getPrimaryKey());
            //*************************************************************************************************
            if (count($list_comdirdestino) > 1) {
                $response_create = $this->addComByDirectorioExt($list_comdirdestino, $firmante_id, $entidad_id, $regional, $depen_codigo);
                if ($response_create['isError'] == false) {
                    $list_com = $response_create['list_com'];
                }
            }
            //*************************************************************************************************
            if ($this->getConsecutivoResp()) {
                $this->setResponseComOrigen($firmante_id);
            }
            //*************************************************************************************************
            $this->initUserNotifications($list_com);
            //*************************************************************************************************
            return true;
        } catch (PropelException $px) {
            return false;
        } catch (Throwable $th) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * objectActions::addNewTransferenciaAuto()
     * funcion para crear una transferencia automatica para casos de vincular automaticamente la respuesta al expediente
     * @return mixed resultado proceso array('isError' => true|false, 'isError' => true|false)
     * @estadotrans_id mixed estado de la transferencia 1=pendiente;2=aceptada;3=rechazada, por defecto aceptada
     */
    public function addNewTransferenciaAuto($contenidodoc_id, $origentransfer_id, $usuariosolicita_id)
    {
        try {
            $contenido_documental = ContenidoUnidadDocumentalPeer::retrieveByPK($contenidodoc_id);
            if ($contenido_documental == null) {
                return null;
            }
            //***************************************************************************************************
            $localizacionexp_id = $contenido_documental->getUnidadDocumental()->getLocalizacionunidaddocumentalId();
            $fecha_acepta = null;
            $observaciones = "Transferencia automatica desde otros modulos";
            //***************************************************************************************************
            if ($localizacionexp_id == 1) {
                $destinotransferencia_id = 1;
                $fecha_acepta = date('Y-m-d G:i:s');
                $estadotrans_id = 2;
            } elseif ($localizacionexp_id == 2) {
                $destinotransferencia_id = 2;
                $estadotrans_id = 1;
            } else {
                $destinotransferencia_id = 3;
                $estadotrans_id = 1;
            }
            //***************************************************************************************************
            if ($contenido_documental != null) {
                $data = array();
                $data['comenviada_id'] = $this->getPrimaryKey();
                $data['estadotrans_id'] = $estadotrans_id;
                $data['unidaddocumental_id'] = $contenido_documental->getUnidaddocumentalId();
                $data['origentransferencia_id'] = $origentransfer_id;
                $data['tipodocumental_id'] = $contenido_documental->getTipodocumentalId();
                $data['destinotransferencia_id'] = $destinotransferencia_id;
                $data['fecha_acepta'] = $fecha_acepta;
                $data['observaciones'] = $observaciones;
                //***********************************************************************************************
                $transferencia = TransferenciaPeer::createDefaultTransfer($data);
                //***********************************************************************************************
                if ($transferencia != null) {
                    $transferencia_user = TransferenciaPeer::createUserTransferencia($transferencia, $usuariosolicita_id, 1);
                    if ($transferencia_user == null) {
                        $transferencia->delete();
                        return null;
                    }
                    //*******************************************************************************************
                    $transferencia->transferirGestion($usuariosolicita_id);
                    //*******************************************************************************************
                    if ($transferencia->getDestinotransferenciaId() == 1) {
                        TransferenciaPeer::createUserTransferencia($transferencia, $usuariosolicita_id);
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

    /**
     * objectActions::setResponseComByComRecibidaId()
     * funcion asociar la respuesta enviada con la comunicacion recibida
     * @param int $comrecibida_id id de la comunicacion recibida a la cual se asociará la respuesta
     * @param int $estado_respondida estado final de la comunicacion entrante por defecto es 5 = responida
     * @param int $usuario_id id del usuario que asocia la comunicacion de respuesta
     * @param bool $addComResp indica si se debe adicionar un nuevo registro en la tabla de respuestas de la com_recibida
     * @return bool resultado true|false
     */
    public function setResponseComByComRecibidaId($comrecibida_id, $estado_respondida = 5, $usuario_id = null, $addComResp = true)
    {
        $usuariologuiado = $usuario_id == null ? sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber') : $usuario_id;
        //*******************************************************************************************************************
        try {
            if (!empty($comrecibida_id)) {
                $com_recibida_resp = ComRecibidaPeer::retrieveByPk($comrecibida_id);
                $com_recibida_resp->setEstadocomrecibidaId($estado_respondida);
                $com_recibida_resp->setComenviadaId($this->getPrimaryKey());
                $com_recibida_resp->save();
                //***********************************************************************************************************
                ComRecibidaPeer::updateEstadosComRecibida($com_recibida_resp->getPrimaryKey(), $estado_respondida);
                //***********************************************************************************************************
                if ($addComResp === true) {
                    $resp_valid = ComrecibidaRespuestaPeer::validarComRecibidaRespuesta($com_recibida_resp->getPrimaryKey(), $this->getPrimaryKey());
                    if ($resp_valid['status'] == 200) {
                        $comadd_resp = ComrecibidaRespuestaPeer::addComRecibidaRespuesta($com_recibida_resp->getPrimaryKey(), $this->getPrimaryKey(), $usuariologuiado);
                        if ($comadd_resp['status'] == 200) {
                            return true;
                        }
                    }
                }
                //***********************************************************************************************************
                if (!empty($com_recibida_resp->getContenidodocId()) && empty($this->getContenidodocId())) {
                    $origentransfer_id = 3;
                    $contenidodoc_id = $com_recibida_resp->getContenidodocId();
                    $this->addNewTransferenciaAuto($contenidodoc_id, $origentransfer_id, $usuariologuiado);
                    //FALTA CODIGO PARA CREAR LA TRANSFERENCIA CUANDO SE MARCA RADICADO POR INTERESADO
                }
                //***********************************************************************************************************
                return true;
            } else {
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

    /**
     * objectActions::setResponseComOrigen()
     * funcion asociar la respuesta enviada con la comunicacion recibida y archivar la respuesta en el expediente
     * @param int $firmante_id id del primer usuario que firma la comunicacion saliente
     * @param int $estado_respondida estado final de la comunicacion entrante por defecto es 5 = responida
     * @return bool resultado true|false
     * @estadotrans_id mixed estado de la transferencia 1=pendiente;2=aceptada;3=rechazada, por defecto aceptada
     */
    public function setResponseComOrigen($firmante_id, $estado_respondida = 5)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //******************************************************************************************************************
        try {
            if (!empty($this->getConsecutivoResp())) {
                $com_recibida_resp = ComRecibidaPeer::retrieveByPk($this->getConsecutivoResp());
                $estadocomrec_original = $com_recibida_resp->getEstadocomrecibidaId();
                //**********************************************************************************************************
                $com_recibida_resp->setEstadocomrecibidaId($estado_respondida);
                $com_recibida_resp->setComenviadaId($this->getPrimaryKey());
                $com_recibida_resp->save();
                //***********************************************************************************************************
                $resp_valid = ComrecibidaRespuestaPeer::validarComRecibidaRespuesta($com_recibida_resp->getPrimaryKey(), $this->getPrimaryKey());
                //***********************************************************************************************************
                if ($resp_valid['status'] == 200) {
                    try {
                        $comrec_resp = new ComrecibidaRespuesta();
                        $comrec_resp->setComrecibidaId($this->getConsecutivoResp());
                        $comrec_resp->setComenviadaId($this->getPrimaryKey());
                        $comrec_resp->setUsuarioId($usuariologuiado);
                        $comrec_resp->setFechaCreacion(date("Y-m-d G:i:s"));
                        $comrec_resp->save();
                    } catch (PropelException $th) {
                        $com_recibida_resp->setEstadocomrecibidaId($estadocomrec_original);
                        $com_recibida_resp->setComenviadaId(null);
                        $com_recibida_resp->save();
                        return false;
                    } catch (\Exception $th) {
                        $com_recibida_resp->setEstadocomrecibidaId($estadocomrec_original);
                        $com_recibida_resp->setComenviadaId(null);
                        $com_recibida_resp->save();
                        return false;
                    }
                }
                //***********************************************************************************************************
                ComRecibidaPeer::updateEstadosComRecibida($com_recibida_resp->getPrimaryKey(), $estado_respondida);
                //***********************************************************************************************************
                if (!empty($com_recibida_resp->getContenidodocId()) && empty($this->getContenidodocId())) {
                    $origentransfer_id = 3;
                    $contenidodoc_id = $com_recibida_resp->getContenidodocId();
                    $this->addNewTransferenciaAuto($contenidodoc_id, $origentransfer_id, $firmante_id);
                    //FALTA CODIGO PARA CREAR LA TRANSFERENCIA CUANDO SE MARCA RADICADO POR INTERESADO
                }
                //***********************************************************************************************************
                return true;
            }
        } catch (PropelException $th) {
            return false;
        } catch (Exception $th) {
            return false;
        } catch (Throwable $th) {
            return false;
        }
    }

    /**
     * objectActions::addNewTransferenciaAndContenido()
     * funcion para crear una transferencia automatica, crea el contenido documental
     * @return mixed resultado proceso array('isError' => true|false, 'message' => message)
     */
    public function addNewTransferenciaAndContenido($origentransfer_id, $usuariosolicita_id)
    {
        try {
            $tipodocumental_id = $this->getTipoDocumentalCod();
            $unidaddocumental_id = $this->getExpedienteId();
            $unidad_documental = UnidadDocumentalPeer::retrieveByPK($unidaddocumental_id);
            //***************************************************************************************************
            $localizacionexp_id = $unidad_documental->getLocalizacionunidaddocumentalId();
            $fecha_acepta = null;
            $observaciones = "Transferencia automatica desde comunicaciones";
            //***************************************************************************************************
            if ($localizacionexp_id == 1) {
                $destinotransferencia_id = 1;
                $fecha_acepta = date('Y-m-d G:i:s');
                $estadotrans_id = 2;
            } elseif ($localizacionexp_id == 2) {
                $destinotransferencia_id = 2;
                $estadotrans_id = 1;
            } else {
                $destinotransferencia_id = 3;
                $estadotrans_id = 1;
            }
            //***************************************************************************************************
            if ($unidad_documental != null) {
                $data = array();
                $data['comenviada_id'] = $this->getPrimaryKey();
                $data['estadotrans_id'] = $estadotrans_id;
                $data['unidaddocumental_id'] = $unidad_documental->getPrimaryKey();
                $data['origentransferencia_id'] = $origentransfer_id;
                $data['tipodocumental_id'] = $tipodocumental_id;
                $data['destinotransferencia_id'] = $destinotransferencia_id;
                $data['fecha_acepta'] = $fecha_acepta;
                $data['observaciones'] = $observaciones;
                //***********************************************************************************************
                $transferencia = TransferenciaPeer::createDefaultTransfer($data);
                //***********************************************************************************************
                if ($transferencia != null) {
                    $transferencia_user = TransferenciaPeer::createUserTransferencia($transferencia, $usuariosolicita_id);
                    if ($transferencia_user == null) {
                        $transferencia->delete();
                        return null;
                    }
                    //*******************************************************************************************
                    $transferencia->transferirGestion($usuariosolicita_id);
                    return array('isError' => false, 'message' => 'Comunicación archivda correctamente');
                } else {
                    return array('isError' => true, 'message' => 'Ocurrio un error y no se pudo transferir la comunicación');
                }
            } else {
                return array('isError' => true, 'message' => 'El expediente no es valido, se puede transferir la comunicación');
            }
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::addNewAttachDocument()
     * funcion para adjuntar un nuevo archivo al registro de la comunicacion
     * @return mixed resultado proceso array('isError' => true|false, list_files => array('filename' => $file, 'info' => xxx, 'isError' => true|false))
     * @files_new mixed lista con ruta de los arhivos adjuntos
     * @userid_attach int usuario id que adjunta el documento
     */
    public function addNewAttachDocument($files_new = array(), $userid_attach = null)
    {
        $current_ruta = !empty($this->getRuta()) ? trim($this->getRuta()) : "";
        $current_files = preg_split("/[,]+/", $current_ruta, -1, PREG_SPLIT_NO_EMPTY);
        //********************************************************************************
        $rolu_proyecta = 1;
        $usuario_attach = !empty($userid_attach) ? UsuarioPeer::retrieveByPK($userid_attach) : null;
        $usuario = empty($usuario_attach) ? UsuarioPeer::retrieveByPK($this->getUserIdComObjectByRol($rolu_proyecta)) : $usuario_attach;
        $usuario_name    = $usuario->getUserName();
        //********************************************************************************
        $simad_util   = new simad_util();
        $dirRaiz      = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_object   = ParametroPeer::retrieveByPk(13)->getValortexto();
        $alias_object = ParametroPeer::retrieveByPk(30)->getValortexto();
        //$dirTmp       = ParametroPeer::retrieveByPk(65)->getValortexto();
        //********************************************************************************
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder . '/' . $regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object . '/' . ($usuario_name);
        //$directorio_tmp = ComEnviada::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dirTmp.DIRECTORY_SEPARATOR);
        $directorio_final = ComEnviada::NormalizePath($directorio_entidad . '/' . $directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text . "/" . $directorio_com;
        //********************************************************************************
        $isError = false;
        $list_state = array();
        //********************************************************************************
        if (count($files_new)) {
            foreach ($files_new as $nfile) {
                try {
                    if (!empty($nfile)) {
                        $info_file = new SplFileInfo($nfile);
                        $filename_new = uniqid() . '_' . $simad_util->clean_name_fileinfo($info_file);
                        //*********************************************************************
                        $path_source = $info_file->getRealPath();
                        if (file_exists($path_source)) {
                            $path_target = simad_util::createPath($directorio_final) . DIRECTORY_SEPARATOR . $filename_new;
                            if (copy($path_source, $path_target)) {
                                $current_files[] = $dirextorio_alias . "/" . $filename_new;
                                $list_state[] = array('filename' => $nfile, 'info' => 'El archivo se adjunto correctamente', 'isError' => false);
                                unlink($path_source);
                            } else {
                                $isError = true;
                                $list_state[] = array('filename' => $nfile, 'info' => 'No fue posible copiar el archivo en la carpeta final');
                            }
                        } else {
                            $isError = true;
                            $list_state[] = array('filename' => $nfile, 'info' => 'el archivo no se encontro en el servidor', 'isError' => true);
                        }
                    } else {
                        $isError = true;
                        $list_state[] = array('filename' => $nfile ? $nfile : 'null', 'info' => 'nombre del archivo no es valido o es nulo', 'isError' => true);
                    }
                } catch (Exception $ex) {
                    $isError = true;
                    $list_state[] = array('filename' => $nfile, 'info' => $ex->getMessage(), 'isError' => true);
                }
            }
        } else {
            $isError = true;
        }
        //********************************************************************************
        $this->setRuta(implode(",", $current_files));
        $this->save();
        //********************************************************************************
        return array('isError' => $isError, 'list_state' => $list_state);
    }

    /**
     * objectActions::getUserIdComObjectByRol()
     * funcion que devuelve el o los ids de los usuarios asignados a la comunicacion 
     * @return mixed or single id
     * @rol_id parametro que especifica el usuario con el rol a retornar, el valor cero retorna todos los roles
     */
    public function getUserIdComObjectByRol($rol_id = 0)
    {
        $index = 0;
        $users_id = array();
        $object_usuarios = $this->getEnviadaUsuarios();
        foreach ($object_usuarios as $object) {
            if ($object->getRoluscomenviadaId() == $rol_id) {
                return $object->getUsuarioId();
            } elseif ($rol_id == 0) {
                $users_id[$index] = $object->getUsuarioId();
                $index++;
            }
        }
        return $users_id;
    }

    /**
     * objectActions::getNuidComObjectByRol()
     * funcion que devuelve la o las cedulas de los usuarios asignados a la comunicación 
     * @return mixed or single id
     * @rol_id parametro que especifica el usuario con el rol a retornar, el valor cero retorna todos los roles
     */
    public function getNuidComObjectByRol($rol_id = 0, $limit = -1)
    {
        $index = 0;
        $list = array();
        $object_usuarios = $this->getEnviadaUsuarios();
        foreach ($object_usuarios as $object) {
            if ($object->getRoluscomenviadaId() == $rol_id) {
                if ($limit == 1) {
                    return $object->getUsuario()->getCedula();
                } else {
                    $list[] = $object->getUsuario()->getCedula();
                }
            } elseif ($rol_id == 0) {
                $list[$index] = $object->getUsuario()->getCedula();
                $index++;
            }
        }
        return $list;
    }

    public static function moveFilesWordTempalteCreate($files_new, $folder_compose = "", $files_old_text = "")
    {
        $url_files = "";
        /********************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_name    = sfContext::getInstance()->getUser()->getAttribute('username', '', 'subscriber');
        $dirRaiz         = ParametroPeer::retrieveByPk(29)->getValortexto();
        $dir_object      = ParametroPeer::retrieveByPk(13)->getValortexto();
        $alias_object    = ""; //ParametroPeer::retrieveByPk(30)->getValortexto();
        $dirTmp          = ParametroPeer::retrieveByPk(65)->getValortexto();
        /********************************************************************************/
        $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
        $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
        $regional_folder = $usuario->getRegional()->getDirectorioName();
        $entidad_text = $entidad_folder . '/' . $regional_folder;
        $directorio_entidad = $dirRaiz . $entidad_text;
        $directorio_com = $dir_object . '/' . $folder_compose . "/";
        $directorio_tmp = ComEnviada::NormalizePath($dirRaiz . $entidad_folder . DIRECTORY_SEPARATOR . $dirTmp . DIRECTORY_SEPARATOR);
        $directorio_final = ComEnviada::NormalizePath($directorio_entidad . '/' . $directorio_com);
        $dirextorio_alias = $alias_object . $entidad_text . '/' . $directorio_com;
        /********************************************************************************/
        if (trim($files_old_text)) {
            $files_adjuntos = preg_split("/[,]+/", $files_old_text, -1, PREG_SPLIT_NO_EMPTY);
            $files_text = preg_split("/[,]+/", $files_new, -1, PREG_SPLIT_NO_EMPTY);
            for ($j = 0; $j < count($files_text); $j++) {
                $url = ComEnviada::strpos_array($files_text[$j], $files_adjuntos);
                if (!is_null($url)) {
                    $url_files .= $url . ",";
                } else {
                    $file_name = basename($files_text[$j]);
                    $filenamesource = $directorio_tmp . $file_name;
                    $filenametarget = $directorio_final . $file_name;
                    if (file_exists($filenamesource)) {
                        $directorio_final = ComEnviada::createPath($directorio_final);
                        copy($filenamesource, $filenametarget);
                        unlink($filenamesource);
                        $url_files .= $dirextorio_alias . $file_name . ",";
                    }
                }
            }
        } else {
            $files_adjuntos = preg_split("/[,]+/", $files_new, -1, PREG_SPLIT_NO_EMPTY);
            for ($j = 0; $j < count($files_adjuntos); $j++) {
                if (trim($files_adjuntos[$j])) {
                    $file_name = basename($files_adjuntos[$j]);
                    $filenamesource = $directorio_tmp . $file_name;
                    $filenametarget = $directorio_final . $file_name;
                    if (file_exists($filenamesource)) {
                        $directorio_final = ComEnviada::createPath($directorio_final);
                        copy($filenamesource, $filenametarget);
                        unlink($filenamesource);
                        $url_files .= $dirextorio_alias . $file_name . ",";
                    }
                }
            }
        }
        return $url_files;
    }

    public static function strpos_array($haystack, $needles)
    {
        if (is_array($needles)) {
            foreach ($needles as $str) {
                if (is_array($str)) {
                    $pos = ComEnviada::strpos_array($haystack, $str);
                } else {
                    $pos = strpos($haystack, basename($str));
                }
                if ($pos !== FALSE) {
                    return $str;
                }
            }
        } else {
            return strpos($haystack, basename($needles));
        }
    }

    public static function NormalizePath($path)
    {
        return simad_util::NormalizePath($path);
    }

    public static function createPath($cadena)
    {
        return simad_util::createPath($cadena);
    }

    public function genPdfByPlantillaCom()
    {
        require_once(sfConfig::get('sf_lib_dir') . "/BarcodeGenerator/generate_barcode.php");
        //***************************************************************************************************
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_solicita  = UsuarioPeer::retrieveByPK($usuariologuiado);
        //************************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR**********************
        $base_path = sfConfig::get('base_simad');
        $path_tmp = sfConfig::get('sf_web_dir') . "/com_html/com_enviada/"; //directorio temporal para guardar los archivos a convertir a pdf
        if (!is_dir($path_tmp)) { //verificar si el directorio existe de lo contrario se crea
            try {
                @mkdir(($path_tmp), 0766, true); //crea el directorio destiono
            } catch (Exception $e) {
                echo 'Excepción: ',  $e->getMessage(), "\n";
            }
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey() . ".php"; //nombre del archivo temporal que contiene los datos a convertir
        $path_plantilla = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "templates";
        $name_plantilla = $this->getPlantillasCom()->getNombre();
        $fullpath = $path_plantilla . DIRECTORY_SEPARATOR . $name_plantilla;
        //**************************************************************************************************
        if (file_exists($path_tmp . $nomb_file_html)) { //verificar si ya esta generado el archivo a convertir
            unlink($path_tmp . $nomb_file_html); //se elimina para actualizar el contenido del documento
        }
        $pt = fopen($path_tmp . $nomb_file_html, 'w'); //se crea el archivo a convertir
        //**************************************************************************************************
        $template_contents = $this->setMergeFiledsCom();
        //$template_contents = str_replace($patrones, $sustituciones, $plantilla_contents);
        //********************************************GUARDAR LA PLANTILLA TEMPORAL*************************
        // Guarda el RTF generado
        simad_util::createPath($path_tmp);
        file_put_contents($path_tmp . DIRECTORY_SEPARATOR . $nomb_file_html, $template_contents);
        //**************************************************************************************************
        return $nomb_file_html;
    }

    public function genPdfByPlantillaComOld()
    {
        require_once(sfConfig::get('sf_lib_dir') . "/BarcodeGenerator/generate_barcode.php");
        //***************************************************************************************************
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario_solicita  = UsuarioPeer::retrieveByPK($usuariologuiado);
        //************************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR**********************
        $base_path = sfConfig::get('base_simad');
        $path_tmp = sfConfig::get('sf_web_dir') . "/com_html/com_enviada/"; //directorio temporal para guardar los archivos a convertir a pdf
        if (!is_dir($path_tmp)) { //verificar si el directorio existe de lo contrario se crea
            try {
                @mkdir(($path_tmp), 0766, true); //crea el directorio destiono
            } catch (Exception $e) {
                echo 'Excepción: ',  $e->getMessage(), "\n";
            }
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey() . ".php"; //nombre del archivo temporal que contiene los datos a convertir
        $path_plantilla = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "templates";
        $name_plantilla = $this->getPlantillasCom()->getNombre();
        $fullpath = $path_plantilla . DIRECTORY_SEPARATOR . $name_plantilla;
        //**************************************************************************************************
        if (file_exists($path_tmp . $nomb_file_html)) { //verificar si ya esta generado el archivo a convertir
            unlink($path_tmp . $nomb_file_html); //se elimina para actualizar el contenido del documento
        }
        $pt = fopen($path_tmp . $nomb_file_html, 'w'); //se crea el archivo a convertir
        //**************************************************************************************************
        //firmantes
        $cf = new Criteria();
        $cf->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cf->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 2);
        $list_firmas = EnviadaUsuarioPeer::doSelect($cf);
        //**************************************************************************************************
        //firmantes
        $cp = new Criteria();
        $cp->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cp->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 1);
        $usuario_proyecta = EnviadaUsuarioPeer::doSelectOne($cp);
        //**************************************************************************************************
        //destinatario
        $cd = new Criteria();
        $cd->add(EnviadaDirectorioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cd->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, 1);
        $comenviada_directorio = EnviadaDirectorioPeer::doSelectOne($cd);
        //**************************************************************************************************
        $com_recibida  = ComRecibidaPeer::retrieveByPK($this->getConsecutivoResp());
        //**************************************************************************************************
        $plantilla_contents = file_get_contents($fullpath);
        //**************************************PATRONES PARA REMPLAZAR LOS VALORES*************************
        $patrones = array();
        $patrones[0]  = '{{$ciudad_carta}}';
        $patrones[1]  = '{{$codigo_barras_radicado}}';
        $patrones[2]  = '{{$radicado_salida}}';
        $patrones[3]  = '{{$fecha_carta}}';
        $patrones[4]  = '{{$prefijo_destinatario}}';
        $patrones[5]  = '{{$destinatario}}';
        $patrones[6]  = '{{$direccion_destinatario}}';
        $patrones[7]  = '{{$municipio_destinatario}}';
        $patrones[8]  = '{{$departamento_destinatario}}';
        $patrones[9]  = '{{$radicado_salida}}';
        $patrones[10]  = '{{$telefono_destinatario}}';
        $patrones[11]  = '{{$asunto_comunicacion}}';
        $patrones[12]  = '{{$radicado_entrada}}';
        $patrones[13]  = '{{$identificacion_destinatario}}';
        $patrones[14]  = '{{$contenido_text_merge}}';
        $patrones[15]  = '{{$firmas_respuesta}}';
        $patrones[16]  = '{{$usuario_creador}}';
        //**************************************************************************************************
        $ciudad_carta = $this->getCiudad()->getNombre();
        setlocale(LC_TIME, "spanish");
        $fecha_carta = strftime("%d de %B de %Y", strtotime($this->getFechaCreacion()));
        //**************************************************************************************************
        $text_radicado = trim($this->getRadicado());
        /*$sticker_dir = sfConfig::get('sf_web_dir');
        $font_dir = sfConfig::get('sf_lib_dir') . "/BarcodeGenerator/class/font/ariblk.ttf";
        $filename = $text_radicado . '.png';
        $filepath = $sticker_dir . "/tmp/" . $filename;
        GenerateCode($filepath,null,$font_dir,1,25,8);
        $text_radicado = "<img src=\"".$base_path."/tmp/$filename\"/>";*/
        //**************************************************************************************************            
        $sustituciones    = array();
        $sustituciones[0] = ($ciudad_carta);
        $sustituciones[1] = $text_radicado;
        $sustituciones[2] = $this->getRadicado();
        $sustituciones[3] = $fecha_carta;
        $sustituciones[4] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getPrefijo()) : "";
        $sustituciones[5] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getNombre()) : "";
        $sustituciones[6] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getDireccion()) : "";
        $sustituciones[7] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getCiudad()->getNombre()) : "";
        $sustituciones[8] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getCiudad()->getDepartamento()) : "";
        $sustituciones[9] = $this->getRadicado();
        $sustituciones[10] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getTelefono()) : "";
        $sustituciones[11] = trim($this->getAsunto());
        $sustituciones[12] = $com_recibida != null ? $com_recibida->getRadicado() : "";
        $sustituciones[13] = $comenviada_directorio ? trim($comenviada_directorio->getDirectorioExterno()->getNit()) : "";
        $sustituciones[14] = $this->getCombinarContenido($com_recibida);
        //**************************************************************************************************
        $firma_nombres = array();
        $firma_cargos = array();
        $firma_area = array();
        $firma_mecanica = array();
        foreach ($list_firmas as $item_firma) {
            if (trim($this->getFirmaElectronica())) {
                if (trim($item_firma->getUsuario()->getFirmaElectronica())) {
                    $firma_mecanica[] = '<img src="' . trim($item_firma->getUsuario()->getFirmaElectronica()) . '" min-height="80px" max-height="150px" width="250px">';
                } else {
                    $firma_mecanica[] = '&nbsp;';
                }
            } else {
                $firma_mecanica[] = '&nbsp;';
            }
            //**********************************************************************************************
            $firma_nombres[] =  $item_firma->getUsuario()->getNombreApellido();
            $firma_cargos[] = ($item_firma->getCargoUsuario()->getCargo()->getDescripcion());
            $firma_area[] = $item_firma->getUsuario()->getDependencia()->getNombre();
        }
        //**************************************************************************************************
        $dataFirmas['firma_nombres'] = $firma_nombres;
        $dataFirmas['firma_cargos'] = $firma_cargos;
        $dataFirmas['firma_areas'] = $firma_area;
        $dataFirmas['firma_mecanica'] = $firma_mecanica;
        //**************************************************************************************************    
        $firmas = '<table border="0" cellspacing="1" cellpadding="1" width="99%" align="left">';
        $usuario_firmas = array();
        $cargos_firmas = array();
        $areas_firmas = array();
        $mecanica_firmas = array();
        $contFirmas = 0;
        $contCopias = 0;
        $contCargos = 0;
        //**************************************************************************************************
        foreach ($dataFirmas['firma_nombres'] as $row_firma) {
            if ($contFirmas > 0) {
                if ($contFirmas % 2 == 0) {
                    //print_r($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas, false);
                    $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
                    $usuario_firmas = array();
                    $cargos_firmas = array();
                    $areas_firmas = array();
                    $mecanica_firmas = array();
                    $firmas .= $this->getDataEmptyRowFirmas(1);
                }
            }
            //**********************************************************************************************
            if ($dataFirmas['firma_mecanica'][$contFirmas] != null) {
                $mecanica_firmas[] = $dataFirmas['firma_mecanica'][$contFirmas];
            }
            //**********************************************************************************************
            $usuario_firmas[] = '<td><b>' . $row_firma . '</b></td>';
            $cargos_firmas[] = $dataFirmas['firma_cargos'][$contFirmas];
            $areas_firmas[] = $dataFirmas['firma_areas'][$contFirmas];
            $contFirmas++;
        }
        //**************************************************************************************************
        if (count($cargos_firmas)) {
            $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas, false);
            $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
            $usuario_firmas = array();
            $cargos_firmas = array();
            $areas_firmas = array();
            $mecanica_firmas = array();
        }
        //**************************************************************************************************
        $firmas .= '</table>';
        $sustituciones[15] = $firmas;
        $sustituciones[16] = $usuario_proyecta->getUsuario()->getNombreApellido();
        //*****************************************REMPLAZAR DATOS *****************************************
        $template_contents = str_replace($patrones, $sustituciones, $plantilla_contents);
        //********************************************GUARDAR LA PLANTILLA TEMPORAL*************************
        // Guarda el RTF generado
        simad_util::createPath($path_tmp);
        file_put_contents($path_tmp . DIRECTORY_SEPARATOR . $nomb_file_html, $template_contents);
        //**************************************************************************************************
        return $nomb_file_html;
    }

    public function generateFilePdf($isFirmaDigital = false)
    {
        //*********************MANEJO PARA CRAACION DIRECTORIO Y ARCHIVO A CONVERTIR************************
        $base_path = sfConfig::get('base_simad');
        $dir_tmp = sfConfig::get('sf_web_dir') . "/com_html/com_enviada/"; //directorio temporal para guardar los archivos a convertir a pdf    
        if (!is_dir($dir_tmp)) { //verificar si el directorio existe de lo contrario se crea
            @mkdir($dir_tmp, 0766); //crea el directorio destiono
        }
        //**************************************************************************************************
        $nomb_file_html = $this->getPrimaryKey() . ".php"; //nombre del archivo temporal que contiene los datos a convertir    
        if (file_exists($dir_tmp . $nomb_file_html)) { //verificar si ya esta generado el archivo a convertir
            unlink($dir_tmp . $nomb_file_html); //se elimina para actualizar el contenido del documento
        }
        $pt = fopen($dir_tmp . $nomb_file_html, 'w'); //se crea el archivo a convertir
        //**************************************************************************************************
        $contenedor_logos = '../../images/encabezado_carta/';
        $radicado_compuesto = "";
        //**************************************************************************************************
        //MANEJO RADICADOS CON CONSECUTIVO BANCOS
        $radicado_compuesto = $this->getRadicado();
        //**************************************************************************************************
        //destinatario
        $cD = new Criteria();
        $cD->add(EnviadaDirectorioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cD->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, 1);
        $enviada_usuario_destino = EnviadaDirectorioPeer::doSelectOne($cD);
        //**************************************************************************************************
        //copias externas
        $cD = new Criteria();
        $cD->add(EnviadaDirectorioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cD->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, 2);
        $enviada_copias_externas = EnviadaDirectorioPeer::doSelect($cD);
        //**************************************************************************************************
        //copias internas
        $cD = new Criteria();
        $cD->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cD->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 3);
        $enviada_usuario_copia = EnviadaUsuarioPeer::doSelect($cD);
        //**************************************************************************************************
        //firmantes
        $cD = new Criteria();
        $cD->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cD->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 2);
        $enviada_usuario_firma = EnviadaUsuarioPeer::doSelect($cD);
        //**************************************************************************************************
        //creador proyecto
        $cD = new Criteria();
        $cD->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cD->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 1);
        $enviada_usuario_proyecto = EnviadaUsuarioPeer::doSelectOne($cD);
        //**************************************************************************************************
        //revisores
        $cr = new Criteria();
        $cr->add(EnviadaUsuarioPeer::COMENVIADA_ID, $this->getPrimaryKey());
        $cr->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID, 4);
        $cr->add(EnviadaUsuarioPeer::FIRMA_APRUEBA, 1);
        $enviada_usuario_revision = EnviadaUsuarioPeer::doSelect($cr);
        //**************************************************************************************************
        //aprobadores estado item aprobado
        $cD = new Criteria();
        $cD->add(ComAprobacionPeer::CONSECUTIVO_ID, $this->getPrimaryKey());
        $cD->add(ComAprobacionPeer::MODULO_ID, 4);
        $cD->add(ComAprobacionPeer::ESTADOCOMAPROBACION_ID, 2); //estado id aprobado
        $enviada_usuarios_aprobadores = ComAprobacionPeer::doSelect($cD);
        //**************************************************************************************************
        $enviada_interesados = ComEnviadaPeer::getListIntersadosByComId($this->getPrimaryKey());
        //**************************************************************************************************
        $contneidoHtml = "";
        $copias = "";
        $anexosHtml = "";
        $proyecto = "";
        $anexos = "";
        //**************************************************************************************************
        $usuarioFirmaId = EnviadaUsuarioPeer::getFirstUsurioFirma($this->getPrimaryKey());
        $regionalFirmaId  = UsuarioPeer::retrieveByPK($usuarioFirmaId);
        $dir_regional = $this->getRegional()->getDireccion();
        //**************************************************************************************************
        $is_borrador = false;
        if (1 == $this->getEstadocomenviadaId()) {
            $correspondenciaEnBorrador = " - BORRADOR";
            $is_borrador = true;
        }
        //**************************************************************************************************
        if ($this->getCiudadId()) {
            $ciudadOrigen = $this->getCiudad()->getNombre();
        } else {
            $ciudadOrigen = $this->getRegional()->getCiudad()->getNombre();
        }
        //**************************************************************************************************
        $FechaMensaje = $this->getFechaCreacion();
        $timestamp_carta = strtotime($FechaMensaje);
        $asunto_title = "";
        $fecha_carta = "";
        if ($FechaMensaje == "") {
            $FechaMensaje = date("Y-m-d h:m:s");
        }
        //**************************************************************************************************
        if ($timestamp_carta == -1) {
            echo "La cadena ($FechaMensaje) no es v&aacute;lida.";
        } else {
            $fecha_carta = date("d", $timestamp_carta);
            $fecha_carta .= " de ";
            $mes = date("n", $timestamp_carta);
            switch ($this->getIdiomaId()) {
                case 5:
                    switch ($mes) {
                        case 1: {
                                $fecha_carta .= "Enero";
                                break;
                            }
                        case 2: {
                                $fecha_carta .= "Febrero";
                                break;
                            }
                        case 3: {
                                $fecha_carta .= "Marzo";
                                break;
                            }
                        case 4: {
                                $fecha_carta .= "Abril";
                                break;
                            }
                        case 5: {
                                $fecha_carta .= "Mayo";
                                break;
                            }
                        case 6: {
                                $fecha_carta .= "Junio";
                                break;
                            }
                        case 7: {
                                $fecha_carta .= "Julio";
                                break;
                            }
                        case 8: {
                                $fecha_carta .= "Agosto";
                                break;
                            }
                        case 9: {
                                $fecha_carta .= "Septiembre";
                                break;
                            }
                        case 10: {
                                $fecha_carta .= "Octubre";
                                break;
                            }
                        case 11: {
                                $fecha_carta .= "Noviembre";
                                break;
                            }
                        case 12: {
                                $fecha_carta .= "Diciembre";
                                break;
                            }
                    }
                    //$firmas_despedida ='Cordial saludo,';
                    $firmas_despedida = '';
                    $asunto_title = "<b>Asunto:</b> ";
                    $proyecto_text = $enviada_usuario_proyecto->getUsuario()->getNombreAndDependencia();
                    $proyecto = '<div width="100%" style="text-align:left; font-size: 6pt;">Elaborado por:<b>' . ($proyecto_text) . "</b></div>";
                    break;
                case 6:
                    switch ($mes) {
                        case 1: {
                                $fecha_carta .= "January";
                                break;
                            }
                        case 2: {
                                $fecha_carta .= "February";
                                break;
                            }
                        case 3: {
                                $fecha_carta .= "March";
                                break;
                            }
                        case 4: {
                                $fecha_carta .= "April";
                                break;
                            }
                        case 5: {
                                $fecha_carta .= "May";
                                break;
                            }
                        case 6: {
                                $fecha_carta .= "June";
                                break;
                            }
                        case 7: {
                                $fecha_carta .= "July";
                                break;
                            }
                        case 8: {
                                $fecha_carta .= "August";
                                break;
                            }
                        case 9: {
                                $fecha_carta .= "September";
                                break;
                            }
                        case 10: {
                                $fecha_carta .= "October";
                                break;
                            }
                        case 11: {
                                $fecha_carta .= "November";
                                break;
                            }
                        case 12: {
                                $fecha_carta .= "December";
                                break;
                            }
                    }
                    //$firmas_despedida ='Best Regards,';
                    $firmas_despedida = '';
                    $asunto_title = "<b>Reference:</b> ";
                    $proyecto_text = $enviada_usuario_proyecto->getUsuario()->getNombreAndDependencia();
                    $proyecto = '<div width="100%" style="text-align:left; font-size: 8pt;">Elaborated by: ' . ($proyecto_text) . "</b></div>";
                    break;
                default:
                    switch ($mes) {
                        case 1: {
                                $fecha_carta .= "Enero";
                                break;
                            }
                        case 2: {
                                $fecha_carta .= "Febrero";
                                break;
                            }
                        case 3: {
                                $fecha_carta .= "Marzo";
                                break;
                            }
                        case 4: {
                                $fecha_carta .= "Abril";
                                break;
                            }
                        case 5: {
                                $fecha_carta .= "Mayo";
                                break;
                            }
                        case 6: {
                                $fecha_carta .= "Junio";
                                break;
                            }
                        case 7: {
                                $fecha_carta .= "Julio";
                                break;
                            }
                        case 8: {
                                $fecha_carta .= "Agosto";
                                break;
                            }
                        case 9: {
                                $fecha_carta .= "Septiembre";
                                break;
                            }
                        case 10: {
                                $fecha_carta .= "Octubre";
                                break;
                            }
                        case 11: {
                                $fecha_carta .= "Noviembre";
                                break;
                            }
                        case 12: {
                                $fecha_carta .= "Diciembre";
                                break;
                            }
                    }
                    //$firmas_despedida ='Cordial saludo,';
                    $firmas_despedida = '';
                    $asunto_title = "<b>Asunto:</b> ";
                    $proyecto_text = $enviada_usuario_proyecto->getUsuario()->getNombreAndDependencia();
                    $proyecto = '<div width="100%" style="text-align:left; font-size: 6pt;">Elaborado por:<b>' . ($proyecto_text) . "</b></div>";
                    break;
            }
            $fecha_carta .= " de ";
            $fecha_carta .= date("Y ", $timestamp_carta);
            //$fecha_carta .= "," . date( "h:m",$timestamp_carta );
        }
        //**************************************************************************************************
        $asunto = $this->getAsuntoHtml();
        $mensaje = $this->getContenido();
        //**************************************************************************************************
        $firma_nombres = array();
        $firma_cargos = array();
        $firma_area = array();
        $firma_mecanica = array();
        foreach ($enviada_usuario_firma as $enviada_usuario_firmaOne) {
            if (trim($this->getFirmaElectronica()) && ($enviada_usuario_firmaOne->getEstadocomenviadaId() != 1)) {
                if (trim($enviada_usuario_firmaOne->getUsuario()->getFirmaElectronica())) {
                    $firma_mecanica[] = '<img src="' . trim($enviada_usuario_firmaOne->getUsuario()->getFirmaElectronica()) . '" min-height="80px" max-height="150px" width="180px">';
                } else {
                    $firma_mecanica[] = '&nbsp;';
                }
            } else {
                $firma_mecanica[] = '&nbsp;';
            }
            //**********************************************************************************************
            $firma_nombres[] =  $enviada_usuario_firmaOne->getUsuario()->getNombreApellido();
            $firma_cargos[] = $enviada_usuario_firmaOne->getCargousuarioId() ? ($enviada_usuario_firmaOne->getCargoUsuario()->getCargo()->getDescripcion()) : "";
            $dependencia_id = !empty($enviada_usuario_firmaOne->getCargousuarioId()) ? $enviada_usuario_firmaOne->getCargoUsuario()->getDependenciaId() : null;

            if (!empty($dependencia_id)) {
                $area_encargo = DependenciaPeer::retrieveByPK($dependencia_id);
                $firma_area[] = $area_encargo != null ? $area_encargo->getNombre() : $enviada_usuario_firmaOne->getUsuario()->getDependencia()->getNombre();
            } else {
                $firma_area[] = $enviada_usuario_firmaOne->getUsuario()->getDependencia()->getNombre();
            }
        }
        //**************************************************************************************************
        $dataFirmas['firma_nombres'] = $firma_nombres;
        $dataFirmas['firma_cargos'] = $firma_cargos;
        $dataFirmas['firma_areas'] = $firma_area;
        $dataFirmas['firma_mecanica'] = $firma_mecanica;
        //**************************************************************************************************
        $contFirmas = 0;
        $useHeadersTpl = $this->getPlantillascomId() ? ($this->getPlantillasCom()->getUseHeaders() ? true : false) : true;
        $isAddCargo = true;
        if ($useHeadersTpl) {
            $firmas = '<br /><table border="0" cellspacing="1" cellpadding="1" width="99%" align="left">';
            $isAddCargo = $this->getPlantillascomId() == 6 ? false : true;
        } else {
            $firmas = '<br /><br /><div style="text-align:center;">';
            $firmas .= '<table border="0" style="margin: 0 auto;">';
            $isAddCargo = $this->getPlantillascomId() == 6 ? false : true;
        }
        //**************************************************************************************************
        $usuario_firmas = array();
        $cargos_firmas = array();
        $areas_firmas = array();
        $mecanica_firmas = array();
        $contFirmas = 0;
        $contCopias = 0;
        $contCargos = 0;
        //$firmas .= '<tr>';
        //**************************************************************************************************
        foreach ($dataFirmas['firma_nombres'] as $row_firma) {
            if ($contFirmas > 0) {
                if ($contFirmas % 2 == 0) {
                    //print_r($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas, false);
                    $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
                    $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
                    $usuario_firmas = array();
                    $cargos_firmas = array();
                    $areas_firmas = array();
                    $mecanica_firmas = array();
                    $firmas .= $this->getDataEmptyRowFirmas(3);
                }
            }
            //**********************************************************************************************
            if ($dataFirmas['firma_mecanica'][$contFirmas] != null) {
                $mecanica_firmas[] = $dataFirmas['firma_mecanica'][$contFirmas];
            }
            //**********************************************************************************************
            $usuario_firmas[] = '<td style="{%class%}"><b>' . $row_firma . '</b></td>';
            $cargos_firmas[] = $dataFirmas['firma_cargos'][$contFirmas];
            $areas_firmas[] = $dataFirmas['firma_areas'][$contFirmas];
            $contFirmas++;
        }
        //**************************************************************************************************
        if (count($cargos_firmas)) {
            $firmas .= $this->getDataAreasOrCargosFirmas($mecanica_firmas);
            $firmas .= $this->getDataAreasOrCargosFirmas($usuario_firmas, false);
            if ($isAddCargo) {
                $firmas .= $this->getDataAreasOrCargosFirmas($cargos_firmas);
            }
            $firmas .= $this->getDataAreasOrCargosFirmas($areas_firmas);
            $usuario_firmas = array();
            $cargos_firmas = array();
            $areas_firmas = array();
            $mecanica_firmas = array();
        }
        //**************************************************************************************************
        if ($useHeadersTpl) {
            $firmas .= '</table>';
        } else {
            $firmas .= '</table></div>';
            $firmas = str_replace("{%class%}", "text-align: center", $firmas);
        }
        //**************************************************************************************************
        $iterator = 0;
        $is_copias_html = false;
        $copias_html = '<br/><table border="0" cellspacing="0" cellpadding="0">';
        //$copias_html .= '<tr><td style="width:110px"><b>Copia Interna:</b></td>';
        foreach ($enviada_usuario_copia as $enviada_usuario_copiaOne) {
            $usuario_name = $enviada_usuario_copiaOne->getUsuario()->getNombre() . " " . $enviada_usuario_copiaOne->getUsuario()->getApellido();
            $cargo_usuario = $enviada_usuario_copiaOne->getCargoUsuario()->getCargo();
            $prefijo_usuario = $enviada_usuario_copiaOne->getUsuario()->getPrefijo();
            $struct_copia_interna = ($prefijo_usuario) . ' - ' . ($usuario_name);
            //**************************************************************************************************
            if (trim($cargo_usuario)) {
                $struct_copia_interna .= ' - ' . ($cargo_usuario);
            }
            //********************************************************************************************
            if ($iterator == 0) {
                $copias_html .= '<tr><td style="width:50px; text-align:left; font-size: 6pt;">Copia Interna:</td>';
                //$copias_html .= '<td align="left">'.($prefijo_usuario).'</td></tr>';
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($usuario_name).'</td></tr>';
                $copias_html .= '<td style="text-align:left; font-size: 6pt;"><b>' . ($struct_copia_interna) . '</b></td></tr>';
            } else {
                $copias_html .= '<tr><td style="width:50px; text-align:left; font-size: 6pt;">&nbsp;</td>';
                $copias_html .= '<td align="left" style="text-align:left; font-size: 6pt;"><b>' . ($struct_copia_interna) . '</b></td></tr>';
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($usuario_name).'</td></tr>';
            }
            //**********************************************************************************************
            if (trim($cargo_usuario)) {
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($cargo_usuario).'</td></tr>';
            }
            //**********************************************************************************************
            $is_copias_html = true;
            $iterator++;
        }
        //**************************************************************************************************
        $list_revisores = array();
        foreach ($enviada_usuario_revision as $usuario_revisor) {
            if (!in_array($usuario_revisor->getUsuarioId(), $list_revisores)) {
                $revisor_text = $usuario_revisor->getUsuario()->getFullNombre();
                $revisor_area = $usuario_revisor->getUsuario()->getDependencia()->getNombre();
                $dependencia_id = $usuario_revisor->getCargoUsuario()->getDependenciaId();

                if (!empty($dependencia_id)) {
                    $revisor_area = $usuario_revisor->getCargoUsuario()->getDependencia()->getNombre();
                }
                $proyecto .= '<div width="100%" style="margin-top:5px;text-align:left; font-size: 6pt;"><b>Revisado por:</b> ' . (sprintf('%s - %s', $revisor_text, $revisor_area)) . "</div>";
                $list_revisores[] = $usuario_revisor->getUsuarioId();
            }
        }
        //**************************************************************************************************
        //$copias_html .= '</tr><tr><td style="width:110px"><b>Copia Externa:</b></td>';
        $index = 0;
        foreach ($enviada_copias_externas as $enviada_copias_externasOne) {
            $prefijo_externo = $enviada_copias_externasOne->getDirectorioExterno()->getPrefijo();
            $usuario_externo = $enviada_copias_externasOne->getDirectorioExterno()->getFuncionario();
            $cargo_externo = trim($enviada_copias_externasOne->getDirectorioExterno()->getCargo());
            $entidad_externa = trim($enviada_copias_externasOne->getDirectorioExterno()->getNombre());
            $struct_copia_externa = "";
            //**************************************************************************************************
            //PREFIJO FUNCIONARIO
            if (trim($prefijo_externo)) {
                $struct_copia_externa .= $struct_copia_externa != "" ? $prefijo_externo : $prefijo_externo;
            }
            //**************************************************************************************************
            //FUNCIONARIO
            if (trim($usuario_externo)) {
                $struct_copia_externa .= $struct_copia_externa != "" ? " - " . $usuario_externo : $usuario_externo;
            }
            //**************************************************************************************************
            //CARGO FUNCIONARIO
            if (trim($cargo_externo)) {
                $struct_copia_externa .= $struct_copia_externa != "" ? " - " . $cargo_externo : $cargo_externo;
            }
            //**************************************************************************************************
            //NOMBRE EMPRESA
            if (trim($entidad_externa)) {
                $struct_copia_externa .= $struct_copia_externa != "" ? " - " . $entidad_externa : $entidad_externa;
            }
            //**************************************************************************************************
            if ($index == 0) {
                $copias_html .= '</tr><tr><td style="width:55px;text-align:left; font-size: 6pt;">Copia Externa:</td>';
                $copias_html .= '<td style="text-align:left; font-size: 6pt;"><b>' . $struct_copia_externa . '</b></td></tr>';
                //$copias_html .= '<tr><td >&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($enviada_copias_externasOne->getDirectorioExterno()->getFuncionario()).'</td></tr>';
            } else {
                $copias_html .= '<tr><td style="width:55px;text-align:left; font-size: 6pt;">&nbsp;</td>';
                $copias_html .= '<td align="left" style="text-align:left; font-size: 6pt;"><b>' . $struct_copia_externa . '</b></td></tr>';
                //$copias_html .= '<tr><td >&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($enviada_copias_externasOne->getDirectorioExterno()->getFuncionario()).'</td></tr>';
            }
            //**************************************************************************************************
            //CARGO FUNCIONARIO
            if (($cargo_externo)) {
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($enviada_copias_externasOne->getDirectorioExterno()->getCargo()).'</td></tr>';
            }
            //**************************************************************************************************
            //NOMBRE EMPRESA
            if (($entidad_externa)) {
                //$copias_html .= '<tr><td>&nbsp;</td>';
                //$copias_html .= '<td align="left">'.($enviada_copias_externasOne->getDirectorioExterno()->getNombre()).'</td></tr>';          
            }
            //**************************************************************************************************
            $is_copias_html = true;
            $index++;
        }
        //echo $index;exit;
        $copias_html .= '</table>';
        //******************************************************ANEXOS**************************************
        if (trim($this->getAnexos()) != "") {
            //$anexosHtml = " <h6>Anexos: ".$this->getAnexos()." </h6>";
            $anexosHtml .= '<div width="100%" style="text-align:left; font-size: 6pt;">Anexos: <b>' . ($this->getAnexos()) . "</b></div>";
        }
        //**************************************************************************************************
        $htmlcontent = '';
        $correspondenciaEnBorrador = "";
        if ($this->getRadicado() != "") {
            //$htmlcontent .= '<div style="text-align:center"><h4>'.$correspondenciaEnBorrador.'</h4></div>';
        }
        //**************************************************************************************************
        if ($useHeadersTpl) {
            $htmlcontent .= $ciudadOrigen . ', ' . $fecha_carta . '<br>';
        }
        //**************************************************************************************************
        $tempDependencia = true;
        $funcionario_destino = "";
        $entidad_origen = "";
        //**************************************************************************************************
        if (!count($enviada_interesados) && $enviada_usuario_destino != null) {
            if (trim($this->getPrefijo())) {
                $tratamiento = ($this->getPrefijoHtml());
                $htmlcontent .= '<br/><br/>' . $tratamiento;
            } elseif (trim($enviada_usuario_destino->getDirectorioExterno()->getPrefijo()) != '') {
                $tratamiento = ($enviada_usuario_destino->getDirectorioExterno()->getPrefijo());
                $htmlcontent .= '<br/><br/>' . ($tratamiento);
            } else {
                $tratamiento = "Se&ntilde;or(a):";
                $htmlcontent .= '<br/><br/>' . $tratamiento;
            }
            //**************************************************************************************************
            if (trim($this->getFuncionarioDestino()) != "") {
                $funcionario_destino = ($this->getFuncionarioHtml());
                $htmlcontent .= '<br/><b>' . $funcionario_destino . '</b>';
            } elseif (trim($enviada_usuario_destino->getDirectorioExterno()->getFuncionario()) != "") {
                $funcionario_destino = ($enviada_usuario_destino->getDirectorioExterno()->getFuncionario());
                if (!$tempDependencia) {
                    $htmlcontent .= '<br/>' . $funcionario_destino;
                } else {
                    $htmlcontent .= '<br/><b>' . $funcionario_destino . '</b>';
                }
            } elseif (trim($enviada_usuario_destino->getDirectorioExterno()->getNombre()) != '' && $tempDependencia != false) {
                $funcionario_destino = ($enviada_usuario_destino->getDirectorioExterno()->getNombre());
                $htmlcontent .= '<br/><b>' . $funcionario_destino . '</b>';
                $tempDependencia = false;
            }
            //**************************************************************************************************
            if (trim($this->getCargoDestinatario()) != "") {
                $cargoDestinatario = ($this->getCargoHtml());
                $htmlcontent .= '<br/>' . $cargoDestinatario;
            } elseif (trim($enviada_usuario_destino->getDirectorioExterno()->getCargo()) != '') {
                $cargoDestinatario = ($enviada_usuario_destino->getDirectorioExterno()->getCargo());
                $htmlcontent .= '<br/>' . $cargoDestinatario;
            }
            //**************************************************************************************************
            if (trim($enviada_usuario_destino->getDirectorioExterno()->getNombre()) != '') {
                $entidad_origen = ($enviada_usuario_destino->getDirectorioExterno()->getNombre());
                if (trim($entidad_origen) != trim($funcionario_destino)) {
                    if ($tempDependencia) {
                        $htmlcontent .= '<br/>' . $entidad_origen;
                        $tempDependencia = false;
                    }
                }
            }
            //************************************************************************************************** 
            if (trim($this->getDireccionDestinatario()) != "") {
                $direccionDestinatario = ($this->getDireccionHtml());
                $htmlcontent .= '<br/>' . $direccionDestinatario;
            } elseif (trim($enviada_usuario_destino->getDirectorioExterno()->getDireccion()) != '') {
                $direccionDestinatario = ($enviada_usuario_destino->getDirectorioExterno()->getDireccion());
                $htmlcontent .= '<br/>' . $direccionDestinatario;
            }
            //**************************************************************************************************
            if (trim($enviada_usuario_destino->getDirectorioExterno()->getCiudad()->getNombre()) != '') {
                $ciudad_destinatario = ($enviada_usuario_destino->getDirectorioExterno()->getCiudad()->getNombre());
                $htmlcontent .= '<br/>' . $ciudad_destinatario;
            }
            //**************************************************************************************************
            /*if(trim($enviada_usuario_destino->getDirectorioExterno()->getTelefono()) != ''){
                $telefonoDestinatario=$enviada_usuario_destino->getDirectorioExterno()->getTelefono();
                $htmlcontent .= '<br>'."Tel: ".$telefonoDestinatario;
            }*/
        }
        //**************************************************************************************************
        $interesados_section = "";
        foreach ($enviada_interesados as $interesado) {
            $interesados_section = '<div width="100%" style="text-align:left;margin-top:25px"><table border="0" width="100%" align="left">';
            //**********************************************************************************************
            if (trim($interesado->getInteresados()->getPrefijo())) {
                $interesados_section .= '<tr><td style="text-align:left;">' . trim($interesado->getInteresados()->getPrefijo()) . '</td></tr>';
            }
            //**********************************************************************************************
            $interesados_section .= '<tr><td style="text-align:left;"><b>' . $interesado->getInteresados()->getNombreCompuesto() . '</b></td></tr>';
            //**********************************************************************************************
            if (trim($interesado->getInteresados()->getNumeroIdentificacion())) {
                $interesados_section .= '<tr><td style="text-align:left;">' . $interesado->getInteresados()->getTipoIdentificacion()->getSigla() . ': ' . trim($interesado->getInteresados()->getNumeroIdentificacion()) . '</td></tr>';
            }
            //**********************************************************************************************
            $interesados_section .= '<tr><td style="text-align:left;">' . $interesado->getInteresados()->getCiudad()->getNombre() . '</td></tr>';
            //**********************************************************************************************
            if ($interesado->getInteresados()->getEmail()) {
                $interesados_section .= '<tr><td style="text-align:left;">' . $interesado->getInteresados()->getEmail() . '</td></tr>';
            }
            //**********************************************************************************************
            if (!empty($interesado->getInteresados()->getTelefono()) || !empty($interesado->getInteresados()->getCelular())) {
                $itel = trim($interesado->getInteresados()->getTelefono()) ? "Tel: " . trim($interesado->getInteresados()->getTelefono()) : "";
                $icel = trim($interesado->getInteresados()->getCelular()) ? "Cel: " . trim($interesado->getInteresados()->getCelular()) : "";
                $icontacto = trim($itel) ? (trim($icel) ? $itel . " / " . $icel : $itel) : $icel;
                $interesados_section .= '<tr><td style="text-align:left;">' . ($icontacto) . '</td></tr>';
            }
            //**********************************************************************************************
            $interesados_section .= '</table></div>';
            if ($useHeadersTpl && $this->getPlantillascomId() != 7) {
                $htmlcontent .= $interesados_section;
            }
        }
        //**************************************************************************************************
        //se crea el html para el pie de pagina de la carta y el encoding
        $footer_logo = "";
        $htmlfooter = '<pd4ml:page.footer>    
        <div width="100%" style="text-align:right">$[page]/$[total]</div>
        </pd4ml:page.footer>';
        fputs($pt, ($htmlfooter));
        //**************************************************************************************************
        //se crea el html para el encabezado de la carta
        $htmlencabezado = '<pd4ml:page.header>';
        //**************************************************************************************************
        if ($isFirmaDigital) {
            $texto_firma = "Documento firmado electr&oacute;nicamente de acuerdo con la Ley 527 de 1999 y el Decreto 2364 de 2012";
            $htmlencabezado .= '<div width="100%" style="text-align:left;font-size: 8pt;font-style: italic;font-family: Segoe UI, Arial, sans-serif; font-weight: lighter;">' . ($texto_firma) . '</div>';
        }
        //**************************************************************************************************
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 7pt;"><b>F-OAP-018-CAR</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;"><img style="width:80px; height:auto;" src="' . $base_path . '/tmp/' . $this->generateImgCodeCom(true, array('clearlabels' => true)) . '"/></div>';
        //$this->generateImgCodeCom(false, array('clearlabels' => true))
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;"><b>Al contestar por favor cite estos datos:</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;">Radicado No.: <b>' . $radicado_compuesto . '</b></div>';
        $htmlencabezado .= '<div width="100%" style="text-align:right;font-size: 8pt;">Fecha: ' . $this->getFechaCreacion("d/m/Y H:i:s A") . '</div>';
        //**************************************************************************************************
        $htmlencabezado .= '<br/><br/><br/><br/></pd4ml:page.header>';
        fputs($pt, ($htmlencabezado));
        //$fecha_carta .= "," . date( "h:m",$timestamp );
        /********************************definir font para el documento************************************/
        $struct_init = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title>
        <style type="text/css">body {font-family:Work Sans,sans-serif;font-size: 13pt;}</style></head><body>';
        fputs($pt, ($struct_init));
        /***********************************definir font para el documento********************************/
        if (!$useHeadersTpl) {
            $htmlcontent .= '';
        } else {
            $htmlcontent .= '<br/><br/><br/><table border="0" cellspacing="1" cellpadding="1"><tr><td width="60px" valign="top">';
            $htmlcontent .= $asunto_title . '</td><td width="99%"><p style="text-align: justify">' . ($asunto) . '</p></td></tr></table><br/><br/>';
        }
        //**************************************************************************************************
        fputs($pt, ($htmlcontent));
        //**************************************************************************************************
        $contneidoHtml = '' . ($mensaje) . '';
        fputs($pt, ($contneidoHtml));
        //**************************************************************************************************
        $com_recibida = $this->getObjRecibidaResp($this->getPrimaryKey());
        if ($com_recibida != null) {
            $html_comresp = '<div width="100%" style="text-align:left; font-size: 6pt;">Antecedente:<b>' . ($com_recibida->getRadicado()) . "</b></div>";
        }
        //**************************************************************************************************
        if (trim($firmas) != "") {
            fputs($pt, ($firmas));
        }
        //**************************************************************************************************
        if ((trim($copias_html) != "" && $is_copias_html) && $useHeadersTpl) {
            fputs($pt, ($copias_html));
        }
        //**************************************************************************************************
        if (trim($anexosHtml) != "") {
            fputs($pt, ($anexosHtml));
        }
        //**************************************************************************************************
        if (!empty($proyecto)) {
            fputs($pt, ($proyecto));
        }
        //**************************************************************************************************
        if (trim($html_comresp) && $useHeadersTpl) {
            fputs($pt, ($html_comresp));
        }
        //**************************************************************************************************
        $struct_end = '</body></html>';
        fputs($pt, ($struct_end));
        //**************************************************************************************************
        //Close and output PDF document
        fclose($pt);
    }

    public function generateImgCodeCom($params = array())   //metodo "bisagra"
    {

        // invocar app.ini
        $read_sections = array('keyprivate_watermark', 'url_virtual', 'com_enviada_sticker');
        $ini_array = simad_util::readConfigFileApp($read_sections);
        $genQR = isset($ini_array['com_enviada_sticker']) ? $ini_array['com_enviada_sticker'] : 'CODEBAR';

        if ($genQR == 'QR') {
            $keyprivate = isset($ini_array['keyprivate_watermark']) ? $ini_array['keyprivate_watermark'] : null;
            $urlvirtual = isset($ini_array['url_virtual']) ? $ini_array['url_virtual'] : null;

            $hash_interno = ComEnviadaPeer::getHashComData($this);
            $encrypted_string = simad_util::encrypt_decrypt('encrypt', $hash_interno, $keyprivate);

            $text_radicado = sprintf('%s/%s=%s', sfConfig::get('publicUrl'), $urlvirtual, $encrypted_string);
            $tamano = isset($params['tamano']) ? $params['tamano'] : 10;
            $level = isset($params['level']) ? $params['level'] : 'H';
            $framesize = isset($params['framesize']) ? $params['framesize'] : 3;

            return simad_util::generateCodeQrInFile($text_radicado, $tamano, $level, $framesize);
        } else {
            $text_radicado = $this->getRadicado();
            $clearlabels = isset($params['clearlabels']) ? $params['clearlabels'] : false;
            $scale = isset($params['scale']) ? $params['scale'] : 1;
            $height = isset($params['height']) ? $params['height'] : 25;
            $fsize = isset($params['fsize']) ? $params['fsize'] : 8;
            $dpi = isset($params['dpi']) ? $params['dpi'] : 72;

            return simad_util::generateCodeBarInFile($text_radicado, $clearlabels, $scale, $height, $fsize, $dpi);
        }
    }


    public function getCombinarContenido(ComRecibida $com_recibida)
    {
        $patrones = array();
        $patrones[0]  = '{{$radicado_entrada}}';
        $patrones[1]  = '{{$fecha_entrada}}';
        $patrones[2]  = '{{$destinatario}}';
        //**************************************************************************************************
        $sustituciones    = array();
        $sustituciones[0] = $com_recibida != null ? $com_recibida->getRadicado() : "";
        $sustituciones[1] = $com_recibida != null ? $com_recibida->getFechaCreacion() : "";
        $sustituciones[1] = $com_recibida != null ? $com_recibida->getDirectorioExterno()->getNombre() : "";
        //*****************************************REMPLAZAR DATOS *****************************************
        $contenido_merge = str_replace($patrones, $sustituciones, $this->getContenido());
        //*****************************************REMPLAZAR DATOS *****************************************
        return $contenido_merge;
    }

    public function getObjRecibidaResp($comenviadaId)
    {
        $com_recibida = null;
        $crp = new Criteria();
        $crp->add(ComRecibidaPeer::COMENVIADA_ID, $comenviadaId);
        $is_resp = ComRecibidaPeer::doCount($crp);
        if ($is_resp > 0) {
            $corp = new Criteria();
            $corp->add(ComRecibidaPeer::COMENVIADA_ID, $comenviadaId);
            $com_recibida = ComRecibidaPeer::doSelectOne($corp);
        }
        return $com_recibida;
    }

    public function generateFileInDisk($margins_list = null, $isFirmaDigital = false)
    {
        $base_path = sfConfig::get('base_simad');
        $tplnot_comdoc = array(6);
        //*********************************************************************************************
        //MARGENES DE IMPRESION
        if ($margins_list == null) {
            $margins_list['top'] = 10;
            $margins_list['left'] = 15;
            $margins_list['buttom'] = 30;
            $margins_list['rigth'] = 18;
        }
        //*********************************************************************************************
        if ($this->getPlantillascomId() && in_array($this->getPlantillascomId(), $tplnot_comdoc)) {
            $this->genPdfByPlantillaCom();
            $margins_list['top'] = 30;
        } else {
            $this->generateFilePdf($isFirmaDigital);
        }
        //*********************************************************************************************
        $filedata = $this->getPrimaryKey() . '.php';
        //$url_base = simad_util::getUrlBase();
        $url_site = "http://" . $_SERVER["HTTP_HOST"];
        $url = $url_site . '/com_html/com_enviada/' . $filedata;
        //*********************************************************************************************
        $filename = md5(date("YmdGis") . $this->getPrimaryKey()) . ".pdf";
        $dir_jar = sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'pd4ml' . DIRECTORY_SEPARATOR . 'pd4ml.jar';
        $dir_font = sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'pd4ml' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR;
        $fullpath_source = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $filename;
        //*********************************************************************************************
        $java = simad_util::getJavaJdkRoot();
        $format_page = "A4";
        $size_point_page = "840";
        $font_use = "-ttf $dir_font";
        $adjustwidth = "-adjustwidth";
        //$margins = '-insets 10,23,8,17,mm';//superior=3,izquierda=3,inferior=2,derecha=2
        $margins = '-insets ' . $margins_list['top'] . ',' . $margins_list['left'] . ',' . $margins_list['buttom'] . ',' . $margins_list['rigth'] . ',mm';
        $outfile = "-out $fullpath_source";
        $orientation = "";
        //*********************************************************************************************
        if ($this->getUseMembrete()) {
            $image_membrete = $this->getRegional()->getImageMembrete();
            //*****************************************************************************************
            $filepath = "/images%sencabezado_carta%s" . $image_membrete;
            $web_path = sprintf($filepath, "/", "/");
            $fullpath = sprintf($filepath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
            $realpath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $fullpath;
            if (file_exists($realpath)) {
                $watermark = "-bgimage " . $url_site . $web_path;
            }
        }
        //*********************************************************************************************
        if (strpos(php_uname(), 'Windows') !== FALSE) {
            // server platform: Windows
            $dir_jar = preg_replace('/\//', "\\", $dir_jar);
            $cmdline = "$java -Xmx512m -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
        } else {
            $font_use = "";
            $cmdline = "$java -XX:MaxHeapSize=8m -XX:CompressedClassSpaceSize=64m -XX:+UseSerialGC -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $outfile";
            //$cmdline = "$java -Xms1024m -Xmx4096m -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use";	    
        }
        //*********************************************************************************************
        //echo $cmdline;exit;
        shell_exec($cmdline);
        //*********************************************************************************************
        if (file_exists($fullpath_source)) {
            return $fullpath_source;
        } else {
            return null;
        }
    }

    public function SimadGeneratePdf($inputFileName, $returnFullPath = false, $deleteFile = false, $isFirmaDigital = false)
    {
        if (file_exists($inputFileName)) {
            $typefile = strtolower(pathinfo($inputFileName, PATHINFO_EXTENSION));
            if ($typefile == 'pdf') {
                return $this->readPdfAndGenPdf($inputFileName, $returnFullPath, $deleteFile, $isFirmaDigital, true);
            } elseif ($typefile == 'doc' || $typefile == 'docx') {
                $key_config = simad_util::readConfigFileApp(array('strategy_gen_word_to_pdf'));
                $word_to_pdf = isset($key_config['strategy_gen_word_to_pdf']) ? trim($key_config['strategy_gen_word_to_pdf']) : "PhpWord";
                //***************************************************************************
                if ($word_to_pdf == "PhpWord")
                    return $this->readPlantillaWordAndGenPdf($inputFileName, $returnFullPath, $deleteFile);
                else
                    return $this->readPlantillaWordAndGenCom($inputFileName, $returnFullPath, $deleteFile);
            } else {
                return null;
            }
        } else {
            try {
                include_once(sfConfig::get('sf_lib_dir') . '/BarcodeGenerator/generate_barcode.php');
                //******************************************************************************
                $text_radicado = $this->getRadicado();
                if (empty($text_radicado) || is_null($text_radicado)) {
                    $text_radicado = "Sin Radicar";
                }
                //******************************************************************************
                $sticker_dir = sfConfig::get('sf_web_dir');
                $font_dir = sfConfig::get('sf_lib_dir') . "/BarcodeGenerator/class/font/ariblk.ttf";
                $filename = md5($text_radicado) . '.png';
                $filepath = $sticker_dir . "/tmp/" . $filename;
                GenerateCode($filepath, $text_radicado, $font_dir, $scale, $height, $fsize, $dpi, $clearlabels);
                //******************************************************************************
                return file_exists($filepath) ?  $filename : null;
            } catch (Exception $e) {
                //$error_text = $e->getMessage();
                return null;
            }
            return null;
        }
    }

    public function readPdfAndGenPdf($inputFileName, $returnFullPath = false, $deleteFile = false, $isFirmaDigital = false, $isCodeBar = false)
    {
        require_once(sfConfig::get('sf_lib_dir') . '/PdfTools/fpdf/fpdf.php');
        require_once(sfConfig::get('sf_lib_dir') . '/PdfTools/fpdi/autoload.php');
        require_once(sfConfig::get('sf_lib_dir') . '/PdfTools/fpdi/PDF-Parser-1.5/autoload.php');
        /*require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/Fpdi.php');*/
        //**********************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        $source_filename = md5(date("YmdGis") . pathinfo($inputFileName, PATHINFO_FILENAME));
        $target_dir = md5(date("YmdGis"));
        //**********************************************************************************************
        $read_sections = array('com_enviada_sticker');
        $ini_array = simad_util::readConfigFileApp($read_sections);
        $genQR = isset($ini_array['com_enviada_sticker']) ? $ini_array['com_enviada_sticker'] : 'CODEBAR';
        //**********************************************************************************************
        if (trim($inputFileName)) {
            try {
                $pathToSave = simad_util::createPath($directorio_tmp . DIRECTORY_SEPARATOR . $target_dir);
                $codebar = $tmp_dir . DIRECTORY_SEPARATOR . $this->generateImgCodeCom(false, array('clearlabels' => true));
                //**************************************************************************************
                if ($this->getEstadocomenviadaId() == 4) {
                    $tanulado = "DOCUMENTO ANULADO";
                    $fanulado = $this->getFechaDeAnulacion();
                    $pdf = new FpdfWatermark();
                    $pdf->setWaterText($tanulado, $fanulado);
                } else {
                    $pdf = new Fpdi();
                }
                // set the source file
                $pagecount = $pdf->setSourceFile($inputFileName);
                //**************************************************************************************
                // import page 1
                $pageNo = 1;
                $ptln = 5;
                $tplId = $pdf->importPage($pageNo);
                //$size = $pdf->getTemplateSize($tplId);
                // add a page
                $pdf->AddPage();
                // use the imported page and place it at point 10,10 with a width of 100 mm
                $pdf->useTemplate($tplId, null, null, null, null, true);
                $pdf->SetFont('Arial', 'B', 8);
                //**************************************************************************************
                if ($isFirmaDigital) {
                    $fdigital_text = "Documento firmado electrónicamente de acuerdo con la Ley 527 de 1999 y el Decreto 2364 de 2012";
                    $pdf->SetFont('Arial', 'I', 7);
                    $pdf->Cell(0, 10, mb_convert_encoding($fdigital_text, "ISO-8859-1", "UTF-8"), 0, 0);
                    //$pdf->Ln($ptln);
                }
                //**************************************************************************************
                if ($genQR == "CODEBAR") {
                    $pdf->Cell(0, 10, "F-OAP-018-CAR", 0, 0, 'R');
                    $pdf->Ln($ptln);
                }
                //**************************************************************************************
                if ($genQR == "CODEBAR") {
                    $pdf->Cell(0, 10, $pdf->Image($codebar, 157.5, 16.5, 0, 0, 'png'), 0, 0, 'R');
                } elseif ($genQR == "QR") {
                    $imgW = 15; // ancho del QR en mm
                    $y    = 10.5;
                    //**********************************************************************************
                    $x = $pdf->GetPageWidth() - $imgW - 10; // 5 mm del borde
                    //**********************************************************************************
                    $pdf->Cell(0, 10, $pdf->Image($codebar, $x, $y, $imgW, 0, 'PNG'), 0, 0, 'R');
                    $pdf->Ln($ptln + 6);
                    $pdf->SetFont('Arial', 'B', 4);
                    $pdf->Cell(0, 10, "Rad No.: " . $this->getRadicado(), 0, 0, 'R');
                } else {
                    $pdf->Cell(0, 10, $pdf->Image($codebar, 157.5, 16.5, 0, 0, 'png'), 0, 0, 'R');
                }
                $pdf->Ln($ptln);
                //**************************************************************************************
                if ($genQR == "CODEBAR") {
                    $pdf->Cell(0, 10, "Al contestar por favor cite estos datos:", 0, 0, 'R');
                    $pdf->Ln($ptln - 1);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(164, 10, "Radicado No.:", 0, 0, 'R');
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(0, 10, $this->getRadicado(), 0, 0, 'R');
                    $pdf->Ln($ptln - 1);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(154, 10, "Fecha:", 0, 0, 'R');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(0, 10, $this->getFechaCreacion("d/m/Y H:i:s A"), 0, 0, 'R');
                }
                //***************************************************************************************
                for ($pageNo = ($pageNo + 1); $pageNo <= $pagecount; $pageNo++) {
                    $tplIdx = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplIdx, null, null, null, null, true);
                }
                //***************************************************************************************
                $pdf->Output('F', $pathToSave . DIRECTORY_SEPARATOR . $source_filename . '.pdf');
                $source_filename = $returnFullPath ? ($pathToSave . DIRECTORY_SEPARATOR . $source_filename) . '.pdf' : ($target_dir . '/' . $source_filename . '.pdf');
            } catch (Exception $e) {
                echo $e->getMessage();
                $source_filename = null;
            }
        } else {
            $source_filename = null;
        }
        //***********************************************************************************************
        return $source_filename;
    }

    public function readPlantillaWordAndGenCom($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        $path_info = pathinfo($inputFileName);
        //*************************************************************************************************************
        $outFileName = md5(date("YmdGis")) . '.pdf';
        $pathToSave = $directorio_tmp . DIRECTORY_SEPARATOR . $outFileName;
        $temp_name = dirname($pathToSave) . DIRECTORY_SEPARATOR . $path_info['filename'] . '.pdf';
        $replacement_images = array();
        //*************************************************************************************************************
        if (trim($inputFileName) && file_exists($inputFileName)) {
            try {
                if (file_exists($temp_name)) {
                    unlink($temp_name);
                }
                //*****************************************************************************************************
                $docxSalida = $tmp_dir . DIRECTORY_SEPARATOR . basename($inputFileName);
                if (file_exists($docxSalida)) {
                    @unlink($docxSalida);
                }
                //*****************************************************************************************************
                $userscom_data = $this->getUsuariosListComObjs();
                //*****************************************************************************************************
                $lstfirmas = $userscom_data['firmas'];
                $unfirma = "";
                $ufcargo = "";
                $ufarea = "";
                $ufregional = "";
                $urlfmecanica = "";
                foreach ($lstfirmas as $value) {
                    $unfirma = $value['nombre_ufirma'];
                    $ufcargo = $value['cargo_ufirma'];
                    $ufarea = $value['area_ufirma'];
                    $ufregional = $value['regional_ufirma'];
                    $urlfmecanica = $value['ufirma_mecanica'];
                }
                //*****************************************************************************************************
                $tmp_codebar = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
                $codebar_radicado = $tmp_codebar . simad_util::generateCodeBarInFile(trim($this->getRadicado()));
                $replacement_images['CODEBAR_COM'] = ['path' => $codebar_radicado, 'wcm' => 150, 'hcm' => 40];
                //*****************************************************************************************************
                $datos = [
                    'FIRMAS_NOMBRE' => $unfirma,
                    'FIRMAS_CARGOS' => $ufcargo,
                    'FIRMAS_DEPENDENCIA' => $ufarea,
                    'FIRMAS_REGIONAL' => $ufregional,
                    'FECHA_COM' => $this->getFechaCreacion("Y-m-d"),
                    'ASUNTO_COM' => trim($this->getAsunto()),
                    'RADICADO_COM' => trim($this->getRadicado()),
                    'ANEXOS_COM' => trim($this->getAnexos()),
                    'VIGENCIA_COM' => $this->getFechaCreacion("Y"),
                    'AREA_CODIGO_COM' => $this->getDependencia()->getCodigo(),
                    'RADICADOR_NOMBRE' => $userscom_data['nombre_radicador'],
                    'RADICADOR_AREA' => $userscom_data['area_uradicador'],
                    'RADICADOR_CARGO' => $userscom_data['cargo_uradicador'],
                    'UPROYECTA_NOMBRE' => $userscom_data['nombre_radicador'],
                    'UPROYECTA_CARGO' => $userscom_data['cargo_uradicador'],
                    'UPROYECTA_AREA' => $userscom_data['area_uradicador']
                ];
                //*****************************************************************************************************
                if (isset($urlfmecanica) && !empty($urlfmecanica)) {
                    $replacement_images['FIRMA_MECANICA'] = ['path' => $urlfmecanica, 'wcm' => 120, 'hcm' => 80];
                } else {
                    $datos['FIRMA_MECANICA'] = "";
                }
                //*****************************************************************************************************
                if (isset($userscom_data['fmecanica_uradicador']) && !empty($userscom_data['fmecanica_uradicador']))
                    $replacement_images['RADICADOR_FMECANICA'] = ['path' => $userscom_data['fmecanica_uradicador'], 'wcm' => 50, 'hcm' => 30];
                else
                    $datos['RADICADOR_FMECANICA'] = "";
                //*****************************************************************************************************
                if (isset($userscom_data['fmecanica_uradicador']) && !empty($userscom_data['fmecanica_uradicador']))
                    $replacement_images['UPROYECTA_FMECANICA'] = ['path' => $userscom_data['fmecanica_uradicador'], 'wcm' => 50, 'hcm' => 30];
                else
                    $datos['UPROYECTA_FMECANICA'] = "";
                //*****************************************************************************************************
                $com_destino = EnviadaDirectorioPeer::getEnviadaDirByRolObject($this->getPrimaryKey());
                if (!empty($com_destino)) {
                    $datos['DESTINO_PREFIJO'] = $com_destino->getDirectorioExterno()->getPrefijo();
                    $datos['DESTINO_NOMBRE'] = $com_destino->getDirectorioExterno()->getNombre();
                    $datos['DESTINO_FUNCIONARIO'] = $com_destino->getDirectorioExterno()->getFuncionario();
                    $datos['DESTINO_EMAIL'] = $com_destino->getDirectorioExterno()->getEmail();
                    $datos['DESTINO_IDENTIFICACION'] = $com_destino->getDirectorioExterno()->getNit();
                    $datos['DESTINO_DIRECCION'] = $com_destino->getDirectorioExterno()->getDireccion();
                    $datos['DESTINO_CIUDAD'] = $com_destino->getDirectorioExterno()->getCiudad()->getNombre();
                    $datos['DESTINO_CARGO'] = $com_destino->getDirectorioExterno()->getCargo();
                }
                //*****************************************************************************************************
                $lstcopias = $userscom_data['copias'];
                $uncopia = "";
                $uccargo = "";
                $ucarea = "";
                foreach ($lstcopias as $value) {
                    $uncopia = $value['nombre_ucopia'];
                    $uccargo = $value['cargo_ucopia'];
                    $ucarea = $value['area_ucopia'];
                }
                //*****************************************************************************************************
                $datos['COPIAS_NOMBRE'] = $uncopia;
                $datos['COPIAS_CARGO'] = $uccargo;
                $datos['COPIAS_DEPENDENCIA'] = $ucarea;
                //***********************************INICIA SECCION DE GESTORES****************************************
                $lstaprobadores = $userscom_data['gestores'];
                $uaprobador_urlfmecanica = "";
                $unaprobador = "";
                $uapcargo = "";
                $uaparea = "";
                //*****************************************************************************************************
                if (count($lstaprobadores) > 1) {
                    $arrayHtml = array();
                    foreach ($lstaprobadores as $value) {
                        $data_list_com = array();
                        $data_list_com['nombre_gestor'] = $value['nombre_gestor'];
                        $data_list_com['cargo_ugestor'] = $value['cargo_ugestor'];
                        $data_list_com['area_ugestor'] = $value['area_ugestor'];
                        $data_list_com['regional_ugestor'] = $value['regional_ugestor'];
                        $data_list_com['ugestor_fmecanica'] = $value['ugestor_fmecanica'];
                        //**********************************************************************************************
                        $arrayHtml[] = $data_list_com;
                    }
                    //**************************************************************************************************
                    $tablaFinal = simad_util::generateTablaHtml($arrayHtml);
                    //**************************************************************************************************
                    $datos['APROBADOR_NOMBRE'] = $tablaFinal;
                } else if (count($lstaprobadores) == 1) {
                    foreach ($lstaprobadores as $value) {
                        $unaprobador = $value['nombre_gestor'];
                        $uapcargo = $value['cargo_ugestor'];
                        $uaparea = $value['area_ugestor'];
                        $uapreg = $value['regional_ugestor'];
                        //***********************************************************************************************
                        $uaprobador_urlfmecanica = '';
                        if (!empty(trim($value['fmecanica_ugestor']))) {
                            $uaprobador_urlfmecanica = trim($value['fmecanica_ugestor']);
                        }
                    }
                    //***************************************************************************************************
                    $datos['APROBADOR_NOMBRE'] = $unaprobador;
                } else {
                    $datos['APROBADOR_NOMBRE'] = "";
                }
                //*******************************************************************************************************
                $datos['APROBADOR_CARGO'] = $uapcargo;
                $datos['APROBADOR_AREA'] = $uaparea;
                $datos['APROBADOR_REGIONAL'] = $uapreg;

                if (!empty($uaprobador_urlfmecanica))
                    $replacement_images['APROBADOR_FMECANICA'] = ['path' => $uaprobador_urlfmecanica, 'wcm' => 50, 'hcm' => 30];
                else
                    $datos['APROBADOR_FMECANICA'] = "";
                //***********************************INICIA SECCION DE REVISORES*****************************************
                $lstrevisores = $userscom_data['revisores'];
                $urevisor_urlfmecanica = "";
                $unrevisa = "";
                $urcargo = "";
                $urarea = "";
                //*******************************************************************************************************
                if (count($lstrevisores) > 1) {
                    $arrayHtml = array();
                    foreach ($lstrevisores as $value) {
                        $data_list_com = array();
                        $data_list_com['nombre_revisor'] = $value['nombre_revisor'];
                        $data_list_com['cargo_urevisor'] = $value['cargo_urevisor'];
                        $data_list_com['area_urevisor'] = $value['area_urevisor'];
                        $data_list_com['urevisor_fmecanica'] = $value['urevisor_fmecanica'];
                        //***********************************************************************************************
                        $arrayHtml[] = $data_list_com;
                    }
                    //***************************************************************************************************
                    $tablaFinal = simad_util::generateTablaHtml($arrayHtml);
                    //***************************************************************************************************
                    $datos['REVISOR_NOMBRE'] = $tablaFinal;
                } else {
                    foreach ($lstrevisores as $value) {
                        $unrevisa = $value['nombre_revisor'];
                        $urcargo = $value['cargo_urevisor'];
                        $urarea = $value['area_urevisor'];
                        //***********************************************************************************************
                        $urevisor_urlfmecanica = '';
                        if (!empty(trim($value['fmecanica_urevisor']))) {
                            $urevisor_urlfmecanica = trim($value['fmecanica_urevisor']);
                        }
                    }
                    //***************************************************************************************************
                    $datos['REVISOR_NOMBRE'] = $unrevisa;
                }
                //*******************************************************************************************************
                $datos['REVISOR_CARGO'] = $urcargo;
                $datos['REVISOR_AREA'] = $urarea;

                if (!empty($urevisor_urlfmecanica))
                    $replacement_images['REVISOR_FMECANICA'] = ['path' => $urevisor_urlfmecanica, 'wcm' => 50, 'hcm' => 30];
                else
                    $datos['REVISOR_FMECANICA'] = "";
                //*******************************************************************************************************
                $docxTpl = $tmp_dir . DIRECTORY_SEPARATOR . 'tpl_' . basename($inputFileName);
                DocxPlaceholderUtil::convertCurlyPlaceholdersToPhpWordTpl($inputFileName, $docxTpl);
                //*******************************************************************************************************
                $tpl = new TemplateProcessor($docxTpl);
                //*******************************************************************************************************
                foreach ($datos as $key => $value) {
                    $tpl->setValue($key,  $value);
                }
                //*******************************************************************************************************
                $pathDefault = simad_paths_app::readConfigFileApp();
                $imgreal_replacement = array();
                foreach ($replacement_images as $key => $row) {
                    $pathreal = null;
                    if (!is_file($row['path']) && !empty($row['path'])) {
                        $pathreal = simad_paths_app::resolveRelativePath($row['path'], $pathDefault);
                    } else if (file_exists($row['path']) && !empty($row['path'])) {
                        $pathreal = $row['path'];
                    }

                    $imgreal_replacement[$key] = ['path' => $pathreal, 'wcm' => $row['wcm'], 'hcm' => $row['hcm']];
                }
                //*******************************************************************************************************
                foreach ($imgreal_replacement as $img_key => $img_value) {
                    if (file_exists($img_value['path'])) {
                        $tpl->setImageValue($img_key, [
                            'path'   => $img_value['path'],
                            'width'  => $img_value['wcm'],
                            'height' => $img_value['hcm'],
                            'ratio'  => true,
                        ]);
                    }
                }
                //*******************************************************************************************************
                $docxFinal = $tmp_dir . DIRECTORY_SEPARATOR . 'convert_' . basename($inputFileName);
                if (file_exists($docxFinal)) {
                    @unlink($docxFinal);
                }

                $tpl->saveAs($docxFinal);
                //*******************************************************************************************************
                if (file_exists($docxFinal)) {
                    @unlink($docxTpl);
                    @unlink($docxSalida);
                    @rename($docxFinal, $docxSalida);
                }
                //*******************************************************************************************************
                $soffice_cli = simad_util::libreOfficeCliPath();
                //*******************************************************************************************************
                $command = sprintf(
                    '"' . $soffice_cli . '" --headless --convert-to pdf --outdir %s %s',
                    escapeshellarg(dirname($pathToSave)),
                    escapeshellarg($docxSalida)
                );
                //*******************************************************************************************************
                @exec($command, $output, $returnVar);
                //*******************************************************************************************************
                if ($returnVar !== 0) {
                    return null;
                }
                //*******************************************************************************************************
                if (file_exists($temp_name)) {
                    @rename($temp_name, $pathToSave);
                    @unlink($docxSalida);
                }
                //*******************************************************************************************************
                return $returnFullPath ? $pathToSave : $outFileName;
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
        } else {
            return null;
        }
        //***************************************************************************************************************
        return $outFileName;
    }

    public function readPlantillaWordAndGenPdf($inputFileName, $returnFullPath = false, $deleteFile = false)
    {
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/bootstrap.php';
        require_once sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/autoload.inc.php';
        $domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/PHPOffice/PHPWord/vendor/dompdf/src');
        Settings::setPdfRenderer(Settings::PDF_RENDERER_DOMPDF, $domPdfPath);
        //$domPdfPath = realpath(sfConfig::get('sf_lib_dir') . '/tcpdf');
        //Settings::setPdfRenderer( Settings::PDF_RENDERER_TCPDF, $domPdfPath );
        //*************************************************************************************************************
        $tmp_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
        $directorio_tmp = simad_util::createPath($tmp_dir);
        //*************************************************************************************************************
        $outFileName = md5(date("YmdGis"));
        $pathToSave = $directorio_tmp . DIRECTORY_SEPARATOR . $outFileName;
        //*************************************************************************************************************
        if (trim($inputFileName)) {
            try {
                //$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($inputFileName);
                //$templateProcessor->saveAs($pathToSave.'.docx');

                $phpWord = \PhpOffice\PhpWord\IOFactory::load($inputFileName);
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $xmlWriter->save($pathToSave . '.pdf', true);
                //*****************************************************************************************************
                return $returnFullPath ? $pathToSave . '.pdf' : $outFileName . '.pdf';
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
            //*********************************************************************************************************
            //if(file_exists($inputFileName) && $deleteFile){ unlink($inputFileName); }
        } else {
            return null;
        }
        //*************************************************************************************************************
        return $outFileName;
    }

    public function getDataEmptyRowFirmas($emptyRows = 2)
    {
        $fila_html = '';
        for ($i = 0; $i < $emptyRows; $i++) {
            $fila_html .= '<tr><td>&nbsp;</td></tr>';
        }
        return $fila_html;
    }

    public function getDataAreasOrCargosFirmas($data_str, $add_td = true)
    {
        if (!count($data_str)) {
            return '';
        }
        $fila_html = '';
        foreach ($data_str as $row_str) {
            if ($add_td) {
                $fila_html .= '<td style="{%class%}">' . $row_str . '</td>';
            } else {
                $fila_html .= $row_str;
            }
        }
        return '<tr>' . $fila_html . '</tr>';
    }

    public function envioEmailRespuesta($email_destino, $attachment, $encabezado_cuerpo = "")
    {
        if (trim($encabezado_cuerpo) == "") {
            $encabezado_cuerpo = "Este es un mensaje para informarle que se le ha enviado la siguiente comunicaci&oacute;n:";
        }
        //********************************************************************************
        $cuerpo = '
        <html>
        <head>
        <title></title>
        </head>
        <body>
        <div id="cotenedor">
        <br>' . $encabezado_cuerpo . '     
        <br>
        <br>        
        <b>Numero Radicado:</b> ' . $this->getRadicado() . '<br>
        <b>Asunto:</b> ' . utf8_encode($this->getAsunto()) . '<br>
        <b>Fecha Radicaci&oacute;n:</b> ' . $this->getFechaCreacion() . ' <br>
        </div>
        </body></html>';
        $cabeceras = "Content-type: text/html; charset=UTF-8\r\n";
        //********************************************************************************
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('CAD : ' . utf8_encode("Comunicación Enviada"));
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($email_destino, $email_destino);
        //if(trim($attachment)){ $baseMail->SetAttachFile($attachment); }
        //********************************************************************************
        $efectiveMail = $baseMail->InitSend();
        //********************************************************************************
        if ($efectiveMail === true) {
            $baseMail->writetolog("Alerta enviada: " . $this->getRadicado() . " Enviado a: " . $email_destino);
        } else {
            $baseMail->writetolog("Error al enviar alerta: " . $this->getRadicado() . " Cuenta correo: " . $email_destino);
        }
        //********************************************************************************
        return $efectiveMail;
    }


    /**
     * @deprecated Reemplazado por singDocumentProcessMulti() [2026-08-12]. 
     * Conservado como respaldo de rollback. Eliminar tras periodo de estabilizacion.
     * objectActions::singDocumentProcessAndes()
     * firma la comunicacion digitalmente
     * @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
     * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
     */
    public function singDocumentProcessAndes($signAllPages = false)
    {
        try {
            $firmaApi = new WsFirmaApiAndes();
            $simadSoap = new WsSimadUariv();
            //***************************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : ParametroPeer::retrieveByPk(29)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
            $filename   = sprintf("%s.%s", trim($this->getRadicado()), 'pdf');
            $tempdir_firma = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . md5(date("YmdGis"));
            $ulist_firma = EnviadaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());
            $isFirmaDigital = count($ulist_firma) ? true : false;
            //***************************************************************************************************			
            if (!empty($this->getUrlFileWord())) {
                $file_attach = $this->SimadGeneratePdf($this->getUrlFileWord(), true, false, $isFirmaDigital);
            } else {
                $margins = array('top' => 4, 'left' => 15, 'buttom' => 30, 'rigth' => 18);
                $file_attach = $this->generateFileInDisk($margins, $isFirmaDigital);
            }
            //***************************************************************************************************
            if (file_exists($file_attach)) {
                $storage_com = $this->getBasicUrlDigitCom($dir_raiz, $digit_dir);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                //***********************************************************************************************
                $response = array();
                $filesing = $file_attach;
                $response_list = array();
                //***********************************************************************************************
                if (WsFirmaApiAndes::SERVICE_ENABLE_WS) {
                    if (count($ulist_firma) <= 0) {
                        $this->setFirmadoDigital(4); //EL DOCUMENTO NO SE FIRMA DIGITAL
                        $this->save();
                        return $response_process = array('httpStatus' => 400, 'message' => 'Ninguno de los usuarios que firman la comunicaci&oacute;n tienen habilitada la firma digital');
                    }
                    //*******************************************************************************************
                    if ($signAllPages) {
                        //$filesing = $firmaApi->addSignVisbleAll($file_attach,$ulist_firma);
                    } else {
                        //$filesing = simad_util::getConvertFileToB64($file_attach);
                    }
                    //*******************************************************************************************
                    //$Xc = 400;$Yc = 700;$Wc = 170;$Hc = 60;
                    $Xc = 5;
                    $Yc = 640;
                    $Wc = 40;
                    $Hc = 120;
                    $fy1 = 100;
                    //*******************************************************************************************
                    $isSingned = true;
                    $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                    foreach ($ulist_firma as $eufirma) {
                        $firma_info = $eufirma->getNombreApellido();
                        //***************************************************************************************
                        $b64firma = sfConfig::get("sf_lib_dir") . DIRECTORY_SEPARATOR . 'efirma' . DIRECTORY_SEPARATOR . WsFirmaApiAndes::FIRMA_VISIBLE_IMAGE;
                        //***************************************************************************************
                        $response_sing = $firmaApi->documentWsApiFirmaAndes($eufirma->getLoginFirma(), base64_decode($eufirma->getPassFirma()), $filesing, $targetpath, $b64firma, $ubicacionFirma);
                        //***************************************************************************************
                        $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                        if (!$response_sing['error']) {
                            $Yc = ($Yc - $fy1);
                            //***********************************************************************************
                            $filesing = $response_sing['filesing'];
                            $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                        } else {
                            $isSingned = false;
                            break;
                        }
                    }
                    //*******************************************************************************************
                    if (!is_file($response_sing['filesing'])) {
                        if ($isSingned) {
                            $isSingned = simad_util::getConvertB64ToFile($response_sing['filesing'], $targetpath);
                        }
                    }
                    //*******************************************************************************************
                    if ($isSingned) {
                        $singOnMsg = 'Documento firmado existosamente!';
                        $response = array('httpStatus' => 200, 'message' => $singOnMsg);
                        $this->setEstadodigitalizacionId(2);
                        $this->setDirDigit($dir_raiz);
                        $this->setFirmadoDigital(1); //FIRMA EXITOSA
                        $this->save();
                    } else {
                        $this->setFirmadoDigital(3); //ERROR SERVIDOR PROVEEDOR FIRMA
                        $this->save();
                        $singOnMsg = 'Ocurrio un error con el proveedor de firma digital(error de archivo1), el documento no se firmo!';
                        //***************************************************************************************
                        foreach ($response_list as $item_error) {
                            if ($item_error['error']) {
                                $singOnMsg .= ", " . $item_error['mesagge'];
                            }
                        }
                        //***************************************************************************************
                        $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                        //***************************************************************************************
                        //if(file_exists($file_attach)){ unlink($file_attach); }
                        //***************************************************************************************
                        return $response_process;
                    }
                    //*******************************************************************************************
                    $tipo_integracion = strtoupper(trim($this->getTipoIntegracion()));
                    //*******************************************************************************************
                    if (empty($tipo_integracion)) {
                        $response_process = $response;
                    } elseif ($tipo_integracion == "DEMANDA") {
                        if (empty($this->getConsecutivoResp())) {
                            $response_process = array('httpStatus' => 400, 'message' => $singOnMsg . ', no se notifico a las herramientas de gestión, porque no se encontro el radicado de entrada');
                        } else {
                            $response_process = $simadSoap->loadWsInfoRadicadoSalida($this->getPrimaryKey(), $this->getConsecutivoResp(), $singOnMsg);
                            $message_info = !empty($response_process['message']) ? trim($response_process['message']) : $singOnMsg;
                            //***********************************************************************************
                            if ($response_process['status'] == 200) {
                                if ($this->getTipoEnvio() == 4) {
                                    $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $message_info);
                                    $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                                    if ($response_acto['status'] == 200) {
                                        //create servicio sgv notificaciones
                                        $response_process = array('httpStatus' => $response_acto['status'], 'message' => $message_info . $message_acto);
                                    } else {
                                        $msgacto_error = sprintf('%s, %s, no se envio el radicado al area de notificaciones', $message_info, $message_acto);
                                        $response_process = array('httpStatus' => 400, 'message' => $msgacto_error);
                                    }
                                } else {
                                    $response_process = array('httpStatus' => 200, 'message' => $message_info);
                                }
                            } else {
                                $message_process = sprintf('%s, Error, NO se envio el radicado a las herramientas de gestión', $message_info);
                                $response_process = array('status' => 400, 'message' => $message_process);
                            }
                        }
                    } elseif ($tipo_integracion == "OFERTA") {
                        $response_process = $this->notificarActoAdminWs($singOnMsg);
                    } else {
                        $response_process = $response;
                    }
                } else {
                    rename($file_attach, $targetpath);
                    $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo digitalmente, servicio deshabilitado';
                    $statusFirma = 400;
                    //$response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                    //********************************************************************************************
                    if ($this->getTipoEnvio() == 4) {
                        $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $singOnMsg, ModulesEnable::ComEnviada);
                        $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                        //FALTA MENEJO DEL ERROR, RETORNAR ERROR SI ES DE INTEGRACION , 
                        if ($response_acto['status'] == 200) {
                            $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg . $message_acto);
                        } else {
                            $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg . $message_acto);
                        }
                    } else {
                        $message_acto = "No se notifico a las herramientas de gesti&oacute;n ";
                        $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg);
                    }
                }
            } else {
                $response_process = array('httpStatus' => 400, 'message' => 'Error al generar el archivo pdf');
            }
            //***************************************************************************************************
            if (file_exists($file_attach)) {
                unlink($file_attach);
            }
            return $response_process;
        } catch (PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,' . $ex->getMessage());
        } catch (\Exception $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        } catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

    /**
     * @deprecated Reemplazado por singDocumentProcessMulti() [2026-08-12]. 
     * Conservado como respaldo de rollback. Eliminar tras periodo de estabilizacion.
     * objectActions::singDocumentProcessGse()
     * firma la comunicacion digitalmente con GSE
     * @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
     * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
     */
    public function singDocumentProcessGse($signAllPages = false)
    {
        try {
            $firmaApi = new WsFirmaApiGse();
            $simadSoap = new WsSimadUariv();
            //***************************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : ParametroPeer::retrieveByPk(29)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
            $filename   = sprintf("%s.%s", trim($this->getRadicado()), 'pdf');
            $tempdir_firma = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . md5(date("YmdGis"));
            $ulist_firma = EnviadaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());
            $isFirmaDigital = count($ulist_firma) ? true : false;
            //***************************************************************************************************			
            if (!empty($this->getUrlFileWord())) {
                $file_attach = $this->SimadGeneratePdf($this->getUrlFileWord(), true, false, $isFirmaDigital);
            } else {
                $margins = array('top' => 4, 'left' => 15, 'buttom' => 30, 'rigth' => 18);
                $file_attach = $this->generateFileInDisk($margins, $isFirmaDigital);
            }
            //***************************************************************************************************
            if ($file_attach != null) {
                $storage_com = $this->getBasicUrlDigitCom($dir_raiz, $digit_dir);
                $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
                //***********************************************************************************************
                $token = $firmaApi->loginWsApiFirmaGse();
                $response = array();
                $filesing_base64 = null;
                $response_list = array();
                //***********************************************************************************************
                if ($token != null) {
                    if (count($ulist_firma) <= 0) {
                        $this->setFirmadoDigital(4); //EL DOCUMENTO NO SE FIRMA DIGITAL
                        $this->save();
                        return $response_process = array('httpStatus' => 400, 'message' => 'Ninguno de los usuarios que firman la comunicaci&oacute;n tienen habilitada la firma digital');
                    }
                    //*******************************************************************************************
                    if ($signAllPages) {
                        $filesing_base64 = $firmaApi->addSignVisbleAll($file_attach, $ulist_firma);
                    } else {
                        $filesing_base64 = simad_util::getConvertFileToB64($file_attach);
                    }
                    //*******************************************************************************************
                    $size_message = strlen($filesing_base64);
                    $isSignHash = ($size_message >= WsFirmaApiGse::MAX_FILE_SIZE_MESSAGE);
                    //*******************************************************************************************
                    $Yc = 680;
                    $Hc = 100;
                    $Xc = 0;
                    $Wc = 40;
                    $fy1 = 100;
                    if ($isSignHash) {
                        $Xc = 0;
                        $Yc = 750;
                        $Wc = 40;
                        $Hc = 640;
                    }
                    //*******************************************************************************************
                    $isSingned = true;
                    $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                    foreach ($ulist_firma as $eufirma) {
                        $firma_info = $eufirma->getNombreApellido();
                        //$temp_firma = md5(uniqid().date("YmdGis"));
                        $temp_firma = $tempdir_firma;
                        $b64firma = simad_util::createImgFirmaDigital($firma_info, $temp_firma);
                        //***************************************************************************************
                        //$logname = sfConfig::get("sf_log_dir").DIRECTORY_SEPARATOR.'efirma_ubicacion.log';
                        //simad_util::writetolog($logname,sprintf("%s => %s => %s",trim($this->getRadicado()),trim($firma_info),implode(",",$ubicacionFirma)));
                        //***************************************************************************************
                        $response_sing = $firmaApi->documentWsApiFirmaGse($eufirma->getLoginFirma(), base64_decode($eufirma->getPassFirma()), $filesing_base64, $token, $targetpath, $b64firma, $ubicacionFirma);
                        //***************************************************************************************
                        $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                        if (!$response_sing['error']) {
                            if ($isSignHash) {
                                $Yc = $Yc - $fy1;
                                $Hc = $Hc - $fy1;
                            } else {
                                $Yc = ($Yc - $fy1);
                            }
                            //***********************************************************************************
                            $filesing_base64 = $response_sing['file_base64'];
                            $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                        } else {
                            $isSingned = false;
                            break;
                        }
                    }
                    //*******************************************************************************************
                    if ($isSingned) {
                        $isSingned = simad_util::getConvertB64ToFile($response_sing['file_base64'], $targetpath);
                    }
                    //*******************************************************************************************
                    if ($isSingned) {
                        $singOnMsg = 'Documento firmado existosamente!';
                        $response = array('httpStatus' => 200, 'message' => $singOnMsg);
                        $this->setEstadodigitalizacionId(2);
                        $this->setFirmadoDigital(1); //FIRMA EXITOSA
                        $this->setDirDigit($dir_raiz);
                        $this->save();
                    } else {
                        $this->setFirmadoDigital(3); //ERROR SERVIDOR PROVEEDOR FIRMA
                        $this->save();
                        $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo!';
                        //***************************************************************************************
                        foreach ($response_list as $item_error) {
                            if ($item_error['error']) {
                                $singOnMsg .= ", " . $item_error['mesagge'];
                            }
                        }
                        //***************************************************************************************
                        $response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                        //***************************************************************************************
                        if (file_exists($file_attach)) {
                            unlink($file_attach);
                        }
                        //***************************************************************************************
                        return $response_process;
                    }
                    //*******************************************************************************************
                    $tipo_integracion = strtoupper(trim($this->getTipoIntegracion()));
                    //*******************************************************************************************
                    if (empty($tipo_integracion)) {
                        $response_process = $response;
                    } elseif ($tipo_integracion == "DEMANDA") {
                        if (empty($this->getConsecutivoResp())) {
                            $response_process = array('httpStatus' => 400, 'message' => $singOnMsg . ', no se notifico a las herramientas de gestión, porque no se encontro el radicado de entrada');
                        } else {
                            $response_process = $simadSoap->loadWsInfoRadicadoSalida($this->getPrimaryKey(), $this->getConsecutivoResp(), $singOnMsg);
                            $message_info = !empty($response_process['message']) ? trim($response_process['message']) : $singOnMsg;
                            //***********************************************************************************
                            if ($response_process['status'] == 200) {
                                if ($this->getTipoEnvio() == 4) {
                                    $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $message_info, ModulesEnable::ComEnviada);
                                    $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                                    if ($response_acto['status'] == 200) {
                                        //create servicio sgv notificaciones
                                        $response_process = array('httpStatus' => $response_acto['status'], 'message' => $message_info . $message_acto);
                                    } else {
                                        $msgacto_error = sprintf('%s, %s, no se envio el radicado al area de notificaciones', $message_info, $message_acto);
                                        $response_process = array('httpStatus' => 400, 'message' => $msgacto_error);
                                    }
                                } else {
                                    $response_process = array('httpStatus' => 200, 'message' => $message_info);
                                }
                            } else {
                                $message_process = sprintf('%s, Error, NO se envio el radicado a las herramientas de gestión', $message_info);
                                $response_process = array('status' => 400, 'message' => $message_process);
                            }
                        }
                    } elseif ($tipo_integracion == "OFERTA") {
                        //$response_process = array();
                        $response_process = $this->notificarActoAdminWs($singOnMsg);
                    } else {
                        $response_process = $response;
                    }
                } else {
                    rename($file_attach, $targetpath);
                    $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo digitalmente';
                    $statusFirma = 400;
                    //$response_process = array('httpStatus' => 400, 'message' => $singOnMsg);
                    //********************************************************************************************
                    if ($this->getTipoEnvio() == 4) {
                        $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $singOnMsg, ModulesEnable::ComEnviada);
                        $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                        //FALTA MENEJO DEL ERROR, RETORNAR ERROR SI ES DE INTEGRACION , 
                        if ($response_acto['status'] == 200) {
                            $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg . $message_acto);
                        } else {
                            $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg . $message_acto);
                        }
                    } else {
                        $message_acto = "No se notifico a las herramientas de gesti&oacute;n ";
                        $response_process = array('httpStatus' => $statusFirma, 'message' => $singOnMsg);
                    }
                }
            } else {
                $response_process = array('httpStatus' => 400, 'message' => 'Error al generar el archivo pdf');
            }
            //***************************************************************************************************
            if (file_exists($file_attach)) {
                unlink($file_attach);
            }
            return $response_process;
        } catch (\PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,' . $ex->getMessage());
        } catch (\Throwable $th) {
            //throw $th;
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }


    /**
     * objectActions::singDocumentProcess()
     * Inicia proceso de firmado digital del documento, usando la integracion
     * con el proveedor de firma digital parametrizado por cada usuario firmante
     * @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
     * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
     */
    public function singDocumentProcess($signAllPages = false)
    {
        try {
            if (!in_array($this->getEstadocomenviadaId(), array(1, 4))) {
                return $this->singDocumentProcessMulti($signAllPages);
            } else {
                return array('httpStatus' => 400, 'message' => 'Este radicado no se puede firmar(no cuenta con un consecutivo de radicación), esta anulado o es un borrador');
            }
        } catch (\PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,' . $ex->getMessage());
        } catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::singDocumentProcessMulti()
     * Firma la comunicacion digitalmente, resolviendo el proveedor de firma
     * (Andes | GSE) por cada usuario firmante, permitiendo documentos mixtos.
     * La cadena de firmas circula en Base64; para Andes (jar que recibe ruta)
     * se materializa un archivo temporal por firma y se normaliza la salida.
     * @param bool $signAllPages indica que se debe adicionarse la firma visible en todas las paginas
     * @return mixed array('httpStatus' => 200|400, 'message' => 'resultado de la operacion de firma')
     */
    public function singDocumentProcessMulti($signAllPages = false)
    {
        try {
            $firmaApiGse = new WsFirmaApiGse();
            $firmaApiAndes = new WsFirmaApiAndes();
            //***************************************************************************************************
            $dir_raiz = !empty($this->getDirDigit()) ? trim($this->getDirDigit()) : ParametroPeer::retrieveByPk(29)->getValortexto();
            $digit_dir  = ParametroPeer::retrieveByPk(13)->getValortexto();
            $filename   = sprintf("%s.%s", trim($this->getRadicado()), 'pdf');
            $tempdir_firma = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . md5(date("YmdGis"));
            $ulist_firma = EnviadaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());
            $isFirmaDigital = count($ulist_firma) ? true : false;
            //***************************************************************************************************
            if (!empty($this->getUrlFileWord())) {
                $file_attach = $this->SimadGeneratePdf($this->getUrlFileWord(), true, false, $isFirmaDigital);
            } else {
                $margins = array('top' => 4, 'left' => 15, 'buttom' => 30, 'rigth' => 18);
                $file_attach = $this->generateFileInDisk($margins, $isFirmaDigital);
            }
            //***************************************************************************************************
            if ($file_attach == null || !file_exists($file_attach)) {
                return array('httpStatus' => 400, 'message' => 'Error al generar el archivo pdf');
            }
            //***************************************************************************************************
            $storage_com = $this->getBasicUrlDigitCom($dir_raiz, $digit_dir);
            $targetpath = $storage_com['storage_path'] . DIRECTORY_SEPARATOR . $filename;
            //***************************************************************************************************
            if (count($ulist_firma) <= 0) {
                $this->setFirmadoDigital(4); //EL DOCUMENTO NO SE FIRMA DIGITAL
                $this->save();
                if (file_exists($file_attach)) {
                    unlink($file_attach);
                }
                return array('httpStatus' => 400, 'message' => 'Ninguno de los usuarios que firman la comunicaci&oacute;n tienen habilitada la firma digital');
            }
            //***************************************************************************************************
            if ($signAllPages) {
                //$filesing_base64 = $firmaApiGse->addSignVisbleAll($file_attach,$ulist_firma);
                $filesing_base64 = simad_util::getConvertFileToB64($file_attach);
            } else {
                $filesing_base64 = simad_util::getConvertFileToB64($file_attach);
            }
            //***************************************************************************************************
            $size_message = strlen($filesing_base64);
            $isSignHash = ($size_message >= WsFirmaApiGse::MAX_FILE_SIZE_MESSAGE);
            //***************************************************************************************************
            $Yc = 680;
            $Hc = 100;
            $Xc = 0;
            $Wc = 40;
            $fy1 = 100;
            if ($isSignHash) {
                $Xc = 0;
                $Yc = 750;
                $Wc = 40;
                $Hc = 640;
            }
            //***************************************************************************************************
            $tokenGse = null; //login perezoso: solo si algun firmante usa GSE, y una sola vez
            $isSingned = true;
            $response_list = array();
            $response_sing = array();
            $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
            //***************************************************************************************************
            foreach ($ulist_firma as $eufirma) {
                $firma_info = $eufirma->getNombreApellido();
                $proveedor  = $this->getProveedorFirmaUsuario($eufirma);
                //***********************************************************************************************
                if ($proveedor == AppApiExternal::FIRMA_GSE) {
                    //*******************************************************************************************
                    if ($tokenGse == null) {
                        $tokenGse = $firmaApiGse->loginWsApiFirmaGse();
                        if ($tokenGse == null) {
                            $response_list[] = array('error' => true, 'mesagge' => 'No fue posible autenticarse con el proveedor GSE', 'usuario' => $firma_info);
                            $isSingned = false;
                            break;
                        }
                    }
                    //*******************************************************************************************
                    $b64firma = simad_util::createImgFirmaDigital($firma_info, $tempdir_firma);
                    $response_sing = $firmaApiGse->documentWsApiFirmaGse($eufirma->getLoginFirma(), base64_decode($eufirma->getPassFirma()), $filesing_base64, $tokenGse, $targetpath, $b64firma, $ubicacionFirma);
                    //*******************************************************************************************
                    $file_result = (!$response_sing['error'] && isset($response_sing['file_base64'])) ? $response_sing['file_base64'] : null;
                } else {
                    $Yc = 640;
                    $Hc = 120;
                    $Xc = 5;
                    $Wc = 40;
                    $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                    //*******************************************************************************************
                    //Andes (jar) trabaja con rutas: materializar el b64 de la cadena en un archivo temporal
                    $tmp_andes = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . md5(uniqid() . date("YmdGis")) . "_andes.pdf";
                    if (!simad_util::getConvertB64ToFile($filesing_base64, $tmp_andes)) {
                        $response_sing = array('error' => true, 'msg_info' => 'No fue posible preparar el archivo temporal para la firma Andes');
                        $file_result = null;
                    } else {
                        $b64firma = sfConfig::get("sf_lib_dir") . DIRECTORY_SEPARATOR . 'efirma' . DIRECTORY_SEPARATOR . WsFirmaApiAndes::FIRMA_VISIBLE_IMAGE;
                        $response_sing = $firmaApiAndes->documentWsApiFirmaAndes($eufirma->getLoginFirma(), base64_decode($eufirma->getPassFirma()), $tmp_andes, $targetpath, $b64firma, $ubicacionFirma);
                        //***************************************************************************************
                        //normalizar la salida del jar (ruta o b64) de vuelta a b64 para la cadena
                        if (!$response_sing['error']) {
                            $file_result = is_file($response_sing['filesing']) ? simad_util::getConvertFileToB64($response_sing['filesing']) : $response_sing['filesing'];
                        } else {
                            $file_result = null;
                        }
                    }
                    //*******************************************************************************************
                    if (file_exists($tmp_andes)) {
                        unlink($tmp_andes);
                    }
                }
                //***********************************************************************************************
                $response_list[] = array('error' => $response_sing['error'], 'mesagge' => $response_sing['msg_info'], 'usuario' => $firma_info);
                //***********************************************************************************************
                if (!$response_sing['error'] && $file_result != null) {
                    if ($isSignHash) {
                        $Yc = $Yc - $fy1;
                        $Hc = $Hc - $fy1;
                    } else {
                        $Yc = ($Yc - $fy1);
                    }
                    //*******************************************************************************************
                    $filesing_base64 = $file_result; //el resultado alimenta la siguiente firma, sin importar el proveedor
                    $ubicacionFirma = array('x' => $Xc, 'y' => $Yc, 'w' => $Wc, 'h' => $Hc);
                } else {
                    $isSingned = false;
                    break;
                }
            }
            //***************************************************************************************************
            if ($isSingned) {
                $isSingned = simad_util::getConvertB64ToFile($filesing_base64, $targetpath);
            }
            //***************************************************************************************************
            if ($isSingned) {
                $singOnMsg = 'Documento firmado existosamente!';
                $response = array('httpStatus' => 200, 'message' => $singOnMsg);
                $this->setEstadodigitalizacionId(2);
                $this->setDirDigit($dir_raiz);
                $this->setFirmadoDigital(1); //FIRMA EXITOSA
                $this->save();
            } else {
                $this->setFirmadoDigital(3); //ERROR SERVIDOR PROVEEDOR FIRMA
                $this->save();
                $singOnMsg = 'Ocurrio un error con el proveedor de firma digital, el documento no se firmo!';
                //***********************************************************************************************
                foreach ($response_list as $item_error) {
                    if ($item_error['error']) {
                        $singOnMsg .= ", [" . $item_error['usuario'] . "] " . $item_error['mesagge'];
                    }
                }
                //***********************************************************************************************
                if (file_exists($file_attach)) {
                    unlink($file_attach);
                }
                return array('httpStatus' => 400, 'message' => $singOnMsg);
            }
            //***************************************************************************************************
            $response_process = $this->procesarIntegracionesFirma($singOnMsg, $response);
            //***************************************************************************************************
            if (file_exists($file_attach)) {
                unlink($file_attach);
            }
            return $response_process;
        } catch (\PropelException $ex) {
            return array('httpStatus' => 400, 'message' => 'Error interno del servidor, Por favor comuniquese con el administrador,' . $ex->getMessage());
        } catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::getProveedorFirmaUsuario()
     * Resuelve el proveedor de firma parametrizado del usuario firmante,
     * via la relacion con la tabla PROVEEDOR_FIRMA_DIGITAL (ENUM_NAME).
     * FK nula, proveedor inactivo o enum desconocido caen a Andes (comportamiento historico)
     * @param mixed $eufirma objeto del usuario firmante
     * @return string AppApiExternal::FIRMA_ANDES | AppApiExternal::FIRMA_GSE
     */
    private function getProveedorFirmaUsuario($eufirma)
    {
        $proveedor = $eufirma->getProveedorFirmaDigital();

        if ($proveedor === null || !$proveedor->getEstaActivo()) {
            return AppApiExternal::FIRMA_ANDES;
        }

        $enum_name = strtoupper(trim((string) $proveedor->getEnumName()));
        return ($enum_name == AppApiExternal::FIRMA_GSE) ? AppApiExternal::FIRMA_GSE : AppApiExternal::FIRMA_ANDES;
    }

    /**
     * objectActions::procesarIntegracionesFirma()
     * Cola comun de integraciones post-firma (DEMANDA | OFERTA | sin integracion).
     * Extraida de singDocumentProcessAndes/Gse, donde era identica en ambos.
     * @param string $singOnMsg mensaje de resultado de la firma
     * @param array $response respuesta base de la operacion de firma
     * @return mixed array('httpStatus' => 200|400, 'message' => '...')
     */
    private function procesarIntegracionesFirma($singOnMsg, $response)
    {
        $simadSoap = new WsSimadUariv();
        $tipo_integracion = strtoupper(trim($this->getTipoIntegracion()));
        //***************************************************************************************************
        if (empty($tipo_integracion)) {
            return $response;
        } elseif ($tipo_integracion == "DEMANDA") {
            if (empty($this->getConsecutivoResp())) {
                return array('httpStatus' => 400, 'message' => $singOnMsg . ', no se notifico a las herramientas de gestión, porque no se encontro el radicado de entrada');
            }
            //***********************************************************************************************
            $response_process = $simadSoap->loadWsInfoRadicadoSalida($this->getPrimaryKey(), $this->getConsecutivoResp(), $singOnMsg);
            $message_info = !empty($response_process['message']) ? trim($response_process['message']) : $singOnMsg;
            //***********************************************************************************************
            if ($response_process['status'] == 200) {
                if ($this->getTipoEnvio() == 4) {
                    $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $message_info);
                    $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                    if ($response_acto['status'] == 200) {
                        //create servicio sgv notificaciones
                        return array('httpStatus' => $response_acto['status'], 'message' => $message_info . $message_acto);
                    }
                    return array('httpStatus' => 400, 'message' => sprintf('%s, %s, no se envio el radicado al area de notificaciones', $message_info, $message_acto));
                }
                return array('httpStatus' => 200, 'message' => $message_info);
            }
            return array('status' => 400, 'message' => sprintf('%s, Error, NO se envio el radicado a las herramientas de gestión', $message_info));
        } elseif ($tipo_integracion == "OFERTA") {
            return $this->notificarActoAdminWs($singOnMsg);
        }
        //***************************************************************************************************
        return $response;
    }

    public function notificarActoAdminWs($message_firma = "")
    {
        try {
            $simadSoap = new WsSimadUariv();
            //********************************************************************************************************
            if ($this->getTipoEnvio() == 4) {
                $response_acto = $simadSoap->loadWsRadActoAdministrativo($this->getPrimaryKey(), $message_firma, ModulesEnable::ComEnviada);
                $message_acto = !empty($response_acto['message']) ? ", " . trim($response_acto['message']) : "";
                if ($response_acto['status'] == 200) {
                    $response_process = array('httpStatus' => $response_acto['status'], 'message' => $message_firma . $message_acto);
                } else {
                    $response_process = array('httpStatus' => 400, 'message' => $message_firma . $message_acto);
                }
            } else {
                $message_acto = "No se notifico a las herramientas de gesti&oacute;n";
                $response_process = array('httpStatus' => 200, 'message' => sprintf('%s, %s', $message_firma, $message_acto));
            }
            //********************************************************************************************************
            return $response_process;
        } catch (\Throwable $th) {
            return array('httpStatus' => 400, 'message' => $th->getMessage());
        }
    }

    public function attachDocumentToServiceCom($nfile, $usuario_attach)
    {
        try {
            if (empty($this->getServicioId())) {
                return null;
            }
            //****************************************************************************
            $path_data = $this->getServicio()->getBasicUrlAttach();
            $servicio_id = $this->getServicioId();
            $obs_integracion = "adjunto por servicios de integracion";
            //$estadoservicio_id = $this->getServicio()->getServicioestadoId();
            $estadoservicio_id = 3;
            //****************************************************************************
            $util_simad = new simad_util();
            $info_file = new SplFileInfo($nfile);
            $filename_new = uniqid() . '_' . $util_simad->clean_name_fileinfo($info_file);
            $directorio = simad_util::createPath($path_data['full_path']);
            $alias_web = ParametroPeer::retrieveByPk(74)->getValortexto();
            $fileweb = $alias_web . $path_data['basic_path'] . '/' . $filename_new;
            $path_target = $directorio . DIRECTORY_SEPARATOR . $filename_new;
            //****************************************************************************
            $isError = false;
            if (copy($nfile, $path_target)) {
                $list_state[] = array('filename' => $nfile, 'info' => 'El archivo se adjunto correctamente', 'isError' => $isError);
                unlink($nfile);
            } else {
                $isError = true;
                $list_state[] = array('filename' => $nfile, 'info' => 'No fue posible copiar el archivo', 'isError' => $isError);
            }
            //****************************************************************************
            ServicioPeer::insertAnexoServicios($servicio_id, $fileweb, $obs_integracion, 0, $usuario_attach, 1);
            ServicioPeer::insertBitacoraServicio($servicio_id, $estadoservicio_id, $usuario_attach, $usuario_attach, $obs_integracion);
            //****************************************************************************
            $idAsignar = AsignarServicioPeer::getLlaveAsignar($servicio_id);
            UsuarioAsignadoSolicitudPeer::updateEstadoAsignado($idAsignar);
            //****************************************************************************
            $this->getServicio()->setServicioestadoId($estadoservicio_id);
            $this->getServicio()->save();
            //****************************************************************************
            if ($estadoservicio_id == 3) {
                $cuerpo  = 'Este es un mensaje para informarle que la solicitud de servicio fue ejecutada y cerrada exitosamente: ';
                $this->getServicio()->envioEmail($this->getServicio()->getUsuarioId(), $obs_integracion, $cuerpo);
            }
            //****************************************************************************
            return array('isError' => $isError, 'list_state' => $list_state);
        } catch (PropelException $th) {
            $list_state = array('filename' => $nfile, 'info' => $th->getMessage(), 'isError' => true);
            return array('isError' => true, 'list_state' => $list_state);
        } catch (\Throwable $th) {
            $list_state = array('filename' => $nfile, 'info' => $th->getMessage(), 'isError' => true);
            return array('isError' => true, 'list_state' => $list_state);
        }
    }

    /**
     * objectActions::addServicioByCom()
     * crea una nueva solicitud de servicio con la informacion de la comunicacion 
     * @return mixed array('isError' => true|false, 'message' => '', 'object' => objeto con los datos del servicio)
     */
    public function addServicioByCom($usuario_servicio, $tiposervicio_id, $intersados_list = array(), $directorioexterno_id = null)
    {
        try {
            $estadoservicio_id = 1;
            $prioridadsolicitudservicio_id = 1;
            $detalle = 'Solicitud servicio de comunicación enviada con radicado ' . $this->getRadicado();
            $modulo_id = 4;
            //****************************************************************************************
            if (empty($tiposervicio_id) || $tiposervicio_id == 0) {
                return array('isError' => true, 'message' => 'El tipo de servicio no se encontro en el SGDEA', 'object' => null);
            }
            //****************************************************************************************
            $servicio_com = array();
            $servicio_com['usuario_id'] = $usuario_servicio;
            $servicio_com['regional_id'] = $this->getRegionalId();
            $servicio_com['directorioexterno_id'] = $directorioexterno_id;
            $servicio_com['tiposervicio_id'] = $tiposervicio_id;
            $servicio_com['estadoservicio_id'] = $estadoservicio_id;
            $servicio_com['prioridadsolicitudservicio_id'] = $prioridadsolicitudservicio_id;
            $servicio_com['detalle'] = $detalle;
            $servicio_com['folios'] = $this->getFolios();
            $servicio_com['coll_interesados'] = $intersados_list;
            //****************************************************************************************
            return ServicioPeer::createServicioByCom($servicio_com, $this->getPrimaryKey(), $modulo_id);
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        }
    }

    /**
     * objectActions::initServicioProcess()
     * inicia el proceso de generacion de un servicio de forma automatica
     * @return mixed
     */
    public function initServicioProcess($usaurio_id = null)
    {
        $response_svc = array('isError' => true, 'message' => 'Ocurrio un error al realizar la automatizacion de la solicitud de servicio');
        //**********************************************************************************************
        try {
            if (empty($this->getPlantillascomId())) {
                return null;
            }
            //*****************************************************************************************
            if (empty($this->getPlantillasCom()->getGeneraServicio())) {
                return null;
            }
            //*****************************************************************************************
            $usuariologuiado = $usaurio_id == null ? sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber') : $usaurio_id;
            //$listalldep = sfContext::getInstance()->getUser()->checkPerm('TRD_LISTAR_DEPENDENCIAS_TODAS_ENTIDADES', $usuariologuiado);
            //*****************************************************************************************
            $dependencia_id = $this->getDependenciaId();
            $response_svc = $this->addServicioBulkByCom($usuariologuiado, $dependencia_id);
            //*****************************************************************************************
            /*if($listalldep){
                $dependencia_id = $this->getDependenciaId();
                $response_svc = $this->addServicioBulkByCom($usuariologuiado,$dependencia_id);
            }else{
                $list_firmantes = EnviadaUsuarioPeer::getAllUserFirmaDigitalObj($this->getPrimaryKey());
                $dependencia_id = $list_firmantes[0]->getDependenciaId();
                $response_svc = $this->addServicioBulkByCom($usuariologuiado,$dependencia_id);
            }*/
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        } catch (\Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'object' => null);
        }
        //**********************************************************************************************
        return $response_svc;
    }

    /**
     * objectActions::addServicioBulkByCom()
     * crea una nueva solicitud de servicio por cada interesado o entidad de destino
     * @return mixed array('isError' => true|false, 'message' => '', 'object' => objeto con los datos del servicio)
     */
    public function addServicioBulkByCom($usuario_origen, $dependencia_id)
    {
        try {
            $intersados_list = ComEnviadaPeer::getListIntersadosByComId($this->getPrimaryKey());
            $coll_interesados = array();
            foreach ($intersados_list as $row) {
                $coll_interesados[] = $row->getInteresados()->getInteresadoId();
            }
            //****************************************************************************************
            $enviada_directorio = EnviadaDirectorioPeer::getEnviadaDirByRolObject($this->getPrimaryKey());
            $directorioexterno_id = $enviada_directorio != null ? $enviada_directorio->getDirectorioexternoId() : null;
            //****************************************************************************************
            $param_tplsrv = $this->getAutomaticServicioInfo();
            //****************************************************************************************
            $estadoservicio_id = 1;
            $detalle = 'Solicitud servicio de comunicación enviada con radicado ' . $this->getRadicado();
            //****************************************************************************************
            if (!isset($param_tplsrv["basic_data"]) || empty($param_tplsrv["basic_data"])) {
                return array('isError' => true, 'message' => 'El tipo de servicio no se encontro en el SGDEA', 'object' => null);
            }
            //****************************************************************************************
            $basic_data = $param_tplsrv["basic_data"];
            $messages_error = "";
            //****************************************************************************************
            $servicio_com = array();
            $servicio_com['usuario_id'] = $usuario_origen;
            $servicio_com['regional_id'] = $this->getRegionalId();
            $servicio_com['directorioexterno_id'] = $directorioexterno_id;
            $servicio_com['tiposervicio_id'] = $basic_data['tiposervicio_id'];
            $servicio_com['estadoservicio_id'] = $estadoservicio_id;
            $servicio_com['prioridadsolicitudservicio_id'] = $basic_data['prioridadservicio_id'];
            $servicio_com['detalle'] = $detalle;
            $servicio_com['folios'] = $this->getFolios();
            $servicio_com['coll_interesados'] = $coll_interesados;
            $servicio_com['dependencia_id'] = $dependencia_id;
            //****************************************************************************************
            $resp_srv = ServicioPeer::createServicioByCom($servicio_com, $this->getPrimaryKey(), ModulesEnable::ComEnviada);
            //****************************************************************************************
            if ($resp_srv['isError'] == false) {
                $servicio = $resp_srv["object"];
                //************************************************************************************
                $params['coll_interesados'] = $coll_interesados;
                $params['dependencia_id'] = $dependencia_id;
                //************************************************************************************
                $resp_integra = $servicio->initIntegraciones($params);
                //************************************************************************************
                return array('isError' => $resp_integra['isError'], 'message' => $resp_integra['message']);
            } else {
                return array('isError' => true, 'message' => "Error creando la solicitud de servicio, {$resp_srv['message']}");
            }
            //****************************************************************************************
            return array('isError' => true, 'message' => $messages_error);
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (\Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::getAutomaticServicioInfo()
     * obtiene la informacion parametrizada en el servicio de plantillas automatizado
     * @return mixed array('isError' => true|false, 'message' => '', 'basic_data' => objeto con los datos basicos del servicio)
     */
    public function getAutomaticServicioInfo()
    {
        try {
            if (empty($this->getPlantillascomId())) {
                return array('isError' => true, 'message' => 'La comunicacion no se genero con plantilla');
            }
            //****************************************************************************************
            if (empty($this->getPlantillasCom()->getGeneraServicio())) {
                return array('isError' => true, 'message' => 'El tipo de plantilla no tiene automatizada la generacion del servicio');
            }
            //****************************************************************************************
            $config_data = PlantillascomServicioPeer::getPlantillaComByTipoServicio($this->getPlantillascomId());
            if (empty($config_data)) {
                return array('isError' => true, 'message' => 'Error de configuracion en el tipo de plantilla, no se encuentra el tipo de servicio');
            }
            //****************************************************************************************
            $basic_data['tiposervicio_id'] = $config_data->getTiposervicioId();
            $basic_data['prioridadservicio_id'] = $config_data->getPrioridadsolicitudservicioId();
            //****************************************************************************************
            return array('isError' => false, 'message' => 'Proceso realizado con exito', 'basic_data' => $basic_data);
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (\Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (\Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::comNumResByIntIsCreate()
     * valida el numero de la resolucion para los interesados de la comunicacion
     * @return mixed array('isError' => true|false, 'message' => '')
     */
    public function comNumResByIntIsCreate($interesados_comlist)
    {
        try {
            if (!count($interesados_comlist)) {
                return array('isError' => false, 'message' => '');
            }
            //****************************************************************************************
            if (!empty($this->getNumeroResolucion())) {
                $interesados_com = InteresadosPeer::retrieveByPKs($interesados_comlist);
                $interesados_nuids = array();
                foreach ($interesados_com as $item) {
                    $interesados_nuids[] = $item->getNumeroIdentificacion();
                }

                $existsComByResol = ComEnviadaPeer::isExistComByNumResolucion($this->getNumeroResolucion(), $interesados_nuids, $this->getPrimaryKey());
                if ($existsComByResol) {
                    $this->setNumeroResolucion(null);
                    $this->setFechaResolucion(null);
                    return array('isError' => true, 'message' => 'El número de resolución para el interesado ya existe en el SGDEA');
                }
            } else {
                return array('isError' => false, 'message' => '');
            }
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        } catch (Throwable $th) {
            return array('isError' => true, 'message' => $th->getMessage());
        }
    }

    /**
     * objectActions::addComByInteresado()
     * Crea una nueva comunicacion basado en un objeto de una comunicacion
     * @return mixed array('isError' => true|false, 'message' => '','list_com' => pks objects)
     */
    public function addComByInteresado($entidad_id, $regional, $depen_codigo)
    {
        try {
            $list_interesados = ComEnviadaPeer::getListIntersadosByComId($this->getPrimaryKey());
            $lenviada_usuarios = EnviadaUsuarioPeer::getListObjectsUserByCom($this->getPrimaryKey());
            $a = 1;
            $list_com[] = $this->getPrimaryKey();
            $estadocomenviada_id = $this->getEstadocomenviadaId();
            //***********************************************************************************************************
            if (count($list_interesados)) {
                do {
                    $new_comenviada = new ComEnviada();
                    $new_comenviada = $this->copy();
                    $use_membrete = $this->getUseMembrete();
                    $new_comenviada->setFechaCreacion(date("Y-m-d G:i:s"));
                    $radicado = $new_comenviada->getRadicadoFormat($entidad_id, $regional, $depen_codigo);
                    $new_comenviada->setRadicado($radicado);
                    $new_comenviada->setUseMembrete($use_membrete);
                    $new_comenviada->save();
                    //*******************************************************************************************************
                    foreach ($lenviada_usuarios as $usuario_comenviada) {
                        $new_usuario_comenviada = $usuario_comenviada->copy();
                        $new_usuario_comenviada->setComenviadaId($new_comenviada->getPrimaryKey());
                        $new_usuario_comenviada->setFechaAsigna(date("Y-m-d G:i:s"));
                        $new_usuario_comenviada->setEstadocomenviadaid($estadocomenviada_id);
                        $new_usuario_comenviada->save();
                        if ($usuario_comenviada->getRolusComenviadaId() == 2) {
                            EnviadaUsuarioPeer::updateAproFirma($new_comenviada->getPrimaryKey());
                        }
                    }
                    //*******************************************************************************************************
                    $com_interesado = new EnviadaInteresados();
                    $com_interesado = $list_interesados[$a];
                    $com_interesado->setComenviadaId($new_comenviada->getPrimaryKey());
                    $com_interesado->save();
                    //*******************************************************************************************************
                    $list_com[] = $new_comenviada->getPrimaryKey();
                    $a++;
                } while ($a < count($list_interesados));
            }
            //***************************************************************************************************************
            return array('isError' => false, 'message' => 'Las comunicaciones se radicaron existosamente', 'list_com' => $list_com);
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'list_com' => null);
        } catch (Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'list_com' => null);
        }
    }

    /**
     * objectActions::addComByDirectorioExt()
     * Crea una nueva comunicacion basado en un objeto de una comunicacion, por cada pk del directorio corporativo asociado a la comunicacion 
     * @param $list_comdirdestino lista de pks del directorio corporativo asociados a la comunicacion
     * @param $firmante_id pk de primer usuario que firma la comunicacion
     * @param $entidad_id pk de la entidad a la cual pertenece la comunicacion
     * @param $regional pk de la regional a la cual pertenece la comunicacion
     * @param $depen_codigo codigo de la dependencia a la cual pertenece la comunicacion
     * @return mixed array('isError' => true|false, 'message' => '','list_com' => pks objects)
     */
    public function addComByDirectorioExt($list_comdirdestino, $firmante_id, $entidad_id, $regional, $depen_codigo)
    {
        try {
            $list_interesados = ComEnviadaPeer::getListIntersadosByComId($this->getPrimaryKey());
            $lenviada_usuarios = EnviadaUsuarioPeer::getListObjectsUserByCom($this->getPrimaryKey());
            $estadocomenviada_id = $this->getEstadocomenviadaId();
            $list_com[] = $this->getPrimaryKey();

            unset($list_comdirdestino[0]); //elimina del array el primer objeto
            foreach ($list_comdirdestino as $ndircom) {
                $unewcom_enviada = new ComEnviada();
                $unewcom_enviada = $this->copy();

                $unewcom_enviada->setFechaCreacion(date("Y-m-d G:i:s"));
                $radicado = $unewcom_enviada->getRadicadoFormat($entidad_id, $regional, $depen_codigo);
                $unewcom_enviada->setRadicado($radicado);
                $unewcom_enviada->setNumeroResolucion(null);
                $unewcom_enviada->setFechaResolucion(null);
                $unewcom_enviada->setContenidodocId(null);
                $unewcom_enviada->save();

                foreach ($lenviada_usuarios as $ucom_object) {
                    $ucomobject_new = new EnviadaUsuario();
                    $ucomobject_new = $ucom_object->copy();
                    $ucomobject_new->setComenviadaId($unewcom_enviada->getPrimaryKey());
                    $ucomobject_new->setEstadocomenviadaId($estadocomenviada_id);
                    if ($ucomobject_new->getRoluscomenviadaId() != 1) {
                        $ucomobject_new->setFechaAsigna(date("Y-m-d G:i:s"));
                        $ucomobject_new->setEstaAsignada(0);
                        $ucomobject_new->setFechaAprueba(date("Y-m-d G:i:s"));
                        $ucomobject_new->setFirmaAprueba(1);
                    } else {
                        $ucomobject_new->setEstaAsignada(1);
                    }
                    $ucomobject_new->save();
                }

                foreach ($list_interesados as $comint_object) {
                    $comintobject_new = new EnviadaInteresados();
                    $comintobject_new = $comint_object->copy();
                    $comintobject_new->setComenviadaId($unewcom_enviada->getPrimaryKey());
                    $comintobject_new->save();
                }
                //*******************************************************************************************************
                $ndircom->setComenviadaId($unewcom_enviada->getPrimaryKey());
                $ndircom->save();

                $list_com[] = $unewcom_enviada->getPrimaryKey();
                //*******************************************************************************************************
                if (!empty($unewcom_enviada->getExpedienteId()) && !empty($unewcom_enviada->getTipoDocumentalCod())) {
                    $origentransfer_id = 3;
                    $unewcom_enviada->addNewTransferenciaAndContenido($origentransfer_id, $firmante_id);
                }
            }
            //***************************************************************************************************************
            return array('isError' => false, 'message' => 'Las comunicaciones se radicaron existosamente', 'list_com' => $list_com);
        } catch (PropelException $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'list_com' => null);
        } catch (Exception $th) {
            return array('isError' => true, 'message' => $th->getMessage(), 'list_com' => null);
        }
    }

    /**
     * objectActions::initUserNotifications()
     * Envia notificaciones por usuario cnfiguradas en la cuenta de los firmantes
     * @param $list_com lista de pks de comunicaciones a notificar
     * @return void
     */
    public function initUserNotifications($list_com = array())
    {
        $listc_user = $this->getUsuariosListComIds(true);
        if (isset($listc_user['firmas'])) {
            $list_com[] = $this->getPrimaryKey();
            $list_com = count($list_com) > 1 ? array_unique($list_com) : $list_com;
            foreach ($list_com as $pkcom_id) {
                UsuarioNotificacionPeer::notifyUserTask($listc_user['firmas'], ModuleEnableNotify::ComEnviada, $pkcom_id, RolComTypeNotify::FirmaDocsEnviada);
            }
        }
    }

    /**
     * objectActions::setMergeFiledsCom()
     * realiza la combinacion de correspondencia segun los campos configurados en la plantilla    
     * @return void
     */
    public function setMergeFiledsCom()
    {
        try {
            //$fields_cods = PlantillasComPeer::getFieldsCodByTemplate();
            $userscom_data = $this->getUsuariosListComObjs();
            $contenido_merge = trim($this->getContenido());
            $base_path = sfConfig::get('publicUrl');
            //*******************************************************************************************************
            $lstfirmas = $userscom_data['firmas'];
            $unfirma = "";
            $ufcargo = "";
            $ufarea = "";
            $ufmecanica = "";
            $ufregional = "";
            foreach ($lstfirmas as $key => $value) {
                $unfirma = $value['nombre_ufirma'];
                $ufcargo = $value['cargo_ufirma'];
                $ufarea = $value['area_ufirma'];
                $ufregional = $value['regional_ufirma'];
                $ufmecanica = '<img src="' . trim($value['ufirma_mecanica']) . '" min-height="80px" max-height="150px" width="250px">';
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[FIRMAS_NOMBRE]}", $unfirma, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_CARGOS]}", $ufcargo, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_DEPENDENCIA]}", $ufarea, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMA_MECANICA]}", $ufmecanica, $contenido_merge);
            $contenido_merge = str_replace("{[FIRMAS_REGIONAL]}", $ufregional, $contenido_merge);
            //*******************************************************************************************************
            $com_destino = EnviadaDirectorioPeer::getEnviadaDirByRolObject($this->getPrimaryKey());
            if (!empty($com_destino)) {
                $contenido_merge = str_replace("{[DESTINO_PREFIJO]}", $com_destino->getDirectorioExterno()->getPrefijo(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_NOMBRE]}", $com_destino->getDirectorioExterno()->getNombre(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_FUNCIONARIO]}", $com_destino->getDirectorioExterno()->getFuncionario(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_EMAIL]}", $com_destino->getDirectorioExterno()->getEmail(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_IDENTIFICACION]}", $com_destino->getDirectorioExterno()->getNit(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_DIRECCION]}", $com_destino->getDirectorioExterno()->getDireccion(), $contenido_merge);
                $contenido_merge = str_replace("{[DESTINO_CIUDAD]}", $com_destino->getDirectorioExterno()->getCiudad()->getNombre(), $contenido_merge);
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[RADICADOR_NOMBRE]}", $userscom_data['nombre_radicador'], $contenido_merge);
            $contenido_merge = str_replace("{[RADICADOR_DEPENDENCIA]}", $userscom_data['area_uradicador'], $contenido_merge);
            //*******************************************************************************************************
            $lstcopias = $userscom_data['copias'];
            $uncopia = "";
            $uccargo = "";
            $ucarea = "";
            foreach ($lstcopias as $key => $value) {
                $uncopia = $value['nombre_ucopia'];
                $uccargo = $value['cargo_ucopia'];
                $ucarea = $value['area_ucopia'];
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[COPIAS_NOMBRE]}", $uncopia, $contenido_merge);
            $contenido_merge = str_replace("{[COPIAS_CARGO]}", $uccargo, $contenido_merge);
            $contenido_merge = str_replace("{[COPIAS_DEPENDENCIA]}", $ucarea, $contenido_merge);
            //*******************************************************************************************************
            $lstrevisores = $userscom_data['revisores'];
            $unrevisa = "";
            $urcargo = "";
            $urarea = "";
            foreach ($lstrevisores as $key => $value) {
                $unrevisa = $value['nombre_revisor'];
                $urcargo = $value['cargo_urevisa'];
                $urarea = $value['area_urevisa'];
                $urfmecanica = $value['fmecanica_urevisor'];
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[REVISOR_NOMBRE]}", $unrevisa, $contenido_merge);
            $contenido_merge = str_replace("{[REVISOR_CARGO]}", $urcargo, $contenido_merge);
            $contenido_merge = str_replace("{[REVISOR_DEPENDENCIA]}", $urarea, $contenido_merge);
            $contenido_merge = str_replace("{[REVISOR_FMECANICA]}", '<img src="' . trim($urfmecanica) . '" height="30px" width="50px">', $contenido_merge);
            //*******************************************************************************************************
            $lstaprobadores = $userscom_data['gestores'];
            $unaprobador = "";
            $uapcargo = "";
            $uaparea = "";
            foreach ($lstaprobadores as $key => $value) {
                $unaprobador = $value['nombre_gestor'];
                $uapcargo = $value['cargo_ugestor'];
                $uaparea = $value['area_ugestor'];
            }
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[APROBADOR_NOMBRE]}", $unaprobador, $contenido_merge);
            $contenido_merge = str_replace("{[APROBADOR_CARGO]}", $uapcargo, $contenido_merge);
            $contenido_merge = str_replace("{[APROBADOR_DEPENDENCIA]}", $uaparea, $contenido_merge);
            //*******************************************************************************************************
            $contenido_merge = str_replace("{[ANEXOS_COM]}", $this->getAnexos(), $contenido_merge);
            $contenido_merge = str_replace("{[FECHA_COM]}", $this->getFechaCreacion(), $contenido_merge);
            $contenido_merge = str_replace("{[ASUNTO_COM]}", $this->getAsunto(), $contenido_merge);
            $contenido_merge = str_replace("{[RADICADO_COM]}", $this->getRadicado(), $contenido_merge);
            //*******************************************************************************************************
            $codebar_radicado = simad_util::generateCodeBarInFile(trim($this->getRadicado()));
            $contenido_merge = str_replace("{[CODEBAR_COM]}", '<img style="width:80px; height:auto;" src="' . $base_path . '/tmp/' . $codebar_radicado . '"/>', $contenido_merge);
            //*******************************************************************************************************
            return $contenido_merge;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        }
    }

    /**
     * objectActions::getComIsArchivedExpediente()
     * valida si la comunicacion esta archivada
     * @return void
     */
    public function getComIsArchivedExpediente()
    {
        try {
            if (!empty($this->getMarcaVinculacion())) {
                $expediente_info = array();
                //********************************************************************************************
                $contenidodoc_id = $this->getContenidodocId();
                $contenido_unidad_documental = ContenidoUnidadDocumentalPeer::retrieveByPk($contenidodoc_id);
                //********************************************************************************************
                if ($contenido_unidad_documental == null) {
                    //no se encontro el contenido documental
                    return array('existe_expediente' => false, 'titulo_expediente' => null);
                }
                //********************************************************************************************
                $unidaddocumental_id = $contenido_unidad_documental->getUnidaddocumentalId();
                $unidad_documental = $contenido_unidad_documental->getUnidadDocumental();
                if ($unidad_documental == null) {
                    //no se encontro el tipo documental
                    return array('existe_expediente' => false, 'titulo_expediente' => null);
                }
                //********************************************************************************************
                $subserie = $unidad_documental->getSubserie();
                if ($subserie == null) {
                    //no se encontro la subserie
                    return array('existe_expediente' => false, 'titulo_expediente' => null);
                }
                //********************************************************************************************
                $expediente_info['existe_expediente'] = true;
                $expediente_info['titulo_expediente'] = $unidad_documental->getCodigoTitulo();
                $expediente_info['subserie_expediente'] = $subserie->getDescripcion();
                $expediente_info['fase_archivo'] = $unidad_documental->getLocalizacionunidaddocumentalId();
                $expediente_info['unidaddocumental_id'] = $unidaddocumental_id;
                //********************************************************************************************                
                return $expediente_info;
            }
        } catch (PropelException $th) {
            return array('existe_expediente' => false, 'titulo_expediente' => null);
        } catch (\Exception $th) {
            return array('existe_expediente' => false, 'titulo_expediente' => null);
        } catch (\Throwable $th) {
            return array('existe_expediente' => false, 'titulo_expediente' => null);
        }
    }

    /**
     * ActoAdministrativo::getAttachmentCom()
     * Obtiene los anexos del registro
     * @return mixed array(ruta_archivos)
     */
    public function getAttachmentCom()
    {
        try {
            $attach_url = array();
            $pathtmp = simad_util::createPath(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);
            $rootUrl = sfConfig::get('localUrl');
            //****************************************************************************************
            if (in_array($this->getEstadocomenviadaId(), array(2, 3))) {
                $files_adjuntos = preg_split("/[,]+/", $this->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
                //************************************************************************************
                for ($j = 0; $j < count($files_adjuntos); $j++) {
                    if (trim($files_adjuntos[$j])) {
                        try {
                            $filename = basename($files_adjuntos[$j]);
                            echo $fileurl = $rootUrl . $files_adjuntos[$j];

                            if (@file_exists($pathtmp . $filename)) {
                                @unlink($pathtmp . $filename);
                            }

                            $ch = curl_init($fileurl);
                            $fp = fopen($pathtmp . $filename, 'wb');
                            curl_setopt($ch, CURLOPT_FILE, $fp);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                            curl_exec($ch);

                            $tamanoDescargado = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

                            curl_close($ch);
                            fclose($fp);

                            // Verificar integridad del archivo
                            if (!simad_util::verificarIntegridadArchivo($pathtmp . $filename, $tamanoDescargado)) {
                                @unlink($pathtmp . $filename);
                                continue;
                            }

                            if (file_exists($pathtmp . $filename)) {
                                $attach_url[] = $pathtmp . $filename;
                            }
                        } catch (\Exception $th) {
                            //throw $th;
                        }
                    }
                }
            }
        } catch (PropelException $th) {
            $attach_url = null;
        } catch (\Exception $th) {
            $attach_url = null;
        }
        //*********************************************************************************************
        return $attach_url;
    }
}
