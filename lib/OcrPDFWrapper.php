<?php
// Excepción personalizada para errores de VeraPDF
class OcrPDFException extends Exception {}

require 'lib/smalot/pdfparser/autoload.php';// Asegúrate de instalar smalot/pdfparser via Composer
use Smalot\PdfParser\Parser;

class OcrPDFWrapper
{
    // Constructor: recibe la ruta al ejecutable de VeraPDF
    public function __construct()
    {
    }

    /**
    * OcrPDFWrapper::has_selectable_text()
    * Buscar texto generado por ocr en un archivo pdf
    * @param string $pdf_path ruta absoluta del archivo
    * @return bool true|false
    */ 
    public function has_selectable_text($pdf_path) 
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdf_path);
            $text = $pdf->getText();
            return !empty(trim($text));
        } catch (\Exception $th) {
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}