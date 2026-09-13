<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php 
echo form_tag('transferencia/updateRechazar', array("name"=>"transferir"));
echo object_input_hidden_tag($transferencia, 'getTransferenciaId');
?>
<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Rechazar Transferencia
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Origen Transferencia:</th>
  <td colspan="4" class="Color"><?php echo $transferencia->getOrigentransferencia()->getDescripcion(); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Destino Transferencia:</th>
  <td colspan="4" class="Color"><?php echo $transferencia->getDestinotransferencia()->getDescripcion(); ?></td>
</tr>
<?php
if($transferencia->getOrigentransferenciaId() <= 4 || $transferencia->getOrigentransferenciaId() == 7){
?>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Unidad Documental:</th>
  <td colspan="4" class="Color"><?php echo $transferencia->getUnidadDocumental()->getTitulo(); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Tipo Documental:</th>
  <td colspan="4" class="Color"><?php echo $transferencia->getTipoDocumental(); ?></td>
</tr>
<?php
}else{
?>
<?php
}
?>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha Solicitud:</th>
  <td colspan="4" class="Color"><?php echo $transferencia->getFechaCreacion(); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Observaciones Rechazo:</th>
  <td colspan="4" class="Color"><?php echo object_textarea_tag($transferencia, 'getObservaciones', array (
  'size' => '30x3','class'=>'jsrequired'
)) ?></td>
  </tr>
</table>
<table>
<td width="190" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Rechazar La Transferencia" name="commit"  border="0" id="Login" ></a>
</td>
</table>
</form>