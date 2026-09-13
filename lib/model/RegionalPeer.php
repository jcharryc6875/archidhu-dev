<?php

class RegionalPeer extends BaseRegionalPeer
{	
	static public function getOrdenRegional(){
		$c = RegionalPeer::getCriteriaPerm();
		$c->add(RegionalPeer::ES_VISIBLE,true);
        $c->addAscendingOrderByColumn(RegionalPeer::ENTIDAD_ID);
		$c->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);
		$rs = RegionalPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getRegionalJoinEntidadAsc(){
		$c = RegionalPeer::getCriteriaPerm();
		$c->add(RegionalPeer::ES_VISIBLE,true);
        $c->addAscendingOrderByColumn(RegionalPeer::ENTIDAD_ID);
		$c->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);
		$rs = RegionalPeer::doSelectJoinEntidad($c);
		return $rs; 	
	}
	
    static public function getAllRegional($filterVisible=false){
		$c = new Criteria();
		if($filterVisible){ $c->add(RegionalPeer::ES_VISIBLE,true); }
        $c->addAscendingOrderByColumn(RegionalPeer::ENTIDAD_ID);
		$c->addAscendingOrderByColumn(RegionalPeer::DESCRIPCION);
		$rs = RegionalPeer::doSelect($c);
		return $rs; 	
	}
	
    static public function getCiudadIdByRegional($regional_id)
	{
		$regional = RegionalPeer::retrieveByPK($regional_id);
		return $regional->getCiudadId();
	}
    
    static public function getRegionalJson(){
        $data = array();
        foreach(RegionalPeer::getAllRegional() as $object){
            $data[$object->getPrimaryKey()] = ($object->getDescripcion());
        }        		
		return json_encode($data); 	
	}
	
	static public function getCiudadIdByRegionalName($name_regional)
	{
		if(!trim($name_regional)) { return null; }
		//*******************************************************************
		$c = new Criteria();
		$c->add(RegionalPeer::DESCRIPCION,$name_regional);
		$regional = RegionalPeer::doSelectOne($c);
		return $regional == null ? null : $regional->getCiudadId();
	}
	
	static public function getRegionalByName($name_regional)
	{
		$c = new Criteria();
		$c->add(RegionalPeer::DESCRIPCION,$name_regional);
		$regional = RegionalPeer::doSelectOne($c);
		return $regional == null ? null : $regional;
	}

	static public function getCriteriaPerm(){
		$usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
		$entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
		$regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
		//**********************************************************************************************
		$listallreg = sfContext::getInstance()->getUser()->checkPerm('LISTAR_TODAS_REGIONALES', $usuariologuiado);
		$listregbyent = sfContext::getInstance()->getUser()->checkPerm('LISTAR_REGIONALES_SOLO_ENTIDAD', $usuariologuiado);
		//**********************************************************************************************
		$c = new Criteria();
		//**********************************************************************************************
		if(!$listallreg){
			if(!$listregbyent){
				$c->add(RegionalPeer::REGIONAL_ID,$regional_conectado);
			}else{
				$c->add(RegionalPeer::ENTIDAD_ID,$entidad_conectado);
			}
		}
		//**********************************************************************************************
		return $c;
	}
}
