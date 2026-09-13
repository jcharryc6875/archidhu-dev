<?php

class BpmnEmailService
{
    /**
     * Enviar notificación de tarea asignada
     */
    public static function sendTaskAssignedNotification(TaskInstance $taskInstance)
    {
        try {
            $usuario = $taskInstance->getUsuarioId() ? $taskInstance->getUsuario() : null;
            if (!$usuario || !trim($usuario->getEmail())) {
                sfContext::getInstance()->getLogger()->info('No se puede enviar email: usuario sin email');
                return false;
            }

            $workflow = $taskInstance->getWorkflowInstance();
            $process = $workflow ? $workflow->getBpmnProcess() : null;

            $subject = 'SGDEA - Workflow Nueva tarea asignada: ' . $taskInstance->getTaskName();

            $body = self::getTaskAssignedEmailTemplate([
                'usuario' => $usuario,
                'task' => $taskInstance,
                'workflow' => $workflow,
                'process' => $process
            ]);

            return self::sendEmail($usuario->getEmail(), $subject, $body);
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err('Error enviando email de tarea asignada: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de tarea próxima a vencer
     */
    public static function sendTaskDueSoonNotification($taskInstance, $hoursRemaining)
    {
        try {
            $usuario = $taskInstance->getUsuarioAsignado();
            if (!$usuario || !$usuario->getEmail()) {
                return false;
            }

            $workflow = $taskInstance->getWorkflowInstance();
            $process = $workflow ? $workflow->getBpmnProcess() : null;

            $subject = 'Recordatorio: Tarea próxima a vencer - ' . $taskInstance->getTaskName();

            $body = self::getTaskDueSoonEmailTemplate([
                'usuario' => $usuario,
                'task' => $taskInstance,
                'workflow' => $workflow,
                'process' => $process,
                'hoursRemaining' => $hoursRemaining
            ]);

            return self::sendEmail($usuario->getEmail(), $subject, $body);
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err('Error enviando email de recordatorio: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de tarea vencida
     */
    public static function sendTaskOverdueNotification($taskInstance)
    {
        try {
            $usuario = $taskInstance->getUsuarioAsignado();
            if (!$usuario || !$usuario->getEmail()) {
                return false;
            }

            $workflow = $taskInstance->getWorkflowInstance();
            $process = $workflow ? $workflow->getBpmnProcess() : null;

            $subject = 'URGENTE: Tarea vencida - ' . $taskInstance->getTaskName();

            $body = self::getTaskOverdueEmailTemplate([
                'usuario' => $usuario,
                'task' => $taskInstance,
                'workflow' => $workflow,
                'process' => $process
            ]);

            return self::sendEmail($usuario->getEmail(), $subject, $body);
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err('Error enviando email de tarea vencida: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de workflow completado
     */
    public static function sendWorkflowCompletedNotification($workflowInstance)
    {
        try {
            $iniciador = $workflowInstance->getUsuarioInicio();
            if (!$iniciador || !$iniciador->getEmail()) {
                return false;
            }

            $process = $workflowInstance->getBpmnProcess();

            $subject = 'Workflow completado: ' . ($workflowInstance->getNombre() ?: 'Workflow #' . $workflowInstance->getPrimaryKey());

            $body = self::getWorkflowCompletedEmailTemplate([
                'usuario' => $iniciador,
                'workflow' => $workflowInstance,
                'process' => $process
            ]);

            return self::sendEmail($iniciador->getEmail(), $subject, $body);
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err('Error enviando email de workflow completado: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Método genérico para enviar emails usando PHPMailer
     */
    private static function sendEmail($to, $subject, $body)
    {
        try {
            $baseMail = new BaseMailSimad();
            $baseMail->SetSubject($subject);
            $baseMail->SetMsgHTML($body);
            $baseMail->SetAddAddress(trim($to), trim($to));
            $baseMail->mail_object->isHTML(true);
            $baseMail->mail_object->AltBody = strip_tags($body);
            //********************************************************************************
            $efectiveMail = $baseMail->InitSend();
            //********************************************************************************
            if ($efectiveMail === true) {
                $baseMail->writetolog("Alerta enviada workflow con destinatario: " . $to);
                return true;
            } else {
                $baseMail->writetolog("Error al enviar alerta workflow con destinatario: " . $to);
                return false;
            }
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err("Error enviando email: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Template HTML para tarea asignada
     */
    private static function getTaskAssignedEmailTemplate($data)
    {
        $usuario = $data['usuario'];
        $task = $data['task'];
        $workflow = $data['workflow'];
        $process = $data['process'];

        $taskUrl = sfConfig::get('app_base_url') . '/comun.php/bpmn/taskList#task-' . $task->getPrimaryKey();

        $dueDate = $task->getFechaVencimiento()
            ? date('d/m/Y H:i', strtotime($task->getFechaVencimiento()))
            : 'No definida';

        $priority = $task->getPrioridad() ?: 5;
        $priorityLabel = $priority >= 8 ? 'Alta' : ($priority >= 5 ? 'Media' : 'Baja');
        $priorityColor = $priority >= 8 ? '#dc3545' : ($priority >= 5 ? '#ffc107' : '#28a745');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .greeting { font-size: 16px; margin-bottom: 20px; }
                .task-box { background: #f8f9fa; padding: 20px; border-left: 4px solid #667eea; margin: 20px 0; border-radius: 4px; }
                .task-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; }
                .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
                .info-label { font-weight: 600; color: #666; }
                .info-value { color: #333; }
                .priority-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; color: white; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .button:hover { background: #5568d3; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 Nueva Tarea Asignada</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        Hola <strong>' . htmlspecialchars($usuario->getNombreApellido()) . '</strong>,
                    </div>
                    <p>Se te ha asignado una nueva tarea en el sistema BPMN:</p>
                    
                    <div class="task-box">
                        <div class="task-title">' . htmlspecialchars($task->getTaskName()) . '</div>
                        
                        <div class="info-row">
                            <span class="info-label">Proceso:</span>
                            <span class="info-value">' . htmlspecialchars($process ? $process->getNombre() : '-') . '</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Workflow:</span>
                            <span class="info-value">' . htmlspecialchars($workflow ? $workflow->getNombre() : '-') . '</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Prioridad:</span>
                            <span class="info-value">
                                <span class="priority-badge" style="background: ' . $priorityColor . ';">' . $priorityLabel . '</span>
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Fecha de vencimiento:</span>
                            <span class="info-value">' . $dueDate . '</span>
                        </div>
                    </div>
                    
                    <p style="margin-top: 20px; font-size: 14px; color: #666;">
                        Por favor, accede al sistema para completar esta tarea.
                    </p>
                </div>
                <div class="footer">
                    Este es un mensaje automático del Sistema BPMN. Por favor no responder a este correo.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Template HTML para tarea próxima a vencer
     */
    private static function getTaskDueSoonEmailTemplate($data)
    {
        $usuario = $data['usuario'];
        $task = $data['task'];
        $workflow = $data['workflow'];
        $process = $data['process'];
        $hoursRemaining = $data['hoursRemaining'];

        $dueDate = $task->getFechaVencimiento()
            ? date('d/m/Y H:i', strtotime($task->getFechaVencimiento()))
            : 'No definida';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px; }
                .task-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
                .time-remaining { font-size: 24px; font-weight: bold; color: #ff9800; text-align: center; margin: 20px 0; }
                .button { display: inline-block; background: #ffc107; color: #333; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>⏰ Recordatorio de Tarea</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        Hola <strong>' . htmlspecialchars($usuario->getNombre()) . '</strong>,
                    </div>
                    <p>Te recordamos que tienes una tarea próxima a vencer:</p>
                    
                    <div class="warning-box">
                        <div class="task-title">' . htmlspecialchars($task->getTaskName()) . '</div>
                        <p><strong>Proceso:</strong> ' . htmlspecialchars($process ? $process->getNombre() : '-') . '</p>
                        <p><strong>Vence:</strong> ' . $dueDate . '</p>
                    </div>
                    
                    <div class="time-remaining">
                        ⏱️ Quedan aproximadamente ' . round($hoursRemaining) . ' horas
                    </div>
                    
                    <center>
                        <a href="' . $taskUrl . '" class="button">Completar Tarea Ahora</a>
                    </center>
                </div>
                <div class="footer">
                    Este es un mensaje automático del Sistema BPMN.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Template HTML para tarea vencida
     */
    private static function getTaskOverdueEmailTemplate($data)
    {
        $usuario = $data['usuario'];
        $task = $data['task'];
        $workflow = $data['workflow'];
        $process = $data['process'];

        $taskUrl = sfConfig::get('app_base_url') . '/comun.php/bpmn/taskList#task-' . $task->getPrimaryKey();

        $dueDate = $task->getFechaVencimiento()
            ? date('d/m/Y H:i', strtotime($task->getFechaVencimiento()))
            : 'No definida';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .alert-box { background: #f8d7da; border-left: 4px solid #dc3545; padding: 20px; margin: 20px 0; border-radius: 4px; color: #721c24; }
                .task-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                .button { display: inline-block; background: #dc3545; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🚨 Tarea Vencida</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        Hola <strong>' . htmlspecialchars($usuario->getNombre()) . '</strong>,
                    </div>
                    <p><strong>URGENTE:</strong> La siguiente tarea ha excedido su fecha límite:</p>
                    
                    <div class="alert-box">
                        <div class="task-title">' . htmlspecialchars($task->getTaskName()) . '</div>
                        <p><strong>Proceso:</strong> ' . htmlspecialchars($process ? $process->getNombre() : '-') . '</p>
                        <p><strong>Venció el:</strong> ' . $dueDate . '</p>
                    </div>
                    
                    <p>Por favor, completa esta tarea lo antes posible para evitar retrasos en el proceso.</p>
                    
                    <center>
                        <a href="' . $taskUrl . '" class="button">Ir a la Tarea</a>
                    </center>
                </div>
                <div class="footer">
                    Este es un mensaje automático del Sistema BPMN.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Template HTML para workflow completado
     */
    private static function getWorkflowCompletedEmailTemplate($data)
    {
        $usuario = $data['usuario'];
        $workflow = $data['workflow'];
        $process = $data['process'];

        $workflowUrl = sfConfig::get('app_base_url') . '/comun.php/bpmn/dashboard?tab=completed';

        $duration = '';
        if ($workflow->getFechaInicio() && $workflow->getFechaFin()) {
            $inicio = new DateTime($workflow->getFechaInicio());
            $fin = new DateTime($workflow->getFechaFin());
            $diff = $inicio->diff($fin);

            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . ' días';
            if ($diff->h > 0) $parts[] = $diff->h . ' horas';
            if ($diff->i > 0) $parts[] = $diff->i . ' minutos';
            $duration = implode(', ', $parts) ?: 'Menos de 1 minuto';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 4px; }
                .workflow-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; }
                .button { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Workflow Completado</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        Hola <strong>' . htmlspecialchars($usuario->getNombreApellido()) . '</strong>,
                    </div>
                    <p>Te informamos que el siguiente workflow ha sido completado exitosamente:</p>
                    
                    <div class="success-box">
                        <div class="workflow-title">' . htmlspecialchars($workflow->getNombre() ?: 'Workflow #' . $workflow->getPrimaryKey()) . '</div>
                        <p><strong>Proceso:</strong> ' . htmlspecialchars($process ? $process->getNombre() : '-') . '</p>
                        <p><strong>Iniciado:</strong> ' . date('d/m/Y H:i', strtotime($workflow->getFechaInicio())) . '</p>
                        <p><strong>Finalizado:</strong> ' . date('d/m/Y H:i', strtotime($workflow->getFechaFin())) . '</p>
                        ' . ($duration ? '<p><strong>Duración:</strong> ' . $duration . '</p>' : '') . '
                    </div>
                    
                    <p>Puedes ver los detalles y métricas del workflow completado ingresando al sistema en la seccion de dashboard.</p>                    
                </div>
                <div class="footer">
                    Este es un mensaje automático del Sistema BPMN.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }
}
