<?php $base_path = sfConfig::get('base_simad'); ?>
<div class="panel panel-primary">
  <div class="panel-heading">
    <div class="panel-title"><?php echo ucfirst($tipo) ?> del log <?php echo str_pad($log_id, 10, "0", STR_PAD_LEFT) ?></div>
    <div class="panel-options">
      <a class="btn btn-white btn-sm" href="<?php echo $base_path ?>/administracion.php/webservice_log/download?id=<?php echo $log_id ?>&tipo=<?php echo $tipo ?>"><i class="entypo-download"></i> Descargar</a>
    </div>
  </div>
  <div class="panel-body">
    <?php if ($truncado) : ?>
      <div class="alert alert-warning">El contenido es muy extenso y se muestra truncado, descargue el archivo para verlo completo.</div>
    <?php endif; ?>
    <pre class="wslog-visor"><?php echo htmlspecialchars($contenido) ?></pre>
  </div>
</div>
