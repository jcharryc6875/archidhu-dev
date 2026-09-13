<?php

/**
 * @author 
 * @copyright 2008
 */

class wf_widgets{
	
	
	public function getButton($straccion, $strurl , $strdescripcion="", $app_name="administracion.php",$valida_form="" ){
		
        $path_theme = sfConfig::get('theme_simad');
        $base_path = sfConfig::get('base_simad');
        //******************************************************************************************************
		$returnstr="";
		$module_name="wf_detalles";
		$base_link = $base_path ."/". $app_name ."/";
		if($strdescripcion=="") $strdescripcion=$straccion;		
		if($straccion=="Consultar"){
			$returnstr="<td>".
            "<button type='submit' class='btn btn-blue btn-icon'>Consultar Registro<i class='entypo-search'></i></button>".
            "</td>";
		}elseif($straccion=="Nuevo"){
			$urlicon = $base_path."/images/simad/ico_crear_nuevo.png";
			$returnstr="<td>".
            "<a class='btn btn-white btn-sm tooltip-primary' href='#' onclick=\"javascript:jQuery.OpenModalSIMAD('$strurl','800','640');\" data-toggle='tooltip' data-original-title='$strdescripcion'>".
            "<img border=0 src='$urlicon' width='25' height='25' align='middle'/>$strdescripcion</a></td>";
		}elseif($straccion=="Exportar"){
	       $urlicon = $base_path."/images/simad/ico_exportar.png";		
			//excel?a=1
			$returnstr="<td><a class='btn btn-white btn-sm tooltip-primary' href='$strurl' data-toggle='tooltip' data-original-title='Exportar registros'>".
			"<img border=0 src='$urlicon' width='25' height='25' ".
			" align='middle'/>$strdescripcion</td>".
            " <td width='40%'>".
			"<a href='$strurl'>";			
		}elseif($straccion=="Guardar"){			
			$returnstr="<td>".
            "<button type='submit' class='btn btn-success'>Guardar Interna</button>".
			"</td>";
		}elseif($straccion=="Listar"){
			$urlicon = $base_path."/images/simad/ico_visualizar.png";
			$returnstr="<td>".
            "<a class='btn btn-white btn-sm tooltip-primary' href='#' onclick=\"javascript:jQuery.OpenModalSIMAD('$strurl','800','640');\" data-toggle='tooltip' data-original-title='$strdescripcion'>".
            "<img border=0 src='$urlicon' width='25' height='25' align='middle'/>$strdescripcion</a></td>";
		}elseif($straccion=="Restaurar"){
			$urlicon = $base_path."/images/simad/ico_restaurar.png";
			$returnstr="<td><a class='btn btn-white btn-sm tooltip-primary' href='#'>".
			"<img border=0 src='$urlicon' alt='Restaurar' width='25' height='25'".
			" align='middle'/>$strdescripcion</td>";
		}elseif($straccion=="Cerrar"){
			$urlicon = $base_path."/images/simad/ico_cerrar.png";
			$returnstr="<td>".
			"<a class='btn btn-white btn-sm tooltip-primary' href='#' onclick=\"javascript:jQuery.CloseModalSIMAD();\"  >".
			"<img border=0 src='$urlicon' alt='Cerrar' width='25' height='25' ".
			" align='middle'/>Cerrar</td>";
		}elseif($straccion=="Ir"){
			$returnstr="<a class='btn btn-white btn-sm tooltip-primary' href='#' ".
			" onclick=\"javascript:jQuery.OpenModalSIMAD('$strurl','800','640');\">$strdescripcion</a>";
		}elseif($straccion=="Link"){
			$returnstr="<a class='btn btn-white btn-sm tooltip-primary' href='$strurl'>$strdescripcion</a>";
        }elseif($straccion=="LinkModal"){
			$returnstr="<a class='tooltip-primary' href='#' ".
			" onclick=\"javascript:jQuery.OpenModalSIMAD('$strurl','800','640');\">$strdescripcion</a>";
		}elseif($straccion=="Editar"){
			$x = "/administracion.php/formas/edit?forma_id=";
			$urlicon = $base_path."/images/simad/ico_editar.png";
			$returnstr="<td>".
            "<a class='btn btn-white btn-sm tooltip-primary' href='$strurl'>".
            "<img border=0 src='$urlicon' alt='$strdescripcion' width='25' height='25' align='middle'/>$strdescripcion</a></td>";
		}elseif($straccion=="Borrar"){
			$x = "/administracion.php/formas/edit?forma_id=";
			$urlicon = $base_path."/images/simad/ico_anular.png";
			$returnstr="<td>".
            "<a class='btn btn-white btn-sm tooltip-primary' href='$strurl'>".
            "<img border=0 src='$urlicon' alt='Borrar' width='25' height='25' align='middle'/>$strdescripcion</a></td>";
		}elseif($straccion=="Devolver"){
			$urlicon = $base_path."/images/simad/ico_devolver.png";
			$returnstr="<td>".
            "<a class='btn btn-white btn-sm tooltip-primary' href='$strurl'>".
            "<img border=0 src='$urlicon' alt='Retornar' width='25' height='25' align='middle'/>$strdescripcion</a></td>";
		}elseif($straccion=="Ejecutar"){
			$urlicon = $base_path."/images/simad/ico_ejecutar.png";
			$returnstr="<a class='tooltip-primary' href='$strurl'>".
            "<img border=0 src='$urlicon' alt='$strdescripcion' width='25' height='25' align='middle'/></a>";
		}elseif($straccion=="IrSIMAD"){
			$urlicon = $base_path."/images/simad/ico_editar.png";
			$returnstr="<a class='btn btn-white btn-sm tooltip-primary' href='$strurl'  target='SIMAD'>".
            "<img border=0 src='$urlicon' alt='$strdescripcion' width='25' height='25' align='middle'/>$strdescripcion</a>";
		}elseif($straccion=="Parar"){
			$urlicon = $base_path."/images/simad/ico_uncheck.png";
			$returnstr="<td><a class='btn btn-white btn-sm tooltip-primary' href='#'>".
			"<img border=0 src='$urlicon' alt='$strdescripcion' width='25' height='25'".
			" align='middle'/>$strdescripcion</td>";
		}		
		return $returnstr;
	}	
}

?>