<?php

/**
 * Tarea de Symfony para depurar automáticamente las versiones preliminares (borradores) de los
 * Actos Administrativos ya radicados, de acuerdo con el tiempo de retención configurado en la
 * pantalla de administración (ACTOADMIN_CONFIGURACION, ver actoadmin_etapa/configuracion).
 *
 * UARIV-202605 CA-3.4.2.
 *
 * Uso:
 *   php symfony actoadmin:purgar-versiones --env=prod
 *
 * Cron recomendado (ejecutar de madrugada, ej. 2:00 AM):
 *   0 2 * * * cd /path/to/project && php symfony actoadmin:purgar-versiones --env=prod
 *
 * Si el valor configurado de días de retención es 0, la tarea no borra nada (retención desactivada).
 */
class PurgarVersionesTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'comun'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
        ]);

        $this->namespace = 'actoadmin';
        $this->name = 'purgar-versiones';
        $this->briefDescription = 'Depura versiones preliminares de Actos Administrativos ya radicados';
        $this->detailedDescription = <<<EOF
La tarea [actoadmin:purgar-versiones|INFO] elimina las versiones preliminares (no actuales) de
DOCS_CONTROL_CAMBIO de los Actos Administrativos que ya fueron radicados, cuando su antigüedad
supera el número de días configurado en la pantalla de administración del flujo
(ACTOADMIN_CONFIGURACION.DIAS_RETENCION_BORRADOR). Si ese valor es 0, no se borra nada.

EOF;
    }

    protected function execute($arguments = [], $options = [])
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $databaseManager->getDatabase($options['connection']);

        $configuracion = ActoadminConfiguracionPeer::getConfiguracionActual();
        $dias_retencion = (int) $configuracion->getDiasRetencionBorrador();

        if ($dias_retencion <= 0) {
            $this->logSection('actoadmin', 'Retención desactivada (0 días configurados), no se depura nada.');
            return 0;
        }

        $fecha_limite = date('Y-m-d H:i:s', strtotime("-{$dias_retencion} days"));
        $this->logSection('actoadmin', "Depurando versiones preliminares anteriores a {$fecha_limite} ({$dias_retencion} días de retención)...");

        //*****************************************************************************************************
        $c = new Criteria();
        $c->add(ActoAdministrativoPeer::NUMERO_RESOLUCION, null, Criteria::ISNOTNULL);
        $c->addAnd(ActoAdministrativoPeer::NUMERO_RESOLUCION, '', Criteria::NOT_EQUAL);
        $c->clearSelectColumns();
        $c->addSelectColumn(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID);
        $stmt = ActoAdministrativoPeer::doSelectStmt($c);
        //*****************************************************************************************************
        $ids_radicados = array();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $ids_radicados[] = $row[0];
        }
        //*****************************************************************************************************
        if (empty($ids_radicados)) {
            $this->logSection('actoadmin', 'No hay actos administrativos radicados, no hay nada que depurar.');
            return 0;
        }

        $total_borrados = 0;
        foreach (array_chunk($ids_radicados, 500) as $chunk) {
            $cq = new Criteria();
            $cq->add(DocsControlCambioPeer::MODULO_ID, ModulesEnable::ActosAdministrativos);
            $cq->add(DocsControlCambioPeer::CURRENT_VERSION, 0);
            $cq->add(DocsControlCambioPeer::FECHA_CREACION, $fecha_limite, Criteria::LESS_THAN);
            $cq->add(DocsControlCambioPeer::CONSECUTIVO_ID, $chunk, Criteria::IN);
            $total_borrados += DocsControlCambioPeer::doDelete($cq);
        }
        //*****************************************************************************************************
        $this->logSection('actoadmin', "Depuración completada. Versiones preliminares eliminadas: {$total_borrados}.");

        return 0;
    }
}
