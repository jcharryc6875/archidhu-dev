<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('ws_modulo/update',array('name'=>'form1')) ?>

<?php echo object_input_hidden_tag($ws_usuarios_modulo, 'getWsusuariosmoduloId') ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Crear/Editar Modulo Webservice
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tbody>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Modulo:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($ws_usuarios_modulo, 'getModuloId', array (
  'related_class' => 'Modulo', 'class'=>'jsrequired', 'include_custom'=>' ---Seleccione--- ',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Usuario Webservice:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($ws_usuarios_modulo, 'getWsusuariosId', array (
  'related_class' => 'WsUsuarios','class'=>'jsrequired','include_custom'=>' ---Seleccione--- ',
)) ?></td>
</tr>
</tbody>
</table>
<table>
<td width="190" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar Usuario Webservice" name="commit"  border="0" id="Login" ></a>
</td>
<?php if($ws_usuarios_modulo->getWsusuariosmoduloId()){	?>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/ico_cerrar.png',array('border'=>"0",'width'=>"40",'align'=>"middle")).'Cancelar','ws_modulo/show?wsusuariosmodulo_id='.$ws_usuarios_modulo->getWsusuariosmoduloId()) ?>
</td>
<?php }else{ ?>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/ico_cerrar.png',array('border'=>"0",'width'=>"40",'align'=>"middle")).'Volver','ws_modulo/list?')?>
</td>
<?php } ?>
</table>

</form>
