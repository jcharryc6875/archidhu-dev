<?php

/**
 * Solución para el problema de edición de procesos con instancias activas
 * 
 * ESTRATEGIA: Versionamiento de procesos
 * - Cuando se edita un proceso con instancias activas, se crea una NUEVA VERSIÓN
 * - Las instancias existentes siguen usando la versión anterior
 * - Las nuevas instancias usan la versión actualizada
 * 
 */

class BpmnProcessManager
{
    /**
     * Guarda un proceso BPMN con manejo inteligente de versiones
     * 
     * @param int|null $processId ID del proceso existente o null para nuevo
     * @param string $name Nombre del proceso
     * @param string $description Descripción
     * @param string $bpmnXml XML del BPMN
     * @param bool $forceNewVersion Forzar creación de nueva versión
     * @return array Resultado con proceso guardado
     */
    public static function saveProcess($processId, $name, $description, $bpmnXml, $usuario_id, $forceNewVersion = false)
    {
        $result = [
            'success' => false,
            'process' => null,
            'is_new_version' => false,
            'message' => ''
        ];

        try {
            // Si es un proceso nuevo
            if (!$processId) {
                $process = self::createNewProcess($name, $description, $bpmnXml, $usuario_id);
                $result['success'] = true;
                $result['process'] = $process;
                $result['message'] = 'Proceso creado correctamente';
                return $result;
            }

            // Obtener proceso existente
            $existingProcess = BpmnProcessPeer::retrieveByPK($processId);
            $bmpn_process_old = clone $existingProcess;

            if (!$existingProcess) {
                throw new Exception("Proceso no encontrado: $processId");
            }

            // Verificar si hay instancias activas
            $hasActiveInstances = self::hasActiveWorkflowInstances($processId);

            // Verificar si hay cambios estructurales en las tareas
            $hasStructuralChanges = self::hasStructuralTaskChanges($existingProcess, $bpmnXml);

            // Decidir estrategia de actualización
            if ($hasActiveInstances && ($hasStructuralChanges || $forceNewVersion)) {
                $historyCreated = self::createVersionHistory($existingProcess, $bpmnXml, $usuario_id);
                $result['version_history_created'] = ($historyCreated !== null);

                // Crear nueva versión del proceso
                $process = self::createNewVersion($existingProcess, $name, $description, $bpmnXml, $usuario_id);
                $result['is_new_version'] = true;
                $result['message'] = 'Se creó una nueva versión del proceso (v' . $process->getVersion() . '). Las instancias activas continúan con la versión anterior.';
            } else if ($hasActiveInstances && !$hasStructuralChanges) {
                $historyCreated = self::createVersionHistory($existingProcess, $bpmnXml, $usuario_id);
                $result['version_history_created'] = ($historyCreated !== null);

                // Actualización segura: solo cambios cosméticos
                $process = self::updateProcessSafely($existingProcess, $name, $description, $bpmnXml);
                $result['message'] = 'Proceso actualizado. Los cambios no afectan las instancias activas.';
            } else {
                $historyCreated = self::createVersionHistory($existingProcess, $bpmnXml, $usuario_id);
                $result['version_history_created'] = ($historyCreated !== null);

                // No hay instancias activas: actualización completa
                $process = self::updateProcessFully($existingProcess, $name, $description, $bpmnXml);
                $result['message'] = 'Proceso actualizado correctamente.';
            }
            //*******************************************************************************************
            if ($result['version_history_created']) {
                $result['message'] .= ' (Versión ' . $process->getVersion() . ')';
            }
            //*******************************************************************************************
            AuditLogPeer::guardarAuditoriaLite(
                BpmnProcessPeer::getOMClass(),
                $bmpn_process_old,
                $existingProcess,
                ModulesEnable::Seguridad,
                $existingProcess->getNombre(),
                $usuario_id,
                'update'
            );
            //*******************************************************************************************
            $result['success'] = true;
            $result['process'] = $process;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Verifica si hay instancias de workflow activas para este proceso
     */
    public static function hasActiveWorkflowInstances($processId)
    {
        $c = new Criteria();
        $c->add(WorkflowInstancePeer::BPMNPROCESS_ID, $processId);
        $c->add(WorkflowInstancePeer::WORKFLOWSTATUS_ID, [WorkflowInstanceStatusBpmn::running, WorkflowInstanceStatusBpmn::suspended], Criteria::IN);

        return WorkflowInstancePeer::doCount($c) > 0;
    }

    /**
     * Cuenta las instancias activas
     */
    public static function countActiveInstances($processId)
    {
        $c = new Criteria();
        $c->add(WorkflowInstancePeer::BPMNPROCESS_ID, $processId);
        $c->add(WorkflowInstancePeer::WORKFLOWSTATUS_ID, [WorkflowInstanceStatusBpmn::running, WorkflowInstanceStatusBpmn::suspended], Criteria::IN);

        return WorkflowInstancePeer::doCount($c);
    }

    /**
     * Verifica si hay cambios estructurales en las tareas
     * (tareas agregadas, eliminadas o con cambios en campos del formulario)
     */
    public static function hasStructuralTaskChanges($existingProcess, $newBpmnXml)
    {
        try {
            $oldTasks = self::extractTaskIds($existingProcess->getBpmnXml());
            $newTasks = self::extractTaskIds($newBpmnXml);

            // Verificar si hay tareas eliminadas o agregadas
            $removed = array_diff($oldTasks, $newTasks);
            $added = array_diff($newTasks, $oldTasks);

            if (!empty($removed) || !empty($added)) {
                return true;
            }

            // Verificar cambios en formFields de tareas existentes
            foreach ($newTasks as $taskId) {
                $oldConfig = self::getTaskConfig($existingProcess->getBpmnXml(), $taskId);
                $newConfig = self::getTaskConfig($newBpmnXml, $taskId);

                // Comparar formFields
                $oldFields = isset($oldConfig['formFields']) ? $oldConfig['formFields'] : [];
                $newFields = isset($newConfig['formFields']) ? $newConfig['formFields'] : [];

                if (self::hasFormFieldChanges($oldFields, $newFields)) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            // En caso de error, asumir que hay cambios estructurales
            return true;
        }
    }

    /**
     * Extrae los IDs de las tareas de usuario del XML BPMN
     */
    private static function extractTaskIds($bpmnXml)
    {
        $taskIds = [];

        if (empty($bpmnXml)) {
            return $taskIds;
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($bpmnXml);
            $xpath = new DOMXPath($dom);

            $tasks = $xpath->query('//*[local-name()="userTask"]');

            foreach ($tasks as $task) {
                // Verificar que sea DOMElement antes de usar getAttribute
                if (!($task instanceof DOMElement)) {
                    continue;
                }

                $id = $task->getAttribute('id');
                if ($id && !in_array($id, $taskIds)) {
                    $taskIds[] = $id;
                }
            }
        } catch (Exception $e) {
            // Error parseando XML
        }

        return $taskIds;
    }

    /**
     * Obtiene la configuración de una tarea desde el XML
     */
    private static function getTaskConfig($bpmnXml, $taskId)
    {
        $config = [];

        if (empty($bpmnXml)) {
            return $config;
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($bpmnXml);
            $xpath = new DOMXPath($dom);

            $tasks = $xpath->query("//*[@id='$taskId']");

            if ($tasks->length > 0) {
                $taskElement = $tasks->item(0);

                // Verificar que sea DOMElement
                if (!($taskElement instanceof DOMElement)) {
                    return $config;
                }

                // Buscar taskConfig en atributos
                foreach ($taskElement->attributes as $attr) {
                    if (strpos($attr->nodeName, 'taskConfig') !== false) {
                        $config = json_decode($attr->nodeValue, true) ?: [];
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            // Error parseando XML
        }

        return $config;
    }

    /**
     * Verifica si hay cambios significativos en los campos del formulario
     */
    private static function hasFormFieldChanges($oldFields, $newFields)
    {
        // Diferente cantidad de campos
        if (count($oldFields) !== count($newFields)) {
            return true;
        }

        // Crear mapas por ID
        $oldMap = [];
        foreach ($oldFields as $field) {
            $oldMap[$field['id']] = $field;
        }

        $newMap = [];
        foreach ($newFields as $field) {
            $newMap[$field['id']] = $field;
        }

        // Verificar campos eliminados o agregados
        if (array_keys($oldMap) !== array_keys($newMap)) {
            return true;
        }

        // Verificar cambios en tipo o required
        foreach ($newMap as $id => $newField) {
            if (!isset($oldMap[$id])) {
                return true;
            }

            $oldField = $oldMap[$id];

            // Cambio de tipo es estructural
            if (($oldField['type'] ?? '') !== ($newField['type'] ?? '')) {
                return true;
            }

            // Cambio de obligatorio a opcional o viceversa es estructural
            if (($oldField['required'] ?? false) !== ($newField['required'] ?? false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo proceso
     */
    private static function createNewProcess($name, $description, $bpmnXml, $usuario_id)
    {
        $process = new BpmnProcess();
        $process->setNombre($name);
        $process->setDescripcion($description);
        $process->setBpmnXml($bpmnXml);
        $process->setUsuarioId($usuario_id);
        $process->setIsActive(true);
        $process->setVersion(1);
        $process->setFechaCreacion(date('Y-m-d G:i:s'));
        $process->setFechaModificacion(date('Y-m-d G:i:s'));
        $process->save();
        //*******************************************************************************************
        // Crear definiciones de tareas
        self::parseAndCreateTaskDefinitions($process);
        //*******************************************************************************************
        AuditLogPeer::guardarAuditoriaLite(
            BpmnProcessPeer::getOMClass(),
            new BpmnProcess(),
            $process,
            ModulesEnable::Seguridad,
            $process->getNombre(),
            $usuario_id,
            'create'
        );
        //*******************************************************************************************
        return $process;
    }

    /**
     * Crea una nueva versión del proceso
     * El proceso anterior se mantiene para las instancias activas
     */
    private static function createNewVersion($existingProcess, $name, $description, $bpmnXml, $usuario_id)
    {
        // Desactivar la versión anterior
        $existingProcess->setIsActive(false);
        $existingProcess->setFechaModificacion(date('Y-m-d G:i:s'));
        $existingProcess->save();

        // Crear nueva versión
        $newProcess = new BpmnProcess();
        $newProcess->setUsuarioId($usuario_id);
        $newProcess->setNombre($name);
        $newProcess->setDescripcion($description);
        $newProcess->setBpmnXml($bpmnXml);
        $newProcess->setVersion($existingProcess->getVersion() + 1);
        $newProcess->setIsActive(true);
        $newProcess->setFechaCreacion(date('Y-m-d G:i:s'));
        $newProcess->setFechaModificacion(date('Y-m-d G:i:s'));

        // Mantener referencia al proceso original (si tienes este campo)
        // $newProcess->setParentProcessId($existingProcess->getPrimaryKey());

        $newProcess->save();

        // Crear nuevas definiciones de tareas para la nueva versión
        self::parseAndCreateTaskDefinitions($newProcess);

        return $newProcess;
    }

    /**
     * Actualización segura: solo cambios que no afectan estructura
     * (nombre, descripción, posiciones visuales, etc.)
     */
    private static function updateProcessSafely($process, $name, $description, $bpmnXml)
    {
        $process->setNombre($name);
        $process->setDescripcion($description);
        $process->setBpmnXml($bpmnXml);
        $process->setFechaModificacion(date('Y-m-d G:i:s'));
        $process->save();

        // Actualizar solo propiedades no estructurales de TaskDefinition
        self::updateTaskDefinitionsSafely($process);

        return $process;
    }

    /**
     * Actualización completa cuando no hay instancias activas
     */
    private static function updateProcessFully($process, $name, $description, $bpmnXml)
    {
        $process->setNombre($name);
        $process->setDescripcion($description);
        $process->setBpmnXml($bpmnXml);
        $process->setVersion($process->getVersion() + 1);
        $process->setFechaModificacion(date('Y-m-d G:i:s'));
        $process->save();

        // Recrear definiciones de tareas
        self::parseAndCreateTaskDefinitions($process);

        return $process;
    }

    /**
     * Actualiza TaskDefinitions de forma segura (sin eliminar)
     * Solo actualiza campos que no rompen instancias existentes
     */
    private static function updateTaskDefinitionsSafely($process)
    {
        $bpmnXml = $process->getBpmnXml();

        if (empty($bpmnXml)) {
            return;
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($bpmnXml);
            $xpath = new DOMXPath($dom);

            $tasks = $xpath->query('//*[local-name()="userTask"]');

            foreach ($tasks as $task) {
                // Verificar que sea DOMElement
                if (!($task instanceof DOMElement)) {
                    continue;
                }

                $taskId = $task->getAttribute('id');
                $taskName = $task->getAttribute('name') ?: $taskId;

                // Buscar TaskDefinition existente
                $taskDef = TaskDefinitionPeer::retrieveByProcessAndTaskId(
                    $process->getPrimaryKey(),
                    $taskId
                );

                if ($taskDef) {
                    // Actualizar solo campos seguros
                    $taskDef->setTaskName($taskName);

                    // Obtener taskConfig
                    $taskConfig = self::getTaskConfigFromElement($task);

                    // Actualizar duración (seguro)
                    if (isset($taskConfig['durationExpected'])) {
                        $taskDef->setDurationExpected((int)$taskConfig['durationExpected']);
                    }
                    if (isset($taskConfig['durationMax'])) {
                        $taskDef->setDurationMax((int)$taskConfig['durationMax']);
                    }

                    // Extraer assignee si está definido
                    $assignee = $task->getAttribute('assignee');
                    if (empty($assignee)) {
                        $assignee = $task->getAttributeNS('http://camunda.org/schema/1.0/bpmn', 'assignee');
                    }

                    if ($assignee) {
                        if (is_numeric($assignee)) {
                            $taskDef->setAssigneeType('user');
                            $taskDef->setAssigneeValue($assignee);
                        } elseif (strpos($assignee, '${') !== false) {
                            $taskDef->setAssigneeType('expression');
                            $taskDef->setAssigneeValue($assignee);
                        } else {
                            $taskDef->setAssigneeType('role');
                            $taskDef->setAssigneeValue($assignee);
                        }
                    }

                    // NO actualizar form_data para no romper instancias existentes
                    // $taskDef->setFormData(...); // ← No hacer esto
                    //***************************************************************************************
                    $taskDef->setFechaModificacion(date('Y-m-d G:i:s'));
                    $taskDef->save();
                }
            }
        } catch (Exception $e) {
            error_log('Error en updateTaskDefinitionsSafely: ' . $e->getMessage());
        }
    }

    /**
     * Parsea el BPMN y crea/actualiza TaskDefinitions
     * VERSIÓN MEJORADA que no elimina si hay dependencias
     */
    public static function parseAndCreateTaskDefinitions($process)
    {
        $bpmnXml = $process->getBpmnXml();

        if (empty($bpmnXml)) {
            return;
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($bpmnXml);
            $xpath = new DOMXPath($dom);

            // Obtener IDs de tareas actuales en el XML
            $currentTaskIds = [];
            $tasks = $xpath->query('//*[local-name()="userTask"]');

            foreach ($tasks as $task) {
                // Verificar que sea DOMElement
                if (!($task instanceof DOMElement)) {
                    continue;
                }
                $currentTaskIds[] = $task->getAttribute('id');
            }

            // Obtener TaskDefinitions existentes
            $c = new Criteria();
            $c->add(TaskDefinitionPeer::BPMNPROCESS_ID, $process->getPrimaryKey());
            $existingDefs = TaskDefinitionPeer::doSelect($c);

            // Mapear existentes por taskId
            $existingMap = [];
            foreach ($existingDefs as $def) {
                $existingMap[$def->getTaskId()] = $def;
            }

            // Procesar cada tarea del XML
            foreach ($tasks as $task) {
                // Verificar que sea DOMElement
                if (!($task instanceof DOMElement)) {
                    continue;
                }

                $taskId = $task->getAttribute('id');
                $taskName = $task->getAttribute('name') ?: $taskId;

                // Obtener configuración del elemento
                $taskConfig = self::getTaskConfigFromElement($task);

                if (isset($existingMap[$taskId])) {
                    // Actualizar existente
                    $taskDef = $existingMap[$taskId];
                    $taskDef->setFechaModificacion(date('Y-m-d G:i:s'));
                } else {
                    // Crear nuevo
                    $taskDef = new TaskDefinition();
                    $taskDef->setBpmnProcessId($process->getPrimaryKey());
                    $taskDef->setTaskId($taskId);
                    $taskDef->setTaskType('userTask');
                    $taskDef->setFechaModificacion(date('Y-m-d G:i:s'));
                    $taskDef->setFechaCreacion(date('Y-m-d G:i:s'));
                }

                // Extraer assignee si está definido
                $assignee = $task->getAttribute('assignee');
                if (empty($assignee)) {
                    $assignee = $task->getAttributeNS('http://camunda.org/schema/1.0/bpmn', 'assignee');
                }

                if ($assignee) {
                    if (is_numeric($assignee)) {
                        $taskDef->setAssigneeType('user');
                        $taskDef->setAssigneeValue($assignee);
                    } elseif (strpos($assignee, '${') !== false) {
                        $taskDef->setAssigneeType('expression');
                        $taskDef->setAssigneeValue($assignee);
                    } else {
                        $taskDef->setAssigneeType('role');
                        $taskDef->setAssigneeValue($assignee);
                    }
                }

                // Actualizar propiedades
                $taskDef->setTaskName($taskName);
                $taskDef->setDurationExpected(isset($taskConfig['durationExpected']) ? (int)$taskConfig['durationExpected'] : 60);
                $taskDef->setDurationMax(isset($taskConfig['durationMax']) ? (int)$taskConfig['durationMax'] : 120);

                if (isset($taskConfig['formFields'])) {
                    $taskDef->setFormData(json_encode($taskConfig['formFields']));
                }

                // Asegurar que esté activa
                if (method_exists($taskDef, 'setIsActive')) {
                    $taskDef->setIsActive(true);
                }

                $taskDef->save();

                // Quitar del mapa de existentes
                unset($existingMap[$taskId]);
            }

            // Manejar TaskDefinitions huérfanas (tareas eliminadas del XML)
            foreach ($existingMap as $taskId => $orphanDef) {
                // Verificar si tiene TaskInstances
                $hasInstances = self::taskDefinitionHasInstances($orphanDef->getPrimaryKey());

                if ($hasInstances) {
                    // Marcar como inactiva en lugar de eliminar
                    if (method_exists($orphanDef, 'setIsActive')) {
                        $orphanDef->setIsActive(false);
                        $orphanDef->save();
                    }
                } else {
                    // Seguro eliminar
                    $orphanDef->delete();
                }
            }
        } catch (Exception $e) {
            error_log('Error en parseAndCreateTaskDefinitions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica si una TaskDefinition tiene TaskInstances asociadas
     */
    private static function taskDefinitionHasInstances($taskDefinitionId)
    {
        $c = new Criteria();
        $c->add(TaskInstancePeer::TASKDEFINITION_ID, $taskDefinitionId);

        return TaskInstancePeer::doCount($c) > 0;
    }

    /**
     * Obtiene taskConfig directamente de un DOMElement
     * Evita re-parsear el XML cuando ya tenemos el elemento
     */
    private static function getTaskConfigFromElement(DOMElement $element)
    {
        $config = [];

        foreach ($element->attributes as $attr) {
            if (strpos($attr->nodeName, 'taskConfig') !== false) {
                $config = json_decode($attr->nodeValue, true) ?: [];
                break;
            }
        }

        return $config;
    }

    /**
     * Crea un registro en el historial de versiones
     * 
     * @param BpmProcess $process Proceso actual (antes de modificar)
     * @param string $newBpmnXml Nuevo XML que se va a guardar
     * @param int $userId ID del usuario que hace el cambio
     * @return BpmVersionHistory|null
     */
    private static function createVersionHistory($process, $newBpmnXml, $userId)
    {
        // Comparar XML actual con el nuevo
        $currentXml = $process->getBpmnXml();

        // Normalizar XMLs para comparación (quitar espacios y saltos de línea extra)
        $normalizedCurrent = self::normalizeXml($currentXml);
        $normalizedNew = self::normalizeXml($newBpmnXml);

        // Solo crear historial si hay cambios reales
        if ($normalizedCurrent === $normalizedNew) {
            return null; // No hay cambios, no crear historial
}

        // Calcular resumen de cambios
        $changesSummary = self::calculateChangesSummary($currentXml, $newBpmnXml);

        // Crear registro de historial con la versión ACTUAL (antes del cambio)
        $history = new BpmnVersionHistory();
        $history->setBpmnprocessId($process->getPrimaryKey());
        $history->setUsuarioId($userId);
        $history->setVersion($process->getVersion());
        $history->setBpmnXml($currentXml); // Guardar XML actual antes del cambio
        $history->setChangesSummary(json_encode($changesSummary));
        $history->setFechaCreacion(date('Y-m-d G:i:s'));
        $history->setFechaArchivado(date('Y-m-d G:i:s'));
        $history->save();

        return $history;
    }

    /**
     * Normaliza el XML para comparación
     */
    private static function normalizeXml($xml)
    {
        if (empty($xml)) {
            return '';
        }

        try {
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
            $dom->loadXML($xml);
            return $dom->saveXML();
        } catch (Exception $e) {
            // Si falla el parsing, comparar como string
            return preg_replace('/\s+/', ' ', trim($xml));
        }
    }

    /**
     * Calcula el resumen de cambios entre dos versiones XML
     */
    private static function calculateChangesSummary($oldXml, $newXml)
    {
        $changes = [
            'added' => [],
            'removed' => [],
            'modified' => []
        ];

        try {
            $oldElements = self::extractBpmnElementsForComparison($oldXml);
            $newElements = self::extractBpmnElementsForComparison($newXml);

            // Elementos añadidos
            foreach ($newElements as $id => $element) {
                if (!isset($oldElements[$id])) {
                    $changes['added'][] = $element['name'] ?: $element['type'];
                }
            }

            // Elementos eliminados
            foreach ($oldElements as $id => $element) {
                if (!isset($newElements[$id])) {
                    $changes['removed'][] = $element['name'] ?: $element['type'];
                }
            }

            // Elementos modificados (cambio de nombre)
            foreach ($newElements as $id => $element) {
                if (isset($oldElements[$id]) && $oldElements[$id]['name'] !== $element['name']) {
                    $changes['modified'][] = $element['name'] ?: $element['type'];
                }
            }
        } catch (Exception $e) {
            $changes['modified'][] = 'Cambios en el diagrama';
        }

        return $changes;
    }

    /**
     * Extrae elementos BPMN para comparación
     */
    private static function extractBpmnElementsForComparison($xml)
    {
        $elements = [];

        if (empty($xml)) {
            return $elements;
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($xml);

            $elementTypes = [
                'startEvent',
                'endEvent',
                'task',
                'userTask',
                'serviceTask',
                'scriptTask',
                'exclusiveGateway',
                'parallelGateway',
                'inclusiveGateway',
                'subProcess',
                'sequenceFlow'
            ];

            foreach ($elementTypes as $type) {
                $nodes = $dom->getElementsByTagName($type);
                foreach ($nodes as $node) {
                    if ($node instanceof DOMElement) {
                        $id = $node->getAttribute('id');
                        if ($id) {
                            $elements[$id] = [
                                'type' => $type,
                                'name' => $node->getAttribute('name') ?: $id
                            ];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Ignorar errores de parsing
        }

        return $elements;
    }
}
