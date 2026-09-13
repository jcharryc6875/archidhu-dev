<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('apoyoUsuario', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
//*******************************************************************************
$isAuthenticated = sfContext::getInstance()->getUser()->isAuthenticated();
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//*******************************************************************************
if($isAuthenticated != 1)
{
    //***************************************************************************
    $request  = sfContext::getInstance()->getRequest();
    $response = sfContext::getInstance()->getResponse();
    //***************************************************************************
	//header("Location: /backend.php/security/login?postbackurl=".$_SERVER['REQUEST_URI']);
	$header_redirect = "Location: ".$base_path;
	header($header_redirect);
}