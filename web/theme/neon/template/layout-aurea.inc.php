<?php
	$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
	$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
	use_helper('Object','jQuery');
	$usuario_id = $sf_user->getAttribute('usuario_id', '', 'subscriber');
	$view_notify = $sf_user->getAttribute('badge_view_notify', '', 'subscriber');
	$licencia_image = $sf_user->getAttribute('image_licencia', '', 'subscriber');
	$view_storageinfo = $sf_user->getAttribute('view_storage_information', '', 'subscriber');

	$getucomstatus = $view_notify == 2 ? '&radicador='.$usuario_id : "";
	$getucomstatus .= "&qucom=".md5($usuario_id.$view_notify);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!--meta charset="windows-1252"-->
	<meta charset="utf-8">
    
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="SIMAD - SGDEA" />

	<?php include_title(); ?>

	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/font-icons/entypo/css/entypo.css"/>	
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/bootstrap.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-core.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-theme.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-forms.css"/>
	<link rel="stylesheet" href="<?php print $path_theme; ?>assets/css/toastr.min.css" />
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5"/>	
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/dropzone/dropzone.css"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/select2/select2-bootstrap.css"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/select2/select2.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/zurb-responsive-tables/responsive-tables.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/custom_aurea.css?v=<?php echo(rand()); ?>"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/skins/yellow_aurea.css?v=<?php echo(rand()); ?>"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/font-icons/font-awesome/css/font-awesome.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/bootstrap-duallistbox.css"/>
	<!-- ACI Tree CSS -->
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/aci-tree/css/aciTree.css" type="text/css" />
	
	<script src="<?php print $path_theme;?>assets/js/jquery-3.7.1.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-migrate-3.4.1.min.js"></script>
	
	<script>
	jQuery(document).ready(function($) {
		// Ocultar Menu en Ventana Emergente
		if(window.top!=window.self)
		{
			jQuery("#the-main-frame").hide().remove();
			jQuery(".sidebar-info-user").hide().remove();
			jQuery("footer").hide().remove();
		}
	});
	</script>
	<script>jQuery.noConflict();</script>  
