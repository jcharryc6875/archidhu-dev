<?php
    $path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
    $ruta_base = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';

    // Config file app.ini
    $app_config = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR."app.ini";
    $com_interna_advance_create = 0;
    $com_enviada_advance_create = 0;
    $mod_contratista = 0;
    //******************************************************************************
    if(file_exists($app_config)){
        $ini_array = parse_ini_file(sfConfig::get('sf_config_dir')."/app.ini");
        $com_interna_advance_create = isset($ini_array['com_interna_advance_create']) ? $ini_array['com_interna_advance_create'] : 0;
        $com_enviada_advance_create = isset($ini_array['com_enviada_advance_create']) ? $ini_array['com_enviada_advance_create'] : 0;
        $com_enviada_factura_create = isset($ini_array['com_enviada_formato_factura']) ? $ini_array['com_enviada_formato_factura'] : 0;
        $mod_contratista = isset($ini_array['mod_control_contratistas']) ? $ini_array['mod_control_contratistas'] : 0;
		$mod_novedades = isset($ini_array['mod_novedades']) ? $ini_array['mod_novedades'] : 0;
		$mod_proveedor = isset($ini_array['mod_proveedor']) ? $ini_array['mod_proveedor'] : 0;
		$mod_factura = isset($ini_array['mod_factura']) ? $ini_array['mod_factura'] : 0;
        $mod_clientes = isset($ini_array['mod_control_clientes']) ? $ini_array['mod_control_clientes'] : 0;	
        $mod_documentacion_tecnica = isset($ini_array['mod_documentacion_tecnica']) ? $ini_array['mod_documentacion_tecnica'] : 0;
        $com_recibida_por_responder = isset($ini_array['com_recibida_por_responder']) ? $ini_array['com_recibida_por_responder'] : 0;
    }
?>

<!-- Modulo Administrar -->
<li>
    <a href="#">
        <i class=""><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_settings.svg" class="submenues" /></i>
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
                <li><a href="<?php print $ruta_base; ?>backend.php/busqueda_avanzada"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consulta_avanzada.svg" class="submenues" />Busqueda Avanzada</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/usuario/cambioPassword"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_password.svg" class="submenues" />Password</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/redireccionar/redireccion"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_redirect.svg" class="submenues" />Redireccionar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/usuario/configSingStamp"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_form_process.svg" class="submenues" />Configurar Firma Digital</span></a></li>
            </ul>
        </li>

        <!-- Configuracion -->
        <li>
            <a href="#">
                <i class="entypo-cog"></i>
                <span>Configuraci&oacute;n</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>administracion.php/formas/list?modulo_id=11&panelControl=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_ctl-panel.svg" class="submenues" />Panel de Control</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/wf_flujo/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_workflow.svg" class="submenues" />Workflow</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/usuario/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_users.svg" class="submenues" />Usuarios</span></a></li>
                <li><a href="<?php print $ruta_base; ?>backend.php/cargo"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_cargo.svg" class="submenues" />Cargos</span></a></li>
                <li><a href="<?php print $ruta_base; ?>backend.php/rol/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_roles.svg" class="submenues" />Perfiles</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/roles_por_usuario/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_rolesxuser.svg" class="submenues" />Perfiles por Usuario</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/formas/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_formas.svg" class="submenues" />Privilegios</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/formas_por_usuario/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_formasxuser.svg" class="submenues" />Privilegios por Usuario</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/formas_por_rol/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_formasxrol.svg" class="submenues" />Privilegios por Perfil</span></a></li>
                <li><a href="<?php print $ruta_base; ?>administracion.php/subserie_por_usuario/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_subseriexuser.svg" class="submenues" />Subseries por Usuario</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/dependencia/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_tabla_retencion.svg" class="submenues" />Tablas de Retenci&oacute;n Documental</span></a></li>
            </ul>
        </li>
        <li>
            <a href="<?php print $ruta_base; ?>backend.php/resumen">
                <i class="entypo-eye"></i>
                <span>Resumen</span>
            </a>
        </li>
    </ul>
