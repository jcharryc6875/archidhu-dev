<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
use_helper('jQuery','Object');
?>
<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Consultar Contrat&iacute;sta
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('control_contratistas/index', array('name'=>'form1', 'role' => 'form', 'method' => 'post' ,'class' => 'form-horizontal form-groups-bordered validate'));
        ?>
        
        <div class="form-group">  
			<!-- Estado Contratista -->  
			<label for="lb001" class="col-sm-1 control-label">Estado Contrat&iacute;sta</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getEstadocontratistaId', array('class' => 'form-control input-sm', 'related_class' => 'EstadoContratista','include_custom'=>'Seleccione...')); ?>
			</div>
            
            <!-- Tipo Contrato -->  
			<label for="lb002" class="col-sm-1 control-label">Tipo Contrato</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getTipocontratoId', array('class' => 'form-control input-sm', 'related_class' => 'TipoContrato', 'peer_method'=>'getTipoContratoOrder','include_custom'=>'Seleccione...')); ?>
			</div>
            
            <!-- Dependencia -->  
			<label for="lb003" class="col-sm-1 control-label">Dependencia</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getDependenciaId', array('class' => 'form-control input-sm', 'related_class' => 'Dependencia', 'peer_method'=>'getDependenciaAllJoin','include_custom'=>'Seleccione...')); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Nombres -->  
			<label for="lb004" class="col-sm-1 control-label">Nombres</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNombre', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Primer Apellido -->  
			<label for="lb005" class="col-sm-1 control-label">Primer Apellido</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getPrimerApellido', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Segundo Apellido -->  
			<label for="lb006" class="col-sm-1 control-label">Segundo Apellido</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getSegundoApellido', array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Cargo -->  
			<label for="lb007" class="col-sm-1 control-label">Cargo</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getCargoId', array('class' => 'form-control input-sm','peer_method'=>'getOrdenCargo','related_class' => 'Cargo','include_custom'=>'Seleccione...',)); ?>
			</div>
            
            <!-- Genero -->  
			<label for="lb008" class="col-sm-1 control-label">Genero</label>
			<div class="col-sm-3">
                <?php $generos = array('MASCULINO'=>'MASCULINO','FEMENINO'=>'FEMENINO'); ?>
				<?php echo select_tag('genero' , options_for_select($generos,$control_contratistas->getGenero(),array('include_custom'=>'Seleccione...',)),array('class' => 'form-control input-sm','include_custom'=>'Seleccione...',)); ?>
			</div>
            
            <!-- Tipo Identificación -->  
			<label for="lb009" class="col-sm-1 control-label">Tipo Identificaci&oacute;n</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getTipoidentificacionId', array('class' => 'form-control input-sm','related_class' => 'TipoIdentificacion','include_custom'=>'Seleccione...',)); ?>
			</div>                        
		</div>
        
        <div class="form-group">  
			<!-- Numero Identificacion -->  
			<label for="lb010" class="col-sm-1 control-label">Numero Identificacion</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNumeroIdentificacion', array('class' => 'form-control input-sm','data-mask'=>'decimal',)); ?>
			</div>
            
            <!-- Ubicacion Laboral -->  
			<label for="lb011" class="col-sm-1 control-label">Ubicaci&oacute;n Laboral</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getUbicacionLaboral', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- EPS -->  
			<label for="lb012" class="col-sm-1 control-label">EPS</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getEps', array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Fondo Pensiones -->  
			<label for="lb013" class="col-sm-1 control-label">Fondo Pensiones</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getFondoPensiones', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Nacionalidad -->  
			<label for="lb014" class="col-sm-1 control-label">Nacionalidad</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNacionalidad', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Libreta Militar -->  
			<label for="lb015" class="col-sm-1 control-label">Libreta Militar</label>
			<div class="col-sm-3">
				<?php echo input_tag('numero_militar', $libreta_militar[0], array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">
          <!-- Fecha Nacimiento -->
          <label for="lb0040" class="col-sm-1 control-label">Fecha Nacimiento</label>
          <div class="col-sm-3">
            <div class="input-group">
              <?php
                echo input_tag('fecha_nacimiento_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="lb0041" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-3">
            <div class="input-group">
              <?php
                echo input_tag('fecha_nacimiento_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <!-- Fecha Creación -->
          <label for="lb0042" class="col-sm-1 control-label">Fecha Creaci&oacute;n</label>
          <div class="col-sm-3">
            <div class="input-group">
              <?php
                echo input_tag('fecha_creacion_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>

          <label for="lb0043" class="col-sm-1 control-label">Hasta</label>
          <div class="col-sm-3">
            <div class="input-group">
              <?php
                echo input_tag('fecha_creacion_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
              ?>
              <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
            </div>
          </div>
        </div>
        
        <div class="form-group">  
			<!-- Ciudad Nacimiento -->  
			<label for="lb019" class="col-sm-1 control-label">Ciudad Nacimiento</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getCiudadId', array('class' => 'form-control input-sm','related_class' => 'Ciudad', 'peer_method'=>'getAllCiudad', 'include_custom'=>'Seleccione...',)); ?>
			</div>
            
            <!-- Email -->  
			<label for="lb025" class="col-sm-1 control-label">Email</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getEmail', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Titulo Educacion Basica -->  
			<label for="lb027" class="col-sm-1 control-label">Titulo Educacion Basica</label>
			<div class="col-sm-3">
				<?php echo textarea_tag('titulo_obtenido', $educacion_basica[3], array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
            <!-- Fecha Grado Basica -->
            <label for="lb0042" class="col-sm-1 control-label">Fecha Grado Basica</label>
            <div class="col-sm-3">
                <div class="input-group">
                <?php
                    echo input_tag('fecha_gradobasica_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>

            <label for="lb0043" class="col-sm-1 control-label">Hasta</label>
            <div class="col-sm-3">
                <div class="input-group">
                <?php
                    echo input_tag('fecha_gradobasica_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            
            <!-- Titulo Educacion Basica -->  
			<label for="lb030" class="col-sm-1 control-label">Nombre o Titulo Obtenido</label>
			<div class="col-sm-3">
				<?php echo object_textarea_tag($control_contratistas, 'getTituloUniversitario', array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
            <!-- Fecha Terminacion -->
            <label for="lb0042" class="col-sm-1 control-label">Fecha Terminaci&oacute;n</label>
            <div class="col-sm-3">
                <div class="input-group">
                <?php
                    echo input_tag('fecha_gradosuperior_inicial', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>

            <label for="lb0043" class="col-sm-1 control-label">Hasta</label>
            <div class="col-sm-3">
                <div class="input-group">
                <?php
                    echo input_tag('fecha_gradosuperior_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                ?>
                <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            
            <!-- Tarjeta Profesional -->  
			<label for="lb032" class="col-sm-1 control-label">Tarjeta Profesional</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNumeroTarjeta', array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">
          <!-- Botonera -->
          <div class="col-sm-offset-4 col-sm-5">
            <button type="submit" class="btn btn-success">Consultar Contratistas</button>
            <?php echo button_to('Cancelar','control_contratistas/index',array('class' => 'btn btn-red ')); ?>
          </div>
        </div>
        <div class="clear"></div>
        </form>
        </div>
      </div>
    </div>
</div>