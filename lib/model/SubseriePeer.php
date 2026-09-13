<?php

/**
 * Subclass for performing query and update operations on the 'subserie' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SubseriePeer extends BaseSubseriePeer
{
	public static function getSubserieByCodigoAndDependencia($el_codigo, $la_dependencia)
	{
		$c = new Criteria();
		$c->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
		$c->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
		$c->add(SubseriePeer::CODIGO, $el_codigo);		
		$c->add(DependenciaPeer::CODIGO, $la_dependencia);		
		return SubseriePeer::doSelectOne($c); 	
	}

    public static function getAllSubseries()
	{
		$c = new Criteria();	
		return SubseriePeer::doSelect($c); 	
	}

	public static function getOrdenSubserie()
	{
		$c = new Criteria();
		$c->addAscendingOrderByColumn(SubseriePeer::DESCRIPCION);		
		$rs = SubseriePeer::doSelect($c);
		return $rs; 	
	}
	
	public static function guardarAuditoria($registro_anterior,$registro_nuevo)
    {
        try{ 
            $campos_objeto = SubseriePeer::getFieldNames();
            $valor_anterior = array();
            $valor_nuevo = array();
			//**************************************************************************************
            foreach($campos_objeto as $field){
                $instanceMethod = 'get'.$field;
				$valor_anterior[] = !empty($registro_anterior) ? $registro_anterior->$instanceMethod() : null;
                $valor_nuevo[] = $registro_nuevo->$instanceMethod();
            }
            //**************************************************************************************
            sfContext::getInstance()->getUser()->guardarAuditoria(1,$campos_objeto,$valor_anterior,$valor_nuevo,$registro_nuevo->getCodigo());
        }catch (Exception $ex){       
            $msg_error = $ex->getMessage();       
        }
    }

    public static function getSubserieByNombreAndCodigo($codigo = null, $nombre = null)
    {
		if(empty(trim($codigo)) || empty(trim($nombre))) { return ""; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(SubseriePeer::CODIGO,$codigo);
        $c->add(SubseriePeer::DESCRIPCION,$nombre);
        $object = SubseriePeer::doSelectOne($c);
        //*******************************************************************
		return $object;
	}

    public static function getSubserieByCodigo($codigo = null)
    {
		if(empty(trim($codigo))) { return null; }
		//*******************************************************************
		$c = new Criteria();
        $c->add(SubseriePeer::CODIGO,trim($codigo));
        return SubseriePeer::doSelectOne($c);
	}

    public static function getAllSubseriesActive($es_visible = 1)
	{
		$c = new Criteria();
		$c->addJoin(SubseriePeer::SERIE_ID, SeriePeer::SERIE_ID);
		$c->addJoin(SeriePeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
		//********************************************************************************
		$c->add(DependenciaPeer::ES_ACTUAL,$es_visible);
		$c->add(SeriePeer::ES_VISIBLE,$es_visible);
		$c->add(SubseriePeer::ES_VISIBLE,$es_visible);
		//********************************************************************************
		$c->addAscendingOrderByColumn(DependenciaPeer::CODIGO);
		$c->addAscendingOrderByColumn(SeriePeer::CODIGO);
		$c->addAscendingOrderByColumn(SubseriePeer::CODIGO);
		//********************************************************************************
		return SubseriePeer::doSelect($c); 	
	}
}
