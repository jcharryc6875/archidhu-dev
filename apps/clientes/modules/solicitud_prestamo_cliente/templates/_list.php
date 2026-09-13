<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentFormRadicar       = "SOLICITUD_PRESTAMO_CLIENTES_RADICAR";   
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
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
            <div class="panel-title">Solicitudes Aceptadas</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Solicitudes Aceptadas</strong>.</div>
            </div>
        </div>
    

      <?php
      else:
      ?>
      <?php echo form_tag('solicitud_prestamo_cliente/updateRadicar',array('name'=>'form1')); ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Solicitudes Aceptadas</div>
          </div>

          <div class="panel-body with-table">
            
            <table class="table table-bordered table-hover table-striped responsive">
                <thead>
                  <tr>
                    <th>Nombre Cliente</th>
                    <th>Modulo</th>
                    <th>Codigo Cliente</th>
                    <th>Ubicación</th>
                    <th>Usuario Solicitó</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                
                <tbody>
                  <?php
                  foreach ($pager->getResults() as $solicitud_prestamo):
                  ?>
                  <tr>
                    <td><?php echo $solicitud_prestamo->getCliente()->getNombreCliente(); ?></td>
                    <td>Clientes</td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getCodigoCliente(); ?></td>
                    <td><?php echo $solicitud_prestamo->getCliente()->getUbicacion(); ?></td>
                    <td><?php echo $solicitud_prestamo->getUsuario()->getNombre().' '.$solicitud_prestamo->getUsuario()->getApellido(); ?></td>
                    <td><?php echo $solicitud_prestamo->getClSolicitudprestamoestado()->getDescripcion() ?></td>
                    <td>
                      <?php echo jq_link_to_function(image_tag('simad/ico_anular.png', array('alt'=>'Anular Solicitud Numero '.$solicitud_prestamo->getClSolicitudprestamoId(),'border'=>"0",'width'=>"20",'align'=>"middle")),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/solicitud_prestamo_cliente/anular?clsolicitudprestamo_id='.$solicitud_prestamo->getClSolicitudprestamoId().'")');?>
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
                    echo pager_navigation($pager, 'solicitud_prestamo_cliente/list', $parametros);
                    ?>
                  </div>
                </div>
              </div>
            </div>

          
          </div>
          </div>


          <!-- Botones Radicar -->
            <div class="row">
              <div class="col-sm-12 form-group">

                <div class="form-group">
                  <!-- Botonera -->
                  <div class="col-sm-offset-3 col-sm-7">
                    <label for="observaciones" class="col-sm-1 control-label">Observaciones</label>
                    <?php echo textarea_tag('observaciones', '', array('class' => 'form-control input-sm')); ?>
                  </div>
                  </div>

                  <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-7"><br />
                    <button type="submit" class="btn btn-success">Radicar</button>
                  </div>
                </div>
                <div class="clear"></div>
               
              </div>
            </div>
        </form>
        <?php
        endif;
        ?>
    </div>
</div>