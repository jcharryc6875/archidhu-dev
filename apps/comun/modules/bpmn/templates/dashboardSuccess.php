<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object', 'jQuery');

$global = $globalMetrics['global'];
$byProcess = $globalMetrics['by_process'];
?>
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-dashboard.css">

<script src="<?php print $path_theme; ?>assets/js/chartjs/chart.js"></script>
<script src="<?php print $path_theme; ?>assets/js/chartjs/chartjs-plugin-datalabels.min.js"></script>

<div class="header">
    <h1><i class="fas fa-chart-line"></i> Dashboard BPMN</h1>
    <p>Métricas y KPIs de procesos de negocio</p>
    <div class="header-actions">
        <a href="<?php echo url_for('bpmn/index') ?>" class="btn btn-warning">
            <i class="fas fa-home"></i> Inicio
        </a>
    </div>
</div>

<!-- Tabs de navegación -->
<ul class="nav nav-tabs" style="margin-bottom: 20px; border-bottom: 2px solid #e0e0e0;">
    <li style="display: inline-block; margin-right: 5px;">
        <a href="<?php echo url_for('bpmn/dashboard?tab=active') ?>"
            class="<?php echo (!isset($currentTab) || $currentTab === 'active') ? 'active' : '' ?>"
            style="display: inline-block; padding: 10px 20px; text-decoration: none; color: <?php echo (!isset($currentTab) || $currentTab === 'active') ? '#667eea' : '#666' ?>; border-bottom: <?php echo (!isset($currentTab) || $currentTab === 'active') ? '3px solid #667eea' : 'none' ?>; font-weight: <?php echo (!isset($currentTab) || $currentTab === 'active') ? 'bold' : 'normal' ?>;">
            <i class="fas fa-chart-line"></i> Dashboard Activo
        </a>
    </li>
    <li style="display: inline-block;">
        <a href="<?php echo url_for('bpmn/dashboard?tab=completed') ?>"
            class="<?php echo (isset($currentTab) && $currentTab === 'completed') ? 'active' : '' ?>"
            style="display: inline-block; padding: 10px 20px; text-decoration: none; color: <?php echo (isset($currentTab) && $currentTab === 'completed') ? '#667eea' : '#666' ?>; border-bottom: <?php echo (isset($currentTab) && $currentTab === 'completed') ? '3px solid #667eea' : 'none' ?>; font-weight: <?php echo (isset($currentTab) && $currentTab === 'completed') ? 'bold' : 'normal' ?>;">
            <i class="fas fa-history"></i> Historial Completados
        </a>
    </li>
</ul>

