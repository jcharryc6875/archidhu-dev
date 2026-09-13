<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('formas_por_rol/update',array('name' => 'form1')) ?>
<?php echo object_input_hidden_tag($rol_privilegio, 'getRolprivilegioId') ?>

<table width="85%" height="134" cellpadding="0" cellspacing="0" align=center>
<caption>
    Crear/Editar Forma por Rol
    
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
  <th height="20" class="ColorRight" scope="row">Rol:</th>
   <td colspan="4" class="Color"><?php echo object_select_tag($rol_privilegio, 'getRolId', array (
  'related_class' => 'Rol','class'=>'jsrequired','include_custom'=>'Seleccione...',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Forma:</th>
  <td colspan="4" class="Color">
<div id="contenedor_formas">
<select name="forma_id" id="forma_id"  class="jsrequired">
<option value="0">Seleccione...</option>
  <?php 	
	foreach($formas as $forma){
		echo "<option value='".$forma->getFormaId()."'";
		if($forma->getFormaId() == $forma_seleccionada){
			echo " selected ";
		}        
		echo ">".$forma->getModulo()->getDescripcion()." - ".$forma->getDescripcion()."</option>";
	}
?>
</select>
</div>
</td>
</tr>
</tbody>
</table>
<?php
    if($sf_params->get('msg')){
		echo "Ya tiene la Forma Cargada para este rol";
	}
    ?>
<table width="85%"  cellpadding="0" cellspacing="0" align=center>
  <tr>
    <td  class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar" name="commit"  border="0" id="Login" /></a>
</td>
<td  class="BotoneraBlanco"><a href="#" onClick="javascript:simad_closeParent();"  ><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" alt="Cerrar" width="40" height="40" align="middle" />Cerrar</td>
 <td width="10%"> &nbsp;</td>
    </tr>
</table>



</form>
