<?php 

define('IN_CB',true);

require_once('class/BCGFontFile.php');
require_once('class/BCGColor.php');
require_once('class/BCGDrawing.php');
require_once('class/BCGcode128.barcode.php');

function GenerateCode($filename="",$text="sin texto",$dir_font,$scale=2,$height=25,$size_font=14,$dpi=72,$clearlabel=false)
{
    // The arguments are R, G, and B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $font = new BCGFontFile($dir_font, $size_font);
    
    $drawException = null;
    try {
        $code = new BCGcode128(); // Or another class name from the manual
        $code->setScale($scale); // Resolution
        $code->setThickness($height); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
        if($clearlabel)
            $code->clearLabels();// eliminar labels
    } catch(Exception $exception) {
	   $drawException = $exception;
    }
    
    $drawing = new BCGDrawing($filename, $color_white);
    
    if($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        //$drawing->setRotationAngle($_GET['rotation']);
        $drawing->setDPI($dpi);
        $drawing->draw();
    }
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}
?>