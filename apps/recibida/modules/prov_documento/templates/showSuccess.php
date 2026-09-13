<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalles Del Formato y Procedimiento
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">
                <?php 
                    echo link_to(image_tag('/images/simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','prov_documento/edit?prov_documento_id='.$prov_documento->getProvDocumentoId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Editar"));
                ?>
                <?php 
                    echo link_to(image_tag('/images/simad/ico_listar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Listar','prov_documento/index',array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Listar"));
                ?>                 
                </div>
            </div>
            
            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Prov Documento:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_documento->getProvDocumentoId() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Prov Periodo de Validez:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_documento->getProvPeriodoValidezId() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Prov Lista Docs:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_documento->getProvListaDocsId() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Prov Estado Docs:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_documento->getProvEstadoDocId() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha de Creacion:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_documento->getFechaCreacion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Observaciones:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_documento->getObservaciones() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Ruta:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_documento->getRuta() ?></p></div>
					</div>
				</div>
            </div>
  	     </div>
	</div>
</div>
</form>