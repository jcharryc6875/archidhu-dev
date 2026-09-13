<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');

use_helper('Object','jQuery');
?>

<div class="row">
	<div class="col-md-12">
		<!-- Contenedor Pagina -->
		<div class="panel panel-gradient" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">
					<?php 
					echo $file_tree->getPrimaryKey() ? "Editar " : "Crear ";  
					?> Carpeta Contenedora
				</div>
			</div>

			<div class="col-md-12">
				<?php
					if($sf_user->hasFlash('error'))
					{
						?>
						<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('error') ?></div>
						<?php
					}

					if($sf_user->hasFlash('error_expclose'))
					{
						?>
						<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('error_expclose') ?></div>
						<?php
					}

					if($sf_user->hasFlash('messages_info'))
					{
						?>
						<div class="alert alert-danger"><strong>Opss Error! </strong><?php echo $sf_user->getFlash('messages_info') ?></div>
						<?php
					}
				?>
			</div>

			<!-- Contenedor Contenido Formulario-->
			<div class="panel-body">
				<?php
					echo form_tag('contenido_documental/updateFileTree', array('name'=>'formCreaFtree', 'id'=>'formCreaFtree', 'class' => 'form-groups-bordered validate')); 
					echo input_hidden_tag('contenidodocfiletree_id', $file_tree->getPrimaryKey());
					echo input_hidden_tag('unidaddocumental_id', $unidaddocumental_id);
				?>

				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
						<!-- Nombre -->
						<label for="lbgetNombre" class="control-label">Nombre<span class="ctrlreq">(*)</span>:</label>
							<input type="text" name="nombre" id="nombre" data-validate = "folder-treename" value="<?php echo !empty($file_tree->getNombre()) ? $file_tree->getNombre() : '' ; ?>" class="required form-control input-sm">   <!-- formatcustom -->
							<?php echo !empty($file_tree->getNombre()) ? $file_tree->getNombre() : '' ; ?>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
						<!-- Descripcion -->
						<label for="lbgetDescripcion" class="control-label">Descripci&oacute;n<span class="ctrlreq">(*)</span>:</label>
							<textarea name="descripcion" id="descripcion" class="form-control required" cols="50" rows="2">
								<?php echo !empty($file_tree->getDescripcion()) ? $file_tree->getDescripcion() : '' ; ?>
							</textarea>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
						<!-- File Tree -->
						<label for="lbparent_id" class="control-label">Carpeta Padre:</label>
							<select name="parent_id" id="parent_id" class="validatstrclose form-control input-sm select2">
								<option value="">Sin nivel superior...</option>
								<?php 
									foreach($file_trees_parents as $row)
									{
										echo "<option value='".$row->getPrimaryKey()."'";
										if($row->getPrimaryKey() == $file_tree->getParentId())
										{
											echo " selected ";
										}
										echo ">". $row->getNombre() . " - " . $row->getDescripcion() . "</option>";
									}
								?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
						<!-- Es Actual -->
						<label for="lbgetDescripcion" class="control-label">Es Actual:</label>
							<select name="es_actual" id="es_actual" class="form-control input-sm" aria-invalid="false">
								<option value="1" selected="selected">SI</option>
								<option value="2">NO</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="form-group">
						<!-- Botonera -->
						<div class="col-sm-offset-4 col-sm-8">
							<button type="submit" class="btn btn-success">Guardar Datos</button>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				</form>
			</div>
		</div>
	</div>
</div>

