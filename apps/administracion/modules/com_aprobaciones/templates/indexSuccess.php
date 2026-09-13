<?php use_helper('jQuery') ?>
<?php $currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');?>
<table cellspacing="0" width="98%" align=center>
    <caption>
        Aprobaciones De Correspondencia 
    </caption>
  <thead>
    <tr>
      <th colspan="2" scope="col" class="nobg">Aprobaciones</th>
      <th scope="col" class="nobg">Estado</th>
      <th scope="col" class="nobg">Usuario</th>            
      <th scope="col" class="nobg">Observaciones</th>
      <th scope="col" class="nobg">Fecha Creacion</th>
      <th scope="col" class="nobg">Fecha Ejecucion</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $fila="spec";
    foreach ($com_aprobacionList as $com_aprobacion): ?>
    <tr class="<?php 
        if($fila=="specalt"){
	       echo $fila="spec";
        }
        else{
	       echo $fila="specalt";
        }?>">
      <th class="<?php echo $fila?>">
      <?php if($com_aprobacion->getEstadocomaprobacionId() == 1 && $com_aprobacion->getUsuarioId() == $currentUser){ ?>
      <a href="<?php echo url_for('com_aprobaciones/comAccion?comaprobacion_id='.$com_aprobacion->getComaprobacionId().'&estado_id=2&modulo_id='.$sf_params->get('modulo_id').'&consecutivo_id='.$sf_params->get('consecutivo_id').'&user_id='.$sf_params->get('user_id')) ?>"><?php echo image_tag('simad/Ico_Atendido.png',array('border'=>"0",'width'=>"25" , 'title'=>'Aprobar la comunicacion')) ?></a>
      <?php } ?>
      &nbsp;</th>      
      <th class="<?php echo $fila?>">
      <?php if($com_aprobacion->getEstadocomaprobacionId() == 1 && $com_aprobacion->getUsuarioId() == $currentUser){ ?>
      <?php
            echo jq_link_to_function(image_tag('simad/Ico_Anular.png',array('border'=>"0",'width'=>"25",'title'=>'Rechazar')),'javascript:openVentanaAjust("/administracion.php/com_aprobaciones/rechazar?comaprobacion_id='.$com_aprobacion->getComaprobacionId().'&estado_id=3&modulo_id='.$sf_params->get('modulo_id').'&consecutivo_id='.$sf_params->get('consecutivo_id').'&user_id='.$sf_params->get('user_id').'","450","250")',array('id'=>'buttonrechazar'));
      ?>
      &nbsp;</th>
      <?php } ?>
      <td class="<?php echo $fila?>"><?php echo $com_aprobacion->getEstadocomaprobacion() ?>&nbsp;</td>
      <td class="<?php echo $fila?>"><?php echo $com_aprobacion->getUsuario() ?></td>
      <td class="<?php echo $fila?>"><?php echo $com_aprobacion->getObservaciones() ?>&nbsp;</td>
      <td class="<?php echo $fila?>"><?php echo $com_aprobacion->getFechaCreacion() ?>&nbsp;</td>
      <td class="<?php echo $fila?>"><?php echo $com_aprobacion->getFechaEjecucion() ?>&nbsp;</td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php 
echo javascript_tag("
    function closeWin() {
		Windows.getWindow(\"dialog2\").close();
        parent.Windows.getWindow(\"dialog2\").refresh();							    	
    	return true;
	}
") ?>
