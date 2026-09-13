<?php
$currentFormImprimir     = "CLIENTES_PRESTAMO_IMPRIMIR";
$currentUser			 = $sf_user->getAttribute('username', '', 'subscriber');
?>


<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">

		<div class="panel-heading">
			<div class="panel-title">
			  Detalles Del Prestamo
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">

				<div class="row">
					<div class="col-sm-4">
						<div class="col-sm-4"><p><strong>Consecutivo</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prestamo->getConsecutivoRegional(); ?></p></div>
					</div>
					<div class="col-sm-4">
						<div class="col-sm-5"><p><strong>Prestado a</strong></p></div>
						<div class="col-sm-7"><p><?php echo $nombSol?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="col-sm-4"><p><strong>Fecha Prestamo</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prestamo->getFechaPrestamo(); ?></p></div>
					</div>
					<div class="col-sm-4">
						<div class="col-sm-5"><p><strong>Fecha Vencimiento</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prestamo->getFechaVencimiento(); ?></p></div>
					</div>
					<div class="col-sm-4">
						<div class="col-sm-5"><p><strong>Fecha Devolución</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prestamo->getFechaDevolucion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="col-sm-4"><p><strong>Estado</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prestamo->getClEstadoPrestamo()->getDescripcion(); ?></p></div>
					</div>
					<div class="col-sm-4">
						<div class="col-sm-5"><p><strong>Atendido Por</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prestamo->getUsuario()->getNombre().' '.$prestamo->getUsuario()->getApellido(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="col-sm-4"><p><strong>Observaciones</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prestamo->getObservaciones(); ?></p></div>
					</div>
				</div>

			</div>

			<hr />
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">

					<?php
					echo jq_link_to_function(image_tag('simad/ico_cerrar.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'alt'=>'Cerrar Detalles Prestamo')).'Cerrar', 'javascript:parent.jQuery.CloseModalSIMAD()', array('class' => 'btn btn-white btn-sm'));
					
					if($sf_user->checkPerm($currentFormImprimir, $currentUser)){
						echo link_to(image_tag('simad/ico_imprimir.png', array('alt'=>'Imprimir Prestamo','border'=>"0",'width'=>"25",'align'=>"middle")).'Imprimir Prestamo','solicitud_prestamo_cliente/imprimir?clprestamo_id='.$prestamo->getClprestamoId().'&opcion=1', array('popup'=>'true', 'class' => 'btn btn-white btn-sm'));
					}
					?>


				</div>

			</div>
		</div>

	</div>
	</div>
</div>