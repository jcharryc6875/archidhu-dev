<?php

/**
 * @alfonso.ayala 
 * @copyright 2008
 */


/*
 *
 WF Interface
 $HOME/lib/workflow/wf_interface.php 

 Interfaz de funciones de wf.



 insertInstanciaNueva( $tarea_id, $usuario_id, $buzon, $consecutivo )

 ejecuta_Paso_Especifico($instancia_id, $usuario_id,$usuario_destino,
$paso_especifico)

 ejecuta_Paso($instancia_id, $usuario_id,$usuario_destino, $paso_especifico)


 getLista_Transiciones_Desc($instancia_id)

 getLista_Transiciones($instancia_id)

 getLista_Pasos($instancia_id)

 *
 */




/*uso de la creacion del wf */

//   /* wf begin */
//       $nudebug=1;
//       return;
//       //definicion
//   $myIfz = new wf_Interface();
   //crea una nueva instancia de un flujo (conjunto de pasos)
   //insertInstancia($flujo_id, $usuario_id, $buzon, $buzon_id){
//   $instancia_id =
//$myIfz->insertInstancia(
//2,  //flujo_id
//1,  //usuario_id
//1,   //buzon
//7);  //buzon_id
//   if($nudebug)
//       echo ">>>>>>>>" . $instancia_id ."<<<<<<<<<";
//       /* wf end */


/* uso del avance de paso */
//       ///* wf begin */
//       $nudebug=1;
//       $instancia_id=16;
//       $usuario_id=1;
//       $usuario_destino=1;
//       $observaciones="Todo OK";
//       //definicion/
//   $myIfz = new wf_Interface();
//   //closeCurrentActividad($instancia_id, $usuario_destino, $observaciones ){
//   $result=$myIfz->closecurrentActividad($instancia_id,$usuario_destino,
//    $observaciones);
//   //crea una nueva instancia de una tarea (conjunto de pasos)
//
//   if($nudebug)
//       echo ">>>>>>>>" . $instancia_id . " ". $result ."<<<<<<<<<";
//       /* wf end */




class wf_Interface{

var $logfile;
var $nulog;
var $nudebug;
var $instance;

public function wf_Interface(){
      $this->logfile="wf.log";
      $this->nulog=1;
      $this->nudebug=0;
}

public function setInterface(){
      $this->logfile="wf.log";
      $this->nulog=1;
      $this->nudebug=0;
}


public function writetolog($msg){
   if($this->nulog){
         $f=fopen($this->logfile,"a");
      if($f){
              fprintf($f,"%s|%s\n",date("Y-m-d G:i:s"),$msg);
      }
      fclose($f);
          if($this->nudebug){
              echo "<pre>".$msg."</pre>";
          }
       }
}

public function isOk($flujo_id)
{
  $max=0;         
  $conexion = Propel::getConnection();
  $consulta = "SELECT COUNT(*) AS max FROM %s  WHERE %s = %s";
  $sql = sprintf($consulta,WfActividadTransicionPeer::TABLE_NAME,WfActividadTransicionPeer::WF_FLUJO_ID, $flujo_id);
  //************************************************************************************
  $sentencia = $conexion->prepare($sql);
  $sentencia->execute();
  $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
  $max = ($resultset->max);
  //************************************************************************************	  
  $firstActividad = $this->findfirstActividad($flujo_id);
  if($max > 1 && $firstActividad > 0 )
     return true;
   else
     return false;
}


public function isFlujoIdOk( $tipocom_id , $buzon)
{
	  $max=0;	  
      //encontrar el maximo consecutivo de las instancias
      $conexion = Propel::getConnection();
      $consulta = "SELECT COUNT(*) AS max FROM %s  WHERE %s = %s AND %s = %s ";
      
      $buzon = $buzon=="recibida" ? 1 : $buzon ;
      $buzon = $buzon=="interna"  ? 2 : $buzon ;
      $buzon = $buzon=="enviada"  ? 3 : $buzon ;
      
      $this->writetolog("buzon=".$buzon);
      
      if($buzon==1 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMRECIBIDA_ID, 
	   $tipocom_id,
	   WfFlujoPeer::WF_BUZON_ID, 
	   $buzon
	   );
	   }

      if($buzon==2 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMINTERNA_ID, 
	   $tipocom_id,
	   WfFlujoPeer::WF_BUZON_ID, 
	   $buzon
	   );
	   }	   
       

      if($buzon==3 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMENVIADA_ID, 
	   $tipocom_id,
	   WfFlujoPeer::WF_BUZON_ID, 
	   $buzon
	   );
	   }	      
	       
      $this->writetolog($consulta);
      //************************************************************************************
      $sentencia = $conexion->prepare($consulta);
      $sentencia->execute();
      $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
      $max = ($resultset->max);
      //************************************************************************************
	  if($max>=1)
         return true;
      else
         return false;
}


public function getFlujoId( $tipocom_id , $buzon){
	  $max=0;
	  $this->writetolog("flujo_id=$tipocom_id  buzon=$buzon");
      //encontrar el maximo consecutivo de las instancias
      $conexion = Propel::getConnection();
      $consulta = "SELECT WF_FLUJO_ID AS myvalue FROM %s  WHERE %s = %s ";
      
      $buzon = $buzon=="recibida" ? 1 : $buzon ;
      $buzon = $buzon=="interna"  ? 2 : $buzon ;
      $buzon = $buzon=="enviada"  ? 3 : $buzon ;
      
      
      if($buzon==1 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMRECIBIDA_ID, 
	   $tipocom_id
	   );
	   }

      if($buzon==2 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMINTERNA_ID, 
	   $tipocom_id
	   );
	   }	   
       

      if($buzon==3 ){
		$consulta = sprintf($consulta,
       WfFlujoPeer::TABLE_NAME,
	   WfFlujoPeer::TIPOCOMENVIADA_ID, 
	   $tipocom_id
	   );
	   }	          
       $this->writetolog($consulta);
       //************************************************************************************
       $sentencia = $conexion->prepare($consulta);
       $sentencia->execute();
       $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
       $max = ($resultset->myvalue);
       //************************************************************************************	   
      if($max>=1)
         return $max;
       else
         return 0;
}


