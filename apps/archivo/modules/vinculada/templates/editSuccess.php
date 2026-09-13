<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
use_helper('jQuery');
?>

<div class="row">
	<div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
    	<div class="panel-heading">
        	<div class="panel-title">Crear / Editar Vinculada</div>
      	</div>
      	<!-- Contenedor Contenido Formulario-->
      	<div class="panel-body">
      		<?php 
			echo form_tag('vinculada/update', array('name'=>'form1', 'class' => 'form-horizontal form-groups-bordered validate')); 
			echo object_input_hidden_tag($vinculada, 'getVinculadaId'); 
			echo input_hidden_tag('transferencia_id', $transferencia_id);
			?>
			<div class="form-group">
			  <!-- Unidad Documental -->
			  <label for="nombre" class="col-sm-2 control-label">Unidad Documental</label>
			  <div class="col-sm-6">
			    <div class="input-group">
			      <?php 
			      echo input_hidden_tag('unidad_documental', $unidad_documental);
			      if($nombre_funcion == "Crear"){
			        $descripcion = "";
			      }
			      echo input_tag('nombre', $descripcion, array('readonly'=>'readonly', 'class'=>'form-control input-sm required'));
			      ?>
			      <div class="input-group-btn">         
			          <button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/archivo.php/vinculada/unidad', 800, 600); return false;">Seleccionar</button>
			          <button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('nombre');jQuery.LimpiarCampoFormulario('unidad_documental');"><i class="entypo-cancel-circled"></i></button>
			      </div>
			      </div>
			    </div>
			</div>
			<div class="form-group">
			  <!-- Tipo Documental -->
			  <label for="tipo_documental" class="col-sm-2 control-label">Tipo Documental</label>
			  <div class="col-sm-6">
			  	<div id="contenedor_tipos">
	  			<?php
	  			if($tipo_documental == 0){
	  			?>
	  				<select name="tipo_documental" disabled="disabled" id="tipo_documental" class="form-control input-sm">
	                	<option value="">Seleccione Unidad Documental...</option>
	                </select>
	            <?php
	            }else{
	            ?>
            		<select name="tipo_documental" id="tipo_documental" class="form-control input-sm required">
					<?php 
					$tipos_documentales = $consulta;
					foreach($tipos_documentales as $tipo){
						echo "<option value='".$tipo->getTipodocumentalId()."'";
						if($tipo->getTipodocumentalId() == $tipo_documental){
							echo " selected ";
						}
						echo ">".$tipo->getDescripcion()."</option>";
					}
					?>
					</select>
				<?php
				}
				?>
  				</div>
			    
			   </div>
			</div>
			<div class="form-group">
			  	<!-- Descripción  -->
			  	<label for="descripcion" class="col-sm-2 control-label">Descripción</label>
			  	<div class="col-sm-6">
			      <?php echo object_input_tag($vinculada, 'getDescripcion', array('class'=>'form-control input-sm required')); ?>
			    </div>
			</div>
			<div class="form-group">  
				<!-- Ruta -->  
				<label for="ruta" class="col-sm-2 control-label">Archivo</label>
				<div class="col-sm-6">
					<div class="input-group">
					<?php 
					$ruta = explode(',', $vinculada->getRuta());
					$count_reg = 0;
					$archivos = "";
					foreach($ruta as $files){			   
						if(trim($files) != ""){
							if($count_reg == 0){
								$archivos .= basename($files);
                			}else{
                  				$archivos .= ','.basename($files);  
                			}
                			$count_reg++;
			  			}              
					}
			 		echo input_tag('ruta', trim($archivos), array('class' => 'form-control input-sm','readonly' => true));
					?>
					<div class="input-group-btn">
						<button type="button" class="btn btn-primary btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php print $base_path;?>/archivo.php/vinculada/file', 500, 300); return false;">Anexar</button>
						<button type="button" class="btn btn-default btn-sm" onclick="javascript:jQuery.LimpiarCampoFormulario('delete_ruta');jQuery.LimpiarCampoFormulario('ruta');"><i class="entypo-cancel-circled"></i></button>
	                </div>
	              </div>
				</div>
			</div>
			<div class="form-group">
			  	<!-- Folios  -->
			  	<label for="folios" class="col-sm-2 control-label">Folios</label>
			  	<div class="col-sm-1">
			      <?php echo object_input_tag($vinculada, 'getFolios', array('class'=>'form-control input-sm required')); ?>
			    </div>
			</div>
			<div class="form-group">
	          <!-- Botonera -->
	          <div class="col-sm-offset-4 col-sm-5">
	            <button type="submit" class="btn btn-success">Guardar Vinculada</button>
	            <a href="<?php print url_for('vinculada/list');?>"><button type="button" class="btn btn-default">Cancelar</button></a>
	          </div>
	        </div>
	        <div class="clear"></div>
  		</div>
	   </div>
    </div>
</div>
