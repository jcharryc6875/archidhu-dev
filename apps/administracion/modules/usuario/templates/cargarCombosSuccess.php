<?php
use_helper('Object','jQuery');
switch($combo_seleccionado){        
        case "entidades":{
        	echo '<select class="form-control input-sm required" id="dependencia_id" name="dependencia_id" '.$disable_combo.' onchange="'.
		    	jq_remote_function(array(
		           'update' => 'cbregional',
		           'url' => 'usuario/cargarCombos',
		           'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=dependencias' + '&entidad_id=".$entidad_id."'",
		           'loading'  => "jQuery('regional_id').disabled='disabled';",
		    	   'script' => true,
		           //'complete' => "jQuery('serie_cambia').value = this.options[this.selectedIndex].value;",
		         )).'">';
            echo '<option value="" selected="selected">Seleccione dependencia...</option>';
            foreach($dependencias_list as $rs){
            	$nombre_dependencia = $rs->getNombre()." - ".$rs->getCodigo()." - ".$rs->getTipoTabla();
            	echo '<option value="'.$rs->getDependenciaId().'">'.$nombre_dependencia.'</option>';
            }
            echo '</select>';
            //echo '<span style="opacity: 0.999999;" class="jsvalidation" id="jsvalidator_dependencia_id">Este Campo No Debe Quedar Vacio</span>';
        break;
        }
        case "dependencias":{
        	echo '<select class="form-control input-sm required" id="regional_id" name="regional_id" '.$disable_combo.' >';
        	echo '<option value="" selected="selected">Seleccione regional...</option>';
        	foreach($regionales_list as $rs){
        		$nombre_item = $rs->getDescripcion();
        		$entidad_item = $rs->getEntidad();
        		echo '<option value="'.$rs->getRegionalId().'">'.$nombre_item.' - '.$entidad_item.'</option>';
        	}
        	echo '</select>';
        	//echo '<span style="opacity: 0.999999;" class="jsvalidation" id="jsvalidator_regional_id">Este Campo No Debe Quedar Vacio</span>';
        break;
        }
    }
?>
