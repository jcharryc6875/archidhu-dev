<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Respuesta a Solicitud de Modificaion para proveedores
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      
        <?php if ($sf_params->get('proveedor_nombre')): ?>
                <p>Los datos introducidos no son correctos.
                Por favor, corrija los siguientes errores y vuelva a enviar el formulario:</p>
                <ul>
                <?php foreach($sf_request->getErrors() as $nombre => $error): ?>
                    <li><?php echo $nombre ?>: <?php echo $error ?></li>
                <?php endforeach; ?>
                </ul>
        <?php endif; ?>
      
      
        <?php 
            echo form_tag('prov_solicitud_modificacion/updaterespuesta', array('name'=>'form1','multipart=true', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
            echo object_input_hidden_tag($prov_solicitud_modificacion, 'getProvSolicitudModificacionId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Estado:</label>
          <div class="col-sm-5">       
            <?php echo object_select_tag($prov_solicitud_modificacion, 'getProvEstadoSolModId', array ('related_class' => 'ProvEstadoSolMod','class'=>'form-control input-sm required', 'include_custom'=>'Selecione...',)) ?>
                     
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Respuesta:</label>
          <div class="col-sm-5">
            <?php echo object_textarea_tag($prov_solicitud_modificacion, 'getRespuestaSolicitud', array ('value'=>$sf_params->get('respuesta'),'class'=>'form-control input-sm required',)) ?>                                    
            </div>
        </div>
        
        
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $prov_solicitud_modificacion->getPrimaryKey() ? "Guardar Respuesta" : "Guardar Cambios"?> </button>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>