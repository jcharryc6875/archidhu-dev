<?php
	$path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    //***************************************************************************
	$combo = $sf_params->get('combo_box');
	if($combo == 'codigo'){
		echo "<select onchange=\"javascript:jQuery.CargaContenidoMarc('subcampo','".$base_path."')\"  id='codigo' name='codigo' class='form-control input-sm'>";
		echo "<option value=''>Seleccione...</option>";
		foreach($lista_codigos as $codigo){
			echo "<option value='".$codigo->getMarcId()."'";
			echo ">".$codigo->getCodigo().' - '.$codigo->getDescripcion()."</option>";
		}
		echo "</select>";
    }
    
	if($combo == 'subcampo'){
	    echo '<div class="form-group"><label class="control-label col-sm-3">Subcampo:</label><div class="col-sm-9">';
		echo "<select onchange=\"javascript:jQuery.CargaContenidoMarc('subcampo','".$base_path."')\"  id='subcampo' name='subcampo' class='form-control input-sm'>";
		echo "<option value=''>Seleccione...</option>";	
		$subcampos = $consulta;
		foreach($subcampos as $subcampo){
			echo "<option value='".$subcampo->getMarcsubcampoId()."'";
			echo ">".$subcampo->getCodigo().' - '.$subcampo->getDescripcion()."</option>";
		}
		echo "</select></div></div>";
		
        echo '<div class="form-group"><label class="control-label col-sm-3">Primer Indicador:</label><div class="col-sm-9">';
		echo "<select id='indicador1' name='indicador1' class='form-control input-sm'>";
		echo "<option value=''>Seleccione...</option>";		
		//$indicadores = $consulta;
		foreach($primer_indicador as $indicador){
			echo "<option value='".$indicador->getMarcindicadorprimarioId()."'";
			echo ">".$indicador->getCodigo().' - '.$indicador->getDescripcion()."</option>";
		}
		echo "</select></div></div>";
		
        echo '<div class="form-group"><label class="control-label col-sm-3">Segundo Indicador:</label><div class="col-sm-9">';
		echo "<select id='indicador2' name='indicador2' class='form-control input-sm'>>";
		echo "<option value=''>Seleccione...</option>";		
		//$indicadores = $consulta;
		foreach($segundo_indicador as $indicador){
			echo "<option value='".$indicador->getMarcindicadorsecundarioId()."'";
			echo ">".$indicador->getCodigo().' - '.$indicador->getDescripcion()."</option>";
		}
		echo "</select></div></div>";
	}
	/*
	else{
		echo "<select class='mensaje' id='indicadores' name='indicadores'>";
		echo "<option value='0'>Seleccione...</option>";		
		$indicadores = $consulta;
		foreach($indicadores as $indicador){
			echo "<option value='".$indicador->getMarcindicadorprimarioId()."'";
			echo ">".$indicador->getDescripcion()."</option>";
		}
		echo "</select>";
		
	}*/
?>