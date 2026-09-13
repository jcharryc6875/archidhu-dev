<?php

/**
 * Subclass for performing query and update operations on the 'SERVICIO_ZONA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ServicioZonaPeer extends BaseServicioZonaPeer
{
    public static function getServicioZonaAll()
    {
        $criteria = new Criteria();
        //$criteria->add(ServicioZonaPeer::ES_VISIBLE,1);
        $criteria->addAscendingOrderByColumn(ServicioZonaPeer::DESCRIPCION);        
        return ServicioZonaPeer::doSelect($criteria);
    }
    
    public static function getServicioZonaList()
    {
        $criteria = new Criteria();
        $criteria->add(ServicioZonaPeer::ES_VISIBLE,1);
        $criteria->addAscendingOrderByColumn(ServicioZonaPeer::DESCRIPCION);        
        return ServicioZonaPeer::doSelect($criteria);
    }
    
}
