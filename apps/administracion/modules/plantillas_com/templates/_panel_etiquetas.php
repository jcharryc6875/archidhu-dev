<div class="panel panel-info">
  <div class="panel-heading">
    <div class="panel-title">C&oacute;digos Etiquetas</div>
  </div>
  <div class="panel-body with-table">
    <p class="tplcom-etiquetas-ayuda text-muted">Seleccione un m&oacute;dulo para ver las etiquetas disponibles.</p>
    <?php foreach ($etiquetas_por_modulo as $modulo_id => $modulo): ?>
      <div class="tplcom-etiquetas" data-modulo="<?php echo $modulo_id ?>">
        <div class="panel-group tplcom-acordeon" id="tplcom-acordeon-<?php echo $modulo_id ?>">
          <?php $indice = 0; ?>
          <?php foreach ($modulo['grupos'] as $grupo => $variables): ?>
            <?php $indice++; $panel_id = 'tplcom-grupo-' . $modulo_id . '-' . $indice; ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <a class="collapsed" data-toggle="collapse" data-parent="#tplcom-acordeon-<?php echo $modulo_id ?>" href="#<?php echo $panel_id ?>">
                  <?php echo $grupo ?> <span class="badge"><?php echo count($variables) ?></span>
                  <small class="tplcom-grupo-pista">Clic aqu&iacute; para ver las etiquetas de esta secci&oacute;n</small>
                </a>
              </div>
              <div id="<?php echo $panel_id ?>" class="panel-collapse collapse">
                <ul class="list-group">
                  <?php foreach ($variables as $variable): ?>
                    <li class="list-group-item">
                      <button type="button" class="tplcom-copiar" data-etiqueta="{[<?php echo $variable['clave'] ?>]}" title="Copiar etiqueta" aria-label="Copiar etiqueta"><i class="entypo-docs"></i></button>
                      <code class="tplcom-etiqueta-clave">{[<?php echo $variable['clave'] ?>]}</code>
                      <span class="tplcom-etiqueta-desc"><?php echo $variable['descripcion'] ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