public function insertInstancia($tipocom_id, $usuario_id, $buzon, $buzon_id){
	
	  $flujo_id=$this->getFlujoId($tipocom_id,$buzon);
	  
	  if($flujo_id==0){
	  	$msg="insertInstancia($flujo_id, $usuario_id,$buzon, $buzon_id) flujo 0";
		$this->writetolog($msg);
		return 0;
	  } 
	  if($usuario_id==0){
	  	$msg="insertInstancia($flujo_id, $usuario_id,$buzon, $buzon_id) usuario 0";
		$this->writetolog($msg);
		return 0;
	  }
	  if(! $this->isok($flujo_id) ){
	  	$msg="insertInstancia($flujo_id, $usuario_id,$buzon, $buzon_id) flujo NOT OK";
		$this->writetolog($msg);
		return 0;
	  } 
	
      $instancia_id=$this->getNextInstancia();
      $actividad_id=$this->findfirstactividad($flujo_id)       ;
      $msg="insertInstancia($flujo_id, $usuario_id,$buzon, $buzon_id, next=$instancia_id)";
      $this->writetolog($msg);
      //inserta instancia
      $myInstance = new WfInstancia();
      $myInstance->setWFInstanciaId($instancia_id);
      $myInstance->setWFFlujoId($flujo_id);
      $myInstance->setWFActividadId($actividad_id);
      $myInstance->setfechaI(date("Y-m-d G:i:s"));
      $myInstance->setestaabierta(true);
      $myInstance->setusuarioID($usuario_id);
      if($buzon==1){
      	    $objCom =  comRecibidaPeer::retrieveByPk($buzon_id);	    
            if($objCom->getComRecibidaId()>0){
			     $myInstance->setComRecibidaID($buzon_id);
				 $radicado = $objCom->getRadicado();	
			}	
	  }elseif($buzon==2){
	  	    $objCom =  comInternaPeer::retrieveByPk($buzon_id);
            if($objCom->getCominternaId()>0){
			     $myInstance->setComInternaID($buzon_id);
				 $radicado = $objCom->getRadicado();	
			}
	  }
	  /*
	  elseif($buzon==3){
	  	    $objCom =  comEnviadaPeer::retrieveByPk($buzon_id);
            if($objCom->getEnviadaId()>0){
			     $myInstance->setComEnviadaID($buzon_id);
				 $radicado = $objCom->getRadicado();	
			}
	  }
	  */
	  $myInstance->save();
	  
      $this->insertBitacora($instancia_id, $usuario_id, $flujo_id );
      $msg="instancia_id [$instancia_id] Creada. ";
      $this->writetolog($msg);
      //$tipocominterna_id=1;
      //$referencia="Instancia WF: $instancia_id  " ;
      //$contenido="La tarea $instancia_id con Radicado $radicado ha sido asignada a usted.";
      //$myinterna = new wfInterna($usuario_id, $usuario_id, $tipocominterna_id, $referencia, $contenido);
      //$this->writetolog("interna insertada.");
      return $instancia_id;
}


public function getNextInstancia(){
      $max=0;
          //encontrar el maximo consecutivo de las instancias
      $conexion = Propel::getConnection();
      $consulta = "SELECT MAX(%s) AS max FROM %s ";
      $sql = sprintf($consulta, WfInstanciaPeer::WFINSTANCIA_ID,WfInstanciaPeer::TABLE_NAME);
      //************************************************************************************
      $sentencia = $conexion->prepare($sql);
      $sentencia->execute();
      $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
      $max = ($resultset->max + 1);
      //************************************************************************************      
	  $this->writetolog("seq Instancia=". $max);
      return $max;
}

public function findFirstActividad($flujo_id)
{       
      $result=0;
      $c=new Criteria();
      $c->add(WfActividadTransicionPeer::WFTIPOACTIVIDAD_ID,1);
      $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
      $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$flujo_id);
      $rs = WfActividadPeer::doSelect($c);
      foreach($rs as $respo){
              $result = $respo->getWFActividadId() ;
      }
      return $result;
}

public function insertBitacora($instancia_id, $usuario_id, $flujo_id=0, $actividad_id=0){
    
	$result=0;
    if($flujo_id == 0){
        $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
        $flujo_id=$myInstancia->getWFFlujoId();
    }
    $actividad_actual=0;
    $bitacora_id=$this->getCurrentBitacora($instancia_id);
    
    if($bitacora_id > 0){
               $myBitacora=WfInstanciaBitacoraPeer::retrieveByPk($bitacora_id);
               $actividad_actual=$myBitacora->getWfActividadId();
    }
    $msg="insertBitacora(instancia=$instancia_id, bitacora=[$bitacora_id] actividad_actual=[$actividad_actual]  usuario_id=$usuario_id, flujo_id=$flujo_id, actividad_id=$actividad_id)";
    $this->writetolog($msg);
    
	if($actividad_id==0){
            //debe buscar la siguiente actividad...
            $actividad_id=$this->findNextActividad($instancia_id,$flujo_id);
    }
    //comprobacion de validez de nueva actividad
    $lista_validas=Array();
    $lista_validas=$this->findListNextActividad($instancia_id,$flujo_id);
    
	if( ! in_array($actividad_id, $lista_validas) ){
        $msg="Validacion de Destinos> No es posible para la instancia=[$instancia_id] flujo=[$flujo_id] ir a=[$actividad_id]";
        $this->writetolog($msg);
        $tipo_actividad=1; 
        $myLastActividad=WfActividadPeer::retrieveByPk($actividad_actual);
        if($myLastActividad)
            $tipo_actividad=$this->getTipoActividad($instancia_id);
            $msg="Actividad Actual> Tipo actividad=[$tipo_actividad] actividad_actual=[$actividad_actual] instancia=[$instancia_id]";
            $this->writetolog($msg);
            //la ultima actividad es un cierre
            if($tipo_actividad==3) {
                $this->closeInstancia($instancia_id);
            }
            //return 0;
    }
    if($actividad_id > 0 && ! is_array($actividad_id)){
        
		$msg="Insertando Bitacora> instancia=[$instancia_id] actividad=[$actividad_id]";
        $this->writetolog($msg);
          
        if($tipo_actividad==3){
              
        }
        
        // la actividad es un cierre
        if($tipo_actividad==3){
          // inserta bitacora
          $myBitacora = new WfInstanciaBitacora();
          $myBitacora->setWFInstanciaId($instancia_id);
          $myBitacora->setWFActividadId($actividad_id);
          $myBitacora->setfechaI(date("Y-m-d G:i:s"));
          $myBitacora->setfechaF(date("Y-m-d G:i:s"));
          $myBitacora->setobservaciones("Cerrada.");
          $myBitacora->setEsActual(false);
          $myBitacora->setUsuarioId($usuario_id);
          $result=$myBitacora->getWFInstanciaBitacoraId();
          $myBitacora->save();
          $this->closeInstancia($instancia_id);
	    }else{
           // inserta bitacora
          $myBitacora = new WfInstanciaBitacora();
          $myBitacora->setWFInstanciaId($instancia_id);
          $myBitacora->setWFActividadId($actividad_id);
          $myBitacora->setfechaI(date("Y-m-d G:i:s"));
          $myBitacora->setobservaciones(".");
          $myBitacora->setEsActual(true);
          $myBitacora->setUsuarioId($usuario_id);
          $myBitacora->save();
          $result = $myBitacora->getWFInstanciaBitacoraId();
          $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
          $myInstancia->setWFActividadId($actividad_id);
          $myInstancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
          $myInstancia->save();
          $flujo_id=$myInstancia->getWFFlujoId();
          $msg="Inserta Bitacora> instancia_id=[$instancia_id] Bitacora_id=[$result] insertada.";
          
          
          $this->writetolog($msg);

        }
        return $result;
    }
    return 0;
}

public function getWfFlujoComId($com_id , $buzon){
      $result=0;
      //$this->writetolog("Buscando buzon=$buzon buzon_id=$buzon_id");
      if($buzon==1 ){
    	  $c=new Criteria();
          $c->add(WfInstanciaPeer::COMRECIBIDA_ID, $com_id);
          $rs = WfInstanciaPeer::doSelectOne($c);
          if($rs->getPrimaryKey()){
                  $result = $rs->getWfFlujoId() ;
          }
	  }elseif($buzon==2){
    	  $c=new Criteria();
          $c->add(WfInstanciaPeer::COMINTERNA_ID, $com_id);
          $rs = WfInstanciaPeer::doSelectOne($c);
          if($rs->getPrimaryKey()){
                  $result =$rs->getWfFlujoId() ;
          }	
	  }
	  //$this->writetolog("Encontrado $result");
      return $result;
}

public function getNameWfActividad($actividad_id){
    $descripcion_actividad = "";
    if($actividad_id == 0){
       $descripcion_actividad = "Finalizado"; 
    }else{
     $actividad = WfActividadPeer::retrieveByPK($actividad_id);
     $descripcion_actividad = $actividad->getDescripcion();
    } 
     return $descripcion_actividad;
}

