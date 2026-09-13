<?php

/**
 * login actions.
 *
 * @package    simad
 * @subpackage login
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class loginActions extends autologinActions
{
public function executeIndex()
{  
  if ($this->getRequest()->getMethod() != sfRequest::POST)
  {
    // display the form
    
    $this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
  }
  else
  {
    // handle the form submission
    $nickname = $this->getRequestParameter('username'); 
    $c = new Criteria();
    $c->add(UsuarioPeer::USER_NAME, $nickname);
    $user = UsuarioPeer::doSelectOne($c);
 
    // user exists?
    if ($user)
    {
       $sha1pass=sha1($user->getSalt(). $this->getRequestParameter('password'));   
      // password is OK?
      if (  $sha1pass  == $user->getPassword()  && $user->getPassword()!="" )
      //if(true)
      {
        $this->getUser()->setAuthenticated(true);
        $this->getUser()->addCredential('subscriber');
        $x=$user->getUsuarioId();
        //fija credencial admin
		 
        $c2= new Criteria();
        $c2->add(RolPorUsuarioPeer::USUARIO_ID,$x);
        $p1 = RolPorUsuarioPeer::doSelectOne($c2);
        if($p1){
		   $p1num=$p1->getRolId();
           if($p1num==1){
		    $this->getUser()->addCredential('admin'); 	
		   }
        }
        
 
        $this->getUser()->setAttribute('usuario_id', $user->getUsuarioId(), 'subscriber');
        $this->getUser()->setAttribute('username',   $user->getUserName(),  'subscriber');
 
        //pide cambio de clave si la fecha de modificacion es muy antigua.
        $fecha   = $user->getFechaCreacion();
        $c1= new Criteria();
        $c1->add(ParametroPeer::CODIGO,'SEG_PERIODO_CAMBIO_CLAVE');
        $p1 = ParametroPeer::doSelectOne($c1);
        $periodo= $p1->getValorNumerico();
        if(! $pnum) $pnum=60;
        
        

        
        if($fecha<( date()-$periodo) ){
            //obliga a cambiar.
            return $this->redirect($this->getRequestParameter('referer', 'passwd'));
        }
        // redirect to last page
        return $this->redirect($this->getRequestParameter('referer', '@homepage'));
      }
	  //else{	
	  //  $this->handleErrorLogin();		
	  //}
    }
  }

}

}
