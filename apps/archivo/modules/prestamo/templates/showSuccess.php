<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormDevolver     = "PRESTAMO_DEVOLVER_DOCUMENTO";
$currentFormRestaurarPrestado     = "RESTAURAR_PRESTAMO_DOCUMENTO";
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
$autorizado  = $sf_user->checkPerm($currentFormDevolver, $currentUser);	 
$restaurarPrestamo = $sf_user->checkPerm($currentFormRestaurarPrestado, $currentUser);

use_helper('jQuery');

?>
<?php 
include_partial('showPrestamo', array('prestamo'=>$prestamo, 'parametros' => $parametros, 'nombSol' => $nombSol));
?>

<div class="row">
  <div class="col-md-12">
      <?php
      $cantidad_registros = $pager->getNbResults();
      if($cantidad_registros == 0):
      ?>
    <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista de Archivos Prestados</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Documentos Prestados</strong>.</div>
            </div>
    </div>
      <?php
      else:
      ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista de Archivos Prestados</div>
          </div>

          <div class="panel-body with-table">
            <table class="table table-bordered table-hover table-striped responsive">
                <thead>
                  <tr>
                    <th>Archivo</th>
                    <th>Subserie</th>
                    <th>Modulo</th>
                    <th>Ubicación</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                      
                <tbody>
                  <?php
                  foreach ($pager->getResults() as $prestamo):
                  ?>
                  <tr>
                    <td><?php echo $prestamo->getSolicitudprestamo()->getUnidaddocumental();?></td>
                    <td><?php echo $prestamo->getSolicitudprestamo()->getUnidaddocumental()->getSubserie();?></td>
                    <td><?php echo $prestamo->getSolicitudprestamo()->getUnidaddocumental()->getLocalizacionunidaddocumental(); ?></td>
                    <td>
                      <?php 
                      $ubica = $prestamo->getSolicitudprestamo()->getUnidaddocumental()->getLocalizacionunidaddocumentalId();
                      if($ubica == 2){ 
                        echo $prestamo->getSolicitudprestamo()->getUnidaddocumental()->getUbicacionencentral();
                      }else{
                        echo $prestamo->getSolicitudprestamo()->getUnidaddocumental()->getUbicacionencentral();  
                      } 
                      ?>
                    </td>
                    <td><?php echo $prestamo->getSolicitudprestamo()->getUsuario()->getUserName(); ?></td>
                    <td><?php echo $prestamo->getEstadoprestamo()->getDescripcion(); ?></td>
                    <td class="text-center">
                      <?php
                          if($autorizado)
                          {
                            if($opcion == 0)
                            {
                              if($prestamo->getEstadoprestamoId() == 1)
                              {
                                echo link_to(image_tag('/images/simad/ico_devolver_doc.png',array('border'=>"0",'width'=>"25",'align'=>"middle")),'prestamo/updateDevolver?page='.$paginaActual.'&solicitudprestamo_id='.$prestamo->getSolicitudprestamoId().'&prestamo_id='.$prestamo->getPrestamoId(),array('class'=>'tooltip-primary', 'data-toggle'=>'tooltip', 'data-original-title'=>'Devolver este espediente'));
                                $temp = true;          
                              }else{
                                echo image_tag('/images/simad/estados/prest_devuelto.png',array('border'=>"0",'width'=>"25",'align'=>"middle",'class'=>'tooltip-primary', 'data-toggle'=>'tooltip', 'data-original-title'=>'El prestamo ya fue devuelto'));
                                if($restaurarPrestamo){
                                  echo link_to(image_tag('/images/simad/ico_restaurar_doc.png',array('border'=>"0",'width'=>"25",'align'=>"middle")),'prestamo/updateRestaurarPrestamo?page='.$paginaActual.'&solicitudprestamo_id='.$prestamo->getSolicitudprestamoId().'&prestamo_id='.$prestamo->getPrestamoId(),array('class'=>'tooltip-primary', 'data-toggle'=>'tooltip', 'data-original-title'=>'Restaurar el prestamo de este expediente'));
                                }       
                              }
                            }
                          }
                      ?>
                    </td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

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
                      echo pager_navigation($pager, 'prestamo/show', $parametros);
                      ?>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Opciones Solicitudes Unidad Documental -->
          <div class="row">
            <div class="col-sm-12 form-group">
              <?php
                if($prestamo->getEstadoprestamoId() == 1)
                {
                    echo link_to(image_tag('simad/ico_devolver_todos.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Devolver Todos','prestamo/devolverTodos?prestamo_id='.$prestamo->getPrestamoId(), array('class' => 'btn btn-white btn-sm tooltip-primary', 'data-toggle'=>'tooltip', 'data-original-title'=>'Devolver todos los documentos del prestamo'));
                }
              ?>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
</div>