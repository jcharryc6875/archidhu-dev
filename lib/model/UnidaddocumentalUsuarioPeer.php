<?php

/**
 * Subclass for performing query and update operations on the 'UNIDADDOCUMENTAL_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UnidaddocumentalUsuarioPeer extends BaseUnidaddocumentalUsuarioPeer
{
    public static function executeUserUniDocRole($rolunidaddoc_id, $usuario_id, $unidaddocumental_id, $usunidaddoc_id = null)
	{  	
		if($usunidaddoc_id)
        {
			$uniDocUsu = UnidaddocumentalUsuarioPeer::retrieveByPk($usunidaddoc_id);
		}
        else
        {
			$uniDocUsu = new UnidaddocumentalUsuario();
		}
		//***********************************************************************************************
		$uniDocUsu->setUsuarioId($usuario_id);
		$uniDocUsu->setRolusuunidaddocId($rolunidaddoc_id);
		$uniDocUsu->setUnidaddocumentalId($unidaddocumental_id);
		$uniDocUsu->save();	
	}
}
