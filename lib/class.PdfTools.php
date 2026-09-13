<?php
/**
 * @javier.charry 
 * @copyright 2022
 * Clase para manipular archivos pdf
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

class PdfTools
{
    public function PdfTools()
    {

    }

    public function setPdfWatherMark($pathsource,$text1,$text2="",$inLine = true)
    {
        if($inLine)
            return $this->setPdfWatherMarkInline($pathsource,$text1,$text2);
        else
            return $this->setPdfWatherMarkRotate($pathsource,$text1,$text2);
    }

    public function setPdfWatherMarkRotate($pathsource,$text1,$text2="")
    {
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdf/fpdf.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/autoload.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/PDF-Parser-1.5/autoload.php');
        //**********************************************************************************************
        try {
            $finfo = pathinfo($pathsource);
            
            $pdf = new FpdfWatermark();

            if (file_exists($pathsource)){
                $pagecount = $pdf->setSourceFile($pathsource);
            } else {
                return FALSE;
            }

            $pdf->setWaterText($text1, $text2);

            /* loop for multipage pdf */
            for($i=1; $i <= $pagecount; $i++) { 
                $tpl = $pdf->importPage($i);               
                $pdf->addPage(); 
                //$pdf->useTemplate($tpl, 1, 1, 0, 0, TRUE);
                $pdf->useTemplate($tpl, null, null, null, null, true);
            }

            $pathtarget = $finfo['dirname'] . DIRECTORY_SEPARATOR . md5($finfo['dirname'].time()).'.'.$finfo['extension'];
            $pdf->Output('F',$pathtarget);

            if (file_exists($pathtarget)){
                return basename($pathtarget);
            } else {
                return null;
            }

        } catch (\Throwable $th) {
            return null;
        }        
    }

    public function setPdfWatherMarkInline($pathsource, $text1 = "", $text2 = "")
    {
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdf/fpdf.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/autoload.php');
        require_once(sfConfig::get('sf_lib_dir').'/PdfTools/fpdi/PDF-Parser-1.5/autoload.php');
        //**********************************************************************************************
        try {
            $finfo = pathinfo($pathsource);

            $file_watermark = md5(basename($pathsource).time()).'.'.$finfo['extension'];
            $output0 = $finfo['dirname'] . DIRECTORY_SEPARATOR . $file_watermark;
            WaterMark::applyAndSpit($pathsource,$output0,$text1,$text2);
            return $file_watermark;
        } catch (\Throwable $th) {
            return null;
        }        
    }
}
?>