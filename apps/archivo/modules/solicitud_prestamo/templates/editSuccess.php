<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('jQuery','Object');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear Solicitud de Prestamo
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('solicitud_prestamo/update', array('name'=>'crear', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
        echo object_input_hidden_tag($solicitud_prestamo, 'getSolicitudprestamoId'); 
        echo input_hidden_tag('unidaddocumental_id', $sf_params->get('unidaddocumental_id'));
        ?>
                      
      <div class="form-group">
        <div class="row">
            <div class="col-sm-6">
            	<div class="col-sm-4"><p><strong>Usuario</strong></p></div>
            	<div class="col-sm-8"><p><?php print $usuario->getNombreAll(); ?></p></div>
            </div>            
        </div>
      </div>
      
      <div class="form-group">
        <div class="row">
            <div class="col-sm-6">
            	<div class="col-sm-4"><p><strong>Unidad documental</strong></p></div>
            	<div class="col-sm-8"><p><?php print $unidad_documental->getTitulo(); ?></p></div>
            </div>            
        </div>
      </div>
      
      <div class="form-group">
          <div class="row">
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Observaciones</strong></p></div>
              <div class="col-sm-8"><?php echo textarea_tag('observaciones', '', array('class' => 'form-control')); ?>
              </div>
            </div>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <?php echo jq_submit_to_remote('radicar','Solicitar Prestamo',array(
                'url'      => "solicitud_prestamo/update",
                'update'   => "",
                //'loading'  => "Element.show('indicator_fondo');Element.show('indicator')",
                'complete' => "javascript:parent.jQuery.ReloadAndCloseModalSIMAD();",
              ),array('method' => 'post','title' => 'Radicar Solicitud Prestamo', 'class'=>'btn btn-success')) 
            ?>
            <?php echo jq_link_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>
      
      