</li>
<!-- Comunicaciones -->
<li>
    <a href="#">
        <i class=""><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_comunicaciones.svg" class="submenues" /></i>
        <span class="title">Comunicaciones</span>
    </a>    
    <ul class="submenu-data" >
        <!-- Comunicaciones Internas -->
        <li>
            <a href="#">
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_com_interna.svg" class="submenues" />Internas</span>
            </a>
            <ul>
                <li>
                    <?php if($com_interna_advance_create){ ?>
                        <a href="#"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a>
                        <ul>
                            <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_form_web.svg" class="submenues" />Formulario Web</span></a></li>                        
                            <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/createRadicarWord"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_plantillas_word.svg" class="submenues" />Radicar Plantilla de Word</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/createPlantillaWord"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_plantillas_word_radicada.svg" class="submenues" />Generar Radicado en Plantilla de Word</span></a></li>                        
                        </ul>
                    <?php }else{ ?>
                        <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Nueva Interna</span></a></li>
                    <?php } ?>
                </li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li>
                    <a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porFunciEntrada=1">
                        <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_bandeja_entrada.svg" class="submenues" />Bandeja de Entrada</span>
                        <!--span class="badge badge-secondary">8</span-->
                    </a>
                </li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_bandeja_salida.svg" class="submenues" />Bandeja de Salida</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=2&porFunciEntrada=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_por_leer.svg" class="submenues" />Por Leer</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=5&porFunciEntrada=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_por_responder.svg" class="submenues" />Por Responder</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=1&porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_borrador.svg" class="submenues" />Borradores</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=4&porFunciEntrada=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_anulada.svg" class="submenues" />Anuladas</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?estadocominterna_id=6&porFunciEntrada=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_papelera.svg" class="submenues" />Papelera</span></a></li>
                <li><a href="<?php print $ruta_base; ?>interna.php/com_interna/list?porWorkflow=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_workflow.svg" class="submenues" />Workflow</span></a></li>
            </ul>
        </li>
        
        <!-- Comunicaciones Externas Recibidas -->
        <li>
            <a href="#">                
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_com_externa_recibida.svg" class="submenues" />Externas Recibidas</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?entradas=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_bandeja_entrada.svg" class="submenues" />Bandeja de Entrada</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?leer=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_por_leer.svg" class="submenues" />Por Leer</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?vencidas=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_vencida.svg" class="submenues" />Vencidas</span></a></li>
                <?php if($com_recibida_por_responder){ ?>
                    <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list??estado_com_recibida_id=6&porEncargado=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_por_responder.svg" class="submenues" />Por Responder</span></a></li>
                <?php } ?>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?porVencer=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_por_vencer.svg" class="submenues" />Por Vencer</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?papelera=14"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_papelera.svg" class="submenues" />Papelera</span></a></li>
                <li><a href="<?php print $ruta_base; ?>recibida.php/com_recibida/list?porWorkflow=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_workflow.svg" class="submenues" />Workflow</span></a></li>
            </ul>
        </li>

        <!-- Comunicaciones Externas Enviadas -->
        <li>
            <a href="#">                
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_com_externa_enviada.svg" class="submenues" />Externas Enviadas</span>
            </a>
            <ul>
                <li>
                    <?php if($com_enviada_advance_create){ ?>
                        <a href="#"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a>
                        <ul>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_form_web.svg" class="submenues" />Formulario Web</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/radicarMasivas"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Radicaci&oacute;n Masiva</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/createRadicarWord"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_plantillas_word.svg" class="submenues" />Radicar Plantilla de Word</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/createPlantillaWord"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_plantillas_word_radicada.svg" class="submenues" />Generar Radicado en Plantilla de Word</span></a></li>                        
                        </ul>
                    <?php }elseif($com_enviada_factura_create){ ?>
                        <a href="#"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a>
                        <ul>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_form_web.svg" class="submenues" />Nueva Enviada</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/radicarMasivas"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Radicaci&oacute;n Masiva</span></a></li>
                            <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/createPlantillaFactura"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_plantillas_word.svg" class="submenues" />Formato Factura</span></a></li>
                        </ul>
					<?php }else{ ?>
                        <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Nueva Enviada</span></a></li>
                        <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/radicarMasivas"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Radicaci&oacute;n Masiva</span></a></li>
                    <?php } ?>
                </li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_bandeja_salida.svg" class="submenues" />Bandeja de Salida</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porFunciCopia=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_copias_inf.svg" class="submenues" />Copias Informativas</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=1&porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_borrador.svg" class="submenues" />Borradores</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=4&porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_anulada.svg" class="submenues" />Anuladas</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?porRadicadas=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_reporte.svg" class="submenues" />Reporte</span></a></li>
                <li><a href="<?php print $ruta_base; ?>enviada.php/com_enviada/list?estadocomenviada_id=5&porFunciSalida=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_papelera.svg" class="submenues" />Papelera</span></a></li>
            </ul>
        </li>
		
		<?php if($mod_factura){ ?>
			<!-- Comunicaciones - Facturas -->
			<li>
				<a href="#">                
					<span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_factura.svg" class="submenues" />Facturas</span>
				</a>
				<ul>
					<li><a href="<?php print $ruta_base; ?>recibida.php/factura/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/factura/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/factura/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
				</ul>
			</li>
		<?php } ?>
		
		<?php if($mod_proveedor){ ?>
			<!-- Comunicaciones - Proveedores -->
			<li>
				<a href="#">                
					<span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_proveedor.svg" class="submenues" />Proveedores</span>
				</a>
				<ul>
					<li><a href="<?php print $ruta_base; ?>recibida.php/proveedor/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/proveedor/consultageneral"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/proveedor/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear_solicitud.svg" class="submenues" />Crear Solicitud</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/consultar"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar_solicitud.svg" class="submenues" />Consultar Solicitud</span></a></li>
					<li><a href="<?php print $ruta_base; ?>recibida.php/prov_solicitud_modificacion/index"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar Solicitudes</span></a></li>
				</ul>
			</li>
		<?php } ?>
		
		<?php if($mod_novedades){ ?>
			<!-- Comunicaciones - Novedades -->
			<li>
				<a href="#">
					<span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_novedad.svg" class="submenues" />Novedades</span>
				</a>
				<ul>
					<li><a href="<?php print $ruta_base; ?>comun.php/novedades/create/consecutivo_id/0/modulo_id/0"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
					<li><a href="<?php print $ruta_base; ?>comun.php/novedades/consulta/consecutivo_id/0/modulo_id/0"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
					<li><a href="<?php print $ruta_base; ?>comun.php/novedades/index?modulo_id=0&consecutivo_id=0"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
				</ul>
			</li>
		<?php } ?>
    </ul>
