<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>

<table cellspacing="0" width="98%" align=center>

<caption>Permisos de usuario por carpeta
 </caption>
<tr class="encabezado_tabla">
<tr>
  <th scope="col" class="nobg">Consecutivo Permiso</th>
  <th scope="col" class="nobg">Unidad Documental</th>
  <th scope="col" class="nobg">Usuario</th>
  <th scope="col" class="nobg">Opciones</th>
</tr>
</thead>
<tbody> 
<?php 
$fila="spec";
foreach ($pager->getResults() as $permiso_usuario_uniddoc): 

if($fila=="specalt"){
	$fila="spec";
}
else{
	$fila="specalt";
}

//foreach ($permiso_usuario_uniddocs as $permiso_usuario_uniddoc): ?>
<tr>
     <th class="<?php echo $fila?>">
	 <a href="#" onclick="javascript:openVentana('/administracion.php/permiso_usuario_uniddoc/show?permisousuariouniddoc_id=<?php echo $permiso_usuario_uniddoc->getPermisousuariouniddocId()?>'); return false;">
 	 <?php echo sprintf("%06d",$permiso_usuario_uniddoc->getPermisousuariouniddocId())?>
     </a>	 
      <td class="<?php echo $fila?>"><?php echo $permiso_usuario_uniddoc->getUnidaddocumental() ?></td>
      <td class="<?php echo $fila?>"><?php echo $permiso_usuario_uniddoc->getUsuario() ?></td>
      <td class="<?php echo $fila?>"><?php echo link_to(image_tag('simad/Ico_Borrar.png',array('border'=>"0",'width'=>"20",'align'=>"middle")), 'permiso_usuario_uniddoc/delete?permisousuariouniddoc_id='.$permiso_usuario_uniddoc->getPermisousuariouniddocId()) ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>


<table width="99%"  align=center border="0" cellpadding="0" cellspacing="0">
  <tr>



<td  class="BotoneraBlanco">
<a href="<?php echo $base_path; ?>/administracion.php/permiso_usuario_uniddoc/consulta">
<img border=0 src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" alt="Consultar Usuario" width="40" height="40" align="middle" />Consultar
</a>
</td>  

<td  class="BotoneraBlanco">
<a href="#" onclick="javascript:openVentana('<?php echo $base_path; ?>/administracion.php/permiso_usuario_uniddoc/create'); return false;">
<img border=0 src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" alt="Crear Nuevo Usuario" width="40" height="40" align="middle" />Crear Nuevo
</a>
</td>  



<td  class="BotoneraBlanco"><a href="<?php echo $base_path; ?>/administracion.php/permiso_usuario_uniddoc/excel?<?php echo $parametros?>" ><img border=0 src="<?php echo $base_path; ?>/images/simad/ico_exportar.png" alt="Exportar" width="40" height="40" align="middle" />Exportar</td>
<td width="40%" class="BotoneraBlanco"><a href="<?php echo $base_path; ?>/enviada.php/permiso_usuario_uniddoc/excel?<?php echo $parametros?>" >

</td>
</tr>
</table>

<!-- inicio  paginacion -->
<table cellspacing=0 width="98%" align=center>

<tr >

<th class="ColorRight" >
<?php echo "   ".$pager->getNbResults()?> registros encontrados
</th>

<td class="Color" width="50%">
 &nbsp;
</td>
<th align=right valign=top class=ColorRight>
<?php 
echo use_helper('Pagination');
echo pager_navigation($pager, 'permiso_usuario_uniddoc/list', $parametros);
?>
 </th>
 </tr>
</table>
<!-- fin  paginacion --> 


