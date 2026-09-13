<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalle de Solicitud de Modificacion
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">
                <?php 
                    echo link_to(image_tag('/images/simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','prov_solicitud_modificacion/edit?prov_solicitud_modificacion_id='.$prov_solicitud_modificacion->getPrimaryKey(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Editar"));
                ?>
                     
                <?php
                if($prov_solicitud_modificacion->getProvEstadoSolModId()==1){ 
                    echo link_to(image_tag('/images/simad/ico_ejecutar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Respuesta','prov_solicitud_modificacion/respuesta?prov_solicitud_modificacion_id='.$prov_solicitud_modificacion->getPrimaryKey(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Responder solicitud de modificacion"));
                }
                ?> 
             
                </div>
            </div>
            
            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Proveedor:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_solicitud_modificacion->getProveedor() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Estado:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_solicitud_modificacion->getProvEstadoSolMod() ?></p></div>
				    </div>
                </div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Usuario:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_solicitud_modificacion->getUsuario() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Descripcion:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_solicitud_modificacion->getDescripcion() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Solicitud:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_solicitud_modificacion->getFechaCreacion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Respuesta:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_solicitud_modificacion->getRespuestaSolicitud() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Respuesta:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_solicitud_modificacion->getFechaRespuesta() ?></p></div>
					</div>
				</div>
            </div>
  	     </div>
	</div>
</div>