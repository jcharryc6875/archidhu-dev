<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

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
		        <div class="panel-title">Prestamos</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Prestamos</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
      	</div>
		

	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Prestamos</div>
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
		          			<td class="text-center"><?php echo jq_link_to_function($prestamo->getConsecutivoRegional(), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/prestamo/show?prestamo_id='.$prestamo->getPrestamoId().'")'); ?></td>
		          			<td><?php echo utf8_encode($nombSol[$i]); ?></td>
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
		          			<td><?php echo $prestamo->getEstadoprestamo()->getDescripcion(); ?></td>
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
	        		<a href="<?php echo $base_path; ?>/archivo.php/prestamo/excel?<?php echo $parametros ?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar</a>
        		</div>
    		</div>
		<?php
		endif;
		?>
	</div>
</div>