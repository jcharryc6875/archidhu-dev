<?php

/**
 * Subclass for performing query and update operations on the 'DESCARTE_POR_SUBSERIE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DescartePorSubseriePeer extends BaseDescartePorSubseriePeer
{
    static public function getDescarteBySubserieId($subserie_id = 0){
        $c = new Criteria();
        $c->add(DescartePorSubseriePeer::SUBSERIE_ID,$subserie_id);        
        $rs = DescartePorSubseriePeer::doSelect($c);
		//**********************************************************************************************
		$array = (array) null;
		foreach ($rs as $object) {
			$array[] = $object->getDescartefinalsubserieId();
		}
		//**********************************************************************************************
        return $array; 	
	}

	public static function addNewDescarteBySubserie($pksDelete = array(), $subserie_id = 0){
        if(count($pksDelete) > 0){
            $rows_affected = DescartePorSubseriePeer::removeRolByUsuario($pksDelete,$subserie_id);
            //**********************************************************************************************
            foreach ($pksDelete as $objectId) {
                $c = new Criteria();
                $c->add(DescartePorSubseriePeer::DESCARTEFINALSUBSERIE_ID,$objectId);
                $c->add(DescartePorSubseriePeer::SUBSERIE_ID,$subserie_id);
                $countAdded = DescartePorSubseriePeer::doCount($c);
                if($countAdded <= 0){
                    $new_object = new DescartePorSubserie();
                    $new_object->setDescartefinalsubserieId($objectId);
                    $new_object->setSubserieId($subserie_id);
                    $new_object->save();
                }
            }
        }
	}

    public static function removeRolByUsuario($objPkDelete = array(), $subserie_id = 0){
        $c = new Criteria();
        $c->add(DescartePorSubseriePeer::DESCARTEFINALSUBSERIE_ID,$objPkDelete,Criteria::NOT_IN);
        $c->add(DescartePorSubseriePeer::SUBSERIE_ID,$subserie_id);
        return DescartePorSubseriePeer::doDelete($c);
	}
}