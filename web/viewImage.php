<?php
require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);
//***********************************************************************************************
$url_redirect = "/backend.php/inicio/errorFile";
$base_path = !empty(trim(sfConfig::get('base_simad'))) ? sfConfig::get('base_simad') : sfConfig::get('publicUrl');
$localUrl = sfConfig::get('localUrl');
//***********************************************************************************************
$tipocom = $_GET['tipocom'];
$pkcomId = $_GET['key_id'];
$token = $_GET['vtoken'];
$xguid = $_GET['xguid'];
$attachment = isset($_GET['attachment']) ? $_GET['attachment'] : null;
$genpdf = isset($_GET['nvar']) ? $_GET['nvar'] : null;
$object_info = array();
$radicado_object = null;
//***********************************************************************************************
try {
    if (!empty($token)) {
        switch ($tipocom) {
            case 1: //com_enviada
                $object_info = ComEnviadaPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                $radicado_object = $object_info->getRadicado();
                break;
            case 2: //com_recibida
                $object_info = ComRecibidaPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                $radicado_object = $object_info->getRadicado();
                break;
            case 3: //com_interna
                $object_info = ComInternaPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                $radicado_object = $object_info->getRadicado();
                break;
            case 4: //servicio
                $object_info = ServicioPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                break;
            case 5: //archivo
                $object_info = ContenidoUnidadDocumentalPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                break;
            case 6: //actos administrativos
                $object_info = ActoAdministrativoPeer::retrieveByPk($pkcomId);
                $fecha_creacion = $object_info->getFechaCreacion();
                $comobject_id = $object_info->getPrimaryKey();
                $radicado_object = $object_info->getRadicadoCompuesto();
                break;
            default:
                $fecha_creacion = null;
                $comobject_id = null;
                break;
        }
        //***************************************************************************************
        $file_response = array();
        $filegenerate = "";
        if (simad_util::hasTokenIsTrust($token, $comobject_id, $fecha_creacion)) {
            if ($genpdf == md5("newgenpdf")) {
                if ($tipocom == 1 || $tipocom == 6) {
                    if (!empty($object_info->getUrlFileWord())) {
                        $filegenerate = $object_info->SimadGeneratePdf(trim($object_info->getUrlFileWord()), true);
                    } else {
                        $filegenerate = $object_info->generateFileInDisk();
                    }
                }
                //********************************************************************************
                $file_response = array('NOMBRE_ARCHIVO' => basename($filegenerate), 'URL' => trim($filegenerate), 'TIPO_ATTACHMENT' => 'DIGIT_COM');
            } elseif ($tipocom == 5) {
                $file_response = $object_info->getDigitDocumentByXguid($xguid);
            } else {
                $file_response = $object_info->getDigitDocumentByXguid($xguid);
            }
            //************************************************************************************
            $isUrlDownload = false;
            $local_url = "";
            if (count($file_response) && !empty($file_response['URL'])) {
                // Initialize a file URL to the variable
                $url = $file_response['URL'];
                //$file_name = $file_response['NOMBRE_ARCHIVO'];
                $efile_ext = pathinfo($file_response['NOMBRE_ARCHIVO'], PATHINFO_EXTENSION);
                $filename = md5(time() . uniqid()) . '.' . $efile_ext;
                $tmpfile = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $filename;
                //*******************************************************************************
                // Use file_get_contents() function to get the file
                // from url and use file_put_contents() function to
                // save the file by using base name
                if ($tipocom == 5) {
                    $url_interna = $url;
                } else {
                    $local_url = $localUrl . $url;
                    $url_interna = $base_path . $url;
                }
                //*******************************************************************************
                if ($file_response['TIPO_ATTACHMENT'] == 'DIGIT_COM') {
                    $url_interna = $url;
                    if (!empty($attachment)) {
                        $tdownload = md5('attachment' . $radicado_object);
                        if ($tdownload == $attachment) {
                            $isUrlDownload = true;
                        }
                    }
                }
                //*******************************************************************************
                $http_host = $base_path;
                $ssl = $_SERVER["HTTPS"];
                if ($file_response['TIPO_ATTACHMENT'] == 'ANEXO') {
                    if (empty($base_path)) {
                        $http = $ssl == "off" ? "http://" : "https://";
                        $http_host = $http . $_SERVER['HTTP_HOST'];
                    }
                }
                //*******************************************************************************
                if (file_put_contents($tmpfile, file_get_contents($url_interna))) {
                    $extension = pathinfo($tmpfile, PATHINFO_EXTENSION);
                    $url_redirect = $base_path . '/tmp/' . ($filename);
                    if (strtolower($extension) == 'pdf') {
                        if (!$isUrlDownload) {
                            $url_redirect = $base_path . '/viewerEx.php?fileview=' . ($filename);
                        }
                    }
                } else if (file_put_contents($tmpfile, file_get_contents($local_url))) {
                    $extension = pathinfo($tmpfile, PATHINFO_EXTENSION);
                    $url_redirect = $base_path . '/tmp/' . ($filename);
                    if (strtolower($extension) == 'pdf') {
                        if (!$isUrlDownload) {
                            $url_redirect = $base_path . '/viewerEx.php?fileview=' . ($filename);
                        }
                    }
                }
            }
        } else {
            $url_redirect = "/backend.php/inicio/errorToken";
        }
    }
    //*******************************************************************************************
    header("Location: $url_redirect");
} catch (\IOException $th) {
    $response_process['message'] = $th->getMessage();
} catch (\Exception $th) {
    $response_process['message'] = $th->getMessage();
} catch (\Throwable $th) {
    $response_process['message'] = $th->getMessage();
}
