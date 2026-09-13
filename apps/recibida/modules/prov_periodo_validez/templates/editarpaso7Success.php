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
        echo form_tag('prov_periodo_validez/updatepaso7', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_periodo_validez, 'getProvPeriodoValidezId');
        ?>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Aprobado para Creacion:</label>
          <div class="col-sm-4">  
            <?php echo object_select_tag($prov_periodo_validez, 'getProbAprobadoParaCreacionId', array (
  'related_class' => 'ProbAprobadoParaCreacion','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));?>
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Rechazado para Creacion:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($prov_periodo_validez, 'getProvRechazadaCreacion2Id', array ('related_class' => 'ProvRechazadaCreacion2','class'=>'form-control input-sm', 'include_custom'=>'Selecione...','selected'=>$sf_params->get('pais_id'),));?>
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
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Ir a Paso:</label>
          <div class="col-sm-4"> 
          <?php echo select_tag('ir_a_paso', options_for_select(array(
   '1'=>'Creacion en SAP',
   '2'=>'Revision de la creacion en SAP',
   '3'=>'Contabilidad y Tesoreria',
   '4'=>'Contabilidad',
   '5'=>'Tesoreria',
   '6'=>'Compras'   
  ), 0), array('class'=>'form-control input-sm')) ?>
          
          </div>
          
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Enviar Alerta a:</label>
          <div class="col-sm-4"> 
            <select name="usuario_enviar_id"  class="form-control input-sm" id="usuario_enviar_id" >
  					<option value="0">Seleccione...</option>
					  <?php   						
  						foreach($usuarios_area as $usuario_area){
							echo "<option value='".$usuario_area->getUsuarioId()."'";
							if($usuario_area->getUsuarioId() == "")
                            {
								echo " selected ";
							}
							echo ">".$usuario_area->getUsuario()."</option>";
						}
					?>
			</select>
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