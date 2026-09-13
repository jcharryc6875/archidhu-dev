<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo form_tag('wf_instancia_bitacora/updateUsuario',array('name'=>'form1')) ?>
<?php echo object_input_hidden_tag($wf_instancia, 'getWfinstanciaId') ?>
<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Remitir Actividad
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tbody>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Consecutivo:</th>
  <td colspan="4" class="Color"><?php echo $wf_instancia->getWfinstanciaId() ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Fecha Inicial:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($wf_instancia, 'getFechaI', array (
  'readonly'=>true, 'size' => 50, 
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Fecha Final:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($wf_instancia, 'getFechaF', array (
  'readonly'=>true, 'size' => 50, 
))?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Fecha Limite:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($wf_instancia, 'getFechaUltimaActividad', array (
  'readonly'=>true, 'size' => 50, 
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Observaciones:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($wf_instancia, 'getObservaciones', array (
  'size' => 50,
))?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Usuario Asignado:</th>
  <td colspan="4" class="Color"><?php echo $wf_instancia->getUsuario() ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Usuario a Remitir:</th>
  <td colspan="4" class="Color"><select id='usuario_id' name='usuario_id' class='jsrequired'>";
		<option value=''>Seleccione Usuario...</option>			
	<?php foreach($usuarios_transicion as $usuarios){
			echo "<option value='".$usuarios->getUsuarioId()."'";
			echo ">".$usuarios->getUsuario()."</option>";
		}?>
		</select>
 </td>
</tr>
</tbody>
</table>
<table>
<td width="190" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar Nuevo Usuario Asignado" name="commit"  border="0" id="Login" ></a>
</td>
<td width="190" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/ico_cancelar.png',array('border'=>"0",'width'=>"40",'align'=>"middle")).'Regresar','wf_instancia_bitacora/list?instancia_id='.$wf_instancia->getPrimaryKey())?>
</td>
</table>
</form>
<?php echo javascript_tag("             	
    function openWindow(url) {
	  //showDebug();	  	  	 
	  var win = new Window('dialog',{title: '', className: 'alphacube', 
								  bottom:70, left:50, width:600, height:420, 
								  resizable: false, url: ''+url, showEffectOptions: {duration:1.0}
								  ,wiredDrag: true})
	win.show();
	win.setDestroyOnClose();
	//win.setCloseCallback(canClose);
	win.showCenter();					
  }    
  
  function canClose(rad,id) {
  	Windows.getWindow(\"dialog\").close();
	document.form1.idCom.value = id;
	document.form1.comunicacion.value = rad;  	
  }	
  					 
") ?>