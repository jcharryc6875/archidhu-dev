<?php

/**
 * Subclass for performing query and update operations on the 'PAIS' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PaisPeer extends BasePaisPeer
{
    static public function getPaisesAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(PaisPeer::NOMBRE);
		$rs = PaisPeer::doSelect($c);
		return $rs; 	
	}
    
    static public function getPaisesJson(){
        $data = array();
        foreach(PaisPeer::getPaisesAll() as $object){
            $data[$object->getPrimaryKey()] = utf8_encode($object->getNombre());
        }
		return json_encode($data); 	
	}
}
