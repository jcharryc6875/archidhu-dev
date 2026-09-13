<table>
  <tbody>
    <tr>
      <th>Prov aprobacion:</th>
      <td><?php echo $prov_aprobacion->getProvAprobacionId() ?></td>
    </tr>
    <tr>
      <th>Prov usuario area:</th>
      <td><?php echo $prov_aprobacion->getProvUsuarioAreaId() ?></td>
    </tr>
    <tr>
      <th>Prov periodo validez:</th>
      <td><?php echo $prov_aprobacion->getProvPeriodoValidezId() ?></td>
    </tr>
    <tr>
      <th>Prov estado aprobacion:</th>
      <td><?php echo $prov_aprobacion->getProvEstadoAprobacionId() ?></td>
    </tr>
    <tr>
      <th>Fecha creacion:</th>
      <td><?php echo $prov_aprobacion->getFechaCreacion() ?></td>
    </tr>
    <tr>
      <th>Observaciones:</th>
      <td><?php echo $prov_aprobacion->getObservaciones() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('prov_aprobacion/edit?prov_aprobacion_id='.$prov_aprobacion->getProvAprobacionId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('prov_aprobacion/index') ?>">List</a>
