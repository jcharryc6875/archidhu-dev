<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('clientes', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
//*******************************************************************************
$isAuthenticated = sfContext::getInstance()->getUser()->isAuthenticated();
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*******************************************************************************
if($isAuthenticated != 1)
{
    //$header_redirect = "Location: /backend.php/security/login?postbackurl=".$_SERVER['REQUEST_URI'];
	$header_redirect = "Location: ".$base_path;
    //***************************************************************************
    header($header_redirect);
    //***************************************************************************
}