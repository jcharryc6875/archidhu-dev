<?php
/**
 * @javier.charry 
 * @copyright 2008
 */
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', true);
sfContext::createInstance($configuration);

// Borra las dos líneas siguientes si no utilizas una base de datos
$databaseManager = new sfDatabaseManager($configuration);
$databaseManager->loadConfiguration();

include_once(sfConfig::get('sf_lib_dir')."/Fechas.class.php");
?>

<?php
class simadAlerta
{

    var $logfile;
    var $nulog;
    var $nudebug;
    var $instance;
    
    public function simadAlerta()
    {
          $this->logfile=sfConfig::get("sf_log_dir")."\send_alert.log";      
          $this->nulog=1;
          $this->nudebug=0;
    }
    
    public function setSendAlert()
    {
          $this->logfile=sfConfig::get("sf_log_dir")."\send_alert.log";
          $this->nulog=1;
          $this->nudebug=0;
    }
    
    
    public function writetolog($msg)
    {
       if($this->nulog){         
             $f=fopen($this->logfile,"a");
          if($f){
                  fprintf($f,"\n%s=>%s\t\r",date("Y-m-d G:i:s"),$msg);
          }
          fclose($f);
              if($this->nudebug){
                  echo "<pre>".$msg."</pre>";
              }
           }
    }
    
