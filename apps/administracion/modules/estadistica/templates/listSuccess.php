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
		        <div class="panel-title">Estadisticas</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
              <!-- Opciones Listar -->
              <hr />
              <div class="row">
             	<div class="col-sm-12 form-group">
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/estadistica/consulta">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle"/>Consultar
                    </a>
                    <a target="_blank" class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/estadistica/excel?<?php echo $parametros?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_report.png" width="25" align="middle"/>Exportar
                    </a>
                 </div>
              </div>
      	</div>
	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Estadisticas</div>
		      </div>
		      <div class="panel-body with-table">		    
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
		            	<th class="text-center" style="width:5%">Orden</th>
                        <th class="text-center" style="width:10%">Modulo</th>
                        <th class="text-center" style="width:20%">Nombre</th>
                        <th class="text-center" style="width:50%">Descripcion</th>
                        <th class="text-center" style="width:5%">Cantidad</th>
					</tr>
		          </thead>
		          <tbody>
		          	<?php
		          	$i = 0;
		            foreach ($pager->getResults() as $estadistica):
		            ?>
                    <tr>
                        <td class="text-center"><?php echo $estadistica->getOrden(); ?></td>
                        <td class="text-center"><?php echo $estadistica->getModulo() ?></td>
                        <td><?php echo $estadistica->getNombre() ?></td>
                        <td><?php echo $estadistica->getDescripcion() ?></td>
                        <td class="text-center"><?php echo $cantidad[$i];$i++; ?></td>
                    </tr>
                  <?php	 
                  endforeach; 
                  ?>
                 </tbody>
            </table>            
            <!-- Opciones Listar -->
            <hr />
            <div class="row">
             	<div class="col-sm-12 form-group">
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/estadistica/consulta">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle"/>Consultar
                    </a>
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/estadistica/excel?<?php echo $parametros?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_report.png" width="25" align="middle"/>Exportar
                    </a>
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
                    echo pager_navigation($pager, 'estadistica/list', $parametros);
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- fin  paginacion -->
          </div>
        </div>
        <?php
		endif;
		?>
	</div>
</div>