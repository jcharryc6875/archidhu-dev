<?php

/**
 * Parser para archivos BPMN
 * Extrae información estructural de los procesos
 */
class BpmnParser
{
    private $dom;
    private $xpath;

    public function __construct($bpmnXml)
    {
        $this->dom = new DOMDocument();
        $this->dom->loadXML($bpmnXml);
        $this->xpath = new DOMXPath($this->dom);

        // Registrar namespaces
        $this->xpath->registerNamespace('bpmn2', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $this->xpath->registerNamespace('bpmndi', 'http://www.omg.org/spec/BPMN/20100524/DI');
    }

    /**
     * Extrae todos los elementos de un tipo específico
     */
    public function getElementsByType($type)
    {
        $elements = [];
        $nodes = $this->xpath->query("//bpmn2:$type");

        if ($nodes === false || $nodes->length === 0) {
            return $elements;
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            if ($node instanceof DOMElement) {
                $elements[] = [
                    'id' => $node->getAttribute('id'),
                    'name' => $node->getAttribute('name'),
                    'type' => $type,
                    'attributes' => $this->getNodeAttributes($node)
                ];
            }
        }

        return $elements;
    }

    /**
     * Obtiene los sequence flows
     */
    public function getSequenceFlows()
    {
        $flows = [];
        $nodes = $this->xpath->query('//bpmn2:sequenceFlow');

        if ($nodes === false || $nodes->length === 0) {
            return $flows;
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            if ($node instanceof DOMElement) {
                $flows[] = [
                    'id' => $node->getAttribute('id'),
                    'name' => $node->getAttribute('name'),
                    'sourceRef' => $node->getAttribute('sourceRef'),
                    'targetRef' => $node->getAttribute('targetRef'),
                    'conditionExpression' => $this->getConditionExpression($node)
                ];
            }
        }

        return $flows;
    }

    /**
     * Obtiene las tareas de usuario
     */
    public function getUserTasks()
    {
        $tasks = [];
        $nodes = $this->xpath->query('//bpmn2:userTask');

        if ($nodes === false || $nodes->length === 0) {
            return $tasks;
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            if ($node instanceof DOMElement) {
                $tasks[] = [
                    'id' => $node->getAttribute('id'),
                    'name' => $node->getAttribute('name'),
                    'assignee' => $node->getAttribute('assignee'),
                    'candidateGroups' => $node->getAttribute('candidateGroups'),
                    'formKey' => $node->getAttribute('formKey'),
                    'documentation' => $this->getDocumentation($node),
                    'extensionElements' => $this->getExtensionElements($node)
                ];
            }
        }

        return $tasks;
    }

    /**
     * Obtiene eventos de timer
     */
    public function getTimerEvents()
    {
        $events = [];
        $nodes = $this->xpath->query('//bpmn2:intermediateCatchEvent[bpmn2:timerEventDefinition]');

        if ($nodes === false || $nodes->length === 0) {
            return $events;
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            if ($node instanceof DOMElement) {
                $timerDef = $node->getElementsByTagName('timerEventDefinition')->item(0);
                $events[] = [
                    'id' => $node->getAttribute('id'),
                    'name' => $node->getAttribute('name'),
                    'timerType' => $this->getTimerType($timerDef),
                    'timerExpression' => $this->getTimerExpression($timerDef)
                ];
            }
        }

        return $events;
    }

    /**
     * Valida la estructura del BPMN
     */
    public function validate()
    {
        $errors = [];

        // Verificar que hay al menos un start event
        $startEvents = $this->xpath->query('//bpmn2:startEvent');
        if ($startEvents === false || $startEvents->length === 0) {
            $errors[] = 'El proceso debe tener al menos un evento de inicio';
        }

        // Verificar que hay al menos un end event
        $endEvents = $this->xpath->query('//bpmn2:endEvent');
        if ($endEvents === false || $endEvents->length === 0) {
            $errors[] = 'El proceso debe tener al menos un evento final';
        }

        // Verificar sequence flows
        $flows = $this->getSequenceFlows();
        foreach ($flows as $flow) {
            if (empty($flow['sourceRef']) || empty($flow['targetRef'])) {
                $errors[] = sprintf('Sequence flow %s debe tener sourceRef y targetRef', $flow['id']);
            }
        }

        // Verificar que todos los elementos referenciados existen
        $allElements = $this->getAllElementIds();
        foreach ($flows as $flow) {
            if (!in_array($flow['sourceRef'], $allElements)) {
                $errors[] = sprintf(
                    'Elemento %s referenciado en flow %s no existe',
                    $flow['sourceRef'],
                    $flow['id']
                );
            }
            if (!in_array($flow['targetRef'], $allElements)) {
                $errors[] = sprintf(
                    'Elemento %s referenciado en flow %s no existe',
                    $flow['targetRef'],
                    $flow['id']
                );
            }
        }

        return $errors;
    }

    private function getNodeAttributes($node)
    {
        $attributes = [];
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attributes[$attr->name] = $attr->value;
            }
        }
        return $attributes;
    }

    private function getConditionExpression($node)
    {
        $conditionNodes = $node->getElementsByTagName('conditionExpression');
        return $conditionNodes->length > 0 ? $conditionNodes->item(0)->textContent : null;
    }

    private function getDocumentation($node)
    {
        $docNodes = $node->getElementsByTagName('documentation');
        return $docNodes->length > 0 ? $docNodes->item(0)->textContent : null;
    }

    private function getExtensionElements($node)
    {
        $extensions = [];
        $extNodes = $node->getElementsByTagName('extensionElements');

        if ($extNodes->length > 0) {
            foreach ($extNodes->item(0)->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $extensions[$child->nodeName] = $child->textContent;
                }
            }
        }

        return $extensions;
    }

    private function getTimerType($timerDef)
    {
        if ($timerDef->getElementsByTagName('timeDuration')->length > 0) {
            return 'duration';
        } elseif ($timerDef->getElementsByTagName('timeDate')->length > 0) {
            return 'date';
        } elseif ($timerDef->getElementsByTagName('timeCycle')->length > 0) {
            return 'cycle';
        }
        return 'unknown';
    }

    private function getTimerExpression($timerDef)
    {
        $duration = $timerDef->getElementsByTagName('timeDuration');
        if ($duration->length > 0) {
            return $duration->item(0)->textContent;
        }

        $date = $timerDef->getElementsByTagName('timeDate');
        if ($date->length > 0) {
            return $date->item(0)->textContent;
        }

        $cycle = $timerDef->getElementsByTagName('timeCycle');
        if ($cycle->length > 0) {
            return $cycle->item(0)->textContent;
        }

        return null;
    }

    private function getAllElementIds()
    {
        $ids = [];
        $allNodes = $this->xpath->query('//*[@id]');

        if ($allNodes !== false && $allNodes->length > 0) {
            for ($i = 0; $i < $allNodes->length; $i++) {
                $node = $allNodes->item($i);
                if ($node instanceof DOMElement) {
                    $ids[] = $node->getAttribute('id');
                }
            }
        }

        return $ids;
    }

    public static function parseProcessElements($bpmnXml)
    {
        $elements = array(
            'startEvents' => array(),
            'tasks' => array(),
            'userTasks' => array(),
            'gateways' => array(),
            'endEvents' => array(),
            'sequenceFlows' => array()
        );

        try {
            // Cargar XML
            $xml = @simplexml_load_string($bpmnXml);

            if ($xml === false) {
                error_log('Error cargando XML');
                return $elements;
            }

            // Obtener namespaces
            $namespaces = $xml->getNamespaces(true);

            // Determinar el prefijo del namespace BPMN
            $bpmnNs = '';
            if (isset($namespaces['bpmn'])) {
                $bpmnNs = 'bpmn';
            } elseif (isset($namespaces['bpmn2'])) {
                $bpmnNs = 'bpmn2';
            } else {
                // Buscar namespace que contenga BPMN
                foreach ($namespaces as $prefix => $uri) {
                    if (strpos($uri, 'BPMN') !== false || strpos($uri, 'bpmn') !== false) {
                        $bpmnNs = $prefix;
                        break;
                    }
                }
            }

            // Si no hay namespace, intentar sin él
            if (empty($bpmnNs)) {
                // Buscar proceso directamente
                $processes = $xml->xpath('//process');
            } else {
                // Buscar con namespace
                $processes = $xml->xpath('//' . $bpmnNs . ':process');
            }

            if (empty($processes)) {
                error_log('No se encontraron procesos en el XML');
                return $elements;
            }

            foreach ($processes as $process) {
                // Start Events
                if (empty($bpmnNs)) {
                    $startEvents = $process->xpath('.//startEvent');
                } else {
                    $startEvents = $process->xpath('.//' . $bpmnNs . ':startEvent');
                }

                foreach ($startEvents as $startEvent) {
                    $attrs = $startEvent->attributes();
                    $elements['startEvents'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : ''
                    );
                }

                // User Tasks
                if (empty($bpmnNs)) {
                    $userTasks = $process->xpath('.//userTask');
                } else {
                    $userTasks = $process->xpath('.//' . $bpmnNs . ':userTask');
                }

                foreach ($userTasks as $userTask) {
                    $attrs = $userTask->attributes();
                    $elements['userTasks'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : ''
                    );
                }

                // Tasks
                if (empty($bpmnNs)) {
                    $tasks = $process->xpath('.//task');
                } else {
                    $tasks = $process->xpath('.//' . $bpmnNs . ':task');
                }

                foreach ($tasks as $task) {
                    $attrs = $task->attributes();
                    $elements['tasks'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : ''
                    );
                }

                // Service Tasks
                if (empty($bpmnNs)) {
                    $serviceTasks = $process->xpath('.//serviceTask');
                } else {
                    $serviceTasks = $process->xpath('.//' . $bpmnNs . ':serviceTask');
                }

                foreach ($serviceTasks as $serviceTask) {
                    $attrs = $serviceTask->attributes();
                    $elements['tasks'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : ''
                    );
                }

                // Exclusive Gateways
                if (empty($bpmnNs)) {
                    $exGateways = $process->xpath('.//exclusiveGateway');
                } else {
                    $exGateways = $process->xpath('.//' . $bpmnNs . ':exclusiveGateway');
                }

                foreach ($exGateways as $gateway) {
                    $attrs = $gateway->attributes();
                    $elements['gateways'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : '',
                        'type' => 'exclusive'
                    );
                }

                // Parallel Gateways
                if (empty($bpmnNs)) {
                    $parGateways = $process->xpath('.//parallelGateway');
                } else {
                    $parGateways = $process->xpath('.//' . $bpmnNs . ':parallelGateway');
                }

                foreach ($parGateways as $gateway) {
                    $attrs = $gateway->attributes();
                    $elements['gateways'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : '',
                        'type' => 'parallel'
                    );
                }

                // Inclusive Gateways
                if (empty($bpmnNs)) {
                    $incGateways = $process->xpath('.//inclusiveGateway');
                } else {
                    $incGateways = $process->xpath('.//' . $bpmnNs . ':inclusiveGateway');
                }

                foreach ($incGateways as $gateway) {
                    $attrs = $gateway->attributes();
                    $elements['gateways'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : '',
                        'type' => 'inclusive'
                    );
                }

                // End Events
                if (empty($bpmnNs)) {
                    $endEvents = $process->xpath('.//endEvent');
                } else {
                    $endEvents = $process->xpath('.//' . $bpmnNs . ':endEvent');
                }

                foreach ($endEvents as $endEvent) {
                    $attrs = $endEvent->attributes();
                    $elements['endEvents'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : ''
                    );
                }

                // Sequence Flows
                if (empty($bpmnNs)) {
                    $flows = $process->xpath('.//sequenceFlow');
                } else {
                    $flows = $process->xpath('.//' . $bpmnNs . ':sequenceFlow');
                }

                foreach ($flows as $flow) {
                    $attrs = $flow->attributes();
                    $elements['sequenceFlows'][] = array(
                        'id' => isset($attrs['id']) ? (string)$attrs['id'] : '',
                        'name' => isset($attrs['name']) ? (string)$attrs['name'] : '',
                        'sourceRef' => isset($attrs['sourceRef']) ? (string)$attrs['sourceRef'] : '',
                        'targetRef' => isset($attrs['targetRef']) ? (string)$attrs['targetRef'] : ''
                    );
                }
            }
        } catch (Exception $e) {
            error_log('Error parseando BPMN XML: ' . $e->getMessage());
        }

        return $elements;
    }

    /**
     * Extrae las variables definidas en el proceso BPMN
     */
    public static function extractProcessVariables($bpmnXml)
    {
        $variables = array();

        try {
            $xml = @simplexml_load_string($bpmnXml);
            if ($xml === false) {
                return $variables;
            }

            $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
            $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');

            // Buscar propiedades del proceso (variables)
            $properties = $xml->xpath('//bpmn:property');

            foreach ($properties as $property) {
                $attrs = $property->attributes();
                $name = (string)$attrs->name;

                if (!empty($name)) {
                    $variables[] = array(
                        'id' => (string)$attrs->id,
                        'name' => $name,
                        'type' => 'string', // Por defecto
                        'value' => null
                    );
                }
            }

            // Buscar extensiones camunda para tipos de datos
            $formFields = $xml->xpath('//camunda:formField');
            foreach ($formFields as $field) {
                $attrs = $field->attributes('camunda', true);
                $id = (string)$attrs->id;
                $label = (string)$attrs->label;
                $type = (string)$attrs->type;

                if (!empty($id)) {
                    $variables[] = array(
                        'id' => $id,
                        'name' => $label ?: $id,
                        'type' => $type ?: 'string',
                        'value' => null
                    );
                }
            }
        } catch (Exception $e) {
            error_log('Error extrayendo variables: ' . $e->getMessage());
        }

        return $variables;
    }

    /**
     * Extrae los formularios asociados a las tareas
     */
    public static function extractTaskForms($bpmnXml)
    {
        $taskForms = array();

        try {
            $xml = @simplexml_load_string($bpmnXml);
            if ($xml === false) {
                return $taskForms;
            }

            $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
            $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');

            // Buscar user tasks con formularios
            $userTasks = $xml->xpath('//bpmn:userTask');

            foreach ($userTasks as $task) {
                $taskAttrs = $task->attributes();
                $taskId = (string)$taskAttrs->id;
                $taskName = (string)$taskAttrs->name;

                // Buscar extensionElements
                $extensionElements = $task->children('bpmn', true)->extensionElements;
                if ($extensionElements) {
                    $formData = $extensionElements->children('camunda', true)->formData;

                    if ($formData) {
                        $fields = array();

                        foreach ($formData->children('camunda', true)->formField as $field) {
                            $fieldAttrs = $field->attributes('camunda', true);

                            $fields[] = array(
                                'id' => (string)$fieldAttrs->id,
                                'label' => (string)$fieldAttrs->label,
                                'type' => (string)$fieldAttrs->type,
                                'defaultValue' => (string)$fieldAttrs->defaultValue,
                                'required' => ((string)$fieldAttrs->required === 'true')
                            );
                        }

                        if (count($fields) > 0) {
                            $taskForms[$taskId] = array(
                                'taskId' => $taskId,
                                'taskName' => $taskName,
                                'fields' => $fields
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Error extrayendo formularios: ' . $e->getMessage());
        }

        return $taskForms;
    }

    public static function isEndNode($nodeId, $processId)
    {
        $process = BpmnProcessPeer::retrieveByPK($processId);
        if (!$process) return false;

        $elements = self::parseProcessElements($process->getBpmnXml());

        foreach ($elements['endEvents'] as $endEvent) {
            if ($endEvent['id'] === $nodeId) {
                return true;
            }
        }

        return false;
    }
}
