<h2><strong>Mis Actividades Pendientes</strong></h2>

<?php 

	echo include_partial('listCurrent',array('total_array_msj' => $total_array_msj, 'recibidas_leer'=>$recibidas_leer,'recibida_copia'=>$recibida_copia,'por_vencer'=>$por_vencer,'vencidas'=>$vencidas ,
			'por_distribuir'=>$por_distribuir,'por_gestionar'=>$por_gestionar,'por_ccalidad'=>$por_ccalidad,'workflow_recibida'=>$workflow_recibida,'facturas_recibida'=>$facturas_recibida,
			'por_leer'=>$por_leer,'copia'=>$copia,'por_responder'=>$por_responder,'internas_revisor'=>$internas_revisor,'internas_firmas'=>$internas_firmas,'internas_prufirmas'=>$internas_prufirmas,
			'workflow_interna'=>$workflow_interna,'enviadas_revisor'=>$enviadas_revisor,'enviadas_gestor'=>$enviadas_gestor,'enviadas_firmar'=>$enviadas_firmar,'enviadas_purfirmar'=>$enviadas_purfirmar,
			'enviadas'=>$enviadas,'copia_informativa'=>$copia_informativa,'enviadas_gsalida'=>$enviadas_gsalida,'prestamos_pendientes'=>$prestamos_pendientes,'actosadm_data' => $actosadm_data,'periodo_id'=>$periodo_id,'usuario'=>$usuario)); 

?>
  
    