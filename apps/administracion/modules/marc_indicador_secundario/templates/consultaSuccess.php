<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('marc_indicador_secundario/list',array('name'=>'form1','method'=>'GET')) ?>
<?php echo input_hidden_tag('marc_id',$codigo_marc)?>
<table width="80%" height="10" cellpadding="0" cellspacing="0" align="center">
<caption>
     Consultar Segundo Indicador MARC
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tbody>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Codigo MARC:</th>
  <td colspan="4" class="Color"><?php echo input_tag('codigo', '', array (
  'size' => 50, 
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Codigo Indicador:</th>
  <td colspan="4" class="Color"><?php echo input_tag('codigoIndicador', '', array (
  'size' => 50, 
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Descripcion:</th>
  <td colspan="4" class="Color"><?php echo input_tag('descripcion', '', array (
  'size' => 50, 
)) ?></td>
</tr>
</tbody>
</table>
<table width="80%" cellpadding="0" cellspacing="0" align="center">
<td width="150" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a></td>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/Ico_BorrarForm.png',array('border'=>"0",'align'=>"middle")),'marc_indicador_secundario/consulta')?></td>
<td width="190" class="BotoneraBlanco">
<td width="190" class="BotoneraBlanco">
<td width="190" class="BotoneraBlanco">
</table>
</form>
