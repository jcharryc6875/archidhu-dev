	searchname = 'buscador.html'
	
	usebannercode=false
	ButtonCode = "<img src='searchbutton.jpg' border=0>" 
	
	function templateBody() {
		document.write('<html><head><title>Buscador</title><'+
		'script language="Javascript">'+
		'<'+'/'+'script'+'></head><body bgcolor="#FFFFFF" text="#666" link="#000099" vlink="#996699" alink="#996699"><Center><font face="Arial" size="2">');
	}

	function templateEnd() {
		document.write('</td></tr></table></font></center></body></html>');
	}
	function bannerCode() {
	}	

/* end configuration settings */

/* database records */
/* Home*/
add("<a href='index.html'>Home Simad </a>","home, inicio, comenzar","<li>Pagina de inicio del centro de Ayuda SIMAD Versión 4.0.</li>")


/*Administrar*/
add("<a href='administrar/adm_inicio.html'>Administrar Inicio </a>"," Busqueda Avanzada, Password, Redireccionar","<li> Busqueda Avanzada, Password, Redireccionar.</li>")

add("<a href='administrar/adm_configuracion.html'>Administrar Configuracion </a>","Panel de Control,Usuarios,Cargos,Roles,Roles por Usuario,Formas,Formas por Usuario,Formas por Rol,Subseries por Usuario,Tabla de Retenciones","<li> Panel de Control, Usuarios, Cargos, Roles, Roles por Usuario, Formas, Formas por Usuario, Formas por Rol, Subseries por Usuario, Tabla de Retenciones.</li>")

add("<a href='administrar/adm_resumen.html'>Administrar Resumen </a>"," Resumen","<li> Resumen</li>")


/* Comunicaciones*/
add("<a href='Comunicaciones/cominicaciones_internas.html'>Comunicaciones internas </a>"," Crear,Consultar,Bandeja de Entrada,Bandeja de Salida,Por leer,por responder,borrador,anuladas,papelera","<li> Crear, Consultar, Bandeja de Entrada, Bandeja de Salida, Por leer, Por responder, Borrador, Anuladas, Papelera.</li>")

add("<a href='Comunicaciones/cominicaciones_ExternasEnviadas.html'>Comunicaciones Externas Enviadas </a>"," Crear,Consultar,Bandeja de Salida,Copias informativas,borrador,reporte,anuladas,papelera","<li> Crear, Consultar, Bandeja de Entrada, Bandeja de Salida, Copias Informativas, Borrador, Reporte, Anuladas, Papelera.</li>")

add("<a href='Comunicaciones/cominicaciones_ExternasRecibidas.html'>Comunicaciones Externas Recibidas </a>"," Crear,Consultar,Bandeja de Salida,Por leer,vencidas,por vencer,papelera,workflow,","<li> Crear, Consultar, Bandeja de Salida, Por leer, Vencidas, Por vencer, Papelera, workflow.</li>")

add("<a href='Comunicaciones/comunicaciones_facturas.html'>Comunicaciones facturas</a>","Crear,Consultar,Listar","<li> Crear, Consultar, Listar.</li>")

add("<a href='Comunicaciones/cominicaciones_proveedores.html'>Comunicaciones Proveedores </a>"," Crear,Consultar,Listar, Crear Solicitud,Consultar Solicitud,Listar Solicitudes,","<li> Crear, Consultar, Listar, Crear Solicitud, Consultar Solicitud, Listar Solicitudes.</li>")

add("<a href='Comunicaciones/comunicaciones_novedades.html'>Comunicaciones Novedades</a>","Crear,Consultar,Listar","<li> Crear, Consultar, Listar.</li>")



/*Archivos*/
add("<a href='Archivos/Archivos_DeGestion.html'>Archivo de Gestion </a>","Crear, Consultar, Listar, Vincular","<li>Crear, Consultar, Listar, Vincular.</li>")

add("<a href='Archivos/Archivos_Central.html'>Archivo Central </a>","Crear, Consultar, Listar, Administrador de Transferencias","<li>Crear, Consultar, Listar, Administrador de Transferencias.</li>")

add("<a href='Archivos/Archivos_Historico.html'>Archivo Historico </a>","Crear, Consultar, Listar, Administrador de Transferencias","<li>Crear, Consultar, Listar, Administrador de Transferencias.</li>")


add("<a href='Archivos/Archivos_Prestamos.html'>Archivo Prestamos </a>","Mis Prestamos, Solicitudes, Descargar Prestamos,","<li>Mis Prestamos, Solicitudes, Descargar Prestamos.</li>")

add("<a href='Archivos/Archivos_Clientes.html'>Archivos Clientes </a>","Crear, Consultar, Listar,","<li>Crear, Consultar, Listar, .</li>")

