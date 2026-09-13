<?php use_helper('Object') ?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Creacion correpondiente en SAP
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatepaso8', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Codigo en SAP:</label>
          <div class="col-sm-4">
            <?php echo object_input_tag($prov_periodo_validez, 'getCodigoSap', array ('class'=>'form-control input-sm',)); ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Fecha Creacion en SAP:</label>
          <div class="col-sm-4"> 
          
            <div class="input-group">
             <?php echo  input_tag('getFechaCreacionSap', $prov_periodo_validez, array('class'=>'form-control input-sm datepicker', 'data-format'=>'yyyy-mm-dd','readonly'=>'readonly', 'type'=>'text', 'value'=>$prov_periodo_validez->getFechaCreacionSap())); ?>
             <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          
          
          
            <?php //echo object_input_date_tag($prov_periodo_validez, 'getFechaCreacionSap', array ('class'=>'form-control input-sm','value'=>$prov_periodo_validez->getFechaCreacionSap(),)) ?>
          </div>
        </div>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Observaciones:</label>
          <div class="col-sm-4">
            <?php echo textarea_tag('observaciones','',array('cols'=>50, 'class'=>'form-control input-sm',)); ?>
          </div>
        </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">        
            <button type="submit" class="btn btn-success">Aprobar y enviar</button>
            <?php 
            echo button_to('Regresar a proveedor','prov_periodo_validez/show?prov_periodo_validez_id=' . $prov_periodo_validez->getPrimaryKey(), array('class' => 'btn btn-red')); 
            ?>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>