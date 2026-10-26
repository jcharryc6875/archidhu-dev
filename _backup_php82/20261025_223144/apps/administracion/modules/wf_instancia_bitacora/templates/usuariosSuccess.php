<?php use_helper('jQuery')?>
<?php	
    $combo = $sf_params->get('combo_box');        
    $usuarios_id_transicion = array();
    	
    if($usuarios_transicion != null)
    {        
    	if($combo == 'usuario_id'){
    	    echo '<div class="form-group">';
            echo '<label for="lbusuario_id" class="col-sm-1 control-label">Asignar:</label>';
            echo '<div class="col-sm-6">';            
    		echo "<select id='usuario_id' name='usuario_id' class='form-control input-sm'>";
    		echo "<option value=''>Seleccione usuario...</option>";			
    		foreach($usuarios_transicion as $usuarios){
    		    $usuarios_id_transicion[] = $usuarios->getUsuarioId();
    			echo "<option value='".$usuarios->getUsuarioId()."'";
    			echo ">".$usuarios->getUsuario()."</option>";
    		}
    		echo "</select>";
            echo '</div>';
            echo '</div>';
            echo input_hidden_tag('allusuarios_id',implode(",",$usuarios_id_transicion));
        }
    }
    
    if($variables != null){
    	$cont = 1;
		foreach($variables as $variable){
			echo '<div class="form-group">';
			echo '<label for="lbusuario_id" class="col-sm-1 control-label">';
			echo ucwords(mb_strtolower($variable->getNombre())).":";
			echo '</label>';
			echo '<div class="col-sm-4">';
			if($variable->getWftipodatoId() == 'fecha'){
				echo select_date_tag($variable->getWfvariableId().'_'.$variable->getNombre(),'','',array('class'=>'form-control input-sm'));
			}else{
				echo input_tag($variable->getWfvariableId().'_'.mb_strtolower(str_replace(" ","_",$variable->getNombre())),'',array('class'=>'form-control input-sm'));
			}
		    echo '</div>';
            echo '</div>';
			$cont++;
		}
	}
?>	
<?php if($modulo != "")
{
  echo '<div class="form-group">';  
  echo '<label for="lbwfcomid" class="col-sm-1 control-label">Seleccionar Comunicacion '.$modulo->getDescripcion().':</label>';
  echo '<div class="col-sm-5">'; 
  echo '<div class="input-group">';
  echo '<input type="text" name="comunicacion" id="copiaUser" class="form-control input-sm required"  placeholder="Seleccione comunicacion" value="" />';                
  echo '<div class="input-group-btn">';         
  echo '<button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD("'.$base_path.'/administracion.php/wf_instancia_bitacora/consultaCom?opcion=0&campoText=comunicacion&campoId=idCom&wfactividadtransicion_id='.$actividad_transicion->getWfactividadtransicionId().'&modulocomunicaciones='.$modulo->getDescripcion().'", "800", "600");">Buscar</button>';
  echo '<button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario("comunicacion");jQuery.LimpiarCampoFormulario("idCom");"><i class="entypo-cancel-circled"></i></button>';
  echo '</div>';
  //echo input_tag('comunicacion' , '', array ('size'=>'50','readonly'=>'true', 'class'=>'jsrequired',));
  //echo jq_link_to_function(image_tag('/images/simad/ico_mail_entrada.png',array('border'=>'0','width'=>"25",'height'=>"25",'title'=>'Buscar Comunicacion')),'javascript:openWindow("/administracion.php/wf_instancia_bitacora/consultaCom?opcion=0&campoText=comunicacion&campoId=idCom&wfactividadtransicion_id='.$actividad_transicion->getWfactividadtransicionId().'&modulocomunicaciones='.$modulo->getDescripcion().'")');
  //echo jq_link_to_function(image_tag('/images/simad/ico_borrar.png',array('border'=>"0",'width'=>"25",'height'=>"25",'title'=>'Borrar Comunicacion')),'javascript:document.forms[0].comunicacion.value="";document.forms[0].idCom.value="";');
  echo '</div>';  
  echo '</div>';
  echo '</div>';
}