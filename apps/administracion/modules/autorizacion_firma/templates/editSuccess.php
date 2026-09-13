<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>/assets/js/bootstrap-switch.min.js"></script>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <?php 
        if($sf_params->get('cod_msg') == 1)
        { ?>
            <div class="alert alert-success">
                La autorizaci&oacute;n de firma correspondencia fue creada satisfactoriamente!
            </div>
        <?php
        }elseif($sf_params->get('cod_msg') == 2){ 
        ?>
            <div class="alert alert-danger">
                Se encontro un error y no se pudo crear la autorizaci&oacute;n de correspondencia!, por favor intente de nuevo
            </div>
        <?php
        }elseif($sf_params->get('cod_msg') == 3){ 
        ?>
            <div class="alert alert-success">
                La autorizaci&oacute;n de firma correspondencia fue actualizada satisfactoriamente!
            </div>
        <?php
        } 
      ?>
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $autorizacion_firma->getPrimaryKey() ?  "Editar" : "Crear"; ?> Autorizaci&oacute;n Firma Correspondencia
        </div>
      </div>      
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('autorizacion_firma/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($autorizacion_firma, 'getAutorizacionfirmaId');
        ?>
       
        <div class="form-group">
          <!-- Estado Autorizacion -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Estado Autorizaci&oacute;n</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($autorizacion_firma , 'getEstadofirmaautoId', array ('related_class' => 'EstadoFirmaAuto','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)) ?>            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario Autoriza -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Usuario Autoriza</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($autorizacion_firma , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario','class'=>'form-control input-sm select2 required','include_custom'=>'Seleccione...','name'=>'usuario_autoriza_id','id'=>'usuario_autoriza_id')) ?>
            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario Autorizado -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Usuario Autorizado</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($usuario_autorizado , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario','class'=>'form-control input-sm select2 required','include_custom'=>'Seleccione...','name'=>'usuario_autorizado_id','id'=>'usuario_autorizado_id',)) ?>
            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Modulo: -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Modulo:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($autorizacion_firma, 'getModuloId', array ('related_class' => 'Modulo','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)) ?>            
          </div>          
        </div>
        
		<div class="form-group">
			<!-- Requiere respuesta -->
			<label for="lbgetFirmaElectronica" class="col-sm-1 control-label">Firma Electronica:</label>
			<div class="col-sm-2">
				<?php $value_check0 = $autorizacion_firma->getFirmaElectronica() ? true : false; ?>
				<div class="make-switch" data-on="success" data-off="warning" data-on-label="SI" data-off-label="NO">
					<?php echo checkbox_tag('firma_electronica', 1 , $value_check0); ?>
				</div>
			</div>
          
			<!-- Use Membrete -->
            <label for="lbgetFirmaFisica" class="col-xs-1 control-label">Firmas Fisicas:</label>
            <div class="col-sm-2">
                <?php $value_check0 = $autorizacion_firma->getFirmaFisica() ? true : false; ?>
                <div class="make-switch" data-on="success" data-off="warning" data-on-label="SI" data-off-label="NO">
                    <?php echo checkbox_tag('firma_fisica', 1 , $value_check0); ?>
                </div>
            </div>
        </div>
		
        <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $autorizacion_firma->getPrimaryKey() ? "Actualizar Autorización" : "Guardar Autorización"?> </button>
            <?php 
            if($autorizacion_firma->getPrimaryKey())
            {
                echo button_to("Eliminar Autorización",$base_path.'/administracion.php/autorizacion_firma/delete?autorizacionfirma_id='.$autorizacion_firma->getAutorizacionfirmaId(), array('class' => 'btn btn-red btn-sm','post=true&confirm=Estas Seguro?'));
            }
            echo '&nbsp;';
            echo button_to("Listar Autorizaciones",$base_path.'/administracion.php/autorizacion_firma/list', array('class' => 'btn btn-blue btn-sm')); 
            ?> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>