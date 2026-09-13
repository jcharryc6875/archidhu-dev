<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object','jQuery'); 
?>
<!-- Imported scripts on this page -->    	
<script src="<?php echo $path_theme; ?>/assets/js/typeahead.min.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/jquery.observe_field.js"></script>
<div class="row">
    <div class="col-md-12">
    <div class="panel panel-primary" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear Novedad
        </div>
      </div>

      <div class="panel-body">

        <form name="form1" role="form" class="form-horizontal form-groups-bordered validate" action="<?php echo url_for('novedades/update'.(!$novedades->isNew() ? '?novedades_id='.$novedades->getNovedadesId() : '')) ?>" method="post">
        <?php 
            echo object_input_hidden_tag($novedades, 'getNovedadesId');
            echo input_hidden_tag('consecutivo_id', $consecutivo_id);
            echo input_hidden_tag('name_window', $consecutivo_id); 
            echo input_hidden_tag('destinatarioId');
            echo input_hidden_tag('cargousuarioId');
        ?>
        <div class="form-group">
          <?php
          if($modulo_id):
            echo input_hidden_tag('modulo_id', $modulo_id);
          else:
          ?>
            <label for="modulo_id" class="col-sm-2 control-label">Modulo</label>
            <div class="col-sm-4">
              <?php 
                echo object_select_tag($novedades, 'getModuloId', array ('related_class' => 'Modulo', 'class'=>'form-control input-sm', 'include_custom'=>'Selecione...', 'data-validate' => 'required')); 
              ?>
            </div>
          <?php
          endif;
          ?>

            <label for="tipocontrol_id" class="col-sm-2 control-label">Tipo Control</label>
            <div class="col-sm-4">
              <?php 
                echo select_tag('tipocontrol_id', objects_for_select($tipos_control, 'getTipocontrolId', 'getDescripcion', $novedades->getTipocontrolId(), array('include_custom'=>'Seleccione...')), array('class'=>'form-control input-sm', 'data-validate' => 'required'));
              ?>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
            <label for="descripcion" class="col-sm-2 control-label">Descripción Novedad</label>
            <div class="col-sm-4">
              <?php 
              echo object_textarea_tag($novedades, 'getDescripcion', array('placeholder' => 'Descripción', 'class' => 'form-control', 'data-validate' => 'required')); 
              ?>
            </div>
   
            <label for="guia" class="col-sm-2 control-label">Número de Guia</label>
            <div class="col-sm-4">
               <?php echo object_input_tag($novedades, 'getGuia', array('class' => 'form-control', 'placeholder' => 'Número de Guia')); ?>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
            <label for="numero_factura" class="col-sm-2 control-label">Número Factura</label>
            <div class="col-sm-4">
              <?php echo object_input_tag($novedades, 'getNumeroFactura', array('class' => 'form-control', 'placeholder' => 'Número de Factura')); ?>
            </div>

            <label for="valor_factura" class="col-sm-2 control-label">Valor Factura</label>
            <div class="col-sm-4">
               <?php echo object_input_tag($novedades, 'getValorFactura', array('class' => 'form-control', 'placeholder' => 'Valor de Factura')); ?>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
            <!-- Destinatario -->
            <label for="lbdestinatarioid" class="col-sm-2 control-label">Destinatario:</label>
            <div class="col-sm-4">
               <div class="input-group">
    			<span class="input-group-addon"><i class="entypo-user"></i></span>
                   <input type="text" name="destinatario" id="destinatario" class="form-control input-sm typeahead required" data-remote="<?php echo $base_path; ?>/comun.php/usuario_firma/dataList?q=%QUERY&opcion=0&campoText=destinatario&campoId=destinatarioId&cargoId=cargousuarioId" data-template="<div class='thumb-entry'><span class='image'><img src='{{img}}' width=30 height=30 /></span><span class='text'><strong>{{value}}</strong><em>{{desc}}</em></span></div>" placeholder="Digite nombre usuario" value="<?php echo $novedades->getDestinatario(); ?>" />                
                   <div class="input-group-btn">
                     <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/comun.php/usuario_firma/consulta?opcion=0&campoText=destinatario&campoId=destinatarioId&cargoId=cargousuarioId', '800', '600');">Buscar</button>
                     <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('destinatarioId');jQuery.LimpiarCampoFormulario('destinatario');jQuery.LimpiarCampoFormulario('cargousuarioId');jQuery('.typeahead').typeahead('setQuery', '');"><i class="entypo-cancel-circled"></i></button>
                   </div>              
    		   </div>
            </div>
            <!-- Descripci&oacute;n del Documento -->        
            <label for="tipo_documento" class="col-sm-2 control-label">Descripci&oacute;n del Documento</label>
            <div class="col-sm-4">
               <?php echo object_input_tag($novedades, 'getTipoDocumento', array('class' => 'form-control', 'placeholder' => 'Descripci&oacute;n del Documento')); ?>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
            <label for="archivo" class="col-sm-2 control-label">Adjuntos</label>
            <div class="col-sm-6">
              <div class="input-group">
                <?php echo input_tag('archivo', $novedades->getRuta(), array('class' => 'form-control','readonly'=>'true'));?>
                <div class="input-group-btn">         
                  <button type="button" class="btn btn-primary" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/comun.php/novedades/file', '450', '250');">Buscar</button>
                  <button type="button" class="btn btn-default" onclick="javascript:jQuery.LimpiarCampoFormulario('archivo');"><i class="entypo-cancel-circled"></i></button>
                </div>
              </div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Novedad</button>
            <a href="<?php print url_for('novedades/index?consecutivo_id='.$consecutivo_id.'&modulo_id='.$modulo_id);?>"><button type="button" class="btn btn-red">Cancelar</button></a>            
          </div>
        </div>
        <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>