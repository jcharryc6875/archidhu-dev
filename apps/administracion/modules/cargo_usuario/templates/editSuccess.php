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
          <?php echo $form->getPrimaryKey() ?  "Editar" : "Crear"; ?> Cargos Usuario
        </div>
      </div>      
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('cargo_usuario/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($form, 'getCargoUsuarioId');
        ?>
       
        <div class="form-group">
          <!-- Cargo -->
          <label for="lbCargo_id" class="col-sm-1 control-label">Cargo</label>
          <div class="col-sm-4">
            <?php 
                echo object_select_tag($form, 'getCargoId', array ('peer_method'=>'getOrdenCargo','related_class' => 'Cargo','class'=>'form-control input-sm required select2','include_custom'=>'Seleccione...',)); 
            ?>            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario -->
          <label for="lbgetUsuarioId" class="col-sm-1 control-label">Usuario</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($form, 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario', 'class'=>'form-control input-sm required select2','include_custom'=>'Seleccione...')) ?>
            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario -->
          <label for="lbgetUsuarioId" class="col-sm-1 control-label">Dependencia</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($form, 'getDependenciaId', array ('peer_method'=>'getDependenciaAll','related_class' => 'Dependencia', 'class'=>'form-control input-sm select2','include_custom'=>'Seleccione...')) ?>
            
          </div>          
        </div>

        <div class="form-group">
          <!-- Fecha Incio Cargo -->          
          <label for="fecha_inicio_cargo" class="col-sm-1 control-label">Fecha Incio Cargo</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_inicio', $form->getFechaInicio(), array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Fecha Fin Cargo -->          
          <label for="fecha_fin_cargo" class="col-sm-1 control-label">Fecha Fin Cargo</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_fin', $form->getFechaFin(), array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario -->
          <label for="lbgetUsuarioId" class="col-sm-1 control-label">Es Activo</label>
          <div class="col-sm-2">
            <?php 
                echo object_checkbox_tag($form, 'getEsActual',array('class' => 'form-control input-sm'),true); 
            ?>            
          </div>          
        </div>
        
        <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $form->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button>
            <?php 
            if($form->getPrimaryKey())
            {
                if(!$form->getEsPrincipal() && 1 != 1)
                {
                    echo button_to("Eliminar",$base_path.'/administracion.php/cargo_usuario/delete?cargousuario_id='.$form->getPrimaryKey(), array('class' => 'btn btn-red btn-sm','post=true&confirm=Estas Seguro?'));
                }
            }
            echo '&nbsp;';
            echo button_to("Listar Cargos Usuarios",$base_path.'/administracion.php/cargo_usuario/index', array('class' => 'btn btn-blue btn-sm')); 
            ?> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>