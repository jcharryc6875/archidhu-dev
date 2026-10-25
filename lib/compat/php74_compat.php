<?php
/**
 * lib/compat/php74_compat.php
 *
 * Funciones de compatibilidad PHP 7.4 -> PHP 8.x para el código del proyecto.
 * Se carga desde config/ProjectConfiguration.class.php.
 */

if (!function_exists('count74')) {
    /**
     * count() con el comportamiento de PHP 7.4.
     *
     * En PHP 7.4, count() sobre un valor no contable emitía un warning y devolvía
     * 0 (null) o 1 (cualquier otro escalar u objeto no Countable). En PHP 8 lanza
     * TypeError. Esta función conserva exactamente el resultado de 7.4, sin el warning.
     *
     * @param mixed $value
     * @param int   $mode  COUNT_NORMAL | COUNT_RECURSIVE
     */
    function count74($value, int $mode = COUNT_NORMAL): int
    {
        if (is_array($value) || $value instanceof Countable) {
            return count($value, $mode);
        }

        return $value === null ? 0 : 1;
    }
}