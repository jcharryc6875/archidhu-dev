<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('contrato/update') ?>

<?php echo object_input_hidden_tag($contrato, 'getContratoId') ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
<?php 
if ($contrato->getContratoId())
		 echo "Editar";
	  else echo "Crear"; 
 ?>
     Contrato
</caption>
<tr>
 <td valign="top" >
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</td>
<td width="135" class="blanco"></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Estado:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($contrato, 'getEstadocontratoId', array (
  'related_class' => 'EstadoContrato',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Tipo de Contrato:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($contrato, 'getTipocontratoId', array (
  'related_class' => 'TipoContrato',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Supervisor:</th>
  <td colspan="4" class="Color"><?php
  
  
   echo object_select_tag($contrato, 'getUsuarioId', array ('peer_method'=>'getAllUser','related_class' => 'Usuario',
  'related_class' => 'Usuario',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Vigencia:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getVigencia', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Numero contrato:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNumeroContrato', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Nombre contratista:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNombreContratista', array (
  'size' => 80,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Nit cedula:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNitCedula', array (
  'size' => 80,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Observaciones:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getObservaciones', array (
  'size' => 80,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Objeto:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getObjeto', array (
  'size' => 80,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha de suscripcion:</th>
  <td colspan="4" class="Color"><?php echo object_input_date_tag($contrato, 'getFechaSubscripcion', array (
  'rich' => true,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha de inicio:</th>
  <td colspan="4" class="Color"><?php echo object_input_date_tag($contrato, 'getFechaInicio', array (
  'rich' => true,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha de terminacion:</th>
  <td colspan="4" class="Color"><?php echo object_input_date_tag($contrato, 'getFechaTerminacion', array (
  'rich' => true,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Valor inicial:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getValorInicial', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Valor total:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getValorTotal', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">CDP:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getCdp', array (
  'size' => 20,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">RP:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getRp', array (
  'size' => 20,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha rp:</th>
  <td colspan="4" class="Color"><?php echo object_input_date_tag($contrato, 'getFechaRp', array (
  'rich' => true,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Fecha CDP:</th>
  <td colspan="4" class="Color"><?php echo object_input_date_tag($contrato, 'getFechaCdp', array (
  'rich' => true,
)) ?></td>
</tr>
</tbody>
</table>

<table width="85%"  cellpadding="0" cellspacing="0" align=center>
  <tr>
    <td  class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Guardar" name="commit"  border="0" id="Login" /></a>
</td>
   
</tr>
</table>


</form>