add("<a href='Archivos/Archivos_prestamoClientes.html'>Archivo Clientes </a>","Crear, Consultar, Listar, Administrador de Transferencias","<li>Crear, Consultar, Listar, Administrador de Transferencias.</li>")



/*Documentacion Tecnica*/
add("<a href='DocumentacionTecnica/DocTech_Planos.html'>Documentación Técnica PLanos </a>","Crear, Consultar, Listar","<li>Crear, Consultar, Listar.</li>")

add("<a href='DocumentacionTecnica/DocTech_biblioteca.html'>Documentación Técnica Biblioteca </a>","Crear, Consultar, Listar","<li>Crear, Consultar, Listar.</li>")

add("<a href='DocumentacionTecnica/DocTech_Prestamos.html'>Informes Tecnicos </a>","Mis Prestamos, Solicitudes, Descargar Prestamos","<li>Mis Prestamos, Solicitudes, Descargar Prestamos.</li>")



/* Servicios*/
add("<a href='Servicios/Serv_FormasyProcedimientos.html'>Servicios Formatos y Procedimientos </a>"," Crear, Consultar, Listar","<li> Crear, Consultar, Listar.</li>")

add("<a href='Servicios/Serv_SolicitudServ.html'>Servicios Solicitud de Servicios </a>"," Crear, Consultar, Listar","<li> Crear, Consultar, Listar.</li>")

add("<a href='Servicios/Serv_ApoyoalUsuario.html'>Servicios Apoyo al Usuario </a>"," Crear, Consultar, Listar","<li> Crear, Consultar, Listar.</li>")



/*Logueo*/

add("<a href='Logueo/Logueo.html'>Logueo en Simad </a>","Loguin, password, olvido, contrasena, Logueo","<li>Logueo en Simad , es un instructivo donde usted podra guiarse paso a paso en el proceso de ingreso al sistema.</li>")

add("<a href='Logueo/OlvidodeContrasena.html'>Olvido de contrasena en Simad </a>","Loguin, password, olvido, contrasena, Logueo","<li>Si usted ha olvidado su contrasena en Simad , este instructivo le guiara paso a paso en el proceso de recuperacion de su contrasena.</li>")

/*Mapa de Iconos*/

add("<a href='MapaIconos/MapaIconos.html'>Iconos de Simad </a>","SIMAD, iconos, mapa, significado, botones","<li>El Mapa de iconos le mostrara la totalidad de los iconos utilizados en el Simad, y sus funciones.</li>")

/*Calendario*/

add("<a href='Calendario/Calendario.html'>Uso del Calendario Simad </a>","calendario, fecha, fecha de apertura, fecha de cierre, año, mes, dia, semana","<li>Calendario Simad facilita la labor de escoger fechas para los registros en el sistema.</li>")

/*SIMADIG*/

add("<a href='simadig/simadig.html'>SIMADIG 1.0 </a>","simadig, escaner, digitalizacion, imagenes","<li>El sistema SIMADIG 1.0 es un software de Digitalización e Indexación de Documentos que nos permite tener en nuestras manos una poderosa herramienta para el manejo documental; ofreciéndole una amplia gama de herramientas para el mejoramiento de sus imágenes.</li>")

add("<a href='simadig/simadig_inicio.html'>Ingresar a SIMADIG 1.0 </a>","simadig, ingreso, logueo, usuario, password","<li>Cómo ingresar al módulo SIMADIG 1.0.</li>")

add("<a href='simadig/simadig_ventana_principal.html'>Ventana Principal SIMADIG 1.0 </a>","simadig, ingreso, ventana, principal, menu archivo, abrir, cerrar, imprimir, menu documento, guardar, copiar, pegar, eliminar pagina, deshacer, menu digitalizar, nuevo documento, insertar pagina, reemplazar pagina, reconocer ocr, ","<li>La Ventana Principal del Sistema SIMADIG 1.0  nos ofrece un entorno visual agradable y estratégicamente diseñado para que sea de un fácil manejo ofreciéndonos menús contextuales y botones de acceso a cada una de sus herramientas.</li>")

add("<a href='simadig/configurar_escaner.html'>Configurar Escaner en SIMADIG 1.0 </a>","simadig, escaner, configurar, configurar escaner, filtros escaner, formato de pagina","<li>A través de este modulo se selecciona el scaner para realizar la digitalización, se configuran los parámetros de digitalización, se aplican los filtros de scaner y se selecciona el tipo de papel a digitalizar.</li>")

add("<a href='simadig/indexacion_documentos.html'>Indexación de documentos en SIMADIG 1.0 </a>","simadig, indexar, indexacion, almacenar, almacenamiento, herramientas de escaner, navegar","<li>Con este menú podemos realizar el proceso de indexación de imágenes al aplicativo SIMAD.</li>")

/* end database records */


