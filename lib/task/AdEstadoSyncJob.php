<?php
require_once(dirname(__FILE__).'/../AdEstadoSyncManager.php');

require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', true);
sfContext::createInstance($configuration);

$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
ini_set('memory_limit', '512M');
set_time_limit(1800); // 30 minutos

$syncManager = new AdEstadoSyncManager();

try {
    $syncManager->sincronizarEstados();
} catch (Exception $e) {
    error_log('Error en sincronizacion de estados AD: '.$e->getMessage());
}

?>
