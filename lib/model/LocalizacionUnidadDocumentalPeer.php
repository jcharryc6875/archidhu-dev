<?php

/**
 * Subclass for performing query and update operations on the 'LOCALIZACION_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class LocalizacionUnidadDocumentalPeer extends BaseLocalizacionUnidadDocumentalPeer
{
    public static function getLacalizacionAll()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        return LocalizacionUnidadDocumentalPeer::doSelect($c);
    }
	
	public static function getLocalizacionByDesc($descripcion)
	{
		$c = new Criteria();
		$c->add(LocalizacionUnidadDocumentalPeer::DESCRIPCION, $descripcion);
        $array = LocalizacionUnidadDocumentalPeer::doSelect($c);
		return $array[0];
	}
}
