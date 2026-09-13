<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
$modulo = $sf_params->get('modulo');
$localizacion  = $localizacionunidaddocumental_id;
$currentFormCrearTipo    	= $modulo."_CREAR_TIPO_DOCUMENTAL";
$currentFormCargarTipo  	= $modulo."_CARGAR_TIPO_DOCUMENTAL";
$currentFormPrestamoTipo  	= $modulo."_PRESTAMO_TIPO_DOCUMENTAL";
$currentFormDeleteTipo    	= $modulo."_ELIMINAR_CONTENIDO";
$currentFormCompartirTipo   = $sf_user->checkPerm($modulo."_COMPARTIR_CONTENIDO", $currentUser);
$currentFormEditarTipo    	= $sf_user->checkPerm($modulo."_EDITAR_TIPO_DOCUMENTAL", $currentUser);

use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<div class="row">
	<div class="col-md-12">
		<?php if ($sf_user->hasFlash('error_expclose')): ?>
			<div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('error_expclose') ?></div>
		<?php endif ?>
		<?php if ($sf_user->hasFlash('messages_info')): ?>
			<div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('messages_info') ?></div>
		<?php endif ?>
		<?php if ($sf_user->hasFlash('messages_error')): ?>
			<div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('messages_error') ?></div>
		<?php endif ?>
		<?php if ($sf_user->hasFlash('error')): ?>
			<div class="alert alert-danger"><?php echo $sf_user->getFlash('error') ?></div>
		<?php endif ?>
    <!-- Contenedor Pagina -->
	<div class="panel panel-primary" data-collapsed="0">

		<div class="panel-heading">
			<div class="panel-title">
			   Lista de Tipos Documentales(<strong><?php echo $unidad_documental->getCodigoBarras(); ?></strong>)
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body with-table">
		<?php
	    $cantidad_registros = $pager->getNbResults();
	    if($cantidad_registros == 0):
	    ?>
			<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	    <?php
	    else:
	    ?>
		<!-- Listado Tipos Documentales -->
		<table class="table table-bordered table-hover table-striped responsive">
			<thead>
				<tr>
				    <th class="text-center" style="width: 5%" data-hide="phone">Marca</th>
					<th>Tipo Documental</th>
                    <th class="text-center" style="width: 8%">Imagenes</th>
					<th>Descripci&oacute;n</th>
					<th class="text-center" style="width: 5%">Pag. Inicial</th>
					<th class="text-center" style="width: 5%">Pag. Final</th>
					<th class="text-center" style="width: 8%">Fecha Documento</th>
					<th class="text-center" style="width: 8%">Origen Documento</th>
					<th class="text-center" style="width: 5%">Total Pag.</th>
					<th class="text-center" style="width: 8%">Check Verificaci&oacute;n</th>
					<th class="text-center" style="width: 8%">Checksum</th>
					<th class="text-center" style="width: 10%">...</th>
				</tr>
			</thead>
			<?php
			//****************************Verificar solicitudes del usuario Para Toda La Unidad Documental********************
			$contador_unidad_documental = ContenidoUnidadDocumentalPeer::getCountSolicitudes($unidaddocumental_id,$currentUser);
			$toda_unidad_documental = ContenidoUnidadDocumentalPeer::getCountPrestamo($unidaddocumental_id); 
			//*************************************Verificar Permisos*********************************************************
			?>
			<tbody>
				<?php
                $x = 0;
				foreach ($pager->getResults() as $contenido_unidad_documental): 
				?> 
				<tr>
                      <?php echo input_hidden_tag(sprintf("iiRec%02d",$x),$contenido_unidad_documental->getPrimaryKey())?>
                 	  <td class="text-center">       
                      <?php
                      $marca_user = false;       
                      if($contenido_unidad_documental->getMarca() == $currentUser){
                         $marca_user = true;
                      }
                      echo checkbox_tag(sprintf("marca%02d",$x), $contenido_unidad_documental->getPrimaryKey(),$marca_user,array('onclick'=>
                        jq_remote_function(array(
                            'update'  => '',
                            'url'     => 'contenido_documental/marcar?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
                        ))
                      ));
                    ?></td>
      
					<td><?php echo ($contenido_unidad_documental->getTipoDocumental()->getDescripcion()); ?></td>					
					<td class="text-center">
					   <?php
						if($contenido_unidad_documental->getVinculoRegistro()){
                            $hash_view = md5(basename($contenido_unidad_documental->getRuta()).$currentUser);
                            $url_view = sfConfig::get('publicUrl').$contenido_unidad_documental->getRuta().'/backid/'.$contenido_unidad_documental->getPrimaryKey().'/viewstate/'.$hash_view;
                            echo link_to(image_tag('simad/ico_adjunto-digital.png', array('width'=>"25",'height'=>"25",'align'=>"middle")),$url_view ,array('data-original-title'=>'Ver documento vinculado', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
						}elseif(!empty(basename($contenido_unidad_documental->getRuta()))){
                            $ruta = preg_split("/[,]+/",trim($contenido_unidad_documental->getRuta()), -1, PREG_SPLIT_NO_EMPTY);
                            //$url_vtoken = $contenido_unidad_documental->getUrlTokenViewImageByObject();
							$url_async = $contenido_unidad_documental->getUrlTokenViewImageByObjectAjax();

							$image_name = "ico_adjunto.png";
							$tooltip_name = "Visualizar este archivo";
							$size_icon = "30";
							if($contenido_unidad_documental->getEsCopia()){
								$image_name = "ico_adjunto_digit_copy.png";
								$tooltip_name = "Este documento es una referencia de otro expediente, click para visualizarlo";
								$size_icon = "20";
							}

							echo jq_link_to_remote(image_tag('simad/'.$image_name,
								array('id'=>"feedcheck",'border'=>"0",'width'=>$size_icon ,'height'=>$size_icon,'align'=>"middle")), array(
								'update'  => null,
								'url'     => $base_path.url_for($url_async['baseurl']),
								'with'    => "'key_id=".$url_async['key_id']."&vtoken=".$url_async['vtoken']."'",
								'loading' => "javascript:jQuery.LoadingStructData();",
								'complete' => "javascript:jQuery.CloseLoadingStructData();toastr.info('Por favor actualice esta pagina para ver los cambios');",
								'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
								),array('data-original-title'=>$tooltip_name, 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
							);
						}else{
							echo image_tag('simad/ico_anular.png',array('data-original-title'=>'ningun archivo adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle"));
						}
						?>
					</td>
                    <td><?php echo $contenido_unidad_documental->getDescripcion(); ?></td>
					<td class="text-center"><?php echo $contenido_unidad_documental->getFolioInicial(); ?></td>
					<td class="text-center"><?php echo $contenido_unidad_documental->getFolioFinal(); ?></td>					
					<td><?php echo $contenido_unidad_documental->getFechaDocumento("Y-m-d"); ?></td>
					<td><?php echo $contenido_unidad_documental->getOrigenDocumento(); ?></td>
					<td class="text-center"><?php echo $contenido_unidad_documental->getFolios(); ?></td>
					<td class="text-center">
                        <div id="<?php echo md5($contenido_unidad_documental->getPrimaryKey()) ?>">
                          <?php if($contenido_unidad_documental->getVerificacioncontunidaddocId() == 1 && empty($unidad_documental->getEstaCerrado())){ ?>
                          <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
                    		array('id'=>"feedcheck",'alt'=>'Registro verificado','title'=>'Registro verificado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                        	'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
                        	'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
                    		'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
                          )) ?>
                          <?php }else if(empty($unidad_documental->getEstaCerrado())){ ?>
                            <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
                    		array('id'=>"feeduncheck",'alt'=>'Registro no verificado','title'=>'Registro no verificado','width'=>"25" ,'height'=>"25")), array(
                        	'update'    => md5($contenido_unidad_documental->getPrimaryKey()),
                        	'url'     => 'contenido_documental/verificarContenido?contenidounidaddocumental_id='.$contenido_unidad_documental->getPrimaryKey(),
                    		'failure' => "alert('Ocurrio un error durante la verificacion, Por favor intente de nuevo!')",
                          )) ?>
                          <?php } ?>
                        </div>
                     </td>
                     <td class="text-center">
						 <?php
							if($contenido_unidad_documental->getDataValorHuella() === $contenido_unidad_documental->getValorHuella()){
								echo image_tag('simad/ico_isValid.png',array('class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','data-original-title'=>'El valor huella es valido','border'=>"0",'width'=>"25" ,'height'=>"25"));
							}else{
								echo image_tag('simad/ico_notValid.png',array('class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','data-original-title'=>'El valor huella no es valido, el registro cambio despues de la ultima verificaci&oacute;n','border'=>"0",'width'=>"25" ,'height'=>"25"));
							}
						 ?>
					</td>
                     <td class="text-center">
						<?php
						  // Ver Detalles Tipo Documental
						  echo link_to(image_tag('simad/ico_ira.png', array('border'=>"0",'width'=>"25",'align'=>"middle")), 'contenido_documental/show?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo,array('data-original-title'=>'Ver detalles de este contenido documental', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); 
						?>
                    
						<?php
							// Editar Tipo Documental
							if($currentFormEditarTipo && empty($unidad_documental->getEstaCerrado())){ 
							echo link_to(image_tag('simad/ico_editar.png', array('border'=>"0",'width'=>"25",'align'=>"middle")), 'contenido_documental/edit?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo,array('data-original-title'=>'Editar este contenido documental', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
							}
						?>

						<?php
							// Solicitar Documento Prestado
							//****************************Verificar solicitudes del usuario Por contenido********************
							$contador = ContenidoUnidadDocumentalPeer::getCountSolicitudesPorContenido($contenido_unidad_documental->getContenidounidaddocumentalId(),$currentUser);
							$array_solicitudes = array();
							$detalles_prestamo = null;
							if($localizacion != 1){
								$detalles_prestamo = new DetallePrestamo();
								$detalles_prestamo = ContenidoUnidadDocumentalPeer::getDetallePrestamoPorContenido($contenido_unidad_documental->getContenidounidaddocumentalId());
							}
							//****************************************************************************************************************
							if($contador == 0 && $detalles_prestamo == null && $contador_unidad_documental == 0 && $toda_unidad_documental == 0){
								echo link_to(image_tag('simad/prest_solic.png', array('border'=>"0",'width'=>"25",'align'=>"middle")), 'solicitud_prestamo/update?unidaddocumental_id='.$contenido_unidad_documental->getUnidaddocumentalId().'&contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'&modulo='.$modulo,array('data-original-title'=>'Solicitar prestado este documento', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
							}elseif($contador){
								echo image_tag('simad/estados/prest_solic.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'data-original-title'=>'El documento se encuentra esta en su lista de solicitudes prendientes', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
							}elseif($detalles_prestamo != null){
								echo jq_link_to_function(image_tag('simad/estados/prest_prestado.png',array('border'=>"0",'width'=>"25",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/showTipo?detalleprestamo_id='.$detalles_prestamo->getPrimaryKey().'","600","400")',array('data-original-title'=>'El Documento se encuentra prestado', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));		
							}
							//*****************************************************************************************************************
							if($currentFormCompartirTipo){
								echo link_to_function(image_tag('simad/ico_compartir.png', array('border'=>"0",'width'=>"20",'align'=>"middle")), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/contenido_documental/consultarExp?contenidounidaddocumental_id='.$contenido_unidad_documental->getContenidounidaddocumentalId().'")',array('data-original-title'=>'Referenciar este tipo documental con otro expediente', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
							}
                        ?>
					</td>
				</tr>
				<?php
				endforeach;
				?>

				<?php
				// Listado Cantidad de Folios
				if($cantidad_registros != 0):
				?>
				<tr>
					<td></td>
					<td></td>
                    <td></td>
                    <td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="text-center"><strong>Total Folios</strong></td>
					<td class="text-center"><?php echo $total_folios; ?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				endif;
				?>
			</tbody>
		</table>

		<!-- Paginador -->
        <div class="dataTables_wrapper">
          <div class="row">
            <div class="col-xs-6 col-left">
              <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults();?></div>
            </div>
            <div class="col-xs-6 col-right">
              <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
                <?php
                echo use_helper('Pagination');
                echo pager_navigation($pager, 'contenido_documental/list', $filtros_consulta.'&modulo='.$modulo);
                ?>
              </div>
            </div>
          </div>
        </div>
		<?php
		endif;
		?>

		<!-- Opciones Listar Tipos Documental -->
        <div class="row">
        	<div class="col-sm-12 form-group">                    
        		<?php
        		  // Devolver
        		  echo link_to(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Regresar', 'unidad_documental/show?unidaddocumental_id='.$unidaddocumental_id.'&localizacionunidaddocumental_id='.$localizacion, 
                    array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Regresar detalles del expediente', 'data-toggle' => 'tooltip'));
        		?>

        		<?php
        		  // Cerrar
        		  echo jq_link_to_function(image_tag('simad/ico_cerrar.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Cerrar','javascript:jQuery.CloseAndRefreshParent()', 
                    array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Cerrar esta ventana', 'data-toggle' => 'tooltip'));
				?>

				<?php 
				    // Consultar Tipos Documentales
				    echo link_to (image_tag('simad/ico_consultar_small.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Consultar', 'contenido_documental/consultar?unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo, 
                        array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Consultar tipos documentales', 'data-toggle' => 'tooltip'));
				?>

				<?php
    				// Cargar Tipos Documentales				
    				if($sf_user->checkPerm($currentFormCargarTipo, $currentUser) && empty($unidad_documental->getEstaCerrado())){
    					echo link_to (image_tag('simad/ico_carga_tipo_doc.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Cargar TD', 'contenido_documental/tiposDocumentales?unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo, 
                            array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Cargar todos los tipos documentales para este expediente', 'data-toggle' => 'tooltip'));
    				}
				?>

				<?php
    				// Crear Tipo Documental
    				if($sf_user->checkPerm($currentFormCrearTipo, $currentUser) && empty($unidad_documental->getEstaCerrado())){
						echo link_to (image_tag('simad/ico_crear_nuevo.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Crear', 'contenido_documental/create?unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo, 
                            array('class' => 'btn btn-sm btn-white tooltip-primary', 'data-original-title'=>'Crear un nuevo contenido documental', 'data-toggle' => 'tooltip'));
    			 	}
			 	?>

				<?php if($sf_user->checkPerm($modulo."_DESCARGAR_DOCUMENTOS", $currentUser)){ ?>
					<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('contenido_documental/downloadBatchDocs?unidaddocumental_id='.$unidaddocumental_id); ?>','1280','640');" class="btn btn-sm btn-white tooltip-primary" data-toggle="tooltip" data-original-title="Descargar todos los documento(s) asociados al expediente actual">
						<?php echo image_tag('simad/ico_expor_informe.png', array('border'=>"0",'width'=>"25",'align'=>"middle")); ?>
						<span>Descargar Documentos</span>
					</a>
				<?php } ?>

                <?php 
                    if($sf_user->checkPerm($currentFormDeleteTipo, $currentUser) && empty($unidad_documental->getEstaCerrado())){                
                        echo jq_link_to_remote(image_tag('simad/ico_delete_doc.png',
                			array('id'=>"feedback",'border'=>"0",'width'=>"25" ,'align'=>"middle")).'Eliminar Marcados', array(
                    			'update'    => '',
                    			'url'     => 'contenido_documental/deleteMarcados?unidaddocumental_id='.$sf_params->get('unidaddocumental_id').'&modulo='.$modulo,
                				'confirm'  => 'Esta seguro de eliminar los contenidos marcados! \n Esta accion no se podra deshacer',
                				//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                				'success' => "javascript:jQuery.RefreshCurrentForm();",
                				'failure' => "",
                				'complete' => "alert('Contenidos documentales eliminados exitosamente!')"),array('class' => 'btn btn-sm btn-white tooltip-primary', 'data-original-title'=>'Eliminar los contenidos marcados', 'data-toggle' => 'tooltip'
							)
						);  
                    } 
                ?>

			</div>
		</div>
     </div>
	</div>
  </div>
</div>