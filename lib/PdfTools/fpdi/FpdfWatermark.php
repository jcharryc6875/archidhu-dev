<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

use setasign\Fpdi\FpdfRotate;

class FpdfWatermark extends FpdfRotate{
    protected $_outerText1;// dynamic text
    protected $_outerText2;

    function setWaterText($txt1="", $txt2=""){
        $this->_outerText1 = $txt1;
        $this->_outerText2 = $txt2;
    }

    function Header(){
        //Put the watermark
        $this->SetFont('Arial', 'I', 35);
        $this->SetTextColor(255,194,194);
        //$this->SetAlpha(0.9);
        $this->RotatedText(35,190, $this->_outerText1, 45);
        $this->RotatedText(75,190, $this->_outerText2, 45);
    }

    function RotatedText($x, $y, $txt, $angle){
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }
}

?>
