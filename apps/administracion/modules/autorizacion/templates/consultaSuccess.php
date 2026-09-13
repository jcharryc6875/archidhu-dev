<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Autorizaci&oacute;n
    </div>
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('autorizacion/list',array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      <!-- Usuario Autoriza -->  
      <label for="usuario_autoriza" class="col-sm-1 control-label">Usuario Autoriza:</label>
      <div class="col-sm-5">
        <?php
            echo object_select_tag($usuarios , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario', 'include_custom'=>'Seleccione...', 'name'=>'userAutoriza','class'=>'form-control input-sm'));
        ?>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Usuario Autorizado -->  
      <label for="usuario_autorizado" class="col-sm-1 control-label">Usuario Autorizado:</label>
      <div class="col-sm-5">
        <?php
            echo object_select_tag($usuarios , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario', 'include_custom'=>'Seleccione...', 'name'=>'userAutorizado','class'=>'form-control input-sm'));
        ?>
      </div>
    </div>
    
    <div class="form-group">
        <!-- Fecha Creacion -->
        <label for="fechaInicial" class="col-sm-1 control-label">Fecha Inicial:</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>
        
        <label for="fechaFinal" class="col-sm-1 control-label">Fecha Final</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-3">
            <button type="submit" class="btn btn-blue btn-icon">Consultar Registro<i class="entypo-search"></i></button>
            <?php echo button_to('Cancelar','autorizacion/consulta',array('class' => 'btn btn-red')); ?>
        </div>
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>