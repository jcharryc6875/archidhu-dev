<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>/assets/js/typeahead.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/fileinput.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/dropzone/dropzone.js"></script>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
				<?php echo $wf_imagen_instancia->getPrimaryKey() ? "Editar" : "Crear"; ?> Documento
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<?php
    			echo form_tag('wf_imagen_instancia/update', array('name'=>'form1','multipart=true','class' => 'form-horizontal form-groups-bordered validate')); 
    			echo object_input_hidden_tag($wf_imagen_instancia, 'getWfImagenInstanciaId');
                echo object_input_hidden_tag($wf_imagen_instancia, 'getWfinstanciaId',array(),$wfinstancia_id);
			?>
            <div class="form-group">
				<!-- Descripcion -->
				<label for="descripcion" class="col-sm-1 control-label">Descripci&oacute;n</label>
				<div class="col-sm-8">
                    <?php                         
					     echo object_textarea_tag($wf_imagen_instancia, 'getDescripcion', array('size' => '50x2','class'=>'form-control input-sm required'));
                    ?>
				</div>
			</div>
			<div class="form-group">  
				<!-- Folios -->  
				<label for="folios" class="col-sm-1 control-label">Folios</label>
				<div class="col-sm-2">
					<?php echo object_input_tag($wf_imagen_instancia, 'getFolios', array('class' => 'form-control input-sm','data-mask'=>'decimal')); ?>
				</div>                
                <!-- Estado -->  
				<label for="codigo_barras" class="col-sm-1 control-label">Estado</label>
				<div class="col-sm-3">
					<?php 
					echo object_select_tag($wf_imagen_instancia, 'getWfEstadoImagen', array('class' => 'form-control input-sm required', 'related_class' => 'WfEstadoImagen')); 
					?>
				</div>
                <!-- Fecha -->
				<label for="fecha_documento" class="col-sm-1 control-label">Fecha Documento</label>
				<div class="col-sm-3">
					<div class="input-group">
		              <?php
                      $option = 'form-control input-sm datepicker';
		              $fecha_documento = '';
		              if($wf_imagen_instancia->getFechaDocumento()){
                        $fecha_documento = $wf_imagen_instancia->getFechaDocumento("Y-m-d");
		              }
		              echo input_tag('fecha_documento', $fecha_documento, array('class' => $option, 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
		              ?>                      
		              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
		            </div>				
				</div>
			</div>			
			<?php if($wf_imagen_instancia->getPrimaryKey() && trim($wf_imagen_instancia->getRuta())){ ?>
                <div class="form-group">
    				<!-- Imagenes Actuales -->
    				<label for="nombre" class="col-sm-1 control-label">Imagenes Actuales</label>
    				<div class="col-sm-6">
    					<div class="input-group">
    						<?php
                                $active_files = array();
                                $json_data = array();
                                if(trim($wf_imagen_instancia->getRuta())){
                                    $arr = preg_split("/[,]+/",trim($wf_imagen_instancia->getRuta()),-1, PREG_SPLIT_NO_EMPTY);
                                    foreach ($arr as $result){
                                        if(trim(basename($result))){
                                            $active_files[] = trim(basename($result));
                                            $json_data[] = array('name' => trim(basename($result)), 'size' => '10');                                        
                            ?>
                                            <a href="<?php echo trim($result); ?>" target='_blank'>
                                                <?php echo image_tag('simad/ico_adjunto.png',array('data-original-title'=>basename(trim($result)), 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','border'=>"0",'width'=>"30",'align'=>"middle"))?>
                                            </a>
                            <?php
                                        }
                                    }
                                }
                                echo input_hidden_tag('ruta',implode(",",$active_files));
                                $json_files = json_encode($json_data);
                            ?>                          
    	             	</div>
    				</div>
    			</div>
            <?php }else{
                    echo input_hidden_tag('ruta','');
                  }
            ?>
			
			<div class="form-group" id="accordion">
              <!-- Upload images -->
              <label for="nombre" class="col-sm-1 control-label">Nuevo adjunto</label>
              <div class="panel col-sm-10 panel-primary">             
                <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" style="cursor: pointer;">
        			<div class="panel-title">                                       
                          <strong>Documentos anexos( haga clic para abrir)(Tama�o Maximo <?php echo (ini_get('upload_max_filesize'));?>)</strong>
        			</div>
        		</div>
                <div id="collapseOne" class="panel-collapse collapse" data-collapsed="1">
                    <div class="panel-body">
                         <div class="dropzone dz-clickable dz-default dz-file-preview" id="myDrop" multiple="multiple" >
                            <div class="dz-message">
                                <h2><i class="glyphicon glyphicon-cloud-upload"></i><br/>Arrastre archivos aqui!</h2>o haga clic para seleccionar
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div> 
			
			<div class="form-group">
	          <!-- Botonera -->
	          <div class="col-sm-offset-2 col-sm-10">
	            <button type="submit" data-loading-text="Cargando informacion..." class="btn btn-success"><?php echo $wf_imagen_instancia->getPrimaryKey() ? "Guardar Cambios" : "Guardar Documento"; ?></button>
	            <?php
                     if(trim($sf_params->get('wfactividadtransicion_id')) && trim($sf_params->get('wfinstancia_id'))){ ?>
                        <a class="btn btn-red" data-loading-text="Cargando informacion..." href="<?php echo $base_path; ?>/administracion.php/wf_instancia_bitacora/create?wfactividadtransicion_id=<?php echo trim($sf_params->get('wfactividadtransicion_id')); ?>&instancia_id=<?php echo $wfinstancia_id?>">
                            Regresar
                        </a>
                <?php }else{
                        echo button_to("Regresar02",'wf_imagen_instancia/index?wfinstancia_id='.$wfinstancia_id.'&wf_imagen_instancia_id='.$wf_imagen_instancia->getWfImagenInstanciaId(), array('class' => 'btn btn-blue btn-sm'));
                     }
	        	?>                
	          </div>
	        </div>
	        <div class="clear"></div>
			</form>
		</div>
	   </div>
	</div>
</div>
<script type="text/javascript">
Dropzone.autoDiscover = false;
jQuery(document).ready(function() 
{
    Dropzone.options.myAwesomeDropzone = false;
    var myDropzone = new Dropzone("div#myDrop", { 
        url: "<?php echo url_for('wf_imagen_instancia/dzFileUpload') ?>",
        // The configuration we've talked about above
        autoProcessQueue: true,       
        //uploadMultiple: true,
        addRemoveLinks: true,
        //thumbnailWidth: 50,
        //thumbnailHeight: 50,
        parallelUploads: 100,
        maxFiles: 100,
        dictResponseError: "Ha ocurrido un error en el server",
        acceptedFiles: 'image/*,.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF,.TIF,.TIFF,.tif,.tiff,.rar,application/pdf,.psd,.xls,.doc,.ppt,.msg,.xlsx,.docx,.pptx,.zip,.7z',
        init: function () {
            this.on("success", function (file, response) {
                 jQuery(file.previewElement).find('[data-dz-name]').html(response.name);
                 var active_value = jQuery('#ruta').val().trim() != "" ? jQuery('#ruta').val() + "," + response.name : response.name;
                 jQuery('#ruta').val(active_value);
            });
            
            this.on("removedfile", function(file) {
                if(file != null)
                {                    
                    var active_new = [];
                    var active_files = jQuery('#ruta').val().split(",");
                    var index = 0;
                    for(i=0; i < active_files.length; i++){
                        if(file.name != active_files[i]){
                            active_new.push(active_files[i]);
                        }
                        index++;
                    }
                    jQuery('#ruta').val(active_new.join(","));
                }
            });
        }
    });    
    //Add existing files into dropzone    
    /*var existingFiles = <?php //echo $json_files; ?>;    
    for (i = 0; i < existingFiles.length; i++) {
        myDropzone.emit("addedfile", existingFiles[i]);
        //myDropzone.emit("thumbnail", existingFiles[i], "/image/url");
        myDropzone.emit("complete", existingFiles[i]);                
    }*/
        
});
</script>