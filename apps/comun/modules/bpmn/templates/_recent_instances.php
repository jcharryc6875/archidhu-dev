<?php

/**
 * Partial para mostrar instancias recientes
 * 
 * @param array $instances - Lista de instancias
 * @param string $title - Título de la sección
 */
?>

<div class="stats-section">
    <h3><i class="fas fa-history"></i> <?php echo $title ?></h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Iniciado Por</th>
                    <th>Fecha Inicio</th>
                    <th>Estado</th>
                    <th>Duración</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($instances) == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-inbox"></i> No hay instancias disponibles
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($instances as $instance): ?>
                        <tr>
                            <td><strong>#<?php echo $instance->getWorkflowInstanceId() ?></strong></td>
                            <td><?php echo htmlspecialchars($instance->getNombre()) ?></td>
                            <td>
                                <?php
                                $usuario = $instance->getUsuario();
                                echo $usuario ? htmlspecialchars($usuario->getNombreApellido()) : 'N/A';
                                ?>
                            </td>
                            <td><?php echo format_datetime($instance->getFechaInicio(), 'dd/MM/yyyy HH:mm') ?></td>
                            <td>
                                <?php
                                $statusMap = array(
                                    1 => array('label' => 'En Progreso', 'class' => 'warning'),
                                    2 => array('label' => 'Completado', 'class' => 'success'),
                                    3 => array('label' => 'Rechazado', 'class' => 'danger'),
                                    4 => array('label' => 'Cancelado', 'class' => 'secondary')
                                );
                                $status = isset($statusMap[$instance->getWorkflowStatusId()])
                                    ? $statusMap[$instance->getWorkflowStatusId()]
                                    : array('label' => 'Desconocido', 'class' => 'info');
                                ?>
                                <span class="badge badge-<?php echo $status['class'] ?>">
                                    <?php echo $status['label'] ?>
                                </span>
                                <?php if ($instance->getIsSimulation()): ?>
                                    <span class="badge badge-warning ml-1">
                                        <i class="fas fa-flask"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $created = strtotime($instance->getFechaInicio());
                                $completed = $instance->getFechaCompletado()
                                    ? strtotime($instance->getFechaCompletado())
                                    : time();
                                $hours = round(($completed - $created) / 3600, 1);
                                echo $hours < 24 ? $hours . 'h' : round($hours / 24, 1) . 'd';
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo url_for('workflow_execution/view?id=' . $instance->getWorkflowinstanceId()) ?>"
                                    class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($instance->getIsSimulation()): ?>
                                    <a href="<?php echo url_for('bpmn/viewSimulation?id=' . $instance->getBpmnsimulationId()) ?>"
                                        class="btn btn-sm btn-warning" title="Ver simulación">
                                        <i class="fas fa-flask"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>