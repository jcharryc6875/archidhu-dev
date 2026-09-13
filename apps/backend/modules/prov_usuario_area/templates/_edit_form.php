<?php echo form_tag('prov_usuario_area/save', array(
  'id'        => 'sf_admin_edit_form',
  'name'      => 'sf_admin_edit_form',
  'multipart' => true,
)) ?>

<?php echo object_input_hidden_tag($prov_usuario_area, 'getProvUsuarioAreaId') ?>

<fieldset id="sf_fieldset_none" class="">

<div class="form-row">
  <?php echo label_for('prov_usuario_area[prov_estado_aprobador_id]', __($labels['prov_usuario_area{prov_estado_aprobador_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('prov_usuario_area{prov_estado_aprobador_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('prov_usuario_area{prov_estado_aprobador_id}')): ?>
    <?php echo form_error('prov_usuario_area{prov_estado_aprobador_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($prov_usuario_area, 'getProvEstadoAprobadorId', array (
  'related_class' => 'ProvEstadoAprobador',
  'control_name' => 'prov_usuario_area[prov_estado_aprobador_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

<div class="form-row">
  <?php echo label_for('prov_usuario_area[usuario_id]', __($labels['prov_usuario_area{usuario_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('prov_usuario_area{usuario_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('prov_usuario_area{usuario_id}')): ?>
    <?php echo form_error('prov_usuario_area{usuario_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($prov_usuario_area, 'getUsuarioId', array (
  'peer_method'=>'getAllUser',
  'related_class' => 'Usuario',
  'control_name' => 'prov_usuario_area[usuario_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

<div class="form-row">
  <?php echo label_for('prov_usuario_area[pais_id]', __($labels['prov_usuario_area{pais_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('prov_usuario_area{pais_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('prov_usuario_area{pais_id}')): ?>
    <?php echo form_error('prov_usuario_area{pais_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($prov_usuario_area, 'getPaisId', array (
  'related_class' => 'Pais',
  'control_name' => 'prov_usuario_area[pais_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

<div class="form-row">
  <?php echo label_for('prov_usuario_area[prov_area_aprobadora_id]', __($labels['prov_usuario_area{prov_area_aprobadora_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('prov_usuario_area{prov_area_aprobadora_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('prov_usuario_area{prov_area_aprobadora_id}')): ?>
    <?php echo form_error('prov_usuario_area{prov_area_aprobadora_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($prov_usuario_area, 'getProvAreaAprobadoraId', array (
  'related_class' => 'ProvAreaAprobadora',
  'control_name' => 'prov_usuario_area[prov_area_aprobadora_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

</fieldset>

<?php include_partial('edit_actions', array('prov_usuario_area' => $prov_usuario_area)) ?>

</form>

<ul class="sf_admin_actions">
      <li class="float-left"><?php if ($prov_usuario_area->getProvUsuarioAreaId()): ?>
<?php echo button_to(__('delete'), 'prov_usuario_area/delete?prov_usuario_area_id='.$prov_usuario_area->getProvUsuarioAreaId(), array (
  'post' => true,
  'confirm' => __('Are you sure?'),
  'class' => 'sf_admin_action_delete',
)) ?><?php endif; ?>
</li>
  </ul>
