<?php
use_helper('jQuery', 'Pagination');
?>
<div class="panel-heading">
  <div class="panel-title">Auditor&iacute;a de env&iacute;os de correo</div>
</div>
<?php if ($pager->getNbResults() == 0) : ?>
  <div class="panel-body">
    <div class="alert alert-default"><strong>No existen Registros</strong>, intente con diferentes filtros de consulta.</div>
  </div>
<?php else : ?>
  <div class="panel-body with-table email-table">
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-striped">
        <thead>
          <tr>
            <th class="text-center">Fecha</th>
            <th class="text-center">Cuenta</th>
            <th class="text-center">Destinatarios</th>
            <th class="text-center">Asunto</th>
            <th class="text-center">Resultado</th>
            <th class="text-center">Error</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pager->getResults() as $row) : ?>
            <?php $ok = $row->getResultado() === 'OK' ?>
            <tr<?php echo $ok ? '' : ' class="danger"' ?>>
              <td class="nelog-fecha"><?php echo htmlspecialchars((string) $row->getFecha()) ?></td>
              <td><?php echo $row->getNotificacioncuentaId() && isset($cuentas[$row->getNotificacioncuentaId()]) ? htmlspecialchars($cuentas[$row->getNotificacioncuentaId()]) : 'SMTP propio / sin cuenta' ?></td>
              <td><?php echo htmlspecialchars((string) $row->getDestinatarios()) ?></td>
              <td><?php echo htmlspecialchars((string) $row->getAsunto()) ?></td>
              <td class="text-center">
                <?php if ($ok) : ?>
                  <i class="entypo-check text-success tooltip-primary" data-toggle="tooltip" data-original-title="Enviado"></i>
                <?php else : ?>
                  <i class="entypo-cancel text-danger tooltip-primary" data-toggle="tooltip" data-original-title="Con error"></i>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars((string) $row->getError()) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <hr />
    <div class="dataTables_wrapper">
      <div class="row">
        <div class="col-xs-6 col-left">
          <div class="dataTables_info" role="status" aria-live="polite">Mostrando del <?php echo $pager->getFirstIndice() ?> al <?php echo $pager->getLastIndice() ?> de <?php echo $pager->getNbResults() ?></div>
        </div>
        <div class="col-xs-6 col-right">
          <div class="dataTables_paginate paging_bootstrap">
            <?php echo pager_navigation_async($pager, 'notificacion_envio_log/list', $parametros) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
