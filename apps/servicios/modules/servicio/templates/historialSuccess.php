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
        	        <div class="panel-title">Historial Solicitud De Servicio</div>
        	      </div>
        
        	      <div class="panel-body">
        	      	<div class="alert alert-default"><strong>No existen Registros</strong></div>
              	  </div>
          	</div>
        <?php
	    else:
	    ?>
            <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Historial Solicitud De Servicio</div>
		      </div>
		      <div class="panel-body with-table">		      	
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
                        <th>Solicitud</th>
                        <th>Numero Radicado</th>
                        <th>Usuario Asigno</th>
                        <th>Usuario Asignado</th>
                        <th>Detalle</th>  
                        <!--<th>Observaciones</th>-->
                        <th>Fecha</th>  
                        <th>Estado</th> 		            	
					</tr>
		          </thead>
		          <tbody>
                      <?php
                      $con = 1;
                      foreach ($pager->getResults() as $servicio):
                      ?>
                          <tr>
                            <td class="text-center"><?php echo $ser->getServicioId() ?></td>
                            <td class="text-center"><?php echo $ser->getRadicado() ?></td>
                            <td>
                                <?php 
                                if($servicio->getRolusuarioasignacionservicioId()=='1'):
                                    echo $servicio->getUsuario()->getNombre()." ".$servicio->getUsuario()->getApellido();
                                endif;        
                                ?>
                            </td>
                            <td><?php echo $nomb[$x]?></td>
                            <td><?php echo $ser->getDetalle() ?></td>
                            <!--<td class="<?php echo $fila?>" nowrap="true"><?php //echo $servicio->getAsignarServicio()->getObservaciones() ?></td>-->
                            <td><?php echo $ser->getFechaCreacion() ?></td>                  
                            <td><?php echo $ser->getServicioestado()->getDescripcion() ?></td>  
                          </tr>
                      <?php 
                      $x++;
                      endforeach; 
                      ?>
                  </tbody>
            </table>
            <hr />
            <!-- Opciones Listar Unidad Documental -->
	        <div class="row">
	        	<div class="col-sm-12 form-group">
                <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Regresar a la solicitud de servicio" href="<?php echo $base_path; ?>/servicios.php/servicio/show?servicio_id=<?php echo $ser->getServicioId();?>">
                    <img src="<?php echo $base_path; ?>/images/simad/ico_devolver.png"  width="25" height="25" align="middle"/>Regresar
                </a>
	        	<?php
	        	// Regresar
	        	//echo jq_link_to_function(image_tag('simad/ico_devolver.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Regresar')).'Regresar','javascript:openWindow("/archivo.php/unidad_documental/desmarcar","Desmarcar")', array('class' => 'btn btn-white btn-sm'));
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
	                echo pager_navigation($pager, 'servicio/list', $parametros);
	                ?>
	              </div>
	            </div>
	          </div>
	        </div>
        <?php
		endif;
		?>
     </div>
</div>