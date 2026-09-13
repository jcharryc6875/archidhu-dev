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
    
    if($this->check_rules($newpass)==false){
    	 $error = $this->getParameter('rules_error');
		return false;
	}
 
    $c = new Criteria();
    $c->add(UsuarioPeer::USER_NAME, $login);
    $user = UsuarioPeer::doSelectOne($c);
    
    
    if ($user)
    {  
	  //iterate trough password history
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
	
      if (preg_match($regex, $text)) {
		return true;
	} 
	else {
		return false;
	}
}  
  
}
 
 ?>