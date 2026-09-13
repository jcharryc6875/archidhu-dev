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
          <?php echo $ws_usuarios->getPrimaryKey() ? "Editar" : "Crear" ?> Usuario Webservice
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('webservice/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($ws_usuarios, 'getWsusuariosId');
        ?>
       
        <div class="form-group">          
          <!-- User name -->
          <label for="lbUsername" class="col-sm-1 control-label">User name</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getUsuario', array ('size' => 50, 'class'=>'form-control input-sm required')); 
            ?>
          </div>          
        </div>
      
        <div class="form-group">
          <!-- Password -->
          <label for="lbPassword" class="col-sm-1 control-label">Password</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getPassword', array ('size' => 70,'type'=>'password','class'=>'form-control input-sm required')); 
            ?>
          </div>
        </div>
        
        <div class="form-group">          
          <!-- Nombre -->
          <label for="lbnombre" class="col-sm-1 control-label">Nombre</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getNombre', array ('size' => 70,'class' => 'form-control input-sm required',)); 
            ?>
          </div>
        </div>
        
        <div class="form-group">          
          <!-- Direccion -->
          <label for="lbDireccion" class="col-sm-1 control-label">Direccion</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getDireccion', array ('size' => 70,'class' => 'form-control input-sm',)); 
            ?>
          </div>
        </div>
        
        <div class="form-group">          
          <!-- Telefono -->
          <label for="lbTelefono" class="col-sm-1 control-label">Telefono</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getTelefono', array ('size' => 70,'class' => 'form-control input-sm',)); 
            ?>
          </div>
        </div>
        
        <div class="form-group">          
          <!-- Email -->
          <label for="lbEmail" class="col-sm-1 control-label">Email</label>
          <div class="col-sm-4">            
            <?php 
                echo object_input_tag($ws_usuarios, 'getEmail', array ('size' => 70,'class' => 'form-control input-sm',)); 
            ?>
          </div>
        </div>
        
        <div class="form-group">
            <!-- Botonera -->
            <div class="col-sm-offset-4 col-sm-5">
                <button type="submit" class="btn btn-success"><?php echo $ws_usuarios->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button>
                <?php
                    echo button_to("Regresar listado",$base_path.'/administracion.php/webservice/list', array('class' => 'btn btn-blue btn-sm'));
                ?> 
            </div> 
        </div>
        <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>