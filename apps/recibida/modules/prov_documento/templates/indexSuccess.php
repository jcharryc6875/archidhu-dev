<?php use_helper('jQuery')?>
<?php use_helper('Object')?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Documentos del Proveedor</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>  
                          <th>Imagen</th>
                          <th>Consecutivo</th>
                          <th>Periodo Validez</th>
                          <th>Tipo Documento</th>
                          <th>Estado</th>
                          <th>Fecha Creacion</th>
                          <th>Fecha Recibido</th>
                          <th>Observaciones</th> 
                      </tr>
                  </thead>
                  <tbody>
                  <?php                   
                  $con = 1;$x = 0;$temp = 0;
                  foreach ($pager->getResults() as $prov_documento): 
                  ?>
                  <tr>
                      <td class="text-center">      
                        <?php  $arr = split(",",$prov_documento->getRuta());
                        $result=$arr[0];
                        ?>           			              			  			 	
                        <a href="<?php 	echo $result; ?> " target=_new><?php echo image_tag('/images/simad/ico_ver_adj.png',array('alt'=>''.'','border'=>"0",'width'=>"20",'height'=>"20",'align'=>"middle"));?></a>        
                       </td>
                      
                       <td><a href="<?php echo url_for('prov_documento/edit?prov_documento_id='.$prov_documento->getProvDocumentoId().'&prov_periodo_validez_id='.$prov_periodo_validez_id) ?>"><?php echo sprintf("%05d",$prov_documento->getProvDocumentoId()) ?></a></td>
                       <td><?php echo $prov_documento->getProvPeriodoValidez(); ?></td>
                       <td><?php echo $prov_documento->getProvListaDocs()->getDescripcion() ?></td>
                       <td><?php echo $prov_documento->getProvEstadoDoc()->getDescripcion() ?></td>
                       <td><?php echo $prov_documento->getFechaCreacion() ?></td>
                       <td><?php echo $prov_documento->getFechaRecibido() ?></td>
                       <td><?php echo $prov_documento->getObservaciones() ?></td>
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


                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo url_for('prov_documento/create?'.$parametros_consulta) ?>" data-toggle = "tooltip" data-original-title = "Adicionar Documento"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_adicionar.png" width="25" align="middle"/>Adicionar Documento</a>
                    
                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo url_for('prov_documento/excel?'.$parametros_consulta) ?>" data-toggle = "tooltip" data-original-title = "Exportar Documento"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_exportar.png" alt="Exportar Documento" width="25" align="middle"/>Exportar</a>

  
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
                    echo pager_navigation($pager, 'prov_documento/index', $parametros_consulta);
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