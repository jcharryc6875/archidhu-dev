<?php use_helper('Object') ?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Aprobacion Area de Contabilidad
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatepaso4', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Grupo de Tesoreria:</label>
          <div class="col-sm-4">  
            <?php echo object_select_tag($prov_periodo_validez, 'getProvGrupoTesoreriaId', array ('related_class' => 'ProvGrupoTesoreria','class'=>'form-control input-sm', 'include_custom'=>'Selecione...',));  ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Vias de Pago:</label>
          <div class="col-sm-4"> 
            <?php echo object_select_tag($prov_via_pago, 'getProvViaPagoId', array ('related_class' => 'ProvViaPago','class'=>'form-control input-sm','multiple' => true, 'include_custom'=>'Selecione...',)); ?>
          </div>
        </div>
        
        
        <div class="form-group">
        
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Estado de Aprobacion:</label>
          <div class="col-sm-4"> 
          <?php 
            echo object_select_tag($prov_estado_flujo_periodo, 'getProvEstadoAprobacionId', array ('related_class' => 'ProvEstadoAprobacion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));?>
          </div>
        
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