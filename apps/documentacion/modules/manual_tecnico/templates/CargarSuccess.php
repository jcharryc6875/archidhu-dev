<?php
	
	$combo = $sf_params->get('combo_box');
	$permiso = $sf_params->get('permiso');
	
	if($combo == 'Codigo Marc'){
		echo "<select class='mensaje' onChange=\"cargaContenido('subcampo', ".$permiso.")\"  id='codigo' name='codigo'>";
		echo "<option value='0'>Seleccione...</option>";	
		$series = $consulta;
		foreach($series as $serie){
			echo "<option value='".$serie->getSerieId()."'";
			echo ">".$serie->getDescripcion()."</option>";
		}
		echo "</select>";
	}else{
		echo "<select class='mensaje' id='subcampo' name='subcampo'>";
		echo "<option value='0'>Seleccione...</option>";		
		$subseries = $consulta;
		foreach($subseries as $subserie){
			echo "<option value='".$subserie->getSubserieId()."'";
			echo ">".$subserie->getDescripcion()."</option>";
		}
		echo "</select>";
		
	}
	


?>