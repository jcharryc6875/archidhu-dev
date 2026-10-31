<?php

/**
 * Cache en archivo compartido entre aplicaciones para la configuracion de cuentas de notificacion,
 * tokens OAuth2 y estado de enfriamiento. Se ubica en cache/ de la raiz del proyecto (no en la
 * cache por aplicacion) para que invalidarla desde el backend aplique a todas las aplicaciones.
 */
class NotificacionMailCache
{
    private static $store = null;

    private static function store()
    {
        if (self::$store === null) {
            $root = sfConfig::get('sf_root_dir');
            $base = empty($root) ? sys_get_temp_dir() : $root . DIRECTORY_SEPARATOR . 'cache';
            self::$store = new sfFileCache(array('cache_dir' => $base . DIRECTORY_SEPARATOR . 'notif_mail'));
        }

        return self::$store;
    }

    /**
     * @return mixed valor guardado o $default si no existe o expiro
     */
    public static function get($key, $default = null)
    {
        try {
            $raw = self::store()->get($key);
            if ($raw === null || $raw === false || $raw === '') {
                return $default;
            }
            $decoded = json_decode($raw, true);

            return $decoded === null ? $default : $decoded;
        } catch (Throwable $e) {
            return $default;
        }
    }

    public static function set($key, $value, $ttlSeconds)
    {
        try {
            self::store()->set($key, json_encode($value), (int) $ttlSeconds);
        } catch (Throwable $e) {
            // La cache es una optimizacion: si falla se opera sin ella.
        }
    }

    public static function remove($key)
    {
        try {
            self::store()->remove($key);
        } catch (Throwable $e) {
        }
    }
}
