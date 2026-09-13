<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
?>
<div class="row">
  <div class="col-md-12">

    <?php
    // Include NavbarArchivo
    include_once("_navbar_archivo.php");
    ?>

      <?php
      $cantidad_registros = $pager->getNbResults();
      if($cantidad_registros == 0):
      ?>
    <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista Vinculadas - Archivo Gestión</div>
          </div>

          <div class="panel-body">
            <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
            </div>
        </div>
    

      <?php
      else:
      ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <div class="panel-title">Lista Vinculadas - Archivo Gestión</div>
          </div>

          <div class="panel-body with-table">

            <table class="table table-bordered table-hover table-striped responsive">
              <thead>
                <tr>
                  <th>Consecutivo</th>
                  <th>Descripción</th>
                  <th>Fecha Creación</th>
                  <th>Folios</th>
                  <th>Estado</th>
                </tr>
              </thead>

              <tbody>
                <?php
                foreach ($pager->getResults() as $vinculada):
                ?>
                <tr>
                  <td class="text-center"><?php echo jq_link_to_function(sprintf("%05d",$vinculada->getVinculadaId()),'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/vinculada/show?vinculada_id='.$vinculada->getVinculadaId().'&opcion=1")'); ?></td>
                  <td><?php echo $vinculada->getDescripcion(); ?></td>
                  <td class="text-center"><?php echo $vinculada->getFechaCreacion(); ?></td>
                  <td class="text-center"><?php echo $vinculada->getFolios(); ?></td>
                  <td class="text-center">
                    <?php 
                    if($vinculada->getAceptado() == 1){ 
                      echo "Aceptado";
                    }elseif($vinculada->getAceptado() == 2){
                      echo "Rechazado";
                    }else{
                      echo "Pendiente";
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
                    echo pager_navigation($pager, 'vinculada/list', $filtros_consulta);
                    ?>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      <?php
      endif;
      ?>

      <div class="row">
        <div class="col-sm-12 form-group">
          <?php echo link_to (image_tag('simad/ico_crear_nuevo.png', array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Crear Vinculada')).'Crear', 'vinculada/create', array('class' => 'btn btn-white btn-sm'));?>
          <?php echo link_to(image_tag('simad/ico_consultar_small.png', array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Consultar Vinculada')).'Consultar', 'vinculada/consultar', array('class' => 'btn btn-white btn-sm'));?>

          <?php
          if($cantidad_registros > 0){
            echo link_to(image_tag('simad/ico_exportar.png', array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Exportar Vinculaciones')).'Exportar Vinculaciones', 'vinculada/exportar?'.$filtros_consulta, array('class' => 'btn btn-white btn-sm'));
          }
          ?>
        </div>
      </div>
  </div>
</div>