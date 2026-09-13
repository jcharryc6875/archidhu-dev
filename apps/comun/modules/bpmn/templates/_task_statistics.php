<?php

/**
 * Partial para mostrar estadísticas de tareas
 * 
 * @param array $taskStats - Estadísticas de tareas
 * @param string $title - Título de la sección
 */
?>

<div class="stats-section">
    <h3><i class="fas fa-tasks"></i> <?php echo $title ?></h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tarea</th>
                    <th class="text-center">Total Ejecuciones</th>
                    <th class="text-center">Completadas</th>
                    <th class="text-center">Rechazadas</th>
                    <th class="text-center">Duración Promedio</th>
                    <th class="text-center">Tasa Éxito</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taskStats as $task): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($task['task_name']) ?></strong>
                        </td>
                        <td class="text-center"><?php echo $task['total_executions'] ?></td>
                        <td class="text-center">
                            <span class="badge badge-success"><?php echo $task['completed_count'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger"><?php echo $task['rejected_count'] ?></span>
                        </td>
                        <td class="text-center">
                            <?php
                            if ($task['avg_duration_hours']) {
                                $hours = round($task['avg_duration_hours'], 1);
                                echo $hours < 24 ? $hours . 'h' : round($hours / 24, 1) . 'd';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            $successRate = $task['total_executions'] > 0
                                ? round(($task['completed_count'] / $task['total_executions']) * 100, 1)
                                : 0;
                            ?>
                            <span class="badge badge-<?php echo $successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger') ?>">
                                <?php echo $successRate ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>