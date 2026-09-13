<?php
$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
	
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<!--meta charset="gb18030"-->
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="ARCHIDHU - Sistema de Gestión Documental de Archivos Electrónicos" />
	<meta name="author" content="PGD SAS" />
    
    <?php include_title();?>
    
    <link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css"/>
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
    
    <script src="<?php print $path_theme;?>assets/js/jquery-1.11.0.min.js"></script>
    <script>jQuery.noConflict();</script>
</head>

<body class="page-body page-fade-only gray" data-url="http://pgd.com.co">
    <div class="page-container" >
        <div class="main-content">
            <?php print $sf_content;?>     
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
	<script src="<?php print $path_theme;?>assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/lodash.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/datatables/responsive/js/datatables.responsive.js"></script>
</body>
</html>