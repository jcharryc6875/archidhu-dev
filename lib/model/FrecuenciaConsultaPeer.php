<?php

/**
 * Subclass for performing query and update operations on the 'FRECUENCIA_CONSULTA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FrecuenciaConsultaPeer extends BaseFrecuenciaConsultaPeer
{
    public static function getFrecuenciaByDesc($descripcion)
	{
		$c = new Criteria();
		$c->add(FrecuenciaConsultaPeer::DESCRIPCION, $descripcion);
        return FrecuenciaConsultaPeer::doSelectOne($c);
	}

	public static function getFrecuenciaAll()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        return FrecuenciaConsultaPeer::doSelect($c);
    }
}
