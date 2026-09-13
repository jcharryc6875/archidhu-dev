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
          <?php echo $forma->getPrimaryKey() ? 'Editar' : 'Crear' ?> Privilegio
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('formas/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($forma, 'getFormaId');
        ?>
       
        <div class="form-group">
          <!-- Modulo: -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Modulo:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($forma, 'getModuloId', array ('related_class' => 'Modulo','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)) ?>
            
          </div>
          
          <!-- Nombre: -->
          <label for="ldependencia_id" class="col-sm-1 control-label">Nombre:</label>
          <div class="col-sm-4">
            <?php
            echo object_input_tag($forma,'getNombre', array('class'=>'form-control input-sm required','size' => 65,));
            ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Descripcion: -->
          <label for="lnombre" class="col-sm-1 control-label">Descripcion:</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($forma, 'getDescripcion', array ('size' => 65,'class'=>'form-control input-sm required',)) ?>
            
          </div>
          <!-- Ruta -->
          <label for="ldescripcion" class="col-sm-1 control-label">Ruta:</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($forma, 'getRuta', array ('size' => 65, 'class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- IsPublic -->
          <label for="lbIsPublic" class="col-sm-1 control-label">Es Publico:</label>
          <div class="col-sm-1">
            <?php echo object_checkbox_tag($forma, 'getIsPublic', array ('size' => 65,'class'=>'form-control input-sm',)) ?>
          </div>
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $forma->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>