<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('permiso_usuario_uniddoc/update',array("name"=>"crear")) ?>

<?php echo object_input_hidden_tag($permiso_usuario_uniddoc, 'getPermisousuariouniddocId') ?>

<table width="85%" height="134" cellpadding="0" cellspacing="0" align=center>
<caption>
    Crear/Editar Permiso de usuario a carpeta
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
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Unidad Documental:</th>
  <td colspan="4" class="Color"><?php 
			echo input_hidden_tag('unidaddocumental_id', '');			
			echo input_tag('nombre', '', array ('size' => 50, 'readonly'=>'true', 'class'=>'jsrequired'));?>
			<td width="150" class="blanco"><?php echo jq_link_to_function(image_tag('simad/Ico_AddNote.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Seleccionar Unidad Documental')), 'javascript:seleccionar_unidad();');
			?></td>
</tr>
<!--
<tr>
  <th height="20" class="ColorRight" scope="row">Unidad documental:</th>
  <td colspan="4" class="Color"><?php 
  
  //echo object_select_tag($permiso_usuario_uniddoc, 'getUnidaddocumentalId', array ('peer_method'=>'getAllUD',
  //'related_class' => 'UnidadDocumental',
  //)) 
?></td>
</tr>
-->
<tr>
  <th height="20" class="ColorRight" scope="row">Usuario:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($permiso_usuario_uniddoc, 'getUsuarioId', array (
  'peer_method'=>'getAllUser',
  'related_class' => 'Usuario',
)) ?></td>
</tr>
</tbody>
</table>


<table width="85%"  cellpadding="0" cellspacing="0" align=center>
  <tr>
    <td  class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar" name="commit"  border="0" id="Login" /></a>
</td>
<td  class="BotoneraBlanco"><a href="#" onClick="javascript:simad_closeParent();"  ><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" alt="Cerrar" width="40" height="40" align="middle" />Cerrar</td>
 <td width="10%"> &nbsp;</td>
<td  class="BotoneraBlanco">
<?php if ($permiso_usuario_uniddoc->getPermisousuariouniddocId()): ?>
  &nbsp;<?php echo link_to('Borrar', 'permiso_usuario_uniddoc/delete?permisousuariouniddoc_id='.$permiso_usuario_uniddoc->getPermisousuariouniddocId(), 'post=true&confirm=Are you sure?') ?>
 <?php endif; ?>
</td>
 <td width="10%"> &nbsp;</td>

    </tr>
</table>
</form>
<?php echo javascript_tag("
   
   function seleccionar_unidad() {	 
	    win2 = new Window('winShow',{title: 'Seleccionar Unidad Documental', className: 'alphacube', 
								  bottom:80, left:50, width:600, height:350, 
								  resizable: true, url: '/administracion.php/permiso_usuario_uniddoc/consultarArchivo', wiredDrag: true})
		win2.show();
		win2.setDestroyOnClose(true);	
		win2.showCenter();	} 
   
   function simad_close(){
		Windows.getWindow(\"winShow\").close();
   }

   
") ?>
