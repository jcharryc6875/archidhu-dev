<td colspan="2">
    <?php echo link_to($tipo_impuesto->getTipoimpuestoId() ? $tipo_impuesto->getTipoimpuestoId() : __('-'), 'tipo_impuesto/edit?tipoimpuesto_id='.$tipo_impuesto->getTipoimpuestoId()) ?>
     - 
    <?php echo $tipo_impuesto->getDescripcion() ?>
     - 
</td>