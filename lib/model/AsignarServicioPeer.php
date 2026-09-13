<?php

/**
 * Subclass for performing query and update operations on the 'ASIGNAR_SERVICIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AsignarServicioPeer extends BaseAsignarServicioPeer
{
    /**
   * servicioActions::getLlaveAsignar()
   *
   * @param mixed $servicio_id
   * @return
   */
    public static function getLlaveAsignar($servicio_id){
        $tempAs = '';
        $a = new Criteria();            
        $a->add(AsignarServicioPeer::SERVICIO_ID,$servicio_id);
        $resp = AsignarServicioPeer::doSelect($a);				        
        $cont = 0;
        foreach ($resp as $val){
            if ($cont == 0)
                $tempAs = $val->getAsignarservicioId();
            $cont = 1;
        }
        return $tempAs;	
    }
}