public function getRSTransiciones($instancia_id, $flujo_id=0)
{
    if($flujo_id==0){
      $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
      $flujo_id=$myInstancia->getWFFlujoId();
    }
    $lista_actividades=Array();
    $cuenta=0;
    $result=0;
    $actividad_id=0;
    $c=new Criteria();
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
    $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
    $c->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID,WfTransicionPeer::WF_TRANSICION_ID);
    $c->addJoin(WfInstanciaPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
    $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
    $c->add(WfInstanciaPeer::WF_FLUJO_ID, $flujo_id);
    $rs = WfTransicionPeer::doSelect($c);
    return $rs;
}


public function getRSActividades()
{
    $lista_actividades=Array();
    $cuenta=0;
    $result=0;
    $actividad_id=0;
    $c=new Criteria();
    $c->addAscendingOrderByColumn(WfActividadPeer::DESCRIPCION);
    $rs = WfActividadPeer::doSelect($c);
    return $rs;
}

public function getRSUltimoOrden($flujo_id)
{    
      $max=0;         
      $conexion = Propel::getConnection();
      $consulta = "SELECT MAX(%s) AS max FROM %s WHERE %s = %s";
      $sql      = sprintf($consulta,WfActividadTransicionPeer::ORDEN,WfActividadTransicionPeer::TABLE_NAME,WfActividadTransicionPeer::WF_FLUJO_ID, $flujo_id);
      //************************************************************************************
      $sentencia = $conexion->prepare($sql);
      $sentencia->execute();
      $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
      return ($resultset->max + 1);
      //************************************************************************************
}

public function getRSActividadesPorTransicion( $transicion_id, $flujo_id )
{
    $lista_actividades=Array();
    $cuenta=0;
    $result=0;
    $actividad_id=0;
    $c=new Criteria();
    $c->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$transicion_id);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo_id);
    $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, WfActividadPeer::WF_ACTIVIDAD_ID);
    $rs =   WfActividadTransicionPeer::doSelect($c);
    
   // $this->writetolog("<pre>". var_dump($rs)."<pre>");
    
    return $rs;
}


public function updateActividadTransicion( $flujo_id, $transicion_id, $actividad_inicial, $actividad_final, $estado_inicial, $estado_final, $orden, $actividadtransicion_id ,$requiere_interna=0, $requiere_recibida=0, $requiere_enviada=0)
{
	
	$this->writetolog("$actividadtransicion_id, $flujo_id, $transicion_id, $actividad_inicial, $actividad_final, $estado_inicial, $estado_final, $orden");
	//14, 1, 2, 6, 1, 1, 5,
	$cuenta=0;
	$eradestino=0;
	
	if( $actividadtransicion_id>0 ){
    $c=new Criteria();
    $c->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$transicion_id);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo_id);
    $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, WfActividadPeer::WF_ACTIVIDAD_ID);
    $c->add(WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID, $actividadtransicion_id );
    $rs = WfActividadTransicionPeer::doSelect($c);
    foreach($rs as $respo){
       $at_id = $respo->getWFActividadTransicionId() ;
       $nuevaAT = WfActividadTransicionPeer::retrieveByPk($at_id);
       //$miActividadTransicion->delete();
       //$miActividadTransicion->save();
       if(!  $nuevaAT->getEsDestino()){
	     $nuevaAT->setWfTransicionId($transicion_id);
         $nuevaAT->setWfFlujoId($flujo_id);
         $nuevaAT->setWfActividadId($actividad_inicial);
         $nuevaAT->setWfEstadoId($estado_inicial);
         $nuevaAT->setEsDestino(false);
         $nuevaAT->setRequiereComInterna($requiere_interna);
         $nuevaAT->setRequiereComEnviada($requiere_enviada);
         $nuevaAT->setRequiereComRecibida($requiere_recibida);
         
	     $nuevaAT->setOrden($orden);
	     $nuevaAT->save();
	     $cuenta++;
		 $eradestino=0;	
	     	
	   }else{
	     $nuevaAT->setWfTransicionId($transicion_id);
         $nuevaAT->setWfFlujoId($flujo_id);
         $nuevaAT->setWfActividadId($actividad_final);
         $nuevaAT->setWfEstadoId($estado_final);
         $nuevaAT->setEsDestino(true);
	     $nuevaAT->setRequiereComInterna($requiere_interna);
         $nuevaAT->setRequiereComEnviada($requiere_enviada);
         $nuevaAT->setRequiereComRecibida($requiere_recibida);
         
		 $nuevaAT->setOrden($orden);
	     $nuevaAT->save();
	     $cuenta++;
	     $eradestino=1;	
	   }
    }
    
    }
    
    
    if($cuenta==0){
	$nuevaAT = new WfActividadTransicion();
    $nuevaAT->setWfTransicionId($transicion_id);
    $nuevaAT->setWfFlujoId($flujo_id);
    $nuevaAT->setWfActividadId($actividad_inicial);
    $nuevaAT->setWfEstadoId($estado_inicial);
    $nuevaAT->setEsDestino(false);
	$nuevaAT->setOrden($orden);
	$nuevaAT->setRequiereComInterna($requiere_interna);
    $nuevaAT->setRequiereComEnviada($requiere_enviada);
    $nuevaAT->setRequiereComRecibida($requiere_recibida);
         
	$nuevaAT->save();
	 
    $nuevaAT = new WfActividadTransicion();
    $nuevaAT->setWfTransicionId($transicion_id);
    $nuevaAT->setWfFlujoId($flujo_id);
    $nuevaAT->setWfActividadId($actividad_final);
    $nuevaAT->setWfEstadoId($estado_final);
    $nuevaAT->setEsDestino(true);
	$nuevaAT->setRequiereComInterna($requiere_interna);
    $nuevaAT->setRequiereComEnviada($requiere_enviada);
    $nuevaAT->setRequiereComRecibida($requiere_recibida);
    $nuevaAT->setOrden($orden);
	$nuevaAT->save();	
	}
	/*
	else{
		if($eradestino==0){
           $nuevaAT = new WfActividadTransicion();
           $nuevaAT->setWfTransicionId($transicion_id);
           $nuevaAT->setWfFlujoId($flujo_id);
           $nuevaAT->setWfActividadId($actividad_final);
           $nuevaAT->setWfEstadoId($estado_final);
           $nuevaAT->setEsDestino(true);
           $nuevaAT->setRequiereComInterna($requiere_interna);
           $nuevaAT->setRequiereComEnviada($requiere_enviada);
           $nuevaAT->setRequiereComRecibida($requiere_recibida);
         
	       $nuevaAT->setOrden($orden);
	       $nuevaAT->save();
	    }else{
           $nuevaAT = new WfActividadTransicion();
           $nuevaAT->setWfTransicionId($transicion_id);
           $nuevaAT->setWfFlujoId($flujo_id);
           $nuevaAT->setWfActividadId($actividad_inicial);
           $nuevaAT->setWfEstadoId($estado_inicial);
           $nuevaAT->setRequiereComInterna($requiere_interna);
           $nuevaAT->setRequiereComEnviada($requiere_enviada);
           $nuevaAT->setRequiereComRecibida($requiere_recibida);
         
           $nuevaAT->setEsDestino(false);
	       $nuevaAT->setOrden($orden);
	       $nuevaAT->save();
        }
	}
	*/
	$actividadtransicion_id = $nuevaAT->getWfActividadTransicionId();
   // $this->writetolog("<pre>". var_dump($rs)."<pre>");
    
    return $actividadtransicion_id;
}


