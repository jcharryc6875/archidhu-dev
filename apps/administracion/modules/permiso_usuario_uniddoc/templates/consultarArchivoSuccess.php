<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('permiso_usuario_uniddoc/listArchivo', array('name'=>'consultar', 'method'=>'GET')) ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Consultar Unidad Documental
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr> 
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Archivo Para Consultar:</th>
  <td colspan="4" class="Color"><?php 
  						$destinos = array(0=>'---Seleccione---',1=>'Archivo de Trámite', 2=>'Archivo de Concentración');
						echo select_tag('localizacionunidaddocumental_id', options_for_select($destinos, ''));?></td>
</tr>

<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Codigo Barras</th>
  <td colspan="4" class="Color"><?php echo input_tag('codigo_barras', '', array('size'=>'20'));?></td>
  </tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Titulo</td>
  <td colspan="4" class="Color"><?php echo input_tag('titulo', '', array('size'=>'50'));?></td>
  </tr>
</table>
<table width="80%" cellpadding="0" cellspacing="0" align="center">
<td width="150" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a></td>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/Ico_BorrarForm.png',array('border'=>"0",'align'=>"middle")),'permiso_usuario_uniddoc/consultarArchivo')?></td>
<td width="190" class="BotoneraBlanco">
<td width="190" class="BotoneraBlanco">
<td width="190" class="BotoneraBlanco">
</table>
</form>