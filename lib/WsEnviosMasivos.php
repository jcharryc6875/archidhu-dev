<?php
/**
 * @javier.charry 
 * @copyright 2022
 */
?>

<?php
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration);

// Borra las dos lineas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();
//**********************************************************************************************

class WsEnviosMasivos
{
    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;
    
    public function WsEnviosMasivos()
    {
        $this->nulog = 1;
        $this->nudebug = 0;
    }    
    
    public function writetolog($msg)
    {
       simad_util::writetolog($this->logfile,$msg);
    }
    
    public function wsComRecibidaMasivo()
    {
        include_once(__DIR__ .DIRECTORY_SEPARATOR. "WsUarivSimad.class.php");
        $marcausuario_id = 4;
		//**************************************************************************
        $wsUariv = new WsSimadUariv();
		//**************************************************************************
		print('HORA INCIO => '.date("Y-m-d G:i:s"). PHP_EOL);
        $wsUariv->initPrcessMasivo($marcausuario_id);
		print('HORA FIN => '.date("Y-m-d G:i:s"). PHP_EOL);
    }
	
	public function wsComRecibidaMasivoReply($ndias = 1)
    {
        include_once(__DIR__ .DIRECTORY_SEPARATOR. "WsUarivSimad.class.php");
        $marcausuario_id = 4;
		//**************************************************************************
		$fecha_actual = new DateTime(); 
		$fecha_actual->modify("-$ndias days");
		$new_fecha = $fecha_actual->format('Y-m-d');
		//**************************************************************************
		$isNullOrZero = true;
        $wsUariv = new WsSimadUariv();
		print('HORA INCIO => '.date("Y-m-d G:i:s"). PHP_EOL);
        $wsUariv->initPrcessMasivo($marcausuario_id,$new_fecha,$isNullOrZero);
		print('HORA FIN => '.date("Y-m-d G:i:s"). PHP_EOL);
    }

    public function wsComEnviadaReply()
    {
        include_once(__DIR__ .DIRECTORY_SEPARATOR. "WsUarivSimad.class.php");
        $marcausuario_id = 4;
		//**************************************************************************
        $wsUariv = new WsSimadUariv();
		print('HORA INCIO => '.date("Y-m-d G:i:s"). PHP_EOL);
        $wsUariv->initComEnviadaReplyAsync();
		print('HORA FIN => '.date("Y-m-d G:i:s"). PHP_EOL);
    }

    public function wsComEnviadaResponse()
    {
        include_once(__DIR__ .DIRECTORY_SEPARATOR. "WsUarivSimad.class.php");
		//**************************************************************************
        $wsUariv = new WsSimadUariv();
		$total_rows = 50000;$rows_max = 2000;
		$iterador = $total_rows/$rows_max;
		for($i = 0; $i < $iterador; $i++){
			print('HORA INCIO => '.date("Y-m-d G:i:s"). PHP_EOL);			
			$wsUariv->initLinkResponseCom($rows_max);
			print('HORA FIN => '.date("Y-m-d G:i:s"). PHP_EOL);
		}
    }
	
	public function wsComServicioCertiMail($app_externa)
    {
        include_once(__DIR__ .DIRECTORY_SEPARATOR. "WsCertiMailApi.php");
		//**************************************************************************
        $provider = simad_util::readConfigFileApp(['provider_certi_email_enable']);
		$svc_certimail = isset($provider['provider_certi_email_enable']) ? $provider['provider_certi_email_enable'] : null;
		//**************************************************************************
        $wsCrtmailApi = null;
        if($svc_certimail == CertiEmailProviders::Andes){
            $wsCrtmailApi = new WsApiAndes();
        }elseif($svc_certimail == CertiEmailProviders::RMail){
            $wsCrtmailApi = new WsApiRMail();
        }else{
            return null;
        }
		//**************************************************************************
		print('HORA INCIO => '.date("Y-m-d G:i:s"). PHP_EOL);
        //**************************************************************************
        if($app_externa == ServicioAppExterna::CertiMail){
            $wsCrtmailApi->searchRegCertiMail();
        }elseif(method_exists($wsCrtmailApi, 'searchRegCorreoCertFisico')){
            $wsCrtmailApi->searchRegCorreoCertFisico();
        }
        //**************************************************************************
		print('HORA FIN => '.date("Y-m-d G:i:s"). PHP_EOL);
    }
}

$wsSimad = new WsEnviosMasivos();
if($argv[1]=="masivo_comrecibida"){
    $wsSimad->wsComRecibidaMasivo();   
}

if($argv[1]=="masivo_comrecibidareply"){
    $ndias = isset($argv[2]) ? $argv[2] : 10;
    $wsSimad->wsComRecibidaMasivoReply($ndias);   
}

if($argv[1]=="comenviada_wsreply"){
    $wsSimad->wsComEnviadaReply();   
}

if($argv[1]=="comenviada_linkto"){
    $wsSimad->wsComEnviadaResponse();   
}

if($argv[1]=="comcertimail_actas"){
    $wsSimad->wsComServicioCertiMail(ServicioAppExterna::CertiMail);   
}

if($argv[1]=="comsipost_actas"){
    //$wsSimad->wsComServicioCertiMail(ServicioAppExterna::CorreoNacional);   
}
?>