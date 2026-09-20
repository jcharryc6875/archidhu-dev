<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
 ?>
<?php ?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear/Editar Subserie por Usuario
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body modal-scroll-body">

        <?php
        echo form_tag('subserie_por_usuario/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($subserie_por_usuario, 'getSubserieporusuarioId');
        ?>
       
        <div class="form-group">
            <!-- Nombre: -->
          <label for="ldependencia_id" class="col-sm-1 control-label">Unidad Administrativa:</label>
          <div class="col-sm-8">               
            <?php 
            echo object_select_tag($dependencias, 'getDependenciaId', array (
              'peer_method'=>'getDependenciaAllJoin','related_class' => 'Dependencia',
              'include_custom'=>'Seleccione unidad administrativa...',
              'class'=>'form-control input-sm required select2',
              'onchange' =>
              jq_remote_function(array(
                   'update' => 'contenedor_series',
                   'url' => 'subserie_por_usuario/cargarCombos',
                   'with' => '"dependencia_id=" + this.options[this.selectedIndex].value + "&cargar_combo=series"',
                   'complete' => 'clearValues();',
                 ))),$sf_params->get('dependencia_id')); 
            ?>
          </div>        
        </div>
        
        <div class="form-group">
          <!-- Nombre: -->
          <label for="lbserie_id" class="col-sm-1 control-label">Serie:</label>
          <div class="col-sm-8">            
            <div id="contenedor_series">
                <select name="serie_id" id="serie_id" disabled="true" class="form-control input-sm">
                    <option value="">Seleccione Serie...</option>      
                </select>
            </div>   
          </div>
        </div>
        
        <div class="form-group">
            <!-- Nombre: -->
          <label for="lbsubserie_id" class="col-sm-1 control-label">Subserie:</label>
          <div class="col-sm-8">            
            <div id="contenedor_subseries">
                <select name="subserie_id" id="subserie_id" disabled="true" class="form-control input-sm">
                    <option value="">Seleccione Subserie...</option>     
                </select>
            </div>   
          </div>        
        </div>
        
        <div class="form-group">          
          <!-- Nombre: -->
          <label for="ldependencia_id" class="col-sm-1 control-label">Usuario:</label>
          <div class="col-sm-8">            
            <?php 
                echo object_select_tag($subserie_por_usuario, 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm required select2',));
            ?>  
          </div>
        
        </div>
        
        <div class="form-group"> 
          <label for="ldependencia_id" class="col-sm-1 control-label">Permisos para:</label>
          <div class="col-sm-8">
              <?php 
                  echo label_for('tmp2','Solo Prestamo',array('style'=>'vertical-align: top;'));
                  $check_prestamo = $subserie_por_usuario->getPrimaryKey() ? $subserie_por_usuario->getPrestamo() : false;
                  echo checkbox_tag('for_prestamo',1,$check_prestamo); ?>&nbsp;&nbsp;  
                  <?php 
                  echo label_for('tmp1','Solo Creacion',array('style'=>'vertical-align: top;'));
                  $check_creacion = $subserie_por_usuario->getPrimaryKey() ? $subserie_por_usuario->getCreacion() : false;
                  echo checkbox_tag('for_creacion',1,$check_creacion) ?>&nbsp;&nbsp;
                  <?php 
                  echo label_for('tmp3','Solo Visualizacion',array('style'=>'vertical-align: top;'));
                  $check_vista = $subserie_por_usuario->getPrimaryKey() ? $subserie_por_usuario->getVisualizacion() : false;
                  echo checkbox_tag('for_visualizacion',1,$check_vista); 
              ?>
          </div>
        </div>

      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $subserie_por_usuario->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>



<?php

echo javascript_tag('
function validateForm()
{    
    if(document.getElementById("dependencia_id").options[document.getElementById("dependencia_id").selectedIndex].value == "")
    {
        alert("Debe Seleccionar Al Menos Una Unidad Administrativa!");
        return false;      
    }
  
    if(document.getElementById("usuario_id").options[document.getElementById("usuario_id").selectedIndex].value == "")
    {
        alert("Debe Seleccionar Un Usuario!");
        return false;      
    }
    
    return true;
}

function clearValues()
{    
    if(jQuery( "#dependencia_id" ).val() == "")
    {
        console.log(jQuery("#dependencia_id").val());
        jQuery("#serie_id").children("option:not(:first)").remove();
        jQuery("#subserie_id").children("option:not(:first)").remove();
        jQuery("#serie_id").val("0");
        jQuery("#subserie_id").val("0");
        jQuery("#serie_id").attr("disabled", "disabled");
        jQuery("#subserie_id").attr("disabled", "disabled");
    }  
}

');

?>