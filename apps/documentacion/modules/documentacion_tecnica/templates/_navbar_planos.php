<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$uri = $_SERVER['REQUEST_URI'];
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
		<a class="navbar-brand" href="#">Planos</a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li class="active"><a href="<?php print $uri; ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
			<li><a href="<?php print url_for('planos/create'); ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
			<li><a href="<?php print url_for('documentacion_tecnica/consulta?tipo_documentacion_id=5'); ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
			<!-- Inhabilitada <li class="disabled"><a href="#">Link</a>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Opciones <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="#">Desmarcar Todos</a></li>
					<li><a href="#">Exportar</a></li>
					<li><a href="#">Something else here</a></li>
					<li class="divider"></li>
					<li><a href="#">Separated link</a></li>
					<li class="divider"></li>
					<li><a href="#">Imprimir Sticker</a>
					</li>-->
				</ul>
			</li>
		</ul>		
        <form  id="form1" name="form1" method="POST" action="<?php print $uri; ?>" class="navbar-form navbar-left" role="form">
			<div class="form-group">
				<input type="text" id="titulo" name="titulo" class="form-control" placeholder="Titulo" value="<?php echo $sf_params->get('titulo') ?>"/>
			</div>
			<button type="submit" class="btn btn-default">Buscar</button>
		</form>
		<!--ul class="nav navbar-nav navbar-right">
			<li><a href="#">Transferencias</a></li>
			<li><a href="#">Prestamos</a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="#">Action</a>
					</li>
					<li><a href="#">Another action</a>
					</li>
					<li><a href="#">Something else here</a>
					</li>
					<li class="divider"></li>
					<li><a href="#">Separated link</a>
					</li>
				</ul>
			</li>
		</ul-->
	</div>
	<!-- /.navbar-collapse -->
</nav>
<!-- Fin NavBar -->