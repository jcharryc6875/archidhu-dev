<td colspan="5">
    <?php echo link_to($prov_usuario_area->getProvUsuarioAreaId() ? $prov_usuario_area->getProvUsuarioAreaId() : __('-'), 'prov_usuario_area/edit?prov_usuario_area_id='.$prov_usuario_area->getProvUsuarioAreaId()) ?>
     - 
    <?php echo $prov_usuario_area->getProvEstadoAprobadorId() ?>
     - 
    <?php echo $prov_usuario_area->getUsuarioId() ?>
     - 
    <?php echo $prov_usuario_area->getPaisId() ?>
     - 
    <?php echo $prov_usuario_area->getProvAreaAprobadoraId() ?>
     - 
</td>