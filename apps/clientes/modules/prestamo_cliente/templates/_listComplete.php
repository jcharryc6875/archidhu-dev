<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormMail         = "PRESTAMO_CLIENTE_ENVIAR_MAIL";
$currentUser			 = $sf_user->getAttribute('username', '', 'subscriber');
use_helper('jQuery');
?>


<div class="row">
	<div class="col-md-12">

	    <?php
	    $cantidad_registros = $pager->getNbResults();
	    if($cantidad_registros == 0):
	    ?>
		<div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Prestamos Clientes</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Prestamos en Clientes</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
      	</div>
	
	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Prestamos Clientes</div>
		      </div>

		      <div class="panel-body with-table">

		      	<table class="table table-bordered table-hover table-striped responsive">
		          	<thead>
			            <tr>
			            	<th>Consecutivo</th>
							<th>Prestado A</th>
							<th>Fecha Prestamo</th>
							<th>Fecha Vencimiento</th>
							<th>Fecha Devolución</th>
							<th>Atendido Por</th>
							<th>Estado</th>
						</tr>
	          		</thead>
		          	
		          	<tbody>
		          		<?php
		          		$i = 0;
		          		foreach ($pager->getResults() as $prestamo):
		          		?>
		          		<tr>
		          			<td class="text-center"><?php echo jq_link_to_function($prestamo->getConsecutivoRegional(), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/prestamo_cliente/show?clprestamo_id='.$prestamo->getClprestamoId().'")'); ?></td>
		          			<td><?php echo $nombSol[$i]; ?></td>
		          			<td><?php echo $prestamo->getFechaPrestamo(); ?></td>
		          			<td><?php echo $prestamo->getFechaVencimiento(); ?></td>
		          			<td>
		          				<?php if($prestamo->getFechaDevolucion()!= ''){
		          					echo $prestamo->getFechaDevolucion();
		          				}else{
									echo '0000-00-00';
								} 
								?>
							</td>
		          			<td><?php echo $prestamo->getUsuario()->getNombre().' '.$prestamo->getUsuario()->getApellido(); ?></td>
		          			<td><?php echo $prestamo->getClEstadoPrestamo()->getDescripcion(); ?></td>
	          			</tr>

	          			<?php
	          				$i++;
	          			endforeach;
	          			?>
          			</tbody>
      			</table>

  				</div>
			</div>

			<!-- Opciones Solicitudes Unidad Documental -->
	        <div class="row">
	        	<div class="col-sm-12 form-group">
	        		<a href="<?php echo $base_path; ?>/clientes.php/prestamo_cliente/excel?<?php echo $parametros ?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar</a>

	        		<?php 
	        		if($mail && $sf_user->checkPerm($currentFormAlerta, $currentUser)){
        				if($pager->getNbResults()){
							echo link_to(image_tag('simad/ico_env_mail.png', array('alt'=>'Enviar Mail De Alerta Para Prestamos Vencidos','border'=>"0",'width'=>"25",'align'=>"middle")).'Enviar Mail','prestamo_cliente/mail?vencidos='.date("Y-m-d"),array('popup'=>true, 'class' => 'btn btn-white btn-sm'));
						}
					}
					?>
        		</div>
    		</div>
		<?php
		endif;
		?>
	</div>
</div>