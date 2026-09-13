<?php
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';

//$_SERVER['HTTPS'] = 'on';

/*if((!empty( $_SERVER['HTTP_X_FORWARDED_HOST'])) || (!empty( $_SERVER['HTTP_X_FORWARDED_FOR'])) ) {
 	$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
 	$_SERVER['HTTPS'] = 'on';
}*/
 //phpinfo();
 
$isAuthenticated = sfContext::getInstance()->getUser()->isAuthenticated();
if($isAuthenticated == 1){
	header("Location: ".$base_path."backend.php/resumen");
}else{
	header("Location: ".$base_path."backend.php/security/login");
}
