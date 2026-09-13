<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('permiso_usuario_uniddoc/list',array('name'=>'form1','method'=>'GET')) ?>

<?php echo object_input_hidden_tag($permiso_usuario_uniddoc, 'getPermisousuariouniddocId') ?>

<table width="85%" height="134" cellpadding="0" cellspacing="0" align=center>
<caption>
    Consulta Usuario Por Carpeta
</caption>          
<tr>
    <th height="14" colspan="6" class="nobgLeft" scope="col">    
	<label>
&nbsp;
  
</label>
	</th>
</td>
<td  class="blanco"></td>
 
</tr>

<tr>
  <th height="20" class="ColorRight" scope="row">Unidad documental:</th>
  <td colspan="4" class="Color"><?php echo input_tag('unidad_documental', '', array ('size'=>'50',)) ?></td>
</tr>
<tr>
  <th height="20" class="ColorRight" scope="row">Codigo Barras:</th>
  <td colspan="4" class="Color"><?php echo input_tag('codigo_barras', '', array ('size'=>'50',)) ?></td>
</tr>
<tr>
  <th height="20" class="ColorRight" scope="row">Usuario:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($permiso_usuario_uniddoc, 'getUsuarioId', array (
  'peer_method'=>'getAllUser','include_custom'=>'--- Seleccione Usuario---',
  'related_class' => 'Usuario',
)) ?></td>
</tr>
</tbody>
</table>


<table width="85%"  cellpadding="0" cellspacing="0" align=center>
  <tr>
    <td  width="190" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a>
</td>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/Ico_BorrarForm.png',array('border'=>"0",'align'=>"middle")),'permiso_usuario_uniddoc/consulta')?></td>
<td  class="BotoneraBlanco">
<?php if ($permiso_usuario_uniddoc->getPermisousuariouniddocId()): ?>
  &nbsp;<?php echo link_to('Borrar', 'permiso_usuario_uniddoc/delete?permisousuariouniddoc_id='.$permiso_usuario_uniddoc->getPermisousuariouniddocId(), 'post=true&confirm=Are you sure?') ?>
 <?php endif; ?>
</td>
 <td width="10%"> &nbsp;</td>

    </tr>
</table>



</form>
