<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Consultar Subseries por Usuario
    </div>    
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">     
    <?php 
        echo form_tag('subserie_por_usuario/list',array('name'=>'crear','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
    ?>

    <div class="form-group">
      <!-- Nombre: -->
      <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Subserie:</label>
      <div class="col-sm-4">
        <div class="input-group">
            <?php 
                echo input_hidden_tag('localizacionunidaddocumental_id', $localizacion);
    			echo input_hidden_tag('subserie_id', '');
      			echo input_tag('serie_documental', '', array('class'=>'form-control input-sm','readonly'=>'true'));
            ?>  
            <div class="input-group-btn">
              <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/unidad_documental/subserie?permiso=1', '600', '400'); return false;">Buscar</button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('serie_documental');jQuery.LimpiarCampoFormulario('subserie_id');"><i class="entypo-cancel-circled"></i></button>
            </div>
         </div>
      </div>
    </div>
    
    <div class="form-group">
      <!-- Modulo: -->
      <label for="numero_solicitud" class="col-sm-1 control-label">Usuario:</label>
      <div class="col-sm-4">
        <?php echo object_select_tag($SubseriePorUsuario, 'getUsuarioId', array ('peer_method'=>'getAllUser','include_custom'=>'Seleccione...', 'class'=>'form-control input-sm select2',)) ?>
        
      </div>
    </div>
    
    <div class="form-group">      
      <!-- Ordenar Por: -->
      <label for="lnombre" class="col-sm-1 control-label">Ordenar Por:</label>
      <div class="col-sm-4">
        <?php $ordenList = array('SUBSERIE_ID'=>'Subserie','USUARIO_ID'=>'Usuario') ?>
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