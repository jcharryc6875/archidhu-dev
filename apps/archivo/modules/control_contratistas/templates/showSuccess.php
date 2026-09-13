<?php
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    //********************************************************************************************************
    $currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
    $currentFormEditar = $sf_user->checkPerm("control_contratistas/edit", $currentUser) ;
    $currentFormDelete = $sf_user->checkPerm("control_contratistas/delete", $currentUser) ;    
    use_helper('Object','jQuery');
?>
<div class="row">
	<div class="col-md-12">
	<!-- Contenedor Pagina -->
	<div class="panel panel-gradient" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			  Detalles archivo <?php echo $tabletitle ?>
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body">
			<!-- Opciones Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="form-group">                    
                    <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Regresar a lista de registros" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
                        <img src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" title="" width="25" align="middle" />Cerrar
                    </a>                    
					<?php if($currentFormEditar): ?>
                        <a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Editar este registro" href="<?php echo $base_path; ?>/archivo.php/control_contratistas/edit?controlcontratistas_id=<?php  echo $control_contratistas->getPrimaryKey(); ?>">
                            <img src="<?php echo $base_path; ?>/images/simad/ico_editar.png" width="25" height="25" align="middle" />Editar
                        </a>
                    <?php endif; ?>					
				</div>
			</div>
			<hr />
			<!-- Informacion Detalle -->
			<div class="col-sm-12 col-md-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Estado Contratista</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getEstadoContratista(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Nombres</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getNombre(); ?></p></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Apellidos</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getPrimerApellido()." ".$control_contratistas->getSegundoApellido(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>EPS</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getEps(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fondo pensiones</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getFondoPensiones(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Tipo identifiaci&oacute;n</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getTipoIdentificacion(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Cargo</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getCargo(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Dependencia</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getDependencia(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Ubicaci&oacute;n Laboral</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getUbicacionLaboral(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Genero</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getGenero(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Nacionalidad</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getNacionalidad(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Libreta militar</strong></p></div>
						<div class="col-sm-7"><p><?php print $libreta_militar[0]; ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Distrito</strong></p></div>
						<div class="col-sm-8"><p><?php print $libreta_militar[1]; ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Clase</strong></p></div>
						<div class="col-sm-7"><p><?php print $libreta_militar[2]; ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha nacimiento</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getFechaNacimiento(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Ciudad nacimiento</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getCiudad(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Direccion correspondencia</strong></p></div>
						<div class="col-sm-8"><p><?php print $direccion_envio[0]; ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Municipio</strong></p></div>
						<div class="col-sm-7"><p><?php print $direccion_envio[1]; ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Departamento</strong></p></div>
						<div class="col-sm-8"><p><?php print $direccion_envio[2]; ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Email</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getEmail(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Titulo Educacion Basica</strong></p></div>
						<div class="col-sm-8"><p><?php print $educacion_basica[3]; ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Fecha Grado Basica</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getFechaGradoBasica(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Educacion Superior</strong></p></div>
						<div class="col-sm-8"><p><?php echo label_for('label1','Modalidad: '.$educacion_superior[0]) . "&nbsp;".label_for('label1','Semestres Aprobados: '.$educacion_superior[1]) . "&nbsp;".label_for('label1','Esta Graduado: '.$educacion_superior[2]); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Nombre o Titulo Obtenido</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getTituloUniversitario(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Fecha Terminaci&oacute;n</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getFechaGradoUniversitario(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Tarjeta Profesional</strong></p></div>
						<div class="col-sm-7"><p><?php print $control_contratistas->getNumeroTarjeta(); ?></p></div>
					</div>
				</div>
                
                <div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><p><strong>Otros Idiomas</strong></p></div>
						<div class="col-sm-8"><p><?php print $control_contratistas->getOtrosIdiomas(); ?></p></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><p><strong>Experiencia Laboral</strong></p></div>
						<div class="col-sm-7">
                            <p>
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
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[0]; ?></th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[1]; ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">EMPLEADO SECTOR PRIVADO</th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[2]; ?></th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[3]; ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">TRABAJADOR INDEPENDIENTE</th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[4]; ?></th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[5]; ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">TOTAL TIEMPO EXPERIENCIA</th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[6]; ?></th>
                                    <th style="border:solid windowtext 1.0pt;"><?php echo $experiencia_laboral[7]; ?></th>
                                </tr>
                              </table>
                            </p>
                        </div>
				    </div>                
                </div>
                <hr />
          	</div>
          </div>
        </div>
    </div>
</div>