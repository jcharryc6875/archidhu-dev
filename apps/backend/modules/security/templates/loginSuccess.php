<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="windows-1252">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta name="description" content="ARCHIDHU - Sistema de Gestión de Documentos Electrónicos de Archivo" />
	<meta name="author" content="UARIV" />

	<title>ARCHIDHU | UARIV</title>
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cookie">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/css/footer-ultimate.css">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/css/Footer-with-social-media-icons.css">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/css/Google-Style-Login-.css">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/css/Hero-Clean-images.css">
    <link rel="stylesheet" href="<?php print $path_theme;?>login/assets/css/Pretty-Footer-.css">

	<script type="text/javascript">
		var baseurl = '<?php print $base_path; ?>';
	</script>
	<style type="text/css">
		.input-box label.error{
			color: red;
		}
		.form-login-error p{
		   color: #cf2148;
		   text-align: center;
		}
	</style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>login/assets/img/Logo_Colombia_potencia_web.png" width="178" height="70"></div>
            <div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="235" height="70"></div>
            <div class="col-md-4 text-center" style="padding: 20px;"><img src="<?php print $path_theme;?>login/assets/img/Logo_Unidad_2023_web.png" width="233" height="70"></div>
        </div>
    </div>
    <div class="login-card"><img class="profile-img-card" src="<?php print $base_path;?>/images/logo_users.png">
        <p class="profile-name-card"> </p>
		<form method="post" role="form" id="form_login" class="form-signin">
			<span class="reauth-email"> </span>
			<input class="form-control" type="text" name="username" id="username" required="" placeholder="Usuario ArchiDHU" autofocus="">
			<input class="form-control" type="password" name="password" id="password" required="" placeholder="Contrase&ntilde;a">
			<div class="checkbox">
                <div class="form-check">&nbsp;</div>
            </div>
            <button class="btn btn-primary btn-block btn-lg btn-signin" type="submit">Entrar</button>			
        </form>
		<div class="login-bottom-links">
			<a href="<?php echo $base_path; ?>/administracion.php/usuario/recuperarPassword" class="link">Olvid&oacute; su contrase&ntilde;a?</a>
		</div>
		<div class="form-login-error">
			<strong><p></p></strong>
		</div>
    </div>
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
                <div><i class="fa fa-globe footer-contacts-icon"></i>
                    <p class="footer-center-info email text-left">
                        <a href="https://mesadeservicios.unidadvictimas.gov.co/unidad/">Mesa de Servicio</a>
                </p>
                </div>
				 <div><i class="fa fa-whatsapp footer-contacts-icon"></i>
                    <p class="footer-center-info email text-left"> </p>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4 footer-about">
                <h4 style="font-size: 16px;">ARCHIDHU</h4>
                <div class="social-links social-icons"></div>
            </div>
        </div>
    </footer>
	<!-- Bottom scripts (common) -->
    <script src="<?php print $path_theme;?>login/assets/js/jquery.min.js"></script>
    <script src="<?php print $path_theme;?>login/assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/neon-login.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery.validate.min.js"></script>
	<script src="<?php print $path_theme;?>assets/js/jquery-validate-messages_es.js"></script>
	<script src="<?php print $path_theme;?>assets/js/simad-api.js?v=<?php echo(rand()); ?>"></script>
</body>
</html>
