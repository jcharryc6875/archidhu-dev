/**
 * JavaScript para el Diseñador BPMN
 * Ubicación: web/js/bpmn-designer.js
 * Requiere: bpmn-js (incluido en el HTML)
 */

/**
 * Extensión de Moddle para atributos personalizados
 * Esto permite que bpmn-js persista atributos con namespace 'custom:'
 */
const customModdleExtension = {
    name: 'custom',
    prefix: 'custom',
    uri: 'http://custom/schema/bpmn',
    xml: {
        tagAlias: 'lowerCase'
    },
    types: [
        {
            name: 'TaskConfig',
            superClass: ['Element'],
            properties: [
                {
                    name: 'taskConfig',
                    isAttr: true,
                    type: 'String'
                }
            ]
        }
    ],
    emumerations: [],
    associations: []
};

// Variables globales
let modeler;
let currentProcessId = null;
let currentTaskId = null;
let currentTaskElement = null;
let isProcessing = false;
let loadingTimeout = null;
let isDuplicateMode = false;

const ColorPickerModule = window.ColorPickerModule;
// Probar con diferentes nombres comunes
const gridModule = window.gridModule ||
    window.diagramJsGrid ||
    window.DiagramJSGrid ||
    window.grid;

const BpmsReplaceModule = window.BpmsReplaceModule;

// Inicialización
document.addEventListener('DOMContentLoaded', function () {
    initializeBpmnModeler();
    loadProcesses();
    loadTasks();
    loadMetrics();

    // Cargar proceso si viene en la URL
    const duplicateProcessIdElement = document.getElementById('duplicateProcessId');
    let duplicateProcessId;
    if (duplicateProcessIdElement) {
        duplicateProcessId = duplicateProcessIdElement.value;
    }

    if (duplicateProcessId) {
        loadProcessForDuplicate(duplicateProcessId);
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        const processId = urlParams.get('process');

        if (processId) {
            loadProcess(processId);
        }
    }

    // Actualizar tareas cada 30 segundos
    setInterval(loadTasks, 30000);
});

/**
 * Muestra overlay de carga
 */
function showLoading(message = 'Procesando...', subtext = 'Por favor espere') {
    let overlay = document.getElementById('loadingOverlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text" id="loadingText">${message}</div>
                <div class="loading-subtext" id="loadingSubtext">${subtext}</div>
                <div class="loading-progress">
                    <div class="loading-progress-bar"></div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loadingText').textContent = message;
        document.getElementById('loadingSubtext').textContent = subtext;
    }

    // Pequeño delay para que la animación se vea
    loadingTimeout = setTimeout(() => {
        overlay.classList.add('active');
    }, 10);

    isProcessing = true;
}

/**
 * Oculta overlay de carga
 */
function hideLoading() {
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
        loadingTimeout = null;
    }

    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
    isProcessing = false;
}

/**
 * Actualiza el mensaje del loading
 */
function updateLoadingMessage(message, subtext = '') {
    const textEl = document.getElementById('loadingText');
    const subtextEl = document.getElementById('loadingSubtext');

    if (textEl) textEl.textContent = message;
    if (subtextEl) subtextEl.textContent = subtext;
}

/**
 * Previene múltiples clicks en botones
 */
function withLoading(asyncFunction, loadingMessage = 'Procesando...', loadingSubtext = 'Por favor espere') {
    return async function (...args) {
        if (isProcessing) {
            showNotification('Ya hay una operación en proceso', 'warning');
            return;
        }

        try {
            showLoading(loadingMessage, loadingSubtext);
            const result = await asyncFunction.apply(this, args);
            return result;
        } catch (error) {
            throw error;
        } finally {
            hideLoading();
        }
    };
}

/**
 * Inicializa el modelador BPMN
 */
function initializeBpmnModeler() {
    modeler = new BpmnJS({
        container: '#canvas',
        additionalModules: [
            gridModule,
            ColorPickerModule
        ],
        moddleExtensions: {
            custom: customModdleExtension
        },
        /*keyboard: { bindTo: window },*/
        i18n: {
            locale: 'es',
            translations: {
                'es': {
                    'Task': 'Tarea',
                    'User Task': 'Tarea de Usuario',
                    'Service Task': 'Tarea de Servicio',
                    'Script Task': 'Tarea de Script',
                    'Business Rule Task': 'Tarea de Regla de Negocio',
                    'Manual Task': 'Tarea Manual',
                    'Send Task': 'Tarea de Envío',
                    'Receive Task': 'Tarea de Recepción',
                    'Call Activity': 'Actividad de Llamada',
                    'Sub Process': 'Subproceso',
                    'Start Event': 'Evento de Inicio',
                    'End Event': 'Evento de Fin',
                    'Exclusive Gateway': 'Gateway Exclusivo',
                    'Parallel Gateway': 'Gateway Paralelo',
                    'Inclusive Gateway': 'Gateway Inclusivo',
                    'Event Based Gateway': 'Gateway Basado en Eventos',
                    'Complex Gateway': 'Gateway Complejo'
                }
            }
        }
    });

    // AGREGAR ESTA LÍNEA después de crear el modeler
    if (window.applyBpmsReplaceFilter) {
        window.applyBpmsReplaceFilter(modeler);
    }

    // Cargar diagrama vacío
    newDiagram();

    // Eventos del modelador
    modeler.on('element.dblclick', function (event) {
        const element = event.element;
        if (element.type === 'bpmn:UserTask') {
            showTaskConfiguration(element);
        }
    });
}

/**
 * Crea un nuevo diagrama vacío
 */
