<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$uri = $_SERVER['REQUEST_URI'];
?>

<!-- NavBar Interna -->
<nav class="navbar navbar-inverse" role="navigation">

	<div class="navbar-aurea">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<!-- ESTE ES EL BOTON TOGGLE EL QUE APARECE PARA CELULARES RESPONSIVE -->
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
				<span class="sr-only">&nbsp;</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<!-- <a class="navbar-brand" href="#">MENU GRIS</a> -->
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
			<ul class="nav navbar-nav">
				<li class=""><a href="<?php print $base_path; ?>/enviada.php/com_enviada/list?porFunciSalida=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Bandeja de Salida</a></li>
				<li><a href="<?php print $base_path; ?>/enviada.php/com_enviada/list?porFunciCopia=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Copias Informativas</a></li>
				<li><a href="<?php print url_for('com_enviada/create'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
				<li><a href="<?php print url_for('com_enviada/consulta'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
				<li><a href="<?php print $base_path; ?>/enviada.php/com_enviada/list?marca=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Ver Marcados</a></li>
			</ul>			
			<form  id="form1" name="form1" method="POST" action="<?php print url_for('com_enviada/list'); ?>" class="navbar-form navbar-left" role="form">
				<div class="form-group">
					<input type="text" id="radicado" name="radicado" class="form-control" placeholder="radicado" value="<?php echo $sf_params->get('radicado') ?>"/>
				</div>
				<button type="submit" class="btn btn-success">Buscar</button>
			</form>	
		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>