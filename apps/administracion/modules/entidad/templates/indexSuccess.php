<?php
use_helper('Object','jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
	<div class="col-md-12">        
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Lista de sociedades</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>   
                          <th>Sociedad</th>
                          <th>Directorio name</th>
                          <th>Es actual</th>
                          <th>Codigo</th>
                          <th>Logo header</th>
                          <th>Logo licencia</th>  
                      </tr>
                  </thead>
                  <tbody>
                  <?php                         
                    foreach ($pager->getResults() as $entidad):
                  ?>
                  <tr>
                    <td class="text-center">
	                   <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/entidad/edit?entidad_id=<?php echo $entidad->getPrimaryKey()?>','640','480');">
                            <?php echo $entidad->getDescripcion(); ?>
                        </a>
                    </td>                    
                    <td><?php echo $entidad->getDirectorioName(); ?></td>
                    <td><?php echo$entidad->getEsActual(); ?></td>
                    <td><?php echo $entidad->getCodigo(); ?></td>
                    <td class="text-center">
                    <?php if(trim($entidad->getLogoHeader())){ ?>
                        <img src="<?php echo $base_path.$directorio_header.$entidad->getLogoHeader() ?>" style="width: 70%; height: 60px; border: solid 1px;" />
                    <?php }elseif(1!=1){ ?>
                        <img src="<?php echo $base_path.$directorio_header.'header_logo_default.jpg' ?>" style="width: 70%; height: 60px; border: solid 1px;" />
                    <?php } ?>
                    </td>
                    
                    <td class="text-center">
                    <?php if(trim($entidad->getLogoCorporativo())){ ?>
                        <img src="<?php echo $base_path.$directorio_header.'logos_carnet/'.$entidad->getLogoCorporativo() ?>" style="width: 40%; height: 30px; border: solid 1px;" />
                    <?php }else{ ?>
                        <img src="<?php echo $base_path.$directorio_header.'lincenciadoa.jpg' ?>" style="width: 70%; height: 60px; border: solid 1px;" />
                    <?php } ?>
                    </td>
                    
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
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/entidad/create','960','480');">
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
                    echo pager_navigation($pager, 'entidad/list', $parametros);
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