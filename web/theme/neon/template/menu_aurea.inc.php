<?php
    $path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
    $ruta_base = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';

    // Config file app.ini
    $app_config = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR."app.ini";
    $com_interna_advance_create = 0;
    $com_enviada_advance_create = 0;
    $acto_administrativo_advance_create = 0;
    $mod_contratista = 0;
	$application_current_version = "6.0.158";
    //******************************************************************************
    if(file_exists($app_config))
    {
        $ini_array = parse_ini_file(sfConfig::get('sf_config_dir')."/app.ini");
        $com_interna_advance_create = isset($ini_array['com_interna_advance_create']) ? $ini_array['com_interna_advance_create'] : 0;
        $com_enviada_advance_create = isset($ini_array['com_enviada_advance_create']) ? $ini_array['com_enviada_advance_create'] : 0;
        $acto_administrativo_advance_create = isset($ini_array['acto_administrativo_advance_create']) ? $ini_array['acto_administrativo_advance_create'] : 0;
        $com_enviada_factura_create = isset($ini_array['com_enviada_formato_factura']) ? $ini_array['com_enviada_formato_factura'] : 0;
        $mod_contratista = isset($ini_array['mod_control_contratistas']) ? $ini_array['mod_control_contratistas'] : 0;
		$mod_novedades = isset($ini_array['mod_novedades']) ? $ini_array['mod_novedades'] : 0;
		$mod_proveedor = isset($ini_array['mod_proveedor']) ? $ini_array['mod_proveedor'] : 0;
		$mod_factura = isset($ini_array['mod_factura']) ? $ini_array['mod_factura'] : 0;
        $mod_clientes = isset($ini_array['mod_control_clientes']) ? $ini_array['mod_control_clientes'] : 0;	
        $mod_documentacion_tecnica = isset($ini_array['mod_documentacion_tecnica']) ? $ini_array['mod_documentacion_tecnica'] : 0;
        $com_recibida_por_responder = isset($ini_array['com_recibida_por_responder']) ? $ini_array['com_recibida_por_responder'] : 0;
		$com_recibida_batch_create = isset($ini_array['com_recibida_batch_create']) ? $ini_array['com_recibida_batch_create'] : 0;
		$mod_actos_administrativos = isset($ini_array['mod_actos_administrativos']) ? $ini_array['mod_actos_administrativos'] : 0;
		$application_current_version = isset($ini_array['application_current_version']) ? $ini_array['application_current_version'] : $application_current_version;
        $mod_correo_electonico  = isset($ini_array['mod_correo_electonico']) ? $ini_array['mod_correo_electonico'] : 0;
    }
?>

<!-- Modulo Administrar -->
<li>
    <a href="#">
        <i class="fa fa-tachometer"></i>
        <span class="title">Administrar</span>
    </a>

    <ul class="submenu-data" >
        <!-- Inicio -->
        <li>
            <a href="#">
                <i class="entypo-key"></i>
                <span>Inicio</span>
            </a>
            <ul>
                <li>
                    <a href="<?php print $ruta_base; ?>backend.php/busqueda_avanzada">
                        <i class="fa fa-search"></i>
                        <span class="title">B&uacute;squeda Avanzada</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/usuario/cambioPassword">
                        <i class="entypo-dot-3"></i>
                        <span class="title">Password</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/redireccionar/redireccion">
                        <i class="entypo-export"></i>
                        <span class="title">Redireccionar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/usuario/configSingStamp">
                        <i class="entypo-newspaper"></i>
                        <span class="title">Configurar Firma Digital</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/usuario/waterMarkUtil">
                        <i class="fa fa-search-plus"></i>
                        <span class="title">Validar Marca de Agua</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Configuracion -->
        <li>
            <a href="#">
                <i class="glyphicon glyphicon-cog"></i>
                <span class="title">Configuración</span>
            </a>
            <ul>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/formas/list?modulo_id=11&panelControl=1">
                        <i class="fa fa-desktop"></i>
                        <span class="title">Panel de Control</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/wf_flujo/list">
                        <i class="fa fa-gears"></i>
                        <span class="title">Workflow</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/usuario/list">
                        <i class="fa fa-users"></i>
                        <span class="title">Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>backend.php/cargo">
                        <i class="fa fa-briefcase"></i>
                        <span class="title">Cargos</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>backend.php/rol/list">
                        <i class="fa fa-user"></i>
                        <span class="title">Perfiles</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/roles_por_usuario/list">
                        <i class="fa fa-bell"></i>
                        <span class="title">Perfiles por Usuario</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/formas/list">
                        <i class="fa fa-list-alt"></i>
                        <span class="title">Privilegios</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/formas_por_usuario/list">
                        <i class="fa fa-bell"></i>
                        <span class="title">Privilegios por Usuario</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/formas_por_rol/list">
                        <i class="fa fa-indent"></i>
                        <span class="title">Privilegios por Perfil</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>administracion.php/subserie_por_usuario/list">
                        <i class="fa fa-bell"></i>
                        <span class="title">Subseries por Usuario</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/dependencia/list">
                        <i class="fa fa-table"></i>
                        <span class="title">Tablas de Retención Documental</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="<?php print $ruta_base; ?>backend.php/resumen">
                <i class="entypo-eye"></i>
                <span class="title">Resumen</span>
            </a>
        </li>
    </ul>
