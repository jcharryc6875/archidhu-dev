<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
	<div class="col-md-12">        
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Perfiles por Usuario</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>   
                          <th>Consecutivo</th>
                          <th>Usuario</th>
                          <th>Perfil</th>  
                      </tr>
                  </thead>
                  <tbody>
                  <?php                         
                    foreach ($pager->getResults() as $rol_por_usuario): 
                  ?>
                  <tr>
                    <td class="text-center">
	                   <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/roles_por_usuario/show?rolporusuario_id=<?php echo $rol_por_usuario->getRolporusuarioId()?>','500','320'); return false;">
                            <?php echo sprintf("%05d",$rol_por_usuario->getRolporusuarioId())?>
                        </a>
                    </td>                    
                    <td><?php echo $rol_por_usuario->getUsuario()->getNombre()." ".$rol_por_usuario->getUsuario()->getApellido() ?></td>
                    <td><?php echo $rol_por_usuario->getRol()->getDescripcion() ?></td>
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
                    
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/roles_por_usuario/consulta"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" width="25" align="middle"/>Consultar</a>
                    
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/roles_por_usuario/create','500','300'); return false;">
                        <img border=0 src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle" />Crear Nuevo
                    </a>
                    
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/roles_por_usuario/excel?<?php echo $parametros?>"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_exportar.png" width="25" align="middle"/>Exportar</a>
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
                    echo pager_navigation($pager, 'roles_por_usuario/list', $parametros);
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- fin  paginacion -->
          </div>
        </div>
	</div>
</div>