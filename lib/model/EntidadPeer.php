<?php

/**
 * Subclass for performing query and update operations on the 'ENTIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EntidadPeer extends BaseEntidadPeer
{
    public static function getEntidadByNombre($nombre = null)
    {
		if(empty(trim($nombre))) { return ""; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(EntidadPeer::DESCRIPCION,$nombre);
        $object = EntidadPeer::doSelectOne($c);
        //*******************************************************************
		return $object;
	}

	static public function getEntidadesOrderbyAsc(){
        /************************************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $listalldep = sfContext::getInstance()->getUser()->checkPerm('LISTAR_TODAS_ENTIDADES', $usuariologuiado);
        //**********************************************************************************************
        $c = new Criteria();
        //**********************************************************************************************
        if(!$listalldep){
  		    $c->add(EntidadPeer::ENTIDAD_ID,$entidad_conectado);
        }else{
            $c->addAscendingOrderByColumn(EntidadPeer::DESCRIPCION);
        }
        //**********************************************************************************************
		$rs = EntidadPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getSelectEntidadesOrderbyAsc(){
        /************************************************************************************************/
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $listalldep = sfContext::getInstance()->getUser()->checkPerm('LISTAR_TODAS_ENTIDADES', $usuariologuiado);
        //**********************************************************************************************
        $c = new Criteria();
        //**********************************************************************************************
        if(!$listalldep){
  		    $c->add(EntidadPeer::ENTIDAD_ID,$entidad_conectado);
        }else{
            $c->addAscendingOrderByColumn(EntidadPeer::DESCRIPCION);
        }
        //**********************************************************************************************
		$rs = EntidadPeer::doSelect($c);
		return $rs; 	
	}
}
