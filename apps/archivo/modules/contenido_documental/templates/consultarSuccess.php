<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
?>
<div class="row">
  <div class="col-md-12">
  <!-- Contenedor Pagina -->
  <div class="panel panel-gradient" data-collapsed="0">
    <div class="panel-heading">
      <div class="panel-title">
        Consultar Tipo Documental
      </div>
    </div>
    <!-- Contenedor Contenido Formulario-->
    <div class="panel-body">
      <?php
      echo form_tag('contenido_documental/list', array('name'=>'consultar','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered validate'));
      echo input_hidden_tag('unidaddocumental_id', $sf_params->get('unidaddocumental_id')); 
      echo input_hidden_tag('modulo', $sf_params->get('modulo'));
      ?>

      <div class="form-group">  
        <!-- Tipo Documental -->  
        <label for="tipodocumental_id" class="col-sm-2 control-label">Tipo Documental</label>
        <div class="col-sm-4">
          <select name="tipo_documental" id="tipo_documental" class="form-control input-sm">
            <option value="">Seleccione Tipo Documental...</option>
            <?php 
            $tipos = $tipos_documentales;
              foreach($tipos as $tipo){
              echo "<option value='".$tipo->getTipodocumentalId()."'";
              if($tipo->getTipodocumentalId() == 0){
                echo " selected ";
              }
              echo ">".$tipo->getDescripcion()."</option>";
            }
            ?>
          </select>

        </div>

        <!-- Descripcion -->
        <label for="descripcion" class="col-sm-1 control-label">Descripción</label>
        <div class="col-sm-5">
          <?php echo input_tag('descripcion', '', array('class' => 'form-control input-sm')); ?>
        </div>
      </div>

      <div class="form-group">  
        <!-- Numero Remision -->  
        <label for="numRemision" class="col-sm-2 control-label">Numero Remision</label>
        <div class="col-sm-2">
          <?php echo input_tag('numRemision', '', array('class'=>'form-control input-sm')); ?>
        </div>

        <!-- Folios -->
        <label for="folios" class="col-sm-1 control-label">Folios</label>
        <div class="col-sm-1">
          <?php echo input_tag('folios', '', array('class' => 'form-control input-sm')); ?>
        </div>

        <!-- Estado -->  
        <label for="estado" class="col-sm-1 control-label">Estado</label>
        <div class="col-sm-2">
          <?php echo object_select_tag('estado', 'getEstadocontenidounidaddocId', array('class' => 'form-control input-sm', 'related_class' => 'EstadoContenidoUnidadDocumental', 'include_custom'=>'Seleccione Estado..')); ?>
        </div>

        <!-- Verificacion -->
        <label for="verificacion" class="col-sm-1 control-label">Verificación</label>
        <div class="col-sm-2">
          <?php echo object_select_tag('verificacion', 'getVerificacioncontunidaddocId', array('class' => 'form-control input-sm', 'related_class' => 'VerificacionContUnidadDoc', 'include_custom' => 'Seleccione Verificacion...')); ?>
        </div>
      </div>

      <div class="form-group">

        <!-- Fecha -->
        <label for="desde" class="col-sm-2 control-label">Fecha Creación</label>
        <div class="col-sm-2">
          <div class="input-group">
                  <?php
                  $fecha_documento = '';
                  echo input_tag('desde', $fecha_documento, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
        
        </div>

        <!-- Fecha -->
        <label for="hasta" class="col-sm-1 control-label">Hasta</label>
        <div class="col-sm-2">
          <div class="input-group">
                  <?php
                  $fecha_documento = '';
                  echo input_tag('hasta', $fecha_documento, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
        
        </div>
      </div>
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-5">
          <button type="submit" class="btn btn-blue btn-icon" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order">Consultar<i class="entypo-search"></i></button>
        </div>
      </div>
      </form>
      </div>
    </div>
  </div>
</div>