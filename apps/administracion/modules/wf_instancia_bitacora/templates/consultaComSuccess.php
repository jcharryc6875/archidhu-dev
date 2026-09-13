<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo form_tag('wf_instancia_bitacora/listCom', array('name'=>'form1','method'=>'GET')) ?>
<?php echo input_hidden_tag('wfactividadtransicion_id', $sf_params->get('wfactividadtransicion_id')) ?>
<table width="80%" height="134" cellpadding="0" cellspacing="0" align="center">
<caption>
     Consultar Comunicaciones <?php echo $sf_params->get('modulocomunicaciones') ?>
</caption>
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Radicado:</th>
  <td colspan="4" class="Color"><?php echo input_tag('radicado' , '', array (
  'size'=>'50',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Asunto:</th>
  <td colspan="4" class="Color"><?php echo input_tag('asunto','',array (
  'size'=>'50',
)) ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Funcionario Destino:</th>
  <td colspan="4" class="Color"><?php echo select_tag('usuario_id',objects_for_select($usuarios,'getUsuarioId','getNombreAll','',
   array('include_custom'=>'Seleccione Usuario...',)) , array (     
   'name'=>'usuario_destino','id'=>'usuario_destino',)); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Copia A:</th>
  <td colspan="4" class="Color"><?php echo select_tag('usuario_id',objects_for_select($usuarios,'getUsuarioId','getNombreAll','',
   array('include_custom'=>'Seleccione Usuario...',)) , array (     
   'name'=>'usuario_copia','id'=>'usuario_copia',)); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Funcionario Radicador:</th>
  <td colspan="4" class="Color"><?php echo select_tag('usuario_id',objects_for_select($usuarios,'getUsuarioId','getNombreAll','',
   array('include_custom'=>'Seleccione Usuario...',)) , array (     
   'name'=>'radicador','id'=>'radicador',)); ?></td>
</tr>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Fecha Radicacion:</th>
  <td colspan="4" class="Color"><?php echo input_date_tag('fechaInicial', ''/*time()*/, array(
           'rich' => true,'readonly'=>true,
           'calendar_button_img' => '/sf/sf_admin/images/date.png')); 
            echo input_date_tag('fechaFinal', ''/*time()*/, array(
           'rich' => true,'readonly'=>true,
           'calendar_button_img' => '/sf/sf_admin/images/date.png'))?></td>
</tr>
<tr>  
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Esta Marcado:</th>
  <?php $marcado = array('1'=>'Si','0'=>'No') ?>
  <td colspan="4" class="Color"><?php echo select_tag('marcada' , options_for_select($marcado,'',array('include_custom'=>'Seleccione',)))?></td>
</tr>
<tr>  
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Periodo:</th>
  <td colspan="4" class="Color"><?php
  $periodo->setPeriodoId(date("Y"));
  echo object_select_tag($periodo, 'getPeriodoId', array ('related_class' => 'Periodo', 
  'include_custom'=>'Seleccione',)) ?></td>
</tr>
</table>
<table width="80%" cellpadding="0" cellspacing="0" align="center">
<td width="150" class="BotoneraBlanco"><a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a></td>
<td width="150" class="BotoneraBlanco"><?php echo link_to(image_tag('simad/Ico_BorrarForm.png',array('border'=>"0",'align'=>"middle")),'wf_instancia_bitacora/consultaCom')?></td>

</table>