</li>
<!-- Archivos -->
<li>
    <a href="#">
        <i class=""><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos.svg" class="submenues" /></i>
        <span class="title">Archivos</span>
    </a>
    <ul class="submenu-data" >
        <!-- Archivo de Gestion -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=1">
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_gestion.svg" class="submenues" />Archivo de Gesti&oacute;n</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/vinculada/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_vincular.svg" class="submenues" />Vincular</span></a></li>
            </ul>
        </li>

        <!-- Archivo de central -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=2">
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_central.svg" class="submenues" />Archivo Central</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=2"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=2"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=2"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/transferencia/consultar?localizacionunidaddocumental_id=2"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_transferencia.svg" class="submenues" />Adm. Transferencias</span></a></li>
            </ul>
        </li>

        <!-- Archivo Histórico -->
        <li>
            <a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=3">
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_historico.svg" class="submenues" />Archivo Hist&oacute;rico</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/create?localizacion=3"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/consultar?localizacion=3"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/unidad_documental/list?localizacionunidaddocumental_id=3"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/transferencia/consultar?localizacionunidaddocumental_id=3"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_transferencia.svg" class="submenues" />Adm. Transferencias</span></a></li>
            </ul>
        </li>      

        <!-- Prestamos de Archivo -->
        <li>
            <a href="#">
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_prestamos.svg" class="submenues" />Pr&eacute;stamos Archivo</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>archivo.php/prestamo/list?misPrestamos=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes.svg" class="submenues" />Mis Pr&eacute;stamos</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/solicitud_prestamo/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_solicitud.svg" class="submenues" />Solicitudes</span></a></li>
                <li><a href="<?php print $ruta_base; ?>archivo.php/prestamo/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamos_descargar.svg" class="submenues" />Descargar Pr&eacute;stamos</span></a></li>
            </ul>
        </li>

        <?php if($mod_clientes){ ?> 
            <!-- Archivo Clientes -->
            <li>
                <a href="<?php print $ruta_base; ?>">
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes.svg" class="submenues" />Archivo Clientes</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/cliente/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/cliente/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/cliente/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                    <!--<li><a href="<?php print $ruta_base; ?>clientes.php/transferencias_clientes/consulta"><span>Adm. Transferencias</span></a></li>-->
                </ul>
            </li>

            <!-- Prestamo de Clientes -->
            <li>
                <a href="<?php print $ruta_base; ?>clientes.php/solicitud_prestamo_cliente/consulta">
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes_prestamos.svg" class="submenues" />Pr&eacute;stamos Clientes</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/prestamo_cliente/list?misPrestamos=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes.svg" class="submenues" />Mis Pr�stamos</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/solicitud_prestamo_cliente/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_solicitud.svg" class="submenues" />Solicitudes</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>clientes.php/prestamo_cliente/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamos_descargar.svg" class="submenues" />Descargar Pr�stamos</span></a></li>
                </ul>
            </li>
        <?php } ?>
        
        <!-- Control Contratistas -->
        <?php if($mod_contratista){ ?>            
            <li>
                <a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/consultar">                   
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes.svg" class="submenues" />Control Contrat&iacute;stas</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Nuevo Contratista</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/consultar"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>archivo.php/control_contratistas/index"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                </ul>
            </li>
        <?php } ?>        
    </ul>
