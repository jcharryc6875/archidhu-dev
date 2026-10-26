<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormDeleteImg    	= "ARCHIVO_".mb_strtolower($name_original)."_ELIMINAR_IMAGEN";
$currentFormDeleteTipo    	= mb_strtolower($name_original)."_ELIMINAR_CONTENIDO";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object','jQuery','UserComponent','InteresadosComponent','RemitentesComponent');
$cantidad_registros=0;
?>
<div class="row">
  <div class="col-md-12">
  <?php include_once("_navbar_archivo.php"); ?>
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
        echo form_tag('unidad_documental/update', array('name'=>'crear', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
        echo object_input_hidden_tag($unidad_documental, 'getUnidaddocumentalId'); 
        echo input_hidden_tag('unidadconservadora_id', 1);
        echo input_hidden_tag('id_localizacion', $localizacion);
        echo input_hidden_tag('serie_cambia');
        ?>
        
        <!-- Serie Documental -->
        <div class="form-group">
          <label for="lbDependencia" class="col-sm-1 control-label">Dependencia</label>
          <div class="col-sm-3">
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
                      echo "<option value='".$dependencia->getDependenciaId()."'";
              		  if($dependencia_select == $dependencia->getDependenciaId()){echo 'selected="selected"';} 
            		  echo ">".$dependencia->getNombreCustomFull()."</option>";
            		}
                 ?>     
             </select>
          </div>
                    
          <!-- serie_id -->
          <label for="serie_id" class="col-sm-1 control-label">Serie</label>
          <div class="col-sm-3">
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
          
          <!-- subserie_id -->
          <label for="serie_id" class="col-sm-1 control-label">Subserie<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-3">              
              <div id="cbsubserie">
                  <?php if (!$unidad_documental->getUnidaddocumentalId()){ ?>
                		<select id="subserie_id" name="subserie_id" class="form-control input-sm required">
                	    	<option value="" selected="selected" >Seleccione Serie</option>
                		</select>
                  <?php }else{ ?>
                    <select class="form-control input-sm required" id="subserie_id" name="subserie_id">
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
        
        <div class="form-group">          
          <!-- Soporte Unidad Documental -->
          <label for="lbSoportedocumental" class="col-sm-1 control-label">Soporte Documental<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-3">
            <?php
              echo object_select_tag($unidad_documental, 'getSoporteunidaddocumentalId', array ('related_class' => 'SoporteUnidadDocumental','include_custom'=>'Seleccione...','class'=>'required form-control input-sm'));
            ?>
          </div>
          <!-- Frecuencia Consulta -->
          <label for="lbFrecuenciaConsulta" class="col-sm-1 control-label">Frecuencia Consulta<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-3">
            <?php 
              echo object_select_tag($unidad_documental, 'getFrecuenciaconsultaId', array ('related_class' => 'FrecuenciaConsulta','include_custom'=>'Seleccione...','class'=>'required form-control input-sm'));
            ?>
          </div>
          <!-- Unidad Conservadora -->
          <label for="lbUnidadConservadora" class="col-sm-1 control-label">Unidad Conservaci&oacute;n<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-3">
            <?php 
              echo object_select_tag($unidad_documental, 'getUnidadconservadoraId', array ('related_class' => 'UnidadConservadora','include_custom'=>'Seleccione...','class'=>'required form-control input-sm'));
            ?>
          </div>
        </div>

		<?php
            echo input_hidden_tag('idUserInteresados',implode(",",$pkInteresados));
            echo component_interesados_multiple('paraUserInt',"idUserInteresados",array('toptext' =>false,'placeholder'=>'Digite interesado(s)', 'caption'=>'Interesados', 'formgroup'=>true, 'coldivwidth'=>'col-sm-10', 'class'=>'form-control input-sm composer-group', 'buttontitle'=>'Buscar','values'=>implode(",",$pkInteresados),'values_text'=>$namesInteresados,'option'=>1,'maximumSelectionSize'=>-1));  
        ?>
        <div class="form-group">
          <!-- Titulo -->
          <label for="titulo" class="col-sm-1 control-label">Nombre Expediente<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-9">
            <?php 
            echo object_textarea_tag($unidad_documental, 'getTitulo', array('class'=>'form-control input-sm required','placeholder'=>'Digite titulo o nombre expediente'));
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Codigo de Barras -->  
          <label for="codigo_barras" class="col-sm-1 control-label">N&uacute;mero Expediente</label>
          <div class="col-sm-2">
            <?php 
              echo object_input_tag($unidad_documental, 'getCodigoBarras', array('class'=>'form-control input-sm','placeholder'=>'Digite codigo o numero del expediente'));
              if($msgError){
                echo "ERROR: El n&uacute;mero de expediente con el volumen ya existe en el sistema";  
              }
            ?>
          </div>
          <!-- Fecha Inicial -->
          <label for="fecha_apertura" class="col-sm-1 control-label">Fecha Inicial<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-2">
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

          <!-- Fecha Final -->
          <?php if($localizacion != 1){ ?>
            <label for="fecha_cierre" class="col-sm-1 control-label">Fecha Final<span class="ctrlreq">(*)</span>:</label>
            <div class="col-sm-2">
              <div class="input-group">
                <?php
                $fecha_cierre = '';
                if($unidad_documental->getFechaCierre()){
                  $fecha_cierre = $unidad_documental->getFechaCierre("Y-m-d");
                }
                echo input_tag('fecha_cierre', $fecha_cierre, array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
              </div>
            </div>
          <?php } ?>
          
          <!-- Folios -->
          <label for="folios" class="col-sm-1 control-label">Numero Identificaci&oacute;n</label>
          <div class="col-sm-2">
            <?php 
              echo object_input_tag($unidad_documental, 'getNumeroIdentificacion', array('class'=>'form-control input-sm', 'data-validate' => 'number','palceholder'=>'Digite numero de identificación')); 
            ?>
          </div>

          <?php if($unidad_documental->getUnidaddocumentalId()){ ?>
            <!-- Estado -->
            <label for="titulo" class="col-sm-1 control-label">Estado</label>
            <div class="col-sm-2">
                  <?php
                      echo object_select_tag($unidad_documental, 'getEstadounidaddocumentalId', array (
                          'related_class' => 'EstadoUnidadDocumental','include_custom'=>'Seleccione estado...','class'=>'form-control input-sm'));
                  ?>
            </div>
          <?php }else{
            echo input_hidden_tag('estadounidaddocumental_id', '1');
          } ?>
        </div>

        <div class="form-group">
          <!-- Contenido -->
          <label for="contenido" class="col-sm-1 control-label">Contenido</label>
          <div class="col-sm-7">
            <?php 
            echo object_textarea_tag($unidad_documental, 'getContenido', array('class' => 'form-control','placeholder'=>'Digite el contenido del expediente')); 
            ?>
          </div>
        </div>
		
		    <div class="form-group">
          <!-- Contenido -->
          <label for="contenido" class="col-sm-1 control-label">Notas</label>
          <div class="col-sm-7">
            <?php 
            echo object_textarea_tag($unidad_documental, 'getNotas', array('class' => 'form-control','placeholder'=>'Digite las notas del expediente')); 
            ?>
          </div>
        </div>
		
        <div class="form-group">
          <!-- Ubicacion -->
          <?php
          $field_localizacion = mb_strtolower($name_original);
          ?>
          <label for="ubicacionen<?php print $field_localizacion;?>" class="col-sm-1 control-label">Ubicaci&oacute;n</label>
          <div class="col-sm-2">
            <?php
            echo object_input_tag($unidad_documental, 'getUbicacionen'.$field_localizacion, array('class'=>'form-control input-sm','placeholder'=>'Digite ubicaci&oacute;n del expediente'));
            ?>
          </div>
          <!-- Numero Caja -->
          <label for="lbNumeroCaja" class="col-sm-1 control-label">N&uacute;mero Caja</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getNumeroCaja', array('class'=>'form-control input-sm','placeholder'=>'Digite numero de caja')); 
            ?>
          </div>
          <!-- Numero Carpeta -->
          <label for="lbNumeroCaja" class="col-sm-1 control-label">N&uacute;mero Carpeta</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getNumeroCarpeta', array('class'=>'form-control input-sm','placeholder'=>'Digite numero de la carpeta')); 
            ?>
          </div> 
          <!-- Repositorio Origen -->
          <label for="repositorio origen" class="col-sm-1 control-label">Repositorio Origen</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getRepositorioOrigen', array('class'=>'form-control input-sm','placeholder'=>'Digite repositorio origen')); 
            ?>
          </div>        
        </div>
        
        <div class="form-group">          
          <!-- Bodega -->
          <label for="bodega" class="col-sm-1 control-label">Bodega</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getGeoBodega', array('class'=>'form-control input-sm', 'placeholder'=>'Bodega del expediente')); 
            ?>
          </div>
          <!-- Cuerpo -->
          <label for="cuerpo" class="col-sm-1 control-label">Cuerpo</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getGeoCuerpo', array('class'=>'form-control input-sm', 'placeholder'=>'Cuerpo del expediente')); 
            ?>
          </div>
          <!-- Torre -->
          <label for="torre" class="col-sm-1 control-label">Torre</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getGeoTorre', array('class'=>'form-control input-sm', 'placeholder'=>'Torre del expediente')); 
            ?>
          </div>
          <!-- Piso -->
          <label for="Piso" class="col-sm-1 control-label">Piso</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getGeoPiso', array('class'=>'form-control input-sm', 'placeholder'=>'Piso del expediente')); 
            ?>
          </div>
        </div>
        
		    <div class="form-group">          
          <!-- Folios -->
          <label for="folios" class="col-sm-1 control-label">Folios</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getFolios', array('class'=>'form-control input-sm', 'data-validate' => 'number','placeholder'=>'Folios del expediente')); 
            ?>
          </div>
          <!-- Volumen -->
          <label for="volumen" class="col-sm-1 control-label">Volumen</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getVolumen', array('class'=>'form-control input-sm', 'data-validate' => 'number','placeholder'=>'Valumen del expediente')); 
            ?>
          </div>
        </div>
        
        <div id="structmetadatos" class="form-group" style="display: none;"></div>

        <?php if($nombre_funcion != "Crear"){ ?>
          <div class="form-group">
            <!-- Fecha Creacion -->
            <label for="fecha_creacion" class="col-sm-1 control-label">Fecha Creaci&oacute;n</label>
            <div class="col-sm-4">
              <?php 
              echo object_input_tag($unidad_documental, 'getFechaCreacion', array('class' => 'form-control input-sm', 'readonly'=>true));
              ?>
            </div>
            <!-- Fecha Vencimiento -->
            <label for="fecha_vencimiento" class="col-sm-1 control-label">Fecha Vencimiento</label>
            <div class="col-sm-4">
              <?php 
              echo object_input_tag($unidad_documental, 'getFechavencimiento', array('class' => 'form-control input-sm', 'readonly'=>true));
              ?>
            </div>
          </div>
        <?php } ?>

        <div class="form-group">
          <!-- Inventariado Por -->
          <label for="lbinventariadorId" class="col-sm-1 control-label">Inventariado Por<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-4">
            <?php
            $currentForm = "GESTION_CREAR_OTRO_INVENTARIADOR";
            if(! $sf_user->checkPerm($currentForm, $currentUser) && $localizacion == 1){
              echo input_hidden_tag('udr_inventariador', $udr_inventariador);
              echo input_hidden_tag('id_inventariador', $creador);
              echo input_tag('Inventariado_por', $creador_name, array('readonly' => true, 'class' => 'form-control input-sm required', ));
            }else{
              echo input_hidden_tag('udr_inventariador', $udr_inventariador);
              echo object_select_tag($UserUniDoc, 'getUsuarioId', 
                      array(
                        'peer_method' => 'getAllUserActive', 
                        'related_class' => 'Usuario',
                        'name' => 'id_inventariador',
                        'id' => 'id_inventariador', 
                        'class'=>'select2 form-control input-sm required',
                        'include_custom' => 'Seleccione....',
                      ), $inventariador);
            }
           ?>
          </div>

          <!-- Responsable -->
          <label for="lbresponsableId" class="col-sm-1 control-label">Responsable<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-4">
            <?php
            $currentForm = "GESTION_CREAR_OTRO_RESPONSABLE";
            if(! $sf_user->checkPerm($currentForm, $currentUser) && $localizacion == 1){ 
              echo input_hidden_tag('udr_responsable', $udr_responsable);
              echo input_hidden_tag('id_responsable', $creador);
              echo input_tag('Responsable_por', $creador_name, array('readonly' => true, 'class'=>'form-control input-sm required'));
            }else{
              echo input_hidden_tag('udr_responsable', $udr_responsable);
              echo object_select_tag($UserUniDoc, 'getUsuarioId', array (
                      'peer_method' => 'getAllUserActive',
                      'related_class' => 'Usuario',
                      'name' => 'id_responsable',
                      'id' => 'id_responsable',
                      'class' => 'form-control input-sm required select2',
                      'include_custom' => 'Seleccione....',
                      ), $responsable);
            }
            ?>
          </div>
        </div>
		
		<div class="form-group">
          <!-- Creado Por -->
          <label for="lbcreadoId" class="col-sm-1 control-label">Creado Por<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-4">
            <?php 
            $currentFormCreadoPor = "GESTION_CREADO_POR";
            if(! $sf_user->checkPerm($currentFormCreadoPor, $currentUser) && $localizacion == 1){
                echo input_hidden_tag('udr_creador', $udr_creador);
                echo input_hidden_tag('id_creador', $creador);
                echo input_tag('creado_por', $creador_name, array('readonly' => true, 'class'=>'form-control input-sm required'));
            }else{
                echo input_hidden_tag('udr_creador', $udr_creador);
                echo input_hidden_tag('id_creador', $creador);
                echo object_select_tag($UserUniDoc, 'getUsuarioId', 
                                array(
                                      'peer_method'=>'getAllUserActive','related_class' => 'Usuario',
                                      'name'=>'id_creador',
                                      'id'=>'id_creador',
                                      'class'=>'form-control input-sm required select2',
                                      'include_custom' => 'Seleccione....',
                                      ), $creador);  
            }
            ?>
          </div>
          <!-- Regional -->
          <label for="lbRegional" class="col-sm-1 control-label">Regional<span class="ctrlreq">(*)</span>:</label>
          <div class="col-sm-4">            
          <?php
            echo select_tag('regional_id',objects_for_select($regionales,'getRegionalId','getRegionalAll',$regional_select,array('include_custom'=>'Seleccione...',)) , array ('class'=>'form-control input-sm required','name'=>'regional_id','id'=>'regional_id',));
          ?>            
          </div>
      </div>
		
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Guardar Expediente</button>
            <?php echo button_to('Cerrar','unidad_documental/list?localizacionunidaddocumental_id='.$localizacion,array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
      </div>
    </div>
  </div>
</div>