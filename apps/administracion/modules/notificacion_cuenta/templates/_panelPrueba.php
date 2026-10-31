<?php
/**
 * Panel de prueba del servicio de una cuenta. Lo manejan los controles [data-nc-*] de notificacion-cuenta.js.
 * Parametros: $cuenta_id (null en el listado: se define al elegir la cuenta), $cuenta_nombre, $puede_probar.
 */
?>
<div id="nc-panel-prueba" class="panel panel-default<?php echo $cuenta_id ? '' : ' hidden' ?>"
  data-id="<?php echo (int) $cuenta_id ?>"
  data-url-conexion="<?php echo url_for('notificacion_cuenta/testConnection') ?>"
  data-url-envio="<?php echo url_for('notificacion_cuenta/testSend') ?>">
  <div class="panel-heading">
    <div class="panel-title">Prueba del servicio: <strong id="nc-prueba-nombre"><?php echo htmlspecialchars($cuenta_nombre) ?></strong></div>
  </div>
  <div class="panel-body">
    <?php if ($puede_probar) : ?>
      <div class="row">
        <div class="col-sm-5 col-md-4">
          <div class="form-group">
            <label class="control-label" for="nc-destino">Correo destino de la prueba:</label>
            <input type="text" id="nc-destino" class="form-control input-sm" placeholder="usuario@unidadvictimas.gov.co" />
          </div>
        </div>
        <div class="col-sm-7 col-md-8">
          <label class="control-label hidden-xs">&nbsp;</label>
          <div>
            <button type="button" class="btn btn-blue btn-sm" data-nc-test="conexion"><i class="entypo-flash"></i> Probar conexi&oacute;n</button>
            <button type="button" class="btn btn-green btn-sm" data-nc-test="envio"><i class="entypo-mail"></i> Enviar correo de prueba</button>
          </div>
          <span class="help-block">Probar conexi&oacute;n valida el token y la autenticaci&oacute;n SMTP sin enviar correo.</span>
        </div>
      </div>
    <?php endif; ?>
    <div id="nc-resultado" aria-live="polite"></div>
  </div>
</div>
