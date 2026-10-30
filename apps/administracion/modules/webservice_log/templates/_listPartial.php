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
  <div class="panel-body with-table">
    <table class="table table-bordered table-hover table-striped responsive">
      <thead>
        <tr>
          <th class="text-center">Consecutivo</th>
          <th class="text-center">Fecha</th>
          <th class="text-center">M&eacute;todo</th>
          <th class="text-center">Usuario</th>
          <th class="text-center">V&aacute;lido</th>
          <th class="text-center">Operaci&oacute;n</th>
          <th class="text-center">Mensaje</th>
          <th class="text-center">Descargar</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pager->getResults() as $row) : ?>
          <tr>
            <td class="text-center"><?php echo str_pad($row[0], 10, "0", STR_PAD_LEFT) ?></td>
            <td><?php echo htmlspecialchars($row[1]) ?></td>
            <td><?php echo htmlspecialchars($row[2]) ?></td>
            <td><?php echo htmlspecialchars($row[3]) ?></td>
            <td class="text-center"><?php echo $row[4] ? 'S&iacute;' : 'No' ?></td>
            <td><?php echo htmlspecialchars($row[5]) ?></td>
            <td><?php echo htmlspecialchars($row[6]) ?></td>
            <td class="text-center">
              <a class="btn btn-white btn-sm" href="<?php echo $base_path ?>/administracion.php/webservice_log/download?id=<?php echo $row[0] ?>&tipo=request">Request</a>
              <a class="btn btn-white btn-sm" href="<?php echo $base_path ?>/administracion.php/webservice_log/download?id=<?php echo $row[0] ?>&tipo=response">Response</a>
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
