<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormIsad       = "ARCHIVO_".strtoupper($name_original)."_ISAD";
$currentFormTipoDescarte = "ARCHIVO_CENTRAL_TIPO_DESCARTE";
$currentFormSinUbicacion = "ARCHIVO_".strtoupper($name_localizacion)."_SIN_UBICACION";
$currentFormEstado = "ARCHIVO_".strtoupper($name_localizacion)."_ESTADO";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('Object','jQuery','UserComponent');
$cantidad_registros=0;
?>
<script src="<?php echo $path_theme; ?>/assets/js/typeahead.min.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" href="<?php echo $path_theme; ?>/assets/js/icheck/skins/square/_all.css"/>

<div class="row">
  <div class="col-md-12">
  <?php include_once("_navbar_archivo.php"); ?>
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Consultar Archivo <?php echo $name_localizacion;?>
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">

      <?php 
          echo form_tag('unidad_documental/list', array('name'=>'form1', 'method' => 'post', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
          echo input_hidden_tag('localizacionunidaddocumental_id', $localizacion);
          echo input_hidden_tag('form_origen', 'consultar');
          echo input_hidden_tag('form_tag', $form_tag);
          echo input_hidden_tag('permiso_id', $permiso);
      ?>

      
      <!-- Serie Documental -->
      <div class="form-group">
          <label for="serie_documental" class="col-sm-1 control-label">Dependencia</label>
          <div class="col-sm-3">
            <select name="dependencia_id" id="dependencia_id" class="select2" onchange="<?php
                  echo jq_remote_function(array(
                       'update' => 'cbseries',
                       'url' => 'unidad_documental/cargarCombos',
                       'with' => "'id_seleccionado=' + this.options[this.selectedIndex].value + '&combo_seleccionado=dependencias&form_tag=$form_tag'+'&id_permiso=".$permiso."'",
                       'loading'  => "jQuery('serie_id').disabled='disabled';jQuery('subserie_id').disabled='disabled';jQuery('serie_id').value='';jQuery('subserie_id').value=''",
                       //'complete' => visual_effect('fade', 'indicator_fondo').visual_effect('fade', 'indicator'),
                     )) ?>" class="form-control input-sm">
                 <option value="">Seleccione Dependencia...</option>
                 <?php
                    foreach($dependencias as $dependencia)
                    {
                      echo "<option value='".$dependencia->getDependenciaId()."'";            
                      echo ">".$dependencia->getNombreCustomFull()."</option>";
                    }
                 ?>     
             </select>
          </div>
                    
          <!-- serie_id -->
          <label for="serie_id" class="col-sm-1 control-label">Serie</label>
          <div class="col-sm-3">
              <div id="cbseries">
                  <select id="serie_id" name="serie_id" disabled="disabled" class="form-control input-sm">    
                    	<option value="" selected="selected">Seleccione Dependencia</option>
                  </select>
              </div>
          </div>
          
          <!-- serie_id -->
          <label for="serie_id" class="col-sm-1 control-label">Subserie</label>
          <div class="col-sm-3">
              <div id="cbsubserie">
                  <select id="subserie_id" name="subserie_id" disabled="true" class="form-control input-sm">
                      <option value="" selected="selected">Seleccione Serie</option>
                  </select>
              </div>
          </div>
      </div>

      <div class="form-group">
          <!-- Codigo de Barras -->  
          <label for="lbnotas" class="col-sm-1 control-label">N&uacute;mero Expediente:</label>
          <div class="col-sm-3">
            <?php echo object_input_tag($unidad_documental, 'getCodigoBarras', array('class'=>'form-control input-sm','placeholder'=>'Digite numero o codigo del expediente')); ?>
          </div>
          <!-- Titulo -->
          <label for="titulo" class="col-sm-1 control-label">Nombre Expediente:</label>
          <div class="col-sm-3">
            <?php 
            echo object_input_tag($unidad_documental, 'getTitulo', array('class'=>'form-control input-sm','placeholder'=>'Digite npmbre o titulo del expediente'));
            ?>
          </div>
           <!-- Id Expediente -->  
           <label for="lbUnidaddocumentalId" class="col-sm-1 control-label">Id Expediente:</label>
          <div class="col-sm-3">
            <?php 
            echo object_input_tag($unidad_documental, 'getUnidaddocumentalId', array('class'=>'form-control input-sm','data-validate'=>'number','placeholder'=>'Digite id del expediente, solo numeros'));
            ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Contenido -->
          <label for="contenido" class="col-sm-1 control-label">Contenido:</label>
          <div class="col-sm-3">
            <?php echo object_input_tag($unidad_documental, 'getContenido', array('class' => 'form-control input-sm','placeholder'=>'Digite contenido de expediente')); ?>
          </div>
          <!-- Imagenes Contiene -->
          <label for="imagen_contiene" class="col-sm-1 control-label">Descripci&oacute;n:</label>
          <div class="col-sm-3">
            <?php echo input_tag('imagen_contiene', '', array('class' => 'form-control input-sm','placeholder'=>'Digite descripci&oacute;n del contenido documental')); ?>
          </div>
          <?php if($sf_user->checkPerm($currentFormEstado, $currentUser)){ ?>
            <!-- Estado -->
            <label for="titulo" class="col-sm-1 control-label">Estado</label>
            <div class="col-sm-3">
              <?php
                echo object_select_tag($unidad_documental, 'getEstadounidaddocumentalId', array ('related_class' => 'EstadoUnidadDocumental','include_custom'=>'Seleccione estado...','class'=>'form-control input-sm'));
              ?>
            </div>
                  <?php 
          }else{
            echo input_hidden_tag('estadounidaddocumental_id', '1');
          } 
          ?>
        </div>
        
		    <div class="form-group">
          <!-- Ubicación -->
          <?php $field_localizacion = mb_strtolower($name_original); ?>
          <label for="ubicacionen<?php print $field_localizacion;?>" class="col-sm-1 control-label">Ubicaci&oacute;n</label>
          <div class="col-sm-3">
            <?php
                echo object_input_tag($unidad_documental, 'getUbicacionen'.$field_localizacion, array('class'=>'form-control input-sm','placeholder'=>'Digite ubicaci&oacute;n del expediente'));
            ?>
          </div>
		      <!-- Numero Caja -->          
          <label for="numero_caja" class="col-sm-1 control-label">N&uacute;mero Caja</label>
          <div class="col-sm-3">
            <?php
                echo object_input_tag($unidad_documental, 'getNumeroCaja', array('class'=>'form-control input-sm','placeholder'=>'Digite numero de caja'));
            ?>
          </div>
          <!-- Notas -->  
          <label for="lbnotas" class="col-sm-1 control-label">Notas</label>
          <div class="col-sm-3">
            <?php echo object_input_tag($unidad_documental, 'getNotas', array('class'=>'form-control input-sm','placeholder'=>'Digite notas del expediente')); ?>
          </div>
        </div>
        
        <div class="form-group">
          <!-- Numero Carpeta -->
          <label for="Numero Carpeta" class="col-sm-1 control-label">N&uacute;mero Carpeta</label>
          <div class="col-sm-3">
            <?php
                echo object_input_tag($unidad_documental, 'getNumeroCarpeta', array('class'=>'form-control input-sm','placeholder'=>'Digite Numero de Carpeta'));
            ?>
          </div>

          <!-- Repositorio Origen -->
          <label for="Repositorio Origen" class="col-sm-1 control-label">Repositorio Origen</label>
          <div class="col-sm-3">
            <?php
                echo object_input_tag($unidad_documental, 'getRepositorioOrigen', array('class'=>'form-control input-sm','placeholder'=>'Digite Repositorio de Origen'));
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
          <label for="folios" class="col-sm-1 control-label">Numero Identificaci&oacute;n</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getNumeroIdentificacion', array('class'=>'form-control input-sm', 'data-validate' => 'number','placeholder'=>'Digite numero de identificaci&oacute;n')); 
            ?>
          </div>
		  
		      <!-- Folios -->
          <label for="folios" class="col-sm-1 control-label">Folios</label>
          <div class="col-sm-2">
            <?php 
            echo object_input_tag($unidad_documental, 'getFolios', array('class'=>'form-control input-sm', 'data-validate' => 'number','placeholder'=>'Digite numero de folios')); 
            ?>
          </div>

          <!-- Volumen -->
          <label for="volumen" class="col-sm-1 control-label">Volumen</label>
          <div class="col-sm-2">
            <?php 
                echo object_input_tag($unidad_documental, 'getVolumen', array('class'=>'form-control input-sm','data-validate' => 'number', 'placeholder'=>'Digite volumen expediente')); 
            ?>
          </div>

          <!-- Numero Remisión -->
          <label for="numero_remision" class="col-sm-1 control-label">Numero Remisi&oacute;n</label>
          <div class="col-sm-2">
            <?php
                echo input_tag('numero_remision', '', array('class'=>'form-control input-sm','data-validate' => 'number','placeholder'=>'Digite id del documento'));
            ?>
          </div>

        </div>

        <div class="form-group">
          <!-- Fecha Inicial -->
          <label for="fecha_apertura" class="col-sm-1 control-label">Fecha Inicial</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
              echo input_tag('fecha_apertura', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_apertura_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_apertura_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <!-- Fecha Final -->
          <label for="fecha_cierre" class="col-sm-1 control-label">Fecha Final</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_cierre', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_cierre_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_cierre_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <!-- Fecha Vencimiento -->
          <label for="fecha_vencimiento" class="col-sm-1 control-label">Fecha Vencimiento</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_vencimiento', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_vencimiento_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_vencimiento_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <!-- Fecha Creacion -->
          <label for="fecha_creacion" class="col-sm-1 control-label">Fecha Creaci&oacute;n</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_creacion', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="fecha_creacion_final" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-2">
            <div class="input-group">
              <?php
                echo input_tag('fecha_creacion_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>
		
		<?php if($entidades_list){ ?>
			<div class="form-group">
				<!-- Entidad -->
				<label for="lbentidad" class="col-sm-1 control-label">Entidad</label>
				<div class="col-sm-3">
					<?php
						echo object_select_tag($entidades_list, 'getEntidadId', array ('related_class' => 'Entidad','include_custom'=>'Seleccione...','class'=>'form-control input-sm'));
					?>
				</div>
				<!-- Regional -->
				<label for="lbRegional" class="col-sm-1 control-label">Regional</label>
				<div class="col-sm-3">
					<?php
						echo object_select_tag($regionales, 'getRegionalId', array ('peer_method'=>'getOrdenRegional','related_class' => 'Regional',
							'include_custom'=>'Seleccione...','name'=>'regional_id', 'id'=>'regional_id','class'=>'form-control input-sm'));
					?>
				</div>
			</div>
        <?php } ?>
		
		<div class="form-group">
            <?php if($sf_user->checkPerm($currentFormSinUbicacion, $currentUser)){ ?>
                <!--  Expedientes sin Ubicaci�n -->
                <label for="lbExpedientesSinUbicaci�n" class="col-xs-2 control-label">Expedientes sin Ubicaci&oacute;n:</label>
                <div class="col-xs-1">
                    <div class="make-switch" data-on="success" data-off="info" data-on-label="SI" data-off-label="NO">
                        <?php echo checkbox_tag('sin_ubicacion', 1 , false); ?>
                    </div>
                </div>
            <?php } ?>
            <!--  Expedientes Vencidos -->
            <label for="lbExpedientesSinUbicaci�n" class="col-xs-2 control-label">Expedientes Vencidos:</label>
            <div class="col-xs-1">
                <div class="make-switch" data-on="success" data-off="info" data-on-label="SI" data-off-label="NO">
                    <?php echo checkbox_tag('vencidos', 1 , false); ?>
                </div>
            </div>
            <!--  Expedientes Eliminados -->
            <label for="lbExpedientesSinUbicaci�n" class="col-xs-2 control-label">Expedientes Eliminados:</label>
            <div class="col-xs-1">
                <div class="make-switch" data-on="success" data-off="info" data-on-label="SI" data-off-label="NO">
                    <?php echo checkbox_tag('exp_eliminacion', 1 , false); ?>
                </div>
            </div>
        </div>
		
        <div class="form-group">
          <!-- Esta Marcado -->
          <label for="marcado" class="col-sm-1 control-label">Esta Marcado</label>
          <div class="col-sm-2">
            <?php 
            $marcado = array('1' => 'Si', '0' => 'No');
            echo select_tag('marcado', options_for_select($marcado, '', array('include_custom' => 'Seleccione...')), array('class'=>'form-control input-sm'));
            ?>
          </div>                             
          
          <!-- Transferido -->
          <label for="estadotransferencia" class="col-sm-1 control-label">Transferido</label>
          <div class="col-sm-2">
            <?php 
            $marcado = array('1' => 'Si', '0' => 'No');
            echo select_tag('estadotransferencia', options_for_select($marcado, '', array('include_custom' => 'Seleccione...')), array('class'=>'form-control input-sm'));
            ?>
          </div>  
                  
          <!-- Con Documentos -->
          <label for="lbcondocumentos" class="col-sm-1 control-label">Con Documentos</label>
          <div class="col-sm-3">
            <?php 
            $documentos = array('1'=>'Si','0'=>'No');
            echo select_tag('con_documentos', options_for_select($documentos, '', array('include_custom'=>'Seleccione...',)), array('class'=>'form-control input-sm')); 
            ?>
          </div>
        </div>
        
        <div class="form-group">            
          <!-- Ordenar Por -->
          <label for="orden" class="col-sm-1 control-label">Ordenar Por</label>
          <div class="col-sm-2">
            <?php 
            $ordenList = array('UNIDADDOCUMENTAL_ID' => 'Fecha de creacion', 'FECHA_APERTURA' => 'Fecha de Apertura', 'TITULO' => 'Titulo', 'CODIGO_BARRAS' => 'Codigo de Barras', 'CONTENIDO' => 'Contenido');
            echo select_tag('orden', options_for_select($ordenList, '', array('include_custom'=>'Seleccione',)), array('class'=>'form-control input-sm')); 
            ?>
          </div>
          <!-- Tipo Orden -->
          <label for="lbtipoorden" class="col-sm-1 control-label">Tipo Orden</label>
          <div class="col-sm-2">
            <?php 
                $ordenTipo = array('2'=>'Descendente', '1'=>'Ascendente');
                echo select_tag('tipo_orden', options_for_select($ordenTipo, ''), array('class'=>'form-control input-sm'));
            ?>
          </div>
          
          <!-- Observaciones eliminacion -->  
          <label for="lbnotas" class="col-sm-1 control-label">Observaciones Eliminaci&oacute;n</label>
          <div class="col-sm-3">
            <?php echo object_input_tag($unidad_documental, 'getObsEliminacion', array('class'=>'form-control input-sm')); ?>
          </div>
        </div>
        
                
		<?php
            echo input_hidden_tag('id_creador');
            echo component_user_multiple('name_creador',"id_creador","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Creador', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
            echo input_hidden_tag('id_inventariador');
            echo component_user_multiple('name_inventariador',"id_inventariador","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Inventariador', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <?php
            echo input_hidden_tag('id_responsable');
            echo input_hidden_tag('cargousuarioId');
            echo component_user_multiple('name_responsable',"id_responsable","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching','caption'=>'Responsable', 'values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));  
        ?>
        
        <div class="panel-group joined" id="accordion01">
            <?php if($sf_user->checkPerm($currentFormTipoDescarte, $currentUser)){ ?>
                <div class="panel panel-default">
        			<div class="panel-heading">
        				<h4 class="panel-title">
        					<a data-toggle="collapse" data-parent="#accordion01" href="#collapseThree">
        						Por Disposici&oacute;n Final
        					</a>
        				</h4>
        			</div>
        			<div id="collapseThree" class="panel-collapse collapse">
        				<div class="panel-body" id="field_pordescarte">
                            <script type="text/javascript">
            					<?php
                                  echo jq_remote_function(array(
                                                'url'      => 'unidad_documental/fieldsDescarte',
                                                'update'   => 'field_pordescarte',
                                              ));
                                ?> 
                            </script>
        				</div>
        			</div>
        		</div>
            <?php } ?>
        </div>
               
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Expedientes</button>
            <?php echo button_to('Deshacer','unidad_documental/consultar?localizacion='.$localizacion,array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
        </div>
      </div>
    </div>
</div>