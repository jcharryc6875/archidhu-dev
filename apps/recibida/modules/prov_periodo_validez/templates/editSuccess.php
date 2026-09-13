<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear/Editar Periodo de Validez
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($periodo_validez, 'getProveedorId');
        echo object_input_hidden_tag($periodo_validez, 'getProvPeriodoValidezId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Fecha Inicio:</label>
          <div class="col-sm-4">
            <div class="input-group">
             <?php echo  input_tag('getFechaInicial', $periodo_validez, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd', 'type'=>'text', 'rich' => true, 'readonly'=>'true',
  'value'=>$periodo_validez->getFechaInicial("Y-m-d"),)); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Fecha Final:</label>
          <div class="col-sm-4">            
            <div class="input-group">
             <?php echo  input_tag('getFechaFinal', $periodo_validez, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd', 'type'=>'text', 'rich' => true, 'readonly'=>'true',
  'value'=>$periodo_validez->getFechaFinal("Y-m-d"),)); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>
      
      <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Observaciones:</label>
          <div class="col-sm-6">

            <?php 
                $periodo_validez->setObservacionesGenerales(''); 
                echo object_textarea_tag($periodo_validez, 'getObservacionesGenerales', array ('class'=>'form-control input-sm',)); 
            ?>
          </div>
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $periodo_validez->getPrimaryKey() ? "Guardar Cambios" : "Guardar Formato"?> </button>
            <?php 
            echo button_to('Regresar a proveedor','proveedor/show?proveedor_id=' . $periodo_validez->getPrimaryKey(), array('class' => 'btn btn-red')); 
 ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>

