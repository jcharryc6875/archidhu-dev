<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');  
use_helper('jQuery','Object');
?>
<div class="row">
  <div class="col-md-12">
  <?php include_once("_navbar_contratistas.php"); ?>
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          <?php echo !$control_contratistas->getPrimaryKey() ? 'Crear' : 'Editar' ?> Contrat&iacute;sta
        </div>
      </div>

      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('control_contratistas/update', array('name'=>'form1', 'role' => 'form', 'method' => 'post' ,'class' => 'form-horizontal form-groups-bordered validate')); 
        echo object_input_hidden_tag($control_contratistas, 'getControlcontratistasId');
        ?>
        
        <div class="form-group">  
			<!-- Estado Contratista -->  
			<label for="lb001" class="col-sm-1 control-label">Estado Contrat&iacute;sta</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getEstadocontratistaId', array('class' => 'form-control input-sm required', 'related_class' => 'EstadoContratista','include_custom'=>'Seleccione...')); ?>
			</div>
            
            <!-- Tipo Contrato -->  
			<label for="lb002" class="col-sm-1 control-label">Tipo Contrato</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getTipocontratoId', array('class' => 'form-control input-sm required', 'related_class' => 'TipoContrato', 'peer_method'=>'getTipoContratoOrder','include_custom'=>'Seleccione...')); ?>
			</div>
            
            <!-- Dependencia -->  
			<label for="lb003" class="col-sm-1 control-label">Dependencia</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getDependenciaId', array('class' => 'form-control input-sm required', 'related_class' => 'Dependencia', 'peer_method'=>'getDependenciaAllJoin','include_custom'=>'Seleccione...')); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Nombres -->  
			<label for="lb004" class="col-sm-1 control-label">Nombres</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNombre', array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- Primer Apellido -->  
			<label for="lb005" class="col-sm-1 control-label">Primer Apellido</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getPrimerApellido', array('class' => 'form-control input-sm required',)); ?>
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
				<?php echo object_select_tag($control_contratistas, 'getCargoId', array('class' => 'form-control input-sm required','peer_method'=>'getOrdenCargo','related_class' => 'Cargo','include_custom'=>'Seleccione...',)); ?>
			</div>
            
            <!-- Genero -->  
			<label for="lb008" class="col-sm-1 control-label">Genero</label>
			<div class="col-sm-3">
                <?php $generos = array('MASCULINO'=>'MASCULINO','FEMENINO'=>'FEMENINO'); ?>
				<?php echo select_tag('genero' , options_for_select($generos,$control_contratistas->getGenero(),array('include_custom'=>'Seleccione...',)),array('class' => 'form-control input-sm required','include_custom'=>'Seleccione...',)); ?>
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
				<?php echo object_input_tag($control_contratistas, 'getNumeroIdentificacion', array('class' => 'form-control input-sm required','data-mask'=>'decimal',)); ?>
			</div>
            
            <!-- Ubicacion Laboral -->  
			<label for="lb011" class="col-sm-1 control-label">Ubicaci&oacute;n Laboral</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getUbicacionLaboral', array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- EPS -->  
			<label for="lb012" class="col-sm-1 control-label">EPS</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getEps', array('class' => 'form-control input-sm required',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Fondo Pensiones -->  
			<label for="lb013" class="col-sm-1 control-label">Fondo Pensiones</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getFondoPensiones', array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- Nacionalidad -->  
			<label for="lb014" class="col-sm-1 control-label">Nacionalidad</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNacionalidad', array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- Libreta Militar -->  
			<label for="lb015" class="col-sm-1 control-label">Libreta Militar</label>
			<div class="col-sm-3">
				<?php echo input_tag('numero_militar', $libreta_militar[0], array('class' => 'form-control input-sm required',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Distrito -->  
			<label for="lb016" class="col-sm-1 control-label">Distrito</label>
			<div class="col-sm-3">
				<?php echo input_tag('distrito', $libreta_militar[1], array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Clase -->
			<label for="lb017" class="col-sm-1 control-label">Clase</label>
			<div class="col-sm-3">
                <?php $select_objs = array('PRIMERA'=>'PRIMERA','SEGUNDA'=>'SEGUNDA'); ?>
				<?php echo select_tag('clase_libreta' , options_for_select($select_objs,$libreta_militar[2],array('include_custom'=>'Seleccione...',)),array('class' => 'form-control input-sm')); ?>
			</div>
            
            <!-- Fecha Nacimiento -->  
			<label for="lb018" class="col-sm-1 control-label">Fecha Nacimiento</label>
			<div class="col-sm-2">
				<div class="input-group">
                    <?php
                    $fecha_nacimiento = '';
                    if(trim($control_contratistas->getFechaNacimiento())){
                        $fecha_nacimiento = $control_contratistas->getFechaNacimiento("Y-m-d");
                    }
                    echo input_tag('fecha_nacimiento', $fecha_nacimiento, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                    ?>                  
                    <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>                  
	            </div>				
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Ciudad Nacimiento -->  
			<label for="lb019" class="col-sm-1 control-label">Ciudad Nacimiento</label>
			<div class="col-sm-3">
				<?php echo object_select_tag($control_contratistas, 'getCiudadId', array('class' => 'form-control input-sm required','related_class' => 'Ciudad', 'peer_method'=>'getAllCiudad', 'include_custom'=>'Seleccione...',)); ?>
			</div>
            
            <!-- Estado Civil -->
			<label for="lb020" class="col-sm-1 control-label">Estado Civil</label>
			<div class="col-sm-3">
				<?php echo select_tag('estado_civil' , options_for_select($estados_civiles,$estado_civil,array('include_custom'=>'Seleccione...',)),array('class' => 'form-control input-sm')); ?>
			</div>
            
            <!-- Numero Hijos -->  
			<label for="lb021" class="col-sm-1 control-label">Numero Hijos</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNumeroHijos', array('class' => 'form-control input-sm','data-mask'=>'decimal',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Direccion Correspondencia -->  
			<label for="lb022" class="col-sm-1 control-label">Direccion Correspondencia</label>
			<div class="col-sm-3">
				<?php echo input_tag('direccion_envio', $direccion_envio[0], array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- Municipio -->
			<label for="lb023" class="col-sm-1 control-label">Municipio</label>
			<div class="col-sm-3">
				<?php echo input_tag('municipio_envio', $direccion_envio[1], array('class' => 'form-control input-sm required',)); ?>
			</div>
            
            <!-- Departamento -->  
			<label for="lb024" class="col-sm-1 control-label">Departamento</label>
			<div class="col-sm-3">
				<?php echo input_tag('dpto_envio', $direccion_envio[2], array('class' => 'form-control input-sm required',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Email -->  
			<label for="lb025" class="col-sm-1 control-label">Email</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getEmail', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Educacion Basica -->
			<label for="lb026" class="col-sm-1 control-label">Educacion Basica</label>            
            <div class="col-sm-3">
				<div class="checkbox checkbox-replace color-red">
                    <?php echo checkbox_tag('primaria','Primaria',$educacion_basica[0]) ?>
					<label>Primaria</label>                    
				</div>
				
                <div class="checkbox checkbox-replace color-blue">
                    <?php echo checkbox_tag('secundaria','Secundaria',$educacion_basica[1]) ?>
					<label>Secundaria</label>                    
				</div>
                
                <div class="checkbox checkbox-replace color-green">
                    <?php echo checkbox_tag('media','Media',$educacion_basica[2]) ?>
					<label>Media</label>                    
				</div>
			</div>
            
            <!-- Titulo Educacion Basica -->  
			<label for="lb027" class="col-sm-1 control-label">Titulo Educacion Basica</label>
			<div class="col-sm-3">
				<?php echo textarea_tag('titulo_obtenido', $educacion_basica[3], array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group">  
			<!-- Fecha Grado Basica -->  
			<label for="lb028" class="col-sm-1 control-label">Fecha Grado Basica</label>
			<div class="col-sm-3">
				<div class="input-group">
                    <?php
                    $fecha_grado_basica = '';
                    if(trim($control_contratistas->getFechaGradoBasica())){
                        $fecha_grado_basica = $control_contratistas->getFechaGradoBasica("Y-m-d");
                    }
                    echo input_tag('fecha_grado_basica', $fecha_grado_basica, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                    ?>                  
                    <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>                  
	            </div>				
			</div>
            
            <!-- Educacion Superior -->
			<label for="lb029" class="col-sm-1 control-label">Educacion Superior</label>            
            <div class="col-sm-3">
				<div class="color-red">
                    <label>Modalidad</label>
                    <?php echo input_tag('modalidad', $educacion_superior[0], array ('size' => 20,'class' => 'form-control input-sm',)); ?>					                    
				</div>
				
                <div class="color-blue">
                    <label>Semestres Aprobados</label>
                    <?php echo input_tag('semestres_aprobados', $educacion_superior[1], array ('size' => 10,'class' => 'form-control input-sm','data-mask'=>'decimal')); ?>					                    
				</div>
                
                <div class="color-green">
                    <?php $esta_graduado = array('SI'=>'SI','NO'=>'NO') ?>
                    <label>Graduado</label>   
                    <?php echo select_tag('esta_graduado' , options_for_select($esta_graduado,$educacion_superior[2]),array('class' => 'form-control input-sm'))?>
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
			<label for="lb031" class="col-sm-1 control-label">Fecha Terminacion</label>
			<div class="col-sm-3">
				<div class="input-group">
                    <?php
                    $fecha_grado_universitario = '';
                    if(trim($control_contratistas->getFechaGradoUniversitario())){
                        $fecha_grado_universitario = $control_contratistas->getFechaGradoUniversitario("Y-m-d");
                    }
                    echo input_tag('fecha_grado_universitario', $fecha_grado_universitario, array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
                    ?>                  
                    <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>                  
	            </div>				
			</div>
            
            <!-- Tarjeta Profesional -->  
			<label for="lb032" class="col-sm-1 control-label">Tarjeta Profesional</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getNumeroTarjeta', array('class' => 'form-control input-sm',)); ?>
			</div>
            
            <!-- Otros Idiomas -->  
			<label for="lb033" class="col-sm-1 control-label">Otros Idiomas</label>
			<div class="col-sm-3">
				<?php echo object_input_tag($control_contratistas, 'getOtrosIdiomas', array('class' => 'form-control input-sm',)); ?>
			</div>
		</div>
        
        <div class="form-group" id="accordion">
          <!-- Upload images -->
          <label for="lb034" class="col-sm-1 control-label">Experiencia Laboral</label>
          <div class="panel col-sm-10 panel-primary">             
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" style="cursor: pointer;">
    			<div class="panel-title">                                       
                      <strong>Experiencia( haga clic para abrir)</strong>
    			</div>
    		</div>
            <div id="collapseOne" class="panel-collapse collapse" data-collapsed="1">
                <div class="panel-body">
                    <table>
                        <tr>
                            <th></th>
                            <th colspan="2"><b>Experiencia</b></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th style="border:solid windowtext 1.0pt;"><b>Años</b></th>
                            <th style="border:solid windowtext 1.0pt;"><b>Meses</b></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">SERVIDOR PUBLICO</th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('spuanos', $experiencia_laboral[0], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('spumeses', $experiencia_laboral[1], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">EMPLEADO SECTOR PRIVADO</th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('espranos', $experiencia_laboral[2], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('esprmeses', $experiencia_laboral[3], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">TRABAJADOR INDEPENDIENTE</th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('tianos', $experiencia_laboral[4], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('timeses', $experiencia_laboral[5], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">TOTAL TIEMPO EXPERIENCIA</th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('sumanos', $experiencia_laboral[6], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                            <th style="border:solid windowtext 1.0pt;"><?php echo input_tag('summeses', $experiencia_laboral[7], array ('size' => 5,'style' => 'text-align: center;')); ?></th>
                        </tr>
                    </table>
                </div>
            </div>
          </div>
        </div>
        
        <div class="form-group">
	          <!-- Botonera -->
	          <div class="col-sm-offset-2 col-sm-10">
	            <button type="submit" class="btn btn-success"><?php echo $control_contratistas->getPrimaryKey() ? "Guardar Cambios" : "Guardar Registro"; ?></button>
	            <?php    	            
    	            if ($control_contratistas->getPrimaryKey()){	                               
                        echo button_to("Eliminar Contenido",'control_contratistas/delete?controlcontratistas_id='.$control_contratistas->getPrimaryKey(), array('class' => 'btn btn-red btn-sm'));
                        echo "&nbsp;";
                        echo button_to("Regresar Lista",'control_contratistas/index', array('class' => 'btn btn-blue'));                     
                    }else{                    
                        echo button_to("Regresar Lista",'control_contratistas/index', array('class' => 'btn btn-blue'));
    	        	}                                                
	        	?>                
	          </div>
	        </div>
	        <div class="clear"></div>
			</form>
		</div>
	   </div>
	</div>
</div>
        