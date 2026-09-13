<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!-- NavBar Archivo -->
<nav class="navbar navbar-default" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="#">Control Contrat&iacute;stas</a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">			
            <li><a href="<?php print url_for('control_contratistas/index'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
			<li><a href="<?php print url_for('control_contratistas/create'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
			<li><a href="<?php print url_for('control_contratistas/consultar'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
		</ul>
	</div>
	<!-- /.navbar-collapse -->       
</nav>
<!-- Fin NavBar -->