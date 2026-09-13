<?php

/**
 * Subclass for performing query and update operations on the 'SOPORTE_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SoporteUnidadDocumentalPeer extends BaseSoporteUnidadDocumentalPeer
{
    public static function getSoporteUnidadDocDesp($la_descripcion)
	{
		$c = new Criteria();
		$c->add(SoporteUnidadDocumentalPeer::DESCRIPCION, $la_descripcion);
		return SoporteUnidadDocumentalPeer::doSelectOne($c);
	}

	public static function getSoporteAll()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        return SoporteUnidadDocumentalPeer::doSelect($c);
    }
}
