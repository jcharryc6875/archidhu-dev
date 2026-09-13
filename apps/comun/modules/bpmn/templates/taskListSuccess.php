<?php

/**
 * Vista de Tareas Pendientes con Aprobación Masiva
 * Ubicación: apps/frontend/modules/bpmn/templates/taskListSuccess.php
 */
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('Object', 'jQuery', 'UserComponent');
?>

<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-taskbatch.css">

<style>

</style>

<!-- Header -->
<div class="task-list-header">
    <h2>
        <i class="fas fa-tasks"></i>
        Tareas Pendientes
    </h2>
    <div class="header-actions">
        <button class="btn btn-info" onclick="refreshTasks()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
        <a href="<?php echo url_for('bpmn/index') ?>" class="btn btn-warning">
            <i class="fas fa-home"></i> Inicio
        </a>
    </div>
</div>

<!-- Barra de acciones masivas -->
<div class="bulk-actions-bar" id="bulkActionsBar">
    <div class="selected-count">
        <i class="fas fa-check-square"></i>
        <span id="selectedCount">0</span> tareas seleccionadas
    </div>
    <div class="bulk-buttons">
        <button class="btn-bulk btn-bulk-approve" onclick="openBulkApproveModal()">
            <i class="fas fa-check-double"></i> Aprobar Seleccionadas
        </button>
        <button class="btn-bulk btn-bulk-reject" onclick="openBulkRejectModal()">
            <i class="fas fa-times"></i> Rechazar Seleccionadas
        </button>
        <button class="btn-bulk btn-bulk-clear" onclick="clearSelection()">
            <i class="fas fa-eraser"></i> Limpiar Selección
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="filters-bar">
    <div class="filter-group">
        <label>Proceso:</label>
        <select id="filterProcess" onchange="applyFilters()">
            <option value="">Todos</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Prioridad:</label>
        <select id="filterPriority" onchange="applyFilters()">
            <option value="">Todas</option>
            <option value="high">Alta</option>
            <option value="medium">Media</option>
            <option value="low">Baja</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Estado:</label>
        <select id="filterStatus" onchange="applyFilters()">
            <option value="">Todos</option>
            <option value="pending">Pendiente</option>
            <option value="overdue">Vencida</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Buscar:</label>
        <input type="text" id="filterSearch" placeholder="Nombre de tarea..." oninput="applyFilters()">
    </div>
    <div class="filter-group" style="margin-left: auto;">
        <label>
            <input type="checkbox" id="filterBulkEligible" onchange="applyFilters()">
            Solo aprobables en lote
        </label>
    </div>
</div>

<!-- Tabla de tareas -->
<div class="task-table-container">
    <table class="task-table">
        <thead>
            <tr>
                <th class="checkbox-col">
                    <input type="checkbox" class="task-checkbox" id="selectAll" onchange="toggleSelectAll()">
                </th>
                <th>Tarea</th>
                <th>Proceso</th>
                <th>Prioridad</th>
                <th>Fecha Límite</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="taskTableBody">
            <tr>
                <td colspan="7" class="text-center">Cargando tareas...</td>
            </tr>
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="pagination-container" id="paginationContainer" style="display: none;">
        <div class="pagination-info">
            Mostrando <span id="showingFrom">0</span> - <span id="showingTo">0</span> de <span id="totalTasks">0</span> tareas
        </div>
        <div class="pagination-buttons" id="paginationButtons">
            <!-- Generado dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal de Aprobación Masiva -->
<div class="modal" id="bulkApproveModal">
    <div class="modal-content">
        <div class="modal-header approve">
            <h3>
                <i class="fas fa-check-double"></i>
                Aprobar Tareas en Lote
            </h3>
            <button class="btn-close" onclick="closeModal('bulkApproveModal')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Resumen de tareas -->
            <div class="selected-tasks-summary">
                <h4><i class="fas fa-list"></i> Tareas a aprobar (<span id="approveTaskCount">0</span>):</h4>
                <div id="approveTaskList">
                    <!-- Lista de tareas -->
                </div>
            </div>

            <!-- Formulario -->
            <div class="form-group">
                <label for="bulkApproveComment">
                    <i class="fas fa-comment"></i> Comentario (opcional):
                </label>
                <textarea id="bulkApproveComment" placeholder="Agregar un comentario para todas las tareas..."></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="skipWithForms" checked>
                    Omitir tareas que requieren formulario
                </label>
                <small style="display: block; color: #666; margin-top: 5px;">
                    Las tareas con formularios requeridos deben completarse individualmente.
                </small>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger" onclick="closeModal('bulkApproveModal')">Cancelar</button>
            <button class="btn btn-success" onclick="executeBulkApprove()">
                <i class="fas fa-check-double"></i> Aprobar <span id="approveCount">0</span> Tareas
            </button>
        </div>

        <!-- Overlay de procesamiento -->
        <div class="processing-overlay" id="approveProcessingOverlay">
            <div class="progress-container">
                <i class="fas fa-cog fa-spin" style="font-size: 3em; color: #667eea; margin-bottom: 20px;"></i>
                <h3>Procesando tareas...</h3>
                <div class="progress-bar">
                    <div class="progress-bar-fill" id="approveProgressBar"></div>
                </div>
                <div class="progress-text" id="approveProgressText">0 de 0 tareas procesadas</div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="processing-overlay" id="approveResultsOverlay">
            <div class="results-summary">
                <div class="results-icon" id="approveResultIcon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 id="approveResultTitle">Proceso completado</h3>
                <div class="results-stats">
                    <div class="stat-item success">
                        <div class="value" id="approveSuccessCount">0</div>
                        <div class="label">Aprobadas</div>
                    </div>
                    <div class="stat-item failed">
                        <div class="value" id="approveFailedCount">0</div>
                        <div class="label">Fallidas</div>
                    </div>
                </div>
                <div class="error-details" id="approveErrorDetails" style="display: none;">
                    <!-- Errores detallados -->
                </div>
                <button class="btn btn-primary" onclick="closeBulkModal('bulkApproveModal')" style="margin-top: 20px;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rechazo Masivo -->
