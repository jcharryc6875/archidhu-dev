<div class="panel panel-info">
  <div class="panel-heading">
    <div class="panel-title">C&oacute;digos Etiquetas</div>
  </div>
  <div class="panel-body with-table">
    <p class="tplcom-etiquetas-ayuda text-muted">Seleccione un m&oacute;dulo para ver las etiquetas disponibles.</p>
    <?php foreach ($etiquetas_por_modulo as $modulo_id => $modulo): ?>
      <div class="tplcom-etiquetas table-responsive" data-modulo="<?php echo $modulo_id ?>">
        <table class="table table-bordered table-hover table-condensed">
          <tbody>
            <?php foreach ($modulo['grupos'] as $grupo => $variables): ?>
              <tr class="tplcom-etiquetas-grupo">
                <th colspan="2"><?php echo $grupo ?></th>
              </tr>
              <?php foreach ($variables as $variable): ?>
                <tr>
                  <td><code>{[<?php echo $variable['clave'] ?>]}</code></td>
                  <td><?php echo $variable['descripcion'] ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  </div>
</div>
