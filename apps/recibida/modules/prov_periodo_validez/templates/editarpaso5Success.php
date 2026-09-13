<?php use_helper('Object') ?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Aprobacion Area de Compras
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('prov_periodo_validez/updatepaso5', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Grupo de Esquema de proveedores:</label>
          <div class="col-sm-4">  
            <?php echo object_select_tag($prov_periodo_validez, 'getProvGrupoEsquemaId', array (
  'related_class' => 'ProvGrupoEsquema','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Condicion de Pago:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($prov_periodo_validez, 'getProvCondicionPagoId', array ('related_class' => 'ProvCondicionPago','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
        </div>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Moneda Pedido:</label>
          <div class="col-sm-4">          
            <?php echo object_select_tag($prov_periodo_validez, 'getProvMonedaPedidoId', array (
  'related_class' => 'ProvMonedaPedido','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Condicion de Expedicion:</label>
          <div class="col-sm-4">           
            <?php echo object_select_tag($prov_periodo_validez, 'getProvCondicionExpedicionId', array ('related_class' => 'ProvCondicionExpedicion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
        </div>
        
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Aduana de entrada:</label>
          <div class="col-sm-4">  
            <?php echo object_select_tag($prov_periodo_validez, 'getProvAduanaEntradaId', array ('related_class' => 'ProvAduanaEntrada','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),)); ?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Correspondiente Autofacturacion:</label>
          <div class="col-sm-4"> 
            <?php echo object_select_tag($prov_periodo_validez, 'getProvCorrespondienteAutofacturacionId', array ('related_class' => 'ProvCorrespondienteAutofacturacion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));    
   ?>
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