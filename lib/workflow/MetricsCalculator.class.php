<?php

/**
 * Clase MetricsCalculator para SQL Server
 * Calcula y consolida métricas de procesos BPMS
 * 
 * IMPORTANTE: SQL Server PDO no permite reusar parámetros nombrados,
 * por lo que cada parámetro debe tener un nombre único.
 * 
 * Tablas:
 * - BPMN_PROCESS (BPMNPROCESS_ID, USUARIO_ID, NOMBRE, DESCRIPCION, BPMN_XML, VERSION, IS_ACTIVE)
 * - WORKFLOW_INSTANCE (WORKFLOWINSTANCE_ID, BPMNPROCESS_ID, WORKFLOWSTATUS_ID, USUARIO_ID, NOMBRE, CURRENT_NODE, VARIABLES, FECHA_INICIO, FECHA_COMPLETADO, PRIORIDAD, IS_SIMULATION)
 * - WORKFLOW_STATUS (WORKFLOWSTATUS_ID, DESCRIPCION, IS_ACTIVE)
 * - BPMN_PROCESS_METRICS (BPMNPROCESSMETRICS_ID, BPMNPROCESS_ID, METRIC_DATE, INSTANCES_STARTED, INSTANCES_COMPLETED, INSTANCES_FAILED, AVG_DURATION, MIN_DURATION, MAX_DURATION, TASKS_COMPLETED, TASKS_OVERDUE)
 * - TASK_INSTANCE (con TASKINSTANCESTATUS_ID: 1=pending, 2=assigned, 4=completed)
 */

class MetricsCalculator
{
    // IDs de estados de workflow (ajustar según tu tabla WORKFLOW_STATUS)
    const STATUS_RUNNING = 1;      // En ejecución
    const STATUS_COMPLETED = 2;    // Completado
    const STATUS_FAILED = 3;       // Fallido
    const STATUS_SUSPENDED = 4;    // Suspendido
    const STATUS_CANCELLED = 5;    // Cancelado

    // IDs de estados de tareas (ajustar según tu tabla)
    const TASK_STATUS_PENDING = 1;
    const TASK_STATUS_ASSIGNED = 2;
    const TASK_STATUS_COMPLETED = 4;

