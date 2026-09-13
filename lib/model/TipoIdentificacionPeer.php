<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_IDENTIFICACION' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoIdentificacionPeer extends BaseTipoIdentificacionPeer
{
    static public function getTipoIdentificacionAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(TipoIdentificacionPeer::DESCRIPCION);
		$rs = TipoIdentificacionPeer::doSelect($c);
		return $rs; 	
	}
    
    static public function getTipoIdentificacionJson(){
        $data = array();
        foreach(TipoIdentificacionPeer::getTipoIdentificacionAll() as $object){
            $data[$object->getPrimaryKey()] = ($object->getDescripcion());
        }
		return json_encode($data); 	
	}

    static public function getTipoIdentificacionList(){
        $data = array();
        foreach(TipoIdentificacionPeer::getTipoIdentificacionAll() as $object){
            $data[] = ($object->getSigla());
        }
		return ($data);
	}

    static public function getTipoIdentificacionPkBySigla($sigla){
        $c = new Criteria();
		$c->add(TipoIdentificacionPeer::SIGLA,$sigla);
		$rs = TipoIdentificacionPeer::doSelectOne($c);
		return $rs != null ? $rs->getPrimaryKey() : null;	
	}

	static public function getTipoIdentificacionPkByName($nombre){
        $c = new Criteria();
		$c->add(TipoIdentificacionPeer::DESCRIPCION,$nombre);
		$rs = TipoIdentificacionPeer::doSelectOne($c);
		return $rs != null ? $rs->getPrimaryKey() : null;	
	}

    static public function getTipoIdentificacionPkByCodigo($codigo){
        $c = new Criteria();
		$c->add(TipoIdentificacionPeer::CODIGO,$codigo);
		$rs = TipoIdentificacionPeer::doSelectOne($c);
		return $rs != null ? $rs->getPrimaryKey() : null;
	}

	static public function getTipoIdentificacionPkById($idtipo){
        $c = new Criteria();
		$c->add(TipoIdentificacionPeer::TIPOIDENTIFICACION_ID,$idtipo);
		$rs = TipoIdentificacionPeer::doSelectOne($c);
		return $rs != null ? $rs->getPrimaryKey() : null;
	}
}