public function getNextActividad($instancia_id)
{
    $descripcion_trancision = "";
    $wf_instancia_bitacora = $this->getObjUltimaBitacora($instancia_id);    
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wf_instancia_bitacora->getWfactividadtransicionId());
    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
    $next_actividad = WfActividadTransicionPeer::doSelectOne($c);
    //echo $next_actividad;
  	/***************************************************************************************************/
    if($next_actividad){
        $descripcion_trancision = $next_actividad->getWfTransicion()->getDescripcion();
    }else{
        $descripcion_trancision = $wf_instancia_bitacora->getWfActividadTransicion()->getWfTransicion()->getDescripcion();
    }
    
    return $descripcion_trancision;
}

public function validarNextActividadUsuario($instancia_id,$usuario_id)
{
    $usuario_id_trancision = "";
    $wf_instancia_bitacora = $this->getObjUltimaBitacora($instancia_id);    
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wf_instancia_bitacora->getWfactividadtransicionId());    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
    $next_actividad = WfActividadTransicionPeer::doSelectOne($c);
  	/***************************************************************************************************/
    if($next_actividad){
        $ut = new Criteria();
        $ut->add(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,$next_actividad->getWfactividadtransicionId());
        $ut->add(WfActividadtransicionUsuarioPeer::USUARIO_ID,$usuario_id);
        $usuario_id_trancision = WfActividadtransicionUsuarioPeer::doCount($ut);
    }else{
        $usuario_id_trancision = 0;
    }
    
    return $usuario_id_trancision;
}

public function nextActividadAlertaEmail($instancia_id)
{
    $email_usuario_trancision = "";
    $wf_instancia_bitacora = $this->getObjUltimaBitacora($instancia_id);    
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wf_instancia_bitacora->getWfactividadtransicionId());
    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
    $next_actividad = WfActividadTransicionPeer::doSelectOne($c);    
  	/***************************************************************************************************/
    if($next_actividad){
        $ut = new Criteria();
        $ut->add(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,$next_actividad->getWfactividadtransicionId());
        //$ut->add(WfActividadtransicionUsuarioPeer::USUARIO_ID,$usuario_id);
        $usuarios_trancision = WfActividadtransicionUsuarioPeer::doSelect($ut);        
        foreach ($usuarios_trancision as $usuario_trancision)
        {
            $email_usuario = $usuario_trancision->getUsuario()->getEmail();
            if(trim($email_usuario) != "")
            {
               $email_usuario_trancision .=  $usuario_trancision->getUsuario()->getEmail().",";
            }
        }
    }else{
        $email_usuario_trancision = "";
    }
    
    return $email_usuario_trancision;
}

public function getNextActividadUsuarios($instancia_id,$flag_userId=0)
{
    $nomb_usuario_trancision = "";
    $wf_instancia_bitacora = $this->getObjUltimaBitacora($instancia_id);    
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wf_instancia_bitacora->getWfactividadtransicionId());
    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
    $next_actividad = WfActividadTransicionPeer::doSelectOne($c);
    //echo $next_actividad;
  	/***************************************************************************************************/
    if($next_actividad){
        $ut = new Criteria();
        $ut->add(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,$next_actividad->getWfactividadtransicionId());        
        $usuarios_trancision = WfActividadtransicionUsuarioPeer::doSelect($ut);
        foreach($usuarios_trancision as $usuario_trancision){
            if($flag_userId == 0){
               $nomb_usuario_trancision .= $usuario_trancision->getUsuario().',';
            }else{
               $nomb_usuario_trancision .= $usuario_trancision->getUsuarioId().','; 
            }
        }        
    }else{
        $nomb_usuario_trancision = "";
    }
    
    return $nomb_usuario_trancision;
}

public function getObjNextActividadUsuarios($actividadtrancision_id,$flag_userId=0)
{
    $nomb_usuario_trancision = "";        
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($actividadtrancision_id);    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() + 1 );    
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->addJoin(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
	$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
    $next_actividad_usuarios = WfActividadtransicionUsuarioPeer::doSelectJoinAllExceptWfActividadTransicion($c);    
  	/***************************************************************************************************/
    return $next_actividad_usuarios;
}

public function getObjCurrentActividadUsuarios($actividadtrancision_id,$flag_userId=0)
{
    $nomb_usuario_trancision = "";        
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($actividadtrancision_id);
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->addJoin(WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);
    $c->add(WfActividadTransicionPeer::ORDEN,$wf_actividad_transicion->getOrden());
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
    $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wf_actividad_transicion->getWfActividadId());
	$c->addAscendingOrderByColumn(UsuarioPeer::NOMBRE);
    $next_actividad_usuarios = WfActividadtransicionUsuarioPeer::doSelectJoinAllExceptWfActividadTransicion($c);    
  	/***************************************************************************************************/
    return $next_actividad_usuarios;
}

public function getObjLastIdByActividadTransicionDestino($wfactividadtrancision_id)
{
    $wf_actividad_transicion = WfActividadTransicionPeer::retrieveByPk($wfactividadtrancision_id);    
    $nuevo_orden = ($wf_actividad_transicion->getOrden() - 1);
    $wfconsecutivo_id = 0;
    //*******************************************************************************
    $wfpr = new Criteria();
    $wfpr->add(WfActividadTransicionPeer::ORDEN,$wf_actividad_transicion->getOrden());
	$wfpr->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
	$wfpr->add(WfActividadTransicionPeer::WF_TRANSICION_ID,$wf_actividad_transicion->getWfTransicionId());
	$wfpr->add(WfActividadTransicionPeer::ES_DESTINO,false);
    $wfpr->addAscendingOrderByColumn(WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);
    $wfactividad_origen = WfActividadTransicionPeer::doSelectOne($wfpr);
    //*******************************************************************************
    if(!$wfactividad_origen->getPrimaryKey()){
        return $wfconsecutivo_id;
    }
    //*******************************************************************************
    $c = new Criteria();
    $c->add(WfActividadTransicionPeer::ORDEN,$nuevo_orden);
	$c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wf_actividad_transicion->getWfFlujoId());
	$c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,$wfactividad_origen->getWfActividadId());
	$c->add(WfActividadTransicionPeer::ES_DESTINO,true);
    $last_actividad_transicion = WfActividadTransicionPeer::doSelectOne($c);
  	//********************************************************************************
	if($last_actividad_transicion){
		return $last_actividad_transicion->getPrimaryKey();
	}else{
		return 0;
	}
}

public function getObjWfBitacoraVariables($wfflujo_id,$flag_userId=0)
{        
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->addJoin(WfVariablePeer::WFACTIVIDADTRANSICION_ID,WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID);    
    $c->add(WfActividadTransicionPeer::WF_FLUJO_ID,$wfflujo_id);    
    $bitacora_variables = WfVariablePeer::doSelectJoinAll($c);    
  	/***************************************************************************************************/
    return $bitacora_variables;
}

public function getValueWfVariable($wfvariable_id,$wfinstanciabitacora_id)
{        
    /******************************Consulta para la Siguiente Actividad**********************************/
    $c = new Criteria();
    $c->addJoin(WfVariablePeer::WFVARIABLE_ID,WfBitacoraVariablePeer::WFVARIABLE_ID);
    $c->add(WfBitacoraVariablePeer::WFVARIABLE_ID,$wfvariable_id);
    $c->add(WfBitacoraVariablePeer::WFINSTANCIABITACORA_ID,$wfinstanciabitacora_id);
    $value_variables = WfVariablePeer::doSelectOne($c);    
  	/***************************************************************************************************/
    return $value_variables;
}

