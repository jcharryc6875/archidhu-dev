<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*****************************************************************************************************
$id_modulo = $localizacion_object->getLocalizacionunidaddocumentalId();
//*****************************************************************************************************
switch($localizacion_object->getPrimaryKey())
{
    case 1:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion_object->getDescripcion()));
        break;
    case 2:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion_object->getDescripcion()));
        break;
    case 3:
        $nombre_modulo = ucfirst(mb_strtolower($localizacion_object->getDescripcion()));
        break;
}
//******************************************************************************************************
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
				<li><a href="<?php print url_for('unidad_documental/list?localizacionunidaddocumental_id='.$id_modulo);; ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list_black.svg" class="submenues" />Listar</a></li>
				<li><a href="<?php print url_for('unidad_documental/create?localizacion='.$id_modulo); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_black.svg" class="submenues" />Crear</a></li>
				<li><a href="<?php print url_for('unidad_documental/consultar?localizacion='.$id_modulo); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Consultar</a></li>
				<li><a href="<?php print url_for('unidad_documental/list?localizacionunidaddocumental_id='.$id_modulo.'&marcado=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_black.svg" class="submenues" />Ver Marcados</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="<?php print url_for('transferencia/consultar?localizacionunidaddocumental_id='.$id_modulo); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_transfer_black.svg" class="submenues" />Transferencias</a></li>
				<li><a href="<?php print url_for('prestamo/list?misPrestamos=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamo_black.svg" class="submenues" />Prestamos</a></li>
				<li><a href="<?php print url_for('solicitud_prestamo/list?misSolicitudes=1'); ?>"><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamo_black.svg" class="submenues" />Solicitudes Prestamo</a></li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
</nav>
<!-- Fin NavBar -->
<div style="width:100%; clear:both;"></div>
<?php
if($cantidad_registros)
{
    include_once("_filters_basic.php");
}
?>