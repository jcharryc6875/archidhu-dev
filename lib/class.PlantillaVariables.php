<?php

/**
 * Catálogo de variables (etiquetas {[CLAVE]}) válidas en las plantillas Word.
 * La fuente es config/plantilla_variables.json; el valor de cada clave se calcula
 * en el modelo de cada módulo al generar el documento.
 */
class PlantillaVariables
{
    /** @var array|null */
    private static $catalogo = null;

    /** @var string|null */
    private static $rutaFuente = null;

    /**
     * Cambia el archivo fuente y descarta la caché (pruebas o ambientes con otra ruta).
     */
    public static function setRutaFuente(?string $ruta): void
    {
        self::$rutaFuente = $ruta;
        self::$catalogo = null;
    }

    public static function getRutaFuente(): string
    {
        if (self::$rutaFuente !== null) {
            return self::$rutaFuente;
        }

        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'plantilla_variables.json';
    }

    /**
     * Variables disponibles para un módulo (ModulesEnable::*), en el orden del JSON.
     *
     * @return array[] cada una con clave, grupo, descripcion, tipo, modulos y repetible
     */
    public static function getByModulo($moduloId): array
    {
        $moduloId = (int) $moduloId;
        $lista = array();
        foreach (self::cargar()['variables'] as $variable) {
            if (in_array($moduloId, $variable['modulos'], true)) {
                $lista[] = $variable;
            }
        }

        return $lista;
    }

    /**
     * Variables del módulo agrupadas por su grupo, para pintar el panel de ayuda.
     *
     * @return array<string, array[]>
     */
    public static function getAgrupadasByModulo($moduloId): array
    {
        $grupos = array();
        foreach (self::getByModulo($moduloId) as $variable) {
            $grupos[$variable['grupo']][] = $variable;
        }

        return $grupos;
    }

    public static function getClaves($moduloId): array
    {
        return array_column(self::getByModulo($moduloId), 'clave');
    }

    public static function isValid($clave, $moduloId): bool
    {
        return in_array(trim((string) $clave), self::getClaves($moduloId), true);
    }

    /**
     * Devuelve las etiquetas que no existen para el módulo, sin repetidos y respetando el orden.
     * Acepta el sufijo "#n" que PHPWord añade al duplicar bloques o filas.
     */
    public static function getDesconocidas(array $etiquetas, $moduloId): array
    {
        $validas = self::getClaves($moduloId);
        $desconocidas = array();
        foreach ($etiquetas as $etiqueta) {
            $clave = preg_replace('/#\d+$/', '', trim((string) $etiqueta));
            if ($clave !== '' && !in_array($clave, $validas, true) && !in_array($clave, $desconocidas, true)) {
                $desconocidas[] = $clave;
            }
        }

        return $desconocidas;
    }

    private static function cargar(): array
    {
        if (self::$catalogo !== null) {
            return self::$catalogo;
        }

        $ruta = self::getRutaFuente();
        if (!is_file($ruta) || !is_readable($ruta)) {
            throw new RuntimeException('No se encontró el catálogo de variables de plantillas: ' . $ruta);
        }

        $datos = json_decode((string) file_get_contents($ruta), true);
        if (!is_array($datos) || !isset($datos['variables']) || !is_array($datos['variables'])) {
            throw new RuntimeException('El catálogo de variables de plantillas no es un JSON válido: ' . json_last_error_msg());
        }

        $variables = array();
        foreach ($datos['variables'] as $i => $variable) {
            if (!is_array($variable) || empty($variable['clave']) || empty($variable['modulos']) || !is_array($variable['modulos'])) {
                throw new RuntimeException('Variable inválida en la posición ' . $i . ' del catálogo de plantillas.');
            }
            $variables[] = array(
                'clave' => (string) $variable['clave'],
                'grupo' => isset($variable['grupo']) ? (string) $variable['grupo'] : 'General',
                'descripcion' => isset($variable['descripcion']) ? (string) $variable['descripcion'] : '',
                'tipo' => isset($variable['tipo']) ? (string) $variable['tipo'] : 'texto',
                'modulos' => array_map('intval', $variable['modulos']),
                'repetible' => !empty($variable['repetible']),
            );
        }

        self::$catalogo = array('variables' => $variables);

        return self::$catalogo;
    }
}
