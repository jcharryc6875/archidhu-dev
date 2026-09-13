/**
 * BPMN Process Simulator
 * Maneja la simulación interactiva de procesos BPMN
 */

class BPMNSimulator {
    constructor(bpmnXml, processElements, processId, processVariables, taskForms) {
        this.bpmnXml = bpmnXml;
        this.processElements = processElements;
        this.processId = processId;
        this.processVariables = processVariables || [];
        this.taskForms = taskForms || {};

        this.viewer = null;
        this.canvas = null;
        this.overlays = null;
        this.elementRegistry = null;
        this.viewerReady = false;
        this.initializationPromise = null;

        this.currentElement = null;
        this.executionPath = [];
        this.visitedElements = new Set();
        this.sequenceFlows = processElements.sequenceFlows;

        this.isRunning = false;
        this.isAutoPlay = false;
        this.autoPlayInterval = null;
        this.speed = 2000;

        this.startTime = null;
        this.elapsedInterval = null;

        this.stats = {
            steps: 0,
            tasks: 0,
            decisions: 0
        };

        // Nuevas propiedades para persistencia
        this.instanceId = null;
        this.simulationId = null;
        this.persistToDatabase = true;
        this.currentVariables = {};

        // Callbacks
        this.onVariablesUpdate = null;
        this.onInstanceCreated = null;
    }

    async initialize() {
        // Inicializar el viewer
        await this.initViewer();

        // Inicializar controles
        this.initControls();

        return this;
    }

    initViewer() {
        return new Promise((resolve, reject) => {
            const container = document.getElementById('bpmn-canvas');

            if (!container) {
                reject(new Error('Canvas container not found'));
                return;
            }

            try {
                this.viewer = new BpmnJS({
                    container: container,
                    keyboard: {
                        bindTo: document
                    }
                });

                this.viewer.importXML(this.bpmnXml)
                    .then((result) => {
                        const { warnings } = result;

                        if (warnings.length) {
                            console.warn('Advertencias al cargar BPMN:', warnings);
                        }

                        // Obtener servicios del viewer
                        this.canvas = this.viewer.get('canvas');
                        this.overlays = this.viewer.get('overlays');
                        this.elementRegistry = this.viewer.get('elementRegistry');

                        if (!this.canvas) {
                            reject(new Error('No se pudo obtener el canvas del viewer'));
                            return;
                        }

                        // Ajustar zoom
                        this.canvas.zoom('fit-viewport');

                        // Luego resetear scroll a posición inicial
                        setTimeout(() => {
                            this.canvas.zoom('fit-viewport', 'auto');
                        }, 100);

                        // Marcar como listo
                        this.viewerReady = true;

                        resolve();
                    })
                    .catch((err) => {
                        reject(err);
                    });
            } catch (error) {
                reject(error);
            }
        });
    }

