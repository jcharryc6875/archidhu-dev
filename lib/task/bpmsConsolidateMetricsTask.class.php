<?php

/**
 * Tarea de Symfony para consolidar métricas diarias
 * Ubicación: lib/task/bpmsConsolidateMetricsTask.class.php
 * 
 * Uso:
 *   php symfony bpms:consolidate-metrics
 *   php symfony bpms:consolidate-metrics --date=2025-12-15
 *   php symfony bpms:consolidate-metrics --days=7
 * 
 * Cron recomendado (ejecutar a la 1:00 AM):
 *   0 1 * * * cd /path/to/project && php symfony bpms:consolidate-metrics --env=prod
 */

class bpmsConsolidateMetricsTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
            new sfCommandOption('date', null, sfCommandOption::PARAMETER_OPTIONAL, 'Fecha específica a consolidar (Y-m-d)'),
            new sfCommandOption('days', null, sfCommandOption::PARAMETER_OPTIONAL, 'Número de días hacia atrás a consolidar', 1),
        ]);

        $this->namespace = 'bpms';
        $this->name = 'consolidate-metrics';
        $this->briefDescription = 'Consolida métricas diarias de procesos BPMS';
        $this->detailedDescription = <<<EOF
La tarea [bpms:consolidate-metrics|INFO] consolida las métricas de los procesos BPMS
en la tabla process_metrics para análisis histórico.

Ejemplos de uso:

  [php symfony bpms:consolidate-metrics|INFO]                    # Consolida el día anterior
  [php symfony bpms:consolidate-metrics --date=2025-12-15|INFO]  # Consolida fecha específica
  [php symfony bpms:consolidate-metrics --days=7|INFO]           # Consolida últimos 7 días

EOF;
    }

    protected function execute($arguments = [], $options = [])
    {
        // Inicializar conexión a BD
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // Cargar clase MetricsCalculator
        require_once sfConfig::get('sf_lib_dir') . '/workflow/MetricsCalculator.class.php';

        $this->logSection('bpms', 'Iniciando consolidación de métricas...');

        // Determinar fechas a procesar
        $datesToProcess = [];

        if ($options['date']) {
            // Fecha específica
            $datesToProcess[] = $options['date'];
        } else {
            // Últimos N días
            $days = (int)$options['days'];
            for ($i = 1; $i <= $days; $i++) {
                $datesToProcess[] = date('Y-m-d', strtotime("-{$i} days"));
            }
        }

        $totalConsolidated = 0;

        foreach ($datesToProcess as $date) {
            $this->logSection('bpms', "Procesando fecha: {$date}");

            try {
                $count = MetricsCalculator::consolidateDailyMetrics($date);
                $totalConsolidated += $count;
                $this->logSection('bpms', "  -> {$count} procesos consolidados");
            } catch (Exception $e) {
                $this->logSection('error', "  -> Error: " . $e->getMessage(), null, 'ERROR');
            }
        }

        $this->logSection('bpms', "Consolidación completada. Total: {$totalConsolidated} registros.");

        return 0;
    }
}
