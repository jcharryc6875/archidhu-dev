<?php
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    //********************************************************************************************************
    $tabletitle = "";
    switch($unidad_documental->getLocalizacionunidaddocumentalId())
    {
        case 1:
            $tabletitle = "de Gesti&oacute;n"; 
            break;
        case 2:
            $tabletitle = "Central";
            break;
        case 3:
            $tabletitle = "Historico";
            break;
    }
    //*******************************************************************************************************
    $modulo  = $unidad_documental->getLocalizacionunidaddocumental()->getDescripcion();
    $currentFormEditar    	    = "ARCHIVO_".$modulo."_EDITAR";
    $currentFormTipos  	    	= "ARCHIVO_".$modulo."_TIPOS_DOCUMENTALES";
    $currentFormPrestamo       	= "ARCHIVO_".$modulo."_SOLICITAR_PRESTAMO";
    $currentFormTransferir     	= "ARCHIVO_".$modulo."_TRASFERIR";
    $currentFormSticker   	    = "ARCHIVO_".$modulo."_STICKER";
    $currentFormIsad    	    = "ARCHIVO_".$modulo."_ISAD";
    $currentFormDelete    	    = "ARCHIVO_".$modulo."_ELIMINAR_CODIGO";
    $currentFormViewOld        	= "ARCHIVO_".$modulo."_VER_ANTIGUOS";
    $currentFormViewNtc4095		= "ARCHIVO_".$modulo."_DESCRIPCION_DE_ARCHIVOS";
    $currentFormCambiarLocalizacion = "ARCHIVO_".$modulo."_CAMBIAR_LOCALIZACION";
	$currentFormReabrir    	    = "ARCHIVO_REABRIR_EXPEDIENTE";
	$currentFormVisualizar      = "ARCHIVO_".$modulo."_VISUALIZAR_GEOUBICACION";
	
    $currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
	//********************************************************************************************************
    use_helper('jQuery');
?>
<style>
    .nav-tabs{
       border-color:#508CC0;
       width:100%;
     }
    
    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:focus,
    .nav-tabs > li.active > a:hover{
        background-color:#D6E6F3;
        color:#000;
        border: 1px solid #783320;
        border-bottom-color: transparent;
    }
    
    .nav-tabs > li > a:hover{
        background-color: #D6E6F3 !important;
        border-radius: 5px;
        color:#000;
    } 
</style>

