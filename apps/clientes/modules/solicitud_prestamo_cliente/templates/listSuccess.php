<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormAdicionar     = "SOLICITUD_PRESTAMO_CLIENTES_ADICIONAR";
$currentUser			 = $sf_user->getAttribute('usuario_id', '', 'subscriber');
$cadSolicitud = $sf_user->getAttribute('solicitud_cliente','','prestamo_cliente');

use_helper('jQuery');
?>

<div class="row">
  <div class="col-md-12">

      <?php
      $cantidad_registros = $pager->getNbResults();
      if($cantidad_registros == 0):
      ?>
    <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista de Solicitudes Clientes</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Solicitudes de Clientes</strong>, Intente con diferentes filtros de consulta.</div>
            </div>
        </div>
    

      <?php
      else:
      ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista de Solicitudes Clientes</div>
          </div>

          <div class="panel-body with-table">

            <table class="table table-bordered table-hover table-striped responsive">
                <thead>
                  <tr>
                    <th>Numero Solicitud</th>
                    <th>Nombre Cliente</th>
                    <th>Código Cliente</th>
                    <th>Modulo</th>
                    <th>Subserie</th>
                    <th>Usuario Solicito</th>
                    <th>Fecha Solicitud</th>
                    <th>Folios</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                
                <tbody>
                  <?php
                  $x = 0;
                  foreach ($pager->getResults() as $solicitud_prestamo):
                  ?>
                  <tr>
                    <td class="text-center"><?php echo sprintf("%05d",$solicitud_prestamo->getClSolicitudprestamoId()) ?></td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getNombreCliente(); ?></td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getCodigoCliente(); ?></td>
                    <td>Clientes</td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getSubserie();?></td>
                    <td><?php echo $solicitud_prestamo->getUsuario()->getNombre().' '.$solicitud_prestamo->getUsuario()->getApellido(); ?></td>
                    <td><?php echo $solicitud_prestamo->getFechaCreacion(); ?></td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getFolios(); ?></td>
                    <td class="text-center"><?php echo $solicitud_prestamo->getClSolicitudprestamoestado()->getDescripcion(); ?></td>
                    <td>
                      <?php 
                      if($solicitud_prestamo->getClSolicitudprestamoestadoId() == 1 && substr_count($cadSolicitud,$solicitud_prestamo->getClSolicitudprestamoId()) == 0 ){
                        if($sf_user->checkPerm($currentFormAdicionar, $currentUser)){
                          if(!$estados[$x]){
                            echo jq_link_to_function(image_tag('simad/ico_adicionar.png',array('title'=>'Adicionar Solicitud Numero '.$solicitud_prestamo->getClSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/solicitud_prestamo_cliente/adicionar?clsolicitudprestamo_id='.$solicitud_prestamo->getClSolicitudprestamoId().'")');
                            echo jq_link_to_function(image_tag('simad/ico_anular.png',array('title'=>'Rechazar Solicitud Numero '.$solicitud_prestamo->getClSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/solicitud_prestamo_cliente/rechazar?clsolicitudprestamo_id='.$solicitud_prestamo->getClSolicitudprestamoId().'")');
                          }else{
                            echo jq_link_to_function(image_tag('simad/estados/prest_prestado.png',array('border'=>"0",'width'=>"20",'align'=>"middle",'title'=>'El Documento Se Encuentra Prestado')),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/solicitud_prestamo_cliente/show?opcion=1&cliente_id='.$solicitud_prestamo->getClienteId().'")');   
                          }
                        }else{
                          echo "&nbsp";
                        }    
                      }else{
                        echo image_tag('simad/ico_atendido.png',array('border'=>"0",'width'=>"20",'align'=>"middle",'title'=>'La Solicitud se Encuentra Atendida'));
                      }
                      ?>
                    </td>
                  </tr>
                  <?php
                  $x++;
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
                    echo pager_navigation($pager, 'solicitud_prestamo_cliente/list', $filtros_consulta);
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
              <a href="<?php echo $base_path; ?>/clientes.php/solicitud_prestamo_cliente/excel?<?php echo $filtros_consulta ?>" class="btn btn-white btn-sm"><img border=0 src="<?php print $base_path;?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle" />Exportar</a>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
</div>

<?php 
include_partial('list', array('pager' => $pagina, 'parametros' => $filtros_consulta));
?>