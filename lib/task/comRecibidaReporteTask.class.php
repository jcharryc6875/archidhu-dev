<?php

/**
 * Tarea de Symfony para consolidar métricas diarias
 * Ubicación: lib/task/comRecibidaReporteTask.class.php
 * 
 * Uso:
 *   php symfony recibida:com_recibida_reporte
 *   php symfony recibida:com_recibida_reporte --date=2025-12-15
 *   php symfony recibida:com_recibida_reporte --days=7
 * 
 * Cron recomendado (ejecutar a la 1:00 AM):
 *   0 1 * * * cd /path/to/project && php symfony recibida:com_recibida_reporte --env=prod
 */

class comRecibidaReporteTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
            new sfCommandOption('date', null, sfCommandOption::PARAMETER_OPTIONAL, 'Fecha específica del reporte (Y-m-d)'),
            new sfCommandOption('days', null, sfCommandOption::PARAMETER_OPTIONAL, 'Número de días hacia atrás a del reporte', 1),
        ]);

        $this->namespace = 'recibida';
        $this->name = 'reporte_status_gestion';
        $this->briefDescription = 'Genera reporte de com_recibida';
        $this->detailedDescription = <<<EOF
La tarea [recibida:reporte_status_gestion|INFO] genera el reporte de com_recibida.

Ejemplos de uso:

  [php symfony recibida:reporte_status_gestion|INFO]                    # Genera el reporte del día anterior
  [php symfony recibida:reporte_status_gestion --date=2025-12-15|INFO]  # Genera el reporte fecha específica
  [php symfony recibida:reporte_status_gestion --days=7|INFO]           # Genera el reporte últimos 7 días

EOF;
    }

    protected function execute($arguments = [], $options = [])
    {
        // Inicializar conexión a BD
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $this->logSection('recibida_reporte', 'Iniciando reporte de com_recibida..');

        // Determinar fechas a procesar
        $datesToProcess = [];

        if ($options['date']) {
            // Fecha específica
            $datesToProcess['start_date'] = $options['date'];
            $datesToProcess['end_date'] = $options['date'];
        } else {
            // Últimos N días
            $days = (int)$options['days'];
            $datesToProcess['start_date'] = date('Y-m-d', strtotime("-{$days} days"));
            $datesToProcess['end_date'] = date('Y-m-d', strtotime("-1 days"));
        }

        $this->logSection('recibida_reporte', "Procesando fecha: {$datesToProcess['start_date']} - {$datesToProcess['end_date']}");

        try {
            $response = EstadisticaPeer::generatePendingReport($datesToProcess['start_date'], $datesToProcess['end_date']);
            $count = count($response);
            $this->logSection('recibida_reporte', "  -> {$count} procesos realizados");
        } catch (Exception $e) {
            $this->logSection('error', "  -> Error: " . $e->getMessage(), null, 'ERROR');
        }

        $this->logSection('recibida_reporte', "Reporte completado");

        return 0;
    }
}
