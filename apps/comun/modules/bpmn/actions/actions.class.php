<?php

/**
 * bpmn actions.
 *
 * @package    simad
 * @subpackage bpmn
 * @author     Javier Fernando Charry - javier.charry@aureasas.com
 * @version    SVN: $Id: actions.class.php 3335 2025-07-01 16:19:56Z fabien $
 */

class bpmnActions extends sfActions
{
    /**
     * bpmnActions::preExecute.
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
     */
    public function preExecute(): void
    {
        $isAuthenticated = $this->getUser()->isAuthenticated();
        $base_path = sfConfig::get('base_simad');
        if (!$isAuthenticated) {
            $this->redirect($base_path . "/backend.php/security/login");
        }
    }

    /**
     * bpmnActions::verificaPrilegio.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
     */
    public function verificaPrilegio($currentForm): void
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $this->redirect(sfConfig::get('base_simad') . '/no_autorizado.html');
        }
    }

    /**
     * bpmnActions::tienePrivilegio.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return bool true|false si tiene el permiso para ejecutar la accion actual
     */
    public function tienePrivilegio($currentForm): bool
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $isValid = true;
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * bpmnActions::verificaPrilegioCerrar.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
     */
    public function verificaPrilegioCerrar($currentForm): void
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $this->redirect(sfConfig::get('base_simad') . '/no_autorizado_cerrar.html');
        }
    }

    /**
     * Muestra la lista de tareas con opción de aprobación masiva
     */
    public function executeTaskList(sfWebRequest $request)
    {
        // La vista carga las tareas por AJAX
    }

    /**
     * API: Obtiene las tareas pendientes del usuario
     */
    public function executeTasksBulk(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            $scope = $request->getParameter('scope', 'my'); // my, all, team
            $status = $request->getParameter('status', 'pending');

            $c = new Criteria();

            // Filtrar por usuario si es "my"
            if ($scope === 'my') {
                $c->add(TaskInstancePeer::USUARIO_ID, $usuariologuiado);
            }

            // Filtrar por estado
            if ($status === 'pending') {
                $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, [TaskInstanceStatusBpmn::pending, TaskInstanceStatusBpmn::assigned], Criteria::IN);
            } else {
                if ($status == 'pending') {
                    $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::pending);
                } elseif ($status == 'assigned') {
                    $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::assigned);
                } elseif ($status == 'in_progress') {
                    $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::in_progress);
                } elseif ($status == 'completed') {
                    $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::completed);
                }
            }

            // Ordenar por prioridad y fecha
            $c->addDescendingOrderByColumn(TaskInstancePeer::PRIORIDAD);
            $c->addAscendingOrderByColumn(TaskInstancePeer::FECHA_VENCIMIENTO);
            $tasks = TaskInstancePeer::doSelect($c);

            $result = [];
            foreach ($tasks as $task) {
                $workflowInstance = $task->getWorkflowInstance();
                $process = $workflowInstance ? $workflowInstance->getBpmnProcess() : null;
                $taskDefinition = $task->getTaskDefinition();

                // Verificar si tiene formulario requerido
                $hasRequiredForm = false;
                if ($taskDefinition) {
                    $formData = $taskDefinition->getFormData();
                    if ($formData) {
                        $formFields = json_decode($formData, true);
                        if (is_array($formFields)) {
                            foreach ($formFields as $field) {
                                if (empty($field['required'])) {
                                    $hasRequiredForm = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                $result[] = [
                    'id' => $task->getPrimaryKey(),
                    'has_required_form' => $hasRequiredForm,
                    'task_name' => $task->getTaskName() ?: ($taskDefinition ? $taskDefinition->getTaskName() : 'Tarea'),
                    'task_id' => $task->getTaskId(),
                    'workflow_instance_id' => $task->getWorkflowinstanceId(),
                    'workflow_instance_name' => $task->getWorkflowInstance()->getNombre(),
                    'process_id' => $workflowInstance ? $workflowInstance->getBpmnprocessId() : null,
                    'process_name' => $process ? $process->getNombre() : 'Sin proceso',
                    'priority' => $task->getPrioridad() ?: 5,
                    'status' => $task->getTaskInstanceStatus()->getDescripcion(),
                    'due_date' => $task->getFechaVencimiento('Y-m-d G:i:s'),
                    'created_at' => $task->getFechaCreacion('Y-m-d G:i:s'),
                    'assignee_id' => $task->getUsuarioId(),
                    'has_required_form' => $hasRequiredForm,
                    'form_data' => $taskDefinition ? $taskDefinition->getFormData() : null
                ];
            }

            return $this->renderText(json_encode([
                'success' => true,
                'tasks' => $result,
                'total' => count($result)
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * Muestra la vista de historial de versiones
     */
    public function executeVersionHistory(sfWebRequest $request)
    {
        $this->processId = $request->getParameter('process_id');

        if (!$this->processId) {
            $this->redirect('bpmn/index');
        }
    }

    /**
     * API: Obtiene el historial de versiones de un proceso
     */
    // Versión optimizada con caché de usuarios
    public function executeGetVersionHistory(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        //**************************************************************************************
        try {
            $processId = $request->getParameter('process_id');
            //**********************************************************************************
            if (!$processId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de proceso requerido'
                ]));
            }
            //**********************************************************************************
            $c = new Criteria();
            $c->add(BpmnVersionHistoryPeer::BPMNPROCESS_ID, $processId);
            $c->addDescendingOrderByColumn(BpmnVersionHistoryPeer::VERSION);
            $versions = BpmnVersionHistoryPeer::doSelect($c);
            //**********************************************************************************
            // Obtener IDs de usuarios únicos
            $userIds = [];
            foreach ($versions as $v) {
                if ($v->getUsuarioId()) {
                    $userIds[$v->getUsuarioId()] = true;
                }
            }
            //**********************************************************************************
            // Cargar usuarios en una sola consulta
            $usuarios = [];
            if (!empty($userIds)) {
                $cUsers = new Criteria();
                $cUsers->add(UsuarioPeer::USUARIO_ID, array_keys($userIds), Criteria::IN);
                $usuariosList = UsuarioPeer::doSelect($cUsers);

                foreach ($usuariosList as $u) {
                    $usuarios[$u->getUsuarioId()] = trim($u->getNombre() . ' ' . $u->getApellido());
                }
            }
            //**********************************************************************************
            // Formatear resultado
            $result = [];
            foreach ($versions as $v) {
                $usuarioNombre = 'Sistema';
                if ($v->getUsuarioId() && isset($usuarios[$v->getUsuarioId()])) {
                    $usuarioNombre = $usuarios[$v->getUsuarioId()];
                }

                $result[] = [
                    'id' => $v->getBpmnversionhistoryId(),
                    'process_id' => $v->getBpmnprocessId(),
                    'usuario_id' => $v->getUsuarioId(),
                    'version' => $v->getVersion(),
                    'changes_summary' => $v->getChangesSummary(),
                    'fecha_creacion' => $v->getFechaCreacion('Y-m-d H:i:s'),
                    'fecha_archivado' => $v->getFechaArchivado() ? $v->getFechaArchivado('Y-m-d H:i:s') : null,
                    'usuario_nombre' => $usuarioNombre
                ];
            }
            //**********************************************************************************
            return $this->renderText(json_encode([
                'success' => true,
                'versions' => $result,
                'total' => count($result)
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Obtiene el XML de una versión específica
     */
    public function executeGetVersionXml(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $versionId = $request->getParameter('version_id');

            if (!$versionId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de versión requerido'
                ]));
            }

            $version = BpmnVersionHistoryPeer::retrieveByPK($versionId);

            if (!$version) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Versión no encontrada'
                ]));
            }

            return $this->renderText(json_encode([
                'success' => true,
                'version_id' => $version->getBpmnversionhistoryId(),
                'process_id' => $version->getBpmnprocessId(),
                'version' => $version->getVersion(),
                'bpmn_xml' => $version->getBpmnXml(),
                'changes_summary' => $version->getChangesSummary(),
                'fecha_creacion' => $version->getFechaCreacion('Y-m-d H:i:s')
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Restaura una versión anterior
     */
    public function executeRestoreVersion(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            $versionId = $request->getParameter('version_id');
            $processId = $request->getParameter('process_id');

            if (!$versionId || !$processId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de versión y proceso requeridos'
                ]));
            }

            // Obtener la versión a restaurar
            $versionToRestore = BpmnVersionHistoryPeer::retrieveByPK($versionId);

            if (!$versionToRestore) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Versión no encontrada'
                ]));
            }

            // Obtener el proceso
            $process = BpmnProcessPeer::retrieveByPK($processId);

            if (!$process) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Proceso no encontrado'
                ]));
            }

            // Guardar versión actual antes de restaurar
            $currentVersion = $process->getVersion();
            $newVersion = $currentVersion + 1;

            // Crear registro de la versión actual (antes de restaurar)
            $historyBackup = new BpmnVersionHistory();
            $historyBackup->setBpmnprocessId($processId);
            $historyBackup->setUsuarioId($usuariologuiado);
            $historyBackup->setVersion($currentVersion);
            $historyBackup->setBpmnXml($process->getBpmnXml());
            $historyBackup->setChangesSummary(json_encode([
                'added' => [],
                'removed' => [],
                'modified' => ['Respaldo antes de restaurar v' . $versionToRestore->getVersion()]
            ]));
            $historyBackup->setFechaCreacion(date('Y-m-d G:i:s'));
            $historyBackup->setFechaArchivado(date('Y-m-d G:i:s')); // Marcar como archivada
            $historyBackup->save();

            // Actualizar proceso con el XML de la versión restaurada
            $process->setBpmnXml($versionToRestore->getBpmnXml());
            $process->setVersion($newVersion);
            $process->setFechaModificacion(date('Y-m-d G:i:s'));
            $process->save();

            // Crear nuevo registro de versión (la restaurada)
            $historyNew = new BpmnVersionHistory();
            $historyNew->setBpmnprocessId($processId);
            $historyNew->setUsuarioId($usuariologuiado);
            $historyNew->setVersion($newVersion);
            $historyNew->setBpmnXml($versionToRestore->getBpmnXml());
            $historyNew->setChangesSummary(json_encode([
                'added' => [],
                'removed' => [],
                'modified' => ['Restaurado desde versión ' . $versionToRestore->getVersion()]
            ]));
            $historyNew->setFechaCreacion(date('Y-m-d G:i:s'));
            $historyNew->save();

            return $this->renderText(json_encode([
                'success' => true,
                'message' => 'Versión restaurada correctamente',
                'new_version' => $newVersion,
                'restored_from' => $versionToRestore->getVersion()
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Compara dos versiones (opcional, para futuro)
     */
    public function executeCompareVersions(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            $version1Id = $request->getParameter('version1_id');
            $version2Id = $request->getParameter('version2_id');

            if (!$version1Id || !$version2Id) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Se requieren dos versiones para comparar'
                ]));
            }

            $version1 = BpmnVersionHistoryPeer::retrieveByPK($version1Id);
            $version2 = BpmnVersionHistoryPeer::retrieveByPK($version2Id);

            if (!$version1 || !$version2) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Una o ambas versiones no encontradas'
                ]));
            }

            return $this->renderText(json_encode([
                'success' => true,
                'version1' => [
                    'id' => $version1->getBpmnversionhistoryId(),
                    'version' => $version1->getVersion(),
                    'bpmn_xml' => $version1->getBpmnXml(),
                    'fecha_creacion' => $version1->getFechaCreacion('Y-m-d H:i:s')
                ],
                'version2' => [
                    'id' => $version2->getBpmnversionhistoryId(),
                    'version' => $version2->getVersion(),
                    'bpmn_xml' => $version2->getBpmnXml(),
                    'fecha_creacion' => $version2->getFechaCreacion('Y-m-d H:i:s')
                ]
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * GET /api/processes - Lista procesos
     * POST /api/processes - Crear proceso
     */
    public function executeProcesses(sfWebRequest $request)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            if ($request->getMethod() == 'POST') {
                $processesData = BpmnProcessPeer::createProcess($request, $usuariologuiado);
                return $this->renderJSON($processesData);
            } else {
                $processesData = BpmnProcessPeer::listProcesses();
                return $this->renderJSON(['processes' => $processesData]);
            }
        } catch (PropelException $e) {
            return $this->renderError($e->getMessage());
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * PUT /api/processes/:id - Actualizar proceso
     * DELETE /api/processes/:id - Eliminar proceso
     * GET /api/processes/:id - Obtener proceso específico
     */
    public function executeProcess(sfWebRequest $request)
    {
        $processId = $request->getParameter('process') ? $request->getParameter('process') : null;
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            if ($request->getMethod() == 'POST') {
                $processesData = BpmnProcessPeer::updateProcess($request, $processId, $usuariologuiado);
                return $this->renderJSON($processesData);
            } elseif ($request->getMethod() == 'DELETE') {
                $processesData = BpmnProcessPeer::deleteProcess($processId);
                return $this->renderJSON($processesData);
            } else {
                $bpmn_process = BpmnProcessPeer::getProcess($processId);
                if ($bpmn_process == 404) {
                    return $this->renderError('Proceso no encontrado', 404);
                }
                return $this->renderJSON($bpmn_process);
            }
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * POST /api/processes/:id/deploy - Desplegar proceso
     */
    public function executeProcessDeploy(sfWebRequest $request)
    {
        $processId = $request->getParameter('id');
        $variables = json_decode($request->getParameter('variables', '{}'), true);
        $isSimulation = $request->getParameter('simulation', false);
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            $engine = new WorkflowEngine($isSimulation);
            $instance = $engine->startProcess(
                $processId,
                $variables,
                $usuariologuiado,
                $isSimulation
            );

            return $this->renderJSON([
                'success' => true,
                'workflow_id' => $instance->getPrimaryKey(),
                'message' => 'Proceso iniciado correctamente'
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/workflows - Lista instancias de workflow
     * POST /api/workflows/:id/suspend - Suspender workflow
     * POST /api/workflows/:id/resume - Reanudar workflow
     * POST /api/workflows/:id/terminate - Terminar workflow
     */
    public function executeWorkflows(sfWebRequest $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                $action = $request->getPostParameter('action') ? $request->getPostParameter('action') : null;
                $workflowId = $request->getPostParameter('id') ? $request->getPostParameter('id') : null;
                $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

                if (!$workflowId) {
                    return $this->renderText(json_encode([
                        'success' => false,
                        'error' => 'ID de workflow requerido'
                    ]));
                }

                if (!$action) {
                    return $this->renderText(json_encode([
                        'success' => false,
                        'error' => 'Acción requerida'
                    ]));
                }

                switch ($action) {
                    case 'suspend':
                        return $this->renderJSON(WorkflowInstancePeer::suspendWorkflow($workflowId, $usuariologuiado));
                    case 'resume':
                        return $this->renderJSON(WorkflowInstancePeer::resumeWorkflow($workflowId, $usuariologuiado));
                    case 'cancel':
                    case 'terminate':
                        return $this->renderJSON(WorkflowInstancePeer::terminateWorkflow($workflowId, $usuariologuiado));
                    default:
                        return $this->renderText(json_encode([
                            'success' => false,
                            'error' => 'Acción no válida: ' . $action
                        ]));
                }
            } else {
                return $this->renderJSON(WorkflowInstancePeer::listWorkflows($request));
            }
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * Muestra las tareas del usuario actual
     * GET /bpmn/myTasks
     */
    public function executeMyTasks(sfWebRequest $request)
    {
        // Esta acción solo renderiza el template
        // Los datos se cargan vía AJAX
    }

    /**
     * GET /api/tasks/my - Obtiene las tareas asignadas al usuario actual
     */
    public function executeTasksMy(sfWebRequest $request)
    {
        $this->setLayout(false);
        $this->getResponse()->setContentType('application/json');

        try {
            $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $status = $request->getParameter('status', 'all'); // pending, all, completed

            $c = new Criteria();
            $c->add(TaskInstancePeer::USUARIO_ID, $usuariologuiado);

            if ($status !== 'all') {
                if ($status === 'pending') {
                    $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, [TaskInstanceStatusBpmn::pending, TaskInstanceStatusBpmn::assigned, TaskInstanceStatusBpmn::in_progress], Criteria::IN);
                } else {
                    if ($status == 'pending') {
                        $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::pending);
                    } elseif ($status == 'assigned') {
                        $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::assigned);
                    } elseif ($status == 'in_progress') {
                        $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::in_progress);
                    } elseif ($status == 'completed') {
                        $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::completed);
                    }
                }
            }

            $c->addDescendingOrderByColumn(TaskInstancePeer::FECHA_CREACION);
            $c->setLimit(50);

            $tasks = TaskInstancePeer::doSelect($c);

            $result = [];
            foreach ($tasks as $task) {
                $workflowInstance = $task->getWorkflowInstance();
                $process = $workflowInstance ? $workflowInstance->getBpmnProcess() : null;

                // Obtener definición de tarea para el formulario
                $taskDef = TaskDefinitionPeer::retrieveByPK($task->getTaskdefinitionId());

                $result[] = [
                    'id' => $task->getPrimaryKey(),
                    'task_id' => $task->getTaskId(),
                    'task_name' => $task->getTaskName(),
                    'status' => $task->getTaskInstanceStatus()->getDescripcion(),
                    'priority' => $task->getPrioridad(),
                    'due_date' => $task->getFechaVencimiento('c'),
                    'created_at' => $task->getFechaCreacion('c'),
                    'assigned_at' => $task->getFechaAsigna('c'),
                    'workflow_instance' => [
                        'id' => $workflowInstance ? $workflowInstance->getPrimaryKey() : null,
                        'name' => $workflowInstance ? $workflowInstance->getNombre() : 'N/A',
                        'process_name' => $process ? $process->getNombre() : 'N/A',
                        'status' => $workflowInstance ? $workflowInstance->getWorkflowStatus()->getDescripcion() : 'N/A'
                    ],
                    'has_form' => $taskDef && $taskDef->getFormData() ? true : false
                ];
            }

            return $this->renderText(json_encode([
                'success' => true,
                'tasks' => $result,
                'total' => count($result)
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * GET /api/tasks - Lista tareas pendientes
     * GET /api/tasks/my - Mis tareas asignadas
     */
    public function executeTasks(sfWebRequest $request)
    {
        try {
            $scope = $request->getParameter('scope', 'all');
            $userId = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $status = $request->getParameter('status', 'assigned');
            $limit = $request->getParameter('limit', 50);
            $offset = $request->getParameter('offset', 0);

            $c = new Criteria();

            if ($scope === 'my') {
                $c->add(TaskInstancePeer::USUARIO_ID, $userId);
            }

            if ($status !== 'all') {
                $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, TaskInstanceStatusBpmn::assigned);
            }

            $c->addDescendingOrderByColumn(TaskInstancePeer::FECHA_CREACION);
            $c->setLimit($limit);
            $c->setOffset($offset);

            $tasks = TaskInstancePeer::doSelect($c);
            $tasksData = [];

            foreach ($tasks as $task) {
                $tasksData[] = [
                    'id' => $task->getPrimaryKey(),
                    'task_id' => $task->getTaskId(),
                    'task_name' => $task->getTaskName(),
                    'status' => $task->getTaskInstanceStatus()->getDescripcion(),
                    'priority' => $task->getPrioridad(),
                    'assigned_user' => $task->getUsuarioId(),
                    'assigned_user_name' => $task->getUsuarioId() ?
                        $task->getUsuario()->getNombreApellido() : null,
                    'due_date' => $task->getFechaVencimiento('c'),
                    'workflow_instance' => [
                        'id' => $task->getWorkflowInstance()->getPrimaryKey(),
                        'name' => $task->getWorkflowInstance()->getNombre(),
                        'process_name' => $task->getWorkflowInstance()->getBpmnProcess()->getNombre()
                    ],
                    'form_data' => json_decode($task->getFormData(), true),
                    'created_at' => $task->getFechaCreacion('c')
                ];
            }

            return $this->renderJSON([
                'tasks' => $tasksData,
                'total' => TaskInstancePeer::doCount($c)
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * POST /api/tasks/:id/complete - Completar tarea
     */
    public function executeTaskComplete(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        if ($request->getMethod() !== 'POST') {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Método no permitido'
            ]));
        }

        try {
            $taskId = $request->getParameter('task_id');
            $formData = json_decode($request->getParameter('fields', '{}'), true);
            $variables = json_decode($request->getParameter('variables', '{}'), true);
            $comments = $request->getParameter('comments', '');
            $userId = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

            // Obtener la tarea
            $task = TaskInstancePeer::retrieveByPK($taskId);

            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            // Verificar que la tarea esté asignada al usuario
            if ($task->getUsuarioId() != $userId) {
                throw new Exception('No tienes permiso para completar esta tarea');
            }

            // Verificar que la tarea esté pendiente
            if (!in_array($task->getTaskinstancestatusId(), [TaskInstanceStatusBpmn::assigned])) {
                throw new Exception('Esta tarea ya fue completada o no está disponible');
            }

            // Validar campos requeridos
            TaskDefinitionPeer::validateRequiredFields($task, $formData);

            // Procesar archivos subidos si existen
            $attachmentIds = [];
            if ($request->hasFiles()) {
                $attachmentIds = TaskAttachmentPeer::processTaskAttachments($taskId, $request, $userId);
                if (!empty($attachmentIds)) {
                    // Agregar IDs de archivos a las variables
                    $variables['attachments'] = $attachmentIds;
                }
            }

            // Combinar form_data y variables
            $allData = array_merge($formData, $variables);

            // Guardar datos del formulario
            /*$task->setTaskinstancestatusId(TaskInstanceStatusBpmn::completed);
            $task->setFormData(json_encode($allData));
            $task->setComentarios($comments);
            $task->setFechaCompletado(date('Y-m-d G:i:s'));
            $task->setFechaModificacion(date('Y-m-d G:i:s'));
            $task->save();*/

            // Actualizar variables del workflow
            TaskDefinitionPeer::updateWorkflowVariables($task->getWorkflowInstance(), $allData);

            $engine = new WorkflowEngine();
            $nextTask = $engine->completeTask($taskId, $userId, $allData, $comments);

            // Verificar si hay siguiente tarea para el usuario
            $hasNextTask = false;
            if ($nextTask != null) {
                $hasNextTask = ($nextTask->getUsuarioId() == $userId);
            }

            // Agregar info de siguiente tarea si existe y es del mismo usuario
            if ($nextTask) {
                $response['has_next_task'] = $hasNextTask;
                $response['next_task'] = [
                    'id' => $nextTask->getTaskinstanceId(),
                    'task_name' => $nextTask->getTaskName(),
                    'assigned_to_me' => ($nextTask->getUsuarioId() == $userId)
                ];
            } else {
                $response['has_next_task'] = $hasNextTask;
            }

            $response['success'] = true;
            $response['message'] = 'Tarea completada correctamente';
            $response['uploaded_files'] = count($attachmentIds);
            $response['attachments'] = $attachmentIds;

            return $this->renderJSON($response);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * API: Completar/Aprobar una tarea
     * Soporta tanto aprobación simple como con formulario
     */
    public function executeTasksComplete(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $taskId = $request->getParameter('task_id');
            $action = $request->getParameter('action', 'approve'); // approve, complete
            $formData = $request->getParameter('form_data');
            $variables = $request->getParameter('variables');
            $comments = $request->getParameter('comments', '');
            $userId = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
            exit;
            if (!$taskId) {
                throw new Exception('ID de tarea requerido');
            }

            // Obtener la tarea
            $task = TaskInstancePeer::retrieveByPK($taskId);
            $workflowInstance = $task->getWorkflowInstance();

            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            // Verificar estado
            if (!in_array($task->getTaskinstancestatusId(), [TaskInstanceStatusBpmn::assigned, TaskInstanceStatusBpmn::in_progress, TaskInstanceStatusBpmn::pending])) {
                throw new Exception('La tarea no está en un estado válido para completar');
            }

            // Verificar permisos
            if ($task->getUsuarioId() && $task->getUsuarioId() != $userId) {
                // Verificar si el usuario tiene rol de supervisor o admin
                if (!$this->canUserCompleteTask($userId, $task)) {
                    throw new Exception('No tienes permiso para completar esta tarea');
                }
            }

            // Procesar datos del formulario si vienen
            if ($formData) {
                $formDataArray = is_string($formData) ? json_decode($formData, true) : $formData;
                $task->setFormData(json_encode($formDataArray));
            }

            // Procesar variables adicionales
            if ($variables) {
                $variablesArray = is_string($variables) ? json_decode($variables, true) : $variables;
                $task->setFormData(json_encode($variablesArray));
                //*************************************************************************************
                $workflowInstance->setVariables(json_encode($variablesArray));
                $workflowInstance->save();

                //$this->updateWorkflowVariables($task->getWorkflowinstanceId(), $variablesArray);
            }

            // Completar la tarea
            $task->setTaskinstancestatusId(TaskInstanceStatusBpmn::completed);
            $task->setFechaCompletado(date('Y-m-d G:i:s'));
            $task->setFechaModificacion(date('Y-m-d G:i:s'));
            //$task->setCompletedBy($userId);

            if (!empty($comments)) {
                $task->setComments($comments);
            }

            $task->save();

            // Registrar en log
            TaskInstancePeer::logTaskAction($task, 'completed', $userId, $comments);

            // Procesar archivos adjuntos si hay
            $uploadedFiles = [];
            if ($request->hasFile('attachments')) {
                $uploadedFiles = TaskAttachmentPeer::processTaskAttachments($taskId, $request, $userId);
            }

            // Avanzar el workflow
            $nextTask = TaskInstancePeer::advanceWorkflowAndGetNext($task, $userId);

            // Preparar respuesta
            $response = [
                'success' => true,
                'message' => 'Tarea completada correctamente',
                'task_id' => $task->getTaskinstanceId(),
                'attachments' => $uploadedFiles
            ];

            // Agregar info de siguiente tarea si existe y es del mismo usuario
            if ($nextTask) {
                $response['has_next_task'] = true;
                $response['next_task'] = [
                    'id' => $nextTask->getTaskinstanceId(),
                    'task_name' => $nextTask->getTaskName(),
                    'assigned_to_me' => ($nextTask->getAssigneeId() == $userId)
                ];
            } else {
                $response['has_next_task'] = false;
            }

            return $this->renderText(json_encode($response));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Aprobación masiva de tareas
     */
    public function executeBulkApprove(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $taskIdsJson = $request->getParameter('task_ids');
            $comment = $request->getParameter('comment', '');
            $skipWithForms = $request->getParameter('skip_with_forms', '1') === '1';

            if (!$taskIdsJson) {
                throw new Exception('No se especificaron tareas');
            }

            $taskIds = json_decode($taskIdsJson, true);

            if (!is_array($taskIds) || count($taskIds) === 0) {
                throw new Exception('Lista de tareas inválida');
            }

            // Limitar cantidad de tareas por lote
            $maxTasks = 50;
            if (count($taskIds) > $maxTasks) {
                throw new Exception("Máximo $maxTasks tareas por lote");
            }

            $userId = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
            $approved = 0;
            $failed = 0;
            $errors = [];

            // Procesar cada tarea
            foreach ($taskIds as $taskId) {
                try {
                    $result = TaskInstancePeer::processTaskApproval($taskId, $userId, $comment, $skipWithForms);

                    if ($result['success']) {
                        $approved++;
                    } else {
                        $failed++;
                        $errors[] = [
                            'task_id' => $taskId,
                            'message' => $result['error']
                        ];
                    }
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = [
                        'task_id' => $taskId,
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $this->renderText(json_encode([
                'success' => $failed === 0,
                'approved' => $approved,
                'failed' => $failed,
                'errors' => $errors,
                'message' => "$approved tareas aprobadas" . ($failed > 0 ? ", $failed fallidas" : "")
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'approved' => 0,
                'failed' => 0,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Rechazo masivo de tareas
     */
    public function executeBulkReject(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $taskIdsJson = $request->getParameter('task_ids');
            $reason = $request->getParameter('reason', '');

            if (!$taskIdsJson) {
                throw new Exception('No se especificaron tareas');
            }

            if (empty(trim($reason))) {
                throw new Exception('El motivo del rechazo es requerido');
            }

            $taskIds = json_decode($taskIdsJson, true);

            if (!is_array($taskIds) || count($taskIds) === 0) {
                throw new Exception('Lista de tareas inválida');
            }

            // Limitar cantidad de tareas por lote
            $maxTasks = 50;
            if (count($taskIds) > $maxTasks) {
                throw new Exception("Máximo $maxTasks tareas por lote");
            }

            $userId = $this->getUser()->getGuardUser()->getId();
            $rejected = 0;
            $failed = 0;
            $errors = [];

            // Procesar cada tarea
            foreach ($taskIds as $taskId) {
                try {
                    $result = TaskInstancePeer::processTaskRejection($taskId, $userId, $reason);

                    if ($result['success']) {
                        $rejected++;
                    } else {
                        $failed++;
                        $errors[] = [
                            'task_id' => $taskId,
                            'message' => $result['error']
                        ];
                    }
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = [
                        'task_id' => $taskId,
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $this->renderText(json_encode([
                'success' => $failed === 0,
                'rejected' => $rejected,
                'failed' => $failed,
                'errors' => $errors,
                'message' => "$rejected tareas rechazadas" . ($failed > 0 ? ", $failed fallidas" : "")
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'rejected' => 0,
                'failed' => 0,
                'error' => $e->getMessage()
            ]));
        }
    }

    public function executeProcessList(sfWebRequest $request) {}

    /**
     * bpmnActions::executeIndex.     
     * @return void retorna la vista asociada a este actions
     */
    public function executeIndex()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        //$taskInstance = TaskInstancePeer::retrieveByPK(86);
        //BpmnEmailService::sendTaskAssignedNotification($taskInstance);
        //$this->forward('bpmn','editor');
    }

    /**
     * bpmnActions::executeDesigner.     
     * @return void retorna la vista asociada a este actions
     */
    public function executeDesigner(sfWebRequest $request)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $this->verificaPrilegio("workflow/create");
        //***********************************************************************************************
        $processId = $request->getParameter('process', null);
        $duplicateId = $request->getParameter('duplicate'); // Funciona para GET y POST
        //***********************************************************************************************
        $this->process = null;
        $this->isDuplicate = false;
        $this->taskDefinitions = [];
        $this->processVariables = [];
        //***********************************************************************************************
        // Modo edición
        if ($processId) {
            $this->processId = $processId;
        } // Modo duplicación (POST)
        elseif ($duplicateId && $request->isMethod('POST')) {
            $this->isDuplicate = true;
            $this->taskDefinitions = [];
            $this->processVariables = [];
            $this->processId = $duplicateId;
        }
        //***********************************************************************************************
        $this->getResponse()->setContentType('text/html');
    }

    /**
     * Ver métricas detalladas de un workflow completado
     */
    public function executeViewWorkflowMetrics(sfWebRequest $request)
    {
        $workflowInstanceId = $request->getParameter('workflowinstance_id');

        $this->workflow = WorkflowInstancePeer::retrieveByPK($workflowInstanceId);
        $this->forward404Unless($this->workflow);

        $this->process = $this->workflow->getBpmnProcess();

        // Calcular métricas generales
        $this->metrics = WorkflowInstancePeer::calculateWorkflowMetrics($this->workflow);

        // Obtener todas las tareas con sus métricas
        $c = new Criteria();
        $c->add(TaskInstancePeer::WORKFLOWINSTANCE_ID, $workflowInstanceId);
        $c->addAscendingOrderByColumn(TaskInstancePeer::FECHA_CREACION);
        $tasks = TaskInstancePeer::doSelect($c);

        $this->tasksWithMetrics = [];
        foreach ($tasks as $task) {
            $this->tasksWithMetrics[] = [
                'task' => $task,
                'metrics' => WorkflowInstancePeer::calculateTaskMetrics($task)
            ];
        }

        // Calcular métricas de tiempo por fase/etapa si las tienes
        $this->phaseMetrics = WorkflowInstancePeer::calculatePhaseMetrics($this->workflow);

        // Timeline de ejecución
        $this->timeline = WorkflowInstancePeer::buildWorkflowTimeline($this->workflow);

        //$this->setLayout(false);
    }

    /**
     * bpmnActions::executeDashboard.     
     * @return void retorna la vista asociada a este actions
     */
    public function executeDashboard(sfWebRequest $request)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $tab = $request->getParameter('tab', 'active'); // active, completed
        //***********************************************************************************************
        // Métricas globales
        $this->globalMetrics = MetricsCalculator::getGlobalMetrics();
        //***********************************************************************************************
        if ($tab === 'completed') {
            // Procesos completados
            $c = new Criteria();
            //*******************************************************************************************
            if (!$this->tienePrivilegio("workflow/listall")) {
                $c->add(WorkflowInstancePeer::USUARIO_ID, $usuariologuiado);
            }
            //*******************************************************************************************
            $c->add(WorkflowInstancePeer::WORKFLOWSTATUS_ID, [WorkflowInstanceStatusBpmn::completed, WorkflowInstanceStatusBpmn::terminated, WorkflowInstanceStatusBpmn::failed], Criteria::IN);
            $c->add(WorkflowInstancePeer::FECHA_COMPLETADO, null, Criteria::ISNOTNULL);
            $c->addDescendingOrderByColumn(WorkflowInstancePeer::FECHA_COMPLETADO);

            // Paginación
            $page = $request->getParameter('page', 1);
            $pager = new sfPropelPager('WorkflowInstance', 15);
            $pager->setCriteria($c);
            $pager->setPage($page);
            $pager->init();

            $this->pager = $pager;
            $completedWorkflows = $pager->getResults();

            // Calcular métricas para cada workflow
            $workflowsWithMetrics = [];
            foreach ($completedWorkflows as $workflow) {
                $workflowsWithMetrics[] = [
                    'workflow' => $workflow,
                    'metrics' => WorkflowInstancePeer::calculateWorkflowMetrics($workflow)
                ];
            }

            // Estadísticas generales de completados
            $this->completedStats = WorkflowInstancePeer::calculateCompletedStats($completedWorkflows);
            $this->completedWorkflows = $workflowsWithMetrics;
        } else {
            // Lista de procesos activos
            $c = new Criteria();
            $c->add(BpmnProcessPeer::IS_ACTIVE, true);
            $c->addAscendingOrderByColumn(BpmnProcessPeer::NOMBRE);
            $this->processes = BpmnProcessPeer::doSelect($c);

            // Tareas pendientes del usuario actual
            $c = new Criteria();
            $c->add(TaskInstancePeer::USUARIO_ID, $usuariologuiado);
            $c->add(TaskInstancePeer::TASKINSTANCESTATUS_ID, [TaskInstanceStatusBpmn::pending, TaskInstanceStatusBpmn::assigned], Criteria::IN);
            $c->addAscendingOrderByColumn(TaskInstancePeer::FECHA_VENCIMIENTO);
            $this->myPendingTasks = TaskInstancePeer::doSelect($c);

            // Workflows activos recientes
            $c = new Criteria();
            $c->add(WorkflowInstancePeer::WORKFLOWSTATUS_ID, WorkflowInstanceStatusBpmn::running);
            $c->add(WorkflowInstancePeer::IS_SIMULATION, false);
            $c->addDescendingOrderByColumn(WorkflowInstancePeer::FECHA_CREACION);
            $c->setLimit(10);
            $this->activeWorkflows = WorkflowInstancePeer::doSelect($c);
        }

        $this->currentTab = $tab;
    }

    public function executeViewProcessDiagram(sfWebRequest $request)
    {
        $processId = $request->getParameter('processId');

        $this->process = BpmnProcessPeer::retrieveByPK($processId);

        if (!$this->process) {
            $this->getUser()->setFlash('error', 'Proceso no encontrado');
            $this->redirect('dashboard/index');
        }

        // Obtener el XML del diagrama BPMN
        $this->bpmnXml = $this->process->getBpmnXml();

        // Si no hay XML, usar un diagrama vacío básico
        if (empty($this->bpmnXml)) {
            $this->bpmnXml = BpmnProcessPeer::getEmptyBpmnDiagram();
        }

        // Obtener estadísticas básicas del proceso
        $this->stats = MetricsCalculator::getProcessStats($processId);
    }

    public function executeProcessMetrics(sfWebRequest $request)
    {
        $processId = $request->getParameter('processId');

        $this->process = BpmnProcessPeer::retrieveByPK($processId);

        if (!$this->process) {
            $this->getUser()->setFlash('error', 'Proceso no encontrado');
            $this->redirect('dashboard/index');
        }

        // Métricas en tiempo real
        $this->realMetrics = MetricsCalculator::calculateProcessMetrics($processId);

        // Obtener métricas de SIMULACIONES (is_simulation = 1)
        $this->simulationMetrics = MetricsCalculator::calculateProcessMetrics($processId, true);

        // Obtener datos para gráficos temporales
        $this->realTimelineData = MetricsCalculator::getTimelineData($processId);

        // Obtener datos para gráficos temporales - SIMULACIONES
        $this->simulationTimelineData = MetricsCalculator::getTimelineData($processId, true);

        // Obtener tareas más frecuentes
        $this->realTaskStats = MetricsCalculator::getTaskStatistics($processId);
        $this->simulationTaskStats = MetricsCalculator::getTaskStatistics($processId, true);

        // Obtener usuarios más activos
        $this->topUsers = MetricsCalculator::getTopUsers($processId);
        $this->simulationTopUsers = MetricsCalculator::getTopUsers($processId, true);

        // Obtener lista de simulaciones guardadas
        $this->simulations = MetricsCalculator::getSimulationsList($processId);

        // Obtener instancias recientes REALES
        $c = new Criteria();
        $c->add(WorkflowInstancePeer::BPMNPROCESS_ID, $processId);
        $c->add(WorkflowInstancePeer::IS_SIMULATION, false);
        $c->addDescendingOrderByColumn(WorkflowInstancePeer::FECHA_INICIO);
        $c->setLimit(10);
        $this->recentInstances = WorkflowInstancePeer::doSelect($c);

        // Obtener instancias recientes de SIMULACIONES
        $c = new Criteria();
        $c->add(WorkflowInstancePeer::BPMNPROCESS_ID, $processId);
        $c->add(WorkflowInstancePeer::IS_SIMULATION, true);
        $c->addDescendingOrderByColumn(WorkflowInstancePeer::FECHA_INICIO);
        $c->setLimit(10);
        $this->recentSimulationInstances = WorkflowInstancePeer::doSelect($c);
    }

    /**
     * bpmnActions::executeBpmnDesigner.     
     * @return void retorna la vista asociada a este actions
     */
    public function executeBpmnDesigner()
    {
        //$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        //$this->forward('bpmn','editor');
    }

    /**
     * bpmnActions::executeEditor.     
     * @return void retorna la vista asociada a este actions
     */
    public function executeEditor(sfWebRequest $request)
    {
        $this->diagram_id = $request->getParameter('id');
        if ($this->diagram_id) {
            $this->diagram = BpmnProcessPeer::retrieveByPK($this->diagram_id);
            $this->task_configs = $this->getTaskConfigurations($this->diagram_id);
        } else {
            //$this->diagram_data = $this->getDefaultDiagram();
            /*$this->diagram = new BpmnDiagram();
            $this->task_configs = array();*/
        }

        // Obtener todos los usuarios disponibles
        $this->available_users = UsuarioPeer::getAllUserActive();
    }

    public function executeTaskExecute(sfWebRequest $request)
    {
        $task_id = $request->getParameter('task_id');
        $this->task = TaskInstancePeer::retrieveByPK($task_id);

        if (!$this->task) {
            $this->forward404();
        }

        $this->diagram = BpmnProcessPeer::retrieveByPK($this->task->getWorkflowInstance()->getBpmnprocessId());
        $this->task_config = $this->getTaskConfiguration($this->task->getTaskId(), $this->diagram->getPrimaryKey());
    }

    public function executeCompleteTask(sfWebRequest $request)
    {
        $task_id = $request->getParameter('id');
        $action = $request->getParameter('action');
        $variables = $request->getParameter('variables', array());

        $task = TaskInstancePeer::retrieveByPK($task_id);
        if (!$task) {
            $this->forward404();
        }

        // Guardar variables en la tarea (puedes crear una tabla específica para esto)
        if (!empty($variables)) {
            $task->setVariables(json_encode($variables)); // Asumiendo que agregaste esta columna
        }

        if ($action === 'complete') {
            $task->setStatus('completed');
            $task->setCompletedAt(date('Y-m-d G:i:s'));
        }

        $task->save();

        $this->getUser()->setFlash('notice', 'Task ' . ($action === 'complete' ? 'completed' : 'saved') . ' successfully');
        $this->redirect('bpmn/taskList');
    }

    /**
     * POST /api/tasks/:id/upload - Subir archivo a tarea
     */
    public function executeTaskUpload(sfWebRequest $request)
    {
        $taskId = $request->getParameter('task_id') ? $request->getParameter('task_id') : null;
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            $task = TaskInstancePeer::retrieveByPK($taskId);
            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            $attachmentIds = TaskAttachmentPeer::processTaskAttachments($taskId, $request, $usuariologuiado);

            return $this->renderJSON([
                'success' => true,
                'message' => 'Archivo(s) subido(s) correctamente',
                'attachments' => $attachmentIds
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/tasks/:id/attachments - Listar archivos de tarea
     */
    public function executeTaskAttachments(sfWebRequest $request)
    {
        $taskId = $request->getParameter('id');

        try {
            $c = new Criteria();
            $c->add(TaskAttachmentPeer::TASKINSTANCE_ID, $taskId);
            $c->addAscendingOrderByColumn(TaskAttachmentPeer::FECHA_CREACION);

            $attachments = TaskAttachmentPeer::doSelect($c);
            $attachmentsData = [];

            foreach ($attachments as $attachment) {
                $attachmentsData[] = [
                    'id' => $attachment->getPrimaryKey(),
                    'filename' => $attachment->getOriginalFilename(),
                    'file_size' => $attachment->getFileSize(),
                    'mime_type' => $attachment->getMimeType(),
                    'description' => $attachment->getDescripcion(),
                    'is_public' => $attachment->getIsPublic(),
                    'uploaded_by' => $attachment->getUsuarioId() ? $attachment->getUsuario()->getNombreApellido() : null,
                    'created_at' => $attachment->getFechaCreacion('c'),
                    'download_url' => '/api/attachments/' . $attachment->getPrimaryKey() . '/download'
                ];
            }

            return $this->renderJSON([
                'attachments' => $attachmentsData
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/workflows/:id/attachments - Listar todos los archivos del workflow
     */
    public function executeWorkflowAttachments(sfWebRequest $request)
    {
        $workflowinstanceId = $request->getParameter('workflowinstance_id') ? $request->getParameter('workflowinstance_id') : null;

        try {
            $c = new Criteria();
            $c->add(TaskAttachmentPeer::WORKFLOWINSTANCE_ID, $workflowinstanceId);
            $c->add(TaskAttachmentPeer::IS_PUBLIC, true); // Solo archivos públicos
            $c->addAscendingOrderByColumn(TaskAttachmentPeer::FECHA_CREACION);
            $attachments = TaskAttachmentPeer::doSelect($c);

            $attachmentsData = [];
            foreach ($attachments as $attachment) {
                $task = $attachment->getTaskInstance();
                $attachmentsData[] = [
                    'id' => $attachment->getPrimaryKey(),
                    'filename' => $attachment->getOriginalFilename(),
                    'file_size' => $attachment->getFileSize(),
                    'mime_type' => $attachment->getMimeType(),
                    'description' => $attachment->getDescripcion(),
                    'task_name' => $task ? $task->getTaskName() : 'N/A',
                    'uploaded_by' => $attachment->getUsuarioId() ? $attachment->getUsuario()->getNombreApellido() : null,
                    'created_at' => $attachment->getFechaCreacion('c'),
                    'download_url' => '/comun.php/bpmn/downloadAttachment?attachment_id=' . $attachment->getPrimaryKey()
                ];
            }

            return $this->renderJSON([
                'attachments' => $attachmentsData,
                'total' => count($attachmentsData)
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/flows/:process_id - Obtener configuración de flujos
     * POST /api/flows/:process_id/toggle - Habilitar/deshabilitar flujo
     */
    public function executeFlows(sfWebRequest $request)
    {
        $processId = $request->getParameter('process_id') ? $request->getParameter('process_id') : null;
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        try {
            if ($request->getMethod() == 'POST') {
                $action = $request->getPostParameter('action') ? $request->getPostParameter('action') : null;
                if ($action === 'itoggle') {
                    return $this->renderJSON(WorkflowConfigurationPeer::toggleFlow($request, $processId));
                }
            } else {
                return $this->renderJSON(['flows' => WorkflowConfigurationPeer::listFlows($processId)]);
            }
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/tasks/:id/definition - Obtener definición de tarea con campos configurados
     */
    public function executeTaskDefinition(sfWebRequest $request)
    {
        $taskInstanceId = $request->getParameter('taskInstanceId') ? $request->getParameter('taskInstanceId') : null;

        try {
            $taskInstance = TaskInstancePeer::retrieveByPK($taskInstanceId);
            if (!$taskInstance) {
                throw new Exception('Tarea no encontrada');
            }

            $taskDef = $taskInstance->getTaskDefinition();

            if (!$taskDef) {
                return $this->renderJSON([
                    'form_fields' => []
                ]);
            }

            // Parsear form_data que contiene los campos configurados
            $formData = json_decode($taskDef->getFormData(), true);
            $formFields = [];

            if ($formData && isset($formData['fields'])) {
                $formFields = $formData['fields'];
            }

            return $this->renderJSON([
                'task_definition_id' => $taskDef->getPrimaryKey(),
                'task_name' => $taskDef->getTaskName(),
                'task_type' => $taskDef->getTaskType(),
                'assignee_type' => $taskDef->getAssigneeType(),
                'assignee_value' => $taskDef->getAssigneeValue(),
                'duration_expected' => $taskDef->getDurationExpected(),
                'duration_max' => $taskDef->getDurationMax(),
                'form_fields' => $formFields
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * POST /api/tasks/:id/assign - Asignar tarea
     */
    public function executeTaskAssign(sfWebRequest $request)
    {
        $taskId = $request->getParameter('task_id');
        $assigneeId = $request->getParameter('assignee_id');

        try {
            $task = TaskInstancePeer::retrieveByPK($taskId);
            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            $task->setUsuarioId($assigneeId);
            $task->setFechaAsigna(date('Y-m-d G:i:s'));
            $task->setTaskinstancestatusId(TaskInstanceStatusBpmn::assigned);
            $task->setFechaModificacion(date('Y-m-d G:i:s'));
            $task->save();

            return $this->renderJSON([
                'success' => true,
                'message' => 'Tarea asignada correctamente'
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * GET /api/metrics/:process_id - Obtener métricas del proceso
     */
    public function executeMetrics(sfWebRequest $request)
    {
        $processId = $request->getParameter('process_id');
        $dateFrom = $request->getParameter('date_from', date('Y-m-d', strtotime('-30 days')));
        $dateTo = $request->getParameter('date_to', date('Y-m-d'));

        try {
            $c = new Criteria();
            $c->add(BpmnProcessMetricsPeer::BPMNPROCESS_ID, $processId);
            $c->add(BpmnProcessMetricsPeer::METRIC_DATE, $dateFrom, Criteria::GREATER_EQUAL);
            $c->add(BpmnProcessMetricsPeer::METRIC_DATE, $dateTo, Criteria::LESS_EQUAL);
            $c->addAscendingOrderByColumn(BpmnProcessMetricsPeer::METRIC_DATE);

            $metrics = BpmnProcessMetricsPeer::doSelect($c);
            $metricsData = [];

            foreach ($metrics as $metric) {
                $metricsData[] = [
                    'date' => $metric->getMetricDate('Y-m-d'),
                    'instances_started' => $metric->getInstancesStarted(),
                    'instances_completed' => $metric->getInstancesCompleted(),
                    'instances_failed' => $metric->getInstancesFailed(),
                    'avg_duration' => $metric->getAvgDuration(),
                    'min_duration' => $metric->getMinDuration(),
                    'max_duration' => $metric->getMaxDuration(),
                    'tasks_completed' => $metric->getTasksCompleted(),
                    'tasks_overdue' => $metric->getTasksOverdue()
                ];
            }

            return $this->renderJSON([
                'metrics' => $metricsData,
                'summary' => $this->calculateSummaryMetrics($metrics)
            ]);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    public function executeSimulate(sfWebRequest $request)
    {
        $processId = $request->getParameter('processId');
        $simulationMode = $request->getParameter('mode', 'interactive');

        // Obtener el proceso
        $this->process = BpmnProcessPeer::retrieveByPK($processId);

        if (!$this->process) {
            $this->getUser()->setFlash('error', 'Proceso no encontrado');
            $this->redirect('bpmn/dashboard');
        }

        // Obtener el XML del diagrama BPMN
        $this->bpmnXml = $this->process->getBpmnXml();

        if (empty($this->bpmnXml)) {
            $this->getUser()->setFlash('error', 'El proceso no tiene un diagrama BPMN definido');
            $this->redirect('bpmn/designer?process=' . $processId);
        }

        // Parsear el XML para obtener elementos del proceso
        $this->processElements = BpmnParser::parseProcessElements($this->bpmnXml);

        // Obtener variables/campos del proceso
        $variables = WorkflowInstancePeer::getProcessInitialVariables($this->process);
        $this->processVariables = [];
        foreach ($variables as $variable) {
            $this->processVariables[] = [
                'id' => $variable['name'],
                'name' => $variable['label'],
                'type' => $variable['type'],
                'value' => $variable['default_value']
            ];
        }

        //$this->processVariables = BpmnParser::extractProcessVariables($this->bpmnXml);

        // Obtener formularios de tareas
        $this->taskForms = BpmnParser::extractTaskForms($this->bpmnXml);

        // Modo de simulación
        $this->simulationMode = $simulationMode;
    }

    /**
     * Inicia una simulación y la guarda en base de datos
     */
    public function executeStartSimulation(sfWebRequest $request)
    {
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

        try {
            $processId = $request->getParameter('process_id');
            $variables = $request->getParameter('variables', array());
            $isSimulation = $request->getParameter('is_simulation', true);

            // Validar proceso
            $process = BpmnProcessPeer::retrieveByPK($processId);
            if (!$process) {
                return $this->renderJson(array(
                    'success' => false,
                    'message' => 'Proceso no encontrado'
                ));
            }

            $con = Propel::getConnection();
            $con->beginTransaction();

            try {
                // Crear instancia de workflow
                $instance = new WorkflowInstance();
                $instance->setBpmnprocessId($processId);
                $instance->setWorkflowstatusId(1); // En progreso
                $instance->setUsuarioId($usuariologuiado);
                $instance->setNombre('Simulación: ' . $process->getNombre());
                $instance->setCurrentNode('StartEvent');
                $instance->setVariables(json_encode($variables));
                $instance->setFechaInicio(date('Y-m-d G:i:s'));
                $instance->setFechaCreacion(date('Y-m-d G:i:s'));
                $instance->setFechaModificacion(date('Y-m-d G:i:s'));
                $instance->setPrioridad(5);
                $instance->setIsSimulation($isSimulation);
                $instance->save($con);

                // Si es simulación, crear registro en bpmn_simulation
                if ($isSimulation) {
                    $simulation = new BpmnSimulation();
                    $simulation->setBpmnprocessId($processId);
                    $simulation->setNombre('Simulación ' . date('Y-m-d G:i:s'));
                    $simulation->setParameters(json_encode($variables));
                    $simulation->setNumInstances(1);
                    $simulation->setStatusSimulation('running');
                    $simulation->setFechaInicio(date('Y-m-d G:i:s'));
                    $simulation->setFechaCreacion(date('Y-m-d G:i:s'));
                    $simulation->setFechaModificacion(date('Y-m-d G:i:s'));
                    $simulation->save($con);

                    // Asociar instancia con simulación
                    $instance->setBpmnsimulationId($simulation->getPrimaryKey());
                    $instance->save($con);
                }

                $con->commit();

                return $this->renderJson(array(
                    'success' => true,
                    'instance_id' => $instance->getPrimaryKey(),
                    'simulation_id' => $isSimulation ? $simulation->getPrimaryKey() : null,
                    'message' => 'Simulación iniciada correctamente'
                ));
            } catch (Exception $e) {
                $con->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log('Error iniciando simulación: ' . $e->getMessage());
            return $this->renderJson(array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Ejecuta un paso de la simulación
     */
    public function executeExecuteStep(sfWebRequest $request)
    {
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);

        try {
            $instanceId = $request->getParameter('instance_id');
            $currentNode = $request->getParameter('current_node');
            $nextNode = $request->getParameter('next_node');
            $flowId = $request->getParameter('flow_id');
            $taskData = $request->getParameter('task_data', array());

            // Obtener instancia
            $instance = WorkflowInstancePeer::retrieveByPK($instanceId);
            if (!$instance) {
                return $this->renderJson(array(
                    'success' => false,
                    'message' => 'Instancia no encontrada'
                ));
            }

            $con = Propel::getConnection();
            $con->beginTransaction();

            try {
                // Actualizar nodo actual
                $instance->setCurrentNode($nextNode);

                // Actualizar variables si hay datos de tarea
                if (!empty($taskData)) {
                    $variables = json_decode($instance->getVariables(), true) ?: array();
                    $variables = array_merge($variables, $taskData);
                    $instance->setVariables(json_encode($variables));
                }

                $instance->setFechaModificacion(date('Y-m-d G:i:s'));
                $instance->save($con);

                // Verificar si es un nodo final
                $isEndNode = BpmnParser::isEndNode($nextNode, $instance->getBpmnprocessId());

                if ($isEndNode) {
                    $instance->setWorkflowstatusId(WorkflowInstanceStatusBpmn::completed); // Completado
                    $instance->setFechaCompletado(date('Y-m-d G:i:s'));
                    $instance->save($con);

                    // Si es simulación, actualizar registro
                    if ($instance->getIsSimulation() && $instance->getBpmnsimulationId()) {
                        $simulation = BpmnSimulationPeer::retrieveByPK($instance->getBpmnsimulationId());
                        if ($simulation) {
                            $simulation->setStatusSimulation('completed');
                            $simulation->setFechaCompletado(date('Y-m-d G:i:s'));

                            // Calcular resultados
                            $results = MetricsCalculator::calculateSimulationResults($instanceId);
                            $simulation->setResults(json_encode($results));
                            $simulation->save($con);
                        }
                    }
                }

                $con->commit();

                return $this->renderJson(array(
                    'success' => true,
                    'is_end' => $isEndNode,
                    'current_node' => $nextNode,
                    'variables' => json_decode($instance->getVariables(), true),
                    'message' => $isEndNode ? 'Simulación completada' : 'Paso ejecutado'
                ));
            } catch (Exception $e) {
                $con->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log('Error ejecutando paso: ' . $e->getMessage());
            return $this->renderJson(array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * POST /api/simulations - Ejecutar simulación
     * GET /api/simulations/:id - Obtener resultados de simulación
     */
    public function executeSimulations(sfWebRequest $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                return $this->runSimulation($request);
            } else {
                $simulationId = $request->getParameter('id');
                return $this->getSimulationResults($simulationId);
            }
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    private function calculateSummaryMetrics($metrics)
    {
        if (empty($metrics)) {
            return [];
        }

        $totalStarted = 0;
        $totalCompleted = 0;
        $totalFailed = 0;
        $durations = [];

        foreach ($metrics as $metric) {
            $totalStarted += $metric->getInstancesStarted();
            $totalCompleted += $metric->getInstancesCompleted();
            $totalFailed += $metric->getInstancesFailed();

            if ($metric->getAvgDuration()) {
                $durations[] = $metric->getAvgDuration();
            }
        }

        return [
            'total_started' => $totalStarted,
            'total_completed' => $totalCompleted,
            'total_failed' => $totalFailed,
            'completion_rate' => $totalStarted > 0 ? ($totalCompleted / $totalStarted) * 100 : 0,
            'avg_duration_overall' => !empty($durations) ? array_sum($durations) / count($durations) : 0
        ];
    }

    private function getTaskConfiguration($task_id, $diagram_id)
    {
        $criteria = new Criteria();
        $criteria->add(BpmnTaskConfigPeer::TASK_ID, $task_id);
        $criteria->add(BpmnTaskConfigPeer::DIAGRAM_ID, $diagram_id);
        $config = BpmnTaskConfigPeer::doSelectOne($criteria);

        if ($config) {
            return array(
                'assignee_users' => $config->getAssigneeUsers() ?
                    explode(',', $config->getAssigneeUsers()) : array(),
                'variables' => $config->getVariables() ?
                    json_decode($config->getVariables(), true) : array()
            );
        }

        return null;
    }

    private function runSimulation($request)
    {
        $processId = $request->getParameter('process_id');
        $numInstances = $request->getParameter('num_instances', 10);
        $parameters = json_decode($request->getParameter('parameters', '{}'), true);

        $simulation = new BpmnSimulation();
        $simulation->setBpmnprocessId($processId);
        $simulation->setNombre('Simulación ' . date('Y-m-d G:i:s'));
        $simulation->setNumInstances($numInstances);
        $simulation->setParameters(json_encode($parameters));
        $simulation->setStatusSimulacion('running');
        $simulation->setFechaInicio(date('Y-m-d G:i:s'));
        $simulation->setFechaCreacion(date('Y-m-d G:i:s'));
        $simulation->setFechaModificacion(date('Y-m-d G:i:s'));
        $simulation->save();

        // Ejecutar simulación en background
        $this->executeSimulationInBackground($simulation);

        return $this->renderJSON([
            'success' => true,
            'simulation_id' => $simulation->getPrimaryKey(),
            'message' => 'Simulación iniciada'
        ]);
    }

    private function executeSimulationInBackground(BpmnSimulation $simulation)
    {
        // En un entorno real, esto se ejecutaría en una cola de trabajos
        // Por simplicidad, lo ejecutamos síncronamente
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //***********************************************************************************************
        $results = [];
        $engine = new WorkflowEngine(true); // modo simulación

        for ($i = 0; $i < $simulation->getNumInstances(); $i++) {
            try {
                $startTime = microtime(true);

                $instance = $engine->startProcess(
                    $simulation->getBpmnprocessId(),
                    json_decode($simulation->getParameters(), true) ?: [],
                    $usuariologuiado,
                    true
                );

                $duration = microtime(true) - $startTime;

                $results[] = [
                    'instance_id' => $instance->getPrimaryKey(),
                    'duration' => $duration,
                    'status' => $instance->getWorkflowStatus()->getDescripcion()
                ];
            } catch (Exception $e) {
                $results[] = [
                    'instance_id' => null,
                    'duration' => 0,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        // Calcular estadísticas
        $totalDuration = array_sum(array_column($results, 'duration'));
        $avgDuration = $totalDuration / count($results);
        $successful = count(array_filter($results, function ($r) {
            return $r['status'] === 'completed';
        }));

        $summaryResults = [
            'total_instances' => $simulation->getNumInstances(),
            'successful_instances' => $successful,
            'failed_instances' => $simulation->getNumInstances() - $successful,
            'avg_duration' => $avgDuration,
            'total_duration' => $totalDuration,
            'instances' => $results
        ];

        $simulation->setStatusSimulation('completed');
        $simulation->setFechaCompletado(date('Y-m-d G:i:s'));
        $simulation->setResults(json_encode($summaryResults));
        $simulation->setFechaModificacion(date('Y-m-d G:i:s'));
        $simulation->save();
    }

    /**
     * API: Obtener actividad/historial de una tarea
     * Agregar a: apps/frontend/modules/bpmn/actions/actions.class.php
     */
    public function executeTaskActivity(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $taskId = $request->getParameter('task_id');

            if (!$taskId) {
                throw new Exception('ID de tarea requerido');
            }

            // Obtener logs de la tarea
            $activities = [];

            // Verificar si existe la tabla de logs
            if (class_exists('TaskLogPeer')) {
                $c = new Criteria();
                $c->add(TaskLogPeer::TASKINSTANCE_ID, $taskId);
                $c->addDescendingOrderByColumn(TaskLogPeer::CREATED_AT);
                $c->setLimit(10);

                $logs = TaskLogPeer::doSelect($c);

                foreach ($logs as $log) {
                    $user = $log->getUsuarioId() ? UsuarioPeer::retrieveByPK($log->getUsuarioId()) : null;

                    // Traducir acción a texto legible
                    $actionText = TaskInstancePeer::translateTaskAction($log->getAction());

                    $activities[] = [
                        'id' => $log->getPrimaryKey(),
                        'action' => $log->getAction(),
                        'action_text' => $actionText,
                        'user_id' => $log->getUsuarioId(),
                        'user_name' => $user ? trim($user->getNombre() . ' ' . $user->getApellido()) : 'Sistema',
                        'comments' => $log->getComments(),
                        'created_at' => $log->getCreatedAt('Y-m-d H:i:s')
                    ];
                }
            }

            // Si no hay logs, crear actividad básica desde la tarea
            if (empty($activities)) {
                $task = TaskInstancePeer::retrieveByPK($taskId);

                if ($task) {
                    // Actividad de creación
                    $activities[] = [
                        'id' => 0,
                        'action' => 'created',
                        'action_text' => 'creó esta tarea',
                        'user_id' => null,
                        'user_name' => 'Sistema',
                        'comments' => null,
                        'created_at' => $task->getFechaCreacion('Y-m-d G:i:s')
                    ];

                    // Si está asignada
                    if ($task->getUsuarioId()) {
                        $assignee = UsuarioPeer::retrieveByPK($task->getUsuarioId());
                        $activities[] = [
                            'id' => 1,
                            'action' => 'assigned',
                            'action_text' => 'fue asignada a ' . ($assignee ? trim($assignee->getNombreApellido()) : 'un usuario'),
                            'user_id' => null,
                            'user_name' => 'Sistema',
                            'comments' => null,
                            'created_at' => $task->getFechaCreacion('Y-m-d G:i:s')
                        ];
                    }
                }
            }

            return $this->renderText(json_encode([
                'success' => true,
                'activities' => $activities
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'activities' => []
            ]));
        }
    }

    /**
     * GET /api/tasks/:id/detail - Obtiene el detalle completo de una tarea
     * Incluye: formulario dinámico, variables del workflow, archivos adjuntos
     */
    public function executeTaskDetail(sfWebRequest $request)
    {
        $this->setLayout(false);
        $this->getResponse()->setContentType('application/json');

        try {
            $taskId = $request->getParameter('task_id') ? $request->getParameter('task_id') : null;
            $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

            if (!$taskId) {
                throw new Exception('No se proporcionó un ID de tarea');
            }

            // Obtener la tarea
            $task = TaskInstancePeer::retrieveByPK($taskId);

            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            // Verificar que la tarea esté asignada al usuario
            if ($task->getUsuarioId() != $usuariologuiado) {
                throw new Exception('No tienes permiso para ver esta tarea');
            }

            // Obtener workflow instance
            $workflowInstance = $task->getWorkflowInstance();
            $process = $workflowInstance ? $workflowInstance->getBpmnProcess() : null;
            $assignee = $task->getUsuarioId() ? UsuarioPeer::retrieveByPK($task->getUsuarioId()) : null;
            $taskDefinition = $task->getTaskDefinition();

            // Obtener definición de la tarea (con formulario)
            //$taskDefinition = TaskDefinitionPeer::getTaskDefinition($task, $process);

            // Obtener rol/cargo del asignado si existe
            $assigneeRole = null;
            if ($assignee) {
                if (method_exists($assignee, 'getCargo')) {
                    $cargo = $assignee->getCargo();
                    if ($cargo && method_exists($cargo, 'getNombre')) {
                        $assigneeRole = $cargo->getNombre();
                    }
                } elseif (method_exists($assignee, 'getDependencia')) {
                    $dependencia = $assignee->getDependencia();
                    if ($dependencia && method_exists($dependencia, 'getNombre')) {
                        $assigneeRole = $dependencia->getNombre();
                    }
                }
            }

            // Obtener variables del workflow
            $formData = null;
            $formDescription = null;
            if ($taskDefinition) {
                $formData = $taskDefinition->getFormData();
                if (method_exists($taskDefinition, 'getDescription')) {
                    $formDescription = $taskDefinition->getDescription();
                }
            }

            $formValues = null;
            // Opción 3: Los valores están en las variables del workflow
            if (!$formValues && $workflowInstance) {
                $workflowVars = $workflowInstance->getVariables();
                if ($workflowVars) {
                    $varsArray = json_decode($workflowVars, true);
                    // Buscar valores que coincidan con los campos del formulario
                    if ($formData && is_array($varsArray)) {
                        $formFields = json_decode($formData, true);
                        if (is_array($formFields)) {
                            $extractedValues = [];
                            foreach ($formFields as $field) {
                                $fieldId = $field['id'] ?? $field['name'] ?? null;
                                if ($fieldId && isset($varsArray[$fieldId])) {
                                    $extractedValues[$fieldId] = $varsArray[$fieldId];
                                }
                            }
                            if (!empty($extractedValues)) {
                                $formValues = json_encode($extractedValues);
                            }
                        }
                    }
                }
            }

            // Obtener variables del proceso
            $processVariables = null;
            if ($workflowInstance) {
                $processVariables = $workflowInstance->getVariables();
            }

            // Obtener archivos adjuntos del workflow
            $attachments = TaskAttachmentPeer::getWorkflowAttachments($workflowInstance->getPrimaryKey());

            // Obtener datos guardados previamente (borrador)
            $savedFormData = [];
            if ($task->getFormData()) {
                $savedFormData = json_decode($task->getFormData(), true) ?: [];
            }

            $assignee_id = null;
            $assignee_name = null;
            if ($task->getUsuarioId() !== null) {
                $assignee = UsuarioPeer::retrieveByPK($task->getUsuarioId());
                $assignee_id = $task->getUsuarioId();
                $assignee_name = $assignee ? $assignee->getNombreApellido() : null;
            }

            return $this->renderText(json_encode([
                'success' => true,
                'task' => [
                    'id' => $task->getPrimaryKey(),
                    'task_id' => $task->getTaskId(),
                    'task_name' => $task->getTaskName(),
                    'status' => $task->getTaskinstanceStatus()->getDescripcion(),
                    'priority' => $task->getPrioridad(),
                    'due_date' => $task->getFechaVencimiento('c'),
                    'created_at' => $task->getFechaCreacion('c'),
                    'assigned_at' => $task->getFechaAsigna('c'),
                    'comments' => $task->getComentarios(),
                    'assignee_id' => $assignee_id,
                    'assignee_name' => $assignee_name,
                    'assignee_role' => $assigneeRole,
                    'workflow_instance_id' => $task->getWorkflowinstanceId(),
                    'process_id' => $workflowInstance ? $workflowInstance->getBpmnprocessId() : null,
                    'process_name' => $process ? $process->getNombre() : 'Sin proceso',
                    'workflow_instance' => [
                        'id' => $workflowInstance ? $workflowInstance->getPrimaryKey() : null,
                        'name' => $workflowInstance ? $workflowInstance->getNombre() : 'N/A',
                        'process_name' => $process ? $process->getNombre() : 'N/A',
                        'status' => $workflowInstance ? $workflowInstance->getWorkflowStatus()->getDescripcion() : 'N/A'
                    ],
                    //'form_fields' => $taskDefinition['form_fields'],
                    'form_fields' => $formData,
                    'workflow_variables' => $workflowVariables,
                    'workflow_attachments' => $attachments,
                    'saved_form_data' => $savedFormData,
                    'form_data' => $formData,
                    'form_values' => $formValues,
                    'variables' => $processVariables,
                    'comments' => $task->getComentarios()
                ]
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * API: Rechazar tarea individual
     */
    public function executeTasksReject(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        $userId = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

        try {
            $taskId = $request->getParameter('task_id');
            $reason = $request->getParameter('reason', '');

            if (!$taskId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'message' => 'Debes proporcionar un ID de tarea'
                ]));
            }

            // Validar que el motivo no esté vacío
            if (trim($reason) === '') {
                return $this->renderText(json_encode([
                    'success' => false,
                    'message' => 'Debes proporcionar un motivo para el rechazo'
                ]));
            }

            $result = TaskInstancePeer::processTaskRejection($taskId, $userId, $reason);

            if ($result['success']) {
                return $this->renderText(json_encode([
                    'success' => true,
                    'message' => 'Tarea rechazada correctamente'
                ]));
            } else {
                return $this->renderText(json_encode([
                    'success' => false,
                    'message' => $result['error']
                ]));
            }
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * POST /api/tasks/:id/draft - Guarda un borrador de la tarea
     */
    public function executeTaskSaveDraft(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

        try {
            $taskId = $request->getParameter('task_id');
            $fieldsJson = $request->getParameter('fields', '{}');
            $comments = $request->getParameter('comments', '');

            $task = TaskInstancePeer::retrieveByPK($taskId);

            if (!$task) {
                throw new Exception('Tarea no encontrada');
            }

            if ($task->getTaskinstancestatusId() === TaskInstanceStatusBpmn::completed) {
                throw new Exception('Tarea ya se encuentra completada');
            }

            if ($task->getUsuarioId() != $usuariologuiado) {
                throw new Exception('No tienes permiso para modificar esta tarea');
            }

            // Guardar borrador
            $task->setFormData($fieldsJson);
            $task->setComentarios($comments);

            if ($task->getTaskinstancestatusId() === TaskInstanceStatusBpmn::pending) {
                $task->setTaskinstancestatusId(TaskInstanceStatusBpmn::in_progress);
            }

            $task->save();

            return $this->renderText(json_encode([
                'success' => true,
                'message' => 'Borrador guardado correctamente'
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * GET /bpmn/workflow/:id/history - Muestra el historial de un workflow
     */
    public function executeWorkflowHistory(sfWebRequest $request)
    {
        $this->workflowId = $request->getParameter('workflowinstance_id') ? $request->getParameter('workflowinstance_id') : null;

        $workflowInstance = WorkflowInstancePeer::retrieveByPK($this->workflowId);

        if (!$workflowInstance) {
            $this->forward404('Workflow no encontrado');
        }

        $this->workflowInstance = $workflowInstance;
        $this->process = $workflowInstance->getBpmnProcess();

        // Obtener historial
        $c = new Criteria();
        $c->add(WorkflowHistoryPeer::WORKFLOWINSTANCE_ID, $this->workflowId);
        $c->addAscendingOrderByColumn(WorkflowHistoryPeer::FECHA_CREACION);
        $this->history = WorkflowHistoryPeer::doSelect($c);

        // Obtener tareas
        $c = new Criteria();
        $c->add(TaskInstancePeer::WORKFLOWINSTANCE_ID, $this->workflowId);
        $c->addAscendingOrderByColumn(TaskInstancePeer::FECHA_CREACION);
        $this->tasks = TaskInstancePeer::doSelect($c);
    }

    /**
     * GET /bpmn/processXml
     * Retorna el XML BPMN de un proceso
     * 
     * @param sfWebRequest $request
     * @return string JSON
     */
    public function executeProcessXml(sfWebRequest $request)
    {
        $this->setLayout(false);
        $this->getResponse()->setContentType('application/json');

        try {
            $processId = $request->getParameter('process_id');

            if (!$processId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de proceso requerido'
                ]));
            }

            $process = BpmnProcessPeer::retrieveByPK($processId);

            if (!$process) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Proceso no encontrado'
                ]));
            }

            $bpmnXml = $process->getBpmnXml();

            if (empty($bpmnXml)) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'El proceso no tiene XML BPMN definido'
                ]));
            }

            return $this->renderText(json_encode([
                'success' => true,
                'xml' => $bpmnXml,
                'process' => [
                    'id' => $process->getPrimaryKey(),
                    'name' => $process->getNombre(),
                    'version' => $process->getVersion(),
                    'is_active' => $process->getIsActive()
                ]
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Error al obtener XML: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * POST /api/workflows/start - Inicia una nueva instancia de workflow
     */
    public function executeWorkflowStart(sfWebRequest $request)
    {
        $this->setLayout(false);
        $this->getResponse()->setContentType('application/json');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = usuariopeer::retrieveByPK($usuariologuiado);

        if ($request->getMethod() !== 'POST') {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Método no permitido'
            ]));
        }

        try {
            $processId = $request->getParameter('process_id');
            $instanceName = $request->getParameter('instance_name');
            $priority = $request->getParameter('priority', 5);
            $comments = $request->getParameter('comments', '');
            $variablesJson = $request->getParameter('variables', '{}');

            // Validar proceso
            $process = BpmnProcessPeer::retrieveByPK($processId);
            if (!$process) {
                throw new Exception('Proceso no encontrado');
            }

            if (!$process->getIsActive()) {
                throw new Exception('No se puede iniciar el proceso, debe estar activo');
            }

            // Parsear variables
            $variables = json_decode($variablesJson, true);
            if (!is_array($variables)) {
                $variables = [];
            }

            // Agregar información del usuario que inicia
            $variables['_initiator'] = $usuariologuiado;
            $variables['_initiator_name'] = trim($usuario->getNombreApellido());
            $variables['_start_time'] = date('c');
            $variables['_comments'] = $comments;

            // Generar nombre de instancia si no se proporciona
            if (empty($instanceName)) {
                $instanceName = $process->getNombre() . ' - ' . date('Y-m-d G:i:s');
            }

            // Usar el motor de workflow para iniciar
            $engine = new WorkflowEngine();
            $instance = $engine->startProcess(
                $processId,
                $variables,
                $usuariologuiado,
                false // No es simulación
            );

            // Actualizar nombre y prioridad de la instancia
            $instance->setNombre($instanceName);
            $instance->setPrioridad((int)$priority);
            $instance->save();

            // Verificar si el usuario tiene una tarea pendiente en este workflow
            $hasPendingTask = TaskInstancePeer::checkUserHasPendingTask($instance->getPrimaryKey(), $usuariologuiado);

            return $this->renderText(json_encode([
                'success' => true,
                'message' => 'Proceso iniciado correctamente',
                'workflow_id' => $instance->getPrimaryKey(),
                'workflow_name' => $instance->getNombre(),
                'has_pending_task' => $hasPendingTask
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    private function getAllUsers()
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
        $criteria->addAscendingOrderByColumn(UsuarioPeer::APELLIDO);
        return UsuarioPeer::doSelect($criteria);
    }

    private function getTaskConfigurations($diagram_id)
    {
        $criteria = new Criteria();
        $criteria->add(BpmnTaskConfigPeer::DIAGRAM_ID, $diagram_id);
        $configs = BpmnTaskConfigPeer::doSelect($criteria);

        $result = array();
        foreach ($configs as $config) {
            $result[$config->getTaskId()] = array(
                'assignee_users' => $config->getAssigneeUsers() ?
                    explode(',', $config->getAssigneeUsers()) : array(),
                'variables' => $config->getVariables() ?
                    json_decode($config->getVariables(), true) : array()
            );
        }
        return $result;
    }

    /**
     * GET /api/processes/available - Lista procesos activos disponibles para usuarios
     * Incluye estadísticas de cada proceso
     */
    public function executeProcessesAvailable(sfWebRequest $request)
    {
        $this->setLayout(false);
        $this->getResponse()->setContentType('application/json');

        try {
            // Obtener solo procesos activos
            $c = new Criteria();
            $c->add(BpmnProcessPeer::IS_ACTIVE, true);
            $c->addAscendingOrderByColumn(BpmnProcessPeer::NOMBRE);

            $processes = BpmnProcessPeer::doSelect($c);

            $result = [];
            foreach ($processes as $process) {
                // Obtener estadísticas del proceso
                $stats = WorkflowInstancePeer::getProcessStats($process->getPrimaryKey());

                // Obtener variables iniciales definidas en el diagrama (si las hay)
                $initialVariables = WorkflowInstancePeer::getProcessInitialVariables($process);

                $result[] = [
                    'id' => $process->getPrimaryKey(),
                    'name' => $process->getNombre(),
                    'description' => $process->getDescripcion(),
                    'version' => $process->getVersion(),
                    'is_active' => $process->getIsActive(),
                    'created_at' => $process->getFechaCreacion('c'),
                    'updated_at' => $process->getFechaModificacion('c'),
                    'stats' => $stats,
                    'initial_variables' => $initialVariables
                ];
            }

            return $this->renderText(json_encode([
                'success' => true,
                'processes' => $result
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * GET /api/attachments/:id/download - Descarga un archivo adjunto
     */
    public function executeAttachmentDownload(sfWebRequest $request)
    {
        try {
            $attachmentId = $request->getParameter('attachmentId') ? $request->getParameter('attachmentId') : null;
            $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

            if (!$attachmentId) {
                throw new Exception('No se proporciono el id del archivo adjunto');
            }

            $attachment = TaskAttachmentPeer::retrieveByPK($attachmentId);

            if (!$attachment) {
                throw new Exception('Archivo no encontrado');
            }

            // Verificar permisos (el usuario debe tener acceso al workflow)
            $workflowInstance = WorkflowInstancePeer::retrieveByPK($attachment->getWorkflowInstanceId());
            if (!$workflowInstance) {
                throw new Exception('Workflow no encontrado');
            }

            // Verificar si el archivo es público o si el usuario tiene tarea en el workflow
            if (!$attachment->getIsPublic()) {
                $c = new Criteria();
                $c->add(TaskInstancePeer::WORKFLOWINSTANCE_ID, $attachment->getWorkflowinstanceId());
                $c->add(TaskInstancePeer::USUARIO_ID, $usuariologuiado);
                $hasAccess = TaskInstancePeer::doCount($c) > 0;

                if (!$hasAccess) {
                    throw new Exception('No tienes permiso para descargar este archivo');
                }
            }

            // Verificar que el archivo existe
            $pathFile = $attachment->getPathAbsolute() . DIRECTORY_SEPARATOR . $attachment->getPathRelative();
            $fullPathFile = $pathFile . DIRECTORY_SEPARATOR . $attachment->getFilename();

            if (!file_exists($fullPathFile)) {
                throw new Exception('El archivo no existe en el servidor');
            }

            $filename_tmp = md5($file_name . time()) . '.' . $format;
            $pathtmp = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $filename_tmp;
            $extension = pathinfo($fullPathFile, PATHINFO_EXTENSION);

            if (copy($fullPathFile, $pathtmp)) {
                $url_viewer = $base_web . '/tmp/' . $filename_tmp;
                if (strtolower($extension) == 'pdf') {
                    $url_viewer = $base_web . '/viewerEx.php?fileview=' . $filename_tmp;
                    $this->redirect($url_viewer);
                } else {
                    // Enviar archivo
                    $response = $this->getResponse();
                    $response->clearHttpHeaders();
                    $response->setContentType($attachment->getMimeType());
                    $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $attachment->getOriginalFilename() . '"');
                    $response->setHttpHeader('Content-Length', $attachment->getFileSize());
                    $response->sendHttpHeaders();

                    readfile($pathtmp);
                }
            } else {
                $this->redirect('/no_file_exists.html');
            }

            return sfView::NONE;
        } catch (Exception $e) {
            $this->redirect('/no_file_exists.html');
        }
    }

    public function executeApiMetricsProcess(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $processId = $request->getParameter('process_id');

            if (!$processId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de proceso requerido'
                ]));
            }

            // Verificar que existe
            $process = BpmnProcessPeer::retrieveByPK($processId);
            if (!$process) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'Proceso no encontrado'
                ]));
            }

            // Obtener parámetros de fecha
            $startDate = $request->getParameter('start_date');
            $endDate = $request->getParameter('end_date');

            // Calcular métricas
            $metrics = MetricsCalculator::getProcessMetrics($processId, $startDate, $endDate);

            return $this->renderText(json_encode([
                'success' => true,
                'process' => [
                    'id' => $process->getPrimaryKey(),
                    'name' => $process->getNombre()
                ],
                'metrics' => $metrics
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Error al obtener métricas: ' . $e->getMessage()
            ]));
        }
    }

    // ========================================
    // API: Métricas globales
    // GET /api/metrics
    // ========================================
    public function executeApiMetricsGlobal(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $startDate = $request->getParameter('start_date');
            $endDate = $request->getParameter('end_date');

            $metrics = MetricsCalculator::getGlobalMetrics($startDate, $endDate);

            return $this->renderText(json_encode([
                'success' => true,
                'metrics' => $metrics
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Error al obtener métricas: ' . $e->getMessage()
            ]));
        }
    }

    // ========================================
    // API: Métricas históricas
    // GET /api/metrics/:process_id/history
    // ========================================
    public function executeApiMetricsHistory(sfWebRequest $request)
    {
        $this->getResponse()->setContentType('application/json');

        try {
            $processId = $request->getParameter('process_id');
            $days = $request->getParameter('days', 30);

            if (!$processId) {
                return $this->renderText(json_encode([
                    'success' => false,
                    'error' => 'ID de proceso requerido'
                ]));
            }

            $history = MetricsCalculator::getHistoricalMetrics($processId, (int)$days);

            return $this->renderText(json_encode([
                'success' => true,
                'history' => $history
            ]));
        } catch (Exception $e) {
            return $this->renderText(json_encode([
                'success' => false,
                'error' => 'Error al obtener historial: ' . $e->getMessage()
            ]));
        }
    }

    private function saveTaskConfigurations($diagram_id, $configs)
    {
        // Eliminar configuraciones existentes
        $criteria = new Criteria();
        $criteria->add(BpmnTaskConfigPeer::DIAGRAM_ID, $diagram_id);
        BpmnTaskConfigPeer::doDelete($criteria);

        // Guardar nuevas configuraciones
        foreach ($configs as $task_id => $config) {
            $taskConfig = new BpmnTaskConfig();
            $taskConfig->setDiagramId($diagram_id);
            $taskConfig->setTaskId($task_id);

            // Guardar usuarios asignados
            if (isset($config['assignee_users']) && is_array($config['assignee_users'])) {
                $taskConfig->setAssigneeUsers(implode(',', $config['assignee_users']));
            }

            // Guardar variables personalizadas
            if (isset($config['variables']) && is_array($config['variables'])) {
                $taskConfig->setVariables(json_encode($config['variables']));
            }

            $taskConfig->save();
        }
    }

    private function renderJSON($data)
    {
        return $this->renderText(json_encode($data));
    }

    private function renderError($message, $code = 500)
    {
        $this->getResponse()->setStatusCode($code);
        return $this->renderJSON([
            'success' => false,
            'error' => $message
        ]);
    }
}
