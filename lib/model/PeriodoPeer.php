<?php

/**
 * Subclass for performing query and update operations on the 'PERIODO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PeriodoPeer extends BasePeriodoPeer
{
    public static function getListAllPeriodo()
	{
        $data = array();
		$c = new Criteria();
		$c->addDescendingOrderByColumn(PeriodoPeer::PERIODO_ID);
		$list_objects = PeriodoPeer::doSelect($c);
        foreach($list_objects as $object){
			$data[] = array('periodo_id' => $object->getPrimaryKey(), 'text' => ($object->getDescripcion()));
        }
		return ($data); 	
	}
}
