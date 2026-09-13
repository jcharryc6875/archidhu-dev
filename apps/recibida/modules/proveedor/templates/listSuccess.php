<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object','jQuery');
?>

<div class="row">
	<div class="col-md-12">
        <?php
		// Include NavbarArchivo
		include_once("_navbar_proveedor.php");        
		?>
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Proveedor</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>  
                          <th class="text-center">Proveedor</th>
                          <th class="text-center">Nit</th>                          
                          <th class="text-center">Direcci&oacute;n</th>
                          <th class="text-center">Telefono</th>
                          <th class="text-center">Pais</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php                   
                  $con = 1;$x = 0;$temp = 0;
                  foreach ($pager->getResults() as $proveedor): 
                  ?>
                  <tr>
                      <td class="text-center">                      
                      <?php echo jq_link_to_function($proveedor->getNombre(), 'javascript:jQuery.OpenModalSIMAD("'.url_for('proveedor/show?proveedor_id='.$proveedor->getProveedorId()).'", "960", "640")'); ?>
                      </td>      
                      <td><?php echo $proveedor->getNit(); ?></td>
                      <td><?php echo trim($proveedor->getDireccion()) ? trim($proveedor->getDireccion()) : "&nbsp;"; ?></td>
                      <td><?php echo trim($proveedor->getTelefono()) ? trim($proveedor->getTelefono()) : "&nbsp;"; ?></td>
                      <td><?php echo $proveedor->getPaisId() ? $proveedor->getPais()->getNombre() : "&nbsp;"; ?></td>
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/recibida.php/proveedor/excelsinperiodos?<?php echo $parametros ?>"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle"/>Exportar</a>
                    
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/recibida.php/proveedor/excel?<?php echo $parametros ?>"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle"/>Exportar con periodos</a>
  
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
                    echo pager_navigation($pager, 'proveedor/list', $parametros);
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