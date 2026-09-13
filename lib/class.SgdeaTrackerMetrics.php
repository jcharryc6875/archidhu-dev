<?php

class SgdeaTrackerMetrics
{
    private $start;
    public $steps = [];
    private $context = [];
    private $log_path = "";
    private $log_name = "";

    public function __construct(array $context = [])
    {
        $this->start = microtime(true);
        $this->context = $context;

        if(isset($context['log_path'])){
            $this->log_path = $context['log_path'];
            if(is_dir($this->log_path)){
                simad_util::createPath($this->log_path);
            }else{
                $this->log_path = sfConfig::get('sf_log_dir');
            }
        }else{
            $this->log_path = sfConfig::get('sf_log_dir');
        }

        if(isset($context['log_name']) && !empty($context['log_name'])){
            $this->log_name = $context['log_name'];
        }else{
            $this->log_name = 'firma_digital_metrics.log';
        }
    }

    public function mark(string $stepName, array $metadata = [])
    {
        $time = microtime(true);
        $elapsed = $time - $this->start;
        $this->steps[$stepName] = [
            'timestamp' => $time,
            'elapsed' => $elapsed,
            'metadata' => $metadata,
        ];
    }

    public function finish()
    {
        $totalTime = microtime(true) - $this->start;
        $this->steps['total'] = ['elapsed' => $totalTime];

        // Registrar métricas (ej: archivo JSON, DB, syslog, etc.)
        $this->logMetrics();
    }

    private function logMetrics()
    {
        $logEntry = [
            'context' => $this->context,
            'steps' => $this->steps,
            'timestamp_iso' => date('c'),
        ];

        // Opción 1: archivo JSON rotativo (simple)
        error_log(json_encode($logEntry) . PHP_EOL, 3, $this->log_path.DIRECTORY_SEPARATOR.$this->log_name);

        // Opción 2: insertar en base de datos si tienes una tabla de métricas
        // Opción 3: enviar a un sistema de monitoreo (New Relic, Prometheus, etc.)
    }
}