<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Date', 'Object', 'jQuery', 'UserComponent');
?>

<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-awesome/all.min.css">
<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bpmn-font/css/bpmn.css">

<div class="bpmn-viewer-page">
    <!-- Header -->
    <div class="viewer-header">
        <div class="header-content">
            <div class="process-info">
                <h1>
                    <i class="fas fa-project-diagram"></i>
                    <?php echo htmlspecialchars($process->getNombre()) ?>
                </h1>
                <p class="process-description">
                    <?php echo htmlspecialchars($process->getDescripcion() ?: 'Sin descripción') ?>
                </p>
            </div>
            <div class="header-actions">
                <div class="process-badges">
                    <span class="badge badge-info">Versión <?php echo $process->getVersion() ?></span>
                    <span class="badge badge-<?php echo $process->getIsActive() == '1' ? 'success' : 'secondary' ?>">
                        <?php echo ucfirst($process->getIsActive() == '1' ? 'Activo' : 'Inactivo') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats rápidas -->
        <?php if ($stats['total_instances'] > 0): ?>
            <div class="quick-stats">
                <div class="stat-item">
                    <i class="fas fa-play-circle"></i>
                    <span class="stat-value"><?php echo $stats['total_instances'] ?></span>
                    <span class="stat-label">Instancias</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-check-circle"></i>
                    <span class="stat-value"><?php echo $stats['completed'] ?></span>
                    <span class="stat-label">Completadas</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-hourglass-half"></i>
                    <span class="stat-value"><?php echo $stats['in_progress'] ?></span>
                    <span class="stat-label">En Progreso</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <span class="stat-value">
                        <?php echo $stats['last_execution'] ? format_date($stats['last_execution'], 'dd/MM/yyyy') : 'N/A' ?>
                    </span>
                    <span class="stat-label">Última Ejecución</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Toolbar -->
    <div class="viewer-toolbar">
        <div class="toolbar-left">
            <button id="zoomIn" class="btn btn-sm btn-light" title="Acercar">
                <i class="fas fa-search-plus"></i>
            </button>
            <button id="zoomOut" class="btn btn-sm btn-light" title="Alejar">
                <i class="fas fa-search-minus"></i>
            </button>
            <button id="zoomReset" class="btn btn-sm btn-light" title="Restablecer Zoom">
                <i class="fas fa-expand"></i>
            </button>
            <div class="toolbar-separator"></div>
            <button id="downloadSVG" class="btn btn-sm btn-light" title="Descargar como SVG">
                <i class="fas fa-download"></i> SVG
            </button>
            <button id="downloadPNG" class="btn btn-sm btn-light" title="Descargar como PNG">
                <i class="fas fa-download"></i> PNG
            </button>
        </div>
        <div class="toolbar-right">
            <span class="zoom-level">Zoom: <span id="zoomValue">100%</span></span>
        </div>
    </div>

    <!-- Canvas del diagrama -->
    <div class="bpmn-canvas-container">
        <div id="bpmn-canvas"></div>
    </div>
</div>

<!-- Include bpmn-js -->
<script src="https://unpkg.com/bpmn-js@17.11.1/dist/bpmn-viewer.development.js"></script>

