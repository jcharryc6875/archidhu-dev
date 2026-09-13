  <th id="sf_admin_list_th_tipoimpuesto_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/tipo_impuesto/sort') == 'tipoimpuesto_id'): ?>
      <?php echo link_to(__('Tipoimpuesto'), 'tipo_impuesto/list?sort=tipoimpuesto_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/tipo_impuesto/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/tipo_impuesto/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Tipoimpuesto'), 'tipo_impuesto/list?sort=tipoimpuesto_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_descripcion">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/tipo_impuesto/sort') == 'descripcion'): ?>
      <?php echo link_to(__('Descripcion'), 'tipo_impuesto/list?sort=descripcion&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/tipo_impuesto/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/tipo_impuesto/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Descripcion'), 'tipo_impuesto/list?sort=descripcion&type=asc') ?>
      <?php endif; ?>
          </th>
