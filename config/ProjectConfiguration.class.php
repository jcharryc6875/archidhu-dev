<?php
require_once(dirname(__FILE__) . '/../data/sf1.5/lib/autoload/sfCoreAutoload.class.php');
require_once(dirname(__FILE__) . '/../lib/compat/php74_compat.php');
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
    $this->enablePlugins('sfPropelORMPlugin', 'sfCompat10Plugin', 'sfPropelAdmin10Plugin');
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
	//****************************************************************
    // Compatibilidad symfony 1.3 -> 1.5: sfAction::validate() y
    // sfAction::handleError() se eliminaron del core en 1.4, pero
    // sfValidationExecutionFilter (sfCompat10Plugin) los invoca cuando
    // la acción no define validateXxx() / handleErrorXxx().
    $this->dispatcher->connect('component.method_not_found', array($this, 'listenToCompat10ActionMethods'));
    $this->dispatcher->connect('context.method_not_found', array($this, 'listenToCompat13ContextMethods'));
  }
  
  public function listenToCompat10ActionMethods(sfEvent $event)
  {
    switch ($event['method']) {
      case 'validate':
        $event->setReturnValue(true);
        return true;

      case 'handleError':
        $event->setReturnValue(sfView::ERROR);
        return true;
    }

    return false;
  }

  /**
   * Compatibilidad symfony 1.3 -> 1.5: sfContext::retrieveObjects() se eliminó
   * en 1.4. Lo usa ObjectHelper (object_select_tag / objects_for_select).
   * Replica la implementación 1.3: delega en sfPropelDataRetriever.
   */
  public function listenToCompat13ContextMethods(sfEvent $event)
  {
    if ('retrieveObjects' !== $event['method']) {
      return false;
    }

    $arguments  = $event['arguments'];
    $class      = $arguments[0];
    $peerMethod = isset($arguments[1]) ? $arguments[1] : null;

    $event->setReturnValue(sfPropelDataRetriever::retrieveObjects($class, $peerMethod));

    return true;
  }
}
