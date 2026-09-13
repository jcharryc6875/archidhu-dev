<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */


namespace setasign\Fpdi;

class WaterMark extends FPDI
{
    public $pdf, $file, $newFile,
        $wmText = "DOCUMENTO NO VALIDO";

    /** $file and $newFile have to include the full path. */
    public function __construct($file, $newFile)
    {
        $this->pdf = new FPDI();
        $this->file = $file;
        $this->newFile = $newFile;
    }

    /** $file and $newFile have to include the full path. */
    public static function applyAndSpit($file, $newFile,$text1,$text2)
    {
        $wm = new WaterMark($file, $newFile);
        $wm->wmText = $text1;

        if($wm->isWaterMarked())
            return $wm->spitWaterMarked();
        else{
            $wm->doWaterMark();
            return $wm->spitWaterMarked();
        }
    }

    /** @todo Make the text nicer and add to all pages */
    /** @todo Make the text nicer and add to all pages */
    public function doWaterMark()
    {
        $currentFile = $this->file;
        $newFile = $this->newFile;
        
        $pagecount = $this->pdf->setSourceFile($currentFile);

        for($i = 1; $i <= $pagecount; $i++){
            $this->pdf->AddPage();
            $tplidx = $this->pdf->importPage($i);
            $size_unit = $this->pdf->getTemplateSize($tplidx);            
            $this->pdf->useTemplate($tplidx, 0, 0, $size_unit['width'], $size_unit['height']);
            // now write some text above the imported page
            $this->pdf->SetFont('Arial', 'I', 35);
            $this->pdf->SetTextColor(255,194,194);
            $this->pdf->SetXY(50, 200);
            $this->_rotate(55);
            $this->pdf->Write(0, $this->wmText);
            $this->_rotate(0);
        }
        
        $this->pdf->Output('F',$newFile);
    }

    public function isWaterMarked()
    {
        return (file_exists($this->newFile));
    }

    public function spitWaterMarked()
    {
        //return readfile($this->newFile);//para mostrar en en navegador con header
        return ($this->newFile);
    }

    protected function _rotate($angle,$x=-1,$y=-1) {
        if($x==-1)
            $x=$this->pdf->x;
        if($y==-1)
            $y=$this->pdf->y;
        if($this->pdf->angle!=0)
            $this->pdf->_out('Q');
        $this->pdf->angle=$angle;

        if($angle!=0){
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->pdf->k;
            $cy=($this->pdf->h-$y)*$this->pdf->k;

            $this->pdf->_out(sprintf(
                'q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',
                $c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
}