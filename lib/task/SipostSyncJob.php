<?php
require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
try {
    $sipost_api = new WsApiSipost();
    $sipost_api->searchRegCertiCorreoFisico();
} catch (Exception $e) {
    error_log("Error en sincronización: " . $e->getMessage());
}
?>