<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormIsad       = "ARCHIVO_".strtoupper($name_original)."_ISAD";
$currentFormDeleteImg    	= "ARCHIVO_".mb_strtolower($name_original)."_ELIMINAR_IMAGEN";
$currentFormDeleteTipo    	= mb_strtolower($name_original)."_ELIMINAR_CONTENIDO";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object','jQuery','UserComponent','InteresadosComponent','RemitentesComponent');
$cantidad_registros=0;
?>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">
      Reabrir Expediente (<strong><?php echo $unidad_documental->getCodigoTitulo() ?></strong>)
    </div>
  </div>

  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">
    <?php 
      echo form_tag('unidad_documental/updateReabrirExpediente', array('name'=>'crear', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
      echo object_input_hidden_tag($unidad_documental, 'getUnidaddocumentalId'); 
      echo input_hidden_tag('localizacion', $localizacion);
    ?>
        <div class="form-group">
			<!-- Fecha Inicial -->
			<label for="fecha_apertura" class="col-sm-1 control-label">Fecha de Apertura <span class="ctrlreq">(*)</span></label>
			<div class="col-sm-3">
				<div class="input-group">
				  <?php
				  echo input_tag('fecha_apertura', '', array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
				  ?>
				  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
				</div>
			</div>
        </div>  
    
        <div class="form-group">
          <!-- Notas -->  
          <label for="lbnotas" class="col-sm-1 control-label">Notas</label>
          <div class="col-sm-10">
              <?php echo object_input_tag($unidad_documental, 'getNotas', array('class'=>'form-control input-sm','placeholder'=>'Digite notas del expediente')); ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Ubicación -->
          <?php 
              $field_localizacion = mb_strtolower($name_original);               
          ?>
          <label for="ubicacion_expediente" class="col-sm-1 control-label">Ubicaci&oacute;n Expediente<span class="ctrlreq">(*)</span></label>
          <div class="col-sm-10">
              <?php
				  echo input_tag('ubicacion_expediente', null, array('class' => 'form-control input-sm required', 'placeholder'=>'Digite ubicaci&oacute;n del expediente'));
              ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Folios -->
          <label for="folios" class="col-sm-1 control-label">Folios <span class="ctrlreq">(*)</span></label>
          <div class="col-sm-3">
            <?php 
            echo object_input_tag($unidad_documental, 'getFolios', array('class'=>'form-control input-sm required', 'data-validate' => 'number','placeholder'=>'Digite numero de folios')); 
            ?>
          </div>
        </div>

      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-8">
          <button type="submit" class="btn btn-success">Reabrir Expediente</button>
		  <?php echo button_to('Cancelar','unidad_documental/show?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&localizacionunidaddocumental_id=2'.$localizacion,array('class' => 'btn btn-red')); ?>
        </div>
      </div>
    <!-- </div> -->

    <div class="clear"></div>
    </form>
  </div>
</div>
