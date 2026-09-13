<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object', 'jQuery');
?>
<div class="row">
  <div class="col-md-12">
    <?php
    $cantidad_registros = $pager->getNbResults();
    if ($cantidad_registros == 0) :
    ?>
      <div class="panel panel-primary">
        <div class="panel-heading">
          <div class="panel-title">Auditoria (Nueva Tabla)</div>
        </div>

        <div class="panel-body">
          <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
        </div>
        <!-- Opciones Listar -->
        <hr />
        <div class="row">
          <div class="col-sm-12 form-group">
            <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/consulta">
              <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle" />Consultar
            </a>
          </div>
        </div>
      </div>
    <?php
    else :
    ?>
      <div class="panel panel-primary">
        <div class="panel-heading">
          <div class="panel-title">Auditoria (Nueva Tabla)</div>
        </div>
        <div class="panel-body with-table">
          <table class="table table-bordered table-hover table-striped responsive">
            <thead>
              <tr>
                <th class="text-center">Usuario</th>
                <th class="text-center">Modulo</th>
                <th class="text-center">Fecha Creación</th>
                <th class="text-center">IP Origen</th>
                <th class="text-center">Accion</th>
                <th class="text-center">Hash Data</th>
                <th class="text-center">Url Origen</th>
              </tr>
            </thead> 
            <tbody>
              <?php
              $con = 1;
              foreach ($pager->getResults() as $auditoria) :
              ?>
                <tr>
                  <td class="text-center">
                    <?php
/*                      
echo jq_link_to_function($acceso_log->getPrimaryKey() ? str_pad($acceso_log->getPrimaryKey(),10,"0",STR_PAD_LEFT) : 
"Sin Codigo Principal", 'javascript:jQuery.OpenModalSIMAD("' . 
$base_path . '/administracion.php/acceso_log/show?accesolog_id=' . $acceso_log->getPrimaryKey() . '")');
*/
                            echo $auditoria->getUsuario();

                            /* CAMPO ORIGINAL: $auditoria->getUsuarioId() 
                            METODO MAGICO PARA TRAER EL NOMBRE DEL USUARIO: $auditoria->getUsuario()//*/
                    ?>
                  </td>
                  <td><?php echo $auditoria->getModulo(); ?></td>
                  <td><?php echo $auditoria->getFechaCreacion("Y-m-d G:i:s"); ?></td>
                  <td><?php echo $auditoria->getIpOrigen(); ?></td>
                  <td><?php echo $auditoria->getAccion(); ?></td>
                  <td><?php echo $auditoria->getHashData(); ?></td>
                  <td><?php echo $auditoria->getUrlOrigen(); ?></td>
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
              <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/excelAuditoria?<?php echo $parametros ?>">
                <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_report.png" width="25" align="middle" />Exportar
              </a>

              <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/consultaAuditoria">
                <img border="0" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" width="25" align="middle" />Consultar
              </a>
            </div>
          </div>
          <!-- Paginador -->
          <div class="dataTables_wrapper">
            <div class="row">
              <div class="col-xs-6 col-left">
                <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice(); ?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults(); ?></div>
              </div>
              <div class="col-xs-6 col-right">
                <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
                  <?php
                  echo use_helper('Pagination');
                  echo pager_navigation($pager, 'auditoria/list', $parametros);
                  ?>
                </div>
              </div>
            </div>
          </div>
          <!-- fin  paginacion -->
        </div>
      </div>
    <?php
    endif;
    ?>
  </div>
</div>