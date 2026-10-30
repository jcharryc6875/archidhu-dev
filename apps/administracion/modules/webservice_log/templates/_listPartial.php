<?php
$base_path = sfConfig::get('base_simad');
use_helper('jQuery', 'Pagination');
?>
<div class="panel-heading">
  <div class="panel-title">Log de Servicios Web</div>
</div>
<?php if ($pager->getNbResults() == 0) : ?>
  <div class="panel-body">
    <div class="alert alert-default"><strong>No existen Registros</strong>, intente con diferentes filtros de consulta.</div>
  </div>
<?php else : ?>
  <div class="panel-body with-table email-table">
    <table class="table table-bordered table-hover table-striped responsive">
      <thead>
        <tr>
          <th class="text-center col-xsm-1">Consecutivo</th>
          <th class=" text-center col-xsm-2">Fecha</th>
          <th class="text-center">M&eacute;todo</th>
          <th class="text-center col-xsm-1">Usuario</th>
          <th class="text-center col-xsm-1">Resultado</th>
          <th class=" text-center">Operaci&oacute;n</th>
          <th class="text-center">Mensaje</th>
          <th class="text-center">Descargar</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pager->getResults() as $row) : ?>
          <tr<?php echo $row[4] ? '' : ' class="danger"' ?>>
            <td class="text-center"><?php echo str_pad($row[0], 10, "0", STR_PAD_LEFT) ?></td>
            <td class="wslog-fecha"><?php echo htmlspecialchars($row[1]) ?></td>
            <td><strong><?php echo htmlspecialchars($row[2]) ?></strong></td>
            <td><?php echo htmlspecialchars($row[3]) ?></td>
            <td class="text-center">
              <?php if ($row[4]) : ?>
                <i class="entypo-check wslog-estado ok tooltip-primary" data-toggle="tooltip" data-original-title="Exitoso"></i>
              <?php else : ?>
                <i class="entypo-cancel wslog-estado error tooltip-primary" data-toggle="tooltip" data-original-title="Con error"></i>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row[5]) ?></td>
            <td><?php echo htmlspecialchars($row[6]) ?></td>
            <td class="text-center wslog-acciones">
              <?php foreach (array('request' => 'Request', 'response' => 'Response') as $tipo => $etiqueta) : ?>
                <?php $url = $base_path . '/administracion.php/webservice_log/%s?id=' . $row[0] . '&tipo=' . $tipo ?>
                <div class="btn-group">
                  <a class="btn btn-<?php echo $tipo === 'request' ? 'blue' : 'green' ?> btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Ver <?php echo $tipo ?>" href="javascript:jQuery.OpenModalSIMAD('<?php echo sprintf($url, 'view') ?>', 1000, 600)"><i class="entypo-eye"></i> <?php echo $etiqueta ?></a>
                  <a class="btn btn-<?php echo $tipo === 'request' ? 'blue' : 'green' ?> btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Descargar <?php echo $tipo ?>" href="<?php echo sprintf($url, 'download') ?>"><i class="entypo-download"></i></a>
                </div>
              <?php endforeach; ?>
            </td>
            </tr>
          <?php endforeach; ?>
      </tbody>
    </table>
    <hr />
    <div class="dataTables_wrapper">
      <div class="row">
        <div class="col-xs-6 col-left">
          <div class="dataTables_info" role="status" aria-live="polite">Mostrando del <?php echo $pager->getFirstIndice() ?> al <?php echo $pager->getLastIndice() ?> de <?php echo $pager->getNbResults() ?></div>
        </div>
        <div class="col-xs-6 col-right">
          <div class="dataTables_paginate paging_bootstrap">
            <?php echo pager_navigation_async($pager, 'webservice_log/list', $parametros) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>