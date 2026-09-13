<?php

/**
 * Subclass for performing query and update operations on the 'WF_VARIABLE' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfVariablePeer extends BaseWfVariablePeer
{
	/**
     * Executes the SQL query get data permisos de actividades por transicion.
     * 
     * @param id de la wf actividad transición.     
     * @return list resultados
     * @throws SQLException If there is an error executing the specified query.
     */
    public static function getAllWfVariablesByActTransicion($wfactividadtransicion_id = 0)
    {
        $c = new Criteria();
        $c->addJoin(WfVariablePeer::WFTIPODATO_ID, WfTipoDatoPeer::WFTIPODATO_ID);
        $c->add(WfVariablePeer::WFACTIVIDADTRANSICION_ID,$wfactividadtransicion_id);
        $listdata = WfVariablePeer::doSelect($c);
        return $listdata;        
    }
}
