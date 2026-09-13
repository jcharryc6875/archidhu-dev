<?php

/**
 * Motor principal de workflow para BPMS
 * Maneja la ejecución, simulación y control de procesos
 */
class WorkflowEngine
{
    private $logger;
    private $isSimulation = false;

    public function __construct($isSimulation = false)
    {
        $this->isSimulation = $isSimulation;
        $this->logger = sfContext::getInstance()->getLogger();
    }

    /**
     * Inicia una nueva instancia de proceso
     */
    public function startProcess($processId, $variables = [], $startedBy = null, $isSimulation = false)
    {
        try {
            $process = BpmnProcessPeer::retrieveByPK($processId);
            if (!$process || !$process->getIsActive()) {
                throw new Exception("Proceso no encontrado o inactivo");
            }

            // Crear instancia de workflow
            $instance = new WorkflowInstance();
            $instance->setBpmnProcessId($processId);
            $instance->setWorkflowstatusId(WorkflowInstanceStatusBpmn::running);
            $instance->setUsuarioId($startedBy);
            $instance->setNombre($process->getNombre() . ' - ' . date('Y-m-d G:i:s'));
            $instance->setVariables(json_encode($variables));
            $instance->setFechaInicio(date('Y-m-d G:i:s'));
            $instance->setFechaCreacion(date('Y-m-d G:i:s'));
            $instance->setFechaModificacion(date('Y-m-d G:i:s'));
            $instance->setIsSimulation($isSimulation);
            $instance->save();

            // Parsear BPMN y encontrar start event
            $bpmnXml = $process->getBpmnXml();
            $dom = new DOMDocument();
            $dom->loadXML($bpmnXml);

            $startEvent = $this->findStartEvent($dom);
            if (!$startEvent) {
                throw new Exception("No se encontró evento de inicio en el proceso");
            }

            // Registrar en historial
            $this->logHistory(
                $instance->getPrimaryKey(),
                $startEvent->getAttribute('id'),
                'Start Event',
                'startEvent',
                'started',
                $startedBy,
                0,
                [],
                $variables
            );

            // Ejecutar siguiente paso
            $this->executeNextStep($instance, $startedBy, $startEvent->getAttribute('id'), $dom);

            return $instance;
        } catch (Exception $e) {
            $this->logger->err('Error starting process: ' . $e->getMessage());
            throw $e;
        }
    }

    private function findNextPendingTask($workflowInstanceId)
    {
        $c = new Criteria();
        $c->add(TaskInstancePeer::WORKFLOWINSTANCE_ID, $workflowInstanceId);
        $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, [TaskInstanceStatusBpmn::assigned, TaskInstanceStatusBpmn::pending], Criteria::IN);
        $c->addDescendingOrderByColumn(TaskInstancePeer::FECHA_CREACION);
        $c->setLimit(1);

