    <td><?php echo link_to($factura_receptor->getFacturareceptorId() ? $factura_receptor->getFacturareceptorId() : __('-'), 'factura_receptor/edit?facturareceptor_id='.$factura_receptor->getFacturareceptorId()) ?></td>
    <td><?php echo $factura_receptor->getFacturaproceso() ?></td>
      <td><?php echo $factura_receptor->getRegional() ?></td>
      <td><?php echo $factura_receptor->getUsuario() ?></td>
  