<?php echo form_tag('tipo_impuesto/save', array(
  'id'        => 'sf_admin_edit_form',
  'name'      => 'sf_admin_edit_form',
  'multipart' => true,
)) ?>

<?php echo object_input_hidden_tag($tipo_impuesto, 'getTipoimpuestoId') ?>

<fieldset id="sf_fieldset_none" class="">

<div class="form-row">
  <?php echo label_for('tipo_impuesto[descripcion]', __($labels['tipo_impuesto{descripcion}']), '') ?>
  <div class="content<?php if ($sf_request->hasError('tipo_impuesto{descripcion}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('tipo_impuesto{descripcion}')): ?>
    <?php echo form_error('tipo_impuesto{descripcion}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_input_tag($tipo_impuesto, 'getDescripcion', array (
  'size' => 80,
  'control_name' => 'tipo_impuesto[descripcion]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

</fieldset>

<?php include_partial('edit_actions', array('tipo_impuesto' => $tipo_impuesto)) ?>

</form>

<ul class="sf_admin_actions">
      <li class="float-left"><?php if ($tipo_impuesto->getTipoimpuestoId()): ?>
<?php echo button_to(__('delete'), 'tipo_impuesto/delete?tipoimpuesto_id='.$tipo_impuesto->getTipoimpuestoId(), array (
  'post' => true,
  'confirm' => __('Are you sure?'),
  'class' => 'sf_admin_action_delete',
)) ?><?php endif; ?>
</li>
  </ul>
