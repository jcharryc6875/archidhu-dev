<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('jQuery','Object');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Eliminaci&oacute; Expedientes por Descarte
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('unidad_documental/updateEliminacion', array('name'=>'crear', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
        echo input_hidden_tag('localizacionunidaddocumental_id', $localizacion);
        ?>
        <div class="form-group">
          <!-- Observaciones Eliminacion -->
          <label for="lbobseliminacion" class="col-sm-1 control-label">Observaciones Eliminaci&oacute;n</label>
          <div class="col-sm-9">
            <?php 
            echo textarea_tag('obs_eliminacion', '', array('class' => 'form-control')); 
            ?>
          </div>
        </div>
        
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">            
            <button type="submit" class="btn btn-success" onclick="return confirm('Esta seguro de realizar esta acción?');">Guardar Cambios</button>
            <?php echo jq_link_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>