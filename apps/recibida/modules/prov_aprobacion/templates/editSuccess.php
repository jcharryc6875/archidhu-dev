<?php $prov_aprobacion = $form->getObject() ?>
<h1><?php echo $prov_aprobacion->isNew() ? 'New' : 'Edit' ?> Prov aprobacion</h1>

<form action="<?php echo url_for('prov_aprobacion/update'.(!$prov_aprobacion->isNew() ? '?prov_aprobacion_id='.$prov_aprobacion->getProvAprobacionId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('prov_aprobacion/index') ?>">Cancel</a>
          <?php if (!$prov_aprobacion->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'prov_aprobacion/delete?prov_aprobacion_id='.$prov_aprobacion->getProvAprobacionId(), array('post' => true, 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['prov_usuario_area_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['prov_usuario_area_id']->renderError() ?>
          <?php echo $form['prov_usuario_area_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['prov_periodo_validez_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['prov_periodo_validez_id']->renderError() ?>
          <?php echo $form['prov_periodo_validez_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['prov_estado_aprobacion_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['prov_estado_aprobacion_id']->renderError() ?>
          <?php echo $form['prov_estado_aprobacion_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['fecha_creacion']->renderLabel() ?></th>
        <td>
          <?php echo $form['fecha_creacion']->renderError() ?>
          <?php echo $form['fecha_creacion'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['observaciones']->renderLabel() ?></th>
        <td>
          <?php echo $form['observaciones']->renderError() ?>
          <?php echo $form['observaciones'] ?>

        <?php echo $form['prov_aprobacion_id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
