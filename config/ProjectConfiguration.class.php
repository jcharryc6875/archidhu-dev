<?php
require_once(dirname(__FILE__).'/../data/sf1.3/lib/autoload/sfCoreAutoload.class.php');
sfCoreAutoload::register();
//*******************************************************************************
$http = 'http';
$base_url = "";
if(!empty($_SERVER["HTTPS"])){
	if($_SERVER["HTTPS"] == 'on'){
		$http .= "s";
	}
}
//*******************************************************************************
if(isset($_SERVER["HTTP_HOST"])){
    //$base_url = $http . "://".$_SERVER["HTTP_HOST"];
	$base_url = $_SERVER["HTTP_HOST"];
}
//*******************************************************************************
$rootUrl = isset($_SERVER["HTTP_HOST"]) ? $http . "://".$_SERVER["HTTP_HOST"] : $base_url;
$rootUrlLocal = "http://sgdeapruebas.unidadvictimas.gov.co";
$rootUrlPublic = $http . "://sgdeapruebas.unidadvictimas.gov.co";
//*******************************************************************************
// Configuracion Variables de Entorno
$base_simad = "";
$theme_simad = "/theme/neon/";
$pd4ml_pdf = dirname(__FILE__).'/../lib/pd4ml';
sfConfig::set('base_simad', $base_simad);
sfConfig::set('theme_simad', $theme_simad);
sfConfig::set('pd4ml_pdf', $pd4ml_pdf);
//*******************************************************************************
sfConfig::set('rootUrl', $rootUrl);
sfConfig::set('localUrl', $rootUrlLocal);
sfConfig::set('publicUrl', $rootUrlPublic);
//*******************************************************************************
class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    sfYaml::setSpecVersion('1.1');
    //$this->enablePlugins('sfPropelPlugin');
    $this->enableAllPluginsExcept(array('sfDoctrinePlugin','sfProtoculousPlugin'));
    //****************************************************************************
    require_once(sfConfig::get('sf_lib_dir') . '/SqlServerSessionHandler.php');
    SqlServerSessionHandler::register();
    //****************************************************************************
	if(!defined('LINKTYPE')){
      define('LINKTYPE', 'MODAL');//MODAL|DEFAULT
    }
    //****************************************************************************
    if(!defined('SIGN_DIGITAL_ENABLE')){
      define('SIGN_DIGITAL_ENABLE', true);
    }
    //****************************************************************************
    if(!defined('MAX_SIZE_FILE_OCR')){
      define('MAX_SIZE_FILE_OCR', 1000000); // en bytes
    }
  }
}
