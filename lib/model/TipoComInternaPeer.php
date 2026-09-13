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

	static public function getTipoComInternaShow()
	{
		$c = new Criteria();
		$c->add(TipoComInternaPeer::ES_VISIBLE,true);
		$c->addAscendingOrderByColumn(TipoComInternaPeer::DESCRIPCION);		
		$rs = TipoComInternaPeer::doSelect($c);
		return $rs;
	}
}
