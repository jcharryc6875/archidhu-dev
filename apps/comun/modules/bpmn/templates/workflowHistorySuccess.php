<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object', 'jQuery');

$status = $workflowInstance->getWorkflowStatus()->getDescripcion();
$startedAt = $workflowInstance->getFechaInicio();
$completedAt = $workflowInstance->getFechaCompletado();
$variables = json_decode($workflowInstance->getVariables() ?: '{}', true);

$completedTasks = 0;
$pendingTasks = 0;
foreach ($tasks as $task) {
    if ($task->getTaskInstanceStatus() == 'completed') $completedTasks++;
    else $pendingTasks++;
}

$statusConfig = [
    'running' => ['label' => 'En Ejecución', 'icon' => 'fa-play-circle', 'class' => 'status-running'],
    'completed' => ['label' => 'Completado', 'icon' => 'fa-check-circle', 'class' => 'status-completed'],
    'failed' => ['label' => 'Fallido', 'icon' => 'fa-times-circle', 'class' => 'status-failed'],
    'suspended' => ['label' => 'Suspendido', 'icon' => 'fa-pause-circle', 'class' => 'status-suspended'],
    'cancelled' => ['label' => 'Cancelado', 'icon' => 'fa-ban', 'class' => 'status-cancelled']
];
$currentStatus = $statusConfig[$status] ?? ['label' => ucfirst($status), 'icon' => 'fa-circle', 'class' => ''];

function getUserNameHelper($userId)
{
    if (!$userId) return 'Sistema';
    $user = UsuarioPeer::retrieveByPK($userId);
    return $user ? $user->getNombreApellido() : 'Usuario #' . $userId;
}

function formatDurationHelper($seconds)
{
    if ($seconds < 60) return $seconds . 's';
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $mins = floor(($seconds % 3600) / 60);
    $parts = [];
    if ($days > 0) $parts[] = $days . 'd';
    if ($hours > 0) $parts[] = $hours . 'h';
    if ($mins > 0) $parts[] = $mins . 'm';
    return implode(' ', $parts) ?: '< 1m';
}

?>
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-history.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/js/bmpn/colors/color-picker.css">