public function findNextActividad($instancia_id, $flujo_id=0,$flag_fist=1)
{
       /*
--actividad siguiente
select tt.* 
from
dbo.wf_actividad_transicion t,
dbo.wf_transicion tt,
dbo.wf_actividad a,
dbo.wf_instancia i
where
t.es_destino=0
and t.wf_flujo_id      =i.wf_flujo_id
and t.wf_actividad_id  =a.wf_actividad_id
and t.wf_transicion_id =tt.wf_transicion_id
and i.wf_actividad_id  =a.wf_actividad_id
and i.wfinstancia_id   =5
       */
   if($flujo_id==0){
      $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
      $flujo_id=$myInstancia->getWFFlujoId();
   }
   $lista_actividades=Array();
   $cuenta=0;

   $msg="findNextActividad($instancia_id, $flujo_id)";
   $this->writetolog($msg);

   $result=0;
   $actividad_id=0;
   $c=new Criteria();
   $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
   $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
   $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
   $c->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID,WfTransicionPeer::WF_TRANSICION_ID);
   //$c->addJoin(WfInstanciaPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
   $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
   $rs = WfTransicionPeer::doSelect($c);
   foreach($rs as $respo){
       $result = $respo->getWFTransicionId() ;
   }
   $msg="Buscando Instancia> Instancia=[$instancia_id] Transicion=[$result ] Encontrada";
   $this->writetolog($msg);
   if($result>0){
            //es una transicion valida. se retorna la siguiente actividad.
            $cantidad_bitacoras=$this->countBitacora($instancia_id);
            if($cantidad_bitacoras > 0){
                $c2=new Criteria()     ;
                $c2->add(WfActividadTransicionPeer::WF_TRANSICION_ID, $result);
                $c2->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID, WfTransicionPeer::WF_TRANSICION_ID);
                $c2->add(WfActividadTransicionPeer::ES_DESTINO,true);
                $c2->add(WfActividadTransicionPeer::WF_FLUJO_ID,$flujo_id );
                $rs2= WfActividadTransicionPeer::doSelect($c2);
                $cuenta=0;
                foreach($rs2 as $respo2){
                     $cuenta++;
                     $actividad_id=$respo2->getWFActividadId()        ;
                     $lista_actividades[]=$actividad_id       ;
                     $this->writetolog("Buscando Actividad Destino> instancia=[$instancia_id] actividad_id=[$actividad_id]");
                }
            }else{
                 if($flag_fist == 1){
                   $actividad_id=$this->findFirstActividad($flujo_id);                 
                   $this->writetolog("Buscando Primera Actividad> instancia=[$instancia_id] Se encuentra la primera actividad=[$actividad_id]");
                 }elseif($flag_fist == 0){
                   $actividad_id = 0;
                 }
            }
   }
   
   $this->writetolog("Buscando Instancia> RESULTADO> Instancia=[$instancia_id]  next actividad=[$actividad_id] cuenta= $cuenta");
   if($cuenta>1){
       return $lista_actividades;
   }else{
       return $actividad_id;
   }
}


public function findListNextActividad($instancia_id, $flujo_id=0){
       /*
--actividad siguiente
select tt.* from
dbo.wf_actividad_transicion t,
dbo.wf_transicion tt,
dbo.wf_actividad a,
dbo.wf_instancia i
where
t.es_destino=0
and t.wf_flujo_id      =i.wf_flujo_id
and t.wf_actividad_id  =a.wf_actividad_id
and t.wf_transicion_id =tt.wf_transicion_id
and i.wf_actividad_id  =a.wf_actividad_id
and i.wfinstancia_id   =5
       */
      if($flujo_id==0){
          $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
          $flujo_id=$myInstancia->getWFFlujoId();
      }
      $lista_actividades=Array();
      $cuenta=0;

      $msg="findListNextActividad($instancia_id, $flujo_id)";
      $this->writetolog($msg);
      
	  $result=0;
      $actividad_id=0;
      $c=new Criteria();
      $c->add(WfActividadTransicionPeer::ES_DESTINO,false);
      $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
      $c->addJoin(WfActividadTransicionPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
      $c->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID,WfTransicionPeer::WF_TRANSICION_ID);
      $c->addJoin(WfInstanciaPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
      $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
      $rs = WfTransicionPeer::doSelect($c);
      foreach($rs as $respo){
              $result = $respo->getWFTransicionId() ;
      }
      $msg="Buscando Transicion> Instancia=[$instancia_id] Transicion=[$result ]";
      $this->writetolog($msg);

      if($result>0)      {
          //es una transicion valida. se retorna la siguiente actividad.
          $cantidad_bitacoras=$this->countBitacora($instancia_id);
          if($cantidad_bitacoras > 0){
                    $c2=new Criteria()     ;
                    $c2->add(WfActividadTransicionPeer::WF_TRANSICION_ID, $result);
                    $c2->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID, WfTransicionPeer::WF_TRANSICION_ID);
                    $c2->add(WfActividadTransicionPeer::ES_DESTINO,true);
                    $c2->add(WfActividadTransicionPeer::WF_FLUJO_ID,$flujo_id );
                    $rs2= WfActividadTransicionPeer::doSelect($c2);
                    $cuenta=0;
                    foreach($rs2 as $respo2){
                          $cuenta++;
                          $actividad_id=$respo2->getWFActividadId()        ;
                          $lista_actividades[]=$actividad_id       ;
                          $this->writetolog("Buscando Destinos>  instancia=[$instancia_id] actividad_id=[$actividad_id]");
                        }
            }else{
                 $actividad_id=$this->findFirstActividad($flujo_id);
                 $this->writetolog("Buscando Primera Actividad> instancia=[$instancia_id] primera actividad=[$actividad_id]")       ;
            }
          }
          if($this->nudebug)
              var_dump($lista_actividades);

      $this->writetolog("findListNextActividad> RESULTADO> instancia=$instancia_id next actividad=[$actividad_id] cuenta=$cuenta");
      return $lista_actividades;
}

public function countBitacora( $instancia_id )
{
    $resultado = 0;
    $conexion = Propel::getConnection();
    $consulta = "SELECT COUNT(%s) AS resultado FROM %s WHERE %s=%s";
    $sql      = sprintf($consulta,WfInstanciaBitacoraPeer::WFINSTANCIABITACORA_ID,WfInstanciaBitacoraPeer::TABLE_NAME,WfInstanciaBitacoraPeer::WFINSTANCIA_ID, $instancia_id);
    //************************************************************************************
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
    return ($resultset->resultado);
    //************************************************************************************
}


