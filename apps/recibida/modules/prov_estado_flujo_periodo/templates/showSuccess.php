<table>
  <tbody>
    <tr>
      <th>Prov estado flujo periodo:</th>
      <td><?php echo $prov_estado_flujo_periodo->getProvEstadoFlujoPeriodoId() ?></td>
    </tr>
    <tr>
      <th>Prov estado aprobacion:</th>
      <td><?php echo $prov_estado_flujo_periodo->getProvEstadoAprobacionId() ?></td>
    </tr>
    <tr>
      <th>Prov periodo validez:</th>
      <td><?php echo $prov_estado_flujo_periodo->getProvPeriodoValidezId() ?></td>
    </tr>
    <tr>
      <th>Prov item flujo:</th>
      <td><?php echo $prov_estado_flujo_periodo->getProvItemFlujoId() ?></td>
    </tr>
    <tr>
      <th>Descripcion:</th>
      <td><?php echo $prov_estado_flujo_periodo->getDescripcion() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('prov_estado_flujo_periodo/edit?prov_estado_flujo_periodo_id='.$prov_estado_flujo_periodo->getProvEstadoFlujoPeriodoId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('prov_estado_flujo_periodo/index') ?>">List</a>
