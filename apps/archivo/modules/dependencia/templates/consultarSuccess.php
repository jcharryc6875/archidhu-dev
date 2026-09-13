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
          Consultar Unidad Administrativa
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      <?php 
          echo form_tag('dependencia/list', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
          echo input_hidden_tag('procesos_id',$sf_params->get('procesos_id')); 
          echo input_hidden_tag('oficinaproductora_id',$sf_params->get('oficinaproductora_id'));
      ?>

      
      <!-- Entidad -->
      <div class="form-group">
          <label for="lbgetEntidadId" class="col-sm-1 control-label">Entidad:</label>
          <div class="col-sm-5">
            <?php 
                echo object_select_tag($dependencia, 'getEntidadId', array ('related_class' => 'Entidad','include_custom'=>'Seleccione entidad...','class'=>'form-control input-sm', )); 
            ?>
          </div>
      </div>
      
      <div class="form-group">      
          <!-- Nombre -->
          <label for="lbnombre" class="col-sm-1 control-label">Nombre:</label>
          <div class="col-sm-5">        
            <?php 
                echo object_input_tag($dependencia, 'getNombre', array ('size' => 43,'class'=>'form-control input-sm',));
            ?>
          </div>
      </div>
      
      <div class="form-group">      
          <!-- Codigo -->
          <label for="lbcodigo" class="col-sm-1 control-label">Codigo:</label>
          <div class="col-sm-5">        
            <?php 
                echo object_input_tag($dependencia, 'getCodigo', array ('size' => 43,'class'=>'form-control input-sm',));
            ?>
          </div>
      </div>
      
      <div class="form-group">      
          <label for="lextension" class="col-sm-1 control-label">Ordenar por:</label>
          <div class="col-sm-2">        
            <?php 
                $ordenList = array('NOMBRE'=>'Nombre','CODIGO'=>'Codigo');
                echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione...',)), array( 'class'=>'form-control input-sm',));
            ?>
          </div>     
      </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar</button>
            <?php echo button_to('Deshacer','dependencia/consultar',array('class' => 'btn btn-red ')); ?>
        </div>
      </div>
      <div class="clear"></div>    
    </form>
  </div>
</div>
</div>
</div>