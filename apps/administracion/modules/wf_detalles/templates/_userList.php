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
			   Activar Alertas
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body with-table">
        <!-- Listado Tipos Documentales -->
		<table class="table table-bordered table-hover table-striped responsive">
			<thead>
				<tr>
                    <th>Usuario</th>
					<th>Enivar Alerta</th>
				</tr>
			</thead>
			<tbody>
				<?php
                $x = 0;
				foreach ($wf_actividadtransicion_usuarios as $row): 
				?> 
				<tr>
                    <td><?php echo $row->getUsuario()->getNombreAll(); ?></td>
					<td class="text-center">
                        <div id="<?php echo md5('wf_detalles/enviarAlerta?wfactividadtransicionusuario_id'.$row->getPrimaryKey()) ?>">
                          <?php if($row->getEnviarAlerta()){ ?>                
                              <?php echo jq_link_to_remote(image_tag('simad/bullet_green.png',
                        		array('id'=>"feedcheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                            	'update'    => md5('wf_detalles/enviarAlerta?wfactividadtransicionusuario_id'.$row->getPrimaryKey()),
                                'url'     => 'wf_detalles/enviarAlerta?wfactividadtransicionusuario_id='.$row->getPrimaryKey(),
                        		'loading'  => "javascript:jQuery.LoadingStructData()",
                                'complete'  => "javascript:jQuery.CloseLoadingStructData()",
                                'script' => true,
                        		'failure' => "alert('Ocurrio un error realizando el proceso, Por favor intente de nuevo!')",
                              ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'El envio de alertas para este usuario esta activo,clic para desactivar')) ?>
                              <?php }else{ ?>
                                <?php echo jq_link_to_remote(image_tag('simad/bullet_red.png',
                            		array('id'=>"feeduncheck",'border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                                	'update'    => md5('wf_detalles/enviarAlerta?wfactividadtransicionusuario_id'.$row->getPrimaryKey()),
                                    'url'     => 'wf_detalles/enviarAlerta?wfactividadtransicionusuario_id='.$row->getPrimaryKey(),
                            		'loading'  => "javascript:jQuery.LoadingStructData()",
                                    'complete'  => "javascript:jQuery.CloseLoadingStructData()",
                                    'script' => true,
                            		'failure' => "alert('Ocurrio un error realizando el proceso, Por favor intente de nuevo!')",
                                ),array('class'=>'tooltip-primary','data-toggle'=>'tooltip','data-original-title'=>'El envio de alertas para este usuario esta inactivo,clic para activar')) ?>
                          <?php } ?>
                        </div>
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