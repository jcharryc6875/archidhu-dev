<h1>Prov periodo validez List</h1>

<table>
  <thead>
    <tr>
      <th>Prov periodo validez</th>
      <th>Prov correspondiente autofacturacion</th>
      <th>Proveedor</th>
      <th>Prov tipo industria</th>
      <th>Prov condicion expedicion</th>
      <th>Prov rechazada creacion2</th>
      <th>Prov aprobacion</th>
      <th>Prov revisado creacion</th>
      <th>Prov condicion pago</th>
      <th>Prov grupo tesoreria</th>
      <th>Pro prov aprobacion</th>
      <th>Prov aduana entrada</th>
      <th>Prov rechazada creacion</th>
      <th>Prob aprobado para creacion</th>
      <th>Prov grupo esquema</th>
      <th>Prov moneda pedido</th>
      <th>Prov tipo contribuyente</th>
      <th>Prov cuenta asociada</th>
      <th>Fecha inicial</th>
      <th>Fecha final</th>
      <th>Observaciones generales</th>
      <th>Fecha creacion sap</th>
      <th>Codigo sap</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($prov_periodo_validezList as $prov_periodo_validez): ?>
    <tr>
      <td><a href="<?php echo url_for('prov_periodo_validez/edit?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId()) ?>"><?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?></a></td>
      <td><?php echo $prov_periodo_validez->getProvCorrespondienteAutofacturacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProveedorId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvTipoIndustriaId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvCondicionExpedicionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvRechazadaCreacion2Id() ?></td>
      <td><?php echo $prov_periodo_validez->getProvAprobacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvRevisadoCreacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvCondicionPagoId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvGrupoTesoreriaId() ?></td>
      <td><?php echo $prov_periodo_validez->getProProvAprobacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvAduanaEntradaId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvRechazadaCreacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProbAprobadoParaCreacionId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvGrupoEsquemaId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvMonedaPedidoId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvTipoContribuyenteId() ?></td>
      <td><?php echo $prov_periodo_validez->getProvCuentaAsociadaId() ?></td>
      <td><?php echo $prov_periodo_validez->getFechaInicial() ?></td>
      <td><?php echo $prov_periodo_validez->getFechaFinal() ?></td>
      <td><?php echo $prov_periodo_validez->getObservacionesGenerales() ?></td>
      <td><?php echo $prov_periodo_validez->getFechaCreacionSap() ?></td>
      <td><?php echo $prov_periodo_validez->getCodigoSap() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('prov_periodo_validez/create') ?>">Create</a>
