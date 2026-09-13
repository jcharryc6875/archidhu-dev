<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormRechazar    = "TRANSFERENCIA_RECHAZAR";
$currentFormAceptar   	= "TRANSFERENCIA_ACEPTAR";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
$marca_registro = false;

use_helper('jQuery');
?>

<div class="row">
	<div class="col-md-12">
	    <?php
        // Include Navbar Archivo
		include_once("_navbar_transferencias.php");
        //********************************************************************************
	    $cantidad_registros = $pager->getNbResults();
	    if($cantidad_registros == 0):
	    ?>
		<div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Lista de Transferencias</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
              
              <!-- Opciones Listar Transferencias -->
		        <div class="row">
		        	<div class="col-sm-12 form-group">
		        	     <a href="<?php print $base_path; ?>/archivo.php/transferencia/consultar?localizacionunidaddocumental_id=<?php echo $localizacion; ?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_consultar.png" alt="Exportar" width="25" align="middle" />Consultar</a>
		        	</div>
	        	</div>
      	</div>		

	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Lista de Transferencias</div>
		      </div>

		      <div class="panel-body with-table">

		      	<table class="table table-bordered table-hover table-striped responsive">
					<thead>
						<tr>
                            <th class="text-center" style="width: 5%" data-hide="phone">Marca</th>
							<th class="text-center" style="width: 8%" >Consecutivo</th>
							<th class="text-center" style="width: 8%">Imagen</th>
							<th class="text-center" style="width: 8%">Origen</th>
							<th class="text-center" style="width: 8%">Destino</th>
							<th class="text-center" style="width: 8%">Estado</th>
							<th>Unidad Documental</th>
							<th class="text-center" style="width: 15%">Fecha Creaci&oacute;n</th>
						</tr>
					</thead>

		          	<tbody>
	          			<?php
                        $con = 1;
		          		foreach ($pager->getResults() as $transferencia):
		            	?>
		          		<tr>
                            <td class="text-center">
        		              	<?php        		              	
        		              	$marca_user = false;      
        						if($transferencia->getMarca() == $currentUser){
        							$marca_user = true;
                                    $marca_registro = true;
        						}
        						echo checkbox_tag(sprintf("marca%02d", $con), $transferencia->getPrimaryKey(), $marca_user, 
        							array('onclick' => jq_remote_function(array('update'  => '', 'url' => 'transferencia/marcar?transferencia_id='.$transferencia->getPrimaryKey())))
        						);
        		              	?>
    						</td>
			          		<td class="text-center">
			          			<?php 
									echo jq_link_to_function(sprintf("%05d",$transferencia->getTransferenciaId()),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/transferencia/detalles?transferencia_id='.$transferencia->getTransferenciaId().'")'); 
			          			?>
			          		</td>
			          		<td class="text-center">
			          			<?php 
								switch($transferencia->getOrigentransferenciaid()){
									case 1:
										$document_digit = $transferencia->getComInterna()->getPathImageDigitByCom();
										if(!empty($document_digit) && file_exists($document_digit)){
											echo jq_link_to_remote(image_tag('simad/ico_ver_adj.png',
												array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
													'update'  => null,
													'url'     => url_for('/interna.php/com_interna/viewImageDigit'),
													'with'    => "'q_vars=".base64_encode($transferencia->getComInterna()->getPrimaryKey())."&vtoken=".md5($transferencia->getComInterna()->getRadicado().$currentUser.$transferencia->getComInterna()->getFechaCreacion())."'",
													'loading' => "javascript:jQuery.LoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
												),array('data-ndoc_text' => $transferencia->getComInterna()->getRadicado(),'data-qsource' => SED::encryption($transferencia->getComInterna()->getRadicado()),'data-qthumb' => SED::encryption($document_digit),'data-endpoint' => url_for('/interna.php/com_interna/imageThumbData'), 'data-original-title' => 'Documento digitalizado', 'class' => 'popover-toggle tooltip-primary gsthumbimg', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
											);
											echo "<br>";
											echo $transferencia->getComInterna()->getRadicado();
										}else{
											echo image_tag('/images/simad/ico_not_digital.png',array('class'=>'tooltip-primary','data-toggle'=>'tooltip' ,'data-original-title'=>'Sin imagen digital','border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle")); 
											echo "<br>";
											echo $transferencia->getComInterna()->getRadicado();
										}
									break;
									case 2:
										$document_digit = $transferencia->getComRecibida()->getPathImageDigitByCom();
										if(!empty($document_digit) && file_exists($document_digit)){
											echo jq_link_to_remote(image_tag('simad/ico_ver_adj.png',
												array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
													'update'  => null,
													'url'     => url_for('/recibida.php/com_recibida/viewImageDigit'),
													'with'    => "'q_vars=".base64_encode($transferencia->getComRecibida()->getPrimaryKey())."&vtoken=".md5($transferencia->getComRecibida()->getRadicado().$currentUser.$transferencia->getComRecibida()->getFechaCreacion())."'",
													'loading' => "javascript:jQuery.LoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
												),array('data-ndoc_text' => $transferencia->getComRecibida()->getRadicado(),'data-qsource' => SED::encryption($transferencia->getComRecibida()->getRadicado()),'data-qthumb' => SED::encryption($document_digit),'data-endpoint' => url_for('/recibida.php/com_recibida/imageThumbData'), 'data-original-title' => 'Documento digitalizado', 'class' => 'popover-toggle tooltip-primary gsthumbimg', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
											);
											echo "<br>";
											echo $transferencia->getComRecibida()->getRadicado();
										}else{
											echo image_tag('/images/simad/ico_not_digital.png',array('class'=>'tooltip-primary','data-toggle'=>'tooltip' ,'data-original-title'=>'Sin imagen digital','border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle")); 
											echo "<br>";
											echo $transferencia->getComRecibida()->getRadicado();
										}
									break;
									case 3:
										$document_digit = $transferencia->getComEnviada()->getPathImageDigitByCom();
										if(!empty($document_digit) && file_exists($document_digit)){
											echo jq_link_to_remote(image_tag('simad/ico_ver_adj.png',
												array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
													'update'  => null,
													'url'     => url_for('/enviada.php/com_enviada/viewImageDigit'),
													'with'    => "'q_vars=".base64_encode($transferencia->getComEnviada()->getPrimaryKey())."&vtoken=".md5($transferencia->getComEnviada()->getRadicado().$currentUser.$transferencia->getComEnviada()->getFechaCreacion())."'",
													'loading' => "javascript:jQuery.LoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
												),array('data-ndoc_text' => $transferencia->getComEnviada()->getRadicado(),'data-qsource' => SED::encryption($transferencia->getComEnviada()->getRadicado()),'data-qthumb' => SED::encryption($document_digit),'data-endpoint' => url_for('/enviada.php/com_enviada/imageThumbData'), 'data-original-title' => 'Documento digitalizado', 'class' => 'popover-toggle tooltip-primary gsthumbimg', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
											);
											echo "<br>";
											echo $transferencia->getComEnviada()->getRadicado();
										}else{
											echo image_tag('/images/simad/ico_not_digital.png',array('class'=>'tooltip-primary','data-toggle'=>'tooltip' ,'data-original-title'=>'Sin imagen digital','border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle")); 
											echo "<br>";
											echo $transferencia->getComEnviada()->getRadicado();
										}
									break;
									case 4:
										$document_digit = $transferencia->getVinculada()->getRuta();
										if(!empty($document_digit)){
											echo '<a target=_new href="'.$transferencia->getVinculada()->getRuta().'">';
												echo image_tag('simad/ico_ver_adj.png', array('border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle",'data-original-title'=>'Ver archivo adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
											echo '</a>';
										}else{
											echo image_tag('simad/ico_anular.png',array('data-original-title'=>'ningun archivo adjunto', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip', 'data-placement' => 'top','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle"));
										}
									break;
									case 8:
										$document_digit = $transferencia->getActoAdministrativo()->getPathImageDigitByCom();
										if(!empty($document_digit) && file_exists($document_digit)){
											echo jq_link_to_remote(image_tag('simad/ico_ver_adj.png',
												array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
													'update'  => null,
													'url'     => url_for('/comun.php/acto_administrativo/viewImageDigit'),
													'with'    => "'q_vars=".base64_encode($transferencia->getActoAdministrativo()->getPrimaryKey())."&vtoken=".md5($transferencia->getActoAdministrativo()->getNumeroResolucion().$currentUser.$transferencia->getActoAdministrativo()->getFechaCreacion())."'",
													'loading' => "javascript:jQuery.LoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData();",
													'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
												),array('data-ndoc_text' => $transferencia->getActoAdministrativo()->getRadicadoCompuesto(),'data-qsource' => SED::encryption($transferencia->getActoAdministrativo()->getRadicadoCompuesto()),'data-qthumb' => SED::encryption($document_digit),'data-endpoint' => url_for('/comun.php/acto_administrativo/imageThumbData'), 'data-original-title' => 'Documento digitalizado', 'class' => 'popover-toggle tooltip-primary gsthumbimg', 'data-toggle' => 'tooltip', 'data-placement' => 'top')
											);
											echo "<br>";
											echo $transferencia->getActoAdministrativo()->getRadicadoCompuesto();
										}else{
											echo image_tag('/images/simad/ico_not_digital.png',array('class'=>'tooltip-primary','data-toggle'=>'tooltip' ,'data-original-title'=>'Sin imagen digital','border'=>"0",'width'=>"25",'height'=>"25",'align'=>"middle")); 
											echo "<br>";
											echo $transferencia->getActoAdministrativo()->getRadicadoCompuesto();
										}
										break;
									default:
									echo $transferencia->getOrigentransferencia()->getDescripcion();
									break;
								}
								?>
			          		</td>
			          		<td class="text-center"><?php echo $transferencia->getOrigentransferencia()->getDescripcion(); ?></td>
			          		<td class="text-center"><?php echo $transferencia->getDestinotransferencia()->getDescripcion(); ?></td>
			          		<td class="text-center"><?php echo $transferencia->getEstadotransferencia()->getDescripcion(); ?></td>
			          		<td>
			          			<?php if($view_link_expediente){ ?>    
							    <?php echo jq_link_to_function($transferencia->getUnidaddocumental(),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/unidad_documental/show?unidaddocumental_id='.$transferencia->getUnidaddocumentalId().'")'); ?>
							    <?php }else{ ?>
							    <?php echo $transferencia->getUnidaddocumental().'&nbsp;' ?>
							    <?php } ?>
			          		</td>
			          		<td class="text-center"><?php echo $transferencia->getFechaCreacion(); ?></td>
		          		</tr>
		          		<?php
		          		endforeach;
		          		?>
	          		</tbody>
		        </table>

		        <!-- Opciones Listar Transferencias -->
		        <div class="row">
		        	<div class="col-sm-12 form-group">
		        	     <a href="<?php print $base_path; ?>/archivo.php/transferencia/excel?<?php echo $filtros_consulta;?>" class="btn btn-white btn-sm">
                            <img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar
                         </a>
                         
                         <a href="<?php print $base_path; ?>/archivo.php/transferencia/consultar?localizacionunidaddocumental_id=<?php echo $localizacion; ?>" class="btn btn-white btn-sm">
                            <img border=0 src="<?php print $base_path;?>/images/simad/ico_consultar.png" alt="Exportar" width="25" align="middle" />Consultar
                         </a>
                         
                         <?php 
                            if($sf_user->checkPerm($currentFormRechazar, $currentUser) && $marca_registro){
                                echo jq_link_to_function(image_tag('simad/ico_anular.png',
                        		array('id'=>"feedback",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")).'Rechazar Marcados',
                        		'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/transferencia/obsRechazarMarcados?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'&modulo='.$modulo.'","500","280")'
                                ,array('class'=>'btn btn-white btn-sm'));
                            } 
                          ?>
                          
                          <?php 
                            if($sf_user->checkPerm($currentFormAceptar, $currentUser) && $marca_registro){
                                echo jq_link_to_function(image_tag('simad/ico_accept_all.png',
                        		array('id'=>"feedback",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")).'Aceptar Marcados',
                        		'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/transferencia/AcceptAll?localizacionunidaddocumental_id='.$sf_params->get('localizacionunidaddocumental_id').'&modulo='.$modulo.'","500","280")'
                                ,array('class'=>'btn btn-white btn-sm'));
                            } 
                          ?>
		        	</div>
	        	</div>

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
		                echo pager_navigation($pager, 'transferencia/list', $filtros_consulta);
		                ?>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
	      	</div>
      	<?php
      	endif;
      	?>
  	</div>
</div>