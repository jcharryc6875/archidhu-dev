<?php
$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!--meta charset="iso-8859-1"-->
	<!--meta charset="windows-1252"-->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="ARCHIDHU - Sistema de Gestión Documental de Archivos Electrónicos" />
	<meta name="author" content="PGD SAS" />

	<?php include_title(); ?>
    
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/font-icons/entypo/css/entypo.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/bootstrap.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-core.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-theme.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-forms.css"/>
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5"/>	
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/css/skins/facebook.css" rel="stylesheet"/>
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
			jQuery(".page-container").removeClass('sidebar-collapsed');
			jQuery(".page-container .sidebar-menu").remove();
			jQuery("footer").remove();
		}
	});
	</script>
</head>
<body class="page-body gray page-left-in" data-url="http://pgd.com.co">

<div class="page-container sidebar-collapsed"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
	
	<div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
                    <a href="<?php print $base_path; ?>/backend.php/resumen">
    					<img src="<?php print $path_theme;?>assets/images/simad/logo-app.png" width="120" alt="" />
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
                
			<div class="sidebar-user-info">
				<div class="sui-normal">
					<a href="#" class="user-link">
                        <?php
                            if($sf_user->isAuthenticated()){                                
                                //$usuario = UsuarioPeer::retrieveByPk($sf_user->getAttribute('usuario_id', '', 'subscriber'));
                                if(trim($sf_user->getAttribute('usuario_id', '', 'subscriber'))){                                    
                                    $ruta_foto = $sf_user->getAttribute('ruta_foto', '', 'subscriber');
                                }else{
                                    $ruta_foto = $base_path.'/images/usuario.jpg';
                                }
                                //****************************************************************************************
                                echo '<img src="'.$ruta_foto.'" alt="" class="img-circle" width="44" />'; 
                                echo '<strong>'.(($sf_user->getAttribute('nombre_apellido', '', 'subscriber'))).'</strong>';
                            }else{
                                echo "(indefinido)";
                            }
                        ?>
					</a>
				</div>				
			</div>
			
									
			<ul id="main-menu" class="main-menu">
				<?php include_once('menu-vertical.inc.php');?>
                <li>
        			<a href="<?php print $base_path;?>/backend.php/security/logout">
        				Salir <i class="entypo-logout right"></i>
        			</a>
        		</li>
			</ul>			            
                
		</div>

	</div>

	<div class="main-content">
        
		<?php echo $sf_content ?>

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
	<script src="<?php print $path_theme;?>assets/js/datatables/TableTools.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/zurb-responsive-tables/responsive-tables.js"></script>
	
	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap-datepicker.es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.inputmask.bundle.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/prefixfree.min.js"></script>
    <script src="<?php print $path_theme;?>assets/js/jquery.observe_field.js"></script>    
    <script src="<?php print $path_theme; ?>assets/js/icheck/icheck.min.js"></script>
    
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
	<script src="<?php print $path_theme;?>assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/lodash.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/responsive/js/datatables.responsive.js"></script>    
	<script src="<?php print $path_theme;?>assets/js/ckeditor/ckeditor.js"></script>
    <script src="<?php print $path_theme;?>assets/js/ckeditor/adapters/jquery.js"></script>
</body>
</html>