        return TaskInstancePeer::doSelectOne($c);
    }

    /**
     * Completa una tarea de usuario
     */
    public function completeTask($taskInstanceId, $userId, $formData = [], $comments = '')
    {
        try {
            $taskInstance = TaskInstancePeer::retrieveByPK($taskInstanceId);
            if (!$taskInstance) {
                throw new Exception("Tarea no encontrada: $taskInstanceId");
            }

            // Verificar que esté en estado válido
            if (!in_array($taskInstance->getTaskinstancestatusId(), [TaskInstanceStatusBpmn::pending, TaskInstanceStatusBpmn::assigned, TaskInstanceStatusBpmn::in_progress])) {
                throw new Exception("La tarea no está en un estado válido para completar");
            }

            // Verificar asignación
            if ($taskInstance->getUsuarioId() != $userId) {
                throw new Exception("Tarea no asignada a este usuario");
            }

            // Marcar tarea como completada
            $taskInstance->setTaskinstancestatusId(TaskInstanceStatusBpmn::completed);
            $taskInstance->setFechaCompletado(date('Y-m-d G:i:s'));
            $taskInstance->setFechaModificacion(date('Y-m-d G:i:s'));
            $taskInstance->setComentarios($comments);
            $taskInstance->setFormData($formData);
            $taskInstance->save();

            $workflowInstance = $taskInstance->getWorkflowInstance();

            if (!$workflowInstance) {
                throw new Exception("Workflow no encontrado para la tarea");
            }

            // Calcular tiempo de ejecución
            $startTime = $taskInstance->getFechaInicio() ? new DateTime($taskInstance->getFechaInicio()) : new DateTime($taskInstance->getFechaAsigna());
            $executionTime = time() - $startTime->format('U');


            // Fusionar variables del formulario con variables del workflow
            $workflowVars = json_decode($workflowInstance->getVariables(), true) ?: [];
            $updatedVars = array_merge($workflowVars, $formData);
            $workflowInstance->setVariables(json_encode($updatedVars));
            $workflowInstance->save();

            // Registrar en historial
            $this->logHistory(
                $workflowInstance->getPrimaryKey(),
                $taskInstance->getTaskId(),
                $taskInstance->getTaskName(),
                'userTask',
                'completed',
                $userId,
                $executionTime,
                $workflowVars,
                $updatedVars,
                $comments,
                $taskInstance->getPrimaryKey()
            );

            // Obtener proceso y DOM
            $process = $workflowInstance->getBpmnProcess();
            if (!$process) {
                throw new Exception("Proceso BPMN no encontrado");
            }

            $dom = new DOMDocument();
            $dom->loadXML($process->getBpmnXml());

            // Guardar el ID del workflow para buscar la siguiente tarea después
            $workflowId = $workflowInstance->getPrimaryKey();

            // Continuar con el siguiente paso
            $this->executeNextStep($workflowInstance, $userId, $taskInstance->getTaskId(), $dom);

            // Buscar si se creó una nueva tarea para el workflow
            $nextTask = $this->findNextPendingTask($workflowId);

            return $nextTask;
        } catch (Exception $e) {
            $this->logger->err('Error completing task: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ejecuta el siguiente paso en el workflow
     */
    public function executeNextStep(WorkflowInstance $workflowInstance, $userId, $currentNodeId, DOMDocument $dom)
    {
        $nextNodes = $this->findNextNodes($dom, $currentNodeId);

        foreach ($nextNodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }

            $nodeType = $node->nodeName;
            $nodeId = $node->getAttribute('id');
            $nodeName = $node->getAttribute('name') ?: $nodeId;

            // Actualizar nodo actual
            $workflowInstance->setCurrentNode($nodeId);
            $workflowInstance->save();

            switch ($nodeType) {
                case 'bpmn2:userTask':
                case 'userTask':
                    $this->handleUserTask($workflowInstance, $userId, $node);
                    break;
                case 'bpmn2:serviceTask':
                case 'serviceTask':
                    $this->handleServiceTask($workflowInstance, $userId, $node, $dom);
                    break;
                case 'bpmn2:exclusiveGateway':
                case 'exclusiveGateway':
                case 'bpmn2:inclusiveGateway':
                case 'inclusiveGateway':
                    $this->handleGateway($workflowInstance, $node, $userId, $dom);
                    break;
                case 'bpmn2:parallelGateway':
                case 'parallelGateway':
                    $this->handleParallelGateway($workflowInstance, $userId, $node, $dom);
                    break;
                case 'bpmn2:intermediateCatchEvent':
                case 'intermediateCatchEvent':
                    $this->handleTimerEvent($workflowInstance, $node, $userId);
                    break;
                case 'bpmn2:endEvent':
                case 'endEvent':
                    $this->handleEndEvent($workflowInstance, $node, $userId);
                    break;
                default:
                    // Para otros tipos de nodos, continuar automáticamente
                    $this->executeNextStep($workflowInstance, $userId, $nodeId, $dom);
                    break;
            }
        }
    }

    /**
     * Maneja parallel gateways (divergencia y convergencia)
     */
    private function handleParallelGateway(WorkflowInstance $workflowInstance, $userId, DOMElement $gatewayNode, DOMDocument $dom)
    {
        $gatewayId = $gatewayNode->getAttribute('id');
        $gatewayName = $gatewayNode->getAttribute('name') ?: $gatewayId;

        $outgoingFlows = $this->findOutgoingSequenceFlows($dom, $gatewayId);
        $incomingFlows = $this->findIncomingSequenceFlows($dom, $gatewayId);

        // Determinar si es divergencia (fork) o convergencia (join)
        $isDivergence = count($outgoingFlows) > 1;
        $isConvergence = count($incomingFlows) > 1;

        if ($isDivergence) {
            // Fork: Ejecutar todos los caminos paralelos
            $this->logHistory(
                $workflowInstance->getPrimaryKey(),
                $gatewayId,
                $gatewayName,
                'parallelGateway',
                'fork',
                $userId,
                0
            );

            foreach ($outgoingFlows as $flow) {
                $targetRef = $flow->getAttribute('targetRef');
                $targetNode = $this->findNodeById($dom, $targetRef);
                if ($targetNode) {
                    // Ejecutar cada rama en paralelo
                    $this->executeNextStep($workflowInstance, $userId, $gatewayId, $dom);
                }
            }
        } elseif ($isConvergence) {
            // Join: Esperar a que todas las ramas lleguen
            // Por simplicidad, continuar directamente
            // En una implementación completa, se debería verificar que todas las ramas han llegado

            $this->logHistory(
                $workflowInstance->getPrimaryKey(),
                $gatewayId,
                $gatewayName,
                'parallelGateway',
                'join',
                $userId,
                0
            );

            // Continuar con el siguiente nodo
            if (count($outgoingFlows) > 0) {
                $flow = $outgoingFlows[0];
                $targetRef = $flow->getAttribute('targetRef');
                $targetNode = $this->findNodeById($dom, $targetRef);
                if ($targetNode) {
                    $this->executeNextStep($workflowInstance, $userId, $gatewayId, $dom);
                }
            }
        } else {
            // Gateway simple, continuar
            $this->logHistory(
                $workflowInstance->getPrimaryKey(),
                $gatewayId,
                $gatewayName,
                'parallelGateway',
                'completed',
                $userId,
                0
            );
            $this->executeNextStep($workflowInstance, $userId, $gatewayId, $dom);
        }
    }

    /**
     * Encuentra sequence flows que llegan a un nodo
     */
    private function findIncomingSequenceFlows(DOMDocument $dom, $nodeId)
    {
        $incomingFlows = [];
        $sequenceFlows = $dom->getElementsByTagName('sequenceFlow');

        for ($i = 0; $i < $sequenceFlows->length; $i++) {
            $flow = $sequenceFlows->item($i);
            if ($flow instanceof DOMElement && $flow->getAttribute('targetRef') === $nodeId) {
                $incomingFlows[] = $flow;
            }
        }

        return $incomingFlows;
    }

    /**
     * Encuentra sequence flows que salen de un nodo
     */
    private function findOutgoingSequenceFlows(DOMDocument $dom, $nodeId)
    {
        $outgoingFlows = [];
        $sequenceFlows = $dom->getElementsByTagName('sequenceFlow');

        for ($i = 0; $i < $sequenceFlows->length; $i++) {
            $flow = $sequenceFlows->item($i);
            if ($flow instanceof DOMElement && $flow->getAttribute('sourceRef') === $nodeId) {
                $outgoingFlows[] = $flow;
            }
        }

        return $outgoingFlows;
    }

    /**
     * Maneja tareas de usuario
     */
    private function handleUserTask(WorkflowInstance $workflowInstance, $userId, DOMElement $taskNode)
    {
        $taskId = $taskNode->getAttribute('id');
        $taskName = $taskNode->getAttribute('name') ?: $taskId;

        // Buscar definición de tarea
        $taskDef = TaskDefinitionPeer::retrieveByProcessAndTaskId(
            $workflowInstance->getBpmnprocessId(),
            $taskId
        );

        // Crear instancia de tarea
        $taskInstance = new TaskInstance();
        $taskInstance->setWorkflowinstanceId($workflowInstance->getPrimaryKey());
        $taskInstance->setTaskdefinitionId($taskDef ? $taskDef->getPrimaryKey() : null);
        $taskInstance->setTaskinstancestatusId(TaskInstanceStatusBpmn::pending);
        $taskInstance->setTaskId($taskId);
        $taskInstance->setTaskName($taskName);
        $taskInstance->setPrioridad($workflowInstance->getPrioridad());

        // Asignar usuario si está definido
        if ($taskDef) {
            $assigneeId = $this->resolveAssignee($taskDef, $workflowInstance);
            if ($assigneeId) {
                $taskInstance->setUsuarioId($assigneeId);
                $taskInstance->setFechaAsigna(date('Y-m-d G:i:s'));
                $taskInstance->setTaskinstancestatusId(TaskInstanceStatusBpmn::assigned);
            }

            // Establecer fecha límite
            if ($taskDef->getDurationMax()) {
                $dueDate = new DateTime();
                $dueDate->add(new DateInterval('PT' . $taskDef->getDurationMax() . 'M'));
                $taskInstance->setFechaVencimiento($dueDate->format('Y-m-d G:i:s'));
            }
        }

        $taskInstance->setFechaInicio(date('Y-m-d G:i:s'));
        $taskInstance->setFechaCreacion(date('Y-m-d G:i:s'));
        $taskInstance->setFechaModificacion(date('Y-m-d G:i:s'));
        $taskInstance->save();

        // **ENVIAR EMAIL DE NOTIFICACIÓN**
        BpmnEmailService::sendTaskAssignedNotification($taskInstance);

        // Registrar en historial
        $this->logHistory(
            $workflowInstance->getPrimaryKey(),
            $taskId,
            $taskName,
            'userTask',
            'started',
            $userId,
            0
        );

        // En simulación, completar automáticamente
        if ($this->isSimulation) {
            $this->simulateTaskCompletion($taskInstance, $workflowInstance, $userId);
        }
    }

    /**
     * Simula la finalización de una tarea
     */
    private function simulateTaskCompletion(TaskInstance $taskInstance, WorkflowInstance $workflowInstance, $userId)
    {
        $taskDef = $taskInstance->getTaskDefinition();
        $expectedDuration = $taskDef ? $taskDef->getDurationExpected() : 60; // 60 min por defecto

        // Añadir variabilidad (+/- 25%)
        $variance = $expectedDuration * 0.25;
        $actualDuration = $expectedDuration + rand(-$variance, $variance);

        $taskInstance->setTaskinstancestatusId(TaskInstanceStatusBpmn::completed);
        $taskInstance->setFechaInicio(date('Y-m-d G:i:s'));

        $completedAt = new DateTime();
        $completedAt->add(new DateInterval('PT' . $actualDuration . 'M'));

        $taskInstance->setFechaVencimiento($completedAt->format('Y-m-d G:i:s'));
        $taskInstance->save();

        // Continuar flujo
        $process = $workflowInstance->getBpmnProcess();
        $dom = new DOMDocument();
        $dom->loadXML($process->getBpmnXml());
        $this->executeNextStep($workflowInstance, $userId, $taskInstance->getTaskId(), $dom);
    }

    /**
     * Maneja service tasks (tareas automáticas)
     */
    private function handleServiceTask(WorkflowInstance $workflowInstance, $userId, DOMElement $taskNode, DOMDocument $dom)
    {
        $taskId = $taskNode->getAttribute('id');
        $taskName = $taskNode->getAttribute('name') ?: $taskId;
        $implementation = $taskNode->getAttribute('implementation') ?: 'default';

        $startTime = microtime(true);
        $success = true;
        $errorMsg = '';

        try {
            // Ejecutar implementación específica
            switch ($implementation) {
                case 'email':
                    $this->sendEmail($workflowInstance, $taskNode);
                    break;
                case 'webhook':
                    $this->callWebhook($workflowInstance, $taskNode);
                    break;
                default:
                    // Implementación por defecto
                    sleep(1); // Simular procesamiento
                    break;
            }
        } catch (Exception $e) {
            $success = false;
            $errorMsg = $e->getMessage();
            $this->logger->err("Service task error: $errorMsg");
        }

        $executionTime = (microtime(true) - $startTime) * 1000; // en milisegundos

        // Registrar en historial
        $this->logHistory(
            $workflowInstance->getPrimaryKey(),
            $taskId,
            $taskName,
            'serviceTask',
            $success ? 'completed' : 'failed',
            $userId,
            $executionTime,
            [],
            [],
            $errorMsg
        );

        if ($success) {
            $this->executeNextStep($workflowInstance, $userId, $taskId, $dom);
        } else {
            // Manejar error según configuración
            $workflowInstance->setWorkflowstatusId(WorkflowInstanceStatusBpmn::failed);
            $workflowInstance->setFechaModificacion(date('Y-m-d G:i:s'));
            $workflowInstance->save();
        }
    }

    /**
     * Maneja gateways exclusivos e inclusivos
     */
    private function handleGateway(WorkflowInstance $workflowInstance, DOMElement $gatewayNode, $userId, DOMDocument $dom)
    {
        $gatewayId = $gatewayNode->getAttribute('id');
        $gatewayName = $gatewayNode->getAttribute('name') ?: $gatewayId;
        $gatewayType = $gatewayNode->nodeName;

        $variables = json_decode($workflowInstance->getVariables(), true) ?: [];
        $outgoingFlows = $this->findOutgoingSequenceFlows($dom, $gatewayId);
        $activeFlows = [];

        foreach ($outgoingFlows as $flow) {
            $flowId = $flow->getAttribute('id');
            $condition = $flow->getAttribute('conditionExpression');

            // Verificar si el flujo está habilitado
            $flowConfig = WorkflowConfigurationPeer::retrieveByProcessAndFlowId(
                $workflowInstance->getBpmnprocessId(),
                $flowId
            );

            if ($flowConfig && !$flowConfig->getIsActive()) {
                continue; // Flujo deshabilitado
            }

            // Evaluar condición
            if ($this->evaluateCondition($condition, $variables)) {
                $activeFlows[] = $flow;

                // Para gateway exclusivo, tomar solo el primero
                if ($gatewayType === 'exclusiveGateway') {
                    break;
                }
            }
        }

        // Registrar en historial
        $this->logHistory(
            $workflowInstance->getPrimaryKey(),
            $gatewayId,
            $gatewayName,
            $gatewayType,
            'completed',
            $userId,
            0,
            $variables,
            $variables
        );

        // Continuar por los flujos activos
        foreach ($activeFlows as $flow) {
            $targetRef = $flow->getAttribute('targetRef');
            $targetNode = $this->findNodeById($dom, $targetRef);
            if ($targetNode) {
                $this->executeNextStep($workflowInstance, $userId, $gatewayId, $dom);
            }
        }
    }

    /**
     * Maneja eventos de tiempo
     */
    private function handleTimerEvent(WorkflowInstance $workflowInstance, DOMElement $eventNode, $userId)
    {
        $eventId = $eventNode->getAttribute('id');
        $eventName = $eventNode->getAttribute('name') ?: $eventId;

        // Buscar definición de timer
        $timerDef = $eventNode->getElementsByTagName('timerEventDefinition')->item(0);
        if (!$timerDef) {
            return; // No es un timer event
        }

        $timerExpression = '';
        $scheduledAt = new DateTime();

        // Determinar tipo de timer
        $timeDuration = $timerDef->getElementsByTagName('timeDuration')->item(0);
        $timeDate = $timerDef->getElementsByTagName('timeDate')->item(0);
        $timeCycle = $timerDef->getElementsByTagName('timeCycle')->item(0);

        if ($timeDuration) {
            $duration = $timeDuration->textContent; // ej: PT15M
            $timerExpression = $duration;
            $scheduledAt->add(new DateInterval($duration));
            $timerType = 'duration';
        } elseif ($timeDate) {
            $date = $timeDate->textContent; // ej: 2024-12-31T23:59:59
            $timerExpression = $date;
            $scheduledAt = new DateTime($date);
            $timerType = 'date';
        } elseif ($timeCycle) {
            $cycle = $timeCycle->textContent; // ej: R5/PT10M
            $timerExpression = $cycle;
            // Implementar lógica para ciclos
            $timerType = 'cycle';
        }

        // Crear timer event
        $timerEvent = new TimerEvent();
        $timerEvent->setWorkflowinstanceId($workflowInstance->getPrimaryKey());
        $timerEvent->setNodeId($eventId);
        $timerEvent->setTimerType($timerType);
        $timerEvent->setTimerExpression($timerExpression);
        $timerEvent->setFechaScheduled($scheduledAt);
        $timerEvent->save();

        // En simulación, ejecutar inmediatamente
        if ($this->isSimulation) {
            $this->executeTimerEvent($timerEvent, $userId);
        }

        // Registrar en historial
        $this->logHistory(
            $workflowInstance->getPrimaryKey(),
            $eventId,
            $eventName,
            'intermediateCatchEvent',
            'scheduled',
            $userId,
            0
        );
    }

    /**
     * Ejecuta un timer event cuando llega su hora programada
     */
    public function executeTimerEvent(TimerEvent $timerEvent, $userId)
    {
        try {
            $workflowInstance = $timerEvent->getWorkflowInstance();

            if (!$workflowInstance || $workflowInstance->getWorkflowstatusId() !== WorkflowInstanceStatusBpmn::running) {
                throw new Exception('Workflow no está en estado running');
            }

            $process = $workflowInstance->getBpmnProcess();
            $dom = new DOMDocument();
            $dom->loadXML($process->getBpmnXml());

            // Registrar en historial
            $history = new WorkflowHistory();
            $history->setWorkflowinstanceId($workflowInstance->getPrimaryKey());
            $history->setUsuarioId($userId);
            $history->setNodeId($timerEvent->getNodeId());
            $history->setNodeName('Timer Event');
            $history->setNodeType('intermediateCatchEvent');
            $history->setAction('completed');
            $history->setTiempoEjecucion(0);
            $history->save();

            // Continuar con el siguiente nodo
            $this->executeNextStep($workflowInstance, $userId, $timerEvent->getNodeId(), $dom);

            // Marcar timer como ejecutado
            $timerEvent->setTimerStatus(WorkflowInstanceStatusBpmn::executed);
            $timerEvent->setFechaExecuted(date('Y-m-d G:i:s'));
            $timerEvent->save();

            return true;
        } catch (Exception $e) {
            $this->logger->err('Error executing timer event: ' . $e->getMessage());

            $timerEvent->setTimerStatus(WorkflowInstanceStatusBpmn::failed);
            $timerEvent->save();

            throw $e;
        }
    }

    /**
     * Maneja eventos finales
     */
    private function handleEndEvent(WorkflowInstance $workflowInstance, DOMElement $endNode, $userId)
    {
        $eventId = $endNode->getAttribute('id');
        $eventName = $endNode->getAttribute('name') ?: $eventId;

        // Completar workflow
        $workflowInstance->setWorkflowstatusId(WorkflowInstanceStatusBpmn::completed);
        $workflowInstance->setFechaCompletado(date('Y-m-d G:i:s'));
        $workflowInstance->setFechaModificacion(date('Y-m-d G:i:s'));
        //$workflowInstance->setUsuarioId($userId);
        $workflowInstance->save();

        // Registrar en historial
        $this->logHistory(
            $workflowInstance->getPrimaryKey(),
            $eventId,
            $eventName,
            'endEvent',
            'completed',
            $userId,
            0
        );

        // Calcular métricas
        $this->updateProcessMetrics($workflowInstance);
    }

    /**
     * Encuentra el evento de inicio
     */
    private function findStartEvent($dom)
    {
        $startEvents = $dom->getElementsByTagName('startEvent');
        return $startEvents->length > 0 ? $startEvents->item(0) : null;
    }

    /**
     * Encuentra los siguientes nodos
     */
    private function findNextNodes(DOMDocument $dom, $sourceId)
    {
        $nextNodes = [];
        $sequenceFlows = $dom->getElementsByTagName('sequenceFlow');

        // Intentar sin namespace
        for ($i = 0; $i < $sequenceFlows->length; $i++) {
            $flow = $sequenceFlows->item($i);
            if ($flow instanceof DOMElement && $flow->getAttribute('sourceRef') === $sourceId) {
                $targetId = $flow->getAttribute('targetRef');
                $targetNode = $this->findNodeById($dom, $targetId);
                if ($targetNode) {
                    $nextNodes[] = $targetNode;
                }
            }
        }

        // Si no encontró nada, intentar con namespace bpmn2
        if (empty($nextNodes)) {
            $sequenceFlows = $dom->getElementsByTagName('bpmn2:sequenceFlow');
            for ($i = 0; $i < $sequenceFlows->length; $i++) {
                $flow = $sequenceFlows->item($i);
                if ($flow instanceof DOMElement && $flow->getAttribute('sourceRef') === $sourceId) {
                    $targetId = $flow->getAttribute('targetRef');
                    $targetNode = $this->findNodeById($dom, $targetId);
                    if ($targetNode) {
                        $nextNodes[] = $targetNode;
                    }
                }
            }
        }

        return $nextNodes;
    }

    /**
     * Encuentra un nodo por ID
     */
    private function findNodeById(DOMDocument $dom, $nodeId)
    {
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//*[@id='$nodeId']");
        return $nodes->length > 0 ? $nodes->item(0) : null;
    }

    /**
     * Resuelve el asignado de una tarea
     */
    private function resolveAssignee(TaskDefinition $taskDef, WorkflowInstance $workflowInstance)
    {
        $assigneeType = $taskDef->getAssigneeType();
        $assigneeValue = $taskDef->getAssigneeValue();

        switch ($assigneeType) {
            case 'user':
                return (int) $assigneeValue;

            case 'expression':
                $variables = json_decode($workflowInstance->getVariables(), true) ?: [];
                return $this->evaluateExpression($assigneeValue, $variables);

            case 'group':
            case 'role':
                // Implementar lógica para grupos/roles
                return $this->findUserInGroup($assigneeValue);

            default:
                return null;
        }
    }

    /**
     * Evalúa una condición
     */
    private function evaluateCondition($condition, $variables)
    {
        if (empty($condition)) {
            return true; // Sin condición = siempre true
        }

        // Evaluación simple de expresiones
        // En producción usar un evaluador más robusto
        try {
            $code = preg_replace_callback('/\$(\w+)/', function ($matches) use ($variables) {
                $varName = $matches[1];
                return isset($variables[$varName]) ? var_export($variables[$varName], true) : 'null';
            }, $condition);

            return eval("return $code;");
        } catch (Exception $e) {
            $this->logger->err("Error evaluating condition: $condition - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra en el historial
     */
    private function logHistory(
        $workflowInstanceId,
        $nodeId,
        $nodeName,
        $nodeType,
        $action,
        $userId = null,
        $executionTime = 0,
        $variablesBefore = [],
        $variablesAfter = [],
        $comments = ''
    ) {
        $history = new WorkflowHistory();
        $history->setWorkflowInstanceId($workflowInstanceId);
        $history->setNodeId($nodeId);
        $history->setNodeName($nodeName);
        $history->setNodeType($nodeType);
        $history->setAction($action);
        $history->setUsuarioId($userId);
        $history->setTiempoEjecucion($executionTime);
        $history->setVariablesBefore(json_encode($variablesBefore));
        $history->setVariablesAfter(json_encode($variablesAfter));
        $history->setComentarios($comments);
        $history->setFechaCreacion(date('Y-m-d G:i:s'));
        $history->save();
    }

    /**
     * Actualiza métricas del proceso
     */
    private function updateProcessMetrics(WorkflowInstance $workflowInstance)
    {
        $processId = $workflowInstance->getBpmnprocessId();
        $today = date('Y-m-d');

        $metrics = bpmnProcessMetricsPeer::retrieveByProcessAndDate($processId, $today);
        if (!$metrics) {
            $metrics = new bpmnProcessMetrics();
            $metrics->setBpmnprocessId($processId);
            $metrics->setMetricDate($today);
            $metrics->setFechaCreacion(date('Y-m-d G:i:s'));
        }

        // Calcular duración
        $startTime = new DateTime($workflowInstance->getFechaInicio());
        $endTime = new DateTime($workflowInstance->getFechaCompletado());
        $duration = ($endTime->format('U') - $startTime->format('U')) / 3600; // en horas

        $metrics->setInstancesCompleted($metrics->getInstancesCompleted() + 1);
        $metrics->setFechaModificacion(date('Y-m-d G:i:s'));
        // Actualizar promedios
        $this->updateAverageDuration($metrics, $duration);

        $metrics->save();
    }

    private function updateAverageDuration(bpmnProcessMetrics $metrics, $newDuration)
    {
        $currentAvg = $metrics->getAvgDuration() ?: 0;
        $completed = $metrics->getInstancesCompleted();

        if ($completed <= 1) {
            $metrics->setAvgDuration($newDuration);
            $metrics->setMinDuration($newDuration);
            $metrics->setMaxDuration($newDuration);
        } else {
            $newAvg = (($currentAvg * ($completed - 1)) + $newDuration) / $completed;
            $metrics->setAvgDuration($newAvg);

            if ($newDuration < $metrics->getMinDuration()) {
                $metrics->setMinDuration($newDuration);
            }
            if ($newDuration > $metrics->getMaxDuration()) {
                $metrics->setMaxDuration($newDuration);
            }
        }
    }
}
