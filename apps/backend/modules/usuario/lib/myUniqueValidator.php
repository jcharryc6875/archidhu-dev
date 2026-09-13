<?php
 
class myUniqueValidator extends sfValidator
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

 
  public function execute(&$value, &$error)
  {
    
    $login = $value;
    $username= $this->getContext()->getRequest()->getParameter('usuario_user_name');
    
    if ($username == ''  || ! isset($username) || strlen($username)< 8 )
    {
      $error = $this->getParameter('long_error');
      return false;
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