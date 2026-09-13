<?php

$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
        <div class="panel-heading">
          <div class="panel-title">
            Lista Unidades Administrativas
          </div>
        </div>
        <!-- Contenedor Contenido Formulario-->
        <div class="panel-body">
          <?php
          // Si No existen Registros
          $cantidad_registros = $pager->getNbResults();
          if($cantidad_registros == 0):
          ?>
          <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
          <?php
          else:
          ?>
          <table class="table table-bordered table-hover table-striped responsive">
              <thead>
                <tr>
                  <th class="text-center" style="width: 5%;">ID</th>
                  <th class="text-center" style="width: 30%;">Nombre</th>
                  <th class="text-center" style="width: 10%;">C&oacute;digo / Sigla</th>
                  <th class="text-center" style="width: 10%;">Entidad</th>
                  <th class="text-center" style="width: 5%;">Tipo Tabla</th>
                  <th class="text-center" style="width: 5%;">Tiempo Prestamo</th>
                  <th class="text-center" style="width: 5%;">Versi&oacute;n</th>
                  <th class="text-center" style="width: 5%;">Esta Activa</th>
                  <th class="text-center" style="width: 10%;">...</th>
                </tr>
              </thead>
              <tbody>
              <?php
              foreach ($pager->getResults() as $dependencia):
              ?>
              <tr>
              <td class="text-center"><?php echo str_pad($dependencia->getDependenciaId(), 5, "0", STR_PAD_LEFT); ?></td>
              <td><?php echo $dependencia->getNombre(); ?></td>
              <td class="text-center"><?php echo $dependencia->getCodigo(); ?></td>
              <td><?php echo $dependencia->getEntidad(); ?></td>
              <td class="text-center"><?php echo $dependencia->getTipoTabla(); ?></td>
              <td class="text-center"><?php echo $dependencia->getTiempoPrestamo(); ?></td>
              <td class="text-center"><?php echo $dependencia->getVersionInst(); ?></td>
              <td class="text-center">
                <div id="<?php echo md5($dependencia->getPrimaryKey().$dependencia->getEntidadId()) ?>">
                  <?php 
                  if($dependencia->getEsActual()){
                    echo jq_link_to_remote(image_tag('simad/ico_check_green.png',array('id'=>"feedcheck",'alt'=>'Esta dependencia esta habilitada,clic para deshabilitarla','title'=>'Esta dependencia esta habilitada,clic para deshabilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), 
                      array(
                        	'update'  => md5($dependencia->getPrimaryKey().$dependencia->getEntidadId()),
                        	'url'     => 'dependencia/validate?dependencia_id='.$dependencia->getPrimaryKey(),
                    		  'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
                    		  //'success' => "toastr.success('La dependencia se deshabilito correctamente, desde ahora no se pueden crear registros con esta dependencia');"
                    ));
                  }else{
                    echo jq_link_to_remote(image_tag('simad/ico_check_red.png',
                    		array('id'=>"feedcheck",'alt'=>'Esta dependencia esta deshabilitada,clic para habilitarla','title'=>'Esta dependencia esta deshabilitada,clic para habilitarla','border'=>"0",'width'=>"25" ,'height'=>"25",'align'=>"middle")), array(
                          'update'  => md5($dependencia->getPrimaryKey().$dependencia->getEntidadId()),
                        	'url'     => 'dependencia/validate?dependencia_id='.$dependencia->getPrimaryKey(),
                    		  'failure' => "toastr.danger('Ocurrio un error realizando la actividad, Por favor intente de nuevo!');",
                    		  //'success' => "toastr.success('La dependencia se habilito correctamente, desde ahora se pueden crear registros con esta dependencia');"
                    ));
                  }
                  ?>
                </div>
              </td>
              <td class="text-center">
                <?php 
                if($procesos_id){ 
                  echo link_to(image_tag("/images/simad/ico_ira.png", array('border'=>"0",'width'=>"25",'align'=>"middle","title"=>'Ver Series',)), 'serie/list?dependencia_id='.$dependencia->getDependenciaId().'&oficinaproductora_id='.$dependencia->getOficinaproductoraId());
                }else{
                  echo link_to(image_tag("/images/simad/ico_ira.png", array('border'=>"0",'width'=>"25",'align'=>"middle","title"=>'Ver Series',)), 'serie/list?dependencia_id='.$dependencia->getDependenciaId());
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
                echo pager_navigation($pager, 'dependencia/list', $parametros);
                ?>
              </div>
            </div>
          </div>
        </div>

      <?php
      endif;
      ?>

      <!-- Opciones Listar Tablas de Retencion-->
      <div class="row">
        <div class="col-sm-12 form-group">
          <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/archivo.php/dependencia/consultar"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" width="25" align="middle"/>Consultar</a>
          <?php           
            echo link_to(image_tag('simad/ico_exportar.png',array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Exportar catalogo de disposicion documental')).'Exportar Unidades Administrativas','dependencia/excelDependencias?'.$parametros, array('class' => 'btn btn-sm btn-white'));
            echo link_to(image_tag('simad/ico_exportar.png',array('border'=>"0",'width'=>"25",'align'=>"middle", 'title'=>'Exportar TRD')).'Reporte TRD','dependencia/excel?'.$parametros, array('class' => 'btn btn-sm btn-white'));
          ?>

          <?php if ($sf_user->checkPerm("TRD_MIGRACION_MASIVA", $usuariologuiado)) { ?>
            <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Permite realizar la migracion masiva de una o varias TRDs" href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/archivo.php/dependencia/importarTrdBatch');">
              <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_batch_responder.png" width="25" height="25" align="middle" />Migraci&oacute;n Masiva TRD
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>