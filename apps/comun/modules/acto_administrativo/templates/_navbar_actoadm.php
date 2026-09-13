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
				<span class="sr-only">CAMBIAR NAVEGACIÓN</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
			<ul class="nav navbar-nav">
				<li><a href="<?php print $base_path; ?>/comun.php/acto_administrativo/list?porFunciEntrada=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Bandeja Entrada</a></li>
				<li><a href="<?php print $base_path; ?>/comun.php/acto_administrativo/list?estadoactoadministrativo_id=6&porFunciSalida=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Bandeja Por Leer</a></li>			
				<li><a href="<?php print url_for('acto_administrativo/create'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
				<li><a href="<?php print url_for('acto_administrativo/consulta'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
				<li><a href="<?php print $base_path; ?>/comun.php/acto_administrativo/list?marca=1"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Ver Marcados</a></li>
				<li><a href="<?php print $base_path; ?>/comun.php/acto_administrativo/list?periodo_id=<?php print date("Y"); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Ver Todos</a></li>
			</ul>
			<form  id="form1" name="form1" method="POST" action="<?php print $uri; ?>" class="navbar-form navbar-left" role="form">        
				<div class="form-group">
					<input type="text" id="asunto" name="asunto" class="form-control" placeholder="Asunto acto admininistrativo" value="<?php echo $sf_params->get('asunto') ?>"/>
				</div>
				<button type="submit" class="btn btn-default">Buscar</button>
			</form>
		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>