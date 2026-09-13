<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

$currentFormDeleteImg    	= "ARCHIVO_".$modulo."_ELIMINAR_IMAGEN";
$currentFormDeleteTipo    	= $modulo."_ELIMINAR_CONTENIDO";
use_helper('Object','jQuery');
?>
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/typeahead.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/fileinput.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/dropzone/dropzone.js"></script>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
				<?php echo $contenido_unidad_documental->getPrimaryKey() ? "Editar" : "Crear"; ?> Contenido Documental (<strong><?php echo $unidad_documental->getCodigoBarras()?></strong>)
			</div>
		</div>

		<div class="col-md-12">
			<?php
				if($sf_user->hasFlash('error'))
				{
					?>
					<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('error') ?></div>
					<?php
				}

				if($sf_user->hasFlash('error_expclose'))
				{
					?>
					<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('error_expclose') ?></div>
					<?php
				}

				if($sf_user->hasFlash('messages_info'))
				{
					?>
					<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('messages_info') ?></div>
					<?php
				}
			?>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<?php
				echo form_tag('contenido_documental/update', array('name'=>'form1','multipart=true','class' => 'form-horizontal form-groups-bordered validate')); 
				echo object_input_hidden_tag($contenido_unidad_documental, 'getContenidounidaddocumentalId');
				echo input_hidden_tag('localizacionunidaddocumental_id', $unidad_documental->getLocalizacionunidaddocumentalId());
				echo input_hidden_tag('unidaddocumental_id', $unidad_documental->getPrimaryKey());
				echo input_hidden_tag('modulo', $sf_params->get('modulo'));
				echo input_hidden_tag('destinotransferencia_id', '');
				echo input_hidden_tag('descripcion_destino', '');
				echo input_hidden_tag('delete_ruta', '0');
			?>
            
			<div class="form-group">  
				<!-- Tipo Documental -->  
				<label for="tipodocumental_id" class="col-sm-1 control-label">Tipo Documental<span class="ctrlreq">(*)</span></label>
				<div class="col-sm-10">
                    <?php if(!$iseditdata)
					{ ?>
    					<select name="tipodocumental_id" id="tipodocumental_id" class="validatstrclose form-control required input-sm select2" <?php echo $iseditdata ?  "disabled" : ""; ?>>
							<option value="">Seleccione tipo documental...</option>
							<?php 
								$tipos = $tipos_documentales;
								foreach($tipos as $tipo)
								{
									$descripcion_tipo = $tipo->getDescripcion();
								$cierra_expediente = $tipo->getCierraExpediente();
									//$tratamiento = htmlentities($cominterna_usuario_destino->getUsuario()->getPrefijo(), ENT_QUOTES, 'UTF-8');
									echo "<option value='".$tipo->getTipodocumentalId()."'";
    							if($tipo->getTipodocumentalId() == $contenido_unidad_documental->getTipodocumentalId())
								{
										echo " selected ";
									}
									echo ">".ucwords(mb_strtolower($descripcion_tipo))."</option>";
								}
							?>
						</select>
                    	<?php 
					}
					else
					{
                        echo input_tag('tipodoc_descripcion', $contenido_unidad_documental->getTipodocumental()->getDescripcion(), array ('size' => 50,'readonly'=>true,'class'=>'form-control input-sm'));
                        echo object_input_hidden_tag($contenido_unidad_documental, 'getTipodocumentalId');
                    } ?>
				</div>
			</div>

            <?php if($unidad_documental->getSubserie()->getTipodocsviewId() == 2){// lista carpetas ?> 
				<div class="form-group">  
					<!-- File Tree -->  
					<label for="contenidodocfiletree_id" class="col-sm-1 control-label">Carpeta Contenedora</label>
					<div class="col-sm-10">
						<select name="contenidodocfiletree_id" id="contenidodocfiletree_id" class="validatstrclose form-control input-sm select2">
							<option value="">Seleccione carpeta...</option>
							<?php 
							foreach($file_trees as $file_tree)
							{
								echo "<option value='".$file_tree->getPrimaryKey()."'";
								if($file_tree->getPrimaryKey() == $contenido_unidad_documental->getContenidodocfiletreeId())
								{
									echo " selected ";
								}
								echo ">". $file_tree->getNombre() . " - " . $file_tree->getDescripcion() . "</option>";
							}
							?>
						</select>
					</div>
				</div>
			<?php } ?>

            <div class="form-group">
				<!-- Descripcion -->
				<label for="descripcion" class="col-sm-1 control-label">Descripci&oacute;n<span class="ctrlreq">(*)</span></label>
				<div class="col-sm-10">
                    <?php 
                        if(!$iseditdata)
						{
					       echo object_textarea_tag($contenido_unidad_documental, 'getDescripcion', array('size' => '50x2','class'=>'form-control input-sm required'));
                        }
						else
						{
                            echo textarea_tag('descripcion', '', array ('size' => '50x2','name'=>'newdescription', 'id'=>'newdescription','name'=>'newdescription','class'=>'form-control input-sm'));
                        } 
                    ?>
				</div>
			</div>
            
            
			<div class="form-group">  
				<!-- Unidad Documental -->
				<label for="nombre" class="col-sm-1 control-label">Unidad Documental</label>
				<div class="col-sm-8">
					<div class="input-group">
						<?php 
						echo input_tag('nombre', $unidad_documental->getCodigoTitulo(), array('size' => '80','class' => 'form-control input-sm', 'readonly'=>true));
						?>
                        <?php if(!$iseditdata){?>
    						<div class="input-group-btn">
    							<button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>archivo.php/transferencia/unidad'); return false;">Seleccionar</button>
    		                </div>
                        <?php } ?>
	             	</div>
				</div>
			</div>
            
            <?php if($contenido_unidad_documental->getPrimaryKey() && trim($contenido_unidad_documental->getRuta())){ ?>
                <div class="form-group">
					<!-- Imagenes Actuales -->
    				<label for="nombre" class="col-sm-1 control-label">Imagenes Actuales</label>
    				<div class="col-sm-10">
    					<div class="input-group">
    						<?php
                                $active_files = array();
                                $json_data = array();
                                if($contenido_unidad_documental->getVinculoRegistro())
								{
									$hash_view = md5(basename($contenido_unidad_documental->getRuta()).$currentUser);
									$url_view = $base_path.$contenido_unidad_documental->getRuta().'/backid/'.$contenido_unidad_documental->getPrimaryKey().'/viewstate/'.$hash_view;
									echo link_to(image_tag('simad/ico_adjunto-digital.png', array('width'=>"25",'align'=>"middle")),$url_view ,array('data-original-title'=>'Ver documento vinculado', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
								}
								elseif(!empty(basename($contenido_unidad_documental->getRuta())))
								{
									$ruta = preg_split("/[,]+/",trim($contenido_unidad_documental->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
									//$url_vtoken = $contenido_unidad_documental->getUrlTokenViewImageByObject();
									$url_async = $contenido_unidad_documental->getUrlTokenViewImageByObjectAjax();
									
									echo jq_link_to_remote(image_tag('simad/ico_adjunto.png',
										array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
										'update'  => null,
										'url'     => $base_path.url_for($url_async['baseurl']),
										'with'    => "'key_id=".$url_async['key_id']."&vtoken=".$url_async['vtoken']."'",
										'loading' => "javascript:jQuery.LoadingStructData();",
										'complete' => "javascript:jQuery.CloseLoadingStructData();toastr.info('Por favor actualice esta pagina para ver los cambios');",
										'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
										),array('data-original-title'=>'Visualizar este adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
									);
								}
                                echo input_hidden_tag('ruta',implode(",",$active_files));
                                $json_files = json_encode($json_data);
                            ?>                          
    	             	</div>
    				</div>
    			</div>
                
                <div class="form-group">  
    				<!-- Opciones -->
    				<label for="nombre" class="col-sm-1 control-label">Opciones</label>
    				<div class="col-sm-10">
    					<div class="input-group">
    						<?php if($contenido_unidad_documental->getContenidounidaddocumentalId() && trim($contenido_unidad_documental->getRuta())){ ?>
                                    <button type="button" class="btn btn-red btn-sm tooltip-primary" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>archivo.php/contenido_documental/deleteImg?contenidounidaddocumental_id=<?php echo $contenido_unidad_documental->getContenidounidaddocumentalId().'&modulo='.$sf_params->get('modulo'); ?>', 600, 380);" data-toggle="tooltip" data-original-title="Eliminar imagenes de este contenido documental">Eliminar Imagen</button>
                            <?php } ?>
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
                          <strong>Documentos anexos( haga clic para abrir)</strong>
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
				<!-- Total Folios -->  
				<label for="lbTotalFolios" class="col-sm-1 control-label">Total Folios<span class="ctrlreq">(*)</span></label>
				<div class="col-sm-2">
					<?php echo object_input_tag($contenido_unidad_documental, 'getFolios', array('class' => 'required form-control input-sm formatcustom','data-mask'=>'decimal','readonly'=>$iseditdata)); ?>
				</div>
			</div>

			<?php //if(1 != 1){ ?>
				<div class="form-group formattypecust">  
					<!-- Folios -->  
					<label for="FolioInicial" class="col-sm-1 control-label">Folio Inicial</label>
					<div class="col-sm-3">
						<?php echo object_input_tag($contenido_unidad_documental, 'getFolioInicial', array('class' => 'form-control input-sm','data-mask'=>'decimal','readonly'=>$iseditdata)); ?>
					</div>
					
					<!-- Folio Final -->  
					<label for="FolioFinal" class="col-sm-1 control-label">Folio Final</label>
					<div class="col-sm-3">
						<?php echo object_input_tag($contenido_unidad_documental, 'getFolioFinal', array('disabled' => $iseditdata,'data-mask'=>'decimal','class' => 'form-control input-sm')); ?>
					</div>
				</div>
            <?php //} ?>

			<div class="form-group">
				<!-- origen documento -->  
				<label for="origen_documento" class="col-sm-1 control-label">Origen Documento<span class="ctrlreq">(*)</span></label>
				<div class="col-sm-3">
					<?php 
						echo object_select_tag($contenido_unidad_documental, 'getOrigendocumentoId', array('peer_method'=>'getOrigenDocumentoOrden','disabled' => $iseditdata,'include_custom'=>'Seleccione...','class' => 'form-control input-sm choicecustom  required', 'related_class' => 'OrigenDocumento')); 
					?>
				</div>
                <!-- Estado -->  
				<label for="codigo_barras" class="col-sm-1 control-label">Estado</label>
				<div class="col-sm-3">
					<?php 
					echo object_select_tag($contenido_unidad_documental, 'getEstadocontenidounidaddocId', array('disabled' => $iseditdata,'class' => 'form-control input-sm', 'related_class' => 'EstadoContenidoUnidadDocumental')); 
					?>
				</div>
			</div>

            <div class="form-group">
				<!-- Fecha -->
				<label for="fecha_documento" class="col-sm-1 control-label">Fecha Documento<span class="ctrlreq">(*)</span></label>
				<div class="col-sm-3">
					<div class="input-group">
		              <?php
                      $option = trim($iseditdata) ? 'form-control input-sm required' : 'form-control input-sm datepicker required';
		              $fecha_documento = '';
		              if($contenido_unidad_documental->getFechaDocumento()){
                        $fecha_documento = $contenido_unidad_documental->getFechaDocumento("Y-m-d");
		              }
		              echo input_tag('fecha_documento', $fecha_documento, array('class' => $option, 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
		              ?>
                      <?php if(!$iseditdata){ ?>
		                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                      <?php } ?>
		            </div>				
				</div>
				<!-- Verificacion -->
				<label for="titulo" class="col-sm-1 control-label">Verificaci&oacute;n</label>
				<div class="col-sm-3">
					<?php echo object_select_tag($contenido_unidad_documental, 'getVerificacioncontunidaddocId', array('disabled' => $iseditdata,'class' => 'form-control input-sm', 'related_class' => 'VerificacionContUnidadDoc')); ?>
				</div>
			</div>
            
			<div class="form-group">				
				<!-- Verificacion -->
				<label for="lbTipofirmadigitalId" class="col-sm-1 control-label">Firma Digital:</label>
				<div class="col-sm-3">
					<?php echo object_select_tag($contenido_unidad_documental, 'getTipofirmadigitalId', array('disabled' => $iseditdata,'include_custom'=>'Seleccione...','class' => 'form-control input-sm', 'related_class' => 'TipoFirmaDigital'),$unidad_documental->getSubserie()->getTipofirmadigitalId()); ?>
				</div>
			</div>

			<div class="form-group">
	          <!-- Botonera -->
	          <div class="col-sm-offset-2 col-sm-10">
	            <button type="submit" class="btn btn-success btn-sm"><?php echo $contenido_unidad_documental->getPrimaryKey() ? "Guardar Cambios" : "Guardar Contenido"; ?></button>
	            <?php
    	            // Si Existe Contenido Documental
    	            if ($contenido_unidad_documental->getPrimaryKey()){	                               
						if($sf_user->checkPerm($currentFormDeleteTipo, $currentUser) && !$contenido_unidad_documental->getVinculoRegistro()){
                        	echo button_to("Eliminar Contenido",'contenido_documental/delete?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey().'&unidaddocumental_id='.$contenido_unidad_documental->getUnidaddocumentalId().'&modulo='.$sf_params->get('modulo'), array('class' => 'btn btn-red btn-sm'));
						}
                        echo "&nbsp;";
                        echo button_to("Ir Detalles",'contenido_documental/show?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey().'&unidaddocumental_id='.$contenido_unidad_documental->getUnidaddocumentalId().'&modulo='.$sf_params->get('modulo'), array('class' => 'btn btn-orange btn-sm'));
                        echo "&nbsp;";
                        echo button_to("Regresar Lista",'contenido_documental/list?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&modulo='.$sf_params->get('modulo'), array('class' => 'btn btn-blue btn-sm'));                     
                    }else{                    
                        echo button_to("Regresar Lista",'contenido_documental/list?unidaddocumental_id='.$unidad_documental->getPrimaryKey().'&modulo='.$sf_params->get('modulo'), array('class' => 'btn btn-blue btn-sm'));
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
jQuery(document).ready(function() {
    Dropzone.options.myAwesomeDropzone = false;
    var myDropzone = new Dropzone("div#myDrop", { 
        url: "<?php echo url_for('contenido_documental/dzFileUpload') ?>",
        // The configuration we've talked about above
        autoProcessQueue: true,
        addRemoveLinks: true,
        parallelUploads: 100,
        maxFiles: 1,
		maxFilesize: 2000,
		timeout: 600000,
		chunking: true,
		chunkSize: 5 * 1024 * 1024,  // 5 MB por fragmento
		retryChunks: true,
		retryChunksLimit: 3,
        dictResponseError: "Ha ocurrido un error en el servidor",
        acceptedFiles: 'image/*,.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF,.TIF,.TIFF,.tif,.tiff,.zip,.7z,.rar,application/pdf,.psd,.csv,.xls,.doc,.dat,.ppt,.eml,.msg,.mso,.xlsx,.docx,.pptx,.txt,.3gp,.mp3,.avi,.dwg,.kmz,.kmz,.wmv,.wav,.mp4,.m4v,.mov,.mpg,.mpeg,.mpeg-4,.avc,.acc_lc,.acc,.mpc-hc,.mpc,.mid,.midi,.gdb,.asf,.aac,.ogg,.p7z',
        init: function () {
			this.on('uploadprogress', (file, progress , bytesSent) => {
				const barra = file.previewElement.querySelector('.dz-upload');
				barra.style.width = progress + '%';
				barra.textContent = Math.round(progress) + '%';
			});

            this.on("success", function (file, response) {
				if (response === "" || response === null || response === undefined /*|| response.trim().length === 0*/) {
					response = JSON.parse(file.xhr.responseText);
				}

				jQuery(file.previewElement).find('[data-dz-name]').html(response.name);
				var active_value = jQuery('#ruta').val().trim() != "" ? jQuery('#ruta').val() + "," + response.name : response.name;
				file.customName = response.name;
				jQuery('#ruta').val(active_value);

				var format_file = file.name.split('.').pop().toLowerCase();
				if (!["tif", "tif", "docx", "doc", "pdf"].includes(format_file)) {
					var origendocumental_id = 1;
					jQuery('.formatcustom').val(1).addClass("data-readonly");
					jQuery('.formattypecust').hide().addClass("data-readonly");
					jQuery('.choicecustom').val(origendocumental_id).addClass("data-readonly");
				}else{
					javascript:jQuery.ArchFileExtMetadata(response.name);
				}				
            });
            
			this.on("error", function(file) {
			});

			this.on("removedfile", function(file) {
                if(file != null){                    
                    var active_new = [];
                    var active_files = jQuery('#ruta').val().split(",");
                    var index = 0;
                    for(i=0; i < active_files.length; i++){
                        if(file.customName != active_files[i]){
                            active_new.push(active_files[i]);
                        }
                        index++;
                    }

                    jQuery('#ruta').val(active_new.join(","));
					jQuery('.formatcustom').val("").removeClass("data-readonly");
					jQuery('.formattypecust').show().removeClass("data-readonly");
					jQuery('.choicecustom').val("").removeClass("data-readonly");
                }
            });            
        }
    });        
});
</script>