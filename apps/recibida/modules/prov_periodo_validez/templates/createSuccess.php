<?php use_helper('Object') ?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Editar Proveedor
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatecreate', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        $periodo_validez->setProveedorId($proveedor_id);
        echo object_input_hidden_tag($periodo_validez, 'getProveedorId');
        ?>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Fecha Inicio:</label>
          <div class="col-sm-4">

            <div class="input-group">
             <?php echo  input_tag('getFechaInicial', $periodo_validez, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd', 'type'=>'text', 'rich' => true, 'readonly'=>'true',
  'value'=>$sf_params->get('fecha_recibido'),)); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
               
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Fecha Final:</label>
          <div class="col-sm-4">            
            <div class="input-group">
             <?php echo  input_tag('getFechaFinal', $periodo_validez, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd', 'type'=>'text', 'rich' => true, 'readonly'=>'true',
  'value'=>$sf_params->get('fecha_recibido'),)); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
            
          </div>
        </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Observaciones:</label>
          <div class="col-sm-6">
            <?php echo object_textarea_tag($periodo_validez, 'getObservacionesGenerales', array (
  'size' => 80,'class'=>'form-control input-sm','value'=>$sf_params->get('observaciones_generales'),
)) ?>
          </div>
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $periodo_validez->getPrimaryKey() ? "Guardar Cambios" : "Guardar Periodo"?> </button>
            <?php  
            echo button_to('Regresar a proveedor','proveedor/show?proveedor_id=' . $proveedor_id, array('class' => 'btn btn-red')); 
            ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>