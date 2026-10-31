<?php
use_helper('Object');
$path_theme = sfConfig::get('theme_simad');
use_javascript($path_theme . 'assets/js/notificacion-cuenta.js');
$base_path = sfConfig::get('base_simad');
$tiposLabel = array('OAUTH2' => 'OAuth2 (Entra ID)', 'SMTP' => 'SMTP b&aacute;sico');
$estadoClase = array('OK' => 'label-success', 'ERROR' => 'label-danger', 'SIN_PROBAR' => 'label-default');
$estadoTexto = array('OK' => 'Operativa', 'ERROR' => 'Con error', 'SIN_PROBAR' => 'Sin probar');
?>
<div class="row">
  <div class="col-md-12">
    <div id="nc-mensaje"></div>
    <div class="panel panel-gradient panel-shadow">
      <div class="panel-heading">
        <div class="panel-title">Cuentas de notificaci&oacute;n por correo</div>
      </div>
      <div class="panel-body with-table" id="nc-listado"
        data-url-toggle="<?php echo url_for('notificacion_cuenta/toggleActivo') ?>"
        data-url-principal="<?php echo url_for('notificacion_cuenta/setPrincipal') ?>">
        <?php if (count($cuentas) == 0) : ?>
          <div class="alert alert-default"><strong>No existen cuentas</strong> registradas. Cree la primera cuenta remitente.</div>
        <?php else : ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                  <th class="text-center">Cuenta</th>
                  <th class="text-center">Remitente</th>
                  <th class="text-center">Autenticaci&oacute;n</th>
                  <th class="text-center">Servidor</th>
                  <th class="text-center">Rol</th>
                  <th class="text-center">Orden</th>
                  <th class="text-center">Activa</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center">&Uacute;ltimo env&iacute;o OK</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cuentas as $cuenta) : ?>
                  <?php $estado = $cuenta->getEstadoOperativo() ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($cuenta->getNombre()) ?></strong></td>
                    <td><?php echo htmlspecialchars($cuenta->getEmailRemitente()) ?></td>
                    <td class="text-center"><?php echo isset($tiposLabel[$cuenta->getTipo()]) ? $tiposLabel[$cuenta->getTipo()] : htmlspecialchars($cuenta->getTipo()) ?></td>
                    <td><?php echo htmlspecialchars($cuenta->getHostSmtp()) ?>:<?php echo (int) $cuenta->getPuerto() ?></td>
                    <td class="text-center">
                      <?php if ($cuenta->getEsPrincipal()) : ?>
                        <span class="label label-info">Principal</span>
                      <?php else : ?>
                        <span class="label label-default">Respaldo</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo (int) $cuenta->getPrioridad() ?></td>
                    <td class="text-center"><?php echo $cuenta->getActivo() ? 'S&iacute;' : 'No' ?></td>
                    <td class="text-center">
                      <span class="label <?php echo isset($estadoClase[$estado]) ? $estadoClase[$estado] : 'label-default' ?> tooltip-primary" data-toggle="tooltip"
                        data-original-title="<?php echo htmlspecialchars((string) $cuenta->getUltimoError()) ?>"><?php echo isset($estadoTexto[$estado]) ? $estadoTexto[$estado] : htmlspecialchars($estado) ?></span>
                    </td>
                    <td class="text-center"><?php echo $cuenta->getFechaUltimoEnvioOk() ? htmlspecialchars($cuenta->getFechaUltimoEnvioOk()) : '-' ?></td>
                    <td class="text-center nc-acciones">
                      <?php if ($puedeProbar) : ?>
                        <a class="btn btn-blue btn-xs tooltip-primary" data-toggle="tooltip" data-original-title="Probar el servicio" href="#"
                          data-nc-abrir-prueba="<?php echo (int) $cuenta->getPrimaryKey() ?>" data-nombre="<?php echo htmlspecialchars($cuenta->getNombre()) ?>"><i class="entypo-flash"></i></a>
                      <?php endif; ?>
                      <?php if ($puedeEditar) : ?>
                        <a class="btn btn-default btn-xs tooltip-primary" data-toggle="tooltip" data-original-title="Editar" href="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('notificacion_cuenta/edit?id=' . $cuenta->getPrimaryKey()) ?>', 1000, 650)"><i class="entypo-pencil"></i></a>
                        <a class="btn btn-<?php echo $cuenta->getActivo() ? 'orange' : 'green' ?> btn-xs tooltip-primary" data-toggle="tooltip"
                          data-original-title="<?php echo $cuenta->getActivo() ? 'Desactivar' : 'Activar' ?>" href="#" data-nc-toggle="<?php echo (int) $cuenta->getPrimaryKey() ?>"><i class="entypo-<?php echo $cuenta->getActivo() ? 'block' : 'check' ?>"></i></a>
                        <?php if (!$cuenta->getEsPrincipal() && $cuenta->getActivo()) : ?>
                          <a class="btn btn-info btn-xs tooltip-primary" data-toggle="tooltip" data-original-title="Definir como principal" href="#" data-nc-principal="<?php echo (int) $cuenta->getPrimaryKey() ?>"><i class="entypo-star"></i></a>
                        <?php endif; ?>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
        <hr />
        <?php if ($puedeEditar) : ?>
          <a class="btn btn-white btn-sm" href="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('notificacion_cuenta/edit') ?>', 1000, 650)">
            <img border="0" src="<?php echo $base_path ?>/images/simad/ico_crear_nuevo.png" width="25" align="middle" />Crear Nueva Cuenta
          </a>
        <?php endif; ?>
        <p class="help-block">Los correos se env&iacute;an por la cuenta principal; si falla, se usa la siguiente cuenta activa seg&uacute;n el orden. Antes de activar una cuenta debe probarse el servicio.</p>
      </div>
    </div>
    <?php include_partial('panelPrueba', array('cuenta_id' => null, 'cuenta_nombre' => '', 'puede_probar' => $puedeProbar)) ?>
  </div>
</div>
