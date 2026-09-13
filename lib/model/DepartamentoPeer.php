<?php

/**
 * Subclass for performing query and update operations on the 'DEPARTAMENTO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DepartamentoPeer extends BaseDepartamentoPeer
{
    static public function getOrderDepartamento(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(DepartamentoPeer::NOMBRE);
		$rs = DepartamentoPeer::doSelect($c);
		return $rs;
	}
    
    static public function getDepartamentoByPais($pais_id = 0){
		$c = new Criteria();
        $c->add(DepartamentoPeer::PAIS_ID,$pais_id);
		$c->addAscendingOrderByColumn(DepartamentoPeer::NOMBRE);
		$rs = DepartamentoPeer::doSelect($c);
		return $rs;
	}
    
    static public function getDepartamentosJson($pais_id = 0){
        $data = array();
        foreach(DepartamentoPeer::getDepartamentoByPais($pais_id) as $object){
            $data[$object->getPrimaryKey()] = ($object->getNombre());
        }
		return json_encode($data); 	
	}
}
