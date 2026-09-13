<?php
	
	$combo = $sf_params->get('combo_box');
	$permiso = $sf_params->get('permiso');
	
	if($combo == 'series'){
		echo "<select class='mensaje' onChange=\"cargaContenido('subseries', ".$permiso.")\"  id='series' name='series'>";
		echo "<option value='0'>Seleccione...</option>";	
		$series = $unidad;
		foreach($series as $serie){
			echo "<option value='".$serie->getSerieId()."'";
			echo ">".$serie->getDescripcion()."</option>";
		}
		echo "</select>";
	}else{
		echo "<select class='mensaje' id='subseries' name='subseries'>";
		echo "<option value='0'>Seleccione...</option>";		
		$subseries = $unidad;
		foreach($subseries as $subserie){
			echo "<option value='".$subserie->getSubserieId()."'";
			echo ">".$subserie->getDescripcion()."</option>";
		}
		echo "</select>";
		
	}
	


?>