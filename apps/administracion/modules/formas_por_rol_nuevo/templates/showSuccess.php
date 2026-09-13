<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<table width="95%" height="134" align=center cellpadding="0" cellspacing="0" >
<caption>
    Detalle de Forma por Rol
</caption>
    
<tr> 
 <th height="14" colspan="6" class="nobgLeft" scope="col">    
    
	<label>&nbsp;
	</label>
		</th>
</td>    
</tr> 

<tr > 
<th class=ColorRight>Consecutivo: </th>
<td class=Color><?php echo $rol_privilegio->getRolprivilegioId() ?></td>
    <td width="150" class="BotoneraBlanco">

<a href="#" onClick="javascript:simad_closeParent();"  ><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" alt="Cerrar" width="30" height="30" align="middle" />Cerrar</a>
</td> 

</tr>
<tr>
<th class=ColorRight>Rol: </th>
<td class=Color><?php echo $rol_privilegio->getRol()->getDescripcion() ?></td>
<td width="170" class="BotoneraBlanco" nowrap>

<a href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/edit?rolprivilegio_id=<?php  echo $rol_privilegio->getRolprivilegioId() ?>" 	>
<img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_Editar.png" alt="Editar" width="30" height="30" align="middle" />Editar</a>


</td>
</tr>
<tr>
<th class=ColorRight>Forma: </th>
<td class=Color><?php echo $rol_privilegio->getForma()->getDescripcion() ?></td>
<td width="170" class="BotoneraBlanco" nowrap>

<a href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/delete?rolprivilegio_id=<?php  echo $rol_privilegio->getRolprivilegioId() ?>" 	>
<img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_Borrar.png" alt="Borrar" width="30" height="30" align="middle" />Borrar</a>


</td>
</tr>
</tbody>
</table>