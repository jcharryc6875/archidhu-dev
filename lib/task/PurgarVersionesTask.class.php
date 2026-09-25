<?php

/**
 * Tarea de Symfony para depurar automáticamente las versiones preliminares (borradores, tanto de
 * contenido como del archivo Word) de los Actos Administrativos ya radicados, de acuerdo con el
 * tiempo de retención configurado en la pantalla de administración (ACTOADMIN_CONFIGURACION, ver
 * actoadmin_etapa/configuracion).
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
 *
 * Cada eliminación queda registrada en la bitácora del flujo del acto administrativo afectado
 * (ACTOADMIN_ETAPA_BITACORA, acción PURGA_VERSIONES), visible en la pestaña "Bitácora del Flujo".
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
DOCS_CONTROL_CAMBIO (contenido) y de ACTOADMIN_WORD_VERSION (archivo Word, incluyendo el .docx en
disco) de los Actos Administrativos que ya fueron radicados, cuando su antigüedad supera el número
de días configurado en la pantalla de administración del flujo
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

        // Etapa/rol usados para dejar la depuración automática registrada en la bitácora del flujo
        // (a pedido del cliente: las eliminaciones programadas de versiones preliminares deben quedar
        // registradas en el flujo de aprobación o en el registro del acto administrativo). Se reutiliza
        // la etapa activa configurada para el rol "Proyectó" (rol 1), el mismo criterio ya usado para la
        // bitácora de Creación: si no hay ninguna etapa activa para ese rol, la depuración se sigue
        // ejecutando pero no queda bitácora (no se fuerza un ACTOADMINETAPA_ID falso solo para insertar).
        $etapa_sistema = ActoadminEtapaPeer::getEtapaByRol(1);
        $usuario_sistema_id = UsuarioPendientesChecker::USUARIO_SISTEMA_INTEGRACION_ID;

        $total_borrados_contenido = 0;
        $total_borrados_word = 0;
        $total_actos_afectados = 0;
        foreach ($ids_radicados as $actoadministrativo_id) {
            $acto_administrativo = ActoAdministrativoPeer::retrieveByPk($actoadministrativo_id);
            if ($acto_administrativo == null) {
                continue;
            }
            //*************************************************************************************************
            // Versiones preliminares del contenido (HTML, DOCS_CONTROL_CAMBIO)
            $c_count = new Criteria();
            $c_count->add(DocsControlCambioPeer::MODULO_ID, ModulesEnable::ActosAdministrativos);
            $c_count->add(DocsControlCambioPeer::CURRENT_VERSION, 0);
            $c_count->add(DocsControlCambioPeer::FECHA_CREACION, $fecha_limite, Criteria::LESS_THAN);
            $c_count->add(DocsControlCambioPeer::CONSECUTIVO_ID, $actoadministrativo_id);
            $borrados_contenido = 0;
            if (DocsControlCambioPeer::doCount($c_count) > 0) {
                $c_delete = new Criteria();
                $c_delete->add(DocsControlCambioPeer::MODULO_ID, ModulesEnable::ActosAdministrativos);
                $c_delete->add(DocsControlCambioPeer::CURRENT_VERSION, 0);
                $c_delete->add(DocsControlCambioPeer::FECHA_CREACION, $fecha_limite, Criteria::LESS_THAN);
                $c_delete->add(DocsControlCambioPeer::CONSECUTIVO_ID, $actoadministrativo_id);
                $borrados_contenido = DocsControlCambioPeer::doDelete($c_delete);
            }
            //*************************************************************************************************
            // Versiones preliminares del archivo Word (ACTOADMIN_WORD_VERSION), archivo físico incluido
            $borrados_word = ActoadminWordVersionPeer::purgarPreliminares($actoadministrativo_id, $fecha_limite, $acto_administrativo->getUrlFileWord());
            //*************************************************************************************************
            if ($borrados_contenido <= 0 && $borrados_word <= 0) {
                continue;
            }
            $total_borrados_contenido += $borrados_contenido;
            $total_borrados_word += $borrados_word;
            $total_actos_afectados++;
            //*************************************************************************************************
            if ($etapa_sistema != null) {
                $partes = array();
                if ($borrados_contenido > 0) {
                    $partes[] = "{$borrados_contenido} versión(es) preliminar(es) de contenido";
                }
                if ($borrados_word > 0) {
                    $partes[] = "{$borrados_word} versión(es) de Word";
                }
                ActoadminEtapaBitacoraPeer::addBitacora(
                    $actoadministrativo_id,
                    $etapa_sistema->getPrimaryKey(),
                    $usuario_sistema_id,
                    1,
                    $acto_administrativo->getEstadoactoadministrativoId(),
                    ActoadminEtapaBitacoraPeer::ACCION_PURGA_VERSIONES,
                    'Depuración automática: se eliminaron ' . implode(' y ', $partes) . " por política de retención de {$dias_retencion} días."
                );
            }
        }
        //*****************************************************************************************************
        $this->logSection('actoadmin', "Depuración completada. Versiones de contenido eliminadas: {$total_borrados_contenido}. Versiones de Word eliminadas: {$total_borrados_word}. Actos afectados: {$total_actos_afectados}.");
        if ($etapa_sistema == null) {
            $this->logSection('actoadmin', 'Aviso: no hay una etapa activa configurada para el rol "Proyectó" (rol 1); no quedó bitácora del flujo para esta depuración.');
        }

        return 0;
    }
}
