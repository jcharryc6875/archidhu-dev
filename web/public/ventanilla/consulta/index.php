<?php

require_once __DIR__ . '/icon-captcha/vendor/autoload.php';

/*$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');*/
$path_theme = '/theme/neon/';
$base_path = '/';

$includeDiv = true;

// Start a session.
// * Required when using any 'session' driver in the configuration.
// * Required when using the IconCaptcha Token, referring to the use of 'IconCaptchaToken' in the form below.
// For more information, please refer to the documentation.
session_start();
?>

<?php
//$urlApi = "http://172.206.254.234/public/restapi/ventanilla/v1/%s";
$urlApi = $base_path . "public/restapi/ventanilla/v1/%s";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    	<meta charset="utf-8"/>
        <!--meta charset="iso-8859-1"-->
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    	<meta name="description" content="Ventanilla Única Virtual" />
    	<meta name="author" content="Aurea SAS" />
        <title>SGDEA | Ventanilla Única Virtual</title>

        <!-- Imported scripts on this page -->
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/font-icons/entypo/css/entypo.css"/>
    	<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/bootstrap.css"/>
    	<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/neon-core.css"/>
    	<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/neon-theme.css"/>
    	<link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/neon-forms.css"/>
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/js/select2/select2-bootstrap.css"/>
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/js/select2/select2.css"/>
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/skins/yellow.css" rel="stylesheet"/>
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/custom.css"/>
		<link rel="stylesheet" href="<?php echo $path_theme;?>login/assets/css/Footer-with-social-media-icons.css">
		<link rel="stylesheet" href="<?php echo $path_theme;?>login/assets/css/Google-Style-Login-.css">
		<link rel="stylesheet" href="<?php echo $path_theme;?>login/assets/css/Hero-Clean-images.css">
		<link rel="stylesheet" href="<?php echo $path_theme;?>login/assets/css/Pretty-Footer-.css">
        <link rel="stylesheet" href="<?php echo $path_theme; ?>assets/css/oficina_virtual.css"/>

        
        <!-- Include IconCaptcha stylesheet - REQUIRED -->
        <link href="icon-captcha/assets/client/css/iconcaptcha.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo $path_theme; ?>assets/js/jquery-1.11.0.min.js"></script>
        <script>$.noConflict();</script>
    </head>
    <body>
        <div class="sidebar-collapsed" style="padding: 10px;">
            <div class="main-content">
                <div class="row">
					<div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>login/assets/img/Logo_Colombia_potencia_web.png" width="178" height="70"></div>
					<div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="235" height="70"></div>
					<div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>login/assets/img/Logo_Unidad_2023_web.png" width="233" height="70"></div>
				</div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Contenedor Pagina -->
                        <div class="panel panel-warning" id="num_radicado" data-collapsed="0">      
                            <div class="panel-heading">
                                <div class="panel-title">
									Sede Electr&oacute;nica
                                </div>
                            </div>    
                            <!--div style="padding: 0 20px;">
                                <h2>Validador de documentos</h2>
                                <p>A través de nuestra Sede Electrónica la ciudadanía en general podrá realizar trámites, servicios, seguimiento al estado de los trámites, presentación de radicado y descarga de información de interés, validar la autenticidad de los documentos generados por nuestra entidad.</p>
                            </div>                
							<hr-->
                            <div class="row"> 
								<div class="col-md-12">
									<ul class="nav nav-tabs bordered" style="background-image: linear-gradient(to bottom, #ffffff 0%, #e5e5e5 100%); border: solid 2px #e5e5e5;"><!-- available classes "bordered", "right-aligned" -->
										<li class="active">
											<a href="#home" data-toggle="tab">
												<span class="visible-xs"><i class="entypo-home"></i></span>
												<span class="hidden-xs"><strong>? Como usar</strong></span> 
											</a>
										</li>
										<li>
											<a href="#profile" data-toggle="tab">
												<span class="visible-xs"><i class="entypo-user"></i></span>
												<span class="hidden-xs"><strong>Validar CSV</strong></span>
											</a>
										</li>
										<li>
											<a href="#messages" data-toggle="tab">
												<span class="visible-xs"><i class="entypo-mail"></i></span>
												<span class="hidden-xs"><strong>Validar Documento</strong></span>
											</a>
										</li>
										<li>
											<a href="#settings" data-toggle="tab">
												<span class="visible-xs"><i class="entypo-cog"></i></span>
												<span class="hidden-xs"><strong>Consultar Tr&aacute;mite</strong></span>
											</a>
										</li>
										<li>
											<a href="#radicar" data-toggle="tab">
												<span class="visible-xs"><i class="entypo-cog"></i></span>
												<span class="hidden-xs"><strong>Radicar Tr&aacute;mite</strong></span>
											</a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="home">  
																		
											<p>Hay tres mecanismos para realizar la validaci&oacute;n:</p>
											<ol>
												<li>Si tiene el documento electrónico y desea conocer si el hash es válido, vaya a la pestaña "Validar Documento".</li>
												<li>Si tiene el Csv y quiere ver el documento como quedó almacenado en el SGDEA o compararlo con el que recibió, vaya a la pestaña "Validar CSV".</li>
												<li>Si tiene el número de indizado del documento, vaya a la pestaña "Validar Id documento indizado".</li>
											</ol>
										</div>
										<div class="tab-pane" id="profile">
											<fieldset>
												<?php
													include_once('_validarHash.php');  
												?>
												<?php include_once('_validarHashView.php'); ?>  
											</fieldset>
												
										</div>
										<div class="tab-pane" id="messages">    
											<fieldset>
												<?php include_once('_validarDocumento.php'); ?>
												<?php include_once('_validarDocumentoView.php'); ?>
											</fieldset>
										</div>
										<div class="tab-pane" id="settings">
											<?php include_once('_customBodySearch.php'); ?>
											<?php include_once('_customBodyViews.php'); ?>                                                            
										</div>
										<div class="tab-pane" id="radicar">
												
											<?php include_once('../_radicar.php'); ?>      

										</div>
									</div>              
								</div>
                            </div>
                            <div class="row"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        
        <!-- Modal 4 (Confirm)-->
        <div class="modal fade" id="modal-4" data-backdrop="static"> <!-- class="modal fade" -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Radicaci&oacute;n Exitosa</h4>
                    </div>
                    <div class="modal-body">    				    					
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success endradprocess" data-dismiss="modal">Finalizar</button>
                    </div>
                </div>
            </div>
        </div>
		<!-- footer -->
		<footer>
			<div class="row">
				<div class="col-sm-6 col-md-4 footer-navigation"><img src="<?php print $path_theme;?>login/assets/img/Logo_Colombia_potencia_web_blanco.png" width="215" height="84">
					<h3><a href="#"></a></h3>
					<p class="company-name"></p>
				</div>
				<div class="col-sm-6 col-md-4 footer-contacts">
					<div><i class="fa fa-phone footer-contacts-icon"></i>
						<p class="footer-center-info email text-left"> (601) 5082238 Ext. 2303 o 5 desde tu extensi&oacute;n</p>
					</div>
					<div><i class="fa fa-envelope footer-contacts-icon"></i>
						<p> <a href="#" target="_blank">SoporteOTI@unidadvictimas.gov.co</a></p>
					</div>
					<div><i class="fa fa-envelope footer-contacts-icon"></i>
						<p> <a href="#" target="_blank">soportearchidhu@unidadvictimas.gov.co</a></p>
					</div>
					<div><i class="fa fa-whatsapp footer-contacts-icon"></i>
						<p class="footer-center-info email text-left"> 3188725295</p>
					</div>
					<div><i class="fa fa-globe footer-contacts-icon"></i>
						<p class="footer-center-info email text-left">
							<a href="https://mesadeservicios.unidadvictimas.gov.co/unidad/">Mesa de Servicio</a>
					</p>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-4 footer-about">
					<h4 style="font-size: 16px;">ARCHIDHU SGDEA - V6.0.154</h4>
					<div class="social-links social-icons"></div>
				</div>
			</div>
		</footer>
    </body>
