import BpmnModeler from 'bpmn-js/lib/Modeler';

class BPMSApp {
    constructor() {
        this.modeler = new BpmnModeler({
            container: '#canvas',
            keyboard: { bindTo: window }
        });

        this.initializeEvents();
        this.loadEmptyDiagram();
    }

    initializeEvents() {
        document.getElementById('save-btn').addEventListener('click', () => {
            this.saveProcess();
        });

        document.getElementById('deploy-btn').addEventListener('click', () => {
            this.deployProcess();
        });
    }

    async saveProcess() {
        try {
            const { xml } = await this.modeler.saveXML({ format: true });

            const response = await fetch('/api/processes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `bpmn_xml=${encodeURIComponent(xml)}&name=${encodeURIComponent('Nuevo Proceso')}`
            });

            const result = await response.json();
            console.log('Proceso guardado:', result);

        } catch (error) {
            console.error('Error al guardar:', error);
        }
    }

    async deployProcess() {
        const processId = prompt('ID del proceso a desplegar:');
        if (!processId) return;

        const variables = prompt('Variables iniciales (JSON):') || '{}';

        try {
            const response = await fetch('/api/workflows', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `process_id=${processId}&variables=${encodeURIComponent(variables)}`
            });

            const result = await response.json();
            console.log('Workflow iniciado:', result);

        } catch (error) {
            console.error('Error al desplegar:', error);
        }
    }

    loadEmptyDiagram() {
        const emptyBpmn = `<?xml version="1.0" encoding="UTF-8"?>
        <bpmn2:definitions xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                          xmlns:bpmn2="http://www.omg.org/spec/BPMN/20100524/MODEL" 
                          xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" 
                          xmlns:dc="http://www.omg.org/spec/DD/20100524/DC" 
                          xsi:schemaLocation="http://www.omg.org/spec/BPMN/20100524/MODEL BPMN20.xsd" 
                          id="sample-diagram" 
                          targetNamespace="http://bpmn.io/schema/bpmn">
          <bpmn2:process id="Process_1" isExecutable="false">
            <bpmn2:startEvent id="StartEvent_1"/>
          </bpmn2:process>
          <bpmndi:BPMNDiagram id="BPMNDiagram_1">
            <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
              <bpmndi:BPMNShape id="_BPMNShape_StartEvent_2" bpmnElement="StartEvent_1">
                <dc:Bounds height="36.0" width="36.0" x="412.0" y="240.0"/>
              </bpmndi:BPMNShape>
            </bpmndi:BPMNPlane>
          </bpmndi:BPMNDiagram>
        </bpmn2:definitions>`;

        this.modeler.importXML(emptyBpmn);
    }
}

// Inicializar la aplicación
new BPMSApp();