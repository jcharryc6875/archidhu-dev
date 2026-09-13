<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object', 'jQuery');
?>

<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-designer.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-deploy.css?v=<?php echo time(); ?>">

<div class="header">
    <h1><i class="fas fa-project-diagram"></i> Gesti&oacute;n de Procesos</h1>
    <p>Selecciona un proceso para iniciar un nuevo flujo de trabajo</p>

    <div class="header-actions">
        <a href="<?php echo url_for('bpmn/index') ?>">
            <i class="fas fa-home"></i> Inicio
        </a>
        <a href="<?php echo url_for('bpmn/processList') ?>" class="active">
            <i class="fas fa-play-circle"></i> Iniciar Proceso
        </a>
        <a href="<?php echo url_for('bpmn/taskList') ?>">
            <i class="fas fa-tasks"></i> Mis Tareas
        </a>
        <!--<a href="<?php echo url_for('bpmn/designer') ?>">
                <i class="fas fa-pencil-ruler"></i> Diseñador
            </a>-->
    </div>
</div>

<div class="toolbar">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Buscar procesos..." onkeyup="filterProcesses()">
        <i class="fas fa-search"></i>
    </div>
    <a href="<?php echo url_for('bpmn/designer') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Proceso
    </a>
</div>

<div class="process-grid" id="processContainer">
    <div class="empty-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando procesos...</p>
    </div>
</div>

