<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormEditar    = "EDITAR_CLIENTE";
$currentFormTipos     = "VER_TIPOS_DOCUMENTALES_CLIENTES"; 
$currentFormSticker   = "IMPRIMIR_STICKER_CLIENTES";
$currentFormAnular    = "ANULAR_CLIENTES";
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
?>

<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">

		<div class="panel-heading">
			<div class="panel-title">
			  Detalles de Cliente
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">

			<!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Nombre Cliente</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getNombreCliente(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Estado Cliente</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getClienteestado()->getDescripcion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Codigo Cliente</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getCodigoCliente(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Frecuencia Consulta</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getFrecconsultacliente()->getDescripcion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Serie Documental</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getSubserie()->getSerie(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Subserie Documental</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getSubserie(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Responsable</strong></p></div>
						<div class="col-sm-8"><p><?php echo $responsable->getUsuario(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Inventariador</strong></p></div>
						<div class="col-sm-7"><p><?php echo $inventariador->getUsuario(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Unidad Conservación</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getUnidconservadora()->getDescripcion(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Soporte Cliente</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getSoporteCliente()->getDescripcion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Apertura</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getFechaApertura(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Cierre</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getFechaCierre(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Contenido</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getContenido(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Ubicación</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getUbicacion(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Folios</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getFolios(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Volúmen</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getVolumen(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Notas</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getNotas(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Vencimiento</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getFechaVencimiento(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Creación</strong></p></div>
						<div class="col-sm-8"><p><?php echo $cliente->getFechaCreacion(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Afiliación</strong></p></div>
						<div class="col-sm-7"><p><?php echo $cliente->getFechaAfiliacion(); ?></p></div>
					</div>
				</div>

			</div>
		
		</div>

		<hr />

		<!-- Opciones Detalle -->
		<div class="col-sm-12 col-md-12">
			<div class="form-group">

				<?php 
				echo jq_link_to_function(image_tag('simad/ico_cerrar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Cerrar','javascript:jQuery.CloseModalSIMAD()', array('class' => 'btn btn-white btn-sm'));
				?>

				<?php 
				if($detalle_prestamo == null ){
					if($contador == 0){
						echo link_to(image_tag('simad/estados/prest_solic.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Solicitar	Prestamo')).' Solicitar','solicitud_prestamo_cliente/update?cliente_id='.$cliente->getClienteId(), array('class' => 'btn btn-white btn-sm'));
					}else{
						echo image_tag('simad/ico_visualizar.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'El Documento ya se Encuentra en su lista de Solicitudes')).'Esta En Su Lista';	
					}       
				}else{
					echo jq_link_to_function(image_tag('simad/estados/prest_prestado.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'El Documento Se Encuentra Prestado')).' Prestado','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/solicitud_prestamo_cliente/show?cliente_id='.$cliente->getClienteId().'&opcion=1")', array('class' => 'btn btn-white btn-sm'));
				}
				?>

				<?php 
				if($sf_user->checkPerm($currentFormEditar, $currentUser)){
					echo link_to(image_tag('simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','cliente/edit?cliente_id='.$cliente->getClienteId(), array('class' => 'btn btn-white btn-sm'));
				}
				?>

				<?php
				if($sf_user->checkPerm($currentFormTipos, $currentUser)){
					echo link_to(image_tag('simad/ico_ver.png',array('border'=>"0",'width'=>"25",'align'=>"middle" , 'title'=>'ver Tipos Documentales')).'Tipos Documentales','cliente_contenido/list?cliente_id='.$cliente->getClienteId(), array('class' => 'btn btn-white btn-sm'));
				}
				?>

				<?php 
				if($sf_user->checkPerm($currentFormSticker, $currentUser)){
				?>
					<a href="#"  onclick="window.open('<?php echo $base_path; ?>/clientes.php/cliente/sticker?cliente_id=<?php echo $cliente->getClienteId() ?>', 'sticker', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=1,width=400,height=280,top=200');" class="btn btn-white btn-sm">
					<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_add_sticker.png" alt="Sticker" name="commit"  border="0" id="Login" width="25">Sticker</a>
				<?php
				}
				?>

				<?php 
				if($sf_user->checkPerm($currentFormAnular, $currentUser)){
					echo link_to(image_tag('simad/ico_anular.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Anular','cliente/anular?cliente_id='.$cliente->getClienteId(), array('class' => 'btn btn-white btn-sm'));
				}
				?>
			</div>
		</div>

	</div>
</div>
</div>