public function closeCurrentActividad($instancia_id, $usuario_destino, $observaciones, $transicion_id=0 ){
   $msg="closeCurrentActividad> instancia=$instancia_id usuario=$usuario_destino obs=$observaciones transicion=$transicion_id";
   $this->writetolog($msg);
   $bitacora_id=$this->getCurrentBitacora($instancia_id);
   
   
   if($instancia_id > 0){
        $miInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
        $flujo_id    = $miInstancia->getWFFlujoID();
	   	$esta_abierta= $miInstancia->getEstaAbierta();
	   	
	   	if(! $esta_abierta){
	   		$this->writetolog("closeCurrentActividad> RESULTADO> Instancia $instancia_id ya esta cerrada.");
			return;
		}	
   }
   
   
   
/*
select  a.wf_actividad_id

from wf_actividad_transicion a,
     wf_transicion t
where t.wf_transicion_id=a.wf_transicion_id
      and a.es_destino=1
      and a.wf_transicion_id=2
      and a.wf_flujo_id=2
*/

   if($transicion_id > 0){
       $actividad_id=0;
       $c2=new Criteria()       ;
       $c2->add(WfActividadTransicionPeer::WF_TRANSICION_ID, $transicion_id);
       $c2->addJoin(WfActividadTransicionPeer::WF_TRANSICION_ID,WfTransicionPeer::WF_TRANSICION_ID);
       $c2->add(WfActividadTransicionPeer::ES_DESTINO, true);
       $c2->add(WfActividadTransicionPeer::WF_FLUJO_ID, $flujo_id);
       $rs= WfActividadTransicionPeer::doSelect($c2);
       foreach($rs as $respo){
           $actividad_id=$respo->getWfActividadId() ;
       }
   }
   $msg="Buscando Actividad> actividad=" .$actividad_id ;
   $this->writetolog($msg);
   
   if($bitacora_id > 0){
      $myBitacora = WfInstanciaBitacoraPeer::retrieveByPk($bitacora_id);
      $usuario_id=$myBitacora->getUsuarioId();
      
      $myBitacora->setFechaF(date("Y-m-d G:i:s"));
      $myBitacora->setObservaciones($observaciones)       ;
      $myBitacora->setEsActual(false)              ;
      $myBitacora->save();
      
      
      $tipo_actividad=$this->getTipoActividad($instancia_id);
      
      $myActividad    = WfActividadPeer::retrieveByPk($actividad_id);
        
        if( $myActividad->getWfActividadId()>0 ){
		
			$myScript_id=0;
			if($myScript_id>0){
				switch($myScript_id){
					case 1:
					 //inserta una correspondencia interna nueva...
	                 $tipocominterna_id=1;
                     $referencia="Instancia WF: $instancia_id  " ;
                     $contenido="La tarea $instancia_id con Radicado $radicado ha sido asignada a usted.";
                     $myinterna = new wfInterna($usuario_id, $usuario_destino, $tipocominterna_id, $referencia, $contenido);
      	             $this->writetolog("Comunicacion interna creada: $contenido");			     
					     
					     
					break;
					
					case 2:
					//responde a una una correspondencia interna...
	                 $tipocominterna_id=1;
                     $referencia="Instancia WF: $instancia_id  " ;
                     $contenido="La tarea $instancia_id con Radicado $radicado ha sido Resuelta.";
                     $myinterna = new wfInterna($usuario_id, $usuario_destino, $tipocominterna_id, $referencia, $contenido);
      				 $this->writetolog("Comunicacion interna creada: $contenido");
					
					
					break;
					
					
					
					default:
					    $this->writetolog("Script NO encontrado.");
					
					
				}
				
				
				
			}
			
			
		}
      
      
      
      
      $msg="cerrando bitacora> instancia=$instancia_id bitacora_id=[$bitacora_id] tipoactividad=$tipo_actividad";
      //$this->writetolog($msg);
      
      if($tipo_actividad == 3){
	      $this->cierraInstancia($instancia_id);
	  }else{
	      //inserta siguiente bitacora
          $this->insertBitacora($instancia_id, $usuario_destino);	
	  }
      
    }else{
       $msg="Busca Actividad> NO se encontro actividad actual para instancia [$instancia_id]";
       $this->writetolog($msg);
       //insertar primera actividad...
       $cantidad_bitacoras=$this->countBitacora($instancia_id)                         ;
       if($cantidad_bitacoras==0){
           $this->insertBitacora($instancia_id, $usuario_destino);
       }
    }
}



public function getCurrentBitacora($instancia_id){
       $bitacora_id=0;
       $msg="Buscando bitacora actual> instancia=[$instancia_id]";
       $this->writetolog($msg);
            
       $c=new Criteria()       ;
       $c->add(WfInstanciaBitacoraPeer::WFINSTANCIA_ID, $instancia_id);
       $c->add(WfInstanciaBitacoraPeer::ES_ACTUAL, true);
       $rs= WfInstanciaBitacoraPeer::doSelect($c);
       foreach($rs as $respo){
               $bitacora_id=$respo->getWFInstanciaBitacoraId() ;
       }
       if($bitacora_id==0){
           $bitacora_id=$this->getMaxBitacora($instancia_id);
           if($bitacora_id==0){
               $msg="Bitacora actual NO encontrada para instancia [$instancia_id]";
               $this->writetolog($msg);
           }
       }
       $msg="Buscando bitacora actual> RESULTADO> instancia=[$instancia_id] bitacora=$bitacora_id";
       $this->writetolog($msg);
       return $bitacora_id;
}

public function insertInstanciaNueva( $tarea_id, $usuario_id, $buzon, $consecutivo )
{
    //encontrar el maximo consecutivo de las instancias
    $conexion = Propel::getConnection();
    $consulta = "SELECT MAX(%s)+1 AS max FROM %s ";
    $sql      = sprintf($consulta, WfInstanciaPeer::INSTANCIA_ID, WfInstanciaPeer::TABLE_NAME);
    //************************************************************************************
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
    $max = ($resultset->max);
    //************************************************************************************		
    $instancia_id=$max;
    //inserta instancia
    $myInstance = new WfInstancia();
    $myInstance->setInstanciaId($max);
    $myInstance->settareaId($tarea_id);
    $myInstance->setfechaInicio(date("Y-m-d G:i:s"));
    $myInstance->setpasoActual(1);
    $myInstance->setusuarioActual($usuario_id);
    $myInstance->Setbuzon($buzon);
    $myInstance->setbuzonid($consecutivo);      
    $myInstance->setobservaciones(".");
    $myInstance->setestadoinstancia(1);
    $myInstance->save();      
    $this->insertPrimerPaso($instancia_id, $tarea_id, $usuario_id );      
    return $instancia_id;
}



public function getInstanciaId($buzon, $buzon_id ){
      $result=0;
      //$this->writetolog("Buscando buzon=$buzon buzon_id=$buzon_id");
      if($buzon==1 ){
	  $c=new Criteria();
      $c->add(WfInstanciaPeer::COMRECIBIDA_ID, $buzon_id);
      $rs = WfInstanciaPeer::doSelect($c);
      foreach($rs as $respo){
              $result =$respo->getWfInstanciaId() ;
      }
	  }elseif($buzon==2){
	  $c=new Criteria();
      $c->add(WfInstanciaPeer::COMINTERNA_ID, $buzon_id);
      $rs = WfInstanciaPeer::doSelect($c);
      foreach($rs as $respo){
              $result =$respo->getWfinstanciaId() ;
      }	
	  }
	  //$this->writetolog("Encontrado $result");
      return $result;
}




public function getInstanciaPorDocumentoRelacionado($buzon, $buzon_id ){
      $result=$this->getInstanciaId($buzon, $buzon_id);      
	  return $result;
}


public function getDocumentoRelacionado($myinstancia ){
      $result=0;
      $cominterna_id=0;
      $comrecibida_id=0;
      
      $objInstancia = WfInstanciaPeer::retrieveByPk($myinstancia);
      if( $objInstancia->getWfInstanciaId()>0 ){
	      $comrecibida_id=$objInstancia->getComrecibidaId();
	      $cominterna_id =$objInstancia->getComInternaId();
	  }
	  if($comrecibida_id>0){
	      $result=$comrecibida_id;	
	  }
	  if($cominterna_id>0){
	      $result=$cominterna_id;	
	  }
      return $result;
}



public function setRelacion($buzon, $buzon_id, $instancia_id){
      $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
      $myInstancia->setBuzon($buzon);
      $myInstancia->setBuzonid($buzon_id);
      $myInstancia->save();
}

