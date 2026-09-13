<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<table width="99%"  cellpadding="0" cellspacing="0" align="center">
<caption>
     Detalles Del Contrato 
</caption>
<tr> 
  <th height="20" colspan="6" class="nobg" scope="col"></th>
</td>
<td  class="blanco"></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Remision: </th>
<td  class="Color">
<?php if($digitalizado){
$remision=sprintf("%06d",$contrato->getContratoId());
//echo $dirApacheDig."CONTRATO".$remision.".PDF";
?>
<a href="<?php echo $dirApacheDig."CONTRATO".$remision.".PDF";?>" >
<?php 
}
 echo "CONTRATO". sprintf("%06d",$contrato->getContratoId()) .".PDF" ;
?>
</a>
</td>
<td width="150" class="BotoneraBlanco">
<a href="#" onclick="javascript:simad_closeParent(); return false;"><img border="0" width="25" align="middle" src="/simad/images/simad/ico_cerrar.png" alt="Ico_Cerrar" />Cerrar</a></td>



</td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Estado: </th>
<td  class="Color"><?php echo $contrato->getEstadocontrato()->getDescripcion() ?></td>

<td class="BotoneraBlanco" nowrap>
<?php //if($sf_user->checkPerm("COM_INTERNA_EDITAR", $currentUser)&&$com_interna->getEstadoComInternaId()==1){?>
<a href="<?php echo $base_path; ?>/archivo.php/contrato/edit?contrato_id=<?php  echo $contrato->getPrimaryKey() ?>">
<img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_Editar.png" alt="Editar" width="30" height="30" align="middle" />Editar</a>
<?php //}?>
</td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Tipo: </th>
<td  class="Color"><?php echo $contrato->getTipocontrato()->getDescripcion() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Supervisor: </th>
<td  class="Color"><?php echo $contrato->getUsuario() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Vigencia: </th>
<td  class="Color"><?php echo $contrato->getVigencia() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Numero contrato: </th>
<td  class="Color"><?php echo $contrato->getNumeroContrato() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Nombre contratista: </th>
<td  class="Color"><?php echo $contrato->getNombreContratista() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Nit cedula: </th>
<td  class="Color"><?php echo $contrato->getNitCedula() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Observaciones: </th>
<td  class="Color"><?php echo $contrato->getObservaciones() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Objeto: </th>
<td  class="Color"><?php echo $contrato->getObjeto() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Fecha suscripcion: </th>
<td  class="Color"><?php echo $contrato->getFechaSubscripcion() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Fecha inicio: </th>
<td  class="Color"><?php echo $contrato->getFechaInicio() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Fecha terminacion: </th>
<td  class="Color"><?php echo $contrato->getFechaTerminacion() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Valor inicial: </th>
<td  class="Color"><?php  echo number_format($contrato->getValorInicial(),2,',','.'); ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Valor total: </th>
<td  class="Color"><?php echo number_format($contrato->getValorTotal(),2,',','.'); ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Cdp: </th>
<td  class="Color"><?php echo $contrato->getCdp() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Rp: </th>
<td  class="Color"><?php echo $contrato->getRp() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Fecha rp: </th>
<td  class="Color"><?php echo $contrato->getFechaRp() ?></td>
</tr>
<tr>
<th width="95" height="20" class="ColorRight" scope="row">Fecha cdp: </th>
<td  class="Color"><?php echo $contrato->getFechaCdp() ?></td>
</tr>
</tbody>
</table>

<?php // echo link_to('edit', 'contrato/edit?contrato_id='.$contrato->getContratoId()) ?>
