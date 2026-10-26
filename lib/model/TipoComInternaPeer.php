<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_COM_INTERNA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoComInternaPeer extends BaseTipoComInternaPeer
{
	static public function getTipoComInternaOrden()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(TipoComInternaPeer::DESCRIPCION);		
		$rs = TipoComInternaPeer::doSelect($c);
		return $rs;
	}

	/**
	 * Ids de los tipos de comunicacion cuya plantilla no tiene habilitado el editor de texto.
	 */
	static public function getIdsSinEditor()
	{
		$c = new Criteria();
		$c->addJoin(TipoComInternaPeer::PLANTILLASCOM_ID, PlantillasComPeer::PLANTILLASCOM_ID, Criteria::INNER_JOIN);
		$c->add(PlantillasComPeer::USA_EDITOR, 0);
		$ids = array();
		foreach(TipoComInternaPeer::doSelect($c) as $tipo){
			$ids[] = (int)$tipo->getPrimaryKey();
		}
		return $ids;
	}

	static public function getTipoComInternaShow()
	{
		$c = new Criteria();
		$c->add(TipoComInternaPeer::ES_VISIBLE,true);
		$c->addAscendingOrderByColumn(TipoComInternaPeer::DESCRIPCION);		
		$rs = TipoComInternaPeer::doSelect($c);
		return $rs;
	}
}
