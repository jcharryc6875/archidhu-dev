<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div id="indicator_fondo" class="modalDialog_transparentDivs" style="top: 0px; left: 0px; width: 100%; height: 110%; display: none; "></div>
<?php 
echo form_tag('com_aprobaciones/comAccion', array("name"=>"form1"));
echo object_input_hidden_tag($com_aprobacion, 'getComaprobacionId');
echo input_hidden_tag('modulo_id', $sf_params->get('modulo_id'));
echo input_hidden_tag('consecutivo_id', $sf_params->get('consecutivo_id'));
echo input_hidden_tag('estado_id', $sf_params->get('estado_id'));
echo input_hidden_tag('user_id', $sf_params->get('user_id'));
?>
<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Rechazar Comunicacion
</caption>
<tr> 
  <th height="10" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<div class="informacion" style="width: 80%; margin-left: auto; margin-right: auto; display: none;">
Ocurrio un error y no se pudo realizar la accion solicitada!</div>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Observaciones Rechazo:</th>
  <td colspan="4" class="Color"><?php echo textarea_tag('observaciones', '', array (
  'size' => '25x3','class'=>'jsrequired'
)) ?></td>
</tr>
</table>
<table>
<td width="190" class="BotoneraBlanco">
<?php echo jq_submit_image_to_remote('rechazar','/images/simad/ico_guardar.png',array(
    'url'      => 'com_aprobaciones/comAccion',
    'update'   => '',
    'loading'  => "Element.show('indicator_fondo')",
    'success'	=> "closeWin()",
    'complete' => "Element.hide('indicator_fondo')",
    'failure' => "Element.show('informacion')",
  ),array('method' => 'post','title' => 'Rechazar aprobacion de la comunicacion')) 
?>
<!--</a><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_guardar.png" alt="Rechazar La Comunicacion" name="commit"  border="0" id="Rechazar" /></a>-->
</td>
</table>
</form>
<?php 
echo javascript_tag("
    function closeWin() {
        parent.closeWin();
	}
") ?>