<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<?php echo form_tag('formas_por_rol/list','method=GET') ?>

<table width="90%" height="134" align=center cellpadding="0" cellspacing="0" >
<caption>
    Consultar Forma por Rol
</caption>
    
<tr>

    <th height="14" colspan="6" class="nobgLeft" scope="col">    
    
	<label>&nbsp;
	</label>
		</th>
</td>    
</tr>


<tr >

  <th class=ColorRight>Rol:</th>
  <td class=Color><?php echo object_select_tag($rolPrivilegio, 'getRolId', array (
  'related_class' => 'Rol','include_custom'=>'Seleccione...',
)) ?></td>
</tr>

<tr >


<tr >

  <th class=ColorRight>Forma:</th>
  <td class=Color><?php echo object_select_tag($rolPrivilegio, 'getFormaId', array (
  'peer_method'=>'getOrdenForma','related_class' => 'Forma','include_custom'=>'Seleccione...',
)) ?></td>
</tr>

<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Ordenar Por:</th>
  <?php $ordenList = array('ROL'=>'Rol','FORMA'=>'Forma') ?>
  <td colspan="4" class="Color"><?php echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',))) ?></td>
</tr>


</table>

<table width="91%"  align=center border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="BotoneraBlanco">
	<a href="#">
<input type="image" name="commit" align="middle" src="<?php echo $base_path; ?>/images/simad/ico_consultar.png" alt="Consultar" name="commit"  border="0" id="Login" /></a>
</td>

<td  class="BotoneraBlanco"><a href="#" onclick=""><img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_BorrarForm.png" alt="Exportar" align="middle" /></a></td>

<td width="60%"> &nbsp;</td>
    </tr>
</table>

</form>
