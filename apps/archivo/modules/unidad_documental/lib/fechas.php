<?php

/**
 * @author 9793826063
 * @copyright 2008
 */   
/******************************************************************************************************************/	
function AddDays($fecha, $ndias)
{
   list($anio,$mes,$dia) = explode("-", $fecha);
   $nueva = mktime(0, 0, 0, $mes, $dia, $anio) + $ndias * 24 * 60 * 60;
   $nuevafecha = date("Y-m-d", $nueva);
   return($nuevafecha);
}
/******************************************************************************************************************/
function AddYears($fecha, $nyear)
{
   $array_date = explode("-", $fecha);
   $nueva = date("Y-m-d", mktime(0, 0, 0, $array_date[1], $array_date[2] , $array_date[0] + $nyear));   
   return $nueva;
}
/******************************************************************************************************************/
	
function deleteDays($fecha, $ndias)
{
   list($anio,$mes,$dia) = explode("-", $fecha);
   $nueva = mktime(0, 0, 0, $mes, $dia, $anio) - $ndias * 24 * 60 * 60;
   $nuevafecha = date(" Y-m-d", $nueva);
   return($nuevafecha);
}

/*function AddWorkDays($fecha, $ndiashabilesasumar)
{
  while ($i < $dias) {
    $fecha = AddDays($fecha, 1);
	list($dia, $mes, $anio) = split("/", $fecha);
	if (date("N", mktime(0, 0, 0, $mes, $dia, $anio)) != 6 && date("N", mktime(0, 0, 0, $mes, $dia, $anio)) != 7)
		$i += 1;
	}
	return $fecha;
}*/
?>