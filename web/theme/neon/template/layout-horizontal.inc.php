<?php
	$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
	$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
	use_helper('Object','jQuery');
	$usuario_id = $sf_user->getAttribute('usuario_id', '', 'subscriber');
	$view_notify = $sf_user->getAttribute('badge_view_notify', '', 'subscriber');

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
	<meta name="description" content="ARCHIDHU - Sistema de Gestión Documental de Archivos Electrónicos" />
	<meta name="author" content="PGD SAS" />

	<?php include_title(); ?>

	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/font-icons/entypo/css/entypo.css"/>	
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/bootstrap.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-core.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-theme.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-forms.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5"/>	
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/css/skins/yellow.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/dropzone/dropzone.css"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/select2/select2-bootstrap.css"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/select2/select2.css"/>
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/css/custom.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/zurb-responsive-tables/responsive-tables.css">
        
	<script src="<?php print $path_theme;?>assets/js/jquery-1.11.0.min.js"></script>        
	<script>jQuery.noConflict();</script>
	<script>
	jQuery(document).ready(function($) {
		// Ocultar Menu en Ventana Emergente
		if(window.top!=window.self){
			jQuery(".page-container").removeClass('horizontal-menu');
			jQuery(".page-container .navbar").remove();
			jQuery("footer").remove();
		}
	});
	</script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->


</head>
<body class="page-body page-fade-only gray" data-url="http://pgd.com.co">

<div class="page-container horizontal-menu">	
	<header class="navbar navbar-fixed-top"><!-- set fixed position by adding class "navbar-fixed-top" -->
		<div class="navbar-inner">
			<!-- logo -->
			<div class="navbar-brand">
				<a href="<?php print $base_path; ?>backend.php/resumen">
					<img class="img-circle" src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="88px" height="32px"/>
				</a>
			</div>			
			<!-- main menu -->		
			<ul class="navbar-nav">
				<?php include_once('menu.inc.php');?>
			</ul>
			<!-- notifications and other links -->
			<ul class="nav navbar-right pull-right">
				<!-- Profile Info --><!-- add class "pull-right" if you want to place this from right -->
				<!--li class="profile-info"-->
				<li class="dropdown user user-menu profile-info">
					<!--a href="#" style="height: 60px;margin-top: -10px;"-->
					<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
						<?php
							if($sf_user->isAuthenticated()){
								//$usuario = UsuarioPeer::retrieveByPk($usuario_id);
								if(trim($usuario_id)){                                    
									$ruta_foto = $sf_user->getAttribute('ruta_foto', '', 'subscriber');
								}else{
									$ruta_foto = $base_path.'/images/usuario.jpg';
								}
								//****************************************************************************************
								echo '<img src="'.$ruta_foto.'" class="user-image img-circle" width="32px"/>';
								echo '<span class="hidden-xs">'.(($sf_user->getAttribute('nombre_apellido', '', 'subscriber'))).'</span>';
							}else{
								echo "(indefinido)";
							}
						?>
					</a>
					<ul class="dropdown-menu user-menu profile-info">
						<!-- Reverse Caret -->
						<li class="caret"></li>
						<!-- Profile sub-links -->
						<li>
							<ul class="dropdown-menu-list scroller">
								<?php if($sf_user->getAttribute('config_advanced_firma', '', 'subscriber') == 1){ ?>
									<li id="uconfigmsing">
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigmsing',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmamecanica=1'",
											)) ?>">
											<?php if($sf_user->getAttribute('firma_mecanica', '', 'subscriber') == 0){ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												<span>Habilitar Firma Mec&aacute;nica</span>
											<?php }else{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												<span>Deshabilitar Firma Mec&aacute;nica</span>
											<?php } ?>
										</a>
									</li>
									<li id="uconfigesing">
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigesing',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmadigital=1'",
											)) ?>">
											<?php if($sf_user->getAttribute('firma_digital', '', 'subscriber') == 0){ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												<span>Habilitar Firma Digital</span>
											<?php }else{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												<span>Deshabilitar Firma Digital</span>
											<?php } ?>
										</a>
									</li>
									<li id="uconfigsingdes">
										<a href="#" onclick="<?php echo jq_remote_function(array(
												'update' => 'uconfigsingdes',
												'url' => url_for('/administracion.php/usuario/firmaStatus'),
												'with'    => "'usuario_id=".$usuario_id."&firmadesatendida=1'",
											)) ?>">
											<?php if($sf_user->getAttribute('firma_desatendida', '', 'subscriber') == 0){ ?>
												<i class="entypo-check" style="background-color: red;"></i>
												<span>Habilitar Firma Desatendida</span>
											<?php }else{ ?>
												<i class="entypo-check" style="background-color: green;"></i>
												<span>Deshabilitar Firma Desatendida</span>
											<?php } ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</li>
					</ul>
				</li>
                
				<li class="sep"></li>
				
				<li class="dropdown profile-info">					
					<a href="<?php print $base_path;?>backend.php/busqueda_avanzada">
						<i class="entypo-search"></i>
					</a>
				</li>
				
				<?php if($sf_user->getAttribute('badge_view_notify', '', 'subscriber')){ ?>
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
											'frequency' => 60,
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
				<?php } ?>
				
				<li class="sep"></li>

                <?php include_once('menu-secundario.inc.php'); ?>

				<li class="sep"></li>
				
				<li class="profile-info">
					<a href="<?php print $base_path;?>backend.php/security/logout">
						Salir <i class="entypo-logout right"></i>
					</a>
				</li>
				<!-- mobile only -->
				<li class="visible-xs">					
					<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
					<div class="horizontal-mobile-menu visible-xs">
						<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
							<i class="entypo-menu"></i>
						</a>
					</div>					
				</li>
			</ul>
		</div>
	</header>
    
	<div class="main-content">
		<?php echo $sf_content; ?>
		<!-- Footer -->
		<footer class="main" style="font-weight: bold!important;">            
			&copy;<?php echo date("Y"); ?> ARCHIDHU SGDEA - <?php echo "V".$application_current_version ?>
		</footer>
	</div>
