<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php echo form_tag('formas_por_rol/list',array('method=GET','name'=>'form1')) ?>
<div id="list_formas_por_rol">
<table width="99%" height="134" align=center cellpadding="0" cellspacing="0" >
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
  <td class=Color><?php   
  echo object_select_tag($rol, 'getRolId', array (
  'related_class' => 'Rol','include_custom'=>'Seleccione...'));
   
?></td>
</tr>

<tr>
  <th class=ColorRight>Modulo:</th>
  <td class=Color><?php echo object_select_tag($modulo, 'getModuloId', array (
  'peer_method'=>'getModuloFormas','related_class' => 'Modulo','include_custom'=>'Seleccione...','onchange' =>
  jq_remote_function(array(
                   'update' => 'list_formas_por_rol',
                   'url' => 'formas_por_rol/list',
                   'with' => '"modulo_id=" + this.options[this.selectedIndex].value'
                 ))
),$sf_params->get('modulo_id')); ?></td>
</tr>

<tr>
  <th class=ColorRight>Forma:</th>
  <td class=Color> 
  <select name="forma_id" id="forma_id" >
	<option value="0">Seleccione...</option>
	  <?php        		
		/*while($objects = $resultset->fetch()){          
			echo "<option value='".$objects[0]."'";
			if($objects[0] == $sf_params->get('forma_id')){
				echo " selected ";
			}
			echo ">".$objects[2]." - ".$objects[1]."</option>";          
		}*/
	?>
	</select>
  </td>
</tr>

<tr>
  <th width="95" height="20" class="ColorRight" scope="row" nowrap="true">Ordenar Por:</th>
  <?php $ordenList = array('ROL'=>'Rol','FORMA'=>'Forma') ?>
  <td colspan="4" class="Color"><?php echo select_tag('orden' , options_for_select($ordenList,'',array('include_custom'=>'Seleccione',))) ?></td>
</tr>
</table>
<?php if($data_list){ ?>
<table cellspacing="0" width="98%" align=center>
<caption>
Formas por Rol 
  </caption>
<tr class="encabezado_tabla">
<tr>
  <th scope="col" class="nobg">Consecutivo</th>
  <th scope="col" class="nobg">Rol</th>
  <th scope="col" class="nobg">Forma</th>
  <th scope="col" class="nobg">Asignar</th>
  <th scope="col" class="nobg">Eliminar</th>
</tr>
</thead>
<tbody>
<?php 
$fila="spec";
foreach ($pager->getResults() as $forma): 

if($fila=="specalt"){
	$fila="spec";
}
else{
	$fila="specalt";
}
?>
<tr>
     <th class="<?php echo $fila?>">
	  <!--<a href="#" onclick="javascript:openVentana('/administracion.php/formas_por_rol/show?rolprivilegio_id=<?php //echo $rol_privilegio->getRolprivilegioId()?>'); return false;">-->   
     <?php echo sprintf("%05d",$forma->getFormaId())?>
     <!--</a>-->
	 </th>
     <td class="<?php echo $fila?>"><?php 
     $arr_rol = $forma->getRolPrivilegiosJoinRol();
     foreach($arr_rol as $rol){
        echo $rol->getRol();
     }
     //echo $rol_privilegio->getRol() ?></td>
     <td class="<?php echo $fila?>"><?php echo $forma->getModulo()." - ".$forma->getDescripcion() ?></td>
     <td class="<?php echo $fila?>"><?php echo jq_link_to_remote(image_tag('simad/ico_atendido.png',array('border'=>"0",'width'=>"25",'align'=>"middle")),array(
    'update'   => array('success' => 'respuesta', 'failure' => 'error'),
    'url'      => 'formas_por_rol/delete?rolprivilegio_id=',
));
?></td>
     <td class="<?php echo $fila?>"><?php echo link_to(image_tag('simad/ico_anular.png',array('border'=>"0",'width'=>"25",'align'=>"middle")),'wf_variable/create?wfactividadtransicion_id='); ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>


<table width="99%"  align=center border="0" cellpadding="0" cellspacing="0">
  <tr>



<td  class="BotoneraBlanco">
<a href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/consulta">
<img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_Buscar.png" alt="Consultar Usuario" width="40" height="40" align="middle" />Consultar
</a>
</td>  

<td  class="BotoneraBlanco">
<a href="#" onclick="javascript:openVentana('<?php echo $base_path; ?>/administracion.php/formas_por_rol/create'); return false;">
<img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_CrearNuevo.png" alt="Crear Nuevo Usuario" width="40" height="40" align="middle" />Crear Nuevo
</a>
</td>
<td  class="BotoneraBlanco"><a href="<?php echo $base_path; ?>/administracion.php/formas_por_rol/excel?<?php echo $parametros?>" ><img border=0 src="<?php echo $base_path; ?>/images/simad/Ico_Exportar.png" alt="Exportar" width="40" height="40" align="middle" />Exportar</td>
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
echo pager_navigation($pager, 'formas_por_rol/list', $parametros);
?>
 </th>
 </tr>
</table>
<!-- fin  paginacion --> 
<?php } ?>
</div>