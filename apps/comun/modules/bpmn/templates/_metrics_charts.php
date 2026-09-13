<?php

/**
 * Partial para mostrar gráficos de métricas
 * 
 * @param array $timelineData - Datos de línea de tiempo
 * @param array $metrics - Datos de métricas
 * @param string $prefix - Prefijo para IDs de canvas (real/simulation)
 */
?>

<div class="charts-section">
    <div class="chart-card">
        <h3><i class="fas fa-pie-chart"></i> Distribución de Estados</h3>
        <div class="chart-container">
            <canvas id="statusChart<?php echo ucfirst($prefix) ?>"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3><i class="fas fa-chart-area"></i> Tendencia de Instancias (Últimos 12 Meses)</h3>
        <div class="chart-container">
            <canvas id="timelineChart<?php echo ucfirst($prefix) ?>"></canvas>
        </div>
    </div>
</div>

<script>
    (function() {
        // Gráfico de distribución de estados
        const statusCtx<?php echo ucfirst($prefix) ?> = document.getElementById('statusChart<?php echo ucfirst($prefix) ?>');
        if (statusCtx<?php echo ucfirst($prefix) ?>) {
            new Chart(statusCtx<?php echo ucfirst($prefix) ?>.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completadas', 'En Progreso', 'Rechazadas', 'Canceladas'],
                    datasets: [{
                        data: [
                            <?php echo $metrics['completed'] ?>,
                            <?php echo $metrics['in_progress'] ?>,
                            <?php echo $metrics['rejected'] ?>,
                            <?php echo $metrics['cancelled'] ?>
                        ],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = <?php echo $metrics['total_instances'] ?>;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Gráfico de línea temporal
        const timelineCtx<?php echo ucfirst($prefix) ?> = document.getElementById('timelineChart<?php echo ucfirst($prefix) ?>');
        if (timelineCtx<?php echo ucfirst($prefix) ?>) {
            new Chart(timelineCtx<?php echo ucfirst($prefix) ?>.getContext('2d'), {
                type: 'line',
                data: {
                    labels: [<?php echo implode(',', array_map(function ($d) {
                                    return "'" . $d['month'] . "'";
                                }, $timelineData)) ?>],
                    datasets: [{
                            label: 'Total Instancias',
                            data: [<?php echo implode(',', array_map(function ($d) {
                                        return $d['total'];
                                    }, $timelineData)) ?>],
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Completadas',
                            data: [<?php echo implode(',', array_map(function ($d) {
                                        return $d['completed'];
                                    }, $timelineData)) ?>],
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Rechazadas',
                            data: [<?php echo implode(',', array_map(function ($d) {
                                        return $d['rejected'];
                                    }, $timelineData)) ?>],
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
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
    })();
</script>