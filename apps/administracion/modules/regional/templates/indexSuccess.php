<?php
use_helper('Object','jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
	<div class="col-md-12">        
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Lista de Regionales</div>
	      </div>
	      <div class="panel-body with-table">
              <table class="table table-bordered table-hover table-striped responsive" id="table-1">
                  <thead>
                      <tr>   
                          <th>Codigo</th>                                                    
                          <th>Descripcion</th>
                          <th>Ciudad</th>                          
                          <th data-hide="phone">Direccion</th>
                          <th>Sociedad</th>
                          <th>Esta Activo</th>
                          <th>Membrete</th>  
                      </tr>
                  </thead>
                  <tbody>
                  <?php                         
                    foreach ($pager->getResults() as $regional):
                  ?>
                  <tr>
                    <td class="text-center">
	                   <a href="<?php echo $base_path; ?>/administracion.php/regional/edit?regional_id=<?php echo $regional->getPrimaryKey();?>">
                            <?php echo $regional->getPrimaryKey(); ?>
                        </a>
                    </td>                    
                    <td><?php echo $regional->getDescripcion(); ?></td>
                    <td><?php echo $regional->getCiudad(); ?></td>
                    <td><?php echo $regional->getDireccion(); ?></td>
                    <td><?php echo $regional->getEntidad(); ?></td>
                    <td class="text-center">
                        <div id="<?php echo md5($regional->getPrimaryKey()) ?>">
                          <?php if($regional->getEsVisible()){ ?>
                          <?php echo jq_link_to_remote(image_tag('simad/ico_check_green.png',
                    		array('id'=>"feedcheck",'alt'=>'La regional esta activa, haga clic para inactivar','title'=>'La regional esta activa, haga clic para inactivar','border'=>"0",'width'=>"35" ,'height'=>"35",'align'=>"middle")), array(
                        	'update'    => md5($regional->getPrimaryKey()),
                        	'url'     => 'regional/verificar?regional_id='.$regional->getPrimaryKey(),
                    		'failure' => "alert('Ocurrio un error durante el proceso, Por favor intente de nuevo!')",
                          )) ?>
                          <?php }else{ ?>
                            <?php echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
                    		array('id'=>"feeduncheck",'alt'=>'La regional esta inactiva, haga clic para activar','title'=>'La sociedad esta inactiva, haga clic para activar','border'=>"0",'width'=>"35" ,'height'=>"35",'align'=>"middle")), array(
                        	'update'    => md5($regional->getPrimaryKey()),
                        	'url'     => 'regional/verificar?regional_id='.$regional->getPrimaryKey(),
                    		'failure' => "alert('Ocurrio un error durante el proceso, Por favor intente de nuevo!')",
                          )) ?>
                          <?php } ?>
                        </div>
                     </td>
                     
                     <td class="text-center">
                        <?php if(trim($regional->getImageMembrete())){ ?>
                            <?php if(file_exists($filedir.trim($regional->getImageMembrete()))){ ?>
                                    <a href="<?php echo $base_path.$directorio_header.$regional->getImageMembrete(); ?>" target="_blank" title="clic para ver la imagen" alt="clic para ver la imagen" >
                                        <img src="<?php echo $base_path.$directorio_header.$regional->getImageMembrete(); ?>" style="width: 99%;" />
                                    </a>
                            <?php }else{ ?>
                                    <a href="<?php echo $base_path.$directorio_header.$regional->getImageMembrete(); ?>" target="_blank" title="clic para ver la imagen" alt="clic para ver la imagen" >
                                        <img src="<?php echo $base_path.$directorio_header.'1700x2200.png' ?>" style="width: 99%;" />
                                    </a>
                            <?php } ?>                            
                        <?php }else{ ?>
                                <a href="<?php echo $base_path.$directorio_header.'1700x2200.png' ?>" target="_blank" title="clic para ver la imagen" alt="clic para ver la imagen" >                                    
                                    <img border="0" src="<?php echo $base_path.$directorio_header.'1700x2200.png' ?>" style="width: 99%;" />
                                </a>                                
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/regional/create">
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
                    echo pager_navigation($pager, 'regional/index', $parametros);
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
<script type="text/javascript">
    var responsiveHelper;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };
    var tableContainer;
    jQuery(document).ready(function($)
    {
    	tableContainer = jQuery("#table-1");
    	
        /*tableContainer.DataTable( {
            "searching": false,
            "paging": false,
            "info": false,
            "ordering": true
        } );*/
                
        
    	tableContainer.dataTable({
    		"sPaginationType": "bootstrap",
    		//"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    		//"bStateSave": true,    		
            "searching": false,
            "paging": false,
            "info": false,
            "ordering": true,
            //"scrollY": "400px",
            
    	    // Responsive Settings
    	    bAutoWidth     : false,
    	    fnPreDrawCallback: function () {
    	        // Initialize the responsive datatables helper once.
    	        if (!responsiveHelper) {
    	            responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
    	        }
    	    },
    	    fnRowCallback  : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    	        responsiveHelper.createExpandIcon(nRow);
    	    },
    	    fnDrawCallback : function (oSettings) {
    	        responsiveHelper.respond();
    	    }
    	});
    	
    	/*jQuery(".dataTables_wrapper select").select2({
    		minimumResultsForSearch: -1
    	});*/
    });
</script>