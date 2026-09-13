  <th id="sf_admin_list_th_prov_usuario_area_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/prov_usuario_area/sort') == 'prov_usuario_area_id'): ?>
      <?php echo link_to(__('Prov usuario area'), 'prov_usuario_area/list?sort=prov_usuario_area_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Prov usuario area'), 'prov_usuario_area/list?sort=prov_usuario_area_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_prov_estado_aprobador_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/prov_usuario_area/sort') == 'prov_estado_aprobador_id'): ?>
      <?php echo link_to(__('Prov estado aprobador'), 'prov_usuario_area/list?sort=prov_estado_aprobador_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Prov estado aprobador'), 'prov_usuario_area/list?sort=prov_estado_aprobador_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_usuario_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/prov_usuario_area/sort') == 'usuario_id'): ?>
      <?php echo link_to(__('Usuario'), 'prov_usuario_area/list?sort=usuario_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Usuario'), 'prov_usuario_area/list?sort=usuario_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_pais_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/prov_usuario_area/sort') == 'pais_id'): ?>
      <?php echo link_to(__('Pais'), 'prov_usuario_area/list?sort=pais_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Pais'), 'prov_usuario_area/list?sort=pais_id&type=asc') ?>
      <?php endif; ?>
          </th>
  <th id="sf_admin_list_th_prov_area_aprobadora_id">
          <?php if ($sf_user->getAttribute('sort', null, 'sf_admin/prov_usuario_area/sort') == 'prov_area_aprobadora_id'): ?>
      <?php echo link_to(__('Prov area aprobadora'), 'prov_usuario_area/list?sort=prov_area_aprobadora_id&type='.($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort') == 'asc' ? 'desc' : 'asc')) ?>
      (<?php echo __($sf_user->getAttribute('type', 'asc', 'sf_admin/prov_usuario_area/sort')) ?>)
      <?php else: ?>
      <?php echo link_to(__('Prov area aprobadora'), 'prov_usuario_area/list?sort=prov_area_aprobadora_id&type=asc') ?>
      <?php endif; ?>
          </th>
