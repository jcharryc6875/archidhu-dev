    <td><?php echo link_to($prov_usuario_area->getProvUsuarioAreaId() ? $prov_usuario_area->getProvUsuarioAreaId() : __('-'), 'prov_usuario_area/edit?prov_usuario_area_id='.$prov_usuario_area->getProvUsuarioAreaId()) ?></td>
    <td><?php echo $prov_usuario_area->getProvEstadoAprobador() ?></td>
      <td><?php echo $prov_usuario_area->getUsuario() ?></td>
      <td><?php echo $prov_usuario_area->getPais() ?></td>
      <td><?php echo $prov_usuario_area->getProvAreaAprobadora() ?></td>
  