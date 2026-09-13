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
		        <div class="panel-title">Usuarios Webservice</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
              <!-- Opciones Listar -->
              <hr />
              <div class="row">
             	<div class="col-sm-12 form-group">                                    
                    <a target="_blank" class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/webservice/create">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle"/>Crear Nuevo
                    </a>                                        
                 </div>
              </div>
      	</div>
	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Usuarios Webservice</div>
		      </div>
		      <div class="panel-body with-table">		    
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
		            	<th>Usuario</th>
                        <th>Nombre</th>
                        <th>Direccion</th>
                        <th>Telefono</th>
                        <th>Email</th>
					</tr>
		          </thead>
		          <tbody>
		          	<?php
		          	$con = 1;
		            foreach ($pager->getResults() as $ws_usuarios):
		            ?>
                    <tr>
                        <td>
	            			<?php 
	            			    echo link_to($ws_usuarios->getUsuario(), $base_path.'/administracion.php/webservice/edit?wsusuarios_id='.$ws_usuarios->getPrimaryKey()); 
	            			?>
	            		</td>
                        <td><?php echo $ws_usuarios->getNombre(); ?></td>
                        <td><?php echo $ws_usuarios->getDireccion(); ?></td>
                        <td><?php echo $ws_usuarios->getTelefono(); ?></td>
                        <td><?php echo $ws_usuarios->getEmail(); ?></td>
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
                    <a target="_blank" class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/webservice/create">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle"/>Crear Nuevo
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
                    echo pager_navigation($pager, 'webservice/list', null);
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