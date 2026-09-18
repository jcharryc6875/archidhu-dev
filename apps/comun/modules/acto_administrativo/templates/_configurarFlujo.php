<?php
// UARIV-202605 (ampliación): configurar orden de ejecución y permiso de edición por acto administrativo
use_helper('Object', 'jQuery');
?>
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">Configurar Flujo de este Acto Administrativo</div>
  </div>
  <div class="panel-body">
    <?php
      echo form_tag('acto_administrativo/updateOrdenParticipantes', array('name' => 'formFlujo', 'role' => 'form', 'class' => 'form-horizontal'));
      echo object_input_hidden_tag($acto_administrativo, 'getActoadministrativoId');
    ?>

    <?php if(count($etapasConfigActo)){ ?>
      <h5>¿Las siguientes etapas permiten edición en este acto?</h5>
      <table class="table table-condensed table-bordered">
        <thead>
          <tr>
            <th>Etapa</th>
            <th style="width: 320px;">¿Permite edición en este acto?</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($etapasConfigActo as $etapa):
            $override = ActoadminEtapaActoConfigPeer::getOverride($acto_administrativo->getPrimaryKey(), $etapa->getPrimaryKey());
            $valorActual = $override != null ? ($override->getPermiteEdicion() ? 'si' : 'no') : 'heredar';
        ?>
          <tr>
            <td>
              <?php echo $etapa->getNombre(); ?>
              <span class="text-muted">(global: <?php echo $etapa->getPermiteEdicion() ? 'Sí' : 'No'; ?>)</span>
            </td>
            <td>
              <select name="etapas_override[<?php echo $etapa->getPrimaryKey(); ?>]" class="form-control input-sm">
                <option value="heredar" <?php echo $valorActual == 'heredar' ? 'selected' : ''; ?>>Heredar configuración global</option>
                <option value="si" <?php echo $valorActual == 'si' ? 'selected' : ''; ?>>Sí, permitir edición en este acto</option>
                <option value="no" <?php echo $valorActual == 'no' ? 'selected' : ''; ?>>No, bloquear edición en este acto</option>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php } ?>

    <h5>Orden de ejecución y permiso de edición por participante</h5>
    <p class="help-block">
      Deje "Orden" vacío para que este participante siga el flujo por etapas configurado globalmente (Administración &gt; Etapas). Si asigna un orden a cualquier participante, este acto pasa a gobernarse por el orden que usted defina aquí; puede repetir números para que, al aprobar, se pueda elegir a cuál de los empatados enviar el trámite.
    </p>
    <table class="table table-condensed table-bordered">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Etapa</th>
          <th style="width: 100px;">Orden</th>
          <th style="width: 90px;" class="text-center">Puede editar</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($participantesFlujo as $participante):
          $etapa = $participante->getActoadminEtapa();
          $permiteEdicionEtapa = ActoAdministrativoPeer::etapaPermiteEdicion($acto_administrativo->getPrimaryKey(), $participante->getActoadminetapaId());
      ?>
        <tr>
          <td><?php echo $participante->getUsuario() ? $participante->getUsuario()->getNombreAll() : ''; ?></td>
          <td><?php echo $participante->getRolUsuarioActoAdministvo() ? $participante->getRolUsuarioActoAdministvo()->getDescripcion() : ''; ?></td>
          <td><?php echo $etapa ? $etapa->getNombre() : '-'; ?></td>
          <td>
            <input type="number" min="1" class="form-control input-sm" name="participantes[<?php echo $participante->getPrimaryKey(); ?>][orden]" value="<?php echo $participante->getOrdenEjecucion(); ?>">
          </td>
          <td class="text-center">
            <?php if($permiteEdicionEtapa){ ?>
              <input type="checkbox" name="participantes[<?php echo $participante->getPrimaryKey(); ?>][puede_editar]" value="1" <?php echo ($participante->getPuedeEditar() === null || $participante->getPuedeEditar()) ? 'checked' : ''; ?>>
            <?php }else{ ?>
              <span class="text-muted" data-toggle="tooltip" data-original-title="La etapa no permite edición en este acto">No aplica</span>
            <?php } ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <div class="row">
      <div class="col-sm-12 form-group">
        <?php
          echo jq_submit_to_remote('guardarFlujo', 'Guardar Configuración de Flujo', array(
            'url' => 'acto_administrativo/updateOrdenParticipantes',
            'loading' => "javascript:jQuery.LoadingStructData();",
            'complete' => 'try{ var response_value = JSON.parse(XMLHttpRequest.responseText); javascript:jQuery.CloseLoadingStructData(); if(response_value.status == 200){ toastr.success(response_value.message); }else{ toastr.error(response_value.message); } }catch(err) { javascript:jQuery.CloseLoadingStructData(); toastr.error(err.message); }',
          ), array('class' => 'btn btn-primary btn-sm'));
        ?>
      </div>
    </div>
    </form>
  </div>
</div>
