<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!--
<h1>Bitacora</h1>
-->
<?php $cuenta=0 ?>
<?php $wdg=new wf_widgets() ?>

<table width="99%" cellpadding="0" cellspacing="0" align="center">
<caption>
     Bitacora del WF
</caption>
<!--
<tr> 
  <th height="20" colspan="6" class="nobgLeft" scope="col"></th>
</td>
<td width="135" class="blanco"></td>
</tr>
-->
<tr>
<th width="95" class="ColorRight" scope="row">Radicado: </th>
<?php 
$i=0;
$cominterna_id=0;
$comrecibida_id=0;
$strasunto="";
$strradicado="";

if($myInstancia->getWfinstanciaId()>0){
    $esta_abierta=$myInstancia->getEstaAbierta();	
	
	$instancia_id=$myInstancia->getWfinstanciaId();
	$cominterna_id=$myInstancia->getCominternaId();
	$comrecibida_id=$myInstancia->getComrecibidaId();
	if($cominterna_id>0){
		$myInterna=ComInternaPeer::retrieveByPK($cominterna_id);
		$strradicado=$myInterna->getRadicado();
		$strasunto=$myInterna->getReferencia();		
	}
	if($comrecibida_id>0){
		$myRecibida=ComRecibidaPeer::retrieveByPK($comrecibida_id);
		$strradicado=$myRecibida->getRadicado();
		$strasunto  =$myRecibida->getAsunto();		
	}
}
?>
<td colspan="4" class="Color"> <?php echo $strradicado ?> </td>
</tr> 
<tr>
<th width="95" class="ColorRight" scope="row">Estado: </th>
<td colspan="4" class="Color"> <?php echo $esta_abierta==true ? "Tramite Abierto: " . $strasunto  : "Tramite Cerrado."  ?> </td>
</tr>
</table>

<?php $th_class=" scope='col' class='nobg' "; ?>

<table width="99%" height="134" cellpadding="0" cellspacing="0" align="center">
<thead>
<tr  height="20" colspan="6" class="nobgLeft" scope="col">
  <th  <?php echo $th_class ?> >Consecutivo</th>
  <th <?php echo $th_class ?>>Usuario</th>
  <th <?php echo $th_class ?>>Actividad</th>
  <th <?php echo $th_class ?>>Fecha Inicio</th>
  <th <?php echo $th_class ?>>Fecha Final</th>
  <th <?php echo $th_class ?>>Actual</th>
  <th <?php echo $th_class ?>>Comunicacion</th>
</tr>
</thead>
<tbody>
<?php foreach ($wf_instancia_bitacoras as $wf_instancia_bitacora): ?>

<?php     $cuenta++;  ?>
<?php     $td_class=($cuenta %2 ==0 )?  " class='specalt' " : " class='spec' "  ?>

<tr>
    <th  <?php echo $td_class ?> >
	<?php // if($wf_instancia_bitacora->getEsActual() && $wf_instancia_bitacora->getUsuarioId() ==   $logged_user   ){ ?>
	<?php
	//verifica si puede ejecutarse
	$myIfz=new wf_Interface();
	$bitacora_id=$myIfz->getBitacoraActual($instancia_id);
	if($bitacora_id>0){
		//echo "bita=". $bitacora_id;
		$objBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
		$actividad_id=$objBitacora->getWfActividadId();
	}
	$arrUsuariosPermitidos=$myIfz->getGrantedUsers($instancia_id, $actividad_id);
	$oktogo=0;
	//echo "Logged user=" .$logged_user;
	if( in_array($logged_user,$arrUsuariosPermitidos) ==true ) $oktogo=1;
    //echo "oktogo=" .$oktogo;	
	
	?>
	
	
	
	<?php  if($wf_instancia_bitacora->getEsActual() && $oktogo ){ ?>
	<?php       $link = $base_path.'/backend.php/wf_bitacora/edit?wfinstanciabitacora_id='.$wf_instancia_bitacora->getWfinstanciabitacoraId(); ?>
	<?php       echo $wdg->getButton("Ejecutar",$link,"Ejecutar"); ?>
	<?php }else { ?>
	
	<?php       echo $wdg->getButton("Link",$base_path.'/backend.php/wf_bitacora/show?wfinstanciabitacora_id='.$wf_instancia_bitacora->getWfinstanciabitacoraId() , $wf_instancia_bitacora->getWfinstanciabitacoraId()); ?>
	
	<?php } ?>
	</td>
	  
      <td <?php echo $td_class ?> ><?php echo $wf_instancia_bitacora->getUsuarioId() . " - " . $wf_instancia_bitacora->getUsuario()->getUserName() ?></td>
      <td <?php echo $td_class ?>><?php echo $wf_instancia_bitacora->getWfActividadId() . " - " . $wf_instancia_bitacora->getWFActividad()->getDescripcion() ?></td>
      <td <?php echo $td_class ?>><?php echo " ".$wf_instancia_bitacora->getFechaI() ?>&nbsp;</td>
      <td <?php echo $td_class ?>><?php echo " ".$wf_instancia_bitacora->getFechaF() ?>&nbsp;</td>
      <td <?php echo $td_class ?>><?php echo " ". ($wf_instancia_bitacora->getEsActual())==1?"SI":"&nbsp;" ; ?>&nbsp;</td>
      <td <?php echo $td_class ?>>
	  <?php echo " ".$wf_instancia_bitacora->getBuzonId() ?>
      <?php
	      $com_id=0; 
	      $com_id=$wf_instancia_bitacora->getCominternaid();
	      if($com_id>0){
			$urlcom = $base_path."/interna.php/com_interna/show?cominterna_id=".$com_id;
		  }else{
		      $com_id=$wf_instancia_bitacora->getComrecibidaid();
	          if($com_id>0){
			      $urlcom = $base_path."/recibida.php/com_recibida/show?comrecibida_id=".$com_id;
		      }	
		  }
	      if($com_id>0){
		      	echo $wdg->getButton("Link",$urlcom,$com_id);
		  }
	  ?> 	  
	  &nbsp;</td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>


