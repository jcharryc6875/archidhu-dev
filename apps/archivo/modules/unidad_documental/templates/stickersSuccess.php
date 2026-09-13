<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

//require_once(sfConfig::get('sf_lib_dir')."/barcodeArchivo/applib_barcode.php");
require_once("lib/BarcodeGenerator/generate_barcode.php");
require_once(sfConfig::get('sf_lib_dir')."/phpqrcode/qrlib.php");

$font_dir = sfConfig::get('sf_lib_dir') .DIRECTORY_SEPARATOR."BarcodeGenerator".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."font".DIRECTORY_SEPARATOR."Arial.ttf";

$path_tmp = sfConfig::get('sf_web_dir').'/tmp/';
$webcodeurl = $base_path.'/tmp/'
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0014)about:internet -->
<html>
	<head>
		<title></title>
		<meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=utf-8'/>
		<style type="text/css">
			.csE75D3AE5 {color:#000000;background-color:transparent;border-left:#000000 1px solid;border-top:#000000 1px solid;border-right-style: none;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csC3BBD80E {color:#000000;background-color:transparent;border-left:#000000 1px solid;border-top-style: none;border-right-style: none;border-bottom:#000000 1px solid;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csD2198692 {color:#000000;background-color:transparent;border-left:#000000 1px solid;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csE33A3B23 {color:#000000;background-color:transparent;border-left-style: none;border-top:#000000 1px solid;border-right:#000000 1px solid;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.cs140EE778 {color:#000000;background-color:transparent;border-left-style: none;border-top:#000000 1px solid;border-right-style: none;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csA4A4F90C {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right:#000000 1px solid;border-bottom:#000000 1px solid;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.cs914D1A68 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right:#000000 1px solid;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csDD500977 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom:#000000 1px solid;font-family:Arial; font-size:8px; font-weight:normal; font-style:normal; }
			.cs7384E3C7 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom:#000000 1px solid;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.cs475035AB {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial Narrow; font-size:11px; font-weight:bold; font-style:normal; }
			.csB739ED8B {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial; font-size:21px; font-weight:normal; font-style:normal; }
			.csB0554C01 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial; font-size:8px; font-weight:normal; font-style:normal; }
			.csF1013A5 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial; font-size:8px; font-weight:normal; font-style:normal; padding-left:5px;}
			.cs9409BF4D {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial; font-size:8px; font-weight:normal; font-style:normal; padding-top:2px;padding-left:2px;padding-right:2px;padding-bottom:2px;}
			.cs889FFBD2 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Arial; font-size:9px; font-weight:bold; font-style:normal; }
			.cs101A94F7 {color:#000000;background-color:transparent;border-left-style: none;border-top-style: none;border-right-style: none;border-bottom-style: none;font-family:Times New Roman; font-size:13px; font-weight:normal; font-style:normal; }
			.csF7D3565D {height:0px;width:0px;overflow:hidden;font-size:0px;line-height:0px;}
		</style>
	</head>

	<?php for($a = 0; $a < count($sticker_final); $a++){ ?>
		<?php
			$qrfilename = 'qr'.$sticker_final[$a]['pkId'].'.png';
			$qrfullpath = $path_tmp.$qrfilename;
			$textqrcode = $sticker_final[$a]['numero_identificacion'] ? sprintf("%s / %s",$sticker_final[$a]['numero_identificacion'],$sticker_final[$a]['titulo']) : $sticker_final[$a]['titulo'];
			$isCreateQr = QRcode::png($textqrcode,$qrfullpath);
			$qrwebname = $webcodeurl.$qrfilename;			
			$textcode128 = $sticker_final[$a]['ubicacion'];			
			
			if($textcode128){
				$codebarname = 'c128'.$sticker_final[$a]['pkId'].'.png';
				$filepath = $path_tmp.$codebarname;
				GenerateCode($filepath,$textcode128,$font_dir,1,25,9);
				$codebarwebname = $webcodeurl.$codebarname;
			}
		
		?>

		<table cellpadding="0" cellspacing="0" border="0" style="border-width:0px;empty-cells:show;width:384px;">
			<tr style="vertical-align:top;">
				<td style="width:0px;height:3px;"></td>
				<td class="csE75D3AE5" style="width:0px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:3px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" colspan="3" style="width:103px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:13px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:2px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:137px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:7px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:107px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs140EE778" style="width:8px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="csE33A3B23" style="width:2px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:22px;"></td>
				<td class="csD2198692" rowspan="3" style="width:0px;height:44px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" rowspan="3" style="width:3px;height:44px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" colspan="4" rowspan="2" style="width:116px;height:60px;text-align:center;vertical-align:middle;">
					<div style="overflow:hidden;width:116px;height:60px;">
						<img alt="" src="<?php echo $sticker_final[$a]['logo_corporativo']; ?>" style="width:60px;height:60px;margin-top:0px;" />
					</div>
				</td>
				<td class="cs101A94F7" rowspan="3" style="width:2px;height:44px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs889FFBD2" colspan="4" style="width:259px;height:22px;line-height:12px;text-align:center;vertical-align:middle;"><nobr>FORMATO&nbsp;ROTULO&nbsp;DE&nbsp;CARPETA</nobr></td>
				<td class="cs914D1A68" rowspan="3" style="width:2px;height:44px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:25px;"></td>
				<td class="cs889FFBD2" colspan="4" rowspan="2" style="width:259px;height:22px;line-height:12px;text-align:center;vertical-align:middle;"><nobr>PROCESO&nbsp;DE&nbsp;GESTI&#211;N&nbsp;DOCUMENTAL</nobr></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:5px;"></td>
				<td class="cs101A94F7" colspan="4" style="width:116px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:2px;"></td>
				<td class="csD2198692" style="width:0px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" colspan="3" style="width:103px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:13px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:2px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:137px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:7px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:107px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:8px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:29px;"></td>
				<td class="csD2198692" style="width:0px;height:29px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:29px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" style="width:80px;height:25px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>DEPENDENCIA</nobr><br/><nobr>SECCI&#211;N</nobr></td>
				<td class="csDD500977" colspan="8" style="width:293px;height:28px;line-height:11px;text-align:center;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['dependencia']; ?></nobr></td>
				<td class="cs914D1A68" style="width:2px;height:29px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:20px;"></td>
				<td class="csD2198692" style="width:0px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" style="width:80px;height:20px;line-height:11px;text-align:left;vertical-align:middle;">OFICINA PRODUCTORA</td>
				<td class="csB0554C01" colspan="8" style="width:293px;height:20px;line-height:11px;text-align:center;vertical-align:middle;"><nobr><?php echo "&nbsp;" ?></nobr></td>
				<td class="cs914D1A68" style="width:2px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:20px;"></td>
				<td class="csD2198692" style="width:0px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" style="width:80px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>SERIE</nobr></td>
				<td class="csB0554C01" colspan="8" style="width:293px;height:20px;line-height:11px;text-align:center;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['serie']; ?></nobr></td>
				<td class="cs914D1A68" style="width:2px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:20px;"></td>
				<td class="csD2198692" style="width:0px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" style="width:80px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>SUBSERIE</nobr></td>
				<td class="csB0554C01" colspan="8" style="width:293px;height:20px;line-height:11px;text-align:center;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['subserie']; ?></nobr></td>
				<td class="cs914D1A68" style="width:2px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<tr style="vertical-align:top;">
				<td style="width:0px;height:20px;"></td>
				<td class="csD2198692" rowspan="2" style="width:0px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" rowspan="2" style="width:3px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" colspan="4" style="width:112px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>NOMBRE&nbsp;EXPEDIENTE</nobr></td>
				<td class="csF1013A5" colspan="5" style="width:256px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['titulo']; ?></nobr></td>
				<td class="cs914D1A68" rowspan="2" style="width:2px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>

			<?php if($sticker_final[$a]['numero_identificacion']){ ?>
				<tr style="vertical-align:top;">
					<td style="width:0px;height:20px;"></td>
					<td class="cs9409BF4D" colspan="4" style="width:112px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>IDENTIFICACI&#211;N</nobr></td>
					<td class="csF1013A5" colspan="5" style="width:256px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['numero_identificacion']; ?></nobr></td>
				</tr>
			<?php } ?>

			
			<tr style="vertical-align:top;">
				<td style="width:0px;height:20px;"></td>
				<td class="csD2198692" style="width:0px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" colspan="4" style="width:87px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>No.&nbsp;EXPEDIENTE</nobr></td>
				<td class="cs475035AB" colspan="5" style="width:256px;height:20px;line-height:11px;text-align:left;vertical-align:middle;"><nobr><?php echo $sticker_final[$a]['codigo_barras']; ?></nobr></td>
				<td class="cs914D1A68" style="width:2px;height:20px;"><!--[if lte IE 7]><div class="csF7D3565D"></div1111><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:8px;"></td>
				<td class="csD2198692" style="width:0px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" colspan="3" style="width:103px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:13px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:2px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:137px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:7px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:107px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:8px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:8px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:17px;"></td>
				<td class="csD2198692" style="width:0px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs9409BF4D" colspan="3" style="width:99px;height:13px;line-height:11px;text-align:left;vertical-align:middle;"><nobr>NUMERO&nbsp;DE&nbsp;CAJA</nobr></td>
				<td class="cs101A94F7" style="width:13px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:2px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:137px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:7px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" rowspan="3" style="width:107px;height:91px;text-align:left;vertical-align:top;"><div style="overflow:hidden;width:107px;height:91px;">
					<img alt="" src="<?php echo $qrwebname; ?>" style="width:107px;height:91px;" /><!--[if lt IE 7]></div><![endif]--></div>
				</td>
				<td class="cs101A94F7" style="width:8px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:17px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:5px;"></td>
				<td class="csD2198692" style="width:0px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:3px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" colspan="3" style="width:103px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:13px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:2px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:137px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:7px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:8px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:5px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:69px;"></td>
				<td class="csD2198692" style="width:0px;height:69px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="csB739ED8B" colspan="7" rowspan="2" style="width:258px;height:71px;text-align:left;vertical-align:top;">
				<div style="overflow:hidden;width:258px;height:71px;">
					<?php if($textcode128){ ?>
						<img alt="" src="<?php echo $codebarwebname; ?>" style="width:258px;height:71px;" /><!--[if lt IE 7]></div><![endif]-->
					<?php } ?>
				</div>
				</td>
				<td class="cs101A94F7" style="width:7px;height:69px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:8px;height:69px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:69px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:2px;"></td>
				<td class="csD2198692" style="width:0px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:7px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:107px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs101A94F7" style="width:8px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs914D1A68" style="width:2px;height:2px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
			<tr style="vertical-align:top;">
				<td style="width:0px;height:2px;"></td>
				<td class="csC3BBD80E" style="width:0px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:3px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" colspan="3" style="width:103px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:13px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:2px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:137px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:7px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:107px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="cs7384E3C7" style="width:8px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
				<td class="csA4A4F90C" style="width:2px;height:1px;"><!--[if lte IE 7]><div class="csF7D3565D"></div><![endif]--></td>
			</tr>
		</table>
		<?php if( count($sticker_final) > 1){ ?> <h6 style="page-break-after: always;">&nbsp;</h6><?php } ?>
	<?php } ?>
</body>
</html>