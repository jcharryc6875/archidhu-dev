<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<style type="text/css">
.enlaceboton {
	PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-WEIGHT: bold; FONT-SIZE: 10pt; PADDING-BOTTOM: 4px; COLOR: #666666; PADDING-TOP: 4px; FONT-FAMILY: verdana, arial, sans-serif; BACKGROUND-COLOR: #ffffcc; TEXT-DECORATION: none
}
.enlaceboton:link {
	BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
}
.enlaceboton:visited {
	BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
}
.enlaceboton:hover {
	BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #666666 2px solid; BORDER-LEFT: #666666 2px solid; BORDER-BOTTOM: #cccccc 1px solid
}
</style>

<div id="indicator_fondo" class="modalDialog_transparentDivs" style="top: 0px; left: 0px; width: 100%; height: 110%; display: none; "></div>
<div id="indicator" style="display: none"></div>

<table width="99%" height="134" cellpadding="0" cellspacing="0" >
<caption>
    Crear Comunicacion Interna
</caption> 
<tr> 
<tr>
    <th height="14" scope="col" class="nobgLeft" colspan="6">    
	<label>
&nbsp;
  
</label>
	</th>

<td class="blanco"></td>
 
</tr>
   <td valign="top" > 
      <table>
                <tr>
                    <td height="50" colspan="6" class="nobgLeft" scope="col" align="center">    
                          <a class="enlaceboton" href="<?php echo $base_path; ?>/enviada.php/com_enviada/create">1. Formulario Web</a>
                          <br />
                	</td>
                </tr>
                <tr>
                    <td height="50" colspan="6" class="nobgLeft" scope="col">    
                          <a class="enlaceboton" href="<?php echo $base_path; ?>/enviada.php/com_enviada/opcionesCreate">2. Complemento de Word</a>
                	<br /></td>
                </tr>
                <tr>
                    <td height="50" colspan="6" class="nobgLeft" scope="col">    
                          <a class="enlaceboton" href="<?php echo $base_path; ?>/enviada.php/com_enviada/createPlantillaWord">3. Generar Radicado en Plantilla de Word</a>
                	<br /></td>
                </tr>
                <tr>
                    <td height="50" colspan="6" class="nobgLeft" scope="col">    
                          <a class="enlaceboton" href="<?php echo $base_path; ?>/enviada.php/com_enviada/createRadicarWord">4. Radicar plantilla de Word </a>
                	<br /></td>
                </tr>
      </table>
   </td>
</tr>
</table>