<?php

class ExpedienteFolioManager
{
    private static $instance = null;
    
    private function __construct() {}
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Reordena los folios de una unidad documental
     * 
     * @param int $unidadDocumentalId
     * @return array
     */
    public function reordenarFolios($unidadDocumentalId)
    {
        try {
            $resultado = ContenidoUnidadDocumentalPeer::reordenarFoliosPorFecha($unidadDocumentalId);
            
            // Log de la operación si es necesario
            $this->logOperacion($unidadDocumentalId, 'reordenar', $resultado);
            
            return $resultado;
            
        } catch (Exception $e) {
            $this->logError($unidadDocumentalId, 'reordenar', $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Reordena múltiples unidades documentales
     * 
     * @param array $unidadesIds
     * @return array
     */
    public function reordenarMultiplesUnidades(array $unidadesIds)
    {
        $resultados = [];
        $errores = [];
        
        foreach ($unidadesIds as $unidadId) {
            try {
                $resultados[$unidadId] = ContenidoUnidadDocumentalPeer::reordenarFoliosPorFecha($unidadId);
            } catch (Exception $e) {
                $errores[$unidadId] = $e->getMessage();
            }
        }
        
        return [
            'success' => empty($errores),
            'procesados' => $resultados,
            'errores' => $errores
        ];
    }
    
    /**
     * Verifica si una unidad tiene folios desordenados
     * 
     * @param int $unidadDocumentalId
     * @return bool
     */
    public function tieneFoliosDesordenados($unidadDocumentalId)
    {
        $documentos = ContenidoUnidadDocumentalPeer::getDocumentosDesordenados($unidadDocumentalId);
        
        foreach ($documentos as $doc) {
            if ($doc['ESTADO_ORDEN'] === 'DESORDENADO') {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Log de operaciones exitosas
     * 
     * @param int $unidadId
     * @param string $operacion
     * @param array $resultado
     */
    private function logOperacion($unidadId, $operacion, $resultado)
    {
        $logMessage = sprintf(
            "[%s] Unidad %d: %s - Documentos: %d, Último folio: %d\n",
            date('Y-m-d H:i:s'),
            $unidadId,
            $operacion,
            $resultado['data']['documentos_procesados'] ?? 0,
            $resultado['data']['ultimo_folio_asignado'] ?? 0
        );
        
        // Guardar en archivo de log
        $logDir = sfConfig::get('sf_log_dir') . '/folios';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents(
            $logDir . '/operaciones.log',
            $logMessage,
            FILE_APPEND
        );
    }
    
    /**
     * Log de errores
     * 
     * @param int $unidadId
     * @param string $operacion
     * @param string $error
     */
    private function logError($unidadId, $operacion, $error)
    {
        $logMessage = sprintf(
            "[%s] ERROR Unidad %d: %s - %s\n",
            date('Y-m-d H:i:s'),
            $unidadId,
            $operacion,
            $error
        );
        
        $logDir = sfConfig::get('sf_log_dir') . '/folios';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents(
            $logDir . '/errores.log',
            $logMessage,
            FILE_APPEND
        );
    }
}