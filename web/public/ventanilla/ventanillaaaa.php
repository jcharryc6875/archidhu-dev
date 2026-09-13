<?php
$basepath = "/";
$urlApi = "http://172.206.254.234/public/restapi/ventanilla/v1/%s";
//*****************************************************************************
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, sprintf($urlApi,"regionales"));
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
//*****************************************************************************
$results =  json_decode($response,true);
$regionales = array();
if(!$results['error']){
    $regionales = json_decode($results["object"],true);
}
//*****************************************************************************
curl_setopt($ch, CURLOPT_URL, sprintf($urlApi,"peticionario"));
$response = curl_exec($ch);
//*****************************************************************************
$results =  json_decode($response,true);
$peticionarios = array();
if(!$results['error']){
    $peticionarios = json_decode($results["object"],true);
}
//*****************************************************************************
curl_setopt($ch, CURLOPT_URL, sprintf($urlApi,"tpeticion"));
$response = curl_exec($ch);
$results =  json_decode($response,true);
$tipos_peticiones = array();
if(!$results['error']){
    $tipos_peticiones = json_decode($results["object"],true);
}
//*****************************************************************************
curl_setopt($ch, CURLOPT_URL, sprintf($urlApi,"tidentificacion"));
$response = curl_exec($ch);
$results =  json_decode($response,true);
$tipos_identificacion = array();
if(!$results['error']){
    $tipos_identificacion = json_decode($results["object"],true);
}
//*****************************************************************************
curl_setopt($ch, CURLOPT_URL, sprintf($urlApi,"paises"));
$response = curl_exec($ch);
$results =  json_decode($response,true);
$paises = array();
if(!$results['error']){
    $paises = json_decode($results["object"],true);
}
//*****************************************************************************
//session_start();
$tokenform = md5(uniqid(rand(), true));
//$_SESSION['uidtoken'] = $tokenform;
//print_r($_SESSION);echo "HOLA..";
//*****************************************************************************
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
        <link rel="stylesheet" href="<?php echo $basepath ?>theme/neon/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
        <link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/font-icons/entypo/css/entypo.css"/>
    	<link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/bootstrap.css"/>
    	<link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/neon-core.css"/>
    	<link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/neon-theme.css"/>
    	<link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/neon-forms.css"/>            
        <link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/js/select2/select2-bootstrap.css"/>
        <link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/js/select2/select2.css"/>
        <link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/skins/yellow.css" rel="stylesheet"/>
        <link rel="stylesheet" href="<?php echo $basepath; ?>theme/neon/assets/css/custom.css"/>
             	
        <script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery-1.11.0.min.js"></script>
        <script>$.noConflict();</script>
        
        <style>
        .control-label{
            font-weight: bold;
        }
        .input-sm {
            border-color: #a4a1a1;
        }
        .form-wizard > ul > li.active a span, .form-wizard > ul > li.current a span {
        	background: #f40f0f;
        	color: #fdfbfb;        
        }
        </style>
    </head>
    <body class="page-body gray page-left-in" data-url="http://www.aureasas.com">
        <div class="sidebar-collapsed" style="padding: 10px;">
            <div class="main-content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Contenedor Pagina -->
                        <div class="panel panel-gradient" data-collapsed="0">      
                            <div class="panel-heading">
                                <div class="panel-title">
                                Ventanilla Virtual
                                </div>
                            </div>                            
                                <!-- Contenedor Contenido Formulario-->
                                <div class="panel-body">
                                    <!--form id="rootwizard" method="post" enctype="multipart/form-data" action="" class="form-wizard validate"-->
                                    <form id="rootwizard" method="post" enctype="multipart/form-data" action="" class="form-wizard">
                                        <input type="hidden" name="urlapi" id="urlapi" value="<?php echo sprintf($urlApi,"radicar"); ?>" />
                                        <input type="hidden" name="uidtoken" id="uidtoken" class="realinputvalue" value="<?php echo $tokenform; ?>" />                                
                                        <div class="steps-progress">
                            				<div class="progress-indicator"></div>
                            			</div>                        			
                            			<ul>
                            				<li class="active">
                            					<a href="#tab2-1" data-toggle="tab"><span>1</span>Datos de la Solicitud</a>
                            				</li>
                            				<li>
                            					<a href="#tab2-2" data-toggle="tab"><span>2</span>Datos de Identificaci&oacute;n y Contacto</a>
                            				</li>
                            				<!--li>
                            					<a href="#tab2-3" data-toggle="tab"><span>3</span>Ubicaci&oacute;n y Contacto</a>
                            				</li-->
                            				<li>
                            					<a href="#tab2-4" data-toggle="tab"><span>3</span>Contenido de la Solicitud</a>
                            				</li>
                            				<li>
                            					<a href="#tab2-5" data-toggle="tab"><span>4</span>Documentos y Anexos</a>
                            				</li>
                            			</ul>
										
                                        <div class="tab-content">
                                            <!-- Inicio tab Datos de la Solicitud -->
        			                        <div class="tab-pane active" id="tab2-1">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                    	<div class="form-group">
                                                    		<!-- Regional/Sede -->
                                                    		<label for="lbRegional" class="control-label">Regional/Sede:</label>
                                                    		<select name="nregional_id" name="nregional_id" class="realinputvalue form-control input-sm required">
                                                    			<option value="">Seleccione...</option>
                                                                <?php foreach($regionales as $clave => $valor){ ?>
                                                        			<option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                                                                <?php } ?>
                                                    		</select>
                                                    	</div>
                                                    </div>
                                                </div>
                                                    
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                    	<div class="form-group">
                                                    		<!-- Tipo Peticionario -->
                                                    		<label for="lbTipoPeticionario" class="control-label">Tipo Peticionario:</label>
                                                    		<select id="ntipopeticionario_id" name="ntipopeticionario_id" class="realinputvalue form-control input-sm required">
                                                    			<option value="">Seleccione...</option>
                                                                <?php foreach($peticionarios as $clave => $valor){ ?>
                                                        			<option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                                                                <?php } ?>
                                                    		</select>
                                                    	</div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                    	<div class="form-group">
                                                    		<!-- Tipo de Petición -->
                                                    		<label for="lbTipoPeticion" class="control-label">Tipo de Petici&oacute;n:</label>
                                                            <select id="ntipopeticion_id" name="ntipopeticion_id" class="realinputvalue form-control input-sm required">
                                                    			<option value="">Seleccione...</option>
                                                                <?php foreach($tipos_peticiones as $clave => $valor){ ?>
                                                        			<option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                                                                <?php } ?>
                                                    		</select>
                                                    	</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Fin tab Datos de la Solicitud -->
                                        
                                            <!-- Inicio tab datos identificacion y Contacto -->
                                            <div class="tab-pane" id="tab2-2">
                                                <div class="row idatapry">
                                                    <div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Tipo Identificacion -->
                                            				<label for="lbPrefijo" class="control-label">Tipo de Identificaci&oacute;n:</label>
                                                            <select id="ntipoidentificacion_id" name="ntipoidentificacion_id" class="realinputvalue form-control input-sm required">
                                            					<option value="">Seleccione...</option>
                                                                <?php foreach($tipos_identificacion as $clave => $valor){ ?>
                                                        			<option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                                                                <?php } ?>
                                            				</select>
                                            			</div>
                                            		</div>
                                            		<div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Número Identificación -->
                                            				<label for="lbNumId" class="control-label">N&uacute;mero Identificaci&oacute;n:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm required" name="num_identificacion" id="num_identificacion" data-validate="number" placeholder="Numero identificaci&oacute;n..." />
                                            			</div>
                                            		</div>
                                                </div>
                                                
                                                <div class="row idatapry">
                                                    <div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Primer Nombre -->
                                            				<label for="lbNombre1" class="control-label">Primer Nombre:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm required" id="primer_nombre" name="primer_nombre" placeholder="Primer Nombre..." />
                                            			</div>
                                            		</div>
                                            		<div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Segundo Nombre -->
                                            				<label for="lbNombre2" class="control-label">Segundo Nombre:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm" id="segundo_nombre" name="segundo_nombre" placeholder="Segundo Nombre..." />
                                            			</div>
                                            		</div>
                                                </div>
                                                  
                                                <div class="row idatapry">
                                                    <div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Primer Apellido -->
                                            				<label for="lbApellido1" class="control-label">Primer Apellido:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm required" id="primer_apellido" name="primer_apellido" placeholder="Primer Apellido..." />
                                            			</div>
                                            		</div>
                                            		<div class="col-sm-6">
                                            			<div class="form-group">
                                            				<!-- Segundo Apellido -->
                                            				<label for="lbApellido2" class="control-label">Segundo Apellido:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm" id="segundo_apellido" name="segundo_apellido" placeholder="Segundo Apellido..." />
                                            			</div>
                                            		</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                            			<div class="form-group">
                                            				<!-- Pais -->
                                            				<label for="lbPains" class="control-label">Pais:</label>
                                                            <select id="npais_id" name="npais_id" class="realinputvalue form-control input-sm">
                                            					<option value="">Seleccione...</option>
                                                                <?php foreach($paises as $clave => $valor){ ?>
                                                        			<option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                                                                <?php } ?>
                                            				</select>
                                            			</div>
                                            		</div>
                                            		<div class="col-sm-4">
                                            			<div class="form-group">
                                            				<!-- Departemento/Estado -->
                                            				<label for="lbDepartamento" class="control-label">Departemento/Estado:</label>
                                                            <select id="ndepartamento_id" name="ndepartamento_id" class="realinputvalue form-control input-sm">
                                            					<option value="">Seleccione...</option>
                                            				</select>
                                            			</div>
                                            		</div>
                                                    <div class="col-sm-4">
                                            			<div class="form-group">
                                            				<!-- Ciudad -->
                                            				<label for="lbCiudad" class="control-label">Ciudad/Municipio:</label>
                                                            <select id="nciudad_id" name="nciudad_id" class="realinputvalue form-control input-sm required">
                                            					<option value="">Seleccione...</option>
                                            				</select>
                                            			</div>
                                            		</div>
                                                </div>
                                                  
                                                <div class="row">
                                                   <div class="col-sm-4">
                                            			<div class="form-group">
                                            				<!-- Email -->
                                            				<label for="lbEmail" class="control-label">Email:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm" id="email" name="email" data-validate="email" placeholder="Email..." />
                                            			</div>
                                          		   </div>
                                            	   <div class="col-sm-4 idatapry">
                                            			<div class="form-group">
                                            				<!-- Direccion -->
                                            				<label for="lbDireccion" class="control-label">Direcci&oacute;n:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm required" id="ndireccion" name="ndireccion" placeholder="Direcci&oacute;n..." />
                                            			</div>
                                            	   </div>
                                                   <div class="col-sm-4 idatapry">
                                            			<div class="form-group">
                                            				<!-- Telefono -->
                                            				<label for="lbEmail" class="control-label">Telefono:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm" id="ntelefono" name="ntelefono" placeholder="Telefono..." />
                                            			</div>
                                            	   </div>
                                                </div>
                                            </div>
                                            <!-- Fin tab datos identificacion -->
                                                                                                                                
                                            <!-- Inicio tab Contenido de la Solicitud -->
                                            <div class="tab-pane" id="tab2-4">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                            			<div class="form-group">
                                            				<!-- Asunto -->
                                            				<label for="lbAsunto" class="control-label">Asunto:</label>
                                            				<input type="text" class="realinputvalue form-control input-sm required" id="nasunto" name="nasunto" placeholder="Digite el asunto" />
                                            			</div>
                                            		</div>
                                                </div>
                                                  
                                                <div class="row">
                                                    <div class="col-sm-12">
                                            			<div class="form-group">
                                            				<!-- Descripcion -->
                                            				<label for="lbAsunto" class="control-label">Descripci&oacute;n:</label>
                                            				<textarea id="nobservaciones" name="nobservaciones" class="realinputvalue form-control input-sm" placeholder="Digite la descripci&oacute;n..."></textarea>
                                            			</div>
                                            		</div>
                                                </div>
                                            </div>
                                            <!-- Fin tab Contenido de la Solicitud -->
                                        
                                            <!-- Inicio tab Documentos y Anexos -->
                                            <div class="tab-pane" id="tab2-5">
                                                <div class="row">
                                                    <div class="col-sm-5">
                                            			<div class="form-group">
                                            				<!-- Documento de la solicitud -->
                                            				<label for="lbComFile" class="control-label">Documento solicitud:</label>
                                            				<input name="file" id="file" class="realinputvalue form-control input-file" accept="application/pdf,application/msg,image/tif,image/tif" multiple type="file"/>
                                            			</div>
                                            		</div>
                                                    <div class="col-sm-5">
                                            			<div class="form-group">
                                            				<!-- Anexos -->
                                            				<label for="lbComAnexos" class="control-label">Anexos:</label>
                                            				<input name="attachments" id="attacments" class="realinputvalue form-control input-file" multiple type="file" accept="image/*,application/pdf,application/msword, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                                            			</div>
                                            		</div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-5">
                                            			<div class="form-group">
                                            				<blockquote class="blockquote-gold">
                                            					<p><strong style="color: #981b1b;">Observaciones</strong></p>
                                            					<p>
                                            						<small>Los Tipos de archivo permitidos: <b style="color: black;font-weight: bold;">PDF,TIF,MSG</b></small>
                                            					</p>
                                                                <p>
                                            						<small>Los archivos deben ser menores a 20 MB.</small>
                                            					</p>
                                            				</blockquote>
                                            			</div>
                                            		</div>
                                                    <div class="col-sm-5">
                                            			<div class="form-group">
                                            				<blockquote class="blockquote-gold">
                                            					<p><strong style="color: #981b1b;">Observaciones</strong></p>
                                            					<p>
                                            						<small>Los Tipos de archivo permitidos: <b style="color: black;font-weight: bold;">JPG,PNG,PDF,DOC,TIFF,XLS,DOCX</b></small>
                                            					</p>
                                                                <p>
                                            						<small>Los archivos deben ser menores a 20 MB.</small>
                                            					</p>
                                            				</blockquote>
                                            			</div>
                                            		</div>
                                                </div>
                                                
                                                <hr />
                                                
                                                <div class="row">
                                                    <div class="col-sm-5">
                                            			<div class="form-group">
                                                            <p><a href="#" id="datos_popup" data-toggle="modal" data-target="#politica"><strong>Ver política de uso y tratamiento de datos</strong></a></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                
                                                 <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 item">
                                                            <input class="form-check-input required" type="checkbox" id="datapersonal" name="datapersonal" value="1"/>
                                                            <label for="check1-1" style="display: inline;"><span style="color:red"> * </span>Autorizo el uso y tratamiento de mis datos personales según la política de uso y tratamiento de datos.</p></label>
                                                        </div>
                                                    </div>
                                                 </div>
                                                 
                                                 <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 item">
                                                            <input class="form-check-input required" type="checkbox" id="emailverify" name="emailverify" value="2"/>
                                                            <label for="check1-2" class="text-justify" style="display: inline;"><span style="color:red"> * </span>Certifico que el correo electrónico ingresado en mis datos personales se encuentra vigente, de igual manera autorizo a la Secretaría Distrital de Gobierno para el envío de la respuesta a mi solicitud por este medio, de conformidad con el artículo 56 y el numeral primero del artículo 67 del Código de Procedimiento Administrativo y de lo Contencioso Administrativo.</label>
                                                        </div>
                                                    </div>
                                                 </div>

                                                <div align="right">
                        							<button id="submitajax" class="btn btn-primary btn-large">
                        								<i class="icon-download-alt icon-white"></i> Radicar Solicitud
                        							</button>
                        							<img src="img/spinner.gif" id="ajaxspinner" style="display: none;" />
                        						</div>
                                                <hr />
                                                <!--div class="form-group">
                            						<button type="submit" class="btn btn-primary">Radicar Solicitud</button>
                            					</div-->
                                                <div id="messagediv"></div>
                                            </div>
                                            <!-- Fin tab Documentos y Anexos -->
                                        
                                            <ul class="pager wizard">
                            					<li class="previous">
                            						<a href="#"><i class="entypo-left-open"></i> Anterior</a>
                            					</li>
                            					<li class="next">
                            						<a href="#">Siguiente <i class="entypo-right-open"></i></a>
                            					</li>
                            				</ul>
                                        </div>
                                </form>                                                                                                        
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <!-- Modal 4 (Confirm)-->
    	<div class="modal fade" id="modal-4" data-backdrop="static">
    		<div class="modal-dialog">
    			<div class="modal-content">
    				
    				<div class="modal-header">
    					<h4 class="modal-title">Radicaci&oacute;n Exitosa</h4>
    				</div>
    				
    				<div class="modal-body">    				    					
    					
    				</div>
    				
    				<div class="modal-footer">
    					<button type="button" class="btn btn-info" data-dismiss="modal">Continue</button>
    				</div>
    			</div>
    		</div>
    	</div>    
    </body>
</html>
<!-- Bottom scripts (common) -->
<script src="<?php echo $basepath; ?>theme/neon/assets/js/gsap/main-gsap.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/bootstrap.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/joinable.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/resizeable.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/neon-api.js"></script>
    
<!-- Imported scripts on this page -->
<script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery.bootstrap.wizard.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery.validate.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery-validate-messages_es.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/select2/select2.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/select2/select2_locale_es.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/bootstrap-datepicker.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo $basepath; ?>theme/neon/assets/js/external-custom.js"></script>
<script>
var qudep = '<?php echo sprintf($urlApi,"departamentos"); ?>';
var quciudad = '<?php echo sprintf($urlApi,"ciudades"); ?>';
</script>