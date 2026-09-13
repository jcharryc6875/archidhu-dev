<?php
 
class myPasswdValidator extends sfValidator
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
    
    $password1 = $this->getContext()->getRequest()->getParameter('newpassword1');
    $password2 = $this->getContext()->getRequest()->getParameter('newpassword2');
    $password = $this->getContext()->getRequest()->getParameter('oldpassword');
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
	  if($password1==$password2){ 
	  // password is OK?
      //if (sha1($user->getSalt().$password) == $user->getSha1Password())
      if($password==$user->getPassword() )
      {
      	//Everything is OK
        return true;
      }else{
	    $error = $this->getParameter('passw_error');
        return false;
	  }
      }else{
        $error = $this->getParameter('difer_error');
        return false;
	  }
    }
    $error = $this->getParameter('login_error');
    return false;
  }
}
 
 ?>