</div>

	<!-- Bottom scripts (common) -->
	<script src="<?php print $path_theme;?>assets/js/gsap/main-gsap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/joinable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/resizeable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-api.js"></script>
    <script src="<?php print $path_theme;?>assets/js/jquery.dataTables.min.js"></script>
	<!--script src="<?php print $path_theme;?>assets/js/datatables/TableTools.min.js"></script-->
	<!--script src="<?php print $path_theme;?>assets/js/zurb-responsive-tables/responsive-tables.js"></script-->

	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.inputmask.bundle.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/prefixfree.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/jquery.observe_field.js"></script>    
    
    <!-- JavaScripts initializations and stuff -->
	<script src="<?php print $path_theme;?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/additional-methods.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-validate-messages_es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-custom.js"></script>
	<script src="<?php print $path_theme;?>assets/js/simad-api.js"></script>   
	<script src="<?php print $path_theme;?>assets/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script src="<?php print $path_theme;?>assets/js/select2/select2.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/select2/select2_locale_es.js"></script>
    <script src="<?php print $path_theme;?>assets/js/placeholders.jquery.min.js"></script>    
    
    <script src="<?php print $path_theme;?>assets/js/dataTables.bootstrap.js"></script>
	<!--script src="<?php print $path_theme;?>assets/js/datatables/jquery.dataTables.columnFilter.js"></script-->
	<script src="<?php print $path_theme;?>assets/js/datatables/lodash.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/responsive/js/datatables.responsive.js"></script>
	<script src="<?php print $path_theme;?>assets/js/ckeditor/ckeditor.js"></script>
    <script src="<?php print $path_theme;?>assets/js/ckeditor/adapters/jquery.js"></script>
</body>
</html>
