<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
?>

<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">

		<div class="panel-heading">
			<div class="panel-title">
			  Detalles de Vinculada
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Descripción</strong></p></div>
						<div class="col-sm-8"><p><?php echo $vinculada->getDescripcion(); ?></p></div>
					</div>
				</div>

				<?php
				if($vinculada->getRuta() != ""){
				?>
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Ruta</strong></p></div>
						<div class="col-sm-8"><p>
							<?php 
							$ruta = split(',',$vinculada->getRuta());
							$cant_registros = count($ruta);
							for($j=0; $j < $cant_registros; $j++){
								echo "<a href='".$ruta[$j]."' target='_blank'>".basename($ruta[$j])."</a> ";
							}			
						 	?>
						</p></div>
					</div>
				</div>
				<?php
				}
				?>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Creación</strong></p></div>
						<div class="col-sm-8"><p><?php echo $vinculada->getFechaCreacion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Folios</strong></p></div>
						<div class="col-sm-8"><p><?php echo $vinculada->getFolios(); ?></p></div>
					</div>
				</div>
			</div>

			<hr />
			<div class="col-sm-12 col-md-12">
				<div class="form-group">

					<?php
					// Boton Cerrar
					if($sf_params->get('opcion')){ 
						echo jq_link_to_function(image_tag('simad/ico_cerrar.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Cerrar Ventana')).'Cerrar','javascript:parent.jQuery.CloseModalSIMAD()', array('class' => 'btn btn-white btn-sm'));
					}else{
						echo link_to(image_tag('simad/ico_ir_tramite.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Listar Vinculadas')).'Listar','vinculada/list', array('class' => 'btn btn-white btn-sm'));	
					}
					?>

					<?php 
					if($vinculada->getAceptado() < 1){
						echo link_to(image_tag('simad/ico_editar.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Editar Vinculada')).'Editar', 'vinculada/edit?vinculada_id='.$vinculada->getVinculadaId(), array('class' => 'btn btn-white btn-sm'));
					}
					?>


				</div>
			</div>

		</div>

	</div>
	</div>
</div>