<!-- Modal para iniciar proceso -->
<div class="modal" id="startProcessModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-play-circle"></i> Iniciar Proceso</h3>
            <button class="modal-close" onclick="closeModal('startProcessModal')">&times;</button>
        </div>

        <div id="startProcessForm">
            <div class="modal-body">
                <input type="hidden" id="selectedProcessId" name="process_id">

                <div class="process-summary" id="processSummary">
                    <!-- Se llena dinámicamente -->
                </div>

                <div class="form-group">
                    <label for="instanceName">
                        <i class="fas fa-tag"></i> Nombre de la Instancia (opcional)
                    </label>
                    <input type="text" id="instanceName" name="instance_name"
                        placeholder="Ej: Aprobar tipo documental">
                    <small>Si lo dejas vacío, se generará automáticamente.</small>
                </div>

                <div class="form-group">
                    <label for="instancePriority">
                        <i class="fas fa-flag"></i> Prioridad
                    </label>
                    <select id="instancePriority" name="priority">
                        <option value="3">Baja</option>
                        <option value="5" selected>Normal</option>
                        <option value="7">Alta</option>
                        <option value="10">Urgente</option>
                    </select>
                </div>

                <!-- Variables iniciales dinámicas -->
                <div class="variables-section" id="initialVariablesSection">
                    <h4><i class="fas fa-sliders-h"></i> Variables Iniciales</h4>
                    <div class="variables-container" id="initialVariablesContainer">
                        <div class="variables-empty" id="variablesEmpty">
                            <i class="fas fa-cube"></i>
                            No hay variables configuradas. Puedes agregar variables personalizadas.
                        </div>
                    </div>
                    <button type="button" class="btn-add-variable" onclick="addInitialVariable()">
                        <i class="fas fa-plus-circle"></i> Agregar Variable
                    </button>
                </div>

                <div class="form-group">
                    <label for="instanceComments">
                        <i class="fas fa-comment"></i> Comentarios Iniciales (opcional)
                    </label>
                    <textarea id="instanceComments" name="comments"
                        placeholder="Agrega cualquier nota o comentario relevante..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('startProcessModal')">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="submitStartProcess()">
                    <i class="fas fa-rocket"></i> Iniciar Proceso
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
    let allProcesses = [];
    let selectedProcess = null;

    // Cargar procesos al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        loadProcesses();
    });

    async function loadProcesses() {

        const container = document.getElementById('processContainer');

        try {
            const response = await fetch('/comun.php/bpmn/processes');
            const data = await response.json();
            allProcesses = data.processes;

            if (!allProcesses || allProcesses.length === 0) {
                container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>No hay procesos</h3>
                            <p>Crea tu primer proceso en el diseñador</p>
                            <br>
                            <a href="<?php echo url_for('bpmn/designer') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Proceso1
                            </a>
                        </div>
                    `;
                return;
            }

            renderProcesses(allProcesses);
            showToastNotify('Procesos cargados exitosamente', 'success');
        } catch (error) {
            container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                        <h3>Error al cargar procesos</h3>
                        <p>${error.message}</p>
                    </div>
                `;
        } finally {
            hideLoading();
        }
    }

    function renderProcesses(processes) {
        const container = document.getElementById('processContainer');

        container.innerHTML = processes.map(process => {
            const statusBadge = process.is_active ?
                '<span class="badge badge-active">Activo</span>' :
                '<span class="badge badge-inactive">Inactivo</span>';

            return `
                    <div class="process-card" data-name="${escapeHtml(process.name.toLowerCase())}">
                        <div class="process-header">
                            <div>
                                <div class="process-title">${escapeHtml(process.name)}</div>
                                <div class="process-description">${escapeHtml(process.description || 'Sin descripción')}</div>
                            </div>
                            <div>
                                ${statusBadge}
                                <span class="badge badge-version">v${process.version}</span>
                                <a onclick="window.location.href='/comun.php/bpmn/versionHistory?process_id=${process.id}'" 
                                    class="badge badge-info" 
                                    title="Ver historial de versiones">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="process-meta">
                            <span><i class="fas fa-calendar"></i> Creado: ${formatDate(process.created_at)}</span>
                            <span><i class="fas fa-clock"></i> Actualizado: ${formatDate(process.updated_at)}</span>
                        </div>

                        <div class="process-actions">
                            <button class="btn btn-primary" onclick="editProcess(${process.id})">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-${!process.is_active ? 'info' : 'warning'}" onclick="toggleActive(${process.id}, ${!process.is_active})">
                                <i class="fas fa-${process.is_active ? 'pause' : 'play'}"></i>
                                ${process.is_active ? 'Desactivar' : 'Activar'}
                            </button>
                            <button class="btn btn-danger" onclick="deleteProcess(${process.id}, '${escapeHtml(process.name)}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <button class="btn btn-success" onclick="openStartModal(${process.id})">
                                <i class="fas fa-play"></i> Iniciar
                            </button>
                            <button class="btn btn-blue" onclick="duplicateProcess(${process.id})">
                                <i class="fas fa-copy"></i> Duplicar
                            </button>
                        </div>
                        <div class="process-card-body">
							<div class="process-stats">
								<div class="stat-item">
									<div class="stat-value running">${process.stats?.running || 0}</div>
									<div class="stat-label">En curso</div>
								</div>
								<div class="stat-item">
									<div class="stat-value completed">${process.stats?.completed || 0}</div>
									<div class="stat-label">Completados</div>
								</div>
								<div class="stat-item">
									<div class="stat-value">${process.stats?.total || 0}</div>
									<div class="stat-label">Total</div>
								</div>
							</div>
						</div>
                    </div>
                `;
        }).join('');
    }

    function filterProcesses() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filtered = allProcesses.filter(p =>
            p.name.toLowerCase().includes(searchTerm) ||
            (p.description && p.description.toLowerCase().includes(searchTerm))
        );
        renderProcesses(filtered);
    }

    function editProcess(processId) {
        window.location.href = '<?php echo url_for('bpmn/designer') ?>?process=' + processId;
    }

    function deployProcess(processId, processName) {
        if (confirm('¿Desplegar una nueva instancia del proceso "' + processName + '"?')) {
            fetch('/comun.php/bpmn/processes/' + processId + '/deploy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        variables: '{}',
                        simulation: false
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToastNotify('Proceso desplegado correctamente. ID: ' + data.workflow_id, 'success');
                    } else {
                        showToastNotify('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => showToastNotify('Error: ' + error.message, 'error'));
        }
    }

    function toggleActive(processId, activate) {
        const action = activate ? 'activar' : 'desactivar';
        if (confirm('¿Desea ' + action + ' este proceso?')) {
            fetch('/comun.php/bpmn/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        process: processId,
                        is_active: activate ? '1' : '0'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToastNotify('Proceso ' + action + ' correctamente', 'success');
                        loadProcesses();
                    } else {
                        showToastNotify('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => showToastNotify('Error: ' + error.message, 'error'));
        }
    }

    function deleteProcess(processId, processName) {
        if (confirm('¿Está seguro de eliminar el proceso "' + processName + '"?\n\nEsta acción no se puede deshacer.')) {
            fetch('/comun.php/bpmn/processes/' + processId, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToastNotify('Proceso eliminado correctamente', 'success');
                        loadProcesses();
                    } else {
                        showToastNotify('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => showToastNotify('Error: ' + error.message, 'error'));
        }
    }

    /**
     * Abre el modal para iniciar un proceso
     */
    function openStartModal(processId) {
        selectedProcess = allProcesses.find(p => p.id === processId);

        if (!selectedProcess) {
            showToastNotify('Proceso no encontrado', 'error');
            return;
        }
        
        if (!selectedProcess.is_active) {
            showToastNotify('El proceso no se puede iniciar, debe estar activo', 'error');
            return;
        }
        
        // Llenar el formulario
        document.getElementById('selectedProcessId').value = processId;

        // Resumen del proceso
        document.getElementById('processSummary').innerHTML = `
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 5px 0; color: #333;">
                        <i class="fas fa-project-diagram" style="color: #667eea;"></i>
                        ${escapeHtml(selectedProcess.name)}
                    </h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        ${escapeHtml(selectedProcess.description || 'Sin descripción')}
                    </p>
                </div>
            `;

        // Limpiar variables iniciales - resetear el contenedor
        const container = document.getElementById('initialVariablesContainer');
        container.innerHTML = `
                <div class="variables-empty" id="variablesEmpty">
                    <i class="fas fa-cube"></i>
                    No hay variables configuradas. Puedes agregar variables personalizadas.
                </div>
            `;

        // Cargar variables predefinidas del proceso si existen
        if (selectedProcess.initial_variables && selectedProcess.initial_variables.length > 0) {
            selectedProcess.initial_variables.forEach(variable => {
                addInitialVariable(
                    variable.name,
                    variable.default_value || '',
                    variable.required || false,
                    variable.type || 'text'
                );
            });
        }

        // Limpiar campos
        document.getElementById('instanceName').value = '';
        document.getElementById('instancePriority').value = '5';
        document.getElementById('instanceComments').value = '';

        // Mostrar modal
        document.getElementById('startProcessModal').classList.add('active');
    }

    /**
     * Agrega una variable inicial
     */
    function addInitialVariable(name = '', value = '', required = false, type = 'text') {
        const container = document.getElementById('initialVariablesContainer');
        const emptyMessage = document.getElementById('variablesEmpty');

        // Ocultar mensaje de vacío
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }

        const varId = 'initVar_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const varNumber = container.querySelectorAll('.variable-row').length + 1;

        const div = document.createElement('div');
        div.className = 'variable-row';
        div.id = varId;
        div.innerHTML = `
                <button type="button" class="btn-remove-var" 
                        onclick="removeInitialVariable('${varId}')"
                        title="Eliminar variable">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="variable-fields-grid">
                    <div class="variable-field">
                        <label for="${varId}_name">
                            Nombre de Variable
                            ${required ? '<span class="required-mark">*</span>' : ''}
                        </label>
                        <input type="text" 
                               id="${varId}_name"
                               name="var_name[]" 
                               value="${escapeHtml(name)}" 
                               placeholder="ej: departamento"
                               pattern="^[a-zA-Z_][a-zA-Z0-9_]*$"
                               title="Solo letras, números y guiones bajos"
                               ${required ? 'required' : ''}
                               onblur="validateVariableName(this)"
                               oninput="clearFieldError(this)">
                        <span class="field-error" id="${varId}_name_error">Nombre inválido</span>
                    </div>
                    
                    <div class="variable-field">
                        <label for="${varId}_type">Tipo de Dato</label>
                        <select id="${varId}_type" 
                                name="var_type[]"
                                onchange="updateVariableValueInput('${varId}')">
                            <option value="text" ${type === 'text' ? 'selected' : ''}>Texto</option>
                            <option value="number" ${type === 'number' ? 'selected' : ''}>Número</option>
                            <option value="date" ${type === 'date' ? 'selected' : ''}>Fecha</option>
                            <option value="boolean" ${type === 'boolean' ? 'selected' : ''}>Sí / No</option>
                        </select>
                    </div>
                    
                    <div class="variable-field full-width">
                        <label for="${varId}_value">
                            Valor Inicial
                            ${required ? '<span class="required-mark">*</span>' : ''}
                        </label>
                        <div id="${varId}_value_container">
                            ${renderValueInput(varId, type, value, required)}
                        </div>
                        <span class="field-error" id="${varId}_value_error">Este campo es requerido</span>
                    </div>
                </div>
            `;

        container.appendChild(div);

        // Focus en el campo nombre si está vacío
        if (!name) {
            setTimeout(() => {
                document.getElementById(`${varId}_name`).focus();
            }, 100);
        }
    }

    /**
     * Renderiza el input de valor según el tipo
     */
    function renderValueInput(varId, type, value = '', required = false) {
        const reqAttr = required ? 'required' : '';
        const escapedValue = escapeHtml(value);

        switch (type) {
            case 'number':
                return `<input type="number" 
                                   id="${varId}_value" 
                                   name="var_value[]" 
                                   value="${escapedValue}"
                                   placeholder="Ingresa un número"
                                   step="any"
                                   ${reqAttr}>`;

            case 'date':
                return `<input type="date" 
                                   id="${varId}_value" 
                                   name="var_value[]" 
                                   value="${escapedValue}"
                                   ${reqAttr}>`;

            case 'boolean':
                return `<select id="${varId}_value" 
                                    name="var_value[]"
                                    ${reqAttr}>
                                <option value="">Seleccionar...</option>
                                <option value="true" ${value === 'true' || value === true ? 'selected' : ''}>Sí</option>
                                <option value="false" ${value === 'false' || value === false ? 'selected' : ''}>No</option>
                            </select>`;

            default: // text
                return `<input type="text" 
                                   id="${varId}_value" 
                                   name="var_value[]" 
                                   value="${escapedValue}"
                                   placeholder="Ingresa el valor"
                                   ${reqAttr}>`;
        }
    }

    /**
     * Actualiza el input de valor cuando cambia el tipo
     */
    function updateVariableValueInput(varId) {
        const typeSelect = document.getElementById(`${varId}_type`);
        const valueContainer = document.getElementById(`${varId}_value_container`);
        const currentValue = document.getElementById(`${varId}_value`)?.value || '';

        if (typeSelect && valueContainer) {
            const newType = typeSelect.value;
            valueContainer.innerHTML = renderValueInput(varId, newType, '', false);
        }
    }

    /**
     * Valida el nombre de la variable
     */
    function validateVariableName(input) {
        const value = input.value.trim();
        const errorSpan = document.getElementById(input.id + '_error');
        const row = input.closest('.variable-row');

        // Patrón válido: empieza con letra o _, seguido de letras, números o _
        const isValid = /^[a-zA-Z_][a-zA-Z0-9_]*$/.test(value) || value === '';

        if (!isValid && value !== '') {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (errorSpan) {
                errorSpan.textContent = 'Solo letras, números y guiones bajos. Debe iniciar con letra.';
                errorSpan.classList.add('show');
            }
            if (row) row.classList.add('has-error');
            return false;
        } else if (value !== '') {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (errorSpan) errorSpan.classList.remove('show');
            if (row) row.classList.remove('has-error');
            return true;
        } else {
            input.classList.remove('is-invalid', 'is-valid');
            if (errorSpan) errorSpan.classList.remove('show');
            if (row) row.classList.remove('has-error');
            return true;
        }
    }

    /**
     * Limpia el error de un campo
     */
    function clearFieldError(input) {
        const errorSpan = document.getElementById(input.id + '_error');
        if (errorSpan) errorSpan.classList.remove('show');
        input.classList.remove('is-invalid');

        const row = input.closest('.variable-row');
        if (row) row.classList.remove('has-error');
    }

    /**
     * Remueve una variable inicial
     */
    function removeInitialVariable(varId) {
        const element = document.getElementById(varId);
        if (element) {
            element.classList.add('removing');
            setTimeout(() => {
                element.remove();
                checkVariablesEmpty();
            }, 200);
        }
    }

    /**
     * Verifica si no hay variables y muestra mensaje
     */
    function checkVariablesEmpty() {
        const container = document.getElementById('initialVariablesContainer');
        const emptyMessage = document.getElementById('variablesEmpty');
        const rows = container.querySelectorAll('.variable-row');

        if (rows.length === 0 && emptyMessage) {
            emptyMessage.style.display = 'block';
        }
    }

    /**
     * Valida todas las variables antes de enviar
     */
    function validateAllVariables() {
        const rows = document.querySelectorAll('.variable-row');
        let isValid = true;

        rows.forEach(row => {
            const nameInput = row.querySelector('input[name="var_name[]"]');
            const valueInput = row.querySelector('[name="var_value[]"]');

            // Validar nombre si hay valor
            if (nameInput && valueInput) {
                const hasValue = valueInput.value.trim() !== '';
                const hasName = nameInput.value.trim() !== '';

                if (hasValue && !hasName) {
                    nameInput.classList.add('is-invalid');
                    const errorSpan = document.getElementById(nameInput.id + '_error');
                    if (errorSpan) {
                        errorSpan.textContent = 'El nombre es requerido si hay un valor';
                        errorSpan.classList.add('show');
                    }
                    row.classList.add('has-error');
                    isValid = false;
                }

                // Validar formato del nombre
                if (hasName && !validateVariableName(nameInput)) {
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    /**
     * Envía el formulario para iniciar el proceso
     */
    async function submitStartProcess() {
        // Validar variables antes de continuar
        if (!validateAllVariables()) {
            showToastNotify('Por favor corrige los errores en las variables', 'warning');
            return;
        }

        // Validar que haya un proceso seleccionado
        const processId = document.getElementById('selectedProcessId').value;
        if (!processId) {
            showToastNotify('No hay proceso seleccionado', 'error');
            return;
        }

        showLoading();

        try {
            // Recopilar variables con sus tipos
            const rows = document.querySelectorAll('.variable-row');
            const variables = {};

            rows.forEach(row => {
                const nameInput = row.querySelector('input[name="var_name[]"]');
                const typeSelect = row.querySelector('select[name="var_type[]"]');
                const valueInput = row.querySelector('[name="var_value[]"]');

                if (nameInput && nameInput.value.trim() && valueInput) {
                    const name = nameInput.value.trim();
                    const type = typeSelect ? typeSelect.value : 'text';
                    let value = valueInput.value;

                    // Convertir valor según tipo
                    switch (type) {
                        case 'number':
                            value = value ? parseFloat(value) : null;
                            break;
                        case 'boolean':
                            value = value === 'true';
                            break;
                        case 'date':
                            // Mantener como string ISO
                            break;
                        default:
                            // Texto, mantener como está
                            break;
                    }

                    variables[name] = value;
                }
            });

            const requestData = {
                process_id: processId,
                instance_name: document.getElementById('instanceName').value.trim(),
                priority: document.getElementById('instancePriority').value,
                comments: document.getElementById('instanceComments').value.trim(),
                variables: JSON.stringify(variables)
            };

            // Realizar petición AJAX
            const response = await fetch('/comun.php/bpmn/workflowStart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(requestData)
            });

            // Verificar si la respuesta es OK
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                showToastNotify('Proceso iniciado correctamente', 'success');
                closeModal('startProcessModal');

                // Limpiar formulario
                resetStartProcessForm();

                // Recargar procesos para actualizar estadísticas
                loadProcesses();

                // Preguntar si desea ir a sus tareas
                if (result.has_pending_task) {
                    setTimeout(() => {
                        if (confirm('El proceso ha generado una tarea para ti. ¿Deseas ir a tus tareas?')) {
                            window.location.href = '<?php echo url_for("bpmn/taskList") ?>';
                        }
                    }, 500);
                }
            } else {
                throw new Error(result.error || 'Error al iniciar proceso');
            }
        } catch (error) {
            showToastNotify('Error: ' + error.message, 'error');
        } finally {
            hideLoading();
        }
    }

    /**
     * Limpia el formulario de inicio de proceso
     */
    function resetStartProcessForm() {
        document.getElementById('selectedProcessId').value = '';
        document.getElementById('instanceName').value = '';
        document.getElementById('instancePriority').value = '5';
        document.getElementById('instanceComments').value = '';
        document.getElementById('processSummary').innerHTML = '';

        // Limpiar variables
        const container = document.getElementById('initialVariablesContainer');
        container.innerHTML = `
            <div class="variables-empty" id="variablesEmpty">
                <i class="fas fa-cube"></i>
                No hay variables configuradas. Puedes agregar variables personalizadas.
            </div>
        `;
    }

    /**
     * Ver detalles del proceso
     */
    function viewProcessDetails(processId) {
        window.location.href = '<?php echo url_for("bpmn/designer") ?>?process=' + processId + '&readonly=1';
    }

    /**
     * Duplicar el proceso
     * Redirigir al diseñador en modo duplicación
     */
    async function duplicateProcess(processId) {
        // Crear formulario dinámico para enviar por POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/comun.php/bpmn/designer';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'duplicate';
        input.value = processId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }


    /**
     * Muestra una notificación
     */
    function showToastNotify(message, type = 'info') {
        toastr[type](message, "", {
            timeOut: 3000,
            positionClass: 'toast-bottom-full-width'
        });
    }

    /**
     * Escapa HTML para prevenir XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Formatea una fecha
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    /**
     * Muestra el loading
     */
    function showLoading() {
        document.getElementById('loadingOverlay').classList.add('active');
    }

    /**
     * Oculta el loading
     */
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
    }

    /**
     * Cierra el modal
     */
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
</script>