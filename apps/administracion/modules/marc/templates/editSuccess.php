<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo form_tag('marc/update',array('name'=>'form1')) ?>

<?php echo object_input_hidden_tag($marc, 'getMarcId') ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Crear/Editar Codigo MARC
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tbody>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Codigo:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($marc, 'getCodigo', array (
  'size' => 50, 'class'=>'jsrequired',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Descripcion:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($marc, 'getDescripcion', array (
  'size' => 50, 'class'=>'jsrequired',
)) ?></td>
</tr>
</tbody>
</table>
<table>
<td width="190" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar Nuevo Marc" name="commit"  border="0" id="Login" ></a>
</td>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/Ico_BorrarForm.png',array('border'=>"0",'align'=>"middle")),'marc/create')?>
</td>
</table>
</form>
