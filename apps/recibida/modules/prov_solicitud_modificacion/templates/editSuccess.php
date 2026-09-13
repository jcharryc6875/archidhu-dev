<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery','Object');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Solicitud Modificacion Proveedor
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
        echo form_tag('prov_solicitud_modificacion/update', array('name'=>'form1','multipart=true', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($prov_solicitud_modificacion, 'getProvSolicitudModificacionId');
        ?>
        <div class="form-group">
          <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Proveedor:</label>
          <div class="col-sm-6">   
          <div class="input-group">    
            
            <?php 
    $proveedor=$prov_solicitud_modificacion->getProveedorId();
  echo input_hidden_tag('proveedor_id',$proveedor);
  echo input_tag('proveedor_nombre' , $prov_solicitud_modificacion->getProveedor(), array ('size'=>'80','readonly'=>'true', 'class'=>'form-control input-sm required',)) ?>

<div class="input-group-btn">   
<button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('proveedor/consulta?opcion=0&campoText=proveedor_nombre&campoId=proveedor_id') ?>', '960', '600'); return false;">Seleccionar</button>
<button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('proveedor_nombre');jQuery.LimpiarCampoFormulario('proveedor_id');"><i class="entypo-cancel-circled"></i></button>
</div> 
</div>

                     
          </div>
        </div>
        
        <div class="form-group">
        <!-- Tipo Procedimiento -->
          <label for="ltipoprocedimiento_id" class="col-sm-2 control-label">Descripcion:</label>
          <div class="col-sm-6">
            <?php echo object_textarea_tag($prov_solicitud_modificacion, 'getDescripcion', array ('value'=>$sf_params->get('descripcion'),'class'=>'form-control input-sm required',)) ?>                                   
            </div>
        </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $prov_solicitud_modificacion->getPrimaryKey() ? "Guardar Cambios" : "Radicar Solicitud" ?> </button>
            
            
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>