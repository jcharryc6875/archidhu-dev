<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber') ;
use_helper('jQuery');
?>

<div class="panel-heading">
	<div class="panel-title">Lista De Registros Pendientes</div>
</div>
<div class="panel-body with-table">              
	<table class="table table-bordered table-hover table-striped responsive">
	  <thead>
		  <tr>                          
			  <th class="text-center">Radicado</th>
			  <th class="text-center">Asunto</th>
			  <th class="text-center">Fecha Radicaci&oacute;n</th>
			  <th class="text-center">REASIGNAR</th>
		  </tr>
	  </thead>
	  <tbody>
	  <?php                   
	  foreach ($pager->getResults() as $losRegistrosPendientes) : 
	  ?>
	  <tr>
		  <td class="text-center">
			<?php echo $losRegistrosPendientes->getRadicado(); ?>
		  </td>  
		  
		  <td class="text-center">
			<?php echo $losRegistrosPendientes->getAsunto(); ?>
		  </td>

		  <td class="text-center">
			<?php echo $losRegistrosPendientes->getFechaCreacion(); ?>
		  </td>

		  <td class="text-center">
			<?php 
				$enviadausuario = EnviadaUsuarioPeer::getCurrentUserAsig($losRegistrosPendientes->getComenviadaId());
				$tipoprocesocom_id = $enviadausuario->getTipoprocesocomId();
			?>
			<input class="btn btn-blue " value="REASIGNAR" type="button" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/usuario/reasignarUsuarioGestor?comenviada_id=<?php echo $losRegistrosPendientes->getComenviadaId(); ?>&tipoprocesocom_id=<?php echo $tipoprocesocom_id; ?>');"> 
		  </td>
	  </tr>
	  <?php	 
	  endforeach; 
	  ?>
	 </tbody>
	</table>
	<!-- Opciones Listar -->
	<hr />
	<!-- Paginador ASINCRONO -->
	<div class="dataTables_wrapper">
	  <div class="row">
		<div class="col-xs-6 col-left">
		  <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php  print $pager->getLastIndice(); ?> de <?php  print $pager->getNbResults();?></div>
		</div>
		<div class="col-xs-6 col-right">
		  <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
			<?php
				echo use_helper('Pagination');
				echo $paginacion_data = pager_navigation_async($pager, 'usuario/listarPendientesComEnviadaAsync', $parametros);
			?>
		  </div>
		</div>
	  </div>
	</div>
	<!-- Fin Paginador ASINCRONO -->
</div>






