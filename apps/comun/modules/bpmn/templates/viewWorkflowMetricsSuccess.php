<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object', 'jQuery');

$wf = $workflow;
$proc = $process;
$met = $metrics;
?>

<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-taskmetrics.css">

<div class="metrics-header">
    <div>
        <h2>
            <i class="fas fa-chart-line"></i>
            Métricas Detalladas
        </h2>
        <div class="workflow-info">
            Workflow: <?php echo htmlspecialchars($wf->getNombre() ?: '#' . $wf->getPrimaryKey()) ?>
            | Proceso: <?php echo htmlspecialchars($proc ? $proc->getNombre() : '-') ?>
        </div>
    </div>
    <button class="close-btn" onclick="window.parent.jQuery.fancybox.close();">
        <i class="fas fa-times"></i> Cerrar
    </button>
</div>

<div class="metrics-container">
    <!-- KPIs Principales -->
    <div class="metrics-grid">
        <div class="metric-card <?php echo $met['on_time'] ? 'success' : 'warning' ?>">
            <div class="icon">
                <i class="fas fa-<?php echo $met['on_time'] ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            </div>
            <div class="value">
                <?php echo $met['on_time'] ? 'A Tiempo' : 'Con Retrasos' ?>
            </div>
            <div class="label">Estado de Tiempo</div>
            <?php if ($met['overdue_tasks'] > 0): ?>
                <div class="sublabel"><?php echo $met['overdue_tasks'] ?> tarea(s) atrasada(s)</div>
            <?php endif; ?>
        </div>

        <div class="metric-card info">
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="value"><?php echo $met['duration_formatted'] ?></div>
            <div class="label">Duración Total</div>
            <?php if ($met['duration']): ?>
                <div class="sublabel"><?php echo round($met['duration'], 2) ?> horas</div>
            <?php endif; ?>
        </div>

        <div class="metric-card success">
            <div class="icon"><i class="fas fa-tasks"></i></div>
            <div class="value"><?php echo $met['completed_tasks'] ?> / <?php echo $met['total_tasks'] ?></div>
            <div class="label">Tareas Completadas</div>
            <div class="sublabel"><?php echo $met['completion_rate'] ?>% completado</div>
        </div>

        <?php if ($met['expected_duration']): ?>
            <div class="metric-card <?php echo $met['time_efficiency'] >= 90 ? 'success' : ($met['time_efficiency'] >= 70 ? 'warning' : 'danger') ?>">
                <div class="icon"><i class="fas fa-tachometer-alt"></i></div>
                <div class="value"><?php echo $met['time_efficiency'] ?>%</div>
                <div class="label">Eficiencia de Tiempo</div>
                <div class="sublabel">Esperado: <?php echo $met['expected_duration'] ?>h</div>
            </div>
        <?php endif; ?>

        <?php if ($met['avg_task_duration']): ?>
            <div class="metric-card info">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="value"><?php echo number_format($met['avg_task_duration'], 1) ?>h</div>
                <div class="label">Promedio por Tarea</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Métricas por Fase -->
    <?php if (!empty($phaseMetrics)): ?>
        <div class="section">
            <h3 class="section-title">
                <i class="fas fa-layer-group"></i>
                Métricas por Fase
            </h3>
            <div class="phase-metrics">
                <?php foreach ($phaseMetrics as $phase): ?>
                    <div class="phase-card">
                        <h4><?php echo htmlspecialchars($phase['name']) ?></h4>
                        <div class="phase-stat">
                            <span class="label">Tareas:</span>
                            <span class="value"><?php echo $phase['task_count'] ?></span>
                        </div>
                        <div class="phase-stat">
                            <span class="label">Duración total:</span>
                            <span class="value"><?php echo round($phase['total_duration'], 1) ?>h</span>
                        </div>
                        <?php if (isset($phase['avg_duration'])): ?>
                            <div class="phase-stat">
                                <span class="label">Promedio/tarea:</span>
                                <span class="value"><?php echo round($phase['avg_duration'], 1) ?>h</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Detalle de Tareas -->
    <div class="section">
        <h3 class="section-title">
            <i class="fas fa-list-check"></i>
            Detalle de Tareas
        </h3>
        <table class="task-metrics-table">
            <thead>
                <tr>
                    <th>Tarea</th>
                    <th>Asignado a</th>
                    <th>Estado</th>
                    <th>Duración</th>
                    <th>Cumplimiento</th>
                    <th>Completado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasksWithMetrics as $item): ?>
                    <?php
                    $task = $item['task'];
                    $taskMet = $item['metrics'];
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($task->getTaskName()) ?></strong>
                            <?php if ($task->getPrioridad()): ?>
                                <span class="badge badge-<?php echo $task->getPrioridad() >= 8 ? 'danger' : ($task->getPrioridad() >= 5 ? 'warning' : 'secondary') ?>">
                                    Prioridad: <?php echo $task->getPrioridad() ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $assignee = $taskMet['assignee'];
                            echo $assignee ? htmlspecialchars($assignee) : '-';
                            ?>
                        </td>
                        <td>
                            <?php if ($taskMet['status'] === 'completed'): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Completada
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <?php echo ucfirst($taskMet['status']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $taskMet['duration_formatted'] ?></strong>
                            <?php if ($taskMet['duration']): ?>
                                <div style="font-size: 11px; color: #999;">
                                    <?php echo round($taskMet['duration'], 2) ?>h
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($taskMet['on_time'] === true): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> A tiempo
                                </span>
                                <?php if ($taskMet['delay_formatted']): ?>
                                    <div style="font-size: 11px; color: #28a745; margin-top: 3px;">
                                        <?php echo $taskMet['delay_formatted'] ?>
                                    </div>
                                <?php endif; ?>
                            <?php elseif ($taskMet['on_time'] === false): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Retrasada
                                </span>
                                <?php if ($taskMet['delay_formatted']): ?>
                                    <div style="font-size: 11px; color: #856404; margin-top: 3px;">
                                        <?php echo $taskMet['delay_formatted'] ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-secondary">Sin fecha límite</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($taskMet['completion_date']): ?>
                                <?php echo date('d/m/Y H:i', strtotime($taskMet['completion_date'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Timeline de Ejecución -->
    <?php if (!empty($timeline)): ?>
        <div class="section">
            <h3 class="section-title">
                <i class="fas fa-stream"></i>
                Timeline de Ejecución
            </h3>
            <div class="timeline">
                <?php foreach ($timeline as $event): ?>
                    <div class="timeline-item <?php echo $event['type'] ?>">
                        <div class="timeline-date">
                            <?php echo date('d/m/Y H:i:s', strtotime($event['date'])) ?>
                        </div>
                        <div class="timeline-description">
                            <?php echo htmlspecialchars($event['description']) ?>
                        </div>
                        <?php if ($event['user']): ?>
                            <div class="timeline-user">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($event['user']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>