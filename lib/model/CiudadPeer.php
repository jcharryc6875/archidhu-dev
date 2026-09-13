<?php

class CiudadPeer extends BaseCiudadPeer
{
	
	static public function getAllCiudad(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(DepartamentoPeer::NOMBRE);
		$c->addAscendingOrderByColumn(CiudadPeer::NOMBRE);
		$rs = CiudadPeer::doSelectJoinDepartamento($c);
		return $rs; 	
	}
	

    static public function getCiudadByDepartamento($departamento_id = 0){
		$c = new Criteria();
        $c->add(CiudadPeer::DEPARTAMENTO_ID,$departamento_id);
		$c->addAscendingOrderByColumn(CiudadPeer::NOMBRE);
		$rs = CiudadPeer::doSelect($c);
		return $rs;
	}
    
	static public function getCiudadByNombAndCod($nombre = null, $codigo = null ,$byObject = true){
		$codigo_dane = ltrim($codigo, "0");
		//***********************************************************************************
		$c = new Criteria();
		if(trim($nombre)){ $c->add(CiudadPeer::NOMBRE,$nombre); }
        if(trim($codigo_dane)){ $c->add(CiudadPeer::CODIGO_DANE,$codigo_dane); }
		//***********************************************************************************
		$ciudad = CiudadPeer::doSelectOne($c);
		//***********************************************************************************
		if($ciudad == null){ return null; }
		//***********************************************************************************
		return $byObject ? $ciudad : $ciudad->getPrimaryKey();
	}

	static public function getCiudadByCod($codigo = null ,$byObject = true){
		$codigo_dane = ltrim($codigo, "0");
		//***********************************************************************************
		$c = new Criteria();
        if(trim($codigo_dane)){ $c->add(CiudadPeer::CODIGO_DANE,$codigo_dane); }
		//***********************************************************************************
		$ciudad = CiudadPeer::doSelectOne($c);
		//***********************************************************************************
		if($ciudad == null){ return null; }
		//***********************************************************************************
		return $byObject ? $ciudad : $ciudad->getPrimaryKey();
	}

    static public function getCiudadesJson($departamento_id = 0){
        $data = array();
        foreach(CiudadPeer::getCiudadByDepartamento($departamento_id) as $object){
            $data[$object->getPrimaryKey()] = ($object->getNombre());
        }
		return json_encode($data); 	
	}

	static public function getAllCiudadTree(){
		$c = new Criteria();
		$c->addJoin(CiudadPeer::DEPARTAMENTO_ID,DepartamentoPeer::DEPARTAMENTO_ID,Criteria::INNER_JOIN);
		$c->addJoin(DepartamentoPeer::PAIS_ID,PaisPeer::PAIS_ID,Criteria::INNER_JOIN);
		$c->clearSelectColumns();
		//********************************************************************************************
		CiudadPeer::addSelectColumns($c);
		DepartamentoPeer::addSelectColumns($c);
		PaisPeer::addSelectColumns($c);
		//********************************************************************************************
		$c->addAscendingOrderByColumn(PaisPeer::NOMBRE);
		$c->addAscendingOrderByColumn(DepartamentoPeer::NOMBRE);
		$c->addAscendingOrderByColumn(CiudadPeer::NOMBRE);
		$rs = CiudadPeer::doSelectStmt($c);
		//********************************************************************************************
		$list = array();
		$list_pais = array();$list_depar = array();$list_ciudad = array();
		while($object = $rs->fetch()){
			$item = array();
			
			$item['CIUDAD_ID'] = $object[0];
			$item['NOMBRE_CIUDAD'] = $object[2];
			$item['CODANE_CIUDAD'] = $object[3];

			$item['NOMBRE_DEPARTAMENTO'] = $object[6];
			$item['DEPARTAMENTO_ID'] = $object[1];
			$item['CODANE_DEPARTAMENTO'] = $object[7];

			$item['NOMBRE_PAIS'] = $object[9];
			$item['PAIS_ID'] = $object[8];
			$item['PAIS_NUMCOD'] = $object[10];
			$item['PAIS_ISOCOD'] = $object[11];

			$list[] = $item;
		}
		//********************************************************************************************
		return $list;
	}
}
