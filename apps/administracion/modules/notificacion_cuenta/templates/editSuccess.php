<?php
use_helper('Object');
$path_theme = sfConfig::get('theme_simad');
use_javascript($path_theme . 'assets/js/notificacion-cuenta.js');
$esNueva = $cuenta->isNew();
$h = function ($valor) {
  return htmlspecialchars((string) $valor);
};
$tipo = $cuenta->getTipo();
$secretoPlaceholder = $esNueva ? '' : 'Sin cambios (deje vac&iacute;o para conservar el actual)';
?>
<div class="row">
  <div class="col-md-12">
    <?php if ($sf_user->hasFlash('message_success')) : ?>
      <div class="alert alert-success"><strong>Excelente! </strong><?php echo $h($sf_user->getFlash('message_success')) ?></div>
    <?php endif ?>
    <?php if ($sf_user->hasFlash('message_error')) : ?>
      <div class="alert alert-danger"><strong>Opps! </strong><?php echo $h($sf_user->getFlash('message_error')) ?></div>
    <?php endif ?>
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title"><?php echo $esNueva ? 'Nueva cuenta de notificaci&oacute;n' : 'Editar cuenta de notificaci&oacute;n' ?></div>
      </div>
      <div class="panel-body">
        <?php echo form_tag('notificacion_cuenta/save', array('id' => 'frm-nc', 'name' => 'frm-nc', 'role' => 'form', 'class' => 'form-groups-bordered', 'autocomplete' => 'off')) ?>
        <input type="hidden" name="id" value="<?php echo (int) $cuenta->getPrimaryKey() ?>" />

        <div class="row">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="nombre">Nombre de la cuenta<span class="ctrlreq">(*)</span>:</label>
              <input type="text" id="nombre" name="nombre" maxlength="100" class="form-control input-sm" value="<?php echo $h($cuenta->getNombre()) ?>" placeholder="Ej: Alertas SGDEA" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label class="control-label" for="nc-tipo">Autenticaci&oacute;n<span class="ctrlreq">(*)</span>:</label>
              <select id="nc-tipo" name="tipo" class="form-control input-sm">
                <option value="OAUTH2"<?php echo $tipo === 'OAUTH2' ? ' selected="selected"' : '' ?>>OAuth2 (Microsoft Entra ID)</option>
                <option value="SMTP"<?php echo $tipo === 'SMTP' ? ' selected="selected"' : '' ?>>SMTP b&aacute;sico</option>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label" for="prioridad">Orden<span class="ctrlreq">(*)</span>:</label>
              <input type="number" min="1" id="prioridad" name="prioridad" class="form-control input-sm" value="<?php echo (int) $cuenta->getPrioridad() ?>" />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="email_remitente">Correo remitente (buz&oacute;n)<span class="ctrlreq">(*)</span>:</label>
              <input type="text" id="email_remitente" name="email_remitente" maxlength="150" class="form-control input-sm" value="<?php echo $h($cuenta->getEmailRemitente()) ?>" />
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="nombre_remitente">Nombre del remitente:</label>
              <input type="text" id="nombre_remitente" name="nombre_remitente" maxlength="150" class="form-control input-sm" value="<?php echo $h($cuenta->getNombreRemitente()) ?>" />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="host_smtp">Servidor SMTP<span class="ctrlreq">(*)</span>:</label>
              <input type="text" id="host_smtp" name="host_smtp" maxlength="150" class="form-control input-sm" value="<?php echo $h($cuenta->getHostSmtp()) ?>" />
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label" for="puerto">Puerto<span class="ctrlreq">(*)</span>:</label>
              <input type="number" min="1" max="65535" id="puerto" name="puerto" class="form-control input-sm" value="<?php echo (int) $cuenta->getPuerto() ?>" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label class="control-label" for="secure_smtp">Seguridad:</label>
              <select id="secure_smtp" name="secure_smtp" class="form-control input-sm">
                <?php foreach (array('tls' => 'TLS (STARTTLS)', 'ssl' => 'SSL', '' => 'Ninguna') as $valor => $texto) : ?>
                  <option value="<?php echo $valor ?>"<?php echo (string) $cuenta->getSecureSmtp() === (string) $valor ? ' selected="selected"' : '' ?>><?php echo $texto ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="row" data-nc-grupo="OAUTH2">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="tenant_id">Tenant ID (Directory)<span class="ctrlreq">(*)</span>:</label>
              <input type="text" id="tenant_id" name="tenant_id" maxlength="80" class="form-control input-sm" value="<?php echo $h($cuenta->getTenantId()) ?>" />
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="client_id">Client ID (Application)<span class="ctrlreq">(*)</span>:</label>
              <input type="text" id="client_id" name="client_id" maxlength="80" class="form-control input-sm" value="<?php echo $h($cuenta->getClientId()) ?>" />
            </div>
          </div>
        </div>
        <div class="row" data-nc-grupo="OAUTH2">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="client_secret">Client Secret<span class="ctrlreq">(*)</span>:</label>
              <input type="password" id="client_secret" name="client_secret" maxlength="300" class="form-control input-sm" autocomplete="new-password" placeholder="<?php echo $secretoPlaceholder ?>" />
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="scope">Scope:</label>
              <input type="text" id="scope" name="scope" maxlength="200" class="form-control input-sm" value="<?php echo $h($cuenta->getScope()) ?>" />
            </div>
          </div>
        </div>

        <div class="row" data-nc-grupo="SMTP">
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="usuario_smtp">Usuario SMTP:</label>
              <input type="text" id="usuario_smtp" name="usuario_smtp" maxlength="150" class="form-control input-sm" value="<?php echo $h($cuenta->getUsuarioSmtp()) ?>" placeholder="Por defecto, el correo remitente" />
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              <label class="control-label" for="pass_smtp">Contrase&ntilde;a<span class="ctrlreq">(*)</span>:</label>
              <input type="password" id="pass_smtp" name="pass_smtp" maxlength="300" class="form-control input-sm" autocomplete="new-password" placeholder="<?php echo $secretoPlaceholder ?>" />
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label" for="auth_smtp">Autenticaci&oacute;n:</label>
              <div class="checkbox"><label><input type="checkbox" id="auth_smtp" name="auth_smtp" value="1"<?php echo $cuenta->getAuthSmtp() ? ' checked="checked"' : '' ?> /> Requiere</label></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <button type="submit" class="btn btn-success">Guardar Datos</button>
            <a class="btn btn-red" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">Cerrar</a>
          </div>
        </div>
        </form>
      </div>
    </div>
    <?php if (!$esNueva) : ?>
      <p class="help-block">La prueba usa los datos guardados: guarde los cambios antes de probar.</p>
      <?php include_partial('panelPrueba', array('cuenta_id' => $cuenta->getPrimaryKey(), 'cuenta_nombre' => $cuenta->getNombre(), 'puede_probar' => $sf_user->checkPerm('notificacion_cuenta/test', $sf_user->getAttribute('usuario_id', '', 'subscriber')))) ?>
    <?php endif; ?>
  </div>
</div>
