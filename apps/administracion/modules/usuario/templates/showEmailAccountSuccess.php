<?php 
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    use_helper('Object','jQuery');
?>

<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
				Detalles de la Cuenta Sincronizada
			</div>
		</div>
		<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="col-sm-4"><p><strong>Cuenta Correo: </strong></p></div>
                    <div class="col-sm-8"><p><?php echo $email_account->getAccountLogin() ?></p></div>
                </div>
                <div class="col-sm-6">
                    <div class="col-sm-5"><p><strong>Usuario: </strong></p></div>
                    <div class="col-sm-7"><?php echo $email_account->getUsuario()->getNombreApellido() ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="col-sm-4"><p><strong>Fecha Creaci&oacute;n: </strong></p></div>
                    <div class="col-sm-8"><p><?php echo $email_account->getFechaCreacion() ?></p></div>
                </div>
                <div class="col-sm-6">
                    <div class="col-sm-5"><p><strong>Fecha Modificaci&oacute;n: </strong></p></div>
                    <div class="col-sm-7"><?php echo $email_account->getFechaModificacion() ?></div>
                </div>
            </div>
        </div>
	</div>
</div>