    /**
    * send_alert::sendAlertsComRecibida()
    * funcion para enviar alertas para las comunicaciones externas recibidas
    * donde falta un dia para vencercen la accion legal
    * @return
    */
    public function sendAlertsComRecibida()
    {    
        //$this->writetolog("Externa Recibida Alerta enviada datos: prueba");    
        /******************************************************************************************************/
      	$fecha = AddDays(date("Y-m-d"),1);
      	/******************************************************************************************************/	
    	$c  = new Criteria();
    	//$c->setDistinct();	
    	$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,1);
    	$c->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, date("Y-m-d") , Criteria::GREATER_EQUAL);
    	$c->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA, $fecha , Criteria::LESS_EQUAL);
    	$c->add(ComRecibidaPeer::COMENVIADA_ID,NULL,Criteria::ISNULL);
    	$c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
    	$c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,true);
    	$c->addAscendingOrderByColumn(ComRecibidaPeer::COMRECIBIDA_ID);
        $c->clearSelectColumns();
        /****************************************JOINS*********************************************************/
        $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $c->addJoin(ComRecibidaPeer::TIPOCOMRECIBIDA_ID,TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);
        $c->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID);
        $c->addJoin(ComRecibidaPeer::DIRECTORIOEXTERNO_ID,DirectorioExternoPeer::DIRECTORIOEXTERNO_ID);
        $c->addJoin(ComRecibidaPeer::ASUNTORECIBIDA_ID,AsuntoRecibidaPeer::ASUNTORECIBIDA_ID);
        /***************************************SELECT COLUMNAS*************************************************/
        $c->addSelectColumn(ComRecibidaPeer::RADICADO);//0
        $c->addSelectColumn(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA);//1
        $c->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);//2
        $c->addSelectColumn(DirectorioExternoPeer::NOMBRE);//3
        $c->addSelectColumn(AsuntoRecibidaPeer::DESCRIPCION);//4
        $c->addSelectColumn(ComRecibidaPeer::ASUNTO);//5
        $c->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);//6
        $c->addSelectColumn(ComRecibidaPeer::OBSERVACIONES);//7
        $c->addSelectColumn(UsuarioPeer::EMAIL);//8
        /*******************************************************************************************************/
    	$resultset = ComrecibidaUsuarioPeer::doSelectStmt($c);    
    	/*******************************************************************************************************/    
    	$cuerpo_emails  = array();
    	$emails_destinos = array();	
    	while($object = $resultset->fetch()){	
    	    $email_destino = $object[8];		
            $cuerpo = '
            <html>
            <head>
            <title></title>
            </head>
            <body>
            <div id="cotenedor">        
            Este es un mensaje para informarle que tiene una Comunicacion Externa Recibida con Accion Legal que se encuentra proxima a vencer su numero de radicado es: 
            <br/>        
            Numero De Radicado: <strong>'.$object[0].'</strong><br/>
            Fecha Vencimiento Legal: <strong>'.$object[1].'</strong><br/>
            Fecha Radicacion: <strong>'.$object[2].'</strong><br/>    
            Remitente: <strong>'.utf8_encode($object[3]).'</strong><br/>
            Asunto: <strong>'.utf8_decode($object[4]).'</strong><br/>
            Detalle Del Asunto: <strong>'.utf8_decode($object[5]).'</strong><br/>
            Tipo De Comunicaci&oacute;n: <strong>'.utf8_decode($object[6]).'</strong><br/>
            Observaciones: <strong>'.utf8_decode($object[7]).'</strong><br/>
            <br/>
            </div>
            </body></html>';
            $cabeceras = "Content-type: text/html\r\n";
        	@mail($email_destino,"CAD :Comunicacion Recibida Con Accion Legal Por Vencida" ,$cuerpo,$cabeceras);
            $cuerpo_emails[]  = $cuerpo;
            $emails_destinos[] = $email_destino;
            $this->writetolog("Externa Recibida Alerta enviada datos: ".$cuerpo);        				
    	}	
    	//$this->datos = $cuerpo_emails;
    	//$this->cuenta = $emails_destinos;        								
    	/******************************************************************************************************/
    }
    
    /**
    * send_alert::sendAlertsWorflows()
    * funcion que envia alertas a los workflows
    * pendientes por ejecutar 
    * @return
    */
    public function sendAlertsWorflows()
    {    
        //$this->writetolog("Workflows Alerta enviada datos: prueba jajajaja");
        /******************************************************************************************************/
        $wf_interface = new wf_Interface();        
        /******************************************************************************************************/
        $c = new Criteria();
        $c->add(WfInstanciaPeer::ESTA_ABIERTA,0);
        $wf_intancias = WfInstanciaPeer::doSelect($c);
        //******************************************************************************************************      	
        foreach($wf_intancias as $wf_intancia)
        {
            $modulo = "";
            $radicado = "";
            $asunto = "";
            $fecha_vencimiento = "";
            /*************************************************************************************************/
            if($wf_intancia->getComrecibidaId())
            {
                $modulo = "Comunicaciones Externas Recibidas";
                $radicado = $wf_intancia->getComRecibida()->getRadicado();
                $asunto = $wf_intancia->getComRecibida()->getAsunto();
                $fecha_vencimiento = $wf_intancia->getComRecibida()->getFechaMaximaRespuesta("Y-m-d H:i:s");
            }
            /*************************************************************************************************/
            if($wf_intancia->getCominternaId())
            {
                $modulo = "Comunicaciones Internas";
                $radicado = $wf_intancia->getComInterna()->getRadicado();
                $asunto = $wf_intancia->getComInterna()->getReferencia();
                $fecha_vencimiento = $wf_intancia->getComInterna()->getFechaMaximaRespuesta("Y-m-d H:i:s");
            }
            /*************************************************************************************************/
            $email_usuarios = $wf_interface->nextActividadAlertaEmail($wf_intancia->getWfinstanciaId());        
            $arr_emails = preg_split("/[,]+/",$email_usuarios,-1,PREG_SPLIT_NO_EMPTY);
            /*************************************************************************************************/
        	$cuerpo_emails  = array();
        	$emails_destinos = array();
        	foreach($arr_emails as $email_destino)
            {    	    
                $cuerpo = '
                <html>
                <head>
                <title></title>
                </head>
                <body>
                <div id="cotenedor">        
                Este es un mensaje para informarle que tiene actividades de Workflow Pendientes por ejecutar: 
                <br/>
                Modulo: <strong>'.$modulo.'</strong><br/>
                Numero De Radicado: <strong>'.$radicado.'</strong><br/>
                Asunto: <strong>'.$asunto.'</strong><br/>
                Fecha Vencimiento: <strong>'.$fecha_vencimiento.'</strong><br/>                
                <br/>
                </div>
                </body></html>';
                $cabeceras = "Content-type: text/html\r\n";
            	  @mail($email_destino,"CAD :Workflow Pendiente Por Ejecutar " ,$cuerpo,$cabeceras);
                $cuerpo_emails[]  = $cuerpo;
                $emails_destinos[] = $email_destino;
                $this->writetolog("Workflows Alerta enviada datos: ".$cuerpo);				
        	}
        }       	
    	//$this->datos = $cuerpo_emails;
    	//$this->cuenta = $emails_destinos;
        /*****************************************************************************************************/    
    }
}

/*$f=fopen(sfConfig::get("sf_log_dir")."\\"."log.txt","w");
fwrite($f,"jajajajajaj");
fclose($f);*/
       
$simad_alertas = new simadAlerta();
if($argv[1]=="-alerta_comrecibida")
{
   $simad_alertas->sendAlertsComRecibida();   
}

if($argv[1]=="-alerta_workflows")
{
   $simad_alertas->sendAlertsWorflows();   
}

?>