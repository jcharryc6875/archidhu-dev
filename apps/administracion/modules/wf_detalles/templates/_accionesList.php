<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
?>
<div class="row">
	<div class="col-md-12">
    <!-- Contenedor Pagina -->
	<div class="panel panel-primary" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			   Lista Acciones a Seguir
			</div>
		</div>
      	<!-- Contenedor -->
		<div class="panel-body with-table">
            <!-- Listado -->
    		<table class="table table-bordered table-hover table-striped responsive">
    			<thead>
    				<tr>
                        <th>Consecutivo</th>
    					<th>Descripcion</th>
                        <th>...</th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php
                    $x = 0;
    				foreach ($wf_transiciones as $row): 
    				?> 
    				<tr>
                        <td class="text-center"><?php echo $row->getPrimaryKey(); ?></td>
    					<td><?php echo $row->getDescripcion(); ?></td>
                        <td class="text-center">
                            <?php 
                            echo jq_link_to_remote(image_tag('simad/ico_edit_property.png',
                        		array('id'=>'feedcheck','border'=>"0",'width'=>"20" ,'height'=>"20",'align'=>"middle")), array(
                            	'update'    => md5('divwftransicionesedit'),
                            	'url'     => $base_path.'/administracion.php/wf_detalles/editByAction',
                                'with' => "'wf_transicion_id=".$row->getPrimaryKey()."'",
                                'loading'  => "javascript:jQuery.LoadingStructData()",
                                'complete'  => "javascript:jQuery.CloseLoadingStructData()",
                                'script' => true,
                        		'failure' => "alert('Ocurrio un error realizando el proceso, Por favor intente de nuevo!')",
                              ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'Haga clic para editar esta acción a seguir'));
                            ?>
                        </td>
    				</tr>
    				<?php
    				endforeach;
    				?>				
    			</tbody>
    		  </table>
          </div>
	</div>
  </div>
</div>