<?php use_helper('jQuery')?>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalles De Item de Calificacion
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group"> 
                <?php 
                    echo link_to(image_tag('/images/simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','prov_check_list_vigencia/edit?prov_check_list_vigencia_id='.$prov_check_list_vigencia->getProvCheckListVigenciaId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Editar"));
                ?>
                </div>
            </div>
            
            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Prov Periodo de Validez:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_check_list_vigencia->getProvPeriodoValidez() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Pregunta:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_check_list_vigencia->getProvCheckListPregunta() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Descripcion:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_check_list_vigencia->getDescripcion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Respuesta:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_check_list_vigencia->getRespuesta() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Observaciones:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_check_list_vigencia->getObservaciones() ?></p></div>
					</div>
				</div>
            </div>
  	     </div>
	</div>
</div>
</form>