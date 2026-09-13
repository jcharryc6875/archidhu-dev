<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Estadisticas
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('estadistica/list',array('name'=>'consultar', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      <!-- Modulo -->
      <label for="lbgetModulo" class="col-sm-1 control-label">Modulo:</label>
      <div class="col-sm-4">
        <?php echo object_select_tag($estadistica, 'getModulo', array ('related_class' => 'Modulo','class'=>'form-control input-sm','include_custom'=>'Seleccione...',)) ?>            
      </div>          
    </div>
    
    <div class="form-group">      
      <!-- Nombre -->
      <label for="lbNombre" class="col-sm-1 control-label">Nombre</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($estadistica, 'getNombre', array ('class'=>'form-control input-sm')); 
        ?>
      </div>
    </div>
    
    <div class="form-group">      
      <!-- Descripcion -->
      <label for="lbDescripcion" class="col-sm-1 control-label">Descripcion</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($estadistica, 'getDescripcion', array ('class'=>'form-control input-sm')); 
        ?>
      </div>
    </div>
    
    <div class="form-group">          
      <!-- Fecha Creacion -->
        <label for="lbFechacreacion" class="col-sm-1 control-label">Fecha creacion</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>
        <label for="lbFechacreacionfinal" class="col-sm-1 control-label">Hasta:</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>                   
    </div>
    
    <div class="form-group">    
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">            
            <button type="submit" class="btn btn-success">Consultar</button>
        </div>    
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>