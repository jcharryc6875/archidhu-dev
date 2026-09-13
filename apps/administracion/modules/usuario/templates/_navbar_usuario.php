<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$uri = $_SERVER['REQUEST_URI'];
?>
<!-- NavBar Formas -->
<nav class="navbar navbar-inverse" role="navigation">

	<div class="navbar-aurea">		

		<!-- Collect the nav links, forms, and other content for toggling -->
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
			<ul class="nav navbar-nav">
				<li><a href="<?php print url_for('usuario/list'); ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
				<li><a href="<?php print url_for('usuario/create'); ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
				<li><a href="<?php print url_for('usuario/consulta'); ?>"><img src="<?php echo $base_path; ?>/theme/neon/assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
			</ul>

			<form  id="form1" name="form1" method="POST" action="<?php print url_for('usuario/list'); ?>" class="navbar-form navbar-left" role="form">
				<div class="form-group">
					<input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo isset($_POST['nombre']) ? $_POST['nombre'] : ""; ?>"/>
					<button type="submit" class="btn btn-success">Buscar</button>
				</div>
			</form>	

		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>