<?php
/**
 * @javier.charry 
 * @copyright 2025
 * Clase extraer metadatos de los archivos
 */
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************
use \setasign\Fpdi\FpdfWatermark;
use \setasign\Fpdi\WaterMark;

class FileMetaInfo
{
    private $exiftool_cli_path = null;

    public function __construct() 
    {
        $read_sections = array('exiftool_cli_command_path');
        $ini_array = simad_util::readConfigFileApp($read_sections);
        $this->exiftool_cli_path = isset($ini_array['exiftool_cli_command_path']) ? $ini_array['exiftool_cli_command_path'] : 'exiftool';
    }

    function countPagesWithExifTool(string $filePath): ?int 
    {
        $cmd = "$this->exiftool_cli_path -s -s -s -PageCount -Pages " . escapeshellarg($filePath) . " 2>&1";
        $out = shell_exec($cmd);
        //**************************************************************************************
        if (!$out) return null;
        //**************************************************************************************
        // Busca primero PageCount, luego Pages
        if (preg_match('/(\d+)/', $out, $m)) {
            return (int)$m[1];
        }
        //**************************************************************************************
        return 1;
    }
}