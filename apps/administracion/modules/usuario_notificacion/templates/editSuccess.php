<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $usuario_notificacion->getPrimaryKey() ? "Editar" : "Crear" ?> Notifiacion Usuario
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
            echo form_tag('usuario_notificacion/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
            echo object_input_hidden_tag($usuario_notificacion, 'getUsuarionotificacionId');
            echo object_input_hidden_tag($usuario_notificacion, 'getUsuarioId');
        ?>
        
        <div class="form-group">
            <!-- Tipo Notificaci&oacute;n -->
            <label for="lbDescripcion" class="col-sm-1 control-label">Tipo Notificaci&oacute;n:</label>
            <div class="col-sm-6">
                <?php
                    echo object_select_tag($usuario_notificacion, 'getTiponotificacionId', array('related_class' => 'TipoNotificacion','include_custom'=>'Selecione...','class'=>'form-control input-sm required'));
                ?>
            </div>
        </div>

        <div class="form-group">
            <!-- EmailMensajeBody -->
            <label for="lbEmailMensajeBody" class="col-sm-1 control-label">Asunto Email:</label>
            <div class="col-sm-11">
                <?php echo object_input_tag($usuario_notificacion, 'getEmailAsunto', array('class'=>'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <!-- EmailMensajeBody -->
            <label for="lbEmailMensajeBody" class="col-sm-1 control-label">Cuerpo Email:</label>
            <div class="col-sm-11">
                <?php echo object_textarea_tag($usuario_notificacion, 'getEmailMensajeBody', array('class'=>'form-control input-sm ckeditor1','size' => '50X4')); ?>
            </div>
        </div>

        <div class="form-group">
            <!-- Botonera -->
                <div class="col-sm-offset-4 col-sm-11">
                <button type="submit" class="btn btn-success">Guardar Registro</button>
                <a class="btn btn-red" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();" >Cerrar</a>
            </div>
        </div>
        <div class="clear"></div>
      </form>
    </div>
    </div>
  </div>
</div>
        