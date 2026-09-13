<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!-- NavBar Formas -->
<nav class="navbar navbar-default" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="#">Proveedores</a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li class="active"><a href="<?php print url_for('proveedor/list'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
			<li><a href="<?php print url_for('proveedor/create'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
			<li><a href="<?php print url_for('proveedor/consultageneral'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
		</ul>			
        <form  id="form1" name="form1" method="POST" action="<?php print url_for('proveedor/list'); ?>" class="navbar-form navbar-left" role="form">
			<div class="form-group">
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre proveedor" value="<?php echo $sf_params->get('nombre') ?>"/>
			</div>
			<button type="submit" class="btn btn-default">Buscar</button>
		</form>		
	</div>
	<!-- /.navbar-collapse -->
</nav>
<!-- Fin NavBar -->