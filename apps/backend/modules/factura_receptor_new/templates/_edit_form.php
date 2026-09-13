<?php echo form_tag('factura_receptor/save', array(
  'id'        => 'sf_admin_edit_form',
  'name'      => 'sf_admin_edit_form',
  'multipart' => true,
)) ?>

<?php echo object_input_hidden_tag($factura_receptor, 'getFacturareceptorId') ?>

<fieldset id="sf_fieldset_none" class="">

<div class="form-row">
  <?php echo label_for('factura_receptor[facturaproceso_id]', __($labels['factura_receptor{facturaproceso_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('factura_receptor{facturaproceso_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('factura_receptor{facturaproceso_id}')): ?>
    <?php echo form_error('factura_receptor{facturaproceso_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($factura_receptor, 'getFacturaprocesoId', array (
  'related_class' => 'FacturaProceso',
  'control_name' => 'factura_receptor[facturaproceso_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

<div class="form-row">
  <?php echo label_for('factura_receptor[regional_id]', __($labels['factura_receptor{regional_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('factura_receptor{regional_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('factura_receptor{regional_id}')): ?>
    <?php echo form_error('factura_receptor{regional_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>

  <?php $value = object_select_tag($factura_receptor, 'getRegionalId', array (
  'related_class' => 'Regional',
  'control_name' => 'factura_receptor[regional_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

<div class="form-row">
  <?php echo label_for('factura_receptor[usuario_id]', __($labels['factura_receptor{usuario_id}']), 'class="required" ') ?>
  <div class="content<?php if ($sf_request->hasError('factura_receptor{usuario_id}')): ?> form-error<?php endif; ?>">
  <?php if ($sf_request->hasError('factura_receptor{usuario_id}')): ?>
    <?php echo form_error('factura_receptor{usuario_id}', array('class' => 'form-error-msg')) ?>
  <?php endif; ?>


	 
  <?php $value = object_select_tag($factura_receptor, 'getUsuarioId', array ('peer_method'=>'getAllUser',
  'related_class' => 'Usuario',
  'control_name' => 'factura_receptor[usuario_id]',
)); echo $value ? $value : '&nbsp;' ?>
    </div>
</div>

</fieldset>

<?php include_partial('edit_actions', array('factura_receptor' => $factura_receptor)) ?>

</form>

<ul class="sf_admin_actions">
      <li class="float-left"><?php if ($factura_receptor->getFacturareceptorId()): ?>
<?php echo button_to(__('delete'), 'factura_receptor/delete?facturareceptor_id='.$factura_receptor->getFacturareceptorId(), array (
  'post' => true,
  'confirm' => __('Are you sure?'),
  'class' => 'sf_admin_action_delete',
)) ?><?php endif; ?>
</li>
  </ul>
