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
          Acciones a Seguir
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
            echo form_tag('wf_detalles/updateAcciones', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        ?>
        
        <div id="<?php echo md5('divwftransicionesedit') ?>" class="form-group">
          <?php echo object_input_hidden_tag($wf_transicion, 'getWfTransicionId'); ?>
          <!-- Accion a ejecutar -->
          <label for="lbgetWfTransicionId" class="col-sm-1 control-label">Descripci&oacute;n:</label>
          <div class="col-sm-4">
            <?php 
                echo object_input_tag($wf_transicion, 'getDescripcion', array ('related_class' => 'WfTransicion','class'=>'form-control input-sm required',));
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
    	if(count($wf_transiciones)){ include_partial('accionesList', array('wf_transiciones' => $wf_transiciones)); } 
	  ?>
    </div>
  </div>
</div>