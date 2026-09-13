<?php use_helper('Object') ?>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Perfiles por Usuario
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('roles_por_usuario/list',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">
      <!-- Modulo: -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Perfil:</label>
      <div class="col-sm-4">        
        <?php echo object_select_tag($RolPorUsuario, 'getRolId', array ('related_class' => 'Rol','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm',)); ?>
      </div>
    </div>
    
    <div class="form-group">      
      <!-- Nombre: -->
      <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Usuario:</label>
      <div class="col-sm-4">
        <!--input size=43 name=nombre type=text value=""/-->
        <?php echo object_select_tag($RolPorUsuario, 'getUsuarioId', array ('peer_method'=>'getAllUser','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm select2')); ?>   
      </div>
    </div>
    
    <div class="form-group">   
      <!-- Ordenar Por: -->
      <label for="lnombre" class="col-sm-1 control-label">Ordenar Por:</label>
      <div class="col-sm-4">  
          <?php $ordenList = array('DESCRIPCION'=>'Perfil','NOMBRE'=>'Usuario') ?>
          <?php echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',)), array('class'=>'form-control input-sm',)); ?>
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