<div class="modal" id="bulkRejectModal">
    <div class="modal-content" style="position: relative;">
        <div class="modal-header reject">
            <h3>
                <i class="fas fa-times-circle"></i>
                Rechazar Tareas en Lote
            </h3>
            <button class="btn-close" onclick="closeModal('bulkRejectModal')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Resumen de tareas -->
            <div class="selected-tasks-summary">
                <h4><i class="fas fa-list"></i> Tareas a rechazar (<span id="rejectTaskCount">0</span>):</h4>
                <div id="rejectTaskList">
                    <!-- Lista de tareas -->
                </div>
            </div>

            <!-- Formulario -->
            <div class="form-group">
                <label for="bulkRejectReason">
                    <i class="fas fa-comment"></i> Motivo del rechazo (requerido):
                </label>
                <textarea id="bulkRejectReason" placeholder="Ingrese el motivo del rechazo..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('bulkRejectModal')">Cancelar</button>
            <button class="btn btn-danger" onclick="executeBulkReject()">
                <i class="fas fa-times"></i> Rechazar <span id="rejectCount">0</span> Tareas
            </button>
        </div>

        <!-- Overlay de procesamiento -->
        <div class="processing-overlay" id="rejectProcessingOverlay">
            <div class="progress-container">
                <i class="fas fa-cog fa-spin" style="font-size: 3em; color: #dc3545; margin-bottom: 20px;"></i>
                <h3>Procesando rechazos...</h3>
                <div class="progress-bar">
                    <div class="progress-bar-fill" id="rejectProgressBar" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);"></div>
                </div>
                <div class="progress-text" id="rejectProgressText">0 de 0 tareas procesadas</div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="processing-overlay" id="rejectResultsOverlay">
            <div class="results-summary">
                <div class="results-icon" id="rejectResultIcon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 id="rejectResultTitle">Proceso completado</h3>
                <div class="results-stats">
                    <div class="stat-item success">
                        <div class="value" id="rejectSuccessCount">0</div>
                        <div class="label">Rechazadas</div>
                    </div>
                    <div class="stat-item failed">
                        <div class="value" id="rejectFailedCount">0</div>
                        <div class="label">Fallidas</div>
                    </div>
                </div>
                <div class="error-details" id="rejectErrorDetails" style="display: none;">
                    <!-- Errores detallados -->
                </div>
                <button class="btn btn-primary" onclick="closeBulkModal('bulkRejectModal')" style="margin-top: 20px;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de tarea individual -->
