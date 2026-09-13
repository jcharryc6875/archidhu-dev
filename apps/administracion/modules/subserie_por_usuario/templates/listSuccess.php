<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
	<div class="col-md-12">        
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Subseries por Usuario</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>
                          <th class="text-center" style="width: 5%;">#</th>   
                          <th class="text-center" style="width: 8%;">Consecutivo</th>
                          <th class="text-center" style="width: 20%;">Usuario</th>
                          <th class="text-center" style="width: 40%;">Subserie</th>
                          <th class="text-center" style="width: 8%;">Creaci&oacute;n</th>
                          <th class="text-center" style="width: 8%;">Visualizaci&oacute;n</th>
                          <th class="text-center" style="width: 8%;">Prestamo</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php                         
                    foreach ($pager->getResults() as $subserie_por_usuario): 
                  ?>                  
                  <tr>
                        <td class="text-center">
                            <?php                             
                                echo link_to(image_tag('/images/simad/ico_delete.png', array('border'=>"0",'width'=>"20",'align'=>"middle")),$base_path.'/administracion.php/subserie_por_usuario/delete?subserieporusuario_id='.$subserie_por_usuario->getSubserieporusuarioId(),array('class' => 'tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Eliminar permiso para esta subserie"));
                            ?>
                        </td>
                        
                        <td class="text-center">
                            <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/subserie_por_usuario/show?subserieporusuario_id=<?php echo $subserie_por_usuario->getSubserieporusuarioId()?>','500','320'); return false;"><?php echo sprintf("%05d",$subserie_por_usuario->getSubserieporusuarioId())?></a>
                        </td> 
                                           
                        <td><?php echo $subserie_por_usuario->getUsuario() ?></td>
                        <td><?php echo $subserie_por_usuario->getSubserie() ?></td>
                        <td class="text-center">
                        <div id="<?php echo md5('create'.$subserie_por_usuario->getPrimaryKey()) ?>">
                        <?php if($subserie_por_usuario->getCreacion()){ ?>
                            <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_green.png',
                            array('id'=>"feedcheck",'alt'=>'eliminar permiso','title'=>'eliminar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            'update'    => md5('create'.$subserie_por_usuario->getPrimaryKey()),
                            'url'     => 'subserie_por_usuario/createPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                            //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                            'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                            //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                            ),array('data-original-title'=>'eliminar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); ?>
                        <?php }else{ ?>
                            <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_red.png',
                            array('id'=>"feeduncheck",'alt'=>'asignar permiso','title'=>'asignar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            'update'    => md5('create'.$subserie_por_usuario->getPrimaryKey()),
                            'url'     => 'subserie_por_usuario/createPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                            //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                            'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                            //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                            ),array('data-original-title'=>'asignar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')) ?>
                        <?php } ?>
                        </div>
                        </td>
                        
                        <td class="<?php echo $fila?>" style="text-align: center;">
                        <div id="<?php echo md5('consulta'.$subserie_por_usuario->getPrimaryKey()) ?>">
                        <?php if($subserie_por_usuario->getVisualizacion()){ ?>
                            <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_green.png',
                            array('id'=>"feedcheck",'alt'=>'eliminar permiso','title'=>'eliminar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            'update'    => md5('consulta'.$subserie_por_usuario->getPrimaryKey()),
                            'url'     => 'subserie_por_usuario/consultaPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                            //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                            'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                            //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                            ),array('data-original-title'=>'eliminar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); ?>
                        <?php }else{ ?>
                            <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_red.png',
                            array('id'=>"feeduncheck",'alt'=>'asignar permiso','title'=>'asignar permiso','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            'update'    => md5('consulta'.$subserie_por_usuario->getPrimaryKey()),
                            'url'     => 'subserie_por_usuario/consultaPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                            //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                            'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                            //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                            ),array('data-original-title'=>'asignar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')) ?>
                        <?php } ?>
                        </div>
                        </td>
                        
                        <td class="<?php echo $fila?>" style="text-align: center;">
                        <div id="<?php echo md5('prestamo'.$subserie_por_usuario->getPrimaryKey()) ?>">
                        <?php if($subserie_por_usuario->getPrestamo()){ ?>
                            <?php echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_green.png',
                            array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            'update'    => md5('prestamo'.$subserie_por_usuario->getPrimaryKey()),
                            'url'     => 'subserie_por_usuario/prestamoPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                            //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                            'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                            //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                            ),array('data-original-title'=>'eliminar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); ?>
                        <?php }else{ ?>
                            <?php 
                            echo jq_link_to_remote(image_tag($base_path.'/images/simad/bullet_red.png',
                                array('id'=>"feeduncheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                                'update'    => md5('prestamo'.$subserie_por_usuario->getPrimaryKey()),
                                'url'     => 'subserie_por_usuario/prestamoPerm?subserieporusuario_id='.$subserie_por_usuario->getPrimaryKey(),		
                                //'loading' => "Element.show('indicator_fondo');Element.show('indicator');",
                                'failure' => "alert('Ocurrio un error al procesar la solicitud, Por favor intente de nuevo!')",
                                //'complete' => "Element.hide('indicator_fondo');Element.hide('indicator');",
                                ),array('data-original-title'=>'asignar permiso', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')); 
                            ?>
                        <?php } ?>
                        </div>
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/subserie_por_usuario/consulta">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" width="25" align="middle"/>Consultar
                    </a>
                
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/subserie_por_usuario/create');">
                        <img border=0 src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle" />Crear Nuevo
                    </a>

  
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/subserie_por_usuario/deleteAll?<?php echo $parametros?>','500','300');">
                        <img border=0 src="<?php echo $base_path; ?>/images/simad/ico_anular.png" width="25" align="middle" />Eliminar Permisos
                    </a>

                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/subserie_por_usuario/excel?<?php echo $parametros?>">
                        <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_exportar.png" width="25" align="middle"/>Exportar
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
                        echo pager_navigation($pager, 'subserie_por_usuario/list', $parametros);
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