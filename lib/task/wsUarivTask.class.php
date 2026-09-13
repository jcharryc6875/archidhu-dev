<?php

class wsUarivTask extends sfBaseTask
{
	const SERVICE_URL = "http://serviciosunidad.unidadvictimas.gov.co/Fachadav2022/FachadaGD.svc?wsdl";
	const SERVICE_URI = "http://serviciosunidad.unidadvictimas.gov.co/Fachadav2022/";
	const SERVICE_USER = "usrGestorUARIV";
	const SERVICE_PASSW = "csgduplaialv16";
	const SERVICE_APPUID = 0;

  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      // add your own options here
    ));

    $this->namespace        = 'uariv';
    $this->name             = 'enviarMarcados';
    $this->briefDescription = 'Hola mundo de las tareas';
    $this->detailedDescription = "Hola mundo de las tareas";
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
	//****************************************************************************************
    // add your code here
    $log_dir = sfConfig::get('sf_log_dir').DIRECTORY_SEPARATOR.date("YmdGis").'_ucodigolexcli.log';
    $marcausuario_id = 1;
    $dependencia_id = 30;
    //****************************************************************************************
    // add your code here
    $this->log('Hora Incio '.date("Y-m-d G:i:s"));
    //****************************************************************************************
    $c1 = new Criteria();
	$c1->setDistinct();
    $c1->add(ComRecibidaPeer::DEPENDENCIA_ID,$dependencia_id);
    $c1->add(ComRecibidaPeer::RESPTA_INTEGRACION,null,Criteria::ISNULL);
	$c1->add(ComRecibidaPeer::FECHA_CREACION,date('Y-m-d').' 00:00:00',Criteria::LESS_THAN);
    //****************************************************************************************
    $c1->clearSelectColumns();
    $c1->addSelectColumn(ComRecibidaPeer::COMRECIBIDA_ID);
    $c1->addSelectColumn(ComRecibidaPeer::RADICADO);
    $c1->addSelectColumn(ComRecibidaPeer::RESPTA_INTEGRACION);
    //****************************************************************************************
    $result_stmt  = ComRecibidaPeer::doSelectStmt($c1);
    $objects_com = $result_stmt->fetchAll();
    simad_util::writetolog($log_dir,'Total registros => '.count($objects_com));
    $this->log('Total registros => '.count($objects_com));
    $conexion = Propel::getConnection();
	$index = 1;
    //****************************************************************************************
    foreach ($objects_com as $com_recibida) {
      $this->log(($index++).'. Procesando Radicado => '.$com_recibida['RADICADO']);
      //**************************************************************************************
      $c2 = new Criteria();
      $c2->add(WebserviceLogPeer::TIPO_OPERACION,'%'.$com_recibida['RADICADO'],Criteria::LIKE);
      $c2->add(WebserviceLogPeer::NOMBRE_METODO,'InformacionRadicadoEntrada');
      $c2->add(WebserviceLogPeer::MENSAJE,'Enviado Exitoso%',Criteria::LIKE);
	  $c2->add(WebserviceLogPeer::RESPONSE_INFO,'',Criteria::NOT_EQUAL);
	  $c2->addAscendingOrderByColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
      //**************************************************************************************
      $c2->setLimit(1);
      $c2->clearSelectColumns();
      $c2->addSelectColumn(WebserviceLogPeer::WEBSERVICELOG_ID);
      $c2->addSelectColumn(WebserviceLogPeer::MENSAJE);
      //**************************************************************************************
      $ws_log = WebserviceLogPeer::doSelectStmt($c2);
      $list_objects = $ws_log->fetchAll();
      //**************************************************************************************
      $enviarRespExt = true;
      if(count($list_objects)){
        foreach ($list_objects as $item) {
          $isNotError = strpos($item['MENSAJE'], 'Enviado Exitoso');
          if ($isNotError !== false){
            $codigo_envio = str_replace("Enviado Exitoso => ","",$item['MENSAJE']);
            simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' ya fue enviado ha LEX Codigo => '.$codigo_envio);
            if(empty($com_recibida['RESPTA_INTEGRACION'])){
              $query = "UPDATE %s SET %s = 0, %s = '".$codigo_envio."' WHERE %s = ".$com_recibida['COMRECIBIDA_ID'];
              $runsql = sprintf($query, ComRecibidaPeer::TABLE_NAME, ComRecibidaPeer::MARCA,ComRecibidaPeer::RESPTA_INTEGRACION,ComRecibidaPeer::COMRECIBIDA_ID);
              $sentencia = $conexion->prepare($runsql);
              $sentencia->execute();
            }
            //********************************************************************************
            $enviarRespExt = false;
          }
        }
      }else{
        simad_util::writetolog($log_dir,'Radicado '.trim($com_recibida['RADICADO']).' no se ha enviado a integración');
      }
    }
	//****************************************************************************************
    $this->log('Hora Fin '.date("Y-m-d G:i:s"));
  }
}
