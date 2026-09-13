<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormRadicar     = "SOLICITUD_PRESTAMO_RADICAR";
$currentFormAdicionar   = "SOLICITUD_PRESTAMO_ADICIONAR";
$currentFormRechazar    = "SOLICITUD_PRESTAMO_RECHAZAR";
$currentUser			= $sf_user->getAttribute('usuario_id', '', 'subscriber');
$cadSolicitud = $sf_user->getAttribute('solicitud', '', 'prestamo');

$solicitud_adicionar = $sf_user->checkPerm($currentFormAdicionar, $currentUser);
$solicitud_rechazar = $sf_user->checkPerm($currentFormRechazar, $currentUser);

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
		        <div class="panel-title">Lista de Solicitudes</div>
		      </div>
		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Solicitudes</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
              <!-- Opciones Solicitudes Unidad Documental -->
              <div class="row">
	        	<div class="col-sm-12 form-group">                    
                    <?php echo link_to (image_tag('simad/ico_consultar_small.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Consultar', 'solicitud_prestamo/consulta', array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Consultar solicitudes de prestamo', 'data-toggle' => 'tooltip')); ?>
        		</div>
    		  </div>
      	</div>
	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Lista de Solicitudes</div>
		      </div>
		      <div class="panel-body with-table">
		      	<table class="table table-bordered table-hover table-striped responsive">
		          	<thead>
			            <tr>
			            	<th class="text-center" style="width: 5%">Numero Solicitud</th>
							<th class="text-center" style="width: 15%">Unidad Documental</th>
							<th class="text-center" style="width: 20%">Subserie</th>
							<th class="text-center" style="width: 10%">Usuario Solicito</th>
							<th class="text-center" style="width: 8%">Fecha Solicitud</th>
							<th class="text-center" style="width: 5%">Estado</th>
							<th class="text-center" style="width: 10%">Ubicación</th>
							<th class="text-center" style="width: 5%">...</th>
						</tr>
	          		</thead>
		          	<tbody>
		          		<?php
                        $x = 0;
		          		foreach ($pager->getResults() as $solicitud_prestamo):
		          		?>
		          		<tr>
		          			<td class="text-center"><?php echo sprintf("%05d",$solicitud_prestamo->getSolicitudprestamoId()); ?></td>
		          			<td><?php echo $solicitud_prestamo->getUnidaddocumental()->getCodigoBarras()." - ".$solicitud_prestamo->getUnidaddocumental()->getTitulo(); ?></td>
		          			<td><?php echo $solicitud_prestamo->getUnidaddocumental()->getSubserie(); ?></td>
		          			<td><?php echo $solicitud_prestamo->getUsuario()->getNombre().' '.$solicitud_prestamo->getUsuario()->getApellido(); ?></td>
		          			<td><?php echo $solicitud_prestamo->getFechaCreacion(); ?></td>
		          			<td class="text-center"><?php echo $solicitud_prestamo->getSolicitudprestamoestado()->getDescripcion(); ?></td>
		          			<td><?php echo $solicitud_prestamo->getUnidaddocumental()->getUbicacionEnCentral() ?></td>
		          			<td class="text-center">
		          				<?php 
		          				if($solicitud_prestamo->getSolicitudprestamoestadoId() == 1 && substr_count($cadSolicitud,$solicitud_prestamo->getSolicitudprestamoId()) == 0 ){
		          					if($solicitud_adicionar){
		          						if(!$estados[$x]){
		          							echo jq_link_to_function(image_tag('simad/ico_adicionar.png', array('title'=>'Adicionar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"25",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/adicionar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().'","380","280")');
                                            echo jq_link_to_function(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"25",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().'","380","280")');
                                            //echo link_to(image_tag('simad/ico_adicionar.png', array('title'=>'Adicionar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")), $base_path.'/archivo.php/solicitud_prestamo/adicionar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().$filtros_consulta);
		          							//echo link_to(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")), $base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().$filtros_consulta);
										}else{
											echo jq_link_to_function(image_tag('simad/estados/prest_prestado.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'El Documento se encuentra prestado')),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/show?opcion=1&unidaddocumental_id,'.$solicitud_prestamo->getUnidaddocumentalId().'","450","300")');
                                            echo jq_link_to_function(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"25",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().'","380","280")');		
											//echo link_to(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")), $base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().$filtros_consulta);
										}
									}elseif($solicitud_rechazar){
									 	//echo link_to(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")), $base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().$filtros_consulta);
                                        echo jq_link_to_function(image_tag('simad/ico_anular.png', array('title'=>'Rechazar solicitud numero '.$solicitud_prestamo->getSolicitudprestamoId(),'border'=>"0",'width'=>"25",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/solicitud_prestamo/rechazar?solicitudprestamo_id='.$solicitud_prestamo->getSolicitudprestamoId().$filtros_consulta.'","380","280")');
									}	  
								}else{		
									echo image_tag('simad/ico_atendido.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'La solicitud se encuentra atendida'));
								}
								?>

		          			</td>
	          			</tr>
	          			<?php
                        $x++;
	          			endforeach;
	          			?>
		          	</tbody>
	          	</table>
	          	<!-- Paginador -->
		        <div class="dataTables_wrapper">
		          <div class="row">
		            <div class="col-xs-6 col-left">
		              <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults();?></div>
		            </div>
		            <div class="col-xs-6 col-right">
		              <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
		                <?php
		                echo use_helper('Pagination');
		                echo pager_navigation($pager, 'solicitud_prestamo/list', $filtros_consulta);
		                ?>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
	      	</div>
	      	<!-- Opciones Solicitudes Unidad Documental -->
	        <div class="row">
	        	<div class="col-sm-12 form-group">
	        		<a href="<?php echo $base_path; ?>/archivo.php/solicitud_prestamo/excel?<?php echo $filtros_consulta ?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar</a>
                    <?php echo link_to (image_tag('simad/ico_consultar_small.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Consultar', 'solicitud_prestamo/consulta', array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Consultar solicitudes de prestamo', 'data-toggle' => 'tooltip')); ?>
        		</div>
    		</div>
      	<?php
      	endif;
      	?>
  	</div>
</div>
<?php 
if($sf_user->checkPerm($currentFormRadicar, $currentUser) && $pagina->getNbResults()){
	include_partial('list', array('pager' => $pagina, 'filtros_consulta' => $filtros_consulta));
}
?>