    /**
     * Obtiene métricas en tiempo real de un proceso específico
     * 
     * @param int $processId ID del proceso (BPMNPROCESS_ID)
     * @param string $startDate Fecha inicio (Y-m-d)
     * @param string $endDate Fecha fin (Y-m-d)
     * @return array
     */
    public static function getProcessMetrics($processId, $startDate = null, $endDate = null)
    {
        // Valores por defecto: últimos 30 días
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        $conn = Propel::getConnection();

        // ========================================
        // 1. MÉTRICAS DE INSTANCIAS
        // ========================================
        $sql = "
            SELECT 
                COUNT(*) as total_instances,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_running THEN 1 ELSE 0 END) as running,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_completed THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_failed THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_suspended THEN 1 ELSE 0 END) as suspended,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_cancelled THEN 1 ELSE 0 END) as cancelled
            FROM WORKFLOW_INSTANCE
            WHERE BPMNPROCESS_ID = :process_id
            AND CAST(FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
            AND (IS_SIMULATION = 0 OR IS_SIMULATION IS NULL)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':status_running' => self::STATUS_RUNNING,
            ':status_completed' => self::STATUS_COMPLETED,
            ':status_failed' => self::STATUS_FAILED,
            ':status_suspended' => self::STATUS_SUSPENDED,
            ':status_cancelled' => self::STATUS_CANCELLED
        ]);
        $instanceStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // ========================================
        // 2. MÉTRICAS DE DURACIÓN
        // ========================================
        $sql = "
            SELECT 
                AVG(DATEDIFF(HOUR, FECHA_INICIO, FECHA_COMPLETADO)) as avg_duration,
                MIN(DATEDIFF(HOUR, FECHA_INICIO, FECHA_COMPLETADO)) as min_duration,
                MAX(DATEDIFF(HOUR, FECHA_INICIO, FECHA_COMPLETADO)) as max_duration,
                AVG(DATEDIFF(MINUTE, FECHA_INICIO, FECHA_COMPLETADO)) as avg_duration_minutes
            FROM WORKFLOW_INSTANCE
            WHERE BPMNPROCESS_ID = :process_id
            AND WORKFLOWSTATUS_ID = :status_completed
            AND FECHA_COMPLETADO IS NOT NULL
            AND CAST(FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
            AND (IS_SIMULATION = 0 OR IS_SIMULATION IS NULL)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':status_completed' => self::STATUS_COMPLETED
        ]);
        $durationStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // ========================================
        // 3. MÉTRICAS DE TAREAS
        // ========================================
        $sql = "
            SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN ti.TASKINSTANCESTATUS_ID = :task_completed THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN ti.TASKINSTANCESTATUS_ID IN (:task_pending, :task_assigned) THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN ti.FECHA_VENCIMIENTO IS NOT NULL AND ti.FECHA_VENCIMIENTO < GETDATE() AND ti.TASKINSTANCESTATUS_ID != :task_completed2 THEN 1 ELSE 0 END) as overdue_tasks,
                AVG(CASE WHEN ti.FECHA_COMPLETADO IS NOT NULL THEN DATEDIFF(MINUTE, ti.FECHA_CREACION, ti.FECHA_COMPLETADO) END) as avg_task_duration
            FROM TASK_INSTANCE ti
            INNER JOIN WORKFLOW_INSTANCE wi ON ti.WORKFLOWINSTANCE_ID = wi.WORKFLOWINSTANCE_ID
            WHERE wi.BPMNPROCESS_ID = :process_id
            AND CAST(ti.FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
            AND (wi.IS_SIMULATION = 0 OR wi.IS_SIMULATION IS NULL)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':task_completed' => self::TASK_STATUS_COMPLETED,
            ':task_completed2' => self::TASK_STATUS_COMPLETED,
            ':task_pending' => self::TASK_STATUS_PENDING,
            ':task_assigned' => self::TASK_STATUS_ASSIGNED
        ]);
        $taskStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // ========================================
        // 4. TENDENCIA DIARIA (últimos 7 días)
        // ========================================
        $sql = "
            SELECT 
                CAST(FECHA_CREACION AS DATE) as fecha,
                COUNT(*) as started,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_completed THEN 1 ELSE 0 END) as completed
            FROM WORKFLOW_INSTANCE
            WHERE BPMNPROCESS_ID = :process_id
            AND CAST(FECHA_CREACION AS DATE) >= DATEADD(DAY, -7, CAST(GETDATE() AS DATE))
            AND (IS_SIMULATION = 0 OR IS_SIMULATION IS NULL)
            GROUP BY CAST(FECHA_CREACION AS DATE)
            ORDER BY fecha ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':status_completed' => self::STATUS_COMPLETED
        ]);
        $dailyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ========================================
        // 5. RENDIMIENTO POR TAREA
        // ========================================
        $sql = "
            SELECT 
                ti.TASK_NAME as task_name,
                ti.TASK_ID as task_id,
                COUNT(*) as total_executions,
                AVG(DATEDIFF(MINUTE, ti.FECHA_CREACION, ti.FECHA_COMPLETADO)) as avg_duration_minutes,
                SUM(CASE WHEN ti.FECHA_VENCIMIENTO IS NOT NULL AND ti.FECHA_COMPLETADO > ti.FECHA_VENCIMIENTO THEN 1 ELSE 0 END) as delayed_count
            FROM TASK_INSTANCE ti
            INNER JOIN WORKFLOW_INSTANCE wi ON ti.WORKFLOWINSTANCE_ID = wi.WORKFLOWINSTANCE_ID
            WHERE wi.BPMNPROCESS_ID = :process_id
            AND ti.TASKINSTANCESTATUS_ID = :task_completed
            AND CAST(ti.FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
            AND (wi.IS_SIMULATION = 0 OR wi.IS_SIMULATION IS NULL)
            GROUP BY ti.TASK_ID, ti.TASK_NAME
            ORDER BY total_executions DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':task_completed' => self::TASK_STATUS_COMPLETED
        ]);
        $taskPerformance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ========================================
        // 6. TOP USUARIOS
        // ========================================
        $sql = "
            SELECT TOP 10
                u.[USER_NAME] as username,
                ti.USUARIO_ID as assigned_user_id,
                COUNT(*) as tasks_completed,
                AVG(DATEDIFF(MINUTE, ti.FECHA_CREACION, ti.FECHA_COMPLETADO)) as avg_duration
            FROM TASK_INSTANCE ti
            INNER JOIN WORKFLOW_INSTANCE wi ON ti.WORKFLOWINSTANCE_ID = wi.WORKFLOWINSTANCE_ID
            LEFT JOIN USUARIO u ON ti.USUARIO_ID = u.USUARIO_ID
            WHERE wi.BPMNPROCESS_ID = :process_id
            AND ti.TASKINSTANCESTATUS_ID = :task_completed
            AND ti.USUARIO_ID IS NOT NULL
            AND CAST(ti.FECHA_COMPLETADO AS DATE) BETWEEN :start_date AND :end_date
            AND (wi.IS_SIMULATION = 0 OR wi.IS_SIMULATION IS NULL)
            GROUP BY ti.USUARIO_ID, u.[USER_NAME]
            ORDER BY tasks_completed DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':process_id' => $processId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':task_completed' => self::TASK_STATUS_COMPLETED
        ]);
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ========================================
        // CALCULAR KPIs
        // ========================================
        $totalInstances = (int)($instanceStats['total_instances'] ?? 0);
        $completedInstances = (int)($instanceStats['completed'] ?? 0);

        $completionRate = $totalInstances > 0
            ? round(($completedInstances / $totalInstances) * 100, 2)
            : 0;

        $totalTasks = (int)($taskStats['total_tasks'] ?? 0);
        $completedTasks = (int)($taskStats['completed_tasks'] ?? 0);
        $taskCompletionRate = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100, 2)
            : 0;

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'instances' => [
                'total' => $totalInstances,
                'running' => (int)($instanceStats['running'] ?? 0),
                'completed' => $completedInstances,
                'failed' => (int)($instanceStats['failed'] ?? 0),
                'suspended' => (int)($instanceStats['suspended'] ?? 0),
                'cancelled' => (int)($instanceStats['cancelled'] ?? 0),
                'completion_rate' => $completionRate
            ],
            'duration' => [
                'avg_hours' => round((float)($durationStats['avg_duration'] ?? 0), 2),
                'min_hours' => round((float)($durationStats['min_duration'] ?? 0), 2),
                'max_hours' => round((float)($durationStats['max_duration'] ?? 0), 2),
                'avg_minutes' => round((float)($durationStats['avg_duration_minutes'] ?? 0), 0)
            ],
            'tasks' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'pending' => (int)($taskStats['pending_tasks'] ?? 0),
                'overdue' => (int)($taskStats['overdue_tasks'] ?? 0),
                'completion_rate' => $taskCompletionRate,
                'avg_duration_minutes' => round((float)($taskStats['avg_task_duration'] ?? 0), 0)
            ],
            'daily_trend' => $dailyTrend,
            'task_performance' => $taskPerformance,
            'top_users' => $topUsers
        ];
    }

    /**
     * Obtiene métricas globales de todos los procesos
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getGlobalMetrics($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        $conn = Propel::getConnection();

        // Resumen global
        $sql = "
            SELECT 
                COUNT(*) as total_instances,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_running THEN 1 ELSE 0 END) as running,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_completed THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_failed THEN 1 ELSE 0 END) as failed
            FROM WORKFLOW_INSTANCE
            WHERE CAST(FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
            AND (IS_SIMULATION = 0 OR IS_SIMULATION IS NULL)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':status_running' => self::STATUS_RUNNING,
            ':status_completed' => self::STATUS_COMPLETED,
            ':status_failed' => self::STATUS_FAILED
        ]);
        $globalStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Por proceso - parámetros únicos para cada uso
        $sql = "
            SELECT 
                bp.BPMNPROCESS_ID as process_id,
                bp.NOMBRE as process_name,
                COUNT(wi.WORKFLOWINSTANCE_ID) as total_instances,
                SUM(CASE WHEN wi.WORKFLOWSTATUS_ID = :status_running THEN 1 ELSE 0 END) as running,
                SUM(CASE WHEN wi.WORKFLOWSTATUS_ID = :status_completed THEN 1 ELSE 0 END) as completed,
                AVG(DATEDIFF(HOUR, wi.FECHA_INICIO, wi.FECHA_COMPLETADO)) as avg_duration
            FROM BPMN_PROCESS bp
            LEFT JOIN WORKFLOW_INSTANCE wi ON bp.BPMNPROCESS_ID = wi.BPMNPROCESS_ID 
                AND CAST(wi.FECHA_CREACION AS DATE) BETWEEN :start_date AND :end_date
                AND (wi.IS_SIMULATION = 0 OR wi.IS_SIMULATION IS NULL)
            WHERE bp.IS_ACTIVE = 1
            GROUP BY bp.BPMNPROCESS_ID, bp.NOMBRE
            ORDER BY total_instances DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':status_running' => self::STATUS_RUNNING,
            ':status_completed' => self::STATUS_COMPLETED
        ]);
        $byProcess = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tareas pendientes por usuario
        $sql = "
            SELECT TOP 10
                u.[USER_NAME] as username,
                COUNT(*) as pending_tasks
            FROM TASK_INSTANCE ti
            INNER JOIN USUARIO u ON ti.USUARIO_ID = u.USUARIO_ID
            WHERE ti.TASKINSTANCESTATUS_ID IN (:task_pending, :task_assigned)
            GROUP BY ti.USUARIO_ID, u.[USER_NAME]
            ORDER BY pending_tasks DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':task_pending' => self::TASK_STATUS_PENDING,
            ':task_assigned' => self::TASK_STATUS_ASSIGNED
        ]);
        $pendingByUser = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'period' => ['start_date' => $startDate, 'end_date' => $endDate],
            'global' => $globalStats,
            'by_process' => $byProcess,
            'pending_by_user' => $pendingByUser
        ];
    }

    /**
     * Consolida métricas diarias en la tabla BPMN_PROCESS_METRICS
     * Para ejecutar vía cron: php symfony bpms:consolidate-metrics
     * 
     * @param string $date Fecha a consolidar (Y-m-d)
     * @return int Número de procesos consolidados
     */
    public static function consolidateDailyMetrics($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }

        $conn = Propel::getConnection();

        // Obtener todos los procesos activos
        $c = new Criteria();
        /*$c->add(BpmnProcessPeer::IS_ACTIVE, true);*/
        $processes = BpmnProcessPeer::doSelect($c);

        $consolidated = 0;

        foreach ($processes as $process) {
            $processId = $process->getPrimaryKey();

            try {
                // Calcular métricas del día - TODOS los parámetros con nombres únicos
                $sql = "
                    SELECT 
                        COUNT(1) as instances_started,
                        SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_completed1 AND CAST(FECHA_COMPLETADO AS DATE) = :date1 THEN 1 ELSE 0 END) as instances_completed,
                        SUM(CASE WHEN WORKFLOWSTATUS_ID = :status_failed THEN 1 ELSE 0 END) as instances_failed,
                        AVG(CASE WHEN WORKFLOWSTATUS_ID = :status_completed2 THEN CAST(DATEDIFF(MINUTE, FECHA_INICIO, FECHA_COMPLETADO) AS FLOAT) / 60.0 END) as avg_duration,
                        MIN(CASE WHEN WORKFLOWSTATUS_ID = :status_completed3 THEN CAST(DATEDIFF(MINUTE, FECHA_INICIO, FECHA_COMPLETADO) AS FLOAT) / 60.0 END) as min_duration,
                        MAX(CASE WHEN WORKFLOWSTATUS_ID = :status_completed4 THEN CAST(DATEDIFF(MINUTE, FECHA_INICIO, FECHA_COMPLETADO) AS FLOAT) / 60.0 END) as max_duration
                    FROM WORKFLOW_INSTANCE
                    WHERE BPMNPROCESS_ID = :process_id
                    AND CAST(FECHA_CREACION AS DATE) = :date2
                    AND (IS_SIMULATION = 0 OR IS_SIMULATION IS NULL)
                ";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':process_id' => $processId,
                    ':date1' => $date,
                    ':date2' => $date,
                    ':status_completed1' => self::STATUS_COMPLETED,
                    ':status_completed2' => self::STATUS_COMPLETED,
                    ':status_completed3' => self::STATUS_COMPLETED,
                    ':status_completed4' => self::STATUS_COMPLETED,
                    ':status_failed' => self::STATUS_FAILED
                ]);
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);

                // Métricas de tareas - parámetros únicos
                $sql = "
                    SELECT 
                        COUNT(CASE WHEN ti.TASKINSTANCESTATUS_ID = :task_completed1 AND CAST(ti.FECHA_COMPLETADO AS DATE) = :date1 THEN 1 END) as tasks_completed,
                        COUNT(CASE WHEN ti.FECHA_VENCIMIENTO IS NOT NULL AND ti.FECHA_VENCIMIENTO < :date2 AND ti.TASKINSTANCESTATUS_ID != :task_completed2 THEN 1 END) as tasks_overdue
                    FROM TASK_INSTANCE ti
                    INNER JOIN WORKFLOW_INSTANCE wi ON ti.WORKFLOWINSTANCE_ID = wi.WORKFLOWINSTANCE_ID
                    WHERE wi.BPMNPROCESS_ID = :process_id
                    AND (wi.IS_SIMULATION = 0 OR wi.IS_SIMULATION IS NULL)
                ";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':process_id' => $processId,
                    ':date1' => $date,
                    ':date2' => $date,
                    ':task_completed1' => self::TASK_STATUS_COMPLETED,
                    ':task_completed2' => self::TASK_STATUS_COMPLETED
                ]);
                $taskStats = $stmt->fetch(PDO::FETCH_ASSOC);

                // Buscar si ya existe registro para este día
                $c = new Criteria();
                $c->add(BpmnProcessMetricsPeer::BPMNPROCESS_ID, $processId);
                $c->add(BpmnProcessMetricsPeer::METRIC_DATE, $date);
                $existing = BpmnProcessMetricsPeer::doSelectOne($c);

                if ($existing) {
                    $metrics = $existing;
                } else {
                    $metrics = new BpmnProcessMetrics();
                    $metrics->setBpmnprocessId($processId);
                    $metrics->setMetricDate($date);
                }

                $metrics->setInstancesStarted((int)($stats['instances_started'] ?? 0));
                $metrics->setInstancesCompleted((int)($stats['instances_completed'] ?? 0));
                $metrics->setInstancesFailed((int)($stats['instances_failed'] ?? 0));
                $metrics->setAvgDuration($stats['avg_duration'] ? round((float)$stats['avg_duration'], 2) : null);
                $metrics->setMinDuration($stats['min_duration'] ? round((float)$stats['min_duration'], 2) : null);
                $metrics->setMaxDuration($stats['max_duration'] ? round((float)$stats['max_duration'], 2) : null);
                $metrics->setTasksCompleted((int)($taskStats['tasks_completed'] ?? 0));
                $metrics->setTasksOverdue((int)($taskStats['tasks_overdue'] ?? 0));
                $metrics->save();

                $consolidated++;
            } catch (Exception $e) {
                error_log("Error consolidando proceso {$processId} para fecha {$date}: " . $e->getMessage());
                continue;
            }
        }

        return $consolidated;
    }

    /**
     * Obtiene métricas históricas desde la tabla BPMN_PROCESS_METRICS
     * 
     * @param int $processId
     * @param int $days Número de días hacia atrás
     * @return array
     */
    public static function getHistoricalMetrics($processId, $days = 30)
    {
        $c = new Criteria();
        $c->add(BpmnProcessMetricsPeer::BPMNPROCESS_ID, $processId);
        $c->add(BpmnProcessMetricsPeer::METRIC_DATE, date('Y-m-d', strtotime("-{$days} days")), Criteria::GREATER_EQUAL);
        $c->addAscendingOrderByColumn(BpmnProcessMetricsPeer::METRIC_DATE);

        $metrics = BpmnProcessMetricsPeer::doSelect($c);

        $result = [];
        foreach ($metrics as $m) {
            $result[] = [
                'date' => $m->getMetricDate(),
                'instances_started' => $m->getInstancesStarted(),
                'instances_completed' => $m->getInstancesCompleted(),
                'instances_failed' => $m->getInstancesFailed(),
                'avg_duration' => $m->getAvgDuration(),
                'tasks_completed' => $m->getTasksCompleted(),
                'tasks_overdue' => $m->getTasksOverdue()
            ];
        }

        return $result;
    }

    /**
     * Obtiene los estados de workflow desde la tabla WORKFLOW_STATUS
     * Útil para mapear los IDs dinámicamente
     * 
     * @return array [descripcion => id]
     */
    public static function getWorkflowStatuses()
    {
        $conn = Propel::getConnection();
        $sql = "SELECT WORKFLOWSTATUS_ID, DESCRIPCION FROM WORKFLOW_STATUS WHERE IS_ACTIVE = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $statuses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statuses[strtolower($row['DESCRIPCION'])] = (int)$row['WORKFLOWSTATUS_ID'];
        }

        return $statuses;
    }

    /**
     * Obtiene los estados de tareas desde la tabla (si existe)
     * 
     * @return array [descripcion => id]
     */
    public static function getTaskStatuses()
    {
        try {
            $conn = Propel::getConnection();
            $sql = "SELECT TASKINSTANCESTATUS_ID, DESCRIPCION FROM TASKINSTANCE_STATUS WHERE IS_ACTIVE = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $statuses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $statuses[strtolower($row['DESCRIPCION'])] = (int)$row['TASKINSTANCESTATUS_ID'];
            }

            return $statuses;
        } catch (Exception $e) {
            // Si no existe la tabla, retornar valores por defecto
            return [
                'pending' => self::TASK_STATUS_PENDING,
                'assigned' => self::TASK_STATUS_ASSIGNED,
                'completed' => self::TASK_STATUS_COMPLETED
            ];
        }
    }

    public static function calculateProcessMetrics($processId, $isSimulation = false)
    {
        $con = Propel::getConnection();

        $sql = "
            SELECT 
                COUNT(*) as total_instances,
                SUM(CASE WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 2 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 1 THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 6 THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 7 THEN 1 ELSE 0 END) as cancelled,
                AVG(CASE 
                    WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 2 AND WORKFLOW_INSTANCE.FECHA_COMPLETADO IS NOT NULL 
                    THEN DATEDIFF(hour, WORKFLOW_INSTANCE.FECHA_INICIO, WORKFLOW_INSTANCE.FECHA_COMPLETADO) 
                END) as avg_completion_hours,
                MIN(CASE 
                    WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 2 AND WORKFLOW_INSTANCE.FECHA_COMPLETADO IS NOT NULL 
                    THEN DATEDIFF(hour, WORKFLOW_INSTANCE.FECHA_INICIO, WORKFLOW_INSTANCE.FECHA_COMPLETADO) 
                END) as min_completion_hours,
                MAX(CASE 
                    WHEN WORKFLOW_INSTANCE.WORKFLOWSTATUS_ID = 2 AND WORKFLOW_INSTANCE.FECHA_COMPLETADO IS NOT NULL 
                    THEN DATEDIFF(hour, WORKFLOW_INSTANCE.FECHA_INICIO, WORKFLOW_INSTANCE.FECHA_COMPLETADO) 
                END) as max_completion_hours,
                MIN(WORKFLOW_INSTANCE.FECHA_INICIO) as first_instance_date,
                MAX(WORKFLOW_INSTANCE.FECHA_INICIO) as last_instance_date
            FROM WORKFLOW_INSTANCE
            WHERE WORKFLOW_INSTANCE.BPMNPROCESS_ID = :process_id AND is_simulation = :is_simulation
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':process_id' => $processId, ':is_simulation' => $isSimulation ? 1 : 0));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Asegurar que los valores numéricos sean números
        if ($result) {
            $result['total_instances'] = (int)$result['total_instances'];
            $result['completed'] = (int)$result['completed'];
            $result['in_progress'] = (int)$result['in_progress'];
            $result['rejected'] = (int)$result['rejected'];
            $result['cancelled'] = (int)$result['cancelled'];
            $result['avg_completion_hours'] = $result['avg_completion_hours'] ? (float)$result['avg_completion_hours'] : null;
            $result['min_completion_hours'] = $result['min_completion_hours'] ? (float)$result['min_completion_hours'] : null;
            $result['max_completion_hours'] = $result['max_completion_hours'] ? (float)$result['max_completion_hours'] : null;
        }

        return $result;
    }

    public static function getTimelineData($processId, $isSimulation = false)
    {
        $con = Propel::getConnection();

        // Datos de los últimos 12 meses
        $sql = "
            SELECT 
                FORMAT(TASK_INSTANCE.FECHA_CREACION, 'yyyy-MM') as month,
                COUNT(*) as total,
                SUM(CASE WHEN TASK_INSTANCE.TASKINSTANCESTATUS_ID = 4 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN TASK_INSTANCE.TASKINSTANCESTATUS_ID = 8 THEN 1 ELSE 0 END) as rejected
            FROM TASK_INSTANCE
            JOIN WORKFLOW_INSTANCE ON TASK_INSTANCE.WORKFLOWINSTANCE_ID = WORKFLOW_INSTANCE.WORKFLOWINSTANCE_ID
            WHERE WORKFLOW_INSTANCE.BPMNPROCESS_ID = :process_id
                AND is_simulation = :is_simulation
                AND TASK_INSTANCE.FECHA_CREACION >= DATEADD(month, -12, GETDATE())
            GROUP BY FORMAT(TASK_INSTANCE.FECHA_CREACION, 'yyyy-MM')
            ORDER BY month
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':process_id' => $processId, ':is_simulation' => $isSimulation ? 1 : 0));

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir a números
        foreach ($results as &$row) {
            $row['total'] = (int)$row['total'];
            $row['completed'] = (int)$row['completed'];
            $row['rejected'] = (int)$row['rejected'];
        }

        return $results;
    }

    public static function getTaskStatistics($processId, $isSimulation = false)
    {
        $con = Propel::getConnection();

        $sql = "
        SELECT TOP 10
            taski.TASK_NAME as task_name,
            COUNT(*) as total_executions,
            AVG(DATEDIFF(hour, taski.FECHA_INICIO, taski.FECHA_COMPLETADO)) as avg_duration_hours,
            SUM(CASE WHEN taski.TASKINSTANCESTATUS_ID = 4 THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN taski.TASKINSTANCESTATUS_ID = 8 THEN 1 ELSE 0 END) as rejected_count
        FROM TASK_INSTANCE taski
        INNER JOIN WORKFLOW_INSTANCE wfi ON taski.WORKFLOWINSTANCE_ID = wfi.WORKFLOWINSTANCE_ID
        WHERE wfi.BPMNPROCESS_ID = :process_id
            AND taski.FECHA_COMPLETADO IS NOT NULL
            AND wfi.is_simulation = :is_simulation
        GROUP BY taski.TASK_NAME
        ORDER BY total_executions DESC
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':process_id' => $processId, ':is_simulation' => $isSimulation ? 1 : 0));

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir a números
        foreach ($results as &$row) {
            $row['total_executions'] = (int)$row['total_executions'];
            $row['avg_duration_hours'] = $row['avg_duration_hours'] ? (float)$row['avg_duration_hours'] : null;
            $row['completed_count'] = (int)$row['completed_count'];
            $row['rejected_count'] = (int)$row['rejected_count'];
        }

        return $results;
    }

    public static function getTopUsers($processId, $isSimulation = false)
    {
        $con = Propel::getConnection();

        $sql = "
            SELECT TOP 10
                CONCAT(utk.NOMBRE,' ',utk.APELLIDO) as username,
                COUNT(*) as tasks_completed,
                AVG(DATEDIFF(hour, tki.FECHA_INICIO, tki.FECHA_COMPLETADO)) as avg_completion_hours
            FROM TASK_INSTANCE tki
            INNER JOIN WORKFLOW_INSTANCE wfi ON tki.WORKFLOWINSTANCE_ID = wfi.WORKFLOWINSTANCE_ID
            INNER JOIN USUARIO utk ON tki.USUARIO_ID = utk.USUARIO_ID
            WHERE wfi.BPMNPROCESS_ID = :process_id
                AND tki.TASKINSTANCESTATUS_ID = 4
                AND tki.USUARIO_ID IS NOT NULL
                AND wfi.is_simulation = :is_simulation
            GROUP BY CONCAT(utk.NOMBRE,' ',utk.APELLIDO)
            ORDER BY tasks_completed DESC
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':process_id' => $processId, ':is_simulation' => $isSimulation ? 1 : 0));

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir a números
        foreach ($results as &$row) {
            $row['tasks_completed'] = (int)$row['tasks_completed'];
            $row['avg_completion_hours'] = $row['avg_completion_hours'] ? (float)$row['avg_completion_hours'] : null;
        }

        return $results;
    }

    public static function getProcessStats($processId, $isSimulation = false)
    {
        $con = Propel::getConnection();

        $sql = "
            SELECT 
                COUNT(*) as total_instances,
                SUM(CASE WHEN wfi.WORKFLOWSTATUS_ID = 2 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN wfi.WORKFLOWSTATUS_ID = 1 THEN 1 ELSE 0 END) as in_progress,
                MAX(wfi.FECHA_COMPLETADO) as last_execution
            FROM WORKFLOW_INSTANCE wfi
            WHERE wfi.BPMNPROCESS_ID = :process_id
                AND wfi.is_simulation = :is_simulation
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':process_id' => $processId, ':is_simulation' => $isSimulation ? 1 : 0));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $result['total_instances'] = (int)$result['total_instances'];
            $result['completed'] = (int)$result['completed'];
            $result['in_progress'] = (int)$result['in_progress'];
        }

        return $result;
    }

    /**
     * Obtener lista de simulaciones guardadas
     */
    public static function getSimulationsList($processId)
    {
        $c = new Criteria();
        $c->add(BpmnSimulationPeer::BPMNPROCESS_ID, $processId);
        $c->addDescendingOrderByColumn(BpmnSimulationPeer::FECHA_INICIO);

        return BpmnSimulationPeer::doSelect($c);
    }

    public static function calculateSimulationResults($instanceId)
    {
        $instance = WorkflowInstancePeer::retrieveByPK($instanceId);
        if (!$instance) return array();

        $startTime = strtotime($instance->getFechaInicio());
        $endTime = strtotime($instance->getFechaCompletado());

        return array(
            'duration_seconds' => $endTime - $startTime,
            'duration_formatted' => self::formatDuration($endTime - $startTime),
            'variables' => json_decode($instance->getVariables(), true),
            'status' => 'completed'
        );
    }

    public static function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
