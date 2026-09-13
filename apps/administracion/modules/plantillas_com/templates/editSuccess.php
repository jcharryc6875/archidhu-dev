<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<style>
    #field-container {
        width: 150px;
        border: 1px solid #ccc;
        padding: 10px;
    }
    .drag-field {
        background: #f0f0f0;
        padding: 5px;
        margin: 5px 0;
        cursor: grab;
        border: 1px solid #999;
    }
</style>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo $plantillas_com->getPrimaryKey() ? "Editar" : "Crear" ?> Plantilla de Comunicaciones
        </div>
      </div>
      <?php if ($sf_user->hasFlash('notice')): ?>
        <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('notice') ?></div>
      <?php endif ?>
      <?php if ($sf_user->hasFlash('success')): ?>
        <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('success') ?></div>
      <?php endif ?>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('plantillas_com/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($plantillas_com, 'getPlantillascomId');
        ?>

        <div class="form-group">
          <!-- Dependencia -->
          <label for="lbgetDependenciaId" class="col-sm-1 control-label">Dependencia:</label>
          <div class="col-sm-3">
              <?php echo object_select_tag($plantillas_com, 'getDependenciaId', array('peer_method'=>'getDependenciaAllJoin','related_class' => 'Dependencia','include_custom'=>'Selecione...','class'=>'select2 form-control input-sm')); ?>
          </div>
          <!-- Regional -->
          <label for="lbgetRegionalId" class="col-sm-1 control-label">Regional:</label>
          <div class="col-sm-3">
              <?php echo object_select_tag($plantillas_com, 'getRegionalId', array('related_class' => 'Regional','include_custom'=>'Selecione...','class'=>'select2 form-control input-sm')); ?>
          </div>
          <!-- Modulo -->
          <label for="lbgetModuloId" class="col-sm-1 control-label">Modulo(*):</label>
          <div class="col-sm-2">
              <?php echo object_select_tag($plantillas_com, 'getModuloId', array('related_class' => 'Modulo','include_custom'=>'Selecione...','class'=>'form-control input-sm required')); ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Nombre: -->
          <label for="lbgetNombre" class="col-sm-1 control-label">Nombre(*):</label>
          <div class="col-sm-3">
            <?php
                echo object_input_tag($plantillas_com, 'getNombre', array('related_class' => 'PlantillasCom','data-validate'=>'maxlength[50]', 'class'=>'form-control input-sm required'));
            ?>
          </div>
          <!-- Codigo: -->
          <label for="lbgetCodigo" class="col-sm-1 control-label">C&oacute;digo(*):</label>
          <div class="col-sm-2">
            <?php
                echo object_input_tag($plantillas_com, 'getCodigo', array('related_class' => 'PlantillasCom','data-validate'=>'maxlength[30]', 'class'=>'form-control input-sm required'));
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Descripcion: -->
          <label for="lbDescripcion" class="col-sm-1 control-label">Descripci&oacute;n(*):</label>
          <div class="col-sm-7">
            <?php
                echo object_input_tag($plantillas_com, 'getDescripcion', array('related_class' => 'PlantillasCom','data-validate'=>'maxlength[50]', 'class'=>'form-control input-sm required'));
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Descripcion -->
          <label for="lbEsVisible" class="col-sm-1 control-label">Esta Activa:</label>
          <div class="col-sm-1">
            <?php 
                echo object_checkbox_tag($plantillas_com, 'getEsActual', array ('class' => 'form-control input-sm icheck'));
            ?>
          </div>
          <!-- Descripcion -->
          <label for="lbEsVisible" class="col-sm-1 control-label">Usar Encabezados:</label>
          <div class="col-sm-1">
            <?php 
                echo object_checkbox_tag($plantillas_com, 'getUseHeaders', array ('class' => 'form-control input-sm icheck'));
            ?>
          </div>
          <!-- Descripcion -->
          <label for="lbEsVisible" class="col-sm-1 control-label">Usar Membrete:</label>
          <div class="col-sm-1">
            <?php 
                echo object_checkbox_tag($plantillas_com, 'getUseMembrete', array ('class' => 'form-control input-sm icheck'));
            ?>
          </div>
          <!-- Descripcion -->
          <label for="lbEsVisible" class="col-sm-1 control-label">Generar Servicio:</label>
          <div class="col-sm-1">
            <?php 
                echo object_checkbox_tag($plantillas_com, 'getGeneraServicio', array ('class' => 'form-control input-sm icheck'));
            ?>
        </div>
        </div>

        <?php if(empty($plantillas_com->getGeneraServicio())) { ?>
          <div class="form-group servfieldstplcom" style="display: none;">
          </div>
        <?php }else{ ?>
            <div class="form-group servfieldstplcom">
              <?php echo PlantillasComPeer::getHtmlFieldsByServicio($plantillacom_servicio->getTiposervicioId(),$plantillacom_servicio->getPrioridadsolicitudservicioId()); ?>
          </div>
        <?php } ?>

        <div class="form-group">
          <!-- header logo -->
          <label for="lbadjuntos" class="col-sm-1 control-label">Adjuntar Membrete:</label>
          <div class="col-sm-6">
              <div class="input-group"> 
                <?php
                    $title = "Esta imagen sera utilizada como membrete de las plantillas que generan documentos";
                    $title .= ", el tama&ntilde;o debe ser de 1700px de ancho por 2200px de alto";
                    echo input_tag('image_membrete',trim($plantillas_com->getImageMembrete()),array('class'=>'form-control input-sm required','readonly'=>true,'title'=>$title,'alt'=>$title));
                ?>
                <div class="input-group-btn">         
                    <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/administracion.php/plantillas_com/file?qvars=image_membrete', 450, 250);">Seleccionar</button>
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
                        <?php if(trim($plantillas_com->getImageMembrete())){ ?>
                                <?php if(file_exists($filedir.trim($plantillas_com->getImageMembrete()))){ ?>
                                        <img border="0" src="<?php echo $base_path.$directorio_header.$plantillas_com->getImageMembrete(); ?>" style="width: 99%; height: 99%;" />
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
          <div class="form-group tmpresponse">
            <!-- Contenido -->
            <div class="col-sm-12">
              <label for="lbcontenido" class="col-sm-1 control-label">Contenido:</label>
            </div>          
          </div>
          <!-- Contents -->
          <div class="col-sm-8">
              <?php echo object_textarea_tag($plantillas_com, 'getContents', array('class'=>'form-control input-sm ckeditor1','height' => '400px')); ?>
          </div>
          <!-- Contents -->
          <div class="col-sm-3">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="panel-title">C&oacute;digos Etiquetas</div>
              </div>
              <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive">
                  <tbody>
                    <tr>
                      <td>{[FIRMAS_NOMBRE]}</td>
                      <td>{[FIRMAS_CARGOS]}</td>
                    </tr>
                    <tr>
                      <td>{[FIRMAS_DEPENDENCIA]}</td>
                      <td>{[FIRMA_MECANICA]}</td>
                    </tr>
                    <tr>
                      <td>{[FIRMAS_REGIONAL]}</td>
                      <td>{[DESTINO_PREFIJO]}</td>
                    </tr>
                    <tr>
                      <td>{[DESTINO_NOMBRE]}</td>
                      <td>{[DESTINO_FUNCIONARIO]}</td>
                    </tr>
                    <tr>
                      <td>{[DESTINO_EMAIL]}</td>
                      <td>{[DESTINO_IDENTIFICACION]}</td>
                    </tr>
                    <tr>
                      <td>{[DESTINO_DIRECCION]}</td>
                      <td>{[DESTINO_CIUDAD]}</td>
                    </tr>
                    <tr>
                      <td>{[RADICADOR_NOMBRE]}</td>
                      <td>{[RADICADOR_CARGO]}</td>
                    </tr>
                    <tr>
                      <td>{[RADICADOR_AREA]}</td>
                      <td>{[RADICADOR_FMECANICA]}</td>
                    </tr>
                    <tr>
                      <td>{[UPROYECTA_NOMBRE]}</td>
                      <td>{[UPROYECTA_CARGO]}</td>
                    </tr>
                    <tr>
                      <td>{[UPROYECTA_AREA]}</td>
                      <td>{[UPROYECTA_FMECANICA]}</td>
                    </tr>
                    <tr>
                      <td>{[REVISOR_NOMBRE]}</td>
                      <td>{[REVISOR_CARGO]}</td>
                    </tr>
                    <tr>
                      <td>{[REVISOR_AREA]}</td>
                      <td>{[REVISOR_FMECANICA]}</td>
                    </tr>
                    <tr>
                      <td>{[APROBADOR_NOMBRE]}</td>
                      <td>{[APROBADOR_CARGO]}</td>
                    </tr>
                    <tr>
                      <td>{[APROBADOR_AREA]}</td>
                      <td>{[APROBADOR_FMECANICA]}</td>
                    </tr>                    
                    <tr>
                      <td>{[FECHA_COM]}</td>
                      <td>{[ASUNTO_COM]}</td>
                    </tr>
                    <tr>
                      <td>{[RADICADO_COM]}</td>
                      <td>{[ANEXOS_COM]}</td>
                    </tr>
                    <tr>
                      <td>{[VIGENCIA_COM]}</td>
                      <td>{[AREA_CODIGO_COM]}</td>
                    </tr>
					<tr>
                      <td>{[DESTINO_CARGO]}</td>
                      <td>{[CODEBAR_COM]}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Registro</button>
            <?php echo button_to("Cancelar",'plantillas_com/list', array('class' => 'btn btn-red btn-sm')); ?>
          </div>
        </div>
        <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>
        