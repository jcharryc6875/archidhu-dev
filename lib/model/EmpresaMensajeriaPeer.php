<?php

/**
 * Subclass for performing query and update operations on the 'EMPRESA_MENSAJERIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EmpresaMensajeriaPeer extends BaseEmpresaMensajeriaPeer
{
    static public function getEmpresaMensajeriaOrderAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(EmpresaMensajeriaPeer::NOMBRE);
		$rs = EmpresaMensajeriaPeer::doSelect($c);
		return $rs; 	
	}

	static public function getEmpresaMensajeriaByName($nombre = null)
    {
		if(!trim($nombre)){ return -1; }
		//*****************************************************************************
		$c = new Criteria();
		$c->add(EmpresaMensajeriaPeer::NOMBRE,$nombre);
		$rs = EmpresaMensajeriaPeer::doSelectOne($c);		
		return $rs != null ? $rs : 0; 	
	}

	static public function getEmpresaMensajeriaBySigle($sigla = null)
    {
		if(!trim($sigla)){ return -1; }
		//*****************************************************************************
		$c = new Criteria();
		$c->add(EmpresaMensajeriaPeer::ABREVIATURA,$sigla);
		$rs = EmpresaMensajeriaPeer::doSelectOne($c);		
		return $rs != null ? $rs : 0; 	
	}

	static public function getCourrierListName()
    {	
		$data = array();
        foreach(EmpresaMensajeriaPeer::getEmpresaMensajeriaOrderAll() as $object){
            $data[] = ($object->getNombre());
        }
		return ($data);
	}
}
