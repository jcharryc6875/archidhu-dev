<?php

/**
 * Subclass for performing query and update operations on the 'AUTORIZACION_FIRMA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AutorizacionFirmaPeer extends BaseAutorizacionFirmaPeer
{
	public static function validateFirmaElectronica($autorized_user, $str_firmausers, $modulo_id = 0)
	{
	    $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $valid_firma = 1;
        //****************************************************************************************
		$usuarios_firma = preg_split("/[,]+/",trim($str_firmausers), -1, PREG_SPLIT_NO_EMPTY);
		//****************************************************************************************
		if(count($usuarios_firma) == 1){
			if($usuarios_firma[0] == $usuariologuiado) { return 1; }
		}
		//****************************************************************************************
		foreach($usuarios_firma as $user){
			if($usuariologuiado != $user){
				$usuario = UsuarioPeer::retrieveByPK($user);
				if($usuario->getUseFirmaElectronica() && !$usuario->getFirmaDesatendida()){
					$c = new Criteria();
					$c->addJoin(AutorizacionFirmaPeer::AUTORIZACIONFIRMA_ID,AutFirmaUsuarioPeer::AUTORIZACIONFIRMA_ID);
					$c->add(AutorizacionFirmaPeer::USUARIO_ID,$user);
					$c->add(AutorizacionFirmaPeer::MODULO_ID,$modulo_id);
					$c->add(AutorizacionFirmaPeer::FIRMA_ELECTRONICA,1);
					$c->add(AutorizacionFirmaPeer::ESTADOFIRMAAUTO_ID,1);
					$c->add(AutFirmaUsuarioPeer::ROLFIRMAUSUARIO_ID,2);//rol autorizado
					$c->add(AutFirmaUsuarioPeer::USUARIO_ID,$autorized_user);
					$result = AutorizacionFirmaPeer::doCount($c);
					//********************************************************************************
					if($result == 0){ return 0; }
				}
			}
		}
        //****************************************************************************************
        return $valid_firma;
	}
}
