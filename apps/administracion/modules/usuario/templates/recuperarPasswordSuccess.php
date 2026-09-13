<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object','jQuery');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="ARCHIDHU - Sistema de Gestion Documental Electronico de Archivo" />
	<meta name="author" content="PGD SAS" />

	<?php include_title(); ?>

	<link rel="stylesheet" href="<?php print $path_theme;?>assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/font-icons/entypo/css/entypo.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/bootstrap.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-core.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-theme.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/neon-forms.css">
	<link rel="stylesheet" href="<?php print $path_theme;?>assets/css/custom.css">

	<script src="<?php print $path_theme;?>assets/js/jquery-1.11.0.min.js"></script>
	<script>jQuery.noConflict();</script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->


</head>
<body class="page-body login-page login-form-fall" data-url="#">

<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">
var baseurl = '<?php print $base_path; ?>';
</script>
	
<div class="login-container">
	
	<div class="login-header login-caret">
		
		<div class="login-content">
			<a href="#" class="logo">
				<img src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="350" alt="" />
			</a>
			<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>43%</h3>
				<span>Recuperando Contrase&ntilde;a...</span>
			</div>
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			<form method="post" role="form" id="form_forgot_password">
				
				<div class="form-forgotpassword-success">
					<i class="entypo-check"></i>
					<h3>Tu contrase&ntilde;a ha sido restaurada.</h3>
					<p>Por favor revisa tu cuenta de correo, se te han enviado las instrucciones.</p>
				</div>

				<div class="form-login-error">
					<h3>Error</h3>
					<p>Enter <strong>demo</strong>/<strong>demo</strong> as login and password.</p>
				</div>
				
				<div class="form-steps">
					
					<div class="step current" id="step-1">
					
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="entypo-mail"></i>
								</div>
								<input type="text" class="form-control" name="username" id="username" placeholder="Usuario" autocomplete="off" />
							</div>
						</div>

						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="entypo-mail"></i>
								</div>
								
								<input type="text" class="form-control" name="cedula" id="cedula" placeholder="Cedula" autocomplete="off" />
							</div>
						</div>
						
						<div class="form-group">
							<button type="submit" class="btn btn-info btn-block btn-login">
								Recuperar Contrase&ntilde;a
								<i class="entypo-right-open-mini"></i>
							</button>
						</div>
					
					</div>
					
				</div>
				
			</form>
			
			
			<div class="login-bottom-links">
				
				<a href="<?php echo $base_path; ?>/backend.php/security/login" class="link">
					<i class="entypo-lock"></i>
					Regresar Pagina de Inicio
				</a>
				
			</div>
			
		</div>
		
	</div>
	
</div>


	<!-- Bottom scripts (common) -->
	<script src="<?php print $path_theme;?>assets/js/gsap/main-gsap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/bootstrap.js"></script>
	<script src="<?php print $path_theme;?>assets/js/joinable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/resizeable.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-api.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-forgotpassword.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.inputmask.bundle.min.js"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="<?php print $path_theme;?>assets/js/neon-custom.js"></script>


	<!-- Demo Settings -->
	<script src="<?php print $path_theme;?>assets/js/neon-demo.js"></script>

</body>
</html>