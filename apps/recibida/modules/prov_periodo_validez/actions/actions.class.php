<?php

/**
 * prov_periodo_validez actions.
 *
 * @package    simad
 * @subpackage prov_periodo_validez
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 8507 2008-04-17 17:32:20Z fabien $
 */
class prov_periodo_validezActions extends sfActions
{

  public function verificaPrilegio($currentForm)
  { 
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}	 
  }
  
  public function verificaPrilegioCerrar($currentForm)
  { 
 	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
	if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
		$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
	}	 	  
  } 
      
  public function executeIndex()
  {
    $this->prov_periodo_validezList = ProvPeriodoValidezPeer::doSelect(new Criteria());
  }
  
  public function enviarEmail($cuerpo , $user)
  {
  	$usuario = UsuarioPeer::retrieveByPK($user);
    //*********************************************************************************************************************
    $mail_destino = $usuario->getEmail();
    $baseMail = new BaseMailSimad();
    $baseMail->SetSubject('CAD :Proveedores');
    $baseMail->SetMsgHTML($cuerpo);
    $baseMail->SetAddAddress($mail_destino, $mail_destino);    
    if($baseMail->InitSend() === true)
    {
       $baseMail->writetolog("Alerta enviada: " . $mail_destino . " Enviado a: " . $mail_destino);
    }else{
       $baseMail->writetolog("Error al enviar alerta: " . $mail_destino . " Cuenta correo: " . $mail_destino);
    }
    //***********************************************************************************************************************
  }


  public function executeCreate($request)
  { 
    $currentForm="prov_periodo_validez/create";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    $this->periodo_validez = new ProvPeriodoValidez();
    $this->proveedor_id=$this->getRequestParameter('proveedor_id');
    //$this->setTemplate('edit');
  }

  public function executeEdit($request)
  { 
    $currentForm="prov_periodo_validez/edit";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    
    $this->periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
  }
  
  public function executeEditarpaso1($request)
  { //liberar para aprobaciones
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    
   $c=new Criteria();   
   $c->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,5);
   $c->add(ProvUsuarioAreaPeer::PROV_ESTADO_APROBADOR_ID,1);//1 es activo, 2 es inactivo
   $this->usuarios_compras=ProvUsuarioAreaPeer::doSelect($c);
    
  } 
  public function executeEditarpaso2($request)
  { //aprobacion del area de impuestos
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
    $this->prov_tipo_retencion=new ProvTipoRetencion();
  }
  public function executeEditarpaso3($request)
  { //aprobacion del area de contabilidad
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
  }
  public function executeEditarpaso4($request)
  { //aprobacion del area de tesoreria
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
     $this->prov_via_pago=new ProvViaPago();
  }
  public function executeEditarpaso5($request)
  { //aprobacion del area de compras
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
  }
  public function executeEditarpaso6($request)
  { //aprobacion del area de proveedores al analista
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
    $this->entidad = new Entidad();
    /*$c = new Criteria();
    $c->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,7);
    $this->usuarios_area = ProvUsuarioAreaPeer::doSelect($c);*/
  }
  public function executeEditarpaso7($request)
  { //aprobacion del area de impuestos Jefe de gestion de proveedores  para aprobar creacion
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
    
    $c = new Criteria();
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,5);
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,2);
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,3);
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,4);
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,8);
    $c->addOr(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,9);
    $this->usuarios_area = ProvUsuarioAreaPeer::doSelect($c);
    
  }
  public function executeEditarpaso8($request)
  { //creacion correspondiente es sap
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
  }public function executeEditarpaso9($request)
  { //la verificacion de la creaci�n 
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
  }
  
  public function executeUpdatepaso1($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=1;
   $estado_aprobacion=1;
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   //if($request->getParameter('prov_revisado_creacion_id')==2)
     $this->actualizaEstadoProveedor($prov_periodo_validez->getProveedorId(),3);
   
   //region enviar email
     $msge="Este mensaje es para informarle que se libero un proveedor para su aprobacion en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(2,$msge);
     $this->enviarAlertas(3,$msge);
     $this->enviarAlertas(4,$msge);
     
     if($request->getParameter('usuario_compras_id')!=0 ){
		$this->enviarAlertasCompras($request->getParameter('usuario_compras_id'),$msge); 
	 }
	 else{
		// $this->enviarAlertas(5,$msge);
	}
	  
     
     $this->enviarAlertas(6,$msge);    
   //fin region  
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }
  
  
  public function executeAlertaSend(){    
      $this->prov_periodo_validez_id = $this->getRequestParameter('prov_periodo_validez_id');                  
  }
  
  public function executeReloadAlertas(){
    
      $prov_periodo_validez_id = $this->getRequestParameter('prov_periodo_validez_id');                  
      $prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPK($prov_periodo_validez_id);      
      $msge="Este mensaje es para informarle que se devolvio la aprobacion de un proveedor para su revision en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
      
      if($this->getRequestParameter('impuestos')?'1':'0'){                    
          $this->enviarAlertas(2,$msge);
      }
      
      if($this->getRequestParameter('contabilidad')?'1':'0'){          
          $this->enviarAlertas(3,$msge);
      }
      
      if($this->getRequestParameter('tesoreria')?'1':'0'){          
          $this->enviarAlertas(4,$msge);
      }
      
      if($this->getRequestParameter('compras')?'1':'0'){          
          $this->enviarAlertas(5,$msge);
      }
            
  }
  
  public function enviarAlertas($item,$msge,$usuario_id=""){
    
      $cuser=new Criteria();
      $cuser->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,$item);//aprobadores de un area especifica
      if($usuario_id != ""){
        $cuser->add(ProvUsuarioAreaPeer::USUARIO_ID,$usuario_id);//enviar alerta a un usuario especifico
      }
      $cuser->add(ProvUsuarioAreaPeer::PROV_ESTADO_APROBADOR_ID,1);//1 es activo, 2 es inactivo
      $usuarios_email=ProvUsuarioAreaPeer::doSelect($cuser);   
      
      foreach($usuarios_email as $usuario_email){
        //*********************************************************************************************************************
        $mail_destino = $usuario_email->getUsuario()->getEmail();
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('Area Proveedores : Aprobaciones');
        $baseMail->SetMsgHTML($cuerpo);
        $baseMail->SetAddAddress($mail_destino, $mail_destino);    
        if($baseMail->InitSend() === true)
        {
           $baseMail->writetolog("Alerta enviada: " . $mail_destino . " Enviado a: " . $mail_destino);
        }else{
           $baseMail->writetolog("Error al enviar alerta: " . $mail_destino . " Cuenta correo: " . $mail_destino);
        }
        //***********************************************************************************************************************
      }     
  }

  public function enviarAlertasCompras($usuario_id,$msge){
    
      
      $usuarios_email=UsuarioPeer::retrieveByPK($usuario_id);   
      
      foreach($usuarios_email as $usuario_email)
      {
        //*********************************************************************************************************************
        $mail_destino = $usuario_email->getUsuario()->getEmail();
        $baseMail = new BaseMailSimad();
        $baseMail->SetSubject('CAD :Proveedores');
        $baseMail->SetMsgHTML($msge);
        $baseMail->SetAddAddress($mail_destino, $mail_destino);    
        if($baseMail->InitSend() === true)
        {
           $baseMail->writetolog("Alerta enviada: " . $mail_destino . " Enviado a: " . $mail_destino);
        }else{
           $baseMail->writetolog("Error al enviar alerta: " . $mail_destino . " Cuenta correo: " . $mail_destino);
        }
        //***********************************************************************************************************************
      }     
  }
  
  
  public function executeUpdatepaso2($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=2;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvTipoContribuyenteId($request->getParameter('prov_tipo_contribuyente_id'));
   $prov_periodo_validez->save();
   
   $prov_tipo_retencion_ids=$request->getParameter('prov_tipo_retencion_id');   
   
   //borra las anteriores
    $conexion = Propel::getConnection();
  	$consulta = "delete from %s where %s=".$prov_periodo_validez->getProvPeriodoValidezId();
	$sql = sprintf($consulta, ProvTipoRetencionAprobadaPeer::TABLE_NAME, ProvTipoRetencionAprobadaPeer::PROV_PERIODO_VALIDEZ_ID);
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
   foreach($prov_tipo_retencion_ids as $prov_tipo_retencion_id){
     // mete las nuevas
     $prov_tipo_retencion_aprobada=new ProvTipoRetencionAprobada();
     $prov_tipo_retencion_aprobada->setProvPeriodoValidezId($prov_periodo_validez->getProvPeriodoValidezId());
     $prov_tipo_retencion_aprobada->setProvTipoRetencionId($prov_tipo_retencion_id);
     $prov_tipo_retencion_aprobada->setFechaCreacion($fecha_actual);
     $prov_tipo_retencion_aprobada->save();
   }
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
   //alertas
   $msge="Este mensaje es para informarle que compras aprobo o rechazo un proveedor para su revisi�n en SIMAD WEB 4.0: 
   Proveedor:".$prov_periodo_validez->getProveedor();
   //$this->enviarAlertas(2,$msge);
   $this->enviarAlertas(10,$msge);
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }

  public function executeUpdatepaso3($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=3;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvCuentaAsociadaId($request->getParameter('prov_cuenta_asociada_id'));   
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }  

  public function executeUpdatepaso4($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=4;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvGrupoTesoreriaId($request->getParameter('prov_grupo_tesoreria_id'));   
   $prov_periodo_validez->save();
   
   
    $prov_via_pago_ids=$request->getParameter('prov_via_pago_id');   
    //borra las anteriores
    $conexion = Propel::getConnection();
  	$consulta = "delete from %s where %s=".$prov_periodo_validez->getProvPeriodoValidezId();
	$sql      = sprintf($consulta, ProvViaPagoAprobadaPeer::TABLE_NAME, ProvViaPagoAprobadaPeer::PROV_PERIODO_VALIDEZ_ID);
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
   foreach($prov_via_pago_ids as $prov_via_pago_id){
     //borra las anteriores y mete las nuevas
     $prov_via_pago_aprobada=new ProvViaPagoAprobada();
     $prov_via_pago_aprobada->setProvPeriodoValidezId($prov_periodo_validez->getProvPeriodoValidezId());
     $prov_via_pago_aprobada->setProvViaPagoId($prov_via_pago_id);
     $prov_via_pago_aprobada->setFechaCreacion($fecha_actual);
     $prov_via_pago_aprobada->save();
   }
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }  
  
  public function executeUpdatepaso5($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=5;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvGrupoEsquemaId($request->getParameter('prov_grupo_esquema_id'));   
   $prov_periodo_validez->setProvCondicionPagoId($request->getParameter('prov_condicion_pago_id'));
   $prov_periodo_validez->setProvMonedaPedidoId($request->getParameter('prov_moneda_pedido_id'));
   $prov_periodo_validez->setProvCondicionExpedicionId($request->getParameter('prov_condicion_expedicion_id'));
   $prov_periodo_validez->setProvAduanaEntradaId($request->getParameter('prov_aduana_entrada_id'));
   $prov_periodo_validez->setProvCorrespondienteAutofacturacionId($request->getParameter('prov_correspondiente_autofacturacion_id'));
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
      
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }
  
   public function executeUpdatepaso6($request)
   {
   
   //region habilitar pasos 3 al 5 se hace completa la tarea del area 1 automaticmente
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=1;
   $estado_aprobacion=1;
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   $entidad =  EntidadPeer::retrieveByPK($request->getParameter('entidad_id'));
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   //if($request->getParameter('prov_revisado_creacion_id')==2)
   $this->actualizaEstadoProveedor($prov_periodo_validez->getProveedorId(),3);
   //fin region habilitar 
    
    
    
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=6;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvTipoIndustriaId($request->getParameter('prov_tipo_industria_id'));   
   $prov_periodo_validez->setProvRechazadaCreacionId($request->getParameter('prov_rechazada_creacion_id'));
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
     $msge="Este mensaje es para informarle que se libero un proveedor de ".$entidad->getDescripcion()." para su aprobacion en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(2,$msge);
     $this->enviarAlertas(3,$msge);
     $this->enviarAlertas(4,$msge);
     $this->enviarAlertas(5,$msge);
     
     //$msge="Este mensaje es para informarle que se aprobo un proveedor para su revision en SIMAD WEB 4.0: 
     //Proveedor:".$prov_periodo_validez->getProveedor();
     if($this->getRequestParameter('usuario_enviar_id')){
        $this->enviarAlertas(7,$msge,$this->getRequestParameter('usuario_enviar_id'));
     }else{
        $this->enviarAlertas(7,$msge);
     }  
     //fin region  
             
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }
  
   public function executeUpdatepaso7($request)
   {
    
     $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
     
   //para devolver el proceso es necesario cambiar el estado_de aprobacion a no aprobado y avisar
   
   //contabilidad
   if($request->getParameter('ir_a_paso')=='3' || $request->getParameter('ir_a_paso')=='4')
   {
    $cri1=new Criteria();
    $cri1->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $cri1->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,3);
    $prov_estado_flujo_periodo1=ProvEstadoFlujoPeriodoPeer::doSelectOne($cri1);
    $prov_estado_flujo_periodo1->setProvEstadoAprobacionid(2);
    $prov_estado_flujo_periodo1->save();
    $msge="Este mensaje es para informarle que se devolvio la aprobacion de un proveedor para su revision en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(3,$msge); 
   }
   
   //tesoreria
   if($request->getParameter('ir_a_paso')=='3' || $request->getParameter('ir_a_paso')=='5')
   {
    $cri2=new Criteria();
    $cri2->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $cri2->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,4);
    $prov_estado_flujo_periodo2=ProvEstadoFlujoPeriodoPeer::doSelectOne($cri2);
    $prov_estado_flujo_periodo2->setProvEstadoAprobacionid(2);    
    $prov_estado_flujo_periodo2->save();
    $msge="Este mensaje es para informarle que se devolvio la aprobacion de un proveedor para su revision en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(4,$msge); 
   }
   
   //Compras
   if($request->getParameter('ir_a_paso')=='6')
   {
    $cri5=new Criteria();
    $cri5->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $cri5->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,5);
    $prov_estado_flujo_periodo2=ProvEstadoFlujoPeriodoPeer::doSelectOne($cri5);
    $prov_estado_flujo_periodo2->setProvEstadoAprobacionid(2);    
    $prov_estado_flujo_periodo2->save();
    $msge="Este mensaje es para informarle que se devolvio la aprobacion de un proveedor para su revision en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     //si es a un usuario o a todos
     //$msge="Este mensaje es para informarle que se aprobo un proveedor para su revision en SIMAD WEB 4.0: 
     //Proveedor:".$prov_periodo_validez->getProveedor();
     if($this->getRequestParameter('usuario_enviar_id')){
        $this->enviarAlertas(5,$msge,$this->getRequestParameter('usuario_enviar_id'));
     }else{
        $this->enviarAlertas(5,$msge);
     }
     
   }
   
   /*'1'=>'Creacion en SAP',
   '2'=>'Revision de la creacion en SAP',
   '3'=>'Contabilidad y tesoreria',
   '4'=>'Contabilidad',
   '5'=>'Tesoreria'   */
   
   //para ir al paso 8 solo si escogio ir al paso 8 o 9:
  
   if($request->getParameter('ir_a_paso')=='1' || $request->getParameter('ir_a_paso')=='2')
   { 
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=7;
   $estado_aprobacion=$request->getParameter('prov_estado_aprobacion_id');
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProbAprobadoParaCreacionId($request->getParameter('prob_aprobado_para_creacion_id'));   
   $prov_periodo_validez->setProvRechazadaCreacion2Id($request->getParameter('prov_rechazada_creacion2_id'));   
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
     //region enviar email
     $msge="Este mensaje es para informarle que se aprobo un proveedor para su creacion en sap desde SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(8,$msge);// monica reina
     $this->enviarAlertas(9,$msge);//director prov fanny  
     //fin region 
    }
     
   //para avanzar al paso 9 es necesario insertar la aprobacion del 8 automaticamente
   if($request->getParameter('ir_a_paso')=='2')
   {
       $fecha_actual=date("Y-m-d G:i:s");
       $item_flujo=8;
       $estado_aprobacion=1;//uno es aprobado
       $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
       
       //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
       //$prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
       $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
       //$prov_periodo_validez->setCodigoSap($request->getParameter('codigo_sap'));   
       //$prov_periodo_validez->setFechaCreacionSap($request->getParameter('fecha_creacion_sap'));  
       $prov_periodo_validez->save();
       
       $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
       
       $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
       
       $msge="Este mensaje es para informarle que se aprobo un proveedor para su revision en sap desde SIMAD WEB 4.0: 
              Proveedor:".$prov_periodo_validez->getProveedor();
       $this->enviarAlertas(9,$msge);//director prov fanny
   } 
    
      
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }
  
  public function executeUpdatepaso8($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=8;
   $estado_aprobacion=1;//uno es aprobado
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setCodigoSap($request->getParameter('codigo_sap'));   
   $prov_periodo_validez->setFechaCreacionSap($request->getParameter('fecha_creacion_sap'));  
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
     //region enviar email
     $msge="Este mensaje es para informarle que se actualizo en sap un proveedor para su revision en SIMAD WEB 4.0: 
     Proveedor:".$prov_periodo_validez->getProveedor();
     $this->enviarAlertas(9,$msge);  
     //fin region 
     
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
  }    

  public function executeUpdatepaso9($request){
   $fecha_actual=date("Y-m-d G:i:s");
   $item_flujo=9;
   $estado_aprobacion=1;//uno es aprobado
   $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
   
   //guardo el usuario que realiza, el estado de flujo que se aprueba y el provperiodovalidez
   $prov_periodo_validez= ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
   $prov_periodo_validez->setObservacionesGenerales($prov_periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones'));
   $prov_periodo_validez->setProvRevisadoCreacionId($request->getParameter('prov_revisado_creacion_id'));   
   $prov_periodo_validez->save();
   
   $this->guardaAprobacion($usuariologuiado,$prov_periodo_validez->getProvPeriodoValidezId(),$fecha_actual,$estado_aprobacion,$this->getUsuarioAreaIdLoguiado(),$request->getParameter('observaciones'));
   
   $this->guardaEstadoFlujo($prov_periodo_validez->getProvPeriodoValidezId(),$estado_aprobacion,$item_flujo);
   
    if($request->getParameter('prov_revisado_creacion_id')==2)
     $this->actualizaEstadoProveedor($prov_periodo_validez->getProveedorId(),4);
   
   
   $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());
   
   
  } 
  public function actualizaEstadoProveedor($proveedor_id,$estado_id){
    
     $proveedor = ProveedorPeer::retrieveByPk($proveedor_id);
     $proveedor->setProvEstadoId($estado_id);
     $proveedor->save();
    
  }
      
  public function guardaEstadoFlujo($prov_periodo_validez_id,$estado,$item_flujo)
  { 
   $c=new Criteria();
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,$item_flujo);
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$prov_periodo_validez_id);
   $cuenta=ProvEstadoFlujoPeriodoPeer::doCount($c);
   if($cuenta>0){
    $prov_estado_flujo_periodo=ProvEstadoFlujoPeriodoPeer::doSelectOne($c);
   }
   else{
    $prov_estado_flujo_periodo=new ProvEstadoFlujoPeriodo();
   }   
   
   $prov_estado_flujo_periodo->setProvEstadoAprobacionId($estado);
   $prov_estado_flujo_periodo->setProvPeriodoValidezId($prov_periodo_validez_id);
   $prov_estado_flujo_periodo->setProvItemFlujoId($item_flujo);
   $prov_estado_flujo_periodo->save();
    
  }
  
  public function getUsuarioAreaIdLoguiado()
  { 
    $usuario_area_aprob= ProvUsuarioAreaPeer::doSelectOne(new Criteria());
    return $usuario_area_aprob->getProvUsuarioAreaId();
  }
  
  public function guardaAprobacion($usuariologuiado,$prov_periodo_validez_id,$fecha_actual,$estado,$usuario_area_id,$obs)
  {
   //$fecha_actual=$fecha_actual." ".date("G:i:s");
   $prov_aprobacion=new ProvAprobacion();
   $prov_aprobacion->setUsuarioId($usuariologuiado);
   $prov_aprobacion->setProvPeriodoValidezId($prov_periodo_validez_id);
   $prov_aprobacion->setFechaCreacion($fecha_actual);
   $prov_aprobacion->setProvEstadoAprobacionId($estado);//1 aprobado; 2 no aplobado
   $prov_aprobacion->setProvUsuarioAreaId($usuario_area_id);
   $prov_aprobacion->setObservaciones($obs);
   $prov_aprobacion->save();
    
  }
  
  public function executeUpdateestadodocsi($request)
  {
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_periodo_validez->setDocumentacionCompleta('SI');
    $this->prov_periodo_validez->save();
    $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$request->getParameter('prov_periodo_validez_id'));
  }
  
  public function executeUpdateestadodocno($request)
  {
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $this->prov_periodo_validez->setDocumentacionCompleta('NO');
    $this->prov_periodo_validez->save();
    $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$request->getParameter('prov_periodo_validez_id'));
  }

  public function executeShow($request)
  { 
    $currentForm="prov_periodo_validez/show";
  	$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
  	$this->verificaPrilegio($currentForm);
    
    $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
    $this->prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    
    $c=new Criteria();
    $c->add(ProvTipoRetencionAprobadaPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $this->prov_tipo_retencionaprobadas = ProvTipoRetencionAprobadaPeer::doSelect($c);
    
    $c=new Criteria();
    $c->add(ProvViaPagoAprobadaPeer::PROV_PERIODO_VALIDEZ_ID,$request->getParameter('prov_periodo_validez_id'));
    $this->prov_via_pago_aprobadas = ProvViaPagoAprobadaPeer::doSelect($c);  
    
    
    //region revisar si al usuario actual le muestra el boton de accion
    $this->mostrar_boton_paso_1=0;
    $this->mostrar_boton_paso_2=0;
    $this->mostrar_boton_paso_3=0;
    $this->mostrar_boton_paso_4=0;
    $this->mostrar_boton_paso_5=0;
    $this->mostrar_boton_paso_6=0;
    $this->mostrar_boton_paso_7=0;
    $this->mostrar_boton_paso_8=0;
    $this->mostrar_boton_paso_9=0;
        
        //areas: 1=contabilidad, 2= tesoreria, 3= impuestos, 4= compras, 5= proveedores
       
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,6,$this->prov_periodo_validez->getPrimaryKey())){//el primero no tiene predecesor
             //$this->mostrar_boton_paso_1=1;//la tarea 1 no aparece porque es automatica desde el paso 6
             $this->mostrar_boton_paso_6=1;
         }         
         
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,2,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_2=1;
         }  
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,3,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_3=1;
         }
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,4,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_4=1;
         }
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,5,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_5=1;
         }      
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,6,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_6=1;
         }
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,7,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(1,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_7=1;
         }
       // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,8,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(7,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_8=1;
         } 
         // si el estado es correcto y el usuario es correcto entonces muestra el boton y si el estado de la accion no se ha ejecutado
         if($this->muestraBotonUsuarioArea($usuariologuiado,9,$this->prov_periodo_validez->getPrimaryKey())&& $this->ejecutadoEstadoPredecesor(8,$this->prov_periodo_validez->getPrimaryKey())){
             $this->mostrar_boton_paso_9=1;
         }
         
         //si la documentacion esta incompleta ningun boton aparece.
         if($this->prov_periodo_validez->getDocumentacionCompleta()!="SI")
         {
            $this->mostrar_boton_paso_1=0;
            $this->mostrar_boton_paso_2=0;
            $this->mostrar_boton_paso_3=0;
            $this->mostrar_boton_paso_4=0;
            $this->mostrar_boton_paso_5=0;
            $this->mostrar_boton_paso_6=0;
            $this->mostrar_boton_paso_7=0;
            $this->mostrar_boton_paso_8=0;
            $this->mostrar_boton_paso_9=0; 
         }
    //fin region  
    
  }  
  public function muestraBotonUsuarioArea($usuario_id,$prov_area_aprobadora_id,$prov_periodo_validez_id){
    
   //revisar si ese usuario con esa area ya aprobo ese periodo de validez : entonces retorna false
   $c=new Criteria();
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$prov_periodo_validez_id);
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,$prov_area_aprobadora_id);
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ESTADO_APROBACION_ID,1);//1 es activo, 2 es inactivo
   $cuenta=ProvEstadoFlujoPeriodoPeer::doCount($c);
   if($cuenta>=1){
    return false;// porque ya aprobo
   }
   
   //revisar si tiene el permiso  
   $c=new Criteria();
   $c->add(ProvUsuarioAreaPeer::USUARIO_ID,$usuario_id);
   $c->add(ProvUsuarioAreaPeer::PROV_AREA_APROBADORA_ID,$prov_area_aprobadora_id);
   $c->add(ProvUsuarioAreaPeer::PROV_ESTADO_APROBADOR_ID,1);//1 es activo, 2 es inactivo
   $cuenta=ProvUsuarioAreaPeer::doCount($c);
   if($cuenta>0){
    $tiene_permiso=true;
   }
   else{
    $tiene_permiso=false;
   }   
    
    
    return $tiene_permiso;
  }
  public function ejecutadoEstadoPredecesor($item_flujo,$prov_periodo_validez_id){
    // si el estado inicial ya fue ejecutado
    
   $c=new Criteria();
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_PERIODO_VALIDEZ_ID,$prov_periodo_validez_id);
   if($item_flujo==6){
      $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,2);
      $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,3);
      $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,4);
      $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,$item_flujo);
   }
   else{
    $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ITEM_FLUJO_ID,$item_flujo);
   }
   
   $c->add(ProvEstadoFlujoPeriodoPeer::PROV_ESTADO_APROBACION_ID,1);//1 es activo, 2 es inactivo
   
   
   $cuenta=ProvEstadoFlujoPeriodoPeer::doCount($c);
   if($cuenta>=1){
     return true;// porque ya aprobo el estado predecesor
   }
   else{
     return false;
   }
    
  }
  public function executeUpdatecreate()
  {
    $periodo_validez = new ProvPeriodoValidez();
    
    $periodo_validez->setFechaInicial($this->getRequestParameter('fecha_inicial'));
    $periodo_validez->setFechaFinal($this->getRequestParameter('fecha_final'));    
    $periodo_validez->setProveedorId($this->getRequestParameter('proveedor_id'));
    $periodo_validez->setProvRevisadoCreacionId(1);
   
    $periodo_validez->save();
    
    $proveedor = ProveedorPeer::retrieveByPk($this->getRequestParameter('proveedor_id'));
    $proveedor->setProvEstadoId(1);
    $proveedor->save();
   
    $this->redirect('proveedor/show?proveedor_id='.$this->getRequestParameter('proveedor_id'));
  }

  public function executeUpdate($request)
  { 
  	
	$fecha_actual=date("Y-m-d G:i:s");
    $periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id'));
    $periodo_validez->setFechaInicial($this->getRequestParameter('fecha_inicial'));
    $periodo_validez->setFechaFinal($this->getRequestParameter('fecha_final'));    
    $periodo_validez->setObservacionesGenerales($periodo_validez->getObservacionesGenerales()." | $fecha_actual =>".$request->getParameter('observaciones_generales'));
    $periodo_validez->save();
   
    $this->redirect('prov_periodo_validez/show?prov_periodo_validez_id='.$request->getParameter('prov_periodo_validez_id'));
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($prov_periodo_validez = ProvPeriodoValidezPeer::retrieveByPk($request->getParameter('prov_periodo_validez_id')));

    $prov_periodo_validez->delete();

    $this->redirect('prov_periodo_validez/index');
  }
}
