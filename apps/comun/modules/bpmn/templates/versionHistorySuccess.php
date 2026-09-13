<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object', 'jQuery', 'UserComponent');
?>

<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn-wfhystory.css">

<div class="version-container">
    <!-- Header -->
    <div class="version-header">
        <h2>
            <i class="fas fa-history"></i>
            Historial de Versiones
        </h2>
        <div>
            <a href="<?php echo url_for('bpmn/index') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo url_for('bpmn/designer') ?>?process=<?php echo $processId ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Proceso
            </a>
        </div>
    </div>

    <!-- Información del proceso -->
    <div class="process-info">
        <h3 id="processName">Cargando...</h3>
        <p id="processDescription"></p>
    </div>

    <!-- Estadísticas -->
    <div class="version-stats">
        <div class="stat-card primary">
            <div class="stat-value" id="totalVersions">-</div>
            <div class="stat-label">Total Versiones</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value" id="currentVersion">-</div>
            <div class="stat-label">Versión Actual</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value" id="archivedVersions">-</div>
            <div class="stat-label">Archivadas</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value" id="lastUpdate">-</div>
            <div class="stat-label">Última Modificación</div>
        </div>
    </div>

    <!-- Tabs de vista -->
    <div class="view-tabs">
        <button class="view-tab active" onclick="switchView('table')">
            <i class="fas fa-table"></i> Tabla
        </button>
        <button class="view-tab" onclick="switchView('timeline')">
            <i class="fas fa-stream"></i> Línea de Tiempo
        </button>
    </div>

    <!-- Vista de tabla -->
    <div id="tableView" class="view-content active">
        <div class="version-table-container">
            <table class="version-table">
                <thead>
                    <tr>
                        <th>Versión</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th>Fecha Creación</th>
                        <th>Cambios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="versionTableBody">
                    <tr>
                        <td colspan="6" class="text-center">Cargando versiones...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vista de línea de tiempo -->
    <div id="timelineView" class="view-content">
        <div class="version-timeline" id="versionTimeline">
            <!-- Se genera dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal de vista previa -->
<div class="modal" id="previewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-eye"></i>
                <span id="previewTitle">Vista Previa - Versión</span>
            </h3>
            <button class="btn-close" onclick="closeModal('previewModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="previewCanvas" class="preview-canvas"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('previewModal')">Cerrar</button>
            <button class="btn btn-success" id="btnRestoreFromPreview" onclick="restoreVersion()">
                <i class="fas fa-undo"></i> Restaurar esta versión
            </button>
        </div>
    </div>
</div>

<!-- Modal de comparación -->
<div class="modal" id="compareModal">
    <div class="modal-content" style="max-width: 1400px;">
        <div class="modal-header">
            <h3>
                <i class="fas fa-columns"></i>
                Comparar Versiones
            </h3>
            <button class="btn-close" onclick="closeModal('compareModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="compare-container">
                <div class="compare-panel">
                    <div class="compare-panel-header">
                        <select id="compareVersion1" onchange="loadCompareVersion(1)">
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                    <div id="compareCanvas1" class="compare-canvas"></div>
                </div>
                <div class="compare-panel">
                    <div class="compare-panel-header">
                        <select id="compareVersion2" onchange="loadCompareVersion(2)">
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                    <div id="compareCanvas2" class="compare-canvas"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('compareModal')">Cerrar</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo $path_theme; ?>assets/js/bmpn/bpmn-modeler.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bmpn/diagram-grid.umd.prod.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bmpn/color-picker-module.js"></script>