</li>
<!-- Documentacion Tecnica -->
<?php if($mod_documentacion_tecnica){ ?>
    <li>
        <a href="#">
            <i class=""><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_documentacion_tecnica.svg" class="submenues" /></i>
            <span class="title">Documentaci&oacute;n T&eacute;cnica</span>
        </a>
        <ul class="submenu-data" >
            <!-- Planos -->
            <li>
                <a href="#">
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_planos.svg" class="submenues" />Planos</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/planos/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/consulta?tipo_documentacion_id=5"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/list?tipo_documentacion_id=5"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                </ul>
            </li>

            <!-- Libros -->
            <li>
                <a href="#">               
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_biblioteca.svg" class="submenues" />Biblioteca</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/libro/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/consulta?tipo_documentacion_id=4"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/documentacion_tecnica/list?tipo_documentacion_id=4"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
                </ul>
            </li>

            <!-- Prestamos -->
            <li>
                <a href="#">
                    <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamos.svg" class="submenues" />Pr&eacute;stamos</span>
                </a>
                <ul>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/prestamo_documentacion/list?misPrestamos=1"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_archivos_clientes.svg" class="submenues" />Mis Pr&eacute;stamos</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/solicitud_documentacion/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_solicitud.svg" class="submenues" />Solicitudes</span></a></li>
                    <li><a href="<?php print $ruta_base; ?>documentacion.php/prestamo_documentacion/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_prestamos_descargar.svg" class="submenues" />Descargar Pr&eacute;stamos</span></a></li>
                </ul>
            </li>
        </ul>
    </li>
<?php } ?>
<!-- Mensajeria y CAD -->
<li>
    <a href="#">
        <i class=""><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_servicios.svg" class="submenues" /></i>
        <span class="title">Mensajer&iacute;a y CAD</span>
    </a>
    <ul class="submenu-data" >
        <!-- Formatos y Procedimientos -->
        <li>
            <a href="#">                
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_form_process.svg" class="submenues" />Formatos y Procedimientos</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>procedimiento.php/formas/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>procedimiento.php/formas/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>procedimiento.php/formas/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
            </ul>
        </li>

        <!-- Solicitud de Servicio -->
        <li>
            <a href="#">                
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_solicitud_servicio.svg" class="submenues" />Solicitud de Servicio</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>servicios.php/servicio/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>servicios.php/servicio/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>servicios.php/servicio/list?periodo_id=<?php echo date("Y"); ?>"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
            </ul>
        </li>

        <!-- Apoyo al Usuario -->
        <li>
            <a href="#">               
                <span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_apoyo_usuario.svg" class="submenues" />Apoyo al Usuario</span>
            </a>
            <ul>
                <li><a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/create"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_crear.svg" class="submenues" />Crear</span></a></li>
                <li><a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/consulta"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_consultar.svg" class="submenues" />Consultar</span></a></li>
                <li><a href="<?php print $ruta_base; ?>apoyoUsuario.php/pqr/list"><span><img src="<?php echo $path_theme; ?>assets/images/sub-menu/ico_list.svg" class="submenues" />Listar</span></a></li>
            </ul>
        </li>
    </ul>
</li>