<?php
 
class myRecuperarValidator extends sfValidator
{    
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);
 
    // set defaults
    $this->setParameter('login_error', 'Usuario NO encontrado');
 
    $this->getParameterHolder()->add($parameters);
 
    return true;
  }
 
  public function execute(&$value, &$error)
  {
    
    $email = $this->getContext()->getRequest()->getParameter('email');
    $login = $value;
 
    // anonymous is not a real user
    if ($login == 'anonymous')
    {
      $error = $this->getParameter('login_error');
      return false;
    }
 
    $c = new Criteria();
    $c->add(UsuarioPeer::USER_NAME, $login);
    $user = UsuarioPeer::doSelectOne($c);
    
    if ($user)
    {  
	  if( strstr($email,"@")  ){ 
	    // password is OK?
        //if (sha1($user->getSalt().$password) == $user->getSha1Password())
        if($email==$user->getEmail() )
        {
      	  //Everything is OK
          return true;
        }else{
	      $error = $this->getParameter('base_error');
          return false;
	    }
      }else{
		  $error = $this->getParameter('email_error');
          return false;    
	  }
    }
    $error = $this->getParameter('login_error');
    return false;
  }
}
 
 ?>