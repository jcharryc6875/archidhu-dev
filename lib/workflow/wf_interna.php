<?php

class wfInterna {
	 
  public function wfInterna(
  $firmanteId,
  $destinatarioId,
  $tipocominterna_id,
  $referencia,
  $contenido,
  $cominterna_id=0,
  $duplicar=0,
  $ruta="",
  $folios=1,
  $anexos=0,
  $copiaInternaId=0
  )
  {
    if (!$cominterna_id || $duplicar==1)
    {
      $com_interna = new ComInterna();
      $com_interna->setMarca(0);
    }
    else
    {
      $com_interna = ComInternaPeer::retrieveByPk($cominterna_id );
    }
    
    
	$usuariologuiado= $firmanteId;
    $objUsuarioLoguiado=UsuarioPeer::retrieveByPk($usuariologuiado);
    
    $usernameloguiado = $objUsuarioLoguiado->getUserName();
    
    $com_interna->setCominternaId($cominterna_id );
    $com_interna->setEstadocominternaId(1);
    $yearActual=date("Y");
	
	$com_interna->setPeriodoId($yearActual);
	
    $com_interna->setTipocominternaId($tipocominterna_id ? $tipocominterna_id : null);
    
    
	$com_interna->setEstadodigitalizacionId(1);
    $com_interna->setFechaCreacion(date("y-m-d h:m:s"));
	$com_interna->setReferencia($referencia);
    $com_interna->setContenido($contenido);
    
    
    $dirRaizApache = ParametroPeer::retrieveByPk(12);
	$dirImg  = ParametroPeer::retrieveByPk(15);
       
 
    if($ruta !=""){
	 	$arr = split(",",$ruta );
	 	$ruta="";
 		foreach ($arr as $result){ 
		 	if($result!="")         			              			  			 	
			$ruta.=$dirRaizApache->getValortexto().'/'.$dirImg->getValortexto().'/'.$usernameloguiado.'/'.$result.",";    
		}
        $com_interna->setRuta($ruta);
	}
    else{
        $com_interna->setRuta('');
    }
    $com_interna->setRadicado("Sin Radicar");
    $com_interna->setEsCopia(0);
    $com_interna->setFolios($folios);
    $com_interna->setAnexos($anexos);
    $com_interna->setEstaentregado(0);
    
    $com_interna->setDependenciaId($objUsuarioLoguiado->getDependenciaId());
    $com_interna->setRegionalId($objUsuarioLoguiado->getRegionalId());
    
    $com_interna->save();
    if (!$cominterna_id )
    {
      $com_interna->setCodigoReenResp($com_interna->getPrimaryKey());
    }
    
    $c2=new Criteria();
    $c2->add(CargoUsuarioPeer::USUARIO_ID,$usuariologuiado);
    $cargoUsuario= CargoUsuarioPeer::doSelectOne($c2);
    $cargos=$cargoUsuario->getCargousuarioId();   
    
	$this->borrarCominternaUsuario($com_interna->getPrimaryKey());
	$this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),1,$cargos); 
    if($firmanteId)
       $this->insertaCominternaUsuario($firmanteId,$com_interna->getPrimaryKey(),2,$cargos);
     else
       $this->insertaCominternaUsuario($usuariologuiado,$com_interna->getPrimaryKey(),2,$cargos);
    
	$this->insertaCominternaUsuario($copiaInternaId ,$com_interna->getPrimaryKey(),3,$cargos);
	$this->insertaCominternaUsuario($destinatarioId ,$com_interna->getPrimaryKey(),4,$cargos);
       
	 $usurioFirma=$this->getFirstUsurioFirmaId($com_interna->getCominternaId());   
     if(!$usurioFirma){
     	$usurioFirma=$usuariologuiado;    
	 }  
	
	$objUsuarioFirma=ComInternaPeer::retrieveByPk($usurioFirma);
    $com_interna->setRegionalId($objUsuarioFirma->getRegionalId());
    $com_interna->setDependenciaId($objUsuarioFirma->getDependenciaId());
    
	$com_interna->save();
	
	$consecutivo=$com_interna->getCominternaId();
	
    $this->executeRadicar($consecutivo);
    
    return $com_interna;
  }	

  public function borrarCominternaUsuario($com_internaId)
  {
    $conexion = Propel::getConnection();
    $consulta = "delete FROM %s where %s =".$com_internaId;
    $sql      = sprintf($consulta,CominternaUsuarioPeer::TABLE_NAME,CominternaUsuarioPeer::COMINTERNA_ID);
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
  }
  
  public function insertaCominternaUsuario($usuarios,$com_internaId,$rol,$cargos)
  { 
  	if($rol==1){
  		$usu=$usuarios;
		$objCominternaUsuario=new CominternaUsuario();
		$objCominternaUsuario->setUsuarioId($usu);
		$objCominternaUsuario->setCominternaId($com_internaId);
		$objCominternaUsuario->setCargousuarioId($cargos);
		$objCominternaUsuario->setRolusuariocominternaId($rol);
		$objCominternaUsuario->save();
	}
	else{
		$arrUsuarios=explode(',',$usuarios);
		$arrCargos=explode(',',$cargos);
		$i=0;
		foreach($arrUsuarios as $usu){
			if($usu!=0){
			  	$objCominternaUsuario=new CominternaUsuario();
				$objCominternaUsuario->setUsuarioId($usu);
				$objCominternaUsuario->setCominternaId($com_internaId);
				$objCominternaUsuario->setRolusuariocominternaId($rol);
				//$cargo=$arrCargos[$i];
				$cargo=$arrCargos[0];
				if(!$cargo){
					$usuariologuiado = $usu;
				    $c3=new Criteria();
				    $c3->add(CargoUsuarioPeer::USUARIO_ID,$usuariologuiado);
				    $cargoUsuario= CargoUsuarioPeer::doSelectOne($c2);
				    $cargo=$cargoUsuario->getCargousuarioId();
    			}
				
				$objCominternaUsuario->setCargousuarioId($cargo);
				$objCominternaUsuario->save();
				$i++;					
			}		
			
		}
     		
	
	}
  	
  }
  
  
   public function getFirstUsurioFirmaName($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
	$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$firmante = $res->getUsuario()->getNombre()." ".$res->getUsuario()->getApellido();
	  $cont=1;					
	}
	return $firmante;
  	
  }
  public function getFirstUsurioFirmaId($com_internaId)
  { 
  	// consultar firmante inicial
    $c=new Criteria();
	$c->add(CominternaUsuarioPeer::COMINTERNA_ID, $com_internaId);
	$c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID, 2);
    $result = CominternaUsuarioPeer::doSelect($c);    
    $cont=0;
	foreach($result as $res){
		if($cont==0)		
			$firmante = $res->getUsuarioId();
	    $cont=1;					
	}
	return $firmante;  	
  }

	
  
  public function executeRadicar(
  $cominterna_id
  )
  {    
  	   $com_interna = ComInternaPeer::retrieveByPk( $cominterna_id );
       $firmante=$this->getFirstUsurioFirmaId($com_interna->getPrimaryKey());
       
       $objUsuarioFirmante=UsuarioPeer::retrieveByPk($firmante);		
       $regional=$objUsuarioFirmante->getRegionalId();
       $dependencia=$objUsuarioFirmante->getDependenciaId();
        
       $max=$this->getNumeroRadicacion($regional,$dependencia);
       
	   if(!$max) 
           $max=0;
       
	   $com_interna->setNumeroradicacion($max);
	   $max=sprintf("%05d",$max);
	   $regional=sprintf("%02d",$regional);
       $radicado=$regional."-".$objUsuarioFirmante->getDependencia()->getCodigo()."-".$max."-".date("Y")."-I";
       $com_interna->setRadicado($radicado);
       $com_interna->setEstadocominternaid(2);
       $com_interna->setRegionalId($regional); 
	   $com_interna->setFechaCreacion(date("y-m-d h:m:s"));   
       $com_interna->setDependenciaId($dependencia);
       $com_interna->save();
       $i=0;  
  }	
	
  public function getNumeroRadicacion($regional,$dependencia) {		
        $periodoActual=date("Y"); 
		$parametro = ParametroPeer::retrieveByPk(4);
		$formaRad=$parametro->getCodigo();
		
		$conexion = Propel::getConnection();
		if($formaRad=="REG"){
		   $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$regional."  and %s=".$periodoActual." ";
		   $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::REGIONAL_ID,ComInternaPeer::PERIODO_ID);
		}elseif($formaRad=="DEP"){
		   $consulta = "SELECT MAX(%s) AS max FROM %s where %s=".$dependencia."  and %s=".$periodoActual." ";
		   $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::DEPENDENCIA_ID,ComInternaPeer::PERIODO_ID);
				
		}   
		elseif($formaRad=="GEN"){
		   $consulta = "SELECT MAX(%s) AS max FROM %s where  %s=".$periodoActual." ";
		   $consulta = sprintf($consulta, ComInternaPeer::NUMERO_RADICACION, ComInternaPeer::TABLE_NAME,ComInternaPeer::PERIODO_ID);
				
		}
		//************************************************************************************
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($resultset->max + 1);
        //************************************************************************************
	}
	
};





?>