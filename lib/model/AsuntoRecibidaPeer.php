<?php

/**
 * Subclass for performing query and update operations on the 'asunto_recibida' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AsuntoRecibidaPeer extends BaseAsuntoRecibidaPeer
{
	
	static public function getAsuntoRecibidaOrdenado(){
    	$c = new Criteria();
        $c->add(AsuntoRecibidaPeer::VENTANILLA_DEFAULT,0);
		//$c->add(AsuntoRecibidaPeer::ES_VISIBLE,1);
    	$c->addAscendingOrderByColumn(AsuntoRecibidaPeer::DESCRIPCION);        
    	$rs = AsuntoRecibidaPeer::doSelect($c);
    	return $rs; 	
	}
    
    static public function getAsuntoRecibidaOrdenadoFilter($is_ventanilla = 0){
    	$c = new Criteria();
        $c->add(AsuntoRecibidaPeer::VENTANILLA_DEFAULT,$is_ventanilla);
		//$c->add(AsuntoRecibidaPeer::ES_VISIBLE,1);
    	$c->addAscendingOrderByColumn(AsuntoRecibidaPeer::DESCRIPCION);        
    	$rs = AsuntoRecibidaPeer::doSelect($c);
    	return $rs; 	
	}
    
    static public function getAsuntoRecibidaOrderAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(AsuntoRecibidaPeer::DESCRIPCION);
		$rs = AsuntoRecibidaPeer::doSelect($c);
		return $rs; 	
	}
    
    static public function getAsuntoRecibidaDefault()
    {
		$c = new Criteria();
		$c->add(AsuntoRecibidaPeer::VENTANILLA_DEFAULT,1);
		//$c->add(AsuntoRecibidaPeer::ES_VISIBLE,1);
		$c->addAscendingOrderByColumn(AsuntoRecibidaPeer::ASUNTORECIBIDA_ID);
		$rs = AsuntoRecibidaPeer::doSelectOne($c);
        if($rs->getPrimaryKey()){
            return $rs->getPrimaryKey();
        }else{
            $c = new Criteria();
    		$c->addAscendingOrderByColumn(AsuntoRecibidaPeer::ASUNTORECIBIDA_ID);
    		$rs = AsuntoRecibidaPeer::doSelectOne($c);
            return $rs->getPrimaryKey();
        }
	}
	
}
