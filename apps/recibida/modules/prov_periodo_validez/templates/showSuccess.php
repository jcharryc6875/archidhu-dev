<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
$wdg = new wf_widgets();
?>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalle de Periodo de Validez
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">
                
                <?php 
                    echo link_to(image_tag('/images/simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','prov_periodo_validez/edit?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Editar este proveedor"));
                ?>
            
                <?php 
                if($sf_user->checkPerm("prov_check_list_vigencia/index", $currentUser)){
                    echo jq_link_to_function(image_tag('/images/simad/ico_check.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Check List','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/recibida.php/prov_check_list_vigencia/index?prov_periodo_validez_id='.$prov_periodo_validez->getPrimaryKey().'", "960", "600")',array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Ver check list del proveedor"));
                }
                ?>

                <?php 
                if($sf_user->checkPerm("prov_check_list_vigencia/index", $currentUser)){
                    echo jq_link_to_function(image_tag('/images/simad/ico_ver_adj.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Documentos Adjuntos','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/recibida.php/prov_documento/index?prov_periodo_validez_id='.$prov_periodo_validez->getPrimaryKey().'", "960", "600")',array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Ver documentos adjuntos"));
                }
                ?>                

                <?php //if($sf_user->checkPerm("COM_INTERNA_EDITAR", $currentUser)){?>
                    <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/recibida.php/prov_estado_flujo_periodo/index?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId(); ?>','960', '600')" class='btn btn-white btn-sm tooltip-primary' data-toggle="tooltip" data-original-title="Ver el estado del proceso de aprobacion" >
                        <img src="<?php echo $base_path; ?>/images/simad/ico_ver_detalle.png" width="25" height="25" align="middle" />Ver Estado de proceso
                    </a>
                <?php //}?>
                
                
                <?php //if($sf_user->checkPerm("COM_INTERNA_EDITAR", $currentUser)){?>
                    <a href="<?php echo $base_path; ?>/recibida.php/prov_aprobacion/index?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Ver las aprobaciones realizadas a este proveedor">
                        <img src="<?php echo $base_path; ?>/images/simad/ico_ver_detalle.png" alt="Ver a Aprobaciones" width="25" height="25" align="middle" />Ver a Aprobaciones
                    </a>
                <?php //}?>
                  
                <a href="<?php echo $base_path; ?>/recibida.php/proveedor/show?proveedor_id=<?php echo $prov_periodo_validez->getProveedorId() ?>" class='btn btn-white btn-sm tooltip-primary' data-toggle="tooltip" data-original-title="Regresar a los detallas de este proveedor">
                    <img src="<?php echo $base_path; ?>/images/simad/ico_cancelar.png" width="25" align="middle"  />Regresar a proveedor
                </a>

            <?php 
            if($sf_user->checkPerm("ENVIAR_ALERTA_RECORDATORIO", $currentUser)){
                echo jq_link_to_function(image_tag('/images/simad/ico_env_mail.png',array('border'=>"0",'width'=>"25",'align'=>"middle", 'class'=>'btn btn-white btn-sm tooltip-primary')).'Enviar Alertas','javascript:jQuery.OpenModalSIMAD("'.$base_path.'/recibida.php/prov_periodo_validez/alertaSend?prov_periodo_validez_id='.$prov_periodo_validez->getPrimaryKey().'", "960", "600")');
            }
            ?>
                                              

                </div>
            </div>
            
            <hr />
            
            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Proveedor:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_periodo_validez->getProveedor() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Inicial:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_periodo_validez->getFechaInicial(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Final:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_periodo_validez->getFechaFinal() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Tipo de Industria:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvTipoIndustriaId()) echo $prov_periodo_validez->getProvTipoIndustria() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Condicion de Expedicion:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProvCondicionExpedicionId())echo $prov_periodo_validez->getProvCondicionExpedicion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Rechazada Creacion:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvRechazadaCreacionId())echo $prov_periodo_validez->getProvRechazadaCreacion()->getDescripcion() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Aprobacion:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProvAprobacionId())echo $prov_periodo_validez->getProvAprobacion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Creacion Revisada:</strong></p></div>
						<div class="col-sm-7">
                        <p>
                            <?php 
                                if( $prov_periodo_validez->getProvRevisadoCreacionId())echo $prov_periodo_validez->getProvRevisadoCreacion()->getDescripcion(); 
                            ?>
                        </p>
                        </div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Condicion de Pago:</strong></p></div>
						<div class="col-sm-8"><p>
                            <?php if($prov_periodo_validez->getProvCondicionPagoId())echo $prov_periodo_validez->getProvCondicionPago() ?></p>
                        </div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Grupo de Tesoreria:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvGrupoTesoreriaId())echo $prov_periodo_validez->getProvGrupoTesoreria() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Aduana de Entrada:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProvAduanaEntradaId())echo $prov_periodo_validez->getProvAduanaEntrada() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Rechazada Creacion:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvRechazadaCreacionId())echo $prov_periodo_validez->getProvRechazadaCreacion()->getDescripcion() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Aprobacion para Creacion:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProbAprobadoParaCreacion())echo $prov_periodo_validez->getProbAprobadoParaCreacion()->getDescripcion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Grupo de Esquema:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvGrupoEsquemaId())echo $prov_periodo_validez->getProvGrupoEsquema() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Moneda:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProvMonedaPedidoId())echo $prov_periodo_validez->getProvMonedaPedido() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Tipo Contribuyente:</strong></p></div>
						<div class="col-sm-7"><p><?php if($prov_periodo_validez->getProvTipoContribuyenteId())echo $prov_periodo_validez->getProvTipoContribuyente() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Cuenta Asociada:</strong></p></div>
						<div class="col-sm-8"><p><?php if($prov_periodo_validez->getProvCuentaAsociadaId())echo $prov_periodo_validez->getProvCuentaAsociada() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Observaciones Generales:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_periodo_validez->getObservacionesGenerales() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha de Creacion en SAP:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $prov_periodo_validez->getFechaCreacionSap() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Codigo SAP:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $prov_periodo_validez->getCodigoSap() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Tipos de Retencion:</strong></p></div>
						<div class="col-sm-8"><p><?php foreach($prov_tipo_retencionaprobadas as $prov_tipo_retencionaprobada){echo $prov_tipo_retencionaprobada->getProvTipoRetencion()." | ";} ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Vias de Pago:</strong></p></div>
						<div class="col-sm-7"><p><?php foreach($prov_via_pago_aprobadas as $prov_via_pago_aprobada){echo $prov_via_pago_aprobada->getProvViaPago()." | ";} ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Documentacion Completa:</strong></p></div>
						<div class="col-sm-8"><p>
                                                    <?php  
                                                        if($prov_periodo_validez->getDocumentacionCompleta()=="")
                                                        {
                                                          echo "NO";
                                                        }
                                                        else
                                                        {
                                                          echo $prov_periodo_validez->getDocumentacionCompleta();
                                                        }
                                                    ?>
                                                </p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Finalizado Chack List:</strong></p></div>
						<div class="col-sm-7"><p>
                                                <?php  
                                                        if($prov_periodo_validez->getAvisoCheckList()=="")
                                                        {
                                                          echo "NO";
                                                        }
                                                        else
                                                        {
                                                          echo $prov_periodo_validez->getAvisoCheckList();
                                                        }
                                                ?>
                                                </p></div>
					</div>
				</div>

            </div>
            
            <hr />
            
            <!-- Opciones Detalle  SEGUNDA PARTE (LOS 9 PASOS)--> 
            <div class="col-sm-12 col-md-12">
				<div class="form-group">
                
                <?php 
                if($sf_user->checkPerm("ACTUALIZAR_PROV_ESTADO_DOCUMENTACION", $currentUser))
                {
                ?>
                    <?php if($prov_periodo_validez->getDocumentacionCompleta()=="SI"): ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/updateestadodocno?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>"  class='btn btn-white btn-sm tooltip-primary' data-toggle='tooltip' data-original-title='Establecer documentacion incompleta' >
                            <img src="<?php echo $base_path; ?>/images/simad/ico_atendido.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" align="middle" />Doc. Incompleta
                        </a>                        
                    <?php else:?>                    
                        <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/updateestadodocsi?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' data-toggle='tooltip' data-original-title='Establecer documentacion completa' >
                            <img src="<?php echo $base_path; ?>/images/simad/ico_atendido.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle"  />Doc. completa
                        </a>
                    <?php endif?>

                <?php 
                }
                ?>
                 
                <?php //if($mostrar_boton_paso_1){?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso1?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>"  class='btn btn-white btn-sm tooltip-primary'>
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle"/>1. Liberar para aprobar
                </a>
                <?php //}
                //if($mostrar_boton_paso_2){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso2?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="30" height="30" align="middle" />2. Aprueba Impuestos
                </a>
                <?php //}
                //if($mostrar_boton_paso_3){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso3?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle"/>3. Aprueba Contabilidad
                </a>
                <?php //}
                //if($mostrar_boton_paso_4){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso4?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para Aprobaciones y Check list" width="25" height="25" align="middle"/>4. Aprueba Tesoreria
                </a> 
                <?php //}
                //if($mostrar_boton_paso_5){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso5?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle"/>5. Aprueba Compras
                </a>
                <?php //}
                //if($mostrar_boton_paso_6){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso6?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle" />1. Aprueba Proveedores
                </a>
                <?php //}
                //if($mostrar_boton_paso_7){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso7?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle" />7. Aprobacion a la creacion
                </a>
                <?php //}
                //if($mostrar_boton_paso_8){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso8?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle" />8. Creacion correpondiente en SAP
                </a>
                <?php //}
                //if($mostrar_boton_paso_9){
                ?>
                <a href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/editarpaso9?prov_periodo_validez_id=<?php echo $prov_periodo_validez->getProvPeriodoValidezId() ?>" class='btn btn-white btn-sm tooltip-primary' >
                    <img src="<?php echo $base_path; ?>/images/simad/ico_ejecutar.png" alt="Liberar Proveedor para aprobaciones y check list" width="25" height="25" align="middle" />9. Revision de creacion en SAP
                </a>
                <?php //} ?>                             

                </div>
            </div>
                   
  	     </div>
	</div>
</div>
<?php echo javascript_tag("     	   
  function openDialogDoc(id,accion) {	  	  	 
	  var win = new Window('alertas',{title: '', className: 'alphacube', 
								  bottom:50, left:100, width:450, height:300, 
								  resizable: true, url: accion+id, 		   								 
								  showEffectOptions: {duration:1.0} ,wiredDrag: true})
	win.show();
	win.setDestroyOnClose();
    win.showCenter();						
  }
    
  function canClose() {  	
  	Windows.getWindow(\"alertas\").close();		
  }
  
") ?>