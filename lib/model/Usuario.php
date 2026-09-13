<?php

/**
 * Subclass for representing a row from the 'usuario' table.
 *
 * 
 *
 * @package lib.model
 */

class Usuario extends BaseUsuario
{
	public function __toString(){
		return trim($this->getNombre()." ".$this->getApellido()." - ".$this->getIniciales());
	}
	
	public function getNombreDependencia(){
		return trim($this->getDependencia()->getNombre());
	}
	
	public function getNombreApellido(){
		return ($this->getNombre()." ".$this->getApellido());
	}
	
	public function getNombreAll(){
		return trim($this->getNombre()." ".$this->getApellido()." - ".$this->getIniciales());
	}
	
	public function getFullNombre(){
		return trim($this->getNombre())." ".trim($this->getApellido());
	}
	
	public function getNombreAndDependencia(){
		return trim($this->getNombre()." ".$this->getApellido()." - ".$this->getDependencia()->getNombre());
	}
	
	public function getAllActiveCargos(){
		$cu = new Criteria();
        $cu->add(CargoUsuarioPeer::ES_ACTUAL, true);
        $scargos = array();
        foreach($this->getCargoUsuariosJoinCargo($cu) as $objCargoUsuario){
          $scargos[] = ($objCargoUsuario->getCargo()->getDescripcion());
        }
        //*************************************************************************
        return $scargos;
	}

	/**
	 * Valida si un usuario está activo en Active Directory
	 */
	public function ldapNativoAuth($uac_password) 
	{
		try {
			$config = require_once(sfConfig::get('sf_config_dir'). DIRECTORY_SEPARATOR . 'ldap_settings.php');
			//******************************************************************************************
			$config_controllers = simad_util::readConfigFileApp(['domain_controllers']);
			$domains_controllers = isset($config_controllers['domain_controllers']) ? $config_controllers['domain_controllers'] : null;
			//******************************************************************************************
			if(!empty($domains_controllers)){
				$config['hosts'] = $domains_controllers;
			}
			//******************************************************************************************
			$auth = null;
			try {
				$auth = new AdldapAuth($config);
				if(empty($auth)){
					return array('success' => false, 'message' => 'Error iniciando el proceso de autenticación');
				}
			} catch (\Adldap\AdldapException $th) {
				return array('success' => false, 'message' => $th->getMessage());
			} catch (\Exception $th) {
				return array('success' => false, 'message' => $th->getMessage());
			}
			//******************************************************************************************
			$user_ldap = $auth->findUser($this->getUsuarioAd());
			//******************************************************************************************
			if($user_ldap != null){
				$isActiveUser = $auth->isUserActive($user_ldap);
				if($isActiveUser['activo'] === true){
					$auth_user = $auth->authenticate($this->getUsuarioAd(),$uac_password);
					if($auth_user['success'] === true){
						return $auth_user;
					}else{
						return [
							'success' => false,
							'message' => $auth_user['message'],
							'code' => $auth_user['code']
						];
					}
				}else{
					return [
						'success' => false,
						'message' => $isActiveUser['message'],
						'code' => $isActiveUser['code']
					];
				}
			}else{
				return [
                    'success' => false,
                    'message' => 'El usuario no existe en el directorio activo',
                    'code' => 'USER_LDAP_ERROR_NOTFOUND'
                ];
			}
		} catch (PropelException $th) {
			//throw $th;
		} catch (\Exception $th) {
			//throw $th;
		} catch (\Throwable $th) {
			//throw $th;
		}
	}
}