<div class="header">
    <div class="header-content">
        <div class="header-info">
            <h1><i class="fas fa-history"></i> <?php echo htmlspecialchars($workflowInstance->getNombre() ?: 'Workflow #' . $workflowId) ?></h1>
            <p><i class="fas fa-project-diagram"></i> <?php echo htmlspecialchars($process ? $process->getNombre() : 'Proceso') ?> <?php if ($process): ?>(v<?php echo $process->getVersion() ?>)<?php endif; ?></p>
        </div>
        <div class="header-actions">
            <!--a href="<?php echo url_for('bpmn/myTasks') ?>"><i class="fas fa-tasks"></i> Mis Tareas</a-->
            <!--a href="<?php echo url_for('bpmn/processList') ?>"><i class="fas fa-list"></i> Procesos</a-->
            <?php if ($status === 'running'): ?>
                <button onclick="suspendWorkflow()" class="btn-warning"><i class="fas fa-pause"></i> Suspender</button>
                <button onclick="cancelWorkflow()" class="btn-danger"><i class="fas fa-times"></i> Cancelar</button>
            <?php elseif ($status === 'suspended'): ?>
                <button onclick="resumeWorkflow()" class="btn-success"><i class="fas fa-play"></i> Reanudar</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="info-grid">
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon primary"><i class="fas <?php echo $currentStatus['icon'] ?>"></i></div>
            <div>
                <div class="info-card-title">Estado</div>
                <div class="info-card-value"><span class="status-badge <?php echo $currentStatus['class'] ?>"><?php echo $currentStatus['label'] ?></span></div>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-row"><span class="info-row-label">Nodo Actual</span><span class="info-row-value"><?php echo htmlspecialchars($workflowInstance->getCurrentNode() ?: 'N/A') ?></span></div>
            <div class="info-row"><span class="info-row-label">Prioridad</span><span class="info-row-value"><?php $p = $workflowInstance->getPrioridad();
                                                                                                            echo $p >= 9 ? '🔴 Urgente' : ($p >= 7 ? '🟠 Alta' : ($p >= 4 ? '🟡 Normal' : '🟢 Baja')); ?></span></div>
        </div>
    </div>

    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon success"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <div class="info-card-title">Temporalidad</div>
                <div class="info-card-value"><?php echo $startedAt ? date('d/m/Y H:i', strtotime($startedAt)) : 'N/A' ?></div>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-row"><span class="info-row-label">Iniciado</span><span class="info-row-value"><?php echo $startedAt ? date('d/m/Y H:i:s', strtotime($startedAt)) : 'N/A' ?></span></div>
            <div class="info-row"><span class="info-row-label">Finalizado</span><span class="info-row-value"><?php echo $completedAt ? date('d/m/Y H:i:s', strtotime($completedAt)) : 'En progreso' ?></span></div>
            <?php if ($startedAt): ?><div class="info-row"><span class="info-row-label">Duración</span><span class="info-row-value"><?php echo formatDurationHelper(($completedAt ? strtotime($completedAt) : time()) - strtotime($startedAt)); ?></span></div><?php endif; ?>
        </div>
    </div>

    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon warning"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="info-card-title">Tareas</div>
                <div class="info-card-value"><?php echo count($tasks) ?> total</div>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-row"><span class="info-row-label">Completadas</span><span class="info-row-value" style="color:#28a745;"><i class="fas fa-check"></i> <?php echo $completedTasks ?></span></div>
            <div class="info-row"><span class="info-row-label">Pendientes</span><span class="info-row-value" style="color:#ffc107;"><i class="fas fa-clock"></i> <?php echo $pendingTasks ?></span></div>
            <?php if (count($tasks) > 0): ?><div class="info-row"><span class="info-row-label">Progreso</span><span class="info-row-value"><?php echo round(($completedTasks / count($tasks)) * 100) ?>%</span></div><?php endif; ?>
        </div>
    </div>

    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon info"><i class="fas fa-user"></i></div>
            <div>
                <div class="info-card-title">Iniciado por</div>
                <div class="info-card-value">
                    <?php
                    if ($workflowInstance->getUsuarioId()) {
                        echo htmlspecialchars(getUserNameHelper($workflowInstance->getUsuarioId()));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-row"><span class="info-row-label">Proceso</span><span class="info-row-value">
                    <?php echo htmlspecialchars($process ? $process->getNombre() : 'N/A') ?></span></div>
            <div class="info-row"><span class="info-row-label">ID Instancia</span><span class="info-row-value">#<?php echo $workflowInstance->getPrimaryKey() ?></span></div>
        </div>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('timeline', this)"><i class="fas fa-stream"></i> Timeline <span class="badge"><?php echo count($history) ?></span></button>
        <button class="tab-btn" onclick="showTab('tasks', this)"><i class="fas fa-clipboard-list"></i> Tareas <span class="badge"><?php echo count($tasks) ?></span></button>
        <button class="tab-btn" onclick="showTab('variables', this)"><i class="fas fa-database"></i> Variables <span class="badge"><?php echo count($variables) ?></span></button>
        <button class="tab-btn" onclick="showTab('attachments', this)"><i class="fas fa-paperclip"></i> Archivos</button>
        <button class="tab-btn" onclick="showTab('diagram', this)"><i class="fas fa-project-diagram"></i> Diagrama</button>
    </div>

    <!-- Tab: Timeline -->
    <div class="tab-content active" id="tab-timeline">
        <?php if (empty($history)): ?>
            <div class="empty-state"><i class="fas fa-history"></i>
                <h3>Sin historial</h3>
                <p>No hay eventos registrados.</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($history as $event):
                    $eventType = $event->getAction();
                    $iconConfig = [
                        'process_started' => ['start', 'fa-play'],
                        'process_completed' => ['end', 'fa-flag-checkered'],
                        'task_created' => ['task', 'fa-plus-circle'],
                        'task_assigned' => ['task', 'fa-user-plus'],
                        'task_completed' => ['task-completed', 'fa-check'],
                        'gateway_evaluated' => ['gateway', 'fa-code-branch'],
                        'error' => ['error', 'fa-exclamation-triangle'],
                        'process_failed' => ['error', 'fa-times-circle'],
                        'variable_updated' => ['info', 'fa-edit'],
                    ];
                    $config = $iconConfig[$eventType] ?? ['info', 'fa-info-circle'];
                ?>
                    <div class="timeline-item">
                        <div class="timeline-icon <?php echo $config[0] ?>"><i class="fas <?php echo $config[1] ?>"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-title"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $eventType))) ?></span>
                                <span class="timeline-time"><i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i:s', strtotime($event->getFechaCreacion())) ?></span>
                            </div>
                            <div class="timeline-meta">
                                <?php if ($event->getNodeId()): ?><span class="timeline-meta-item"><i class="fas fa-cube"></i> <?php echo htmlspecialchars($event->getNodeId()) ?></span><?php endif; ?>
                                <?php if ($event->getUsuarioId()): ?><span class="timeline-meta-item"><i class="fas fa-user"></i> <?php echo htmlspecialchars(getUserNameHelper($event->getUsuarioId())) ?></span><?php endif; ?>
                            </div>
                            <?php $details = $event->getComentarios();
                            if ($details): $detailsArray = json_decode($details, true);
                                if ($detailsArray && is_array($detailsArray)): ?>
                                    <div class="timeline-details"><?php foreach ($detailsArray as $key => $value): ?><div><strong><?php echo htmlspecialchars($key) ?>:</strong> <?php echo htmlspecialchars(is_array($value) ? json_encode($value) : $value) ?></div><?php endforeach; ?></div>
                            <?php endif;
                            endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Tasks -->
    <div class="tab-content" id="tab-tasks">
        <?php if (empty($tasks)): ?>
            <div class="empty-state"><i class="fas fa-clipboard-list"></i>
                <h3>Sin tareas</h3>
                <p>No hay tareas registradas.</p>
            </div>
        <?php else: ?>
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Tarea</th>
                        <th>Asignado</th>
                        <th>Creada</th>
                        <th>Completada</th>
                        <th>Duración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task):
                        $taskStatus = $task->getTaskinstancestatus();
                        $statusClasses = ['completed' => ['completed', 'fa-check'], 'pending' => ['pending', 'fa-clock'], 'in_progress' => ['in-progress', 'fa-spinner']];
                        $statusConf = $statusClasses[$taskStatus] ?? ['pending', 'fa-question'];
                    ?>
                        <tr>
                            <td>
                                <div class="task-status-icon <?php echo $statusConf[0] ?>"><i class="fas <?php echo $statusConf[1] ?>"></i></div>
                            </td>
                            <td>
                                <div class="task-name"><?php echo htmlspecialchars($task->getTaskName()) ?></div>
                                <div class="task-id"><?php echo htmlspecialchars($task->getTaskId()) ?></div>
                            </td>
                            <td><?php echo htmlspecialchars(getUserNameHelper($task->getUsuarioId())) ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($task->getFechaCreacion())) ?></td>
                            <td><?php echo $task->getFechaCompletado() ? date('d/m/Y H:i', strtotime($task->getFechaCompletado())) : '-' ?></td>
                            <td><?php if ($task->getFechaCompletado()): ?><span class="task-duration"><i class="fas fa-stopwatch"></i> <?php echo formatDurationHelper(strtotime($task->getFechaCompletado()) - strtotime($task->getFechaInicio())) ?></span><?php else: ?><span class="status-badge status-running" style="font-size:10px;">En curso</span><?php endif; ?></td>
                            <td><button class="btn-view-details" onclick="viewTaskDetails(<?php echo $task->getPrimaryKey() ?>)"><i class="fas fa-eye"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Tab: Variables -->
    <div class="tab-content" id="tab-variables">
        <?php if (empty($variables)): ?>
            <div class="empty-state"><i class="fas fa-database"></i>
                <h3>Sin variables</h3>
                <p>No hay variables registradas.</p>
            </div>
        <?php else: ?>
            <div class="variables-grid">
                <?php foreach ($variables as $name => $value): ?>
                    <div class="variable-card <?php echo strpos($name, '_') === 0 ? 'system' : '' ?>">
                        <div class="variable-name"><?php if (strpos($name, '_') === 0): ?><i class="fas fa-cog"></i><?php endif; ?> <?php echo htmlspecialchars($name) ?></div>
                        <div class="variable-value"><?php
                                                    if (is_bool($value)) echo $value ? '<i class="fas fa-check-circle" style="color:#28a745;"></i> Sí' : '<i class="fas fa-times-circle" style="color:#dc3545;"></i> No';
                                                    elseif (is_array($value)) echo '<code>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</code>';
                                                    elseif ($value === null) echo '<span style="color:#999;font-style:italic;">null</span>';
                                                    else echo htmlspecialchars((string)$value);
                                                    ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Attachments -->
    <div class="tab-content" id="tab-attachments">
        <div id="attachmentsList">
            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i>
                <h3>Cargando archivos...</h3>
            </div>
        </div>
    </div>

    <!-- Tab: Diagram -->
    <div class="tab-content" id="tab-diagram">
        <div class="bpmn-controls">
            <button onclick="zoomIn()"><i class="fas fa-search-plus"></i> Acercar</button>
            <button onclick="zoomOut()"><i class="fas fa-search-minus"></i> Alejar</button>
            <button onclick="resetZoom()"><i class="fas fa-compress-arrows-alt"></i> Ajustar</button>
        </div>
        <div class="bpmn-viewer-container">
            <div id="bpmnViewer"></div>
        </div>
    </div>
