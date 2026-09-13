<?php

/**
 * Partial para mostrar resumen de métricas
 * 
 * @param array $metrics - Datos de métricas
 * @param string $prefix - Prefijo para IDs (real/simulation)
 * @param string $title - Título de la sección
 */
?>

<div class="metrics-summary">
    <div class="metric-card">
        <div class="metric-icon primary">
            <i class="fas fa-project-diagram"></i>
        </div>
        <div class="metric-content">
            <div class="metric-value"><?php echo number_format($metrics['total_instances']) ?></div>
            <div class="metric-label">Total Instancias</div>
            <?php if ($metrics['first_instance_date']): ?>
                <div class="metric-detail">
                    Desde <?php echo format_date($metrics['first_instance_date'], 'dd/MM/yyyy') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="metric-content">
            <div class="metric-value"><?php echo number_format($metrics['completed']) ?></div>
            <div class="metric-label">Completadas</div>
            <div class="metric-detail">
                <?php
                $percentage = $metrics['total_instances'] > 0
                    ? round(($metrics['completed'] / $metrics['total_instances']) * 100, 1)
                    : 0;
                echo $percentage . '% del total';
                ?>
            </div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon warning">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="metric-content">
            <div class="metric-value"><?php echo number_format($metrics['in_progress']) ?></div>
            <div class="metric-label">En Progreso</div>
            <div class="metric-detail">
                <?php
                $percentage = $metrics['total_instances'] > 0
                    ? round(($metrics['in_progress'] / $metrics['total_instances']) * 100, 1)
                    : 0;
                echo $percentage . '% activas';
                ?>
            </div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon danger">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="metric-content">
            <div class="metric-value"><?php echo number_format($metrics['rejected']) ?></div>
            <div class="metric-label">Rechazadas</div>
            <div class="metric-detail">
                <?php
                $percentage = $metrics['total_instances'] > 0
                    ? round(($metrics['rejected'] / $metrics['total_instances']) * 100, 1)
                    : 0;
                echo $percentage . '% del total';
                ?>
            </div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon info">
            <i class="fas fa-clock"></i>
        </div>
        <div class="metric-content">
            <div class="metric-value">
                <?php
                if ($metrics['avg_completion_hours']) {
                    $hours = round($metrics['avg_completion_hours'], 1);
                    if ($hours < 24) {
                        echo $hours . 'h';
                    } else {
                        echo round($hours / 24, 1) . 'd';
                    }
                } else {
                    echo 'N/A';
                }
                ?>
            </div>
            <div class="metric-label">Tiempo Promedio</div>
            <?php if ($metrics['min_completion_hours'] && $metrics['max_completion_hours']): ?>
                <div class="metric-detail">
                    Rango: <?php echo round($metrics['min_completion_hours'], 1) ?>h - <?php echo round($metrics['max_completion_hours'], 1) ?>h
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>