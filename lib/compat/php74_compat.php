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

if (!function_exists('implode74')) {
    /**
     * implode() / join() con el comportamiento de PHP 7.4.
     *
     * PHP 8 lanza TypeError cuando el arreglo no es un arreglo (por ejemplo null) y cuando
     * los argumentos van en el orden antiguo implode($arreglo, $separador). PHP 7.4:
     *   - implode($arreglo)                -> une sin separador
     *   - implode($separador, $arreglo)    -> forma normal
     *   - implode($arreglo, $separador)    -> orden antiguo, aceptado
     *   - cualquier otro caso              -> warning y null
     * Aquí ese último caso devuelve '' en lugar de null: al imprimir o concatenar el
     * resultado es el mismo, y evita avisos de "null" en funciones que esperan texto.
     *
     * @param mixed $a
     * @param mixed $b
     */
    function implode74($a, $b = null): string
    {
        if (func_num_args() < 2) {
            return is_array($a) ? implode($a) : '';
        }
        if (is_array($b)) {
            return implode(is_array($a) ? '' : (string) $a, $b);
        }
        if (is_array($a)) {
            return implode((string) $b, $a);
        }

        return '';
    }
}

if (!function_exists('number_format74')) {
    /**
     * number_format() con el comportamiento de PHP 7.4.
     *
     * PHP 8 lanza TypeError cuando el número es un texto no numérico ("", "abc").
     * PHP 7.4:
     *   - texto numérico ("1500", ".50", " 12")  -> se formatea
     *   - texto con número inicial ("12abc")      -> notice y usa 12
     *   - null / booleano                          -> 0 / 1
     *   - texto no numérico, arreglo u objeto      -> warning y null
     * Aquí ese último caso devuelve '' en lugar de null (mismo resultado al imprimir).
     *
     * @param mixed $num
     */
    function number_format74($num, $decimals = 0, $dec_point = '.', $thousands_sep = ','): string
    {
        if (is_string($num)) {
            if (!is_numeric($num)) {
                if (!preg_match('/^\s*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?/', $num, $m)) {
                    return '';
                }
                $num = $m[0];
            }
            $num = (float) $num;
        } elseif ($num === null || is_bool($num)) {
            $num = (float) $num;
        } elseif (!is_int($num) && !is_float($num)) {
            return '';
        }

        return number_format($num, (int) $decimals, (string) $dec_point, (string) $thousands_sep);
    }
}