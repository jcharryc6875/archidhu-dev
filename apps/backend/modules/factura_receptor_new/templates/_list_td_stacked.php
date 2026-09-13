<td colspan="4">
    <?php echo link_to($factura_receptor->getFacturareceptorId() ? $factura_receptor->getFacturareceptorId() : __('-'), 'factura_receptor/edit?facturareceptor_id='.$factura_receptor->getFacturareceptorId()) ?>
     - 
    <?php echo $factura_receptor->getFacturaprocesoId() ?>
     - 
    <?php echo $factura_receptor->getRegionalId() ?>
     - 
    <?php echo $factura_receptor->getUsuarioId() ?>
     - 
</td>