<div class="modal task-detail-modal" id="taskDetailModal">
    <div class="modal-content">
        <div class="task-detail-header" id="taskDetailHeader">
            <button class="btn-close" onclick="closeModal('taskDetailModal')" style="position: absolute; right: 15px; top: 15px;">&times;</button>
            <h3>
                <i class="fas fa-tasks"></i>
                <span id="taskDetailName">Cargando...</span>
                <span class="task-id-badge" id="taskDetailIdBadge">#0</span>
            </h3>
            <div class="process-name">
                <i class="fas fa-project-diagram"></i>
                <span id="taskDetailProcess">-</span>
            </div>
        </div>

        <div class="task-detail-body">
            <!-- Grid de información -->
            <div class="task-info-grid">
                <div class="task-info-item">
                    <div class="label"><i class="fas fa-flag"></i> Estado</div>
                    <div class="value" id="taskDetailStatus">-</div>
                </div>

                <div class="task-info-item">
                    <div class="label"><i class="fas fa-thermometer-half"></i> Prioridad</div>
                    <div class="value" id="taskDetailPriority">-</div>
                </div>

                <div class="task-info-item">
                    <div class="label"><i class="fas fa-user"></i> Asignado a</div>
                    <div class="value" id="taskDetailAssignee">-</div>
                </div>

                <div class="task-info-item">
                    <div class="label"><i class="fas fa-calendar-alt"></i> Fecha límite</div>
                    <div class="value" id="taskDetailDueDate">-</div>
                </div>

                <div class="task-info-item">
                    <div class="label"><i class="fas fa-play-circle"></i> Fecha creación</div>
                    <div class="value" id="taskDetailCreatedAt">-</div>
                </div>

                <div class="task-info-item">
                    <div class="label"><i class="fas fa-hashtag"></i> Instancia</div>
                    <div class="value" id="taskDetailInstance">-</div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="task-description-section" id="taskDescriptionSection" style="display: none;">
                <h4><i class="fas fa-align-left"></i> Descripción</h4>
                <p id="taskDetailDescription"></p>
            </div>

            <!-- Comentarios -->
            <div class="task-description-section" id="taskCommentsSection" style="display: none;">
                <h4><i class="fas fa-comment"></i> Comentarios</h4>
                <p id="taskDetailComments"></p>
            </div>

            <!-- Alerta de formulario requerido -->
            <div class="form-required-alert" id="formRequiredAlert" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content">
                    <div class="alert-title">Esta tarea requiere completar un formulario</div>
                    <div class="alert-text">Debes llenar los campos marcados como obligatorios antes de aprobar.</div>
                </div>
                <button class="btn-fill-form" onclick="openTaskForm(currentDetailTaskId)">
                    <i class="fas fa-edit"></i> Completar Formulario
                </button>
            </div>

            <!-- Sección de variables del proceso -->
            <div class="process-variables-section" id="processVariablesSection" style="display: none;">
                <h4>
                    <i class="fas fa-database"></i>
                    Variables del Proceso
                </h4>
                <table class="process-vars-table">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Variable</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody id="processVariablesBody">
                        <!-- Se genera dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Timeline de actividad -->
            <div class="task-activity-section" id="taskActivitySection" style="display: none;">
                <h4><i class="fas fa-history"></i> Actividad Reciente</h4>
                <div class="activity-timeline" id="taskActivityTimeline">
                    <!-- Se genera dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Subir nuevos archivos -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-paperclip"></i>
                Adjuntar Archivos
            </div>

            <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Arrastra archivos aquí o haz clic para seleccionar</p>
                <small>Máximo 10MB por archivo. Formatos: PDF, DOC, XLS, JPG, PNG</small>
            </div>
            <input type="file" id="fileInput" multiple style="display: none;" onchange="handleFileSelect(event)">

            <div class="uploaded-files" id="uploadedFilesList">
                <!-- Archivos seleccionados -->
            </div>
        </div>

        <!-- Sección de variables del formulario -->
        <div class="task-variables-section" id="taskVariablesSection" style="display: none;">
            <h4>
                <i class="fas fa-file-alt"></i>
                Campos del Formulario
                <span class="badge-count" id="variablesCount">0</span>
            </h4>
            <div class="variables-grid" id="variablesGrid">
                <!-- Se genera dinámicamente -->
            </div>
        </div>

        <div class="approval-section" id="approvalSection" style="display: none;">
            <h4><i class="fas fa-clipboard-check"></i> Completar Tarea</h4>

            <div class="validation-summary" id="validationSummary">
                <div class="title"><i class="fas fa-exclamation-triangle"></i> Campos requeridos faltantes</div>
                <ul id="validationList"></ul>
            </div>

            <div class="form-group">
                <label for="approvalComments"><i class="fas fa-comment"></i> Comentarios (opcional)</label>
                <textarea id="approvalComments" placeholder="Agregar comentarios..."></textarea>
            </div>

            <div class="reject-reason-section" id="rejectReasonSection">
                <div class="form-group" style="margin: 0;">
                    <label for="rejectReason"><i class="fas fa-times-circle"></i> Motivo del rechazo (requerido)</label>
                    <textarea id="rejectReason" placeholder="Ingrese el motivo del rechazo..."></textarea>
                </div>
            </div>

            <div class="approval-actions">
                <button class="btn-cancel-action" onclick="closeModal('taskDetailModal')">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button class="btn-reject-task" onclick="showRejectSection()" id="btnShowReject">
                    <i class="fas fa-ban"></i> Rechazar
                </button>
                <button class="btn-reject-task" onclick="confirmReject()" id="btnConfirmReject" style="display: none;">
                    <i class="fas fa-times"></i> Confirmar Rechazo
                </button>
                <button class="btn-cancel-action" onclick="cancelReject()" id="btnCancelReject" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </button>
                <button class="btn-approve-task" onclick="approveTaskWithForm()" id="btnApproveWithForm">
                    <i class="fas fa-check"></i> Aprobar Tarea
                </button>
            </div>
        </div>

        <!-- Footer simple (para tareas ya completadas) -->
        <div class="task-detail-footer" id="simpleFooter" style="display: none;">
            <div></div>
            <div class="btn-group">
                <button class="btn btn-secondary" onclick="closeModal('taskDetailModal')">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let allTasks = [];
    let filteredTasks = [];
    let selectedTasks = new Set();
    let currentPage = 1;
    const tasksPerPage = 20;
    const maxBulkTasks = 50;
    let currentDetailTaskId = null;
    let currentTaskData = null;
    let isEditMode = false;
    let formFields = [];
    let formValues = {};
    let selectedFiles = [];

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        loadTasks();
    });

    /**
     * Carga las tareas pendientes
     */
    async function loadTasks() {
        try {
            const response = await fetch('/comun.php/bpmn/tasksBulk?scope=my&status=pending');
            const data = await response.json();

            if (data.success !== false) {
                allTasks = data.tasks || [];
                populateProcessFilter();
                applyFilters();
            } else {
                throw new Error(data.error || 'Error al cargar tareas');
            }
        } catch (error) {
            document.getElementById('taskTableBody').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center" style="color: #dc3545;">
                        Error al cargar tareas: ${error.message}
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Poblar filtro de procesos
     */
    function populateProcessFilter() {
        const processes = [...new Set(allTasks.map(t => t.process_name))];
        const select = document.getElementById('filterProcess');

        processes.forEach(process => {
            const option = document.createElement('option');
            option.value = process;
            option.textContent = process;
            select.appendChild(option);
        });
    }

    /**
     * Aplicar filtros
     */
    function applyFilters() {
        const processFilter = document.getElementById('filterProcess').value;
        const priorityFilter = document.getElementById('filterPriority').value;
        const statusFilter = document.getElementById('filterStatus').value;
        const searchFilter = document.getElementById('filterSearch').value.toLowerCase();
        const bulkEligibleOnly = document.getElementById('filterBulkEligible').checked;

        filteredTasks = allTasks.filter(task => {
            // Filtro por proceso
            if (processFilter && task.process_name !== processFilter) return false;

            // Filtro por prioridad
            if (priorityFilter) {
                const priority = getPriorityLevel(task.priority);
                if (priority !== priorityFilter) return false;
            }

            // Filtro por estado (vencida)
            if (statusFilter === 'overdue' && !isOverdue(task.due_date)) return false;
            if (statusFilter === 'pending' && isOverdue(task.due_date)) return false;

            // Filtro por búsqueda
            if (searchFilter && !task.task_name.toLowerCase().includes(searchFilter)) return false;

            // Filtro por elegibilidad para aprobación masiva
            if (bulkEligibleOnly && task.has_required_form) return false;

            return true;
        });

        currentPage = 1;
        renderTasks();
        updateBulkActionsBar();
    }

    /**
     * Renderizar tabla de tareas
     */
    function renderTasks() {
        const tbody = document.getElementById('taskTableBody');

        if (filteredTasks.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No hay tareas pendientes</h3>
                            <p>No se encontraron tareas con los filtros seleccionados.</p>
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('paginationContainer').style.display = 'none';
            return;
        }

        // Calcular paginación
        const startIndex = (currentPage - 1) * tasksPerPage;
        const endIndex = Math.min(startIndex + tasksPerPage, filteredTasks.length);
        const pageTasks = filteredTasks.slice(startIndex, endIndex);

        tbody.innerHTML = pageTasks.map(task => {
            const isSelected = selectedTasks.has(task.id);
            const canBulkApprove = !task.has_required_form;
            const isOverdueTask = isOverdue(task.due_date);
            const priorityLevel = getPriorityLevel(task.priority);

            return `
                <tr class="${isSelected ? 'selected' : ''} ${!canBulkApprove ? 'disabled' : ''}" data-task-id="${task.id}">
                    <td class="checkbox-col">
                        <input type="checkbox" 
                               class="task-checkbox" 
                               ${isSelected ? 'checked' : ''} 
                               ${!canBulkApprove ? 'disabled title="Esta tarea requiere formulario"' : ''}
                               onchange="toggleTaskSelection(${task.id}, this.checked)">
                    </td>
                    <td>
                        <div class="task-info">
                            <span class="task-name">
                                ${task.task_name}
                                ${task.has_required_form ? '<span class="has-form-badge"><i class="fas fa-file-alt"></i> Requiere formulario</span>' : ''}
                            </span>
                            <span class="task-id">#${task.id}</span>
                        </div>
                    </td>
                    <td>
                        <div class="task-info">
                            <span class="task-process">${task.process_name}</span>
                            <span class="task-id">Instancia #${task.workflow_instance_id}/${task.workflow_instance_name}</span>
                        </div>
                    </td>
                    <td>
                        <span class="priority-badge priority-${priorityLevel}">
                            ${priorityLevel === 'high' ? 'Alta' : priorityLevel === 'medium' ? 'Media' : 'Baja'}
                        </span>
                    </td>
                    <td>
                        <div class="date-info ${isOverdueTask ? 'overdue' : ''}">
                            ${task.due_date ? `
                                <div class="date">${formatDate(task.due_date)}</div>
                                <div class="time">${formatTime(task.due_date)}</div>
                            ` : '<span style="color: #999;">Sin fecha límite</span>'}
                        </div>
                    </td>
                    <td>
                        <span class="status-badge ${isOverdueTask ? 'status-overdue' : 'status-pending'}">
                            ${isOverdueTask ? '<i class="fas fa-exclamation-triangle"></i> Vencida' : '<i class="fas fa-clock"></i> Pendiente'}
                        </span>
                    </td>
                    <td>
                        <div class="task-actions">
                            <button class="btn-action btn-view tooltip-primary" onclick="viewTask(${task.id})" data-toggle="tooltip" data-original-title="Ver tarea">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action btn-approve tooltip-primary" onclick="approveTaskSingle(${task.id})" data-toggle="tooltip" data-original-title="Aprobar tarea simple">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-action btn-reject tooltip-primary" onclick="rejectTaskSingle(${task.id})" data-toggle="tooltip" data-original-title="Rechazar tarea simple">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="btn-action btn-info tooltip-primary" onclick="viewWorkflowHistory(${task.workflow_instance_id})" data-toggle="tooltip" data-original-title="Historial de la tarea">
                                <i class="fas fa-history"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Actualizar paginación
        renderPagination(startIndex, endIndex);
    }

    /**
     * Renderizar paginación
     */
    function renderPagination(startIndex, endIndex) {
        const totalPages = Math.ceil(filteredTasks.length / tasksPerPage);

        document.getElementById('showingFrom').textContent = startIndex + 1;
        document.getElementById('showingTo').textContent = endIndex;
        document.getElementById('totalTasks').textContent = filteredTasks.length;

        if (totalPages <= 1) {
            document.getElementById('paginationContainer').style.display = 'none';
            return;
        }

        document.getElementById('paginationContainer').style.display = 'flex';

        let buttonsHtml = `
            <button class="pagination-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                buttonsHtml += `
                    <button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">
                        ${i}
                    </button>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                buttonsHtml += '<span style="padding: 0 5px;">...</span>';
            }
        }

        buttonsHtml += `
            <button class="pagination-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        document.getElementById('paginationButtons').innerHTML = buttonsHtml;
    }

    /**
     * Ir a página
     */
    function goToPage(page) {
        currentPage = page;
        renderTasks();
    }

    /**
     * Toggle selección de tarea
     */
    function toggleTaskSelection(taskId, isSelected) {
        if (isSelected) {
            if (selectedTasks.size >= maxBulkTasks) {
                toastr.warning(`Máximo ${maxBulkTasks} tareas por lote`);
                event.target.checked = false;
                return;
            }
            selectedTasks.add(taskId);
        } else {
            selectedTasks.delete(taskId);
        }

        updateRowSelection(taskId, isSelected);
        updateBulkActionsBar();
        updateSelectAllCheckbox();
    }

    /**
     * Toggle seleccionar todo
     */
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const isChecked = selectAllCheckbox.checked;

        // Obtener tareas de la página actual que pueden ser seleccionadas
        const startIndex = (currentPage - 1) * tasksPerPage;
        const endIndex = Math.min(startIndex + tasksPerPage, filteredTasks.length);
        const pageTasks = filteredTasks.slice(startIndex, endIndex);

        pageTasks.forEach(task => {
            if (!task.has_required_form) {
                if (isChecked && selectedTasks.size < maxBulkTasks) {
                    selectedTasks.add(task.id);
                } else if (!isChecked) {
                    selectedTasks.delete(task.id);
                }
            }
        });

        renderTasks();
        updateBulkActionsBar();
    }

    /**
     * Actualizar checkbox de seleccionar todo
     */
    function updateSelectAllCheckbox() {
        const startIndex = (currentPage - 1) * tasksPerPage;
        const endIndex = Math.min(startIndex + tasksPerPage, filteredTasks.length);
        const pageTasks = filteredTasks.slice(startIndex, endIndex);

        const selectableTasks = pageTasks.filter(t => !t.has_required_form);
        const selectedOnPage = selectableTasks.filter(t => selectedTasks.has(t.id));

        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = selectableTasks.length > 0 && selectedOnPage.length === selectableTasks.length;
        selectAllCheckbox.indeterminate = selectedOnPage.length > 0 && selectedOnPage.length < selectableTasks.length;
    }

    /**
     * Actualizar selección visual de fila
     */
    function updateRowSelection(taskId, isSelected) {
        const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.classList.toggle('selected', isSelected);
        }
    }

    /**
     * Actualizar barra de acciones masivas
     */
    function updateBulkActionsBar() {
        const bar = document.getElementById('bulkActionsBar');
        const count = selectedTasks.size;

        document.getElementById('selectedCount').textContent = count;

        if (count > 0) {
            bar.classList.add('visible');
        } else {
            bar.classList.remove('visible');
        }
    }

    /**
     * Limpiar selección
     */
    function clearSelection() {
        selectedTasks.clear();
        renderTasks();
        updateBulkActionsBar();
        document.getElementById('selectAll').checked = false;
    }

    /**
     * Abrir modal de aprobación masiva
     */
    function openBulkApproveModal() {
        if (selectedTasks.size === 0) {
            toastr.warning('Seleccione al menos una tarea');
            return;
        }

        const selectedTasksData = allTasks.filter(t => selectedTasks.has(t.id));

        document.getElementById('approveTaskCount').textContent = selectedTasksData.length;
        document.getElementById('approveCount').textContent = selectedTasksData.length;

        document.getElementById('approveTaskList').innerHTML = selectedTasksData.map(task => `
            <div class="selected-task-item">
                <span>${task.task_name}</span>
                <span style="color: #666;">${task.process_name}</span>
            </div>
        `).join('');

        // Resetear estado del modal
        document.getElementById('bulkApproveComment').value = '';
        document.getElementById('approveProcessingOverlay').classList.remove('active');
        document.getElementById('approveResultsOverlay').classList.remove('active');

        document.getElementById('bulkApproveModal').classList.add('active');
    }

    /**
     * Abrir modal de rechazo masivo
     */
    function openBulkRejectModal() {
        if (selectedTasks.size === 0) {
            toastr.warning('Seleccione al menos una tarea');
            return;
        }

        const selectedTasksData = allTasks.filter(t => selectedTasks.has(t.id));

        document.getElementById('rejectTaskCount').textContent = selectedTasksData.length;
        document.getElementById('rejectCount').textContent = selectedTasksData.length;

        document.getElementById('rejectTaskList').innerHTML = selectedTasksData.map(task => `
            <div class="selected-task-item">
                <span>${task.task_name}</span>
                <span style="color: #666;">${task.process_name}</span>
            </div>
        `).join('');

        // Resetear estado del modal
        document.getElementById('bulkRejectReason').value = '';
        document.getElementById('rejectProcessingOverlay').classList.remove('active');
        document.getElementById('rejectResultsOverlay').classList.remove('active');

        document.getElementById('bulkRejectModal').classList.add('active');
    }

    /**
     * Ejecutar aprobación masiva
     */
    async function executeBulkApprove() {
        const comment = document.getElementById('bulkApproveComment').value;
        const skipWithForms = document.getElementById('skipWithForms').checked;
        const taskIds = Array.from(selectedTasks);

        // Mostrar overlay de procesamiento
        document.getElementById('approveProcessingOverlay').classList.add('active');

        try {
            const response = await fetch('/comun.php/bpmn/bulkApprove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    task_ids: JSON.stringify(taskIds),
                    comment: comment,
                    skip_with_forms: skipWithForms ? '1' : '0'
                })
            });

            const result = await response.json();

            // Mostrar resultados
            showBulkResults('approve', result);

        } catch (error) {
            showBulkResults('approve', {
                success: false,
                approved: 0,
                failed: taskIds.length,
                errors: [{
                    message: error.message
                }]
            });
        }
    }

    /**
     * Ejecutar rechazo masivo
     */
    async function executeBulkReject() {
        const reason = document.getElementById('bulkRejectReason').value.trim();

        if (!reason) {
            toastr.warning('El motivo del rechazo es requerido');
            return;
        }

        const taskIds = Array.from(selectedTasks);

        // Mostrar overlay de procesamiento
        document.getElementById('rejectProcessingOverlay').classList.add('active');

        try {
            const response = await fetch('/comun.php/bpmn/bulkReject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    task_ids: JSON.stringify(taskIds),
                    reason: reason
                })
            });

            const result = await response.json();

            // Mostrar resultados
            showBulkResults('reject', result);

        } catch (error) {
            showBulkResults('reject', {
                success: false,
                rejected: 0,
                failed: taskIds.length,
                errors: [{
                    message: error.message
                }]
            });
        }
    }

    /**
     * Mostrar resultados de operación masiva
     */
    function showBulkResults(type, result) {
        const prefix = type === 'approve' ? 'approve' : 'reject';
        const successCount = type === 'approve' ? result.approved : result.rejected;
        const failedCount = result.failed || 0;

        // Ocultar procesamiento, mostrar resultados
        document.getElementById(`${prefix}ProcessingOverlay`).classList.remove('active');
        document.getElementById(`${prefix}ResultsOverlay`).classList.add('active');

        // Actualizar icono según resultado
        const iconEl = document.getElementById(`${prefix}ResultIcon`);
        if (failedCount === 0) {
            iconEl.className = 'results-icon success';
            iconEl.innerHTML = '<i class="fas fa-check-circle"></i>';
            document.getElementById(`${prefix}ResultTitle`).textContent =
                type === 'approve' ? '¡Tareas aprobadas exitosamente!' : '¡Tareas rechazadas exitosamente!';
        } else if (successCount === 0) {
            iconEl.className = 'results-icon error';
            iconEl.innerHTML = '<i class="fas fa-times-circle"></i>';
            document.getElementById(`${prefix}ResultTitle`).textContent = 'Error al procesar tareas';
        } else {
            iconEl.className = 'results-icon partial';
            iconEl.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
            document.getElementById(`${prefix}ResultTitle`).textContent = 'Proceso completado con errores';
        }

        // Actualizar contadores
        document.getElementById(`${prefix}SuccessCount`).textContent = successCount;
        document.getElementById(`${prefix}FailedCount`).textContent = failedCount;

        // Mostrar errores si hay
        const errorDetailsEl = document.getElementById(`${prefix}ErrorDetails`);
        if (result.errors && result.errors.length > 0) {
            errorDetailsEl.style.display = 'block';
            errorDetailsEl.innerHTML = '<h4 style="margin-bottom: 10px;">Errores:</h4>' +
                result.errors.map(e => `
                    <div class="error-item">
                        <strong>Tarea ${e.task_id || ''}:</strong> ${e.message}
                    </div>
                `).join('');
        } else {
            errorDetailsEl.style.display = 'none';
        }
    }

    /**
     * Cerrar modal de operación masiva y refrescar
     */
    function closeBulkModal(modalId) {
        closeModal(modalId);
        clearSelection();
        loadTasks();
    }

    /**
     * Cerrar modal
     */
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    /**
     * Refrescar tareas
     */
    function refreshTasks() {
        loadTasks();
        toastr.success('Tareas actualizadas');
    }

    /**
     * Resetear secciones del modal
     */
    function resetModalSections() {
        document.getElementById('approvalSection').style.display = 'none';
        document.getElementById('simpleFooter').style.display = 'none';
        document.getElementById('rejectReasonSection').classList.remove('visible');
        document.getElementById('validationSummary').classList.remove('visible');
        document.getElementById('btnShowReject').style.display = 'inline-flex';
        document.getElementById('btnConfirmReject').style.display = 'none';
        document.getElementById('btnCancelReject').style.display = 'none';
        document.getElementById('btnApproveWithForm').style.display = 'inline-flex';
        document.getElementById('approvalComments').value = '';
        document.getElementById('rejectReason').value = '';
    }

    /**
     * Ver detalle de tarea mejorado
     */
    async function viewTask(taskId) {
        resetModalSections();

        currentDetailTaskId = taskId;
        currentTaskData = null;
        isEditMode = false;
        formFields = [];
        formValues = {};

        // Mostrar modal con loading
        document.getElementById('taskDetailName').textContent = 'Cargando...';
        document.getElementById('taskDetailModal').classList.add('active');

        try {
            const response = await fetch(`/comun.php/bpmn/taskDetail?task_id=${taskId}`);
            const task = await response.json();

            if (!task.success && task.error) {
                throw new Error(task.error);
            }

            currentTaskData = task;

            // Renderizar información básica
            renderBasicInfo(task.task);

            // Renderizar formulario editable
            renderEditableForm(task.task);

            // Renderizar variables del proceso (solo lectura)
            renderProcessVariables(task.task.variables);

            if (task.task.status === 'pending' || task.task.status === 'assigned' || task.task.status === 'in_progress') {
                document.getElementById('approvalSection').style.display = 'block';
                document.getElementById('simpleFooter').style.display = 'none';
            } else {
                document.getElementById('approvalSection').style.display = 'none';
                document.getElementById('simpleFooter').style.display = 'flex';
            }

            // Cargar actividad
            loadTaskActivity(taskId);
        } catch (error) {
            document.getElementById('taskDetailName').textContent = 'Error';
            toastr.error('Error al cargar detalle: ' + error.message);
        }
    }

    /**
     * Renderizar formulario editable
     */
    function renderEditableForm(task) {
        // Parsear campos del formulario
        let formFields = [];
        if (task.form_data) {
            try {
                let parsed = typeof task.form_data === 'string' ? JSON.parse(task.form_data) : task.form_data;
                
                // Extraer el array de fields si viene envuelto en un objeto
                if (parsed && parsed.fields && Array.isArray(parsed.fields)) {
                    formFields = parsed.fields;
                } else if (Array.isArray(parsed)) {
                    formFields = parsed;
                }
            } catch (e) {
                formFields = [];
            }
        }

        // Validar que tengamos campos - usar formFields ya parseado, no task.form_data
        if (!Array.isArray(formFields) || formFields.length === 0) {
            document.getElementById('taskVariablesSection').style.display = 'none';
            return;
        }

        // Parsear valores existentes
        let formValues = {};
        if (task.form_values) {
            try {
                formValues = typeof task.form_values === 'string' ? JSON.parse(task.form_values) : task.form_values;
            } catch (e) {
                formValues = {};
            }
        }

        // También buscar valores en las variables del proceso
        if (task.workflow_variables) {
            try {
                const processVars = typeof task.workflow_variables === 'string' ? JSON.parse(task.workflow_variables) : task.workflow_variables;
                formFields.forEach(field => {
                    const fieldId = field.id || field.name;
                    if (fieldId && processVars[fieldId] !== undefined && formValues[fieldId] === undefined) {
                        formValues[fieldId] = processVars[fieldId];
                    }
                });
            } catch (e) {}
        }

        // Mostrar sección
        document.getElementById('taskVariablesSection').style.display = 'block';
        document.getElementById('variablesCount').textContent = formFields.length;
        
        // Generar campos editables
        const gridHtml = formFields.map(field => {
            const fieldId = field.id || field.name || 'field_' + Math.random().toString(36).substr(2, 9);
            const value = formValues[fieldId] !== undefined ? formValues[fieldId] : '';
            const isRequired = field.required === true || field.required === 'true';

            return `
            <div class="variable-card editable ${isRequired ? 'required' : ''}">
                <div class="var-header">
                    <div class="var-name">
                        ${isRequired ? '<span class="required-mark">*</span>' : ''}
                        ${field.label || fieldId}
                    </div>
                    <span class="var-type type-${field.type || 'text'}">${getFieldTypeName(field.type)}</span>
                </div>
                ${renderInputField(field, fieldId, value)}
                <div class="var-input-error">Este campo es requerido</div>
            </div>
            `;
        }).join('');

        document.getElementById('variablesGrid').innerHTML = gridHtml;
    }

    /**
     * Renderizar información básica de la tarea
     */
    function renderBasicInfo(task) {
        // Encabezado
        document.getElementById('taskDetailName').textContent = task.task_name + ' / ' + task.workflow_instance.name || 'Sin nombre';
        document.getElementById('taskDetailIdBadge').textContent = `#${task.id}`;
        document.getElementById('taskDetailProcess').textContent = task.process_name || 'Sin proceso';

        // Estado
        const isOverdue = task.due_date && new Date(task.due_date) < new Date();
        document.getElementById('taskDetailStatus').innerHTML = `
        <span class="task-detail-status ${isOverdue ? 'overdue' : 'pending'}">
            <i class="fas fa-${isOverdue ? 'exclamation-triangle' : 'clock'}"></i>
            ${isOverdue ? 'Vencida' : 'Pendiente'}
        </span>
    `;

        // Prioridad
        const priority = task.priority || 5;
        const priorityLevel = getPriorityLevel(priority);
        document.getElementById('taskDetailPriority').innerHTML = `
        <div class="priority-indicator">
            <div class="priority-bar">
                <div class="bar ${priority >= 3 ? 'active ' + priorityLevel : ''}"></div>
                <div class="bar ${priority >= 5 ? 'active ' + priorityLevel : ''}"></div>
                <div class="bar ${priority >= 7 ? 'active ' + priorityLevel : ''}"></div>
            </div>
            <span class="priority-text ${priorityLevel}">
                ${priorityLevel === 'high' ? 'Alta' : priorityLevel === 'medium' ? 'Media' : 'Baja'} (${priority}/10)
            </span>
        </div>
    `;

        // Asignado
        if (task.assignee_name) {
            const initials = getInitials(task.assignee_name);
            document.getElementById('taskDetailAssignee').innerHTML = `
            <div class="assignee-display">
                <div class="assignee-avatar">${initials}</div>
                <div class="assignee-info">
                    <span class="name">${task.assignee_name}</span>
                    ${task.assignee_role ? `<span class="role">${task.assignee_role}</span>` : ''}
                </div>
            </div>
        `;
        } else {
            document.getElementById('taskDetailAssignee').innerHTML = '<span class="text-muted">Sin asignar</span>';
        }

        // Fecha límite
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            const now = new Date();
            const diffHours = Math.floor((dueDate - now) / (1000 * 60 * 60));
            const diffDays = Math.floor(diffHours / 24);

            let countdownClass = 'normal';
            let countdownText = '';

            if (diffHours < 0) {
                countdownClass = 'overdue';
                countdownText = `Vencida hace ${Math.abs(diffDays)} día(s)`;
            } else if (diffHours < 24) {
                countdownClass = 'urgent';
                countdownText = `Vence en ${diffHours} hora(s)`;
            } else if (diffDays <= 3) {
                countdownClass = 'urgent';
                countdownText = `Vence en ${diffDays} día(s)`;
            } else {
                countdownText = `Faltan ${diffDays} días`;
            }

            document.getElementById('taskDetailDueDate').innerHTML = `
            <div class="due-date-display">
                <span class="date">${formatDateTime(task.due_date)}</span>
                <span class="countdown ${countdownClass}">${countdownText}</span>
            </div>
        `;
        } else {
            document.getElementById('taskDetailDueDate').innerHTML = '<span class="text-muted">Sin fecha límite</span>';
        }

        // Fecha creación
        document.getElementById('taskDetailCreatedAt').textContent = task.created_at ? formatDateTime(task.created_at) : '-';

        // Instancia
        document.getElementById('taskDetailInstance').innerHTML = `
        <a href="/comun.php/bpmn/workflowHistory?id=${task.workflow_instance_id}" style="color: #667eea;">
            #${task.workflow_instance_id}/${task.workflow_instance.name}
        </a>
    `;

        // Descripción
        if (task.description) {
            document.getElementById('taskDescriptionSection').style.display = 'block';
            document.getElementById('taskDetailDescription').textContent = task.description;
        }

        // Comentarios existentes
        if (task.comments) {
            document.getElementById('taskCommentsSection').style.display = 'block';
            document.getElementById('taskDetailComments').textContent = task.comments;
        }
    }

    /**
     * Actualiza los botones del modal según el estado del formulario
     */
    function updateDetailButtons(task) {
        const btnApprove = document.getElementById('btnApproveFromDetail');
        const btnReject = document.getElementById('btnRejectFromDetail');

        // Verificar si tiene formulario con campos requeridos vacíos
        let hasRequiredEmpty = false;

        if (task.form_data) {
            try {
                const fields = typeof task.form_data === 'string' ? JSON.parse(task.form_data) : task.form_data;
                const values = task.form_values ?
                    (typeof task.form_values === 'string' ? JSON.parse(task.form_values) : task.form_values) : {};

                hasRequiredEmpty = fields.some(f => f.required && !values[f.id]);
            } catch (e) {
                // Ignorar errores de parseo
            }
        }

        if (hasRequiredEmpty) {
            btnApprove.disabled = true;
            btnApprove.title = 'Debe completar el formulario primero';
            btnApprove.innerHTML = '<i class="fas fa-lock"></i> Aprobar';
        } else {
            btnApprove.disabled = false;
            btnApprove.title = '';
            btnApprove.innerHTML = '<i class="fas fa-check"></i> Aprobar';
        }
    }

    /**
     * Renderiza las variables/campos del formulario
     */
    function renderFormVariables(formData, formValuesData) {
        formFields = [];
        if (formData) {
            try {
                formFields = typeof formData === 'string' ? JSON.parse(formData) : formData;
            } catch (e) {
                formFields = [];
            }
        }

        if (!Array.isArray(formFields) || formFields.length === 0) {
            document.getElementById('taskVariablesSection').style.display = 'none';
            return;
        }

        // Parsear valores existentes
        formValues = {};
        if (formValuesData) {
            try {
                formValues = typeof formValuesData === 'string' ? JSON.parse(formValuesData) : formValuesData;
            } catch (e) {
                formValues = {};
            }
        }

        document.getElementById('taskVariablesSection').style.display = 'block';
        document.getElementById('variablesCount').textContent = formFields.length;

        // Generar campos EDITABLES
        const gridHtml = formFields.map(field => {
            const fieldId = field.id || field.name || 'field_' + Math.random().toString(36).substr(2, 9);
            const value = formValues[fieldId] !== undefined ? formValues[fieldId] : '';
            const isRequired = field.required === true || field.required === 'true' || field.required === 1;

            return `
            <div class="variable-card editable ${isRequired ? 'required' : ''}">
                <div class="var-header">
                    <div class="var-name">
                        ${isRequired ? '<span class="required-mark">*</span>' : ''}
                        ${field.label || fieldId}
                    </div>
                    <span class="var-type type-${field.type || 'text'}">${getFieldTypeName(field.type)}</span>
                </div>
                ${renderInputField(field, fieldId, value)}
                <div class="var-input-error">Este campo es requerido</div>
            </div>
        `;
        }).join('');

        document.getElementById('variablesGrid').innerHTML = gridHtml;
    }

    /**
     * Renderizar campo de entrada según tipo
     */
    function renderInputField(field, fieldId, value) {
        const type = field.type || 'text';
        const placeholder = field.placeholder || '';

        switch (type) {
            case 'textarea':
                return `<textarea class="var-input" id="field_${fieldId}" name="${fieldId}" 
                    placeholder="${placeholder}">${value || ''}</textarea>`;

            case 'select':
                let options = '<option value="">-- Seleccionar --</option>';
                if (field.options && Array.isArray(field.options)) {
                    options += field.options.map(opt => {
                        const optValue = opt.value !== undefined ? opt.value : opt;
                        const optLabel = opt.label !== undefined ? opt.label : opt;
                        const selected = value == optValue ? 'selected' : '';
                        return `<option value="${optValue}" ${selected}>${optLabel}</option>`;
                    }).join('');
                }
                return `<select class="var-input" id="field_${fieldId}" name="${fieldId}">${options}</select>`;

            case 'checkbox':
                const checked = value === true || value === 'true' || value === '1' || value === 1;
                return `
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="field_${fieldId}" name="${fieldId}" ${checked ? 'checked' : ''}>
                        <label for="field_${fieldId}">${field.checkboxLabel || 'Sí'}</label>
                    </div>
                `;

            case 'date':
                const dateValue = value ? value.split(' ')[0].split('T')[0] : '';
                return `<input type="date" class="var-input" id="field_${fieldId}" name="${fieldId}" value="${dateValue}">`;

            case 'number':
                return `<input type="number" class="var-input" id="field_${fieldId}" name="${fieldId}" 
                    value="${value || ''}" placeholder="${placeholder}" 
                    ${field.min !== undefined ? `min="${field.min}"` : ''} 
                    ${field.max !== undefined ? `max="${field.max}"` : ''}>`;

            case 'email':
                return `<input type="email" class="var-input" id="field_${fieldId}" name="${fieldId}" 
                    value="${value || ''}" placeholder="${placeholder}">`;

            default:
                return `<input type="text" class="var-input" id="field_${fieldId}" name="${fieldId}" 
                    value="${value || ''}" placeholder="${placeholder}">`;
        }
    }

    /**
     * Recolectar valores del formulario
     */
    function collectFormValues() {
        const values = {};
        formFields.forEach(field => {
            const fieldId = field.id || field.name;
            const input = document.getElementById(`field_${fieldId}`);
            if (input) {
                if (field.type === 'checkbox') {
                    values[fieldId] = input.checked;
                } else {
                    values[fieldId] = input.value;
                }
            }
        });
        return values;
    }

    /**
     * Validar formulario
     */
    function validateForm() {
        const errors = [];
        let isValid = true;

        formFields.forEach(field => {
            const fieldId = field.id || field.name;
            const isRequired = field.required === true || field.required === 'true' || field.required === 1;
            const input = document.getElementById(`field_${fieldId}`);

            if (input && isRequired) {
                let value = field.type === 'checkbox' ? input.checked : input.value;

                if (value === '' || value === null || value === undefined || value === false) {
                    isValid = false;
                    input.classList.add('error');
                    errors.push(field.label || fieldId);
                } else {
                    input.classList.remove('error');
                }
            }
        });

        const summary = document.getElementById('validationSummary');
        const list = document.getElementById('validationList');

        if (errors.length > 0) {
            list.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            summary.classList.add('visible');
        } else {
            summary.classList.remove('visible');
        }

        return isValid;
    }

    /**
     * Aprobar tarea con formulario
     */
    async function approveTaskWithForm() {
        if (!validateForm()) {
            toastr.warning('Por favor complete los campos requeridos');
            return;
        }

        const btn = document.getElementById('btnApproveWithForm');
        const originalHtml = btn.innerHTML;
        btn.classList.add('btn-loading');
        btn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('task_id', currentDetailTaskId);
            formData.append('comments', document.getElementById('approvalComments').value);
            formData.append('action', 'approve');

            // Recopilar campos del formulario dinámico
            const fields = {};
            document.querySelectorAll('[id^="field_"]').forEach(input => {
                const fieldId = input.id.replace('field_', '');
                if (input.type === 'checkbox') {
                    fields[fieldId] = input.checked;
                } else {
                    fields[fieldId] = input.value;
                }
            });
            formData.append('fields', JSON.stringify(fields));

            // Agregar archivos
            if (typeof selectedFiles !== 'undefined' && selectedFiles.length > 0) {
                selectedFiles.forEach(file => {
                    formData.append('attachments[]', file);
                });
            }

            const response = await fetch('/comun.php/bpmn/taskComplete', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                toastr.success('Tarea aprobada correctamente');
                closeModal('taskDetailModal');
                await loadTasks();
                if (typeof selectedFiles !== 'undefined') {
                    selectedFiles = [];
                }

                if (result.has_next_task && result.next_task && result.next_task.assigned_to_me) {
                    if (confirm(`Hay una nueva tarea: "${result.next_task.task_name}". ¿Desea abrirla?`)) {
                        setTimeout(() => viewTask(result.next_task.id), 500);
                    }
                }
            } else {
                throw new Error(result.error || 'Error al aprobar tarea');
            }
        } catch (error) {
            toastr.error('Error: ' + error.message);
        } finally {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    /**
     * Obtiene el nombre legible del tipo de campo
     */
    function getFieldTypeName(type) {
        const types = {
            'text': 'Texto',
            'textarea': 'Texto largo',
            'number': 'Número',
            'date': 'Fecha',
            'datetime': 'Fecha/Hora',
            'select': 'Selección',
            'checkbox': 'Casilla',
            'radio': 'Opción',
            'file': 'Archivo',
            'email': 'Email',
            'tel': 'Teléfono',
            'url': 'URL'
        };
        return types[type] || type || 'Texto';
    }

    /**
     * Renderiza las variables del proceso/workflow
     */
    function renderProcessVariables(variables) {
        if (!variables || variables === '{}' || variables === 'null') {
            document.getElementById('processVariablesSection').style.display = 'none';
            return;
        }

        let vars = {};
        try {
            vars = typeof variables === 'string' ? JSON.parse(variables) : variables;
        } catch (e) {
            console.error('Error parsing variables:', e);
            return;
        }

        const keys = Object.keys(vars);

        if (keys.length === 0) {
            document.getElementById('processVariablesSection').style.display = 'none';
            return;
        }

        // Mostrar sección
        document.getElementById('processVariablesSection').style.display = 'block';

        // Generar HTML
        const rowsHtml = keys.map(key => {
            let value = vars[key];

            // Formatear valor según tipo
            if (typeof value === 'object') {
                value = JSON.stringify(value, null, 2);
            } else if (typeof value === 'boolean') {
                value = value ? 'Sí' : 'No';
            }

            return `
            <tr>
                <td class="var-key">${key}</td>
                <td class="var-val">${value !== null && value !== '' ? value : '<em style="color:#999">vacío</em>'}</td>
            </tr>
        `;
        }).join('');

        document.getElementById('processVariablesBody').innerHTML = rowsHtml;
    }

    /**
     * Cargar actividad/historial de la tarea
     */
    async function loadTaskActivity(taskId) {
        try {
            const response = await fetch(`/comun.php/bpmn/taskActivity?task_id=${taskId}`);
            const data = await response.json();

            if (data.success && data.activities && data.activities.length > 0) {
                document.getElementById('taskActivitySection').style.display = 'block';

                const timelineHtml = data.activities.map(activity => `
                <div class="activity-item">
                    <div class="activity-text">
                        <strong>${activity.user_name || 'Sistema'}</strong> ${activity.action_text || activity.action}
                    </div>
                    <div class="activity-date">${formatDateTime(activity.created_at)}</div>
                </div>
            `).join('');

                document.getElementById('taskActivityTimeline').innerHTML = timelineHtml;
            } else {
                document.getElementById('taskActivitySection').style.display = 'none';
            }
        } catch (error) {
            // Si falla, simplemente ocultar la sección
            document.getElementById('taskActivitySection').style.display = 'none';
        }
    }

    /**
     * Aprobar desde el modal de detalle
     */
    function approveFromDetail() {
        if (currentDetailTaskId) {
            closeModal('taskDetailModal');
            approveTaskSingle(currentDetailTaskId);
        }
    }

    /**
     * Rechazar desde el modal de detalle
     */
    function rejectFromDetail() {
        if (currentDetailTaskId) {
            closeModal('taskDetailModal');
            rejectTaskSingle(currentDetailTaskId);
        }
    }

    /**
     * Aprobar tarea individual
     */
    async function approveTaskSingle(taskId) {
        if (!confirm('¿Aprobar esta tarea?')) return;

        try {
            const response = await fetch('/comun.php/bpmn/taskComplete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    task_id: taskId,
                    action: 'approve'
                })
            });

            const result = await response.json();

            if (result.success) {
                toastr.success('Tarea aprobada');
                loadTasks();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            toastr.error('Error: ' + error.message);
        }
    }

    /**
     * Rechazar tarea individual
     */
    async function rejectTaskSingle(taskId) {
        const reason = prompt('Ingrese el motivo del rechazo:');
        if (!reason) return;

        try {
            const response = await fetch('/comun.php/bpmn/tasksReject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    task_id: taskId,
                    reason: reason
                })
            });

            const result = await response.json();

            if (result.success) {
                toastr.success('Tarea rechazada');
                loadTasks();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            toastr.error('Error: ' + error.message);
        }
    }

    /**
     * Abrir formulario de la tarea para completar
     */
    function openTaskForm(taskId) {
        // Cerrar el modal de detalle
        closeModal('taskDetailModal');

        // Redirigir a la vista de completar tarea
        window.location.href = `/comun.php/bpmn/taskForm?task_id=${taskId}`;
    }

    /**
     * Mostrar sección de rechazo
     */
    function showRejectSection() {
        document.getElementById('rejectReasonSection').classList.add('visible');
        document.getElementById('btnShowReject').style.display = 'none';
        document.getElementById('btnConfirmReject').style.display = 'inline-flex';
        document.getElementById('btnCancelReject').style.display = 'inline-flex';
        document.getElementById('btnApproveWithForm').style.display = 'none';
        document.getElementById('rejectReason').focus();
    }

    /**
     * Cancelar rechazo
     */
    function cancelReject() {
        document.getElementById('rejectReasonSection').classList.remove('visible');
        document.getElementById('btnShowReject').style.display = 'inline-flex';
        document.getElementById('btnConfirmReject').style.display = 'none';
        document.getElementById('btnCancelReject').style.display = 'none';
        document.getElementById('btnApproveWithForm').style.display = 'inline-flex';
        document.getElementById('rejectReason').value = '';
    }

    /**
     * Confirmar rechazo
     */
    async function confirmReject() {
        const reason = document.getElementById('rejectReason').value.trim();

        if (!reason) {
            toastr.warning('Por favor ingrese el motivo del rechazo');
            document.getElementById('rejectReason').focus();
            return;
        }

        const btn = document.getElementById('btnConfirmReject');
        const originalHtml = btn.innerHTML;
        btn.classList.add('btn-loading');
        btn.disabled = true;

        try {
            const response = await fetch('/comun.php/bpmn/tasksReject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    task_id: currentDetailTaskId,
                    reason: reason
                })
            });

            const result = await response.json();

            if (result.success) {
                toastr.success('Tarea rechazada correctamente');
                closeModal('taskDetailModal');
                loadTasks();
            } else {
                throw new Error(result.error || 'Error al rechazar');
            }
        } catch (error) {
            toastr.error('Error: ' + error.message);
        } finally {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    /**
     * Ver historial del workflow
     */
    async function viewWorkflowHistory(workflowtaskId) {
        console.log(workflowtaskId);

        try {
            if (!workflowtaskId) return;
            javascript: jQuery.OpenModalSIMAD(`<?php echo url_for("bpmn/workflowHistory") ?>?workflowinstance_id=${workflowtaskId}`);

        } catch (error) {
            toastr.error('Error al cargar detalle: ' + error.message);
        }
    }

    /**
     * Formatear fecha y hora
     */
    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    /**
     * Obtener iniciales de un nombre
     */
    function getInitials(name) {
        if (!name) return '?';
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    // Funciones auxiliares
    function getPriorityLevel(priority) {
        if (priority >= 8) return 'high';
        if (priority >= 4) return 'medium';
        return 'low';
    }

    function isOverdue(dueDate) {
        if (!dueDate) return false;
        return new Date(dueDate) < new Date();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function formatTime(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleTimeString('es-CO', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    /**
     * Configura drag and drop para archivos
     */
    function setupDragAndDrop() {
        const dropArea = document.getElementById('fileUploadArea');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        dropArea.addEventListener('drop', handleDrop, false);
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            // Validar tamaño (10MB)
            if (file.size > 10 * 1024 * 1024) {
                toastr.warning(`El archivo ${file.name} excede el límite de 10MB`);
                return;
            }

            // Evitar duplicados
            if (selectedFiles.some(f => f.name === file.name)) {
                toastr.warning(`El archivo ${file.name} ya fue agregado`);
                return;
            }

            selectedFiles.push(file);
        });

        renderSelectedFiles();
    }

    function renderSelectedFiles() {
        const container = document.getElementById('uploadedFilesList');

        container.innerHTML = selectedFiles.map((file, index) => `
                <div class="uploaded-file-item">
                    <div class="file-info">
                        <i class="fas fa-file"></i>
                        <span>${escapeHtml(file.name)} (${formatFileSize(file.size)})</span>
                    </div>
                    <button type="button" class="remove-file" onclick="removeSelectedFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
    }

    function removeSelectedFile(index) {
        selectedFiles.splice(index, 1);
        renderSelectedFiles();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatFileSize(size) {
        if (size < 1024) return size + ' bytes';
        if (size < 1024 * 1024) return (size / 1024).toFixed(2) + ' KB';
        if (size < 1024 * 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB';
        return (size / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('active');
        }
    });
</script>