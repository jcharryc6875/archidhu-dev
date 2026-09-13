<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery','UserComponent');

?>
<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Autorizacion Correspodencia
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('autorizacion_firma/list',array('name'=>'consultar','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
    <div class="form-group">      
      <!-- Estado Autorizacion -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Estado Autorizacion:</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($autorizacion_firma , 'getEstadofirmaautoId', array ('related_class' => 'EstadoFirmaAuto','class'=>'form-control input-sm','include_custom'=>'Seleccione...',)); 
        ?>
      </div>
    </div>
		
    <div class="form-group">      
      <!-- Usuario Autoriza -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Usuario Autoriza</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($autorizacion_firma , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario','class'=>'form-control input-sm','include_custom'=>'Seleccione...','name'=>'usuario_autoriza_id','id'=>'usuario_autoriza_id')); 
        ?>
      </div>
    </div>
    
    <div class="form-group">      
      <!-- Usuario Autoriza -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Usuario Autorizado</label>
      <div class="col-sm-4">
        <?php 
            echo object_select_tag($autorizacion_firma , 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario','class'=>'form-control input-sm','include_custom'=>'Seleccione...','name'=>'usuario_autorizado_id','id'=>'usuario_autorizado_id',)); 
        ?>
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