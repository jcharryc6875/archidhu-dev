<?php 
function getBarcode($text,$t,$home_dir="./")
{
	define('IN_CB',true);
	$path="";	
	//*********************************************************************************
	require_once('FColor.php');
	require_once('BarCode.php');
    //*********************************************************************************
	if(include_once('code128.barcode.php')){
		$color_black = new FColor(0,0,0);
		$color_white = new FColor(255,255,255);
		$barcode_dir = $home_dir . "/tmp/";
		if ( file_exists($barcode_dir)==FALSE) {
			@mkdir($barcode_dir, 0700);
			echo "creating $barcode_dir";
		}
		$filename= $text .".png";
		
		$maxHeight=55;
		$res=2;
		$start="B";
		$textfont=4;
		//$maxHeight, $color1, $color2,$res,$text,$textfont,$start='B'
		$code_generated = new code128($maxHeight,$color_black,$color_white,$res,$text,$textfont,$start);
		//var_dump($code_generated);
		//$im = @imagecreate(128, 120) or die("Cannot Initialize new GD image stream");        
		$im = imagecreatefrompng($barcode_dir."error_archivo.png");
		$code_generated->draw($im);
		//header('Content: Imagenes Cod\image/png');
		$bc = imagecolorallocate($im, 0, 255, 255);
		$tc = imagecolorallocate($im, 0, 0, 0);
		imagestring($im, 1, 40, 40,  $t, $tc);
		//echo "Generada.";
		$full_path=$barcode_dir . $filename;
		imagepng($im,$full_path);
		//imagepng($im);
		return $filename;
	}//if
}//getBarcode

/*uso de la funcion:
getBarcode( $cadena ,$descripcion);
$nombre_imagen=$cadena.".png";
echo "<img src=\"<?php echo $nombre_imagen; ?>\" />";
*/

?>


