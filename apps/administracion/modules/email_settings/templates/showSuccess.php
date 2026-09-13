<?php 
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    use_helper('Object','jQuery');
?>

<div class="row">
	<div class="col-md-12">
    <?php if ($sf_user->hasFlash('message_success')): ?>
        <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('message_success') ?></div>
    <?php endif ?>
    <?php if ($sf_user->hasFlash('message_error')): ?>
        <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('message_error') ?></div>
    <?php endif ?>
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
				Detalles Email Settings
			</div>
		</div>
		<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <!-- Informacion Detalle -->
            <div class="col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><p><strong>Provider: </strong></p></div>
                        <div class="col-sm-8"><p><?php echo $email_settings->getEmailProvider() ?></p></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-5"><p><strong>ServerName/Host: </strong></p></div>
                        <div class="col-sm-7"><?php echo $email_settings->getServerName() ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><p><strong>Puerto Servidor: </strong></p></div>
                        <div class="col-sm-8"><p><?php echo $email_settings->getListenPuerto() ?></p></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-5"><p><strong>Tipo Seguridad: </strong></p></div>
                        <div class="col-sm-7"><?php echo $email_settings->getTypeEncryption() ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><p><strong>Tenant: </strong></p></div>
                        <div class="col-sm-8"><p><?php echo $email_settings->getTenantName() ?></p></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-5"><p><strong>Client: </strong></p></div>
                        <div class="col-sm-7"><?php echo $email_settings->getOauth2Client() ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><p><strong>Redirect URI: </strong></p></div>
                        <div class="col-sm-8"><p><?php echo $email_settings->getOauth2RedirectUri() ?></p></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-5"><p><strong>Secret Client: </strong></p></div>
                        <div class="col-sm-7"><?php echo $email_settings->getOauth2Secret() ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><p><strong>Fecha Creaci&oacute;n: </strong></p></div>
                        <div class="col-sm-8"><p><?php echo $email_settings->getFechaCreacion() ?></p></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-5"><p><strong>Fecha Modificaci&oacute;n: </strong></p></div>
                        <div class="col-sm-7"><?php echo $email_settings->getFechaModificacion() ?></div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>