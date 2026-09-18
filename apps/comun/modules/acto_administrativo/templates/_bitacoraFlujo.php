<?php
// UARIV-202605 - Bitácora unificada del flujo de aprobación (ACTOADMIN_ETAPA_BITACORA)
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$accion_labels = array(
    ActoadminEtapaBitacoraPeer::ACCION_APROBACION     => 'Aprobación',
    ActoadminEtapaBitacoraPeer::ACCION_RECHAZO        => 'Rechazo',
    ActoadminEtapaBitacoraPeer::ACCION_DEVOLUCION     => 'Devolución',
    ActoadminEtapaBitacoraPeer::ACCION_SOLICITUD_FIRMA=> 'Solicitud de Firma',
    ActoadminEtapaBitacoraPeer::ACCION_FIRMA          => 'Firma',
    ActoadminEtapaBitacoraPeer::ACCION_RADICACION     => 'Radicación',
    ActoadminEtapaBitacoraPeer::ACCION_FINALIZACION   => 'Finalización',
);
?>
<div class="row">
    <div class="col-md-12">
        <?php if(count($list_bitacora) == 0): ?>
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="alert alert-default"><strong>No existen Registros</strong>.</div>
                </div>
            </div>
        <?php else: ?>
            <div class="panel panel-primary">
                <div class="panel-body with-table">
                    <table class="table table-bordered table-hover table-striped responsive">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 12%;">Fecha</th>
                                <th class="text-center" style="width: 18%;">Usuario</th>
                                <th class="text-center" style="width: 12%;">Rol</th>
                                <th class="text-center" style="width: 15%;">Etapa</th>
                                <th class="text-center" style="width: 10%;">Acción</th>
                                <th class="text-center" style="width: 13%;">Estado Resultante</th>
                                <th class="text-center">Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list_bitacora as $item): ?>
                            <tr>
                                <td class="text-center"><?php echo $item->getFechaAccion() ?></td>
                                <td><?php echo $item->getUsuario() ? $item->getUsuario()->getNombreAll() : '' ?></td>
                                <td class="text-center"><?php echo $item->getRolUsuarioActoAdministvo() ? $item->getRolUsuarioActoAdministvo()->getDescripcion() : '' ?></td>
                                <td class="text-center"><?php echo $item->getActoadminEtapa() ? $item->getActoadminEtapa()->getNombre() : '' ?></td>
                                <td class="text-center">
                                    <?php echo isset($accion_labels[$item->getAccion()]) ? $accion_labels[$item->getAccion()] : $item->getAccion() ?>
                                </td>
                                <td class="text-center"><?php echo $item->getEstadoActoAdministrativo() ? $item->getEstadoActoAdministrativo()->getDescripcion() : '' ?></td>
                                <td><?php echo $item->getObservacion() ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
