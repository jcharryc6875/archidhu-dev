<?php

/**
 * Partial para mostrar usuarios más activos
 * 
 * @param array $topUsers - Lista de usuarios
 * @param float $avgHours - Promedio de horas de completado
 * @param string $title - Título de la sección
 */
?>

<div class="stats-section">
    <h3><i class="fas fa-users"></i> <?php echo $title ?></h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th class="text-center">Instancias Creadas</th>
                    <th class="text-center">Tiempo Promedio</th>
                    <th class="text-center">Desempeño</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topUsers as $index => $user): ?>
                    <tr>
                        <td>
                            <?php if ($index < 3): ?>
                                <i class="fas fa-medal" style="color: <?php echo array('gold', 'silver', '#cd7f32')[$index] ?>"></i>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($user['username'] ?: 'Usuario #' . $user['user_id']) ?></strong>
                        </td>
                        <td class="text-center"><?php echo $user['instances_created'] ?></td>
                        <td class="text-center">
                            <?php
                            if ($user['avg_completion_hours']) {
                                $hours = round($user['avg_completion_hours'], 1);
                                echo $hours < 24 ? $hours . 'h' : round($hours / 24, 1) . 'd';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            if ($user['avg_completion_hours'] && $avgHours) {
                                if ($user['avg_completion_hours'] < $avgHours) {
                                    echo '<span class="badge badge-success">Por encima del promedio</span>';
                                } else {
                                    echo '<span class="badge badge-info">Normal</span>';
                                }
                            } else {
                                echo '<span class="badge badge-secondary">N/A</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>