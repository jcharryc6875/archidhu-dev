<?php

/**
 * passwd actions.
 *
 * @package    simad
 * @subpackage passwd
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class passwdActions extends autopasswdActions
{
public function executeOk()
{
  return sfView::SUCCESS;
}

public function handleErrorIndex()
{
  return sfView::SUCCESS;
}

public function executeIndex()
{
 if ($this->getRequest()->getMethod() != sfRequest::POST)
  {
    // display the form
     $this->getRequest()->setAttribute('referer', $this->getRequest()->getReferer());
     return sfView::SUCCESS;
  }	else{
     //handle form submission...
     //update password.
    $nickname = $this->getRequestParameter('username');
    $c = new Criteria();
    $c->add(UsuarioPeer::USER_NAME, $nickname);
    $user = UsuarioPeer::doSelectOne($c);
	if($user){
		$newpassword=$this->getRequestParameter('newpassword1');
		$this->setSha1Password($user->getUsuarioId(), $newpassword);
		//guarda en historico
		$hist=new HistUsuario();
        $hist->fromArray($user->toArray());
        $hist->setFechaModificacion( date('Y-m-d G:i:s'));
        $hist->save();
        $hist_id=$hist->getHistusuarioId();
        //////
        $usuario_id=$this->getUser()->getAttribute('usuario_id','', 'subscriber');
        $rolhist=new UsUsHistorico();
        $rolhist->setUsuarioId($usuario_id);
        $rolhist->setRolushistoricoid(1);
        $rolhist->setHistusuarioId($hist_id);
        $rolhist->save();
        
        
        
		
	}else{
		echo "Usuario NO encontrado. Error Actualizando.";
	}
	
	
	
	
	//return $this->redirect($this->getRequestParameter('referer','@homepage'));
	  $this->redirect('security/ok');	
  }
}



public function setSha1Password($usuario_id, $password){   
   $user = UsuarioPeer::retrieveByPk($usuario_id);
   if($user){
       $salt= md5(rand(100000,999999).$user->getEmail().$user->getNombre());   	
       $user->setSalt($salt);
       $newpass=sha1($salt.$password);
       $user->setPassword($newpass);
       $user->save();
	   return $newpass;
   }
   return "";      	
}



}
