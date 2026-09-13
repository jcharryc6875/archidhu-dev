<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$ruta_base = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';


$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
//$modulo = $sf_params->get('modulo');
$modulo = 'GESTION';
$currentFormCargarTipo  	= $modulo."_CARGAR_TIPO_DOCUMENTAL";
$currentFormCompartirTipo   = $sf_user->checkPerm($modulo."_COMPARTIR_CONTENIDO", $currentUser);

use_helper('jQuery');
?>

<script src="<?php print $path_theme;?>assets/js/aci-tree/js/jquery.aciPlugin.min.js"></script>
<script src="<?php print $path_theme;?>assets/js/aci-tree/js/jquery.aciTree.min.js"></script>
<script src="<?php print $path_theme;?>assets/js/aci-tree/js/jquery.aciTree.column.js"></script>
<script src="<?php print $path_theme; ?>assets/js/toastr.js"></script>

<style>
.aciTreeHeader {
    display: flex;
    font-weight: bold;
    font-size: 12px;
    padding: 4px 6px;
    background-color: #f7f7f7;
    border-bottom: 1px solid #ccc;
}
.aciTreeCol {
    padding-right: 12px;
}
.aciTreeColumn {
    text-align: left;
    padding-right: 10px;
    font-size: 12px;
}
.aciTree .aciTreeItem .aciTreeText {
    display: inline-flex;
    align-items: center;
}
.aciTree .aciTreeIcon {
    margin-right: 5px;
}

#screenOverlay {
	display: none;
	position: fixed;
	z-index: 9999;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0,0,0,0.5);
}


.highlight {
        background: yellow;
        color: black;
        font-weight: bold;
        padding: 1px 2px;
        border-radius: 2px;
    }
</style>

<!-- 🕶️ Cortina (overlay) oculta por defecto -->
<div id="screenOverlay"></div>

