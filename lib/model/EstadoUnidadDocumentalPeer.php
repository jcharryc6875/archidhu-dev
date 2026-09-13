<?php

/**
 * Subclass for performing query and update operations on the 'ESTADO_UNIDAD_DOCUMENTAL' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EstadoUnidadDocumentalPeer extends BaseEstadoUnidadDocumentalPeer
{
    public static function getEstadoByDesc($descripcion)
	{
		$c = new Criteria();
		$c->add(EstadoUnidadDocumentalPeer::DESCRIPCION, $descripcion);
        $array = EstadoUnidadDocumentalPeer::doSelect($c);
		return $array[0];
	}

	public static function getEstadoAll()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        return EstadoUnidadDocumentalPeer::doSelect($c);
    }
}
