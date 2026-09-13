<?php
 
class myPasswdRuleValidator extends sfValidator
{    
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);
 
    // set defaults
    $this->setParameter('login_error', 'Invalid input');
 
    $this->getParameterHolder()->add($parameters);
 
    return true;
  }
   /*
   
   passwordValidator:
    class:         myPasswdRuleValidator
    param:
      long_error:  El password debe tener por lo menos 8 caracteres
      passw_error: La clave anterior no es correcta
      difer_error: Las clave nueva no coincide con su copia
      rules_error: La nueva clave no cumple con las reglas de seguridad
   */
 
 
  public function execute(&$value, &$error)
  {
    
    $login = $value;
    $newpass = $this->getContext()->getRequest()->getParameter('newpassword1');
    $newpass2 = $this->getContext()->getRequest()->getParameter('newpassword2');
    $oldpass = $this->getContext()->getRequest()->getParameter('oldpassword');
    $username= $this->getContext()->getRequest()->getParameter('username');
    
    if ($newpass == ''  || ! isset($newpass) || strlen($newpass)< 8 )
    {
      $error = $this->getParameter('long_error');
      return false;
    }
    if($newpass==$oldpass){
        $error = $this->getParameter('idem_error');
		return false;
	}
    
    if($newpass != $newpass2){
        $error = $this->getParameter('match_error');
		return false;
	}
    
    $c2 = new Criteria();
    $c2->add(ParametroPeer::CODIGO, 'SEG_USAR_EXPRESION_REGULAR');
    $result = ParametroPeer::doSelectOne($c2);
    $usa_regex=0;
    if($result){
       $usa_regex=$result->getValorNumerico();	
	}
	
	$c3 = new Criteria();
    $c3->add(ParametroPeer::CODIGO, 'SEG_USAR_HISTORICO_CLAVES');
    $result = ParametroPeer::doSelectOne($c3);
    $usa_hist=0;
    if($result){
       $usa_hist=$result->getValorNumerico();	
	}
    
    if($usa_regex){
	   if($this->check_rules($newpass)==false){
    	 $error = $this->getParameter('rules_error');
		 return false;
	   }
	}
    
    $c = new Criteria();
    $c->add(UsuarioPeer::USER_NAME, $username);
    $user = UsuarioPeer::doSelectOne($c);
    // user exists?
    $sha1pass="";
    if ($user)
    {
    	$sha1pass=sha1($user->getSalt(). $oldpass );
      // password is OK?
        //echo "<pre>";
        //echo $newpass ."\n";
        //echo $oldpass ."\n";
        //echo $user->getPassword() ."\n";
        //echo $sha1pass ."\n";
        //echo "</pre>";
        if($sha1pass != $user->getPassword()){
	  	  $error = $this->getParameter('passwd_error');
		  return false;
		}
	}
if($usa_hist){	
	
    $conexion=Propel::getConnection();
    $consulta="SELECT  %s, %s FROM %s,%s WHERE %s=%s and %s='%s'";
    $sql=sprintf($consulta,
	HistUsuarioPeer::PASSWORD,
	//HistUsuarioPeer::SALT,
	UsuarioPeer::SALT,
	HistUsuarioPeer::TABLE_NAME,  
	UsuarioPeer::TABLE_NAME, 
	UsuarioPeer::USER_NAME,
	HistUsuarioPeer::USER_NAME, 
	UsuarioPeer::USER_NAME, 
	$username);
    //*************************************************************************
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $resultset = $sentencia->fetch();    
    //*************************************************************************    
    $arrPass="";
    while($object = $resultset->fetch()){
    	$oldpass = $object[0];
		$calpass=sha1($object[1].$newpass );		
		if($oldpass==$calpass){
			 $error = $this->getParameter('hist_error');
		     return false;
		}
		
	}
	
}	
        
    return true;
  }
  
function check_rules($text)
{
	
//Password complexity
//Tests if the input consists of 6 or more letters, digits, underscores and hyphens.
//The input must contain at least one upper case letter, one lower case letter and one digit.
//$regex='\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}\z';

//Password complexity
//Tests if the input consists of 6 or more characters.
//The input must contain at least one upper case letter, one lower case letter and one digit.

$regex='/\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{6,}\z/';	

    $c3 = new Criteria();
    $c3->add(ParametroPeer::CODIGO, 'SEG_EXPRESION_REGULAR');
    $result = ParametroPeer::doSelectOne($c3);
    if($result){
	   //$result->next();
       $regex=$result->getValorTexto();
	   if($regex==""){
	
$regex='/\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{6,}\z/';	
	
	   }	
	}
    if (preg_match($regex, $text)) {
		return true;
	} 
	else {
		return false;
	}
}  




  
}
 
 ?>