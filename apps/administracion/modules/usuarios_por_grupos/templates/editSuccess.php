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
          <?php echo $usuario_por_grupo->getPrimaryKey() ?  "Editar" : "Crear"; ?> Usuarios Por Grupos
        </div>
      </div>      
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('usuarios_por_grupos/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($usuario_por_grupo, 'getUsuarioporgrupoId');
        ?>
       
        <div class="form-group">
          <!-- Usuario -->
          <label for="lbUsuario_id" class="col-sm-1 control-label">Usuario</label>
          <div class="col-sm-4">
            <?php 
                echo object_select_tag($usuario_por_grupo, 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)); 
            ?>            
          </div>          
        </div>
        
        <div class="form-group">
          <!-- Usuario -->
          <label for="lbgetUsuarioId" class="col-sm-1 control-label">Grupo</label>
          <div class="col-sm-4">
            <?php echo object_select_tag($usuario_por_grupo, 'getGrupousuariId', array ('peer_method'=>'getGrupoUsuarioOrdenado','related_class' => 'GrupoUsuario', 'class'=>'form-control input-sm required','include_custom'=>'Seleccione...')) ?>
            
          </div>          
        </div>
        
        <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success"><?php echo $usuario_por_grupo->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button>
            <?php 
            if($usuario_por_grupo->getPrimaryKey())
            {                
                echo button_to("Eliminar",$base_path.'/administracion.php/usuarios_por_grupos/delete?usuarioporgrupo_id='.$usuario_por_grupo->getPrimaryKey(), array('class' => 'btn btn-red btn-sm','post=true&confirm=Estas Seguro?'));             
            }
            echo '&nbsp;';
            echo button_to("Listar Grupos de Usuarios",$base_path.'/administracion.php/usuarios_por_grupos/list', array('class' => 'btn btn-blue btn-sm')); 
            ?> 
        </div> 
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>