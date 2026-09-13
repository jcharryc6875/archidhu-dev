<?php

/**
 * Subclass for performing query and update operations on the 'TIPO_SERVICIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoServicioPeer extends BaseTipoServicioPeer
{
	public static function getTipoServicioEnable()
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
		$listall = sfContext::getInstance()->getUser()->checkPerm('TIPO_SERVICIO_LISTAR_TODAS_ENTIDADES', $usuariologuiado);
		//**********************************************************************************************
        $criteria = new Criteria();
		//**********************************************************************************************
		if(!$listall){
			$criteria->add(TipoServicioPeer::ENTIDAD_ID,$entidad_conectado);
			$cton0 = $criteria->getNewCriterion(TipoServicioPeer::ENTIDAD_ID,$entidad_conectado);
			$cton1 = $criteria->getNewCriterion(TipoServicioPeer::ENTIDAD_ID,null,Criteria::ISNULL);  	
			$cton0->addOr($cton1);
			$criteria->add($cton0);
		}
		//**********************************************************************************************
        $criteria->add(TipoServicioPeer::ES_VISIBLE,1);
        return TipoServicioPeer::doSelect($criteria);
    }

	public static function getTipoServicioByTipoEnvioIntegra($tipo_envio = 0)
    {
        $criteria = new Criteria();
		$criteria->add(TipoServicioPeer::TIPO_ENVIO,$tipo_envio);
		$criteria->add(TipoServicioPeer::INIT_INTEGRACION,1);
        $tipo_servicio = TipoServicioPeer::doSelectOne($criteria);
		if($tipo_servicio != null)
			return $tipo_servicio->getPrimaryKey();
		else
			return 0;
    }

	public static function getTipoServicioByName($descripcion)
    {
        $criteria = new Criteria();
		$criteria->add(TipoServicioPeer::DESCRIPCION,trim($descripcion));
        $tipo_servicio = TipoServicioPeer::doSelectOne($criteria);
		if($tipo_servicio != null)
			return $tipo_servicio->getPrimaryKey();
		else
			return 0;
    }

	public static function getTipoServicioObjByName($descripcion)
    {
        $c = new Criteria();
		$c->add(TipoServicioPeer::DESCRIPCION,trim($descripcion));
        $tipo_servicio = TipoServicioPeer::doSelectOne($c);
		if($tipo_servicio != null)
			return $tipo_servicio;
		else
			return null;
    }
}
