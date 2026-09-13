<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear/Editar Perfil por Usuario
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('roles_por_usuario/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($rol_por_usuario, 'getRolporusuarioId');
        ?>
       
        <div class="form-group">    
          <!-- Modulo: -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Usuario:</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($rol_por_usuario, 'getUsuarioId', array ('peer_method'=>'getAllUser', 'related_class' => 'Usuario','class'=>'form-control input-sm required select2','include_custom'=>'Seleccione...',)) ?>
            
            
          </div>
          
          <!-- Nombre: -->
          <label for="ldependencia_id" class="col-sm-1 control-label">Perfil:</label>
          <div class="col-sm-4">          
            <?php echo object_select_tag($rol_por_usuario, 'getRolId', array ('peer_method'=>'getOrdenarRol','related_class' => 'Rol','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)) ?>
            
            
          </div>
         </div>
      
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $rol_por_usuario->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>