<?php

/**
 * Subclass for performing query and update operations on the 'PRIORIDAD_SOLICITUD_SERVICIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PrioridadSolicitudServicioPeer extends BasePrioridadSolicitudServicioPeer
{
	public static function getPrioridadServicioEnable()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $criteria = new Criteria();
        $criteria->add(PrioridadSolicitudServicioPeer::ES_VISIBLE,1);        
        return PrioridadSolicitudServicioPeer::doSelect($criteria);
    }

    public static function getPrioridadServicioByName($descripcion)
    {
        $criteria = new Criteria();
		$criteria->add(PrioridadSolicitudServicioPeer::DESCRIPCION,trim($descripcion));
        $object = PrioridadSolicitudServicioPeer::doSelectOne($criteria);
		if($object != null)
			return $object->getPrimaryKey();
		else
			return null;
    }
}
