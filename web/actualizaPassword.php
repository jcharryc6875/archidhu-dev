<?php
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('administracion', 'prod', false);
sfContext::createInstance($configuration)->dispatch();

$path_theme = sfConfig::get('theme_simad');
$base_path = simad_util::endsWith(sfConfig::get('base_simad'),"/") ? sfConfig::get('base_simad') : "/";

header('Location: '.$base_path.'administracion.php/usuario/cambioPassword?porfechaactualizacion=1');
?>