<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object', 'jQuery');
?>
<style>
	.nav-tabs {
		border-color: #508CC0;
		width: 100%;
	}

	.nav-tabs>li.active>a,
	.nav-tabs>li.active>a:focus,
	.nav-tabs>li.active>a:hover {
		background-color: #D6E6F3;
		color: #000;
		border: 1px solid #783320;
		border-bottom-color: transparent;
	}

	.nav-tabs>li>a:hover {
		background-color: #D6E6F3 !important;
		border-radius: 5px;
		color: #000;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<!-- Contenedor Pagina -->
		<div class="panel panel-gradient" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">
					Detalles del Usuario
				</div>
			</div>
			<!-- Contenedor Contenido Formulario-->
			<div class="panel-body">
				<!-- Opciones Detalle -->
				<div class="col-sm-12 col-md-12">
					<div class="form-group">
						<a href="<?php echo $base_path; ?>/administracion.php/usuario/edit?usuario_id=<?php echo $usuario->getUsuarioId() ?>" class="btn btn-white btn-sm tooltip-primary">
							<img border=0 src="<?php echo $base_path; ?>/images/simad/ico_editar.png" alt="Editar" width="25" align="middle" />Editar
						</a>
						<a class="btn btn-white btn-sm tooltip-primary" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('usuario/addSyncMail?usuario_id=' . $usuario->getUsuarioId()); ?>','640','400');">
							<img border="0" src="<?php echo $base_path; ?>/images/simad/ico_citacion_mail.png" alt="Cuenta de correo para sincronizar" width="25" align="middle" />EmailSync
						</a>
						<a class="btn btn-white btn-sm tooltip-primary" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('email_settings/list'); ?>','1280','640');">
							<img border="0" src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Configuracion de providers" width="25" align="middle" />Email Providers
						</a>
						<a href="<?php echo $base_path; ?>/administracion.php/usuario/historial?usuario_id=<?php echo $usuario->getUsuarioId() ?>" class="btn btn-white btn-sm tooltip-primary">
							<img border=0 src="<?php echo $base_path; ?>/images/simad/ico_historial.png" alt="Historial" width="25" align="middle" />Historial
						</a>
					</div>
				</div>
				<div class="tabs-vertical-env">
					<ul class="nav nav-tabs bordered"><!-- available classes "right-aligned" -->
						<li class="active"><a data-toggle="tab" href="#data"><span class="glyphicon glyphicon-import"></span>&nbsp;Detalles</a></li>
						<?php if ($usuario->getActivarAlertas()) { ?>
							<li><a data-toggle="tab" href="#configalertas"><span class="glyphicon glyphicon-list"></span>&nbsp;Configuraci&oacute;n Alertas</a></li>
						<?php } ?>
						<?php if ($usuario->getCargoId()) { ?>
							<li><a data-toggle="tab" href="#regPendientes"><span class="glyphicon glyphicon-list"></span>&nbsp;Registros Pendientes</a></li>
						<?php } ?>
					</ul>
					<div class="tab-content">
						<div id="data" class="tab-pane active">
							<?php echo include_partial("showInfo", array('usuario' => $usuario, 'objCargoUsuario' => $objCargoUsuario)); ?>
						</div>
						<!-- Configuración Alertas -->
						<?php if ($usuario->getActivarAlertas()) { ?>
							<div id="configalertas" class="tab-pane">
								<?php include_partial('configNotificacion', array('usuario' => $usuario)); ?>
							</div>
						<?php } ?>
						<!-- Registros Pendientes -->
						<?php if ($usuario->getCargoId()) { ?>
							<div id="regPendientes" class="tab-pane class-elvis">
								<?php
								$el_periodo_id = date("Y");
								include_partial('regPendientes', array('usuario' => $usuario, 'el_periodo_id' => $el_periodo_id));
								?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>