</head>
<body class="page-body" data-url="http://neon.dev">
	<div class="page-container sidebar-collapsed">	
		<!-- EL MENU PRINCIPAL IZQUIERDA - INICIO -->
		<div class="sidebar-menu" id="the-main-frame">
			<div class="sidebar-menu-inner">
				<header class="logo-env">
					<!-- logo -->
					<div class="logo">
						<a href="<?php print $base_path; ?>backend.php/resumen">
							<img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="88px" height="32px"/>
						</a>
					</div>

					<!-- logo collapse icon -->
					<div class="sidebar-collapse">
						<a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
							<i class="entypo-menu"></i>
						</a>
					</div>
					<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
					<div class="sidebar-mobile-menu visible-xs">
						<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
							<i class="entypo-menu"></i>
						</a>
					</div>
				</header>
				<ul id="main-menu" class="main-menu">
					<!-- add class "multiple-expanded" to allow multiple submenus to open -->
					<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
					<?php 
					include_once('menu_aurea.inc.php');
					?>
				</ul>
			</div>
		</div>
		<!-- EL MENU PRINCIPAL IZQUIERDA - FIN -->

		<!-- EL CONTENIDO PRINCIPAL - INICIO -->
		<div class="main-content">
		
			<!-- MENUS: HENDERSON (TOP IZQ) Y TOP DERECHA -->
			<div class="row sidebar-info-user">
				
				<!-- EL MENU JOHN HENDERSON - INICIO -->
				<!-- Profile Info and Notifications -->
				<div class="col-md-6 col-sm-8 clearfix">
			
					<ul class="user-info pull-left pull-none-xsm">
			
						<!-- Profile Info -->
						<li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->

							<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
								<?php
									if($sf_user->isAuthenticated())
									{
										//$usuario = UsuarioPeer::retrieveByPk($usuario_id);
										if(trim($usuario_id))
										{                                    
											$ruta_foto = $sf_user->getAttribute('ruta_foto', '', 'subscriber');
										}
										else
										{
											$ruta_foto = $base_path.'/images/usuario.jpg';
										}
										//****************************************************************************************
										echo '<img src="'.$ruta_foto.'" class="user-image img-circle" width="32px"/>';
										echo '<span class="hidden-xs">'.(($sf_user->getAttribute('nombre_apellido', '', 'subscriber'))).'</span>';
									}
									else
									{
										echo "(indefinido)";
									}
								?>
							</a>
			
							<ul class="dropdown-menu">
			
								<!-- Reverse Caret -->
								<li class="caret"></li>

								<?php if($sf_user->getAttribute('config_advanced_firma', '', 'subscriber') == 1)
								{ ?>
									<li>
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigmsing',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmamecanica=1'",
											)) ?>">
											<?php 
											if($sf_user->getAttribute('firma_mecanica', '', 'subscriber') == 0)
											{ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												Habilitar Firma Mecánica
												<?php 
											}
											else
											{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												Deshabilitar Firma Mecánica
												<?php 
											} ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigesing',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmadigital=1'",
											)) ?>">
											<?php 
											if($sf_user->getAttribute('firma_digital', '', 'subscriber') == 0)
											{ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												Habilitar Firma Digital
												<?php 
											}
											else
											{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												Deshabilitar Firma Digital
											<?php 
											} ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigsingdes',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmadesatendida=1'",
											)) ?>">
											<?php 
											if($sf_user->getAttribute('firma_desatendida', '', 'subscriber') == 0)
											{ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												Habilitar Firma Desatendida
											<?php 
											}
											else
											{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												Deshabilitar Firma Desatendida
												<?php 
											} ?>
										</a>
									</li>
									<li class="sep"></li>
									<li>											
										<a onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('/administracion.php/plantillas_com/currentList'); ?>','1280','600')">
											<i style="background-color: green;" class="entypo-check"></i>
											<span class="title">Plantillas Comunicaciones</span>
										</a>
									</li>
									<?php 
								} ?>
									
							</ul>
						</li>
			
					</ul>
				</div>
				<!-- EL MENU JOHN HENDERSON - FIN -->
			
				
				<!-- EL NAV BAR MENU TOP-DERECHA - INICIO -->
				<!-- Raw Links -->
				<div class="col-md-6 col-sm-4 clearfix hidden-xs">
					<ul class="list-inline links-list pull-right">
						<li>
							<img style="max-height: 40px; max-width: 80px; min-width: 40px;border-radius: 3px;" src="<?php echo $base_path.$licencia_image; ?>" />
						</li>
						<li class="sep"></li>
						<li>
							<a href="#" data-toggle="chat" data-collapse-sidebar="1">
								<i class="entypo-star menu-icon"></i>
								<span class="nav-text">Favoritos</span>
								<!-- <span class="badge badge-success chat-notifications-badge">3</span> -->
							</a>
						</li>
						<li class="sep"></li>
						<li>
							<a class="tooltip-primary" data-original-title="Ver cartelera informativa" data-toggle="tooltip" data-placement="top" href="<?php echo url_for('/comun.php/cartelera_informativa')?>">
								<i class="entypo-publish menu-icon"></i>
								<span class="nav-text">Publicaciones</span>
							</a>
						</li>
						
						<li class="sep"></li>
						<!-- OPCION PREGUNTA INICIO -->
						<?php include_once('menu-secundario.inc.php'); ?>
						<!-- OPCION PREGUNTA FIN -->
						
						<li class="sep"></li>
						
						<li>
							<a class="tooltip-primary" data-original-title="Workflow" 
								data-toggle="tooltip" data-placement="top" href="<?php echo url_for('/comun.php/bpmn')?>">
								<i class="entypo-flow-tree"></i>
								<span class="title">&nbsp;</span>
							</a>
						</li>
						
						<li class="sep"></li>

						<li>
							<a class="tooltip-primary" data-original-title="Generar estadisticas en tiempo real" 
								data-toggle="tooltip" data-placement="top" href="<?php echo url_for('/backend.php/resumen/estadisticas')?>">
								<i class="entypo-chart-bar"></i>
								<span class="title">&nbsp;</span>
							</a>
						</li>
						
						<li>
							<a class="tooltip-primary" data-original-title="Reporte de unidades documentales y comunicaciones" 
								data-toggle="tooltip" data-placement="top" href="<?php echo url_for('/backend.php/busqueda_avanzada/reporteDinamico')?>">
								<i class="entypo-clipboard"></i>
								<span class="title">&nbsp;</span>
							</a>
						</li>
					
						<?php if(!empty($view_storageinfo)){ ?>
							<a class="tooltip-primary" data-original-title="Datos en tiempo real almacenamiento del servidor" 
								data-toggle="tooltip" data-placement="top" href="<?php echo url_for('/administracion.php/estadistica/storageInfoAdv')?>">
								<i class="entypo-drive"></i>
								<span class="title">&nbsp;</span>
							</a>
						<?php } ?>
						
						<?php if($sf_user->getAttribute('badge_view_notify', '', 'subscriber'))
						{ ?>
							<li class="sep"></li>

							<!-- Message Notifications -->
							<li class="notifications dropdown">		
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
									<i class="entypo-list"></i>
										<div id="<?php echo md5($usuario_id."badge-danger"); ?>">
											<?php $term_list = ComRecibidaPeer::getDigitTaskNotImage($view_notify); ?>
											<span class="badge badge-<?php echo ($term_list == 0 ? "success" : "danger") ?>"><?php echo $term_list; ?></span>
										</div>
										<?php
											echo jq_periodically_call_remote(
												array(
													'update' => md5($usuario_id."badge-danger"),
													'url' => url_for('/administracion.php/usuario/digitStatus'),
													'with'    => "'usuario_id=".$usuario_id."'",
													'frequency' => 120,
												)
											);
										?>							
								</a>
								<ul class="dropdown-menu">
									<li class="external">
										<a href="<?php echo url_for('/recibida.php/com_recibida/list?estadodigitalizacion_id=1'.$getucomstatus) ?>">
											<span class="task">
												<span class="desc">Ver Comunicaciones Pendientes</span>												
											</span>
										</a>
									</li>
								</ul>
							</li>
							<?php 	
						} ?>
							<li class="sep"></li>
				
							<li class="dropdown profile-info">					
								<a href="<?php print $base_path;?>backend.php/busqueda_avanzada">
									<i class="entypo-search"></i>
								</a>
							</li>
						<li class="sep"></li>
							<li class="dropdown profile-info">					
								<a href="<?php print $base_path;?>backend.php/resumen">
									<i class="entypo-home"></i>
								</a>
							</li>
							<li class="sep"></li>			
							<li>
							
							<a href="<?php echo url_for('/backend.php/security/logout')?>">
								<i class="fa fa-sign-out"></i>
								<span class="title">Salir</span>
							</a>
						</li>
					</ul>
				</div>
				<!-- EL NAV BAR MENU TOP-DERECHA - FIN -->
			</div>
		
			<hr class="sidebar-info-user spacetopx1"/>

			<!-- INICIO  HOME  -->
			<div id='form_focus'>
				<?php 
					echo $sf_content; 
				?>
			</div>
			<!-- Footer -->
			<footer class="main" style="font-weight: bold!important;">
				&copy;<?php echo date("Y"); ?> ARCHIDHU SGDEA - <?php echo "V".$application_current_version ?>
			</footer>
    		<!-- FINAL   HOME  -->
		</div>
		<!-- EL CONTENIDO PRINCIPAL - FIN -->

		<!-- EL MENU CHAT DE LA DERECHA - INICIO -->
		<div id="chat" class="fixed" data-order-by-status="1" data-max-chat-history="25">
			<div class="chat-inner">
				<h2 class="chat-header">
					<a href="#" class="chat-close"><i class="entypo-cancel"></i></a>
		
					<i class="entypo-star"></i>
					Favoritos
					<span class="badge badge-success is-hidden">0</span>
				</h2>

				<div class="chat-group">
					<strong>RECIBIDA</strong>
					<a href="/recibida.php/com_recibida/create">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Recibida</span>
					</a>
					<a href="/recibida.php/com_recibida/consulta">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Recibida</span>
					</a>
				</div>


				<div class="chat-group">
					<strong>INTERNA</strong>
					<a href="/interna.php/com_interna/create">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Interna</span>
					</a>
					<a href="/interna.php/com_interna/consulta">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Interna</span>
					</a>
				</div>


				<div class="chat-group">
					<strong>ENVIADA</strong>
					<a href="/enviada.php/com_enviada/create">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Enviada</span>
					</a>
					<a href="/enviada.php/com_enviada/consulta">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Enviada</span>
					</a>
				</div>
		

				<div class="chat-group">
					<strong>OTROS ACCESOS</strong>
					<a href="/archivo.php/unidad_documental/create?localizacion=1">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Expediente Gestión</span>
					</a>
					<a href="/archivo.php/unidad_documental/consultar?localizacion=1">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Expediente Gestión</span>
					</a>
					<a href="/archivo.php/unidad_documental/create?localizacion=2">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Expediente Central</span>
					</a>
					<a href="/archivo.php/unidad_documental/consultar?localizacion=2">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Expediente Central</span>
					</a>
					<a href="/archivo.php/unidad_documental/create?localizacion=3">
						<i class="entypo-plus-circled"></i>
						<span class="title">Crear Expediente Historico</span>
					</a>
					<a href="/archivo.php/unidad_documental/consultar?localizacion=3">
						<i class="glyphicon glyphicon-search"></i>
						<span class="title">Consultar Expediente Historico</span>
					</a>
				</div>
			</div>	
		</div>
		<!-- EL MENU CHAT DE LA DERECHA - FIN -->
	</div>

	<!-- Bottom scripts (common) -->
	<script src="<?php print $path_theme;?>assets/js/gsap/main-gsap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/joinable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/resizeable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-api.js"></script>
    <script src="<?php print $path_theme;?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.inputmask.bundle.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/prefixfree.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/jquery.observe_field.js"></script>    
    <!-- Imported styles on this page -->
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jvectormap/jquery-jvectormap-1.2.2.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/rickshaw/rickshaw.min.css">
	<!-- Bottom scripts (common) -->
    <script src="<?php print $path_theme;?>assets/js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <!-- Imported scripts on this page -->
	<script src="<?php print $path_theme;?>assets/js/jvectormap/jquery-jvectormap-europe-merc-en.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.sparkline.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/raphael-min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/morris.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/toastr.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-chat.js"></script>
	<!-- JavaScripts initializations and stuff -->
	<script src="<?php print $path_theme;?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/additional-methods.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-validate-messages_es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-custom.js"></script>
	<script src="<?php print $path_theme;?>assets/js/simad-api.js?v=<?php echo(rand()); ?>"></script>
	<script src="<?php print $path_theme;?>assets/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script src="<?php print $path_theme;?>assets/js/select2/select2.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/select2/select2_locale_es.js"></script>
    <script src="<?php print $path_theme;?>assets/js/placeholders.jquery.min.js"></script>    
    <script src="<?php print $path_theme;?>assets/js/dataTables.bootstrap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/lodash.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/responsive/js/datatables.responsive.js"></script>
	<script src="<?php print $path_theme;?>assets/js/ckeditor/ckeditor.js"></script>
    <script src="<?php print $path_theme;?>assets/js/ckeditor/adapters/jquery.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.bootstrap-duallistbox.js"></script>

	<script type="text/javascript">
        jQuery(document).ready(function() 
		{
			var $formFields = jQuery('#form_focus input.form-control.input-sm:not([type="hidden"]):not([type="submit"]):not([type="button"]), #form_focus select');
			$formFields.each(function() {
				var $field = jQuery(this);
				if ( jQuery.isElementVisible($field)) {
					$field.focus();
					return false; // Salir del bucle
				}
			});

			jQuery('.chat-inner[tabindex]').each(function () {
				var t = parseInt(jQuery(this).attr('tabindex'), 10);
				if (!isNaN(t) && t > 0) {
					jQuery(this).attr('tabindex', '-1');
				}
			});

			var hasChatDom = jQuery('.chat-env, .chat-conversation, .chat-group').length > 0;
			if (!hasChatDom && window.neonChat) {
				// Evita que el módulo se ejecute fuera del chat
				['init','open','updateConversationOffset','updateScrollbars','scrollToBottom']
				.forEach(function (fn) {
					if (typeof neonChat[fn] === 'function') {
						neonChat[fn] = function(){ return; };
					}
				});

				// Por si ataron eventos globales
				jQuery(document).off('.neon.chat');
			}
        });
    </script>
</body>
</html>