<body class="page-body" data-url="http://aureasas.dev">
    <div class="page-container sidebar-collapsed1">
        <div class="sidebar-menu">
            <div class="sidebar-menu-inner">
                <!-- INICIO  MENU SIDEBAR IZQ _menuSidebar.php  -->
                <?php 
					include_once('_menuSidebar.php'); 
                ?>
                <!--FIN  MENU SIDEBAR IZQ _menuSidebar.php  -->
            </div>
        </div>

        <!--div class="main-content"-->
			<!-- Contenedor Pagina -->
			<div class="col-md-12">
				<?php if ($sf_user->hasFlash('notice')): ?>
					<div class="alert alert-success"><strong>Informaci&oacute;n! </strong><?php echo $sf_user->getFlash('notice') ?></div>
				<?php endif ?>
				<?php if ($sf_user->hasFlash('error')): ?>
					<div class="alert alert-danger"><strong>Opps! Error! </strong><?php echo $sf_user->getFlash('error') ?></div>
				<?php endif ?>
				<?php if ($sf_user->hasFlash('error_warning')): ?>
					<div class="alert alert-warning"><strong>Atenci&oacute;n! </strong><?php echo $sf_user->getFlash('error_warning') ?></div>
				<?php endif ?>
			</div>
			
			<div class="panel panel-gradient" data-collapsed="0">
				<div class="panel-heading">
					<div class="panel-title">
						Detalles expediente <?php echo $tabletitle ?>(<strong><?php echo $unidad_documental->getCodigoBarras() ?></strong>)
					</div>
				</div>
				<!-- Contenedor Contenido Formulario-->
				<div class="panel-body">
					<!-- Opciones Detalle -->
					<div class="process tabs-vertical-env">
						<ul class="process-row nav nav-tabs bordered"><!-- available classes "right-aligned" -->
							<li class="active"><a data-toggle="tab" href="#data"><span class="glyphicon glyphicon-import"></span>&nbsp;Detalles</a></li>
							<?php if(count($lista_metadatos) && (1 != 1)){ ?>
								<li><a data-toggle="tab" href="#listmetadatos"><span class="glyphicon glyphicon-list"></span>&nbsp;Lista Metadatos</a></li>
							<?php } ?>
							<?php if(count($lista_interesados)){ ?>
								<li><a data-toggle="tab" href="#listinteresados"><span class="glyphicon glyphicon-list"></span>&nbsp;Lista Interesados</a></li>
							<?php } ?>
							<?php if(count($lista_documentos) && empty($unidad_documental->getEstaCerrado())){ ?>
								<li><a data-toggle="tab" href="#expindice"><span class="glyphicon glyphicon-list"></span>&nbsp;&Iacute;ndice Electr&oacute;nico</a></li>
							<?php } ?>
							<?php if(!empty($unidad_documental->getEstaCerrado()) && !empty($unidad_documental->getEidxAbspath()) && !empty($unidad_documental->getEidxRelpath())){ ?>
								<li><a data-toggle="tab" href="#expindiceviewer"><span class="glyphicon glyphicon-list"></span>&nbsp;&Iacute;ndice Electr&oacute;nico</a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">
							<div id="data" class="tab-pane active">
								<!-- Informacion Detalle -->
								<div class="col-sm-12 col-md-12">
										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>ID Expediente</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getPrimaryKey(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>N&uacute;mero Expediente</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getCodigoBarras(); ?></p></div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Dependencia</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre()." - ".$unidad_documental->getSubserie()->getSerie()->getDependencia()->getEntidad(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>Serie</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getSubserie()->getSerie()->getDescripcion(); ?></p></div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Subserie</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getSubserie()->getDescripcion(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>Unidad Conservaci&oacute;n</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getUnidadconservadora()->getDescripcion(); ?></p></div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Estado</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getEstadounidaddocumental()->getDescripcion(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>Frecuencia Consulta</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getFrecuenciaconsulta()->getDescripcion(); ?></p></div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<div class="col-md-12"><p><strong>Nombre Expediente</strong></p></div>
												<div class="col-md-8"><p><?php print $unidad_documental->getTitulo(); ?></p></div>
											</div>
										</div>				

										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Ubicaci&oacute;n</strong></p></div>
												<div class="col-sm-8">
													<p>
														<?php
															$ubicacion = $unidad_documental->{'getUbicacionen'.mb_strtolower($modulo)}();
															if(trim($unidad_documental->getNumeroCaja())){
																$ubicacion .= " / Num. Caja: ".trim($unidad_documental->getNumeroCaja());
															}
															echo $ubicacion;
														?>
													</p>
												</div>
											</div>	
											<?php if(trim($unidad_documental->getNumeroIdentificacion())){ ?>
												<div class="col-sm-6">
													<div class="col-sm-5"><p><strong>Numero Identificaci&oacute;n</strong></p></div>
													<div class="col-sm-7"><p><?php print $unidad_documental->getNumeroIdentificacion(); ?></p></div>
												</div>
											<?php } ?>
										</div>
										
										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Fecha Inicial</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getFechaApertura(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<?php if($unidad_documental->getLocalizacionunidaddocumentalId() != 1): ?>
												<div class="col-sm-5"><p><strong>Fecha Final</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getFechaCierre(); ?></p></div>
												<?php endif ?>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Folios</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getFolios(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>Tomo</strong></p></div>
												<div class="col-sm-7"><p><?php print $unidad_documental->getVolumen(); ?></p></div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-6">
												<div class="col-sm-4"><p><strong>Fecha Vencimiento</strong></p></div>
												<div class="col-sm-8"><p><?php print $unidad_documental->getFechavencimiento(); ?></p></div>
											</div>
											<div class="col-sm-6">
												<div class="col-sm-5"><p><strong>Fecha Creaci&oacute;n</strong></p></div>
												<div class="col-sm-7"><p><?php echo $unidad_documental->getFechaCreacion(); ?></p></div>
											</div>
										</div>
										
										<?php if(!empty($unidad_documental->getNumeroCaja()) || !empty($unidad_documental->getNumeroCarpeta())) { ?>
											<div class="row">
												<div class="col-sm-6">
													<div class="col-sm-4"><p><strong>N&uacute;mero Caja</strong></p></div>
													<div class="col-sm-8"><p><?php print $unidad_documental->getNumeroCaja(); ?></p></div>
												</div>
												<div class="col-sm-6">
													<div class="col-sm-5"><p><strong>N&uacute;mero Carpeta</strong></p></div>
													<div class="col-sm-7"><p><?php echo $unidad_documental->getNumeroCarpeta(); ?></p></div>
												</div>
											</div>
										<?php } ?>
										
										<?php if(!empty($unidad_documental->getGeoBodega()) || !empty($unidad_documental->getGeoCuerpo()) || !empty($unidad_documental->getGeoTorre()) || !empty($unidad_documental->getGeoPiso()) || !empty($unidad_documental->getRepositorioOrigen())){ ?>
											<div class="row">
												<div class="col-sm-6">
													<div class="col-sm-4"><p><strong>Ubicaci&oacute;n Topografica</strong></p></div>
													<div class="col-sm-8"><p><?php print $sf_user->checkPerm($currentFormVisualizar, $currentUser) ? $unidad_documental->getUbicacionTopografica() : "confidencial"; ?></p></div>
												</div>
												<div class="col-sm-6">
													<div class="col-sm-4"><p><strong>Repositorio Origen</strong></p></div>
													<div class="col-sm-8"><p><?php print $unidad_documental->getRepositorioOrigen(); ?></p></div>
												</div>
											</div>
										<?php } ?>

										<?php if($unidad_documental->getParentunidaddocId()) { ?>
											<div class="row">
												<div class="col-sm-6">
													<div class="col-sm-4"><p><strong>Expediente Origen</strong></p></div>
													<div class="col-sm-8"><p><?php echo $expediente_origen; ?></p></div>
												</div>
												<div class="col-sm-6">
													<div class="col-sm-5"><p><strong>&nbsp;</strong></p></div>
													<div class="col-sm-7"><p><?php echo "&nbsp;"; ?></p></div>
												</div>
											</div>
										<?php } ?>

										<?php for ($num = 0; $num < count($lista_metadatos); $num++) { ?>
											<?php if (($num % 2) == 0) { ?>
												<div class="row">
													<div class="col-sm-6">
														<div class="col-sm-4"><p><strong><?php echo ucwords(mb_strtolower($lista_metadatos[$num]->getMetadatos()->getNombre())) ?></strong></p></div>
														<div class="col-sm-8"><p><?php echo $lista_metadatos[$num]->getValueField() ?></p></div>
													</div>
											<?php }else{ ?>
													<div class="col-sm-6">
														<div class="col-sm-5"><p><strong><?php echo ucwords(mb_strtolower($lista_metadatos[$num]->getMetadatos()->getNombre())) ?></strong></p></div>
														<div class="col-sm-7"><p><?php echo $lista_metadatos[$num]->getValueField() ?></p></div>
													</div>
												</div>
											<?php } ?>
										<?php } ?>
										
										<?php if((count($lista_metadatos) % 2) != 0) { echo '</div>'; } ?>

										<?php if(trim($unidad_documental->getContenido())){ ?>
											<div class="row">                    
												<div class="col-sm-12">
													<div class="panel panel-success">
														<div class="panel-heading">
															<div class="panel-title">Contenido</div>
														</div>        					
														<div class="panel-body">        						
															<div class="scrollable" data-height="200px" data-rail-opacity="1" data-rail-color="blue">
																<?php echo $unidad_documental->getContenido(); ?>
															</div>        						
														</div>
													</div>                						
												</div>
											</div>					
										<?php } ?>
										
										<?php if(trim($unidad_documental->getNotas())){ ?>
											<div class="row">                    
												<div class="col-sm-12">
													<div class="panel panel-info">
														<div class="panel-heading">
															<div class="panel-title">Notas</div>
														</div>        					
														<div class="panel-body">        						
															<div class="scrollable" data-height="100" data-rail-opacity="1" data-rail-color="blue">
																<?php echo $unidad_documental->getNotas(); ?>
															</div>        						
														</div>
													</div>                						
												</div>
											</div>
										<?php } ?>

										<?php if(trim($unidad_documental->getObsCierre())){ ?>
											<div class="row">                    
												<div class="col-sm-12">
													<div class="panel panel-warning">
														<div class="panel-heading">
															<div class="panel-title">Justificaci&oacute;n Cierre</div>
														</div>        					
														<div class="panel-body">        						
															<div class="scrollable" data-height="100" data-rail-opacity="1" data-rail-color="blue">
																<?php echo $unidad_documental->getObsCierre(); ?>
															</div>        						
														</div>
													</div>                						
												</div>
											</div>
										<?php } ?>
								</div>
							</div>
							<!-- listado de metadatos -->
							<?php if(count($lista_metadatos)){ ?>
								<div id="listmetadatos" class="tab-pane">
									<?php 
										include_partial('listMetadatos',array('list_metadatos'=>$lista_metadatos)); 
									?>
								</div>
							<?php } ?>
							<!-- listado de interesados -->
							<?php if(count($lista_interesados)){ ?>
								<div id="listinteresados" class="tab-pane">
									<?php 
										include_partial('listInteresados',array('unidad_documental' => $unidad_documental,'list_interesados'=> $lista_interesados));
									?>
								</div>
							<?php } ?>
							<!-- lista de documentos -->
							<?php if(count($lista_documentos) && empty($unidad_documental->getEstaCerrado())){ ?>
								<div id="expindice" class="tab-pane">
									<?php 
										include_partial('viewIndice',array('unidad_documental' => $unidad_documental,'list_documentos'=> $lista_documentos));
									?>
								</div>
							<?php }else if(!empty($unidad_documental->getEstaCerrado()) && !empty($unidad_documental->getEidxAbspath()) && !empty($unidad_documental->getEidxRelpath())){ ?>
								<div id="expindiceviewer" class="tab-pane" style="height: 100vh !important;">
									<?php 
										include_partial('expIndiceViewer',array('unidad_documental' => $unidad_documental));
									?>
								</div>
							<?php } ?>							
						</div>
					</div>
				</div>
			</div>
        <!--/div-->
	</div>
</body>


