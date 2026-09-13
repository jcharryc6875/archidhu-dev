<?php

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('jQuery'); 
?>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalle del Proveedor
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">

                <?php
					if($sf_user->checkPerm("proveedor/edit", $currentUser)){
						echo link_to(image_tag('/images/simad/ico_editar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Editar','proveedor/edit?proveedor_id='.$proveedor->getProveedorId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Editar"));
					}
                ?>  

                <?php 
                    echo link_to(image_tag('/images/simad/ico_add-time.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Crear Periodo de Validez','prov_periodo_validez/create?proveedor_id='.$proveedor->getProveedorId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Crear Periodo de Validez"));
                ?>              

                <?php 
					if($sf_user->checkPerm("PROVEEDOR_DEJAR_PENDIENTE", $currentUser)){
						echo link_to(image_tag('/images/simad/ico_uncheck.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Dejar como pendiente','proveedor/updateapendiente?proveedor_id='.$proveedor->getProveedorId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Dejar como pendiente"));
					}
                ?> 
   
                <?php 
					if($sf_user->checkPerm("PROVEEDOR_ANULAR", $currentUser) && $proveedor->getProvEstadoId() != 6 && $proveedor->getProvEstadoId() != 4){ 
						echo link_to(image_tag('/images/simad/ico_anular.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Anular','proveedor/updateaanulado?proveedor_id='.$proveedor->getProveedorId(),array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Anular"));
					}
                ?>
                
				<?php 
					if($sf_user->checkPerm("PROVEEDOR_PUBLICAR_DIRECTORIO", $currentUser)){ 
						//echo link_to(image_tag('/images/simad/ico_asignar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Enviar Directorio','proveedor/publicar?proveedor_id='.$proveedor->getProveedorId());
						echo jq_link_to_function(image_tag('/images/simad/ico_asignar.png',array('border'=>"0",'width'=>"25",'align'=>"middle")).'Enviar Directorio', 'javascript:jQuery.OpenModalSIMAD("'.url_for('proveedor/publicar?proveedor_id='.$proveedor->getProveedorId()).'", "960", "640")',array('class' => 'btn btn-white btn-sm tooltip-primary',"data-toggle" => "tooltip", "data-original-title" => "Publicar en el directorio corporativo"));
					}
                ?>				

                </div>
            </div>
            
            <hr />
            
            <!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Nombre:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $proveedor->getNombre() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Nit:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $proveedor->getNit() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Representante Legal:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $proveedor->getRepresentanteLegal() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Identificacion r1:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $proveedor->getIdentificacionRl() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Direccion:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $proveedor->getDireccion() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Telefono:</strong></p></div>
						<div class="col-sm-7"><p><?php echo $proveedor->getTelefono() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Naturaleza Juridica:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $proveedor->getNaturalezaJuridica() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Pais:</strong></p></div>
						<div class="col-sm-7"><p><?php if($proveedor->getPaisId())echo $proveedor->getPais()->getNombre() ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha de Ingreso:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $proveedor->getFechaIngreso() ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Estado:</strong></p></div>
						<div class="col-sm-7"><p><?php if($proveedor->getProvEstadoId())echo $proveedor->getProvEstado()->getDescripcion() ?></p></div>
					</div>
				</div>
                <!--hr />
    			<!-- Opciones Detalle -->
    			<!--div class="col-sm-12 col-md-12">
    				<div class="form-group">
                    </div>
                </div-->
            </div>
  	     </div>
	   </div>
       <?php 
            include_partial('listSuccess',array('prov_periodo_validezList'=>$prov_periodo_validezList)); 
       ?>
    </div>    
</div>