function newDiagram() {
    const emptyBpmn = `<?xml version="1.0" encoding="UTF-8"?>
    <bpmn2:definitions xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                      xmlns:bpmn2="http://www.omg.org/spec/BPMN/20100524/MODEL" 
                      xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" 
                      xmlns:dc="http://www.omg.org/spec/DD/20100524/DC" 
                      xmlns:custom="http://custom/schema/bpmn"
                      xsi:schemaLocation="http://www.omg.org/spec/BPMN/20100524/MODEL BPMN20.xsd" 
                      id="sample-diagram" 
                      targetNamespace="http://bpmn.io/schema/bpmn">
      <bpmn2:process id="Process_1" isExecutable="true">
        <bpmn2:startEvent id="StartEvent_1" name="Inicio"/>
      </bpmn2:process>
      <bpmndi:BPMNDiagram id="BPMNDiagram_1">
        <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
          <bpmndi:BPMNShape id="_BPMNShape_StartEvent_2" bpmnElement="StartEvent_1">
            <dc:Bounds height="36.0" width="36.0" x="412.0" y="240.0"/>
          </bpmndi:BPMNShape>
        </bpmndi:BPMNPlane>
      </bpmndi:BPMNDiagram>
    </bpmn2:definitions>`;

    modeler.importXML(emptyBpmn);
    currentProcessId = null;
}

/**
 * Guarda el diagrama actual
 */