    initControls() {
        // Botón iniciar
        const startBtn = document.getElementById('startSimulation');
        if (startBtn) {
            startBtn.addEventListener('click', () => this.startSimulation());
        }

        // Botón siguiente paso
        const nextBtn = document.getElementById('nextStep');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.executeNextStep());
        }

        // Botón auto play
        const autoPlayBtn = document.getElementById('autoPlay');
        if (autoPlayBtn) {
            autoPlayBtn.addEventListener('click', () => this.toggleAutoPlay());
        }

        // Botón reiniciar
        const resetBtn = document.getElementById('resetSimulation');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetSimulation());
        }

        // Control de velocidad
        const speedControl = document.getElementById('speedControl');
        if (speedControl) {
            speedControl.addEventListener('change', (e) => {
                this.speed = parseInt(e.target.value);
                if (this.isAutoPlay) {
                    this.stopAutoPlay();
                    this.startAutoPlay();
                }
            });
        }

        // Controles de zoom
        const zoomInBtn = document.getElementById('zoomIn');
        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', () => this.handleZoomIn());
        }

        const zoomOutBtn = document.getElementById('zoomOut');
        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', () => this.handleZoomOut());
        }

        const zoomResetBtn = document.getElementById('zoomReset');
        if (zoomResetBtn) {
            zoomResetBtn.addEventListener('click', () => this.handleZoomReset());
        }
    }

    handleZoomIn() {
        if (!this.viewerReady || !this.canvas) {
            console.warn('Canvas no disponible para zoom in');
            return;
        }
        try {
            this.canvas.zoom(this.canvas.zoom() + 0.1);
        } catch (error) {
            console.error('Error en zoom in:', error);
        }
    }

    handleZoomOut() {
        if (!this.viewerReady || !this.canvas) {
            console.warn('Canvas no disponible para zoom out');
            return;
        }
        try {
            this.canvas.zoom(this.canvas.zoom() - 0.1);
        } catch (error) {
            console.error('Error en zoom out:', error);
        }
    }

    handleZoomReset() {
        if (!this.viewerReady || !this.canvas) {
            console.warn('Canvas no disponible para zoom reset');
            return;
        }
        try {
            this.canvas.zoom('fit-viewport');
        } catch (error) {
            console.error('Error en zoom reset:', error);
        }
    }

    startSimulation() {
        if (this.isRunning) {
            return;
        }

        if (!this.viewerReady) {
            this.showError('El visor BPMN aún no está listo. Por favor espera un momento e intenta de nuevo.');
            return;
        }

        if (!this.canvas) {
            this.showError('Error: El canvas no está disponible. Recarga la página e intenta de nuevo.');
            return;
        }

        // Encontrar el evento de inicio
        const startEvents = this.processElements.startEvents;
        if (!startEvents || startEvents.length === 0) {
            this.showError('No se encontró un evento de inicio en el proceso');
            return;
        }

        this.isRunning = true;
        this.currentElement = startEvents[0];
        this.startTime = Date.now();

        // Iniciar contador de tiempo
        this.elapsedInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const elapsedTimeEl = document.getElementById('elapsedTime');
            if (elapsedTimeEl) {
                elapsedTimeEl.textContent = elapsed + 's';
            }
        }, 1000);

        // Actualizar UI
        this.setButtonState('startSimulation', true);
        this.setButtonState('nextStep', false);
        this.setButtonState('autoPlay', false);
        this.setButtonState('resetSimulation', false);

        this.updateSimulationStatus('En Ejecución', 'success');

        // Highlight elemento inicial
        this.highlightElement(this.currentElement.id, 'current');
        this.addToLog('Inicio', 'Proceso iniciado desde: ' + (this.currentElement.name || this.currentElement.id));
        this.updateCurrentState(this.currentElement);

        this.stats.steps++;
        this.updateStats();
    }

    /**
     * Ejecutar siguiente paso (actualizado con persistencia)
     */
    async executeNextStep() {
        if (!this.isRunning) {
            console.warn('La simulación no está en ejecución');
            return;
        }

        if (!this.canvas) {
            this.showError('El canvas no está disponible');
            return;
        }

        // Buscar el siguiente elemento
        const outgoingFlows = this.sequenceFlows.filter(flow =>
            flow.sourceRef === this.currentElement.id
        );

        if (outgoingFlows.length === 0) {
            // Es un elemento final
            this.finishSimulation();
            return;
        }

        // Si hay múltiples flujos (gateway), necesitamos decisión del usuario
        if (outgoingFlows.length > 1) {
            this.handleGatewayDecision(outgoingFlows);
            return;
        }

        // Un solo flujo saliente
        const nextFlow = outgoingFlows[0];

        // Si el siguiente elemento es una user task con formulario, mostrar formulario
        const nextElement = this.findElementById(nextFlow.targetRef);
        if (nextElement && this.taskForms[nextElement.id]) {
            await this.handleUserTask(nextElement, nextFlow.id);
        } else {
            await this.moveToElement(nextFlow.targetRef, nextFlow.id);
        }
    }

    /**
     * Manejar Gateway con múltiples flujos
     */
    handleGatewayDecision(flows) {
        this.stats.decisions++;
        this.updateStats();

        // Pausar auto-play si está activo
        const wasAutoPlaying = this.isAutoPlay;
        if (this.isAutoPlay) {
            this.stopAutoPlay();
        }

        // Mostrar modal con opciones
        const gatewayName = this.currentElement.name || this.currentElement.id;
        const gatewayNameEl = document.getElementById('gatewayName');
        if (gatewayNameEl) {
            gatewayNameEl.textContent = 'Gateway: ' + gatewayName;
        }

        // Crear HTML para las opciones
        const optionsHtml = flows.map((flow, index) => {
            const routeName = flow.name || 'Ruta ' + (index + 1);
            return `
            <button class="btn btn-outline-primary btn-block gateway-option mb-2" 
                    data-flow-id="${flow.id}" 
                    data-target="${flow.targetRef}"
                    style="text-align: left; padding: 15px;">
                <i class="fas fa-arrow-right"></i> 
                ${this.escapeHtml(routeName)}
            </button>
        `;
        }).join('');

        const optionsContainer = document.getElementById('gatewayOptions');
        if (optionsContainer) {
            optionsContainer.innerHTML = optionsHtml;
        }

        // Agregar eventos a los botones
        const self = this;
        setTimeout(() => {
            document.querySelectorAll('.gateway-option').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const flowId = this.getAttribute('data-flow-id');
                    const targetRef = this.getAttribute('data-target');

                    // Cerrar modal
                    self.closeModal();

                    // Continuar simulación
                    self.moveToElement(targetRef, flowId);

                    // Reanudar auto-play si estaba activo
                    if (wasAutoPlaying) {
                        setTimeout(() => self.startAutoPlay(), 500);
                    }
                });
            });
        }, 100);

        // Mostrar modal
        this.showModal();
    }

    showModal() {
        const modal = document.getElementById('gatewayModal');
        if (!modal) {
            // Crear modal dinámicamente si no existe
            this.createModalDynamically();
            return;
        }

        // Método simple y directo
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        // Agregar backdrop si no existe
        let backdrop = document.getElementById('gateway-modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'gateway-modal-backdrop';
            backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        `;
            document.body.appendChild(backdrop);
        }

        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        const modal = document.getElementById('gatewayModal');
        if (!modal) return;

        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        // Remover backdrop
        const backdrop = document.getElementById('gateway-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }

        document.body.style.overflow = '';
    }

    createModalDynamically() {
        const modalHTML = `
            <div class="modal" id="gatewayModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-code-branch"></i> Seleccionar Ruta
                            </h5>
                            <button type="button" class="close text-white" onclick="window.bpmnSimulator.closeModal()">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="gatewayName" class="font-weight-bold mb-3"></p>
                            <p class="text-muted">Selecciona la ruta que deseas seguir:</p>
                            <div id="gatewayOptions"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Intentar mostrar de nuevo
        setTimeout(() => this.showModal(), 100);
    }

    highlightElement(elementId, type) {
        if (!this.canvas || !this.viewerReady) {
            console.warn('Canvas no disponible para highlight');
            return;
        }

        const colors = {
            'current': '#007bff',
            'visited': '#28a745',
            'executed': '#6c757d'
        };

        try {
            // En bpmn-js, los markers se agregan a través del canvas pero primero verificamos que el elemento exista
            if (!this.elementRegistry) {
                console.warn('Element registry no disponible');
                return;
            }

            const element = this.elementRegistry.get(elementId);
            if (!element) {
                console.warn('Elemento no encontrado en el registro:', elementId);
                return;
            }

            // Agregar clase CSS para el highlighting
            const gfx = this.elementRegistry.getGraphics(elementId);
            if (gfx) {
                // Remover clases anteriores
                gfx.classList.remove('current', 'visited', 'executed');
                // Agregar nueva clase
                gfx.classList.add(type);

                // Para sequence flows, cambiar el estilo directamente
                if (element.type === 'bpmn:SequenceFlow') {
                    const path = gfx.querySelector('path');
                    if (path) {
                        path.style.stroke = colors[type];
                        path.style.strokeWidth = '3px';
                    }
                }
            }
        } catch (e) {
            console.warn('No se pudo marcar elemento:', elementId, e);
        }
    }

    getElementType(element) {
        if (this.processElements.userTasks.some(e => e.id === element.id)) {
            return 'Tarea de Usuario';
        }
        if (this.processElements.tasks.some(e => e.id === element.id)) {
            return 'Tarea';
        }
        if (this.processElements.gateways.some(e => e.id === element.id)) {
            const gateway = this.processElements.gateways.find(e => e.id === element.id);
            return 'Gateway (' + (gateway.type || 'exclusive') + ')';
        }
        if (this.processElements.endEvents.some(e => e.id === element.id)) {
            return 'Evento Final';
        }
        if (this.processElements.startEvents.some(e => e.id === element.id)) {
            return 'Evento Inicio';
        }
        return 'Elemento';
    }

    updateCurrentState(element) {
        const elementType = this.getElementType(element);
        const html = `
            <div class="element-info">
                <div class="element-icon">
                    <i class="fas fa-${this.getElementIcon(elementType)}"></i>
                </div>
                <div class="element-details">
                    <div class="element-type">${this.escapeHtml(elementType)}</div>
                    <div class="element-name">${this.escapeHtml(element.name || element.id)}</div>
                    <div class="element-id">ID: ${this.escapeHtml(element.id)}</div>
                </div>
            </div>
        `;

        const currentElement = document.getElementById('currentElement');
        if (currentElement) {
            currentElement.innerHTML = html;
        }
    }

    getElementIcon(type) {
        if (type.includes('Usuario')) return 'user';
        if (type.includes('Tarea')) return 'tasks';
        if (type.includes('Gateway')) return 'code-branch';
        if (type.includes('Final')) return 'flag-checkered';
        if (type.includes('Inicio')) return 'play-circle';
        return 'circle';
    }

    addToLog(type, message) {
        const logContainer = document.getElementById('executionLog');
        if (!logContainer) return;

        // Remover mensaje de vacío si existe
        const emptyMsg = logContainer.querySelector('.log-empty');
        if (emptyMsg) emptyMsg.remove();

        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        logEntry.innerHTML = `
            <span class="log-time">${this.escapeHtml(timestamp)}</span>
            <span class="log-type">${this.escapeHtml(type)}</span>
            <span class="log-message">${this.escapeHtml(message)}</span>
        `;

        logContainer.insertBefore(logEntry, logContainer.firstChild);
    }

    updateStats() {
        this.setElementText('stepsCount', this.stats.steps);
        this.setElementText('tasksCount', this.stats.tasks);
        this.setElementText('decisionsCount', this.stats.decisions);
    }

    updateSimulationStatus(text, status) {
        const statusBadge = document.getElementById('simulationStatus');
        if (statusBadge) {
            statusBadge.className = 'badge badge-' + status;
            statusBadge.innerHTML = `<i class="fas fa-circle"></i> ${this.escapeHtml(text)}`;
        }
    }

    toggleAutoPlay() {
        if (this.isAutoPlay) {
            this.stopAutoPlay();
        } else {
            this.startAutoPlay();
        }
    }

    startAutoPlay() {
        this.isAutoPlay = true;

        const autoPlayBtn = document.getElementById('autoPlay');
        if (autoPlayBtn) {
            autoPlayBtn.innerHTML = '<i class="fas fa-pause"></i> Pausar Auto-Play';
        }

        this.setButtonState('nextStep', true);

        this.autoPlayInterval = setInterval(() => {
            this.executeNextStep();
        }, this.speed);
    }

    stopAutoPlay() {
        this.isAutoPlay = false;

        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }

        const autoPlayBtn = document.getElementById('autoPlay');
        if (autoPlayBtn) {
            autoPlayBtn.innerHTML = '<i class="fas fa-forward"></i> Reproducción Automática';
        }

        this.setButtonState('nextStep', false);
    }

    finishSimulation() {
        this.isRunning = false;

        if (this.isAutoPlay) {
            this.stopAutoPlay();
        }

        if (this.elapsedInterval) {
            clearInterval(this.elapsedInterval);
        }

        this.updateSimulationStatus('Completada', 'success');
        this.addToLog('Fin', 'Simulación completada exitosamente');

        this.setButtonState('nextStep', true);
        this.setButtonState('autoPlay', true);

        alert('¡Simulación completada!\n\n' +
            'Pasos ejecutados: ' + this.stats.steps + '\n' +
            'Tareas completadas: ' + this.stats.tasks);
    }

    resetSimulation() {
        if (!confirm('¿Estás seguro de que deseas reiniciar la simulación?')) {
            return;
        }

        // Detener auto-play y timers
        if (this.isAutoPlay) {
            this.stopAutoPlay();
        }

        if (this.elapsedInterval) {
            clearInterval(this.elapsedInterval);
            this.elapsedInterval = null;
        }

        // Limpiar TODOS los elementos del diagrama
        if (this.elementRegistry && this.viewerReady) {
            // Obtener TODOS los elementos gráficos del diagrama
            const allElements = this.elementRegistry.getAll();

            allElements.forEach(element => {
                try {
                    const gfx = this.elementRegistry.getGraphics(element.id);
                    if (gfx) {
                        // Remover todas las clases de highlighting
                        gfx.classList.remove('visited', 'current', 'executed');

                        // Si es un sequence flow, limpiar estilos inline
                        if (element.type === 'bpmn:SequenceFlow') {
                            const path = gfx.querySelector('path');
                            if (path) {
                                path.style.stroke = '';
                                path.style.strokeWidth = '';
                            }
                        }
                    }
                } catch (e) {
                    console.warn('Error limpiando elemento:', element.id, e);
                }
            });
        }

        // Resetear estado interno
        this.currentElement = null;
        this.executionPath = [];
        this.visitedElements.clear();
        this.isRunning = false;
        this.startTime = null;
        this.stats = { steps: 0, tasks: 0, decisions: 0 };

        // Resetear UI - Botones
        this.setButtonState('startSimulation', false);
        this.setButtonState('nextStep', true);
        this.setButtonState('autoPlay', true);
        this.setButtonState('resetSimulation', true);

        // Resetear UI - Contenido
        const currentElement = document.getElementById('currentElement');
        if (currentElement) {
            currentElement.innerHTML = `
            <div class="no-simulation">
                <i class="fas fa-info-circle"></i>
                <p>Presiona "Iniciar Simulación" para comenzar</p>
            </div>
        `;
        }

        const executionLog = document.getElementById('executionLog');
        if (executionLog) {
            executionLog.innerHTML = `
            <div class="log-empty">
                <i class="fas fa-clipboard-list"></i>
                <p>El historial aparecerá aquí</p>
            </div>
        `;
        }

        this.setElementText('elapsedTime', '0s');
        this.updateStats();
        this.updateSimulationStatus('Detenida', 'warning');

        // Resetear vista del diagrama
        if (this.canvas && this.viewerReady) {
            try {
                // Primero ajustar zoom
                this.canvas.zoom('fit-viewport');

                // Luego resetear scroll a posición inicial
                setTimeout(() => {
                    this.canvas.zoom('fit-viewport', 'auto');
                }, 100);
            } catch (error) {
                console.warn('Error al resetear vista:', error);
            }
        }
    }

    /**
 * Manejar User Task con formulario
 */
    async handleUserTask(taskElement, flowId) {
        const taskForm = this.taskForms[taskElement.id];

        if (!taskForm || !taskForm.fields || taskForm.fields.length === 0) {
            // No hay formulario, continuar normalmente
            await this.moveToElement(taskElement.id, flowId);
            return;
        }

        // Mostrar formulario de la tarea
        const taskData = await this.promptForTaskData(taskElement, taskForm);

        if (taskData === null) {
            // Usuario canceló
            return;
        }

        // Actualizar variables
        this.currentVariables = { ...this.currentVariables, ...taskData };

        // Continuar con el movimiento
        await this.moveToElement(taskElement.id, flowId, taskData);
    }

    /**
     * Solicitar datos de tarea al usuario
     */
    async promptForTaskData(taskElement, taskForm) {
        return new Promise((resolve) => {
            const modalHTML = `
            <div class="modal" id="taskFormModal" style="display: flex;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-tasks"></i> ${this.escapeHtml(taskElement.name || taskElement.id)}
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Completa los siguientes campos:</p>
                            <form id="taskDataForm">
                                ${taskForm.fields.map(field => this.renderTaskField(field)).join('')}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.cancelTaskForm()">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-success" onclick="window.submitTaskForm()">
                                <i class="fas fa-check"></i> Completar Tarea
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            window.cancelTaskForm = () => {
                document.getElementById('taskFormModal').remove();
                resolve(null);
            };

            window.submitTaskForm = () => {
                const form = document.getElementById('taskDataForm');
                const formData = new FormData(form);
                const data = {};

                for (const [key, value] of formData.entries()) {
                    data[key] = value;
                }

                document.getElementById('taskFormModal').remove();
                resolve(data);
            };
        });
    }

    /**
     * Renderizar campo de formulario de tarea
     */
    renderTaskField(field) {
        const fieldId = 'field_' + field.id;
        const required = field.required ? 'required' : '';

        switch (field.type) {
            case 'string':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">
                        ${this.escapeHtml(field.label)}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <input type="text" class="form-control" id="${fieldId}" name="${field.id}" 
                           value="${this.escapeHtml(field.defaultValue || '')}" ${required} />
                </div>
            `;

            case 'long':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">
                        ${this.escapeHtml(field.label)}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <input type="number" class="form-control" id="${fieldId}" name="${field.id}" 
                           value="${field.defaultValue || ''}" ${required} />
                </div>
            `;

            case 'boolean':
                return `
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="${fieldId}" name="${field.id}" 
                               value="true" ${field.defaultValue === 'true' ? 'checked' : ''} />
                        <label class="form-check-label" for="${fieldId}">
                            ${this.escapeHtml(field.label)}
                            ${field.required ? '<span class="text-danger">*</span>' : ''}
                        </label>
                    </div>
                </div>
            `;

            case 'date':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">
                        ${this.escapeHtml(field.label)}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <input type="date" class="form-control" id="${fieldId}" name="${field.id}" 
                           value="${field.defaultValue || ''}" ${required} />
                </div>
            `;

            case 'enum':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">
                        ${this.escapeHtml(field.label)}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <select class="form-control" id="${fieldId}" name="${field.id}" ${required}>
                        <option value="">Seleccionar...</option>
                        ${(field.values || []).map(opt => `
                            <option value="${this.escapeHtml(opt.id)}" 
                                    ${field.defaultValue === opt.id ? 'selected' : ''}>
                                ${this.escapeHtml(opt.name)}
                            </option>
                        `).join('')}
                    </select>
                </div>
            `;

            default:
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">
                        ${this.escapeHtml(field.label)}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <input type="text" class="form-control" id="${fieldId}" name="${field.id}" 
                           value="${this.escapeHtml(field.defaultValue || '')}" ${required} />
                </div>
            `;
        }
    }

    /**
     * Mover a elemento (actualizado con persistencia)
     */
    async moveToElement(elementId, flowId, taskData = null) {
        if (!this.canvas) {
            console.error('Canvas no disponible para mover elemento');
            return;
        }

        // Marcar flujo como ejecutado
        if (flowId) {
            this.highlightElement(flowId, 'executed');
        }

        // Marcar elemento anterior como visitado
        this.highlightElement(this.currentElement.id, 'visited');
        this.visitedElements.add(this.currentElement.id);

        // Buscar el nuevo elemento
        const nextElement = this.findElementById(elementId);

        if (!nextElement) {
            console.error('Elemento no encontrado:', elementId);
            return;
        }

        // Guardar paso en base de datos si está habilitado
        if (this.persistToDatabase && this.instanceId) {
            try {
                const response = await this.saveStepToDatabase(
                    this.currentElement.id,
                    nextElement.id,
                    flowId,
                    taskData
                );

                if (!response.success) {
                    console.error('Error guardando paso:', response.message);
                }

                // Verificar si llegamos al final
                if (response.is_end) {
                    this.finishSimulation();
                    return;
                }

            } catch (error) {
                console.error('Error al guardar paso:', error);
            }
        }

        this.currentElement = nextElement;
        this.executionPath.push(nextElement);

        // Highlight nuevo elemento
        this.highlightElement(elementId, 'current');

        // Agregar al log
        const elementType = this.getElementType(nextElement);
        this.addToLog(elementType, nextElement.name || nextElement.id);

        // Actualizar estado actual
        this.updateCurrentState(nextElement);

        // Actualizar estadísticas
        this.stats.steps++;
        if (elementType === 'Tarea de Usuario' || elementType === 'Tarea') {
            this.stats.tasks++;
        }
        this.updateStats();

        // Verificar si es un evento final
        if (this.processElements.endEvents.some(e => e.id === elementId)) {
            this.finishSimulation();
        }
    }

    /**
    * Guardar paso en base de datos
    */
    async saveStepToDatabase(fromNode, toNode, flowId, taskData) {
        const url = '/comun.php/bpmn/executeStep';

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                instance_id: this.instanceId,
                current_node: fromNode,
                next_node: toNode,
                flow_id: flowId || '',
                task_data: JSON.stringify(taskData || {})
            })
        });

        return await response.json();
    }

    /**
     * Iniciar simulación con persistencia en base de datos
     */
    async startSimulation() {
        if (this.isRunning) {
            console.warn('Simulación ya está en ejecución');
            return;
        }

        if (!this.viewerReady) {
            this.showError('El visor BPMN aún no está listo. Por favor espera un momento e intenta de nuevo.');
            return;
        }

        if (!this.canvas) {
            this.showError('Error: El canvas no está disponible. Recarga la página e intenta de nuevo.');
            return;
        }

        // Encontrar el evento de inicio
        const startEvents = this.processElements.startEvents;
        if (!startEvents || startEvents.length === 0) {
            this.showError('No se encontró un evento de inicio en el proceso');
            return;
        }

        // Si hay variables de proceso, solicitar valores antes de iniciar
        if (this.processVariables.length > 0) {
            const variables = await this.promptForVariables();
            if (variables === null) {
                // Usuario canceló
                return;
            }
            this.currentVariables = variables;
        } else {
            this.currentVariables = {};
        }

        // Crear instancia en base de datos si está habilitado
        if (this.persistToDatabase) {
            try {
                const response = await this.createInstanceInDatabase(this.currentVariables);
                if (response.success) {
                    this.instanceId = response.instance_id;
                    this.simulationId = response.simulation_id;

                    // Notificar creación de instancia
                    this.notifyInstanceCreated();

                    // Actualizar display de variables
                    this.updateVariablesInDisplay();
                } else {
                    this.showError('Error al crear instancia: ' + response.message);
                    return;
                }
            } catch (error) {
                this.showError('Error al iniciar simulación en base de datos');
                return;
            }
        }

        // Iniciar simulación
        this.isRunning = true;
        this.currentElement = startEvents[0];
        this.startTime = Date.now();

        // Iniciar contador de tiempo
        this.elapsedInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const elapsedTimeEl = document.getElementById('elapsedTime');
            if (elapsedTimeEl) {
                elapsedTimeEl.textContent = elapsed + 's';
            }
        }, 1000);

        // Actualizar UI
        this.setButtonState('startSimulation', true);
        this.setButtonState('nextStep', false);
        this.setButtonState('autoPlay', false);
        this.setButtonState('resetSimulation', false);

        this.updateSimulationStatus('En Ejecución', 'success');

        // Highlight elemento inicial
        this.highlightElement(this.currentElement.id, 'current');
        this.addToLog('Inicio', 'Proceso iniciado desde: ' + (this.currentElement.name || this.currentElement.id));
        this.updateCurrentState(this.currentElement);

        this.stats.steps++;
        this.updateStats();
    }

    /**
     * Solicitar valores de variables al usuario
     */
    async promptForVariables() {
        return new Promise((resolve) => {
            // Crear modal para variables
            const modalHTML = `
            <div class="modal" id="variablesModal" style="display: flex;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-database"></i> Variables del Proceso
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Ingresa los valores iniciales para las variables del proceso:</p>
                            <form id="variablesForm">
                                ${this.processVariables.map(variable => this.renderVariableField(variable)).join('')}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="window.cancelVariables()">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-success" onclick="window.submitVariables()">
                                <i class="fas fa-check"></i> Continuar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Funciones globales para manejar el modal
            window.cancelVariables = () => {
                document.getElementById('variablesModal').remove();
                resolve(null);
            };

            window.submitVariables = () => {
                const form = document.getElementById('variablesForm');
                const formData = new FormData(form);
                const variables = {};

                for (const [key, value] of formData.entries()) {
                    variables[key] = value;
                }

                document.getElementById('variablesModal').remove();
                resolve(variables);
            };
        });
    }

    /**
     * Renderizar campo de variable según su tipo
     */
    renderVariableField(variable) {
        const fieldId = 'var_' + variable.id;
        const fieldName = variable.id;

        switch (variable.type) {
            case 'string':
            case 'text':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">${this.escapeHtml(variable.name)}</label>
                    <input type="text" class="form-control" id="${fieldId}" name="${fieldName}" 
                           value="${this.escapeHtml(variable.value || '')}" />
                </div>
            `;

            case 'long':
            case 'integer':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">${this.escapeHtml(variable.name)}</label>
                    <input type="number" class="form-control" id="${fieldId}" name="${fieldName}" 
                           value="${variable.value || 0}" />
                </div>
            `;

            case 'boolean':
                return `
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="${fieldId}" name="${fieldName}" 
                               value="true" ${variable.value ? 'checked' : ''} />
                        <label class="form-check-label" for="${fieldId}">
                            ${this.escapeHtml(variable.name)}
                        </label>
                    </div>
                </div>
            `;

            case 'date':
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">${this.escapeHtml(variable.name)}</label>
                    <input type="date" class="form-control" id="${fieldId}" name="${fieldName}" 
                           value="${variable.value || ''}" />
                </div>
            `;

            case 'enum':
                // Asumiendo que las opciones vienen en variable.values
                const options = variable.values || [];
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">${this.escapeHtml(variable.name)}</label>
                    <select class="form-control" id="${fieldId}" name="${fieldName}">
                        <option value="">Seleccionar...</option>
                        ${options.map(opt => `
                            <option value="${this.escapeHtml(opt.id)}" 
                                    ${variable.value === opt.id ? 'selected' : ''}>
                                ${this.escapeHtml(opt.name)}
                            </option>
                        `).join('')}
                    </select>
                </div>
            `;

            default:
                return `
                <div class="form-group mb-3">
                    <label for="${fieldId}">${this.escapeHtml(variable.name)}</label>
                    <input type="text" class="form-control" id="${fieldId}" name="${fieldName}" 
                           value="${this.escapeHtml(variable.value || '')}" />
                </div>
            `;
        }
    }

    /**
     * Crear instancia en base de datos
     */
    async createInstanceInDatabase(variables) {
        const url = '/comun.php/bpmn/startSimulation';

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                process_id: this.processId,
                variables: JSON.stringify(variables),
                is_simulation: '1'
            })
        });

        return await response.json();
    }

    updateVariablesInDisplay() {
        if (this.onVariablesUpdate && typeof this.onVariablesUpdate === 'function') {
            this.onVariablesUpdate(this.currentVariables);
        }
    }

    notifyInstanceCreated() {
        if (this.onInstanceCreated && typeof this.onInstanceCreated === 'function') {
            this.onInstanceCreated(this.instanceId, this.simulationId);
        }
    }

    /**
     * Encontrar elemento por ID
     */
    findElementById(elementId) {
        const allElements = [
            ...this.processElements.userTasks,
            ...this.processElements.tasks,
            ...this.processElements.gateways,
            ...this.processElements.endEvents
        ];

        return allElements.find(el => el.id === elementId);
    }

    // Métodos auxiliares
    setButtonState(buttonId, disabled) {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = disabled;
        }
    }

    setElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        alert(message);
    }
}

// Función de inicialización global con async/await
async function initBPMNSimulator(bpmnXml, processElements, processId, processVariables, taskForms) {
    if (typeof BpmnJS === 'undefined') {
        alert('Error: La librería bpmn-js no está cargada. Por favor recarga la página.');
        return null;
    }

    try {
        const simulator = new BPMNSimulator(bpmnXml, processElements, processId, processVariables, taskForms);
        await simulator.initialize();

        return simulator;
    } catch (error) {
        alert('Error al inicializar el simulador: ' + error.message + '\n\nPor favor recarga la página e intenta de nuevo.');
        return null;
    }
}