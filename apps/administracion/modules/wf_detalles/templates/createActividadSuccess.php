<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">      
      <div class="panel-heading">
        <div class="panel-title">
          Actividades Worflow
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
            echo form_tag('wf_detalles/updateActividades', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        ?>
        
        <div id="<?php echo md5('divwfactividadesedit') ?>" class="form-group">
          <?php echo object_input_hidden_tag($wf_actividad, 'getWfActividadId'); ?>
          <?php echo object_input_hidden_tag($wf_transicion, 'getWfTransicionId'); ?>
          <!-- Actividades -->
          <label for="lbgetWfActividadId" class="col-sm-1 control-label">Descripci&oacute;n:</label>
          <div class="col-sm-4">
            <?php 
                echo object_input_tag($wf_actividad, 'getDescripcion', array ('related_class' => 'WfActividad','class'=>'form-control input-sm required',));
            ?>
          </div>          
        </div>        
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Registro</button>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
      <?php 
    	if(count($wf_actividades)){ include_partial('actividadesList', array('wf_actividades' => $wf_actividades)); } 
	  ?>
    </div>
  </div>
</div>