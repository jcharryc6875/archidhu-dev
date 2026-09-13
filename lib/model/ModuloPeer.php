<?php

class ModuloPeer extends BaseModuloPeer
{
	public static function getOrdenModulo(){
		$c = new Criteria();
		$c->addAscendingOrderByColumn(ModuloPeer::DESCRIPCION);
		$rs = ModuloPeer::doSelect($c);
		return $rs; 	
	}
    
    public static function getModuloFormas(){
		$c = new Criteria();
        $c->add(ModuloPeer::MODULO_ID,11,Criteria::NOT_EQUAL);
		$c->addAscendingOrderByColumn(ModuloPeer::DESCRIPCION);
		$rs = ModuloPeer::doSelect($c);
		return $rs; 	
	}
}
