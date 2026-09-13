<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$uri = $_SERVER['REQUEST_URI'];
?>

<!-- NavBar Archivo -->
<nav class="navbar navbar-inverse" role="navigation">

	<div class="navbar-aurea">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<!-- ESTE ES EL BOTON TOGGLE EL QUE APARECE PARA CELULARES RESPONSIVE -->
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
				<span class="sr-only">CAMBIAR NAVEGACIÓN</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
			<ul class="nav navbar-nav">
				<li><a href="<?php print $base_path; ?>/servicios.php/servicio/list?periodo_id=<?php echo date("Y"); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
				<li><a href="<?php print url_for('servicio/create'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
				<li><a href="<?php print url_for('servicio/consulta'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
			</ul>

			<form  id="form_navbar" name="form_navbar" method="POST" action="<?php print $uri; ?>" class="navbar-form navbar-left" role="form">
				<div class="form-group">
					<div class="col-sm-4">
						<input type="text" id="detalle" name="detalle" class="form-control" placeholder="Detalle servicio" value="<?php echo $sf_params->get('detalle') ?>"/>
					</div>
				</div>
				<button type="submit" class="btn btn-blue btn-icon">Buscar<i class="entypo-search"></i></button>
			</form>
		</div>
		<!-- /.navbar-collapse --> 
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>