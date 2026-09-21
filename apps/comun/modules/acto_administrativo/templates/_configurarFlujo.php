<?php
// UARIV-202605 (ampliación): configurar orden de ejecución y permiso de edición por acto administrativo.
// Contenido reutilizable: se muestra dentro de un modal (edición) o dentro de una pestaña (detalle/show).
use_helper('Object', 'jQuery');
?>
<?php echo form_tag('acto_administrativo/updateOrdenParticipantes', array('name' => 'formFlujo', 'id' => 'formFlujo', 'role' => 'form')); ?>
<?php echo object_input_hidden_tag($acto_administrativo, 'getActoadministrativoId'); ?>

<ul class="nav nav-tabs flujo-subtabs">
  <li class="active"><a data-toggle="tab" href="#flujoOrdenPane_<?php echo $acto_administrativo->getPrimaryKey(); ?>">Orden y participantes</a></li>
  <?php if (count($etapasConfigActo)) { ?>
    <li><a data-toggle="tab" href="#flujoEtapasPane_<?php echo $acto_administrativo->getPrimaryKey(); ?>">Permisos de edición por etapa</a></li>
  <?php } ?>
</ul>

<div class="tab-content flujo-subtabs-content">

  <div id="flujoOrdenPane_<?php echo $acto_administrativo->getPrimaryKey(); ?>" class="tab-pane active">
    <blockquote class="blockquote-blue">
      <p class="help-block">Arrastre para reordenar (icono de la izquierda); el número se actualiza solo. Deje "Orden" vacío para que ese participante siga el flujo por etapas configurado globalmente. Dos participantes con el mismo número quedan "empatados": al aprobar, se le pedirá al usuario elegir a cuál enviar el trámite.</p>
    </blockquote>
    <div class="alert alert-warning flujo-tie-alert" style="display:none;">
      <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;Hay participantes con el mismo orden: al aprobar esa etapa, se pedirá elegir a cuál enviar el trámite.
    </div>
    <div class="alert alert-danger flujo-firmante-alert" style="display:none;">
      <span class="glyphicon glyphicon-remove-sign"></span>&nbsp;El último participante del orden configurado debe ser un Firmante: es quien cierra el flujo y genera el radicado. No podrá guardar esta configuración hasta ajustarlo.
    </div>
    <ul class="list-unstyled flujo-participant-list">
      <?php foreach ($participantesFlujo as $index => $participante):
        $etapa = $participante->getActoadminEtapa();
        $permiteEdicionEtapa = ActoAdministrativoPeer::etapaPermiteEdicion($acto_administrativo->getPrimaryKey(), $participante->getActoadminetapaId());
        $nombre = $participante->getUsuario() ? $participante->getUsuario()->getNombreAll() : '';
        $palabras = preg_split('/\s+/', trim($nombre), -1, PREG_SPLIT_NO_EMPTY);
        $iniciales = strtoupper(substr(isset($palabras[0]) ? $palabras[0] : '', 0, 1) . substr(isset($palabras[1]) ? $palabras[1] : '', 0, 1));
      ?>
        <li class="flujo-participant-item" data-rol-id="<?php echo $participante->getRolusuarioactoadministvoId(); ?>">
          <span class="flujo-drag-handle" title="Arrastrar para reordenar"><span class="glyphicon glyphicon-resize-vertical"></span></span>
          <?php if ($participante->getOrdenEjecucion()) { ?>
            <span class="flujo-order-badge"><?php echo $participante->getOrdenEjecucion(); ?></span>
          <?php } else { ?>
            <span class="flujo-order-badge flujo-order-badge-default" data-toggle="tooltip" data-original-title="Sin orden manual asignado: sigue el orden por defecto del flujo (posición <?php echo $index + 1; ?>)">-</span>
          <?php } ?>
          <span class="flujo-avatar"><?php echo $iniciales; ?></span>
          <span class="flujo-participant-info">
            <span class="flujo-participant-name">
              <?php echo $nombre; ?>
              <span class="label label-default flujo-role-badge"><?php echo $participante->getRolUsuarioActoAdministvo() ? $participante->getRolUsuarioActoAdministvo()->getDescripcion() : ''; ?></span>
            </span>
            <span class="flujo-participant-meta">Etapa: <?php echo $etapa ? $etapa->getNombre() : 'Sin etapa configurada'; ?></span>
          </span>
          <input type="number" min="1" class="flujo-order-input" name="participantes[<?php echo $participante->getPrimaryKey(); ?>][orden]" value="<?php echo $participante->getOrdenEjecucion(); ?>">
          <span class="flujo-editar-col">
            <?php if ($permiteEdicionEtapa) { ?>
              <div class="make-switch flujo-switch" data-on="success" data-off="default" data-on-label="Sí" data-off-label="No" data-toggle="tooltip" data-original-title="Puede editar el contenido en esta etapa">
                <?php echo checkbox_tag('participantes[' . $participante->getPrimaryKey() . '][puede_editar]', 1, ($participante->getPuedeEditar() === null || $participante->getPuedeEditar())); ?>
              </div>
            <?php } else { ?>
              <span class="text-muted small flujo-no-aplica" data-toggle="tooltip" data-original-title="La etapa no permite edición en este acto">No aplica</span>
            <?php } ?>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php if (count($etapasConfigActo)) { ?>
    <div id="flujoEtapasPane_<?php echo $acto_administrativo->getPrimaryKey(); ?>" class="tab-pane">
      <p class="help-block">Por defecto cada etapa hereda la configuración global (Administración &gt; Etapas). Aquí puede forzar sí/no solo para este acto.</p>
      <?php foreach ($etapasConfigActo as $etapa):
        $override = ActoadminEtapaActoConfigPeer::getOverride($acto_administrativo->getPrimaryKey(), $etapa->getPrimaryKey());
        $valorActual = $override != null ? ($override->getPermiteEdicion() ? 'si' : 'no') : 'heredar';
        $inputName = 'etapas_override[' . $etapa->getPrimaryKey() . ']';
      ?>
        <div class="flujo-etapa-row">
          <span class="flujo-etapa-name">
            <?php echo $etapa->getNombre(); ?>
            <small class="text-muted">Global: <?php echo $etapa->getPermiteEdicion() ? 'Sí' : 'No'; ?> permite edición</small>
          </span>
          <div class="btn-group flujo-segmented" data-toggle="buttons">
            <label class="btn btn-default btn-sm <?php echo $valorActual == 'heredar' ? 'active' : ''; ?>">
              <input type="radio" name="<?php echo $inputName; ?>" value="heredar" <?php echo $valorActual == 'heredar' ? 'checked' : ''; ?>> Heredar
            </label>
            <label class="btn btn-default btn-sm <?php echo $valorActual == 'si' ? 'active' : ''; ?>">
              <input type="radio" name="<?php echo $inputName; ?>" value="si" <?php echo $valorActual == 'si' ? 'checked' : ''; ?>> Sí
            </label>
            <label class="btn btn-default btn-sm <?php echo $valorActual == 'no' ? 'active' : ''; ?>">
              <input type="radio" name="<?php echo $inputName; ?>" value="no" <?php echo $valorActual == 'no' ? 'checked' : ''; ?>> No
            </label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php } ?>

</div>

<div class="flujo-actions">
  <?php
  echo jq_submit_to_remote('guardarFlujo', 'Guardar Configuración de Flujo', array(
    'url' => 'acto_administrativo/updateOrdenParticipantes',
    'loading' => "javascript:jQuery.LoadingStructData();",
    'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText); javascript:jQuery.CloseLoadingStructData(); if(response_value.status == 200){ toastr.success(response_value.message); setTimeout(function(){ try{ parent.jQuery.ReloadAndCloseModalSIMAD(); }catch(e){ document.location.reload(); } }, 1500); }else{ toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
  ), array('class' => 'btn btn-primary btn-sm'));
  ?>
</div>
</form>