</div>

<div class="modal" id="taskDetailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles de la Tarea</h3><button class="modal-close" onclick="closeModal('taskDetailsModal')">&times;</button>
        </div>
        <div class="modal-body" id="taskDetailsContent">
            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i>
                <p>Cargando...</p>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo $path_theme; ?>assets/js/bmpn/bpmn-modeler.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bmpn/diagram-grid.umd.prod.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bmpn/color-picker-module.js"></script>
<script>
    const workflowId = <?php echo $workflowId ?>;
    const currentNode = '<?php echo addslashes($workflowInstance->getCurrentNode() ?: '') ?>';
    let bpmnViewer = null;

    function showTab(tabName, btn) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        btn.classList.add('active');
        if (tabName === 'attachments') loadAttachments();
        if (tabName === 'diagram') loadDiagram();
    }

    async function loadAttachments() {
        try {
            const response = await fetch('/comun.php/bpmn/workflowAttachments?workflowId=' + workflowId);
            const data = await response.json();
            const container = document.getElementById('attachmentsList');
            if (!data.success || !data.attachments || data.attachments.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-paperclip"></i><h3>Sin archivos</h3><p>No hay archivos adjuntos.</p></div>';
                return;
            }
            const getIcon = (mime) => {
                if (mime.includes('pdf')) return ['pdf', 'fa-file-pdf'];
                if (mime.includes('word')) return ['doc', 'fa-file-word'];
                if (mime.includes('excel')) return ['xls', 'fa-file-excel'];
                if (mime.includes('image')) return ['img', 'fa-file-image'];
                return ['other', 'fa-file'];
            };
            container.innerHTML = '<div class="attachments-grid">' + data.attachments.map(file => {
                const [ic, icon] = getIcon(file.mime_type);
                return '<div class="attachment-card"><div class="attachment-icon ' + ic + '"><i class="fas ' + icon + '"></i></div><div class="attachment-info"><div class="attachment-name">' + file.original_name + '</div><div class="attachment-meta">' + formatFileSize(file.file_size) + ' • ' + file.uploaded_by_name + '</div></div><button class="attachment-download" onclick="downloadFile(' + file.id + ')"><i class="fas fa-download"></i></button></div>';
            }).join('') + '</div>';
        } catch (e) {
            document.getElementById('attachmentsList').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Error</h3></div>';
        }
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function downloadFile(id) {
        window.location.href = '/comun.php/bpmn/downloadAttachment?id=' + id;
    }

    async function loadDiagram() {
        if (bpmnViewer) return;
        try {
            const response = await fetch('/comun.php/bpmn/processXml?process_id=<?php echo $process ? $process->getPrimaryKey() : 0 ?>');
            const data = await response.json();
            if (!data.success) throw new Error(data.error);
            bpmnViewer = new BpmnJS({
                container: '#bpmnViewer'
            });
            await bpmnViewer.importXML(data.xml);
            bpmnViewer.get('canvas').zoom('fit-viewport');
            if (currentNode) try {
                bpmnViewer.get('canvas').addMarker(currentNode, 'highlight-current');
            } catch (e) {}
            <?php foreach ($tasks as $task): if ($task->getTaskinstancestatusId() === TaskInstanceStatusBpmn::completed): ?>try {
                bpmnViewer.get('canvas').addMarker('<?php echo addslashes($task->getTaskId()) ?>', 'highlight-completed');
            } catch (e) {}
        <?php endif;
            endforeach; ?>
        } catch (e) {
            document.getElementById('bpmnViewer').innerHTML = '<div class="empty-state" style="height:100%;display:flex;flex-direction:column;justify-content:center;"><i class="fas fa-exclamation-triangle"></i><h3>Error al cargar diagrama</h3></div>';
        }
    }

    function zoomIn() {
        if (bpmnViewer) bpmnViewer.get('canvas').zoom(bpmnViewer.get('canvas').zoom() * 1.2);
    }

    function zoomOut() {
        if (bpmnViewer) bpmnViewer.get('canvas').zoom(bpmnViewer.get('canvas').zoom() * 0.8);
    }

    function resetZoom() {
        if (bpmnViewer) bpmnViewer.get('canvas').zoom('fit-viewport');
    }

    async function viewTaskDetails(taskId) {
        document.getElementById('taskDetailsModal').classList.add('active');
        try {
            const response = await fetch('/comun.php/bpmn/taskDetail?id=' + taskId);
            const data = await response.json();
            if (!data.success) throw new Error(data.error);
            const task = data.task;
            document.getElementById('taskDetailsContent').innerHTML = '<div style="display:flex;flex-direction:column;gap:15px;"><div class="info-row"><span class="info-row-label">Nombre:</span><span class="info-row-value">' + task.task_name + '</span></div><div class="info-row"><span class="info-row-label">Estado:</span><span class="info-row-value">' + task.status + '</span></div><div class="info-row"><span class="info-row-label">Asignado:</span><span class="info-row-value">' + (task.assigned_user_name || 'Sin asignar') + '</span></div>' + (task.form_data ? '<div><strong style="color:#666;">Datos del formulario:</strong><pre style="background:#f8f9fa;padding:10px;border-radius:6px;margin-top:8px;font-size:12px;">' + JSON.stringify(JSON.parse(task.form_data), null, 2) + '</pre></div>' : '') + (task.comments ? '<div><strong style="color:#666;">Comentarios:</strong><p style="margin-top:8px;">' + task.comments + '</p></div>' : '') + '</div>';
        } catch (e) {
            document.getElementById('taskDetailsContent').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error: ' + e.message + '</p></div>';
        }
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    async function suspendWorkflow() {
        if (confirm('¿Suspender este workflow?')) await workflowAction('suspend');
    }
    async function resumeWorkflow() {
        if (confirm('¿Reanudar este workflow?')) await workflowAction('resume');
    }
    async function cancelWorkflow() {
        if (confirm('¿Cancelar este workflow? Esta acción no se puede deshacer.')) await workflowAction('cancel');
    }

    async function workflowAction(action) {
        try {
            const response = await fetch('/comun.php/bpmn/workflows', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id: workflowId,
                    action: action
                })
            });
            const data = await response.json();
            if (data.success) {
                showToast('Acción realizada', 'success');
                setTimeout(() => location.reload(), 1000);
            } else throw new Error(data.error);
        } catch (e) {
            showToast('Error: ' + e.message, 'error');
        }
    }

    function showToast(msg, type) {
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    document.querySelectorAll('.modal').forEach(m => m.addEventListener('click', e => {
        if (e.target === m) closeModal(m.id);
    }));
</script>