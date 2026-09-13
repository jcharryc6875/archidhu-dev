<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Date', 'Object', 'jQuery', 'UserComponent');
?>
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-processmetrics.css">
<script src="<?php print $path_theme; ?>assets/js/chartjs/chart.js"></script>

<div class="process-metrics-page">
    <!-- Header del proceso -->
    <div class="page-header">
        <div class="header-content">
            <div class="process-info">
                <h1>
                    <i class="fas fa-chart-line"></i>
                    <?php echo htmlspecialchars($process->getNombre()) ?>
                </h1>
                <p class="process-description">
                    <?php echo htmlspecialchars($process->getDescripcion() ?: 'Sin descripción') ?>
                </p>
            </div>
            <div class="header-actions">
                <div class="process-badges">
                    <span class="badge badge-info">Versión <?php echo $process->getVersion() ?></span>
                    <span class="badge badge-<?php echo $process->getIsActive() == true ? 'success' : 'secondary' ?>">
                        <?php echo $process->getIsActive() == true ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs para separar métricas reales y simulaciones -->
    <ul class="nav nav-tabs mb-4" id="metricsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="real-tab" data-toggle="tab" href="#real-metrics" role="tab">
                <i class="fas fa-chart-bar"></i> Métricas Reales
                <span class="badge badge-primary ml-2"><?php echo $realMetrics['total_instances'] ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="simulation-tab" data-toggle="tab" href="#simulation-metrics" role="tab">
                <i class="fas fa-flask"></i> Simulaciones
                <span class="badge badge-info ml-2"><?php echo $simulationMetrics['total_instances'] ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="comparison-tab" data-toggle="tab" href="#comparison" role="tab">
                <i class="fas fa-balance-scale"></i> Comparación
            </a>
        </li>
    </ul>

    <div class="tab-content" id="metricsTabContent">
        <!-- TAB 1: MÉTRICAS REALES -->
        <div class="tab-pane fade show active" id="real-metrics" role="tabpanel" aria-labelledby="real-tab">
            <?php if ($realMetrics['total_instances'] == 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Este proceso aún no tiene instancias reales ejecutadas.
                </div>
            <?php else: ?>
                <?php include_partial('metrics_summary', array(
                    'metrics' => $realMetrics,
                    'prefix' => 'real',
                    'title' => 'Ejecuciones Reales'
                )) ?>

                <?php include_partial('metrics_charts', array(
                    'timelineData' => $realTimelineData,
                    'metrics' => $realMetrics,
                    'prefix' => 'real'
                )) ?>

                <?php if (count($realTaskStats) > 0): ?>
                    <?php include_partial('task_statistics', array(
                        'taskStats' => $realTaskStats,
                        'title' => 'Tareas Más Frecuentes (Real)'
                    )) ?>
                <?php endif; ?>

                <?php if (count($realTopUsers) > 0): ?>
                    <?php include_partial('top_users', array(
                        'topUsers' => $realTopUsers,
                        'avgHours' => $realMetrics['avg_completion_hours'],
                        'title' => 'Usuarios Más Activos (Real)'
                    )) ?>
                <?php endif; ?>

                <?php include_partial('recent_instances', array(
                    'instances' => $recentRealInstances,
                    'title' => 'Instancias Reales Recientes'
                )) ?>
            <?php endif; ?>
        </div>

        <!-- TAB 2: MÉTRICAS DE SIMULACIONES -->
        <div class="tab-pane fade" id="simulation-metrics" role="tabpanel" aria-labelledby="simulation-tab">
            <?php if ($simulationMetrics['total_instances'] == 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-flask"></i>
                    Este proceso aún no tiene simulaciones ejecutadas.
                    <a href="<?php echo url_for('bpmn/simulate') . '?processId=' . $process->getPrimaryKey() ?>" class="btn btn-sm btn-info ml-3">
                        <i class="fas fa-play-circle"></i> Ejecutar Simulación
                    </a>
                </div>
            <?php else: ?>
                <?php include_partial('metrics_summary', array(
                    'metrics' => $simulationMetrics,
                    'prefix' => 'simulation',
                    'title' => 'Simulaciones'
                )) ?>

                <?php include_partial('metrics_charts', array(
                    'timelineData' => $simulationTimelineData,
                    'metrics' => $simulationMetrics,
                    'prefix' => 'simulation'
                )) ?>

                <!-- Lista de simulaciones guardadas -->
                <?php if (count($simulations) > 0): ?>
                    <div class="stats-section">
                        <h3><i class="fas fa-database"></i> Simulaciones Guardadas</h3>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Estado</th>
                                        <th>Instancias</th>
                                        <th>Duración</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($simulations as $sim): ?>
                                        <tr>
                                            <td><strong>#<?php echo $sim->getBpmnSimulationId() ?></strong></td>
                                            <td><?php echo htmlspecialchars($sim->getNombre()) ?></td>
                                            <td><?php echo format_datetime($sim->getFechaInicio(), 'dd/MM/yyyy HH:mm') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = array(
                                                    'completed' => 'success',
                                                    'running' => 'warning',
                                                    'pending' => 'secondary'
                                                )[$sim->getStatusSimulation()] ?? 'info';
                                                ?>
                                                <span class="badge badge-<?php echo $statusClass ?>">
                                                    <?php echo ucfirst($sim->getStatusSimulation()) ?>
                                                </span>
                                            </td>
                                            <td><?php echo $sim->getNumInstances() ?></td>
                                            <td>
                                                <?php
                                                if ($sim->getFechaCompletado()) {
                                                    $duration = strtotime($sim->getFechaCompletado()) - strtotime($sim->getFechaInicio());
                                                    echo gmdate('H:i:s', $duration);
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo url_for('bpmn/viewSimulation?id=' . $sim->getBpmnSimulationId()) ?>"
                                                    class="btn btn-sm btn-info" title="Ver resultados">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (count($simulationTaskStats) > 0): ?>
                    <?php include_partial('task_statistics', array(
                        'taskStats' => $simulationTaskStats,
                        'title' => 'Tareas Más Frecuentes (Simulación)'
                    )) ?>
                <?php endif; ?>

                <?php include_partial('recent_instances', array(
                    'instances' => $recentSimulationInstances,
                    'title' => 'Simulaciones Recientes'
                )) ?>
            <?php endif; ?>
        </div>

        <!-- TAB 3: COMPARACIÓN -->
        <div class="tab-pane fade" id="comparison" role="tabpanel" aria-labelledby="comparison-tab">
            <div class="comparison-container">
                <h3><i class="fas fa-balance-scale"></i> Comparación Real vs Simulación</h3>

                <div class="comparison-grid">
                    <!-- Total de instancias -->
                    <div class="comparison-card">
                        <h4>Total de Instancias</h4>
                        <div class="comparison-values">
                            <div class="value-item">
                                <span class="label">Real:</span>
                                <span class="value"><?php echo number_format($realMetrics['total_instances']) ?></span>
                            </div>
                            <div class="value-item">
                                <span class="label">Simulación:</span>
                                <span class="value"><?php echo number_format($simulationMetrics['total_instances']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tasa de completado -->
                    <div class="comparison-card">
                        <h4>Tasa de Completado</h4>
                        <div class="comparison-values">
                            <div class="value-item">
                                <span class="label">Real:</span>
                                <span class="value">
                                    <?php
                                    $realRate = $realMetrics['total_instances'] > 0
                                        ? round(($realMetrics['completed'] / $realMetrics['total_instances']) * 100, 1)
                                        : 0;
                                    echo $realRate . '%';
                                    ?>
                                </span>
                            </div>
                            <div class="value-item">
                                <span class="label">Simulación:</span>
                                <span class="value">
                                    <?php
                                    $simRate = $simulationMetrics['total_instances'] > 0
                                        ? round(($simulationMetrics['completed'] / $simulationMetrics['total_instances']) * 100, 1)
                                        : 0;
                                    echo $simRate . '%';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="comparison-diff">
                            <?php
                            $diff = $simRate - $realRate;
                            $diffClass = $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : 'neutral');
                            ?>
                            <span class="diff <?php echo $diffClass ?>">
                                <?php echo $diff > 0 ? '+' : '' ?><?php echo $diff ?>%
                            </span>
                        </div>
                    </div>

                    <!-- Tiempo promedio -->
                    <div class="comparison-card">
                        <h4>Tiempo Promedio de Completado</h4>
                        <div class="comparison-values">
                            <div class="value-item">
                                <span class="label">Real:</span>
                                <span class="value">
                                    <?php
                                    if ($realMetrics['avg_completion_hours']) {
                                        $hours = round($realMetrics['avg_completion_hours'], 1);
                                        echo $hours < 24 ? $hours . 'h' : round($hours / 24, 1) . 'd';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="value-item">
                                <span class="label">Simulación:</span>
                                <span class="value">
                                    <?php
                                    if ($simulationMetrics['avg_completion_hours']) {
                                        $hours = round($simulationMetrics['avg_completion_hours'], 1);
                                        echo $hours < 24 ? $hours . 'h' : round($hours / 24, 1) . 'd';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($realMetrics['avg_completion_hours'] && $simulationMetrics['avg_completion_hours']): ?>
                            <div class="comparison-diff">
                                <?php
                                $timeDiff = $simulationMetrics['avg_completion_hours'] - $realMetrics['avg_completion_hours'];
                                $diffClass = $timeDiff < 0 ? 'positive' : ($timeDiff > 0 ? 'negative' : 'neutral');
                                ?>
                                <span class="diff <?php echo $diffClass ?>">
                                    <?php echo $timeDiff > 0 ? '+' : '' ?><?php echo round($timeDiff, 1) ?>h
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Gráfico de comparación -->
                    <div class="comparison-card wide">
                        <h4>Comparación de Estados</h4>
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="action-buttons">
        <a href="<?php echo url_for('bpmn/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
        <a onclick="viewProcessDiagram(<?php echo $process->getPrimaryKey() ?>)" class="btn btn-primary">
            <i class="fas fa-project-diagram"></i> Ver Diagrama BPMN
        </a>
        <a href="<?php echo url_for('bpmn/simulate') . '?processId=' . $process->getPrimaryKey() ?>" class="btn btn-info">
            <i class="fas fa-play-circle"></i> Nueva Simulación
        </a>
    </div>
</div>

<script>
    // Manejo manual de tabs para evitar problemas de visualización
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando tabs de métricas...');

        // Obtener todos los enlaces de tabs
        const tabLinks = document.querySelectorAll('#metricsTabs a[data-toggle="tab"]');

        tabLinks.forEach(function(tabLink) {
            tabLink.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                console.log('Cambiando a tab:', targetId);

                // Remover clases active de todos los enlaces
                tabLinks.forEach(function(link) {
                    link.classList.remove('active');
                    link.setAttribute('aria-selected', 'false');
                });

                // Agregar clase active al enlace clickeado
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Ocultar todos los paneles
                const allPanes = document.querySelectorAll('#metricsTabContent .tab-pane');
                allPanes.forEach(function(pane) {
                    pane.classList.remove('show', 'active');
                    pane.style.display = 'none';
                });

                // Mostrar el panel seleccionado
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.style.display = 'block';
                    // Usar setTimeout para asegurar que la animación funcione
                    setTimeout(function() {
                        targetPane.classList.add('show', 'active');
                    }, 10);

                    // Si es el tab de comparación, renderizar gráfico
                    if (targetId === '#comparison') {
                        setTimeout(function() {
                            renderComparisonChart();
                        }, 100);
                    }
                }
            });
        });

        // Asegurar que la primera tab esté visible al cargar
        const firstPane = document.querySelector('#real-metrics');
        if (firstPane) {
            firstPane.style.display = 'block';
            firstPane.classList.add('show', 'active');
        }
    });

    // Gráfico de comparación, Función para renderizar gráfico de comparación
    function renderComparisonChart() {
        const comparisonCtx = document.getElementById('comparisonChart');
        if (!comparisonCtx) return;

        // Destruir instancia previa si existe
        if (window.comparisonChartInstance) {
            window.comparisonChartInstance.destroy();
        }

        window.comparisonChartInstance = new Chart(comparisonCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Completadas', 'En Progreso', 'Rechazadas'],
                datasets: [{
                        label: 'Real',
                        data: [
                            <?php echo $realMetrics['completed'] ?>,
                            <?php echo $realMetrics['in_progress'] ?>,
                            <?php echo $realMetrics['rejected'] ?>
                        ],
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Simulación',
                        data: [
                            <?php echo $simulationMetrics['completed'] ?>,
                            <?php echo $simulationMetrics['in_progress'] ?>,
                            <?php echo $simulationMetrics['rejected'] ?>
                        ],
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    /**
     * Ver diagrama del proceso
     */
    async function viewProcessDiagram(processId) {
        try {
            if (!processId) return;
            javascript: jQuery.OpenModalSIMAD(`<?php echo url_for("bpmn/viewProcessDiagram") ?>?processId=${processId}`);

        } catch (error) {
            toastr.error('Error al cargar el diagrama del proceso: ' + error.message);
        }
    }
</script>