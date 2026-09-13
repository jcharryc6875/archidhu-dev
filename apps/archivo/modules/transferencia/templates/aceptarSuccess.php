<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Aceptar Transferencia
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
            <?php 
            echo form_tag('transferencia/transferir', array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
            echo object_input_hidden_tag($transferencia, 'getTransferenciaId'); 
            ?>
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">                
                    <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Cerrar registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
                        <img src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" title="" width="25" align="middle" />Cerrar
                    </a>                   
                </div>
            </div>
            <hr />
			<!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Origen Transferencia:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $transferencia->getOrigentransferencia()->getDescripcion(); ?></p></div>
					</div>					                  
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Destino Transferencia:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $transferencia->getDestinotransferencia()->getDescripcion(); ?></p></div>
					</div>					                  
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Solicitud:</strong></p></div>
						<div class="col-sm-8"><p><?php echo $transferencia->getFechaCreacion(); ?></p></div>
					</div>					                  
				</div>
                
                <?php
                if($transferencia->getOrigentransferenciaId() <= 4 || $transferencia->getOrigentransferenciaId() == 7){
                ?>
                    <div class="row">
    					<div class="col-sm-6">
    						<div class="col-sm-4"><p><strong>Unidad Documental:</strong></p></div>
    						<div class="col-sm-8"><p><?php echo $transferencia->getUnidadDocumental()->getTitulo(); ?></p></div>
    					</div>					                  
    				</div>
                    <div class="row">
    					<div class="col-sm-6">
    						<div class="col-sm-4"><p><strong>Tipo Documental:</strong></p></div>
    						<div class="col-sm-8"><p><?php echo $transferencia->getTipoDocumental(); ?></p></div>
    					</div>					                  
    				</div>
                <?php
                }else{
                ?>
                    <div class="row">
    					<div class="col-sm-6">
    						<div class="col-sm-4"><p><strong>Ubicaci&oacute;n:</strong></p></div>
    						<div class="col-sm-8"><p><?php echo input_tag('ubicacion', '', array('class' => 'form-control input-sm required')); ?></p></div>
    					</div>					                  
    				</div>
                <?php
                }
                ?>
                
                <div class="form-group">
                  <!-- Botonera -->
                  <div class="col-sm-offset-4 col-sm-5">
                    <button type="submit" class="btn btn-success">Aceptar Transferencia</button>
                  </div>
                </div>
                <div class="clear"></div>
            </div>
  	     </div>
	   </div>
    </div>
</div>
</form>