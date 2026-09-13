<?php
require_once(dirname(__FILE__).'/../EmailTools/OAuth2Providers.php');
require_once(dirname(__FILE__).'/../EmailTools/providers/EmailCoreProvider.php');
require_once(dirname(__FILE__).'/../EmailTools/EmailSyncManager.php');

require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', true);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
ini_set('memory_limit', '2048M');//2GB RAM
set_time_limit(1800); // 30 minutos

$syncManager = new EmailSyncManager();
$startTime = time();

try {
    $syncManager->syncAllAccounts();
} catch (Exception $e) {
    error_log("Error en sincronización: " . $e->getMessage());
}

?>