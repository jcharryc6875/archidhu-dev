<?php

/**
 * pwdset actions.
 *
 * @package    simad
 * @subpackage pwdset
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class pwdsetActions extends autopwdsetActions
{
	

  protected function updateUsuarioFromRequest()
  {
    $usuario = $this->getRequestParameter('usuario');

    if (isset($usuario['password']))
    {
      //calcula el nuevo password. 	
      $usuario_id=$this->getRequestParameter('usuario_id');
      $clave=$usuario['password'];
      $crypted="CRYPTED";
      $salt="SALT";
	  $this->setSha1Password($usuario_id,$clave,$crypted, $salt);
      $this->usuario->setPassword($crypted); 
      $this->usuario->setSalt($salt); 
    }
    
  }

   public function setSha1Password($usuario_id, $password, & $crypted, & $salt){   
       $user = UsuarioPeer::retrieveByPk($usuario_id);
       if($user){
          $salt= md5(rand(100000,999999).$user->getEmail().$user->getNombre());    
          $crypted=sha1($salt.$password);
          return $crypted;
        }
        return "";      	
   }

	
	
}
