<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
  <div class="col-md-12">       
        <?php
		$cantidad_registros = $pager->getNbResults();
		if($cantidad_registros == 0):
		?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Documentos del workflow</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
          </div>
		  <!-- Opciones Listar -->
		  <hr />
		  <div class="row">
			<div class="col-sm-12 form-group">
				<a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Adicionar documento" href="<?php echo $base_path; ?>/administracion.php/wf_imagen_instancia/create?wfinstancia_id=<?php echo $wfinstancia_id; ?>">
					<img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" height="25" align="middle" />Nuevo Documento
				</a>
				
				<?php
				  // Devolver
				  echo link_to(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Regresar', 'wf_instancia_bitacora/list?instancia_id='.$wfinstancia_id, 
					array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Regresar bitacora del flujo', 'data-toggle' => 'tooltip'));
				?>
			</div>
		 </div>
        </div>        
        <?php
		else:
		?>
    <div class="panel panel-primary">
        <div class="panel-heading">
          <div class="panel-title">Anexos del flujo</div>
        </div>
        <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>
                          <th>Descripcion</th>
                          <th>Documento</th>
                          <th>Estado</th>
                          <th>Fecha creacion</th>
                          <th>Fecha documento</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                    foreach ($pager->getResults() as $wf_imagen_instancia):
                  ?>
                        <tr>
                            <td>
                                <a class="tooltip-primary" data-toggle="tooltip" data-original-title="Editar documento" 
                                  href="<?php echo $base_path; ?>/administracion.php/wf_imagen_instancia/edit?wfinstancia_id=<?php echo $wfinstancia_id; ?>&wf_imagen_instancia_id=<?php echo $wf_imagen_instancia->getWfImagenInstanciaId() ?>">
                                  <?php echo $wf_imagen_instancia->getDescripcion() ?>
                              </a>
                            </td>
                            <td class="text-center">
                                <p>
									<?php  
									$arr = preg_split("/[,]+/",$wf_imagen_instancia->getRuta(), -1, PREG_SPLIT_NO_EMPTY);
									foreach ($arr as $result):
										if(trim(basename($result))){
									?>
										<a class="tooltip-primary" data-toggle="tooltip" data-original-title="<?php echo basename($result) ?>" href="<?php echo ($result);?>" target="_blank">
											<img src="<?php echo $base_path; ?>/images/simad/ico-adj-file.png" width="25" height="25" align="middle" />                                    
										</a>
									<?php
										} 
									endforeach; 
									?>
								</p>
                            </td>
                            <td>
								<?php echo $wf_imagen_instancia->getWfEstadoImagen() ?>
                            </td>
                            <td>
                                <?php echo $wf_imagen_instancia->getFechaCreacion(); ?>
                            </td>
                            <td>
                                <?php echo $wf_imagen_instancia->getFechaDocumento();?>
                            </td>
                        </tr>
                   <?php endforeach; ?>
                   </tbody>
              </table>
              <!-- Opciones Listar -->
              <hr />
              <div class="row">
            	<div class="col-sm-12 form-group">
                    <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Adicionar documento" href="<?php echo $base_path; ?>/administracion.php/wf_imagen_instancia/create?wfinstancia_id=<?php echo $wfinstancia_id; ?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" height="25" align="middle" />Nuevo Documento
                    </a>
                    
					<?php
					  // Devolver
					  echo link_to(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Regresar', 'wf_instancia_bitacora/list?instancia_id='.$wfinstancia_id, 
						array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Regresar bitacora del flujo', 'data-toggle' => 'tooltip'));
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
                            echo pager_navigation($pager, 'wf_imagen_instancia/list', $filtros_consulta);
                        ?>
                      </div>
                    </div>
                </div>
             </div>
            <!-- fin  paginacion -->            
             </div>
        </div>
    <?php endif; ?>
  </div>
</div>