<?php if (!isset($currentTab) || $currentTab === 'active'): ?>
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon primary">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format((int)$global['total_instances']) ?></h3>
                <p>Instancias Totales</p>
                <div class="trend up"><i class="fas fa-arrow-up"></i> Últimos 30 días</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format((int)$global['completed']) ?></h3>
                <p>Completadas</p>
                <?php
                $rate = $global['total_instances'] > 0
                    ? round(($global['completed'] / $global['total_instances']) * 100, 1)
                    : 0;
                ?>
                <div class="trend up"><i class="fas fa-percentage"></i> <?php echo $rate ?>% tasa éxito</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon warning">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format((int)$global['running']) ?></h3>
                <p>En Ejecución</p>
                <div class="trend">Actualmente activas</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format((int)$global['failed']) ?></h3>
                <p>Fallidas</p>
                <div class="trend down">Requieren atención</div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Procesos -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-project-diagram"></i> Procesos</h2>
                <a href="<?php echo url_for('bpmn/processList') ?>" class="btn btn-outline">Ver todos</a>
            </div>

            <?php if (empty($byProcess)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No hay procesos activos</p>
                </div>
            <?php else: ?>
                <table class="process-table">
                    <thead>
                        <tr>
                            <th>Proceso</th>
                            <th>Instancias</th>
                            <th>Activas</th>
                            <th>Completadas</th>
                            <th>Tiempo Prom.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($byProcess, 0, 5) as $proc): ?>
                            <tr>
                                <td class="process-name">
                                    <a href="<?php echo url_for('bpmn/processMetrics?processId=' . $proc['process_id']) ?>">
                                        <?php echo htmlspecialchars($proc['process_name']) ?>
                                    </a>
                                </td>
                                <td><?php echo (int)$proc['total_instances'] ?></td>
                                <td>
                                    <?php if ($proc['running'] > 0): ?>
                                        <span class="badge badge-warning"><?php echo (int)$proc['running'] ?></span>
                                    <?php else: ?>
                                        <span style="color:#999">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-success"><?php echo (int)$proc['completed'] ?></span>
                                </td>
                                <td>
                                    <?php
                                    $avgDuration = $proc['avg_duration'];
                                    if ($avgDuration) {
                                        echo round($avgDuration, 1) . 'h';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Mis Tareas Pendientes -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-tasks"></i> Mis Tareas Pendientes</h2>
                <a href="<?php echo url_for('bpmn/taskList') ?>" class="btn btn-outline">Ver todas</a>
            </div>

            <?php if (empty($myPendingTasks)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No tienes tareas pendientes 🎉</p>
                </div>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach (array_slice($myPendingTasks, 0, 5) as $task): ?>
                        <?php
                        $priority = $task->getPrioridad();
                        $priorityClass = $priority >= 8 ? 'high' : ($priority >= 5 ? 'medium' : 'low');
                        $isOverdue = $task->getFechaVencimiento() && strtotime($task->getFechaVencimiento()) < time();
                        ?>
                        <li class="task-item">
                            <div class="task-priority <?php echo $priorityClass ?>"></div>
                            <div class="task-info">
                                <div class="task-name">
                                    <?php echo htmlspecialchars($task->getTaskName()) ?>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge badge-danger">Vencida</span>
                                    <?php endif; ?>
                                </div>
                                <div class="task-meta">
                                    <?php
                                    $workflow = $task->getWorkflowInstance();
                                    echo htmlspecialchars($workflow ? $workflow->getNombre() : 'Workflow #' . $task->getWorkflowInstanceId());
                                    ?>
                                    <?php if ($task->getFechaVencimiento()): ?>
                                        • Vence: <?php echo date('d/m H:i', strtotime($task->getFechaVencimiento())) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button class="task-action" onclick="openTask(<?php echo $task->getPrimaryKey() ?>)">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid-2">
        <!-- Gráfico de tendencia -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-chart-area"></i> Tendencia (7 días)</h2>
            </div>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Workflows Activos -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-play"></i> Workflows Activos Recientes</h2>
            </div>

            <?php if (empty($activeWorkflows)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No hay workflows activos</p>
                </div>
            <?php else: ?>
                <table class="process-table">
                    <thead>
                        <tr>
                            <th>Workflow</th>
                            <th>Proceso</th>
                            <th>Iniciado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeWorkflows as $wf): ?>
                            <tr>
                                <td class="process-name"><?php echo htmlspecialchars($wf->getNombre() ?: 'Workflow #' . $wf->getId()) ?></td>
                                <td>
                                    <?php
                                    $proc = $wf->getBpmnProcess();
                                    echo htmlspecialchars($proc ? $proc->getNombre() : '-');
                                    ?>
                                </td>
                                <td><?php echo date('d/m H:i', strtotime($wf->getFechaInicio())) ?></td>
                                <td>
                                    <a onclick="viewWorkflowHistory(<?php echo $wf->getPrimaryKey() ?>)" class="btn btn-outline" style="padding: 4px 8px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- KPIs de Completados -->
    <div class="kpi-grid" style="margin-bottom: 30px;">
        <div class="kpi-card">
            <div class="kpi-icon success">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format($completedStats['total']) ?></h3>
                <p>Total Completados</p>
                <div class="trend">Últimos registros</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon primary">
                <i class="fas fa-clock"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format($completedStats['on_time']) ?></h3>
                <p>A Tiempo</p>
                <?php
                $onTimeRate = $completedStats['total'] > 0
                    ? round(($completedStats['on_time'] / $completedStats['total']) * 100, 1)
                    : 0;
                ?>
                <div class="trend up">
                    <i class="fas fa-percentage"></i> <?php echo $onTimeRate ?>% cumplimiento
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format($completedStats['delayed']) ?></h3>
                <p>Con Retrasos</p>
                <?php
                $delayedRate = $completedStats['total'] > 0
                    ? round(($completedStats['delayed'] / $completedStats['total']) * 100, 1)
                    : 0;
                ?>
                <div class="trend down"><?php echo $delayedRate ?>% con demoras</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon info">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="kpi-content">
                <h3><?php echo number_format($completedStats['avg_duration'], 1) ?>h</h3>
                <p>Duración Promedio</p>
                <div class="trend">Por workflow</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-check-circle"></i> Workflows Completados
            </h2>
            <div style="color: #666;">
                Total: <?php echo isset($pager) ? $pager->getNbResults() : 0 ?> workflows
            </div>
        </div>

        <?php if (empty($completedWorkflows)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No hay workflows completados</p>
            </div>
        <?php else: ?>
            <table class="process-table">
                <thead>
                    <tr>
                        <th>Workflow</th>
                        <th>Proceso</th>
                        <th>Finalización</th>
                        <th>Duración</th>
                        <th>Tareas</th>
                        <th>% Completado</th>
                        <th>Estado Tiempo</th>
                        <th>Eficiencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedWorkflows as $item): ?>
                        <?php
                        $wf = $item['workflow'];
                        $metrics = $item['metrics'];
                        $proc = $wf->getBpmnProcess();
                        ?>
                        <tr>
                            <td class="process-name">
                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($wf->getNombre() ?: 'Workflow #' . $wf->getWorkflowInstanceId()) ?>
                                <div style="font-size: 11px; color: #999; margin-top: 2px;">
                                    ID: <?php echo $wf->getWorkflowInstanceId() ?>
                                </div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($proc ? $proc->getNombre() : '-') ?>
                            </td>
                            <td>
                                <?php echo $wf->getFechaCompletado() ? date('d/m/Y H:i', strtotime($wf->getFechaCompletado())) : '-' ?>
                            </td>
                            <td>
                                <span class="badge badge-info" title="<?php echo $metrics['duration'] ? round($metrics['duration'], 2) . ' horas' : '' ?>">
                                    <?php echo $metrics['duration_formatted'] ?>
                                </span>
                                <?php if ($metrics['avg_task_duration']): ?>
                                    <div style="font-size: 10px; color: #999; margin-top: 2px;">
                                        ~<?php echo number_format($metrics['avg_task_duration'], 1) ?>h/tarea
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $metrics['completed_tasks'] ?></strong> / <?php echo $metrics['total_tasks'] ?>
                                <?php if ($metrics['overdue_tasks'] > 0): ?>
                                    <div style="font-size: 10px; color: #dc3545; margin-top: 2px;">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $metrics['overdue_tasks'] ?> atrasada(s)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $rate = $metrics['completion_rate'];
                                $rateColor = $rate >= 90 ? '#28a745' : ($rate >= 70 ? '#ffc107' : '#dc3545');
                                ?>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; background: #e9ecef; border-radius: 10px; height: 8px; overflow: hidden;">
                                        <div style="background: <?php echo $rateColor ?>; height: 100%; width: <?php echo $rate ?>%; transition: width 0.3s;"></div>
                                    </div>
                                    <strong style="color: <?php echo $rateColor ?>; font-size: 13px;">
                                        <?php echo $rate ?>%
                                    </strong>
                                </div>
                            </td>
                            <td>
                                <?php if ($metrics['on_time']): ?>
                                    <span class="badge badge-success" title="Completado sin retrasos">
                                        <i class="fas fa-check"></i> A tiempo
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning" title="<?php echo $metrics['overdue_tasks'] ?> tarea(s) con retraso">
                                        <i class="fas fa-clock"></i> Con retrasos
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $efficiency = $metrics['time_efficiency'];
                                if ($metrics['expected_duration']):
                                    $effColor = $efficiency >= 90 ? '#28a745' : ($efficiency >= 70 ? '#ffc107' : '#dc3545');
                                ?>
                                    <div style="text-align: center;">
                                        <div style="font-weight: bold; color: <?php echo $effColor ?>; font-size: 16px;">
                                            <?php echo $efficiency ?>%
                                        </div>
                                        <div style="font-size: 10px; color: #999;">
                                            Esperado: <?php echo $metrics['expected_duration'] ?>h
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <a onclick="viewWorkflowMetrics(<?php echo $wf->getPrimaryKey() ?>)"
                                    class="btn btn-outline"
                                    style="padding: 4px 8px; margin-right: 5px;"
                                    title="Ver métricas detalladas">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <a onclick="viewWorkflowHistory(<?php echo $wf->getPrimaryKey() ?>)"
                                    class="btn btn-outline"
                                    style="padding: 4px 8px; margin-right: 5px;"
                                    title="Ver historial">
                                    <i class="fas fa-history"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Paginación -->
            <?php if ($pager->haveToPaginate()): ?>
                <div style="margin-top: 20px; text-align: center; padding: 15px;">
                    <?php if ($pager->getPage() > 1): ?>
                        <a href="<?php echo url_for('bpmn/dashboard?tab=completed&page=' . $pager->getPreviousPage()) ?>"
                            class="btn btn-outline">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>

                    <span style="margin: 0 15px; color: #666;">
                        Página <?php echo $pager->getPage() ?> de <?php echo $pager->getLastPage() ?>
                    </span>

                    <?php if ($pager->getPage() < $pager->getLastPage()): ?>
                        <a href="<?php echo url_for('bpmn/dashboard?tab=completed&page=' . $pager->getNextPage()) ?>"
                            class="btn btn-outline">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    // Datos para el gráfico
    const trendData = <?php echo json_encode($globalMetrics['by_process'] ?? []); ?>;
    <?php if (!isset($currentTab) || $currentTab === 'active') { ?>
        // Gráfico de tendencia
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Iniciadas',
                    data: [12, 19, 15, 17, 14, 8, 10],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Completadas',
                    data: [10, 15, 13, 14, 12, 7, 9],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    <?php } ?>

    function openTask(taskId) {
        window.location.href = '/comun.php/bpmn/taskList#task-' + taskId;
    }

    /**
     * Ver historial del workflow
     */
    function viewWorkflowHistory(workflowInstanceId) {
        if (!workflowInstanceId) return;
        javascript: jQuery.OpenModalSIMAD(`<?php echo url_for("bpmn/workflowHistory") ?>?workflowinstance_id=${workflowInstanceId}`);
    }

    /**
     * Ver metricas del workflow completado
     */
    function viewWorkflowMetrics(workflowInstanceId) {
        if (!workflowInstanceId) return;
        javascript: jQuery.OpenModalSIMAD(`<?php echo url_for("bpmn/viewWorkflowMetrics") ?>?workflowinstance_id=${workflowInstanceId}`);
    }
</script>