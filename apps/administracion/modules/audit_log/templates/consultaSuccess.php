<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Auditoria
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('audit_log/list',array('name'=>'form1','method'=>'GET','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    
    <div class="form-group">
      <!-- Usuario -->
      <label for="lbgetUsuarioId" class="col-sm-1 control-label">Usuario</label>
      <div class="col-sm-4">
        <?php echo object_select_tag($audit_log, 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario', 'class'=>'form-control input-sm','include_custom'=>'Seleccione...')) ?>
        
      </div>          
    </div>
    
    <div class="form-group">      
      <!-- Modulo -->
      <label for="lbModuloId" class="col-sm-1 control-label">Modulo</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($audit_log, 'getModuloId', array ('peer_method'=>'getOrdenModulo','related_class' => 'Modulo','class'=>'form-control input-sm','include_custom'=>'Seleccione...',)); 
        ?>
      </div>
    </div>        
    
    <div class="form-group">      
      <!-- Codigo Principal -->
      <label for="lbgetCodigoPrincipal" class="col-sm-1 control-label">Codigo Principal</label>
      <div class="col-sm-4">
        <?php 
            echo object_input_tag($audit_log, 'getCodigoPrincipal', array ('class'=>'form-control input-sm',)); 
        ?>
      </div>
    </div>
    
    <div class="form-group">          
      <!-- Fecha Creacion -->
        <label for="lbFechacreacion" class="col-sm-1 control-label">Fecha creacion</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fechaCreaInicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>
        <label for="lbFechacreacionfinal" class="col-sm-1 control-label">Hasta:</label>
        <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fechaCreaFinal', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
        </div>                   
    </div>
    
    <div class="form-group">    
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">            
            <button type="submit" class="btn btn-success">Consultar</button>
            <?php
                echo button_to("Regresar listado",$base_path.'/administracion.php/audit_log/list', array('class' => 'btn btn-blue btn-sm'));
            ?>
        </div>    
    </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>