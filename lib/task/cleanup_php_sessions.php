<?php

/**
 * ============================================================================
 * cleanup_php_sessions.php
 * ============================================================================
 * 
 * Limpieza de sesiones PHP almacenadas en SQL Server.
 * Ejecutar con Windows Task Scheduler cada hora.
 * 
 * Ejemplo de comando para Task Scheduler:
 *   php C:\inetpub\wwwroot\simad\batch\cleanup_php_sessions.php
 * 
 * ============================================================================
 */

// Cargar configuración de Symfony para obtener la conexión
require_once(dirname(__FILE__) . '/../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

try {
    $con = Propel::getConnection();
    
    // =========================================================================
    // 1. Sesiones expiradas (sin actividad por más de 2 horas)
    // =========================================================================
    $maxLifetime = 7200; // 2 horas
    $expireTime = time() - $maxLifetime;
    
    $stmt = $con->prepare("DELETE FROM PHP_SESSIONS WHERE SESSION_TIME < :time");
    $stmt->execute([':time' => $expireTime]);
    $deletedExpired = $stmt->rowCount();
    
    // =========================================================================
    // 2. Sesiones vacías (no autenticadas, más de 30 minutos)
    // =========================================================================
    $emptyTimeout = time() - 1800;
    
    $stmt = $con->prepare(
        "DELETE FROM PHP_SESSIONS 
         WHERE SESSION_DATA LIKE :pattern 
           AND SESSION_TIME < :time"
    );
    $stmt->execute([
        ':pattern' => '%authenticated|b:0%',
        ':time' => $emptyTimeout
    ]);
    $deletedEmpty = $stmt->rowCount();
    
    // =========================================================================
    // 3. Sesiones sin atributos (completamente vacías, más de 10 minutos)
    // =========================================================================
    $noAttrTimeout = time() - 600;
    
    $stmt = $con->prepare(
        "DELETE FROM PHP_SESSIONS 
         WHERE SESSION_DATA LIKE :pattern 
           AND SESSION_TIME < :time"
    );
    $stmt->execute([
        ':pattern' => '%attributes|a:0:%',
        ':time' => $noAttrTimeout
    ]);
    $deletedNoAttr = $stmt->rowCount();
    
    // =========================================================================
    // Resumen
    // =========================================================================
    $total = $deletedExpired + $deletedEmpty + $deletedNoAttr;
    $remaining = $con->query("SELECT COUNT(*) FROM PHP_SESSIONS")->fetchColumn();
    
    $msg = sprintf(
        "[%s] Limpieza PHP_SESSIONS: expiradas=%d, vacías=%d, sin_atributos=%d, total_eliminadas=%d, restantes=%d",
        date('Y-m-d H:i:s'),
        $deletedExpired,
        $deletedEmpty,
        $deletedNoAttr,
        $total,
        $remaining
    );
    
    echo $msg . PHP_EOL;
    
    // Log
    $logFile = sfConfig::get('sf_log_dir') . DIRECTORY_SEPARATOR . 'session_cleanup.log';
    file_put_contents($logFile, $msg . PHP_EOL, FILE_APPEND);
    
} catch (Exception $e) {
    $msg = sprintf("[%s] Error en limpieza: %s", date('Y-m-d H:i:s'), $e->getMessage());
    echo $msg . PHP_EOL;
    error_log($msg);
}