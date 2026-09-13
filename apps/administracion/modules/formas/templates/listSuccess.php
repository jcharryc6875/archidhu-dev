<?php
use_helper('Object','jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<div class="row">
	<div class="col-md-12">
        <?php
        // Include Navbar
        include_partial("navbar_panel",array('parametros'=>$parametros));
        ?>
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Privilegios</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>   
                          <th class="text-center" style="width: 10%;">Consecutivo</th>
                          <th class="text-center" style="width: 25%;">Nombre</th>
                          <th class="text-center">Descripci&oacute;n</th>
                          <th class="text-center" style="width: 10%;">Modulo</th>
                          <th class="text-center" style="width: 8%;">Es Publico</th>
                          <th class="text-center" style="width: 8%;">Opciones</th>  
                      </tr>
                  </thead>
                  <tbody>
                  <?php                         
                    foreach ($pager->getResults() as $forma):
                  ?>
                  
                  <tr>
                    <td class="text-center">
	                   <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/formas/show?forma_id=<?php echo $forma->getFormaId()?>','600','450'); return false;">
                            <?php echo sprintf("%05d",$forma->getFormaId())?>
                        </a>
                    </td>                    
                    <td><?php echo $forma->getNombre() ?></td>
                    <td><?php echo $forma->getDescripcion() ?></td>
                    <td><?php echo $forma->getModulo()->getDescripcion() ?></td>
                    <td class="text-center">
                        <div id="<?php echo md5($forma->getPrimaryKey()) ?>">
                          <?php if($forma->getIsPublic()){ ?>
                              <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_perm-unlocked.png',
                        		array('id'=>"feedcheck",'alt'=>'Este privilegio es publico','title'=>'Este privilegio es publico','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
                                array(
                            	'update'    => md5($forma->getPrimaryKey()),
                            	'url'     => 'formas/lockedPerm?forma_id='.$forma->getPrimaryKey(),		
                        		//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                        		'failure' => "alert('Ocurrio un error al bloquear el permiso, Por favor intente de nuevo!')",
                        		//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                             )) ?>
                             <?php }else{ ?>
                                <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/ico_perm-locked.png',
                        		array('id'=>"feeduncheck",'alt'=>'Este privilegio es privado','title'=>'Este privilegio es privado','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            	'update'    => md5($forma->getPrimaryKey()),
                            	'url'     => 'formas/lockedPerm?forma_id='.$forma->getPrimaryKey(),		
                        		//'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                        		'failure' => "alert('Ocurrio un error al desbloquear el permiso, Por favor intente de nuevo!')",
                        		//'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                             )) ?>
                         <?php } ?>
                        </div>
                    </td>
                    <td class="text-center">
                    <?php 
                    if($forma->getModuloId()==11)
                    {
                    ?>      
                    <a href="<?php echo $base_path.$forma->getRuta() ?>">
	                   <img alt="ir a" width="25" border="=0" title="Ir a" src="<?php echo $base_path; ?>/images/simad/ico_ira.png" />
	                </a>	  
                    <?php 
                    }else{ 
                        echo "&nbsp;";
                    }
                   ?>	  
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/formas/consulta"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" width="25" align="middle"/>Consultar</a>
                    
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/formas/create','600','450'); return false;">
                        <img border=0 src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle" />Crear Nueva
                    </a>
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/formas/excel?<?php echo $parametros?>"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_exportar.png" width="25" align="middle"/>Exportar</a>
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
            <!-- fin  paginacion -->
          </div>
        </div>
	</div>
</div>