async function saveDiagram() {
    if (isProcessing) {
        showNotification('Ya hay una operación en proceso', 'warning');
        return;
    }

    try {
        showLoading('Guardando proceso...', 'Generando XML y validando cambios');

        const { xml } = await modeler.saveXML({ format: true });

        if (!currentProcessId) {
            hideLoading();
            showCreateProcessModal();
            return;
        }

        updateLoadingMessage('Guardando proceso...', 'Enviando al servidor');

        const response = await fetch('/comun.php/bpmn/process?process=' + currentProcessId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                bpmn_xml: xml
            })
        });

        const result = await response.json();

        if (result.success) {
            updateLoadingMessage('Proceso guardado correctamente', 'Actualizando interfaz');
            // Recargar lista de procesos para actualizar la versión mostrada
            await loadProcesses();

            hideLoading();
            showNotification(result.message, 'success');
        } else {
            throw new Error(result.error);
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al guardar: ' + error.message, 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Carga la lista de procesos
 */
async function loadProcesses() {
    try {
        const response = await fetch('/comun.php/bpmn/processes');
        const data = await response.json();

        const processList = document.getElementById('processList');
        processList.innerHTML = '';

        if (data.processes.length === 0) {
            processList.innerHTML = '<div style="text-align: center; color: #666;">No hay procesos</div>';
            return;
        }

        data.processes.forEach(process => {
            const div = document.createElement('div');
            div.className = 'process-item';
            div.setAttribute('data-process-id', process.id);
            div.onclick = () => loadProcess(process.id);

            // Marcar como activo si es el proceso actual
            if (currentProcessId && currentProcessId == process.id) {
                div.classList.add('active');
            }

            div.innerHTML = `
                <div class="process-name">${process.name}</div>
                <div class="process-info">
                    Versión ${process.version} • 
                    <span class="status-badge ${process.is_active ? 'status-running' : 'status-failed'}">
                        ${process.is_active ? 'Activo' : 'Inactivo'}
                    </span>
                </div>
            `;

            processList.appendChild(div);
        });

    } catch (error) {
        if (document.getElementById('processList') !== null) {
            document.getElementById('processList').innerHTML =
                '<div style="color: #dc3545;">Error al cargar procesos</div>';
            return;
        }
    }
}

/**
 * Carga un proceso específico
 */
async function loadProcess(processId) {
    try {
        const response = await fetch('/comun.php/bpmn/process?process=' + processId);
        const data = await response.json();

        if (data.bpmn_xml) {
            await modeler.importXML(data.bpmn_xml);
            currentProcessId = processId;

            // Marcar como activo en la lista
            document.querySelectorAll('.process-item').forEach(item => {
                item.classList.remove('active');
            });

            // Buscar y marcar el item correcto
            const processItems = document.querySelectorAll('.process-item');
            processItems.forEach(item => {
                // Necesitamos una forma de identificar cual es el proceso correcto
                // Lo haremos agregando data-id al renderizar los procesos
                if (item.getAttribute('data-process-id') == processId) {
                    item.classList.add('active');
                }
            });

            showNotification('Proceso cargado correctamente', 'success');
            loadMetrics(processId);
        }

    } catch (error) {
        showNotification('Error al cargar proceso: ' + error.message, 'error');
    }
}

/**
 * Carga un proceso para duplicarlo (sin guardar)
 * @param {number} processId - ID del proceso a duplicar
 */
async function loadProcessForDuplicate(processId) {
    try {
        showLoading('Cargando proceso para duplicar...', 'Obteniendo configuración');

        const response = await fetch('/comun.php/bpmn/process?process=' + processId);
        const data = await response.json();

        if (data.bpmn_xml) {
            // Cargar el XML en el canvas
            await modeler.importXML(data.bpmn_xml);

            // NO establecer currentProcessId (es un proceso nuevo)
            currentProcessId = null;
            isDuplicateMode = true;

            // Guardar configuración de tareas si existe
            if (data.task_definitions) {
                window.duplicatedTaskDefinitions = data.task_definitions;
            }

            if (data.process_variables) {
                window.duplicatedProcessVariables = data.process_variables;
            }

            // Guardar descripción original para el modal
            window.duplicatedDescription = data.description || '';

            hideLoading();

            // Mostrar modal para ingresar nombre
            showCreateProcessModalForDuplicate();

            showNotification('Proceso cargado para duplicar. Ingresa un nombre y guarda.', 'info');
        } else {
            throw new Error('No se pudo cargar el proceso');
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al cargar proceso: ' + error.message, 'error');
    }
}

/**
 * Carga las tareas del usuario
 */
async function loadTasks() {
    try {
        const response = await fetch('/comun.php/bpmn/tasks?scope=my&status=assigned');
        const data = await response.json();

        const taskList = document.getElementById('taskList');
        taskList.innerHTML = '';

        if (data.tasks.length === 0) {
            taskList.innerHTML = '<div style="text-align: center; color: #666;">No hay tareas pendientes</div>';
            return;
        }

        data.tasks.forEach(task => {
            const div = document.createElement('div');
            div.className = 'task-item';

            // Determinar clase según prioridad y fecha límite
            if (task.due_date && new Date(task.due_date) < new Date()) {
                div.classList.add('overdue');
            } else if (task.priority > 7) {
                div.classList.add('high-priority');
            }

            div.onclick = () => openTask(task);

            const dueDate = task.due_date ?
                new Date(task.due_date).toLocaleDateString('es-ES') : 'Sin límite';

            div.innerHTML = `
                <div class="task-name">${task.task_name}</div>
                <div class="task-info">
                    ${task.workflow_instance.process_name}<br>
                    Vence: ${dueDate}
                </div>
            `;

            taskList.appendChild(div);
        });

    } catch (error) {
        if (document.getElementById('taskList') !== null) {
            document.getElementById('taskList').innerHTML =
                '<div style="color: #dc3545;">Error al cargar tareas</div>';
        }
    }
}

/**
 * Carga métricas del proceso
 */
async function loadMetrics(processId = null) {
    try {
        let url = '/comun.php/bpmn/metrics';
        if (processId) {
            url += '?process_id=' + processId;
        }

        const response = await fetch(url);
        const data = await response.json();

        const metricsGrid = document.getElementById('metricsGrid');
        metricsGrid.innerHTML = '';

        if (data.summary) {
            const metrics = [
                { label: 'Iniciados', value: data.summary.total_started, icon: 'fa-play' },
                { label: 'Completados', value: data.summary.total_completed, icon: 'fa-check' },
                { label: 'Fallidos', value: data.summary.total_failed, icon: 'fa-times' },
                { label: 'Tasa Éxito', value: data.summary.completion_rate.toFixed(1) + '%', icon: 'fa-percentage' }
            ];

            metrics.forEach(metric => {
                const div = document.createElement('div');
                div.className = 'metric-card';
                div.innerHTML = `
                    <div class="metric-value">${metric.value}</div>
                    <div class="metric-label"><i class="fas ${metric.icon}"></i> ${metric.label}</div>
                `;
                metricsGrid.appendChild(div);
            });
        } else {
            metricsGrid.innerHTML = '<div style="text-align: center; color: #666;">No hay métricas disponibles</div>';
        }

    } catch (error) {
        if (document.getElementById('metricsGrid') !== null) {
            document.getElementById('metricsGrid').innerHTML =
                '<div style="color: #dc3545;">Error al cargar métricas</div>';
        }
    }
}

/**
 * Muestra modal para crear proceso
 */
function showCreateProcessModal() {
    document.getElementById('createProcessModal').classList.add('active');
}

/**
 * Despliega el proceso actual
 */
function deployProcess() {
    if (!currentProcessId) {
        showNotification('Seleccione un proceso primero', 'warning');
        return;
    }
    document.getElementById('deployModal').classList.add('active');
}

/**
 * Ejecuta una simulación
 */
function runSimulation() {
    if (!currentProcessId) {
        showNotification('Seleccione un proceso primero', 'warning');
        return;
    }
    window.location.href = `/comun.php/bpmn/simulate?processId=${currentProcessId}`;
}

/**
 * Muestra configuración de flujos
 */
function showFlowConfig() {
    if (!currentProcessId) {
        showNotification('Seleccione un proceso primero', 'warning');
        return;
    }
    loadFlowConfiguration();
}

/**
 * Carga la configuración de flujos
 */
async function loadFlowConfiguration() {
    try {
        document.getElementById('flowConfigModal').classList.add('active');
        const flowContent = document.getElementById('flowConfigContent');
        flowContent.innerHTML = '<div class="loading">Cargando configuración...</div>';

        const response = await fetch(`/comun.php/bpmn/flows?process_id=${currentProcessId}`);
        const data = await response.json();

        if (!data.flows || data.flows.length === 0) {
            flowContent.innerHTML = '<div style="text-align: center; color: #666; padding: 20px;">No hay flujos configurados</div>';
            return;
        }

        let html = `
            <div style="margin-bottom: 20px;">
                <p style="color: #666; margin-bottom: 15px;">
                    Active o desactive los flujos de secuencia del proceso. Los flujos desactivados no se ejecutarán.
                </p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Flow ID</th>
                        <th class="text-center">Origen → Destino</th>
                        <th class="text-center">Condición</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.flows.forEach(flow => {
            const isActive = flow.is_active;
            const statusBadge = isActive
                ? '<span class="status-badge status-running">Activo</span>'
                : '<span class="status-badge status-failed">Inactivo</span>';

            const condition = flow.condition_expression
                ? `<code>${flow.condition_expression}</code>`
                : '<span style="color: #999;">Sin condición</span>';

            html += `
                <tr>
                    <td style="font-family: monospace; font-size: 12px;">${flow.flow_id}</td>
                    <td class="text-center">
                        <div style="/*display: flex;*/ align-items: center; gap: 8px;">
                            <span style="background: #e3f2fd; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${flow.source_ref}</span>
                            <i class="fas fa-arrow-right" style="color: #999;"></i>
                            <span style="background: #e8f5e9; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${flow.target_ref}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">${condition}</td>
                    <td style="text-align: center;">${statusBadge}</td>
                    <td style="text-align: center;">
                        <button class="btn ${isActive ? 'btn-warning' : 'btn-success'}" 
                                style="padding: 6px 12px; font-size: 12px;"
                                onclick="toggleFlow('${flow.flow_id}', ${!isActive})">
                            <i class="fas fa-${isActive ? 'pause' : 'play'}"></i>
                            ${isActive ? 'Desactivar' : 'Activar'}
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
            <div style="margin-top: 20px; text-align: right;">
                <button class="btn btn-secondary" onclick="closeModal('flowConfigModal')">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        `;

        flowContent.innerHTML = html;

    } catch (error) {
        document.getElementById('flowConfigContent').innerHTML =
            '<div style="color: #dc3545; padding: 20px; text-align: center;">Error al cargar configuración: ' + error.message + '</div>';
    }
}

/**
 * Activa/desactiva un flujo
 */
async function toggleFlow(flowId, isActive) {
    try {
        const response = await fetch(`/comun.php/bpmn/flows`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'itoggle',
                process_id: currentProcessId,
                flow_id: flowId,
                is_active: isActive ? '1' : '0'
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification(
                isActive ? 'Flujo activado correctamente' : 'Flujo desactivado correctamente',
                'success'
            );
            // Recargar configuración
            loadFlowConfiguration();
        } else {
            throw new Error(result.error);
        }

    } catch (error) {
        showNotification('Error al cambiar estado del flujo: ' + error.message, 'error');
    }
}

/**
 * Abre una tarea para completar
 */
async function openTask(task) {
    currentTaskId = task.id;
    document.getElementById('taskModalTitle').textContent = `Completar: ${task.task_name}`;

    // Cargar definición de la tarea para obtener variables configuradas
    const taskDef = await loadTaskDefinition(task.id);

    // Generar campos de formulario dinámicos desde la definición
    const formFields = document.getElementById('taskFormFields');
    formFields.innerHTML = '';

    if (taskDef && taskDef.form_fields) {
        taskDef.form_fields.forEach(field => {
            const div = document.createElement('div');
            div.className = 'form-group';

            let input = '';
            const requiredAttr = field.required ? 'required' : '';
            const defaultValue = field.defaultValue || '';

            switch (field.type) {
                case 'string':
                    input = `<input type="text" id="${field.id}" name="${field.id}" value="${defaultValue}" ${requiredAttr} 
                             placeholder="${field.label}">`;
                    break;
                case 'long':
                    input = `<input type="number" id="${field.id}" name="${field.id}" value="${defaultValue}" ${requiredAttr}
                             placeholder="${field.label}">`;
                    break;
                case 'boolean':
                    input = `
                        <select id="${field.id}" name="${field.id}" ${requiredAttr}>
                            <option value="">Seleccionar...</option>
                            <option value="true" ${defaultValue === 'true' ? 'selected' : ''}>Sí</option>
                            <option value="false" ${defaultValue === 'false' ? 'selected' : ''}>No</option>
                        </select>
                    `;
                    break;
                case 'date':
                    input = `<input type="date" id="${field.id}" name="${field.id}" value="${defaultValue}" ${requiredAttr}>`;
                    break;
                case 'enum':
                    // Para listas de opciones (implementar más adelante)
                    input = `<input type="text" id="${field.id}" name="${field.id}" value="${defaultValue}" ${requiredAttr}
                             placeholder="${field.label}">`;
                    break;
                case 'number':
                    input = `<input type="number" id="${field.id}" name="${field.id}" value="${defaultValue}" ${requiredAttr}
                             placeholder="${field.label}">`;
                    break;
                case 'select':
                    const options = field.options.map(opt => `<option value="${opt.value}">${opt.label}</option>`).join('');
                    input = `<select id="${field.id}" name="${field.id}" ${requiredAttr}>${options}</select>`;
                    break;
                case 'textarea':
                    input = `<textarea id="${field.id}" name="${field.id}" rows="3" ${requiredAttr}></textarea>`;
                    break;
            }

            const requiredIndicator = field.required ? '<span style="color: red;">*</span>' : '';

            div.innerHTML = `
                <label>${field.label} ${requiredIndicator}:</label>
                ${input}
            `;

            formFields.appendChild(div);
        });
    }

    // Limpiar variables adicionales y archivos anteriores
    document.getElementById('taskVariables').innerHTML = `
        <button type="button" class="btn btn-secondary" onclick="addTaskVariable()">
            <i class="fas fa-plus"></i> Agregar Variable
        </button>
    `;
    document.getElementById('filePreview').innerHTML = '';
    document.getElementById('fileInput').value = '';

    // Cargar archivos existentes del workflow
    loadWorkflowFiles(task.workflow_instance.id);

    document.getElementById('taskModal').classList.add('active');
}

/**
 * Carga la definición de la tarea con sus campos configurados
 */
async function loadTaskDefinition(taskInstanceId) {
    try {
        const response = await fetch(`/comun.php/bpmn/taskDefinition?taskInstanceId=${taskInstanceId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error loading task definition:', error);
        return null;
    }
}

/**
 * Agrega una variable personalizada
 */
function addTaskVariable() {
    const container = document.getElementById('taskVariables');
    const variableId = 'var_' + Date.now();

    const div = document.createElement('div');
    div.className = 'form-group';
    div.style.display = 'flex';
    div.style.gap = '10px';
    div.style.alignItems = 'center';
    div.id = variableId;

    div.innerHTML = `
        <input type="text" placeholder="Nombre variable" 
               id="${variableId}_name" 
               style="flex: 1;"
               pattern="[a-zA-Z_][a-zA-Z0-9_]*"
               title="Solo letras, números y guiones bajos. Debe comenzar con letra.">
        <select id="${variableId}_type" style="flex: 0 0 120px;" onchange="updateVariableInput('${variableId}')">
            <option value="text">Texto</option>
            <option value="number">Número</option>
            <option value="boolean">Booleano</option>
            <option value="date">Fecha</option>
        </select>
        <input type="text" placeholder="Valor" 
               id="${variableId}_value" 
               style="flex: 1;">
        <button type="button" class="btn btn-danger" 
                style="padding: 8px 12px;" 
                onclick="document.getElementById('${variableId}').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(div);
}

/**
 * Actualiza el tipo de input según el tipo de variable
 */
function updateVariableInput(variableId) {
    const typeSelect = document.getElementById(`${variableId}_type`);
    const valueInput = document.getElementById(`${variableId}_value`);

    switch (typeSelect.value) {
        case 'number':
            valueInput.type = 'number';
            valueInput.placeholder = 'Valor numérico';
            break;
        case 'boolean':
            const oldValue = valueInput.value;
            const select = document.createElement('select');
            select.id = valueInput.id;
            select.style.cssText = valueInput.style.cssText;
            select.innerHTML = `
                <option value="true" ${oldValue === 'true' ? 'selected' : ''}>Verdadero</option>
                <option value="false" ${oldValue === 'false' ? 'selected' : ''}>Falso</option>
            `;
            valueInput.parentNode.replaceChild(select, valueInput);
            break;
        case 'date':
            valueInput.type = 'date';
            valueInput.placeholder = 'Fecha';
            break;
        default:
            valueInput.type = 'text';
            valueInput.placeholder = 'Valor';
    }
}

/**
 * Preview de archivos seleccionados
 */
function previewFiles(input) {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';

    if (input.files.length === 0) {
        return;
    }

    Array.from(input.files).forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.style.cssText = 'padding: 10px; background: #f8f9fa; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;';

        const fileInfo = document.createElement('div');
        fileInfo.innerHTML = `
            <i class="fas fa-file"></i>
            <strong>${file.name}</strong>
            <span style="color: #666; margin-left: 10px;">(${formatFileSize(file.size)})</span>
        `;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger';
        removeBtn.style.padding = '4px 8px';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => {
            const dt = new DataTransfer();
            const files = Array.from(input.files);
            files.splice(index, 1);
            files.forEach(f => dt.items.add(f));
            input.files = dt.files;
            previewFiles(input);
        };

        fileItem.appendChild(fileInfo);
        fileItem.appendChild(removeBtn);
        preview.appendChild(fileItem);
    });
}

/**
 * Formatea tamaño de archivo
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Carga archivos existentes del workflow
 */
async function loadWorkflowFiles(workflowInstanceId) {
    try {
        const response = await fetch(`/comun.php/bpmn/workflowAttachments?workflowinstance_id=${workflowInstanceId}`);
        const data = await response.json();

        const container = document.getElementById('existingFiles');
        const containerDiv = document.getElementById('existingFilesContainer');

        if (data.attachments && data.attachments.length > 0) {
            containerDiv.style.display = 'block';

            container.innerHTML = data.attachments.map(file => `
                <div style="padding: 8px; background: #e3f2fd; border-radius: 6px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <i class="fas fa-file"></i>
                        <strong>${file.filename}</strong>
                        <span style="color: #666; margin-left: 8px;">(${formatFileSize(file.file_size)})</span>
                        <br>
                        <small style="color: #666;">
                            <i class="fas fa-tag"></i> ${file.task_name} • 
                            <i class="fas fa-user"></i> ${file.uploaded_by} • 
                            ${new Date(file.created_at).toLocaleDateString('es-ES')}
                        </small>
                    </div>
                    <a href="${file.download_url}" class="btn btn-info" style="padding: 4px 8px;" download>
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            `).join('');
        } else {
            containerDiv.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading workflow files:', error);
    }
}

/**
 * Muestra configuración de tarea
 */
function showTaskConfiguration(element) {
    currentTaskElement = element;
    const businessObject = element.businessObject;

    // Cargar datos existentes
    document.getElementById('taskConfigName').value = businessObject.name || '';

    // Tipo de asignación
    const assignee = businessObject.assignee || '';
    if (assignee.startsWith('${')) {
        document.getElementById('taskConfigAssigneeType').value = 'expression';
    } else if (!isNaN(assignee)) {
        document.getElementById('taskConfigAssigneeType').value = 'user';
        document.getElementById('taskConfigAssignee').value = businessObject.$attrs.assignee;
    } else {
        document.getElementById('taskConfigAssigneeType').value = assignee ? 'role' : 'user';
        document.getElementById('taskConfigAssignee').value = businessObject.$attrs.assignee;
    }

    toggleAssigneeField();

    // Cargar variables existentes si las hay
    loadTaskConfigVariables(businessObject);

    document.getElementById('taskConfigModal').classList.add('active');
}

/**
 * Cambia el label según el tipo de asignación
 */
function toggleAssigneeField() {
    const type = document.getElementById('taskConfigAssigneeType').value;
    const label = document.getElementById('taskConfigAssigneeLabel');
    const input = document.getElementById('taskConfigAssignee');

    switch (type) {
        case 'user':
            label.textContent = 'ID de Usuario:';
            input.placeholder = 'ej: 123';
            input.type = 'number';
            if (input.classList.contains('select2') && input.tagName === 'SELECT') {
                jQuery(input).val(currentTaskElement.businessObject.$attrs.assignee);
                jQuery(input).trigger('change');
            } else if (input.tagName === 'SELECT') {
                jQuery(input).val(currentTaskElement.businessObject.$attrs.assignee);
            } else {
                input.value = currentTaskElement.businessObject.$attrs.assignee;
            }
            break;
        case 'role':
            label.textContent = 'Nombre del Rol:';
            input.placeholder = 'ej: Gerente, Aprobador';
            input.type = 'text';
            break;
        case 'group':
            label.textContent = 'Nombre del Grupo:';
            input.placeholder = 'ej: Ventas, Finanzas';
            input.type = 'text';
            break;
        case 'expression':
            label.textContent = 'Expresión:';
            input.placeholder = 'ej: ${responsable} o ${workflow.departamento}';
            input.type = 'text';
            break;
    }
}

/**
 * Carga variables de configuración de tarea desde atributos personalizados
 */
function loadTaskConfigVariables(businessObject) {
    const container = document.getElementById('taskConfigVariables');
    container.innerHTML = '';
    // Intentar leer desde atributo custom:taskConfig
    // Verificar que $attrs existe antes de acceder
    let taskConfigStr = null;

    if (businessObject.$attrs) {
        taskConfigStr = businessObject.$attrs['custom:taskConfig'];
    }

    if (taskConfigStr) {
        try {
            const taskConfig = JSON.parse(taskConfigStr);

            // Cargar duraciones si existen
            if (taskConfig.durationExpected) {
                document.getElementById('taskConfigDuration').value = taskConfig.durationExpected;
            }
            if (taskConfig.durationMax) {
                document.getElementById('taskConfigMaxDuration').value = taskConfig.durationMax;
            }

            // Cargar tipo de asignación
            if (taskConfig.assigneeType) {
                document.getElementById('taskConfigAssigneeType').value = taskConfig.assigneeType;
                toggleAssigneeField();
            }

            // Cargar campos del formulario
            if (taskConfig.formFields && taskConfig.formFields.length > 0) {
                taskConfig.formFields.forEach(field => {
                    addTaskConfigVariable(field);
                });
                return;
            }
        } catch (error) {
            console.error('Error parsing taskConfig:', error);
        }
    }

    // Si no hay variables, mostrar mensaje
    if (container.children.length === 0) {
        container.innerHTML = '<p style="color: #666; font-style: italic;">No hay variables configuradas. Click en "Agregar Variable" para comenzar.</p>';
    }
}

/**
 * Agrega una variable a la configuración de tarea
 */
function addTaskConfigVariable(existingData = null) {
    const container = document.getElementById('taskConfigVariables');

    // Remover mensaje de "no hay variables"
    const emptyMessage = container.querySelector('p');
    if (emptyMessage) {
        emptyMessage.remove();
    }

    const varId = 'taskVar_' + Date.now();
    const div = document.createElement('div');
    div.className = 'task-variable-item';
    div.id = varId;

    const fieldId = existingData?.id || '';
    const fieldLabel = existingData?.label || '';
    const fieldType = existingData?.type || 'string';
    const fieldRequired = existingData?.required === 'true' || existingData?.required === true;
    const fieldDefaultValue = existingData?.defaultValue || '';

    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <strong style="flex: 1;">Variable de Formulario</strong>
            <button type="button" class="btn btn-danger" style="padding: 4px 8px;" 
                    onclick="removeTaskConfigVariable('${varId}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">
                    ID/Nombre de Campo *
                </label>
                <input type="text" class="var-id" value="${fieldId}" 
                       placeholder="ej: monto, departamento" required
                       pattern="[a-zA-Z_][a-zA-Z0-9_]*"
                       title="Solo letras, números y guiones bajos"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">
                    Etiqueta *
                </label>
                <input type="text" class="var-label" value="${fieldLabel}" 
                       placeholder="ej: Monto del Presupuesto" required
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">
                    Tipo de Campo *
                </label>
                <select class="var-type" 
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="string" ${fieldType === 'string' ? 'selected' : ''}>Texto</option>
                    <option value="long" ${fieldType === 'long' ? 'selected' : ''}>Número</option>
                    <option value="boolean" ${fieldType === 'boolean' ? 'selected' : ''}>Sí/No</option>
                    <option value="date" ${fieldType === 'date' ? 'selected' : ''}>Fecha</option>
                    <option value="enum" ${fieldType === 'enum' ? 'selected' : ''}>Lista de Opciones</option>
                </select>
            </div>
            
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">
                    Valor por Defecto
                </label>
                <input type="text" class="var-default" value="${fieldDefaultValue}" 
                       placeholder="Opcional"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                <input type="checkbox" class="var-required" ${fieldRequired ? 'checked' : ''}>
                <span style="font-size: 14px;">Campo Obligatorio</span>
            </label>
        </div>
    `;

    container.appendChild(div);
}

/**
 * Remueve una variable de configuración de tarea
 */
function removeTaskConfigVariable(varId) {
    const element = document.getElementById(varId);
    if (element) {
        element.remove();
    }

    // Si no quedan variables, mostrar mensaje
    const container = document.getElementById('taskConfigVariables');
    const items = container.querySelectorAll('.task-variable-item');
    if (items.length === 0) {
        container.innerHTML = '<p style="color: #666; font-style: italic;">No hay variables configuradas</p>';
    }
}

/**
 * Guarda la configuración de la tarea en el BPMN
 */
async function saveTaskConfiguration() {
    if (!currentTaskElement) {
        showNotification('No hay tarea seleccionada', 'error');
        return;
    }

    // Validar campos requeridos
    const taskName = document.getElementById('taskConfigName').value.trim();
    if (!taskName) {
        showNotification('El nombre de la tarea es obligatorio', 'warning');
        return;
    }

    // Validar variables
    const variableItems = document.querySelectorAll('.task-variable-item');
    let hasInvalidVariables = false;

    variableItems.forEach(item => {
        const varId = item.querySelector('.var-id').value.trim();
        const varLabel = item.querySelector('.var-label').value.trim();

        if (!varId || !varLabel) {
            hasInvalidVariables = true;
        }
    });

    if (hasInvalidVariables) {
        showNotification('Todas las variables deben tener ID y Etiqueta', 'warning');
        return;
    }

    try {
        const modeling = modeler.get('modeling');
        const moddle = modeler.get('moddle');
        const businessObject = currentTaskElement.businessObject;

        // Actualizar nombre de la tarea
        modeling.updateProperties(currentTaskElement, {
            name: taskName
        });

        // Actualizar assignee según el tipo
        const assigneeType = document.getElementById('taskConfigAssigneeType').value;
        const assigneeValue = document.getElementById('taskConfigAssignee').value.trim();

        if (assigneeValue) {
            modeling.updateProperties(currentTaskElement, {
                'assignee': assigneeValue
            });
        }

        // Recopilar variables del formulario
        const formFields = [];

        variableItems.forEach(item => {
            const varId = item.querySelector('.var-id').value.trim();
            const varLabel = item.querySelector('.var-label').value.trim();
            const varType = item.querySelector('.var-type').value;
            const varDefault = item.querySelector('.var-default').value.trim();
            const varRequired = item.querySelector('.var-required').checked;

            if (varId && varLabel) {
                formFields.push({
                    id: varId,
                    label: varLabel,
                    type: varType,
                    defaultValue: varDefault,
                    required: varRequired
                });
            }
        });

        // Guardar las variables y duraciones como atributos personalizados
        const duration = document.getElementById('taskConfigDuration').value;
        const maxDuration = document.getElementById('taskConfigMaxDuration').value;

        // Crear un objeto con toda la configuración
        const taskConfig = {
            assigneeType: assigneeType,
            durationExpected: parseInt(duration) || 60,
            durationMax: parseInt(maxDuration) || 120,
            formFields: formFields
        };

        // SOLUCIÓN: Usar $attrs directamente en el businessObject
        // Esto persiste correctamente en el XML como atributo del elemento
        if (!businessObject.$attrs) {
            businessObject.$attrs = {};
        }
        businessObject.$attrs['custom:taskConfig'] = JSON.stringify(taskConfig);

        // Forzar que el modeler reconozca el cambio
        modeling.updateProperties(currentTaskElement, {
            name: taskName // Re-aplicar para marcar como modificado
        });

        hideLoading();
        showNotification('Configuración guardada correctamente. No olvides guardar el proceso.', 'success');
        closeModal('taskConfigModal');

    } catch (error) {
        hideLoading();
        showNotification('Error al guardar configuración: ' + error.message, 'error');
    }
};

/**
 * Muestra el modal de crear proceso en modo duplicación
 */
function showCreateProcessModalForDuplicate() {
    // Limpiar nombre (el usuario debe ingresar uno nuevo)
    document.getElementById('processName').value = '';
    document.getElementById('processName').setAttribute('placeholder', 'Ingrese nombre del nuevo proceso...');

    // Cargar descripción del proceso original
    if (window.duplicatedDescription) {
        document.getElementById('processDescription').value = window.duplicatedDescription;
    }

    // Cambiar título del modal si es posible
    const modalTitle = document.querySelector('#createProcessModal h3');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-copy"></i> Duplicar Proceso';
    }

    // Mostrar modal
    document.getElementById('createProcessModal').classList.add('active');

    // Enfocar en el campo nombre
    setTimeout(() => {
        document.getElementById('processName').focus();
    }, 100);
}

/**
 * Cierra un modal
 */
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

/**
 * Muestra una notificación
 */
function showNotification(message, type = 'info') {
    toastr[type](message, "", { timeOut: 3000, positionClass: 'toast-bottom-full-width' });
}

// Crear proceso
async function createProcess() {
    // Validar nombre
    const processName = document.getElementById('processName').value.trim();
    if (!processName) {
        showNotification('El nombre del proceso es requerido', 'warning');
        document.getElementById('processName').focus();
        return;
    }

    if (isProcessing) {
        showNotification('Ya hay una operación en proceso', 'warning');
        return;
    }

    try {
        showLoading('Creando proceso...', 'Generando BPMN');

        const { xml } = await modeler.saveXML({ format: true });

        updateLoadingMessage('Creando proceso...', 'Guardando en base de datos');

        // Preparar datos
        const formData = {
            name: processName,
            description: document.getElementById('processDescription').value.trim(),
            bpmn_xml: xml
        };

        // Si es duplicación, incluir configuración de tareas y variables
        if (isDuplicateMode) {
            if (window.duplicatedTaskDefinitions) {
                formData.task_definitions = JSON.stringify(window.duplicatedTaskDefinitions);
            }
            if (window.duplicatedProcessVariables) {
                formData.process_variables = JSON.stringify(window.duplicatedProcessVariables);
            }
        }

        const response = await fetch('/comun.php/bpmn/processes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData)
        });

        const result = await response.json();

        if (result.success) {
            currentProcessId = result.process_id;

            updateLoadingMessage('Proceso creado', 'Actualizando interfaz');
            await loadProcesses();

            hideLoading();
            showNotification(
                isDuplicateMode ? 'Proceso duplicado correctamente' : 'Proceso creado correctamente',
                'success'
            );
            closeModal('createProcessModal');

            // Limpiar formulario
            document.getElementById('processName').value = '';
            document.getElementById('processDescription').value = '';
            document.getElementById('processName').removeAttribute('placeholder');

            // Restaurar título del modal
            const modalTitle = document.querySelector('#createProcessModal h3');
            if (modalTitle) {
                modalTitle.textContent = 'Crear Nuevo Proceso';
            }

            // Limpiar estado de duplicación
            isDuplicateMode = false;
            window.duplicatedTaskDefinitions = null;
            window.duplicatedProcessVariables = null;
            window.duplicatedDescription = null;

            // Limpiar URL si tenía parámetro duplicate
            if (window.location.search.includes('duplicate')) {
                history.replaceState(null, '', window.location.pathname + '?process=' + currentProcessId);
            }
        } else {
            throw new Error(result.error);
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al crear proceso: ' + error.message, 'error');
    }
}

// ===================
// Manejadores de formularios
// ===================

// Desplegar proceso
/*document.getElementById('deployForm').onsubmit = async function (e) {
    e.preventDefault();

    if (isProcessing) {
        showNotification('Ya hay una operación en proceso', 'warning');
        return;
    }

    try {
        showLoading('Desplegando proceso...', 'Iniciando nueva instancia de workflow');

        const response = await fetch(`/comun.php/bpmn/processes/${currentProcessId}/deploy`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                variables: document.getElementById('deployVariables').value,
                simulation: document.getElementById('isSimulation').checked
            })
        });

        const result = await response.json();

        if (result.success) {
            updateLoadingMessage('Proceso desplegado', 'Actualizando tareas');
            await loadTasks();

            hideLoading();
            showNotification('Proceso desplegado correctamente', 'success');
            closeModal('deployModal');
        } else {
            throw new Error(result.error);
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al desplegar: ' + error.message, 'error');
    }
};*/

// Ejecutar simulación
document.getElementById('simulationForm').onsubmit = async function (e) {
    e.preventDefault();

    if (isProcessing) {
        showNotification('Ya hay una operación en proceso', 'warning');
        return;
    }

    try {
        const numInstances = document.getElementById('numInstances').value;
        showLoading(
            'Ejecutando simulación...',
            `Procesando ${numInstances} instancia(s). Esto puede tardar varios segundos`
        );

        const response = await fetch('/comun.php/bpmn/simulations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                process_id: currentProcessId,
                num_instances: numInstances,
                parameters: document.getElementById('simulationParams').value
            })
        });

        const result = await response.json();

        if (result.success) {
            updateLoadingMessage('Simulación completada', 'Actualizando métricas');
            await loadMetrics(currentProcessId);

            hideLoading();
            showNotification('Simulación iniciada correctamente', 'success');
            closeModal('simulationModal');
        } else {
            throw new Error(result.error);
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al ejecutar simulación: ' + error.message, 'error');
    }
};

