<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$modulo  = $sf_params->get('modulo');
$currentFormEditarTipo    	= $modulo."_EDITAR_TIPO_DOCUMENTAL";
$currentFormMezclar     	= $modulo."_MEZCLAR";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<!-- Contenendor Basico -->
<div class="row">
	<div class="col-md-12">
    <!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalles de Tipo Documental
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<div class="col-sm-12 col-md-12">
				<div class="form-group">

					<?php
					// Boton Regresar
					echo link_to(image_tag('simad/ico_regresar.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Regresar a Lista De Tipos Documentales')).'Regresar', 'contenido_documental/list?unidaddocumental_id='.$unidad_documental.'&modulo='.$modulo, array('class' => 'btn btn-sm btn-white'));
					?>

					<?php
					// Boton Editar					
					if($sf_user->checkPerm($currentFormEditarTipo, $currentUser) && empty($contenido_unidad_documental->getUnidadDocumental()->getEstaCerrado())){ 
						echo link_to(image_tag('simad/ico_editar.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Editar Tipo Documental')).'Editar', 'contenido_documental/edit?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&unidaddocumental_id='.$contenido_unidad_documental->getUnidaddocumentalId().'&modulo='.$modulo, array('class' => 'btn btn-sm btn-white'));
					}
					?>

					<?php
					// Boton Mezclar
					if($sf_user->checkPerm($currentFormMezclar, $currentUser)){
						if($contenido_unidad_documental->getRuta() != '' && $formato == 'tiff' || $formato == 'tif' && $formato_adjunto == 'tif' || $formato_adjunto == 'tiff' ){ 
							echo jq_link_to_function(image_tag('simad/ico_pre_visualizar_img.png', array('border'=>"0",'width'=>"25",'title'=>'Adicionar Imagenes Tiff','align'=>"middle")).'Mezclar Imagenes', 'javascript:mezclar_imagenes("/archivo.php/contenido_documental/mezclarImagen?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'");', array('class' => 'btn btn-sm btn-white'));
						}elseif($contenido_unidad_documental->getRuta() != '' && $formato == '.pdf' && $formato_adjunto == '.pdf'){ 
							echo jq_link_to_function(image_tag('simad/ico_pdf.png', array('border'=>"0",'width'=>"25",'title'=>'Adicionar mas PDF`s','align'=>"middle")).'Mezclar PDF`s', 'javascript:mezclar_imagenes("/archivo.php/contenido_documental/mezclarPdf?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'");', array('class' => 'btn btn-sm btn-white'));
						}
					} 
					?>
				</div>
			</div>
			<hr />
			<div class="col-sm-12 col-md-12">

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Unidad Documental</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getUnidaddocumental()->getTitulo(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Tipo Documental</strong></p></div>
						<div class="col-sm-7"><p><?php echo $contenido_unidad_documental->getTipodocumental()->getDescripcion();?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Descripci&oacute;n</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getDescripcion(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Total Paginas:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $contenido_unidad_documental->getFolios(); ?></p></div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Pagina Inicial:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getFolioInicial(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Pagina Final:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $contenido_unidad_documental->getFolioFinal(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Estado:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getEstadoContenidoUnidadDocumental()->getDescripcion(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Verificaci&oacute;n:</strong></p></div>
						<div class="col-sm-7">
							<p>
								<div id="<?php echo md5($contenido_unidad_documental->getPrimaryKey()) ?>">
									<?php if($contenido_unidad_documental->getVerificacioncontunidaddocId() == 1){ ?>
										<?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
											array('id'=>"feedcheck",'alt'=>'Registro verificado','title'=>'Registro verificado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
											'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
											'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
											'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
										)) ?>
										<?php }else{ ?>
											<?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
											array('id'=>"feeduncheck",'alt'=>'Registro no verificado','title'=>'Registro no verificado','width'=>"25" ,'height'=>"25")), array(
											'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
											'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
											'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
										)) ?>
									<?php } ?>
								</div>
							</p>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Documento:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getFechaDocumento(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Creaci&oacute;n:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $contenido_unidad_documental->getFechaCreacion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Usuario:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getUsuario()->getNombre()." ".$contenido_unidad_documental->getUsuario()->getApellido(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Origen Documento:</strong></p></div>
						<div class="col-sm-7"><p><?php  echo $contenido_unidad_documental->getOrigenDocumento(); ?></p></div>
					</div>
				</div>

				<div class="row">
				<?php if($contenido_unidad_documental->getRuta() != ""){ ?>
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Ruta:</strong></p></div>
						<div class="col-sm-8">
							<p>
								<?php
									if($contenido_unidad_documental->getVinculoRegistro()){
										$hash_view = md5(basename($contenido_unidad_documental->getRuta()).$currentUser);
										$url_view = $base_path.$contenido_unidad_documental->getRuta().'/backid/'.$contenido_unidad_documental->getPrimaryKey().'/viewstate/'.$hash_view;
										echo link_to(image_tag('simad/ico_adjunto-digital.png', array('width'=>"25",'align'=>"middle")),$url_view ,array('data-original-title'=>'Ver documento vinculado', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
									}elseif(!empty(basename($contenido_unidad_documental->getRuta()))){
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
									}else{
										echo image_tag('simad/ico_anular.png',array('data-original-title'=>'ningun archivo adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','border'=>"0",'width'=>"25",'align'=>"middle"));
									}
								?>				
							</p>
						</div>
					</div>				
				<?php }else{ ?>
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Archivo Remisionado</strong></p></div>
						<div class="col-sm-8"><p><?php echo $contenido_unidad_documental->getContenidounidaddocumentalId().".pdf";?></p></div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	   </div>
	</div>
</div>