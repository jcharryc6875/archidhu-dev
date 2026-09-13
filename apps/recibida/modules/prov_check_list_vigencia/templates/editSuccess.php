<?php use_helper('Object','jQuery') ?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Calificar Item del Check List
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
            echo form_tag('prov_check_list_vigencia/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
            echo object_input_hidden_tag($prov_check_list_vigencia, 'getProvCheckListVigenciaId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Periodo de Validez:</label>
          <div class="col-sm-6">
            <?php echo $prov_check_list_vigencia->getProvPeriodoValidez(); ?>
          </div>
        </div>
        
        <div class="form-group">         
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Pregunta:</label>
          <div class="col-sm-6">  
            <?php echo $prov_check_list_vigencia->getProvCheckListPregunta();?>
          </div>
        </div>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Respuesta:</label>
          <div class="col-sm-6">
            <?php echo object_input_tag($prov_check_list_vigencia, 'getRespuesta', array ('class'=>'form-control input-sm required',)) ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Descripcion:</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($prov_check_list_vigencia, 'getDescripcion', array ('class'=>'form-control input-sm required',)) ?>
          </div>
        </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Observaciones:</label>
          <div class="col-sm-6">
            <?php echo object_input_tag($prov_check_list_vigencia, 'getObservaciones', array ('class'=>'form-control input-sm required',)) ?>
          </div>
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $prov_check_list_vigencia->getPrimaryKey() ? "Guardar Cambios" : "Guardar Respuesta"?> </button>
            <?php 
                echo jq_button_to_function('Cerrar','javascript:parent.jQuery.CloseModalSIMAD();', array('class' => 'btn btn-red')); 
            ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>