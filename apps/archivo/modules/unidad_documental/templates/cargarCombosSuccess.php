<?php
use_helper('Object','jQuery');
switch($sf_params->get("combo_seleccionado")){
        case  "macroproceso":{
            echo '<option value="" selected="selected" class="form-control input-sm">Seleccione Proceso...</option>';
            foreach($procesos as $rs)
                echo '<option value="'.$rs->getProcesosId().'">'.(ucwords(mb_strtolower($rs->getDescripcion()))).'</option>';   
        break;
        }
        case  "proceso":{
            echo '<option value="" selected="selected" class="form-control input-sm">Seleccione Oficina Productora...</option>';
            foreach($oficina_productora as $rs)
                echo '<option value="'.$rs->getOficinaproductoraId().'">'.(ucwords(mb_strtolower($rs->getDescripcion()))).'</option>';   
        break;
        }
        case  "unidad_administrativa":{
            echo '<option value="" selected="selected" class="form-control input-sm">Seleccione Dependencia...</option>';
            foreach($dependencias as $rs)
                echo '<option value="'.$rs->getDependenciaId().'">'.(ucwords(mb_strtolower($rs->getNombre())))." - ".$rs->getTipoTabla()." - ".$rs->getEntidad().'</option>';   
        break;
        }
        case  "dependencias_anterior":{
            echo '<option value="" selected="selected" class="form-control input-sm">Seleccione Dependencia...</option>';
            foreach($dependencias_anterior as $rs)
                echo '<option value="'.$rs->getDependenciaId().'">'.(ucwords(mb_strtolower($rs->getNombre())))." - ".$rs->getTipoTabla().'</option>';   
        break;
        }
        case  "dependencias":{        	
        	if($form_tag){
        		echo '<select class="form-control input-sm" id="serie_id" name="serie_id" '.$disable_combo.' onchange="'.
		    	jq_remote_function(array(
		           'update' => 'cbsubserie',
		           'url' => 'unidad_documental/cargarCombos',
		           'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=series&form_tag=$form_tag'+'&id_permiso=$id_permiso'",
		           'loading'  => "jQuery('#subserie_id').empty().append('<option selected=selected value>Seleccione Serie</option>');jQuery('serie_cambia').value = jQuery('serie_id').value;jQuery('.dinamicallfields').remove();",
		    	   'script' => true
		         )).'">';
        	}
            echo '<option value="" selected="selected">Seleccione Serie...</option>';
            foreach($series as $rs)
                echo '<option value="'.$rs->getSerieId().'">'.(ucwords(mb_strtolower($rs->getDescripcion()))).'</option>';
            if($form_tag){
            	echo '</select>';
            }            
        break;
        }
        case "series":{
        	if($form_tag == 1){
        		echo '<select class="form-control input-sm" id="subserie_id" name="subserie_id">';
        	}else{
        		echo '<select class="form-control input-sm required" id="subserie_id" name="subserie_id">';        		
        	}
            echo '<option value="" selected="selected">Seleccione subserie...</option>';
            foreach($subseries as $rs){
            	$split_name = preg_split("/[-]+/", $rs->getDescripcion(),-1, PREG_SPLIT_NO_EMPTY);
            	$sigla = "";
            	if(count($split_name) > 1){
            		$sigla = ucwords(mb_strtolower($split_name[1]));
            	}
            	$text = (ucwords(mb_strtolower($split_name[0])));
            	$nombre_subserie = $sigla != "" ? $sigla.' - '.$text : $text;
                echo '<option value="'.$rs->getSubserieId().'">'.$nombre_subserie.'</option>';
            }
            if($form_tag == 1){
            	echo '</select>';            	
            }else{
            	echo '</select>';
            }
        break;
        }        
    }
?>