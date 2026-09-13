<?php use_helper('Object') ?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Aprobacion Area de Proveedores
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatepaso6', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Tipo de Industria:</label>
          <div class="col-sm-4">  
            <?php echo object_select_tag($prov_periodo_validez, 'getProvTipoIndustriaId', array ('related_class' => 'ProvTipoIndustria','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Rechazado para Creacion:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($prov_periodo_validez, 'getProvRechazadaCreacionId', array ('related_class' => 'ProvRechazadaCreacion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));    
   ?>
          </div>
        </div>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Estado de Aprobacion:</label>
          <div class="col-sm-4"> 
            <?php echo object_select_tag($prov_estado_flujo_periodo, 'getProvEstadoAprobacionId', array ('related_class' => 'ProvEstadoAprobacion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));?>
          </div>
        
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Observaciones:</label>
          <div class="col-sm-4">
            <?php echo textarea_tag('observaciones','',array('cols'=>50, 'class'=>'form-control input-sm',)); ?>
          </div>
        </div>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Proveedor de Entidad:</label>
          <div class="col-sm-4"> 
          <?php echo object_select_tag($entidad, 'getEntidadId', array ('related_class' => 'Entidad', 'include_custom'=>'Selecione...', 'class'=>'form-control input-sm',));    
   ?>
          
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