<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
use_helper('Object');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Lista de chequeo de Documentos del Proveedor</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>
                          <th>Pregunta</th>
                          <th>Prov periodo validez</th>
                          <th>Descripcion</th>
                          <th>Respuesta</th>
                          <th>Observaciones</th>
                          <th>Opciones</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach ($prov_check_list_vigenciaList as $prov_check_list_vigencia):
                  ?>
                  <tr>                      
                      <td class="text-center">                    
                        <?php echo jq_link_to_function($prov_check_list_vigencia->getProvCheckListPregunta(), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/recibida.php/prov_check_list_vigencia/edit?prov_check_list_vigencia_id='.$prov_check_list_vigencia->getProvCheckListVigenciaId().'", "960", "600")') ?>   
                      </td>                      
                      <td><?php echo $prov_check_list_vigencia->getProvPeriodoValidez() ?></td>
                      <td><?php echo $prov_check_list_vigencia->getDescripcion() ?></td>
                      <td><?php echo $prov_check_list_vigencia->getRespuesta() ?></td>
                      <td><?php echo $prov_check_list_vigencia->getObservaciones() ?></td>
                      <td><?php echo jq_link_to_function('Historial', 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/recibida.php/prov_check_list_vigencia/historico?prov_check_list_vigencia_id='.$prov_check_list_vigencia->getProvCheckListVigenciaId().'", "960", "600")') ?></td>
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

<?php if($sf_user->checkPerm("PROVEEDOR_ALERTAR_FIN_CHECK_LIST", $currentUser)){?>
<a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/recibida.php/prov_check_list_vigencia/alertacheckcompleto?prov_periodo_validez_id=<?php echo $prov_periodo_validez_id?>"><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_atendido.png" alt="Alertar sobre finalizacion de actividad" width="25" align="middle"/>
Alerta de fin de CheckList</a>
<?php }?>
                </div>
            </div>
          </div>
        </div>                      
	</div>
</div>