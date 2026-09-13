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
          <?php echo $entidad->getEntidadId() ? "Editar" : "Crear" ?> Entidad
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('entidad/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($entidad, 'getEntidadId');
        ?>
       
        <div class="form-group">
          <!-- Modulo: -->
          <label for="lbDescripcion" class="col-sm-1 control-label">Descripcion:</label>
          <div class="col-sm-4">
            <?php
                echo object_input_tag($entidad, 'getDescripcion', array('related_class' => 'Entidad', 'class'=>'form-control input-sm required','size' => 65,));
            ?>
          </div>
          
          <!-- Codigo: -->
          <label for="lbCodigo" class="col-sm-1 control-label">Codigo:</label>
          <div class="col-sm-4">
            <?php
                echo object_input_tag($entidad, 'getCodigo', array('related_class' => 'Entidad','class'=>'form-control input-sm','size' => 65,));
            ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- Modulo: -->
          <label for="lbDescripcion" class="col-sm-1 control-label">Nombre directorio:</label>
          <div class="col-sm-4">
            <?php
                echo object_input_tag($entidad, 'getDirectorioName', array('related_class' => 'Entidad', 'class'=>'form-control input-sm required','size' => 65,));
            ?>
          </div>
          
          <!-- Codigo: -->
          <label for="lbCodigo" class="col-sm-1 control-label">Es actual:</label>
          <div class="col-sm-4">
            <?php
                echo object_input_tag($entidad, 'getEsActual', array('related_class' => 'Entidad','class'=>'form-control input-sm required','size' => 65,));
            ?>
          </div>
      </div>
      
      <div class="form-group">
          <!-- header logo -->
          <label for="lbadjuntos" class="col-sm-1 control-label">Encabezados Comunicaciones:</label>
          <div class="col-sm-6">
              <div class="input-group"> 
                    <?php
                        $title = "Esta imagen sera utilizada para el encabezado de las comunicaciones y reportes del sistema";
                        echo input_tag('name_logo',$entidad->getLogoHeader(),array('class'=>'form-control input-sm','readonly'=>true,'title'=>$title,'alt'=>$title));
                    ?>
                    <div class="input-group-btn">         
                        <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/administracion.php/entidad/file?qvars=name_logo', 450, 250);">Seleccionar</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('name_logo');"><i class="entypo-cancel-circled"></i></button>
                    </div>                    
              </div>                
          </div>
       </div>
      
       <div class="form-group">
          <!-- Imagenes -->
          <label for="lbadjuntos" class="col-sm-1 control-label">Imagen Licencia:</label>
          <div class="col-sm-6">
              <div class="input-group"> 
                    <?php
                        $title = "Esta imagen sera utilizada para visualizar el logo corporativo al ingresar a la aplicacion";
                        echo input_tag('corp_logo',$entidad->getLogoCorporativo(),array('class'=>'form-control input-sm','readonly'=>true,'title'=>$title,'alt'=>$title));
                    ?>
                    <div class="input-group-btn">         
                        <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/administracion.php/entidad/corpFile?qvars=corp_logo', 450, 250);">Seleccionar</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('corp_logo');"><i class="entypo-cancel-circled"></i></button>
                    </div>                    
              </div>  
          </div>
      </div>
      <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a class="btn btn-red" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();" >Cerrar</a>
          </div>
        </div>
        <div class="clear"></div>
      </form>
    </div>
    </div>
  </div>
</div>
        