</li>
<!-- Comunicaciones -->
<li>
    <a href="#">
        <i class="fa fa-envelope"></i>
        <span class="title">Comunicaciones</span>
    </a>
    <ul>
        <!-- Comunicaciones Internas -->
        <li>
            <a href="#">
                <i class="entypo-back"></i>
                <span class="title">Internas</span>
            </a>

            <ul>
                <li>
                    <?php 
                    if($com_interna_advance_create)
                    { ?>
                        
                        <a href="#">
                            <i class="entypo-plus-circled"></i>
                            <span class="title">Crear</span>
                        </a>



                        <ul>
                            
                            <li>
                                <a href="<?php print $ruta_base; ?>interna.php/com_interna/create">
                                    <i class="entypo-plus-circled"></i>
                                    <span class="title">Formulario Web</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php print $ruta_base; ?>interna.php/com_interna/createRadicarWord">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title">Radicar Plantilla de Word</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php print $ruta_base; ?>interna.php/com_interna/createPlantillaWord">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title">Generar Radicado en Plantilla de Word</span>
                                </a>
                            </li>                       
                        </ul>
                    <?php 
                    }
                    else
                    { 
                        ?>
                        <li>
                            <a href="<?php print $ruta_base; ?>interna.php/com_interna/create">
                                <i class="entypo-plus-circled"></i>
                                <span class="title">Nueva Interna</span>
                            </a>
                        </li>
                    <?php 
                    } 
                    ?>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porFunciEntrada=1">
                        <i class="glyphicon glyphicon-download-alt"></i>
                        <span class="title">Bandeja de Entrada</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porFunciSalida=1">
                        <i class="glyphicon glyphicon-open"></i>
                        <span class="title">Bandeja de Salida</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=2&porFunciEntrada=1">
                        <i class="glyphicon glyphicon-envelope"></i>
                        <span class="title">Por Leer</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=5&porFunciEntrada=1">
                        <i class="glyphicon glyphicon-send"></i>
                        <span class="title">Por Responder</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=1&porFunciSalida=1">
                        <i class="fa fa-eraser"></i>
                        <span class="title">Borradores</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=4&porFunciEntrada=1">
                        <i class="entypo-cancel-circled"></i>
                        <span class="title">Anulados</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=6&porFunciEntrada=1">
                        <i class="entypo-trash"></i>
                        <span class="title">Papelera</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porWorkflow=1">
                        <i class="fa fa-gears"></i>
                        <span class="title">Workflow</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Comunicaciones Externas Recibidas -->
        <li>
            <a href="#">
                <i class="fa fa-reply"></i>
                <span class="title">Externas Recibidas</span>
            </a>
            <ul>
                <li>
					<?php if($com_recibida_batch_create) { ?>
                        <a href="#">
                            <i class="entypo-plus-circled"></i>
                            <span class="title">Crear</span>
                        </a>

                        <ul>
                            <li>
                                <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/create">
									<i class="entypo-plus-circled"></i>
									<span class="title">Crear</span>
								</a>
                            </li>
                            <li>
                                <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/radicarMasivas">
                                <i class="entypo-list-add"></i>
                                <span class="title">Radicaci&oacute;n Masiva</span>
                                </a>
                            </li>
                        </ul>
                    <?php } else { ?>
                        <li>
                            <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/create">
								<i class="entypo-plus-circled"></i>
								<span class="title">Crear</span>
							</a>
                        </li>
                    <?php } ?>

                    <?php if($mod_correo_electonico) { ?>
                        <!-- Comunicaciones - Correo Electronico -->
                        <li>
                            <a href="#">
                                <i class="fa fa-download"></i>
                                <span class="title">Correo Electr&oacute;nico</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="<?php print $ruta_base; ?>recibida.php/email_sync/esync">
                                        <i class="glyphicon glyphicon-cog"></i>
                                        <span class="title">Cuentas Sinconizadas</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php print $ruta_base; ?>recibida.php/email_sync/list">
                                        <i class="glyphicon glyphicon-tasks"></i>
                                        <span class="title">Listar Email</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?entradas=1">
                        <i class="glyphicon glyphicon-download-alt"></i>
                        <span class="title">Bandeja de Entrada</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?leer=1">
                        <i class="glyphicon glyphicon-envelope"></i>
                        <span class="title">Por Leer</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?vencidas=1">
                        <i class="glyphicon glyphicon-warning-sign"></i>
                        <span class="title">Vencidas</span>
                    </a>
                </li>
                <?php 
                if($com_recibida_por_responder)
                { ?>
                    <li>
                        <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list??estado_com_recibida_id=6&porEncargado=1">
                            <i class="glyphicon glyphicon-send"></i>
                            <span class="title">Por Responder</span>
                        </a>
                    </li>
                <?php 
                } ?>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?porVencer=1">
                        <i class="glyphicon glyphicon-calendar"></i>
                        <span class="title">Por Vencer</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?papelera=14">
                        <i class="entypo-trash"></i>
                        <span class="title">Papelera</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?porWorkflow=1">
                        <i class="fa fa-gears"></i>
                        <span class="title">Workflow</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Comunicaciones Externas Enviadas -->
        <li>
            <a href="#">
                <i class="fa fa-share"></i>
                <span class="title">Externas Enviadas</span>
            </a>

            <ul>
                <li>
                    <?php if($com_enviada_advance_create) { ?>
                        <a href="#">
                            <i class="entypo-plus-circled"></i>
                            <span class="title">Crear</span>
                        </a>

                        <ul>
                            <li>
                                <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/create">
                                    <i class="entypo-plus-circled"></i>
                                    <span class="title">Nueva Enviada</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/radicarMasivas">
                                <i class="entypo-list-add"></i>
                                <span class="title">Radicaci&oacute;n Masiva</span>
                                </a>
                            </li>
                        </ul>
                    <?php } else { ?>
                        <li>
                            <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/create">
                                <i class="entypo-plus-circled"></i>
                                <span class="title">Nueva Enviada</span>
                            </a>
                        </li>
                    <?php } ?>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porFunciSalida=1">
                        <i class="glyphicon glyphicon-open"></i>
                        <span class="title">Bandeja de Salida</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porFunciCopia=1">
                        <i class="glyphicon glyphicon-tags"></i>
                        <span class="title">Copias Informativas</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=1&porFunciSalida=1">
                        <i class="fa fa-eraser"></i>
                        <span class="title">Borradores</span> 
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=4&porFunciSalida=1">
                        <i class="entypo-cancel-circled"></i>
                        <span class="title">Anuladas</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porRadicadas=1">
                        <i class="entypo-clipboard"></i>
                        <span class="title">Reporte</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=5&porFunciSalida=1">
                        <i class="entypo-trash"></i>
                        <span class="title">Papelera</span>
                    </a>
                </li>
            </ul>
        </li>

		<?php 
        if($mod_actos_administrativos)
        { ?>
			<!-- Resoluciones y Actos Administrativos -->
			<li>
				<a href="#">
					<i class="fa fa-edit"></i>
					<span class="title">Actos Adminstrativos</span>
				</a>

				<ul>
                    <li>
                        <?php if($acto_administrativo_advance_create) { ?>
                            <a href="#">
                                <i class="entypo-plus-circled"></i>
                                <span class="title">Crear</span>
                            </a>

                            <ul>
                                <li>
                                    <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/create">
                                        <i class="entypo-plus-circled"></i>
                                        <span class="title">Nuevo Acto Administrativo</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/radicarMasivas">
                                    <i class="entypo-list-add"></i>
                                    <span class="title">Radicaci&oacute;n Masiva</span>
                                    </a>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <li>
                                <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/create">
                                    <i class="entypo-plus-circled"></i>
                                    <span class="title">Crear</span>
                                </a>
                            </li>
                        <?php } ?>
                    </li>

					<li>
						<a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/consulta">
							<i class="glyphicon glyphicon-search"></i>
							<span class="title">Consultar</span>
						</a>
					</li>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?porFunciEntrada=1">
                            <i class="glyphicon glyphicon-download-alt"></i>
                            <span class="title">Bandeja de Entrada</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?porFunciSalida=1">
                            <i class="glyphicon glyphicon-open"></i>
                            <span class="title">Bandeja de Salida</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?estadoactoadministrativo_id=6&porFunciEntrada=1">
                            <i class="glyphicon glyphicon-envelope"></i>
                            <span class="title">Por Leer</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?estadoactoadministrativo_id=1&porFunciSalida=1">
                            <i class="fa fa-eraser"></i>
                            <span class="title">Borradores</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?porFunciCopia=1">
                            <i class="glyphicon glyphicon-tags"></i>
                            <span class="title">Copias Informativas</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?estadoactoadministrativo_id=5&porFunciEntrada=1">
                            <i class="entypo-cancel-circled"></i>
                            <span class="title">Anulados</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/acto_administrativo/list?estadoactoadministrativo_id=8&porFunciEntrada=1">
                            <i class="entypo-trash"></i>
                            <span class="title">Papelera</span>
                        </a>
                    </li>
				</ul>
			</li>
		<?php 
        } 
        ?>

		
		<?php 
        if($mod_factura)
        { ?>
			<!-- Comunicaciones - Facturas -->
        <li>
            

            <a href="#">
                <i class="fa fa-file-text"></i>
                <span class="title">Facturas</span>
            </a>



            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/factura/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/factura/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/factura/list">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>
		<?php 
        } 
        ?>
		
		<?php 
        if($mod_proveedor)
        { ?>
		<!-- Comunicaciones - Proveedores -->

        <li>
            <a href="#">
                <i class="entypo-user"></i>
                <span class="title">Proveedores</span>
            </a>

            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/proveedor/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/proveedor/consultageneral">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/proveedor/list">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear Solicitud</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/consultar">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar Solicitud</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/index">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar Solicitudes</span>
                    </a>
                </li>
            </ul>
        </li>

		<?php } ?>
		
		<?php 
        if($mod_novedades)
        { ?>
			<!-- Comunicaciones - Novedades -->
			<li>


                <a href="#">
                    <i class="fa fa-lightbulb-o"></i>
                    <span class="title">Novedades</span>
                </a>


				<ul>

                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/novedades/create/consecutivo_id/0/modulo_id/0">
                            <i class="entypo-plus-circled"></i>
                            <span class="title">Crear</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/novedades/consulta/consecutivo_id/0/modulo_id/0">
                            <i class="glyphicon glyphicon-search"></i>
                            <span class="title">Consultar</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php print $ruta_base; ?>comun.php/novedades/index?modulo_id=0&consecutivo_id=0">
                            <i class="fa fa-list"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
				</ul>
			</li>
		<?php 
        } ?>
    </ul>
</li>
<!-- Archivos -->
<li>
    <a href="#">
        <i class="glyphicon glyphicon-folder-close"></i>
        <span class="title">Archivos</span>
    </a>

    <ul>
        <!-- Archivo de Gestion -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=1">
                <i class="fa fa-archive"></i>
                <span class="title">Archivo de Gestión</span>
            </a>


            <ul>
                
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=1">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=1">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=1">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/vinculada/list">
                        <i class="fa fa-link"></i>
                        <span class="title">Vincular</span>
                    </a>
                </li>            
            </ul>
        </li>

        <!-- Archivo de central -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=2">
                <i class="entypo-archive"></i>
                <span class="title">Archivo Central</span>
            </a>
            <ul>
                
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=2">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=2">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=2">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/transferencia/consultar?localizacionunidaddocumental_id=2">
                        <i class="entypo-arrows-ccw"></i>
                        <span class="title">Adm. Transferencias</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Archivo Histórico -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=3">
                <i class="entypo-box"></i>
                <span class="title">Archivo Historico</span>
            </a>

            <ul>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=3">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=3">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=3">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/transferencia/consultar?localizacionunidaddocumental_id=3">
                        <i class="entypo-arrows-ccw"></i>
                        <span class="title">Adm. Transferencias</span>
                    </a>
                </li>
            </ul>
        </li>      

        <!-- Prestamos de Archivo -->
        <li>
            <a href="#">
                <i class="glyphicon glyphicon-hand-right"></i>
                <span class="title">Préstamos Archivo</span>
            </a>

            <ul>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/prestamo/list?misPrestamos=1">
                        <i class="fa fa-user"></i>
                        <span class="title">Mis Prestamos</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/solicitud_prestamo/consulta">
                        <i class="entypo-clipboard"></i>
                        <span class="title">Solicitudes</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/prestamo/consulta">
                        <i class="fa fa-download"></i>
                        <span class="title">Descargar Prestamos</span>
                    </a>
                </li>
            </ul>
        </li>

        <?php if($mod_clientes)
        { ?> 
            <!-- Archivo Clientes -->
            <li>

                <a href="<?php print $ruta_base; ?>">
                    <i class="glyphicon glyphicon-hand-right"></i>
                    <span class="title">Archivo Clientes</span>
                </a>

                <ul>
                    
                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/cliente/create">
                            <i class="entypo-plus-circled"></i>
                            <span class="title">Crear</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/cliente/consulta">
                            <i class="glyphicon glyphicon-search"></i>
                            <span class="title">Consultar</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/cliente/list">
                            <i class="fa fa-list"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
                    <!--<li><a href="<?php /* print $ruta_base; */ ?>clientes.php/transferencias_clientes/consulta"><span>Adm. Transferencias</span></a></li>-->
                </ul>
            </li>

            <!-- Prestamo de Clientes -->
            <li>

                <a href="<?php print $ruta_base; ?>clientes.php/solicitud_prestamo_cliente/consulta">
                    <i class="glyphicon glyphicon-hand-right"></i>
                    <span class="title">Préstamos Clientes</span>
                </a>


                <ul>
                    
                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/prestamo_cliente/list?misPrestamos=1">
                            <i class="fa fa-user"></i>
                            <span class="title">Mis Préstamos</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/solicitud_prestamo_cliente/consulta">
                            <i class="entypo-clipboard"></i>
                            <span class="title">Solicitudes</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php print $ruta_base; ?>clientes.php/prestamo_cliente/consulta">
                            <i class="fa fa-download"></i>
                            <span class="title">Descargar Préstamos</span>
                        </a>
                    </li>
                </ul>
            </li>

        <?php 
        } 
        ?>
        
        <!-- Control Contratistas -->
        <?php 
        if($mod_contratista)
        { ?>            
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/consultar">
                <i class="fa fa-user"></i>
                <span class="title">Control Contratistas</span>
            </a>

            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Nuevo Contratista</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/consultar">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>

                <li>
                    <a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/index">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php 
        } 
        ?>        
    </ul>
</li>
<!-- Documentacion Tecnica -->
<?php 
if($mod_documentacion_tecnica)
{ ?>

<li>
    <a href="#">
        <i class="glyphicon glyphicon-wrench"></i>
        <span class="title">Documentación Técnica</span>
    </a>


    <ul>
        <!-- Planos -->
        <li>
            <a href="#">
                <i class="entypo-window"></i>
                <span class="title">Planos</span>
            </a>

            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/planos/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/consulta?tipo_documentacion_id=5">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/list?tipo_documentacion_id=5">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Libros -->
        <li>
            <a href="#">
                <i class="entypo-book"></i>
                <span class="title">Biblioteca</span>
            </a>

            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/libro/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/consulta?tipo_documentacion_id=4">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/list?tipo_documentacion_id=4">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Prestamos -->
        <li>
            <a href="#">
                <i class="glyphicon glyphicon-hand-right"></i>
                <span class="title">Prestamos</span>
            </a>

            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/prestamo_documentacion/list?misPrestamos=1">
                        <i class="fa fa-user"></i>
                        <span class="title">Mis Prestamos</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/solicitud_documentacion/consulta">
                        <i class="entypo-clipboard"></i>
                        <span class="title">Solicitudes</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>documentacion.php/prestamo_documentacion/consulta">
                        <i class="fa fa-download"></i>
                        <span class="title">Descargar Prestamos</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
<?php 
} ?>
<!-- Mensajeria y CAD -->
<li>
    <a href="#">
        <i class="glyphicon glyphicon-folder-open"></i>
        <span class="title">Mensajería y CAD</span>
    </a>


    <ul>
        <!-- Formatos y Procedimientos -->
        <li>
            
            <a href="#">
                <i class="entypo-newspaper"></i>
                <span class="title">Formatos y Procedimientos</span>
            </a>
            <ul>
                
                <li>
                    <a href="<?php print $ruta_base; ?>procedimiento.php/formas/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>procedimiento.php/formas/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>procedimiento.php/formas/list">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Solicitud de Servicio -->
        <li>
            <a href="#">
                <i class="entypo-clipboard"></i>
                <span class="title">Solicitud de Servicio</span>
            </a>
            <ul>
                <li>
                    <a href="<?php print $ruta_base; ?>servicios.php/servicio/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>servicios.php/servicio/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>servicios.php/servicio/list?periodo_id=<?php echo date("Y"); ?>">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Apoyo al Usuario -->
        <li>
            <a href="#">
                <i class="fa fa-user"></i>
                <span class="title">Apoyo al Usuario</span>
            </a>
            <ul>

                <li>
                    <a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/create">
                        <i class="entypo-plus-circled"></i>
                        <span class="title">Crear</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/consulta">
                        <i class="glyphicon glyphicon-search"></i>
                        <span class="title">Consultar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/list">
                        <i class="fa fa-list"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>