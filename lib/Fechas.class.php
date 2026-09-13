<?php

/**
 * @author 9793826063
 * @copyright 2008
 */   
/******************************************************************************************************************/	
function AddDays($fecha, $ndias)
{	
	$fecha_str = strtotime ( "+$ndias day" , strtotime ( $fecha ) ) ;
	$fecha_format =  date("Y-m-d", $fecha_str);
   	/*
   	list($año,$mes,$dia) = split("-", $fecha);
   	$nueva = mktime(0, 0, 0, $mes, $dia, $año) + $ndias * 24 * 60 * 60;
   	$nuevafecha = date("Y-m-d", $nueva);*/
   	return $fecha_format;
}
/******************************************************************************************************************/
function AddYears($fecha, $nyear)
{
   $array_date = explode("-", $fecha);
   $nueva = date("Y-m-d", mktime(0, 0, 0, $array_date[1], $array_date[2] , $array_date[0] + $nyear));   
   return $nueva;
}
/******************************************************************************************************************/
function AddYearsDateTime($fecha, $nyear)
{
	try{
   		$date = new DateTime($fecha);
   		$date->modify("+$nyear year");
   		$fnew = $date->format('Y-m-d');
   		return $fnew;
   	}catch (Exception $ex){
   		return null;
   	}
}
/******************************************************************************************************************/
function AddMonth($fecha, $nmonth)
{
   $array_date = explode("-", $fecha);
   $nueva = date("Y-m-d", mktime(0, 0, 0, $array_date[1] + $nmonth, $array_date[2] , $array_date[0]));   
   return $nueva;
}
/******************************************************************************************************************/	
function deleteDays($fecha, $ndias)
{
   list($año,$mes,$dia) = split("-", $fecha);
   $nueva = mktime(0, 0, 0, $mes, $dia, $año) - $ndias * 24 * 60 * 60;
   $nuevafecha = date(" Y-m-d", $nueva);
   return($nuevafecha);
}
/******************************************************************************************************************/
function AddWorkDays($fecha, $ndiashabilesasumar)
{
  while ($i < $dias) {
    $fecha = AddDays($fecha, 1);
	list($dia, $mes, $año) = split("/", $fecha);
	if (date("N", mktime(0, 0, 0, $mes, $dia, $año)) != 6 && date("N", mktime(0, 0, 0, $mes, $dia, $año)) != 7)
		$i += 1;
	}
	return $fecha;
}
/***************************************************************************************************************/

/****************************************************************************************************************/
function cambiarFormatoFecha($fecha){	
    list($dia,$mes,$anio)=explode("/",$fecha);        
    return $anio."-".$mes."-".$dia." ".date("G:i:s");
}
/***************************************************************************************************************/

/**************************************************************************************************************/
function getFechaFormat($fecha){	
    // fecha original en formato americano/europeo    
    if(substr_count($fecha,"/")){//verificar si el formato fehca es europeo        
        $dia = substr($fecha, 0, 2);
        $mes   = substr($fecha, 3, 2);
        $anio = substr($fecha, -4);
        //$fecha = $ano.'-'.$mes. '-'.$dia;
        $fecha = $dia.'-'.$mes. '-'.$anio;
    }/*elseif(substr_count($fecha,"-")){//verificar si el formato fehca es europeo        
        $dia = substr($fecha, 0, 2);
        $mes   = substr($fecha, 3, 2);
        $anio = substr($fecha, -4);
        //$fecha = $ano.'-'.$mes. '-'.$dia;
        $fecha = $dia.'/'.$mes. '/'.$ano;
    }*/
    // fechal final realizada el cambio de formato a las fechas europeas
    return $fecha;
}
/*************************************************************************************************************/

?>