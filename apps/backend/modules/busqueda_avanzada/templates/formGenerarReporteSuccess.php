<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

$currentFormDeleteImg    	= "ARCHIVO_".$modulo."_ELIMINAR_IMAGEN";
$currentFormDeleteTipo    	= $modulo."_ELIMINAR_CONTENIDO";
use_helper('Object','jQuery');
?>
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/typeahead.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/fileinput.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/dropzone/dropzone.js"></script>
<hr/>
<div class="row">
	<div class="col-sm-offset-2 col-md-8">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
				Crear Reporte
			</div>
		</div>

      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<?php
    			echo form_tag('busqueda_avanzada/guardarReporte', array('name'=>'form1','multipart' => true,'class' => 'form-horizontal form-groups-bordered validate')); 
    			echo input_hidden_tag('usuario_reporte_id', $usuario_reporte_id);
			?>
            
			<div class="row" style="padding-left: 20px;">
				<div class="col-sm-10">
					<div class="form-group">
					<!-- Nombre Reporte -->
					<label for="nombre_reporte" class="control-label" style="font-size:20px;">Nombre Reporte<span class="ctrlreq">(*)</span></label>
						<?php 
							echo input_tag('nombre_reporte', '', array('class'=>'form-control input-sm required'));                
						?>
					</div>
				</div>
			</div>
				
			<div class="row" style="padding-left: 20px;">
				<div class="col-sm-10">
					<div class="form-group">
						<!-- Descripcion Reporte -->
						<label for="descripcion_reporte" class="control-label" style="font-size:20px;">Descripci&oacute;n<span class="ctrlreq">(*)</span></label>
						<?php      
							echo textarea_tag('descripcion_reporte', '', array ('size' => '50x2','name'=>'descripcion_reporte', 'id'=>'descripcion_reporte', 'class'=>'form-control input-sm required')); 
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
	          <!-- Botonera -->
	          <div class="col-sm-offset-2 col-sm-10">
				<a data-toggle="tooltip" data-original-title="Guardar reporte" href="#" class="btn btn-success" id="expFinReporte">Guardar Reporte</a>          
	          </div>
	        </div>
	        <div class="clear"></div>
			</form>
		</div>

	   </div>
	</div>
</div>
