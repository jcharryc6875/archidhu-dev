<?php

/**
 * Subclass for performing query and update operations on the 'UNIDAD_CONSERVADORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class UnidadConservadoraPeer extends BaseUnidadConservadoraPeer
{
    public static function getUnidadByDesc($descripcion)
	{
		$c = new Criteria();
		$c->add(UnidadConservadoraPeer::DESCRIPCION, trim($descripcion));
        return UnidadConservadoraPeer::doSelectOne($c);
	}

	public static function getAllConservadoras()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        return UnidadConservadoraPeer::doSelect($c);
    }
}
