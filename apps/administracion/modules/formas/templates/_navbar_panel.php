<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$uri = $_SERVER['REQUEST_URI'];
?>
<!-- NavBar  -->
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
			<li><a href="<?php print url_for('formas/consulta?modulo_id=11&panelControl=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>			
			<li><a href="<?php print url_for('formas/list?modulo_id=11&panelControl=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar Todos</a></li>
		</ul>
			<form id="navbar-frm" class="navbar-form navbar-left" role="search" method="POST">
				<input type="hidden" value="11" id="modulo_id" name="modulo_id"/>
				<input type="hidden" value="1" id="panelControl" name="panelControl"/>
				<div class="form-group">
					<div class="input-group">
						<input type="text" id="nombre" name="nombre" class="form-control" placeholder="nombre del submodulo" value="<?php echo isset($_POST['nombre']) ? $_POST['nombre'] : ""; ?>"/>
						<div class="input-group-btn">
							<button type="submit" class="btn btn-success">Buscar</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>
