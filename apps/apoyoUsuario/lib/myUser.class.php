<?php

class myUser extends sfBasicSecurityUser
{
	
 public function guardarAuditoria($modulo,$campos,$valor_anterior,$valor_nuevo,$codigo_principal)
  {
    $audit_log = new AuditLog();
    $audit_log->setUsuarioId($this->getUsuarioId());
    $audit_log->setModuloId($modulo);
    //$campos=array_keys($valor_anterior);  
    
	foreach($campos as $campo){
    	 $campos_string.=$campo."|";		
	}
    foreach($valor_anterior as $valor){
    	 $valor_anterior_string.=$valor."|";		
	}
	foreach($valor_nuevo as $valor){
    	 $valor_nuevo_string.=$valor."|";		
	}
    
    $audit_log->setCampos($campos_string);
	$audit_log->setValorAnterior($valor_anterior_string);
    $audit_log->setValorNuevo($valor_nuevo_string);
    $audit_log->setFechaCreacion(date("Y-m-d h:m:s"));
    $audit_log->setCodigoPrincipal($codigo_principal);
    $audit_log->save();    
  }

	
	public function getUsuarioId(){
	    return $this->getAttribute('usuario_id','', 'subscriber');	
	}
	public function getAuthorization($nombre_forma, $usuario_id){
		if(  $this->checkPerm($nombre_forma, $usuario_id) == true ){
		    return "OK";	
		}else{
			return "NO TIENE ACCESO";
		}
		//return "OK";
	}
	
	public function checkPerm($nombre_forma , $usuario_id=""){
		$nudebug=1;
		//revisa si tiene permiso para la forma.
		if($usuario_id==""){
		     $usuario_id=$this->getAttribute('usuario_id','', 'subscriber');	
		}else{
			 if($usuario_id * 1 == 0 ){
    			  //obtiene el usuario_id a partir del username
    		      $cu = new Criteria();
                  $cu->add(UsuarioPeer::USER_NAME, $usuario_id);
                  
                  $rsresult = UsuarioPeer::doSelectOne($cu);
                  if($rsresult){
    			      $usuario_id= $rsresult->getUsuarioId();	
    			  }
			 }
		}
		//permisos de usuario
        $c = new Criteria();
        $c->add(UsuarioPrivilegioPeer::USUARIO_ID, $usuario_id);
        $c->addJoin(UsuarioPrivilegioPeer::FORMA_ID, FormaPeer::FORMA_ID);
        $c->add(FormaPeer::NOMBRE,$nombre_forma);
        if($nudebug){
		  $cuenta=UsuarioPrivilegioPeer::doCount($c);			
		}
        //*************************************************************************************
        $permission = UsuarioPrivilegioPeer::doSelectOne($c);
        if($permission){
			return true;
		}else{
			//revise permisos de roles.
		    $c = new Criteria();
            $c->add(RolPorUsuarioPeer::USUARIO_ID, $usuario_id);
            $c->addJoin(RolPorUsuarioPeer::ROL_ID, RolPrivilegioPeer::ROL_ID);
            $c->addJoin(RolPrivilegioPeer::FORMA_ID, FormaPeer::FORMA_ID);
            $c->add(FormaPeer::NOMBRE,$nombre_forma);
            $permission2 = RolPorUsuarioPeer::doSelectOne($c);
        	if($permission2){
				return true;
			}
		}
        return false;
	}
}

