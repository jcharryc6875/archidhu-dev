<?php
class IpUtils
{
    /**
     * Obtiene la IP real del usuario considerando proxies
     */
    public static function getUserIp()
    {
        $ipKeys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    // Validar que sea una IP válida y no sea privada (opcional)
                    if (self::isValidIp($ip) && !self::isPrivateIp($ip)) {
                        return $ip;
                    }
                }
            }
        }

        // Si no encontramos una IP pública, devolvemos la primera disponible
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /**
     * Validar formato de IP
     */
    private static function isValidIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }

    /**
     * Verificar si es IP privada
     */
    private static function isPrivateIp($ip)
    {
        $privateRanges = array(
            '10.0.0.0|10.255.255.255',     // RFC1918
            '172.16.0.0|172.31.255.255',   // RFC1918
            '192.168.0.0|192.168.255.255', // RFC1918
            '127.0.0.0|127.255.255.255',   // Localhost
            '169.254.0.0|169.254.255.255', // Link-local
            '::1|::1',                     // IPv6 localhost
            'fc00::|fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' // IPv6 private
        );

        $ipLong = ip2long($ip);
        if ($ipLong !== false) {
            foreach ($privateRanges as $range) {
                list($rangeStart, $rangeEnd) = explode('|', $range);
                if (ip2long($ip) >= ip2long($rangeStart) && ip2long($ip) <= ip2long($rangeEnd)) {
                    return true;
                }
            }
        }

        // Para IPv6
        if (strpos($ip, ':') !== false) {
            if (preg_match('/^(::1|fe80::|fc00::)/', $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener todas las IPs posibles (para logging)
    */
    public static function getAllIps()
    {
        $ips = array();
        $ipKeys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ips[$key] = $_SERVER[$key];
            }
        }

        return $ips;
    }
}