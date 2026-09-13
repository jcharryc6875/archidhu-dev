<?php
	//archivo generador del codigo 
   	define('IN_CB',true);
	require('FColor.php');
	require('BarCode.php');
	if(include('code128.barcode.php')){
		$color_black = new FColor(0,0,0);
		$color_white = new FColor(255,255,255);
		$text=$_GET['texto'];
		$t=$_GET['descripcion'];
		
		$filename=$text .".png";
		$maxHeight=25;
		$res=1;
		$start="B";
		$textfont=3;
		//$maxHeight, $color1, $color2,$res,$text,$textfont,$start='B'
		$code_generated = new code128($maxHeight,$color_black,$color_white,$res,$text,$textfont,$start);
		//var_dump($code_generated);
		//$im = @imagecreate(128, 120) or die("Cannot Initialize new GD image stream");
		$im = imagecreatefrompng("error.png");
		$code_generated->draw($im);
		$bc = imagecolorallocate($im, 0, 255, 255);
		$tc = imagecolorallocate($im, 0, 0, 0);
		imagestring($im, 1, 4, 40,  $t, $tc);
		//escribe la imagen
		header('Content: Imagenes Cod\image/png');
		imagepng($im,$filename);
		//muestra la imagen.
		imagepng($im);
	}//if
?>
