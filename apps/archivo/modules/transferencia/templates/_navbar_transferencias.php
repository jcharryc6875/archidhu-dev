<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!-- NavBar Archivo -->
<nav class="navbar navbar-inverse" role="navigation">
	<div class="navbar-aurea">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="<?php print url_for('transferencia/list?localizacionunidaddocumental_id='.$localizacion);; ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>			
				<li><a href="<?php print url_for('transferencia/consultar?localizacionunidaddocumental_id='.$localizacion); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
				<li><a href="<?php print url_for('transferencia/list?localizacionunidaddocumental_id='.$localizacion.'&marcado=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Ver Marcados</a></li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>