/**
 * Filtro de Replace Menu para BPMN Designer
 * Ubicación: web/theme/neon/js/bpmn/lib/customReplaceMenuProvider.js
 * 
 * USO: Llamar applyBpmsReplaceFilter(modeler) DESPUÉS de crear el modeler
 */

(function () {
    'use strict';

    // ============================================
    // CONFIGURACIÓN: Elementos PERMITIDOS
    // ============================================
    // Solo estos aparecerán en el menú
    var ALLOWED_REPLACE_ENTRIES = [
        // Tareas permitidas
        'replace-with-user-task',
        'replace-with-service-task',
        'replace-with-send-task',
        'replace-with-manual-task',
        // Tareas genéricas
        'replace-with-task',
        // Mantener opciones de eventos
        'replace-with-none-start',
        'replace-with-none-end',
        'replace-with-message-start',
        'replace-with-message-end',
        'replace-with-timer-start',
        'replace-with-signal-start',
        'replace-with-conditional-start',
        'replace-with-error-end',
        'replace-with-escalation-end',
        'replace-with-terminate-end',
        'replace-with-signal-end',
        'replace-with-compensation-end',
        // Gateways
        'replace-with-exclusive-gateway',
        'replace-with-parallel-gateway',
        'replace-with-inclusive-gateway',
        'replace-with-event-based-gateway',
        'replace-with-complex-gateway',
        // Otros que quieras mantener
        'replace-with-collapsed-pool',
        'replace-with-expanded-pool'
    ];

    // ============================================
    // Entradas BLOQUEADAS específicamente
    // ============================================
    var BLOCKED_ENTRIES = [
        // Tareas no implementadas
        'replace-with-receive-task',
        'replace-with-business-rule-task',
        'replace-with-script-task',
        // Subprocesos y call activity
        'replace-with-call-activity',
        'replace-with-subprocess',
        'replace-with-collapsed-subprocess',
        'replace-with-expanded-subprocess',
        'replace-with-sub-process',
        'replace-with-collapsed-sub-process',
        'replace-with-expanded-sub-process',
        'replace-with-adhoc-subprocess',
        'replace-with-ad-hoc-subprocess',
        'replace-with-transaction',
        'replace-with-event-sub-process',
        'replace-with-event-subprocess'
    ];

    // ============================================
    // Función principal - llamar después de crear modeler
    // ============================================
    function applyBpmsReplaceFilter(modeler) {
        if (!modeler) {
            return;
        }

        var popupMenu = modeler.get('popupMenu');
        if (!popupMenu) {
            return;
        }

        // Guardar referencia al método original
        var originalOpen = popupMenu.open.bind(popupMenu);

        // Sobrescribir el método open
        popupMenu.open = function (element, type, position, options) {
            // Solo filtrar el menú de reemplazo
            if (type === 'bpmn-replace') {
                var self = this;
                var originalGetEntries = this._getEntries;

                this._getEntries = function (element, providers) {
                    var entries = originalGetEntries.call(self, element, providers);
                    var filtered = filterMenuEntries(entries);
                    return filtered;
                };

                var result = originalOpen(element, type, position, options);

                // Restaurar
                this._getEntries = originalGetEntries;

                return result;
            }

            return originalOpen(element, type, position, options);
        };
    }

    // ============================================
    // Filtrado de entradas
    // ============================================
    function filterMenuEntries(entries) {
        if (!entries || typeof entries !== 'object') {
            return entries;
        }

        var filtered = {};

        Object.keys(entries).forEach(function (key) {
            if (!isBlockedEntry(key, entries[key])) {
                filtered[key] = entries[key];
            }
        });

        return filtered;
    }

    function isBlockedEntry(key, entry) {
        var keyLower = key.toLowerCase();

        // 1. Verificar si está explícitamente bloqueado
        for (var i = 0; i < BLOCKED_ENTRIES.length; i++) {
            if (keyLower === BLOCKED_ENTRIES[i].toLowerCase()) {
                return true;
            }
        }

        // 2. Verificar por el tipo de target
        if (entry && entry.target && entry.target.type) {
            var targetType = entry.target.type.toLowerCase();

            // Bloquear receive task
            if (targetType.indexOf('receivetask') > -1 || targetType === 'bpmn:receivetask') {
                return true;
            }
            // Bloquear business rule task
            if (targetType.indexOf('businessruletask') > -1 || targetType === 'bpmn:businessruletask') {
                return true;
            }
            // Bloquear script task
            if (targetType.indexOf('scripttask') > -1 || targetType === 'bpmn:scripttask') {
                return true;
            }
            // Bloquear subprocesos
            if (targetType.indexOf('subprocess') > -1 || targetType.indexOf('sub-process') > -1) {
                return true;
            }
            // Bloquear call activity
            if (targetType.indexOf('callactivity') > -1 || targetType === 'bpmn:callactivity') {
                return true;
            }
            // Bloquear transaction
            if (targetType.indexOf('transaction') > -1) {
                return true;
            }
        }

        // 3. Verificar por palabras clave en el key
        if (keyLower.indexOf('receive') > -1 && keyLower.indexOf('task') > -1) {
            return true;
        }
        if (keyLower.indexOf('business') > -1 && keyLower.indexOf('rule') > -1) {
            return true;
        }
        if (keyLower.indexOf('script') > -1 && keyLower.indexOf('task') > -1) {
            return true;
        }
        if (keyLower.indexOf('subprocess') > -1 || keyLower.indexOf('sub-process') > -1) {
            return true;
        }
        if (keyLower.indexOf('call') > -1 && keyLower.indexOf('activity') > -1) {
            return true;
        }
        if (keyLower.indexOf('adhoc') > -1 || keyLower.indexOf('ad-hoc') > -1) {
            return true;
        }
        if (keyLower.indexOf('transaction') > -1) {
            return true;
        }

        // 4. Verificar por label/título si existe
        if (entry && entry.label) {
            var labelLower = entry.label.toLowerCase();
            if (labelLower.indexOf('receive') > -1 ||
                labelLower.indexOf('business rule') > -1 ||
                labelLower.indexOf('script task') > -1 ||
                labelLower.indexOf('sub-process') > -1 ||
                labelLower.indexOf('subprocess') > -1 ||
                labelLower.indexOf('call activity') > -1) {
                return true;
            }
        }

        return false;
    }

    // ============================================
    // Exportar función global
    // ============================================
    window.applyBpmsReplaceFilter = applyBpmsReplaceFilter;
    window.BPMN_BLOCKED_ENTRIES = BLOCKED_ENTRIES;
    window.BPMN_ALLOWED_ENTRIES = ALLOWED_REPLACE_ENTRIES;

})();