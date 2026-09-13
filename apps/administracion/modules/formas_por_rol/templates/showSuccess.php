<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormEditar       = "ADMIN_EDITAR_FORMAS_POR_ROL";
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
?>
<div class="row">
	<div class="col-md-12">
    
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalle de Privilegios por Perfil
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">                   
                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/edit?rolprivilegio_id=<?php echo $rol_privilegio->getRolprivilegioId() ?>"><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_editar.png" alt="Editar" width="25" />Editar</a>            
                    
                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/delete?rolprivilegio_id=<?php echo $rol_privilegio->getRolprivilegioId() ?>"><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_borrar.png" alt="Borrar" width="25" />Borrar</a>
                    
                </div>
            </div>            
            <hr />

            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
                <div class="row">
					<div class="col-xs-6">
						<div class="col-xs-8"><p><strong>Consecutivo: </strong></p></div>
						<div class="col-xs-2"><p><?php echo $rol_privilegio->getRolprivilegioId() ?></p></div>
					</div>

					<div class="col-xs-6">
						<div class="col-xs-5"><p><strong>Rol: </strong></p></div>
						<div class="col-xs-7"><p><?php echo $rol_privilegio->getRol()->getDescripcion() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-xs-6">
						<div class="col-xs-5"><p><strong>Forma: </strong></p></div>
						<div class="col-xs-7"><p><?php echo $rol_privilegio->getForma()->getDescripcion() ?></p></div>
					</div>
				</div>
            </div>
  	     </div>
	</div>
</div>
</div>
