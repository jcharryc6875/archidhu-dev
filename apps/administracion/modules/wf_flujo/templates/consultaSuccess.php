<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object')
?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar flujos de trabajo
    </div>
  </div>
  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('wf_flujo/list',array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
        <div class="form-group">
          <!-- Tipo Correspondencia -->
          <label for="lbgetWfBuzonId" class="col-sm-2 control-label">Tipo Correspondencia:</label>
          <div class="col-sm-3">
            <?php 
                echo object_select_tag($wf_flujos, 'getWfBuzonId', array ('related_class' => 'WfBuzon', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm',));
            ?>
          </div>      
        </div>
        
        <div class="form-group">
            <!-- Flujo ID -->
            <label for="lbgetWfBuzonId" class="col-sm-2 control-label">Flujo:</label>
                <div class="col-sm-3">
                <?php 
                    echo object_select_tag($wf_flujos, 'getWfFlujoId', array ('peer_method' => 'getListFujosOrder','related_class' => 'WfFlujo', 'include_custom'=>'Seleccione...','class'=>'form-control input-sm',));
                ?>
                </div>      
        </div>                
        
        <div class="form-group">
            <!-- Botonera -->
            <div class="col-sm-offset-4 col-sm-5">
                <button type="submit" class="btn btn-blue btn-icon">Consultar Flujos<i class="entypo-search"></i></button>             
                <?php echo button_to('Deshacer','wf_flujo/consulta',array('class' => 'btn btn-red')); ?>
            </div>
        </div>
    <div class="clear"></div>    
    </form>
  </div>
</div>