<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormDeleteImg    	= "ARCHIVO_".mb_strtolower($name_original)."_ELIMINAR_IMAGEN";
$currentFormDeleteTipo    	= mb_strtolower($name_original)."_ELIMINAR_CONTENIDO";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object','jQuery','UserComponent','InteresadosComponent','RemitentesComponent');
$cantidad_registros=0;
?>
<script src="<?php echo $path_theme; ?>/assets/js/jquery.observe_field.js"></script>
<div class="row">
  <div class="col-md-12">
  <?php //include_once("_navbar_archivo.php"); ?>
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php print $nombre_funcion;?> Expediente <?php print $name_localizacion;?>
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('unidad_documental/update', array('name'=>'crear', 'role' => 'form', 'class' => 'form-groups-bordered validate')); 
        echo object_input_hidden_tag($unidad_documental, 'getUnidaddocumentalId'); 
        echo input_hidden_tag('soporteunidaddocumental_id', 1);
        echo input_hidden_tag('frecuenciaconsulta_id', 3);
        echo input_hidden_tag('unidadconservadora_id', 1);
        echo input_hidden_tag('id_localizacion', $localizacion);
        echo input_hidden_tag('estadounidaddocumental_id', '1');
        echo input_hidden_tag('serie_cambia');
		    echo input_hidden_tag('backselect',1);
        echo input_hidden_tag('comrecibida_id',$comrecibida_id);
        ?>
      
        <div class="row">
          <div class="col-sm-10">
            <div class="form-group">
              <!-- Ciudad -->
              <label class="control-label">Dependencia:</label>
              <input type="hidden" name="form_tag" id="form_tag" value="<?php echo $form_tag?>"/>
              <input type="hidden" name="permiso_id" id="permiso_id" value="<?php echo $permiso?>"/>
              <select name="dependencia_id" id="dependencia_id" class="select2" onchange="<?php
                echo jq_remote_function(array(
                      'update' => 'cbseries',
                      'url' => 'unidad_documental/cargarCombos',
                      'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=dependencias&form_tag=$form_tag'+'&id_permiso=".$permiso."'",
                      'loading'  => "jQuery('#serie_id').find('option').remove().end().val('');jQuery('#subserie_id').empty().append('<option selected=selected value>Seleccione Serie</option>');",
                      'script' => true,
                    )) ?>" class="form-control input-sm">
                <option value="">Seleccione Dependencia...</option>
                <?php
                  $dependencia_compuesto = "";
                  foreach($dependencias as $dependencia)
                  {
                    $dependencia_nombre = trim($dependencia->getNombre());
                    $dependencia_compuesto = (trim($dependencia->getCodigo()) ? trim($dependencia->getCodigo())." - ".$dependencia_nombre." - " : $dependencia_nombre." - ").$dependencia->getTipoTabla()." - ".$dependencia->getEntidad();
                    echo "<option value='".$dependencia->getDependenciaId()."'";
                    if($dependencia_select == $dependencia->getDependenciaId()){echo 'selected="selected"';} 
                    echo ">".$dependencia_compuesto."</option>";
                  }
                ?>     
              </select>
            </div>
          </div>
        </div>      
      
        <div class="row">
          <div class="col-sm-5">
            <div class="form-group">
              <!-- Serie -->
              <label class="control-label">Serie:</label>
              <div id="cbseries">
                  <?php if (!$unidad_documental->getUnidaddocumentalId()){ ?>
                    <select id="serie_id" name="serie_id" disabled="disabled" class="form-control input-sm">    
                        <option value="" selected="selected">Seleccione...</option>
                    </select>
                  <?php }else{ ?>
                  <select id="serie_id" name="serie_id" class="form-control input-sm" onchange="<?php
                	echo jq_remote_function(array(
                	   'update' => 'cbsubserie',
                	   'url' => 'unidad_documental/cargarCombos',
                	   'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=series&form_tag=$form_tag'",
                	   'loading'  => "jQuery('subserie_id').disabled='disabled';jQuery('subserie_id').value='';",
                	   'complete' => '',
                     'script' => true,
                	 )) ?>">
                	<option value="">Seleccione...</option>
                	<?php foreach($series_list as $rs){ ?>
                	    <option value="<?php echo $rs->getSerieId()?>"<?php if($rs->getSerieId() == $serie_select){ ?> selected="selected" <?php } ?>>
                	    <?php echo $rs->getDescripcion(); ?></option>
                	<?php } ?>
                  </select>
                  <?php }?>
              </div>
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              <!-- Subserie -->
              <label class="control-label">Subserie<span class="ctrlreq">(*)</span>:</label>
              <div id="cbsubserie">
                  <?php if (!$unidad_documental->getUnidaddocumentalId()){ ?>
                		<select id="subserie_id" name="subserie_id" class="form-control input-sm required">
                	    	<option value="" selected="selected" >Seleccione Serie</option>
                		</select>
                  <?php }else{ ?>
                    <select class="form-control input-sm required" id="subserie_id" name="subserie_id"">
                    <option value="" >Seleccione...</option>
                    <?php foreach($subseries_list as $rs){ ?>
                        <option value="<?php echo $rs->getSubserieId()?>"
                        <?php if($rs->getSubserieId() == $subserie_select){ ?>  selected="selected" <?php } ?> >
                        <?php echo $rs->getDescripcion(); ?></option>
                    <?php } ?>
                    </select>
                  <?php } ?>
              </div>
            </div>
          </div>
        </div>
                
        <?php
            echo input_hidden_tag('idUserInteresados',implode(",",$pkInteresados));
            echo component_interesados_multiple('paraUserInt',"idUserInteresados",array('toptext' =>true,'placeholder'=>'Digite interesado(s)', 'caption'=>'Interesados', 'formgroup'=>false, 'coldivwidth'=>'col-sm-10', 'class'=>'form-control input-sm composer-group', 'buttontitle'=>'Buscar','values'=>implode(",",$pkInteresados),'values_text'=>$namesInteresados,'option'=>1,'maximumSelectionSize'=>-1));
        ?>

        <div class="row">
          <div class="col-sm-10">
            <div class="form-group">
              <!-- getTitulo -->
              <label class="control-label">Nombre Expediente<span class="ctrlreq">(*)</span>:</label>
              <?php 
                echo object_textarea_tag($unidad_documental, 'getTitulo', array('class'=>'form-control input-sm required','placeholder'=>'Digite el nombre del expediente'));
              ?>
            </div>
          </div>
        </div>
      
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <!-- Fecha Inicial -->
              <label class="control-label">Fecha Inicial<span class="ctrlreq">(*)</span>:</label>
              <div class="input-group">
                <?php
                  $fecha_inicial = date("Y-m-d");
                  if($unidad_documental->getFechaApertura()){
                    $fecha_inicial = $unidad_documental->getFechaApertura("Y-m-d");
                  }
                  echo input_tag('fecha_apertura', $fecha_inicial, array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
              </div>
            </div>
          </div>
          <?php if($localizacion != 1){ ?>
            <div class="col-sm-3">
              <div class="form-group">
                <!-- Fecha Final -->
                <label class="control-label">Fecha Final<span class="ctrlreq">(*)</span>:</label>
                <div class="input-group">
                  <?php
                  $fecha_cierre = '';
                  if($unidad_documental->getFechaCierre()){
                    $fecha_cierre = $unidad_documental->getFechaApertura("Y-m-d");
                  }
                  echo input_tag('fecha_cierre', $fecha_cierre, array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>      
        
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <!-- Contenido -->
              <label class="control-label">N&uacute;mero Carpeta:</label>
              <?php 
                echo object_input_tag($unidad_documental, 'getNumeroCarpeta', array('class' => 'form-control input-sm','placeholder'=>'Digite el n&uacute;mero de la carpeta')); 
              ?>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-10">
            <div class="form-group">
              <!-- Contenido -->
              <label class="control-label">Contenido:</label>
              <?php 
                echo object_textarea_tag($unidad_documental, 'getContenido', array('size'=>'50X50','class' => 'form-control','placeholder'=>'Digite el contenido del expediente')); 
              ?>
            </div>
          </div>
        </div>      
      
        <div id="structmetadatos" class="form-group" style="display: none;"></div>
      
        <div class="row">
          <div class="form-group">
            <!-- Botonera -->
            <div class="col-sm-offset-4 col-sm-8">
              <button type="submit" class="btn btn-success">Guardar Expediente</button>
              <?php echo jq_link_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class' => 'btn btn-red ')); ?>
            </div>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>