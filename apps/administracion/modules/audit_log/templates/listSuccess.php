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
		        <div class="panel-title">Auditoria</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
              <!-- Opciones Listar -->
              <hr />
              <div class="row">
             	<div class="col-sm-12 form-group">                                    
                    <a target="_blank" class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/audit_log/excel?<?php echo $parametros?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_report.png" width="25" align="middle"/>Exportar
                    </a>
                        
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/audit_log/consulta">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle"/>Consultar
                    </a>                    
                 </div>
              </div>
      	</div>
	    <?php
	    else:
	    ?>
		    <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Auditoria</div>
		      </div>
		      <div class="panel-body with-table">		    
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
		            	<th class="text-center" style="width:10%">C&oacute;digo principal</th>
						<th class="text-center" style="width:10%">Modulo</th>
						<th class="text-center">Usuario</th>
						<th class="text-center" style="width:10%">Fecha</th>
						<th class="text-center" style="width:10%">Client Ip</th>
						<th class="text-center">Hash Log</th>
						<th class="text-center" style="width:10%">Tipo Operaci&oacute;n</th>
					</tr>
		          </thead>
		          <tbody>
		          	<?php
		          	$con = 1;
		            foreach ($pager->getResults() as $audit_log):
		            ?>
                    <tr>
                        <td>
	            			<?php 
	            			    echo jq_link_to_function($audit_log->getCodigoPrincipal() ? $audit_log->getCodigoPrincipal() : "Sin Codigo Principal", 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/administracion.php/audit_log/show?audit_log_id='.$audit_log->getPrimaryKey().'")'); 
	            			?>
	            		</td>
                        <td class="text-center"><?php echo $audit_log->getModulo()->getDescripcion(); ?></td>
                        <td><?php echo $audit_log->getUsuario()->getNombreAll() ?></td>
                        <td class="text-center"><?php echo $audit_log->getFechaCreacion() ?></td>
						<td class="text-center"><?php echo $audit_log->getClientIp() ?></td>
						<td><?php echo $audit_log->getHashLog() ?></td>
						<td class="text-center"><?php echo $audit_log->getTipoOperacion() ?></td>
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/audit_log/excel?<?php echo $parametros?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_report.png" width="25" align="middle"/>Exportar
                    </a>
                        
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/audit_log/consulta">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle"/>Consultar
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
                    echo pager_navigation($pager, 'audit_log/list',$parametros);
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