public function getMaxBitacora($instancia_id)
{
	  $bitacora_id=0;
      $conexion = Propel::getConnection();
      $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$instancia_id ;
      $sql      = sprintf($consulta,WfInstanciaBitacoraPeer::WFINSTANCIABITACORA_ID,WfInstanciaBitacoraPeer::TABLE_NAME,WfInstanciaBitacoraPeer::WFINSTANCIA_ID);
      //************************************************************************************
      $sentencia = $conexion->prepare($sql);
      $sentencia->execute();
      $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
      $bitacora_id = ($resultset->max);
      //************************************************************************************
      return $bitacora_id;
}

public function getObjUltimaBitacora($instancia_id)
{
	  $bitacora_id=0;
      $conexion = Propel::getConnection();
      $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$instancia_id ;
      $sql      = sprintf($consulta,WfInstanciaBitacoraPeer::WFINSTANCIABITACORA_ID,WfInstanciaBitacoraPeer::TABLE_NAME,WfInstanciaBitacoraPeer::WFINSTANCIA_ID);
      //************************************************************************************
      $sentencia = $conexion->prepare($sql);
      $sentencia->execute();
      $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
      $bitacora_id = ($resultset->max);
      //************************************************************************************	  
      $objBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
      //$this->writetolog("getMaxBitacora> RESULTADO>  $instancia_id  $bitacora_id ");
      return $objBitacora;
}



public function getBitacoraActual($instancia_id){
    $bitacora_id=0;
    $c=new Criteria();
    $c->add(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,$instancia_id);
    $c->addJoin(WfInstanciaBitacoraPeer::WFINSTANCIA_ID,WfInstanciaPeer::WFINSTANCIA_ID);
    $c->add(WfInstanciaBitacoraPeer::FECHA_F,null);
    $rs = WfInstanciaBitacoraPeer::doSelect($c);
    $bitacora_id=0;
    foreach($rs as $respo){
        $bitacora_id = $respo->getWfInstanciaBitacoraId() ;
    }
    if($bitacora_id==0){
        //encontrar el ultimo paso ejecutado
        $bitacora_id = $this->getMaxBitacora($instancia_id);
        if($bitacora_id==0){
             $this->insertPrimerPaso($instancia_id, $tarea_id, $usuario_id );
             //encontrar el ultimo paso ejecutado
             $bitacora_id=$this->getMaxBitacora($instancia_id);
        }
    }
    return $bitacora_id;
}


public function closeInstancia($instancia_id){

       if($instancia_id>0){
               $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
               $esta_abierta=$myInstancia->getEstaAbierta();
               if($esta_abierta){
                       $myInstancia->setEstaAbierta(false);
                       $myInstancia->setFechaf(date("Y-m-d G:i:s"));
                       $myInstancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
                       $myInstancia->save();
                       //cierra todas las bitacoras...
               }
       }
}


public function cierraInstancia($instancia_id){
          $bitacora_id    =$this->getMaxBitacora($instancia_id);
          $myLastBitacora =WfInstanciaBitacoraPeer::retrieveByPk($bitacora_id);
		  
		  $tipo=$this->getTipoActividad($instancia_id);
		  
          $msg="Cerrando Instancia> Instancia=[$instancia_id] bitacora=[$bitacora_id] tipo=$tipo";
          //$this->writetolog($msg);
		  if($tipo!=3){
              return 0;
          }
		  $myLastBitacora->setfechaF(date("Y-m-d G:i:s"));
		  $myLastBitacora->setEsActual(false);
          $myLastBitacora->setobservaciones("Cerrada");
          $myLastBitacora->save();
          $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
          if($myInstancia->getestaabierta() ){
              $myInstancia->setestaabierta(false);
              $myInstancia->setfechaf(date("Y-m-d G:i:s"));
              $myInstancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
              $myInstancia->save();
           }else{
              return 0;
           }
}


public function cierraInstanciaIncondicionalmente($instancia_id){
    if($instancia_id>0){
		$bitacora_id    =$this->getMaxBitacora($instancia_id);
        if($bitacora_id){
          $myLastBitacora =WfInstanciaBitacoraPeer::retrieveByPk($bitacora_id);
          $tipo=$this->getTipoActividad($instancia_id);
		  $msg="Cerrando Instancia> Instancia=[$instancia_id] bitacora=[$bitacora_id] tipo=$tipo";
          //$this->writetolog($msg);
		  $myLastBitacora->setfechaF(date("Y-m-d G:i:s"));
		  $myLastBitacora->setEsActual(false);
          $myLastBitacora->setobservaciones("Cerrada");
          $myLastBitacora->save();
        }
        $myInstancia = WfInstanciaPeer::retrieveByPk($instancia_id);
        if($myInstancia->getestaabierta() ){
              $myInstancia->setestaabierta(false);
              $myInstancia->setfechaf(date("Y-m-d G:i:s"));
              $myInstancia->setFechaUltimaActividad(date("Y-m-d G:i:s"));
              $myInstancia->save();
        }else{
              return 0;
        }
    }
}



  public function getGrantedUsers($instancia_id, $actividad_id=0){
  	 //$this->writetolog("Revisando Permisos para inst= $instancia_id  activ= $actividad_id");
  	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }
	 $granted_users=Array();
     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 $c->addJoin(WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID ,WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID );
	 
	 $rs =WfActividadtransicionUsuarioPeer::doSelect($c);
	 //var_dump($rs);
	 foreach($rs as $row){
	    $granted_users[]=$row->getUsuarioId();
		//$this->writetolog(">". $row->getUsuarioId());	
	 }
	 return $granted_users;	
  }

   public function getArrayGrantedUsers($instancia_id, $actividad_id=0){
  	 //$this->writetolog("Revisando Permisos para inst= $instancia_id  activ= $actividad_id");
  	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }
	 $granted_users=Array();
     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 $c->addJoin(WfActividadTransicionPeer::WFACTIVIDADTRANSICION_ID ,WfActividadtransicionUsuarioPeer::WFACTIVIDADTRANSICION_ID );
	 
	 $rs =WfActividadtransicionUsuarioPeer::doSelect($c);
	 //var_dump($rs);
	 foreach($rs as $row){
	    $granted_users[]=$row->getUsuario();
		//$this->writetolog(">". $row->getUsuarioId());	
	 }
	 return $granted_users;	
  }



  public function getRequiereComunicacion($instancia_id, $actividad_id=0){
	  //retorna  1:interna  10:recibida  100:enviada... puede tener combinaciones...
	 //$this->writetolog("Revisando Permisos para inst= $instancia_id  activ= $actividad_id");
  	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }

     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 
	 $rs =WfActividadtransicionPeer::doSelect($c);
	 $requiereComEnviada=0;
	 $requiereComRecibida=0;
	 $requiereComInterna=0;
	 $requiere=0;
	 foreach($rs as $row){
	 	$requiere=$row->getRequiereComInterna()==1? $requiere+1 : $requiere+0;
	 	$requiere=$row->getRequiereComRecibida()==1? $requiere+2 : $requiere+0;
	 	$requiere=$row->getRequiereComEnviada()==1? $requiere+4 : $requiere+0;
	 	
	 	
	    $requiereComInterna= $row->getRequiereComInterna()==1?1:$requiereComInterna ;
	    $requiereComRecibida= $row->getRequiereComRecibida()==1?1:$requiereComRecibida ;
	    $requiereComEnviada= $row->getRequiereComEnviada()==1?1:$requiereComEnviada ;
		//$this->writetolog(">". $row->getUsuarioId());	
	 }
	 return $requiere;	
  }

    public function getRequiereInterna($instancia_id, $actividad_id=0){
	  //retorna  1:interna  10:recibida  100:enviada... puede tener combinaciones...
	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }
     //$this->writetolog("Revisando com requeridas para inst= $instancia_id  activ= $actividad_id");
  	 
     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
     $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 
	 $rs =WfActividadtransicionPeer::doSelect($c);
	 $requiereComEnviada=0;
	 $requiereComRecibida=0;
	 $requiereComInterna=0;
	 $requiere=0;
	 foreach($rs as $row){
	 	$requiere += $row->getRequiereComInterna() ;
	 }
	 return $requiere;	
     }
     public function getRequiereRecibida($instancia_id, $actividad_id=0){
	  //retorna  1:interna  10:recibida  100:enviada... puede tener combinaciones...
	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }
