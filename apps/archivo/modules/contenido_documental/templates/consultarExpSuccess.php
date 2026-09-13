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
        Consultar Expedientes
      </div>
    </div>
    <!-- Contenedor Contenido Formulario-->
    <div class="panel-body">
      <?php
      echo form_tag('contenido_documental/listExpediente', array('name'=>'form1','method'=>'GET', 'class' => 'form-horizontal form-groups-bordered validate'));
      echo input_hidden_tag('contenidounidaddocumental_id', $contenidounidaddocumental_id);
      ?>

      <div class="form-group">  
        <!-- Localizacion Expediente: -->  
        <label for="localizacionexpediente" class="col-sm-1 control-label">Localizaci&oacute;n Expediente</label>
        <div class="col-sm-4">
            <?php
                $destinos = array(1=>'Archivo Gesti&oacute;n', 2=>'Archivo Central');
                echo select_tag('localizacionexpediente', options_for_select($destinos, ''),array('class'=>'form-control input-sm'));
            ?>
        </div>        
        <!-- Esta marcado: -->  
        <label for="marcado" class="col-sm-1 control-label">Esta marcado</label>
        <div class="col-sm-4">
            <?php
                $marcado = array('1'=>'Si','0'=>'No');
                echo select_tag('marcado', options_for_select($marcado, '',array('include_custom'=>'Seleccione...')),array('class'=>'form-control input-sm'));
            ?>
        </div>
      </div>

      <div class="form-group">          
        <!-- codigo_barras -->
        <label for="codigo_barras" class="col-sm-1 control-label">N&uacute;mero Expediente</label>
        <div class="col-sm-3">
          <?php echo input_tag('codigo_barras', '', array('class' => 'form-control input-sm')); ?>
        </div>
        <!-- id_expediente -->
        <label for="id_expediente" class="col-sm-1 control-label">ID Expediente</label>
        <div class="col-sm-3">
          <?php echo input_tag('id_expediente', '', array('class' => 'form-control input-sm')); ?>
        </div>
      </div>

      <div class="form-group">  
        <!-- titulo -->  
        <label for="titulo" class="col-sm-1 control-label">Nombre Expediente</label>
        <div class="col-sm-10">
          <?php echo input_tag('titulo', '', array('class'=>'form-control input-sm')); ?>
        </div>        
      </div>
      
      <div class="form-group">  
        <!-- contenido -->  
        <label for="contenido" class="col-sm-1 control-label">Contenido</label>
        <div class="col-sm-10">
          <?php echo textarea_tag('contenido', '', array('class'=>'form-control input-sm','size' => '37x2')); ?>
        </div>
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-5">
          <button type="submit" class="btn btn-blue btn-icon" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Procesando">Buscar Expedientes<i class="entypo-search"></i></button>
        </div>
      </div>
      </form>
      </div>
    </div>
  </div>
</div>