<script>
    const processId = <?php echo $processId ?>;
    let versions = [];
    let currentVersionId = null;
    let previewViewer = null;
    let compareViewer1 = null;
    let compareViewer2 = null;

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        loadProcessInfo();
        loadVersions();
    });

    /**
     * Carga información del proceso
     */
    async function loadProcessInfo() {
        try {
            const response = await fetch(`/comun.php/bpmn/process?process=${processId}`);
            const data = await response.json();

            if (data.success !== false) {
                document.getElementById('processName').textContent = data.name || 'Sin nombre';
                document.getElementById('processDescription').textContent = data.description || 'Sin descripción';
                document.getElementById('currentVersion').textContent = data.version || '1';
            }
        } catch (error) {
            console.error('Error al cargar proceso:', error);
        }
    }

    /**
     * Carga el historial de versiones
     */
    async function loadVersions() {
        try {
            const response = await fetch(`/comun.php/bpmn/getVersionHistory?process_id=${processId}`);
            const data = await response.json();

            if (data.success) {
                versions = data.versions;
                renderVersionTable();
                renderVersionTimeline();
                updateStats();
                populateCompareSelects();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            document.getElementById('versionTableBody').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center" style="color: #dc3545;">
                        Error al cargar versiones: ${error.message}
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Renderiza la tabla de versiones
     */
    function renderVersionTable() {
        const tbody = document.getElementById('versionTableBody');

        if (versions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No hay versiones registradas</h3>
                            <p>Las versiones se crean automáticamente al guardar cambios en el proceso.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = versions.map((v, index) => {
            const isCurrentVersion = index === 0 && !v.fecha_archivado;
            const isArchived = v.fecha_archivado !== null;
            const rowClass = isCurrentVersion ? 'current-version' : (isArchived ? 'archived' : '');

            return `
                <tr class="${rowClass}" data-version-id="${v.id}">
                    <td>
                        <strong style="font-size: 1.2em;">v${v.version}</strong>
                    </td>
                    <td>
                        ${isCurrentVersion ? 
                            '<span class="version-badge current"><i class="fas fa-check"></i> Actual</span>' : 
                            (isArchived ? 
                                '<span class="version-badge archived"><i class="fas fa-archive"></i> Archivada</span>' : 
                                '<span class="version-badge draft"><i class="fas fa-file"></i> Histórica</span>'
                            )
                        }
                    </td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">${getInitials(v.usuario_nombre)}</div>
                            <span class="user-name">${v.usuario_nombre || 'Sistema'}</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date">${formatDate(v.fecha_creacion)}</div>
                            <div class="time">${formatTime(v.fecha_creacion)}</div>
                        </div>
                    </td>
                    <td>
                        <div class="changes-summary">
                            ${renderChangesSummary(v.changes_summary)}
                        </div>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn-sm btn-view" onclick="previewVersion(${v.id}, ${v.version})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${!isCurrentVersion ? `
                                <button class="btn-sm btn-restore" onclick="confirmRestore(${v.id}, ${v.version})" title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                            ` : ''}
                            <button class="btn-sm btn-download" onclick="downloadVersion(${v.id}, ${v.version})" title="Descargar XML">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    /**
     * Renderiza la línea de tiempo
     */
    function renderVersionTimeline() {
        const timeline = document.getElementById('versionTimeline');

        if (versions.length === 0) {
            timeline.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h3>No hay versiones registradas</h3>
                </div>
            `;
            return;
        }

        timeline.innerHTML = versions.map((v, index) => {
            const isCurrentVersion = index === 0 && !v.fecha_archivado;

            return `
                <div class="timeline-item ${isCurrentVersion ? 'current' : ''}">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <strong>Versión ${v.version}</strong>
                            ${isCurrentVersion ? '<span class="version-badge current" style="margin-left: 10px;"><i class="fas fa-check"></i> Actual</span>' : ''}
                            <div style="color: #666; margin-top: 5px;">
                                ${formatDate(v.fecha_creacion)} a las ${formatTime(v.fecha_creacion)}
                            </div>
                            <div style="margin-top: 5px;">
                                <i class="fas fa-user" style="color: #999;"></i> ${v.usuario_nombre || 'Sistema'}
                            </div>
                            <div style="margin-top: 10px;">
                                ${renderChangesSummary(v.changes_summary)}
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn-sm btn-view" onclick="previewVersion(${v.id}, ${v.version})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            ${!isCurrentVersion ? `
                                <button class="btn-sm btn-restore" onclick="confirmRestore(${v.id}, ${v.version})">
                                    <i class="fas fa-undo"></i> Restaurar
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Actualiza las estadísticas
     */
    function updateStats() {
        document.getElementById('totalVersions').textContent = versions.length;

        const archived = versions.filter(v => v.fecha_archivado !== null).length;
        document.getElementById('archivedVersions').textContent = archived;

        if (versions.length > 0) {
            const lastDate = new Date(versions[0].fecha_creacion);
            const now = new Date();
            const diffDays = Math.floor((now - lastDate) / (1000 * 60 * 60 * 24));

            if (diffDays === 0) {
                document.getElementById('lastUpdate').textContent = 'Hoy';
            } else if (diffDays === 1) {
                document.getElementById('lastUpdate').textContent = 'Ayer';
            } else {
                document.getElementById('lastUpdate').textContent = diffDays + ' días';
            }
        }
    }

    /**
     * Renderiza el resumen de cambios
     */
    function renderChangesSummary(summary) {
        if (!summary) return '<span style="color: #999;">Sin detalles</span>';

        try {
            const changes = JSON.parse(summary);
            let html = '';

            if (changes.added && changes.added.length > 0) {
                changes.added.forEach(item => {
                    html += `<span class="change-item added"><i class="fas fa-plus"></i> ${item}</span>`;
                });
            }

            if (changes.removed && changes.removed.length > 0) {
                changes.removed.forEach(item => {
                    html += `<span class="change-item removed"><i class="fas fa-minus"></i> ${item}</span>`;
                });
            }

            if (changes.modified && changes.modified.length > 0) {
                changes.modified.forEach(item => {
                    html += `<span class="change-item modified"><i class="fas fa-edit"></i> ${item}</span>`;
                });
            }

            return html || '<span style="color: #999;">Sin cambios registrados</span>';
        } catch (e) {
            return `<span style="color: #666;">${summary}</span>`;
        }
    }

    /**
     * Cambia entre vistas
     */
    function switchView(view) {
        document.querySelectorAll('.view-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.view-content').forEach(content => content.classList.remove('active'));

        document.querySelector(`.view-tab[onclick="switchView('${view}')"]`).classList.add('active');
        document.getElementById(view + 'View').classList.add('active');
    }

    /**
     * Vista previa de una versión
     */
    async function previewVersion(versionId, versionNumber) {
        currentVersionId = versionId;
        document.getElementById('previewTitle').textContent = `Vista Previa - Versión ${versionNumber}`;

        // Mostrar modal
        document.getElementById('previewModal').classList.add('active');

        try {
            // Obtener XML de la versión
            const response = await fetch(`/comun.php/bpmn/getVersionXml?version_id=${versionId}`);
            const data = await response.json();

            if (data.success) {
                // Inicializar viewer si no existe
                if (!previewViewer) {
                    previewViewer = new BpmnJS({
                        container: '#previewCanvas'
                    });
                }

                await previewViewer.importXML(data.bpmn_xml);

                // Ajustar vista
                const canvas = previewViewer.get('canvas');
                canvas.zoom('fit-viewport');
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            document.getElementById('previewCanvas').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error al cargar vista previa</h3>
                    <p>${error.message}</p>
                </div>
            `;
        }
    }

    /**
     * Confirmar restauración de versión
     */
    function confirmRestore(versionId, versionNumber) {
        if (confirm(`¿Estás seguro de restaurar la versión ${versionNumber}?\n\nEsto creará una nueva versión basada en la seleccionada.`)) {
            restoreVersionById(versionId);
        }
    }

    /**
     * Restaurar versión desde el modal de preview
     */
    function restoreVersion() {
        if (currentVersionId) {
            const version = versions.find(v => v.id === currentVersionId);
            if (version) {
                confirmRestore(currentVersionId, version.version);
            }
        }
    }

    /**
     * Restaurar una versión específica
     */
    async function restoreVersionById(versionId) {
        try {
            const response = await fetch('/comun.php/bpmn/restoreVersion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    version_id: versionId,
                    process_id: processId
                })
            });

            const data = await response.json();

            if (data.success) {
                toastr.success('Versión restaurada correctamente');
                closeModal('previewModal');
                loadVersions();
                loadProcessInfo();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            toastr.error('Error al restaurar: ' + error.message);
        }
    }

    /**
     * Descargar XML de una versión
     */
    async function downloadVersion(versionId, versionNumber) {
        try {
            const response = await fetch(`/comun.php/bpmn/getVersionXml?version_id=${versionId}`);
            const data = await response.json();

            if (data.success) {
                const blob = new Blob([data.bpmn_xml], {
                    type: 'application/xml'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `proceso_${processId}_v${versionNumber}.bpmn`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            toastr.error('Error al descargar: ' + error.message);
        }
    }

    /**
     * Poblar selects de comparación
     */
    function populateCompareSelects() {
        const select1 = document.getElementById('compareVersion1');
        const select2 = document.getElementById('compareVersion2');

        const options = versions.map(v =>
            `<option value="${v.id}">Versión ${v.version} - ${formatDate(v.fecha_creacion)}</option>`
        ).join('');

        select1.innerHTML = options;
        select2.innerHTML = options;

        // Seleccionar las dos primeras versiones por defecto
        if (versions.length >= 2) {
            select1.value = versions[0].id;
            select2.value = versions[1].id;
        }
    }

    /**
     * Abrir modal de comparación
     */
    function openCompareModal() {
        if (versions.length < 2) {
            toastr.warning('Se necesitan al menos 2 versiones para comparar');
            return;
        }

        document.getElementById('compareModal').classList.add('active');
        loadCompareVersion(1);
        loadCompareVersion(2);
    }

    /**
     * Cargar versión en panel de comparación
     */
    async function loadCompareVersion(panel) {
        const select = document.getElementById(`compareVersion${panel}`);
        const versionId = select.value;

        try {
            const response = await fetch(`/comun.php/bpmn/getVersionXml?version_id=${versionId}`);
            const data = await response.json();

            if (data.success) {
                if (panel === 1) {
                    if (!compareViewer1) {
                        compareViewer1 = new BpmnJS({
                            container: '#compareCanvas1'
                        });
                    }
                    await compareViewer1.importXML(data.bpmn_xml);
                    compareViewer1.get('canvas').zoom('fit-viewport');
                } else {
                    if (!compareViewer2) {
                        compareViewer2 = new BpmnJS({
                            container: '#compareCanvas2'
                        });
                    }
                    await compareViewer2.importXML(data.bpmn_xml);
                    compareViewer2.get('canvas').zoom('fit-viewport');
                }
            }
        } catch (error) {
            console.error('Error al cargar versión para comparar:', error);
        }
    }

    /**
     * Cerrar modal
     */
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    /**
     * Obtener iniciales de un nombre
     */
    function getInitials(name) {
        if (!name) return 'S';
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    /**
     * Formatear fecha
     */
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    /**
     * Formatear hora
     */
    function formatTime(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleTimeString('es-CO', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('active');
        }
    });
</script>