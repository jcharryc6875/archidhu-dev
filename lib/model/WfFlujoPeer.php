<?php

/**
 * Subclass for performing query and update operations on the 'WF_FLUJO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfFlujoPeer extends BaseWfFlujoPeer
{
	static public function getComRecibidaWf()
	{
		$c = new Criteria();
		$c->addJoin(WfFlujoPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);
		$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,1);
		$c->addAscendingOrderByColumn(WfFlujoPeer::DESCRIPCION);
		$rs = WfFlujoPeer::doSelect($c);
		return $rs;
	}
	
	static public function getComInternaWf()
	{
		$c = new Criteria();
		$c->addJoin(WfFlujoPeer::TIPOCOMINTERNA_ID, TipoComInternaPeer::TIPOCOMINTERNA_ID);
		$c->addAscendingOrderByColumn(WfFlujoPeer::DESCRIPCION);
		$rs = WfFlujoPeer::doSelect($c);
		return $rs;
	}
	
	static public function getListFujosOrder($wfbuzon_id=0,$viewall=0)
	{
	    //**********************************************************************************************
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $listall = sfContext::getInstance()->getUser()->checkPerm('LISTAR_FUJOS_TODAS_ENTIDADES', $usuariologuiado);
        //**********************************************************************************************
		$c = new Criteria();
        if(!$listall){
            $listentidad = sfContext::getInstance()->getUser()->checkPerm('LISTAR_FUJOS_SOLO_ENTIDAD', $usuariologuiado);
            $listxreg = sfContext::getInstance()->getUser()->checkPerm('LISTAR_FUJOS_SOLO_REGIONAL', $usuariologuiado);
            if($listentidad){
                $c->addJoin(WfFlujoPeer::REGIONAL_ID, RegionalPeer::REGIONAL_ID);
                $c->add(RegionalPeer::ENTIDAD_ID,$entidad_conectado);
                $c->addOr(WfFlujoPeer::REGIONAL_ID,null,Criteria::ISNULL);
            }elseif($listxreg){
                $c->add(WfFlujoPeer::REGIONAL_ID,$regional_conectado);
                $c->addOr(WfFlujoPeer::REGIONAL_ID,null,Criteria::ISNULL);
            }else{
                $c->add(WfFlujoPeer::REGIONAL_ID,null,Criteria::ISNULL);
            }
        }
		//**********************************************************************************************
        if($wfbuzon_id){
            $c->add(WfFlujoPeer::WF_BUZON_ID,1);
        }
        //**********************************************************************************************
        if(!$viewall){ $c->add(WfFlujoPeer::ESTA_ACTIVO,1); }        
		$c->addAscendingOrderByColumn(WfFlujoPeer::DESCRIPCION);
		$rs = WfFlujoPeer::doSelect($c);
		return $rs;
	}
}
