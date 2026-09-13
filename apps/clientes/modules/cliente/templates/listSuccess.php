<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUserId= $sf_user->getAttribute('usuario_id','', 'subscriber');
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
            <div class="panel-title">Lista de Clientes</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Clientes</strong>, Intente con diferentes filtros de consulta.</div>
            </div>
        </div>
    

      <?php
      else:
      ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista de Clientes</div>
          </div>

          <div class="panel-body with-table">

            <table class="table table-bordered table-hover table-striped responsive">
              <thead>
                <tr>
                  <th>Marca</th>
                  <th>Codigo Cliente</th>
                  <th>Nombre</th>
                  <th>Serie</th>
                  <th>Subserie</th>
                  <th>Contenido</th>
                  <th>Fecha Apertura</th>
                </tr>
              </thead>

              <tbody>
              <?php
              $con = 1;
              foreach ($pager->getResults() as $cliente):
                echo input_hidden_tag(sprintf("rec%02d",$con),$cliente->getPrimaryKey());
              ?>
                <tr>
                  <td class="text-center">
                    <?php
                    $marca_user = false;
                    if($cliente->getMarca() == $currentUserId){
                       $marca_user = true;
                    }
                    echo checkbox_tag(sprintf("marca%02d",$c), $cliente->getPrimaryKey(),$marca_user,array('onclick'=>
                    jq_remote_function(
                      array('update'  => '', 'url' => 'cliente/marcar?cliente_id='.$cliente->getPrimaryKey())
                    )));
                    ?>
                  </td>
                  <td class="text-center"><?php echo jq_link_to_function($cliente->getCodigoCliente(),'javascript:jQuery.OpenModalSIMAD('.$base_path.'"/clientes.php/cliente/show?cliente_id='.$cliente->getClienteId().'")'); ?></td>
                  <td><?php echo $cliente->getNombreCliente(); ?></td>
                  <td><?php echo $cliente->getSubserie()->getSerie(); ?></td>
                  <td><?php echo $cliente->getSubserie(); ?></td>
                  <td><?php echo substr($cliente->getContenido(),0,20).'...'; ?></td>
                  <td class="text-center"><?php echo substr($cliente->getFechaApertura(),0,10); ?></td>
                </tr>
              <?php
              $con++;
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
                    echo pager_navigation($pager, 'cliente/list', $parametros);
                    ?>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Opciones Listar Unidad Documental -->
        <div class="row">
          <div class="col-sm-12 form-group">

            <?php echo jq_link_to_function(image_tag('simad/ico_check.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Marcar Todos','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/cliente/marcarTodos?'.$parametros.'&accion=1")', array('class' => 'btn btn-white btn-sm'));?>
            <?php echo jq_link_to_function(image_tag('simad/ico_uncheck.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Desmarcar Todos','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/clientes.php/cliente/marcarTodos?accion=0")', array('class' => 'btn btn-white btn-sm'));?>
            <?php echo link_to(image_tag('simad/ico_exportar.png',array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Exportar Clientes')).'Exportar','cliente/excel?'.$parametros, array('class' => 'btn btn-white btn-sm'));?>
          </div>
        </div>
    <?php
    endif;
    ?>
  </div>
</div>