//$this->writetolog("Revisando com requeridas para inst= $instancia_id  activ= $actividad_id");
  	 
     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 
	 $rs =WfActividadtransicionPeer::doSelect($c);
	 $requiereComEnviada=0;
	 $requiereComRecibida=0;
	 $requiereComInterna=0;
	 $requiere=0;
	 foreach($rs as $row){
	 	$requiere += $row->getRequiereComRecibida() ;
	 }
	 return $requiere;	
     }
     public function getRequiereEnviada($instancia_id, $actividad_id=0){
	  //retorna  1:interna  10:recibida  100:enviada... puede tener combinaciones...
	 if($instancia_id > 0){
  	     $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
  	     $bitacora_id=$this->getMaxBitacora($instancia_id);
  	     $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
  	     $actividad_id=$myBitacora->getWfActividadId();
	 }
  //   $this->writetolog("Revisando com requeridas para inst= $instancia_id  activ= $actividad_id");
  	
     $c=new Criteria();
     $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
	 $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);
	 $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
	 
	 $rs =WfActividadtransicionPeer::doSelect($c);
	 $requiereComEnviada=0;
	 $requiereComRecibida=0;
	 $requiereComInterna=0;
	 $requiere=0;
	 foreach($rs as $row){
	 	$requiere += $row->getRequiereComEnviada() ;
	 }
	 return $requiere;	
     }
     
     public function callback($instancia_id, $com_id, $buzon, $bitacora_id=0){
     	$this->writetolog("callback($instancia_id, $com_id, $buzon, $bitacora_id=0)");
		$objBitacora=0;
		if($bitacora_id==0){
			$objBitacora=$this->getObjUltimaBitacora($instancia_id);
			
		}else{
			$objBitacora=WFInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
		}
		if($buzon==2){
		    $objBitacora->setComInternaId($com_id);	
		}
		if($buzon==1){
		    $objBitacora->setComRecibidaId($com_id);	
		}
		if($buzon==3){
		    $objBitacora->setComEnviadaId($com_id);	
		}
		$objBitacora->save();
	 }
     
     public function getEstadoBitacora($instancia_id)
     {
     	    $strreturn="";
     	    if(! $this->getEstaAbiertaInstancia($instancia_id)){
				return "Cerrado";
			}else{
			    $bit_id = $this->getMaxBitacora($instancia_id);
     	        if($bit_id>0){  	
		            $objBitacora = WfInstanciaBitacoraPeer::retrieveByPK($bit_id);
		            $estado_id = $objBitacora->getEstadoActividad();
		            if($estado_id>0){
                        $strreturn = "Ejecutada";
		                //$objWfEstado = WfEstadoPeer::retrieveByPK($estado_id );
		                //$strreturn = $objWfEstado->getDescripcion();
		            }
			     }	
			}
			return $strreturn;
	 }

     public function getEstaAbiertaInstancia($instancia_id)
     {
     	    $esta_abierta=true;
     	    if($instancia_id>0){  	
		        $objInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
		        $esta_abierta=$objInstancia->getEstaAbierta();
			}
			return $esta_abierta;
	 }
	 	 	 
    public function getTipoActividad($instancia_id, $actividad_id=0)
    {    
        if($instancia_id > 0){
            $myInstancia=WfInstanciaPeer::retrieveByPK($instancia_id);
            $bitacora_id=$this->getMaxBitacora($instancia_id);
            $myBitacora=WfInstanciaBitacoraPeer::retrieveByPK($bitacora_id);
            $actividad_id=$myBitacora->getWfActividadId();
        }
        //************************************************************************
        $c=new Criteria();
        $c->add(WfInstanciaPeer::WFINSTANCIA_ID,$instancia_id);
        $c->addJoin(WfActividadTransicionPeer::WF_FLUJO_ID,WfInstanciaPeer::WF_FLUJO_ID);
        $c->add(WfActividadTransicionPeer::WF_ACTIVIDAD_ID, $actividad_id);        
        $rs =WfActividadtransicionPeer::doSelect($c);
        //************************************************************************
        $requiereComEnviada=0;
        $requiereComRecibida=0;
        $requiereComInterna=0;
        $tipoactividad=0;
        foreach($rs as $row){
            $tipoactividad= $row->getWftipoactividadId() ;
        }
        return $tipoactividad;	
    }
    
    public function envioEmail($comunicacion_id,$user,$buzon_id)
    {
        if(trim($comunicacion_id) != "")
        {
            $radicado ="";
            $fecha_radicacion = "";
            $asunto = "";
            $subject = "";
            $usuario = UsuarioPeer::retrieveByPK($user);
            $email_destino = $usuario->getEmail();
            //*********************************************************************************
            switch($buzon_id){
                case 1:
                        $com_recibida_mail = ComRecibidaPeer::retrieveByPK($comunicacion_id);
                        $radicado = $com_recibida_mail->getRadicado();
                        $fecha_radicacion = $com_recibida_mail->getFechaCreacion();
                        $asunto = $com_recibida_mail->getAsunto();
                        $subject = "CAD : Workflow Comunicaciones Recibidas";
                break;
                case 2:
                        $com_interna_mail = ComInternaPeer::retrieveByPK($comunicacion_id);
                        $radicado = $com_interna_mail->getRadicado();
                        $fecha_radicacion = $com_interna_mail->getFechaCreacion();
                        $asunto = $com_interna_mail->getReferencia();
                        $subject = "CAD : Workflow Comunicaciones Internas";
                break;
            }
            //*********************************************************************************
            $cuerpo = '
            <html>
            <head>
            <title></title>
            </head>
            <body>
            <div id="cotenedor">
            <br>
            Este es un mensaje para informarle que se le ha asignado una actividad en el workflow de la siguiente comunicacion: 
            <br>
            <br>
            <b>Radicado:</b> '.$radicado.'<br>
            <b>Fecha Radicado:</b> '.$fecha_radicacion.' <br>
            <b>Asunto:</b> '.$asunto.'<br>
            <b>Fecha Asignacion Tramite:</b> '.date("Y-m-d G:i:s").'<br>
            <br>
            </div>
            </body></html>';
            $cabeceras = "Content-type: text/html\r\n";
            //*********************************************************************************
            $baseMail = new BaseMailSimad();
            $baseMail->SetSubject($subject);
            $baseMail->SetMsgHTML($cuerpo);
            $baseMail->SetAddAddress($email_destino, $email_destino);
            //*********************************************************************************
            if($baseMail->InitSend() === true){
               $baseMail->writetolog("Alerta enviada Radicado: " . $radicado . " Enviado a: " . $email_destino);
            }else{
               $baseMail->writetolog("Error al enviar alerta radicado: " . $radicado . " Cuenta correo: " . $email_destino);
            }
        }
    }
};
?>