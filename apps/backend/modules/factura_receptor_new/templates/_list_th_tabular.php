  <th id="sf_admin_list_th_facturareceptor_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/factura_receptor/sort') == 'facturareceptor_id'): ?>
      <?php echo link_to(__('Facturareceptor'), 'factura_receptor/list?sort=facturareceptor_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Facturareceptor'), 'factura_receptor/list?sort=facturareceptor_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_facturaproceso_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/factura_receptor/sort') == 'facturaproceso_id'): ?>
      <?php echo link_to(__('Facturaproceso'), 'factura_receptor/list?sort=facturaproceso_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Facturaproceso'), 'factura_receptor/list?sort=facturaproceso_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_regional_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/factura_receptor/sort') == 'regional_id'): ?>
      <?php echo link_to(__('Regional'), 'factura_receptor/list?sort=regional_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Regional'), 'factura_receptor/list?sort=regional_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_usuario_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/factura_receptor/sort') == 'usuario_id'): ?>
      <?php echo link_to(__('Usuario'), 'factura_receptor/list?sort=usuario_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/factura_receptor/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Usuario'), 'factura_receptor/list?sort=usuario_id&type=asc') ?>
      <?php endif; ?>
          </th>
