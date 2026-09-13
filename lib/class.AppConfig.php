<?php
class AppConfig
{
    private static $cache = array();
    private static $cacheTime = array();
    private static $cacheTimeout = 60; // Cache de 1 minuto

    /**
     * Obtiene un valor de configuración
     */
    public static function get($key, $default = null)
    {
        $now = time();
        
        // Verificar cache
        if (isset(self::$cache[$key]) && 
            isset(self::$cacheTime[$key]) && 
            ($now - self::$cacheTime[$key]) < self::$cacheTimeout) {
            return self::$cache[$key];
        }

        try {
            $config = ParametroPeer::retrieveByPK(80);
            $value = $config ? $config->getValorNumerico() : $default;
            
            // Guardar en cache
            self::$cache[$key] = $value;
            self::$cacheTime[$key] = $now;
            
            return $value;
        } catch (Exception $e) {
            error_log("Error obteniendo configuración {$key}: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Limpia el cache
     */
    public static function clearCache()
    {
        self::$cache = array();
        self::$cacheTime = array();
    }
}