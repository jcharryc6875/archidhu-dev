<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object', 'jQuery');
?>
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /*body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }*/

    .container {
        max-width: 1200px;
        width: 100%;
    }

    .header {
        text-align: center;
        color: white;
        margin-bottom: 60px;
    }

    .header h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .header p {
        font-size: 20px;
        opacity: 0.9;
        color: #333;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .card-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e6c333, #292c2f);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: white;
    }

    .card h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #333;
    }

    .card p {
        color: #666;
        line-height: 1.6;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
    }

    .stat-value {
        font-size: 36px;
        font-weight: bold;
        background: linear-gradient(135deg, #e6c333, #292c2f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-project-diagram"></i> BPMS</h1>
            <p>Sistema de Gestión de Procesos de Negocio</p>
        </div>

        <div class="cards">
            <a href="<?php echo url_for('bpmn/designer') ?>" class="card">
                <div class="card-icon">
                    <i class="fas fa-pencil-ruler"></i>
                </div>
                <h3>Diseñador</h3>
                <p>Crea y modela procesos de negocio utilizando notación BPMN 2.0</p>
            </a>

            <a href="<?php echo url_for('bpmn/taskList'); //echo url_for('bpmn/myTasks') 
                        ?>" class="card">
                <div class="card-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3>Mis Tareas</h3>
                <p>Gestiona y completa las tareas asignadas en los procesos activos</p>
            </a>

            <a href="<?php echo url_for('bpmn/dashboard') ?>" class="card">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Dashboard</h3>
                <p>Visualiza métricas y KPIs de rendimiento de procesos</p>
            </a>

            <a href="<?php echo url_for('bpmn/processList') ?>" class="card">
                <div class="card-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h3>Procesos</h3>
                <p>Administra, despliega y configura procesos de negocio</p>
            </a>

            <?php if(1 != 1){ ?>
                <a href="<?php echo url_for('bpmn/analytics') ?>" class="card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Analíticas</h3>
                    <p>Reportes avanzados y análisis de ejecución de procesos</p>
                </a>
            <?php } ?>

            <?php if(1 != 1){ ?>
                <a href="#" class="card" onclick="alert('Próximamente'); return false;">
                    <div class="card-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3>Configuración</h3>
                    <p>Configura usuarios, roles y parámetros del sistema</p>
                </a>
            <?php } ?>
        </div>

        <div class="stats" id="statsContainer">
            <div class="stat-card">
                <div class="stat-value" id="processCount">-</div>
                <div class="stat-label">Procesos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="workflowCount">-</div>
                <div class="stat-label">Workflows en Ejecución</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="taskCount">-</div>
                <div class="stat-label">Tareas Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="completionRate">-</div>
                <div class="stat-label">Tasa de Éxito</div>
            </div>
        </div>
    </div>

    <script>
        // Cargar estadísticas
        async function loadStats() {
            try {
                // Procesos activos
                const processResponse = await fetch('/comun.php/bpmn/processes');
                const processData = await processResponse.json();
                const activeProcesses = processData.processes.filter(p => p.is_active).length;
                document.getElementById('processCount').textContent = activeProcesses;

                // Workflows en ejecución
                const workflowResponse = await fetch('/comun.php/bpmn/workflows');
                const workflowData = await workflowResponse.json();                
                document.getElementById('workflowCount').textContent = workflowData.workflows?.length || 0;

                // Tareas pendientes
                const taskResponse = await fetch('/comun.php/bpmn/tasks?scope=my&status=assigned');
                const taskData = await taskResponse.json();
                document.getElementById('taskCount').textContent = taskData.total || 0;

                // Tasa de éxito
                const metricsResponse = await fetch('/comun.php/bpmn/metrics');
                const metricsData = await metricsResponse.json();
                const rate = metricsData.summary?.completion_rate || 0;
                document.getElementById('completionRate').textContent = rate.toFixed(1) + '%';

            } catch (error) {
                alert('Error cargando estadísticas: ' + error);
            }
        }

        // Cargar al inicio
        loadStats();

        // Actualizar cada 30 segundos
        setInterval(loadStats, 30000);
    </script>