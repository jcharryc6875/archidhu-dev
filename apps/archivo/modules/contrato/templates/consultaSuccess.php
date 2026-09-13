<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('contrato/list','method=GET') ?>

<?php echo object_input_hidden_tag($contrato, 'getContratoId') ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
Consultar Contrato
</caption>
<tr>
 <td valign="top" >
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</td>
<td width="135" class="blanco"></td>
</tr>
<tr>
  <th width="150" height="20" class="ColorRight" scope="row">Estado:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($contrato, 'getEstadocontratoId', array (
  'related_class' => 'EstadoContrato','include_custom'=>'Seleccione...',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Tipo de Contrato:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($contrato, 'getTipocontratoId', array (
  'related_class' => 'TipoContrato','include_custom'=>'Seleccione...',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Supervisor:</th>
  <td colspan="4" class="Color"><?php echo object_select_tag($contrato, 'getUsuarioId', array (
  'related_class' => 'Usuario','include_custom'=>'Seleccione...',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Vigencia:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getVigencia', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Numero de Contrato:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNumeroContrato', array (
  'size' => 7,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Nombre de Contratista:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNombreContratista', array (
  'size' => 80,
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row">Nit o Cedula:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getNitCedula', array (
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
  <th width="95" height="20" class="ColorRight" scope="row">Fecha Inicio:</th>
  <td colspan="4" class="Color">
Entre <?php echo  input_date_tag('fechaInicioInicial', ''/*time()*/, 'rich=true') ?>
   y
   <?php echo  input_date_tag('fechaInicioFinal', ''/*time()*/, 'rich=true') ?>

</td>
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
  <th width="95" height="20" class="ColorRight" scope="row">Observaciones:</th>
  <td colspan="4" class="Color"><?php echo object_input_tag($contrato, 'getObservaciones', array (
  'size' => 80,
)) ?></td>
</tr>
</tbody>
</table>


</table>

<table width="91%"  align=center border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="BotoneraBlanco">
	<a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a>
</td>

    </tr>
</table>



</form>
