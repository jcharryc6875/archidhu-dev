<?php

/**
 * Subclass for performing query and update operations on the 'tipo_com_recibida' table.
 *
 * 
 *
 * @package lib.model
 */ 
class TipoComRecibidaPeer extends BaseTipoComRecibidaPeer
{
	static public function getTipoComRecibida()
    {
		$c = new Criteria();
		$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,0);
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$rs = TipoComRecibidaPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getTipoComRecibidaShow()
    {
		$c = new Criteria();
		$c->add(TipoComRecibidaPeer::ES_VISIBLE,1);
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$rs = TipoComRecibidaPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getTipoComRecibidaWfAll()
    {
		$c = new Criteria();
		$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,1);
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$rs = TipoComRecibidaPeer::doSelect($c);
		return $rs; 	
	}
	
	static public function getTipoComRecibidaWf()
    {
		$c = new Criteria();
		$c->add(TipoComRecibidaPeer::ES_ACCION_LEGAL,1);
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$rs = TipoComRecibidaPeer::doSelect($c);
		return $rs; 	
	}
	
    static public function getTipoComRecibidaAll()
    {
		$c = new Criteria();
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$rs = TipoComRecibidaPeer::doSelect($c);
		return $rs; 	
	}
    
    static public function getTipoComRecibidaJson()
	{
        $data = array();
        foreach(TipoComRecibidaPeer::getTipoComRecibidaAll() as $object){
            $data[$object->getPrimaryKey()] = ($object->getDescripcion());
        }
		return json_encode($data); 	
	}
    
	static public function getTipoComByAreaJson($dependencia_id=0)
	{
        $data = array();
		$c = new Criteria();
		$c->setDistinct();
		$c->addJoin(TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID,ReceptorComunicacionesPeer::TIPOCOMRECIBIDA_ID);
		$c->add(ReceptorComunicacionesPeer::DEPENDENCIA_ID,$dependencia_id);
		$c->addAscendingOrderByColumn(TipoComRecibidaPeer::DESCRIPCION);
		$list_objects = TipoComRecibidaPeer::doSelect($c);
        foreach($list_objects as $object){
			$data[] = array('id' => $object->getPrimaryKey(), 'text' => mb_strtoupper($object->getDescripcionCompuesta()));
        }
		return json_encode($data); 	
	}
	
	static public function getVencimientoByTipoCom($tipocomrecibida_id,$fecha_base = null)
	{
		include_once(sfConfig::get('sf_lib_dir'). DIRECTORY_SEPARATOR . "Fechas.class.php");
		if(!trim($tipocomrecibida_id)){ return null; }
		$tipo_com_recibida = TipoComRecibidaPeer::retrieveByPk($tipocomrecibida_id);
		//*****************************************************************************
		$fecha_vence = null;
		$fecha_initial = empty($fecha_base) ? date("Y-m-d") : $fecha_base;
		$ndias  = !empty($tipo_com_recibida->getDiasRespuesta()) ? trim($tipo_com_recibida->getDiasRespuesta()) : 0;
		if($ndias > 0){ 
			if($tipo_com_recibida->getDiasHabiles()){
				$fecha_vence = simad_util::add_business_days($fecha_initial,$ndias,null,'Y-m-d');
			}else{
				$fecha_vence = AddDays($fecha_initial,$ndias); 
			}
		}else{
			$fecha_vence = $fecha_initial;
		}
		//*****************************************************************************
		return $fecha_vence;
	}
	
    static public function getVencimientoByTipoCom2($tipocomrecibida_id,$fecha_base = null)
	{
		include_once(sfConfig::get('sf_lib_dir'). DIRECTORY_SEPARATOR . "Fechas.class.php");
		if(!trim($tipocomrecibida_id)){ return null; }
		$tipo_com_recibida = TipoComRecibidaPeer::retrieveByPk($tipocomrecibida_id);
		//*****************************************************************************
		$fecha_vence = null;
		$fecha_initial = empty($fecha_base) ? date("Y-m-d") : $fecha_base;
		$ndias  = !empty($tipo_com_recibida->getDiasRespuesta()) ? trim($tipo_com_recibida->getDiasRespuesta()) : 0;
		if($ndias > 0){ $fecha_vence = AddDays($fecha_initial,$ndias); }
		//*****************************************************************************
		return $fecha_vence;
	}

	static public function getTipoComRecibidaByText($descripcion = null)
    {
		if(empty($descripcion)){ return null; }
		//*****************************************************************************
		$c = new Criteria();
		$c->add(TipoComRecibidaPeer::DESCRIPCION,$descripcion);
		$rs = TipoComRecibidaPeer::doSelectOne($c);
		return $rs; 	
	}
}
