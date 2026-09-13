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
        	        <div class="panel-title">Historial Formatos y Procedimiento</div>
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
		        <div class="panel-title">Historial Formatos y Procedimiento</div>
		      </div>
		      <div class="panel-body with-table">		      	
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
                      <th>Codigo</th>
                      <th>Usuario</th>
                      <th>Tipo</th>
                      <th>Unidad Administrativa</th>
                      <th>Nombre</th>
                      <th>Version</th>  
                      <th>Descripcion</th>
                      <th>Extension</th>
                      <th>Fecha Creacion</th>
                      <th>Ruta</th>      
                    </tr>
		          </thead>
		          <tbody>
                  <?php 
                    $con = 1;$x = 0;$temp = 0;
                    foreach ($pager->getResults() as $procedimiento):                    
                  ?>
                    <tr>
                      <td class="text-center"><?php echo link_to($procedimiento->getCodigo(),'formas/show?procedimiento_id='.$procedimiento->getProcedimientoId()) ?></td>
                      <td><?php echo $procedimiento->getUsuario()->getUserName() ?></td>
                      <td><?php echo $procedimiento->getTipoprocedimiento()->getDescripcion() ?></td>
                      <td><?php echo $procedimiento->getDependencia()->getNombre() ?></td>
                      <td><?php echo $procedimiento->getNombre() ?></td>
                      <td class="text-center"><?php echo $procedimiento->getVersion() ?></td>
                      <td><?php echo $procedimiento->getDescripcion() ?></td>
                      <td class="text-center"><?php echo $procedimiento->getExtension() ?></td>
                      <td><?php echo substr($procedimiento->getFechaCreacion(),0,10) ?></td>
                      <td><?php echo basename($procedimiento->getRuta())."&nbsp;" ?></td>      
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
                <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Regresar detalle del registro" href="<?php echo $base_path; ?>/procedimiento.php/formas/show?procedimiento_id=<?php echo $forma;?>">
                    <img src="<?php echo $base_path; ?>/images/simad/ico_devolver.png"  width="25" height="25" align="middle"/>Regresar
                </a>
	        	<?php
	        	// exportar
                echo link_to(image_tag('/images/simad/ico_exportar.png',array('border'=>"0",'width'=>"25",'align'=>"middle",)).'Exportar','formas/exportar?procedimiento_id='.$forma,array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Exportar El Historial"));
                //echo link_to(image_tag('/images/simad/ico_crear_nuevo.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Nueva Version','formas/crear?procedimiento_id='.$procedimiento->getPrimaryKey(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Crear Una Nueva Version De Este Formato"));
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
	                echo pager_navigation($pager, 'formas/list', $parametros);
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
  </div>
</div>