<?php

/**
 * Informa por correo las fallas de envio consolidadas y purga la auditoria antigua.
 * Programar dos veces al dia (taskNotificacionFallas.cmd):
 *   php symfony notificacion:reporte_fallas --env=prod
 */
class notificacionFallasTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
        ));

        $this->namespace = 'notificacion';
        $this->name = 'reporte_fallas';
        $this->briefDescription = 'Informa las fallas de envio de correo consolidadas';
        $this->detailedDescription = <<<EOF
La tarea [notificacion:reporte_fallas|INFO] envia un correo con las fallas de envio aun no informadas
(si notif_mail_fail_report_enable esta activo en config/app.ini) y purga la auditoria de envios antigua.

  [php symfony notificacion:reporte_fallas --env=prod|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $databaseManager->getDatabase($options['connection'])->getConnection();

        $resumen = NotificacionFallasReporter::ejecutar();
        $this->logSection('notificacion', 'Reporte: ' . $resumen['reporte'] . ' - ' . $resumen['mensaje']);
        $this->logSection('notificacion', 'Fallas informadas: ' . $resumen['fallas'] . ', registros purgados: ' . $resumen['purgados']);
    }
}