<script>
    (function() {
        // Inicializar el viewer de BPMN
        const container = document.getElementById('bpmn-canvas');
        const viewer = new BpmnJS({
            container: container,
            keyboard: {
                bindTo: document
            }
        });

        // XML del diagrama BPMN
        const bpmnXML = <?php echo json_encode($bpmnXml) ?>;

        // Cargar el diagrama
        viewer.importXML(bpmnXML)
            .then(function(result) {
                const {
                    warnings
                } = result;

                if (warnings.length) {
                    console.warn('Advertencias al cargar el diagrama:', warnings);
                }

                // Ajustar zoom al contenido
                const canvas = viewer.get('canvas');
                document.getElementById('zoomValue').textContent = '100%';

                // Luego resetear scroll a posición inicial
                /*setTimeout(() => {
                    canvas.zoom('fit-viewport', 'auto');
                }, 100);*/

                // Actualizar valor de zoom
                updateZoomLevel();
            })
            .catch(function(err) {
                showError('Error al cargar el diagrama: ' + err.message);
            });

        // Funciones de zoom
        let currentZoom = 1;

        function updateZoomLevel() {
            const canvas = viewer.get('canvas');
            currentZoom = canvas.zoom();
            document.getElementById('zoomValue').textContent = Math.round(currentZoom * 100) + '%';
        }

        document.getElementById('zoomIn').addEventListener('click', function() {
            const canvas = viewer.get('canvas');
            canvas.zoom(canvas.zoom() + 0.1);
            updateZoomLevel();
        });

        document.getElementById('zoomOut').addEventListener('click', function() {
            const canvas = viewer.get('canvas');
            canvas.zoom(canvas.zoom() - 0.1);
            updateZoomLevel();
        });

        document.getElementById('zoomReset').addEventListener('click', function() {
            const canvas = viewer.get('canvas');
            canvas.zoom('fit-viewport');
            updateZoomLevel();
        });

        // Descargar como SVG
        document.getElementById('downloadSVG').addEventListener('click', function() {
            viewer.saveSVG()
                .then(function(result) {
                    const {
                        svg
                    } = result;
                    downloadFile(svg, '<?php echo $process->getNombre() ?>.svg', 'image/svg+xml');
                })
                .catch(function(err) {
                    showError('Error al generar SVG');
                });
        });

        // Descargar como PNG
        document.getElementById('downloadPNG').addEventListener('click', function() {
            viewer.saveSVG()
                .then(function(result) {
                    const {
                        svg
                    } = result;
                    svgToPng(svg, function(pngDataUrl) {
                        downloadFile(pngDataUrl, '<?php echo $process->getNombre() ?>.png', 'image/png');
                    });
                })
                .catch(function(err) {
                    showError('Error al generar PNG');
                });
        });

        // Función auxiliar para descargar archivos
        function downloadFile(content, filename, mimeType) {
            const blob = mimeType === 'image/png' ?
                dataURLtoBlob(content) :
                new Blob([content], {
                    type: mimeType
                });

            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        // Convertir data URL a Blob
        function dataURLtoBlob(dataURL) {
            const arr = dataURL.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], {
                type: mime
            });
        }

        // Convertir SVG a PNG
        function svgToPng(svgString, callback) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function() {
                canvas.width = img.width * 2; // Aumentar resolución
                canvas.height = img.height * 2;
                ctx.scale(2, 2);
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                callback(canvas.toDataURL('image/png'));
            };

            const blob = new Blob([svgString], {
                type: 'image/svg+xml'
            });
            const url = URL.createObjectURL(blob);
            img.src = url;
        }

        // Mostrar errores
        function showError(message) {
            alert(message);
        }

        // Atajos de teclado
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Plus: Zoom In
            if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '=')) {
                e.preventDefault();
                document.getElementById('zoomIn').click();
            }
            // Ctrl/Cmd + Minus: Zoom Out
            if ((e.ctrlKey || e.metaKey) && e.key === '-') {
                e.preventDefault();
                document.getElementById('zoomOut').click();
            }
            // Ctrl/Cmd + 0: Reset Zoom
            if ((e.ctrlKey || e.metaKey) && e.key === '0') {
                e.preventDefault();
                document.getElementById('zoomReset').click();
            }
        });
    })();
</script>

<style>
    .bpmn-viewer-page {
        padding: 20px;
        max-width: 100%;
        height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }

    .viewer-header {
        background: linear-gradient(135deg, #e3e3e3, #292c2f);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .process-info h1 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
    }

    .process-description {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
        color: #000;
    }

    .process-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .process-badges .badge {
        padding: 8px 16px;
        font-size: 0.9rem;
    }

    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .stat-item i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        opacity: 0.8;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .viewer-toolbar {
        background: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .toolbar-left {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .toolbar-separator {
        width: 1px;
        height: 24px;
        background: #dee2e6;
        margin: 0 8px;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .zoom-level {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
    }

    .bpmn-canvas-container {
        flex: 1;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }

    #bpmn-canvas {
        width: 100%;
        height: 100%;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    /* Estilos para el viewer BPMN */
    .djs-container {
        background: #f8f9fa;
    }

    .bjs-powered-by {
        display: none !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .bpmn-viewer-page {
            padding: 10px;
            height: calc(100vh - 80px);
        }

        .viewer-header {
            padding: 15px;
        }

        .header-content {
            flex-direction: column;
            gap: 15px;
        }

        .process-info h1 {
            font-size: 1.4rem;
        }

        .viewer-toolbar {
            flex-direction: column;
            gap: 10px;
        }

        .toolbar-left {
            width: 100%;
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .quick-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Loading state */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .loading-spinner {
        text-align: center;
    }

    .loading-spinner i {
        font-size: 3rem;
        color: #667eea;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>