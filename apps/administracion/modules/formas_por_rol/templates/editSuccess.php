<?php use_helper('Object') ?>
<?php use_helper('jQuery')?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Crear/Editar Privilegios por Perfil
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      
        <?php 
        echo form_tag('formas_por_rol/update', array('name'=>'form1','role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
        echo object_input_hidden_tag($rol_privilegio, 'getRolprivilegioId');
        ?>
		
		<div class="form-group">
          <!-- Nombre: -->
          <label for="ldependencia_id" class="col-sm-1 control-label">Perfil:</label>
          <div class="col-sm-10">              
            <?php echo object_select_tag($rol_privilegio, 'getRolId', array ('related_class' => 'Rol','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',)) ?>
          </div>        
		</div>
		
        <div class="form-group">
          <!-- Modulo: -->
          <label for="ltipoprocedimiento_id" class="col-sm-1 control-label">Privilegio:</label>
          <div class="col-sm-10">
            <select name="forma_id" id="forma_id"  class="form-control input-sm required select2">
                <option value="">Seleccione...</option>
				<?php 	
					foreach($formas as $forma)
					{
						echo "<option value='".$forma->getFormaId()."'";
						if($forma->getFormaId() == $forma_seleccionada)
						{
							echo " selected ";
						}        
						echo ">".$forma->getModulo()->getDescripcion()." - ".$forma->getDescripcion()."</option>";
					}
				?>
			</select> 
          </div>
		</div>
		<div class="form-group">
			<!-- Botonera -->
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" class="btn btn-success"><?php echo $forma->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"?> </button> 
			</div> 
		</div>
		<div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>