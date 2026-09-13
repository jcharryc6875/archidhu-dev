<?php

/**
 * Subclass for performing query and update operations on the 'FACTURA_VITACORA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaVitacoraPeer extends BaseFacturaVitacoraPeer
{
    public static function getLastVitacoraActive(Factura $factura){
        $c = new Criteria();
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura->getPrimaryKey());
        $c->add(FacturaVitacoraPeer::EJECUTADA,0);
        $c->add(FacturaVitacoraPeer::FACTURAESTADO_ID,$factura->getFacturaestadoId());
        $factura_vitacora = FacturaVitacoraPeer::doSelectOne($c);
        if($factura_vitacora != null){
            return $factura_vitacora;
        }else{
            return null;
        }
    }
}
