<?php

/**
 * Subclass for performing query and update operations on the 'FORMA_RECEPCION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FormaRecepcionPeer extends BaseFormaRecepcionPeer
{
	static public function getFormaRecepcionOrder($is_ventanilla = false)
    {
		$c = new Criteria();
		$c->add(FormaRecepcionPeer::VENTANILLA_DEFAULT,($is_ventanilla ? 1 : 0));
		$c->add(FormaRecepcionPeer::ES_VISIBLE,1);
		$c->addAscendingOrderByColumn(FormaRecepcionPeer::DESCRIPCION);
		$rs = FormaRecepcionPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getFormaRecepcionWebOrder()
    {
		$c = new Criteria();
		$c->add(FormaRecepcionPeer::VENTANILLA_DEFAULT,0);
		$c->add(FormaRecepcionPeer::ES_VISIBLE,1);
		$c->addAscendingOrderByColumn(FormaRecepcionPeer::DESCRIPCION);
		$rs = FormaRecepcionPeer::doSelect($c);
		return $rs; 	
	}

	static public function getFormaRecepcionOrderAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(FormaRecepcionPeer::DESCRIPCION);
		$rs = FormaRecepcionPeer::doSelect($c);
		return $rs; 	
	}
    
    static public function getFormaRecepcionDefault()
    {
		$c = new Criteria();
		$c->add(FormaRecepcionPeer::VENTANILLA_DEFAULT,true);
		$c->addAscendingOrderByColumn(FormaRecepcionPeer::FORMARECEPCION_ID);
		$rs = FormaRecepcionPeer::doSelectOne($c);
        if($rs->getPrimaryKey()){
            return $rs->getPrimaryKey();
        }else{
            $c = new Criteria();
    		$c->addAscendingOrderByColumn(FormaRecepcionPeer::FORMARECEPCION_ID);
    		$rs = FormaRecepcionPeer::doSelectOne($c);
            return $rs->getPrimaryKey();
        }
	}

	static public function getFormaRecepcionByText($descripcion = null, $isObject = false)
    {
		$c = new Criteria();
		$c->add(FormaRecepcionPeer::DESCRIPCION,$descripcion);
		$rs = FormaRecepcionPeer::doSelectOne($c);
        if($rs != null){
            return $isObject ? $rs : $rs->getPrimaryKey();
        }else{
            return null;
        }
	}
}
