<?php
$currentFormDevolver     = "PRESTAMO_CLIENTE_DEVOLVER_DOCUMENTO";
$currentUser			 = $sf_user->getAttribute('username', '', 'subscriber');
$autorizado = $sf_user->checkPerm($currentFormDevolver, $currentUser);	 

use_helper('jQuery');
?>
<?php
include_partial('showPrestamo' ,array('prestamo' => $prestamo, 'parametros' => $parametros, 'nombSol' => $nombSol));
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
                    <th>Num. Solicitud</th>
                    <th>Nombre Cliente</th>
                    <th>Codigo Cliente</th>
                    <th>Serie</th>
                    <th>Subserie</th>
                    <th>Modulo</th>
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
                    <td><?php echo sprintf("%05d", $prestamo->getClsolicitudprestamoId()); ?></td>
                    <td><?php echo $prestamo->getClSolicitudPrestamo()->getCliente()->getNombreCliente();?></td>
                    <td><?php echo $prestamo->getClSolicitudprestamo()->getCliente()->getCodigoCliente(); ?></td>
                    <td><?php echo $prestamo->getClSolicitudprestamo()->getCliente()->getSubserie()->getSerie();?></td>
                    <td><?php echo $prestamo->getClSolicitudprestamo()->getCliente()->getSubserie(); ?></td>
                    <td>Clientes</td>
                    <td><?php echo $prestamo->getClSolicitudprestamo()->getUsuario(); ?></td>
                    <td><?php echo $prestamo->getClEstadoPrestamo()->getDescripcion(); ?></td>
                    <td>
                      <?php
                      if($autorizado){
                        if($opcion == 0){
                          if($prestamo->getClEstadoprestamoId() == 1){
                            echo link_to(image_tag('simad/ico_devolver_doc.png',array('alt'=>'Devolver Este Documento ','border'=>"0",'width'=>"25",'align'=>"middle")),'prestamo_cliente/updateDevolver?clsolicitudprestamo_id='.$prestamo->getClSolicitudprestamoId().'&clprestamo_id='.$prestamo->getClPrestamoId());
                            $temp = true;          
                         }else{
                            echo image_tag('simad/estados/prest_devuelto.png',array('alt'=>'Documento Devuelto','border'=>"0",'width'=>"25",'align'=>"middle"));
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
                      echo pager_navigation($pager, 'prestamo_cliente/show', $parametros);
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
              echo link_to(image_tag('simad/ico_devolver_todos.png', array('alt'=>'Devolver Todos Los Documentos Del Prestamo','border'=>"0",'width'=>"25",'align'=>"middle")).'Devolver Todos', 'prestamo_cliente/devolverTodos?clprestamo_id='.$prestamo->getClPrestamoId(), array('class' => 'btn btn-white btn-sm'));

              ?>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
</div>