</html>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        // si llega por GET hashcom redirecciona al tab de Validar el Hash
        if (window.location.search.includes('hashcom'))  
        {
            activaTab('profile');
            muestraHash('hashcom'); 
        }

        /* Initialize the IconCaptcha - REQUIRED  */
        jQuery('.iconcaptcha-widget').iconCaptcha(
        {
            general: {
                endpoint: 'icon-captcha/captcha-request.php',
                fontFamily: 'inherit',
            },
            security: {
                interactionDelay: 1000,
                hoverProtection: true,
                displayInitialMessage: true,
                initializationDelay: 500,
                incorrectSelectionResetDelay: 3000,
                loadingAnimationDuration: 1000,
            },
            locale: {
                initialization: {
                    verify: 'Verifique que usted es un humano.',
                    loading: 'Cargando reto...',
                },
                header: 'Seleccione la imagen que se muestra la menor cantidad de veces',
                correct: 'Verificacion completa.',
                incorrect: {
                    title: 'Uh oh.',
                    subtitle: "Usted ha seleccionado la imagen incorrecta"
                },
                timeout: {
                    title: 'Por favor espere',
                    subtitle: 'Ha hecho demasiadas selecciones incorrectas'
                }
            }
        });
        // .bind('init', function(e) { // You can bind to custom events, in case you want to execute custom code.
        //     console.log('Event: Captcha initialized', e.detail.captchaId);
        // }).bind('selected', function(e) {
        //     console.log('Event: Icon selected', e.detail.captchaId);
        // }).bind('refreshed', function(e) {
        //     console.log('Event: Captcha refreshed', e.detail.captchaId);
        // }).bind('invalidated', function(e) {
        //     console.log('Event: Invalidated', e.detail.captchaId);
        // }).bind('reset', function(e) {
        //     console.log('Event: Reset', e.detail.captchaId);
        // }).bind('success', function(e) {
        //     console.log('Event: Correct input', e.detail.captchaId);
        // }).bind('error', function(e) {
        //     console.log('Event: Wrong input', e.detail.captchaId);
        // });

        /**/   
        
        

        /********************************************************************** */
        /****** FUNCION ACTIVA TAB */
        function activaTab(elTab)
        {
            jQuery('.nav-tabs a[href="#' + elTab + '"]').tab('show');
        }

        function muestraHash(elHash)
        {
            var urlParams = new URLSearchParams(window.location.search);
            var valor_Hash = urlParams.get(elHash);
            var valorHash = valor_Hash.replace(/ /g, "+");
            //console.log(valorHash);
            jQuery('#strhash').val(valorHash);    
        }

        function limpiaForm()
        {
            jQuery('#strhash').val('');
            jQuery('#radicado').val('');
        }


    });
</script>

<!-- Bottom scripts (common) -->
<script src="<?php print $path_theme;?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/gsap/main-gsap.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/joinable.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/resizeable.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/neon-api.js"></script>
<script src="<?php print $path_theme;?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?php print $path_theme;?>assets/js/simad-api.js?v=<?php echo(rand()); ?>"></script>

    
<!-- Imported scripts on this page -->
<script src="<?php echo $path_theme; ?>assets/js/jquery.bootstrap.wizard.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/jquery-validate-messages_es.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/select2/select2.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/select2/select2_locale_es.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap-datepicker.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/external-custom.js?v=<?php echo(rand()); ?>"></script>

<!-- Include IconCaptcha script - REQUIRED -->
<script src="icon-captcha/assets/client/js/iconcaptcha.min.js" type="text/javascript"></script>

<script src="<?php print $path_theme;?>assets/js/dataTables.bootstrap.js"></script>
<script src="<?php print $path_theme;?>assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
<script>
var qudep = '<?php echo sprintf($urlApi,"departamentos"); ?>';
var quciudad = '<?php echo sprintf($urlApi,"ciudades"); ?>';
</script>