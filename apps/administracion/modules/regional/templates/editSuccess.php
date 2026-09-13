<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>/assets/js/bootstrap-switch.min.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/fileinput.js"></script>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $regional->getPrimaryKey() ? "Editar" : "Crear" ?> Regional
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('regional/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($regional, 'getRegionalId');
        ?>
       
        <div class="form-group">
            <!-- Ciudad -->
            <label for="lbgetCiudadId" class="col-sm-1 control-label">Ciudad:</label>
            <div class="col-sm-3">
                <?php echo object_select_tag($regional, 'getCiudadId', array('peer_method'=>'getAllCiudad','related_class' => 'Ciudad','include_custom'=>'Selecione...','class'=>'form-control input-sm required')); ?>
            </div>
        </div>
        
        <div class="form-group">
            <!-- Ciudad -->
            <label for="lbgetCiudadId" class="col-sm-1 control-label">Entidad:</label>
            <div class="col-sm-3">
                <?php echo object_select_tag($regional, 'getEntidadId', array('peer_method'=>'getEntidadesOrderbyAsc','related_class' => 'Entidad','include_custom'=>'Selecione...','class'=>'form-control input-sm required')); ?>
            </div>
        </div>
        
        <div class="form-group">
            <!-- Descripcion -->
            <label for="lbDescripcion" class="col-sm-1 control-label">Descripci&oacute;n:</label>
            <div class="col-sm-6">
                <?php echo object_input_tag($regional, 'getDescripcion', array('related_class' => 'Regional', 'class'=>'form-control input-sm required','size' => 65,)); ?>
            </div>
        </div>
        
        <div class="form-group">
            <!-- Direccion -->
            <label for="lbDireccion" class="col-sm-1 control-label">Direcci&oacute;n:</label>
            <div class="col-sm-6">
                <?php echo object_input_tag($regional, 'getDireccion', array('related_class' => 'Regional', 'class'=>'form-control input-sm required','size' => 65,)); ?>
            </div>
        </div>
        
        <div class="form-group">
            <!-- DirectorioName -->
            <label for="lbDirectorioName" class="col-sm-1 control-label">Nombre directorio:</label>
            <div class="col-sm-6">
                <?php //echo object_input_tag($regional, 'getDirectorioName', array('related_class' => 'Entidad', 'class'=>'form-control input-sm required','size' => 65,)); ?>
                <input class="form-control input-sm" name="directorio_name" id="directorio_name" value="<?php echo trim($regional->getDirectorioName());?>" data-mask="\w{20}" data-is-regex="true" placeholder="Solo letras o numeros separados con guion bajo, maximo 50 caracteres" type="text"/>
            </div>
        </div>
        
        <div class="form-group">    
            <!-- EsVisible -->
            <label for="lbEsVisible" class="col-sm-1 control-label">Esta Activa:</label>
            <div class="col-sm-6">
              <?php $value_check0 = $regional->getEsVisible() ? true : false; ?>
              <div class="make-switch" data-on-label="SI" data-off-label="NO">
                <?php echo checkbox_tag('es_visible', 1 , $value_check0); ?>
              </div>
            </div>
        </div>
        
        <div class="form-group">
          <!-- header logo -->
          <label for="lbadjuntos" class="col-sm-1 control-label">Adjuntar Membrete:</label>
          <div class="col-sm-6">
              <div class="input-group"> 
                <?php
                    $title = "Esta imagen sera utilizada como membrete de las comunicaciones internas y enviadas";
                    $title .= ", el tamaño debe ser de 1700px de ancho por 2200px de alto";
                    echo input_tag('image_membrete',trim($regional->getImageMembrete()),array('class'=>'form-control input-sm required','readonly'=>true,'title'=>$title,'alt'=>$title));
                ?>
                <div class="input-group-btn">         
                    <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/administracion.php/regional/file?qvars=image_membrete', 450, 250);">Seleccionar</button>
                    <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('image_membrete');"><i class="entypo-cancel-circled"></i></button>
                </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
            <!-- Vista previa -->
            <label for="lbadjuntos" class="col-sm-1 control-label">Vista previa:</label>              
            <div class="col-sm-5">
                <div class="fileinput text-center">
                	<div class="thumbnail" style="width: 170px; height: 220px;" data-trigger="fileinput" title="<?php echo $title; ?>" title="<?php echo $title; ?>">
                        <?php if(trim($regional->getImageMembrete())){ ?>
                                <?php if(file_exists($filedir.trim($regional->getImageMembrete()))){ ?>
                                        <img border="0" src="<?php echo $base_path.$directorio_header.$regional->getImageMembrete(); ?>" style="width: 99%; height: 99%;" />
                                <?php }else{ ?>                                                                            
                                        <img border="0" src="<?php echo $base_path.$directorio_header.'1700x2200.png' ?>" style="width: 99%; height: 99%;" />
                                <?php } ?>
                        <?php }else{ ?>
                                <img border="0" src="<?php echo $base_path.$directorio_header.'1700x2200.png' ?>" style="width: 99%; height: 99%;" />
                        <?php } ?>
                	</div>
                </div>
            </div>
        </div>
                     
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Datos</button>
            <a class="btn btn-red" href="<?php echo url_for('regional/index') ?>">Listar regionales</a>
          </div>
        </div>
        
        <div class="clear"></div>
      </form>
    </div>
    </div>
  </div>
</div>