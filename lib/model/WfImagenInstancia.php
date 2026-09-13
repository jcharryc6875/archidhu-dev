<?php

/**
 * Subclass for representing a row from the 'wf_imagen_instancia' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfImagenInstancia extends BaseWfImagenInstancia
{
	 public static function resolveWfUrlUpload($ruta_uploads){
        //MANEJO DE ARCHIVOS UPLAODS
        if(trim($ruta_uploads)){
            $archivos_upload = preg_split("/[,]+/", trim($ruta_uploads),-1,PREG_SPLIT_NO_EMPTY);
            $cantidad_archivos = count($archivos_upload);
            $ruta_archivo = array();
            $str_files = "";
            /********************************************************************************/
            //RUTA Y ESTRUCTURA DE ARCHIVOS
            $dirRaiz = ParametroPeer::retrieveByPk(17);
            $dirAlias = ParametroPeer::retrieveByPk(28)->getValortexto();
            $dirTemp = ParametroPeer::retrieveByPk(65);
            $dirWorkflow = "AdjWorkFolw";
            /********************************************************************************/
            $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $usuario = UsuarioPeer::retrieveByPK($usuariologuiado);
            $entidad_folder = $usuario->getRegional()->getEntidad()->getDirectorioName();
            $regional_folder = $usuario->getRegional()->getDirectorioName();
            /********************************************************************************/
            $entidad_text = $entidad_folder.'/'.$regional_folder;
            $directorio_entidad = $dirRaiz->getValortexto() . $entidad_folder;
            $directorio_tmp = $directorio_entidad."/".$dirTemp->getValortexto()."/";
            $path_target = $dirRaiz->getValortexto().$entidad_text."/".$dirWorkflow;
            /********************************************************************************/
            //$nombre_archivo = "";
            foreach($archivos_upload as $file_upload){
            	if(trim($file_upload) != ""){
            		$directorio = WfImagenInstancia::createPath($path_target);
            		if(copy($directorio_tmp.$file_upload,$directorio."/".$file_upload)){
            			//$ruta_archivo .= $this->StrEndsWith($ruta_archivo, ",") ? "" : ",";
            			$ruta_archivo[] = $dirAlias.$entidad_text."/".$dirWorkflow."/".$file_upload;
            			unlink($directorio_tmp.$file_upload);
            		}else{
            			$msgerror[] =  "Archivo no encontrado ".$file_upload;
            		}
            	}
            }
            //********************************************************************************
            if(count($ruta_archivo)){
                $str_files = implode(",",$ruta_archivo);
            }
            //********************************************************************************
        }
        return $str_files;
    }
    
    public static function  createPath($cadena){
    	return simad_util::createPath($cadena);
	}//function createPath
}