<div class="row">
	<div class="col-md-12">
		<!-- Contenedor Pagina -->
		<div class="panel panel-primary" data-collapsed="0">

			<div class="row">		
				<div class="col-sm-4">
					<!-- INICIO FILETREE -->
					<script type="text/javascript">
						jQuery(document).ready(function() 
						{
							var $ = jQuery,
							$events_log = $("#events_log");

							$('#tree1').aciTree({
								ajax: {
									url: '<?php echo url_for('/archivo.php/contenido_documental/dataFolderTree'); ?>?branch=',
									data: {unidaddocumental_id: '<?php echo $unidaddocumental_id; ?>'}
								},
								fullRow: true,
								checkbox: false,
								// === COLUMNAS ===
								itemHook: function(parent, item, itemData, level) {
									if (!itemData) return;

									if (itemData.type === 'file' && itemData.data) 
									{
										this.setLabel(item, {label: itemData.data.tipo_doc});

										$(item).find('.aciTreeText').off('click').on('click', function(e) {
											e.preventDefault();
											e.stopPropagation();
											// Hacemos la petición AJAX al nuevo action Symfony
											$.ajax({
												url: '<?php echo url_for('/archivo.php/contenido_documental/showFileTree'); ?>',
												method: 'POST',
												data: { 
													contenidounidaddocumental_id: itemData.contenidounidaddocumental_id,
													unidaddocumental_id: '<?php echo $unidaddocumental_id; ?>', 
													modulo: '<?php echo $modulo; ?>' 
												},
												success: function(response) 
												{
													$('#<?php echo md5('contudocftreeinfo');?>').html(response);
												},
												error: function() 
												{
													$('#<?php echo md5('contudocftreeinfo');?>').html('<span style="color:red;">Error al cargar el detalle.</span>');
												}
											});
										});
									}

									if (itemData.type === 'folder') 
									{
										$(item).find('.aciTreeText').off('click').on('click', function(e) {


											// ⚠️ Importante: primero, permitimos el toggle normal
											const api = $('#tree1').aciTree('api');
											const currentItem = api.itemFrom(e.target);

											if (currentItem && api.isInode(currentItem)) 
											{
												if (api.isOpen(currentItem)) 
												{
													api.close(currentItem);
												} 
												else 
												{
													api.open(currentItem);
												}
											}

											$('#<?php echo md5('contudocftreeinfo');?>').html(`<div class="lazyview"></div>`);

										});
									}

									this.itemData(item, itemData);
								},
							})
							.on('acitree', function(event, api, item, eventName, options) {
								switch (eventName) {
									case 'init':
										api.open(api.first(), {
											success: function(item) {
												this.select(this.first(item));
											}
										});
										$('#tree1').focus();
										break;

									case 'selected':
										
										break;
								}
							});

							// el recargador
							$('#reloadTree').on('click', function(e) 
							{
								e.preventDefault();

								// Mostrar la cortina
								$('#screenOverlay').fadeIn(200);

								// Confirmar con el usuario
								const confirmReload = confirm('¿Está seguro? Este proceso puede tardar en realizarse.');

								if (!confirmReload) {
									// Si cancela, ocultar cortina y salir
									$('#screenOverlay').fadeOut(200);
									return;
								}

								// Si confirma, proceder a recargar el árbol
								const api = $('#tree1').aciTree('api');
								if (api) {
									api.unload(null, {
										success: function() {
											api.ajaxLoad(null, {
												success: function() {
													// Opcional: abre el primer nodo
													api.open(api.first());
													// Ocultar la cortina
													$('#screenOverlay').fadeOut(300);
													// Limpia el panel lateral
													$('#detalleContenido').html('<p>🌳 Árbol recargado. Seleccione una carpeta o documento.</p>');
												},
												error: function() {
													alert('❌ Ocurrió un error al recargar el árbol.');
													$('#screenOverlay').fadeOut(300);
												}
											});
										}
									});
								} else {
									$('#screenOverlay').fadeOut(200);
								}
							});

							// =========================================================================================
							// 🔍 BUSCADOR ASÍNCRONO ACITREE (3+ caracteres, resalta e ilumina coincidencias)
							// =========================================================================================

							$('#searchInput').on('keyup', function () {
								const term = $(this).val().trim();
								const treeApi = $('#tree1').aciTree('api');

								if (term.length < 3) {
									treeApi.filter(null, { search: '' });
									return;
								}
								
								treeApi.filter(null, {
									search: term,
									caseSensitive: false,
									reveal: true,        // Expande los nodos donde hay coincidencias
									highlight: false,    // Usaremos highlight propio (más bonito)
									multiple: true,      // Permite encontrar varios nodos
								});
							});
						});
					</script>
					
					<div class="panel panel-primary" data-collapsed="0">
								
						<div class="panel-heading">

							
							<input type="text" name="searchInput" id="searchInput" placeholder="Buscar en el árbol..." class="required form-control input-sm formatcustom">
							

							<div class="panel-title">
								Agrupaci&oacute;n de Documentos
							</div>
							
							<div class="panel-options">

								<?php
									echo link_to_function('<i class="entypo-plus"></i>', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/contenido_documental/createFiletree?unidaddocumental_id=' . $unidaddocumental_id . '",960,480)',array('data-original-title'=>'Crear nueva carpeta contenedora', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip'));
								?>
								<!-- 🔁 Botón o enlace de recarga -->
								<a href="#" id="reloadTree"><i class="entypo-arrows-ccw"></i></a>
							</div>
						</div>
						
						
						<div class="panel-body no-padding" style="padding: 5px 0;">
							
							<div id="tree1" class="aciTree" style="min-height: 70px;">
							<!-- Here comes the tree AQUI VIENE EL ARBOL-->
							</div>
						</div>
						
						<?php // echo include_partial('textareaFileTreeView'); ?>
					</div>
					<!-- FINAL FILETREE -->
				</div>
			
				<div class="col-sm-8">
					<div class="panel panel-primary">
						<div class="panel-heading">
						</div>
						<div class="panel-body with-table">
							<div class="row">
								<?php echo include_partial('defectFileTreeView', 
								array(
									'currentUser'=>$currentUser, 
									'modulo'=>$modulo, 
									'currentFormCargarTipo'=>$currentFormCargarTipo, 
									'unidaddocumental_id'=>$unidaddocumental_id, 
									'unidad_documental'=>$unidad_documental, 
									'subserie_id'=>$subserie_id, 
									'estadotransfer'=>$estadotransfer, 
									'currentFormCompartirTipo'=>$currentFormCompartirTipo, 
									)); ?>
							</div>
						</div>

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
									if($sf_user->checkPerm($currentFormCargarTipo, $currentUser) && $estadotransfer == 0 && empty($unidad_documental->getEstaCerrado())){
										echo link_to (image_tag('simad/ico_carga_tipo_doc.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Cargar TD', 'contenido_documental/tiposDocumentales?unidaddocumental_id='.$unidaddocumental_id.'&modulo='.$modulo, 
											array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Cargar todos los tipos documentales para este expediente', 'data-toggle' => 'tooltip'));
									}
								?>

								<?php
									if($estadotransfer == 0 && empty($unidad_documental->getEstaCerrado()))
									{
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

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>




