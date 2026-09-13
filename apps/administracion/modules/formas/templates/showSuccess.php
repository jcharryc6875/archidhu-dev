<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentFormEditar       = "ADMIN_EDITAR_FROMAS";
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
?>
<div class="row">
	<div class="col-md-12">    
    	<!-- Contenedor Pagina -->
    	<div class="panel panel-gradient" data-collapsed="0">
    		<div class="panel-heading">
    			<div class="panel-title">
    			  Detalle del Privilegio
    			</div>
    		</div>
    
          	<!-- Contenedor Contenido Formulario-->
    		<div class="panel-body">
                <!-- Opciones Detalle -->
    			<div class="col-sm-12 col-md-12">
    				<div class="form-group">
                        <?php ///if($sf_user->checkPerm($currentFormEditar, $currentUser)){ ?>
                            <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo $base_path; ?>/administracion.php/formas/edit?forma_id=<?php  echo $forma->getFormaId() ?>" 	>
                            <img border=0 src="<?php echo $base_path; ?>/images/simad/ico_editar.png" alt="Editar" width="25" />Editar</a>
                        <?php //} ?>
                    </div>
                </div>            
                <hr />
    
                <!-- Informacion Detalle -->
    			<div class="col-sm-12 col-md-12">
                    <div class="row">
    					<div class="col-xs-6">
    						<div class="col-xs-8"><p><strong>Consecutivo: </strong></p></div>
    						<div class="col-xs-2"><p><?php echo $forma->getPrimaryKey() ?></p></div>
    					</div>
    
    					<div class="col-xs-6">
    						<div class="col-xs-5"><p><strong>Modulo: </strong></p></div>
    						<div class="col-xs-7"><p><?php echo $forma->getModulo() ?></p></div>
    					</div>
    				</div>
                    
                    <div class="row">
    					<div class="col-xs-12">
    						<div class="col-xs-4"><p><strong>Nombre: </strong></p></div>
    						<div class="col-xs-8"><p><?php echo $forma->getNombre() ?></p></div>
    					</div>
    				</div>
    
                    <div class="row">
                        <div class="col-xs-12">
        					<div class="col-xs-4"><p><strong>Descripcion: </strong></p></div>
        					<div class="col-xs-8"><p><?php echo $forma->getDescripcion() ?></p></div>
        				</div>
                    </div>
                    
                    <div class="row">
    					<div class="col-xs-12">
    						<div class="col-xs-4"><p><strong>Es publico:</strong></p></div>
    						<div class="col-xs-8"><p><?php echo $forma->getIsPublic() ? 'Si' : 'No'; ?></p></div>
    					</div>					
    				</div>
                    
                    <div class="row">
    					<div class="col-xs-12">
    						<div class="col-xs-4"><p><strong>Ruta:</strong></p></div>
    						<div class="col-xs-8"><p><?php echo $forma->getRuta() ?></p></div>
    					</div>					
    				</div>
                </div>
      	     </div>
    	</div>
    </div>
</div>