<?php $com_aprobacion = $form->getObject() ?>
<h1><?php echo $com_aprobacion->isNew() ? 'New' : 'Edit' ?> Com aprobaciones</h1>

<form action="<?php echo url_for('com_aprobaciones/update'.(!$com_aprobacion->isNew() ? '?comaprobacion_id='.$com_aprobacion->getComaprobacionId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('com_aprobaciones/index') ?>">Cancel</a>
          <?php if (!$com_aprobacion->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'com_aprobaciones/delete?comaprobacion_id='.$com_aprobacion->getComaprobacionId(), array('post' => true, 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['estadocomaprobacion_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['estadocomaprobacion_id']->renderError() ?>
          <?php echo $form['estadocomaprobacion_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['usuario_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['usuario_id']->renderError() ?>
          <?php echo $form['usuario_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['modulo_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['modulo_id']->renderError() ?>
          <?php echo $form['modulo_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['consecutivo_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['consecutivo_id']->renderError() ?>
          <?php echo $form['consecutivo_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['observaciones']->renderLabel() ?></th>
        <td>
          <?php echo $form['observaciones']->renderError() ?>
          <?php echo $form['observaciones'] ?>

        <?php echo $form['comaprobacion_id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