// Completar tarea
document.getElementById('taskForm').onsubmit = async function (e) {
    e.preventDefault();

    if (isProcessing) {
        showNotification('Ya hay una operación en proceso', 'warning');
        return;
    }

    try {
        showLoading('Completando tarea...', 'Enviando datos del formulario');

        const formData = new FormData(this);
        const formDataObj = {};
        const customVariables = {};

        // Extraer datos del formulario (campos estándar)
        for (let [key, value] of formData.entries()) {
            if (key !== 'comments' && !key.startsWith('attachments')) {
                formDataObj[key] = value;
            }
        }

        // Extraer variables personalizadas
        const variableContainers = document.querySelectorAll('[id^="var_"]');
        variableContainers.forEach(container => {
            const id = container.id;
            if (id.endsWith('_name')) return; // Skip, solo procesamos el container principal

            const nameInput = document.getElementById(`${id}_name`);
            const typeSelect = document.getElementById(`${id}_type`);
            const valueInput = document.getElementById(`${id}_value`);

            if (nameInput && nameInput.value) {
                let value = valueInput.value;

                // Convertir según tipo
                switch (typeSelect.value) {
                    case 'number':
                        value = parseFloat(value);
                        break;
                    case 'boolean':
                        value = value === 'true';
                        break;
                    case 'date':
                        value = value; // Mantener como string ISO
                        break;
                }

                customVariables[nameInput.value] = value;
            }
        });

        // Si hay archivos, usar FormData para enviar
        const fileInput = document.getElementById('fileInput');
        const hasFiles = fileInput.files.length > 0;

        if (hasFiles) {
            updateLoadingMessage('Completando tarea...', 'Subiendo archivos adjuntos');

            const uploadFormData = new FormData();

            // Agregar archivos
            Array.from(fileInput.files).forEach(file => {
                uploadFormData.append('attachments[]', file);
            });

            // Agregar metadatos
            uploadFormData.append('form_data', JSON.stringify(formDataObj));
            uploadFormData.append('variables', JSON.stringify(customVariables));
            uploadFormData.append('comments', document.getElementById('taskComments').value);
            uploadFormData.append('is_public', document.getElementById('filesPublic').checked);

            const response = await fetch(`/comun.php/bpmn/tasksComplete?task_id=${currentTaskId}`, {
                method: 'POST',
                body: uploadFormData
            });

            const result = await response.json();

            if (result.success) {
                updateLoadingMessage('Tarea completada', 'Actualizando lista de tareas');
                await loadTasks();

                hideLoading();
                showNotification(
                    `Tarea completada. ${result.attachments.length} archivo(s) subido(s).`,
                    'success'
                );
                closeModal('taskModal');
            } else {
                throw new Error(result.error);
            }
        } else {
            // Sin archivos, enviar datos normales
            const response = await fetch(`/comun.php/bpmn/tasksComplete?task_id=${currentTaskId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    form_data: JSON.stringify(formDataObj),
                    variables: JSON.stringify(customVariables),
                    comments: document.getElementById('taskComments').value
                })
            });

            const result = await response.json();

            if (result.success) {
                updateLoadingMessage('Tarea completada', 'Actualizando lista de tareas');
                await loadTasks();

                hideLoading();
                showNotification('Tarea completada correctamente', 'success');
                closeModal('taskModal');
            } else {
                throw new Error(result.error);
            }
        }

    } catch (error) {
        hideLoading();
        showNotification('Error al completar tarea: ' + error.message, 'error');
    }
};

// Cerrar modales al hacer clic fuera
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});